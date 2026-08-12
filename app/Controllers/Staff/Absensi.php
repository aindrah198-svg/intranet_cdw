<?php

namespace App\Controllers\Staff;

use App\Controllers\BaseController;

class Absensi extends BaseController
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
        $today = date('Y-m-d');

        $absensiHariIni = null;
        if ($karyawanId && $db->tableExists('absensi')) {
            $absensiHariIni = $db->table('absensi')
                                 ->where('karyawan_id', $karyawanId)
                                 ->where('tanggal', $today)
                                 ->get()
                                 ->getRowArray();
        }

        $data = [
            'title' => 'Absen Masuk/Pulang - Staff',
            'active' => 'absensi_checkin',
            'user' => [
                'name' => $user['nama_lengkap'] ?? $user['name'] ?? session('username'),
                'jabatan' => $user['jabatan'] ?? 'Staff',
                'divisi' => $user['divisi'] ?? 'General',
            ],
            'karyawanId' => $karyawanId,
            'absensiHariIni' => $absensiHariIni
        ];

        return view('staff/templates/header', $data)
             . view('staff/templates/sidebar', $data)
             . view('staff/absensi/checkin', $data)
             . view('staff/templates/footer', $data);
    }

    public function checkin()
    {
        $user = $this->getUserData();
        $karyawanId = $user['karyawan_id'] ?? null;

        if (!$karyawanId) {
            return redirect()->back()->with('error', 'Akun Anda belum terikat dengan Data Karyawan. Hubungi HRD.');
        }

        $db = \Config\Database::connect();
        $today = date('Y-m-d');
        $now = date('H:i:s');

        // Check if already checked in today
        $existing = $db->table('absensi')
                       ->where('karyawan_id', $karyawanId)
                       ->where('tanggal', $today)
                       ->get()
                       ->getRowArray();

        if ($existing) {
            return redirect()->back()->with('info', 'Anda sudah melakukan Absen Masuk hari ini.');
        }

        $lokasiMasuk = $this->request->getPost('lokasi_masuk') ?? 'Kantor CDW Engineering';
        $statusHadir = (strtotime($now) > strtotime('08:30:00')) ? 'Terlambat' : 'Hadir';

        $db->table('absensi')->insert([
            'karyawan_id' => $karyawanId,
            'tanggal' => $today,
            'waktu_masuk' => $now,
            'status' => $statusHadir,
            'lokasi_masuk' => $lokasiMasuk,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('staff/absensi'))->with('success', 'Absen Masuk Berhasil! Jam: ' . $now);
    }

    public function checkout()
    {
        $user = $this->getUserData();
        $karyawanId = $user['karyawan_id'] ?? null;

        if (!$karyawanId) {
            return redirect()->back()->with('error', 'Data Karyawan tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $today = date('Y-m-d');
        $now = date('H:i:s');

        $existing = $db->table('absensi')
                       ->where('karyawan_id', $karyawanId)
                       ->where('tanggal', $today)
                       ->get()
                       ->getRowArray();

        if (!$existing) {
            return redirect()->back()->with('error', 'Anda belum melakukan Absen Masuk hari ini.');
        }

        if (!empty($existing['waktu_keluar'])) {
            return redirect()->back()->with('info', 'Anda sudah melakukan Absen Pulang hari ini.');
        }

        $lokasiKeluar = $this->request->getPost('lokasi_keluar') ?? 'Kantor CDW Engineering';

        $db->table('absensi')
           ->where('id', $existing['id'])
           ->update([
               'waktu_keluar' => $now,
               'lokasi_keluar' => $lokasiKeluar,
               'updated_at' => date('Y-m-d H:i:s')
           ]);

        return redirect()->to(base_url('staff/absensi'))->with('success', 'Absen Pulang Berhasil! Jam: ' . $now);
    }

    public function riwayat()
    {
        $user = $this->getUserData();
        $karyawanId = $user['karyawan_id'] ?? null;
        $db = \Config\Database::connect();

        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $riwayat = [];
        if ($karyawanId && $db->tableExists('absensi')) {
            $riwayat = $db->table('absensi')
                          ->where('karyawan_id', $karyawanId)
                          ->where('MONTH(tanggal)', $bulan)
                          ->where('YEAR(tanggal)', $tahun)
                          ->orderBy('tanggal', 'DESC')
                          ->get()
                          ->getResultArray();
        }

        $data = [
            'title' => 'Riwayat Absensi Saya',
            'active' => 'absensi_riwayat',
            'user' => [
                'name' => $user['nama_lengkap'] ?? $user['name'] ?? session('username'),
                'jabatan' => $user['jabatan'] ?? 'Staff',
                'divisi' => $user['divisi'] ?? 'General',
            ],
            'riwayat' => $riwayat,
            'bulan' => $bulan,
            'tahun' => $tahun
        ];

        return view('staff/templates/header', $data)
             . view('staff/templates/sidebar', $data)
             . view('staff/absensi/riwayat', $data)
             . view('staff/templates/footer', $data);
    }
}
