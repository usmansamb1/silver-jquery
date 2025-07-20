<?php

namespace App\Events;

use App\Models\WalletApprovalRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WalletApprovalRejected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $approvalRequest;
    public $comment;

    /**
     * Create a new event instance.
     */
    public function __construct(WalletApprovalRequest $approvalRequest, string $comment)
    {
        $this->approvalRequest = $approvalRequest;
        $this->comment = $comment;
    }
} 