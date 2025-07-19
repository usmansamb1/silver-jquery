<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Jenssegers\Agent\Agent;
use App\Helpers\LogHelper;

class AuthController extends Controller
{
    public function index(Request $request)
    {

         /*$response = Http::withHeaders([
            'X-Authorization' => '$2y$10$RD0j9wMqYWlKwjMQU2ZdY.ILtnSv8Z0Puc34lhXWSGKpOJc9cj3bq',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post('https://api.authentica.sa/api/v1/send-otp', [
            "phone"=>"+966566925152",
    "method"=>"whatsapp",
    "number_of_digits"=> 4,
    "otp_format"=> "numeric",
    "otp"=> "6674",
    "is_fallback_on"=> 0
        ]);

        if ($response->successful()) {
           $result = $response->json();
            dd($result['success']);
        } else {
            return response()->json(['message' => 'Failed',"success"=>false], $response->status());
        }*/



       // $request->session()->regenerate();
       // dd(session()->all());
       return view('auth.index');
    }

    public function verifyOtp(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'mobile' => ['required', 'regex:/^(05\d{8}|5\d{8,9})$/'],
            'otp'    => 'required',
        ]);

        if ($validator->fails()){
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator);
        }

 

        $user = User::where('mobile', $request->mobile)->first();
        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'User not found'], 404);
            }
            return redirect()->back()->with('error', 'User not found');
        }
        
 
        if ($user->otp == $request->otp) {
            // Clear OTP fields
            $user->update([
                'otp' => null,
                'otp_created_at' => null,
            ]);


            // Authenticate the user
            \Illuminate\Support\Facades\Auth::login($user);
            $request->session()->regenerate();

            $agent = new Agent();
            $agent->setUserAgent($request->userAgent()); // Or let it auto-detect

            // Log OTP verification
            LogHelper::logOtpVerification($user, 'User verified OTP successfully', [
                'mobile' => $user->mobile,
                'device_type' => $agent->isMobile() ? 'mobile' : 'desktop',
                'browser' => $agent->browser()
            ]);
            
            // Log successful login
            LogHelper::logLogin($user, 'User logged in via web interface', [
                'mobile' => $user->mobile,
                'device_type' => $agent->isMobile() ? 'mobile' : 'desktop',
                'browser' => $agent->browser()
            ]);

            if ($agent->isMobile()) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'OTP verified successfully, user authenticated.']);
                }

            }else{
                Log::info('Detected Desktop Browser');
                // Could still be Axios from a desktop browser
                return redirect()->route('home');
            }


            //return redirect()->route('home');
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Invalid OTP'], 401);
        }
        return redirect()->back()->with('error', 'Invalid OTP');
    }

}
