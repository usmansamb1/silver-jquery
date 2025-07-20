@extends('layouts.app')

@section('title', __('Add New Vehicle'))

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="mb-0">{{ __('Add New Vehicle') }}</h3>
            <a href="{{ route('vehicles.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> {{ __('Back to Vehicles') }}
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('vehicles.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="plate_number">{{ __('Plate Number') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('plate_number') is-invalid @enderror" 
                                id="plate_number" name="plate_number" value="{{ old('plate_number') }}" required>
                            @error('plate_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="year">{{ __('Year') }} <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('year') is-invalid @enderror" 
                                id="year" name="year" value="{{ old('year', date('Y')) }}" min="1900" max="{{ date('Y') + 1 }}" required>
                            @error('year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="manufacturer">{{ __('Manufacturer') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('manufacturer') is-invalid @enderror" 
                                id="manufacturer" name="manufacturer" value="{{ old('manufacturer') }}" required>
                            @error('manufacturer')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="make">{{ __('Make') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('make') is-invalid @enderror" 
                                id="make" name="make" value="{{ old('make') }}" required>
                            @error('make')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="model">{{ __('Model') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('model') is-invalid @enderror" 
                                id="model" name="model" value="{{ old('model') }}" required>
                            @error('model')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> {{ __('Save Vehicle') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 