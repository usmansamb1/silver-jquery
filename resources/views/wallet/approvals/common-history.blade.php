@extends('layouts.app')

@section('title', __('Approval History'))

@section('content')
<div class="container py-4">
  <h3>{{ __('Wallet Topup Request History') }}</h3>
  <!-- Filter Form -->
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('wallet-approvals.history') }}" class="row g-3 align-items-end">
        <div class="col-md-2">
          <label for="date_from" class="form-label">{{ __('From') }}</label>
          <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="form-control">
        </div>
        <div class="col-md-2">
          <label for="date_to" class="form-label">{{ __('To') }}</label>
          <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="form-control">
        </div>
        <div class="col-md-2">
          <label for="status" class="form-label">{{ __('Status') }}</label>
          <select name="status" id="status" class="form-select">
            <option value="">{{ __('All') }}</option>
            <option value="pending"{{ request('status')=='pending'?' selected':'' }}>{{ __('Pending') }}</option>
            <option value="approved"{{ request('status')=='approved'?' selected':'' }}>{{ __('Approved') }}</option>
            <option value="rejected"{{ request('status')=='rejected'?' selected':'' }}>{{ __('Rejected') }}</option>
          </select>
        </div>
        <div class="col-md-2">
          <label for="type" class="form-label">{{ __('Type') }}</label>
          <select name="type" id="type" class="form-select">
            <option value="">{{ __('All') }}</option>
            <option value="bank_transfer"{{ request('type')=='bank_transfer'?' selected':'' }}>{{ __('Bank Transfer') }}</option>
            <option value="bank_lc"{{ request('type')=='bank_lc'?' selected':'' }}>{{ __('Bank LC') }}</option>
            <option value="bank_guarantee"{{ request('type')=='bank_guarantee'?' selected':'' }}>{{ __('Bank Guarantee') }}</option>
          </select>
        </div>
        <div class="col-md-3">
          <label for="email" class="form-label">{{ __('Customer Email') }}</label>
          <input type="email" name="email" id="email" value="{{ request('email') }}" class="form-control" placeholder="name@example.com">
        </div>
        <div class="col-md-1">
          <button type="submit" class="btn btn-primary w-100">
            <i class="fas fa-filter me-1"></i>{{ __('Filter') }}
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Results Table -->
  <div class="card">
    <div class="card-header bg-white">
      <h5 class="mb-0">{{ __('Approval History') }}</h5>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>{{ __('Request ID') }}</th>
              <th>{{ __('Customer') }}</th>
              <th>{{ __('Amount') }}</th>
              <th>{{ __('Method') }}</th>
              <th>{{ __('Status') }}</th>
              <th>{{ __('My Role') }}</th>
              <th>{{ __('Decision') }}</th>
              <th>{{ __('Date') }}</th>
              <th>{{ __('Action') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse($steps as $step)
            <tr>
              <td>{{ $step->request->id }}</td>
              <td>
                {{ $step->request->payment->user->name }}<br>
                <small class="text-muted">{{ $step->request->payment->user->email }}</small>
              </td>
              <td>SAR {{ number_format($step->request->payment->amount, 2) }}</td>
              <td>{{ ucfirst(str_replace('_', ' ', $step->request->payment->payment_type)) }}</td>
              <td>
                <span class="badge bg-{{ $step->status=='approved'?'success':($step->status=='rejected'?'danger':'warning') }}">
                  {{ ucfirst($step->status) }}
                </span>
              </td>
              <td>{{ ucfirst($step->role) }}</td>
              <td>
                @if($step->status=='approved')
                  <i class="fas fa-check-circle text-success"></i>
                @elseif($step->status=='rejected')
                  <i class="fas fa-times-circle text-danger"></i>
                @else
                  <i class="fas fa-clock text-warning"></i>
                @endif
              </td>
              <td>{{ $step->created_at->format('d M Y, h:i A') }}</td>
              <td>
                <a href="{{ route('wallet.approvals.show', $step->request->id) }}" class="btn btn-sm btn-outline-primary">
                  <i class="fas fa-eye"></i>
                </a>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="9" class="text-center py-4">{{ __('No approvals found.') }}</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @if($steps->hasPages())
    <div class="card-footer">
      {{ $steps->links() }}
    </div>
    @endif
  </div>
</div>
@endsection 