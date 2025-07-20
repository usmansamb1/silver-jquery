@extends('layouts.app')

@section('title', 'Verify RFID Transfer')

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

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Verify RFID Transfer</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <p><strong>Transfer Details:</strong></p>
                        <p>From: {{ $transfer->sourceVehicle->manufacturer }} {{ $transfer->sourceVehicle->make }} {{ $transfer->sourceVehicle->model }} ({{ $transfer->sourceVehicle->plate_number }})</p>
                        <p>To: {{ $transfer->targetVehicle->manufacturer }} {{ $transfer->targetVehicle->make }} {{ $transfer->targetVehicle->model }} ({{ $transfer->targetVehicle->plate_number }})</p>
                        <p>RFID Number: {{ $transfer->rfid_number }}</p>
                        <p>OTP expires at: {{ $transfer->otp_expires_at->format('M d, Y h:i A') }}</p>
                    </div>
                    
                    <form action="{{ route('rfid.verify-transfer.submit', $transfer->id) }}" method="POST">
                        @csrf
                        
                        <div class="form-group mb-4">
                            <label for="otp_code">Enter OTP Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg text-center @error('otp_code') is-invalid @enderror" 
                                id="otp_code" name="otp_code" value="{{ old('otp_code') }}" 
                                placeholder="Enter 6-digit OTP"
                                maxlength="6" autocomplete="off" required>
                            <div class="form-text">
                                An OTP has been sent to your registered email and phone number. Enter the 6-digit code to verify the transfer.
                            </div>
                            @error('otp_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fa fa-check-circle"></i> Verify Transfer
                            </button>
                        </div>
                    </form>
                    
                    <div class="mt-4 text-center">
                        <form action="{{ route('rfid.cancel-transfer', $transfer->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to cancel this transfer?')">
                                <i class="fa fa-times-circle"></i> Cancel Transfer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 