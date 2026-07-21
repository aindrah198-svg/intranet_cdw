<?php
// C:\xampp\htdocs\intranet_cdw\app\Config\Routes.php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Initialize routes
$routes = Services::routes();

// ============================================
// DEFAULT CONFIGURATIONS
// ============================================
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

// ============================================
// DIRECT TEST ROUTE (TAMBAHKAN INI DULU UNTUK TESTING)
// ============================================
$routes->get('/', 'Home::index');
$routes->get('/test-simple', function() {
    return "Simple route test works!";
});

// ============================================
// LOAD ROUTES FROM SEPARATE FILES
// ============================================

// Definisikan path folder routes
$routesFolder = APPPATH . 'Config/Routes/';

// Daftar file routes yang akan dimuat
$routesFiles = [
    'AuthRoutes',
    'PublicRoutes',
    'AdminRoutes',
    'DirekturRoutes',
    'TeknisiRoutes',
    'SalesRoutes',
    'AccountingRoutes',
];

// Load setiap file routes jika ada
foreach ($routesFiles as $file) {
    $filePath = $routesFolder . $file . '.php';
    if (file_exists($filePath)) {
        require_once $filePath;
    }
}

// ============================================
// ERROR PAGES
// ============================================
$routes->set404Override(function() {
    $data = [
        'title' => '404 - Halaman Tidak Ditemukan',
        'message' => 'Halaman yang Anda cari tidak ditemukan.'
    ];
    return view('errors/html/error_404', $data);
});

// ============================================
// DEBUG ROUTES (Development Only)
// ============================================
if (ENVIRONMENT === 'development') {
    $routes->get('debug-routes', function() use ($routes) {
        echo '<h2>Daftar Semua Routes</h2>';
        echo '<pre>';
        print_r($routes->getRoutes());
        echo '</pre>';
        
        echo '<h3>Current URL: ' . current_url() . '</h3>';
        echo '<h3>Base URL: ' . base_url() . '</h3>';
    });
}