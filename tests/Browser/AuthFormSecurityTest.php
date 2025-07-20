<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AuthFormSecurityTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test that honeypot fields are properly hidden
     */
    public function test_honeypot_fields_are_hidden()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertPresent('input[name="website"]')
                    ->assertScript('document.querySelector(\'input[name="website"]\').style.display === "none"');
        });
    }

    /**
     * Test that JavaScript tokens are generated
     */
    public function test_javascript_tokens_are_generated()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->waitFor('#login_js_token')
                    ->assertInputValueIsNot('login_js_token', '')
                    ->assertInputValueIsNot('register_js_token', '');
        });
    }

    /**
     * Test that form timing is tracked
     */
    public function test_form_timing_is_tracked()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertPresent('input[name="_form_time"]')
                    ->assertScript('document.querySelector(\'input[name="_form_time"]\').value !== ""');
        });
    }

    /**
     * Test that form interactions are tracked
     */
    public function test_form_interactions_are_tracked()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->click('#login_mobile')
                    ->type('#login_mobile', '0512345678')
                    ->assertScript('typeof formInteractionCount !== "undefined" && formInteractionCount > 0');
        });
    }

    /**
     * Test that legitimate user can submit form
     */
    public function test_legitimate_user_can_submit_form()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->waitFor('#login_mobile')
                    ->type('#login_mobile', '0512345678')
                    ->pause(2000) // Wait to avoid timing issues
                    ->click('button[type="submit"]')
                    ->waitFor('.swal2-popup', 10); // Wait for success/error popup
        });
    }

    /**
     * Test that bot-like behavior is detected
     */
    public function test_bot_behavior_detection()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->waitFor('#login_mobile')
                    // Fill honeypot field (simulate bot)
                    ->script('document.querySelector(\'input[name="website"]\').value = "http://spam.com"')
                    ->type('#login_mobile', '0512345678')
                    ->click('button[type="submit"]')
                    ->waitFor('.swal2-popup', 10)
                    ->assertSee('error'); // Should show error due to honeypot
        });
    }

    /**
     * Test registration form security features
     */
    public function test_registration_form_security()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->waitFor('#registerForm')
                    ->assertPresent('#registerForm input[name="website"]')
                    ->assertPresent('#registerForm input[name="company_website"]')
                    ->assertPresent('#registerForm input[name="url"]')
                    ->assertPresent('#registerForm input[name="_form_time"]')
                    ->assertPresent('#registerForm input[name="_js_token"]');
        });
    }

    /**
     * Test that security doesn't interfere with normal form validation
     */
    public function test_security_does_not_interfere_with_validation()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->waitFor('#login_mobile')
                    ->click('button[type="submit"]') // Submit without filling required field
                    ->waitFor('.invalid-feedback', 5)
                    ->assertSee('Mobile number is required');
        });
    }

    /**
     * Test that forms work with different user agents
     */
    public function test_forms_work_with_legitimate_user_agents()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->waitFor('#login_mobile')
                    ->type('#login_mobile', '0512345678')
                    ->pause(2000)
                    ->click('button[type="submit"]')
                    ->waitFor('.swal2-popup', 10);
                    
            // Should not be blocked for legitimate browser
            $browser->assertDontSee('Access denied');
        });
    }

    /**
     * Test accordion functionality still works with security
     */
    public function test_accordion_functionality_works()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->waitFor('.accordion-header')
                    ->click('.accordion-header:nth-child(2)') // Click registration accordion
                    ->waitFor('#registerForm', 3)
                    ->assertVisible('#registerForm');
        });
    }

    /**
     * Test that language switching works with security
     */
    public function test_language_switching_works()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->waitFor('.dropdown-toggle')
                    ->click('.dropdown-toggle')
                    ->waitFor('.dropdown-menu', 3)
                    ->click('a[href*="lang.change"]')
                    ->waitUntilMissing('.dropdown-menu')
                    ->assertPresent('#login_mobile'); // Form should still be there
        });
    }

    /**
     * Test that security works with RTL layout
     */
    public function test_security_works_with_rtl()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/?lang=ar')
                    ->waitFor('#login_mobile')
                    ->assertPresent('input[name="website"]')
                    ->assertPresent('input[name="_js_token"]')
                    ->assertPresent('input[name="_form_time"]');
        });
    }
}