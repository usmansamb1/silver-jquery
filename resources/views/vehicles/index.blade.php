@extends('layouts.app')

@section('title', __('My Vehicles'))

@section('content')
<div class="container-fluid py-4">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <h4 class="mb-0">{{ __('Vehicle Summary') }}</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-2 col-sm-4 mb-3">
                    <div class="card bg-light">
                        <div class="card-body py-2 text-center">
                            <h3 class="mb-0">{{ $statusCounts['all'] }}</h3>
                            <div class="small text-muted">{{ __('Total Vehicles') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 mb-3">
                    <div class="card bg-success text-white">
                        <div class="card-body py-2 text-center">
                            <h3 class="mb-0">{{ $statusCounts['active'] }}</h3>
                            <div class="small">{{ __('Active') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 mb-3">
                    <div class="card bg-info text-white">
                        <div class="card-body py-2 text-center">
                            <h3 class="mb-0">{{ $statusCounts['pending_delivery'] }}</h3>
                            <div class="small">{{ __('Pending Delivery') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 mb-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body py-2 text-center">
                            <h3 class="mb-0">{{ $statusCounts['delivered'] }}</h3>
                            <div class="small">{{ __('Delivered') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 mb-3">
                    <div class="card bg-secondary text-white">
                        <div class="card-body py-2 text-center">
                            <h3 class="mb-0">{{ $statusCounts['with_rfid'] }}</h3>
                            <div class="small">{{ __('With RFID') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 mb-3">
                    <div class="card bg-light">
                        <div class="card-body py-2 text-center">
                            <h3 class="mb-0">{{ $statusCounts['without_rfid'] }}</h3>
                            <div class="small text-muted">{{ __('Without RFID') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="mb-0">{{ __('My Vehicles') }}</h3>
            <div class="d-flex">
                <a href="{{ route('rfid.index') }}" class="btn btn-info me-2">
                    <i class="fa fa-id-card"></i> {{ __('RFID Management') }}
                </a>
                <a href="{{ route('vehicles.create') }}" class="btn btn-success">
                    <i class="fa fa-plus"></i> {{ __('Add New Vehicle') }}
                </a>
            </div>
        </div>
        
        <div class="card-body">
            <div class="mb-4">
                <form action="{{ route('vehicles.index') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="delivery_status" class="form-label">{{ __('Delivery Status') }}</label>
                        <select name="delivery_status" id="delivery_status" class="form-select">
                            <option value="all" {{ request('delivery_status') == 'all' ? 'selected' : '' }}>{{ __('All') }}</option>
                            <option value="active" {{ request('delivery_status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                            <option value="pending_delivery" {{ request('delivery_status') == 'pending_delivery' ? 'selected' : '' }}>{{ __('Pending Delivery') }}</option>
                            <option value="delivered" {{ request('delivery_status') == 'delivered' ? 'selected' : '' }}>{{ __('Delivered') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="rfid_status" class="form-label">{{ __('RFID Status') }}</label>
                        <select name="rfid_status" id="rfid_status" class="form-select">
                            <option value="all" {{ request('rfid_status') == 'all' ? 'selected' : '' }}>{{ __('All') }}</option>
                            <option value="with_rfid" {{ request('rfid_status') == 'with_rfid' ? 'selected' : '' }}>{{ __('With RFID') }}</option>
                            <option value="without_rfid" {{ request('rfid_status') == 'without_rfid' ? 'selected' : '' }}>{{ __('Without RFID') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">{{ __('Filter') }}</button>
                        <a href="{{ route('vehicles.index') }}" class="btn btn-secondary">{{ __('Reset') }}</a>
                    </div>
                </form>
            </div>
            
            @if($vehicles->isEmpty())
                <div class="alert alert-info">
                    {{ __('You don\'t have any vehicles yet. Add your first vehicle to easily link it to service bookings.') }}
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('Plate Number') }}</th>
                                <th>{{ __('Make') }}</th>
                                <th>{{ __('Model') }}</th>
                                <th>{{ __('Year') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('RFID Status') }}</th>
                                <th>{{ __('RFID Balance') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vehicles as $vehicle)
                                <tr>
                                    <td>{{ $vehicle->plate_number }}</td>
                                    <td>{{ $vehicle->manufacturer }} {{ $vehicle->make }}</td>
                                    <td>{{ $vehicle->model }}</td>
                                    <td>{{ $vehicle->year }}</td>
                                    <td>{!! $vehicle->status_label !!}</td>
                                    <td>
                                        @if($vehicle->hasRfid())
                                            {!! $vehicle->rfid_status_label !!}
                                        @else
                                            <span class="badge bg-light text-dark">{{ __('No RFID') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($vehicle->hasRfid())
                                            {{ $vehicle->formatted_rfid_balance }}
                                            <a href="{{ route('rfid.recharge') }}?vehicle={{ $vehicle->id }}" class="btn btn-sm btn-outline-success px-2 py-0">
                                                <i class="fa fa-plus-circle"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">{{ __('N/A') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('vehicles.show', $vehicle->id) }}" class="btn btn-sm btn-info">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('vehicles.edit', $vehicle->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            @if($vehicle->serviceBookings->count() == 0 && !$vehicle->hasRfid())
                                                <form action="{{ route('vehicles.destroy', $vehicle->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Are you sure you want to delete this vehicle?') }}')">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                        
                                        @if($vehicle->hasRfid())
                                            <div class="mt-1">
                                                <a href="{{ route('rfid.transfer') }}?source={{ $vehicle->id }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fa fa-exchange-alt"></i> {{ __('Transfer RFID') }}
                                                </a>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-submit the form when a filter changes
    $('#delivery_status, #rfid_status').change(function() {
        $(this).closest('form').submit();
    });
});
</script>
@endpush 