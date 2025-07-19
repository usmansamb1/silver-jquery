<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletApprovalRequest;
use App\Models\WalletApprovalStep;
use App\Models\ApprovalStatus;
use App\Models\StepStatus;
use App\Models\StatusHistory;
use App\Services\StatusTransitionService;

class StatusManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a status change is recorded in history.
     *
     * @return void
     */
    public function test_status_change_is_recorded()
    {
        // Create a user for the request
        $user = User::factory()->create();
        
        // Create an approval request
        $request = WalletApprovalRequest::create([
            'user_id' => $user->id,
            'status' => ApprovalStatus::PENDING,
            'amount' => 100,
            'currency' => 'SAR',
            'description' => 'Test request'
        ]);
        
        // Change the status
        $oldStatus = $request->status;
        $request->changeStatus(
            ApprovalStatus::APPROVED, 
            'Test approval',
            ['completed_at' => now()]
        );
        
        // Verify the status was changed
        $this->assertEquals(ApprovalStatus::APPROVED, $request->fresh()->status);
        
        // Verify a history record was created
        $history = StatusHistory::where('model_id', $request->id)->first();
        $this->assertNotNull($history);
        $this->assertEquals($oldStatus, $history->status_from);
        $this->assertEquals(ApprovalStatus::APPROVED, $history->status_to);
        $this->assertEquals('Test approval', $history->notes);
    }

    /**
     * Test that the transition service enforces allowed transitions.
     *
     * @return void
     */
    public function test_transition_service_enforces_allowed_transitions()
    {
        // Create a user for the request
        $user = User::factory()->create();
        
        // Create an approval request
        $request = WalletApprovalRequest::create([
            'user_id' => $user->id,
            'status' => ApprovalStatus::REJECTED, // Already rejected
            'amount' => 100,
            'currency' => 'SAR',
            'description' => 'Test request'
        ]);
        
        // Try to change from rejected to approved (not allowed)
        $transitionService = new StatusTransitionService($request);
        $allowed = $transitionService->canTransitionTo(ApprovalStatus::APPROVED);
        
        // Verify transition is not allowed
        $this->assertFalse($allowed);
        
        // Create a new request with pending status
        $request2 = WalletApprovalRequest::create([
            'user_id' => $user->id,
            'status' => ApprovalStatus::PENDING,
            'amount' => 200,
            'currency' => 'SAR',
            'description' => 'Test request 2'
        ]);
        
        // Verify transition from pending to approved is allowed
        $transitionService2 = new StatusTransitionService($request2);
        $allowed2 = $transitionService2->canTransitionTo(ApprovalStatus::APPROVED);
        
        $this->assertTrue($allowed2);
    }

    /**
     * Test that the status badge component works correctly.
     *
     * @return void
     */
    public function test_status_badge_component()
    {
        // Create a component instance (through Blade in a real app)
        $component = new \App\View\Components\StatusBadge(ApprovalStatus::APPROVED);
        
        // Test the component methods
        $this->assertEquals(ApprovalStatus::APPROVED, $component->getStatusCode());
        $this->assertEquals('Approved', $component->getStatusName());
        $this->assertStringContainsString('bg-success', $component->getBadgeClass());
        
        // Test with a different status
        $component2 = new \App\View\Components\StatusBadge(ApprovalStatus::REJECTED);
        $this->assertEquals(ApprovalStatus::REJECTED, $component2->getStatusCode());
        $this->assertEquals('Rejected', $component2->getStatusName());
        $this->assertStringContainsString('bg-danger', $component2->getBadgeClass());
    }
} 