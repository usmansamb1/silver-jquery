<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Service;
use App\Models\ServiceBooking;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ServiceBookingFormTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $service;
    protected $wallet;

    protected function setUp(): void
    {
        parent::setUp();

        // Create customer role
        $role = Role::create([
            'name' => 'customer',
            'description' => 'Customer Role'
        ]);

        // Create user with customer role
        $this->user = User::create([
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'password' => bcrypt('password'),
            'registration_type' => 'individual',
            'company_type' => null,
            'company_name' => null,
            'address' => '123 Test Street',
            'city' => 'Test City',
            'country' => 'Test Country',
            'is_active' => true,
        ]);
        
        $this->user->roles()->attach($role->id);

        // Create a test service
        $this->service = Service::create([
            'name' => 'Oil Change',
            'description' => 'Regular oil change service',
            'base_price' => 100.00,
            'is_active' => true,
            'service_type' => 'maintenance',
            'estimated_duration' => 60,
            'vat_percentage' => 15
        ]);

        // Create wallet for the user
        $this->wallet = Wallet::create([
            'user_id' => $this->user->id,
            'balance' => 500.00
        ]);
    }

    /** @test */
    public function customer_can_view_booking_form()
    {
        $this->actingAs($this->user)
            ->get(route('services.booking.create', $this->service))
            ->assertStatus(200)
            ->assertViewIs('services.booking.create')
            ->assertSee($this->service->name);
    }

    /** @test */
    public function booking_form_validates_empty_fields()
    {
        $this->actingAs($this->user)
            ->post(route('services.booking.store'), [])
            ->assertSessionHasErrors([
                'service_id', 'vehicle_make', 'vehicle_model', 
                'vehicle_year', 'plate_number', 'booking_date', 
                'booking_time', 'payment_method'
            ]);

        $this->assertDatabaseCount('service_bookings', 0);
    }

    /** @test */
    public function booking_form_validates_past_dates()
    {
        $this->actingAs($this->user)
            ->post(route('services.booking.store'), [
                'service_id' => $this->service->id,
                'vehicle_make' => 'Toyota',
                'vehicle_model' => 'Camry',
                'vehicle_year' => 2020,
                'plate_number' => 'ABC123',
                'booking_date' => now()->subDay()->format('Y-m-d'),
                'booking_time' => '14:00',
                'payment_method' => 'wallet'
            ])
            ->assertSessionHasErrors('booking_date');

        $this->assertDatabaseCount('service_bookings', 0);
    }

    /** @test */
    public function customer_can_book_service_with_wallet_payment()
    {
        $response = $this->actingAs($this->user)
            ->post(route('services.booking.store'), [
                'service_id' => $this->service->id,
                'vehicle_make' => 'Toyota',
                'vehicle_model' => 'Camry',
                'vehicle_year' => 2020,
                'plate_number' => 'ABC123',
                'booking_date' => now()->addDays(2)->format('Y-m-d'),
                'booking_time' => '14:00',
                'payment_method' => 'wallet'
            ]);

        $booking = ServiceBooking::first();
        
        $response->assertRedirect(route('services.booking.show', $booking))
            ->assertSessionHas('success');
        
        $this->assertDatabaseHas('service_bookings', [
            'user_id' => $this->user->id,
            'service_id' => $this->service->id,
            'vehicle_make' => 'Toyota',
            'vehicle_model' => 'Camry',
            'payment_method' => 'wallet',
            'booking_status' => 'pending',
            'payment_status' => 'pending'
        ]);

        // Verify price calculations
        $this->assertEquals(100.00, $booking->base_price);
        $this->assertEquals(15.00, $booking->vat_amount);
        $this->assertEquals(115.00, $booking->total_amount);
    }

    /** @test */
    public function customer_can_book_service_with_credit_card_payment()
    {
        $response = $this->actingAs($this->user)
            ->post(route('services.booking.store'), [
                'service_id' => $this->service->id,
                'vehicle_make' => 'Honda',
                'vehicle_model' => 'Accord',
                'vehicle_year' => 2019,
                'plate_number' => 'XYZ789',
                'booking_date' => now()->addDays(3)->format('Y-m-d'),
                'booking_time' => '10:00',
                'payment_method' => 'credit_card'
            ]);

        $booking = ServiceBooking::first();
        
        $response->assertRedirect(route('services.booking.show', $booking))
            ->assertSessionHas('success');
        
        $this->assertDatabaseHas('service_bookings', [
            'user_id' => $this->user->id,
            'service_id' => $this->service->id,
            'vehicle_make' => 'Honda',
            'vehicle_model' => 'Accord',
            'payment_method' => 'credit_card',
            'booking_status' => 'pending',
            'payment_status' => 'pending'
        ]);
    }

    /** @test */
    public function customer_can_view_booking_details()
    {
        // Create a booking first
        $booking = ServiceBooking::create([
            'user_id' => $this->user->id,
            'service_id' => $this->service->id,
            'vehicle_make' => 'BMW',
            'vehicle_model' => 'X5',
            'vehicle_year' => 2021,
            'plate_number' => 'LMN456',
            'booking_date' => now()->addDays(5)->format('Y-m-d'),
            'booking_time' => '16:00',
            'base_price' => $this->service->base_price,
            'vat_amount' => $this->service->base_price * ($this->service->vat_percentage / 100),
            'total_amount' => $this->service->calculateTotalPrice(),
            'payment_method' => 'wallet',
            'payment_status' => 'pending',
            'booking_status' => 'pending',
            'reference_number' => 'SB-' . \Illuminate\Support\Str::random(10)
        ]);

        $this->actingAs($this->user)
            ->get(route('services.booking.show', $booking))
            ->assertStatus(200)
            ->assertViewIs('services.booking.show')
            ->assertSee($booking->reference_number)
            ->assertSee($this->service->name);
    }
} 