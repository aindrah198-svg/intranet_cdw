<?php

namespace App\Controllers\Staff;

use App\Controllers\BaseController;

class Profil extends BaseController
{
    private function getUserData()
    {
        $session = session();
        $db = \Config\Database::connect();
        $userId = $session->get('user_id');

        return $db->table('users')
                  ->select('users.*, karyawan.*, karyawan.id as karyawan_id')
                  ->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
                  ->where('users.id', $userId)
                  ->get()
                  ->getRowArray();
    }

    public function index()
    {
        $userData = $this->getUserData();

        $data = [
            'title' => 'Profil Saya',
            'active' => 'profil',
            'user' => [
                'name' => $userData['nama_lengkap'] ?? $userData['name'] ?? session('username'),
                'jabatan' => $userData['jabatan'] ?? 'Staff',
                'divisi' => $userData['divisi'] ?? 'General',
                'nik' => $userData['nik'] ?? '-'
            ],
            'detail' => $userData
        ];

        return view('staff/templates/header', $data)
             . view('staff/templates/sidebar', $data)
             . view('staff/profil/index', $data)
             . view('staff/templates/footer', $data);
    }

    public function update()
    {
        $userData = $this->getUserData();
        $karyawanId = $userData['karyawan_id'] ?? null;

        if (!$karyawanId) {
            return redirect()->back()->with('error', 'Data karyawan tidak ditemukan.');
        }

        $noTelepon = $this->request->getPost('no_telepon');
        $alamat = $this->request->getPost('alamat');
        $kontakDarurat = $this->request->getPost('kontak_darurat');

        $db = \Config\Database::connect();
        $db->table('karyawan')
           ->where('id', $karyawanId)
           ->update([
               'no_telepon' => $noTelepon,
               'alamat' => $alamat,
               'updated_at' => date('Y-m-d H:i:s')
           ]);

        return redirect()->to(base_url('staff/profil'))->with('success', 'Data profil pribadi berhasil diperbarui!');
    }

    public function dokumen()
    {
        $userData = $this->getUserData();
        $karyawanId = $userData['karyawan_id'] ?? null;
        $db = \Config\Database::connect();

        $dokumenList = [];
        if ($karyawanId && $db->tableExists('dokumen')) {
            $dokumenList = $db->table('dokumen')
                             ->where('karyawan_id', $karyawanId)
                             ->orderBy('created_at', 'DESC')
                             ->get()
                             ->getResultArray();
        }

        if (empty($dokumenList) && $karyawanId && $db->tableExists('form_dokumen')) {
            $dokumenList = $db->table('form_dokumen')
                             ->where('karyawan_id', $karyawanId)
                             ->orderBy('created_at', 'DESC')
                             ->get()
                             ->getResultArray();
        }

        $data = [
            'title' => 'Dokumen Saya',
            'active' => 'dokumen',
            'user' => [
                'name' => $userData['nama_lengkap'] ?? $userData['name'] ?? session('username'),
                'jabatan' => $userData['jabatan'] ?? 'Staff',
                'divisi' => $userData['divisi'] ?? 'General',
                'nik' => $userData['nik'] ?? '-'
            ],
            'dokumenList' => $dokumenList
        ];

        return view('staff/templates/header', $data)
             . view('staff/templates/sidebar', $data)
             . view('staff/profil/dokumen', $data)
             . view('staff/templates/footer', $data);
    }
}
