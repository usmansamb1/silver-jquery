<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test updating profile data without an avatar.
     */
    public function test_user_can_update_profile_without_avatar(): void
    {
        $user = User::factory()->create([
            'registration_type' => 'personal'
        ]);

        $updateData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'gender' => 'male',
            'region' => 'Test Region'
        ];

        $response = $this->actingAs($user)->put(route('profile.update'), $updateData);

        $response->assertRedirect(route('profile.show'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $updateData['name'],
            'email' => $updateData['email'],
            'gender' => $updateData['gender'],
            'region' => $updateData['region']
        ]);
    }

    /**
     * Test updating profile data with an avatar.
     */
    public function test_user_can_update_profile_with_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'registration_type' => 'personal'
        ]);

        $updateData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'gender' => 'female',
            'region' => 'Another Region',
            'avatar' => UploadedFile::fake()->image('avatar.jpg', 100, 100)
        ];

        $response = $this->actingAs($user)->put(route('profile.update'), $updateData);

        $response->assertRedirect(route('profile.show'));
        $response->assertSessionHas('success');
        
        // Refresh user model to get updated data
        $user->refresh(); 
        
        $this->assertNotNull($user->avatar);
        Storage::disk('public')->assertExists($user->avatar);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $updateData['name'],
            'email' => $updateData['email'],
            'avatar' => $user->avatar // Check if the path is saved
        ]);
    }

    /**
     * Test updating company profile data.
     */
    public function test_company_user_can_update_profile(): void
    {
        $user = User::factory()->create([
            'registration_type' => 'company'
        ]);

        $updateData = [
            'email' => $this->faker->unique()->safeEmail,
            'company_type' => 'private',
            'company_name' => $this->faker->company,
            'cr_number' => $this->faker->numerify('##########'),
            'vat_number' => $this->faker->numerify('##############'),
            'city' => $this->faker->city,
            'building_number' => $this->faker->buildingNumber,
            'zip_code' => $this->faker->postcode,
            'company_region' => $this->faker->state,
            'phone' => $this->faker->phoneNumber,
        ];

        $response = $this->actingAs($user)->put(route('profile.update'), $updateData);

        $response->assertRedirect(route('profile.show'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', array_merge(['id' => $user->id], $updateData));
    }
    
    /**
     * Test password confirmation requirement for certain roles.
     */
    public function test_password_confirmation_is_required_for_admin_update(): void
    {
        $user = User::factory()->create([
            'registration_type' => 'personal',
            'password' => Hash::make('password123')
        ]);
        $user->assignRole('admin'); // Assign a role that requires password

        $updateData = [
            'name' => 'Admin Test Name',
            'email' => 'admin.test@example.com',
            'gender' => 'male',
            'region' => 'Admin Region'
            // No password provided
        ];

        $response = $this->actingAs($user)->put(route('profile.update'), $updateData);
        
        // Should redirect back with errors because password is required
        $response->assertStatus(302);
        $response->assertSessionHasErrors('password');

        // Now try with the correct password
        $updateData['password'] = 'password123';
        $response = $this->actingAs($user)->put(route('profile.update'), $updateData);
        
        $response->assertRedirect(route('profile.show'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $updateData['name']
        ]);
    }

     /**
     * Test updating profile with cropped avatar data.
     * Note: This simulates receiving Base64 data, actual cropping happens client-side.
     */
    public function test_user_can_update_profile_with_cropped_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'registration_type' => 'personal'
        ]);

        // Generate a fake Base64 image string
        $fakeImage = 'data:image/png;base64,' . base64_encode(file_get_contents(UploadedFile::fake()->image('avatar.png')->getPathname()));

        $updateData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'gender' => 'female',
            'region' => 'Cropped Region',
            'cropped_avatar' => $fakeImage // Send Base64 data instead of file
        ];

        $response = $this->actingAs($user)->put(route('profile.update'), $updateData);

        $response->assertRedirect(route('profile.show'));
        $response->assertSessionHas('success');
        
        $user->refresh(); 
        
        $this->assertNotNull($user->avatar);
        $this->assertStringContainsString('.png', $user->avatar); // Check if it saved as png
        Storage::disk('public')->assertExists($user->avatar);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $updateData['name'],
            'avatar' => $user->avatar
        ]);
    }
} 