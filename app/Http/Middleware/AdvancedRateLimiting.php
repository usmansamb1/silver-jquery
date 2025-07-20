<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class AdvancedRateLimiting
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $type = 'default'): SymfonyResponse
    {
        $identifier = $this->getIdentifier($request);
        $limits = $this->getLimits($type);
        
        // Check if IP is blocked
        if ($this->isBlocked($identifier)) {
            Log::warning('Blocked IP attempted access', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'route' => $request->route()?->getName(),
                'type' => $type
            ]);
            
            return response()->json([
                'error' => 'Too many attempts. Please try again later.',
                'retry_after' => $this->getBlockTimeRemaining($identifier)
            ], 429);
        }
        
        // Check rate limits
        $attempts = $this->getAttempts($identifier);
        $delay = $this->calculateDelay($attempts, $limits);
        
        if ($delay > 0) {
            // Apply progressive delay
            $this->applyDelay($identifier, $delay);
            
            Log::info('Rate limit applied', [
                'ip' => $request->ip(),
                'attempts' => $attempts,
                'delay' => $delay,
                'type' => $type
            ]);
            
            return response()->json([
                'error' => 'Rate limit exceeded. Please wait before trying again.',
                'retry_after' => $delay,
                'attempts' => $attempts
            ], 429);
        }
        
        // Increment attempt counter
        $this->incrementAttempts($identifier);
        
        $response = $next($request);
        
        // Reset attempts on successful response (200-299)
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $this->resetAttempts($identifier);
        }
        
        return $response;
    }
    
    /**
     * Get unique identifier for the request
     */
    protected function getIdentifier(Request $request): string
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();
        
        // Combine IP and user agent hash for better tracking
        return 'rate_limit:' . $ip . ':' . md5($userAgent ?? '');
    }
    
    /**
     * Get rate limiting configuration based on type
     */
    protected function getLimits(string $type): array
    {
        $limits = [
            'login' => [
                'max_attempts' => 3,
                'base_delay' => 300, // 5 minutes
                'multiplier' => 2,
                'max_delay' => 3600, // 1 hour
                'block_threshold' => 10,
                'block_duration' => 7200 // 2 hours
            ],
            'registration' => [
                'max_attempts' => 5,
                'base_delay' => 180, // 3 minutes
                'multiplier' => 1.5,
                'max_delay' => 1800, // 30 minutes
                'block_threshold' => 15,
                'block_duration' => 3600 // 1 hour
            ],
            'otp' => [
                'max_attempts' => 3,
                'base_delay' => 600, // 10 minutes
                'multiplier' => 3,
                'max_delay' => 7200, // 2 hours
                'block_threshold' => 5,
                'block_duration' => 14400 // 4 hours
            ],
            'default' => [
                'max_attempts' => 10,
                'base_delay' => 60, // 1 minute
                'multiplier' => 1.2,
                'max_delay' => 900, // 15 minutes
                'block_threshold' => 50,
                'block_duration' => 1800 // 30 minutes
            ]
        ];
        
        return $limits[$type] ?? $limits['default'];
    }
    
    /**
     * Get current attempt count
     */
    protected function getAttempts(string $identifier): int
    {
        return Cache::get($identifier . ':attempts', 0);
    }
    
    /**
     * Increment attempt counter
     */
    protected function incrementAttempts(string $identifier): void
    {
        $attempts = $this->getAttempts($identifier) + 1;
        Cache::put($identifier . ':attempts', $attempts, now()->addHours(24));
    }
    
    /**
     * Reset attempt counter
     */
    protected function resetAttempts(string $identifier): void
    {
        Cache::forget($identifier . ':attempts');
        Cache::forget($identifier . ':delay');
    }
    
    /**
     * Calculate delay based on attempts and limits
     */
    protected function calculateDelay(int $attempts, array $limits): int
    {
        if ($attempts < $limits['max_attempts']) {
            return 0;
        }
        
        // Check if user is in delay period
        $identifier = request()->ip() . ':' . md5(request()->userAgent() ?? '');
        $delayKey = $identifier . ':delay';
        
        if (Cache::has($delayKey)) {
            return Cache::get($delayKey . ':remaining', 0);
        }
        
        // Calculate progressive delay
        $excessAttempts = $attempts - $limits['max_attempts'];
        $delay = $limits['base_delay'] * pow($limits['multiplier'], $excessAttempts);
        $delay = min($delay, $limits['max_delay']);
        
        // Check if we should block the user
        if ($attempts >= $limits['block_threshold']) {
            $this->blockUser($identifier, $limits['block_duration']);
        }
        
        return (int) $delay;
    }
    
    /**
     * Apply delay to user
     */
    protected function applyDelay(string $identifier, int $delay): void
    {
        $delayKey = $identifier . ':delay';
        Cache::put($delayKey, true, now()->addSeconds($delay));
        Cache::put($delayKey . ':remaining', $delay, now()->addSeconds($delay));
    }
    
    
    /**
     * Block user for extended period
     */
    protected function blockUser(string $identifier, int $duration): void
    {
        $blockKey = $identifier . ':blocked';
        Cache::put($blockKey, true, now()->addSeconds($duration));
        Cache::put($blockKey . ':remaining', $duration, now()->addSeconds($duration));
        
        Log::warning('User blocked due to excessive attempts', [
            'identifier' => $identifier,
            'duration' => $duration,
            'ip' => request()->ip()
        ]);
    }
    
    /**
     * Check if user is blocked
     */
    protected function isBlocked(string $identifier): bool
    {
        return Cache::has($identifier . ':blocked');
    }
    
    /**
     * Get remaining block time
     */
    protected function getBlockTimeRemaining(string $identifier): int
    {
        return Cache::get($identifier . ':blocked:remaining', 0);
    }
}