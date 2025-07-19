<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use App\Http\Middleware\RecaptchaMiddleware;

class RecaptchaMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        
        // Set up reCAPTCHA configuration for testing
        Config::set('security.recaptcha.enabled', true);
        Config::set('security.recaptcha.site_key', 'test_site_key');
        Config::set('security.recaptcha.secret_key', 'test_secret_key');
        Config::set('security.recaptcha.minimum_score', 0.5);
    }

    /** @test */
    public function it_allows_requests_when_recaptcha_is_disabled()
    {
        Config::set('security.recaptcha.enabled', false);
        
        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ]);
        
        // Should not be blocked by reCAPTCHA when disabled
        $this->assertNotEquals(403, $response->getStatusCode());
    }

    /** @test */
    public function it_allows_get_requests_without_recaptcha()
    {
        $response = $this->getJson('/api/health');
        
        // GET requests should not require reCAPTCHA
        $this->assertNotEquals(403, $response->getStatusCode());
    }

    /** @test */
    public function it_blocks_requests_without_recaptcha_token_when_triggered()
    {
        // Trigger reCAPTCHA requirement by simulating failed attempts
        $this->simulateFailedAttempts('recaptcha_mobile:0512345678', 5);
        
        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
            // Missing g-recaptcha-response
        ]);
        
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertStringContainsString('reCAPTCHA token is required', $response->json('message'));
    }

    /** @test */
    public function it_verifies_recaptcha_token_with_google_api()
    {
        // Mock successful reCAPTCHA verification
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.8,
                'action' => 'login',
                'challenge_ts' => now()->toISOString(),
                'hostname' => 'example.com'
            ])
        ]);
        
        // Trigger reCAPTCHA requirement
        $this->simulateFailedAttempts('recaptcha_mobile:0512345678', 5);
        
        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678',
            'g-recaptcha-response' => 'valid_token',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ]);
        
        // Should pass reCAPTCHA verification
        $this->assertNotEquals(403, $response->getStatusCode());
    }

    /** @test */
    public function it_blocks_requests_with_low_recaptcha_score()
    {
        // Mock reCAPTCHA with low score
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.2, // Below threshold
                'action' => 'login',
                'challenge_ts' => now()->toISOString(),
                'hostname' => 'example.com'
            ])
        ]);
        
        // Trigger reCAPTCHA requirement
        $this->simulateFailedAttempts('recaptcha_mobile:0512345678', 5);
        
        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678',
            'g-recaptcha-response' => 'low_score_token',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ]);
        
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertStringContainsString('reCAPTCHA score too low', $response->json('message'));
    }

    /** @test */
    public function it_blocks_requests_with_action_mismatch()
    {
        // Mock reCAPTCHA with wrong action
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.8,
                'action' => 'register', // Wrong action
                'challenge_ts' => now()->toISOString(),
                'hostname' => 'example.com'
            ])
        ]);
        
        // Trigger reCAPTCHA requirement
        $this->simulateFailedAttempts('recaptcha_mobile:0512345678', 5);
        
        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678',
            'g-recaptcha-response' => 'wrong_action_token',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ]);
        
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertStringContainsString('reCAPTCHA action mismatch', $response->json('message'));
    }

    /** @test */
    public function it_triggers_recaptcha_for_suspicious_behavior()
    {
        // Mark account as suspended (suspicious behavior)
        Cache::put('account_mobile:0512345678:suspended', true, now()->addHours(1));
        
        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
            // Missing reCAPTCHA token
        ]);
        
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertStringContainsString('reCAPTCHA token is required', $response->json('message'));
    }

    /** @test */
    public function it_triggers_recaptcha_for_bot_detection()
    {
        // Mark as bot detected
        Cache::put('bot_mobile:0512345678:detected', true, now()->addHours(1));
        
        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
            // Missing reCAPTCHA token
        ]);
        
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertStringContainsString('reCAPTCHA token is required', $response->json('message'));
    }

    /** @test */
    public function it_triggers_recaptcha_for_new_ip_when_enabled()
    {
        Config::set('security.recaptcha.triggers.new_ip', true);
        
        // Set up previous IP data
        Cache::put('account_mobile:0512345678:recent_ips', [
            ['ip' => '192.168.1.1', 'timestamp' => time() - 3600]
        ], now()->addDays(30));
        
        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
            // Missing reCAPTCHA token, new IP should trigger reCAPTCHA
        ]);
        
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertStringContainsString('reCAPTCHA token is required', $response->json('message'));
    }

    /** @test */
    public function it_handles_recaptcha_api_failures_gracefully()
    {
        // Mock API failure
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response(null, 500)
        ]);
        
        Config::set('security.recaptcha.bypass_on_failure', true);
        
        // Trigger reCAPTCHA requirement
        $this->simulateFailedAttempts('recaptcha_mobile:0512345678', 5);
        
        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678',
            'g-recaptcha-response' => 'test_token',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ]);
        
        // Should bypass when configured to do so
        $this->assertNotEquals(403, $response->getStatusCode());
    }

    /** @test */
    public function it_stores_verification_history()
    {
        // Mock successful reCAPTCHA verification
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.8,
                'action' => 'login',
                'challenge_ts' => now()->toISOString(),
                'hostname' => 'example.com'
            ])
        ]);
        
        // Trigger reCAPTCHA requirement
        $this->simulateFailedAttempts('recaptcha_mobile:0512345678', 5);
        
        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678',
            'g-recaptcha-response' => 'valid_token',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ]);
        
        // Check that verification history is stored
        $history = Cache::get('recaptcha_mobile:0512345678:recaptcha_history', []);
        $this->assertNotEmpty($history);
        $this->assertEquals(0.8, $history[0]['score']);
        $this->assertEquals('login', $history[0]['action']);
    }

    /** @test */
    public function it_updates_success_rate_statistics()
    {
        // Mock successful reCAPTCHA verification
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.8,
                'action' => 'login',
                'challenge_ts' => now()->toISOString(),
                'hostname' => 'example.com'
            ])
        ]);
        
        // Trigger reCAPTCHA requirement
        $this->simulateFailedAttempts('recaptcha_mobile:0512345678', 5);
        
        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678',
            'g-recaptcha-response' => 'valid_token',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ]);
        
        // Check that statistics are updated
        $stats = Cache::get('recaptcha_mobile:0512345678:recaptcha_stats', []);
        $this->assertEquals(1, $stats['total']);
        $this->assertEquals(1, $stats['successful']);
        $this->assertEquals(0, $stats['failed']);
    }

    /** @test */
    public function it_tracks_failed_verification_attempts()
    {
        // Mock failed reCAPTCHA verification
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => false,
                'error-codes' => ['invalid-input-response']
            ])
        ]);
        
        // Trigger reCAPTCHA requirement
        $this->simulateFailedAttempts('recaptcha_mobile:0512345678', 5);
        
        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678',
            'g-recaptcha-response' => 'invalid_token',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ]);
        
        $this->assertEquals(403, $response->getStatusCode());
        
        // Check that failed attempts are tracked
        $failedCount = Cache::get('recaptcha_mobile:0512345678:recaptcha_failed', 0);
        $this->assertEquals(1, $failedCount);
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
    public function it_handles_different_score_thresholds_per_action()
    {
        Config::set('security.recaptcha.score_thresholds.login', 0.7);
        Config::set('security.recaptcha.score_thresholds.register', 0.3);
        
        // Mock reCAPTCHA with score 0.5 (below login threshold, above register threshold)
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.5,
                'action' => 'login',
                'challenge_ts' => now()->toISOString(),
                'hostname' => 'example.com'
            ])
        ]);
        
        // Trigger reCAPTCHA requirement
        $this->simulateFailedAttempts('recaptcha_mobile:0512345678', 5);
        
        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678',
            'g-recaptcha-response' => 'medium_score_token',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ]);
        
        // Should be blocked due to low score for login action
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertStringContainsString('reCAPTCHA score too low', $response->json('message'));
    }

    /**
     * Simulate failed attempts to trigger reCAPTCHA
     */
    protected function simulateFailedAttempts(string $identifier, int $count): void
    {
        Cache::put($identifier . ':failed_attempts', $count, now()->addHours(1));
        Config::set('security.recaptcha.triggers.failed_attempts', $count - 1);
    }

    /**
     * Generate a JavaScript token for testing
     */
    protected function generateJSToken(): string
    {
        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36';
        $timestamp = date('Y-m-d-H');
        $data = '127.0.0.1' . $userAgent . $timestamp;
        return hash('sha256', $data);
    }

    protected function tearDown(): void
    {
        Cache::flush();
        parent::tearDown();
    }
}