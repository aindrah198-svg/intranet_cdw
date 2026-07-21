<?php
// app/Controllers/Direktur/Approval/PembelianController.php

namespace App\Controllers\Direktur\Approval;

use App\Controllers\BaseController;
use App\Models\Direktur\PembelianModel;
use App\Models\KaryawanModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

class PembelianController extends BaseController
{
    use ResponseTrait;
    
    protected $pembelianModel;
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
        
        $this->pembelianModel = new PembelianModel();
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
     * Display list of form pembelian for direktur approval
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
        $prioritasFilter = $this->request->getGet('prioritas');
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
        $builder = $db->table('form_pembelian');
        
        $builder->select('
            form_pembelian.*,
            karyawan.nik,
            karyawan.nama_lengkap,
            karyawan.nama_panggilan,
            karyawan.jabatan,
            karyawan.departemen,
            karyawan.foto
        ');
        
        $builder->join('karyawan', 'karyawan.id = form_pembelian.karyawan_id');
        $builder->where('form_pembelian.deleted_at', null);
        
        // Filter status untuk approval direktur
        if ($statusFilter === 'pending') {
            $builder->where('form_pembelian.status_direktur', 'Menunggu');
            $builder->where('form_pembelian.status_hrd', 'Disetujui HRD');
        } elseif ($statusFilter === 'approved') {
            $builder->where('form_pembelian.status_direktur', 'Disetujui');
        } elseif ($statusFilter === 'rejected') {
            $builder->where('form_pembelian.status_direktur', 'Ditolak');
        } elseif ($statusFilter === 'ordered') {
            $builder->where('form_pembelian.status_keseluruhan', 'Dipesan');
        } elseif ($statusFilter === 'completed') {
            $builder->where('form_pembelian.status_keseluruhan', 'Selesai');
        } else {
            // Default: tampilkan yang menunggu, disetujui, ditolak
            $builder->whereIn('form_pembelian.status_direktur', ['Menunggu', 'Disetujui', 'Ditolak']);
            $builder->where('form_pembelian.status_keseluruhan !=', 'Draft');
        }
        
        // Apply prioritas filter
        if ($prioritasFilter) {
            $builder->where('form_pembelian.prioritas', $prioritasFilter);
        }
        
        // Apply search filter
        if ($searchQuery) {
            $builder->groupStart()
                    ->like('form_pembelian.nomor_pr', $searchQuery)
                    ->orLike('karyawan.nama_lengkap', $searchQuery)
                    ->orLike('karyawan.nik', $searchQuery)
                    ->orLike('form_pembelian.alasan_pembelian', $searchQuery)
                    ->orLike('form_pembelian.supplier', $searchQuery)
                    ->groupEnd();
        }
        
        // Apply date filter
        if ($startDate && $endDate) {
            $builder->where('form_pembelian.tanggal_pengajuan >=', $startDate);
            $builder->where('form_pembelian.tanggal_pengajuan <=', $endDate . ' 23:59:59');
        }
        
        // Clone builder for total count
        $totalBuilder = clone $builder;
        $totalData = $totalBuilder->countAllResults();
        
        // Apply pagination and get results
        $builder->limit($perPage, $offset);
        $builder->orderBy('form_pembelian.tanggal_pengajuan', 'DESC');
        
        $pembelianData = $builder->get()->getResultArray();
        
        // Get statistics
        $stats = $this->pembelianModel->getStatistics($startDate, $endDate);
        
        // Get prioritas list for filter
        $prioritasList = $this->pembelianModel->getPrioritasList();
        
        // Calculate pagination
        $totalPages = ceil($totalData / $perPage);
        
        // Build base URL for pagination links
        $queryParams = [
            'status' => $statusFilter,
            'prioritas' => $prioritasFilter,
            'search' => $searchQuery,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
        
        $baseUrl = base_url('direktur/approval/pembelian') . '?' . http_build_query(array_filter($queryParams));
        
        // Get user data for navbar
        $userId = $session->get('user_id');
        $userData = $this->userModel->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
            ->select('users.*, karyawan.nama_lengkap as karyawan_nama, karyawan.nama_panggilan')
            ->where('users.id', $userId)
            ->first();
        
        // Prepare data for view
        $data = [
            'title' => 'Approval Pembelian',
            'subtitle' => 'Persetujuan Purchase Request (PR) oleh Direktur',
            'active' => 'approval',
            'pembelianData' => $pembelianData,
            'prioritasList' => $prioritasList,
            'statusFilter' => $statusFilter,
            'prioritasFilter' => $prioritasFilter,
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
            'pendingCount' => $this->pembelianModel->getPendingCount()
        ];
        
        // Return view with templates
        return view('direktur/templates/header', $data)
             . view('direktur/templates/sidebar', $data)
             . view('direktur/templates/navbar', $data)
             . view('direktur/approval/pembelian', $data)
             . view('direktur/templates/footer', $data);
    }

    /**
     * Display form pembelian detail
     */
    public function detail($id)
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        
        // Get form pembelian detail with complete info
        $pembelian = $this->pembelianModel->getDetailById($id);
        
        if (!$pembelian) {
            return redirect()->to(base_url('direktur/approval/pembelian'))
                ->with('error', 'Data purchase request tidak ditemukan');
        }
        
        // Get items for this purchase request
        $items = $this->pembelianModel->getItems($id);
        
        // Get user data for navbar
        $userId = $session->get('user_id');
        $userData = $this->userModel->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
            ->select('users.*, karyawan.nama_lengkap as karyawan_nama')
            ->where('users.id', $userId)
            ->first();
        
        $data = [
            'title' => 'Detail Purchase Request',
            'subtitle' => 'Detail Pengajuan Pembelian Barang',
            'active' => 'approval',
            'pembelian' => $pembelian,
            'items' => $items,
            'user' => $userData,
            'pendingCount' => $this->pembelianModel->getPendingCount()
        ];
        
        return view('direktur/templates/header', $data)
             . view('direktur/templates/sidebar', $data)
             . view('direktur/templates/navbar', $data)
             . view('direktur/approval/pembelian_detail', $data)
             . view('direktur/templates/footer', $data);
    }

    /**
     * Approve form pembelian
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
        
        // Check if form pembelian exists and needs approval
        $pembelian = $this->pembelianModel->find($id);
        if (!$pembelian) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data purchase request tidak ditemukan']);
        }
        
        if ($pembelian['status_direktur'] !== 'Menunggu') {
            return $this->response->setJSON(['success' => false, 'message' => 'Purchase request ini sudah diproses']);
        }
        
        if ($pembelian['status_hrd'] !== 'Disetujui HRD') {
            return $this->response->setJSON(['success' => false, 'message' => 'Purchase request belum disetujui HRD']);
        }
        
        // Approve form pembelian
        $result = $this->pembelianModel->approveByDirektur($id, $this->userId, $this->karyawanId);
        
        if ($result) {
            // Log activity
            log_message('info', "Direktur approved pembelian ID: {$id} by user: {$this->userId}");
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Purchase request berhasil disetujui',
                'redirect' => base_url('direktur/approval/pembelian')
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyetujui purchase request'
            ]);
        }
    }

    /**
     * Reject form pembelian
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
        
        // Check if form pembelian exists
        $pembelian = $this->pembelianModel->find($id);
        if (!$pembelian) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data purchase request tidak ditemukan']);
        }
        
        if ($pembelian['status_direktur'] !== 'Menunggu') {
            return $this->response->setJSON(['success' => false, 'message' => 'Purchase request ini sudah diproses']);
        }
        
        // Reject form pembelian
        $result = $this->pembelianModel->rejectByDirektur($id, $this->userId, $this->karyawanId, $alasan);
        
        if ($result) {
            // Log activity
            log_message('info', "Direktur rejected pembelian ID: {$id} by user: {$this->userId}");
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Purchase request berhasil ditolak',
                'redirect' => base_url('direktur/approval/pembelian')
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menolak purchase request'
            ]);
        }
    }
    
    /**
     * Batch approve multiple form pembelian
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
            $pembelian = $this->pembelianModel->find($id);
            if ($pembelian && $pembelian['status_direktur'] === 'Menunggu' && $pembelian['status_hrd'] === 'Disetujui HRD') {
                $result = $this->pembelianModel->approveByDirektur($id, $this->userId, $this->karyawanId);
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
            'message' => "{$successCount} purchase request berhasil disetujui, {$failCount} gagal",
            'success_count' => $successCount,
            'fail_count' => $failCount,
            'redirect' => base_url('direktur/approval/pembelian')
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
        $exportData = $this->pembelianModel->getForExport($startDate, $endDate, $statusFilter);
        
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
            ->setTitle("Laporan Purchase Request")
            ->setSubject("Export Data PR")
            ->setDescription("Laporan purchase request periode $startDate s/d $endDate");
        
        // Set headers
        $headers = [
            'No', 'No. PR', 'Tanggal Pengajuan', 'NIK', 'Nama Karyawan', 'Jabatan', 'Departemen',
            'Prioritas', 'Alasan Pembelian', 'Total Estimasi', 'Supplier', 'No. PO',
            'Status HRD', 'Status Direktur', 'Status'
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
            $sheet->setCellValue('B' . $row, $data['nomor_pr']);
            $sheet->setCellValue('C' . $row, date('d/m/Y', strtotime($data['tanggal_pengajuan'])));
            $sheet->setCellValue('D' . $row, $data['nik']);
            $sheet->setCellValue('E' . $row, $data['nama_lengkap']);
            $sheet->setCellValue('F' . $row, $data['jabatan'] ?? '-');
            $sheet->setCellValue('G' . $row, $data['departemen'] ?? '-');
            $sheet->setCellValue('H' . $row, $data['prioritas'] ?? '-');
            $sheet->setCellValue('I' . $row, substr($data['alasan_pembelian'], 0, 100));
            $sheet->setCellValue('J' . $row, $this->pembelianModel->formatCurrency($data['total_estimasi'] ?? 0));
            $sheet->setCellValue('K' . $row, $data['supplier'] ?? '-');
            $sheet->setCellValue('L' . $row, $data['no_po_dibuat'] ?? '-');
            $sheet->setCellValue('M' . $row, $data['status_hrd'] ?? '-');
            $sheet->setCellValue('N' . $row, $data['status_direktur'] ?? '-');
            $sheet->setCellValue('O' . $row, $data['status_keseluruhan'] ?? '-');
            $row++;
        }
        
        // Auto size columns
        foreach (range('A', 'O') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Set filename
        $filename = 'Laporan_Purchase_Request_' . date('Ymd_His') . '.xlsx';
        
        // Create writer and output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit();
    }

    /**
     * Print form pembelian detail
     */
    public function print($id)
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        
        $pembelian = $this->pembelianModel->getDetailById($id);
        
        if (!$pembelian) {
            return redirect()->to(base_url('direktur/approval/pembelian'))
                ->with('error', 'Data purchase request tidak ditemukan');
        }
        
        $items = $this->pembelianModel->getItems($id);
        
        $data = [
            'title' => 'Cetak Purchase Request',
            'pembelian' => $pembelian,
            'items' => $items,
            'user' => $session->get()
        ];
        
        return view('direktur/approval/pembelian_print', $data);
    }
}