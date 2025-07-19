<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Payment;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;

class WalletTransactionHistoryTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    protected $customer;
    protected $wallet;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::firstOrCreate(['name' => 'customer']);

        // Create user
        $this->customer = User::factory()->create();
        $this->customer->assignRole('customer');

        // Create wallet
        $this->wallet = Wallet::create([
            'user_id' => $this->customer->id,
            'balance' => 1000.00
        ]);
    }

    /** @test */
    public function user_can_view_wallet_transactions_page()
    {
        $this->actingAs($this->customer);

        $response = $this->get(route('wallet.transactions'));
        $response->assertStatus(200);
        $response->assertViewIs('wallet.transactions');
        $response->assertViewHas('wallet');
        $response->assertViewHas('transactions');
    }

    /** @test */
    public function transactions_are_displayed_correctly()
    {
        // Create some transactions
        WalletTransaction::create([
            'wallet_id' => $this->wallet->id,
            'user_id' => $this->customer->id,
            'amount' => 500.00,
            'type' => 'deposit',
            'status' => 'completed',
            'description' => 'Test deposit',
            'balance_before' => 1000.00,
            'balance_after' => 1500.00
        ]);

        WalletTransaction::create([
            'wallet_id' => $this->wallet->id,
            'user_id' => $this->customer->id,
            'amount' => 200.00,
            'type' => 'withdrawal',
            'status' => 'completed',
            'description' => 'Test withdrawal',
            'balance_before' => 1500.00,
            'balance_after' => 1300.00
        ]);

        $this->actingAs($this->customer);
        
        $response = $this->get(route('wallet.transactions'));
        $response->assertStatus(200);
        
        // Check that transactions are displayed
        $response->assertSee('Test deposit');
        $response->assertSee('Test withdrawal');
        $response->assertSee('+500.00');
        $response->assertSee('-200.00');
    }

    /** @test */
    public function wallet_shows_correct_balance()
    {
        $this->actingAs($this->customer);
        
        $response = $this->get(route('wallet.transactions'));
        $response->assertStatus(200);
        
        // Check that wallet balance is displayed correctly
        $response->assertSee('1,000.00 SAR');
    }

    /** @test */
    public function deposit_transaction_updates_wallet_and_creates_transaction_record()
    {
        $this->actingAs($this->customer);
        
        // Initial balance is 1000
        $initialBalance = $this->wallet->balance;
        
        // Record a deposit of 500
        $transaction = $this->wallet->deposit(500, 'Test deposit via API');
        
        // Refresh the wallet
        $this->wallet->refresh();
        
        // Verify wallet balance increased
        $this->assertEquals($initialBalance + 500, $this->wallet->balance);
        
        // Verify transaction record
        $this->assertEquals($this->wallet->id, $transaction->wallet_id);
        $this->assertEquals($this->customer->id, $transaction->user_id);
        $this->assertEquals(500, $transaction->amount);
        $this->assertEquals('deposit', $transaction->type);
        $this->assertEquals('completed', $transaction->status);
        $this->assertEquals('Test deposit via API', $transaction->description);
        $this->assertEquals($initialBalance, $transaction->balance_before);
        $this->assertEquals($initialBalance + 500, $transaction->balance_after);
    }

    /** @test */
    public function withdrawal_transaction_updates_wallet_and_creates_transaction_record()
    {
        $this->actingAs($this->customer);
        
        // Initial balance is 1000
        $initialBalance = $this->wallet->balance;
        
        // Record a withdrawal of 300
        $transaction = $this->wallet->withdraw(300, 'Test withdrawal via API');
        
        // Refresh the wallet
        $this->wallet->refresh();
        
        // Verify wallet balance decreased
        $this->assertEquals($initialBalance - 300, $this->wallet->balance);
        
        // Verify transaction record
        $this->assertEquals($this->wallet->id, $transaction->wallet_id);
        $this->assertEquals($this->customer->id, $transaction->user_id);
        $this->assertEquals(300, $transaction->amount);
        $this->assertEquals('withdrawal', $transaction->type);
        $this->assertEquals('completed', $transaction->status);
        $this->assertEquals('Test withdrawal via API', $transaction->description);
        $this->assertEquals($initialBalance, $transaction->balance_before);
        $this->assertEquals($initialBalance - 300, $transaction->balance_after);
    }

    /** @test */
    public function withdrawal_fails_when_insufficient_funds()
    {
        $this->actingAs($this->customer);
        
        // Try to withdraw more than the balance
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient funds');
        
        $this->wallet->withdraw(2000, 'This should fail');
    }

    /** @test */
    public function payment_approval_creates_wallet_transaction()
    {
        $this->actingAs($this->customer);
        
        // Create a payment
        $payment = Payment::create([
            'user_id' => $this->customer->id,
            'payment_type' => 'bank_transfer',
            'amount' => 750.00,
            'status' => 'pending',
            'notes' => 'Test bank transfer for approval'
        ]);
        
        // Create approval request
        $approvalRequest = \App\Models\WalletApprovalRequest::create([
            'payment_id' => $payment->id,
            'user_id' => $this->customer->id,
            'amount' => 750.00,
            'status' => 'pending',
            'current_step' => 1,
            'description' => 'Test approval request'
        ]);
        
        // Initial transaction count
        $initialTransactionCount = WalletTransaction::count();
        $initialBalance = $this->wallet->balance;
        
        // Simulate approval (directly call the method instead of going through controller)
        $approvalRequest->approve();
        
        // Refresh the wallet
        $this->wallet->refresh();
        
        // Verify a new transaction was created
        $this->assertEquals($initialTransactionCount + 1, WalletTransaction::count());
        
        // Get the latest transaction
        $transaction = WalletTransaction::latest()->first();
        
        // Verify transaction details
        $this->assertEquals($this->wallet->id, $transaction->wallet_id);
        $this->assertEquals($this->customer->id, $transaction->user_id);
        $this->assertEquals(750.00, $transaction->amount);
        $this->assertEquals('deposit', $transaction->type);
        $this->assertEquals('completed', $transaction->status);
        $this->assertEquals($initialBalance, $transaction->balance_before);
        $this->assertEquals($initialBalance + 750.00, $transaction->balance_after);
        
        // Verify wallet balance was updated
        $this->assertEquals($initialBalance + 750.00, $this->wallet->balance);
    }
} 