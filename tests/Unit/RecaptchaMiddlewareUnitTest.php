<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use App\Http\Middleware\RecaptchaMiddleware;

class RecaptchaMiddlewareUnitTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    /** @test */
    public function recaptcha_middleware_can_be_instantiated()
    {
        $middleware = new RecaptchaMiddleware();
        $this->assertInstanceOf(RecaptchaMiddleware::class, $middleware);
    }

    /** @test */
    public function it_gets_correct_identifier_from_request()
    {
        $middleware = new RecaptchaMiddleware();
        
        // Test with mobile
        $request = Request::create('/test', 'POST', ['mobile' => '0512345678']);
        $reflection = new \ReflectionClass($middleware);
        $method = $reflection->getMethod('getIdentifier');
        $method->setAccessible(true);
        
        $identifier = $method->invoke($middleware, $request);
        $this->assertEquals('recaptcha_mobile:0512345678', $identifier);
        
        // Test with email
        $request = Request::create('/test', 'POST', ['email' => 'test@example.com']);
        $identifier = $method->invoke($middleware, $request);
        $this->assertEquals('recaptcha_email:test@example.com', $identifier);
    }

    /** @test */
    public function it_gets_correct_score_threshold_for_action()
    {
        $middleware = new RecaptchaMiddleware();
        
        Config::set('security.recaptcha.score_thresholds.login', 0.7);
        Config::set('security.recaptcha.score_thresholds.register', 0.3);
        Config::set('security.recaptcha.minimum_score', 0.5);
        
        $reflection = new \ReflectionClass($middleware);
        $method = $reflection->getMethod('getScoreThreshold');
        $method->setAccessible(true);
        
        $this->assertEquals(0.7, $method->invoke($middleware, 'login'));
        $this->assertEquals(0.3, $method->invoke($middleware, 'register'));
        $this->assertEquals(0.5, $method->invoke($middleware, 'unknown_action'));
    }

    /** @test */
    public function it_detects_suspicious_activity()
    {
        $middleware = new RecaptchaMiddleware();
        $identifier = 'recaptcha_mobile:0512345678';
        
        $reflection = new \ReflectionClass($middleware);
        $method = $reflection->getMethod('hasSuspiciousActivity');
        $method->setAccessible(true);
        
        // No suspicious activity initially
        $this->assertFalse($method->invoke($middleware, $identifier));
        
        // Set up suspended account
        Cache::put('account_mobile:0512345678:suspended', true, now()->addHours(1));
        $this->assertTrue($method->invoke($middleware, $identifier));
        
        // Clear and test bot detection
        Cache::flush();
        Cache::put('bot_mobile:0512345678:detected', true, now()->addHours(1));
        $this->assertTrue($method->invoke($middleware, $identifier));
    }

    /** @test */
    public function it_detects_new_ip_addresses()
    {
        $middleware = new RecaptchaMiddleware();
        $identifier = 'recaptcha_mobile:0512345678';
        
        $reflection = new \ReflectionClass($middleware);
        $method = $reflection->getMethod('isNewIP');
        $method->setAccessible(true);
        
        // No previous IPs - should return false
        $this->assertFalse($method->invoke($middleware, $identifier, '192.168.1.1'));
        
        // Set up previous IPs
        Cache::put('account_mobile:0512345678:recent_ips', [
            ['ip' => '192.168.1.1', 'timestamp' => time() - 3600],
            ['ip' => '10.0.0.1', 'timestamp' => time() - 1800]
        ], now()->addDays(30));
        
        // Same IP - should return false
        $this->assertFalse($method->invoke($middleware, $identifier, '192.168.1.1'));
        
        // New IP - should return true
        $this->assertTrue($method->invoke($middleware, $identifier, '172.16.0.1'));
    }

    /** @test */
    public function it_determines_when_to_trigger_recaptcha()
    {
        $middleware = new RecaptchaMiddleware();
        $request = Request::create('/test', 'POST', ['mobile' => '0512345678']);
        
        $reflection = new \ReflectionClass($middleware);
        $method = $reflection->getMethod('shouldTriggerRecaptcha');
        $method->setAccessible(true);
        
        // Should not trigger initially
        $this->assertFalse($method->invoke($middleware, $request, 'login'));
        
        // Set up suspended account (suspicious behavior)
        Cache::put('account_mobile:0512345678:suspended', true, now()->addHours(1));
        $this->assertTrue($method->invoke($middleware, $request, 'login'));
    }

    /** @test */
    public function it_provides_security_statistics()
    {
        $middleware = new RecaptchaMiddleware();
        $identifier = 'recaptcha_mobile:0512345678';
        
        $stats = $middleware->getSecurityStats($identifier);
        
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('recaptcha_history', $stats);
        $this->assertArrayHasKey('recaptcha_stats', $stats);
        $this->assertArrayHasKey('recaptcha_failed', $stats);
        $this->assertArrayHasKey('should_trigger', $stats);
        $this->assertArrayHasKey('last_verification', $stats);
    }

    /** @test */
    public function it_can_reset_security_data()
    {
        $middleware = new RecaptchaMiddleware();
        $identifier = 'recaptcha_mobile:0512345678';
        
        // Set up some data
        Cache::put($identifier . ':recaptcha_history', ['test'], now()->addHours(1));
        Cache::put($identifier . ':recaptcha_stats', ['test'], now()->addHours(1));
        Cache::put($identifier . ':recaptcha_failed', 5, now()->addHours(1));
        
        // Reset security
        $middleware->resetSecurity($identifier);
        
        // Check that data is cleared
        $this->assertFalse(Cache::has($identifier . ':recaptcha_history'));
        $this->assertFalse(Cache::has($identifier . ':recaptcha_stats'));
        $this->assertFalse(Cache::has($identifier . ':recaptcha_failed'));
    }

    /** @test */
    public function it_handles_configuration_correctly()
    {
        // Test enabled/disabled state
        Config::set('security.recaptcha.enabled', false);
        $middleware = new RecaptchaMiddleware();
        $this->assertFalse(config('security.recaptcha.enabled'));
        
        Config::set('security.recaptcha.enabled', true);
        $this->assertTrue(config('security.recaptcha.enabled'));
        
        // Test actions configuration
        Config::set('security.recaptcha.actions', [
            'login' => 'login',
            'register' => 'register'
        ]);
        
        $actions = config('security.recaptcha.actions');
        $this->assertArrayHasKey('login', $actions);
        $this->assertArrayHasKey('register', $actions);
        $this->assertEquals('login', $actions['login']);
    }

    /** @test */
    public function it_handles_failed_verification_tracking()
    {
        $middleware = new RecaptchaMiddleware();
        $identifier = 'recaptcha_mobile:0512345678';
        
        // Simulate failed verification
        Cache::put($identifier . ':recaptcha_failed', 3, now()->addHours(1));
        
        $failedCount = Cache::get($identifier . ':recaptcha_failed', 0);
        $this->assertEquals(3, $failedCount);
        
        // Reset should clear failed count
        $middleware->resetSecurity($identifier);
        $failedCount = Cache::get($identifier . ':recaptcha_failed', 0);
        $this->assertEquals(0, $failedCount);
    }

    /** @test */
    public function it_tracks_verification_history()
    {
        $middleware = new RecaptchaMiddleware();
        $identifier = 'recaptcha_mobile:0512345678';
        
        // Add verification history
        $history = [
            [
                'timestamp' => time(),
                'score' => 0.8,
                'action' => 'login',
                'success' => true
            ],
            [
                'timestamp' => time() - 3600,
                'score' => 0.3,
                'action' => 'register',
                'success' => false
            ]
        ];
        
        Cache::put($identifier . ':recaptcha_history', $history, now()->addDays(30));
        
        $stats = $middleware->getSecurityStats($identifier);
        $this->assertCount(2, $stats['recaptcha_history']);
        $this->assertEquals(0.8, $stats['recaptcha_history'][0]['score']);
        $this->assertEquals('login', $stats['recaptcha_history'][0]['action']);
    }

    protected function tearDown(): void
    {
        Cache::flush();
        parent::tearDown();
    }
}