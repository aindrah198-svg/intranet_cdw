<?php
// app/Controllers/Direktur/Approval/BastController.php

namespace App\Controllers\Direktur\Approval;

use App\Controllers\BaseController;
use App\Models\Direktur\BastModel;
use App\Models\KaryawanModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

class BastController extends BaseController
{
    use ResponseTrait;
    
    protected $bastModel;
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
        
        $this->bastModel = new BastModel();
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
     * Display list of BAST for direktur approval
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
        $kondisiFilter = $this->request->getGet('kondisi');
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
        $builder = $db->table('form_bast');
        
        $builder->select('
            form_bast.*,
            client.nama_perusahaan,
            client.kode_client,
            spk_instalasi.nomor_spk
        ');
        
        $builder->join('client', 'client.id = form_bast.client_id');
        $builder->join('spk_instalasi', 'spk_instalasi.id = form_bast.spk_id', 'left');
        $builder->where('form_bast.deleted_at', null);
        
        // Filter status untuk approval direktur
        if ($statusFilter === 'pending') {
            $builder->where('form_bast.status_direktur', 'Menunggu');
            $builder->where('form_bast.status_hrd', 'Disetujui HRD');
        } elseif ($statusFilter === 'approved') {
            $builder->where('form_bast.status_direktur', 'Disetujui');
        } elseif ($statusFilter === 'rejected') {
            $builder->where('form_bast.status_direktur', 'Ditolak');
        } else {
            // Default: tampilkan yang menunggu, disetujui, ditolak
            $builder->whereIn('form_bast.status_direktur', ['Menunggu', 'Disetujui', 'Ditolak']);
            $builder->where('form_bast.status_hrd !=', 'Draft');
        }
        
        // Apply kondisi filter
        if ($kondisiFilter) {
            $builder->where('form_bast.kondisi', $kondisiFilter);
        }
        
        // Apply search filter
        if ($searchQuery) {
            $builder->groupStart()
                    ->like('form_bast.nomor_bast', $searchQuery)
                    ->orLike('client.nama_perusahaan', $searchQuery)
                    ->orLike('form_bast.judul_pekerjaan', $searchQuery)
                    ->groupEnd();
        }
        
        // Apply date filter
        if ($startDate && $endDate) {
            $builder->where('form_bast.tanggal_bast >=', $startDate);
            $builder->where('form_bast.tanggal_bast <=', $endDate);
        }
        
        // Clone builder for total count
        $totalBuilder = clone $builder;
        $totalData = $totalBuilder->countAllResults();
        
        // Apply pagination and get results
        $builder->limit($perPage, $offset);
        $builder->orderBy('form_bast.tanggal_bast', 'DESC');
        
        $bastData = $builder->get()->getResultArray();
        
        // Get statistics
        $stats = $this->bastModel->getStatistics($startDate, $endDate);
        
        // Get kondisi list for filter
        $kondisiList = $this->bastModel->getKondisiList();
        
        // Calculate pagination
        $totalPages = ceil($totalData / $perPage);
        
        // Build base URL for pagination links
        $queryParams = [
            'status' => $statusFilter,
            'kondisi' => $kondisiFilter,
            'search' => $searchQuery,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
        
        $baseUrl = base_url('direktur/approval/bast') . '?' . http_build_query(array_filter($queryParams));
        
        // Get user data for navbar
        $userId = $session->get('user_id');
        $userData = $this->userModel->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
            ->select('users.*, karyawan.nama_lengkap as karyawan_nama, karyawan.nama_panggilan')
            ->where('users.id', $userId)
            ->first();
        
        // Prepare data for view
        $data = [
            'title' => 'Approval BAST',
            'subtitle' => 'Persetujuan Berita Acara Serah Terima oleh Direktur',
            'active' => 'approval',
            'bastData' => $bastData,
            'kondisiList' => $kondisiList,
            'statusFilter' => $statusFilter,
            'kondisiFilter' => $kondisiFilter,
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
            'pendingCount' => $this->bastModel->getPendingCount()
        ];
        
        // Return view with templates
        return view('direktur/templates/header', $data)
             . view('direktur/templates/sidebar', $data)
             . view('direktur/templates/navbar', $data)
             . view('direktur/approval/bast', $data)
             . view('direktur/templates/footer', $data);
    }

    /**
     * Display BAST detail
     */
    public function detail($id)
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        
        // Get BAST detail with complete info
        $bast = $this->bastModel->getDetailById($id);
        
        if (!$bast) {
            return redirect()->to(base_url('direktur/approval/bast'))
                ->with('error', 'Data BAST tidak ditemukan');
        }
        
        // Get user data for navbar
        $userId = $session->get('user_id');
        $userData = $this->userModel->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
            ->select('users.*, karyawan.nama_lengkap as karyawan_nama')
            ->where('users.id', $userId)
            ->first();
        
        $data = [
            'title' => 'Detail BAST',
            'subtitle' => 'Detail Berita Acara Serah Terima',
            'active' => 'approval',
            'bast' => $bast,
            'user' => $userData,
            'pendingCount' => $this->bastModel->getPendingCount()
        ];
        
        return view('direktur/templates/header', $data)
             . view('direktur/templates/sidebar', $data)
             . view('direktur/templates/navbar', $data)
             . view('direktur/approval/bast_detail', $data)
             . view('direktur/templates/footer', $data);
    }

    /**
     * Approve BAST
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
        
        // Check if BAST exists and needs approval
        $bast = $this->bastModel->find($id);
        if (!$bast) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data BAST tidak ditemukan']);
        }
        
        if ($bast['status_direktur'] !== 'Menunggu') {
            return $this->response->setJSON(['success' => false, 'message' => 'BAST ini sudah diproses']);
        }
        
        if ($bast['status_hrd'] !== 'Disetujui HRD') {
            return $this->response->setJSON(['success' => false, 'message' => 'BAST belum disetujui HRD']);
        }
        
        // Approve BAST
        $result = $this->bastModel->approveByDirektur($id, $this->userId, $this->karyawanId);
        
        if ($result) {
            // Log activity
            log_message('info', "Direktur approved BAST ID: {$id} by user: {$this->userId}");
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'BAST berhasil disetujui',
                'redirect' => base_url('direktur/approval/bast')
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyetujui BAST'
            ]);
        }
    }

    /**
     * Reject BAST
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
        
        // Check if BAST exists
        $bast = $this->bastModel->find($id);
        if (!$bast) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data BAST tidak ditemukan']);
        }
        
        if ($bast['status_direktur'] !== 'Menunggu') {
            return $this->response->setJSON(['success' => false, 'message' => 'BAST ini sudah diproses']);
        }
        
        // Reject BAST
        $result = $this->bastModel->rejectByDirektur($id, $this->userId, $this->karyawanId, $alasan);
        
        if ($result) {
            // Log activity
            log_message('info', "Direktur rejected BAST ID: {$id} by user: {$this->userId}");
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'BAST berhasil ditolak',
                'redirect' => base_url('direktur/approval/bast')
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menolak BAST'
            ]);
        }
    }
    
    /**
     * Batch approve multiple BAST
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
            $bast = $this->bastModel->find($id);
            if ($bast && $bast['status_direktur'] === 'Menunggu' && $bast['status_hrd'] === 'Disetujui HRD') {
                $result = $this->bastModel->approveByDirektur($id, $this->userId, $this->karyawanId);
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
            'message' => "{$successCount} BAST berhasil disetujui, {$failCount} gagal",
            'success_count' => $successCount,
            'fail_count' => $failCount,
            'redirect' => base_url('direktur/approval/bast')
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
        $exportData = $this->bastModel->getForExport($startDate, $endDate, $statusFilter);
        
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
            ->setTitle("Laporan BAST")
            ->setSubject("Export Data BAST")
            ->setDescription("Laporan Berita Acara Serah Terima periode $startDate s/d $endDate");
        
        // Set headers
        $headers = [
            'No', 'No. BAST', 'Tanggal BAST', 'Client', 'No. SPK', 'Judul Pekerjaan',
            'Lokasi Pekerjaan', 'Kondisi', 'Status HRD', 'Status Direktur', 'Status'
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
        
        foreach ($exportData as $data) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $data['nomor_bast']);
            $sheet->setCellValue('C' . $row, date('d/m/Y', strtotime($data['tanggal_bast'])));
            $sheet->setCellValue('D' . $row, $data['nama_perusahaan']);
            $sheet->setCellValue('E' . $row, $data['nomor_spk'] ?? '-');
            $sheet->setCellValue('F' . $row, substr($data['judul_pekerjaan'], 0, 100));
            $sheet->setCellValue('G' . $row, substr($data['lokasi_pekerjaan'] ?? '-', 0, 100));
            $sheet->setCellValue('H' . $row, $data['kondisi'] ?? '-');
            $sheet->setCellValue('I' . $row, $data['status_hrd'] ?? '-');
            $sheet->setCellValue('J' . $row, $data['status_direktur'] ?? '-');
            $sheet->setCellValue('K' . $row, $data['status_keseluruhan'] ?? '-');
            $row++;
        }
        
        // Auto size columns
        foreach (range('A', 'K') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Set filename
        $filename = 'Laporan_BAST_' . date('Ymd_His') . '.xlsx';
        
        // Create writer and output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit();
    }

    /**
     * Print BAST detail
     */
    public function print($id)
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        
        $bast = $this->bastModel->getDetailById($id);
        
        if (!$bast) {
            return redirect()->to(base_url('direktur/approval/bast'))
                ->with('error', 'Data BAST tidak ditemukan');
        }
        
        $data = [
            'title' => 'Cetak BAST',
            'bast' => $bast,
            'user' => $session->get()
        ];
        
        return view('direktur/approval/bast_print', $data);
    }
}