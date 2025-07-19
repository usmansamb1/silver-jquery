@extends('emails.layouts.app')

@section('title', 'Approval Action Required')

@section('content')
    <h2>Approval Action Required</h2>
    
    <p>Dear {{ $approver }},</p>

    <p>A new approval action is required for a wallet top-up request.</p>

    <div style="margin: 20px 0;">
        <p><strong>Details:</strong></p>
        <ul>
            <li>Amount: <span class="amount">SAR {{ number_format($amount, 2) }}</span></li>
            @if(isset($reference))
            <li>Reference: <div class="reference">{{ $reference }}</div></li>
            @endif
            <li>Step: {{ ucfirst($step) }}</li>
        </ul>
    </div>

    <p>Please review and take action on this request as soon as possible.</p>

    <a href="{{ route('wallet.approvals.show', ['request' => $reference]) }}" class="button">
        Review Request
    </a>

    <p style="margin-top: 20px;">
        <small>If you're unable to click the button above, please copy and paste this URL into your browser:<br>
        {{ route('wallet.approvals.show', ['request' => $reference]) }}</small>
    </p>
@endsection 