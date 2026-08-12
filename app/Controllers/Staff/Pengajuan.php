<?php

namespace App\Controllers\Staff;

use App\Controllers\BaseController;

class Pengajuan extends BaseController
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

    public function cuti()
    {
        $user = $this->getUserData();
        $karyawanId = $user['karyawan_id'] ?? null;
        $db = \Config\Database::connect();

        $sisaCuti = 12;
        if ($karyawanId && $db->tableExists('kuota_cuti')) {
            $kuota = $db->table('kuota_cuti')->where('karyawan_id', $karyawanId)->where('tahun', date('Y'))->get()->getRowArray();
            if ($kuota) {
                $sisaCuti = $kuota['sisa'] ?? ($kuota['kuota_tahunan'] - $kuota['terpakai']);
            }
        }

        $data = [
            'title' => 'Form Pengajuan Cuti',
            'active' => 'pengajuan_cuti',
            'user' => [
                'name' => $user['nama_lengkap'] ?? $user['name'] ?? session('username'),
                'jabatan' => $user['jabatan'] ?? 'Staff',
                'divisi' => $user['divisi'] ?? 'General',
            ],
            'sisaCuti' => $sisaCuti
        ];

        return view('staff/templates/header', $data)
             . view('staff/templates/sidebar', $data)
             . view('staff/pengajuan/cuti', $data)
             . view('staff/templates/footer', $data);
    }

    public function storeCuti()
    {
        $user = $this->getUserData();
        $karyawanId = $user['karyawan_id'] ?? null;

        if (!$karyawanId) {
            return redirect()->back()->with('error', 'Data Karyawan belum terikat. Hubungi HRD.');
        }

        $jenisCuti = $this->request->getPost('jenis_cuti') ?? 'Tahunan';
        $tanggalMulai = $this->request->getPost('tanggal_mulai');
        $tanggalSelesai = $this->request->getPost('tanggal_selesai');
        $alasan = $this->request->getPost('alasan');

        if (empty($tanggalMulai) || empty($tanggalSelesai) || empty($alasan)) {
            return redirect()->back()->withInput()->with('error', 'Semua field wajib diisi.');
        }

        $start = strtotime($tanggalMulai);
        $end = strtotime($tanggalSelesai);
        $lamaHari = max(1, round(($end - $start) / (60 * 60 * 24)) + 1);

        $db = \Config\Database::connect();
        if ($db->tableExists('cuti')) {
            $nomorCuti = 'CTI-' . date('Ymd') . '-' . rand(1000, 9999);
            $db->table('cuti')->insert([
                'karyawan_id' => $karyawanId,
                'nomor_cuti' => $nomorCuti,
                'jenis_cuti' => $jenisCuti,
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
                'lama_hari' => $lamaHari,
                'alasan' => $alasan,
                'status_hrd' => 'Menunggu',
                'status_direktur' => 'Menunggu',
                'tanggal_pengajuan' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        return redirect()->to(base_url('staff/pengajuan/riwayat'))->with('success', 'Pengajuan Cuti berhasil dikirimkan!');
    }

    public function izin()
    {
        $user = $this->getUserData();
        $data = [
            'title' => 'Form Pengajuan Izin',
            'active' => 'pengajuan_izin',
            'user' => [
                'name' => $user['nama_lengkap'] ?? $user['name'] ?? session('username'),
                'jabatan' => $user['jabatan'] ?? 'Staff',
                'divisi' => $user['divisi'] ?? 'General',
            ]
        ];

        return view('staff/templates/header', $data)
             . view('staff/templates/sidebar', $data)
             . view('staff/pengajuan/izin', $data)
             . view('staff/templates/footer', $data);
    }

    public function storeIzin()
    {
        $user = $this->getUserData();
        $karyawanId = $user['karyawan_id'] ?? null;

        if (!$karyawanId) {
            return redirect()->back()->with('error', 'Data Karyawan belum terikat.');
        }

        $jenisIzin = $this->request->getPost('jenis_izin') ?? 'Sakit';
        $tanggal = $this->request->getPost('tanggal');
        $alasan = $this->request->getPost('alasan');

        if (empty($tanggal) || empty($alasan)) {
            return redirect()->back()->withInput()->with('error', 'Tanggal dan alasan izin wajib diisi.');
        }

        $db = \Config\Database::connect();
        if ($db->tableExists('form_izin')) {
            $kodeIzin = 'IZN-' . date('Ymd') . '-' . rand(1000, 9999);
            $db->table('form_izin')->insert([
                'karyawan_id' => $karyawanId,
                'kode_izin' => $kodeIzin,
                'jenis_izin' => $jenisIzin,
                'tanggal' => $tanggal,
                'alasan' => $alasan,
                'status_hrd' => 'Menunggu',
                'status_direktur' => 'Menunggu',
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } else if ($db->tableExists('cuti')) {
            // Fallback insert to cuti table if form_izin table doesn't exist
            $db->table('cuti')->insert([
                'karyawan_id' => $karyawanId,
                'nomor_cuti' => 'IZN-' . date('Ymd') . '-' . rand(1000, 9999),
                'jenis_cuti' => 'Izin: ' . $jenisIzin,
                'tanggal_mulai' => $tanggal,
                'tanggal_selesai' => $tanggal,
                'lama_hari' => 1,
                'alasan' => $alasan,
                'status_hrd' => 'Menunggu',
                'status_direktur' => 'Menunggu',
                'tanggal_pengajuan' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        return redirect()->to(base_url('staff/pengajuan/riwayat'))->with('success', 'Pengajuan Izin berhasil dikirimkan!');
    }

    public function kasbon()
    {
        $user = $this->getUserData();
        $data = [
            'title' => 'Form Pengajuan Kasbon',
            'active' => 'pengajuan_kasbon',
            'user' => [
                'name' => $user['nama_lengkap'] ?? $user['name'] ?? session('username'),
                'jabatan' => $user['jabatan'] ?? 'Staff',
                'divisi' => $user['divisi'] ?? 'General',
            ]
        ];

        return view('staff/templates/header', $data)
             . view('staff/templates/sidebar', $data)
             . view('staff/pengajuan/kasbon', $data)
             . view('staff/templates/footer', $data);
    }

    public function storeKasbon()
    {
        $user = $this->getUserData();
        $karyawanId = $user['karyawan_id'] ?? null;

        if (!$karyawanId) {
            return redirect()->back()->with('error', 'Data Karyawan belum terikat.');
        }

        $jumlahKasbon = (float) str_replace(['.', ','], '', $this->request->getPost('nominal') ?? $this->request->getPost('jumlah_kasbon'));
        $alasan = $this->request->getPost('keperluan') ?? $this->request->getPost('alasan');
        $skema = $this->request->getPost('skema_pengembalian');

        if ($jumlahKasbon <= 0 || empty($alasan)) {
            return redirect()->back()->withInput()->with('error', 'Nominal dan Alasan Kasbon wajib diisi dengan benar.');
        }

        $db = \Config\Database::connect();
        if ($db->tableExists('form_kasbon')) {
            $nomorKasbon = 'KSB-' . date('Ymd') . '-' . rand(1000, 9999);
            $db->table('form_kasbon')->insert([
                'karyawan_id' => $karyawanId,
                'nomor_kasbon' => $nomorKasbon,
                'jumlah_kasbon' => $jumlahKasbon,
                'alasan' => $alasan,
                'rencana_pelunasan' => $skema,
                'tanggal_pengajuan' => date('Y-m-d H:i:s'),
                'status_hrd' => 'Menunggu',
                'status_direktur' => 'Menunggu',
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        return redirect()->to(base_url('staff/pengajuan/riwayat'))->with('success', 'Pengajuan Kasbon sebesar Rp ' . number_format($jumlahKasbon, 0, ',', '.') . ' berhasil dikirimkan!');
    }

    public function riwayat()
    {
        $user = $this->getUserData();
        $karyawanId = $user['karyawan_id'] ?? null;
        $db = \Config\Database::connect();

        $allSubmissions = [];

        // 1. Cuti
        if ($karyawanId && $db->tableExists('cuti')) {
            $cutiList = $db->table('cuti')
                           ->select('id, nomor_cuti as kode, jenis_cuti as kategori, alasan, tanggal_mulai as tanggal, COALESCE(status_hrd, status_direktur, "Menunggu") as status, created_at')
                           ->where('karyawan_id', $karyawanId)
                           ->get()
                           ->getResultArray();
            $allSubmissions = array_merge($allSubmissions, $cutiList);
        }

        // 2. Izin
        if ($karyawanId && $db->tableExists('form_izin')) {
            $izinList = $db->table('form_izin')
                           ->select('id, kode_izin as kode, CONCAT("Izin: ", jenis_izin) as kategori, alasan, tanggal, COALESCE(status_hrd, status_direktur, "Menunggu") as status, created_at')
                           ->where('karyawan_id', $karyawanId)
                           ->get()
                           ->getResultArray();
            $allSubmissions = array_merge($allSubmissions, $izinList);
        }

        // 3. Kasbon
        if ($karyawanId && $db->tableExists('form_kasbon')) {
            $kasbonList = $db->table('form_kasbon')
                             ->select('id, nomor_kasbon as kode, "Kasbon" as kategori, alasan, tanggal_pengajuan as tanggal, COALESCE(status_hrd, status_direktur, "Menunggu") as status, created_at')
                             ->where('karyawan_id', $karyawanId)
                             ->get()
                             ->getResultArray();
            $allSubmissions = array_merge($allSubmissions, $kasbonList);
        }

        usort($allSubmissions, function($a, $b) {
            return strtotime($b['created_at'] ?? 0) - strtotime($a['created_at'] ?? 0);
        });

        $data = [
            'title' => 'Riwayat Pengajuan Saya',
            'active' => 'pengajuan_riwayat',
            'user' => [
                'name' => $user['nama_lengkap'] ?? $user['name'] ?? session('username'),
                'jabatan' => $user['jabatan'] ?? 'Staff',
                'divisi' => $user['divisi'] ?? 'General',
            ],
            'submissions' => $allSubmissions
        ];

        return view('staff/templates/header', $data)
             . view('staff/templates/sidebar', $data)
             . view('staff/pengajuan/riwayat', $data)
             . view('staff/templates/footer', $data);
    }
}
