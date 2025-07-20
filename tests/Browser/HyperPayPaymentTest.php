<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Service;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class HyperPayPaymentTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $wallet;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user
        $this->user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => bcrypt('password123'),
            'first_name' => 'Test',
            'last_name' => 'User',
            'mobile' => '966501234567',
            'status' => 'active'
        ]);
        
        // Create wallet
        $this->wallet = Wallet::factory()->create([
            'user_id' => $this->user->id,
            'balance' => 50.00
        ]);
        
        // Create service
        $this->service = Service::factory()->create([
            'name' => 'RFID Installation',
            'price' => 150.00,
            'is_active' => true
        ]);
        
        // Set up HyperPay test configuration
        Config::set('services.hyperpay.access_token', 'test_token');
        Config::set('services.hyperpay.base_url', 'https://eu-test.oppwa.com/');
        Config::set('services.hyperpay.entity_id_credit', 'test_entity_credit');
        Config::set('services.hyperpay.entity_id_mada', 'test_entity_mada');
        Config::set('services.hyperpay.mode', 'test');
    }

    /**
     * Test wallet topup payment flow
     */
    public function test_wallet_topup_payment_flow()
    {
        // Mock successful HyperPay responses
        Http::fake([
            'https://eu-test.oppwa.com/v1/checkouts' => Http::response([
                'id' => 'wallet_checkout_123',
                'result' => [
                    'code' => '000.200.100',
                    'description' => 'successfully created checkout'
                ]
            ], 200),
            'https://eu-test.oppwa.com/v1/checkouts/wallet_checkout_123/payment' => Http::response([
                'id' => 'wallet_payment_123',
                'amount' => '100.00',
                'currency' => 'SAR',
                'result' => [
                    'code' => '000.100.110',
                    'description' => 'Request successfully processed in TEST mode'
                ],
                'paymentBrand' => 'VISA'
            ], 200)
        ]);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/wallet/topup')
                    ->assertSee('Wallet Top-up')
                    ->type('#topupAmount', '100')
                    ->click('.payment-method[data-method="credit-card"]')
                    ->waitFor('#hyperpay-section', 10)
                    ->click('#show-hyperpay-form')
                    ->waitFor('#hyperpay-widget .paymentWidgets', 15)
                    ->assertSee('Loading secure payment form')
                    
                    // Verify HyperPay form loaded
                    ->waitUntilMissing('#hyperpay-widget .spinner-border', 20)
                    ->assertPresent('.paymentWidgets')
                    
                    // Test payment form interaction
                    ->assertSee('Secure payment powered by HyperPay')
                    ->assertPresent('form.paymentWidgets');
        });
    }

    /**
     * Test service booking payment flow
     */
    public function test_service_booking_payment_flow()
    {
        // Mock successful HyperPay responses
        Http::fake([
            'https://eu-test.oppwa.com/v1/checkouts' => Http::response([
                'id' => 'service_checkout_456',
                'result' => [
                    'code' => '000.200.100',
                    'description' => 'successfully created checkout'
                ]
            ], 200),
            'https://eu-test.oppwa.com/v1/checkouts/service_checkout_456/payment' => Http::response([
                'id' => 'service_payment_456',
                'amount' => '172.50',
                'currency' => 'SAR',
                'result' => [
                    'code' => '000.100.110',
                    'description' => 'Request successfully processed in TEST mode'
                ],
                'paymentBrand' => 'VISA'
            ], 200)
        ]);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/services/booking/order/form')
                    ->assertSee('Service Order Form')
                    
                    // Add service
                    ->click('#addServiceBtn')
                    ->waitFor('#servicesList .service-item', 5)
                    
                    // Fill vehicle details
                    ->type('#plate_number', 'ABC123')
                    ->type('#vehicle_make', 'Toyota')
                    ->type('#vehicle_manufacturer', 'Toyota')
                    ->type('#vehicle_model', 'Camry')
                    ->type('#vehicle_year', '2020')
                    ->type('#pickup_location', 'Test Location')
                    
                    // Select credit card payment
                    ->click('#payment_credit_card')
                    ->waitFor('#credit-card-form', 5)
                    ->assertVisible('#credit-card-form')
                    
                    // Select card brand
                    ->click('input[name="card_brand"][value="VISA MASTER"]')
                    
                    // Wait for HyperPay widget to load
                    ->waitFor('#hyperpay-widget .paymentWidgets', 15)
                    ->assertSee('Loading secure payment form')
                    
                    // Verify HyperPay form loaded
                    ->waitUntilMissing('#hyperpay-widget .spinner-border', 20)
                    ->assertPresent('.paymentWidgets')
                    ->assertSee('Secure payment powered by HyperPay');
        });
    }

    /**
     * Test payment method switching
     */
    public function test_payment_method_switching()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/wallet/topup')
                    ->type('#topupAmount', '100')
                    
                    // Start with wallet payment (if available)
                    ->click('.payment-method[data-method="credit-card"]')
                    ->waitFor('#hyperpay-section', 10)
                    ->click('#show-hyperpay-form')
                    ->waitFor('#hyperpay-widget', 10)
                    
                    // Switch back to different payment method if available
                    ->click('#change-payment-method')
                    ->waitUntilMissing('#hyperpay-section', 5)
                    ->assertNotVisible('#hyperpay-section')
                    
                    // Switch back to credit card
                    ->click('#show-hyperpay-form')
                    ->waitFor('#hyperpay-section', 10)
                    ->assertVisible('#hyperpay-section');
        });
    }

    /**
     * Test amount change handling
     */
    public function test_amount_change_handling()
    {
        // Mock HyperPay responses for different amounts
        Http::fake([
            'https://eu-test.oppwa.com/v1/checkouts' => Http::response([
                'id' => 'amount_test_checkout',
                'result' => [
                    'code' => '000.200.100',
                    'description' => 'successfully created checkout'
                ]
            ], 200)
        ]);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/wallet/topup')
                    ->type('#topupAmount', '100')
                    ->click('.payment-method[data-method="credit-card"]')
                    ->click('#show-hyperpay-form')
                    ->waitFor('#hyperpay-widget', 10)
                    
                    // Change amount
                    ->clear('#topupAmount')
                    ->type('#topupAmount', '200')
                    ->pause(1000) // Wait for debounced update
                    
                    // Verify amount display updates
                    ->waitForTextIn('#hyperpay-current-amount', '200.00', 5)
                    ->assertSeeIn('#hyperpay-current-amount', '200.00');
        });
    }

    /**
     * Test error handling
     */
    public function test_error_handling()
    {
        // Mock HyperPay error response
        Http::fake([
            'https://eu-test.oppwa.com/v1/checkouts' => Http::response([
                'result' => [
                    'code' => '200.300.404',
                    'description' => 'invalid or missing parameter'
                ]
            ], 400)
        ]);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/wallet/topup')
                    ->type('#topupAmount', '100')
                    ->click('.payment-method[data-method="credit-card"]')
                    ->click('#show-hyperpay-form')
                    ->waitFor('#hyperpay-widget', 10)
                    
                    // Wait for error message
                    ->waitForText('Failed to load payment form', 15)
                    ->assertSee('Failed to load payment form')
                    ->assertSee('Try Again');
        });
    }

    /**
     * Test minimum amount validation
     */
    public function test_minimum_amount_validation()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/wallet/topup')
                    ->type('#topupAmount', '5') // Below minimum
                    ->click('.payment-method[data-method="credit-card"]')
                    ->click('#show-hyperpay-form')
                    ->waitFor('#hyperpay-widget', 10)
                    
                    // Should show validation error
                    ->waitForText('Please enter a valid amount', 10)
                    ->assertSee('minimum 10 SAR');
        });
    }

    /**
     * Test card brand selection in service booking
     */
    public function test_card_brand_selection()
    {
        // Mock responses for different card brands
        Http::fake([
            'https://eu-test.oppwa.com/v1/checkouts' => Http::response([
                'id' => 'brand_test_checkout',
                'result' => [
                    'code' => '000.200.100',
                    'description' => 'successfully created checkout'
                ]
            ], 200)
        ]);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/services/booking/order/form')
                    
                    // Add service
                    ->click('#addServiceBtn')
                    ->waitFor('#servicesList .service-item', 5)
                    
                    // Fill required fields
                    ->type('#plate_number', 'ABC123')
                    ->type('#vehicle_make', 'Toyota')
                    ->type('#pickup_location', 'Test Location')
                    
                    // Select credit card payment
                    ->click('#payment_credit_card')
                    ->waitFor('#credit-card-form', 5)
                    
                    // Test VISA/MasterCard selection
                    ->click('input[name="card_brand"][value="VISA MASTER"]')
                    ->waitFor('#hyperpay-widget .paymentWidgets', 15)
                    ->assertPresent('.paymentWidgets[data-brands*="VISA MASTER"]')
                    
                    // Test MADA selection
                    ->click('input[name="card_brand"][value="MADA"]')
                    ->pause(1000) // Wait for widget reload
                    ->waitFor('#hyperpay-widget .paymentWidgets', 15)
                    ->assertPresent('.paymentWidgets[data-brands*="MADA"]');
        });
    }

    /**
     * Test responsive design
     */
    public function test_responsive_design()
    {
        $this->browse(function (Browser $browser) {
            // Test on mobile viewport
            $browser->resize(375, 667) // iPhone SE size
                    ->loginAs($this->user)
                    ->visit('/wallet/topup')
                    ->assertSee('Wallet Top-up')
                    ->type('#topupAmount', '100')
                    ->click('.payment-method[data-method="credit-card"]')
                    ->click('#show-hyperpay-form')
                    ->waitFor('#hyperpay-widget', 10)
                    ->assertPresent('#hyperpay-widget')
                    
                    // Test on tablet viewport
                    ->resize(768, 1024) // iPad size
                    ->refresh()
                    ->waitFor('#topupAmount', 5)
                    ->type('#topupAmount', '100')
                    ->click('.payment-method[data-method="credit-card"]')
                    ->click('#show-hyperpay-form')
                    ->waitFor('#hyperpay-widget', 10)
                    ->assertPresent('#hyperpay-widget')
                    
                    // Test on desktop
                    ->resize(1920, 1080)
                    ->refresh()
                    ->waitFor('#topupAmount', 5)
                    ->type('#topupAmount', '100')
                    ->click('.payment-method[data-method="credit-card"]')
                    ->click('#show-hyperpay-form')
                    ->waitFor('#hyperpay-widget', 10)
                    ->assertPresent('#hyperpay-widget');
        });
    }

    /**
     * Test form state persistence during navigation
     */
    public function test_form_state_persistence()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/services/booking/order/form')
                    
                    // Fill form data
                    ->type('#plate_number', 'TEST123')
                    ->type('#vehicle_make', 'BMW')
                    ->type('#pickup_location', 'Test Location')
                    
                    // Add service
                    ->click('#addServiceBtn')
                    ->waitFor('#servicesList .service-item', 5)
                    
                    // Navigate to another page and back
                    ->visit('/services/booking/history')
                    ->back()
                    
                    // Check if form data is preserved (if implemented)
                    ->assertInputValue('#plate_number', '')
                    ->assertInputValue('#vehicle_make', '')
                    ->assertInputValue('#pickup_location', '');
        });
    }

    /**
     * Test JavaScript console for errors
     */
    public function test_javascript_errors()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/wallet/topup')
                    ->type('#topupAmount', '100')
                    ->click('.payment-method[data-method="credit-card"]')
                    ->click('#show-hyperpay-form')
                    ->waitFor('#hyperpay-widget', 10)
                    
                    // Check for JavaScript errors
                    ->assertMissing('.error-message')
                    ->assertMissing('[class*="error"]');
            
            // Get console logs to check for JavaScript errors
            $logs = $browser->driver->manage()->getLog('browser');
            $errors = array_filter($logs, function($log) {
                return $log['level'] === 'SEVERE';
            });
            
            $this->assertEmpty($errors, 'JavaScript errors found: ' . json_encode($errors));
        });
    }

    /**
     * Test accessibility features
     */
    public function test_accessibility_features()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/wallet/topup')
                    
                    // Check for proper labeling
                    ->assertPresent('label[for="topupAmount"]')
                    ->assertAttribute('#topupAmount', 'aria-label', null)
                    
                    // Check for proper form structure
                    ->assertPresent('form')
                    ->assertPresent('button[type="submit"], button[type="button"]')
                    
                    // Test keyboard navigation
                    ->keys('#topupAmount', ['{tab}'])
                    ->assertFocused('.payment-method input, #show-hyperpay-form')
                    
                    // Check for screen reader content
                    ->assertPresent('.visually-hidden, .sr-only');
        });
    }
} 