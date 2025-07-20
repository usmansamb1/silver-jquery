@extends('layouts.app')

@section('title', __('Wallet Request Details'))

@section('content')

<div class="container-fluid">
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
      <h1 class="h3 mb-0 text-gray-800">{{ __('Wallet Request Details') }}</h1>
      <p class="mb-0">{{ __('Request') }} #{{ $walletRequest->reference_no ?? $walletRequest->id }}</p>
    </div>
    <div>
      <a href="{{ route('admin.wallet-requests.index') }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-left mr-1"></i> {{ __('Back to List') }}
      </a>
      
      @if($walletRequest->status == 'pending' || $walletRequest->status == 'in_progress' && auth()->user()->hasRole('admin'))
      <div class="btn-group ml-2">
        <button class="btn btn-sm btn-success" id="approveBtn">
          <i class="fas fa-check mr-1"></i> {{ __('Approve') }}
        </button>
        <button class="btn btn-sm btn-danger" id="rejectBtn">
          <i class="fas fa-times mr-1"></i> {{ __('Reject') }}
        </button>
      </div>
      @endif
    </div>
  </div>

  @include('partials.alerts')

  <div class="row">
    <!-- Request Details -->
    <div class="col-xl-8 col-lg-7">
      <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary">{{ __('Request Information') }}</h6>
          <div>
            @if($walletRequest->status == 'pending' || $walletRequest->status == 'in_progress')
              <span class="badge bg-warning" data-bs-toggle="tooltip" title="{{ __('awaiting_finance') }}">
                <i class="fas fa-clock me-1"></i>{{ __('Pending') }}
              </span>
            @elseif($walletRequest->status == 'approved')
              <span class="badge bg-success">
                <i class="fas fa-check-circle me-1"></i>{{ __('Approved') }}
              </span>
            @elseif($walletRequest->status == 'rejected')
              <span class="badge bg-danger">
                <i class="fas fa-times-circle me-1"></i>{{ __('Rejected') }}
              </span>
            @endif
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <p class="mb-1 small text-muted">{{ __('request_info.reference_no') }}</p>
              <p class="mb-0 font-weight-bold">{{ $walletRequest->reference_no ?? __('N/A') }}</p>
            </div>
            <div class="col-md-6 mb-3">
              <p class="mb-1 small text-muted">{{ __('Amount') }}</p>
              <p class="mb-0 font-weight-bold">{{ number_format($walletRequest->amount, 2) }}</p>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <p class="mb-1 small text-muted">{{ __('Payment Type') }}</p>
              <p class="mb-0">
                @if($walletRequest->payment)
                  <span class="badge bg-info">
                    <i class="fas fa-credit-card me-1"></i>{{ __('payment_types.' . $walletRequest->payment->payment_type) }}
                  </span>
                @else
                  <span class="badge bg-secondary">{{ __('N/A') }}</span>
                @endif
              </p>
            </div>
            <div class="col-md-6 mb-3">
              <p class="mb-1 small text-muted">{{ __('request_info.current_approval_step') }}</p>
              <p class="mb-0">
                @if($walletRequest->current_step)
                  <span class="badge bg-info">
                    <i class="fas fa-spinner me-1"></i>{{ __('approval_steps.' . $walletRequest->current_step) }}
                  </span>
                @else
                  <span class="badge bg-secondary">
                    <i class="fas fa-flag-checkered me-1"></i>{{ __('Completed') }}
                  </span>
                @endif
              </p>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <p class="mb-1 small text-muted">{{ __('Created At') }}</p>
              <p class="mb-0 font-weight-bold">{{ $walletRequest->created_at->format('Y-m-d H:i:s') }}</p>
            </div>
            <div class="col-md-6 mb-3">
              <p class="mb-1 small text-muted">{{ __('Updated At') }}</p>
              <p class="mb-0 font-weight-bold">{{ $walletRequest->updated_at->format('Y-m-d H:i:s') }}</p>
            </div>
          </div>
          
          @if($walletRequest->payment && !empty($walletRequest->payment->payment_notes))
          <div class="row">
            <div class="col-12 mb-3">
              <p class="mb-1 small text-muted">{{ __('request_info.payment_notes') }}</p>
              <p class="mb-0">{{ $walletRequest->payment->payment_notes }}</p>
            </div>
          </div>
          @endif
          
          @if($walletRequest->payment && $walletRequest->payment->payment_receipt_path)
          <div class="row">
            <div class="col-12 mb-3">
              <p class="mb-1 small text-muted">{{ __('request_info.payment_receipt') }}</p>
              <a href="{{ Storage::url($walletRequest->payment->payment_receipt_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-download mr-1"></i> {{ __('request_info.view_receipt') }}
              </a>
            </div>
          </div>
          @endif
        </div>
      </div>
      
      <!-- Payment Information -->
      @if($walletRequest->payment)
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">{{ __('request_info.payment_information') }}</h6>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <p class="mb-1 small text-muted">{{ __('Payment Method') }}</p>
              <p class="mb-0 font-weight-bold">{{ __('payment_types.' . $walletRequest->payment->payment_type) }}</p>
            </div>
            <div class="col-md-6 mb-3">
              <p class="mb-1 small text-muted">{{ __('Payment Status') }}</p>
              <p class="mb-0">
                <span class="badge {{ $walletRequest->payment->status == 'completed' ? 'bg-success' : 'bg-warning' }}">
                  <i class="fas fa-{{ $walletRequest->payment->status == 'completed' ? 'check-circle' : 'clock' }} me-1"></i>{{ __('payment_status.' . $walletRequest->payment->status) }}
                </span>
              </p>
            </div>
          </div>
          
          @if($walletRequest->payment->payment_type == 'bank_transfer')
          <div class="row">
            <div class="col-md-6 mb-3">
              <p class="mb-1 small text-muted">{{ __('request_info.bank_name') }}</p>
              <p class="mb-0 font-weight-bold">{{ $walletRequest->payment->bank_name ?? __('N/A') }}</p>
            </div>
            <div class="col-md-6 mb-3">
              <p class="mb-1 small text-muted">{{ __('request_info.transfer_number_reference') }}</p>
              <p class="mb-0 font-weight-bold">{{ $walletRequest->payment->transfer_number ?? __('N/A') }}</p>
            </div>
          </div>
          @endif
          
          @if(in_array($walletRequest->payment->payment_type, ['bank_lc', 'bank_guarantee']))
          <div class="row">
            <div class="col-md-6 mb-3">
              <p class="mb-1 small text-muted">{{ __('request_info.issuing_bank') }}</p>
              <p class="mb-0 font-weight-bold">{{ $walletRequest->payment->issuing_bank ?? __('N/A') }}</p>
            </div>
            <div class="col-md-6 mb-3">
              <p class="mb-1 small text-muted">{{ __('request_info.document_number') }}</p>
              <p class="mb-0 font-weight-bold">{{ $walletRequest->payment->document_number ?? __('N/A') }}</p>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <p class="mb-1 small text-muted">{{ __('request_info.expiry_date') }}</p>
              <p class="mb-0 font-weight-bold">{{ $walletRequest->payment->expiry_date ? date('Y-m-d', strtotime($walletRequest->payment->expiry_date)) : __('N/A') }}</p>
            </div>
          </div>
          @endif
        </div>
      </div>
      @endif
      
      <!-- Approval Steps -->
      @if($walletRequest->steps && $walletRequest->steps->count() > 0)
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">{{ __('request_info.approval_timeline') }}</h6>
        </div>
        <div class="card-body">
          <div class="timeline">
            @foreach($walletRequest->steps as $step)
            <div class="timeline-item">
              <div class="timeline-marker 
                @if($step->status == 'approved') bg-success
                @elseif($step->status == 'rejected') bg-danger
                @else bg-warning @endif"></div>
              <div class="timeline-content">
                <h3 class="timeline-title">
                  {{ __('approval_steps.' . $step->role) }} {{ __('request_info.step') }} 
                  @if($step->status == 'approved')
                    <span class="badge bg-success"><i class="fas fa-check me-1"></i>{{ __('Approved') }}</span>
                  @elseif($step->status == 'rejected')
                    <span class="badge bg-danger"><i class="fas fa-times me-1"></i>{{ __('Rejected') }}</span>
                  @else
                    <span class="badge bg-warning"><i class="fas fa-clock me-1"></i>{{ __('Pending') }}</span>
                  @endif
                </h3>
                <p class="mb-0">{{ __('request_info.assigned_to') }}: {{ $step->user->name ?? __('N/A') }}</p>
                @if($step->processed_at)
                <p class="mb-0 small text-muted">{{ __('request_info.processed_on') }}: {{ date('Y-m-d H:i:s', strtotime($step->processed_at)) }}</p>
                @endif
                @if($step->comment)
                <div class="mt-2 p-2 bg-light rounded">
                  <p class="mb-0 small"><strong>{{ __('request_info.comment') }}:</strong> {{ $step->comment }}</p>
                </div>
                @endif
                
                @if($step->status == 'pending' && $walletRequest->current_step == $step->role && auth()->user()->hasRole($step->role))
                <div class="mt-3">
                  <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#approveModal">
                    <i class="fas fa-check mr-1"></i> {{ __('Approve') }}
                  </button>
                  <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rejectModal">
                    <i class="fas fa-times mr-1"></i> {{ __('Reject') }}
                  </button>
                </div>
                @endif
              </div>
            </div>
            @endforeach
          </div>
        </div>
      </div>
      @endif
    </div>
    
    <!-- User Information -->
    <div class="col-xl-4 col-lg-5">
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">{{ __('request_info.requestor_information') }}</h6>
        </div>
        <div class="card-body">
          <div class="text-center mb-3">
            <img class="img-profile rounded-circle" width="80" height="80"
                 src="{{ $walletRequest->user->profile_image ? Storage::url($walletRequest->user->profile_image) : asset('img/default-avatar.png') }}">
            <h4 class="mt-2 mb-0">{{ $walletRequest->user->name }}</h4>
            <p class="text-muted small">{{ $walletRequest->user->email }}</p>
          </div>
          
          <div class="mb-3">
            <p class="mb-1 small text-muted">{{ __('Phone') }}</p>
            <p class="mb-0 font-weight-bold">{{ $walletRequest->user->phone ?? __('N/A') }}</p>
          </div>
          
          <div class="mb-3">
            <p class="mb-1 small text-muted">{{ __('request_info.user_type') }}</p>
            <p class="mb-0 font-weight-bold">
              @if($walletRequest->user->roles->count() > 0)
                @foreach($walletRequest->user->roles as $role)
                  <span class="badge badge-info">{{ $role->name }}</span>
                @endforeach
              @else
                <span class="badge badge-secondary">{{ __('request_info.no_roles_assigned') }}</span>
              @endif
            </p>
          </div>
          
          <div class="mb-3">
            <p class="mb-1 small text-muted">{{ __('request_info.current_wallet_balance') }}</p>
            <p class="mb-0 font-weight-bold">{{ number_format($walletRequest->user->wallet->balance ?? 0, 2) }}</p>
          </div>
          
          <div class="mb-3">
            <p class="mb-1 small text-muted">{{ __('request_info.account_created') }}</p>
            <p class="mb-0 font-weight-bold">{{ $walletRequest->user->created_at->format('Y-m-d') }}</p>
          </div>
          
          <div class="text-center mt-3">
            <a href="{{ route('admin.users.show', $walletRequest->user) }}" class="btn btn-primary btn-sm">
              <i class="fas fa-user mr-1"></i> {{ __('request_info.view_full_profile') }}
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Remove the existing modals and replace with SweetAlert2 script -->

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Store translations in JavaScript variables
    const translations = {
      approveWalletRequest: @json(__('wallet_messages.approve_wallet_request')),
      approveConfirmation: @json(__('wallet_messages.approve_confirmation', ['amount' => number_format($walletRequest->amount, 2)])),
      commentOptional: @json(__('wallet_messages.comment_optional')),
      addCommentsPlaceholder: @json(__('wallet_messages.add_comments_placeholder')),
      rejectWalletRequest: @json(__('wallet_messages.reject_wallet_request')),
      rejectConfirmation: @json(__('wallet_messages.reject_confirmation')),
      reasonForRejection: @json(__('wallet_messages.reason_for_rejection')),
      rejectionReasonPlaceholder: @json(__('wallet_messages.rejection_reason_placeholder')),
      rejectionReasonRequired: @json(__('wallet_messages.rejection_reason_required')),
      approve: @json(__('Approve')),
      reject: @json(__('Reject')),
      cancel: @json(__('Cancel'))
    };

    // Approve Button
    const approveBtn = document.getElementById('approveBtn');
    if (approveBtn) {
      approveBtn.addEventListener('click', function() {
        Swal.fire({
          title: translations.approveWalletRequest,
          html: `
            <p>${translations.approveConfirmation}</p>
            <div class="form-group mt-3">
              <label for="swal-comment" class="text-left d-block">${translations.commentOptional}</label>
              <textarea id="swal-comment" class="form-control" placeholder="${translations.addCommentsPlaceholder}"></textarea>
            </div>
          `,
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#28a745',
          cancelButtonColor: '#6c757d',
          confirmButtonText: `<i class="fas fa-check mr-1"></i> ${translations.approve}`,
          cancelButtonText: translations.cancel,
          showLoaderOnConfirm: true,
          preConfirm: () => {
            // Create form for traditional submit to avoid AJAX issues
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('admin.wallet-requests.approve', $walletRequest) }}';
            form.style.display = 'none';
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Add comment if provided
            const comment = document.getElementById('swal-comment').value;
            if (comment) {
              const commentInput = document.createElement('input');
              commentInput.type = 'hidden';
              commentInput.name = 'comment';
              commentInput.value = comment;
              form.appendChild(commentInput);
            }
            
            // Add form to body and submit
            document.body.appendChild(form);
            form.submit();
            
            return false; // Prevent SweetAlert from closing
          }
        });
      });
    }
    
    // Reject Button
    const rejectBtn = document.getElementById('rejectBtn');
    if (rejectBtn) {
      rejectBtn.addEventListener('click', function() {
        Swal.fire({
          title: translations.rejectWalletRequest,
          html: `
            <p class="text-danger">${translations.rejectConfirmation}</p>
            <div class="form-group mt-3">
              <label for="swal-rejection-reason" class="text-left d-block">${translations.reasonForRejection} <span class="text-danger">*</span></label>
              <textarea id="swal-rejection-reason" class="form-control" placeholder="${translations.rejectionReasonPlaceholder}"></textarea>
            </div>
          `,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#dc3545',
          cancelButtonColor: '#6c757d',
          confirmButtonText: `<i class="fas fa-times mr-1"></i> ${translations.reject}`,
          cancelButtonText: translations.cancel,
          showLoaderOnConfirm: true,
          preConfirm: () => {
            const rejectionReason = document.getElementById('swal-rejection-reason').value;
            
            if (!rejectionReason.trim()) {
              Swal.showValidationMessage(translations.rejectionReasonRequired);
              return false;
            }
            
            // Create form for traditional submit to avoid AJAX issues
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('admin.wallet-requests.reject', $walletRequest) }}';
            form.style.display = 'none';
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Add rejection reason
            const reasonInput = document.createElement('input');
            reasonInput.type = 'hidden';
            reasonInput.name = 'rejection_reason';
            reasonInput.value = rejectionReason;
            form.appendChild(reasonInput);
            
            // Add form to body and submit
            document.body.appendChild(form);
            form.submit();
            
            return false; // Prevent SweetAlert from closing
          }
        });
      });
    }
  });
</script>
@endpush

<style>
.timeline {
  position: relative;
  padding-left: 40px;
}

.timeline-item {
  position: relative;
  margin-bottom: 1.5rem;
}

.timeline-marker {
  position: absolute;
  left: -40px;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  border: 2px solid #fff;
  box-shadow: 0 0 0 3px #e3e6f0;
}

.timeline-content {
  padding-bottom: 1.5rem;
  border-bottom: 1px solid #e3e6f0;
}

.timeline-title {
  font-size: 1rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.timeline-item:last-child .timeline-content {
  border-bottom: none;
  padding-bottom: 0;
}

.table .badge {
  font-size: 0.85rem;
  padding: 0.4em 0.75em;
}

.table-striped tbody tr:nth-of-type(odd) {
  background-color: #f8f9fc;
}
</style>
@endsection 