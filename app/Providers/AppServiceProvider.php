<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Observers\UserObserver;
use App\Services\StatusTransitionService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register the StatusTransitionService as a binding rather than a singleton
        // This way we can pass the model when we need to instantiate it
        $this->app->bind(StatusTransitionService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register User model observer
        User::observe(UserObserver::class);
        
        // Share authenticated user with menu partial
        View::composer('partials.menu', function ($view) {
            $view->with('user', Auth::user());
        });
    }
}
