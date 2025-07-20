<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;

class EmailTestController extends Controller
{
    /**
     * Display form and send test email on POST.
     */
    public function test(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'email' => 'required|email'
            ]);

            try {
                Mail::to($request->email)
                    ->send(new TestMail());

                return back()->with('success', 'Test email sent to ' . $request->email);
            } catch (\Exception $e) {
                return back()->with('error', 'Failed to send email: ' . $e->getMessage());
            }
        }

        return view('admin.test-email');
    }
} 