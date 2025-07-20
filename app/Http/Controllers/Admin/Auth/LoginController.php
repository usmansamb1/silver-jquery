<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AdminLoginOTP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    protected $otpExpiryMinutes = 20;
    protected $allowedRoles = ['admin', 'finance', 'activation', 'validation', 'it', 'delivery'];

    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check if user has allowed role
        if (!$user->hasAnyRole($this->allowedRoles)) {
            throw ValidationException::withMessages([
                'email' => ['You do not have permission to access the admin panel.'],
            ]);
        }

        // Generate and store OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $cacheKey = "admin_login_otp_{$user->id}";
        
        Cache::put($cacheKey, [
            'otp' => $otp,
            'email' => $user->email
        ], now()->addMinutes($this->otpExpiryMinutes));

        // Update user with OTP
        $user->update([
            'otp' => $otp,
            'otp_created_at' => now()
        ]);

        // Send OTP notification
        $user->notify(new AdminLoginOTP($otp));

        // Store user ID in session for OTP verification
        session(['admin_login_user_id' => $user->id]);

        return response()->json([
            'message' => 'OTP has been sent to your email.',
            'show_otp_form' => true
        ]);
    }

    public function verifyOTP(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6'
        ]);

        $userId = session('admin_login_user_id');
        if (!$userId) {
            throw ValidationException::withMessages([
                'otp' => ['Session expired. Please try logging in again.'],
            ]);
        }

        $cacheKey = "admin_login_otp_{$userId}";
        $cachedData = Cache::get($cacheKey);
 
        if (!$cachedData || $cachedData['otp'] !== $request->otp) {
            throw ValidationException::withMessages([
                'otp' => ['The OTP entered is invalid or has expired.'],
            ]);
        }

        $user = User::findOrFail($userId);
        Auth::login($user);

        // Clear OTP and session data
        Cache::forget($cacheKey);
        session()->forget('admin_login_user_id');

        // Redirect based on role
        $redirectRoute = route('admin.dashboard');
        if ($user->hasRole('delivery')) {
            $redirectRoute = route('admin.delivery.dashboard');
        }

        return response()->json([
            'message' => 'Login successful',
            'redirect' => $redirectRoute
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