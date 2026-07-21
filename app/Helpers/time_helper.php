// C:\xampp\htdocs\cdwnet\app\Helpers\time_helper.php
<?php

if (!function_exists('normalize_time')) {
    /**
     * Normalize time format from various inputs to HH:MM:SS
     */
    function normalize_time($time)
    {
        if (empty($time)) {
            return null;
        }
        
        // Jika sudah format HH:MM:SS
        if (preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $time)) {
            return $time;
        }
        
        // Jika format HH:MM
        if (preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $time)) {
            return $time . ':00';
        }
        
        // Jika format HH.MM
        if (preg_match('/^([01]?[0-9]|2[0-3])\.[0-5][0-9]$/', $time)) {
            return str_replace('.', ':', $time) . ':00';
        }
        
        // Jika format HH.MM.SS
        if (preg_match('/^([01]?[0-9]|2[0-3])\.[0-5][0-9]\.[0-5][0-9]$/', $time)) {
            return str_replace('.', ':', $time);
        }
        
        return null;
    }
}

if (!function_exists('validate_time_format')) {
    /**
     * Validate if time is in acceptable format
     */
    function validate_time_format($time)
    {
        if (empty($time)) {
            return true;
        }
        
        $formats = [
            '/^([01]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', // HH:MM:SS
            '/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/',             // HH:MM
            '/^([01]?[0-9]|2[0-3])\.[0-5][0-9]$/',            // HH.MM
            '/^([01]?[0-9]|2[0-3])\.[0-5][0-9]\.[0-5][0-9]$/' // HH.MM.SS
        ];
        
        foreach ($formats as $pattern) {
            if (preg_match($pattern, $time)) {
                return true;
            }
        }
        
        return false;
    }
}