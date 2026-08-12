<?php

namespace App\Controllers\Staff;

use App\Controllers\BaseController;

class Tugas extends BaseController
{
    public function index()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $userId = $session->get('user_id');
        $db = \Config\Database::connect();

        $user = $db->table('users')
                   ->select('users.*, karyawan.nama_lengkap, karyawan.nik, karyawan.jabatan, karyawan.divisi, karyawan.id as karyawan_id')
                   ->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
                   ->where('users.id', $userId)
                   ->get()
                   ->getRowArray();

        $karyawanId = $user['karyawan_id'] ?? null;

        $tugas = [];
        if ($db->tableExists('penugasan_harian')) {
            $builder = $db->table('penugasan_harian');
            $builder->groupStart()
                    ->where('penerima_id', $userId)
                    ->orWhere('penerima_id', $karyawanId)
                    ->groupEnd();
            $tugas = $builder->orderBy('created_at', 'DESC')->get()->getResultArray();
        }

        $data = [
            'title' => 'Tugas Saya',
            'active' => 'tugas',
            'user' => [
                'name' => $user['nama_lengkap'] ?? $user['name'] ?? session('username'),
                'jabatan' => $user['jabatan'] ?? 'Staff',
                'divisi' => $user['divisi'] ?? 'General',
            ],
            'tugas' => $tugas
        ];

        return view('staff/templates/header', $data)
             . view('staff/templates/sidebar', $data)
             . view('staff/tugas/index', $data)
             . view('staff/templates/footer', $data);
    }

    public function updateStatus()
    {
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');

        if (!$id || !in_array($status, ['pending', 'proses', 'selesai', 'ditunda'])) {
            return redirect()->back()->with('error', 'Data status tugas tidak valid.');
        }

        $db = \Config\Database::connect();
        if ($db->tableExists('penugasan_harian')) {
            $db->table('penugasan_harian')
               ->where('id', $id)
               ->update([
                   'status' => $status,
                   'updated_at' => date('Y-m-d H:i:s')
               ]);
        }

        return redirect()->to(base_url('staff/tugas'))->with('success', 'Status tugas berhasil diperbarui menjadi: ' . ucfirst($status));
    }
}
