<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Service;
use App\Models\ServiceBooking;
use App\Models\Vehicle;
use App\Models\Wallet;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class VehicleAndDeliveryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $customer;
    protected $deliveryAgent;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'customer']);
        Role::create(['name' => 'delivery']);
        
        // Create customer user
        $this->customer = User::factory()->create([
            'email' => 'customer@example.com',
            'password' => bcrypt('password')
        ]);
        $this->customer->assignRole('customer');
        
        // Create wallet for customer
        Wallet::create([
            'id' => Str::uuid(),
            'user_id' => $this->customer->id,
            'balance' => 1000.00
        ]);
        
        // Create delivery agent user
        $this->deliveryAgent = User::factory()->create([
            'email' => 'delivery@example.com',
            'password' => bcrypt('password')
        ]);
        $this->deliveryAgent->assignRole('delivery');
        
        // Create service
        $this->service = Service::create([
            'id' => Str::uuid(),
            'name' => 'Oil Change',
            'description' => 'Full vehicle oil change service',
            'base_price' => 150.00,
            'is_active' => true,
            'service_type' => 'A',
            'estimated_duration' => 60,
            'vat_percentage' => 15.00
        ]);
    }

    /** @test */
    public function it_creates_a_vehicle_record_when_booking_a_service()
    {
        $this->actingAs($this->customer);
        
        $vehicleData = [
            'plate_number' => 'ABC123',
            'vehicle_make' => 'Toyota',
            'vehicle_manufacturer' => 'Toyota',
            'vehicle_model' => 'Camry',
            'vehicle_year' => '2022'
        ];
        
        $response = $this->post(route('services.booking.store'), [
            'service_id' => $this->service->id,
            'booking_date' => now()->addDays(2)->format('Y-m-d'),
            'booking_time' => now()->addDays(2)->format('H:i'),
            'payment_method' => 'wallet',
            ...$vehicleData
        ]);
        
        // Check if vehicle was created
        $this->assertDatabaseHas('vehicles', [
            'user_id' => $this->customer->id,
            'plate_number' => 'ABC123',
            'make' => 'Toyota',
            'manufacturer' => 'Toyota',
            'model' => 'Camry'
        ]);
        
        // Get the created vehicle
        $vehicle = Vehicle::where('plate_number', 'ABC123')->first();
        
        // Check if the booking has correct vehicle_id
        $booking = ServiceBooking::latest('created_at')->first();
        $this->assertEquals($vehicle->id, $booking->vehicle_id);
    }

    /** @test */
    public function it_reuses_existing_vehicle_when_plate_number_matches()
    {
        $this->actingAs($this->customer);
        
        // Create a vehicle first
        $vehicle = Vehicle::create([
            'id' => Str::uuid(),
            'user_id' => $this->customer->id,
            'plate_number' => 'XYZ789',
            'make' => 'Honda',
            'manufacturer' => 'Honda',
            'model' => 'Accord',
            'year' => '2020'
        ]);
        
        // Book a service using the same plate number
        $response = $this->post(route('services.booking.store'), [
            'service_id' => $this->service->id,
            'booking_date' => now()->addDays(3)->format('Y-m-d'),
            'booking_time' => now()->addDays(3)->format('H:i'),
            'payment_method' => 'wallet',
            'plate_number' => 'XYZ789',
            'vehicle_make' => 'Honda', 
            'vehicle_manufacturer' => 'Honda',
            'vehicle_model' => 'Accord',
            'vehicle_year' => '2020'
        ]);
        
        // Check that only one vehicle exists with this plate
        $this->assertEquals(1, Vehicle::where('plate_number', 'XYZ789')->count());
        
        // Check if the booking has correct vehicle_id
        $booking = ServiceBooking::latest('created_at')->first();
        $this->assertEquals($vehicle->id, $booking->vehicle_id);
    }
    
    /** @test */
    public function delivery_agent_can_update_rfid_status()
    {
        // Create vehicle
        $vehicle = Vehicle::create([
            'id' => Str::uuid(),
            'user_id' => $this->customer->id,
            'plate_number' => 'TEST123',
            'make' => 'BMW',
            'manufacturer' => 'BMW',
            'model' => 'X5',
            'year' => '2023'
        ]);
        
        // Create a service booking with paid status
        $booking = ServiceBooking::create([
            'id' => Str::uuid(),
            'user_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'vehicle_id' => $vehicle->id,
            'vehicle_make' => 'BMW',
            'vehicle_manufacturer' => 'BMW',
            'vehicle_model' => 'X5',
            'vehicle_year' => '2023',
            'plate_number' => 'TEST123',
            'booking_date' => now()->addDays(5),
            'booking_time' => now()->addDays(5),
            'base_price' => 150.00,
            'vat_amount' => 22.50,
            'total_amount' => 172.50,
            'payment_method' => 'wallet',
            'payment_status' => 'paid',
            'status' => 'paid',
            'delivery_status' => 'pending',
            'reference_number' => 'TEST-' . Str::random(8)
        ]);
        
        // Login as delivery agent
        $this->actingAs($this->deliveryAgent);
        
        // Update RFID
        $response = $this->post(route('admin.delivery.update-rfid', $booking->id), [
            'rfid_number' => 'RFID987654321'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);
        
        // Check if the booking was updated
        $this->assertDatabaseHas('service_bookings', [
            'id' => $booking->id,
            'rfid_number' => 'RFID987654321',
            'delivery_status' => 'delivered'
        ]);
    }
    
    /** @test */
    public function delivery_agent_cannot_update_already_delivered_rfid()
    {
        // Create vehicle
        $vehicle = Vehicle::create([
            'id' => Str::uuid(),
            'user_id' => $this->customer->id,
            'plate_number' => 'DELIV123',
            'make' => 'Lexus',
            'manufacturer' => 'Lexus',
            'model' => 'ES',
            'year' => '2023'
        ]);
        
        // Create a service booking with delivered status
        $booking = ServiceBooking::create([
            'id' => Str::uuid(),
            'user_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'vehicle_id' => $vehicle->id,
            'vehicle_make' => 'Lexus',
            'vehicle_manufacturer' => 'Lexus',
            'vehicle_model' => 'ES',
            'vehicle_year' => '2023',
            'plate_number' => 'DELIV123',
            'booking_date' => now()->addDays(5),
            'booking_time' => now()->addDays(5),
            'base_price' => 150.00,
            'vat_amount' => 22.50,
            'total_amount' => 172.50,
            'payment_method' => 'wallet',
            'payment_status' => 'paid',
            'status' => 'paid',
            'delivery_status' => 'delivered',
            'rfid_number' => 'RFID123456789',
            'reference_number' => 'TEST-' . Str::random(8)
        ]);
        
        // Login as delivery agent
        $this->actingAs($this->deliveryAgent);
        
        // Try to update RFID
        $response = $this->post(route('admin.delivery.update-rfid', $booking->id), [
            'rfid_number' => 'NEWRFID987654'
        ]);
        
        $response->assertStatus(422)
            ->assertJson([
                'success' => false
            ]);
        
        // Check if the booking was not updated
        $this->assertDatabaseHas('service_bookings', [
            'id' => $booking->id,
            'rfid_number' => 'RFID123456789',
            'delivery_status' => 'delivered'
        ]);
    }
}
