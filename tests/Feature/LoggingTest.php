<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Helpers\LogHelper;
use Illuminate\Support\Facades\DB;

class LoggingTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Test that user login is logged properly
     *
     * @return void
     */
    public function test_user_login_is_logged()
    {
        // Create a user
        $user = User::factory()->create([
            'mobile' => '0590000000',
            'otp' => '1234'
        ]);

        // Make OTP verification request to simulate login
        $response = $this->postJson('/api/verify-otp', [
            'mobile' => $user->mobile,
            'otp' => '1234'
        ]);

        $response->assertStatus(200);

        // Check that login log was created
        $this->assertDatabaseHas('activity_logs', [
            'event' => 'login',
            'causer_id' => $user->id,
        ]);
    }

    /**
     * Test that OTP verification is logged
     *
     * @return void
     */
    public function test_otp_verification_is_logged()
    {
        // Create a user
        $user = User::factory()->create([
            'mobile' => '0590000000',
            'otp' => '1234'
        ]);

        // Make OTP verification request
        $response = $this->postJson('/api/verify-otp', [
            'mobile' => $user->mobile,
            'otp' => '1234'
        ]);

        $response->assertStatus(200);

        // Check that OTP verification log was created
        $this->assertDatabaseHas('activity_logs', [
            'event' => 'otp_verification',
            'causer_id' => $user->id,
        ]);
    }

    /**
     * Test that user creation is logged via observer
     *
     * @return void
     */
    public function test_user_creation_is_logged()
    {
        // Clean up logs
        DB::table('activity_logs')->truncate();
        
        // Create a user
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'mobile' => '0599999999',
        ]);

        // Check that user creation log was created
        $this->assertDatabaseHas('activity_logs', [
            'event' => 'user_created',
            'subject_id' => $user->id,
            'subject_type' => User::class,
        ]);
    }

    /**
     * Test that user profile update is logged
     *
     * @return void
     */
    public function test_user_update_is_logged()
    {
        // Create a user
        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
        ]);

        // Clear logs
        DB::table('activity_logs')->truncate();

        // Update user
        $user->name = 'Updated Name';
        $user->email = 'updated@example.com';
        $user->save();

        // Check that profile update log was created
        $this->assertDatabaseHas('activity_logs', [
            'event' => 'profile_update',
            'subject_id' => $user->id,
            'subject_type' => User::class,
        ]);
    }

    /**
     * Test that OTP updates don't trigger profile update logs
     */
    public function test_otp_update_does_not_log_profile_update()
    {
        // Create a user
        $user = User::factory()->create();

        // Clear logs
        DB::table('activity_logs')->truncate();

        // Update only OTP
        $user->otp = '5678';
        $user->otp_created_at = now();
        $user->save();

        // Verify no profile update log was created
        $this->assertDatabaseMissing('activity_logs', [
            'event' => 'profile_update',
            'subject_id' => $user->id,
        ]);
    }

    /**
     * Test direct use of LogHelper
     */
    public function test_direct_log_helper_usage()
    {
        // Create a user
        $user = User::factory()->create();

        // Clear logs
        DB::table('activity_logs')->truncate();

        // Use the LogHelper directly
        $log = LogHelper::logLogin($user, 'Test login log', ['test' => true]);

        // Verify log was created
        $this->assertDatabaseHas('activity_logs', [
            'id' => $log->id,
            'event' => 'login',
            'description' => 'Test login log',
            'causer_id' => $user->id,
        ]);

        // Test JSON properties
        $logFromDb = ActivityLog::find($log->id);
        $this->assertEquals(true, $logFromDb->properties['test']);
    }
} 