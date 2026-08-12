<?php

namespace App\Controllers\AdminPanel;

use App\Controllers\BaseController;

/**
 * Menu Pribadi untuk Admin
 * Absensi Saya, Tugas Saya, Laporan Harian, Keluhan Saya, Form Pengajuan, Slip Gaji, Profil
 */
class Pribadi extends BaseController
{
    private function check()
    {
        if (!session()->get('isLoggedIn')) return redirect()->to(base_url('login'));
        if (strtolower(session()->get('role') ?? '') !== 'admin') return redirect()->to($this->getDashboardUrl())->with('error', 'Akses ditolak!');
        return null;
    }

    public function absensi()
    {
        if ($r = $this->check()) return $r;
        return view('admin_panel/pribadi/absensi', ['title' => 'Absensi Saya', 'active' => 'absensi-saya', 'user' => ['name' => session()->get('name'), 'role' => session()->get('role')]]);
    }

    public function checkin()
    {
        if ($r = $this->check()) return $r;
        return redirect()->to(base_url('admin/absensi-saya'))->with('success', 'Check-in berhasil!');
    }

    public function checkout()
    {
        if ($r = $this->check()) return $r;
        return redirect()->to(base_url('admin/absensi-saya'))->with('success', 'Check-out berhasil!');
    }

    public function tugas()
    {
        if ($r = $this->check()) return $r;
        return view('admin_panel/pribadi/tugas', ['title' => 'Tugas Saya', 'active' => 'tugas-saya', 'user' => ['name' => session()->get('name'), 'role' => session()->get('role')]]);
    }

    public function laporanHarian()
    {
        if ($r = $this->check()) return $r;
        return view('admin_panel/pribadi/laporan_harian', ['title' => 'Laporan Kerja Harian', 'active' => 'laporan-harian-saya', 'user' => ['name' => session()->get('name'), 'role' => session()->get('role')]]);
    }

    public function storeLaporan()
    {
        if ($r = $this->check()) return $r;
        return redirect()->to(base_url('admin/laporan-harian-saya'))->with('success', 'Laporan berhasil disimpan!');
    }

    public function keluhan()
    {
        if ($r = $this->check()) return $r;
        return view('admin_panel/pribadi/keluhan', ['title' => 'Keluhan Saya', 'active' => 'keluhan-saya', 'user' => ['name' => session()->get('name'), 'role' => session()->get('role')]]);
    }

    public function storeKeluhan()
    {
        if ($r = $this->check()) return $r;
        return redirect()->to(base_url('admin/keluhan-saya'))->with('success', 'Keluhan berhasil dikirim!');
    }

    public function pengajuanCuti()
    {
        if ($r = $this->check()) return $r;
        return view('admin_panel/pribadi/pengajuan_cuti', ['title' => 'Form Pengajuan Cuti', 'active' => 'form-pengajuan', 'user' => ['name' => session()->get('name'), 'role' => session()->get('role')]]);
    }

    public function storeCuti()
    {
        if ($r = $this->check()) return $r;
        return redirect()->to(base_url('admin/form-pengajuan/cuti'))->with('success', 'Pengajuan cuti berhasil dikirim!');
    }

    public function slipGaji()
    {
        if ($r = $this->check()) return $r;
        return view('admin_panel/pribadi/slip_gaji', ['title' => 'Slip Gaji', 'active' => 'slip-gaji', 'user' => ['name' => session()->get('name'), 'role' => session()->get('role')]]);
    }

    public function profil()
    {
        if ($r = $this->check()) return $r;
        return view('admin_panel/pribadi/profil', ['title' => 'Profil Saya', 'active' => 'profil', 'user' => ['name' => session()->get('name'), 'role' => session()->get('role')]]);
    }

    public function updateProfil()
    {
        if ($r = $this->check()) return $r;
        return redirect()->to(base_url('admin/profil'))->with('success', 'Profil berhasil diupdate!');
    }
}
