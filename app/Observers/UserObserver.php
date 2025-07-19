<?php

namespace App\Observers;

use App\Models\User;
use App\Helpers\LogHelper;

class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        LogHelper::activity('User created', [
            'type' => 'user_created',
            'subject' => $user,
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'registration_type' => $user->registration_type
        ]);
    }

    /**
     * Handle the User "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        // Get the dirty (changed) attributes
        $dirtyAttributes = $user->getDirty();
        
        // Skip logging if only OTP or timestamps were updated
        $otpRelatedFields = ['otp', 'otp_created_at', 'updated_at', 'last_login_at'];
        $nonOtpUpdates = array_diff(array_keys($dirtyAttributes), $otpRelatedFields);
        
        if (count($nonOtpUpdates) > 0) {
            // Log only if there are changes other than OTP related fields
            LogHelper::logProfileUpdate($user, 'User profile updated', [
                'changes' => $nonOtpUpdates,
                'old_values' => array_intersect_key($user->getOriginal(), $dirtyAttributes),
                'new_values' => array_intersect_key($dirtyAttributes, array_flip($nonOtpUpdates))
            ]);
        }
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        LogHelper::activity('User deleted', [
            'type' => 'user_deleted',
            'subject' => $user,
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'mobile' => $user->mobile
        ]);
    }

    /**
     * Handle the User "restored" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        LogHelper::activity('User restored', [
            'type' => 'user_restored',
            'subject' => $user,
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'mobile' => $user->mobile
        ]);
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        LogHelper::activity('User permanently deleted', [
            'type' => 'user_force_deleted',
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'mobile' => $user->mobile
        ]);
    }
} 