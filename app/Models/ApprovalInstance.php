<?php

namespace App\Models;

use App\Notifications\ApprovalRequiredNotification;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalInstance extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'approval_workflow_id',
        'approvable_type',
        'approvable_id',
        'initiated_by',
        'status',
        'completed_at'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    /**
     * Get the workflow for this instance.
     */
    public function workflow()
    {
        return $this->belongsTo(ApprovalWorkflow::class, 'approval_workflow_id');
    }

    /**
     * Get the approvals for this instance.
     */
    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }

    /**
     * Get the user who initiated this approval instance.
     */
    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    /**
     * Get the approvable entity.
     */
    public function approvable()
    {
        return $this->morphTo();
    }

    /**
     * Get the current active approval.
     */
    public function currentApproval()
    {
        return $this->approvals()
            ->where('action', 'pending')
            ->orderBy('created_at')
            ->first();
    }

    /**
     * Get the current step.
     */
    public function currentStep()
    {
        $currentApproval = $this->currentApproval();
        return $currentApproval ? $currentApproval->step : null;
    }

    /**
     * Process an approval action.
     */
    public function processApproval($user, $action, $comments = null, $filePath = null, $transferredTo = null)
    {
        $currentApproval = $this->currentApproval();
        
        if (!$currentApproval) {
            return false;
        }
        
        $currentApproval->update([
            'user_id' => $user->id,
            'action' => $action,
            'comments' => $comments,
            'file_path' => $filePath,
            'transferred_to' => $transferredTo,
        ]);
        
        // If rejected, mark the whole instance as rejected
        if ($action == 'rejected') {
            $this->update([
                'status' => 'rejected',
                'completed_at' => now(),
            ]);
            return true;
        }
        
        // If approved, check if there are more steps
        $nextStep = $this->workflow->getNextStep($currentApproval->step);
        
        if (!$nextStep) {
            // No more steps, mark the instance as approved
            $this->update([
                'status' => 'approved',
                'completed_at' => now(),
            ]);
        } else {
            // Create approval for the next step
            $this->approvals()->create([
                'approval_step_id' => $nextStep->id,
                'action' => 'pending',
            ]);
            
            $this->update([
                'status' => 'in_progress',
            ]);
            
            // Send notifications to the next approvers
            $this->notifyNextApprovers($nextStep);
        }
        
        return true;
    }
    
    /**
     * Notify the next approvers.
     */
    protected function notifyNextApprovers($step)
    {
        $approverUsers = $step->getApproverUsers();
        
        foreach ($approverUsers as $user) {
            // Send notification using Laravel's notification system
            $user->notify(new ApprovalRequiredNotification($this));
            
            if ($this->workflow->notify_by_sms && $user->phone) {
                // For SMS notifications, you would need to implement your SMS provider here
                // Example using Twilio or any SMS provider
                // $this->sendSms($user->phone, "Your approval is required. Please check your dashboard.");
            }
        }
    }
} 