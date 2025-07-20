<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ServiceBooking;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\ServiceBookingController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class HyperPayPaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $wallet;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'first_name' => 'Test',
            'last_name' => 'User',
            'mobile' => '966501234567'
        ]);
        
        // Create wallet
        $this->wallet = Wallet::factory()->create([
            'user_id' => $this->user->id,
            'balance' => 100.00
        ]);
        
        // Set up HyperPay configuration
        Config::set('services.hyperpay.access_token', 'test_token');
        Config::set('services.hyperpay.base_url', 'https://eu-test.oppwa.com/');
        Config::set('services.hyperpay.entity_id_credit', 'test_entity_credit');
        Config::set('services.hyperpay.entity_id_mada', 'test_entity_mada');
        Config::set('services.hyperpay.mode', 'test');
    }

    /** @test */
    public function it_can_create_wallet_topup_hyperpay_form()
    {
        // Mock successful HyperPay API response
        Http::fake([
            'https://eu-test.oppwa.com/v1/checkouts' => Http::response([
                'id' => 'test_checkout_id_123',
                'result' => [
                    'code' => '000.200.100',
                    'description' => 'successfully created checkout'
                ]
            ], 200)
        ]);

        $controller = new WalletController();
        $request = new Request([
            'amount' => 100.00,
            'brand' => 'credit_card'
        ]);

        $this->actingAs($this->user);
        $response = $controller->getHyperpayForm($request);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = $response->getData(true);
        
        $this->assertEquals('success', $responseData['status']);
        $this->assertEquals('test_checkout_id_123', $responseData['checkout_id']);
        $this->assertArrayHasKey('html', $responseData);
        $this->assertStringContains('hyperpay-form-test_checkout_id_123', $responseData['html']);
    }

    /** @test */
    public function it_can_create_service_booking_hyperpay_form()
    {
        // Mock successful HyperPay API response
        Http::fake([
            'https://eu-test.oppwa.com/v1/checkouts' => Http::response([
                'id' => 'service_checkout_id_456',
                'result' => [
                    'code' => '000.200.100',
                    'description' => 'successfully created checkout'
                ]
            ], 200)
        ]);

        $controller = new ServiceBookingController();
        $request = new Request([
            'amount' => 216.50,
            'brand' => 'credit_card',
            'order_id' => null
        ]);

        $this->actingAs($this->user);
        $response = $controller->getHyperpayForm($request);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = $response->getData(true);
        
        $this->assertEquals('success', $responseData['status']);
        $this->assertEquals('service_checkout_id_456', $responseData['checkout_id']);
        $this->assertArrayHasKey('html', $responseData);
        $this->assertStringContains('hyperpay-form-service_checkout_id_456', $responseData['html']);
    }

    /** @test */
    public function it_handles_brand_normalization_correctly()
    {
        // Test MADA brand normalization
        Http::fake([
            'https://eu-test.oppwa.com/v1/checkouts' => Http::response([
                'id' => 'mada_checkout_id_789',
                'result' => [
                    'code' => '000.200.100',
                    'description' => 'successfully created checkout'
                ]
            ], 200)
        ]);

        $controller = new ServiceBookingController();
        $request = new Request([
            'amount' => 100.00,
            'brand' => 'mada_card'
        ]);

        $this->actingAs($this->user);
        $response = $controller->getHyperpayForm($request);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = $response->getData(true);
        
        $this->assertEquals('success', $responseData['status']);
        $this->assertArrayHasKey('widget_options', $responseData);
        $this->assertEquals('MADA', $responseData['widget_options']['brands']);
    }

    /** @test */
    public function it_validates_form_input_correctly()
    {
        $controller = new ServiceBookingController();
        
        // Test invalid amount
        $request = new Request([
            'amount' => 5.00, // Below minimum
            'brand' => 'credit_card'
        ]);

        $this->actingAs($this->user);
        $response = $controller->getHyperpayForm($request);

        $this->assertEquals(422, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertEquals('error', $responseData['status']);
        $this->assertStringContains('Minimum payment amount is 10 SAR', $responseData['message']);
    }

    /** @test */
    public function it_handles_successful_wallet_payment()
    {
        // Mock successful payment status response
        Http::fake([
            'https://eu-test.oppwa.com/v1/checkouts/test_checkout_id/payment' => Http::response([
                'id' => 'payment_123',
                'amount' => '100.00',
                'currency' => 'SAR',
                'result' => [
                    'code' => '000.100.110',
                    'description' => 'Request successfully processed in TEST mode'
                ],
                'paymentBrand' => 'VISA'
            ], 200)
        ]);

        // Set up session
        Session::put('hyperpay_checkout_id', 'test_checkout_id');
        Session::put('hyperpay_amount', 100.00);
        Session::put('hyperpay_brand', 'credit_card');

        $controller = new WalletController();
        $request = new Request([
            'id' => 'test_checkout_id',
            'resourcePath' => '/v1/checkouts/test_checkout_id/payment'
        ]);

        $this->actingAs($this->user);
        $response = $controller->hyperpayStatus($request);

        // Check that payment was processed successfully
        $this->assertEquals(200, $response->getStatusCode());
        
        // Check that payment record was created
        $payment = Payment::where('user_id', $this->user->id)
                         ->where('hyperpay_transaction_id', 'payment_123')
                         ->first();
        $this->assertNotNull($payment);
        $this->assertEquals('approved', $payment->status);
        $this->assertEquals(100.00, $payment->amount);
        
        // Check that wallet balance was updated
        $this->wallet->refresh();
        $this->assertEquals(200.00, $this->wallet->balance);
    }

    /** @test */
    public function it_handles_successful_service_booking_payment()
    {
        // Create order first
        $order = Order::create([
            'user_id' => $this->user->id,
            'reference_number' => 'ORD-TEST-123',
            'order_number' => 'ORD-TEST-123',
            'total_amount' => 216.50,
            'subtotal' => 188.00,
            'vat' => 28.50,
            'pickup_location' => 'Test Location',
            'payment_method' => 'credit_card',
            'payment_status' => 'pending'
        ]);

        // Mock successful payment status response
        Http::fake([
            'https://eu-test.oppwa.com/v1/checkouts/service_checkout_id/payment' => Http::response([
                'id' => 'service_payment_456',
                'amount' => '216.50',
                'currency' => 'SAR',
                'result' => [
                    'code' => '000.100.110',
                    'description' => 'Request successfully processed in TEST mode'
                ],
                'paymentBrand' => 'VISA'
            ], 200)
        ]);

        // Set up session
        Session::put('hyperpay_checkout_id', 'service_checkout_id');
        Session::put('hyperpay_amount', 216.50);
        Session::put('hyperpay_order_id', $order->id);
        Session::put('hyperpay_brand', 'credit_card');

        $controller = new ServiceBookingController();
        $request = new Request([
            'id' => 'service_checkout_id',
            'resourcePath' => '/v1/checkouts/service_checkout_id/payment'
        ]);

        $this->actingAs($this->user);
        $response = $controller->hyperpayStatus($request);

        // Check that payment was processed successfully
        $this->assertEquals(302, $response->getStatusCode()); // Redirect response
        
        // Check that order was updated
        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('service_payment_456', $order->transaction_id);
    }

    /** @test */
    public function it_handles_failed_payment()
    {
        // Mock failed payment status response
        Http::fake([
            'https://eu-test.oppwa.com/v1/checkouts/failed_checkout_id/payment' => Http::response([
                'id' => 'failed_payment_789',
                'amount' => '100.00',
                'currency' => 'SAR',
                'result' => [
                    'code' => '200.300.404',
                    'description' => 'invalid or missing parameter'
                ]
            ], 200)
        ]);

        // Set up session
        Session::put('hyperpay_checkout_id', 'failed_checkout_id');
        Session::put('hyperpay_amount', 100.00);
        Session::put('hyperpay_brand', 'credit_card');

        $controller = new WalletController();
        $request = new Request([
            'id' => 'failed_checkout_id',
            'resourcePath' => '/v1/checkouts/failed_checkout_id/payment'
        ]);

        $this->actingAs($this->user);
        $response = $controller->hyperpayStatus($request);

        // Check that payment failed appropriately
        $this->assertEquals(200, $response->getStatusCode());
        
        // Check that no payment record was created for failed payment
        $payment = Payment::where('user_id', $this->user->id)
                         ->where('hyperpay_transaction_id', 'failed_payment_789')
                         ->first();
        $this->assertNull($payment);
        
        // Check that wallet balance was not updated
        $this->wallet->refresh();
        $this->assertEquals(100.00, $this->wallet->balance);
    }

    /** @test */
    public function it_detects_duplicate_payments()
    {
        // Create existing payment record
        $existingPayment = Payment::create([
            'user_id' => $this->user->id,
            'payment_type' => 'credit_card',
            'amount' => 100.00,
            'status' => 'approved',
            'hyperpay_transaction_id' => 'duplicate_payment_123'
        ]);

        // Mock successful payment status response with same transaction ID
        Http::fake([
            'https://eu-test.oppwa.com/v1/checkouts/duplicate_checkout_id/payment' => Http::response([
                'id' => 'duplicate_payment_123',
                'amount' => '100.00',
                'currency' => 'SAR',
                'result' => [
                    'code' => '000.100.110',
                    'description' => 'Request successfully processed in TEST mode'
                ],
                'paymentBrand' => 'VISA'
            ], 200)
        ]);

        // Set up session
        Session::put('hyperpay_checkout_id', 'duplicate_checkout_id');
        Session::put('hyperpay_amount', 100.00);
        Session::put('hyperpay_brand', 'credit_card');

        $controller = new WalletController();
        $request = new Request([
            'id' => 'duplicate_checkout_id',
            'resourcePath' => '/v1/checkouts/duplicate_checkout_id/payment'
        ]);

        $this->actingAs($this->user);
        $response = $controller->hyperpayStatus($request);

        // Check that duplicate payment was detected and handled
        $this->assertEquals(200, $response->getStatusCode());
        
        // Check that wallet balance was not updated again
        $this->wallet->refresh();
        $this->assertEquals(100.00, $this->wallet->balance);
    }

    /** @test */
    public function it_detects_amount_mismatch()
    {
        // Mock payment status response with mismatched amount
        Http::fake([
            'https://eu-test.oppwa.com/v1/checkouts/mismatch_checkout_id/payment' => Http::response([
                'id' => 'mismatch_payment_456',
                'amount' => '150.00', // Different from session amount
                'currency' => 'SAR',
                'result' => [
                    'code' => '000.100.110',
                    'description' => 'Request successfully processed in TEST mode'
                ],
                'paymentBrand' => 'VISA'
            ], 200)
        ]);

        // Set up session with different amount
        Session::put('hyperpay_checkout_id', 'mismatch_checkout_id');
        Session::put('hyperpay_amount', 100.00); // Different from response amount
        Session::put('hyperpay_brand', 'credit_card');

        $controller = new WalletController();
        $request = new Request([
            'id' => 'mismatch_checkout_id',
            'resourcePath' => '/v1/checkouts/mismatch_checkout_id/payment',
            'expected_amount' => 100.00
        ]);

        $this->actingAs($this->user);
        $response = $controller->hyperpayStatus($request);

        // Check that amount mismatch was detected and logged
        $this->assertEquals(200, $response->getStatusCode());
        
        // Amount mismatch within tolerance should still process
        $payment = Payment::where('user_id', $this->user->id)
                         ->where('hyperpay_transaction_id', 'mismatch_payment_456')
                         ->first();
        $this->assertNotNull($payment);
    }

    /** @test */
    public function it_handles_hyperpay_api_errors()
    {
        // Mock API error response
        Http::fake([
            'https://eu-test.oppwa.com/v1/checkouts' => Http::response([
                'result' => [
                    'code' => '200.300.404',
                    'description' => 'invalid or missing parameter'
                ]
            ], 400)
        ]);

        $controller = new ServiceBookingController();
        $request = new Request([
            'amount' => 100.00,
            'brand' => 'credit_card'
        ]);

        $this->actingAs($this->user);
        $response = $controller->getHyperpayForm($request);

        $this->assertEquals(500, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertEquals('error', $responseData['status']);
        $this->assertStringContains('Invalid or missing parameter', $responseData['message']);
    }

    /** @test */
    public function it_handles_session_mismatch()
    {
        // Set up session with different checkout ID
        Session::put('hyperpay_checkout_id', 'different_checkout_id');
        Session::put('hyperpay_amount', 100.00);
        Session::put('hyperpay_brand', 'credit_card');

        $controller = new WalletController();
        $request = new Request([
            'id' => 'mismatched_checkout_id', // Different from session
            'resourcePath' => '/v1/checkouts/mismatched_checkout_id/payment'
        ]);

        $this->actingAs($this->user);
        $response = $controller->hyperpayStatus($request);

        // Check that session mismatch was detected
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContains('Payment session mismatch', $response->getContent());
    }

    /** @test */
    public function it_clears_session_data_after_payment()
    {
        // Mock successful payment status response
        Http::fake([
            'https://eu-test.oppwa.com/v1/checkouts/cleanup_checkout_id/payment' => Http::response([
                'id' => 'cleanup_payment_123',
                'amount' => '100.00',
                'currency' => 'SAR',
                'result' => [
                    'code' => '000.100.110',
                    'description' => 'Request successfully processed in TEST mode'
                ],
                'paymentBrand' => 'VISA'
            ], 200)
        ]);

        // Set up session
        Session::put('hyperpay_checkout_id', 'cleanup_checkout_id');
        Session::put('hyperpay_amount', 100.00);
        Session::put('hyperpay_brand', 'credit_card');

        $controller = new WalletController();
        $request = new Request([
            'id' => 'cleanup_checkout_id',
            'resourcePath' => '/v1/checkouts/cleanup_checkout_id/payment'
        ]);

        $this->actingAs($this->user);
        $response = $controller->hyperpayStatus($request);

        // Check that session data was cleared
        $this->assertNull(Session::get('hyperpay_checkout_id'));
        $this->assertNull(Session::get('hyperpay_amount'));
        $this->assertNull(Session::get('hyperpay_brand'));
    }
} 