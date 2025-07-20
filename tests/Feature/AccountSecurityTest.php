<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Http\Middleware\AccountSecurityMiddleware;

class AccountSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    /** @test */
    public function it_allows_normal_account_access()
    {
        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ], ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36']);

        $this->assertNotEquals(403, $response->getStatusCode());
    }

    /** @test */
    public function it_blocks_suspended_accounts()
    {
        $identifier = 'account_mobile:0512345678';
        
        // Manually suspend the account
        $suspensionData = [
            'reason' => 'manual_suspension',
            'applied_at' => now()->toISOString(),
            'expires_at' => now()->addHours(1)->toISOString(),
            'remaining_time' => 3600,
            'ip' => '127.0.0.1'
        ];
        
        Cache::put($identifier . ':suspended', true, now()->addHours(1));
        Cache::put($identifier . ':suspension_data', $suspensionData, now()->addHours(1));
        
        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ], ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36']);

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertStringContainsString('suspended', strtolower($response->json('message')));
    }

    /** @test */
    public function it_detects_multiple_failed_logins_from_different_ips()
    {
        $identifier = 'account_mobile:0512345678';
        
        // Simulate multiple failed logins from different IPs
        $failedLogins = [
            ['ip' => '192.168.1.1', 'timestamp' => time() - 1800],
            ['ip' => '10.0.0.1', 'timestamp' => time() - 1200],
            ['ip' => '172.16.0.1', 'timestamp' => time() - 600],
            ['ip' => '203.0.113.1', 'timestamp' => time() - 300],
            ['ip' => '198.51.100.1', 'timestamp' => time() - 100]
        ];
        
        Cache::put($identifier . ':failed_logins', $failedLogins, now()->addHours(24));
        
        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ], ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36']);

        // Should detect suspicious activity
        $this->assertNotEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function it_detects_rapid_location_changes()
    {
        $identifier = 'account_mobile:0512345678';
        
        // Simulate rapid IP changes
        $recentIPs = [
            ['ip' => '192.168.1.1', 'timestamp' => time() - 600],
            ['ip' => '10.0.0.1', 'timestamp' => time() - 300],
            ['ip' => '172.16.0.1', 'timestamp' => time() - 100]
        ];
        
        Cache::put($identifier . ':recent_ips', $recentIPs, now()->addDays(30));
        
        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ], ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36']);

        // Should detect rapid location changes
        $this->assertNotEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function it_detects_unusual_time_patterns()
    {
        $identifier = 'account_mobile:0512345678';
        
        // Simulate unusual login times (all during 2-6 AM)
        $loginTimes = [2, 3, 4, 5, 3, 2, 4];
        
        Cache::put($identifier . ':login_times', $loginTimes, now()->addDays(7));
        
        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ], ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36']);

        // Should detect unusual time patterns
        $this->assertNotEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function it_detects_multiple_concurrent_sessions()
    {
        $identifier = 'account_mobile:0512345678';
        
        // Simulate multiple active sessions
        $activeSessions = [
            'session1' => ['ip' => '192.168.1.1', 'last_activity' => time() - 100],
            'session2' => ['ip' => '10.0.0.1', 'last_activity' => time() - 200],
            'session3' => ['ip' => '172.16.0.1', 'last_activity' => time() - 300],
            'session4' => ['ip' => '203.0.113.1', 'last_activity' => time() - 400],
        ];
        
        Cache::put($identifier . ':active_sessions', $activeSessions, now()->addHours(2));
        
        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ], ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36']);

        // Should detect multiple concurrent sessions
        $this->assertNotEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function it_calculates_suspicion_levels_correctly()
    {
        $middleware = new AccountSecurityMiddleware();
        $identifier = 'account_mobile:0512345678';
        
        // Set up conditions for high suspicion
        $failedLogins = [
            ['ip' => '192.168.1.1', 'timestamp' => time() - 1800],
            ['ip' => '10.0.0.1', 'timestamp' => time() - 1200],
            ['ip' => '172.16.0.1', 'timestamp' => time() - 600],
            ['ip' => '203.0.113.1', 'timestamp' => time() - 300],
            ['ip' => '198.51.100.1', 'timestamp' => time() - 100]
        ];
        
        $recentIPs = [
            ['ip' => '192.168.1.1', 'timestamp' => time() - 600],
            ['ip' => '10.0.0.1', 'timestamp' => time() - 100]
        ];
        
        $loginTimes = [2, 3, 4, 5, 3];
        
        Cache::put($identifier . ':failed_logins', $failedLogins, now()->addHours(24));
        Cache::put($identifier . ':recent_ips', $recentIPs, now()->addDays(30));
        Cache::put($identifier . ':login_times', $loginTimes, now()->addDays(7));
        
        // Override request IP for testing
        $this->app['request']->server->set('REMOTE_ADDR', '172.16.0.1');
        
        $stats = $middleware->getSecurityStats($identifier);
        
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('suspicion_level', $stats);
        $this->assertGreaterThan(0, $stats['suspicion_level']);
    }

    /** @test */
    public function it_applies_appropriate_suspensions()
    {
        $identifier = 'account_mobile:0512345678';
        
        // Set up high suspicion conditions
        $failedLogins = [
            ['ip' => '192.168.1.1', 'timestamp' => time() - 1800],
            ['ip' => '10.0.0.1', 'timestamp' => time() - 1200],
            ['ip' => '172.16.0.1', 'timestamp' => time() - 600],
            ['ip' => '203.0.113.1', 'timestamp' => time() - 300],
            ['ip' => '198.51.100.1', 'timestamp' => time() - 100]
        ];
        
        $recentIPs = [
            ['ip' => '192.168.1.1', 'timestamp' => time() - 600],
            ['ip' => '10.0.0.1', 'timestamp' => time() - 100]
        ];
        
        $loginTimes = [2, 3, 4, 5, 3];
        
        Cache::put($identifier . ':failed_logins', $failedLogins, now()->addHours(24));
        Cache::put($identifier . ':recent_ips', $recentIPs, now()->addDays(30));
        Cache::put($identifier . ':login_times', $loginTimes, now()->addDays(7));
        
        // Override request IP for testing
        $this->app['request']->server->set('REMOTE_ADDR', '172.16.0.1');
        
        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ], ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36']);

        // Should be suspended due to high suspicion
        $this->assertEquals(403, $response->getStatusCode());
    }

    /** @test */
    public function it_tracks_login_attempts_correctly()
    {
        $identifier = 'account_mobile:0512345678';
        
        // Make failed login attempt
        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ], ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36']);
        
        // Check that failed login is tracked
        $failedLogins = Cache::get($identifier . ':failed_logins', []);
        $this->assertNotEmpty($failedLogins);
    }

    /** @test */
    public function it_tracks_ip_changes_correctly()
    {
        $identifier = 'account_mobile:0512345678';
        
        // Make request from one IP
        $this->app['request']->server->set('REMOTE_ADDR', '192.168.1.1');
        
        $response1 = $this->postJson('/api/login', [
            'mobile' => '0512345678',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ], ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36']);
        
        // Make request from different IP
        $this->app['request']->server->set('REMOTE_ADDR', '10.0.0.1');
        
        $response2 = $this->postJson('/api/login', [
            'mobile' => '0512345678',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ], ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36']);
        
        // Check that IP changes are tracked
        $recentIPs = Cache::get($identifier . ':recent_ips', []);
        $this->assertGreaterThan(1, count($recentIPs));
    }

    /** @test */
    public function it_logs_security_events()
    {
        Log::shouldReceive('info')
            ->withArgs([\Mockery::pattern('/Account security event:/'), \Mockery::type('array')])
            ->atLeast()->once();
        
        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ], ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36']);
    }

    /** @test */
    public function it_provides_security_statistics()
    {
        $middleware = new AccountSecurityMiddleware();
        $identifier = 'account_mobile:0512345678';
        
        // Set up some data
        Cache::put($identifier . ':failed_logins', [['ip' => '127.0.0.1', 'timestamp' => time()]], now()->addHours(24));
        Cache::put($identifier . ':recent_ips', [['ip' => '127.0.0.1', 'timestamp' => time()]], now()->addDays(30));
        
        $stats = $middleware->getSecurityStats($identifier);
        
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('is_suspended', $stats);
        $this->assertArrayHasKey('failed_logins', $stats);
        $this->assertArrayHasKey('recent_ips', $stats);
        $this->assertArrayHasKey('suspicion_level', $stats);
    }

    /** @test */
    public function it_can_reset_security_data()
    {
        $middleware = new AccountSecurityMiddleware();
        $identifier = 'account_mobile:0512345678';
        
        // Set up some data
        Cache::put($identifier . ':failed_logins', [['ip' => '127.0.0.1', 'timestamp' => time()]], now()->addHours(24));
        Cache::put($identifier . ':suspended', true, now()->addHours(1));
        
        // Reset security
        $middleware->resetSecurity($identifier);
        
        // Check that data is cleared
        $this->assertFalse(Cache::has($identifier . ':failed_logins'));
        $this->assertFalse(Cache::has($identifier . ':suspended'));
    }

    /** @test */
    public function it_handles_different_identifier_types()
    {
        // Test with mobile identifier
        $response1 = $this->postJson('/api/login', [
            'mobile' => '0512345678',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ], ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36']);
        
        // Test with email identifier
        $response2 = $this->postJson('/api/register/otp', [
            'email' => 'test@example.com',
            'name' => 'Test User',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ], ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36']);
        
        $this->assertNotEquals(500, $response1->getStatusCode());
        $this->assertNotEquals(500, $response2->getStatusCode());
    }

    /** @test */
    public function it_cleans_up_expired_sessions()
    {
        $identifier = 'account_mobile:0512345678';
        
        // Add expired and active sessions
        $activeSessions = [
            'expired_session' => ['ip' => '192.168.1.1', 'last_activity' => time() - 3600], // 1 hour ago
            'active_session' => ['ip' => '10.0.0.1', 'last_activity' => time() - 100] // 100 seconds ago
        ];
        
        Cache::put($identifier . ':active_sessions', $activeSessions, now()->addHours(2));
        
        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ], ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36']);
        
        // Should clean up expired sessions
        $this->assertNotEquals(500, $response->getStatusCode());
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