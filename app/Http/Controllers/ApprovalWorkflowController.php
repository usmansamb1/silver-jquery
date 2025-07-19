<?php

namespace App\Http\Controllers;

use App\Models\ApprovalWorkflow;
use App\Models\ApprovalStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalWorkflowController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $workflows = ApprovalWorkflow::with('steps')->get();
        return view('approval.workflows.index', compact('workflows'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get users, roles, and departments for selection
        $users = \App\Models\User::all();
        $roles = \Spatie\Permission\Models\Role::all();
        // Assume a Department model exists
        $departments = \App\Models\Department::all();

        return view('approval.workflows.create', compact('users', 'roles', 'departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'model_type' => 'nullable|string',
            'is_active' => 'boolean',
            'notify_by_email' => 'boolean',
            'notify_by_sms' => 'boolean',
            'steps' => 'required|array',
            'steps.*.name' => 'required|string|max:255',
            'steps.*.sequence' => 'required|integer',
            'steps.*.approver_type' => 'required|string|in:user,role,department',
            'steps.*.approver_id' => 'required|string',
            'steps.*.is_required' => 'boolean',
            'steps.*.timeout_hours' => 'nullable|integer',
        ]);

        DB::beginTransaction();

        try {
            $workflow = ApprovalWorkflow::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'model_type' => $validated['model_type'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
                'notify_by_email' => $validated['notify_by_email'] ?? true,
                'notify_by_sms' => $validated['notify_by_sms'] ?? false,
            ]);

            foreach ($validated['steps'] as $stepData) {
                $workflow->steps()->create([
                    'name' => $stepData['name'],
                    'sequence' => $stepData['sequence'],
                    'approver_type' => $stepData['approver_type'],
                    'approver_id' => $stepData['approver_id'],
                    'is_required' => $stepData['is_required'] ?? true,
                    'timeout_hours' => $stepData['timeout_hours'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('approval.workflows.index')
                ->with('success', 'Workflow created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to create workflow: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ApprovalWorkflow $workflow)
    {
        $workflow->load('steps');
        return view('approval.workflows.show', compact('workflow'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ApprovalWorkflow $workflow)
    {
        $workflow->load('steps');
        $users = \App\Models\User::all();
        $roles = \Spatie\Permission\Models\Role::all();
        $departments = \App\Models\Department::all();

        return view('approval.workflows.edit', compact('workflow', 'users', 'roles', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ApprovalWorkflow $workflow)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'model_type' => 'nullable|string',
            'is_active' => 'boolean',
            'notify_by_email' => 'boolean',
            'notify_by_sms' => 'boolean',
            'steps' => 'required|array',
            'steps.*.id' => 'nullable|string',
            'steps.*.name' => 'required|string|max:255',
            'steps.*.sequence' => 'required|integer',
            'steps.*.approver_type' => 'required|string|in:user,role,department',
            'steps.*.approver_id' => 'required|string',
            'steps.*.is_required' => 'boolean',
            'steps.*.timeout_hours' => 'nullable|integer',
        ]);

        DB::beginTransaction();

        try {
            $workflow->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'model_type' => $validated['model_type'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
                'notify_by_email' => $validated['notify_by_email'] ?? true,
                'notify_by_sms' => $validated['notify_by_sms'] ?? false,
            ]);

            // Get existing step IDs
            $existingStepIds = $workflow->steps->pluck('id')->toArray();
            $updatedStepIds = [];

            foreach ($validated['steps'] as $stepData) {
                if (isset($stepData['id']) && !empty($stepData['id'])) {
                    // Update existing step
                    $step = ApprovalStep::find($stepData['id']);
                    if ($step && $step->approval_workflow_id == $workflow->id) {
                        $step->update([
                            'name' => $stepData['name'],
                            'sequence' => $stepData['sequence'],
                            'approver_type' => $stepData['approver_type'],
                            'approver_id' => $stepData['approver_id'],
                            'is_required' => $stepData['is_required'] ?? true,
                            'timeout_hours' => $stepData['timeout_hours'] ?? null,
                        ]);
                        $updatedStepIds[] = $step->id;
                    }
                } else {
                    // Create new step
                    $step = $workflow->steps()->create([
                        'name' => $stepData['name'],
                        'sequence' => $stepData['sequence'],
                        'approver_type' => $stepData['approver_type'],
                        'approver_id' => $stepData['approver_id'],
                        'is_required' => $stepData['is_required'] ?? true,
                        'timeout_hours' => $stepData['timeout_hours'] ?? null,
                    ]);
                    $updatedStepIds[] = $step->id;
                }
            }

            // Delete steps that are no longer needed
            $stepsToDelete = array_diff($existingStepIds, $updatedStepIds);
            if (!empty($stepsToDelete)) {
                ApprovalStep::whereIn('id', $stepsToDelete)->delete();
            }

            DB::commit();

            return redirect()->route('approval.workflows.index')
                ->with('success', 'Workflow updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to update workflow: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApprovalWorkflow $workflow)
    {
        try {
            // Check if workflow is in use
            $instanceCount = $workflow->instances()->count();
            if ($instanceCount > 0) {
                return back()->withErrors(['error' => 'Cannot delete workflow that is in use']);
            }

            DB::beginTransaction();
            
            // Delete steps first
            $workflow->steps()->delete();
            
            // Delete workflow
            $workflow->delete();
            
            DB::commit();
            
            return redirect()->route('approval.workflows.index')
                ->with('success', 'Workflow deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete workflow: ' . $e->getMessage()]);
        }
    }
} 