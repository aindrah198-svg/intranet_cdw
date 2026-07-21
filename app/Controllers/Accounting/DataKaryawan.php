<?php
namespace App\Controllers\Accounting\Penggajian;

use App\Controllers\BaseController;
use App\Models\Accounting\Penggajian\DataKaryawanModel;
use App\Models\KaryawanModel;
use App\Models\KontrakModel;
use App\Models\UsersModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Dompdf\Dompdf;
use Dompdf\Options;

class DataKaryawan extends BaseController
{
    protected $dataKaryawanModel;
    protected $karyawanModel;
    protected $kontrakModel;
    protected $usersModel;
    protected $db;

    public function __construct()
    {
        $this->dataKaryawanModel = new DataKaryawanModel();
        $this->karyawanModel = new KaryawanModel();
        $this->kontrakModel = new KontrakModel();
        $this->usersModel = new UsersModel();
        $this->db = \Config\Database::connect();
        
        helper(['form', 'url', 'text', 'number']);
        
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
    }

    /**
     * Index / Daftar Karyawan untuk Penggajian
     */
    public function index()
    {
        $data['title'] = 'Data Karyawan - Penggajian';
        
        $filters = [
            'search' => $this->request->getGet('search'),
            'status_karyawan' => $this->request->getGet('status_karyawan'),
            'departemen' => $this->request->getGet('departemen'),
            'divisi' => $this->request->getGet('divisi'),
            'jabatan' => $this->request->getGet('jabatan'),
            'memiliki_akun' => $this->request->getGet('memiliki_akun'),
            'memiliki_penggajian' => $this->request->getGet('memiliki_penggajian')
        ];
        
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 20;
        
        $result = $this->dataKaryawanModel->getAllForPayroll($filters, $perPage, $page);
        
        $data['karyawan'] = $result['data'];
        $data['pager'] = $this->dataKaryawanModel->pager;
        $data['total'] = $result['total'];
        $data['perPage'] = $perPage;
        $data['currentPage'] = $page;
        $data['totalPages'] = $result['total_pages'];
        
        // Data untuk filter dropdown
        $data['departemenOptions'] = $this->dataKaryawanModel->getDepartemenOptions();
        $data['divisiOptions'] = $this->dataKaryawanModel->getDivisiOptions();
        $data['jabatanOptions'] = $this->dataKaryawanModel->getJabatanOptions();
        $data['statusKaryawanOptions'] = $this->dataKaryawanModel->getStatusKaryawanOptions();
        
        // Statistik untuk dashboard
        $data['stats'] = $this->dataKaryawanModel->getStatistikKaryawan();
        $data['distribusiDepartemen'] = $this->dataKaryawanModel->getDistribusiPerDepartemen();
        
        return view('accounting/penggajian/data-karyawan/index', $data);
    }

    /**
     * Get data untuk DataTables (AJAX)
     */
    public function getData()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('accounting/penggajian/data-karyawan');
        }
        
        $filters = [
            'search' => $this->request->getPost('search'),
            'status_karyawan' => $this->request->getPost('status_karyawan'),
            'departemen' => $this->request->getPost('departemen'),
            'divisi' => $this->request->getPost('divisi'),
            'jabatan' => $this->request->getPost('jabatan')
        ];
        
        $page = $this->request->getPost('page') ?? 1;
        $perPage = $this->request->getPost('per_page') ?? 20;
        
        $result = $this->dataKaryawanModel->getAllForPayroll($filters, $perPage, $page);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $result['data'],
            'total' => $result['total'],
            'current_page' => $result['current_page'],
            'total_pages' => $result['total_pages']
        ]);
    }

    /**
     * Detail karyawan untuk penggajian
     */
    public function detail($id)
    {
        $data['title'] = 'Detail Karyawan';
        
        $karyawan = $this->dataKaryawanModel->getKaryawanDetail($id);
        
        if (!$karyawan) {
            return redirect()->to('accounting/penggajian/data-karyawan')
                ->with('error', 'Data karyawan tidak ditemukan');
        }
        
        $data['karyawan'] = $karyawan;
        
        // Ambil riwayat kontrak
        $data['kontrak'] = $this->kontrakModel
            ->where('karyawan_id', $id)
            ->orderBy('tanggal_mulai', 'DESC')
            ->findAll();
        
        // Ambil riwayat penggajian
        $db = \Config\Database::connect();
        $data['riwayat_gaji'] = $db->table('penggajian_detail_pembayaran d')
            ->select('
                pg.periode_bulan,
                pg.periode_tahun,
                pg.gaji_pokok,
                pg.total_potongan,
                d.gaji_bersih,
                d.status_pembayaran,
                d.tanggal_pembayaran,
                p.nomor_proses
            ')
            ->join('penggajian_proses_pembayaran p', 'p.id = d.proses_id')
            ->join('penggajian_perhitungan pg', 'pg.id = d.perhitungan_id')
            ->where('d.karyawan_id', $id)
            ->orderBy('pg.periode_tahun', 'DESC')
            ->orderBy('pg.periode_bulan', 'DESC')
            ->limit(12)
            ->get()
            ->getResultArray();
        
        return view('accounting/penggajian/data-karyawan/detail', $data);
    }

    /**
     * Karyawan Aktif
     */
    public function aktif()
    {
        $data['title'] = 'Karyawan Aktif';
        
        $filters = [
            'status_karyawan' => 'aktif',
            'search' => $this->request->getGet('search'),
            'departemen' => $this->request->getGet('departemen')
        ];
        
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 20;
        
        $result = $this->dataKaryawanModel->getAllForPayroll($filters, $perPage, $page);
        
        $data['karyawan'] = $result['data'];
        $data['pager'] = $this->dataKaryawanModel->pager;
        $data['total'] = $result['total'];
        $data['active_tab'] = 'aktif';
        
        $data['departemenOptions'] = $this->dataKaryawanModel->getDepartemenOptions();
        $data['stats'] = $this->dataKaryawanModel->getStatistikKaryawan();
        
        return view('accounting/penggajian/data-karyawan/index', $data);
    }

    /**
     * Karyawan Non-Aktif (Magang)
     */
    public function nonAktif()
    {
        $data['title'] = 'Karyawan Non-Aktif';
        
        $filters = [
            'status_karyawan' => 'nonaktif',
            'search' => $this->request->getGet('search')
        ];
        
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 20;
        
        $result = $this->dataKaryawanModel->getAllForPayroll($filters, $perPage, $page);
        
        $data['karyawan'] = $result['data'];
        $data['pager'] = $this->dataKaryawanModel->pager;
        $data['total'] = $result['total'];
        $data['active_tab'] = 'nonaktif';
        
        $data['stats'] = $this->dataKaryawanModel->getStatistikKaryawan();
        
        return view('accounting/penggajian/data-karyawan/index', $data);
    }

    /**
     * Karyawan Keluar
     */
    public function keluar()
    {
        $data['title'] = 'Karyawan Keluar';
        
        $filters = [
            'status_karyawan' => 'keluar',
            'search' => $this->request->getGet('search')
        ];
        
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 20;
        
        $result = $this->dataKaryawanModel->getAllForPayroll($filters, $perPage, $page);
        
        $data['karyawan'] = $result['data'];
        $data['pager'] = $this->dataKaryawanModel->pager;
        $data['total'] = $result['total'];
        $data['active_tab'] = 'keluar';
        
        $data['stats'] = $this->dataKaryawanModel->getStatistikKaryawan();
        
        return view('accounting/penggajian/data-karyawan/index', $data);
    }

    /**
     * Riwayat Gaji Karyawan
     */
    public function riwayatGaji($id)
    {
        $data['title'] = 'Riwayat Gaji Karyawan';
        
        $karyawan = $this->karyawanModel->find($id);
        
        if (!$karyawan) {
            return redirect()->to('accounting/penggajian/data-karyawan')
                ->with('error', 'Data karyawan tidak ditemukan');
        }
        
        $data['karyawan'] = $karyawan;
        
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $data['tahun'] = $tahun;
        
        $db = \Config\Database::connect();
        
        // Ambil riwayat gaji per tahun
        $data['riwayat'] = $db->table('penggajian_detail_pembayaran d')
            ->select('
                pg.periode_bulan,
                pg.periode_tahun,
                pg.gaji_pokok,
                pg.tunjangan_jabatan,
                pg.tunjangan_bpjs,
                pg.tunjangan_makan,
                pg.tunjangan_transport,
                pg.tunjangan_lainnya,
                pg.upah_lembur,
                pg.potongan_bpjs_kes,
                pg.potongan_bpjs_tk,
                pg.potongan_pph21,
                pg.potongan_absensi,
                pg.potongan_kasbon,
                pg.potongan_lainnya,
                pg.total_hadir,
                pg.total_izin,
                pg.total_sakit,
                pg.total_cuti,
                pg.total_alpha,
                pg.jam_lembur,
                d.gaji_bersih,
                d.status_pembayaran,
                d.tanggal_pembayaran,
                p.nomor_proses,
                p.tanggal_proses
            ')
            ->join('penggajian_proses_pembayaran p', 'p.id = d.proses_id')
            ->join('penggajian_perhitungan pg', 'pg.id = d.perhitungan_id')
            ->where('d.karyawan_id', $id)
            ->where('pg.periode_tahun', $tahun)
            ->where('p.status', 'Selesai')
            ->orderBy('pg.periode_bulan', 'ASC')
            ->get()
            ->getResultArray();
        
        // Hitung total setahun
        $total = [
            'gaji_pokok' => 0,
            'tunjangan' => 0,
            'lembur' => 0,
            'potongan' => 0,
            'gaji_bersih' => 0,
            'hadir' => 0,
            'alpha' => 0
        ];
        
        foreach ($data['riwayat'] as $row) {
            $total['gaji_pokok'] += $row['gaji_pokok'];
            $total['tunjangan'] += ($row['tunjangan_jabatan'] + $row['tunjangan_bpjs'] + 
                                    $row['tunjangan_makan'] + $row['tunjangan_transport'] + 
                                    $row['tunjangan_lainnya']);
            $total['lembur'] += $row['upah_lembur'];
            $total['potongan'] += ($row['potongan_bpjs_kes'] + $row['potongan_bpjs_tk'] + 
                                   $row['potongan_pph21'] + $row['potongan_absensi'] + 
                                   $row['potongan_kasbon'] + $row['potongan_lainnya']);
            $total['gaji_bersih'] += $row['gaji_bersih'];
            $total['hadir'] += $row['total_hadir'];
            $total['alpha'] += $row['total_alpha'];
        }
        
        $data['total'] = $total;
        $data['jumlah_bulan'] = count($data['riwayat']);
        
        // Tahun-tahun yang tersedia untuk filter
        $data['tahunOptions'] = $db->table('penggajian_perhitungan')
            ->select('DISTINCT periode_tahun')
            ->where('karyawan_id', $id)
            ->orderBy('periode_tahun', 'DESC')
            ->get()
            ->getResultArray();
        
        return view('accounting/penggajian/data-karyawan/riwayat-gaji', $data);
    }

    /**
     * Karyawan tanpa data bank lengkap
     */
    public function tanpaBank()
    {
        $data['title'] = 'Karyawan Tanpa Data Bank';
        
        $karyawan = $this->dataKaryawanModel->getKaryawanWithoutBankInfo();
        
        $data['karyawan'] = $karyawan;
        $data['total'] = count($karyawan);
        
        return view('accounting/penggajian/data-karyawan/tanpa-bank', $data);
    }

    /**
     * Export data karyawan
     */
    public function export()
    {
        $type = $this->request->getGet('type') ?? 'excel';
        
        $filters = [
            'status_karyawan' => $this->request->getGet('status_karyawan'),
            'departemen' => $this->request->getGet('departemen')
        ];
        
        $data = $this->dataKaryawanModel->getExportData($filters);
        
        $stats = $this->dataKaryawanModel->getStatistikKaryawan();
        
        if ($type === 'excel') {
            return $this->exportExcel($data, $filters, $stats);
        } else {
            return $this->exportPdf($data, $filters, $stats);
        }
    }

    /**
     * Export ke Excel
     */
    private function exportExcel($data, $filters, $stats)
    {
        try {
            $spreadsheet = new Spreadsheet();
            
            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator("CDW Engineering")
                ->setLastModifiedBy("CDW Engineering")
                ->setTitle("Data Karyawan - Penggajian")
                ->setSubject("Data Karyawan untuk Penggajian")
                ->setDescription("Data Karyawan " . date('d-m-Y'));
            
            // ============= SHEET 1: DATA KARYAWAN =============
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Data Karyawan');
            
            // Header laporan
            $sheet->mergeCells('A1:AF1');
            $sheet->setCellValue('A1', 'DATA KARYAWAN - PENGGUNAAN UNTUK PENGGAJIAN');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->mergeCells('A2:AF2');
            $sheet->setCellValue('A2', 'PT. CIPTA DUTA WACANA');
            $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Filter info
            $rowFilter = 3;
            if (!empty($filters['status_karyawan'])) {
                $statusLabel = '';
                if ($filters['status_karyawan'] == 'aktif') {
                    $statusLabel = 'Karyawan Aktif';
                } elseif ($filters['status_karyawan'] == 'nonaktif') {
                    $statusLabel = 'Karyawan Non-Aktif';
                } elseif ($filters['status_karyawan'] == 'keluar') {
                    $statusLabel = 'Karyawan Keluar';
                } else {
                    $statusLabel = $filters['status_karyawan'];
                }
                
                $sheet->mergeCells('A' . $rowFilter . ':AF' . $rowFilter);
                $sheet->setCellValue('A' . $rowFilter, 'Status: ' . $statusLabel);
                $rowFilter++;
            }
            
            if (!empty($filters['departemen'])) {
                $sheet->mergeCells('A' . $rowFilter . ':AF' . $rowFilter);
                $sheet->setCellValue('A' . $rowFilter, 'Departemen: ' . $filters['departemen']);
                $rowFilter++;
            }
            
            // Baris kosong
            $rowFilter++;
            $startRow = $rowFilter;
            
            // Header tabel
            $headers = [
                'A' => 'No',
                'B' => 'NIK',
                'C' => 'Nama Lengkap',
                'D' => 'Nama Panggilan',
                'E' => 'Jenis Kelamin',
                'F' => 'Tempat Lahir',
                'G' => 'Tanggal Lahir',
                'H' => 'Agama',
                'I' => 'Status Pernikahan',
                'J' => 'Alamat',
                'K' => 'Telepon',
                'L' => 'Email',
                'M' => 'Jabatan',
                'N' => 'Departemen',
                'O' => 'Divisi',
                'P' => 'Tanggal Masuk',
                'Q' => 'Status Karyawan',
                'R' => 'Tanggal Keluar',
                'S' => 'NPWP',
                'T' => 'BPJS Kesehatan',
                'U' => 'BPJS TK',
                'V' => 'Bank',
                'W' => 'No Rekening',
                'X' => 'Nama Rekening',
                'Y' => 'Pendidikan',
                'Z' => 'Jurusan',
                'AA' => 'Institusi',
                'AB' => 'Tahun Lulus',
                'AC' => 'Kontak Darurat',
                'AD' => 'Hubungan',
                'AE' => 'Telp Darurat',
                'AF' => 'Username'
            ];
            
            foreach ($headers as $col => $header) {
                $sheet->setCellValue($col . $startRow, $header);
            }
            
            // Style header
            $headerRange = 'A' . $startRow . ':AF' . $startRow;
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
                $sheet->setCellValue('B' . $row, $item['NIK']);
                $sheet->setCellValue('C' . $row, $item['Nama Lengkap']);
                $sheet->setCellValue('D' . $row, $item['Nama Panggilan']);
                $sheet->setCellValue('E' . $row, $item['Jenis Kelamin']);
                $sheet->setCellValue('F' . $row, $item['Tempat Lahir']);
                $sheet->setCellValue('G' . $row, $item['Tanggal Lahir']);
                $sheet->setCellValue('H' . $row, $item['Agama']);
                $sheet->setCellValue('I' . $row, $item['Status Pernikahan']);
                $sheet->setCellValue('J' . $row, $item['Alamat']);
                $sheet->setCellValue('K' . $row, $item['Telepon']);
                $sheet->setCellValue('L' . $row, $item['Email']);
                $sheet->setCellValue('M' . $row, $item['Jabatan']);
                $sheet->setCellValue('N' . $row, $item['Departemen']);
                $sheet->setCellValue('O' . $row, $item['Divisi']);
                $sheet->setCellValue('P' . $row, $item['Tanggal Masuk']);
                $sheet->setCellValue('Q' . $row, $item['Status Karyawan']);
                $sheet->setCellValue('R' . $row, $item['Tanggal Keluar']);
                $sheet->setCellValue('S' . $row, $item['NPWP']);
                $sheet->setCellValue('T' . $row, $item['BPJS Kesehatan']);
                $sheet->setCellValue('U' . $row, $item['BPJS Ketenagakerjaan']);
                $sheet->setCellValue('V' . $row, $item['Bank']);
                $sheet->setCellValue('W' . $row, $item['No Rekening']);
                $sheet->setCellValue('X' . $row, $item['Nama Rekening']);
                $sheet->setCellValue('Y' . $row, $item['Pendidikan Terakhir']);
                $sheet->setCellValue('Z' . $row, $item['Jurusan']);
                $sheet->setCellValue('AA' . $row, $item['Institusi']);
                $sheet->setCellValue('AB' . $row, $item['Tahun Lulus']);
                $sheet->setCellValue('AC' . $row, $item['Kontak Darurat - Nama']);
                $sheet->setCellValue('AD' . $row, $item['Kontak Darurat - Hubungan']);
                $sheet->setCellValue('AE' . $row, $item['Kontak Darurat - Telepon']);
                $sheet->setCellValue('AF' . $row, $item['Username']);
                
                $row++;
                $no++;
            }
            
            // Auto-size columns
            foreach (range('A', 'AF') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Border untuk seluruh data
            $lastRow = $row - 1;
            if ($lastRow >= $startRow) {
                $dataRange = 'A' . $startRow . ':AF' . $lastRow;
                $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            }
            
            // ============= SHEET 2: STATISTIK =============
            $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex(1);
            $sheet2 = $spreadsheet->getActiveSheet();
            $sheet2->setTitle('Statistik');
            
            // Header
            $sheet2->mergeCells('A1:C1');
            $sheet2->setCellValue('A1', 'STATISTIK KARYAWAN');
            $sheet2->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            
            // Statistik
            $statsData = [
                ['Total Karyawan', $stats['total_karyawan']],
                ['Karyawan Aktif', $stats['karyawan_aktif']],
                ['- Tetap', $stats['karyawan_tetap']],
                ['- Kontrak', $stats['karyawan_kontrak']],
                ['- Probation', $stats['karyawan_probation']],
                ['Karyawan Magang', $stats['karyawan_magang']],
                ['Karyawan Keluar', $stats['karyawan_keluar']],
                ['Tanpa Rekening', $stats['karyawan_tanpa_rekening']],
                ['Tanpa NPWP', $stats['karyawan_tanpa_npwp']],
                ['Tanpa BPJS Kesehatan', $stats['karyawan_tanpa_bpjs_kes']],
                ['Tanpa BPJS TK', $stats['karyawan_tanpa_bpjs_tk']]
            ];
            
            $rowStat = 3;
            foreach ($statsData as $stat) {
                $sheet2->setCellValue('A' . $rowStat, $stat[0]);
                $sheet2->setCellValue('B' . $rowStat, $stat[1]);
                $rowStat++;
            }
            
            // Auto-size columns sheet 2
            foreach (range('A', 'C') as $col) {
                $sheet2->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Set active sheet ke sheet pertama
            $spreadsheet->setActiveSheetIndex(0);
            
            // Output file
            $filename = 'Data_Karyawan_Penggajian_' . date('Ymd_His') . '.xlsx';
            
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
    private function exportPdf($data, $filters, $stats)
    {
        try {
            // Buat HTML untuk PDF
            $html = $this->generatePdfHtml($data, $filters, $stats);
            
            // Konfigurasi Dompdf
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);
            $options->set('defaultFont', 'Arial');
            $options->set('isRemoteEnabled', true);
            
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            
            // Output PDF
            $filename = 'Data_Karyawan_Penggajian_' . date('Ymd_His') . '.pdf';
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
    private function generatePdfHtml($data, $filters, $stats)
    {
        // Filter info
        $filterText = '';
        if (!empty($filters['status_karyawan'])) {
            $statusLabel = $filters['status_karyawan'] == 'aktif' ? 'Karyawan Aktif' : 
                          ($filters['status_karyawan'] == 'nonaktif' ? 'Karyawan Non-Aktif' : 
                          ($filters['status_karyawan'] == 'keluar' ? 'Karyawan Keluar' : $filters['status_karyawan']));
            $filterText .= 'Status: ' . $statusLabel . ' | ';
        }
        if (!empty($filters['departemen'])) {
            $filterText .= 'Departemen: ' . $filters['departemen'];
        }
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Data Karyawan - Penggajian</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 8px;
                    line-height: 1.2;
                    margin: 10px;
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
                .info-section {
                    margin-bottom: 15px;
                    padding: 5px;
                    background-color: #f8f9fa;
                    border: 1px solid #dee2e6;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 15px;
                    font-size: 7px;
                }
                th {
                    background-color: #4F81BD;
                    color: white;
                    padding: 3px;
                    text-align: center;
                    font-weight: bold;
                    border: 1px solid #000;
                }
                td {
                    padding: 2px;
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
                    padding-top: 5px;
                    border-top: 1px solid #000;
                    font-size: 7px;
                }
                .summary-box {
                    display: inline-block;
                    width: 18%;
                    margin: 0 0.5%;
                    padding: 3px;
                    background-color: #f8f9fa;
                    border: 1px solid #dee2e6;
                    text-align: center;
                }
                .summary-value {
                    font-size: 9px;
                    font-weight: bold;
                }
                .summary-label {
                    font-size: 6px;
                    color: #666;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>DATA KARYAWAN - PENGGUNAAN UNTUK PENGGAJIAN</h1>
                <h2>PT. CIPTA DUTA WACANA</h2>
                <div style="font-size: 8px;">' . $filterText . '</div>
            </div>
            
            <!-- Summary Cards -->
            <div style="text-align: center; margin-bottom: 15px;">
                <div class="summary-box">
                    <div class="summary-label">Total Karyawan</div>
                    <div class="summary-value">' . number_format($stats['total_karyawan']) . '</div>
                </div>
                <div class="summary-box" style="background-color: #d4edda;">
                    <div class="summary-label">Karyawan Aktif</div>
                    <div class="summary-value">' . number_format($stats['karyawan_aktif']) . '</div>
                </div>
                <div class="summary-box" style="background-color: #fff3cd;">
                    <div class="summary-label">Magang</div>
                    <div class="summary-value">' . number_format($stats['karyawan_magang']) . '</div>
                </div>
                <div class="summary-box" style="background-color: #f8d7da;">
                    <div class="summary-label">Keluar</div>
                    <div class="summary-value">' . number_format($stats['karyawan_keluar']) . '</div>
                </div>
                <div class="summary-box" style="background-color: #cce5ff;">
                    <div class="summary-label">Tanpa Rekening</div>
                    <div class="summary-value">' . number_format($stats['karyawan_tanpa_rekening']) . '</div>
                </div>
            </div>';
            
            // Tabel Data Karyawan
            $html .= '
            <table>
                <thead>
                    <tr>
                        <th width="2%">No</th>
                        <th width="5%">NIK</th>
                        <th width="8%">Nama</th>
                        <th width="3%">JK</th>
                        <th width="5%">Jabatan</th>
                        <th width="5%">Departemen</th>
                        <th width="5%">Tgl Masuk</th>
                        <th width="5%">Status</th>
                        <th width="5%">Bank</th>
                        <th width="6%">No Rekening</th>
                        <th width="6%">Nama Rekening</th>
                        <th width="5%">NPWP</th>
                        <th width="5%">BPJS Kes</th>
                        <th width="5%">BPJS TK</th>
                        <th width="5%">Telepon</th>
                        <th width="5%">Email</th>
                    </tr>
                </thead>
                <tbody>';
            
            if (empty($data)) {
                $html .= '
                    <tr>
                        <td colspan="16" class="text-center">Tidak ada data karyawan</td>
                    </tr>';
            } else {
                $no = 1;
                foreach ($data as $item) {
                    $html .= '
                    <tr>
                        <td class="text-center">' . $no . '</td>
                        <td>' . $item['NIK'] . '</td>
                        <td>' . $item['Nama Lengkap'] . '</td>
                        <td class="text-center">' . $item['Jenis Kelamin'] . '</td>
                        <td>' . $item['Jabatan'] . '</td>
                        <td>' . $item['Departemen'] . '</td>
                        <td class="text-center">' . $item['Tanggal Masuk'] . '</td>
                        <td class="text-center">' . $item['Status Karyawan'] . '</td>
                        <td>' . $item['Bank'] . '</td>
                        <td>' . $item['No Rekening'] . '</td>
                        <td>' . $item['Nama Rekening'] . '</td>
                        <td>' . $item['NPWP'] . '</td>
                        <td>' . $item['BPJS Kesehatan'] . '</td>
                        <td>' . $item['BPJS Ketenagakerjaan'] . '</td>
                        <td>' . $item['Telepon'] . '</td>
                        <td>' . $item['Email'] . '</td>
                    </tr>';
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
                            <strong>Total Data:</strong> ' . count($data) . ' karyawan<br>
                            <strong>Karyawan Aktif:</strong> ' . $stats['karyawan_aktif'] . ' | 
                            <strong>Tanpa Rekening:</strong> ' . $stats['karyawan_tanpa_rekening'] . '
                        </td>
                        <td style="border: none; text-align: right;">
                            Dicetak pada: ' . date('d/m/Y H:i:s') . '<br>
                            Oleh: ' . session()->get('name') . '
                        </td>
                    </tr>
                </table>
            </div>
            
        </body>
        </html>';
        
        return $html;
    }

    /**
     * Get data untuk dropdown Select2
     */
    public function getSelect2()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([]);
        }
        
        $search = $this->request->getGet('q');
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 20;
        
        $builder = $this->karyawanModel
            ->select('id, nik, nama_lengkap, jabatan, departemen')
            ->whereIn('status_karyawan', ['Tetap', 'Kontrak', 'Probation'])
            ->where('deleted_at IS NULL');
        
        if (!empty($search)) {
            $builder->groupStart()
                ->like('nik', $search)
                ->orLike('nama_lengkap', $search)
                ->orLike('jabatan', $search)
                ->groupEnd();
        }
        
        $total = $builder->countAllResults(false);
        $offset = ($page - 1) * $perPage;
        $results = $builder->orderBy('nama_lengkap', 'ASC')
            ->limit($perPage, $offset)
            ->findAll();
        
        $items = [];
        foreach ($results as $row) {
            $items[] = [
                'id' => $row['id'],
                'text' => $row['nik'] . ' - ' . $row['nama_lengkap'] . ' (' . $row['jabatan'] . ')'
            ];
        }
        
        return $this->response->setJSON([
            'results' => $items,
            'pagination' => [
                'more' => ($page * $perPage) < $total
            ]
        ]);
    }

    /**
     * Get data karyawan by ID (JSON)
     */
    public function getJson($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([]);
        }
        
        $karyawan = $this->karyawanModel
            ->select('id, nik, nama_lengkap, jabatan, departemen, bank, no_rekening, nama_rekening')
            ->find($id);
        
        if (!$karyawan) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $karyawan
        ]);
    }

    /**
     * Update data bank karyawan
     */
    public function updateBank($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('accounting/penggajian/data-karyawan');
        }
        
        $rules = [
            'bank' => 'required',
            'no_rekening' => 'required',
            'nama_rekening' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }
        
        $data = [
            'bank' => $this->request->getPost('bank'),
            'no_rekening' => $this->request->getPost('no_rekening'),
            'nama_rekening' => $this->request->getPost('nama_rekening')
        ];
        
        try {
            $this->karyawanModel->update($id, $data);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data bank karyawan berhasil diperbarui'
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memperbarui data bank: ' . $e->getMessage()
            ]);
        }
    }
}