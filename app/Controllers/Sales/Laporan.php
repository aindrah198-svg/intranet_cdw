<?php

namespace App\Controllers\Sales;

class Laporan extends BaseSalesController
{
    public function index()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $deals = $this->db->table('sales_deal')
            ->where('deleted_at IS NULL')
            ->where('MONTH(tanggal_closing)', $bulan)
            ->where('YEAR(tanggal_closing)', $tahun)
            ->orderBy('tanggal_closing', 'DESC')
            ->get()->getResultArray();

        $totalClosing = array_sum(array_column($deals, 'nilai_deal'));
        $jumlahDeal = count($deals);

        $data = [
            'title' => 'Laporan Penjualan Harian & Mingguan',
            'active' => 'laporan',
            'deals' => $deals,
            'totalClosing' => $totalClosing,
            'jumlahDeal' => $jumlahDeal,
            'bulan' => $bulan,
            'tahun' => $tahun
        ];

        return view('sales/templates/header', $data)
             . view('sales/templates/sidebar', $data)
             . view('sales/laporan/index', $data)
             . view('sales/templates/footer', $data);
    }

    public function target()
    {
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $targets = $this->db->table('sales_target')
            ->where('tahun', $tahun)
            ->orderBy('bulan', 'ASC')
            ->get()->getResultArray();

        // Calculate realisasi from deals
        $targetList = [];
        for ($m = 1; $m <= 12; $m++) {
            $rowTarget = array_filter($targets, function($t) use ($m) {
                return $t['bulan'] == $m;
            });
            $rowTarget = reset($rowTarget) ?: ['target_penjualan' => 0, 'target_leads' => 0];

            $dealBulan = $this->db->table('sales_deal')
                ->where('deleted_at IS NULL')
                ->where('MONTH(tanggal_closing)', $m)
                ->where('YEAR(tanggal_closing)', $tahun)
                ->get()->getResultArray();

            $realisasiPenjualan = array_sum(array_column($dealBulan, 'nilai_deal'));
            $realisasiLeads = $this->db->table('sales_leads')
                ->where('deleted_at IS NULL')
                ->where('MONTH(created_at)', $m)
                ->where('YEAR(created_at)', $tahun)
                ->countAllResults();

            $targetList[$m] = [
                'bulan' => $m,
                'target_penjualan' => $rowTarget['target_penjualan'],
                'realisasi_penjualan' => $realisasiPenjualan,
                'target_leads' => $rowTarget['target_leads'],
                'realisasi_leads' => $realisasiLeads,
                'persen' => $rowTarget['target_penjualan'] > 0 ? round(($realisasiPenjualan / $rowTarget['target_penjualan']) * 100, 1) : 0
            ];
        }

        $data = [
            'title' => 'Target vs Realisasi Penjualan',
            'active' => 'target',
            'tahun' => $tahun,
            'targetList' => $targetList
        ];

        return view('sales/templates/header', $data)
             . view('sales/templates/sidebar', $data)
             . view('sales/laporan/target', $data)
             . view('sales/templates/footer', $data);
    }

    public function saveTarget()
    {
        $tahun = $this->request->getPost('tahun');
        $bulan = $this->request->getPost('bulan');
        $targetPenjualan = (float)$this->request->getPost('target_penjualan');
        $targetLeads = (int)$this->request->getPost('target_leads');

        $existing = $this->db->table('sales_target')
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->get()->getRowArray();

        if ($existing) {
            $this->db->table('sales_target')->where('id', $existing['id'])->update([
                'target_penjualan' => $targetPenjualan,
                'target_leads' => $targetLeads,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            $this->db->table('sales_target')->insert([
                'tahun' => $tahun,
                'bulan' => $bulan,
                'target_penjualan' => $targetPenjualan,
                'target_leads' => $targetLeads,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        return redirect()->to(site_url('sales/laporan/target?tahun=' . $tahun))->with('success', 'Target penjualan berhasil disimpan');
    }
}
