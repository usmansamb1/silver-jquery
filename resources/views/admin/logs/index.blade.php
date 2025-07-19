@extends('layouts.app')

@section('title', __('admin-system.system_logs'))

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('admin-system.system_logs') }}</h1>
    </div>
    
    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('admin-system.filters') }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.logs.index') }}" method="get" class="row g-3">
                <div class="col-md-3">
                    <label for="event" class="form-label">{{ __('admin-system.log_level') }}</label>
                    <select name="event" id="event" class="form-select">
                        <option value="">{{ __('admin-system.all_events') }}</option>
                        @foreach($eventTypes as $eventType)
                            <option value="{{ $eventType }}" {{ request()->event == $eventType ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $eventType)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="level" class="form-label">{{ __('admin-system.log_level') }}</label>
                    <select name="level" id="level" class="form-select">
                        <option value="">{{ __('admin-system.all_levels') }}</option>
                        @foreach($levels as $level)
                            <option value="{{ $level }}" {{ request()->level == $level ? 'selected' : '' }}>
                                {{ strtoupper($level) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="date_from" class="form-label">{{ __('admin-dashboard.filters.from_date') }}</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request()->date_from }}">
                </div>
                
                <div class="col-md-3">
                    <label for="date_to" class="form-label">{{ __('admin-dashboard.filters.to_date') }}</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request()->date_to }}">
                </div>
                
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> {{ __('admin-dashboard.filters.apply_filters') }}
                    </button>
                    <a href="{{ route('admin.logs.index') }}" class="btn btn-secondary">
                        <i class="fas fa-sync"></i> {{ __('admin-dashboard.filters.reset_filters') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Logs Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('admin-system.system_logs') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{{ __('admin-system.log_date') }}</th>
                            <th>{{ __('admin-system.log_level') }}</th>
                            <th>{{ __('admin-system.log_message') }}</th>
                            <th>{{ __('admin-dashboard.navigation.users') }}</th>
                            <th>{{ __('admin-system.log_level') }}</th>
                            <th>{{ __('admin-system.ip_address') }}</th>
                            <th>{{ __('admin-dashboard.tables.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td>
                                @if($log->event)
                                    <span class="badge bg-info">{{ $log->event }}</span>
                                @else
                                    <span class="badge bg-secondary">general</span>
                                @endif
                            </td>
                            <td>{{ Str::limit($log->description, 50) }}</td>
                            <td>{{ $log->causer ? $log->causer->name : 'System' }}</td>
                            <td>
                                <span class="badge bg-{{ $log->level == 'info' ? 'info' : 
                                                    ($log->level == 'warning' ? 'warning' : 
                                                    ($log->level == 'error' || $log->level == 'critical' ? 'danger' : 'secondary')) }}">
                                    {{ strtoupper($log->level) }}
                                </span>
                            </td>
                            <td>{{ $log->ip_address }}</td>
                            <td>
                                <a href="{{ route('admin.logs.show', $log->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> {{ __('admin-dashboard.tables.view') }}
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">{{ __('admin-dashboard.tables.no_data') }}</td>
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
@endsection

@push('scripts')
<script>
    // Initialize datepickers if needed
    $(document).ready(function() {
        // Additional JavaScript if needed
    });
</script>
@endpush 