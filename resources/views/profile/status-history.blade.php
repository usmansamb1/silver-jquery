@extends('layouts.app')

@section('title', 'Status History')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-0 rounded-4 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="fas fa-history text-primary me-2"></i> Account Status History</h5>
                    <a href="{{ route('profile.show') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Profile
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 mb-3">Current Information</h6>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item px-0 d-flex border-0">
                                    <span class="text-muted me-2 w-25">Account ID:</span>
                                    <span class="fw-medium">{{ $user->id }}</span>
                                </li>
                                <li class="list-group-item px-0 d-flex border-0">
                                    <span class="text-muted me-2 w-25">Name:</span>
                                    <span class="fw-medium">{{ $user->name ?? $user->company_name }}</span>
                                </li>
                                <li class="list-group-item px-0 d-flex border-0">
                                    <span class="text-muted me-2 w-25">Current Status:</span>
                                    <span class="fw-medium">
                                        {!! $statusBadge !!}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <h6 class="border-bottom pb-2 mb-3">Status Change History</h6>
                    
                    @if($statusHistories->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Changed From</th>
                                        <th>Changed To</th>
                                        <th>Changed By</th>
                                        <th>Comment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($statusHistories as $history)
                                        <tr>
                                            <td>{{ $history->created_at->format('Y-m-d H:i:s') }}</td>
                                            <td>
                                                @if($history->previous_status)
                                                    <span class="badge {{ getStatusBadgeClass($history->previous_status) }}">
                                                        {{ ucfirst($history->previous_status) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ getStatusBadgeClass($history->new_status) }}">
                                                    {{ ucfirst($history->new_status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($history->user)
                                                    {{ $history->user->name ?? $history->user->company_name }}
                                                @else
                                                    <span class="text-muted">System</span>
                                                @endif
                                            </td>
                                            <td>{{ $history->comment ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $statusHistories->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> No status change history found.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@php
function getStatusBadgeClass($status) {
    $classes = [
        'active' => 'bg-success',
        'inactive' => 'bg-secondary',
        'suspended' => 'bg-warning',
        'pending_verification' => 'bg-info',
        'blocked' => 'bg-danger'
    ];
    
    return $classes[$status] ?? 'bg-secondary';
}
@endphp 