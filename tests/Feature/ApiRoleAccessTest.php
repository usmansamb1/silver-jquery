<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ApiRoleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
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
                'name' => 'Admin API',
                'email' => 'adminapi@test.com',
                'password' => bcrypt('password123'),
                'mobile' => '0512345698',
                'phone' => '0112345698',
                'gender' => 'male',
                'company_name' => 'API Company',
                'company_type' => 'private',
                'city' => 'Riyadh',
                'is_active' => true,
                'registration_type' => 'personal',
                'role' => 'admin'
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Finance API',
                'email' => 'financeapi@test.com',
                'password' => bcrypt('password123'),
                'mobile' => '0512345697',
                'phone' => '0112345697',
                'gender' => 'male',
                'company_name' => 'API Company',
                'company_type' => 'private',
                'city' => 'Riyadh',
                'is_active' => true,
                'registration_type' => 'personal',
                'role' => 'finance'
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Audit API',
                'email' => 'auditapi@test.com',
                'password' => bcrypt('password123'),
                'mobile' => '0512345696',
                'phone' => '0112345696',
                'gender' => 'male',
                'company_name' => 'API Company',
                'company_type' => 'private',
                'city' => 'Riyadh',
                'is_active' => true,
                'registration_type' => 'personal',
                'role' => 'audit'
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Validation API',
                'email' => 'validationapi@test.com',
                'password' => bcrypt('password123'),
                'mobile' => '0512345695',
                'phone' => '0112345695',
                'gender' => 'male',
                'company_name' => 'API Company',
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
        $this->admin = User::where('email', 'adminapi@test.com')->first();
        $this->finance = User::where('email', 'financeapi@test.com')->first();
        $this->audit = User::where('email', 'auditapi@test.com')->first();
        $this->validation = User::where('email', 'validationapi@test.com')->first();
    }

    /**
     * Test API users were created successfully.
     */
    public function testApiUsersCreation(): void
    {
        $this->assertNotNull($this->admin);
        $this->assertNotNull($this->finance);
        $this->assertNotNull($this->audit);
        $this->assertNotNull($this->validation);
        
        // Check credentials
        $this->assertTrue(
            auth()->attempt(['email' => 'adminapi@test.com', 'password' => 'password123'])
        );
        
        auth()->logout();
    }
    
    /**
     * Test API roles assignment.
     */
    public function testApiRolesAssignment(): void
    {
        // Get roles
        $adminRole = Role::where('name', 'admin')->first();
        $financeRole = Role::where('name', 'finance')->first();
        $auditRole = Role::where('name', 'audit')->first();
        $validationRole = Role::where('name', 'validation')->first();
        
        // Check admin role assignment
        $adminHasRole = DB::table('model_has_roles')
            ->where('role_id', $adminRole->id)
            ->where('model_id', $this->admin->id)
            ->exists();
        $this->assertTrue($adminHasRole);
        
        // Check finance role assignment
        $financeHasRole = DB::table('model_has_roles')
            ->where('role_id', $financeRole->id)
            ->where('model_id', $this->finance->id)
            ->exists();
        $this->assertTrue($financeHasRole);
        
        // Check audit role assignment
        $auditHasRole = DB::table('model_has_roles')
            ->where('role_id', $auditRole->id)
            ->where('model_id', $this->audit->id)
            ->exists();
        $this->assertTrue($auditHasRole);
        
        // Check validation role assignment
        $validationHasRole = DB::table('model_has_roles')
            ->where('role_id', $validationRole->id)
            ->where('model_id', $this->validation->id)
            ->exists();
        $this->assertTrue($validationHasRole);
    }
    
    /**
     * Test validation user restriction.
     */
    public function testValidationRestriction(): void
    {
        // Validation user should not access admin dashboard
        $response = $this->actingAs($this->validation)->get('/admin/dashboard');
        
        // Should redirect (302) since they don't have permission
        $response->assertStatus(302);
    }
} 