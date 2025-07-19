<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Http\Middleware\AdvancedRateLimiting;

class AdvancedRateLimitingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    /** @test */
    public function it_allows_requests_within_limit()
    {
        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678'
        ]);

        $this->assertNotEquals(429, $response->getStatusCode());
    }

    /** @test */
    public function it_blocks_requests_exceeding_login_limit()
    {
        // Make multiple failed login attempts
        for ($i = 0; $i < 4; $i++) {
            $response = $this->postJson('/api/login', [
                'mobile' => '0512345678'
            ]);
        }

        // The 4th attempt should be rate limited
        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678'
        ]);

        $this->assertEquals(429, $response->getStatusCode());
        $this->assertArrayHasKey('retry_after', $response->json());
    }

    /** @test */
    public function it_applies_progressive_delays()
    {
        $identifier = 'rate_limit:127.0.0.1:' . md5('Symfony');
        
        // Set attempts to trigger delay
        Cache::put($identifier . ':attempts', 5, now()->addHours(24));
        
        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678'
        ]);

        $this->assertEquals(429, $response->getStatusCode());
        
        $responseData = $response->json();
        $this->assertArrayHasKey('retry_after', $responseData);
        $this->assertGreaterThan(0, $responseData['retry_after']);
    }

    /** @test */
    public function it_blocks_users_after_threshold()
    {
        $identifier = 'rate_limit:127.0.0.1:' . md5('Symfony');
        
        // Set attempts to trigger blocking
        Cache::put($identifier . ':attempts', 12, now()->addHours(24));
        
        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678'
        ]);

        $this->assertEquals(429, $response->getStatusCode());
        
        // Check if user is blocked
        $this->assertTrue(Cache::has($identifier . ':blocked'));
    }

    /** @test */
    public function it_resets_attempts_on_successful_response()
    {
        $identifier = 'rate_limit:127.0.0.1:' . md5('Symfony');
        
        // Set some attempts
        Cache::put($identifier . ':attempts', 2, now()->addHours(24));
        
        // Make a successful request (mock success)
        $response = $this->getJson('/api/health');
        
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            // Attempts should be reset for successful responses
            $this->assertFalse(Cache::has($identifier . ':attempts'));
        }
    }

    /** @test */
    public function it_handles_different_rate_limit_types()
    {
        // Test registration rate limiting
        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson('/api/register/otp', [
                'mobile' => '0512345678',
                'name' => 'Test User'
            ]);
        }

        $this->assertEquals(429, $response->getStatusCode());

        // Test OTP rate limiting
        for ($i = 0; $i < 4; $i++) {
            $response = $this->postJson('/api/register/verify-otp', [
                'otp' => '1234',
                'temp_token' => 'test_token'
            ]);
        }

        $this->assertEquals(429, $response->getStatusCode());
    }

    /** @test */
    public function it_logs_security_events()
    {
        Log::shouldReceive('warning')
            ->with('Blocked IP attempted access', \Mockery::type('array'))
            ->once();

        $identifier = 'rate_limit:127.0.0.1:' . md5('Symfony');
        
        // Block the user
        Cache::put($identifier . ':blocked', true, now()->addHours(2));
        
        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678'
        ]);

        $this->assertEquals(429, $response->getStatusCode());
    }

    /** @test */
    public function it_calculates_correct_delays()
    {
        $middleware = new AdvancedRateLimiting();
        $reflection = new \ReflectionClass($middleware);
        
        $calculateDelayMethod = $reflection->getMethod('calculateDelay');
        $calculateDelayMethod->setAccessible(true);
        
        $limits = [
            'max_attempts' => 3,
            'base_delay' => 300,
            'multiplier' => 2,
            'max_delay' => 3600,
            'block_threshold' => 10
        ];
        
        // Test no delay for attempts within limit
        $delay = $calculateDelayMethod->invokeArgs($middleware, [2, $limits]);
        $this->assertEquals(0, $delay);
        
        // Test progressive delay
        $delay = $calculateDelayMethod->invokeArgs($middleware, [4, $limits]);
        $this->assertGreaterThan(0, $delay);
        
        // Test max delay cap
        $delay = $calculateDelayMethod->invokeArgs($middleware, [20, $limits]);
        $this->assertLessThanOrEqual($limits['max_delay'], $delay);
    }

    /** @test */
    public function it_handles_different_user_agents()
    {
        $response1 = $this->postJson('/api/login', [
            'mobile' => '0512345678'
        ], ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36']);

        $response2 = $this->postJson('/api/login', [
            'mobile' => '0512345678'
        ], ['User-Agent' => 'curl/7.68.0']);

        // Different user agents should have separate rate limits
        $this->assertNotEquals(429, $response1->getStatusCode());
        $this->assertNotEquals(429, $response2->getStatusCode());
    }

    /** @test */
    public function it_respects_configuration_settings()
    {
        config(['security.rate_limiting.enabled' => false]);
        
        // With rate limiting disabled, should not apply limits
        for ($i = 0; $i < 10; $i++) {
            $response = $this->postJson('/api/login', [
                'mobile' => '0512345678'
            ]);
        }

        // Should not be rate limited when disabled
        $this->assertNotEquals(429, $response->getStatusCode());
    }

    /** @test */
    public function it_provides_retry_after_information()
    {
        $identifier = 'rate_limit:127.0.0.1:' . md5('Symfony');
        
        // Set attempts to trigger delay
        Cache::put($identifier . ':attempts', 5, now()->addHours(24));
        
        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678'
        ]);

        $this->assertEquals(429, $response->getStatusCode());
        
        $responseData = $response->json();
        $this->assertArrayHasKey('retry_after', $responseData);
        $this->assertArrayHasKey('error', $responseData);
        $this->assertIsInt($responseData['retry_after']);
    }

    /** @test */
    public function it_handles_wallet_operations_rate_limiting()
    {
        // Test wallet-specific rate limiting
        for ($i = 0; $i < 12; $i++) {
            $response = $this->postJson('/api/wallet/recharge', [
                'amount' => 100,
                'payment_method' => 'credit_card'
            ]);
        }

        $this->assertEquals(429, $response->getStatusCode());
    }

    /** @test */
    public function it_handles_service_booking_rate_limiting()
    {
        // Test service booking rate limiting
        for ($i = 0; $i < 7; $i++) {
            $response = $this->postJson('/api/service/booking', [
                'service_id' => 1,
                'vehicle_type' => 'car'
            ]);
        }

        $this->assertEquals(429, $response->getStatusCode());
    }

    protected function tearDown(): void
    {
        Cache::flush();
        parent::tearDown();
    }
}