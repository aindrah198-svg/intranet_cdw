<?php
// app/Config/Routes/SalesRoutes.php

/**
 * @var \CodeIgniter\Router\RouteCollection $routes
 */

$routes->group('sales', ['filter' => 'auth'], function($routes) {
    // Dashboard
    $routes->get('/', 'Sales\Dashboard::index', ['as' => 'sales.dashboard']);
    $routes->get('dashboard', 'Sales\Dashboard::index', ['as' => 'sales.dashboard.index']);

    // Leads & Pipeline
    $routes->group('leads', function($routes) {
        $routes->get('/', 'Sales\Leads::index', ['as' => 'sales.leads']);
        $routes->get('pipeline', 'Sales\Leads::pipeline', ['as' => 'sales.leads.pipeline']);
        $routes->get('create', 'Sales\Leads::create', ['as' => 'sales.leads.create']);
        $routes->post('store', 'Sales\Leads::store', ['as' => 'sales.leads.store']);
        $routes->get('edit/(:num)', 'Sales\Leads::edit/$1', ['as' => 'sales.leads.edit']);
        $routes->post('update/(:num)', 'Sales\Leads::update/$1', ['as' => 'sales.leads.update']);
        $routes->post('delete/(:num)', 'Sales\Leads::delete/$1', ['as' => 'sales.leads.delete']);
        $routes->post('update-status', 'Sales\Leads::updateStatus', ['as' => 'sales.leads.update-status']);
    });

    // Quotation
    $routes->group('quotation', function($routes) {
        $routes->get('/', 'Sales\Quotation::index', ['as' => 'sales.quotation']);
        $routes->get('create', 'Sales\Quotation::create', ['as' => 'sales.quotation.create']);
        $routes->post('store', 'Sales\Quotation::store', ['as' => 'sales.quotation.store']);
        $routes->get('detail/(:num)', 'Sales\Quotation::detail/$1', ['as' => 'sales.quotation.detail']);
        $routes->get('edit/(:num)', 'Sales\Quotation::edit/$1', ['as' => 'sales.quotation.edit']);
        $routes->post('update/(:num)', 'Sales\Quotation::update/$1', ['as' => 'sales.quotation.update']);
        $routes->post('delete/(:num)', 'Sales\Quotation::delete/$1', ['as' => 'sales.quotation.delete']);
        $routes->get('pdf/(:num)', 'Sales\Quotation::pdf/$1', ['as' => 'sales.quotation.pdf']);
    });

    // Closing & Deal
    $routes->group('deal', function($routes) {
        $routes->get('/', 'Sales\Deal::index', ['as' => 'sales.deal']);
        $routes->get('create', 'Sales\Deal::create', ['as' => 'sales.deal.create']);
        $routes->post('store', 'Sales\Deal::store', ['as' => 'sales.deal.store']);
        $routes->get('detail/(:num)', 'Sales\Deal::detail/$1', ['as' => 'sales.deal.detail']);
        $routes->post('delete/(:num)', 'Sales\Deal::delete/$1', ['as' => 'sales.deal.delete']);
        $routes->get('invoice/(:num)', 'Sales\Deal::createInvoice/$1', ['as' => 'sales.deal.invoice']);
        $routes->get('project/(:num)', 'Sales\Deal::createProject/$1', ['as' => 'sales.deal.project']);
    });

    // Laporan Penjualan
    $routes->group('laporan', function($routes) {
        $routes->get('/', 'Sales\Laporan::index', ['as' => 'sales.laporan']);
        $routes->get('target', 'Sales\Laporan::target', ['as' => 'sales.laporan.target']);
        $routes->post('save-target', 'Sales\Laporan::saveTarget', ['as' => 'sales.laporan.save-target']);
    });

    // Kontak Klien
    $routes->group('kontak', function($routes) {
        $routes->get('/', 'Sales\Kontak::index', ['as' => 'sales.kontak']);
        $routes->get('create', 'Sales\Kontak::create', ['as' => 'sales.kontak.create']);
        $routes->post('store', 'Sales\Kontak::store', ['as' => 'sales.kontak.store']);
        $routes->get('detail/(:num)', 'Sales\Kontak::detail/$1', ['as' => 'sales.kontak.detail']);
        $routes->get('edit/(:num)', 'Sales\Kontak::edit/$1', ['as' => 'sales.kontak.edit']);
        $routes->post('update/(:num)', 'Sales\Kontak::update/$1', ['as' => 'sales.kontak.update']);
        $routes->post('delete/(:num)', 'Sales\Kontak::delete/$1', ['as' => 'sales.kontak.delete']);
        $routes->post('interaksi/store', 'Sales\Kontak::storeInteraksi', ['as' => 'sales.kontak.interaksi.store']);
    });

    // Menu Pribadi
    $routes->group('pribadi', function($routes) {
        $routes->get('absensi', 'Sales\Pribadi::absensi', ['as' => 'sales.pribadi.absensi']);
        $routes->get('tugas', 'Sales\Pribadi::tugas', ['as' => 'sales.pribadi.tugas']);
        $routes->get('laporan-harian', 'Sales\Pribadi::laporanHarian', ['as' => 'sales.pribadi.laporan-harian']);
        $routes->get('pengajuan', 'Sales\Pribadi::pengajuan', ['as' => 'sales.pribadi.pengajuan']);
        $routes->get('slip-gaji', 'Sales\Pribadi::slipGaji', ['as' => 'sales.pribadi.slip-gaji']);
        $routes->get('profil', 'Sales\Pribadi::profil', ['as' => 'sales.pribadi.profil']);
    });
});