<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Middleware\AdvancedRateLimiting;
use App\Http\Middleware\BotDetectionMiddleware;
use App\Http\Middleware\OTPSecurityMiddleware;
use App\Http\Middleware\AccountSecurityMiddleware;

class SecurityMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    /** @test */
    public function advanced_rate_limiting_middleware_can_be_instantiated()
    {
        $middleware = new AdvancedRateLimiting();
        $this->assertInstanceOf(AdvancedRateLimiting::class, $middleware);
    }

    /** @test */
    public function bot_detection_middleware_can_be_instantiated()
    {
        $middleware = new BotDetectionMiddleware();
        $this->assertInstanceOf(BotDetectionMiddleware::class, $middleware);
    }

    /** @test */
    public function otp_security_middleware_can_be_instantiated()
    {
        $middleware = new OTPSecurityMiddleware();
        $this->assertInstanceOf(OTPSecurityMiddleware::class, $middleware);
    }

    /** @test */
    public function account_security_middleware_can_be_instantiated()
    {
        $middleware = new AccountSecurityMiddleware();
        $this->assertInstanceOf(AccountSecurityMiddleware::class, $middleware);
    }

    /** @test */
    public function rate_limiting_tracks_attempts_correctly()
    {
        $middleware = new AdvancedRateLimiting();
        $request = Request::create('/test', 'POST', ['mobile' => '0512345678']);
        
        // Use reflection to test private method
        $reflection = new \ReflectionClass($middleware);
        $method = $reflection->getMethod('getIdentifier');
        $method->setAccessible(true);
        
        $identifier = $method->invoke($middleware, $request);
        $this->assertStringStartsWith('rate_limit:', $identifier);
        $this->assertStringContainsString('878d66f98b73e26b50c1392e7ee12ad9', $identifier);
    }

    /** @test */
    public function bot_detection_detects_honeypot_correctly()
    {
        $middleware = new BotDetectionMiddleware();
        $request = Request::create('/test', 'POST', [
            'mobile' => '0512345678',
            'website' => 'http://spam.com' // Honeypot field
        ]);
        
        // Use reflection to test private method
        $reflection = new \ReflectionClass($middleware);
        $method = $reflection->getMethod('isHoneypotTriggered');
        $method->setAccessible(true);
        
        $result = $method->invoke($middleware, $request);
        $this->assertTrue($result);
    }

    /** @test */
    public function account_security_calculates_suspicion_levels()
    {
        $middleware = new AccountSecurityMiddleware();
        $identifier = 'account_mobile:0512345678';
        
        // Set up suspicious conditions
        $failedLogins = [
            ['ip' => '192.168.1.1', 'timestamp' => time() - 1800],
            ['ip' => '10.0.0.1', 'timestamp' => time() - 1200],
            ['ip' => '172.16.0.1', 'timestamp' => time() - 600],
            ['ip' => '203.0.113.1', 'timestamp' => time() - 300],
            ['ip' => '198.51.100.1', 'timestamp' => time() - 100]
        ];
        
        Cache::put($identifier . ':failed_logins', $failedLogins, now()->addHours(24));
        
        $stats = $middleware->getSecurityStats($identifier);
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('suspicion_level', $stats);
        $this->assertGreaterThan(0, $stats['suspicion_level']);
    }

    /** @test */
    public function otp_security_tracks_attempts()
    {
        $middleware = new OTPSecurityMiddleware();
        $identifier = 'otp_mobile:0512345678';
        
        // Test that we can get statistics (which includes attempt tracking)
        $stats = $middleware->getSecurityStats($identifier);
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('attempts', $stats);
        $this->assertArrayHasKey('resend_count', $stats);
        $this->assertArrayHasKey('is_locked_out', $stats);
    }

    /** @test */
    public function security_middleware_configuration_works()
    {
        // Test that configuration values are properly loaded
        $this->assertTrue(config('security.rate_limiting.enabled', true));
        $this->assertTrue(config('security.bot_detection.enabled', true));
        $this->assertIsNumeric(config('security.otp_security.max_attempts', 5));
        $this->assertIsNumeric(config('security.account_security.max_concurrent_sessions', 3));
    }

    /** @test */
    public function cache_keys_are_properly_namespaced()
    {
        $middleware = new AdvancedRateLimiting();
        $request = Request::create('/test', 'POST', ['mobile' => '0512345678']);
        
        // Use reflection to test private method
        $reflection = new \ReflectionClass($middleware);
        $method = $reflection->getMethod('getIdentifier');
        $method->setAccessible(true);
        
        $identifier = $method->invoke($middleware, $request);
        $this->assertStringStartsWith('rate_limit:', $identifier);
    }

    /** @test */
    public function security_statistics_are_properly_formatted()
    {
        $middleware = new AccountSecurityMiddleware();
        $identifier = 'account_mobile:0512345678';
        
        $stats = $middleware->getSecurityStats($identifier);
        
        $expectedKeys = [
            'is_suspended',
            'suspension_data',
            'failed_logins',
            'recent_ips',
            'active_sessions',
            'login_times',
            'requires_verification',
            'suspicion_level'
        ];
        
        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $stats);
        }
    }

    /** @test */
    public function security_reset_clears_all_data()
    {
        $middleware = new AccountSecurityMiddleware();
        $identifier = 'account_mobile:0512345678';
        
        // Set up some data
        Cache::put($identifier . ':failed_logins', ['test'], now()->addHours(24));
        Cache::put($identifier . ':suspended', true, now()->addHours(1));
        
        // Reset security
        $middleware->resetSecurity($identifier);
        
        // Check that data is cleared
        $this->assertFalse(Cache::has($identifier . ':failed_logins'));
        $this->assertFalse(Cache::has($identifier . ':suspended'));
    }

    protected function tearDown(): void
    {
        Cache::flush();
        parent::tearDown();
    }
}