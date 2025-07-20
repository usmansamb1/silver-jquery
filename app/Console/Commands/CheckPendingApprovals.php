<?php

namespace App\Console\Commands;

use App\Models\ApprovalStatus;
use App\Models\WalletApprovalRequest;
use App\Models\WalletApprovalStep;
use App\Models\StepStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckPendingApprovals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallet:check-approvals {--fix : Fix any status inconsistencies}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for wallet approval requests that should be approved but are still showing as pending';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for pending wallet approval requests...');
        
        // Find wallet approval requests that are pending but have all steps approved
        $pendingRequests = WalletApprovalRequest::where('status', ApprovalStatus::PENDING)
            ->orWhere('status', 'pending')
            ->get();
            
        $this->info('Found ' . $pendingRequests->count() . ' pending requests');
        
        $needsFix = 0;
        $fixed = 0;
        
        foreach ($pendingRequests as $request) {
            // Check if all steps are approved
            $steps = $request->steps;
            if ($steps->isEmpty()) {
                $this->warn('Request ' . $request->id . ' has no approval steps. Skipping.');
                continue;
            }
            
            $allApproved = $steps->every(function ($step) {
                return $step->status === StepStatus::APPROVED;
            });
            
            if ($allApproved) {
                $this->warn('Request ' . $request->id . ' has all steps approved but is still pending!');
                $needsFix++;
                
                if ($this->option('fix')) {
                    try {
                        DB::beginTransaction();
                        
                        // Get approved status
                        $approvedStatus = ApprovalStatus::where('code', ApprovalStatus::APPROVED)->first();
                        if (!$approvedStatus) {
                            throw new \Exception('Approved status not found in database');
                        }
                        
                        // Update request status
                        $updateData = [
                            'status' => ApprovalStatus::APPROVED,
                            'current_step' => null,
                            'completed_at' => now()
                        ];
                        
                        // Add status_id if it exists in the table
                        if (DB::getSchemaBuilder()->hasColumn('v2_wallet_approval_requests', 'status_id') && $approvedStatus) {
                            $updateData['status_id'] = $approvedStatus->id;
                        }
                        
                        $request->update($updateData);
                        
                        // Make sure the request has a status history entry
                        try {
                            $request->recordStatusChange('pending', ApprovalStatus::APPROVED, 'Fixed by system check');
                        } catch (\Exception $e) {
                            $this->warn('Failed to record status change: ' . $e->getMessage());
                        }
                        
                        // Call the handleApproval method to process wallet transaction
                        if (method_exists($request, 'handleApproval')) {
                            $result = $request->handleApproval();
                            if ($result) {
                                $this->info('Successfully processed wallet transaction for request ' . $request->id);
                            } else {
                                $this->error('Failed to process wallet transaction for request ' . $request->id);
                            }
                        }
                        
                        DB::commit();
                        $fixed++;
                        
                        $this->info('Fixed request ' . $request->id);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $this->error('Failed to fix request ' . $request->id . ': ' . $e->getMessage());
                        Log::error('Failed to fix wallet approval request', [
                            'request_id' => $request->id,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                    }
                }
            }
        }
        
        if ($needsFix > 0 && !$this->option('fix')) {
            $this->warn($needsFix . ' requests need fixing. Run with --fix to apply fixes.');
        } else if ($needsFix > 0) {
            $this->info('Fixed ' . $fixed . ' of ' . $needsFix . ' requests that needed fixing.');
        } else {
            $this->info('All pending requests are correctly pending (waiting for approvals).');
        }
        
        return 0;
    }
}
