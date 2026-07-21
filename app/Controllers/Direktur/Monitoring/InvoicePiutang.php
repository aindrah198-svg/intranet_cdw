<?php

namespace App\Controllers\Direktur\Monitoring;

use App\Controllers\BaseController;
use App\Models\Direktur\InvoicePiutangModel;
use App\Models\Direktur\PembayaranPiutangModel;
use App\Models\ClientModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

class InvoicePiutang extends BaseController
{
    use ResponseTrait;
    
    protected $invoiceModel;
    protected $paymentModel;
    protected $clientModel;
    protected $userModel;
    
    /**
     * Initialize controller
     */
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, 
                                 \CodeIgniter\HTTP\ResponseInterface $response, 
                                 \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        $this->invoiceModel = new InvoicePiutangModel();
        $this->paymentModel = new PembayaranPiutangModel();
        $this->clientModel = new ClientModel();
        $this->userModel = new UserModel();
    }

    /**
     * Display invoice list for direktur (monitoring only)
     */
    public function index()
    {
        // Cek session
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        
        // Get filter parameters
        $statusFilter = $this->request->getGet('status');
        $clientIdFilter = $this->request->getGet('client_id');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $searchQuery = $this->request->getGet('search');
        $page = $this->request->getGet('page') ?? 1;
        
        // Get per page setting
        $perPage = 20;
        $currentPage = (int) $page;
        $offset = ($currentPage - 1) * $perPage;
        
        // Build query
        $db = \Config\Database::connect();
        $builder = $db->table('invoice_piutang');
        
        $builder->select('invoice_piutang.*, client.nama_perusahaan, client.nama_kontak, client.telepon')
                ->join('client', 'client.id = invoice_piutang.client_id')
                ->where('invoice_piutang.deleted_at', null);
        
        // Apply filters
        if ($statusFilter) {
            $builder->where('invoice_piutang.status', $statusFilter);
        }
        
        if ($clientIdFilter) {
            $builder->where('invoice_piutang.client_id', $clientIdFilter);
        }
        
        if ($startDate) {
            $builder->where('invoice_piutang.tanggal_invoice >=', $startDate);
        }
        
        if ($endDate) {
            $builder->where('invoice_piutang.tanggal_invoice <=', $endDate);
        }
        
        if ($searchQuery) {
            $builder->groupStart()
                    ->like('invoice_piutang.nomor_invoice', $searchQuery)
                    ->orLike('client.nama_perusahaan', $searchQuery)
                    ->orLike('client.nama_kontak', $searchQuery)
                    ->groupEnd();
        }
        
        // Clone builder for total count
        $totalBuilder = clone $builder;
        $totalData = $totalBuilder->countAllResults();
        
        // Apply pagination and get results
        $builder->limit($perPage, $offset);
        $builder->orderBy('invoice_piutang.tanggal_invoice', 'DESC');
        
        $invoiceData = $builder->get()->getResultArray();
        
        // Get statistics
        $stats = $this->invoiceModel->getSummaryStats();
        
        // Get aging report
        $agingReport = $this->invoiceModel->getAgingReport();
        
        // Get summary by client
        $summaryByClient = $this->invoiceModel->getSummaryByClient();
        
        // Get client list for dropdown
        $clientList = $this->clientModel->select('id, nama_perusahaan, nama_kontak')
                                      ->where('deleted_at', null)
                                      ->where('status', 'active')
                                      ->orderBy('nama_perusahaan', 'ASC')
                                      ->findAll();
        
        // Get status options
        $statusOptions = InvoicePiutangModel::$statusOptions;
        
        // Calculate summary totals for aging
        $agingSummary = [
            'current' => 0,
            '31_60' => 0,
            '61_90' => 0,
            '90_plus' => 0
        ];
        
        foreach ($agingReport as $key => $invoices) {
            foreach ($invoices as $inv) {
                $agingSummary[$key] += $inv['sisa_piutang'] ?? 0;
            }
        }
        
        // Calculate pagination
        $totalPages = ceil($totalData / $perPage);
        
        // Build base URL for pagination links
        $queryParams = [
            'status' => $statusFilter,
            'client_id' => $clientIdFilter,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'search' => $searchQuery
        ];
        
        $baseUrl = base_url('direktur/monitoring/invoice-piutang') . '?' . http_build_query(array_filter($queryParams));
        
        // Get user data for navbar
        $userId = $session->get('user_id');
        $userData = $this->userModel->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
            ->select('users.*, karyawan.nama_lengkap as karyawan_nama, karyawan.nama_panggilan')
            ->where('users.id', $userId)
            ->first();
        
        // Prepare data for view
        $data = [
            'title' => 'Monitoring Invoice & Piutang',
            'subtitle' => 'Pantau Tagihan dan Piutang Client',
            'active' => 'monitoring',
            'invoiceData' => $invoiceData,
            'clientList' => $clientList,
            'statusFilter' => $statusFilter,
            'clientIdFilter' => $clientIdFilter,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'searchQuery' => $searchQuery,
            'currentPage' => $currentPage,
            'perPage' => $perPage,
            'totalData' => $totalData,
            'totalPages' => $totalPages,
            'baseUrl' => $baseUrl,
            'queryParams' => $queryParams,
            'user' => $userData,
            'stats' => $stats,
            'statusOptions' => $statusOptions,
            'agingReport' => $agingReport,
            'agingSummary' => $agingSummary,
            'summaryByClient' => $summaryByClient
        ];
        
        // Return view dengan include template
        return view('direktur/templates/header', $data)
             . view('direktur/templates/sidebar', $data)
             . view('direktur/templates/navbar', $data)
             . view('direktur/monitoring/invoice_piutang', $data)
             . view('direktur/templates/footer', $data);
    }

    /**
     * Display invoice detail
     */
    public function detail($id)
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        
        // Get invoice data with client info
        $invoice = $this->invoiceModel->getWithClient($id);
        
        if (!$invoice) {
            return redirect()->to(base_url('direktur/monitoring/invoice-piutang'))
                ->with('error', 'Data invoice tidak ditemukan');
        }
        
        // Get payment history for this invoice
        $payments = $this->paymentModel->getByInvoice($id);
        
        // Get user data for navbar
        $userId = $session->get('user_id');
        $userData = $this->userModel->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
            ->select('users.*, karyawan.nama_lengkap as karyawan_nama')
            ->where('users.id', $userId)
            ->first();
        
        // Prepare data for view
        $data = [
            'title' => 'Detail Invoice & Piutang',
            'active' => 'monitoring',
            'invoice' => $invoice,
            'payments' => $payments,
            'statusOptions' => InvoicePiutangModel::$statusOptions,
            'metodeOptions' => PembayaranPiutangModel::$metodeOptions,
            'user' => $userData
        ];
        
        return view('direktur/templates/header', $data)
             . view('direktur/templates/sidebar', $data)
             . view('direktur/templates/navbar', $data)
             . view('direktur/monitoring/invoice_piutang_detail', $data)
             . view('direktur/templates/footer', $data);
    }

    /**
     * Get invoice summary for chart (AJAX)
     */
    public function getSummary()
    {
        if (!$this->request->isAJAX()) {
            return $this->fail('Only AJAX requests are allowed', 400);
        }
        
        $stats = $this->invoiceModel->getSummaryStats();
        $monthlySummary = $this->invoiceModel->getMonthlySummary(date('Y'));
        $agingReport = $this->invoiceModel->getAgingReport();
        
        // Calculate aging totals
        $agingTotals = [
            'current' => 0,
            '31_60' => 0,
            '61_90' => 0,
            '90_plus' => 0
        ];
        
        foreach ($agingReport as $key => $invoices) {
            foreach ($invoices as $inv) {
                $agingTotals[$key] += $inv['sisa_piutang'] ?? 0;
            }
        }
        
        return $this->respond([
            'status' => 'success',
            'stats' => $stats,
            'monthlySummary' => $monthlySummary,
            'agingTotals' => $agingTotals
        ]);
    }

    /**
     * Export data to Excel
     */
    public function exportExcel()
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        
        // Get filter parameters
        $statusFilter = $this->request->getGet('status');
        $clientIdFilter = $this->request->getGet('client_id');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        
        // Build query
        $db = \Config\Database::connect();
        $builder = $db->table('invoice_piutang');
        
        $builder->select('invoice_piutang.*, client.nama_perusahaan, client.nama_kontak, client.telepon, client.alamat')
                ->join('client', 'client.id = invoice_piutang.client_id')
                ->where('invoice_piutang.deleted_at', null);
        
        if ($statusFilter) {
            $builder->where('invoice_piutang.status', $statusFilter);
        }
        
        if ($clientIdFilter) {
            $builder->where('invoice_piutang.client_id', $clientIdFilter);
        }
        
        if ($startDate) {
            $builder->where('invoice_piutang.tanggal_invoice >=', $startDate);
        }
        
        if ($endDate) {
            $builder->where('invoice_piutang.tanggal_invoice <=', $endDate);
        }
        
        $builder->orderBy('invoice_piutang.tanggal_invoice', 'DESC');
        
        $invoiceData = $builder->get()->getResultArray();
        
        // Check if PhpSpreadsheet is available
        if (!class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            return redirect()->back()
                ->with('error', 'Fitur Excel export membutuhkan PhpSpreadsheet. Install dengan: <code>composer require phpoffice/phpspreadsheet</code>');
        }
        
        // Load PhpSpreadsheet library
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator("CDW Engineering Accounting System")
            ->setLastModifiedBy("CDW Engineering Accounting System")
            ->setTitle("Laporan Invoice & Piutang")
            ->setSubject("Export Data Invoice")
            ->setDescription("Laporan invoice dan piutang");
        
        // Set headers
        $headers = [
            'No', 'Nomor Invoice', 'Tanggal Invoice', 'Jatuh Tempo', 'Client',
            'Deskripsi', 'Subtotal', 'PPN', 'Total', 'Sisa Piutang', 'Status'
        ];
        
        // Write headers
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }
        
        // Style headers
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ];
        
        $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);
        
        // Write data
        $row = 2;
        $no = 1;
        
        foreach ($invoiceData as $data) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $data['nomor_invoice']);
            $sheet->setCellValue('C' . $row, date('d/m/Y', strtotime($data['tanggal_invoice'])));
            $sheet->setCellValue('D' . $row, date('d/m/Y', strtotime($data['tanggal_jatuh_tempo'])));
            $sheet->setCellValue('E' . $row, $data['nama_perusahaan']);
            $sheet->setCellValue('F' . $row, substr($data['deskripsi'] ?? '-', 0, 100));
            $sheet->setCellValue('G' . $row, number_format($data['subtotal'] ?? 0, 0, ',', '.'));
            $sheet->setCellValue('H' . $row, number_format($data['ppn'] ?? 0, 0, ',', '.'));
            $sheet->setCellValue('I' . $row, number_format($data['total'] ?? 0, 0, ',', '.'));
            $sheet->setCellValue('J' . $row, number_format($data['sisa_piutang'] ?? 0, 0, ',', '.'));
            $sheet->setCellValue('K' . $row, $this->invoiceModel->getStatusLabel($data['status']));
            
            $row++;
        }
        
        // Auto size columns
        foreach (range('A', 'K') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Set filename
        $filename = 'Laporan_Invoice_Piutang_' . date('Ymd_His') . '.xlsx';
        
        // Create writer and output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit();
    }

    /**
     * Print invoice report (view only)
     */
    public function print()
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        
        // Get filter parameters
        $statusFilter = $this->request->getGet('status');
        $clientIdFilter = $this->request->getGet('client_id');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        
        // Build query
        $db = \Config\Database::connect();
        $builder = $db->table('invoice_piutang');
        
        $builder->select('invoice_piutang.*, client.nama_perusahaan, client.nama_kontak, client.telepon')
                ->join('client', 'client.id = invoice_piutang.client_id')
                ->where('invoice_piutang.deleted_at', null);
        
        if ($statusFilter) {
            $builder->where('invoice_piutang.status', $statusFilter);
        }
        
        if ($clientIdFilter) {
            $builder->where('invoice_piutang.client_id', $clientIdFilter);
        }
        
        if ($startDate) {
            $builder->where('invoice_piutang.tanggal_invoice >=', $startDate);
        }
        
        if ($endDate) {
            $builder->where('invoice_piutang.tanggal_invoice <=', $endDate);
        }
        
        $builder->orderBy('invoice_piutang.tanggal_invoice', 'DESC');
        
        $invoiceData = $builder->get()->getResultArray();
        
        // Get statistics
        $stats = $this->invoiceModel->getSummaryStats();
        
        // Get selected client info if filtered
        $selectedClient = null;
        if ($clientIdFilter) {
            $selectedClient = $this->clientModel->find($clientIdFilter);
        }
        
        // Prepare data for view
        $data = [
            'title' => 'Cetak Laporan Invoice & Piutang',
            'active' => 'monitoring',
            'invoiceData' => $invoiceData,
            'statusFilter' => $statusFilter,
            'clientIdFilter' => $clientIdFilter,
            'selectedClient' => $selectedClient,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'stats' => $stats,
            'statusOptions' => InvoicePiutangModel::$statusOptions,
            'user' => $session->get()
        ];
        
        // Return the print view (simple HTML for printing)
        return view('direktur/monitoring/invoice_piutang_print', $data);
    }

    /**
     * Get aging report for chart (AJAX)
     */
    public function getAgingReport()
    {
        if (!$this->request->isAJAX()) {
            return $this->fail('Only AJAX requests are allowed', 400);
        }
        
        $agingReport = $this->invoiceModel->getAgingReport();
        
        $agingTotals = [
            'current' => 0,
            '31_60' => 0,
            '61_90' => 0,
            '90_plus' => 0
        ];
        
        foreach ($agingReport as $key => $invoices) {
            foreach ($invoices as $inv) {
                $agingTotals[$key] += $inv['sisa_piutang'] ?? 0;
            }
        }
        
        return $this->respond([
            'status' => 'success',
            'data' => $agingTotals
        ]);
    }
}