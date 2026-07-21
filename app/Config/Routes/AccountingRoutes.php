<?php
// C:\xampp\htdocs\intranet_cdw\app\Config\Routes\AccountingRoutes.php

/**
 * @var \CodeIgniter\Router\RouteCollection $routes
 * 
 * File ini berisi semua routes untuk Accounting:
 * - Dashboard
 * - Kas & Bank (Mutasi Bank, Transfer Internal, Rekonsiliasi, Kas Kecil, Pengeluaran Pribadi)
 * - Pembukuan (Daftar Akun/COA, Jurnal Umum, Buku Besar, Neraca Saldo)
 * - Penggajian (Data Karyawan, Perhitungan Gaji, Proses Pembayaran, Slip Gaji, Komponen/Potongan Gaji)
 * - Aset Tetap (Register Aset, Kategori Aset, Penyusutan, Pelepasan Aset, Mutasi Aset)
 * - Manajemen Pajak (PPN, PPh Badan, Arsip Pajak, Tarif Pajak)
 * - Laporan Keuangan (Laba Rugi, Neraca, Arus Kas, Modal Pemilik)
 * - Menu Pribadi (Absensi, Profil, Riwayat Audit)
 */

// ============================================
// ACCOUNTING ROUTES (Dengan Filter Auth)
// ============================================

$routes->group('accounting', ['filter' => 'auth'], function($routes) {
    
    // ============================================
    // DASHBOARD
    // ============================================
    $routes->get('/', 'Accounting\Dashboard::index', ['as' => 'accounting']);
    $routes->get('dashboard', 'Accounting\Dashboard::index', ['as' => 'accounting.dashboard']);
    
    // ============================================
    // KAS & BANK
    // ============================================
    $routes->group('kas-bank', function($routes) {
        
        // ========== MUTASI BANK ==========
        $routes->get('mutasi-bank', 'Accounting\MutasiBank::index', ['as' => 'accounting.kas-bank.mutasi-bank']);
        $routes->get('mutasi-bank/create', 'Accounting\MutasiBank::create', ['as' => 'accounting.kas-bank.mutasi-bank.create']);
        $routes->post('mutasi-bank/store', 'Accounting\MutasiBank::store', ['as' => 'accounting.kas-bank.mutasi-bank.store']);
        $routes->get('mutasi-bank/detail/(:num)', 'Accounting\MutasiBank::detail/$1', ['as' => 'accounting.kas-bank.mutasi-bank.detail']);
        $routes->get('mutasi-bank/edit/(:num)', 'Accounting\MutasiBank::edit/$1', ['as' => 'accounting.kas-bank.mutasi-bank.edit']);
        $routes->post('mutasi-bank/update/(:num)', 'Accounting\MutasiBank::update/$1', ['as' => 'accounting.kas-bank.mutasi-bank.update']);
        $routes->post('mutasi-bank/delete/(:num)', 'Accounting\MutasiBank::delete/$1', ['as' => 'accounting.kas-bank.mutasi-bank.delete']);
        $routes->post('mutasi-bank/post/(:num)', 'Accounting\MutasiBank::post/$1', ['as' => 'accounting.kas-bank.mutasi-bank.post']);
        $routes->post('mutasi-bank/batalkan/(:num)', 'Accounting\MutasiBank::batalkan/$1', ['as' => 'accounting.kas-bank.mutasi-bank.batalkan']);
        $routes->get('mutasi-bank/export', 'Accounting\MutasiBank::export', ['as' => 'accounting.kas-bank.mutasi-bank.export']);
        $routes->get('mutasi-bank/print/(:num)', 'Accounting\MutasiBank::print/$1', ['as' => 'accounting.kas-bank.mutasi-bank.print']);
        $routes->get('mutasi-bank/export-pdf', 'Accounting\MutasiBank::exportPdf', ['as' => 'accounting.kas-bank.mutasi-bank.export-pdf']);
        $routes->get('mutasi-bank/ajax-get-coa', 'Accounting\MutasiBank::ajaxGetCoa', ['as' => 'accounting.kas-bank.mutasi-bank.ajax-get-coa']);
        $routes->get('mutasi-bank/ajax-get-saldo', 'Accounting\MutasiBank::ajaxGetSaldo', ['as' => 'accounting.kas-bank.mutasi-bank.ajax-get-saldo']);
        $routes->post('mutasi-bank/ajax-validate', 'Accounting\MutasiBank::ajaxValidate', ['as' => 'accounting.kas-bank.mutasi-bank.ajax-validate']);
        $routes->get('mutasi-bank/recalculate', 'Accounting\MutasiBank::recalculateSaldo', ['as' => 'accounting.kas-bank.mutasi-bank.recalculate']);
        
        // ========== TRANSFER INTERNAL ==========
        $routes->group('transfer-internal', function($routes) {
            $routes->get('/', 'Accounting\TransferInternal::index', ['as' => 'accounting.kas-bank.transfer-internal']);
            $routes->get('create', 'Accounting\TransferInternal::create', ['as' => 'accounting.kas-bank.transfer-internal.create']);
            $routes->post('store', 'Accounting\TransferInternal::store', ['as' => 'accounting.kas-bank.transfer-internal.store']);
            $routes->get('detail/(:num)', 'Accounting\TransferInternal::detail/$1', ['as' => 'accounting.kas-bank.transfer-internal.detail']);
            $routes->get('edit/(:num)', 'Accounting\TransferInternal::edit/$1', ['as' => 'accounting.kas-bank.transfer-internal.edit']);
            $routes->post('update/(:num)', 'Accounting\TransferInternal::update/$1', ['as' => 'accounting.kas-bank.transfer-internal.update']);
            $routes->post('delete/(:num)', 'Accounting\TransferInternal::delete/$1', ['as' => 'accounting.kas-bank.transfer-internal.delete']);
            $routes->post('post/(:num)', 'Accounting\TransferInternal::post/$1', ['as' => 'accounting.kas-bank.transfer-internal.post']);
            $routes->post('batalkan/(:num)', 'Accounting\TransferInternal::batalkan/$1', ['as' => 'accounting.kas-bank.transfer-internal.batalkan']);
            $routes->get('export', 'Accounting\TransferInternal::export', ['as' => 'accounting.kas-bank.transfer-internal.export']);
            $routes->get('export-excel', 'Accounting\TransferInternal::exportExcel', ['as' => 'accounting.kas-bank.transfer-internal.export-excel']);
            $routes->get('export-pdf', 'Accounting\TransferInternal::exportPdf', ['as' => 'accounting.kas-bank.transfer-internal.export-pdf']);
            $routes->get('print/(:num)', 'Accounting\TransferInternal::print/$1', ['as' => 'accounting.kas-bank.transfer-internal.print']);
            $routes->get('filter', 'Accounting\TransferInternal::filter', ['as' => 'accounting.kas-bank.transfer-internal.filter']);
            $routes->get('search', 'Accounting\TransferInternal::search', ['as' => 'accounting.kas-bank.transfer-internal.search']);
            $routes->get('draft', 'Accounting\TransferInternal::draft', ['as' => 'accounting.kas-bank.transfer-internal.draft']);
            $routes->get('posted', 'Accounting\TransferInternal::posted', ['as' => 'accounting.kas-bank.transfer-internal.posted']);
            $routes->get('dibatalkan', 'Accounting\TransferInternal::dibatalkan', ['as' => 'accounting.kas-bank.transfer-internal.dibatalkan']);
            $routes->get('ajax-get-coa', 'Accounting\TransferInternal::ajaxGetCoa', ['as' => 'accounting.kas-bank.transfer-internal.ajax-get-coa']);
            $routes->get('ajax-get-saldo-sumber', 'Accounting\TransferInternal::ajaxGetSaldoSumber', ['as' => 'accounting.kas-bank.transfer-internal.ajax-get-saldo-sumber']);
            $routes->post('ajax-validate', 'Accounting\TransferInternal::ajaxValidate', ['as' => 'accounting.kas-bank.transfer-internal.ajax-validate']);
            $routes->get('ajax-get-terbilang', 'Accounting\TransferInternal::ajaxGetTerbilang', ['as' => 'accounting.kas-bank.transfer-internal.ajax-get-terbilang']);
            $routes->get('ajax-get-rekening-info', 'Accounting\TransferInternal::ajaxGetRekeningInfo', ['as' => 'accounting.kas-bank.transfer-internal.ajax-get-rekening-info']);
            $routes->post('bulk-post', 'Accounting\TransferInternal::bulkPost', ['as' => 'accounting.kas-bank.transfer-internal.bulk-post']);
            $routes->post('bulk-delete', 'Accounting\TransferInternal::bulkDelete', ['as' => 'accounting.kas-bank.transfer-internal.bulk-delete']);
            $routes->get('report', 'Accounting\TransferInternal::report', ['as' => 'accounting.kas-bank.transfer-internal.report']);
        });
        
        // ========== REKONSILIASI ==========
        $routes->group('rekonsiliasi', function($routes) {
            $routes->get('/', 'Accounting\Rekonsiliasi::index', ['as' => 'accounting.kas-bank.rekonsiliasi']);
            $routes->get('create', 'Accounting\Rekonsiliasi::create', ['as' => 'accounting.kas-bank.rekonsiliasi.create']);
            $routes->post('store', 'Accounting\Rekonsiliasi::store', ['as' => 'accounting.kas-bank.rekonsiliasi.store']);
            $routes->get('detail/(:num)', 'Accounting\Rekonsiliasi::detail/$1', ['as' => 'accounting.kas-bank.rekonsiliasi.detail']);
            $routes->get('edit/(:num)', 'Accounting\Rekonsiliasi::edit/$1', ['as' => 'accounting.kas-bank.rekonsiliasi.edit']);
            $routes->post('update/(:num)', 'Accounting\Rekonsiliasi::update/$1', ['as' => 'accounting.kas-bank.rekonsiliasi.update']);
            $routes->post('delete/(:num)', 'Accounting\Rekonsiliasi::delete/$1', ['as' => 'accounting.kas-bank.rekonsiliasi.delete']);
            $routes->post('selesaikan/(:num)', 'Accounting\Rekonsiliasi::selesaikan/$1', ['as' => 'accounting.kas-bank.rekonsiliasi.selesaikan']);
            $routes->post('batalkan/(:num)', 'Accounting\Rekonsiliasi::batalkan/$1', ['as' => 'accounting.kas-bank.rekonsiliasi.batalkan']);
            $routes->get('export', 'Accounting\Rekonsiliasi::export', ['as' => 'accounting.kas-bank.rekonsiliasi.export']);
            $routes->get('export-excel', 'Accounting\Rekonsiliasi::exportExcel', ['as' => 'accounting.kas-bank.rekonsiliasi.export-excel']);
            $routes->get('export-pdf', 'Accounting\Rekonsiliasi::exportPdf', ['as' => 'accounting.kas-bank.rekonsiliasi.export-pdf']);
            $routes->get('print/(:num)', 'Accounting\Rekonsiliasi::print/$1', ['as' => 'accounting.kas-bank.rekonsiliasi.print']);
            $routes->get('filter', 'Accounting\Rekonsiliasi::filter', ['as' => 'accounting.kas-bank.rekonsiliasi.filter']);
            $routes->get('search', 'Accounting\Rekonsiliasi::search', ['as' => 'accounting.kas-bank.rekonsiliasi.search']);
            $routes->get('draft', 'Accounting\Rekonsiliasi::draft', ['as' => 'accounting.kas-bank.rekonsiliasi.draft']);
            $routes->get('selesai', 'Accounting\Rekonsiliasi::selesai', ['as' => 'accounting.kas-bank.rekonsiliasi.selesai']);
            $routes->get('dibatalkan', 'Accounting\Rekonsiliasi::dibatalkan', ['as' => 'accounting.kas-bank.rekonsiliasi.dibatalkan']);
            $routes->get('ajax-get-bank-accounts', 'Accounting\Rekonsiliasi::ajaxGetBankAccounts', ['as' => 'accounting.kas-bank.rekonsiliasi.ajax-get-bank-accounts']);
            $routes->get('ajax-get-saldo-bank/(:num)', 'Accounting\Rekonsiliasi::ajaxGetSaldoBank/$1', ['as' => 'accounting.kas-bank.rekonsiliasi.ajax-get-saldo-bank']);
            $routes->get('ajax-get-mutasi-belum-rekonsiliasi/(:num)', 'Accounting\Rekonsiliasi::ajaxGetMutasiBelumRekonsiliasi/$1', ['as' => 'accounting.kas-bank.rekonsiliasi.ajax-get-mutasi-belum-rekonsiliasi']);
            $routes->post('ajax-match-transaksi', 'Accounting\Rekonsiliasi::ajaxMatchTransaksi', ['as' => 'accounting.kas-bank.rekonsiliasi.ajax-match-transaksi']);
            $routes->post('ajax-unmatch-transaksi', 'Accounting\Rekonsiliasi::ajaxUnmatchTransaksi', ['as' => 'accounting.kas-bank.rekonsiliasi.ajax-unmatch-transaksi']);
            $routes->get('ajax-get-ringkasan', 'Accounting\Rekonsiliasi::ajaxGetRingkasan', ['as' => 'accounting.kas-bank.rekonsiliasi.ajax-get-ringkasan']);
            $routes->post('ajax-simpan-penyesuaian', 'Accounting\Rekonsiliasi::ajaxSimpanPenyesuaian', ['as' => 'accounting.kas-bank.rekonsiliasi.ajax-simpan-penyesuaian']);
            $routes->post('bulk-match', 'Accounting\Rekonsiliasi::bulkMatch', ['as' => 'accounting.kas-bank.rekonsiliasi.bulk-match']);
            $routes->post('bulk-unmatch', 'Accounting\Rekonsiliasi::bulkUnmatch', ['as' => 'accounting.kas-bank.rekonsiliasi.bulk-unmatch']);
            $routes->get('laporan-rekonsiliasi', 'Accounting\Rekonsiliasi::laporanRekonsiliasi', ['as' => 'accounting.kas-bank.rekonsiliasi.laporan']);
        });
        
        // ========== KAS KECIL ==========
        $routes->group('kas-kecil', function($routes) {
            $routes->get('/', 'Accounting\KasKecil::index', ['as' => 'accounting.kas-bank.kas-kecil']);
            $routes->get('create', 'Accounting\KasKecil::create', ['as' => 'accounting.kas-bank.kas-kecil.create']);
            $routes->post('store', 'Accounting\KasKecil::store', ['as' => 'accounting.kas-bank.kas-kecil.store']);
            $routes->get('detail/(:num)', 'Accounting\KasKecil::detail/$1', ['as' => 'accounting.kas-bank.kas-kecil.detail']);
            $routes->get('edit/(:num)', 'Accounting\KasKecil::edit/$1', ['as' => 'accounting.kas-bank.kas-kecil.edit']);
            $routes->post('update/(:num)', 'Accounting\KasKecil::update/$1', ['as' => 'accounting.kas-bank.kas-kecil.update']);
            $routes->post('delete/(:num)', 'Accounting\KasKecil::delete/$1', ['as' => 'accounting.kas-bank.kas-kecil.delete']);
            $routes->post('post/(:num)', 'Accounting\KasKecil::post/$1', ['as' => 'accounting.kas-bank.kas-kecil.post']);
            $routes->post('batalkan/(:num)', 'Accounting\KasKecil::batalkan/$1', ['as' => 'accounting.kas-bank.kas-kecil.batalkan']);
            $routes->get('export', 'Accounting\KasKecil::export', ['as' => 'accounting.kas-bank.kas-kecil.export']);
            $routes->get('export-excel', 'Accounting\KasKecil::exportExcel', ['as' => 'accounting.kas-bank.kas-kecil.export-excel']);
            $routes->get('export-pdf', 'Accounting\KasKecil::exportPdf', ['as' => 'accounting.kas-bank.kas-kecil.export-pdf']);
            $routes->get('print/(:num)', 'Accounting\KasKecil::print/$1', ['as' => 'accounting.kas-bank.kas-kecil.print']);
            $routes->get('filter', 'Accounting\KasKecil::filter', ['as' => 'accounting.kas-bank.kas-kecil.filter']);
            $routes->get('search', 'Accounting\KasKecil::search', ['as' => 'accounting.kas-bank.kas-kecil.search']);
            $routes->get('draft', 'Accounting\KasKecil::draft', ['as' => 'accounting.kas-bank.kas-kecil.draft']);
            $routes->get('posted', 'Accounting\KasKecil::posted', ['as' => 'accounting.kas-bank.kas-kecil.posted']);
            $routes->get('dibatalkan', 'Accounting\KasKecil::dibatalkan', ['as' => 'accounting.kas-bank.kas-kecil.dibatalkan']);
            $routes->get('buku-kas-kecil', 'Accounting\KasKecil::bukuKasKecil', ['as' => 'accounting.kas-bank.kas-kecil.buku-kas-kecil']);
            $routes->get('mutasi-kas-kecil', 'Accounting\KasKecil::mutasiKasKecil', ['as' => 'accounting.kas-bank.kas-kecil.mutasi']);
            $routes->get('pengisian-kembali', 'Accounting\KasKecil::pengisianKembali', ['as' => 'accounting.kas-bank.kas-kecil.pengisian-kembali']);
            $routes->post('proses-pengisian-kembali', 'Accounting\KasKecil::prosesPengisianKembali', ['as' => 'accounting.kas-bank.kas-kecil.proses-pengisian-kembali']);
            $routes->get('ajax-get-coa-lawan', 'Accounting\KasKecil::ajaxGetCoaLawan', ['as' => 'accounting.kas-bank.kas-kecil.ajax-get-coa-lawan']);
            $routes->get('ajax-get-karyawan', 'Accounting\KasKecil::ajaxGetKaryawan', ['as' => 'accounting.kas-bank.kas-kecil.ajax-get-karyawan']);
            $routes->get('ajax-get-spk', 'Accounting\KasKecil::ajaxGetSpk', ['as' => 'accounting.kas-bank.kas-kecil.ajax-get-spk']);
            $routes->get('ajax-get-saldo-kas-kecil', 'Accounting\KasKecil::ajaxGetSaldoKasKecil', ['as' => 'accounting.kas-bank.kas-kecil.ajax-get-saldo']);
            $routes->post('ajax-validate-saldo', 'Accounting\KasKecil::ajaxValidateSaldo', ['as' => 'accounting.kas-bank.kas-kecil.ajax-validate-saldo']);
            $routes->get('ajax-get-terbilang', 'Accounting\KasKecil::ajaxGetTerbilang', ['as' => 'accounting.kas-bank.kas-kecil.ajax-get-terbilang']);
            $routes->get('ajax-get-rekap-pengeluaran', 'Accounting\KasKecil::ajaxGetRekapPengeluaran', ['as' => 'accounting.kas-bank.kas-kecil.ajax-get-rekap-pengeluaran']);
            $routes->post('bulk-post', 'Accounting\KasKecil::bulkPost', ['as' => 'accounting.kas-bank.kas-kecil.bulk-post']);
            $routes->post('bulk-delete', 'Accounting\KasKecil::bulkDelete', ['as' => 'accounting.kas-bank.kas-kecil.bulk-delete']);
        });
        
        // ========== PENGELUARAN PRIBADI ==========
        $routes->group('pengeluaran-pribadi', function($routes) {
            $routes->get('/', 'Accounting\PengeluaranPribadi::index', ['as' => 'accounting.kas-bank.pengeluaran-pribadi']);
            $routes->get('create', 'Accounting\PengeluaranPribadi::create', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.create']);
            $routes->post('store', 'Accounting\PengeluaranPribadi::store', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.store']);
            $routes->get('detail/(:num)', 'Accounting\PengeluaranPribadi::detail/$1', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.detail']);
            $routes->get('edit/(:num)', 'Accounting\PengeluaranPribadi::edit/$1', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.edit']);
            $routes->post('update/(:num)', 'Accounting\PengeluaranPribadi::update/$1', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.update']);
            $routes->post('delete/(:num)', 'Accounting\PengeluaranPribadi::delete/$1', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.delete']);
            $routes->post('post/(:num)', 'Accounting\PengeluaranPribadi::post/$1', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.post']);
            $routes->post('batalkan/(:num)', 'Accounting\PengeluaranPribadi::batalkan/$1', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.batalkan']);
            $routes->get('export', 'Accounting\PengeluaranPribadi::export', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.export']);
            $routes->get('export-excel', 'Accounting\PengeluaranPribadi::exportExcel', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.export-excel']);
            $routes->get('export-pdf', 'Accounting\PengeluaranPribadi::exportPdf', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.export-pdf']);
            $routes->get('print/(:num)', 'Accounting\PengeluaranPribadi::print/$1', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.print']);
            $routes->get('filter', 'Accounting\PengeluaranPribadi::filter', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.filter']);
            $routes->get('search', 'Accounting\PengeluaranPribadi::search', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.search']);
            $routes->get('draft', 'Accounting\PengeluaranPribadi::draft', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.draft']);
            $routes->get('posted', 'Accounting\PengeluaranPribadi::posted', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.posted']);
            $routes->get('dibatalkan', 'Accounting\PengeluaranPribadi::dibatalkan', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.dibatalkan']);
            $routes->get('hutang-belum-dibayar', 'Accounting\PengeluaranPribadi::hutangBelumDibayar', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.hutang-belum-dibayar']);
            $routes->get('hutang-lunas', 'Accounting\PengeluaranPribadi::hutangLunas', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.hutang-lunas']);
            $routes->get('kasbon', 'Accounting\PengeluaranPribadi::kasbon', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.kasbon']);
            $routes->get('reimbursement', 'Accounting\PengeluaranPribadi::reimbursement', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.reimbursement']);
            $routes->get('prive', 'Accounting\PengeluaranPribadi::prive', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.prive']);
            $routes->get('proses-pelunasan/(:num)', 'Accounting\PengeluaranPribadi::prosesPelunasan/$1', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.proses-pelunasan']);
            $routes->post('lunasi/(:num)', 'Accounting\PengeluaranPribadi::lunasi/$1', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.lunasi']);
            $routes->post('lunasi-sebagian/(:num)', 'Accounting\PengeluaranPribadi::lunasiSebagian/$1', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.lunasi-sebagian']);
            $routes->get('ajax-get-karyawan', 'Accounting\PengeluaranPribadi::ajaxGetKaryawan', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.ajax-get-karyawan']);
            $routes->get('ajax-get-coa-debit', 'Accounting\PengeluaranPribadi::ajaxGetCoaDebit', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.ajax-get-coa-debit']);
            $routes->get('ajax-get-coa-kredit', 'Accounting\PengeluaranPribadi::ajaxGetCoaKredit', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.ajax-get-coa-kredit']);
            $routes->get('ajax-get-spk', 'Accounting\PengeluaranPribadi::ajaxGetSpk', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.ajax-get-spk']);
            $routes->get('ajax-get-terbilang', 'Accounting\PengeluaranPribadi::ajaxGetTerbilang', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.ajax-get-terbilang']);
            $routes->get('ajax-get-data-pengeluaran/(:num)', 'Accounting\PengeluaranPribadi::ajaxGetDataPengeluaran/$1', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.ajax-get-data-pengeluaran']);
            $routes->post('ajax-validate-pelunasan', 'Accounting\PengeluaranPribadi::ajaxValidatePelunasan', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.ajax-validate-pelunasan']);
            $routes->get('ajax-get-ringkasan-hutang/(:num)', 'Accounting\PengeluaranPribadi::ajaxGetRingkasanHutang/$1', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.ajax-get-ringkasan-hutang']);
            $routes->get('laporan-hutang-karyawan', 'Accounting\PengeluaranPribadi::laporanHutangKaryawan', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.laporan-hutang-karyawan']);
            $routes->get('laporan-rekap-per-karyawan', 'Accounting\PengeluaranPribadi::laporanRekapPerKaryawan', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.laporan-rekap-per-karyawan']);
            $routes->post('bulk-post', 'Accounting\PengeluaranPribadi::bulkPost', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.bulk-post']);
            $routes->post('bulk-delete', 'Accounting\PengeluaranPribadi::bulkDelete', ['as' => 'accounting.kas-bank.pengeluaran-pribadi.bulk-delete']);
        });
    });
    
    // ============================================
    // PEMBUKUAN
    // ============================================
    $routes->group('pembukuan', function($routes) {
        
        // ========== DAFTAR AKUN (COA) ==========
        $routes->group('daftar-akun', function($routes) {
            $routes->get('/', 'Accounting\Coa::index', ['as' => 'accounting.coa']);
            $routes->get('create', 'Accounting\Coa::create', ['as' => 'accounting.coa.create']);
            $routes->post('store', 'Accounting\Coa::store', ['as' => 'accounting.coa.store']);
            $routes->get('detail/(:num)', 'Accounting\Coa::detail/$1', ['as' => 'accounting.coa.detail']);
            $routes->get('edit/(:num)', 'Accounting\Coa::edit/$1', ['as' => 'accounting.coa.edit']);
            $routes->post('update/(:num)', 'Accounting\Coa::update/$1', ['as' => 'accounting.coa.update']);
            $routes->post('delete/(:num)', 'Accounting\Coa::delete/$1', ['as' => 'accounting.coa.delete']);
            $routes->get('tree', 'Accounting\Coa::tree', ['as' => 'accounting.coa.tree']);
            $routes->get('export', 'Accounting\Coa::export', ['as' => 'accounting.coa.export']);
            $routes->get('print', 'Accounting\Coa::print', ['as' => 'accounting.coa.print']);
            $routes->post('toggle-status/(:num)', 'Accounting\Coa::toggleStatus/$1', ['as' => 'accounting.coa.toggle.status']);
            $routes->get('ajax-get-tree-data', 'Accounting\Coa::ajaxGetTreeData', ['as' => 'accounting.coa.ajax.tree']);
            $routes->get('ajax-get-parent-info', 'Accounting\Coa::ajaxGetParentInfo', ['as' => 'accounting.coa.ajax.parent.info']);
            $routes->get('ajax-validate-kode', 'Accounting\Coa::ajaxValidateKode', ['as' => 'accounting.coa.ajax.validate.kode']);
            $routes->post('ajax-quick-add', 'Accounting\Coa::ajaxQuickAdd', ['as' => 'accounting.coa.ajax.quick.add']);
        });
        
        // ========== JURNAL UMUM ==========
        $routes->group('jurnal-umum', function($routes) {
            $routes->get('/', 'Accounting\JurnalUmum::index', ['as' => 'accounting.jurnal.umum']);
            $routes->get('create', 'Accounting\JurnalUmum::create', ['as' => 'accounting.jurnal.umum.create']);
            $routes->post('store', 'Accounting\JurnalUmum::store', ['as' => 'accounting.jurnal.umum.store']);
            $routes->get('detail/(:num)', 'Accounting\JurnalUmum::detail/$1', ['as' => 'accounting.jurnal.umum.detail']);
            $routes->get('edit/(:num)', 'Accounting\JurnalUmum::edit/$1', ['as' => 'accounting.jurnal.umum.edit']);
            $routes->post('update/(:num)', 'Accounting\JurnalUmum::update/$1', ['as' => 'accounting.jurnal.umum.update']);
            $routes->post('delete/(:num)', 'Accounting\JurnalUmum::delete/$1', ['as' => 'accounting.jurnal.umum.delete']);
            $routes->post('post/(:num)', 'Accounting\JurnalUmum::post/$1', ['as' => 'accounting.jurnal.umum.post']);
            $routes->post('void/(:num)', 'Accounting\JurnalUmum::void/$1', ['as' => 'accounting.jurnal.umum.void']);
            $routes->post('post-to-buku-besar/(:num)', 'Accounting\JurnalUmum::postToBukuBesar/$1', ['as' => 'accounting.jurnal.umum.post.to.buku.besar']);
            $routes->get('ajax-get-coa', 'Accounting\JurnalUmum::ajaxGetCoa', ['as' => 'accounting.jurnal.umum.ajax.coa']);
            $routes->post('ajax-validate-balance', 'Accounting\JurnalUmum::ajaxValidateBalance', ['as' => 'accounting.jurnal.umum.ajax.validate.balance']);
        });
        
        // ========== BUKU BESAR ==========
        $routes->group('buku-besar', function($routes) {
            $routes->get('/', 'Accounting\BukuBesar::index', ['as' => 'accounting.buku.besar']);
            $routes->get('detail/(:num)', 'Accounting\BukuBesar::detail/$1', ['as' => 'accounting.buku.besar.detail']);
            $routes->get('neraca-saldo', 'Accounting\BukuBesar::neracaSaldo', ['as' => 'accounting.buku.besar.neraca.saldo']);
            $routes->get('jurnal-posted', 'Accounting\BukuBesar::jurnalPosted', ['as' => 'accounting.buku.besar.jurnal.posted']);
            $routes->get('jurnal-posted/detail/(:num)', 'Accounting\BukuBesar::jurnalPostedDetail/$1', ['as' => 'accounting.buku.besar.jurnal.posted.detail']);
            $routes->get('batch-detail/(:any)', 'Accounting\BukuBesar::batchDetail/$1', ['as' => 'accounting.buku.besar.batch.detail']);
            $routes->post('void-jurnal/(:num)', 'Accounting\BukuBesar::voidJurnal/$1', ['as' => 'accounting.buku.besar.void.jurnal']);
            $routes->get('export-jurnal-posted', 'Accounting\BukuBesar::exportJurnalPosted', ['as' => 'accounting.buku.besar.export.jurnal.posted']);
            $routes->get('export-batch/(:any)', 'Accounting\BukuBesar::exportBatch/$1', ['as' => 'accounting.buku.besar.export.batch']);
            $routes->get('export', 'Accounting\BukuBesar::export', ['as' => 'accounting.buku.besar.export']);
            $routes->get('print', 'Accounting\BukuBesar::print', ['as' => 'accounting.buku.besar.print']);
            $routes->get('export-neraca-saldo', 'Accounting\BukuBesar::exportNeracaSaldo', ['as' => 'accounting.buku.besar.export.neraca.saldo']);
            $routes->post('post-all', 'Accounting\BukuBesar::postAllJurnals', ['as' => 'accounting.buku.besar.post.all']);
            $routes->get('post-all', 'Accounting\BukuBesar::postAllJurnalsForm', ['as' => 'accounting.buku.besar.post.all.form']);
            $routes->post('recalculate-saldo', 'Accounting\BukuBesar::recalculateSaldo', ['as' => 'accounting.buku.besar.recalculate.saldo']);
            $routes->post('generate-monthly', 'Accounting\BukuBesar::generateMonthlySaldo', ['as' => 'accounting.buku.besar.generate.monthly']);
            $routes->post('rollback-batch/(:num)', 'Accounting\BukuBesar::rollbackBatch/$1', ['as' => 'accounting.buku.besar.rollback.batch']);
            $routes->get('ajax-get-pending-counts', 'Accounting\BukuBesar::ajaxGetPendingCounts', ['as' => 'accounting.buku.besar.ajax.pending.counts']);
            $routes->get('ajax-get-data', 'Accounting\BukuBesar::ajaxGetData', ['as' => 'accounting.buku.besar.ajax.data']);
            $routes->get('ajax-get-saldo', 'Accounting\BukuBesar::ajaxGetSaldo', ['as' => 'accounting.buku.besar.ajax.saldo']);
            $routes->get('ajax-get-neraca-saldo', 'Accounting\BukuBesar::ajaxGetNeracaSaldo', ['as' => 'accounting.buku.besar.ajax.neraca.saldo']);
            $routes->get('ajax-get-batch-history', 'Accounting\BukuBesar::ajaxGetBatchHistory', ['as' => 'accounting.buku.besar.ajax.batch.history']);
            $routes->get('ajax-get-available-periods', 'Accounting\BukuBesar::ajaxGetAvailablePeriods', ['as' => 'accounting.buku.besar.ajax.available.periods']);
            $routes->post('void/(:num)', 'Accounting\BukuBesar::voidEntry/$1', ['as' => 'accounting.buku.besar.void']);
        });
        
        // ========== NERACA SALDO ==========
        $routes->group('neraca-saldo', function($routes) {
            $routes->get('/', 'Accounting\NeracaSaldo::index', ['as' => 'accounting.neraca.saldo']);
            $routes->get('generate', 'Accounting\NeracaSaldo::generate', ['as' => 'accounting.neraca.saldo.generate']);
            $routes->get('export', 'Accounting\NeracaSaldo::export', ['as' => 'accounting.neraca.saldo.export']);
            $routes->get('print', 'Accounting\NeracaSaldo::print', ['as' => 'accounting.neraca.saldo.print']);
        });
    });
    
    // ============================================
    // PENGGAGIAN
    // ============================================
    $routes->group('penggajian', function($routes) {
        $routes->get('/', 'Accounting\Penggajian::index', ['as' => 'accounting.penggajian']);
        
        // Data Karyawan
        $routes->group('data-karyawan', function($routes) {
            $routes->get('/', 'Accounting\Penggajian::dataKaryawan', ['as' => 'accounting.penggajian.data-karyawan']);
            $routes->get('create', 'Accounting\Penggajian::createKaryawan', ['as' => 'accounting.penggajian.data-karyawan.create']);
            $routes->post('store', 'Accounting\Penggajian::storeKaryawan', ['as' => 'accounting.penggajian.data-karyawan.store']);
            $routes->get('edit/(:num)', 'Accounting\Penggajian::editKaryawan/$1', ['as' => 'accounting.penggajian.data-karyawan.edit']);
            $routes->post('update/(:num)', 'Accounting\Penggajian::updateKaryawan/$1', ['as' => 'accounting.penggajian.data-karyawan.update']);
            $routes->post('delete/(:num)', 'Accounting\Penggajian::deleteKaryawan/$1', ['as' => 'accounting.penggajian.data-karyawan.delete']);
            $routes->get('detail/(:num)', 'Accounting\Penggajian::detailKaryawan/$1', ['as' => 'accounting.penggajian.data-karyawan.detail']);
            $routes->get('export', 'Accounting\Penggajian::exportKaryawan', ['as' => 'accounting.penggajian.data-karyawan.export']);
            $routes->get('print', 'Accounting\Penggajian::printKaryawan', ['as' => 'accounting.penggajian.data-karyawan.print']);
            $routes->post('import', 'Accounting\Penggajian::importKaryawan', ['as' => 'accounting.penggajian.data-karyawan.import']);
            $routes->get('ajax-get-data', 'Accounting\Penggajian::ajaxGetKaryawan', ['as' => 'accounting.penggajian.data-karyawan.ajax.get']);
            $routes->get('ajax-get-detail/(:num)', 'Accounting\Penggajian::ajaxGetKaryawanDetail/$1', ['as' => 'accounting.penggajian.data-karyawan.ajax.detail']);
        });
        
        // Perhitungan Gaji
        $routes->group('perhitungan-gaji', function($routes) {
            $routes->get('/', 'Accounting\Penggajian::perhitunganGaji', ['as' => 'accounting.penggajian.perhitungan-gaji']);
            $routes->get('create', 'Accounting\Penggajian::createPerhitungan', ['as' => 'accounting.penggajian.perhitungan-gaji.create']);
            $routes->post('store', 'Accounting\Penggajian::storePerhitungan', ['as' => 'accounting.penggajian.perhitungan-gaji.store']);
            $routes->get('edit/(:num)', 'Accounting\Penggajian::editPerhitungan/$1', ['as' => 'accounting.penggajian.perhitungan-gaji.edit']);
            $routes->post('update/(:num)', 'Accounting\Penggajian::updatePerhitungan/$1', ['as' => 'accounting.penggajian.perhitungan-gaji.update']);
            $routes->post('delete/(:num)', 'Accounting\Penggajian::deletePerhitungan/$1', ['as' => 'accounting.penggajian.perhitungan-gaji.delete']);
            $routes->get('detail/(:num)', 'Accounting\Penggajian::detailPerhitungan/$1', ['as' => 'accounting.penggajian.perhitungan-gaji.detail']);
            $routes->post('bulk-generate', 'Accounting\Penggajian::bulkGenerate', ['as' => 'accounting.penggajian.perhitungan-gaji.bulk-generate']);
            $routes->post('bulk-post', 'Accounting\Penggajian::bulkPost', ['as' => 'accounting.penggajian.perhitungan-gaji.bulk-post']);
            $routes->post('bulk-delete', 'Accounting\Penggajian::bulkDeletePerhitungan', ['as' => 'accounting.penggajian.perhitungan-gaji.bulk-delete']);
            $routes->get('draft', 'Accounting\Penggajian::perhitunganDraft', ['as' => 'accounting.penggajian.perhitungan-gaji.draft']);
            $routes->get('posted', 'Accounting\Penggajian::perhitunganPosted', ['as' => 'accounting.penggajian.perhitungan-gaji.posted']);
            $routes->get('periode/(:any)', 'Accounting\Penggajian::perhitunganByPeriode/$1', ['as' => 'accounting.penggajian.perhitungan-gaji.periode']);
            $routes->get('export', 'Accounting\Penggajian::exportPerhitungan', ['as' => 'accounting.penggajian.perhitungan-gaji.export']);
            $routes->get('export-excel', 'Accounting\Penggajian::exportPerhitunganExcel', ['as' => 'accounting.penggajian.perhitungan-gaji.export-excel']);
            $routes->get('export-pdf', 'Accounting\Penggajian::exportPerhitunganPdf', ['as' => 'accounting.penggajian.perhitungan-gaji.export-pdf']);
            $routes->get('ajax-get-komponen-gaji/(:num)', 'Accounting\Penggajian::ajaxGetKomponenGaji/$1', ['as' => 'accounting.penggajian.perhitungan-gaji.ajax.get-komponen']);
            $routes->get('ajax-get-potongan/(:num)', 'Accounting\Penggajian::ajaxGetPotongan/$1', ['as' => 'accounting.penggajian.perhitungan-gaji.ajax.get-potongan']);
            $routes->get('ajax-hitung-gaji', 'Accounting\Penggajian::ajaxHitungGaji', ['as' => 'accounting.penggajian.perhitungan-gaji.ajax.hitung']);
            $routes->post('ajax-simpan-temp', 'Accounting\Penggajian::ajaxSimpanTemp', ['as' => 'accounting.penggajian.perhitungan-gaji.ajax.simpan-temp']);
        });
        
        // Proses Pembayaran
        $routes->group('proses-pembayaran', function($routes) {
            $routes->get('/', 'Accounting\Penggajian::prosesPembayaran', ['as' => 'accounting.penggajian.proses-pembayaran']);
            $routes->get('create', 'Accounting\Penggajian::createPembayaran', ['as' => 'accounting.penggajian.proses-pembayaran.create']);
            $routes->post('store', 'Accounting\Penggajian::storePembayaran', ['as' => 'accounting.penggajian.proses-pembayaran.store']);
            $routes->get('detail/(:num)', 'Accounting\Penggajian::detailPembayaran/$1', ['as' => 'accounting.penggajian.proses-pembayaran.detail']);
            $routes->get('edit/(:num)', 'Accounting\Penggajian::editPembayaran/$1', ['as' => 'accounting.penggajian.proses-pembayaran.edit']);
            $routes->post('update/(:num)', 'Accounting\Penggajian::updatePembayaran/$1', ['as' => 'accounting.penggajian.proses-pembayaran.update']);
            $routes->post('delete/(:num)', 'Accounting\Penggajian::deletePembayaran/$1', ['as' => 'accounting.penggajian.proses-pembayaran.delete']);
            $routes->post('process/(:num)', 'Accounting\Penggajian::processPembayaran/$1', ['as' => 'accounting.penggajian.proses-pembayaran.process']);
            $routes->post('cancel/(:num)', 'Accounting\Penggajian::cancelPembayaran/$1', ['as' => 'accounting.penggajian.proses-pembayaran.cancel']);
            $routes->post('approve/(:num)', 'Accounting\Penggajian::approvePembayaran/$1', ['as' => 'accounting.penggajian.proses-pembayaran.approve']);
            $routes->post('reject/(:num)', 'Accounting\Penggajian::rejectPembayaran/$1', ['as' => 'accounting.penggajian.proses-pembayaran.reject']);
            $routes->post('batch-process', 'Accounting\Penggajian::batchProcessPembayaran', ['as' => 'accounting.penggajian.proses-pembayaran.batch-process']);
            $routes->get('pending', 'Accounting\Penggajian::pembayaranPending', ['as' => 'accounting.penggajian.proses-pembayaran.pending']);
            $routes->get('approved', 'Accounting\Penggajian::pembayaranApproved', ['as' => 'accounting.penggajian.proses-pembayaran.approved']);
            $routes->get('processed', 'Accounting\Penggajian::pembayaranProcessed', ['as' => 'accounting.penggajian.proses-pembayaran.processed']);
            $routes->get('export', 'Accounting\Penggajian::exportPembayaran', ['as' => 'accounting.penggajian.proses-pembayaran.export']);
            $routes->get('export-excel', 'Accounting\Penggajian::exportPembayaranExcel', ['as' => 'accounting.penggajian.proses-pembayaran.export-excel']);
            $routes->get('export-pdf', 'Accounting\Penggajian::exportPembayaranPdf', ['as' => 'accounting.penggajian.proses-pembayaran.export-pdf']);
            $routes->get('print/(:num)', 'Accounting\Penggajian::printPembayaran/$1', ['as' => 'accounting.penggajian.proses-pembayaran.print']);
            $routes->get('ajax-get-perhitungan-tersedia', 'Accounting\Penggajian::ajaxGetPerhitunganTersedia', ['as' => 'accounting.penggajian.proses-pembayaran.ajax.get-perhitungan']);
            $routes->get('ajax-get-ringkasan-pembayaran', 'Accounting\Penggajian::ajaxGetRingkasanPembayaran', ['as' => 'accounting.penggajian.proses-pembayaran.ajax.get-ringkasan']);
            $routes->post('ajax-validate-budget', 'Accounting\Penggajian::ajaxValidateBudget', ['as' => 'accounting.penggajian.proses-pembayaran.ajax.validate-budget']);
        });
        
        // Slip Gaji & Laporan
        $routes->group('slip-gaji', function($routes) {
            $routes->get('/', 'Accounting\Penggajian::slipGajiLaporan', ['as' => 'accounting.penggajian.slip-gaji']);
            $routes->get('view/(:num)', 'Accounting\Penggajian::viewSlipGaji/$1', ['as' => 'accounting.penggajian.slip-gaji.view']);
            $routes->get('print/(:num)', 'Accounting\Penggajian::printSlipGaji/$1', ['as' => 'accounting.penggajian.slip-gaji.print']);
            $routes->get('pdf/(:num)', 'Accounting\Penggajian::pdfSlipGaji/$1', ['as' => 'accounting.penggajian.slip-gaji.pdf']);
            $routes->post('email/(:num)', 'Accounting\Penggajian::emailSlipGaji/$1', ['as' => 'accounting.penggajian.slip-gaji.email']);
            $routes->get('batch-print', 'Accounting\Penggajian::batchPrintSlipGaji', ['as' => 'accounting.penggajian.slip-gaji.batch-print']);
            $routes->post('batch-email', 'Accounting\Penggajian::batchEmailSlipGaji', ['as' => 'accounting.penggajian.slip-gaji.batch-email']);
            $routes->get('laporan', 'Accounting\Penggajian::laporanPenggajian', ['as' => 'accounting.penggajian.slip-gaji.laporan']);
            $routes->get('laporan-periode', 'Accounting\Penggajian::laporanPeriode', ['as' => 'accounting.penggajian.slip-gaji.laporan-periode']);
            $routes->get('laporan-karyawan/(:num)', 'Accounting\Penggajian::laporanKaryawan/$1', ['as' => 'accounting.penggajian.slip-gaji.laporan-karyawan']);
            $routes->get('rekap-gaji', 'Accounting\Penggajian::rekapGaji', ['as' => 'accounting.penggajian.slip-gaji.rekap-gaji']);
            $routes->get('export-excel', 'Accounting\Penggajian::exportSlipGajiExcel', ['as' => 'accounting.penggajian.slip-gaji.export-excel']);
            $routes->get('export-pdf', 'Accounting\Penggajian::exportSlipGajiPdf', ['as' => 'accounting.penggajian.slip-gaji.export-pdf']);
            $routes->get('ajax-get-slips-by-periode', 'Accounting\Penggajian::ajaxGetSlipsByPeriode', ['as' => 'accounting.penggajian.slip-gaji.ajax.get-by-periode']);
            $routes->get('ajax-get-summary-by-periode', 'Accounting\Penggajian::ajaxGetSummaryByPeriode', ['as' => 'accounting.penggajian.slip-gaji.ajax.summary']);
        });
        
        // Komponen Gaji (Settings)
        $routes->group('komponen-gaji', function($routes) {
            $routes->get('/', 'Accounting\Penggajian::komponenGaji', ['as' => 'accounting.penggajian.komponen-gaji']);
            $routes->get('create', 'Accounting\Penggajian::createKomponenGaji', ['as' => 'accounting.penggajian.komponen-gaji.create']);
            $routes->post('store', 'Accounting\Penggajian::storeKomponenGaji', ['as' => 'accounting.penggajian.komponen-gaji.store']);
            $routes->get('edit/(:num)', 'Accounting\Penggajian::editKomponenGaji/$1', ['as' => 'accounting.penggajian.komponen-gaji.edit']);
            $routes->post('update/(:num)', 'Accounting\Penggajian::updateKomponenGaji/$1', ['as' => 'accounting.penggajian.komponen-gaji.update']);
            $routes->post('delete/(:num)', 'Accounting\Penggajian::deleteKomponenGaji/$1', ['as' => 'accounting.penggajian.komponen-gaji.delete']);
            $routes->post('toggle-status/(:num)', 'Accounting\Penggajian::toggleKomponenStatus/$1', ['as' => 'accounting.penggajian.komponen-gaji.toggle-status']);
            $routes->get('ajax-get-data', 'Accounting\Penggajian::ajaxGetKomponenGajiData', ['as' => 'accounting.penggajian.komponen-gaji.ajax.get']);
        });
        
        // Potongan Gaji (Settings)
        $routes->group('potongan-gaji', function($routes) {
            $routes->get('/', 'Accounting\Penggajian::potonganGaji', ['as' => 'accounting.penggajian.potongan-gaji']);
            $routes->get('create', 'Accounting\Penggajian::createPotonganGaji', ['as' => 'accounting.penggajian.potongan-gaji.create']);
            $routes->post('store', 'Accounting\Penggajian::storePotonganGaji', ['as' => 'accounting.penggajian.potongan-gaji.store']);
            $routes->get('edit/(:num)', 'Accounting\Penggajian::editPotonganGaji/$1', ['as' => 'accounting.penggajian.potongan-gaji.edit']);
            $routes->post('update/(:num)', 'Accounting\Penggajian::updatePotonganGaji/$1', ['as' => 'accounting.penggajian.potongan-gaji.update']);
            $routes->post('delete/(:num)', 'Accounting\Penggajian::deletePotonganGaji/$1', ['as' => 'accounting.penggajian.potongan-gaji.delete']);
            $routes->post('toggle-status/(:num)', 'Accounting\Penggajian::togglePotonganStatus/$1', ['as' => 'accounting.penggajian.potongan-gaji.toggle-status']);
            $routes->get('ajax-get-data', 'Accounting\Penggajian::ajaxGetPotonganGajiData', ['as' => 'accounting.penggajian.potongan-gaji.ajax.get']);
        });
    });
    
    // ============================================
    // ASET TETAP
    // ============================================
    $routes->group('aset-tetap', function($routes) {
        $routes->get('/', 'Accounting\AsetTetap::index', ['as' => 'accounting.aset-tetap']);
        
        // Register Aset
        $routes->group('register-aset', function($routes) {
            $routes->get('/', 'Accounting\AsetTetap::registerAset', ['as' => 'accounting.aset-tetap.register-aset']);
            $routes->get('create', 'Accounting\AsetTetap::createAset', ['as' => 'accounting.aset-tetap.register-aset.create']);
            $routes->post('store', 'Accounting\AsetTetap::storeAset', ['as' => 'accounting.aset-tetap.register-aset.store']);
            $routes->get('detail/(:num)', 'Accounting\AsetTetap::detailAset/$1', ['as' => 'accounting.aset-tetap.register-aset.detail']);
            $routes->get('edit/(:num)', 'Accounting\AsetTetap::editAset/$1', ['as' => 'accounting.aset-tetap.register-aset.edit']);
            $routes->post('update/(:num)', 'Accounting\AsetTetap::updateAset/$1', ['as' => 'accounting.aset-tetap.register-aset.update']);
            $routes->post('delete/(:num)', 'Accounting\AsetTetap::deleteAset/$1', ['as' => 'accounting.aset-tetap.register-aset.delete']);
            $routes->post('restore/(:num)', 'Accounting\AsetTetap::restoreAset/$1', ['as' => 'accounting.aset-tetap.register-aset.restore']);
            $routes->get('aktif', 'Accounting\AsetTetap::asetAktif', ['as' => 'accounting.aset-tetap.register-aset.aktif']);
            $routes->get('nonaktif', 'Accounting\AsetTetap::asetNonaktif', ['as' => 'accounting.aset-tetap.register-aset.nonaktif']);
            $routes->get('dihapus', 'Accounting\AsetTetap::asetDihapus', ['as' => 'accounting.aset-tetap.register-aset.dihapus']);
            $routes->get('export', 'Accounting\AsetTetap::exportAset', ['as' => 'accounting.aset-tetap.register-aset.export']);
            $routes->get('export-excel', 'Accounting\AsetTetap::exportAsetExcel', ['as' => 'accounting.aset-tetap.register-aset.export-excel']);
            $routes->get('export-pdf', 'Accounting\AsetTetap::exportAsetPdf', ['as' => 'accounting.aset-tetap.register-aset.export-pdf']);
            $routes->get('print', 'Accounting\AsetTetap::printAset', ['as' => 'accounting.aset-tetap.register-aset.print']);
            $routes->get('import', 'Accounting\AsetTetap::importAset', ['as' => 'accounting.aset-tetap.register-aset.import']);
            $routes->post('import-process', 'Accounting\AsetTetap::processImportAset', ['as' => 'accounting.aset-tetap.register-aset.import-process']);
            $routes->get('barcode/(:num)', 'Accounting\AsetTetap::generateBarcode/$1', ['as' => 'accounting.aset-tetap.register-aset.barcode']);
            $routes->get('print-label/(:num)', 'Accounting\AsetTetap::printLabel/$1', ['as' => 'accounting.aset-tetap.register-aset.print-label']);
            $routes->post('batch-print-labels', 'Accounting\AsetTetap::batchPrintLabels', ['as' => 'accounting.aset-tetap.register-aset.batch-print-labels']);
            $routes->get('ajax-get-data', 'Accounting\AsetTetap::ajaxGetAsetData', ['as' => 'accounting.aset-tetap.register-aset.ajax.get']);
            $routes->get('ajax-get-detail/(:num)', 'Accounting\AsetTetap::ajaxGetAsetDetail/$1', ['as' => 'accounting.aset-tetap.register-aset.ajax.detail']);
            $routes->get('ajax-get-kategori', 'Accounting\AsetTetap::ajaxGetKategori', ['as' => 'accounting.aset-tetap.register-aset.ajax.kategori']);
            $routes->get('ajax-get-lokasi', 'Accounting\AsetTetap::ajaxGetLokasi', ['as' => 'accounting.aset-tetap.register-aset.ajax.lokasi']);
        });
        
        // Kategori Aset
        $routes->group('kategori-aset', function($routes) {
            $routes->get('/', 'Accounting\AsetTetap::kategoriAset', ['as' => 'accounting.aset-tetap.kategori-aset']);
            $routes->get('create', 'Accounting\AsetTetap::createKategori', ['as' => 'accounting.aset-tetap.kategori-aset.create']);
            $routes->post('store', 'Accounting\AsetTetap::storeKategori', ['as' => 'accounting.aset-tetap.kategori-aset.store']);
            $routes->get('edit/(:num)', 'Accounting\AsetTetap::editKategori/$1', ['as' => 'accounting.aset-tetap.kategori-aset.edit']);
            $routes->post('update/(:num)', 'Accounting\AsetTetap::updateKategori/$1', ['as' => 'accounting.aset-tetap.kategori-aset.update']);
            $routes->post('delete/(:num)', 'Accounting\AsetTetap::deleteKategori/$1', ['as' => 'accounting.aset-tetap.kategori-aset.delete']);
        });
        
        // Penyusutan
        $routes->group('penyusutan', function($routes) {
            $routes->get('/', 'Accounting\AsetTetap::penyusutan', ['as' => 'accounting.aset-tetap.penyusutan']);
            $routes->get('create', 'Accounting\AsetTetap::createPenyusutan', ['as' => 'accounting.aset-tetap.penyusutan.create']);
            $routes->post('store', 'Accounting\AsetTetap::storePenyusutan', ['as' => 'accounting.aset-tetap.penyusutan.store']);
            $routes->get('detail/(:num)', 'Accounting\AsetTetap::detailPenyusutan/$1', ['as' => 'accounting.aset-tetap.penyusutan.detail']);
            $routes->get('edit/(:num)', 'Accounting\AsetTetap::editPenyusutan/$1', ['as' => 'accounting.aset-tetap.penyusutan.edit']);
            $routes->post('update/(:num)', 'Accounting\AsetTetap::updatePenyusutan/$1', ['as' => 'accounting.aset-tetap.penyusutan.update']);
            $routes->post('delete/(:num)', 'Accounting\AsetTetap::deletePenyusutan/$1', ['as' => 'accounting.aset-tetap.penyusutan.delete']);
            $routes->post('generate', 'Accounting\AsetTetap::generatePenyusutan', ['as' => 'accounting.aset-tetap.penyusutan.generate']);
            $routes->post('generate-bulk', 'Accounting\AsetTetap::generateBulkPenyusutan', ['as' => 'accounting.aset-tetap.penyusutan.generate-bulk']);
            $routes->post('generate-periode', 'Accounting\AsetTetap::generatePenyusutanPeriode', ['as' => 'accounting.aset-tetap.penyusutan.generate-periode']);
            $routes->post('post/(:num)', 'Accounting\AsetTetap::postPenyusutan/$1', ['as' => 'accounting.aset-tetap.penyusutan.post']);
            $routes->post('bulk-post', 'Accounting\AsetTetap::bulkPostPenyusutan', ['as' => 'accounting.aset-tetap.penyusutan.bulk-post']);
            $routes->get('laporan', 'Accounting\AsetTetap::laporanPenyusutan', ['as' => 'accounting.aset-tetap.penyusutan.laporan']);
            $routes->get('laporan-aset/(:num)', 'Accounting\AsetTetap::laporanPenyusutanAset/$1', ['as' => 'accounting.aset-tetap.penyusutan.laporan-aset']);
            $routes->get('proyeksi', 'Accounting\AsetTetap::proyeksiPenyusutan', ['as' => 'accounting.aset-tetap.penyusutan.proyeksi']);
            $routes->get('export', 'Accounting\AsetTetap::exportPenyusutan', ['as' => 'accounting.aset-tetap.penyusutan.export']);
            $routes->get('export-excel', 'Accounting\AsetTetap::exportPenyusutanExcel', ['as' => 'accounting.aset-tetap.penyusutan.export-excel']);
            $routes->get('export-pdf', 'Accounting\AsetTetap::exportPenyusutanPdf', ['as' => 'accounting.aset-tetap.penyusutan.export-pdf']);
            $routes->get('ajax-get-aset-options', 'Accounting\AsetTetap::ajaxGetAsetOptions', ['as' => 'accounting.aset-tetap.penyusutan.ajax.get-aset']);
            $routes->get('ajax-hitung-penyusutan', 'Accounting\AsetTetap::ajaxHitungPenyusutan', ['as' => 'accounting.aset-tetap.penyusutan.ajax.hitung']);
            $routes->get('ajax-get-riwayat/(:num)', 'Accounting\AsetTetap::ajaxGetRiwayatPenyusutan/$1', ['as' => 'accounting.aset-tetap.penyusutan.ajax.riwayat']);
        });
        
        // Pelepasan Aset
        $routes->group('pelepasan-aset', function($routes) {
            $routes->get('/', 'Accounting\AsetTetap::pelepasanAset', ['as' => 'accounting.aset-tetap.pelepasan-aset']);
            $routes->get('create', 'Accounting\AsetTetap::createPelepasan', ['as' => 'accounting.aset-tetap.pelepasan-aset.create']);
            $routes->post('store', 'Accounting\AsetTetap::storePelepasan', ['as' => 'accounting.aset-tetap.pelepasan-aset.store']);
            $routes->get('detail/(:num)', 'Accounting\AsetTetap::detailPelepasan/$1', ['as' => 'accounting.aset-tetap.pelepasan-aset.detail']);
            $routes->get('edit/(:num)', 'Accounting\AsetTetap::editPelepasan/$1', ['as' => 'accounting.aset-tetap.pelepasan-aset.edit']);
            $routes->post('update/(:num)', 'Accounting\AsetTetap::updatePelepasan/$1', ['as' => 'accounting.aset-tetap.pelepasan-aset.update']);
            $routes->post('delete/(:num)', 'Accounting\AsetTetap::deletePelepasan/$1', ['as' => 'accounting.aset-tetap.pelepasan-aset.delete']);
            $routes->post('approve/(:num)', 'Accounting\AsetTetap::approvePelepasan/$1', ['as' => 'accounting.aset-tetap.pelepasan-aset.approve']);
            $routes->post('reject/(:num)', 'Accounting\AsetTetap::rejectPelepasan/$1', ['as' => 'accounting.aset-tetap.pelepasan-aset.reject']);
            $routes->get('pending', 'Accounting\AsetTetap::pelepasanPending', ['as' => 'accounting.aset-tetap.pelepasan-aset.pending']);
            $routes->get('approved', 'Accounting\AsetTetap::pelepasanApproved', ['as' => 'accounting.aset-tetap.pelepasan-aset.approved']);
            $routes->get('completed', 'Accounting\AsetTetap::pelepasanCompleted', ['as' => 'accounting.aset-tetap.pelepasan-aset.completed']);
            $routes->get('rejected', 'Accounting\AsetTetap::pelepasanRejected', ['as' => 'accounting.aset-tetap.pelepasan-aset.rejected']);
            $routes->get('export', 'Accounting\AsetTetap::exportPelepasan', ['as' => 'accounting.aset-tetap.pelepasan-aset.export']);
            $routes->get('export-excel', 'Accounting\AsetTetap::exportPelepasanExcel', ['as' => 'accounting.aset-tetap.pelepasan-aset.export-excel']);
            $routes->get('export-pdf', 'Accounting\AsetTetap::exportPelepasanPdf', ['as' => 'accounting.aset-tetap.pelepasan-aset.export-pdf']);
            $routes->get('ajax-get-aset-options', 'Accounting\AsetTetap::ajaxGetAsetForPelepasan', ['as' => 'accounting.aset-tetap.pelepasan-aset.ajax.get-aset']);
            $routes->get('ajax-get-aset-info/(:num)', 'Accounting\AsetTetap::ajaxGetAsetInfo/$1', ['as' => 'accounting.aset-tetap.pelepasan-aset.ajax.get-aset-info']);
            $routes->post('ajax-hitung-nilai-buku', 'Accounting\AsetTetap::ajaxHitungNilaiBuku', ['as' => 'accounting.aset-tetap.pelepasan-aset.ajax.hitung-nilai-buku']);
        });
        
        // Mutasi Aset
        $routes->group('mutasi-aset', function($routes) {
            $routes->get('/', 'Accounting\AsetTetap::mutasiAset', ['as' => 'accounting.aset-tetap.mutasi-aset']);
            $routes->get('create', 'Accounting\AsetTetap::createMutasi', ['as' => 'accounting.aset-tetap.mutasi-aset.create']);
            $routes->post('store', 'Accounting\AsetTetap::storeMutasi', ['as' => 'accounting.aset-tetap.mutasi-aset.store']);
            $routes->get('detail/(:num)', 'Accounting\AsetTetap::detailMutasi/$1', ['as' => 'accounting.aset-tetap.mutasi-aset.detail']);
            $routes->post('delete/(:num)', 'Accounting\AsetTetap::deleteMutasi/$1', ['as' => 'accounting.aset-tetap.mutasi-aset.delete']);
            $routes->get('ajax-get-aset-options', 'Accounting\AsetTetap::ajaxGetAsetForMutasi', ['as' => 'accounting.aset-tetap.mutasi-aset.ajax.get-aset']);
        });
    });
    
    // ============================================
    // MANAJEMEN PAJAK
    // ============================================
    $routes->group('manajemen-pajak', function($routes) {
        $routes->get('/', 'Accounting\ManajemenPajak::index', ['as' => 'accounting.manajemen-pajak']);
        
        // PPN
        $routes->group('ppn', function($routes) {
            $routes->get('/', 'Accounting\ManajemenPajak::ppn', ['as' => 'accounting.manajemen-pajak.ppn']);
            
            // Faktur Pajak
            $routes->group('faktur', function($routes) {
                $routes->get('/', 'Accounting\ManajemenPajak::fakturPajak', ['as' => 'accounting.manajemen-pajak.ppn.faktur']);
                $routes->get('create', 'Accounting\ManajemenPajak::createFaktur', ['as' => 'accounting.manajemen-pajak.ppn.faktur.create']);
                $routes->post('store', 'Accounting\ManajemenPajak::storeFaktur', ['as' => 'accounting.manajemen-pajak.ppn.faktur.store']);
                $routes->get('detail/(:num)', 'Accounting\ManajemenPajak::detailFaktur/$1', ['as' => 'accounting.manajemen-pajak.ppn.faktur.detail']);
                $routes->get('edit/(:num)', 'Accounting\ManajemenPajak::editFaktur/$1', ['as' => 'accounting.manajemen-pajak.ppn.faktur.edit']);
                $routes->post('update/(:num)', 'Accounting\ManajemenPajak::updateFaktur/$1', ['as' => 'accounting.manajemen-pajak.ppn.faktur.update']);
                $routes->post('delete/(:num)', 'Accounting\ManajemenPajak::deleteFaktur/$1', ['as' => 'accounting.manajemen-pajak.ppn.faktur.delete']);
                $routes->post('approve/(:num)', 'Accounting\ManajemenPajak::approveFaktur/$1', ['as' => 'accounting.manajemen-pajak.ppn.faktur.approve']);
                $routes->post('reject/(:num)', 'Accounting\ManajemenPajak::rejectFaktur/$1', ['as' => 'accounting.manajemen-pajak.ppn.faktur.reject']);
                $routes->get('export', 'Accounting\ManajemenPajak::exportFaktur', ['as' => 'accounting.manajemen-pajak.ppn.faktur.export']);
                $routes->get('export-excel', 'Accounting\ManajemenPajak::exportFakturExcel', ['as' => 'accounting.manajemen-pajak.ppn.faktur.export-excel']);
                $routes->get('export-pdf', 'Accounting\ManajemenPajak::exportFakturPdf', ['as' => 'accounting.manajemen-pajak.ppn.faktur.export-pdf']);
                $routes->get('print/(:num)', 'Accounting\ManajemenPajak::printFaktur/$1', ['as' => 'accounting.manajemen-pajak.ppn.faktur.print']);
                $routes->get('draft', 'Accounting\ManajemenPajak::fakturDraft', ['as' => 'accounting.manajemen-pajak.ppn.faktur.draft']);
                $routes->get('pending', 'Accounting\ManajemenPajak::fakturPending', ['as' => 'accounting.manajemen-pajak.ppn.faktur.pending']);
                $routes->get('approved', 'Accounting\ManajemenPajak::fakturApproved', ['as' => 'accounting.manajemen-pajak.ppn.faktur.approved']);
                $routes->get('rejected', 'Accounting\ManajemenPajak::fakturRejected', ['as' => 'accounting.manajemen-pajak.ppn.faktur.rejected']);
                $routes->get('ajax-get-data', 'Accounting\ManajemenPajak::ajaxGetFakturData', ['as' => 'accounting.manajemen-pajak.ppn.faktur.ajax.get']);
            });
            
            // PPN Masukan & Keluaran
            $routes->group('masukan', function($routes) {
                $routes->get('/', 'Accounting\ManajemenPajak::ppnMasukan', ['as' => 'accounting.manajemen-pajak.ppn.masukan']);
                $routes->get('create', 'Accounting\ManajemenPajak::createPpnMasukan', ['as' => 'accounting.manajemen-pajak.ppn.masukan.create']);
                $routes->post('store', 'Accounting\ManajemenPajak::storePpnMasukan', ['as' => 'accounting.manajemen-pajak.ppn.masukan.store']);
                $routes->get('edit/(:num)', 'Accounting\ManajemenPajak::editPpnMasukan/$1', ['as' => 'accounting.manajemen-pajak.ppn.masukan.edit']);
                $routes->post('update/(:num)', 'Accounting\ManajemenPajak::updatePpnMasukan/$1', ['as' => 'accounting.manajemen-pajak.ppn.masukan.update']);
                $routes->post('delete/(:num)', 'Accounting\ManajemenPajak::deletePpnMasukan/$1', ['as' => 'accounting.manajemen-pajak.ppn.masukan.delete']);
            });
            
            $routes->group('keluaran', function($routes) {
                $routes->get('/', 'Accounting\ManajemenPajak::ppnKeluaran', ['as' => 'accounting.manajemen-pajak.ppn.keluaran']);
                $routes->get('create', 'Accounting\ManajemenPajak::createPpnKeluaran', ['as' => 'accounting.manajemen-pajak.ppn.keluaran.create']);
                $routes->post('store', 'Accounting\ManajemenPajak::storePpnKeluaran', ['as' => 'accounting.manajemen-pajak.ppn.keluaran.store']);
                $routes->get('edit/(:num)', 'Accounting\ManajemenPajak::editPpnKeluaran/$1', ['as' => 'accounting.manajemen-pajak.ppn.keluaran.edit']);
                $routes->post('update/(:num)', 'Accounting\ManajemenPajak::updatePpnKeluaran/$1', ['as' => 'accounting.manajemen-pajak.ppn.keluaran.update']);
                $routes->post('delete/(:num)', 'Accounting\ManajemenPajak::deletePpnKeluaran/$1', ['as' => 'accounting.manajemen-pajak.ppn.keluaran.delete']);
            });
            
            // Laporan PPN
            $routes->group('laporan', function($routes) {
                $routes->get('/', 'Accounting\ManajemenPajak::laporanPpn', ['as' => 'accounting.manajemen-pajak.ppn.laporan']);
                $routes->get('bulanan', 'Accounting\ManajemenPajak::laporanPpnBulanan', ['as' => 'accounting.manajemen-pajak.ppn.laporan.bulanan']);
                $routes->get('tahunan', 'Accounting\ManajemenPajak::laporanPpnTahunan', ['as' => 'accounting.manajemen-pajak.ppn.laporan.tahunan']);
                $routes->get('export', 'Accounting\ManajemenPajak::exportLaporanPpn', ['as' => 'accounting.manajemen-pajak.ppn.laporan.export']);
                $routes->get('export-excel', 'Accounting\ManajemenPajak::exportLaporanPpnExcel', ['as' => 'accounting.manajemen-pajak.ppn.laporan.export-excel']);
                $routes->get('export-pdf', 'Accounting\ManajemenPajak::exportLaporanPpnPdf', ['as' => 'accounting.manajemen-pajak.ppn.laporan.export-pdf']);
            });
            
            // Setoran PPN
            $routes->group('setoran', function($routes) {
                $routes->get('/', 'Accounting\ManajemenPajak::setoranPpn', ['as' => 'accounting.manajemen-pajak.ppn.setoran']);
                $routes->get('create', 'Accounting\ManajemenPajak::createSetoranPpn', ['as' => 'accounting.manajemen-pajak.ppn.setoran.create']);
                $routes->post('store', 'Accounting\ManajemenPajak::storeSetoranPpn', ['as' => 'accounting.manajemen-pajak.ppn.setoran.store']);
                $routes->get('detail/(:num)', 'Accounting\ManajemenPajak::detailSetoranPpn/$1', ['as' => 'accounting.manajemen-pajak.ppn.setoran.detail']);
                $routes->post('delete/(:num)', 'Accounting\ManajemenPajak::deleteSetoranPpn/$1', ['as' => 'accounting.manajemen-pajak.ppn.setoran.delete']);
            });
        });
        
        // PPh Badan
        $routes->group('pph-badan', function($routes) {
            $routes->get('/', 'Accounting\ManajemenPajak::pphBadan', ['as' => 'accounting.manajemen-pajak.pph-badan']);
            
            // Perhitungan PPh
            $routes->group('perhitungan', function($routes) {
                $routes->get('/', 'Accounting\ManajemenPajak::perhitunganPph', ['as' => 'accounting.manajemen-pajak.pph-badan.perhitungan']);
                $routes->post('hitung', 'Accounting\ManajemenPajak::hitungPph', ['as' => 'accounting.manajemen-pajak.pph-badan.hitung']);
                $routes->get('detail/(:num)', 'Accounting\ManajemenPajak::detailPerhitungan/$1', ['as' => 'accounting.manajemen-pajak.pph-badan.detail']);
                $routes->post('save', 'Accounting\ManajemenPajak::savePerhitungan', ['as' => 'accounting.manajemen-pajak.pph-badan.save']);
                $routes->post('delete/(:num)', 'Accounting\ManajemenPajak::deletePerhitungan/$1', ['as' => 'accounting.manajemen-pajak.pph-badan.delete']);
            });
            
            // Setoran PPh
            $routes->group('setoran', function($routes) {
                $routes->get('/', 'Accounting\ManajemenPajak::setoranPph', ['as' => 'accounting.manajemen-pajak.pph-badan.setoran']);
                $routes->get('create', 'Accounting\ManajemenPajak::createSetoranPph', ['as' => 'accounting.manajemen-pajak.pph-badan.setoran.create']);
                $routes->post('store', 'Accounting\ManajemenPajak::storeSetoranPph', ['as' => 'accounting.manajemen-pajak.pph-badan.setoran.store']);
                $routes->get('detail/(:num)', 'Accounting\ManajemenPajak::detailSetoranPph/$1', ['as' => 'accounting.manajemen-pajak.pph-badan.setoran.detail']);
                $routes->post('delete/(:num)', 'Accounting\ManajemenPajak::deleteSetoranPph/$1', ['as' => 'accounting.manajemen-pajak.pph-badan.setoran.delete']);
            });
            
            // Laporan PPh
            $routes->group('laporan', function($routes) {
                $routes->get('/', 'Accounting\ManajemenPajak::laporanPph', ['as' => 'accounting.manajemen-pajak.pph-badan.laporan']);
                $routes->get('bulanan', 'Accounting\ManajemenPajak::laporanPphBulanan', ['as' => 'accounting.manajemen-pajak.pph-badan.laporan.bulanan']);
                $routes->get('tahunan', 'Accounting\ManajemenPajak::laporanPphTahunan', ['as' => 'accounting.manajemen-pajak.pph-badan.laporan.tahunan']);
                $routes->get('export', 'Accounting\ManajemenPajak::exportLaporanPph', ['as' => 'accounting.manajemen-pajak.pph-badan.laporan.export']);
                $routes->get('export-excel', 'Accounting\ManajemenPajak::exportLaporanPphExcel', ['as' => 'accounting.manajemen-pajak.pph-badan.laporan.export-excel']);
                $routes->get('export-pdf', 'Accounting\ManajemenPajak::exportLaporanPphPdf', ['as' => 'accounting.manajemen-pajak.pph-badan.laporan.export-pdf']);
            });
            
            // AJAX
            $routes->get('ajax-get-data-penghasilan', 'Accounting\ManajemenPajak::ajaxGetDataPenghasilan', ['as' => 'accounting.manajemen-pajak.pph-badan.ajax.penghasilan']);
            $routes->get('ajax-get-data-pajak', 'Accounting\ManajemenPajak::ajaxGetDataPajak', ['as' => 'accounting.manajemen-pajak.pph-badan.ajax.pajak']);
        });
        
        // Arsip Pajak
        $routes->group('arsip-pajak', function($routes) {
            $routes->get('/', 'Accounting\ManajemenPajak::arsipPajak', ['as' => 'accounting.manajemen-pajak.arsip-pajak']);
            $routes->get('upload', 'Accounting\ManajemenPajak::uploadArsip', ['as' => 'accounting.manajemen-pajak.arsip-pajak.upload']);
            $routes->post('store-upload', 'Accounting\ManajemenPajak::storeArsip', ['as' => 'accounting.manajemen-pajak.arsip-pajak.store']);
            $routes->get('download/(:num)', 'Accounting\ManajemenPajak::downloadArsip/$1', ['as' => 'accounting.manajemen-pajak.arsip-pajak.download']);
            $routes->post('delete/(:num)', 'Accounting\ManajemenPajak::deleteArsip/$1', ['as' => 'accounting.manajemen-pajak.arsip-pajak.delete']);
            
            // Kategori Arsip
            $routes->group('kategori', function($routes) {
                $routes->get('/', 'Accounting\ManajemenPajak::kategoriArsip', ['as' => 'accounting.manajemen-pajak.arsip-pajak.kategori']);
                $routes->post('store', 'Accounting\ManajemenPajak::storeKategoriArsip', ['as' => 'accounting.manajemen-pajak.arsip-pajak.kategori.store']);
                $routes->post('update/(:num)', 'Accounting\ManajemenPajak::updateKategoriArsip/$1', ['as' => 'accounting.manajemen-pajak.arsip-pajak.kategori.update']);
                $routes->post('delete/(:num)', 'Accounting\ManajemenPajak::deleteKategoriArsip/$1', ['as' => 'accounting.manajemen-pajak.arsip-pajak.kategori.delete']);
            });
            
            // Filter by type
            $routes->get('ppn', 'Accounting\ManajemenPajak::arsipPpn', ['as' => 'accounting.manajemen-pajak.arsip-pajak.ppn']);
            $routes->get('pph', 'Accounting\ManajemenPajak::arsipPph', ['as' => 'accounting.manajemen-pajak.arsip-pajak.pph']);
            $routes->get('lainnya', 'Accounting\ManajemenPajak::arsipLainnya', ['as' => 'accounting.manajemen-pajak.arsip-pajak.lainnya']);
            $routes->get('export', 'Accounting\ManajemenPajak::exportArsip', ['as' => 'accounting.manajemen-pajak.arsip-pajak.export']);
            $routes->get('ajax-get-data', 'Accounting\ManajemenPajak::ajaxGetArsipData', ['as' => 'accounting.manajemen-pajak.arsip-pajak.ajax.get']);
        });
        
        // Tarif Pajak (Settings)
        $routes->group('tarif', function($routes) {
            $routes->get('/', 'Accounting\ManajemenPajak::tarifPajak', ['as' => 'accounting.manajemen-pajak.tarif']);
            $routes->get('create', 'Accounting\ManajemenPajak::createTarif', ['as' => 'accounting.manajemen-pajak.tarif.create']);
            $routes->post('store', 'Accounting\ManajemenPajak::storeTarif', ['as' => 'accounting.manajemen-pajak.tarif.store']);
            $routes->get('edit/(:num)', 'Accounting\ManajemenPajak::editTarif/$1', ['as' => 'accounting.manajemen-pajak.tarif.edit']);
            $routes->post('update/(:num)', 'Accounting\ManajemenPajak::updateTarif/$1', ['as' => 'accounting.manajemen-pajak.tarif.update']);
            $routes->post('delete/(:num)', 'Accounting\ManajemenPajak::deleteTarif/$1', ['as' => 'accounting.manajemen-pajak.tarif.delete']);
        });
    });
    
    // ============================================
    // LAPORAN KEUANGAN
    // ============================================
    $routes->group('laporan-keuangan', function($routes) {
        $routes->get('/', 'Accounting\LaporanKeuangan::index', ['as' => 'accounting.laporan-keuangan']);
        
        $routes->group('laporan', function($routes) {
            $routes->get('laba-rugi', 'Accounting\LabaRugi::index', ['as' => 'accounting.laporan.laba-rugi']);
            $routes->get('neraca', 'Accounting\Neraca::index', ['as' => 'accounting.laporan.neraca']);
            $routes->get('arus-kas', 'Accounting\ArusKas::index', ['as' => 'accounting.laporan.arus-kas']);
            $routes->get('modal-pemilik', 'Accounting\ModalPemilik::index', ['as' => 'accounting.laporan.modal-pemilik']);
            
            // Export routes
            $routes->get('laba-rugi/export', 'Accounting\LabaRugi::export', ['as' => 'accounting.laporan.laba-rugi.export']);
            $routes->get('laba-rugi/print', 'Accounting\LabaRugi::print', ['as' => 'accounting.laporan.laba-rugi.print']);
            $routes->get('laba-rugi/export-pdf', 'Accounting\LabaRugi::exportPdf', ['as' => 'accounting.laporan.laba-rugi.export-pdf']);
            $routes->get('neraca/export-pdf', 'Accounting\Neraca::exportPdf', ['as' => 'accounting.laporan.neraca.export-pdf']);
            $routes->get('neraca/print', 'Accounting\Neraca::print', ['as' => 'accounting.laporan.neraca.print']);
            $routes->get('arus-kas/export', 'Accounting\ArusKas::export', ['as' => 'accounting.laporan.arus-kas.export']);
            $routes->get('arus-kas/print', 'Accounting\ArusKas::print', ['as' => 'accounting.laporan.arus-kas.print']);
            $routes->get('modal-pemilik/export', 'Accounting\ModalPemilik::export', ['as' => 'accounting.laporan.modal-pemilik.export']);
            $routes->get('modal-pemilik/print', 'Accounting\ModalPemilik::print', ['as' => 'accounting.laporan.modal-pemilik.print']);
            
            // AJAX Routes
            $routes->get('laba-rugi/ajax-get-summary', 'Accounting\LabaRugi::ajaxGetSummary', ['as' => 'accounting.laporan.laba-rugi.ajax-get-summary']);
            $routes->get('laba-rugi/ajax-get-detail', 'Accounting\LabaRugi::ajaxGetDetail', ['as' => 'accounting.laporan.laba-rugi.ajax-get-detail']);
            $routes->get('neraca/ajax-get-summary', 'Accounting\Neraca::ajaxGetSummary', ['as' => 'accounting.laporan.neraca.ajax-get-summary']);
            $routes->get('arus-kas/ajax-get-summary', 'Accounting\ArusKas::ajaxGetSummary', ['as' => 'accounting.laporan.arus-kas.ajax-get-summary']);
            $routes->get('arus-kas/ajax-get-detail', 'Accounting\ArusKas::ajaxGetDetail', ['as' => 'accounting.laporan.arus-kas.ajax-get-detail']);
            $routes->get('arus-kas/ajax-validate', 'Accounting\ArusKas::ajaxValidate', ['as' => 'accounting.laporan.arus-kas.ajax-validate']);
        });
    });
    
    // ============================================
    // MENU PRIBADI
    // ============================================
    $routes->group('pribadi', function($routes) {
        $routes->get('absensi', 'Accounting\Pribadi::absensi', ['as' => 'accounting.pribadi.absensi']);
        $routes->get('profil', 'Accounting\Pribadi::profil', ['as' => 'accounting.pribadi.profil']);
        $routes->get('riwayat-audit', 'Accounting\Pribadi::riwayatAudit', ['as' => 'accounting.pribadi.riwayat-audit']);
    });
    
    // ============================================
    // REDIRECT ROUTES LAMA (UNTUK COMPATIBILITY)
    // ============================================
    $routes->get('coa', function() {
        return redirect()->to(site_url('accounting/pembukuan/daftar-akun'));
    });
    
    $routes->get('jurnal', function() {
        return redirect()->to(site_url('accounting/pembukuan/jurnal-umum'));
    });
    
    $routes->get('laporan', function() {
        return redirect()->to(site_url('accounting/laporan-keuangan/laporan/laba-rugi'));
    });
    
    $routes->get('absensi', function() {
        return redirect()->to(site_url('accounting/pribadi/absensi'));
    });
    
    $routes->get('profile', function() {
        return redirect()->to(site_url('accounting/pribadi/profil'));
    });
    
}); // END OF ACCOUNTING GROUP