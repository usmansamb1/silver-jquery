<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class OTPSecurityMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $identifier = $this->getIdentifier($request);
        
        // Check if OTP is locked out
        if ($this->isLockedOut($identifier)) {
            $remainingTime = $this->getLockoutRemainingTime($identifier);
            
            Log::warning('OTP lockout attempted access', [
                'identifier' => $identifier,
                'ip' => $request->ip(),
                'remaining_time' => $remainingTime,
                'endpoint' => $request->path()
            ]);
            
            return response()->json([
                'error' => 'OTP verification temporarily locked',
                'message' => 'Too many failed attempts. Please try again later.',
                'retry_after' => $remainingTime,
                'lockout_reason' => 'excessive_otp_attempts'
            ], 429);
        }
        
        // Check resend cooldown
        if ($this->isResendCooldown($identifier)) {
            $cooldownRemaining = $this->getResendCooldownRemaining($identifier);
            
            return response()->json([
                'error' => 'OTP resend cooldown active',
                'message' => 'Please wait before requesting another OTP',
                'retry_after' => $cooldownRemaining
            ], 429);
        }
        
        // Check maximum resends
        if ($this->hasExceededMaxResends($identifier)) {
            $this->applyLockout($identifier);
            
            return response()->json([
                'error' => 'Maximum OTP resends exceeded',
                'message' => 'Too many OTP requests. Account temporarily locked.',
                'retry_after' => config('security.otp_security.lockout_duration', 3600)
            ], 429);
        }
        
        $response = $next($request);
        
        // Track OTP attempts and resends
        $this->trackOTPActivity($request, $response, $identifier);
        
        return $response;
    }
    
    /**
     * Get unique identifier for OTP tracking
     */
    protected function getIdentifier(Request $request): string
    {
        $mobile = $request->input('mobile');
        $tempToken = $request->input('temp_token');
        
        if ($mobile) {
            return "otp_mobile:" . $mobile;
        } elseif ($tempToken) {
            return "otp_token:" . $tempToken;
        }
        
        return "otp_ip:" . $request->ip();
    }
    
    /**
     * Check if OTP verification is locked out
     */
    protected function isLockedOut(string $identifier): bool
    {
        return Cache::has($identifier . ':lockout');
    }
    
    /**
     * Get remaining lockout time
     */
    protected function getLockoutRemainingTime(string $identifier): int
    {
        return Cache::get($identifier . ':lockout:remaining', 0);
    }
    
    /**
     * Check if in resend cooldown
     */
    protected function isResendCooldown(string $identifier): bool
    {
        return Cache::has($identifier . ':resend_cooldown');
    }
    
    /**
     * Get remaining resend cooldown time
     */
    protected function getResendCooldownRemaining(string $identifier): int
    {
        return Cache::get($identifier . ':resend_cooldown:remaining', 0);
    }
    
    /**
     * Check if exceeded maximum resends
     */
    protected function hasExceededMaxResends(string $identifier): bool
    {
        $maxResends = config('security.otp_security.max_resends', 3);
        $resendCount = Cache::get($identifier . ':resend_count', 0);
        
        return $resendCount >= $maxResends;
    }
    
    /**
     * Apply lockout to identifier
     */
    protected function applyLockout(string $identifier): void
    {
        $lockoutDuration = config('security.otp_security.lockout_duration', 3600);
        
        Cache::put($identifier . ':lockout', true, now()->addSeconds($lockoutDuration));
        Cache::put($identifier . ':lockout:remaining', $lockoutDuration, now()->addSeconds($lockoutDuration));
        
        // Reset counters
        Cache::forget($identifier . ':attempts');
        Cache::forget($identifier . ':resend_count');
        Cache::forget($identifier . ':resend_cooldown');
        
        Log::warning('OTP lockout applied', [
            'identifier' => $identifier,
            'duration' => $lockoutDuration,
            'ip' => request()->ip()
        ]);
    }
    
    /**
     * Track OTP activity
     */
    protected function trackOTPActivity(Request $request, Response $response, string $identifier): void
    {
        $endpoint = $request->path();
        $statusCode = $response->getStatusCode();
        
        // Track OTP verification attempts
        if (strpos($endpoint, 'verify-otp') !== false) {
            $this->trackVerificationAttempt($identifier, $statusCode);
        }
        
        // Track OTP resend requests
        if (strpos($endpoint, 'otp') !== false && $request->isMethod('POST') && !strpos($endpoint, 'verify')) {
            $this->trackResendRequest($identifier, $statusCode);
        }
        
        // Track OTP generation
        if (strpos($endpoint, 'otp') !== false && $statusCode === 200) {
            $this->trackOTPGeneration($identifier);
        }
    }
    
    /**
     * Track OTP verification attempt
     */
    protected function trackVerificationAttempt(string $identifier, int $statusCode): void
    {
        $attempts = Cache::get($identifier . ':attempts', 0);
        $maxAttempts = config('security.otp_security.max_attempts', 5);
        
        if ($statusCode !== 200) {
            // Failed verification
            $attempts++;
            Cache::put($identifier . ':attempts', $attempts, now()->addMinutes(30));
            
            Log::info('OTP verification failed', [
                'identifier' => $identifier,
                'attempts' => $attempts,
                'max_attempts' => $maxAttempts,
                'ip' => request()->ip()
            ]);
            
            // Apply lockout if max attempts reached
            if ($attempts >= $maxAttempts) {
                $this->applyLockout($identifier);
            }
        } else {
            // Successful verification - reset counters
            Cache::forget($identifier . ':attempts');
            Cache::forget($identifier . ':resend_count');
            Cache::forget($identifier . ':resend_cooldown');
            
            Log::info('OTP verification successful', [
                'identifier' => $identifier,
                'ip' => request()->ip()
            ]);
        }
    }
    
    /**
     * Track OTP resend request
     */
    protected function trackResendRequest(string $identifier, int $statusCode): void
    {
        if ($statusCode === 200) {
            $resendCount = Cache::get($identifier . ':resend_count', 0) + 1;
            Cache::put($identifier . ':resend_count', $resendCount, now()->addHours(1));
            
            // Apply resend cooldown
            $cooldownDuration = config('security.otp_security.resend_cooldown', 60);
            Cache::put($identifier . ':resend_cooldown', true, now()->addSeconds($cooldownDuration));
            Cache::put($identifier . ':resend_cooldown:remaining', $cooldownDuration, now()->addSeconds($cooldownDuration));
            
            Log::info('OTP resend requested', [
                'identifier' => $identifier,
                'resend_count' => $resendCount,
                'ip' => request()->ip()
            ]);
        }
    }
    
    /**
     * Track OTP generation
     */
    protected function trackOTPGeneration(string $identifier): void
    {
        $otpData = [
            'generated_at' => now()->toISOString(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'expires_at' => now()->addMinutes(config('security.otp_security.expiry_minutes', 5))->toISOString()
        ];
        
        Cache::put($identifier . ':otp_data', $otpData, now()->addMinutes(10));
        
        Log::info('OTP generated', [
            'identifier' => $identifier,
            'ip' => request()->ip()
        ]);
    }
    
    /**
     * Get OTP security statistics
     */
    public function getSecurityStats(string $identifier): array
    {
        return [
            'attempts' => Cache::get($identifier . ':attempts', 0),
            'resend_count' => Cache::get($identifier . ':resend_count', 0),
            'is_locked_out' => $this->isLockedOut($identifier),
            'lockout_remaining' => $this->getLockoutRemainingTime($identifier),
            'resend_cooldown_remaining' => $this->getResendCooldownRemaining($identifier),
            'otp_data' => Cache::get($identifier . ':otp_data', [])
        ];
    }
    
    /**
     * Reset OTP security for identifier
     */
    public function resetSecurity(string $identifier): void
    {
        Cache::forget($identifier . ':attempts');
        Cache::forget($identifier . ':resend_count');
        Cache::forget($identifier . ':resend_cooldown');
        Cache::forget($identifier . ':lockout');
        Cache::forget($identifier . ':otp_data');
        
        Log::info('OTP security reset', [
            'identifier' => $identifier,
            'ip' => request()->ip()
        ]);
    }
}