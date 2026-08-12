<?php

namespace App\Controllers\Sales;

class Dashboard extends BaseSalesController
{
    public function index()
    {
        $userId = session()->get('user_id');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan') ?? date('m');

        // Stats Leads
        $totalLeads = $this->db->table('sales_leads')->where('deleted_at IS NULL')->countAllResults();
        $leadsAktif = $this->db->table('sales_leads')->where('deleted_at IS NULL')->whereIn('status', ['Baru', 'Follow Up', 'Negosiasi'])->countAllResults();
        
        // Closing Bulan Ini
        $dealClosing = $this->db->table('sales_deal')
            ->where('deleted_at IS NULL')
            ->where('MONTH(tanggal_closing)', $bulan)
            ->where('YEAR(tanggal_closing)', $tahun)
            ->get()->getResultArray();
            
        $nilaiClosingBulanIni = array_sum(array_column($dealClosing, 'nilai_deal'));
        $jumlahDealBulanIni = count($dealClosing);

        // Target Bulan Ini
        $targetRow = $this->db->table('sales_target')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get()->getRowArray();

        $targetBulanIni = $targetRow['target_penjualan'] ?? 0;
        $persenRealisasi = $targetBulanIni > 0 ? round(($nilaiClosingBulanIni / $targetBulanIni) * 100, 1) : 0;

        // Quotation Pending
        $quotationPending = $this->db->table('sales_quotation')
            ->where('deleted_at IS NULL')
            ->whereIn('status', ['Draft', 'Sent'])
            ->countAllResults();

        // Leads Terbaru
        $recentLeads = $this->db->table('sales_leads')
            ->where('deleted_at IS NULL')
            ->orderBy('id', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        // Pipeline Counts per Status
        $statuses = ['Baru', 'Follow Up', 'Negosiasi', 'Closing', 'Hilang'];
        $pipelineStats = [];
        foreach ($statuses as $st) {
            $pipelineStats[$st] = $this->db->table('sales_leads')
                ->where('deleted_at IS NULL')
                ->where('status', $st)
                ->countAllResults();
        }

        $data = [
            'title' => 'Dashboard Sales & Marketing',
            'active' => 'dashboard',
            'totalLeads' => $totalLeads,
            'leadsAktif' => $leadsAktif,
            'nilaiClosingBulanIni' => $nilaiClosingBulanIni,
            'jumlahDealBulanIni' => $jumlahDealBulanIni,
            'targetBulanIni' => $targetBulanIni,
            'persenRealisasi' => $persenRealisasi,
            'quotationPending' => $quotationPending,
            'recentLeads' => $recentLeads,
            'pipelineStats' => $pipelineStats,
            'tahun' => $tahun,
            'bulan' => $bulan
        ];

        return view('sales/templates/header', $data)
             . view('sales/templates/sidebar', $data)
             . view('sales/dashboard/index', $data)
             . view('sales/templates/footer', $data);
    }
}