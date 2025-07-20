<?php

namespace App\Translation;

use Illuminate\Translation\FileLoader;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ModularFileLoader extends FileLoader
{
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
     * Load the messages for the given locale.
     */
    public function load($locale, $group, $namespace = null)
    {
        if ($group === '*' && $namespace === '*') {
            return $this->loadJsonTranslations($locale);
        }

        return parent::load($locale, $group, $namespace);
    }

    /**
     * Load a namespaced translation group.
     */
    public function loadNamespaced($locale, $group, $namespace)
    {
        if ($namespace === '*' && $group === '*') {
            return $this->loadJsonTranslations($locale);
        }

        return parent::loadNamespaced($locale, $group, $namespace);
    }

    /**
     * Load JSON translations from multiple files
     */
    protected function loadJsonTranslations($locale)
    {
        $cacheKey = "modular_translations_{$locale}";
        
        return Cache::remember($cacheKey, $this->cacheMinutes, function () use ($locale) {
            $translations = [];

            // Load main translation file for backward compatibility
            $mainFile = $this->paths[0] . "/{$locale}.json";
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
                $moduleFile = $this->paths[0] . "/{$module}.{$locale}.json";
                if (File::exists($moduleFile)) {
                    try {
                        $moduleTranslations = json_decode(File::get($moduleFile), true);
                        if (is_array($moduleTranslations)) {
                            // Flatten nested translations to dot notation with module prefix
                            $flattenedTranslations = $this->flattenTranslations($moduleTranslations, $module);
                            $translations = array_merge($translations, $flattenedTranslations);
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
     * Flatten nested translation arrays to dot notation
     */
    protected function flattenTranslations($translations, $prefix = '')
    {
        $flattened = [];
        
        foreach ($translations as $key => $value) {
            $fullKey = $prefix ? "{$prefix}.{$key}" : $key;
            
            if (is_array($value)) {
                $flattened = array_merge($flattened, $this->flattenTranslations($value, $fullKey));
            } else {
                $flattened[$fullKey] = $value;
            }
        }
        
        return $flattened;
    }
}