<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\WalletApprovalWorkflow;
use App\Models\WalletApprovalStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalWorkflowController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $workflows = WalletApprovalWorkflow::with(['creator', 'steps.user'])
            ->latest()
            ->paginate(10);
        
        return view('admin.approval-workflows.index', compact('workflows'));
    }

    public function create()
    {
        $users = User::all();
        
        return view('admin.approval-workflows.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'notify_by_email' => 'boolean',
            'notify_by_sms' => 'boolean',
            'approvers' => 'required|array|min:1',
            'approvers.*' => 'exists:users,id'
        ]);

        try {
            DB::beginTransaction();
            
            $workflow = WalletApprovalWorkflow::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_active' => $request->has('is_active'),
                'notify_by_email' => $request->has('notify_by_email'),
                'notify_by_sms' => $request->has('notify_by_sms'),
                'created_by' => auth()->id(),
                'updated_by' => auth()->id()
            ]);

            // Add approvers as steps
            foreach ($request->approvers as $order => $userId) {
                $workflow->steps()->create([
                    'user_id' => $userId,
                    'order' => $order + 1,
                    'is_active' => true,
                    'is_required' => true,
                    'can_edit' => false,
                    'can_reject' => true
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('admin.approval-workflows.index')
                ->with('success', 'Approval workflow created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error creating approval workflow: ' . $e->getMessage());
        }
    }

    public function edit(WalletApprovalWorkflow $workflow)
    {
        $workflow->load('steps.user');
        $users = User::all();
        
        return view('admin.approval-workflows.edit', compact('workflow', 'users'));
    }

    public function update(Request $request, WalletApprovalWorkflow $workflow)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'notify_by_email' => 'boolean',
            'notify_by_sms' => 'boolean',
            'approvers' => 'required|array|min:1',
            'approvers.*' => 'exists:users,id'
        ]);

        try {
            DB::beginTransaction();
            
            $workflow->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_active' => $request->has('is_active'),
                'notify_by_email' => $request->has('notify_by_email'),
                'notify_by_sms' => $request->has('notify_by_sms'),
                'updated_by' => auth()->id()
            ]);

            // Remove existing steps
            $workflow->steps()->delete();
            
            // Add new steps
            foreach ($request->approvers as $order => $userId) {
                $workflow->steps()->create([
                    'user_id' => $userId,
                    'order' => $order + 1,
                    'is_active' => true,
                    'is_required' => true,
                    'can_edit' => false,
                    'can_reject' => true
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('admin.approval-workflows.index')
                ->with('success', 'Approval workflow updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error updating approval workflow: ' . $e->getMessage());
        }
    }

    public function destroy(WalletApprovalWorkflow $workflow)
    {
        try {
            // Check if workflow has any requests
            if ($workflow->requests()->exists()) {
                return back()->with('error', 'Cannot delete workflow with existing requests.');
            }
            
            $workflow->steps()->delete();
            $workflow->delete();
            
            return redirect()->route('admin.approval-workflows.index')
                ->with('success', 'Approval workflow deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting workflow: ' . $e->getMessage());
        }
    }

    public function updateStepOrder(Request $request, WalletApprovalWorkflow $workflow)
    {
        $validated = $request->validate([
            'steps' => 'required|array',
            'steps.*' => 'exists:wallet_approval_steps,id'
        ]);

        try {
            $workflow->reorderSteps($request->steps);
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
} 