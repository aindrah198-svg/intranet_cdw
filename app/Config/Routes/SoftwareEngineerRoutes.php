<?php
// app/Config/Routes/SoftwareEngineerRoutes.php

/**
 * @var \CodeIgniter\Router\RouteCollection $routes
 */

$routes->group('software-engineer', ['filter' => 'auth'], function($routes) {
    // Alias / Software Engineer Dashboard
    $routes->get('/', 'SoftwareEngineer\Dashboard::index', ['as' => 'se']);
    $routes->get('dashboard', 'SoftwareEngineer\Dashboard::index', ['as' => 'se.dashboard']);

    // 2. Development (Project Sedang Dikerjakan)
    $routes->group('development', function($routes) {
        $routes->get('/', 'SoftwareEngineer\Development::taskBoard', ['as' => 'se.development']);
        $routes->get('task-board', 'SoftwareEngineer\Development::taskBoard', ['as' => 'se.development.task_board']);
        $routes->post('task/store', 'SoftwareEngineer\Development::storeTask', ['as' => 'se.development.task.store']);
        $routes->post('task/update-status', 'SoftwareEngineer\Development::updateTaskStatus', ['as' => 'se.development.task.update_status']);
        $routes->post('task/delete/(:num)', 'SoftwareEngineer\Development::deleteTask/$1', ['as' => 'se.development.task.delete']);
        
        $routes->get('sprint', 'SoftwareEngineer\Development::sprint', ['as' => 'se.development.sprint']);
        $routes->get('info-client', 'SoftwareEngineer\Development::infoClient', ['as' => 'se.development.info_client']);
    });

    // 3. Manajemen Sistem (Inti)
    $routes->group('manajemen-sistem', function($routes) {
        $routes->get('/', 'SoftwareEngineer\ManajemenSistem::daftarSistem', ['as' => 'se.manajemen_sistem']);
        
        // Daftar Sistem / Website
        $routes->get('daftar-sistem', 'SoftwareEngineer\ManajemenSistem::daftarSistem', ['as' => 'se.manajemen_sistem.daftar_sistem']);
        $routes->post('daftar-sistem/store', 'SoftwareEngineer\ManajemenSistem::storeSistem', ['as' => 'se.manajemen_sistem.sistem.store']);
        $routes->post('daftar-sistem/update/(:num)', 'SoftwareEngineer\ManajemenSistem::updateSistem/$1', ['as' => 'se.manajemen_sistem.sistem.update']);
        $routes->post('daftar-sistem/delete/(:num)', 'SoftwareEngineer\ManajemenSistem::deleteSistem/$1', ['as' => 'se.manajemen_sistem.sistem.delete']);

        // Hosting & Domain
        $routes->get('hosting-domain', 'SoftwareEngineer\ManajemenSistem::hostingDomain', ['as' => 'se.manajemen_sistem.hosting_domain']);
        $routes->post('hosting-domain/store', 'SoftwareEngineer\ManajemenSistem::storeHostingDomain', ['as' => 'se.manajemen_sistem.hosting_domain.store']);
        $routes->post('hosting-domain/delete/(:num)', 'SoftwareEngineer\ManajemenSistem::deleteHostingDomain/$1', ['as' => 'se.manajemen_sistem.hosting_domain.delete']);

        // Kredensial Akses ⚠️ (Security Encrypted + Audit Log)
        $routes->get('kredensial-akses', 'SoftwareEngineer\ManajemenSistem::kredensialAkses', ['as' => 'se.manajemen_sistem.kredensial_akses']);
        $routes->post('kredensial-akses/store', 'SoftwareEngineer\ManajemenSistem::storeKredensial', ['as' => 'se.manajemen_sistem.kredensial.store']);
        $routes->post('kredensial-akses/reveal/(:num)', 'SoftwareEngineer\ManajemenSistem::revealCredential/$1', ['as' => 'se.manajemen_sistem.kredensial.reveal']);
        $routes->post('kredensial-akses/delete/(:num)', 'SoftwareEngineer\ManajemenSistem::deleteKredensial/$1', ['as' => 'se.manajemen_sistem.kredensial.delete']);
        $routes->get('kredensial-akses/audit-log', 'SoftwareEngineer\ManajemenSistem::auditLog', ['as' => 'se.manajemen_sistem.kredensial.audit_log']);

        // Riwayat Deploy
        $routes->get('riwayat-deploy', 'SoftwareEngineer\ManajemenSistem::riwayatDeploy', ['as' => 'se.manajemen_sistem.riwayat_deploy']);
        $routes->post('riwayat-deploy/store', 'SoftwareEngineer\ManajemenSistem::storeDeploy', ['as' => 'se.manajemen_sistem.deploy.store']);
    });

    // 4. Bug & Maintenance
    $routes->group('bug-maintenance', function($routes) {
        $routes->get('/', 'SoftwareEngineer\BugMaintenance::bugTracking', ['as' => 'se.bug_maintenance']);
        
        $routes->get('bug-tracking', 'SoftwareEngineer\BugMaintenance::bugTracking', ['as' => 'se.bug_maintenance.bug_tracking']);
        $routes->post('bug-tracking/store', 'SoftwareEngineer\BugMaintenance::storeBug', ['as' => 'se.bug_maintenance.bug.store']);
        $routes->post('bug-tracking/update-status/(:num)', 'SoftwareEngineer\BugMaintenance::updateBugStatus/$1', ['as' => 'se.bug_maintenance.bug.update_status']);
        
        $routes->get('maintenance-terjadwal', 'SoftwareEngineer\BugMaintenance::maintenanceTerjadwal', ['as' => 'se.bug_maintenance.maintenance_terjadwal']);
        $routes->post('maintenance-terjadwal/store', 'SoftwareEngineer\BugMaintenance::storeMaintenance', ['as' => 'se.bug_maintenance.maintenance.store']);
        
        $routes->get('backup-log', 'SoftwareEngineer\BugMaintenance::backupLog', ['as' => 'se.bug_maintenance.backup_log']);
        $routes->post('backup-log/store', 'SoftwareEngineer\BugMaintenance::storeBackup', ['as' => 'se.bug_maintenance.backup.store']);
    });

    // 5. Dokumentasi Teknis
    $routes->group('dokumentasi-teknis', function($routes) {
        $routes->get('/', 'SoftwareEngineer\DokumentasiTeknis::dokumentasi', ['as' => 'se.dokumentasi_teknis']);
        $routes->get('dokumentasi-sistem', 'SoftwareEngineer\DokumentasiTeknis::dokumentasi', ['as' => 'se.dokumentasi_teknis.sistem']);
        $routes->post('dokumentasi-sistem/store', 'SoftwareEngineer\DokumentasiTeknis::storeDoc', ['as' => 'se.dokumentasi_teknis.doc.store']);
        
        $routes->get('arsitektur-sistem', 'SoftwareEngineer\DokumentasiTeknis::arsitektur', ['as' => 'se.dokumentasi_teknis.arsitektur']);
        $routes->post('arsitektur-sistem/store', 'SoftwareEngineer\DokumentasiTeknis::storeArsitektur', ['as' => 'se.dokumentasi_teknis.arsitektur.store']);
    });

    // 6. Pengajuan
    $routes->group('pengajuan', function($routes) {
        $routes->get('/', 'SoftwareEngineer\Pengajuan::index', ['as' => 'se.pengajuan']);
        $routes->get('permintaan-alat', 'SoftwareEngineer\Pengajuan::permintaanAlat', ['as' => 'se.pengajuan.permintaan_alat']);
        $routes->post('permintaan-alat/store', 'SoftwareEngineer\Pengajuan::storePermintaanAlat', ['as' => 'se.pengajuan.permintaan_alat.store']);
        $routes->get('cuti', 'SoftwareEngineer\Pengajuan::cuti', ['as' => 'se.pengajuan.cuti']);
        $routes->post('cuti/store', 'SoftwareEngineer\Pengajuan::storeCuti', ['as' => 'se.pengajuan.cuti.store']);
    });

    // 7. Laporan & Keluhan
    $routes->group('laporan-keluhan', function($routes) {
        $routes->get('/', 'SoftwareEngineer\LaporanKeluhan::dashboard', ['as' => 'se.laporan_keluhan']);
        $routes->get('dashboard', 'SoftwareEngineer\LaporanKeluhan::dashboard', ['as' => 'se.laporan_keluhan.dashboard']);
        $routes->get('laporan-harian', 'SoftwareEngineer\LaporanKeluhan::laporanHarian', ['as' => 'se.laporan_keluhan.harian']);
        $routes->post('laporan-harian/store', 'SoftwareEngineer\LaporanKeluhan::storeLaporanHarian', ['as' => 'se.laporan_keluhan.harian.store']);
        $routes->get('keluhan', 'SoftwareEngineer\LaporanKeluhan::keluhan', ['as' => 'se.laporan_keluhan.keluhan']);
        $routes->post('keluhan/store', 'SoftwareEngineer\LaporanKeluhan::storeKeluhan', ['as' => 'se.laporan_keluhan.keluhan.store']);
    });

    // 8. Menu Pribadi
    $routes->group('pribadi', function($routes) {
        $routes->get('absensi', 'SoftwareEngineer\Pribadi::absensi', ['as' => 'se.pribadi.absensi']);
        $routes->get('tugas', 'SoftwareEngineer\Pribadi::tugas', ['as' => 'se.pribadi.tugas']);
        $routes->get('laporan-harian', 'SoftwareEngineer\Pribadi::laporanHarian', ['as' => 'se.pribadi.laporan_harian']);
        $routes->get('keluhan', 'SoftwareEngineer\Pribadi::keluhan', ['as' => 'se.pribadi.keluhan']);
        $routes->get('pengajuan', 'SoftwareEngineer\Pribadi::pengajuan', ['as' => 'se.pribadi.pengajuan']);
        $routes->get('slip-gaji', 'SoftwareEngineer\Pribadi::slipGaji', ['as' => 'se.pribadi.slip_gaji']);
        $routes->get('profil', 'SoftwareEngineer\Pribadi::profil', ['as' => 'se.pribadi.profil']);
        $routes->post('profil/update', 'SoftwareEngineer\Pribadi::updateProfil', ['as' => 'se.pribadi.profil.update']);
    });

    // Legacy / Direct Profile Link
    $routes->get('profile', 'SoftwareEngineer\Pribadi::profil');
});
