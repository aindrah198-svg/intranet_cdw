<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class TeknisiFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Debug info
        log_message('debug', 'TEKNISI FILTER: Checking authentication...');
        
        // Cek apakah user sudah login
        $session = \Config\Services::session();
        $isLoggedIn = $session->get('isLoggedIn');
        
        if (!$isLoggedIn) {
            log_message('debug', 'TEKNISI FILTER: User not logged in. Redirecting to login.');
            $session->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('login'));
        }
        
        // Cek apakah user adalah teknisi (case-insensitive)
        $userRole = $session->get('role');
        $roleLower = strtolower($userRole ?? '');
        
        // Debug info
        log_message('debug', 'TEKNISI FILTER: User role = ' . $userRole . ' (Lowercase: ' . $roleLower . ')');
        log_message('debug', 'TEKNISI FILTER: User ID = ' . $session->get('user_id'));
        log_message('debug', 'TEKNISI FILTER: Username = ' . $session->get('username'));
        
        // Jika role bukan teknisi, redirect ke dashboard sesuai role
        if ($roleLower !== 'teknisi') {
            log_message('debug', 'TEKNISI FILTER: Access denied. User is not TEKNISI. Redirecting...');
            $session->setFlashdata('error', 'Akses ditolak! Hanya untuk teknisi.');
            
            // Redirect ke dashboard sesuai role
            switch ($roleLower) {
                case 'hrd':
                    log_message('debug', 'TEKNISI FILTER: Redirecting to HRD dashboard');
                    return redirect()->to(base_url('hrd'));
                case 'admin':
                    log_message('debug', 'TEKNISI FILTER: Redirecting to ADMIN dashboard');
                    return redirect()->to(base_url('admin'));
                case 'direktur':
                    log_message('debug', 'TEKNISI FILTER: Redirecting to DIREKTUR dashboard');
                    return redirect()->to(base_url('direktur'));
                case 'accounting':
                    log_message('debug', 'TEKNISI FILTER: Redirecting to ACCOUNTING dashboard');
                    return redirect()->to(base_url('accounting'));
                case 'sales':
                case 'marketing':
                    log_message('debug', 'TEKNISI FILTER: Redirecting to SALES dashboard');
                    return redirect()->to(base_url('sales'));
                case 'staff':
                    log_message('debug', 'TEKNISI FILTER: Redirecting to STAFF dashboard');
                    return redirect()->to(base_url('staff'));
                default:
                    log_message('debug', 'TEKNISI FILTER: Role not recognized: ' . $userRole);
                    $session->setFlashdata('error', 'Role tidak dikenali. Hubungi administrator.');
                    return redirect()->to(base_url('login'));
            }
        }
        
        log_message('debug', 'TEKNISI FILTER: Access granted for TEKNISI');
        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here if needed
        return $response;
    }
}