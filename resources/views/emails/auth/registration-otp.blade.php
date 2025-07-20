@extends('emails.layouts.app')

@section('title', 'Registration OTP')

@section('content')
    <h2>Registration OTP</h2>
    
    <p>Dear {{ $name }},</p>

    <p>Thank you for registering with {{ config('app.name') }}. Please use the following OTP to complete your {{ $registration_type }} registration:</p>

    <div style="text-align: center; margin: 30px 0;">
        <div class="reference" style="font-size: 32px; letter-spacing: 5px;">
            {{ $otp }}
        </div>
    </div>

    <p>This OTP will expire in 5 minutes.</p>

    <div style="margin-top: 20px; padding: 15px; background-color: #f8fafc; border-radius: 5px;">
        <p style="margin: 0; color: #64748b; font-size: 14px;">
            <strong>Security Notice:</strong> If you didn't request this OTP, please ignore this email.
        </p>
    </div>
@endsection 