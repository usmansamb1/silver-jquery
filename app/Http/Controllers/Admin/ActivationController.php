<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ActivationController extends Controller
{
    /**
     * Display a listing of users pending activation.
     */
    public function index()
    {
        $users = User::where('is_active', false)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('admin.activation.index', compact('users'));
    }

    /**
     * Display the specified user for activation review.
     */
    public function show(User $user)
    {
        return view('admin.activation.show', compact('user'));
    }

    /**
     * Approve a user's activation.
     */
    public function approve(Request $request, User $user)
    {
        $user->is_active = true;
        $user->save();
        
        return redirect()->route('activation.index')
            ->with('success', 'User has been activated successfully');
    }

    /**
     * Reject a user's activation.
     */
    public function reject(Request $request, User $user)
    {
        // Optional: Add rejection reason
        $user->rejection_reason = $request->reason;
        $user->save();
        
        return redirect()->route('activation.index')
            ->with('success', 'User activation has been rejected');
    }
} 