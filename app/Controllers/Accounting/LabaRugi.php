<?php

namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\Accounting\LabaRugiModel;

class LabaRugi extends BaseController
{
    protected $labaRugiModel;
    
    public function __construct()
    {
        $this->labaRugiModel = new LabaRugiModel();
        helper(['form', 'number']);
    }
    
    /**
     * Display Laporan Laba Rugi
     */
    public function index()
    {
        // Get current user data
        $userModel = model('UserModel');
        $karyawanModel = model('KaryawanModel');
        
        $userId = session()->get('user_id');
        $user = $userModel->find($userId);
        $karyawan = $user && $user['karyawan_id'] ? $karyawanModel->find($user['karyawan_id']) : [];
        
        // ============================================
        // GET FILTERS
        // ============================================
        $startDate = $this->request->getGet('tanggal_mulai') ?? date('Y-m-01');
        $endDate = $this->request->getGet('tanggal_selesai') ?? date('Y-m-t');
        
        // Validasi tanggal
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
            $startDate = date('Y-m-01');
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
            $endDate = date('Y-m-t');
        }
        
        // Clear cache jika parameter refresh ada
        if ($this->request->getGet('refresh') == '1') {
            $this->labaRugiModel->clearCache($startDate, $endDate);
        }
        
        // ============================================
        // GET LAPORAN LABA RUGI DARI MODEL
        // ============================================
        $laporan = $this->labaRugiModel->getLaporanLabaRugi($startDate, $endDate);
        $ringkasan = $this->labaRugiModel->getRingkasan($startDate, $endDate);
        
        // ============================================
        // PREPARE STATS CARDS
        // ============================================
        $stats = [
            'total_pendapatan' => [
                'value' => $this->formatRupiah($laporan['total_pendapatan']),
                'label' => 'Total Pendapatan',
                'icon' => 'fa-money-bill-wave',
                'color' => 'success',
                'raw' => $laporan['total_pendapatan']
            ],
            'total_beban' => [
                'value' => $this->formatRupiah($laporan['total_hpp'] + $laporan['total_beban_operasional'] + $laporan['total_beban_lain']),
                'label' => 'Total Beban',
                'icon' => 'fa-file-invoice-dollar',
                'color' => 'danger',
                'raw' => $laporan['total_hpp'] + $laporan['total_beban_operasional'] + $laporan['total_beban_lain']
            ],
            'laba_kotor' => [
                'value' => $this->formatRupiah($laporan['laba_kotor']),
                'label' => 'Laba Kotor',
                'icon' => 'fa-chart-line',
                'color' => $laporan['laba_kotor'] >= 0 ? 'primary' : 'danger',
                'raw' => $laporan['laba_kotor']
            ],
            'laba_bersih' => [
                'value' => $this->formatRupiah($laporan['laba_bersih']),
                'label' => $laporan['is_profit'] ? 'LABA BERSIH' : ($laporan['is_loss'] ? 'RUGI BERSIH' : 'BREAK EVEN'),
                'icon' => $laporan['is_profit'] ? 'fa-trophy' : ($laporan['is_loss'] ? 'fa-exclamation-triangle' : 'fa-balance-scale'),
                'color' => $laporan['is_profit'] ? 'success' : ($laporan['is_loss'] ? 'danger' : 'info'),
                'raw' => $laporan['laba_bersih']
            ]
        ];
        
        // ============================================
        // PREPARE DATA UNTUK VIEW
        // ============================================
        $data = [
            'title' => 'Laporan Laba Rugi',
            'active' => 'laporan-keuangan',
            'submenu' => 'laba-rugi',
            'user' => $user ?? ['name' => 'Accounting Staff', 'role' => 'accounting'],
            'karyawan' => $karyawan ?? [],
            'subtitle' => 'Income Statement - Laporan Laba Rugi',
            
            // Data utama dari model
            'laporan' => $laporan,
            'ringkasan' => $ringkasan,
            
            // Kelompokan data untuk view
            'pendapatan' => $laporan['pendapatan'] ?? [],
            'total_pendapatan' => $laporan['total_pendapatan'] ?? 0,
            
            'hpp' => $laporan['hpp'] ?? [],
            'total_hpp' => $laporan['total_hpp'] ?? 0,
            
            'beban_operasional' => $laporan['beban_operasional'] ?? [],
            'total_beban_operasional' => $laporan['total_beban_operasional'] ?? 0,
            
            'beban_lain' => $laporan['beban_lain'] ?? [],
            'total_beban_lain' => $laporan['total_beban_lain'] ?? 0,
            
            // Laba
            'laba_kotor' => $laporan['laba_kotor'] ?? 0,
            'laba_operasional' => $laporan['laba_operasional'] ?? 0,
            'laba_sebelum_pajak' => $laporan['laba_sebelum_pajak'] ?? 0,
            'laba_bersih' => $laporan['laba_bersih'] ?? 0,
            
            // Status
            'is_profit' => $laporan['is_profit'] ?? false,
            'is_loss' => $laporan['is_loss'] ?? false,
            'is_break_even' => $laporan['is_break_even'] ?? false,
            
            // Filters
            'filters' => [
                'tanggal_mulai' => $startDate,
                'tanggal_selesai' => $endDate,
                'periode_label' => date('d F Y', strtotime($startDate)) . ' - ' . date('d F Y', strtotime($endDate))
            ],
            
            // Stats cards
            'stats' => $stats,
            
            // Margin
            'margin_laba' => $ringkasan['margin'] ?? 0,
            
            // Periode untuk dropdown
            'recentPeriods' => $this->getRecentPeriods(),
            
            // Tanggal generate
            'date_generated' => date('d/m/Y H:i:s'),
            
            // Environment
            'is_development' => ENVIRONMENT !== 'production'
        ];
        
        return view('accounting/laporan-keuangan/laporan-laba-rugi/index', $data);
    }
    
    /**
     * Get recent periods untuk dropdown filter
     */
    private function getRecentPeriods($months = 6)
    {
        $periods = [];
        $currentDate = date('Y-m-01');
        
        for ($i = 0; $i < $months; $i++) {
            $date = date('Y-m-01', strtotime("-$i months", strtotime($currentDate)));
            $endDate = date('Y-m-t', strtotime($date));
            $periods[] = [
                'start' => $date,
                'end' => $endDate,
                'label' => date('F Y', strtotime($date))
            ];
        }
        
        return $periods;
    }
    
    /**
     * Format Rupiah dengan tanda kurung untuk nilai negatif
     */
    private function formatRupiah($nilai)
    {
        $nilai = (float) $nilai;
        
        if ($nilai < 0) {
            return '(Rp ' . number_format(abs($nilai), 0, ',', '.') . ')';
        }
        
        return 'Rp ' . number_format($nilai, 0, ',', '.');
    }
    
    // ================================================================
    // AJAX METHODS
    // ================================================================
    
    /**
     * AJAX: Get summary untuk dashboard
     */
    public function ajaxGetSummary()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['error' => 'Method not allowed']);
        }
        
        try {
            $startDate = $this->request->getGet('tanggal_mulai') ?? date('Y-m-01');
            $endDate = $this->request->getGet('tanggal_selesai') ?? date('Y-m-t');
            
            $ringkasan = $this->labaRugiModel->getRingkasan($startDate, $endDate);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'total_pendapatan' => $ringkasan['total_pendapatan'],
                    'total_pendapatan_formatted' => $this->formatRupiah($ringkasan['total_pendapatan']),
                    'total_beban' => $ringkasan['total_beban'],
                    'total_beban_formatted' => $this->formatRupiah($ringkasan['total_beban']),
                    'laba_kotor' => $ringkasan['laba_kotor'],
                    'laba_kotor_formatted' => $this->formatRupiah($ringkasan['laba_kotor']),
                    'laba_bersih' => $ringkasan['laba_bersih'],
                    'laba_bersih_formatted' => $this->formatRupiah($ringkasan['laba_bersih']),
                    'is_profit' => $ringkasan['is_profit'],
                    'margin' => number_format($ringkasan['margin'], 2) . '%',
                    'periode' => date('d M Y', strtotime($startDate)) . ' - ' . date('d M Y', strtotime($endDate))
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'AJAX Get Summary Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil summary: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * AJAX: Get detail laporan
     */
    public function ajaxGetDetail()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['error' => 'Method not allowed']);
        }
        
        try {
            $startDate = $this->request->getGet('tanggal_mulai') ?? date('Y-m-01');
            $endDate = $this->request->getGet('tanggal_selesai') ?? date('Y-m-t');
            
            $laporan = $this->labaRugiModel->getLaporanLabaRugi($startDate, $endDate);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $laporan,
                'message' => 'Data berhasil diambil'
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'AJAX Get Detail Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ]);
        }
    }
    
    // ================================================================
    // EXPORT & PRINT
    // ================================================================
    
    /**
     * Export Laporan Laba Rugi ke CSV
     */
    public function export()
    {
        try {
            $startDate = $this->request->getGet('tanggal_mulai') ?? date('Y-m-01');
            $endDate = $this->request->getGet('tanggal_selesai') ?? date('Y-m-t');
            $type = $this->request->getGet('type') ?? 'csv';
            
            $laporan = $this->labaRugiModel->getLaporanLabaRugi($startDate, $endDate);
            
            if ($type == 'csv') {
                return $this->exportToCSV($laporan);
            } else {
                return $this->exportToExcel($laporan);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Export Laba Rugi Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengekspor: ' . $e->getMessage());
        }
    }
    
    /**
     * Export to CSV
     */
    private function exportToCSV($laporan)
    {
        $filename = 'laporan_laba_rugi_' . date('Ymd_His') . '.csv';
        
        $csv = [];
        $csv[] = "LAPORAN LABA RUGI - PT. CIPTA DUTA WACANA";
        $csv[] = "Periode: " . $laporan['periode']['start_label'] . " - " . $laporan['periode']['end_label'];
        $csv[] = "Tanggal Generate: " . date('d/m/Y H:i:s');
        $csv[] = "Status: " . ($laporan['is_profit'] ? 'LABA' : ($laporan['is_loss'] ? 'RUGI' : 'BREAK EVEN'));
        $csv[] = "";
        
        // PENDAPATAN
        $csv[] = "PENDAPATAN";
        $csv[] = "Kode Akun,Nama Akun,Saldo (Rp)";
        foreach ($laporan['pendapatan'] as $item) {
            $csv[] = $item['kode_akun'] . ',' . $item['nama_akun'] . ',' . $item['saldo'];
        }
        $csv[] = "TOTAL PENDAPATAN,," . $laporan['total_pendapatan'];
        $csv[] = "";
        
        // HPP
        if (!empty($laporan['hpp'])) {
            $csv[] = "HARGA POKOK PENJUALAN (HPP)";
            $csv[] = "Kode Akun,Nama Akun,Saldo (Rp)";
            foreach ($laporan['hpp'] as $item) {
                $csv[] = $item['kode_akun'] . ',' . $item['nama_akun'] . ',' . $item['saldo'];
            }
            $csv[] = "TOTAL HPP,," . $laporan['total_hpp'];
            $csv[] = "";
            $csv[] = "LABA KOTOR,," . $laporan['laba_kotor'];
            $csv[] = "";
        }
        
        // BEBAN OPERASIONAL
        if (!empty($laporan['beban_operasional'])) {
            $csv[] = "BEBAN OPERASIONAL";
            $csv[] = "Kode Akun,Nama Akun,Saldo (Rp)";
            foreach ($laporan['beban_operasional'] as $item) {
                $csv[] = $item['kode_akun'] . ',' . $item['nama_akun'] . ',' . $item['saldo'];
            }
            $csv[] = "TOTAL BEBAN OPERASIONAL,," . $laporan['total_beban_operasional'];
            $csv[] = "";
            $csv[] = "LABA OPERASIONAL,," . $laporan['laba_operasional'];
            $csv[] = "";
        }
        
        // BEBAN LAIN
        if (!empty($laporan['beban_lain'])) {
            $csv[] = "BEBAN LAIN-LAIN";
            $csv[] = "Kode Akun,Nama Akun,Saldo (Rp)";
            foreach ($laporan['beban_lain'] as $item) {
                $csv[] = $item['kode_akun'] . ',' . $item['nama_akun'] . ',' . $item['saldo'];
            }
            $csv[] = "TOTAL BEBAN LAIN,," . $laporan['total_beban_lain'];
            $csv[] = "";
        }
        
        // LABA BERSIH
        $csv[] = "LABA/RUGI BERSIH,," . $laporan['laba_bersih'];
        
        return $this->response
            ->setContentType('text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody(implode("\n", $csv));
    }
    
    /**
     * Export to Excel (CSV format)
     */
    private function exportToExcel($laporan)
    {
        return $this->exportToCSV($laporan);
    }
    
    /**
     * Print Laporan Laba Rugi
     */
    public function print()
    {
        try {
            $startDate = $this->request->getGet('tanggal_mulai') ?? date('Y-m-01');
            $endDate = $this->request->getGet('tanggal_selesai') ?? date('Y-m-t');
            
            $laporan = $this->labaRugiModel->getLaporanLabaRugi($startDate, $endDate);
            
            // Get user info
            $userModel = model('UserModel');
            $user = $userModel->find(session()->get('user_id'));
            
            $data = [
                'title' => 'Print Laporan Laba Rugi',
                'laporan' => $laporan,
                'user' => $user ?? ['name' => 'System'],
                'date_generated' => date('d/m/Y H:i:s'),
                
                // Data untuk view print
                'pendapatan' => $laporan['pendapatan'] ?? [],
                'total_pendapatan' => $laporan['total_pendapatan'] ?? 0,
                'hpp' => $laporan['hpp'] ?? [],
                'total_hpp' => $laporan['total_hpp'] ?? 0,
                'beban_operasional' => $laporan['beban_operasional'] ?? [],
                'total_beban_operasional' => $laporan['total_beban_operasional'] ?? 0,
                'beban_lain' => $laporan['beban_lain'] ?? [],
                'total_beban_lain' => $laporan['total_beban_lain'] ?? 0,
                'laba_kotor' => $laporan['laba_kotor'] ?? 0,
                'laba_operasional' => $laporan['laba_operasional'] ?? 0,
                'laba_bersih' => $laporan['laba_bersih'] ?? 0,
                'is_profit' => $laporan['is_profit'] ?? false,
                'is_loss' => $laporan['is_loss'] ?? false,
                'periode' => $laporan['periode'] ?? []
            ];
            
            return view('accounting/laporan-keuangan/laporan-laba-rugi/print', $data);
            
        } catch (\Exception $e) {
            log_message('error', 'Print Laba Rugi Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mencetak: ' . $e->getMessage());
        }
    }
    /**
 * Export Laporan Laba Rugi ke PDF
 */
public function exportPdf()
{
    try {
        $startDate = $this->request->getGet('tanggal_mulai') ?? date('Y-m-01');
        $endDate = $this->request->getGet('tanggal_selesai') ?? date('Y-m-t');
        
        $laporan = $this->labaRugiModel->getLaporanLabaRugi($startDate, $endDate);
        
        // Get user info
        $userModel = model('UserModel');
        $user = $userModel->find(session()->get('user_id'));
        
        $data = [
            'title' => 'Laporan Laba Rugi',
            'laporan' => $laporan,
            'periode_start' => $startDate,
            'periode_end' => $endDate,
            'user' => $user ?? ['name' => 'System'],
            'date_generated' => date('d/m/Y H:i:s'),
            
            // Data untuk view pdf
            'pendapatan' => $laporan['pendapatan'] ?? [],
            'total_pendapatan' => $laporan['total_pendapatan'] ?? 0,
            'hpp' => $laporan['hpp'] ?? [],
            'total_hpp' => $laporan['total_hpp'] ?? 0,
            'beban_operasional' => $laporan['beban_operasional'] ?? [],
            'total_beban_operasional' => $laporan['total_beban_operasional'] ?? 0,
            'beban_lain' => $laporan['beban_lain'] ?? [],
            'total_beban_lain' => $laporan['total_beban_lain'] ?? 0,
            'laba_kotor' => $laporan['laba_kotor'] ?? 0,
            'laba_operasional' => $laporan['laba_operasional'] ?? 0,
            'laba_sebelum_pajak' => $laporan['laba_sebelum_pajak'] ?? 0,
            'laba_bersih' => $laporan['laba_bersih'] ?? 0,
            'is_profit' => $laporan['is_profit'] ?? false,
            'is_loss' => $laporan['is_loss'] ?? false,
            'periode' => $laporan['periode'] ?? []
        ];
        
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->setPaper('A4', 'portrait');
        $html = view('accounting/laporan-keuangan/laporan-laba-rugi/pdf_template', $data);
        $dompdf->loadHtml($html);
        $dompdf->render();
        
        $dompdf->stream('Laporan_Laba_Rugi_' . date('Ymd_His') . '.pdf', ['Attachment' => 1]);
        
    } catch (\Exception $e) {
        log_message('error', 'Export Laba Rugi PDF Error: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
    }
}
}