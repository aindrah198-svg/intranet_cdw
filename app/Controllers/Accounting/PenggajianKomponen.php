<?php
namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\Accounting\PenggajianKomponenModel;
use App\Models\CoaModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Dompdf\Dompdf;
use Dompdf\Options;

class PenggajianKomponen extends BaseController
{
    protected $komponenModel;
    protected $coaModel;
    protected $db;

    public function __construct()
    {
        $this->komponenModel = new PenggajianKomponenModel();
        $this->coaModel = new CoaModel();
        $this->db = \Config\Database::connect();
        
        helper(['form', 'url', 'text', 'number']);
        
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
    }

    /**
     * Index / Daftar Komponen Gaji
     */
    public function index()
    {
        $data['title'] = 'Daftar Komponen Gaji';
        
        $filters = [
            'search' => $this->request->getGet('search'),
            'tipe' => $this->request->getGet('tipe'),
            'is_aktif' => $this->request->getGet('is_aktif')
        ];
        
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 20;
        
        $result = $this->komponenModel->getAllWithFilters($filters, $perPage, $page);
        
        $data['komponen'] = $result['data'];
        $data['pager'] = $this->komponenModel->pager;
        $data['total'] = $result['total'];
        $data['perPage'] = $perPage;
        $data['currentPage'] = $page;
        $data['totalPages'] = $result['total_pages'];
        
        $data['tipeOptions'] = ['Pendapatan', 'Potongan'];
        $data['statusOptions'] = ['1' => 'Aktif', '0' => 'Nonaktif'];
        
        $data['stats'] = $this->komponenModel->getStats();
        
        return view('accounting/penggajian/komponen/index', $data);
    }

    /**
     * Form tambah komponen gaji
     */
    public function create()
    {
        $data['title'] = 'Tambah Komponen Gaji';
        $data['validation'] = \Config\Services::validation();
        
        $data['tipeOptions'] = ['Pendapatan', 'Potongan'];
        $data['kategoriOptions'] = ['Tetap', 'Variabel'];
        
        // Ambil COA options berdasarkan tipe
        $data['coaPendapatanOptions'] = $this->komponenModel->getCoaOptions('Pendapatan');
        $data['coaPotonganOptions'] = $this->komponenModel->getCoaOptions('Potongan');
        
        $data['komponen'] = [
            'kode_komponen' => '',
            'nama_komponen' => '',
            'tipe' => 'Pendapatan',
            'kategori' => 'Tetap',
            'coa_id' => '',
            'rumus' => '',
            'is_wajib' => 0,
            'is_aktif' => 1,
            'keterangan' => ''
        ];
        
        return view('accounting/penggajian/komponen/create', $data);
    }

    /**
     * Simpan komponen gaji baru
     */
    public function store()
    {
        $rules = [
            'nama_komponen' => 'required',
            'tipe' => 'required|in_list[Pendapatan,Potongan]',
            'kategori' => 'required|in_list[Tetap,Variabel]',
            'coa_id' => 'permit_empty|is_natural_no_zero',
            'is_wajib' => 'permit_empty|in_list[0,1]',
            'is_aktif' => 'permit_empty|in_list[0,1]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $tipe = $this->request->getPost('tipe');
        
        // Generate kode komponen otomatis
        $kodeKomponen = $this->komponenModel->generateKodeKomponen($tipe);
        
        $data = [
            'kode_komponen' => $kodeKomponen,
            'nama_komponen' => $this->request->getPost('nama_komponen'),
            'tipe' => $tipe,
            'kategori' => $this->request->getPost('kategori'),
            'coa_id' => $this->request->getPost('coa_id') ?: null,
            'rumus' => $this->request->getPost('rumus'),
            'is_wajib' => $this->request->getPost('is_wajib') ?? 0,
            'is_aktif' => $this->request->getPost('is_aktif') ?? 1,
            'keterangan' => $this->request->getPost('keterangan')
        ];
        
        try {
            $this->db->transBegin();
            
            $saved = $this->komponenModel->save($data);
            
            if (!$saved) {
                throw new \Exception('Gagal menyimpan data: ' . json_encode($this->komponenModel->errors()));
            }
            
            $this->db->transCommit();
            
            return redirect()->to('accounting/penggajian/komponen')
                ->with('success', 'Komponen gaji berhasil ditambahkan.');
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal menyimpan komponen gaji: ' . $e->getMessage());
        }
    }

    /**
     * Detail komponen gaji
     */
    public function detail($id)
    {
        $data['title'] = 'Detail Komponen Gaji';
        
        $komponen = $this->komponenModel->getWithDetails($id);
        
        if (!$komponen) {
            return redirect()->to('accounting/penggajian/komponen')
                ->with('error', 'Komponen gaji tidak ditemukan');
        }
        
        $data['komponen'] = $komponen;
        
        return view('accounting/penggajian/komponen/detail', $data);
    }

    /**
     * Form edit komponen gaji
     */
    public function edit($id)
    {
        $data['title'] = 'Edit Komponen Gaji';
        
        $komponen = $this->komponenModel->find($id);
        
        if (!$komponen) {
            return redirect()->to('accounting/penggajian/komponen')
                ->with('error', 'Komponen gaji tidak ditemukan');
        }
        
        $data['validation'] = \Config\Services::validation();
        $data['komponen'] = $komponen;
        
        $data['tipeOptions'] = ['Pendapatan', 'Potongan'];
        $data['kategoriOptions'] = ['Tetap', 'Variabel'];
        
        // Ambil COA options berdasarkan tipe
        $data['coaPendapatanOptions'] = $this->komponenModel->getCoaOptions('Pendapatan');
        $data['coaPotonganOptions'] = $this->komponenModel->getCoaOptions('Potongan');
        
        return view('accounting/penggajian/komponen/edit', $data);
    }

    /**
     * Update komponen gaji
     */
    public function update($id)
    {
        $komponen = $this->komponenModel->find($id);
        
        if (!$komponen) {
            return redirect()->to('accounting/penggajian/komponen')
                ->with('error', 'Komponen gaji tidak ditemukan');
        }
        
        $rules = [
            'nama_komponen' => 'required',
            'tipe' => 'required|in_list[Pendapatan,Potongan]',
            'kategori' => 'required|in_list[Tetap,Variabel]',
            'coa_id' => 'permit_empty|is_natural_no_zero',
            'is_wajib' => 'permit_empty|in_list[0,1]',
            'is_aktif' => 'permit_empty|in_list[0,1]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'id' => $id,
            'nama_komponen' => $this->request->getPost('nama_komponen'),
            'tipe' => $this->request->getPost('tipe'),
            'kategori' => $this->request->getPost('kategori'),
            'coa_id' => $this->request->getPost('coa_id') ?: null,
            'rumus' => $this->request->getPost('rumus'),
            'is_wajib' => $this->request->getPost('is_wajib') ?? 0,
            'is_aktif' => $this->request->getPost('is_aktif') ?? 0,
            'keterangan' => $this->request->getPost('keterangan')
        ];
        
        // Jika tipe berubah, update kode komponen
        if ($komponen['tipe'] !== $data['tipe']) {
            $data['kode_komponen'] = $this->komponenModel->generateKodeKomponen($data['tipe']);
        }
        
        try {
            $this->db->transBegin();
            
            $updated = $this->komponenModel->save($data);
            
            if (!$updated) {
                throw new \Exception('Gagal mengupdate data: ' . json_encode($this->komponenModel->errors()));
            }
            
            $this->db->transCommit();
            
            return redirect()->to('accounting/penggajian/komponen')
                ->with('success', 'Komponen gaji berhasil diupdate.');
                
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal mengupdate komponen gaji: ' . $e->getMessage());
        }
    }

    /**
     * Hapus komponen gaji
     */
    public function delete($id)
    {
        $isAjax = $this->request->isAJAX();
        
        $komponen = $this->komponenModel->find($id);
        
        if (!$komponen) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Komponen gaji tidak ditemukan'
                ]);
            } else {
                return redirect()->to('accounting/penggajian/komponen')
                    ->with('error', 'Komponen gaji tidak ditemukan');
            }
        }
        
        // Cek apakah komponen digunakan dalam perhitungan gaji
        if ($this->komponenModel->isUsedInPayroll($id)) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Komponen tidak dapat dihapus karena sudah digunakan dalam perhitungan gaji'
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Komponen tidak dapat dihapus karena sudah digunakan dalam perhitungan gaji');
            }
        }
        
        try {
            $this->db->transBegin();
            
            $deleted = $this->komponenModel->delete($id);
            
            if (!$deleted) {
                throw new \Exception('Gagal menghapus data');
            }
            
            $this->db->transCommit();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Komponen gaji berhasil dihapus',
                    'redirect' => site_url('accounting/penggajian/komponen')
                ]);
            } else {
                return redirect()->to('accounting/penggajian/komponen')
                    ->with('success', 'Komponen gaji berhasil dihapus');
            }
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus komponen gaji: ' . $e->getMessage()
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal menghapus komponen gaji: ' . $e->getMessage());
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
            $result = $this->komponenModel->toggleStatus($id);
            
            if (!$result) {
                throw new \Exception('Gagal mengubah status');
            }
            
            $komponen = $this->komponenModel->find($id);
            $newStatus = $komponen['is_aktif'];
            $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => "Komponen gaji berhasil {$statusText}",
                    'is_aktif' => $newStatus
                ]);
            } else {
                return redirect()->back()
                    ->with('success', "Komponen gaji berhasil {$statusText}");
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
            'tipe' => $this->request->getGet('tipe'),
            'is_aktif' => $this->request->getGet('is_aktif')
        ];
        
        session()->set('filter_komponen_gaji', $filters);
        
        return redirect()->to('accounting/penggajian/komponen');
    }

    /**
     * Search data
     */
    public function search()
    {
        $search = $this->request->getGet('q');
        
        $filters = session()->get('filter_komponen_gaji') ?? [];
        $filters['search'] = $search;
        
        session()->set('filter_komponen_gaji', $filters);
        
        return redirect()->to('accounting/penggajian/komponen');
    }

    /**
     * Reset filter
     */
    public function resetFilter()
    {
        session()->remove('filter_komponen_gaji');
        
        return redirect()->to('accounting/penggajian/komponen');
    }

    /**
     * Export data
     */
    public function export()
    {
        $type = $this->request->getGet('type') ?? 'excel';
        $filters = [
            'tipe' => $this->request->getGet('tipe'),
            'is_aktif' => $this->request->getGet('is_aktif')
        ];
        
        $data = $this->komponenModel->getExportData($filters);
        
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
                ->setTitle("Daftar Komponen Gaji")
                ->setSubject("Daftar Komponen Gaji")
                ->setDescription("Daftar Komponen Gaji " . date('d-m-Y'));
            
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Daftar Komponen Gaji');
            
            // Header laporan
            $sheet->mergeCells('A1:H1');
            $sheet->setCellValue('A1', 'DAFTAR KOMPONEN GAJI');
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
                'B' => 'Kode',
                'C' => 'Nama Komponen',
                'D' => 'Tipe',
                'E' => 'Kategori',
                'F' => 'COA',
                'G' => 'Wajib',
                'H' => 'Status'
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
                $sheet->setCellValue('B' . $row, $item['Kode Komponen']);
                $sheet->setCellValue('C' . $row, $item['Nama Komponen']);
                $sheet->setCellValue('D' . $row, $item['Tipe']);
                $sheet->setCellValue('E' . $row, $item['Kategori']);
                $sheet->setCellValue('F' . $row, $item['COA']);
                $sheet->setCellValue('G' . $row, $item['Wajib']);
                $sheet->setCellValue('H' . $row, $item['Aktif']);
                
                // Warna status
                if ($item['Aktif'] == 'Ya') {
                    $sheet->getStyle('H' . $row)->getFont()->getColor()->setARGB('FF008000');
                } else {
                    $sheet->getStyle('H' . $row)->getFont()->getColor()->setARGB('FFFF0000');
                }
                
                // Warna tipe
                if ($item['Tipe'] == 'Pendapatan') {
                    $sheet->getStyle('D' . $row)->getFont()->getColor()->setARGB('FF008000');
                } else {
                    $sheet->getStyle('D' . $row)->getFont()->getColor()->setARGB('FFFF0000');
                }
                
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
            $filename = 'Daftar_Komponen_Gaji_' . date('Ymd_His') . '.xlsx';
            
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
            
            $filename = 'Daftar_Komponen_Gaji_' . date('Ymd_His') . '.pdf';
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
        if (!empty($filters['tipe'])) {
            $filterText .= 'Tipe: ' . $filters['tipe'] . ' | ';
        }
        if (isset($filters['is_aktif']) && $filters['is_aktif'] !== '') {
            $filterText .= 'Status: ' . ($filters['is_aktif'] ? 'Aktif' : 'Nonaktif');
        }
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Daftar Komponen Gaji</title>
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
                <h1>DAFTAR KOMPONEN GAJI</h1>
                <h2>PT. CIPTA DUTA WACANA</h2>
                <div>Dicetak: ' . date('d/m/Y H:i:s') . '</div>
                ' . (!empty($filterText) ? '<div>' . $filterText . '</div>' : '') . '
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th width="3%">No</th>
                        <th width="8%">Kode</th>
                        <th width="20%">Nama Komponen</th>
                        <th width="8%">Tipe</th>
                        <th width="8%">Kategori</th>
                        <th width="25%">COA</th>
                        <th width="5%">Wajib</th>
                        <th width="5%">Status</th>
                    </tr>
                </thead>
                <tbody>';
        
        if (empty($data)) {
            $html .= '<tr><td colspan="8" class="text-center">Tidak ada data komponen gaji</td></tr>';
        } else {
            $no = 1;
            foreach ($data as $item) {
                $tipeClass = $item['Tipe'] == 'Pendapatan' ? 'text-success' : 'text-danger';
                $statusClass = $item['Aktif'] == 'Ya' ? 'text-success' : 'text-danger';
                
                $html .= '
                    <tr>
                        <td class="text-center">' . $no . '</td>
                        <td class="text-center">' . $item['Kode Komponen'] . '</td>
                        <td>' . $item['Nama Komponen'] . '</td>
                        <td class="text-center ' . $tipeClass . '">' . $item['Tipe'] . '</td>
                        <td class="text-center">' . $item['Kategori'] . '</td>
                        <td>' . $item['COA'] . '</td>
                        <td class="text-center">' . $item['Wajib'] . '</td>
                        <td class="text-center ' . $statusClass . '">' . $item['Aktif'] . '</td>
                    </table>';
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
                            <strong>Total Data:</strong> ' . count($data) . ' komponen
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
     * AJAX: Get komponen options untuk dropdown
     */
    public function ajaxGetOptions()
    {
        $tipe = $this->request->getGet('tipe');
        
        $komponen = $this->komponenModel->getDropdown($tipe);
        
        $options = [];
        foreach ($komponen as $item) {
            $options[] = [
                'id' => $item['id'],
                'text' => $item['kode_komponen'] . ' - ' . $item['nama_komponen']
            ];
        }
        
        return $this->response->setJSON($options);
    }

    /**
     * AJAX: Get komponen pendapatan
     */
    public function ajaxGetPendapatan()
    {
        $komponen = $this->komponenModel->getPendapatanOptions();
        
        $options = [];
        foreach ($komponen as $item) {
            $options[] = [
                'id' => $item['id'],
                'text' => $item['kode_komponen'] . ' - ' . $item['nama_komponen']
            ];
        }
        
        return $this->response->setJSON($options);
    }

    /**
     * AJAX: Get komponen potongan
     */
    public function ajaxGetPotongan()
    {
        $komponen = $this->komponenModel->getPotonganOptions();
        
        $options = [];
        foreach ($komponen as $item) {
            $options[] = [
                'id' => $item['id'],
                'text' => $item['kode_komponen'] . ' - ' . $item['nama_komponen']
            ];
        }
        
        return $this->response->setJSON($options);
    }
}