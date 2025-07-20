<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Services\NotificationService;
use App\Services\UserStatusService;
use App\Models\User;
use App\Models\StatusHistory;
use App\Models\ServiceOrder;
use Spatie\Permission\Models\Role;

class ProfileController extends Controller
{
    protected $notificationService;
    protected $userStatusService;

    public function __construct(NotificationService $notificationService, UserStatusService $userStatusService)
    {
        $this->middleware('auth');
        $this->notificationService = $notificationService;
        $this->userStatusService = $userStatusService;
    }

    /**
     * Show the user's profile.
     */
    public function show()
    {
        $user = Auth::user();
        
        // Fix for service bookings count - check both types of service records
        try {
            // First try ServiceBooking model (seems to be the primary one used)
            $bookingsCount = \App\Models\ServiceBooking::where('user_id', $user->id)->count();
            
            // If we have a ServiceOrder model as well, add those too
            if (class_exists('\App\Models\ServiceOrder')) {
                $ordersCount = \App\Models\ServiceOrder::where('user_id', $user->id)->count();
                $bookings_count = $bookingsCount + $ordersCount;
            } else {
                $bookings_count = $bookingsCount;
            }
            
            // Debug log to track the counts
            Log::debug('Service booking counts', [
                'user_id' => $user->id,
                'service_bookings' => $bookingsCount,
                'service_orders' => $ordersCount ?? 0,
                'total_count' => $bookings_count
            ]);
            
        } catch (\Exception $e) {
            Log::error("Error counting service bookings", [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $bookings_count = 0;
        }
        
        // Ensure last_login_at is a proper datetime object
        if ($user->last_login_at && !($user->last_login_at instanceof \DateTime) && !($user->last_login_at instanceof \Carbon\Carbon)) {
            try {
                $user->last_login_at = \Carbon\Carbon::parse($user->last_login_at);
                Log::debug('Parsed last_login_at', [
                    'user_id' => $user->id,
                    'raw_value' => $user->last_login_at,
                    'parsed_value' => $user->last_login_at->format('Y-m-d H:i:s')
                ]);
            } catch (\Exception $e) {
                Log::error("Error parsing last_login_at", [
                    'user_id' => $user->id,
                    'last_login_at' => $user->last_login_at,
                    'error' => $e->getMessage()
                ]);
                $user->last_login_at = null;
            }
        }
        
        $statusBadge = $this->userStatusService->getStatusBadgeHtml($user->status ?? 'active');
        $statusHistories = StatusHistory::where([
            'model_id' => $user->id,
            'model_type' => User::class,
        ])->with('user')->orderBy('created_at', 'desc')->take(5)->get();
        
        return view('profile.show', compact('user', 'bookings_count', 'statusBadge', 'statusHistories'));
    }

    /**
     * Show the form for editing the profile.
     */
    public function edit()
    {
        $user = Auth::user();
        $requiresPassword = $this->requiresPasswordConfirmation($user);
        
        return view('profile.edit', compact('user', 'requiresPassword'));
    }

    /**
     * Update the user's profile.
     */
    public function update(UpdateProfileRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Check password if required
        if ($this->requiresPasswordConfirmation($user)) {
            if (!Hash::check($request->password, $user->password)) {
                return back()->withErrors(['password' => 'The provided password is incorrect.']);
            }
        }

        try {
            Log::info('Profile update request received:', $request->except('password', 'cropped_avatar')); // Log basic data
            if ($request->has('cropped_avatar') && !empty($request->input('cropped_avatar'))) {
                Log::info('Cropped avatar data detected.');
            } elseif ($request->hasFile('avatar')) {
                 Log::info('Avatar file upload detected.');
            }

            // Get validated data, excluding the avatar and password for now if they exist
            $updateData = $request->validated();
            Log::info('Validated data:', $updateData);
            
            // CRITICAL: Remove password from update data - it's only for validation, not for updating
            if (isset($updateData['password'])) {
                Log::info('Removing password from update data - password is only for validation');
                unset($updateData['password']);
            }
            
            unset($updateData['avatar']); // Remove avatar if it was validated initially

            $avatarPath = null; // Initialize path variable

            // Handle avatar upload if provided
            if ($request->hasFile('avatar') || $request->input('cropped_avatar')) { // Check for file or cropped data
                Log::info('Processing avatar...');
                // Delete old avatar if exists
                if ($user->avatar) {
                    Log::info('Deleting old avatar:', ['path' => $user->avatar]);
                    Storage::disk('public')->delete($user->avatar);
                }
                
                if ($request->hasFile('avatar')) {
                     Log::info('Storing uploaded file...');
                    // Store original file if no cropping is implemented yet or as fallback
                    $avatarPath = $request->file('avatar')->store('avatars/' . $user->id, 'public');
                    Log::info('Stored uploaded file path:', ['path' => $avatarPath]);
                } elseif ($request->input('cropped_avatar')) {
                    Log::info('Storing cropped avatar data...');
                    // Handle cropped image data (Base64)
                    $imageData = $request->input('cropped_avatar');
                    
                    // Basic check if it looks like base64 data URL
                    if (strpos($imageData, 'data:image') === 0) {
                        list($type, $imageData) = explode(';', $imageData);
                        list(, $imageData) = explode(',', $imageData);
                        $imageData = base64_decode($imageData);
                        
                        if ($imageData === false) {
                            Log::error('Failed to decode base64 avatar data.');
                        } else {
                            $imageName = Str::uuid() . '.png'; // Defaulting to png
                            $avatarPath = 'avatars/' . $user->id . '/' . $imageName;
                            $saved = Storage::disk('public')->put($avatarPath, $imageData);
                            Log::info('Stored cropped avatar path:', ['path' => $avatarPath, 'saved' => $saved]);
                            if (!$saved) {
                                 $avatarPath = null; // Don't try to save path if storage failed
                                 Log::error('Failed to save cropped avatar to storage.');
                            }
                        }
                    } else {
                        Log::warning('Received cropped_avatar data does not look like a data URL.');
                    }
                }

                if ($avatarPath) {
                     Log::info('Adding avatar path to update data:', ['path' => $avatarPath]);
                    $updateData['avatar'] = $avatarPath; 
                } else {
                     Log::warning('No valid avatar path generated to update.');
                }
            }

            // Update user fields 
            Log::info('Attempting user update with data (password excluded):', $updateData);
            $updated = $user->update($updateData); // Update with validated data (password excluded)
            Log::info('User update result:', ['success' => $updated]);

            if (!$updated) {
                 Log::error('User model update returned false.', ['user_id' => $user->id]);
                 // Potentially throw an exception or return specific error
            }

            // Send notification about profile update
            try {
                $this->notificationService->sendEmail(
                    $user->email,
                    'Profile Updated',
                    'emails.profile.updated',
                    [
                        'name' => $user->name ?? $user->company_name,
                        'time' => now()->format('Y-m-d H:i:s')
                    ],
                    'profile_update',
                    'low'
                );
            } catch (\Exception $e) {
                Log::warning("Failed to send profile update notification", [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }

            return redirect()->route('profile.show')
                ->with('success', 'Profile updated successfully.');

        } catch (\Exception $e) {
            Log::error('Profile update failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return back()->withErrors(['error' => 'Failed to update profile. Please try again.']);
        }
    }

    /**
     * Update the user's avatar.
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:5120']
        ]);

        $user = Auth::user();

        try {
            $path = $request->file('avatar')->store('avatars/' . $user->id, 'public');
            
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            // Update avatar using DB facade
            \Illuminate\Support\Facades\DB::table('users')
                ->where('id', $user->id)
                ->update(['avatar' => $path]);
                
            // Update local user object for immediate use
            $user->avatar = $path;

            return response()->json([
                'message' => 'Avatar updated successfully',
                'avatar_url' => Storage::url($path)
            ]);

        } catch (\Exception $e) {
            Log::error('Avatar update failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Failed to update avatar'
            ], 500);
        }
    }

    /**
     * Remove the user's avatar.
     */
    public function removeAvatar()
    {
        $user = Auth::user();

        try {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
                
                // Update avatar using DB facade
                \Illuminate\Support\Facades\DB::table('users')
                    ->where('id', $user->id)
                    ->update(['avatar' => null]);
                    
                // Update local user object for immediate use
                $user->avatar = null;
            }

            return response()->json(['message' => 'Avatar removed successfully']);

        } catch (\Exception $e) {
            Log::error('Avatar removal failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Failed to remove avatar'], 500);
        }
    }

    /**
     * Check if the user requires password confirmation for profile updates.
     */
    protected function requiresPasswordConfirmation($user)
    {
        $rolesRequiringPassword = [
            'admin', 'finance', 'audit', 'it', 
            'contractor', 'validation', 'activation'
        ];

        return $user->roles->pluck('name')->intersect($rolesRequiringPassword)->isNotEmpty();
    }

    /**
     * Show the user's status history.
     */
    public function statusHistory()
    {
        $user = Auth::user();
        $statusBadge = $this->userStatusService->getStatusBadgeHtml($user->status ?? 'active');
        $statusHistories = StatusHistory::where([
            'model_id' => $user->id,
            'model_type' => User::class,
        ])->with('user')->orderBy('created_at', 'desc')->paginate(15);
        
        return view('profile.status-history', compact('user', 'statusHistories', 'statusBadge'));
    }

    /**
     * Update the user's status (admin only).
     */
    public function updateStatus(Request $request, string $userId)
    {
        // Check if the current user has admin or super-admin role using DB query
        $currentUser = Auth::user();
        if (!$currentUser) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }
        
        // Check admin role directly from the database
        $adminRoles = Role::whereIn('name', ['admin', 'super-admin'])
            ->join('model_has_roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_id', $currentUser->id)
            ->where('model_has_roles.model_type', User::class)
            ->count();
            
        if ($adminRoles === 0) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }
        
        $request->validate([
            'status' => ['required', 'string', Rule::in(array_keys($this->userStatusService->getAvailableStatuses()))],
            'comment' => ['nullable', 'string', 'max:255'],
        ]);
        
        $user = User::findOrFail($userId);
        $result = $this->userStatusService->changeStatus(
            $user,
            $request->status,
            $request->comment,
            ['updated_by_admin' => true]
        );
        
        if ($result) {
            // Send notification to the user about status change
            try {
                $this->notificationService->sendEmail(
                    $user->email,
                    'Account Status Updated',
                    'emails.account.status-updated',
                    [
                        'name' => $user->name ?? $user->company_name,
                        'status' => ucfirst($request->status),
                        'comment' => $request->comment,
                    ],
                    'account_status_update',
                    'medium'
                );
            } catch (\Exception $e) {
                Log::warning("Failed to send status update notification", [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }
            
            return redirect()->back()->with('success', 'User status updated successfully.');
        }
        
        return redirect()->back()->with('error', 'Failed to update user status.');
    }
} 