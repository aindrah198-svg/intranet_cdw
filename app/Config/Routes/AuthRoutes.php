<?php
// C:\xampp\htdocs\intranet_cdw\app\Config\Routes\AuthRoutes.php

/**
 * @var \CodeIgniter\Router\RouteCollection $routes
 * 
 * File ini berisi semua routes yang berhubungan dengan autentikasi:
 * - Login
 * - Logout
 * - Forgot Password (Lupa Password)
 * - Reset Password
 */

// ============================================
// AUTHENTICATION ROUTES (Tanpa Filter Auth)
// ============================================

// Login Routes
$routes->get('login', 'Auth::index', ['as' => 'login']);
$routes->post('login', 'Auth::process', ['as' => 'login.process']);
$routes->get('logout', 'Auth::logout', ['as' => 'logout']);

// Forgot Password Routes
$routes->get('forgot-password', 'Auth::forgotPassword', ['as' => 'forgot.password']);
$routes->post('forgot-password/send', 'Auth::sendResetLink', ['as' => 'forgot.password.send']);

// Reset Password Routes
$routes->get('reset-password/(:any)', 'Auth::resetPassword/$1', ['as' => 'reset.password']);
$routes->post('reset-password/update', 'Auth::updatePassword', ['as' => 'reset.password.update']);

// Debug routes (development only) - untuk testing remember me
if (ENVIRONMENT === 'development') {
    $routes->get('debug-remember', 'Auth::debugRemember');
    $routes->get('test-remember/(:num)', 'Auth::testSetRemember/$1');
}