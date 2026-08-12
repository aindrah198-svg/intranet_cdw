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
// LOAD ROUTES FROM SEPARATE FILES
// ============================================

// Daftar file routes yang akan dimuat
$routesFiles = [
    'AuthRoutes',            // Login, logout, forgot password
    'PublicRoutes',          // Home, about, contact, services, products, projects, gallery
    'SuperadminRoutes',      // Superadmin group (/superadmin)
    'HrdRoutes',             // HRD group (/hrd) - karyawan, absensi, cuti, jam-kerja, dokumen, kontrak
    'AdminRoutes',           // Admin Panel group (/admin) - surat, inventaris, dokumen legal, fasilitas
    'AdminPanelRoutes',      // Admin Panel routes fallback
    'DirekturRoutes',        // Direktur group (dashboard, monitoring, approval, penawaran, laporan)
    'TeknisiRoutes',         // Teknisi group (absensi, tugas-proyek, gudang, pengajuan, cuti)
    'SalesRoutes',           // Sales group (client, penawaran, project, invoice, surat-jalan) - KOSONG
    'AccountingRoutes',      // Accounting group (kas-bank, pembukuan, penggajian, aset-tetap, pajak, laporan)
    'StaffRoutes',           // Staff group (dashboard, logout)
    'SoftwareEngineerRoutes', // Software Engineer group (dev, maintenance, hosting, bugs, docs, etc)
];

// Load setiap file routes jika ada
foreach ($routesFiles as $file) {
    $filePath = APPPATH . 'Config/Routes/' . $file . '.php';
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
    // Debug routes - menampilkan semua route yang terdaftar
    $routes->get('debug-routes', function() use ($routes) {
        echo '<h2>Daftar Semua Routes</h2>';
        echo '<pre>';
        print_r($routes->getRoutes());
        echo '</pre>';
    });
    
    // Debug remember me
    $routes->get('debug-remember', 'Auth::debugRemember');
    $routes->get('test-remember/(:num)', 'Auth::testSetRemember/$1');
}