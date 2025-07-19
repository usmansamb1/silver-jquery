<?php

namespace App\Console\Commands;

use App\Http\Controllers\Customer\MapMarksController;
use App\Models\MapLocation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SyncKmlLocations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kml:sync 
                            {filename=joil_locations.kml : The name of the KML file to process}
                            {--test : Run in test mode without saving to database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process KML files and sync locations to the database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $filename = $this->argument('filename');
        $testMode = $this->option('test');
        
        $kmlStoragePath = 'kml_files/' . $filename;
        
        // Check if file exists
        if (!Storage::exists($kmlStoragePath)) {
            $this->error("KML file not found: storage/app/{$kmlStoragePath}");
            return 1;
        }
        
        $this->info("Processing KML file: {$filename}");
        
        // Use our own implementation rather than relying on private methods in the controller
        $kmlContent = Storage::get($kmlStoragePath);
        
        try {
            $locations = $this->extractLocationsFromKml($kmlContent);
            
            $this->info("Successfully parsed {$filename}");
            $this->info("Found " . count($locations) . " locations");
            
            if (count($locations) > 0) {
                // Display sample location
                $this->info("Sample location:");
                $this->table(
                    ['Field', 'Value'],
                    $this->formatLocationForTable($locations[0])
                );
                
                if (!$testMode) {
                    // Save to database
                    $this->info("Syncing locations to database...");
                    $result = $this->saveLocationsToDatabase($locations);
                    
                    $this->info("Sync complete");
                    $this->info("Success: {$result['success_count']} locations");
                    $this->info("Skipped: {$result['skipped_count']} locations");
                    
                    if (count($result['errors']) > 0) {
                        $this->warn("Errors encountered:");
                        foreach ($result['errors'] as $error) {
                            $this->line("- " . $error);
                        }
                    }
                } else {
                    $this->info("Test mode - not saving to database");
                }
            }
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Error processing KML: " . $e->getMessage());
            Log::error("KML Sync Error: " . $e->getMessage(), [
                'file' => $filename,
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
    
    /**
     * Extract locations from KML content
     *
     * @param string $kmlContent
     * @return array
     */
    public function extractLocationsFromKml(string $kmlContent): array
    {
        $locations = [];
        
        // Configure proper XML handling
        libxml_use_internal_errors(true);
        
        try {
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
            
            // Register KML namespace
            $xml->registerXPathNamespace('kml', 'http://www.opengis.net/kml/2.2');
            
            // Find all placemarks - try different XPath patterns
            $placemarks = $xml->xpath('//kml:Placemark') ?: $xml->xpath('//Placemark');
            
            if (empty($placemarks)) {
                // Try more specific paths
                $placemarks = $xml->xpath('/kml/Document/Folder/Placemark') ?: $xml->xpath('/kml/Document/Placemark');
                
                if (empty($placemarks)) {
                    Log::error("KML Parser: No Placemarks found in the KML file.");
                    return [];
                }
            }
            
            Log::info("Found " . count($placemarks) . " placemarks in KML file");
            
            foreach ($placemarks as $index => $placemark) {
                try {
                    // Initialize location data
                    $locationData = $this->initializeLocationData();
                    
                    // Extract basic placemark properties
                    $locationData['raw_placemark_name'] = isset($placemark->name) ? trim((string)$placemark->name) : '';
                    $locationData['address'] = isset($placemark->address) ? trim((string)$placemark->address) : '';
                    $locationData['description_raw'] = isset($placemark->description) ? trim((string)$placemark->description) : '';
                    
                    // Process ExtendedData if available
                    if (isset($placemark->ExtendedData) && isset($placemark->ExtendedData->Data)) {
                        foreach ($placemark->ExtendedData->Data as $data) {
                            $attributes = $data->attributes();
                            $dataName = (string)$attributes['name'];
                            $dataValue = trim((string)$data->value);
                            
                            $this->processExtendedData($locationData, $dataName, $dataValue);
                        }
                    }
                    
                    // Extract coordinates
                    $this->extractCoordinatesFromPlacemark($placemark, $locationData);
                    
                    // Only include locations with valid coordinates
                    if (!is_null($locationData['latitude']) && !is_null($locationData['longitude'])) {
                        // Choose the best name to use
                        $locationData['name_to_use'] = !empty($locationData['name_extended']) 
                            ? $locationData['name_extended'] 
                            : (!empty($locationData['raw_placemark_name']) ? $locationData['raw_placemark_name'] : 
                              "Station @ " . $locationData['latitude'] . "," . $locationData['longitude']);
                        
                        // Create a unique identifier for the location
                        $locationData['kml_code'] = $locationData['code'] ?? 
                            ($locationData['name_to_use'] . '_' . $locationData['latitude'] . '_' . $locationData['longitude']);
                        
                        // Add to the locations array
                        $locations[] = $locationData;
                    }
                } catch (\Exception $e) {
                    Log::error("Error processing placemark at index {$index}: " . $e->getMessage());
                    continue;
                }
            }
            
            return $locations;
            
        } catch (\Exception $e) {
            Log::error("KML Parsing Exception: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Initialize location data with default values
     *
     * @return array
     */
    protected function initializeLocationData(): array
    {
        return [
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
    }
    
    /**
     * Process extended data from placemark
     *
     * @param array &$locationData
     * @param string $dataName
     * @param string $dataValue
     * @return void
     */
    protected function processExtendedData(array &$locationData, string $dataName, string $dataValue): void
    {
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
            case 'title':
                $locationData['title'] = $dataValue;
                break;
            case 'gps':
                if (!empty($dataValue)) {
                    $coords = explode(',', $dataValue);
                    if (count($coords) >= 2) {
                        $locationData['latitude'] = (float)trim($coords[0]);
                        $locationData['longitude'] = (float)trim($coords[1]);
                    }
                }
                break;
            case 'status':
                $locationData['status'] = $dataValue;
                break;
            case 'type':
                $locationData['type'] = $dataValue;
                break;
            case 'services':
                $locationData['services'] = $dataValue;
                break;
            case 'hours':
                $locationData['hours'] = $dataValue;
                break;
        }
        
        // Log the data being processed for debugging
        Log::debug("Processed ExtendedData", [
            'name' => $dataName,
            'value' => $dataValue,
            'normalized_key' => $normalizedKey
        ]);
    }
    
    /**
     * Extract coordinates from placemark
     *
     * @param \SimpleXMLElement $placemark
     * @param array &$locationData
     * @return void
     */
    protected function extractCoordinatesFromPlacemark(\SimpleXMLElement $placemark, array &$locationData): void
    {
        // Try Point coordinates first (most common)
        if (isset($placemark->Point->coordinates)) {
            $coordsStr = trim((string)$placemark->Point->coordinates);
            $this->extractCoordinatesFromString($coordsStr, $locationData);
        }
        // Then try LineString
        elseif (isset($placemark->LineString->coordinates)) {
            $coordsStr = trim((string)$placemark->LineString->coordinates);
            $this->extractFirstCoordinateFromMultiCoords($coordsStr, $locationData);
        }
        // Finally try Polygon
        elseif (isset($placemark->Polygon->outerBoundaryIs->LinearRing->coordinates)) {
            $coordsStr = trim((string)$placemark->Polygon->outerBoundaryIs->LinearRing->coordinates);
            $this->extractFirstCoordinateFromMultiCoords($coordsStr, $locationData);
        }
    }
    
    /**
     * Extract coordinates from a string
     *
     * @param string $coordsStr
     * @param array &$locationData
     * @return void
     */
    protected function extractCoordinatesFromString(string $coordsStr, array &$locationData): void
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
     * Extract first coordinate from multi-coordinate strings
     *
     * @param string $coordsStr
     * @param array &$locationData
     * @return void
     */
    protected function extractFirstCoordinateFromMultiCoords(string $coordsStr, array &$locationData): void
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
     * Save locations to database
     *
     * @param array $locations
     * @return array
     */
    public function saveLocationsToDatabase(array $locations): array
    {
        $result = [
            'success_count' => 0,
            'skipped_count' => 0,
            'errors' => []
        ];
        
        DB::beginTransaction();
        
        try {
            foreach ($locations as $location) {
                try {
                    // Add debugging information before saving
                    Log::info('Saving location to database', [
                        'name_to_use' => $location['name_to_use'],
                        'latitude' => $location['latitude'],
                        'longitude' => $location['longitude'],
                        'kml_code' => $location['kml_code']
                    ]);
                    
                    // Create/update location
                    $model = MapLocation::updateOrCreate(
                        ['kml_code' => $location['kml_code']],
                        [
                            'name' => $location['name_to_use'],
                            'title' => $location['name_to_use'],
                            'latitude' => $location['latitude'],
                            'longitude' => $location['longitude'],
                            'status' => strtolower($location['status']),
                            'region' => $location['region'],
                            'city' => $location['city'],
                            'address' => $location['address'],
                            'description_raw' => $location['description_raw'],
                            'source_map_id' => Str::uuid()->toString(),
                            'type' => $location['type'] ?? 'standard',
                            'services' => $location['services'] ?? null,
                            'hours' => $location['hours'] ?? null
                        ]
                    );
                    
                    Log::info('Saved location: ' . $location['name_to_use'], [
                        'model_id' => $model->id,
                        'latitude' => $model->latitude,
                        'longitude' => $model->longitude
                    ]);
                    
                    $result['success_count']++;
                } catch (\Exception $e) {
                    $result['errors'][] = "Error saving {$location['name_to_use']}: " . $e->getMessage();
                    Log::error("Error saving location {$location['name_to_use']}: " . $e->getMessage(), [
                        'exception' => $e,
                        'trace' => $e->getTraceAsString(),
                        'location_data' => $location
                    ]);
                    $result['skipped_count']++;
                }
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Database transaction error: " . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
        
        return $result;
    }
    
    /**
     * Format a location for table display
     *
     * @param array $location
     * @return array
     */
    protected function formatLocationForTable(array $location): array
    {
        $result = [];
        
        // Select key fields to display
        $keysToShow = [
            'name_to_use', 'kml_code', 'latitude', 'longitude', 
            'status', 'city', 'region'
        ];
        
        foreach ($keysToShow as $key) {
            if (isset($location[$key])) {
                $result[] = [$key, $location[$key]];
            }
        }
        
        return $result;
    }
} 