<?php

namespace App\Controllers\Accounting;

use App\Controllers\BaseController;

class AccountingController extends BaseController
{
    protected $session;
    
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, 
                                 \CodeIgniter\HTTP\ResponseInterface $response, 
                                 \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        $this->session = \Config\Services::session();
        
        // Cek login
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu.');
        }
        
        // Cek role
        $userRole = $this->session->get('role');
        $roleLower = strtolower($userRole ?? '');
        
        if ($roleLower !== 'accounting') {
            $this->redirectToDashboard($userRole);
        }
    }
    
    /**
     * Redirect based on role
     */
    private function redirectToDashboard($role)
    {
        $roleLower = strtolower($role ?? '');
        
        switch ($roleLower) {
            case 'hrd':
                return redirect()->to(base_url('hrd'));
            case 'admin':
                return redirect()->to(base_url('admin'));
            case 'direktur':
                return redirect()->to(base_url('direktur'));
            case 'sales':
            case 'marketing':
                return redirect()->to(base_url('sales'));
            case 'teknisi':
                return redirect()->to(base_url('teknisi'));
            case 'staff':
                return redirect()->to(base_url('staff'));
            default:
                return redirect()->to(base_url('login'))->with('error', 'Role tidak dikenali.');
        }
    }
    
    /**
     * Render view dengan template
     */
    protected function render($view, $data = [])
    {
        // Default data dari session
        $defaultData = [
            'title' => 'Accounting System',
            'subtitle' => date('l, d F Y'),
            'user' => [
                'name' => $this->session->get('name') ?? 'Accounting Staff',
                'username' => $this->session->get('username') ?? 'accounting',
                'email' => $this->session->get('email') ?? '',
                'role' => $this->session->get('role') ?? 'accounting'
            ],
            'karyawan' => [
                'nik' => $this->session->get('nik') ?? 'N/A',
                'nama_lengkap' => $this->session->get('name') ?? 'Accounting Staff',
                'nama_panggilan' => $this->session->get('username') ?? 'Accounting',
                'jabatan' => 'Accounting Staff',
                'departemen' => 'Finance & Accounting',
                'divisi' => 'Finance'
            ]
        ];
        
        // Merge data
        $viewData = array_merge($defaultData, $data);
        
        // Tampilkan view dengan template
        echo view('accounting/templates/header', $viewData);
        echo view('accounting/templates/sidebar', $viewData);
        echo view('accounting/templates/navbar', $viewData);
        echo view($view, $viewData);
        echo view('accounting/templates/footer', $viewData);
    }
}