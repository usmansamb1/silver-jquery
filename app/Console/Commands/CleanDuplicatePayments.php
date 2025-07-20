<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\WalletTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanDuplicatePayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:clean-duplicates {--dry-run : Show what would be cleaned without actually doing it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up duplicate Hyperpay payments and wallet transactions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('🔍 Scanning for duplicate Hyperpay payments...');
        
        // Find duplicate payments by extracting transaction IDs from notes
        $duplicates = Payment::where('notes', 'LIKE', '%Transaction ID:%')
            ->where('payment_type', 'credit_card')
            ->get()
            ->groupBy(function ($payment) {
                // Extract transaction ID from notes
                preg_match('/Transaction ID: ([a-zA-Z0-9]+)/', $payment->notes, $matches);
                return $matches[1] ?? 'unknown';
            })
            ->filter(function ($group) {
                return $group->count() > 1; // Only groups with duplicates
            });

        if ($duplicates->isEmpty()) {
            $this->info('✅ No duplicate payments found!');
            return 0;
        }

        $this->info("Found {$duplicates->count()} sets of duplicate payments:");

        $totalDuplicates = 0;
        $totalRefundAmount = 0;

        foreach ($duplicates as $transactionId => $payments) {
            $count = $payments->count();
            $amount = $payments->first()->amount;
            $userId = $payments->first()->user_id;
            
            $this->warn("  📋 Transaction ID: {$transactionId}");
            $this->warn("     User ID: {$userId}");
            $this->warn("     Amount: {$amount} SAR");
            $this->warn("     Duplicates: {$count} payments");
            
            // Show payment IDs
            $paymentIds = $payments->pluck('id')->toArray();
            $this->line("     Payment IDs: " . implode(', ', $paymentIds));
            
            $totalDuplicates += ($count - 1); // Don't count the original
            $totalRefundAmount += ($amount * ($count - 1));
        }

        $this->error("💰 Total duplicate amount to refund: {$totalRefundAmount} SAR");
        $this->error("🔢 Total duplicate payments to remove: {$totalDuplicates}");

        if ($dryRun) {
            $this->info('🧪 DRY RUN - No changes made. Use without --dry-run to actually clean duplicates.');
            return 0;
        }

        if (!$this->confirm('Do you want to proceed with cleaning these duplicates?')) {
            $this->info('❌ Operation cancelled.');
            return 0;
        }

        $this->info('🧹 Starting cleanup process...');

        DB::beginTransaction();
        
        try {
            foreach ($duplicates as $transactionId => $payments) {
                // Keep the first payment (oldest) and remove the rest
                $keepPayment = $payments->sortBy('created_at')->first();
                $duplicatePayments = $payments->except($keepPayment->id);
                
                $this->info("  ✅ Keeping payment ID: {$keepPayment->id} (created: {$keepPayment->created_at})");
                
                // Update the kept payment with the hyperpay_transaction_id
                if ($transactionId !== 'unknown') {
                    $keepPayment->update(['hyperpay_transaction_id' => $transactionId]);
                    $this->info("     Updated with hyperpay_transaction_id: {$transactionId}");
                }
                
                foreach ($duplicatePayments as $duplicatePayment) {
                    $this->warn("  🗑️  Removing duplicate payment ID: {$duplicatePayment->id}");
                    
                    // Find and remove associated wallet transactions
                    $walletTransactions = WalletTransaction::where('reference_type', Payment::class)
                        ->where('reference_id', $duplicatePayment->id)
                        ->get();
                    
                    foreach ($walletTransactions as $transaction) {
                        $this->warn("     🔄 Reversing wallet transaction ID: {$transaction->id} (Amount: {$transaction->amount})");
                        
                        // Reverse the wallet balance
                        $wallet = $transaction->wallet;
                        $wallet->balance -= $transaction->amount;
                        $wallet->save();
                        
                        // Delete the transaction
                        $transaction->delete();
                    }
                    
                    // Delete the duplicate payment
                    $duplicatePayment->delete();
                }
            }
            
            DB::commit();
            
            $this->info('✅ Cleanup completed successfully!');
            $this->info("💰 Refunded {$totalRefundAmount} SAR from duplicate transactions");
            $this->info("🗑️  Removed {$totalDuplicates} duplicate payment records");
            
            Log::info('Duplicate payments cleaned up', [
                'total_duplicates_removed' => $totalDuplicates,
                'total_refund_amount' => $totalRefundAmount,
                'cleaned_transaction_ids' => $duplicates->keys()->toArray()
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Cleanup failed: ' . $e->getMessage());
            Log::error('Duplicate payment cleanup failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }

        return 0;
    }
} 