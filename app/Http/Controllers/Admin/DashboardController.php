<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Payment;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get counts for dashboard cards
        $activeUsersCount = User::where('is_active', true)->count();
        $inactiveUsersCount = User::where('is_active', false)->count();
        $pendingWalletRequestsCount = Payment::where('status', 'pending')->count();

        return view('admin.dashboard', compact(
            'activeUsersCount',
            'inactiveUsersCount',
            'pendingWalletRequestsCount'
        ));
    }

    public function usersList()
    {
        $users = User::with('roles')
            ->when(request('filter') === 'trashed', function($query) {
                $query->onlyTrashed();
            })
            ->latest()
            ->paginate(10);

        $trashedCount = User::onlyTrashed()->count();

        return view('admin.users.index', compact('users', 'trashedCount'));
    }

    public function createUser()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function storeUser(Request $request)
    {

     
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'gender' => ['required', 'in:male,female,other'],
            'phone' => ['required', 'string', 'max:20', 'unique:users'],
            'mobile' => ['required', 'string', 'regex:/^(05\d{8}|5\d{8,9})$/', 'unique:users'],
            'roles' => ['required', 'array'],
            'roles.*' => [
                Rule::exists('roles', 'name'),
                function($attribute, $value, $fail) use ($request) {
                    // If customer role is selected, no other roles are allowed
                    if (in_array('customer', $request->roles) && count($request->roles) > 1) {
                        $fail('Customer role cannot be combined with other roles.');
                    }
                }
            ],
            'company_name' => ['required', 'string', 'max:255'],
            'company_type' => 'required_if:registration_type,company|in:private,semi Govt.,Govt',
            'city' => ['required', 'string', 'max:100'],
             
        ]);


       
        
        
    $usedata = [
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'gender' => $request->gender,
        'phone' => $request->phone,
        'mobile' => $request->mobile,
        'registration_type' => 'personal',
        'company_type' => $request->company_type,
        'company_name' => $request->company_name,
        'city' => $request->city,
        'is_active' => true,
    ];
 
        $user = User::create($usedata);
       

        $user->assignRole($request->roles);

        // Log user creation
        Log::info('User created', [
            'user_id' => $user->id,
            'created_by' => auth()->id(),
            'roles' => $request->roles,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully');
    }

    public function editUser(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'gender' => ['required', 'in:male,female,other'],
            'mobile' => ['required', 'string', 'regex:/^(05\d{8}|5\d{8,9})$/', Rule::unique('users')->ignore($user->id)],
            'roles' => ['required', 'array'],
            'roles.*' => [
                Rule::exists('roles', 'name'),
                function($attribute, $value, $fail) use ($request) {
                    if (in_array('customer', $request->roles) && count($request->roles) > 1) {
                        $fail('Customer role cannot be combined with other roles.');
                    }
                }
            ],
            'company_name' => ['required', 'string', 'max:255'],
            'company_type' => ['required', 'in:private,semi Govt.,Govt'],
            'city' => ['required', 'string', 'max:100'],
        ]);

        $oldRoles = $user->roles->pluck('name')->toArray();

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'gender' => $request->gender,
            'mobile' => $request->mobile,
            'company_type' => $request->company_type,
            'company_name' => $request->company_name,
            'city' => $request->city,
            'is_active' => $request->has('is_active'),
        ]);

        // Update roles
        $user->syncRoles($request->roles);

        // Log user update
        Log::info('User updated', [
            'user_id' => $user->id,
            'updated_by' => auth()->id(),
            'old_roles' => $oldRoles,
            'new_roles' => $request->roles,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        // Check if user has admin role
        if ($user->hasRole('admin')) {
            return redirect()->back()
                ->with('error', 'Admin users cannot be deleted.');
        }

        // Check if user has customer role
        if ($user->hasRole('customer')) {
            return redirect()->back()
                ->with('error', 'Customer users cannot be deleted.');
        }

        // Log user deletion
        Log::info('User deleted', [
            'user_id' => $user->id,
            'deleted_by' => auth()->id(),
            'roles' => $user->roles->pluck('name')->toArray(),
        ]);

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully');
    }

    public function restoreUser(User $user)
    {
        $user->restore();
        return redirect()->route('admin.users.index')
            ->with('success', 'User restored successfully.');
    }

    public function forceDeleteUser(User $user)
    {
        $user->forceDelete();
        return redirect()->route('admin.users.index')
            ->with('success', 'User permanently deleted.');
    }
}

// Middleware for BPM access
class CheckBPMAccess
{
    public function handle($request, $next)
    {
        if (!auth()->user()->hasRole('bpm-manager')) {
            abort(403, 'Unauthorized action.');
        }
        return $next($request);
    }
}

// Implement a state machine for approval status transitions
class ApprovalStateMachine
{
    protected $transitions = [
        'pending' => ['approve' => 'in_progress', 'reject' => 'rejected'],
        'in_progress' => ['approve' => 'in_progress', 'reject' => 'rejected'],
        'approved' => [],
        'rejected' => []
    ];

    public function transition($currentState, $action)
    {
        return $this->transitions[$currentState][$action] ?? $currentState;
    }
} 