<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\ApprovalInstance;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ApprovalController extends Controller
{
    /**
     * Display a form for approving or rejecting an item.
     */
    public function showApprovalForm(Request $request, string $instanceId)
    {
        $instance = ApprovalInstance::with(['workflow', 'approvals', 'approvable'])->findOrFail($instanceId);
        $currentApproval = $instance->currentApproval();
        
        // Check if user can approve this step
        $currentStep = $currentApproval ? $currentApproval->step : null;
        if (!$currentStep) {
            return redirect()->back()->withErrors(['error' => 'No pending approval found.']);
        }
        
        $approverUsers = $currentStep->getApproverUsers();
        $canApprove = $approverUsers->contains('id', auth()->id());
        
        if (!$canApprove) {
            return redirect()->back()->withErrors(['error' => 'You are not authorized to approve this item.']);
        }
        
        return view('approval.form', compact('instance', 'currentApproval'));
    }
    
    /**
     * Process an approval action (approve, reject, transfer).
     */
    public function processApproval(Request $request, string $instanceId)
    {
        $validated = $request->validate([
            'action' => 'required|string|in:approved,rejected,transferred',
            'comments' => 'nullable|string',
            'file' => 'nullable|file|max:10240', // Max 10MB
            'transferred_to' => 'required_if:action,transferred|nullable|exists:users,id',
        ]);
        
        $instance = ApprovalInstance::findOrFail($instanceId);
        $currentApproval = $instance->currentApproval();
        
        if (!$currentApproval) {
            return redirect()->back()->withErrors(['error' => 'No pending approval found.']);
        }
        
        $currentStep = $currentApproval->step;
        $approverUsers = $currentStep->getApproverUsers();
        $canApprove = $approverUsers->contains('id', auth()->id());
        
        if (!$canApprove) {
            return redirect()->back()->withErrors(['error' => 'You are not authorized to approve this item.']);
        }
        
        DB::beginTransaction();
        
        try {
            // Handle file upload if provided
            $filePath = null;
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filePath = $file->store('approval-files');
            }
            
            // Process the approval
            $transferredTo = ($validated['action'] == 'transferred') ? $validated['transferred_to'] : null;
            $instance->processApproval(
                auth()->user(),
                $validated['action'],
                $validated['comments'],
                $filePath,
                $transferredTo
            );
            
            // If this is a payment approval, update the payment status accordingly
            if ($instance->approvable_type == Payment::class) {
                $payment = Payment::find($instance->approvable_id);
                if ($payment) {
                    $payment->update([
                        'status' => $instance->status,
                    ]);
                }
            }
            
            DB::commit();
            
            // Determine redirect based on approval type
            $redirectRoute = 'admin.dashboard';
            $message = 'Action processed successfully.';
            
            if ($instance->approvable_type == Payment::class) {
                $redirectRoute = 'wallet.pending-payments';
                $message = 'Payment ' . $validated['action'] . ' successfully.';
            }
            
            return redirect()->route($redirectRoute)->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to process approval: ' . $e->getMessage()]);
        }
    }
    
    /**
     * View approval history for an item.
     */
    public function viewApprovalHistory(Request $request, string $instanceId)
    {
        $instance = ApprovalInstance::with(['workflow', 'approvals.user', 'approvals.step'])->findOrFail($instanceId);
        
        return view('approval.history', compact('instance'));
    }
    
    /**
     * Display approvals that require the current user's attention.
     */
    public function myApprovals()
    {
        $user = auth()->user();
        
        // Get all active approval instances
        $activeInstances = ApprovalInstance::where('status', 'in', ['pending', 'in_progress'])
            ->with(['workflow', 'approvals', 'approvable'])
            ->get();
        
        // Filter to only those the current user can approve
        $pendingApprovals = collect();
        
        foreach ($activeInstances as $instance) {
            $currentApproval = $instance->currentApproval();
            if ($currentApproval) {
                $currentStep = $currentApproval->step;
                $approverUsers = $currentStep->getApproverUsers();
                
                if ($approverUsers->contains('id', $user->id)) {
                    $pendingApprovals->push([
                        'instance' => $instance,
                        'approval' => $currentApproval,
                        'step' => $currentStep,
                    ]);
                }
            }
        }
        
        return view('approval.my-approvals', compact('pendingApprovals'));
    }
} 