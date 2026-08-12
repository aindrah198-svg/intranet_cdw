<?php

namespace App\Controllers\AdminPanel;

use App\Controllers\BaseController;

class Laporan extends BaseController
{
    private function check()
    {
        if (!session()->get('isLoggedIn')) return redirect()->to(base_url('login'));
        if (strtolower(session()->get('role') ?? '') !== 'admin') return redirect()->to($this->getDashboardUrl())->with('error', 'Akses ditolak!');
        return null;
    }

    public function dashboard()
    {
        if ($r = $this->check()) return $r;
        return view('admin_panel/laporan/dashboard', ['title' => 'Dashboard Laporan', 'active' => 'laporan-dashboard', 'user' => ['name' => session()->get('name'), 'role' => session()->get('role')]]);
    }

    public function kerjaHarian()
    {
        if ($r = $this->check()) return $r;
        return view('admin_panel/laporan/kerja_harian', ['title' => 'Laporan Kerja Harian', 'active' => 'laporan-kerja', 'user' => ['name' => session()->get('name'), 'role' => session()->get('role')]]);
    }

    public function keluhan()
    {
        if ($r = $this->check()) return $r;
        return view('admin_panel/laporan/keluhan', ['title' => 'Keluhan', 'active' => 'laporan-keluhan', 'user' => ['name' => session()->get('name'), 'role' => session()->get('role')]]);
    }
}
