@extends('emails.layouts.app')

@section('title', 'Wallet Top-up Rejected')

@section('content')
    <h2>Wallet Top-up Rejected</h2>
    
    <p>Dear {{ $name }},</p>

    <p>Unfortunately, your wallet top-up request has been rejected.</p>

    <div style="margin: 20px 0;">
        <p><strong>Request Details:</strong></p>
        <ul>
            <li>Amount: <span class="amount">SAR {{ number_format($amount, 2) }}</span></li>
            @if(isset($reference))
            <li>Reference: <div class="reference">{{ $reference }}</div></li>
            @endif
            <li>Date: {{ now()->format('Y-m-d H:i:s') }}</li>
        </ul>
    </div>

    <div style="margin: 20px 0; padding: 15px; background-color: #fef2f2; border-radius: 5px; color: #991b1b;">
        <p style="margin: 0;">
            <strong>Reason for Rejection:</strong><br>
            {{ $reason }}
        </p>
    </div>

    <p>You can submit a new top-up request after addressing the reason for rejection.</p>

    <a href="{{ route('wallet.topup') }}" class="button" style="background-color: #4f46e5;">
        Submit New Request
    </a>

    <div style="margin-top: 20px; padding: 15px; background-color: #f8fafc; border-radius: 5px;">
        <p style="margin: 0; color: #64748b; font-size: 14px;">
            If you believe this rejection was made in error or need assistance, please contact our support team.
        </p>
    </div>
@endsection 