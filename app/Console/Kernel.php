<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Monitor notification queues every 5 minutes
        $schedule->command('notifications:monitor')
            ->everyFiveMinutes()
            ->appendOutputTo(storage_path('logs/notifications-monitor.log'));

        // Clean up soft-deleted models weekly
        $schedule->command('model:prune')
            ->weekly()
            ->appendOutputTo(storage_path('logs/model-prune.log'));

        // Prune failed jobs older than 7 days weekly
        $schedule->command('queue:prune-failed --hours=168')
            ->weekly()
            ->appendOutputTo(storage_path('logs/prune-failed-jobs.log'));

        // Restart queue workers daily to prevent memory issues
        $schedule->command('queue:restart')
            ->daily()
            ->appendOutputTo(storage_path('logs/queue-restart.log'));

        // Clean old wallet approval requests monthly (90 days old)
        $schedule->command('wallet:clean-approvals --days=90')
            ->monthly()
            ->appendOutputTo(storage_path('logs/clean-wallet-approvals.log'));
            
        // Check for pending approvals that should be approved
        $schedule->command('wallet:check-approvals --fix')
            ->hourly()
            ->appendOutputTo(storage_path('logs/wallet-check-approvals.log'));
            
        // Complete any incomplete wallet transactions
        $schedule->command('wallet:complete-transactions')
            ->hourly()
            ->appendOutputTo(storage_path('logs/wallet-complete-transactions.log'));

        // Fix any missing reference numbers in orders and service bookings
        $schedule->command('app:fix-missing-order-data')
            ->hourly()
            ->appendOutputTo(storage_path('logs/fix-missing-order-data.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\SyncKmlLocations::class,
        Commands\SyncTestKml::class,
        Commands\FixMapLocationEncoding::class,
    ];
}
