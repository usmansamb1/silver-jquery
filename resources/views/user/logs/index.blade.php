@extends('layouts.app')

@section('title', __('My Activity Logs'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h5 class="m-0 font-weight-bold text-primary">{{ __('My Activity History') }}</h5>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <form action="{{ route('user.logs.index') }}" method="get" class="row g-3">
                            <div class="col-md-4">
                                <label for="event" class="form-label">{{ __('Activity Type') }}</label>
                                <select name="event" id="event" class="form-select">
                                    <option value="">{{ __('All Activities') }}</option>
                                    @foreach($eventTypes as $eventType)
                                        <option value="{{ $eventType }}" {{ request()->event == $eventType ? 'selected' : '' }}>
                                            @if($eventType == 'login')
                                                {{ __('Login Activity') }}
                                            @elseif($eventType == 'wallet_recharge')
                                                {{ __('Wallet Recharge') }}
                                            @elseif($eventType == 'service_booking')
                                                {{ __('Service Booking') }}
                                            @elseif($eventType == 'profile_update')
                                                {{ __('Profile Update') }}
                                            @elseif($eventType == 'vehicle_created')
                                                {{ __('Vehicle Added') }}
                                            @elseif($eventType == 'vehicle_updated')
                                                {{ __('Vehicle Updated') }}
                                            @elseif($eventType == 'vehicle_deleted')
                                                {{ __('Vehicle Deleted') }}
                                            @elseif($eventType == 'rfid_transfer_initiated')
                                                {{ __('RFID Transfer Initiated') }}
                                            @elseif($eventType == 'rfid_transfer_completed')
                                                {{ __('RFID Transfer Completed') }}
                                            @elseif($eventType == 'rfid_transfer_cancelled')
                                                {{ __('RFID Transfer Cancelled') }}
                                            @elseif($eventType == 'rfid_recharge')
                                                {{ __('RFID Recharged') }}
                                            @else
                                                {{ ucfirst(str_replace('_', ' ', $eventType)) }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label for="date_from" class="form-label">{{ __('From Date') }}</label>
                                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request()->date_from }}">
                            </div>
                            
                            <div class="col-md-3">
                                <label for="date_to" class="form-label">{{ __('To Date') }}</label>
                                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request()->date_to }}">
                            </div>
                            
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-filter"></i> {{ __('Filter') }}
                                </button>
                                <a href="{{ route('user.logs.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-sync"></i> {{ __('Reset') }}
                                </a>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Activity Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('Date & Time') }}</th>
                                    <th>{{ __('Activity') }}</th>
                                    <th>{{ __('Description') }}</th>
                                    <th>{{ __('Details') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td>
                                        @if($log->event == 'login')
                                            <span class="badge bg-primary">{{ __('Login Activity') }}</span>
                                        @elseif($log->event == 'wallet_recharge')
                                            <span class="badge bg-success">{{ __('Wallet Recharge') }}</span>
                                        @elseif($log->event == 'service_booking')
                                            <span class="badge bg-info">{{ __('Service Booking') }}</span>
                                        @elseif($log->event == 'profile_update')
                                            <span class="badge bg-warning">{{ __('Profile Update') }}</span>
                                        @elseif($log->event == 'vehicle_created')
                                            <span class="badge bg-success">{{ __('Vehicle Added') }}</span>
                                        @elseif($log->event == 'vehicle_updated')
                                            <span class="badge bg-warning">{{ __('Vehicle Updated') }}</span>
                                        @elseif($log->event == 'vehicle_deleted')
                                            <span class="badge bg-danger">{{ __('Vehicle Deleted') }}</span>
                                        @elseif($log->event == 'rfid_transfer_initiated')
                                            <span class="badge bg-info">{{ __('RFID Transfer Initiated') }}</span>
                                        @elseif($log->event == 'rfid_transfer_completed')
                                            <span class="badge bg-success">{{ __('RFID Transfer Completed') }}</span>
                                        @elseif($log->event == 'rfid_transfer_cancelled')
                                            <span class="badge bg-danger">{{ __('RFID Transfer Cancelled') }}</span>
                                        @elseif($log->event == 'rfid_recharge')
                                            <span class="badge bg-success">{{ __('RFID Recharged') }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $log->event)) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $log->description }}
                                        @if($log->event == 'wallet_recharge' && isset($log->properties['card_brand']))
                                            <br><small class="text-muted">
                                                <i class="fas fa-credit-card"></i> 
                                                {{ $log->properties['card_brand'] }}
                                                @if($log->properties['card_brand'] === 'MADA')
                                                    (مدى)
                                                @endif
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('user.logs.show', $log->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> {{ __('View Details') }}
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">{{ __('No activity logs found') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize any JavaScript functionality here if needed
    });
</script>
@endpush 