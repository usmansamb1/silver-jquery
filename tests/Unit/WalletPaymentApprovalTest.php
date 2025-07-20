<?php

namespace Tests\Unit;

use App\Events\WalletApprovalCompleted;
use App\Listeners\WalletApprovalApprovedListener;
use App\Models\Payment;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletApprovalRequest;
use App\Models\WalletTransaction;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class WalletPaymentApprovalTest extends TestCase
{
    use WithFaker;
    
    protected $listener;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->listener = new WalletApprovalApprovedListener();
    }
    
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
    
    /**
     * Helper function to run all tests with a cleaner structure
     * 
     * @param string $paymentType
     * @param float $amount
     */
    private function runApprovalTest($paymentType, $amount)
    {
        // Set up mocks
        $this->setupDatabaseMock();
        
        // Create test data
        $userId = $this->faker->uuid;
        $paymentId = $this->faker->uuid;
        
        // Create a mock payment with proper setAttribute handling
        $payment = $this->createMockPayment($userId, $amount, $paymentType, $paymentId);
        
        // Create a mock wallet
        $wallet = $this->createMockWallet($userId);
        
        // Create a mock approval request
        $request = $this->createMockApprovalRequest($userId, $paymentId, $amount, $payment);
        
        // Execute the listener
        $event = new WalletApprovalCompleted($request);
        $this->listener->handle($event);
        
        // Assert wallet was updated
        $this->assertEquals($amount, $wallet->balance);
        $this->assertEquals('approved', $payment->status);
        $this->assertTrue($request->transaction_complete);
        
        // Verify transaction was recorded
        $this->assertCount(1, $wallet->transactions);
        $transaction = $wallet->transactions->first();
        $this->assertEquals($amount, $transaction->amount);
        $this->assertEquals('deposit', $transaction->type);
        $this->assertEquals('completed', $transaction->status);
        
        return compact('wallet', 'payment', 'request', 'transaction');
    }
    
    /**
     * Test bank transfer payment updates wallet balance
     */
    public function test_bank_transfer_payment_updates_wallet_balance(): void
    {
        $this->runApprovalTest('bank_transfer', 1000.00);
    }
    
    /**
     * Test bank LC payment updates wallet balance
     */
    public function test_bank_lc_payment_updates_wallet_balance(): void
    {
        $this->runApprovalTest('bank_lc', 2000.00);
    }
    
    /**
     * Test bank guarantee payment updates wallet balance
     */
    public function test_bank_guarantee_payment_updates_wallet_balance(): void
    {
        $this->runApprovalTest('bank_guarantee', 3000.00);
    }
    
    /**
     * Test that nothing happens if payment is not found
     */
    public function test_nothing_happens_when_payment_not_found(): void
    {
        // Set up mocks
        $this->setupDatabaseMock();
        
        // Create user ID
        $userId = $this->faker->uuid;
        
        // Create a mock wallet
        $wallet = $this->createMockWallet($userId);
        
        // Create request with invalid payment_id
        $request = Mockery::mock(WalletApprovalRequest::class);
        $request->shouldReceive('getAttribute')->with('id')->andReturn('test-request-id');
        $request->shouldReceive('getAttribute')->with('user_id')->andReturn($userId);
        $request->shouldReceive('getAttribute')->with('payment_id')->andReturn('invalid-payment-id');
        $request->shouldReceive('payment')->andReturn(null);
        $request->shouldReceive('setAttribute')->withAnyArgs()->andReturnSelf();
        $request->shouldReceive('save')->andReturn(true);
        
        // Execute the listener
        $event = new WalletApprovalCompleted($request);
        $this->listener->handle($event);
        
        // Assert wallet was not updated
        $this->assertEquals(0.00, $wallet->balance);
        $this->assertCount(0, $wallet->transactions);
    }
    
    /**
     * Set up database transaction mock
     */
    private function setupDatabaseMock()
    {
        // Mock the database transaction
        DB::shouldReceive('beginTransaction')->zeroOrMoreTimes()->andReturn(true);
        DB::shouldReceive('commit')->zeroOrMoreTimes()->andReturn(true);
        DB::shouldReceive('rollBack')->zeroOrMoreTimes()->andReturn(true);
        
        // Mock the Log facade
        Log::shouldReceive('info')->zeroOrMoreTimes()->andReturn(true);
        Log::shouldReceive('error')->zeroOrMoreTimes()->andReturn(true);
    }
    
    /**
     * Create a mock payment with proper attribute handling
     */
    private function createMockPayment($userId, $amount, $paymentType, $paymentId)
    {
        /** @var MockInterface $payment */
        $payment = Mockery::mock(Payment::class);
        
        // Set up common attribute getters
        $payment->shouldReceive('getAttribute')->with('id')->andReturn($paymentId);
        $payment->shouldReceive('getAttribute')->with('user_id')->andReturn($userId);
        $payment->shouldReceive('getAttribute')->with('amount')->andReturn($amount);
        $payment->shouldReceive('getAttribute')->with('payment_type')->andReturn($paymentType);
        $payment->shouldReceive('getAttribute')->with('notes')->andReturn('Test payment');
        $payment->shouldReceive('setAttribute')->withAnyArgs()->andReturnSelf();
        
        // Handle dynamic property access
        $payment->status = 'pending';
        $payment->shouldReceive('__get')->with('status')->andReturn($payment->status);
        $payment->shouldReceive('__set')->with('status', Mockery::any())->andReturnUsing(function($key, $value) use ($payment) {
            $payment->status = $value;
        });
        
        $payment->shouldReceive('getKey')->andReturn($paymentId);
        $payment->shouldReceive('save')->andReturn(true);
        
        return $payment;
    }
    
    /**
     * Create a mock wallet
     */
    private function createMockWallet($userId)
    {
        /** @var MockInterface $wallet */
        $wallet = Mockery::mock(Wallet::class);
        $wallet->shouldReceive('getAttribute')->with('id')->andReturn($this->faker->uuid);
        $wallet->shouldReceive('getAttribute')->with('user_id')->andReturn($userId);
        $wallet->shouldReceive('setAttribute')->withAnyArgs()->andReturnSelf();
        
        // Set initial balance and allow it to be updated
        $wallet->balance = 0.00;
        $wallet->shouldReceive('__get')->with('balance')->andReturn($wallet->balance);
        $wallet->shouldReceive('__set')->with('balance', Mockery::any())->andReturnUsing(function($key, $value) use ($wallet) {
            $wallet->balance = $value;
        });
        
        // Collection for transactions
        $transactions = collect();
        $wallet->transactions = $transactions;
        $wallet->shouldReceive('__get')->with('transactions')->andReturn($transactions);
        
        // Mock the deposit method
        $wallet->shouldReceive('deposit')->andReturnUsing(function($amount, $description, $reference, $metadata) use ($wallet) {
            // Create a mock transaction
            $transaction = Mockery::mock(WalletTransaction::class);
            $transaction->shouldReceive('getAttribute')->with('id')->andReturn($this->faker->uuid);
            $transaction->shouldReceive('getAttribute')->with('amount')->andReturn($amount);
            $transaction->shouldReceive('getAttribute')->with('type')->andReturn('deposit');
            $transaction->shouldReceive('getAttribute')->with('status')->andReturn('completed');
            $transaction->shouldReceive('getAttribute')->with('reference_id')->andReturn($reference->id);
            $transaction->shouldReceive('getAttribute')->with('reference_type')->andReturn(get_class($reference));
            $transaction->shouldReceive('setAttribute')->withAnyArgs()->andReturnSelf();
            
            // Add transaction to collection
            $wallet->transactions->push($transaction);
            
            // Update balance
            $wallet->balance += $amount;
            
            return $transaction;
        });
        
        // Mock firstOrCreate method
        Wallet::shouldReceive('firstOrCreate')->once()->with(
            ['user_id' => $userId],
            ['balance' => 0]
        )->andReturn($wallet);
        
        return $wallet;
    }
    
    /**
     * Create a mock approval request
     */
    private function createMockApprovalRequest($userId, $paymentId, $amount, $payment = null)
    {
        /** @var MockInterface $request */
        $request = Mockery::mock(WalletApprovalRequest::class);
        $request->shouldReceive('getAttribute')->with('id')->andReturn($this->faker->uuid);
        $request->shouldReceive('getAttribute')->with('user_id')->andReturn($userId);
        $request->shouldReceive('getAttribute')->with('payment_id')->andReturn($paymentId);
        $request->shouldReceive('getAttribute')->with('amount')->andReturn($amount);
        $request->shouldReceive('setAttribute')->withAnyArgs()->andReturnSelf();
        
        // Set up the payment relationship
        if ($payment) {
            $request->shouldReceive('payment')->andReturn($payment);
        }
        
        // Allow setting transaction_complete
        $request->transaction_complete = false;
        $request->shouldReceive('__get')->with('transaction_complete')->andReturn($request->transaction_complete);
        $request->shouldReceive('__set')->with('transaction_complete', Mockery::any())->andReturnUsing(function($key, $value) use ($request) {
            $request->transaction_complete = $value;
        });
        $request->shouldReceive('save')->andReturn(true);
        
        return $request;
    }
}
