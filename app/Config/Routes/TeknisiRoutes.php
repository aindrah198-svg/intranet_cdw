<?php
// app/Config/Routes/TeknisiRoutes.php

/**
 * @var \CodeIgniter\Router\RouteCollection $routes
 */

$routes->group('teknisi', ['filter' => 'auth'], function($routes) {
    // Dashboard
    $routes->get('/', 'Teknisi\Dashboard::index', ['as' => 'teknisi']);
    $routes->get('dashboard', 'Teknisi\Dashboard::index', ['as' => 'teknisi.dashboard']);

    // Absensi
    $routes->group('absensi', function($routes) {
        $routes->get('/', 'Teknisi\Absensi::index', ['as' => 'teknisi.absensi']);
        $routes->post('checkin', 'Teknisi\Absensi::checkin', ['as' => 'teknisi.absensi.checkin']);
        $routes->post('checkout', 'Teknisi\Absensi::checkout', ['as' => 'teknisi.absensi.checkout']);
        $routes->get('history', 'Teknisi\Absensi::history', ['as' => 'teknisi.absensi.history']);
    });

    // Tugas & Proyek
    $routes->group('tugas-proyek', function($routes) {
        $routes->get('/', 'Teknisi\SpkInstalasi::index', ['as' => 'teknisi.tugas_proyek']);
        
        // SPK / Tugas Instalasi
        $routes->group('spk', function($routes) {
            $routes->get('/', 'Teknisi\SpkInstalasi::index', ['as' => 'teknisi.spk']);
            $routes->get('create', 'Teknisi\SpkInstalasi::create', ['as' => 'teknisi.spk.create']);
            $routes->post('store', 'Teknisi\SpkInstalasi::store', ['as' => 'teknisi.spk.store']);
            $routes->get('detail/(:num)', 'Teknisi\SpkInstalasi::detail/$1', ['as' => 'teknisi.spk.detail']);
            $routes->post('selesaikan/(:num)', 'Teknisi\SpkInstalasi::selesaikan/$1', ['as' => 'teknisi.spk.selesaikan']);
        });

        // Timeline
        $routes->get('timeline', 'Teknisi\Timeline::index', ['as' => 'teknisi.tugas_proyek.timeline']);
        
        // Tambahan Barang
        $routes->group('tambahan-barang', function($routes) {
            $routes->get('/', 'Teknisi\TambahanBarang::index', ['as' => 'teknisi.tambahan_barang']);
            $routes->get('create', 'Teknisi\TambahanBarang::create', ['as' => 'teknisi.tambahan_barang.create']);
            $routes->post('store', 'Teknisi\TambahanBarang::store', ['as' => 'teknisi.tambahan_barang.store']);
        });

        // Info Client (Centralized Read-Only Client Data for Assigned Projects)
        $routes->group('info-client', function($routes) {
            $routes->get('/', 'Teknisi\InfoClient::index', ['as' => 'teknisi.info_client']);
            $routes->get('detail/(:num)', 'Teknisi\InfoClient::detail/$1', ['as' => 'teknisi.info_client.detail']);
        });
        
        // Alias legacy route untuk compatibility
        $routes->get('tambah-client', 'Teknisi\InfoClient::index');
    });

    // Gudang & Penyimpanan
    $routes->group('gudang', function($routes) {
        $routes->get('/', 'Teknisi\Gudang::index', ['as' => 'teknisi.gudang']);
        $routes->get('penyimpanan', 'Teknisi\Gudang::penyimpanan', ['as' => 'teknisi.gudang.penyimpanan']);
        $routes->get('peralatan-dipinjam', 'Teknisi\Gudang::peralatanDipinjam', ['as' => 'teknisi.gudang.peralatan_dipinjam']);
        $routes->post('pinjam-alat', 'Teknisi\Gudang::pinjamAlat', ['as' => 'teknisi.gudang.pinjam_alat']);
        $routes->post('kembalikan-alat/(:num)', 'Teknisi\Gudang::kembalikanAlat/$1', ['as' => 'teknisi.gudang.kembalikan_alat']);
        $routes->get('perawatan-alat', 'Teknisi\Gudang::perawatanAlat', ['as' => 'teknisi.gudang.perawatan_alat']);
    });

    // Pengajuan
    $routes->group('pengajuan', function($routes) {
        $routes->get('/', 'Teknisi\Pengajuan::index', ['as' => 'teknisi.pengajuan']);
        $routes->get('permintaan-pembelian', 'Teknisi\Pengajuan::permintaanPembelian', ['as' => 'teknisi.pengajuan.permintaan_pembelian']);
        $routes->get('biaya-lapangan', 'Teknisi\Pengajuan::biayaLapangan', ['as' => 'teknisi.pengajuan.biaya_lapangan']);
        $routes->post('store-biaya-lapangan', 'Teknisi\Pengajuan::storeBiayaLapangan', ['as' => 'teknisi.pengajuan.store_biaya_lapangan']);
        $routes->get('cuti', 'Teknisi\Pengajuan::cuti', ['as' => 'teknisi.pengajuan.cuti']);
    });

    // Laporan (FITUR WAJIB)
    $routes->group('laporan', function($routes) {
        $routes->get('/', 'Teknisi\Laporan::index', ['as' => 'teknisi.laporan']);
        $routes->get('lapangan', 'Teknisi\Laporan::lapangan', ['as' => 'teknisi.laporan.lapangan']);
        $routes->get('harian', 'Teknisi\Laporan::lapangan'); // alias
        $routes->post('store-lapangan', 'Teknisi\Laporan::storeLapangan', ['as' => 'teknisi.laporan.store_lapangan']);
        $routes->get('keluhan', 'Teknisi\Laporan::keluhan', ['as' => 'teknisi.laporan.keluhan']);
        $routes->post('store-keluhan', 'Teknisi\Laporan::storeKeluhan', ['as' => 'teknisi.laporan.store_keluhan']);
        $routes->get('inventory', 'Teknisi\Laporan::inventory', ['as' => 'teknisi.laporan.inventory']);
    });

    // Menu Pribadi
    $routes->group('pribadi', function($routes) {
        $routes->get('absensi', 'Teknisi\Pribadi::absensi', ['as' => 'teknisi.pribadi.absensi']);
        $routes->get('tugas', 'Teknisi\Pribadi::tugas', ['as' => 'teknisi.pribadi.tugas']);
        $routes->get('laporan-harian', 'Teknisi\Pribadi::laporanHarian', ['as' => 'teknisi.pribadi.laporan-harian']);
        $routes->get('keluhan', 'Teknisi\Pribadi::keluhan', ['as' => 'teknisi.pribadi.keluhan']);
        $routes->get('pengajuan', 'Teknisi\Pribadi::pengajuan', ['as' => 'teknisi.pribadi.pengajuan']);
        $routes->get('slip-gaji', 'Teknisi\Pribadi::slipGaji', ['as' => 'teknisi.pribadi.slip-gaji']);
        $routes->get('profil', 'Teknisi\Pribadi::profil', ['as' => 'teknisi.pribadi.profil']);
    });

    // Profile Legacy Route
    $routes->get('profile', 'Teknisi\Pribadi::profil');
});