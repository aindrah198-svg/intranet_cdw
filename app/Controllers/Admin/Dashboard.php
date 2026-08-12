<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = strtolower(session()->get('role') ?? '');
        if ($role !== 'admin') {
            return redirect()->to($this->getDashboardUrl())->with('error', 'Akses ditolak!');
        }

        $data = [
            'title'    => 'Admin Panel - CDW Engineering',
            'subtitle' => 'Dashboard Administrasi',
            'active'   => 'dashboard',
            'user'     => [
                'name'  => session()->get('name') ?? 'Administrator',
                'role'  => session()->get('role') ?? 'admin',
            ],
        ];

        return view('admin/dashboard/index', $data);
    }
}