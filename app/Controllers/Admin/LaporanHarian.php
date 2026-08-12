<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LaporanHarianModel;

class LaporanHarian extends BaseController
{
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $laporanModel = new LaporanHarianModel();
        $laporan = [];
        
        try {
            $laporan = $laporanModel->getLaporanWithKaryawan();
        } catch (\Exception $e) {
            log_message('error', 'LaporanHarian getLaporanWithKaryawan error: ' . $e->getMessage());
        }

        $data = [
            'title' => 'Laporan Harian Karyawan (Shared View) - HRD CDW',
            'active' => 'laporan-harian',
            'laporan' => $laporan
        ];

        return view('admin/templates/header', $data)
             . view('admin/templates/navbar', $data)
             . view('admin/templates/sidebar', $data)
             . view('admin/laporan_harian/index', $data)
             . view('admin/templates/footer', $data);
    }
}
