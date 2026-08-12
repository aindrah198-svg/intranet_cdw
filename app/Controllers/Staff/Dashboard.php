<?php

namespace App\Controllers\Staff;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $db = \Config\Database::connect();
        $userId = $session->get('user_id');
        
        $user = $db->table('users')
                   ->select('users.*, karyawan.nama_lengkap, karyawan.nik, karyawan.jabatan, karyawan.divisi, karyawan.id as karyawan_id')
                   ->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
                   ->where('users.id', $userId)
                   ->get()
                   ->getRowArray();

        $karyawanId = $user['karyawan_id'] ?? null;
        $today = date('Y-m-d');

        // Status Absensi Hari Ini
        $absensiHariIni = null;
        if ($karyawanId && $db->tableExists('absensi')) {
            $absensiHariIni = $db->table('absensi')
                                 ->where('karyawan_id', $karyawanId)
                                 ->where('tanggal', $today)
                                 ->get()
                                 ->getRowArray();
        }

        // Tugas Hari Ini (Penugasan Harian)
        $tugasHariIni = [];
        if ($db->tableExists('penugasan_harian')) {
            $tugasHariIni = $db->table('penugasan_harian')
                               ->where('penerima_id', $userId)
                               ->orWhere('penerima_id', $karyawanId)
                               ->orderBy('created_at', 'DESC')
                               ->get()
                               ->getResultArray();
        }

        // Sisa Kuota Cuti
        $sisaCuti = 12;
        if ($karyawanId && $db->tableExists('kuota_cuti')) {
            $kuota = $db->table('kuota_cuti')
                        ->where('karyawan_id', $karyawanId)
                        ->where('tahun', date('Y'))
                        ->get()
                        ->getRowArray();
            if ($kuota) {
                $sisaCuti = $kuota['sisa'] ?? ($kuota['kuota_tahunan'] - $kuota['terpakai']);
            }
        }

        // Pengajuan Terakhir (Cuti / Izin / Kasbon)
        $pengajuanTerakhir = [];
        if ($karyawanId && $db->tableExists('cuti')) {
            $cutiList = $db->table('cuti')
                           ->select('id, jenis_cuti as kategori, alasan, created_at, COALESCE(status_hrd, status_direktur, "Menunggu") as status')
                           ->where('karyawan_id', $karyawanId)
                           ->orderBy('created_at', 'DESC')
                           ->limit(5)
                           ->get()
                           ->getResultArray();
            $pengajuanTerakhir = array_merge($pengajuanTerakhir, $cutiList);
        }

        if ($karyawanId && $db->tableExists('form_kasbon')) {
            $kasbonList = $db->table('form_kasbon')
                             ->select('id, "Kasbon" as kategori, alasan, created_at, COALESCE(status_hrd, status_direktur, "Menunggu") as status')
                             ->where('karyawan_id', $karyawanId)
                             ->orderBy('created_at', 'DESC')
                             ->limit(5)
                             ->get()
                             ->getResultArray();
            $pengajuanTerakhir = array_merge($pengajuanTerakhir, $kasbonList);
        }

        usort($pengajuanTerakhir, function($a, $b) {
            return strtotime($b['created_at'] ?? 0) - strtotime($a['created_at'] ?? 0);
        });

        $data = [
            'title' => 'Dashboard Staff',
            'active' => 'dashboard',
            'user' => [
                'name' => $user['nama_lengkap'] ?? $user['name'] ?? $session->get('username'),
                'jabatan' => $user['jabatan'] ?? 'Staff',
                'divisi' => $user['divisi'] ?? 'General',
                'nik' => $user['nik'] ?? '-'
            ],
            'absensiHariIni' => $absensiHariIni,
            'tugasHariIni' => $tugasHariIni,
            'sisaCuti' => $sisaCuti,
            'pengajuanTerakhir' => array_slice($pengajuanTerakhir, 0, 5)
        ];

        return view('staff/templates/header', $data)
             . view('staff/templates/sidebar', $data)
             . view('staff/dashboard/index', $data)
             . view('staff/templates/footer', $data);
    }
}
