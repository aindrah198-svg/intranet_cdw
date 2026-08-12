<?php

namespace App\Controllers\Staff;

use App\Controllers\BaseController;

class Laporan extends BaseController
{
    private function getUserData()
    {
        $session = session();
        $db = \Config\Database::connect();
        $userId = $session->get('user_id');

        return $db->table('users')
                  ->select('users.*, karyawan.nama_lengkap, karyawan.nik, karyawan.jabatan, karyawan.divisi, karyawan.id as karyawan_id')
                  ->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
                  ->where('users.id', $userId)
                  ->get()
                  ->getRowArray();
    }

    public function index()
    {
        $user = $this->getUserData();
        $karyawanId = $user['karyawan_id'] ?? null;
        $db = \Config\Database::connect();

        $laporanList = [];
        if ($karyawanId && $db->tableExists('laporan_harian')) {
            $laporanList = $db->table('laporan_harian')
                              ->where('karyawan_id', $karyawanId)
                              ->orderBy('tanggal', 'DESC')
                              ->get()
                              ->getResultArray();
        }

        $data = [
            'title' => 'Riwayat Laporan Saya',
            'active' => 'laporan_riwayat',
            'user' => [
                'name' => $user['nama_lengkap'] ?? $user['name'] ?? session('username'),
                'jabatan' => $user['jabatan'] ?? 'Staff',
                'divisi' => $user['divisi'] ?? 'General',
            ],
            'laporanList' => $laporanList
        ];

        return view('staff/templates/header', $data)
             . view('staff/templates/sidebar', $data)
             . view('staff/laporan/index', $data)
             . view('staff/templates/footer', $data);
    }

    public function create()
    {
        $user = $this->getUserData();
        $userId = session('user_id');
        $karyawanId = $user['karyawan_id'] ?? null;
        $db = \Config\Database::connect();

        $tugasList = [];
        if ($db->tableExists('penugasan_harian')) {
            $builder = $db->table('penugasan_harian');
            $builder->groupStart()
                    ->where('penerima_id', $userId)
                    ->orWhere('penerima_id', $karyawanId)
                    ->groupEnd();
            $tugasList = $builder->where('status !=', 'selesai')
                                 ->orderBy('created_at', 'DESC')
                                 ->get()
                                 ->getResultArray();
        }

        $data = [
            'title' => 'Form Laporan Kerja Harian',
            'active' => 'laporan_create',
            'user' => [
                'name' => $user['nama_lengkap'] ?? $user['name'] ?? session('username'),
                'jabatan' => $user['jabatan'] ?? 'Staff',
                'divisi' => $user['divisi'] ?? 'General',
            ],
            'tugasList' => $tugasList
        ];

        return view('staff/templates/header', $data)
             . view('staff/templates/sidebar', $data)
             . view('staff/laporan/create', $data)
             . view('staff/templates/footer', $data);
    }

    public function store()
    {
        $user = $this->getUserData();
        $karyawanId = $user['karyawan_id'] ?? null;

        if (!$karyawanId) {
            return redirect()->back()->with('error', 'Akun Anda belum terhubung dengan Data Karyawan. Hubungi HRD.');
        }

        $tanggal = $this->request->getPost('tanggal') ?? date('Y-m-d');
        $judul = $this->request->getPost('judul');
        $deskripsi = $this->request->getPost('deskripsi');

        if (empty($judul) || empty($deskripsi)) {
            return redirect()->back()->withInput()->with('error', 'Judul dan Deskripsi Laporan wajib diisi.');
        }

        $lampiranFile = $this->request->getFile('lampiran');
        $namaLampiran = null;

        if ($lampiranFile && $lampiranFile->isValid() && !$lampiranFile->hasMoved()) {
            $namaLampiran = $lampiranFile->getRandomName();
            $lampiranFile->move(FCPATH . 'uploads/laporan', $namaLampiran);
        }

        $db = \Config\Database::connect();
        if ($db->tableExists('laporan_harian')) {
            $db->table('laporan_harian')->insert([
                'karyawan_id' => $karyawanId,
                'tanggal' => $tanggal,
                'judul' => $judul,
                'deskripsi' => $deskripsi,
                'lampiran' => $namaLampiran,
                'status' => 'Pending',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        return redirect()->to(base_url('staff/laporan'))->with('success', 'Laporan Kerja Harian berhasil disubmit!');
    }
}
