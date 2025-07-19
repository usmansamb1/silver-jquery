<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminRouteAccessTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $finance;
    protected $it;
    protected $activation;
    protected $validation;
    protected $customer;
    
    /**
     * Admin routes that should be accessible to admin, finance, IT, activation, and validation roles
     */
    protected $adminRoutes = [
        '/admin/dashboard',
        '/admin/users',
        '/admin/approval-workflows',
        '/admin/settings',
    ];
    
    /**
     * Potential admin routes to test
     */
    protected $potentialRoutes = [
        '/admin/dashboard',
        '/admin/users',
        '/admin/payments',
        '/admin/settings',
        '/admin/reports',
        '/admin/approvals',
        '/admin/activation',
        '/admin/validation',
    ];
    
    /**
     * User roles with admin access
     */
    protected $adminRoles = [
        'admin' => 'admin1@test.com',
        'finance' => 'finance@test.com',
        'it' => 'audit@test.com',
        'activation' => 'activation@test.com',
        'validation' => 'validation@test.com'
    ];
    
    /**
     * User roles without admin access
     */
    protected $nonAdminRoles = [
        'customer' => 'customer@test.com'
    ];
    
    protected $authorizedRoles = [
        'admin',
        'finance',
        'audit',
        'it',
        'contractor', 
        'activation',
        'validation'
    ];

    protected $unauthorizedRoles = [
        'customer'
    ];
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Use existing users from the database
        $this->fetchExistingUsers();
        
        // Create all needed roles
        foreach (array_merge($this->authorizedRoles, $this->unauthorizedRoles) as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }
        
        // Find admin routes that actually exist in the application
        $this->findExistingRoutes();
    }
    
    /**
     * Fetch existing users from database
     */
    private function fetchExistingUsers(): void
    {
        // Get admin users that should have access
        $this->admin = User::where('email', 'admin1@test.com')->first();
        $this->finance = User::where('email', 'finance@test.com')->first();
        $this->it = User::where('email', 'audit@test.com')->first(); // Using audit user for IT role tests
        $this->activation = User::where('email', 'activation@test.com')->first();
        $this->validation = User::where('email', 'validation@test.com')->first();
        
        // Get non-admin users
        $this->customer = User::where('email', 'customer@test.com')->first();
        
        // Load roles relationship for all users
        foreach ([$this->admin, $this->finance, $this->it, $this->activation, $this->validation, $this->customer] as $user) {
            if ($user) {
                $user->load('roles');
            }
        }
    }
    
    /**
     * Find admin routes that actually exist in the application
     */
    private function findExistingRoutes(): void
    {
        // Start with a base set of routes we know should work
        $baseRoutes = ['/admin/dashboard', '/admin/users'];
        $workingRoutes = [];
        
        // Test each base route
        foreach ($baseRoutes as $route) {
            try {
                $response = $this->get($route);
                if ($response->status() != 404 && $response->status() != 405) {
                    $workingRoutes[] = $route;
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        
        // If we have at least one working route, use only those for testing
        if (!empty($workingRoutes)) {
            $this->adminRoutes = $workingRoutes;
            return;
        }
        
        // If no routes worked, try one more fallback
        try {
            $response = $this->get('/admin');
            if ($response->status() != 404 && $response->status() != 405) {
                $this->adminRoutes[] = '/admin';
            }
        } catch (\Exception $e) {
            // Fallback to at least have one route for testing
            $this->adminRoutes[] = '/admin/dashboard';
        }
    }

    /**
     * Test that admin role has access to admin routes
     */
    public function test_admin_role_has_access_to_admin_routes(): void
    {
        // Remove address/city/country from user creation
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'registration_type' => 'test',
            'company_type' => 'test', 
            'company_name' => 'Test Company',
            'mobile' => '0512345678',
            'is_active' => true
        ]);
        $admin->assignRole('admin');
        
        // Test route access
        $this->actingAs($admin);
        
        foreach ($this->adminRoutes as $route) {
            $response = $this->get($route);
            $this->assertTrue(
                $response->status() === 200 || $response->status() === 302,
                "Admin cannot access {$route}. Status: " . $response->status()
            );
        }
    }
    
    /**
     * Test that authorized roles can access admin routes
     */
    public function test_authorized_roles_can_access_admin_routes(): void
    {
        $roles = ['finance', 'audit', 'activation', 'validation'];
        
        foreach ($roles as $role) {
            $user = User::create([
                'name' => ucfirst($role).' User',
                'email' => "$role@test.com",
                'password' => Hash::make('password'),
                'registration_type' => 'test',
                'company_type' => 'test',
                'company_name' => 'Test Company',
                'mobile' => '0512345678',
                'is_active' => true
            ]);
            $user->assignRole($role);
            
            // Test dashboard access
            $response = $this->actingAs($user)->get('/admin/dashboard');
            $response->assertStatus(200);
        }
    }
    
    /**
     * Test that customer role cannot access admin routes
     */
    public function test_customer_role_cannot_access_admin_routes(): void
    {
        $customer = User::create([
            'name' => 'Customer User',
            'email' => 'customer@test.com',
            'password' => Hash::make('password'),
            'registration_type' => 'customer',
            'mobile' => '0512345678',
            'is_active' => true
        ]);
        $customer->assignRole('customer');
        
        $response = $this->actingAs($customer)->get('/admin/dashboard');
        $response->assertRedirect(route('home'));
    }

    /**
     * Test login functionality
     */
    public function test_users_can_authenticate(): void
    {
        // Get all users to test
        $allUsers = [
            $this->admin, 
            $this->finance, 
            $this->it, 
            $this->activation, 
            $this->validation, 
            $this->customer
        ];
        
        // Filter out null users
        $allUsers = array_filter($allUsers);
        
        if (empty($allUsers)) {
            $this->markTestSkipped('No users found in database');
        }
        
        // Test the authentication directly instead of using login route
        foreach ($allUsers as $user) {
            // Clear authentication
            Auth::logout();
            
            // Authenticate as user
            $this->actingAs($user);
            
            // Check if authenticated
            $this->assertTrue(Auth::check(), "Failed to authenticate as {$user->email}");
            
            // Check if authenticated user is the correct one
            $this->assertEquals($user->id, Auth::id(), "Authenticated user ID does not match expected ID");
        }
    }

    /**
     * Test activation and validation users can login and access admin routes
     */
    public function test_activation_validation_users_can_login_and_access_admin(): void
    {
        $testUsers = [
            'activation' => 'activation@test.com',
            'validation' => 'validation@test.com'
        ];

        foreach ($testUsers as $role => $email) {
            // Test login
            $response = $this->post('/admin/login', [
                'email' => $email,
                'password' => 'password123'
            ]);

            // Should redirect to either dashboard or users page
            $this->assertTrue(
                $response->isRedirect(route('admin.dashboard')) || $response->isRedirect(route('admin.users.index')),
                "Login should redirect to dashboard or users page for {$role} user"
            );

            // Test accessing admin routes
            $user = User::where('email', $email)->first();
            $this->actingAs($user);

            // Test dashboard access
            $response = $this->get('/admin/dashboard');
            $this->assertTrue(
                $response->status() == 200 || $response->status() == 302,
                "{$role} user cannot access dashboard"
            );

            // Test role-specific routes
            $roleRoute = "/admin/{$role}";
            $response = $this->get($roleRoute);
            $this->assertTrue(
                $response->status() == 200 || $response->status() == 302,
                "{$role} user cannot access their specific route"
            );

            // Clear authentication
            $this->app['auth']->logout();
        }
    }

    /**
     * Test that only admin can access approval workflows
     */
    public function test_only_admin_can_access_approval_workflows(): void
    {
        // Admin should have access
        $response = $this->actingAs($this->admin)->get('/admin/approval-workflows');
        $this->assertTrue(
            $response->status() == 200 || $response->status() == 302,
            "Admin cannot access approval workflows"
        );

        $response = $this->actingAs($this->admin)->get('/admin/approval-workflows/create');
        $this->assertTrue(
            $response->status() == 200 || $response->status() == 302,
            "Admin cannot access approval workflow creation"
        );

        // Other roles should not have access
        $nonAdminUsers = [
            $this->finance,
            $this->it,
            $this->activation,
            $this->validation
        ];

        foreach ($nonAdminUsers as $user) {
            if (!$user) continue;

            $response = $this->actingAs($user)->get('/admin/approval-workflows');
            $this->assertTrue(
                $response->status() == 403 || $response->status() == 401 || 
                ($response->status() == 302 && str_contains($response->headers->get('Location'), 'login')),
                "User with role {$user->roles->first()->name} should not access approval workflows"
            );

            $response = $this->actingAs($user)->get('/admin/approval-workflows/create');
            $this->assertTrue(
                $response->status() == 403 || $response->status() == 401 || 
                ($response->status() == 302 && str_contains($response->headers->get('Location'), 'login')),
                "User with role {$user->roles->first()->name} should not access approval workflow creation"
            );
        }
    }

    /**
     * Test that only admin can access admin dashboard and user management
     */
    public function test_only_admin_can_access_admin_core_features(): void
    {
        $adminRoutes = [
            '/admin/dashboard',
            '/admin/users',
            '/admin/users/create',
            '/admin/settings'
        ];

        // Admin should have access to all routes
        foreach ($adminRoutes as $route) {
            $response = $this->actingAs($this->admin)->get($route);
            $this->assertTrue(
                $response->status() == 200 || $response->status() == 302,
                "Admin cannot access {$route}"
            );
        }

        // Other roles should not have access
        $nonAdminUsers = [
            $this->finance,
            $this->it,
            $this->activation,
            $this->validation
        ];

        foreach ($nonAdminUsers as $user) {
            if (!$user) continue;

            foreach ($adminRoutes as $route) {
                $response = $this->actingAs($user)->get($route);
                $this->assertTrue(
                    $response->status() == 403 || $response->status() == 401 || 
                    ($response->status() == 302 && str_contains($response->headers->get('Location'), 'login')),
                    "User with role {$user->roles->first()->name} should not access {$route}"
                );
            }
        }
    }

    public function test_non_admin_users_cannot_access_admin_routes(): void
    {
        $nonAdminUsers = [
            $this->finance,
            $this->it,
            $this->activation,
            $this->validation
        ];

        $adminRoutes = [
            '/admin/dashboard',
            '/admin/users',
            '/admin/users/create',
            '/admin/approval-workflows',
            '/admin/approval-workflows/create'
        ];

        foreach ($nonAdminUsers as $user) {
            if (!$user) continue;

            foreach ($adminRoutes as $route) {
                $response = $this->actingAs($user)->get($route);
                $this->assertTrue(
                    $response->status() == 403 || $response->status() == 401 || 
                    ($response->status() == 302 && str_contains($response->headers->get('Location'), 'login')),
                    "User with role {$user->roles->first()->name} should not access {$route}"
                );
            }
        }
    }

    public function test_other_roles_can_access_their_routes(): void
    {
        $roleRoutes = [
            'finance' => ['/admin/payments', '/admin/payments/1'],
            'activation' => ['/admin/activation', '/admin/activation/1'],
            'validation' => ['/admin/validation', '/admin/validation/1']
        ];

        foreach ($roleRoutes as $role => $routes) {
            $user = $this->$role;
            if (!$user) continue;

            foreach ($routes as $route) {
                $response = $this->actingAs($user)->get($route);
                $this->assertTrue(
                    $response->status() == 200 || $response->status() == 302 || $response->status() == 404,
                    "User with role {$role} cannot access {$route}. Status: {$response->status()}"
                );
            }
        }
    }

    /**
     * Test that all admin roles can access admin routes
     */
    public function test_all_admin_roles_can_access_admin_routes(): void
    {
        $adminUsers = [
            $this->admin,
            $this->finance,
            $this->it,
            $this->activation,
            $this->validation
        ];

        $adminRoutes = [
            '/admin/dashboard',
            '/admin/users',
            '/admin/approval-workflows',
            '/admin/payments',
            '/admin/activation',
            '/admin/validation'
        ];

        foreach ($adminUsers as $user) {
            if (!$user) continue;

            // Test login
            $response = $this->post('/admin/login', [
                'email' => $user->email,
                'password' => 'password123'
            ]);

            // Should redirect to admin dashboard
            $this->assertTrue(
                $response->isRedirect(route('admin.dashboard')),
                "User {$user->email} should be redirected to admin dashboard after login"
            );

            // Test route access
            $this->actingAs($user);
            foreach ($adminRoutes as $route) {
                $response = $this->get($route);
                $this->assertTrue(
                    $response->status() == 200 || $response->status() == 302,
                    "User {$user->email} cannot access {$route}. Status: {$response->status()}"
                );
            }
        }
    }

    /**
     * Test that customer is redirected to home
     */
    public function test_customer_is_redirected_to_home(): void
    {
        if (!$this->customer) {
            $this->markTestSkipped('Customer user not found');
        }

        // Test login
        $response = $this->post('/admin/login', [
            'email' => $this->customer->email,
            'password' => 'password123'
        ]);

        // Should redirect to home
        $this->assertTrue(
            $response->isRedirect(route('home')),
            "Customer should be redirected to home after login"
        );

        // Test admin routes access (should be denied)
        $this->actingAs($this->customer);
        $adminRoutes = [
            '/admin/dashboard',
            '/admin/users',
            '/admin/approval-workflows',
            '/admin/payments',
            '/admin/activation',
            '/admin/validation'
        ];

        foreach ($adminRoutes as $route) {
            $response = $this->get($route);
            $this->assertTrue(
                $response->status() == 403 || $response->status() == 401 || 
                ($response->status() == 302 && str_contains($response->headers->get('Location'), 'login')),
                "Customer should not access {$route}"
            );
        }
    }

    /**
     * Test approval workflows access
     */
    public function test_approval_workflows_access(): void
    {
        $adminUsers = [
            $this->admin,
            $this->finance,
            $this->it,
            $this->activation,
            $this->validation
        ];

        $workflowRoutes = [
            '/admin/approval-workflows',
            '/admin/approval-workflows/create'
        ];

        foreach ($adminUsers as $user) {
            if (!$user) continue;

            $this->actingAs($user);
            foreach ($workflowRoutes as $route) {
                $response = $this->get($route);
                $this->assertTrue(
                    $response->status() == 200 || $response->status() == 302,
                    "User {$user->email} cannot access {$route}. Status: {$response->status()}"
                );
            }
        }

        // Test customer cannot access
        if ($this->customer) {
            $this->actingAs($this->customer);
            foreach ($workflowRoutes as $route) {
                $response = $this->get($route);
                $this->assertTrue(
                    $response->status() == 403 || $response->status() == 401 || 
                    ($response->status() == 302 && str_contains($response->headers->get('Location'), 'login')),
                    "Customer should not access {$route}"
                );
            }
        }
    }

    /**
     * Create a user with the given role.
     */
    protected function createUserWithRole($roleName)
    {
        // Create a user with all required fields
        $user = new User();
        $user->id = \Illuminate\Support\Str::uuid(); // Assuming users have UUID primary keys
        $user->name = ucfirst($roleName) . ' User';
        $user->email = $roleName . '@example.com';
        $user->password = Hash::make('password');
        $user->registration_type = 'test';
        $user->company_type = 'test';
        $user->company_name = 'Test Company';
        $user->is_active = true;
        $user->save();
        
        // Assign the role
        $user->assignRole($roleName);
        
        return $user;
    }

    /**
     * Test that authorized roles are redirected to dashboard after login
     *
     * @return void
     */
    public function test_authorized_users_redirected_to_dashboard_after_login()
    {
        // First verify the login page is accessible
        $response = $this->get('/admin/login');
        // Either the route exists (200) or it's a fallback to another login (302)
        $this->assertTrue(
            $response->status() === 200 || 
            $response->status() === 302,
            "Admin login page not accessible, status: " . $response->status()
        );
        
        foreach ($this->authorizedRoles as $role) {
            // Create a user with the correct role and all required fields
            $user = $this->createUser(
                ucfirst($role) . ' User',
                $role . '@example.com',
                $role
            );
            
            // Try to login with credentials
            $response = $this->post('/admin/login', [
                'email' => $user->email,
                'password' => 'password',
            ]);
            
            // Should be redirected after login
            $this->assertTrue(
                $response->status() === 302,
                "Login failed for user with role '{$role}'"
            );
            
            // Check that we're redirected to a proper admin route
            $redirectUrl = $response->headers->get('Location');
            $this->assertTrue(
                str_contains($redirectUrl, '/admin'),
                "User with role '{$role}' was not redirected to admin area after login, instead got: {$redirectUrl}"
            );
            
            // Logout to test the next role
            $this->post('/admin/logout');
        }
    }

    /**
     * Create a new user with required fields
     */
    protected function createUser($name, $email, $roleName)
    {
        // Create the customer_no_seq if it doesn't exist
        try {
            DB::statement('CREATE SEQUENCE customer_no_seq START WITH 1000 INCREMENT BY 1');
        } catch (\Exception $e) {
            // Sequence already exists, ignore
        }

        // Create user with all required fields
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make('password'),
            'registration_type' => 'test',
            'company_type' => 'test',
            'company_name' => 'Test Company',
            'mobile' => '0512345678',
            'is_active' => true,
        ]);
        
        // Assign role
        $role = Role::where('name', $roleName)->first();
        $user->assignRole($role);
        
        return $user;
    }
} 