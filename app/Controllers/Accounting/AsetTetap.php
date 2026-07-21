<?php
namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\Accounting\AsetTetapModel;
use App\Models\Accounting\AsetTetapKategoriModel;
use App\Models\Accounting\PenyusutanModel;
use App\Models\Accounting\PelepasanAsetModel;
use App\Models\Accounting\MutasiAsetModel;
use App\Models\CoaModel;
use App\Models\KaryawanModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Dompdf\Dompdf;
use Dompdf\Options;

class AsetTetap extends BaseController
{
    protected $asetModel;
    protected $kategoriModel;
    protected $penyusutanModel;
    protected $pelepasanModel;
    protected $mutasiModel;
    protected $coaModel;
    protected $karyawanModel;
    protected $db;

    public function __construct()
    {
        $this->asetModel = new AsetTetapModel();
        $this->kategoriModel = new AsetTetapKategoriModel();
        $this->penyusutanModel = new PenyusutanModel();
        $this->pelepasanModel = new PelepasanAsetModel();
        $this->mutasiModel = new MutasiAsetModel();
        $this->coaModel = new CoaModel();
        $this->karyawanModel = new KaryawanModel();
        $this->db = \Config\Database::connect();
        
        helper(['form', 'url', 'text', 'number']);
        
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
    }

    /**
     * Dashboard Aset Tetap
     */
    public function index()
    {
        $data['title'] = 'Dashboard Aset Tetap';
        
        // Statistik
        $data['stats'] = $this->asetModel->getStats();
        
        // Rekap per kategori
        $data['rekapPerKategori'] = $this->asetModel->getRekapPerKategori();
        
        // Rekap per departemen
        $data['rekapPerDepartemen'] = $this->asetModel->getRekapPerDepartemen();
        
        // Aset terbaru
        $filters = ['limit' => 10];
        $asetTerbaru = $this->asetModel->getAllWithFilters($filters, 10, 1);
        $data['asetTerbaru'] = $asetTerbaru['data'];
        
        // Aset akan habis masa manfaat
        $data['asetAkanHabis'] = $this->asetModel->getAsetAkanHabis(12);
        
        // Total nilai buku per kategori
        $data['rekapPenyusutan'] = $this->kategoriModel->getRekapPenyusutan();
        
        // Statistik penyusutan
        $data['statsPenyusutan'] = $this->penyusutanModel->getStats(date('Y'));
        
        // Statistik pelepasan
        $data['statsPelepasan'] = $this->pelepasanModel->getStats(date('Y'));
        
        // Statistik mutasi
        $data['statsMutasi'] = $this->mutasiModel->getStats(date('Y'));
        
        return view('accounting/aset-tetap/dashboard', $data);
    }

    /**
     * Daftar Aset Tetap
     */
    public function daftar()
    {
        $data['title'] = 'Daftar Aset Tetap';
        
        $filters = [
            'search' => $this->request->getGet('search'),
            'kategori_id' => $this->request->getGet('kategori_id'),
            'status' => $this->request->getGet('status'),
            'kondisi' => $this->request->getGet('kondisi'),
            'lokasi' => $this->request->getGet('lokasi'),
            'departemen' => $this->request->getGet('departemen'),
            'penanggung_jawab' => $this->request->getGet('penanggung_jawab'),
            'tanggal_perolehan_mulai' => $this->request->getGet('tanggal_perolehan_mulai'),
            'tanggal_perolehan_selesai' => $this->request->getGet('tanggal_perolehan_selesai'),
            'min_harga' => $this->request->getGet('min_harga'),
            'max_harga' => $this->request->getGet('max_harga')
        ];
        
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 20;
        
        $result = $this->asetModel->getAllWithFilters($filters, $perPage, $page);
        
        $data['aset'] = $result['data'];
        $data['pager'] = $this->asetModel->pager;
        $data['total'] = $result['total'];
        $data['perPage'] = $perPage;
        $data['currentPage'] = $page;
        $data['totalPages'] = $result['total_pages'];
        
        // Options untuk filter
        $data['kategoriOptions'] = $this->kategoriModel->getActiveOptions();
        $data['statusOptions'] = ['Aktif', 'Rusak', 'Dilepas', 'Dalam Perbaikan', 'Dihapus'];
        $data['kondisiOptions'] = ['Baik', 'Rusak Ringan', 'Rusak Berat', 'Perlu Perbaikan'];
        $data['lokasiOptions'] = $this->asetModel->getLokasiOptions();
        $data['departemenOptions'] = $this->getDepartemenOptions();
        $data['karyawanOptions'] = $this->karyawanModel->select('id, nik, nama_lengkap')->findAll();
        
        $data['stats'] = $this->asetModel->getStats();
        
        return view('accounting/aset-tetap/daftar', $data);
    }

    /**
     * Detail Aset Tetap
     */
    public function detail($id)
    {
        $data['title'] = 'Detail Aset Tetap';
        
        $aset = $this->asetModel->getWithDetails($id);
        
        if (!$aset) {
            return redirect()->to('accounting/aset-tetap/daftar')
                ->with('error', 'Aset tetap tidak ditemukan');
        }
        
        $aset['harga_perolehan_formatted'] = $this->formatRupiah($aset['harga_perolehan']);
        $aset['nilai_residu_formatted'] = $this->formatRupiah($aset['nilai_residu']);
        $aset['akumulasi_penyusutan_formatted'] = $this->formatRupiah($aset['akumulasi_penyusutan']);
        $aset['nilai_buku_formatted'] = $this->formatRupiah($aset['nilai_buku']);
        
        $data['aset'] = $aset;
        
        return view('accounting/aset-tetap/detail', $data);
    }

    /**
     * Filter data daftar aset
     */
    public function filter()
    {
        $filters = [
            'kategori_id' => $this->request->getGet('kategori_id'),
            'status' => $this->request->getGet('status'),
            'kondisi' => $this->request->getGet('kondisi'),
            'lokasi' => $this->request->getGet('lokasi'),
            'departemen' => $this->request->getGet('departemen'),
            'penanggung_jawab' => $this->request->getGet('penanggung_jawab'),
            'tanggal_perolehan_mulai' => $this->request->getGet('tanggal_perolehan_mulai'),
            'tanggal_perolehan_selesai' => $this->request->getGet('tanggal_perolehan_selesai'),
            'min_harga' => $this->request->getGet('min_harga'),
            'max_harga' => $this->request->getGet('max_harga')
        ];
        
        session()->set('filter_aset_tetap', $filters);
        
        return redirect()->to('accounting/aset-tetap/daftar');
    }

    /**
     * Search data
     */
    public function search()
    {
        $search = $this->request->getGet('q');
        
        $filters = session()->get('filter_aset_tetap') ?? [];
        $filters['search'] = $search;
        
        session()->set('filter_aset_tetap', $filters);
        
        return redirect()->to('accounting/aset-tetap/daftar');
    }

    /**
     * Reset filter
     */
    public function resetFilter()
    {
        session()->remove('filter_aset_tetap');
        
        return redirect()->to('accounting/aset-tetap/daftar');
    }

    /**
     * Export data
     */
    public function export()
    {
        $type = $this->request->getGet('type') ?? 'excel';
        $filters = [
            'kategori_id' => $this->request->getGet('kategori_id'),
            'status' => $this->request->getGet('status'),
            'tanggal_perolehan_mulai' => $this->request->getGet('tanggal_perolehan_mulai'),
            'tanggal_perolehan_selesai' => $this->request->getGet('tanggal_perolehan_selesai')
        ];
        
        $data = $this->asetModel->getExportData($filters);
        
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
                ->setTitle("Daftar Aset Tetap")
                ->setSubject("Daftar Aset Tetap")
                ->setDescription("Daftar Aset Tetap " . date('d-m-Y'));
            
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Daftar Aset Tetap');
            
            // Header laporan
            $sheet->mergeCells('A1:P1');
            $sheet->setCellValue('A1', 'DAFTAR ASET TETAP');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->mergeCells('A2:P2');
            $sheet->setCellValue('A2', 'PT. CIPTA DUTA WACANA');
            $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->mergeCells('A3:P3');
            $sheet->setCellValue('A3', 'Dicetak: ' . date('d/m/Y H:i:s'));
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Baris kosong
            $startRow = 5;
            
            // Header tabel
            $headers = [
                'A' => 'No',
                'B' => 'Kode Aset',
                'C' => 'Nama Aset',
                'D' => 'Kategori',
                'E' => 'Merk',
                'F' => 'Serial Number',
                'G' => 'Tanggal Perolehan',
                'H' => 'Harga Perolehan',
                'I' => 'Nilai Residu',
                'J' => 'Akumulasi',
                'K' => 'Nilai Buku',
                'L' => 'Lokasi',
                'M' => 'Departemen',
                'N' => 'Penanggung Jawab',
                'O' => 'Status',
                'P' => 'Kondisi'
            ];
            
            foreach ($headers as $col => $header) {
                $sheet->setCellValue($col . $startRow, $header);
            }
            
            // Style header
            $headerRange = 'A' . $startRow . ':P' . $startRow;
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
                $sheet->setCellValue('D' . $row, $item['Kategori']);
                $sheet->setCellValue('E' . $row, $item['Merk']);
                $sheet->setCellValue('F' . $row, $item['Serial Number']);
                $sheet->setCellValue('G' . $row, $item['Tanggal Perolehan']);
                $sheet->setCellValue('H' . $row, $item['Harga Perolehan']);
                $sheet->setCellValue('I' . $row, $item['Nilai Residu']);
                $sheet->setCellValue('J' . $row, $item['Akumulasi Penyusutan']);
                $sheet->setCellValue('K' . $row, $item['Nilai Buku']);
                $sheet->setCellValue('L' . $row, $item['Lokasi']);
                $sheet->setCellValue('M' . $row, $item['Departemen']);
                $sheet->setCellValue('N' . $row, $item['Penanggung Jawab']);
                $sheet->setCellValue('O' . $row, $item['Status']);
                $sheet->setCellValue('P' . $row, $item['Kondisi']);
                
                // Format angka
                $sheet->getStyle('H' . $row . ':K' . $row)->getNumberFormat()
                    ->setFormatCode('"Rp" #,##0.00');
                
                // Warna status
                if ($item['Status'] == 'Aktif') {
                    $sheet->getStyle('O' . $row)->getFont()->getColor()->setARGB('FF008000');
                } elseif ($item['Status'] == 'Dilepas') {
                    $sheet->getStyle('O' . $row)->getFont()->getColor()->setARGB('FFFF0000');
                } else {
                    $sheet->getStyle('O' . $row)->getFont()->getColor()->setARGB('FFFFA500');
                }
                
                $row++;
                $no++;
            }
            
            // Auto-size columns
            foreach (range('A', 'P') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Border untuk seluruh data
            $lastRow = $row - 1;
            if ($lastRow >= $startRow) {
                $dataRange = 'A' . $startRow . ':P' . $lastRow;
                $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            }
            
            // Output file
            $filename = 'Daftar_Aset_Tetap_' . date('Ymd_His') . '.xlsx';
            
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
            
            $filename = 'Daftar_Aset_Tetap_' . date('Ymd_His') . '.pdf';
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
        if (!empty($filters['kategori_id'])) {
            $kategori = $this->kategoriModel->find($filters['kategori_id']);
            $filterText .= 'Kategori: ' . ($kategori['nama_kategori'] ?? '-') . ' | ';
        }
        if (!empty($filters['status'])) {
            $filterText .= 'Status: ' . $filters['status'];
        }
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Daftar Aset Tetap</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 8px;
                    margin: 10px;
                }
                h1 {
                    text-align: center;
                    font-size: 14px;
                    margin-bottom: 5px;
                }
                h2 {
                    text-align: center;
                    font-size: 10px;
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
                    padding: 4px;
                    text-align: center;
                    font-weight: bold;
                    border: 1px solid #000;
                    font-size: 7px;
                }
                td {
                    padding: 3px;
                    border: 1px solid #000;
                    vertical-align: top;
                    font-size: 7px;
                }
                .text-end {
                    text-align: right;
                }
                .text-center {
                    text-align: center;
                }
                .text-success {
                    color: #28a745;
                    font-weight: bold;
                }
                .text-danger {
                    color: #dc3545;
                    font-weight: bold;
                }
                .text-warning {
                    color: #ffc107;
                    font-weight: bold;
                }
                .footer {
                    margin-top: 15px;
                    padding-top: 8px;
                    border-top: 1px solid #000;
                    font-size: 7px;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>DAFTAR ASET TETAP</h1>
                <h2>PT. CIPTA DUTA WACANA</h2>
                <div>Dicetak: ' . date('d/m/Y H:i:s') . '</div>
                ' . (!empty($filterText) ? '<div>' . $filterText . '</div>' : '') . '
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th width="2%">No</th>
                        <th width="6%">Kode</th>
                        <th width="12%">Nama Aset</th>
                        <th width="8%">Kategori</th>
                        <th width="8%">Tgl Perolehan</th>
                        <th width="7%">Harga</th>
                        <th width="6%">Nilai Buku</th>
                        <th width="8%">Lokasi</th>
                        <th width="8%">Departemen</th>
                        <th width="10%">Penanggung Jawab</th>
                        <th width="6%">Status</th>
                        <th width="5%">Kondisi</th>
                    </tr>
                </thead>
                <tbody>';
        
        if (empty($data)) {
            $html .= '<tr><td colspan="12" class="text-center">Tidak ada data aset tetap</td></tr>';
        } else {
            $no = 1;
            foreach ($data as $item) {
                $statusClass = match($item['Status']) {
                    'Aktif' => 'text-success',
                    'Dilepas' => 'text-danger',
                    default => 'text-warning'
                };
                
                $html .= '
                    <tr>
                        <td class="text-center">' . $no . '</td>
                        <td>' . $item['Kode Aset'] . '</td>
                        <td>' . substr($item['Nama Aset'], 0, 30) . '</td>
                        <td>' . $item['Kategori'] . '</td>
                        <td class="text-center">' . $item['Tanggal Perolehan'] . '</td>
                        <td class="text-end">' . number_format($item['Harga Perolehan'], 0) . '</td>
                        <td class="text-end">' . number_format($item['Nilai Buku'], 0) . '</td>
                        <td>' . substr($item['Lokasi'] ?? '-', 0, 15) . '</td>
                        <td>' . ($item['Departemen'] ?? '-') . '</td>
                        <td>' . substr($item['Penanggung Jawab'] ?? '-', 0, 15) . '</td>
                        <td class="text-center ' . $statusClass . '">' . $item['Status'] . '</td>
                        <td class="text-center">' . $item['Kondisi'] . '</td>
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
                            <strong>Total Data:</strong> ' . count($data) . ' aset
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
     * Get ringkasan per kategori (AJAX)
     */
    public function ajaxGetRingkasanPerKategori()
    {
        $ringkasan = $this->asetModel->getRekapPerKategori();
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $ringkasan
        ]);
    }

    /**
     * Get ringkasan per departemen (AJAX)
     */
    public function ajaxGetRingkasanPerDepartemen()
    {
        $ringkasan = $this->asetModel->getRekapPerDepartemen();
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $ringkasan
        ]);
    }

    /**
     * Get aset akan habis (AJAX)
     */
    public function ajaxGetAsetAkanHabis()
    {
        $bulan = $this->request->getGet('bulan') ?? 12;
        
        $aset = $this->asetModel->getAsetAkanHabis($bulan);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $aset,
            'count' => count($aset)
        ]);
    }

    /**
     * Get lokasi options untuk dropdown
     */
    private function getLokasiOptions()
    {
        $asetList = $this->asetModel->findAll();
        $lokasi = array_unique(array_column($asetList, 'lokasi'));
        $lokasi = array_filter($lokasi);
        sort($lokasi);
        
        return $lokasi;
    }

    /**
     * Get departemen options
     */
    private function getDepartemenOptions()
    {
        $asetList = $this->asetModel->findAll();
        $departemen = array_unique(array_column($asetList, 'departemen'));
        $departemen = array_filter($departemen);
        sort($departemen);
        
        return $departemen;
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