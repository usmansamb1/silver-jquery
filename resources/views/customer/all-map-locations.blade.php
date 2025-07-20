@extends('layouts.app')
@section('title', 'All Map Locations - JOIL YASEEIR')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>
            <i class="fas fa-map-marker-alt me-2"></i> 
            All Marked Locations - JOIL YASEEIR
        </h3>
        <div>
            <a href="{{ route('maps-list') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-th-list me-1"></i> Standard View
            </a>
            <a href="{{ route('all-map-locations') }}" class="btn btn-primary me-2">
                <i class="fas fa-sync-alt me-1"></i> Refresh Data
            </a>
            <a href="{{ route('database-maps-list') }}" class="btn btn-success">
                <i class="fas fa-database me-1"></i> View from Database
            </a>
        </div>
    </div>

    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        Displaying all location data from Google My Maps KML feed:
        <a href="https://www.google.com/maps/d/u/0/viewer?mid=1_CRPlXxV43UXnOjAVk4OV-4KCbhgcSI" 
           target="_blank" class="alert-link">
            View Original Map <i class="fas fa-external-link-alt ms-1"></i>
        </a>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Locations</h6>
                    <h2 class="mb-0">{{ count($locations) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Unique Location Names</h6>
                    <h2 class="mb-0">{{ count(array_unique(array_column($locations, 'title'))) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Last Updated</h6>
                    <h2 class="mb-0">{{ date('d M Y H:i') }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="allLocationsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Latitude</th>
                            <th>Longitude</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($locations as $loc)
                        <tr>
                            <td>{{ $loc['title'] }}</td>
                            <td>
                                <div class="text-truncate" style="max-width: 250px;">
                                    {{ $loc['description'] ?? 'No description available' }}
                                </div>
                            </td>
                            <td>{{ number_format($loc['lat'], 6) }}</td>
                            <td>{{ number_format($loc['lng'], 6) }}</td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ $loc['type'] ?? 'RFID Station' }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $status = $loc['status'] ?? 'operational';
                                    $statusClass = $status === 'operational' ? 'success' : ($status === 'maintenance' ? 'warning' : 'danger');
                                @endphp
                                <span class="badge bg-{{ $statusClass }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="https://www.google.com/maps?q={{ $loc['lat'] }},{{ $loc['lng'] }}" 
                                       target="_blank" 
                                       class="btn btn-info" 
                                       title="View on Google Maps">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-primary"
                                            onclick="showLocationDetails({{ json_encode($loc) }})"
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a id="viewOnMapBtn" href="#" target="_blank" class="btn btn-primary">
                    <i class="fas fa-map-marker-alt me-1"></i> View on Map
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#allLocationsTable').DataTable({
        responsive: true,
        pageLength: 25,
        language: { 
            search: "_INPUT_", 
            searchPlaceholder: "Search locations...",
            lengthMenu: "_MENU_ locations per page"
        },
        dom: '<"d-flex justify-content-between align-items-center mb-3"lf>rt<"d-flex justify-content-between align-items-center"ip>',
        order: [[0, 'asc']]
    });
});

function showLocationDetails(location) {
    const modal = new bootstrap.Modal(document.getElementById('locationDetailsModal'));
    const detailsContainer = document.getElementById('locationDetails');
    const mapBtn = document.getElementById('viewOnMapBtn');
    
    // Update map link
    mapBtn.href = `https://www.google.com/maps?q=${location.lat},${location.lng}`;
    
    // Format location type and status with badges
    const typeClass = 'secondary';
    const statusClass = location.status === 'operational' ? 'success' : 
                       (location.status === 'maintenance' ? 'warning' : 'danger');
                       
    const statusText = location.status ? (location.status.charAt(0).toUpperCase() + location.status.slice(1)) : 'Operational';
    const typeText = location.type || 'RFID Station';
    
    // Update details content
    detailsContainer.innerHTML = `
        <h4>${location.title}</h4>
        <div class="mb-3">
            <span class="badge bg-${typeClass} me-2">${typeText}</span>
            <span class="badge bg-${statusClass}">${statusText}</span>
        </div>
        
        <div class="mb-3">
            <h6>Description:</h6>
            <p class="text-muted">${location.description || 'No description available'}</p>
        </div>
        
        <div class="mb-3">
            <h6>Location Coordinates:</h6>
            <div class="row">
                <div class="col-md-6">
                    <strong>Latitude:</strong> ${Number(location.lat).toFixed(6)}
                </div>
                <div class="col-md-6">
                    <strong>Longitude:</strong> ${Number(location.lng).toFixed(6)}
                </div>
            </div>
        </div>
        
        <div class="mb-3">
            <h6>Services:</h6>
            <p>${location.services?.length > 0 
                ? location.services.map(s => `<span class="badge bg-info me-1">${s}</span>`).join('') 
                : '<span class="text-muted">No services specified</span>'}
            </p>
        </div>
        
        <div class="mb-3">
            <h6>Hours:</h6>
            <p>${location.hours || 'Standard Hours'}</p>
        </div>
    `;
    
    modal.show();
}
</script>
@endpush

@push('styles')
<style>
.table th {
    white-space: nowrap;
}
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    margin-bottom: 1.5rem;
}
.dataTables_filter {
    margin-bottom: 0;
}
.dataTables_filter input {
    border-radius: 5px;
    border: 1px solid #ced4da;
    padding: 0.375rem 0.75rem;
}
.badge {
    font-weight: 500;
}
</style>
@endpush 