<?php

namespace App\Controllers\Accounting;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        // Cek session
        $session = session();
        
        // Cek login
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu.');
        }
        
        // Cek role
        $userRole = $session->get('role');
        $roleLower = strtolower($userRole ?? '');
        
        if ($roleLower !== 'accounting') {
            // Redirect sesuai role
            switch ($roleLower) {
                case 'hrd':
                    return redirect()->to(base_url('hrd'))->with('info', 'Anda dialihkan ke dashboard HRD.');
                case 'admin':
                    return redirect()->to(base_url('admin'))->with('info', 'Anda dialihkan ke dashboard Admin.');
                case 'direktur':
                    return redirect()->to(base_url('direktur'))->with('info', 'Anda dialihkan ke dashboard Direktur.');
                case 'sales':
                case 'marketing':
                    return redirect()->to(base_url('sales'))->with('info', 'Anda dialihkan ke dashboard Sales.');
                case 'teknisi':
                    return redirect()->to(base_url('teknisi'))->with('info', 'Anda dialihkan ke dashboard Teknisi.');
                case 'staff':
                    return redirect()->to(base_url('staff'))->with('info', 'Anda dialihkan ke dashboard Staff.');
                default:
                    return redirect()->to(base_url('login'))->with('error', 'Role tidak dikenali.');
            }
        }
        
        // Ambil user data
        $userId = $session->get('user_id');
        
        // Get user data (sederhana, tanpa model dulu)
        $userData = [
            'user_id' => $userId,
            'name' => $session->get('name') ?? 'Accounting Staff',
            'username' => $session->get('username') ?? 'accounting',
            'email' => $session->get('email') ?? '',
            'role' => $userRole,
            'karyawan_id' => $session->get('karyawan_id') ?? null
        ];
        
        // Get karyawan data sederhana
        $karyawanData = [
            'nik' => $session->get('nik') ?? 'N/A',
            'nama_lengkap' => $session->get('name') ?? 'Accounting Staff',
            'nama_panggilan' => $session->get('username') ?? 'Accounting',
            'jabatan' => 'Accounting Staff',
            'departemen' => 'Finance & Accounting',
            'divisi' => 'Finance',
            'email' => $session->get('email') ?? '',
            'status_karyawan' => 'Tetap'
        ];
        
        // Data untuk view
        $data = [
            'title' => 'Dashboard Accounting',
            'subtitle' => date('l, d F Y'),
            'active' => 'dashboard',
            'user' => $userData,
            'karyawan' => $karyawanData,
             'activeMenu' => 'dashboard', // Pastikan ini ada
        ];
        
        // Render view menggunakan template
        return view('accounting/dashboard/index', $data);
    }
    
    /**
     * Alternative render method
     */
    protected function renderView($view, $data = [])
    {
        // Default data
        $defaultData = [
            'title' => 'Accounting System',
            'subtitle' => date('l, d F Y'),
            'active' => 'dashboard',
            'user' => session()->get() ?? []
        ];
        
        // Merge data
        $viewData = array_merge($defaultData, $data);
        
        // Load template components
        echo view('accounting/templates/header', $viewData);
        echo view('accounting/templates/sidebar', $viewData);
        echo view('accounting/templates/navbar', $viewData);
        echo view($view, $viewData);
        echo view('accounting/templates/footer', $viewData);
    }
}