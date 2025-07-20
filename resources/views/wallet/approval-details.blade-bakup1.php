@extends('layouts.app')

@section('title', 'Wallet Top-up Approval Details')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">Wallet Top-up Approval Details</h2>
            
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('wallet.history') }}">Wallet History</a></li>
                    <li class="breadcrumb-item active">Approval Details</li>
                </ol>
            </nav>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="{{ route('wallet.history') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Back to History
                </a>
                
                <div>
                    <span class="badge bg-{{ $request->status === 'pending' ? 'warning' : ($request->status === 'completed' ? 'success' : 'danger') }} p-2">
                        {{ ucfirst($request->status) }}
                    </span>
                </div>
            </div>

            <!-- Payment Details Card -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Payment Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Reference #:</div>
                                <div class="col-md-8">{{ $request->reference_no ?? 'N/A' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Customer:</div>
                                <div class="col-md-8">{{ $request->payment->user->name }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Payment Method:</div>
                                <div class="col-md-8">{{ ucfirst(str_replace('_', ' ', $request->payment->payment_type)) }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Amount:</div>
                                <div class="col-md-8">SAR {{ number_format($request->payment->amount, 2) }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Date Submitted:</div>
                                <div class="col-md-8">{{ $request->created_at->format('d M Y, h:i A') }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Current Status:</div>
                                <div class="col-md-8">
                                    <span class="badge bg-{{ $request->status === 'pending' ? 'warning' : ($request->status === 'completed' ? 'success' : 'danger') }}">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($request->payment->notes)
                    <div class="row mt-3">
                        <div class="col-md-2 fw-bold">Notes:</div>
                        <div class="col-md-10">{{ $request->payment->notes }}</div>
                    </div>
                    @endif

                    @if($request->payment->files)
                    <div class="row mt-3">
                        <div class="col-md-2 fw-bold">Attachments:</div>
                        <div class="col-md-10">
                            @foreach(is_array($request->payment->files) ? $request->payment->files : json_decode($request->payment->files) as $file)
                            <a href="{{ asset('storage/' . $file) }}" target="_blank" class="btn btn-sm btn-outline-secondary mb-2">
                                <i class="fas fa-file-download"></i> View Attachment
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Approval Process Timeline -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Approval Process</h5>
                </div>
                <div class="card-body">
                    <!-- Progress Tracker -->
                    <div class="progress-tracker mb-4">
                        @php
                            $totalSteps = $approvalSteps->count();
                            $completedSteps = $approvalSteps->where('status', 'approved')->count();
                            $rejectedSteps = $approvalSteps->where('status', 'rejected')->count();
                            $progressPercentage = $totalSteps > 0 ? ($completedSteps / $totalSteps) * 100 : 0;
                        @endphp
                        
                        <div class="d-flex justify-content-between mb-1">
                            <div>
                                <strong>Progress:</strong> {{ $completedSteps }} of {{ $totalSteps }} steps completed
                            </div>
                            <div>
                                <strong>{{ round($progressPercentage) }}%</strong>
                            </div>
                        </div>
                        
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ $progressPercentage }}%" 
                                 aria-valuenow="{{ $progressPercentage }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100"></div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-2">
                            @foreach($approvalSteps as $index => $step)
                            <div class="text-center">
                                <span class="badge {{ $step->status === 'approved' ? 'bg-success' : 
                                                      ($step->status === 'rejected' ? 'bg-danger' : 
                                                      ($step->status === 'pending' && $request->current_step == $step->step_order ? 'bg-warning' : 'bg-secondary')) }}">
                                    {{ ucfirst($step->role) }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="position-relative p-4">
                        <!-- Vertical timeline line -->
                        <div class="position-absolute" style="top: 0; bottom: 0; left: 11px; width: 2px; background-color: #e9ecef;"></div>
                        
                        @foreach($approvalSteps as $step)
                        <div class="row mb-4">
                            <div class="col-auto">
                                <div class="position-relative">
                                    <div class="rounded-circle d-flex justify-content-center align-items-center 
                                        {{ $step->status === 'approved' ? 'bg-success' : 
                                           ($step->status === 'rejected' ? 'bg-danger' : 
                                           ($step->status === 'pending' && $request->current_step == $step->step_order ? 'bg-warning' : 'bg-secondary')) }}"
                                        style="width: 24px; height: 24px; z-index: 1;">
                                        <i class="fas fa-{{ $step->status === 'approved' ? 'check' : 
                                                         ($step->status === 'rejected' ? 'times' : 'clock') }} 
                                                         text-white small"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col">
                                <div class="card shadow-sm h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center 
                                        {{ $step->status === 'approved' ? 'bg-success bg-opacity-10' : 
                                           ($step->status === 'rejected' ? 'bg-danger bg-opacity-10' : 
                                           ($step->status === 'pending' && $request->current_step == $step->step_order ? 'bg-warning bg-opacity-10' : 'bg-light')) }}">
                                        <h6 class="mb-0">{{ ucfirst($step->role) }} Approval</h6>
                                        <span class="badge {{ $step->status === 'approved' ? 'bg-success' : 
                                                           ($step->status === 'rejected' ? 'bg-danger' : 
                                                           ($step->status === 'pending' && $request->current_step == $step->step_order ? 'bg-warning' : 'bg-secondary')) }}">
                                            {{ ucfirst($step->status) }}
                                        </span>
                                    </div>
                                    
                                    <div class="card-body">
                                        <div class="row mb-2">
                                            <div class="col-md-4 fw-bold">Approver:</div>
                                            <div class="col-md-8">{{ $step->user->name }}</div>
                                        </div>
                                        
                                        @if($step->status !== 'pending')
                                        <div class="row mb-2">
                                            <div class="col-md-4 fw-bold">Date Processed:</div>
                                            <div class="col-md-8">{{ $step->processed_at ? $step->processed_at->format('d M Y, h:i A') : 'N/A' }}</div>
                                        </div>
                                        
                                        @if(!$loop->first && $step->processed_at && $approvalSteps[$loop->index - 1]->processed_at)
                                        <div class="row mb-2">
                                            <div class="col-md-4 fw-bold">Time Elapsed:</div>
                                            <div class="col-md-8">
                                                @php
                                                    $previousStep = $approvalSteps[$loop->index - 1];
                                                    $elapsed = $step->processed_at->diffForHumans($previousStep->processed_at, true);
                                                @endphp
                                                {{ $elapsed }}
                                            </div>
                                        </div>
                                        @endif
                                        
                                        @if($step->comments)
                                        <div class="row mb-2">
                                            <div class="col-md-4 fw-bold">Comments:</div>
                                            <div class="col-md-8">{{ $step->comments }}</div>
                                        </div>
                                        @endif
                                        @endif
                                        
                                        @if($step->status === 'pending' && $request->current_step == $step->step_order && auth()->user()->id == $step->user_id)
                                        <div class="alert alert-warning mt-3 mb-0">
                                            <i class="fas fa-info-circle me-2"></i> Waiting for your approval
                                            <div class="mt-2">
                                                <button type="button" class="btn btn-success btn-sm approve-action" data-bs-toggle="modal" data-bs-target="#approveModal" data-request-id="{{ $request->id }}" data-amount="{{ $request->payment->amount }}">
                                                    <i class="fas fa-check me-1"></i> Approve
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm reject-action" data-bs-toggle="modal" data-bs-target="#rejectModal" data-request-id="{{ $request->id }}">
                                                    <i class="fas fa-times me-1"></i> Reject
                                                </button>
                                            </div>
                                        </div>
                                        @elseif($step->status === 'pending' && $request->current_step == $step->step_order)
                                        <div class="alert alert-warning mt-3 mb-0">
                                            <i class="fas fa-info-circle me-2"></i> Waiting for approval from {{ $step->user->name }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('wallet.approval.approve', $request) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="approveModalLabel">Approve Wallet Top-up</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="comment" class="form-label">Comments (Optional)</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3" placeholder="Add any comments or notes about this approval"></textarea>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> You are approving a wallet top-up of <strong>SAR {{ number_format($request->payment->amount, 2) }}</strong>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i> Confirm Approval
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('wallet.approval.reject', $request) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Wallet Top-up</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" placeholder="Explain why you are rejecting this top-up request" required></textarea>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i> Rejecting this request will terminate the approval process. This action cannot be undone.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-1"></i> Confirm Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Handle approve button click
    $('.approve-action').click(function(e) {
        e.preventDefault();
        const requestId = $(this).data('request-id');
        const amount = $(this).data('amount');
        
        Swal.fire({
            title: 'Approve Top-up Request',
            html: `
                <div class="mb-3">
                    <label for="approve-comment" class="form-label">Comments (Optional)</label>
                    <textarea id="approve-comment" class="form-control" rows="3" placeholder="Add any comments about this approval"></textarea>
                </div>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    You are about to approve a wallet top-up of <strong>SAR ${amount}</strong>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-check me-1"></i> Confirm Approval',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#198754',
            reverseButtons: true,
            focusConfirm: false,
            preConfirm: () => {
                return {
                    comment: $('#approve-comment').val()
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Processing...',
                    html: 'Please wait while we process your approval',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });

                // Submit approval
                axios.post(`/wallet/approvals/${requestId}/approve`, {
                    comment: result.value.comment
                })
                .then(response => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Approved!',
                        text: 'The top-up request has been approved successfully.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.reload();
                    });
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response?.data?.message || 'Failed to process approval. Please try again.',
                    });
                });
            }
        });
    });

    // Handle reject button click
    $('.reject-action').click(function(e) {
        e.preventDefault();
        const requestId = $(this).data('request-id');
        
        Swal.fire({
            title: 'Reject Top-up Request',
            html: `
                <div class="mb-3">
                    <label for="reject-comment" class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                    <textarea id="reject-comment" class="form-control" rows="3" placeholder="Please provide a reason for rejection" required></textarea>
                </div>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    This action will terminate the approval process and cannot be undone.
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-times me-1"></i> Confirm Rejection',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#dc3545',
            reverseButtons: true,
            focusConfirm: false,
            preConfirm: () => {
                const comment = $('#reject-comment').val();
                if (!comment) {
                    Swal.showValidationMessage('Please provide a reason for rejection');
                }
                return { comment };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Processing...',
                    html: 'Please wait while we process your rejection',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });

                // Submit rejection
                axios.post(`/wallet/approvals/${requestId}/reject`, {
                    comment: result.value.comment
                })
                .then(response => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Rejected',
                        text: 'The top-up request has been rejected.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.reload();
                    });
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response?.data?.message || 'Failed to process rejection. Please try again.',
                    });
                });
            }
        });
    });
});
</script>
@endpush

@push('styles')
<style>
    .badge {
        font-size: 0.8rem;
        font-weight: 500;
    }
    
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
    
    /* Timeline styling enhancements */
    .timeline-item {
        position: relative;
        padding-left: 3rem;
        margin-bottom: 2rem;
    }
    
    .timeline-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 15px;
        height: 100%;
        width: 2px;
        background-color: #e9ecef;
    }
    
    .timeline-badge {
        position: absolute;
        left: 0;
        top: 0;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        text-align: center;
        line-height: 30px;
        z-index: 1;
    }

    /* Add styles for approval buttons */
    .approval-actions {
        display: flex;
        gap: 0.5rem;
    }

    .approval-actions .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* SweetAlert2 customizations */
    .swal2-popup textarea.form-control {
        font-size: 0.875rem;
        padding: 0.5rem;
    }

    .swal2-popup .alert {
        margin-top: 1rem;
        font-size: 0.875rem;
    }
</style>
@endpush 