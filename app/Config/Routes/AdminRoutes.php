<?php
// app/Config/Routes/AdminRoutes.php

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

$routes->group('admin', ['filter' => 'auth', 'namespace' => 'App\Controllers\Admin'], function($routes) {

    // ============================================
    // DASHBOARD
    // ============================================
    $routes->get('/', 'Dashboard::index', ['as' => 'admin.dashboard']);
    $routes->get('dashboard', 'Dashboard::index');

    // ============================================
    // SURAT MENYURAT (Unified Module)
    // ============================================
    $routes->group('surat', function($routes) {
        $routes->get('/', 'Surat::index', ['as' => 'admin.surat']);
        $routes->get('tambah', 'Surat::tambah');
        $routes->post('simpan', 'Surat::simpan');
        $routes->get('detail/(:num)', 'Surat::detail/$1');
        $routes->get('pratinjau/(:num)', 'Surat::pratinjau/$1', ['as' => 'admin.surat.pratinjau']);
        $routes->get('edit/(:num)', 'Surat::edit/$1');
        $routes->post('update', 'Surat::update');
        $routes->post('update/(:num)', 'Surat::update/$1');
        $routes->get('hapus/(:num)', 'Surat::hapus/$1');
        $routes->post('update-status/(:num)', 'Surat::updateStatus/$1');
        $routes->get('clear-html-full', 'Surat::clearHtmlFull'); // One-time cleanup

        // Aliases / Backwards Compatibility
        $routes->get('masuk', 'Surat::index');
        $routes->get('keluar', 'Surat::index');
        $routes->get('template', 'Surat::index');
        $routes->get('masuk/tambah', 'Surat::tambah');
        $routes->get('masuk/detail/(:num)', 'Surat::detail/$1');
        $routes->get('masuk/edit/(:num)', 'Surat::edit/$1');
    });

    // ============================================
    // PENGADAAN & ASET / ATK & INVENTARIS
    // ============================================
    $routes->group('inventaris', function($routes) {
        $routes->get('/', 'Inventaris::pengajuanAtk', ['as' => 'admin.inventaris']);
        
        // 1. Pengajuan ATK
        $routes->get('pengajuan-atk', 'Inventaris::pengajuanAtk', ['as' => 'admin.inventaris.pengajuan_atk']);
        $routes->get('pengajuan-atk/review/(:num)', 'Inventaris::reviewAtk/$1');
        $routes->post('pengajuan-atk/simpan', 'Inventaris::simpanAtk');
        $routes->post('pengajuan-atk/update', 'Inventaris::updateAtk');
        $routes->post('pengajuan-atk/approve', 'Inventaris::approveAtk');
        $routes->post('pengajuan-atk/delete/(:num)', 'Inventaris::deleteAtk/$1');

        // 2. Monitoring Stok ATK
        $routes->get('stok-atk', 'Inventaris::stokAtk', ['as' => 'admin.inventaris.stok_atk']);
        $routes->get('stok-atk/detail/(:num)', 'Inventaris::detailStokAtk/$1');
        $routes->post('stok-atk/simpan', 'Inventaris::simpanStokAtk');
        $routes->post('stok-atk/update', 'Inventaris::updateStokAtk');
        $routes->post('stok-atk/delete/(:num)', 'Inventaris::deleteStokAtk/$1');

        // 3. Pengadaan Aset
        $routes->get('aset', 'Inventaris::aset', ['as' => 'admin.inventaris.aset']);
        $routes->get('inventaris-kantor', 'Inventaris::aset', ['as' => 'admin.inventaris.kantor']);
        $routes->get('aset/review/(:num)', 'Inventaris::reviewAset/$1');
        $routes->get('aset/cetak', 'Inventaris::cetakAset');
        $routes->post('aset/simpan', 'Inventaris::simpanAset');
        $routes->post('aset/update', 'Inventaris::updateAset');
        $routes->post('aset/approve', 'Inventaris::approveAset');
        $routes->post('aset/delete/(:num)', 'Inventaris::deleteAset/$1');

        // 4. Pencatatan & Tracking Pembelian (PR)
        $routes->get('pembelian', 'Inventaris::pembelian', ['as' => 'admin.inventaris.pembelian']);
        $routes->get('pembelian/tambah', 'Inventaris::tambahPembelian');
        $routes->get('pembelian/detail/(:num)', 'Inventaris::detailPembelian/$1');
        $routes->get('pembelian/edit/(:num)', 'Inventaris::editPembelian/$1');
        $routes->get('pembelian/cetak/(:num)', 'Inventaris::cetakPembelian/$1');
        $routes->get('pembelian/cetak', 'Inventaris::cetakPembelian');
        $routes->get('pembelian/export-excel', 'Inventaris::exportExcelPembelian');
        $routes->post('pembelian/simpan', 'Inventaris::simpanPembelian');
        $routes->post('pembelian/update', 'Inventaris::updatePembelian');
        $routes->post('pembelian/delete/(:num)', 'Inventaris::deletePembelian/$1');
        $routes->post('pembelian/approve', 'Inventaris::approvePembelian');

        // 5. Kerusakan Alat
        $routes->get('kerusakan', 'Inventaris::kerusakan', ['as' => 'admin.inventaris.kerusakan']);
        $routes->get('kerusakan/tambah', 'Inventaris::tambahKerusakan');
        $routes->get('kerusakan/edit/(:num)', 'Inventaris::editKerusakan/$1');
        $routes->get('kerusakan/detail/(:num)', 'Inventaris::detailKerusakan/$1');
        $routes->post('kerusakan/simpan', 'Inventaris::simpanKerusakan');
        $routes->post('kerusakan/update', 'Inventaris::updateKerusakan');
        $routes->post('kerusakan/delete/(:num)', 'Inventaris::deleteKerusakan/$1');

        // 6. Monitoring Gudang
        $routes->get('gudang', 'Inventaris::gudang', ['as' => 'admin.inventaris.gudang']);
        $routes->get('gudang/tambah', 'Inventaris::tambahGudang');
        $routes->get('gudang/edit/(:num)', 'Inventaris::editGudang/$1');
        $routes->get('gudang/detail/(:num)', 'Inventaris::detailGudang/$1');
        $routes->post('gudang/simpan', 'Inventaris::simpanGudang');
        $routes->post('gudang/update', 'Inventaris::updateGudang');
        $routes->post('gudang/delete/(:num)', 'Inventaris::deleteGudang/$1');
    });

    // Group alias: pengadaan
    $routes->group('pengadaan', function($routes) {
        $routes->get('pengajuan-atk', 'Inventaris::pengajuanAtk');
        $routes->get('stok-atk', 'Inventaris::stokAtk');
        $routes->get('aset', 'Inventaris::aset');
        $routes->get('pembelian', 'Inventaris::pembelian');
        $routes->get('kerusakan', 'Inventaris::kerusakan');
        $routes->get('gudang', 'Inventaris::gudang');
    });

    // ============================================
    // DOKUMEN ROUTES (Dokumen Penting, Sertifikat, Kontak Project)
    // ============================================
    $routes->group('dokumen', function($routes) {
        // Dokumen Penting
        $routes->get('penting', 'DokumenController::penting', ['as' => 'admin.dokumen.penting']);
        $routes->get('penting/tambah', 'DokumenController::tambah_penting', ['as' => 'admin.dokumen.tambah_penting']);
        $routes->get('penting/edit/(:num)', 'DokumenController::edit_penting/$1', ['as' => 'admin.dokumen.edit_penting']);
        $routes->get('penting/detail/(:num)', 'DokumenController::detail_penting/$1', ['as' => 'admin.dokumen.detail_penting']);
        $routes->post('penting/simpan', 'DokumenController::simpan_penting', ['as' => 'admin.dokumen.simpan_penting']);
        $routes->post('penting/update', 'DokumenController::update_penting', ['as' => 'admin.dokumen.update_penting']);
        $routes->post('penting/delete/(:num)', 'DokumenController::delete_penting/$1', ['as' => 'admin.dokumen.delete_penting']);

        // Dokumen Sertifikat
        $routes->get('sertifikat', 'DokumenController::sertifikat', ['as' => 'admin.dokumen.sertifikat']);
        $routes->get('sertifikat/tambah', 'DokumenController::tambah_sertifikat', ['as' => 'admin.dokumen.tambah_sertifikat']);
        $routes->get('sertifikat/edit/(:num)', 'DokumenController::edit_sertifikat/$1', ['as' => 'admin.dokumen.edit_sertifikat']);
        $routes->get('sertifikat/detail/(:num)', 'DokumenController::detail_sertifikat/$1', ['as' => 'admin.dokumen.detail_sertifikat']);
        $routes->post('sertifikat/simpan', 'DokumenController::simpan_sertifikat', ['as' => 'admin.dokumen.simpan_sertifikat']);
        $routes->post('sertifikat/update', 'DokumenController::update_sertifikat', ['as' => 'admin.dokumen.update_sertifikat']);
        $routes->post('sertifikat/delete/(:num)', 'DokumenController::delete_sertifikat/$1', ['as' => 'admin.dokumen.delete_sertifikat']);

        // Kontak Project
        $routes->get('kontak', 'DokumenController::kontak', ['as' => 'admin.dokumen.kontak']);
        $routes->get('kontak/tambah', 'DokumenController::tambah_kontak', ['as' => 'admin.dokumen.tambah_kontak']);
        $routes->get('kontak/edit/(:num)', 'DokumenController::edit_kontak/$1', ['as' => 'admin.dokumen.edit_kontak']);
        $routes->get('kontak/detail/(:num)', 'DokumenController::detail_kontak/$1', ['as' => 'admin.dokumen.detail_kontak']);
        $routes->post('kontak/simpan', 'DokumenController::simpan_kontak', ['as' => 'admin.dokumen.simpan_kontak']);
        $routes->post('kontak/update', 'DokumenController::update_kontak', ['as' => 'admin.dokumen.update_kontak']);
        $routes->post('kontak/delete/(:num)', 'DokumenController::delete_kontak/$1', ['as' => 'admin.dokumen.delete_kontak']);
    });

    // Legacy fallback redirect
    $routes->get('dokumen-legal', 'DokumenController::penting');
    $routes->get('dokumen-legal/arsip', 'DokumenController::penting');

    // ============================================
    // FASILITAS & TAMU
    // ============================================
    $routes->group('fasilitas', function($routes) {
        $routes->get('/', 'Fasilitas::bukuTamu', ['as' => 'admin.fasilitas']);
        
        // Buku Tamu
        $routes->get('buku-tamu', 'Fasilitas::bukuTamu', ['as' => 'admin.fasilitas.buku_tamu']);
        $routes->post('buku-tamu/simpan', 'Fasilitas::simpanBukuTamu', ['as' => 'admin.fasilitas.buku_tamu.simpan']);
        $routes->get('buku-tamu/detail/(:num)', 'Fasilitas::detailBukuTamu/$1', ['as' => 'admin.fasilitas.buku_tamu.detail']);
        $routes->get('buku-tamu/edit/(:num)', 'Fasilitas::editBukuTamu/$1', ['as' => 'admin.fasilitas.buku_tamu.edit']);
        $routes->post('buku-tamu/update', 'Fasilitas::updateBukuTamu', ['as' => 'admin.fasilitas.buku_tamu.update']);
        $routes->post('buku-tamu/status', 'Fasilitas::updateStatusBukuTamu', ['as' => 'admin.fasilitas.buku_tamu.status']);
        $routes->post('buku-tamu/delete/(:num)', 'Fasilitas::deleteBukuTamu/$1', ['as' => 'admin.fasilitas.buku_tamu.delete']);

        // Booking Ruang Meeting
        $routes->get('booking-ruang', 'Fasilitas::bookingRuang', ['as' => 'admin.fasilitas.booking_ruang']);
        $routes->post('booking-ruang/simpan', 'Fasilitas::simpanBookingRuang', ['as' => 'admin.fasilitas.booking_ruang.simpan']);
        $routes->get('booking-ruang/detail/(:num)', 'Fasilitas::detailBookingRuang/$1', ['as' => 'admin.fasilitas.booking_ruang.detail']);
        $routes->get('booking-ruang/edit/(:num)', 'Fasilitas::editBookingRuang/$1', ['as' => 'admin.fasilitas.booking_ruang.edit']);
        $routes->post('booking-ruang/update', 'Fasilitas::updateBookingRuang', ['as' => 'admin.fasilitas.booking_ruang.update']);
        $routes->post('booking-ruang/status', 'Fasilitas::updateStatusBookingRuang', ['as' => 'admin.fasilitas.booking_ruang.status']);
        $routes->post('booking-ruang/delete/(:num)', 'Fasilitas::deleteBookingRuang/$1', ['as' => 'admin.fasilitas.booking_ruang.delete']);

        // Koordinasi Kendaraan
        $routes->get('kendaraan', 'Fasilitas::kendaraan', ['as' => 'admin.fasilitas.kendaraan']);
        $routes->post('kendaraan/simpan', 'Fasilitas::simpanKendaraan', ['as' => 'admin.fasilitas.kendaraan.simpan']);
        $routes->get('kendaraan/detail/(:num)', 'Fasilitas::detailKendaraan/$1', ['as' => 'admin.fasilitas.kendaraan.detail']);
        $routes->get('kendaraan/edit/(:num)', 'Fasilitas::editKendaraan/$1', ['as' => 'admin.fasilitas.kendaraan.edit']);
        $routes->post('kendaraan/update', 'Fasilitas::updateKendaraan', ['as' => 'admin.fasilitas.kendaraan.update']);
        $routes->post('kendaraan/status', 'Fasilitas::updateStatusKendaraan', ['as' => 'admin.fasilitas.kendaraan.status']);
        $routes->post('kendaraan/delete/(:num)', 'Fasilitas::deleteKendaraan/$1', ['as' => 'admin.fasilitas.kendaraan.delete']);
    });

    // ============================================
    // PENGAJUAN (Semua Pengajuan Non-Cuti, Cuti)
    // ============================================
    $routes->group('pengajuan', function($routes) {
        $routes->get('/', 'Pengajuan::semua', ['as' => 'admin.pengajuan']);
        $routes->get('semua', 'Pengajuan::semua', ['as' => 'admin.pengajuan.semua']);
        $routes->get('tambah', 'Pengajuan::tambah', ['as' => 'admin.pengajuan.tambah']);
        $routes->post('simpan', 'Pengajuan::simpan', ['as' => 'admin.pengajuan.simpan']);
        $routes->get('detail/(:num)', 'Pengajuan::detail/$1', ['as' => 'admin.pengajuan.detail']);
        $routes->get('edit/(:num)', 'Pengajuan::edit/$1', ['as' => 'admin.pengajuan.edit']);
        $routes->post('update', 'Pengajuan::update', ['as' => 'admin.pengajuan.update']);
        $routes->post('delete/(:num)', 'Pengajuan::delete/$1', ['as' => 'admin.pengajuan.delete']);

        // Cuti Khusus
        $routes->get('cuti', 'Pengajuan::cuti', ['as' => 'admin.pengajuan.cuti']);
        $routes->get('cuti/tambah', 'Pengajuan::tambahCuti', ['as' => 'admin.pengajuan.cuti.tambah']);
        $routes->post('cuti/simpan', 'Pengajuan::simpanCuti', ['as' => 'admin.pengajuan.cuti.simpan']);
        $routes->get('cuti/detail/(:num)', 'Pengajuan::detailCuti/$1', ['as' => 'admin.pengajuan.cuti.detail']);
        $routes->get('cuti/edit/(:num)', 'Pengajuan::editCuti/$1', ['as' => 'admin.pengajuan.cuti.edit']);
        $routes->post('cuti/update', 'Pengajuan::updateCuti', ['as' => 'admin.pengajuan.cuti.update']);
        $routes->post('cuti/delete/(:num)', 'Pengajuan::deleteCuti/$1', ['as' => 'admin.pengajuan.cuti.delete']);

        // Kasbon (Terhubung Ke Direktur Keuangan Kasbon)
        $routes->get('kasbon', 'Pengajuan::kasbon', ['as' => 'admin.pengajuan.kasbon']);
        $routes->get('kasbon/tambah', 'Pengajuan::tambahKasbon', ['as' => 'admin.pengajuan.kasbon.tambah']);
        $routes->post('kasbon/simpan', 'Pengajuan::simpanKasbon', ['as' => 'admin.pengajuan.kasbon.simpan']);
        $routes->get('kasbon/detail/(:num)', 'Pengajuan::detailKasbon/$1', ['as' => 'admin.pengajuan.kasbon.detail']);
        $routes->post('kasbon/delete/(:num)', 'Pengajuan::deleteKasbon/$1', ['as' => 'admin.pengajuan.kasbon.delete']);
    });

    // Form Pengajuan Fallback Alias
    $routes->group('form-pengajuan', function($routes) {
        $routes->get('/', 'Pengajuan::tambah', ['as' => 'admin.form_pengajuan']);
        $routes->get('cuti', 'Pengajuan::tambahCuti', ['as' => 'admin.form_pengajuan.cuti']);
        $routes->post('store', 'Pengajuan::simpan', ['as' => 'admin.form_pengajuan.store']);
    });

    // ============================================
    // LAPORAN & KELUHAN
    // ============================================
    $routes->group('laporan', function($routes) {
        $routes->get('/', 'Laporan::kerjaHarian', ['as' => 'admin.laporan']);
        $routes->get('dashboard', 'Laporan::dashboard', ['as' => 'admin.laporan.dashboard']);
        $routes->get('kerja-harian', 'Laporan::kerjaHarian', ['as' => 'admin.laporan.kerja_harian']);
        $routes->get('kerja-harian/tambah', 'Laporan::tambahKerjaHarian', ['as' => 'admin.laporan.kerja_harian.tambah']);
        $routes->post('kerja-harian/simpan', 'Laporan::simpanKerjaHarian', ['as' => 'admin.laporan.kerja_harian.simpan']);
        $routes->get('kerja-harian/detail/(:num)', 'Laporan::detailKerjaHarian/$1', ['as' => 'admin.laporan.kerja_harian.detail']);
        $routes->get('kerja-harian/edit/(:num)', 'Laporan::editKerjaHarian/$1', ['as' => 'admin.laporan.kerja_harian.edit']);
        $routes->post('kerja-harian/update', 'Laporan::updateKerjaHarian', ['as' => 'admin.laporan.kerja_harian.update']);
        $routes->get('kerja-harian/delete/(:num)', 'Laporan::deleteKerjaHarian/$1', ['as' => 'admin.laporan.kerja_harian.delete']);
        $routes->post('kerja-harian/delete/(:num)', 'Laporan::deleteKerjaHarian/$1');
        $routes->get('keluhan', 'Laporan::keluhan', ['as' => 'admin.laporan.keluhan']);
        $routes->get('keluhan/tambah', 'Laporan::tambahKeluhan', ['as' => 'admin.laporan.keluhan.tambah']);
        $routes->post('keluhan/simpan', 'Laporan::simpanKeluhan', ['as' => 'admin.laporan.keluhan.simpan']);
        $routes->get('keluhan/detail/(:num)', 'Laporan::detailKeluhan/$1', ['as' => 'admin.laporan.keluhan.detail']);
        $routes->post('keluhan/delete/(:num)', 'Laporan::deleteKeluhan/$1', ['as' => 'admin.laporan.keluhan.delete']);
    });

    // ============================================
    // MENU PRIBADI
    // ============================================
    // Absensi Mandiri Admin (Terhubung ke Direktur)
    $routes->get('absensi-saya', 'Pribadi::absensi', ['as' => 'admin.pribadi.absensi']);
    $routes->post('absensi-saya/checkin', 'Pribadi::checkin', ['as' => 'admin.pribadi.checkin']);
    $routes->post('absensi-saya/checkout', 'Pribadi::checkout', ['as' => 'admin.pribadi.checkout']);

    // Tugas Hari Ini
    $routes->get('tugas-saya', 'Pribadi::tugas', ['as' => 'admin.pribadi.tugas']);
    $routes->get('tugas-saya/detail/(:num)', 'Pribadi::detailTugas/$1', ['as' => 'admin.pribadi.tugas.detail']);
    $routes->post('tugas-saya/update-status/(:num)', 'Pribadi::updateStatusTugas/$1', ['as' => 'admin.pribadi.tugas.update_status']);
    $routes->post('tugas-saya/update-subitems/(:num)', 'Pribadi::updateSubItemStatus/$1', ['as' => 'admin.pribadi.tugas.update_subitems']);
    $routes->get('tugas-saya/buat-laporan/(:num)', 'Pribadi::buatLaporanFromTugas/$1', ['as' => 'admin.pribadi.tugas.buat_laporan']);

    // Timeline Kerja & Project Saat Ini (Terhubung & Berbagi Fitur Penuh dari Direktur)
    $routes->get('timeline-kerja', 'Pribadi::timelineKerja', ['as' => 'admin.pribadi.timeline_kerja']);
    $routes->post('timeline-kerja/aktifkan', 'Pribadi::aktifkanProyekTimeline', ['as' => 'admin.pribadi.timeline_kerja.aktifkan']);
    $routes->get('timeline-kerja/detail/(:num)', 'Pribadi::detailTimelineKerja/$1', ['as' => 'admin.pribadi.timeline_kerja.detail']);
    $routes->get('timeline-kerja/(:num)', 'Pribadi::detailTimelineKerja/$1');
    $routes->post('timeline-kerja/simpan_task', 'Pribadi::simpanTaskTimeline', ['as' => 'admin.pribadi.timeline_kerja.simpan_task']);
    $routes->post('timeline-kerja/update_task_status', 'Pribadi::updateTaskStatusTimeline', ['as' => 'admin.pribadi.timeline_kerja.update_task_status']);
    $routes->post('timeline-kerja/delete_task/(:num)', 'Pribadi::deleteTaskTimeline/$1', ['as' => 'admin.pribadi.timeline_kerja.delete_task']);
    $routes->post('timeline-kerja/selesaikan/(:num)', 'Pribadi::selesaikanProyekTimeline/$1', ['as' => 'admin.pribadi.timeline_kerja.selesaikan_proyek']);
    $routes->post('timeline-kerja/delete/(:num)', 'Pribadi::deleteProyekTimeline/$1', ['as' => 'admin.pribadi.timeline_kerja.delete_proyek']);
    $routes->get('project-saat-ini', 'Pribadi::projectSaatIni', ['as' => 'admin.pribadi.project_saat_ini']);

    // Laporan Harian Mandiri
    $routes->get('laporan-harian-saya', 'Pribadi::laporanHarian', ['as' => 'admin.pribadi.laporan_harian']);
    $routes->post('laporan-harian-saya/store', 'Pribadi::storeLaporan', ['as' => 'admin.pribadi.laporan_harian.store']);

    // Keluhan Saya
    $routes->get('keluhan-saya', 'Pribadi::keluhan', ['as' => 'admin.pribadi.keluhan']);
    $routes->post('keluhan-saya/store', 'Pribadi::storeKeluhan', ['as' => 'admin.pribadi.keluhan.store']);
    $routes->get('keluhan-saya/detail/(:num)', 'Pribadi::detailKeluhan/$1', ['as' => 'admin.pribadi.keluhan.detail']);
    $routes->get('keluhan-saya/edit/(:num)', 'Pribadi::editKeluhan/$1', ['as' => 'admin.pribadi.keluhan.edit']);
    $routes->post('keluhan-saya/update', 'Pribadi::updateKeluhan', ['as' => 'admin.pribadi.keluhan.update']);
    $routes->post('keluhan-saya/delete/(:num)', 'Pribadi::deleteKeluhan/$1', ['as' => 'admin.pribadi.keluhan.delete']);

    // Slip Gaji
    $routes->get('slip-gaji', 'Pribadi::slipGaji', ['as' => 'admin.pribadi.slip_gaji']);

    // Profil
    $routes->get('profil', 'Pribadi::profil', ['as' => 'admin.profil']);
    $routes->post('profil/update', 'Pribadi::updateProfil', ['as' => 'admin.profil.update']);

}); // END OF ADMIN GROUP