<?php

namespace App\Helpers;

class StatusHelper
{
    /**
     * Get status color based on status code
     *
     * @param string $statusCode
     * @return string
     */
    public static function getStatusColor(string $statusCode): string
    {
        return match(strtolower($statusCode)) {
            'pending' => '#FFA500',    // Orange
            'approved', 'completed' => '#28a745',  // Green
            'rejected' => '#dc3545',   // Red
            'cancelled' => '#6c757d',  // Gray
            'in_progress' => '#17a2b8', // Blue
            default => '#6c757d'       // Default gray
        };
    }

    /**
     * Get Bootstrap badge class based on status code
     *
     * @param string $statusCode
     * @return string
     */
    public static function getStatusBadgeClass(string $statusCode): string
    {
        return match(strtolower($statusCode)) {
            'pending' => 'bg-warning',
            'approved', 'completed' => 'bg-success',
            'rejected' => 'bg-danger',
            'in_progress' => 'bg-info',
            'cancelled' => 'bg-secondary',
            default => 'bg-secondary'
        };
    }

    /**
     * Get appropriate icon for status
     *
     * @param string $statusCode
     * @return string
     */
    public static function getStatusIcon(string $statusCode): string
    {
        return match(strtolower($statusCode)) {
            'pending' => 'clock',
            'approved', 'completed' => 'check-circle',
            'rejected' => 'times-circle',
            'in_progress' => 'spinner',
            'cancelled' => 'ban',
            default => 'circle'
        };
    }

    /**
     * Get display name for status code
     *
     * @param string $statusCode
     * @return string
     */
    public static function getStatusDisplayName(string $statusCode): string
    {
        // First convert snake_case to space-separated
        $name = str_replace('_', ' ', $statusCode);
        
        // Then capitalize each word
        return ucwords($name);
    }
} 