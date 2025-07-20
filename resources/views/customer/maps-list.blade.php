@extends('layouts.app')

@section('title', 'Map Locations List - JOIL YASEEIR')

@php
use Illuminate\Support\Facades\Route;
@endphp

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">
            <i class="fas fa-map-marker-alt me-2"></i> Map Locations List
            @if(Route::currentRouteName() === 'enhanced-maps-list')
                <span class="badge bg-success ms-2">Enhanced</span>
            @endif
        </h3>
        <div>
            @if(Route::currentRouteName() === 'maps-list')
                <a href="{{ route('enhanced-maps-list') }}" class="btn btn-info me-2">
                    <i class="fas fa-exchange-alt me-1"></i> Switch to Enhanced View
                </a>
            @else
                <a href="{{ route('maps-list') }}" class="btn btn-info me-2">
                    <i class="fas fa-exchange-alt me-1"></i> Switch to Regular View
                </a>
            @endif
            <a href="{{ route('all-map-locations') }}" class="btn btn-success me-2">
                <i class="fas fa-table me-1"></i> All Locations
            </a>
            <button type="button" class="btn btn-primary" onclick="refreshLocations()">
                <i class="fas fa-sync-alt me-1"></i> Refresh
            </button>
        </div>
    </div>

    <div id="loading-overlay" class="position-fixed w-100 h-100 top-0 start-0 d-none" style="background: rgba(0,0,0,0.3); z-index: 1050;">
        <div class="d-flex justify-content-center align-items-center h-100">
            <div class="bg-white p-4 rounded shadow">
                <div class="spinner-border text-primary mb-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="text-center">Loading locations...</div>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Locations</h6>
                    <h2 class="mb-0">{{ $total_count ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Operational</h6>
                    <h2 class="mb-0">{{ $operational_count ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning">
                <div class="card-body">
                    <h6 class="card-title">Under Maintenance</h6>
                    <h2 class="mb-0">{{ $maintenance_count ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Service Types</h6>
                    <h2 class="mb-0">{{ isset($locations) ? count(array_unique(array_merge(
                        isset($locations['main_stations']) ? array_merge(...array_map(fn($s) => $s['services'] ?? [], $locations['main_stations'])) : [],
                        isset($locations['rfid_stations']) ? array_merge(...array_map(fn($s) => $s['services'] ?? [], $locations['rfid_stations'])) : [],
                        isset($locations['service_points']) ? array_merge(...array_map(fn($s) => $s['services'] ?? [], $locations['service_points'])) : []
                    ))) : 0 }}</h2>
                </div>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs mb-3" id="locationTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="rfid-tab" data-bs-toggle="tab" data-bs-target="#rfid-stations" type="button" role="tab" aria-controls="rfid-stations" aria-selected="true">
                RFID Stations <span class="badge bg-primary ms-1">{{ isset($locations['rfid_stations']) ? count($locations['rfid_stations']) : 0 }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="main-tab" data-bs-toggle="tab" data-bs-target="#main-stations" type="button" role="tab" aria-controls="main-stations" aria-selected="false">
                Main Stations <span class="badge bg-primary ms-1">{{ isset($locations['main_stations']) ? count($locations['main_stations']) : 0 }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="service-tab" data-bs-toggle="tab" data-bs-target="#service-points" type="button" role="tab" aria-controls="service-points" aria-selected="false">
                Service Points <span class="badge bg-primary ms-1">{{ isset($locations['service_points']) ? count($locations['service_points']) : 0 }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content" id="locationTabsContent">
        <!-- RFID Stations -->
        <div class="tab-pane fade show active" id="rfid-stations" role="tabpanel" aria-labelledby="rfid-tab">
            @if(isset($locations['rfid_stations']) && !empty($locations['rfid_stations']))
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover location-table" id="rfidStationsTable">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Type</th>
                                        <th>Latitude</th>
                                        <th>Longitude</th>
                                        <th>Services</th>
                                        <th>Hours</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($locations['rfid_stations'] as $location)
                                        <tr>
                                            <td>{{ $location['title'] }}</td>
                                            <td>
                                                <span class="badge bg-{{ $location['status'] === 'operational' ? 'success' : ($location['status'] === 'maintenance' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($location['status']) }}
                                                </span>
                                            </td>
                                            <td>{{ $location['type'] }}</td>
                                            <td>{{ number_format($location['lat'], 6) }}</td>
                                            <td>{{ number_format($location['lng'], 6) }}</td>
                                            <td>
                                                @foreach($location['services'] as $service)
                                                    <span class="badge bg-info me-1">{{ $service }}</span>
                                                @endforeach
                                            </td>
                                            <td>{{ $location['hours'] }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="https://www.google.com/maps?q={{ $location['lat'] }},{{ $location['lng'] }}" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-outline-primary"
                                                       title="View on Google Maps">
                                                        <i class="fas fa-map-marker-alt"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-info"
                                                            onclick="showLocationDetails('{{ json_encode($location) }}')"
                                                            title="View Details">
                                                        <i class="fas fa-info-circle"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No RFID Stations available.
                </div>
            @endif
        </div>

        <!-- Main Stations -->
        <div class="tab-pane fade" id="main-stations" role="tabpanel" aria-labelledby="main-tab">
            @if(isset($locations['main_stations']) && !empty($locations['main_stations']))
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover location-table" id="mainStationsTable">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Type</th>
                                        <th>Latitude</th>
                                        <th>Longitude</th>
                                        <th>Services</th>
                                        <th>Hours</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($locations['main_stations'] as $location)
                                        <tr>
                                            <td>{{ $location['title'] }}</td>
                                            <td>
                                                <span class="badge bg-{{ $location['status'] === 'operational' ? 'success' : ($location['status'] === 'maintenance' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($location['status']) }}
                                                </span>
                                            </td>
                                            <td>{{ $location['type'] }}</td>
                                            <td>{{ number_format($location['lat'], 6) }}</td>
                                            <td>{{ number_format($location['lng'], 6) }}</td>
                                            <td>
                                                @foreach($location['services'] as $service)
                                                    <span class="badge bg-info me-1">{{ $service }}</span>
                                                @endforeach
                                            </td>
                                            <td>{{ $location['hours'] }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="https://www.google.com/maps?q={{ $location['lat'] }},{{ $location['lng'] }}" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-outline-primary"
                                                       title="View on Google Maps">
                                                        <i class="fas fa-map-marker-alt"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-info"
                                                            onclick="showLocationDetails('{{ json_encode($location) }}')"
                                                            title="View Details">
                                                        <i class="fas fa-info-circle"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No Main Stations available.
                </div>
            @endif
        </div>

        <!-- Service Points -->
        <div class="tab-pane fade" id="service-points" role="tabpanel" aria-labelledby="service-tab">
            @if(isset($locations['service_points']) && !empty($locations['service_points']))
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover location-table" id="servicePointsTable">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Type</th>
                                        <th>Latitude</th>
                                        <th>Longitude</th>
                                        <th>Services</th>
                                        <th>Hours</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($locations['service_points'] as $location)
                                        <tr>
                                            <td>{{ $location['title'] }}</td>
                                            <td>
                                                <span class="badge bg-{{ $location['status'] === 'operational' ? 'success' : ($location['status'] === 'maintenance' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($location['status']) }}
                                                </span>
                                            </td>
                                            <td>{{ $location['type'] }}</td>
                                            <td>{{ number_format($location['lat'], 6) }}</td>
                                            <td>{{ number_format($location['lng'], 6) }}</td>
                                            <td>
                                                @foreach($location['services'] as $service)
                                                    <span class="badge bg-info me-1">{{ $service }}</span>
                                                @endforeach
                                            </td>
                                            <td>{{ $location['hours'] }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="https://www.google.com/maps?q={{ $location['lat'] }},{{ $location['lng'] }}" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-outline-primary"
                                                       title="View on Google Maps">
                                                        <i class="fas fa-map-marker-alt"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-info"
                                                            onclick="showLocationDetails('{{ json_encode($location) }}')"
                                                            title="View Details">
                                                        <i class="fas fa-info-circle"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No Service Points available.
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Location Details Modal -->
<div class="modal fade" id="locationDetailsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Location Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="locationDetails"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function refreshLocations() {
    const loadingOverlay = document.getElementById('loading-overlay');
    
    loadingOverlay.classList.remove('d-none');
    
    // Clear cache and reload locations
    fetch('/test-kml')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Redirect to the same page (enhanced or regular) after refresh
                const currentRoute = '{{ Route::currentRouteName() }}';
                if (currentRoute === 'enhanced-maps-list') {
                    window.location.href = '{{ route("enhanced-maps-list") }}';
                } else {
                    window.location.href = '{{ route("maps-list") }}';
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error || 'Failed to refresh locations'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to refresh locations. Please try again.'
            });
        })
        .finally(() => {
            loadingOverlay.classList.add('d-none');
        });
}

function showLocationDetails(locationJson) {
    const location = JSON.parse(locationJson);
    const modal = new bootstrap.Modal(document.getElementById('locationDetailsModal'));
    const detailsContainer = document.getElementById('locationDetails');
    
    let servicesHtml = '';
    if (location.services && location.services.length > 0) {
        servicesHtml = `
            <h6>Available Services:</h6>
            <ul>
                ${location.services.map(service => `<li>${service}</li>`).join('')}
            </ul>
        `;
    }
    
    detailsContainer.innerHTML = `
        <h5>${location.title}</h5>
        <p class="text-muted">${location.description}</p>
        <div class="mb-3">
            <strong>Status:</strong> 
            <span class="badge bg-${location.status === 'operational' ? 'success' : (location.status === 'maintenance' ? 'warning' : 'danger')}">
                ${location.status.charAt(0).toUpperCase() + location.status.slice(1)}
            </span>
        </div>
        <div class="mb-3">
            <strong>Type:</strong> ${location.type}
        </div>
        <div class="mb-3">
            <strong>Operating Hours:</strong> ${location.hours}
        </div>
        <div class="mb-3">
            <strong>Location:</strong><br>
            Latitude: ${location.lat}<br>
            Longitude: ${location.lng}
        </div>
        ${servicesHtml}
        <div class="mt-3">
            <a href="https://www.google.com/maps?q=${location.lat},${location.lng}" 
               target="_blank" 
               class="btn btn-primary btn-sm">
                <i class="fas fa-map-marker-alt me-1"></i>View on Google Maps
            </a>
        </div>
    `;
    
    modal.show();
}

// Initialize DataTables
$(document).ready(function() {
    $('.location-table').each(function() {
        $(this).DataTable({
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search locations..."
            },
            pageLength: 5,
            lengthMenu: [[5, 10, 25, -1], [5, 10, 25, "All"]],
            dom: '<"d-flex justify-content-between align-items-center mb-3"lf>rt<"d-flex justify-content-between align-items-center"ip>',
            columnDefs: [
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: 1 },
                { responsivePriority: 3, targets: -1 }
            ]
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.card {
    transition: transform 0.2s;
    overflow: hidden;
    margin-bottom: 1rem;
}
.card:hover {
    transform: translateY(-5px);
}
.card-title {
    font-size: 0.875rem;
    font-weight: 500;
}
.table th {
    background-color: #f8f9fa;
    white-space: nowrap;
}
.badge {
    font-weight: 500;
}
.btn-group .btn {
    padding: 0.25rem 0.5rem;
}
.nav-tabs .nav-link {
    font-weight: 500;
}
.nav-tabs .nav-link.active {
    font-weight: 600;
}
#loading-overlay {
    backdrop-filter: blur(3px);
}
.dataTables_filter {
    margin-bottom: 0;
}
.dataTables_filter input {
    border-radius: 5px;
    border: 1px solid #ced4da;
    padding: 0.375rem 0.75rem;
}
</style>
@endpush 