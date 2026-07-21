<?php

namespace App\Controllers\Direktur\Monitoring;

use App\Controllers\BaseController;
use App\Models\Direktur\RingkasanPenggajianModel;
use App\Models\KaryawanModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

class RingkasanPenggajian extends BaseController
{
    use ResponseTrait;
    
    protected $penggajianModel;
    protected $karyawanModel;
    protected $userModel;
    
    /**
     * Initialize controller
     */
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, 
                                 \CodeIgniter\HTTP\ResponseInterface $response, 
                                 \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        $this->penggajianModel = new RingkasanPenggajianModel();
        $this->karyawanModel = new KaryawanModel();
        $this->userModel = new UserModel();
    }

    /**
     * Display payroll list for direktur (monitoring only)
     */
    public function index()
    {
        // Cek session
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        
        // Get filter parameters
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan');
        $statusFilter = $this->request->getGet('status');
        $karyawanIdFilter = $this->request->getGet('karyawan_id');
        $searchQuery = $this->request->getGet('search');
        $page = $this->request->getGet('page') ?? 1;
        
        // Get per page setting
        $perPage = 20;
        $currentPage = (int) $page;
        $offset = ($currentPage - 1) * $perPage;
        
        // Build query
        $db = \Config\Database::connect();
        $builder = $db->table('ringkasan_penggajian');
        
        $builder->select('ringkasan_penggajian.*, karyawan.nik, karyawan.nama_lengkap, karyawan.nama_panggilan, karyawan.jabatan, karyawan.departemen')
                ->join('karyawan', 'karyawan.id = ringkasan_penggajian.karyawan_id')
                ->where('ringkasan_penggajian.deleted_at', null);
        
        // Apply filters
        $builder->where('ringkasan_penggajian.periode_tahun', $tahun);
        
        if ($bulan) {
            $builder->where('ringkasan_penggajian.periode_bulan', $bulan);
        }
        
        if ($statusFilter) {
            $builder->where('ringkasan_penggajian.status', $statusFilter);
        }
        
        if ($karyawanIdFilter) {
            $builder->where('ringkasan_penggajian.karyawan_id', $karyawanIdFilter);
        }
        
        if ($searchQuery) {
            $builder->groupStart()
                    ->like('karyawan.nama_lengkap', $searchQuery)
                    ->orLike('karyawan.nik', $searchQuery)
                    ->orLike('karyawan.nama_panggilan', $searchQuery)
                    ->groupEnd();
        }
        
        // Clone builder for total count
        $totalBuilder = clone $builder;
        $totalData = $totalBuilder->countAllResults();
        
        // Apply pagination and get results
        $builder->limit($perPage, $offset);
        $builder->orderBy('ringkasan_penggajian.gaji_bersih', 'DESC');
        
        $penggajianData = $builder->get()->getResultArray();
        
        // Get statistics
        $stats = $this->penggajianModel->getSummaryStats($tahun, $bulan);
        
        // Get summary by department
        $summaryByDepartment = $this->penggajianModel->getSummaryByDepartment($tahun, $bulan);
        
        // Get top earners
        $topEarners = $this->penggajianModel->getTopEarners($tahun, $bulan, 5);
        
        // Get karyawan list for dropdown
        $karyawanList = $this->karyawanModel->select('id, nik, nama_lengkap, nama_panggilan, jabatan')
                                      ->where('deleted_at', null)
                                      ->orderBy('nama_lengkap', 'ASC')
                                      ->findAll();
        
        // Get available years from data
        try {
            $availableYears = $this->penggajianModel->getAvailableYears();
        } catch (\Exception $e) {
            $availableYears = [date('Y')];
        }
        if (empty($availableYears)) {
            $availableYears = [date('Y')];
        }
        
        // Get available months for selected year
        try {
            $availableMonths = $this->penggajianModel->getAvailableMonths($tahun);
        } catch (\Exception $e) {
            $availableMonths = [];
        }
        
        // Calculate pagination
        $totalPages = ceil($totalData / $perPage);
        
        // Build base URL for pagination links
        $queryParams = [
            'tahun' => $tahun,
            'bulan' => $bulan,
            'status' => $statusFilter,
            'karyawan_id' => $karyawanIdFilter,
            'search' => $searchQuery
        ];
        
        $baseUrl = base_url('direktur/monitoring/ringkasan-penggajian') . '?' . http_build_query(array_filter($queryParams));
        
        // Get user data for navbar
        $userId = $session->get('user_id');
        $userData = $this->userModel->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
            ->select('users.*, karyawan.nama_lengkap as karyawan_nama, karyawan.nama_panggilan')
            ->where('users.id', $userId)
            ->first();
        
        // Status options
        $statusOptions = [
            'draft' => 'Draft',
            'proses' => 'Proses',
            'approved' => 'Disetujui',
            'paid' => 'Dibayar',
            'rejected' => 'Ditolak'
        ];
        
        // Month names
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        // Payment methods
        $paymentMethods = [
            'transfer' => 'Transfer Bank',
            'tunai' => 'Tunai',
            'cheque' => 'Cheque/Giro'
        ];
        
        // Prepare data for view
        $data = [
            'title' => 'Ringkasan Penggajian Karyawan',
            'subtitle' => 'Monitoring Gaji dan Kompensasi Karyawan',
            'active' => 'monitoring',
            'penggajianData' => $penggajianData,
            'karyawanList' => $karyawanList,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'statusFilter' => $statusFilter,
            'karyawanIdFilter' => $karyawanIdFilter,
            'searchQuery' => $searchQuery,
            'currentPage' => $currentPage,
            'perPage' => $perPage,
            'totalData' => $totalData,
            'totalPages' => $totalPages,
            'baseUrl' => $baseUrl,
            'queryParams' => $queryParams,
            'user' => $userData,
            'stats' => $stats,
            'summaryByDepartment' => $summaryByDepartment,
            'topEarners' => $topEarners,
            'availableYears' => $availableYears,
            'availableMonths' => $availableMonths,
            'statusOptions' => $statusOptions,
            'paymentMethods' => $paymentMethods,
            'monthNames' => $monthNames
        ];
        
        // Return view dengan include template
        return view('direktur/templates/header', $data)
             . view('direktur/templates/sidebar', $data)
             . view('direktur/templates/navbar', $data)
             . view('direktur/monitoring/ringkasan_penggajian', $data)
             . view('direktur/templates/footer', $data);
    }

    /**
     * Display payroll detail
     */
    public function detail($id)
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        
        // Get payroll data with employee info
        $db = \Config\Database::connect();
        $builder = $db->table('ringkasan_penggajian')
            ->select('ringkasan_penggajian.*, 
                karyawan.nik, karyawan.nama_lengkap, karyawan.nama_panggilan, karyawan.jabatan, karyawan.departemen,
                creator.username as created_by_username,
                creator_karyawan.nama_lengkap as created_by_name,
                updater.username as updated_by_username,
                updater_karyawan.nama_lengkap as updated_by_name,
                approver.username as approver_username,
                approver_karyawan.nama_lengkap as approver_name')
            ->join('karyawan', 'karyawan.id = ringkasan_penggajian.karyawan_id')
            ->join('users as creator', 'creator.id = ringkasan_penggajian.created_by', 'left')
            ->join('karyawan as creator_karyawan', 'creator_karyawan.id = creator.karyawan_id', 'left')
            ->join('users as updater', 'updater.id = ringkasan_penggajian.updated_by', 'left')
            ->join('karyawan as updater_karyawan', 'updater_karyawan.id = updater.karyawan_id', 'left')
            ->join('users as approver', 'approver.id = ringkasan_penggajian.approved_by', 'left')
            ->join('karyawan as approver_karyawan', 'approver_karyawan.id = approver.karyawan_id', 'left')
            ->where('ringkasan_penggajian.id', $id)
            ->where('ringkasan_penggajian.deleted_at', null);
        
        $penggajian = $builder->get()->getRowArray();
        
        if (!$penggajian) {
            return redirect()->to(base_url('direktur/monitoring/ringkasan-penggajian'))
                ->with('error', 'Data penggajian tidak ditemukan');
        }
        
        // Get historical payroll data for this karyawan
        $historyData = $this->penggajianModel->getByKaryawan($penggajian['karyawan_id'], 6);
        
        // Get user data for navbar
        $userId = $session->get('user_id');
        $userData = $this->userModel->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
            ->select('users.*, karyawan.nama_lengkap as karyawan_nama')
            ->where('users.id', $userId)
            ->first();
        
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $statusClass = [
            'draft' => 'secondary',
            'proses' => 'info',
            'approved' => 'success',
            'paid' => 'primary',
            'rejected' => 'danger'
        ];
        
        $statusLabel = [
            'draft' => 'Draft',
            'proses' => 'Diproses',
            'approved' => 'Disetujui',
            'paid' => 'Sudah Dibayar',
            'rejected' => 'Ditolak'
        ];
        
        $data = [
            'title' => 'Detail Penggajian Karyawan',
            'active' => 'monitoring',
            'penggajian' => $penggajian,
            'historyData' => $historyData,
            'monthNames' => $monthNames,
            'statusClass' => $statusClass,
            'statusLabel' => $statusLabel,
            'user' => $userData
        ];
        
        return view('direktur/templates/header', $data)
             . view('direktur/templates/sidebar', $data)
             . view('direktur/templates/navbar', $data)
             . view('direktur/monitoring/ringkasan_penggajian_detail', $data)
             . view('direktur/templates/footer', $data);
    }

    /**
     * Get payroll summary for chart (AJAX)
     */
    public function getSummary()
    {
        if (!$this->request->isAJAX()) {
            return $this->fail('Only AJAX requests are allowed', 400);
        }
        
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan');
        
        $stats = $this->penggajianModel->getSummaryStats($tahun, $bulan);
        $summaryByDepartment = $this->penggajianModel->getSummaryByDepartment($tahun, $bulan);
        
        return $this->respond([
            'status' => 'success',
            'stats' => $stats,
            'summaryByDepartment' => $summaryByDepartment
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
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan');
        $statusFilter = $this->request->getGet('status');
        $karyawanIdFilter = $this->request->getGet('karyawan_id');
        
        // Build query
        $db = \Config\Database::connect();
        $builder = $db->table('ringkasan_penggajian');
        
        $builder->select('
            ringkasan_penggajian.*,
            karyawan.nik,
            karyawan.nama_lengkap,
            karyawan.nama_panggilan,
            karyawan.jabatan,
            karyawan.departemen
        ')
        ->join('karyawan', 'karyawan.id = ringkasan_penggajian.karyawan_id')
        ->where('ringkasan_penggajian.deleted_at', null);
        
        // Apply filters
        $builder->where('ringkasan_penggajian.periode_tahun', $tahun);
        
        if ($bulan) {
            $builder->where('ringkasan_penggajian.periode_bulan', $bulan);
        }
        
        if ($statusFilter) {
            $builder->where('ringkasan_penggajian.status', $statusFilter);
        }
        
        if ($karyawanIdFilter) {
            $builder->where('ringkasan_penggajian.karyawan_id', $karyawanIdFilter);
        }
        
        $builder->orderBy('ringkasan_penggajian.gaji_bersih', 'DESC');
        
        $penggajianData = $builder->get()->getResultArray();
        
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
            ->setCreator("CDW Engineering HR System")
            ->setLastModifiedBy("CDW Engineering HR System")
            ->setTitle("Laporan Penggajian Karyawan")
            ->setSubject("Export Data Penggajian")
            ->setDescription("Laporan penggajian karyawan periode tahun $tahun" . ($bulan ? " bulan $bulan" : ""));
        
        // Set headers
        $headers = [
            'No', 'NIK', 'Nama Karyawan', 'Jabatan', 'Departemen',
            'Periode', 'Gaji Pokok', 'Tunjangan', 'Lembur', 'Bonus',
            'Total Penghasilan', 'Potongan BPJS', 'Potongan Lain', 'Total Potongan',
            'Gaji Bersih', 'Hadir', 'Lembur (Jam)', 'Status'
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
        
        $sheet->getStyle('A1:R1')->applyFromArray($headerStyle);
        
        // Month names
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        // Status labels
        $statusLabel = [
            'draft' => 'Draft',
            'proses' => 'Diproses',
            'approved' => 'Disetujui',
            'paid' => 'Dibayar',
            'rejected' => 'Ditolak'
        ];
        
        // Write data
        $row = 2;
        $no = 1;
        
        foreach ($penggajianData as $data) {
            $periode = ($monthNames[$data['periode_bulan']] ?? $data['periode_bulan']) . ' ' . $data['periode_tahun'];
            $totalTunjangan = ($data['tunjangan_jabatan'] ?? 0) + ($data['tunjangan_makan'] ?? 0) + 
                              ($data['tunjangan_transport'] ?? 0) + ($data['tunjangan_kesehatan'] ?? 0) +
                              ($data['tunjangan_hari_raya'] ?? 0) + ($data['tunjangan_lainnya'] ?? 0);
            $totalBonus = ($data['bonus_kinerja'] ?? 0) + ($data['insentif_proyek'] ?? 0) + ($data['komisi_penjualan'] ?? 0);
            $totalPotonganBpjs = ($data['potongan_bpjs_kesehatan'] ?? 0) + ($data['potongan_bpjs_tenaga_kerja'] ?? 0);
            $totalPotonganLain = ($data['potongan_pph21'] ?? 0) + ($data['potongan_absensi'] ?? 0) + 
                                 ($data['potongan_pinjaman'] ?? 0) + ($data['potongan_lainnya'] ?? 0);
            
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $data['nik']);
            $sheet->setCellValue('C' . $row, $data['nama_panggilan'] ?? $data['nama_lengkap']);
            $sheet->setCellValue('D' . $row, $data['jabatan'] ?? '-');
            $sheet->setCellValue('E' . $row, $data['departemen'] ?? '-');
            $sheet->setCellValue('F' . $row, $periode);
            $sheet->setCellValue('G' . $row, number_format($data['gaji_pokok'] ?? 0, 0, ',', '.'));
            $sheet->setCellValue('H' . $row, number_format($totalTunjangan, 0, ',', '.'));
            $sheet->setCellValue('I' . $row, number_format($data['lembur'] ?? 0, 0, ',', '.'));
            $sheet->setCellValue('J' . $row, number_format($totalBonus, 0, ',', '.'));
            $sheet->setCellValue('K' . $row, number_format($data['total_penghasilan'] ?? 0, 0, ',', '.'));
            $sheet->setCellValue('L' . $row, number_format($totalPotonganBpjs, 0, ',', '.'));
            $sheet->setCellValue('M' . $row, number_format($totalPotonganLain, 0, ',', '.'));
            $sheet->setCellValue('N' . $row, number_format($data['total_potongan'] ?? 0, 0, ',', '.'));
            $sheet->setCellValue('O' . $row, number_format($data['gaji_bersih'] ?? 0, 0, ',', '.'));
            $sheet->setCellValue('P' . $row, $data['jumlah_hadir'] ?? 0);
            $sheet->setCellValue('Q' . $row, number_format($data['total_jam_lembur'] ?? 0, 1));
            $sheet->setCellValue('R' . $row, $statusLabel[$data['status']] ?? $data['status']);
            
            $row++;
        }
        
        // Auto size columns
        foreach (range('A', 'R') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Set filename
        $filename = 'Laporan_Penggajian_' . date('Ymd_His') . '.xlsx';
        
        // Create writer and output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit();
    }

    /**
     * Print payroll report (view only)
     */
    public function print()
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        
        // Get filter parameters
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan');
        $statusFilter = $this->request->getGet('status');
        $karyawanIdFilter = $this->request->getGet('karyawan_id');
        
        // Build query
        $db = \Config\Database::connect();
        $builder = $db->table('ringkasan_penggajian');
        
        $builder->select('ringkasan_penggajian.*, karyawan.nik, karyawan.nama_lengkap, karyawan.nama_panggilan, karyawan.jabatan, karyawan.departemen')
                ->join('karyawan', 'karyawan.id = ringkasan_penggajian.karyawan_id')
                ->where('ringkasan_penggajian.deleted_at', null);
        
        // Apply filters
        $builder->where('ringkasan_penggajian.periode_tahun', $tahun);
        
        if ($bulan) {
            $builder->where('ringkasan_penggajian.periode_bulan', $bulan);
        }
        
        if ($statusFilter) {
            $builder->where('ringkasan_penggajian.status', $statusFilter);
        }
        
        if ($karyawanIdFilter) {
            $builder->where('ringkasan_penggajian.karyawan_id', $karyawanIdFilter);
        }
        
        $builder->orderBy('ringkasan_penggajian.gaji_bersih', 'DESC');
        
        $penggajianData = $builder->get()->getResultArray();
        
        // Get statistics
        $stats = $this->penggajianModel->getSummaryStats($tahun, $bulan);
        
        // Get selected karyawan info if filtered
        $selectedKaryawan = null;
        if ($karyawanIdFilter) {
            $selectedKaryawan = $this->karyawanModel->find($karyawanIdFilter);
        }
        
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $statusLabel = [
            'draft' => 'Draft',
            'proses' => 'Diproses',
            'approved' => 'Disetujui',
            'paid' => 'Dibayar',
            'rejected' => 'Ditolak'
        ];
        
        // Prepare data for view
        $data = [
            'title' => 'Cetak Laporan Penggajian',
            'active' => 'monitoring',
            'penggajianData' => $penggajianData,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'statusFilter' => $statusFilter,
            'karyawanIdFilter' => $karyawanIdFilter,
            'selectedKaryawan' => $selectedKaryawan,
            'stats' => $stats,
            'monthNames' => $monthNames,
            'statusLabel' => $statusLabel,
            'user' => $session->get()
        ];
        
        // Return the print view (simple HTML for printing)
        return view('direktur/monitoring/ringkasan_penggajian_print', $data);
    }

    /**
     * Get top earners for dashboard (AJAX)
     */
    public function getTopEarners()
    {
        if (!$this->request->isAJAX()) {
            return $this->fail('Only AJAX requests are allowed', 400);
        }
        
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan');
        $limit = $this->request->getGet('limit') ?? 5;
        
        $topEarners = $this->penggajianModel->getTopEarners($tahun, $bulan, $limit);
        
        return $this->respond([
            'status' => 'success',
            'data' => $topEarners
        ]);
    }

    /**
     * Get summary by department for chart (AJAX)
     */
    public function getDepartmentSummary()
    {
        if (!$this->request->isAJAX()) {
            return $this->fail('Only AJAX requests are allowed', 400);
        }
        
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan');
        
        $summaryByDepartment = $this->penggajianModel->getSummaryByDepartment($tahun, $bulan);
        
        return $this->respond([
            'status' => 'success',
            'data' => $summaryByDepartment
        ]);
    }
}