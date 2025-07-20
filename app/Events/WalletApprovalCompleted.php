<?php

namespace App\Events;

use App\Models\WalletApprovalRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WalletApprovalCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $approvalRequest;

    /**
     * Create a new event instance.
     */
    public function __construct(WalletApprovalRequest $approvalRequest)
    {
        $this->approvalRequest = $approvalRequest;
    }
} 