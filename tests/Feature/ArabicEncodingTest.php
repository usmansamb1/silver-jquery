<?php

namespace Tests\Feature;

use App\Models\MapLocation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ArabicEncodingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating and retrieving a model with Arabic text
     */
    public function test_arabic_text_in_map_location(): void
    {
        // Define sample Arabic text
        $arabicName = 'القلعة';
        $arabicCity = 'الرياض';
        $arabicRegion = 'المنطقة الوسطى';
        
        // Create a map location with Arabic text
        $location = MapLocation::create([
            'name' => $arabicName,
            'title' => $arabicName,
            'city' => $arabicCity,
            'region' => $arabicRegion,
            'latitude' => 24.7136,
            'longitude' => 46.6753,
            'status' => 'verified',
            'kml_code' => 'test_location_' . time()
        ]);
        
        // Get the ID of the created location
        $locationId = $location->id;
        
        // Clear the model instance to force a fresh database fetch
        $location = null;
        
        // Fetch the location from database
        $fetchedLocation = MapLocation::find($locationId);
        
        // Verify the Arabic text is retrieved correctly
        $this->assertEquals($arabicName, $fetchedLocation->name);
        $this->assertEquals($arabicCity, $fetchedLocation->city);
        $this->assertEquals($arabicRegion, $fetchedLocation->region);
    }
    
    /**
     * Test that the sanitization functions work properly
     */
    public function test_arabic_text_sanitization(): void
    {
        // Create a mixed text with Arabic, Latin and some problematic characters
        $mixedText = "Test مرحبا بك! \x00\x1F Test";
        $expectedText = "Test مرحبا بك!  Test";
        
        // Use reflection to access the private method
        $controller = new \App\Http\Controllers\Customer\MapMarksController();
        $reflectionMethod = new \ReflectionMethod($controller, 'ensureProperArabicEncoding');
        $reflectionMethod->setAccessible(true);
        
        // Call the method
        $result = $reflectionMethod->invoke($controller, $mixedText);
        
        // Verify the sanitization worked correctly
        $this->assertEquals($expectedText, $result);
    }
    
    /**
     * Test updating existing records
     */
    public function test_updating_with_arabic_text(): void
    {
        // Create a map location with Latin text
        $location = MapLocation::create([
            'name' => 'Test Location',
            'title' => 'Test Location',
            'city' => 'Test City',
            'region' => 'Test Region',
            'latitude' => 24.7136,
            'longitude' => 46.6753,
            'status' => 'verified',
            'kml_code' => 'test_update_' . time()
        ]);
        
        // Update the location with Arabic text
        $location->name = 'موقع الاختبار';
        $location->city = 'مدينة الاختبار';
        $location->save();
        
        // Get the ID of the updated location
        $locationId = $location->id;
        
        // Clear the model instance to force a fresh database fetch
        $location = null;
        
        // Fetch the location from database
        $fetchedLocation = MapLocation::find($locationId);
        
        // Verify the Arabic text was saved and retrieved correctly
        $this->assertEquals('موقع الاختبار', $fetchedLocation->name);
        $this->assertEquals('مدينة الاختبار', $fetchedLocation->city);
    }
} 