@extends('emails.layouts.app')

@section('title', 'Login OTP')

@section('content')
    <h2>Login OTP</h2>
    
    <p>Dear {{ $name }},</p>

    <p>Please use the following OTP to complete your login:</p>

    <div style="text-align: center; margin: 30px 0;">
        <div class="reference" style="font-size: 32px; letter-spacing: 5px;">
            {{ $otp }}
        </div>
    </div>

    <p>This OTP will expire in 5 minutes.</p>

    <div style="margin-top: 20px; padding: 15px; background-color: #f8fafc; border-radius: 5px;">
        <p style="margin: 0; color: #64748b; font-size: 14px;">
            <strong>Security Notice:</strong> If you didn't attempt to login, please contact our support team immediately.
        </p>
    </div>

    <div style="margin-top: 20px; font-size: 14px; color: #64748b;">
        <p>Login attempt from:</p>
        <ul style="margin: 5px 0;">
            <li>Time: {{ now()->format('Y-m-d H:i:s') }}</li>
            <li>IP: {{ request()->ip() }}</li>
            <li>Browser: {{ request()->userAgent() }}</li>
        </ul>
    </div>
@endsection 