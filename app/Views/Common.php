<?php
// app/Common.php - BARIS PALING ATAS

// ==============================================
// LOAD COMPOSER AUTOLOAD DULU SEBELUM APAPUN!
// ==============================================

// Cek apakah vendor autoload sudah dimuat
if (!class_exists('CodeIgniter\Config\BaseConfig')) {
    // Path ke vendor autoload
    $vendorPath = dirname(__DIR__, 1) . '/vendor/autoload.php';
    
    if (file_exists($vendorPath)) {
        require_once $vendorPath;
    } else {
        die('ERROR: vendor/autoload.php tidak ditemukan. Jalankan: composer install');
    }
}

// Setelah ini, baru kode Common.php Anda yang lain...
// ...

// Contoh kode Common.php yang sudah ada:
// if (! function_exists('function_name')) {
//     function function_name() {
//         // ...
//     }
// }