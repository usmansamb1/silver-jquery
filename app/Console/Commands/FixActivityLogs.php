<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixActivityLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-activity-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix ServiceBooking IDs in activity logs for UUID compatibility';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to fix ServiceBooking IDs in activity logs...');
        
        // First, delete problematic logs that could cause errors
        // Convert SQL Server ISNUMERIC to MySQL equivalent
        $deleted = DB::table('activity_logs')
            ->where('subject_type', 'App\\Models\\ServiceBooking')
            ->whereRaw("subject_id REGEXP '^[0-9]+$'")
            ->delete();
            
        $this->info("Removed {$deleted} problematic activity logs.");
        
        // For future logging, we rely on the enhanced LogHelper that now properly formats IDs
        
        $this->info('Completed fixing activity logs!');
        
        return Command::SUCCESS;
    }
} 