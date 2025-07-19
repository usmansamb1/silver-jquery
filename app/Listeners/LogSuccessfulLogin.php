<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;
        
        try {
            // Update last login timestamp directly in database
            DB::table('users')
                ->where('id', $user->id)
                ->update(['last_login_at' => now()]);
                
            Log::info('Updated last_login_at on successful login', [
                'user_id' => $user->id,
                'timestamp' => now()->format('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update last_login_at on login', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
