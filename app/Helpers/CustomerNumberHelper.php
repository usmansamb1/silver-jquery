<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CustomerNumberHelper
{
    /**
     * Validate if a customer number follows the correct format
     * 
     * @param int|string $customerNo
     * @return bool
     */
    public static function isValidFormat($customerNo): bool
    {
        // Convert to string for validation
        $customerStr = (string)$customerNo;
        
        // Must be exactly 12 digits
        if (!preg_match('/^\d{12}$/', $customerStr)) {
            return false;
        }
        
        // Check if it follows YYMMDD format (first 6 digits)
        $dateComponent = substr($customerStr, 0, 6);
        $year = '20' . substr($dateComponent, 0, 2);
        $month = substr($dateComponent, 2, 2);
        $day = substr($dateComponent, 4, 2);
        
        // Validate date
        if (!checkdate((int)$month, (int)$day, (int)$year)) {
            // Not a valid date, could be fallback format - still valid
            return true;
        }
        
        // Check if sequence part is in valid range (100001-999999)
        $sequenceComponent = substr($customerStr, 6, 6);
        $sequence = (int)$sequenceComponent;
        
        return $sequence >= 100001 && $sequence <= 999999;
    }

    /**
     * Extract date from customer number if it follows date format
     * 
     * @param int|string $customerNo
     * @return Carbon|null
     */
    public static function extractDate($customerNo): ?Carbon
    {
        $customerStr = str_pad((string)$customerNo, 12, '0', STR_PAD_LEFT);
        
        if (strlen($customerStr) !== 12) {
            return null;
        }
        
        $dateComponent = substr($customerStr, 0, 6);
        $year = '20' . substr($dateComponent, 0, 2);
        $month = substr($dateComponent, 2, 2);
        $day = substr($dateComponent, 4, 2);
        
        try {
            return Carbon::createFromDate((int)$year, (int)$month, (int)$day);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get statistics about customer number generation
     * 
     * @return array
     */
    public static function getStatistics(): array
    {
        $today = now()->format('ymd');
        $todayPrefix = (int)$today;
        $todayMin = $todayPrefix * 1000000 + 100001;
        $todayMax = $todayPrefix * 1000000 + 999999;
        
        // Count registrations today
        $todayCount = DB::table('users')
            ->whereBetween('customer_no', [$todayMin, $todayMax])
            ->count();
        
        // Get highest customer number today
        $maxToday = DB::table('users')
            ->whereBetween('customer_no', [$todayMin, $todayMax])
            ->max('customer_no');
        
        // Calculate remaining capacity for today
        $remainingToday = 899999 - $todayCount; // 999999 - 100001 + 1 - used
        
        // Total customers
        $totalCustomers = DB::table('users')
            ->whereNotNull('customer_no')
            ->count();
        
        return [
            'today_registrations' => $todayCount,
            'remaining_today' => $remainingToday,
            'max_today' => $maxToday,
            'total_customers' => $totalCustomers,
            'today_capacity_used_percent' => round(($todayCount / 899999) * 100, 2),
            'date_prefix' => $today,
        ];
    }

    /**
     * Check if customer number exists
     * 
     * @param int $customerNo
     * @return bool
     */
    public static function exists(int $customerNo): bool
    {
        return DB::table('users')
            ->where('customer_no', $customerNo)
            ->exists();
    }

    /**
     * Format customer number for display
     * 
     * @param int|string|null $customerNo
     * @return string|null
     */
    public static function format($customerNo): ?string
    {
        if (empty($customerNo)) {
            return null;
        }
        
        return str_pad((string)$customerNo, 12, '0', STR_PAD_LEFT);
    }

    /**
     * Parse customer number components
     * 
     * @param int|string $customerNo
     * @return array
     */
    public static function parseComponents($customerNo): array
    {
        $customerStr = str_pad((string)$customerNo, 12, '0', STR_PAD_LEFT);
        
        if (strlen($customerStr) !== 12) {
            return [
                'valid' => false,
                'format' => 'invalid',
            ];
        }
        
        $dateComponent = substr($customerStr, 0, 6);
        $sequenceComponent = substr($customerStr, 6, 6);
        
        $year = '20' . substr($dateComponent, 0, 2);
        $month = substr($dateComponent, 2, 2);
        $day = substr($dateComponent, 4, 2);
        
        $isValidDate = checkdate((int)$month, (int)$day, (int)$year);
        $sequence = (int)$sequenceComponent;
        
        if ($isValidDate && $sequence >= 100001 && $sequence <= 999999) {
            return [
                'valid' => true,
                'format' => 'date_based',
                'date_component' => $dateComponent,
                'sequence_component' => $sequenceComponent,
                'year' => $year,
                'month' => $month,
                'day' => $day,
                'sequence' => $sequence,
                'date' => Carbon::createFromDate((int)$year, (int)$month, (int)$day),
            ];
        } else {
            return [
                'valid' => true,
                'format' => 'fallback',
                'timestamp_component' => substr($customerStr, 0, 8),
                'random_component' => substr($customerStr, 8, 4),
            ];
        }
    }
} 