<?php 
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')
            ->whereDoesntHave('roles', function($query) {
                $query->where('name', 'customer');
            })
            ->latest()
            ->paginate(10);
            
        return view('admin.users.index', compact('users'));
    }

    public function list()
    {
        $users = User::with('roles')->get();
        
        return DataTables::of($users)
            ->addColumn('roles', function ($user) {
                return $user->roles->pluck('name')->implode(', ');
            })
            ->rawColumns(['roles', 'is_active'])
            ->make(true);
    }

    public function listDeleted()
    {
        $users = User::onlyTrashed()->with('roles')->get();
        
        return DataTables::of($users)
            ->addColumn('roles', function ($user) {
                return $user->roles->pluck('name')->implode(', ');
            })
            ->rawColumns(['roles'])
            ->make(true);
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.form', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone' => 'nullable|string|max:20',
            'gender' => 'required|in:male,female',
            'registration_type' => 'required|in:personal,company',
            'company_type' => 'required_if:registration_type,company|string|max:255',
            'company_name' => 'required_if:registration_type,company|string|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,name']
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'gender' => $validated['gender'],
            'registration_type' => $validated['registration_type'],
            'company_type' => $validated['company_type'],
            'company_name' => $validated['company_name'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'country' => $validated['country'],
            'is_active' => true
        ]);

        $user->assignRole($validated['roles']);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        if ($user->hasRole('customer')) {
            abort(403, 'Cannot edit customer accounts through admin panel.');
        }

        $roles = Role::all();
        return view('admin.users.form', compact('user', 'roles'));
    }

    /**
     * Display the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function show(User $user)
    {
        $roles = $user->roles->pluck('name');
        $wallet = $user->wallet;
        
        // Get recent transactions if any
        $transactions = [];
        if ($wallet) {
            $transactions = $wallet->transactions()
                ->limit(5)
                ->get();
        }
        
        return view('admin.users.show', compact('user', 'roles', 'wallet', 'transactions'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->hasRole('customer')) {
            abort(403, 'Cannot edit customer accounts through admin panel.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => 'nullable|string|max:20',
            'gender' => 'required|in:male,female',
            'registration_type' => 'required|in:individual,company',
            'company_type' => 'required_if:registration_type,company|string|max:255',
            'company_name' => 'required_if:registration_type,company|string|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,name']
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'gender' => $validated['gender'],
            'registration_type' => $validated['registration_type'],
            'company_type' => $validated['company_type'],
            'company_name' => $validated['company_name'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'country' => $validated['country'],
            'is_active' => true
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        $user->syncRoles($validated['roles']);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->hasRole('customer')) {
            abort(403, 'Cannot delete customer accounts through admin panel.');
        }

        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return response()->json([
            'message' => 'User restored successfully'
        ]);
    }
} 