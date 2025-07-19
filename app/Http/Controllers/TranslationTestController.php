<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\LocalizationController;

class TranslationTestController extends Controller
{
    private $localizationController;
    
    public function __construct()
    {
        $this->localizationController = new LocalizationController();
    }
    
    /**
     * Show the translation test interface
     */
    public function index()
    {
        return view('translation-test');
    }
    
    /**
     * Test translation system and return results
     */
    public function test(Request $request)
    {
        $locale = $request->input('locale', 'en');
        App::setLocale($locale);
        
        $results = [];
        
        // Test 1: Basic translations
        $results['basic_translations'] = $this->testBasicTranslations();
        
        // Test 2: Nested translations
        $results['nested_translations'] = $this->testNestedTranslations();
        
        // Test 3: Module-specific translations
        $results['module_translations'] = $this->testModuleTranslations();
        
        // Test 4: Parameter translations
        $results['parameter_translations'] = $this->testParameterTranslations();
        
        // Test 5: Missing translations
        $results['missing_translations'] = $this->testMissingTranslations();
        
        // Test 6: Performance
        $results['performance'] = $this->testPerformance();
        
        // Test 7: Translation statistics
        $results['statistics'] = $this->getTranslationStatistics();
        
        return response()->json([
            'locale' => $locale,
            'results' => $results,
            'overall_status' => $this->calculateOverallStatus($results)
        ]);
    }
    
    /**
     * Test basic translations
     */
    private function testBasicTranslations()
    {
        $tests = [
            'Login' => __('Login'),
            'Email' => __('Email'),
            'Password' => __('Password'),
            'Mobile Number' => __('Mobile Number'),
            'Company Name' => __('Company Name')
        ];
        
        $results = [];
        foreach ($tests as $key => $translation) {
            $results[$key] = [
                'key' => $key,
                'translation' => $translation,
                'status' => $translation !== $key ? 'success' : 'missing'
            ];
        }
        
        return $results;
    }
    
    /**
     * Test nested translations
     */
    private function testNestedTranslations()
    {
        $tests = [
            'dashboard.title' => __('dashboard.title'),
            'navigation.dashboard' => __('navigation.dashboard'),
            'statistics.total_users' => __('statistics.total_users'),
            'quick_actions.add_user' => __('quick_actions.add_user'),
            'tables.actions' => __('tables.actions'),
            'status.pending' => __('status.pending'),
            'alerts.low_wallet_balance' => __('alerts.low_wallet_balance')
        ];
        
        $results = [];
        foreach ($tests as $key => $translation) {
            $results[$key] = [
                'key' => $key,
                'translation' => $translation,
                'status' => $translation !== $key ? 'success' : 'missing'
            ];
        }
        
        return $results;
    }
    
    /**
     * Test module-specific translations
     */
    private function testModuleTranslations()
    {
        $modules = ['admin-dashboard', 'admin-users', 'admin-approvals', 'admin-payments', 'admin-system'];
        $results = [];
        
        foreach ($modules as $module) {
            $moduleTranslations = $this->localizationController->getModuleTranslations($module);
            $results[$module] = [
                'module' => $module,
                'count' => count($moduleTranslations),
                'status' => count($moduleTranslations) > 0 ? 'success' : 'missing',
                'sample_keys' => array_slice(array_keys($moduleTranslations), 0, 5)
            ];
        }
        
        return $results;
    }
    
    /**
     * Test parameter translations
     */
    private function testParameterTranslations()
    {
        $tests = [
            'alerts.pending_approvals_count' => [
                'parameters' => ['count' => 5],
                'expected_contains' => '5'
            ],
            'pagination.showing' => [
                'parameters' => ['from' => 1, 'to' => 10, 'total' => 100],
                'expected_contains' => '1'
            ]
        ];
        
        $results = [];
        foreach ($tests as $key => $config) {
            $translation = __($key, $config['parameters']);
            $results[$key] = [
                'key' => $key,
                'translation' => $translation,
                'parameters' => $config['parameters'],
                'status' => strpos($translation, $config['expected_contains']) !== false ? 'success' : 'failed'
            ];
        }
        
        return $results;
    }
    
    /**
     * Test missing translations
     */
    private function testMissingTranslations()
    {
        $tests = [
            'nonexistent.key',
            'dashboard.missing_key',
            'invalid_key_format'
        ];
        
        $results = [];
        foreach ($tests as $key) {
            $translation = __($key);
            $results[$key] = [
                'key' => $key,
                'translation' => $translation,
                'status' => $translation === $key ? 'correctly_missing' : 'unexpected_translation'
            ];
        }
        
        return $results;
    }
    
    /**
     * Test performance
     */
    private function testPerformance()
    {
        // Test loading time
        $startTime = microtime(true);
        $this->localizationController->loadTranslations(App::getLocale());
        $loadTime = microtime(true) - $startTime;
        
        // Test lookup time
        $startTime = microtime(true);
        for ($i = 0; $i < 100; $i++) {
            __('dashboard.title');
        }
        $lookupTime = (microtime(true) - $startTime) / 100;
        
        return [
            'load_time_ms' => number_format($loadTime * 1000, 2),
            'lookup_time_ms' => number_format($lookupTime * 1000, 3),
            'load_status' => $loadTime < 0.1 ? 'excellent' : ($loadTime < 0.5 ? 'good' : 'slow'),
            'lookup_status' => $lookupTime < 0.001 ? 'excellent' : ($lookupTime < 0.01 ? 'good' : 'slow')
        ];
    }
    
    /**
     * Get translation statistics
     */
    private function getTranslationStatistics()
    {
        $stats = $this->localizationController->getTranslationStats();
        return $stats->getData();
    }
    
    /**
     * Calculate overall status
     */
    private function calculateOverallStatus($results)
    {
        $totalTests = 0;
        $passedTests = 0;
        
        // Count basic translations
        foreach ($results['basic_translations'] as $test) {
            $totalTests++;
            if ($test['status'] === 'success') $passedTests++;
        }
        
        // Count nested translations
        foreach ($results['nested_translations'] as $test) {
            $totalTests++;
            if ($test['status'] === 'success') $passedTests++;
        }
        
        // Count module translations
        foreach ($results['module_translations'] as $test) {
            $totalTests++;
            if ($test['status'] === 'success') $passedTests++;
        }
        
        // Count parameter translations
        foreach ($results['parameter_translations'] as $test) {
            $totalTests++;
            if ($test['status'] === 'success') $passedTests++;
        }
        
        $successRate = $totalTests > 0 ? ($passedTests / $totalTests) * 100 : 0;
        
        return [
            'total_tests' => $totalTests,
            'passed_tests' => $passedTests,
            'failed_tests' => $totalTests - $passedTests,
            'success_rate' => number_format($successRate, 1),
            'status' => $successRate >= 90 ? 'excellent' : ($successRate >= 70 ? 'good' : 'needs_improvement')
        ];
    }
    
    /**
     * Get all translation keys for a locale
     */
    public function getTranslationKeys(Request $request)
    {
        $locale = $request->input('locale', 'en');
        $result = $this->localizationController->getTranslationKeys($locale);
        
        return $result;
    }
    
    /**
     * Clear translation cache
     */
    public function clearCache()
    {
        return $this->localizationController->clearTranslationCache();
    }
    
    /**
     * Refresh translations
     */
    public function refreshTranslations()
    {
        return $this->localizationController->refreshTranslations();
    }
}