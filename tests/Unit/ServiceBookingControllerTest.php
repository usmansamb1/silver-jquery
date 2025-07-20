<?php

namespace Tests\Unit;

use App\Http\Controllers\ServiceBookingController;
use App\Models\User;
use App\Models\Order;
use App\Models\ServiceBooking;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ServiceBookingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->controller = new ServiceBookingController();
        
        Auth::login($this->user);
    }

    /** @test */
    public function it_processes_successful_payment_without_existing_order()
    {
        $paymentData = [
            'id' => 'test_payment_id_123',
            'amount' => '172.50',
            'paymentBrand' => 'VISA',
            'result' => [
                'code' => '000.000.000',
                'description' => 'Transaction succeeded'
            ],
            'card' => [
                'number' => '4200000000000000'
            ]
        ];

        // Test without existing order
        $result = $this->controller->processSuccessfulServicePayment($paymentData, null, 172.50, 'credit_card');

        // Should create an order
        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'payment_status' => 'paid',
            'total_amount' => 172.50,
            'payment_method' => 'credit_card',
            'transaction_id' => 'test_payment_id_123'
        ]);

        // Check that it redirects to history page
        $this->assertEquals(302, $result->getStatusCode());
        $this->assertStringContainsString('services.booking.history', $result->getTargetUrl());
    }

    /** @test */
    public function it_processes_successful_payment_with_existing_order()
    {
        // Create an existing order
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'payment_status' => 'pending',
            'total_amount' => 172.50
        ]);

        $paymentData = [
            'id' => 'test_payment_id_456',
            'amount' => '172.50',
            'paymentBrand' => 'MASTERCARD',
            'result' => [
                'code' => '000.000.000',
                'description' => 'Transaction succeeded'
            ],
            'card' => [
                'number' => '5200000000000000'
            ]
        ];

        // Test with existing order
        $result = $this->controller->processSuccessfulServicePayment($paymentData, $order->id, 172.50, 'credit_card');

        // Should update existing order
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'user_id' => $this->user->id,
            'payment_status' => 'paid',
            'transaction_id' => 'test_payment_id_456'
        ]);

        // Check that it redirects to history page
        $this->assertEquals(302, $result->getStatusCode());
        $this->assertStringContainsString('services.booking.history', $result->getTargetUrl());
    }

    /** @test */
    public function it_updates_service_bookings_when_order_exists()
    {
        // Create an order with service bookings
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'payment_status' => 'pending'
        ]);

        $service = Service::factory()->create();
        
        $booking = ServiceBooking::factory()->create([
            'user_id' => $this->user->id,
            'order_id' => $order->id,
            'service_id' => $service->id,
            'payment_status' => 'pending',
            'status' => 'pending'
        ]);

        $paymentData = [
            'id' => 'test_payment_id_789',
            'amount' => '172.50',
            'paymentBrand' => 'VISA',
            'result' => [
                'code' => '000.000.000',
                'description' => 'Transaction succeeded'
            ],
            'card' => [
                'number' => '4200000000000000'
            ]
        ];

        // Process payment
        $result = $this->controller->processSuccessfulServicePayment($paymentData, $order->id, 172.50, 'credit_card');

        // Should update service booking status
        $this->assertDatabaseHas('service_bookings', [
            'id' => $booking->id,
            'payment_status' => 'approved',
            'status' => 'approved'
        ]);
    }

    /** @test */
    public function it_validates_hyperpay_form_request()
    {
        $request = Request::create('/test', 'POST', [
            'amount' => 'invalid_amount',
            'brand' => 'invalid_brand',
            'order_id' => 'non_existent_order'
        ]);

        $response = $this->controller->getHyperpayForm($request);

        $this->assertEquals(422, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('error', $responseData['status']);
        $this->assertArrayHasKey('message', $responseData);
    }

    /** @test */
    public function it_normalizes_card_brands_correctly()
    {
        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('normalizeCardBrand');
        $method->setAccessible(true);

        $this->assertEquals('VISA', $method->invoke($this->controller, 'VISA'));
        $this->assertEquals('MASTERCARD', $method->invoke($this->controller, 'MASTER'));
        $this->assertEquals('MASTERCARD', $method->invoke($this->controller, 'MASTERCARD'));
        $this->assertEquals('MADA', $method->invoke($this->controller, 'MADA'));
    }

    /** @test */
    public function it_identifies_successful_payment_codes()
    {
        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('isSuccessfulPayment');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($this->controller, '000.000.000'));
        $this->assertTrue($method->invoke($this->controller, '000.100.110'));
        $this->assertTrue($method->invoke($this->controller, '000.100.111'));
        $this->assertTrue($method->invoke($this->controller, '000.100.112'));
        $this->assertTrue($method->invoke($this->controller, '000.000.100'));
        $this->assertTrue($method->invoke($this->controller, '000.100.101'));
        
        $this->assertFalse($method->invoke($this->controller, '800.100.156'));
        $this->assertFalse($method->invoke($this->controller, '100.100.101'));
    }
} 