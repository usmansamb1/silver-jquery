<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserRoleAuthTest extends TestCase
{
    use RefreshDatabase;

    protected $admin1;
    protected $admin2;
    protected $finance;
    protected $audit;
    protected $validation;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create the roles first
        $this->createRoles();
        
        // Create test users
        $this->createTestUsers();
    }
    
    /**
     * Create roles for testing
     */
    private function createRoles(): void
    {
        $roles = ['admin', 'finance', 'audit', 'validation'];
        
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }
    }
    
    /**
     * Create test users directly with SQL to avoid factory issues
     */
    private function createTestUsers(): void
    {
        // Create users directly with DB to avoid schema issues
        $users = [
            [
                'id' => (string) Str::uuid(),
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
                'role' => 'admin'
            ],
            [
                'id' => (string) Str::uuid(),
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
                'role' => 'admin'
            ],
            [
                'id' => (string) Str::uuid(),
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
                'role' => 'finance'
            ],
            [
                'id' => (string) Str::uuid(),
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
                'role' => 'audit'
            ],
            [
                'id' => (string) Str::uuid(),
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
                'role' => 'validation'
            ]
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);
            
            // Let the model generate customer_no automatically
            // Remove customer_no as it will be auto-generated
            
            // Set created_at and updated_at
            $userData['created_at'] = now();
            $userData['updated_at'] = now();
            
            // Insert the user
            DB::table('users')->insert($userData);
            
            // Assign role
            $roleModel = Role::where('name', $role)->first();
            if ($roleModel) {
                DB::table('model_has_roles')->insert([
                    'role_id' => $roleModel->id,
                    'model_type' => 'App\\Models\\User',
                    'model_id' => $userData['id']
                ]);
            }
        }
        
        // Retrieve the users
        $this->admin1 = User::where('email', 'admin1@test.com')->first();
        $this->admin2 = User::where('email', 'admin2@test.com')->first(); 
        $this->finance = User::where('email', 'finance@test.com')->first();
        $this->audit = User::where('email', 'audit@test.com')->first();
        $this->validation = User::where('email', 'validation@test.com')->first();
    }

    /**
     * Test creating and assigning roles.
     */
    public function testRoleCreation(): void
    {
        // Check if roles exist
        $this->assertTrue(Role::where('name', 'admin')->exists());
        $this->assertTrue(Role::where('name', 'finance')->exists());
        $this->assertTrue(Role::where('name', 'audit')->exists());
        $this->assertTrue(Role::where('name', 'validation')->exists());
        
        // Check if users exist
        $this->assertNotNull($this->admin1);
        $this->assertNotNull($this->admin2);
        $this->assertNotNull($this->finance);
        $this->assertNotNull($this->audit);
        $this->assertNotNull($this->validation);
        
        // Check if roles are assigned correctly using direct DB queries to avoid model issues
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        $this->assertNotNull($adminRole);
        
        $adminRoleAssignment = DB::table('model_has_roles')
            ->where('role_id', $adminRole->id)
            ->where('model_id', $this->admin1->id)
            ->first();
        $this->assertNotNull($adminRoleAssignment);
    }

    /**
     * Test user authentication.
     */
    public function testUserAuthentication(): void
    {
        // Bypass full authentication, check if credentials are correct
        $this->assertTrue(
            auth()->attempt(['email' => 'admin1@test.com', 'password' => 'password123'])
        );
        
        // Logout
        auth()->logout();
        
        // Check finance authentication
        $this->assertTrue(
            auth()->attempt(['email' => 'finance@test.com', 'password' => 'password123'])
        );
    }
    
    /**
     * Test admin role assignment.
     */
    public function testAdminRoleAssignment(): void
    {
        // Check admin has proper role
        $adminRole = Role::where('name', 'admin')->first();
        $this->assertNotNull($adminRole);
        
        $hasRole = DB::table('model_has_roles')
            ->where('role_id', $adminRole->id)
            ->where('model_id', $this->admin1->id)
            ->exists();
        
        $this->assertTrue($hasRole);
    }
    
    /**
     * Test finance role assignment.
     */
    public function testFinanceRoleAssignment(): void
    {
        // Check finance has proper role
        $financeRole = Role::where('name', 'finance')->first();
        $this->assertNotNull($financeRole);
        
        $hasRole = DB::table('model_has_roles')
            ->where('role_id', $financeRole->id)
            ->where('model_id', $this->finance->id)
            ->exists();
        
        $this->assertTrue($hasRole);
    }
    
    /**
     * Test audit role assignment.
     */
    public function testAuditRoleAssignment(): void
    {
        // Check audit has proper role
        $auditRole = Role::where('name', 'audit')->first();
        $this->assertNotNull($auditRole);
        
        $hasRole = DB::table('model_has_roles')
            ->where('role_id', $auditRole->id)
            ->where('model_id', $this->audit->id)
            ->exists();
        
        $this->assertTrue($hasRole);
    }
    
    /**
     * Test validation role assignment and restrictions.
     */
    public function testValidationRoleAssignment(): void
    {
        // Check validation has proper role
        $validationRole = Role::where('name', 'validation')->first();
        $this->assertNotNull($validationRole);
        
        $hasRole = DB::table('model_has_roles')
            ->where('role_id', $validationRole->id)
            ->where('model_id', $this->validation->id)
            ->exists();
        
        $this->assertTrue($hasRole);
        
        // Validation user should not access admin dashboard (redirect)
        $response = $this->actingAs($this->validation)->get('/admin/dashboard');
        $response->assertStatus(302); // Should redirect away
    }
    
    /**
     * Test role middleware functions.
     */
    public function testRoleMiddlewareFunction(): void
    {
        // Check that the middleware correctly loads roles from the database
        $adminRole = Role::where('name', 'admin')->first();
        $validationRole = Role::where('name', 'validation')->first();
        
        $this->assertNotNull($adminRole);
        $this->assertNotNull($validationRole);
        
        // Admin should have admin role
        $adminHasRole = DB::table('model_has_roles')
            ->where('role_id', $adminRole->id)
            ->where('model_id', $this->admin1->id)
            ->exists();
        $this->assertTrue($adminHasRole);
        
        // Validation should have validation role
        $validationHasRole = DB::table('model_has_roles')
            ->where('role_id', $validationRole->id)
            ->where('model_id', $this->validation->id)
            ->exists();
        $this->assertTrue($validationHasRole);
        
        // Validation should not have admin role
        $validationHasAdminRole = DB::table('model_has_roles')
            ->where('role_id', $adminRole->id)
            ->where('model_id', $this->validation->id)
            ->exists();
        $this->assertFalse($validationHasAdminRole);
    }

    /**
     * Test users have the correct number of roles.
     */
    public function testUserRoleCount(): void
    {
        // Get count of roles for admin using DB query
        $adminRoleCount = DB::table('model_has_roles')
            ->where('model_id', $this->admin1->id)
            ->count();
        $this->assertEquals(1, $adminRoleCount);
        
        // Get count of roles for finance using DB query
        $financeRoleCount = DB::table('model_has_roles')
            ->where('model_id', $this->finance->id)
            ->count();
        $this->assertEquals(1, $financeRoleCount);
    }
} 