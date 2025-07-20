<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalStep extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'approval_workflow_id',
        'name',
        'sequence',
        'approver_type',
        'approver_id',
        'is_required',
        'timeout_hours'
    ];

    /**
     * Get the workflow that owns this step.
     */
    public function workflow()
    {
        return $this->belongsTo(ApprovalWorkflow::class, 'approval_workflow_id');
    }

    /**
     * Get the approvals for this step.
     */
    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }

    /**
     * Get the approver based on approver_type and approver_id.
     */
    public function getApprover()
    {
        switch ($this->approver_type) {
            case 'user':
                return User::find($this->approver_id);
            case 'role':
                // Assuming Spatie roles
                return \Spatie\Permission\Models\Role::find($this->approver_id);
            case 'department':
                // Assuming Department model exists
                return Department::find($this->approver_id);
            default:
                return null;
        }
    }

    /**
     * Get users who can approve this step.
     */
    public function getApproverUsers()
    {
        switch ($this->approver_type) {
            case 'user':
                return User::where('id', $this->approver_id)->get();
            case 'role':
                // Assuming Spatie roles
                return User::role($this->approver_id)->get();
            case 'department':
                // Assuming users belong to departments
                return User::where('department_id', $this->approver_id)->get();
            default:
                return collect();
        }
    }
} 