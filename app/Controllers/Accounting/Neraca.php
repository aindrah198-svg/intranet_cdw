<?php

namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\Accounting\NeracaModel;
use App\Models\CoaModel;

class Neraca extends BaseController
{
    protected $neracaModel;
    protected $coaModel;
    
    public function __construct()
    {
        $this->neracaModel = new NeracaModel();
        $this->coaModel = new CoaModel();
        
        helper(['form', 'number']);
    }
    
    /**
     * Display Laporan Neraca (Balance Sheet)
     * Format: Staffel (Aset - Kewajiban - Ekuitas)
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
        $periodeDate = $this->request->getGet('tanggal_periode') ?? date('Y-m-d');
        
        // Validasi tanggal
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $periodeDate)) {
            $periodeDate = date('Y-m-d');
        }
        
        // Clear cache jika parameter refresh ada
        if ($this->request->getGet('refresh') == '1') {
            $this->neracaModel->clearCache($periodeDate);
        }
        
        // ============================================
        // GET LAPORAN NERACA DARI MODEL
        // ============================================
        $neraca = $this->neracaModel->getNeraca($periodeDate);
        
        // ============================================
        // PREPARE DATA UNTUK VIEW
        // ============================================
        $data = [
            'title' => 'Laporan Neraca',
            'active' => 'laporan-keuangan',
            'submenu' => 'neraca',
            'user' => $user ?? ['name' => 'Accounting Staff', 'role' => 'accounting'],
            'karyawan' => $karyawan ?? [],
            'subtitle' => 'Balance Sheet - Posisi Keuangan',
            
            // Data utama dari model
            'neraca' => $neraca,
            
            // Kelompokan data untuk view
            'aset_lancar' => $neraca['aset_lancar'] ?? [],
            'aset_tetap' => $neraca['aset_tetap'] ?? [],
            'aset_lainnya' => $neraca['aset_lainnya'] ?? [],
            'kewajiban_lancar' => $neraca['kewajiban_lancar'] ?? [],
            'kewajiban_jangka_panjang' => $neraca['kewajiban_jangka_panjang'] ?? [],
            'ekuitas' => $neraca['ekuitas'] ?? [],
            
            // Subtotal
            'subtotal_aset_lancar' => $neraca['subtotal']['aset_lancar'] ?? 0,
            'subtotal_aset_tetap' => $neraca['subtotal']['aset_tetap'] ?? 0,
            'subtotal_aset_lainnya' => $neraca['subtotal']['aset_lainnya'] ?? 0,
            'subtotal_kewajiban_lancar' => $neraca['subtotal']['kewajiban_lancar'] ?? 0,
            'subtotal_kewajiban_jangka_panjang' => $neraca['subtotal']['kewajiban_jangka_panjang'] ?? 0,
            
            // Total
            'total_aset' => $neraca['total']['aset'] ?? 0,
            'total_kewajiban' => $neraca['total']['kewajiban'] ?? 0,
            'total_ekuitas' => $neraca['total']['ekuitas'] ?? 0,
            'total_aset_formatted' => $neraca['total_formatted']['aset'] ?? 'Rp 0',
            'total_kewajiban_formatted' => $neraca['total_formatted']['kewajiban'] ?? 'Rp 0',
            'total_ekuitas_formatted' => $neraca['total_formatted']['ekuitas'] ?? 'Rp 0',
            
            // Laba bersih
            'laba_bersih' => $neraca['laba_bersih'] ?? 0,
            'laba_bersih_formatted' => $neraca['laba_bersih_formatted'] ?? 'Rp 0',
            'is_profit' => $neraca['is_profit'] ?? true,
            'total_pendapatan' => $neraca['total_pendapatan'] ?? 0,
            'total_beban' => $neraca['total_beban'] ?? 0,
            
            // Verifikasi
            'verifikasi' => $neraca['verifikasi'] ?? [],
            
            // Filters
            'filters' => [
                'tanggal_periode' => $periodeDate,
                'periode_label' => date('d F Y', strtotime($periodeDate))
            ],
            
            // Periode untuk dropdown
            'recentPeriods' => $this->getRecentPeriods(),
            
            // Stats cards
            'stats' => $this->prepareStatsCards($neraca),
            
            // Rasio keuangan
            'rasio' => $this->calculateRatios($neraca),
            
            // Chart data untuk tren
            'chart_data' => $this->getChartData($periodeDate),
            
            // Tanggal generate
            'date_generated' => date('d/m/Y H:i:s'),
            
            // Environment
            'is_development' => ENVIRONMENT !== 'production'
        ];
        
        // ============================================
        // KALKULASI LABA BERJALAN & PENYEIMBANGAN NERACA
        // (Sesuai dengan instruksi kalkulasi langsung di controller)
        // ============================================
        $db = \Config\Database::connect();
        $tahunMulai = date('Y-01-01', strtotime($periodeDate));

        // Total Pendapatan dari buku_besar
        $pendapatanResult = $db->table('coa')
            ->select('(SUM(buku_besar.kredit) - SUM(buku_besar.debit)) as saldo')
            ->join('buku_besar', 'buku_besar.coa_id = coa.id')
            ->where('coa.tipe_akun', 'Pendapatan')
            ->where('buku_besar.tanggal >=', $tahunMulai)
            ->where('buku_besar.tanggal <=', $periodeDate)
            ->where('buku_besar.status', 'processed')
            ->where('buku_besar.is_void', 0)
            ->get()->getRowArray();
        $totalPendapatan = (float) ($pendapatanResult['saldo'] ?? 0);

        // Total Beban dari buku_besar
        $bebanResult = $db->table('coa')
            ->select('(SUM(buku_besar.debit) - SUM(buku_besar.kredit)) as saldo')
            ->join('buku_besar', 'buku_besar.coa_id = coa.id')
            ->where('coa.tipe_akun', 'Beban')
            ->where('buku_besar.tanggal >=', $tahunMulai)
            ->where('buku_besar.tanggal <=', $periodeDate)
            ->where('buku_besar.status', 'processed')
            ->where('buku_besar.is_void', 0)
            ->get()->getRowArray();
        $totalBeban = (float) ($bebanResult['saldo'] ?? 0);

        // Laba Tahun Berjalan
        $laba_berjalan = $totalPendapatan - $totalBeban;

        // Pastikan array ekuitas belum mengandung LABA sebelumnya (menghindari duplikasi dari model)
        $data['ekuitas'] = array_filter($data['ekuitas'], function($item) {
            return ($item['kode_akun'] ?? '') !== 'LABA';
        });

        // Hitung ulang total ekuitas bawaan
        $totalEkuitasBawaan = array_sum(array_column($data['ekuitas'], 'saldo'));

        // Sisipkan Laba Tahun Berjalan ke kelompok Ekuitas
        $data['ekuitas'][] = [
            'id' => null,
            'kode_akun' => 'LABA',
            'nama_akun' => 'Laba Tahun Berjalan',
            'tipe_akun' => 'Ekuitas',
            'kategori' => 'Ekuitas',
            'saldo_normal' => 'Kredit',
            'saldo' => $laba_berjalan,
            'saldo_formatted' => $this->formatRupiah($laba_berjalan),
            'is_laba_berjalan' => true
        ];

        // Perbarui Total Akhir Ekuitas
        $data['total_ekuitas'] = $totalEkuitasBawaan + $laba_berjalan;
        $data['total_ekuitas_formatted'] = $this->formatRupiah($data['total_ekuitas']);
        $data['laba_bersih'] = $laba_berjalan;
        $data['laba_bersih_formatted'] = $this->formatRupiah($laba_berjalan);

        // Verifikasi Ulang Neraca (Aset = Kewajiban + Ekuitas Akhir)
        $totalPasiva = $data['total_kewajiban'] + $data['total_ekuitas'];
        $selisih = $data['total_aset'] - $totalPasiva;

        $data['verifikasi']['is_seimbang'] = abs($selisih) < 1;
        $data['verifikasi']['selisih'] = $selisih;
        $data['verifikasi']['selisih_formatted'] = $this->formatRupiah($selisih);
        $data['verifikasi']['total_ekuitas'] = $data['total_ekuitas'];
        $data['verifikasi']['total_pasiva'] = $totalPasiva;
        $data['verifikasi']['formula'] = $data['total_aset_formatted'] . ' = ' . 
                                        $data['total_kewajiban_formatted'] . ' + ' . 
                                        $data['total_ekuitas_formatted'];

        // Perbarui juga data di stats cards
        $data['stats']['total_ekuitas']['value'] = $data['total_ekuitas_formatted'];
        $data['stats']['total_ekuitas']['raw'] = $data['total_ekuitas'];
        if ($laba_berjalan >= 0) {
            $data['stats']['total_ekuitas']['color'] = 'success';
        } else {
            $data['stats']['total_ekuitas']['color'] = 'danger';
        }
        
        return view('accounting/laporan-keuangan/neraca/index', $data);
    }
    
    /**
     * Get recent periods untuk dropdown filter
     */
    private function getRecentPeriods($months = 6)
    {
        $periods = [];
        $currentDate = date('Y-m-t');
        
        for ($i = 0; $i < $months; $i++) {
            $date = date('Y-m-t', strtotime("-$i months", strtotime($currentDate)));
            $periods[] = [
                'value' => $date,
                'label' => date('F Y', strtotime($date))
            ];
        }
        
        return $periods;
    }
    
    /**
     * Prepare stats cards untuk dashboard
     */
    private function prepareStatsCards($neraca)
    {
        $totalAset = $neraca['total']['aset'] ?? 0;
        $totalKewajiban = $neraca['total']['kewajiban'] ?? 0;
        $totalEkuitas = $neraca['total']['ekuitas'] ?? 0;
        $labaBersih = $neraca['laba_bersih'] ?? 0;
        
        // Hitung rasio lancar
        $asetLancar = $neraca['subtotal']['aset_lancar'] ?? 0;
        $kewajibanLancar = $neraca['subtotal']['kewajiban_lancar'] ?? 0;
        $rasioLancar = $kewajibanLancar > 0 ? $asetLancar / $kewajibanLancar : 0;
        
        return [
            'total_aset' => [
                'value' => $neraca['total_formatted']['aset'] ?? 'Rp 0',
                'label' => 'Total Aset',
                'icon' => 'fa-building',
                'color' => 'primary',
                'raw' => $totalAset
            ],
            'total_kewajiban' => [
                'value' => $neraca['total_formatted']['kewajiban'] ?? 'Rp 0',
                'label' => 'Total Kewajiban',
                'icon' => 'fa-credit-card',
                'color' => 'warning',
                'raw' => $totalKewajiban
            ],
            'total_ekuitas' => [
                'value' => $neraca['total_formatted']['ekuitas'] ?? 'Rp 0',
                'label' => 'Total Ekuitas',
                'icon' => 'fa-chart-pie',
                'color' => $labaBersih >= 0 ? 'success' : 'danger',
                'raw' => $totalEkuitas
            ],
            'rasio_lancar' => [
                'value' => number_format($rasioLancar, 2),
                'label' => 'Rasio Lancar',
                'icon' => 'fa-water',
                'color' => 'info',
                'raw' => $rasioLancar
            ]
        ];
    }
    
    /**
     * Calculate financial ratios
     */
    private function calculateRatios($neraca)
    {
        $totalAset = $neraca['total']['aset'] ?? 0;
        $totalKewajiban = $neraca['total']['kewajiban'] ?? 0;
        $totalEkuitas = $neraca['total']['ekuitas'] ?? 0;
        $asetLancar = $neraca['subtotal']['aset_lancar'] ?? 0;
        $kewajibanLancar = $neraca['subtotal']['kewajiban_lancar'] ?? 0;
        
        // Rasio Lancar (Current Ratio)
        $currentRatio = $kewajibanLancar > 0 ? $asetLancar / $kewajibanLancar : 0;
        
        // Debt to Equity Ratio
        $der = $totalEkuitas > 0 ? $totalKewajiban / $totalEkuitas : 0;
        
        // Debt to Assets Ratio
        $dar = $totalAset > 0 ? ($totalKewajiban / $totalAset) * 100 : 0;
        
        // Working Capital
        $workingCapital = $asetLancar - $kewajibanLancar;
        
        return [
            'current_ratio' => number_format($currentRatio, 2),
            'current_ratio_raw' => $currentRatio,
            'debt_to_equity' => number_format($der, 2),
            'debt_to_equity_raw' => $der,
            'debt_to_assets' => number_format($dar, 2) . '%',
            'debt_to_assets_raw' => $dar,
            'working_capital' => $workingCapital,
            'working_capital_formatted' => $this->formatRupiah($workingCapital)
        ];
    }
    
    /**
     * Get chart data untuk tren neraca
     */
    private function getChartData($periodeDate)
    {
        $tahun = date('Y', strtotime($periodeDate));
        
        $chartData = [
            'labels' => [],
            'asets' => [],
            'kewajibans' => [],
            'ekuitas' => []
        ];
        
        // Loop 12 bulan terakhir
        for ($i = 11; $i >= 0; $i--) {
            $bulan = date('Y-m-t', strtotime("-$i months", strtotime($periodeDate)));
            $bulanLabel = date('M', strtotime($bulan));
            
            // Ambil data neraca per bulan
            $neracaBulan = $this->neracaModel->getNeraca($bulan);
            
            $chartData['labels'][] = $bulanLabel;
            $chartData['asets'][] = $neracaBulan['total']['aset'] ?? 0;
            $chartData['kewajibans'][] = $neracaBulan['total']['kewajiban'] ?? 0;
            $chartData['ekuitas'][] = $neracaBulan['total']['ekuitas'] ?? 0;
        }
        
        return [
            'labels' => $chartData['labels'],
            'datasets' => [
                [
                    'label' => 'Total Aset',
                    'data' => $chartData['asets'],
                    'backgroundColor' => 'rgba(0, 123, 255, 0.2)',
                    'borderColor' => 'rgba(0, 123, 255, 1)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Total Kewajiban',
                    'data' => $chartData['kewajibans'],
                    'backgroundColor' => 'rgba(255, 193, 7, 0.2)',
                    'borderColor' => 'rgba(255, 193, 7, 1)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Total Ekuitas',
                    'data' => $chartData['ekuitas'],
                    'backgroundColor' => 'rgba(40, 167, 69, 0.2)',
                    'borderColor' => 'rgba(40, 167, 69, 1)',
                    'borderWidth' => 1
                ]
            ]
        ];
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
            $periodeDate = $this->request->getGet('tanggal_periode') ?? date('Y-m-d');
            
            $neraca = $this->neracaModel->getNeraca($periodeDate);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'total_aset' => $neraca['total']['aset'] ?? 0,
                    'total_aset_formatted' => $neraca['total_formatted']['aset'] ?? 'Rp 0',
                    'total_kewajiban' => $neraca['total']['kewajiban'] ?? 0,
                    'total_kewajiban_formatted' => $neraca['total_formatted']['kewajiban'] ?? 'Rp 0',
                    'total_ekuitas' => $neraca['total']['ekuitas'] ?? 0,
                    'total_ekuitas_formatted' => $neraca['total_formatted']['ekuitas'] ?? 'Rp 0',
                    'laba_bersih' => $neraca['laba_bersih'] ?? 0,
                    'laba_bersih_formatted' => $neraca['laba_bersih_formatted'] ?? 'Rp 0',
                    'is_seimbang' => $neraca['verifikasi']['is_seimbang'] ?? false,
                    'periode' => date('d M Y', strtotime($periodeDate))
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
     * AJAX: Validate neraca
     */
    public function ajaxValidate()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['error' => 'Method not allowed']);
        }
        
        try {
            $periodeDate = $this->request->getGet('tanggal_periode') ?? date('Y-m-d');
            
            $neraca = $this->neracaModel->getNeraca($periodeDate);
            $verifikasi = $neraca['verifikasi'] ?? [];
            
            return $this->response->setJSON([
                'success' => true,
                'is_seimbang' => $verifikasi['is_seimbang'] ?? false,
                'selisih' => $verifikasi['selisih'] ?? 0,
                'selisih_formatted' => $verifikasi['selisih_formatted'] ?? 'Rp 0',
                'total_aset' => $verifikasi['total_aset'] ?? 0,
                'total_kewajiban' => $verifikasi['total_kewajiban'] ?? 0,
                'total_ekuitas' => $verifikasi['total_ekuitas'] ?? 0,
                'formula' => $verifikasi['formula'] ?? '',
                'message' => ($verifikasi['is_seimbang'] ?? false) ? 
                    '✓ Neraca seimbang' : 
                    '✗ Neraca tidak seimbang: ' . ($verifikasi['selisih_formatted'] ?? '')
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'AJAX Validate Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal validasi: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * AJAX: Get chart data
     */
    public function ajaxGetChartData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['error' => 'Method not allowed']);
        }
        
        try {
            $tahun = $this->request->getGet('tahun') ?? date('Y');
            $periodeDate = $tahun . '-12-31';
            $chartData = $this->getChartData($periodeDate);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $chartData,
                'message' => 'Data chart berhasil diambil'
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'AJAX Get Chart Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data chart: ' . $e->getMessage()
            ]);
        }
    }
    
    // ================================================================
    // EXPORT & PRINT
    // ================================================================
    
    /**
     * Export Laporan Neraca ke PDF
     */
    public function exportPdf()
    {
        try {
            $periodeDate = $this->request->getGet('tanggal_periode') ?? date('Y-m-d');
            
            $neraca = $this->neracaModel->getNeraca($periodeDate);
            
            // Get user info
            $userModel = model('UserModel');
            $user = $userModel->find(session()->get('user_id'));
            
            $data = [
                'title' => 'Laporan Neraca',
                'neraca' => $neraca,
                'periodeDate' => $periodeDate,
                'user' => $user ?? ['name' => 'System'],
                'date_generated' => date('d/m/Y H:i:s'),
                
                // Data untuk view print
                'aset_lancar' => $neraca['aset_lancar'] ?? [],
                'aset_tetap' => $neraca['aset_tetap'] ?? [],
                'aset_lainnya' => $neraca['aset_lainnya'] ?? [],
                'kewajiban_lancar' => $neraca['kewajiban_lancar'] ?? [],
                'kewajiban_jangka_panjang' => $neraca['kewajiban_jangka_panjang'] ?? [],
                'ekuitas' => $neraca['ekuitas'] ?? [],
                'subtotal_aset_lancar' => $neraca['subtotal']['aset_lancar'] ?? 0,
                'subtotal_aset_tetap' => $neraca['subtotal']['aset_tetap'] ?? 0,
                'subtotal_aset_lainnya' => $neraca['subtotal']['aset_lainnya'] ?? 0,
                'subtotal_kewajiban_lancar' => $neraca['subtotal']['kewajiban_lancar'] ?? 0,
                'subtotal_kewajiban_jangka_panjang' => $neraca['subtotal']['kewajiban_jangka_panjang'] ?? 0,
                'total_aset_formatted' => $neraca['total_formatted']['aset'] ?? 'Rp 0',
                'total_kewajiban_formatted' => $neraca['total_formatted']['kewajiban'] ?? 'Rp 0',
                'total_ekuitas_formatted' => $neraca['total_formatted']['ekuitas'] ?? 'Rp 0',
                'total_kewajiban' => $neraca['total']['kewajiban'] ?? 0,
                'total_ekuitas' => $neraca['total']['ekuitas'] ?? 0,
                'laba_bersih' => $neraca['laba_bersih'] ?? 0,
                'verifikasi' => $neraca['verifikasi'] ?? [],
                'rasio' => $this->calculateRatios($neraca)
            ];
            
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->setPaper('A4', 'portrait');
            $html = view('accounting/laporan-keuangan/neraca/pdf_template', $data);
            $dompdf->loadHtml($html);
            $dompdf->render();
            
            $dompdf->stream('Laporan_Neraca_' . date('Ymd_His') . '.pdf', ['Attachment' => 1]);
            
        } catch (\Exception $e) {
            log_message('error', 'Export Neraca Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengekspor: ' . $e->getMessage());
        }
    }
    
    /**
     * Print Laporan Neraca
     */
    public function print()
    {
        try {
            $periodeDate = $this->request->getGet('tanggal_periode') ?? date('Y-m-d');
            
            $neraca = $this->neracaModel->getNeraca($periodeDate);
            
            // Get user info
            $userModel = model('UserModel');
            $user = $userModel->find(session()->get('user_id'));
            
            $data = [
                'title' => 'Print Laporan Neraca',
                'neraca' => $neraca,
                'periodeDate' => $periodeDate,
                'user' => $user ?? ['name' => 'System'],
                'date_generated' => date('d/m/Y H:i:s'),
                
                // Data untuk view print
                'aset_lancar' => $neraca['aset_lancar'] ?? [],
                'aset_tetap' => $neraca['aset_tetap'] ?? [],
                'aset_lainnya' => $neraca['aset_lainnya'] ?? [],
                'kewajiban_lancar' => $neraca['kewajiban_lancar'] ?? [],
                'kewajiban_jangka_panjang' => $neraca['kewajiban_jangka_panjang'] ?? [],
                'ekuitas' => $neraca['ekuitas'] ?? [],
                'subtotal_aset_lancar' => $neraca['subtotal']['aset_lancar'] ?? 0,
                'subtotal_aset_tetap' => $neraca['subtotal']['aset_tetap'] ?? 0,
                'subtotal_aset_lainnya' => $neraca['subtotal']['aset_lainnya'] ?? 0,
                'subtotal_kewajiban_lancar' => $neraca['subtotal']['kewajiban_lancar'] ?? 0,
                'subtotal_kewajiban_jangka_panjang' => $neraca['subtotal']['kewajiban_jangka_panjang'] ?? 0,
                'total_aset_formatted' => $neraca['total_formatted']['aset'] ?? 'Rp 0',
                'total_kewajiban_formatted' => $neraca['total_formatted']['kewajiban'] ?? 'Rp 0',
                'total_ekuitas_formatted' => $neraca['total_formatted']['ekuitas'] ?? 'Rp 0',
                'total_kewajiban' => $neraca['total']['kewajiban'] ?? 0,
                'total_ekuitas' => $neraca['total']['ekuitas'] ?? 0,
                'laba_bersih' => $neraca['laba_bersih'] ?? 0,
                'verifikasi' => $neraca['verifikasi'] ?? [],
                'rasio' => $this->calculateRatios($neraca)
            ];
            
            return view('accounting/laporan-keuangan/neraca/print', $data);
            
        } catch (\Exception $e) {
            log_message('error', 'Print Neraca Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mencetak: ' . $e->getMessage());
        }
    }
}