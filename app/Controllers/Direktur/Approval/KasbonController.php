<?php
// app/Controllers/Direktur/Approval/KasbonController.php

namespace App\Controllers\Direktur\Approval;

use App\Controllers\BaseController;
use App\Models\Direktur\KasbonModel;
use App\Models\KaryawanModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

class KasbonController extends BaseController
{
    use ResponseTrait;
    
    protected $kasbonModel;
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
        
        $this->kasbonModel = new KasbonModel();
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
     * Display list of kasbon for direktur approval
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
        $builder = $db->table('form_kasbon');
        
        $builder->select('
            form_kasbon.*,
            karyawan.nik,
            karyawan.nama_lengkap,
            karyawan.nama_panggilan,
            karyawan.jabatan,
            karyawan.departemen,
            karyawan.foto
        ');
        
        $builder->join('karyawan', 'karyawan.id = form_kasbon.karyawan_id');
        $builder->where('form_kasbon.deleted_at', null);
        
        // Filter status untuk approval direktur
        if ($statusFilter === 'pending') {
            $builder->where('form_kasbon.status_direktur', 'Menunggu');
            $builder->where('form_kasbon.status_hrd', 'Disetujui HRD');
        } elseif ($statusFilter === 'approved') {
            $builder->where('form_kasbon.status_direktur', 'Disetujui');
        } elseif ($statusFilter === 'rejected') {
            $builder->where('form_kasbon.status_direktur', 'Ditolak');
        } elseif ($statusFilter === 'disbursed') {
            $builder->where('form_kasbon.status_keseluruhan', 'Dicairkan');
        } elseif ($statusFilter === 'paid') {
            $builder->where('form_kasbon.status_keseluruhan', 'Lunas');
        } else {
            // Default: tampilkan yang menunggu, disetujui, ditolak
            $builder->where('form_kasbon.status_direktur !=', '');
            $builder->where('form_kasbon.status_keseluruhan !=', 'Draft');
        }
        
        // Apply search filter
        if ($searchQuery) {
            $builder->groupStart()
                    ->like('form_kasbon.nomor_kasbon', $searchQuery)
                    ->orLike('karyawan.nama_lengkap', $searchQuery)
                    ->orLike('karyawan.nik', $searchQuery)
                    ->orLike('form_kasbon.alasan', $searchQuery)
                    ->groupEnd();
        }
        
        // Apply date filter
        if ($startDate && $endDate) {
            $builder->where('form_kasbon.tanggal_pengajuan >=', $startDate);
            $builder->where('form_kasbon.tanggal_pengajuan <=', $endDate . ' 23:59:59');
        }
        
        // Clone builder for total count
        $totalBuilder = clone $builder;
        $totalData = $totalBuilder->countAllResults();
        
        // Apply pagination and get results
        $builder->limit($perPage, $offset);
        $builder->orderBy('form_kasbon.tanggal_pengajuan', 'DESC');
        
        $kasbonData = $builder->get()->getResultArray();
        
        // Get statistics
        $stats = $this->kasbonModel->getStatistics($startDate, $endDate);
        
        // Calculate pagination
        $totalPages = ceil($totalData / $perPage);
        
        // Build base URL for pagination links
        $queryParams = [
            'status' => $statusFilter,
            'search' => $searchQuery,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
        
        $baseUrl = base_url('direktur/approval/kasbon') . '?' . http_build_query(array_filter($queryParams));
        
        // Get user data for navbar
        $userId = $session->get('user_id');
        $userData = $this->userModel->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
            ->select('users.*, karyawan.nama_lengkap as karyawan_nama, karyawan.nama_panggilan')
            ->where('users.id', $userId)
            ->first();
        
        // Prepare data for view
        $data = [
            'title' => 'Approval Kasbon',
            'subtitle' => 'Persetujuan Pengajuan Kasbon Karyawan oleh Direktur',
            'active' => 'approval',
            'kasbonData' => $kasbonData,
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
            'pendingCount' => $this->kasbonModel->getPendingCount()
        ];
        
        // Return view with templates
        return view('direktur/templates/header', $data)
             . view('direktur/templates/sidebar', $data)
             . view('direktur/templates/navbar', $data)
             . view('direktur/approval/kasbon', $data)
             . view('direktur/templates/footer', $data);
    }

    /**
     * Display kasbon detail
     */
    public function detail($id)
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        
        // Get kasbon detail with complete info
        $kasbon = $this->kasbonModel->getDetailById($id);
        
        if (!$kasbon) {
            return redirect()->to(base_url('direktur/approval/kasbon'))
                ->with('error', 'Data kasbon tidak ditemukan');
        }
        
        // Get user data for navbar
        $userId = $session->get('user_id');
        $userData = $this->userModel->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
            ->select('users.*, karyawan.nama_lengkap as karyawan_nama')
            ->where('users.id', $userId)
            ->first();
        
        $data = [
            'title' => 'Detail Kasbon',
            'subtitle' => 'Detail Pengajuan Kasbon Karyawan',
            'active' => 'approval',
            'kasbon' => $kasbon,
            'user' => $userData,
            'pendingCount' => $this->kasbonModel->getPendingCount()
        ];
        
        return view('direktur/templates/header', $data)
             . view('direktur/templates/sidebar', $data)
             . view('direktur/templates/navbar', $data)
             . view('direktur/approval/kasbon_detail', $data)
             . view('direktur/templates/footer', $data);
    }

    /**
     * Approve kasbon
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
        
        // Check if kasbon exists and needs approval
        $kasbon = $this->kasbonModel->find($id);
        if (!$kasbon) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data kasbon tidak ditemukan']);
        }
        
        if ($kasbon['status_direktur'] !== 'Menunggu') {
            return $this->response->setJSON(['success' => false, 'message' => 'Kasbon ini sudah diproses']);
        }
        
        if ($kasbon['status_hrd'] !== 'Disetujui HRD') {
            return $this->response->setJSON(['success' => false, 'message' => 'Kasbon belum disetujui HRD']);
        }
        
        // Approve kasbon
        $result = $this->kasbonModel->approveByDirektur($id, $this->userId, $this->karyawanId);
        
        if ($result) {
            // Log activity
            log_message('info', "Direktur approved kasbon ID: {$id} by user: {$this->userId}");
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pengajuan kasbon berhasil disetujui',
                'redirect' => base_url('direktur/approval/kasbon')
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyetujui pengajuan kasbon'
            ]);
        }
    }

    /**
     * Reject kasbon
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
        
        // Check if kasbon exists
        $kasbon = $this->kasbonModel->find($id);
        if (!$kasbon) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data kasbon tidak ditemukan']);
        }
        
        if ($kasbon['status_direktur'] !== 'Menunggu') {
            return $this->response->setJSON(['success' => false, 'message' => 'Kasbon ini sudah diproses']);
        }
        
        // Reject kasbon
        $result = $this->kasbonModel->rejectByDirektur($id, $this->userId, $this->karyawanId, $alasan);
        
        if ($result) {
            // Log activity
            log_message('info', "Direktur rejected kasbon ID: {$id} by user: {$this->userId}");
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pengajuan kasbon berhasil ditolak',
                'redirect' => base_url('direktur/approval/kasbon')
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menolak pengajuan kasbon'
            ]);
        }
    }
    
    /**
     * Batch approve multiple kasbon
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
            $kasbon = $this->kasbonModel->find($id);
            if ($kasbon && $kasbon['status_direktur'] === 'Menunggu' && $kasbon['status_hrd'] === 'Disetujui HRD') {
                $result = $this->kasbonModel->approveByDirektur($id, $this->userId, $this->karyawanId);
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
            'message' => "{$successCount} pengajuan berhasil disetujui, {$failCount} gagal",
            'success_count' => $successCount,
            'fail_count' => $failCount,
            'redirect' => base_url('direktur/approval/kasbon')
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
        $exportData = $this->kasbonModel->getForExport($startDate, $endDate, $statusFilter);
        
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
            ->setCreator("CDW Engineering HR System")
            ->setLastModifiedBy("CDW Engineering HR System")
            ->setTitle("Laporan Pengajuan Kasbon")
            ->setSubject("Export Data Kasbon")
            ->setDescription("Laporan pengajuan kasbon periode $startDate s/d $endDate");
        
        // Set headers
        $headers = [
            'No', 'No. Kasbon', 'Tanggal Pengajuan', 'NIK', 'Nama Karyawan', 'Jabatan', 'Departemen',
            'Jumlah Kasbon', 'Alasan', 'Tanggal Dibutuhkan', 'Rencana Pelunasan',
            'Status HRD', 'Status Direktur', 'Status Keseluruhan', 'Sisa Pinjaman'
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
        
        $sheet->getStyle('A1:O1')->applyFromArray($headerStyle);
        
        // Write data
        $row = 2;
        $no = 1;
        
        foreach ($exportData as $data) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $data['nomor_kasbon']);
            $sheet->setCellValue('C' . $row, date('d/m/Y', strtotime($data['tanggal_pengajuan'])));
            $sheet->setCellValue('D' . $row, $data['nik']);
            $sheet->setCellValue('E' . $row, $data['nama_lengkap']);
            $sheet->setCellValue('F' . $row, $data['jabatan'] ?? '-');
            $sheet->setCellValue('G' . $row, $data['departemen'] ?? '-');
            $sheet->setCellValue('H' . $row, $this->kasbonModel->formatCurrency($data['jumlah_kasbon']));
            $sheet->setCellValue('I' . $row, substr($data['alasan'], 0, 100));
            $sheet->setCellValue('J' . $row, !empty($data['tanggal_dibutuhkan']) ? date('d/m/Y', strtotime($data['tanggal_dibutuhkan'])) : '-');
            $sheet->setCellValue('K' . $row, substr($data['rencana_pelunasan'] ?? '-', 0, 100));
            $sheet->setCellValue('L' . $row, $data['status_hrd'] ?? '-');
            $sheet->setCellValue('M' . $row, $data['status_direktur'] ?? '-');
            $sheet->setCellValue('N' . $row, $data['status_keseluruhan'] ?? '-');
            $sheet->setCellValue('O' . $row, $this->kasbonModel->formatCurrency($data['sisa_pinjaman'] ?? 0));
            $row++;
        }
        
        // Auto size columns
        foreach (range('A', 'O') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Set filename
        $filename = 'Laporan_Pengajuan_Kasbon_' . date('Ymd_His') . '.xlsx';
        
        // Create writer and output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit();
    }

    /**
     * Print kasbon detail
     */
    public function print($id)
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        
        $kasbon = $this->kasbonModel->getDetailById($id);
        
        if (!$kasbon) {
            return redirect()->to(base_url('direktur/approval/kasbon'))
                ->with('error', 'Data kasbon tidak ditemukan');
        }
        
        $data = [
            'title' => 'Cetak Pengajuan Kasbon',
            'kasbon' => $kasbon,
            'user' => $session->get()
        ];
        
        return view('direktur/approval/kasbon_print', $data);
    }
}