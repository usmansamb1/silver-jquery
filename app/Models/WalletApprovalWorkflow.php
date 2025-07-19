<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class WalletApprovalWorkflow extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'notify_by_email',
        'notify_by_sms',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'notify_by_email' => 'boolean',
        'notify_by_sms' => 'boolean'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function steps()
    {
        return $this->hasMany(WalletApprovalStep::class, 'workflow_id')->orderBy('order');
    }

    public function requests()
    {
        return $this->hasMany(WalletApprovalRequest::class, 'workflow_id');
    }

    public function addStep(User $user, int $order, array $attributes = [])
    {
        return DB::transaction(function () use ($user, $order, $attributes) {
            // Reorder existing steps if needed
            if ($this->steps()->where('order', '>=', $order)->exists()) {
                $this->steps()
                    ->where('order', '>=', $order)
                    ->increment('order');
            }

            return $this->steps()->create(array_merge([
                'user_id' => $user->id,
                'order' => $order,
                'is_active' => true,
                'is_required' => true,
                'can_edit' => false,
                'can_reject' => true
            ], $attributes));
        });
    }

    public function removeStep(WalletApprovalStep $step)
    {
        return DB::transaction(function () use ($step) {
            // Reorder remaining steps
            $this->steps()
                ->where('order', '>', $step->order)
                ->decrement('order');

            return $step->delete();
        });
    }

    public function reorderSteps(array $stepIds)
    {
        return DB::transaction(function () use ($stepIds) {
            foreach ($stepIds as $order => $stepId) {
                $this->steps()
                    ->where('id', $stepId)
                    ->update(['order' => $order + 1]);
            }
        });
    }

    public function getNextApprover(WalletApprovalRequest $request)
    {
        $lastAction = $request->actions()->latest()->first();
        $currentOrder = $lastAction ? $lastAction->step->order : 0;

        return $this->steps()
            ->where('order', '>', $currentOrder)
            ->where('is_active', true)
            ->orderBy('order')
            ->first();
    }

    public function isComplete(WalletApprovalRequest $request)
    {
        $requiredSteps = $this->steps()
            ->where('is_active', true)
            ->where('is_required', true)
            ->count();

        $completedSteps = $request->actions()
            ->whereIn('action', ['approve', 'reject'])
            ->count();

        return $completedSteps >= $requiredSteps;
    }
} 