<?php

namespace App\Controllers\Staff;

use App\Controllers\BaseController;

class Payroll extends BaseController
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

        $payrollList = [];
        if ($karyawanId && $db->tableExists('ringkasan_penggajian')) {
            $payrollList = $db->table('ringkasan_penggajian')
                             ->where('karyawan_id', $karyawanId)
                             ->orderBy('periode_tahun', 'DESC')
                             ->orderBy('periode_bulan', 'DESC')
                             ->get()
                             ->getResultArray();
        }

        $data = [
            'title' => 'Slip Gaji Saya',
            'active' => 'payroll',
            'user' => [
                'name' => $user['nama_lengkap'] ?? $user['name'] ?? session('username'),
                'jabatan' => $user['jabatan'] ?? 'Staff',
                'divisi' => $user['divisi'] ?? 'General',
                'nik' => $user['nik'] ?? '-'
            ],
            'payrollList' => $payrollList
        ];

        return view('staff/templates/header', $data)
             . view('staff/templates/sidebar', $data)
             . view('staff/payroll/index', $data)
             . view('staff/templates/footer', $data);
    }

    public function cetak($id)
    {
        $user = $this->getUserData();
        $karyawanId = $user['karyawan_id'] ?? null;
        $db = \Config\Database::connect();

        $slip = null;
        if ($karyawanId && $db->tableExists('ringkasan_penggajian')) {
            $slip = $db->table('ringkasan_penggajian')
                       ->select('ringkasan_penggajian.*, karyawan.nama_lengkap, karyawan.nik, karyawan.jabatan, karyawan.divisi')
                       ->join('karyawan', 'karyawan.id = ringkasan_penggajian.karyawan_id', 'left')
                       ->where('ringkasan_penggajian.id', $id)
                       ->where('ringkasan_penggajian.karyawan_id', $karyawanId)
                       ->get()
                       ->getRowArray();
        }

        if (!$slip) {
            return redirect()->to(base_url('staff/payroll'))->with('error', 'Slip gaji tidak ditemukan.');
        }

        $data = [
            'title' => 'Cetak Slip Gaji - ' . ($slip['nama_lengkap'] ?? 'Staff'),
            'slip' => $slip
        ];

        return view('staff/payroll/cetak', $data);
    }
}
