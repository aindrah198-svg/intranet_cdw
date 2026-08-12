<?php

namespace App\Controllers\AdminPanel;

use App\Controllers\BaseController;

/**
 * Inventaris & ATK Controller
 */
class Inventaris extends BaseController
{
    private function checkAccess()
    {
        if (!session()->get('isLoggedIn')) return redirect()->to(base_url('login'));
        $role = strtolower(session()->get('role') ?? '');
        if ($role !== 'admin') return redirect()->to($this->getDashboardUrl())->with('error', 'Akses ditolak!');
        return null;
    }

    public function stokAtk()
    {
        if ($r = $this->checkAccess()) return $r;
        return view('admin_panel/inventaris/stok_atk', ['title' => 'Stok ATK', 'active' => 'stok-atk', 'user' => ['name' => session()->get('name'), 'role' => session()->get('role')]]);
    }

    public function pengajuanAtk()
    {
        if ($r = $this->checkAccess()) return $r;
        return view('admin_panel/inventaris/pengajuan_atk', ['title' => 'Pengajuan Pembelian ATK', 'active' => 'pengajuan-atk', 'user' => ['name' => session()->get('name'), 'role' => session()->get('role')]]);
    }

    public function inventarisKantor()
    {
        if ($r = $this->checkAccess()) return $r;
        return view('admin_panel/inventaris/inventaris_kantor', ['title' => 'Inventaris Kantor', 'active' => 'inventaris-kantor', 'user' => ['name' => session()->get('name'), 'role' => session()->get('role')]]);
    }
}
