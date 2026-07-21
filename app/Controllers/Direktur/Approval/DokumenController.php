<?php
// app/Controllers/Direktur/Approval/DokumenController.php

namespace App\Controllers\Direktur\Approval;

use App\Controllers\BaseController;
use App\Models\Direktur\DokumenModel;
use App\Models\KaryawanModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

class DokumenController extends BaseController
{
    use ResponseTrait;
    
    protected $dokumenModel;
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
        
        $this->dokumenModel = new DokumenModel();
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
     * Display list of form dokumen for direktur approval
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
        $builder = $db->table('form_dokumen');
        
        $builder->select('
            form_dokumen.*,
            karyawan.nik,
            karyawan.nama_lengkap,
            karyawan.nama_panggilan,
            karyawan.jabatan,
            karyawan.departemen,
            karyawan.foto
        ');
        
        $builder->join('karyawan', 'karyawan.id = form_dokumen.karyawan_id');
        $builder->where('form_dokumen.deleted_at', null);
        
        // Filter status untuk approval direktur
        if ($statusFilter === 'pending') {
            $builder->where('form_dokumen.status_direktur', 'Menunggu');
            $builder->where('form_dokumen.status_hrd', 'Diproses');
        } elseif ($statusFilter === 'approved') {
            $builder->where('form_dokumen.status_direktur', 'Disetujui');
        } elseif ($statusFilter === 'rejected') {
            $builder->where('form_dokumen.status_direktur', 'Ditolak');
        } elseif ($statusFilter === 'processed') {
            $builder->where('form_dokumen.status_hrd', 'Diproses');
        } elseif ($statusFilter === 'completed') {
            $builder->where('form_dokumen.status_keseluruhan', 'Selesai');
        } else {
            // Default: tampilkan yang menunggu, disetujui, ditolak
            $builder->whereIn('form_dokumen.status_direktur', ['Menunggu', 'Disetujui', 'Ditolak']);
            $builder->where('form_dokumen.status_keseluruhan !=', 'Draft');
        }
        
        // Apply jenis filter
        if ($jenisFilter) {
            $builder->where('form_dokumen.jenis_dokumen', $jenisFilter);
        }
        
        // Apply search filter
        if ($searchQuery) {
            $builder->groupStart()
                    ->like('form_dokumen.nomor_form', $searchQuery)
                    ->orLike('karyawan.nama_lengkap', $searchQuery)
                    ->orLike('karyawan.nik', $searchQuery)
                    ->orLike('form_dokumen.keperluan', $searchQuery)
                    ->groupEnd();
        }
        
        // Apply date filter
        if ($startDate && $endDate) {
            $builder->where('form_dokumen.tanggal_pengajuan >=', $startDate);
            $builder->where('form_dokumen.tanggal_pengajuan <=', $endDate . ' 23:59:59');
        }
        
        // Clone builder for total count
        $totalBuilder = clone $builder;
        $totalData = $totalBuilder->countAllResults();
        
        // Apply pagination and get results
        $builder->limit($perPage, $offset);
        $builder->orderBy('form_dokumen.tanggal_pengajuan', 'DESC');
        
        $dokumenData = $builder->get()->getResultArray();
        
        // Get statistics
        $stats = $this->dokumenModel->getStatistics($startDate, $endDate);
        
        // Get jenis dokumen list for filter
        $jenisDokumenList = $this->dokumenModel->getJenisDokumenList();
        
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
        
        $baseUrl = base_url('direktur/approval/dokumen') . '?' . http_build_query(array_filter($queryParams));
        
        // Get user data for navbar
        $userId = $session->get('user_id');
        $userData = $this->userModel->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
            ->select('users.*, karyawan.nama_lengkap as karyawan_nama, karyawan.nama_panggilan')
            ->where('users.id', $userId)
            ->first();
        
        // Prepare data for view
        $data = [
            'title' => 'Approval Dokumen',
            'subtitle' => 'Persetujuan Pengajuan Dokumen oleh Direktur',
            'active' => 'approval',
            'dokumenData' => $dokumenData,
            'jenisDokumenList' => $jenisDokumenList,
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
            'pendingCount' => $this->dokumenModel->getPendingCount()
        ];
        
        // Return view with templates
        return view('direktur/templates/header', $data)
             . view('direktur/templates/sidebar', $data)
             . view('direktur/templates/navbar', $data)
             . view('direktur/approval/dokumen', $data)
             . view('direktur/templates/footer', $data);
    }

    /**
     * Display form dokumen detail
     */
    public function detail($id)
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        
        // Get form dokumen detail with complete info
        $dokumen = $this->dokumenModel->getDetailById($id);
        
        if (!$dokumen) {
            return redirect()->to(base_url('direktur/approval/dokumen'))
                ->with('error', 'Data pengajuan dokumen tidak ditemukan');
        }
        
        // Get user data for navbar
        $userId = $session->get('user_id');
        $userData = $this->userModel->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
            ->select('users.*, karyawan.nama_lengkap as karyawan_nama')
            ->where('users.id', $userId)
            ->first();
        
        $data = [
            'title' => 'Detail Pengajuan Dokumen',
            'subtitle' => 'Detail Pengajuan Dokumen Karyawan',
            'active' => 'approval',
            'dokumen' => $dokumen,
            'user' => $userData,
            'pendingCount' => $this->dokumenModel->getPendingCount()
        ];
        
        return view('direktur/templates/header', $data)
             . view('direktur/templates/sidebar', $data)
             . view('direktur/templates/navbar', $data)
             . view('direktur/approval/dokumen_detail', $data)
             . view('direktur/templates/footer', $data);
    }

    /**
     * Approve form dokumen
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
        
        // Check if form dokumen exists and needs approval
        $dokumen = $this->dokumenModel->find($id);
        if (!$dokumen) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data pengajuan dokumen tidak ditemukan']);
        }
        
        if ($dokumen['status_direktur'] !== 'Menunggu') {
            return $this->response->setJSON(['success' => false, 'message' => 'Pengajuan dokumen ini sudah diproses']);
        }
        
        if ($dokumen['status_hrd'] !== 'Diproses') {
            return $this->response->setJSON(['success' => false, 'message' => 'Pengajuan dokumen belum diproses oleh HRD']);
        }
        
        // Approve form dokumen
        $result = $this->dokumenModel->approveByDirektur($id, $this->userId, $this->karyawanId);
        
        if ($result) {
            // Log activity
            log_message('info', "Direktur approved dokumen ID: {$id} by user: {$this->userId}");
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pengajuan dokumen berhasil disetujui',
                'redirect' => base_url('direktur/approval/dokumen')
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyetujui pengajuan dokumen'
            ]);
        }
    }

    /**
     * Reject form dokumen
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
        
        // Check if form dokumen exists
        $dokumen = $this->dokumenModel->find($id);
        if (!$dokumen) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data pengajuan dokumen tidak ditemukan']);
        }
        
        if ($dokumen['status_direktur'] !== 'Menunggu') {
            return $this->response->setJSON(['success' => false, 'message' => 'Pengajuan dokumen ini sudah diproses']);
        }
        
        // Reject form dokumen
        $result = $this->dokumenModel->rejectByDirektur($id, $this->userId, $this->karyawanId, $alasan);
        
        if ($result) {
            // Log activity
            log_message('info', "Direktur rejected dokumen ID: {$id} by user: {$this->userId}");
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pengajuan dokumen berhasil ditolak',
                'redirect' => base_url('direktur/approval/dokumen')
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menolak pengajuan dokumen'
            ]);
        }
    }
    
    /**
     * Batch approve multiple form dokumen
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
            $dokumen = $this->dokumenModel->find($id);
            if ($dokumen && $dokumen['status_direktur'] === 'Menunggu' && $dokumen['status_hrd'] === 'Diproses') {
                $result = $this->dokumenModel->approveByDirektur($id, $this->userId, $this->karyawanId);
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
            'redirect' => base_url('direktur/approval/dokumen')
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
        $exportData = $this->dokumenModel->getForExport($startDate, $endDate, $statusFilter);
        
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
            ->setTitle("Laporan Pengajuan Dokumen")
            ->setSubject("Export Data Dokumen")
            ->setDescription("Laporan pengajuan dokumen periode $startDate s/d $endDate");
        
        // Set headers
        $headers = [
            'No', 'No. Form', 'Tanggal Pengajuan', 'NIK', 'Nama Karyawan', 'Jabatan', 'Departemen',
            'Jenis Dokumen', 'Keperluan', 'Keterangan Tambahan', 'Status HRD', 'Status Direktur', 'Status'
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
            $sheet->setCellValue('B' . $row, $data['nomor_form']);
            $sheet->setCellValue('C' . $row, date('d/m/Y', strtotime($data['tanggal_pengajuan'])));
            $sheet->setCellValue('D' . $row, $data['nik']);
            $sheet->setCellValue('E' . $row, $data['nama_lengkap']);
            $sheet->setCellValue('F' . $row, $data['jabatan'] ?? '-');
            $sheet->setCellValue('G' . $row, $data['departemen'] ?? '-');
            $sheet->setCellValue('H' . $row, $data['jenis_dokumen']);
            $sheet->setCellValue('I' . $row, substr($data['keperluan'], 0, 100));
            $sheet->setCellValue('J' . $row, substr($data['keterangan_tambahan'] ?? '-', 0, 100));
            $sheet->setCellValue('K' . $row, $data['status_hrd'] ?? '-');
            $sheet->setCellValue('L' . $row, $data['status_direktur'] ?? '-');
            $sheet->setCellValue('M' . $row, $data['status_keseluruhan'] ?? '-');
            $row++;
        }
        
        // Auto size columns
        foreach (range('A', 'M') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Set filename
        $filename = 'Laporan_Pengajuan_Dokumen_' . date('Ymd_His') . '.xlsx';
        
        // Create writer and output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit();
    }

    /**
     * Print form dokumen detail
     */
    public function print($id)
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        
        $dokumen = $this->dokumenModel->getDetailById($id);
        
        if (!$dokumen) {
            return redirect()->to(base_url('direktur/approval/dokumen'))
                ->with('error', 'Data pengajuan dokumen tidak ditemukan');
        }
        
        $data = [
            'title' => 'Cetak Pengajuan Dokumen',
            'dokumen' => $dokumen,
            'user' => $session->get()
        ];
        
        return view('direktur/approval/dokumen_print', $data);
    }
}