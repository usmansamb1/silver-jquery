<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory, HasUuids;

    /**
     * The allowed payment types.
     */
    const PAYMENT_TYPES = [
        'credit_card' => 'Credit Card',
        'bank_transfer' => 'Bank Transfer',
        'bank_guarantee' => 'Bank Guarantee',
        'bank_lc' => 'Bank LC'
    ];

    protected $fillable = [
        'user_id',
        'amount',
        'payment_type',
        'status',
        'notes',
        'files',
        'transaction_id',
        'hyperpay_transaction_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Get the user that owns the payment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the approval request associated with this payment.
     */
    public function approvalRequest()
    {
        return $this->hasOne(WalletApprovalRequest::class);
    }

    /**
     * Get the files as an array.
     */
    public function getFilesAttribute($value)
    {
        if (empty($value)) {
            return [];
        }
        
        return json_decode($value, true);
    }

    /**
     * Set the files attribute.
     */
    public function setFilesAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['files'] = json_encode($value);
        } else {
            $this->attributes['files'] = $value;
        }
    }

    /**
     * Get the approval instance for this payment.
     */
    public function approvalInstance()
    {
        return $this->belongsTo(ApprovalInstance::class);
    }

    /**
     * Morph relationship for approval instances.
     */
    public function approvalInstances()
    {
        return $this->morphMany(ApprovalInstance::class, 'approvable');
    }

    /**
     * Get the current approval instance.
     */
    public function currentApprovalInstance()
    {
        return $this->approvalInstances()->latest()->first();
    }

    /**
     * Check if the payment is pending approval.
     */
    public function isPendingApproval()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the payment is approved.
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the payment is rejected.
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Start the approval workflow for this payment.
     */
    public function startApprovalWorkflow($workflowId = null)
    {
        // Find the appropriate workflow
        if (!$workflowId) {
            $workflow = ApprovalWorkflow::where('model_type', get_class())->where('is_active', true)->first();
        } else {
            $workflow = ApprovalWorkflow::find($workflowId);
        }

        if (!$workflow) {
            throw new \Exception('No active workflow found for payments');
        }

        // Create the approval instance
        $instance = $this->approvalInstances()->create([
            'approval_workflow_id' => $workflow->id,
            'initiated_by' => auth()->id(),
            'status' => 'pending',
        ]);

        // Get the first step in the workflow
        $firstStep = $workflow->getFirstStep();

        if (!$firstStep) {
            throw new \Exception('Workflow has no steps defined');
        }

        // Create the first approval
        $instance->approvals()->create([
            'approval_step_id' => $firstStep->id,
            'action' => 'pending',
        ]);

        // Update the payment with the approval instance ID
        $this->update([
            'approval_instance_id' => $instance->id,
        ]);

        // Send notifications to the first approvers
        $approverUsers = $firstStep->getApproverUsers();
        
        foreach ($approverUsers as $user) {
            if ($workflow->notify_by_email) {
                // Send email notification
                // TODO: Implement email notification
            }
            
            if ($workflow->notify_by_sms) {
                // Send SMS notification
                // TODO: Implement SMS notification
            }
        }

        return $instance;
    }
}
