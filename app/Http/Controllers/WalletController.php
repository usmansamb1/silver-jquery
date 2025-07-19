<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\Wallet;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\WalletApprovalRequest;
use App\Models\WalletApprovalStep;
use App\Notifications\WalletApprovalNotification;
use App\Notifications\WalletTopupApprovedNotification;
use App\Notifications\WalletTopupRejectedNotification;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Helpers\LogHelper;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WalletController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->middleware('auth');
        $this->notificationService = $notificationService;
    }

    /**
     * Display the Wallet Top-up page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function topup()
    {
        $user = Auth::user();
        $wallet = $user->wallet;
        $currentBalance = $wallet ? $wallet->balance : 0;

        return view('wallet.topup', compact('currentBalance','user'));
    }

    /**
     * Process the wallet top-up.
     *
     * Expects:
     *  - amount (numeric, minimum 1)
     *  - payment_method (credit_card or bank_transfer)
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function storeTopup(Request $request)
    {
        $rules = [
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|in:credit_card,bank_transfer'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();
        $amount = floatval($request->amount);

        // Create a payment record (you may extend this with file uploads or notes as needed)
        $payment = Payment::create([
            'user_id'       => $user->id,
            'payment_type'  => $request->payment_method,
            'amount'        => $amount,
            'status'        => 'approved' // For demo purposes we assume an instant approval.
        ]);

        // Update the wallet's balance
        $wallet = $user->wallet;
        if ($wallet) {
            $wallet->balance += $amount;
            $wallet->save();
        } else {
            $wallet = Wallet::create([
                'user_id' => $user->id,
                'balance' => $amount,
            ]);
        }
        
        // Log the wallet recharge activity
        LogHelper::logWalletRecharge($wallet, "Wallet topped up with {$amount} via {$request->payment_method}", [
            'amount' => $amount,
            'payment_method' => $request->payment_method,
            'payment_id' => $payment->id
        ]);

        if ($request->expectsJson()) {
            // Reload the user to get the updated wallet balance
            $user = User::find($user->id);
            return response()->json([
                'message' => 'Wallet topped up successfully.',
                'balance' => $user->wallet->balance
            ]);
        }

        return redirect()->route('wallet.topup')->with('success', 'Wallet topped up successfully.');
    }

    /**
     * (Optional) Show the wallet transaction history.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function history()
    {
        $user = Auth::user();
        $payments = Payment::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        // Ensure user has a wallet record
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );
        $transactions = $wallet->transactions()->paginate(10);
        return view('wallet.history', compact('payments','wallet','transactions'));
    }

    public function paymentProcess(Request $request)
    {
        $user = Auth::user();
        
        // Get amount from request or session
        $amount = $request->input('amount') ?? session('topup_amount', 0);
        
        // Validate amount
        if (!$amount || $amount <= 0) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Invalid amount'], 400);
            }
            return redirect()->route('wallet.topup')->with('error', 'Invalid amount provided.');
        }
        
        // Get or create wallet
        $wallet = $user->wallet;
        if (!$wallet) {
            $wallet = Wallet::create([
                'user_id' => $user->id,
                'balance' => 0,
            ]);
        }
        
        try {
            DB::beginTransaction();
        
        // Create payment record
        $payment = Payment::create([
            'user_id' => $user->id,
                'payment_type' => 'credit_card',
            'amount' => $amount,
                'status' => 'approved',
                'notes' => 'Credit card payment via ' . ($request->input('payment_gateway') ?? 'Stripe/MADA')
        ]);
        
            // Create wallet transaction using the deposit method
            $transaction = $wallet->deposit(
                $amount,
                'Wallet top-up via credit card',
                $payment,
                [
                    'payment_method' => 'credit_card',
                    'payment_id' => $payment->id,
                    'gateway' => $request->input('payment_gateway') ?? 'stripe',
                    'paymentIntent' => $request->input('paymentIntent')
                ]
            );
        
        // Log the wallet recharge activity
            LogHelper::logWalletRecharge($wallet, "Wallet topped up with {$amount} SAR via credit card", [
            'amount' => $amount,
            'payment_method' => 'credit_card',
                'payment_id' => $payment->id,
                'transaction_id' => $transaction->id,
                'gateway' => $request->input('payment_gateway') ?? 'stripe'
            ]);
            
            DB::commit();
            
            // Clear session amount
            session()->forget('topup_amount');
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment completed successfully',
                    'balance' => $wallet->fresh()->balance,
            'payment_id' => $payment->id
        ]);
            }
            
            return redirect()->route('wallet.topup')->with('success', 'Payment completed successfully via credit card.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Credit card payment processing failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'amount' => $amount,
                'error' => $e->getMessage()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Payment processing failed'], 500);
            }
            
            return redirect()->route('wallet.topup')->with('error', 'Payment processing failed. Please try again.');
        }
    }

    /**
     * Process bank payment with file uploads
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processBankPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:10',
            'payment_method' => 'required|in:bank_transfer,bank_guarantee,bank_lc',
            'payment_files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'payment_notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $files = [];
        $payment = null;
        $approvalRequest = null;

        try {
            DB::beginTransaction();

            // Handle file uploads if they exist
        if ($request->hasFile('payment_files')) {
            foreach ($request->file('payment_files') as $file) {
                $path = $file->store('payment_proofs/' . $user->id, 'public');
                $files[] = $path;
            }
        }

        // Create payment record
        $payment = Payment::create([
            'user_id' => $user->id,
            'payment_type' => $request->payment_method,
            'amount' => $request->amount,
            'status' => 'pending',
            'notes' => $request->payment_notes,
                'files' => !empty($files) ? json_encode($files) : null
            ]);

            if (!$payment || !$payment->exists) {
                throw new \Exception("Failed to create payment record");
            }

            // Create approval request
            $approvalRequest = new WalletApprovalRequest();
            $approvalRequest->payment_id = $payment->id;
            $approvalRequest->user_id = $user->id;
            $approvalRequest->status = 'pending';
            $approvalRequest->current_step = 1;
            
            // Make sure we set the amount field
            $approvalRequest->amount = $request->amount;
            $approvalRequest->description = $request->payment_notes;
            
            // Generate a unique reference number
            $referenceNo = 'JOiL-' . strtoupper(Str::random(8));
            if (Schema::hasColumn('v2_wallet_approval_requests', 'reference_no')) {
                $approvalRequest->reference_no = $referenceNo;
            }

            if (!$approvalRequest->save()) {
                throw new \Exception("Failed to create approval request");
            }

            // Find all approvers with roles
            $roles = ['finance', 'validation', 'activation'];
            $approvers = [];
            
            foreach ($roles as $index => $role) {
                // Find or create test approvers in non-production
                if (app()->environment(['local', 'testing', 'development'])) {
                    $approver = User::role($role)->first();
                    if (!$approver) {
                        $approver = User::factory()->create([
                            'email' => "{$role}@test.com",
                            'name' => ucfirst($role) . ' Approver'
                        ]);
                        $approver->assignRole($role);
                    }
                } else {
                    // In production, ensure approvers exist
                    $approver = User::role($role)->first();
                    if (!$approver) {
                        throw new \Exception("No {$role} approver found");
            }
                }
                
                $approvers[$role] = $approver;
            }

            // Create approval steps for the complete chain
            $steps = [
                [
                    'request_id' => $approvalRequest->id,
                    'user_id' => $approvers['finance']->id,
                    'role' => 'finance',
                    'status' => 'pending',
                    'step_order' => 1
                ],
                [
                    'request_id' => $approvalRequest->id,
                    'user_id' => $approvers['validation']->id,
                    'role' => 'validation',
                    'status' => 'pending',
                    'step_order' => 2
                ],
                [
                    'request_id' => $approvalRequest->id,
                    'user_id' => $approvers['activation']->id,
                    'role' => 'activation',
                    'status' => 'pending',
                    'step_order' => 3
                ]
            ];

            foreach ($steps as $step) {
                $approvalStep = WalletApprovalStep::create($step);
                if (!$approvalStep || !$approvalStep->exists) {
                    throw new \Exception("Failed to create approval step for " . $step['role']);
                }
            }

            // Send notification outside of transaction
            $shouldNotify = !app()->environment('testing');
            
            // Log the wallet submission with payment type
            LogHelper::logWalletSubmission($payment, "New payment {$request->payment_method} submission created", [
                'payment_id' => $payment->id,
                'user_id' => $user->id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method
            ]);

            DB::commit();

            // Send notification after successful commit
            if ($shouldNotify && isset($approvers['finance'])) {
                try {
                    // Replace direct notification with queued notification
                    $this->notificationService->sendEmail(
                        $approvers['finance']->email,
                        'New Wallet Approval Required',
                        'emails.wallet.approval-required',
                        [
                            'approver' => $approvers['finance']->name,
                            'amount' => $request->amount,
                            'reference' => $approvalRequest->reference_no,
                            'payment_method' => $request->payment_method
                        ],
                        'approval_request',
                        'high'
                    );
                } catch (\Exception $e) {
                    Log::warning("Failed to queue approval notification: " . $e->getMessage());
                }
            }

            return response()->json([
                'message' => 'Payment details submitted successfully. Our team will review and process your payment.',
                'payment_id' => $payment->id,
                'reference_no' => $approvalRequest->reference_no
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log the detailed error
            Log::error("Error processing bank payment", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $user->id,
                'payment_data' => $request->except('payment_files'),
                'payment_id' => $payment->id ?? null,
                'approval_request_id' => $approvalRequest->id ?? null
        ]);

        return response()->json([
                'error' => 'Failed to process payment request. Please try again.',
                'details' => app()->environment('local', 'development') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Find user by email and role
     *
     * @param string $email
     * @param string $role
     * @return User|null
     */
    protected function findUserByEmailAndRole($email, $role)
    {
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            // For development/testing, create the user if they don't exist
            if (app()->environment(['local', 'testing', 'development'])) {
                $user = User::factory()->create([
                    'email' => $email,
                    'name' => ucfirst($role) . ' Approver'
                ]);
                $user->assignRole($role);
            }
        }
        
        return $user && $user->hasRole($role) ? $user : null;
    }

    public function approvePaymentStep(Request $request, WalletApprovalRequest $approvalRequest)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Get the current step
            $currentStep = WalletApprovalStep::where('request_id', $approvalRequest->id)
                ->where('step_order', $approvalRequest->current_step)
                ->first();

            if (!$currentStep) {
                return response()->json(['error' => 'Invalid approval step'], 400);
            }

            $user = auth()->user();

            // Check if the user has the required role for this step
            if (!$this->userHasRole($user, $currentStep->role)) {
                return response()->json(['error' => 'You do not have permission to approve this step'], 403);
            }

            DB::beginTransaction();

            // Update current step
            $currentStep->status = 'approved';
            $currentStep->comment = $request->comment;
            $currentStep->processed_at = now();
            $currentStep->save();

            // Check if this is the last step
            $nextStep = WalletApprovalStep::where('request_id', $approvalRequest->id)
                ->where('step_order', '>', $approvalRequest->current_step)
                ->orderBy('step_order')
                ->first();

            if ($nextStep) {
                // Move to the next step
                $approvalRequest->current_step = $nextStep->step_order;
                $approvalRequest->save();

                // Notify the next approver asynchronously
                $shouldNotify = !app()->environment('testing');
                if ($shouldNotify) {
                    try {
                        $nextApprover = User::find($nextStep->user_id);
                        if ($nextApprover) {
                            $this->notificationService->sendEmail(
                                $nextApprover->email,
                                'New Wallet Approval Required',
                                'emails.wallet.approval-required',
                                [
                                    'approver' => $nextApprover->name,
                                    'amount' => $approvalRequest->payment->amount,
                                    'reference' => $approvalRequest->reference_no
                                ],
                                'approval_request',
                                'high'
                            );
                        }
                    } catch (\Exception $e) {
                        Log::warning("Failed to queue approval notification: " . $e->getMessage());
                    }
                }
            } else {
                // This was the last step, complete the approval process
                $approvalRequest->status = 'completed';
                $approvalRequest->completed_at = now();
                $approvalRequest->save();

                // Get the payment and update its status
                $payment = Payment::find($approvalRequest->payment_id);
                if ($payment) {
                    $this->finalizeApprovedPayment($payment);

                    // Notify the customer asynchronously
                $shouldNotify = !app()->environment('testing');
                if ($shouldNotify) {
                    try {
                        $customer = User::find($payment->user_id);
                        if ($customer) {
                                $this->notificationService->sendEmail(
                                    $customer->email,
                                    'Wallet Top-up Approved',
                                    'emails.wallet.topup-approved',
                                    [
                                        'name' => $customer->name,
                                        'amount' => $payment->amount,
                                        'reference' => $approvalRequest->reference_no
                                    ],
                                    'payment_approved',
                                    'high'
                                );

                                // Send SMS if mobile number exists
                                if ($customer->mobile) {
                                    $this->notificationService->sendSms(
                                        $customer->mobile,
                                        "Your wallet top-up of SAR {$payment->amount} has been approved. Ref: {$approvalRequest->reference_no}",
                                        'payment_approved',
                                        'high'
                                    );
                                }
                        }
                    } catch (\Exception $e) {
                            Log::warning("Failed to queue approval notification to customer: " . $e->getMessage());
                        }
                    }
                }
            }

            DB::commit();

            return response()->json(['message' => 'Step approved successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error approving payment step", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'approval_request_id' => $approvalRequest->id
            ]);
            return response()->json(['error' => 'Failed to approve step'], 500);
        }
    }

    // Helper methods
    private function getRequiredRoleForStep($step)
    {
        switch ($step) {
            case 1: return 'finance';
            case 2: return 'validation';
            case 3: return 'activation';
            default: throw new \Exception("Invalid approval step");
        }
    }

    private function getNewStatusForStep($step)
    {
        switch ($step) {
            case 1: return 'finance_approved';
            case 2: return 'validation_approved';
            case 3: return 'completed';
            default: throw new \Exception("Invalid approval step");
        }
    }

    private function finalizeApprovedPayment(Payment $payment)
    {
        // Update payment status
        $payment->update(['status' => 'approved']);
        
        // Recharge wallet
        $wallet = $payment->user->wallet;
        if (!$wallet) {
            $wallet = Wallet::create([
                'user_id' => $payment->user_id,
                'balance' => 0
            ]);
        }
        
        $wallet->balance += $payment->amount;
        $wallet->save();
    }

    public function rejectApprovalRequest(Request $request, WalletApprovalRequest $approvalRequest)
    {
        $user = Auth::user();
        $currentStep = $approvalRequest->current_step;
        
        // Validate user has correct role for current step
        $requiredRole = $this->getRequiredRoleForStep($currentStep);
        // Manual role check
        if (!$this->userHasRole($user, $requiredRole)) {
            return response()->json(['error' => 'You are not authorized to reject this request'], 403);
        }
        
        DB::beginTransaction();
        try {
            // Update the current approval step
            $approvalStep = $approvalRequest->approvals()
                ->where('role', $requiredRole)
                ->first();
                
            $approvalStep->update([
                'status' => 'rejected',
                'comments' => $request->rejection_reason,
                'processed_at' => now()
            ]);
            
            // Update the approval request status
            $approvalRequest->update([
                'status' => 'rejected',
                'current_step' => null,
                'rejection_reason' => $request->rejection_reason
            ]);
            
            // Update the payment status
            $approvalRequest->payment->update(['status' => 'rejected']);
            
            // Notify the customer asynchronously
            try {
                if (!app()->environment('testing')) {
                    $customer = $approvalRequest->payment->user;
                    if ($customer) {
                        $this->notificationService->sendEmail(
                            $customer->email,
                            'Wallet Top-up Rejected',
                            'emails.wallet.topup-rejected',
                            [
                                'name' => $customer->name,
                                'amount' => $approvalRequest->payment->amount,
                                'reason' => $request->rejection_reason,
                                'reference' => $approvalRequest->reference_no
                            ],
                            'payment_rejected',
                            'high'
                        );

                        // Send SMS if mobile number exists
                        if ($customer->mobile) {
                            $this->notificationService->sendSms(
                                $customer->mobile,
                                "Your wallet top-up request has been rejected. Reason: {$request->rejection_reason}. Ref: {$approvalRequest->reference_no}",
                                'payment_rejected',
                                'high'
                            );
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning("Failed to queue rejection notification: " . $e->getMessage());
            }
            
            DB::commit();
            
            return response()->json([
                'message' => 'Payment request rejected',
                'current_status' => 'rejected'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Error rejecting payment: " . $e->getMessage());
            return response()->json(['error' => 'Failed to reject payment'], 500);
        }
    }

    public function index()
    {
        $wallet = Wallet::where('user_id', Auth::id())->firstOrFail();
        $payments = Payment::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('wallet.history', compact('wallet', 'payments'));
    }

    public function pendingPayments()
    {
        // Check if user has any of the required roles
        $allowedRoles = ['admin', 'finance', 'audit', 'activation', 'validation'];
     
        if (!$this->userHasAnyRole(auth()->user(), $allowedRoles)) {
            abort(403, 'Unauthorized');
        }

        $pendingPayments = Payment::whereIn('payment_type', ['bank_transfer', 'bank_guarantee', 'bank_lc'])
            ->where('status', 'pending')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('wallet.pending-payments', compact('pendingPayments'));
    }

    public function approvePayment(Request $request, Payment $payment)
    {
        // Check if user has any of the required roles
        $allowedRoles = ['admin', 'finance', 'audit', 'activation', 'validation'];
        
        if (!$this->userHasAnyRole(auth()->user(), $allowedRoles)) {
            abort(403, 'Unauthorized');
        }

        // Rest of the approval logic
        try {
            DB::beginTransaction();

            // Update payment status
            $payment->status = 'approved';
            $payment->save();

            // Update user's wallet balance
            $wallet = $payment->user->wallet;
            if (!$wallet) {
                $wallet = Wallet::create([
                    'user_id' => $payment->user_id,
                    'balance' => 0
                ]);
            }
            $wallet->balance += $payment->amount;
            $wallet->save();

            DB::commit();
            return back()->with('success', 'Payment has been approved.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Payment approval failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to approve payment. Please try again.');
        }
    }

    public function rejectPayment(Request $request, Payment $payment)
    {
        // Check if user has any of the required roles
        $allowedRoles = ['admin', 'finance'];
        
        if (!$this->userHasAnyRole(auth()->user(), $allowedRoles)) {
            abort(403, 'Unauthorized');
        }

        $payment->status = 'rejected';
        $payment->save();

        return back()->with('success', 'Payment has been rejected.');
    }

    public function createPaymentIntent(Request $request)
    {
        try {
            // Set your secret key
            Stripe::setApiKey(config('services.stripe.secret'));

            // Validate the amount
            $amount = $request->amount;
            if (!$amount || $amount < 1000) { // Minimum 10 SAR (1000 cents)
                return response()->json(['error' => 'Invalid amount'], 400);
            }

            // Create a PaymentIntent with amount and currency
            $paymentIntent = PaymentIntent::create([
                'amount' => $amount,
                'currency' => 'sar',
                'payment_method_types' => ['card'],
                'metadata' => [
                    'user_id' => auth()->id(),
                ],
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
            ]);
        } catch (\Exception $e) {
            Log::error('Stripe Payment Intent Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Check if a user has a specific role
     */
    private function userHasRole(User $user, string $role)
    {
        // We'll use a DB query to check roles directly
        return DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_id', $user->id)
            ->where('roles.name', $role)
            ->exists();
    }
    
    /**
     * Check if a user has any of the specified roles
     */
    private function userHasAnyRole(User $user, array $roles)
    {
        return DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_id', $user->id)
            ->whereIn('roles.name', $roles)
            ->exists();
    }

    /**
     * Show approval details page
     *
     * @param WalletApprovalRequest $request
     * @return \Illuminate\View\View
     */
    public function approvalDetails(WalletApprovalRequest $request)
    {
        // Load relationship data
        $request->load([
            'payment',
            'payment.user'
        ]);

        // Get all approval steps in order
        $approvalSteps = WalletApprovalStep::where('request_id', $request->id)
            ->orderBy('step_order')
            ->get();

        return view('wallet.approval-details', [
            'request' => $request,
            'approvalSteps' => $approvalSteps
        ]);
    }

    /**
     * Display wallet approval requests that need the current user's approval.
     *
     * @return \Illuminate\View\View
     */
    public function myPendingApprovals()
    {
        $user = auth()->user();
        
        // Find all approval steps assigned to this user that are still pending
        $pendingSteps = WalletApprovalStep::where('user_id', $user->id)
            ->where('status', 'pending')
            ->with(['request', 'request.payment', 'request.payment.user'])
            ->get();
            
        $pendingApprovals = collect();
        
        // Filter to include only steps that are currently active (current_step matches step_order)
        foreach ($pendingSteps as $step) {
            if ($step->request && $step->request->status === 'pending' && $step->request->current_step === $step->step_order) {
                $pendingApprovals->push($step);
            }
        }
        
        return view('wallet.my-approvals', compact('pendingApprovals'));
    }

    /**
     * Display a list of the user's wallet transactions.
     *
     * @return \Illuminate\View\View
     */
    public function transactions()
    {
        $user = Auth::user();
        $wallet = $user->wallet;
        
        if (!$wallet) {
            return view('wallet.transactions', [
                'transactions' => collect([]),
                'wallet' => null,
                'balance' => 0
            ]);
        }
        
        $transactions = $wallet->transactions()->paginate(15);
        
        return view('wallet.transactions', [
            'transactions' => $transactions,
            'wallet' => $wallet,
            'balance' => $wallet->balance
        ]);
    }

    /**
     * Initiate Hyperpay checkout (AJAX)
     */
    public function hyperpayCheckout(Request $request)
    {
        $user = auth()->user();
        $amount = $request->input('amount');
        $paymentType = $request->input('paymentType', 'credit'); // 'credit' or 'mada'
        
        // Store amount in session for later processing
        session(['hyperpay_amount' => $amount]);
        
        $entityId = config('services.hyperpay.entity_id_credit');
        if ($paymentType === 'mada') {
            $entityId = config('services.hyperpay.entity_id_mada');
        }
        $merchantTransactionId = uniqid('txn_');
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.hyperpay.access_token'),
        ])->asForm()->post(config('services.hyperpay.base_url') . 'v1/checkouts', [
            'entityId' => $entityId,
            'amount' => number_format($amount, 2, '.', ''),
            'currency' => config('services.hyperpay.currency'),
            'paymentType' => 'DB',
            'merchantTransactionId' => $merchantTransactionId,
            'customer.email' => $user->email,
            'testMode' => 'EXTERNAL',
            'customParameters[3DS2_enrolled]' => 'true',
            'customParameters[3DS2_flow]' => 'challenge',
        ]);
        if ($response->failed()) {
            return response()->json(['error' => 'Failed to initiate payment.'], 500);
        }
        $data = $response->json();
        return response()->json(['id' => $data['id'] ?? null]);
    }

    /**
     * Handle Hyperpay payment status (redirect from widget)
     */
    public function hyperpayStatus(Request $request)
    {
        $resourcePath = $request->input('resourcePath');
        $checkoutId = $request->input('id');
        $user = auth()->user();
        
        // Handle test mode
        if ($checkoutId === 'demo-checkout-id') {
            Log::info('Processing demo payment in test mode', [
                'user_id' => $user->id,
                'amount' => session('hyperpay_amount', 100)
            ]);
            
            // Create a simulated successful result
            $result = [
                'id' => 'test-' . uniqid(),
                'amount' => session('hyperpay_amount', 100),
                'currency' => 'SAR',
                'result' => [
                    'code' => '000.100.110',
                    'description' => 'Request successfully processed in TEST mode'
                ],
                'buildNumber' => 'test',
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'ndc' => 'test',
                'test_mode' => true
            ];
            
            // Process the test payment
            if ($user) {
                $this->processSuccessfulHyperpayPayment($user, $result['amount'], $result, '/v1/test/payment');
            }
            
            return view('wallet.topup-status', compact('result'));
        }
        
        // Regular processing for real payments: use stored entity ID or fallback
        $entityId = session('hyperpay_entity_id', config('services.hyperpay.entity_id_credit'));
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.hyperpay.access_token'),
            ])->get(config('services.hyperpay.base_url') . ltrim($resourcePath, '/'), [
                'entityId' => $entityId,
            ]);
            
            $result = $response->json();
            Log::info('Hyperpay status response', [
                'user_id' => $user->id ?? 'guest',
                'entity_id' => $entityId,
                'response' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get payment status from Hyperpay', [
                'error' => $e->getMessage(),
                'resourcePath' => $resourcePath,
                'user_id' => $user->id ?? 'guest',
                'entity_id' => $entityId
            ]);
            $result = [
                'result' => [
                    'code' => 'ERROR',
                    'description' => 'Failed to verify payment status: ' . $e->getMessage()
                ]
            ];
        }
        
        // Check payment status
        $code = $result['result']['code'] ?? null;
        $description = $result['result']['description'] ?? 'Unknown error';
        $success = isset($code) && str_starts_with($code, '000.');
        $amount = floatval($result['amount'] ?? session('hyperpay_amount', 0));
        $hyperpayTransactionId = $result['id'] ?? null;
        
        // Validate expected amount from frontend
        $expectedAmount = floatval($request->input('expected_amount', 0));
        if ($expectedAmount > 0 && $amount > 0 && abs($amount - $expectedAmount) > 0.01) {
            Log::warning('Amount mismatch detected in Hyperpay payment', [
                'user_id' => $user->id,
                'hyperpay_amount' => $amount,
                'expected_amount' => $expectedAmount,
                'difference' => abs($amount - $expectedAmount),
                'hyperpay_transaction_id' => $hyperpayTransactionId
            ]);
            
            // For critical amount mismatches, we should not process the payment
            if (abs($amount - $expectedAmount) > 10) { // More than 10 SAR difference
                Log::error('Critical amount mismatch - payment processing halted', [
                    'user_id' => $user->id,
                    'hyperpay_amount' => $amount,
                    'expected_amount' => $expectedAmount,
                    'difference' => abs($amount - $expectedAmount)
                ]);
                
                // Return error view or redirect with error
                return view('wallet.topup-status', [
                    'result' => [
                        'result' => [
                            'code' => 'AMOUNT_MISMATCH',
                            'description' => 'Payment amount mismatch detected. Please contact support.'
                        ]
                    ]
                ]);
            }
        }
        
        // CRITICAL: Check for duplicate processing using Hyperpay transaction ID
        if ($success && $hyperpayTransactionId) {
            $existingPayment = Payment::where('hyperpay_transaction_id', $hyperpayTransactionId)->first();
            if ($existingPayment) {
                Log::info('Duplicate Hyperpay payment attempt detected', [
                    'user_id' => $user->id,
                    'hyperpay_transaction_id' => $hyperpayTransactionId,
                    'existing_payment_id' => $existingPayment->id,
                    'amount' => $amount
                ]);
                
                // Clear session and return status view to prevent further processing
                session()->forget('hyperpay_amount');
                return view('wallet.topup-status', compact('result'));
            }
        }
        
        // Process payment only once
        if ($user && $amount > 0) {
            if ($success) {
                // Handle successful payment
                $this->processSuccessfulHyperpayPayment($user, $amount, $result, $resourcePath);
            } else {
                // Handle failed/declined/canceled payment
                $this->logFailedHyperpayPayment($user, $amount, $code, $description, $result);
            }
        } else {
            // Log failed payment attempt
            Log::warning('Hyperpay payment failed', [
                'user_id' => $user->id ?? null,
                'success' => $success,
                'amount' => $amount,
                'code' => $code,
                'result' => $result
            ]);
        }
        
        return view('wallet.topup-status', compact('result'));
    }

    /**
     * Pre-create checkout sessions for common amounts (session pooling)
     */
    public function preCreateSessions(Request $request)
    {
        $commonAmounts = [50, 100, 200, 500, 1000];
        $sessions = [];
        
        foreach ($commonAmounts as $amount) {
            try {
                $checkoutData = [
                    'entityId' => config('services.hyperpay.entity_id_credit'),
                    'amount' => number_format($amount, 2, '.', ''),
                    'currency' => 'SAR',
                    'paymentType' => 'DB',
                    'customer.email' => auth()->user()->email,
                    'customer.givenName' => auth()->user()->first_name,
                    'customer.surname' => auth()->user()->last_name,
                    'customer.mobile' => auth()->user()->mobile,
                    'billing.country' => 'SA',
                    'customParameters[SHOPPER_EndToEndIdentity]' => auth()->user()->email,
                    'customParameters[CTPE_DESCRIPTOR_TEMPLATE]' => 'JoilYaseer Wallet Topup'
                ];

                $response = Http::asForm()->withHeaders([
                    'Authorization' => 'Bearer ' . config('services.hyperpay.access_token'),
                ])->post(config('services.hyperpay.base_url') . '/v1/checkouts', $checkoutData);

                if ($response->successful()) {
                    $data = $response->json();
                    $sessions[$amount] = [
                        'checkout_id' => $data['id'],
                        'amount' => $amount,
                        'created_at' => now(),
                        'expires_at' => now()->addMinutes(30) // Hyperpay sessions typically expire in 30 minutes
                    ];
                }
            } catch (\Exception $e) {
                Log::warning('Failed to pre-create session for amount: ' . $amount, [
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Store sessions in cache
        Cache::put('hyperpay_session_pool_' . auth()->id(), $sessions, now()->addMinutes(25));
        
        return response()->json([
            'success' => true,
            'sessions_created' => count($sessions),
            'amounts' => array_keys($sessions)
        ]);
    }

    /**
     * Get pre-created session from pool
     */
    public function getPooledSession(Request $request)
    {
        $amount = floatval($request->input('amount'));
        $sessionPool = Cache::get('hyperpay_session_pool_' . auth()->id(), []);
        
        if (isset($sessionPool[$amount])) {
            $session = $sessionPool[$amount];
            
            // Check if session is still valid (not expired)
            if (now()->lt($session['expires_at'])) {
                // Remove from pool to prevent reuse
                unset($sessionPool[$amount]);
                Cache::put('hyperpay_session_pool_' . auth()->id(), $sessionPool, now()->addMinutes(25));
                
                return response()->json([
                    'success' => true,
                    'checkout_id' => $session['checkout_id'],
                    'amount' => $session['amount'],
                    'from_pool' => true
                ]);
            }
        }
        
        return response()->json([
            'success' => false,
            'message' => 'No valid pooled session available'
        ]);
    }

    /**
     * Validate checkout session with Hyperpay API
     */
    public function validateCheckoutSession(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'checkout_id' => 'required|string',
            'expected_amount' => 'required|numeric|min:10'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $checkoutId = $request->input('checkout_id');
        $expectedAmount = floatval($request->input('expected_amount'));
        
        try {
            // Validate with Hyperpay API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.hyperpay.access_token'),
            ])->get(config('services.hyperpay.base_url') . '/v1/checkouts/' . $checkoutId, [
                'entityId' => config('services.hyperpay.entity_id_credit')
            ]);

            if ($response->successful()) {
                $sessionData = $response->json();
                $sessionAmount = floatval($sessionData['amount'] ?? 0);
                $sessionStatus = $sessionData['result']['code'] ?? '';
                
                // Check if session is valid and active
                if (!str_starts_with($sessionStatus, '000.')) {
                    return response()->json([
                        'valid' => false,
                        'message' => 'Checkout session is not valid or has expired',
                        'session_status' => $sessionStatus
                    ]);
                }
                
                // Check amount mismatch
                if (abs($sessionAmount - $expectedAmount) > 0.01) {
                    Log::warning('Session amount mismatch detected during validation', [
                        'user_id' => auth()->id(),
                        'checkout_id' => $checkoutId,
                        'session_amount' => $sessionAmount,
                        'expected_amount' => $expectedAmount,
                        'difference' => abs($sessionAmount - $expectedAmount)
                    ]);
                    
                    return response()->json([
                        'valid' => false,
                        'message' => 'Session amount mismatch detected',
                        'session_amount' => $sessionAmount,
                        'expected_amount' => $expectedAmount,
                        'difference' => abs($sessionAmount - $expectedAmount)
                    ]);
                }
                
                // Session is valid
                return response()->json([
                    'valid' => true,
                    'session_amount' => $sessionAmount,
                    'session_status' => $sessionStatus,
                    'message' => 'Checkout session is valid'
                ]);
                
            } else {
                Log::error('Failed to validate checkout session with Hyperpay', [
                    'checkout_id' => $checkoutId,
                    'response_status' => $response->status(),
                    'response_body' => $response->body()
                ]);
                
                return response()->json([
                    'valid' => false,
                    'message' => 'Unable to validate session with payment gateway',
                    'error' => 'Gateway communication error'
                ], 503);
            }
            
        } catch (\Exception $e) {
            Log::error('Exception during checkout session validation', [
                'checkout_id' => $checkoutId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'valid' => false,
                'message' => 'Session validation failed due to technical error',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Log payment errors to activity logs
     */
    public function logPaymentError(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'error_type' => 'required|string',
            'error_message' => 'required|string',
            'amount' => 'required|numeric',
            'gateway' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        
        try {
            // Log the payment error activity
            LogHelper::log(
                'payment_error',
                "Payment failed: {$request->error_message}",
                $user,
                [
                    'error_type' => $request->error_type,
                    'error_message' => $request->error_message,
                    'amount' => $request->amount,
                    'gateway' => $request->gateway,
                    'user_id' => $user->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ],
                'warning'
            );

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Failed to log payment error', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            
            return response()->json(['error' => 'Failed to log error'], 500);
        }
    }

    /**
     * Extract card brand from HyperPay response
     */
    private function extractCardBrand($result)
    {
        // Extract card brand from various possible locations in HyperPay response
        $cardBrand = null;
        
        // Check paymentBrand field (most reliable)
        if (isset($result['paymentBrand'])) {
            $cardBrand = strtoupper($result['paymentBrand']);
        }
        
        // Check card details if available
        elseif (isset($result['card']['bin'])) {
            $bin = $result['card']['bin'];
            $cardBrand = $this->getBrandFromBin($bin);
        }
        
        // Check descriptorTemplate or other brand indicators
        elseif (isset($result['descriptor'])) {
            $descriptor = strtoupper($result['descriptor']);
            if (str_contains($descriptor, 'MADA')) {
                $cardBrand = 'MADA';
            } elseif (str_contains($descriptor, 'VISA')) {
                $cardBrand = 'VISA';
            } elseif (str_contains($descriptor, 'MASTER')) {
                $cardBrand = 'MASTERCARD';
            }
        }
        
        // Fallback to session brand selection
        if (!$cardBrand) {
            $sessionEntityId = session('hyperpay_entity_id');
            $madaEntityId = config('services.hyperpay.entity_id_mada');
            $cardBrand = ($sessionEntityId === $madaEntityId) ? 'MADA' : 'CREDIT_CARD';
        }
        
        // Normalize brand names
        return $this->normalizeCardBrand($cardBrand);
    }
    
    /**
     * Get card brand from BIN (Bank Identification Number)
     */
    private function getBrandFromBin($bin)
    {
        $bin = substr($bin, 0, 6); // Use first 6 digits
        
        // MADA BIN ranges (Saudi domestic cards)
        $madaBins = [
            '446404', '446405', '446406', '446407', '446408', '446409',
            '457865', '457866', '457867', '457868', '457869',
            '486094', '486095', '486096', '486097', '486098', '486099',
            '504300', '504301', '504302', '504303', '504304', '504305',
            '524130', '524131', '524132', '524133', '524134', '524135',
            '529415', '529416', '529417', '529418', '529419',
            '968208', '968209', '968210', '968211', '968212', '968213'
        ];
        
        foreach ($madaBins as $madaBin) {
            if (str_starts_with($bin, $madaBin)) {
                return 'MADA';
            }
        }
        
        // Visa BIN ranges
        if (str_starts_with($bin, '4')) {
            return 'VISA';
        }
        
        // Mastercard BIN ranges
        if (str_starts_with($bin, '5') || 
            (intval(substr($bin, 0, 4)) >= 2221 && intval(substr($bin, 0, 4)) <= 2720)) {
            return 'MASTERCARD';
        }
        
        return 'UNKNOWN';
    }
    
    /**
     * Normalize card brand names
     */
    private function normalizeCardBrand($brand)
    {
        if (!$brand) return 'UNKNOWN';
        
        $brand = strtoupper(trim($brand));
        
        $brandMap = [
            'VISA' => 'VISA',
            'MASTER' => 'MASTERCARD', 
            'MASTERCARD' => 'MASTERCARD',
            'MADA' => 'MADA',
            'CREDIT_CARD' => 'CREDIT_CARD',
            'UNKNOWN' => 'UNKNOWN'
        ];
        
        return $brandMap[$brand] ?? 'UNKNOWN';
    }

    /**
     * Process successful Hyperpay payment
     */
    private function processSuccessfulHyperpayPayment($user, $amount, $result, $resourcePath)
    {
        $hyperpayTransactionId = $result['id'] ?? null;
        $isTestMode = isset($result['test_mode']) && $result['test_mode'] === true;
        
        // For test mode, add a prefix to avoid conflicts with real transactions
        if ($isTestMode && $hyperpayTransactionId && !str_starts_with($hyperpayTransactionId, 'test-')) {
            $hyperpayTransactionId = 'test-' . $hyperpayTransactionId;
        }
        
        // Double-check for duplicates before processing
        if ($hyperpayTransactionId && !$isTestMode) {
            $existingPayment = Payment::where('hyperpay_transaction_id', $hyperpayTransactionId)->first();
            if ($existingPayment) {
                Log::info('Duplicate payment processing prevented in processSuccessfulHyperpayPayment', [
                    'user_id' => $user->id,
                    'hyperpay_transaction_id' => $hyperpayTransactionId,
                    'existing_payment_id' => $existingPayment->id,
                    'amount' => $amount
                ]);
                return; // Exit early to prevent duplicate processing
            }
        }
        
        try {
            DB::beginTransaction();
            
            // Extract card brand from HyperPay response
            $cardBrand = $this->extractCardBrand($result);
            
            // Get or create wallet
            $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);
            
            // Create payment record with test mode indicator and card brand
            $paymentNotes = $isTestMode 
                ? "[TEST MODE] {$cardBrand} payment via HyperPay - Transaction ID: " . ($hyperpayTransactionId ?? 'N/A')
                : "{$cardBrand} payment via HyperPay - Transaction ID: " . ($hyperpayTransactionId ?? 'N/A');
                
            $payment = Payment::create([
                'user_id' => $user->id,
                'payment_type' => 'credit_card',
                'card_brand' => $cardBrand,
                'amount' => $amount,
                'status' => 'approved',
                'notes' => $paymentNotes,
                'hyperpay_transaction_id' => $hyperpayTransactionId
            ]);
            
            // Create wallet transaction using the deposit method
            $transactionDescription = $isTestMode 
                ? "[TEST MODE] Wallet top-up via {$cardBrand} (HyperPay)" 
                : "Wallet top-up via {$cardBrand} (HyperPay)";
                
            $transaction = $wallet->deposit(
                $amount,
                $transactionDescription,
                $payment,
                [
                    'payment_method' => 'credit_card',
                    'card_brand' => $cardBrand,
                    'payment_id' => $payment->id,
                    'gateway' => 'hyperpay',
                    'hyperpay_result' => $result,
                    'resourcePath' => $resourcePath,
                    'transaction_id' => $hyperpayTransactionId,
                    'test_mode' => $isTestMode,
                ]
            );
            
            // Log the wallet recharge activity
            $logMessage = $isTestMode
                ? "TEST MODE: Wallet topped up with {$amount} SAR via {$cardBrand} (HyperPay)"
                : "Wallet topped up with {$amount} SAR via {$cardBrand} (HyperPay)";
                
            LogHelper::logWalletRecharge($wallet, $logMessage, [
                'amount' => $amount,
                'payment_method' => 'credit_card',
                'card_brand' => $cardBrand,
                'payment_id' => $payment->id,
                'transaction_id' => $transaction->id,
                'gateway' => 'hyperpay',
                'hyperpay_transaction_id' => $hyperpayTransactionId,
                'test_mode' => $isTestMode
            ]);
            
            DB::commit();
            
            // Clear session amount
            session()->forget('hyperpay_amount');
            
            Log::info($isTestMode ? "TEST MODE: {$cardBrand} payment processed successfully via HyperPay" : "{$cardBrand} payment processed successfully via HyperPay", [
                'user_id' => $user->id,
                'amount' => $amount,
                'card_brand' => $cardBrand,
                'payment_id' => $payment->id,
                'transaction_id' => $transaction->id,
                'hyperpay_transaction_id' => $hyperpayTransactionId,
                'test_mode' => $isTestMode
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Hyperpay payment processing failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'amount' => $amount,
                'result' => $result,
                'error' => $e->getMessage()
            ]);
            
            // Log the processing error as well
            $this->logFailedHyperpayPayment($user, $amount, 'PROCESSING_ERROR', $e->getMessage(), $result);
        }
    }

    /**
     * Log failed Hyperpay payment to activity logs
     */
    private function logFailedHyperpayPayment($user, $amount, $code, $description, $result)
    {
        try {
            // Determine error type based on code
            $errorType = $this->getHyperpayErrorType($code);
            $errorMessage = $this->getHyperpayErrorMessage($code, $description);
            
            // Log the payment error activity
            LogHelper::log(
                'payment_error',
                "Hyperpay payment failed: {$errorMessage} (Amount: {$amount} SAR)",
                $user,
                [
                    'error_type' => $errorType,
                    'error_message' => $errorMessage,
                    'amount' => $amount,
                    'gateway' => 'hyperpay',
                    'hyperpay_code' => $code,
                    'hyperpay_description' => $description,
                    'hyperpay_result' => $result,
                    'user_id' => $user->id
                ],
                'warning'
            );

            // Clear session amount for failed payments too
            session()->forget('hyperpay_amount');

            Log::warning('Hyperpay payment failed', [
                'user_id' => $user->id,
                'amount' => $amount,
                'code' => $code,
                'description' => $description,
                'error_type' => $errorType
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to log Hyperpay payment error', [
                'user_id' => $user->id,
                'amount' => $amount,
                'code' => $code,
                'description' => $description,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get error type based on Hyperpay result code
     */
    private function getHyperpayErrorType($code)
    {
        if (!$code) return 'unknown_error';
        
        // Map common Hyperpay codes to error types
        $errorTypes = [
            '100.100.101' => 'invalid_card',
            '100.100.201' => 'invalid_account',
            '100.100.303' => 'invalid_currency',
            '100.100.500' => 'invalid_amount',
            '100.100.600' => 'invalid_format',
            '100.100.700' => 'invalid_length',
            '100.380.401' => 'user_cancelled',
            '100.380.501' => 'transaction_declined',
            '800.100.151' => 'transaction_declined',
            '800.100.152' => 'transaction_declined_authorization',
            '800.100.153' => 'transaction_declined',
            '800.100.162' => 'insufficient_funds',
            '800.100.163' => 'card_expired',
            '800.100.164' => 'card_restricted',
            '800.100.165' => 'card_blocked',
            '800.100.168' => 'transaction_not_permitted',
            '800.100.169' => 'transaction_not_permitted',
            '800.100.170' => 'transaction_limit_exceeded',
            '800.100.171' => 'invalid_pin',
            '800.100.172' => 'pin_tries_exceeded',
            '800.100.190' => 'transaction_cancelled',
            '800.100.191' => 'transaction_cancelled',
            '800.100.192' => 'transaction_cancelled',
            '800.100.193' => 'transaction_cancelled',
            '800.100.194' => 'transaction_cancelled',
            '800.100.195' => 'transaction_cancelled',
            '800.100.196' => 'transaction_cancelled',
            '800.100.197' => 'transaction_cancelled',
            '800.100.198' => 'transaction_cancelled',
            '800.100.199' => 'transaction_cancelled',
            '900.100.100' => 'timeout_error',
            '900.100.200' => 'timeout_error',
            '900.100.300' => 'timeout_error'
        ];

        return $errorTypes[$code] ?? 'payment_failed';
    }

    /**
     * Get user-friendly error message based on Hyperpay result code
     */
    private function getHyperpayErrorMessage($code, $description)
    {
        if (!$code) return $description ?: 'Payment failed due to unknown error';
        
        // Map common codes to user-friendly messages
        $messages = [
            '100.100.101' => 'Invalid card number or card details',
            '100.100.201' => 'Invalid account information',
            '100.100.303' => 'Currency not supported',
            '100.100.500' => 'Invalid payment amount',
            '100.100.600' => 'Invalid data format',
            '100.100.700' => 'Invalid data length',
            '100.380.401' => 'Payment cancelled by user',
            '100.380.501' => 'Transaction declined by bank',
            '800.100.151' => 'Transaction declined by bank',
            '800.100.152' => 'Transaction declined by authorization system. Please check: 1) Card is enabled for online payments 2) Sufficient balance 3) Use test cards in test environment',
            '800.100.153' => 'Transaction declined by bank',
            '800.100.162' => 'Insufficient funds in account',
            '800.100.163' => 'Card has expired',
            '800.100.164' => 'Card is restricted',
            '800.100.165' => 'Card is blocked',
            '800.100.168' => 'Transaction not permitted for this card',
            '800.100.169' => 'Transaction not permitted for this card',
            '800.100.170' => 'Transaction limit exceeded',
            '800.100.171' => 'Invalid PIN entered',
            '800.100.172' => 'Too many PIN attempts',
            '800.100.190' => 'Transaction cancelled',
            '800.100.191' => 'Transaction cancelled',
            '800.100.192' => 'Transaction cancelled',
            '800.100.193' => 'Transaction cancelled',
            '800.100.194' => 'Transaction cancelled',
            '800.100.195' => 'Transaction cancelled',
            '800.100.196' => 'Transaction cancelled',
            '800.100.197' => 'Transaction cancelled',
            '800.100.198' => 'Transaction cancelled',
            '800.100.199' => 'Transaction cancelled',
            '900.100.100' => 'Payment timeout - please try again',
            '900.100.200' => 'Payment timeout - please try again',
            '900.100.300' => 'Payment timeout - please try again'
        ];

        return $messages[$code] ?? ($description ?: 'Payment failed');
    }

    /**
     * Get Hyperpay form HTML via AJAX
     * This creates a fresh checkout session and returns the form HTML
     */
    public function getHyperpayForm(Request $request)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:10|max:50000',
                'brand' => 'nullable|string|in:credit_card,mada_card',
            ]);

            $amount = floatval($request->amount);
            $brand = $request->input('brand', 'credit_card');
            $user = auth()->user();
            $merchantTransactionId = uniqid('txn_');

            // Build payload and entityId based on brand
            if ($brand === 'mada_card') {
                $entityId = config('services.hyperpay.entity_id_mada');
                $formBrand = 'MADA';
            } else {
                $entityId = config('services.hyperpay.entity_id_credit');
                $formBrand = 'VISA MASTER';
            }

            // Validate entity ID is configured
            if (empty($entityId)) {
                Log::error('Missing entity ID for payment brand', [
                    'brand' => $brand,
                    'entity_id' => $entityId,
                    'config_key' => $brand === 'mada_card' ? 'services.hyperpay.entity_id_mada' : 'services.hyperpay.entity_id_credit'
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => $brand === 'mada_card' ? 
                        'MADA payment is not configured. Please contact support.' : 
                        'Credit card payment is not configured. Please contact support.'
                ], 500);
            }

            $payload = [
                'entityId' => $entityId,
                'amount' => number_format($amount, 2, '.', ''),
                'currency' => config('services.hyperpay.currency'),
                'paymentType' => 'DB',
                'merchantTransactionId' => $merchantTransactionId,
                'customer.email' => $user->email,
                'testMode' => 'EXTERNAL',
                'customParameters[3DS2_enrolled]' => 'true',
                'customParameters[3DS2_flow]' => 'challenge',
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.hyperpay.access_token'),
            ])->asForm()->post(config('services.hyperpay.base_url') . 'v1/checkouts', $payload);

            if ($response->failed()) {
                $responseBody = $response->json();
                $errorMessage = $responseBody['result']['description'] ?? 'Failed to create checkout session';
                
                Log::error('HyperPay checkout creation failed', [
                    'brand' => $brand,
                    'entity_id' => $entityId,
                    'amount' => $amount,
                    'status_code' => $response->status(),
                    'response' => $responseBody,
                    'payload' => $payload
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => $brand === 'mada_card' ? 
                        'Mada card payment initialization failed: ' . $errorMessage : 
                        'Payment initialization failed: ' . $errorMessage
                ], 500);
            }

            $data = $response->json();
            $checkoutId = $data['id'] ?? null;

            if (!$checkoutId) {
                Log::error('HyperPay invalid response', [
                    'brand' => $brand,
                    'response' => $data
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid response from payment gateway'
                ], 500);
            }

            // Store amount and entity ID in session for later processing
            session([
                'hyperpay_amount' => $amount,
                'hyperpay_entity_id' => $entityId,
            ]);

            // Generate form HTML
            $formHtml = view('wallet.partials.hyperpay-form', [
                'checkoutId' => $checkoutId,
                'amount' => $amount,
                'brand' => $brand,
            ])->render();

            return response()->json([
                'success' => true,
                'checkout_id' => $checkoutId,
                'amount' => $amount,
                'html' => $formHtml
            ]);
        } catch (\Exception $e) {
            logger()->error('Hyperpay form creation failed', [
                'error' => $e->getMessage(),
                'amount' => $request->amount ?? 'unknown',
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to create payment form. Please try again.'
            ], 500);
        }
    }

    /**
     * Redirect to Hyperpay's hosted checkout page
     * This approach bypasses CSP issues as payment happens on Hyperpay's domain
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function redirectToHyperpay(Request $request)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:10|max:50000',
            ]);

            $amount = floatval($request->amount);
            $user = auth()->user();

            // Create checkout session with Hyperpay API
            $entityId = config('services.hyperpay.entity_id_credit');
            $merchantTransactionId = uniqid('txn_');
            
            // Let's use a simpler approach - direct to hosted checkout
            // instead of trying to embed the form
            
            // Prepare the redirect URL for the hosted payment page
            $baseUrl = "https://eu-test.oppwa.com";
            $returnUrl = route('wallet.hyperpay.status');
            
            // Log what we're about to do
            Log::info('Preparing Hyperpay hosted checkout', [
                'user_id' => $user->id,
                'amount' => $amount,
                'entity_id' => $entityId,
                'return_url' => $returnUrl
            ]);
            
            // For troubleshooting, let's create a simple HTML with direct link
            $formHtml = view('wallet.partials.hyperpay-redirect', [
                'amount' => $amount,
                'redirect_url' => "https://eu-test.oppwa.com/v1/paymentWidgets.js?checkoutId=demo-checkout-id",
                'checkout_id' => 'demo-checkout-id',
                'is_test_mode' => true
            ])->render();
            
            return response()->json([
                'success' => true,
                'checkout_id' => 'demo-checkout-id',
                'amount' => $amount,
                'html' => $formHtml,
                'message' => 'Using test mode - click the button to simulate payment'
            ]);
            
            /* Commented out for troubleshooting
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.hyperpay.access_token'),
            ])->asForm()->post(config('services.hyperpay.base_url') . 'v1/checkouts', [
                'entityId' => $entityId,
                'amount' => number_format($amount, 2, '.', ''),
                'currency' => config('services.hyperpay.currency', 'SAR'),
                'paymentType' => 'DB',
                'merchantTransactionId' => $merchantTransactionId,
                'customer.email' => $user->email,
                'testMode' => 'EXTERNAL',
                'customParameters[3DS2_enrolled]' => 'true',
                'customParameters[3DS2_flow]' => 'challenge',
                // Add returnUrl to handle payment result
                'shopperResultUrl' => $returnUrl
            ]);

            if ($response->failed()) {
                $errorBody = $response->body();
                Log::error('Failed to create Hyperpay hosted checkout', [
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'response' => $errorBody,
                    'status' => $response->status()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to initialize payment gateway: ' . substr($errorBody, 0, 100),
                    'details' => $response->json() ?? 'No details available'
                ], 500);
            }

            $data = $response->json();
            $checkoutId = $data['id'] ?? null;

            if (!$checkoutId) {
                Log::error('Missing checkout ID in Hyperpay response', [
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'response' => $data
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid response from payment gateway',
                    'details' => $data
                ], 500);
            }

            // Store amount in session for later processing
            session(['hyperpay_amount' => $amount]);

            // Generate the hosted checkout URL
            $redirectUrl = config('services.hyperpay.base_url') . "v1/paymentWidgets.js?checkoutId={$checkoutId}";
            $hostedPageUrl = "https://eu-test.oppwa.com/v1/paymentPage?checkoutId={$checkoutId}";

            Log::info('Created Hyperpay hosted checkout', [
                'user_id' => $user->id,
                'amount' => $amount,
                'checkout_id' => $checkoutId
            ]);
            
            // Render the partial template
            $formHtml = view('wallet.partials.hyperpay-redirect', [
                'amount' => $amount,
                'redirect_url' => $hostedPageUrl,
                'checkout_id' => $checkoutId
            ])->render();

            return response()->json([
                'success' => true,
                'checkout_id' => $checkoutId,
                'amount' => $amount,
                'redirect_url' => $hostedPageUrl,
                'html' => $formHtml
            ]);
            */

        } catch (\Exception $e) {
            Log::error('Hyperpay redirect error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'amount' => $request->amount ?? 'unknown'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}
