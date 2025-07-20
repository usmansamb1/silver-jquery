@extends('layouts.app')

@section('title', __('admin-system.log_details_page'))

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('admin-system.log_details_page') }}</h1>
        <a href="{{ route('admin.logs.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> {{ __('admin-system.back_to_logs') }}
        </a>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('admin-system.log_information') }}</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <h6 class="font-weight-bold">{{ __('admin-system.date_time') }}:</h6>
                        <p>{{ $log->created_at->format('Y-m-d H:i:s') }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="font-weight-bold">{{ __('admin-system.event_type') }}:</h6>
                        <p>
                            @if($log->event)
                                <span class="badge bg-info">{{ $log->event }}</span>
                            @else
                                <span class="badge bg-secondary">{{ __('admin-system.general') }}</span>
                            @endif
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="font-weight-bold">{{ __('admin-system.description') }}:</h6>
                        <p>{{ $log->description }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="font-weight-bold">{{ __('admin-system.user') }}:</h6>
                        <p>{{ $log->causer ? $log->causer->name : __('admin-system.system') }}</p>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <h6 class="font-weight-bold">{{ __('admin-system.level') }}:</h6>
                        <p>
                            <span class="badge bg-{{ $log->level == 'info' ? 'info' : 
                                                   ($log->level == 'warning' ? 'warning' : 
                                                   ($log->level == 'error' || $log->level == 'critical' ? 'danger' : 'secondary')) }}">
                                {{ __('admin-system.' . $log->level) }}
                            </span>
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="font-weight-bold">{{ __('admin-system.ip_address') }}:</h6>
                        <p>{{ $log->ip_address }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="font-weight-bold">{{ __('admin-system.user_agent') }}:</h6>
                        <p class="text-wrap">{{ $log->user_agent }}</p>
                    </div>
                </div>
            </div>
            
            @if($log->subject_type)
            <div class="row mt-4">
                <div class="col-12">
                    <h6 class="font-weight-bold">{{ __('admin-system.subject') }}:</h6>
                    <p>{{ class_basename($log->subject_type) }} (ID: {{ $log->subject_id }})</p>
                </div>
            </div>
            @endif
            
            @if($log->properties)
            <div class="row mt-4">
                <div class="col-12">
                    <h6 class="font-weight-bold">{{ __('admin-system.properties') }}:</h6>
                    <div class="card bg-light">
                        <div class="card-body">
                            <pre class="mb-0">{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection 