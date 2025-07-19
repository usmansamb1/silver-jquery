@extends('layouts.app')

@section('title', __('Wallet Top-up Approval Details'))

@section('content')
<div class="container py-4">
    @php
    if (! function_exists('hexToRgba')) {
        // Helper function to convert hex to rgba for PHP sections
        function hexToRgba($hex, $opacity) {
            // Remove the hash if it exists
            $hex = str_replace('#', '', $hex);
            
            // Parse the hex values
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
            
            // Return the rgba value
            return "rgba($r, $g, $b, $opacity)";
        }
    }
    @endphp
    <div class="row">
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">{{ __('Wallet Top-up Approval Details') }}</h2>
                @role('customer')
                <a href="{{ route('wallet.history') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i> {{ __('Back to History') }}
                </a> 
                @endrole
                @role('admin|finance|validation|activation|audit|it')
                <a href="{{ route('wallet.approvals.my-approvals') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Approvals') }}
                </a>
                @endrole 
            </div>
            
            <nav aria-label="breadcrumb" class="mt-2">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('wallet.history') }}">{{ __('Wallet History') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('Approval Details') }}</li>
                </ol>
            </nav>
        </div>

        <!-- Application Details Section -->
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            {{ __('Wallet Top-up Details') }}
                        </h5>
                        @php
                            use App\Helpers\StatusHelper;

                            // Get status details from the relationship or directly from the status code
                            $statusCode = $request->status;
                            
                            // Get status display properties using our helper
                            $statusColor = StatusHelper::getStatusColor($statusCode);
                            $statusName = StatusHelper::getStatusDisplayName($statusCode);
                            $statusBadgeClass = StatusHelper::getStatusBadgeClass($statusCode);
                        @endphp
                        <span class="badge fs-6 px-3 py-2 {{ $statusBadgeClass }}" 
                              style="{{ isset($statusColor) ? 'background-color: '.$statusColor.' !important;' : '' }}">
                            {{ $statusName }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="row mb-3">
                                <div class="col-lg-4 fw-bold text-muted">{{ __('Application ID #') }}:</div>
                                <div class="col-lg-8">{{ $request->id }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-4 fw-bold text-muted">{{ __('Reference #') }}:</div>
                                <div class="col-lg-8">{{ $request->reference_no ?? 'N/A' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-4 fw-bold text-muted">{{ __('Customer') }}:</div>
                                <div class="col-lg-8">{{ $request->payment->user->getFormattedCustomerNoAttribute() }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-4 fw-bold text-muted">{{ __('Payment Method') }}:</div>
                                <div class="col-lg-8">
                                    <span class="d-inline-flex align-items-center">
                                        @php
                                            $paymentType = strtolower($request->payment->payment_type);
                                            $paymentIcon = 'credit-card';
                                            
                                            if (strpos($paymentType, 'bank') !== false) {
                                                $paymentIcon = 'university';
                                            } elseif (strpos($paymentType, 'transfer') !== false) {
                                                $paymentIcon = 'exchange-alt';
                                            }
                                        @endphp
                                        <i class="fas fa-{{ $paymentIcon }} text-primary me-2"></i>
                                        {{ ucfirst(str_replace('_', ' ', $request->payment->payment_type)) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row mb-3">
                                <div class="col-lg-4 fw-bold text-muted">{{ __('Amount') }}:</div>
                                <div class="col-lg-8 fs-5 fw-bold text-primary">
                                    SAR {{ number_format($request->payment->amount, 2) }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-4 fw-bold text-muted">{{ __('Date Submitted') }}:</div>
                                <div class="col-lg-8">
                                    <i class="far fa-calendar-alt me-1 text-muted"></i>
                                    {{ $request->created_at->format('d M Y, h:i A') }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-4 fw-bold text-muted">{{ __('Current Status') }}:</div>
                                <div class="col-lg-8">
                                    <span class="badge {{ $statusBadgeClass }}" 
                                          style="{{ isset($statusColor) ? 'background-color: '.$statusColor.' !important;' : '' }}">
                                        {{ $statusName }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($request->payment->notes)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="fw-bold text-muted mb-2"><i class="fas fa-sticky-note me-2"></i> {{ __('Notes') }}:</h6>
                            <div class="p-3 bg-light rounded">
                                {{ $request->payment->notes }}
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($request->payment->files)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="fw-bold text-muted mb-2"><i class="fas fa-paperclip me-2"></i> {{ __('Attachments') }}:</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach(is_array($request->payment->files) ? $request->payment->files : json_decode($request->payment->files) as $file)
                                <a href="{{ asset('storage/' . $file) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-file-download me-1"></i> 
                                    {{ basename($file) }}
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @role('admin|finance|validation|activation|audit|it')
        <!-- Approvers List (Horizontal Process Flow) -->
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-users text-primary me-2"></i>
                        {{ __('Approval Flow') }}
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Horizontal Step Progress -->
                    <div class="approval-flow-container">
                        <div class="progress-tracker mb-4">
                            @php
                                $totalSteps = $approvalSteps->count();
                                $completedSteps = $approvalSteps->where('status', 'approved')->count();
                                $rejectedSteps = $approvalSteps->where('status', 'rejected')->count();
                                $progressPercentage = $totalSteps > 0 ? ($completedSteps / $totalSteps) * 100 : 0;
                            @endphp
                            
                            <div class="d-flex justify-content-between mb-2">
                                <div>
                                    <strong>{{ __('Progress') }}:</strong> {{ $completedSteps }} {{ __('of') }} {{ $totalSteps }} {{ __('steps completed') }}
                                </div>
                                <div>
                                    <strong>{{ round($progressPercentage) }}%</strong>
                                </div>
                            </div>
                            
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ $progressPercentage }}%" 
                                     aria-valuenow="{{ $progressPercentage }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100"></div>
                            </div>
                        </div>
                        
                        <div class="approval-steps position-relative">
                            <div class="connector-line"></div>
                            <div class="row">
                                @foreach($approvalSteps as $index => $step)
                                @php
                                    // Get status from relationship if available, otherwise fallback to string status
                                    $stepStatusCode = $step->status;
                                    
                                    // Try to get color from relationship if it exists and is an object
                                    $stepStatusColor = null;
                                    if (method_exists($step, 'stepStatus') && is_object($step->stepStatus)) {
                                        $stepStatusColor = $step->stepStatus->color ?? null;
                                    }
                                    
                                    // Determine the step state
                                    $isApproved = $stepStatusCode === \App\Models\StepStatus::APPROVED;
                                    $isRejected = $stepStatusCode === \App\Models\StepStatus::REJECTED;
                                    $isPending = $stepStatusCode === \App\Models\StepStatus::PENDING;
                                    $isCurrentStep = $isPending && $request->current_step == $step->step_order;
                                    $canApprove = $isCurrentStep && auth()->user()->id == $step->user_id;
                                    
                                    // Get status display properties using our helper
                                    $statusClass = StatusHelper::getStatusBadgeClass($stepStatusCode);
                                    $statusIcon = StatusHelper::getStatusIcon($stepStatusCode);
                                    
                                    // Override for current step
                                    if ($isCurrentStep) {
                                        $statusClass = 'bg-warning';
                                        $statusIcon = 'clock';
                                    }
                                @endphp
                                <div class="col">
                                    <div class="step-item text-center">
                                        <div class="step-icon mx-auto mb-2 d-flex align-items-center justify-content-center
                                             {{ $canApprove ? 'active-approver' : '' }}"
                                             data-status="{{ $statusClass }}"
                                             data-bs-toggle="tooltip"
                                             data-bs-placement="top"
                                             title="{{ $canApprove ? 'Your approval is required' : '' }}"
                                             style="{{ $stepStatusColor ? 'border-color: '.$stepStatusColor.';' : '' }}"
                                             @if($canApprove || auth()->user()->hasRole('admin'))
                                             data-user-id="{{ $step->user_id }}"
                                             data-request-id="{{ $request->id }}"
                                             data-step-id="{{ $step->id }}"
                                             data-can-approve="{{ $canApprove ? 'true' : 'false' }}"
                                             onclick="handleApproverClick(this)"
                                             style="cursor: pointer; {{ $stepStatusColor ? 'border-color: '.$stepStatusColor.';' : '' }}"
                                             @endif
                                        >
                                            <i class="fas fa-{{ $statusIcon }} icon-{{ $statusClass }}" style="{{ $stepStatusColor ? 'color: '.$stepStatusColor.';' : '' }}"></i>
                                        </div>
                                        <div class="step-name ">
                                            {{ $step->user->name }}
                                        </div>
                                        <div class="step-role text-muted small mb-2">
                                            {{ ucfirst($step->role) }}
                                        </div>
                                        <span class="badge bg-{{ $statusClass }} px-3 py-2" style="{{ $stepStatusColor ? 'background-color: '.$stepStatusColor.' !important;' : '' }}">
                                            {{ $step->getStatusName() }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Approval History (Vertical Timeline) -->
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-history text-primary me-2"></i>
                        {{ __('Approval History') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="approval-timeline position-relative">
                        <!-- Vertical timeline line -->
                        <div class="timeline-line"></div>
                        
                        @forelse($approvalSteps->whereIn('status', [\App\Models\StepStatus::APPROVED, \App\Models\StepStatus::REJECTED])->sortByDesc('processed_at') as $step)
                        @php
                            $isApproved = $step->status === \App\Models\StepStatus::APPROVED;
                            $statusClass = $isApproved ? 'success' : 'danger';
                            $statusIcon = $isApproved ? 'check-circle' : 'times-circle';
                            $actionText = $isApproved ? 'Approved' : 'Rejected';
                            
                            // Try to get color from relationship if it exists
                            $stepStatusColor = null;
                            if (method_exists($step, 'stepStatus') && is_object($step->stepStatus)) {
                                $stepStatusColor = $step->stepStatus->color ?? null;
                            }
                        @endphp
                        <div class="timeline-item">
                            <div class="timeline-icon bg-{{ $statusClass }}" style="{{ $stepStatusColor ? 'background-color: '.$stepStatusColor.' !important;' : '' }}">
                                <i class="fas fa-{{ $statusIcon }}"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="card shadow-sm border-0">
                                    <div class="card-header bg-{{ $statusClass }} bg-opacity-10 d-flex justify-content-between align-items-center border-0" 
                                         style="{{ $stepStatusColor ? 'background-color: '.hexToRgba($stepStatusColor, 0.1).' !important;' : '' }}">
                                        <div>
                                            <span class="fw-bold">{{ $step->user->name }}</span>
                                            <span class="text-muted ms-2 small">{{ ucfirst($step->role) }}</span>
                                        </div>
                                        <span class="badge bg-{{ $statusClass }}" style="{{ $stepStatusColor ? 'background-color: '.$stepStatusColor.' !important;' : '' }}">
                                            {{ $actionText }}
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="far fa-calendar-alt me-2 text-muted"></i>
                                            <span class="text-muted small">
                                                {{ $step->processed_at ? $step->processed_at->format('d M Y, h:i A') : 'N/A' }}
                                            </span>
                                        </div>
                                        
                                        @if($step->comment)
                                        <div class="mt-2">
                                            <h6 class="fw-bold mb-1">Comments:</h6>
                                            <p class="mb-0">{{ $step->comment }}</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center p-4">
                            <i class="far fa-clipboard text-muted fs-1 mb-3"></i>
                            <p class="text-muted">{{ __('No approval actions have been recorded yet.') }}</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        @endrole
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Card styling */
    .card {
        border-radius: 0.75rem;
        overflow: hidden;
    }
    
    .card-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid rgba(0,0,0,.05);
    }
    
    /* Badge styling */
    .badge {
        font-weight: 500;
        letter-spacing: 0.3px;
    }
    
    /* Horizontal approval flow styling */
    .approval-steps {
        display: flex;
        justify-content: center ;
        padding: 2rem 1rem;
    }
    
    /* Increase spacing between each step item */
    .approval-steps .row {
        --bs-gutter-x: 0;       /* remove default bootstrap gutters */
        display: flex;          /* enforce flex layout */
        justify-content: center;/* center items */
        gap: 4rem;              /* add consistent spacing between steps */
    }
    
    .connector-line {
        position: absolute;
        top: 40px;
        left: 0;
        right: 0;
        height: 2px;
        background-color: #e9ecef;
        z-index: 0;
    }
    
    .step-item {
        position: relative;
        z-index: 1;
        min-width: 190px;
    }
    
    .step-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #fff;
        border: 2px solid #ced4da;
        position: relative;
        transition: all 0.3s ease;
    }
    
    .step-icon i {
        font-size: 1.25rem;
    }
    
    .step-icon.active-approver {
        transform: scale(1.1);
        box-shadow: 0 0 0 5px rgba(255, 193, 7, 62%);
        cursor: pointer;
    }
    
    .icon-success {
        color: #198754;
    }
    
    .icon-danger {
        color: #dc3545;
    }
    
    .icon-warning {
        color: #ffc107;
    }
    
    .icon-secondary {
        color: #6c757d;
    }
    
    .step-icon[data-status="success"] {
        border-color: #198754;
        background-color: #d1e7dd;
    }
    
    .step-icon[data-status="danger"] {
        border-color: #dc3545;
        background-color: #f8d7da;
    }
    
    .step-icon[data-status="warning"] {
        border-color: #ffc107;
        background-color: #fff3cd;
    }
    
    .step-icon[data-status="secondary"] {
        border-color: #6c757d;
        background-color: #e9ecef;
    }
    
    /* Vertical timeline styling */
    .approval-timeline {
        position: relative;
        padding: 1rem 0;
    }
    
    .timeline-line {
        position: absolute;
        left: 20px;
        top: 0;
        bottom: 0;
        width: 3px;
        background-color: #e9ecef;
        z-index: 0;
    }
    
    .timeline-item {
        position: relative;
        padding-left: 60px;
        margin-bottom: 2rem;
    }
    
    .timeline-icon {
        position: absolute;
        left: 0;
        top: 10px;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        z-index: 1;
    }
    
    .timeline-content {
        position: relative;
    }
    
    .timeline-content .card {
        margin-bottom: 0;
    }
    
    /* SweetAlert2 customizations */
    .swal2-popup textarea.form-control {
        font-size: 0.875rem;
        padding: 0.75rem;
        border-radius: 0.5rem;
        border: 1px solid #ced4da;
    }
    
    .swal2-popup .alert {
        margin-top: 1rem;
        font-size: 0.875rem;
        border-radius: 0.5rem;
    }

    .animated-icon {
        animation: iconBounce 1s infinite alternate;
    }

    @keyframes iconBounce {
        0% {
            transform: translateY(0);
        }
        100% {
            transform: translateY(-5px);
        }
    }
</style>
@endpush

@push('scripts')
<!-- Make sure SweetAlert2 and Axios are included -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
// Helper function to convert hex color to rgba for opacity
function hexToRgba(hex, opacity) {
    // Remove the hash if it exists
    hex = hex.replace('#', '');
    
    // Parse the hex values
    let r = parseInt(hex.substring(0, 2), 16);
    let g = parseInt(hex.substring(2, 4), 16);
    let b = parseInt(hex.substring(4, 6), 16);
    
    // Return the rgba value
    return `rgba(${r}, ${g}, ${b}, ${opacity})`;
}

$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Set up Axios CSRF token
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
});

function handleApproverClick(element) {
    const requestId = element.getAttribute('data-request-id');
    const canApprove = element.getAttribute('data-can-approve') === 'true';
    const stepId = element.getAttribute('data-step-id');
    const userId = element.getAttribute('data-user-id');
    
    // Check if current user can take action
    if (canApprove) {
        showApprovalModal(requestId);
    } else {
        // Admin or other users viewing
        showViewOnlyModal();
    }
}

function showApprovalModal(requestId) {
    const amount = parseFloat("{{ $request->payment->amount ?? 0 }}") || 0;
    
    Swal.fire({
        icon: "question",
        title: '{{ __('Approval Action Required') }}',
        html: `
            <div class="text-start mb-4">
                <p>{{ __('You are authorizing a wallet top-up for') }} <strong>SAR ${amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</strong></p>
                <p>{{ __('Please select your action below:') }}</p>
            </div>
            <div class="mb-3">
                <label class="form-label text-start d-block">{{ __('Comments') }}</label>
                <textarea id="action-comment" class="form-control" rows="3" placeholder="{{ __('Add any comments or notes (required for rejection)') }}"></textarea>
            </div>
        `,
        showCancelButton: false,
        showConfirmButton: false,
        showDenyButton: false,
        footer: `
            <div class="d-flex gap-2 w-100 justify-content-center">
                <button type="button" class="btn btn-success px-4 py-2" id="approveBtn">
                    <i class="fas fa-check-circle me-2 animated-icon"></i> {{ __('Approve') }}
                </button>
                <button type="button" class="btn btn-danger px-4 py-2" id="rejectBtn">
                    <i class="fas fa-times-circle me-2 animated-icon"></i> {{ __('Reject') }}
                </button>
                <button type="button" class="btn btn-secondary px-4 py-2" id="cancelBtn">
                    {{ __('Cancel') }}
                </button>
            </div>
        `,
        allowOutsideClick: false,
        didOpen: () => {
            document.getElementById('approveBtn').addEventListener('click', () => handleApprove(requestId));
            document.getElementById('rejectBtn').addEventListener('click', () => handleReject(requestId));
            document.getElementById('cancelBtn').addEventListener('click', () => Swal.close());
            
            // Auto-resize textarea
            const textarea = document.getElementById('action-comment');
            textarea.addEventListener('input', function() {
                this.style.height = '0';
                this.style.height = this.scrollHeight + 'px';
            });
        },
        customClass: {
            popup: 'animated fadeIn'
        }
    });
}

function showViewOnlyModal() {
    Swal.fire({
        title: '{{ __('View Only') }}',
        icon: 'info',
        html: `
            <p>{{ __('You are viewing the approval flow.') }}</p>
            <p>{{ __('Action is required from the assigned approver at the current step.') }}</p>
        `,
        confirmButtonText: '{{ __('OK') }}',
        confirmButtonColor: '#3085d6',
        timer: 3000,
        timerProgressBar: true,
        customClass: {
            popup: 'animated fadeIn'
        }
    });
}

function handleApprove(requestId) {
    const comment = document.getElementById('action-comment').value;
    
    Swal.fire({
        title: '{{ __('Confirm Approval') }}',
        icon: 'question',
        html: '{{ __('Are you sure you want to approve this request?') }}',
        showCancelButton: true,
        confirmButtonText: '{{ __('Yes, Approve') }}',
        cancelButtonText: '{{ __('Cancel') }}',
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return axios.post(`/wallet/approvals/${requestId}/approve`, { comment })
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    console.error('Approval error:', error.response?.data || error);
                    Swal.showValidationMessage(
                        error.response?.data?.message || '{{ __('Failed to approve request') }}'
                    );
                });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: '{{ __('Approved!') }}',
                text: result.value.message || '{{ __('Request approved successfully') }}',
                showConfirmButton: false,
                timer: 2000
            }).then(() => {
                window.location.reload();
            });
        }
    });
}

function handleReject(requestId) {
    const comment = document.getElementById('action-comment').value;
    
    if (!comment) {
        Swal.showValidationMessage('{{ __('Please provide a reason for rejection') }}');
        return;
    }
    
    Swal.fire({
        title: '{{ __('Confirm Rejection') }}',
        icon: 'warning',
        html: `
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i> {{ __('Rejecting this request will terminate the approval process. This action cannot be undone.') }}
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: '{{ __('Yes, Reject') }}',
        cancelButtonText: '{{ __('Cancel') }}',
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return axios.post(`/wallet/approvals/${requestId}/reject`, { 
                comment
            })
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    console.error('Rejection error:', error.response?.data || error);
                    Swal.showValidationMessage(
                        error.response?.data?.message || '{{ __('Failed to reject request') }}'
                    );
                });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: '{{ __('Rejected!') }}',
                text: result.value.message || '{{ __('Request rejected successfully') }}',
                showConfirmButton: false,
                timer: 2000
            }).then(() => {
                window.location.reload();
            });
        }
    });
}
</script>
@endpush 