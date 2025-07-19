<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TestUsersAndRolesSeeder extends Seeder
{
    /**
     * Run the database seeds to create test users with different roles.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create roles
        $roles = [
            'admin',
            'finance',
            'audit',
            'it',
            'contractor', 
            'customer',
            'validation',
            'activation'
        ];

        foreach ($roles as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if (!$role) {
                Role::create(['name' => $roleName, 'guard_name' => 'web']);
                $this->command->info("Role {$roleName} created");
            } else {
                $this->command->info("Role {$roleName} already exists");
            }
        }

        // 2. Create test users
        $users = [
            [
                'name' => 'Admin One',
                'email' => 'admin1@test.com',
                'password' => 'password123',
                'mobile' => '0512345678',
                'phone' => '0112345678',
                'gender' => 'male',
                'company_name' => 'Test Company',
                'company_type' => 'private',
                'city' => 'Riyadh',
                'roles' => ['admin']
            ],
            [
                'name' => 'Admin Two',
                'email' => 'admin2@test.com',
                'password' => 'password123',
                'mobile' => '0512345679',
                'phone' => '0112345679',
                'gender' => 'female',
                'company_name' => 'Test Company',
                'company_type' => 'private', 
                'city' => 'Jeddah',
                'roles' => ['admin']
            ],
            [
                'name' => 'Finance User',
                'email' => 'finance@test.com',
                'password' => 'password123',
                'mobile' => '0512345680',
                'phone' => '0112345680', 
                'gender' => 'male',
                'company_name' => 'Test Company',
                'company_type' => 'private',
                'city' => 'Riyadh',
                'roles' => ['finance']
            ],
            [
                'name' => 'Audit User',
                'email' => 'audit@test.com',
                'password' => 'password123',
                'mobile' => '0512345681',
                'phone' => '0112345681',
                'gender' => 'female',
                'company_name' => 'Test Company',
                'company_type' => 'private',
                'city' => 'Dammam',
                'roles' => ['audit']
            ],
            [
                'name' => 'Validation User',
                'email' => 'validation@test.com',
                'password' => 'password123',
                'mobile' => '0512345682',
                'phone' => '0112345682',
                'gender' => 'male',
                'company_name' => 'Test Company',
                'company_type' => 'private',
                'city' => 'Riyadh',
                'roles' => ['validation']
            ]
        ];

        foreach ($users as $userData) {
            $roles = $userData['roles'];
            unset($userData['roles']);
            
            $userData['password'] = Hash::make($userData['password']);
            $userData['is_active'] = true;
            $userData['registration_type'] = 'personal';
            
            $existingUser = User::where('email', $userData['email'])->first();
            if ($existingUser) {
                $existingUser->update($userData);
                $existingUser->syncRoles($roles);
                $this->command->info("User {$userData['email']} updated with roles: " . implode(', ', $roles));
            } else {
                $user = User::create($userData);
                $user->assignRole($roles);
                $this->command->info("User {$userData['email']} created with roles: " . implode(', ', $roles));
            }
        }

        $this->verifyUsersAndRoles();
    }

    /**
     * Verify and output user role assignments
     */
    private function verifyUsersAndRoles(): void
    {
        $this->command->info("\nVerifying users and their roles:");
        $this->command->info("=====================================");
        
        $users = User::whereIn('email', [
            'admin1@test.com', 
            'admin2@test.com', 
            'finance@test.com', 
            'audit@test.com', 
            'validation@test.com'
        ])->get();
        
        foreach ($users as $user) {
            $roles = $user->getRoleNames()->toArray();
            $this->command->info("User: {$user->name} ({$user->email})");
            $this->command->info("Roles: " . implode(', ', $roles));
            $this->command->info("-------------------------------------");
        }
    }
} 