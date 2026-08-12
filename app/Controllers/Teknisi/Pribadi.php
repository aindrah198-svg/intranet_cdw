<?php

namespace App\Controllers\Teknisi;

class Pribadi extends TeknisiController
{
    public function absensi()
    {
        return redirect()->to(site_url('teknisi/absensi'));
    }

    public function tugas()
    {
        return redirect()->to(site_url('teknisi/tugas-proyek/spk'));
    }

    public function laporanHarian()
    {
        return redirect()->to(site_url('teknisi/laporan/lapangan'));
    }

    public function keluhan()
    {
        return redirect()->to(site_url('teknisi/laporan/keluhan'));
    }

    public function pengajuan()
    {
        return redirect()->to(site_url('teknisi/pengajuan'));
    }

    public function slipGaji()
    {
        $db = \Config\Database::connect();
        $teknisiId = $this->karyawanData['id'] ?? $this->userId;

        $slipGajiList = [];
        if ($db->tableExists('penggajian_perhitungan')) {
            $slipGajiList = $db->table('penggajian_perhitungan')
                ->where('karyawan_id', $teknisiId)
                ->where('status', 'Disetujui')
                ->orderBy('id', 'DESC')
                ->get()->getResultArray();
        }

        $data = [
            'title' => 'Slip Gaji Saya',
            'active' => 'pribadi-slip-gaji',
            'slipGajiList' => $slipGajiList
        ];

        return $this->renderView('teknisi/pribadi/slip_gaji', $data);
    }

    public function profil()
    {
        $data = [
            'title' => 'Profil Saya',
            'active' => 'profile',
            'user' => $this->userData,
            'karyawan' => $this->karyawanData
        ];

        return $this->renderView('teknisi/profile/index', $data);
    }
}
