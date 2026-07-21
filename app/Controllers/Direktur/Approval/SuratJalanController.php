<?php
// app/Controllers/Direktur/Approval/SuratJalanController.php

namespace App\Controllers\Direktur\Approval;

use App\Controllers\BaseController;
use App\Models\Direktur\SuratJalanModel;
use App\Models\KaryawanModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

class SuratJalanController extends BaseController
{
    use ResponseTrait;
    
    protected $suratJalanModel;
    protected $karyawanModel;
    protected $userModel;
    protected $karyawanId;
    protected $userId;
    
    /**
     * Initialize controller
     */
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, 
                                 \CodeIgniter\HTTP\ResponseInterface $response, 
                                 \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        $this->suratJalanModel = new SuratJalanModel();
        $this->karyawanModel = new KaryawanModel();
        $this->userModel = new UserModel();
        
        $session = \Config\Services::session();
        $userId = $session->get('user_id');
        $this->userId = $userId;
        
        if ($userId) {
            $user = $this->userModel->find($userId);
            $this->karyawanId = $user['karyawan_id'] ?? null;
        }
    }

    /**
     * Display list of surat jalan for direktur approval
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
        $searchQuery = $this->request->getGet('search');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $page = $this->request->getGet('page') ?? 1;
        
        // Set default date range (last 3 months)
        if (!$startDate) {
            $startDate = date('Y-m-d', strtotime('-3 months'));
        }
        if (!$endDate) {
            $endDate = date('Y-m-d');
        }
        
        // Get per page setting
        $perPage = 15;
        $currentPage = (int) $page;
        $offset = ($currentPage - 1) * $perPage;
        
        // Build query
        $db = \Config\Database::connect();
        $builder = $db->table('surat_jalan');
        
        $builder->select('
            surat_jalan.*,
            project.nama_project,
            project.kode_project,
            client.nama_perusahaan as client_nama,
            invoice.nomor_invoice
        ');
        
        $builder->join('project', 'project.id = surat_jalan.project_id', 'left');
        $builder->join('client', 'client.id = project.client_id', 'left');
        $builder->join('invoice', 'invoice.id = surat_jalan.invoice_id', 'left');
        
        // Filter status untuk approval direktur
        if ($statusFilter === 'pending') {
            $builder->where('surat_jalan.status_direktur', 'Menunggu');
            $builder->where('surat_jalan.status_hrd', 'Disetujui HRD');
        } elseif ($statusFilter === 'approved') {
            $builder->where('surat_jalan.status_direktur', 'Disetujui');
        } elseif ($statusFilter === 'rejected') {
            $builder->where('surat_jalan.status_direktur', 'Ditolak');
        } else {
            // Default: tampilkan yang menunggu, disetujui, ditolak
            $builder->whereIn('surat_jalan.status_direktur', ['Menunggu', 'Disetujui', 'Ditolak']);
            $builder->where('surat_jalan.status_hrd !=', 'Draft');
        }
        
        // Apply search filter
        if ($searchQuery) {
            $builder->groupStart()
                    ->like('surat_jalan.nomor_surat_jalan', $searchQuery)
                    ->orLike('client.nama_perusahaan', $searchQuery)
                    ->orLike('project.nama_project', $searchQuery)
                    ->orLike('surat_jalan.penerima_nama', $searchQuery)
                    ->groupEnd();
        }
        
        // Apply date filter
        if ($startDate && $endDate) {
            $builder->where('surat_jalan.tanggal_kirim >=', $startDate);
            $builder->where('surat_jalan.tanggal_kirim <=', $endDate);
        }
        
        // Clone builder for total count
        $totalBuilder = clone $builder;
        $totalData = $totalBuilder->countAllResults();
        
        // Apply pagination and get results
        $builder->limit($perPage, $offset);
        $builder->orderBy('surat_jalan.created_at', 'DESC');
        
        $suratJalanData = $builder->get()->getResultArray();
        
        // Get statistics
        $stats = $this->suratJalanModel->getStatistics($startDate, $endDate);
        
        // Calculate pagination
        $totalPages = ceil($totalData / $perPage);
        
        // Build base URL for pagination links
        $queryParams = [
            'status' => $statusFilter,
            'search' => $searchQuery,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
        
        $baseUrl = base_url('direktur/approval/surat-jalan') . '?' . http_build_query(array_filter($queryParams));
        
        // Get user data for navbar
        $userId = $session->get('user_id');
        $userData = $this->userModel->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
            ->select('users.*, karyawan.nama_lengkap as karyawan_nama, karyawan.nama_panggilan')
            ->where('users.id', $userId)
            ->first();
        
        // Prepare data for view
        $data = [
            'title' => 'Approval Surat Jalan',
            'subtitle' => 'Persetujuan Surat Jalan oleh Direktur',
            'active' => 'approval',
            'suratJalanData' => $suratJalanData,
            'statusFilter' => $statusFilter,
            'searchQuery' => $searchQuery,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'currentPage' => $currentPage,
            'perPage' => $perPage,
            'totalData' => $totalData,
            'totalPages' => $totalPages,
            'baseUrl' => $baseUrl,
            'queryParams' => $queryParams,
            'stats' => $stats,
            'user' => $userData,
            'pendingCount' => $this->suratJalanModel->getPendingCount()
        ];
        
        // Return view with templates
        return view('direktur/templates/header', $data)
             . view('direktur/templates/sidebar', $data)
             . view('direktur/templates/navbar', $data)
             . view('direktur/approval/surat_jalan', $data)
             . view('direktur/templates/footer', $data);
    }

    /**
     * Display surat jalan detail
     */
    public function detail($id)
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        
        // Get surat jalan detail with complete info
        $suratJalan = $this->suratJalanModel->getDetailById($id);
        
        if (!$suratJalan) {
            return redirect()->to(base_url('direktur/approval/surat-jalan'))
                ->with('error', 'Data surat jalan tidak ditemukan');
        }
        
        // Get items for this surat jalan
        $items = $this->suratJalanModel->getItems($id);
        
        // Get user data for navbar
        $userId = $session->get('user_id');
        $userData = $this->userModel->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
            ->select('users.*, karyawan.nama_lengkap as karyawan_nama')
            ->where('users.id', $userId)
            ->first();
        
        $data = [
            'title' => 'Detail Surat Jalan',
            'subtitle' => 'Detail Surat Jalan Pengiriman Barang',
            'active' => 'approval',
            'suratJalan' => $suratJalan,
            'items' => $items,
            'user' => $userData,
            'pendingCount' => $this->suratJalanModel->getPendingCount()
        ];
        
        return view('direktur/templates/header', $data)
             . view('direktur/templates/sidebar', $data)
             . view('direktur/templates/navbar', $data)
             . view('direktur/approval/surat_jalan_detail', $data)
             . view('direktur/templates/footer', $data);
    }

    /**
     * Approve surat jalan
     */
    public function approve($id)
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Session expired']);
        }
        
        // Check if AJAX request
        if (!$this->request->isAJAX()) {
            return redirect()->back()->with('error', 'Invalid request');
        }
        
        // Check if surat jalan exists and needs approval
        $suratJalan = $this->suratJalanModel->find($id);
        if (!$suratJalan) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data surat jalan tidak ditemukan']);
        }
        
        if ($suratJalan['status_direktur'] !== 'Menunggu') {
            return $this->response->setJSON(['success' => false, 'message' => 'Surat jalan ini sudah diproses']);
        }
        
        if ($suratJalan['status_hrd'] !== 'Disetujui HRD') {
            return $this->response->setJSON(['success' => false, 'message' => 'Surat jalan belum disetujui HRD']);
        }
        
        // Approve surat jalan
        $result = $this->suratJalanModel->approveByDirektur($id, $this->userId, $this->karyawanId);
        
        if ($result) {
            // Log activity
            log_message('info', "Direktur approved surat jalan ID: {$id} by user: {$this->userId}");
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Surat jalan berhasil disetujui',
                'redirect' => base_url('direktur/approval/surat-jalan')
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyetujui surat jalan'
            ]);
        }
    }

    /**
     * Reject surat jalan
     */
    public function reject($id)
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Session expired']);
        }
        
        // Check if AJAX request
        if (!$this->request->isAJAX()) {
            return redirect()->back()->with('error', 'Invalid request');
        }
        
        // Get reason from POST
        $alasan = $this->request->getPost('alasan');
        if (empty($alasan)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Alasan penolakan harus diisi'
            ]);
        }
        
        // Check if surat jalan exists
        $suratJalan = $this->suratJalanModel->find($id);
        if (!$suratJalan) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data surat jalan tidak ditemukan']);
        }
        
        if ($suratJalan['status_direktur'] !== 'Menunggu') {
            return $this->response->setJSON(['success' => false, 'message' => 'Surat jalan ini sudah diproses']);
        }
        
        // Reject surat jalan
        $result = $this->suratJalanModel->rejectByDirektur($id, $this->userId, $this->karyawanId, $alasan);
        
        if ($result) {
            // Log activity
            log_message('info', "Direktur rejected surat jalan ID: {$id} by user: {$this->userId}");
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Surat jalan berhasil ditolak',
                'redirect' => base_url('direktur/approval/surat-jalan')
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menolak surat jalan'
            ]);
        }
    }
    
    /**
     * Batch approve multiple surat jalan
     */
    public function batchApprove()
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Session expired']);
        }
        
        // Check if AJAX request
        if (!$this->request->isAJAX()) {
            return redirect()->back()->with('error', 'Invalid request');
        }
        
        $ids = $this->request->getPost('ids');
        if (empty($ids) || !is_array($ids)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada data yang dipilih'
            ]);
        }
        
        $successCount = 0;
        $failCount = 0;
        
        foreach ($ids as $id) {
            $suratJalan = $this->suratJalanModel->find($id);
            if ($suratJalan && $suratJalan['status_direktur'] === 'Menunggu' && $suratJalan['status_hrd'] === 'Disetujui HRD') {
                $result = $this->suratJalanModel->approveByDirektur($id, $this->userId, $this->karyawanId);
                if ($result) {
                    $successCount++;
                } else {
                    $failCount++;
                }
            } else {
                $failCount++;
            }
        }
        
        return $this->response->setJSON([
            'success' => true,
            'message' => "{$successCount} surat jalan berhasil disetujui, {$failCount} gagal",
            'success_count' => $successCount,
            'fail_count' => $failCount,
            'redirect' => base_url('direktur/approval/surat-jalan')
        ]);
    }

    /**
     * Export Excel
     */
    public function exportExcel()
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        
        // Get filter parameters
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $statusFilter = $this->request->getGet('status');
        
        if (!$startDate) {
            $startDate = date('Y-m-d', strtotime('-3 months'));
        }
        if (!$endDate) {
            $endDate = date('Y-m-d');
        }
        
        // Get data for export
        $exportData = $this->suratJalanModel->getForExport($startDate, $endDate, $statusFilter);
        
        // Check if PhpSpreadsheet is available
        if (!class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            return redirect()->back()
                ->with('error', 'Fitur Excel export membutuhkan PhpSpreadsheet. Install dengan: <code>composer require phpoffice/phpspreadsheet</code>');
        }
        
        // Load PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator("CDW Engineering")
            ->setLastModifiedBy("CDW Engineering")
            ->setTitle("Laporan Surat Jalan")
            ->setSubject("Export Data Surat Jalan")
            ->setDescription("Laporan surat jalan periode $startDate s/d $endDate");
        
        // Set headers
        $headers = [
            'No', 'No. Surat Jalan', 'Tanggal Kirim', 'Project', 'Client', 'No. Invoice',
            'Penerima', 'Alamat Pengiriman', 'Sopir', 'No. Kendaraan',
            'Status Pengiriman', 'Status HRD', 'Status Direktur'
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
        
        $sheet->getStyle('A1:M1')->applyFromArray($headerStyle);
        
        // Write data
        $row = 2;
        $no = 1;
        
        foreach ($exportData as $data) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $data['nomor_surat_jalan']);
            $sheet->setCellValue('C' . $row, date('d/m/Y', strtotime($data['tanggal_kirim'])));
            $sheet->setCellValue('D' . $row, $data['nama_project'] ?? '-');
            $sheet->setCellValue('E' . $row, $data['client_nama'] ?? '-');
            $sheet->setCellValue('F' . $row, $data['nomor_invoice'] ?? '-');
            $sheet->setCellValue('G' . $row, $data['penerima_nama'] ?? '-');
            $sheet->setCellValue('H' . $row, substr($data['alamat_pengiriman'] ?? '-', 0, 100));
            $sheet->setCellValue('I' . $row, $data['sopir'] ?? '-');
            $sheet->setCellValue('J' . $row, $data['no_kendaraan'] ?? '-');
            $sheet->setCellValue('K' . $row, $data['status'] ?? '-');
            $sheet->setCellValue('L' . $row, $data['status_hrd'] ?? '-');
            $sheet->setCellValue('M' . $row, $data['status_direktur'] ?? '-');
            $row++;
        }
        
        // Auto size columns
        foreach (range('A', 'M') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Set filename
        $filename = 'Laporan_Surat_Jalan_' . date('Ymd_His') . '.xlsx';
        
        // Create writer and output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit();
    }

    /**
     * Print surat jalan detail
     */
    public function print($id)
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        
        $suratJalan = $this->suratJalanModel->getDetailById($id);
        
        if (!$suratJalan) {
            return redirect()->to(base_url('direktur/approval/surat-jalan'))
                ->with('error', 'Data surat jalan tidak ditemukan');
        }
        
        $items = $this->suratJalanModel->getItems($id);
        
        $data = [
            'title' => 'Cetak Surat Jalan',
            'suratJalan' => $suratJalan,
            'items' => $items,
            'user' => $session->get()
        ];
        
        return view('direktur/approval/surat_jalan_print', $data);
    }
}