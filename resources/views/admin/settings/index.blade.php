@extends('layouts.admin')

@section('title', __('admin-system.system_settings'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('admin-system.system_settings') }}</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.index') }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="site_name">{{ __('admin-system.general_settings') }}</label>
                            <input type="text" class="form-control" id="site_name" name="site_name" 
                                value="{{ $settings['site_name'] ?? old('site_name') ?? config('app.name') }}">
                        </div>
                        
                        <div class="form-group">
                            <label for="site_description">{{ __('admin-system.system_configuration') }}</label>
                            <textarea class="form-control" id="site_description" name="site_description" rows="3">{{ $settings['site_description'] ?? old('site_description') ?? '' }}</textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="contact_email">{{ __('admin-system.from_email') }}</label>
                            <input type="email" class="form-control" id="contact_email" name="contact_email" 
                                value="{{ $settings['contact_email'] ?? old('contact_email') ?? '' }}">
                        </div>
                        
                        <div class="form-group">
                            <label for="tax_rate">{{ __('admin-system.advanced_settings') }}</label>
                            <input type="number" class="form-control" id="tax_rate" name="tax_rate" step="0.01" 
                                value="{{ $settings['tax_rate'] ?? old('tax_rate') ?? '0' }}">
                        </div>
                        
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="maintenance_mode" name="maintenance_mode" 
                                value="1" {{ ($settings['maintenance_mode'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="maintenance_mode">{{ __('admin-system.maintenance_mode') }}</label>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">{{ __('admin-system.save_settings') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 