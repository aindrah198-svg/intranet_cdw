<?php
// app/Config/Routes/HrdRoutes.php

/**
 * @var \CodeIgniter\Router\RouteCollection $routes
 * 
 * File ini berisi semua routes untuk HRD (Human Resource Department):
 * - Dashboard
 * - Profile Management
 * - Karyawan Management (dengan sub-modul Dokumen, Kontrak, Akun)
 * - Absensi Management
 * - Jam Kerja Management
 * - Cuti Management
 */

// ============================================
// HRD ROUTES (Dengan Filter Auth)
// ============================================

$routes->group('hrd', ['filter' => 'auth', 'namespace' => 'App\Controllers\Hrd'], function($routes) {
    
    // ============================================
    // DASHBOARD
    // ============================================
    $routes->get('/', 'Dashboard::index', ['as' => 'hrd.dashboard']);
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('dashboard/stats', 'Dashboard::stats');
    $routes->get('dashboard/activities', 'Dashboard::activities');
    
    // ============================================
    // PROFILE MANAGEMENT
    // ============================================
    $routes->group('profile', function($routes) {
        $routes->get('/', 'Profile::index', ['as' => 'hrd.profile']);
        $routes->post('update', 'Profile::update', ['as' => 'hrd.profile.update']);
        $routes->post('change-password', 'Profile::changePassword', ['as' => 'hrd.profile.change_password']);
        $routes->post('update-photo', 'Profile::updatePhoto', ['as' => 'hrd.profile.update_photo']);
        $routes->get('activity', 'Profile::activityLog', ['as' => 'hrd.profile.activity']);
        $routes->get('download-cv', 'Profile::downloadCV', ['as' => 'hrd.profile.download_cv']);
    });
    
    // ============================================
    // KARYAWAN MANAGEMENT
    // ============================================
    $routes->group('karyawan', function($routes) {
        
        // Main Karyawan Routes
        $routes->get('/', 'Karyawan::index', ['as' => 'hrd.karyawan']);
        $routes->get('aktif', 'Karyawan::aktif', ['as' => 'hrd.karyawan.aktif']);
        $routes->get('nonaktif', 'Karyawan::nonaktif', ['as' => 'hrd.karyawan.nonaktif']);
        $routes->get('keluar', 'Karyawan::keluar', ['as' => 'hrd.karyawan.keluar']);
        $routes->get('search', 'Karyawan::search', ['as' => 'hrd.karyawan.search']);
        
        // CRUD Operations
        $routes->get('create', 'Karyawan::create', ['as' => 'hrd.karyawan.create']);
        $routes->post('store', 'Karyawan::store', ['as' => 'hrd.karyawan.store']);
        $routes->get('show/(:num)', 'Karyawan::show/$1', ['as' => 'hrd.karyawan.show']);
        $routes->get('edit/(:num)', 'Karyawan::edit/$1', ['as' => 'hrd.karyawan.edit']);
        $routes->post('update/(:num)', 'Karyawan::update/$1', ['as' => 'hrd.karyawan.update']);
        $routes->post('delete/(:num)', 'Karyawan::delete/$1', ['as' => 'hrd.karyawan.delete']);
        $routes->post('restore/(:num)', 'Karyawan::restore/$1', ['as' => 'hrd.karyawan.restore']);
        $routes->post('force-delete/(:num)', 'Karyawan::forceDelete/$1', ['as' => 'hrd.karyawan.force_delete']);
        
        // Status Management
        $routes->post('update-status/(:num)', 'Karyawan::updateStatus/$1', ['as' => 'hrd.karyawan.update_status']);
        $routes->post('update-keluar/(:num)', 'Karyawan::updateKeluar/$1', ['as' => 'hrd.karyawan.update_keluar']);
        
        // Export & Import
        $routes->get('export', 'Karyawan::export', ['as' => 'hrd.karyawan.export']);
        $routes->get('export/(:any)', 'Karyawan::export/$1', ['as' => 'hrd.karyawan.export.type']);
        $routes->get('import', 'Karyawan::import', ['as' => 'hrd.karyawan.import']);
        $routes->post('import/process', 'Karyawan::processImport', ['as' => 'hrd.karyawan.import.process']);
        $routes->get('template', 'Karyawan::downloadTemplate', ['as' => 'hrd.karyawan.template']);
        
        // AJAX Upload
        $routes->post('update-foto/(:num)', 'Karyawan::updateFoto/$1', ['as' => 'hrd.karyawan.update_foto']);
        $routes->post('update-cv/(:num)', 'Karyawan::updateCV/$1', ['as' => 'hrd.karyawan.update_cv']);
        
        // AJAX Data
        $routes->get('select2', 'Karyawan::getSelect2', ['as' => 'hrd.karyawan.select2']);
        $routes->get('autocomplete', 'Karyawan::autocomplete', ['as' => 'hrd.karyawan.autocomplete']);
        $routes->get('json/(:num)', 'Karyawan::getJson/$1', ['as' => 'hrd.karyawan.json']);
        
        // ============================================
        // DOKUMEN (nested di bawah karyawan)
        // ============================================
        $routes->group('dokumen', function($routes) {
            $routes->get('/', 'Dokumen::index', ['as' => 'hrd.karyawan.dokumen']);
            $routes->get('create', 'Dokumen::create', ['as' => 'hrd.karyawan.dokumen.create']);
            $routes->post('store', 'Dokumen::store', ['as' => 'hrd.karyawan.dokumen.store']);
            $routes->get('(:num)', 'Dokumen::byKaryawan/$1', ['as' => 'hrd.karyawan.dokumen.show.karyawan']);
            $routes->get('show/(:num)', 'Dokumen::show/$1', ['as' => 'hrd.karyawan.dokumen.show']);
            $routes->get('edit/(:num)', 'Dokumen::edit/$1', ['as' => 'hrd.karyawan.dokumen.edit']);
            $routes->post('update/(:num)', 'Dokumen::update/$1', ['as' => 'hrd.karyawan.dokumen.update']);
            $routes->post('delete/(:num)', 'Dokumen::delete/$1', ['as' => 'hrd.karyawan.dokumen.delete']);
            $routes->get('download/(:num)', 'Dokumen::download/$1', ['as' => 'hrd.karyawan.dokumen.download']);
            $routes->get('preview/(:num)', 'Dokumen::preview/$1', ['as' => 'hrd.karyawan.dokumen.preview']);
            $routes->get('debug/(:num)', 'Dokumen::debug/$1', ['as' => 'hrd.karyawan.dokumen.debug']);
            $routes->post('update-test/(:num)', 'Dokumen::updateTest/$1', ['as' => 'hrd.karyawan.dokumen.update.test']);
            $routes->post('update-status/(:num)', 'Dokumen::updateStatus/$1', ['as' => 'hrd.karyawan.dokumen.update.status']);
            $routes->post('update-status-ajax/(:num)', 'Dokumen::updateStatusAjax/$1', ['as' => 'hrd.karyawan.dokumen.update.status.ajax']);
        });

        // ============================================
        // KONTRAK (nested di bawah karyawan)
        // ============================================
        $routes->group('kontrak', function($routes) {
            $routes->get('/', 'Kontrak::index', ['as' => 'hrd.karyawan.kontrak']);
            $routes->get('create', 'Kontrak::create', ['as' => 'hrd.karyawan.kontrak.create']);
            $routes->post('store', 'Kontrak::store', ['as' => 'hrd.karyawan.kontrak.store']);
            $routes->get('show/(:num)', 'Kontrak::show/$1', ['as' => 'hrd.karyawan.kontrak.show']);
            $routes->get('edit/(:num)', 'Kontrak::edit/$1', ['as' => 'hrd.karyawan.kontrak.edit']);
            $routes->post('update/(:num)', 'Kontrak::update/$1', ['as' => 'hrd.karyawan.kontrak.update']);
            $routes->post('delete/(:num)', 'Kontrak::delete/$1', ['as' => 'hrd.karyawan.kontrak.delete']);
            $routes->get('download/(:num)', 'Kontrak::download/$1', ['as' => 'hrd.karyawan.kontrak.download']);
            $routes->get('preview/(:num)', 'Kontrak::preview/$1', ['as' => 'hrd.karyawan.kontrak.preview']);
            $routes->get('karyawan/(:num)', 'Kontrak::byKaryawan/$1', ['as' => 'hrd.karyawan.kontrak.show.karyawan']);
            $routes->get('create-for/(:num)', 'Kontrak::createFor/$1', ['as' => 'hrd.karyawan.kontrak.create.for']);
            $routes->post('update-status/(:num)', 'Kontrak::updateStatus/$1', ['as' => 'hrd.karyawan.kontrak.update.status']);
            $routes->get('json/(:num)', 'Kontrak::getJson/$1', ['as' => 'hrd.karyawan.kontrak.json']);
            $routes->get('select2', 'Kontrak::getSelect2', ['as' => 'hrd.karyawan.kontrak.select2']);
            $routes->get('export', 'Kontrak::export', ['as' => 'hrd.karyawan.kontrak.export']);
            $routes->get('import', 'Kontrak::import', ['as' => 'hrd.karyawan.kontrak.import']);
            $routes->post('import/process', 'Kontrak::processImport', ['as' => 'hrd.karyawan.kontrak.import.process']);
            $routes->get('print/(:num)', 'Kontrak::print/$1', ['as' => 'hrd.karyawan.kontrak.print']);
            $routes->get('pdf/(:num)', 'Kontrak::pdf/$1', ['as' => 'hrd.karyawan.kontrak.pdf']);
        });

        // ============================================
        // AKUN (nested di bawah karyawan)
        // ============================================
        $routes->group('akun', function($routes) {
            $routes->get('/', 'Akun::index');
            $routes->get('create', 'Akun::create');
            $routes->post('store', 'Akun::store');
            $routes->get('show/(:num)', 'Karyawan::show/$1');
            $routes->get('edit/(:num)', 'Akun::edit/$1');
            $routes->post('update/(:num)', 'Akun::update/$1');
            $routes->post('delete/(:num)', 'Akun::delete/$1', ['as' => 'hrd.karyawan.akun.delete']);
            $routes->delete('delete/(:num)', 'Akun::delete/$1');
            $routes->get('check-username/(:any)', 'Akun::checkUsername/$1');
            $routes->get('check-email/(:any)', 'Akun::checkEmail/$1');
            $routes->post('reset-password/(:num)', 'Akun::resetPassword/$1', ['as' => 'hrd.karyawan.akun.reset-password']);
            $routes->post('toggle-status/(:num)', 'Akun::toggleStatus/$1', ['as' => 'hrd.karyawan.akun.toggle-status']);
        });

    }); // END OF KARYAWAN GROUP
    
    // ============================================
    // ABSENSI MANAGEMENT
    // ============================================
    $routes->group('absensi', function($routes) {
        $routes->get('/', 'Absensi::index', ['as' => 'hrd.absensi']);
        $routes->get('my-attendance', 'Absensi::myAttendance', ['as' => 'hrd.absensi.my_attendance']);
        $routes->get('user', 'Absensi::myAttendance', ['as' => 'hrd.absensi.user']);
        $routes->post('checkin', 'Absensi::checkin', ['as' => 'hrd.absensi.checkin']);
        $routes->post('checkout', 'Absensi::checkout', ['as' => 'hrd.absensi.checkout']);
        $routes->post('checkout/(:num)', 'Absensi::checkoutById/$1', ['as' => 'hrd.absensi.checkout.by.id']);
        $routes->post('getLocationInfo', 'Absensi::getLocationInfo', ['as' => 'hrd.absensi.get.location.info']);
        $routes->get('create', 'Absensi::create', ['as' => 'hrd.absensi.create']);
        $routes->post('store', 'Absensi::store', ['as' => 'hrd.absensi.store']);
        $routes->get('detail/(:num)', 'Absensi::detail/$1', ['as' => 'hrd.absensi.detail']);
        $routes->get('edit/(:num)', 'Absensi::edit/$1', ['as' => 'hrd.absensi.edit']);
        $routes->post('update/(:num)', 'Absensi::update/$1', ['as' => 'hrd.absensi.update']);
        $routes->post('delete/(:num)', 'Absensi::delete/$1', ['as' => 'hrd.absensi.delete']);
        $routes->post('checkout-manual/(:num)', 'Absensi::manualCheckout/$1', ['as' => 'hrd.absensi.checkout.manual']);
        $routes->get('report', 'Absensi::report', ['as' => 'hrd.absensi.report']);
        $routes->get('filter', 'Absensi::filter', ['as' => 'hrd.absensi.filter']);
        $routes->get('search', 'Absensi::search', ['as' => 'hrd.absensi.search']);
        $routes->get('by-karyawan/(:num)', 'Absensi::byKaryawan/$1', ['as' => 'hrd.absensi.by.karyawan']);
        $routes->get('export', 'Absensi::export', ['as' => 'hrd.absensi.export']);
        $routes->get('export/excel', 'Absensi::exportExcel', ['as' => 'hrd.absensi.export.excel']);
        $routes->get('export/pdf', 'Absensi::exportPdf', ['as' => 'hrd.absensi.export.pdf']);
        $routes->get('export/print', 'Absensi::print', ['as' => 'hrd.absensi.export.print']);
        $routes->get('today', 'Absensi::today', ['as' => 'hrd.absensi.today']);
        $routes->get('history', 'Absensi::history', ['as' => 'hrd.absensi.history']);
        $routes->get('stats', 'Absensi::stats', ['as' => 'hrd.absensi.stats']);
        $routes->get('karyawan-options', 'Absensi::getKaryawanOptions', ['as' => 'hrd.absensi.karyawan.options']);
    });

    // ============================================
    // JAM KERJA MANAGEMENT
    // ============================================
    $routes->group('jam-kerja', function($routes) {
        $routes->get('/', 'JamKerja::index', ['as' => 'hrd.jam_kerja']);
        $routes->get('rekap', 'JamKerja::rekap', ['as' => 'hrd.jam_kerja.rekap']);
        $routes->get('detail', 'JamKerja::detail', ['as' => 'hrd.jam_kerja.detail']);
        $routes->get('detail/(:num)', 'JamKerja::detail/$1', ['as' => 'hrd.jam_kerja.detail.id']);
        $routes->get('create', 'JamKerja::create', ['as' => 'hrd.jam_kerja.create']);
        $routes->post('store', 'JamKerja::store', ['as' => 'hrd.jam_kerja.store']);
        $routes->get('edit/(:num)', 'JamKerja::edit/$1', ['as' => 'hrd.jam_kerja.edit']);
        $routes->post('update/(:num)', 'JamKerja::update/$1', ['as' => 'hrd.jam_kerja.update']);
        $routes->post('delete/(:num)', 'JamKerja::delete/$1', ['as' => 'hrd.jam_kerja.delete']);
        $routes->get('export', 'JamKerja::export', ['as' => 'hrd.jam_kerja.export']);
        $routes->get('export/excel', 'JamKerja::exportExcel', ['as' => 'hrd.jam_kerja.export.excel']);
        $routes->get('export/pdf', 'JamKerja::exportPdf', ['as' => 'hrd.jam_kerja.export.pdf']);
        $routes->get('export/print', 'JamKerja::exportPrint', ['as' => 'hrd.jam_kerja.export.print']);
        $routes->get('export/view', 'JamKerja::exportView', ['as' => 'hrd.jam_kerja.export.view']);
        $routes->get('get-data', 'JamKerja::getData', ['as' => 'hrd.jam_kerja.get_data']);
        $routes->get('get-rekap', 'JamKerja::getRekap', ['as' => 'hrd.jam_kerja.get_rekap']);
        $routes->post('update-status/(:num)', 'JamKerja::updateStatus/$1', ['as' => 'hrd.jam_kerja.update_status']);
        $routes->get('import', 'JamKerja::import', ['as' => 'hrd.jam_kerja.import']);
        $routes->post('import/process', 'JamKerja::processImport', ['as' => 'hrd.jam_kerja.import.process']);
        $routes->get('export/rekap', 'JamKerja::exportRekap', ['as' => 'hrd.jam_kerja.export.rekap']);
        $routes->get('export/rekap/excel', 'JamKerja::exportRekap', ['as' => 'hrd.jam_kerja.export.rekap.excel']);
    });

    // ============================================
    // CUTI MANAGEMENT
    // ============================================
    $routes->group('cuti', function ($routes) {
        $routes->get('/', 'Cuti::index', ['as' => 'hrd.cuti']);
        $routes->get('create', 'Cuti::create', ['as' => 'hrd.cuti.create']);
        $routes->post('store', 'Cuti::store', ['as' => 'hrd.cuti.store']);
        $routes->get('show/(:num)', 'Cuti::show/$1', ['as' => 'hrd.cuti.show']);
        $routes->get('edit/(:num)', 'Cuti::edit/$1', ['as' => 'hrd.cuti.edit']);
        $routes->post('update/(:num)', 'Cuti::update/$1', ['as' => 'hrd.cuti.update']);
        $routes->post('delete/(:num)', 'Cuti::delete/$1', ['as' => 'hrd.cuti.delete']);
        $routes->post('approve/(:num)', 'Cuti::approve/$1', ['as' => 'hrd.cuti.approve']);
        $routes->post('reject/(:num)', 'Cuti::reject/$1', ['as' => 'hrd.cuti.reject']);
        $routes->get('pending', 'Cuti::pending', ['as' => 'hrd.cuti.pending']);
        $routes->get('approved', 'Cuti::approved', ['as' => 'hrd.cuti.approved']);
        $routes->get('rejected', 'Cuti::rejected', ['as' => 'hrd.cuti.rejected']);
        $routes->get('my-cuti', 'Cuti::myCuti', ['as' => 'hrd.cuti.my']);
        $routes->get('user', 'Cuti::userCuti', ['as' => 'hrd.cuti.user']);
        $routes->get('calendar', 'Cuti::calendar', ['as' => 'hrd.cuti.calendar']);
        $routes->get('export/excel', 'Cuti::exportExcel', ['as' => 'hrd.cuti.export.excel']);
    });

    // ============================================
    // REKRUTMEN (Lowongan & Pelamar, Onboarding)
    // ============================================
    $routes->group('rekrutmen', function($routes) {
        $routes->get('/', 'Rekrutmen::pelamar');
        $routes->get('pelamar', 'Rekrutmen::pelamar');
        $routes->get('onboarding', 'Rekrutmen::onboarding');
        $routes->post('pelamar/store', 'Rekrutmen::storePelamar');
        $routes->post('onboarding/process/(:num)', 'Rekrutmen::processOnboarding/$1');
    });

    // ============================================
    // FORM PENGAJUAN KARYAWAN (Cuti, Izin, Dokumen)
    // ============================================
    $routes->group('form-pengajuan', function($routes) {
        $routes->get('/', 'FormPengajuan::cuti');
        $routes->get('cuti', 'FormPengajuan::cuti');
        $routes->post('cuti/store', 'FormPengajuan::storeCuti');
        $routes->get('izin', 'FormPengajuan::izin');
        $routes->post('izin/store', 'FormPengajuan::storeIzin');
        $routes->get('dokumen', 'FormPengajuan::dokumen');
        $routes->post('dokumen/store', 'FormPengajuan::storeDokumen');
    });

    // ============================================
    // FINANSIAL (Payroll, BPJS, Pajak PPh21)
    // ============================================
    $routes->group('finansial', function($routes) {
        $routes->get('/', 'Finansial::payroll');
        $routes->get('payroll', 'Finansial::payroll');
        $routes->get('bpjs', 'Finansial::bpjs');
        $routes->get('pajak', 'Finansial::pajak');
    });

    // ============================================
    // PERFORMA & KEAMANAN (KPI, Tinjauan, Audit Trail)
    // ============================================
    $routes->group('performa', function($routes) {
        $routes->get('/', 'Performa::kpi');
        $routes->get('kpi', 'Performa::kpi');
        $routes->get('tinjauan', 'Performa::tinjauan');
        $routes->get('audit-trail', 'Performa::auditTrail');
    });

    // ============================================
    // LAPORAN HARIAN
    // ============================================
    $routes->get('laporan-harian', 'LaporanHarian::index');

    // ============================================
    // KELUHAN KARYAWAN
    // ============================================
    $routes->get('keluhan', 'Keluhan::index');
    $routes->post('keluhan/tanggapi/(:num)', 'Keluhan::tanggapi/$1');

}); // END OF HRD GROUP
