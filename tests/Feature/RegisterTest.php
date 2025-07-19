<?php

namespace Tests\Feature;

use App\Models\PendingRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Test that registration requires Terms and Conditions agreement.
     */
    public function test_registration_requires_terms_agreement()
    {
        $userData = [
            'registration_type' => 'personal',
            'mobile' => '0512345678',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'region' => 'Central',
            // Deliberately omitting terms_agree
        ];

        $response = $this->postJson('/api/register/otp', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['terms_agree']);
    }

    /**
     * Test that OTP is sent during registration.
     */
    public function test_otp_is_sent_during_registration()
    {
        Notification::fake();
        
        $userData = [
            'registration_type' => 'personal',
            'mobile' => '0512345678',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'region' => 'Central',
            'terms_agree' => 'on',
        ];

        $response = $this->postJson('/api/register/otp', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'temp_token'
            ]);

        $this->assertDatabaseHas('pending_registrations', [
            'mobile' => $userData['mobile']
        ]);
    }

    /**
     * Test that OTP verification is required during registration.
     */
    public function test_otp_verification_is_required()
    {
        $userData = [
            'registration_type' => 'personal',
            'mobile' => '0512345678',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'region' => 'Central',
            'terms_agree' => 'on',
            // Missing OTP verification
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['temp_token', 'otp_verified']);
    }

    /**
     * Test that invalid OTP fails verification.
     */
    public function test_invalid_otp_fails_verification()
    {
        // Create a pending registration with a known OTP
        $pendingReg = PendingRegistration::create([
            'registration_data' => json_encode([
                'registration_type' => 'personal',
                'mobile' => '0512345678',
                'name' => 'Test User',
                'email' => 'test@example.com',
                'region' => 'Central',
            ]),
            'mobile' => '0512345678',
            'otp' => '1234',
            'otp_created_at' => now(),
        ]);

        // Try to verify with wrong OTP
        $response = $this->postJson('/api/register/verify-otp', [
            'temp_token' => $pendingReg->temp_token,
            'otp' => '5678' // Wrong OTP
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid OTP.'
            ]);
    }

    /**
     * Test complete successful registration flow.
     */
    public function test_successful_registration_flow()
    {
        Notification::fake();
        
        // Step 1: Send OTP
        $userData = [
            'registration_type' => 'personal',
            'mobile' => '0512345678',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'region' => 'Central',
            'terms_agree' => 'on',
        ];

        $response = $this->postJson('/api/register/otp', $userData);
        $response->assertStatus(201);
        
        $tempToken = $response->json('temp_token');
        
        // Get the OTP from the database
        $pendingReg = PendingRegistration::where('temp_token', $tempToken)->first();
        $otp = $pendingReg->otp;
        
        // Step 2: Verify OTP
        $response = $this->postJson('/api/register/verify-otp', [
            'temp_token' => $tempToken,
            'otp' => $otp
        ]);
        
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Registration complete. OTP verified successfully.'
            ]);
        
        // Verify user was created
        $this->assertDatabaseHas('users', [
            'mobile' => $userData['mobile'],
            'name' => $userData['name'],
            'email' => $userData['email'],
        ]);
        
        // Verify pending registration was deleted
        $this->assertDatabaseMissing('pending_registrations', [
            'temp_token' => $tempToken
        ]);
    }
} 