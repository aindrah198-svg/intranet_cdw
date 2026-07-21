<?php
namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\Accounting\SetoranPajakModel;
use App\Models\Accounting\TarifPajakModel;
use App\Models\Accounting\MutasiBankModel;
use App\Models\CoaModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Dompdf\Dompdf;
use Dompdf\Options;

class SetoranPajak extends BaseController
{
    protected $setoranPajakModel;
    protected $tarifPajakModel;
    protected $mutasiBankModel;
    protected $coaModel;
    protected $db;

    public function __construct()
    {
        $this->setoranPajakModel = new SetoranPajakModel();
        $this->tarifPajakModel = new TarifPajakModel();
        $this->mutasiBankModel = new MutasiBankModel();
        $this->coaModel = new CoaModel();
        $this->db = \Config\Database::connect();
        
        helper(['form', 'url', 'text', 'number']);
        
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
    }

    /**
     * Index / Daftar Setoran Pajak
     */
    public function index()
    {
        $data['title'] = 'Daftar Setoran Pajak';
        
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
        
        $result = $this->setoranPajakModel->getAllWithFilters($filters, $perPage, $page);
        
        $data['setoran'] = $result['data'];
        $data['pager'] = $this->setoranPajakModel->pager;
        $data['total'] = $result['total'];
        $data['perPage'] = $perPage;
        $data['currentPage'] = $page;
        $data['totalPages'] = $result['total_pages'];
        
        $data['jenisPajakOptions'] = ['PPN', 'PPh 21', 'PPh 23', 'PPh 25', 'PPh 29', 'PPh Badan', 'Lainnya'];
        $data['tahunOptions'] = $this->getTahunOptions();
        $data['bulanOptions'] = $this->getBulanOptions();
        
        $data['stats'] = $this->setoranPajakModel->getStats();
        $data['statsPerJenis'] = $this->setoranPajakModel->getTotalPerJenis();
        
        return view('accounting/manajemen-pajak/setoran-pajak/index', $data);
    }

    /**
     * Form tambah setoran pajak
     */
    public function create()
    {
        $data['title'] = 'Tambah Setoran Pajak';
        $data['validation'] = \Config\Services::validation();
        
        $data['jenisPajakOptions'] = ['PPN', 'PPh 21', 'PPh 23', 'PPh 25', 'PPh 29', 'PPh Badan', 'Lainnya'];
        $data['bulanOptions'] = $this->getBulanOptions();
        $data['tahunOptions'] = $this->getTahunOptions();
        
        // Ambil daftar mutasi bank yang belum digunakan
        $data['mutasiBankOptions'] = $this->mutasiBankModel->select('id, kode_transaksi, tanggal, jumlah, keterangan')
            ->where('tipe', 'Debit')
            ->where('status', 'Posted')
            ->whereNotIn('id', function($builder) {
                $builder->select('mutasi_bank_id')->from('setoran_pajak')->where('mutasi_bank_id IS NOT NULL');
            })
            ->orderBy('tanggal', 'DESC')
            ->findAll();
        
        // Ambil daftar COA untuk referensi
        $data['coaOptions'] = $this->coaModel->where('is_header', 0)
            ->where('is_active', 1)
            ->like('kode_akun', '1-11', 'after')
            ->orderBy('kode_akun', 'ASC')
            ->findAll();
        
        $data['setoran'] = [
            'jenis_pajak' => 'PPN',
            'masa_pajak' => date('m'),
            'tahun_pajak' => date('Y'),
            'tanggal_setor' => date('Y-m-d'),
            'nominal' => 0,
            'no_bukti_setor' => '',
            'no_ntpn' => '',
            'mutasi_bank_id' => '',
            'keterangan' => ''
        ];
        
        return view('accounting/manajemen-pajak/setoran-pajak/create', $data);
    }

    /**
     * Simpan setoran pajak baru
     */
    public function store()
    {
        $rules = [
            'jenis_pajak' => 'required|in_list[PPN,PPh 21,PPh 23,PPh 25,PPh 29,PPh Badan,Lainnya]',
            'tahun_pajak' => 'required|numeric|min_length[4]|max_length[4]',
            'tanggal_setor' => 'required|valid_date',
            'nominal' => 'required|numeric|greater_than[0]',
            'no_ntpn' => 'permit_empty|min_length[16]|max_length[20]'
        ];
        
        // Validasi masa pajak untuk jenis pajak selain PPh Badan
        $jenisPajak = $this->request->getPost('jenis_pajak');
        if ($jenisPajak !== 'PPh Badan') {
            $rules['masa_pajak'] = 'required|numeric|greater_than[0]|less_than_equal_to[12]';
        }
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'jenis_pajak' => $jenisPajak,
            'tahun_pajak' => $this->request->getPost('tahun_pajak'),
            'tanggal_setor' => $this->request->getPost('tanggal_setor'),
            'nominal' => $this->cleanCurrency($this->request->getPost('nominal')),
            'no_bukti_setor' => $this->request->getPost('no_bukti_setor'),
            'no_ntpn' => $this->request->getPost('no_ntpn'),
            'mutasi_bank_id' => $this->request->getPost('mutasi_bank_id') ?: null,
            'keterangan' => $this->request->getPost('keterangan')
        ];
        
        if ($jenisPajak !== 'PPh Badan') {
            $data['masa_pajak'] = $this->request->getPost('masa_pajak');
        }
        
        try {
            $this->db->transBegin();
            
            $saved = $this->setoranPajakModel->save($data);
            
            if (!$saved) {
                throw new \Exception('Gagal menyimpan data: ' . json_encode($this->setoranPajakModel->errors()));
            }
            
            $this->db->transCommit();
            
            return redirect()->to('accounting/manajemen-pajak/setoran-pajak')
                ->with('success', 'Setoran pajak berhasil ditambahkan.');
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal menyimpan setoran pajak: ' . $e->getMessage());
        }
    }

    /**
     * Detail setoran pajak
     */
    public function detail($id)
    {
        $data['title'] = 'Detail Setoran Pajak';
        
        $setoran = $this->setoranPajakModel->getWithDetails($id);
        
        if (!$setoran) {
            return redirect()->to('accounting/manajemen-pajak/setoran-pajak')
                ->with('error', 'Setoran pajak tidak ditemukan');
        }
        
        $setoran['nominal_formatted'] = $this->formatRupiah($setoran['nominal']);
        $setoran['terbilang'] = ucwords($this->terbilang($setoran['nominal'])) . ' Rupiah';
        
        $data['setoran'] = $setoran;
        
        return view('accounting/manajemen-pajak/setoran-pajak/detail', $data);
    }

    /**
     * Form edit setoran pajak
     */
    public function edit($id)
    {
        $data['title'] = 'Edit Setoran Pajak';
        
        $setoran = $this->setoranPajakModel->find($id);
        
        if (!$setoran) {
            return redirect()->to('accounting/manajemen-pajak/setoran-pajak')
                ->with('error', 'Setoran pajak tidak ditemukan');
        }
        
        $data['validation'] = \Config\Services::validation();
        $data['setoran'] = $setoran;
        
        $data['jenisPajakOptions'] = ['PPN', 'PPh 21', 'PPh 23', 'PPh 25', 'PPh 29', 'PPh Badan', 'Lainnya'];
        $data['bulanOptions'] = $this->getBulanOptions();
        $data['tahunOptions'] = $this->getTahunOptions();
        
        // Ambil daftar mutasi bank yang belum digunakan (termasuk yang sudah digunakan oleh setoran ini)
        $data['mutasiBankOptions'] = $this->mutasiBankModel->select('id, kode_transaksi, tanggal, jumlah, keterangan')
            ->where('tipe', 'Debit')
            ->where('status', 'Posted')
            ->whereNotIn('id', function($builder) use ($id) {
                $builder->select('mutasi_bank_id')->from('setoran_pajak')
                    ->where('mutasi_bank_id IS NOT NULL')
                    ->where('id !=', $id);
            })
            ->orderBy('tanggal', 'DESC')
            ->findAll();
        
        return view('accounting/manajemen-pajak/setoran-pajak/edit', $data);
    }

    /**
     * Update setoran pajak
     */
    public function update($id)
    {
        $setoran = $this->setoranPajakModel->find($id);
        
        if (!$setoran) {
            return redirect()->to('accounting/manajemen-pajak/setoran-pajak')
                ->with('error', 'Setoran pajak tidak ditemukan');
        }
        
        $rules = [
            'jenis_pajak' => 'required|in_list[PPN,PPh 21,PPh 23,PPh 25,PPh 29,PPh Badan,Lainnya]',
            'tahun_pajak' => 'required|numeric|min_length[4]|max_length[4]',
            'tanggal_setor' => 'required|valid_date',
            'nominal' => 'required|numeric|greater_than[0]',
            'no_ntpn' => 'permit_empty|min_length[16]|max_length[20]'
        ];
        
        $jenisPajak = $this->request->getPost('jenis_pajak');
        if ($jenisPajak !== 'PPh Badan') {
            $rules['masa_pajak'] = 'required|numeric|greater_than[0]|less_than_equal_to[12]';
        }
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'id' => $id,
            'jenis_pajak' => $jenisPajak,
            'tahun_pajak' => $this->request->getPost('tahun_pajak'),
            'tanggal_setor' => $this->request->getPost('tanggal_setor'),
            'nominal' => $this->cleanCurrency($this->request->getPost('nominal')),
            'no_bukti_setor' => $this->request->getPost('no_bukti_setor'),
            'no_ntpn' => $this->request->getPost('no_ntpn'),
            'mutasi_bank_id' => $this->request->getPost('mutasi_bank_id') ?: null,
            'keterangan' => $this->request->getPost('keterangan')
        ];
        
        if ($jenisPajak !== 'PPh Badan') {
            $data['masa_pajak'] = $this->request->getPost('masa_pajak');
        }
        
        try {
            $this->db->transBegin();
            
            $updated = $this->setoranPajakModel->save($data);
            
            if (!$updated) {
                throw new \Exception('Gagal mengupdate data: ' . json_encode($this->setoranPajakModel->errors()));
            }
            
            $this->db->transCommit();
            
            return redirect()->to('accounting/manajemen-pajak/setoran-pajak')
                ->with('success', 'Setoran pajak berhasil diupdate.');
                
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal mengupdate setoran pajak: ' . $e->getMessage());
        }
    }

    /**
     * Hapus setoran pajak
     */
    public function delete($id)
    {
        $isAjax = $this->request->isAJAX();
        
        $setoran = $this->setoranPajakModel->find($id);
        
        if (!$setoran) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Setoran pajak tidak ditemukan'
                ]);
            } else {
                return redirect()->to('accounting/manajemen-pajak/setoran-pajak')
                    ->with('error', 'Setoran pajak tidak ditemukan');
            }
        }
        
        try {
            $this->db->transBegin();
            
            $deleted = $this->setoranPajakModel->delete($id);
            
            if (!$deleted) {
                throw new \Exception('Gagal menghapus data');
            }
            
            $this->db->transCommit();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Setoran pajak berhasil dihapus',
                    'redirect' => site_url('accounting/manajemen-pajak/setoran-pajak')
                ]);
            } else {
                return redirect()->to('accounting/manajemen-pajak/setoran-pajak')
                    ->with('success', 'Setoran pajak berhasil dihapus');
            }
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus setoran pajak: ' . $e->getMessage()
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal menghapus setoran pajak: ' . $e->getMessage());
            }
        }
    }

    /**
     * Update NTPN
     */
    public function updateNtpn($id)
    {
        $isAjax = $this->request->isAJAX();
        $noNtpn = $this->request->getPost('no_ntpn');
        
        if (empty($noNtpn)) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'NTPN harus diisi'
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'NTPN harus diisi');
            }
        }
        
        try {
            $result = $this->setoranPajakModel->updateNtpn($id, $noNtpn);
            
            if (!$result) {
                throw new \Exception('Gagal mengupdate NTPN');
            }
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'NTPN berhasil diupdate'
                ]);
            } else {
                return redirect()->back()
                    ->with('success', 'NTPN berhasil diupdate');
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
            'tahun' => $this->request->getGet('tahun'),
            'bulan' => $this->request->getGet('bulan'),
            'tanggal_mulai' => $this->request->getGet('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getGet('tanggal_selesai')
        ];
        
        session()->set('filter_setoran_pajak', $filters);
        
        return redirect()->to('accounting/manajemen-pajak/setoran-pajak');
    }

    /**
     * Search data
     */
    public function search()
    {
        $search = $this->request->getGet('q');
        
        $filters = session()->get('filter_setoran_pajak') ?? [];
        $filters['search'] = $search;
        
        session()->set('filter_setoran_pajak', $filters);
        
        return redirect()->to('accounting/manajemen-pajak/setoran-pajak');
    }

    /**
     * Reset filter
     */
    public function resetFilter()
    {
        session()->remove('filter_setoran_pajak');
        
        return redirect()->to('accounting/manajemen-pajak/setoran-pajak');
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
        
        $data = $this->setoranPajakModel->getExportData($filters);
        
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
                ->setTitle("Daftar Setoran Pajak")
                ->setSubject("Daftar Setoran Pajak")
                ->setDescription("Daftar Setoran Pajak " . date('d-m-Y'));
            
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Daftar Setoran Pajak');
            
            // Header laporan
            $sheet->mergeCells('A1:J1');
            $sheet->setCellValue('A1', 'DAFTAR SETORAN PAJAK');
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
                'B' => 'Jenis Pajak',
                'C' => 'Masa Pajak',
                'D' => 'Tanggal Setor',
                'E' => 'Nominal',
                'F' => 'No Bukti Setor',
                'G' => 'NTPN',
                'H' => 'Kode Mutasi',
                'I' => 'No Jurnal',
                'J' => 'Keterangan'
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
                $sheet->setCellValue('B' . $row, $item['Jenis Pajak']);
                $sheet->setCellValue('C' . $row, $item['Masa Pajak']);
                $sheet->setCellValue('D' . $row, $item['Tanggal Setor']);
                $sheet->setCellValue('E' . $row, $item['Nominal']);
                $sheet->setCellValue('F' . $row, $item['No Bukti Setor']);
                $sheet->setCellValue('G' . $row, $item['NTPN']);
                $sheet->setCellValue('H' . $row, $item['Kode Mutasi']);
                $sheet->setCellValue('I' . $row, $item['No Jurnal']);
                $sheet->setCellValue('J' . $row, $item['Keterangan']);
                
                // Format angka
                $sheet->getStyle('E' . $row)->getNumberFormat()
                    ->setFormatCode('"Rp" #,##0.00');
                
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
            $filename = 'Daftar_Setoran_Pajak_' . date('Ymd_His') . '.xlsx';
            
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
            
            $filename = 'Daftar_Setoran_Pajak_' . date('Ymd_His') . '.pdf';
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
            <title>Daftar Setoran Pajak</title>
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
                <h1>DAFTAR SETORAN PAJAK</h1>
                <h2>PT. CIPTA DUTA WACANA</h2>
                <div>Dicetak: ' . date('d/m/Y H:i:s') . '</div>
                ' . (!empty($filterText) ? '<div>' . $filterText . '</div>' : '') . '
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th width="3%">No</th>
                        <th width="10%">Jenis Pajak</th>
                        <th width="8%">Masa Pajak</th>
                        <th width="8%">Tanggal Setor</th>
                        <th width="8%">Nominal</th>
                        <th width="10%">No Bukti Setor</th>
                        <th width="12%">NTPN</th>
                        <th width="10%">Kode Mutasi</th>
                        <th width="10%">No Jurnal</th>
                        <th width="21%">Keterangan</th>
                    </tr>
                </thead>
                <tbody>';
        
        if (empty($data)) {
            $html .= '<tr><td colspan="10" class="text-center">Tidak ada data setoran pajak</td></tr>';
        } else {
            $no = 1;
            foreach ($data as $item) {
                $html .= '
                    <tr>
                        <td class="text-center">' . $no . '</td>
                        <td>' . $item['Jenis Pajak'] . '</td>
                        <td class="text-center">' . $item['Masa Pajak'] . '</td>
                        <td class="text-center">' . $item['Tanggal Setor'] . '</td>
                        <td class="text-end">Rp ' . number_format($item['Nominal'], 0) . '</td>
                        <td>' . $item['No Bukti Setor'] . '</td>
                        <td>' . $item['NTPN'] . '</td>
                        <td>' . $item['Kode Mutasi'] . '</td>
                        <td>' . $item['No Jurnal'] . '</td>
                        <td>' . $item['Keterangan'] . '</td>
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
                            <strong>Total Data:</strong> ' . count($data) . ' setoran
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
     * AJAX: Get total setoran per jenis pajak
     */
    public function ajaxGetTotalPerJenis()
    {
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        
        $total = $this->setoranPajakModel->getTotalPerJenis($tahun);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $total,
            'tahun' => $tahun
        ]);
    }

    /**
     * AJAX: Get total setoran per masa
     */
    public function ajaxGetTotalPerMasa()
    {
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        
        $total = $this->setoranPajakModel->getTotalPerMasa($tahun);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $total,
            'tahun' => $tahun
        ]);
    }

    /**
     * AJAX: Get mutasi bank details
     */
    public function ajaxGetMutasiBankDetails()
    {
        $mutasiBankId = $this->request->getGet('mutasi_bank_id');
        
        if (!$mutasiBankId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Mutasi bank ID required']);
        }
        
        $mutasi = $this->mutasiBankModel->getWithDetails($mutasiBankId);
        
        if (!$mutasi) {
            return $this->response->setJSON(['success' => false, 'message' => 'Mutasi bank tidak ditemukan']);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'mutasi' => [
                'id' => $mutasi['id'],
                'kode_transaksi' => $mutasi['kode_transaksi'],
                'tanggal' => $mutasi['tanggal'],
                'jumlah' => $mutasi['jumlah'],
                'jumlah_formatted' => $this->formatRupiah($mutasi['jumlah']),
                'keterangan' => $mutasi['keterangan']
            ]
        ]);
    }

    /**
     * AJAX: Get chart data
     */
    public function ajaxGetChartData()
    {
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        
        $chartData = $this->setoranPajakModel->getChartData($tahun);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $chartData,
            'tahun' => $tahun
        ]);
    }

    /**
     * Fungsi untuk membersihkan format currency
     */
    private function cleanCurrency($value)
    {
        if (empty($value)) return 0;
        
        $value = str_replace('Rp', '', $value);
        $value = str_replace('rp', '', $value);
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);
        $value = trim($value);
        
        return (float) $value;
    }

    /**
     * Fungsi untuk format currency ke Rupiah
     */
    private function formatRupiah($angka)
    {
        if (!$angka || $angka == 0) return '0';
        return number_format($angka, 0, ',', '.');
    }

    /**
     * Fungsi untuk mendapatkan teks terbilang
     */
    private function terbilang($angka)
    {
        $angka = abs($angka);
        $baca = array('', 'satu', 'dua', 'tiga', 'quatre', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas');
        
        if ($angka < 12) {
            return $baca[$angka];
        } elseif ($angka < 20) {
            return $baca[$angka - 10] . ' belas';
        } elseif ($angka < 100) {
            return $baca[floor($angka / 10)] . ' puluh ' . $baca[$angka % 10];
        } elseif ($angka < 200) {
            return 'seratus ' . $this->terbilang($angka - 100);
        } elseif ($angka < 1000) {
            return $baca[floor($angka / 100)] . ' ratus ' . $this->terbilang($angka % 100);
        } elseif ($angka < 2000) {
            return 'seribu ' . $this->terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            return $this->terbilang(floor($angka / 1000)) . ' ribu ' . $this->terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            return $this->terbilang(floor($angka / 1000000)) . ' juta ' . $this->terbilang($angka % 1000000);
        } elseif ($angka < 1000000000000) {
            return $this->terbilang(floor($angka / 1000000000)) . ' miliar ' . $this->terbilang($angka % 1000000000);
        } else {
            return $this->terbilang(floor($angka / 1000000000000)) . ' triliun ' . $this->terbilang($angka % 1000000000000);
        }
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