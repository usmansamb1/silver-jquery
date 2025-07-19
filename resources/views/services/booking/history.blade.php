@extends('layouts.app')

@section('title', __('Booking History'))

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

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="mb-0">{{ __('Booking History') }}</h3>
            <div>
                <a href="{{ route('services.booking.index') }}" class="btn btn-primary">
                    <i class="fa fa-list"></i> {{ __('Active Bookings') }}
                </a>
                <a href="{{ route('services.booking.create') }}" class="btn btn-success">
                    <i class="fa fa-plus"></i> {{ __('Book New Service') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($bookings->isEmpty())
                <div class="alert alert-info">
                    {{ __('You don\'t have any booking history yet.') }}
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('Reference') }}</th>
                                <th>{{ __('Service') }}</th>
                                <th>{{ __('Vehicle') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Payment') }}</th>
                                <th>{{ __('Payment Type') }}</th>
                                <th>{{ __('RFID Status') }}</th>
                                <th>{{ __('RFID Balance') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                                <tr>
                                    <td>{{ $booking->reference_number }}</td>
                                    <td>
                                        @if($booking->service)
                                            {{ $booking->service->name }}
                                        @elseif($booking->service_type)
                                            {{ App\Models\Service::getServiceTypeById($booking->service_type) }}
                                        @else
                                            <span class="text-muted">{{ __('Unknown Service') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $booking->vehicle_make }} {{ $booking->vehicle_model }}</td>
                                    <td>
                                        @if($booking->status == 'pending')
                                            <span class="badge bg-warning">{{ __('Pending') }}</span>
                                        @elseif($booking->status == 'confirmed')
                                            <span class="badge bg-success">{{ __('Confirmed') }}</span>
                                        @elseif($booking->status == 'completed')
                                            <span class="badge bg-info">{{ __('Completed') }}</span>
                                        @elseif($booking->status == 'cancelled')
                                            <span class="badge bg-danger">{{ __('Cancelled') }}</span>
                                        @elseif($booking->status == 'approved')
                                            <span class="badge bg-primary">{{ __('Approved') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($booking->payment_status == 'paid' || $booking->payment_status == 'approved')
                                            <span class="badge bg-success">{{ __('Paid') }}</span>
                                        @elseif($booking->payment_status == 'pending')
                                            <span class="badge bg-warning">{{ __('Pending') }}</span>
                                        @elseif($booking->payment_status == 'refunded')
                                            <span class="badge bg-info">{{ __('Refunded') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($booking->payment_method == 'wallet')
                                            <span class="badge bg-info">{{ __('Wallet') }}</span>
                                        @elseif($booking->payment_method == 'credit_card')
                                            <span class="badge bg-primary">{{ __('Credit') }}</span>
                                        @elseif($booking->payment_method == 'mada_card')
                                            <span class="badge bg-success">{{ __('Mada') }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ __('N/A') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($booking->status == 'confirmed' || $booking->payment_status == 'paid')
                                            @if($booking->delivery_status == 'pending' || $booking->delivery_status == null)
                                                <span class="badge bg-warning">{{ __('Pending') }}</span>
                                            @else
                                                <span class="badge bg-success">{{ __('Delivered') }}</span>
                                                @if($booking->rfid_number)
                                                    <br><small>{{ __('RFID') }}: {{ $booking->rfid_number }}</small>
                                                @endif
                                            @endif
                                        @elseif($booking->vehicle && $booking->vehicle->rfid_status)
                                            @if($booking->vehicle->rfid_status == 'pending')
                                                <span class="badge bg-warning">{{ __('Pending') }}</span>
                                            @elseif($booking->vehicle->rfid_status == 'active')
                                                <span class="badge bg-success">{{ __('Active') }}</span>
                                                @if($booking->vehicle->rfid_number)
                                                    <br><small>{{ __('RFID') }}: {{ $booking->vehicle->rfid_number }}</small>
                                                @endif
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">{{ __('N/A') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($booking->refule_amount > 0)
                                            {{ number_format($booking->refule_amount, 2) }} SAR
                                        @else
                                            <span class="text-muted">{{ __('N/A') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $booking->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('services.booking.show', $booking->id) }}" class="btn btn-sm btn-info">
                                            <i class="fa fa-eye"></i> {{ __('View') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $bookings->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 