<?php

namespace App\Listeners;

use App\Events\WalletApprovalCompleted;
use App\Models\Wallet;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class WalletApprovalApprovedListener
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\WalletApprovalCompleted  $event
     * @return void
     */
    public function handle(WalletApprovalCompleted $event)
    {
        $request = $event->approvalRequest;
        
        try {
            Log::info('Processing wallet approval completion', [
                'request_id' => $request->id,
                'user_id' => $request->user_id,
                'amount' => $request->amount,
                'payment_id' => $request->payment_id ?? 'no payment ID',
                'transaction_complete' => $request->transaction_complete ?? false
            ]);
            
            // Skip if transaction is already completed
            if ($request->transaction_complete) {
                Log::info('Skipping already completed transaction', [
                    'request_id' => $request->id,
                    'user_id' => $request->user_id,
                    'payment_id' => $request->payment_id ?? 'no payment ID'
                ]);
                return;
            }
            
            DB::beginTransaction();
            
            // Get the payment associated with this request
            $payment = $request->payment;
            
            if (!$payment) {
                Log::error('Payment not found for approval request', [
                    'request_id' => $request->id
                ]);
                DB::rollBack();
                return;
            }
            
            Log::info('Found payment for approval', [
                'payment_id' => $payment->id,
                'payment_type' => $payment->payment_type,
                'payment_status' => $payment->status,
                'amount' => $payment->amount
            ]);
            
            // Mark payment as approved if not already
            if ($payment->status !== 'approved') {
                $payment->status = 'approved';
                $payment->save();
            }
            
            // Get or create wallet for the user
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $request->user_id],
                ['balance' => 0]
            );
            
            // Add amount to wallet balance and record transaction
            $oldBalance = $wallet->balance;
            
            // Create a wallet transaction record with the payment as reference
            $transaction = $wallet->deposit(
                $payment->amount,
                'Wallet top-up via ' . $payment->payment_type,
                $payment,
                [
                    'approval_request_id' => $request->id,
                    'payment_type' => $payment->payment_type,
                    'payment_notes' => $payment->notes ?? null,
                    'reference_no' => $request->reference_no ?? null
                ]
            );
            
            Log::info('Successfully updated wallet balance', [
                'user_id' => $request->user_id,
                'request_id' => $request->id,
                'payment_id' => $payment->id,
                'transaction_id' => $transaction->id,
                'amount' => $payment->amount,
                'old_balance' => $oldBalance,
                'new_balance' => $wallet->balance,
                'payment_type' => $payment->payment_type,
                'transaction_reference_type' => $transaction->reference_type,
                'transaction_reference_id' => $transaction->reference_id,
                'transaction_details' => [
                    'type' => $transaction->type,
                    'status' => $transaction->status,
                    'description' => $transaction->description
                ]
            ]);
            
            // Update the request status to reflect the completed transaction
            $request->transaction_complete = true;
            $request->save();
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update wallet after approval', [
                'request_id' => $request->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
} 