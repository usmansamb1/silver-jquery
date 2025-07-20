<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Wallet;
use App\Models\StepStatus;
use Spatie\Permission\Traits\HasRoles;

class WalletApprovalRequest extends Model
{
    use SoftDeletes, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'v2_wallet_approval_requests';

    protected $fillable = [
        'reference_no',
        'workflow_id',
        'user_id',
        'status_id',
        'amount',
        'currency',
        'description',
        'metadata',
        'approved_at',
        'rejected_at',
        'payment_id',
        'status',
        'current_step',
        'rejection_reason',
        'completed_at',
        'transaction_complete',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'completed_at' => 'datetime',
        'transaction_complete' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($request) {
            if (empty($request->{$request->getKeyName()})) {
                $request->{$request->getKeyName()} = Str::uuid()->toString();
            }
            // Only set reference_no if the column exists
            if (Schema::hasColumn('v2_wallet_approval_requests', 'reference_no')) {
                $request->reference_no = static::generateReferenceNumber();
            }
        });
    }

    public static function generateReferenceNumber()
    {
        try {
            $maxAttempts = 10; // Prevent infinite loops
            $attempts = 0;
            
            do {
                $number = 'JOiL-' . strtoupper(Str::random(8));
                $attempts++;
                
                // Check if the column exists before querying
                if (Schema::hasColumn('v2_wallet_approval_requests', 'reference_no')) {
                    $exists = static::where('reference_no', $number)->exists();
                } else {
                    // If column doesn't exist yet, just return the generated number
                    $exists = false;
                }
            } while ($exists && $attempts < $maxAttempts);
            
            if ($attempts >= $maxAttempts) {
                Log::warning("Reached maximum attempts to generate unique reference number");
                // Add timestamp to ensure uniqueness
                $number = 'JOiL-' . strtoupper(Str::random(5)) . time();
            }
            
            return $number;
        } catch (\Exception $e) {
            // Log the error but don't halt execution
            Log::error("Error generating reference number: " . $e->getMessage());
            // Fallback to a timestamp-based reference number in case of errors
            return 'JOiL-' . time() . rand(100, 999);
        }
    }

    public function workflow()
    {
        return $this->belongsTo(WalletApprovalWorkflow::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function status()
    {
        return $this->belongsTo(ApprovalStatus::class);
    }

    public function actions()
    {
        return $this->hasMany(WalletApprovalAction::class, 'request_id');
    }

    public function notifications()
    {
        return $this->hasMany(WalletApprovalNotification::class, 'request_id');
    }

    public function getCurrentStep()
    {
        return $this->steps()
                    ->where('status', StepStatus::PENDING)
                    ->where('step_order', $this->current_step)
                    ->first();
    }

    public function approve($comment = null)
    {
        $currentStep = $this->getCurrentStep();
        if (!$currentStep) {
            throw new \Exception('No pending steps found');
        }

        DB::transaction(function () use ($currentStep, $comment) {
            // Update current step
            $currentStep->setStatusByCode(StepStatus::APPROVED);
            $currentStep->comment = $comment;
            $currentStep->processed_at = now();
            $currentStep->save();

            // Get next step
            $nextStep = $this->steps()
                            ->where('step_order', '>', $currentStep->step_order)
                            ->where('status', StepStatus::PENDING)
                            ->orderBy('step_order')
                            ->first();

            if ($nextStep) {
                // Move to next step
                $this->update(['current_step' => $nextStep->step_order]);
            } else {
                // Complete the request
                $status = ApprovalStatus::where('code', ApprovalStatus::APPROVED)->first();
                
                $this->update([
                    'status' => ApprovalStatus::APPROVED,
                    'status_id' => $status ? $status->id : null,
                    'current_step' => null,
                    'completed_at' => now()
                ]);

                // Update payment status
                $this->payment->update(['status' => 'approved']);

                // Get or create the user's wallet
                $wallet = Wallet::firstOrCreate(
                    ['user_id' => $this->user_id],
                    ['balance' => 0]
                );
                
                // Record the deposit transaction
                $wallet->deposit(
                    $this->amount,
                    'Wallet top-up via ' . $this->payment->payment_type,
                    $this->payment,
                    [
                        'approval_request_id' => $this->id,
                        'payment_notes' => $this->payment->notes
                    ]
                );
            }
        });
    }

    public function reject($comment)
    {
        $currentStep = $this->getCurrentStep();
        if (!$currentStep) {
            throw new \Exception('No pending steps found');
        }

        DB::transaction(function () use ($currentStep, $comment) {
            // Update current step
            $currentStep->setStatusByCode(StepStatus::REJECTED);
            $currentStep->comment = $comment;
            $currentStep->processed_at = now();
            $currentStep->save();

            // Update request status
            $status = ApprovalStatus::where('code', ApprovalStatus::REJECTED)->first();
            
            $this->update([
                'status' => ApprovalStatus::REJECTED,
                'status_id' => $status ? $status->id : null,
                'current_step' => null,
                'rejection_reason' => $comment
            ]);

            // Update payment status
            $this->payment->update(['status' => 'rejected']);
        });
    }

    public function notifyApprover(WalletApprovalStep $step)
    {
        if ($this->workflow->notify_by_email) {
            $this->notifications()->create([
                'user_id' => $step->user_id,
                'type' => 'email',
                'message' => "You have a new wallet approval request ({$this->reference_no}) pending your review."
            ]);
        }

        if ($this->workflow->notify_by_sms) {
            $this->notifications()->create([
                'user_id' => $step->user_id,
                'type' => 'sms',
                'message' => "You have a new wallet approval request ({$this->reference_no}) pending your review."
            ]);
        }
    }

    /**
     * Check if the given user can approve this request
     */
    public function canBeApprovedBy(User $user): bool
    {
        try {
            // Log the authorization attempt
            Log::info('Checking approval authorization:', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'request_id' => $this->id,
                'current_step' => $this->current_step,
                'request_status' => $this->status
            ]);

            // Get the current step
            $currentStep = $this->steps()
                        ->where('status', 'pending')
                        ->where('step_order', $this->current_step)
                        ->first();
            
            if (!$currentStep) {
                Log::warning('No current step found for request', [
                    'request_id' => $this->id,
                    'current_step' => $this->current_step
                ]);
                return false;
            }

            // Log current step details
            Log::info('Current step details:', [
                'step_id' => $currentStep->id,
                'step_user_id' => $currentStep->user_id,
                'step_role' => $currentStep->role,
                'step_order' => $currentStep->step_order
            ]);

            // Check if user has the required role using Spatie's hasRole
            $hasRole = $user->hasRole($currentStep->role);
            
            // Log role check
            Log::info('Role check:', [
                'user_roles' => $user->getRoleNames(),
                'required_role' => $currentStep->role,
                'has_role' => $hasRole
            ]);

            // Check all conditions
            $isAuthorized = $currentStep->user_id === $user->id && 
                           $hasRole &&
                           $this->status === 'pending';

            // Log final authorization result
            Log::info('Authorization result:', [
                'is_authorized' => $isAuthorized,
                'user_id_match' => $currentStep->user_id === $user->id,
                'has_role' => $hasRole,
                'status_check' => $this->status === 'pending'
            ]);

            return $isAuthorized;

        } catch (\Exception $e) {
            Log::error('Error in canBeApprovedBy:', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'request_id' => $this->id
            ]);
            return false;
        }
    }

    public function getStatusColorAttribute()
    {
        return $this->status->color ?? '#000000';
    }

    public function getStatusNameAttribute()
    {
        return $this->status->name ?? 'Unknown';
    }

    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2) . ' ' . $this->currency;
    }

    /**
     * Get the metadata attribute.
     *
     * @param  string|null  $value
     * @return array|null
     */
    public function getMetadataAttribute($value)
    {
        // Simply return the value as Laravel already handles the casting
        return $value;
    }

    /**
     * Set the metadata attribute.
     *
     * @param  array|null  $value
     * @return void
     */
    public function setMetadataAttribute($value)
    {
        if (is_null($value)) {
            $this->attributes['metadata'] = null;
            return;
        }
        
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $value = $decoded;
            }
        }
        
        $this->attributes['metadata'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * Get the payment associated with this approval request.
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Get all steps for this approval request
     */
    public function steps()
    {
        return $this->hasMany(WalletApprovalStep::class, 'request_id')
                    ->orderBy('step_order');
    }

    /**
     * Check if the request is in a pending state.
     */
    public function isPending()
    {
        return $this->status === ApprovalStatus::PENDING || 
               $this->status === 'finance_approved' || 
               $this->status === 'validation_approved';
    }

    /**
     * Check if the request is completed.
     */
    public function isCompleted()
    {
        return $this->status === ApprovalStatus::APPROVED || 
               $this->status === 'completed';
    }

    /**
     * Check if the request is rejected.
     */
    public function isRejected()
    {
        return $this->status === ApprovalStatus::REJECTED;
    }

    /**
     * Track status changes
     */
    public function statusHistory()
    {
        return $this->morphMany(StatusHistory::class, 'model');
    }

    /**
     * Record a status change
     *
     * @param string|null $oldStatus
     * @param string $newStatus
     * @param string|null $notes
     * @param array $metadata
     * @return StatusHistory
     */
    public function recordStatusChange(?string $oldStatus, string $newStatus, ?string $notes = null, array $metadata = []): StatusHistory
    {
        // Log the status change
        Log::info('Recording status change', [
            'request_id' => $this->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'user_id' => auth()->id() ?? 'system'
        ]);
        
        return $this->statusHistory()->create([
            'status_from' => $oldStatus ?? 'pending', // Default to pending if null
            'status_to' => $newStatus,
            'user_id' => auth()->id(),
            'notes' => $notes,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Change status using the transition service
     *
     * @param string $newStatus
     * @param string|null $notes
     * @param array $additionalData
     * @return bool
     */
    public function changeStatus(string $newStatus, ?string $notes = null, array $additionalData = []): bool
    {
        $oldStatus = $this->status;
        
        // Log status change request
        Log::info('Attempting status change', [
            'request_id' => $this->id,
            'from' => $oldStatus,
            'to' => $newStatus,
            'user_id' => auth()->id() ?? 'system'
        ]);
        
        try {
            // Use status transition service to validate and perform the change
            $transitionService = new \App\Services\StatusTransitionService($this);
            
            // Record the status change first if old and new status are different
            if ($oldStatus !== $newStatus) {
                // Try to record the change directly
                $this->recordStatusChange($oldStatus, $newStatus, $notes, $additionalData);
            }
            
            $result = $transitionService->transitionTo($newStatus, array_merge(
                ['comment' => $notes],
                $additionalData
            ));
            
            if ($result && $oldStatus !== $newStatus) {
                // Trigger status-specific handling
                if ($newStatus === ApprovalStatus::APPROVED) {
                    $this->handleApproval();
                } elseif ($newStatus === ApprovalStatus::REJECTED) {
                    $this->handleRejection($notes);
                }
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Status change failed', [
                'request_id' => $this->id,
                'from' => $oldStatus,
                'to' => $newStatus,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
    
    /**
     * Handle approval-specific logic
     */
    public function handleApproval()
    {
        try {
            DB::beginTransaction();
            
            // Update payment status if it exists
            if ($this->payment) {
                $this->payment->update(['status' => 'approved']);
                
                // Update wallet balance
                $wallet = Wallet::firstOrCreate(
                    ['user_id' => $this->user_id],
                    ['balance' => 0]
                );
                
                // If transaction isn't already completed, do it now
                if (!$this->transaction_complete) {
                    Log::info('Creating wallet transaction for approved request', [
                        'request_id' => $this->id,
                        'user_id' => $this->user_id,
                        'payment_id' => $this->payment->id,
                        'amount' => $this->amount,
                        'payment_type' => $this->payment->payment_type
                    ]);
                    
                    // Record the deposit transaction
                    $transaction = $wallet->deposit(
                        $this->amount,
                        'Wallet top-up via ' . $this->payment->payment_type,
                        $this->payment,
                        [
                            'approval_request_id' => $this->id,
                            'payment_method' => $this->payment->payment_type,
                            'reference_no' => $this->reference_no ?? null
                        ]
                    );
                    
                    // Mark as transaction complete
                    $this->transaction_complete = true;
                    $this->save();
                    
                    Log::info('Successfully created wallet transaction', [
                        'request_id' => $this->id,
                        'transaction_id' => $transaction->id,
                        'user_id' => $this->user_id,
                        'wallet_balance' => $wallet->balance,
                        'transaction_complete' => $this->transaction_complete
                    ]);
                } else {
                    Log::info('Skipping duplicate wallet transaction', [
                        'request_id' => $this->id,
                        'user_id' => $this->user_id,
                        'payment_id' => $this->payment_id,
                        'transaction_complete' => $this->transaction_complete
                    ]);
                }
            } else {
                Log::warning('Tried to handle approval but payment not found', [
                    'request_id' => $this->id,
                    'user_id' => $this->user_id,
                    'payment_id' => $this->payment_id ?? 'null'
                ]);
                DB::rollBack();
                return false;
            }
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in handleApproval', [
                'request_id' => $this->id, 
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
    
    /**
     * Handle rejection-specific logic
     */
    protected function handleRejection(?string $reason = null)
    {
        // Update payment status if it exists
        if ($this->payment) {
            $this->payment->update(['status' => 'rejected']);
        }
        
        // Log the event
        Log::info('Wallet request rejected', [
            'request_id' => $this->id,
            'user_id' => $this->user_id,
            'reason' => $reason
        ]);
    }
} 