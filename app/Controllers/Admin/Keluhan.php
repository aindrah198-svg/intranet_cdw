<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KeluhanKaryawanModel;

class Keluhan extends BaseController
{
    protected $keluhanModel;

    public function __construct()
    {
        $this->keluhanModel = new KeluhanKaryawanModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $keluhanList = [];
        $statistik = ['baru' => 0, 'diproses' => 0, 'selesai' => 0, 'ditolak' => 0];

        try {
            $keluhanList = $this->keluhanModel->getAllWithKaryawan();
            $statistik   = $this->keluhanModel->getStatistik();
        } catch (\Throwable $e) {
            $keluhanList = [];
        }

        $data = [
            'title' => 'Keluhan Karyawan (Shared View) - HRD CDW',
            'active' => 'keluhan',
            'keluhanList' => $keluhanList,
            'statistik' => $statistik
        ];

        return view('admin/templates/header', $data)
             . view('admin/templates/navbar', $data)
             . view('admin/templates/sidebar', $data)
             . view('admin/keluhan/index', $data)
             . view('admin/templates/footer', $data);
    }

    public function tanggapi($id)
    {
        $status = $this->request->getPost('status');
        $tanggapan = $this->request->getPost('tanggapan');

        try {
            $this->keluhanModel->update($id, [
                'status' => $status,
                'tanggapan' => $tanggapan,
                'ditanggapi_oleh' => session()->get('name') ?? 'HRD Admin',
                'tanggal_tanggapan' => date('Y-m-d H:i:s')
            ]);
            return redirect()->to(base_url('admin/keluhan'))->with('success', 'Tanggapan keluhan berhasil disimpan.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan tanggapan.');
        }
    }
}
