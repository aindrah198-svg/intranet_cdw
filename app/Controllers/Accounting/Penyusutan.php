<?php
namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\Accounting\AsetTetapModel;
use App\Models\Accounting\AsetTetapKategoriModel;
use App\Models\Accounting\PenyusutanModel;
use App\Models\CoaModel;
use App\Models\JurnalModel;
use App\Models\JurnalDetailModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Dompdf\Dompdf;
use Dompdf\Options;

class Penyusutan extends BaseController
{
    protected $asetModel;
    protected $kategoriModel;
    protected $penyusutanModel;
    protected $coaModel;
    protected $jurnalModel;
    protected $jurnalDetailModel;
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        if (!$this->db->tableExists('penyusutan_aset')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `penyusutan_aset` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `kode_penyusutan` VARCHAR(50) NOT NULL,
                  `periode_bulan` INT NOT NULL,
                  `periode_tahun` INT NOT NULL,
                  `tanggal_penyusutan` DATE DEFAULT NULL,
                  `aset_id` INT DEFAULT NULL,
                  `nominal` DECIMAL(15,2) DEFAULT 0.00,
                  `jurnal_id` INT DEFAULT NULL,
                  `status` ENUM('Draft','Posted','Dibatalkan') DEFAULT 'Draft',
                  `keterangan` TEXT DEFAULT NULL,
                  `created_by` INT DEFAULT NULL,
                  `created_at` DATETIME DEFAULT NULL,
                  `updated_at` DATETIME DEFAULT NULL,
                  `deleted_at` DATETIME DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }
        if (!$this->db->tableExists('penyusutan')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `penyusutan` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `kode_penyusutan` VARCHAR(50) DEFAULT NULL,
                  `periode` DATE DEFAULT NULL,
                  `periode_bulan` INT DEFAULT NULL,
                  `periode_tahun` INT DEFAULT NULL,
                  `tanggal_penyusutan` DATE DEFAULT NULL,
                  `aset_id` INT DEFAULT NULL,
                  `nilai_buku_awal` DECIMAL(15,2) DEFAULT 0.00,
                  `nilai_penyusutan` DECIMAL(15,2) DEFAULT 0.00,
                  `nominal` DECIMAL(15,2) DEFAULT 0.00,
                  `akumulasi_penyusutan` DECIMAL(15,2) DEFAULT 0.00,
                  `nilai_buku_akhir` DECIMAL(15,2) DEFAULT 0.00,
                  `jurnal_id` INT DEFAULT NULL,
                  `status` ENUM('Draft','Posted','Dibatalkan') DEFAULT 'Draft',
                  `keterangan` TEXT DEFAULT NULL,
                  `created_by` INT DEFAULT NULL,
                  `created_at` DATETIME DEFAULT NULL,
                  `updated_at` DATETIME DEFAULT NULL,
                  `deleted_at` DATETIME DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }

        $this->asetModel = new AsetTetapModel();
        $this->kategoriModel = new AsetTetapKategoriModel();
        $this->penyusutanModel = new PenyusutanModel();
        $this->coaModel = new CoaModel();
        $this->jurnalModel = new JurnalModel();
        $this->jurnalDetailModel = new JurnalDetailModel();
        
        helper(['form', 'url', 'text', 'number']);
        
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
    }

    /**
     * Index / Daftar Penyusutan
     */
    public function index()
    {
        $data['title'] = 'Daftar Penyusutan Aset Tetap';
        
        $filters = [
            'search' => $this->request->getGet('search'),
            'aset_id' => $this->request->getGet('aset_id'),
            'kategori_id' => $this->request->getGet('kategori_id'),
            'tahun' => $this->request->getGet('tahun'),
            'bulan' => $this->request->getGet('bulan'),
            'status' => $this->request->getGet('status')
        ];
        
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 20;
        
        $result = $this->penyusutanModel->getAllWithFilters($filters, $perPage, $page);
        
        $data['penyusutan'] = $result['data'];
        $data['pager'] = $this->penyusutanModel->pager;
        $data['total'] = $result['total'];
        $data['perPage'] = $perPage;
        $data['currentPage'] = $page;
        $data['totalPages'] = $result['total_pages'];
        
        // Options untuk filter
        $data['asetOptions'] = $this->asetModel->getAsetOptions();
        $data['kategoriOptions'] = $this->kategoriModel->getActiveOptions();
        $data['tahunOptions'] = $this->getTahunOptions();
        $data['bulanOptions'] = $this->getBulanOptions();
        $data['statusOptions'] = ['Draft', 'Posted', 'Dibatalkan'];
        
        $data['active'] = 'penyusutan';
        return view('accounting/templates/header', $data)
             . view('accounting/templates/sidebar', $data)
             . view('accounting/aset-tetap/penyusutan/index', $data)
             . view('accounting/templates/footer', $data);
    }

    /**
     * Form generate penyusutan
     */
    public function generate()
    {
        $data['title'] = 'Generate Penyusutan Aset Tetap';
        $data['validation'] = \Config\Services::validation();
        
        $data['periodeOptions'] = [
            'bulanan' => 'Bulanan',
            'triwulan' => 'Triwulan',
            'semester' => 'Semester',
            'tahunan' => 'Tahunan'
        ];
        
        $data['bulanOptions'] = $this->getBulanOptions();
        $data['tahunOptions'] = $this->getTahunOptions();
        
        // Aset yang aktif
        $data['asetAktif'] = $this->asetModel->where('status', 'Aktif')
            ->where('deleted_at IS NULL')
            ->countAllResults();
        
        return view('accounting/aset-tetap/penyusutan/generate', $data);
    }

    /**
     * Proses generate penyusutan
     */
    public function processGenerate()
    {
        $rules = [
            'periode' => 'required|in_list[bulanan,triwulan,semester,tahunan]',
            'bulan' => 'required|numeric|greater_than[0]|less_than_equal_to[12]',
            'tahun' => 'required|numeric|min_length[4]|max_length[4]',
            'force' => 'permit_empty|in_list[0,1]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $periode = $this->request->getPost('periode');
        $bulan = $this->request->getPost('bulan');
        $tahun = $this->request->getPost('tahun');
        $force = $this->request->getPost('force') ?? false;
        
        // Tentukan tanggal periode
        if ($periode === 'bulanan') {
            $tanggalPeriode = date('Y-m-01', strtotime("$tahun-$bulan-01"));
        } elseif ($periode === 'triwulan') {
            $triwulan = ceil($bulan / 3);
            $bulanAwal = ($triwulan - 1) * 3 + 1;
            $tanggalPeriode = date('Y-m-01', strtotime("$tahun-$bulanAwal-01"));
        } elseif ($periode === 'semester') {
            $semester = $bulan <= 6 ? 1 : 2;
            $bulanAwal = $semester == 1 ? 1 : 7;
            $tanggalPeriode = date('Y-m-01', strtotime("$tahun-$bulanAwal-01"));
        } else {
            $tanggalPeriode = date('Y-m-01', strtotime("$tahun-01-01"));
        }
        
        try {
            $this->db->transBegin();
            
            $results = $this->penyusutanModel->generateForPeriode($tahun, $bulan, $force);
            
            $this->db->transCommit();
            
            $successCount = count(array_filter($results, function($item) {
                return $item['status'] === 'success';
            }));
            
            $skippedCount = count(array_filter($results, function($item) {
                return $item['status'] === 'skipped';
            }));
            
            $errorCount = count(array_filter($results, function($item) {
                return $item['status'] === 'error';
            }));
            
            $message = "Generate penyusutan selesai: {$successCount} berhasil, {$skippedCount} dilewati, {$errorCount} gagal";
            
            if ($errorCount > 0) {
                $errors = array_filter($results, function($item) {
                    return $item['status'] === 'error';
                });
                session()->setFlashdata('generate_errors', $errors);
                return redirect()->back()->with('warning', $message);
            }
            
            return redirect()->to('accounting/aset-tetap/penyusutan')
                ->with('success', $message);
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal generate penyusutan: ' . $e->getMessage());
        }
    }

    /**
     * Detail penyusutan
     */
    public function detail($id)
    {
        $data['title'] = 'Detail Penyusutan Aset Tetap';
        
        $penyusutan = $this->penyusutanModel->getWithDetails($id);
        
        if (!$penyusutan) {
            return redirect()->to('accounting/aset-tetap/penyusutan')
                ->with('error', 'Data penyusutan tidak ditemukan');
        }
        
        $penyusutan['nilai_buku_awal_formatted'] = $this->formatRupiah($penyusutan['nilai_buku_awal']);
        $penyusutan['nilai_penyusutan_formatted'] = $this->formatRupiah($penyusutan['nilai_penyusutan']);
        $penyusutan['akumulasi_penyusutan_formatted'] = $this->formatRupiah($penyusutan['akumulasi_penyusutan']);
        $penyusutan['nilai_buku_akhir_formatted'] = $this->formatRupiah($penyusutan['nilai_buku_akhir']);
        
        $data['penyusutan'] = $penyusutan;
        
        return view('accounting/aset-tetap/penyusutan/detail', $data);
    }

    /**
     * Post penyusutan ke jurnal
     */
    public function post($id)
    {
        $isAjax = $this->request->isAJAX();
        
        $penyusutan = $this->penyusutanModel->find($id);
        
        if (!$penyusutan) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data penyusutan tidak ditemukan'
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Data penyusutan tidak ditemukan');
            }
        }
        
        if ($penyusutan['status'] !== 'Draft') {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Hanya penyusutan dengan status Draft yang dapat diposting'
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Hanya penyusutan dengan status Draft yang dapat diposting');
            }
        }
        
        try {
            $this->db->transBegin();
            
            // Ambil data aset
            $aset = $this->asetModel->find($penyusutan['aset_id']);
            if (!$aset) {
                throw new \Exception('Aset tidak ditemukan');
            }
            
            // Ambil COA untuk beban penyusutan dan akumulasi penyusutan
            $coaBeban = $this->coaModel->find($aset['coa_beban_id']);
            $coaAkumulasi = $this->coaModel->find($aset['coa_akumulasi_id']);
            
            if (!$coaBeban || !$coaAkumulasi) {
                throw new \Exception('COA untuk penyusutan tidak lengkap');
            }
            
            // Buat jurnal
            $jurnalData = [
                'tanggal' => $penyusutan['periode'],
                'keterangan' => 'Penyusutan aset ' . $aset['nama_aset'] . ' periode ' . date('F Y', strtotime($penyusutan['periode'])),
                'referensi' => $aset['kode_aset'],
                'tipe_referensi' => 'penyusutan',
                'total_debit' => $penyusutan['nilai_penyusutan'],
                'total_kredit' => $penyusutan['nilai_penyusutan'],
                'status' => 'posted',
                'posted_by' => session()->get('user_id'),
                'posted_at' => date('Y-m-d H:i:s'),
                'created_by' => session()->get('user_id')
            ];
            
            $jurnalId = $this->jurnalModel->insert($jurnalData);
            
            if (!$jurnalId) {
                throw new \Exception('Gagal membuat jurnal');
            }
            
            // Detail jurnal (Debit Beban Penyusutan)
            $detailDebit = [
                'jurnal_id' => $jurnalId,
                'coa_id' => $coaBeban['id'],
                'kode_akun' => $coaBeban['kode_akun'],
                'nama_akun' => $coaBeban['nama_akun'],
                'debit' => $penyusutan['nilai_penyusutan'],
                'kredit' => 0,
                'keterangan' => 'Beban penyusutan ' . $aset['nama_aset']
            ];
            
            // Detail jurnal (Kredit Akumulasi Penyusutan)
            $detailKredit = [
                'jurnal_id' => $jurnalId,
                'coa_id' => $coaAkumulasi['id'],
                'kode_akun' => $coaAkumulasi['kode_akun'],
                'nama_akun' => $coaAkumulasi['nama_akun'],
                'debit' => 0,
                'kredit' => $penyusutan['nilai_penyusutan'],
                'keterangan' => 'Akumulasi penyusutan ' . $aset['nama_aset']
            ];
            
            if (!$this->jurnalDetailModel->insert($detailDebit)) {
                throw new \Exception('Gagal menyimpan detail jurnal debit');
            }
            
            if (!$this->jurnalDetailModel->insert($detailKredit)) {
                throw new \Exception('Gagal menyimpan detail jurnal kredit');
            }
            
            // Update status penyusutan
            $this->penyusutanModel->postPenyusutan($id, $jurnalId);
            
            $this->db->transCommit();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Penyusutan berhasil diposting ke jurnal'
                ]);
            } else {
                return redirect()->to('accounting/aset-tetap/penyusutan/detail/' . $id)
                    ->with('success', 'Penyusutan berhasil diposting ke jurnal');
            }
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            } else {
                return redirect()->back()
                    ->with('error', $e->getMessage());
            }
        }
    }

    /**
     * Post all penyusutan dalam periode
     */
    public function postAll()
    {
        $isAjax = $this->request->isAJAX();
        $periode = $this->request->getPost('periode');
        
        if (!$periode) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Periode harus dipilih'
            ]);
        }
        
        try {
            $this->db->transBegin();
            
            $results = $this->penyusutanModel->postAllForPeriode($periode);
            
            $this->db->transCommit();
            
            $successCount = count(array_filter($results, function($item) {
                return $item['status'] === 'success';
            }));
            
            $errorCount = count(array_filter($results, function($item) {
                return $item['status'] === 'error';
            }));
            
            $message = "Posting penyusutan selesai: {$successCount} berhasil, {$errorCount} gagal";
            
            return $this->response->setJSON([
                'success' => true,
                'message' => $message,
                'results' => $results
            ]);
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Hapus penyusutan
     */
    public function delete($id)
    {
        $isAjax = $this->request->isAJAX();
        
        $penyusutan = $this->penyusutanModel->find($id);
        
        if (!$penyusutan) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data penyusutan tidak ditemukan'
                ]);
            } else {
                return redirect()->to('accounting/aset-tetap/penyusutan')
                    ->with('error', 'Data penyusutan tidak ditemukan');
            }
        }
        
        if ($penyusutan['status'] === 'Posted') {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Penyusutan yang sudah diposting tidak dapat dihapus'
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Penyusutan yang sudah diposting tidak dapat dihapus');
            }
        }
        
        try {
            $this->db->transBegin();
            
            $deleted = $this->penyusutanModel->delete($id);
            
            if (!$deleted) {
                throw new \Exception('Gagal menghapus data');
            }
            
            $this->db->transCommit();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Penyusutan berhasil dihapus',
                    'redirect' => site_url('accounting/aset-tetap/penyusutan')
                ]);
            } else {
                return redirect()->to('accounting/aset-tetap/penyusutan')
                    ->with('success', 'Penyusutan berhasil dihapus');
            }
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus penyusutan: ' . $e->getMessage()
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal menghapus penyusutan: ' . $e->getMessage());
            }
        }
    }

    /**
     * Laporan penyusutan
     */
    public function laporan()
    {
        $data['title'] = 'Laporan Penyusutan Aset Tetap';
        
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan') ?? date('m');
        
        $data['tahun'] = $tahun;
        $data['bulan'] = $bulan;
        $data['tahunOptions'] = $this->getTahunOptions();
        $data['bulanOptions'] = $this->getBulanOptions();
        
        // Data penyusutan per bulan
        $data['ringkasanPerBulan'] = $this->penyusutanModel->getRingkasanPerBulan($tahun);
        
        // Data penyusutan per aset
        $data['ringkasanPerAset'] = $this->penyusutanModel->getRingkasanPerAset($tahun);
        
        // Data penyusutan untuk periode yang dipilih
        $periode = date('Y-m-01', strtotime("$tahun-$bulan-01"));
        $data['penyusutanBulanIni'] = $this->penyusutanModel->getByPeriodeWithSummary($periode);
        
        // Total akumulasi
        $data['totalAkumulasi'] = $this->penyusutanModel->getStats($tahun);
        
        return view('accounting/aset-tetap/penyusutan/laporan', $data);
    }

    /**
     * Proyeksi penyusutan
     */
    public function proyeksi()
    {
        $data['title'] = 'Proyeksi Penyusutan Aset Tetap';
        
        $asetId = $this->request->getGet('aset_id');
        $bulanKeDepan = $this->request->getGet('bulan') ?? 12;
        
        $data['asetId'] = $asetId;
        $data['bulanKeDepan'] = $bulanKeDepan;
        $data['asetOptions'] = $this->asetModel->getAsetOptions();
        
        if ($asetId) {
            $aset = $this->asetModel->find($asetId);
            if ($aset) {
                $data['aset'] = $aset;
                $data['proyeksi'] = $this->penyusutanModel->getProyeksiPenyusutan($asetId, $bulanKeDepan);
                $data['totalPenyusutan'] = array_sum(array_column($data['proyeksi'], 'nilai_penyusutan'));
            }
        }
        
        return view('accounting/aset-tetap/penyusutan/proyeksi', $data);
    }

    /**
     * Filter data
     */
    public function filter()
    {
        $filters = [
            'aset_id' => $this->request->getGet('aset_id'),
            'kategori_id' => $this->request->getGet('kategori_id'),
            'tahun' => $this->request->getGet('tahun'),
            'bulan' => $this->request->getGet('bulan'),
            'status' => $this->request->getGet('status')
        ];
        
        session()->set('filter_penyusutan', $filters);
        
        return redirect()->to('accounting/aset-tetap/penyusutan');
    }

    /**
     * Search data
     */
    public function search()
    {
        $search = $this->request->getGet('q');
        
        $filters = session()->get('filter_penyusutan') ?? [];
        $filters['search'] = $search;
        
        session()->set('filter_penyusutan', $filters);
        
        return redirect()->to('accounting/aset-tetap/penyusutan');
    }

    /**
     * Reset filter
     */
    public function resetFilter()
    {
        session()->remove('filter_penyusutan');
        
        return redirect()->to('accounting/aset-tetap/penyusutan');
    }

    /**
     * Export data
     */
    public function export()
    {
        $type = $this->request->getGet('type') ?? 'excel';
        $filters = [
            'tahun' => $this->request->getGet('tahun'),
            'aset_id' => $this->request->getGet('aset_id')
        ];
        
        $data = $this->penyusutanModel->getExportData($filters);
        
        if ($type === 'excel') {
            return $this->exportExcel($data, $filters);
        } else {
            return $this->exportPdf($data, $filters);
        }
    }

    /**
     * Export ke Excel
     */
    private function exportExcel($data, $filters)
    {
        try {
            $spreadsheet = new Spreadsheet();
            
            $spreadsheet->getProperties()
                ->setCreator("CDW Engineering")
                ->setLastModifiedBy("CDW Engineering")
                ->setTitle("Laporan Penyusutan Aset Tetap")
                ->setSubject("Laporan Penyusutan Aset Tetap")
                ->setDescription("Laporan Penyusutan Aset Tetap " . date('d-m-Y'));
            
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Laporan Penyusutan');
            
            // Header laporan
            $sheet->mergeCells('A1:H1');
            $sheet->setCellValue('A1', 'LAPORAN PENYUSUTAN ASET TETAP');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->mergeCells('A2:H2');
            $sheet->setCellValue('A2', 'PT. CIPTA DUTA WACANA');
            $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->mergeCells('A3:H3');
            $sheet->setCellValue('A3', 'Dicetak: ' . date('d/m/Y H:i:s'));
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Baris kosong
            $startRow = 5;
            
            // Header tabel
            $headers = [
                'A' => 'No',
                'B' => 'Kode Aset',
                'C' => 'Nama Aset',
                'D' => 'Periode',
                'E' => 'Nilai Buku Awal',
                'F' => 'Nilai Penyusutan',
                'G' => 'Akumulasi',
                'H' => 'Nilai Buku Akhir'
            ];
            
            foreach ($headers as $col => $header) {
                $sheet->setCellValue($col . $startRow, $header);
            }
            
            // Style header
            $headerRange = 'A' . $startRow . ':H' . $startRow;
            $sheet->getStyle($headerRange)->getFont()->setBold(true);
            $sheet->getStyle($headerRange)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF4F81BD');
            $sheet->getStyle($headerRange)->getFont()->getColor()->setARGB('FFFFFFFF');
            $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($headerRange)->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
            
            // Isi data
            $row = $startRow + 1;
            $no = 1;
            foreach ($data as $item) {
                $sheet->setCellValue('A' . $row, $no);
                $sheet->setCellValue('B' . $row, $item['Kode Aset']);
                $sheet->setCellValue('C' . $row, $item['Nama Aset']);
                $sheet->setCellValue('D' . $row, $item['Periode']);
                $sheet->setCellValue('E' . $row, $item['Nilai Buku Awal']);
                $sheet->setCellValue('F' . $row, $item['Nilai Penyusutan']);
                $sheet->setCellValue('G' . $row, $item['Akumulasi Penyusutan']);
                $sheet->setCellValue('H' . $row, $item['Nilai Buku Akhir']);
                
                // Format angka
                $sheet->getStyle('E' . $row . ':H' . $row)->getNumberFormat()
                    ->setFormatCode('"Rp" #,##0.00');
                
                $row++;
                $no++;
            }
            
            // Auto-size columns
            foreach (range('A', 'H') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Border untuk seluruh data
            $lastRow = $row - 1;
            if ($lastRow >= $startRow) {
                $dataRange = 'A' . $startRow . ':H' . $lastRow;
                $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            }
            
            // Output file
            $filename = 'Laporan_Penyusutan_Aset_Tetap_' . date('Ymd_His') . '.xlsx';
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit();
            
        } catch (\Exception $e) {
            log_message('error', 'Export Excel error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export Excel: ' . $e->getMessage());
        }
    }

    /**
     * Export ke PDF
     */
    private function exportPdf($data, $filters)
    {
        try {
            $html = $this->generatePdfHtml($data, $filters);
            
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);
            $options->set('defaultFont', 'Arial');
            
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            
            $filename = 'Laporan_Penyusutan_Aset_Tetap_' . date('Ymd_His') . '.pdf';
            $dompdf->stream($filename, ['Attachment' => true]);
            exit();
            
        } catch (\Exception $e) {
            log_message('error', 'Export PDF error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export PDF: ' . $e->getMessage());
        }
    }

    /**
     * Generate HTML untuk PDF
     */
    private function generatePdfHtml($data, $filters)
    {
        $filterText = '';
        if (!empty($filters['tahun'])) {
            $filterText .= 'Tahun: ' . $filters['tahun'];
        }
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Laporan Penyusutan Aset Tetap</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 9px;
                    margin: 15px;
                }
                h1 {
                    text-align: center;
                    font-size: 16px;
                    margin-bottom: 5px;
                }
                h2 {
                    text-align: center;
                    font-size: 12px;
                    color: #666;
                    margin-top: 0;
                    margin-bottom: 10px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 15px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 15px;
                }
                th {
                    background-color: #4F81BD;
                    color: white;
                    padding: 6px;
                    text-align: center;
                    font-weight: bold;
                    border: 1px solid #000;
                }
                td {
                    padding: 5px;
                    border: 1px solid #000;
                    vertical-align: top;
                }
                .text-end {
                    text-align: right;
                }
                .text-center {
                    text-align: center;
                }
                .footer {
                    margin-top: 15px;
                    padding-top: 8px;
                    border-top: 1px solid #000;
                    font-size: 8px;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>LAPORAN PENYUSUTAN ASET TETAP</h1>
                <h2>PT. CIPTA DUTA WACANA</h2>
                <div>Dicetak: ' . date('d/m/Y H:i:s') . '</div>
                ' . (!empty($filterText) ? '<div>' . $filterText . '</div>' : '') . '
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th width="3%">No</th>
                        <th width="8%">Kode Aset</th>
                        <th width="15%">Nama Aset</th>
                        <th width="10%">Periode</th>
                        <th width="12%">Nilai Buku Awal</th>
                        <th width="12%">Nilai Penyusutan</th>
                        <th width="12%">Akumulasi</th>
                        <th width="12%">Nilai Buku Akhir</th>
                    </tr>
                </thead>
                <tbody>';
        
        if (empty($data)) {
            $html .= '<tr><td colspan="8" class="text-center">Tidak ada data penyusutan</td></tr>';
        } else {
            $no = 1;
            foreach ($data as $item) {
                $html .= '
                    <tr>
                        <td class="text-center">' . $no . '</td>
                        <td class="text-center">' . $item['Kode Aset'] . '</td>
                        <td>' . $item['Nama Aset'] . '</td>
                        <td class="text-center">' . $item['Periode'] . '</td>
                        <td class="text-end">Rp ' . number_format($item['Nilai Buku Awal'], 0) . '</td>
                        <td class="text-end">Rp ' . number_format($item['Nilai Penyusutan'], 0) . '</td>
                        <td class="text-end">Rp ' . number_format($item['Akumulasi Penyusutan'], 0) . '</td>
                        <td class="text-end">Rp ' . number_format($item['Nilai Buku Akhir'], 0) . '</td>
                    <tr>';
                $no++;
            }
        }
        
        $html .= '
                </tbody>
            </table>
            
            <div class="footer">
                <table style="width: 100%; border: none;">
                    <tr>
                        <td style="border: none; text-align: left;">
                            <strong>Total Data:</strong> ' . count($data) . ' transaksi
                        </td>
                        <td style="border: none; text-align: right;">
                            Dicetak oleh: ' . session()->get('name') . '
                        </td>
                    </tr>
                </table>
            </div>
        </body>
        </html>';
        
        return $html;
    }

    /**
     * AJAX: Get aset options
     */
    public function ajaxGetAsetOptions()
    {
        $kategoriId = $this->request->getGet('kategori_id');
        
        $builder = $this->asetModel->select('id, kode_aset, nama_aset')
            ->where('status', 'Aktif');
        
        if ($kategoriId) {
            $builder->where('kategori_id', $kategoriId);
        }
        
        $aset = $builder->orderBy('kode_aset', 'ASC')->findAll();
        
        $options = [];
        foreach ($aset as $item) {
            $options[] = [
                'id' => $item['id'],
                'text' => $item['kode_aset'] . ' - ' . $item['nama_aset']
            ];
        }
        
        return $this->response->setJSON($options);
    }

    /**
     * AJAX: Get ringkasan per aset
     */
    public function ajaxGetRingkasanPerAset()
    {
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        
        $ringkasan = $this->penyusutanModel->getRingkasanPerAset($tahun);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $ringkasan,
            'tahun' => $tahun
        ]);
    }

    /**
     * AJAX: Get total akumulasi
     */
    public function ajaxGetTotalAkumulasi()
    {
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        
        $total = $this->penyusutanModel->getStats($tahun);
        
        return $this->response->setJSON([
            'success' => true,
            'total_penyusutan' => $total['total_penyusutan'],
            'total_akumulasi' => $total['total_akumulasi'],
            'total_aset' => $total['total_aset']
        ]);
    }

    /**
     * Get tahun options
     */
    private function getTahunOptions()
    {
        $tahunSekarang = date('Y');
        $options = [];
        
        for ($i = $tahunSekarang - 5; $i <= $tahunSekarang + 5; $i++) {
            $options[] = $i;
        }
        
        return $options;
    }

    /**
     * Get bulan options
     */
    private function getBulanOptions()
    {
        return [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
    }

    /**
     * Fungsi untuk format currency ke Rupiah
     */
    private function formatRupiah($angka)
    {
        if (!$angka || $angka == 0) return '0';
        return number_format($angka, 0, ',', '.');
    }
}