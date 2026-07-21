<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        // Cek session login
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        // Data untuk view
        $data = [
            'title' => 'Admin Dashboard - CDW Engineering',
            'subtitle' => 'Dashboard Overview',
            'user' => [
                'name' => session()->get('name') ?: 'Administrator',
                'role' => session()->get('role') ?: 'admin'
            ],
            'active' => 'dashboard'
        ];

        // Load view dengan data
        return view('admin/index', $data);
    }
    
    public function stats()
    {
        // Cek session login
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        // Data statistik dummy
        $stats = [
            'total_karyawan' => 156,
            'karyawan_aktif' => 148,
            'total_absensi_hari_ini' => 142,
            'payroll_terakhir' => 'Rp 1.250.000.000'
        ];

        return $this->response->setJSON($stats);
    }
    
    public function activities()
    {
        // Cek session login
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        // Data aktivitas dummy
        $activities = [
            [
                'type' => 'new_employee',
                'description' => 'Budi Santoso joined as Project Manager',
                'time' => '2 hours ago'
            ],
            [
                'type' => 'contract',
                'description' => 'New contract with Pertamina for SPBU project',
                'time' => '5 hours ago'
            ],
            [
                'type' => 'system_update',
                'description' => 'Employee management system updated to v2.0',
                'time' => '1 day ago'
            ],
            [
                'type' => 'report',
                'description' => 'Monthly performance report for Q1 2024',
                'time' => '2 days ago'
            ]
        ];

        return $this->response->setJSON($activities);
    }
}