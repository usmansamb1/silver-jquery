<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Otp;
use App\Services\LogService;
use App\Services\ActivityLogService;
use App\Helpers\LogHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * Handle the login request (generates OTP for verification)
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'mobile' => ['required', 'regex:/^05\d{8}$/'],
        ]);
        
        // Check if user exists
        $user = User::where('mobile', $request->mobile)->first();
        
        if (!$user) {
            // Log failed login attempt
            ActivityLogService::logLoginFailed('Login attempt failed - user not found', 'user_not_found', [
                'mobile' => $request->mobile
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'No account found with this mobile number.'
            ], 422);
        }
        
        try {
            // Start transaction
            DB::beginTransaction();
            
            // Generate 4-digit OTP
            $otp = mt_rand(1000, 9999);
            
            // Save OTP record
            $otpRecord = new Otp();
            $otpRecord->token = Str::uuid();
            $otpRecord->otp = $otp;
            $otpRecord->purpose = 'login';
            $otpRecord->data = ['mobile' => $request->mobile]; // Simplified: with array cast, no need for json_encode
            $otpRecord->expires_at = now()->addMinutes(5);
            $otpRecord->is_used = false;
            $otpRecord->save();
            
            // Here we would send the OTP via SMS in a real application
            // sendSmsOtp($request->mobile, $otp);
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'OTP has been sent to your mobile number.',
                'temp_token' => $otpRecord->token,
                'otp' => $otp, // Remove in production
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log error
            ActivityLogService::logLoginFailed('Failed to generate OTP', 'system_error', [
                'mobile' => $request->mobile,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate OTP. Please try again.',
                'error' => $e->getMessage() // Remove in production
            ], 500);
        }
    }
    
    /**
     * Verify the OTP and login the user
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyOtp(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'mobile' => ['required', 'regex:/^(05\d{8}|5\d{8,9})$/'],
                'otp'    => 'required',
            ]);
            
            if ($validator->fails()){
                return response()->json(['errors' => $validator->errors()], 422);
            }
            
            // Check if mobile number exists in users table
            $user = User::where('mobile', $request->mobile)->first();
            if (!$user) {
                return response()->json(['message' => 'No account found with this mobile number.'], 422);
            }
            
            // Check if the OTP is correct (first check user's OTP field)
            if ($user->otp == $request->otp) {
                // Clear OTP fields
                $user->update([
                    'otp' => null,
                    'otp_created_at' => null,
                ]);
                
                // Generate token for API authentication
                $token = $user->createToken('auth_token')->plainTextToken;
                
                // Log successful OTP verification using LogHelper
                LogHelper::logOtpVerification($user, 'User verified OTP successfully', [
                    'mobile' => $user->mobile,
                    'login_method' => 'user_model_otp'
                ]);
                
                // Log successful login
                LogHelper::logLogin($user, 'User logged in successfully via OTP', [
                    'mobile' => $user->mobile,
                    'login_method' => 'user_model_otp'
                ]);
                
                // Log with new ActivityLogService 
                ActivityLogService::logLogin('User logged in successfully via OTP', [
                    'mobile' => $user->mobile,
                    'login_method' => 'user_model_otp'
                ]);
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Login successful!',
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'mobile' => $user->mobile,
                        'email' => $user->email,
                        'user_type' => $user->user_type,
                    ]
                ]);
            }
            
            // If not in User model, check the Otp model
            $otpRecord = Otp::findActiveByMobileAndOtp($request->mobile, $request->otp);
            
            if (!$otpRecord) {
                // For debugging, check if there's any OTP for this mobile at all
                $anyOtp = Otp::where('purpose', 'login')
                            ->whereJsonContains('data->mobile', $request->mobile)
                            ->orderBy('created_at', 'desc')
                            ->first();
                
                if ($anyOtp) {
                    $otpDetails = [
                        'type' => 'login',
                        'is_used' => $anyOtp->is_used, 
                        'expires_at' => $anyOtp->expires_at,
                        'otp' => $anyOtp->otp,
                        'requested_otp' => $request->otp,
                        'mobile' => $request->mobile
                    ];
                    
                    LogService::activity('OTP found but not valid', $otpDetails, 'warning');
                    
                    // Log with new ActivityLogService
                    ActivityLogService::logLoginFailed('Invalid OTP attempt', 'invalid_otp', $otpDetails);
                } else {
                    LogService::activity('No OTP found for mobile', [
                        'type' => 'login',
                        'mobile' => $request->mobile
                    ], 'warning');
                    
                    // Log with new ActivityLogService
                    ActivityLogService::logLoginFailed('No OTP found for mobile', 'otp_not_found', [
                        'mobile' => $request->mobile
                    ]);
                }
                
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid OTP.'
                ], 401);
            }
            
            // Mark OTP as used
            $otpRecord->is_used = true;
            $otpRecord->save();
            
            // Generate token for API authentication
            $token = $user->createToken('auth_token')->plainTextToken;
            
            // Log successful login with our LogService
            LogService::logLogin($user, 'User logged in successfully via OTP model', [
                'mobile' => $user->mobile,
                'login_method' => 'otp_model'
            ]);
            
            // Log with new ActivityLogService
            ActivityLogService::logLogin('User logged in successfully via OTP model', [
                'mobile' => $user->mobile,
                'login_method' => 'otp_model'
            ]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Login successful!',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'mobile' => $user->mobile,
                    'email' => $user->email,
                    'user_type' => $user->user_type,
                ]
            ]);
        } catch (ValidationException $e) {
            LogService::activity('Validation error during login', [
                'type' => 'login',
                'errors' => $e->errors(),
                'mobile' => $request->mobile ?? null
            ], 'error');
            
            // Log with new ActivityLogService
            ActivityLogService::logLoginFailed('Validation error during login', 'validation_error', [
                'errors' => $e->errors(),
                'mobile' => $request->mobile ?? null
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid input.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            LogService::activity('OTP verification error', [
                'type' => 'login',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'mobile' => $request->mobile ?? null
            ], 'error');
            
            // Log with new ActivityLogService
            ActivityLogService::logLoginFailed('OTP verification error', 'system_error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'mobile' => $request->mobile ?? null
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Login failed. Please try again.',
                'error' => $e->getMessage() // Remove in production
            ], 500);
        }
    }
    
    /**
     * Logout the user (revoke the token)
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Get user before token is revoked
        $user = $request->user();
        
        // Log the logout action with LogHelper
        LogHelper::logLogout($user, 'User logged out via API', [
            'mobile' => $user->mobile
        ]);
        
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully.'
        ]);
    }
} 