@extends('layouts.app')

@section('title', __('Map View') . ' - JOIL YASEEIR')

@section('content')
    <div class="container-fluid p-0 h-100 position-relative">
        <!-- Direct Map Embed - More reliable than the component -->
        <div id="map-container" class="w-100 h-100">
            <iframe id="google-my-map" 
                src="https://www.google.com/maps/d/embed?mid=1_CRPlXxV43UXnOjAVk4OV-4KCbhgcSI&ll=22.70688634283307%2C50.54282924889061&z=6" 
                style="width: 100%; height: 100%; border: 0;" 
                allowfullscreen>
            </iframe>
        </div>
        
        <!-- Map Controls Container -->
        <div class="map-controls-container">
            <button id="get-nearest-station" class="btn btn-primary btn-lg">
                <i class="fas fa-location-arrow me-2"></i>{{ __('map_interface.get_me_nearest_station') }}
            </button>
            <div id="location-status" class="alert alert-info d-none mt-2"></div>
        </div>
        
        <!-- Map Error Message (hidden by default) -->
        <div id="map-error-container" class="position-absolute top-50 start-50 translate-middle bg-white p-4 rounded shadow-lg d-none">
            <div class="text-center">
                <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                <h4>{{ __('map_interface.unable_to_load_map') }}</h4>
                <p class="mb-3">{{ __('map_interface.map_loading_trouble') }}</p>
                <button id="reload-map-btn" class="btn btn-primary">
                    <i class="fas fa-sync-alt me-2"></i>{{ __('map_interface.reload_map') }}
                </button>
            </div>
        </div>
        
        <!-- Results Modal -->
        <div class="modal fade" id="nearestStationModal" tabindex="-1" aria-labelledby="nearestStationModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="nearestStationModalLabel">{{ __('map_interface.nearest_station') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                    </div>
                    <div class="modal-body" id="nearest-location-details">
                        <!-- Will be filled by JavaScript -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <a id="get-directions-link" href="#" class="btn btn-success" target="_blank">
                            <i class="fas fa-directions me-1"></i>{{ __('map_interface.get_directions') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Nearest Location Toast Notification -->
    <div class="position-fixed top-50 start-50 translate-middle" style="z-index: 9999;">
        <div id="nearestLocationToast" class="toast align-items-center text-white bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-map-marker-alt me-2"></i><span id="nearest-toast-message"></span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="{{ __('Close') }}"></button>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* Ensure map container takes up full available space */
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        overflow: hidden;
    }
    
    #app, main, #content, .container-fluid {
        height: 100%;
        padding: 0 !important;
        margin: 0 !important;
        overflow: hidden;
    }
    
    #map-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
    }
    
    /* Map controls container */
    .map-controls-container {
        position: absolute;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1000;
        text-align: center;
        width: 90%;
        max-width: 400px;
    }
    
    /* Make the button more prominent */
    #get-nearest-station {
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        font-weight: bold;
        padding: 12px 24px;
    }
    
    /* Alert styling */
    #location-status {
        background-color: rgba(255, 255, 255, 0.9);
        border-radius: 4px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }
    
    #map-error-container {
        z-index: 2000;
        width: 90%;
        max-width: 400px;
    }

    .container-fluid.p-0.h-100.position-relative {
    min-height: 500px !important;
}
</style>
@endpush

@push('scripts')
<script>
    // Laravel translations for JavaScript
    window.translations = {!! json_encode([
        'get_me_nearest_station' => __('map_interface.get_me_nearest_station'),
        'finding_nearest_station' => __('map_interface.finding_nearest_station'),
        'getting_your_location' => __('map_interface.getting_your_location'),
        'finding_nearest_station_progress' => __('map_interface.finding_nearest_station'),
        'location_access_denied' => __('map_interface.location_access_denied'),
        'location_unavailable' => __('map_interface.location_unavailable'),
        'location_timeout' => __('map_interface.location_timeout'),
        'geolocation_not_supported' => __('map_interface.geolocation_not_supported'),
        'no_stations_found' => __('map_interface.no_stations_found'),
        'error_finding_station' => __('map_interface.error_finding_station'),
        'server_error' => __('map_interface.server_error'),
        'no_response_from_server' => __('map_interface.no_response_from_server'),
        'retry' => __('map_interface.retry'),
        'found_nearest_location' => __('map_interface.found_nearest_location'),
        'nearest_location' => __('map_interface.nearest_location'),
        'km' => __('map_interface.km'),
        'est_time' => __('map_interface.est_time'),
        'min' => __('map_interface.min'),
        'distance' => __('map_interface.distance'),
        'rfid_station' => __('map_interface.rfid_station'),
        'nearest_joil_station' => __('map_interface.nearest_joil_station'),
        'unable_to_get_location' => __('map_interface.unable_to_get_location')
    ]) !!};

    // Translation helper function
    function __(key) {
        return window.translations[key] || key;
    }

    document.addEventListener('DOMContentLoaded', function() {
        console.log('Map view loaded');
        const mapIframe = document.getElementById('google-my-map');
        const errorContainer = document.getElementById('map-error-container');
        const reloadBtn = document.getElementById('reload-map-btn');
        const nearestStationBtn = document.getElementById('get-nearest-station');
        
        // Initialize Bootstrap components
        let nearestLocationToast = null;
        let nearestStationModal = null;
        
        try {
            nearestLocationToast = new bootstrap.Toast(document.getElementById('nearestLocationToast'), {
                delay: 5000 // Auto-hide after 5 seconds
            });
            
            nearestStationModal = new bootstrap.Modal(document.getElementById('nearestStationModal'));
        } catch(e) {
            console.error('Error initializing Bootstrap components:', e);
        }
        
        // Check if iframe loaded successfully
        mapIframe.addEventListener('load', function() {
            console.log('Map iframe loaded');
            // Hide error message if it was shown
            errorContainer.classList.add('d-none');
        });
        
        // Handle iframe load error
        mapIframe.addEventListener('error', function() {
            console.error('Error loading map iframe');
            errorContainer.classList.remove('d-none');
        });
        
        // Add reload functionality
        reloadBtn.addEventListener('click', function() {
            console.log('Reloading map');
            // Refresh the iframe
            const currentSrc = mapIframe.src;
            mapIframe.src = '';
            setTimeout(() => {
                mapIframe.src = currentSrc;
            }, 100);
        });
        
        // Add event listener for the Get Me Nearest Station button
        if (nearestStationBtn) {
            nearestStationBtn.addEventListener('click', getNearestStation);
        }
        
        // Set a timeout to check if the map is visible
        setTimeout(function() {
            // Try to detect if iframe content is not available
            const iframeDisplayStyle = window.getComputedStyle(mapIframe).display;
            
            if (iframeDisplayStyle === 'none' || mapIframe.offsetHeight < 10) {
                console.warn('Map iframe appears to be hidden or not loaded properly');
                errorContainer.classList.remove('d-none');
            } else {
                console.log('Map appears to be displayed correctly');
            }
        }, 3000);
        
        // Find nearest station function
        function getNearestStation() {
            // Prevent duplicate requests
            if (window.isRequestInProgress) return;
            
            const statusDiv = document.getElementById('location-status');
            
            // Set request in progress
            window.isRequestInProgress = true;
            nearestStationBtn.disabled = true;
            nearestStationBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>' + __('finding_nearest_station');
            
            statusDiv.classList.remove('d-none', 'alert-danger', 'alert-success', 'alert-info', 'alert-warning');
            statusDiv.classList.add('alert-info');
            statusDiv.textContent = __('getting_your_location');
            
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        window.userLocation = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
                        
                        statusDiv.textContent = __('finding_nearest_station_progress');
                        
                        // Get the CSRF token
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        
                        // Call the API to find the nearest station
                        axios.post('/nearest-station', window.userLocation, {
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            }
                        })
                        .then(response => {
                            window.isRequestInProgress = false;
                            nearestStationBtn.disabled = false;
                            nearestStationBtn.innerHTML = '<i class="fas fa-location-arrow me-2"></i>' + __('get_me_nearest_station');
                            
                            if (response.data.success) {
                                window.nearestLocation = response.data.station;
                                
                                // Update Google My Maps to center on the nearest location and zoom in
                                updateMapLocation(window.nearestLocation.lat, window.nearestLocation.lng, 16);
                                
                                // Show nearest location toast notification
                                document.getElementById('nearest-toast-message').textContent = 
                                    __('nearest_location') + ': ' + window.nearestLocation.title + ' (' + window.nearestLocation.distance.toFixed(1) + ' ' + __('km') + ')';
                                
                                if (nearestLocationToast) {
                                    nearestLocationToast.show();
                                }
                                
                                // Update status message
                                statusDiv.classList.remove('alert-info');
                                statusDiv.classList.add('alert-success');
                                statusDiv.innerHTML = `
                                    <i class="fas fa-check-circle me-2"></i>${__('found_nearest_location')}: <strong>${window.nearestLocation.title}</strong> 
                                    <span class="badge bg-secondary"><i class="fas fa-road me-1"></i>${window.nearestLocation.distance.toFixed(1)} ${__('km')}</span>
                                `;
                                
                                // Show station details in modal
                                displayNearestLocation(window.nearestLocation);
                                
                                if (nearestStationModal) {
                                    nearestStationModal.show();
                                }
                                
                                // Hide status after a delay
                                setTimeout(() => {
                                    statusDiv.classList.add('d-none');
                                }, 5000);
                            } else {
                                statusDiv.classList.remove('alert-info');
                                statusDiv.classList.add('alert-warning');
                                statusDiv.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>' + __('no_stations_found');
                            }
                        })
                        .catch(error => {
                            console.error('Error finding nearest station:', error);
                            window.isRequestInProgress = false;
                            nearestStationBtn.disabled = false;
                            nearestStationBtn.innerHTML = '<i class="fas fa-location-arrow me-2"></i>' + __('get_me_nearest_station');
                            
                            statusDiv.classList.remove('alert-info');
                            statusDiv.classList.add('alert-danger');
                            
                            let errorMessage = __('error_finding_station');
                            if (error.response) {
                                errorMessage = error.response.data.error || __('server_error');
                            } else if (error.request) {
                                errorMessage = __('no_response_from_server');
                            }
                            
                            statusDiv.innerHTML = `
                                <i class="fas fa-exclamation-circle me-2"></i>${errorMessage}
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-primary retry-button">
                                        <i class="fas fa-redo me-1"></i>${__('retry')}
                                    </button>
                                </div>
                            `;
                            
                            // Add retry functionality
                            document.querySelector('.retry-button').addEventListener('click', getNearestStation);
                        });
                    },
                    (error) => {
                        console.error('Geolocation error:', error);
                        window.isRequestInProgress = false;
                        nearestStationBtn.disabled = false;
                        nearestStationBtn.innerHTML = '<i class="fas fa-location-arrow me-2"></i>' + __('get_me_nearest_station');
                        
                        statusDiv.classList.remove('alert-info');
                        statusDiv.classList.add('alert-danger');
                        
                        let errorMessage = __('unable_to_get_location');
                        if (error.code === 1) {
                            errorMessage = __('location_access_denied');
                        } else if (error.code === 2) {
                            errorMessage = __('location_unavailable');
                        } else if (error.code === 3) {
                            errorMessage = __('location_timeout');
                        }
                        
                        statusDiv.innerHTML = `
                            <i class="fas fa-exclamation-circle me-2"></i>${errorMessage}
                            <div class="mt-2">
                                <button class="btn btn-sm btn-primary retry-button">
                                    <i class="fas fa-redo me-1"></i>${__('retry')}
                                </button>
                            </div>
                        `;
                        
                        // Add retry functionality
                        document.querySelector('.retry-button').addEventListener('click', getNearestStation);
                    }
                );
            } else {
                window.isRequestInProgress = false;
                nearestStationBtn.disabled = false;
                nearestStationBtn.innerHTML = '<i class="fas fa-location-arrow me-2"></i>' + __('get_me_nearest_station');
                
                statusDiv.classList.remove('alert-info');
                statusDiv.classList.add('alert-danger');
                statusDiv.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>' + __('geolocation_not_supported');
            }
        }
        
        function updateMapLocation(lat, lng, zoom = 14) {
            // Update the iframe src to center on the new location with specified zoom level
            const iframe = document.getElementById('google-my-map');
            if (iframe) {
                // Create new URL with embed format
                const newSrc = `https://www.google.com/maps/d/embed?mid=1_CRPlXxV43UXnOjAVk4OV-4KCbhgcSI&ll=${lat}%2C${lng}&z=${zoom}`;
                
                iframe.src = newSrc;
            }
        }
        
        function displayNearestLocation(location) {
            const detailsContainer = document.getElementById('nearest-location-details');
            const directionsLink = document.getElementById('get-directions-link');
            
            if (detailsContainer && directionsLink) {
                // Update modal title
                document.getElementById('nearestStationModalLabel').textContent = location.title;
                
                // Set directions link
                directionsLink.href = `https://www.google.com/maps/dir/?api=1&destination=${location.lat},${location.lng}`;
                
                // Format the content
                detailsContainer.innerHTML = `
                    <div class="card border-0">
                        <div class="card-body p-0">
                            <p class="lead mb-3">${location.description || __('rfid_station')}</p>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-primary fs-6 p-2">
                                    <i class="fas fa-road me-1"></i> ${__('distance')}: ${location.distance.toFixed(1)} ${__('km')}
                                </span>
                                <span class="badge bg-success fs-6 p-2">
                                    <i class="fas fa-clock me-1"></i> ${__('est_time')}: ${Math.round(location.distance * 1.5)} ${__('min')}
                                </span>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                ${__('nearest_joil_station')}
                            </div>
                        </div>
                    </div>
                `;
            }
        }
    });
</script>
@endpush 