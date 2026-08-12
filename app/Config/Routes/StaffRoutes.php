<?php
// app/Config/Routes/StaffRoutes.php

/**
 * @var \CodeIgniter\Router\RouteCollection $routes
 */

$routes->group('staff', ['filter' => 'auth'], function($routes) {
    // Dashboard
    $routes->get('/', 'Staff\Dashboard::index', ['as' => 'staff.dashboard']);
    $routes->get('dashboard', 'Staff\Dashboard::index');

    // Absensi
    $routes->get('absensi', 'Staff\Absensi::index', ['as' => 'staff.absensi']);
    $routes->get('absensi/checkin-page', 'Staff\Absensi::index');
    $routes->post('absensi/checkin', 'Staff\Absensi::checkin', ['as' => 'staff.absensi.checkin']);
    $routes->post('absensi/checkout', 'Staff\Absensi::checkout', ['as' => 'staff.absensi.checkout']);
    $routes->get('absensi/riwayat', 'Staff\Absensi::riwayat', ['as' => 'staff.absensi.riwayat']);

    // Tugas & Laporan
    $routes->get('tugas', 'Staff\Tugas::index', ['as' => 'staff.tugas']);
    $routes->post('tugas/update-status', 'Staff\Tugas::updateStatus', ['as' => 'staff.tugas.update_status']);
    $routes->get('laporan/create', 'Staff\Laporan::create', ['as' => 'staff.laporan.create']);
    $routes->post('laporan/store', 'Staff\Laporan::store', ['as' => 'staff.laporan.store']);
    $routes->get('laporan', 'Staff\Laporan::index', ['as' => 'staff.laporan']);

    // Form Pengajuan
    $routes->get('pengajuan/cuti', 'Staff\Pengajuan::cuti', ['as' => 'staff.pengajuan.cuti']);
    $routes->post('pengajuan/cuti/store', 'Staff\Pengajuan::storeCuti', ['as' => 'staff.pengajuan.cuti.store']);
    $routes->get('pengajuan/izin', 'Staff\Pengajuan::izin', ['as' => 'staff.pengajuan.izin']);
    $routes->post('pengajuan/izin/store', 'Staff\Pengajuan::storeIzin', ['as' => 'staff.pengajuan.izin.store']);
    $routes->get('pengajuan/kasbon', 'Staff\Pengajuan::kasbon', ['as' => 'staff.pengajuan.kasbon']);
    $routes->post('pengajuan/kasbon/store', 'Staff\Pengajuan::storeKasbon', ['as' => 'staff.pengajuan.kasbon.store']);
    $routes->get('pengajuan/riwayat', 'Staff\Pengajuan::riwayat', ['as' => 'staff.pengajuan.riwayat']);

    // Slip Gaji
    $routes->get('payroll', 'Staff\Payroll::index', ['as' => 'staff.payroll']);
    $routes->get('payroll/cetak/(:num)', 'Staff\Payroll::cetak/$1', ['as' => 'staff.payroll.cetak']);

    // Profil
    $routes->get('profil', 'Staff\Profil::index', ['as' => 'staff.profil']);
    $routes->post('profil/update', 'Staff\Profil::update', ['as' => 'staff.profil.update']);
    $routes->get('dokumen', 'Staff\Profil::dokumen', ['as' => 'staff.dokumen']);
});
