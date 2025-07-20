<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\WalletApprovalRequest;
use Carbon\Carbon;

class CleanOldWalletApprovalRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallet:clean-approvals {--days=90 : Number of days to keep records} {--dry-run : Run without deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean old wallet approval requests from database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $dryRun = $this->option('dry-run');
        
        $cutoffDate = Carbon::now()->subDays($days);
        
        $this->info("Cleaning wallet approval requests older than {$days} days ({$cutoffDate->toDateTimeString()})");
        
        if ($dryRun) {
            $this->warn("Running in dry-run mode - no records will be deleted");
        }
        
        $query = WalletApprovalRequest::where('created_at', '<', $cutoffDate)
            ->where(function($q) {
                $q->where('status', 'approved')
                  ->orWhere('status', 'rejected');
            });
        
        $count = $query->count();
        
        $this->info("Found {$count} records to clean");
        
        if ($count > 0 && !$dryRun) {
            DB::beginTransaction();
            try {
                // Delete related records first (steps, actions)
                $requestIds = $query->pluck('id')->toArray();
                
                // Delete related steps
                $stepsDeleted = DB::table('wallet_approval_steps')
                    ->whereIn('request_id', $requestIds)
                    ->delete();
                
                $this->info("Deleted {$stepsDeleted} related approval steps");
                
                // Delete related actions if such table exists
                if (DB::getSchemaBuilder()->hasTable('wallet_approval_actions')) {
                    $actionsDeleted = DB::table('wallet_approval_actions')
                        ->whereIn('request_id', $requestIds)
                        ->delete();
                    $this->info("Deleted {$actionsDeleted} related approval actions");
                }
                
                // Delete the requests
                $deleted = $query->delete();
                
                DB::commit();
                
                $this->info("Successfully deleted {$deleted} wallet approval requests");
                
                Log::info("Cleaned old wallet approval requests", [
                    'deleted_count' => $deleted,
                    'days_threshold' => $days,
                    'cutoff_date' => $cutoffDate->toDateTimeString()
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Error cleaning wallet approval requests: {$e->getMessage()}");
                Log::error("Error cleaning wallet approval requests", [
                    'error' => $e->getMessage(),
                    'days_threshold' => $days,
                    'cutoff_date' => $cutoffDate->toDateTimeString()
                ]);
                
                return Command::FAILURE;
            }
        }
        
        return Command::SUCCESS;
    }
} 