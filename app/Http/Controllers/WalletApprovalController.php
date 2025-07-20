<?php

namespace App\Http\Controllers;

use App\Models\WalletApprovalRequest;
use App\Models\WalletApprovalWorkflow;
use App\Models\ApprovalStatus;
use App\Models\WalletApprovalStep;
use App\Models\StepStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Events\WalletApprovalCompleted;
use App\Events\WalletApprovalRejected;
use App\Models\User;
use App\Notifications\WalletApprovalNotification;
use App\Helpers\LogHelper;
use App\Models\Wallet;
use App\Notifications\WalletTopupApprovedNotification;
use App\Services\NotificationService;

class WalletApprovalController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->middleware('auth');
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $requests = WalletApprovalRequest::with(['workflow', 'status', 'user'])
            ->latest()
            ->paginate(10);

        return view('wallet.approvals.index', compact('requests'));
    }

    /**
     * Show the form for creating a new approval request.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $workflows = WalletApprovalWorkflow::where('is_active', true)->get();
        return view('wallet.approvals.create', compact('workflows'));
    }

    /**
     * Store a newly created approval request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'workflow_id' => 'required|exists:wallet_approval_workflows,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:1000',
            'metadata' => 'nullable|array',
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120'
        ]);

        try {
            DB::beginTransaction();
            
            // Get the pending status
            $pendingStatus = ApprovalStatus::where('code', ApprovalStatus::PENDING)->first();
            
            // Prepare metadata
            $metadata = $request->metadata ?? [];
            
            // Process file attachments if any
            if ($request->hasFile('attachments')) {
                $attachmentPaths = [];
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('approval-attachments/' . auth()->id(), 'public');
                    $attachmentPaths[] = $path;
                }
                $metadata['attachments'] = $attachmentPaths;
            }
            
            // Create the approval request
            $approvalRequest = WalletApprovalRequest::create([
                'workflow_id' => $validated['workflow_id'],
                'user_id' => auth()->id(),
                'status_id' => $pendingStatus->id,
                'amount' => $validated['amount'],
                'currency' => 'SAR', // Default currency
                'description' => $validated['description'],
                'metadata' => $metadata
            ]);
            
            // Get the first step in the workflow
            $workflow = WalletApprovalWorkflow::with('steps')->find($validated['workflow_id']);
            $firstStep = $workflow->steps()->orderBy('order')->first();
            
            // Notify the first approver if notification is enabled
            if ($firstStep && $workflow->notify_by_email) {
                try {
                    $approver = User::find($firstStep->user_id);
                    if ($approver) {
                        $this->notificationService->sendEmail(
                            $approver->email,
                            'New Approval Request',
                            'emails.approvals.new-request',
                            [
                                'approver' => $approver->name,
                                'amount' => $validated['amount'],
                                'description' => $validated['description'],
                                'workflow' => $workflow->name
                            ],
                            'new_approval_request',
                            'high'
                        );
                    }
                } catch (\Exception $e) {
                    Log::warning("Failed to queue approval notification: " . $e->getMessage());
                }
            }
            
            DB::commit();
            
            return redirect()->route('wallet.approvals.show', $approvalRequest)
                ->with('success', 'Approval request submitted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Failed to submit approval request. ' . $e->getMessage());
        }
    }

    /**
     * Show approval details
     */
    public function show(WalletApprovalRequest $request)
    {
        $request->load(['payment', 'payment.user', 'steps.user']);
        
        return view('wallet.approval-details', [
            'request' => $request,
            'approvalSteps' => $request->steps
        ]);
    }

    /**
     * Approve a wallet top-up request
     */
    public function approve(Request $request, $id)
    {
        /** @var User $user */
        $user = Auth::user();
        Log::info('Attempting to approve wallet request', [
            'request_id' => $id,
            'user_id' => $user->id,
            'user_roles' => $user->getRoleNames()
        ]);

        $approvalRequest = WalletApprovalRequest::findOrFail($id);
        
        // Get current step
        $currentStep = $approvalRequest->steps()
            ->where('status', StepStatus::PENDING)
            ->first();

        if (!$currentStep) {
            return response()->json([
                'message' => 'No pending steps found for this request'
            ], 422);
        }

        // Check if user has the required role for this step
        if (!$user->hasRole($currentStep->role)) {
            return response()->json([
                'message' => 'You are not authorized to approve this step'
            ], 403);
        }

        DB::beginTransaction();
        try {
            // Update the step
            $currentStep->setStatusByCode(StepStatus::APPROVED);
            $currentStep->comment = $request->comment;
            $currentStep->processed_at = now();
            $currentStep->save();

            // Check if this was the final step
            $nextStep = $approvalRequest->steps()
                ->where('status', StepStatus::PENDING)
                ->first();

            if (!$nextStep) {
                // This was the final step - approve the request using the status change method
                $approvalRequest->changeStatus(
                    ApprovalStatus::APPROVED, 
                    $request->comment,
                    [
                        'completed_at' => now(),
                        'current_step' => null
                    ]
                );
                
                // Log the final approval event
                LogHelper::logApprovalAction($approvalRequest, 'approve', 
                    "Wallet top-up request fully approved by " . $user->name, [
                    'step' => $currentStep->role,
                    'step_order' => $currentStep->step_order,
                    'payment_id' => $approvalRequest->payment_id,
                    'amount' => $approvalRequest->payment ? $approvalRequest->payment->amount : null,
                    'is_final_step' => true
                ]);
                
                // Explicitly handle the wallet transaction using the direct method
                if (method_exists($approvalRequest, 'handleApproval')) {
                    try {
                        $approvalRequest->handleApproval();
                        Log::info('Manually triggered handleApproval for request', [
                            'request_id' => $approvalRequest->id,
                            'payment_id' => $approvalRequest->payment_id,
                            'user_id' => $approvalRequest->user_id,
                            'amount' => $approvalRequest->amount
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Error manually handling approval', [
                            'request_id' => $approvalRequest->id,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                    }
                }
                
                // Trigger the wallet approval completed event as a fallback mechanism
                try {
                    event(new WalletApprovalCompleted($approvalRequest));
                    
                    Log::info('Wallet approval completion event triggered', [
                        'request_id' => $approvalRequest->id,
                        'user_id' => $approvalRequest->user_id,
                        'payment_id' => $approvalRequest->payment_id
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error handling wallet approval completion', [
                        'request_id' => $approvalRequest->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
                
                // Notify the user asynchronously
                try {
                    if (!app()->environment('testing')) {
                        $customer = $approvalRequest->payment->user;
                        if ($customer) {
                            $this->notificationService->sendEmail(
                                $customer->email,
                                'Wallet Top-up Approved',
                                'emails.wallet.topup-approved',
                                [
                                    'name' => $customer->name,
                                    'amount' => $approvalRequest->payment->amount,
                                    'reference' => $approvalRequest->reference_no ?? null
                                ],
                                'approval_complete',
                                'high'
                            );

                            // Send SMS if mobile exists
                            if ($customer->mobile) {
                                $this->notificationService->sendSms(
                                    $customer->mobile,
                                    "Your wallet top-up of SAR {$approvalRequest->payment->amount} has been approved.",
                                    'approval_complete',
                                    'high'
                                );
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning("Failed to queue approval completion notification: " . $e->getMessage());
                }
            } else {
                // Move to next step
                $approvalRequest->update(['current_step' => $nextStep->step_order]);
                
                // Log the step approval
                LogHelper::logApprovalAction($approvalRequest, 'approve', 
                    "Wallet top-up request step approved by " . $user->name, [
                    'step' => $currentStep->role,
                    'step_order' => $currentStep->step_order,
                    'next_step' => $nextStep->step_order,
                    'payment_id' => $approvalRequest->payment_id,
                    'amount' => $approvalRequest->payment ? $approvalRequest->payment->amount : null,
                    'is_final_step' => false
                ]);
                
                // Notify next approver asynchronously
                try {
                    $nextApprover = User::find($nextStep->user_id);
                    if ($nextApprover) {
                        $this->notificationService->sendEmail(
                            $nextApprover->email,
                            'Approval Action Required',
                            'emails.approvals.action-required',
                            [
                                'approver' => $nextApprover->name,
                                'amount' => $approvalRequest->payment->amount,
                                'reference' => $approvalRequest->reference_no ?? null,
                                'step' => $nextStep->role
                            ],
                            'next_approval_step',
                            'high'
                        );
                    }
                } catch (\Exception $e) {
                    Log::warning("Failed to queue next approver notification: " . $e->getMessage());
                }
            }

            DB::commit();
            
            Log::info('Successfully approved wallet request step', [
                'request_id' => $id,
                'step_id' => $currentStep->id,
                'next_step_exists' => isset($nextStep),
                'next_step_order' => $nextStep->step_order ?? null
            ]);

            return response()->json([
                'message' => 'Request approved successfully',
                'request' => $approvalRequest->fresh()->load('steps')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to approve wallet request', [
                'request_id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Reject a wallet top-up request
     */
    public function reject(Request $request, $id)
    {
        /** @var User $user */
        $user = Auth::user();
        Log::info('Attempting to reject wallet request', [
            'request_id' => $id,
            'user_id' => $user->id,
            'user_roles' => $user->getRoleNames()
        ]);

        $approvalRequest = WalletApprovalRequest::findOrFail($id);
        
        // Get current step
        $currentStep = $approvalRequest->steps()
            ->where('status', StepStatus::PENDING)
            ->first();

        if (!$currentStep) {
            return response()->json([
                'message' => 'No pending steps found for this request'
            ], 422);
        }

        // Check if user has the required role for this step
        if (!$user->hasRole($currentStep->role)) {
            return response()->json([
                'message' => 'You are not authorized to reject this step'
            ], 403);
        }
    
        // Validate rejection requires a comment
        $request->validate([
            'comment' => 'required|string|min:10'
        ]);

        DB::beginTransaction();
        try {
            // Update current step
            $currentStep->setStatusByCode(StepStatus::REJECTED);
            $currentStep->comment = $request->comment;
            $currentStep->processed_at = now();
            $currentStep->save();
           
            // Change the request status using the change status method
            $approvalRequest->changeStatus(
                ApprovalStatus::REJECTED, 
                $request->comment,
                [
                    'current_step' => null,
                    'rejection_reason' => $request->comment
                ]
            );
           
            // Log the rejection
            LogHelper::logApprovalAction($approvalRequest, 'reject', 
                "Wallet top-up request rejected by " . $user->name, [
                'step' => $currentStep->role,
                'step_order' => $currentStep->step_order,
                'reason' => $request->comment,
                'payment_id' => $approvalRequest->payment_id,
                'amount' => $approvalRequest->payment ? $approvalRequest->payment->amount : null
            ]);
           
            // Notify user of rejection asynchronously
            try {
                if (!app()->environment('testing')) {
                    $customer = $approvalRequest->payment->user;
                    if ($customer) {
                        $this->notificationService->sendEmail(
                            $customer->email,
                            'Approval Request Rejected',
                            'emails.approvals.request-rejected',
                            [
                                'name' => $customer->name,
                                'amount' => $approvalRequest->payment->amount,
                                'reason' => $request->comment,
                                'reference' => $approvalRequest->reference_no ?? null
                            ],
                            'approval_rejected',
                            'high'
                        );

                        // Send SMS if mobile exists
                        if ($customer->mobile) {
                            $this->notificationService->sendSms(
                                $customer->mobile,
                                "Your approval request for SAR {$approvalRequest->payment->amount} has been rejected. Reason: {$request->comment}",
                                'approval_rejected',
                                'high'
                            );
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning("Failed to queue rejection notification: " . $e->getMessage());
            }
             
            DB::commit();
           
            Log::info('Successfully rejected wallet request', [
                'request_id' => $id,
                'step_id' => $currentStep->id
            ]);

            return response()->json([
                'message' => 'Request rejected successfully',
                'request' => $approvalRequest->fresh()->load('steps')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reject wallet request', [
                'request_id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function myApprovals()
    {
        $user = auth()->user();
        
        // Find pending steps assigned to the current user
        $pendingSteps = WalletApprovalStep::where('user_id', $user->id)
            ->where('status', StepStatus::PENDING)
            ->with(['request', 'request.payment', 'request.payment.user', 'request.workflow'])
            ->get();
            
        // Filter steps to only include those where current_step matches step_order
        $activePendingSteps = $pendingSteps->filter(function($step) {
            return $step->request && 
                  ($step->request->status === 'pending' || $step->request->status === 'in_progress') && 
                  $step->request->current_step == $step->step_order;
        });
        
        // For compatibility with both view templates
        $requests = $activePendingSteps->map(function($step) {
            return $step->request;
        });
        
        // Paginate the collection
        $perPage = 10;
        $page = request()->get('page', 1);
        $paginatedRequests = new \Illuminate\Pagination\LengthAwarePaginator(
            $requests->forPage($page, $perPage),
            $requests->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        return view('wallet.my-approvals', [
            'pendingApprovals' => $activePendingSteps,
            'requests' => $paginatedRequests
        ]);
    }

    public function history()
    {
        // Show all approval requests in history
        $requests = WalletApprovalRequest::with(['workflow', 'status', 'user'])
            ->latest()
            ->paginate(10);

        return view('wallet.approvals.history', compact('requests'));
    }

    public function downloadAttachment(WalletApprovalRequest $request, $actionId)
    {
        $action = $request->actions()->findOrFail($actionId);

        if (!$action->attachment) {
            abort(404);
        }

        return Storage::download($action->attachment);
    }
} 