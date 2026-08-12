<?php

namespace App\Controllers\AdminPanel;

use App\Controllers\BaseController;

/**
 * Surat Menyurat Controller
 * Menangani: Surat Masuk, Surat Keluar, Template Surat
 */
class Surat extends BaseController
{
    private function checkAccess()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        $role = strtolower(session()->get('role') ?? '');
        if ($role !== 'admin') {
            return redirect()->to($this->getDashboardUrl())->with('error', 'Akses ditolak!');
        }
        return null;
    }

    public function masuk()
    {
        if ($redirect = $this->checkAccess()) return $redirect;
        $data = [
            'title'    => 'Surat Masuk',
            'subtitle' => 'Manajemen Surat Masuk',
            'active'   => 'surat-masuk',
            'user'     => ['name' => session()->get('name'), 'role' => session()->get('role')],
        ];
        return view('admin_panel/surat/masuk', $data);
    }

    public function keluar()
    {
        if ($redirect = $this->checkAccess()) return $redirect;
        $data = [
            'title'    => 'Surat Keluar',
            'subtitle' => 'Manajemen Surat Keluar',
            'active'   => 'surat-keluar',
            'user'     => ['name' => session()->get('name'), 'role' => session()->get('role')],
        ];
        return view('admin_panel/surat/keluar', $data);
    }

    public function template()
    {
        if ($redirect = $this->checkAccess()) return $redirect;
        $data = [
            'title'    => 'Template Surat',
            'subtitle' => 'Manajemen Template Surat',
            'active'   => 'surat-template',
            'user'     => ['name' => session()->get('name'), 'role' => session()->get('role')],
        ];
        return view('admin_panel/surat/template', $data);
    }
}
