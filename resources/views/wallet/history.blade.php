@extends('layouts.app')

@push('styles')
<style>
    .wallet-card {
        background: linear-gradient(135deg, #0061f2 0%, #6900f2 100%);
        color: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(33, 40, 50, 0.15);
    }
    .wallet-card .balance-title {
        font-size: 1rem;
        opacity: 0.8;
        margin-bottom: 0.5rem;
    }
    .wallet-card .balance-amount {
        font-size: 2.5rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }
    .wallet-card .action-button {
        background: rgba(255, 255, 255, 0.15);
        border: none;
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 50px;
        transition: all 0.3s ease;
    }
    .wallet-card .action-button:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-2px);
    }
    .stats-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        height: 100%;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(33, 40, 50, 0.15);
        transition: transform 0.3s ease;
    }
    .stats-card:hover {
        transform: translateY(-5px);
    }
    .stats-card .stats-icon {
        width: 48px;
        height: 48px;
        background: rgba(0, 97, 242, 0.1);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
    }
    .stats-card .stats-icon i {
        font-size: 1.5rem;
        color: #0061f2;
    }
    .card-brand-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-weight: 600;
    }
    .card-brand-visa {
        background-color: #1a1f71;
        color: white;
    }
    .card-brand-mastercard {
        background-color: #eb001b;
        color: white;
    }
    .card-brand-mada {
        background-color: #00a651;
        color: white;
    }
    .stats-card .stats-title {
        font-size: 0.875rem;
        color: #69707a;
        margin-bottom: 0.25rem;
    }
    .stats-card .stats-amount {
        font-size: 1.25rem;
        font-weight: 600;
        color: #363d47;
    }
    .transaction-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.875rem;
        letter-spacing: 0.05em;
    }
    .status-badge {
        padding: 0.5em 1em;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }
    .status-approved {
        background-color: #d4edda;
        color: #155724;
    }
    .status-rejected {
        background-color: #f8d7da;
        color: #721c24;
    }
    .smallnotes {
        font-size: 12px;
        max-width: 240px;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <!-- Wallet Card -->
            <div class="wallet-card mb-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="balance-title">{{ __('Available Balance') }}</div>
                        <div class="balance-amount">{{ number_format($wallet->balance, 2) }} <span class="icon-saudi_riyal"></span></div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="{{ route('wallet.topup') }}" class="action-button">
                            <i class="fas fa-plus-circle me-2"></i>{{ __('Top Up Wallet') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-4 mb-4">
                    <div class="stats-card">
                        <div class="stats-icon">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                        <div class="stats-title">{{ __('Total Top-ups') }}</div>
                        <div class="stats-amount">
                            {{ number_format($payments->where('status', 'approved')->sum('amount'), 2) }} <span class="icon-saudi_riyal"></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="stats-card">
                        <div class="stats-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stats-title">{{ __('Pending Transactions') }}</div>
                        <div class="stats-amount">
                            {{ $payments->where('status', 'pending')->count() }}
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="stats-card">
                        <div class="stats-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stats-title">{{ __('Completed Transactions') }}</div>
                        <div class="stats-amount">
                            {{ $payments->where('status', 'approved')->count() }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="card">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('Transaction History') }}</h5>
                        <div class="btn-group">
                            <a href="{{ route('wallet.history') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-credit-card me-1"></i> {{ __('Payment History') }}
                            </a>
                            <a href="{{ route('wallet.transactions') }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-exchange-alt me-1"></i> {{ __('Wallet Transactions') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 transaction-table">
                            <thead>
                                <tr>
                                    <th class="px-4">{{ __('Date') }}</th>
                                    <th>{{ __('Transaction Type') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Notes') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)
                                <tr>
                                    <td class="px-4">
                                        <div class="fw-bold">{{ $payment->created_at->format('d M Y') }}</div>
                                        <small class="text-muted">{{ $payment->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($payment->payment_type == 'credit_card')
                                                <i class="fas fa-credit-card text-primary me-2"></i>
                                            @elseif($payment->payment_type == 'bank_transfer')
                                                <i class="fas fa-university text-info me-2"></i>
                                            @elseif($payment->payment_type == 'bank_guarantee')
                                                <i class="fas fa-file-contract text-success me-2"></i>
                                            @elseif($payment->payment_type == 'bank_lc')
                                                <i class="fas fa-file-contract text-success me-2"></i>
                                            @else
                                                <i class="fas fa-money-check text-warning me-2"></i>
                                            @endif
                                            @if ($payment->payment_type === 'bank_transfer')
                                                {{ __('Bank Transfer') }}
                                            @elseif ($payment->payment_type === 'bank_guarantee')
                                                {{ __('Bank Guarantee') }}
                                            @elseif ($payment->payment_type === 'bank_lc')
                                                {{ __('Bank LC') }}
                                            @elseif ($payment->payment_type === 'credit_card')
                                                @if ($payment->card_brand)
                                                    <span class="card-brand-badge 
                                                        @if($payment->card_brand === 'VISA') card-brand-visa 
                                                        @elseif($payment->card_brand === 'MASTERCARD') card-brand-mastercard
                                                        @elseif($payment->card_brand === 'MADA') card-brand-mada
                                                        @endif">
                                                        {{ $payment->card_brand }}
                                                        @if ($payment->card_brand === 'MADA')
                                                            (مدى)
                                                        @endif
                                                    </span>
                                                @else
                                                    {{ __('Credit Card') }}
                                                @endif
                                            @else
                                                {{ str_replace('_', ' ', ucfirst($payment->payment_type)) }}
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ number_format($payment->amount, 2) }} <span class="icon-saudi_riyal"></span></div>
                                    </td>
                                    <td>
                                        @if($payment->status == 'pending')
                                            <span class="status-badge status-pending">
                                                <i class="fas fa-clock me-1"></i>{{ __('Pending') }}
                                            </span>
                                        @elseif($payment->status == 'approved')
                                            <span class="status-badge status-approved">
                                                <i class="fas fa-check me-1"></i>{{ __('Approved') }}
                                            </span>
                                        @else
                                            <span class="status-badge status-rejected">
                                                <i class="fas fa-times me-1"></i>{{ __('Rejected') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-muted smallnotes " >
                                        <span class="text-muted " >{{ $payment->notes ?: __('No notes') }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $approvalRequest = \App\Models\WalletApprovalRequest::where('payment_id', $payment->id)->first();
                                        @endphp
                                        
                                        @if($approvalRequest && in_array($payment->payment_type, ['bank_transfer', 'bank_guarantee', 'bank_lc']))
                                            <a href="{{ route('wallet.approval.details', $approvalRequest) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i>{{ __('View Details') }}
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p>{{ __('No transactions found') }}</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Wallet Transactions Table -->
            {{-- <div class="card mt-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">{{ __('Wallet Transactions') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 transaction-table">
                            <thead>
                                <tr>
                                    <th class="px-4">{{ __('Date') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Description') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                <tr>
                                    <td class="px-4">
                                        <div class="fw-bold">{{ $transaction->created_at->format('d M Y') }}</div>
                                        <small class="text-muted">{{ $transaction->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        {{ \App\Models\WalletTransaction::TRANSACTION_TYPES[$transaction->type] ?? ucfirst($transaction->type) }}
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $transaction->formatted_amount }} <span class="icon-saudi_riyal"></span></div>
                                    </td>
                                    <td>
                                        <span class="status-badge status-{{ $transaction->status }}">
                                            @if($transaction->status === 'completed')
                                                <i class="fas fa-check me-1"></i>{{ __('Completed') }}
                                            @elseif($transaction->status === 'pending')
                                                <i class="fas fa-clock me-1"></i>{{ __('Pending') }}
                                            @elseif($transaction->status === 'failed')
                                                <i class="fas fa-times me-1"></i>{{ __('Failed') }}
                                            @elseif($transaction->status === 'reversed')
                                                <i class="fas fa-undo me-1"></i>{{ __('Reversed') }}
                                            @else
                                                {{ ucfirst($transaction->status) }}
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $transaction->description ?: '—' }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p>{{ __('No wallet transactions found') }}</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $transactions->links() }}
                </div>
            </div> --}}

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $payments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 