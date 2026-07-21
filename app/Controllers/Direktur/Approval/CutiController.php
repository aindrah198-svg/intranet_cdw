<?php
// app/Controllers/Direktur/Approval/CutiController.php

namespace App\Controllers\Direktur\Approval;

use App\Controllers\BaseController;
use App\Models\Direktur\CutiModel;
use App\Models\KaryawanModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

class CutiController extends BaseController
{
    use ResponseTrait;
    
    protected $cutiModel;
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
        
        $this->cutiModel = new CutiModel();
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
     * Display list of cuti for direktur approval
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
        $jenisFilter = $this->request->getGet('jenis');
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
        $builder = $db->table('cuti');
        
        $builder->select('
            cuti.*,
            karyawan.nik,
            karyawan.nama_lengkap,
            karyawan.nama_panggilan,
            karyawan.jabatan,
            karyawan.departemen,
            karyawan.foto,
            atasan.nama_lengkap as atasan_nama,
            hrd.nama_lengkap as hrd_nama
        ');
        
        $builder->join('karyawan', 'karyawan.id = cuti.karyawan_id');
        $builder->join('karyawan as atasan', 'atasan.id = cuti.atasan_id', 'left');
        $builder->join('karyawan as hrd', 'hrd.id = cuti.hrd_id', 'left');
        $builder->where('cuti.deleted_at', null);
        
        // Filter untuk approval direktur: status_direktur = 'Menunggu' dan status_hrd = 'Disetujui'
        if ($statusFilter === 'pending') {
            $builder->where('cuti.status_direktur', 'Menunggu');
            $builder->where('cuti.status_hrd', 'Disetujui');
        } elseif ($statusFilter === 'approved') {
            $builder->where('cuti.status_direktur', 'Disetujui');
        } elseif ($statusFilter === 'rejected') {
            $builder->where('cuti.status_direktur', 'Ditolak');
        } else {
            // Default: tampilkan semua yang status_direktur Menunggu, Disetujui, Ditolak (tidak termasuk Tidak Diperlukan)
            $builder->whereIn('cuti.status_direktur', ['Menunggu', 'Disetujui', 'Ditolak']);
            $builder->where('cuti.status_hrd', 'Disetujui');
        }
        
        // Apply filters
        if ($jenisFilter) {
            $builder->where('cuti.jenis_cuti', $jenisFilter);
        }
        
        if ($searchQuery) {
            $builder->groupStart()
                    ->like('karyawan.nama_lengkap', $searchQuery)
                    ->orLike('karyawan.nik', $searchQuery)
                    ->orLike('karyawan.nama_panggilan', $searchQuery)
                    ->orLike('cuti.nomor_cuti', $searchQuery)
                    ->groupEnd();
        }
        
        if ($startDate && $endDate) {
            $builder->where('cuti.tanggal_pengajuan >=', $startDate);
            $builder->where('cuti.tanggal_pengajuan <=', $endDate . ' 23:59:59');
        }
        
        // Clone builder for total count
        $totalBuilder = clone $builder;
        $totalData = $totalBuilder->countAllResults();
        
        // Apply pagination and get results
        $builder->limit($perPage, $offset);
        $builder->orderBy('cuti.tanggal_pengajuan', 'DESC');
        
        $cutiData = $builder->get()->getResultArray();
        
        // Get statistics
        $stats = $this->getStatisticsData($startDate, $endDate);
        
        // Get karyawan list for filter (optional)
        $karyawanList = $this->karyawanModel->select('id, nik, nama_lengkap, nama_panggilan')
                                      ->where('deleted_at', null)
                                      ->orderBy('nama_lengkap', 'ASC')
                                      ->findAll();
        
        // Get jenis cuti list for filter
        $jenisCutiList = ['Tahunan', 'Sakit', 'Hamil', 'Penting', 'Izin', 'Lainnya'];
        
        // Calculate pagination
        $totalPages = ceil($totalData / $perPage);
        
        // Build base URL for pagination links
        $queryParams = [
            'status' => $statusFilter,
            'jenis' => $jenisFilter,
            'search' => $searchQuery,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
        
        $baseUrl = base_url('direktur/approval/cuti') . '?' . http_build_query(array_filter($queryParams));
        
        // Get user data for navbar
        $userId = $session->get('user_id');
        $userData = $this->userModel->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
            ->select('users.*, karyawan.nama_lengkap as karyawan_nama, karyawan.nama_panggilan')
            ->where('users.id', $userId)
            ->first();
        
        // Prepare data for view
        $data = [
            'title' => 'Approval Cuti Karyawan',
            'subtitle' => 'Persetujuan Cuti Karyawan oleh Direktur',
            'active' => 'approval',
            'cutiData' => $cutiData,
            'karyawanList' => $karyawanList,
            'jenisCutiList' => $jenisCutiList,
            'statusFilter' => $statusFilter,
            'jenisFilter' => $jenisFilter,
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
            'pendingCount' => $this->cutiModel->getPendingCount()
        ];
        
        // Return view with templates
        return view('direktur/templates/header', $data)
             . view('direktur/templates/sidebar', $data)
             . view('direktur/templates/navbar', $data)
             . view('direktur/approval/cuti', $data)
             . view('direktur/templates/footer', $data);
    }
    
    /**
     * Display cuti detail
     */
    public function detail($id)
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        
        // Get cuti detail with complete info
        $cuti = $this->cutiModel->getDetailById($id);
        
        if (!$cuti) {
            return redirect()->to(base_url('direktur/approval/cuti'))
                ->with('error', 'Data cuti tidak ditemukan');
        }
        
        // Get user data for navbar
        $userId = $session->get('user_id');
        $userData = $this->userModel->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
            ->select('users.*, karyawan.nama_lengkap as karyawan_nama')
            ->where('users.id', $userId)
            ->first();
        
        $data = [
            'title' => 'Detail Pengajuan Cuti',
            'subtitle' => 'Detail Pengajuan Cuti Karyawan',
            'active' => 'approval',
            'cuti' => $cuti,
            'user' => $userData,
            'pendingCount' => $this->cutiModel->getPendingCount()
        ];
        
        return view('direktur/templates/header', $data)
             . view('direktur/templates/sidebar', $data)
             . view('direktur/templates/navbar', $data)
             . view('direktur/approval/cuti_detail', $data)
             . view('direktur/templates/footer', $data);
    }
    
    /**
     * Approve cuti
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
        
        // Check if cuti exists and needs approval
        $cuti = $this->cutiModel->find($id);
        if (!$cuti) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data cuti tidak ditemukan']);
        }
        
        if ($cuti['status_direktur'] !== 'Menunggu') {
            return $this->response->setJSON(['success' => false, 'message' => 'Pengajuan cuti ini sudah diproses']);
        }
        
        if ($cuti['status_hrd'] !== 'Disetujui') {
            return $this->response->setJSON(['success' => false, 'message' => 'Pengajuan cuti belum disetujui HRD']);
        }
        
        // Approve cuti
        $result = $this->cutiModel->approveByDirektur($id, $this->userId, $this->karyawanId);
        
        if ($result) {
            // Log activity
            log_message('info', "Direktur approved cuti ID: {$id} by user: {$this->userId}");
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pengajuan cuti berhasil disetujui',
                'redirect' => base_url('direktur/approval/cuti')
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyetujui pengajuan cuti'
            ]);
        }
    }
    
    /**
     * Reject cuti
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
        
        // Check if cuti exists
        $cuti = $this->cutiModel->find($id);
        if (!$cuti) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data cuti tidak ditemukan']);
        }
        
        if ($cuti['status_direktur'] !== 'Menunggu') {
            return $this->response->setJSON(['success' => false, 'message' => 'Pengajuan cuti ini sudah diproses']);
        }
        
        // Reject cuti
        $result = $this->cutiModel->rejectByDirektur($id, $this->userId, $this->karyawanId, $alasan);
        
        if ($result) {
            // Log activity
            log_message('info', "Direktur rejected cuti ID: {$id} by user: {$this->userId}");
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pengajuan cuti berhasil ditolak',
                'redirect' => base_url('direktur/approval/cuti')
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menolak pengajuan cuti'
            ]);
        }
    }
    
    /**
     * Batch approve multiple cuti
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
            $cuti = $this->cutiModel->find($id);
            if ($cuti && $cuti['status_direktur'] === 'Menunggu' && $cuti['status_hrd'] === 'Disetujui') {
                $result = $this->cutiModel->approveByDirektur($id, $this->userId, $this->karyawanId);
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
            'redirect' => base_url('direktur/approval/cuti')
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
        $exportData = $this->cutiModel->getForExport($startDate, $endDate, $statusFilter);
        
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
            ->setTitle("Laporan Pengajuan Cuti")
            ->setSubject("Export Data Cuti")
            ->setDescription("Laporan pengajuan cuti periode $startDate s/d $endDate");
        
        // Set headers
        $headers = [
            'No', 'No. Cuti', 'Tanggal Pengajuan', 'NIK', 'Nama Karyawan', 'Jabatan', 'Departemen',
            'Jenis Cuti', 'Alasan', 'Tanggal Mulai', 'Tanggal Selesai', 'Lama Hari',
            'Status Atasan', 'Status HRD', 'Status Direktur', 'Status Pengajuan'
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
        
        $sheet->getStyle('A1:P1')->applyFromArray($headerStyle);
        
        // Write data
        $row = 2;
        $no = 1;
        
        foreach ($exportData as $data) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $data['nomor_cuti']);
            $sheet->setCellValue('C' . $row, date('d/m/Y', strtotime($data['tanggal_pengajuan'])));
            $sheet->setCellValue('D' . $row, $data['nik']);
            $sheet->setCellValue('E' . $row, $data['nama_panggilan'] ?? $data['nama_lengkap']);
            $sheet->setCellValue('F' . $row, $data['jabatan'] ?? '-');
            $sheet->setCellValue('G' . $row, $data['departemen'] ?? '-');
            $sheet->setCellValue('H' . $row, $data['jenis_cuti']);
            $sheet->setCellValue('I' . $row, substr($data['alasan'], 0, 100));
            $sheet->setCellValue('J' . $row, date('d/m/Y', strtotime($data['tanggal_mulai'])));
            $sheet->setCellValue('K' . $row, date('d/m/Y', strtotime($data['tanggal_selesai'])));
            $sheet->setCellValue('L' . $row, $data['lama_hari']);
            $sheet->setCellValue('M' . $row, $data['status_atasan'] ?? '-');
            $sheet->setCellValue('N' . $row, $data['status_hrd'] ?? '-');
            $sheet->setCellValue('O' . $row, $data['status_direktur'] ?? '-');
            $sheet->setCellValue('P' . $row, $data['status_pengajuan']);
            $row++;
        }
        
        // Auto size columns
        foreach (range('A', 'P') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Set filename
        $filename = 'Laporan_Pengajuan_Cuti_' . date('Ymd_His') . '.xlsx';
        
        // Create writer and output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit();
    }
    
    /**
     * Print cuti detail
     */
    public function print($id)
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        
        $cuti = $this->cutiModel->getDetailById($id);
        
        if (!$cuti) {
            return redirect()->to(base_url('direktur/approval/cuti'))
                ->with('error', 'Data cuti tidak ditemukan');
        }
        
        $data = [
            'title' => 'Cetak Pengajuan Cuti',
            'cuti' => $cuti,
            'user' => $session->get()
        ];
        
        return view('direktur/approval/cuti_print', $data);
    }
    
    /**
     * Export PDF (placeholder - will implement with Dompdf)
     */
    public function exportPdf($id)
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        
        // This will be implemented later with Dompdf
        return redirect()->back()->with('error', 'Fitur PDF belum tersedia. Silakan gunakan fitur Print.');
    }
    
    /**
     * Get statistics data
     */
    private function getStatisticsData($startDate, $endDate)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('cuti');
        
        $builder->select("
            COUNT(*) as total,
            SUM(CASE WHEN status_direktur = 'Menunggu' AND status_hrd = 'Disetujui' THEN 1 ELSE 0 END) as menunggu,
            SUM(CASE WHEN status_direktur = 'Disetujui' THEN 1 ELSE 0 END) as disetujui,
            SUM(CASE WHEN status_direktur = 'Ditolak' THEN 1 ELSE 0 END) as ditolak,
            SUM(lama_hari) as total_hari_cuti
        ");
        
        $builder->where('deleted_at', null);
        
        if ($startDate && $endDate) {
            $builder->where('tanggal_pengajuan >=', $startDate);
            $builder->where('tanggal_pengajuan <=', $endDate . ' 23:59:59');
        }
        
        $result = $builder->get()->getRowArray();
        
        return [
            'total' => $result['total'] ?? 0,
            'menunggu' => $result['menunggu'] ?? 0,
            'disetujui' => $result['disetujui'] ?? 0,
            'ditolak' => $result['ditolak'] ?? 0,
            'total_hari_cuti' => $result['total_hari_cuti'] ?? 0
        ];
    }
}