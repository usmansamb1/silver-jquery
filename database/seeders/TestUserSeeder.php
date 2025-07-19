<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles if they don't exist
        $this->createRoles();
        
        // Create test users
        $this->createUsers();
    }
    
    /**
     * Create necessary roles
     */
    private function createRoles(): void
    {
        $roles = ['admin', 'finance', 'it', 'audit', 'activation', 'validation', 'customer'];
        
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }
    
    /**
     * Create test users
     */
    private function createUsers(): void
    {
        $users = [
            [
                'id' => (string) Str::uuid(),
                'name' => 'Admin Test',
                'email' => 'admin1@test.com',
                'password' => Hash::make('password123'),
                'mobile' => '0512345700',
                'phone' => '0112345700',
                'gender' => 'male',
                'company_name' => 'Test Company',
                'company_type' => 'private',
                'city' => 'Riyadh',
                'is_active' => true,
                'registration_type' => 'personal',
                'created_at' => now(),
                'updated_at' => now(),
                'role' => 'admin'
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Finance Test',
                'email' => 'finance@test.com',
                'password' => Hash::make('password123'),
                'mobile' => '0512345701',
                'phone' => '0112345701',
                'gender' => 'male',
                'company_name' => 'Test Company',
                'company_type' => 'private',
                'city' => 'Riyadh',
                'is_active' => true,
                'registration_type' => 'personal',
                'created_at' => now(),
                'updated_at' => now(),
                'role' => 'finance'
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Audit Test',
                'email' => 'audit@test.com',
                'password' => Hash::make('password123'),
                'mobile' => '0512345702',
                'phone' => '0112345702',
                'gender' => 'male',
                'company_name' => 'Test Company',
                'company_type' => 'private',
                'city' => 'Riyadh',
                'is_active' => true,
                'registration_type' => 'personal',
                'created_at' => now(),
                'updated_at' => now(),
                'role' => 'audit'
            ],
            // New users for new roles
            [
                'id' => (string) Str::uuid(),
                'name' => 'Activation Test',
                'email' => 'activation@test.com',
                'password' => Hash::make('password123'),
                'mobile' => '0512345703',
                'phone' => '0112345703',
                'gender' => 'male',
                'company_name' => 'Test Company',
                'company_type' => 'private',
                'city' => 'Riyadh',
                'is_active' => true,
                'registration_type' => 'personal',
                'created_at' => now(),
                'updated_at' => now(),
                'role' => 'activation'
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Validation Test',
                'email' => 'validation@test.com',
                'password' => Hash::make('password123'),
                'mobile' => '0512345704',
                'phone' => '0112345704',
                'gender' => 'male',
                'company_name' => 'Test Company',
                'company_type' => 'private',
                'city' => 'Riyadh',
                'is_active' => true,
                'registration_type' => 'personal',
                'created_at' => now(),
                'updated_at' => now(),
                'role' => 'validation'
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Customer Test',
                'email' => 'customer@test.com',
                'password' => Hash::make('password123'),
                'mobile' => '0512345705',
                'phone' => '0112345705',
                'gender' => 'male',
                'company_name' => 'Test Company',
                'company_type' => 'private',
                'city' => 'Riyadh',
                'is_active' => true,
                'registration_type' => 'personal',
                'created_at' => now(),
                'updated_at' => now(),
                'role' => 'customer'
            ]
        ];
        
        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);
            
            // Check if user already exists
            $existingUser = User::where('email', $userData['email'])->first();
            
            if (!$existingUser) {
                try {
                    // Remove customer_no from userData - let the model generate it automatically
                    unset($userData['customer_no']);
                    
                    // Insert user
                    $user = User::create($userData);
                    
                    // Assign role
                    $user->assignRole($role);
                    
                    echo "Created user: {$userData['email']} with role: {$role}\n";
                } catch (\Exception $e) {
                    echo "Error creating user {$userData['email']}: {$e->getMessage()}\n";
                }
            } else {
                echo "User {$userData['email']} already exists\n";
                
                try {
                    // Make sure the user has the role - avoid duplicate key error
                    if (!$existingUser->hasRole($role)) {
                        $existingUser->assignRole($role);
                        echo "Assigned role {$role} to {$userData['email']}\n";
                    } else {
                        echo "User {$userData['email']} already has role {$role}\n";
                    }
                } catch (\Exception $e) {
                    echo "Error assigning role {$role} to {$userData['email']}: {$e->getMessage()}\n";
                }
            }
        }
    }
} 