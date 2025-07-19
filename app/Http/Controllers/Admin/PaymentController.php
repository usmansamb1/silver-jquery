<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\ApprovalWorkflow;
use App\Models\ApprovalInstance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['user', 'approvalInstance.workflow', 'approvalInstance.approvals'])
            ->latest()
            ->paginate(10);

        return view('admin.payments.index', compact('payments'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['user', 'approvalInstance.workflow', 'approvalInstance.approvals.user', 'approvalInstance.approvals.step']);
        
        // Get the approval workflow steps and current status
        $approvalWorkflow = null;
        $currentApproval = null;
        $approvalHistory = collect();
        
        if ($payment->approvalInstance) {
            $approvalWorkflow = $payment->approvalInstance->workflow;
            $currentApproval = $payment->approvalInstance->currentApproval();
            $approvalHistory = $payment->approvalInstance->approvals()
                ->with(['user', 'step'])
                ->where('action', '!=', 'pending')
                ->orderBy('created_at', 'desc')
                ->get();
        }
        
        return view('admin.payments.show', compact('payment', 'approvalWorkflow', 'currentApproval', 'approvalHistory'));
    }

    public function approve(Payment $payment)
    {
        try {
            DB::beginTransaction();
            
            // Check if payment is already in an approval workflow
            if (!$payment->approvalInstance) {
                // Find the appropriate workflow for payments
                $workflow = ApprovalWorkflow::where('model_type', Payment::class)
                    ->where('is_active', true)
                    ->first();
                
                if (!$workflow) {
                    throw new \Exception('No active workflow found for payments');
                }
                
                // Start the approval workflow
                $payment->startApprovalWorkflow($workflow->id);
            } else {
                // Get the current approval
                $instance = $payment->approvalInstance;
                $currentApproval = $instance->currentApproval();
                
                if (!$currentApproval) {
                    throw new \Exception('No pending approval found');
                }
                
                // Check if user can approve this step
                $currentStep = $currentApproval->step;
                $approverUsers = $currentStep->getApproverUsers();
                $canApprove = $approverUsers->contains('id', auth()->id());
                
                if (!$canApprove) {
                    throw new \Exception('You are not authorized to approve this payment');
                }
                
                // Process the approval
                $instance->processApproval(
                    auth()->user(),
                    'approved',
                    'Approved by admin',
                    null,
                    null
                );
                
                // If this was the final approval, update the payment status
                if ($instance->status === 'approved') {
                    $payment->update([
                        'status' => 'approved'
                    ]);
                    
                    // Add amount to user's wallet
                    $payment->user->increment('wallet_balance', $payment->amount);
                }
            }
            
            DB::commit();
            
            Log::info('Payment approval process initiated', [
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'user_id' => $payment->user_id,
                'initiated_by' => auth()->id()
            ]);

            return redirect()
                ->route('admin.payments.index')
                ->with('success', __('admin-payments.payment_approval_process_initiated_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Payment approval process failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->back()
                ->with('error', __('admin-payments.failed_to_process_payment') . ': ' . $e->getMessage());
        }
    }

    public function reject(Payment $payment)
    {
        try {
            DB::beginTransaction();
            
            // Check if payment is already in an approval workflow
            if ($payment->approvalInstance) {
                // Get the current approval
                $instance = $payment->approvalInstance;
                $currentApproval = $instance->currentApproval();
                
                if (!$currentApproval) {
                    throw new \Exception('No pending approval found');
                }
                
                // Check if user can reject this step
                $currentStep = $currentApproval->step;
                $approverUsers = $currentStep->getApproverUsers();
                $canReject = $approverUsers->contains('id', auth()->id());
                
                if (!$canReject) {
                    throw new \Exception('You are not authorized to reject this payment');
                }
                
                // Process the rejection
                $instance->processApproval(
                    auth()->user(),
                    'rejected',
                    'Rejected by admin',
                    null,
                    null
                );
                
                // Update payment status
                $payment->update([
                    'status' => 'rejected'
                ]);
            } else {
                // Simply reject the payment without workflow
                $payment->update([
                    'status' => 'rejected',
                ]);
            }
            
            DB::commit();
            
            Log::info('Payment rejected', [
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'user_id' => $payment->user_id,
                'rejected_by' => auth()->id()
            ]);

            return redirect()
                ->route('admin.payments.index')
                ->with('success', __('admin-payments.payment_rejected_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Payment rejection failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->back()
                ->with('error', __('admin-payments.failed_to_reject_payment') . ': ' . $e->getMessage());
        }
    }
} 