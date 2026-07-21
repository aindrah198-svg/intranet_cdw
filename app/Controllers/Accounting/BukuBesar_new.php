<?php

namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\BukuBesarModel;  // ← Langsung gunakan ini
use App\Models\CoaModel;
use App\Models\JurnalModel;
use App\Models\JurnalDetailModel;

class BukuBesar extends BaseController
{
    protected $bukuBesarModel;
    protected $coaModel;
    protected $jurnalModel;
    protected $jurnalDetailModel;
    protected $db;
    protected $validation;

    public function __construct()
    {
        $this->bukuBesarModel = new BukuBesarModel();
        $this->coaModel = new CoaModel();
        $this->jurnalModel = new JurnalModel();
        $this->jurnalDetailModel = new JurnalDetailModel();
        $this->db = \Config\Database::connect();
        $this->validation = \Config\Services::validation();
        
        helper(['form', 'url', 'number']);
        
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
    }

   public function index()
{
    $data['title'] = 'Buku Besar';
    $data['subtitle'] = 'Laporan Buku Besar General Ledger';
    
    // Get current user data
    $userModel = model('UserModel');
    $karyawanModel = model('KaryawanModel');
    
    $userId = session()->get('user_id');
    $user = $userModel->find($userId);
    $karyawan = $user && $user['karyawan_id'] ? $karyawanModel->find($user['karyawan_id']) : [];
    
    // Get filters
    $filters = [
        'coa_id' => $this->request->getGet('coa_id'),
        'periode' => $this->request->getGet('periode'),
        'tanggal_mulai' => $this->request->getGet('tanggal_mulai'),
        'tanggal_selesai' => $this->request->getGet('tanggal_selesai'),
        'tipe_jurnal' => $this->request->getGet('tipe_jurnal')
    ];
    
    // Get list of active COA for filter
    $data['coa_list'] = $this->coaModel->where('is_active', 1)
        ->orderBy('kode_akun', 'ASC')
        ->findAll();
    
    // Get pending counts for batch processing
    $pendingJurnals = $this->bukuBesarModel->getPendingJurnals();
    $jurnalIds = [];
    foreach ($pendingJurnals as $detail) {
        $jurnalIds[$detail['jurnal_id']] = true;
    }
    
    $data['pending_jurnals_count'] = count($jurnalIds);  // Jumlah JURNAL
    $data['pending_details_count'] = count($pendingJurnals);  // Jumlah DETAIL
    $data['pending_count'] = count($pendingJurnals);  // Untuk backward compatibility
    
    // Get total stats
    $totalStats = $this->bukuBesarModel->getStatistics();
    $data['total_jurnals_count'] = $totalStats['total_transaksi_unik'] ?? 0;
    $data['total_debit_all'] = $totalStats['total_debit'] ?? 0;
    $data['total_kredit_all'] = $totalStats['total_kredit'] ?? 0;
    
    // Get batch history
    $data['batch_history'] = $this->bukuBesarModel->getBatchHistory(5);
    
    // If COA selected, show buku besar detail
    if (!empty($filters['coa_id'])) {
        $coa = $this->coaModel->find($filters['coa_id']);
        if ($coa) {
            $startDate = $filters['tanggal_mulai'] ?? date('Y-m-01');
            $endDate = $filters['tanggal_selesai'] ?? date('Y-m-t');
            
            $bukuBesar = $this->bukuBesarModel->getBukuBesarByCoa(
                $filters['coa_id'],
                $startDate,
                $endDate
            );
            
            $data['selected_coa'] = $coa;
            $data['buku_besar'] = $bukuBesar;
            $data['start_date'] = $startDate;
            $data['end_date'] = $endDate;
            $data['filters'] = $filters;
        }
    }
    
    $data['user'] = $user ?? ['name' => 'Accounting Staff', 'role' => 'accounting'];
    $data['karyawan'] = $karyawan ?? [];
    $data['active'] = 'accounting';
    
    return view('accounting/pembukuan/buku-besar/index', $data);
}

    /**
     * Detail Buku Besar per akun
     */
    public function detail($coaId)
    {
        $coa = $this->coaModel->find($coaId);
        
        if (!$coa) {
            return redirect()->to('accounting/pembukuan/buku-besar')
                ->with('error', 'Akun tidak ditemukan');
        }
        
        $periode = $this->request->getGet('periode') ?? date('Y-m');
        $startDate = $periode . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));
        
        $bukuBesar = $this->bukuBesarModel->getBukuBesarByCoa($coaId, $startDate, $endDate);
        
        // Get current user data
        $userModel = model('UserModel');
        $karyawanModel = model('KaryawanModel');
        
        $userId = session()->get('user_id');
        $user = $userModel->find($userId);
        $karyawan = $user && $user['karyawan_id'] ? $karyawanModel->find($user['karyawan_id']) : [];
        
        $data = [
            'title' => 'Detail Buku Besar: ' . $coa['kode_akun'] . ' - ' . $coa['nama_akun'],
            'subtitle' => 'Detail transaksi per akun',
            'coa' => $coa,
            'buku_besar' => $bukuBesar,
            'periode' => $periode,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'user' => $user ?? ['name' => 'Accounting Staff', 'role' => 'accounting'],
            'karyawan' => $karyawan ?? [],
            'active' => 'accounting'
        ];
        
        return view('accounting/pembukuan/buku-besar/detail', $data);
    }
public function postAllJurnalsForm()
{
    // Get current user data
    $userModel = model('UserModel');
    $karyawanModel = model('KaryawanModel');
    
    $userId = session()->get('user_id');
    $user = $userModel->find($userId);
    $karyawan = $user && $user['karyawan_id'] ? $karyawanModel->find($user['karyawan_id']) : [];
    
    // WAJIB: Ambil bulan dan tahun dari parameter
    $bulan = $this->request->getGet('bulan') ?? $this->request->getPost('bulan');
    $tahun = $this->request->getGet('tahun') ?? $this->request->getPost('tahun');
    
    // Jika tidak ada bulan/tahun, redirect ke halaman pilih bulan
    if (empty($bulan) || empty($tahun)) {
        return redirect()->to(site_url('accounting/pembukuan/buku-besar'))
            ->with('error', 'Silakan pilih bulan dan tahun terlebih dahulu sebelum posting!');
    }
    
    $periode = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT);
    
    // 🔥 CEK APAKAH ADA JURNAL UNTUK PERIODE TERSEBUT
    $pendingJurnals = $this->bukuBesarModel->getPendingJurnals($periode, $periode);
    
    if (empty($pendingJurnals)) {
        // Redirect dengan pesan error bahwa tidak ada data
        return redirect()->to(site_url('accounting/pembukuan/buku-besar'))
            ->with('error', 'Tidak ada jurnal pending untuk periode ' . $this->getNamaBulan($bulan) . ' ' . $tahun . '. Pastikan jurnal untuk periode tersebut sudah ada atau pilih periode lain.');
    }
    
    // Hitung jumlah jurnal unik
    $jurnalIds = [];
    foreach ($pendingJurnals as $detail) {
        $jurnalIds[$detail['jurnal_id']] = true;
    }
    
    // Group by nomor_jurnal for summary
    $summary = [];
    foreach ($pendingJurnals as $jurnal) {
        $key = $jurnal['nomor_jurnal'];
        if (!isset($summary[$key])) {
            $summary[$key] = [
                'nomor_jurnal' => $jurnal['nomor_jurnal'],
                'tanggal' => $jurnal['tanggal'],
                'keterangan' => $jurnal['jurnal_keterangan'],
                'tipe_jurnal' => $jurnal['tipe_jurnal'],
                'total_debit' => 0,
                'total_kredit' => 0,
                'details' => []
            ];
        }
        $summary[$key]['total_debit'] += $jurnal['debit'];
        $summary[$key]['total_kredit'] += $jurnal['kredit'];
        $summary[$key]['details'][] = $jurnal;
    }
    
    $data = [
        'title' => 'Konfirmasi Posting ke Buku Besar',
        'subtitle' => 'Review dan konfirmasi jurnal yang akan diposting',
        'pending_count' => count($pendingJurnals),
        'jurnal_count' => count($jurnalIds),
        'pending_summary' => $summary,
        'bulan' => $bulan,
        'tahun' => $tahun,
        'nama_bulan' => $this->getNamaBulan($bulan),
        'periode' => $periode,
        'user' => $user ?? ['name' => 'Accounting Staff', 'role' => 'accounting'],
        'karyawan' => $karyawan ?? [],
        'active' => 'accounting'
    ];
    
    return view('accounting/pembukuan/buku-besar/post-confirm', $data);
}

/**
 * Helper function untuk mendapatkan nama bulan
 */
private function getNamaBulan($bulan)
{
    $namaBulan = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];
    return $namaBulan[$bulan] ?? $bulan;
}

public function postAllJurnals()
{
    // Pastikan ini AJAX request
    if (!$this->request->isAJAX()) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Hanya request AJAX yang diperbolehkan'
        ]);
    }
    
    // Validasi CSRF
    $csrfToken = $this->request->getPost('csrf_token');
    $csrfHash = csrf_hash();
    
    if ($csrfToken !== $csrfHash) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'CSRF token tidak valid. Silakan refresh halaman.'
        ]);
    }
    
    // Ambil bulan dan tahun dari POST
    $bulan = $this->request->getPost('bulan');
    $tahun = $this->request->getPost('tahun');
    
    // Validasi wajib bulan dan tahun
    if (empty($bulan) || empty($tahun)) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Bulan dan tahun wajib dipilih!',
            'total_jurnal' => 0,
            'total_baris' => 0,
            'success_count' => 0,
            'failed_count' => 0,
            'failed_items' => []
        ]);
    }
    
    // Format periode
    $periode = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT);
    
    // 🔥 CEK APAKAH ADA JURNAL SEBELUM PROSES
    $pendingJurnals = $this->bukuBesarModel->getPendingJurnals($periode, $periode);
    
    if (empty($pendingJurnals)) {
        $namaBulan = $this->getNamaBulan($bulan);
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Tidak ada jurnal pending untuk periode ' . $namaBulan . ' ' . $tahun . '.',
            'total_jurnal' => 0,
            'total_baris' => 0,
            'success_count' => 0,
            'failed_count' => 0,
            'failed_items' => [],
            'sisa_jurnal' => 0
        ]);
    }
    
    try {
        $result = $this->bukuBesarModel->processBatch($periode, $periode);
        
        // Hitung sisa jurnal di bulan lain
        $allPendingJurnals = $this->bukuBesarModel->getPendingJurnals();
        $allJurnalIds = [];
        foreach ($allPendingJurnals as $detail) {
            $allJurnalIds[$detail['jurnal_id']] = true;
        }
        
        $sisaJurnal = count($allJurnalIds) - count(array_unique(array_column($pendingJurnals, 'jurnal_id')));
        
        $result['total_jurnal'] = count(array_unique(array_column($pendingJurnals, 'jurnal_id')));
        $result['total_baris'] = count($pendingJurnals);
        $result['sisa_jurnal'] = $sisaJurnal > 0 ? $sisaJurnal : 0;
        $result['bulan'] = $bulan;
        $result['tahun'] = $tahun;
        
        return $this->response->setJSON($result);
        
    } catch (\Exception $e) {
        log_message('error', 'Post All Jurnals Error: ' . $e->getMessage());
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage(),
            'total_jurnal' => 0,
            'total_baris' => 0,
            'success_count' => 0,
            'failed_count' => 0,
            'failed_items' => [],
            'sisa_jurnal' => 0
        ]);
    }
}

    /**
     * Rollback batch processing (AJAX)
     */
    public function rollbackBatch()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Hanya request AJAX yang diperbolehkan'
            ]);
        }
        
        $batchId = $this->request->getPost('batch_id');
        
        if (empty($batchId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Batch ID tidak boleh kosong'
            ]);
        }
        
        try {
            $result = $this->bukuBesarModel->rollbackBatch($batchId);
            return $this->response->setJSON($result);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Recalculate all saldo (maintenance)
     */
    public function recalculateSaldo()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Hanya request AJAX yang diperbolehkan'
            ]);
        }
        
        try {
            $updated = $this->bukuBesarModel->recalculateAllSaldo();
            
            return $this->response->setJSON([
                'success' => true,
                'message' => "Berhasil menghitung ulang saldo untuk {$updated} entri",
                'updated' => $updated
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generate monthly saldo summary (maintenance)
     */
    public function generateMonthlySaldo()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Hanya request AJAX yang diperbolehkan'
            ]);
        }
        
        $tahun = $this->request->getPost('tahun') ?? date('Y');
        
        try {
            // Get all active COA
            $allCoa = $this->coaModel->where('is_active', 1)->findAll();
            
            $result = [];
            foreach ($allCoa as $coa) {
                $saldoPerBulan = [];
                for ($bulan = 1; $bulan <= 12; $bulan++) {
                    $periode = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT);
                    $lastDay = date('Y-m-t', strtotime($periode . '-01'));
                    $saldo = $this->bukuBesarModel->getSaldoByDate($coa['id'], $lastDay);
                    $saldoPerBulan[$bulan] = $saldo;
                }
                $result[] = [
                    'coa' => $coa,
                    'saldo_per_bulan' => $saldoPerBulan
                ];
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $result,
                'tahun' => $tahun
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Laporan Neraca Saldo (Trial Balance)
     */
    public function neracaSaldo()
    {
        $data['title'] = 'Neraca Saldo';
        $data['subtitle'] = 'Trial Balance - Laporan Saldo Akun';
        
        // Get current user data
        $userModel = model('UserModel');
        $karyawanModel = model('KaryawanModel');
        
        $userId = session()->get('user_id');
        $user = $userModel->find($userId);
        $karyawan = $user && $user['karyawan_id'] ? $karyawanModel->find($user['karyawan_id']) : [];
        
        $periode = $this->request->getGet('periode') ?? date('Y-m');
        
        $neracaSaldo = $this->bukuBesarModel->getNeracaSaldo($periode);
        
        $data['neraca_saldo'] = $neracaSaldo;
        $data['periode'] = $periode;
        $data['user'] = $user ?? ['name' => 'Accounting Staff', 'role' => 'accounting'];
        $data['karyawan'] = $karyawan ?? [];
        $data['active'] = 'accounting';
        
        return view('accounting/pembukuan/buku-besar/neraca-saldo', $data);
    }

    /**
     * Export Buku Besar ke Excel
     */
    public function export()
    {
        $type = $this->request->getGet('type') ?? 'excel';
        $coaId = $this->request->getGet('coa_id');
        $periode = $this->request->getGet('periode') ?? date('Y-m');
        $startDate = $this->request->getGet('tanggal_mulai') ?? $periode . '-01';
        $endDate = $this->request->getGet('tanggal_selesai') ?? date('Y-m-t', strtotime($startDate));
        
        if (empty($coaId)) {
            return redirect()->back()->with('error', 'Pilih akun terlebih dahulu');
        }
        
        $coa = $this->coaModel->find($coaId);
        if (!$coa) {
            return redirect()->back()->with('error', 'Akun tidak ditemukan');
        }
        
        $bukuBesar = $this->bukuBesarModel->getBukuBesarByCoa($coaId, $startDate, $endDate);
        
        if ($type === 'excel') {
            return $this->exportExcel($coa, $bukuBesar, $startDate, $endDate);
        } else {
            return $this->exportPdf($coa, $bukuBesar, $startDate, $endDate);
        }
    }

    /**
 * Export Neraca Saldo ke Excel atau PDF
 */
public function exportNeracaSaldo()
{
    $type = $this->request->getGet('type') ?? 'excel';
    $periode = $this->request->getGet('periode') ?? date('Y-m');
    $tanggal = $this->request->getGet('tanggal');
    $tipeAkun = $this->request->getGet('tipe_akun');
    
    // Tentukan tanggal yang digunakan
    if (!empty($tanggal)) {
        $targetDate = $tanggal;
    } else {
        $targetDate = date('Y-m-t', strtotime($periode . '-01'));
    }
    
    // Get neraca saldo data
    $neracaSaldo = $this->bukuBesarModel->getNeracaSaldo($periode);
    
    // Filter by tipe akun if needed
    if (!empty($tipeAkun)) {
        $neracaSaldo['data'] = array_filter($neracaSaldo['data'], function($item) use ($tipeAkun) {
            return $item['tipe_akun'] == $tipeAkun;
        });
        // Recalculate totals
        $neracaSaldo['total_debit'] = array_sum(array_column($neracaSaldo['data'], 'debit'));
        $neracaSaldo['total_kredit'] = array_sum(array_column($neracaSaldo['data'], 'kredit'));
        $neracaSaldo['is_balance'] = abs($neracaSaldo['total_debit'] - $neracaSaldo['total_kredit']) <= 0.01;
    }
    
    $neracaSaldo['total_akun'] = count($neracaSaldo['data']);
    $neracaSaldo['periode'] = $periode;
    $neracaSaldo['tanggal'] = $targetDate;
    $neracaSaldo['tipe_akun_filter'] = $tipeAkun;
    
    if ($type === 'excel') {
        return $this->exportNeracaSaldoExcel($neracaSaldo);
    } else {
        return $this->exportNeracaSaldoPdf($neracaSaldo);
    }
}

/**
 * Export Neraca Saldo ke Excel
 */
private function exportNeracaSaldoExcel($neracaSaldo)
{
    try {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Title
        $sheet->setCellValue('A1', 'NERACA SALDO');
        $sheet->setCellValue('A2', 'PT. CIPTA DUTA WACANA');
        $sheet->setCellValue('A3', 'Periode: ' . date('d/m/Y', strtotime($neracaSaldo['tanggal'])));
        
        // Column headers
        $sheet->setCellValue('A5', 'No');
        $sheet->setCellValue('B5', 'Kode Akun');
        $sheet->setCellValue('C5', 'Nama Akun');
        $sheet->setCellValue('D5', 'Tipe Akun');
        $sheet->setCellValue('E5', 'Saldo Normal');
        $sheet->setCellValue('F5', 'Debit');
        $sheet->setCellValue('G5', 'Kredit');
        
        // Style header
        $sheet->getStyle('A5:G5')->getFont()->setBold(true);
        $sheet->getStyle('A5:G5')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF4F81BD');
        
        // Data
        $row = 6;
        $no = 1;
        foreach ($neracaSaldo['data'] as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['kode_akun']);
            $sheet->setCellValue('C' . $row, $item['nama_akun']);
            $sheet->setCellValue('D' . $row, $item['tipe_akun']);
            $sheet->setCellValue('E' . $row, $item['saldo_normal']);
            $sheet->setCellValue('F' . $row, $item['debit']);
            $sheet->setCellValue('G' . $row, $item['kredit']);
            $row++;
        }
        
        // Total row
        $sheet->setCellValue('E' . $row, 'TOTAL');
        $sheet->setCellValue('F' . $row, $neracaSaldo['total_debit']);
        $sheet->setCellValue('G' . $row, $neracaSaldo['total_kredit']);
        $sheet->getStyle('E' . $row . ':G' . $row)->getFont()->setBold(true);
        
        // Format currency
        $sheet->getStyle('F6:G' . $row)->getNumberFormat()
            ->setFormatCode('"Rp" #,##0.00');
        
        // Auto size columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $filename = 'Neraca_Saldo_' . date('Ymd_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
        
    } catch (\Exception $e) {
        log_message('error', 'Export Neraca Saldo Excel error: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Gagal export Excel: ' . $e->getMessage());
    }
}

/**
 * Export Neraca Saldo ke PDF
 */
private function exportNeracaSaldoPdf($neracaSaldo)
{
    $html = $this->generateNeracaSaldoPdfHtml($neracaSaldo);
    
    $options = new \Dompdf\Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', true);
    $options->set('defaultFont', 'Arial');
    
    $dompdf = new \Dompdf\Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    
    $filename = 'Neraca_Saldo_' . date('Ymd_His') . '.pdf';
    $dompdf->stream($filename, ['Attachment' => true]);
    exit();
}

/**
 * Generate HTML untuk PDF Neraca Saldo
 */
private function generateNeracaSaldoPdfHtml($neracaSaldo)
{
    // Group by tipe akun
    $groupedData = [];
    foreach ($neracaSaldo['data'] as $item) {
        $tipe = $item['tipe_akun'];
        if (!isset($groupedData[$tipe])) {
            $groupedData[$tipe] = [];
        }
        $groupedData[$tipe][] = $item;
    }
    
    $tipeOrder = ['Aset', 'Kewajiban', 'Ekuitas', 'Pendapatan', 'Beban'];
    $tipeLabels = [
        'Aset' => 'ASET',
        'Kewajiban' => 'KEWAJIBAN',
        'Ekuitas' => 'EKUITAS',
        'Pendapatan' => 'PENDAPATAN',
        'Beban' => 'BEBAN'
    ];
    
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Neraca Saldo</title>
        <style>
            body { font-family: Arial, sans-serif; font-size: 10px; margin: 20px; }
            h1 { text-align: center; font-size: 18px; margin-bottom: 5px; }
            h2 { text-align: center; font-size: 14px; color: #666; margin-top: 0; margin-bottom: 10px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th { background-color: #4F81BD; color: white; padding: 8px; border: 1px solid #000; }
            td { padding: 6px; border: 1px solid #000; vertical-align: top; }
            .text-end { text-align: right; }
            .text-center { text-align: center; }
            .bold { font-weight: bold; }
            .bg-secondary { background-color: #f0f0f0; }
            .footer { margin-top: 30px; text-align: right; font-size: 9px; }
        </style>
    </head>
    <body>
        <h1>NERACA SALDO</h1>
        <h2>PT. CIPTA DUTA WACANA</h2>
        <div class="text-center">Periode: ' . date('d/m/Y', strtotime($neracaSaldo['tanggal'])) . '</div>
        
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="12%">Kode Akun</th>
                    <th width="30%">Nama Akun</th>
                    <th width="12%">Tipe Akun</th>
                    <th width="10%">Normal</th>
                    <th width="15%" class="text-end">Debit</th>
                    <th width="15%" class="text-end">Kredit</th>
                </tr>
            </thead>
            <tbody>';
    
    $no = 1;
    $totalDebit = 0;
    $totalKredit = 0;
    
    foreach ($tipeOrder as $tipe):
        if (isset($groupedData[$tipe]) && !empty($groupedData[$tipe])):
            $html .= '<tr class="bg-secondary">';
            $html .= '<td colspan="7"><strong>' . $tipeLabels[$tipe] . '</strong></td>';
            $html .= '</tr>';
            
            foreach ($groupedData[$tipe] as $item):
                $html .= '<tr>';
                $html .= '<td class="text-center">' . $no++ . '</td>';
                $html .= '<td>' . $item['kode_akun'] . '</td>';
                $html .= '<td>' . $item['nama_akun'] . '</td>';
                $html .= '<td>' . $item['tipe_akun'] . '</td>';
                $html .= '<td class="text-center">' . $item['saldo_normal'] . '</td>';
                $html .= '<td class="text-end">' . ($item['debit'] > 0 ? 'Rp ' . number_format($item['debit'], 2) : '-') . '</td>';
                $html .= '<td class="text-end">' . ($item['kredit'] > 0 ? 'Rp ' . number_format($item['kredit'], 2) : '-') . '</td>';
                $html .= '</tr>';
                $totalDebit += $item['debit'];
                $totalKredit += $item['kredit'];
            endforeach;
            
            $html .= '<tr class="bg-secondary">';
            $html .= '<td colspan="5" class="text-end"><strong>Subtotal ' . $tipeLabels[$tipe] . '</strong></td>';
            $html .= '<td class="text-end"><strong>Rp ' . number_format(array_sum(array_column($groupedData[$tipe], 'debit')), 2) . '</strong></td>';
            $html .= '<td class="text-end"><strong>Rp ' . number_format(array_sum(array_column($groupedData[$tipe], 'kredit')), 2) . '</strong></td>';
            $html .= '</tr>';
        endif;
    endforeach;
    
    $html .= '
            </tbody>
            <tfoot>
                <tr style="background-color: #333; color: white;">
                    <td colspan="5" class="text-end"><strong>TOTAL NERACA SALDO</strong></td>
                    <td class="text-end"><strong>Rp ' . number_format($totalDebit, 2) . '</strong></td>
                    <td class="text-end"><strong>Rp ' . number_format($totalKredit, 2) . '</strong></td>
                </tr>';
    
    if (abs($totalDebit - $totalKredit) > 0.01):
        $selisih = $totalDebit - $totalKredit;
        $html .= '<tr style="background-color: #ffc107;">
                    <td colspan="5" class="text-end"><strong>SELISIH</strong></td>
                    <td class="text-end"><strong>' . ($selisih > 0 ? 'Rp ' . number_format($selisih, 2) : '-') . '</strong></td>
                    <td class="text-end"><strong>' . ($selisih < 0 ? 'Rp ' . number_format(abs($selisih), 2) : '-') . '</strong></td>
                </tr>';
    endif;
    
    $html .= '
            </tfoot>
        </table>
        
        <div class="footer">
            Dicetak pada: ' . date('d/m/Y H:i:s') . '<br>
            Oleh: ' . (session()->get('name') ?? 'System') . '
        </div>
    </body>
    </html>';
    
    return $html;
}

    /**
     * Export to Excel
     */
    private function exportExcel($coa, $bukuBesar, $startDate, $endDate)
    {
        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Header
            $sheet->setCellValue('A1', 'BUKU BESAR');
            $sheet->setCellValue('A2', $coa['kode_akun'] . ' - ' . $coa['nama_akun']);
            $sheet->setCellValue('A3', 'Periode: ' . date('d/m/Y', strtotime($startDate)) . ' s/d ' . date('d/m/Y', strtotime($endDate)));
            
            // Column headers
            $sheet->setCellValue('A5', 'Tanggal');
            $sheet->setCellValue('B5', 'No. Jurnal');
            $sheet->setCellValue('C5', 'Keterangan');
            $sheet->setCellValue('D5', 'Debit');
            $sheet->setCellValue('E5', 'Kredit');
            $sheet->setCellValue('F5', 'Saldo');
            
            // Style header
            $sheet->getStyle('A5:F5')->getFont()->setBold(true);
            $sheet->getStyle('A5:F5')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF4F81BD');
            
            // Data
            $row = 6;
            $saldoAwalRow = $row;
            $sheet->setCellValue('A' . $row, 'Saldo Awal');
            $sheet->setCellValue('B' . $row, '-');
            $sheet->setCellValue('C' . $row, 'Saldo awal periode');
            $sheet->setCellValue('D' . $row, $bukuBesar['saldo_awal'] > 0 ? $bukuBesar['saldo_awal'] : 0);
            $sheet->setCellValue('E' . $row, $bukuBesar['saldo_awal'] < 0 ? abs($bukuBesar['saldo_awal']) : 0);
            $sheet->setCellValue('F' . $row, $bukuBesar['saldo_awal']);
            $row++;
            
            foreach ($bukuBesar['entries'] as $entry) {
                $sheet->setCellValue('A' . $row, date('d/m/Y', strtotime($entry['tanggal'])));
                $sheet->setCellValue('B' . $row, $entry['nomor_jurnal']);
                $sheet->setCellValue('C' . $row, $entry['keterangan']);
                $sheet->setCellValue('D' . $row, $entry['debit']);
                $sheet->setCellValue('E' . $row, $entry['kredit']);
                $sheet->setCellValue('F' . $row, $entry['saldo_akhir']);
                $row++;
            }
            
            // Total row
            $sheet->setCellValue('A' . $row, 'TOTAL');
            $sheet->setCellValue('D' . $row, $bukuBesar['total_debit']);
            $sheet->setCellValue('E' . $row, $bukuBesar['total_kredit']);
            $sheet->setCellValue('F' . $row, $bukuBesar['saldo_akhir']);
            $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->setBold(true);
            
            // Format currency
            $sheet->getStyle('D6:F' . $row)->getNumberFormat()
                ->setFormatCode('"Rp" #,##0.00');
            
            // Auto size columns
            foreach (range('A', 'F') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            $filename = 'Buku_Besar_' . $coa['kode_akun'] . '_' . date('Ymd_His') . '.xlsx';
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit();
            
        } catch (\Exception $e) {
            log_message('error', 'Export Excel error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export Excel: ' . $e->getMessage());
        }
    }

    /**
     * Export to PDF
     */
    private function exportPdf($coa, $bukuBesar, $startDate, $endDate)
    {
        $html = $this->generatePdfHtml($coa, $bukuBesar, $startDate, $endDate);
        
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('defaultFont', 'Arial');
        
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        $filename = 'Buku_Besar_' . $coa['kode_akun'] . '_' . date('Ymd_His') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
        exit();
    }

    /**
     * Generate PDF HTML
     */
    private function generatePdfHtml($coa, $bukuBesar, $startDate, $endDate)
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Buku Besar</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 10px; margin: 20px; }
                h1 { text-align: center; font-size: 18px; margin-bottom: 5px; }
                h2 { text-align: center; font-size: 14px; color: #666; margin-top: 0; margin-bottom: 10px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th { background-color: #4F81BD; color: white; padding: 8px; border: 1px solid #000; }
                td { padding: 6px; border: 1px solid #000; vertical-align: top; }
                .text-end { text-align: right; }
                .text-center { text-align: center; }
                .bold { font-weight: bold; }
                .footer { margin-top: 30px; text-align: right; font-size: 9px; }
            </style>
        </head>
        <body>
            <h1>BUKU BESAR</h1>
            <h2>' . $coa['kode_akun'] . ' - ' . $coa['nama_akun'] . '</h2>
            <div class="text-center">Periode: ' . date('d/m/Y', strtotime($startDate)) . ' s/d ' . date('d/m/Y', strtotime($endDate)) . '</div>
            
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>No. Jurnal</th>
                        <th>Keterangan</th>
                        <th class="text-end">Debit</th>
                        <th class="text-end">Kredit</th>
                        <th class="text-end">Saldo</th>
                    </tr>
                </thead>
                <tbody>';
        
        $html .= '<tr>';
        $html .= '<td>Saldo Awal</td>';
        $html .= '<td>-</td>';
        $html .= '<td>Saldo awal periode</td>';
        $html .= '<td class="text-end">' . ($bukuBesar['saldo_awal'] > 0 ? number_format($bukuBesar['saldo_awal'], 2) : 0) . '</td>';
        $html .= '<td class="text-end">' . ($bukuBesar['saldo_awal'] < 0 ? number_format(abs($bukuBesar['saldo_awal']), 2) : 0) . '</td>';
        $html .= '<td class="text-end">' . number_format($bukuBesar['saldo_awal'], 2) . '</td>';
        $html .= '</tr>';
        
        foreach ($bukuBesar['entries'] as $entry) {
            $html .= '<tr>';
            $html .= '<td class="text-center">' . date('d/m/Y', strtotime($entry['tanggal'])) . '</td>';
            $html .= '<td>' . $entry['nomor_jurnal'] . '</td>';
            $html .= '<td>' . $entry['keterangan'] . '</td>';
            $html .= '<td class="text-end">' . number_format($entry['debit'], 2) . '</td>';
            $html .= '<td class="text-end">' . number_format($entry['kredit'], 2) . '</td>';
            $html .= '<td class="text-end">' . number_format($entry['saldo_akhir'], 2) . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '<tr class="bold">';
        $html .= '<td colspan="3"><strong>TOTAL</strong></td>';
        $html .= '<td class="text-end"><strong>' . number_format($bukuBesar['total_debit'], 2) . '</strong></td>';
        $html .= '<td class="text-end"><strong>' . number_format($bukuBesar['total_kredit'], 2) . '</strong></td>';
        $html .= '<td class="text-end"><strong>' . number_format($bukuBesar['saldo_akhir'], 2) . '</strong></td>';
        $html .= '</tr>';
        
        $html .= '
                </tbody>
            </table>
            <div class="footer">
                Dicetak pada: ' . date('d/m/Y H:i:s') . '<br>
                Oleh: ' . (session()->get('name') ?? 'System') . '
            </div>
        </body>
        </html>';
        
        return $html;
    }

    /**
     * Print Buku Besar
     */
    public function print()
    {
        $coaId = $this->request->getGet('coa_id');
        $periode = $this->request->getGet('periode') ?? date('Y-m');
        $startDate = $this->request->getGet('tanggal_mulai') ?? $periode . '-01';
        $endDate = $this->request->getGet('tanggal_selesai') ?? date('Y-m-t', strtotime($startDate));
        
        if (empty($coaId)) {
            return redirect()->back()->with('error', 'Pilih akun terlebih dahulu');
        }
        
        $coa = $this->coaModel->find($coaId);
        if (!$coa) {
            return redirect()->back()->with('error', 'Akun tidak ditemukan');
        }
        
        $bukuBesar = $this->bukuBesarModel->getBukuBesarByCoa($coaId, $startDate, $endDate);
        
        $data = [
            'title' => 'Print Buku Besar',
            'coa' => $coa,
            'buku_besar' => $bukuBesar,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'printed_by' => session()->get('name') ?? 'System',
            'print_date' => date('d/m/Y H:i:s')
        ];
        
        return view('accounting/pembukuan/buku-besar/print', $data);
    }

    // ================================================================
    // AJAX METHODS
    // ================================================================

 public function ajaxGetPendingCounts()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setJSON(['success' => false]);
    }
    
    $bulan = $this->request->getGet('bulan');
    $tahun = $this->request->getGet('tahun');
    
    $periodeMulai = null;
    $periodeSelesai = null;
    
    if ($bulan && $tahun) {
        $periode = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT);
        $periodeMulai = $periode;
        $periodeSelesai = $periode;
        log_message('debug', 'Cek pending untuk periode: ' . $periode);
    }
    
    $pendingJurnals = $this->bukuBesarModel->getPendingJurnals($periodeMulai, $periodeSelesai);
    
    // Hitung jumlah jurnal unik
    $jurnalIds = [];
    foreach ($pendingJurnals as $detail) {
        $jurnalIds[$detail['jurnal_id']] = true;
    }
    
    return $this->response->setJSON([
        'success' => true,
        'jurnal_count' => count($jurnalIds),
        'detail_count' => count($pendingJurnals),
        'periode' => $periodeMulai
    ]);
}

    /**
     * AJAX: Get buku besar data
     */
    public function ajaxGetData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }
        
        $coaId = $this->request->getGet('coa_id');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        
        if (empty($coaId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'COA ID required']);
        }
        
        $data = $this->bukuBesarModel->getBukuBesarByCoa($coaId, $startDate, $endDate);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * AJAX: Get saldo akun
     */
    public function ajaxGetSaldo()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }
        
        $coaId = $this->request->getGet('coa_id');
        $tanggal = $this->request->getGet('tanggal') ?? date('Y-m-d');
        
        if (empty($coaId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'COA ID required']);
        }
        
        $saldo = $this->bukuBesarModel->getSaldoByDate($coaId, $tanggal);
        
        return $this->response->setJSON([
            'success' => true,
            'saldo' => $saldo,
            'saldo_formatted' => 'Rp ' . number_format($saldo, 2, ',', '.')
        ]);
    }

    /**
     * AJAX: Get neraca saldo data
     */
    public function ajaxGetNeracaSaldo()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }
        
        $periode = $this->request->getGet('periode') ?? date('Y-m');
        
        $neracaSaldo = $this->bukuBesarModel->getNeracaSaldo($periode);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $neracaSaldo['data'],
            'total_debit' => $neracaSaldo['total_debit'],
            'total_kredit' => $neracaSaldo['total_kredit'],
            'is_balance' => $neracaSaldo['is_balance']
        ]);
    }

    /**
     * AJAX: Get batch history
     */
    public function ajaxGetBatchHistory()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }
        
        $limit = $this->request->getGet('limit') ?? 10;
        $history = $this->bukuBesarModel->getBatchHistory($limit);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $history
        ]);
    }
    // ================================================================
// JURNAL POSTED (Jurnal yang sudah masuk buku besar)
// ================================================================

/**
 * Halaman daftar jurnal yang sudah diposting ke buku besar
 */
public function jurnalPosted()
{
    $data['title'] = 'Jurnal Posted';
    $data['subtitle'] = 'Daftar jurnal yang sudah diposting ke buku besar';
    
    // Get current user data
    $userModel = model('UserModel');
    $karyawanModel = model('KaryawanModel');
    
    $userId = session()->get('user_id');
    $user = $userModel->find($userId);
    $karyawan = $user && $user['karyawan_id'] ? $karyawanModel->find($user['karyawan_id']) : [];
    
    // Get filters
    $filters = [
        'tanggal_mulai' => $this->request->getGet('tanggal_mulai'),
        'tanggal_selesai' => $this->request->getGet('tanggal_selesai'),
        'tipe_jurnal' => $this->request->getGet('tipe_jurnal'),
        'status' => $this->request->getGet('status'),
        'batch_id' => $this->request->getGet('batch_id'),
        'search' => $this->request->getGet('search')
    ];
    
    $perPage = $this->request->getGet('per_page') ?? 20;
    $page = $this->request->getGet('page') ?? 1;
    
    // Get jurnal posted data
    $result = $this->bukuBesarModel->getJurnalPosted($filters, $perPage, $page);
    
    // Get batch list untuk filter
    $batchList = $this->bukuBesarModel->getBatchHistory(100);
    
    $data['jurnal'] = $result['data'];
    $data['pager'] = [
        'total' => $result['total'],
        'per_page' => $result['per_page'],
        'current_page' => $result['current_page'],
        'total_pages' => $result['total_pages']
    ];
    $data['filters'] = $filters;
    $data['batch_list'] = $batchList;
    $data['user'] = $user ?? ['name' => 'Accounting Staff', 'role' => 'accounting'];
    $data['karyawan'] = $karyawan ?? [];
    $data['active'] = 'jurnal-posted';
    
    return view('accounting/pembukuan/buku-besar/jurnal-posted', $data);
}

/**
 * Detail jurnal yang sudah diposting
 */
public function jurnalPostedDetail($jurnalId)
{
    $data['title'] = 'Detail Jurnal Posted';
    $data['subtitle'] = 'Detail lengkap jurnal yang sudah diposting';
    
    // Get current user data
    $userModel = model('UserModel');
    $karyawanModel = model('KaryawanModel');
    
    $userId = session()->get('user_id');
    $user = $userModel->find($userId);
    $karyawan = $user && $user['karyawan_id'] ? $karyawanModel->find($user['karyawan_id']) : [];
    
    // Get jurnal detail from buku besar
    $jurnal = $this->bukuBesarModel->getJurnalPostedDetail($jurnalId);
    
    if (!$jurnal) {
        return redirect()->to(site_url('accounting/pembukuan/buku-besar/jurnal-posted'))
            ->with('error', 'Jurnal tidak ditemukan');
    }
    
    $data['jurnal'] = $jurnal;
    $data['user'] = $user ?? ['name' => 'Accounting Staff', 'role' => 'accounting'];
    $data['karyawan'] = $karyawan ?? [];
    $data['active'] = 'jurnal-posted';
    
    return view('accounting/pembukuan/buku-besar/jurnal-posted-detail', $data);
}

/**
 * Detail batch (lihat semua jurnal dalam satu batch)
 */
public function batchDetail($batchId)
{
    $data['title'] = 'Detail Batch';
    $data['subtitle'] = 'Detail batch posting: ' . $batchId;
    
    // Get current user data
    $userModel = model('UserModel');
    $karyawanModel = model('KaryawanModel');
    
    $userId = session()->get('user_id');
    $user = $userModel->find($userId);
    $karyawan = $user && $user['karyawan_id'] ? $karyawanModel->find($user['karyawan_id']) : [];
    
    // Get batch detail
    $batchDetail = $this->bukuBesarModel->getBatchDetail($batchId);
    
    if (empty($batchDetail)) {
        return redirect()->to(site_url('accounting/pembukuan/buku-besar/jurnal-posted'))
            ->with('error', 'Batch tidak ditemukan');
    }
    
    // Get summary per jurnal dalam batch
    $summary = [];
    foreach ($batchDetail as $item) {
        $key = $item['jurnal_id'];
        if (!isset($summary[$key])) {
            $summary[$key] = [
                'jurnal_id' => $item['jurnal_id'],
                'nomor_jurnal' => $item['nomor_jurnal'],
                'tanggal' => $item['tanggal'],
                'keterangan' => $item['keterangan'],
                'tipe_jurnal' => $item['tipe_jurnal'],
                'total_debit' => 0,
                'total_kredit' => 0,
                'status' => $item['status'],
                'entries' => []
            ];
        }
        $summary[$key]['total_debit'] += $item['debit'];
        $summary[$key]['total_kredit'] += $item['kredit'];
        $summary[$key]['entries'][] = $item;
    }
    
    $data['batch_id'] = $batchId;
    $data['batch_detail'] = $summary;
    $data['total_jurnal'] = count($summary);
    $data['total_baris'] = count($batchDetail);
    $data['user'] = $user ?? ['name' => 'Accounting Staff', 'role' => 'accounting'];
    $data['karyawan'] = $karyawan ?? [];
    $data['active'] = 'jurnal-posted';
    
    return view('accounting/pembukuan/buku-besar/batch-detail', $data);
}

/**
 * Void jurnal yang sudah diposting (membatalkan)
 */
public function voidJurnal($jurnalId)
{
    if (!$this->request->isAJAX()) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Hanya request AJAX yang diperbolehkan'
        ]);
    }
    
    $reason = $this->request->getPost('reason');
    
    if (empty($reason)) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Alasan pembatalan harus diisi'
        ]);
    }
    
    try {
        $result = $this->bukuBesarModel->voidByJurnalId($jurnalId, $reason);
        return $this->response->setJSON($result);
        
    } catch (\Exception $e) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}

/**
 * Export jurnal posted ke Excel/PDF
 */
public function exportJurnalPosted()
{
    $type = $this->request->getGet('type') ?? 'excel';
    
    $filters = [
        'tanggal_mulai' => $this->request->getGet('tanggal_mulai'),
        'tanggal_selesai' => $this->request->getGet('tanggal_selesai'),
        'tipe_jurnal' => $this->request->getGet('tipe_jurnal'),
        'status' => $this->request->getGet('status'),
        'batch_id' => $this->request->getGet('batch_id'),
        'search' => $this->request->getGet('search')
    ];
    
    $data = $this->bukuBesarModel->getJurnalPostedExport($filters);
    
    if ($type === 'excel') {
        return $this->exportJurnalPostedExcel($data, $filters);
    } else {
        return $this->exportJurnalPostedPdf($data, $filters);
    }
}

/**
 * Export batch detail ke Excel/PDF
 */
public function exportBatch($batchId)
{
    $type = $this->request->getGet('type') ?? 'excel';
    
    $batchDetail = $this->bukuBesarModel->getBatchDetail($batchId);
    
    if (empty($batchDetail)) {
        return redirect()->back()->with('error', 'Batch tidak ditemukan');
    }
    
    if ($type === 'excel') {
        return $this->exportBatchExcel($batchDetail, $batchId);
    } else {
        return $this->exportBatchPdf($batchDetail, $batchId);
    }
}

// ================================================================
// PRIVATE METHOD UNTUK EXPORT
// ================================================================

private function exportJurnalPostedExcel($data, $filters)
{
    try {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Title
        $sheet->setCellValue('A1', 'LAPORAN JURNAL POSTED');
        $sheet->setCellValue('A2', 'PT. CIPTA DUTA WACANA');
        
        $periodeText = '';
        if (!empty($filters['tanggal_mulai']) && !empty($filters['tanggal_selesai'])) {
            $periodeText = 'Periode: ' . date('d/m/Y', strtotime($filters['tanggal_mulai'])) . ' - ' . date('d/m/Y', strtotime($filters['tanggal_selesai']));
        } elseif (!empty($filters['tanggal_mulai'])) {
            $periodeText = 'Periode: Mulai ' . date('d/m/Y', strtotime($filters['tanggal_mulai']));
        } elseif (!empty($filters['tanggal_selesai'])) {
            $periodeText = 'Periode: Sampai ' . date('d/m/Y', strtotime($filters['tanggal_selesai']));
        } else {
            $periodeText = 'Periode: Semua';
        }
        
        $sheet->setCellValue('A3', $periodeText);
        
        // Headers
        $sheet->setCellValue('A5', 'No');
        $sheet->setCellValue('B5', 'Tanggal');
        $sheet->setCellValue('C5', 'Nomor Jurnal');
        $sheet->setCellValue('D5', 'Keterangan');
        $sheet->setCellValue('E5', 'Tipe');
        $sheet->setCellValue('F5', 'Total Debit');
        $sheet->setCellValue('G5', 'Total Kredit');
        $sheet->setCellValue('H5', 'Status');
        $sheet->setCellValue('I5', 'Batch ID');
        $sheet->setCellValue('J5', 'Waktu Posting');
        
        // Style header
        $sheet->getStyle('A5:J5')->getFont()->setBold(true);
        
        // Data
        $row = 6;
        $no = 1;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($item['tanggal'])));
            $sheet->setCellValue('C' . $row, $item['nomor_jurnal']);
            $sheet->setCellValue('D' . $row, $item['keterangan']);
            $sheet->setCellValue('E' . $row, $item['tipe_jurnal']);
            $sheet->setCellValue('F' . $row, $item['total_debit']);
            $sheet->setCellValue('G' . $row, $item['total_kredit']);
            $sheet->setCellValue('H' . $row, $item['status']);
            $sheet->setCellValue('I' . $row, $item['batch_id']);
            $sheet->setCellValue('J' . $row, !empty($item['processed_at']) ? date('d/m/Y H:i', strtotime($item['processed_at'])) : '-');
            $row++;
        }
        
        // Format currency
        $sheet->getStyle('F6:G' . ($row-1))->getNumberFormat()
            ->setFormatCode('"Rp" #,##0.00');
        
        // Auto size columns
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $filename = 'Jurnal_Posted_' . date('Ymd_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
        
    } catch (\Exception $e) {
        log_message('error', 'Export Jurnal Posted error: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Gagal export: ' . $e->getMessage());
    }
}

private function exportJurnalPostedPdf($data, $filters)
{
    $html = $this->generateJurnalPostedPdfHtml($data, $filters);
    
    $options = new \Dompdf\Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', true);
    $options->set('defaultFont', 'Arial');
    
    $dompdf = new \Dompdf\Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    
    $filename = 'Jurnal_Posted_' . date('Ymd_His') . '.pdf';
    $dompdf->stream($filename, ['Attachment' => true]);
    exit();
}

private function generateJurnalPostedPdfHtml($data, $filters)
{
    $periodeText = '';
    if (!empty($filters['tanggal_mulai']) && !empty($filters['tanggal_selesai'])) {
        $periodeText = date('d/m/Y', strtotime($filters['tanggal_mulai'])) . ' - ' . date('d/m/Y', strtotime($filters['tanggal_selesai']));
    } elseif (!empty($filters['tanggal_mulai'])) {
        $periodeText = 'Mulai ' . date('d/m/Y', strtotime($filters['tanggal_mulai']));
    } elseif (!empty($filters['tanggal_selesai'])) {
        $periodeText = 'Sampai ' . date('d/m/Y', strtotime($filters['tanggal_selesai']));
    } else {
        $periodeText = 'Semua Periode';
    }
    
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Laporan Jurnal Posted</title>
        <style>
            body { font-family: Arial, sans-serif; font-size: 10px; margin: 20px; }
            h1 { text-align: center; font-size: 18px; margin-bottom: 5px; }
            h2 { text-align: center; font-size: 14px; color: #666; margin-top: 0; margin-bottom: 10px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th { background-color: #4F81BD; color: white; padding: 8px; border: 1px solid #000; }
            td { padding: 6px; border: 1px solid #000; vertical-align: top; }
            .text-end { text-align: right; }
            .text-center { text-align: center; }
            .footer { margin-top: 30px; text-align: right; font-size: 9px; }
        </style>
    </head>
    <body>
        <h1>LAPORAN JURNAL POSTED</h1>
        <h2>PT. CIPTA DUTA WACANA</h2>
        <div class="text-center">Periode: ' . $periodeText . '</div>
        
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nomor Jurnal</th>
                    <th>Keterangan</th>
                    <th>Tipe</th>
                    <th class="text-end">Debit</th>
                    <th class="text-end">Kredit</th>
                    <th>Status</th>
                    <th>Batch ID</th>
                </tr>
            </thead>
            <tbody>';
    
    $no = 1;
    foreach ($data as $item) {
        $html .= '<tr>';
        $html .= '<td class="text-center">' . $no++ . '</td>';
        $html .= '<td class="text-center">' . date('d/m/Y', strtotime($item['tanggal'])) . '</td>';
        $html .= '<td>' . $item['nomor_jurnal'] . '</td>';
        $html .= '<td>' . $item['keterangan'] . '</td>';
        $html .= '<td>' . $item['tipe_jurnal'] . '</td>';
        $html .= '<td class="text-end">' . number_format($item['total_debit'], 2) . '</td>';
        $html .= '<td class="text-end">' . number_format($item['total_kredit'], 2) . '</td>';
        $html .= '<td>' . $item['status'] . '</td>';
        $html .= '<td>' . $item['batch_id'] . '</td>';
        $html .= '</tr>';
    }
    
    $html .= '
            </tbody>
        </table>
        <div class="footer">
            Dicetak pada: ' . date('d/m/Y H:i:s') . '<br>
            Oleh: ' . (session()->get('name') ?? 'System') . '
        </div>
    </body>
    </html>';
    
    return $html;
}

private function exportBatchExcel($data, $batchId)
{
    try {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Title
        $sheet->setCellValue('A1', 'DETAIL BATCH');
        $sheet->setCellValue('A2', 'Batch ID: ' . $batchId);
        $sheet->setCellValue('A3', 'PT. CIPTA DUTA WACANA');
        
        // Headers
        $sheet->setCellValue('A5', 'No');
        $sheet->setCellValue('B5', 'Tanggal');
        $sheet->setCellValue('C5', 'Nomor Jurnal');
        $sheet->setCellValue('D5', 'Kode Akun');
        $sheet->setCellValue('E5', 'Nama Akun');
        $sheet->setCellValue('F5', 'Debit');
        $sheet->setCellValue('G5', 'Kredit');
        $sheet->setCellValue('H5', 'Keterangan');
        $sheet->setCellValue('I5', 'Status');
        
        // Style header
        $sheet->getStyle('A5:I5')->getFont()->setBold(true);
        
        // Data
        $row = 6;
        $no = 1;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($item['tanggal'])));
            $sheet->setCellValue('C' . $row, $item['nomor_jurnal']);
            $sheet->setCellValue('D' . $row, $item['kode_akun']);
            $sheet->setCellValue('E' . $row, $item['nama_akun']);
            $sheet->setCellValue('F' . $row, $item['debit']);
            $sheet->setCellValue('G' . $row, $item['kredit']);
            $sheet->setCellValue('H' . $row, $item['keterangan']);
            $sheet->setCellValue('I' . $row, $item['status']);
            $row++;
        }
        
        // Format currency
        $sheet->getStyle('F6:G' . ($row-1))->getNumberFormat()
            ->setFormatCode('"Rp" #,##0.00');
        
        // Auto size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $filename = 'Batch_Detail_' . $batchId . '_' . date('Ymd_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
        
    } catch (\Exception $e) {
        log_message('error', 'Export Batch error: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Gagal export: ' . $e->getMessage());
    }
}

private function exportBatchPdf($data, $batchId)
{
    $html = $this->generateBatchPdfHtml($data, $batchId);
    
    $options = new \Dompdf\Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', true);
    $options->set('defaultFont', 'Arial');
    
    $dompdf = new \Dompdf\Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    
    $filename = 'Batch_Detail_' . $batchId . '_' . date('Ymd_His') . '.pdf';
    $dompdf->stream($filename, ['Attachment' => true]);
    exit();
}

private function generateBatchPdfHtml($data, $batchId)
{
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Detail Batch</title>
        <style>
            body { font-family: Arial, sans-serif; font-size: 10px; margin: 20px; }
            h1 { text-align: center; font-size: 18px; margin-bottom: 5px; }
            h2 { text-align: center; font-size: 14px; color: #666; margin-top: 0; margin-bottom: 10px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th { background-color: #4F81BD; color: white; padding: 8px; border: 1px solid #000; }
            td { padding: 6px; border: 1px solid #000; vertical-align: top; }
            .text-end { text-align: right; }
            .text-center { text-align: center; }
            .footer { margin-top: 30px; text-align: right; font-size: 9px; }
        </style>
    </head>
    <body>
        <h1>DETAIL BATCH</h1>
        <h2>Batch ID: ' . $batchId . '</h2>
        <div class="text-center">PT. CIPTA DUTA WACANA</div>
        
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nomor Jurnal</th>
                    <th>Kode Akun</th>
                    <th>Nama Akun</th>
                    <th class="text-end">Debit</th>
                    <th class="text-end">Kredit</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>';
    
    $no = 1;
    foreach ($data as $item) {
        $html .= '<tr>';
        $html .= '<td class="text-center">' . $no++ . '</td>';
        $html .= '<td class="text-center">' . date('d/m/Y', strtotime($item['tanggal'])) . '</td>';
        $html .= '<td>' . $item['nomor_jurnal'] . '</td>';
        $html .= '<td>' . $item['kode_akun'] . '</td>';
        $html .= '<td>' . $item['nama_akun'] . '</td>';
        $html .= '<td class="text-end">' . number_format($item['debit'], 2) . '</td>';
        $html .= '<td class="text-end">' . number_format($item['kredit'], 2) . '</td>';
        $html .= '<td>' . $item['keterangan'] . '</td>';
        $html .= '<td>' . $item['status'] . '</td>';
        $html .= '</tr>';
    }
    
    $html .= '
            </tbody>
        </table>
        <div class="footer">
            Dicetak pada: ' . date('d/m/Y H:i:s') . '<br>
            Oleh: ' . (session()->get('name') ?? 'System') . '
        </div>
    </body>
    </html>';
    
    return $html;
}

/**
 * AJAX: Get available periods (periode yang memiliki jurnal pending)
 */
public function ajaxGetAvailablePeriods()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setJSON(['success' => false]);
    }
    
    $db = \Config\Database::connect();
    
    // Get distinct bulan-tahun dari jurnal yang belum diproses
    $pendingJurnals = $this->bukuBesarModel->getPendingJurnals();
    
    $periods = [];
    foreach ($pendingJurnals as $jurnal) {
        $periode = date('Y-m', strtotime($jurnal['tanggal']));
        if (!in_array($periode, $periods)) {
            $periods[] = $periode;
        }
    }
    
    // Format untuk display
    $namaBulan = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];
    
    $formattedPeriods = [];
    foreach ($periods as $periode) {
        list($tahun, $bulan) = explode('-', $periode);
        $formattedPeriods[] = $namaBulan[$bulan] . ' ' . $tahun;
    }
    
    sort($formattedPeriods);
    
    return $this->response->setJSON([
        'success' => true,
        'periods' => $formattedPeriods,
        'raw_periods' => $periods
    ]);
}

}
?>