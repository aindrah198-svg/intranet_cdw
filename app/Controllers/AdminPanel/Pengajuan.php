<?php

namespace App\Controllers\AdminPanel;

use App\Controllers\BaseController;

class Pengajuan extends BaseController
{
    private function check()
    {
        if (!session()->get('isLoggedIn')) return redirect()->to(base_url('login'));
        if (strtolower(session()->get('role') ?? '') !== 'admin') return redirect()->to($this->getDashboardUrl())->with('error', 'Akses ditolak!');
        return null;
    }

    public function semua()
    {
        if ($r = $this->check()) return $r;
        return view('admin_panel/pengajuan/semua', ['title' => 'Semua Pengajuan', 'active' => 'pengajuan-semua', 'user' => ['name' => session()->get('name'), 'role' => session()->get('role')]]);
    }

    public function cuti()
    {
        if ($r = $this->check()) return $r;
        return view('admin_panel/pengajuan/cuti', ['title' => 'Pengajuan Cuti', 'active' => 'pengajuan-cuti', 'user' => ['name' => session()->get('name'), 'role' => session()->get('role')]]);
    }
}
