@extends('layouts.app')

@section('title', __('RFID Management'))

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

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ __('RFID Management') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('rfid.transfer') }}" class="btn btn-outline-primary">
                            <i class="fa fa-exchange-alt"></i> {{ __('Transfer RFID') }}
                        </a>
                        <a href="{{ route('rfid.recharge') }}" class="btn btn-outline-success">
                            <i class="fa fa-credit-card"></i> {{ __('Recharge RFID') }}
                        </a>
                        <a href="{{ route('rfid.transactions') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-history"></i> {{ __('Transaction History') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">{{ __('Quick Links') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('vehicles.index') }}" class="btn btn-outline-info">
                            <i class="fa fa-car"></i> {{ __('Manage Vehicles') }}
                        </a>
                        <a href="{{ route('services.booking.create') }}" class="btn btn-outline-dark">
                            <i class="fa fa-plus-circle"></i> {{ __('Book a Service') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vehicles with RFID -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ __('Vehicles with RFID') }}</h5>
        </div>
        <div class="card-body">
            @if($vehicles->isEmpty())
                <div class="alert alert-info">
                    {{ __('You don\'t have any vehicles with RFID chips. You can get an RFID chip by booking a service.') }}
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('Plate Number') }}</th>
                                <th>{{ __('Vehicle') }}</th>
                                <th>{{ __('RFID Number') }}</th>
                                <th>{{ __('Balance') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vehicles as $vehicle)
                                <tr>
                                    <td>{{ $vehicle->plate_number }}</td>
                                    <td>{{ $vehicle->manufacturer }} {{ $vehicle->make }} {{ $vehicle->model }}</td>
                                    <td>{{ $vehicle->rfid_number }}</td>
                                    <td>{{ $vehicle->formatted_rfid_balance }}</td>
                                    <td>{!! $vehicle->rfid_status_label !!}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('vehicles.show', $vehicle->id) }}" class="btn btn-sm btn-info">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('rfid.recharge') }}?vehicle={{ $vehicle->id }}" class="btn btn-sm btn-success">
                                                <i class="fa fa-credit-card"></i> {{ __('Recharge') }}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Pending RFID Transfers -->
    @if(!$pendingTransfers->isEmpty())
        <div class="card mb-4">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0">{{ __('Pending RFID Transfers') }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('From') }}</th>
                                <th>{{ __('To') }}</th>
                                <th>{{ __('RFID Number') }}</th>
                                <th>{{ __('Requested') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingTransfers as $transfer)
                                <tr>
                                    <td>{{ $transfer->sourceVehicle->plate_number }}</td>
                                    <td>{{ $transfer->targetVehicle->plate_number }}</td>
                                    <td>{{ $transfer->rfid_number }}</td>
                                    <td>{{ $transfer->created_at->diffForHumans() }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('rfid.verify-transfer', $transfer->id) }}" class="btn btn-sm btn-success">
                                                <i class="fa fa-check"></i> {{ __('Verify') }}
                                            </a>
                                            <form action="{{ route('rfid.cancel-transfer', $transfer->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Are you sure you want to cancel this transfer?') }}')">
                                                    <i class="fa fa-times"></i> {{ __('Cancel') }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection 