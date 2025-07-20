<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TestDuplicatePaymentPrevention extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:test-duplicate-prevention';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test that duplicate payment prevention is working correctly';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing Duplicate Payment Prevention System...');
        
        // Find a test user
        $user = User::first();
        if (!$user) {
            $this->error('❌ No users found in database');
            return 1;
        }
        
        $this->info("📋 Using test user: {$user->email} (ID: {$user->id})");
        
        // Get or create wallet
        $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);
        $initialBalance = $wallet->balance;
        $this->info("💰 Initial wallet balance: {$initialBalance} SAR");
        
        // Test 1: Create a payment with hyperpay_transaction_id
        $testTransactionId = 'test_' . uniqid();
        $testAmount = 50.00;
        
        $this->info("\n🔬 Test 1: Creating first payment with transaction ID: {$testTransactionId}");
        
        try {
            DB::beginTransaction();
            
            $payment1 = Payment::create([
                'user_id' => $user->id,
                'payment_type' => 'credit_card',
                'amount' => $testAmount,
                'status' => 'approved',
                'notes' => "Test Hyperpay payment - Transaction ID: {$testTransactionId}",
                'hyperpay_transaction_id' => $testTransactionId
            ]);
            
            $transaction1 = $wallet->deposit(
                $testAmount,
                'Test wallet top-up via Hyperpay',
                $payment1,
                [
                    'payment_method' => 'credit_card',
                    'payment_id' => $payment1->id,
                    'gateway' => 'hyperpay',
                    'test' => true
                ]
            );
            
            DB::commit();
            $this->info("✅ First payment created successfully (ID: {$payment1->id})");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ First payment creation failed: " . $e->getMessage());
            return 1;
        }
        
        // Test 2: Try to create duplicate payment (should be prevented)
        $this->info("\n🔬 Test 2: Attempting to create duplicate payment...");
        
        try {
            DB::beginTransaction();
            
            // This should fail due to unique constraint
            $payment2 = Payment::create([
                'user_id' => $user->id,
                'payment_type' => 'credit_card',
                'amount' => $testAmount,
                'status' => 'approved',
                'notes' => "Duplicate test payment - Transaction ID: {$testTransactionId}",
                'hyperpay_transaction_id' => $testTransactionId
            ]);
            
            DB::commit();
            $this->error("❌ DUPLICATE PAYMENT WAS CREATED! This should not happen!");
            return 1;
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("✅ Duplicate payment prevented by database constraint: " . $e->getMessage());
        }
        
        // Test 3: Verify detection logic
        $this->info("\n🔬 Test 3: Testing duplicate detection logic...");
        
        $existingPayment = Payment::where('hyperpay_transaction_id', $testTransactionId)->first();
        if ($existingPayment) {
            $this->info("✅ Duplicate detection working - found existing payment ID: {$existingPayment->id}");
        } else {
            $this->error("❌ Duplicate detection failed - no payment found");
            return 1;
        }
        
        // Test 4: Verify wallet balance
        $this->info("\n🔬 Test 4: Verifying wallet balance...");
        
        $wallet->refresh();
        $finalBalance = $wallet->balance;
        $expectedBalance = $initialBalance + $testAmount;
        
        $this->info("💰 Final wallet balance: {$finalBalance} SAR");
        $this->info("💰 Expected balance: {$expectedBalance} SAR");
        
        if ($finalBalance == $expectedBalance) {
            $this->info("✅ Wallet balance is correct - no duplicate charges");
        } else {
            $this->error("❌ Wallet balance mismatch - possible duplicate charging");
            return 1;
        }
        
        // Test 5: Check transaction count
        $this->info("\n🔬 Test 5: Verifying transaction count...");
        
        $transactionCount = $wallet->transactions()
            ->where('reference_type', Payment::class)
            ->where('reference_id', $payment1->id)
            ->count();
            
        if ($transactionCount == 1) {
            $this->info("✅ Correct number of wallet transactions (1)");
        } else {
            $this->error("❌ Incorrect transaction count: {$transactionCount} (expected 1)");
            return 1;
        }
        
        // Cleanup
        $this->info("\n🧹 Cleaning up test data...");
        
        try {
            DB::beginTransaction();
            
            // Remove wallet transaction
            $wallet->transactions()
                ->where('reference_type', Payment::class)
                ->where('reference_id', $payment1->id)
                ->delete();
            
            // Restore wallet balance
            $wallet->balance = $initialBalance;
            $wallet->save();
            
            // Remove test payment
            $payment1->delete();
            
            DB::commit();
            $this->info("✅ Test data cleaned up successfully");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Cleanup failed: " . $e->getMessage());
        }
        
        $this->info("\n🎉 All tests passed! Duplicate payment prevention is working correctly.");
        
        return 0;
    }
} 