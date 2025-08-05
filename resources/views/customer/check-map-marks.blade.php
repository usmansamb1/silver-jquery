@extends('layouts.app-minimal')

@section('title', __('Find Nearest Station') . ' - FuelApp - JOIL')

@section('content')
<div class="container-fluid px-0">
    <div class="row g-0">
        <div class="col-12">
            <!-- Map Loading Overlay -->
            <div id="map-loading-overlay" class="loading-overlay">
                <div class="loading-content">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">{{ __('Loading map...') }}</span>
                    </div>
                    <h5 class="mt-3 text-primary">{{ __('Loading Map') }}</h5>
                </div>
            </div>

            <!-- Location Search Loading Overlay -->
            <div id="location-loading-overlay" class="loading-overlay d-none">
                <div class="loading-content">
                    <div class="spinner-grow text-primary" role="status">
                        <span class="visually-hidden">{{ __('Finding location...') }}</span>
                    </div>
                    <h5 class="mt-3 text-primary" id="location-loading-text">{{ __('Getting Your Location') }}</h5>
                    <p class="text-muted small" id="location-loading-subtext">{{ __('Please allow location access when prompted') }}</p>
                </div>
            </div>

            <x-map-viewer 
                :fullPage="true"
                :showNearestButton="true"
                :controls="true"
            />
            
            <!-- Nearest Location Toast -->
            <div class="toast-container position-fixed bottom-0 end-0 p-3">
                <div id="nearestLocationToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                        <strong class="me-auto">{{ __('Location Found') }}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="{{ __('Close') }}"></button>
                    </div>
                    <div class="toast-body" id="nearest-toast-message"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .page-transition #wrapper:not(.animated) {
        position: relative;
        opacity: 1 !important;
        min-height: 500px;
    }

    .content-wrap {
        min-height: 400px !important;
        height: 100vh !important;
    }

    .map-full-container {
        position: relative;
        height: calc(100vh - 70px);
        width: 100%;
    }

    #map-container {
        height: 100%;
        width: 100%;
    }

    #google-my-map {
        height: 100%;
        width: 100%;
        border: none;
    }

    .map-controls-container {
        position: absolute;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1000;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    #location-status {
        min-width: 300px;
        text-align: center;
        background-color: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(5px);
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
    }

    .toast-container {
        z-index: 1100;
    }

    /* Loading Overlay Styles */
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(5px);
        z-index: 1200;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: opacity 0.3s ease-in-out;
    }

    .loading-content {
        text-align: center;
        padding: 2rem;
        background-color: rgba(255, 255, 255, 0.95);
        border-radius: 1rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .loading-content .spinner-border,
    .loading-content .spinner-grow {
        width: 3rem;
        height: 3rem;
    }

    /* Pulse Animation for Location Search */
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }

    .pulse {
        animation: pulse 2s infinite;
    }

    /* Enhanced Button Styles */
    #get-nearest-station {
        padding: 0.8rem 1.5rem;
        font-weight: 600;
        border-radius: 50px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
        background: linear-gradient(45deg, #007bff, #0056b3);
        border: none;
    }

    #get-nearest-station:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
    }

    #get-nearest-station:active {
        transform: translateY(1px);
    }

    #get-nearest-station:disabled {
        background: linear-gradient(45deg, #6c757d, #495057);
        transform: none;
    }

    /* Error State Styling */
    .alert-danger {
        background-color: rgba(220, 53, 69, 0.1);
        border: 1px solid rgba(220, 53, 69, 0.2);
        color: #dc3545;
    }

    /* Success State Styling */
    .alert-success {
        background-color: rgba(40, 167, 69, 0.1);
        border: 1px solid rgba(40, 167, 69, 0.2);
        color: #28a745;
    }
</style>
@endpush

@push('scripts')
<script>
// Translation strings
const translations = {
    failedToLoadMaps: "{{ __('Failed to load Google Maps') }}",
    gettingYourLocation: "{{ __('Getting Your Location') }}",
    pleaseAllowLocation: "{{ __('Please allow location access when prompted') }}",
    findingNearestStation: "{{ __('Finding Nearest Station') }}",
    calculatingDistances: "{{ __('Calculating distances...') }}",
    geolocationNotSupported: "{{ __('Geolocation is not supported by your browser.') }}",
    noStationsFound: "{{ __('No stations found nearby. Please try again later.') }}",
    unableToGetLocation: "{{ __('Unable to get your location.') }}",
    locationAccessDenied: "{{ __('Location access denied. Please allow location access and try again.') }}",
    locationUnavailable: "{{ __('Location information is unavailable. Please try again.') }}",
    locationTimeout: "{{ __('Location request timed out. Please try again.') }}",
    errorFindingStation: "{{ __('Error finding nearest station. Please try again.') }}",
    sessionExpired: "{{ __('Session expired. Please refresh the page and try again.') }}",
    nearestLocation: "{{ __('Nearest location') }}",
    foundNearestLocation: "{{ __('Found nearest location') }}",
    getMeNearestStation: "{{ __('Get Me Nearest Station') }}",
    mapLoadingTimeout: "{{ __('Map loading timeout') }}",
    googleMapsLoaded: "{{ __('Google Maps iframe loaded successfully') }}",
    km: "{{ __('km') }}"
};

// Handle map error
function handleMapError() {
    console.error(translations.failedToLoadMaps);
    const mapError = document.getElementById('map-error-message');
    const mapLoadingOverlay = document.getElementById('map-loading-overlay');
    if (mapError) {
        mapError.style.display = 'block';
    }
    if (mapLoadingOverlay) {
        mapLoadingOverlay.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const mapLoadingOverlay = document.getElementById('map-loading-overlay');
    const locationLoadingOverlay = document.getElementById('location-loading-overlay');
    const locationLoadingText = document.getElementById('location-loading-text');
    const locationLoadingSubtext = document.getElementById('location-loading-subtext');
    const mapIframe = document.getElementById('google-my-map');

    // Handle map loading
    if (mapIframe) {
        let mapLoaded = false;
        
        mapIframe.addEventListener('load', function() {
            mapLoaded = true;
            console.log(translations.googleMapsLoaded);
            setTimeout(() => {
                mapLoadingOverlay.style.opacity = '0';
                setTimeout(() => {
                    mapLoadingOverlay.style.display = 'none';
                }, 300);
            }, 500);
        });
        
        // Timeout handler - if map doesn't load within 10 seconds
        setTimeout(() => {
            if (!mapLoaded) {
                console.error(translations.mapLoadingTimeout);
                handleMapError();
            }
        }, 10000);
    }

    // Override the original getNearestStation function
    const originalGetNearestStation = window.getNearestStation;
    window.getNearestStation = function() {
        if (window.isRequestInProgress) return;

        const button = document.getElementById('get-nearest-station');
        
        // Show location loading overlay
        locationLoadingOverlay.classList.remove('d-none');
        locationLoadingText.textContent = translations.gettingYourLocation;
        locationLoadingSubtext.textContent = translations.pleaseAllowLocation;
        
        if (!navigator.geolocation) {
            handleError(translations.geolocationNotSupported);
            return;
        }

        window.isRequestInProgress = true;
        button.disabled = true;

        navigator.geolocation.getCurrentPosition(
            (position) => {
                locationLoadingText.textContent = translations.findingNearestStation;
                locationLoadingSubtext.textContent = translations.calculatingDistances;

                window.userLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };

                // Call the API to find the nearest station
                axios.post('/nearest-station', window.userLocation)
                    .then(response => {
                        if (response.data.success) {
                            window.nearestLocation = response.data.station;
                            // Store nearby stations if provided
                            if (response.data.nearby_stations) {
                                window.nearbyStations = response.data.nearby_stations;
                            }
                            handleSuccess(window.nearestLocation);
                        } else {
                            handleError(translations.noStationsFound);
                        }
                    })
                    .catch(error => handleApiError(error))
                    .finally(() => {
                        resetUI(button);
                    });
            },
            (error) => {
                handleGeolocationError(error, button);
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    };

    function resetUI(button) {
        window.isRequestInProgress = false;
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-location-arrow me-2"></i>' + translations.getMeNearestStation;
        
        // Hide location loading overlay
        locationLoadingOverlay.style.opacity = '0';
        setTimeout(() => {
            locationLoadingOverlay.classList.add('d-none');
            locationLoadingOverlay.style.opacity = '1';
        }, 300);
    }

    function handleError(message) {
        showStatus('danger', message);
        resetUI(document.getElementById('get-nearest-station'));
    }

    function handleGeolocationError(error, button) {
        let message = translations.unableToGetLocation;
        switch(error.code) {
            case error.PERMISSION_DENIED:
                message = translations.locationAccessDenied;
                break;
            case error.POSITION_UNAVAILABLE:
                message = translations.locationUnavailable;
                break;
            case error.TIMEOUT:
                message = translations.locationTimeout;
                break;
        }
        handleError(message);
    }

    function handleApiError(error) {
        let message = translations.errorFindingStation;
        if (error.response) {
            if (error.response.status === 419) {
                message = translations.sessionExpired;
            } else if (error.response.data.error) {
                message = error.response.data.error;
            }
        }
        handleError(message);
    }

    function handleSuccess(location) {
        updateMapLocation(location.lat, location.lng, 16);
        showSuccessNotifications(location);
    }

    function showSuccessNotifications(location) {
        // Show toast
        document.getElementById('nearest-toast-message').textContent =
            `${translations.nearestLocation}: ${location.title} (${location.distance.toFixed(1)} ${translations.km})`;
        window.nearestLocationToast.show();

        // Show success status
        showStatus('success', `
            ${translations.foundNearestLocation}: <strong>${location.title}</strong>
            <span class="badge bg-secondary"><i class="fas fa-road me-1"></i>${location.distance.toFixed(1)} ${translations.km}</span>
        `);

        // Show modal with details
        displayNearestLocation(location);
        window.nearestStationModal.show();
    }
});
</script>
@endpush 