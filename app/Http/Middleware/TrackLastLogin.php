<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class TrackLastLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Only update last_login_at if this is a new session or the user just logged in
            $sessionKey = 'last_login_tracked_' . $user->id;
            
            // Only update timestamp if this session hasn't tracked login yet
            if (!Session::has($sessionKey)) {
                try {
                    // Check if the last_login_at column exists before trying to update
                    if (Schema::hasColumn('users', 'last_login_at')) {
                        // Update last login directly in DB to avoid model save issues
                        DB::table('users')
                            ->where('id', $user->id)
                            ->update(['last_login_at' => now()]);
                        
                        Log::debug('Updated last_login_at timestamp for user session', [
                            'user_id' => $user->id,
                            'timestamp' => now()->format('Y-m-d H:i:s')
                        ]);
                    } else {
                        Log::warning('last_login_at column does not exist in users table', [
                            'user_id' => $user->id
                        ]);
                    }
                    
                    // Mark this session as having tracked login regardless
                    Session::put($sessionKey, true);
                    
                } catch (\Exception $e) {
                    Log::error('Failed to update last_login_at', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        return $next($request);
    }
}
