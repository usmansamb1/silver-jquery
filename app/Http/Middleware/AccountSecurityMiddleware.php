<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AccountSecurityMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $identifier = $this->getIdentifier($request);
        
        // Check if account is temporarily suspended
        if ($this->isSuspended($identifier)) {
            $suspensionData = $this->getSuspensionData($identifier);
            
            $this->logSecurityEvent($identifier, 'suspended_account_access_attempted', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'suspension_reason' => $suspensionData['reason'] ?? 'unknown',
                'suspension_expires' => $suspensionData['expires_at'] ?? null
            ]);
            
            return response()->json([
                'error' => 'Account temporarily suspended',
                'message' => 'Your account has been temporarily suspended due to security concerns',
                'retry_after' => $suspensionData['remaining_time'] ?? 0,
                'suspension_reason' => $suspensionData['reason'] ?? 'security_violation',
                'contact_support' => true
            ], 403);
        }
        
        // Check for suspicious login patterns
        if ($this->detectSuspiciousActivity($request, $identifier)) {
            $this->handleSuspiciousActivity($request, $identifier);
        }
        
        $response = $next($request);
        
        // Track security events after processing
        $this->trackSecurityEvents($request, $response, $identifier);
        
        return $response;
    }
    
    /**
     * Get unique identifier for account security tracking
     */
    protected function getIdentifier(Request $request): string
    {
        $mobile = $request->input('mobile');
        $email = $request->input('email');
        $userId = $request->user()?->id;
        
        if ($userId) {
            return "account_user:{$userId}";
        } elseif ($mobile) {
            return "account_mobile:{$mobile}";
        } elseif ($email) {
            return "account_email:{$email}";
        }
        
        return "account_ip:" . $request->ip();
    }
    
    /**
     * Check if account is suspended
     */
    protected function isSuspended(string $identifier): bool
    {
        return Cache::has($identifier . ':suspended');
    }
    
    /**
     * Get suspension data
     */
    protected function getSuspensionData(string $identifier): array
    {
        return Cache::get($identifier . ':suspension_data', []);
    }
    
    /**
     * Detect suspicious activity patterns
     */
    protected function detectSuspiciousActivity(Request $request, string $identifier): bool
    {
        // Check for multiple failed login attempts from different IPs
        if ($this->hasMultipleFailedLoginsFromDifferentIPs($identifier)) {
            return true;
        }
        
        // Check for rapid location changes
        if ($this->hasRapidLocationChanges($identifier, $request->ip())) {
            return true;
        }
        
        // Check for unusual time patterns
        if ($this->hasUnusualTimePattern($identifier)) {
            return true;
        }
        
        // Check for multiple concurrent sessions
        if ($this->hasMultipleConcurrentSessions($identifier)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Check for multiple failed logins from different IPs
     */
    protected function hasMultipleFailedLoginsFromDifferentIPs(string $identifier): bool
    {
        $failedLogins = Cache::get($identifier . ':failed_logins', []);
        
        if (count($failedLogins) < 3) {
            return false;
        }
        
        $uniqueIPs = array_unique(array_column($failedLogins, 'ip'));
        $recentFailures = array_filter($failedLogins, function($login) {
            return $login['timestamp'] > (time() - 3600); // Within last hour
        });
        
        return count($uniqueIPs) >= 3 && count($recentFailures) >= 5;
    }
    
    /**
     * Check for rapid location changes
     */
    protected function hasRapidLocationChanges(string $identifier, string $currentIP): bool
    {
        $recentIPs = Cache::get($identifier . ':recent_ips', []);
        
        if (count($recentIPs) < 2) {
            return false;
        }
        
        $lastIP = end($recentIPs);
        $timeDiff = time() - $lastIP['timestamp'];
        
        // If IP changed within 5 minutes, consider it suspicious
        return $lastIP['ip'] !== $currentIP && $timeDiff < 300;
    }
    
    /**
     * Check for unusual time patterns
     */
    protected function hasUnusualTimePattern(string $identifier): bool
    {
        $loginTimes = Cache::get($identifier . ':login_times', []);
        
        if (count($loginTimes) < 5) {
            return false;
        }
        
        $currentHour = (int) date('H');
        $recentLoginHours = array_slice($loginTimes, -5);
        
        // Check if all recent logins are in unusual hours (2 AM - 6 AM)
        $unusualHours = array_filter($recentLoginHours, function($hour) {
            return $hour >= 2 && $hour <= 6;
        });
        
        return count($unusualHours) >= 4;
    }
    
    /**
     * Check for multiple concurrent sessions
     */
    protected function hasMultipleConcurrentSessions(string $identifier): bool
    {
        $maxSessions = config('security.session_security.max_concurrent_sessions', 3);
        $activeSessions = Cache::get($identifier . ':active_sessions', []);
        
        // Clean up expired sessions
        $activeSessions = array_filter($activeSessions, function($session) {
            return $session['last_activity'] > (time() - 1800); // 30 minutes
        });
        
        return count($activeSessions) > $maxSessions;
    }
    
    /**
     * Handle suspicious activity
     */
    protected function handleSuspiciousActivity(Request $request, string $identifier): void
    {
        $suspicionLevel = $this->calculateSuspicionLevel($identifier);
        
        if ($suspicionLevel >= 3) {
            // High suspicion - temporary suspension
            $this->applySuspension($identifier, 'suspicious_activity', 3600); // 1 hour
            
            // Send security alert
            $this->sendSecurityAlert($identifier, 'account_suspended', [
                'reason' => 'suspicious_activity',
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'suspicion_level' => $suspicionLevel
            ]);
        } elseif ($suspicionLevel >= 2) {
            // Medium suspicion - require additional verification
            $this->requireAdditionalVerification($identifier);
        }
        
        // Log suspicious activity
        $this->logSecurityEvent($identifier, 'suspicious_activity_detected', [
            'suspicion_level' => $suspicionLevel,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
    }
    
    /**
     * Calculate suspicion level
     */
    protected function calculateSuspicionLevel(string $identifier): int
    {
        $level = 0;
        
        if ($this->hasMultipleFailedLoginsFromDifferentIPs($identifier)) {
            $level += 2;
        }
        
        if ($this->hasRapidLocationChanges($identifier, request()->ip())) {
            $level += 1;
        }
        
        if ($this->hasUnusualTimePattern($identifier)) {
            $level += 1;
        }
        
        if ($this->hasMultipleConcurrentSessions($identifier)) {
            $level += 1;
        }
        
        return $level;
    }
    
    /**
     * Apply suspension to account
     */
    protected function applySuspension(string $identifier, string $reason, int $duration): void
    {
        $expiresAt = now()->addSeconds($duration);
        
        $suspensionData = [
            'reason' => $reason,
            'applied_at' => now()->toISOString(),
            'expires_at' => $expiresAt->toISOString(),
            'remaining_time' => $duration,
            'ip' => request()->ip()
        ];
        
        Cache::put($identifier . ':suspended', true, $expiresAt);
        Cache::put($identifier . ':suspension_data', $suspensionData, $expiresAt);
        
        // Clear other tracking data
        Cache::forget($identifier . ':failed_logins');
        Cache::forget($identifier . ':recent_ips');
        Cache::forget($identifier . ':active_sessions');
        
        $this->logSecurityEvent($identifier, 'account_suspended', $suspensionData);
    }
    
    /**
     * Require additional verification
     */
    protected function requireAdditionalVerification(string $identifier): void
    {
        Cache::put($identifier . ':requires_verification', true, now()->addHours(1));
        
        $this->logSecurityEvent($identifier, 'additional_verification_required', [
            'ip' => request()->ip()
        ]);
    }
    
    /**
     * Track security events
     */
    protected function trackSecurityEvents(Request $request, Response $response, string $identifier): void
    {
        $statusCode = $response->getStatusCode();
        $endpoint = $request->path();
        
        // Track login attempts
        if (strpos($endpoint, 'login') !== false) {
            $this->trackLoginAttempt($identifier, $request, $statusCode);
        }
        
        // Track IP changes
        $this->trackIPChange($identifier, $request->ip());
        
        // Track session activity
        if ($request->user()) {
            $this->trackSessionActivity($identifier, $request);
        }
    }
    
    /**
     * Track login attempt
     */
    protected function trackLoginAttempt(string $identifier, Request $request, int $statusCode): void
    {
        $failedLogins = Cache::get($identifier . ':failed_logins', []);
        
        if ($statusCode !== 200) {
            $failedLogins[] = [
                'ip' => $request->ip(),
                'timestamp' => time(),
                'user_agent' => $request->userAgent(),
                'endpoint' => $request->path()
            ];
            
            // Keep only last 20 failed attempts
            $failedLogins = array_slice($failedLogins, -20);
            
            Cache::put($identifier . ':failed_logins', $failedLogins, now()->addHours(24));
        } else {
            // Successful login - reset failed attempts
            Cache::forget($identifier . ':failed_logins');
            
            // Track login time
            $loginTimes = Cache::get($identifier . ':login_times', []);
            $loginTimes[] = (int) date('H');
            $loginTimes = array_slice($loginTimes, -10); // Keep last 10 login times
            
            Cache::put($identifier . ':login_times', $loginTimes, now()->addDays(7));
        }
    }
    
    /**
     * Track IP changes
     */
    protected function trackIPChange(string $identifier, string $ip): void
    {
        $recentIPs = Cache::get($identifier . ':recent_ips', []);
        
        // Check if IP already exists
        $existingIP = array_filter($recentIPs, function($item) use ($ip) {
            return $item['ip'] === $ip;
        });
        
        if (empty($existingIP)) {
            $recentIPs[] = [
                'ip' => $ip,
                'timestamp' => time(),
                'first_seen' => time()
            ];
            
            // Keep only last 10 IPs
            $recentIPs = array_slice($recentIPs, -10);
            
            Cache::put($identifier . ':recent_ips', $recentIPs, now()->addDays(30));
        }
    }
    
    /**
     * Track session activity
     */
    protected function trackSessionActivity(string $identifier, Request $request): void
    {
        $sessionId = $request->session()->getId();
        $activeSessions = Cache::get($identifier . ':active_sessions', []);
        
        $activeSessions[$sessionId] = [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'last_activity' => time(),
            'started_at' => $activeSessions[$sessionId]['started_at'] ?? time()
        ];
        
        Cache::put($identifier . ':active_sessions', $activeSessions, now()->addHours(2));
    }
    
    /**
     * Send security alert
     */
    protected function sendSecurityAlert(string $identifier, string $type, array $data): void
    {
        // Extract email/mobile from identifier for notification
        $notificationTarget = $this->extractNotificationTarget($identifier);
        
        if ($notificationTarget) {
            // Queue security alert notification
            $alertData = array_merge($data, [
                'type' => $type,
                'timestamp' => now()->toISOString(),
                'identifier' => $identifier
            ]);
            
            // Store for admin notification
            $alerts = Cache::get('security_alerts', []);
            $alerts[] = $alertData;
            Cache::put('security_alerts', array_slice($alerts, -100), now()->addDays(7));
        }
    }
    
    /**
     * Extract notification target from identifier
     */
    protected function extractNotificationTarget(string $identifier): ?string
    {
        if (strpos($identifier, 'account_mobile:') === 0) {
            return substr($identifier, 15); // Remove 'account_mobile:' prefix
        } elseif (strpos($identifier, 'account_email:') === 0) {
            return substr($identifier, 14); // Remove 'account_email:' prefix
        }
        
        return null;
    }
    
    /**
     * Log security event
     */
    protected function logSecurityEvent(string $identifier, string $event, array $data): void
    {
        Log::info("Account security event: {$event}", array_merge([
            'identifier' => $identifier,
            'timestamp' => now()->toISOString()
        ], $data));
    }
    
    /**
     * Get security statistics for identifier
     */
    public function getSecurityStats(string $identifier): array
    {
        return [
            'is_suspended' => $this->isSuspended($identifier),
            'suspension_data' => $this->getSuspensionData($identifier),
            'failed_logins' => Cache::get($identifier . ':failed_logins', []),
            'recent_ips' => Cache::get($identifier . ':recent_ips', []),
            'active_sessions' => Cache::get($identifier . ':active_sessions', []),
            'login_times' => Cache::get($identifier . ':login_times', []),
            'requires_verification' => Cache::get($identifier . ':requires_verification', false),
            'suspicion_level' => $this->calculateSuspicionLevel($identifier)
        ];
    }
    
    /**
     * Reset security data for identifier
     */
    public function resetSecurity(string $identifier): void
    {
        Cache::forget($identifier . ':suspended');
        Cache::forget($identifier . ':suspension_data');
        Cache::forget($identifier . ':failed_logins');
        Cache::forget($identifier . ':recent_ips');
        Cache::forget($identifier . ':active_sessions');
        Cache::forget($identifier . ':login_times');
        Cache::forget($identifier . ':requires_verification');
        
        $this->logSecurityEvent($identifier, 'security_reset', [
            'ip' => request()->ip()
        ]);
    }
}