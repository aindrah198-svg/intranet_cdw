<?php
namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\Accounting\PpnKeluaranModel;
use App\Models\Accounting\FakturPajakModel;
use App\Models\Accounting\TarifPajakModel;
use App\Models\Accounting\SetoranPajakModel;
use App\Models\CoaModel;
use App\Models\InvoiceModel;
use App\Models\ProjectModel;
use App\Models\ClientModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Dompdf\Dompdf;
use Dompdf\Options;

class PpnKeluaran extends BaseController
{
    protected $ppnKeluaranModel;
    protected $fakturPajakModel;
    protected $tarifPajakModel;
    protected $setoranPajakModel;
    protected $coaModel;
    protected $invoiceModel;
    protected $projectModel;
    protected $clientModel;
    protected $db;

    public function __construct()
    {
        $this->ppnKeluaranModel = new PpnKeluaranModel();
        $this->fakturPajakModel = new FakturPajakModel();
        $this->tarifPajakModel = new TarifPajakModel();
        $this->setoranPajakModel = new SetoranPajakModel();
        $this->coaModel = new CoaModel();
        $this->invoiceModel = new InvoiceModel();
        $this->projectModel = new ProjectModel();
        $this->clientModel = new ClientModel();
        $this->db = \Config\Database::connect();
        
        helper(['form', 'url', 'text', 'number']);
        
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
    }

    /**
     * Index / Daftar PPN Keluaran
     */
    public function index()
    {
        $data['title'] = 'Daftar PPN Keluaran';
        
        $filters = [
            'search' => $this->request->getGet('search'),
            'status_setor' => $this->request->getGet('status_setor'),
            'tahun' => $this->request->getGet('tahun'),
            'bulan' => $this->request->getGet('bulan')
        ];
        
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 20;
        
        $result = $this->ppnKeluaranModel->getAllWithFilters($filters, $perPage, $page);
        
        $data['ppn_keluaran'] = $result['data'];
        $data['pager'] = $this->ppnKeluaranModel->pager;
        $data['total'] = $result['total'];
        $data['perPage'] = $perPage;
        $data['currentPage'] = $page;
        $data['totalPages'] = $result['total_pages'];
        
        $data['statusSetorOptions'] = ['Belum Disetor', 'Sudah Disetor'];
        $data['tahunOptions'] = $this->getTahunOptions();
        $data['bulanOptions'] = $this->getBulanOptions();
        
        $data['stats'] = $this->ppnKeluaranModel->getStats();
        
        return view('accounting/manajemen-pajak/ppn-keluaran/index', $data);
    }

    /**
     * Detail PPN Keluaran
     */
    public function detail($id)
    {
        $data['title'] = 'Detail PPN Keluaran';
        
        $ppnKeluaran = $this->ppnKeluaranModel->getWithDetails($id);
        
        if (!$ppnKeluaran) {
            return redirect()->to('accounting/manajemen-pajak/ppn-keluaran')
                ->with('error', 'Data PPN keluaran tidak ditemukan');
        }
        
        $ppnKeluaran['nilai_dpp_formatted'] = $this->formatRupiah($ppnKeluaran['nilai_dpp']);
        $ppnKeluaran['nilai_ppn_formatted'] = $this->formatRupiah($ppnKeluaran['nilai_ppn']);
        
        $data['ppn_keluaran'] = $ppnKeluaran;
        
        return view('accounting/manajemen-pajak/ppn-keluaran/detail', $data);
    }

    /**
     * Form tambah PPN Keluaran (dari faktur)
     */
    public function create()
    {
        $data['title'] = 'Tambah PPN Keluaran';
        $data['validation'] = \Config\Services::validation();
        
        // Ambil daftar faktur keluaran yang belum memiliki detail PPN
        $fakturOptions = $this->fakturPajakModel->select('faktur_pajak.id, faktur_pajak.nomor_faktur, faktur_pajak.tanggal_faktur, faktur_pajak.nama_pengusaha, faktur_pajak.nilai_transaksi, faktur_pajak.nilai_ppn, faktur_pajak.invoice_id')
            ->where('jenis_faktur', 'Keluaran')
            ->where('status_approval', 'Disetujui')
            ->whereNotIn('faktur_pajak.id', function($builder) {
                $builder->select('faktur_id')->from('ppn_keluaran');
            })
            ->orderBy('tanggal_faktur', 'DESC')
            ->findAll();
        
        $data['fakturOptions'] = $fakturOptions;
        
        // Ambil daftar invoice untuk referensi
        $data['invoiceOptions'] = $this->invoiceModel->select('id, nomor_invoice, tanggal_invoice, total')
            ->where('deleted_at IS NULL')
            ->orderBy('tanggal_invoice', 'DESC')
            ->findAll();
        
        $data['ppn_keluaran'] = [
            'faktur_id' => '',
            'tanggal_penjualan' => date('Y-m-d'),
            'customer' => '',
            'npwp_customer' => '',
            'nomor_invoice' => '',
            'nilai_dpp' => 0,
            'nilai_ppn' => 0,
            'masa_pajak' => date('m'),
            'tahun_pajak' => date('Y'),
            'status_setor' => 'Belum Disetor'
        ];
        
        return view('accounting/manajemen-pajak/ppn-keluaran/create', $data);
    }

    /**
     * Simpan PPN Keluaran dari faktur
     */
    public function store()
    {
        $rules = [
            'faktur_id' => 'required|is_natural_no_zero',
            'tanggal_penjualan' => 'required|valid_date',
            'customer' => 'required',
            'nilai_dpp' => 'required|numeric|greater_than[0]',
            'nilai_ppn' => 'required|numeric|greater_than[0]',
            'masa_pajak' => 'required|numeric|greater_than[0]|less_than_equal_to[12]',
            'tahun_pajak' => 'required|numeric|min_length[4]|max_length[4]',
            'status_setor' => 'required|in_list[Belum Disetor,Sudah Disetor]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $fakturId = $this->request->getPost('faktur_id');
        
        // Cek apakah faktur sudah memiliki PPN keluaran
        $existing = $this->ppnKeluaranModel->where('faktur_id', $fakturId)->first();
        if ($existing) {
            return redirect()->back()->withInput()
                ->with('error', 'Faktur ini sudah memiliki data PPN keluaran');
        }
        
        // Ambil data faktur
        $faktur = $this->fakturPajakModel->find($fakturId);
        if (!$faktur) {
            return redirect()->back()->withInput()
                ->with('error', 'Faktur tidak ditemukan');
        }
        
        $data = [
            'faktur_id' => $fakturId,
            'tanggal_penjualan' => $this->request->getPost('tanggal_penjualan'),
            'customer' => $this->request->getPost('customer'),
            'npwp_customer' => $this->request->getPost('npwp_customer'),
            'nomor_invoice' => $this->request->getPost('nomor_invoice') ?: null,
            'nilai_dpp' => $this->cleanCurrency($this->request->getPost('nilai_dpp')),
            'nilai_ppn' => $this->cleanCurrency($this->request->getPost('nilai_ppn')),
            'masa_pajak' => $this->request->getPost('masa_pajak'),
            'tahun_pajak' => $this->request->getPost('tahun_pajak'),
            'status_setor' => $this->request->getPost('status_setor')
        ];
        
        // Jika status sudah disetor, set tanggal setor
        if ($data['status_setor'] === 'Sudah Disetor') {
            $data['tanggal_setor'] = $this->request->getPost('tanggal_setor') ?: date('Y-m-d');
        }
        
        try {
            $this->db->transBegin();
            
            $saved = $this->ppnKeluaranModel->save($data);
            
            if (!$saved) {
                throw new \Exception('Gagal menyimpan data: ' . json_encode($this->ppnKeluaranModel->errors()));
            }
            
            $this->db->transCommit();
            
            return redirect()->to('accounting/manajemen-pajak/ppn-keluaran')
                ->with('success', 'PPN Keluaran berhasil ditambahkan.');
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal menyimpan PPN Keluaran: ' . $e->getMessage());
        }
    }

    /**
     * Form edit PPN Keluaran
     */
    public function edit($id)
    {
        $data['title'] = 'Edit PPN Keluaran';
        
        $ppnKeluaran = $this->ppnKeluaranModel->find($id);
        
        if (!$ppnKeluaran) {
            return redirect()->to('accounting/manajemen-pajak/ppn-keluaran')
                ->with('error', 'Data PPN keluaran tidak ditemukan');
        }
        
        $data['validation'] = \Config\Services::validation();
        $data['ppn_keluaran'] = $ppnKeluaran;
        $data['statusSetorOptions'] = ['Belum Disetor', 'Sudah Disetor'];
        $data['bulanOptions'] = $this->getBulanOptions();
        $data['tahunOptions'] = $this->getTahunOptions();
        
        // Ambil daftar invoice untuk referensi
        $data['invoiceOptions'] = $this->invoiceModel->select('id, nomor_invoice, tanggal_invoice, total')
            ->where('deleted_at IS NULL')
            ->orderBy('tanggal_invoice', 'DESC')
            ->findAll();
        
        return view('accounting/manajemen-pajak/ppn-keluaran/edit', $data);
    }

    /**
     * Update PPN Keluaran
     */
    public function update($id)
    {
        $ppnKeluaran = $this->ppnKeluaranModel->find($id);
        
        if (!$ppnKeluaran) {
            return redirect()->to('accounting/manajemen-pajak/ppn-keluaran')
                ->with('error', 'Data PPN keluaran tidak ditemukan');
        }
        
        $rules = [
            'tanggal_penjualan' => 'required|valid_date',
            'customer' => 'required',
            'nilai_dpp' => 'required|numeric|greater_than[0]',
            'nilai_ppn' => 'required|numeric|greater_than[0]',
            'masa_pajak' => 'required|numeric|greater_than[0]|less_than_equal_to[12]',
            'tahun_pajak' => 'required|numeric|min_length[4]|max_length[4]',
            'status_setor' => 'required|in_list[Belum Disetor,Sudah Disetor]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'id' => $id,
            'tanggal_penjualan' => $this->request->getPost('tanggal_penjualan'),
            'customer' => $this->request->getPost('customer'),
            'npwp_customer' => $this->request->getPost('npwp_customer'),
            'nomor_invoice' => $this->request->getPost('nomor_invoice') ?: null,
            'nilai_dpp' => $this->cleanCurrency($this->request->getPost('nilai_dpp')),
            'nilai_ppn' => $this->cleanCurrency($this->request->getPost('nilai_ppn')),
            'masa_pajak' => $this->request->getPost('masa_pajak'),
            'tahun_pajak' => $this->request->getPost('tahun_pajak'),
            'status_setor' => $this->request->getPost('status_setor')
        ];
        
        // Jika status berubah menjadi Sudah Disetor, set tanggal setor
        if ($data['status_setor'] === 'Sudah Disetor' && $ppnKeluaran['status_setor'] !== 'Sudah Disetor') {
            $data['tanggal_setor'] = $this->request->getPost('tanggal_setor') ?: date('Y-m-d');
        } elseif ($data['status_setor'] === 'Belum Disetor') {
            $data['tanggal_setor'] = null;
        }
        
        try {
            $this->db->transBegin();
            
            $updated = $this->ppnKeluaranModel->save($data);
            
            if (!$updated) {
                throw new \Exception('Gagal mengupdate data: ' . json_encode($this->ppnKeluaranModel->errors()));
            }
            
            $this->db->transCommit();
            
            return redirect()->to('accounting/manajemen-pajak/ppn-keluaran')
                ->with('success', 'PPN Keluaran berhasil diupdate.');
                
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal mengupdate PPN Keluaran: ' . $e->getMessage());
        }
    }

    /**
     * Hapus PPN Keluaran
     */
    public function delete($id)
    {
        $isAjax = $this->request->isAJAX();
        
        $ppnKeluaran = $this->ppnKeluaranModel->find($id);
        
        if (!$ppnKeluaran) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data PPN keluaran tidak ditemukan'
                ]);
            } else {
                return redirect()->to('accounting/manajemen-pajak/ppn-keluaran')
                    ->with('error', 'Data PPN keluaran tidak ditemukan');
            }
        }
        
        try {
            $this->db->transBegin();
            
            $deleted = $this->ppnKeluaranModel->delete($id);
            
            if (!$deleted) {
                throw new \Exception('Gagal menghapus data');
            }
            
            $this->db->transCommit();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'PPN Keluaran berhasil dihapus',
                    'redirect' => site_url('accounting/manajemen-pajak/ppn-keluaran')
                ]);
            } else {
                return redirect()->to('accounting/manajemen-pajak/ppn-keluaran')
                    ->with('success', 'PPN Keluaran berhasil dihapus');
            }
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus PPN Keluaran: ' . $e->getMessage()
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal menghapus PPN Keluaran: ' . $e->getMessage());
            }
        }
    }

    /**
     * Mark as paid (tandai sebagai sudah disetor)
     */
    public function markAsPaid($id)
    {
        $isAjax = $this->request->isAJAX();
        $tanggalSetor = $this->request->getPost('tanggal_setor');
        
        $ppnKeluaran = $this->ppnKeluaranModel->find($id);
        
        if (!$ppnKeluaran) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data PPN keluaran tidak ditemukan'
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Data PPN keluaran tidak ditemukan');
            }
        }
        
        if ($ppnKeluaran['status_setor'] !== 'Belum Disetor') {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Hanya PPN dengan status Belum Disetor yang dapat ditandai sudah disetor'
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Hanya PPN dengan status Belum Disetor yang dapat ditandai sudah disetor');
            }
        }
        
        try {
            $result = $this->ppnKeluaranModel->markAsPaid($id, $tanggalSetor);
            
            if (!$result) {
                throw new \Exception('Gagal menandai PPN sebagai sudah disetor');
            }
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'PPN Keluaran berhasil ditandai sebagai sudah disetor'
                ]);
            } else {
                return redirect()->back()
                    ->with('success', 'PPN Keluaran berhasil ditandai sebagai sudah disetor');
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
     * Batch mark as paid (tandai multiple sebagai sudah disetor)
     */
    public function batchMarkAsPaid()
    {
        $isAjax = $this->request->isAJAX();
        $ids = $this->request->getPost('ids');
        $tanggalSetor = $this->request->getPost('tanggal_setor') ?: date('Y-m-d');
        
        if (empty($ids)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada data yang dipilih'
            ]);
        }
        
        $result = $this->ppnKeluaranModel->batchMarkAsPaid($ids, $tanggalSetor);
        
        $message = "Berhasil menandai {$result['success']} PPN sebagai sudah disetor";
        if ($result['failed'] > 0) {
            $message .= ", {$result['failed']} gagal";
        }
        
        return $this->response->setJSON([
            'success' => true,
            'message' => $message,
            'failed_items' => $result['failed']
        ]);
    }

    /**
     * Filter data
     */
    public function filter()
    {
        $filters = [
            'status_setor' => $this->request->getGet('status_setor'),
            'tahun' => $this->request->getGet('tahun'),
            'bulan' => $this->request->getGet('bulan')
        ];
        
        session()->set('filter_ppn_keluaran', $filters);
        
        return redirect()->to('accounting/manajemen-pajak/ppn-keluaran');
    }

    /**
     * Search data
     */
    public function search()
    {
        $search = $this->request->getGet('q');
        
        $filters = session()->get('filter_ppn_keluaran') ?? [];
        $filters['search'] = $search;
        
        session()->set('filter_ppn_keluaran', $filters);
        
        return redirect()->to('accounting/manajemen-pajak/ppn-keluaran');
    }

    /**
     * Reset filter
     */
    public function resetFilter()
    {
        session()->remove('filter_ppn_keluaran');
        
        return redirect()->to('accounting/manajemen-pajak/ppn-keluaran');
    }

    /**
     * Export data
     */
    public function export()
    {
        $type = $this->request->getGet('type') ?? 'excel';
        $filters = [
            'status_setor' => $this->request->getGet('status_setor'),
            'tahun' => $this->request->getGet('tahun')
        ];
        
        $data = $this->ppnKeluaranModel->getExportData($filters);
        
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
                ->setTitle("Daftar PPN Keluaran")
                ->setSubject("Daftar PPN Keluaran")
                ->setDescription("Daftar PPN Keluaran " . date('d-m-Y'));
            
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Daftar PPN Keluaran');
            
            // Header laporan
            $sheet->mergeCells('A1:K1');
            $sheet->setCellValue('A1', 'DAFTAR PPN KELUARAN');
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
                'B' => 'Nomor Faktur',
                'C' => 'Tanggal Faktur',
                'D' => 'Customer',
                'E' => 'NPWP Customer',
                'F' => 'No Invoice',
                'G' => 'Nilai DPP',
                'H' => 'Nilai PPN',
                'I' => 'Masa Pajak',
                'J' => 'Status Setor',
                'K' => 'Tanggal Setor'
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
                $sheet->setCellValue('B' . $row, $item['Nomor Faktur']);
                $sheet->setCellValue('C' . $row, $item['Tanggal Faktur']);
                $sheet->setCellValue('D' . $row, $item['Customer']);
                $sheet->setCellValue('E' . $row, $item['NPWP Customer'] ?? '-');
                $sheet->setCellValue('F' . $row, $item['No Invoice'] ?? '-');
                $sheet->setCellValue('G' . $row, $item['Nilai DPP']);
                $sheet->setCellValue('H' . $row, $item['Nilai PPN']);
                $sheet->setCellValue('I' . $row, $item['Masa Pajak']);
                $sheet->setCellValue('J' . $row, $item['Status Setor']);
                $sheet->setCellValue('K' . $row, $item['Tanggal Setor'] ?? '-');
                
                // Format angka
                $sheet->getStyle('G' . $row . ':H' . $row)->getNumberFormat()
                    ->setFormatCode('"Rp" #,##0.00');
                
                // Warna status
                if ($item['Status Setor'] == 'Sudah Disetor') {
                    $sheet->getStyle('J' . $row)->getFont()->getColor()->setARGB('FF008000');
                } else {
                    $sheet->getStyle('J' . $row)->getFont()->getColor()->setARGB('FFFF0000');
                }
                
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
            $filename = 'Daftar_PPN_Keluaran_' . date('Ymd_His') . '.xlsx';
            
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
            
            $filename = 'Daftar_PPN_Keluaran_' . date('Ymd_His') . '.pdf';
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
        if (!empty($filters['status_setor'])) {
            $filterText .= 'Status: ' . $filters['status_setor'] . ' | ';
        }
        if (!empty($filters['tahun'])) {
            $filterText .= 'Tahun: ' . $filters['tahun'];
        }
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Daftar PPN Keluaran</title>
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
                <h1>DAFTAR PPN KELUARAN</h1>
                <h2>PT. CIPTA DUTA WACANA</h2>
                <div>Dicetak: ' . date('d/m/Y H:i:s') . '</div>
                ' . (!empty($filterText) ? '<div>' . $filterText . '</div>' : '') . '
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th width="3%">No</th>
                        <th width="8%">Nomor Faktur</th>
                        <th width="8%">Tanggal</th>
                        <th width="15%">Customer</th>
                        <th width="10%">NPWP</th>
                        <th width="8%">No Invoice</th>
                        <th width="8%">Nilai DPP</th>
                        <th width="8%">Nilai PPN</th>
                        <th width="6%">Masa</th>
                        <th width="8%">Status</th>
                        <th width="8%">Tgl Setor</th>
                    </tr>
                </thead>
                <tbody>';
        
        if (empty($data)) {
            $html .= '<tr><td colspan="11" class="text-center">Tidak ada data PPN keluaran</td></tr>';
        } else {
            $no = 1;
            foreach ($data as $item) {
                $statusClass = $item['Status Setor'] == 'Sudah Disetor' ? 'text-success' : 'text-danger';
                
                $html .= '
                    <tr>
                        <td class="text-center">' . $no . '</td>
                        <td>' . $item['Nomor Faktur'] . '</td>
                        <td class="text-center">' . $item['Tanggal Faktur'] . '</td>
                        <td>' . $item['Customer'] . '</td>
                        <td>' . $item['NPWP Customer'] . '</td>
                        <td>' . ($item['No Invoice'] ?? '-') . '</td>
                        <td class="text-end">Rp ' . number_format($item['Nilai DPP'], 0) . '</td>
                        <td class="text-end">Rp ' . number_format($item['Nilai PPN'], 0) . '</td>
                        <td class="text-center">' . $item['Masa Pajak'] . '</td>
                        <td class="text-center ' . $statusClass . '">' . $item['Status Setor'] . '</td>
                        <td class="text-center">' . ($item['Tanggal Setor'] ?? '-') . '</td>
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
                            <strong>Total Data:</strong> ' . count($data) . ' faktur
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
     * AJAX: Get faktur details
     */
    public function ajaxGetFakturDetails()
    {
        $fakturId = $this->request->getGet('faktur_id');
        
        if (!$fakturId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Faktur ID required']);
        }
        
        $faktur = $this->fakturPajakModel->getWithDetails($fakturId);
        
        if (!$faktur) {
            return $this->response->setJSON(['success' => false, 'message' => 'Faktur not found']);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'faktur' => [
                'id' => $faktur['id'],
                'nomor_faktur' => $faktur['nomor_faktur'],
                'tanggal_faktur' => $faktur['tanggal_faktur'],
                'nama_pengusaha' => $faktur['nama_pengusaha'],
                'npwp_pengusaha' => $faktur['npwp_pengusaha'],
                'nilai_transaksi' => $faktur['nilai_transaksi'],
                'nilai_transaksi_formatted' => $this->formatRupiah($faktur['nilai_transaksi']),
                'nilai_ppn' => $faktur['nilai_ppn'],
                'nilai_ppn_formatted' => $this->formatRupiah($faktur['nilai_ppn']),
                'masa_pajak' => $faktur['masa_pajak'],
                'tahun_pajak' => $faktur['tahun_pajak'],
                'invoice_id' => $faktur['invoice_id']
            ]
        ]);
    }

    /**
     * AJAX: Get invoice details
     */
    public function ajaxGetInvoiceDetails()
    {
        $invoiceId = $this->request->getGet('invoice_id');
        
        if (!$invoiceId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invoice ID required']);
        }
        
        $invoice = $this->invoiceModel->getWithDetails($invoiceId);
        
        if (!$invoice) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invoice not found']);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'invoice' => [
                'id' => $invoice['id'],
                'nomor_invoice' => $invoice['nomor_invoice'],
                'tanggal_invoice' => $invoice['tanggal_invoice'],
                'total' => $invoice['total'],
                'total_formatted' => $this->formatRupiah($invoice['total']),
                'client_nama' => $invoice['client_nama'] ?? '',
                'client_npwp' => $invoice['client_npwp'] ?? '',
                'client_alamat' => $invoice['client_alamat'] ?? ''
            ]
        ]);
    }

    /**
     * AJAX: Get summary for SPT Masa
     */
    public function ajaxGetSummary()
    {
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        
        $summary = $this->ppnKeluaranModel->getTotalPerMasa($tahun);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $summary,
            'tahun' => $tahun
        ]);
    }

    /**
     * AJAX: Get PPN terutang for SPT Masa
     */
    public function ajaxGetPpnTerutang()
    {
        $masaPajak = $this->request->getGet('masa_pajak');
        $tahunPajak = $this->request->getGet('tahun_pajak');
        
        if (!$masaPajak || !$tahunPajak) {
            return $this->response->setJSON(['success' => false, 'message' => 'Masa dan tahun pajak required']);
        }
        
        $result = $this->ppnKeluaranModel->getPpnTerutang($masaPajak, $tahunPajak);
        
        return $this->response->setJSON([
            'success' => true,
            'masa_pajak' => $masaPajak,
            'tahun_pajak' => $tahunPajak,
            'total_ppn_keluaran' => $result['total_ppn_keluaran'],
            'total_ppn_masukan' => $result['total_ppn_masukan'],
            'ppn_kurang_bayar' => $result['ppn_kurang_bayar'],
            'ppn_lebih_bayar' => $result['ppn_lebih_bayar'],
            'total_ppn_keluaran_formatted' => $this->formatRupiah($result['total_ppn_keluaran']),
            'total_ppn_masukan_formatted' => $this->formatRupiah($result['total_ppn_masukan']),
            'ppn_kurang_bayar_formatted' => $this->formatRupiah($result['ppn_kurang_bayar']),
            'ppn_lebih_bayar_formatted' => $this->formatRupiah($result['ppn_lebih_bayar'])
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