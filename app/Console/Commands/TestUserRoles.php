<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class TestUserRoles extends Command
{
    protected $signature = 'test:user-roles 
                            {--create : Create test users and roles}
                            {--verify : Verify roles for test users}
                            {--list : List all test users}';

    protected $description = 'Create and verify test users with various roles';

    public function handle()
    {
        if ($this->option('create')) {
            $this->createTestUsers();
        } elseif ($this->option('verify')) {
            $this->verifyTestUsers();
        } elseif ($this->option('list')) {
            $this->listTestUsers();
        } else {
            // If no options provided, show help
            $this->info("Please provide one of the following options:");
            $this->info("  --create : Create test users and roles");
            $this->info("  --verify : Verify roles for test users");
            $this->info("  --list : List all test users");
        }

        return Command::SUCCESS;
    }

    protected function createTestUsers()
    {
        $this->info("Creating roles...");
        
        // Create roles
        $roles = ['admin', 'finance', 'audit', 'validation'];
        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $this->info("Role $roleName is ready.");
        }
        
        // Create users
        $this->info("\nCreating test users...");
        
        $users = [
            [
                'name' => 'Admin One',
                'email' => 'admin1@test.com',
                'password' => bcrypt('password123'),
                'mobile' => '0512345678',
                'phone' => '0112345678',
                'gender' => 'male',
                'company_name' => 'Test Company',
                'company_type' => 'private',
                'city' => 'Riyadh',
                'is_active' => true,
                'registration_type' => 'personal',
                'roles' => ['admin'],
            ],
            [
                'name' => 'Admin Two', 
                'email' => 'admin2@test.com',
                'password' => bcrypt('password123'),
                'mobile' => '0512345679',
                'phone' => '0112345679',
                'gender' => 'female',
                'company_name' => 'Test Company',
                'company_type' => 'private',
                'city' => 'Jeddah',
                'is_active' => true,
                'registration_type' => 'personal',
                'roles' => ['admin'],
            ],
            [
                'name' => 'Finance User',
                'email' => 'finance@test.com',
                'password' => bcrypt('password123'),
                'mobile' => '0512345680',
                'phone' => '0112345680',
                'gender' => 'male',
                'company_name' => 'Test Company',
                'company_type' => 'private',
                'city' => 'Riyadh',
                'is_active' => true,
                'registration_type' => 'personal',
                'roles' => ['finance'],
            ],
            [
                'name' => 'Audit User',
                'email' => 'audit@test.com',
                'password' => bcrypt('password123'),
                'mobile' => '0512345681',
                'phone' => '0112345681',
                'gender' => 'female',
                'company_name' => 'Test Company',
                'company_type' => 'private',
                'city' => 'Dammam',
                'is_active' => true,
                'registration_type' => 'personal',
                'roles' => ['audit'],
            ],
            [
                'name' => 'Validation User',
                'email' => 'validation@test.com',
                'password' => bcrypt('password123'),
                'mobile' => '0512345682',
                'phone' => '0112345682',
                'gender' => 'male',
                'company_name' => 'Test Company',
                'company_type' => 'private',
                'city' => 'Riyadh',
                'is_active' => true,
                'registration_type' => 'personal',
                'roles' => ['validation'],
            ],
        ];
        
        $count = 0;
        
        foreach ($users as $userData) {
            $roles = $userData['roles'];
            unset($userData['roles']);
            
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
            
            // Clear existing roles and assign new ones
            DB::table('model_has_roles')->where('model_id', $user->id)->delete();
            
            foreach ($roles as $roleName) {
                $role = Role::where('name', $roleName)->first();
                if ($role) {
                    DB::table('model_has_roles')->insert([
                        'role_id' => $role->id,
                        'model_type' => 'App\\Models\\User',
                        'model_id' => $user->id
                    ]);
                }
            }
            
            $count++;
            $this->info("Created user: {$userData['name']} ({$userData['email']}) with roles: " . implode(', ', $roles));
        }
        
        $this->info("\nCreated $count test users successfully.");
    }
    
    protected function verifyTestUsers()
    {
        $this->info("Verifying test users and their roles:");
        $this->info("=====================================");
        
        $users = User::whereIn('email', [
            'admin1@test.com', 
            'admin2@test.com', 
            'finance@test.com', 
            'audit@test.com', 
            'validation@test.com'
        ])->get();
        
        $table = [];
        
        foreach ($users as $user) {
            $roles = DB::table('model_has_roles')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('model_has_roles.model_id', $user->id)
                ->pluck('roles.name')
                ->toArray();
                
            $table[] = [
                'Name' => $user->name,
                'Email' => $user->email,
                'Roles' => implode(', ', $roles),
            ];
        }
        
        $this->table(['Name', 'Email', 'Roles'], $table);
    }
    
    protected function listTestUsers()
    {
        $this->info("Listing all test users:");
        $this->info("======================");
        
        $users = User::whereIn('email', [
            'admin1@test.com', 
            'admin2@test.com', 
            'finance@test.com', 
            'audit@test.com', 
            'validation@test.com'
        ])->get();
        
        $table = [];
        
        foreach ($users as $user) {
            $table[] = [
                'ID' => $user->id,
                'Name' => $user->name,
                'Email' => $user->email,
                'Password' => 'password123',
                'Mobile' => $user->mobile,
            ];
        }
        
        $this->table(['ID', 'Name', 'Email', 'Password', 'Mobile'], $table);
    }
} 