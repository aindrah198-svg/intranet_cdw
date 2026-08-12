<?php

namespace App\Controllers\Sales;

class Pribadi extends BaseSalesController
{
    public function absensi()
    {
        $userId = session()->get('user_id');
        $user = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
        $karyawanId = session()->get('karyawan_id') ?? $user['karyawan_id'] ?? null;
        
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $absensiList = [];
        if ($karyawanId && $this->db->tableExists('absensi')) {
            $absensiList = $this->db->table('absensi')
                ->where('karyawan_id', $karyawanId)
                ->where('MONTH(tanggal)', $bulan)
                ->where('YEAR(tanggal)', $tahun)
                ->orderBy('tanggal', 'DESC')
                ->get()->getResultArray();
        }

        $data = [
            'title' => 'Absensi Saya',
            'active' => 'pribadi-absensi',
            'absensiList' => $absensiList,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'user' => $user
        ];

        return view('sales/templates/header', $data)
             . view('sales/templates/sidebar', $data)
             . view('sales/pribadi/absensi/index', $data)
             . view('sales/templates/footer', $data);
    }

    public function tugas()
    {
        $userId = session()->get('user_id');
        $user = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
        $karyawanId = session()->get('karyawan_id') ?? $user['karyawan_id'] ?? null;

        $tugasList = [];
        if ($this->db->tableExists('penugasan_harian')) {
            $builder = $this->db->table('penugasan_harian');
            
            if ($this->db->fieldExists('penerima_id', 'penugasan_harian')) {
                $builder->groupStart();
                if ($karyawanId) {
                    $builder->where('penerima_id', $karyawanId);
                }
                $builder->orWhere('penerima_id', $userId);
                $builder->groupEnd();
            } elseif ($this->db->fieldExists('karyawan_id', 'penugasan_harian') && $karyawanId) {
                $builder->where('karyawan_id', $karyawanId);
            }

            if ($this->db->fieldExists('tanggal_tugas', 'penugasan_harian')) {
                $builder->orderBy('tanggal_tugas', 'DESC');
            } elseif ($this->db->fieldExists('tanggal', 'penugasan_harian')) {
                $builder->orderBy('tanggal', 'DESC');
            }

            $tugasList = $builder->get()->getResultArray();
        }

        $data = [
            'title' => 'Tugas Saya',
            'active' => 'pribadi-tugas',
            'tugasList' => $tugasList
        ];

        return view('sales/templates/header', $data)
             . view('sales/templates/sidebar', $data)
             . view('sales/pribadi/tugas/index', $data)
             . view('sales/templates/footer', $data);
    }

    public function laporanHarian()
    {
        $userId = session()->get('user_id');
        $user = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
        $karyawanId = session()->get('karyawan_id') ?? $user['karyawan_id'] ?? null;

        $laporanList = [];
        if ($this->db->tableExists('laporan_harian')) {
            $builder = $this->db->table('laporan_harian');
            
            if ($this->db->fieldExists('karyawan_id', 'laporan_harian') && $karyawanId) {
                $builder->where('karyawan_id', $karyawanId);
            } elseif ($this->db->fieldExists('created_by', 'laporan_harian')) {
                $builder->where('created_by', $userId);
            }

            if ($this->db->fieldExists('tanggal', 'laporan_harian')) {
                $builder->orderBy('tanggal', 'DESC');
            } else {
                $builder->orderBy('id', 'DESC');
            }

            $laporanList = $builder->get()->getResultArray();
        }

        $data = [
            'title' => 'Laporan Kerja Harian',
            'active' => 'pribadi-laporan',
            'laporanList' => $laporanList
        ];

        return view('sales/templates/header', $data)
             . view('sales/templates/sidebar', $data)
             . view('sales/pribadi/laporan-harian/index', $data)
             . view('sales/templates/footer', $data);
    }

    public function pengajuan()
    {
        $userId = session()->get('user_id');
        $user = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
        $karyawanId = session()->get('karyawan_id') ?? $user['karyawan_id'] ?? null;

        $cutiList = [];
        if ($karyawanId && $this->db->tableExists('cuti')) {
            $cutiList = $this->db->table('cuti')
                ->where('karyawan_id', $karyawanId)
                ->orderBy('id', 'DESC')
                ->get()->getResultArray();
        }

        $kasbonList = [];
        if ($karyawanId && $this->db->tableExists('kasbon')) {
            $kasbonList = $this->db->table('kasbon')
                ->where('karyawan_id', $karyawanId)
                ->orderBy('id', 'DESC')
                ->get()->getResultArray();
        }

        $data = [
            'title' => 'Form Pengajuan (Cuti / Kasbon)',
            'active' => 'pribadi-pengajuan',
            'cutiList' => $cutiList,
            'kasbonList' => $kasbonList
        ];

        return view('sales/templates/header', $data)
             . view('sales/templates/sidebar', $data)
             . view('sales/pribadi/pengajuan/index', $data)
             . view('sales/templates/footer', $data);
    }

    public function slipGaji()
    {
        $userId = session()->get('user_id');
        $user = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
        $karyawanId = session()->get('karyawan_id') ?? $user['karyawan_id'] ?? null;

        $slipGajiList = [];
        if ($karyawanId && $this->db->tableExists('penggajian_perhitungan')) {
            $builder = $this->db->table('penggajian_perhitungan')
                ->where('karyawan_id', $karyawanId)
                ->where('status', 'Disetujui');

            if ($this->db->fieldExists('periode_tahun', 'penggajian_perhitungan')) {
                $builder->orderBy('periode_tahun', 'DESC')->orderBy('periode_bulan', 'DESC');
            } elseif ($this->db->fieldExists('tahun', 'penggajian_perhitungan')) {
                $builder->orderBy('tahun', 'DESC')->orderBy('bulan', 'DESC');
            } else {
                $builder->orderBy('id', 'DESC');
            }

            $slipGajiList = $builder->get()->getResultArray();
        }

        $data = [
            'title' => 'Slip Gaji Saya',
            'active' => 'pribadi-slip-gaji',
            'slipGajiList' => $slipGajiList
        ];

        return view('sales/templates/header', $data)
             . view('sales/templates/sidebar', $data)
             . view('sales/pribadi/slip-gaji/index', $data)
             . view('sales/templates/footer', $data);
    }

    public function profil()
    {
        $userId = session()->get('user_id');
        $user = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
        $karyawan = null;
        if (!empty($user['karyawan_id']) && $this->db->tableExists('karyawan')) {
            $karyawan = $this->db->table('karyawan')->where('id', $user['karyawan_id'])->get()->getRowArray();
        }

        $data = [
            'title' => 'Profil Saya',
            'active' => 'pribadi-profil',
            'user' => $user,
            'karyawan' => $karyawan
        ];

        return view('sales/templates/header', $data)
             . view('sales/templates/sidebar', $data)
             . view('sales/pribadi/profil/index', $data)
             . view('sales/templates/footer', $data);
    }
}
