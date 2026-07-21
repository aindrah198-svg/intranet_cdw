<?php
// app/Controllers/Direktur/Approval/IzinController.php

namespace App\Controllers\Direktur\Approval;

use App\Controllers\BaseController;
use App\Models\Direktur\IzinModel;
use App\Models\KaryawanModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

class IzinController extends BaseController
{
    use ResponseTrait;
    
    protected $izinModel;
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
        
        $this->izinModel = new IzinModel();
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
     * Display list of form izin for direktur approval
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
        $builder = $db->table('form_izin');
        
        $builder->select('
            form_izin.*,
            karyawan.nik,
            karyawan.nama_lengkap,
            karyawan.nama_panggilan,
            karyawan.jabatan,
            karyawan.departemen,
            karyawan.foto
        ');
        
        $builder->join('karyawan', 'karyawan.id = form_izin.karyawan_id');
        $builder->where('form_izin.deleted_at', null);
        
        // Filter status untuk approval direktur
        if ($statusFilter === 'pending') {
            $builder->where('form_izin.status_hrd', 'Disetujui');
            $builder->where('form_izin.status_keseluruhan', 'Menunggu');
        } elseif ($statusFilter === 'approved') {
            $builder->where('form_izin.status_keseluruhan', 'Disetujui');
        } elseif ($statusFilter === 'rejected') {
            $builder->where('form_izin.status_keseluruhan', 'Ditolak');
        } else {
            // Default: tampilkan yang menunggu, disetujui, ditolak
            $builder->whereIn('form_izin.status_keseluruhan', ['Menunggu', 'Disetujui', 'Ditolak']);
            $builder->where('form_izin.status_hrd', 'Disetujui');
        }
        
        // Apply jenis filter
        if ($jenisFilter) {
            $builder->where('form_izin.jenis_izin', $jenisFilter);
        }
        
        // Apply search filter
        if ($searchQuery) {
            $builder->groupStart()
                    ->like('form_izin.nomor_izin', $searchQuery)
                    ->orLike('karyawan.nama_lengkap', $searchQuery)
                    ->orLike('karyawan.nik', $searchQuery)
                    ->orLike('form_izin.alasan', $searchQuery)
                    ->groupEnd();
        }
        
        // Apply date filter
        if ($startDate && $endDate) {
            $builder->where('form_izin.tanggal_pengajuan >=', $startDate);
            $builder->where('form_izin.tanggal_pengajuan <=', $endDate . ' 23:59:59');
        }
        
        // Clone builder for total count
        $totalBuilder = clone $builder;
        $totalData = $totalBuilder->countAllResults();
        
        // Apply pagination and get results
        $builder->limit($perPage, $offset);
        $builder->orderBy('form_izin.tanggal_pengajuan', 'DESC');
        
        $izinData = $builder->get()->getResultArray();
        
        // Get statistics
        $stats = $this->izinModel->getStatistics($startDate, $endDate);
        
        // Get jenis izin list for filter
        $jenisIzinList = $this->izinModel->getJenisIzinList();
        
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
        
        $baseUrl = base_url('direktur/approval/izin') . '?' . http_build_query(array_filter($queryParams));
        
        // Get user data for navbar
        $userId = $session->get('user_id');
        $userData = $this->userModel->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
            ->select('users.*, karyawan.nama_lengkap as karyawan_nama, karyawan.nama_panggilan')
            ->where('users.id', $userId)
            ->first();
        
        // Prepare data for view
        $data = [
            'title' => 'Approval Izin',
            'subtitle' => 'Persetujuan Pengajuan Izin Karyawan oleh Direktur',
            'active' => 'approval',
            'izinData' => $izinData,
            'jenisIzinList' => $jenisIzinList,
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
            'pendingCount' => $this->izinModel->getPendingCount()
        ];
        
        // Return view with templates
        return view('direktur/templates/header', $data)
             . view('direktur/templates/sidebar', $data)
             . view('direktur/templates/navbar', $data)
             . view('direktur/approval/izin', $data)
             . view('direktur/templates/footer', $data);
    }

    /**
     * Display form izin detail
     */
    public function detail($id)
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        
        // Get form izin detail with complete info
        $izin = $this->izinModel->getDetailById($id);
        
        if (!$izin) {
            return redirect()->to(base_url('direktur/approval/izin'))
                ->with('error', 'Data pengajuan izin tidak ditemukan');
        }
        
        // Calculate jam izin if jam_keluar and jam_kembali exist
        $jamIzin = '';
        if (!empty($izin['jam_keluar']) && !empty($izin['jam_kembali'])) {
            $jamIzin = $this->izinModel->calculateJamIzin($izin['jam_keluar'], $izin['jam_kembali']);
        }
        
        // Get user data for navbar
        $userId = $session->get('user_id');
        $userData = $this->userModel->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
            ->select('users.*, karyawan.nama_lengkap as karyawan_nama')
            ->where('users.id', $userId)
            ->first();
        
        $data = [
            'title' => 'Detail Izin',
            'subtitle' => 'Detail Pengajuan Izin Karyawan',
            'active' => 'approval',
            'izin' => $izin,
            'jamIzin' => $jamIzin,
            'user' => $userData,
            'pendingCount' => $this->izinModel->getPendingCount()
        ];
        
        return view('direktur/templates/header', $data)
             . view('direktur/templates/sidebar', $data)
             . view('direktur/templates/navbar', $data)
             . view('direktur/approval/izin_detail', $data)
             . view('direktur/templates/footer', $data);
    }

    /**
     * Approve form izin
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
        
        // Check if form izin exists and needs approval
        $izin = $this->izinModel->find($id);
        if (!$izin) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data pengajuan izin tidak ditemukan']);
        }
        
        if ($izin['status_keseluruhan'] !== 'Menunggu') {
            return $this->response->setJSON(['success' => false, 'message' => 'Pengajuan izin ini sudah diproses']);
        }
        
        if ($izin['status_hrd'] !== 'Disetujui') {
            return $this->response->setJSON(['success' => false, 'message' => 'Pengajuan izin belum disetujui HRD']);
        }
        
        // Approve form izin
        $result = $this->izinModel->approveByDirektur($id, $this->userId, $this->karyawanId);
        
        if ($result) {
            // Log activity
            log_message('info', "Direktur approved izin ID: {$id} by user: {$this->userId}");
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pengajuan izin berhasil disetujui',
                'redirect' => base_url('direktur/approval/izin')
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyetujui pengajuan izin'
            ]);
        }
    }

    /**
     * Reject form izin
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
        
        // Check if form izin exists
        $izin = $this->izinModel->find($id);
        if (!$izin) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data pengajuan izin tidak ditemukan']);
        }
        
        if ($izin['status_keseluruhan'] !== 'Menunggu') {
            return $this->response->setJSON(['success' => false, 'message' => 'Pengajuan izin ini sudah diproses']);
        }
        
        // Reject form izin
        $result = $this->izinModel->rejectByDirektur($id, $this->userId, $this->karyawanId, $alasan);
        
        if ($result) {
            // Log activity
            log_message('info', "Direktur rejected izin ID: {$id} by user: {$this->userId}");
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pengajuan izin berhasil ditolak',
                'redirect' => base_url('direktur/approval/izin')
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menolak pengajuan izin'
            ]);
        }
    }
    
    /**
     * Batch approve multiple form izin
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
            $izin = $this->izinModel->find($id);
            if ($izin && $izin['status_keseluruhan'] === 'Menunggu' && $izin['status_hrd'] === 'Disetujui') {
                $result = $this->izinModel->approveByDirektur($id, $this->userId, $this->karyawanId);
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
            'redirect' => base_url('direktur/approval/izin')
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
        $exportData = $this->izinModel->getForExport($startDate, $endDate, $statusFilter);
        
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
            ->setTitle("Laporan Pengajuan Izin")
            ->setSubject("Export Data Izin")
            ->setDescription("Laporan pengajuan izin periode $startDate s/d $endDate");
        
        // Set headers
        $headers = [
            'No', 'No. Izin', 'Tanggal Pengajuan', 'NIK', 'Nama Karyawan', 'Jabatan', 'Departemen',
            'Jenis Izin', 'Alasan', 'Tanggal Mulai', 'Tanggal Selesai', 'Lama Hari',
            'Jam Keluar', 'Jam Kembali', 'Status Atasan', 'Status HRD', 'Status'
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
        
        $sheet->getStyle('A1:Q1')->applyFromArray($headerStyle);
        
        // Write data
        $row = 2;
        $no = 1;
        
        foreach ($exportData as $data) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $data['nomor_izin']);
            $sheet->setCellValue('C' . $row, date('d/m/Y', strtotime($data['tanggal_pengajuan'])));
            $sheet->setCellValue('D' . $row, $data['nik']);
            $sheet->setCellValue('E' . $row, $data['nama_lengkap']);
            $sheet->setCellValue('F' . $row, $data['jabatan'] ?? '-');
            $sheet->setCellValue('G' . $row, $data['departemen'] ?? '-');
            $sheet->setCellValue('H' . $row, $data['jenis_izin']);
            $sheet->setCellValue('I' . $row, substr($data['alasan'], 0, 100));
            $sheet->setCellValue('J' . $row, date('d/m/Y', strtotime($data['tanggal_mulai'])));
            $sheet->setCellValue('K' . $row, date('d/m/Y', strtotime($data['tanggal_selesai'])));
            $sheet->setCellValue('L' . $row, $data['lama_hari']);
            $sheet->setCellValue('M' . $row, $data['jam_keluar'] ?? '-');
            $sheet->setCellValue('N' . $row, $data['jam_kembali'] ?? '-');
            $sheet->setCellValue('O' . $row, $data['status_atasan'] ?? '-');
            $sheet->setCellValue('P' . $row, $data['status_hrd'] ?? '-');
            $sheet->setCellValue('Q' . $row, $data['status_keseluruhan'] ?? '-');
            $row++;
        }
        
        // Auto size columns
        foreach (range('A', 'Q') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Set filename
        $filename = 'Laporan_Pengajuan_Izin_' . date('Ymd_His') . '.xlsx';
        
        // Create writer and output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit();
    }

    /**
     * Print form izin detail
     */
    public function print($id)
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        
        $izin = $this->izinModel->getDetailById($id);
        
        if (!$izin) {
            return redirect()->to(base_url('direktur/approval/izin'))
                ->with('error', 'Data pengajuan izin tidak ditemukan');
        }
        
        // Calculate jam izin if jam_keluar and jam_kembali exist
        $jamIzin = '';
        if (!empty($izin['jam_keluar']) && !empty($izin['jam_kembali'])) {
            $jamIzin = $this->izinModel->calculateJamIzin($izin['jam_keluar'], $izin['jam_kembali']);
        }
        
        $data = [
            'title' => 'Cetak Pengajuan Izin',
            'izin' => $izin,
            'jamIzin' => $jamIzin,
            'user' => $session->get()
        ];
        
        return view('direktur/approval/izin_print', $data);
    }
}