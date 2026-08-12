<?php

namespace App\Controllers\Accounting;

use App\Controllers\BaseController;

class Kasbon extends BaseController
{
    private function getUserData()
    {
        $session = session();
        $db = \Config\Database::connect();
        $userId = $session->get('user_id');

        return $db->table('users')
                  ->select('users.*, karyawan.nama_lengkap, karyawan.nik, karyawan.jabatan, karyawan.departemen')
                  ->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
                  ->where('users.id', $userId)
                  ->get()
                  ->getRowArray();
    }

    public function index()
    {
        $db = \Config\Database::connect();
        
        $kasbonList = [];
        if ($db->tableExists('form_kasbon')) {
            $kasbonList = $db->table('form_kasbon')
                             ->select('form_kasbon.*, karyawan.nama_lengkap, karyawan.nik, karyawan.jabatan, karyawan.departemen')
                             ->join('karyawan', 'karyawan.id = form_kasbon.karyawan_id', 'left')
                             ->orderBy('form_kasbon.created_at', 'DESC')
                             ->get()
                             ->getResultArray();
        }

        $totalApproved = 0;
        $totalPending = 0;
        foreach ($kasbonList as $k) {
            $status = $k['status_direktur'] ?? $k['status_hrd'] ?? 'Menunggu';
            if (in_array(strtolower($status), ['disetujui', 'approved', 'acc'])) {
                $totalApproved += (float) ($k['jumlah_kasbon'] ?? 0);
            } else if (in_array(strtolower($status), ['menunggu', 'pending'])) {
                $totalPending += (float) ($k['jumlah_kasbon'] ?? 0);
            }
        }

        $data = [
            'title' => 'Daftar Kasbon Karyawan',
            'active' => 'kasbon',
            'kasbonList' => $kasbonList,
            'totalApproved' => $totalApproved,
            'totalPending' => $totalPending,
            'user' => $this->getUserData()
        ];

        return view('accounting/templates/header', $data)
             . view('accounting/templates/sidebar', $data)
             . view('accounting/kasbon/index', $data)
             . view('accounting/templates/footer', $data);
    }

    public function potongGaji()
    {
        $db = \Config\Database::connect();
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan') ?? date('m');

        $potonganList = [];
        if ($db->tableExists('form_kasbon')) {
            $potonganList = $db->table('form_kasbon')
                               ->select('form_kasbon.*, karyawan.nama_lengkap, karyawan.nik, karyawan.jabatan, karyawan.departemen')
                               ->join('karyawan', 'karyawan.id = form_kasbon.karyawan_id', 'left')
                               ->where('LOWER(status_direktur)', 'disetujui')
                               ->orWhere('LOWER(status_hrd)', 'disetujui')
                               ->orderBy('form_kasbon.created_at', 'DESC')
                               ->get()
                               ->getResultArray();
        }

        $data = [
            'title' => 'Proses Potong Gaji Kasbon',
            'active' => 'kasbon-potong',
            'tahun' => $tahun,
            'bulan' => $bulan,
            'potonganList' => $potonganList,
            'user' => $this->getUserData()
        ];

        return view('accounting/templates/header', $data)
             . view('accounting/templates/sidebar', $data)
             . view('accounting/kasbon/potong', $data)
             . view('accounting/templates/footer', $data);
    }

    public function prosesPotong($id)
    {
        $db = \Config\Database::connect();
        if ($db->tableExists('form_kasbon')) {
            $db->table('form_kasbon')
               ->where('id', $id)
               ->update([
                   'status_keseluruhan' => 'Lunas',
                   'updated_at' => date('Y-m-d H:i:s')
               ]);
        }

        return redirect()->to(base_url('accounting/kasbon/potong-gaji'))->with('success', 'Status potongan kasbon telah berhasil diperbarui menjadi Lunas.');
    }
}
