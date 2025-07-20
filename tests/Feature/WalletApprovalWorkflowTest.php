<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Payment;
use App\Models\Wallet;
use App\Models\WalletApprovalRequest;
use App\Models\WalletApprovalWorkflow;
use App\Models\WalletApprovalStep;
use App\Models\ApprovalStatus;
use App\Models\StepStatus;
use App\Events\WalletApprovalCompleted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class WalletApprovalWorkflowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $customer;
    protected $financeApprover;
    protected $validationApprover;
    protected $activationApprover;
    protected $workflow;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'customer']);
        Role::create(['name' => 'finance_approver']);
        Role::create(['name' => 'validation_approver']);
        Role::create(['name' => 'activation_approver']);
        Role::create(['name' => 'admin']);

        // Create users
        $this->customer = User::factory()->create();
        $this->customer->assignRole('customer');

        $this->financeApprover = User::factory()->create();
        $this->financeApprover->assignRole('finance_approver');

        $this->validationApprover = User::factory()->create();
        $this->validationApprover->assignRole('validation_approver');

        $this->activationApprover = User::factory()->create();
        $this->activationApprover->assignRole('activation_approver');

        // Create approval statuses if they don't exist
        $this->createApprovalStatuses();

        // Create a workflow
        $this->workflow = $this->createWorkflow();
    }

    /**
     * Create approval statuses for testing.
     */
    private function createApprovalStatuses()
    {
        if (!ApprovalStatus::where('code', ApprovalStatus::PENDING)->exists()) {
            ApprovalStatus::create([
                'name' => 'Pending',
                'code' => ApprovalStatus::PENDING,
                'color' => '#FFA500'
            ]);
        }

        if (!ApprovalStatus::where('code', ApprovalStatus::APPROVED)->exists()) {
            ApprovalStatus::create([
                'name' => 'Approved',
                'code' => ApprovalStatus::APPROVED,
                'color' => '#00FF00'
            ]);
        }

        if (!ApprovalStatus::where('code', ApprovalStatus::REJECTED)->exists()) {
            ApprovalStatus::create([
                'name' => 'Rejected',
                'code' => ApprovalStatus::REJECTED,
                'color' => '#FF0000'
            ]);
        }
    }

    /**
     * Create a testing workflow.
     */
    private function createWorkflow()
    {
        return WalletApprovalWorkflow::create([
            'name' => 'Test Approval Workflow',
            'description' => 'A test workflow for wallet approval',
            'created_by' => $this->financeApprover->id,
            'is_active' => true,
            'notify_by_email' => true,
            'notify_by_sms' => false,
        ]);
    }

    /**
     * Test the complete approval workflow for bank transfer payments.
     */
    public function test_bank_transfer_payment_reflects_in_wallet_after_all_approvals()
    {
        // Create a payment
        $amount = 1000.00;
        $payment = Payment::create([
            'user_id' => $this->customer->id,
            'amount' => $amount,
            'payment_type' => 'bank_transfer',
            'status' => 'pending',
            'notes' => 'Test bank transfer payment'
        ]);

        // Get pending status ID
        $pendingStatus = ApprovalStatus::where('code', ApprovalStatus::PENDING)->first();

        // Create an approval request
        $approvalRequest = WalletApprovalRequest::create([
            'workflow_id' => $this->workflow->id,
            'user_id' => $this->customer->id,
            'status_id' => $pendingStatus->id,
            'status' => ApprovalStatus::PENDING,
            'payment_id' => $payment->id,
            'amount' => $amount,
            'currency' => 'SAR',
            'description' => 'Test bank transfer approval',
            'current_step' => 1
        ]);

        // Create the approval steps in the correct order
        $financeStep = WalletApprovalStep::create([
            'request_id' => $approvalRequest->id,
            'user_id' => $this->financeApprover->id,
            'role' => 'finance_approver',
            'step_order' => 1,
            'status' => StepStatus::PENDING
        ]);

        $validationStep = WalletApprovalStep::create([
            'request_id' => $approvalRequest->id,
            'user_id' => $this->validationApprover->id,
            'role' => 'validation_approver',
            'step_order' => 2,
            'status' => StepStatus::PENDING
        ]);

        $activationStep = WalletApprovalStep::create([
            'request_id' => $approvalRequest->id,
            'user_id' => $this->activationApprover->id,
            'role' => 'activation_approver',
            'step_order' => 3,
            'status' => StepStatus::PENDING
        ]);

        // Create a wallet for the customer
        $wallet = Wallet::create([
            'user_id' => $this->customer->id,
            'balance' => 0.00
        ]);

        // Verify initial state
        $this->assertEquals(0.00, $wallet->balance);
        $this->assertEquals('pending', $payment->status);
        $this->assertEquals(ApprovalStatus::PENDING, $approvalRequest->status);

        // Act as finance approver and approve the first step
        $this->actingAs($this->financeApprover);
        $financeStep->setStatusByCode(StepStatus::APPROVED);
        $financeStep->processed_at = now();
        $financeStep->save();
        
        // Move to next step
        $approvalRequest->current_step = 2;
        $approvalRequest->save();

        // Act as validation approver and approve the second step
        $this->actingAs($this->validationApprover);
        $validationStep->setStatusByCode(StepStatus::APPROVED);
        $validationStep->processed_at = now();
        $validationStep->save();
        
        // Move to next step
        $approvalRequest->current_step = 3;
        $approvalRequest->save();

        // Act as activation approver and approve the final step
        $this->actingAs($this->activationApprover);
        $activationStep->setStatusByCode(StepStatus::APPROVED);
        $activationStep->processed_at = now();
        $activationStep->save();

        // Update request to approved status
        $approvedStatus = ApprovalStatus::where('code', ApprovalStatus::APPROVED)->first();
        $approvalRequest->status_id = $approvedStatus->id;
        $approvalRequest->status = ApprovalStatus::APPROVED;
        $approvalRequest->current_step = null;
        $approvalRequest->completed_at = now();
        $approvalRequest->save();

        // Trigger the approval completed event
        event(new WalletApprovalCompleted($approvalRequest));

        // Refresh models from database
        $wallet->refresh();
        $payment->refresh();
        $approvalRequest->refresh();

        // Assert that wallet was updated
        $this->assertEquals($amount, $wallet->balance);
        $this->assertEquals('approved', $payment->status);
        $this->assertEquals(ApprovalStatus::APPROVED, $approvalRequest->status);
        $this->assertTrue($approvalRequest->transaction_complete);

        // Verify a transaction record was created
        $transactions = $wallet->transactions;
        $this->assertCount(1, $transactions);
        $this->assertEquals($amount, $transactions->first()->amount);
        $this->assertEquals('deposit', $transactions->first()->type);
        $this->assertEquals('completed', $transactions->first()->status);
    }

    /**
     * Test the complete approval workflow for bank LC payments.
     */
    public function test_bank_lc_payment_reflects_in_wallet_after_all_approvals()
    {
        $amount = 2000.00;
        $payment = Payment::create([
            'user_id' => $this->customer->id,
            'amount' => $amount,
            'payment_type' => 'bank_lc',
            'status' => 'pending',
            'notes' => 'Test bank LC payment'
        ]);

        // Create similar approval request flow
        $pendingStatus = ApprovalStatus::where('code', ApprovalStatus::PENDING)->first();
        $approvalRequest = WalletApprovalRequest::create([
            'workflow_id' => $this->workflow->id,
            'user_id' => $this->customer->id,
            'status_id' => $pendingStatus->id,
            'status' => ApprovalStatus::PENDING,
            'payment_id' => $payment->id,
            'amount' => $amount,
            'currency' => 'SAR',
            'description' => 'Test bank LC approval',
            'current_step' => 1
        ]);

        // Add the approval steps, approve each one
        $this->createAndApproveSteps($approvalRequest);

        // Create wallet
        $wallet = Wallet::create([
            'user_id' => $this->customer->id,
            'balance' => 0.00
        ]);

        // Trigger the event
        event(new WalletApprovalCompleted($approvalRequest));

        // Refresh data
        $wallet->refresh();
        $payment->refresh();
        $approvalRequest->refresh();

        // Assert that everything was updated correctly
        $this->assertEquals($amount, $wallet->balance);
        $this->assertEquals('approved', $payment->status);
        $this->assertEquals(ApprovalStatus::APPROVED, $approvalRequest->status);
        $this->assertTrue($approvalRequest->transaction_complete);
    }

    /**
     * Test the complete approval workflow for bank guarantee payments.
     */
    public function test_bank_guarantee_payment_reflects_in_wallet_after_all_approvals()
    {
        $amount = 3000.00;
        $payment = Payment::create([
            'user_id' => $this->customer->id,
            'amount' => $amount,
            'payment_type' => 'bank_guarantee',
            'status' => 'pending',
            'notes' => 'Test bank guarantee payment'
        ]);

        // Create similar approval request flow
        $pendingStatus = ApprovalStatus::where('code', ApprovalStatus::PENDING)->first();
        $approvalRequest = WalletApprovalRequest::create([
            'workflow_id' => $this->workflow->id,
            'user_id' => $this->customer->id,
            'status_id' => $pendingStatus->id,
            'status' => ApprovalStatus::PENDING,
            'payment_id' => $payment->id,
            'amount' => $amount,
            'currency' => 'SAR',
            'description' => 'Test bank guarantee approval',
            'current_step' => 1
        ]);

        // Add the approval steps, approve each one
        $this->createAndApproveSteps($approvalRequest);

        // Create wallet
        $wallet = Wallet::create([
            'user_id' => $this->customer->id,
            'balance' => 0.00
        ]);

        // Trigger the event
        event(new WalletApprovalCompleted($approvalRequest));

        // Refresh data
        $wallet->refresh();
        $payment->refresh();
        $approvalRequest->refresh();

        // Assert that everything was updated correctly
        $this->assertEquals($amount, $wallet->balance);
        $this->assertEquals('approved', $payment->status);
        $this->assertEquals(ApprovalStatus::APPROVED, $approvalRequest->status);
        $this->assertTrue($approvalRequest->transaction_complete);

        // Also verify transaction details were recorded correctly
        $transactions = $wallet->transactions;
        $this->assertCount(1, $transactions);
        $transaction = $transactions->first();
        
        $this->assertEquals($amount, $transaction->amount);
        $this->assertEquals('deposit', $transaction->type);
        $this->assertEquals('completed', $transaction->status);
        $this->assertEquals($payment->id, $transaction->reference_id);
        $this->assertEquals(Payment::class, $transaction->reference_type);
    }

    /**
     * Helper function to create and approve all steps for a request
     */
    private function createAndApproveSteps(WalletApprovalRequest $request)
    {
        // Create steps
        $steps = [
            [
                'user' => $this->financeApprover,
                'role' => 'finance_approver',
                'order' => 1
            ],
            [
                'user' => $this->validationApprover,
                'role' => 'validation_approver',
                'order' => 2
            ],
            [
                'user' => $this->activationApprover,
                'role' => 'activation_approver',
                'order' => 3
            ]
        ];

        $approvalSteps = [];
        foreach ($steps as $step) {
            $approvalSteps[] = WalletApprovalStep::create([
                'request_id' => $request->id,
                'user_id' => $step['user']->id,
                'role' => $step['role'],
                'step_order' => $step['order'],
                'status' => StepStatus::PENDING
            ]);
        }

        // Approve each step
        foreach ($approvalSteps as $index => $step) {
            $this->actingAs($steps[$index]['user']);
            $step->setStatusByCode(StepStatus::APPROVED);
            $step->processed_at = now();
            $step->save();
            
            if ($index < count($approvalSteps) - 1) {
                // Update current step if not the last one
                $request->current_step = $steps[$index + 1]['order'];
                $request->save();
            }
        }

        // Mark request as approved
        $approvedStatus = ApprovalStatus::where('code', ApprovalStatus::APPROVED)->first();
        $request->status_id = $approvedStatus->id;
        $request->status = ApprovalStatus::APPROVED;
        $request->current_step = null;
        $request->completed_at = now();
        $request->save();

        return $approvalSteps;
    }
}
