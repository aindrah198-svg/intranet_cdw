<?php

if (!function_exists('format_currency')) {
    /**
     * Format number to Indonesian currency
     */
    function format_currency($number, $decimal = 0)
    {
        if (!is_numeric($number)) {
            return '0';
        }
        
        return 'Rp ' . number_format($number, $decimal, ',', '.');
    }
}

if (!function_exists('truncate_text')) {
    /**
     * Truncate text with ellipsis
     */
    function truncate_text($text, $length = 100, $ellipsis = '...')
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        
        $truncated = substr($text, 0, $length);
        $last_space = strrpos($truncated, ' ');
        
        if ($last_space !== false) {
            $truncated = substr($truncated, 0, $last_space);
        }
        
        return $truncated . $ellipsis;
    }
}

if (!function_exists('generate_random_string')) {
    /**
     * Generate random string
     */
    function generate_random_string($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        
        return $randomString;
    }
}

if (!function_exists('get_user_status_badge')) {
    /**
     * Get user status badge HTML
     */
    function get_user_status_badge($status)
    {
        $badges = [
            'active' => '<span class="badge bg-success">Aktif</span>',
            'inactive' => '<span class="badge bg-secondary">Nonaktif</span>',
            'suspended' => '<span class="badge bg-danger">Suspended</span>'
        ];
        
        return $badges[$status] ?? '<span class="badge bg-light text-dark">Unknown</span>';
    }
}

if (!function_exists('get_role_badge')) {
    /**
     * Get role badge HTML
     */
    function get_role_badge($role)
    {
        $badges = [
            'admin' => '<span class="badge bg-danger"><i class="fas fa-shield-alt me-1"></i>Admin</span>',
            'manager' => '<span class="badge bg-warning"><i class="fas fa-user-tie me-1"></i>Manager</span>',
            'staff' => '<span class="badge bg-info"><i class="fas fa-user me-1"></i>Staff</span>'
        ];
        
        return $badges[$role] ?? '<span class="badge bg-light text-dark">Unknown</span>';
    }
}