<?php

namespace App\Controllers\Teknisi;

class Laporan extends TeknisiController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $teknisiId = $this->karyawanData['id'] ?? $this->userId;

        $totalLaporanHarian = 0;
        if ($db->tableExists('laporan_harian')) {
            $totalLaporanHarian = $db->table('laporan_harian')->where('karyawan_id', $teknisiId)->countAllResults();
        }

        $totalKeluhan = $db->table('keluhan_karyawan')->where('karyawan_id', $teknisiId)->countAllResults();

        $data = [
            'title' => 'Dashboard Laporan Teknisi',
            'active' => 'laporan',
            'totalLaporanHarian' => $totalLaporanHarian,
            'totalKeluhan' => $totalKeluhan
        ];

        return $this->renderView('teknisi/laporan/index', $data);
    }

    /**
     * FITUR WAJIB: Laporan Pekerjaan Harian / Lapangan
     */
    public function lapangan()
    {
        $db = \Config\Database::connect();
        $teknisiId = $this->karyawanData['id'] ?? $this->userId;

        $laporanList = [];
        if ($db->tableExists('laporan_harian')) {
            $laporanList = $db->table('laporan_harian')
                ->where('karyawan_id', $teknisiId)
                ->orderBy('tanggal', 'DESC')
                ->get()->getResultArray();
        }

        $data = [
            'title' => 'Laporan Pekerjaan Harian / Lapangan',
            'active' => 'laporan-lapangan',
            'laporanList' => $laporanList
        ];

        return $this->renderView('teknisi/laporan/lapangan', $data);
    }

    public function storeLapangan()
    {
        $db = \Config\Database::connect();
        $teknisiId = $this->karyawanData['id'] ?? $this->userId;

        if ($db->tableExists('laporan_harian')) {
            $db->table('laporan_harian')->insert([
                'karyawan_id' => $teknisiId,
                'tanggal' => $this->request->getPost('tanggal') ?: date('Y-m-d'),
                'judul' => $this->request->getPost('judul'),
                'deskripsi' => $this->request->getPost('deskripsi'),
                'status' => 'Pending Review',
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        return redirect()->to(site_url('teknisi/laporan/lapangan'))->with('success', 'Laporan pekerjaan harian lapangan berhasil disubmit.');
    }

    /**
     * FITUR WAJIB: Keluhan Karyawan / Keluhan Lapangan
     */
    public function keluhan()
    {
        $db = \Config\Database::connect();
        $teknisiId = $this->karyawanData['id'] ?? $this->userId;

        $keluhanList = $db->table('keluhan_karyawan')
            ->where('karyawan_id', $teknisiId)
            ->orderBy('id', 'DESC')
            ->get()->getResultArray();

        $data = [
            'title' => 'Keluhan Karyawan & Kendala Lapangan',
            'active' => 'laporan-keluhan',
            'keluhanList' => $keluhanList
        ];

        return $this->renderView('teknisi/laporan/keluhan', $data);
    }

    public function storeKeluhan()
    {
        $db = \Config\Database::connect();
        $teknisiId = $this->karyawanData['id'] ?? $this->userId;
        $namaKaryawan = $this->karyawanData['nama_lengkap'] ?? $this->userData['name'] ?? 'Teknisi';
        $kode = 'KLH-' . date('Ymd') . '-' . rand(100, 999);

        $db->table('keluhan_karyawan')->insert([
            'kode_keluhan' => $kode,
            'karyawan_id' => $teknisiId,
            'nama_karyawan' => $namaKaryawan,
            'kategori' => $this->request->getPost('kategori') ?? 'Kendala Lapangan',
            'judul' => $this->request->getPost('judul'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'tgl_keluhan' => $this->request->getPost('tgl_keluhan') ?: date('Y-m-d'),
            'status' => 'Baru',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(site_url('teknisi/laporan/keluhan'))->with('success', 'Keluhan / kendala lapangan berhasil disampaikan ke Manajemen.');
    }

    public function inventory()
    {
        $data = [
            'title' => 'Laporan Rekap Mutasi Inventory Gudang',
            'active' => 'laporan-inventory'
        ];

        return $this->renderView('teknisi/laporan/inventory', $data);
    }
}
