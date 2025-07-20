<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Services\ActivityLogService;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Add debugging
            Log::info('User login attempt', [
                'user_id' => $user->id,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name'),
                'has_admin_access' => $user->hasAnyRole(['admin', 'finance', 'audit', 'it', 'contractor', 'activation', 'validation']),
                'guard_name' => Auth::getDefaultDriver()
            ]);
            
            // Check if user has admin access (any role except customer)
            if ($user->hasAnyRole(['admin', 'finance', 'audit', 'it', 'contractor', 'activation', 'validation'])) {
                $request->session()->regenerate();
                
                // Log admin login with ActivityLogService
                ActivityLogService::logLogin('Admin user logged in', [
                    'email' => $user->email,
                    'roles' => $user->roles->pluck('name')->toArray()
                ]);
                
                return redirect()->intended(route('admin.dashboard'));
            }
            
            // If user is customer, redirect to home
            if ($user->hasRole('customer')) {
                $request->session()->regenerate();
                
                // Log customer login with ActivityLogService
                ActivityLogService::logLogin('Customer logged in via admin portal', [
                    'email' => $user->email
                ]);
                
                return redirect()->intended(route('home'));
            }
            
            // If user doesn't have proper role, logout
            Auth::logout();
            return back()->withErrors([
                'email' => 'You do not have permission to access this system.',
            ]);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
} 