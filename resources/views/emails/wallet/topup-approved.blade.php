@extends('emails.layouts.app')

@section('title', 'Wallet Top-up Approved')

@section('content')
    <h2>Wallet Top-up Approved</h2>
    
    <p>Dear {{ $name }},</p>

    <p>Your wallet top-up request has been approved and processed successfully.</p>

    <div style="margin: 20px 0;">
        <p><strong>Transaction Details:</strong></p>
        <ul>
            <li>Amount: <span class="amount">SAR {{ number_format($amount, 2) }}</span></li>
            @if(isset($reference))
            <li>Reference: <div class="reference">{{ $reference }}</div></li>
            @endif
            <li>Date: {{ now()->format('Y-m-d H:i:s') }}</li>
        </ul>
    </div>

    <a href="{{ route('wallet.transactions') }}" class="button">
        View Transaction History
    </a>

    <div style="margin-top: 20px; padding: 15px; background-color: #f8fafc; border-radius: 5px;">
        <p style="margin: 0; color: #64748b; font-size: 14px;">
            Your wallet has been credited with the above amount. You can now use these funds for services and transactions.
        </p>
    </div>
@endsection 