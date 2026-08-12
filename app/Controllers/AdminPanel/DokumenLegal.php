<?php

namespace App\Controllers\AdminPanel;

use App\Controllers\BaseController;

class DokumenLegal extends BaseController
{
    private function check()
    {
        if (!session()->get('isLoggedIn')) return redirect()->to(base_url('login'));
        if (strtolower(session()->get('role') ?? '') !== 'admin') return redirect()->to($this->getDashboardUrl())->with('error', 'Akses ditolak!');
        return null;
    }

    public function index()
    {
        if ($r = $this->check()) return $r;
        return view('admin_panel/dokumen_legal/index', ['title' => 'Dokumen Legal', 'active' => 'dokumen-legal', 'user' => ['name' => session()->get('name'), 'role' => session()->get('role')]]);
    }

    public function arsip()
    {
        if ($r = $this->check()) return $r;
        return view('admin_panel/dokumen_legal/arsip', ['title' => 'Arsip Dokumen', 'active' => 'arsip-dokumen', 'user' => ['name' => session()->get('name'), 'role' => session()->get('role')]]);
    }
}
