<?php

namespace App\Controllers\Hrd;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = strtolower(session()->get('role') ?? '');
        if ($role !== 'hrd' && $role !== 'admin') {
            return redirect()->to($this->getDashboardUrl())->with('error', 'Akses ditolak!');
        }

        $data = [
            'title' => 'HRD Dashboard - CDW Engineering',
            'subtitle' => 'Human Resource Management Overview',
            'user' => [
                'name' => session()->get('name') ?: 'HRD Manager',
                'role' => session()->get('role') ?: 'hrd'
            ],
            'active' => 'dashboard'
        ];

        return view('hrd/index', $data);
    }
    
    public function stats()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

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
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

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
