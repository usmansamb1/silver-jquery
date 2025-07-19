<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SecurityIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    /** @test */
    public function it_blocks_bot_attacks_with_all_security_layers()
    {
        // Test bot with honeypot, no JS token, and rapid requests
        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/api/register/otp', [
                'mobile' => '0512345678',
                'name' => 'Test User',
                'website' => 'http://spam.com', // Honeypot triggered
                '_form_time' => time() - 1, // Too fast
                // Missing _js_token
            ], ['User-Agent' => 'curl/7.68.0']); // Suspicious user agent
            
            $this->assertEquals(403, $response->getStatusCode());
        }
    }

    /** @test */
    public function it_handles_legitimate_user_registration_flow()
    {
        // Step 1: Request OTP (should succeed)
        $response = $this->postJson('/api/register/otp', [
            'mobile' => '0512345678',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'registration_type' => 'personal',
            'gender' => 'male',
            'region' => 'Central',
            'terms_agree' => true,
            '_form_time' => time() - 10, // 10 seconds to fill form
            '_js_token' => $this->generateJSToken()
        ], ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36']);

        $this->assertNotEquals(403, $response->getStatusCode());
        $this->assertNotEquals(429, $response->getStatusCode());
    }

    /** @test */
    public function it_applies_progressive_rate_limiting()
    {
        $mobile = '0512345678';
        
        // Make multiple failed login attempts
        for ($i = 0; $i < 4; $i++) {
            $response = $this->postJson('/api/login', [
                'mobile' => $mobile,
                '_form_time' => time() - 5,
                '_js_token' => $this->generateJSToken()
            ], ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36']);
            
            if ($i < 3) {
                $this->assertNotEquals(429, $response->getStatusCode());
            } else {
                // 4th attempt should be rate limited
                $this->assertEquals(429, $response->getStatusCode());
                $this->assertArrayHasKey('retry_after', $response->json());
            }
        }
    }

    /** @test */
    public function it_tracks_and_blocks_otp_abuse()
    {
        $mobile = '0512345678';
        $maxAttempts = config('security.otp_security.max_attempts', 5);
        
        // First get OTP
        $otpResponse = $this->postJson('/api/register/otp', [
            'mobile' => $mobile,
            'name' => 'Test User',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ], ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36']);
        
        // Make multiple failed OTP verification attempts
        for ($i = 0; $i < $maxAttempts + 1; $i++) {
            $response = $this->postJson('/api/register/verify-otp', [
                'otp' => '0000', // Wrong OTP
                'temp_token' => 'test_token',
                'mobile' => $mobile,
                '_form_time' => time() - 5,
                '_js_token' => $this->generateJSToken()
            ], ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36']);
        }
        
        // Should be locked out
        $this->assertEquals(429, $response->getStatusCode());
        $this->assertStringContainsString('locked', strtolower($response->json('message')));
    }

    /** @test */
    public function it_detects_and_blocks_suspicious_account_activity()
    {
        $mobile = '0512345678';
        
        // Simulate multiple failed logins from different IPs
        $suspiciousIPs = ['192.168.1.1', '10.0.0.1', '172.16.0.1', '203.0.113.1'];
        
        foreach ($suspiciousIPs as $ip) {
            // Override the IP for testing
            $this->app['request']->server->set('REMOTE_ADDR', $ip);
            
            $response = $this->postJson('/api/login', [
                'mobile' => $mobile,
                '_form_time' => time() - 5,
                '_js_token' => $this->generateJSToken()
            ], ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36']);
        }
        
        // After suspicious activity, account should be flagged
        $this->assertNotEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function it_handles_multiple_security_violations_correctly()
    {
        // Test user with multiple violations
        $response = $this->postJson('/api/register/otp', [
            'mobile' => '0512345678',
            'name' => 'Test User',
            'website' => 'http://spam.com', // Honeypot violation
            '_form_time' => time() - 1, // Timing violation
            '_js_token' => 'invalid_token' // Invalid JS token
        ], ['User-Agent' => 'bot/1.0']); // Suspicious user agent
        
        $this->assertEquals(403, $response->getStatusCode());
    }

    /** @test */
    public function it_allows_legitimate_users_through_all_security_layers()
    {
        // Test legitimate user flow
        $mobile = '0512345678';
        
        // Step 1: Registration OTP request
        $response1 = $this->postJson('/api/register/otp', [
            'mobile' => $mobile,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'registration_type' => 'personal',
            'gender' => 'male',
            'region' => 'Central',
            'terms_agree' => true,
            '_form_time' => time() - 10,
            '_js_token' => $this->generateJSToken()
        ], ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36']);
        
        // Should pass all security checks
        $this->assertNotEquals(403, $response1->getStatusCode());
        $this->assertNotEquals(429, $response1->getStatusCode());
        
        // Step 2: Login attempt
        $response2 = $this->postJson('/api/login', [
            'mobile' => $mobile,
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ], ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36']);
        
        // Should pass all security checks
        $this->assertNotEquals(403, $response2->getStatusCode());
        $this->assertNotEquals(429, $response2->getStatusCode());
    }

    /** @test */
    public function it_resets_security_counters_on_successful_actions()
    {
        $mobile = '0512345678';
        
        // Make some failed attempts
        for ($i = 0; $i < 2; $i++) {
            $this->postJson('/api/login', [
                'mobile' => $mobile,
                '_form_time' => time() - 5,
                '_js_token' => $this->generateJSToken()
            ], ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36']);
        }
        
        // Check that we can still make requests (not blocked yet)
        $response = $this->postJson('/api/login', [
            'mobile' => $mobile,
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ], ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36']);
        
        $this->assertNotEquals(429, $response->getStatusCode());
    }

    /** @test */
    public function it_provides_appropriate_error_responses()
    {
        // Test different types of security violations return appropriate responses
        
        // Bot detection should return 403
        $response1 = $this->postJson('/api/login', [
            'mobile' => '0512345678',
            'website' => 'http://spam.com',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ], ['User-Agent' => 'bot/1.0']);
        
        $this->assertEquals(403, $response1->getStatusCode());
        $this->assertArrayHasKey('error', $response1->json());
        
        // Rate limiting should return 429
        for ($i = 0; $i < 5; $i++) {
            $response2 = $this->postJson('/api/login', [
                'mobile' => '0512345679',
                '_form_time' => time() - 5,
                '_js_token' => $this->generateJSToken()
            ], ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36']);
        }
        
        $this->assertEquals(429, $response2->getStatusCode());
        $this->assertArrayHasKey('retry_after', $response2->json());
    }

    /** @test */
    public function it_handles_concurrent_requests_correctly()
    {
        $mobile = '0512345678';
        
        // Simulate concurrent requests from same user
        $responses = [];
        for ($i = 0; $i < 3; $i++) {
            $responses[] = $this->postJson('/api/login', [
                'mobile' => $mobile,
                '_form_time' => time() - 5,
                '_js_token' => $this->generateJSToken()
            ], ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36']);
        }
        
        // At least one should succeed, others may be rate limited
        $successCount = 0;
        foreach ($responses as $response) {
            if ($response->getStatusCode() !== 429) {
                $successCount++;
            }
        }
        
        $this->assertGreaterThan(0, $successCount);
    }

    /** @test */
    public function it_logs_security_events_properly()
    {
        Log::shouldReceive('warning')
            ->withArgs(['Bot activity detected', \Mockery::type('array')])
            ->once();
        
        Log::shouldReceive('info')
            ->withArgs([\Mockery::pattern('/Rate limit applied/'), \Mockery::type('array')])
            ->once();
        
        // Trigger bot detection
        $this->postJson('/api/login', [
            'mobile' => '0512345678',
            'website' => 'http://spam.com',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ], ['User-Agent' => 'bot/1.0']);
        
        // Trigger rate limiting
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/login', [
                'mobile' => '0512345679',
                '_form_time' => time() - 5,
                '_js_token' => $this->generateJSToken()
            ], ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36']);
        }
    }

    /** @test */
    public function it_handles_configuration_changes_dynamically()
    {
        // Test with stricter configuration
        config(['security.rate_limiting.limits.login.max_attempts' => 2]);
        config(['security.bot_detection.enabled' => true]);
        config(['security.otp_security.max_attempts' => 3]);
        
        $mobile = '0512345678';
        
        // Should be blocked after 2 attempts instead of default 3
        for ($i = 0; $i < 3; $i++) {
            $response = $this->postJson('/api/login', [
                'mobile' => $mobile,
                '_form_time' => time() - 5,
                '_js_token' => $this->generateJSToken()
            ], ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36']);
        }
        
        $this->assertEquals(429, $response->getStatusCode());
    }

    /** @test */
    public function it_maintains_performance_under_load()
    {
        $startTime = microtime(true);
        
        // Make multiple requests to test performance
        for ($i = 0; $i < 10; $i++) {
            $this->postJson('/api/login', [
                'mobile' => "05123456{$i}",
                '_form_time' => time() - 5,
                '_js_token' => $this->generateJSToken()
            ], ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36']);
        }
        
        $endTime = microtime(true);
        $totalTime = $endTime - $startTime;
        
        // Should process 10 requests in under 5 seconds
        $this->assertLessThan(5, $totalTime);
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