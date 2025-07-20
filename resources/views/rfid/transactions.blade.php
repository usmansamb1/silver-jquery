@extends('layouts.app')

@section('title', __('RFID Transaction History'))

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="mb-0">{{ __('RFID Transaction History') }}</h3>
            <a href="{{ route('rfid.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> {{ __('Back to RFID Management') }}
            </a>
        </div>
        <div class="card-body">
            @if($transactions->isEmpty())
                <div class="alert alert-info">
                    {{ __('No RFID transactions found. Recharge your RFID to see transaction history.') }}
                </div>
                <a href="{{ route('rfid.recharge') }}" class="btn btn-primary">
                    <i class="fa fa-credit-card"></i> {{ __('Recharge RFID') }}
                </a>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Vehicle') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Payment Method') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Reference') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->created_at->format('M d, Y h:i A') }}</td>
                                    <td>
                                        @if($transaction->vehicle)
                                            {{ $transaction->vehicle->manufacturer }} {{ $transaction->vehicle->make }} {{ $transaction->vehicle->model }}
                                            <br>
                                            <small class="text-muted">{{ $transaction->vehicle->plate_number }}</small>
                                        @else
                                            <span class="text-muted">{{ __('Vehicle not found') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $transaction->formatted_amount }}</td>
                                    <td>
                                        @if($transaction->payment_method === 'wallet')
                                            <span class="badge bg-primary">Wallet</span>
                                        @elseif($transaction->payment_method === 'credit_card')
                                            <span class="badge bg-success">Credit Card</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($transaction->payment_method) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction->status === 'completed' && $transaction->payment_status === 'paid')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($transaction->status === 'pending' || $transaction->payment_status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($transaction->status === 'failed' || $transaction->payment_status === 'failed')
                                            <span class="badge bg-danger">Failed</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($transaction->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ Str::limit($transaction->transaction_reference, 15) }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 