<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'approval_instance_id',
        'approval_step_id',
        'user_id',
        'action',
        'comments',
        'file_path',
        'transferred_to'
    ];

    /**
     * Get the instance that owns this approval.
     */
    public function instance()
    {
        return $this->belongsTo(ApprovalInstance::class, 'approval_instance_id');
    }

    /**
     * Get the step that owns this approval.
     */
    public function step()
    {
        return $this->belongsTo(ApprovalStep::class, 'approval_step_id');
    }

    /**
     * Get the user who actioned this approval.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user this approval was transferred to.
     */
    public function transferredTo()
    {
        return $this->belongsTo(User::class, 'transferred_to');
    }

    /**
     * Check if this approval is pending.
     */
    public function isPending()
    {
        return $this->action === 'pending';
    }

    /**
     * Check if this approval is approved.
     */
    public function isApproved()
    {
        return $this->action === 'approved';
    }

    /**
     * Check if this approval is rejected.
     */
    public function isRejected()
    {
        return $this->action === 'rejected';
    }

    /**
     * Check if this approval is transferred.
     */
    public function isTransferred()
    {
        return $this->action === 'transferred';
    }
} 