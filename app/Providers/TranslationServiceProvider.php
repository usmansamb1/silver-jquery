<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use App\Translation\ModularFileLoader;

class TranslationServiceProvider extends \Illuminate\Translation\TranslationServiceProvider
{

    /**
     * Register services.
     */
    public function register(): void
    {
        // Call parent registration first
        parent::register();
        
        // Then override with our custom file loader
        $this->app->singleton('translation.loader', function ($app) {
            return new ModularFileLoader($app['files'], base_path('lang'));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Nothing needed here as the loader handles everything
    }
}