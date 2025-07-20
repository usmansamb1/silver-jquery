 
@extends('layouts.app')
<style>.card-title, .card-text {
    color: #3f3c3c !important;
}</style>
@section('content')
<style> .card-title, .card-text {
    color: #3f3c3c !important;
} </style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('admin-payments.payment_management') }}</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('admin-payments.payment_id') }}</th>
                                    <th>{{ __('admin-payments.customer_details') }}</th>
                                    <th>{{ __('admin-payments.amount') }}</th>
                                    <th>{{ __('admin-payments.payment_type') }}</th>
                                    <th>{{ __('admin-payments.status') }}</th>
                                    <th>{{ __('admin-payments.created_at') }}</th>
                                    <th>{{ __('admin-payments.payment_actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)
                                    <tr>
                                        <td>{{ $payment->id }}</td>
                                        <td>{{ $payment->user->name }}</td>
                                        <td>{{ number_format($payment->amount, 2) }}</td>
                                        <td>{{ __('admin-payments.payment_types.' . $payment->payment_type, ['default' => ucfirst(str_replace('_', ' ', $payment->payment_type))]) }}</td>
                                        <td>
                                            <span class="badge badge-{{ $payment->status === 'approved' ? 'success' : ($payment->status === 'rejected' ? 'danger' : 'warning') }}">
                                                {{ __('admin-payments.statuses.' . $payment->status, ['default' => ucfirst($payment->status)]) }}
                                            </span>
                                        </td>
                                        <td>{{ $payment->created_at->format('Y-m-d H:i:s') }}</td>
                                        <td>
                                            <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i> {{ __('admin-payments.actions.view_details') }}
                                            </a>
                                            
                                            @if($payment->status === 'pending')
                                                <form action="{{ route('admin.payments.approve', $payment) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('{{ __('admin-payments.confirmations.approve_payment') }}')">
                                                        <i class="fas fa-check"></i> {{ __('admin-payments.approve_payment') }}
                                                    </button>
                                                </form>

                                                <form action="{{ route('admin.payments.reject', $payment) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('admin-payments.confirmations.reject_payment') }}')">
                                                        <i class="fas fa-times"></i> {{ __('admin-payments.reject_payment') }}
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">{{ __('admin-dashboard.tables.no_data') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $payments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 