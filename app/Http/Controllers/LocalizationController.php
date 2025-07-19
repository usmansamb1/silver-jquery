<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class LocalizationController extends Controller
{
    /**
     * Supported languages
     */
    protected $supportedLanguages = ['en', 'ar'];

    /**
     * Modular translation files
     */
    protected $modularFiles = [
        'admin-dashboard',
        'admin-users', 
        'admin-approvals',
        'admin-payments',
        'admin-system'
    ];

    /**
     * Cache duration in minutes
     */
    protected $cacheMinutes = 60;

    /**
     * Change the language
     */
    public function changeLanguage(Request $request, $locale)
    {
        // Validate the locale
        if (!in_array($locale, $this->supportedLanguages)) {
            abort(404);
        }

        // Set the locale
        App::setLocale($locale);
        
        // Store in session
        Session::put('locale', $locale);
        
        // Store in cookie for persistence (expires in 1 year)
        Cookie::queue('locale', $locale, 60 * 24 * 365);
        
        // If user is authenticated, store in database
        if (auth()->check()) {
            $user = auth()->user();
            $user->update(['locale' => $locale]);
        }

        // Clear translation cache for immediate effect
        $this->clearTranslationCache();

        // Return back to previous page
        return redirect()->back()->with('success', __('Language changed successfully'));
    }

    /**
     * Get current language
     */
    public function getCurrentLanguage()
    {
        return response()->json([
            'current_locale' => App::getLocale(),
            'supported_languages' => $this->supportedLanguages
        ]);
    }

    /**
     * Get all available languages
     */
    public function getSupportedLanguages()
    {
        return response()->json([
            'languages' => [
                'en' => [
                    'name' => 'English',
                    'native' => 'English',
                    'direction' => 'ltr'
                ],
                'ar' => [
                    'name' => 'Arabic',
                    'native' => 'العربية',
                    'direction' => 'rtl'
                ]
            ]
        ]);
    }

    /**
     * Load and merge all translation files for a given locale
     */
    public function loadTranslations($locale)
    {
        if (!in_array($locale, $this->supportedLanguages)) {
            return [];
        }

        $cacheKey = "translations_{$locale}";
        
        return Cache::remember($cacheKey, $this->cacheMinutes, function () use ($locale) {
            $translations = [];

            // Load main translation file for backward compatibility
            $mainFile = base_path("lang/{$locale}.json");
            if (File::exists($mainFile)) {
                try {
                    $mainTranslations = json_decode(File::get($mainFile), true);
                    if (is_array($mainTranslations)) {
                        $translations = array_merge($translations, $mainTranslations);
                    }
                } catch (\Exception $e) {
                    Log::warning("Failed to load main translation file {$mainFile}: " . $e->getMessage());
                }
            }

            // Load modular translation files
            foreach ($this->modularFiles as $module) {
                $moduleFile = base_path("lang/{$module}.{$locale}.json");
                if (File::exists($moduleFile)) {
                    try {
                        $moduleTranslations = json_decode(File::get($moduleFile), true);
                        if (is_array($moduleTranslations)) {
                            $translations = $this->mergeNestedTranslations($translations, $moduleTranslations);
                        }
                    } catch (\Exception $e) {
                        Log::warning("Failed to load module translation file {$moduleFile}: " . $e->getMessage());
                    }
                }
            }

            return $translations;
        });
    }

    /**
     * Merge nested translation arrays
     */
    protected function mergeNestedTranslations($existing, $new)
    {
        foreach ($new as $key => $value) {
            if (is_array($value) && isset($existing[$key]) && is_array($existing[$key])) {
                $existing[$key] = $this->mergeNestedTranslations($existing[$key], $value);
            } else {
                $existing[$key] = $value;
            }
        }
        return $existing;
    }

    /**
     * Get translations for a specific module
     */
    public function getModuleTranslations($module, $locale = null)
    {
        $locale = $locale ?: App::getLocale();
        
        if (!in_array($locale, $this->supportedLanguages)) {
            return [];
        }

        if (!in_array($module, $this->modularFiles)) {
            return [];
        }

        $cacheKey = "module_translations_{$module}_{$locale}";
        
        return Cache::remember($cacheKey, $this->cacheMinutes, function () use ($module, $locale) {
            $moduleFile = base_path("lang/{$module}.{$locale}.json");
            if (File::exists($moduleFile)) {
                try {
                    $translations = json_decode(File::get($moduleFile), true);
                    return is_array($translations) ? $translations : [];
                } catch (\Exception $e) {
                    Log::warning("Failed to load module translation file {$moduleFile}: " . $e->getMessage());
                    return [];
                }
            }
            return [];
        });
    }

    /**
     * Clear translation cache
     */
    public function clearTranslationCache()
    {
        foreach ($this->supportedLanguages as $locale) {
            Cache::forget("translations_{$locale}");
            
            foreach ($this->modularFiles as $module) {
                Cache::forget("module_translations_{$module}_{$locale}");
            }
        }
        
        return response()->json(['message' => 'Translation cache cleared successfully']);
    }

    /**
     * Get all available translation keys for a locale
     */
    public function getTranslationKeys($locale = null)
    {
        $locale = $locale ?: App::getLocale();
        $translations = $this->loadTranslations($locale);
        
        return response()->json([
            'locale' => $locale,
            'keys' => $this->flattenTranslationKeys($translations)
        ]);
    }

    /**
     * Flatten nested translation keys
     */
    protected function flattenTranslationKeys($translations, $prefix = '')
    {
        $keys = [];
        foreach ($translations as $key => $value) {
            $fullKey = $prefix ? "{$prefix}.{$key}" : $key;
            if (is_array($value)) {
                $keys = array_merge($keys, $this->flattenTranslationKeys($value, $fullKey));
            } else {
                $keys[] = $fullKey;
            }
        }
        return $keys;
    }

    /**
     * Refresh translations and clear cache
     */
    public function refreshTranslations()
    {
        $this->clearTranslationCache();
        
        // Preload translations for all supported languages
        foreach ($this->supportedLanguages as $locale) {
            $this->loadTranslations($locale);
        }
        
        return response()->json(['message' => 'Translations refreshed successfully']);
    }

    /**
     * Get translation statistics
     */
    public function getTranslationStats()
    {
        $stats = [];
        
        foreach ($this->supportedLanguages as $locale) {
            $translations = $this->loadTranslations($locale);
            $flatKeys = $this->flattenTranslationKeys($translations);
            
            $stats[$locale] = [
                'total_keys' => count($flatKeys),
                'nested_keys' => count($translations),
                'modules_loaded' => count($this->modularFiles),
                'cache_key' => "translations_{$locale}"
            ];
        }
        
        return response()->json($stats);
    }
}