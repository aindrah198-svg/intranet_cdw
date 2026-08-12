<?php
// app/Config/Routes/SuperadminRoutes.php

/**
 * @var \CodeIgniter\Router\RouteCollection $routes
 */

$routes->group('superadmin', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('/', 'Superadmin::index', ['as' => 'superadmin.dashboard']);
    $routes->get('welcome', 'Superadmin::welcome', ['as' => 'superadmin.welcome']);
    $routes->get('cetak-pdf', 'Superadmin::cetakPdf', ['as' => 'superadmin.cetak_pdf']);
    $routes->get('flow-direktur', 'Superadmin::flowDirektur', ['as' => 'superadmin.flow_direktur']);
    $routes->get('flow-admin', 'Superadmin::flowAdmin', ['as' => 'superadmin.flow_admin']);
    $routes->get('logout', 'Auth::logout', ['as' => 'superadmin.logout']);
});
