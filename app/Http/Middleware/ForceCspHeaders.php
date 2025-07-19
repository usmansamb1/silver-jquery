<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceCspHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Remove any existing CSP headers
        $response->headers->remove('Content-Security-Policy');
        $response->headers->remove('X-Content-Security-Policy');
        $response->headers->remove('X-WebKit-CSP');

        // The permissive CSP required by Hyperpay and other assets
        $permissiveCsp = "default-src 'self' https: wss:; " .
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://*.oppwa.com https://*.techlab-cdn.com https://code.jquery.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " .
            "style-src 'self' 'unsafe-inline' https: http: https://*.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " .
            "img-src 'self' data: https: http:; " .
            "font-src 'self' data: https://*.gstatic.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; " .
            "connect-src 'self' https: wss: https://*.oppwa.com https://*.techlab-cdn.com; " .
            "frame-src 'self' https://*.oppwa.com https://www.google.com https://maps.google.com https://*.google.com; " .
            "worker-src 'self' blob: https:;";

        // Apply our new, correct CSP header
        $response->headers->set('Content-Security-Policy', $permissiveCsp);

        return $response;
    }
} 