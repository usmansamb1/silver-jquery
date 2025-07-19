<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;

class HomeController extends Controller
{
    // Constructor to apply authentication middleware.
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Show the user home page with account information.
    public function index()
    {
      // lets authenticate

        $user = Auth::user();
        $wallet = $user->wallet;
        $currentBalance = $wallet ? $wallet->balance : 0;
        return view('home.index', compact('user','currentBalance'));
    }

    // Show the user home page with account information.
    public function testu()
    {

        dd(session()->all());
    }



    // Logout the user.
    public function logout(Request $request)
    {
        // Log the logout action
        $user = Auth::user();
        LogHelper::logLogout($user, 'User logged out manually', [
            'logout_method' => 'manual'
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/auth'); // Redirect to login/registration page
    }
}
