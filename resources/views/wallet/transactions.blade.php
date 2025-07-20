@extends('layouts.app')

@section('title', __('Wallet Transactions'))

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-0">{{ __('Wallet Transactions') }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('wallet.index') }}">{{ __('Wallet') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Transactions') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-primary-subtle p-3 me-3">
                            <i class="bi bi-wallet2 fs-2 text-primary"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ __('Wallet Balance') }}</h6>
                            <h3 class="mb-0 fw-bold">{!! $wallet->formatted_balance !!}</h3>
                        </div>
                        <div class="ms-auto">
                            <a href="{{ route('wallet.topup') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>
                                {{ __('Top Up') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('Transaction History') }}</h5>
                        <div class="btn-group">
                            <a href="{{ route('wallet.history') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-credit-card me-1"></i> {{ __('Payment History') }}
                            </a>
                            <a href="{{ route('wallet.transactions') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-exchange-alt me-1"></i> {{ __('Transactions') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($transactions->isEmpty())
                        <div class="text-center py-5">
                            <img src="{{ asset('images/empty-wallet.svg') }}" alt="{{ __('No transactions') }}" width="120" class="mb-3">
                            <h5>{{ __('No transactions yet') }}</h5>
                            <p class="text-muted">{{ __('Your transaction history will appear here once you start using your wallet.') }}</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Type') }}</th>
                                        <th>{{ __('Description') }}</th>
                                        <th class="text-end">{{ __('Amount') }}</th>
                                        <th class="text-end">{{ __('Balance') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td>
                                                <div>{{ $transaction->created_at->format('d M Y') }}</div>
                                                <small class="text-muted">{{ $transaction->created_at->format('h:i A') }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $transaction->status_color }}">
                                                    {{ ucfirst($transaction->type) }}
                                                </span>
                                            </td>
                                            <td>{{ $transaction->description ?? __('N/A') }}</td>
                                            <td class="text-end">
                                                <span class="{{ in_array($transaction->type, ['deposit', 'refund']) ? 'text-success' : 'text-danger' }}">
                                                    {{ $transaction->formatted_amount }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                {{ number_format($transaction->balance_after, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                @if($transactions->hasPages())
                    <div class="card-footer bg-white">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 