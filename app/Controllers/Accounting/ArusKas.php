<?php

namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\LaporanArusKasModel;

class ArusKas extends BaseController
{
    protected $arusKasModel;
    
    public function __construct()
    {
        $this->arusKasModel = new LaporanArusKasModel();
        helper(['form', 'number', 'text', 'date']);
    }
    
    /**
     * Display Laporan Arus Kas (Cash Flow Statement) - DIPERBAIKI
     * SESUAI DENGAN MODEL VERSI 2.0.1
     */
    public function index()
    {
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
        $excludeSaldoAwal = $this->request->getGet('exclude_saldo_awal') ?? '1';
        $format = $this->request->getGet('format') ?? 'standard';
        
        // Clear cache untuk data fresh
        $this->arusKasModel->clearCache();
        
        // ============================================
        // GET LAPORAN ARUS KAS DARI MODEL FIXED
        // ============================================
        $laporan = $this->arusKasModel->getLaporanArusKas(
            $startDate, 
            $endDate, 
            $excludeSaldoAwal == '1'
        );
        
        // ============================================
        // EXTRACT DATA DARI MODEL
        // ============================================
        $akunKas = $laporan['akun_kas'] ?? [];
        $saldoKas = $laporan['saldo_kas'] ?? [];
        $arusKas = $laporan['arus_kas'] ?? [];
        $detailPerAkun = $laporan['detail_per_akun'] ?? [];
        $verifikasi = $laporan['verifikasi'] ?? [];
        $statistik = $laporan['statistik'] ?? [];
        $summary = $laporan['summary'] ?? [];
        $periode = $laporan['periode'] ?? [];
        $metadata = $laporan['metadata'] ?? [];
        
        // ============================================
        // STATS CARDS - SESUAI DENGAN STRUKTUR MODEL
        // ============================================
        $statsCards = [
            'operasi' => [
                'value' => isset($arusKas['operasi']['total']) ? 
                    'Rp ' . number_format($arusKas['operasi']['total'], 0, ',', '.') : 'Rp 0',
                'label' => 'Arus Kas Operasi',
                'icon' => 'fas fa-industry',
                'color' => ($arusKas['operasi']['total'] ?? 0) >= 0 ? 'success' : 'danger',
                'trend' => ($arusKas['operasi']['total'] ?? 0) >= 0 ? 'Cash Inflow' : 'Cash Outflow',
                'penerimaan' => 'Rp ' . number_format($arusKas['operasi']['penerimaan'] ?? 0, 0, ',', '.'),
                'pengeluaran' => 'Rp ' . number_format($arusKas['operasi']['pengeluaran'] ?? 0, 0, ',', '.')
            ],
            'investasi' => [
                'value' => isset($arusKas['investasi']['total']) ? 
                    'Rp ' . number_format($arusKas['investasi']['total'], 0, ',', '.') : 'Rp 0',
                'label' => 'Arus Kas Investasi',
                'icon' => 'fas fa-chart-line',
                'color' => ($arusKas['investasi']['total'] ?? 0) >= 0 ? 'success' : 'warning',
                'trend' => ($arusKas['investasi']['total'] ?? 0) >= 0 ? 'Cash Inflow' : 'Cash Outflow',
                'penerimaan' => 'Rp ' . number_format($arusKas['investasi']['penerimaan'] ?? 0, 0, ',', '.'),
                'pengeluaran' => 'Rp ' . number_format($arusKas['investasi']['pengeluaran'] ?? 0, 0, ',', '.')
            ],
            'pendanaan' => [
                'value' => isset($arusKas['pendanaan']['total']) ? 
                    'Rp ' . number_format($arusKas['pendanaan']['total'], 0, ',', '.') : 'Rp 0',
                'label' => 'Arus Kas Pendanaan',
                'icon' => 'fas fa-hand-holding-usd',
                'color' => ($arusKas['pendanaan']['total'] ?? 0) >= 0 ? 'success' : 'info',
                'trend' => ($arusKas['pendanaan']['total'] ?? 0) >= 0 ? 'Cash Inflow' : 'Cash Outflow',
                'penerimaan' => 'Rp ' . number_format($arusKas['pendanaan']['penerimaan'] ?? 0, 0, ',', '.'),
                'pengeluaran' => 'Rp ' . number_format($arusKas['pendanaan']['pengeluaran'] ?? 0, 0, ',', '.')
            ],
            'saldo' => [
                'value' => isset($saldoKas['saldo_akhir_formatted']) ? 
                    $saldoKas['saldo_akhir_formatted'] : 'Rp 0',
                'label' => 'Saldo Kas Akhir',
                'icon' => 'fas fa-piggy-bank',
                'color' => 'primary',
                'trend' => 'Ending Balance',
                'saldo_awal' => $saldoKas['saldo_awal_formatted'] ?? 'Rp 0',
                'perubahan' => $saldoKas['perubahan_kas_formatted'] ?? 'Rp 0'
            ]
        ];
        
        // ============================================
        // STATUS VERIFIKASI
        // ============================================
        $verifikasiStatus = [
            'is_valid' => $verifikasi['is_valid'] ?? false,
            'message' => ($verifikasi['is_valid'] ?? false) ? 'VALID' : 'TIDAK VALID',
            'color' => ($verifikasi['is_valid'] ?? false) ? 'success' : 'danger',
            'icon' => ($verifikasi['is_valid'] ?? false) ? 'fa-check-circle' : 'fa-exclamation-triangle',
            'selisih' => $verifikasi['selisih'] ?? 0,
            'selisih_formatted' => $verifikasi['selisih_formatted'] ?? 'Rp 0',
            'keterangan' => $verifikasi['keterangan'] ?? '',
            'formula' => $verifikasi['formula'] ?? ''
        ];
        
        // ============================================
        // HITUNG TOTAL TRANSAKSI
        // ============================================
        $totalTransaksiOperasi = count($arusKas['operasi']['items'] ?? []);
        $totalTransaksiInvestasi = count($arusKas['investasi']['items'] ?? []);
        $totalTransaksiPendanaan = count($arusKas['pendanaan']['items'] ?? []);
        $totalTransaksi = $totalTransaksiOperasi + $totalTransaksiInvestasi + $totalTransaksiPendanaan;
        
        // ============================================
        // PREPARE DATA UNTUK VIEW
        // ============================================
        $data = [
            'title' => 'Laporan Arus Kas',
            'active' => 'bookkeeping',
            'user' => $user ?? ['name' => 'Accounting Staff', 'role' => 'accounting'],
            'karyawan' => $karyawan ?? [],
            'subtitle' => 'Cash Flow Statement - Metode Langsung',
            
            // =========== DATA UTAMA DARI MODEL ===========
            'laporan' => $laporan,
            'akun_kas' => $akunKas,
            'saldo_kas' => $saldoKas,
            'arus_kas' => $arusKas,
            'detail_per_akun' => $detailPerAkun,
            'verifikasi' => $verifikasiStatus,
            'statistik' => $statistik,
            'summary' => $summary,
            'periode' => $periode,
            'metadata' => $metadata,
            
            // =========== TRANSAKSI PER AKTIVITAS ===========
            'transaksi_operasi' => $arusKas['operasi']['items'] ?? [],
            'transaksi_investasi' => $arusKas['investasi']['items'] ?? [],
            'transaksi_pendanaan' => $arusKas['pendanaan']['items'] ?? [],
            
            // =========== TOTAL PER AKTIVITAS ===========
            'total_operasi' => $arusKas['operasi']['total'] ?? 0,
            'total_investasi' => $arusKas['investasi']['total'] ?? 0,
            'total_pendanaan' => $arusKas['pendanaan']['total'] ?? 0,
            'total_arus_kas' => $laporan['total_arus_kas'] ?? 0,
            
            // =========== STATISTIK TRANSAKSI ===========
            'total_transaksi_operasi' => $totalTransaksiOperasi,
            'total_transaksi_investasi' => $totalTransaksiInvestasi,
            'total_transaksi_pendanaan' => $totalTransaksiPendanaan,
            'total_transaksi' => $totalTransaksi,
            
            // =========== STATS CARDS ===========
            'stats' => $statsCards,
            
            // =========== FILTERS ===========
            'filters' => [
                'tanggal_mulai' => $startDate,
                'tanggal_selesai' => $endDate,
                'exclude_saldo_awal' => $excludeSaldoAwal,
                'format' => $format
            ],
            
            // =========== PERIODE ===========
            'recentPeriods' => $this->getRecentPeriods(),
            'date_generated' => date('Y-m-d H:i:s'),
            
            // =========== DEBUG INFO ===========
            'debug_info' => [
                'total_akun_kas' => count($akunKas),
                'akun_kas_codes' => array_column($akunKas, 'kode_akun'),
                'total_transaksi' => $totalTransaksi,
                'saldo_awal' => $saldoKas['saldo_awal'] ?? 0,
                'saldo_akhir' => $saldoKas['saldo_akhir'] ?? 0,
                'perubahan_kas' => $saldoKas['perubahan_kas'] ?? 0,
                'verifikasi_status' => $verifikasiStatus['message'],
                'metode_perhitungan' => $metadata['calculation_method'] ?? 'arus_kas_fixed',
                'version' => $metadata['version'] ?? '2.0.1'
            ],
            
            // =========== NOTES ===========
            'notes' => [
                'saldo_awal_note' => '✓ Transaksi saldo awal modal/kas sudah difilter (bukan arus kas periode berjalan)',
                'classification_note' => '✓ Klasifikasi berdasarkan counterpart akun: Modal → Pendanaan, Aset Tetap → Investasi, Pendapatan/Beban → Operasi',
                'cash_only_note' => '✓ Hanya transaksi yang mempengaruhi akun kas/bank yang ditampilkan',
                'non_cash_note' => '✓ Transaksi non-kas (jurnal penyesuaian internal) tidak dimasukkan dalam arus kas',
                'verification_note' => $verifikasiStatus['is_valid'] ? 
                    '✓ Laporan arus kas valid (Saldo Awal + Arus Kas Bersih = Saldo Akhir)' : 
                    '✗ Laporan arus kas tidak valid - periksa jurnal penyesuaian yang menggunakan akun kas'
            ],
            
// =========== CHART DATA ===========
'chart_data' => $this->prepareChartData(
    method_exists($this->arusKasModel, 'getArusKasBulanan') 
        ? $this->arusKasModel->getArusKasBulanan(date('Y', strtotime($startDate)))
        : $this->arusKasModel->getTrenArusKas(date('Y', strtotime($startDate)))
),
            
            // =========== ENVIRONMENT ===========
            'is_development' => ENVIRONMENT !== 'production'
        ];
        
        return view('accounting/laporan-keuangan/laporan-arus-kas/index', $data);
    }
    
    /**
     * Debug Laporan Arus Kas - Untuk troubleshooting
     */
    public function debug()
    {
        // Hanya admin dan development mode
        if (ENVIRONMENT === 'production' && session()->get('role') !== 'admin') {
            return redirect()->to('/accounting/arus-kas')->with('error', 'Debug hanya untuk development');
        }
        
        $startDate = $this->request->getGet('tanggal_mulai') ?? date('Y-m-01');
        $endDate = $this->request->getGet('tanggal_selesai') ?? date('Y-m-t');
        $excludeSaldoAwal = $this->request->getGet('exclude_saldo_awal') ?? '1';
        
        $this->arusKasModel->clearCache();
        
        // ============================================
        // 1. GET LAPORAN DARI MODEL
        // ============================================
        $laporan = $this->arusKasModel->getLaporanArusKas(
            $startDate, 
            $endDate, 
            $excludeSaldoAwal == '1'
        );
        
        // ============================================
        // 2. GET DATA MENTAH UNTUK DEBUG
        // ============================================
        $debugMentah = $this->arusKasModel->debugDataMentah($startDate, $endDate);
        
        // ============================================
        // 3. HITUNG SALDO MANUAL
        // ============================================
        $saldoManual = $this->arusKasModel->hitungSaldoManual($startDate, $endDate);
        
        // ============================================
        // 4. ANALISIS KLASIFIKASI
        // ============================================
        $classificationAnalysis = [];
        
        // Cek transaksi operasi yang seharusnya bukan operasi
        foreach ($laporan['arus_kas']['operasi']['items'] ?? [] as $item) {
            $keterangan = strtolower($item['keterangan'] ?? '');
            
            if (strpos($keterangan, 'saldo awal') !== false) {
                $classificationAnalysis[] = [
                    'type' => 'SALDO_AWAL_DI_OPERASI',
                    'tanggal' => $item['tanggal'],
                    'nomor_jurnal' => $item['nomor_jurnal'],
                    'keterangan' => $item['keterangan'],
                    'jumlah' => $item['jumlah'],
                    'arus_kas' => $item['arus_kas'],
                    'current_classification' => 'OPERASI',
                    'suggested_classification' => 'FILTERED (BUKAN TRANSAKSI)',
                    'severity' => 'HIGH'
                ];
            }
            
            if (strpos($keterangan, 'modal') !== false && 
                strpos($keterangan, 'setoran') !== false) {
                $classificationAnalysis[] = [
                    'type' => 'MODAL_DI_OPERASI',
                    'tanggal' => $item['tanggal'],
                    'nomor_jurnal' => $item['nomor_jurnal'],
                    'keterangan' => $item['keterangan'],
                    'jumlah' => $item['jumlah'],
                    'arus_kas' => $item['arus_kas'],
                    'current_classification' => 'OPERASI',
                    'suggested_classification' => 'PENDANAAN',
                    'severity' => 'MEDIUM'
                ];
            }
        }
        
        // Cek transaksi yang counterpartnya aset tetap
        foreach ($laporan['arus_kas']['operasi']['items'] ?? [] as $item) {
            foreach ($item['counterpart'] ?? [] as $cp) {
                $kodeAkun = $cp['kode_akun'] ?? '';
                if (strpos($kodeAkun, '1-2') === 0) {
                    $classificationAnalysis[] = [
                        'type' => 'INVESTASI_DI_OPERASI',
                        'tanggal' => $item['tanggal'],
                        'nomor_jurnal' => $item['nomor_jurnal'],
                        'keterangan' => $item['keterangan'],
                        'counterpart' => $cp['kode_akun'] . ' - ' . $cp['nama_akun'],
                        'current_classification' => 'OPERASI',
                        'suggested_classification' => 'INVESTASI',
                        'severity' => 'HIGH'
                    ];
                }
            }
        }
        
        return view('accounting/laporan-keuangan/laporan-arus-kas/debug', [
            'title' => 'Debug Laporan Arus Kas',
            'active' => 'bookkeeping',
            'laporan' => $laporan,
            'debug_mentah' => $debugMentah,
            'saldo_manual' => $saldoManual,
            'classification_analysis' => $classificationAnalysis,
            'filters' => [
                'tanggal_mulai' => $startDate,
                'tanggal_selesai' => $endDate,
                'exclude_saldo_awal' => $excludeSaldoAwal
            ],
            'summary' => [
                'periode' => date('d M Y', strtotime($startDate)) . ' - ' . date('d M Y', strtotime($endDate)),
                'akun_kas_terdeteksi' => count($laporan['akun_kas'] ?? []),
                'transaksi_terdeteksi' => count($laporan['transaksi_kas'] ?? []),
                'total_arus_operasi' => $laporan['total_operasi'] ?? 0,
                'total_arus_investasi' => $laporan['total_investasi'] ?? 0,
                'total_arus_pendanaan' => $laporan['total_pendanaan'] ?? 0,
                'perubahan_kas_bersih' => $laporan['total_arus_kas'] ?? 0,
                'saldo_awal' => $laporan['saldo_kas']['saldo_awal'] ?? 0,
                'saldo_akhir' => $laporan['saldo_kas']['saldo_akhir'] ?? 0,
                'verifikasi' => ($laporan['verifikasi']['is_valid'] ?? false) ? 'VALID' : 'INVALID'
            ]
        ]);
    }
    
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
            $excludeSaldoAwal = $this->request->getGet('exclude_saldo_awal') ?? '1';
            
            $this->arusKasModel->clearCache();
            
            $ringkasan = $this->arusKasModel->getRingkasanArusKas($startDate, $endDate);
            
            return $this->response->setJSON($ringkasan);
            
        } catch (\Exception $e) {
            log_message('error', 'AJAX Get Summary Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil summary: ' . $e->getMessage(),
                'data' => [
                    'saldo_awal' => 0,
                    'saldo_akhir' => 0,
                    'saldo_awal_formatted' => 'Rp 0',
                    'saldo_akhir_formatted' => 'Rp 0',
                    'perubahan_kas' => 0,
                    'arus_operasi' => 0,
                    'arus_investasi' => 0,
                    'arus_pendanaan' => 0,
                    'total_arus_kas' => 0,
                    'is_valid' => false,
                    'is_valid_label' => 'ERROR',
                    'periode' => date('d M Y', strtotime($startDate)) . ' - ' . date('d M Y', strtotime($endDate))
                ]
            ]);
        }
    }
    
    /**
     * AJAX: Get detailed arus kas
     */
    public function ajaxGetDetail()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['error' => 'Method not allowed']);
        }
        
        try {
            $startDate = $this->request->getGet('tanggal_mulai') ?? date('Y-m-01');
            $endDate = $this->request->getGet('tanggal_selesai') ?? date('Y-m-t');
            $excludeSaldoAwal = $this->request->getGet('exclude_saldo_awal') ?? '1';
            
            $this->arusKasModel->clearCache();
            
            $laporan = $this->arusKasModel->getLaporanArusKas(
                $startDate, 
                $endDate, 
                $excludeSaldoAwal == '1'
            );
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $laporan,
                'period' => date('d M Y', strtotime($startDate)) . ' - ' . date('d M Y', strtotime($endDate)),
                'message' => 'Data arus kas berhasil diambil',
                'note' => '✓ Transaksi saldo awal modal sudah difilter dari laporan'
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'AJAX Get Detail Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * AJAX: Validate arus kas
     */
    public function ajaxValidate()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['error' => 'Method not allowed']);
        }
        
        try {
            $startDate = $this->request->getGet('tanggal_mulai') ?? date('Y-m-01');
            $endDate = $this->request->getGet('tanggal_selesai') ?? date('Y-m-t');
            $excludeSaldoAwal = $this->request->getGet('exclude_saldo_awal') ?? '1';
            
            $this->arusKasModel->clearCache();
            
            $laporan = $this->arusKasModel->getLaporanArusKas(
                $startDate, 
                $endDate, 
                $excludeSaldoAwal == '1'
            );
            
            // Cek apakah ada transaksi saldo awal yang masih masuk
            $hasSaldoAwalError = false;
            $saldoAwalTransactions = [];
            
            foreach ($laporan['arus_kas']['operasi']['items'] ?? [] as $item) {
                $keterangan = strtolower($item['keterangan'] ?? '');
                if (strpos($keterangan, 'saldo awal') !== false) {
                    $hasSaldoAwalError = true;
                    $saldoAwalTransactions[] = [
                        'tanggal' => $item['tanggal'],
                        'keterangan' => $item['keterangan'],
                        'jumlah' => $item['jumlah']
                    ];
                }
            }
            
            return $this->response->setJSON([
                'success' => true,
                'is_valid' => $laporan['verifikasi']['is_valid'] ?? false,
                'selisih' => $laporan['verifikasi']['selisih'] ?? 0,
                'selisih_formatted' => $laporan['verifikasi']['selisih_formatted'] ?? 'Rp 0',
                'saldo_awal' => $laporan['saldo_kas']['saldo_awal'] ?? 0,
                'saldo_akhir' => $laporan['saldo_kas']['saldo_akhir'] ?? 0,
                'perubahan_kas' => $laporan['saldo_kas']['perubahan_kas'] ?? 0,
                'total_arus_kas' => $laporan['total_arus_kas'] ?? 0,
                'verifikasi_formula' => $laporan['verifikasi']['formula'] ?? '',
                'has_saldo_awal_error' => $hasSaldoAwalError,
                'saldo_awal_transactions' => $saldoAwalTransactions,
                'exclude_saldo_awal' => $excludeSaldoAwal == '1',
                'message' => ($laporan['verifikasi']['is_valid'] ?? false) ? 
                    '✓ Laporan arus kas valid' : 
                    '✗ Laporan arus kas tidak valid: ' . ($laporan['verifikasi']['keterangan'] ?? ''),
                'warning' => $hasSaldoAwalError ? 
                    '⚠ Masih ada transaksi saldo awal yang masuk ke laporan' : 
                    '✓ Tidak ada transaksi saldo awal'
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
     * AJAX: Get arus kas bulanan untuk chart
     */
    public function ajaxGetChartData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['error' => 'Method not allowed']);
        }
        
        try {
            $tahun = $this->request->getGet('tahun') ?? date('Y');
            $data = $this->arusKasModel->getArusKasBulanan($tahun);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $data,
                'chart' => $this->prepareChartData($data),
                'tahun' => $tahun,
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
    
    /**
     * Get recent periods (6 bulan terakhir)
     */
    private function getRecentPeriods($months = 6)
    {
        $periods = [];
        $currentDate = date('Y-m-t');
        
        for ($i = 0; $i < $months; $i++) {
            $date = date('Y-m-t', strtotime("-$i months", strtotime($currentDate)));
            $periods[$date] = [
                'label' => date('F Y', strtotime($date)),
                'start' => date('Y-m-01', strtotime($date)),
                'end' => $date
            ];
        }
        
        return $periods;
    }
    
 /**
 * Prepare chart data untuk dashboard
 * DIPERBAIKI - Support format dari getTrenArusKas()
 */
private function prepareChartData($data)
{
    // Jika data kosong
    if (empty($data)) {
        return [
            'labels' => [],
            'datasets' => [
                [
                    'label' => 'Penerimaan Kas',
                    'data' => [],
                    'backgroundColor' => 'rgba(40, 167, 69, 0.2)',
                    'borderColor' => 'rgba(40, 167, 69, 1)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Pengeluaran Kas',
                    'data' => [],
                    'backgroundColor' => 'rgba(220, 53, 69, 0.2)',
                    'borderColor' => 'rgba(220, 53, 69, 1)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Arus Kas Bersih',
                    'data' => [],
                    'type' => 'line',
                    'fill' => false,
                    'borderColor' => 'rgba(0, 123, 255, 1)',
                    'borderWidth' => 2,
                    'pointBackgroundColor' => 'rgba(0, 123, 255, 1)'
                ]
            ]
        ];
    }
    
    // CEK FORMAT DATA - Apakah dari getTrenArusKas() atau dari getArusKasBulanan()
    if (isset($data['labels']) && isset($data['penerimaan']) && isset($data['pengeluaran']) && isset($data['arus_kas'])) {
        // Format dari getTrenArusKas()
        return [
            'labels' => $data['labels'],
            'datasets' => [
                [
                    'label' => 'Penerimaan Kas',
                    'data' => $data['penerimaan'],
                    'backgroundColor' => 'rgba(40, 167, 69, 0.2)',
                    'borderColor' => 'rgba(40, 167, 69, 1)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Pengeluaran Kas',
                    'data' => $data['pengeluaran'],
                    'backgroundColor' => 'rgba(220, 53, 69, 0.2)',
                    'borderColor' => 'rgba(220, 53, 69, 1)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Arus Kas Bersih',
                    'data' => $data['arus_kas'],
                    'type' => 'line',
                    'fill' => false,
                    'borderColor' => 'rgba(0, 123, 255, 1)',
                    'borderWidth' => 2,
                    'pointBackgroundColor' => 'rgba(0, 123, 255, 1)'
                ]
            ]
        ];
    } 
    // Format dari getArusKasBulanan() - array biasa
    else if (is_array($data) && !isset($data['labels'])) {
        $labels = [];
        $penerimaan = [];
        $pengeluaran = [];
        $arusKas = [];
        
        foreach ($data as $item) {
            $labels[] = $item['bulan'] ?? '';
            $penerimaan[] = $item['penerimaan'] ?? 0;
            $pengeluaran[] = $item['pengeluaran'] ?? 0;
            $arusKas[] = $item['arus_kas'] ?? 0;
        }
        
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Penerimaan Kas',
                    'data' => $penerimaan,
                    'backgroundColor' => 'rgba(40, 167, 69, 0.2)',
                    'borderColor' => 'rgba(40, 167, 69, 1)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Pengeluaran Kas',
                    'data' => $pengeluaran,
                    'backgroundColor' => 'rgba(220, 53, 69, 0.2)',
                    'borderColor' => 'rgba(220, 53, 69, 1)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Arus Kas Bersih',
                    'data' => $arusKas,
                    'type' => 'line',
                    'fill' => false,
                    'borderColor' => 'rgba(0, 123, 255, 1)',
                    'borderWidth' => 2,
                    'pointBackgroundColor' => 'rgba(0, 123, 255, 1)'
                ]
            ]
        ];
    }
    
    // Fallback: return data apa adanya
    return $data;
}
    
    /**
     * Export Laporan Arus Kas
     */
    public function export()
    {
        try {
            $startDate = $this->request->getGet('tanggal_mulai') ?? date('Y-m-01');
            $endDate = $this->request->getGet('tanggal_selesai') ?? date('Y-m-t');
            $excludeSaldoAwal = $this->request->getGet('exclude_saldo_awal') ?? '1';
            $type = $this->request->getGet('type') ?? 'csv';
            
            $this->arusKasModel->clearCache();
            
            $laporan = $this->arusKasModel->getLaporanArusKas(
                $startDate, 
                $endDate, 
                $excludeSaldoAwal == '1'
            );
            
            if ($type == 'csv') {
                return $this->exportToCSV($laporan, $startDate, $endDate);
            } else {
                return $this->exportToExcel($laporan, $startDate, $endDate);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Export Arus Kas Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengekspor: ' . $e->getMessage());
        }
    }
    
    /**
     * Export to CSV - DIPERBAIKI
     */
    private function exportToCSV($laporan, $startDate, $endDate)
    {
        $filename = 'laporan_arus_kas_' . date('Ymd_His') . '.csv';
        $csv = '';
        
        // Header
        $csv .= "LAPORAN ARUS KAS - PT. CIPTA DUTA WACANA\n";
        $csv .= "Periode: " . date('d/m/Y', strtotime($startDate)) . " - " . date('d/m/Y', strtotime($endDate)) . "\n";
        $csv .= "Metode: Langsung (Direct Method)\n";
        $csv .= "Tanggal Generate: " . date('d/m/Y H:i:s') . "\n";
        $csv .= "Status: " . ($laporan['verifikasi']['is_valid'] ? 'VALID' : 'TIDAK VALID') . "\n";
        $csv .= "\n";
        
        // Saldo Awal
        $csv .= "SALDO KAS AWAL PERIODE,,\n";
        $csv .= "Kode Akun,Nama Akun,Saldo Awal (Rp)\n";
        
        $totalSaldoAwal = 0;
        foreach ($laporan['detail_per_akun'] as $akun) {
            if ($akun['saldo_awal'] > 0) {
                $csv .= $akun['kode_akun'] . ',' . 
                       $akun['nama_akun'] . ',' . 
                       number_format($akun['saldo_awal'], 2) . "\n";
                $totalSaldoAwal += $akun['saldo_awal'];
            }
        }
        
        $csv .= "TOTAL SALDO KAS AWAL,," . number_format($totalSaldoAwal, 2) . "\n";
        $csv .= "\n";
        
        // Aktivitas Operasi
        $csv .= "AKTIVITAS OPERASI\n";
        $csv .= "No.,Tanggal,Nomor Jurnal,Kode Akun,Keterangan,Counterpart,Penerimaan (Rp),Pengeluaran (Rp)\n";
        
        $counter = 1;
        foreach ($laporan['arus_kas']['operasi']['items'] as $item) {
            $counterpartText = '';
            foreach ($item['counterpart'] as $cp) {
                $counterpartText .= $cp['kode_akun'] . ' ' . $cp['nama_akun'] . '; ';
            }
            
            $csv .= $counter++ . ',' . 
                   date('d/m/Y', strtotime($item['tanggal'])) . ',' .
                   $item['nomor_jurnal'] . ',' .
                   $item['kode_akun_kas'] . ',"' . 
                   $item['keterangan'] . '","' . 
                   trim($counterpartText) . '",' . 
                   ($item['penerimaan'] > 0 ? number_format($item['penerimaan'], 2) : '') . ',' .
                   ($item['pengeluaran'] > 0 ? number_format($item['pengeluaran'], 2) : '') . "\n";
        }
        
        $csv .= "SUBTOTAL ARUS KAS OPERASI,,,," . 
               number_format($laporan['arus_kas']['operasi']['penerimaan'], 2) . ',' . 
               number_format($laporan['arus_kas']['operasi']['pengeluaran'], 2) . "\n";
        $csv .= "ARUS KAS BERSIH DARI OPERASI,,,,," . 
               number_format($laporan['arus_kas']['operasi']['total'], 2) . "\n";
        $csv .= "\n";
        
        // Aktivitas Investasi
        $csv .= "AKTIVITAS INVESTASI\n";
        $csv .= "No.,Tanggal,Nomor Jurnal,Kode Akun,Keterangan,Counterpart,Penerimaan (Rp),Pengeluaran (Rp)\n";
        
        $counter = 1;
        foreach ($laporan['arus_kas']['investasi']['items'] as $item) {
            $counterpartText = '';
            foreach ($item['counterpart'] as $cp) {
                $counterpartText .= $cp['kode_akun'] . ' ' . $cp['nama_akun'] . '; ';
            }
            
            $csv .= $counter++ . ',' . 
                   date('d/m/Y', strtotime($item['tanggal'])) . ',' .
                   $item['nomor_jurnal'] . ',' .
                   $item['kode_akun_kas'] . ',"' . 
                   $item['keterangan'] . '","' . 
                   trim($counterpartText) . '",' . 
                   ($item['penerimaan'] > 0 ? number_format($item['penerimaan'], 2) : '') . ',' .
                   ($item['pengeluaran'] > 0 ? number_format($item['pengeluaran'], 2) : '') . "\n";
        }
        
        $csv .= "SUBTOTAL ARUS KAS INVESTASI,,,," . 
               number_format($laporan['arus_kas']['investasi']['penerimaan'], 2) . ',' . 
               number_format($laporan['arus_kas']['investasi']['pengeluaran'], 2) . "\n";
        $csv .= "ARUS KAS BERSIH DARI INVESTASI,,,,," . 
               number_format($laporan['arus_kas']['investasi']['total'], 2) . "\n";
        $csv .= "\n";
        
        // Aktivitas Pendanaan
        $csv .= "AKTIVITAS PENDANAAN\n";
        $csv .= "No.,Tanggal,Nomor Jurnal,Kode Akun,Keterangan,Counterpart,Penerimaan (Rp),Pengeluaran (Rp)\n";
        
        $counter = 1;
        foreach ($laporan['arus_kas']['pendanaan']['items'] as $item) {
            $counterpartText = '';
            foreach ($item['counterpart'] as $cp) {
                $counterpartText .= $cp['kode_akun'] . ' ' . $cp['nama_akun'] . '; ';
            }
            
            $csv .= $counter++ . ',' . 
                   date('d/m/Y', strtotime($item['tanggal'])) . ',' .
                   $item['nomor_jurnal'] . ',' .
                   $item['kode_akun_kas'] . ',"' . 
                   $item['keterangan'] . '","' . 
                   trim($counterpartText) . '",' . 
                   ($item['penerimaan'] > 0 ? number_format($item['penerimaan'], 2) : '') . ',' .
                   ($item['pengeluaran'] > 0 ? number_format($item['pengeluaran'], 2) : '') . "\n";
        }
        
        $csv .= "SUBTOTAL ARUS KAS PENDANAAN,,,," . 
               number_format($laporan['arus_kas']['pendanaan']['penerimaan'], 2) . ',' . 
               number_format($laporan['arus_kas']['pendanaan']['pengeluaran'], 2) . "\n";
        $csv .= "ARUS KAS BERSIH DARI PENDANAAN,,,,," . 
               number_format($laporan['arus_kas']['pendanaan']['total'], 2) . "\n";
        $csv .= "\n";
        
        // Ringkasan
        $csv .= "RINGKASAN ARUS KAS\n";
        $csv .= "Total Arus Kas dari Aktivitas Operasi,," . number_format($laporan['arus_kas']['operasi']['total'], 2) . "\n";
        $csv .= "Total Arus Kas dari Aktivitas Investasi,," . number_format($laporan['arus_kas']['investasi']['total'], 2) . "\n";
        $csv .= "Total Arus Kas dari Aktivitas Pendanaan,," . number_format($laporan['arus_kas']['pendanaan']['total'], 2) . "\n";
        $csv .= "PERUBAHAN KAS BERSIH,," . number_format($laporan['total_arus_kas'], 2) . "\n";
        $csv .= "\n";
        
        // Saldo Akhir
        $csv .= "SALDO KAS AKHIR PERIODE\n";
        $csv .= "Saldo Kas Awal," . number_format($laporan['saldo_kas']['saldo_awal'], 2) . "\n";
        $csv .= "Perubahan Kas Bersih," . number_format($laporan['total_arus_kas'], 2) . "\n";
        $csv .= "Saldo Kas Akhir (Perhitungan)," . number_format($laporan['saldo_kas']['saldo_awal'] + $laporan['total_arus_kas'], 2) . "\n";
        $csv .= "Saldo Kas Akhir (Buku Besar)," . number_format($laporan['saldo_kas']['saldo_akhir'], 2) . "\n";
        $csv .= "Status Verifikasi," . ($laporan['verifikasi']['is_valid'] ? 'VALID' : 'TIDAK VALID') . "\n";
        
        return $this->response
            ->setContentType('text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($csv);
    }
    
    /**
     * Export to Excel (simplified)
     */
    private function exportToExcel($laporan, $startDate, $endDate)
    {
        // Untuk simplicity, gunakan CSV dulu
        return $this->exportToCSV($laporan, $startDate, $endDate);
    }
}