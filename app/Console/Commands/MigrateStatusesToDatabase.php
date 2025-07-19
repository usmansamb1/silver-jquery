<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\ApprovalStatus;
use App\Models\StepStatus;
use App\Models\WalletApprovalRequest;
use App\Models\WalletApprovalStep;

class MigrateStatusesToDatabase extends Command
{
    protected $signature = 'statuses:migrate';
    protected $description = 'Migrate hardcoded status strings to database-driven approach';

    public function handle()
    {
        $this->info('Starting migration of hardcoded statuses to database...');
        
        // Ensure statuses exist in the database first
        $this->call('db:seed', ['--class' => 'Database\Seeders\StatusSeeder']);
        
        // 1. Migrate WalletApprovalRequest statuses
        $this->migrateApprovalRequestStatuses();
        
        // 2. Migrate WalletApprovalStep statuses
        $this->migrateApprovalStepStatuses();
        
        $this->info('Status migration completed successfully!');
        
        return Command::SUCCESS;
    }
    
    protected function migrateApprovalRequestStatuses()
    {
        $this->info('Migrating approval request statuses...');
        
        // Get status mapping
        $statusMap = $this->getStatusMapping(ApprovalStatus::class);
        
        // Count pending updates
        $count = WalletApprovalRequest::whereNull('status_id')
            ->whereNotNull('status')
            ->count();
            
        $this->info("Found {$count} approval requests to update");
        
        if ($count === 0) {
            $this->info('No approval requests need updating');
            return;
        }
        
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        
        $updated = 0;
        $skipped = 0;
        
        // Process in chunks to avoid memory issues
        WalletApprovalRequest::whereNull('status_id')
            ->whereNotNull('status')
            ->chunk(100, function ($requests) use ($statusMap, $bar, &$updated, &$skipped) {
                foreach ($requests as $request) {
                    $status = $request->status;
                    
                    if (isset($statusMap[$status])) {
                        $request->status_id = $statusMap[$status];
                        $request->save();
                        $updated++;
                    } else {
                        $skipped++;
                    }
                    
                    $bar->advance();
                }
            });
        
        $bar->finish();
        $this->newLine();
        
        $this->info("Updated {$updated} approval requests, skipped {$skipped}");
    }
    
    protected function migrateApprovalStepStatuses()
    {
        $this->info('Migrating approval step statuses...');
        
        // Get status mapping
        $statusMap = $this->getStatusMapping(StepStatus::class);
        
        // Count pending updates
        $count = WalletApprovalStep::whereNull('status_id')
            ->whereNotNull('status')
            ->count();
            
        $this->info("Found {$count} approval steps to update");
        
        if ($count === 0) {
            $this->info('No approval steps need updating');
            return;
        }
        
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        
        $updated = 0;
        $skipped = 0;
        
        // Process in chunks to avoid memory issues
        WalletApprovalStep::whereNull('status_id')
            ->whereNotNull('status')
            ->chunk(100, function ($steps) use ($statusMap, $bar, &$updated, &$skipped) {
                foreach ($steps as $step) {
                    $status = $step->status;
                    
                    if (isset($statusMap[$status])) {
                        $step->status_id = $statusMap[$status];
                        $step->save();
                        $updated++;
                    } else {
                        $skipped++;
                    }
                    
                    $bar->advance();
                }
            });
        
        $bar->finish();
        $this->newLine();
        
        $this->info("Updated {$updated} approval steps, skipped {$skipped}");
    }
    
    /**
     * Get a mapping of status codes to IDs
     * 
     * @param string $statusClass
     * @return array
     */
    protected function getStatusMapping(string $statusClass): array
    {
        $tableName = (new $statusClass)->getTable();
        $statuses = DB::table($tableName)->pluck('id', 'code')->toArray();
        
        return $statuses;
    }
} 