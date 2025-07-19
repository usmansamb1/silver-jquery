<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletApprovalRequest;
use App\Models\WalletApprovalStep;
use App\Notifications\WalletRequestStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class WalletRequestController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display a listing of wallet top-up requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = WalletApprovalRequest::with(['user', 'payment'])
            ->orderBy('created_at', 'desc');

        // Filter by payment type
        if ($request->has('payment_type') && !empty($request->payment_type)) {
            $query->whereHas('payment', function ($q) use ($request) {
                $q->where('payment_type', $request->payment_type);
            });
        }

        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Filter by amount range
        if ($request->has('min_amount') && is_numeric($request->min_amount)) {
            $query->whereHas('payment', function ($q) use ($request) {
                $q->where('amount', '>=', $request->min_amount);
            });
        }

        if ($request->has('max_amount') && is_numeric($request->max_amount)) {
            $query->whereHas('payment', function ($q) use ($request) {
                $q->where('amount', '<=', $request->max_amount);
            });
        }

        // Filter by date range
        if ($request->has('start_date') && !empty($request->start_date)) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && !empty($request->end_date)) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $requests = $query->paginate(10);

        // Get payment types for filter dropdown
        $paymentTypes = Payment::PAYMENT_TYPES;

        return view('admin.wallet_requests.index', [
            'requests' => $requests,
            'paymentTypes' => $paymentTypes,
            'filters' => $request->all(),
        ]);
    }

    /**
     * Display the specified wallet request.
     *
     * @param  string  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $walletRequest = WalletApprovalRequest::with([
            'user', 
            'payment',
            'steps.user'
        ])->findOrFail($id);

        return view('admin.wallet_requests.show', [
            'walletRequest' => $walletRequest
        ]);
    }

    /**
     * Approve the specified wallet request.
     *
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve($id)
    {
        $walletRequest = WalletApprovalRequest::with(['user', 'payment', 'steps.user'])
            ->findOrFail($id);

        // Check if request is still pending
        if ($walletRequest->status !== 'pending') {
            return redirect()
                ->route('admin.wallet-requests.show', $walletRequest->id)
                ->with('error', __('wallet_messages.request_cannot_be_approved_not_pending'));
        }

        try {
            DB::beginTransaction();

            // Update wallet request status
            $walletRequest->update([
                'status' => 'approved',
                'current_step' => 0
            ]);

            // Update payment status
            if ($walletRequest->payment) {
                $walletRequest->payment->update(['status' => 'approved']);
            }

            // Update user's wallet balance
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $walletRequest->user_id],
                ['balance' => 0]
            );
            
            // Only proceed if there's a payment with an amount
            if ($walletRequest->payment && $walletRequest->payment->amount > 0) {
                // Instead of direct increment, use the deposit method to create a transaction record
                $wallet->deposit(
                    $walletRequest->payment->amount,
                    'Wallet top-up via ' . $walletRequest->payment->payment_type . ' (Admin Approval)',
                    $walletRequest->payment,
                    [
                        'approval_request_id' => $walletRequest->id,
                        'approved_by' => auth()->id(),
                        'approval_method' => 'admin_direct'
                    ]
                );
                
                Log::info('Wallet balance updated by admin approval', [
                    'user_id' => $walletRequest->user_id,
                    'request_id' => $walletRequest->id,
                    'amount' => $walletRequest->payment->amount,
                    'new_balance' => $wallet->fresh()->balance
                ]);
            }
            
            // Mark all steps as administratively approved
            foreach ($walletRequest->steps as $step) {
                if ($step->status === 'pending') {
                    $step->update([
                        'status' => 'admin_approved',
                        'comment' => 'Approved by administrator',
                        'processed_at' => now()
                    ]);
                }
            }

            // Send notifications to all users in the approval cycle
            $approvalUsers = $walletRequest->steps->pluck('user')->unique();
            
            // Add the request owner as well
            if ($walletRequest->user && !$approvalUsers->contains('id', $walletRequest->user->id)) {
                $approvalUsers->push($walletRequest->user);
            }
            
            Notification::send(
                $approvalUsers, 
                new WalletRequestStatusChanged(
                    $walletRequest,
                    'approved',
                    'This request has been approved by the administrator.'
                )
            );

            DB::commit();

            return redirect()
                ->route('admin.wallet-requests.show', $walletRequest->id)
                ->with('success', __('wallet_messages.request_approved_successfully'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to approve wallet request by admin', [
                'request_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->route('admin.wallet-requests.show', $walletRequest->id)
                ->with('error', __('wallet_messages.failed_to_approve', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Reject the specified wallet request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Request $request, $id)
    {
        // Debug log to see what's being received
        Log::info('Wallet Request Rejection', [
            'request_data' => $request->all(),
            'request_id' => $id
        ]);

        $walletRequest = WalletApprovalRequest::with(['user', 'payment', 'steps.user'])
            ->findOrFail($id);

        // Check if request is still pending
        if ($walletRequest->status !== 'pending') {
            return redirect()
                ->route('admin.wallet-requests.show', $walletRequest->id)
                ->with('error', __('wallet_messages.request_cannot_be_rejected_not_pending'));
        }

        // Validate rejection reason
        $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ]);

        try {
            DB::beginTransaction();

            // Update wallet request status
            $walletRequest->update([
                'status' => 'rejected',
                'current_step' => 0,
                'rejection_reason' => $request->rejection_reason
            ]);

            // Update payment status
            if ($walletRequest->payment) {
                $walletRequest->payment->update(['status' => 'rejected']);
            }
            
            // Mark all steps as administratively rejected
            foreach ($walletRequest->steps as $step) {
                if ($step->status === 'pending') {
                    $step->update([
                        'status' => 'admin_rejected',
                        'comment' => 'Rejected by administrator: ' . $request->rejection_reason,
                        'processed_at' => now()
                    ]);
                }
            }

            // Send notifications to all users in the approval cycle
            $approvalUsers = $walletRequest->steps->pluck('user')->unique();
            
            // Add the request owner as well
            if ($walletRequest->user && !$approvalUsers->contains('id', $walletRequest->user->id)) {
                $approvalUsers->push($walletRequest->user);
            }
            
            Notification::send(
                $approvalUsers, 
                new WalletRequestStatusChanged(
                    $walletRequest,
                    'rejected',
                    'This request has been rejected by the administrator: ' . $request->rejection_reason
                )
            );

            DB::commit();

            return redirect()
                ->route('admin.wallet-requests.show', $walletRequest->id)
                ->with('success', __('wallet_messages.request_rejected_successfully'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reject wallet request by admin', [
                'request_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->route('admin.wallet-requests.show', $walletRequest->id)
                ->with('error', __('wallet_messages.failed_to_reject', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Reset all filters and redirect back to index.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetFilters()
    {
        return redirect()->route('admin.wallet-requests.index');
    }
} 