<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WalletApprovalRequest;
use App\Models\WalletApprovalStep;
use App\Notifications\WalletApprovalNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletApprovalController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get pending approvals where current user is the next approver
        $pendingApprovals = WalletApprovalRequest::with(['payment.user', 'currentStep.approver'])
            ->whereHas('currentStep', function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->where('status', 'pending');
            })
            ->latest()
            ->paginate(10);
            
        return view('admin.wallet-approvals.index', compact('pendingApprovals'));
    }

    public function show($id)
    {
        $request = WalletApprovalRequest::with([
            'payment.user',
            'approvalSteps.approver'
        ])->findOrFail($id);
        
        return view('admin.wallet-approvals.show', compact('request'));
    }

    public function approve(Request $request, $id)
    {
        $approvalRequest = WalletApprovalRequest::with(['payment.user', 'currentStep.approver'])
            ->findOrFail($id);

            // var_dump($approvalRequest->currentStep->user_id);

            // dd(auth()->id());

            // exit();
        // Verify current user is the next approver
        if ($approvalRequest->currentStep->user_id !== auth()->id()) {
            return back()->with('error', 'You are not authorized to approve this request.');
        }

        DB::beginTransaction();
        try {
            // Update current step
            $currentStep = $approvalRequest->currentStep;
            $currentStep->update([
                'status' => 'approved',
                'comments' => $request->comment,
                'processed_at' => now()
            ]);

            // Get next step if exists
            $nextStep = WalletApprovalStep::where('request_id', $approvalRequest->id)
                ->where('step_order', '>', $currentStep->step_order)
                ->orderBy('step_order')
                ->first();

            if ($nextStep) {
                // Update request current step
                $approvalRequest->update(['current_step' => $nextStep->step_order]);
                
                // Notify next approver
                $nextStep->approver->notify(new WalletApprovalNotification($approvalRequest));
            } else {
                // Final approval - update request status and add balance
                $approvalRequest->update(['status' => 'completed']);
                
                // Add amount to user's wallet
                $approvalRequest->payment->user->wallet()->increment('balance', $approvalRequest->payment->amount);
                
                // Notify customer
                $approvalRequest->payment->user->notify(new WalletApprovalNotification($approvalRequest, 'completed'));
            }

            DB::commit();
            return back()->with('success', 'Request approved successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while processing the approval.');
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $approvalRequest = WalletApprovalRequest::with(['payment.user', 'currentStep.approver'])
            ->findOrFail($id);
            
        // Verify current user is the next approver
        if ($approvalRequest->currentStep->user_id !== auth()->id()) {
            return back()->with('error', 'You are not authorized to reject this request.');
        }

        DB::beginTransaction();
        try {
            // Update current step
            $approvalRequest->currentStep->update([
                'status' => 'rejected',
                'comments' => $request->rejection_reason,
                'processed_at' => now()
            ]);

            // Update request status
            $approvalRequest->update(['status' => 'rejected']);
            
            // Notify customer
            $approvalRequest->payment->user->notify(new WalletApprovalNotification($approvalRequest, 'rejected'));

            DB::commit();
            return back()->with('success', 'Request rejected successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while processing the rejection.');
        }
    }
} 