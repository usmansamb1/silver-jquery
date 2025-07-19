<?php

namespace App\Providers;

use App\Logging\DatabaseLogger;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class LogServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register custom database log channel
        Log::extend('database', function ($app) {
            return (new DatabaseLogger())($app);
        });
    }
}
