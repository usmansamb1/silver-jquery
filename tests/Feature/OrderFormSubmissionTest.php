<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Str;
use App\Models\Service;

class OrderFormSubmissionTest extends TestCase
{
    use WithFaker;
    
    /**
     * Create a test user
     *
     * @return User
     */
    protected function createUser()
    {
        return User::create([
            'id' => Str::uuid()->toString(),
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'),
        ]);
    }

    public function testOrderFormEndpointRequiresAuthentication()
    {
        // Test that the endpoint requires authentication
        $response = $this->get(route('services.booking.order.form'));
        
        // It should redirect to login page (302 status)
        $response->assertStatus(302)
                 ->assertRedirect(route('login'));
    }
    
    public function testJsonEndpointRequiresAuthentication()
    {
        // Test that the JSON endpoint requires authentication
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post(route('services.booking.order.form.json'), [
            'payment_method' => 'wallet',
        ]);
        
        // It should return 401 Unauthorized
        $response->assertStatus(401);
    }

    public function testValidationErrorsWhenAuthenticated()
    {
        // Create and authenticate a user
        $user = User::factory()->create();
        $this->actingAs($user);
        
        // Test validation errors by sending incomplete data
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post(route('services.booking.order.form.json'), [
            'payment_method' => 'wallet',
            // Missing required fields like pickup_location and services
        ]);
        
        // It should return validation errors
        $response->assertStatus(422);
    }
    
    /**
     * Test that wallet balance is properly deducted on successful order creation
     *
     * @return void
     */
    public function testWalletBalanceDeductionOnSuccessfulOrder()
    {
        // Create a user
        $user = User::factory()->create();
        
        // Create a wallet with sufficient balance for the user
        $initialBalance = 1000.00;
        $wallet = $user->wallet()->create([
            'balance' => $initialBalance,
            'currency' => 'SAR'
        ]);
        
        $this->actingAs($user);
        
        // Create test service data
        $serviceData = [
            'service_type' => 'rfid_car',
            'service_id' => 'rfid_80mm', // Using a string value as expected by the validator
            'refule_amount' => 100.00,
            'vehicle_make' => 'Toyota',
            'vehicle_manufacturer' => 'Toyota Motor Corp',
            'vehicle_model' => 'Camry',
            'vehicle_year' => '2023',
            'plate_number' => 'ABC123'
        ];
        
        // Create test order data
        $orderData = [
            'pickup_location' => 'Test Location',
            'payment_method' => 'wallet',
            'services' => [$serviceData]
        ];
        
        // Calculate expected amounts
        $baseServicePrice = 150.00; // Same as in the controller
        $refuelingAmount = 100.00;
        $subtotal = $baseServicePrice + $refuelingAmount;
        $vatAmount = $subtotal * 0.15;
        $totalAmount = $subtotal + $vatAmount;
        
        // Submit the order
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post(route('services.booking.order.form.json'), $orderData);
        
        // Assert the response is successful
        $response->assertOk()
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Service order created!'
                 ]);
                 
        // Refresh the wallet from the database
        $wallet->refresh();
        
        // Assert that the wallet balance has been deducted correctly
        $this->assertEquals(
            round($initialBalance - $totalAmount, 2),
            round($wallet->balance, 2),
            "Wallet balance was not deducted correctly"
        );
        
        // Check that a transaction record was created
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'amount' => -$totalAmount,
            'type' => 'debit'
        ]);
        
        // Check that the order was created with correct payment status
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'payment_status' => 'paid',
            'payment_method' => 'wallet'
        ]);
    }
    
    /**
     * Test that order fails when wallet balance is insufficient
     *
     * @return void
     */
    public function testOrderFailsWithInsufficientWalletBalance()
    {
        // Create a user
        $user = User::factory()->create();
        
        // Create a wallet with insufficient balance
        $initialBalance = 50.00; // Very low balance
        $wallet = $user->wallet()->create([
            'balance' => $initialBalance,
            'currency' => 'SAR'
        ]);
        
        $this->actingAs($user);
        
        // Create test service data
        $serviceData = [
            'service_type' => 'rfid_car',
            'service_id' => 'rfid_80mm', // Using a string value as expected by the validator
            'refule_amount' => 100.00,
            'vehicle_make' => 'Toyota',
            'vehicle_manufacturer' => 'Toyota Motor Corp',
            'vehicle_model' => 'Camry',
            'vehicle_year' => '2023',
            'plate_number' => 'ABC123'
        ];
        
        // Create test order data
        $orderData = [
            'pickup_location' => 'Test Location',
            'payment_method' => 'wallet',
            'services' => [$serviceData]
        ];
        
        // Calculate expected amounts
        $baseServicePrice = 150.00;
        $refuelingAmount = 100.00;
        $subtotal = $baseServicePrice + $refuelingAmount;
        $vatAmount = $subtotal * 0.15;
        $totalAmount = $subtotal + $vatAmount;
        
        // Submit the order
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post(route('services.booking.order.form.json'), $orderData);
        
        // Assert that the response is an error
        $response->assertStatus(422)
                 ->assertJson([
                     'status' => 'error',
                     'message' => 'Insufficient wallet balance'
                 ]);
                 
        // Refresh the wallet from the database
        $wallet->refresh();
        
        // Assert that the wallet balance has not changed
        $this->assertEquals(
            $initialBalance,
            $wallet->balance,
            "Wallet balance should not have changed"
        );
        
        // Check that no order was created for this failed attempt
        $this->assertDatabaseMissing('orders', [
            'user_id' => $user->id,
            'payment_status' => 'paid',
            'payment_method' => 'wallet'
        ]);
        
        // Check that no transaction record was created
        $this->assertDatabaseMissing('transactions', [
            'user_id' => $user->id,
            'type' => 'debit',
            'amount' => -$totalAmount
        ]);
    }
    
    /**
     * Test failed credit card payment handling
     * 
     * This test uses mocking to simulate a Stripe card error
     *
     * @return void
     */
    public function testFailedCreditCardPaymentHandling()
    {
        // Skip the test if running in CI environment
        if (env('CI') === true) {
            $this->markTestSkipped('Skipping Stripe test in CI environment');
        }
        
        // Create a user
        $user = User::factory()->create();
        
        $this->actingAs($user);
        
        // Create test service data
        $serviceData = [
            'service_type' => 'rfid_car',
            'service_id' => 'rfid_80mm', // Using a string value as expected by the validator
            'refule_amount' => 100.00,
            'vehicle_make' => 'Toyota',
            'vehicle_manufacturer' => 'Toyota Motor Corp',
            'vehicle_model' => 'Camry',
            'vehicle_year' => '2023',
            'plate_number' => 'ABC123'
        ];
        
        // Create test order data with an invalid test token that will trigger a card error
        $orderData = [
            'pickup_location' => 'Test Location',
            'payment_method' => 'credit_card',
            'services' => [$serviceData],
            'stripeToken' => 'tok_chargeDeclined', // Stripe test token that will always fail
            'save_card' => '1'
        ];
        
        // Mock the Stripe service
        $this->mock(\Stripe\StripeClient::class, function ($mock) {
            $mock->shouldReceive('charges->create')
                 ->andThrow(new \Stripe\Exception\CardException(
                     'Your card was declined.',
                     'card_declined',
                     null,
                     'ch_123',
                     400,
                     'error',
                     'req_123',
                     []
                 ));
        });
        
        // Submit the order
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post(route('services.booking.order.form.json'), $orderData);
        
        // Assert the response is an error
        $response->assertStatus(422)
                 ->assertJson([
                     'status' => 'error',
                     'message' => 'Payment failed'
                 ]);
        
        // Check that no order was created with 'paid' status
        $this->assertDatabaseMissing('orders', [
            'user_id' => $user->id,
            'payment_status' => 'paid',
            'payment_method' => 'credit_card'
        ]);
        
        // Check that no saved card was created
        $this->assertDatabaseMissing('saved_cards', [
            'user_id' => $user->id
        ]);
    }
    
    /**
     * Test that service_bookings records properly associate with the order record
     *
     * @return void
     */
    public function testServiceBookingsAssociateWithOrder()
    {
        // Create a user
        $user = User::factory()->create();
        
        // Create a wallet with sufficient balance for the user
        $initialBalance = 1000.00;
        $wallet = $user->wallet()->create([
            'balance' => $initialBalance,
            'currency' => 'SAR'
        ]);
        
        $this->actingAs($user);
        
        // Create multiple service data items to test multiple bookings
        $servicesData = [
            [
                'service_type' => 'rfid_car',
                'service_id' => 'rfid_80mm', // Using a string value as expected by the validator
                'refule_amount' => 100.00,
                'vehicle_make' => 'Toyota',
                'vehicle_manufacturer' => 'Toyota Motor Corp',
                'vehicle_model' => 'Camry',
                'vehicle_year' => '2023',
                'plate_number' => 'ABC123'
            ],
            [
                'service_type' => 'rfid_truck',
                'service_id' => 'rfid_120mm', // Using a string value as expected by the validator
                'refule_amount' => 200.00,
                'vehicle_make' => 'Ford',
                'vehicle_manufacturer' => 'Ford Motors',
                'vehicle_model' => 'F-150',
                'vehicle_year' => '2022',
                'plate_number' => 'XYZ456'
            ]
        ];
        
        // Create test order data with multiple services
        $orderData = [
            'pickup_location' => 'Test Location',
            'payment_method' => 'wallet',
            'services' => $servicesData
        ];
        
        // Submit the order
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post(route('services.booking.order.form.json'), $orderData);
        
        // Output the response content for debugging
        echo "Response status: " . $response->getStatusCode() . "\n";
        echo "Response content: " . $response->getContent() . "\n";
        
        // Assert the response is successful
        $response->assertOk()
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Service order created!'
                 ]);
                 
        // Extract the order ID from the database
        $order = \App\Models\Order::where('user_id', $user->id)
                                  ->where('payment_method', 'wallet')
                                  ->latest()
                                  ->first();
                                  
        $this->assertNotNull($order, 'Order should be created');
        
        // Check that service bookings were created with the correct order_id
        $this->assertDatabaseHas('service_bookings', [
            'user_id' => $user->id,
            'order_id' => $order->id,
            'service_type' => 'rfid_car',
            'vehicle_make' => 'Toyota',
        ]);
        
        $this->assertDatabaseHas('service_bookings', [
            'user_id' => $user->id,
            'order_id' => $order->id,
            'service_type' => 'rfid_truck',
            'vehicle_make' => 'Ford',
        ]);
        
        // Check the count of service bookings
        $bookingsCount = \App\Models\ServiceBooking::where('order_id', $order->id)->count();
        $this->assertEquals(2, $bookingsCount, 'There should be 2 service bookings for this order');
    }
} 