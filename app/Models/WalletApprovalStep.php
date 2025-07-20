<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WalletApprovalStep extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'v2_wallet_approval_steps';

    protected $fillable = [
        'request_id',
        'user_id',
        'role',
        'status',       // Keeping for backward compatibility
        'status_id',    // New relationship to StepStatus
        'comment',
        'step_order',
        'processed_at'
    ];

    protected $casts = [
        'processed_at' => 'datetime'
    ];

    /**
     * Get the approval request that owns this step
     */
    public function request()
    {
        return $this->belongsTo(WalletApprovalRequest::class, 'request_id');
    }

    /**
     * Get the user who needs to approve this step
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the status model for this step
     */
    public function stepStatus()
    {
        return $this->belongsTo(StepStatus::class, 'status_id');
    }

    /**
     * Set the status using code and update both status and status_id
     * 
     * @param string $statusCode
     * @return void
     */
    public function setStatusByCode(string $statusCode): void
    {
        // Set the legacy status string
        $this->status = $statusCode;
        
        // Find the corresponding status_id if it exists
        $status = StepStatus::where('code', $statusCode)->first();
        if ($status) {
            $this->status_id = $status->id;
        }
        
        $this->save();
    }

    /**
     * Check if the step is pending
     */
    public function isPending(): bool
    {
        return $this->status === StepStatus::PENDING;
    }

    /**
     * Check if the step is approved
     */
    public function isApproved(): bool
    {
        return $this->status === StepStatus::APPROVED;
    }

    /**
     * Check if the step is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === StepStatus::REJECTED;
    }

    /**
     * Get the status code - with fallback to string status if relationship doesn't exist
     */
    public function getStatusCode(): string
    {
        return $this->stepStatus ? $this->stepStatus->code : $this->status;
    }

    /**
     * Get the status name - with fallback to capitalized status if relationship doesn't exist
     */
    public function getStatusName(): string
    {
        return $this->stepStatus ? $this->stepStatus->name : ucfirst($this->status);
    }

    /**
     * Get the status color - with fallback color if relationship doesn't exist
     */
    public function getStatusColor(): string
    {
        return $this->stepStatus ? $this->stepStatus->color : '#808080';
    }
} 