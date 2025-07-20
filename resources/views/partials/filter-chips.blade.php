@if(count(array_filter($filters ?? [])))
<div class="active-filters mb-3">
  <div class="d-flex align-items-center mb-2">
    <span class="text-muted small mr-2">{{ __('Active filters') }}:</span>
    <a href="{{ route($resetRoute) }}" class="text-sm text-primary">
      <small>{{ __('Clear all') }}</small>
    </a>
  </div>
  <div class="filter-chips">
    @if(isset($filters['status']) && $filters['status'])
      <div class="filter-chip">
        <span>{{ __('Status') }}: {{ __($filters['status'] === 'pending' ? 'Pending' : ($filters['status'] === 'approved' ? 'Approved' : 'Rejected')) }}</span>
        <a href="{{ route($indexRoute, array_merge($filters, ['status' => ''])) }}" class="close text-danger ml-1" aria-label="Remove">
          <i class="fas fa-times"></i>
        </a>
      </div>
    @endif

    @if(isset($filters['payment_type']) && $filters['payment_type'])
      <div class="filter-chip">
        <span>{{ __('Payment') }}: {{ __('payment_types.' . $filters['payment_type']) }}</span>
        <a href="{{ route($indexRoute, array_merge($filters, ['payment_type' => ''])) }}" class="close text-danger ml-1" aria-label="Remove">
          <i class="fas fa-times"></i>
        </a>
      </div>
    @endif

    @if(isset($filters['current_step']) && $filters['current_step'])
      <div class="filter-chip">
        <span>{{ __('Step') }}: {{ __('approval_steps.' . $filters['current_step']) }}</span>
        <a href="{{ route($indexRoute, array_merge($filters, ['current_step' => ''])) }}" class="close text-danger ml-1" aria-label="Remove">
          <i class="fas fa-times"></i>
        </a>
      </div>
    @endif

    @if(isset($filters['min_amount']) && $filters['min_amount'])
      <div class="filter-chip">
        <span>{{ __('Min Amount') }}: {{ __('SAR') }} {{ number_format($filters['min_amount'], 2) }}</span>
        <a href="{{ route($indexRoute, array_merge($filters, ['min_amount' => ''])) }}" class="close text-danger ml-1" aria-label="Remove">
          <i class="fas fa-times"></i>
        </a>
      </div>
    @endif

    @if(isset($filters['max_amount']) && $filters['max_amount'])
      <div class="filter-chip">
        <span>{{ __('Max Amount') }}: {{ __('SAR') }} {{ number_format($filters['max_amount'], 2) }}</span>
        <a href="{{ route($indexRoute, array_merge($filters, ['max_amount' => ''])) }}" class="close text-danger ml-1" aria-label="Remove">
          <i class="fas fa-times"></i>
        </a>
      </div>
    @endif

    @if(isset($filters['start_date']) && $filters['start_date'])
      <div class="filter-chip">
        <span>{{ __('From') }}: {{ \Carbon\Carbon::parse($filters['start_date'])->format('M d, Y') }}</span>
        <a href="{{ route($indexRoute, array_merge($filters, ['start_date' => ''])) }}" class="close text-danger ml-1" aria-label="Remove">
          <i class="fas fa-times"></i>
        </a>
      </div>
    @endif

    @if(isset($filters['end_date']) && $filters['end_date'])
      <div class="filter-chip">
        <span>{{ __('To') }}: {{ \Carbon\Carbon::parse($filters['end_date'])->format('M d, Y') }}</span>
        <a href="{{ route($indexRoute, array_merge($filters, ['end_date' => ''])) }}" class="close text-danger ml-1" aria-label="Remove">
          <i class="fas fa-times"></i>
        </a>
      </div>
    @endif
  </div>
</div>
@endif 