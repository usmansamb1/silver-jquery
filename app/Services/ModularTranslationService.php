<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ModularTranslationService
{
    /**
     * The default cache time for translations (in seconds)
     */
    protected $cacheTime = 3600; // 1 hour

    /**
     * The supported locales
     */
    protected $supportedLocales = ['en', 'ar'];

    /**
     * Load and merge all translation files for a given locale
     *
     * @param string $locale
     * @return array
     */
    public function loadTranslations(string $locale): array
    {
        if (!in_array($locale, $this->supportedLocales)) {
            return [];
        }

        $cacheKey = "translations.{$locale}";
        
        // Check if translations are cached
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $translations = [];
        $langPath = base_path("lang/{$locale}");
        
        // Load the main single JSON file if it exists (for backward compatibility)
        $mainJsonFile = base_path("lang/{$locale}.json");
        if (File::exists($mainJsonFile)) {
            $mainTranslations = json_decode(File::get($mainJsonFile), true);
            if (is_array($mainTranslations)) {
                $translations = array_merge($translations, $mainTranslations);
            }
        }

        // Load modular JSON files
        $modularJsonFiles = $this->getModularTranslationFiles($locale);
        foreach ($modularJsonFiles as $file) {
            $fileTranslations = json_decode(File::get($file), true);
            if (is_array($fileTranslations)) {
                $translations = array_merge($translations, $fileTranslations);
            }
        }

        // Load PHP translation files from lang/{locale}/ directory
        if (File::isDirectory($langPath)) {
            $phpFiles = File::files($langPath);
            foreach ($phpFiles as $file) {
                if ($file->getExtension() === 'php') {
                    $fileName = $file->getBasename('.php');
                    $fileTranslations = include $file->getPathname();
                    if (is_array($fileTranslations)) {
                        // Prefix with filename for namespacing (e.g., auth.login)
                        foreach ($fileTranslations as $key => $value) {
                            $translations["{$fileName}.{$key}"] = $value;
                        }
                    }
                }
            }
        }

        // Cache the merged translations
        Cache::put($cacheKey, $translations, $this->cacheTime);

        return $translations;
    }

    /**
     * Get all modular translation JSON files for a locale
     *
     * @param string $locale
     * @return array
     */
    protected function getModularTranslationFiles(string $locale): array
    {
        $modularPath = base_path("lang/{$locale}");
        $files = [];

        if (!File::isDirectory($modularPath)) {
            return $files;
        }

        // Get all JSON files in the locale directory
        $jsonFiles = File::glob("{$modularPath}/*.json");
        
        return $jsonFiles;
    }

    /**
     * Clear translation cache for a specific locale or all locales
     *
     * @param string|null $locale
     */
    public function clearCache(string $locale = null): void
    {
        if ($locale) {
            Cache::forget("translations.{$locale}");
        } else {
            foreach ($this->supportedLocales as $supportedLocale) {
                Cache::forget("translations.{$supportedLocale}");
            }
        }
    }

    /**
     * Get a specific translation key
     *
     * @param string $key
     * @param string $locale
     * @param array $replace
     * @return string
     */
    public function get(string $key, string $locale, array $replace = []): string
    {
        $translations = $this->loadTranslations($locale);
        
        $value = $translations[$key] ?? $key;
        
        // Handle replacement parameters
        if (!empty($replace)) {
            foreach ($replace as $search => $replacement) {
                $value = str_replace(":{$search}", $replacement, $value);
            }
        }
        
        return $value;
    }

    /**
     * Check if a translation key exists
     *
     * @param string $key
     * @param string $locale
     * @return bool
     */
    public function has(string $key, string $locale): bool
    {
        $translations = $this->loadTranslations($locale);
        return array_key_exists($key, $translations);
    }

    /**
     * Get all translations for a locale
     *
     * @param string $locale
     * @return array
     */
    public function all(string $locale): array
    {
        return $this->loadTranslations($locale);
    }

    /**
     * Add translations to runtime (useful for dynamic translations)
     *
     * @param array $translations
     * @param string $locale
     */
    public function addTranslations(array $translations, string $locale): void
    {
        $cacheKey = "translations.{$locale}";
        $existingTranslations = Cache::get($cacheKey, []);
        $mergedTranslations = array_merge($existingTranslations, $translations);
        Cache::put($cacheKey, $mergedTranslations, $this->cacheTime);
    }

    /**
     * Get supported locales
     *
     * @return array
     */
    public function getSupportedLocales(): array
    {
        return $this->supportedLocales;
    }

    /**
     * Set supported locales
     *
     * @param array $locales
     */
    public function setSupportedLocales(array $locales): void
    {
        $this->supportedLocales = $locales;
    }
}