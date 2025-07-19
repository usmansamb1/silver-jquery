@extends('layouts.app')

@section('title', __('Profile'))

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Profile Header -->
            <div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="bg-primary bg-gradient text-white py-5 px-4 position-relative" style="min-height: 180px;">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="position-relative">
                                    <img src="{{ $user->avatar ? Storage::url($user->avatar) : asset('images/default-avatar.png') }}"
                                         class="rounded-circle border border-4 border-white shadow"
                                         style="width: 150px; height: 150px; object-fit: cover;"
                                         alt="Profile Picture">
                                </div>
                            </div>
                            <div class="col ps-md-4">
                                <h2 class="display-6 fw-bold mb-1">{{ $user->name ?? $user->company_name }}</h2>
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-light text-primary fs-6 me-2">
                                        {{ $user->roles->pluck('name')->implode(', ') }}
                                    </span>
                                    @if($user->formatted_customer_no)
                                    <span class="badge bg-light text-primary fs-6">
                                        Customer #{{ $user->formatted_customer_no }}
                                    </span>
                                    @endif
                                </div>
                                <p class="mb-0">
                                    <i class="fas fa-envelope me-2"></i> {{ $user->email }}
                                    <span class="ms-3">
                                        <i class="fas fa-phone me-2"></i> {{ $user->mobile }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('profile.edit') }}" class="btn btn-light">
                                    <i class="fas fa-edit"></i> {{ __('Edit Profile') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <!-- Personal Information Card -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-0 rounded-4 shadow-sm">
                        <div class="card-header bg-white border-0 pt-4 pb-2">
                            <div class="d-flex align-items-center">
                                <div class="icon-box bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                    <i class="fas fa-user text-primary"></i>
                                </div>
                                <h4 class="card-title mb-0">{{ __('Personal Information') }}</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item px-0 py-3 d-flex border-0 border-bottom">
                                    <span class="text-muted me-2 w-25">{{ __('Mobile') }}:</span>
                                    <span class="fw-medium">{{ $user->mobile }}</span>
                                </li>
                                <li class="list-group-item px-0 py-3 d-flex border-0 border-bottom">
                                    <span class="text-muted me-2 w-25">{{ __('Email') }}:</span>
                                    <span class="fw-medium">{{ $user->email }}</span>
                                </li>
                                @if($user->registration_type === 'personal')
                                <li class="list-group-item px-0 py-3 d-flex border-0 border-bottom">
                                    <span class="text-muted me-2 w-25">{{ __('Gender') }}:</span>
                                    <span class="fw-medium">{{ ucfirst($user->gender ?? __('Not specified')) }}</span>
                                </li>
                                <li class="list-group-item px-0 py-3 d-flex border-0 border-bottom">
                                    <span class="text-muted me-2 w-25">{{ __('Region') }}:</span>
                                    <span class="fw-medium">{{ $user->region }}</span>
                                </li>
                                @endif
                                <li class="list-group-item px-0 py-3 d-flex border-0 border-bottom">
                                    <span class="text-muted me-2 w-25">{{ __('Customer No') }}:</span>
                                    <span class="fw-medium">{{ $user->formatted_customer_no ?? 'N/A' }}</span>
                                </li>
                                <li class="list-group-item px-0 py-3 d-flex border-0">
                                    <span class="text-muted me-2 w-25">{{ __('Status') }}:</span>
                                    <span class="fw-medium d-flex align-items-center">
                                        {!! $statusBadge !!}
                                        <a href="{{ route('profile.status-history') }}" class="ms-2 small">
                                            <i class="fas fa-history"></i> {{ __('View History') }}
                                        </a>
                                    </span>
                                </li>
                                <li class="list-group-item px-0 py-3 d-flex border-0">
                                    <span class="text-muted me-2 w-25">{{ __('Terms & Conditions') }}:</span>
                                    <span class="fw-medium">
                                        @if($user->terms_accepted_at)
                                            <span class="text-success d-flex align-items-center">
                                                <i class="fas fa-check-circle me-1"></i>
                                                {{ __('Accepted') }} {{ $user->terms_accepted_at->format('M j, Y g:i A') }}
                                            </span>
                                        @else
                                            <span class="text-danger d-flex align-items-center">
                                                <i class="fas fa-times-circle me-1"></i>
                                                {{ __('Not Accepted') }}
                                            </span>
                                        @endif
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Company Details Card -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-0 rounded-4 shadow-sm">
                        <div class="card-header bg-white border-0 pt-4 pb-2">
                            <div class="d-flex align-items-center">
                                <div class="icon-box bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                    @if(!$user->hasRole('customer'))
                                    <i class="fas fa-clock text-primary"></i>
                                    @else
                                    <i class="fas fa-building text-primary"></i>
                                    @endif
                                </div>
                                <h4 class="card-title mb-0">{{ __('Company Details') }}</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($user->registration_type === 'company')
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item px-0 py-3 d-flex border-0 border-bottom">
                                    <span class="text-muted me-2 w-25">{{ __('Company') }}:</span>
                                    <span class="fw-medium">{{ $user->company_name }}</span>
                                </li>
                                <li class="list-group-item px-0 py-3 d-flex border-0 border-bottom">
                                    <span class="text-muted me-2 w-25">{{ __('Type') }}:</span>
                                    <span class="fw-medium">{{ $user->company_type }}</span>
                                </li>
                                <li class="list-group-item px-0 py-3 d-flex border-0 border-bottom">
                                    <span class="text-muted me-2 w-25">{{ __('CR Number') }}:</span>
                                    <span class="fw-medium">{{ $user->cr_number }}</span>
                                </li>
                                <li class="list-group-item px-0 py-3 d-flex border-0 border-bottom">
                                    <span class="text-muted me-2 w-25">{{ __('VAT Number') }}:</span>
                                    <span class="fw-medium">{{ $user->vat_number }}</span>
                                </li>
                                <li class="list-group-item px-0 py-3 d-flex border-0 border-bottom">
                                    <span class="text-muted me-2 w-25">{{ __('Phone') }}:</span>
                                    <span class="fw-medium">{{ $user->phone ?? __('Not provided') }}</span>
                                </li>
                                <li class="list-group-item px-0 py-3 d-flex border-0 border-bottom">
                                    <span class="text-muted me-2 w-25">{{ __('City') }}:</span>
                                    <span class="fw-medium">{{ $user->city }}</span>
                                </li>
                                <li class="list-group-item px-0 py-3 d-flex border-0">
                                    <span class="text-muted me-2 w-25">{{ __('Region') }}:</span>
                                    <span class="fw-medium">{{ $user->company_region }}</span>
                                </li>
                            </ul>
                            @else
                                @if(!$user->hasRole('customer'))
                                <div class="col-md-12">
                                    <div class="card border-0   mb-3">
                                        <div class="card-body text-center py-4">
                                             
                                            <h5>{{ __('Last Login') }}</h5>
                                            <h3 class="text-primary">
                                                @php
                                                    // Direct DB query for last login with random param to avoid browser caching
                                                    $lastLogin = DB::table('users')
                                                        ->where('id', $user->id)
                                                        ->value('last_login_at');
                                                    
                                                    $formattedLastLogin = 'N/A';
                                                    if (!empty($lastLogin)) {
                                                        try {
                                                            // Don't use diffForHumans() for last login since it changes on refresh
                                                            // Instead use a fixed format that won't change unless the actual value changes
                                                            $carbonDate = \Carbon\Carbon::parse($lastLogin);
                                                            $formattedLastLogin = $carbonDate->format('M j, Y g:i A');
                                                        } catch (\Exception $e) {
                                                            $formattedLastLogin = 'N/A';
                                                        }
                                                    }
                                                @endphp
                                                {{ $formattedLastLogin }}
                                            </h3>
                                            <a href="{{ route('user.logs.index') }}" class="btn btn-sm btn-primary mt-2">
                                                {{ __('View Activity') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @else
                            <div class="text-center py-5">
                                <div class="icon-box bg-light rounded-circle p-4 mx-auto mb-3" style="width: fit-content;">
                                    <i class="fas fa-info-circle text-muted fa-2x"></i>
                                </div>
                                <h5 class="text-muted">{{ __('No Company Information') }}</h5>
                                <p class="text-muted mb-0">{{ __('This is a personal account without company details.') }}</p>
                            </div>
                            @endif
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Account Activity Card -->
                <div class="col-12 mb-4">
                    <div class="card border-0 rounded-4 shadow-sm">
                        <div class="card-header bg-white border-0 pt-4 pb-2">
                            <div class="d-flex align-items-center">
                                <div class="icon-box bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                    <i class="fas fa-chart-bar text-primary"></i>
                                </div>
                                <h4 class="card-title mb-0">{{ __('Account Activity') }}</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if($user->hasRole('customer'))
                                <div class="col-md-4">
                                    <div class="card border-0 bg-light mb-3">
                                        <div class="card-body text-center py-4">
                                            <div class="icon-box bg-white rounded-circle p-3 mx-auto mb-3" style="width: fit-content;">
                                                <i class="fas fa-wallet text-primary"></i>
                                            </div>
                                            <h5>{{ __('Wallet Balance') }}</h5>
                                            <h3 class="text-primary">
                                                @if(isset($user->wallet))
                                                    SAR {{ number_format($user->wallet->balance, 2) }}
                                                @else
                                                    SAR 0.00
                                                @endif
                                            </h3>
                                            <a href="{{ route('wallet.index') }}" class="btn btn-sm btn-primary mt-2">
                                                {{ __('Manage Wallet') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if($user->hasRole('customer'))
                                <div class="{{ $user->hasRole('customer') ? 'col-md-4' : 'col-md-6' }}">
                                    <div class="card border-0 bg-light mb-3">
                                        <div class="card-body text-center py-4">
                                            <div class="icon-box bg-white rounded-circle p-3 mx-auto mb-3" style="width: fit-content;">
                                                <i class="fas fa-calendar-check text-primary"></i>
                                            </div>
                                            <h5>{{ __('Service Bookings') }}</h5>
                                            <h3 class="text-primary">
                                                @php
                                                    // Direct DB query for service bookings
                                                    $bookingsCount = \App\Models\ServiceBooking::where('user_id', $user->id)->count();
                                                    $ordersCount = 0;
                                                    if (class_exists('\App\Models\ServiceOrder')) {
                                                        $ordersCount = \App\Models\ServiceOrder::where('user_id', $user->id)->count();
                                                    }
                                                    $totalCount = $bookingsCount + $ordersCount;
                                                @endphp
                                                {{ $totalCount }}
                                            </h3>
                                            <a href="{{ route('services.booking.history') }}" class="btn btn-sm btn-primary mt-2">
                                                {{ __('View History') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            
                                <div class="{{ $user->hasRole('customer') ? 'col-md-4' : 'col-md-6' }}">
                                    <div class="card border-0 bg-light mb-3">
                                        <div class="card-body text-center py-4">
                                            <div class="icon-box bg-white rounded-circle p-3 mx-auto mb-3" style="width: fit-content;">
                                                <i class="fas fa-clock text-primary"></i>
                                            </div>
                                            <h5>{{ __('Last Login') }}</h5>
                                            <h3 class="text-primary">
                                                @php
                                                    // Direct DB query for last login with random param to avoid browser caching
                                                    $lastLogin = DB::table('users')
                                                        ->where('id', $user->id)
                                                        ->value('last_login_at');
                                                    
                                                    $formattedLastLogin = 'N/A';
                                                    if (!empty($lastLogin)) {
                                                        try {
                                                            // Don't use diffForHumans() for last login since it changes on refresh
                                                            // Instead use a fixed format that won't change unless the actual value changes
                                                            $carbonDate = \Carbon\Carbon::parse($lastLogin);
                                                            $formattedLastLogin = $carbonDate->format('M j, Y g:i A');
                                                        } catch (\Exception $e) {
                                                            $formattedLastLogin = 'N/A';
                                                        }
                                                    }
                                                @endphp
                                                {{ $formattedLastLogin }}
                                            </h3>
                                            <a href="{{ route('user.logs.index') }}" class="btn btn-sm btn-primary mt-2">
                                                {{ __('View Activity') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .icon-box {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .icon-box i {
        font-size: 1.5rem;
    }
    
    .fw-medium {
        font-weight: 500;
    }
    
    @media (max-width: 768px) {
        .icon-box {
            width: 40px;
            height: 40px;
        }
        
        .icon-box i {
            font-size: 1.2rem;
        }
    }
</style>
@endpush 