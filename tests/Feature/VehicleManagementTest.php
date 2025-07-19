<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\RfidTransfer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification;

class VehicleManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        
        // Create a user for testing
        $this->user = User::factory()->create([
            'mobile' => '966500000000', // For SMS testing
        ]);
        $this->actingAs($this->user);
    }

    /** @test */
    public function user_can_view_vehicles_list()
    {
        // Create some vehicles for the user
        Vehicle::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->get(route('vehicles.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('vehicles.index');
        $response->assertViewHas('vehicles');
        $response->assertSee('My Vehicles');
    }

    /** @test */
    public function user_can_create_new_vehicle()
    {
        $vehicleData = [
            'plate_number' => $this->faker->bothify('####???'),
            'make' => $this->faker->word,
            'manufacturer' => $this->faker->company,
            'model' => $this->faker->word,
            'year' => (string) $this->faker->year,
        ];

        $response = $this->post(route('vehicles.store'), $vehicleData);
        
        $response->assertRedirect(route('vehicles.index'));
        $response->assertSessionHas('success');
        
        // Verify the vehicle was created in the database
        $this->assertDatabaseHas('vehicles', [
            'user_id' => $this->user->id,
            'plate_number' => $vehicleData['plate_number'],
            'status' => 'active',
        ]);
    }

    /** @test */
    public function user_can_edit_vehicle()
    {
        // Create a vehicle for testing
        $vehicle = Vehicle::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $updatedData = [
            'plate_number' => $this->faker->bothify('####???'),
            'make' => 'Updated Make',
            'manufacturer' => 'Updated Manufacturer',
            'model' => 'Updated Model',
            'year' => '2023',
        ];

        $response = $this->put(route('vehicles.update', $vehicle->id), $updatedData);
        
        $response->assertRedirect(route('vehicles.index'));
        $response->assertSessionHas('success');
        
        // Verify the vehicle was updated
        $this->assertDatabaseHas('vehicles', [
            'id' => $vehicle->id,
            'user_id' => $this->user->id,
            'make' => 'Updated Make',
            'manufacturer' => 'Updated Manufacturer',
        ]);
    }

    /** @test */
    public function user_can_delete_vehicle_without_rfid()
    {
        // Create a vehicle without RFID
        $vehicle = Vehicle::factory()->create([
            'user_id' => $this->user->id,
            'rfid_number' => null,
        ]);

        $response = $this->delete(route('vehicles.destroy', $vehicle->id));
        
        $response->assertRedirect(route('vehicles.index'));
        $response->assertSessionHas('success');
        
        // Verify the vehicle was deleted
        $this->assertDatabaseMissing('vehicles', [
            'id' => $vehicle->id,
        ]);
    }

    /** @test */
    public function user_cannot_delete_vehicle_with_rfid()
    {
        // Create a vehicle with RFID
        $vehicle = Vehicle::factory()->create([
            'user_id' => $this->user->id,
            'rfid_number' => 'RFID' . $this->faker->numberBetween(1000, 9999),
            'rfid_status' => 'active',
        ]);

        $response = $this->delete(route('vehicles.destroy', $vehicle->id));
        
        $response->assertRedirect(route('vehicles.index'));
        $response->assertSessionHas('error');
        
        // Verify the vehicle was not deleted
        $this->assertDatabaseHas('vehicles', [
            'id' => $vehicle->id,
        ]);
    }

    /** @test */
    public function user_can_initiate_rfid_transfer()
    {
        // Create source vehicle with RFID
        $sourceVehicle = Vehicle::factory()->create([
            'user_id' => $this->user->id,
            'rfid_number' => 'RFID' . $this->faker->numberBetween(1000, 9999),
            'rfid_status' => 'active',
            'rfid_balance' => 100.00,
        ]);
        
        // Create target vehicle without RFID
        $targetVehicle = Vehicle::factory()->create([
            'user_id' => $this->user->id,
            'rfid_number' => null,
        ]);
        
        // Mock the notification for testing
        Notification::fake();
        
        $response = $this->post(route('rfid.initiate-transfer'), [
            'source_vehicle_id' => $sourceVehicle->id,
            'target_vehicle_id' => $targetVehicle->id,
            'notes' => 'Test transfer',
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        // Verify transfer record was created
        $this->assertDatabaseHas('rfid_transfers', [
            'user_id' => $this->user->id,
            'source_vehicle_id' => $sourceVehicle->id,
            'target_vehicle_id' => $targetVehicle->id,
            'rfid_number' => $sourceVehicle->rfid_number,
            'status' => 'pending',
        ]);
        
        // Verify notification was sent
        Notification::assertSentTo(
            $this->user,
            \App\Notifications\RfidTransferOtp::class
        );
    }

    /** @test */
    public function user_can_verify_rfid_transfer_with_otp()
    {
        // Create source vehicle with RFID
        $sourceVehicle = Vehicle::factory()->create([
            'user_id' => $this->user->id,
            'rfid_number' => 'RFID' . $this->faker->numberBetween(1000, 9999),
            'rfid_status' => 'active',
            'rfid_balance' => 100.00,
        ]);
        
        // Create target vehicle without RFID
        $targetVehicle = Vehicle::factory()->create([
            'user_id' => $this->user->id,
            'rfid_number' => null,
        ]);
        
        // Create a pending transfer
        $otpCode = '123456';
        $transfer = RfidTransfer::create([
            'user_id' => $this->user->id,
            'source_vehicle_id' => $sourceVehicle->id,
            'target_vehicle_id' => $targetVehicle->id,
            'rfid_number' => $sourceVehicle->rfid_number,
            'otp_code' => $otpCode,
            'otp_expires_at' => now()->addMinutes(10),
            'status' => 'pending',
            'transfer_details' => [
                'source_vehicle' => [
                    'plate_number' => $sourceVehicle->plate_number,
                    'make' => $sourceVehicle->make,
                    'model' => $sourceVehicle->model,
                    'rfid_balance' => $sourceVehicle->rfid_balance,
                ],
                'target_vehicle' => [
                    'plate_number' => $targetVehicle->plate_number,
                    'make' => $targetVehicle->make,
                    'model' => $targetVehicle->model,
                ],
            ],
        ]);
        
        $response = $this->post(route('rfid.verify-transfer.submit', $transfer->id), [
            'otp_code' => $otpCode,
        ]);
        
        $response->assertRedirect(route('rfid.index'));
        $response->assertSessionHas('success');
        
        // Verify source vehicle no longer has RFID
        $this->assertDatabaseHas('vehicles', [
            'id' => $sourceVehicle->id,
            'rfid_number' => null,
        ]);
        
        // Verify target vehicle now has RFID
        $this->assertDatabaseHas('vehicles', [
            'id' => $targetVehicle->id,
            'rfid_number' => $sourceVehicle->rfid_number,
            'rfid_balance' => $sourceVehicle->rfid_balance,
        ]);
        
        // Verify transfer record is marked as completed
        $this->assertDatabaseHas('rfid_transfers', [
            'id' => $transfer->id,
            'status' => 'completed',
        ]);
    }
} 