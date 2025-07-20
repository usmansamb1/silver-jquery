<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MapLocation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FixMapLocationEncoding extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'map:fix-encoding {--transliterate : Also create transliterated versions of Arabic text} {--force : Force update all records}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix character encoding issues in MapLocation records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting character encoding fix for map locations...');
        $transliterate = $this->option('transliterate');
        $force = $this->option('force');

        if ($transliterate) {
            $this->info('Transliteration mode enabled - will create Latin versions of Arabic text');
        }

        if ($force) {
            $this->info('Force mode enabled - will update all records regardless of current state');
        }

        try {
            // Get all map locations
            $totalLocations = MapLocation::count();
            $this->info("Found {$totalLocations} locations to process");

            // Set up a progress bar
            $bar = $this->output->createProgressBar($totalLocations);
            $bar->start();

            $fixedCount = 0;
            $errorCount = 0;

            // Process in chunks to avoid memory issues
            MapLocation::query()->chunk(100, function ($locations) use ($bar, $transliterate, $force, &$fixedCount, &$errorCount) {
                DB::beginTransaction();
                
                try {
                    foreach ($locations as $location) {
                        $result = $this->fixLocationEncoding($location, $transliterate, $force);
                        if ($result) {
                            $fixedCount++;
                        }
                        $bar->advance();
                    }
                    
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    $errorCount++;
                    $this->error("Error processing chunk: " . $e->getMessage());
                    Log::error("Error in map:fix-encoding: " . $e->getMessage(), [
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            });

            $bar->finish();
            $this->newLine(2);
            $this->info("Character encoding fix completed!");
            $this->info("Fixed {$fixedCount} records with {$errorCount} errors");
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to fix character encoding: ' . $e->getMessage());
            Log::error("Failed to run map:fix-encoding: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return 1;
        }
    }

    /**
     * Fix character encoding for a single location
     * 
     * @param MapLocation $location
     * @param bool $transliterate Whether to create Latin versions of Arabic text
     * @param bool $force Whether to force update regardless of current state
     * @return bool Whether changes were made
     */
    private function fixLocationEncoding(MapLocation $location, bool $transliterate = false, bool $force = false)
    {
        // Fields that might contain Arabic text
        $textFields = [
            'name',
            'title',
            'city',
            'region',
            'address',
            'description_raw'
        ];

        $changed = false;

        foreach ($textFields as $field) {
            if (!empty($location->$field) || $force) {
                $originalValue = $location->$field ?? '';
                
                // Check if the field contains only question marks or placeholders
                $isQuestionMarks = $this->isOnlyQuestionMarks($originalValue);
                
                // Apply encoding fixes
                $fixedValue = $this->cleanTextEncoding($originalValue);
                
                // Apply transliteration if needed
                if ($transliterate && $this->containsArabic($fixedValue)) {
                    // Create a latin version using transliteration
                    $latinVersion = $this->transliterateArabic($fixedValue);
                    
                    // If the original is just question marks, replace it
                    if ($isQuestionMarks) {
                        $fixedValue = $latinVersion;
                    }
                }
                
                // Only update if there's a change or if force is enabled
                if ($force || $fixedValue !== $originalValue) {
                    $location->$field = $fixedValue;
                    $changed = true;
                }
            }
        }

        // Generate a safe kml_code if needed
        if ($force || empty($location->kml_code) || str_contains($location->kml_code, '?') || str_contains($location->kml_code, '')) {
            $location->kml_code = mb_substr(preg_replace('/[^a-zA-Z0-9_]/', '_', $location->name), 0, 30) . '_' . 
                               $location->latitude . '_' . $location->longitude;
            $changed = true;
        }

        // Save if any changes were made
        if ($changed) {
            try {
                $location->saveQuietly(); // Skip events, to avoid infinite loops
                return true;
            } catch (\Exception $e) {
                Log::error("Failed to save location ID {$location->id}: " . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                return false;
            }
        }
        
        return false;
    }

    /**
     * Check if a string contains only question marks or placeholders
     * 
     * @param string $text
     * @return bool
     */
    private function isOnlyQuestionMarks($text)
    {
        if (empty($text)) {
            return false;
        }
        
        // Remove spaces and check if only contains question marks or common placeholder symbols
        $stripped = preg_replace('/\s+/', '', $text);
        return preg_match('/^[\?\x{FFFD}]+$/u', $stripped) || 
               preg_match('/^[?]+$/', $stripped);
    }
    
    /**
     * Check if a string contains Arabic characters
     * 
     * @param string $text
     * @return bool
     */
    private function containsArabic($text)
    {
        return preg_match('/[\x{0600}-\x{06FF}\x{0750}-\x{077F}]/u', $text);
    }
    
    /**
     * Transliterate Arabic text to Latin alphabet
     * 
     * @param string $text
     * @return string
     */
    private function transliterateArabic($text)
    {
        // Simple mapping table for common Arabic characters
        $arabicToLatin = [
            // Basic mapping of Arabic letters to Latin equivalents
            'ا' => 'a', 'أ' => 'a', 'إ' => 'i', 'آ' => 'a',
            'ب' => 'b', 'ت' => 't', 'ث' => 'th',
            'ج' => 'j', 'ح' => 'h', 'خ' => 'kh',
            'د' => 'd', 'ذ' => 'th', 'ر' => 'r',
            'ز' => 'z', 'س' => 's', 'ش' => 'sh',
            'ص' => 's', 'ض' => 'd', 'ط' => 't',
            'ظ' => 'z', 'ع' => 'a', 'غ' => 'gh',
            'ف' => 'f', 'ق' => 'q', 'ك' => 'k',
            'ل' => 'l', 'م' => 'm', 'ن' => 'n',
            'ه' => 'h', 'و' => 'w', 'ي' => 'y',
            'ى' => 'a', 'ة' => 'h', 'ء' => 'a',
            'ؤ' => 'o', 'ئ' => 'e'
        ];
        
        // Replace each Arabic character with its Latin equivalent
        $transliterated = $text;
        foreach ($arabicToLatin as $arabic => $latin) {
            $transliterated = str_replace($arabic, $latin, $transliterated);
        }
        
        // For characters we haven't mapped, just keep them
        return $transliterated;
    }

    /**
     * Clean text encoding for storage in the database
     */
    private function cleanTextEncoding($text)
    {
        if (empty($text)) {
            return '';
        }
        
        // Convert to UTF-8 and remove invalid characters
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        
        // Remove HTML entities
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        
        // Remove any remaining invalid characters
        $text = preg_replace('/\x{FFFD}|/u', '', $text);
        
        // Replace sequences of question marks with spaces
        $text = preg_replace('/\?{2,}/', ' ', $text);
        
        // Truncate if necessary
        if (mb_strlen($text, 'UTF-8') > 200) {
            $text = mb_substr($text, 0, 200, 'UTF-8');
        }
        
        return trim($text);
    }
} 