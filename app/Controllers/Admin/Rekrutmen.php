<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KaryawanModel;
use App\Models\UserModel;

class Rekrutmen extends BaseController
{
    protected $karyawanModel;
    protected $userModel;

    public function __construct()
    {
        $this->karyawanModel = new KaryawanModel();
        $this->userModel = new UserModel();
        helper(['form', 'url']);
    }

    public function pelamar()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $data = [
            'title' => 'Lowongan & Pelamar - HRD CDW',
            'active' => 'rekrutmen',
            'pelamarList' => [
                ['id' => 1, 'nama' => 'Budi Santoso', 'posisi' => 'Teknisi Field', 'email' => 'budi@gmail.com', 'telepon' => '08123456789', 'status' => 'Interview', 'tanggal' => '2026-07-15'],
                ['id' => 2, 'nama' => 'Siti Rahma', 'posisi' => 'Staff Admin HRD', 'email' => 'siti@gmail.com', 'telepon' => '08198765432', 'status' => 'Offer', 'tanggal' => '2026-07-18'],
                ['id' => 3, 'nama' => 'Ahmad Fauzi', 'posisi' => 'Accounting Junior', 'email' => 'fauzi@gmail.com', 'telepon' => '08211223344', 'status' => 'Applied', 'tanggal' => '2026-07-20']
            ]
        ];

        return view('admin/templates/header', $data)
             . view('admin/templates/navbar', $data)
             . view('admin/templates/sidebar', $data)
             . view('admin/rekrutmen/pelamar', $data)
             . view('admin/templates/footer', $data);
    }

    public function onboarding()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $data = [
            'title' => 'Onboarding Karyawan Baru - HRD CDW',
            'active' => 'rekrutmen',
            'onboardingList' => [
                ['id' => 1, 'nama' => 'Siti Rahma', 'posisi' => 'Staff Admin HRD', 'divisi' => 'HRD', 'tgl_masuk' => '2026-08-01', 'status' => 'Diterima - Siap Onboarding']
            ]
        ];

        return view('admin/templates/header', $data)
             . view('admin/templates/navbar', $data)
             . view('admin/templates/sidebar', $data)
             . view('admin/rekrutmen/onboarding', $data)
             . view('admin/templates/footer', $data);
    }

    public function storePelamar()
    {
        return redirect()->to(base_url('admin/rekrutmen/pelamar'))->with('success', 'Data pelamar berhasil ditambahkan.');
    }

    public function processOnboarding($id)
    {
        return redirect()->to(base_url('admin/rekrutmen/onboarding'))->with('success', 'Karyawan berhasil di-onboard dan digenerate sebagai Staff baru.');
    }
}
