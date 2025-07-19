<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminRole
{
    protected $allowedRoles = ['admin', 'finance', 'activation', 'validation', 'it','delivery','audit'];

    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->hasAnyRole($this->allowedRoles)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }
            return redirect()->route('admin.login');
        }

        // Auto-logout after 1 hour of inactivity
        $lastActivity = session('last_admin_activity', 0);
        $inactiveFor = time() - $lastActivity;
        
        if ($lastActivity && $inactiveFor > 3600) {
            auth()->logout();
            session()->flush();
            return redirect()->route('admin.login')
                           ->with('message', 'Session expired due to inactivity.');
        }

        session(['last_admin_activity' => time()]);
        return $next($request);
    }
} 