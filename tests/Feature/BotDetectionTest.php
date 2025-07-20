<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

class BotDetectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    /** @test */
    public function it_allows_legitimate_requests()
    {
        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ]);

        $this->assertNotEquals(403, $response->getStatusCode());
    }

    /** @test */
    public function it_blocks_honeypot_triggered_requests()
    {
        $response = $this->postJson('/api/register/otp', [
            'mobile' => '0512345678',
            'name' => 'Test User',
            'website' => 'http://spam.com', // Honeypot field filled
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ]);

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertStringContainsString('suspicious activity', strtolower($response->json('message')));
    }

    /** @test */
    public function it_blocks_suspicious_user_agents()
    {
        $suspiciousUserAgents = [
            'curl/7.68.0',
            'python-requests/2.25.1',
            'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
            'Scrapy/2.5.0',
            'automated-tool/1.0'
        ];

        foreach ($suspiciousUserAgents as $userAgent) {
            $response = $this->postJson('/api/login', [
                'mobile' => '0512345678',
                '_form_time' => time() - 5,
                '_js_token' => $this->generateJSToken()
            ], ['User-Agent' => $userAgent]);

            $this->assertEquals(403, $response->getStatusCode(), "Failed to block user agent: {$userAgent}");
        }
    }

    /** @test */
    public function it_allows_legitimate_user_agents()
    {
        $legitimateUserAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0'
        ];

        foreach ($legitimateUserAgents as $userAgent) {
            $response = $this->postJson('/api/login', [
                'mobile' => '0512345678',
                '_form_time' => time() - 5,
                '_js_token' => $this->generateJSToken()
            ], ['User-Agent' => $userAgent]);

            $this->assertNotEquals(403, $response->getStatusCode(), "Incorrectly blocked legitimate user agent: {$userAgent}");
        }
    }

    /** @test */
    public function it_blocks_requests_without_javascript_token()
    {
        $response = $this->postJson('/api/register/otp', [
            'mobile' => '0512345678',
            'name' => 'Test User',
            '_form_time' => time() - 5
            // Missing _js_token
        ]);

        $this->assertEquals(403, $response->getStatusCode());
    }

    /** @test */
    public function it_blocks_requests_with_invalid_javascript_token()
    {
        $response = $this->postJson('/api/register/otp', [
            'mobile' => '0512345678',
            'name' => 'Test User',
            '_form_time' => time() - 5,
            '_js_token' => 'invalid_token'
        ]);

        $this->assertEquals(403, $response->getStatusCode());
    }

    /** @test */
    public function it_blocks_too_fast_form_submissions()
    {
        $response = $this->postJson('/api/register/otp', [
            'mobile' => '0512345678',
            'name' => 'Test User',
            '_form_time' => time() - 1, // Only 1 second to fill form
            '_js_token' => $this->generateJSToken()
        ]);

        $this->assertEquals(403, $response->getStatusCode());
    }

    /** @test */
    public function it_blocks_too_old_form_submissions()
    {
        $response = $this->postJson('/api/register/otp', [
            'mobile' => '0512345678',
            'name' => 'Test User',
            '_form_time' => time() - 4000, // 4000 seconds old
            '_js_token' => $this->generateJSToken()
        ]);

        $this->assertEquals(403, $response->getStatusCode());
    }

    /** @test */
    public function it_detects_automated_behavior_patterns()
    {
        // Make rapid sequential requests
        for ($i = 0; $i < 25; $i++) {
            $response = $this->postJson('/api/login', [
                'mobile' => '0512345678',
                '_form_time' => time() - 5,
                '_js_token' => $this->generateJSToken()
            ]);
        }

        // The last requests should be blocked
        $this->assertEquals(403, $response->getStatusCode());
    }

    /** @test */
    public function it_whitelists_configured_ips()
    {
        config(['security.monitoring.ip_whitelist' => ['127.0.0.1']]);

        // Even with suspicious behavior, whitelisted IPs should pass
        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678',
            'website' => 'http://spam.com', // Honeypot field filled
            '_form_time' => time() - 1, // Too fast
            '_js_token' => 'invalid_token'
        ]);

        $this->assertNotEquals(403, $response->getStatusCode());
    }

    /** @test */
    public function it_logs_bot_activities()
    {
        \Log::shouldReceive('warning')
            ->with('Bot activity detected', \Mockery::type('array'))
            ->once();

        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678',
            'website' => 'http://spam.com', // Honeypot field filled
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ]);

        $this->assertEquals(403, $response->getStatusCode());
    }

    /** @test */
    public function it_auto_blocks_repeat_offenders()
    {
        config(['security.bot_detection.honeypot.auto_block' => true]);

        // First offense - should be blocked and IP should be auto-blocked
        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678',
            'website' => 'http://spam.com', // Honeypot field filled
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ]);

        $this->assertEquals(403, $response->getStatusCode());

        // Second request should also be blocked due to IP being blocked
        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ]);

        $this->assertEquals(403, $response->getStatusCode());
    }

    /** @test */
    public function it_handles_missing_user_agent()
    {
        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678',
            '_form_time' => time() - 5,
            '_js_token' => $this->generateJSToken()
        ], ['User-Agent' => '']);

        $this->assertEquals(403, $response->getStatusCode());
    }

    /** @test */
    public function it_allows_get_requests_without_javascript_validation()
    {
        $response = $this->getJson('/api/services');

        $this->assertNotEquals(403, $response->getStatusCode());
    }

    /** @test */
    public function it_handles_multiple_honeypot_fields()
    {
        $honeypotFields = [
            'website' => 'http://spam.com',
            'company_website' => 'http://bot.com',
            'url' => 'http://automated.com'
        ];

        foreach ($honeypotFields as $field => $value) {
            $response = $this->postJson('/api/register/otp', [
                'mobile' => '0512345678',
                'name' => 'Test User',
                $field => $value,
                '_form_time' => time() - 5,
                '_js_token' => $this->generateJSToken()
            ]);

            $this->assertEquals(403, $response->getStatusCode(), "Failed to detect honeypot field: {$field}");
        }
    }

    /** @test */
    public function it_can_be_disabled_via_configuration()
    {
        config(['security.bot_detection.enabled' => false]);

        $response = $this->postJson('/api/login', [
            'mobile' => '0512345678',
            'website' => 'http://spam.com', // Honeypot field filled
            '_form_time' => time() - 1, // Too fast
            '_js_token' => 'invalid_token'
        ]);

        $this->assertNotEquals(403, $response->getStatusCode());
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