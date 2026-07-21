<?php
namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\Accounting\PpnMasukanModel;
use App\Models\Accounting\FakturPajakModel;
use App\Models\Accounting\TarifPajakModel;
use App\Models\CoaModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Dompdf\Dompdf;
use Dompdf\Options;

class PpnMasukan extends BaseController
{
    protected $ppnMasukanModel;
    protected $fakturPajakModel;
    protected $tarifPajakModel;
    protected $coaModel;
    protected $db;

    public function __construct()
    {
        $this->ppnMasukanModel = new PpnMasukanModel();
        $this->fakturPajakModel = new FakturPajakModel();
        $this->tarifPajakModel = new TarifPajakModel();
        $this->coaModel = new CoaModel();
        $this->db = \Config\Database::connect();
        
        helper(['form', 'url', 'text', 'number']);
        
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
    }

    /**
     * Index / Daftar PPN Masukan
     */
    public function index()
    {
        $data['title'] = 'Daftar PPN Masukan';
        
        $filters = [
            'search' => $this->request->getGet('search'),
            'status_kredit' => $this->request->getGet('status_kredit'),
            'tahun' => $this->request->getGet('tahun'),
            'bulan' => $this->request->getGet('bulan')
        ];
        
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 20;
        
        $result = $this->ppnMasukanModel->getAllWithFilters($filters, $perPage, $page);
        
        $data['ppn_masukan'] = $result['data'];
        $data['pager'] = $this->ppnMasukanModel->pager;
        $data['total'] = $result['total'];
        $data['perPage'] = $perPage;
        $data['currentPage'] = $page;
        $data['totalPages'] = $result['total_pages'];
        
        $data['statusKreditOptions'] = ['Belum Dikreditkan', 'Dikreditkan', 'Tidak Dikreditkan'];
        $data['tahunOptions'] = $this->getTahunOptions();
        $data['bulanOptions'] = $this->getBulanOptions();
        
        $data['stats'] = $this->ppnMasukanModel->getStats();
        
        return view('accounting/manajemen-pajak/ppn-masukan/index', $data);
    }

    /**
     * Detail PPN Masukan
     */
    public function detail($id)
    {
        $data['title'] = 'Detail PPN Masukan';
        
        $ppnMasukan = $this->ppnMasukanModel->getWithDetails($id);
        
        if (!$ppnMasukan) {
            return redirect()->to('accounting/manajemen-pajak/ppn-masukan')
                ->with('error', 'Data PPN masukan tidak ditemukan');
        }
        
        $ppnMasukan['nilai_dpp_formatted'] = $this->formatRupiah($ppnMasukan['nilai_dpp']);
        $ppnMasukan['nilai_ppn_formatted'] = $this->formatRupiah($ppnMasukan['nilai_ppn']);
        
        $data['ppn_masukan'] = $ppnMasukan;
        
        return view('accounting/manajemen-pajak/ppn-masukan/detail', $data);
    }

    /**
     * Form tambah PPN Masukan (dari faktur)
     */
    public function create()
    {
        $data['title'] = 'Tambah PPN Masukan';
        $data['validation'] = \Config\Services::validation();
        
        // Ambil daftar faktur masukan yang belum memiliki detail PPN
        $fakturOptions = $this->fakturPajakModel->select('faktur_pajak.id, faktur_pajak.nomor_faktur, faktur_pajak.tanggal_faktur, faktur_pajak.nama_pengusaha, faktur_pajak.nilai_transaksi, faktur_pajak.nilai_ppn')
            ->where('jenis_faktur', 'Masukan')
            ->where('status_approval', 'Disetujui')
            ->whereNotIn('faktur_pajak.id', function($builder) {
                $builder->select('faktur_id')->from('ppn_masukan');
            })
            ->orderBy('tanggal_faktur', 'DESC')
            ->findAll();
        
        $data['fakturOptions'] = $fakturOptions;
        
        $data['ppn_masukan'] = [
            'faktur_id' => '',
            'tanggal_pembelian' => date('Y-m-d'),
            'supplier' => '',
            'npwp_supplier' => '',
            'nomor_invoice_supplier' => '',
            'nilai_dpp' => 0,
            'nilai_ppn' => 0,
            'masa_pajak' => date('m'),
            'tahun_pajak' => date('Y'),
            'status_kredit' => 'Belum Dikreditkan'
        ];
        
        return view('accounting/manajemen-pajak/ppn-masukan/create', $data);
    }

    /**
     * Simpan PPN Masukan dari faktur
     */
    public function store()
    {
        $rules = [
            'faktur_id' => 'required|is_natural_no_zero',
            'tanggal_pembelian' => 'required|valid_date',
            'supplier' => 'required',
            'npwp_supplier' => 'required|min_length[15]|max_length[20]',
            'nilai_dpp' => 'required|numeric|greater_than[0]',
            'nilai_ppn' => 'required|numeric|greater_than[0]',
            'masa_pajak' => 'required|numeric|greater_than[0]|less_than_equal_to[12]',
            'tahun_pajak' => 'required|numeric|min_length[4]|max_length[4]',
            'status_kredit' => 'required|in_list[Belum Dikreditkan,Dikreditkan,Tidak Dikreditkan]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $fakturId = $this->request->getPost('faktur_id');
        
        // Cek apakah faktur sudah memiliki PPN masukan
        $existing = $this->ppnMasukanModel->where('faktur_id', $fakturId)->first();
        if ($existing) {
            return redirect()->back()->withInput()
                ->with('error', 'Faktur ini sudah memiliki data PPN masukan');
        }
        
        // Ambil data faktur
        $faktur = $this->fakturPajakModel->find($fakturId);
        if (!$faktur) {
            return redirect()->back()->withInput()
                ->with('error', 'Faktur tidak ditemukan');
        }
        
        $data = [
            'faktur_id' => $fakturId,
            'tanggal_pembelian' => $this->request->getPost('tanggal_pembelian'),
            'supplier' => $this->request->getPost('supplier'),
            'npwp_supplier' => $this->request->getPost('npwp_supplier'),
            'nomor_invoice_supplier' => $this->request->getPost('nomor_invoice_supplier'),
            'nilai_dpp' => $this->cleanCurrency($this->request->getPost('nilai_dpp')),
            'nilai_ppn' => $this->cleanCurrency($this->request->getPost('nilai_ppn')),
            'masa_pajak' => $this->request->getPost('masa_pajak'),
            'tahun_pajak' => $this->request->getPost('tahun_pajak'),
            'status_kredit' => $this->request->getPost('status_kredit')
        ];
        
        // Jika status kredit adalah Dikreditkan, set bulan dan tahun dikreditkan
        if ($data['status_kredit'] === 'Dikreditkan') {
            $data['bulan_dikreditkan'] = $this->request->getPost('bulan_dikreditkan') ?: $data['masa_pajak'];
            $data['tahun_dikreditkan'] = $this->request->getPost('tahun_dikreditkan') ?: $data['tahun_pajak'];
        }
        
        try {
            $this->db->transBegin();
            
            $saved = $this->ppnMasukanModel->save($data);
            
            if (!$saved) {
                throw new \Exception('Gagal menyimpan data: ' . json_encode($this->ppnMasukanModel->errors()));
            }
            
            $this->db->transCommit();
            
            return redirect()->to('accounting/manajemen-pajak/ppn-masukan')
                ->with('success', 'PPN Masukan berhasil ditambahkan.');
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal menyimpan PPN Masukan: ' . $e->getMessage());
        }
    }

    /**
     * Form edit PPN Masukan
     */
    public function edit($id)
    {
        $data['title'] = 'Edit PPN Masukan';
        
        $ppnMasukan = $this->ppnMasukanModel->find($id);
        
        if (!$ppnMasukan) {
            return redirect()->to('accounting/manajemen-pajak/ppn-masukan')
                ->with('error', 'Data PPN masukan tidak ditemukan');
        }
        
        $data['validation'] = \Config\Services::validation();
        $data['ppn_masukan'] = $ppnMasukan;
        $data['statusKreditOptions'] = ['Belum Dikreditkan', 'Dikreditkan', 'Tidak Dikreditkan'];
        $data['bulanOptions'] = $this->getBulanOptions();
        $data['tahunOptions'] = $this->getTahunOptions();
        
        return view('accounting/manajemen-pajak/ppn-masukan/edit', $data);
    }

    /**
     * Update PPN Masukan
     */
    public function update($id)
    {
        $ppnMasukan = $this->ppnMasukanModel->find($id);
        
        if (!$ppnMasukan) {
            return redirect()->to('accounting/manajemen-pajak/ppn-masukan')
                ->with('error', 'Data PPN masukan tidak ditemukan');
        }
        
        $rules = [
            'tanggal_pembelian' => 'required|valid_date',
            'supplier' => 'required',
            'npwp_supplier' => 'required|min_length[15]|max_length[20]',
            'nilai_dpp' => 'required|numeric|greater_than[0]',
            'nilai_ppn' => 'required|numeric|greater_than[0]',
            'masa_pajak' => 'required|numeric|greater_than[0]|less_than_equal_to[12]',
            'tahun_pajak' => 'required|numeric|min_length[4]|max_length[4]',
            'status_kredit' => 'required|in_list[Belum Dikreditkan,Dikreditkan,Tidak Dikreditkan]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'id' => $id,
            'tanggal_pembelian' => $this->request->getPost('tanggal_pembelian'),
            'supplier' => $this->request->getPost('supplier'),
            'npwp_supplier' => $this->request->getPost('npwp_supplier'),
            'nomor_invoice_supplier' => $this->request->getPost('nomor_invoice_supplier'),
            'nilai_dpp' => $this->cleanCurrency($this->request->getPost('nilai_dpp')),
            'nilai_ppn' => $this->cleanCurrency($this->request->getPost('nilai_ppn')),
            'masa_pajak' => $this->request->getPost('masa_pajak'),
            'tahun_pajak' => $this->request->getPost('tahun_pajak'),
            'status_kredit' => $this->request->getPost('status_kredit')
        ];
        
        // Jika status kredit adalah Dikreditkan, set bulan dan tahun dikreditkan
        if ($data['status_kredit'] === 'Dikreditkan') {
            $data['bulan_dikreditkan'] = $this->request->getPost('bulan_dikreditkan') ?: $data['masa_pajak'];
            $data['tahun_dikreditkan'] = $this->request->getPost('tahun_dikreditkan') ?: $data['tahun_pajak'];
        } else {
            $data['bulan_dikreditkan'] = null;
            $data['tahun_dikreditkan'] = null;
        }
        
        try {
            $this->db->transBegin();
            
            $updated = $this->ppnMasukanModel->save($data);
            
            if (!$updated) {
                throw new \Exception('Gagal mengupdate data: ' . json_encode($this->ppnMasukanModel->errors()));
            }
            
            $this->db->transCommit();
            
            return redirect()->to('accounting/manajemen-pajak/ppn-masukan')
                ->with('success', 'PPN Masukan berhasil diupdate.');
                
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal mengupdate PPN Masukan: ' . $e->getMessage());
        }
    }

    /**
     * Hapus PPN Masukan
     */
    public function delete($id)
    {
        $isAjax = $this->request->isAJAX();
        
        $ppnMasukan = $this->ppnMasukanModel->find($id);
        
        if (!$ppnMasukan) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data PPN masukan tidak ditemukan'
                ]);
            } else {
                return redirect()->to('accounting/manajemen-pajak/ppn-masukan')
                    ->with('error', 'Data PPN masukan tidak ditemukan');
            }
        }
        
        try {
            $this->db->transBegin();
            
            $deleted = $this->ppnMasukanModel->delete($id);
            
            if (!$deleted) {
                throw new \Exception('Gagal menghapus data');
            }
            
            $this->db->transCommit();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'PPN Masukan berhasil dihapus',
                    'redirect' => site_url('accounting/manajemen-pajak/ppn-masukan')
                ]);
            } else {
                return redirect()->to('accounting/manajemen-pajak/ppn-masukan')
                    ->with('success', 'PPN Masukan berhasil dihapus');
            }
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus PPN Masukan: ' . $e->getMessage()
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal menghapus PPN Masukan: ' . $e->getMessage());
            }
        }
    }

    /**
     * Credit PPN Masukan (tandai sebagai dikreditkan)
     */
    public function credit($id)
    {
        $isAjax = $this->request->isAJAX();
        
        $ppnMasukan = $this->ppnMasukanModel->find($id);
        
        if (!$ppnMasukan) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data PPN masukan tidak ditemukan'
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Data PPN masukan tidak ditemukan');
            }
        }
        
        if ($ppnMasukan['status_kredit'] !== 'Belum Dikreditkan') {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Hanya PPN dengan status Belum Dikreditkan yang dapat dikreditkan'
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Hanya PPN dengan status Belum Dikreditkan yang dapat dikreditkan');
            }
        }
        
        $bulanKredit = $this->request->getPost('bulan_kredit') ?: date('m');
        $tahunKredit = $this->request->getPost('tahun_kredit') ?: date('Y');
        
        try {
            $result = $this->ppnMasukanModel->credit($id, $bulanKredit, $tahunKredit);
            
            if (!$result) {
                throw new \Exception('Gagal mengkreditkan PPN');
            }
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'PPN Masukan berhasil dikreditkan'
                ]);
            } else {
                return redirect()->back()
                    ->with('success', 'PPN Masukan berhasil dikreditkan');
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
     * Mark as not creditable (tidak dikreditkan)
     */
    public function markAsNotCreditable($id)
    {
        $isAjax = $this->request->isAJAX();
        $alasan = $this->request->getPost('alasan');
        
        $ppnMasukan = $this->ppnMasukanModel->find($id);
        
        if (!$ppnMasukan) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data PPN masukan tidak ditemukan'
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Data PPN masukan tidak ditemukan');
            }
        }
        
        if ($ppnMasukan['status_kredit'] !== 'Belum Dikreditkan') {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Hanya PPN dengan status Belum Dikreditkan yang dapat ditetapkan tidak dikreditkan'
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Hanya PPN dengan status Belum Dikreditkan yang dapat ditetapkan tidak dikreditkan');
            }
        }
        
        try {
            $result = $this->ppnMasukanModel->markAsNotCreditable($id, $alasan);
            
            if (!$result) {
                throw new \Exception('Gagal menetapkan PPN sebagai tidak dikreditkan');
            }
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'PPN Masukan ditetapkan sebagai tidak dikreditkan'
                ]);
            } else {
                return redirect()->back()
                    ->with('success', 'PPN Masukan ditetapkan sebagai tidak dikreditkan');
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
            'status_kredit' => $this->request->getGet('status_kredit'),
            'tahun' => $this->request->getGet('tahun'),
            'bulan' => $this->request->getGet('bulan')
        ];
        
        session()->set('filter_ppn_masukan', $filters);
        
        return redirect()->to('accounting/manajemen-pajak/ppn-masukan');
    }

    /**
     * Search data
     */
    public function search()
    {
        $search = $this->request->getGet('q');
        
        $filters = session()->get('filter_ppn_masukan') ?? [];
        $filters['search'] = $search;
        
        session()->set('filter_ppn_masukan', $filters);
        
        return redirect()->to('accounting/manajemen-pajak/ppn-masukan');
    }

    /**
     * Reset filter
     */
    public function resetFilter()
    {
        session()->remove('filter_ppn_masukan');
        
        return redirect()->to('accounting/manajemen-pajak/ppn-masukan');
    }

    /**
     * Export data
     */
    public function export()
    {
        $type = $this->request->getGet('type') ?? 'excel';
        $filters = [
            'status_kredit' => $this->request->getGet('status_kredit'),
            'tahun' => $this->request->getGet('tahun')
        ];
        
        $data = $this->ppnMasukanModel->getExportData($filters);
        
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
                ->setTitle("Daftar PPN Masukan")
                ->setSubject("Daftar PPN Masukan")
                ->setDescription("Daftar PPN Masukan " . date('d-m-Y'));
            
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Daftar PPN Masukan');
            
            // Header laporan
            $sheet->mergeCells('A1:K1');
            $sheet->setCellValue('A1', 'DAFTAR PPN MASUKAN');
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
                'D' => 'Supplier',
                'E' => 'NPWP Supplier',
                'F' => 'Nilai DPP',
                'G' => 'Nilai PPN',
                'H' => 'Masa Pajak',
                'I' => 'Status Kredit',
                'J' => 'Dikreditkan Pada',
                'K' => 'Keterangan'
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
                $sheet->setCellValue('D' . $row, $item['Supplier']);
                $sheet->setCellValue('E' . $row, $item['NPWP Supplier']);
                $sheet->setCellValue('F' . $row, $item['Nilai DPP']);
                $sheet->setCellValue('G' . $row, $item['Nilai PPN']);
                $sheet->setCellValue('H' . $row, $item['Masa Pajak']);
                $sheet->setCellValue('I' . $row, $item['Status Kredit']);
                $sheet->setCellValue('J' . $row, $item['Dikreditkan Pada']);
                $sheet->setCellValue('K' . $row, $item['Keterangan'] ?? '-');
                
                // Format angka
                $sheet->getStyle('F' . $row . ':G' . $row)->getNumberFormat()
                    ->setFormatCode('"Rp" #,##0.00');
                
                // Warna status
                if ($item['Status Kredit'] == 'Dikreditkan') {
                    $sheet->getStyle('I' . $row)->getFont()->getColor()->setARGB('FF008000');
                } elseif ($item['Status Kredit'] == 'Tidak Dikreditkan') {
                    $sheet->getStyle('I' . $row)->getFont()->getColor()->setARGB('FFFF0000');
                } else {
                    $sheet->getStyle('I' . $row)->getFont()->getColor()->setARGB('FFFFA500');
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
            $filename = 'Daftar_PPN_Masukan_' . date('Ymd_His') . '.xlsx';
            
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
            
            $filename = 'Daftar_PPN_Masukan_' . date('Ymd_His') . '.pdf';
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
        if (!empty($filters['status_kredit'])) {
            $filterText .= 'Status: ' . $filters['status_kredit'] . ' | ';
        }
        if (!empty($filters['tahun'])) {
            $filterText .= 'Tahun: ' . $filters['tahun'];
        }
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Daftar PPN Masukan</title>
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
                .text-warning {
                    color: #ffc107;
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
                <h1>DAFTAR PPN MASUKAN</h1>
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
                        <th width="15%">Supplier</th>
                        <th width="10%">NPWP</th>
                        <th width="8%">Nilai DPP</th>
                        <th width="8%">Nilai PPN</th>
                        <th width="6%">Masa</th>
                        <th width="8%">Status</th>
                        <th width="8%">Dikreditkan</th>
                        <th width="18%">Keterangan</th>
                    </tr>
                </thead>
                <tbody>';
        
        if (empty($data)) {
            $html .= '<tr><td colspan="11" class="text-center">Tidak ada data PPN masukan</td></tr>';
        } else {
            $no = 1;
            foreach ($data as $item) {
                $statusClass = match($item['Status Kredit']) {
                    'Dikreditkan' => 'text-success',
                    'Tidak Dikreditkan' => 'text-danger',
                    default => 'text-warning'
                };
                
                $html .= '
                    <tr>
                        <td class="text-center">' . $no . '</td>
                        <td>' . $item['Nomor Faktur'] . '</td>
                        <td class="text-center">' . $item['Tanggal Faktur'] . '</td>
                        <td>' . $item['Supplier'] . '</td>
                        <td>' . $item['NPWP Supplier'] . '</td>
                        <td class="text-end">Rp ' . number_format($item['Nilai DPP'], 0) . '</td>
                        <td class="text-end">Rp ' . number_format($item['Nilai PPN'], 0) . '</td>
                        <td class="text-center">' . $item['Masa Pajak'] . '</td>
                        <td class="text-center ' . $statusClass . '">' . $item['Status Kredit'] . '</td>
                        <td class="text-center">' . $item['Dikreditkan Pada'] . '</td>
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
        
        $faktur = $this->fakturPajakModel->find($fakturId);
        
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
                'tahun_pajak' => $faktur['tahun_pajak']
            ]
        ]);
    }

    /**
     * AJAX: Get summary for SPT Masa
     */
    public function ajaxGetSummary()
    {
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        
        $summary = $this->ppnMasukanModel->getTotalPerMasa($tahun);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $summary,
            'tahun' => $tahun
        ]);
    }

    /**
     * AJAX: Get expiring soon
     */
    public function ajaxGetExpiringSoon()
    {
        $bulanKeDepan = $this->request->getGet('months') ?? 3;
        
        $expiring = $this->ppnMasukanModel->getExpiringSoon($bulanKeDepan);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $expiring,
            'count' => count($expiring)
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