<?php

namespace App\Console\Commands;

use App\Models\MapLocation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SyncTestKml extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kml:sync-test {filename=test_locations.kml}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync test KML file for testing purposes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filename = $this->argument('filename');
        $kmlPath = 'kml_files/' . $filename;
        
        // Check if file exists
        if (!Storage::exists($kmlPath)) {
            // Try to copy from fixtures if not exists
            if (file_exists(base_path('tests/fixtures/' . $filename))) {
                Storage::put(
                    $kmlPath, 
                    file_get_contents(base_path('tests/fixtures/' . $filename))
                );
                $this->info("Copied test KML file from fixtures to storage");
            } else {
                $this->error("KML file not found: {$kmlPath}");
                return 1;
            }
        }
        
        // Process the KML file directly
        $this->info("Processing test KML file: {$filename}");
        
        try {
            // Get the KML content
            $kmlContent = Storage::get($kmlPath);
            
            // Process the KML content
            $syncCommand = new SyncKmlLocations();
            $locations = $syncCommand->extractLocationsFromKml($kmlContent);
            
            $this->info("Found " . count($locations) . " locations in KML file");
            
            if (count($locations) > 0) {
                // Save to database
                $this->info("Saving locations to database...");
                $result = $syncCommand->saveLocationsToDatabase($locations);
                
                $this->info("Sync complete");
                $this->info("Success: {$result['success_count']} locations");
                $this->info("Skipped: {$result['skipped_count']} locations");
                
                if (count($result['errors']) > 0) {
                    $this->warn("Errors encountered:");
                    foreach ($result['errors'] as $error) {
                        $this->error("- " . $error);
                    }
                }
            }
            
            // Get all locations from database and print them
            $dbLocations = MapLocation::all();
            $this->info("Found {$dbLocations->count()} locations in database");
            
            foreach ($dbLocations as $location) {
                $this->line("- {$location->name}: ({$location->latitude}, {$location->longitude})");
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
}
