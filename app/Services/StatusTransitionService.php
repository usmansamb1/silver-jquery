<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ApprovalStatus;
use App\Models\StepStatus;
use App\Models\StatusHistory;
use App\Models\User;

class StatusTransitionService
{
    /**
     * The model instance that is being transitioned
     */
    protected $model;

    /**
     * The status field name on the model
     */
    protected $statusField = 'status';

    /**
     * The status ID field name on the model
     */
    protected $statusIdField = 'status_id';

    /**
     * Status model class name
     */
    protected $statusModelClass;
    
    /**
     * Transition events
     */
    protected $events = [];

    /**
     * Create a new status transition service
     *
     * @param Model $model
     * @param string $statusModelClass
     * @param string $statusField
     * @param string $statusIdField
     */
    public function __construct(Model $model, string $statusModelClass = ApprovalStatus::class, string $statusField = 'status', string $statusIdField = 'status_id')
    {
        $this->model = $model;
        $this->statusModelClass = $statusModelClass;
        $this->statusField = $statusField;
        $this->statusIdField = $statusIdField;
    }

    /**
     * Get allowed status transitions for the current status
     *
     * @return array
     */
    public function getAllowedTransitions(): array
    {
        $currentStatus = $this->model->{$this->statusField};
        
        // Define allowed transitions based on current status
        return match($currentStatus) {
            ApprovalStatus::PENDING => [
                ApprovalStatus::APPROVED,
                ApprovalStatus::REJECTED,
                ApprovalStatus::IN_PROGRESS,
                ApprovalStatus::CANCELLED
            ],
            ApprovalStatus::IN_PROGRESS => [
                ApprovalStatus::APPROVED,
                ApprovalStatus::REJECTED,
                ApprovalStatus::CANCELLED
            ],
            ApprovalStatus::APPROVED => [
                ApprovalStatus::CANCELLED
            ],
            ApprovalStatus::REJECTED, ApprovalStatus::CANCELLED => [
                // No further transitions allowed for these final states
            ],
            default => [
                ApprovalStatus::PENDING,
                ApprovalStatus::APPROVED,
                ApprovalStatus::REJECTED
            ]
        };
    }

    /**
     * Check if a transition is allowed
     *
     * @param string $targetStatus
     * @return bool
     */
    public function canTransitionTo(string $targetStatus): bool
    {
        return in_array($targetStatus, $this->getAllowedTransitions());
    }

    /**
     * Check if user has permission to perform this transition
     * 
     * @param string $targetStatus
     * @return bool
     */
    public function userCanTransition(string $targetStatus): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        // Allow admins to perform any transition
        if ($this->userHasRole($user, 'admin')) {
            return true;
        }
        
        $currentStatus = $this->model->{$this->statusField};
        
        // Specific role-based permission checks
        if ($currentStatus === ApprovalStatus::PENDING && $targetStatus === ApprovalStatus::APPROVED) {
            // Check if user has any of these roles
            return $this->userHasAnyRole($user, ['finance', 'validation', 'activation']);
        }
        
        if ($currentStatus === ApprovalStatus::PENDING && $targetStatus === ApprovalStatus::REJECTED) {
            // Check if user has any of these roles
            return $this->userHasAnyRole($user, ['finance', 'validation', 'activation']);
        }
        
        // Default to false for undefined transitions
        return false;
    }
    
    /**
     * Check if a user has a specific role
     * 
     * @param \Illuminate\Contracts\Auth\Authenticatable|null $user
     * @param string $role
     * @return bool
     */
    protected function userHasRole($user, string $role): bool
    {
        if (!$user) {
            return false;
        }
        
        // Check if user is in role model
        if (property_exists($user, 'roles')) {
            return $user->roles->pluck('name')->contains($role);
        }
        
        return false;
    }
    
    /**
     * Check if a user has any of the given roles
     * 
     * @param \Illuminate\Contracts\Auth\Authenticatable|null $user
     * @param array $roles
     * @return bool
     */
    protected function userHasAnyRole($user, array $roles): bool
    {
        // Check each role individually
        foreach ($roles as $role) {
            if ($this->userHasRole($user, $role)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Register status transition hooks
     * 
     * @param string $fromStatus
     * @param string $toStatus
     * @param callable $callback
     * @return $this
     */
    public function registerTransitionHook(string $fromStatus, string $toStatus, callable $callback)
    {
        $key = "{$fromStatus}_to_{$toStatus}";
        
        if (!isset($this->events[$key])) {
            $this->events[$key] = [];
        }
        
        $this->events[$key][] = $callback;
        
        return $this;
    }

    /**
     * Register a pre-transition hook for any transition
     * 
     * @param callable $callback
     * @return $this
     */
    public function beforeAnyTransition(callable $callback)
    {
        if (!isset($this->events['before_any'])) {
            $this->events['before_any'] = [];
        }
        
        $this->events['before_any'][] = $callback;
        
        return $this;
    }

    /**
     * Register a post-transition hook for any transition
     * 
     * @param callable $callback
     * @return $this
     */
    public function afterAnyTransition(callable $callback)
    {
        if (!isset($this->events['after_any'])) {
            $this->events['after_any'] = [];
        }
        
        $this->events['after_any'][] = $callback;
        
        return $this;
    }

    /**
     * Trigger transition events
     * 
     * @param string $fromStatus
     * @param string $toStatus
     * @param string $eventType before|after
     * @return void
     */
    protected function triggerEvents(string $fromStatus, string $toStatus, string $eventType)
    {
        // Trigger specific transition events
        $key = "{$fromStatus}_to_{$toStatus}";
        
        if (isset($this->events[$key])) {
            foreach ($this->events[$key] as $callback) {
                call_user_func($callback, $this->model, $fromStatus, $toStatus);
            }
        }
        
        // Trigger global events
        $globalKey = "{$eventType}_any";
        
        if (isset($this->events[$globalKey])) {
            foreach ($this->events[$globalKey] as $callback) {
                call_user_func($callback, $this->model, $fromStatus, $toStatus);
            }
        }
    }

    /**
     * Transition to the given status
     *
     * @param string $targetStatus
     * @param array $additionalData Additional data to update
     * @return bool
     */
    public function transitionTo(string $targetStatus, array $additionalData = []): bool
    {
        $currentStatus = $this->model->{$this->statusField} ?? null;
        
        // Log the transition attempt
        Log::info("Attempting status transition", [
            'model' => get_class($this->model),
            'id' => $this->model->getKey(),
            'from' => $currentStatus,
            'to' => $targetStatus,
            'user_id' => Auth::id() ?? 'system'
        ]);
        
        // Check if the transition is allowed
        if (!$this->isTransitionAllowed($currentStatus, $targetStatus)) {
            Log::warning("Transition not allowed", [
                'model' => get_class($this->model),
                'id' => $this->model->getKey(),
                'current' => $currentStatus,
                'target' => $targetStatus
            ]);
            
            return false;
        }
        
        try {
            DB::beginTransaction();
            
            // Trigger before events
            $this->triggerEvents($currentStatus, $targetStatus, 'before');
            
            // Find status ID if it exists
            $statusId = null;
            if (class_exists($this->statusModelClass)) {
                $statusModel = $this->statusModelClass::where('code', $targetStatus)->first();
                if ($statusModel) {
                    $statusId = $statusModel->id;
                }
            }
            
            // Update the model
            $updateData = array_merge([
                $this->statusField => $targetStatus
            ], $additionalData);
            
            // Add status_id if available
            if ($statusId && property_exists($this->model, $this->statusIdField)) {
                $updateData[$this->statusIdField] = $statusId;
            }
            
            Log::info("Updating model with data", [
                'model' => get_class($this->model),
                'id' => $this->model->getKey(),
                'update_data' => $updateData
            ]);
            
            $this->model->update($updateData);
            
            // Record transition in history if model has history relationship
            // Skip this if WalletApprovalRequest since it handles it separately
            if (method_exists($this->model, 'statusHistory') && !($this->model instanceof \App\Models\WalletApprovalRequest)) {
                $historyData = [
                    'status_from' => $currentStatus,
                    'status_to' => $targetStatus,
                    'user_id' => Auth::id(),
                    'notes' => $additionalData['comment'] ?? null
                ];
                
                Log::info("Creating status history record", [
                    'model' => get_class($this->model),
                    'id' => $this->model->getKey(),
                    'history_data' => $historyData
                ]);
                
                $this->model->statusHistory()->create($historyData);
            }
            
            // Trigger after events
            $this->triggerEvents($currentStatus, $targetStatus, 'after');
            
            DB::commit();
            
            Log::info("Status transition completed", [
                'model' => get_class($this->model),
                'id' => $this->model->getKey(),
                'from' => $currentStatus,
                'to' => $targetStatus,
                'user_id' => Auth::id() ?? 'system'
            ]);
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error("Status transition failed", [
                'model' => get_class($this->model),
                'id' => $this->model->getKey(),
                'from' => $currentStatus,
                'to' => $targetStatus,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return false;
        }
    }

    /**
     * Change status of a model and record the history
     *
     * @param Model $model The model to change status
     * @param string $newStatus The new status
     * @param string|null $comment Optional comment about the status change
     * @param array $metadata Optional additional data to store
     * @return bool
     */
    public function changeStatus(Model $model, string $newStatus, ?string $comment = null, array $metadata = []): bool
    {
        $previousStatus = $model->status ?? null;
        
        // Don't record if status hasn't changed
        if ($previousStatus === $newStatus) {
            return false;
        }
        
        // Begin transaction
        DB::beginTransaction();
        
        try {
            // Update the model's status
            $model->status = $newStatus;
            $model->save();
            
            // Record the status change history
            StatusHistory::create([
                'model_id' => $model->getKey(),
                'model_type' => get_class($model),
                'previous_status' => $previousStatus,
                'new_status' => $newStatus,
                'comment' => $comment,
                'user_id' => Auth::id(),
                'metadata' => $metadata,
            ]);
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Status change failed: ' . $e->getMessage(), [
                'model' => get_class($model),
                'id' => $model->getKey(),
                'from' => $previousStatus,
                'to' => $newStatus,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Get status history for a model
     *
     * @param Model $model
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getStatusHistory(Model $model)
    {
        return StatusHistory::where([
            'model_id' => $model->getKey(),
            'model_type' => get_class($model),
        ])->with('user')->orderBy('created_at', 'desc')->get();
    }

    /**
     * Check if the transition from current status to target status is allowed
     *
     * @param string|null $currentStatus
     * @param string $targetStatus
     * @return bool
     */
    protected function isTransitionAllowed(?string $currentStatus, string $targetStatus): bool
    {
        // If no current status (new object), always allow setting initial status
        if ($currentStatus === null) {
            return true;
        }
        
        // Check if this is a valid transition according to business rules
        // For now, we're just allowing any transition, but this is where you would
        // implement specific validation rules between statuses
        
        // Same status is allowed (no change)
        if ($currentStatus === $targetStatus) {
            return true;
        }
        
        // Check if user has permission to perform this transition
        if (!$this->userCanMakeTransition($currentStatus, $targetStatus)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Check if the current user has permission to make this transition
     * 
     * @param string $currentStatus
     * @param string $targetStatus
     * @return bool
     */
    protected function userCanMakeTransition(string $currentStatus, string $targetStatus): bool
    {
        // Get the current user
        $user = Auth::user();
        
        // No user, no transition (system can still make transitions)
        if (!$user && Auth::id() !== null) {
            return false;
        }
        
        // For now allow all transitions - implement specific role checks here if needed
        return true;
    }
} 