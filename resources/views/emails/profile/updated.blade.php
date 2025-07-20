@extends('emails.layouts.app')

@section('title', 'Profile Updated')

@section('content')
    <h2>Profile Updated</h2>
    
    <p>Dear {{ $name }},</p>

    <p>Your profile has been updated successfully.</p>

    <div style="margin: 20px 0; padding: 15px; background-color: #f8fafc; border-radius: 5px;">
        <p style="margin: 0;">
            <strong>Update Time:</strong><br>
            {{ $time }}
        </p>
    </div>

    <div style="margin-top: 20px; padding: 15px; background-color: #f8fafc; border-radius: 5px;">
        <p style="margin: 0; color: #64748b; font-size: 14px;">
            <strong>Security Notice:</strong> If you did not make these changes, please contact our support team immediately.
        </p>
    </div>

    <a href="{{ route('profile.show') }}" class="button">
        View Profile
    </a>
@endsection 