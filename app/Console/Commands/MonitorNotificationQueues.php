<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonitorNotificationQueues extends Command
{
    protected $signature = 'notifications:monitor';
    protected $description = 'Monitor notification queues and report status';

    public function handle()
    {
        $this->info('Monitoring notification queues...');

        // Check pending jobs
        $pendingJobs = DB::table('jobs')
            ->select('queue', DB::raw('count(*) as total'))
            ->groupBy('queue')
            ->get();

        // Check failed jobs
        $failedJobs = DB::table('failed_jobs')
            ->select('queue', DB::raw('count(*) as total'))
            ->whereDate('failed_at', '>=', now()->subDay())
            ->groupBy('queue')
            ->get();

        // Display status
        $this->table(
            ['Queue', 'Pending Jobs'],
            $pendingJobs->map(function($queue) {
                return [$queue->queue, $queue->total];
            })
        );

        $this->table(
            ['Queue', 'Failed Jobs (24h)'],
            $failedJobs->map(function($queue) {
                return [$queue->queue, $queue->total];
            })
        );

        // Alert if too many pending jobs
        foreach ($pendingJobs as $queue) {
            if ($queue->total > 100) {
                Log::warning("High number of pending jobs in queue: {$queue->queue}", [
                    'queue' => $queue->queue,
                    'pending_jobs' => $queue->total
                ]);
            }
        }

        // Alert if failed jobs
        foreach ($failedJobs as $queue) {
            if ($queue->total > 0) {
                Log::error("Failed jobs detected in queue: {$queue->queue}", [
                    'queue' => $queue->queue,
                    'failed_jobs' => $queue->total
                ]);
            }
        }

        return Command::SUCCESS;
    }
} 