<?php

if (!function_exists('admin_url')) {
    /**
     * Admin URL helper
     * 
     * @param string $path
     * @return string
     */
    function admin_url(string $path = ''): string
    {
        $baseURL = rtrim(base_url(), '/');
        $path = ltrim($path, '/');
        
        return $baseURL . '/admin/' . $path;
    }
}