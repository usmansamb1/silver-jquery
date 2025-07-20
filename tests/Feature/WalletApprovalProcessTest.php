<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletApprovalRequest;
use App\Models\WalletApprovalStep;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use App\Notifications\WalletApprovalNotification;
use App\Notifications\WalletTopupApprovedNotification;
use App\Notifications\WalletTopupRejectedNotification;

class WalletApprovalProcessTest extends TestCase
{
    use DatabaseTransactions;

    protected $customer;
    protected $financeUser;
    protected $validationUser;
    protected $activationUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test customer user
        $this->customer = User::factory()->create([
            'name' => 'Customer User',
            'email' => 'customer@test.com',
            'is_active' => true,
            'registration_type' => 'company',
            'company_type' => 'private',
            'company_name' => 'Test Company'
        ]);
        
        // Find users with required roles or create them
        $this->financeUser = User::where('email', 'finance@test.com')->first();
        if (!$this->financeUser) {
            $this->financeUser = User::factory()->create([
                'name' => 'Finance User',
                'email' => 'finance@test.com',
                'is_active' => true
            ]);
            // We would assign roles here but it's already done in the migration
        }
        
        $this->validationUser = User::where('email', 'validation@test.com')->first();
        if (!$this->validationUser) {
            $this->validationUser = User::factory()->create([
                'name' => 'Validation User',
                'email' => 'validation@test.com',
                'is_active' => true
            ]);
            // We would assign roles here but it's already done in the migration
        }
        
        $this->activationUser = User::where('email', 'activation@test.com')->first();
        if (!$this->activationUser) {
            $this->activationUser = User::factory()->create([
                'name' => 'Activation User',
                'email' => 'activation@test.com',
                'is_active' => true
            ]);
            // We would assign roles here but it's already done in the migration
        }
        
        // Create wallet for customer
        Wallet::factory()->create([
            'user_id' => $this->customer->id,
            'balance' => 0
        ]);
    }

    /** @test */
    public function customer_can_create_bank_payment_request()
    {
        Notification::fake();
        
        // Acting as the customer
        $this->actingAs($this->customer);
        
        // Submit a bank payment request
        $response = $this->postJson('/wallet/bank-payment', [
            'amount' => 1000,
            'payment_method' => 'bank_transfer',
            'payment_notes' => 'Test bank transfer',
        ]);
        
        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'payment_id']);
        
        // Check database for the payment record
        $this->assertDatabaseHas('payments', [
            'user_id' => $this->customer->id,
            'payment_type' => 'bank_transfer',
            'amount' => 1000,
            'status' => 'pending'
        ]);
        
        // Check for approval request record
        $payment = Payment::where('user_id', $this->customer->id)->latest()->first();
        $this->assertDatabaseHas('v2_wallet_approval_requests', [
            'payment_id' => $payment->id,
            'status' => 'pending',
            'current_step' => 1
        ]);
        
        // Check for first approval step
        $approvalRequest = WalletApprovalRequest::where('payment_id', $payment->id)->first();
        $this->assertDatabaseHas('v2_wallet_approval_steps', [
            'wallet_approval_request_id' => $approvalRequest->id,
            'user_id' => $this->financeUser->id,
            'role' => 'finance',
            'status' => 'pending'
        ]);
        
        // Verify notification was sent to finance approver
        Notification::assertSentTo(
            $this->financeUser,
            WalletApprovalNotification::class
        );
    }
    
    /** @test */
    public function finance_user_can_approve_first_step()
    {
        Notification::fake();
        
        // Create a payment and approval request
        $payment = Payment::factory()->create([
            'user_id' => $this->customer->id,
            'payment_type' => 'bank_transfer',
            'amount' => 1000,
            'status' => 'pending'
        ]);
        
        $approvalRequest = WalletApprovalRequest::create([
            'payment_id' => $payment->id,
            'status' => 'pending',
            'current_step' => 1
        ]);
        
        WalletApprovalStep::create([
            'wallet_approval_request_id' => $approvalRequest->id,
            'user_id' => $this->financeUser->id,
            'role' => 'finance',
            'status' => 'pending'
        ]);
        
        // Act as finance user and approve the request
        $this->actingAs($this->financeUser);
        
        $response = $this->postJson("/wallet/approvals/{$approvalRequest->id}/approve", [
            'comments' => 'Approved by finance'
        ]);
        
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Payment step approved successfully',
                'current_status' => 'finance_approved'
            ]);
        
        // Check database for updated records
        $this->assertDatabaseHas('v2_wallet_approval_requests', [
            'id' => $approvalRequest->id,
            'status' => 'finance_approved',
            'current_step' => 2
        ]);
        
        // Check for validation approval step
        $this->assertDatabaseHas('v2_wallet_approval_steps', [
            'wallet_approval_request_id' => $approvalRequest->id,
            'user_id' => $this->validationUser->id,
            'role' => 'validation',
            'status' => 'pending'
        ]);
        
        // Verify notification was sent to validation approver
        Notification::assertSentTo(
            $this->validationUser,
            WalletApprovalNotification::class
        );
    }
    
    /** @test */
    public function complete_approval_flow_recharges_wallet()
    {
        Notification::fake();
        
        // Create a payment and approval request
        $payment = Payment::factory()->create([
            'user_id' => $this->customer->id,
            'payment_type' => 'bank_transfer',
            'amount' => 1000,
            'status' => 'pending'
        ]);
        
        $approvalRequest = WalletApprovalRequest::create([
            'payment_id' => $payment->id,
            'status' => 'pending',
            'current_step' => 1
        ]);
        
        // Create first approval step (finance)
        WalletApprovalStep::create([
            'wallet_approval_request_id' => $approvalRequest->id,
            'user_id' => $this->financeUser->id,
            'role' => 'finance',
            'status' => 'pending'
        ]);
        
        // Step 1: Finance approval
        $this->actingAs($this->financeUser);
        $response = $this->postJson("/wallet/approvals/{$approvalRequest->id}/approve", [
            'comments' => 'Finance approved'
        ]);
        $response->assertStatus(200);
        
        // Step 2: Validation approval
        $this->actingAs($this->validationUser);
        $response = $this->postJson("/wallet/approvals/{$approvalRequest->id}/approve", [
            'comments' => 'Validation approved'
        ]);
        $response->assertStatus(200);
        
        // Step 3: Activation approval
        $this->actingAs($this->activationUser);
        $response = $this->postJson("/wallet/approvals/{$approvalRequest->id}/approve", [
            'comments' => 'Activation approved'
        ]);
        $response->assertStatus(200);
        
        // Check final status
        $this->assertDatabaseHas('v2_wallet_approval_requests', [
            'id' => $approvalRequest->id,
            'status' => 'completed',
            'current_step' => null
        ]);
        
        // Check payment status
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'approved'
        ]);
        
        // Check wallet balance updated
        $this->assertDatabaseHas('wallets', [
            'user_id' => $this->customer->id,
            'balance' => 1000 // Initial 0 + 1000 from approval
        ]);
        
        // Verify customer notification
        Notification::assertSentTo(
            $this->customer,
            WalletTopupApprovedNotification::class
        );
    }
    
    /** @test */
    public function any_user_can_reject_approval_request_at_their_step()
    {
        Notification::fake();
        
        // Create payment and approval request
        $payment = Payment::factory()->create([
            'user_id' => $this->customer->id,
            'payment_type' => 'bank_transfer',
            'amount' => 1000,
            'status' => 'pending'
        ]);
        
        $approvalRequest = WalletApprovalRequest::create([
            'payment_id' => $payment->id,
            'status' => 'pending',
            'current_step' => 1
        ]);
        
        // Create first approval step
        WalletApprovalStep::create([
            'wallet_approval_request_id' => $approvalRequest->id,
            'user_id' => $this->financeUser->id,
            'role' => 'finance',
            'status' => 'pending'
        ]);
        
        // Act as finance user and reject
        $this->actingAs($this->financeUser);
        $response = $this->postJson("/wallet/approvals/{$approvalRequest->id}/reject", [
            'rejection_reason' => 'Invalid payment details'
        ]);
        
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Payment request rejected',
                'current_status' => 'rejected'
            ]);
        
        // Check database for updated records
        $this->assertDatabaseHas('v2_wallet_approval_requests', [
            'id' => $approvalRequest->id,
            'status' => 'rejected',
            'rejection_reason' => 'Invalid payment details',
            'current_step' => null
        ]);
        
        // Check payment status
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'rejected'
        ]);
        
        // Verify customer notification
        Notification::assertSentTo(
            $this->customer,
            WalletTopupRejectedNotification::class
        );
    }
} 