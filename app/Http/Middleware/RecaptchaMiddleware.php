<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RecaptchaMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $action = 'default'): Response
    {
        // Skip if reCAPTCHA is disabled
        if (!config('security.recaptcha.enabled', true)) {
            return $next($request);
        }

        // Skip for GET requests (reCAPTCHA only for forms)
        if ($request->isMethod('GET')) {
            return $next($request);
        }

        // Check if reCAPTCHA should be triggered
        if (!$this->shouldTriggerRecaptcha($request, $action)) {
            return $next($request);
        }

        // Get reCAPTCHA token from request
        $recaptchaToken = $request->input('g-recaptcha-response');
        
        if (!$recaptchaToken) {
            return $this->blockResponse('reCAPTCHA token is required', $request);
        }

        // Verify reCAPTCHA token
        $verificationResult = $this->verifyRecaptcha($recaptchaToken, $request->ip(), $action);
        
        if (!$verificationResult['success']) {
            return $this->blockResponse($verificationResult['message'], $request);
        }

        // Store reCAPTCHA verification data for audit
        $this->storeVerificationData($request, $verificationResult, $action);

        return $next($request);
    }

    /**
     * Check if reCAPTCHA should be triggered
     */
    protected function shouldTriggerRecaptcha(Request $request, string $action): bool
    {
        $identifier = $this->getIdentifier($request);
        $triggers = config('security.recaptcha.triggers', []);
        
        // Always trigger for suspicious behavior
        if ($triggers['suspicious_behavior'] ?? true) {
            if ($this->hasSuspiciousActivity($identifier)) {
                return true;
            }
        }
        
        // Trigger after failed attempts
        if ($triggers['failed_attempts'] ?? false) {
            $threshold = $triggers['failed_attempts'];
            $failedAttempts = Cache::get($identifier . ':failed_attempts', 0);
            
            if ($failedAttempts >= $threshold) {
                return true;
            }
        }
        
        // Trigger for new IP addresses
        if ($triggers['new_ip'] ?? false) {
            if ($this->isNewIP($identifier, $request->ip())) {
                return true;
            }
        }
        
        // Trigger if bot was detected
        if ($triggers['bot_detected'] ?? true) {
            if ($this->wasBotDetected($identifier)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Verify reCAPTCHA token with Google
     */
    protected function verifyRecaptcha(string $token, string $ip, string $action): array
    {
        $secretKey = config('security.recaptcha.secret_key');
        $verifyUrl = config('security.recaptcha.verify_url');
        $timeout = config('security.recaptcha.timeout', 5);
        
        if (!$secretKey) {
            Log::error('reCAPTCHA secret key not configured');
            return [
                'success' => config('security.recaptcha.bypass_on_failure', false),
                'message' => 'reCAPTCHA verification unavailable',
                'score' => 0.0
            ];
        }

        try {
            $response = Http::timeout($timeout)
                ->asForm()
                ->post($verifyUrl, [
                    'secret' => $secretKey,
                    'response' => $token,
                    'remoteip' => $ip
                ]);

            if (!$response->successful()) {
                Log::error('reCAPTCHA API request failed', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                
                return [
                    'success' => config('security.recaptcha.bypass_on_failure', false),
                    'message' => 'reCAPTCHA verification failed',
                    'score' => 0.0
                ];
            }

            $data = $response->json();
            
            if (!$data['success']) {
                Log::warning('reCAPTCHA verification failed', [
                    'errors' => $data['error-codes'] ?? [],
                    'action' => $action,
                    'ip' => $ip
                ]);
                
                return [
                    'success' => false,
                    'message' => 'reCAPTCHA verification failed: ' . implode(', ', $data['error-codes'] ?? ['Unknown error']),
                    'score' => 0.0
                ];
            }

            $score = $data['score'] ?? 0.0;
            $responseAction = $data['action'] ?? '';
            
            // Verify action matches
            if ($responseAction !== $action) {
                Log::warning('reCAPTCHA action mismatch', [
                    'expected' => $action,
                    'received' => $responseAction,
                    'ip' => $ip
                ]);
                
                return [
                    'success' => false,
                    'message' => 'reCAPTCHA action mismatch',
                    'score' => $score
                ];
            }

            // Check score against threshold
            $threshold = $this->getScoreThreshold($action);
            
            if ($score < $threshold) {
                Log::warning('reCAPTCHA score below threshold', [
                    'score' => $score,
                    'threshold' => $threshold,
                    'action' => $action,
                    'ip' => $ip
                ]);
                
                return [
                    'success' => false,
                    'message' => 'reCAPTCHA score too low',
                    'score' => $score
                ];
            }

            return [
                'success' => true,
                'message' => 'reCAPTCHA verification successful',
                'score' => $score,
                'action' => $responseAction
            ];

        } catch (\Exception $e) {
            Log::error('reCAPTCHA verification exception', [
                'message' => $e->getMessage(),
                'action' => $action,
                'ip' => $ip
            ]);
            
            return [
                'success' => config('security.recaptcha.bypass_on_failure', false),
                'message' => 'reCAPTCHA verification error',
                'score' => 0.0
            ];
        }
    }

    /**
     * Get score threshold for action
     */
    protected function getScoreThreshold(string $action): float
    {
        $thresholds = config('security.recaptcha.score_thresholds', []);
        return $thresholds[$action] ?? config('security.recaptcha.minimum_score', 0.5);
    }

    /**
     * Get identifier for tracking
     */
    protected function getIdentifier(Request $request): string
    {
        $mobile = $request->input('mobile');
        $email = $request->input('email');
        $userId = $request->user()?->id;
        
        if ($userId) {
            return "recaptcha_user:{$userId}";
        } elseif ($mobile) {
            return "recaptcha_mobile:{$mobile}";
        } elseif ($email) {
            return "recaptcha_email:{$email}";
        }
        
        return "recaptcha_ip:" . $request->ip();
    }

    /**
     * Check for suspicious activity
     */
    protected function hasSuspiciousActivity(string $identifier): bool
    {
        // Check if account is suspended
        if (Cache::has(str_replace('recaptcha_', 'account_', $identifier) . ':suspended')) {
            return true;
        }
        
        // Check if bot was detected
        if (Cache::has(str_replace('recaptcha_', 'bot_', $identifier) . ':detected')) {
            return true;
        }
        
        // Check if rate limited
        if (Cache::has(str_replace('recaptcha_', 'rate_limit_', $identifier) . ':blocked')) {
            return true;
        }
        
        return false;
    }

    /**
     * Check if this is a new IP address
     */
    protected function isNewIP(string $identifier, string $ip): bool
    {
        $accountIdentifier = str_replace('recaptcha_', 'account_', $identifier);
        $recentIPs = Cache::get($accountIdentifier . ':recent_ips', []);
        
        foreach ($recentIPs as $ipData) {
            if ($ipData['ip'] === $ip) {
                return false;
            }
        }
        
        return count($recentIPs) > 0; // New IP only if there are previous IPs
    }

    /**
     * Check if bot was detected
     */
    protected function wasBotDetected(string $identifier): bool
    {
        $botIdentifier = str_replace('recaptcha_', 'bot_', $identifier);
        return Cache::has($botIdentifier . ':detected');
    }

    /**
     * Store verification data for audit
     */
    protected function storeVerificationData(Request $request, array $verificationResult, string $action): void
    {
        $identifier = $this->getIdentifier($request);
        
        $verificationData = [
            'timestamp' => time(),
            'ip' => $request->ip(),
            'action' => $action,
            'score' => $verificationResult['score'] ?? 0.0,
            'success' => $verificationResult['success'],
            'user_agent' => $request->userAgent(),
            'endpoint' => $request->path()
        ];
        
        // Store verification history
        $history = Cache::get($identifier . ':recaptcha_history', []);
        $history[] = $verificationData;
        $history = array_slice($history, -20); // Keep last 20 verifications
        
        Cache::put($identifier . ':recaptcha_history', $history, now()->addDays(30));
        
        // Update success rate
        $this->updateSuccessRate($identifier, $verificationResult['success']);
        
        // Log verification
        Log::info('reCAPTCHA verification completed', [
            'identifier' => $identifier,
            'score' => $verificationResult['score'] ?? 0.0,
            'action' => $action,
            'success' => $verificationResult['success']
        ]);
    }

    /**
     * Update success rate statistics
     */
    protected function updateSuccessRate(string $identifier, bool $success): void
    {
        $stats = Cache::get($identifier . ':recaptcha_stats', [
            'total' => 0,
            'successful' => 0,
            'failed' => 0
        ]);
        
        $stats['total']++;
        if ($success) {
            $stats['successful']++;
        } else {
            $stats['failed']++;
        }
        
        Cache::put($identifier . ':recaptcha_stats', $stats, now()->addDays(30));
    }

    /**
     * Create block response
     */
    protected function blockResponse(string $message, Request $request): Response
    {
        $identifier = $this->getIdentifier($request);
        
        // Log blocked request
        Log::warning('reCAPTCHA verification blocked request', [
            'identifier' => $identifier,
            'message' => $message,
            'ip' => $request->ip(),
            'endpoint' => $request->path(),
            'user_agent' => $request->userAgent()
        ]);
        
        // Track failed verification
        $failedCount = Cache::get($identifier . ':recaptcha_failed', 0) + 1;
        Cache::put($identifier . ':recaptcha_failed', $failedCount, now()->addHours(1));
        
        return response()->json([
            'error' => 'Security verification failed',
            'message' => $message,
            'code' => 'RECAPTCHA_VERIFICATION_FAILED'
        ], 403);
    }

    /**
     * Get security statistics for identifier
     */
    public function getSecurityStats(string $identifier): array
    {
        return [
            'recaptcha_history' => Cache::get($identifier . ':recaptcha_history', []),
            'recaptcha_stats' => Cache::get($identifier . ':recaptcha_stats', []),
            'recaptcha_failed' => Cache::get($identifier . ':recaptcha_failed', 0),
            'should_trigger' => $this->shouldTriggerRecaptcha(request(), 'default'),
            'last_verification' => $this->getLastVerification($identifier)
        ];
    }

    /**
     * Get last verification data
     */
    protected function getLastVerification(string $identifier): ?array
    {
        $history = Cache::get($identifier . ':recaptcha_history', []);
        return end($history) ?: null;
    }

    /**
     * Reset reCAPTCHA data for identifier
     */
    public function resetSecurity(string $identifier): void
    {
        Cache::forget($identifier . ':recaptcha_history');
        Cache::forget($identifier . ':recaptcha_stats');
        Cache::forget($identifier . ':recaptcha_failed');
        
        Log::info('reCAPTCHA security reset', [
            'identifier' => $identifier,
            'ip' => request()->ip()
        ]);
    }
}