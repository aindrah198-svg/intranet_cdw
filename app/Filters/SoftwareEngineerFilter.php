<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class SoftwareEngineerFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = \Config\Services::session();
        $isLoggedIn = $session->get('isLoggedIn');
        
        if (!$isLoggedIn) {
            $session->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to(base_url('login'));
        }
        
        $userRole = strtolower(trim($session->get('role') ?? ''));
        
        // Software engineer role aliases
        $allowedRoles = ['software_engineer', 'software engineer', 'se', 'developer', 'programmer', 'admin', 'direktur'];
        
        if (!in_array($userRole, $allowedRoles)) {
            $session->setFlashdata('error', 'Akses ditolak! Halaman ini hanya untuk Software Engineer.');
            
            switch ($userRole) {
                case 'hrd':
                    return redirect()->to(base_url('hrd'));
                case 'admin':
                    return redirect()->to(base_url('admin'));
                case 'direktur':
                    return redirect()->to(base_url('direktur'));
                case 'accounting':
                    return redirect()->to(base_url('accounting'));
                case 'sales':
                case 'marketing':
                    return redirect()->to(base_url('sales'));
                case 'teknisi':
                    return redirect()->to(base_url('teknisi'));
                default:
                    return redirect()->to(base_url('staff'));
            }
        }
        
        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}
