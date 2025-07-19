<?php

namespace App\Services;

use App\Models\User;
use App\Models\StatusHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserStatusService
{
    /**
     * Change a user's status and record the change in the status history.
     *
     * @param User $user The user model
     * @param string $newStatus The new status value
     * @param string|null $comment Optional comment about the status change
     * @param array $metadata Optional additional data to store
     * @return bool Whether the status change was successful
     */
    public function changeStatus(User $user, string $newStatus, ?string $comment = null, array $metadata = []): bool
    {
        try {
            // Get previous status
            $previousStatus = $user->status ?? 'active';
            
            // Update user status
            $user->status = $newStatus;
            $saved = $user->save();
            
            if (!$saved) {
                Log::error('Failed to update user status', [
                    'user_id' => $user->id,
                    'previous_status' => $previousStatus,
                    'new_status' => $newStatus
                ]);
                return false;
            }
            
            // Record status change in history
            $history = new StatusHistory();
            $history->model_type = get_class($user);
            $history->model_id = $user->id;
            $history->previous_status = $previousStatus;
            $history->new_status = $newStatus;
            $history->comment = $comment;
            $history->user_id = Auth::id(); // Current user making the change
            $history->metadata = $metadata;
            $history->save();
            
            Log::info('User status changed', [
                'user_id' => $user->id,
                'previous_status' => $previousStatus,
                'new_status' => $newStatus,
                'changed_by' => Auth::id(),
                'comment' => $comment
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Exception when changing user status', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return false;
        }
    }
    
    /**
     * Get all valid user statuses
     * 
     * @return array
     */
    public function getAvailableStatuses(): array
    {
        return [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'suspended' => 'Suspended',
            'pending_verification' => 'Pending Verification',
            'blocked' => 'Blocked'
        ];
    }
    
    /**
     * Get status badge HTML
     * 
     * @param string $status
     * @return string
     */
    public function getStatusBadgeHtml(string $status): string
    {
        $classes = [
            'active' => 'bg-success',
            'inactive' => 'bg-secondary',
            'suspended' => 'bg-warning',
            'pending_verification' => 'bg-info',
            'blocked' => 'bg-danger'
        ];
        
        $class = $classes[$status] ?? 'bg-secondary';
        $label = ucfirst(str_replace('_', ' ', $status));
        
        return '<span class="badge ' . $class . '">' . $label . '</span>';
    }
} 