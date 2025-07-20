<?php

namespace App\Listeners;

use App\Events\WalletApprovalRejected;
use App\Models\WalletApprovalRequest;
use App\Notifications\WalletApprovalNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class WalletApprovalRejectedListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(WalletApprovalRejected $event): void
    {
        $request = $event->approvalRequest;
        $comment = $event->comment;

        // Send notification to the user
        $request->user->notify(new WalletApprovalNotification(
            $request,
            'rejected',
            $comment
        ));

        // Record the rejection in wallet_transactions with all required fields
        $wallet = $request->user->wallet;
        $balanceBefore = $wallet->balance;
        $balanceAfter = $balanceBefore;
        $wallet->transactions()->create([
            'user_id'        => $request->user->id,
            'wallet_id'      => $wallet->id,
            'reference_type' => get_class($request->payment),
            'reference_id'   => $request->payment->id,
            'amount'         => $request->amount,
            'balance_before' => $balanceBefore,
            'balance_after'  => $balanceAfter,
            'type'           => 'rejected',
            'status'         => 'rejected',
            'description'    => "Top-up via {$request->payment->payment_type} rejected - #{$request->reference_no}",
            'metadata'       => ['rejection_reason' => $comment],
        ]);
    }
} 