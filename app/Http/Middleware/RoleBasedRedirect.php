<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleBasedRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only proceed if user is authenticated
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $currentPath = $request->path();

        // Rule 1: Non-customer users accessing /home should be redirected to appropriate dashboard
        if ($currentPath === 'home') {
            if (!$user->hasRole('customer')) {
                // Rule 2: Delivery users go to delivery dashboard
                if ($user->hasRole('delivery')) {
                    return redirect()->route('admin.delivery.dashboard');
                }
                
                // Rule 1: All other non-customer users go to admin dashboard
                return redirect()->route('admin.dashboard');
            }
        }

        // Rule 3: Customer users accessing admin dashboards should be redirected to /home
        if (in_array($currentPath, ['admin/dashboard', 'admin/deliverydashboard'])) {
            if ($user->hasRole('customer')) {
                return redirect()->route('home');
            }
        }

        return $next($request);
    }
} 