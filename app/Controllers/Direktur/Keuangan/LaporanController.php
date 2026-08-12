<?php

namespace App\Controllers\Direktur\Keuangan;

use App\Controllers\BaseController;

class LaporanController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan') ?? 'all';

        $summary = $this->getMonthlyData($tahun, $bulan);
        $realTransactions = $this->getRealTransactions($tahun, $bulan);

        $data = array_merge([
            'title'            => 'Executive Summary Laporan Keuangan Real-Time',
            'tahun'            => $tahun,
            'bulan'            => $bulan,
            'realTransactions' => $realTransactions,
        ], $summary);

        return view('direktur/keuangan/laporan', $data);
    }

    public function cetak()
    {
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan') ?? 'all';

        $summary = $this->getMonthlyData($tahun, $bulan);
        $realTransactions = $this->getRealTransactions($tahun, $bulan);

        $data = array_merge([
            'tahun'            => $tahun,
            'bulan'            => $bulan,
            'tanggalCetak'     => date('d F Y'),
            'realTransactions' => $realTransactions,
        ], $summary);

        return view('direktur/keuangan/cetak_laporan', $data);
    }

    public function export_excel()
    {
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan') ?? 'all';

        $summary = $this->getMonthlyData($tahun, $bulan);

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=Laporan_Keuangan_Eksekutif_{$tahun}.xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo "<h2 style='text-align:center;'>LAPORAN KEUANGAN EKSEKUTIF - PT CDW ENGINEERING ({$tahun})</h2>";
        echo "<p style='text-align:center;'>Tanggal Cetak: " . date('d F Y') . "</p>";

        echo "<h3>1. Ringkasan Eksekutif Finansial</h3>";
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr style='background:#f1f5f9;'><th>Komponen Keuangan</th><th>Nominal (Rp)</th></tr>";
        echo "<tr><td>Total Pendapatan Client (Inflow)</td><td align='right'>" . number_format($summary['totalPendapatan'], 0, ',', '.') . "</td></tr>";
        echo "<tr><td>Beban Pengadaan Barang (PR)</td><td align='right'>" . number_format($summary['totalPembelian'], 0, ',', '.') . "</td></tr>";
        echo "<tr><td>Beban Penggajian Karyawan (Payroll)</td><td align='right'>" . number_format($summary['totalGaji'], 0, ',', '.') . "</td></tr>";
        echo "<tr><td>Pencairan Kasbon Karyawan</td><td align='right'>" . number_format($summary['totalKasbon'], 0, ',', '.') . "</td></tr>";
        echo "<tr style='background:#fee2e2;'><td><strong>TOTAL PENGELUARAN OPERASIONAL</strong></td><td align='right'><strong>" . number_format($summary['totalPengeluaran'], 0, ',', '.') . "</strong></td></tr>";
        echo "<tr style='background:#dcfce7;'><td><strong>ESTIMASI LABA / RUGI BERSIH</strong></td><td align='right'><strong>" . number_format($summary['labaBersih'], 0, ',', '.') . "</strong></td></tr>";
        echo "</table>";

        echo "<br><h3>2. Rincian Arus Kas Bulanan (Cashflow Jan - Des {$tahun})</h3>";
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr style='background:#e2e8f0;'>
                <th>Bulan</th>
                <th>Pendapatan Client (Rp)</th>
                <th>Pembelian Barang (Rp)</th>
                <th>Gaji Karyawan (Rp)</th>
                <th>Kasbon (Rp)</th>
                <th>Total Pengeluaran (Rp)</th>
                <th>Surplus / Defisit (Rp)</th>
              </tr>";

        foreach ($summary['monthlyData'] as $m) {
            echo "<tr>";
            echo "<td>" . $m['bulan_name'] . "</td>";
            echo "<td align='right'>" . number_format($m['pendapatan'], 0, ',', '.') . "</td>";
            echo "<td align='right'>" . number_format($m['pembelian'], 0, ',', '.') . "</td>";
            echo "<td align='right'>" . number_format($m['gaji'], 0, ',', '.') . "</td>";
            echo "<td align='right'>" . number_format($m['kasbon'], 0, ',', '.') . "</td>";
            echo "<td align='right'>" . number_format($m['total_pengeluaran'], 0, ',', '.') . "</td>";
            echo "<td align='right' style='font-weight:bold; color:" . ($m['surplus'] >= 0 ? 'green' : 'red') . ";'>" . number_format($m['surplus'], 0, ',', '.') . "</td>";
            echo "</tr>";
        }

        echo "<tr style='background:#cbd5e1; font-weight:bold;'>";
        echo "<td>TOTAL TAHUNAN</td>";
        echo "<td align='right'>" . number_format($summary['totalPendapatan'], 0, ',', '.') . "</td>";
        echo "<td align='right'>" . number_format($summary['totalPembelian'], 0, ',', '.') . "</td>";
        echo "<td align='right'>" . number_format($summary['totalGaji'], 0, ',', '.') . "</td>";
        echo "<td align='right'>" . number_format($summary['totalKasbon'], 0, ',', '.') . "</td>";
        echo "<td align='right'>" . number_format($summary['totalPengeluaran'], 0, ',', '.') . "</td>";
        echo "<td align='right'>" . number_format($summary['labaBersih'], 0, ',', '.') . "</td>";
        echo "</tr>";
        echo "</table>";

        exit;
    }

    private function getMonthlyData($tahun, $bulan = 'all')
    {
        $months = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April',   '05' => 'Mei',      '06' => 'Juni',
            '07' => 'Juli',    '08' => 'Agustus',  '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        $monthlyData = [];

        $grandPendapatan = 0;
        $grandPembelian  = 0;
        $grandGaji       = 0;
        $grandKasbon     = 0;

        foreach ($months as $num => $name) {
            if ($bulan && $bulan !== 'all' && $bulan !== $num) {
                continue;
            }

            // 1. REAL Pendapatan Client (Inflow)
            $pendapatan = 0;
            if ($this->db->tableExists('pembayaran')) {
                try {
                    $q = $this->db->table('pembayaran')
                        ->selectSum('jumlah_bayar', 'total')
                        ->where('YEAR(tanggal_bayar)', $tahun)
                        ->where('MONTH(tanggal_bayar)', (int)$num)
                        ->get()->getRowArray();
                    $pendapatan += floatval($q['total'] ?? 0);
                } catch (\Throwable $t) {}
            }
            
            if ($this->db->tableExists('project')) {
                try {
                    $fieldVal = $this->db->fieldExists('nilai_proyek', 'project') ? 'nilai_proyek' : ($this->db->fieldExists('anggaran', 'project') ? 'anggaran' : null);
                    if ($fieldVal) {
                        $q = $this->db->table('project')
                            ->selectSum($fieldVal, 'total')
                            ->where('YEAR(created_at)', $tahun)
                            ->where('MONTH(created_at)', (int)$num)
                            ->get()->getRowArray();
                        $pendapatan += floatval($q['total'] ?? 0);
                    }
                } catch (\Throwable $t) {}
            }

            // 2. REAL Pembelian Barang (PR) - Realtime Database Match
            $pembelian = 0;
            if ($this->db->tableExists('form_pembelian')) {
                try {
                    $builder = $this->db->table('form_pembelian')
                        ->selectSum('total_estimasi', 'total')
                        ->whereIn('status_direktur', ['Disetujui', 'disetujui', 'Approved', 'approved']);
                    
                    if ($this->db->fieldExists('deleted_at', 'form_pembelian')) {
                        $builder->where('deleted_at', null);
                    }

                    $builder->where("YEAR(COALESCE(NULLIF(tanggal_pengajuan, ''), created_at))", $tahun);
                    $builder->where("MONTH(COALESCE(NULLIF(tanggal_pengajuan, ''), created_at))", (int)$num);

                    $q = $builder->get()->getRowArray();
                    $pembelian = floatval($q['total'] ?? 0);
                } catch (\Throwable $t) {}
            }

            // 3. REAL Pengeluaran Gaji (HANYA Gaji Yang Telah Dibayar)
            $gaji = 0;
            if ($this->db->tableExists('penggajian_detail_pembayaran')) {
                try {
                    $q = $this->db->table('penggajian_detail_pembayaran')
                        ->selectSum('gaji_bersih', 'total')
                        ->where('tahun', $tahun)
                        ->whereIn('bulan', [$num, (string)(int)$num])
                        ->whereIn('status_pembayaran', ['Dibayar', 'dibayar', 'Paid', 'paid', 'Sukses', 'sukses'])
                        ->get()->getRowArray();
                    $gaji = floatval($q['total'] ?? 0);
                } catch (\Throwable $t) {}
            }

            // 4. REAL Pengeluaran Kasbon (Disetujui Realtime Database Match)
            $kasbon = 0;
            if ($this->db->tableExists('form_kasbon')) {
                try {
                    $builder = $this->db->table('form_kasbon')
                        ->selectSum('jumlah_kasbon', 'total')
                        ->whereIn('status_direktur', ['Disetujui', 'disetujui', 'Approved', 'approved']);

                    if ($this->db->fieldExists('deleted_at', 'form_kasbon')) {
                        $builder->where('deleted_at', null);
                    }

                    $builder->where("YEAR(COALESCE(NULLIF(tanggal_pengajuan, ''), created_at))", $tahun);
                    $builder->where("MONTH(COALESCE(NULLIF(tanggal_pengajuan, ''), created_at))", (int)$num);

                    $q = $builder->get()->getRowArray();
                    $kasbon = floatval($q['total'] ?? 0);
                } catch (\Throwable $t) {}
            }

            $totalPengeluaranBulan = $pembelian + $gaji + $kasbon;
            $surplusBulan = $pendapatan - $totalPengeluaranBulan;

            $grandPendapatan += $pendapatan;
            $grandPembelian  += $pembelian;
            $grandGaji       += $gaji;
            $grandKasbon     += $kasbon;

            $monthlyData[] = [
                'bulan_num'  => $num,
                'bulan_name' => $name,
                'pendapatan' => $pendapatan,
                'pembelian'  => $pembelian,
                'gaji'       => $gaji,
                'kasbon'     => $kasbon,
                'total_pengeluaran' => $totalPengeluaranBulan,
                'surplus'    => $surplusBulan
            ];
        }

        $grandPengeluaran = $grandPembelian + $grandGaji + $grandKasbon;
        $grandLabaBersih  = $grandPendapatan - $grandPengeluaran;

        return [
            'monthlyData'      => $monthlyData,
            'totalPendapatan'  => $grandPendapatan,
            'totalPembelian'   => $grandPembelian,
            'totalGaji'        => $grandGaji,
            'totalKasbon'      => $grandKasbon,
            'totalPengeluaran' => $grandPengeluaran,
            'labaBersih'       => $grandLabaBersih
        ];
    }

    private function getRealTransactions($tahun, $bulan = 'all')
    {
        $transactions = [];

        // 1. Fetch Approved Purchase Requests (PR Pembelian)
        if ($this->db->tableExists('form_pembelian')) {
            try {
                $builder = $this->db->table('form_pembelian p')
                    ->select('p.nomor_pr, p.keperluan, p.total_estimasi as nominal, p.tanggal_pengajuan as tanggal, p.created_at, p.status_direktur, k.nama_lengkap as nama_pemohon')
                    ->join('karyawan k', 'k.id = p.karyawan_id', 'left')
                    ->whereIn('p.status_direktur', ['Disetujui', 'disetujui', 'Approved', 'approved']);

                if ($this->db->fieldExists('deleted_at', 'form_pembelian')) {
                    $builder->where('p.deleted_at', null);
                }

                $prList = $builder->orderBy('p.id', 'DESC')->limit(10)->get()->getResultArray();
                foreach ($prList as $pr) {
                    $tgl = !empty($pr['tanggal']) ? $pr['tanggal'] : (!empty($pr['created_at']) ? date('Y-m-d', strtotime($pr['created_at'])) : date('Y-m-d'));
                    $transactions[] = [
                        'jenis'      => 'Pengadaan / Pembelian (PR)',
                        'nomor'      => $pr['nomor_pr'] ?? 'PR-REF',
                        'pemohon'    => $pr['nama_pemohon'] ?? 'Staf CDW',
                        'keterangan' => $pr['keperluan'] ?? 'Pembelian Barang',
                        'tipe'       => 'Pengeluaran',
                        'nominal'    => floatval($pr['nominal'] ?? 0),
                        'tanggal'    => $tgl,
                        'badge'      => 'bg-warning text-dark'
                    ];
                }
            } catch (\Throwable $t) {}
        }

        // 2. Fetch Approved Kasbon Requests
        if ($this->db->tableExists('form_kasbon')) {
            try {
                $builder = $this->db->table('form_kasbon ksb')
                    ->select('ksb.nomor_kasbon, ksb.keperluan, ksb.alasan, ksb.jumlah_kasbon as nominal, ksb.tanggal_pengajuan, ksb.created_at, k.nama_lengkap as nama_pemohon')
                    ->join('karyawan k', 'k.id = ksb.karyawan_id', 'left')
                    ->whereIn('ksb.status_direktur', ['Disetujui', 'disetujui', 'Approved', 'approved']);

                if ($this->db->fieldExists('deleted_at', 'form_kasbon')) {
                    $builder->where('ksb.deleted_at', null);
                }

                $ksbList = $builder->orderBy('ksb.id', 'DESC')->limit(10)->get()->getResultArray();
                foreach ($ksbList as $ksb) {
                    $tgl = !empty($ksb['tanggal_pengajuan']) ? $ksb['tanggal_pengajuan'] : (!empty($ksb['created_at']) ? date('Y-m-d', strtotime($ksb['created_at'])) : date('Y-m-d'));
                    $transactions[] = [
                        'jenis'      => 'Pencairan Kasbon Karyawan',
                        'nomor'      => $ksb['nomor_kasbon'] ?? 'KSB-REF',
                        'pemohon'    => $ksb['nama_pemohon'] ?? 'Karyawan CDW',
                        'keterangan' => !empty($ksb['keperluan']) ? $ksb['keperluan'] : (!empty($ksb['alasan']) ? $ksb['alasan'] : 'Pinjaman Kasbon'),
                        'tipe'       => 'Pengeluaran',
                        'nominal'    => floatval($ksb['nominal'] ?? 0),
                        'tanggal'    => $tgl,
                        'badge'      => 'bg-secondary text-white'
                    ];
                }
            } catch (\Throwable $t) {}
        }

        // 3. Fetch Real Processed & Paid Payroll Records Only (status_pembayaran = 'Dibayar')
        if ($this->db->tableExists('penggajian_detail_pembayaran')) {
            try {
                $builder = $this->db->table('penggajian_detail_pembayaran p')
                    ->select('p.bulan, p.tahun, p.gaji_bersih as nominal, p.status_pembayaran, p.updated_at, p.created_at, k.nama_lengkap, k.jabatan')
                    ->join('karyawan k', 'k.id = p.karyawan_id', 'left')
                    ->whereIn('p.status_pembayaran', ['Dibayar', 'dibayar', 'Paid', 'paid', 'Sukses', 'sukses']);

                $payList = $builder->orderBy('p.id', 'DESC')->limit(10)->get()->getResultArray();
                foreach ($payList as $pay) {
                    $tgl = !empty($pay['updated_at']) ? date('Y-m-d', strtotime($pay['updated_at'])) : (!empty($pay['created_at']) ? date('Y-m-d', strtotime($pay['created_at'])) : date('Y-m-d'));
                    $transactions[] = [
                        'jenis'      => 'Penggajian Karyawan',
                        'nomor'      => 'PAY-' . sprintf('%02d', $pay['bulan']) . $pay['tahun'],
                        'pemohon'    => $pay['nama_lengkap'] ?? 'Karyawan CDW',
                        'keterangan' => 'Gaji Periode ' . $pay['bulan'] . '/' . $pay['tahun'] . ' (Dibayar)',
                        'tipe'       => 'Pengeluaran',
                        'nominal'    => floatval($pay['nominal'] ?? 0),
                        'tanggal'    => $tgl,
                        'badge'      => 'bg-info text-dark'
                    ];
                }
            } catch (\Throwable $t) {}
        }

        // 4. Fetch Projects Revenue
        if ($this->db->tableExists('project')) {
            try {
                $fieldVal = $this->db->fieldExists('nilai_proyek', 'project') ? 'nilai_proyek' : ($this->db->fieldExists('anggaran', 'project') ? 'anggaran' : null);
                if ($fieldVal) {
                    $builder = $this->db->table('project')
                        ->select("nama_project, {$fieldVal} as nominal, created_at");

                    $projList = $builder->orderBy('id', 'DESC')->limit(10)->get()->getResultArray();
                    foreach ($projList as $p) {
                        $transactions[] = [
                            'jenis'      => 'Pendapatan Proyek Client',
                            'nomor'      => 'PRJ-CDW',
                            'pemohon'    => 'Klien Perusahaan',
                            'keterangan' => $p['nama_project'] ?? 'Nilai Kontrak Proyek',
                            'tipe'       => 'Pemasukan',
                            'nominal'    => floatval($p['nominal'] ?? 0),
                            'tanggal'    => !empty($p['created_at']) ? date('Y-m-d', strtotime($p['created_at'])) : date('Y-m-d'),
                            'badge'      => 'bg-success text-white'
                        ];
                    }
                }
            } catch (\Throwable $t) {}
        }

        // Sort all transactions by date descending
        usort($transactions, function($a, $b) {
            return strtotime($b['tanggal']) - strtotime($a['tanggal']);
        });

        return array_slice($transactions, 0, 15);
    }
}
