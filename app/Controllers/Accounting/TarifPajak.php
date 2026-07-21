<?php
namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\Accounting\TarifPajakModel;
use App\Models\CoaModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Dompdf\Dompdf;
use Dompdf\Options;

class TarifPajak extends BaseController
{
    protected $tarifPajakModel;
    protected $coaModel;
    protected $db;

    public function __construct()
    {
        $this->tarifPajakModel = new TarifPajakModel();
        $this->coaModel = new CoaModel();
        $this->db = \Config\Database::connect();
        
        helper(['form', 'url', 'text', 'number']);
        
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
    }

    /**
     * Index / Daftar Tarif Pajak
     */
    public function index()
    {
        $data['title'] = 'Daftar Tarif Pajak';
        
        $filters = [
            'search' => $this->request->getGet('search'),
            'jenis_pajak' => $this->request->getGet('jenis_pajak'),
            'is_active' => $this->request->getGet('is_active')
        ];
        
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 20;
        
        $result = $this->tarifPajakModel->getAllWithFilters($filters, $perPage, $page);
        
        $data['tarif'] = $result['data'];
        $data['pager'] = $this->tarifPajakModel->pager;
        $data['total'] = $result['total'];
        $data['perPage'] = $perPage;
        $data['currentPage'] = $page;
        $data['totalPages'] = $result['total_pages'];
        
        $data['jenisPajakOptions'] = $this->tarifPajakModel->getJenisPajakOptions();
        $data['statusOptions'] = ['1' => 'Aktif', '0' => 'Nonaktif'];
        
        $data['stats'] = $this->tarifPajakModel->getStats();
        
        return view('accounting/manajemen-pajak/tarif-pajak/index', $data);
    }

    /**
     * Form tambah tarif pajak
     */
    public function create()
    {
        $data['title'] = 'Tambah Tarif Pajak';
        $data['validation'] = \Config\Services::validation();
        
        $data['jenisPajakOptions'] = [
            'PPN' => 'PPN',
            'PPh 21' => 'PPh 21',
            'PPh 23' => 'PPh 23',
            'PPh 25' => 'PPh 25',
            'PPh 29' => 'PPh 29',
            'PPh Badan' => 'PPh Badan',
            'Lainnya' => 'Lainnya'
        ];
        
        $data['tarif'] = [
            'jenis_pajak' => '',
            'kode_tarif' => '',
            'nama_tarif' => '',
            'persentase' => 0,
            'berlaku_mulai' => date('Y-m-d'),
            'berlaku_sampai' => null,
            'keterangan' => '',
            'is_active' => 1
        ];
        
        return view('accounting/manajemen-pajak/tarif-pajak/create', $data);
    }

    /**
     * Simpan tarif pajak baru
     */
    public function store()
    {
        // Validasi input
        $rules = [
            'jenis_pajak' => 'required|in_list[PPN,PPh 21,PPh 23,PPh 25,PPh 29,PPh Badan,Lainnya]',
            'nama_tarif' => 'required',
            'persentase' => 'required|numeric|greater_than[0]|less_than_equal_to[100]',
            'berlaku_mulai' => 'required|valid_date',
            'berlaku_sampai' => 'permit_empty|valid_date'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $berlakuMulai = $this->request->getPost('berlaku_mulai');
        $berlakuSampai = $this->request->getPost('berlaku_sampai');
        
        // Validasi tanggal
        if ($berlakuSampai && $berlakuSampai < $berlakuMulai) {
            return redirect()->back()->withInput()
                ->with('error', 'Tanggal berlaku sampai harus lebih besar atau sama dengan tanggal berlaku mulai');
        }
        
        $data = [
            'jenis_pajak' => $this->request->getPost('jenis_pajak'),
            'nama_tarif' => $this->request->getPost('nama_tarif'),
            'persentase' => $this->request->getPost('persentase'),
            'berlaku_mulai' => $berlakuMulai,
            'berlaku_sampai' => $berlakuSampai ?: null,
            'keterangan' => $this->request->getPost('keterangan'),
            'is_active' => $this->request->getPost('is_active') ?? 1
        ];
        
        try {
            $this->db->transBegin();
            
            // Generate kode tarif otomatis
            $data['kode_tarif'] = $this->tarifPajakModel->generateKodeTarif(['data' => $data]);
            
            $saved = $this->tarifPajakModel->save($data);
            
            if (!$saved) {
                throw new \Exception('Gagal menyimpan data: ' . json_encode($this->tarifPajakModel->errors()));
            }
            
            $this->db->transCommit();
            
            return redirect()->to('accounting/manajemen-pajak/tarif-pajak')
                ->with('success', 'Tarif pajak berhasil ditambahkan.');
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal menyimpan tarif pajak: ' . $e->getMessage());
        }
    }

    /**
     * Detail tarif pajak
     */
    public function detail($id)
    {
        $data['title'] = 'Detail Tarif Pajak';
        
        $tarif = $this->tarifPajakModel->getById($id);
        
        if (!$tarif) {
            return redirect()->to('accounting/manajemen-pajak/tarif-pajak')
                ->with('error', 'Tarif pajak tidak ditemukan');
        }
        
        // Format persentase
        $tarif['persentase_formatted'] = number_format($tarif['persentase'], 2) . '%';
        
        // Cek status aktif berdasarkan tanggal
        $today = date('Y-m-d');
        $isCurrentlyActive = $tarif['berlaku_mulai'] <= $today && 
            (!$tarif['berlaku_sampai'] || $today <= $tarif['berlaku_sampai']);
        
        $data['tarif'] = $tarif;
        $data['is_currently_active'] = $isCurrentlyActive;
        
        return view('accounting/manajemen-pajak/tarif-pajak/detail', $data);
    }

    /**
     * Form edit tarif pajak
     */
    public function edit($id)
    {
        $data['title'] = 'Edit Tarif Pajak';
        
        $tarif = $this->tarifPajakModel->find($id);
        
        if (!$tarif) {
            return redirect()->to('accounting/manajemen-pajak/tarif-pajak')
                ->with('error', 'Tarif pajak tidak ditemukan');
        }
        
        $data['validation'] = \Config\Services::validation();
        $data['tarif'] = $tarif;
        
        $data['jenisPajakOptions'] = [
            'PPN' => 'PPN',
            'PPh 21' => 'PPh 21',
            'PPh 23' => 'PPh 23',
            'PPh 25' => 'PPh 25',
            'PPh 29' => 'PPh 29',
            'PPh Badan' => 'PPh Badan',
            'Lainnya' => 'Lainnya'
        ];
        
        return view('accounting/manajemen-pajak/tarif-pajak/edit', $data);
    }

    /**
     * Update tarif pajak
     */
    public function update($id)
    {
        $tarif = $this->tarifPajakModel->find($id);
        
        if (!$tarif) {
            return redirect()->to('accounting/manajemen-pajak/tarif-pajak')
                ->with('error', 'Tarif pajak tidak ditemukan');
        }
        
        $rules = [
            'jenis_pajak' => 'required|in_list[PPN,PPh 21,PPh 23,PPh 25,PPh 29,PPh Badan,Lainnya]',
            'nama_tarif' => 'required',
            'persentase' => 'required|numeric|greater_than[0]|less_than_equal_to[100]',
            'berlaku_mulai' => 'required|valid_date',
            'berlaku_sampai' => 'permit_empty|valid_date'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $berlakuMulai = $this->request->getPost('berlaku_mulai');
        $berlakuSampai = $this->request->getPost('berlaku_sampai');
        
        if ($berlakuSampai && $berlakuSampai < $berlakuMulai) {
            return redirect()->back()->withInput()
                ->with('error', 'Tanggal berlaku sampai harus lebih besar atau sama dengan tanggal berlaku mulai');
        }
        
        $data = [
            'id' => $id,
            'jenis_pajak' => $this->request->getPost('jenis_pajak'),
            'nama_tarif' => $this->request->getPost('nama_tarif'),
            'persentase' => $this->request->getPost('persentase'),
            'berlaku_mulai' => $berlakuMulai,
            'berlaku_sampai' => $berlakuSampai ?: null,
            'keterangan' => $this->request->getPost('keterangan'),
            'is_active' => $this->request->getPost('is_active') ?? 0
        ];
        
        try {
            $this->db->transBegin();
            
            $updated = $this->tarifPajakModel->save($data);
            
            if (!$updated) {
                throw new \Exception('Gagal mengupdate data: ' . json_encode($this->tarifPajakModel->errors()));
            }
            
            $this->db->transCommit();
            
            return redirect()->to('accounting/manajemen-pajak/tarif-pajak')
                ->with('success', 'Tarif pajak berhasil diupdate.');
                
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal mengupdate tarif pajak: ' . $e->getMessage());
        }
    }

    /**
     * Hapus tarif pajak
     */
    public function delete($id)
    {
        $isAjax = $this->request->isAJAX();
        
        $tarif = $this->tarifPajakModel->find($id);
        
        if (!$tarif) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tarif pajak tidak ditemukan'
                ]);
            } else {
                return redirect()->to('accounting/manajemen-pajak/tarif-pajak')
                    ->with('error', 'Tarif pajak tidak ditemukan');
            }
        }
        
        try {
            $this->db->transBegin();
            
            $deleted = $this->tarifPajakModel->delete($id);
            
            if (!$deleted) {
                throw new \Exception('Gagal menghapus data');
            }
            
            $this->db->transCommit();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Tarif pajak berhasil dihapus',
                    'redirect' => site_url('accounting/manajemen-pajak/tarif-pajak')
                ]);
            } else {
                return redirect()->to('accounting/manajemen-pajak/tarif-pajak')
                    ->with('success', 'Tarif pajak berhasil dihapus');
            }
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus tarif pajak: ' . $e->getMessage()
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal menghapus tarif pajak: ' . $e->getMessage());
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
            $result = $this->tarifPajakModel->toggleStatus($id);
            
            if (!$result) {
                throw new \Exception('Gagal mengubah status');
            }
            
            $tarif = $this->tarifPajakModel->find($id);
            $newStatus = $tarif['is_active'];
            $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => "Tarif pajak berhasil {$statusText}",
                    'is_active' => $newStatus
                ]);
            } else {
                return redirect()->back()
                    ->with('success', "Tarif pajak berhasil {$statusText}");
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
            'jenis_pajak' => $this->request->getGet('jenis_pajak'),
            'is_active' => $this->request->getGet('is_active')
        ];
        
        session()->set('filter_tarif_pajak', $filters);
        
        return redirect()->to('accounting/manajemen-pajak/tarif-pajak');
    }

    /**
     * Search data
     */
    public function search()
    {
        $search = $this->request->getGet('q');
        
        $filters = session()->get('filter_tarif_pajak') ?? [];
        $filters['search'] = $search;
        
        session()->set('filter_tarif_pajak', $filters);
        
        return redirect()->to('accounting/manajemen-pajak/tarif-pajak');
    }

    /**
     * Reset filter
     */
    public function resetFilter()
    {
        session()->remove('filter_tarif_pajak');
        
        return redirect()->to('accounting/manajemen-pajak/tarif-pajak');
    }

    /**
     * Export data
     */
    public function export()
    {
        $type = $this->request->getGet('type') ?? 'excel';
        $filters = [
            'jenis_pajak' => $this->request->getGet('jenis_pajak'),
            'is_active' => $this->request->getGet('is_active')
        ];
        
        $data = $this->tarifPajakModel->getExportData($filters);
        
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
                ->setTitle("Daftar Tarif Pajak")
                ->setSubject("Daftar Tarif Pajak")
                ->setDescription("Daftar Tarif Pajak " . date('d-m-Y'));
            
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Daftar Tarif Pajak');
            
            // Header laporan
            $sheet->mergeCells('A1:I1');
            $sheet->setCellValue('A1', 'DAFTAR TARIF PAJAK');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->mergeCells('A2:I2');
            $sheet->setCellValue('A2', 'PT. CIPTA DUTA WACANA');
            $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->mergeCells('A3:I3');
            $sheet->setCellValue('A3', 'Dicetak: ' . date('d/m/Y H:i:s'));
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Baris kosong
            $startRow = 5;
            
            // Header tabel
            $headers = [
                'A' => 'No',
                'B' => 'Kode Tarif',
                'C' => 'Jenis Pajak',
                'D' => 'Nama Tarif',
                'E' => 'Persentase',
                'F' => 'Berlaku Mulai',
                'G' => 'Berlaku Sampai',
                'H' => 'Status',
                'I' => 'Keterangan'
            ];
            
            foreach ($headers as $col => $header) {
                $sheet->setCellValue($col . $startRow, $header);
            }
            
            // Style header
            $headerRange = 'A' . $startRow . ':I' . $startRow;
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
                $sheet->setCellValue('B' . $row, $item['Kode Tarif']);
                $sheet->setCellValue('C' . $row, $item['Jenis Pajak']);
                $sheet->setCellValue('D' . $row, $item['Nama Tarif']);
                $sheet->setCellValue('E' . $row, $item['Persentase']);
                $sheet->setCellValue('F' . $row, $item['Berlaku Mulai']);
                $sheet->setCellValue('G' . $row, $item['Berlaku Sampai']);
                $sheet->setCellValue('H' . $row, $item['Status']);
                $sheet->setCellValue('I' . $row, $item['Keterangan']);
                
                // Warna status
                if ($item['Status'] == 'Aktif') {
                    $sheet->getStyle('H' . $row)->getFont()->getColor()->setARGB('FF008000');
                } else {
                    $sheet->getStyle('H' . $row)->getFont()->getColor()->setARGB('FFFF0000');
                }
                
                $row++;
                $no++;
            }
            
            // Auto-size columns
            foreach (range('A', 'I') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Border untuk seluruh data
            $lastRow = $row - 1;
            if ($lastRow >= $startRow) {
                $dataRange = 'A' . $startRow . ':I' . $lastRow;
                $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            }
            
            // Output file
            $filename = 'Daftar_Tarif_Pajak_' . date('Ymd_His') . '.xlsx';
            
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
            
            $filename = 'Daftar_Tarif_Pajak_' . date('Ymd_His') . '.pdf';
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
            $filterText .= 'Jenis Pajak: ' . $filters['jenis_pajak'] . ' | ';
        }
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $filterText .= 'Status: ' . ($filters['is_active'] ? 'Aktif' : 'Nonaktif');
        }
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Daftar Tarif Pajak</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 10px;
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
                    padding: 8px;
                    text-align: center;
                    font-weight: bold;
                    border: 1px solid #000;
                }
                td {
                    padding: 6px;
                    border: 1px solid #000;
                    vertical-align: top;
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
                <h1>DAFTAR TARIF PAJAK</h1>
                <h2>PT. CIPTA DUTA WACANA</h2>
                <div>Dicetak: ' . date('d/m/Y H:i:s') . '</div>
                ' . (!empty($filterText) ? '<div>' . $filterText . '</div>' : '') . '
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th width="3%">No</th>
                        <th width="10%">Kode Tarif</th>
                        <th width="12%">Jenis Pajak</th>
                        <th width="15%">Nama Tarif</th>
                        <th width="8%">Persentase</th>
                        <th width="10%">Berlaku Mulai</th>
                        <th width="10%">Berlaku Sampai</th>
                        <th width="8%">Status</th>
                        <th width="24%">Keterangan</th>
                    </tr>
                </thead>
                <tbody>';
        
        if (empty($data)) {
            $html .= '
                    <tr>
                        <td colspan="9" class="text-center">Tidak ada data tarif pajak</td>
                    </tr>';
        } else {
            $no = 1;
            foreach ($data as $item) {
                $statusClass = $item['Status'] == 'Aktif' ? 'text-success' : 'text-danger';
                
                $html .= '
                    <tr>
                        <td class="text-center">' . $no . '</td>
                        <td>' . $item['Kode Tarif'] . '</td>
                        <td>' . $item['Jenis Pajak'] . '</td>
                        <td>' . $item['Nama Tarif'] . '</td>
                        <td class="text-end">' . $item['Persentase'] . '</td>
                        <td class="text-center">' . $item['Berlaku Mulai'] . '</td>
                        <td class="text-center">' . $item['Berlaku Sampai'] . '</td>
                        <td class="text-center ' . $statusClass . '">' . $item['Status'] . '</td>
                        <td>' . ($item['Keterangan'] ?? '-') . '</td>
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
                            <strong>Total Data:</strong> ' . count($data) . ' tarif
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
     * Get options for dropdown (AJAX)
     */
    public function ajaxGetOptions()
    {
        $jenisPajak = $this->request->getGet('jenis_pajak');
        
        $tarif = $this->tarifPajakModel->getOptions($jenisPajak);
        
        $options = [];
        foreach ($tarif as $item) {
            $options[] = [
                'id' => $item['id'],
                'text' => $item['kode_tarif'] . ' - ' . $item['nama_tarif'] . ' (' . $item['persentase'] . '%)'
            ];
        }
        
        return $this->response->setJSON($options);
    }

    /**
     * Get current rate for specific jenis pajak (AJAX)
     */
    public function ajaxGetCurrentRate()
    {
        $jenisPajak = $this->request->getGet('jenis_pajak');
        
        $rate = $this->tarifPajakModel->getCurrentRate($jenisPajak);
        
        return $this->response->setJSON([
            'success' => true,
            'jenis_pajak' => $jenisPajak,
            'rate' => $rate,
            'rate_formatted' => number_format($rate, 2) . '%'
        ]);
    }

    /**
     * Get tarif yang akan segera berakhir (AJAX)
     */
    public function ajaxGetExpiringSoon()
    {
        $hariKeDepan = $this->request->getGet('days') ?? 30;
        
        $expiring = $this->tarifPajakModel->getExpiringSoon($hariKeDepan);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $expiring,
            'count' => count($expiring)
        ]);
    }

    /**
     * Get tarif yang akan segera berlaku (AJAX)
     */
    public function ajaxGetUpcomingSoon()
    {
        $hariKeDepan = $this->request->getGet('days') ?? 30;
        
        $upcoming = $this->tarifPajakModel->getUpcomingSoon($hariKeDepan);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $upcoming,
            'count' => count($upcoming)
        ]);
    }

    /**
     * Update expired tarif (maintenance)
     */
    public function updateExpired()
    {
        try {
            $updated = $this->tarifPajakModel->updateExpiredTarif();
            
            return $this->response->setJSON([
                'success' => true,
                'message' => "Berhasil menonaktifkan {$updated} tarif yang sudah kadaluarsa"
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Activate new tarif (maintenance)
     */
    public function activateNew()
    {
        try {
            $activated = $this->tarifPajakModel->activateNewTarif();
            
            return $this->response->setJSON([
                'success' => true,
                'message' => "Berhasil mengaktifkan {$activated} tarif yang baru berlaku"
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}