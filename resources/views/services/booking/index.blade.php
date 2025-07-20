@extends('layouts.app')

@section('content')
<div class="container">
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
            <h3 class="mb-0">{{ __('My Service Bookings') }}</h3>
            <div>
                <a href="{{ route('services.booking.order.form') }}" class="btn btn-success me-2">
                    <i class="fa fa-shopping-cart"></i> {{ __('Order Multiple Services') }}
                </a>
                <a href="{{ route('services.booking.create') }}" class="btn btn-primary">
                    <i class="fa fa-plus"></i> {{ __('Book Single Service') }}
                </a>
                <a href="{{ route('services.booking.history') }}" class="btn btn-secondary ms-2">
                    <i class="fa fa-history"></i> {{ __('History') }}
                </a>
            </div>
        </div>

        <div class="card-body">
            @if($bookings->isEmpty())
                <div class="text-center py-4">
                    <h4>{{ __('No bookings found') }}</h4>
                    <p>{{ __('You haven\'t made any service bookings yet.') }}</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('Reference') }}</th>
                                <th>{{ __('Service') }}</th>
                                <th>{{ __('Vehicle') }}</th>
                                <th>{{ __('Schedule') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Created') }}</th>
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
                                    <td>
                                        {{ $booking->vehicle_make }} {{ $booking->vehicle_model }}<br>
                                        <small class="text-muted">{{ $booking->plate_number }}</small>
                                    </td>
                                    <td>
                                        @if($booking->booking_date)
                                            {{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}<br>
                                            <small class="text-muted">{{ $booking->booking_time ? \Carbon\Carbon::parse($booking->booking_time)->format('h:i A') : 'N/A' }}</small>
                                        @else
                                            <span class="text-muted">{{ __('Not scheduled') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $booking->status === 'pending' ? 'warning' : ($booking->status === 'confirmed' ? 'success' : 'danger') }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $booking->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('services.booking.show', $booking) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> {{ __('View') }}
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