@extends('emails.layouts.app')

@section('title', 'Wallet Approval Required')

@section('content')
    <p>Dear {{ $approver }},</p>

    <p>You have a new wallet top-up request awaiting your approval:</p>

    <table style="width:100%; border-collapse: collapse;">
        <tr>
            <td style="padding:8px; border:1px solid #e2e8f0;"><strong>Amount:</strong></td>
            <td style="padding:8px; border:1px solid #e2e8f0;">SAR {{ number_format($amount, 2) }}</td>
        </tr>
        <tr>
            <td style="padding:8px; border:1px solid #e2e8f0;"><strong>Reference No:</strong></td>
            <td style="padding:8px; border:1px solid #e2e8f0;">{{ $reference }}</td>
        </tr>
        <tr>
            <td style="padding:8px; border:1px solid #e2e8f0;"><strong>Payment Method:</strong></td>
            <td style="padding:8px; border:1px solid #e2e8f0;">{{ ucfirst($payment_method) }}</td>
        </tr>
    </table>

    <p>Please review and take action in the <a href="{{ url('/wallet/approvals') }}">Wallet Approvals</a> section of the dashboard.</p>

    <p>Thank you,<br>{{ config('app.name') }} Team</p>
@endsection 