<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PendingRegistration;
use App\Models\User;
use App\Models\Wallet;
use App\Services\SmsService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Helpers\LogHelper;
use App\Services\ActivityLogService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Step 1: Receive registration details and send OTP.
     */
    public function registerOtp(Request $request)
    {
        $rules = [
            'registration_type' => 'required|in:personal,company',
            'mobile'            => ['required', 'regex:/^(05\d{8}|5\d{8,9})$/'],
            // Personal rules
            'name'              => 'required_if:registration_type,personal',
            'email'             => 'required_if:registration_type,personal|email|nullable',
            'region'            => 'required_if:registration_type,personal',
            // Company rules
            'company_type'      => 'required_if:registration_type,company|in:private,semi Govt.,Govt',
            'company_name'      => 'required_if:registration_type,company',
            'cr_number'         => 'required_if:registration_type,company',
            'vat_number'        => 'required_if:registration_type,company',
            // Terms and Conditions
            'terms_agree'       => 'required|accepted',
        ];

        $validator = Validator::make($request->all(), $rules, [
            'terms_agree.required' => 'You must agree to the Terms and Conditions',
            'terms_agree.accepted' => 'You must agree to the Terms and Conditions',
        ]);
        if ($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if mobile already exists in users table
        if (User::where('mobile', $request->mobile)->exists()) {
            return response()->json(['message' => 'Mobile already exists'], 422);
        }

        // Gather registration data
        $data = $request->only([
            'registration_type', 'mobile', 'name', 'email', 'region',
            'company_type', 'company_name', 'cr_number', 'vat_number',
            'city', 'building_number', 'zip_code', 'company_region', 'gender'
        ]);

        // Generate OTP
        $otp = rand(1357, 9246);

        try {
            DB::beginTransaction();

        // Create a pending registration record
        $pending = PendingRegistration::create([
            'registration_data' => json_encode($data),
            'mobile'            => $data['mobile'],
            'otp'               => $otp,
            'otp_created_at'    => now(),
        ]);

            // Log the registration initiation
            LogHelper::activity('Registration process initiated via API', [
                'type' => 'registration',
                'mobile' => $data['mobile'],
                'registration_type' => $data['registration_type'],
                'pending_id' => $pending->id
            ]);

            DB::commit();

            // Queue SMS notification
            try {
                $this->notificationService->sendSms(
                    $data['mobile'],
                    "Your OTP for registration is: {$otp}",
                    'registration_otp',
                    'high'
                );
            } catch (\Exception $e) {
                Log::error('Failed to queue registration SMS OTP', [
                    'mobile' => $data['mobile'],
                    'error' => $e->getMessage()
                ]);
            }

            // Queue Email notification if email provided
            if (!empty($data['email'])) {
                try {
                    $this->notificationService->sendEmail(
                        $data['email'],
                        'Registration OTP',
                        'emails.auth.registration-otp',
                        [
                            'otp' => $otp,
                            'name' => $data['name'] ?? '',
                            'registration_type' => $data['registration_type']
                        ],
                        'registration_otp',
                        'high'
                    );
                } catch (\Exception $e) {
                    Log::error('Failed to queue registration email OTP', [
                        'email' => $data['email'],
                        'error' => $e->getMessage()
                    ]);
            }
        }

        return response()->json([
            'message' => 'OTP sent for verification.',
            'temp_token' => $pending->temp_token,
        ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration OTP process failed', [
                'error' => $e->getMessage(),
                'mobile' => $data['mobile'] ?? null
            ]);
            return response()->json(['message' => 'Failed to process registration'], 500);
        }
    }

    /**
     * Step 2: Verify OTP and create the user.
     */
    public function verifyRegistrationOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'temp_token' => 'required',
            'otp'        => 'required',
        ]);

        if ($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $pending = PendingRegistration::where('temp_token', $request->temp_token)->first();

        if (!$pending) {
            return response()->json(['message' => 'Invalid registration token.'], 404);
        }

        // Check OTP validity (you can also check for expiration here)
        if ($pending->otp != $request->otp) {
            return response()->json(['message' => 'Invalid OTP.'], 401);
        }

        // Decode registration data and create the user
        $regData = json_decode($pending->registration_data, true);
        $regData['terms_accepted_at'] = now();
        $regData['is_verified'] = true;

        $user = User::create($regData);

        // Assign the default customer role
        $user->assignRole('customer');

        // Create wallet
        Wallet::create([
            'user_id' => $user->id,
            'balance' => 0,
        ]);

        // Log the OTP verification for registration
        LogHelper::logOtpVerification($user, 'Registration OTP verified successfully', [
            'mobile' => $user->mobile,
            'registration_type' => $regData['registration_type'] ?? 'unknown'
        ]);

        // Log the user registration/creation
        LogHelper::activity('New user registered via API', [
            'type' => 'user_created',
            'subject' => $user,
            'user_id' => $user->id,
            'mobile' => $user->mobile,
            'registration_type' => $regData['registration_type'] ?? 'unknown'
        ]);

        // Delete pending registration record
        $pending->delete();

        // Optionally generate token or log the user in
        return response()->json([
            'message' => 'Registration complete. OTP verified successfully.',
            'user'    => $user,
        ]);
    }
    // Registration for personal or company users
    public function register(Request $request)
    {
        //dd($request);
        $rules = [
            'registration_type' => 'required|in:personal,company',
            'mobile'            => ['required', 'regex:/^(05\d{8}|5\d{8,9})$/'],
            // Personal validation
            'name' => 'required_if:registration_type,personal',
            'email' => 'required_if:registration_type,personal|email|nullable',
            'region' => 'required_if:registration_type,personal',
            // Company validation
            'company_type' => 'required_if:registration_type,company|in:private,semi Govt.,Govt',
            'company_name' => 'required_if:registration_type,company',
            'cr_number' => 'required_if:registration_type,company',
            'vat_number' => 'required_if:registration_type,company',
            // Terms and OTP verification
            'temp_token' => 'required',
            'terms_agree' => 'required|accepted',
            'otp_verified' => 'required|boolean|accepted',
        ];

        $messages = [
            'terms_agree.required' => 'You must agree to the Terms and Conditions',
            'terms_agree.accepted' => 'You must agree to the Terms and Conditions',
            'otp_verified.required' => 'OTP verification is required',
            'otp_verified.accepted' => 'OTP verification is required',
            'temp_token.required' => 'OTP verification token is missing'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Verify that the temp_token exists and is valid
        $pending = PendingRegistration::where('temp_token', $request->temp_token)->first();
        if (!$pending) {
            return response()->json(['message' => 'Invalid or expired verification token. Please restart the registration process.'], 422);
        }

        // Extract data from the pending registration
        $regData = json_decode($pending->registration_data, true);
        $regData['terms_accepted_at'] = now();
        
        // Create the user
        $user = User::create($regData);

        // Create wallet for the user
        Wallet::create([
            'user_id' => $user->id,
            'balance' => 0
        ]);

        // Assign the default customer role
        $user->assignRole('customer');

        // Log the user registration/creation
        LogHelper::activity('New user registered via API registration', [
            'type' => 'user_created',
            'subject' => $user,
            'user_id' => $user->id,
            'mobile' => $user->mobile,
            'registration_type' => $regData['registration_type'] ?? 'unknown'
        ]);

        // Delete the pending registration
        $pending->delete();

        return response()->json([
            'message' => 'Registration successful.',
            'user' => $user
        ], 201);
    }

    /**
     * Login endpoint – accepts mobile number, generates OTP, stores it, and sends it.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => ['required', 'regex:/^(05\d{8}|5\d{8,9})$/'],
        ]);

        if ($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if user exists
        $user = User::where('mobile', $request->mobile)->first();
        if (!$user) {
            LogHelper::activity('Login attempt failed - user not found', [
                'type' => 'login_failed',
                'mobile' => $request->mobile,
                'reason' => 'user_not_found'
            ], 'warning');
            
            ActivityLogService::logLoginFailed('Login attempt failed - user not found', 'user_not_found', [
                'mobile' => $request->mobile
            ]);
            
            return response()->json(['message' => 'User not found'], 404);
        }
        
        try {
            DB::beginTransaction();
            
            // Generate OTP and update user
        $otp = rand(1234, 9875);
        $user->update([
            'otp' => $otp,
            'otp_created_at' => now(),
        ]);

            DB::commit();

            // Queue SMS OTP
            try {
                $this->notificationService->sendSms(
                    $user->mobile,
                    "{$otp}",
                    'login_otp',
                    'high'
                );
            } catch (\Exception $e) {
                Log::error('Failed to queue login SMS OTP', [
                    'user_id' => $user->id,
                    'mobile' => $user->mobile,
                    'error' => $e->getMessage()
                ]);
            }

            // Queue Email OTP if email exists
            if ($user->email) {
        try {
                    $this->notificationService->sendEmail(
                        $user->email,
                        'Login OTP',
                        'emails.auth.login-otp',
                        [
                            'otp' => $otp,
                            'name' => $user->name
                        ],
                        'login_otp',
                        'high'
                    );
        } catch (\Exception $e) {
                    Log::error('Failed to queue login email OTP', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Log the login attempt
            LogHelper::activity('Login OTP sent to user', [
                'type' => 'login_otp_sent',
                'user_id' => $user->id,
                'mobile' => $user->mobile,
                'email_sent' => !empty($user->email)
            ]);

            return response()->json(['message' => 'OTP sent successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Login OTP process failed', [
                'error' => $e->getMessage(),
                'mobile' => $request->mobile
            ]);
            return response()->json(['message' => 'Failed to process login request'], 500);
        }
    }

    /**
     * Verify OTP endpoint – accepts mobile and OTP,
     * checks the database, and logs the user in if valid.
     */
    public function verifyOtp(Request $request)
    {
        // Validate both mobile and otp fields
        $validator = Validator::make($request->all(), [
            'mobile' => ['required', 'regex:/^(05\d{8}|5\d{8,9})$/'],
            'otp'    => 'required',
        ]);

        if ($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Retrieve user by mobile
        $user = User::where('mobile', $request->mobile)->first();
        if (!$user) {
            LogHelper::activity('OTP verification failed - user not found', [
                'type' => 'login_failed',
                'mobile' => $request->mobile,
                'reason' => 'user_not_found'
            ], 'warning');
            
            // Use new ActivityLogService
            ActivityLogService::logLoginFailed('OTP verification failed - user not found', 'user_not_found', [
                'mobile' => $request->mobile
            ]);
            
            return response()->json(['message' => 'User not found'], 404);
        }

        // Compare the provided OTP with the one stored in the database
        if ($user->otp == $request->otp) {
            // OTP is correct – clear OTP fields
            $user->update([
                'otp' => null,
                'otp_created_at' => null,
            ]);

            // Optionally, generate an API token for the user:
             $token = $user->createToken('api_token')->plainTextToken;

            // Log OTP verification success
            LogHelper::logOtpVerification($user, 'OTP verified successfully for API login', [
                'mobile' => $user->mobile
            ]);

            // Log successful login
            LogHelper::logLogin($user, 'User logged in via API', [
                'mobile' => $user->mobile,
                'login_method' => 'api_otp'
            ]);

            // Respond with success and, for example, user details or a redirect URL
            return response()->json([
                'message' => 'OTP verified successfully. Login successful.',
                'user'    => $user,
                 'token' => $token, // if using token authentication
            ]);
        }

        // Log failed verification attempt
        LogHelper::activity('OTP verification failed - invalid OTP', [
            'type' => 'login_failed',
            'user_id' => $user->id,
            'mobile' => $user->mobile,
            'reason' => 'invalid_otp'
        ], 'warning');
        
        // Use new ActivityLogService
        ActivityLogService::logLoginFailed('OTP verification failed - invalid OTP', 'invalid_otp', [
            'user_id' => $user->id,
            'mobile' => $user->mobile
        ]);

        // If OTP does not match, return an error
        return response()->json(['message' => 'Invalid OTP'], 401);
    }

    /**
     * Send OTP via Email for existing user login
     */
    public function sendOtpEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => ['required', 'regex:/^(05\d{8}|5\d{8,9})$/'],
        ]);

        if ($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if user exists
        $user = User::where('mobile', $request->mobile)->first();
        if (!$user) {
            LogHelper::activity('Email OTP request failed - user not found', [
                'type' => 'login_failed',
                'mobile' => $request->mobile,
                'reason' => 'user_not_found'
            ], 'warning');
            
            ActivityLogService::logLoginFailed('Email OTP request failed - user not found', 'user_not_found', [
                'mobile' => $request->mobile
            ]);
            
            return response()->json(['message' => 'User not found'], 404);
        }

        // Check if user has email
        if (!$user->email) {
            LogHelper::activity('Email OTP request failed - no email address', [
                'type' => 'login_failed',
                'user_id' => $user->id,
                'mobile' => $user->mobile,
                'reason' => 'no_email_address'
            ], 'warning');
            
            ActivityLogService::logLoginFailed('Email OTP request failed - no email address', 'no_email_address', [
                'user_id' => $user->id,
                'mobile' => $user->mobile
            ]);
            
            return response()->json(['message' => 'User does not have an email address'], 422);
        }
        
        try {
            DB::beginTransaction();
            
            // Generate OTP and update user
        $otp = rand(1234, 9875);
        $user->update([
            'otp' => $otp,
            'otp_created_at' => now(),
        ]);

            DB::commit();

            // Queue Email OTP
            try {
                $this->notificationService->sendEmail(
                    $user->email,
                    'Login OTP',
                    'emails.auth.login-otp',
                    [
                        'otp' => $otp,
                        'name' => $user->name
                    ],
                    'login_otp',
                    'high'
                );

                // Log success
                LogHelper::activity('Email OTP sent to user', [
                    'type' => 'login_otp_sent',
                    'user_id' => $user->id,
                    'mobile' => $user->mobile,
                    'email' => $user->email,
                    'method' => 'email'
                ]);

            return response()->json(['message' => 'OTP sent to your email successfully']);

            } catch (\Exception $e) {
                Log::error('Failed to queue email OTP', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage()
                ]);
                
                ActivityLogService::logLoginFailed('Failed to send email OTP', 'email_error', [
                    'user_id' => $user->id,
                    'mobile' => $user->mobile,
                    'email' => $user->email,
                    'error' => $e->getMessage()
                ]);
                
                return response()->json(['message' => 'Failed to send OTP email'], 500);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Email OTP process failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id ?? null,
                'mobile' => $request->mobile
            ]);
            return response()->json(['message' => 'Failed to process OTP request'], 500);
        }
    }
}
