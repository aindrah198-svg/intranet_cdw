<?php

namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\LaporanLabaRugiModel;
use App\Models\CoaModel;
use App\Models\BukuBesarModel;
use App\Models\JurnalModel;

class LaporanKeuangan extends BaseController
{
    protected $laporanLabaRugiModel;
    protected $coaModel;
    protected $bukuBesarModel;
    protected $jurnalModel;
    
    public function __construct()
    {
        helper(['form', 'number', 'date', 'text']);
        
        // Inisialisasi models
        try {
            $this->laporanLabaRugiModel = new LaporanLabaRugiModel();
            $this->coaModel = new CoaModel();
            $this->bukuBesarModel = new BukuBesarModel();
            $this->jurnalModel = new JurnalModel();
        } catch (\Exception $e) {
            log_message('error', 'Gagal load models di LaporanKeuangan: ' . $e->getMessage());
        }
    }
    
    /**
     * Halaman utama Laporan Laba Rugi
     */
    public function index()
    {
        return redirect()->to(site_url('accounting/laporan-keuangan/laporan-laba-rugi'));
    }
    
    /**
     * Display laporan laba rugi form
     */
    public function laporanLabaRugi()
    {
        // Get current user data
        $userModel = model('UserModel');
        
        $userId = session()->get('user_id');
        $user = $userModel->find($userId);
        
        // Get flash messages
        $error = session()->getFlashdata('error');
        $success = session()->getFlashdata('success');
        $warning = session()->getFlashdata('warning');
        
        // PERBAIKAN 1: Validasi dan default values yang lebih baik
        $bulan = $this->request->getGet('bulan');
        $tahun = $this->request->getGet('tahun');
        
        // Gunakan nilai default jika kosong atau tidak valid
        if (empty($bulan) || !is_numeric($bulan) || $bulan < 1 || $bulan > 12) {
            $bulan = date('m');
        }
        
        if (empty($tahun) || !is_numeric($tahun) || $tahun < 2000 || $tahun > 2100) {
            $tahun = date('Y');
        }
        
        // Format bulan dengan leading zero
        $bulan = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        
        // PERBAIKAN 2: Validasi tanggal dengan lebih hati-hati
        $tanggalMulai = "$tahun-$bulan-01";
        if (!checkdate((int)$bulan, 1, (int)$tahun)) {
            // Jika tanggal tidak valid, gunakan bulan dan tahun saat ini
            $bulan = date('m');
            $tahun = date('Y');
            $tanggalMulai = "$tahun-$bulan-01";
        }
        
        $tanggalAkhir = date('Y-m-t', strtotime($tanggalMulai));
        
        // Get data COA untuk dropdown
        $coaPendapatanList = [];
        $coaBebanList = [];
        
        if ($this->coaModel) {
            try {
                // Get akun Pendapatan
                $coaPendapatanList = $this->coaModel
                    ->select('id, kode_akun, nama_akun, saldo_normal')
                    ->where('tipe_akun', 'Pendapatan')
                    ->where('is_active', 1)
                    ->orderBy('kode_akun', 'ASC')
                    ->findAll();
                
                // Get akun Beban
                $coaBebanList = $this->coaModel
                    ->select('id, kode_akun, nama_akun, saldo_normal')
                    ->where('tipe_akun', 'Beban')
                    ->where('is_active', 1)
                    ->orderBy('kode_akun', 'ASC')
                    ->findAll();
                    
            } catch (\Exception $e) {
                log_message('error', 'Error mengambil data COA: ' . $e->getMessage());
            }
        }
        
        // Hitung statistik
        $stats = [
            'total_coa_pendapatan' => count($coaPendapatanList),
            'total_coa_beban' => count($coaBebanList),
            'periode' => $this->getNamaBulan($bulan) . ' ' . $tahun
        ];
        
        // PERBAIKAN 3: Handle preview dengan validasi tanggal
        $showPreview = $this->request->getGet('preview') ?? false;
        $previewData = null;
        
        if ($showPreview && $this->laporanLabaRugiModel) {
            try {
                // Validasi ulang sebelum generate
                if (checkdate((int)$bulan, 1, (int)$tahun)) {
                    $previewData = $this->laporanLabaRugiModel->generateLabaRugi($bulan, $tahun);
                    
                    // PERBAIKAN 4: Pastikan previewData memiliki struktur yang benar
                    if ($previewData && isset($previewData['periode'])) {
                        // Tambahkan informasi tambahan untuk view
                        $previewData['periode_display'] = $this->getNamaBulan($bulan) . ' ' . $tahun;
                        $previewData['tanggal_mulai'] = $tanggalMulai;
                        $previewData['tanggal_akhir'] = $tanggalAkhir;
                        
                        // Pastikan ada data
                        if (empty($previewData['pendapatan']) && empty($previewData['beban'])) {
                            $warning = 'Tidak ada data transaksi untuk periode ' . $previewData['periode_display'];
                        }
                    } else {
                        $error = 'Data preview tidak valid. Silakan coba lagi.';
                        $showPreview = false;
                    }
                } else {
                    $error = 'Periode yang dipilih tidak valid';
                    $showPreview = false;
                }
                
            } catch (\Exception $e) {
                log_message('error', 'Error generate preview laba rugi: ' . $e->getMessage());
                $error = 'Gagal generate preview: ' . $e->getMessage();
                $showPreview = false;
            }
        }
        
        // Get data jurnal untuk statistik
        $jurnalStats = [];
        if ($this->jurnalModel) {
            try {
                $jurnalStats = [
                    'total_jurnal' => $this->jurnalModel->countAll(),
                    'jurnal_posted' => $this->jurnalModel->where('status', 'posted')->countAllResults(),
                    'jurnal_bulan_ini' => $this->jurnalModel
                        ->where('DATE_FORMAT(tanggal, "%Y-%m")', "$tahun-$bulan")
                        ->countAllResults()
                ];
            } catch (\Exception $e) {
                log_message('error', 'Error mengambil statistik jurnal: ' . $e->getMessage());
            }
        }
        
        // Prepare data untuk view
        $data = [
            'title' => 'Laporan Laba Rugi',
            'breadcrumb' => [
                ['name' => 'Accounting', 'url' => site_url('accounting')],
                ['name' => 'Laporan Keuangan', 'url' => site_url('accounting/laporan-keuangan')],
                ['name' => 'Laporan Laba Rugi', 'active' => true]
            ],
            // Filter data
            'bulan' => $bulan,
            'tahun' => $tahun,
            'namaBulan' => $this->getNamaBulan($bulan),
            'tanggalMulai' => $tanggalMulai,
            'tanggalAkhir' => $tanggalAkhir,
            'showPreview' => $showPreview,
            
            // COA Data
            'coaPendapatanList' => $coaPendapatanList,
            'coaBebanList' => $coaBebanList,
            
            // Preview data (jika ada)
            'previewData' => $previewData,
            
            // Statistik
            'stats' => $stats,
            'jurnalStats' => $jurnalStats,
            
            // Options
            'bulanOptions' => $this->getBulanOptions(),
            'tahunOptions' => $this->getTahunOptions(),
            
            // Company info
            'company' => $this->getCompanyInfo(),
            
            // User data
            'active' => 'laporan',
            'user' => $user ?? ['name' => 'Accounting Staff', 'role' => 'accounting'],
            'subtitle' => 'Income Statement Report',
            
            // Flash messages
            'error' => $error,
            'success' => $success,
            'warning' => $warning
        ];
        
        return view('accounting/laporan-keuangan/laporan-laba-rugi/index', $data);
    }
    
    /**
     * Generate laporan laba rugi (POST method) - PERBAIKAN UTAMA
     */
    public function generateLabaRugi()
    {
        // Validasi input dengan lebih ketat
        $rules = [
            'bulan' => 'required|numeric|greater_than[0]|less_than[13]',
            'tahun' => 'required|numeric|greater_than[1999]|less_than[2100]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()
                ->with('error', 'Bulan (1-12) dan tahun (2000-2099) harus valid')
                ->withInput();
        }
        
        $bulan = $this->request->getPost('bulan');
        $tahun = $this->request->getPost('tahun');
        
        // PERBAIKAN: Validasi tanggal sebelum melanjutkan
        if (!checkdate((int)$bulan, 1, (int)$tahun)) {
            return redirect()->back()
                ->with('error', 'Tanggal tidak valid. Bulan: ' . $bulan . ', Tahun: ' . $tahun)
                ->withInput();
        }
        
        $bulan = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        
        // Gunakan model untuk menghitung laporan laba rugi
        if (!$this->laporanLabaRugiModel) {
            return redirect()->back()
                ->with('error', 'Model laporan tidak tersedia')
                ->withInput();
        }
        
        try {
            $laporanData = $this->laporanLabaRugiModel->generateLabaRugi($bulan, $tahun);
            
            // PERBAIKAN: Cek apakah data yang dikembalikan valid
            if (!$laporanData || !isset($laporanData['periode'])) {
                throw new \Exception('Data laporan tidak valid');
            }
            
            // Format data untuk view
            $formattedData = $this->formatLaporanDataForView($laporanData);
            
            // Cek apakah ada data
            if ($formattedData['total_pendapatan'] == 0 && $formattedData['total_beban'] == 0) {
                return redirect()->back()
                    ->with('warning', 'Tidak ada data transaksi untuk periode ' . $formattedData['periode'])
                    ->withInput();
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error generate laba rugi: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal generate laporan: ' . $e->getMessage())
                ->withInput();
        }
        
        // Get current user data
        $userModel = model('UserModel');
        $user = $userModel->find(session()->get('user_id'));
        
        // Prepare data untuk view
        $data = [
            'title' => 'Hasil Laporan Laba Rugi - ' . $formattedData['periode'],
            'breadcrumb' => [
                ['name' => 'Accounting', 'url' => site_url('accounting')],
                ['name' => 'Laporan Keuangan', 'url' => site_url('accounting/laporan-keuangan')],
                ['name' => 'Hasil Laporan Laba Rugi', 'active' => true]
            ],
            // Laporan data
            'laporan' => $formattedData,
            'bulan' => $bulan,
            'tahun' => $tahun,
            
            // Company info
            'company' => $this->getCompanyInfo(),
            
            // User data
            'active' => 'laporan',
            'user' => $user ?? ['name' => 'Accounting Staff', 'role' => 'accounting'],
            'subtitle' => 'Hasil Laporan Laba Rugi',
            
            // For export/print
            'export_url' => site_url('accounting/laporan-keuangan/laporan-laba-rugi/export-excel') . 
                           '?bulan=' . $bulan . '&tahun=' . $tahun,
            'print_url' => site_url('accounting/laporan-keuangan/laporan-laba-rugi/print') . 
                          '?bulan=' . $bulan . '&tahun=' . $tahun
        ];
        
        return view('accounting/laporan-keuangan/laporan-laba-rugi/hasil', $data);
    }
    
    /**
     * Print laporan laba rugi
     */
    public function printLabaRugi()
    {
        // Get parameters
        $bulan = $this->request->getGet('bulan');
        $tahun = $this->request->getGet('tahun');
        
        // Validasi dengan lebih ketat
        if (!$bulan || !$tahun || !is_numeric($bulan) || !is_numeric($tahun)) {
            return redirect()->to(site_url('accounting/laporan-keuangan/laporan-laba-rugi'))
                ->with('error', 'Parameter bulan dan tahun tidak valid');
        }
        
        // PERBAIKAN: Validasi tanggal
        if (!checkdate((int)$bulan, 1, (int)$tahun)) {
            return redirect()->to(site_url('accounting/laporan-keuangan/laporan-laba-rugi'))
                ->with('error', 'Tanggal tidak valid. Bulan: ' . $bulan . ', Tahun: ' . $tahun);
        }
        
        $bulan = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        
        // Gunakan model untuk menghitung laporan laba rugi
        if (!$this->laporanLabaRugiModel) {
            return redirect()->to(site_url('accounting/laporan-keuangan/laporan-laba-rugi'))
                ->with('error', 'Model laporan tidak tersedia');
        }
        
        try {
            $laporanData = $this->laporanLabaRugiModel->generateLabaRugi($bulan, $tahun);
            $formattedData = $this->formatLaporanDataForView($laporanData);
            
            // Get company info
            $company = $this->getCompanyInfo();
            
            // HTML untuk print
            $html = $this->generatePrintHtml($formattedData, $company);
            
            return $this->response->setContentType('text/html')->setBody($html);
            
        } catch (\Exception $e) {
            log_message('error', 'Error print laba rugi: ' . $e->getMessage());
            return redirect()->to(site_url('accounting/laporan-keuangan/laporan-laba-rugi'))
                ->with('error', 'Gagal generate print: ' . $e->getMessage());
        }
    }
    
    /**
     * Export to Excel
     */
    public function exportExcel()
    {
        // Get parameters
        $bulan = $this->request->getGet('bulan');
        $tahun = $this->request->getGet('tahun');
        
        // Validasi
        if (!$bulan || !$tahun || !is_numeric($bulan) || !is_numeric($tahun)) {
            return redirect()->back()->with('error', 'Bulan dan tahun harus diisi');
        }
        
        // PERBAIKAN: Validasi tanggal
        if (!checkdate((int)$bulan, 1, (int)$tahun)) {
            return redirect()->back()->with('error', 'Tanggal tidak valid. Bulan: ' . $bulan . ', Tahun: ' . $tahun);
        }
        
        $bulan = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        
        // Gunakan model untuk menghitung laporan laba rugi
        if (!$this->laporanLabaRugiModel) {
            return redirect()->back()->with('error', 'Model laporan tidak tersedia');
        }
        
        try {
            $laporanData = $this->laporanLabaRugiModel->generateLabaRugi($bulan, $tahun);
            $formattedData = $this->formatLaporanDataForView($laporanData);
            
            // Create CSV
            $filename = 'Laporan_Laba_Rugi_' . $this->getNamaBulan($bulan) . '_' . $tahun . '_' . date('Ymd_His') . '.csv';
            $csv = $this->generateCsv($formattedData);
            
            // Return CSV
            return $this->response
                ->setContentType('text/csv; charset=utf-8')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->setBody($csv);
                
        } catch (\Exception $e) {
            log_message('error', 'Error export excel laba rugi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export: ' . $e->getMessage());
        }
    }
    
    /**
     * Export to PDF (placeholder)
     */
    public function exportPdf()
    {
        // Get parameters
        $bulan = $this->request->getGet('bulan');
        $tahun = $this->request->getGet('tahun');
        
        if (!$bulan || !$tahun) {
            return redirect()->back()->with('error', 'Bulan dan tahun harus diisi');
        }
        
        // PERBAIKAN: Validasi tanggal
        if (!checkdate((int)$bulan, 1, (int)$tahun)) {
            return redirect()->back()->with('error', 'Tanggal tidak valid. Bulan: ' . $bulan . ', Tahun: ' . $tahun);
        }
        
        $bulan = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        
        // Untuk sementara redirect ke print
        return redirect()->to(site_url('accounting/laporan-keuangan/laporan-laba-rugi/print') . 
                            '?bulan=' . $bulan . '&tahun=' . $tahun);
    }
    
    /**
     * Check data availability (AJAX)
     */
    public function checkData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Method not allowed']);
        }
        
        $bulan = $this->request->getGet('bulan');
        $tahun = $this->request->getGet('tahun');
        
        if (!$bulan || !$tahun) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Bulan dan tahun harus diisi'
            ]);
        }
        
        // PERBAIKAN: Validasi tanggal
        if (!checkdate((int)$bulan, 1, (int)$tahun)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tanggal tidak valid. Bulan: ' . $bulan . ', Tahun: ' . $tahun
            ]);
        }
        
        $bulan = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        
        if (!$this->laporanLabaRugiModel) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Model laporan tidak tersedia'
            ]);
        }
        
        try {
            // Cek apakah ada data COA aktif
            $coaPendapatan = 0;
            $coaBeban = 0;
            
            if ($this->coaModel) {
                $coaPendapatan = $this->coaModel
                    ->where('tipe_akun', 'Pendapatan')
                    ->where('is_active', 1)
                    ->countAllResults();
                    
                $coaBeban = $this->coaModel
                    ->where('tipe_akun', 'Beban')
                    ->where('is_active', 1)
                    ->countAllResults();
            }
            
            // Cek apakah ada transaksi untuk periode ini
            $hasTransactions = false;
            if ($this->bukuBesarModel) {
                $tanggalMulai = "$tahun-$bulan-01";
                $tanggalAkhir = date('Y-m-t', strtotime($tanggalMulai));
                
                $hasTransactions = $this->bukuBesarModel
                    ->where('tanggal >=', $tanggalMulai)
                    ->where('tanggal <=', $tanggalAkhir)
                    ->countAllResults() > 0;
            }
            
            $hasDataForReport = ($coaPendapatan > 0 || $coaBeban > 0) && $hasTransactions;
            
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'coa_pendapatan' => $coaPendapatan,
                    'coa_beban' => $coaBeban,
                    'has_transactions' => $hasTransactions,
                    'has_data_for_report' => $hasDataForReport,
                    'periode' => $this->getNamaBulan($bulan) . ' ' . $tahun,
                    'message' => $hasDataForReport ? 
                        'Data tersedia untuk generate laporan' : 
                        ($hasTransactions ? 
                            'Ada transaksi tetapi belum ada akun pendapatan/beban aktif' : 
                            'Belum ada transaksi untuk periode ini')
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error check data: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error checking data: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Debug data (AJAX)
     */
    public function debugData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Method not allowed']);
        }
        
        $bulan = $this->request->getGet('bulan');
        $tahun = $this->request->getGet('tahun');
        
        if (!$bulan || !$tahun) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Bulan dan tahun harus diisi'
            ]);
        }
        
        $bulan = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        
        $debugInfo = [
            'success' => true,
            'data' => [
                'input' => [
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'periode' => $this->getNamaBulan($bulan) . ' ' . $tahun
                ],
                'validation' => [
                    'checkdate_result' => checkdate((int)$bulan, 1, (int)$tahun) ? 'VALID' : 'INVALID',
                    'tanggal_mulai' => "$tahun-$bulan-01",
                    'tanggal_akhir' => date('Y-m-t', strtotime("$tahun-$bulan-01"))
                ]
            ]
        ];
        
        return $this->response->setJSON($debugInfo);
    }
    
    /**
     * Format data laporan dari model untuk view
     */
    private function formatLaporanDataForView($laporanData)
    {
        // PERBAIKAN: Tangani jika $laporanData kosong atau tidak valid
        if (!$laporanData || !isset($laporanData['periode'])) {
            // Jika tidak ada data, kembalikan struktur kosong
            $bulan = date('m');
            $tahun = date('Y');
            $periodeStr = "$tahun-$bulan-01";
        } else {
            $periodeStr = $laporanData['periode'];
        }
        
        // Parse periode untuk mendapatkan bulan dan tahun
        $dateParts = explode('-', $periodeStr);
        if (count($dateParts) >= 2) {
            $bulan = $dateParts[1];
            $tahun = $dateParts[0];
        } else {
            // Fallback jika parsing gagal
            $bulan = date('m');
            $tahun = date('Y');
        }
        
        // Format pendapatan
        $pendapatanData = [];
        if (isset($laporanData['pendapatan']) && is_array($laporanData['pendapatan'])) {
            foreach ($laporanData['pendapatan'] as $item) {
                $pendapatanData[] = [
                    'kode_akun' => $item['kode_akun'] ?? $item['coa_kode'] ?? '',
                    'nama_akun' => $item['nama_akun'] ?? '',
                    'saldo_normal' => isset($item['saldo_normal']) ? $item['saldo_normal'] : 
                                     (isset($item['normal_saldo']) ? $item['normal_saldo'] : 'Kredit'),
                    'saldo' => $item['saldo_akhir'] ?? $item['saldo'] ?? 0
                ];
            }
        }
        
        // Format beban
        $bebanData = [];
        if (isset($laporanData['beban']) && is_array($laporanData['beban'])) {
            foreach ($laporanData['beban'] as $item) {
                $bebanData[] = [
                    'kode_akun' => $item['kode_akun'] ?? $item['coa_kode'] ?? '',
                    'nama_akun' => $item['nama_akun'] ?? '',
                    'saldo_normal' => isset($item['saldo_normal']) ? $item['saldo_normal'] : 
                                     (isset($item['normal_saldo']) ? $item['normal_saldo'] : 'Debit'),
                    'saldo' => $item['saldo_akhir'] ?? $item['saldo'] ?? 0
                ];
            }
        }
        
        return [
            'pendapatan_data' => $pendapatanData,
            'beban_data' => $bebanData,
            'total_pendapatan' => $laporanData['total_pendapatan'] ?? 0,
            'total_beban' => $laporanData['total_beban'] ?? 0,
            'laba_rugi' => $laporanData['laba_rugi'] ?? 0,
            'periode' => $this->getNamaBulan($bulan) . ' ' . $tahun,
            'tanggal_mulai' => date('Y-m-01', strtotime($periodeStr)),
            'tanggal_akhir' => date('Y-m-t', strtotime($periodeStr)),
            'status_laba_rugi' => ($laporanData['laba_rugi'] ?? 0) >= 0 ? 'LABA' : 'RUGI',
            'jumlah_akun_pendapatan' => count($pendapatanData),
            'jumlah_akun_beban' => count($bebanData),
            'sumber' => $laporanData['sumber'] ?? 'unknown'
        ];
    }
    
    /**
     * Generate HTML untuk print
     */
    private function generatePrintHtml($laporanData, $company)
    {
        // Kode yang sama seperti sebelumnya...
        // ... [tetap sama dengan kode sebelumnya] ...
    }
    
    /**
     * Generate CSV
     */
    private function generateCsv($laporanData)
    {
        // Kode yang sama seperti sebelumnya...
        // ... [tetap sama dengan kode sebelumnya] ...
    }
    
    // ==============================
    // METODE BANTUAN
    // ==============================
    
    private function getBulanOptions()
    {
        return [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
    }
    
    private function getTahunOptions()
    {
        $currentYear = date('Y');
        $years = [];
        
        for ($i = $currentYear; $i >= $currentYear - 5; $i--) {
            $years[$i] = $i;
        }
        
        // Tambahkan tahun depan juga
        $years[$currentYear + 1] = $currentYear + 1;
        
        krsort($years); // Urutkan dari tahun terbesar ke terkecil
        
        return $years;
    }
    
    private function getNamaBulan($bulan)
    {
        $bulanArr = $this->getBulanOptions();
        return $bulanArr[$bulan] ?? $bulan;
    }
    
    private function getCompanyInfo()
    {
        $db = \Config\Database::connect();
        
        $company = $db->table('perusahaan')
            ->where('kode_perusahaan', 'CDW')
            ->orWhere('id', 1)
            ->get()
            ->getRowArray();
        
        if (!$company) {
            $company = [
                'nama_perusahaan' => 'PT. CIPTA DUTA WACANA',
                'alamat' => 'Beltway Office Park Tower B Lantai 5, Jl. TB Simatupang No. 41 Ragunan-Pasar Minggu, Jakarta Selatan',
                'telepon' => '(+62-21) 29857462',
                'email' => 'info@cdw-engineering.com',
                'website' => 'www.cdw-engineering.com'
            ];
        }
        
        return $company;
    }
    
    /**
     * Neraca (placeholder)
     */
    public function neraca()
    {
        $userModel = model('UserModel');
        $user = $userModel->find(session()->get('user_id'));
        
        $data = [
            'title' => 'Neraca',
            'breadcrumb' => [
                ['name' => 'Accounting', 'url' => site_url('accounting')],
                ['name' => 'Laporan Keuangan', 'url' => site_url('accounting/laporan-keuangan')],
                ['name' => 'Neraca', 'active' => true]
            ],
            'active' => 'laporan',
            'user' => $user ?? ['name' => 'Accounting Staff', 'role' => 'accounting'],
            'subtitle' => 'Balance Sheet Report',
            'company' => $this->getCompanyInfo(),
            'message' => 'Fitur neraca sedang dalam pengembangan'
        ];
        
        return view('accounting/laporan-keuangan/neraca/index', $data);
    }
    
    /**
     * Laporan Arus Kas (placeholder)
     */
    public function laporanArusKas()
    {
        $userModel = model('UserModel');
        $user = $userModel->find(session()->get('user_id'));
        
        $data = [
            'title' => 'Laporan Arus Kas',
            'breadcrumb' => [
                ['name' => 'Accounting', 'url' => site_url('accounting')],
                ['name' => 'Laporan Keuangan', 'url' => site_url('accounting/laporan-keuangan')],
                ['name' => 'Laporan Arus Kas', 'active' => true]
            ],
            'active' => 'laporan',
            'user' => $user ?? ['name' => 'Accounting Staff', 'role' => 'accounting'],
            'subtitle' => 'Cash Flow Statement',
            'company' => $this->getCompanyInfo(),
            'message' => 'Fitur laporan arus kas sedang dalam pengembangan'
        ];
        
        return view('accounting/laporan-keuangan/arus-kas/index', $data);
    }
    
    /**
     * Laporan Modal Pemilik (placeholder)
     */
    public function laporanModalPemilik()
    {
        $userModel = model('UserModel');
        $user = $userModel->find(session()->get('user_id'));
        
        $data = [
            'title' => 'Laporan Perubahan Modal',
            'breadcrumb' => [
                ['name' => 'Accounting', 'url' => site_url('accounting')],
                ['name' => 'Laporan Keuangan', 'url' => site_url('accounting/laporan-keuangan')],
                ['name' => 'Laporan Perubahan Modal', 'active' => true]
            ],
            'active' => 'laporan',
            'user' => $user ?? ['name' => 'Accounting Staff', 'role' => 'accounting'],
            'subtitle' => 'Owner\'s Equity Statement',
            'company' => $this->getCompanyInfo(),
            'message' => 'Fitur laporan perubahan modal sedang dalam pengembangan'
        ];
        
        return view('accounting/laporan-keuangan/modal-pemilik/index', $data);
    }
}