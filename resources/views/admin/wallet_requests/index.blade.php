@extends('layouts.app')

@section('title', __('Admin Wallet Request Management'))

@section('content')
<div class="container-fluid">
  <!-- Page Header -->
  <div class="page-header bg-light border-bottom mb-4">
    <div class="container-fluid px-4 py-4">
      <div class="d-sm-flex align-items-center justify-content-between">
        <div>
          <h1 class="h3 mb-1 text-gray-800">{{ __('Wallet Top-up Requests') }}</h1>
          <p class="mb-0 text-muted">{{ __('Manage and process wallet top-up requests') }}</p>
        </div>
        <div class="d-flex gap-2">
          <button class="btn btn-outline-primary px-4" type="button" id="toggleFilterBtn" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
            <i class="fas fa-filter me-2"></i>{{ __('Filters') }}
            <span class="filter-counter badge bg-primary ms-2" style="display: none;">0</span>
          </button>
          <button class="btn btn-outline-secondary" id="clearFiltersBtn" style="display: none;">
            <i class="fas fa-times me-2"></i>{{ __('Clear Filters') }}
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="container-fluid px-4">
    @include('partials.alerts')
    
    <!-- Active Filters -->
    @include('partials.filter-chips', [
      'resetRoute' => 'admin.wallet-requests.reset-filters',
      'indexRoute' => 'admin.wallet-requests.index'
    ])

    <!-- Filters -->
    <div class="card shadow-sm mb-4 border-0">
      <div class="collapse {{ count(array_filter($filters ?? [])) ? 'show' : '' }}" id="filterCollapse">
        <div class="card-body bg-light">
      <form id="filterForm" method="GET" action="{{ route('admin.wallet-requests.index') }}">
        <div class="row">
          <div class="col-md-3 mb-3">
            <label for="status" class="form-label small font-weight-bold">{{ __('Status') }}</label>
            <select name="status" id="status" class="form-control form-control-sm filter-control">
              <option value="">{{ __('All Statuses') }}</option>
              <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
              <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
              <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
            </select>
          </div>
          <div class="col-md-3 mb-3">
            <label for="payment_type" class="form-label small font-weight-bold">{{ __('Payment Type') }}</label>
            <select name="payment_type" id="payment_type" class="form-control form-control-sm filter-control">
              <option value="">{{ __('All Types') }}</option>
              @foreach($paymentTypes ?? ['stripe', 'bank_transfer', 'bank_lc', 'bank_guarantee'] as $type)
                <option value="{{ $type }}" {{ request('payment_type') == $type ? 'selected' : '' }}>
                  {{ __('payment_types.' . $type) }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3 mb-3">
            <label for="current_step" class="form-label small font-weight-bold">{{ __('Current Step') }}</label>
            <select name="current_step" id="current_step" class="form-control form-control-sm filter-control">
              <option value="">{{ __('All Steps') }}</option>
              <option value="finance" {{ request('current_step') == 'finance' ? 'selected' : '' }}>{{ __('approval_steps.finance') }}</option>
              <option value="validation" {{ request('current_step') == 'validation' ? 'selected' : '' }}>{{ __('approval_steps.validation') }}</option>
              <option value="activation" {{ request('current_step') == 'activation' ? 'selected' : '' }}>{{ __('approval_steps.activation') }}</option>
            </select>
          </div>
          <div class="col-md-3 mb-3">
            <label for="date_range" class="form-label small font-weight-bold">{{ __('Date Range') }}</label>
            <div class="input-group">
              <input type="text" class="form-control form-control-sm date-range-picker" id="date_range" readonly>
              <input type="hidden" name="start_date" id="start_date" value="{{ request('start_date') }}">
              <input type="hidden" name="end_date" id="end_date" value="{{ request('end_date') }}">
              <div class="input-group-append">
                <button class="btn btn-sm btn-outline-secondary clear-date-range" type="button">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <label for="min_amount" class="form-label small font-weight-bold">{{ __('Min Amount') }}</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text form-control-sm">{{ __('SAR') }}</span>
              </div>
              <input type="number" step="0.01" class="form-control form-control-sm filter-control" id="min_amount" name="min_amount" value="{{ request('min_amount') }}">
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <label for="max_amount" class="form-label small font-weight-bold">{{ __('Max Amount') }}</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text form-control-sm">{{ __('SAR') }}</span>
              </div>
              <input type="number" step="0.01" class="form-control form-control-sm filter-control" id="max_amount" name="max_amount" value="{{ request('max_amount') }}">
            </div>
          </div>
        </div>
        <div class="d-flex gap-2 mt-3">
          <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i> {{ __('Apply Filters') }}</button>
          <button type="button" id="resetFiltersBtn" class="btn btn-outline-secondary"><i class="fas fa-redo me-1"></i> {{ __('Reset') }}</button>
          </div>
        </form>
        </div>
      </div>
    </div>

    <!-- Requests Table -->
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
        <h6 class="m-0 font-weight-bold text-dark">{{ __('Wallet Requests') }}</h6>
        <span class="badge bg-primary rounded-pill">{{ $requests->total() }} {{ __('Records') }}</span>
      </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover table-bordered" width="100%" cellspacing="0">
          <thead class="bg-light">
            <tr>
              <th>{{ __('Ref No') }}</th>
              <th>{{ __('User') }}</th>
              <th>{{ __('Amount') }}</th>
              <th>{{ __('Payment Type') }}</th>
              <th>{{ __('Created') }}</th>
              <th>{{ __('Status') }}</th>
              <th>{{ __('Current Step') }}</th>
              <th>{{ __('Actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($requests as $request)
              <tr>
                <td><span class="font-weight-bold text-primary">{{ $request->reference_no ?? $request->id }}</span></td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="mr-2">
                      <div class="avatar-circle">{{ strtoupper(substr($request->user->name ?? 'U', 0, 1)) }}</div>
                    </div>
                    <div>
                      <div class="font-weight-bold">{{ $request->user->name ?? __('N/A') }}</div>
                      <small class="text-muted">{{ $request->user->email ?? '' }}</small>
                    </div>
                  </div>
                </td>
                <td class="text-right font-weight-bold">{{ __('SAR') }} {{ number_format($request->amount, 2) }}</td>
                <td>
                  @if($request->payment)
                    <span class="badge bg-warning">{{ __('payment_types.' . $request->payment->payment_type) }}</span>
                  @else
                    <span class="badge bg-secondary">{{ __('N/A') }}</span>
                  @endif
                </td>
                <td>
                  <div>{{ $request->created_at->format('Y-m-d') }}</div>
                  <small class="text-muted">{{ $request->created_at->format('H:i') }}</small>
                </td>
                <td>
                  @if($request->status == 'pending' || $request->status == 'in_progress')
                    <span class="badge bg-warning">{{ __('Pending') }}</span>
                  @elseif($request->status == 'approved')
                    <span class="badge bg-success">{{ __('Approved') }}</span>
                  @elseif($request->status == 'rejected')
                    <span class="badge bg-danger">{{ __('Rejected') }}</span>
                  @else
                    <span class="badge bg-secondary">{{ $request->status }}</span>
                  @endif
                </td>
                <td>
                  @if($request->current_step)
                    <span class="badge bg-info">{{ __('approval_steps.' . $request->current_step) }}</span>
                  @else
                    <span class="badge bg-secondary">{{ __('Completed') }}</span>
                  @endif
                </td>
                <td>
                  <a href="{{ route('admin.wallet-requests.show', $request) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-eye"></i> {{ __('View') }}
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center py-4">
                  <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                  <p class="mb-0">{{ __('No wallet requests found') }}</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      
      <!-- Pagination -->
      <div class="d-flex justify-content-center mt-4">
        {{ $requests->withQueryString()->links() }}
      </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<style>
  .avatar-circle {
    width: 36px;
    height: 36px;
    background-color: #4e73df;
    border-radius: 50%;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
  }
  
  th {
    font-size: 0.85rem;
    text-transform: uppercase;
    color: #4a5568;
  }

  .badge {
    font-weight: 500;
    font-size: 0.75rem;
    letter-spacing: 0.25px;
    padding: 0.4em 0.8em;
    border-radius: 0.5rem;
  }
  
  .badge.rounded-pill {
    border-radius: 10rem !important;
  }
  
  .table-hover tbody tr:hover {
    background-color: #f8f9fc;
  }
  
  .daterangepicker {
    font-size: 0.85rem;
  }
  
  .filter-chip {
    display: inline-flex;
    align-items: center;
    background: #e9ecef;
    border-radius: 16px;
    padding: 4px 10px;
    margin-right: 5px;
    margin-bottom: 5px;
    font-size: 0.8rem;
  }
  
  .filter-chip .close {
    margin-left: 5px;
    font-size: 0.8rem;
  }
  
  .filter-highlight {
    background-color: rgba(78, 115, 223, 0.1);
    border-left: 3px solid #4e73df;
  }
  
  .page-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #dee2e6;
    margin-bottom: 0;
  }
  
  .page-header h1 {
    font-weight: 600;
    color: #2c3e50;
  }
  
  .page-header p {
    font-size: 0.95rem;
    color: #6c757d;
  }
  
  .btn-outline-primary {
    border-color: #4e73df;
    color: #4e73df;
    transition: all 0.3s ease;
  }
  
  .btn-outline-primary:hover {
    background-color: #4e73df;
    border-color: #4e73df;
    color: white;
  }
  
  .btn-outline-secondary {
    border-color: #6c757d;
    color: #6c757d;
    transition: all 0.3s ease;
  }
  
  .btn-outline-secondary:hover {
    background-color: #6c757d;
    border-color: #6c757d;
    color: white;
  }
  
  .card {
    border: 1px solid #e3e6f0;
    border-radius: .5rem;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    transition: all 0.3s ease;
  }
  
  .card:hover {
    box-shadow: 0 0.25rem 2rem 0 rgba(58, 59, 69, 0.2);
  }
  
  .card-header {
    background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
    border-bottom: 1px solid #e3e6f0;
  }
  
  .container-fluid.px-4 {
    padding-left: 1.5rem;
    padding-right: 1.5rem;
  }
  
  .btn {
    font-weight: 500;
    border-radius: 0.375rem;
    transition: all 0.3s ease;
  }
  
  .btn-primary {
    background-color: #4e73df;
    border-color: #4e73df;
  }
  
  .btn-primary:hover {
    background-color: #364fc7;
    border-color: #364fc7;
    transform: translateY(-1px);
  }
  
  .gap-2 {
    gap: 0.5rem;
  }
  
  /* Improve visual hierarchy */
  .table-responsive {
    border-radius: 0.5rem;
  }
  
  .table tbody td {
    vertical-align: middle;
    padding: 1rem 0.75rem;
  }
  
  .font-weight-bold {
    font-weight: 600;
  }
  
  .text-gray-800 {
    color: #2c3e50;
  }
  
  .text-muted {
    color: #6c757d;
  }
  
  .table thead th {
    background-color: #f8f9fa;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
  }

  /* Mobile responsive adjustments - Canvas theme compatible */
  @media (max-width: 767.98px) {
    .page-header .d-sm-flex {
      flex-direction: column;
      gap: 1rem;
    }
    
    .page-header .d-flex.gap-2 {
      justify-content: center;
      width: 100%;
    }

    .d-flex.gap-2.mt-3 {
      flex-wrap: wrap;
      justify-content: center;
    }
    
    .d-flex.gap-2.mt-3 .btn {
      min-width: 120px;
      flex: 0 1 auto;
    }
  }

  /* Table responsive adjustments */
  @media (max-width: 991.98px) {
    .table-responsive {
      font-size: 0.875rem;
    }
    
    .avatar-circle {
      width: 28px;
      height: 28px;
      font-size: 12px;
    }
  }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Initialize variables
  const filterForm = document.getElementById('filterForm');
  const filterControls = document.querySelectorAll('.filter-control');
  const clearFiltersBtn = document.getElementById('clearFiltersBtn');
  const resetFiltersBtn = document.getElementById('resetFiltersBtn');
  const filterCounter = document.querySelector('.filter-counter');
  const toggleFilterBtn = document.getElementById('toggleFilterBtn');
  
  // Initialize date range picker
  const startDateInput = document.getElementById('start_date');
  const endDateInput = document.getElementById('end_date');
  const dateRangeInput = document.getElementById('date_range');
  const clearDateRangeBtn = document.querySelector('.clear-date-range');
  
  // Set initial date range text if dates are present
  updateDateRangeText();
  
  // Initialize the date range picker
  $(dateRangeInput).daterangepicker({
    opens: 'left',
    autoUpdateInput: false,
    locale: {
      cancelLabel: @json(__('Clear')),
      applyLabel: @json(__('Apply')),
      format: 'YYYY-MM-DD'
    }
  });
  
  // Date range picker events
  $(dateRangeInput).on('apply.daterangepicker', function(ev, picker) {
    startDateInput.value = picker.startDate.format('YYYY-MM-DD');
    endDateInput.value = picker.endDate.format('YYYY-MM-DD');
    updateDateRangeText();
    updateFilterCounterAndButtonStates();
  });
  
  // Clear date range button
  clearDateRangeBtn.addEventListener('click', function(e) {
    e.preventDefault();
    startDateInput.value = '';
    endDateInput.value = '';
    dateRangeInput.value = '';
    updateFilterCounterAndButtonStates();
  });
  
  // Auto-submit filter form on select changes
  filterControls.forEach(control => {
    control.addEventListener('change', function() {
      updateFilterCounterAndButtonStates();
    });
  });
  
  // Clear all filters
  clearFiltersBtn.addEventListener('click', function() {
    window.location.href = '{{ route("admin.wallet-requests.reset-filters") }}';
  });
  
  // Reset filters button
  resetFiltersBtn.addEventListener('click', function() {
    filterControls.forEach(control => {
      control.value = '';
    });
    startDateInput.value = '';
    endDateInput.value = '';
    dateRangeInput.value = '';
    updateFilterCounterAndButtonStates();
    filterForm.submit();
  });
  
  // Update filter counter and button states on page load
  updateFilterCounterAndButtonStates();
  
  // Function to update date range text
  function updateDateRangeText() {
    if (startDateInput.value && endDateInput.value) {
      const startFormatted = moment(startDateInput.value).format('MMM D, YYYY');
      const endFormatted = moment(endDateInput.value).format('MMM D, YYYY');
      dateRangeInput.value = `${startFormatted} - ${endFormatted}`;
    } else {
      dateRangeInput.value = '';
    }
  }
  
  // Function to count active filters and update UI
  function updateFilterCounterAndButtonStates() {
    let activeFilterCount = 0;
    
    // Check all filter controls
    filterControls.forEach(control => {
      if (control.value) {
        activeFilterCount++;
        control.closest('.mb-3').classList.add('filter-highlight');
      } else {
        control.closest('.mb-3').classList.remove('filter-highlight');
      }
    });
    
    // Check date ranges
    if (startDateInput.value || endDateInput.value) {
      activeFilterCount++;
      dateRangeInput.closest('.mb-3').classList.add('filter-highlight');
    } else {
      dateRangeInput.closest('.mb-3').classList.remove('filter-highlight');
    }
    
    // Update counter and button visibility
    if (activeFilterCount > 0) {
      filterCounter.textContent = activeFilterCount;
      filterCounter.style.display = 'inline-block';
      clearFiltersBtn.style.display = 'inline-block';
    } else {
      filterCounter.style.display = 'none';
      clearFiltersBtn.style.display = 'none';
    }
    
    // Log for debugging
    console.log(`Active filters: ${activeFilterCount}`);
  }
  
  // Add debounced auto-submit for number inputs
  const debounce = (func, delay) => {
    let debounceTimer;
    return function() {
      const context = this;
      const args = arguments;
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(() => func.apply(context, args), delay);
    };
  };
  
  // Auto-submit after typing in number inputs (with debounce)
  const amountInputs = document.querySelectorAll('input[type="number"]');
  amountInputs.forEach(input => {
    input.addEventListener('input', debounce(function() {
      filterForm.submit();
    }, 800));
  });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var collapseEl = document.getElementById('filterCollapse');
  var bsCollapse = bootstrap.Collapse.getOrCreateInstance(collapseEl, {toggle: false});
  document.getElementById('toggleFilterBtn').addEventListener('click', function() {
    bsCollapse.toggle();
  });
});
</script>
@endpush 