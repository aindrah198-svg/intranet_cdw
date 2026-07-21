<?php
namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\Accounting\ArsipPajakModel;
use App\Models\Accounting\TarifPajakModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Dompdf\Dompdf;
use Dompdf\Options;

class ArsipPajak extends BaseController
{
    protected $arsipPajakModel;
    protected $tarifPajakModel;
    protected $db;

    public function __construct()
    {
        $this->arsipPajakModel = new ArsipPajakModel();
        $this->tarifPajakModel = new TarifPajakModel();
        $this->db = \Config\Database::connect();
        
        helper(['form', 'url', 'text', 'number', 'filesystem']);
        
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
    }

    /**
     * Index / Daftar Arsip Pajak
     */
    public function index()
    {
        $data['title'] = 'Daftar Arsip Pajak';
        
        $filters = [
            'search' => $this->request->getGet('search'),
            'jenis_pajak' => $this->request->getGet('jenis_pajak'),
            'tahun' => $this->request->getGet('tahun'),
            'bulan' => $this->request->getGet('bulan'),
            'tanggal_mulai' => $this->request->getGet('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getGet('tanggal_selesai')
        ];
        
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 20;
        
        $result = $this->arsipPajakModel->getAllWithFilters($filters, $perPage, $page);
        
        $data['arsip'] = $result['data'];
        $data['pager'] = $this->arsipPajakModel->pager;
        $data['total'] = $result['total'];
        $data['perPage'] = $perPage;
        $data['currentPage'] = $page;
        $data['totalPages'] = $result['total_pages'];
        
        $data['jenisPajakOptions'] = ['PPN', 'PPh 21', 'PPh 23', 'PPh 25', 'PPh 29', 'PPh Badan', 'Lainnya'];
        $data['tahunOptions'] = $this->getTahunOptions();
        $data['bulanOptions'] = $this->getBulanOptions();
        
        $data['stats'] = $this->arsipPajakModel->getStats();
        $data['ringkasanPerJenis'] = $this->arsipPajakModel->getRingkasanPerJenis();
        
        $data['totalStorage'] = $this->arsipPajakModel->getTotalStorageUsed();
        
        return view('accounting/manajemen-pajak/arsip-pajak/index', $data);
    }

    /**
     * Form upload arsip pajak
     */
    public function upload()
    {
        $data['title'] = 'Upload Arsip Pajak';
        $data['validation'] = \Config\Services::validation();
        
        $data['jenisPajakOptions'] = ['PPN', 'PPh 21', 'PPh 23', 'PPh 25', 'PPh 29', 'PPh Badan', 'Lainnya'];
        $data['bulanOptions'] = $this->getBulanOptions();
        $data['tahunOptions'] = $this->getTahunOptions();
        
        $data['arsip'] = [
            'jenis_pajak' => 'PPN',
            'masa_pajak' => date('m'),
            'tahun_pajak' => date('Y'),
            'nomor_dokumen' => '',
            'judul' => '',
            'deskripsi' => '',
            'tanggal_dokumen' => date('Y-m-d')
        ];
        
        return view('accounting/manajemen-pajak/arsip-pajak/upload', $data);
    }

    /**
     * Simpan arsip pajak yang diupload
     */
    public function storeUpload()
    {
        $rules = [
            'jenis_pajak' => 'required|in_list[PPN,PPh 21,PPh 23,PPh 25,PPh 29,PPh Badan,Lainnya]',
            'tahun_pajak' => 'required|numeric|min_length[4]|max_length[4]',
            'judul' => 'required|min_length[3]',
            'tanggal_dokumen' => 'required|valid_date',
            'file_arsip' => 'uploaded[file_arsip]|max_size[file_arsip,10240]|ext_in[file_arsip,pdf,jpg,jpeg,png,doc,docx,xls,xlsx]'
        ];
        
        $jenisPajak = $this->request->getPost('jenis_pajak');
        if ($jenisPajak !== 'PPh Badan') {
            $rules['masa_pajak'] = 'required|numeric|greater_than[0]|less_than_equal_to[12]';
        }
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $file = $this->request->getFile('file_arsip');
        
        if (!$file->isValid()) {
            return redirect()->back()->withInput()
                ->with('error', 'File tidak valid');
        }
        
        $data = [
            'jenis_pajak' => $jenisPajak,
            'tahun_pajak' => $this->request->getPost('tahun_pajak'),
            'nomor_dokumen' => $this->request->getPost('nomor_dokumen'),
            'judul' => $this->request->getPost('judul'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'tanggal_dokumen' => $this->request->getPost('tanggal_dokumen')
        ];
        
        if ($jenisPajak !== 'PPh Badan') {
            $data['masa_pajak'] = $this->request->getPost('masa_pajak');
        }
        
        try {
            $this->db->transBegin();
            
            $result = $this->arsipPajakModel->uploadArsip($file, $data);
            
            if (!$result) {
                throw new \Exception('Gagal mengupload arsip: ' . json_encode($this->arsipPajakModel->errors()));
            }
            
            $this->db->transCommit();
            
            return redirect()->to('accounting/manajemen-pajak/arsip-pajak/detail/' . $result['id'])
                ->with('success', 'Arsip pajak berhasil diupload.');
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal mengupload arsip pajak: ' . $e->getMessage());
        }
    }

    /**
     * Detail arsip pajak
     */
    public function detail($id)
    {
        $data['title'] = 'Detail Arsip Pajak';
        
        $arsip = $this->arsipPajakModel->getWithDetails($id);
        
        if (!$arsip) {
            return redirect()->to('accounting/manajemen-pajak/arsip-pajak')
                ->with('error', 'Arsip pajak tidak ditemukan');
        }
        
        $data['arsip'] = $arsip;
        
        return view('accounting/manajemen-pajak/arsip-pajak/detail', $data);
    }

    /**
     * Download arsip pajak
     */
    public function download($id)
    {
        $arsip = $this->arsipPajakModel->find($id);
        
        if (!$arsip) {
            return redirect()->to('accounting/manajemen-pajak/arsip-pajak')
                ->with('error', 'Arsip pajak tidak ditemukan');
        }
        
        try {
            $file = $this->arsipPajakModel->downloadArsip($id);
            
            return $this->response->download($file['path'], null)
                ->setFileName($file['name']);
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Hapus arsip pajak
     */
    public function delete($id)
    {
        $isAjax = $this->request->isAJAX();
        
        $arsip = $this->arsipPajakModel->find($id);
        
        if (!$arsip) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Arsip pajak tidak ditemukan'
                ]);
            } else {
                return redirect()->to('accounting/manajemen-pajak/arsip-pajak')
                    ->with('error', 'Arsip pajak tidak ditemukan');
            }
        }
        
        try {
            $this->db->transBegin();
            
            $deleted = $this->arsipPajakModel->deleteArsip($id);
            
            if (!$deleted) {
                throw new \Exception('Gagal menghapus arsip');
            }
            
            $this->db->transCommit();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Arsip pajak berhasil dihapus',
                    'redirect' => site_url('accounting/manajemen-pajak/arsip-pajak')
                ]);
            } else {
                return redirect()->to('accounting/manajemen-pajak/arsip-pajak')
                    ->with('success', 'Arsip pajak berhasil dihapus');
            }
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus arsip pajak: ' . $e->getMessage()
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal menghapus arsip pajak: ' . $e->getMessage());
            }
        }
    }

    /**
     * Filter data
     */
    public function filter()
    {
        $filters = [
            'jenis_pajak' => $this->request->getGet('jenis_pajak'),
            'tahun' => $this->request->getGet('tahun'),
            'bulan' => $this->request->getGet('bulan'),
            'tanggal_mulai' => $this->request->getGet('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getGet('tanggal_selesai')
        ];
        
        session()->set('filter_arsip_pajak', $filters);
        
        return redirect()->to('accounting/manajemen-pajak/arsip-pajak');
    }

    /**
     * Search data
     */
    public function search()
    {
        $search = $this->request->getGet('q');
        
        $filters = session()->get('filter_arsip_pajak') ?? [];
        $filters['search'] = $search;
        
        session()->set('filter_arsip_pajak', $filters);
        
        return redirect()->to('accounting/manajemen-pajak/arsip-pajak');
    }

    /**
     * Reset filter
     */
    public function resetFilter()
    {
        session()->remove('filter_arsip_pajak');
        
        return redirect()->to('accounting/manajemen-pajak/arsip-pajak');
    }

    /**
     * Filter by jenis PPN
     */
    public function ppn()
    {
        $filters = ['jenis_pajak' => 'PPN'];
        session()->set('filter_arsip_pajak', $filters);
        
        return redirect()->to('accounting/manajemen-pajak/arsip-pajak');
    }

    /**
     * Filter by jenis PPh
     */
    public function pph()
    {
        $filters = ['jenis_pajak' => 'PPh 21'];
        session()->set('filter_arsip_pajak', $filters);
        
        return redirect()->to('accounting/manajemen-pajak/arsip-pajak');
    }

    /**
     * Filter by jenis lainnya
     */
    public function lainnya()
    {
        $filters = ['jenis_pajak' => 'Lainnya'];
        session()->set('filter_arsip_pajak', $filters);
        
        return redirect()->to('accounting/manajemen-pajak/arsip-pajak');
    }

    /**
     * Export data
     */
    public function export()
    {
        $type = $this->request->getGet('type') ?? 'excel';
        $filters = [
            'jenis_pajak' => $this->request->getGet('jenis_pajak'),
            'tahun' => $this->request->getGet('tahun')
        ];
        
        $data = $this->arsipPajakModel->getExportData($filters);
        
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
                ->setTitle("Daftar Arsip Pajak")
                ->setSubject("Daftar Arsip Pajak")
                ->setDescription("Daftar Arsip Pajak " . date('d-m-Y'));
            
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Daftar Arsip Pajak');
            
            // Header laporan
            $sheet->mergeCells('A1:K1');
            $sheet->setCellValue('A1', 'DAFTAR ARSIP PAJAK');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->mergeCells('A2:K2');
            $sheet->setCellValue('A2', 'PT. CIPTA DUTA WACANA');
            $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->mergeCells('A3:K3');
            $sheet->setCellValue('A3', 'Dicetak: ' . date('d/m/Y H:i:s'));
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Baris kosong
            $startRow = 5;
            
            // Header tabel
            $headers = [
                'A' => 'No',
                'B' => 'Kode Arsip',
                'C' => 'Jenis Pajak',
                'D' => 'Masa Pajak',
                'E' => 'Nomor Dokumen',
                'F' => 'Judul',
                'G' => 'Tanggal Dokumen',
                'H' => 'Tipe File',
                'I' => 'Ukuran File',
                'J' => 'Dibuat Oleh',
                'K' => 'Dibuat Tanggal'
            ];
            
            foreach ($headers as $col => $header) {
                $sheet->setCellValue($col . $startRow, $header);
            }
            
            // Style header
            $headerRange = 'A' . $startRow . ':K' . $startRow;
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
                $sheet->setCellValue('B' . $row, $item['Kode Arsip']);
                $sheet->setCellValue('C' . $row, $item['Jenis Pajak']);
                $sheet->setCellValue('D' . $row, $item['Masa Pajak']);
                $sheet->setCellValue('E' . $row, $item['Nomor Dokumen']);
                $sheet->setCellValue('F' . $row, $item['Judul']);
                $sheet->setCellValue('G' . $row, $item['Tanggal Dokumen']);
                $sheet->setCellValue('H' . $row, $item['Tipe File']);
                $sheet->setCellValue('I' . $row, $item['Ukuran File']);
                $sheet->setCellValue('J' . $row, $item['Dibuat Oleh']);
                $sheet->setCellValue('K' . $row, $item['Dibuat Tanggal']);
                
                $row++;
                $no++;
            }
            
            // Auto-size columns
            foreach (range('A', 'K') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Border untuk seluruh data
            $lastRow = $row - 1;
            if ($lastRow >= $startRow) {
                $dataRange = 'A' . $startRow . ':K' . $lastRow;
                $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            }
            
            // Output file
            $filename = 'Daftar_Arsip_Pajak_' . date('Ymd_His') . '.xlsx';
            
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
            
            $filename = 'Daftar_Arsip_Pajak_' . date('Ymd_His') . '.pdf';
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
        if (!empty($filters['jenis_pajak'])) {
            $filterText .= 'Jenis: ' . $filters['jenis_pajak'] . ' | ';
        }
        if (!empty($filters['tahun'])) {
            $filterText .= 'Tahun: ' . $filters['tahun'];
        }
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Daftar Arsip Pajak</title>
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
                <h1>DAFTAR ARSIP PAJAK</h1>
                <h2>PT. CIPTA DUTA WACANA</h2>
                <div>Dicetak: ' . date('d/m/Y H:i:s') . '</div>
                ' . (!empty($filterText) ? '<div>' . $filterText . '</div>' : '') . '
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th width="3%">No</th>
                        <th width="8%">Kode Arsip</th>
                        <th width="8%">Jenis Pajak</th>
                        <th width="6%">Masa</th>
                        <th width="8%">No Dokumen</th>
                        <th width="15%">Judul</th>
                        <th width="8%">Tgl Dokumen</th>
                        <th width="8%">Tipe File</th>
                        <th width="6%">Ukuran</th>
                        <th width="8%">Dibuat Oleh</th>
                        <th width="10%">Dibuat Tanggal</th>
                    </tr>
                </thead>
                <tbody>';
        
        if (empty($data)) {
            $html .= '<tr><td colspan="11" class="text-center">Tidak ada data arsip pajak</td></tr>';
        } else {
            $no = 1;
            foreach ($data as $item) {
                $html .= '
                    <tr>
                        <td class="text-center">' . $no . '</td>
                        <td>' . $item['Kode Arsip'] . '</td>
                        <td>' . $item['Jenis Pajak'] . '</td>
                        <td>' . $item['Masa Pajak'] . '</td>
                        <td>' . $item['Nomor Dokumen'] . '</td>
                        <td>' . $item['Judul'] . '</td>
                        <td class="text-center">' . $item['Tanggal Dokumen'] . '</td>
                        <td>' . $item['Tipe File'] . '</td>
                        <td>' . $item['Ukuran File'] . '</td>
                        <td>' . $item['Dibuat Oleh'] . '</td>
                        <td>' . $item['Dibuat Tanggal'] . '</td>
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
                            <strong>Total Data:</strong> ' . count($data) . ' arsip
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
     * AJAX: Get total storage used
     */
    public function ajaxGetTotalStorage()
    {
        $storage = $this->arsipPajakModel->getTotalStorageUsed();
        
        return $this->response->setJSON([
            'success' => true,
            'storage' => $storage
        ]);
    }

    /**
     * AJAX: Get ringkasan per jenis
     */
    public function ajaxGetRingkasanPerJenis()
    {
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        
        $ringkasan = $this->arsipPajakModel->getRingkasanPerJenis($tahun);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $ringkasan,
            'tahun' => $tahun
        ]);
    }

    /**
     * AJAX: Get ringkasan per tahun
     */
    public function ajaxGetRingkasanPerTahun()
    {
        $ringkasan = $this->arsipPajakModel->getRingkasanPerTahun();
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $ringkasan
        ]);
    }

    /**
     * AJAX: Get latest arsip
     */
    public function ajaxGetLatest()
    {
        $limit = $this->request->getGet('limit') ?? 10;
        
        $latest = $this->arsipPajakModel->getLatest($limit);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $latest
        ]);
    }

    /**
     * Get tahun options untuk dropdown
     */
    private function getTahunOptions()
    {
        $tahunSekarang = date('Y');
        $options = [];
        
        for ($i = $tahunSekarang - 5; $i <= $tahunSekarang + 1; $i++) {
            $options[] = $i;
        }
        
        return array_reverse($options);
    }

    /**
     * Get bulan options untuk dropdown
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
}