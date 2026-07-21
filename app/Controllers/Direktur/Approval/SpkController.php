<?php
// app/Controllers/Direktur/Approval/SpkController.php

namespace App\Controllers\Direktur\Approval;

use App\Controllers\BaseController;
use App\Models\Direktur\SpkModel;
use App\Models\ClientModel;
use App\Models\KaryawanModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

class SpkController extends BaseController
{
    use ResponseTrait;
    
    protected $spkModel;
    protected $clientModel;
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
        
        $this->spkModel = new SpkModel();
        $this->clientModel = new ClientModel();
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
     * Display list of SPK for direktur approval
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
        $builder = $db->table('spk_instalasi');
        
        $builder->select('
            spk_instalasi.*,
            client.nama_perusahaan,
            client.kode_client,
            karyawan.nama_lengkap as penanggung_jawab_nama,
            karyawan.nama_panggilan as penanggung_jawab_panggilan,
            project.nama_project
        ');
        
        $builder->join('client', 'client.id = spk_instalasi.client_id');
        $builder->join('karyawan', 'karyawan.id = spk_instalasi.penanggung_jawab_id', 'left');
        $builder->join('project', 'project.id = spk_instalasi.project_id', 'left');
        $builder->where('spk_instalasi.deleted_at', null);
        
        // Filter status untuk approval direktur
        if ($statusFilter === 'pending') {
            $builder->where('spk_instalasi.status', 'draft');
        } elseif ($statusFilter === 'approved') {
            $builder->where('spk_instalasi.status', 'disetujui');
        } elseif ($statusFilter === 'rejected') {
            $builder->where('spk_instalasi.status', 'ditolak');
        } elseif ($statusFilter === 'progress') {
            $builder->where('spk_instalasi.status', 'on_progress');
        } elseif ($statusFilter === 'completed') {
            $builder->where('spk_instalasi.status', 'selesai');
        } else {
            // Default: tampilkan semua status
            $builder->whereIn('spk_instalasi.status', ['draft', 'disetujui', 'ditolak', 'on_progress', 'selesai']);
        }
        
        // Apply search filter
        if ($searchQuery) {
            $builder->groupStart()
                    ->like('spk_instalasi.nomor_spk', $searchQuery)
                    ->orLike('client.nama_perusahaan', $searchQuery)
                    ->orLike('spk_instalasi.judul_pekerjaan', $searchQuery)
                    ->groupEnd();
        }
        
        // Apply date filter
        if ($startDate && $endDate) {
            $builder->where('spk_instalasi.created_at >=', $startDate);
            $builder->where('spk_instalasi.created_at <=', $endDate . ' 23:59:59');
        }
        
        // Clone builder for total count
        $totalBuilder = clone $builder;
        $totalData = $totalBuilder->countAllResults();
        
        // Apply pagination and get results
        $builder->limit($perPage, $offset);
        $builder->orderBy('spk_instalasi.created_at', 'DESC');
        
        $spkData = $builder->get()->getResultArray();
        
        // Get statistics
        $stats = $this->spkModel->getStatistics($startDate, $endDate);
        
        // Calculate pagination
        $totalPages = ceil($totalData / $perPage);
        
        // Build base URL for pagination links
        $queryParams = [
            'status' => $statusFilter,
            'search' => $searchQuery,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
        
        $baseUrl = base_url('direktur/approval/spk') . '?' . http_build_query(array_filter($queryParams));
        
        // Get user data for navbar
        $userId = $session->get('user_id');
        $userData = $this->userModel->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
            ->select('users.*, karyawan.nama_lengkap as karyawan_nama, karyawan.nama_panggilan')
            ->where('users.id', $userId)
            ->first();
        
        // Prepare data for view
        $data = [
            'title' => 'Approval SPK',
            'subtitle' => 'Persetujuan Surat Perintah Kerja oleh Direktur',
            'active' => 'approval',
            'spkData' => $spkData,
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
            'pendingCount' => $this->spkModel->getPendingCount()
        ];
        
        // Return view with templates
        return view('direktur/templates/header', $data)
             . view('direktur/templates/sidebar', $data)
             . view('direktur/templates/navbar', $data)
             . view('direktur/approval/spk', $data)
             . view('direktur/templates/footer', $data);
    }

    /**
     * Display SPK detail
     */
    public function detail($id)
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        
        // Get SPK detail with complete info
        $spk = $this->spkModel->getDetailById($id);
        
        if (!$spk) {
            return redirect()->to(base_url('direktur/approval/spk'))
                ->with('error', 'Data SPK tidak ditemukan');
        }
        
        // Get user data for navbar
        $userId = $session->get('user_id');
        $userData = $this->userModel->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
            ->select('users.*, karyawan.nama_lengkap as karyawan_nama')
            ->where('users.id', $userId)
            ->first();
        
        $data = [
            'title' => 'Detail SPK',
            'subtitle' => 'Detail Surat Perintah Kerja',
            'active' => 'approval',
            'spk' => $spk,
            'user' => $userData,
            'pendingCount' => $this->spkModel->getPendingCount()
        ];
        
        return view('direktur/templates/header', $data)
             . view('direktur/templates/sidebar', $data)
             . view('direktur/templates/navbar', $data)
             . view('direktur/approval/spk_detail', $data)
             . view('direktur/templates/footer', $data);
    }

    /**
     * Approve SPK
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
        
        // Check if SPK exists and needs approval
        $spk = $this->spkModel->find($id);
        if (!$spk) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data SPK tidak ditemukan']);
        }
        
        if ($spk['status'] !== 'draft') {
            return $this->response->setJSON(['success' => false, 'message' => 'SPK ini sudah diproses']);
        }
        
        // Approve SPK
        $result = $this->spkModel->approveByDirektur($id, $this->userId);
        
        if ($result) {
            // Log activity
            log_message('info', "Direktur approved SPK ID: {$id} by user: {$this->userId}");
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'SPK berhasil disetujui',
                'redirect' => base_url('direktur/approval/spk')
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyetujui SPK'
            ]);
        }
    }

    /**
     * Reject SPK
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
        
        // Check if SPK exists
        $spk = $this->spkModel->find($id);
        if (!$spk) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data SPK tidak ditemukan']);
        }
        
        if ($spk['status'] !== 'draft') {
            return $this->response->setJSON(['success' => false, 'message' => 'SPK ini sudah diproses']);
        }
        
        // Reject SPK
        $result = $this->spkModel->rejectByDirektur($id, $this->userId, $alasan);
        
        if ($result) {
            // Log activity
            log_message('info', "Direktur rejected SPK ID: {$id} by user: {$this->userId}");
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'SPK berhasil ditolak',
                'redirect' => base_url('direktur/approval/spk')
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menolak SPK'
            ]);
        }
    }
    
    /**
     * Batch approve multiple SPK
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
            $spk = $this->spkModel->find($id);
            if ($spk && $spk['status'] === 'draft') {
                $result = $this->spkModel->approveByDirektur($id, $this->userId);
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
            'message' => "{$successCount} SPK berhasil disetujui, {$failCount} gagal",
            'success_count' => $successCount,
            'fail_count' => $failCount,
            'redirect' => base_url('direktur/approval/spk')
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
        $exportData = $this->spkModel->getForExport($startDate, $endDate, $statusFilter);
        
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
            ->setTitle("Laporan SPK")
            ->setSubject("Export Data SPK")
            ->setDescription("Laporan Surat Perintah Kerja periode $startDate s/d $endDate");
        
        // Set headers
        $headers = [
            'No', 'No. SPK', 'Tanggal SPK', 'Client', 'Judul Pekerjaan', 'Lokasi',
            'Tanggal Mulai', 'Tanggal Selesai', 'Nilai Kontrak', 'Penanggung Jawab', 'Status'
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
            $sheet->setCellValue('B' . $row, $data['nomor_spk']);
            $sheet->setCellValue('C' . $row, date('d/m/Y', strtotime($data['created_at'])));
            $sheet->setCellValue('D' . $row, $data['nama_perusahaan']);
            $sheet->setCellValue('E' . $row, substr($data['judul_pekerjaan'], 0, 100));
            $sheet->setCellValue('F' . $row, substr($data['lokasi_pekerjaan'] ?? '-', 0, 100));
            $sheet->setCellValue('G' . $row, date('d/m/Y', strtotime($data['tanggal_mulai'])));
            $sheet->setCellValue('H' . $row, date('d/m/Y', strtotime($data['tanggal_selesai'])));
            $sheet->setCellValue('I' . $row, $this->spkModel->formatCurrency($data['nilai_kontrak'] ?? 0));
            $sheet->setCellValue('J' . $row, $data['penanggung_jawab_nama'] ?? '-');
            $sheet->setCellValue('K' . $row, $data['status']);
            $row++;
        }
        
        // Auto size columns
        foreach (range('A', 'K') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Set filename
        $filename = 'Laporan_SPK_' . date('Ymd_His') . '.xlsx';
        
        // Create writer and output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit();
    }

    /**
     * Print SPK detail
     */
    public function print($id)
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        
        $spk = $this->spkModel->getDetailById($id);
        
        if (!$spk) {
            return redirect()->to(base_url('direktur/approval/spk'))
                ->with('error', 'Data SPK tidak ditemukan');
        }
        
        $data = [
            'title' => 'Cetak SPK',
            'spk' => $spk,
            'user' => $session->get()
        ];
        
        return view('direktur/approval/spk_print', $data);
    }
}