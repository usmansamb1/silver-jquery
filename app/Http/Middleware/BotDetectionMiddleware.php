<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class BotDetectionMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip bot detection for whitelisted IPs
        if ($this->isWhitelistedIP($request->ip())) {
            return $next($request);
        }

        // Check if bot detection is enabled
        if (!config('security.bot_detection.enabled', true)) {
            return $next($request);
        }

        // Check for honeypot trap
        if ($this->isHoneypotTriggered($request)) {
            $this->logBotActivity($request, 'honeypot_triggered');
            return $this->blockBotResponse($request, 'Honeypot triggered');
        }

        // Check user agent for bot patterns
        if ($this->isSuspiciousUserAgent($request)) {
            $this->logBotActivity($request, 'suspicious_user_agent');
            return $this->blockBotResponse($request, 'Suspicious user agent');
        }

        // Check for JavaScript requirement
        if ($this->isJavaScriptRequired($request)) {
            $this->logBotActivity($request, 'javascript_required');
            return $this->blockBotResponse($request, 'JavaScript required');
        }

        // Check form submission timing
        if ($this->isSuspiciousFormTiming($request)) {
            $this->logBotActivity($request, 'suspicious_timing');
            return $this->blockBotResponse($request, 'Suspicious form timing');
        }

        // Check for automated patterns
        if ($this->hasAutomatedBehavior($request)) {
            $this->logBotActivity($request, 'automated_behavior');
            return $this->blockBotResponse($request, 'Automated behavior detected');
        }

        return $next($request);
    }

    /**
     * Check if IP is whitelisted
     */
    protected function isWhitelistedIP(string $ip): bool
    {
        $whitelist = config('security.monitoring.ip_whitelist', []);
        return in_array($ip, $whitelist);
    }

    /**
     * Check if honeypot field is filled
     */
    protected function isHoneypotTriggered(Request $request): bool
    {
        if (!config('security.bot_detection.honeypot.enabled', true)) {
            return false;
        }

        $honeypotField = config('security.bot_detection.honeypot.field_name', 'website');
        
        // Check if honeypot field has value (bots typically fill all fields)
        if ($request->filled($honeypotField)) {
            return true;
        }

        // Check for multiple honeypot fields
        $honeypotFields = ['website', 'url', 'company_website', 'homepage'];
        foreach ($honeypotFields as $field) {
            if ($request->filled($field) && !in_array($field, ['company_name', 'name', 'email', 'mobile'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check for suspicious user agent patterns
     */
    protected function isSuspiciousUserAgent(Request $request): bool
    {
        if (!config('security.bot_detection.user_agent_filtering.enabled', true)) {
            return false;
        }

        $userAgent = strtolower($request->userAgent() ?? '');
        
        if (empty($userAgent)) {
            return true; // No user agent is suspicious
        }

        $blockedPatterns = config('security.bot_detection.user_agent_filtering.blocked_patterns', []);
        $suspiciousPatterns = config('security.bot_detection.user_agent_filtering.suspicious_patterns', []);

        // Check for blocked patterns
        foreach ($blockedPatterns as $pattern) {
            if (strpos($userAgent, strtolower($pattern)) !== false) {
                return true;
            }
        }

        // Check for suspicious patterns and increase suspicion score
        $suspicionScore = 0;
        foreach ($suspiciousPatterns as $pattern) {
            if (strpos($userAgent, strtolower($pattern)) !== false) {
                $suspicionScore++;
            }
        }

        // Check for missing common browser indicators
        $browserIndicators = ['mozilla', 'webkit', 'chrome', 'firefox', 'safari', 'edge'];
        $hasIndicator = false;
        foreach ($browserIndicators as $indicator) {
            if (strpos($userAgent, $indicator) !== false) {
                $hasIndicator = true;
                break;
            }
        }

        if (!$hasIndicator) {
            $suspicionScore += 2;
        }

        return $suspicionScore >= 2;
    }

    /**
     * Check if JavaScript is required and not detected
     */
    protected function isJavaScriptRequired(Request $request): bool
    {
        if (!config('security.bot_detection.javascript_required', true)) {
            return false;
        }

        // Skip for GET requests
        if ($request->isMethod('GET')) {
            return false;
        }

        // Check for JavaScript token in form submissions
        $jsToken = $request->input('_js_token');
        if (empty($jsToken)) {
            return true;
        }

        // Verify JavaScript token is valid (should be generated by client-side JS)
        $expectedToken = $this->generateJavaScriptToken($request);
        if ($jsToken !== $expectedToken) {
            return true;
        }

        return false;
    }

    /**
     * Check for suspicious form submission timing
     */
    protected function isSuspiciousFormTiming(Request $request): bool
    {
        if (!config('security.bot_detection.timing_analysis.enabled', true)) {
            return false;
        }

        $formTime = $request->input('_form_time');
        if (!$formTime) {
            return false; // No timing data available
        }

        $submissionTime = time();
        $formLoadTime = (int) $formTime;
        $timeDiff = $submissionTime - $formLoadTime;

        $minTime = config('security.bot_detection.timing_analysis.min_form_time', 3);
        $maxTime = config('security.bot_detection.timing_analysis.max_form_time', 3600);

        // Too fast (likely bot)
        if ($timeDiff < $minTime) {
            return true;
        }

        // Too slow (likely abandoned session)
        if ($timeDiff > $maxTime) {
            return true;
        }

        return false;
    }

    /**
     * Check for automated behavior patterns
     */
    protected function hasAutomatedBehavior(Request $request): bool
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();
        
        // Check request frequency
        $requestKey = "bot_detection:{$ip}:requests";
        $requestCount = Cache::get($requestKey, 0);
        
        if ($requestCount > 20) { // More than 20 requests in 1 minute
            return true;
        }

        // Increment request counter
        Cache::put($requestKey, $requestCount + 1, now()->addMinutes(1));

        // Check for identical requests pattern
        $requestSignature = md5($request->getPathInfo() . $request->getQueryString() . $userAgent);
        $signatureKey = "bot_detection:{$ip}:signature:{$requestSignature}";
        $signatureCount = Cache::get($signatureKey, 0);

        if ($signatureCount > 5) { // Same request more than 5 times
            return true;
        }

        Cache::put($signatureKey, $signatureCount + 1, now()->addMinutes(5));

        // Check for rapid sequential requests
        $lastRequestKey = "bot_detection:{$ip}:last_request";
        $lastRequestTime = Cache::get($lastRequestKey, 0);
        $currentTime = microtime(true);

        if ($lastRequestTime && ($currentTime - $lastRequestTime) < 0.5) { // Less than 500ms between requests
            return true;
        }

        Cache::put($lastRequestKey, $currentTime, now()->addMinutes(1));

        return false;
    }

    /**
     * Generate JavaScript token for verification
     */
    protected function generateJavaScriptToken(Request $request): string
    {
        $data = $request->ip() . $request->userAgent() . date('Y-m-d-H');
        return hash('sha256', $data);
    }

    /**
     * Log bot activity
     */
    protected function logBotActivity(Request $request, string $reason): void
    {
        Log::warning('Bot activity detected', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'reason' => $reason,
            'headers' => $request->headers->all(),
            'timestamp' => now()->toISOString()
        ]);

        // Store in cache for monitoring
        $botKey = "bot_activity:{$request->ip()}";
        $activity = Cache::get($botKey, []);
        $activity[] = [
            'reason' => $reason,
            'timestamp' => now()->toISOString(),
            'url' => $request->fullUrl()
        ];
        Cache::put($botKey, $activity, now()->addHours(24));
    }

    /**
     * Return blocked bot response
     */
    protected function blockBotResponse(Request $request, string $reason): Response
    {
        // Auto-block if enabled
        if (config('security.bot_detection.honeypot.auto_block', true)) {
            $blockDuration = config('security.bot_detection.honeypot.block_duration', 86400);
            $blockKey = "bot_blocked:{$request->ip()}";
            Cache::put($blockKey, true, now()->addSeconds($blockDuration));
        }

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Access denied',
                'message' => 'Suspicious activity detected',
                'code' => 'BOT_DETECTED'
            ], 403);
        }

        return response()->view('errors.403', [
            'message' => 'Access denied due to suspicious activity'
        ], 403);
    }
}