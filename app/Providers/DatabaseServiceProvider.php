<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Only apply these settings for SQL Server connections
        if (config('database.default') === 'sqlsrv') {
            $this->configureSqlServerForArabic();
        }
    }

    /**
     * Configure SQL Server connection for proper Arabic text support
     */
    protected function configureSqlServerForArabic(): void
    {
        try {
            // Update charset
            Config::set('database.connections.sqlsrv.charset', 'utf8');
            
            // Ensure new connections pick up these settings
            DB::purge('sqlsrv');
        } catch (\Exception $e) {
            // Log any errors but don't crash the application
            Log::error('Failed to configure SQL Server for Arabic: ' . $e->getMessage());
        }
    }
} 