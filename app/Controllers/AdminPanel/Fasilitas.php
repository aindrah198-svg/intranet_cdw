<?php

namespace App\Controllers\AdminPanel;

use App\Controllers\BaseController;

class Fasilitas extends BaseController
{
    private function check()
    {
        if (!session()->get('isLoggedIn')) return redirect()->to(base_url('login'));
        if (strtolower(session()->get('role') ?? '') !== 'admin') return redirect()->to($this->getDashboardUrl())->with('error', 'Akses ditolak!');
        return null;
    }

    public function bukuTamu()
    {
        if ($r = $this->check()) return $r;
        return view('admin_panel/fasilitas/buku_tamu', ['title' => 'Buku Tamu', 'active' => 'buku-tamu', 'user' => ['name' => session()->get('name'), 'role' => session()->get('role')]]);
    }

    public function bookingRuang()
    {
        if ($r = $this->check()) return $r;
        return view('admin_panel/fasilitas/booking_ruang', ['title' => 'Booking Ruang Meeting', 'active' => 'booking-ruang', 'user' => ['name' => session()->get('name'), 'role' => session()->get('role')]]);
    }

    public function kendaraan()
    {
        if ($r = $this->check()) return $r;
        return view('admin_panel/fasilitas/kendaraan', ['title' => 'Koordinasi Kendaraan', 'active' => 'kendaraan', 'user' => ['name' => session()->get('name'), 'role' => session()->get('role')]]);
    }
}
