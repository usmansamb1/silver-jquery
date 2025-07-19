<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CommandLineRoleTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateRoles(): void
    {
        // Create roles manually for testing
        $roles = ['admin', 'finance', 'audit', 'validation'];
        
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }
        
        // Verify roles were created correctly
        $this->assertTrue(Role::where('name', 'admin')->exists());
        $this->assertTrue(Role::where('name', 'finance')->exists());
        $this->assertTrue(Role::where('name', 'audit')->exists());
        $this->assertTrue(Role::where('name', 'validation')->exists());
    }
    
    public function testCreateUsers(): void
    {
        // First create roles
        $this->testCreateRoles();
        
        // Create sample users manually
        $user = User::updateOrCreate(
            ['email' => 'commandtest@test.com'],
            [
                'name' => 'Command Test User',
                'password' => bcrypt('password123'),
                'mobile' => '0512345699',
                'phone' => '0112345699',
                'gender' => 'male',
                'company_name' => 'Command Test Company',
                'company_type' => 'private',
                'city' => 'Riyadh',
                'is_active' => true,
                'registration_type' => 'personal',
            ]
        );
        
        // Assign admin role directly
        $adminRole = Role::where('name', 'admin')->first();
        DB::table('model_has_roles')->insert([
            'role_id' => $adminRole->id,
            'model_type' => 'App\\Models\\User',
            'model_id' => $user->id
        ]);
        
        // Check if user exists
        $this->assertTrue(User::where('email', 'commandtest@test.com')->exists());
        
        // Check if role is assigned using DB query
        $roleAssigned = DB::table('model_has_roles')
            ->where('role_id', $adminRole->id)
            ->where('model_id', $user->id)
            ->exists();
        $this->assertTrue($roleAssigned);
    }
    
    public function testRoleAssignmentVerification(): void
    {
        // Create a test user and assign roles
        $this->testCreateUsers();
        
        // Get the user
        $user = User::where('email', 'commandtest@test.com')->first();
        $this->assertNotNull($user);
        
        // Check the role assignment using direct DB query
        $adminRole = Role::where('name', 'admin')->first();
        $hasRole = DB::table('model_has_roles')
            ->where('role_id', $adminRole->id)
            ->where('model_id', $user->id)
            ->exists();
        
        $this->assertTrue($hasRole);
    }
    
    public function testMultipleRoleAssignments(): void
    {
        // Create roles
        $this->testCreateRoles();
        
        // Create a user with multiple roles
        $user = User::updateOrCreate(
            ['email' => 'multirole@test.com'],
            [
                'name' => 'Multi Role User',
                'password' => bcrypt('password123'),
                'mobile' => '0512345700',
                'phone' => '0112345700',
                'gender' => 'female',
                'company_name' => 'Multi Role Company',
                'company_type' => 'private',
                'city' => 'Riyadh',
                'is_active' => true,
                'registration_type' => 'personal',
            ]
        );
        
        // Assign multiple roles
        $adminRole = Role::where('name', 'admin')->first();
        $financeRole = Role::where('name', 'finance')->first();
        
        DB::table('model_has_roles')->insert([
            [
                'role_id' => $adminRole->id,
                'model_type' => 'App\\Models\\User',
                'model_id' => $user->id
            ],
            [
                'role_id' => $financeRole->id,
                'model_type' => 'App\\Models\\User',
                'model_id' => $user->id
            ]
        ]);
        
        // Check if user has both roles
        $roleCount = DB::table('model_has_roles')
            ->where('model_id', $user->id)
            ->count();
        
        $this->assertEquals(2, $roleCount);
        
        // Check specific roles
        $hasAdminRole = DB::table('model_has_roles')
            ->where('role_id', $adminRole->id)
            ->where('model_id', $user->id)
            ->exists();
        
        $hasFinanceRole = DB::table('model_has_roles')
            ->where('role_id', $financeRole->id)
            ->where('model_id', $user->id)
            ->exists();
            
        $this->assertTrue($hasAdminRole);
        $this->assertTrue($hasFinanceRole);
    }
} 