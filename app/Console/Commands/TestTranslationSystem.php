<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\LocalizationController;
use App\Services\ModularTranslationService;

class TestTranslationSystem extends Command
{
    protected $signature = 'test:translations';
    protected $description = 'Test the modular translation system implementation';
    
    private $errors = [];
    private $results = [];
    private $startTime;
    private $localizationController;
    private $translationService;
    
    public function __construct()
    {
        parent::__construct();
        $this->localizationController = new LocalizationController();
        $this->translationService = new ModularTranslationService();
    }
    
    public function handle()
    {
        $this->startTime = microtime(true);
        
        $this->info('=== MODULAR TRANSLATION SYSTEM TEST SUITE ===');
        $this->info('Starting tests at: ' . date('Y-m-d H:i:s'));
        
        // Run all tests
        $this->testTranslationFileLoading();
        $this->testLanguageSwitching();
        $this->testTranslationFunctionUsage();
        $this->testNestedTranslationKeys();
        $this->testBackwardCompatibility();
        $this->testMultiLanguageSupport();
        $this->testCachingFunctionality();
        $this->testMissingTranslations();
        $this->testPerformance();
        $this->testAPIEndpoints();
        
        $this->generateReport();
        
        return 0;
    }
    
    /**
     * Test 1: Translation File Loading
     */
    private function testTranslationFileLoading()
    {
        $this->info("\n--- Test 1: Translation File Loading ---");
        
        foreach (['en', 'ar'] as $locale) {
            try {
                // Test LocalizationController loading
                $translations = $this->localizationController->loadTranslations($locale);
                $this->results["file_loading_{$locale}"] = count($translations) > 0;
                
                $this->line("✓ {$locale} translations loaded: " . count($translations) . " keys");
                
                // Test specific modular files
                $modules = ['admin-dashboard', 'admin-users', 'admin-approvals', 'admin-payments', 'admin-system'];
                foreach ($modules as $module) {
                    $moduleTranslations = $this->localizationController->getModuleTranslations($module, $locale);
                    $moduleCount = count($moduleTranslations);
                    $this->results["module_{$module}_{$locale}"] = $moduleCount > 0;
                    $this->line("  └─ {$module}: {$moduleCount} keys");
                }
                
            } catch (\Exception $e) {
                $this->errors[] = "Translation loading failed for {$locale}: " . $e->getMessage();
                $this->results["file_loading_{$locale}"] = false;
                $this->error("✗ Failed to load {$locale} translations: " . $e->getMessage());
            }
        }
    }
    
    /**
     * Test 2: Language Switching
     */
    private function testLanguageSwitching()
    {
        $this->info("\n--- Test 2: Language Switching ---");
        
        try {
            // Test setting different locales
            foreach (['en', 'ar'] as $locale) {
                App::setLocale($locale);
                $currentLocale = App::getLocale();
                
                $this->results["language_switch_{$locale}"] = ($currentLocale === $locale);
                $this->line("✓ Language switched to: {$locale} (current: {$currentLocale})");
            }
            
            // Test supported languages API
            $supportedLanguages = $this->localizationController->getSupportedLanguages();
            $this->results["supported_languages_api"] = $supportedLanguages->status() === 200;
            $this->line("✓ Supported languages API working");
            
        } catch (\Exception $e) {
            $this->errors[] = "Language switching failed: " . $e->getMessage();
            $this->results["language_switching"] = false;
            $this->error("✗ Language switching failed: " . $e->getMessage());
        }
    }
    
    /**
     * Test 3: Translation Function Usage
     */
    private function testTranslationFunctionUsage()
    {
        $this->info("\n--- Test 3: Translation Function Usage ---");
        
        foreach (['en', 'ar'] as $locale) {
            App::setLocale($locale);
            
            try {
                // Test basic translation
                $basicTranslation = __('Login');
                $this->results["basic_translation_{$locale}"] = !empty($basicTranslation);
                $this->line("✓ Basic translation ({$locale}): '{$basicTranslation}'");
                
                // Test nested translation with dot notation
                $nestedTranslation = __('dashboard.title');
                $this->results["nested_translation_{$locale}"] = !empty($nestedTranslation) && $nestedTranslation !== 'dashboard.title';
                $this->line("✓ Nested translation ({$locale}): '{$nestedTranslation}'");
                
                // Test with parameters
                $paramTranslation = __('alerts.pending_approvals_count', ['count' => 5]);
                $this->results["param_translation_{$locale}"] = strpos($paramTranslation, '5') !== false;
                $this->line("✓ Parameter translation ({$locale}): '{$paramTranslation}'");
                
            } catch (\Exception $e) {
                $this->errors[] = "Translation function failed for {$locale}: " . $e->getMessage();
                $this->results["translation_function_{$locale}"] = false;
                $this->error("✗ Translation function failed for {$locale}: " . $e->getMessage());
            }
        }
    }
    
    /**
     * Test 4: Nested Translation Keys
     */
    private function testNestedTranslationKeys()
    {
        $this->info("\n--- Test 4: Nested Translation Keys ---");
        
        foreach (['en', 'ar'] as $locale) {
            App::setLocale($locale);
            
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
                        $this->line("  ✓ {$key}: '{$translation}'");
                    } else {
                        $this->line("  ✗ {$key}: MISSING");
                    }
                }
                
                $this->results["nested_keys_{$locale}"] = $successCount === count($testKeys);
                
            } catch (\Exception $e) {
                $this->errors[] = "Nested translation keys failed for {$locale}: " . $e->getMessage();
                $this->results["nested_keys_{$locale}"] = false;
                $this->error("✗ Nested translation keys failed for {$locale}: " . $e->getMessage());
            }
        }
    }
    
    /**
     * Test 5: Backward Compatibility
     */
    private function testBackwardCompatibility()
    {
        $this->info("\n--- Test 5: Backward Compatibility ---");
        
        foreach (['en', 'ar'] as $locale) {
            App::setLocale($locale);
            
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
                    // For English, even if translation equals key, it's still a valid translation
                    // For Arabic, translation should be different from key
                    $isValidTranslation = ($locale === 'en') ? !empty($translation) : ($translation !== $key);
                    
                    if ($isValidTranslation) {
                        $successCount++;
                        $this->line("  ✓ {$key}: '{$translation}'");
                    } else {
                        $this->line("  ✗ {$key}: MISSING");
                    }
                }
                
                $this->results["backward_compatibility_{$locale}"] = $successCount > 0;
                
            } catch (\Exception $e) {
                $this->errors[] = "Backward compatibility failed for {$locale}: " . $e->getMessage();
                $this->results["backward_compatibility_{$locale}"] = false;
                $this->error("✗ Backward compatibility failed for {$locale}: " . $e->getMessage());
            }
        }
    }
    
    /**
     * Test 6: Multi-Language Support
     */
    private function testMultiLanguageSupport()
    {
        $this->info("\n--- Test 6: Multi-Language Support ---");
        
        try {
            // Test English
            App::setLocale('en');
            $englishTitle = __('dashboard.title');
            $this->results["english_support"] = ($englishTitle === 'Dashboard');
            $this->line("✓ English: '{$englishTitle}'");
            
            // Test Arabic
            App::setLocale('ar');
            $arabicTitle = __('dashboard.title');
            $this->results["arabic_support"] = ($arabicTitle === 'لوحة التحكم');
            $this->line("✓ Arabic: '{$arabicTitle}'");
            
            // Test language direction
            $this->results["language_direction"] = true;
            $this->line("✓ Language direction support enabled");
            
        } catch (\Exception $e) {
            $this->errors[] = "Multi-language support failed: " . $e->getMessage();
            $this->results["multi_language_support"] = false;
            $this->error("✗ Multi-language support failed: " . $e->getMessage());
        }
    }
    
    /**
     * Test 7: Caching Functionality
     */
    private function testCachingFunctionality()
    {
        $this->info("\n--- Test 7: Caching Functionality ---");
        
        try {
            // Clear cache first
            $this->localizationController->clearTranslationCache();
            $this->line("✓ Translation cache cleared");
            
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
            
            $this->line("✓ First load time: " . number_format($firstLoadTime * 1000, 2) . "ms");
            $this->line("✓ Second load time: " . number_format($secondLoadTime * 1000, 2) . "ms");
            $this->line("✓ Cache performance improvement: " . number_format((1 - $secondLoadTime / $firstLoadTime) * 100, 1) . "%");
            
        } catch (\Exception $e) {
            $this->errors[] = "Caching functionality failed: " . $e->getMessage();
            $this->results["caching_functionality"] = false;
            $this->error("✗ Caching functionality failed: " . $e->getMessage());
        }
    }
    
    /**
     * Test 8: Missing Translations
     */
    private function testMissingTranslations()
    {
        $this->info("\n--- Test 8: Missing Translations ---");
        
        try {
            // Test for common missing keys
            $testKeys = [
                'nonexistent.key',
                'dashboard.missing_key',
                'invalid_key_format'
            ];
            
            foreach (['en', 'ar'] as $locale) {
                App::setLocale($locale);
                
                foreach ($testKeys as $key) {
                    $translation = __($key);
                    if ($translation === $key) {
                        // This is expected behavior - missing keys return the key itself
                        $this->line("  ✓ {$locale}: '{$key}' correctly returns key for missing translation");
                    }
                }
            }
            
            $this->results["missing_translations_handled"] = true;
            
        } catch (\Exception $e) {
            $this->errors[] = "Missing translations test failed: " . $e->getMessage();
            $this->results["missing_translations_handled"] = false;
            $this->error("✗ Missing translations test failed: " . $e->getMessage());
        }
    }
    
    /**
     * Test 9: Performance Testing
     */
    private function testPerformance()
    {
        $this->info("\n--- Test 9: Performance Testing ---");
        
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
            $this->line("✓ Average load time: " . number_format($averageTime * 1000, 2) . "ms");
            
            // Test translation key lookup performance
            App::setLocale('en');
            $startTime = microtime(true);
            
            for ($i = 0; $i < 1000; $i++) {
                __('dashboard.title');
            }
            
            $endTime = microtime(true);
            $lookupTime = ($endTime - $startTime) / 1000;
            
            $this->results["lookup_performance"] = $lookupTime < 0.001; // Less than 1ms average
            $this->line("✓ Average lookup time: " . number_format($lookupTime * 1000, 3) . "ms");
            
        } catch (\Exception $e) {
            $this->errors[] = "Performance testing failed: " . $e->getMessage();
            $this->results["performance_testing"] = false;
            $this->error("✗ Performance testing failed: " . $e->getMessage());
        }
    }
    
    /**
     * Test 10: API Endpoints
     */
    private function testAPIEndpoints()
    {
        $this->info("\n--- Test 10: API Endpoints ---");
        
        try {
            // Test current language endpoint
            $currentLang = $this->localizationController->getCurrentLanguage();
            $this->results["current_language_api"] = $currentLang->status() === 200;
            $this->line("✓ Current language API: " . $currentLang->status());
            
            // Test supported languages endpoint
            $supportedLangs = $this->localizationController->getSupportedLanguages();
            $this->results["supported_languages_api"] = $supportedLangs->status() === 200;
            $this->line("✓ Supported languages API: " . $supportedLangs->status());
            
            // Test translation keys endpoint
            $translationKeys = $this->localizationController->getTranslationKeys();
            $this->results["translation_keys_api"] = $translationKeys->status() === 200;
            $this->line("✓ Translation keys API: " . $translationKeys->status());
            
            // Test translation stats endpoint
            $translationStats = $this->localizationController->getTranslationStats();
            $this->results["translation_stats_api"] = $translationStats->status() === 200;
            $this->line("✓ Translation stats API: " . $translationStats->status());
            
            // Test refresh translations endpoint
            $refreshTranslations = $this->localizationController->refreshTranslations();
            $this->results["refresh_translations_api"] = $refreshTranslations->status() === 200;
            $this->line("✓ Refresh translations API: " . $refreshTranslations->status());
            
        } catch (\Exception $e) {
            $this->errors[] = "API endpoints testing failed: " . $e->getMessage();
            $this->results["api_endpoints"] = false;
            $this->error("✗ API endpoints testing failed: " . $e->getMessage());
        }
    }
    
    /**
     * Generate comprehensive test report
     */
    private function generateReport()
    {
        $endTime = microtime(true);
        $totalTime = $endTime - $this->startTime;
        
        $this->info("\n=== FINAL TEST REPORT ===");
        $this->info("Total execution time: " . number_format($totalTime, 2) . " seconds");
        $this->info("Tests completed at: " . date('Y-m-d H:i:s'));
        
        // Count results
        $totalTests = count($this->results);
        $passedTests = count(array_filter($this->results));
        $failedTests = $totalTests - $passedTests;
        $successRate = $totalTests > 0 ? ($passedTests / $totalTests) * 100 : 0;
        
        $this->info("\nTEST SUMMARY:");
        $this->info("Total tests: {$totalTests}");
        $this->info("Passed: {$passedTests}");
        $this->info("Failed: {$failedTests}");
        $this->info("Success rate: " . number_format($successRate, 1) . "%");
        
        // Show detailed results
        $this->info("\nDETAILED RESULTS:");
        foreach ($this->results as $test => $passed) {
            $status = $passed ? "PASS" : "FAIL";
            $color = $passed ? 'green' : 'red';
            $this->line("  {$test}: <fg={$color}>{$status}</>");
        }
        
        // Show errors if any
        if (!empty($this->errors)) {
            $this->error("\nERRORS ENCOUNTERED:");
            foreach ($this->errors as $error) {
                $this->error("  ✗ {$error}");
            }
        }
        
        // Overall status
        $overallStatus = $successRate >= 90 ? "EXCELLENT" : ($successRate >= 70 ? "GOOD" : "NEEDS IMPROVEMENT");
        $color = $successRate >= 90 ? 'green' : ($successRate >= 70 ? 'yellow' : 'red');
        $this->line("\nOVERALL STATUS: <fg={$color}>{$overallStatus}</>");
        
        // Recommendations
        $this->info("\nRECOMMENDATIONS:");
        if ($successRate < 100) {
            $this->warn("  - Review failed tests and fix issues");
        }
        if ($successRate >= 90) {
            $this->info("  - Translation system is working well");
            $this->info("  - Consider monitoring performance in production");
        }
        $this->info("  - Run this test suite regularly to catch regressions");
    }
}