<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\Wallet;
use App\Models\RfidTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Str;

class RfidRechargeTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $wallet;
    
    public function setUp(): void
    {
        parent::setUp();
        
        // Create a user for testing
        $this->user = User::factory()->create();
        
        // Create a wallet for the user with balance
        $this->wallet = Wallet::create([
            'user_id' => $this->user->id,
            'balance' => 1000.00,
            'status' => 'active'
        ]);
        
        $this->actingAs($this->user);
    }

    /** @test */
    public function user_can_view_recharge_form()
    {
        // Create a vehicle with RFID
        $vehicle = Vehicle::factory()->create([
            'user_id' => $this->user->id,
            'rfid_number' => 'RFID' . $this->faker->numberBetween(1000, 9999),
            'rfid_status' => 'active',
            'rfid_balance' => 50.00,
        ]);

        $response = $this->get(route('rfid.recharge'));
        
        $response->assertStatus(200);
        $response->assertViewIs('rfid.recharge');
        $response->assertSee($vehicle->plate_number);
        $response->assertSee('Recharge RFID');
    }
    
    /** @test */
    public function user_can_recharge_single_rfid_from_wallet()
    {
        // Create a vehicle with RFID
        $vehicle = Vehicle::factory()->create([
            'user_id' => $this->user->id,
            'rfid_number' => 'RFID' . $this->faker->numberBetween(1000, 9999),
            'rfid_status' => 'active',
            'rfid_balance' => 50.00,
        ]);
        
        $initialBalance = $vehicle->rfid_balance;
        $rechargeAmount = 100;
        
        $response = $this->post(route('rfid.process-recharge'), [
            'vehicles' => [$vehicle->id],
            'amount' => $rechargeAmount,
            'payment_method' => 'wallet',
        ]);
        
        $response->assertRedirect(route('rfid.index'));
        $response->assertSessionHas('success');
        
        // Verify RFID balance was updated
        $vehicle->refresh();
        $this->assertEquals($initialBalance + $rechargeAmount, $vehicle->rfid_balance);
        
        // Verify wallet balance was reduced
        $this->wallet->refresh();
        $this->assertEquals(1000 - $rechargeAmount, $this->wallet->balance);
        
        // Verify transaction record was created
        $this->assertDatabaseHas('rfid_transactions', [
            'vehicle_id' => $vehicle->id,
            'user_id' => $this->user->id,
            'amount' => $rechargeAmount,
            'payment_method' => 'wallet',
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);
    }
    
    /** @test */
    public function user_can_recharge_multiple_rfids_from_wallet()
    {
        // Create multiple vehicles with RFID
        $vehicle1 = Vehicle::factory()->create([
            'user_id' => $this->user->id,
            'rfid_number' => 'RFID' . $this->faker->numberBetween(1000, 9999),
            'rfid_status' => 'active',
            'rfid_balance' => 50.00,
        ]);
        
        $vehicle2 = Vehicle::factory()->create([
            'user_id' => $this->user->id,
            'rfid_number' => 'RFID' . $this->faker->numberBetween(1000, 9999),
            'rfid_status' => 'active',
            'rfid_balance' => 75.00,
        ]);
        
        $initialBalance1 = $vehicle1->rfid_balance;
        $initialBalance2 = $vehicle2->rfid_balance;
        $rechargeAmount = 100;
        $totalRechargeAmount = $rechargeAmount * 2; // For two vehicles
        
        $response = $this->post(route('rfid.process-recharge'), [
            'vehicles' => [$vehicle1->id, $vehicle2->id],
            'amount' => $rechargeAmount,
            'payment_method' => 'wallet',
        ]);
        
        $response->assertRedirect(route('rfid.index'));
        $response->assertSessionHas('success');
        
        // Verify RFID balances were updated
        $vehicle1->refresh();
        $vehicle2->refresh();
        $this->assertEquals($initialBalance1 + $rechargeAmount, $vehicle1->rfid_balance);
        $this->assertEquals($initialBalance2 + $rechargeAmount, $vehicle2->rfid_balance);
        
        // Verify wallet balance was reduced by the total amount
        $this->wallet->refresh();
        $this->assertEquals(1000 - $totalRechargeAmount, $this->wallet->balance);
        
        // Verify transaction records were created
        $this->assertDatabaseHas('rfid_transactions', [
            'vehicle_id' => $vehicle1->id,
            'user_id' => $this->user->id,
            'amount' => $rechargeAmount,
            'payment_method' => 'wallet',
        ]);
        
        $this->assertDatabaseHas('rfid_transactions', [
            'vehicle_id' => $vehicle2->id,
            'user_id' => $this->user->id,
            'amount' => $rechargeAmount,
            'payment_method' => 'wallet',
        ]);
    }
    
    /** @test */
    public function insufficient_wallet_balance_prevents_recharge()
    {
        // Create a vehicle with RFID
        $vehicle = Vehicle::factory()->create([
            'user_id' => $this->user->id,
            'rfid_number' => 'RFID' . $this->faker->numberBetween(1000, 9999),
            'rfid_status' => 'active',
            'rfid_balance' => 50.00,
        ]);
        
        // Set wallet balance to a low amount
        $this->wallet->update(['balance' => 50.00]);
        
        $response = $this->post(route('rfid.process-recharge'), [
            'vehicles' => [$vehicle->id],
            'amount' => 100, // More than wallet balance
            'payment_method' => 'wallet',
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        // Verify RFID balance was not updated
        $vehicle->refresh();
        $this->assertEquals(50.00, $vehicle->rfid_balance);
        
        // Verify wallet balance was not changed
        $this->wallet->refresh();
        $this->assertEquals(50.00, $this->wallet->balance);
    }
} 