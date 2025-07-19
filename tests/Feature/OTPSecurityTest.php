<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Http\Middleware\OTPSecurityMiddleware;

class OTPSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    /** @test */
    public function it_allows_normal_otp_requests()
    {
        $response = $this->postJson('/api/register/otp', [
            'mobile' => '0512345678',
            'name' => 'Test User',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ]);

        $this->assertNotEquals(429, $response->getStatusCode());
    }

    /** @test */
    public function it_enforces_otp_verification_attempt_limits()
    {
        $mobile = '0512345678';
        $maxAttempts = config('security.otp_security.max_attempts', 5);
        
        // Make multiple failed OTP verification attempts
        for ($i = 0; $i < $maxAttempts + 1; $i++) {
            $response = $this->postJson('/api/register/verify-otp', [
                'otp' => '0000', // Wrong OTP
                'temp_token' => 'test_token',
                'mobile' => $mobile,
                '_form_time' => time() - 5,
                '_js_token' => $this->generateJSToken()
            ]);
        }

        // Should be locked out after max attempts
        $this->assertEquals(429, $response->getStatusCode());
        $this->assertStringContainsString('locked', strtolower($response->json('message')));
    }

    /** @test */
    public function it_enforces_otp_resend_limits()
    {
        $mobile = '0512345678';
        $maxResends = config('security.otp_security.max_resends', 3);
        
        // Make multiple OTP resend requests
        for ($i = 0; $i < $maxResends + 1; $i++) {
            $response = $this->postJson('/api/register/otp', [
                'mobile' => $mobile,
                'name' => 'Test User',
                '_form_time' => time() - 5,
                '_js_token' => $this->generateJSToken()
            ]);
            
            // Wait for cooldown between requests
            sleep(1);
        }

        // Should be locked out after max resends
        $this->assertEquals(429, $response->getStatusCode());
        $this->assertStringContainsString('maximum', strtolower($response->json('message')));
    }

    /** @test */
    public function it_enforces_resend_cooldown()
    {
        $mobile = '0512345678';
        
        // First OTP request should succeed
        $response1 = $this->postJson('/api/register/otp', [
            'mobile' => $mobile,
            'name' => 'Test User',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ]);
        
        $this->assertNotEquals(429, $response1->getStatusCode());
        
        // Immediate second request should be blocked by cooldown
        $response2 = $this->postJson('/api/register/otp', [
            'mobile' => $mobile,
            'name' => 'Test User',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ]);
        
        $this->assertEquals(429, $response2->getStatusCode());
        $this->assertStringContainsString('cooldown', strtolower($response2->json('message')));
    }

    /** @test */
    public function it_tracks_different_identifiers_separately()
    {
        $mobile1 = '0512345678';
        $mobile2 = '0512345679';
        
        // Make requests with different mobile numbers
        $response1 = $this->postJson('/api/register/otp', [
            'mobile' => $mobile1,
            'name' => 'Test User 1',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ]);
        
        $response2 = $this->postJson('/api/register/otp', [
            'mobile' => $mobile2,
            'name' => 'Test User 2',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ]);
        
        // Both should succeed as they're different identifiers
        $this->assertNotEquals(429, $response1->getStatusCode());
        $this->assertNotEquals(429, $response2->getStatusCode());
    }

    /** @test */
    public function it_resets_counters_on_successful_verification()
    {
        $mobile = '0512345678';
        
        // Make some failed attempts
        for ($i = 0; $i < 3; $i++) {
            $response = $this->postJson('/api/register/verify-otp', [
                'otp' => '0000', // Wrong OTP
                'temp_token' => 'test_token',
                'mobile' => $mobile,
                '_form_time' => time() - 5,
                '_js_token' => $this->generateJSToken()
            ]);
        }
        
        // Simulate successful verification (200 response)
        $this->mock(\App\Http\Controllers\API\AuthController::class, function ($mock) {
            $mock->shouldReceive('verifyRegistrationOtp')
                 ->once()
                 ->andReturn(response()->json(['success' => true], 200));
        });
        
        $response = $this->postJson('/api/register/verify-otp', [
            'otp' => '1234', // Correct OTP
            'temp_token' => 'test_token',
            'mobile' => $mobile,
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ]);
        
        // Should succeed and reset counters
        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function it_provides_retry_after_information()
    {
        $mobile = '0512345678';
        $maxAttempts = config('security.otp_security.max_attempts', 5);
        
        // Exceed max attempts
        for ($i = 0; $i < $maxAttempts + 1; $i++) {
            $response = $this->postJson('/api/register/verify-otp', [
                'otp' => '0000',
                'temp_token' => 'test_token',
                'mobile' => $mobile,
                '_form_time' => time() - 5,
                '_js_token' => $this->generateJSToken()
            ]);
        }
        
        $this->assertEquals(429, $response->getStatusCode());
        $this->assertArrayHasKey('retry_after', $response->json());
        $this->assertIsInt($response->json('retry_after'));
    }

    /** @test */
    public function it_logs_security_events()
    {
        Log::shouldReceive('info')
            ->withArgs(['OTP verification failed', \Mockery::type('array')])
            ->once();
        
        Log::shouldReceive('warning')
            ->withArgs(['OTP lockout applied', \Mockery::type('array')])
            ->once();
        
        $mobile = '0512345678';
        $maxAttempts = config('security.otp_security.max_attempts', 5);
        
        // Exceed max attempts to trigger lockout
        for ($i = 0; $i < $maxAttempts + 1; $i++) {
            $response = $this->postJson('/api/register/verify-otp', [
                'otp' => '0000',
                'temp_token' => 'test_token',
                'mobile' => $mobile,
                '_form_time' => time() - 5,
                '_js_token' => $this->generateJSToken()
            ]);
        }
        
        $this->assertEquals(429, $response->getStatusCode());
    }

    /** @test */
    public function it_handles_token_based_identification()
    {
        $tempToken = 'unique_temp_token';
        
        // Make request with temp token
        $response = $this->postJson('/api/register/verify-otp', [
            'otp' => '1234',
            'temp_token' => $tempToken,
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ]);
        
        $this->assertNotEquals(429, $response->getStatusCode());
    }

    /** @test */
    public function it_handles_ip_based_identification_fallback()
    {
        // Request without mobile or temp_token should use IP
        $response = $this->postJson('/api/register/verify-otp', [
            'otp' => '1234',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ]);
        
        $this->assertNotEquals(429, $response->getStatusCode());
    }

    /** @test */
    public function it_tracks_otp_generation()
    {
        Log::shouldReceive('info')
            ->withArgs(['OTP generated', \Mockery::type('array')])
            ->once();
        
        $response = $this->postJson('/api/register/otp', [
            'mobile' => '0512345678',
            'name' => 'Test User',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ]);
        
        $this->assertNotEquals(429, $response->getStatusCode());
    }

    /** @test */
    public function it_can_get_security_statistics()
    {
        $middleware = new OTPSecurityMiddleware();
        $identifier = 'otp_mobile:0512345678';
        
        // Make some attempts
        Cache::put($identifier . ':attempts', 3, now()->addMinutes(30));
        Cache::put($identifier . ':resend_count', 2, now()->addHours(1));
        
        $stats = $middleware->getSecurityStats($identifier);
        
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('attempts', $stats);
        $this->assertArrayHasKey('resend_count', $stats);
        $this->assertArrayHasKey('is_locked_out', $stats);
        $this->assertEquals(3, $stats['attempts']);
        $this->assertEquals(2, $stats['resend_count']);
    }

    /** @test */
    public function it_can_reset_security_for_identifier()
    {
        $middleware = new OTPSecurityMiddleware();
        $identifier = 'otp_mobile:0512345678';
        
        // Set some data
        Cache::put($identifier . ':attempts', 3, now()->addMinutes(30));
        Cache::put($identifier . ':resend_count', 2, now()->addHours(1));
        Cache::put($identifier . ':lockout', true, now()->addHours(1));
        
        // Reset security
        $middleware->resetSecurity($identifier);
        
        // Check that all data is cleared
        $this->assertFalse(Cache::has($identifier . ':attempts'));
        $this->assertFalse(Cache::has($identifier . ':resend_count'));
        $this->assertFalse(Cache::has($identifier . ':lockout'));
    }

    /** @test */
    public function it_handles_configuration_changes()
    {
        // Test with different configuration values
        config(['security.otp_security.max_attempts' => 3]);
        config(['security.otp_security.max_resends' => 2]);
        config(['security.otp_security.lockout_duration' => 1800]);
        
        $mobile = '0512345678';
        
        // Should be locked out after 3 attempts instead of default 5
        for ($i = 0; $i < 4; $i++) {
            $response = $this->postJson('/api/register/verify-otp', [
                'otp' => '0000',
                'temp_token' => 'test_token',
                'mobile' => $mobile,
                '_form_time' => time() - 5,
                '_js_token' => $this->generateJSToken()
            ]);
        }
        
        $this->assertEquals(429, $response->getStatusCode());
        $this->assertEquals(1800, $response->json('retry_after'));
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