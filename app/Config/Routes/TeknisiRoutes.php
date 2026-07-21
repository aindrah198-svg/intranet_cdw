<?php
// C:\xampp\htdocs\intranet_cdw\app\Config\Routes\TeknisiRoutes.php

/**
 * @var \CodeIgniter\Router\RouteCollection $routes
 * 
 * File ini berisi semua routes untuk Teknisi:
 * - Dashboard
 * - Absensi
 * - Tugas & Proyek (SPK Instalasi, Tambahan Barang, Timeline)
 * - Tambah Client
 * - Gudang & Penyimpanan
 * - Pengajuan
 * - Cuti
 * - Laporan
 * - Profile
 */

// ============================================
// TEKNISI ROUTES (Dengan Filter Auth)
// ============================================

$routes->group('teknisi', ['filter' => 'auth'], function($routes) {
    
    // ============================================
    // DASHBOARD
    // ============================================
    $routes->get('/', 'Teknisi\Dashboard::index', ['as' => 'teknisi']);
    $routes->get('dashboard', 'Teknisi\Dashboard::index', ['as' => 'teknisi.dashboard']);
    
    // ============================================
    // ABSENSI TEKNISI
    // ============================================
    $routes->group('absensi', function($routes) {
        $routes->get('/', 'Teknisi\Absensi::index', ['as' => 'teknisi.absensi']);
        $routes->post('checkin', 'Teknisi\Absensi::checkin', ['as' => 'teknisi.absensi.checkin']);
        $routes->post('checkout', 'Teknisi\Absensi::checkout', ['as' => 'teknisi.absensi.checkout']);
        $routes->get('history', 'Teknisi\Absensi::history', ['as' => 'teknisi.absensi.history']);
    });
    
    // ============================================
    // TUGAS & PROYEK
    // ============================================
    $routes->group('tugas-proyek', function($routes) {
        $routes->get('/', 'Teknisi\TugasProyek::index', ['as' => 'teknisi.tugas_proyek']);
        
        // SPK / Tugas Instalasi Routes
        $routes->group('spk', function($routes) {
            $routes->get('/', 'Teknisi\SpkInstalasi::index', ['as' => 'teknisi.spk']);
            $routes->get('create', 'Teknisi\SpkInstalasi::create', ['as' => 'teknisi.spk.create']);
            $routes->post('store', 'Teknisi\SpkInstalasi::store', ['as' => 'teknisi.spk.store']);
            $routes->get('detail/(:num)', 'Teknisi\SpkInstalasi::detail/$1', ['as' => 'teknisi.spk.detail']);
            $routes->get('edit/(:num)', 'Teknisi\SpkInstalasi::edit/$1', ['as' => 'teknisi.spk.edit']);
            $routes->post('update/(:num)', 'Teknisi\SpkInstalasi::update/$1', ['as' => 'teknisi.spk.update']);
            $routes->post('delete/(:num)', 'Teknisi\SpkInstalasi::delete/$1', ['as' => 'teknisi.spk.delete']);
            $routes->post('update-progress', 'Teknisi\SpkInstalasi::updateProgress', ['as' => 'teknisi.spk.updateProgress']);
            $routes->post('selesaikan/(:num)', 'Teknisi\SpkInstalasi::selesaikan/$1', ['as' => 'teknisi.spk.selesaikan']);
            $routes->get('get-client/(:num)', 'Teknisi\SpkInstalasi::getClient/$1', ['as' => 'teknisi.spk.getClient']);
        });
        
        // Tambahan Barang Routes
        $routes->group('tambahan-barang', function($routes) {
            $routes->get('/', 'Teknisi\TambahanBarang::index', ['as' => 'teknisi.tambahan_barang']);
            $routes->get('create', 'Teknisi\TambahanBarang::create', ['as' => 'teknisi.tambahan_barang.create']);
            $routes->get('create/(:num)', 'Teknisi\TambahanBarang::createWithSpk/$1', ['as' => 'teknisi.tambahan_barang.create_with_spk']);
            $routes->post('store', 'Teknisi\TambahanBarang::store', ['as' => 'teknisi.tambahan_barang.store']);
            $routes->get('detail/(:num)', 'Teknisi\TambahanBarang::detail/$1', ['as' => 'teknisi.tambahan_barang.detail']);
            $routes->get('edit/(:num)', 'Teknisi\TambahanBarang::edit/$1', ['as' => 'teknisi.tambahan_barang.edit']);
            $routes->post('update/(:num)', 'Teknisi\TambahanBarang::update/$1', ['as' => 'teknisi.tambahan_barang.update']);
            $routes->post('delete/(:num)', 'Teknisi\TambahanBarang::delete/$1', ['as' => 'teknisi.tambahan_barang.delete']);
            $routes->get('get-by-spk/(:num)', 'Teknisi\TambahanBarang::getBySpk/$1', ['as' => 'teknisi.tambahan_barang.getBySpk']);
            $routes->get('total-by-spk/(:num)', 'Teknisi\TambahanBarang::getTotalBySpk/$1', ['as' => 'teknisi.tambahan_barang.totalBySpk']);
            $routes->get('export-excel/(:num)', 'Teknisi\TambahanBarang::exportExcel/$1', ['as' => 'teknisi.tambahan_barang.export_excel']);
            $routes->post('set-uang-akomodasi', 'Teknisi\TambahanBarang::setUangAkomodasi', ['as' => 'teknisi.tambahan_barang.set_uang_akomodasi']);
        });
        
        // Timeline Routes
        $routes->get('timeline', 'Teknisi\Timeline::index', ['as' => 'teknisi.tugas_proyek.timeline']);
        $routes->get('timeline/data', 'Teknisi\Timeline::getTimelineData', ['as' => 'teknisi.timeline.data']);
        $routes->get('timeline/detail/(:num)', 'Teknisi\Timeline::detail/$1', ['as' => 'teknisi.timeline.detail']);
        
        // Tambahan Waktu
        $routes->get('tambahan-waktu', 'Teknisi\TugasProyek::tambahanWaktu', ['as' => 'teknisi.tugas_proyek.tambahan_waktu']);
    });
    
    // ============================================
    // TAMBAH CLIENT
    // ============================================
    $routes->group('tambah-client', function($routes) {
        $routes->get('/', 'Teknisi\TambahClient::index', ['as' => 'teknisi.tambah_client']);
        $routes->get('create', 'Teknisi\TambahClient::create', ['as' => 'teknisi.tambah_client.create']);
        $routes->post('store', 'Teknisi\TambahClient::store', ['as' => 'teknisi.tambah_client.store']);
        $routes->get('edit/(:num)', 'Teknisi\TambahClient::edit/$1', ['as' => 'teknisi.tambah_client.edit']);
        $routes->post('update/(:num)', 'Teknisi\TambahClient::update/$1', ['as' => 'teknisi.tambah_client.update']);
        $routes->post('delete/(:num)', 'Teknisi\TambahClient::delete/$1', ['as' => 'teknisi.tambah_client.delete']);
        $routes->get('detail/(:num)', 'Teknisi\TambahClient::detail/$1', ['as' => 'teknisi.tambah_client.detail']);
        $routes->post('ajax-store', 'Teknisi\TambahClient::ajaxStore', ['as' => 'teknisi.tambah_client.ajax_store']);
        $routes->get('get-list', 'Teknisi\TambahClient::getList', ['as' => 'teknisi.tambah_client.get_list']);
        $routes->post('ubah-status/(:num)', 'Teknisi\TambahClient::ubahStatus/$1', ['as' => 'teknisi.tambah_client.ubah_status']);
    });
    
    // ============================================
    // GUDANG & PENYIMPANAN
    // ============================================
    $routes->group('gudang', function($routes) {
        $routes->get('/', 'Teknisi\Gudang::index', ['as' => 'teknisi.gudang']);
        $routes->get('penyimpanan', 'Teknisi\Gudang::penyimpanan', ['as' => 'teknisi.gudang.penyimpanan']);
        $routes->get('peralatan-dipinjam', 'Teknisi\Gudang::peralatanDipinjam', ['as' => 'teknisi.gudang.peralatan_dipinjam']);
        $routes->get('perawatan-alat', 'Teknisi\Gudang::perawatanAlat', ['as' => 'teknisi.gudang.perawatan_alat']);
    });
    
    // ============================================
    // PENGAJUAN
    // ============================================
    $routes->group('pengajuan', function($routes) {
        $routes->get('/', 'Teknisi\Pengajuan::index', ['as' => 'teknisi.pengajuan']);
        $routes->get('permintaan-pembelian', 'Teknisi\Pengajuan::permintaanPembelian', ['as' => 'teknisi.pengajuan.permintaan_pembelian']);
        $routes->get('biaya-lapangan', 'Teknisi\Pengajuan::biayaLapangan', ['as' => 'teknisi.pengajuan.biaya_lapangan']);
    });
    
    // ============================================
    // CUTI TEKNISI
    // ============================================
    $routes->group('cuti', function($routes) {
        $routes->get('/', 'Teknisi\Cuti::index', ['as' => 'teknisi.cuti']);
        $routes->get('create', 'Teknisi\Cuti::create', ['as' => 'teknisi.cuti.create']);
        $routes->post('store', 'Teknisi\Cuti::store', ['as' => 'teknisi.cuti.store']);
        $routes->get('history', 'Teknisi\Cuti::history', ['as' => 'teknisi.cuti.history']);
    });
    
    // ============================================
    // LAPORAN TEKNISI
    // ============================================
    $routes->group('laporan', function($routes) {
        $routes->get('/', 'Teknisi\Laporan::index', ['as' => 'teknisi.laporan']);
        $routes->get('lapangan', 'Teknisi\Laporan::lapangan', ['as' => 'teknisi.laporan.lapangan']);
        $routes->get('inventory', 'Teknisi\Laporan::inventory', ['as' => 'teknisi.laporan.inventory']);
    });
    
    // ============================================
    // PROFILE TEKNISI
    // ============================================
    $routes->group('profile', function($routes) {
        $routes->get('/', 'Teknisi\Profile::index', ['as' => 'teknisi.profile']);
        $routes->post('update', 'Teknisi\Profile::update', ['as' => 'teknisi.profile.update']);
        $routes->post('change-password', 'Teknisi\Profile::changePassword', ['as' => 'teknisi.profile.change_password']);
    });
    
}); // END OF TEKNISI GROUP