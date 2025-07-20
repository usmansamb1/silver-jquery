<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\MapLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Storage;
use SimpleXMLElement;
use App\Utils\ArabicTextUtils;

class MapMarksController extends Controller
{
    /**
     * Display the map marks page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Redirect to the map-view route for a better and more reliable map experience
        //return redirect()->route('map-view');
        
        // Google Maps API Key for development/testing
        $googleMapsApiKey = 'AIzaSyDev_0jHjLt31Z9dVUZPifVVNvKHhN5BvA';
        
        $googleMapsApiKey = config('services.google.maps_api_key');
        
        return view('customer.map-marks', [
            'googleMapsApiKey' => $googleMapsApiKey,
            'mapId' => '1_CRPlXxV43UXnOjAVk4OV-4KCbhgcSI'
        ]);
    }

    /**
     * Display the check map marks page (iframe-compatible version).
     *
     * @return \Illuminate\View\View
     */
    public function checkMapMarks()
    {
          

        //Google Maps API Key for development/testing
        $googleMapsApiKey = 'AIzaSyDev_0jHjLt31Z9dVUZPifVVNvKHhN5BvA';
        
        $googleMapsApiKey = config('services.google.maps_api_key');

        return view('customer.check-map-marks', [
            'googleMapsApiKey' => $googleMapsApiKey,
            'mapId' => '1_CRPlXxV43UXnOjAVk4OV-4KCbhgcSI'
        ]);
    }

    /**
     * Display the dedicated map view page for sidebar navigation.
     * 
     * @return \Illuminate\View\View
     */
    public function mapView()
    {

        //Google Maps API Key for development/testing
         $googleMapsApiKey = 'AIzaSyDev_0jHjLt31Z9dVUZPifVVNvKHhN5BvA';
        
         $googleMapsApiKey = config('services.google.maps_api_key');

        
        
        return view('customer.map-view', [
            'googleMapsApiKey' => $googleMapsApiKey,
            'mapId' => '1_CRPlXxV43UXnOjAVk4OV-4KCbhgcSI'
        ]);
    }

    /**
     * Get the nearest station to the user's location.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNearestStation(Request $request)
    {
        // Validate request
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180'
        ]);

        // Get user's location
        $userLat = $request->lat;
        $userLng = $request->lng;

        try {
            // Log the user's location for debugging
            Log::info('User location for nearest station search', [
                'lat' => $userLat,
                'lng' => $userLng
            ]);
            
            // Get all locations from database or KML data
            $locations = $this->getLocationsData();
            
            if (empty($locations)) {
                Log::error('No locations available for nearest station search');
                return response()->json([
                    'success' => false,
                    'error' => 'No stations are currently available. Please try again later.'
                ], 404);
            }
            
            // Log the number of locations retrieved for debugging
            Log::info('Retrieved locations for nearest station search', [
                'count' => count($locations)
            ]);
            
            // Calculate distances and find nearest stations
            $nearbyStations = [];
            foreach ($locations as $location) {
                try {
                    // Make sure lat/lng are valid
                    if (!isset($location['lat']) || !isset($location['lng']) ||
                        !is_numeric($location['lat']) || !is_numeric($location['lng'])) {
                        continue;
                    }
                    
                    $distance = $this->calculateDistance(
                        $userLat, 
                        $userLng, 
                        $location['lat'], 
                        $location['lng']
                    );
                    
                    // Skip extremely distant locations (> 500km) to avoid showing irrelevant results
                    if ($distance > 150) {
                        continue;
                    }
                    
                    $nearbyStations[] = array_merge($location, ['distance' => $distance]);
                } catch (\Exception $e) {
                    Log::warning('Error calculating distance for station: ' . ($location['title'] ?? 'Unknown'), [
                        'error' => $e->getMessage(),
                        'location' => $location
                    ]);
                    continue;
                }
            }
            
            if (empty($nearbyStations)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unable to find stations near your location. Please try again or try a different location.'
                ], 404);
            }
            
            // Sort by distance
            usort($nearbyStations, function($a, $b) {
                return $a['distance'] <=> $b['distance'];
            });
            
            // Log the nearest station found for debugging
            Log::info('Nearest station before weighting', [
                'station' => $nearbyStations[0]['title'],
                'distance' => $nearbyStations[0]['distance'],
                'coordinates' => [
                    'lat' => $nearbyStations[0]['lat'],
                    'lng' => $nearbyStations[0]['lng']
                ]
            ]);
            
            // Get the closest 5 stations (or fewer if not available)
            $nearbyStations = array_slice($nearbyStations, 0, 2); // change to 2
            
            // Apply weighting factors for stations
            foreach ($nearbyStations as &$station) {
                $station['weighted_distance'] = $this->calculateWeightedDistance($station);
            }
            
            // Re-sort by weighted distance
            usort($nearbyStations, function($a, $b) {
                return $a['weighted_distance'] <=> $b['weighted_distance'];
            });
            
            // Get the nearest station
            $nearestStation = $nearbyStations[0];
            
            // Log the search results
            Log::info('Nearest Station Search Results', [
                'user_location' => ['lat' => $userLat, 'lng' => $userLng],
                'nearest_station' => $nearestStation,
                'nearby_stations_count' => count($nearbyStations)
            ]);
            
            return response()->json([
                'success' => true,
                'station' => $nearestStation,
                'nearby_stations' => $nearbyStations[1]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error finding nearest station: ' . $e->getMessage(), [
                'user_location' => ['lat' => $userLat, 'lng' => $userLng],
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Error finding nearest station. Please try again later.'
            ], 500);
        }
    }

    /**
     * Calculate weighted distance for a station based on various factors
     *
     * @param array $station Station data including distance
     * @return float Weighted distance
     */
    private function calculateWeightedDistance($station)
    {
        $distance = $station['distance'];
        $weight = 1.0;

        // Distance is the most important factor - heavily favor closer stations
        // For stations under 5km, reduce the weight more dramatically
        if ($distance < 5) {
            $weight *= 0.7; // 30% reduction for very close stations
        } else if ($distance < 10) {
            $weight *= 0.8; // 20% reduction for stations within 10km
        } else if ($distance < 20) {
            $weight *= 0.9; // 10% reduction for stations within 20km
        }

        // Check station type and status from the description or explicit type
        $type = strtolower($station['type'] ?? '');
        $description = strtolower($station['description'] ?? '');
        
        if (empty($type)) {
            if (strpos($description, 'rfid station') !== false) {
                $type = 'rfid station';
            } elseif (strpos($description, 'main station') !== false) {
                $type = 'main station';
            } elseif (strpos($description, 'service point') !== false) {
                $type = 'service point';
            }
        }
        
        switch ($type) {
            case 'rfid station':
                $weight *= 0.9; // Prefer RFID stations
                break;
            case 'main station':
                $weight *= 0.85; // Strongly prefer main stations
                break;
            case 'service point':
                $weight *= 1.1; // Slightly deprioritize service-only points
                break;
        }

        // Check station status
        $status = strtolower($station['status'] ?? '');
        if (empty($status) && strpos($description, 'maintenance') !== false) {
            $status = 'maintenance';
        }
        
        switch ($status) {
            case 'maintenance':
            case 'under maintenance':
                $weight *= 1.2; // Deprioritize stations under maintenance
                break;
            case 'closed':
            case 'not operational':
                $weight *= 2.0; // Strongly deprioritize closed stations
                break;
            case 'operational':
                $weight *= 0.9; // Prefer operational stations
                break;
        }

        // Check for service availability
        $services = isset($station['services']) ? $station['services'] : [];
        $hasFullService = false;
        $serviceKeywords = ['fuel', 'rfid', 'maintenance', 'car wash'];
        
        // If services array is empty, try to extract from description
        if (empty($services) && !empty($description)) {
            foreach ($serviceKeywords as $service) {
                if (strpos($description, $service) !== false) {
                    $hasFullService = true;
                    $weight *= 0.97; // Reduce weight for each available service
                }
            }
        } else {
            foreach ($serviceKeywords as $service) {
                if (is_array($services) && in_array($service, array_map('strtolower', $services))) {
                    $hasFullService = true;
                    $weight *= 0.95; // Reduce weight for each available service
                }
            }
        }
        
        if ($hasFullService) {
            $weight *= 0.9; // Additional reduction for full-service stations
        }

        // Check for 24/7 operation
        if (strpos($description, '24/7') !== false || 
            strpos($description, '24 hours') !== false) {
            $weight *= 0.92; // Prefer 24/7 stations
        }

        // Apply city/region weighting if available
        if (isset($station['city']) || isset($station['region'])) {
            // Get cities from major list
            $majorCities = ['riyadh', 'jeddah', 'dammam', 'mecca', 'medina', 'khobar'];
            
            // Check if station is in a major city
            $stationCity = strtolower($station['city'] ?? '');
            $stationRegion = strtolower($station['region'] ?? '');
            
            if (in_array($stationCity, $majorCities) || 
                array_filter($majorCities, fn($city) => strpos($stationRegion, $city) !== false)) {
                $weight *= 0.95; // Slight preference for major city locations
            }
        }

        // Apply the weight to the distance
        return $distance * $weight;
    }

    /**
     * Get locations data from database, cache or fallback to sample data.
     * 
     * @return array
     */
    private function getLocationsData()
    {
        try {

            /*
               $operationalCount = $allDbLocations->filter(function ($location) {
            return $location->status === 'verified' || $location->status === 'verification required' || $location->status === 'under check' || $location->status === 'check location'; // Case-sensitive
        })->count();
        //dd($operationalCount);

        $maintenanceStatuses = ['suspended','suspended/ under construction', 'need to manage/ request sent','', 'need to manage/ request sent']; // Case-sensitive exact matches

            */
            // First try to get from database (preferred source)
            $dbLocations = MapLocation::where('status', 'verified')
                ->orWhere('status', 'verification required')->orWhere('status','=', '')
                ->get();
            if ($dbLocations->count() > 0) {
                $locations = [];
                foreach ($dbLocations as $location) {
                    $locations[] = [
                        'title' => $location->name ?? 'JOIL Station',
                        'description' => $location->description_raw ?? 'RFID Station',
                        'lat' => (float) $location->latitude,
                        'lng' => (float) $location->longitude,
                        'type' => $location->type ?? 'RFID Station',
                        'status' => $location->status ?? 'operational',
                        'city' => $location->city,
                        'region' => $location->region,
                        'kml_code' => $location->kml_code
                    ];
                }
                
                Log::info('Retrieved locations from database', ['count' => count($locations)]);
                return $locations;
            }

            // If not in DB, try to get from cache
            if (Cache::has('map_locations')) {
                $cachedLocations = Cache::get('map_locations');
                if (!empty($cachedLocations)) {
                    Log::info('Returning cached locations', ['count' => count($cachedLocations)]);
                    return $cachedLocations;
                }
                Log::info('Clearing empty cache');
                Cache::forget('map_locations');
            }

            // Fetch KML data from Google Maps
            $mapId = '1_CRPlXxV43UXnOjAVk4OV-4KCbhgcSI';
            $kmlUrl = "https://www.google.com/maps/d/u/0/kml?mid={$mapId}&forcekml=1";
            
            Log::info('Fetching KML data from URL', ['url' => $kmlUrl]);
            
            $client = new Client([
                'timeout' => 30,
                'verify' => false,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Accept' => 'text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,*/*;q=0.7',
                    'Accept-Language' => 'en-US,en;q=0.9',
                    'Cache-Control' => 'no-cache',
                    'Pragma' => 'no-cache'
                ]
            ]);

            try {
                $response = $client->get($kmlUrl);
                $kmlData = (string) $response->getBody();
                
                Log::info('KML data fetched', [
                    'status_code' => $response->getStatusCode(),
                    'content_type' => $response->getHeaderLine('Content-Type'),
                    'data_length' => strlen($kmlData)
                ]);
                
                if (empty($kmlData)) {
                    Log::error('Empty KML data received');
                    throw new \Exception('Empty KML data received from Google Maps');
                }
            } catch (GuzzleException $e) {
                Log::error('Failed to fetch KML data', [
                    'error' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'url' => $kmlUrl
                ]);
                return $this->getSampleLocations();
            }

            // Enable internal errors for better error handling
            libxml_use_internal_errors(true);
            
            // Parse the KML data
            $kml = simplexml_load_string($kmlData);
            
            if (!$kml) {
                $errors = libxml_get_errors();
                libxml_clear_errors();
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = "Line {$error->line}: {$error->message}";
                }
                Log::error('XML Parse Errors', ['errors' => $errorMessages]);
                return $this->getSampleLocations();
            }

            $locations = [];
            
            // Process Folders first
            if ($kml->Document->Folder) {
                Log::info('Processing KML Folders', ['folder_count' => count($kml->Document->Folder)]);
                foreach ($kml->Document->Folder as $folder) {
                    if ($folder->Placemark) {
                        Log::info('Processing Folder Placemarks', [
                            'folder_name' => (string)$folder->name,
                            'placemark_count' => count($folder->Placemark)
                        ]);
                        foreach ($folder->Placemark as $placemark) {
                            $location = $this->parsePlacemark($placemark);
                            if ($location) {
                                $locations[] = $location;
                            }
                        }
                    }
                }
            }
            
            // Then process direct Placemarks
            if ($kml->Document->Placemark) {
                Log::info('Processing direct Placemarks', ['count' => count($kml->Document->Placemark)]);
                foreach ($kml->Document->Placemark as $placemark) {
                    $location = $this->parsePlacemark($placemark);
                    if ($location) {
                        $locations[] = $location;
                    }
                }
            }

            if (empty($locations)) {
                Log::warning('No valid locations found in KML data, using sample data');
                return $this->getSampleLocations();
            }

            Log::info('Successfully parsed KML data', [
                'total_locations' => count($locations),
                'first_location' => $locations[0] ?? null,
                'location_types' => array_count_values(array_map(function($loc) {
                    return $this->determineLocationType($loc);
                }, $locations))
            ]);

            // Cache the locations for 1 hour
            Cache::put('map_locations', $locations, now()->addHour());
            
            return $locations;

        } catch (\Exception $e) {
            Log::error('Error in getLocationsData', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->getSampleLocations();
        }
    }

    /**
     * Parse a KML Placemark into a location array
     *
     * @param \SimpleXMLElement $placemark
     * @return array|null
     */
    private function parsePlacemark($placemark)
    {
        try {
            // Get coordinates
            $coordinates = null;
            
            // Check for Point coordinates
            if (isset($placemark->Point->coordinates)) {
                $coordinates = (string)$placemark->Point->coordinates;
            }
            // Check for LineString coordinates (first point)
            else if (isset($placemark->LineString->coordinates)) {
                $coordinates = explode(' ', (string)$placemark->LineString->coordinates)[0];
            }
            
            if (!$coordinates) {
                return null;
            }

            // Parse coordinates
            $coords = explode(',', $coordinates);
            if (count($coords) < 2) {
                return null;
            }

            // Get name and description
            $name = (string)$placemark->name;
            $description = (string)$placemark->description;
            
            // If no name, try to get it from description
            if (empty($name) && !empty($description)) {
                // Try to extract name from description HTML
                if (preg_match('/<h3[^>]*>(.*?)<\/h3>/i', $description, $matches)) {
                    $name = strip_tags($matches[1]);
                }
            }

            // Clean up description
            $description = strip_tags($description);
            if (empty($description)) {
                $description = 'RFID Station';
            }

            return [
                'title' => $name ?: 'JOIL Station',
                'description' => $description,
                'lat' => (float)$coords[1],
                'lng' => (float)$coords[0]
            ];
        } catch (\Exception $e) {
            Log::warning('Failed to parse placemark: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get sample locations data as fallback.
     * 
     * @return array
     */
    private function getSampleLocations()
    {
        return [
            [
                'title' => "Riyadh JOIL Station", 
                'lat' => 24.7136, 
                'lng' => 46.6753, 
                'description' => "RFID Station - King Fahd Road, Riyadh",
            ],
            [
                'title' => "Dammam JOIL Station", 
                'lat' => 26.4344, 
                'lng' => 50.1033, 
                'description' => "RFID Station - Dammam-Khobar Highway",
            ],
            [
                'title' => "Jeddah Main Station", 
                'lat' => 21.5433, 
                'lng' => 39.1728, 
                'description' => "RFID Station - Palestine Road, Jeddah",
            ],
            [
                'title' => "Mecca JOIL Point", 
                'lat' => 21.3891, 
                'lng' => 39.8379, 
                'description' => "Service Point - Mecca",
            ],
            [
                'title' => "Medina Station", 
                'lat' => 24.5247, 
                'lng' => 39.5692, 
                'description' => "RFID Station - Airport Road, Medina",
            ],
            [
                'title' => "Al Khobar JOIL", 
                'lat' => 26.2172, 
                'lng' => 50.1971, 
                'description' => "RFID Station - King Fahd Road, Al Khobar",
            ],
            [
                'title' => "Abha Service Station", 
                'lat' => 18.2464, 
                'lng' => 42.5117, 
                'description' => "RFID Station - Abha Main Road",
            ],
            [
                'title' => "Tabuk JOIL", 
                'lat' => 28.3998, 
                'lng' => 36.5715, 
                'description' => "RFID Station - Tabuk City Center",
            ],
            [
                'title' => "Hail Station", 
                'lat' => 27.5114, 
                'lng' => 41.7208, 
                'description' => "Service Station - Hail",
            ]
        ];
    }

    /**
     * Calculate distance between two points using Haversine formula.
     *
     * @param float $lat1 First point latitude
     * @param float $lon1 First point longitude
     * @param float $lat2 Second point latitude
     * @param float $lon2 Second point longitude
     * @return float Distance in kilometers
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371; // Earth's radius in kilometers

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);
            
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }

    /**
     * Display a listing of all locations.
     *
     * @return \Illuminate\View\View
     */
    public function mapsList()
    {
        try {
            // Clear existing cache
            Cache::forget('map_locations');
            
            // Get locations data
            $allLocations = $this->getLocationsData();
            
            Log::info('MapsList - Raw locations data', [
                'count' => count($allLocations),
                'first_few' => array_slice($allLocations, 0, 3)
            ]);
            
            if (empty($allLocations)) {
                return view('customer.maps-list')->with('error', 'No locations data available');
            }

            // Group locations by type
            $locations = [
                'main_stations' => [],
                'rfid_stations' => [],
                'service_points' => []
            ];

            foreach ($allLocations as $location) {
                $type = $this->determineLocationType($location);
                $processedLocation = $this->processLocation($location);
                
                Log::info('Processing location', [
                    'title' => $location['title'] ?? 'Unknown',
                    'type' => $type,
                    'processed' => $processedLocation
                ]);
                
                switch ($type) {
                    case 'Main Station':
                        $locations['main_stations'][] = $processedLocation;
                        break;
                    case 'RFID Station':
                        $locations['rfid_stations'][] = $processedLocation;
                        break;
                    case 'Service Point':
                        $locations['service_points'][] = $processedLocation;
                        break;
                }
            }

            // Calculate statistics
            $total_count = count($allLocations);
            $operational_count = count(array_filter($allLocations, fn($loc) => $this->determineLocationStatus($loc) === 'operational'));
            $maintenance_count = count(array_filter($allLocations, fn($loc) => $this->determineLocationStatus($loc) === 'maintenance'));

            Log::info('MapsList - Final processed data', [
                'total_count' => $total_count,
                'operational_count' => $operational_count,
                'maintenance_count' => $maintenance_count,
                'main_stations_count' => count($locations['main_stations']),
                'rfid_stations_count' => count($locations['rfid_stations']),
                'service_points_count' => count($locations['service_points'])
            ]);

            return view('customer.maps-list', compact('locations', 'total_count', 'operational_count', 'maintenance_count'));

        } catch (\Exception $e) {
            Log::error('Error in mapsList: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return view('customer.maps-list')->with('error', 'Failed to load locations. Please try again later.');
        }
    }

    /**
     * Process a single location data.
     *
     * @param array $location
     * @return array
     */
    private function processLocation($location)
    {
        return [
            'title' => $location['title'] ?? 'Unnamed Location',
            'description' => $location['description'] ?? '',
            'type' => $this->determineLocationType($location),
            'status' => $this->determineLocationStatus($location),
            'lat' => $location['lat'] ?? $location['coordinates']['lat'] ?? 0,
            'lng' => $location['lng'] ?? $location['coordinates']['lng'] ?? 0,
            'services' => $this->determineLocationServices($location),
            'hours' => $this->determineLocationHours($location),
        ];
    }

    /**
     * Determine the type of location based on description and title
     */
    private function determineLocationType($location)
    {
        $description = strtolower($location['description'] ?? '');
        $title = strtolower($location['title'] ?? '');
        
        if (str_contains($description, 'main station') || str_contains($title, 'main station')) {
            return 'Main Station';
        }
        
        if (str_contains($description, 'service point') || str_contains($title, 'service point')) {
            return 'Service Point';
        }
        
        return 'RFID Station';
    }

    /**
     * Determine the current status of the location
     */
    private function determineLocationStatus($location)
    {
        $description = strtolower($location['description'] ?? '');
        
        if (str_contains($description, 'maintenance') || str_contains($description, 'under repair')) {
            return 'maintenance';
        }
        
        if (str_contains($description, 'closed') || str_contains($description, 'not operational')) {
            return 'closed';
        }
        
        return 'operational';
    }

    /**
     * Determine available services at the location
     */
    private function determineLocationServices($location)
    {
        $description = strtolower($location['description'] ?? '');
        $services = [];
        
        // Check for common services
        if (str_contains($description, 'fuel') || str_contains($description, 'petrol') || str_contains($description, 'diesel')) {
            $services[] = 'Fuel';
        }
        
        if (str_contains($description, 'rfid') || str_contains($description, 'payment')) {
            $services[] = 'RFID Payment';
        }
        
        if (str_contains($description, 'maintenance') || str_contains($description, 'repair')) {
            $services[] = 'Maintenance';
        }
        
        if (str_contains($description, 'car wash') || str_contains($description, 'washing')) {
            $services[] = 'Car Wash';
        }
        
        if (str_contains($description, 'oil change') || str_contains($description, 'oil service')) {
            $services[] = 'Oil Change';
        }
        
        if (str_contains($description, '24/7') || str_contains($description, '24 hours')) {
            $services[] = '24/7 Service';
        }
        
        return $services;
    }

    /**
     * Determine operating hours from location description
     */
    private function determineLocationHours($location)
    {
        $description = strtolower($location['description'] ?? '');
        
        if (str_contains($description, '24/7') || str_contains($description, '24 hours')) {
            return '24/7';
        }
        
        // Try to find time patterns like "8:00 AM - 10:00 PM" or "8am-10pm"
        if (preg_match('/(\d{1,2}(?::\d{2})?\s*(?:am|pm))\s*-\s*(\d{1,2}(?::\d{2})?\s*(?:am|pm))/i', $description, $matches)) {
            return $matches[1] . ' - ' . $matches[2];
        }
        
        return 'Standard Hours';
    }

    /**
     * Test KML data fetching and parsing
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function testKmlFetch()
    {
        try {
            // Clear existing cache
            Cache::forget('map_locations');
            
            // Attempt to fetch and parse KML data
            $locations = $this->getLocationsData();
            
            // Log success and data
            Log::info('KML Test Fetch Success', [
                'total_locations' => count($locations),
                'sample_location' => !empty($locations) ? $locations[0] : null
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'KML data fetched successfully',
                'total_locations' => count($locations),
                'locations' => $locations
            ]);
        } catch (\Exception $e) {
            Log::error('KML Test Fetch Failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enhanced KML data extraction to fetch all locations from the embedded map
     * This is a new function that doesn't replace existing functionality
     * 
     * @return array
     */
    private function extractAllLocationsFromKML()
    {
        try {
            // Log the start of extraction
            Log::info('Starting extraction of all locations from KML');
            
            // Use the correct Map ID for JOIL YASEEIR
            $mapId = '1_CRPlXxV43UXnOjAVk4OV-4KCbhgcSI'; // Main map ID
            $alternateMapId = '1jd-3oRTv5ySoNWJ8J6dknqfj0bA5J61h'; // Alternate map ID if needed
            
            // Try main map ID first
            $kmlUrl = "https://www.google.com/maps/d/u/0/kml?mid={$mapId}&forcekml=1";
            $locations = $this->fetchAndParseKMLFromURL($kmlUrl);
            
            // If no locations found, try alternate map ID
            if (empty($locations)) {
                $kmlUrl = "https://www.google.com/maps/d/u/0/kml?mid={$alternateMapId}&forcekml=1";
                $locations = $this->fetchAndParseKMLFromURL($kmlUrl);
            }
            
            // If still no locations, try without forcekml parameter
            if (empty($locations)) {
                $kmlUrl = "https://www.google.com/maps/d/kml?mid={$mapId}";
                $locations = $this->fetchAndParseKMLFromURL($kmlUrl);
            }
            
            // Log the result
            Log::info('KML extraction complete', [
                'total_locations' => count($locations),
                'first_location' => $locations[0] ?? null,
                'last_location' => count($locations) > 0 ? $locations[count($locations) - 1] : null
            ]);
            
            return $locations;
        } catch (\Exception $e) {
            Log::error('Error in extractAllLocationsFromKML: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Fetch and parse KML data from a URL
     * Helper function for extractAllLocationsFromKML
     * 
     * @param string $kmlUrl
     * @return array
     */
    private function fetchAndParseKMLFromURL($kmlUrl)
    {
        try {
            Log::info('Fetching KML from URL', ['url' => $kmlUrl]);
            
            $client = new Client([
                'timeout' => 30,
                'verify' => false,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Accept' => 'text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,*/*;q=0.7',
                    'Accept-Language' => 'en-US,en;q=0.9',
                    'Cache-Control' => 'no-cache',
                    'Pragma' => 'no-cache'
                ]
            ]);

            $response = $client->get($kmlUrl);
            $kmlData = (string) $response->getBody();
            
            Log::info('KML data fetched', [
                'status_code' => $response->getStatusCode(),
                'data_length' => strlen($kmlData)
            ]);
            
            if (empty($kmlData)) {
                Log::error('Empty KML data received');
                return [];
            }

            // Parse the KML data
            libxml_use_internal_errors(true);
            $kml = simplexml_load_string($kmlData);
            
            if (!$kml) {
                $errors = libxml_get_errors();
                libxml_clear_errors();
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = "Line {$error->line}: {$error->message}";
                }
                Log::error('XML Parse Errors', ['errors' => $errorMessages]);
                return [];
            }

            $locations = [];
            
            // Register all XML namespaces
            $namespaces = $kml->getNamespaces(true);
            
            // Process Document placemarks directly
            if (isset($kml->Document->Placemark)) {
                $this->processPlacemarks($kml->Document->Placemark, $locations);
            }
            
            // Process all folders and subfolders
            if (isset($kml->Document->Folder)) {
                $this->processFolders($kml->Document->Folder, $locations);
            }
            
            // Debug log the locations found
            Log::info('Locations found in KML', [
                'count' => count($locations),
                'folders_processed' => isset($kml->Document->Folder) ? count($kml->Document->Folder) : 0,
                'direct_placemarks' => isset($kml->Document->Placemark) ? count($kml->Document->Placemark) : 0
            ]);
            
            return $locations;
            
        } catch (GuzzleException $e) { // Specific exception for Guzzle
            $logContext = [
                'url' => $kmlUrl,
                'error_message' => $e->getMessage(),
                'trace_snippet' => implode("\n", array_slice(explode("\n", $e->getTraceAsString()), 0, 5)) // Log first 5 lines of trace
            ];

            if ($e instanceof \GuzzleHttp\Exception\RequestException) {
                $logContext['response_code'] = $e->hasResponse() ? $e->getResponse()->getStatusCode() : null;
                $logContext['response_body_snippet'] = $e->hasResponse() ? substr((string) $e->getResponse()->getBody(), 0, 500) : 'No response body available';
            }

             Log::error('Guzzle Error fetching KML data: ' . $e->getMessage(), $logContext);
            return [];
        } catch (\Exception $e) { // Catch other general exceptions
            Log::error('General Error extracting enhanced KML data: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Process all folders and their placemarks recursively
     * Helper function for fetchAndParseKMLFromURL
     * 
     * @param \SimpleXMLElement $folders
     * @param array &$locations
     * @return void
     */
    private function processFolders($folders, &$locations)
    {
        foreach ($folders as $folder) {
            // Process placemarks in this folder
            if (isset($folder->Placemark)) {
                $this->processPlacemarks($folder->Placemark, $locations);
            }
            
            // Process sub-folders recursively
            if (isset($folder->Folder)) {
                $this->processFolders($folder->Folder, $locations);
            }
        }
    }

    /**
     * Process all placemarks and extract location data
     * Helper function for fetchAndParseKMLFromURL
     * 
     * @param \SimpleXMLElement $placemarks
     * @param array &$locations
     * @return void
     */
    private function processPlacemarks($placemarks, &$locations)
    {
        foreach ($placemarks as $placemark) {
            $location = $this->extractLocationFromPlacemark($placemark);
            if ($location) {
                $locations[] = $location;
            }
        }
    }

    /**
     * Extract location data from a single placemark
     * Enhanced version that handles more placemark types
     * 
     * @param \SimpleXMLElement $placemark
     * @return array|null
     */
    private function extractLocationFromPlacemark($placemark)
    {
        try {
            $title = (string)$placemark->name;
            $description = (string)$placemark->description;
            
            // Strip HTML tags from description but preserve line breaks
            $description = preg_replace('/<br\s*\/?>/i', "\n", $description);
            $description = strip_tags($description);
            $description = trim($description);
            
            // If title is empty, try to extract from description
            if (empty($title) && !empty($description)) {
                // Look for the first line as title
                $lines = explode("\n", $description);
                $title = trim($lines[0]);
                
                // If first line is not empty, use it as title
                if (!empty($title)) {
                    // Remove the first line from description
                    array_shift($lines);
                    $description = implode("\n", $lines);
                }
            }
            
            // Default title if still empty
            $title = !empty($title) ? $title : 'JOIL Location';
            
            // Extract coordinates based on geometry type
            $coords = null;
            
            // Check for Point coordinates
            if (isset($placemark->Point->coordinates)) {
                $coords = $this->parsePointCoordinates((string)$placemark->Point->coordinates);
            }
            // Check for LineString (get the first point)
            elseif (isset($placemark->LineString->coordinates)) {
                $coords = $this->parseLineStringCoordinates((string)$placemark->LineString->coordinates);
            }
            // Check for Polygon (get the first point of the outer boundary)
            elseif (isset($placemark->Polygon->outerBoundaryIs->LinearRing->coordinates)) {
                $coords = $this->parseLineStringCoordinates((string)$placemark->Polygon->outerBoundaryIs->LinearRing->coordinates);
            }
            // Check for MultiGeometry (get the first geometry's first point)
            elseif (isset($placemark->MultiGeometry)) {
                if (isset($placemark->MultiGeometry->Point->coordinates)) {
                    $coords = $this->parsePointCoordinates((string)$placemark->MultiGeometry->Point->coordinates);
                }
                elseif (isset($placemark->MultiGeometry->LineString->coordinates)) {
                    $coords = $this->parseLineStringCoordinates((string)$placemark->MultiGeometry->LineString->coordinates);
                }
                elseif (isset($placemark->MultiGeometry->Polygon->outerBoundaryIs->LinearRing->coordinates)) {
                    $coords = $this->parseLineStringCoordinates((string)$placemark->MultiGeometry->Polygon->outerBoundaryIs->LinearRing->coordinates);
                }
            }
            
            // If no coordinates found, return null
            if (!$coords) {
                return null;
            }
            
            // Create location data
            return [
                'title' => $title,
                'description' => $description,
                'lat' => $coords['lat'],
                'lng' => $coords['lng']
            ];
            
        } catch (\Exception $e) {
            Log::warning('Failed to extract location from placemark: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Parse point coordinates from KML
     * 
     * @param string $coordinatesString
     * @return array|null
     */
    private function parsePointCoordinates($coordinatesString)
    {
        $coordinatesString = trim($coordinatesString);
        $parts = explode(',', $coordinatesString);
        
        if (count($parts) >= 2) {
            return [
                'lng' => (float)$parts[0],
                'lat' => (float)$parts[1]
            ];
        }
        
        return null;
    }

    /**
     * Parse LineString or LinearRing coordinates from KML
     * Returns the first point
     * 
     * @param string $coordinatesString
     * @return array|null
     */
    private function parseLineStringCoordinates($coordinatesString)
    {
        $coordinatesString = trim($coordinatesString);
        $points = preg_split('/\s+/', $coordinatesString);
        
        if (!empty($points)) {
            $firstPoint = $points[0];
            return $this->parsePointCoordinates($firstPoint);
        }
        
        return null;
    }

    /**
     * Display a listing of all locations using the enhanced KML extraction
     * This is a new method that doesn't replace the existing mapsList method
     *
     * @return \Illuminate\View\View
     */
    public function enhancedMapsList()
    {
        try {
            // Clear location cache
            Cache::forget('map_locations_enhanced');
            
            // Get locations from cache or fetch them
            $allLocations = Cache::remember('map_locations_enhanced', now()->addHour(), function () {
                return $this->extractAllLocationsFromKML();
            });
            
            Log::info('Enhanced MapsList - Raw locations data', [
                'count' => count($allLocations),
                'first_few' => array_slice($allLocations, 0, 3)
            ]);
            
            if (empty($allLocations)) {
                return view('customer.maps-list')->with('error', 'No locations data available from the embedded map');
            }

            // Group locations by type
            $locations = [
                'main_stations' => [],
                'rfid_stations' => [],
                'service_points' => []
            ];

            foreach ($allLocations as $location) {
                $type = $this->determineLocationType($location);
                $processedLocation = $this->processLocation($location);
                
                switch ($type) {
                    case 'Main Station':
                        $locations['main_stations'][] = $processedLocation;
                        break;
                    case 'RFID Station':
                        $locations['rfid_stations'][] = $processedLocation;
                        break;
                    case 'Service Point':
                        $locations['service_points'][] = $processedLocation;
                        break;
                }
            }

            // Calculate statistics
            $total_count = count($allLocations);
            $operational_count = count(array_filter($allLocations, fn($loc) => $this->determineLocationStatus($loc) === 'operational'));
            $maintenance_count = count(array_filter($allLocations, fn($loc) => $this->determineLocationStatus($loc) === 'maintenance'));

            Log::info('Enhanced MapsList - Final processed data', [
                'total_count' => $total_count,
                'main_stations_count' => count($locations['main_stations']),
                'rfid_stations_count' => count($locations['rfid_stations']),
                'service_points_count' => count($locations['service_points'])
            ]);

            return view('customer.maps-list', compact('locations', 'total_count', 'operational_count', 'maintenance_count'));

        } catch (\Exception $e) {
            Log::error('Error in enhancedMapsList: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return view('customer.maps-list')->with('error', 'Failed to load locations. Please try again later.');
        }
    }

    public function allMapLocationsList()
    {
        $kmlUrl = 'https://www.google.com/maps/d/u/0/kml?mid=1_CRPlXxV43UXnOjAVk4OV-4KCbhgcSI&forcekml=1';
        $locations = $this->fetchAndParseKMLFromURL($kmlUrl); // Use robust new function
        return view('customer.all-map-locations', compact('locations'));
    }

    /**
     * Synchronize all map locations from KML to database
     * This method fetches all locations from the KML feed and stores them in the database
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncLocationsToDatabase()
    {
        try {
            $mapLocationModel = app(\App\Models\MapLocation::class);
            
            Cache::forget('map_locations_enhanced'); // Clear specific cache used by enhanced extraction
            Cache::forget('map_locations'); // Clear original cache too
            
            Log::info('Starting KML data extraction for database sync...');
            $locations = $this->extractEnhancedKMLData();
            Log::info('Finished KML data extraction.');
            
            if (empty($locations)) {
                 Log::warning('No locations extracted from KML data. Database sync aborted.');
                return response()->json([
                    'success' => false,
                    'message' => 'No locations found in KML data to sync.'
                ], 404);
            }
            
            Log::info('Starting database transaction for location sync. Extracted ' . count($locations) . ' locations.');
            DB::beginTransaction();
            
            $mapId = '1_CRPlXxV43UXnOjAVk4OV-4KCbhgcSI';
            Log::info("Deleting existing locations for source_map_id: {$mapId}");
            $deletedCount = $mapLocationModel::where('source_map_id', $mapId)->delete();
            Log::info("Deleted {$deletedCount} existing locations.");
            
            $insertedCount = 0;
            $skippedCount = 0;
            foreach ($locations as $index => $location) {
                // Basic validation: Ensure essential fields exist and are somewhat valid
                if (empty($location['title']) || !isset($location['lat']) || !isset($location['lng']) || !is_numeric($location['lat']) || !is_numeric($location['lng'])) {
                    Log::warning("Skipping location at index {$index} due to missing/invalid core data (title, lat, lng).", ['location_data' => $location]);
                    $skippedCount++;
                    continue;
                }

                // Process derived fields (type, status, etc.) using existing helper methods
                $type = $this->determineLocationType($location);
                $status = $this->determineLocationStatus($location);
                $services = $this->determineLocationServices($location);
                $hours = $this->determineLocationHours($location);
                
                // Create a safe, unique code for this location
                $safeCode = mb_substr(preg_replace('/[^a-zA-Z0-9_]/', '_', $location['title']), 0, 30) . '_' . 
                           $location['lat'] . '_' . $location['lng'];
                
                try {
                    $truncatedDescription = mb_substr($location['description'] ?? '', 0, 200, 'UTF-8');
                    
                    // Sanitize description and remove any problematic characters
                    $cleanDescription = preg_replace('/[^\p{L}\p{N}\s\.,;:!?\-_\(\)]/u', '', $truncatedDescription);
                    
                    $mapLocationModel::create([
                        'title' => $this->ensureProperArabicEncoding($location['title']),
                        'name' => $this->ensureProperArabicEncoding($location['title']),
                        'description_raw' => $this->ensureProperArabicEncoding($location['description'] ?? ''),
                        'latitude' => (float)$location['lat'],
                        'longitude' => (float)$location['lng'],
                        'type' => $type,
                        'status' => $status,
                        'services' => is_array($services) ? json_encode($services) : null,
                        'hours' => $hours,
                        'source_map_id' => $mapId,
                        'kml_code' => $safeCode,
                        'city' => $this->ensureProperArabicEncoding($location['city'] ?? ''),
                        'region' => $this->ensureProperArabicEncoding($location['region'] ?? ''),
                        'address' => $this->ensureProperArabicEncoding($location['address'] ?? '')
                    ]);
                    $insertedCount++;
                } catch (\Illuminate\Database\QueryException $qe) {
                    Log::error("Database Query Error inserting location: '{$location['title']}'", [
                        'error' => $qe->getMessage(),
                        'sql' => $qe->getSql() ?? 'SQL not available',
                        'bindings' => $qe->getBindings() ?? [],
                        'location_data' => $location
                    ]);
                    $skippedCount++;
                    // Continue with the next location
                } catch (\Exception $e) { // Catch other potential errors during create
                     Log::error("Error creating MapLocation record for: '{$location['title']}'", [
                        'error' => $e->getMessage(),
                        'location_data' => $location,
                        'trace' => $e->getTraceAsString()
                    ]);
                    $skippedCount++;
                }
            }
            
            DB::commit();
            Log::info("Database transaction committed. Sync complete.", [
                'inserted' => $insertedCount,
                 'skipped' => $skippedCount,
                 'total_extracted' => count($locations)
            ]);
            
            // Clear cache again after successful sync
            Cache::forget('map_locations_enhanced');
            Cache::forget('map_locations');

            return response()->json([
                'success' => true,
                'message' => "Successfully synced {$insertedCount} locations to database." . ($skippedCount > 0 ? " Skipped {$skippedCount} locations." : ""),
                'inserted_count' => $insertedCount,
                'skipped_count' => $skippedCount
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack(); // Ensure rollback on any exception during the process
            Log::error('Fatal Error during database sync process: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error syncing locations: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Extract enhanced KML data with improved parsing for all geometry types
     * 
     * @return array
     */
    private function extractEnhancedKMLData()
    {
        try {
            $mapId = '1_CRPlXxV43UXnOjAVk4OV-4KCbhgcSI';
            $kmlUrl = "https://www.google.com/maps/d/u/0/kml?mid={$mapId}&forcekml=1";
            
            Log::info('Fetching enhanced KML data', ['url' => $kmlUrl]);
            
            $client = new Client([
                'timeout' => 30,
                'verify' => false, // Consider setting to true in production with proper CA certs
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36', // More common user agent
                    'Accept' => 'application/vnd.google-earth.kml+xml, application/xml, text/xml;q=0.9, */*;q=0.8', // KML specific accept header
                    'Accept-Language' => 'en-US,en;q=0.9',
                    'Cache-Control' => 'no-cache',
                    'Pragma' => 'no-cache',
                ]
            ]);
            
            $response = $client->get($kmlUrl);
            $kmlData = (string) $response->getBody();
             
            if (empty($kmlData)) {
                Log::error('Empty KML data received');
                return [];
            }
            
            // Parse KML
            libxml_use_internal_errors(true);
            $kml = simplexml_load_string($kmlData);
         
            if (!$kml || !isset($kml->Document)) { // Ensure Document exists
                $errors = libxml_get_errors();
                libxml_clear_errors();
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = "Line {$error->line}: {$error->message}";
                }
                Log::error('KML Parse Errors or missing Document tag', ['errors' => $errorMessages, 'data_start' => substr($kmlData, 0, 500)]);
                return [];
            }
         
            $locations = [];
           
            // Process all folders recursively - this should handle all placemarks within folders
            if (isset($kml->Document->Folder)) {
                Log::info('Starting KML folder processing.');
                $this->processAllFolders($kml->Document->Folder, $locations);
                Log::info('Finished KML folder processing.');
            }
 
            // Process direct placemarks under the Document (if any)
            if (isset($kml->Document->Placemark)) {
                 Log::info('Processing direct KML placemarks under Document.');
                $this->processAllPlacemarks($kml->Document->Placemark, $locations, 'Document Root'); // Provide context
                 Log::info('Finished processing direct KML placemarks.');
            }
           
            Log::info('Enhanced KML extraction complete', [
                'total_locations_found' => count($locations),
                'sample_location' => !empty($locations) ? $locations[0] : null, // Log a sample
            ]);
           
            return $locations;
            
        } catch (GuzzleException $e) { // Specific exception for Guzzle
            $logContext = [
                'url' => $kmlUrl,
                'error_message' => $e->getMessage(),
                'trace_snippet' => implode("\n", array_slice(explode("\n", $e->getTraceAsString()), 0, 5)) // Log first 5 lines of trace
            ];

            if ($e instanceof \GuzzleHttp\Exception\RequestException) {
                $logContext['response_code'] = $e->hasResponse() ? $e->getResponse()->getStatusCode() : null;
                $logContext['response_body_snippet'] = $e->hasResponse() ? substr((string) $e->getResponse()->getBody(), 0, 500) : 'No response body available';
            }

             Log::error('Guzzle Error fetching KML data: ' . $e->getMessage(), $logContext);
            return [];
        } catch (\Exception $e) { // Catch other general exceptions
            Log::error('General Error extracting enhanced KML data: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }
    
    /**
     * Process all folders recursively and extract placemark data
     * 
     * @param \SimpleXMLElement $folders
     * @param array &$locations
     */
    private function processAllFolders($folders, &$locations)
    {
        foreach ($folders as $folder) {
            // Extract folder name for context
            $folderName = (string)$folder->name ?: 'Unnamed Folder';
            Log::debug("Processing folder: '{$folderName}'"); // Use debug level for verbosity
            
            // Process placemarks in this folder
            if (isset($folder->Placemark)) {
                 Log::debug("Found " . count($folder->Placemark) . " placemarks in folder '{$folderName}'. Processing...");
                $this->processAllPlacemarks($folder->Placemark, $locations, $folderName);
            } else {
                 Log::debug("No direct placemarks found in folder '{$folderName}'.");
            }
            
            // Process nested folders recursively
            if (isset($folder->Folder)) {
                 Log::debug("Found nested folders in '{$folderName}'. Processing recursively...");
                $this->processAllFolders($folder->Folder, $locations);
            }
        }
    }
    
    /**
     * Process all placemarks and extract location data
     * 
     * @param \SimpleXMLElement $placemarks
     * @param array &$locations
     * @param string|null $folderContext Optional folder name for context
     */
    private function processAllPlacemarks($placemarks, &$locations, $folderContext = null)
    {
         Log::debug("Processing " . count($placemarks) . " placemarks within context: '" . ($folderContext ?? 'N/A') . "'.");
        foreach ($placemarks as $placemark) {
            // Get basic placemark info
            $name = trim((string)$placemark->name);
            $description = (string)$placemark->description;
            
            // Clean description HTML
            $cleanedDescription = trim(strip_tags(preg_replace('/<br\s*\/?>/i', "\n", $description)));

            // Attempt to derive name if empty
            if (empty($name)) {
                if (!empty($folderContext) && $folderContext !== 'Document Root') {
                    $name = $folderContext; // Use folder name if placemark name is empty
                    Log::debug("Placemark name empty, using folder context: '{$name}'");
                } elseif (!empty($cleanedDescription)) {
                    // Try to extract name from first line of description
                    $lines = explode("\n", $cleanedDescription, 2); // Limit to 2 parts
                    $potentialName = trim($lines[0]);
                    if (!empty($potentialName) && strlen($potentialName) < 100) { // Basic check if it looks like a title
                         $name = $potentialName;
                         $cleanedDescription = $lines[1] ?? ''; // Use rest as description
                         Log::debug("Placemark name empty, derived from description: '{$name}'");
                    }
                }
            }
            
            // Use default name if still empty
            $name = !empty($name) ? $name : 'JOIL Location';
            
            // Try to extract coordinates from different geometry types
            $coords = $this->extractCoordinatesFromGeometry($placemark);
            
            // If coordinates were found, add location
            if ($coords) {
                $locationData = [
                    'title' => $name,
                    'description' => $cleanedDescription, // Use cleaned description
                    'lat' => $coords['lat'],
                    'lng' => $coords['lng']
                ];
                $locations[] = $locationData;
                 Log::debug("Successfully extracted Placemark: '{$name}'", ['coords' => $coords]);
            } else {
                 Log::warning("Could not extract coordinates for Placemark: '{$name}'", ['placemark_xml' => $placemark->asXML()]);
            }
        }
    }

    /**
     * Helper function to extract coordinates from various geometry types within a placemark
     *
     * @param \SimpleXMLElement $placemark
     * @return array|null ['lat' => float, 'lng' => float] or null if not found
     */
    private function extractCoordinatesFromGeometry(\SimpleXMLElement $placemark): ?array
    {
        // Check Point geometry
        if (isset($placemark->Point->coordinates)) {
            return $this->extractPointCoordinates((string)$placemark->Point->coordinates);
        }
        // Check LineString geometry
        if (isset($placemark->LineString->coordinates)) {
            return $this->extractFirstCoordinateFromLine((string)$placemark->LineString->coordinates);
        }
        // Check Polygon geometry
        if (isset($placemark->Polygon->outerBoundaryIs->LinearRing->coordinates)) {
            return $this->extractFirstCoordinateFromLine((string)$placemark->Polygon->outerBoundaryIs->LinearRing->coordinates);
        }
        // Check MultiGeometry
        if (isset($placemark->MultiGeometry)) {
            // Prioritize Point within MultiGeometry
            if (isset($placemark->MultiGeometry->Point->coordinates)) {
                return $this->extractPointCoordinates((string)$placemark->MultiGeometry->Point->coordinates);
            }
            // Then LineString
            if (isset($placemark->MultiGeometry->LineString->coordinates)) {
                return $this->extractFirstCoordinateFromLine((string)$placemark->MultiGeometry->LineString->coordinates);
            }
            // Then Polygon
            if (isset($placemark->MultiGeometry->Polygon->outerBoundaryIs->LinearRing->coordinates)) {
                return $this->extractFirstCoordinateFromLine((string)$placemark->MultiGeometry->Polygon->outerBoundaryIs->LinearRing->coordinates);
            }
            // Handle multiple Points within MultiGeometry (return the first valid one)
            if (isset($placemark->MultiGeometry->Point)) {
                foreach ($placemark->MultiGeometry->Point as $point) {
                    if (isset($point->coordinates)) {
                        $coords = $this->extractPointCoordinates((string)$point->coordinates);
                        if ($coords) return $coords; // Return the first valid coordinates found
                    }
                }
            }
        }
        
        // Check for coordinates directly under placemark (less common but possible)
        if (isset($placemark->coordinates)) {
             return $this->extractPointCoordinates((string)$placemark->coordinates);
        }

        return null; // No coordinates found
    }
    
    /**
     * Extract coordinates from a Point string (lon,lat[,alt])
     * 
     * @param string $coordinatesString
     * @return array|null
     */
    private function extractPointCoordinates($coordinatesString)
    {
        $coordinatesString = trim($coordinatesString);
        // Regex to handle potential altitude value and extra spaces
        if (preg_match('/^([-\d\.]+)\s*,\s*([-\d\.]+)(?:,\s*[-\d\.]+)?$/', $coordinatesString, $matches)) {
            // $matches[1] = longitude, $matches[2] = latitude
            if (is_numeric($matches[1]) && is_numeric($matches[2])) {
                 return [
                    'lng' => (float)$matches[1],
                    'lat' => (float)$matches[2]
                ];
            }
        }
         Log::warning("Failed to parse Point coordinates string: '{$coordinatesString}'");
        return null;
    }
    
    /**
     * Extract the first coordinate pair from a LineString or LinearRing string
     * 
     * @param string $coordinatesString
     * @return array|null
     */
    private function extractFirstCoordinateFromLine($coordinatesString)
    {
        $coordinatesString = trim($coordinatesString);
        // Get the first coordinate group (lon,lat[,alt])
        $firstCoordGroup = preg_split('/\s+/', $coordinatesString, 2)[0]; // Limit split to 2 to get only the first group
        
        if (!empty($firstCoordGroup)) {
            return $this->extractPointCoordinates($firstCoordGroup); // Reuse point parsing logic
        }
        
         Log::warning("Failed to extract first coordinate from LineString/LinearRing: '{$coordinatesString}'");
        return null;
    }

    /**
     * Method to trigger the KML processing from storage and then show the list.
     * This is the method your route will call.
     * 
     * @param string $filename Optional filename to process (defaults to joil_locations.kml)
     * @return \Illuminate\View\View
     */
    public function syncKmlAndShowList(string $filename = 'joil_locations.kml')
    {
        $kmlStoragePath = 'kml_files/' . $filename;
        $successCount = 0;
        $skippedCount = 0;
        $processingErrors = [];
        $statusMessage = '';
        $debugInfo = [];

        if (Storage::exists($kmlStoragePath)) {
            try {
                // Read KML content
                $kmlContent = Storage::get($kmlStoragePath);
                
                // Extract locations from KML
                $parsedLocations = $this->extractLocationsFromKmlString($kmlContent);
                
                // Add debug information
                $debugInfo['kml_file_size'] = strlen($kmlContent);
                $debugInfo['parsed_locations_count'] = count($parsedLocations);
                $debugInfo['sample_location'] = !empty($parsedLocations) ? $parsedLocations[0] : null;
     
                if (empty($parsedLocations)) {
                    $processingErrors[] = "No valid locations extracted from the source file in storage.";
                    Log::warning("MapData Sync: No locations extracted from {$kmlStoragePath}");
                } else {
                    DB::beginTransaction();
                    
                    try {
                        Log::info("Starting database transaction for " . count($parsedLocations) . " locations");
                        
                        foreach ($parsedLocations as $locData) {
                            try {
                                // Create a unique identifier for this location
                                $identifierValue = isset($locData['code']) && !empty($locData['code']) 
                                    ? $locData['code'] 
                                    : ($locData['name_to_use'] . '_' . $locData['latitude'] . '_' . $locData['longitude']);
                                
                                // Prepare data for database
                                $locationData = [
                                    'name' => $locData['name_to_use'],
                                    'latitude' => $locData['latitude'],
                                    'longitude' => $locData['longitude'],
                                    'status' => strtolower(isset($locData['status']) ? $locData['status'] : 'unknown'),
                                    'address' => isset($locData['address']) ? $locData['address'] : null,
                                    'description_raw' => substr(isset($locData['description_raw']) ? $locData['description_raw'] : '', 0, 255),
                                    'region' => isset($locData['region']) ? $locData['region'] : null,
                                    'city' => isset($locData['city']) ? $locData['city'] : null,
                                    'station_name_extended' => isset($locData['station_name_extended']) ? $locData['station_name_extended'] : null,
                                    'raw_placemark_name' => isset($locData['raw_placemark_name']) ? $locData['raw_placemark_name'] : null,
                                ];
                                
                                // Check for required fields
                                if (empty($locationData['name']) || 
                                    !isset($locationData['latitude']) || 
                                    !isset($locationData['longitude'])) {
                                    $skippedCount++;
                                    Log::warning("Skipping location due to missing required fields", $locationData);
                                    continue;
                                }
                                
                                // Insert or update the record
                                MapLocation::updateOrCreate(
                                    ['kml_code' => $identifierValue],
                                    $locationData
                                );
                                
                                $successCount++;
                            } catch (\Illuminate\Database\QueryException $qe) {
                                // Handle specific database errors
                                Log::error("DB Query Error during sync for '{$locData['name_to_use']}'", [
                                    'error' => $qe->getMessage(),
                                    'identifier' => $identifierValue ?? 'unknown'
                                ]);
                                
                                // Try to extract the specific cause from the SQL exception
                                $errorMessage = $qe->getMessage();
                                
                                // Check for common errors like string truncation
                                if (str_contains($errorMessage, 'truncated')) {
                                    $processingErrors[] = "Data truncation error for '{$locData['name_to_use']}': " . 
                                        $this->getReadableErrorFromException($qe);
                                } else {
                                    $processingErrors[] = "Database error for '{$locData['name_to_use']}': " . 
                                        $this->getReadableErrorFromException($qe);
                                }
                                
                                $skippedCount++;
                            } catch (\Exception $e) {
                                // Handle other errors
                                $nameToUse = isset($locData['name_to_use']) ? $locData['name_to_use'] : 'Unknown';
                                Log::error("General Error during sync for '{$nameToUse}'", [
                                    'error' => $e->getMessage(),
                                    'stack_trace' => $e->getTraceAsString()
                                ]);
                                
                                $processingErrors[] = "Error saving '{$nameToUse}': " . $e->getMessage();
                                $skippedCount++;
                            }
                        }
                        
                        // Commit transaction if we get here
                        DB::commit();
                        
                        $statusMessage = "Sync Complete: {$successCount} locations processed, {$skippedCount} skipped/errors.";
                        Log::info($statusMessage);
                    } catch (\Exception $e) {
                        // Roll back on fatal error
                        DB::rollBack();
                        
                        Log::error("Fatal Error during KML Sync Transaction", [
                            'error' => $e->getMessage(),
                            'stack_trace' => $e->getTraceAsString()
                        ]);
                        
                        $processingErrors[] = "A fatal error occurred during the database update: " . $e->getMessage();
                        $statusMessage = "Sync failed due to a fatal error.";
                    }
                }
            } catch (\Exception $e) {
                // Handle exceptions from the KML extraction process
                Log::error("Exception during KML processing", [
                    'error' => $e->getMessage(),
                    'stack_trace' => $e->getTraceAsString()
                ]);
                
                $processingErrors[] = "Error processing KML file: " . $e->getMessage();
                $statusMessage = "Failed to process KML file.";
            }
        } else {
            $statusMessage = "Location source file not found at storage/app/{$kmlStoragePath}. Please ensure it's placed there.";
            Log::error($statusMessage);
            $processingErrors[] = $statusMessage;
        }

        // --- Fetch data for the view AFTER sync attempt ---
        $allDbLocations = MapLocation::all();
        $totalCount = $allDbLocations->count();
        
        // Categorize locations by status
        $operationalCount = $allDbLocations->filter(function ($location) {
            return $location->status === 'verified' || 
                   $location->status === 'verification required' || 
                   $location->status === 'under check' || 
                   $location->status === 'check location';
        })->count();

        $maintenanceStatuses = [
            'suspended',
            'suspended/ under construction', 
            'need to manage/ request sent',
            '', 
            'need to manage/ request sent'
        ];

        $maintenanceCount = $allDbLocations->whereIn('status', $maintenanceStatuses)->count();
        $otherStatusCount = $totalCount - $operationalCount - $maintenanceCount;
        
        // Fetch paginated data for the table
        $dbLocations = MapLocation::orderBy('name')->paginate(25);

        return view('customer.map-glist', [
            'locations' => $dbLocations,
            'syncStatusMessage' => $statusMessage,
            'syncErrors' => $processingErrors,
            'totalCount' => $totalCount,
            'operationalCount' => $operationalCount,
            'maintenanceCount' => $maintenanceCount,
            'otherStatusCount' => $otherStatusCount,
            'debugInfo' => $debugInfo
        ]);
    }
    
    /**
     * Extract a readable error message from an exception
     * 
     * @param \Exception $e The exception
     * @return string A readable error message
     */
    private function getReadableErrorFromException(\Exception $e): string
    {
        $message = $e->getMessage();
        
        // Handle common database errors
        if (strpos($message, 'String or binary data would be truncated') !== false) {
            return 'Data too long for one of the fields. Try shortening text fields.';
        }
        
        if (strpos($message, 'Violation of PRIMARY KEY constraint') !== false) {
            return 'Duplicate primary key. Location already exists.';
        }
        
        if (strpos($message, 'Cannot insert duplicate key') !== false) {
            return 'Duplicate unique key. Location with the same code already exists.';
        }
        
        // Return a generic message if no specific error is detected
        return 'Database error. Check logs for details.';
    }
    
    /**
     * Special method for test KML import route
     * 
     * @param Request $request HTTP request
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncTestKml(Request $request)
    {
        // Get filename from request or use default
        $filename = $request->query('file', 'test_locations.kml');
        
        try {
            // Log that we're processing the file
            Log::info('Processing test KML file: ' . $filename);
            
            $kmlStoragePath = 'kml_files/' . $filename;
            
            // Check if file exists
            if (!Storage::exists($kmlStoragePath)) {
                Log::error('Test KML file not found: kml_files/' . $filename);
                
                // For debugging, list available files
                $files = Storage::files('kml_files');
                Log::info('Available KML files:', $files);
                
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Test KML file not found: ' . $filename,
                    'available_files' => $files
                ], 404);
            }
            
            // Get KML content
            $kmlContent = Storage::get($kmlStoragePath);
            Log::info('KML content loaded, size: ' . strlen($kmlContent) . ' bytes');
            Log::info('KML content preview: ' . substr($kmlContent, 0, 200) . '...');
            
            // Parse the KML
            $locations = $this->extractLocationsFromKmlString($kmlContent);
            Log::info('Extracted ' . count($locations) . ' locations from test KML');
            
            if (count($locations) > 0) {
                Log::info('First location data:', $locations[0]);
            }
            
            if (empty($locations)) {
                Log::error('No locations extracted from test KML file');
                return response()->json([
                    'status' => 'Error',
                    'message' => 'No locations extracted from KML file'
                ]);
            }
            
            // Save directly to database
            $successCount = 0;
            $skippedCount = 0;
            $errors = [];
            
            DB::beginTransaction();
            
            try {
                Log::info('Starting database transaction for test KML import');
                
                foreach ($locations as $location) {
                    try {
                        // Log the location being processed
                        Log::info('Processing location:', [
                            'name' => $location['name_to_use'] ?? 'Unknown',
                            'latitude' => $location['latitude'] ?? null,
                            'longitude' => $location['longitude'] ?? null,
                            'status' => $location['status'] ?? 'unknown'
                        ]);
                        
                        // Create unique identifier
                        $code = isset($location['code']) && !empty($location['code']) 
                            ? $location['code'] 
                            : ($location['name_to_use'] . '_' . $location['latitude'] . '_' . $location['longitude']);
                        
                        // Create/update location
                        $model = MapLocation::updateOrCreate(
                            ['kml_code' => $code],
                            [
                                'name' => $location['name_to_use'],
                                'title' => $location['name_to_use'], // Add title field
                                'latitude' => $location['latitude'],
                                'longitude' => $location['longitude'],
                                'status' => strtolower(isset($location['status']) ? $location['status'] : 'unknown'),
                                'region' => isset($location['region']) ? $location['region'] : null,
                                'city' => isset($location['city']) ? $location['city'] : null,
                                'address' => isset($location['address']) ? $location['address'] : null,
                                'description_raw' => substr(isset($location['description_raw']) ? $location['description_raw'] : '', 0, 255),
                                'type' => isset($location['type']) ? $location['type'] : 'standard',
                                'services' => isset($location['services']) ? $location['services'] : null,
                                'hours' => isset($location['hours']) ? $location['hours'] : null,
                                'source_map_id' => 'test-import'
                            ]
                        );
                        
                        Log::info('Saved test location: ' . $location['name_to_use'], [
                            'model_id' => $model->id,
                            'name' => $model->name,
                            'latitude' => $model->latitude,
                            'longitude' => $model->longitude
                        ]);
                        
                        $successCount++;
                    } catch (\Exception $e) {
                        $skippedCount++;
                        $errors[] = $e->getMessage();
                        Log::error('Error saving location: ' . $e->getMessage(), [
                            'exception' => get_class($e),
                            'trace' => $e->getTraceAsString()
                        ]);
                    }
                }
                
                DB::commit();
                Log::info('Test KML import complete. Saved ' . $successCount . ' locations');
                
                // Verify data for testing
                $count = MapLocation::count();
                $allLocations = MapLocation::all()->toArray();
                
                Log::info('Database check after import', [
                    'count' => $count,
                    'first_location' => $count > 0 ? $allLocations[0] : null
                ]);
                
                return response()->json([
                    'status' => 'Success',
                    'message' => 'KML data successfully imported',
                    'count' => $successCount,
                    'db_count' => $count,
                    'locations' => $allLocations,
                    'errors' => $errors,
                    'skipped' => $skippedCount
                ]);
                
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error saving test KML data: ' . $e->getMessage(), [
                    'exception' => get_class($e),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Database error: ' . $e->getMessage()
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('Exception in test KML import: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'Error',
                'message' => 'Exception: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extract locations from KML string with improved error handling and validation
     * 
     * @param string $kmlContent The KML content as a string
     * @return array Array of extracted locations
     */
    private function extractLocationsFromKmlString(string $kmlContent): array
    {
        $locations = [];
        $processingErrors = [];
        
        try {
            // Log input data size
            Log::info('Parsing KML string', [
                'content_length' => strlen($kmlContent),
                'content_start' => substr($kmlContent, 0, 100) . '...'
            ]);
            
            // Configure proper XML handling
            libxml_use_internal_errors(true);
            $xml = new \SimpleXMLElement($kmlContent);
            $loadErrors = libxml_get_errors();
            libxml_clear_errors();
            
            if (!empty($loadErrors)) {
                foreach ($loadErrors as $error) {
                    Log::error("KML Loading Error: " . trim($error->message), [
                        'line' => $error->line,
                        'column' => $error->column,
                        'code' => $error->code
                    ]);
                }
                throw new \Exception("Failed to parse XML: " . $loadErrors[0]->message);
            }
            
            // Log the XML structure
            Log::info("KML Structure", [
                'has_document' => isset($xml->Document) ? 'Yes' : 'No',
                'has_folder' => isset($xml->Document->Folder) ? 'Yes' : 'No',
                'document_name' => isset($xml->Document->name) ? (string)$xml->Document->name : 'Not set'
            ]);
            
            // Register KML namespace
            $xml->registerXPathNamespace('kml', 'http://www.opengis.net/kml/2.2');
            
            // Find all placemarks - try different XPath patterns
            $placemarks = $xml->xpath('//kml:Placemark');
            
            if (empty($placemarks)) {
                // Try alternate paths if the namespace-based query didn't work
                $placemarks = $xml->xpath('//Placemark');
                
                if (empty($placemarks)) {
                    // Try more specific paths
                    $placemarks = $xml->xpath('/kml/Document/Folder/Placemark') ?:
                                $xml->xpath('/kml/Document/Placemark');
                }
                
                if (empty($placemarks)) {
                    Log::error("KML Parser: No Placemarks found in the KML file.");
                    return [];
                }
            }
            
            Log::info("Found " . count($placemarks) . " placemarks in KML file");
            
            foreach ($placemarks as $index => $placemark) {
                try {
                    // Initialize all fields with defaults
                    $locationData = [
                        'raw_placemark_name' => '',
                        'address' => '',
                        'description_raw' => '',
                        'code' => null,
                        'region' => null,
                        'city' => null,
                        'station_name_extended' => null,
                        'name_extended' => null,
                        'name_to_use' => '',
                        'latitude' => null,
                        'longitude' => null,
                        'status' => 'unknown',
                    ];
                    
                    // Log the placemark structure
                    $placemark_struct = [];
                    foreach ((array)$placemark as $key => $value) {
                        if (is_object($value)) {
                            $placemark_struct[$key] = get_class($value);
                        } else {
                            $placemark_struct[$key] = gettype($value);
                        }
                    }
                    Log::info("Placemark {$index} structure", $placemark_struct);
                    
                    // Extract basic placemark properties
                    $locationData['raw_placemark_name'] = isset($placemark->name) ? trim((string)$placemark->name) : '';
                    $locationData['address'] = isset($placemark->address) ? trim((string)$placemark->address) : '';
                    $locationData['description_raw'] = isset($placemark->description) ? trim((string)$placemark->description) : '';
                    
                    Log::info("Placemark {$index} basic fields", [
                        'name' => $locationData['raw_placemark_name'],
                        'description_length' => strlen($locationData['description_raw'])
                    ]);
                    
                    // Process ExtendedData if available
                    if (isset($placemark->ExtendedData)) {
                        Log::info("Placemark {$index} has ExtendedData");
                        
                        if (isset($placemark->ExtendedData->Data)) {
                            Log::info("ExtendedData has " . count($placemark->ExtendedData->Data) . " data elements");
                            
                            foreach ($placemark->ExtendedData->Data as $data) {
                                $attributes = $data->attributes();
                                $dataName = (string)$attributes['name'];
                                $dataValue = trim((string)$data->value);
                                
                                Log::info("Processing ExtendedData", [
                                    'name' => $dataName,
                                    'value' => $dataValue
                                ]);
                                
                                $normalizedKey = strtolower(str_replace([' ', '-'], '_', $dataName));
                                
                                switch ($normalizedKey) {
                                    case 'code':
                                        $locationData['code'] = $dataValue;
                                        break;
                                    case 'region':
                                        $locationData['region'] = $dataValue;
                                        break;
                                    case 'city':
                                        $locationData['city'] = $dataValue;
                                        break;
                                    case 'station_name':
                                        $locationData['station_name_extended'] = $dataValue;
                                        break;
                                    case 'name':
                                        $locationData['name_extended'] = $dataValue;
                                        break;
                                    case 'gps':
                                        if (!empty($dataValue)) {
                                            $coords = explode(',', $dataValue);
                                            if (count($coords) === 2) {
                                                $locationData['latitude'] = (float)trim($coords[0]);
                                                $locationData['longitude'] = (float)trim($coords[1]);
                                            } else {
                                                Log::warning("KML Parser: Invalid GPS format '{$dataValue}' for '{$locationData['raw_placemark_name']}'");
                                            }
                                        }
                                        break;
                                    case 'status':
                                        $locationData['status'] = $dataValue;
                                        break;
                                }
                            }
                        }
                    }
                    
                    // Extract coordinates from different geometry elements if GPS data wasn't found
                    if (is_null($locationData['latitude']) || is_null($locationData['longitude'])) {
                        Log::info("Placemark {$index} needs coordinates extraction");
                        
                        // Try Point coordinates first (most common)
                        if (isset($placemark->Point->coordinates)) {
                            Log::info("Extracting from Point coordinates");
                            $coordsStr = trim((string)$placemark->Point->coordinates);
                            $this->extractCoordinatesFromString($coordsStr, $locationData);
                        }
                        // Then try LineString (if Point failed)
                        elseif (isset($placemark->LineString->coordinates)) {
                            Log::info("Extracting from LineString coordinates");
                            $coordsStr = trim((string)$placemark->LineString->coordinates);
                            $this->extractFirstCoordinateFromMultiCoords($coordsStr, $locationData);
                        }
                        // Finally try Polygon (if previous methods failed)
                        elseif (isset($placemark->Polygon->outerBoundaryIs->LinearRing->coordinates)) {
                            Log::info("Extracting from Polygon coordinates");
                            $coordsStr = trim((string)$placemark->Polygon->outerBoundaryIs->LinearRing->coordinates);
                            $this->extractFirstCoordinateFromMultiCoords($coordsStr, $locationData);
                        }
                    }
                    
                    // Log coordinates extraction result
                    Log::info("Placemark {$index} coordinates", [
                        'latitude' => $locationData['latitude'],
                        'longitude' => $locationData['longitude']
                    ]);
                    
                    // Only include locations with valid coordinates
                    if (!is_null($locationData['latitude']) && !is_null($locationData['longitude'])) {
                        // Choose the best name to use (prefer extended name, then raw placemark name)
                        $locationData['name_to_use'] = !empty($locationData['name_extended']) 
                            ? $locationData['name_extended'] 
                            : $locationData['raw_placemark_name'];
                        
                        // If still no name, create a generic one using coordinates
                        if (empty($locationData['name_to_use'])) {
                            $locationData['name_to_use'] = "Station @ " . $locationData['latitude'] . "," . $locationData['longitude'];
                        }
                        
                        Log::info("Placemark {$index} name_to_use set to: " . $locationData['name_to_use']);
                        
                        // Create a unique identifier for the location
                        $locationData['kml_code'] = $locationData['code'] ?? 
                            ($locationData['name_to_use'] . '_' . $locationData['latitude'] . '_' . $locationData['longitude']);
                        
                        // Add to the locations array
                        $locations[] = $locationData;
                        
                        Log::info("Placemark {$index} successfully processed and added to locations");
                    } else {
                        Log::warning("KML Parser: Skipping placemark '{$locationData['raw_placemark_name']}' (index: {$index}) due to missing coordinates");
                    }
                } catch (\Exception $e) {
                    // Log error but continue processing other placemarks
                    $processingErrors[] = "Error processing placemark at index {$index}: " . $e->getMessage();
                    Log::error("KML Parser Exception (placemark {$index}): " . $e->getMessage(), [
                        'exception' => get_class($e),
                        'trace' => $e->getTraceAsString()
                    ]);
                    continue;
                }
            }
        } catch (\Exception $e) {
            Log::error("KML Parsing Exception: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            throw $e; // Re-throw to handle at higher level
        }
        
        if (!empty($processingErrors)) {
            Log::warning("KML Parser: Encountered " . count($processingErrors) . " errors during processing", [
                'errors' => $processingErrors
            ]);
        }
        
        Log::info("KML Parser: Successfully extracted " . count($locations) . " locations");
        return $locations;
    }

    /**
     * Helper function to extract coordinates from a string
     * 
     * @param string $coordsStr The coordinates string
     * @param array &$locationData The location data array to update
     * @return void
     */
    private function extractCoordinatesFromString(string $coordsStr, array &$locationData): void
    {
        // KML format is typically longitude,latitude[,altitude]
        $parts = explode(',', $coordsStr);
        if (count($parts) >= 2) {
            // For Point coordinates, the order is lon,lat[,alt]
            $locationData['longitude'] = (float)trim($parts[0]);
            $locationData['latitude'] = (float)trim($parts[1]);
        }
    }

    /**
     * Helper function to extract first coordinate from multi-coordinate strings
     * Used for LineString and Polygon coordinates
     * 
     * @param string $coordsStr The coordinates string
     * @param array &$locationData The location data array to update
     * @return void
     */
    private function extractFirstCoordinateFromMultiCoords(string $coordsStr, array &$locationData): void
    {
        // Split by whitespace to get individual coordinate tuples
        $tuples = preg_split('/\s+/', $coordsStr);
        if (!empty($tuples)) {
            // Extract the first tuple
            $firstTuple = trim($tuples[0]);
            $this->extractCoordinatesFromString($firstTuple, $locationData);
        }
    }

    /**
     * Method to test KML parsing without affecting the database
     * Use for debugging KML issues
     * 
     * @param string $filename The KML file name to test
     * @return \Illuminate\Http\JsonResponse
     */
    public function testKmlParsing(string $filename = 'joil_locations.kml')
    {
        $kmlStoragePath = 'kml_files/' . $filename;
        
        if (!Storage::exists($kmlStoragePath)) {
            return response()->json([
                'success' => false,
                'message' => "KML file not found: {$kmlStoragePath}"
            ], 404);
        }
        
        try {
            $kmlContent = Storage::get($kmlStoragePath);
            $locations = $this->extractLocationsFromKmlString($kmlContent);
            
            return response()->json([
                'success' => true,
                'message' => 'KML parsed successfully',
                'location_count' => count($locations),
                'sample_locations' => array_slice($locations, 0, 5)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error parsing KML: ' . $e->getMessage()
            ], 500);
        }
    }

    // Add after the syncTestKml method
    
    /**
     * Process KML synchronization without rendering a view
     * Used by the console command
     * 
     * @param string $filename The name of the KML file
     * @return array Result data
     */
    public function processSyncKml(string $filename = 'joil_locations.kml'): array
    {
        $kmlStoragePath = 'kml_files/' . $filename;
        $successCount = 0;
        $skippedCount = 0;
        $processingErrors = [];
        
        if (!Storage::exists($kmlStoragePath)) {
            return [
                'success' => false,
                'message' => "File not found: {$kmlStoragePath}",
                'success_count' => 0,
                'skipped_count' => 0,
                'errors' => ["File not found: {$kmlStoragePath}"]
            ];
        }
        
        try {
            // Read KML content
            $kmlContent = Storage::get($kmlStoragePath);
            
            // Extract locations from KML
            $parsedLocations = $this->extractLocationsFromKmlString($kmlContent);
            
            if (empty($parsedLocations)) {
                return [
                    'success' => false,
                    'message' => 'No valid locations extracted from file',
                    'success_count' => 0,
                    'skipped_count' => 0,
                    'errors' => ['No valid locations extracted from file']
                ];
            }
            
            DB::beginTransaction();
            
            try {
                Log::info("Starting database transaction for " . count($parsedLocations) . " locations");
                
                foreach ($parsedLocations as $locData) {
                    try {
                        // Create a unique identifier for this location
                        $identifierValue = isset($locData['code']) && !empty($locData['code']) 
                            ? $locData['code'] 
                            : ($locData['name_to_use'] . '_' . $locData['latitude'] . '_' . $locData['longitude']);
                        
                        // Prepare data for database
                        $locationData = [
                            'name' => $locData['name_to_use'],
                            'latitude' => $locData['latitude'],
                            'longitude' => $locData['longitude'],
                            'status' => strtolower(isset($locData['status']) ? $locData['status'] : 'unknown'),
                            'address' => isset($locData['address']) ? $locData['address'] : null,
                            'description_raw' => substr(isset($locData['description_raw']) ? $locData['description_raw'] : '', 0, 255),
                            'region' => isset($locData['region']) ? $locData['region'] : null,
                            'city' => isset($locData['city']) ? $locData['city'] : null,
                            'station_name_extended' => isset($locData['station_name_extended']) ? $locData['station_name_extended'] : null,
                            'raw_placemark_name' => isset($locData['raw_placemark_name']) ? $locData['raw_placemark_name'] : null,
                        ];
                        
                        // Check for required fields
                        if (empty($locationData['name']) || 
                            !isset($locationData['latitude']) || 
                            !isset($locationData['longitude'])) {
                            $skippedCount++;
                            Log::warning("Skipping location due to missing required fields", $locationData);
                            continue;
                        }
                        
                        // Insert or update the record
                        MapLocation::updateOrCreate(
                            ['kml_code' => $identifierValue],
                            $locationData
                        );
                        
                        $successCount++;
                    } catch (\Illuminate\Database\QueryException $qe) {
                        // Handle specific database errors
                        Log::error("DB Query Error during sync for '{$locData['name_to_use']}'", [
                            'error' => $qe->getMessage(),
                            'identifier' => $identifierValue ?? 'unknown'
                        ]);
                        
                        $processingErrors[] = "Database error for '{$locData['name_to_use']}': " . 
                            $this->getReadableErrorFromException($qe);
                        
                        $skippedCount++;
                    } catch (\Exception $e) {
                        // Handle other errors
                        $nameToUse = isset($locData['name_to_use']) ? $locData['name_to_use'] : 'Unknown';
                        Log::error("General Error during sync for '{$nameToUse}'", [
                            'error' => $e->getMessage(),
                            'stack_trace' => $e->getTraceAsString()
                        ]);
                        
                        $processingErrors[] = "Error saving '{$nameToUse}': " . $e->getMessage();
                        $skippedCount++;
                    }
                }
                
                // Commit transaction if we get here
                DB::commit();
                
                Log::info("KML Sync Complete: {$successCount} locations processed, {$skippedCount} skipped/errors.");
                
                return [
                    'success' => true,
                    'message' => "Sync Complete: {$successCount} locations processed, {$skippedCount} skipped/errors.",
                    'success_count' => $successCount,
                    'skipped_count' => $skippedCount,
                    'errors' => $processingErrors
                ];
                
            } catch (\Exception $e) {
                // Roll back on fatal error
                DB::rollBack();
                
                Log::error("Fatal Error during KML Sync Transaction", [
                    'error' => $e->getMessage(),
                    'stack_trace' => $e->getTraceAsString()
                ]);
                
                return [
                    'success' => false,
                    'message' => "Fatal error during sync: " . $e->getMessage(),
                    'success_count' => $successCount,
                    'skipped_count' => $skippedCount,
                    'errors' => array_merge($processingErrors, ["Fatal error: " . $e->getMessage()])
                ];
            }
        } catch (\Exception $e) {
            // Handle exceptions from the KML extraction process
            Log::error("Exception during KML processing", [
                'error' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => "Error processing KML file: " . $e->getMessage(),
                'success_count' => 0,
                'skipped_count' => 0,
                'errors' => ["Error processing KML file: " . $e->getMessage()]
            ];
        }
    }

    // Function moved to avoid duplication

    // Methods already defined elsewhere in this class

    // Implementation moved to fix duplicate method errors

    /**
     * Ensure proper encoding of Arabic text before saving to database
     *
     * @param string $text The text to process
     * @return string The processed text
     */
    private function ensureProperArabicEncoding($text)
    {
        // Use the dedicated utility class for consistent handling
        return ArabicTextUtils::sanitize($text);
    }
}
