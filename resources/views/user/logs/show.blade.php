@extends('layouts.app')

@section('title', __('Activity Log Details'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h5 class="m-0 font-weight-bold text-primary">{{ __('Activity Log Details') }}</h5>
                    <a href="{{ route('user.logs.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('Back to Logs') }}
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th style="width: 150px;">{{ __('Date & Time') }}</th>
                                        <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Activity Type') }}</th>
                                        <td>
                                            @if($log->event == 'login')
                                                <span class="badge bg-primary">{{ __('Login Activity') }}</span>
                                            @elseif($log->event == 'wallet_recharge')
                                                <span class="badge bg-success">{{ __('Wallet Recharge') }}</span>
                                            @elseif($log->event == 'service_booking')
                                                <span class="badge bg-info">{{ __('Service Booking') }}</span>
                                            @elseif($log->event == 'profile_update')
                                                <span class="badge bg-warning">{{ __('Profile Update') }}</span>
                                            @elseif($log->event == 'vehicle_created')
                                                <span class="badge bg-success">{{ __('Vehicle Added') }}</span>
                                            @elseif($log->event == 'vehicle_updated')
                                                <span class="badge bg-warning">{{ __('Vehicle Updated') }}</span>
                                            @elseif($log->event == 'vehicle_deleted')
                                                <span class="badge bg-danger">{{ __('Vehicle Deleted') }}</span>
                                            @elseif($log->event == 'rfid_transfer_initiated')
                                                <span class="badge bg-info">{{ __('RFID Transfer Initiated') }}</span>
                                            @elseif($log->event == 'rfid_transfer_completed')
                                                <span class="badge bg-success">{{ __('RFID Transfer Completed') }}</span>
                                            @elseif($log->event == 'rfid_transfer_cancelled')
                                                <span class="badge bg-danger">{{ __('RFID Transfer Cancelled') }}</span>
                                            @elseif($log->event == 'rfid_recharge')
                                                <span class="badge bg-success">{{ __('RFID Recharged') }}</span>
                                            @elseif(Str::contains($log->event, 'payment'))
                                                <span class="badge bg-{{ Str::contains($log->event, 'failed') ? 'danger' : (Str::contains($log->event, 'success') ? 'success' : 'warning') }}">
                                                    {{ __('Payment') }} {{ Str::contains($log->event, 'failed') ? __('Failed') : (Str::contains($log->event, 'success') ? __('Successful') : __('Processing')) }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $log->event)) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Description') }}</th>
                                        <td>{{ $log->description }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('IP Address') }}</th>
                                        <td>{{ $log->ip_address }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            @if($log->subject_id)
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="m-0 font-weight-bold">{{ __('Related Item') }}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <strong>{{ __('Type') }}:</strong> 
                                        @if(Str::contains($log->subject_type, 'Vehicle'))
                                            <span class="badge bg-primary">{{ __('Vehicle') }}</span>
                                        @elseif(Str::contains($log->subject_type, 'RfidTransfer'))
                                            <span class="badge bg-info">{{ __('RFID Transfer') }}</span>
                                        @elseif(Str::contains($log->subject_type, 'RfidTransaction'))
                                            <span class="badge bg-success">{{ __('RFID Transaction') }}</span>
                                        @elseif(Str::contains($log->subject_type, 'ServiceBooking'))
                                            <span class="badge bg-warning">{{ __('Service Booking') }}</span>
                                        @else
                                            {{ class_basename($log->subject_type) }}
                                        @endif
                                    </div>
                                    <div>
                                        <strong>{{ __('ID') }}:</strong> {{ $log->subject_id }}
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    @if(!empty($log->properties))
                    <div class="mb-4">
                        <div class="card">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold">{{ __('Detailed Information') }}</h6>
                                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#rawJsonCollapse" aria-expanded="false">
                                    <i class="fas fa-code"></i> {{ __('View Raw Data') }}
                                </button>
                            </div>
                            <div class="card-body">
                                @if($log->event == 'vehicle_created')
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>{{ __('Plate Number') }}:</strong> {{ $log->properties['plate_number'] ?? 'N/A' }}</p>
                                            <p><strong>{{ __('Make') }}:</strong> {{ $log->properties['make'] ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>{{ __('Model') }}:</strong> {{ $log->properties['model'] ?? 'N/A' }}</p>
                                            <p><strong>{{ __('Year') }}:</strong> {{ $log->properties['year'] ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                @elseif($log->event == 'vehicle_updated')
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>{{ __('Previous Information') }}</h6>
                                            <p><strong>{{ __('Plate Number') }}:</strong> {{ $log->properties['old_values']['plate_number'] ?? 'N/A' }}</p>
                                            <p><strong>{{ __('Make') }}:</strong> {{ $log->properties['old_values']['make'] ?? 'N/A' }}</p>
                                            <p><strong>{{ __('Model') }}:</strong> {{ $log->properties['old_values']['model'] ?? 'N/A' }}</p>
                                            <p><strong>{{ __('Year') }}:</strong> {{ $log->properties['old_values']['year'] ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>{{ __('Updated Information') }}</h6>
                                            <p><strong>{{ __('Plate Number') }}:</strong> {{ $log->properties['new_values']['plate_number'] ?? 'N/A' }}</p>
                                            <p><strong>{{ __('Make') }}:</strong> {{ $log->properties['new_values']['make'] ?? 'N/A' }}</p>
                                            <p><strong>{{ __('Model') }}:</strong> {{ $log->properties['new_values']['model'] ?? 'N/A' }}</p>
                                            <p><strong>{{ __('Year') }}:</strong> {{ $log->properties['new_values']['year'] ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                @elseif($log->event == 'rfid_recharge')
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>{{ __('Vehicle') }}:</strong> {{ $log->properties['plate_number'] ?? 'N/A' }}</p>
                                            <p><strong>{{ __('Amount') }}:</strong> SAR {{ number_format($log->properties['amount'] ?? 0, 2) }}</p>
                                            <p><strong>{{ __('Payment Method') }}:</strong> {{ ucfirst($log->properties['payment_method'] ?? 'N/A') }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>{{ __('Previous Balance') }}:</strong> SAR {{ number_format($log->properties['previous_balance'] ?? 0, 2) }}</p>
                                            <p><strong>{{ __('New Balance') }}:</strong> SAR {{ number_format($log->properties['new_balance'] ?? 0, 2) }}</p>
                                        </div>
                                    </div>
                                @elseif(Str::contains($log->event, 'rfid_transfer'))
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>{{ __('Source Vehicle') }}:</strong> {{ $log->properties['source_vehicle']['plate_number'] ?? ($log->properties['source_vehicle'] ?? 'N/A') }}</p>
                                            <p><strong>{{ __('RFID Number') }}:</strong> {{ $log->properties['rfid_number'] ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>{{ __('Target Vehicle') }}:</strong> {{ $log->properties['target_vehicle']['plate_number'] ?? ($log->properties['target_vehicle'] ?? 'N/A') }}</p>
                                            <p><strong>{{ __('Status') }}:</strong> 
                                                @if($log->event == 'rfid_transfer_initiated')
                                                    <span class="badge bg-info">{{ __('Initiated') }}</span>
                                                @elseif($log->event == 'rfid_transfer_completed')
                                                    <span class="badge bg-success">{{ __('Completed') }}</span>
                                                @elseif($log->event == 'rfid_transfer_cancelled')
                                                    <span class="badge bg-danger">{{ __('Cancelled') }}</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                @elseif($log->event == 'wallet_recharge')
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>{{ __('Amount') }}:</strong> SAR {{ number_format($log->properties['amount'] ?? 0, 2) }}</p>
                                            <p><strong>{{ __('Payment Method') }}:</strong> {{ ucfirst(str_replace('_', ' ', $log->properties['payment_method'] ?? 'N/A')) }}</p>
                                            @if(isset($log->properties['card_brand']))
                                                <p><strong>{{ __('Card Type') }}:</strong> 
                                                    <span class="badge 
                                                        @if($log->properties['card_brand'] === 'VISA') bg-primary
                                                        @elseif($log->properties['card_brand'] === 'MASTERCARD') bg-danger
                                                        @elseif($log->properties['card_brand'] === 'MADA') bg-success
                                                        @else bg-secondary
                                                        @endif">
                                                        {{ $log->properties['card_brand'] }}
                                                        @if($log->properties['card_brand'] === 'MADA')
                                                            (مدى)
                                                        @endif
                                                    </span>
                                                </p>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            @if(isset($log->properties['gateway']))
                                                <p><strong>{{ __('Gateway') }}:</strong> {{ ucfirst($log->properties['gateway'] ?? 'N/A') }}</p>
                                            @endif
                                            @if(isset($log->properties['transaction_id']))
                                                <p><strong>{{ __('Transaction ID') }}:</strong> {{ $log->properties['transaction_id'] ?? 'N/A' }}</p>
                                            @endif
                                            @if(isset($log->properties['payment_id']))
                                                <p><strong>{{ __('Payment Reference') }}:</strong> {{ $log->properties['payment_id'] ?? 'N/A' }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @elseif($log->event == 'service_booking')
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>{{ __('Service Type') }}:</strong> {{ $log->properties['service_type'] ?? 'N/A' }}</p>
                                            <p><strong>{{ __('Vehicle') }}:</strong> {{ $log->properties['vehicle_make'] ?? 'N/A' }} {{ $log->properties['vehicle_model'] ?? '' }}</p>
                                            <p><strong>{{ __('Plate Number') }}:</strong> {{ $log->properties['plate_number'] ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>{{ __('Payment Method') }}:</strong> {{ ucfirst($log->properties['payment_method'] ?? 'N/A') }}</p>
                                            <p><strong>{{ __('Payment Status') }}:</strong> {{ ucfirst($log->properties['payment_status'] ?? 'N/A') }}</p>
                                            <p><strong>{{ __('Amount') }}:</strong> SAR {{ number_format($log->properties['amount'] ?? 0, 2) }}</p>
                                            <p><strong>{{ __('Reference') }}:</strong> {{ $log->properties['reference_number'] ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                @else
                                    {{-- Universal JSON Data Display --}}
                                    <div class="json-data-display">
                                        @php
                                            $properties = is_string($log->properties) ? json_decode($log->properties, true) : $log->properties;
                                        @endphp
                                        
                                        @if(isset($properties['payment_data']))
                                            {{-- Payment Data Section --}}
                                            <div class="row mb-4">
                                                <div class="col-12">
                                                    <h6 class="text-primary mb-3">
                                                        <i class="fas fa-credit-card me-2"></i>{{ __('Payment Information') }}
                                                    </h6>
                                                    <div class="row">
                                                        @if(isset($properties['payment_data']['amount']))
                                                            <div class="col-md-6 mb-3">
                                                                <div class="card border-left-primary h-100">
                                                                    <div class="card-body">
                                                                        <div class="row no-gutters align-items-center">
                                                                            <div class="col mr-2">
                                                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __('Amount') }}</div>
                                                                                <div class="h5 mb-0 font-weight-bold text-gray-800">SAR {{ number_format($properties['payment_data']['amount'], 2) }}</div>
                                                                            </div>
                                                                            <div class="col-auto">
                                                                                <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        
                                                        @if(isset($properties['payment_data']['id']))
                                                            <div class="col-md-6 mb-3">
                                                                <div class="card border-left-info h-100">
                                                                    <div class="card-body">
                                                                        <div class="row no-gutters align-items-center">
                                                                            <div class="col mr-2">
                                                                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">{{ __('Payment ID') }}</div>
                                                                                <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $properties['payment_data']['id'] }}</div>
                                                                            </div>
                                                                            <div class="col-auto">
                                                                                <i class="fas fa-hashtag fa-2x text-gray-300"></i>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            {{-- Card Information --}}
                                            @if(isset($properties['payment_data']['card']))
                                                <div class="row mb-4">
                                                    <div class="col-12">
                                                        <h6 class="text-success mb-3">
                                                            <i class="fas fa-credit-card me-2"></i>{{ __('Card Details') }}
                                                        </h6>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <table class="table table-sm">
                                                                    <tbody>
                                                                        @if(isset($properties['payment_data']['card']['type']))
                                                                            <tr>
                                                                                <th style="width: 40%;">{{ __('Card Type') }}:</th>
                                                                                <td>
                                                                                    <span class="badge bg-{{ $properties['payment_data']['card']['type'] === 'DEBIT' ? 'success' : 'primary' }}">
                                                                                        {{ $properties['payment_data']['card']['type'] }}
                                                                                    </span>
                                                                                </td>
                                                                            </tr>
                                                                        @endif
                                                                        @if(isset($properties['payment_data']['card']['level']))
                                                                            <tr>
                                                                                <th>{{ __('Card Level') }}:</th>
                                                                                <td>{{ $properties['payment_data']['card']['level'] }}</td>
                                                                            </tr>
                                                                        @endif
                                                                        @if(isset($properties['payment_data']['card']['holder']))
                                                                            <tr>
                                                                                <th>{{ __('Card Holder') }}:</th>
                                                                                <td>{{ $properties['payment_data']['card']['holder'] }}</td>
                                                                            </tr>
                                                                        @endif
                                                                        @if(isset($properties['payment_data']['card']['last4Digits']))
                                                                            <tr>
                                                                                <th>{{ __('Last 4 Digits') }}:</th>
                                                                                <td>**** **** **** {{ $properties['payment_data']['card']['last4Digits'] }}</td>
                                                                            </tr>
                                                                        @endif
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <table class="table table-sm">
                                                                    <tbody>
                                                                        @if(isset($properties['payment_data']['card']['issuer']['bank']))
                                                                            <tr>
                                                                                <th style="width: 40%;">{{ __('Bank') }}:</th>
                                                                                <td>{{ $properties['payment_data']['card']['issuer']['bank'] }}</td>
                                                                            </tr>
                                                                        @endif
                                                                        @if(isset($properties['payment_data']['card']['country']))
                                                                            <tr>
                                                                                <th>{{ __('Country') }}:</th>
                                                                                <td>{{ $properties['payment_data']['card']['country'] }}</td>
                                                                            </tr>
                                                                        @endif
                                                                        @if(isset($properties['payment_data']['card']['expiryMonth']) && isset($properties['payment_data']['card']['expiryYear']))
                                                                            <tr>
                                                                                <th>{{ __('Expiry Date') }}:</th>
                                                                                <td>{{ $properties['payment_data']['card']['expiryMonth'] }}/{{ $properties['payment_data']['card']['expiryYear'] }}</td>
                                                                            </tr>
                                                                        @endif
                                                                        @if(isset($properties['payment_data']['card']['regulatedFlag']))
                                                                            <tr>
                                                                                <th>{{ __('Regulated') }}:</th>
                                                                                <td>
                                                                                    <span class="badge bg-{{ $properties['payment_data']['card']['regulatedFlag'] ? 'success' : 'warning' }}">
                                                                                        {{ $properties['payment_data']['card']['regulatedFlag'] ? __('Yes') : __('No') }}
                                                                                    </span>
                                                                                </td>
                                                                            </tr>
                                                                        @endif
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            {{-- Risk Information --}}
                                            @if(isset($properties['payment_data']['risk']))
                                                <div class="row mb-4">
                                                    <div class="col-12">
                                                        <h6 class="text-warning mb-3">
                                                            <i class="fas fa-shield-alt me-2"></i>{{ __('Risk Assessment') }}
                                                        </h6>
                                                        <div class="row">
                                                            @if(isset($properties['payment_data']['risk']['score']))
                                                                <div class="col-md-6">
                                                                    <div class="card border-left-warning">
                                                                        <div class="card-body">
                                                                            <div class="row no-gutters align-items-center">
                                                                                <div class="col mr-2">
                                                                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">{{ __('Risk Score') }}</div>
                                                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $properties['payment_data']['risk']['score'] }}</div>
                                                                                </div>
                                                                                <div class="col-auto">
                                                                                    <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            {{-- Result Information --}}
                                            @if(isset($properties['payment_data']['result']))
                                                <div class="row mb-4">
                                                    <div class="col-12">
                                                        <h6 class="text-{{ isset($properties['payment_data']['result']['code']) && $properties['payment_data']['result']['code'] === '000.100.110' ? 'success' : 'danger' }} mb-3">
                                                            <i class="fas fa-check-circle me-2"></i>{{ __('Transaction Result') }}
                                                        </h6>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <table class="table table-sm">
                                                                    <tbody>
                                                                        @if(isset($properties['payment_data']['result']['code']))
                                                                            <tr>
                                                                                <th style="width: 40%;">{{ __('Result Code') }}:</th>
                                                                                <td>
                                                                                    <span class="badge bg-{{ $properties['payment_data']['result']['code'] === '000.100.110' ? 'success' : 'danger' }}">
                                                                                        {{ $properties['payment_data']['result']['code'] }}
                                                                                    </span>
                                                                                </td>
                                                                            </tr>
                                                                        @endif
                                                                        @if(isset($properties['payment_data']['result']['description']))
                                                                            <tr>
                                                                                <th>{{ __('Description') }}:</th>
                                                                                <td>{{ $properties['payment_data']['result']['description'] }}</td>
                                                                            </tr>
                                                                        @endif
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                        
                                        {{-- General Information --}}
                                        @if(isset($properties['ip']) || isset($properties['url']) || isset($properties['route']) || isset($properties['method']))
                                            <div class="row mb-4">
                                                <div class="col-12">
                                                    <h6 class="text-info mb-3">
                                                        <i class="fas fa-info-circle me-2"></i>{{ __('General Information') }}
                                                    </h6>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <table class="table table-sm">
                                                                <tbody>
                                                                    @if(isset($properties['ip']))
                                                                        <tr>
                                                                            <th style="width: 40%;">{{ __('IP Address') }}:</th>
                                                                            <td><code>{{ $properties['ip'] }}</code></td>
                                                                        </tr>
                                                                    @endif
                                                                    @if(isset($properties['method']))
                                                                        <tr>
                                                                            <th>{{ __('HTTP Method') }}:</th>
                                                                            <td>
                                                                                <span class="badge bg-{{ $properties['method'] === 'POST' ? 'success' : ($properties['method'] === 'GET' ? 'primary' : 'secondary') }}">
                                                                                    {{ $properties['method'] }}
                                                                                </span>
                                                                            </td>
                                                                        </tr>
                                                                    @endif
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <table class="table table-sm">
                                                                <tbody>
                                                                    @if(isset($properties['route']))
                                                                        <tr>
                                                                            <th style="width: 40%;">{{ __('Route') }}:</th>
                                                                            <td><code>{{ $properties['route'] }}</code></td>
                                                                        </tr>
                                                                    @endif
                                                                    @if(isset($properties['user_agent']))
                                                                        <tr>
                                                                            <th>{{ __('User Agent') }}:</th>
                                                                            <td><small class="text-muted">{{ Str::limit($properties['user_agent'], 50) }}</small></td>
                                                                        </tr>
                                                                    @endif
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        {{-- Additional Data with Intelligent JSON Formatting --}}
                                        @php
                                            $excludedKeys = ['payment_data', 'ip', 'url', 'route', 'method', 'user_agent'];
                                            $additionalData = array_diff_key($properties, array_flip($excludedKeys));
                                        @endphp
                                        
                                        @if(!empty($additionalData))
                                            <div class="row">
                                                <div class="col-12">
                                                    <h6 class="text-secondary mb-3">
                                                        <i class="fas fa-database me-2"></i>{{ __('Additional Data') }}
                                                    </h6>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th style="width: 30%;">{{ __('Field') }}</th>
                                                                    <th>{{ __('Value') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($additionalData as $key => $value)
                                                                    <tr>
                                                                        <th>{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                                                                        <td>
                                                                            @if($key === 'subject' && is_array($value))
                                                                                {{-- User Profile/Subject Information --}}
                                                                                <div class="user-profile-data">
                                                                                    <div class="row">
                                                                                        @if(isset($value['name']) || isset($value['email']) || isset($value['mobile']))
                                                                                            <div class="col-md-6 mb-3">
                                                                                                <h6 class="text-primary mb-2">
                                                                                                    <i class="fas fa-user me-2"></i>{{ __('Personal Information') }}
                                                                                                </h6>
                                                                                                <table class="table table-sm table-borderless">
                                                                                                    <tbody>
                                                                                                        @if(isset($value['name']))
                                                                                                            <tr>
                                                                                                                <th style="width: 40%;">{{ __('Name') }}:</th>
                                                                                                                <td>{{ $value['name'] ?? __('Not provided') }}</td>
                                                                                                            </tr>
                                                                                                        @endif
                                                                                                        @if(isset($value['email']))
                                                                                                            <tr>
                                                                                                                <th>{{ __('Email') }}:</th>
                                                                                                                <td><a href="mailto:{{ $value['email'] }}">{{ $value['email'] }}</a></td>
                                                                                                            </tr>
                                                                                                        @endif
                                                                                                        @if(isset($value['mobile']))
                                                                                                            <tr>
                                                                                                                <th>{{ __('Mobile') }}:</th>
                                                                                                                <td>{{ $value['mobile'] }}</td>
                                                                                                            </tr>
                                                                                                        @endif
                                                                                                        @if(isset($value['phone']))
                                                                                                            <tr>
                                                                                                                <th>{{ __('Phone') }}:</th>
                                                                                                                <td>{{ $value['phone'] ?? __('Not provided') }}</td>
                                                                                                            </tr>
                                                                                                        @endif
                                                                                                        @if(isset($value['gender']))
                                                                                                            <tr>
                                                                                                                <th>{{ __('Gender') }}:</th>
                                                                                                                <td>{{ ucfirst($value['gender'] ?? __('Not specified')) }}</td>
                                                                                                            </tr>
                                                                                                        @endif
                                                                                                    </tbody>
                                                                                                </table>
                                                                                            </div>
                                                                                        @endif
                                                                                        
                                                                                        @if(isset($value['company_name']) || isset($value['company_type']) || isset($value['cr_number']))
                                                                                            <div class="col-md-6 mb-3">
                                                                                                <h6 class="text-success mb-2">
                                                                                                    <i class="fas fa-building me-2"></i>{{ __('Company Information') }}
                                                                                                </h6>
                                                                                                <table class="table table-sm table-borderless">
                                                                                                    <tbody>
                                                                                                        @if(isset($value['company_name']))
                                                                                                            <tr>
                                                                                                                <th style="width: 40%;">{{ __('Company Name') }}:</th>
                                                                                                                <td>{{ $value['company_name'] }}</td>
                                                                                                            </tr>
                                                                                                        @endif
                                                                                                        @if(isset($value['company_type']))
                                                                                                            <tr>
                                                                                                                <th>{{ __('Company Type') }}:</th>
                                                                                                                <td>
                                                                                                                    <span class="badge bg-{{ $value['company_type'] === 'private' ? 'primary' : 'info' }}">
                                                                                                                        {{ ucfirst($value['company_type']) }}
                                                                                                                    </span>
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                        @endif
                                                                                                        @if(isset($value['cr_number']))
                                                                                                            <tr>
                                                                                                                <th>{{ __('CR Number') }}:</th>
                                                                                                                <td><code>{{ $value['cr_number'] }}</code></td>
                                                                                                            </tr>
                                                                                                        @endif
                                                                                                        @if(isset($value['vat_number']))
                                                                                                            <tr>
                                                                                                                <th>{{ __('VAT Number') }}:</th>
                                                                                                                <td><code>{{ $value['vat_number'] }}</code></td>
                                                                                                            </tr>
                                                                                                        @endif
                                                                                                        @if(isset($value['customer_no']))
                                                                                                            <tr>
                                                                                                                <th>{{ __('Customer No') }}:</th>
                                                                                                                <td><code>{{ $value['customer_no'] }}</code></td>
                                                                                                            </tr>
                                                                                                        @endif
                                                                                                    </tbody>
                                                                                                </table>
                                                                                            </div>
                                                                                        @endif
                                                                                    </div>
                                                                                    
                                                                                    @if(isset($value['city']) || isset($value['region']) || isset($value['zip_code']))
                                                                                        <div class="row">
                                                                                            <div class="col-md-6 mb-3">
                                                                                                <h6 class="text-info mb-2">
                                                                                                    <i class="fas fa-map-marker-alt me-2"></i>{{ __('Location Information') }}
                                                                                                </h6>
                                                                                                <table class="table table-sm table-borderless">
                                                                                                    <tbody>
                                                                                                        @if(isset($value['city']))
                                                                                                            <tr>
                                                                                                                <th style="width: 40%;">{{ __('City') }}:</th>
                                                                                                                <td>{{ $value['city'] }}</td>
                                                                                                            </tr>
                                                                                                        @endif
                                                                                                        @if(isset($value['region']))
                                                                                                            <tr>
                                                                                                                <th>{{ __('Region') }}:</th>
                                                                                                                <td>{{ $value['region'] ?? __('Not specified') }}</td>
                                                                                                            </tr>
                                                                                                        @endif
                                                                                                        @if(isset($value['zip_code']))
                                                                                                            <tr>
                                                                                                                <th>{{ __('ZIP Code') }}:</th>
                                                                                                                <td>{{ $value['zip_code'] }}</td>
                                                                                                            </tr>
                                                                                                        @endif
                                                                                                        @if(isset($value['building_number']))
                                                                                                            <tr>
                                                                                                                <th>{{ __('Building Number') }}:</th>
                                                                                                                <td>{{ $value['building_number'] }}</td>
                                                                                                            </tr>
                                                                                                        @endif
                                                                                                    </tbody>
                                                                                                </table>
                                                                                            </div>
                                                                                            
                                                                                            <div class="col-md-6 mb-3">
                                                                                                <h6 class="text-warning mb-2">
                                                                                                    <i class="fas fa-cog me-2"></i>{{ __('Account Information') }}
                                                                                                </h6>
                                                                                                <table class="table table-sm table-borderless">
                                                                                                    <tbody>
                                                                                                        @if(isset($value['status']))
                                                                                                            <tr>
                                                                                                                <th style="width: 40%;">{{ __('Status') }}:</th>
                                                                                                                <td>
                                                                                                                    <span class="badge bg-{{ $value['status'] === 'active' ? 'success' : 'danger' }}">
                                                                                                                        {{ ucfirst($value['status']) }}
                                                                                                                    </span>
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                        @endif
                                                                                                        @if(isset($value['locale']))
                                                                                                            <tr>
                                                                                                                <th>{{ __('Language') }}:</th>
                                                                                                                <td>
                                                                                                                    <span class="badge bg-{{ $value['locale'] === 'ar' ? 'primary' : 'secondary' }}">
                                                                                                                        {{ $value['locale'] === 'ar' ? 'العربية' : 'English' }}
                                                                                                                    </span>
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                        @endif
                                                                                                        @if(isset($value['registration_type']))
                                                                                                            <tr>
                                                                                                                <th>{{ __('Registration Type') }}:</th>
                                                                                                                <td>{{ ucfirst($value['registration_type']) }}</td>
                                                                                                            </tr>
                                                                                                        @endif
                                                                                                        @if(isset($value['is_active']))
                                                                                                            <tr>
                                                                                                                <th>{{ __('Active') }}:</th>
                                                                                                                <td>
                                                                                                                    <span class="badge bg-{{ $value['is_active'] ? 'success' : 'danger' }}">
                                                                                                                        {{ $value['is_active'] ? __('Yes') : __('No') }}
                                                                                                                    </span>
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                        @endif
                                                                                                    </tbody>
                                                                                                </table>
                                                                                            </div>
                                                                                        </div>
                                                                                    @endif
                                                                                    
                                                                                    @if(isset($value['created_at']) || isset($value['updated_at']) || isset($value['last_login_at']))
                                                                                        <div class="row">
                                                                                            <div class="col-12">
                                                                                                <h6 class="text-secondary mb-2">
                                                                                                    <i class="fas fa-clock me-2"></i>{{ __('Timestamps') }}
                                                                                                </h6>
                                                                                                <div class="row">
                                                                                                    @if(isset($value['created_at']))
                                                                                                        <div class="col-md-4">
                                                                                                            <small class="text-muted">{{ __('Created') }}:</small><br>
                                                                                                            <strong>{{ \Carbon\Carbon::parse($value['created_at'])->format('Y-m-d H:i:s') }}</strong>
                                                                                                        </div>
                                                                                                    @endif
                                                                                                    @if(isset($value['updated_at']))
                                                                                                        <div class="col-md-4">
                                                                                                            <small class="text-muted">{{ __('Updated') }}:</small><br>
                                                                                                            <strong>{{ \Carbon\Carbon::parse($value['updated_at'])->format('Y-m-d H:i:s') }}</strong>
                                                                                                        </div>
                                                                                                    @endif
                                                                                                    @if(isset($value['last_login_at']))
                                                                                                        <div class="col-md-4">
                                                                                                            <small class="text-muted">{{ __('Last Login') }}:</small><br>
                                                                                                            <strong>{{ \Carbon\Carbon::parse($value['last_login_at'])->format('Y-m-d H:i:s') }}</strong>
                                                                                                        </div>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            @elseif($key === 'changes' && is_array($value))
                                                                                {{-- Changes Information --}}
                                                                                <div class="changes-data">
                                                                                    <h6 class="text-info mb-2">
                                                                                        <i class="fas fa-edit me-2"></i>{{ __('Modified Fields') }}
                                                                                    </h6>
                                                                                    <div class="row">
                                                                                        @foreach($value as $change)
                                                                                            <div class="col-md-6 mb-2">
                                                                                                <span class="badge bg-warning">{{ ucfirst(str_replace('_', ' ', $change)) }}</span>
                                                                                            </div>
                                                                                        @endforeach
                                                                                    </div>
                                                                                </div>
                                                                            @elseif($key === 'new_values' && is_array($value))
                                                                                {{-- New Values Information --}}
                                                                                <div class="new-values-data">
                                                                                    <h6 class="text-success mb-2">
                                                                                        <i class="fas fa-plus-circle me-2"></i>{{ __('New Values') }}
                                                                                    </h6>
                                                                                    <table class="table table-sm table-borderless">
                                                                                        <tbody>
                                                                                            @foreach($value as $field => $newValue)
                                                                                                <tr>
                                                                                                    <th style="width: 40%;">{{ ucfirst(str_replace('_', ' ', $field)) }}:</th>
                                                                                                    <td>
                                                                                                        @if($field === 'locale')
                                                                                                            <span class="badge bg-{{ $newValue === 'ar' ? 'primary' : 'secondary' }}">
                                                                                                                {{ $newValue === 'ar' ? 'العربية' : 'English' }}
                                                                                                            </span>
                                                                                                        @elseif(is_bool($newValue))
                                                                                                            <span class="badge bg-{{ $newValue ? 'success' : 'danger' }}">{{ $newValue ? __('Yes') : __('No') }}</span>
                                                                                                        @else
                                                                                                            {{ $newValue }}
                                                                                                        @endif
                                                                                                    </td>
                                                                                                </tr>
                                                                                            @endforeach
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            @elseif($key === 'old_values' && is_array($value))
                                                                                {{-- Old Values Information --}}
                                                                                <div class="old-values-data">
                                                                                    <h6 class="text-danger mb-2">
                                                                                        <i class="fas fa-minus-circle me-2"></i>{{ __('Previous Values') }}
                                                                                    </h6>
                                                                                    <table class="table table-sm table-borderless">
                                                                                        <tbody>
                                                                                            @foreach($value as $field => $oldValue)
                                                                                                <tr>
                                                                                                    <th style="width: 40%;">{{ ucfirst(str_replace('_', ' ', $field)) }}:</th>
                                                                                                    <td>
                                                                                                        @if($field === 'locale')
                                                                                                            <span class="badge bg-{{ $oldValue === 'ar' ? 'primary' : 'secondary' }}">
                                                                                                                {{ $oldValue === 'ar' ? 'العربية' : 'English' }}
                                                                                                            </span>
                                                                                                        @elseif(is_bool($oldValue))
                                                                                                            <span class="badge bg-{{ $oldValue ? 'success' : 'danger' }}">{{ $oldValue ? __('Yes') : __('No') }}</span>
                                                                                                        @else
                                                                                                            {{ $oldValue }}
                                                                                                        @endif
                                                                                                    </td>
                                                                                                </tr>
                                                                                            @endforeach
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            @elseif(is_array($value))
                                                                                {{-- Generic Array Handling --}}
                                                                                <div class="array-data">
                                                                                    @if(count($value) <= 5)
                                                                                        <div class="row">
                                                                                            @foreach($value as $item)
                                                                                                <div class="col-md-6 mb-1">
                                                                                                    <span class="badge bg-secondary">{{ $item }}</span>
                                                                                                </div>
                                                                                            @endforeach
                                                                                        </div>
                                                                                    @else
                                                                                        <div class="row">
                                                                                            @foreach(array_slice($value, 0, 5) as $item)
                                                                                                <div class="col-md-6 mb-1">
                                                                                                    <span class="badge bg-secondary">{{ $item }}</span>
                                                                                                </div>
                                                                                            @endforeach
                                                                                            <div class="col-12">
                                                                                                <small class="text-muted">{{ __('And') }} {{ count($value) - 5 }} {{ __('more items') }}</small>
                                                                                            </div>
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            @elseif(is_bool($value))
                                                                                <span class="badge bg-{{ $value ? 'success' : 'danger' }}">{{ $value ? __('Yes') : __('No') }}</span>
                                                                            @elseif(is_numeric($value) && strpos($key, 'amount') !== false)
                                                                                SAR {{ number_format($value, 2) }}
                                                                            @elseif($key === 'type')
                                                                                <span class="badge bg-info">{{ ucfirst($value) }}</span>
                                                                            @elseif($key === 'browser')
                                                                                <span class="badge bg-primary">{{ $value }}</span>
                                                                            @elseif($key === 'device_type')
                                                                                <span class="badge bg-secondary">{{ ucfirst($value) }}</span>
                                                                            @else
                                                                                {{ $value }}
                                                                            @endif
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
                                    
                                    {{-- Raw JSON Collapsible Section --}}
                                    <div class="collapse mt-3" id="rawJsonCollapse">
                                        <div class="card">
                                            <div class="card-header bg-light">
                                                <h6 class="m-0 font-weight-bold text-muted">{{ __('Raw JSON Data') }}</h6>
                                            </div>
                                            <div class="card-body">
                                                <pre class="mb-0"><code>{{ json_encode($properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.border-left-danger {
    border-left: 0.25rem solid #e74a3b !important;
}
.text-xs {
    font-size: 0.7rem;
}
.json-data-display .card {
    transition: all 0.3s ease;
}
.json-data-display .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>
@endsection 