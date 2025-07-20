<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class TestRolesController extends Controller
{
    /**
     * Show a list of all test users and their roles
     */
    public function index()
    {
        $users = User::whereIn('email', [
            'admin1@test.com', 
            'admin2@test.com', 
            'finance@test.com', 
            'audit@test.com', 
            'validation@test.com'
        ])->with('roles')->get();

        return view('test-roles', compact('users'));
    }

    /**
     * Login as a specific test user
     */
    public function loginAs($userId)
    {
        // Only allow in local/dev environment
        if (!app()->environment('local', 'development')) {
            abort(403, 'This functionality is only available in development environment');
        }

        $user = User::findOrFail($userId);
        Auth::login($user);
        
        $roles = $user->roles->pluck('name')->toArray();
        
        return redirect()->route('home')
            ->with('success', "Logged in as {$user->name} with roles: " . implode(', ', $roles));
    }

    /**
     * Show the current user's roles
     */
    public function currentUser()
    {
        if (!Auth::check()) {
            return "Not logged in";
        }

        $user = Auth::user();
        $roles = $user->roles->pluck('name')->toArray();
        
        $hasAdmin = in_array('admin', $roles);
        $hasFinance = in_array('finance', $roles);
        $hasAudit = in_array('audit', $roles);
        $hasValidation = in_array('validation', $roles);
        
        return "Logged in as: {$user->name} ({$user->email})<br>" .
               "Roles: " . implode(', ', $roles) . "<br>" .
               "Is Admin: " . ($hasAdmin ? 'Yes' : 'No') . "<br>" .
               "Is Finance: " . ($hasFinance ? 'Yes' : 'No') . "<br>" .
               "Is Audit: " . ($hasAudit ? 'Yes' : 'No') . "<br>" .
               "Is Validation: " . ($hasValidation ? 'Yes' : 'No') . "<br>";
    }
    
    /**
     * Fix role assignments for test users
     */
    public function fixRoles()
    {
        // Check and create roles if they don't exist
        $roles = ['admin', 'finance', 'audit', 'validation'];
        $createdRoles = [];
        
        foreach ($roles as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if (!$role) {
                Role::create(['name' => $roleName, 'guard_name' => 'web']);
                $createdRoles[] = $roleName;
            }
        }
        
        // Get users
        $users = [
            'admin1@test.com' => 'admin',
            'admin2@test.com' => 'admin',
            'finance@test.com' => 'finance',
            'audit@test.com' => 'audit',
            'validation@test.com' => 'validation',
        ];
        
        $results = [];
        
        foreach ($users as $email => $roleName) {
            $user = User::where('email', $email)->first();
            if (!$user) {
                $results[] = "User {$email} not found";
                continue;
            }
            
            // Directly using DB to assign role
            $roleId = DB::table('roles')->where('name', $roleName)->value('id');
            if (!$roleId) {
                $results[] = "Role {$roleName} not found";
                continue;
            }
            
            // Check if already has role
            $existingRole = DB::table('model_has_roles')
                ->where('model_id', $user->id)
                ->where('role_id', $roleId)
                ->first();
                
            if (!$existingRole) {
                // Insert directly into model_has_roles table
                DB::table('model_has_roles')->insert([
                    'role_id' => $roleId,
                    'model_type' => 'App\\Models\\User',
                    'model_id' => $user->id
                ]);
                $results[] = "Assigned {$roleName} to {$email}";
            } else {
                $results[] = "{$email} already has role {$roleName}";
            }
        }
        
        return implode('<br>', $results);
    }
} 