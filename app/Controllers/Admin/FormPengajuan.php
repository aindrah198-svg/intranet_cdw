<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KaryawanModel;
use App\Models\CutiModel;

class FormPengajuan extends BaseController
{
    protected $karyawanModel;
    protected $cutiModel;

    public function __construct()
    {
        $this->karyawanModel = new KaryawanModel();
        $this->cutiModel = new CutiModel();
        helper(['form', 'url']);
    }

    public function cuti()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $data = [
            'title' => 'Form Pengajuan Cuti - HRD CDW',
            'active' => 'form-pengajuan',
            'karyawanList' => $this->karyawanModel->getKaryawanAktif()
        ];

        return view('admin/templates/header', $data)
             . view('admin/templates/navbar', $data)
             . view('admin/templates/sidebar', $data)
             . view('admin/form_pengajuan/cuti', $data)
             . view('admin/templates/footer', $data);
    }

    public function izin()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $data = [
            'title' => 'Form Pengajuan Izin / Sakit - HRD CDW',
            'active' => 'form-pengajuan',
            'karyawanList' => $this->karyawanModel->getKaryawanAktif()
        ];

        return view('admin/templates/header', $data)
             . view('admin/templates/navbar', $data)
             . view('admin/templates/sidebar', $data)
             . view('admin/form_pengajuan/izin', $data)
             . view('admin/templates/footer', $data);
    }

    public function dokumen()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $data = [
            'title' => 'Form Pengajuan Surat/Dokumen Karyawan - HRD CDW',
            'active' => 'form-pengajuan',
            'karyawanList' => $this->karyawanModel->getKaryawanAktif()
        ];

        return view('admin/templates/header', $data)
             . view('admin/templates/navbar', $data)
             . view('admin/templates/sidebar', $data)
             . view('admin/form_pengajuan/dokumen', $data)
             . view('admin/templates/footer', $data);
    }

    public function storeCuti()
    {
        return redirect()->to(base_url('admin/cuti'))->with('success', 'Pengajuan cuti berhasil disubmit.');
    }

    public function storeIzin()
    {
        return redirect()->to(base_url('admin/form-pengajuan/izin'))->with('success', 'Form izin/sakit berhasil disubmit.');
    }

    public function storeDokumen()
    {
        return redirect()->to(base_url('admin/form-pengajuan/dokumen'))->with('success', 'Form permintaan dokumen berhasil disubmit.');
    }
}
