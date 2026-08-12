<?php
// app/Config/Routes/AdminPanelRoutes.php

/**
 * @var \CodeIgniter\Router\RouteCollection $routes
 * 
 * File ini berisi semua routes untuk Admin Panel (Sistem Administrator):
 * - Dashboard
 * - Surat Menyurat
 * - ATK & Inventaris
 * - Dokumen Legal
 * - Fasilitas & Tamu
 * - Pengajuan
 * - Laporan & Keluhan
 * - Menu Pribadi (Absensi, Tugas, Laporan, Keluhan, Form Pengajuan, Slip Gaji, Profil)
 */

// ============================================
// ADMIN PANEL ROUTES (Dengan Filter Auth)
// ============================================

$routes->group('admin', ['filter' => 'auth'], function($routes) {

    // ============================================
    // DASHBOARD
    // ============================================
    $routes->get('/', 'AdminPanel\Dashboard::index', ['as' => 'admin.dashboard']);
    $routes->get('dashboard', 'AdminPanel\Dashboard::index');

    // ============================================
    // SURAT MENYURAT
    // ============================================
    $routes->group('surat', ['namespace' => 'App\Controllers\AdminPanel'], function($routes) {
        $routes->get('/', 'Surat::masuk', ['as' => 'admin.surat']);
        $routes->get('masuk', 'Surat::masuk', ['as' => 'admin.surat.masuk']);
        $routes->get('keluar', 'Surat::keluar', ['as' => 'admin.surat.keluar']);
        $routes->get('template', 'Surat::template', ['as' => 'admin.surat.template']);
    });

    // ============================================
    // ATK & INVENTARIS
    // ============================================
    $routes->group('inventaris', ['namespace' => 'App\Controllers\AdminPanel'], function($routes) {
        $routes->get('/', 'Inventaris::stokAtk', ['as' => 'admin.inventaris']);
        $routes->get('stok-atk', 'Inventaris::stokAtk', ['as' => 'admin.inventaris.stok_atk']);
        $routes->get('pengajuan-atk', 'Inventaris::pengajuanAtk', ['as' => 'admin.inventaris.pengajuan_atk']);
        $routes->get('inventaris-kantor', 'Inventaris::inventarisKantor', ['as' => 'admin.inventaris.kantor']);
    });

    // ============================================
    // DOKUMEN ROUTES (Dokumen Penting, Sertifikat, Kontak Project)
    // ============================================
    $routes->group('dokumen', ['namespace' => 'App\Controllers\Admin'], function($routes) {
        $routes->get('penting', 'DokumenController::penting');
        $routes->get('penting/tambah', 'DokumenController::tambah_penting');
        $routes->get('penting/edit/(:num)', 'DokumenController::edit_penting/$1');
        $routes->get('penting/detail/(:num)', 'DokumenController::detail_penting/$1');
        $routes->post('penting/simpan', 'DokumenController::simpan_penting');
        $routes->post('penting/update', 'DokumenController::update_penting');
        $routes->post('penting/delete/(:num)', 'DokumenController::delete_penting/$1');

        $routes->get('sertifikat', 'DokumenController::sertifikat');
        $routes->get('sertifikat/tambah', 'DokumenController::tambah_sertifikat');
        $routes->get('sertifikat/edit/(:num)', 'DokumenController::edit_sertifikat/$1');
        $routes->get('sertifikat/detail/(:num)', 'DokumenController::detail_sertifikat/$1');
        $routes->post('sertifikat/simpan', 'DokumenController::simpan_sertifikat');
        $routes->post('sertifikat/update', 'DokumenController::update_sertifikat');
        $routes->post('sertifikat/delete/(:num)', 'DokumenController::delete_sertifikat/$1');

        $routes->get('kontak', 'DokumenController::kontak');
        $routes->get('kontak/tambah', 'DokumenController::tambah_kontak');
        $routes->get('kontak/edit/(:num)', 'DokumenController::edit_kontak/$1');
        $routes->get('kontak/detail/(:num)', 'DokumenController::detail_kontak/$1');
        $routes->post('kontak/simpan', 'DokumenController::simpan_kontak');
        $routes->post('kontak/update', 'DokumenController::update_kontak');
        $routes->post('kontak/delete/(:num)', 'DokumenController::delete_kontak/$1');
    });

    $routes->get('dokumen-legal', 'Admin\DokumenController::penting');
    $routes->get('dokumen-legal/arsip', 'Admin\DokumenController::penting');

    // ============================================
    // FASILITAS & TAMU
    // ============================================
    $routes->group('fasilitas', ['namespace' => 'App\Controllers\AdminPanel'], function($routes) {
        $routes->get('/', 'Fasilitas::bukuTamu', ['as' => 'admin.fasilitas']);
        $routes->get('buku-tamu', 'Fasilitas::bukuTamu', ['as' => 'admin.fasilitas.buku_tamu']);
        $routes->get('booking-ruang', 'Fasilitas::bookingRuang', ['as' => 'admin.fasilitas.booking_ruang']);
        $routes->get('kendaraan', 'Fasilitas::kendaraan', ['as' => 'admin.fasilitas.kendaraan']);
    });

    // ============================================
    // PENGAJUAN (Semua Pengajuan & Cuti)
    // ============================================
    $routes->group('pengajuan', ['namespace' => 'App\Controllers\AdminPanel'], function($routes) {
        $routes->get('/', 'Pengajuan::semua', ['as' => 'admin.pengajuan']);
        $routes->get('semua', 'Pengajuan::semua', ['as' => 'admin.pengajuan.semua']);
        $routes->get('cuti', 'Pengajuan::cuti', ['as' => 'admin.pengajuan.cuti']);
    });

    // ============================================
    // LAPORAN & KELUHAN
    // ============================================
    $routes->group('laporan', ['namespace' => 'App\Controllers\AdminPanel'], function($routes) {
        $routes->get('/', 'Laporan::dashboard', ['as' => 'admin.laporan']);
        $routes->get('dashboard', 'Laporan::dashboard', ['as' => 'admin.laporan.dashboard']);
        $routes->get('kerja-harian', 'Laporan::kerjaHarian', ['as' => 'admin.laporan.kerja_harian']);
        $routes->get('keluhan', 'Laporan::keluhan', ['as' => 'admin.laporan.keluhan']);
    });

    // ============================================
    // MENU PRIBADI
    // ============================================
    // Absensi Pribadi
    $routes->get('absensi-saya', 'Admin\Pribadi::absensi', ['as' => 'admin.pribadi.absensi']);
    $routes->post('absensi-saya/checkin', 'Admin\Pribadi::checkin', ['as' => 'admin.pribadi.checkin']);
    $routes->post('absensi-saya/checkout', 'Admin\Pribadi::checkout', ['as' => 'admin.pribadi.checkout']);

    // Tugas Saya
    $routes->get('tugas-saya', 'Admin\Pribadi::tugas', ['as' => 'admin.pribadi.tugas']);

    // Laporan Kerja Harian Pribadi
    $routes->get('laporan-harian-saya', 'Admin\Pribadi::laporanHarian', ['as' => 'admin.pribadi.laporan_harian']);
    $routes->post('laporan-harian-saya/store', 'Admin\Pribadi::storeLaporan', ['as' => 'admin.pribadi.laporan_harian.store']);

    // Keluhan Saya
    $routes->get('keluhan-saya', 'Admin\Pribadi::keluhan', ['as' => 'admin.pribadi.keluhan']);
    $routes->post('keluhan-saya/store', 'Admin\Pribadi::storeKeluhan', ['as' => 'admin.pribadi.keluhan.store']);

    // Form Pengajuan
    $routes->get('form-pengajuan/cuti', 'Admin\Pribadi::pengajuanCuti', ['as' => 'admin.pribadi.pengajuan_cuti']);
    $routes->post('form-pengajuan/cuti/store', 'Admin\Pribadi::storeCuti', ['as' => 'admin.pribadi.pengajuan_cuti.store']);

    // Slip Gaji
    $routes->get('slip-gaji', 'Admin\Pribadi::slipGaji', ['as' => 'admin.pribadi.slip_gaji']);

    // Timeline Kerja & Project Saat Ini (Terhubung dari Direktur)
    $routes->get('timeline-kerja', 'Admin\Pribadi::timelineKerja', ['as' => 'admin.pribadi.timeline_kerja']);
    $routes->get('project-saat-ini', 'Admin\Pribadi::projectSaatIni', ['as' => 'admin.pribadi.project_saat_ini']);

    // Profil
    $routes->get('profil', 'Admin\Pribadi::profil', ['as' => 'admin.profil']);
    $routes->post('profil/update', 'Admin\Pribadi::updateProfil', ['as' => 'admin.profil.update']);

}); // END OF ADMIN PANEL GROUP
