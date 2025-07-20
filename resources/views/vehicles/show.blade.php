@extends('layouts.app')

@section('title', 'Vehicle Details')

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

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Vehicle Details</h3>
                    <a href="{{ route('vehicles.index') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Back to Vehicles
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Plate Number:</strong> {{ $vehicle->plate_number }}</p>
                            <p><strong>Manufacturer:</strong> {{ $vehicle->manufacturer }}</p>
                            <p><strong>Make:</strong> {{ $vehicle->make }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Model:</strong> {{ $vehicle->model }}</p>
                            <p><strong>Year:</strong> {{ $vehicle->year }}</p>
                            <p><strong>Added:</strong> {{ $vehicle->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('vehicles.edit', $vehicle->id) }}" class="btn btn-primary">
                            <i class="fa fa-edit"></i> Edit Vehicle
                        </a>
                        
                        @if($vehicle->serviceBookings->count() == 0 && !$vehicle->hasRfid())
                            <form action="{{ route('vehicles.destroy', $vehicle->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this vehicle?')">
                                    <i class="fa fa-trash"></i> Delete Vehicle
                                </button>
                            </form>
                        @elseif($vehicle->hasRfid())
                            <a href="{{ route('rfid.recharge') }}?vehicle={{ $vehicle->id }}" class="btn btn-success">
                                <i class="fa fa-credit-card"></i> Recharge RFID
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- RFID Information Card -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-{{ $vehicle->hasRfid() ? 'success' : 'light' }}">
                    <h5 class="mb-0 {{ $vehicle->hasRfid() ? 'text-white' : '' }}">
                        <i class="fa fa-id-card"></i> RFID Information
                    </h5>
                </div>
                <div class="card-body">
                    @if($vehicle->hasRfid())
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>RFID Number:</strong> {{ $vehicle->rfid_number }}</p>
                                <p><strong>RFID Balance:</strong> {{ $vehicle->formatted_rfid_balance }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Status:</strong> {!! $vehicle->rfid_status_label !!}</p>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <a href="{{ route('rfid.recharge') }}?vehicle={{ $vehicle->id }}" class="btn btn-success">
                                <i class="fa fa-credit-card"></i> Recharge RFID
                            </a>
                            <a href="{{ route('rfid.transfer') }}" class="btn btn-primary">
                                <i class="fa fa-exchange-alt"></i> Transfer RFID
                            </a>
                            <a href="{{ route('rfid.transactions') }}" class="btn btn-info">
                                <i class="fa fa-history"></i> Transaction History
                            </a>
                        </div>
                        
                        @if(!empty($rfidTransactions) && $rfidTransactions->count() > 0)
                            <div class="mt-4">
                                <h6>Recent Transactions</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Amount</th>
                                                <th>Payment</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($rfidTransactions as $transaction)
                                                <tr>
                                                    <td>{{ $transaction->created_at->format('M d, Y') }}</td>
                                                    <td>{{ $transaction->formatted_amount }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $transaction->payment_method == 'wallet' ? 'primary' : 'success' }}">
                                                            {{ ucfirst($transaction->payment_method) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $transaction->status == 'completed' ? 'success' : 'warning' }}">
                                                            {{ ucfirst($transaction->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="mt-2 text-end">
                                    <a href="{{ route('rfid.transactions') }}" class="btn btn-sm btn-outline-secondary">
                                        View All Transactions
                                    </a>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info">
                            <p><i class="fa fa-info-circle"></i> This vehicle doesn't have an RFID chip assigned.</p>
                            <p>You can get an RFID chip by booking one of our RFID services. Alternatively, you can transfer an RFID from another vehicle if you already have one.</p>
                            <div class="mt-3">
                                <a href="{{ route('services.booking.create') }}" class="btn btn-primary">
                                    <i class="fa fa-plus-circle"></i> Book RFID Service
                                </a>
                                <a href="{{ route('rfid.transfer') }}" class="btn btn-secondary">
                                    <i class="fa fa-exchange-alt"></i> Transfer Existing RFID
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Service History</h4>
                </div>
                <div class="card-body">
                    @if($serviceBookings->isEmpty())
                        <div class="alert alert-info">
                            No service bookings found for this vehicle.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Reference</th>
                                        <th>Service</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($serviceBookings as $booking)
                                        <tr>
                                            <td>{{ $booking->reference_number }}</td>
                                            <td>{{ $booking->service->name ?? 'N/A' }}</td>
                                            <td>{{ $booking->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $booking->status == 'completed' ? 'success' : ($booking->status == 'pending' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($booking->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('services.booking.show', $booking->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fa fa-eye"></i> View
                                                </a>
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
    </div>
</div>
@endsection 