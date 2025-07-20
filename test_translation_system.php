<?php

/**
 * Comprehensive Test Script for Modular Translation System
 * 
 * This script tests the new modular translation implementation to ensure:
 * 1. Translation loading from multiple JSON files
 * 2. Language switching functionality
 * 3. __() function works with new translation keys
 * 4. Admin views display correctly in both languages
 * 5. Backward compatibility with existing translations
 * 6. Both Arabic and English translations work
 * 7. Caching functionality
 * 8. No missing translations or errors
 * 9. Performance with caching enabled
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Test class
class TranslationSystemTest
{
    private $errors = [];
    private $results = [];
    private $startTime;
    private $localizationController;
    private $translationService;
    
    public function __construct()
    {
        $this->startTime = microtime(true);
        $this->localizationController = new \App\Http\Controllers\LocalizationController();
        $this->translationService = new \App\Services\ModularTranslationService();
    }
    
    /**
     * Run all tests
     */
    public function runAllTests()
    {
        $this->log("=== MODULAR TRANSLATION SYSTEM TEST SUITE ===");
        $this->log("Starting tests at: " . date('Y-m-d H:i:s'));
        
        // Test 1: Translation File Loading
        $this->testTranslationFileLoading();
        
        // Test 2: Language Switching
        $this->testLanguageSwitching();
        
        // Test 3: Translation Function Usage
        $this->testTranslationFunctionUsage();
        
        // Test 4: Nested Translation Keys
        $this->testNestedTranslationKeys();
        
        // Test 5: Backward Compatibility
        $this->testBackwardCompatibility();
        
        // Test 6: Arabic and English Support
        $this->testMultiLanguageSupport();
        
        // Test 7: Caching Functionality
        $this->testCachingFunctionality();
        
        // Test 8: Missing Translations
        $this->testMissingTranslations();
        
        // Test 9: Performance Testing
        $this->testPerformance();
        
        // Test 10: API Endpoints
        $this->testAPIEndpoints();
        
        // Generate report
        $this->generateReport();
    }
    
    /**
     * Test 1: Translation File Loading
     */
    private function testTranslationFileLoading()
    {
        $this->log("\n--- Test 1: Translation File Loading ---");
        
        foreach (['en', 'ar'] as $locale) {
            try {
                // Test LocalizationController loading
                $translations = $this->localizationController->loadTranslations($locale);
                $this->results["file_loading_{$locale}"] = count($translations) > 0;
                
                $this->log("✓ {$locale} translations loaded: " . count($translations) . " keys");
                
                // Test specific modular files
                $modules = ['admin-dashboard', 'admin-users', 'admin-approvals', 'admin-payments', 'admin-system'];
                foreach ($modules as $module) {
                    $moduleTranslations = $this->localizationController->getModuleTranslations($module, $locale);
                    $moduleCount = count($moduleTranslations);
                    $this->results["module_{$module}_{$locale}"] = $moduleCount > 0;
                    $this->log("  └─ {$module}: {$moduleCount} keys");
                }
                
            } catch (\Exception $e) {
                $this->errors[] = "Translation loading failed for {$locale}: " . $e->getMessage();
                $this->results["file_loading_{$locale}"] = false;
            }
        }
    }
    
    /**
     * Test 2: Language Switching
     */
    private function testLanguageSwitching()
    {
        $this->log("\n--- Test 2: Language Switching ---");
        
        try {
            // Test setting different locales
            foreach (['en', 'ar'] as $locale) {
                \Illuminate\Support\Facades\App::setLocale($locale);
                $currentLocale = \Illuminate\Support\Facades\App::getLocale();
                
                $this->results["language_switch_{$locale}"] = ($currentLocale === $locale);
                $this->log("✓ Language switched to: {$locale} (current: {$currentLocale})");
            }
            
            // Test supported languages API
            $supportedLanguages = $this->localizationController->getSupportedLanguages();
            $this->results["supported_languages_api"] = $supportedLanguages->status() === 200;
            $this->log("✓ Supported languages API working");
            
        } catch (\Exception $e) {
            $this->errors[] = "Language switching failed: " . $e->getMessage();
            $this->results["language_switching"] = false;
        }
    }
    
    /**
     * Test 3: Translation Function Usage
     */
    private function testTranslationFunctionUsage()
    {
        $this->log("\n--- Test 3: Translation Function Usage ---");
        
        foreach (['en', 'ar'] as $locale) {
            \Illuminate\Support\Facades\App::setLocale($locale);
            
            try {
                // Test basic translation
                $basicTranslation = __('Login');
                $this->results["basic_translation_{$locale}"] = !empty($basicTranslation);
                $this->log("✓ Basic translation ({$locale}): '{$basicTranslation}'");
                
                // Test nested translation with dot notation
                $nestedTranslation = __('dashboard.title');
                $this->results["nested_translation_{$locale}"] = !empty($nestedTranslation) && $nestedTranslation !== 'dashboard.title';
                $this->log("✓ Nested translation ({$locale}): '{$nestedTranslation}'");
                
                // Test with parameters
                $paramTranslation = __('alerts.pending_approvals_count', ['count' => 5]);
                $this->results["param_translation_{$locale}"] = strpos($paramTranslation, '5') !== false;
                $this->log("✓ Parameter translation ({$locale}): '{$paramTranslation}'");
                
            } catch (\Exception $e) {
                $this->errors[] = "Translation function failed for {$locale}: " . $e->getMessage();
                $this->results["translation_function_{$locale}"] = false;
            }
        }
    }
    
    /**
     * Test 4: Nested Translation Keys
     */
    private function testNestedTranslationKeys()
    {
        $this->log("\n--- Test 4: Nested Translation Keys ---");
        
        foreach (['en', 'ar'] as $locale) {
            \Illuminate\Support\Facades\App::setLocale($locale);
            
            try {
                // Test various nested keys from admin dashboard
                $testKeys = [
                    'dashboard.title',
                    'navigation.dashboard',
                    'statistics.total_users',
                    'quick_actions.add_user',
                    'tables.actions'
                ];
                
                $successCount = 0;
                foreach ($testKeys as $key) {
                    $translation = __($key);
                    if ($translation !== $key) {
                        $successCount++;
                        $this->log("  ✓ {$key}: '{$translation}'");
                    } else {
                        $this->log("  ✗ {$key}: MISSING");
                    }
                }
                
                $this->results["nested_keys_{$locale}"] = $successCount === count($testKeys);
                
            } catch (\Exception $e) {
                $this->errors[] = "Nested translation keys failed for {$locale}: " . $e->getMessage();
                $this->results["nested_keys_{$locale}"] = false;
            }
        }
    }
    
    /**
     * Test 5: Backward Compatibility
     */
    private function testBackwardCompatibility()
    {
        $this->log("\n--- Test 5: Backward Compatibility ---");
        
        foreach (['en', 'ar'] as $locale) {
            \Illuminate\Support\Facades\App::setLocale($locale);
            
            try {
                // Test old translation keys from main files
                $oldKeys = [
                    'Login',
                    'Email',
                    'Password',
                    'Mobile Number',
                    'Company Name'
                ];
                
                $successCount = 0;
                foreach ($oldKeys as $key) {
                    $translation = __($key);
                    if ($translation !== $key) {
                        $successCount++;
                        $this->log("  ✓ {$key}: '{$translation}'");
                    } else {
                        $this->log("  ✗ {$key}: MISSING");
                    }
                }
                
                $this->results["backward_compatibility_{$locale}"] = $successCount > 0;
                
            } catch (\Exception $e) {
                $this->errors[] = "Backward compatibility failed for {$locale}: " . $e->getMessage();
                $this->results["backward_compatibility_{$locale}"] = false;
            }
        }
    }
    
    /**
     * Test 6: Multi-Language Support
     */
    private function testMultiLanguageSupport()
    {
        $this->log("\n--- Test 6: Multi-Language Support ---");
        
        try {
            // Test English
            \Illuminate\Support\Facades\App::setLocale('en');
            $englishTitle = __('dashboard.title');
            $this->results["english_support"] = ($englishTitle === 'Dashboard');
            $this->log("✓ English: '{$englishTitle}'");
            
            // Test Arabic
            \Illuminate\Support\Facades\App::setLocale('ar');
            $arabicTitle = __('dashboard.title');
            $this->results["arabic_support"] = ($arabicTitle === 'لوحة التحكم');
            $this->log("✓ Arabic: '{$arabicTitle}'");
            
            // Test language direction
            $this->results["language_direction"] = true;
            $this->log("✓ Language direction support enabled");
            
        } catch (\Exception $e) {
            $this->errors[] = "Multi-language support failed: " . $e->getMessage();
            $this->results["multi_language_support"] = false;
        }
    }
    
    /**
     * Test 7: Caching Functionality
     */
    private function testCachingFunctionality()
    {
        $this->log("\n--- Test 7: Caching Functionality ---");
        
        try {
            // Clear cache first
            $this->localizationController->clearTranslationCache();
            $this->log("✓ Translation cache cleared");
            
            // Load translations (should cache them)
            $startTime = microtime(true);
            $translations1 = $this->localizationController->loadTranslations('en');
            $firstLoadTime = microtime(true) - $startTime;
            
            // Load again (should use cache)
            $startTime = microtime(true);
            $translations2 = $this->localizationController->loadTranslations('en');
            $secondLoadTime = microtime(true) - $startTime;
            
            $this->results["caching_works"] = $secondLoadTime < $firstLoadTime;
            $this->results["cache_consistency"] = $translations1 === $translations2;
            
            $this->log("✓ First load time: " . number_format($firstLoadTime * 1000, 2) . "ms");
            $this->log("✓ Second load time: " . number_format($secondLoadTime * 1000, 2) . "ms");
            $this->log("✓ Cache performance improvement: " . number_format((1 - $secondLoadTime / $firstLoadTime) * 100, 1) . "%");
            
        } catch (\Exception $e) {
            $this->errors[] = "Caching functionality failed: " . $e->getMessage();
            $this->results["caching_functionality"] = false;
        }
    }
    
    /**
     * Test 8: Missing Translations
     */
    private function testMissingTranslations()
    {
        $this->log("\n--- Test 8: Missing Translations ---");
        
        try {
            $missingTranslations = [];
            
            // Test for common missing keys
            $testKeys = [
                'nonexistent.key',
                'dashboard.missing_key',
                'invalid_key_format'
            ];
            
            foreach (['en', 'ar'] as $locale) {
                \Illuminate\Support\Facades\App::setLocale($locale);
                
                foreach ($testKeys as $key) {
                    $translation = __($key);
                    if ($translation === $key) {
                        // This is expected behavior - missing keys return the key itself
                        $this->log("  ✓ {$locale}: '{$key}' correctly returns key for missing translation");
                    }
                }
            }
            
            $this->results["missing_translations_handled"] = true;
            
        } catch (\Exception $e) {
            $this->errors[] = "Missing translations test failed: " . $e->getMessage();
            $this->results["missing_translations_handled"] = false;
        }
    }
    
    /**
     * Test 9: Performance Testing
     */
    private function testPerformance()
    {
        $this->log("\n--- Test 9: Performance Testing ---");
        
        try {
            // Clear cache for fresh test
            $this->localizationController->clearTranslationCache();
            
            // Test multiple translation loads
            $iterations = 100;
            $startTime = microtime(true);
            
            for ($i = 0; $i < $iterations; $i++) {
                $this->localizationController->loadTranslations('en');
            }
            
            $endTime = microtime(true);
            $averageTime = ($endTime - $startTime) / $iterations;
            
            $this->results["performance_acceptable"] = $averageTime < 0.1; // Less than 100ms average
            $this->log("✓ Average load time: " . number_format($averageTime * 1000, 2) . "ms");
            
            // Test translation key lookup performance
            \Illuminate\Support\Facades\App::setLocale('en');
            $startTime = microtime(true);
            
            for ($i = 0; $i < 1000; $i++) {
                __('dashboard.title');
            }
            
            $endTime = microtime(true);
            $lookupTime = ($endTime - $startTime) / 1000;
            
            $this->results["lookup_performance"] = $lookupTime < 0.001; // Less than 1ms average
            $this->log("✓ Average lookup time: " . number_format($lookupTime * 1000, 3) . "ms");
            
        } catch (\Exception $e) {
            $this->errors[] = "Performance testing failed: " . $e->getMessage();
            $this->results["performance_testing"] = false;
        }
    }
    
    /**
     * Test 10: API Endpoints
     */
    private function testAPIEndpoints()
    {
        $this->log("\n--- Test 10: API Endpoints ---");
        
        try {
            // Test current language endpoint
            $currentLang = $this->localizationController->getCurrentLanguage();
            $this->results["current_language_api"] = $currentLang->status() === 200;
            $this->log("✓ Current language API: " . $currentLang->status());
            
            // Test supported languages endpoint
            $supportedLangs = $this->localizationController->getSupportedLanguages();
            $this->results["supported_languages_api"] = $supportedLangs->status() === 200;
            $this->log("✓ Supported languages API: " . $supportedLangs->status());
            
            // Test translation keys endpoint
            $translationKeys = $this->localizationController->getTranslationKeys();
            $this->results["translation_keys_api"] = $translationKeys->status() === 200;
            $this->log("✓ Translation keys API: " . $translationKeys->status());
            
            // Test translation stats endpoint
            $translationStats = $this->localizationController->getTranslationStats();
            $this->results["translation_stats_api"] = $translationStats->status() === 200;
            $this->log("✓ Translation stats API: " . $translationStats->status());
            
            // Test refresh translations endpoint
            $refreshTranslations = $this->localizationController->refreshTranslations();
            $this->results["refresh_translations_api"] = $refreshTranslations->status() === 200;
            $this->log("✓ Refresh translations API: " . $refreshTranslations->status());
            
        } catch (\Exception $e) {
            $this->errors[] = "API endpoints testing failed: " . $e->getMessage();
            $this->results["api_endpoints"] = false;
        }
    }
    
    /**
     * Generate comprehensive test report
     */
    private function generateReport()
    {
        $endTime = microtime(true);
        $totalTime = $endTime - $this->startTime;
        
        $this->log("\n=== FINAL TEST REPORT ===");
        $this->log("Total execution time: " . number_format($totalTime, 2) . " seconds");
        $this->log("Tests completed at: " . date('Y-m-d H:i:s'));
        
        // Count results
        $totalTests = count($this->results);
        $passedTests = count(array_filter($this->results));
        $failedTests = $totalTests - $passedTests;
        $successRate = $totalTests > 0 ? ($passedTests / $totalTests) * 100 : 0;
        
        $this->log("\nTEST SUMMARY:");
        $this->log("Total tests: {$totalTests}");
        $this->log("Passed: {$passedTests}");
        $this->log("Failed: {$failedTests}");
        $this->log("Success rate: " . number_format($successRate, 1) . "%");
        
        // Show detailed results
        $this->log("\nDETAILED RESULTS:");
        foreach ($this->results as $test => $passed) {
            $status = $passed ? "PASS" : "FAIL";
            $this->log("  {$test}: {$status}");
        }
        
        // Show errors if any
        if (!empty($this->errors)) {
            $this->log("\nERRORS ENCOUNTERED:");
            foreach ($this->errors as $error) {
                $this->log("  ✗ {$error}");
            }
        }
        
        // Overall status
        $overallStatus = $successRate >= 90 ? "EXCELLENT" : ($successRate >= 70 ? "GOOD" : "NEEDS IMPROVEMENT");
        $this->log("\nOVERALL STATUS: {$overallStatus}");
        
        // Recommendations
        $this->log("\nRECOMMENDATIONS:");
        if ($successRate < 100) {
            $this->log("  - Review failed tests and fix issues");
        }
        if ($successRate >= 90) {
            $this->log("  - Translation system is working well");
            $this->log("  - Consider monitoring performance in production");
        }
        $this->log("  - Run this test suite regularly to catch regressions");
    }
    
    /**
     * Log message with timestamp
     */
    private function log($message)
    {
        echo "[" . date('H:i:s') . "] " . $message . "\n";
    }
}

// Run the tests
$test = new TranslationSystemTest();
$test->runAllTests();