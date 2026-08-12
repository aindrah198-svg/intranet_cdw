<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KaryawanModel;

class Finansial extends BaseController
{
    protected $karyawanModel;

    public function __construct()
    {
        $this->karyawanModel = new KaryawanModel();
        helper(['form', 'url']);
    }

    public function payroll()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $data = [
            'title' => 'Komponen Payroll & Perhitungan Gaji - HRD CDW',
            'active' => 'finansial',
            'karyawanList' => $this->karyawanModel->getKaryawanAktif()
        ];

        return view('admin/templates/header', $data)
             . view('admin/templates/navbar', $data)
             . view('admin/templates/sidebar', $data)
             . view('admin/finansial/payroll', $data)
             . view('admin/templates/footer', $data);
    }

    public function bpjs()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $data = [
            'title' => 'Data BPJS Kesehatan & Ketenagakerjaan - HRD CDW',
            'active' => 'finansial',
            'karyawanList' => $this->karyawanModel->getKaryawanAktif()
        ];

        return view('admin/templates/header', $data)
             . view('admin/templates/navbar', $data)
             . view('admin/templates/sidebar', $data)
             . view('admin/finansial/bpjs', $data)
             . view('admin/templates/footer', $data);
    }

    public function pajak()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $data = [
            'title' => 'Perhitungan Pajak PPh21 Karyawan - HRD CDW',
            'active' => 'finansial',
            'karyawanList' => $this->karyawanModel->getKaryawanAktif()
        ];

        return view('admin/templates/header', $data)
             . view('admin/templates/navbar', $data)
             . view('admin/templates/sidebar', $data)
             . view('admin/finansial/pajak', $data)
             . view('admin/templates/footer', $data);
    }
}
