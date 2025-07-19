<?php

namespace App\Utils;

class ArabicTextUtils
{
    /**
     * Clean and sanitize Arabic text for database storage
     * 
     * @param string|null $text The text to process
     * @return string Cleaned text
     */
    public static function sanitize($text): string
    {
        if (empty($text)) {
            return '';
        }
        
        // Force UTF-8 encoding
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        
        // Remove control characters
        $text = preg_replace('/[\x00-\x1F\x7F]/u', '', $text);
        
        // Remove HTML entities that might cause issues
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        
        // Limit length to prevent overflow
        if (mb_strlen($text, 'UTF-8') > 200) {
            $text = mb_substr($text, 0, 200, 'UTF-8');
        }
        
        return trim($text);
    }
    
    /**
     * Check if text contains Arabic characters
     * 
     * @param string $text Text to check
     * @return bool True if contains Arabic
     */
    public static function containsArabic($text): bool
    {
        return preg_match('/[\x{0600}-\x{06FF}\x{0750}-\x{077F}]/u', $text) === 1;
    }
    
    /**
     * Transliterate Arabic text to Latin alphabet
     * 
     * @param string $text Text to transliterate
     * @return string Transliterated text
     */
    public static function transliterate($text): string
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
        
        return $transliterated;
    }
    
    /**
     * Attempt to fix question marks in text by replacing with transliterated version
     * 
     * @param string $text Text that may contain question marks from failed encoding
     * @return string Fixed text
     */
    public static function fixQuestionMarks($text): string
    {
        // Check if text has question marks (failed encoding)
        if (preg_match('/\?{2,}/', $text)) {
            // Attempt to recover using transliteration
            $transliterated = self::transliterate($text);
            
            // Replace sequences of ? with empty space
            $text = preg_replace('/\?+/', ' ', $text);
            
            // If result is empty or still has many question marks, use transliterated
            if (empty(trim($text)) || substr_count($text, '?') > 5) {
                return $transliterated;
            }
        }
        
        return $text;
    }
} 