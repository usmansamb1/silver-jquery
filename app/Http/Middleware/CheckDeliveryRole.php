<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckDeliveryRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->hasRole('delivery')) {
            if (auth()->check()) {
                // If user is logged in but doesn't have delivery role
                abort(403, 'Unauthorized. You do not have delivery agent permissions.');
            }
            
            // If user is not logged in
            return redirect()->route('admin.login')
                ->with('error', 'Please login to access the delivery dashboard.');
        }
        
        return $next($request);
    }
}
