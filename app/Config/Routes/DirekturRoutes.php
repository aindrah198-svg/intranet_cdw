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
    // MONITORING ROUTES
    // ============================================
    $routes->group('monitoring', function($routes) {
        
        // Absensi
        $routes->get('absensi', 'Direktur\Monitoring\Absensi::index', ['as' => 'direktur.monitoring.absensi']);
        $routes->get('absensi/detail/(:num)', 'Direktur\Monitoring\Absensi::detail/$1', ['as' => 'direktur.monitoring.absensi.detail']);
        $routes->get('absensi/exportExcel', 'Direktur\Monitoring\Absensi::exportExcel', ['as' => 'direktur.monitoring.absensi.exportExcel']);
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
    // EXISTING ROUTES (untuk kompatibilitas)
    // ============================================
    $routes->get('reports', 'Direktur\Dashboard\Dashboard::reports', ['as' => 'direktur.reports']);
    $routes->get('financial', 'Direktur\Dashboard\Dashboard::financial', ['as' => 'direktur.financial']);
    $routes->get('performance', 'Direktur\Dashboard\Dashboard::performance', ['as' => 'direktur.performance']);
    
}); // END OF DIREKTUR GROUP