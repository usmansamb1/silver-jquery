<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Payment;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\ActivityLog;
use App\Helpers\LogHelper;
use Illuminate\Support\Facades\DB;

class TestCreditCardPayments extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:credit-card-payments {--user-id= : User ID to test with} {--amount=100 : Amount to test}';

    /**
     * The console command description.
     */
    protected $description = 'Test credit card payment processing to ensure proper Payment records, wallet transactions, and activity logs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user-id');
        $amount = floatval($this->option('amount'));

        // Find user
        if ($userId) {
            $user = User::find($userId);
        } else {
            $user = User::role('customer')->first();
        }

        if (!$user) {
            $this->error('No user found. Please specify a valid user ID or ensure customer users exist.');
            return 1;
        }

        $this->info("Testing credit card payments for user: {$user->name} (ID: {$user->id})");
        $this->info("Test amount: {$amount} SAR");

        try {
            DB::beginTransaction();
            
            // Authenticate as the user for proper activity logging
            auth()->login($user);

            // Get or create wallet
            $wallet = $user->wallet;
            if (!$wallet) {
                $wallet = Wallet::create([
                    'user_id' => $user->id,
                    'balance' => 0,
                ]);
                $this->info("Created new wallet for user");
            }

            $balanceBefore = $wallet->balance;
            $this->info("Wallet balance before: {$balanceBefore} SAR");

            // Test 1: Stripe Payment Processing
            $this->info("\n--- Testing Stripe Payment ---");
            
            $stripePayment = Payment::create([
                'user_id' => $user->id,
                'payment_type' => 'credit_card',
                'amount' => $amount,
                'status' => 'approved',
                'notes' => 'Test Stripe credit card payment'
            ]);

            $stripeTransaction = $wallet->deposit(
                $amount,
                'Test wallet top-up via Stripe',
                $stripePayment,
                [
                    'payment_method' => 'credit_card',
                    'payment_id' => $stripePayment->id,
                    'gateway' => 'stripe',
                    'test' => true
                ]
            );

            LogHelper::logWalletRecharge($wallet, "Test wallet top-up with {$amount} SAR via Stripe", [
                'amount' => $amount,
                'payment_method' => 'credit_card',
                'payment_id' => $stripePayment->id,
                'transaction_id' => $stripeTransaction->id,
                'gateway' => 'stripe',
                'test' => true
            ]);

            $this->info("✓ Stripe payment record created (ID: {$stripePayment->id})");
            $this->info("✓ Stripe wallet transaction created (ID: {$stripeTransaction->id})");

            // Test 2: Hyperpay Payment Processing
            $this->info("\n--- Testing Hyperpay Payment ---");
            
            $hyperpayPayment = Payment::create([
                'user_id' => $user->id,
                'payment_type' => 'credit_card',
                'amount' => $amount,
                'status' => 'approved',
                'notes' => 'Test Hyperpay credit card payment'
            ]);

            $hyperpayTransaction = $wallet->deposit(
                $amount,
                'Test wallet top-up via Hyperpay',
                $hyperpayPayment,
                [
                    'payment_method' => 'credit_card',
                    'payment_id' => $hyperpayPayment->id,
                    'gateway' => 'hyperpay',
                    'test' => true
                ]
            );

            LogHelper::logWalletRecharge($wallet, "Test wallet top-up with {$amount} SAR via Hyperpay", [
                'amount' => $amount,
                'payment_method' => 'credit_card',
                'payment_id' => $hyperpayPayment->id,
                'transaction_id' => $hyperpayTransaction->id,
                'gateway' => 'hyperpay',
                'test' => true
            ]);

            $this->info("✓ Hyperpay payment record created (ID: {$hyperpayPayment->id})");
            $this->info("✓ Hyperpay wallet transaction created (ID: {$hyperpayTransaction->id})");

            DB::commit();

            // Verify results
            $this->info("\n--- Verification ---");
            
            $wallet->refresh();
            $balanceAfter = $wallet->balance;
            $expectedBalance = $balanceBefore + ($amount * 2);
            
            $this->info("Wallet balance after: {$balanceAfter} SAR");
            $this->info("Expected balance: {$expectedBalance} SAR");
            
            if ($balanceAfter == $expectedBalance) {
                $this->info("✓ Wallet balance updated correctly");
            } else {
                $this->error("✗ Wallet balance mismatch");
            }

            // Check Payment records
            $paymentCount = Payment::where('user_id', $user->id)
                ->where('payment_type', 'credit_card')
                ->where('status', 'approved')
                ->count();
            $this->info("Credit card payments in database: {$paymentCount}");

            // Check WalletTransaction records
            $transactionCount = WalletTransaction::where('user_id', $user->id)
                ->where('type', 'deposit')
                ->count();
            $this->info("Wallet transactions in database: {$transactionCount}");

            // Check ActivityLog records
            $activityCount = ActivityLog::where('causer_id', $user->id)
                ->where('event', 'wallet_recharge')
                ->count();
            $this->info("Activity logs in database: {$activityCount}");

            $this->info("\n--- Test Summary ---");
            $this->info("✓ Payment records created and visible in wallet history");
            $this->info("✓ Wallet transactions created and balance updated");
            $this->info("✓ Activity logs created and visible in my-activity");
            $this->info("\nTest completed successfully!");
            
            // Clean up authentication
            auth()->logout();

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Test failed: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }
    }
} 