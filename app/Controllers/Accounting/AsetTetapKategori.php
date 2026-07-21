<?php
namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\Accounting\AsetTetapKategoriModel;
use App\Models\CoaModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Dompdf\Dompdf;
use Dompdf\Options;

class AsetTetapKategori extends BaseController
{
    protected $kategoriModel;
    protected $coaModel;
    protected $db;

    public function __construct()
    {
        $this->kategoriModel = new AsetTetapKategoriModel();
        $this->coaModel = new CoaModel();
        $this->db = \Config\Database::connect();
        
        helper(['form', 'url', 'text', 'number']);
        
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
    }

    /**
     * Index / Daftar Kategori Aset Tetap
     */
    public function index()
    {
        $data['title'] = 'Daftar Kategori Aset Tetap';
        
        $filters = [
            'search' => $this->request->getGet('search'),
            'is_active' => $this->request->getGet('is_active'),
            'metode_penyusutan' => $this->request->getGet('metode_penyusutan')
        ];
        
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 20;
        
        $result = $this->kategoriModel->getAllWithFilters($filters, $perPage, $page);
        
        $data['kategori'] = $result['data'];
        $data['pager'] = $this->kategoriModel->pager;
        $data['total'] = $result['total'];
        $data['perPage'] = $perPage;
        $data['currentPage'] = $page;
        $data['totalPages'] = $result['total_pages'];
        
        $data['metodePenyusutanOptions'] = ['Garis Lurus', 'Saldo Menurun', 'Unit Produksi'];
        $data['statusOptions'] = ['1' => 'Aktif', '0' => 'Nonaktif'];
        
        $data['stats'] = $this->kategoriModel->getStats();
        
        return view('accounting/aset-tetap/kategori/index', $data);
    }

    /**
     * Form tambah kategori aset tetap
     */
    public function create()
    {
        $data['title'] = 'Tambah Kategori Aset Tetap';
        $data['validation'] = \Config\Services::validation();
        
        $data['metodePenyusutanOptions'] = ['Garis Lurus', 'Saldo Menurun', 'Unit Produksi'];
        $data['coaAsetOptions'] = $this->kategoriModel->getCoaAsetOptions();
        $data['coaAkumulasiOptions'] = $this->kategoriModel->getCoaAkumulasiOptions();
        $data['coaBebanOptions'] = $this->kategoriModel->getCoaBebanOptions();
        
        $data['kategori'] = [
            'kode_kategori' => '',
            'nama_kategori' => '',
            'masa_manfaat' => 5,
            'metode_penyusutan' => 'Garis Lurus',
            'persentase_penyusutan' => null,
            'coa_aset_id' => '',
            'coa_akumulasi_id' => '',
            'coa_beban_id' => '',
            'keterangan' => '',
            'is_active' => 1
        ];
        
        return view('accounting/aset-tetap/kategori/create', $data);
    }

    /**
     * Simpan kategori aset tetap baru
     */
    public function store()
    {
        $rules = [
            'nama_kategori' => 'required',
            'masa_manfaat' => 'permit_empty|is_natural|greater_than[0]',
            'metode_penyusutan' => 'required|in_list[Garis Lurus,Saldo Menurun,Unit Produksi]',
            'persentase_penyusutan' => 'permit_empty|numeric|greater_than[0]|less_than_equal_to[100]',
            'coa_aset_id' => 'permit_empty|is_natural_no_zero',
            'coa_akumulasi_id' => 'permit_empty|is_natural_no_zero',
            'coa_beban_id' => 'permit_empty|is_natural_no_zero',
            'is_active' => 'permit_empty|in_list[0,1]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'nama_kategori' => $this->request->getPost('nama_kategori'),
            'masa_manfaat' => $this->request->getPost('masa_manfaat') ?: 5,
            'metode_penyusutan' => $this->request->getPost('metode_penyusutan'),
            'persentase_penyusutan' => $this->request->getPost('persentase_penyusutan') ?: null,
            'coa_aset_id' => $this->request->getPost('coa_aset_id') ?: null,
            'coa_akumulasi_id' => $this->request->getPost('coa_akumulasi_id') ?: null,
            'coa_beban_id' => $this->request->getPost('coa_beban_id') ?: null,
            'keterangan' => $this->request->getPost('keterangan'),
            'is_active' => $this->request->getPost('is_active') ?? 1
        ];
        
        try {
            $this->db->transBegin();
            
            $saved = $this->kategoriModel->save($data);
            
            if (!$saved) {
                throw new \Exception('Gagal menyimpan data: ' . json_encode($this->kategoriModel->errors()));
            }
            
            $this->db->transCommit();
            
            return redirect()->to('accounting/aset-tetap/kategori')
                ->with('success', 'Kategori aset tetap berhasil ditambahkan.');
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal menyimpan kategori aset tetap: ' . $e->getMessage());
        }
    }

    /**
     * Detail kategori aset tetap
     */
    public function detail($id)
    {
        $data['title'] = 'Detail Kategori Aset Tetap';
        
        $kategori = $this->kategoriModel->getWithDetails($id);
        
        if (!$kategori) {
            return redirect()->to('accounting/aset-tetap/kategori')
                ->with('error', 'Kategori aset tetap tidak ditemukan');
        }
        
        // Format persentase
        if ($kategori['persentase_penyusutan']) {
            $kategori['persentase_penyusutan_formatted'] = number_format($kategori['persentase_penyusutan'], 2) . '%';
        }
        
        $data['kategori'] = $kategori;
        
        return view('accounting/aset-tetap/kategori/detail', $data);
    }

    /**
     * Form edit kategori aset tetap
     */
    public function edit($id)
    {
        $data['title'] = 'Edit Kategori Aset Tetap';
        
        $kategori = $this->kategoriModel->find($id);
        
        if (!$kategori) {
            return redirect()->to('accounting/aset-tetap/kategori')
                ->with('error', 'Kategori aset tetap tidak ditemukan');
        }
        
        $data['validation'] = \Config\Services::validation();
        $data['kategori'] = $kategori;
        
        $data['metodePenyusutanOptions'] = ['Garis Lurus', 'Saldo Menurun', 'Unit Produksi'];
        $data['coaAsetOptions'] = $this->kategoriModel->getCoaAsetOptions();
        $data['coaAkumulasiOptions'] = $this->kategoriModel->getCoaAkumulasiOptions();
        $data['coaBebanOptions'] = $this->kategoriModel->getCoaBebanOptions();
        
        return view('accounting/aset-tetap/kategori/edit', $data);
    }

    /**
     * Update kategori aset tetap
     */
    public function update($id)
    {
        $kategori = $this->kategoriModel->find($id);
        
        if (!$kategori) {
            return redirect()->to('accounting/aset-tetap/kategori')
                ->with('error', 'Kategori aset tetap tidak ditemukan');
        }
        
        $rules = [
            'nama_kategori' => 'required',
            'masa_manfaat' => 'permit_empty|is_natural|greater_than[0]',
            'metode_penyusutan' => 'required|in_list[Garis Lurus,Saldo Menurun,Unit Produksi]',
            'persentase_penyusutan' => 'permit_empty|numeric|greater_than[0]|less_than_equal_to[100]',
            'coa_aset_id' => 'permit_empty|is_natural_no_zero',
            'coa_akumulasi_id' => 'permit_empty|is_natural_no_zero',
            'coa_beban_id' => 'permit_empty|is_natural_no_zero',
            'is_active' => 'permit_empty|in_list[0,1]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'id' => $id,
            'nama_kategori' => $this->request->getPost('nama_kategori'),
            'masa_manfaat' => $this->request->getPost('masa_manfaat') ?: 5,
            'metode_penyusutan' => $this->request->getPost('metode_penyusutan'),
            'persentase_penyusutan' => $this->request->getPost('persentase_penyusutan') ?: null,
            'coa_aset_id' => $this->request->getPost('coa_aset_id') ?: null,
            'coa_akumulasi_id' => $this->request->getPost('coa_akumulasi_id') ?: null,
            'coa_beban_id' => $this->request->getPost('coa_beban_id') ?: null,
            'keterangan' => $this->request->getPost('keterangan'),
            'is_active' => $this->request->getPost('is_active') ?? 0
        ];
        
        try {
            $this->db->transBegin();
            
            $updated = $this->kategoriModel->save($data);
            
            if (!$updated) {
                throw new \Exception('Gagal mengupdate data: ' . json_encode($this->kategoriModel->errors()));
            }
            
            $this->db->transCommit();
            
            return redirect()->to('accounting/aset-tetap/kategori')
                ->with('success', 'Kategori aset tetap berhasil diupdate.');
                
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal mengupdate kategori aset tetap: ' . $e->getMessage());
        }
    }

    /**
     * Hapus kategori aset tetap
     */
    public function delete($id)
    {
        $isAjax = $this->request->isAJAX();
        
        $kategori = $this->kategoriModel->find($id);
        
        if (!$kategori) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kategori aset tetap tidak ditemukan'
                ]);
            } else {
                return redirect()->to('accounting/aset-tetap/kategori')
                    ->with('error', 'Kategori aset tetap tidak ditemukan');
            }
        }
        
        // Cek apakah bisa dihapus
        if (!$this->kategoriModel->canDelete($id)) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kategori tidak dapat dihapus karena masih memiliki aset yang terdaftar'
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Kategori tidak dapat dihapus karena masih memiliki aset yang terdaftar');
            }
        }
        
        try {
            $this->db->transBegin();
            
            $deleted = $this->kategoriModel->delete($id);
            
            if (!$deleted) {
                throw new \Exception('Gagal menghapus data');
            }
            
            $this->db->transCommit();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Kategori aset tetap berhasil dihapus',
                    'redirect' => site_url('accounting/aset-tetap/kategori')
                ]);
            } else {
                return redirect()->to('accounting/aset-tetap/kategori')
                    ->with('success', 'Kategori aset tetap berhasil dihapus');
            }
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus kategori aset tetap: ' . $e->getMessage()
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal menghapus kategori aset tetap: ' . $e->getMessage());
            }
        }
    }

    /**
     * Toggle status aktif/nonaktif
     */
    public function toggleStatus($id)
    {
        $isAjax = $this->request->isAJAX();
        
        try {
            $result = $this->kategoriModel->toggleStatus($id);
            
            if (!$result) {
                throw new \Exception('Gagal mengubah status');
            }
            
            $kategori = $this->kategoriModel->find($id);
            $newStatus = $kategori['is_active'];
            $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => "Kategori aset tetap berhasil {$statusText}",
                    'is_active' => $newStatus
                ]);
            } else {
                return redirect()->back()
                    ->with('success', "Kategori aset tetap berhasil {$statusText}");
            }
            
        } catch (\Exception $e) {
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
     * Filter data
     */
    public function filter()
    {
        $filters = [
            'is_active' => $this->request->getGet('is_active'),
            'metode_penyusutan' => $this->request->getGet('metode_penyusutan')
        ];
        
        session()->set('filter_kategori_aset', $filters);
        
        return redirect()->to('accounting/aset-tetap/kategori');
    }

    /**
     * Search data
     */
    public function search()
    {
        $search = $this->request->getGet('q');
        
        $filters = session()->get('filter_kategori_aset') ?? [];
        $filters['search'] = $search;
        
        session()->set('filter_kategori_aset', $filters);
        
        return redirect()->to('accounting/aset-tetap/kategori');
    }

    /**
     * Reset filter
     */
    public function resetFilter()
    {
        session()->remove('filter_kategori_aset');
        
        return redirect()->to('accounting/aset-tetap/kategori');
    }

    /**
     * Export data
     */
    public function export()
    {
        $type = $this->request->getGet('type') ?? 'excel';
        $filters = [
            'is_active' => $this->request->getGet('is_active')
        ];
        
        $data = $this->kategoriModel->getExportData($filters);
        
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
                ->setTitle("Daftar Kategori Aset Tetap")
                ->setSubject("Daftar Kategori Aset Tetap")
                ->setDescription("Daftar Kategori Aset Tetap " . date('d-m-Y'));
            
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Daftar Kategori Aset');
            
            // Header laporan
            $sheet->mergeCells('A1:J1');
            $sheet->setCellValue('A1', 'DAFTAR KATEGORI ASET TETAP');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->mergeCells('A2:J2');
            $sheet->setCellValue('A2', 'PT. CIPTA DUTA WACANA');
            $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->mergeCells('A3:J3');
            $sheet->setCellValue('A3', 'Dicetak: ' . date('d/m/Y H:i:s'));
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Baris kosong
            $startRow = 5;
            
            // Header tabel
            $headers = [
                'A' => 'No',
                'B' => 'Kode Kategori',
                'C' => 'Nama Kategori',
                'D' => 'Masa Manfaat',
                'E' => 'Metode Penyusutan',
                'F' => 'Persentase',
                'G' => 'COA Aset',
                'H' => 'COA Akumulasi',
                'I' => 'COA Beban',
                'J' => 'Status'
            ];
            
            foreach ($headers as $col => $header) {
                $sheet->setCellValue($col . $startRow, $header);
            }
            
            // Style header
            $headerRange = 'A' . $startRow . ':J' . $startRow;
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
                $sheet->setCellValue('B' . $row, $item['Kode Kategori']);
                $sheet->setCellValue('C' . $row, $item['Nama Kategori']);
                $sheet->setCellValue('D' . $row, $item['Masa Manfaat (Tahun)']);
                $sheet->setCellValue('E' . $row, $item['Metode Penyusutan']);
                $sheet->setCellValue('F' . $row, $item['Persentase Penyusutan (%)']);
                $sheet->setCellValue('G' . $row, $item['COA Aset']);
                $sheet->setCellValue('H' . $row, $item['COA Akumulasi']);
                $sheet->setCellValue('I' . $row, $item['COA Beban']);
                $sheet->setCellValue('J' . $row, $item['Status']);
                
                // Warna status
                if ($item['Status'] == 'Aktif') {
                    $sheet->getStyle('J' . $row)->getFont()->getColor()->setARGB('FF008000');
                } else {
                    $sheet->getStyle('J' . $row)->getFont()->getColor()->setARGB('FFFF0000');
                }
                
                $row++;
                $no++;
            }
            
            // Auto-size columns
            foreach (range('A', 'J') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Border untuk seluruh data
            $lastRow = $row - 1;
            if ($lastRow >= $startRow) {
                $dataRange = 'A' . $startRow . ':J' . $lastRow;
                $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            }
            
            // Output file
            $filename = 'Daftar_Kategori_Aset_Tetap_' . date('Ymd_His') . '.xlsx';
            
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
            
            $filename = 'Daftar_Kategori_Aset_Tetap_' . date('Ymd_His') . '.pdf';
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
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $filterText .= 'Status: ' . ($filters['is_active'] ? 'Aktif' : 'Nonaktif');
        }
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Daftar Kategori Aset Tetap</title>
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
                .text-success {
                    color: #28a745;
                    font-weight: bold;
                }
                .text-danger {
                    color: #dc3545;
                    font-weight: bold;
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
                <h1>DAFTAR KATEGORI ASET TETAP</h1>
                <h2>PT. CIPTA DUTA WACANA</h2>
                <div>Dicetak: ' . date('d/m/Y H:i:s') . '</div>
                ' . (!empty($filterText) ? '<div>' . $filterText . '</div>' : '') . '
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th width="3%">No</th>
                        <th width="8%">Kode</th>
                        <th width="15%">Nama Kategori</th>
                        <th width="8%">Masa Manfaat</th>
                        <th width="12%">Metode</th>
                        <th width="6%">Persen</th>
                        <th width="18%">COA Aset</th>
                        <th width="18%">COA Akumulasi</th>
                        <th width="18%">COA Beban</th>
                        <th width="6%">Status</th>
                    </tr>
                </thead>
                <tbody>';
        
        if (empty($data)) {
            $html .= '<tr><td colspan="10" class="text-center">Tidak ada data kategori aset tetap</td></tr>';
        } else {
            $no = 1;
            foreach ($data as $item) {
                $statusClass = $item['Status'] == 'Aktif' ? 'text-success' : 'text-danger';
                
                $html .= '
                    <tr>
                        <td class="text-center">' . $no . '</td>
                        <td>' . $item['Kode Kategori'] . '</td>
                        <td>' . $item['Nama Kategori'] . '</td>
                        <td class="text-center">' . $item['Masa Manfaat (Tahun)'] . ' thn</td>
                        <td>' . $item['Metode Penyusutan'] . '</td>
                        <td class="text-center">' . $item['Persentase Penyusutan (%)'] . '</td>
                        <td>' . $item['COA Aset'] . '</td>
                        <td>' . $item['COA Akumulasi'] . '</td>
                        <td>' . $item['COA Beban'] . '</td>
                        <td class="text-center ' . $statusClass . '">' . $item['Status'] . '</td>
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
                            <strong>Total Data:</strong> ' . count($data) . ' kategori
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
     * AJAX: Get kategori options untuk dropdown
     */
    public function ajaxGetOptions()
    {
        $kategori = $this->kategoriModel->getActiveOptions();
        
        $options = [];
        foreach ($kategori as $item) {
            $options[] = [
                'id' => $item['id'],
                'text' => $item['kode_kategori'] . ' - ' . $item['nama_kategori']
            ];
        }
        
        return $this->response->setJSON($options);
    }

    /**
     * AJAX: Get detail kategori
     */
    public function ajaxGetDetail($id)
    {
        $kategori = $this->kategoriModel->getById($id);
        
        if (!$kategori) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Kategori tidak ditemukan'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $kategori
        ]);
    }

    /**
     * AJAX: Get penyusutan rate
     */
    public function ajaxGetPenyusutanRate($id)
    {
        $rate = $this->kategoriModel->getPenyusutanRate($id);
        
        return $this->response->setJSON([
            'success' => true,
            'rate' => $rate,
            'rate_formatted' => number_format($rate, 2) . '%'
        ]);
    }
}