<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApprovalWorkflow extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the steps for this workflow.
     */
    public function steps()
    {
        return $this->hasMany(ApprovalStep::class)->orderBy('order');
    }

    /**
     * Get all instances of this workflow.
     */
    public function instances()
    {
        return $this->hasMany(ApprovalInstance::class);
    }

    /**
     * Check if this workflow is active.
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Get the first approval step.
     */
    public function getFirstStep()
    {
        return $this->steps()->orderBy('order')->first();
    }

    /**
     * Get the next step after the given step.
     */
    public function getNextStep(ApprovalStep $currentStep)
    {
        return $this->steps()
            ->where('order', '>', $currentStep->order)
            ->orderBy('order')
            ->first();
    }

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Helper Methods
    public function activate()
    {
        $this->status = 'active';
        $this->save();
    }

    public function deactivate()
    {
        $this->status = 'inactive';
        $this->save();
    }
} 