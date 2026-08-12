<?php

namespace App\Controllers\SoftwareEngineer;

use App\Models\LaporanHarianModel;
use App\Models\KeluhanKaryawanModel;

class LaporanKeluhan extends BaseSeController
{
    protected $laporanModel;
    protected $keluhanModel;

    public function __construct()
    {
        $this->laporanModel = new LaporanHarianModel();
        $this->keluhanModel = new KeluhanKaryawanModel();
    }

    public function dashboard()
    {
        $userId     = session()->get('user_id');
        $karyawanId = session()->get('karyawan_id');

        $laporan = $this->laporanModel->where('user_id', $userId)->findAll();
        $keluhan = $this->keluhanModel->where('karyawan_id', $karyawanId)->findAll();

        $data = [
            'title'         => 'Dashboard Laporan & Keluhan - Software Engineer',
            'active'        => 'laporan-keluhan',
            'sub'           => 'dashboard',
            'laporan_list'  => $laporan,
            'keluhan_list'  => $keluhan
        ];

        return view('software_engineer/laporan_keluhan/dashboard', $data);
    }

    public function laporanHarian()
    {
        $userId  = session()->get('user_id');
        $laporan = $this->laporanModel->where('user_id', $userId)->orderBy('tanggal', 'DESC')->findAll();

        $data = [
            'title'        => 'Laporan Progress Harian - Software Engineer',
            'active'       => 'laporan-keluhan',
            'sub'          => 'laporan-harian',
            'laporan_list' => $laporan
        ];

        return view('software_engineer/laporan_keluhan/laporan_harian', $data);
    }

    public function storeLaporanHarian()
    {
        $this->laporanModel->save([
            'user_id'          => session()->get('user_id'),
            'karyawan_id'      => session()->get('karyawan_id'),
            'tanggal'          => $this->request->getPost('tanggal') ?: date('Y-m-d'),
            'ringkasan_kerja'  => $this->request->getPost('ringkasan_kerja'),
            'detail_pekerjaan' => $this->request->getPost('detail_pekerjaan'),
            'kendala'          => $this->request->getPost('kendala'),
            'rencana_besok'    => $this->request->getPost('rencana_besok')
        ]);

        return redirect()->to(base_url('software-engineer/laporan-keluhan/laporan-harian'))->with('success', 'Laporan progress harian berhasil disimpan.');
    }

    public function keluhan()
    {
        $karyawanId = session()->get('karyawan_id');
        $keluhan    = $this->keluhanModel->where('karyawan_id', $karyawanId)->orderBy('created_at', 'DESC')->findAll();

        $data = [
            'title'        => 'Keluhan & Masalah Kerja - Software Engineer',
            'active'       => 'laporan-keluhan',
            'sub'          => 'keluhan',
            'keluhan_list' => $keluhan
        ];

        return view('software_engineer/laporan_keluhan/keluhan', $data);
    }

    public function storeKeluhan()
    {
        $this->keluhanModel->save([
            'karyawan_id'    => session()->get('karyawan_id'),
            'judul_keluhan'  => $this->request->getPost('judul_keluhan'),
            'detail_keluhan' => $this->request->getPost('detail_keluhan'),
            'kategori'       => $this->request->getPost('kategori') ?: 'Teknis / Infrastructure',
            'status'         => 'Open'
        ]);

        return redirect()->to(base_url('software-engineer/laporan-keluhan/keluhan'))->with('success', 'Keluhan Anda telah terkirim.');
    }
}
