<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Payment;
use App\Models\WalletApprovalRequest;
use App\Models\WalletApprovalStep;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class WalletTransactionTest extends TestCase
{
    use DatabaseTransactions;

    protected $customer;
    protected $financeApprover;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::firstOrCreate(['name' => 'customer']);
        Role::firstOrCreate(['name' => 'finance']);

        // Create users
        $this->customer = User::factory()->create();
        $this->customer->assignRole('customer');

        $this->financeApprover = User::factory()->create(['email' => 'finance@test.com']);
        $this->financeApprover->assignRole('finance');
    }

    /** @test */
    public function transaction_commits_successfully_on_valid_request()
    {
        Storage::fake('public');
        $this->actingAs($this->customer);

        $response = $this->postJson('/wallet/bank-payment', [
            'amount' => 1000,
            'payment_method' => 'bank_transfer',
            'payment_notes' => 'Test transaction commit',
            'payment_files' => [
                UploadedFile::fake()->create('test.pdf', 500)
            ]
        ]);

        $response->assertStatus(200);

        // Verify all records were created
        $payment = Payment::latest()->first();
        $this->assertNotNull($payment);
        
        $approvalRequest = WalletApprovalRequest::where('payment_id', $payment->id)->first();
        $this->assertNotNull($approvalRequest);
        
        $approvalStep = WalletApprovalStep::where('request_id', $approvalRequest->id)->first();
        $this->assertNotNull($approvalStep);

        // Verify file was stored
        $files = json_decode($payment->files);
        Storage::disk('public')->assertExists($files[0]);
    }

    /** @test */
    public function transaction_rolls_back_on_invalid_data()
    {
        $this->actingAs($this->customer);
        
        // Force a rollback by providing invalid data
        $initialPaymentCount = Payment::count();
        $initialRequestCount = WalletApprovalRequest::count();
        $initialStepCount = WalletApprovalStep::count();

        $response = $this->postJson('/wallet/bank-payment', [
            'amount' => -1000, // Invalid amount
            'payment_method' => 'bank_transfer'
        ]);

        $response->assertStatus(422);

        // Verify no records were created
        $this->assertEquals($initialPaymentCount, Payment::count());
        $this->assertEquals($initialRequestCount, WalletApprovalRequest::count());
        $this->assertEquals($initialStepCount, WalletApprovalStep::count());
    }

    /** @test */
    public function transaction_rolls_back_on_exception()
    {
        $this->actingAs($this->customer);
        
        // Store initial counts
        $initialPaymentCount = Payment::count();
        $initialRequestCount = WalletApprovalRequest::count();
        $initialStepCount = WalletApprovalStep::count();

        // Mock DB::commit to throw exception
        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once()->andThrow(new \Exception('Forced exception'));
        DB::shouldReceive('rollBack')->once();

        $response = $this->postJson('/wallet/bank-payment', [
            'amount' => 1000,
            'payment_method' => 'bank_transfer',
            'payment_notes' => 'Test rollback'
        ]);

        $response->assertStatus(500);

        // Verify no records were created
        $this->assertEquals($initialPaymentCount, Payment::count());
        $this->assertEquals($initialRequestCount, WalletApprovalRequest::count());
        $this->assertEquals($initialStepCount, WalletApprovalStep::count());
    }

    /** @test */
    public function transaction_maintains_data_consistency()
    {
        $this->actingAs($this->customer);

        $response = $this->postJson('/wallet/bank-payment', [
            'amount' => 1000,
            'payment_method' => 'bank_transfer',
            'payment_notes' => 'Test consistency'
        ]);

        $response->assertStatus(200);
        
        // Get the created records
        $payment = Payment::latest()->first();
        $approvalRequest = WalletApprovalRequest::where('payment_id', $payment->id)->first();
        $approvalStep = WalletApprovalStep::where('request_id', $approvalRequest->id)->first();

        // Verify data consistency
        $this->assertEquals($this->customer->id, $payment->user_id);
        $this->assertEquals($payment->id, $approvalRequest->payment_id);
        $this->assertEquals($approvalRequest->id, $approvalStep->request_id);
        $this->assertEquals('finance', $approvalStep->role);
        $this->assertEquals('pending', $approvalStep->status);
    }
} 