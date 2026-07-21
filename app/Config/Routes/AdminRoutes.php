<?php
// C:\xampp\htdocs\intranet_cdw\app\Config\Routes\AdminRoutes.php

/**
 * @var \CodeIgniter\Router\RouteCollection $routes
 * 
 * File ini berisi semua routes untuk Admin/HRD:
 * - Dashboard
 * - Profile Management
 * - Karyawan Management (dengan sub-modul Dokumen, Kontrak, Akun)
 * - Absensi Management
 * - Jam Kerja Management
 * - Cuti Management
 */

// ============================================
// ADMIN ROUTES (Dengan Filter Auth)
// ============================================

$routes->group('admin', ['filter' => 'auth'], function($routes) {
    
    // ============================================
    // DASHBOARD
    // ============================================
    $routes->get('/', 'Admin\Dashboard::index', ['as' => 'admin.dashboard']);
    $routes->get('dashboard', 'Admin\Dashboard::index');
    $routes->get('dashboard/stats', 'Admin\Dashboard::stats');
    $routes->get('dashboard/activities', 'Admin\Dashboard::activities');
    
    // ============================================
    // PROFILE MANAGEMENT
    // ============================================
    $routes->group('profile', ['namespace' => 'App\Controllers\Admin'], function($routes) {
        $routes->get('/', 'Profile::index', ['as' => 'admin.profile']);
        $routes->post('update', 'Profile::update', ['as' => 'admin.profile.update']);
        $routes->post('change-password', 'Profile::changePassword', ['as' => 'admin.profile.change_password']);
        $routes->post('update-photo', 'Profile::updatePhoto', ['as' => 'admin.profile.update_photo']);
        $routes->get('activity', 'Profile::activityLog', ['as' => 'admin.profile.activity']);
        $routes->get('download-cv', 'Profile::downloadCV', ['as' => 'admin.profile.download_cv']);
    });
    
    // ============================================
    // KARYAWAN MANAGEMENT
    // ============================================
    $routes->group('karyawan', ['namespace' => 'App\Controllers\Admin'], function($routes) {
        
        // Main Karyawan Routes
        $routes->get('/', 'Karyawan::index', ['as' => 'admin.karyawan']);
        $routes->get('aktif', 'Karyawan::aktif', ['as' => 'admin.karyawan.aktif']);
        $routes->get('nonaktif', 'Karyawan::nonaktif', ['as' => 'admin.karyawan.nonaktif']);
        $routes->get('keluar', 'Karyawan::keluar', ['as' => 'admin.karyawan.keluar']);
        $routes->get('search', 'Karyawan::search', ['as' => 'admin.karyawan.search']);
        
        // CRUD Operations
        $routes->get('create', 'Karyawan::create', ['as' => 'admin.karyawan.create']);
        $routes->post('store', 'Karyawan::store', ['as' => 'admin.karyawan.store']);
        $routes->get('show/(:num)', 'Karyawan::show/$1', ['as' => 'admin.karyawan.show']);
        $routes->get('edit/(:num)', 'Karyawan::edit/$1', ['as' => 'admin.karyawan.edit']);
        $routes->post('update/(:num)', 'Karyawan::update/$1', ['as' => 'admin.karyawan.update']);
        $routes->post('delete/(:num)', 'Karyawan::delete/$1', ['as' => 'admin.karyawan.delete']);
        $routes->post('restore/(:num)', 'Karyawan::restore/$1', ['as' => 'admin.karyawan.restore']);
        $routes->post('force-delete/(:num)', 'Karyawan::forceDelete/$1', ['as' => 'admin.karyawan.force_delete']);
        
        // Status Management
        $routes->post('update-status/(:num)', 'Karyawan::updateStatus/$1', ['as' => 'admin.karyawan.update_status']);
        $routes->post('update-keluar/(:num)', 'Karyawan::updateKeluar/$1', ['as' => 'admin.karyawan.update_keluar']);
        
        // Export & Import
        $routes->get('export', 'Karyawan::export', ['as' => 'admin.karyawan.export']);
        $routes->get('export/(:any)', 'Karyawan::export/$1', ['as' => 'admin.karyawan.export.type']);
        $routes->get('import', 'Karyawan::import', ['as' => 'admin.karyawan.import']);
        $routes->post('import/process', 'Karyawan::processImport', ['as' => 'admin.karyawan.import.process']);
        $routes->get('template', 'Karyawan::downloadTemplate', ['as' => 'admin.karyawan.template']);
        
        // AJAX Upload
        $routes->post('update-foto/(:num)', 'Karyawan::updateFoto/$1', ['as' => 'admin.karyawan.update_foto']);
        $routes->post('update-cv/(:num)', 'Karyawan::updateCV/$1', ['as' => 'admin.karyawan.update_cv']);
        
        // AJAX Data
        $routes->get('select2', 'Karyawan::getSelect2', ['as' => 'admin.karyawan.select2']);
        $routes->get('autocomplete', 'Karyawan::autocomplete', ['as' => 'admin.karyawan.autocomplete']);
        $routes->get('json/(:num)', 'Karyawan::getJson/$1', ['as' => 'admin.karyawan.json']);
        
        // ============================================
        // DOKUMEN (nested di bawah karyawan)
        // ============================================
        $routes->group('dokumen', function($routes) {
            $routes->get('/', 'Dokumen::index', ['as' => 'admin.karyawan.dokumen']);
            $routes->get('create', 'Dokumen::create', ['as' => 'admin.karyawan.dokumen.create']);
            $routes->post('store', 'Dokumen::store', ['as' => 'admin.karyawan.dokumen.store']);
            $routes->get('(:num)', 'Dokumen::byKaryawan/$1', ['as' => 'admin.karyawan.dokumen.show.karyawan']);
            $routes->get('show/(:num)', 'Dokumen::show/$1', ['as' => 'admin.karyawan.dokumen.show']);
            $routes->get('edit/(:num)', 'Dokumen::edit/$1', ['as' => 'admin.karyawan.dokumen.edit']);
            $routes->post('update/(:num)', 'Dokumen::update/$1', ['as' => 'admin.karyawan.dokumen.update']);
            $routes->post('delete/(:num)', 'Dokumen::delete/$1', ['as' => 'admin.karyawan.dokumen.delete']);
            $routes->get('download/(:num)', 'Dokumen::download/$1', ['as' => 'admin.karyawan.dokumen.download']);
            $routes->get('preview/(:num)', 'Dokumen::preview/$1', ['as' => 'admin.karyawan.dokumen.preview']);
            $routes->get('debug/(:num)', 'Dokumen::debug/$1', ['as' => 'admin.karyawan.dokumen.debug']);
            $routes->post('update-test/(:num)', 'Dokumen::updateTest/$1', ['as' => 'admin.karyawan.dokumen.update.test']);
            $routes->post('update-status/(:num)', 'Dokumen::updateStatus/$1', ['as' => 'admin.karyawan.dokumen.update.status']);
            $routes->post('update-status-ajax/(:num)', 'Dokumen::updateStatusAjax/$1', ['as' => 'admin.karyawan.dokumen.update.status.ajax']);
        });

        // ============================================
        // KONTRAK (nested di bawah karyawan)
        // ============================================
        $routes->group('kontrak', function($routes) {
            $routes->get('/', 'Kontrak::index', ['as' => 'admin.karyawan.kontrak']);
            $routes->get('create', 'Kontrak::create', ['as' => 'admin.karyawan.kontrak.create']);
            $routes->post('store', 'Kontrak::store', ['as' => 'admin.karyawan.kontrak.store']);
            $routes->get('show/(:num)', 'Kontrak::show/$1', ['as' => 'admin.karyawan.kontrak.show']);
            $routes->get('edit/(:num)', 'Kontrak::edit/$1', ['as' => 'admin.karyawan.kontrak.edit']);
            $routes->post('update/(:num)', 'Kontrak::update/$1', ['as' => 'admin.karyawan.kontrak.update']);
            $routes->post('delete/(:num)', 'Kontrak::delete/$1', ['as' => 'admin.karyawan.kontrak.delete']);
            $routes->get('download/(:num)', 'Kontrak::download/$1', ['as' => 'admin.karyawan.kontrak.download']);
            $routes->get('preview/(:num)', 'Kontrak::preview/$1', ['as' => 'admin.karyawan.kontrak.preview']);
            $routes->get('karyawan/(:num)', 'Kontrak::byKaryawan/$1', ['as' => 'admin.karyawan.kontrak.show.karyawan']);
            $routes->get('create-for/(:num)', 'Kontrak::createFor/$1', ['as' => 'admin.karyawan.kontrak.create.for']);
            $routes->post('update-status/(:num)', 'Kontrak::updateStatus/$1', ['as' => 'admin.karyawan.kontrak.update.status']);
            $routes->get('json/(:num)', 'Kontrak::getJson/$1', ['as' => 'admin.karyawan.kontrak.json']);
            $routes->get('select2', 'Kontrak::getSelect2', ['as' => 'admin.karyawan.kontrak.select2']);
            $routes->get('export', 'Kontrak::export', ['as' => 'admin.karyawan.kontrak.export']);
            $routes->get('import', 'Kontrak::import', ['as' => 'admin.karyawan.kontrak.import']);
            $routes->post('import/process', 'Kontrak::processImport', ['as' => 'admin.karyawan.kontrak.import.process']);
            $routes->get('print/(:num)', 'Kontrak::print/$1', ['as' => 'admin.karyawan.kontrak.print']);
            $routes->get('pdf/(:num)', 'Kontrak::pdf/$1', ['as' => 'admin.karyawan.kontrak.pdf']);
        });

        // ============================================
        // AKUN (nested di bawah karyawan)
        // ============================================
        $routes->group('akun', function($routes) {
            $routes->get('/', 'Akun::index');
            $routes->get('create', 'Akun::create');
            $routes->post('store', 'Akun::store');
            $routes->get('show/(:num)', 'Karyawan::show/$1', ['as' => 'admin.karyawan.show']);
            $routes->get('edit/(:num)', 'Akun::edit/$1');
            $routes->post('update/(:num)', 'Akun::update/$1');
            $routes->post('delete/(:num)', 'Akun::delete/$1', ['as' => 'admin.karyawan.akun.delete']);
            $routes->delete('delete/(:num)', 'Akun::delete/$1');
            $routes->get('check-username/(:any)', 'Akun::checkUsername/$1');
            $routes->get('check-email/(:any)', 'Akun::checkEmail/$1');
            $routes->post('reset-password/(:num)', 'Akun::resetPassword/$1', ['as' => 'admin.karyawan.akun.reset-password']);
            $routes->post('toggle-status/(:num)', 'Akun::toggleStatus/$1', ['as' => 'admin.karyawan.akun.toggle-status']);
        });

    }); // END OF KARYAWAN GROUP
    
    // ============================================
    // ABSENSI MANAGEMENT
    // ============================================
    $routes->group('absensi', ['namespace' => 'App\Controllers\Admin'], function($routes) {
        $routes->get('/', 'Absensi::index', ['as' => 'admin.absensi']);
        $routes->get('my-attendance', 'Absensi::myAttendance', ['as' => 'admin.absensi.my_attendance']);
        $routes->get('user', 'Absensi::myAttendance', ['as' => 'admin.absensi.user']);
        $routes->post('checkin', 'Absensi::checkin', ['as' => 'admin.absensi.checkin']);
        $routes->post('checkout', 'Absensi::checkout', ['as' => 'admin.absensi.checkout']);
        $routes->post('checkout/(:num)', 'Absensi::checkoutById/$1', ['as' => 'admin.absensi.checkout.by.id']);
        $routes->post('getLocationInfo', 'Absensi::getLocationInfo', ['as' => 'admin.absensi.get.location.info']);
        $routes->get('create', 'Absensi::create', ['as' => 'admin.absensi.create']);
        $routes->post('store', 'Absensi::store', ['as' => 'admin.absensi.store']);
        $routes->get('detail/(:num)', 'Absensi::detail/$1', ['as' => 'admin.absensi.detail']);
        $routes->get('edit/(:num)', 'Absensi::edit/$1', ['as' => 'admin.absensi.edit']);
        $routes->post('update/(:num)', 'Absensi::update/$1', ['as' => 'admin.absensi.update']);
        $routes->post('delete/(:num)', 'Absensi::delete/$1', ['as' => 'admin.absensi.delete']);
        $routes->post('checkout-manual/(:num)', 'Absensi::manualCheckout/$1', ['as' => 'admin.absensi.checkout.manual']);
        $routes->get('report', 'Absensi::report', ['as' => 'admin.absensi.report']);
        $routes->get('filter', 'Absensi::filter', ['as' => 'admin.absensi.filter']);
        $routes->get('search', 'Absensi::search', ['as' => 'admin.absensi.search']);
        $routes->get('by-karyawan/(:num)', 'Absensi::byKaryawan/$1', ['as' => 'admin.absensi.by.karyawan']);
        $routes->get('export', 'Absensi::export', ['as' => 'admin.absensi.export']);
        $routes->get('export/excel', 'Absensi::exportExcel', ['as' => 'admin.absensi.export.excel']);
        $routes->get('export/pdf', 'Absensi::exportPdf', ['as' => 'admin.absensi.export.pdf']);
        $routes->get('export/print', 'Absensi::print', ['as' => 'admin.absensi.export.print']);
        $routes->get('today', 'Absensi::today', ['as' => 'admin.absensi.today']);
        $routes->get('history', 'Absensi::history', ['as' => 'admin.absensi.history']);
        $routes->get('stats', 'Absensi::stats', ['as' => 'admin.absensi.stats']);
        $routes->get('karyawan-options', 'Absensi::getKaryawanOptions', ['as' => 'admin.absensi.karyawan.options']);
    });

    // ============================================
    // JAM KERJA MANAGEMENT
    // ============================================
    $routes->group('jam-kerja', ['namespace' => 'App\Controllers\Admin'], function($routes) {
        $routes->get('/', 'JamKerja::index', ['as' => 'admin.jam_kerja']);
        $routes->get('rekap', 'JamKerja::rekap', ['as' => 'admin.jam_kerja.rekap']);
        $routes->get('detail', 'JamKerja::detail', ['as' => 'admin.jam_kerja.detail']);
        $routes->get('detail/(:num)', 'JamKerja::detail/$1', ['as' => 'admin.jam_kerja.detail.id']);
        $routes->get('create', 'JamKerja::create', ['as' => 'admin.jam_kerja.create']);
        $routes->post('store', 'JamKerja::store', ['as' => 'admin.jam_kerja.store']);
        $routes->get('edit/(:num)', 'JamKerja::edit/$1', ['as' => 'admin.jam_kerja.edit']);
        $routes->post('update/(:num)', 'JamKerja::update/$1', ['as' => 'admin.jam_kerja.update']);
        $routes->post('delete/(:num)', 'JamKerja::delete/$1', ['as' => 'admin.jam_kerja.delete']);
        $routes->get('export', 'JamKerja::export', ['as' => 'admin.jam_kerja.export']);
        $routes->get('export/excel', 'JamKerja::exportExcel', ['as' => 'admin.jam_kerja.export.excel']);
        $routes->get('export/pdf', 'JamKerja::exportPdf', ['as' => 'admin.jam_kerja.export.pdf']);
        $routes->get('export/print', 'JamKerja::exportPrint', ['as' => 'admin.jam_kerja.export.print']);
        $routes->get('export/view', 'JamKerja::exportView', ['as' => 'admin.jam_kerja.export.view']);
        $routes->get('get-data', 'JamKerja::getData', ['as' => 'admin.jam_kerja.get_data']);
        $routes->get('get-rekap', 'JamKerja::getRekap', ['as' => 'admin.jam_kerja.get_rekap']);
        $routes->post('update-status/(:num)', 'JamKerja::updateStatus/$1', ['as' => 'admin.jam_kerja.update_status']);
        $routes->get('import', 'JamKerja::import', ['as' => 'admin.jam_kerja.import']);
        $routes->post('import/process', 'JamKerja::processImport', ['as' => 'admin.jam_kerja.import.process']);
        $routes->get('export/rekap', 'JamKerja::exportRekap', ['as' => 'admin.jam_kerja.export.rekap']);
        $routes->get('export/rekap/excel', 'JamKerja::exportRekap', ['as' => 'admin.jam_kerja.export.rekap.excel']);
    });

    // ============================================
    // CUTI MANAGEMENT
    // ============================================
    $routes->group('cuti', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
        $routes->get('/', 'Cuti::index', ['as' => 'admin.cuti']);
        $routes->get('create', 'Cuti::create', ['as' => 'admin.cuti.create']);
        $routes->post('store', 'Cuti::store', ['as' => 'admin.cuti.store']);
        $routes->get('show/(:num)', 'Cuti::show/$1', ['as' => 'admin.cuti.show']);
        $routes->get('edit/(:num)', 'Cuti::edit/$1', ['as' => 'admin.cuti.edit']);
        $routes->post('update/(:num)', 'Cuti::update/$1', ['as' => 'admin.cuti.update']);
        $routes->post('delete/(:num)', 'Cuti::delete/$1', ['as' => 'admin.cuti.delete']);
        $routes->post('approve/(:num)', 'Cuti::approve/$1', ['as' => 'admin.cuti.approve']);
        $routes->post('reject/(:num)', 'Cuti::reject/$1', ['as' => 'admin.cuti.reject']);
        $routes->get('pending', 'Cuti::pending', ['as' => 'admin.cuti.pending']);
        $routes->get('approved', 'Cuti::approved', ['as' => 'admin.cuti.approved']);
        $routes->get('rejected', 'Cuti::rejected', ['as' => 'admin.cuti.rejected']);
        $routes->get('my-cuti', 'Cuti::myCuti', ['as' => 'admin.cuti.my']);
        $routes->get('user', 'Cuti::userCuti', ['as' => 'admin.cuti.user']);
        $routes->get('calendar', 'Cuti::calendar', ['as' => 'admin.cuti.calendar']);
        $routes->get('export/excel', 'Cuti::exportExcel', ['as' => 'admin.cuti.export.excel']);
    });
    
}); // END OF ADMIN GROUP