<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds to create test users with different roles.
     */
    public function run(): void
    {
        // Create validation role if it doesn't exist
        if (!Role::where('name', 'validation')->exists()) {
            Role::create(['name' => 'validation']);
        }

        // Create admin users (2)
        $this->createUser([
            'name' => 'Admin One',
            'email' => 'admin1@test.com',
            'password' => 'password123',
            'mobile' => '0512345678',
            'company_name' => 'Test Company',
            'company_type' => 'private',
        ], ['admin']);

        $this->createUser([
            'name' => 'Admin Two',
            'email' => 'admin2@test.com',
            'password' => 'password123',
            'mobile' => '0512345679',
            'company_name' => 'Test Company',
            'company_type' => 'private',
        ], ['admin']);

        // Create finance user
        $this->createUser([
            'name' => 'Finance User',
            'email' => 'finance@test.com',
            'password' => 'password123',
            'mobile' => '0512345680',
            'company_name' => 'Test Company',
            'company_type' => 'private',
        ], ['finance']);

        // Create audit user
        $this->createUser([
            'name' => 'Audit User',
            'email' => 'audit@test.com',
            'password' => 'password123',
            'mobile' => '0512345681',
            'company_name' => 'Test Company',
            'company_type' => 'private',
        ], ['audit']);

        // Create validation users (2) 
        $this->createUser([
            'name' => 'Validation One',
            'email' => 'validation1@test.com',
            'password' => 'password123',
            'mobile' => '0512345682',
            'company_name' => 'Test Company',
            'company_type' => 'private',
        ], ['validation']);

        $this->createUser([
            'name' => 'Validation Two',
            'email' => 'validation2@test.com',
            'password' => 'password123',
            'mobile' => '0512345683',
            'company_name' => 'Test Company',
            'company_type' => 'private',
        ], ['validation']);

        // Create delivery users (2)
        $this->createUser([
            'name' => 'Delivery One',
            'email' => 'delivery1@test.com',
            'password' => 'password123',
            'mobile' => '0512345684',
            'company_name' => 'Test Company',
            'company_type' => 'private',
        ], ['delivery']);

       

        $this->command->info('Test users created successfully!');
    }

    /**
     * Helper function to create a user with specific roles
     */
    private function createUser(array $userData, array $roles): User
    {
        $userData['password'] = Hash::make($userData['password']);
        $userData['is_active'] = true;
        $userData['registration_type'] = 'personal';

        // Check if user already exists
        $existingUser = User::where('email', $userData['email'])->first();
        if ($existingUser) {
            $existingUser->update($userData);
            $existingUser->syncRoles($roles);
            return $existingUser;
        }

        // Create a new user
        $user = User::create($userData);
        $user->assignRole($roles);
        return $user;
    }
} 