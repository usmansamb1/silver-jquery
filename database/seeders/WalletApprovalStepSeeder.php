<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\WalletApprovalStep;
use App\Models\WalletApprovalRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WalletApprovalStepSeeder extends Seeder
{
    public function run()
    {
        DB::beginTransaction();
        try {
            // Get the finance user
            $financeUser = User::where('email', 'finance@test.com')->first();
            
            if (!$financeUser) {
                throw new \Exception('Finance user not found');
            }

            // Log finance user details
            Log::info('Finance user found:', [
                'id' => $financeUser->id,
                'email' => $financeUser->email,
                'roles' => $financeUser->getRoleNames()
            ]);

            // Get the approval request
            $approvalRequest = WalletApprovalRequest::find('9EB51FF8-B62B-409B-A8C0-910DDFFE73A5');
            
            if (!$approvalRequest) {
                throw new \Exception('Approval request not found');
            }

            // Log approval request details
            Log::info('Approval request found:', [
                'id' => $approvalRequest->id,
                'status' => $approvalRequest->status,
                'current_step' => $approvalRequest->current_step
            ]);

            // Delete any existing steps for this request
            WalletApprovalStep::where('request_id', $approvalRequest->id)->delete();

            // Create new approval step
            $step = WalletApprovalStep::create([
                'request_id' => $approvalRequest->id,
                'user_id' => $financeUser->id,
                'role' => 'finance',
                'status' => 'pending',
                'step_order' => 1
            ]);

            // Log created step
            Log::info('Approval step created:', [
                'id' => $step->id,
                'user_id' => $step->user_id,
                'role' => $step->role,
                'status' => $step->status,
                'step_order' => $step->step_order
            ]);

            // Update the approval request current step
            $approvalRequest->update([
                'status' => 'pending',
                'current_step' => 1
            ]);

            // Verify the step was created correctly
            $verifyStep = WalletApprovalStep::where('request_id', $approvalRequest->id)
                ->where('user_id', $financeUser->id)
                ->where('role', 'finance')
                ->where('status', 'pending')
                ->where('step_order', 1)
                ->first();

            if (!$verifyStep) {
                throw new \Exception('Failed to verify approval step creation');
            }

            DB::commit();

            // Final verification
            Log::info('Final state:', [
                'request_status' => $approvalRequest->fresh()->status,
                'current_step' => $approvalRequest->fresh()->current_step,
                'step_exists' => (bool)$verifyStep,
                'finance_user_roles' => $financeUser->getRoleNames()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to seed approval step: ' . $e->getMessage());
            throw $e;
        }
    }
} 