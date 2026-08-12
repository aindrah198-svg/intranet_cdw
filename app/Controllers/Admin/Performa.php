<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KaryawanModel;
use App\Models\ActivityLogModel;

class Performa extends BaseController
{
    protected $karyawanModel;
    protected $activityLogModel;

    public function __construct()
    {
        $this->karyawanModel = new KaryawanModel();
        $this->activityLogModel = new ActivityLogModel();
        helper(['form', 'url']);
    }

    public function kpi()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $data = [
            'title' => 'KPI & Performa Karyawan - HRD CDW',
            'active' => 'performa',
            'karyawanList' => $this->karyawanModel->getKaryawanAktif()
        ];

        return view('admin/templates/header', $data)
             . view('admin/templates/navbar', $data)
             . view('admin/templates/sidebar', $data)
             . view('admin/performa/kpi', $data)
             . view('admin/templates/footer', $data);
    }

    public function tinjauan()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $data = [
            'title' => 'Tinjauan & Evaluasi Karyawan - HRD CDW',
            'active' => 'performa',
            'karyawanList' => $this->karyawanModel->getKaryawanAktif()
        ];

        return view('admin/templates/header', $data)
             . view('admin/templates/navbar', $data)
             . view('admin/templates/sidebar', $data)
             . view('admin/performa/tinjauan', $data)
             . view('admin/templates/footer', $data);
    }

    public function auditTrail()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $logs = [];
        if (class_exists('App\Models\ActivityLogModel')) {
            try {
                $logs = $this->activityLogModel->orderBy('id', 'DESC')->findAll(50);
            } catch (\Throwable $e) {
                $logs = [];
            }
        }

        $data = [
            'title' => 'Audit Trail & Keamanan Sistem - HRD CDW',
            'active' => 'performa',
            'logs' => $logs
        ];

        return view('admin/templates/header', $data)
             . view('admin/templates/navbar', $data)
             . view('admin/templates/sidebar', $data)
             . view('admin/performa/audit_trail', $data)
             . view('admin/templates/footer', $data);
    }
}
