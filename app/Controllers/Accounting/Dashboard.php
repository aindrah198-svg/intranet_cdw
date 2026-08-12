<?php

namespace App\Controllers\Accounting;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu.');
        }

        $db = \Config\Database::connect();

        // Count real metrics from database
        $totalKasbon = 0;
        if ($db->tableExists('form_kasbon')) {
            $q = $db->table('form_kasbon')->whereIn('status_direktur', ['Menunggu', 'pending', 'menunggu']);
            if ($db->fieldExists('deleted_at', 'form_kasbon')) $q->where('deleted_at', null);
            $totalKasbon = $q->countAllResults();
        }

        $totalPembelian = 0;
        if ($db->tableExists('pembelian')) {
            $q = $db->table('pembelian')->whereIn('status', ['pending', 'menunggu']);
            if ($db->fieldExists('deleted_at', 'pembelian')) $q->where('deleted_at', null);
            $totalPembelian = $q->countAllResults();
        }

        $totalKaryawan = 0;
        if ($db->tableExists('karyawan')) {
            $q = $db->table('karyawan');
            if ($db->fieldExists('deleted_at', 'karyawan')) $q->where('deleted_at', null);
            $totalKaryawan = $q->countAllResults();
        }

        $totalCOA = 0;
        if ($db->tableExists('coa')) {
            $totalCOA = $db->table('coa')->countAllResults();
        }

        $userData = [
            'user_id' => $session->get('user_id'),
            'name' => $session->get('name') ?? 'Accounting Staff',
            'username' => $session->get('username') ?? 'accounting',
            'email' => $session->get('email') ?? '',
            'role' => $session->get('role') ?? 'Accounting',
            'karyawan_id' => $session->get('karyawan_id') ?? null
        ];

        $karyawanData = [
            'nik' => $session->get('nik') ?? 'AC-001',
            'nama_lengkap' => $session->get('name') ?? 'Accounting Staff',
            'nama_panggilan' => $session->get('username') ?? 'Accounting',
            'jabatan' => 'Accounting Staff',
            'departemen' => 'Finance & Accounting',
            'divisi' => 'Finance',
            'email' => $session->get('email') ?? ''
        ];

        $data = [
            'title'          => 'Dashboard Accounting',
            'subtitle'       => date('l, d F Y'),
            'active'         => 'dashboard',
            'activeMenu'     => 'dashboard',
            'user'           => $userData,
            'karyawan'       => $karyawanData,
            'totalKasbon'    => $totalKasbon,
            'totalPembelian' => $totalPembelian,
            'totalKaryawan'  => $totalKaryawan,
            'totalCOA'       => $totalCOA
        ];

        return view('accounting/dashboard/index', $data);
    }
}