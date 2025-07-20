@extends('layouts.app')

@section('title', __('Transfer RFID'))

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
            <h3 class="mb-0">{{ __('Transfer RFID') }}</h3>
            <a href="{{ route('rfid.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> {{ __('Back to RFID Management') }}
            </a>
        </div>
        <div class="card-body">
            @if($sourceVehicles->isEmpty())
                <div class="alert alert-warning">
                    <h5>{{ __('No vehicles with RFID available') }}</h5>
                    <p>{{ __('You don\'t have any vehicles with RFID chips. You can get an RFID chip by booking a service.') }}</p>
                    <a href="{{ route('services.booking.create') }}" class="btn btn-primary mt-2">{{ __('Book a Service') }}</a>
                </div>
            @elseif($targetVehicles->isEmpty())
                <div class="alert alert-warning">
                    <h5>{{ __('No available target vehicles') }}</h5>
                    <p>{{ __('You need to add at least one vehicle without an RFID chip to transfer to.') }}</p>
                    <a href="{{ route('vehicles.create') }}" class="btn btn-primary mt-2">{{ __('Add New Vehicle') }}</a>
                </div>
            @else
                <div class="alert alert-info">
                    <h5><i class="fa fa-info-circle"></i> {{ __('Important Information') }}</h5>
                    <ul>
                        <li>{{ __('RFID transfers require OTP verification for security.') }}</li>
                        <li>{{ __('The RFID balance will be transferred along with the RFID number.') }}</li>
                        <li>{{ __('After transfer, the source vehicle will no longer have an RFID chip.') }}</li>
                        <li>{{ __('Once initiated, you\'ll have 10 minutes to complete the verification.') }}</li>
                    </ul>
                </div>
                
                <form action="{{ route('rfid.initiate-transfer') }}" method="POST">
                    @csrf
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="source_vehicle_id">{{ __('Source Vehicle (with RFID)') }} <span class="text-danger">*</span></label>
                                <select class="form-control @error('source_vehicle_id') is-invalid @enderror" id="source_vehicle_id" name="source_vehicle_id" required>
                                    <option value="">{{ __('-- Select Source Vehicle --') }}</option>
                                    @foreach($sourceVehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" 
                                            {{ old('source_vehicle_id') == $vehicle->id || $selectedSourceId == $vehicle->id ? 'selected' : '' }}>
                                            {{ $vehicle->manufacturer }} {{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->plate_number }}) - {{ __('RFID') }}: {{ $vehicle->rfid_number }} - {{ __('Balance') }}: {{ $vehicle->formatted_rfid_balance }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('source_vehicle_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="target_vehicle_id">{{ __('Target Vehicle (without RFID)') }} <span class="text-danger">*</span></label>
                                <select class="form-control @error('target_vehicle_id') is-invalid @enderror" id="target_vehicle_id" name="target_vehicle_id" required>
                                    <option value="">{{ __('-- Select Target Vehicle --') }}</option>
                                    @foreach($targetVehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" {{ old('target_vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                            {{ $vehicle->manufacturer }} {{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->plate_number }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('target_vehicle_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label for="notes">{{ __('Notes (Optional)') }}</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-exchange-alt"></i> {{ __('Initiate Transfer') }}
                        </button>
                        <a href="{{ route('rfid.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection 