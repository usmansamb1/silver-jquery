@extends('layouts.app')

@section('title', 'Map Locations - JOIL YASEEIR')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-map-marker-alt me-2"></i>Map Locations
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <p class="lead">Find our service stations and pick-up points across the region.</p>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <!-- Find Near Me Button -->
                                <button id="find-near-me" class="btn btn-primary w-100">
                                    <i class="fas fa-location-arrow me-2"></i>Find Near Me
                                </button>
                            </div>
                            <div class="col-md-6">
                                <!-- Book at Nearest Location Button -->
                                <button id="book-nearest" class="btn btn-success w-100" disabled>
                                    <i class="fas fa-calendar-check me-2"></i>Book at Nearest Location
                                </button>
                            </div>
                        </div>
                        
                        <div id="location-status" class="alert alert-info d-none mb-3"></div>
                        
                        <!-- Nearest Location Card (previously here - moved below the iframe) -->
                    </div>
                    
                    <!-- Google My Maps Embed -->
                    <div class="card mb-3">
                        <div class="card-body p-0">
                            <div id="map-container">
                                <iframe id="google-my-map" src="https://www.google.com/maps/d/embed?mid=1jd-3oRTv5ySoNWJ8J6dknqfj0bA5J61h&hl=en&femb=1&z=9" 
                                        style="height: 500px; width: 100%; border: 0;" allowfullscreen></iframe>
                                
                                <!-- Loading Overlay -->
                                <div id="map-loading-overlay" class="map-overlay d-none">
                                    <div class="d-flex flex-column align-items-center justify-content-center h-100">
                                        <div class="spinner-border text-primary mb-3" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <h5 class="text-white mb-0" id="loading-message">Finding nearest stations...</h5>
                                    </div>
                                </div>
                                
                                <!-- Map Popup for Location Details -->
                                <div id="map-popup" class="map-popup d-none">
                                    <div class="map-popup-content">
                                        <h6 id="popup-title" class="mb-2"></h6>
                                        <p id="popup-details" class="small mb-2"></p>
                                        <div class="station-services mb-2">
                                            <span class="badge bg-info me-1" id="station-type"></span>
                                            <span class="badge bg-success me-1" id="station-status"></span>
                                        </div>
                                        <div class="station-info small mb-2">
                                            <p class="mb-1"><i class="fas fa-clock me-2"></i><span id="station-hours">24/7</span></p>
                                            <p class="mb-1"><i class="fas fa-car me-2"></i><span id="station-services">Loading services...</span></p>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <span id="popup-distance" class="badge bg-secondary"></span>
                                            <div class="btn-group">
                                                <a id="popup-directions" href="#" class="btn btn-sm btn-success" target="_blank">
                                                    <i class="fas fa-directions me-1"></i>Directions
                                                </a>
                                                <button class="btn btn-sm btn-primary" onclick="closeMapPopup()">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Nearest Location Card (now moved below the iframe) -->
                    <div id="nearest-location-card" class="card d-none mb-3">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-map-pin me-2"></i>Nearest Location</h5>
                            <div id="nearest-location-details">
                                <!-- Will be filled by JavaScript -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Location information section -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Location Types</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <h6 class="card-title d-flex align-items-center">
                                                <div style="width: 15px; height: 15px; background-color: #4285F4; border-radius: 50%; margin-right: 8px;"></div>
                                                RFID Stations
                                            </h6>
                                            <p class="card-text">Fuel stations equipped with RFID payment technology for seamless transactions.</p>
                                            <ul class="mb-0">
                                                <li>24/7 access with your RFID card</li>
                                                <li>Automated payment processing</li>
                                                <li>Digital receipts</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <h6 class="card-title d-flex align-items-center">
                                                <div style="width: 15px; height: 15px; background-color: #EA4335; border-radius: 50%; margin-right: 8px;"></div>
                                                Service Points
                                            </h6>
                                            <p class="card-text">Service centers offering maintenance and additional vehicle services.</p>
                                            <ul class="mb-0">
                                                <li>Vehicle maintenance services</li>
                                                <li>Car wash and cleaning</li>
                                                <li>Technical support</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Location Details Modal -->
<div class="modal fade" id="locationModal" tabindex="-1" aria-labelledby="locationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="locationModalLabel">Location Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="locationModalBody">
                <!-- Will be filled dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" id="book-service-btn" class="btn btn-primary">Book Service</a>
            </div>
        </div>
    </div>
</div>

<!-- Favorite Location Toast -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="favoriteToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <i class="fas fa-heart text-danger me-2"></i>
            <strong class="me-auto">Favorites Updated</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="favoriteToastBody">
            Location saved to favorites!
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

<!-- Error Toast -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="errorToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-danger text-white">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong class="me-auto">Error</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="errorToastMessage"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Google Maps API Key for development/testing
    const API_KEY = 'AIzaSyDev_0jHjLt31Z9dVUZPifVVNvKHhN5BvA';
    
    // Extract the Google My Maps ID from the iframe src
    const iframe = document.getElementById('google-my-map');
    const myMapsUrl = iframe.src;
    const myMapsId = myMapsUrl.match(/mid=([^&]+)/)[1];
    
    // Variables to store state
    let userLocation = null;
    let nearestLocation = null;
    let mapLocations = [];
    let locationModal = null;
    let favoriteToast = null;
    let nearestLocationToast = null;
    let userFavorites = [];
    let estimatedTravelTime = null;
    
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Modal and Toasts
        locationModal = new bootstrap.Modal(document.getElementById('locationModal'));
        favoriteToast = new bootstrap.Toast(document.getElementById('favoriteToast'));
        nearestLocationToast = new bootstrap.Toast(document.getElementById('nearestLocationToast'), {
            delay: 5000 // Auto-hide after 5 seconds
        });
        
        // Load favorites from local storage
        loadFavorites();
        
        // Fetch locations from KML data in the background
        fetchMapLocations();
        
        // Add event listener for the Find Near Me button
        document.getElementById('find-near-me').addEventListener('click', findNearMe);
        
        // Add event listener for the Book at Nearest button
        document.getElementById('book-nearest').addEventListener('click', function() {
            if (nearestLocation) {
                bookService(nearestLocation);
            }
        });
    });
    
    // Fetch the KML data from Google My Maps
    function fetchMapLocations() {
        // KML URL for Google My Maps
        const kmlUrl = `https://www.google.com/maps/d/kml?mid=${myMapsId}&forcekml=1`;
        
        // Don't show loading indicator - load in background
        // const statusDiv = document.getElementById('location-status');
        // statusDiv.classList.remove('d-none');
        // statusDiv.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading map locations...';
        
        console.log('Loading map locations in background...');
        
        // Fetch the KML data
        fetch('https://api.allorigins.win/get?url=' + encodeURIComponent(kmlUrl))
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Parse the KML data (it's in XML format)
                const parser = new DOMParser();
                const kml = parser.parseFromString(data.contents, 'text/xml');
                
                // Extract placemarks (markers)
                const placemarks = kml.getElementsByTagName('Placemark');
                
                // Process each placemark
                for (let i = 0; i < placemarks.length; i++) {
                    try {
                        const placemark = placemarks[i];
                        const name = placemark.getElementsByTagName('name')[0]?.textContent || 'Unnamed Location';
                        const description = placemark.getElementsByTagName('description')[0]?.textContent || '';
                        const point = placemark.getElementsByTagName('Point')[0];
                        
                        if (point) {
                            const coordinates = point.getElementsByTagName('coordinates')[0]?.textContent.split(',');
                            if (coordinates && coordinates.length >= 2) {
                                // Extract coordinates (longitude comes first in KML, so we swap them)
                                const lng = parseFloat(coordinates[0]);
                                const lat = parseFloat(coordinates[1]);
                                
                                // Parse the description to extract metadata
                                const parser = new DOMParser();
                                const descriptionHtml = parser.parseFromString(description, 'text/html');
                                
                                // Extract location type and other attributes from description
                                // This is a simplistic approach; in a real app, you'd have more structured data
                                const descriptionText = descriptionHtml.body.textContent;
                                const type = descriptionText.includes('RFID') ? 'RFID' : 'Service';
                                const address = getAddressFromDescription(descriptionText) || 'Address unavailable';
                                const hours = getHoursFromDescription(descriptionText) || '24/7';
                                const services = getServicesFromDescription(descriptionText);
                                
                                // Create a location object
                                const location = {
                                    title: name,
                                    lat: lat,
                                    lng: lng,
                                    type: type,
                                    address: address,
                                    services: services,
                                    hours: hours,
                                    // Default availability for demonstration - in production, this would come from your API
                                    availability: getAvailabilityForType(type)
                                };
                                
                                mapLocations.push(location);
                            }
                        }
                    } catch (error) {
                        console.error('Error processing placemark:', error);
                    }
                }
                
                // Update UI if locations were loaded
                if (mapLocations.length > 0) {
                    console.log(`Loaded ${mapLocations.length} locations from Google My Maps`);
                } else {
                    console.warn('No locations found in the map data. Using sample data instead.');
                    // Load sample data as fallback
                    loadSampleData();
                }
            })
            .catch(error => {
                console.error('Error fetching KML data:', error);
                
                // Load sample data as fallback
                loadSampleData();
            });
    }
    
    // Helper functions to extract data from description
    function getAddressFromDescription(description) {
        // Try to extract address-like information
        const addressMatch = description.match(/(located at|address|location):?\s*([^,\.]+)/i);
        return addressMatch ? addressMatch[2].trim() : '';
    }
    
    function getHoursFromDescription(description) {
        // Try to extract hours information
        const hoursMatch = description.match(/(hours|open|available):?\s*([^,\.]+)/i);
        return hoursMatch ? hoursMatch[2].trim() : '24/7';
    }
    
    function getServicesFromDescription(description) {
        const services = [];
        
        // Common services to look for
        const serviceKeywords = ['Fuel', 'Gas', 'Petrol', 'Diesel', 'Charging', 'EV', 'Car Wash', 'Maintenance', 'Market', 'Rest Area'];
        
        serviceKeywords.forEach(keyword => {
            if (description.includes(keyword)) {
                services.push(keyword);
            }
        });
        
        // Default services if none found
        if (services.length === 0) {
            if (description.toLowerCase().includes('rfid')) {
                services.push('Fuel');
            } else {
                services.push('Maintenance');
            }
        }
        
        return services;
    }
    
    // Generate sample availability data based on location type
    function getAvailabilityForType(type) {
        if (type === 'RFID') {
            const pumpsTotal = Math.floor(Math.random() * 4) + 3; // 3-6 pumps
            const pumpsAvailable = Math.max(1, Math.floor(Math.random() * pumpsTotal));
            const waitTime = Math.floor(Math.random() * 15) + 1; // 1-15 min wait
            
            let status;
            if (pumpsAvailable / pumpsTotal > 0.7) status = 'high';
            else if (pumpsAvailable / pumpsTotal > 0.3) status = 'medium';
            else status = 'low';
            
            return {
                status: status,
                fuelTypes: [
                    { type: "91", available: Math.random() > 0.1 }, // 10% chance unavailable
                    { type: "95", available: Math.random() > 0.2 }, // 20% chance unavailable
                    { type: "Diesel", available: Math.random() > 0.3 } // 30% chance unavailable
                ],
                waitTime: waitTime,
                pumpsAvailable: pumpsAvailable,
                pumpsTotal: pumpsTotal
            };
        } else {
            // Service location
            return {
                status: Math.random() > 0.7 ? 'high' : (Math.random() > 0.4 ? 'medium' : 'low'),
                serviceTypes: [
                    { type: "Car Wash", available: Math.random() > 0.2, waitTime: Math.floor(Math.random() * 20) + 10 },
                    { type: "Oil Change", available: Math.random() > 0.3, waitTime: Math.floor(Math.random() * 20) + 20 },
                    { type: "Tire Service", available: Math.random() > 0.2, waitTime: Math.floor(Math.random() * 20) + 15 }
                ]
            };
        }
    }
    
    // Load sample data as fallback
    function loadSampleData() {
        mapLocations = [
            { 
                title: "Riyadh Central Station", 
                lat: 24.7136, 
                lng: 46.6753, 
                type: "RFID", 
                address: "King Fahd Road, Riyadh", 
                services: ["Fuel", "Charging", "Car Wash"], 
                hours: "24/7",
                availability: { 
                    status: "high", 
                    fuelTypes: [
                        { type: "91", available: true },
                        { type: "95", available: true },
                        { type: "Diesel", available: true }
                    ],
                    waitTime: 5,
                    pumpsAvailable: 4,
                    pumpsTotal: 6
                }
            },
            { 
                title: "Eastern Riyadh Station", 
                lat: 24.6911, 
                lng: 46.7177, 
                type: "RFID", 
                address: "Eastern Ring Road, Riyadh", 
                services: ["Fuel", "Market"], 
                hours: "6am-12am",
                availability: { 
                    status: "medium", 
                    fuelTypes: [
                        { type: "91", available: true },
                        { type: "95", available: true },
                        { type: "Diesel", available: false }
                    ],
                    waitTime: 10,
                    pumpsAvailable: 2,
                    pumpsTotal: 4
                }
            },
            { 
                title: "Jeddah Main Station", 
                lat: 21.4858, 
                lng: 39.1925, 
                type: "RFID", 
                address: "Palestine Road, Jeddah", 
                services: ["Fuel", "Car Wash", "Maintenance"], 
                hours: "24/7",
                availability: { 
                    status: "low", 
                    fuelTypes: [
                        { type: "91", available: true },
                        { type: "95", available: false },
                        { type: "Diesel", available: true }
                    ],
                    waitTime: 20,
                    pumpsAvailable: 1,
                    pumpsTotal: 6
                }
            },
            { 
                title: "Dammam Service Point", 
                lat: 26.3126, 
                lng: 50.2234, 
                type: "Service", 
                address: "King Abdullah Road, Dammam", 
                services: ["Car Wash", "Maintenance"], 
                hours: "8am-10pm",
                availability: { 
                    status: "high", 
                    serviceTypes: [
                        { type: "Car Wash", available: true, waitTime: 15 },
                        { type: "Oil Change", available: true, waitTime: 30 },
                        { type: "Tire Service", available: true, waitTime: 20 }
                    ]
                }
            },
            { 
                title: "Medina Station", 
                lat: 24.4539, 
                lng: 39.6142, 
                type: "RFID", 
                address: "Airport Road, Medina", 
                services: ["Fuel", "Market", "Rest Area"], 
                hours: "24/7",
                availability: { 
                    status: "high", 
                    fuelTypes: [
                        { type: "91", available: true },
                        { type: "95", available: true },
                        { type: "Diesel", available: true }
                    ],
                    waitTime: 5,
                    pumpsAvailable: 5,
                    pumpsTotal: 6
                }
            }
        ];
    }
    
    // Load favorites from localStorage
    function loadFavorites() {
        const savedFavorites = localStorage.getItem('joilFavoriteLocations');
        if (savedFavorites) {
            userFavorites = JSON.parse(savedFavorites);
        }
    }
    
    // Save favorites to localStorage
    function saveFavorites() {
        localStorage.setItem('joilFavoriteLocations', JSON.stringify(userFavorites));
    }
    
    // Toggle favorite status of a location
    function toggleFavorite(locationTitle) {
        const isFavorite = userFavorites.includes(locationTitle);
        
        if (isFavorite) {
            // Remove from favorites
            userFavorites = userFavorites.filter(title => title !== locationTitle);
            document.getElementById('favoriteToastBody').textContent = `${locationTitle} removed from favorites`;
        } else {
            // Add to favorites
            userFavorites.push(locationTitle);
            document.getElementById('favoriteToastBody').textContent = `${locationTitle} saved to favorites`;
        }
        
        // Save updated favorites
        saveFavorites();
        
        // Show toast notification
        favoriteToast.show();
        
        // Update UI if needed
        const favoriteBtn = document.querySelector(`.favorite-btn[data-location="${locationTitle}"]`);
        if (favoriteBtn) {
            if (isFavorite) {
                favoriteBtn.innerHTML = '<i class="far fa-heart"></i>';
                favoriteBtn.classList.remove('active');
            } else {
                favoriteBtn.innerHTML = '<i class="fas fa-heart"></i>';
                favoriteBtn.classList.add('active');
            }
        }
        
        return !isFavorite; // Return new state
    }
    
    // Haversine formula to calculate distance between two points on Earth
    function getDistanceFromLatLonInKm(lat1, lon1, lat2, lon2) {
        const R = 6371; // Radius of the earth in km
        const dLat = deg2rad(lat2 - lat1);
        const dLon = deg2rad(lon2 - lon1);
        const a = 
            Math.sin(dLat/2) * Math.sin(dLat/2) +
            Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * 
            Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        const distance = R * c; // Distance in km
        return distance;
    }
    
    function deg2rad(deg) {
        return deg * (Math.PI/180);
    }
    
    // Estimate travel time based on distance
    function estimateTravelTime(distanceKm) {
        // Average speed in km/h
        const avgSpeed = 50;
        
        // Calculate travel time in minutes
        const travelTimeMinutes = Math.round((distanceKm / avgSpeed) * 60);
        
        // Format travel time
        if (travelTimeMinutes < 60) {
            return `${travelTimeMinutes} min`;
        } else {
            const hours = Math.floor(travelTimeMinutes / 60);
            const minutes = travelTimeMinutes % 60;
            return `${hours} h ${minutes} min`;
        }
    }
    
    // Get availability status color and label
    function getAvailabilityInfo(status) {
        switch(status) {
            case 'high':
                return { color: '#34A853', label: 'High Availability' };
            case 'medium':
                return { color: '#FBBC05', label: 'Moderate Availability' };
            case 'low':
                return { color: '#EA4335', label: 'Low Availability' };
            default:
                return { color: '#9AA0A6', label: 'Unknown Availability' };
        }
    }
    
    // Fetch real-time travel time from Google Maps API (simulated for development)
    function fetchRealTimeTravelTime(origin, destination) {
        // Simulate API call for development
        // In production, this would use the Distance Matrix API
        
        // Get straight-line distance
        const distance = getDistanceFromLatLonInKm(
            origin.lat,
            origin.lng,
            destination.lat,
            destination.lng
        );
        
        // Simulate travel time calculation
        // In real implementation, this would come from the API response
        const estimatedTime = estimateTravelTime(distance);
        
        // Return simulated response
        return {
            distance: distance,
            duration: estimatedTime,
            traffic_factor: Math.random() < 0.3 ? 'heavy' : (Math.random() < 0.6 ? 'moderate' : 'light')
        };
    }
    
    function findNearMe() {
        const statusDiv = document.getElementById('location-status');
        const nearestLocationCard = document.getElementById('nearest-location-card');
        const bookNearestBtn = document.getElementById('book-nearest');
        
        statusDiv.classList.remove('d-none', 'alert-danger', 'alert-success', 'alert-info', 'alert-warning');
        statusDiv.classList.add('alert-info');
        statusDiv.textContent = 'Getting your location...';
        
        nearestLocationCard.classList.add('d-none');
        bookNearestBtn.disabled = true;
        
        // Make sure we have locations data
        if (mapLocations.length === 0) {
            statusDiv.classList.remove('alert-info');
            statusDiv.classList.add('alert-warning');
            statusDiv.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>No map locations available. Please reload the page and try again.';
            return;
        }
        
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    userLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    
                    // Find nearest location from REAL map data
                    nearestLocation = findNearestLocation(userLocation);
                    
                    if (nearestLocation) {
                        // Get estimated travel time
                        estimatedTravelTime = fetchRealTimeTravelTime(userLocation, nearestLocation);
                        
                        // Update Google My Maps to center on the nearest location and zoom in
                        updateMapLocation(nearestLocation.lat, nearestLocation.lng, 16);
                        
                        // Show map popup
                        showMapPopup(nearestLocation);
                        
                        // Show nearest location toast notification
                        document.getElementById('nearest-toast-message').textContent = 
                            `Nearest location: ${nearestLocation.title} (${nearestLocation.distance.toFixed(1)} km)`;
                        nearestLocationToast.show();
                        
                        // Update status message
                        statusDiv.classList.remove('alert-info');
                        statusDiv.classList.add('alert-success');
                        statusDiv.innerHTML = `
                            <i class="fas fa-check-circle me-2"></i>Found nearest location: <strong>${nearestLocation.title}</strong> 
                            <div class="mt-1">
                                <span class="badge bg-secondary"><i class="fas fa-road me-1"></i>${nearestLocation.distance.toFixed(1)} km</span>
                                <span class="badge bg-secondary"><i class="fas fa-clock me-1"></i>${estimatedTravelTime.duration}</span>
                                ${estimatedTravelTime.traffic_factor === 'heavy' ? '<span class="badge bg-danger"><i class="fas fa-car-crash me-1"></i>Heavy Traffic</span>' : 
                                 (estimatedTravelTime.traffic_factor === 'moderate' ? '<span class="badge bg-warning text-dark"><i class="fas fa-traffic-light me-1"></i>Moderate Traffic</span>' : 
                                 '<span class="badge bg-success"><i class="fas fa-car me-1"></i>Light Traffic</span>')}
                            </div>
                        `;
                        
                        // Show nearest location card
                        displayNearestLocation(nearestLocation);
                        nearestLocationCard.classList.remove('d-none');
                        
                        // Enable the book button
                        bookNearestBtn.disabled = false;
                    } else {
                        statusDiv.classList.remove('alert-info');
                        statusDiv.classList.add('alert-warning');
                        statusDiv.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>No locations found nearby. Please try again later.';
                    }
                },
                (error) => {
                    let errorMessage = 'Unable to determine your location.';
                    
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage = 'Location access was denied. Please enable location services in your browser.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage = 'Location information is unavailable.';
                            break;
                        case error.TIMEOUT:
                            errorMessage = 'The request to get your location timed out.';
                            break;
                    }
                    
                    statusDiv.classList.remove('alert-info');
                    statusDiv.classList.add('alert-danger');
                    statusDiv.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i>${errorMessage}`;
                },
                { maximumAge: 60000, timeout: 10000, enableHighAccuracy: true }
            );
        } else {
            statusDiv.classList.remove('alert-info');
            statusDiv.classList.add('alert-danger');
            statusDiv.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Geolocation is not supported by this browser.';
        }
    }
    
    function findNearestLocation(position) {
        showLoading();
        
        const data = {
            lat: position.lat,
            lng: position.lng
        };
        
        axios.post('/nearest-station', data)
            .then(response => {
                if (response.data.success) {
                    const nearest = response.data.stations[0];
                    if (nearest) {
                        updateMapLocation(nearest.lat, nearest.lng);
                        showMapPopup(nearest);
                    } else {
                        showError('No stations found in your area.');
                    }
                } else {
                    showError(response.data.error || 'Failed to find nearest station.');
                }
            })
            .catch(error => {
                console.error('Error finding nearest station:', error);
                showError('Failed to find nearest station. Please try again later.');
            })
            .finally(() => {
                hideLoading();
            });
    }
    
    function updateMapLocation(lat, lng, zoom = 14) {
        // Update the iframe src to center on the new location with specified zoom level
        const iframe = document.getElementById('google-my-map');
        const currentSrc = iframe.src;
        
        // Extract the base URL and parameters
        const urlParts = currentSrc.split('&');
        const baseUrl = urlParts[0];
        
        // Create a new URL with updated center coordinates and zoom level
        const newParams = urlParts.filter(part => !part.startsWith('ll=') && !part.startsWith('z=')).join('&');
        const newSrc = `${baseUrl}&${newParams}&ll=${lat}%2C${lng}&z=${zoom}`;
        
        iframe.src = newSrc;
    }
    
    function showLoading(message = 'Finding nearest stations...') {
        const overlay = document.getElementById('map-loading-overlay');
        const loadingMessage = document.getElementById('loading-message');
        loadingMessage.textContent = message;
        overlay.classList.remove('d-none');
    }
    
    function hideLoading() {
        const overlay = document.getElementById('map-loading-overlay');
        overlay.classList.add('d-none');
    }
    
    function showError(message) {
        const toast = document.getElementById('errorToast');
        const toastMessage = document.getElementById('errorToastMessage');
        toastMessage.textContent = message;
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
    }
    
    function closeMapPopup() {
        const popup = document.getElementById('map-popup');
        popup.classList.add('d-none');
    }
    
    function showMapPopup(location) {
        const popup = document.getElementById('map-popup');
        
        // Set basic information
        document.getElementById('popup-title').textContent = location.title;
        document.getElementById('popup-details').textContent = location.address || location.description;
        document.getElementById('popup-distance').textContent = `${location.distance.toFixed(1)} km away`;
        
        // Set station type and status
        const stationType = document.getElementById('station-type');
        stationType.textContent = location.type || 'RFID Station';
        stationType.className = `badge ${location.type === 'Service Point' ? 'bg-warning' : 'bg-info'} me-1`;
        
        // Set station status
        const stationStatus = document.getElementById('station-status');
        const status = location.availability?.status || 'operational';
        stationStatus.textContent = status.charAt(0).toUpperCase() + status.slice(1);
        stationStatus.className = `badge bg-${status === 'high' ? 'success' : status === 'medium' ? 'warning' : 'danger'} me-1`;
        
        // Set hours
        document.getElementById('station-hours').textContent = location.hours || '24/7';
        
        // Set services
        const services = location.services || [];
        document.getElementById('station-services').textContent = services.join(', ') || 'Basic Services';
        
        // Set directions link
        document.getElementById('popup-directions').href = 
            `https://www.google.com/maps/dir/?api=1&destination=${location.lat},${location.lng}`;
        
        // Show popup
        popup.classList.remove('d-none');
    }
    
    function displayNearestLocation(location) {
        const detailsContainer = document.getElementById('nearest-location-details');
        const typeColor = location.type === 'RFID' ? '#4285F4' : '#EA4335';
        const availabilityInfo = getAvailabilityInfo(location.availability.status);
        const isFavorite = userFavorites.includes(location.title);
        const servicesList = location.services.map(service => 
            `<span class="badge bg-secondary me-1">${service}</span>`
        ).join('');
        
        let availabilityHTML = '';
        
        if (location.type === 'RFID') {
            const availableFuels = location.availability.fuelTypes
                .filter(fuel => fuel.available)
                .map(fuel => `<span class="badge bg-success me-1">${fuel.type}</span>`)
                .join('');
                
            const unavailableFuels = location.availability.fuelTypes
                .filter(fuel => !fuel.available)
                .map(fuel => `<span class="badge bg-secondary me-1 text-decoration-line-through">${fuel.type}</span>`)
                .join('');
                
            availabilityHTML = `
                <div class="mt-2 p-2 rounded" style="background-color: rgba(0,0,0,0.03);">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h6 class="mb-0 small">Station Status</h6>
                        <span class="badge" style="background-color: ${availabilityInfo.color}">${availabilityInfo.label}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center small mb-1">
                        <span>Wait Time:</span>
                        <span class="badge bg-${location.availability.waitTime > 15 ? 'danger' : (location.availability.waitTime > 5 ? 'warning text-dark' : 'success')}">${location.availability.waitTime} min</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center small mb-1">
                        <span>Pumps Available:</span>
                        <span>${location.availability.pumpsAvailable}/${location.availability.pumpsTotal}</span>
                    </div>
                    <div class="mt-1">
                        <small class="d-block mb-1">Available Fuel Types:</small>
                        <div>${availableFuels}${unavailableFuels}</div>
                    </div>
                </div>
            `;
        } else if (location.type === 'Service') {
            const serviceStatus = location.availability.serviceTypes.map(service => `
                <div class="d-flex justify-content-between align-items-center small mb-1">
                    <span>${service.type}:</span>
                    <div>
                        <span class="badge ${service.available ? 'bg-success' : 'bg-danger'}">${service.available ? 'Available' : 'Unavailable'}</span>
                        ${service.available ? `<span class="ms-1 text-muted">(${service.waitTime} min wait)</span>` : ''}
                    </div>
                </div>
            `).join('');
            
            availabilityHTML = `
                <div class="mt-2 p-2 rounded" style="background-color: rgba(0,0,0,0.03);">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h6 class="mb-0 small">Service Status</h6>
                        <span class="badge" style="background-color: ${availabilityInfo.color}">${availabilityInfo.label}</span>
                    </div>
                    ${serviceStatus}
                </div>
            `;
        }
        
        detailsContainer.innerHTML = `
            <div class="d-flex align-items-center mb-2">
                <div style="width: 15px; height: 15px; background-color: ${typeColor}; border-radius: 50%; margin-right: 8px;"></div>
                <h6 class="mb-0">${location.title}</h6>
                <button class="btn btn-sm btn-link ms-auto favorite-btn ${isFavorite ? 'active' : ''}" 
                        data-location="${location.title}" 
                        onclick="toggleFavorite('${location.title}')">
                    <i class="${isFavorite ? 'fas' : 'far'} fa-heart"></i>
                </button>
            </div>
            <p class="mb-1 small text-muted">${location.address}</p>
            <div class="d-flex align-items-center gap-2 mb-2">
                <small><i class="fas fa-clock me-1"></i>${location.hours}</small>
                <div class="ms-auto">
                    <span class="badge bg-secondary"><i class="fas fa-car me-1"></i>${estimatedTravelTime.duration}</span>
                </div>
            </div>
            <div class="mb-2">
                ${servicesList}
            </div>
            ${availabilityHTML}
            <div class="d-flex gap-2 mt-3">
                <button class="btn btn-sm btn-outline-primary view-details-btn" onclick="showLocationDetails('${location.title}')">
                    <i class="fas fa-info-circle me-1"></i>Details
                </button>
                <a href="https://www.google.com/maps/dir/?api=1&destination=${location.lat},${location.lng}" 
                   class="btn btn-sm btn-outline-success" 
                   target="_blank">
                    <i class="fas fa-directions me-1"></i>Directions
                </a>
            </div>
        `;
    }
    
    function showLocationDetails(locationTitle) {
        // Find the location by title
        const location = mapLocations.find(loc => loc.title === locationTitle);
        
        if (location) {
            const modal = document.getElementById('locationModal');
            const modalTitle = document.getElementById('locationModalLabel');
            const modalBody = document.getElementById('locationModalBody');
            const bookServiceBtn = document.getElementById('book-service-btn');
            const availabilityInfo = getAvailabilityInfo(location.availability.status);
            const isFavorite = userFavorites.includes(location.title);
            
            modalTitle.innerHTML = `
                ${location.title}
                <button class="btn btn-sm btn-link p-0 ms-2 favorite-btn ${isFavorite ? 'active' : ''}" 
                        data-location="${location.title}" 
                        onclick="toggleFavorite('${location.title}')">
                    <i class="${isFavorite ? 'fas' : 'far'} fa-heart"></i>
                </button>
            `;
            
            const typeColor = location.type === 'RFID' ? '#4285F4' : '#EA4335';
            const servicesList = location.services.map(service => 
                `<li>${service}</li>`
            ).join('');
            
            // Dynamic availability section
            let availabilitySection = '';
            
            if (location.type === 'RFID') {
                const fuelTypesList = location.availability.fuelTypes.map(fuel => 
                    `<li class="${!fuel.available ? 'text-decoration-line-through text-muted' : ''}">
                        ${fuel.type} ${fuel.available ? '(Available)' : '(Unavailable)'}
                    </li>`
                ).join('');
                
                availabilitySection = `
                    <div class="mb-3">
                        <h6>Current Availability:</h6>
                        <div class="card">
                            <div class="card-body p-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>Status:</span>
                                    <span class="badge" style="background-color: ${availabilityInfo.color}">
                                        ${availabilityInfo.label}
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>Wait Time:</span>
                                    <span class="badge bg-${location.availability.waitTime > 15 ? 'danger' : (location.availability.waitTime > 5 ? 'warning text-dark' : 'success')}">
                                        ${location.availability.waitTime} minutes
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>Pumps:</span>
                                    <span>${location.availability.pumpsAvailable} available of ${location.availability.pumpsTotal}</span>
                                </div>
                                <div>
                                    <span>Fuel Types:</span>
                                    <ul class="mb-0 mt-1">
                                        ${fuelTypesList}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            } else if (location.type === 'Service') {
                const serviceTypesList = location.availability.serviceTypes.map(service => 
                    `<li class="${!service.available ? 'text-decoration-line-through text-muted' : ''}">
                        ${service.type} ${service.available ? `(${service.waitTime} min wait)` : '(Unavailable)'}
                    </li>`
                ).join('');
                
                availabilitySection = `
                    <div class="mb-3">
                        <h6>Current Availability:</h6>
                        <div class="card">
                            <div class="card-body p-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>Status:</span>
                                    <span class="badge" style="background-color: ${availabilityInfo.color}">
                                        ${availabilityInfo.label}
                                    </span>
                                </div>
                                <div>
                                    <span>Services:</span>
                                    <ul class="mb-0 mt-1">
                                        ${serviceTypesList}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            // Display travel time if available
            let travelTimeSection = '';
            if (estimatedTravelTime) {
                travelTimeSection = `
                    <div class="mb-3">
                        <h6>Travel Information:</h6>
                        <div class="card">
                            <div class="card-body p-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>Distance:</span>
                                    <span>${location.distance ? location.distance.toFixed(1) : estimatedTravelTime.distance.toFixed(1)} km</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>Estimated Time:</span>
                                    <span>${estimatedTravelTime.duration}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Traffic Conditions:</span>
                                    <span class="badge bg-${estimatedTravelTime.traffic_factor === 'heavy' ? 'danger' : 
                                                        (estimatedTravelTime.traffic_factor === 'moderate' ? 'warning text-dark' : 'success')}">
                                        ${estimatedTravelTime.traffic_factor.charAt(0).toUpperCase() + estimatedTravelTime.traffic_factor.slice(1)}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            modalBody.innerHTML = `
                <div class="mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <div style="width: 15px; height: 15px; background-color: ${typeColor}; border-radius: 50%; margin-right: 8px;"></div>
                        <span class="fw-bold">${location.type} Location</span>
                    </div>
                    <p class="mb-1"><i class="fas fa-map-marker-alt me-2"></i>${location.address}</p>
                    <p class="mb-1"><i class="fas fa-clock me-2"></i>Operating Hours: ${location.hours}</p>
                </div>
                
                ${travelTimeSection}
                
                ${availabilitySection}
                
                <div class="mb-3">
                    <h6>Services Available:</h6>
                    <ul>
                        ${servicesList}
                    </ul>
                </div>
                
                <div class="mb-3">
                    <h6>Amenities:</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-light text-dark border"><i class="fas fa-wifi me-1"></i>Free Wi-Fi</span>
                        <span class="badge bg-light text-dark border"><i class="fas fa-coffee me-1"></i>Café</span>
                        <span class="badge bg-light text-dark border"><i class="fas fa-restroom me-1"></i>Restrooms</span>
                        <span class="badge bg-light text-dark border"><i class="fas fa-shopping-cart me-1"></i>Convenience Store</span>
                    </div>
                </div>
            `;
            
            // Update the book service button URL
            bookServiceBtn.onclick = function() {
                bookService(location);
            };
            
            // Show the modal
            locationModal.show();
        }
    }
    
    function bookService(location) {
        // Redirect to the booking form with the location pre-selected
        window.location.href = `{{ route('services.booking.order.form') }}?location=${encodeURIComponent(location.title)}`;
    }
</script>
@endpush

@push('styles')
<style>
    #nearest-location-card {
        border-left: 4px solid #0d6efd;
    }
    
    #map-container {
        position: relative;
        overflow: hidden;
        border-radius: 0.25rem;
    }
    
    #google-my-map {
        transition: all 0.3s ease;
    }
    
    /* Map popup styling */
    .map-popup {
        position: absolute;
        transform: translate(-50%, -50%);
        z-index: 10;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        padding: 15px;
        max-width: 250px;
        border-left: 4px solid #0d6efd;
        animation: fadeIn 0.3s ease-in-out;
    }
    
    .map-popup:after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        margin-left: -10px;
        width: 0;
        height: 0;
        border-left: 10px solid transparent;
        border-right: 10px solid transparent;
        border-top: 10px solid white;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translate(-50%, -60%); }
        to { opacity: 1; transform: translate(-50%, -43%); }
    }
    
    .btn-check:focus + .btn-primary, 
    .btn-primary:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .favorite-btn {
        color: #6c757d;
        padding: 0.25rem 0.5rem;
        transition: all 0.2s ease;
    }
    
    .favorite-btn:hover {
        color: #dc3545;
    }
    
    .favorite-btn.active {
        color: #dc3545;
    }
    
    /* Toast animation */
    .toast.show {
        animation: toastFadeIn 0.3s ease-in-out;
    }
    
    @keyframes toastFadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @media (max-width: 768px) {
        #google-my-map {
            height: 350px !important;
        }
        
        .row > .col-md-6 + .col-md-6 {
            margin-top: 10px;
        }
    }

    .map-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        z-index: 1000;
    }

    .station-services .badge {
        font-size: 0.8rem;
    }

    .station-info i {
        width: 20px;
        text-align: center;
    }
</style>
@endpush 