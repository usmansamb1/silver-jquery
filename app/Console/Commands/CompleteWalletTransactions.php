<?php

namespace App\Console\Commands;

use App\Models\Wallet;
use App\Models\WalletApprovalRequest;
use App\Models\WalletTransaction;
use App\Models\ApprovalStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompleteWalletTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallet:complete-transactions 
                            {--id= : Fix a specific wallet approval request by ID} 
                            {--force : Force reprocessing even for transactions marked as complete}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Complete any wallet transactions for approved requests that are not reflected in the user wallet';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for incomplete wallet transactions...');
        
        // Handle specific request ID if provided
        if ($this->option('id')) {
            $specificId = $this->option('id');
            $this->info("Processing specific request ID: $specificId");
            
            $request = WalletApprovalRequest::with(['payment', 'user'])
                ->where('id', $specificId)
                ->first();
                
            if (!$request) {
                $this->error("Request with ID $specificId not found");
                return 1;
            }
            
            $this->processRequest($request);
            return 0;
        }
        
        // Get approved requests that haven't been completed
        $query = WalletApprovalRequest::with(['payment', 'user'])
            ->where('status', ApprovalStatus::APPROVED)
            ->whereNotNull('payment_id');
            
        // Only process incomplete transactions unless --force is used
        if (!$this->option('force')) {
            $query->where(function($q) {
                $q->where('transaction_complete', false)
                  ->orWhereNull('transaction_complete');
            });
        }
        
        $requests = $query->get();
        
        $this->info('Found ' . $requests->count() . ' approved requests to process');
        
        $processed = 0;
        $errors = 0;
        
        foreach ($requests as $request) {
            $result = $this->processRequest($request);
            if ($result) {
                $processed++;
            } else {
                $errors++;
            }
        }
        
        $this->info('Command completed. Processed: ' . $processed . ', Errors: ' . $errors);
        return 0;
    }
    
    /**
     * Process a single wallet approval request
     *
     * @param WalletApprovalRequest $request
     * @return bool Success or failure
     */
    protected function processRequest(WalletApprovalRequest $request)
    {
        $this->line('Processing request: ' . $request->id . ' (' . $request->reference_no . ')');
        
        try {
            DB::beginTransaction();
            
            // Check if payment exists
            if (!$request->payment) {
                $this->warn('Payment not found for request: ' . $request->id);
                DB::rollBack();
                return false;
            }
            
            // Check if the wallet already has a transaction for this payment
            $existingTransaction = WalletTransaction::where('reference_id', $request->payment->id)
                ->where('reference_type', get_class($request->payment))
                ->first();
                
            if ($existingTransaction && !$this->option('force')) {
                $this->warn('Transaction already exists for payment: ' . $request->payment->id);
                
                // Just mark the request as completed
                $request->transaction_complete = true;
                $request->save();
                
                DB::commit();
                return true;
            }
            
            // Get or create user wallet
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $request->user_id],
                ['balance' => 0]
            );
            
            // Complete the transaction
            $oldBalance = $wallet->balance;
            
            $transaction = $wallet->deposit(
                $request->payment->amount,
                'Wallet top-up via ' . $request->payment->payment_type,
                $request->payment,
                [
                    'approval_request_id' => $request->id,
                    'payment_type' => $request->payment->payment_type,
                    'reference_no' => $request->reference_no ?? null,
                    'fixed_by_command' => true
                ]
            );
            
            // Mark payment as approved
            $request->payment->status = 'approved';
            $request->payment->save();
            
            // Mark request as completed
            $request->transaction_complete = true;
            $request->save();
            
            $this->info('Successfully completed transaction for request: ' . $request->id);
            $this->line('  User: ' . $request->user->name . ' (' . $request->user_id . ')');
            $this->line('  Amount: ' . $request->payment->amount);
            $this->line('  Old Balance: ' . $oldBalance);
            $this->line('  New Balance: ' . $wallet->balance);
            
            DB::commit();
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error processing request ' . $request->id . ': ' . $e->getMessage());
            Log::error('Error in CompleteWalletTransactions command', [
                'request_id' => $request->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
}
