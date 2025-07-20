@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Pending Payments</h4>
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
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>User</th>
                                    <th>Payment Type</th>
                                    <th>Amount</th>
                                    <th>Notes</th>
                                    <th>Documents</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingPayments as $payment)
                                <tr>
                                    <td>{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        {{ $payment->user->name ?? $payment->user->company_name }}
                                        <br>
                                        <small class="text-muted">{{ $payment->user->email }}</small>
                                    </td>
                                    <td>
                                        @if ($payment->payment_type === 'credit_card' && $payment->card_brand)
                                            <span class="badge bg-info">
                                                {{ $payment->card_brand }} Card
                                                @if ($payment->card_brand === 'MADA')
                                                    (مدى)
                                                @endif
                                            </span>
                                        @else
                                            <span class="badge bg-info">{{ str_replace('_', ' ', ucfirst($payment->payment_type)) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($payment->amount, 2) }} SAR</td>
                                    <td>{{ $payment->notes }}</td>
                                    <td>
                                        @if($payment->file)
                                            <a href="{{ Storage::url($payment->file) }}" target="_blank" class="btn btn-sm btn-info">
                                                <i class="fas fa-file"></i> View Document
                                            </a>
                                        @else
                                            <span class="text-muted">No document</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <form action="{{ route('wallet.approve-payment', $payment) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm me-2" onclick="return confirm('Are you sure you want to approve this payment?')">
                                                    <i class="fas fa-check"></i> Approve
                                                </button>
                                            </form>

                                            <form action="{{ route('wallet.reject-payment', $payment) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to reject this payment?')">
                                                    <i class="fas fa-times"></i> Reject
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No pending payments found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-4">
                        {{ $pendingPayments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 