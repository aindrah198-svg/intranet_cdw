<?php
// C:\xampp\htdocs\intranet_cdw\app\Config\Routes\DirekturRoutes.php

/**
 * @var \CodeIgniter\Router\RouteCollection $routes
 * 
 * File ini berisi semua routes untuk Direktur:
 * - Dashboard
 * - Monitoring (Absensi, Performansi, Ringkasan Penggajian, Invoice Piutang)
 * - Approval (Cuti, SPK, Kasbon, Dokumen, Pembelian, Surat Jalan, Izin, BAST)
 * - Laporan
 */

// ============================================
// DIREKTUR ROUTES (Dengan Filter Auth)
// ============================================

$routes->group('direktur', ['filter' => 'auth'], function($routes) {
    
    // ============================================
    // DASHBOARD
    // ============================================
    
    $routes->get('/', 'Direktur\Dashboard\Dashboard::index', ['as' => 'direktur.dashboard']);
    $routes->get('dashboard', 'Direktur\Dashboard\Dashboard::index', ['as' => 'direktur.dashboard.index']);
    
    // AJAX endpoints untuk dashboard
    $routes->get('dashboard/get-stats', 'Direktur\Dashboard\Dashboard::getStats', ['as' => 'direktur.dashboard.getStats']);
    $routes->get('dashboard/get-chart-data', 'Direktur\Dashboard\Dashboard::getChartData', ['as' => 'direktur.dashboard.getChartData']);
    $routes->get('dashboard/get-recent-activities', 'Direktur\Dashboard\Dashboard::getRecentActivities', ['as' => 'direktur.dashboard.getRecentActivities']);
    $routes->get('dashboard/get-notifications', 'Direktur\Dashboard\Dashboard::getNotifications', ['as' => 'direktur.dashboard.getNotifications']);
    $routes->get('dashboard/export-pdf', 'Direktur\Dashboard\Dashboard::exportPdf', ['as' => 'direktur.dashboard.exportPdf']);
    $routes->get('dashboard/print', 'Direktur\Dashboard\Dashboard::printView', ['as' => 'direktur.dashboard.print']);
    
    // ============================================
    // KARYAWAN & SDM ROUTES
    // ============================================
    $routes->group('karyawan', function($routes) {
        // Kelola Karyawan (CRUD)
        $routes->get('/', 'Direktur\Karyawan\KaryawanController::index', ['as' => 'direktur.karyawan']);
        $routes->get('tambah', 'Direktur\Karyawan\KaryawanController::tambah', ['as' => 'direktur.karyawan.tambah']);
        $routes->post('simpan', 'Direktur\Karyawan\KaryawanController::simpan', ['as' => 'direktur.karyawan.simpan']);
        $routes->get('detail/(:num)', 'Direktur\Karyawan\KaryawanController::detail/$1', ['as' => 'direktur.karyawan.detail']);
        $routes->get('edit/(:num)', 'Direktur\Karyawan\KaryawanController::edit/$1', ['as' => 'direktur.karyawan.edit']);
        $routes->post('update/(:num)', 'Direktur\Karyawan\KaryawanController::update/$1', ['as' => 'direktur.karyawan.update']);
        $routes->post('delete/(:num)', 'Direktur\Karyawan\KaryawanController::delete/$1', ['as' => 'direktur.karyawan.delete']);
        $routes->post('tambah-dummy', 'Direktur\Karyawan\KaryawanController::tambahDummy', ['as' => 'direktur.karyawan.tambahDummy']);
        $routes->post('edit-dummy/(:num)', 'Direktur\Karyawan\KaryawanController::editDummy/$1', ['as' => 'direktur.karyawan.editDummy']);
        
        // Akun Karyawan
        $routes->get('akun', 'Direktur\Karyawan\KaryawanController::akun', ['as' => 'direktur.karyawan.akun']);
        $routes->post('generate-akun', 'Direktur\Karyawan\KaryawanController::generateAkun', ['as' => 'direktur.karyawan.generate_akun']);
        $routes->get('edit-akun/(:num)', 'Direktur\Karyawan\KaryawanController::editAkun/$1', ['as' => 'direktur.karyawan.edit_akun']);
        $routes->post('update-akun/(:num)', 'Direktur\Karyawan\KaryawanController::updateAkun/$1', ['as' => 'direktur.karyawan.update_akun']);
        $routes->post('hapus-akun/(:num)', 'Direktur\Karyawan\KaryawanController::hapusAkun/$1', ['as' => 'direktur.karyawan.hapus_akun']);
        
        // Surat Karyawan (Kontrak/SP/Lainnya)
        $routes->get('surat', 'Direktur\Karyawan\SuratController::index', ['as' => 'direktur.karyawan.surat']);
        $routes->get('surat/tambah', 'Direktur\Karyawan\SuratController::tambah', ['as' => 'direktur.karyawan.surat.tambah']);
        $routes->post('surat/simpan', 'Direktur\Karyawan\SuratController::simpan', ['as' => 'direktur.karyawan.surat.simpan']);
        $routes->get('surat/detail/(:num)', 'Direktur\Karyawan\SuratController::detail/$1', ['as' => 'direktur.karyawan.surat.detail']);
        $routes->get('surat/pratinjau/(:num)', 'Direktur\Karyawan\SuratController::pratinjau/$1', ['as' => 'direktur.karyawan.surat.pratinjau']);
        $routes->get('surat/edit/(:num)', 'Direktur\Karyawan\SuratController::edit/$1', ['as' => 'direktur.karyawan.surat.edit']);
        $routes->post('surat/update/(:num)', 'Direktur\Karyawan\SuratController::update/$1', ['as' => 'direktur.karyawan.surat.update']);
        $routes->post('surat/delete/(:num)', 'Direktur\Karyawan\SuratController::delete/$1', ['as' => 'direktur.karyawan.surat.delete']);
        $routes->post('surat/update-status/(:num)', 'Direktur\Karyawan\SuratController::updateStatus/$1', ['as' => 'direktur.karyawan.surat.updateStatus']);
        
        // Keluhan Karyawan
        $routes->get('keluhan', 'Direktur\Karyawan\KeluhanController::index', ['as' => 'direktur.karyawan.keluhan']);
        $routes->get('keluhan/tambah', 'Direktur\Karyawan\KeluhanController::tambah', ['as' => 'direktur.karyawan.keluhan.tambah']);
        $routes->post('keluhan/simpan', 'Direktur\Karyawan\KeluhanController::simpan', ['as' => 'direktur.karyawan.keluhan.simpan']);
        $routes->get('keluhan/detail/(:num)', 'Direktur\Karyawan\KeluhanController::detail/$1', ['as' => 'direktur.karyawan.keluhan.detail']);
        $routes->post('keluhan/tanggapi/(:num)', 'Direktur\Karyawan\KeluhanController::tanggapi/$1', ['as' => 'direktur.karyawan.keluhan.tanggapi']);
        $routes->post('keluhan/delete/(:num)', 'Direktur\Karyawan\KeluhanController::delete/$1', ['as' => 'direktur.karyawan.keluhan.delete']);
        
        // Permohonan & Izin Karyawan (Non-Cuti)
        $routes->get('pengajuan', 'Direktur\Karyawan\KaryawanController::pengajuan', ['as' => 'direktur.karyawan.pengajuan']);
        $routes->get('pengajuan/detail/(:num)', 'Direktur\Karyawan\KaryawanController::detailPengajuan/$1', ['as' => 'direktur.karyawan.pengajuan.detail']);
        $routes->get('pengajuan/edit/(:num)', 'Direktur\Karyawan\KaryawanController::editPengajuan/$1', ['as' => 'direktur.karyawan.pengajuan.edit']);
        $routes->post('pengajuan/update/(:num)', 'Direktur\Karyawan\KaryawanController::updatePengajuan/$1', ['as' => 'direktur.karyawan.pengajuan.update']);
        $routes->post('pengajuan/approve/(:num)', 'Direktur\Karyawan\KaryawanController::approvePengajuan/$1', ['as' => 'direktur.karyawan.pengajuan.approve']);
        $routes->post('pengajuan/reject/(:num)', 'Direktur\Karyawan\KaryawanController::rejectPengajuan/$1', ['as' => 'direktur.karyawan.pengajuan.reject']);
        $routes->post('pengajuan/delete/(:num)', 'Direktur\Karyawan\KaryawanController::deletePengajuan/$1', ['as' => 'direktur.karyawan.pengajuan.delete']);
        
        // Cuti Karyawan & Approval
        $routes->get('cuti', 'Direktur\Karyawan\KaryawanController::cuti', ['as' => 'direktur.karyawan.cuti']);
        $routes->get('cuti/detail/(:num)', 'Direktur\Karyawan\KaryawanController::detailCuti/$1', ['as' => 'direktur.karyawan.cuti.detail']);
        $routes->get('cuti/edit/(:num)', 'Direktur\Karyawan\KaryawanController::editCuti/$1', ['as' => 'direktur.karyawan.cuti.edit']);
        $routes->post('cuti/update/(:num)', 'Direktur\Karyawan\KaryawanController::updateCuti/$1', ['as' => 'direktur.karyawan.cuti.update']);
        $routes->post('cuti/approve/(:num)', 'Direktur\Karyawan\KaryawanController::approveCuti/$1', ['as' => 'direktur.karyawan.cuti.approve']);
        $routes->post('cuti/reject/(:num)', 'Direktur\Karyawan\KaryawanController::rejectCuti/$1', ['as' => 'direktur.karyawan.cuti.reject']);
        $routes->post('cuti/kuota', 'Direktur\Karyawan\KaryawanController::updateKuotaCuti', ['as' => 'direktur.karyawan.cuti.kuota']);
        $routes->post('cuti/delete/(:num)', 'Direktur\Karyawan\KaryawanController::deleteCuti/$1', ['as' => 'direktur.karyawan.cuti.delete']);
        
        $routes->get('absensi', 'Direktur\Karyawan\KaryawanController::absensi', ['as' => 'direktur.karyawan.absensi']);
    });

    // ============================================
    // MONITORING ROUTES
    // ============================================
    $routes->group('monitoring', function($routes) {
        
        // Halaman utama monitoring
        $routes->get('/', 'Direktur\Monitoring\MonitoringController::index', ['as' => 'direktur.monitoring']);
        
        // Absensi
        $routes->get('absensi', 'Direktur\Monitoring\Absensi::index', ['as' => 'direktur.monitoring.absensi']);
        $routes->post('absensi/simpan', 'Direktur\Monitoring\Absensi::simpan', ['as' => 'direktur.monitoring.absensi.simpan']);
        $routes->get('absensi/detail/(:num)', 'Direktur\Monitoring\Absensi::detail/$1', ['as' => 'direktur.monitoring.absensi.detail']);
        $routes->post('absensi/update/(:num)', 'Direktur\Monitoring\Absensi::update/$1', ['as' => 'direktur.monitoring.absensi.update']);
        $routes->post('absensi/delete/(:num)', 'Direktur\Monitoring\Absensi::delete/$1', ['as' => 'direktur.monitoring.absensi.delete']);
        $routes->get('absensi/exportExcel', 'Direktur\Monitoring\Absensi::exportExcel', ['as' => 'direktur.monitoring.absensi.exportExcel']);
        $routes->get('absensi/exportPdf', 'Direktur\Monitoring\Absensi::exportPdf', ['as' => 'direktur.monitoring.absensi.exportPdf']);
        $routes->get('absensi/print', 'Direktur\Monitoring\Absensi::print', ['as' => 'direktur.monitoring.absensi.print']);
        $routes->get('absensi/get-summary', 'Direktur\Monitoring\Absensi::getSummary', ['as' => 'direktur.monitoring.absensi.getSummary']);
        $routes->get('absensi/get-stats', 'Direktur\Monitoring\Absensi::getStats', ['as' => 'direktur.monitoring.absensi.getStats']);
        
        // Performansi
        $routes->get('performansi', 'Direktur\Monitoring\Performansi::index', ['as' => 'direktur.monitoring.performansi']);
        $routes->get('performansi/detail/(:num)', 'Direktur\Monitoring\Performansi::detail/$1', ['as' => 'direktur.monitoring.performansi.detail']);
        $routes->get('performansi/exportExcel', 'Direktur\Monitoring\Performansi::exportExcel', ['as' => 'direktur.monitoring.performansi.exportExcel']);
        $routes->get('performansi/print', 'Direktur\Monitoring\Performansi::print', ['as' => 'direktur.monitoring.performansi.print']);
        $routes->get('performansi/get-summary', 'Direktur\Monitoring\Performansi::getSummary', ['as' => 'direktur.monitoring.performansi.getSummary']);
        $routes->get('performansi/get-top-performers', 'Direktur\Monitoring\Performansi::getTopPerformers', ['as' => 'direktur.monitoring.performansi.getTopPerformers']);
        
        // Ringkasan Penggajian
        $routes->get('ringkasan-penggajian', 'Direktur\Monitoring\RingkasanPenggajian::index', ['as' => 'direktur.monitoring.ringkasan_penggajian']);
        $routes->get('ringkasan-penggajian/detail/(:num)', 'Direktur\Monitoring\RingkasanPenggajian::detail/$1', ['as' => 'direktur.monitoring.ringkasan_penggajian.detail']);
        $routes->get('ringkasan-penggajian/exportExcel', 'Direktur\Monitoring\RingkasanPenggajian::exportExcel', ['as' => 'direktur.monitoring.ringkasan_penggajian.exportExcel']);
        $routes->get('ringkasan-penggajian/print', 'Direktur\Monitoring\RingkasanPenggajian::print', ['as' => 'direktur.monitoring.ringkasan_penggajian.print']);
        $routes->get('ringkasan-penggajian/get-summary', 'Direktur\Monitoring\RingkasanPenggajian::getSummary', ['as' => 'direktur.monitoring.ringkasan_penggajian.getSummary']);
        $routes->get('ringkasan-penggajian/get-top-earners', 'Direktur\Monitoring\RingkasanPenggajian::getTopEarners', ['as' => 'direktur.monitoring.ringkasan_penggajian.getTopEarners']);
        $routes->get('ringkasan-penggajian/get-department-summary', 'Direktur\Monitoring\RingkasanPenggajian::getDepartmentSummary', ['as' => 'direktur.monitoring.ringkasan_penggajian.getDepartmentSummary']);
        
        // Invoice Piutang
        $routes->get('invoice-piutang', 'Direktur\Monitoring\InvoicePiutang::index', ['as' => 'direktur.monitoring.invoice_piutang']);
        $routes->get('invoice-piutang/detail/(:num)', 'Direktur\Monitoring\InvoicePiutang::detail/$1', ['as' => 'direktur.monitoring.invoice_piutang.detail']);
        $routes->get('invoice-piutang/exportExcel', 'Direktur\Monitoring\InvoicePiutang::exportExcel', ['as' => 'direktur.monitoring.invoice_piutang.exportExcel']);
        $routes->get('invoice-piutang/print', 'Direktur\Monitoring\InvoicePiutang::print', ['as' => 'direktur.monitoring.invoice_piutang.print']);
        $routes->get('invoice-piutang/get-summary', 'Direktur\Monitoring\InvoicePiutang::getSummary', ['as' => 'direktur.monitoring.invoice_piutang.getSummary']);
        $routes->get('invoice-piutang/get-aging-report', 'Direktur\Monitoring\InvoicePiutang::getAgingReport', ['as' => 'direktur.monitoring.invoice_piutang.getAgingReport']);
        
    }); // END OF MONITORING GROUP
    
    // ============================================
    // APPROVAL ROUTES
    // ============================================
    $routes->group('approval', function($routes) {
        
        // Halaman utama approval (ringkasan semua approval)
        $routes->get('/', 'Direktur\Approval\ApprovalController::index', ['as' => 'direktur.approval']);
        
        // ========== Approval Cuti ==========
        $routes->group('cuti', function($routes) {
            $routes->get('/', 'Direktur\Approval\CutiController::index', ['as' => 'direktur.approval.cuti']);
            $routes->get('detail/(:num)', 'Direktur\Approval\CutiController::detail/$1', ['as' => 'direktur.approval.cuti.detail']);
            $routes->post('approve/(:num)', 'Direktur\Approval\CutiController::approve/$1', ['as' => 'direktur.approval.cuti.approve']);
            $routes->post('reject/(:num)', 'Direktur\Approval\CutiController::reject/$1', ['as' => 'direktur.approval.cuti.reject']);
            $routes->get('export-pdf/(:num)', 'Direktur\Approval\CutiController::exportPdf/$1', ['as' => 'direktur.approval.cuti.exportPdf']);
            $routes->get('export-excel', 'Direktur\Approval\CutiController::exportExcel', ['as' => 'direktur.approval.cuti.exportExcel']);
            $routes->get('print/(:num)', 'Direktur\Approval\CutiController::print/$1', ['as' => 'direktur.approval.cuti.print']);
            $routes->post('batch-approve', 'Direktur\Approval\CutiController::batchApprove', ['as' => 'direktur.approval.cuti.batchApprove']);
        });
        
        // ========== Approval SPK ==========
        $routes->group('spk', function($routes) {
            $routes->get('/', 'Direktur\Approval\SpkController::index', ['as' => 'direktur.approval.spk']);
            $routes->get('detail/(:num)', 'Direktur\Approval\SpkController::detail/$1', ['as' => 'direktur.approval.spk.detail']);
            $routes->post('approve/(:num)', 'Direktur\Approval\SpkController::approve/$1', ['as' => 'direktur.approval.spk.approve']);
            $routes->post('reject/(:num)', 'Direktur\Approval\SpkController::reject/$1', ['as' => 'direktur.approval.spk.reject']);
            $routes->get('export-pdf/(:num)', 'Direktur\Approval\SpkController::exportPdf/$1', ['as' => 'direktur.approval.spk.exportPdf']);
            $routes->get('export-excel', 'Direktur\Approval\SpkController::exportExcel', ['as' => 'direktur.approval.spk.exportExcel']);
            $routes->get('print/(:num)', 'Direktur\Approval\SpkController::print/$1', ['as' => 'direktur.approval.spk.print']);
        });
        
        // ========== Approval Kasbon ==========
        $routes->group('kasbon', function($routes) {
            $routes->get('/', 'Direktur\Approval\KasbonController::index', ['as' => 'direktur.approval.kasbon']);
            $routes->get('detail/(:num)', 'Direktur\Approval\KasbonController::detail/$1', ['as' => 'direktur.approval.kasbon.detail']);
            $routes->post('approve/(:num)', 'Direktur\Approval\KasbonController::approve/$1', ['as' => 'direktur.approval.kasbon.approve']);
            $routes->post('reject/(:num)', 'Direktur\Approval\KasbonController::reject/$1', ['as' => 'direktur.approval.kasbon.reject']);
            $routes->get('export-excel', 'Direktur\Approval\KasbonController::exportExcel', ['as' => 'direktur.approval.kasbon.exportExcel']);
        });
        
        // ========== Approval Dokumen ==========
        $routes->group('dokumen', function($routes) {
            $routes->get('/', 'Direktur\Approval\DokumenController::index', ['as' => 'direktur.approval.dokumen']);
            $routes->get('detail/(:num)', 'Direktur\Approval\DokumenController::detail/$1', ['as' => 'direktur.approval.dokumen.detail']);
            $routes->post('approve/(:num)', 'Direktur\Approval\DokumenController::approve/$1', ['as' => 'direktur.approval.dokumen.approve']);
            $routes->post('reject/(:num)', 'Direktur\Approval\DokumenController::reject/$1', ['as' => 'direktur.approval.dokumen.reject']);
            $routes->get('export-excel', 'Direktur\Approval\DokumenController::exportExcel', ['as' => 'direktur.approval.dokumen.exportExcel']);
        });
        
        // ========== Approval Pembelian ==========
        $routes->group('pembelian', function($routes) {
            $routes->get('/', 'Direktur\Approval\PembelianController::index', ['as' => 'direktur.approval.pembelian']);
            $routes->get('detail/(:num)', 'Direktur\Approval\PembelianController::detail/$1', ['as' => 'direktur.approval.pembelian.detail']);
            $routes->post('approve/(:num)', 'Direktur\Approval\PembelianController::approve/$1', ['as' => 'direktur.approval.pembelian.approve']);
            $routes->post('reject/(:num)', 'Direktur\Approval\PembelianController::reject/$1', ['as' => 'direktur.approval.pembelian.reject']);
            $routes->get('export-excel', 'Direktur\Approval\PembelianController::exportExcel', ['as' => 'direktur.approval.pembelian.exportExcel']);
        });
        
        // ========== Approval Surat Jalan ==========
        $routes->group('surat-jalan', function($routes) {
            $routes->get('/', 'Direktur\Approval\SuratJalanController::index', ['as' => 'direktur.approval.surat_jalan']);
            $routes->get('detail/(:num)', 'Direktur\Approval\SuratJalanController::detail/$1', ['as' => 'direktur.approval.surat_jalan.detail']);
            $routes->post('approve/(:num)', 'Direktur\Approval\SuratJalanController::approve/$1', ['as' => 'direktur.approval.surat_jalan.approve']);
            $routes->post('reject/(:num)', 'Direktur\Approval\SuratJalanController::reject/$1', ['as' => 'direktur.approval.surat_jalan.reject']);
            $routes->get('export-excel', 'Direktur\Approval\SuratJalanController::exportExcel', ['as' => 'direktur.approval.surat_jalan.exportExcel']);
        });
        
        // ========== Approval Izin ==========
        $routes->group('izin', function($routes) {
            $routes->get('/', 'Direktur\Approval\IzinController::index', ['as' => 'direktur.approval.izin']);
            $routes->get('detail/(:num)', 'Direktur\Approval\IzinController::detail/$1', ['as' => 'direktur.approval.izin.detail']);
            $routes->post('approve/(:num)', 'Direktur\Approval\IzinController::approve/$1', ['as' => 'direktur.approval.izin.approve']);
            $routes->post('reject/(:num)', 'Direktur\Approval\IzinController::reject/$1', ['as' => 'direktur.approval.izin.reject']);
            $routes->get('export-excel', 'Direktur\Approval\IzinController::exportExcel', ['as' => 'direktur.approval.izin.exportExcel']);
        });
        
        // ========== Approval BAST ==========
        $routes->group('bast', function($routes) {
            $routes->get('/', 'Direktur\Approval\BastController::index', ['as' => 'direktur.approval.bast']);
            $routes->get('detail/(:num)', 'Direktur\Approval\BastController::detail/$1', ['as' => 'direktur.approval.bast.detail']);
            $routes->post('approve/(:num)', 'Direktur\Approval\BastController::approve/$1', ['as' => 'direktur.approval.bast.approve']);
            $routes->post('reject/(:num)', 'Direktur\Approval\BastController::reject/$1', ['as' => 'direktur.approval.bast.reject']);
            $routes->get('export-excel', 'Direktur\Approval\BastController::exportExcel', ['as' => 'direktur.approval.bast.exportExcel']);
            $routes->get('print/(:num)', 'Direktur\Approval\BastController::print/$1', ['as' => 'direktur.approval.bast.print']);
        });
        
    }); // END OF APPROVAL GROUP
    
    // ============================================
    // LAPORAN ROUTES
    // ============================================
    $routes->group('laporan', function($routes) {
        $routes->get('keuangan', 'Direktur\Laporan::keuangan', ['as' => 'direktur.laporan.keuangan']);
        $routes->get('stok-gudang', 'Direktur\Laporan::stokGudang', ['as' => 'direktur.laporan.stok_gudang']);
    });
    
    // ============================================
    // PENUGASAN HARIAN ROUTES
    // ============================================
    $routes->group('penugasan', function($routes) {
        $routes->get('/', 'Direktur\PenugasanController::index', ['as' => 'direktur.penugasan']);
        $routes->get('harian', 'Direktur\PenugasanController::index');
        $routes->get('tambah', 'Direktur\PenugasanController::tambah', ['as' => 'direktur.penugasan.tambah']);
        $routes->post('store', 'Direktur\PenugasanController::store', ['as' => 'direktur.penugasan.store']);
        $routes->get('detail/(:num)', 'Direktur\PenugasanController::detail/$1', ['as' => 'direktur.penugasan.detail']);
        $routes->get('edit/(:num)', 'Direktur\PenugasanController::edit/$1', ['as' => 'direktur.penugasan.edit']);
        $routes->post('update/(:num)', 'Direktur\PenugasanController::update/$1', ['as' => 'direktur.penugasan.update']);
        $routes->post('update-item-status/(:num)', 'Direktur\PenugasanController::updateItemStatus/$1', ['as' => 'direktur.penugasan.updateItemStatus']);
        $routes->get('delete/(:num)', 'Direktur\PenugasanController::delete/$1', ['as' => 'direktur.penugasan.delete']);
        $routes->post('delete/(:num)', 'Direktur\PenugasanController::delete/$1');
    });
    
    // ============================================
    // PROYEK & LAPORAN ROUTES
    // ============================================
    $routes->group('proyek', function($routes) {
        
        // Project Baru & Manajemen Proyek
        $routes->get('baru', 'Direktur\Proyek\ProyekController::baru', ['as' => 'direktur.proyek.baru']);
        $routes->get('tambah', 'Direktur\Proyek\ProyekController::tambah_proyek', ['as' => 'direktur.proyek.tambah_proyek']);
        $routes->get('edit/(:num)', 'Direktur\Proyek\ProyekController::edit_proyek/$1', ['as' => 'direktur.proyek.edit_proyek']);
        $routes->get('detail/(:num)', 'Direktur\Proyek\ProyekController::detail_proyek/$1', ['as' => 'direktur.proyek.detail_proyek']);
        $routes->post('simpan', 'Direktur\Proyek\ProyekController::simpan', ['as' => 'direktur.proyek.simpan']);
        $routes->post('update', 'Direktur\Proyek\ProyekController::update_proyek', ['as' => 'direktur.proyek.update_proyek']);
        $routes->post('delete/(:num)', 'Direktur\Proyek\ProyekController::delete_proyek/$1', ['as' => 'direktur.proyek.delete_proyek']);
        $routes->post('simpan_client', 'Direktur\Proyek\ProyekController::simpan_client', ['as' => 'direktur.proyek.simpan_client']);
        
        // Timeline Kerja
        $routes->get('timeline', 'Direktur\Proyek\ProyekController::timeline', ['as' => 'direktur.proyek.timeline']);
        $routes->get('timeline/tambah', 'Direktur\Proyek\ProyekController::tambah_timeline', ['as' => 'direktur.proyek.tambah_timeline']);
        $routes->get('timeline/edit/(:num)', 'Direktur\Proyek\ProyekController::edit_timeline/$1', ['as' => 'direktur.proyek.edit_timeline']);
        $routes->get('timeline/(:num)', 'Direktur\Proyek\ProyekController::detail_timeline/$1', ['as' => 'direktur.proyek.detail_timeline']);
        $routes->get('timeline/export-excel/(:num)', 'Direktur\Proyek\ProyekController::export_excel_timeline/$1', ['as' => 'direktur.proyek.export_excel_timeline']);
        $routes->get('timeline/print-pdf/(:num)', 'Direktur\Proyek\ProyekController::print_pdf_timeline/$1', ['as' => 'direktur.proyek.print_pdf_timeline']);
        $routes->post('timeline/aktifkan', 'Direktur\Proyek\ProyekController::aktifkan_proyek_timeline', ['as' => 'direktur.proyek.aktifkan_proyek_timeline']);
        $routes->post('timeline/simpan', 'Direktur\Proyek\ProyekController::simpan_timeline', ['as' => 'direktur.proyek.simpan_timeline']);
        $routes->post('timeline/update', 'Direktur\Proyek\ProyekController::update_timeline', ['as' => 'direktur.proyek.update_timeline']);
        $routes->post('timeline/delete/(:num)', 'Direktur\Proyek\ProyekController::delete_timeline/$1', ['as' => 'direktur.proyek.delete_timeline']);
        $routes->post('timeline/simpan_task', 'Direktur\Proyek\ProyekController::simpan_task', ['as' => 'direktur.proyek.simpan_task']);
        $routes->post('timeline/update_task_status', 'Direktur\Proyek\ProyekController::update_task_status', ['as' => 'direktur.proyek.update_task_status']);
        $routes->post('timeline/delete_task/(:num)', 'Direktur\Proyek\ProyekController::delete_task/$1', ['as' => 'direktur.proyek.delete_task']);
        $routes->post('timeline/update-progress', 'Direktur\Proyek\ProyekController::update_progress', ['as' => 'direktur.proyek.update_progress']);
        $routes->post('timeline/selesaikan/(:num)', 'Direktur\Proyek\ProyekController::selesaikan_proyek/$1', ['as' => 'direktur.proyek.selesaikan_proyek']);
        
        // Project Selesai & Arsip
        $routes->get('selesai', 'Direktur\Proyek\ProyekController::selesai', ['as' => 'direktur.proyek.selesai']);
        $routes->post('selesai/simpan', 'Direktur\Proyek\ProyekController::simpan_selesai', ['as' => 'direktur.proyek.simpan_selesai']);
        $routes->post('selesai/delete/(:num)', 'Direktur\Proyek\ProyekController::delete_selesai/$1', ['as' => 'direktur.proyek.delete_selesai']);
        
        // Laporan Kerja Harian (Milik Sendiri)
        $routes->get('laporan-harian', 'Direktur\Proyek\LaporanHarianController::index', ['as' => 'direktur.proyek.laporan_harian']);
        $routes->post('laporan-harian/simpan', 'Direktur\Proyek\LaporanHarianController::simpan', ['as' => 'direktur.proyek.laporan_harian.simpan']);
        
        // Monitoring Laporan (Semua Karyawan)
        $routes->get('monitoring-laporan', 'Direktur\Proyek\LaporanHarianController::monitoring', ['as' => 'direktur.proyek.monitoring_laporan']);
        $routes->post('monitoring-laporan/approve', 'Direktur\Proyek\LaporanHarianController::approve', ['as' => 'direktur.proyek.monitoring_laporan.approve']);
        
        // Pencarian Barang / Penugasan RAB
        $routes->get('pencarian-barang', 'Direktur\Proyek\PencarianController::index', ['as' => 'direktur.proyek.pencarian_barang']);
        $routes->get('pencarian-barang/tambah', 'Direktur\Proyek\PencarianController::tambah', ['as' => 'direktur.proyek.pencarian_barang.tambah']);
        $routes->post('pencarian-barang/simpan', 'Direktur\Proyek\PencarianController::simpan', ['as' => 'direktur.proyek.pencarian_barang.simpan']);
        $routes->get('pencarian-barang/detail/(:num)', 'Direktur\Proyek\PencarianController::detail/$1', ['as' => 'direktur.proyek.pencarian_barang.detail']);
        $routes->get('pencarian-barang/edit/(:num)', 'Direktur\Proyek\PencarianController::edit/$1', ['as' => 'direktur.proyek.pencarian_barang.edit']);
        $routes->post('pencarian-barang/update', 'Direktur\Proyek\PencarianController::update', ['as' => 'direktur.proyek.pencarian_barang.update']);
        $routes->post('pencarian-barang/delete/(:num)', 'Direktur\Proyek\PencarianController::delete/$1', ['as' => 'direktur.proyek.pencarian_barang.delete']);
        $routes->post('pencarian-barang/approve-keuangan/(:num)', 'Direktur\Proyek\PencarianController::approve_keuangan/$1', ['as' => 'direktur.proyek.pencarian_barang.approve_keuangan']);
    });
    
    // ============================================
    // KEUANGAN ROUTES
    // ============================================
    $routes->group('keuangan', function($routes) {
        // Penggajian Karyawan
        $routes->get('penggajian', 'Direktur\Keuangan\PenggajianController::index', ['as' => 'direktur.keuangan.penggajian']);
        $routes->get('penggajian/detail/(:num)', 'Direktur\Keuangan\PenggajianController::detail/$1', ['as' => 'direktur.keuangan.penggajian.detail']);
        $routes->get('penggajian/cetak-slip/(:num)', 'Direktur\Keuangan\PenggajianController::cetak_slip/$1', ['as' => 'direktur.keuangan.penggajian.cetak_slip']);
        $routes->get('penggajian/cetak', 'Direktur\Keuangan\PenggajianController::cetak', ['as' => 'direktur.keuangan.penggajian.cetak']);
        $routes->get('penggajian/export-excel', 'Direktur\Keuangan\PenggajianController::export_excel', ['as' => 'direktur.keuangan.penggajian.export_excel']);
        $routes->post('penggajian/generate', 'Direktur\Keuangan\PenggajianController::generate', ['as' => 'direktur.keuangan.penggajian.generate']);
        $routes->post('penggajian/simpan-detail', 'Direktur\Keuangan\PenggajianController::simpanDetail', ['as' => 'direktur.keuangan.penggajian.simpanDetail']);
        $routes->post('penggajian/bayar/(:num)', 'Direktur\Keuangan\PenggajianController::bayar/$1', ['as' => 'direktur.keuangan.penggajian.bayar']);
        $routes->post('penggajian/delete/(:num)', 'Direktur\Keuangan\PenggajianController::delete/$1', ['as' => 'direktur.keuangan.penggajian.delete']);
        
        // Kasbon
        $routes->get('kasbon', 'Direktur\Keuangan\KasbonController::index', ['as' => 'direktur.keuangan.kasbon']);
        $routes->get('kasbon/cetak', 'Direktur\Keuangan\KasbonController::cetak', ['as' => 'direktur.keuangan.kasbon.cetak']);
        $routes->get('kasbon/export-excel', 'Direktur\Keuangan\KasbonController::export_excel', ['as' => 'direktur.keuangan.kasbon.export_excel']);
        $routes->post('kasbon/approve', 'Direktur\Keuangan\KasbonController::approve', ['as' => 'direktur.keuangan.kasbon.approve']);
        $routes->post('kasbon/reject', 'Direktur\Keuangan\KasbonController::reject', ['as' => 'direktur.keuangan.kasbon.reject']);
        $routes->post('kasbon/simpan', 'Direktur\Keuangan\KasbonController::simpan', ['as' => 'direktur.keuangan.kasbon.simpan']);
        $routes->post('kasbon/delete/(:num)', 'Direktur\Keuangan\KasbonController::delete/$1', ['as' => 'direktur.keuangan.kasbon.delete']);
        
        // Pencatatan Pembelian (Purchase Requisition)
        $routes->get('pembelian', 'Direktur\Keuangan\PembelianController::index', ['as' => 'direktur.keuangan.pembelian']);
        $routes->get('pembelian/tambah', 'Direktur\Keuangan\PembelianController::tambah', ['as' => 'direktur.keuangan.pembelian.tambah']);
        $routes->get('pembelian/detail/(:num)', 'Direktur\Keuangan\PembelianController::detail/$1', ['as' => 'direktur.keuangan.pembelian.detail']);
        $routes->get('pembelian/edit/(:num)', 'Direktur\Keuangan\PembelianController::edit/$1', ['as' => 'direktur.keuangan.pembelian.edit']);
        $routes->post('pembelian/simpan', 'Direktur\Keuangan\PembelianController::simpan', ['as' => 'direktur.keuangan.pembelian.simpan']);
        $routes->post('pembelian/update', 'Direktur\Keuangan\PembelianController::update', ['as' => 'direktur.keuangan.pembelian.update']);
        $routes->post('pembelian/delete/(:num)', 'Direktur\Keuangan\PembelianController::delete/$1', ['as' => 'direktur.keuangan.pembelian.delete']);
        $routes->get('pembelian/cetak/(:num)', 'Direktur\Keuangan\PembelianController::cetak/$1', ['as' => 'direktur.keuangan.pembelian.cetak_single']);
        $routes->get('pembelian/cetak', 'Direktur\Keuangan\PembelianController::cetak', ['as' => 'direktur.keuangan.pembelian.cetak']);
        $routes->get('pembelian/export-excel', 'Direktur\Keuangan\PembelianController::export_excel', ['as' => 'direktur.keuangan.pembelian.export_excel']);
        $routes->post('pembelian/approve', 'Direktur\Keuangan\PembelianController::approve', ['as' => 'direktur.keuangan.pembelian.approve']);
        $routes->post('pembelian/reject', 'Direktur\Keuangan\PembelianController::reject', ['as' => 'direktur.keuangan.pembelian.reject']);
        $routes->add('pembelian/reset-data-lama', 'Direktur\Keuangan\PembelianController::resetDataLama', ['as' => 'direktur.keuangan.pembelian.resetDataLama']);

        
        // Laporan Keuangan
        $routes->get('laporan', 'Direktur\Keuangan\LaporanController::index', ['as' => 'direktur.keuangan.laporan']);
        $routes->get('laporan/cetak', 'Direktur\Keuangan\LaporanController::cetak', ['as' => 'direktur.keuangan.laporan.cetak']);
        $routes->get('laporan/export-excel', 'Direktur\Keuangan\LaporanController::export_excel', ['as' => 'direktur.keuangan.laporan.export_excel']);
    });
    
    // ============================================
    // PENGADAAN & ASET ROUTES
    // ============================================
    $routes->group('pengadaan', function($routes) {
        // Pengajuan ATK
        $routes->get('pengajuan-atk', 'Direktur\PengadaanController::pengajuan_atk', ['as' => 'direktur.pengadaan.pengajuan_atk']);
        $routes->get('pengajuan-atk/review/(:num)', 'Direktur\PengadaanController::review_atk/$1', ['as' => 'direktur.pengadaan.review_atk']);
        $routes->post('pengajuan-atk/simpan', 'Direktur\PengadaanController::simpan_atk', ['as' => 'direktur.pengadaan.simpan_atk']);
        $routes->post('pengajuan-atk/update', 'Direktur\PengadaanController::update_atk', ['as' => 'direktur.pengadaan.update_atk']);
        $routes->post('pengajuan-atk/delete/(:num)', 'Direktur\PengadaanController::delete_atk/$1', ['as' => 'direktur.pengadaan.delete_atk']);
        $routes->post('pengajuan-atk/approve', 'Direktur\PengadaanController::approve_atk', ['as' => 'direktur.pengadaan.approve_atk']);
        
        // Monitoring Stok ATK
        $routes->get('stok-atk', 'Direktur\PengadaanController::stok_atk', ['as' => 'direktur.pengadaan.stok_atk']);
        $routes->get('stok-atk/detail/(:num)', 'Direktur\PengadaanController::detail_stok_atk/$1', ['as' => 'direktur.pengadaan.detail_stok_atk']);
        $routes->post('stok-atk/simpan', 'Direktur\PengadaanController::simpan_stok_atk', ['as' => 'direktur.pengadaan.simpan_stok_atk']);
        $routes->post('stok-atk/update', 'Direktur\PengadaanController::update_stok_atk', ['as' => 'direktur.pengadaan.update_stok_atk']);
        $routes->post('stok-atk/delete/(:num)', 'Direktur\PengadaanController::delete_stok_atk/$1', ['as' => 'direktur.pengadaan.delete_stok_atk']);
        
        // Pengadaan Aset
        $routes->get('aset', 'Direktur\PengadaanController::aset', ['as' => 'direktur.pengadaan.aset']);
        $routes->get('aset/review/(:num)', 'Direktur\PengadaanController::review_aset/$1', ['as' => 'direktur.pengadaan.review_aset']);
        $routes->get('aset/cetak', 'Direktur\PengadaanController::cetak_aset', ['as' => 'direktur.pengadaan.cetak_aset']);
        $routes->post('aset/simpan', 'Direktur\PengadaanController::simpan_aset', ['as' => 'direktur.pengadaan.simpan_aset']);
        $routes->post('aset/update', 'Direktur\PengadaanController::update_aset', ['as' => 'direktur.pengadaan.update_aset']);
        $routes->post('aset/approve', 'Direktur\PengadaanController::approve_aset', ['as' => 'direktur.pengadaan.approve_aset']);
        $routes->post('aset/delete/(:num)', 'Direktur\PengadaanController::delete_aset/$1', ['as' => 'direktur.pengadaan.delete_aset']);
        
        // Kerusakan Alat
        $routes->get('kerusakan', 'Direktur\PengadaanController::kerusakan', ['as' => 'direktur.pengadaan.kerusakan']);
        $routes->get('kerusakan/tambah', 'Direktur\PengadaanController::tambah_kerusakan', ['as' => 'direktur.pengadaan.tambah_kerusakan']);
        $routes->get('kerusakan/edit/(:num)', 'Direktur\PengadaanController::edit_kerusakan/$1', ['as' => 'direktur.pengadaan.edit_kerusakan']);
        $routes->get('kerusakan/detail/(:num)', 'Direktur\PengadaanController::detail_kerusakan/$1', ['as' => 'direktur.pengadaan.detail_kerusakan']);
        $routes->post('kerusakan/simpan', 'Direktur\PengadaanController::simpan_kerusakan', ['as' => 'direktur.pengadaan.simpan_kerusakan']);
        $routes->post('kerusakan/update', 'Direktur\PengadaanController::update_kerusakan', ['as' => 'direktur.pengadaan.update_kerusakan']);
        $routes->post('kerusakan/delete/(:num)', 'Direktur\PengadaanController::delete_kerusakan/$1', ['as' => 'direktur.pengadaan.delete_kerusakan']);
        
        // Monitoring Gudang & Material
        $routes->get('gudang', 'Direktur\PengadaanController::gudang', ['as' => 'direktur.pengadaan.gudang']);
        $routes->get('gudang/tambah', 'Direktur\PengadaanController::tambah_gudang', ['as' => 'direktur.pengadaan.tambah_gudang']);
        $routes->get('gudang/edit/(:num)', 'Direktur\PengadaanController::edit_gudang/$1', ['as' => 'direktur.pengadaan.edit_gudang']);
        $routes->get('gudang/detail/(:num)', 'Direktur\PengadaanController::detail_gudang/$1', ['as' => 'direktur.pengadaan.detail_gudang']);
        $routes->post('gudang/simpan', 'Direktur\PengadaanController::simpan_gudang', ['as' => 'direktur.pengadaan.simpan_gudang']);
        $routes->post('gudang/update', 'Direktur\PengadaanController::update_gudang', ['as' => 'direktur.pengadaan.update_gudang']);
        $routes->post('gudang/delete/(:num)', 'Direktur\PengadaanController::delete_gudang/$1', ['as' => 'direktur.pengadaan.delete_gudang']);
        
    });
    
    // ============================================
    // DOKUMEN ROUTES
    // ============================================
    $routes->group('dokumen', function($routes) {
        // Dokumen Penting
        $routes->get('penting', 'Direktur\DokumenController::penting', ['as' => 'direktur.dokumen.penting']);
        $routes->get('penting/tambah', 'Direktur\DokumenController::tambah_penting', ['as' => 'direktur.dokumen.tambah_penting']);
        $routes->get('penting/edit/(:num)', 'Direktur\DokumenController::edit_penting/$1', ['as' => 'direktur.dokumen.edit_penting']);
        $routes->get('penting/detail/(:num)', 'Direktur\DokumenController::detail_penting/$1', ['as' => 'direktur.dokumen.detail_penting']);
        $routes->post('penting/simpan', 'Direktur\DokumenController::simpan_penting', ['as' => 'direktur.dokumen.simpan_penting']);
        $routes->post('penting/update', 'Direktur\DokumenController::update_penting', ['as' => 'direktur.dokumen.update_penting']);
        $routes->post('penting/delete/(:num)', 'Direktur\DokumenController::delete_penting/$1', ['as' => 'direktur.dokumen.delete_penting']);
        
        // Dokumen Sertifikat
        $routes->get('sertifikat', 'Direktur\DokumenController::sertifikat', ['as' => 'direktur.dokumen.sertifikat']);
        $routes->get('sertifikat/tambah', 'Direktur\DokumenController::tambah_sertifikat', ['as' => 'direktur.dokumen.tambah_sertifikat']);
        $routes->get('sertifikat/edit/(:num)', 'Direktur\DokumenController::edit_sertifikat/$1', ['as' => 'direktur.dokumen.edit_sertifikat']);
        $routes->get('sertifikat/detail/(:num)', 'Direktur\DokumenController::detail_sertifikat/$1', ['as' => 'direktur.dokumen.detail_sertifikat']);
        $routes->post('sertifikat/simpan', 'Direktur\DokumenController::simpan_sertifikat', ['as' => 'direktur.dokumen.simpan_sertifikat']);
        $routes->post('sertifikat/update', 'Direktur\DokumenController::update_sertifikat', ['as' => 'direktur.dokumen.update_sertifikat']);
        $routes->post('sertifikat/delete/(:num)', 'Direktur\DokumenController::delete_sertifikat/$1', ['as' => 'direktur.dokumen.delete_sertifikat']);
        
        // Kontak Project
        $routes->get('kontak', 'Direktur\DokumenController::kontak', ['as' => 'direktur.dokumen.kontak']);
        $routes->get('kontak/tambah', 'Direktur\DokumenController::tambah_kontak', ['as' => 'direktur.dokumen.tambah_kontak']);
        $routes->get('kontak/edit/(:num)', 'Direktur\DokumenController::edit_kontak/$1', ['as' => 'direktur.dokumen.edit_kontak']);
        $routes->get('kontak/detail/(:num)', 'Direktur\DokumenController::detail_kontak/$1', ['as' => 'direktur.dokumen.detail_kontak']);
        $routes->post('kontak/simpan', 'Direktur\DokumenController::simpan_kontak', ['as' => 'direktur.dokumen.simpan_kontak']);
        $routes->post('kontak/update', 'Direktur\DokumenController::update_kontak', ['as' => 'direktur.dokumen.update_kontak']);
        $routes->post('kontak/delete/(:num)', 'Direktur\DokumenController::delete_kontak/$1', ['as' => 'direktur.dokumen.delete_kontak']);
    });
    
    // ============================================
    // EXISTING ROUTES (untuk kompatibilitas)
    // ============================================
    $routes->get('reports', 'Direktur\Dashboard\Dashboard::reports', ['as' => 'direktur.reports']);
    $routes->get('financial', 'Direktur\Dashboard\Dashboard::financial', ['as' => 'direktur.financial']);
    $routes->get('performance', 'Direktur\Dashboard\Dashboard::performance', ['as' => 'direktur.performance']);
    
}); // END OF DIREKTUR GROUP