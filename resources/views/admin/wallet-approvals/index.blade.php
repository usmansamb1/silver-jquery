@extends('layouts.admin')

@section('title', __('admin-approvals.titles.wallet_approvals'))

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-0">
                                <i class="fas fa-wallet me-2"></i>
                                {{ __('admin-approvals.titles.pending_approvals') }}
                            </h5>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-primary">
                                {{ $pendingApprovals->total() }} {{ __('admin-approvals.status.pending') }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    @if($pendingApprovals->isEmpty())
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5>{{ __('admin-approvals.messages.no_pending_approvals') }}</h5>
                            <p class="text-muted">{{ __('admin-approvals.messages.no_pending_approvals') }}</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>{{ __('admin-approvals.fields.request_id') }}</th>
                                        <th>{{ __('admin-approvals.fields.requester') }}</th>
                                        <th>{{ __('admin-approvals.fields.amount') }}</th>
                                        <th>{{ __('admin-approvals.wallet_approval.payment_method') }}</th>
                                        <th>{{ __('admin-approvals.fields.request_date') }}</th>
                                        <th>{{ __('admin-approvals.status.pending') }}</th>
                                        <th>{{ __('admin-approvals.actions.take_action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingApprovals as $approval)
                                    <tr>
                                        <td>
                                            <span class="text-primary">{{ $approval->reference_no ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm bg-primary rounded-circle me-2">
                                                    {{ strtoupper(substr($approval->payment->user->name, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $approval->payment->user->name }}</div>
                                                    <small class="text-muted">{{ $approval->payment->user->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold">SAR {{ number_format($approval->payment->amount, 2) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ ucfirst(str_replace('_', ' ', $approval->payment->payment_type)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div data-bs-toggle="tooltip" title="{{ $approval->created_at->format('d M Y, h:i A') }}">
                                                {{ $approval->created_at->diffForHumans() }}
                                            </div>
                                        </td>
                                        <td>
                                            @if($approval->currentStep->step_order == 1)
                                                <span class="badge bg-primary">{{ __('admin-approvals.steps.step_name') }} 1</span>
                                            @else
                                                <span class="badge bg-info">{{ __('admin-approvals.steps.step_name') }} {{ $approval->currentStep->step_order }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.wallet-requests.show', $approval->id) }}" 
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye me-1"></i> {{ __('admin-approvals.actions.view_details') }}
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            {{ $pendingApprovals->links() }}
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
.avatar {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.875rem;
}

.table > :not(caption) > * > * {
    padding: 1rem 0.75rem;
}

.badge {
    font-weight: 500;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush 