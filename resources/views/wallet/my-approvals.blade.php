@extends('layouts.app')

@section('title', __('My Pending Approvals'))

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">{{ __('My Pending Approvals') }}</h2>
            
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">{{ __('Wallet Top-up Requests Awaiting Your Approval') }}</h5>
                </div>
                <div class="card-body p-0">
                    @if($pendingApprovals->isEmpty())
                        <div class="alert alert-info m-4">
                            <i class="fas fa-info-circle me-2"></i> {{ __("You dont have any pending approvals at this time") }}
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('Reference #') }}</th>
                                        <th>{{ __('Customer') }}</th>
                                        <th>{{ __('Amount') }}</th>
                                        <th>{{ __('Payment Method') }}</th>
                                        <th>{{ __('Date Submitted') }}</th>
                                        <th>{{ __('Your Role') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingApprovals as $step)
                                    <tr>
                                        <td>{{ $step->request->reference_no ?? 'N/A' }}</td>
                                        <td>{{ $step->request->payment->user->getFormattedCustomerNoAttribute() }}</td>
                                        <td>SAR {{ number_format($step->request->payment->amount, 2) }}</td>
                                        <td>
                                            @if($step->request->payment->payment_type == 'bank_transfer')
                                                <span><i class="fas fa-university text-info me-1"></i> {{ __('Bank Transfer') }}</span>
                                            @elseif($step->request->payment->payment_type == 'bank_guarantee')
                                                <span><i class="fas fa-file-contract text-success me-1"></i> {{ __('Bank Guarantee') }}</span>
                                            @elseif($step->request->payment->payment_type == 'bank_lc')
                                                <span><i class="fas fa-file-contract text-success me-1"></i> {{ __('Bank LC') }}</span>
                                            @else
                                                <span><i class="fas fa-money-check text-warning me-1"></i> {{ ucfirst(str_replace('_', ' ', $step->request->payment->payment_type)) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $step->request->created_at->format('d M Y, h:i A') }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ ucfirst($step->role) }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('wallet.approvals.show', $step->request) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye me-1"></i> {{ __('View Details') }}
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .badge {
        font-size: 0.8rem;
        font-weight: 500;
    }
    
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
    }
</style>
@endpush 