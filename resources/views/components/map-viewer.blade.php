@props([
    'mapId' => '1_CRPlXxV43UXnOjAVk4OV-4KCbhgcSI',
    'center' => '22.70688634283307, 50.54282924889061',
    'zoom' => 6,
    'height' => '100%',
    'width' => '100%',
    'controls' => true,
    'showNearestButton' => true,
    'fullPage' => false
])

<div class="{{ $fullPage ? 'map-full-container' : 'map-container' }}">
    @if($controls && $showNearestButton)
    <!-- Map Controls Container -->
    <div class="map-controls-container">
        <button id="get-nearest-station" class="btn btn-primary btn-lg">
            <i class="fas fa-location-arrow me-2"></i>Get Me Nearest Station
        </button>
        <div id="location-status" class="alert alert-info d-none mt-2"></div>
    </div>
    @endif
    
    <!-- Google My Maps Embed -->
    <div id="map-container" class="{{ !$fullPage ? 'map-standard-container' : '' }}">
        <iframe id="google-my-map" 
                src="https://www.google.com/maps/d/embed?mid={{ $mapId }}&ll={{ $center }}&z={{ $zoom }}" 
                style="{{ !$fullPage ? 'height: ' . $height . '; width: ' . $width . ';' : '' }}"  
                sandbox="allow-scripts allow-same-origin allow-forms allow-popups allow-popups-to-escape-sandbox" 
                allowfullscreen
                loading="lazy"
                onerror="handleMapError()"></iframe>
        
        <!-- Error Message (hidden by default) -->
        <div id="map-error-message" class="alert alert-danger m-3" style="display: none;">
            <h5><i class="fas fa-exclamation-triangle me-2"></i>Map Loading Error</h5>
            <p>Unable to load the map. This could be due to:</p>
            <ul>
                <li>Network connectivity issues</li>
                <li>Browser security settings blocking the map</li>
                <li>The map might have been removed or made private</li>
            </ul>
            <p class="mb-0">Please try refreshing the page or contact support if the issue persists.</p>
        </div>
    </div>
    
    <!-- Results Modal -->
    <div class="modal fade" id="nearestStationModal" tabindex="-1" aria-labelledby="nearestStationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="nearestStationModalLabel">Nearest Station</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="nearest-location-details">
                    <!-- Will be filled by JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a id="get-directions-link" href="#" class="btn btn-success" target="_blank">
                        <i class="fas fa-directions me-1"></i>Get Directions
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
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<style>
    /* Full height map container */
    .map-full-container {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100%;
        height: 100vh;
        margin: 0;
        padding: 0;
        overflow: hidden;
    }
    
    /* Standard map container */
    .map-container {
        position: relative;
        width: 100%;
        height: 100%;
    }
    
    .map-standard-container {
        position: relative;
        border-radius: 4px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
    
    /* Map iframe */
    #map-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
    
    #google-my-map {
        width: 100%;
        height: 100%;
        border: 0;
    }
    
    /* Alert styling */
    #location-status {
        background-color: rgba(255, 255, 255, 0.9);
        border-radius: 4px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }
    
    /* Make the button more prominent */
    #get-nearest-station {
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        font-weight: bold;
        padding: 12px 24px;
    }
</style>

@once
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize variables
        window.isRequestInProgress = false;
        window.userLocation = null;
        window.nearestLocation = null;
        
        // Initialize Bootstrap components
        window.nearestLocationToast = new bootstrap.Toast(document.getElementById('nearestLocationToast'));
        window.nearestStationModal = new bootstrap.Modal(document.getElementById('nearestStationModal'));
        
        // Setup CSRF token for Axios
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
        }
        
        // Add event listener to the nearest station button
        const nearestStationBtn = document.getElementById('get-nearest-station');
        if (nearestStationBtn) {
            nearestStationBtn.addEventListener('click', getNearestStation);
        }
        
        function getNearestStation() {
            if (window.isRequestInProgress) return;
            
            const statusDiv = document.getElementById('location-status');
            const button = document.getElementById('get-nearest-station');
            
            // Set request in progress
            window.isRequestInProgress = true;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Finding nearest station...';
            
            showStatus('info', 'Getting your location...');
            
            if (!navigator.geolocation) {
                handleError('Geolocation is not supported by your browser.', button);
                return;
            }
            
            navigator.geolocation.getCurrentPosition(
                // Success callback
                (position) => {
                    window.userLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    
                    showStatus('info', 'Finding nearest station...');
                    
                    // Call the API to find the nearest station
                    axios.post('/nearest-station', window.userLocation)
                        .then(response => {
                            resetRequestState(button);
                            
                            if (response.data.success) {
                                window.nearestLocation = response.data.station;
                                // Store nearby stations if provided in the response
                                if (response.data.nearby_stations) {
                                    window.nearbyStationsResponse = response.data.nearby_stations;
                                }
                                handleSuccess(window.nearestLocation);
                            } else {
                                showStatus('warning', 'No stations found nearby. Please try again later.');
                            }
                        })
                        .catch(error => {
                            resetRequestState(button);
                            handleApiError(error);
                        });
                },
                // Error callback
                (error) => {
                    let message = 'Unable to get your location.';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            message = 'Location access denied. Please allow location access and try again.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            message = 'Location information is unavailable. Please try again.';
                            break;
                        case error.TIMEOUT:
                            message = 'Location request timed out. Please try again.';
                            break;
                    }
                    handleError(message, button);
                },
                // Options
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        }
        
        function resetRequestState(button) {
            window.isRequestInProgress = false;
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-location-arrow me-2"></i>Get Me Nearest Station';
        }
        
        function showStatus(type, message) {
            const statusDiv = document.getElementById('location-status');
            const icons = {
                info: 'info-circle',
                success: 'check-circle',
                warning: 'exclamation-triangle',
                danger: 'exclamation-circle'
            };
            
            statusDiv.className = `alert alert-${type} mt-2`;
            statusDiv.innerHTML = `<i class="fas fa-${icons[type]} me-2"></i>${message}`;
            statusDiv.classList.remove('d-none');
        }
        
        function handleError(message, button) {
            if (button) resetRequestState(button);
            showStatus('danger', message);
        }
        
        function handleApiError(error) {
            let message = 'Error finding nearest station. Please try again.';
            if (error.response) {
                if (error.response.status === 419) {
                    message = 'Session expired. Please refresh the page and try again.';
                } else if (error.response.data.error) {
                    message = error.response.data.error;
                }
            }
            showStatus('danger', message);
        }
        
        function handleSuccess(location) {
            // Store nearby stations if available in the response
            if (window.nearbyStationsResponse) {
                window.nearbyStations = window.nearbyStationsResponse;
            }
            
            // Update map location
            updateMapLocation(location.lat, location.lng, 16);
            
            // Show success message
            showStatus('success', `
                Found nearest location: <strong>${location.title}</strong> 
                <span class="badge bg-secondary"><i class="fas fa-road me-1"></i>${location.distance.toFixed(1)} km</span>
            `);
            
            // Show toast notification
            const toastMessage = document.getElementById('nearest-toast-message');
            if (toastMessage) {
                toastMessage.textContent = `Nearest location: ${location.title} (${location.distance.toFixed(1)} km)`;
                window.nearestLocationToast.show();
            }
            
            // Display location details and show modal
            displayNearestLocation(location);
            window.nearestStationModal.show();
            
            // Add custom marker to the map
            addMapMarker(location.lat, location.lng);
            
            // Hide status after delay
            setTimeout(() => {
                document.getElementById('location-status').classList.add('d-none');
            }, 5000);
        }
        
        function updateMapLocation(lat, lng, zoom = 14) {
            const iframe = document.getElementById('google-my-map');
            if (iframe) {
                // Save the current iframe src to check if it's actually changing
                const currentSrc = iframe.src;
                const newSrc = `https://www.google.com/maps/d/embed?mid={{ $mapId }}&ll=${lat}%2C${lng}&z=${zoom}`;
                
                // Only update if the source is different to avoid unnecessary reloads
                if (currentSrc !== newSrc) {
                    // Log the map update for debugging
                    console.log('Updating map to coordinates:', { lat, lng, zoom });
                    
                    // Force the map to update with new coordinates
                    iframe.src = newSrc;
                    
                    // Create a marker effect by briefly showing a visual indicator at the target location
                    const statusDiv = document.getElementById('location-status');
                    if (statusDiv) {
                        statusDiv.innerHTML += '<div class="text-center mt-2"><i class="fas fa-map-marker-alt text-danger pulse" style="font-size: 1.5rem;"></i></div>';
                        
                        // Remove the marker effect after 5 seconds
                        setTimeout(() => {
                            const pulseElement = statusDiv.querySelector('.pulse');
                            if (pulseElement) {
                                pulseElement.remove();
                            }
                        }, 5000);
                    }
                }
            }
        }
        
        // This function is a visual helper only - Google My Maps iframe doesn't allow direct marker addition
        function addMapMarker(lat, lng) {
            // Since we can't directly add markers to the Google My Maps iframe,
            // we'll create a visual indicator on the page
            const mapContainer = document.getElementById('map-container');
            
            if (!mapContainer) return;
            
            // Remove any existing markers
            const existingMarker = document.getElementById('map-location-marker');
            if (existingMarker) {
                existingMarker.remove();
            }
            
            // Create a marker element
            const marker = document.createElement('div');
            marker.id = 'map-location-marker';
            marker.className = 'map-marker-indicator pulse';
            marker.innerHTML = '<i class="fas fa-map-marker-alt"></i>';
            marker.title = 'Nearest Location';
            
            // Add to the map container
            mapContainer.appendChild(marker);
            
            // Add styling for the marker
            const style = document.createElement('style');
            style.textContent = `
                .map-marker-indicator {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    z-index: 1000;
                    color: #dc3545;
                    font-size: 2rem;
                    text-shadow: 0 0 5px rgba(255,255,255,0.8);
                    pointer-events: none;
                }
            `;
            document.head.appendChild(style);
            
            // Remove after 5 seconds
            setTimeout(() => {
                marker.remove();
            }, 5000);
        }
        
        function displayNearestLocation(location) {
            const detailsContainer = document.getElementById('nearest-location-details');
            const directionsLink = document.getElementById('get-directions-link');
            
            if (detailsContainer && directionsLink) {
                // Update modal title
                document.getElementById('nearestStationModalLabel').textContent = location.title;
                
                // Update directions link
                directionsLink.href = `https://www.google.com/maps/dir/?api=1&destination=${location.lat},${location.lng}`;
                
                // Build the content HTML
                let contentHTML = `
                    <div class="card border-0 mb-3">
                        <div class="card-body p-0">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-primary fs-6 p-2">
                                    <i class="fas fa-road me-1"></i> Distance: ${location.distance.toFixed(1)} km
                                </span>
                                <span class="badge bg-success fs-6 p-2">
                                    <i class="fas fa-clock me-1"></i> Est. Time: ${Math.round(location.distance * 1.5)} min
                                </span>
                            </div>`;
                
                // Add status badge if available
                if (location.status) {
                    let statusClass = 'secondary';
                    if (location.status.toLowerCase() === 'verified') statusClass = 'success';
                    else if (location.status.toLowerCase() === 'verification required') statusClass = 'success';
                    else if (location.status.toLowerCase() === 'under check') statusClass = 'warning';
                    
                    contentHTML += `
                        <div class="mb-3">
                            <span class="badge bg-${statusClass} p-2">
                                <i class="fas fa-info-circle me-1"></i> Status: ${location.status.charAt(0).toUpperCase() + location.status.slice(1)}
                            </span>
                        </div>`;
                }
                
                // Add description if available
                 
                 if (location.description) {
                    // contentHTML += `
                    //     <div class="alert alert-light mb-3">
                    //         <i class="fas fa-info-circle me-2 text-primary"></i>
                    //         ${location.description}
                    //     </div>`;
                }
                
                // Add city/region if available
                if (location.city || location.region) {
                    contentHTML += `
                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                ${location.city ? location.city : ''}
                                ${location.city && location.region ? ', ' : ''}
                                ${location.region ? location.region : ''}
                            </small>
                        </div>`;
                }
             /* I changed this  
                contentHTML += `
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                This is the nearest JOIL station to your current location.
                            </div>
                        </div>
                    </div>`;
                
                // If we have nearby stations info in the window object, show them
                if (window.nearbyStations && window.nearbyStations.length > 1) {
                    contentHTML += `
                        <div class="mt-3">
                            <h6 class="mb-2">Other Nearby Stations:</h6>
                            <div class="list-group">`;
                    
                    // Start from index 1 to skip the current station
                    for (let i = 1; i < Math.min(window.nearbyStations.length, 3); i++) {
                        const station = window.nearbyStations[i];
                        contentHTML += `
                            <a href="#" class="list-group-item list-group-item-action" 
                               onclick="updateMapLocation(${station.lat}, ${station.lng}, 16); return false;">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">${station.title}</h6>
                                    <small>${station.distance.toFixed(1)} km</small>
                                </div>
                                <small class="text-muted">${station.description || ''}</small>
                            </a>`;
                    }
                    
                    contentHTML += `
                            </div>
                        </div>`;
                }
                */
                contentHTML += `
                            </div>
                        </div>`;
                // Set the HTML content
                detailsContainer.innerHTML = contentHTML;
            }
        }
    });
</script>
@endonce 