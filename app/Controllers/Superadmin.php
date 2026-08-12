<?php
namespace App\Controllers;

class Superadmin extends BaseController
{
    private function checkAccess()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu.');
        }

        $role = strtolower(session()->get('role') ?? '');
        if (!in_array($role, ['superadmin', 'super_admin', 'super admin'])) {
            return redirect()->to(base_url('login'))->with('error', 'Anda tidak memiliki hak akses Superadmin.');
        }

        return null;
    }

    public function index()
    {
        if ($r = $this->checkAccess()) return $r;

        return $this->welcome();
    }

    public function welcome()
    {
        if ($r = $this->checkAccess()) return $r;

        $db = \Config\Database::connect();
        
        $totalUsers    = $db->tableExists('users') ? $db->table('users')->where('deleted_at', null)->countAllResults() : 0;
        $totalKaryawan = $db->tableExists('karyawan') ? $db->table('karyawan')->countAllResults() : 0;
        $totalProyek   = $db->tableExists('project') ? $db->table('project')->countAllResults() : 0;

        $data = [
            'title'         => 'Halaman Selamat Datang Superadmin',
            'user'          => [
                'name'     => session()->get('name') ?? 'Super Administrator',
                'username' => session()->get('username') ?? 'superadmin',
                'role'     => 'superadmin'
            ],
            'totalUsers'    => $totalUsers,
            'totalKaryawan' => $totalKaryawan,
            'totalProyek'   => $totalProyek
        ];

        return view('superadmin/welcome', $data);
    }

    public function cetakPdf()
    {
        if ($r = $this->checkAccess()) return $r;

        $db = \Config\Database::connect();
        
        $totalUsers    = $db->tableExists('users') ? $db->table('users')->where('deleted_at', null)->countAllResults() : 0;
        $totalKaryawan = $db->tableExists('karyawan') ? $db->table('karyawan')->countAllResults() : 0;
        $totalProyek   = $db->tableExists('project') ? $db->table('project')->countAllResults() : 0;

        $data = [
            'title'         => 'Laporan Rekapitulasi Pembaruan Fitur System Intranet CDW',
            'user'          => [
                'name'     => session()->get('name') ?? 'Super Administrator',
                'username' => session()->get('username') ?? 'superadmin',
                'role'     => 'superadmin'
            ],
            'totalUsers'    => $totalUsers,
            'totalKaryawan' => $totalKaryawan,
            'totalProyek'   => $totalProyek,
            'printDate'     => date('d F Y H:i')
        ];

        return view('superadmin/cetak_pdf', $data);
    }

    public function flowDirektur()
    {
        if ($r = $this->checkAccess()) return $r;

        $data = [
            'title'     => 'Flow Sistem Direktur (Executive Workflow)',
            'activeNav' => 'flow-direktur',
            'user'      => [
                'name'     => session()->get('name') ?? 'Super Administrator',
                'username' => session()->get('username') ?? 'superadmin',
                'role'     => 'superadmin'
            ]
        ];

        return view('superadmin/flow_direktur', $data);
    }

    public function flowAdmin()
    {
        if ($r = $this->checkAccess()) return $r;

        $data = [
            'title'     => 'Flow Sistem Admin (Operations Workflow)',
            'activeNav' => 'flow-admin',
            'user'      => [
                'name'     => session()->get('name') ?? 'Super Administrator',
                'username' => session()->get('username') ?? 'superadmin',
                'role'     => 'superadmin'
            ]
        ];

        return view('superadmin/flow_admin', $data);
    }
}
