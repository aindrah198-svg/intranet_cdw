<?php

namespace App\Controllers\Direktur\Monitoring;

use App\Controllers\BaseController;
use App\Models\Direktur\KaryawanPerformansiModel;
use App\Models\KaryawanModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

class Performansi extends BaseController
{
    use ResponseTrait;
    
    protected $performansiModel;
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
        
        $this->performansiModel = new KaryawanPerformansiModel();
        $this->karyawanModel = new KaryawanModel();
        $this->userModel = new UserModel();
    }

    /**
     * Display performance list for direktur (monitoring only)
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
        $gradeFilter = $this->request->getGet('grade');
        $karyawanIdFilter = $this->request->getGet('karyawan_id');
        $searchQuery = $this->request->getGet('search');
        $page = $this->request->getGet('page') ?? 1;
        
        // Get per page setting
        $perPage = 20;
        $currentPage = (int) $page;
        $offset = ($currentPage - 1) * $perPage;
        
        // Build query
        $db = \Config\Database::connect();
        $builder = $db->table('karyawan_performansi');
        
        $builder->select('karyawan_performansi.*, karyawan.nik, karyawan.nama_lengkap, karyawan.nama_panggilan, karyawan.jabatan, karyawan.departemen')
                ->join('karyawan', 'karyawan.id = karyawan_performansi.karyawan_id')
                ->where('karyawan_performansi.deleted_at', null);
        
        // Apply filters
        $builder->where('karyawan_performansi.periode_tahun', $tahun);
        
        if ($bulan) {
            $builder->where('karyawan_performansi.periode_bulan', $bulan);
        }
        
        if ($gradeFilter) {
            $builder->where('karyawan_performansi.grade', $gradeFilter);
        }
        
        if ($karyawanIdFilter) {
            $builder->where('karyawan_performansi.karyawan_id', $karyawanIdFilter);
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
        $builder->orderBy('karyawan_performansi.skor_total', 'DESC');
        
        $performansiData = $builder->get()->getResultArray();
        
        // Get statistics
        $stats = $this->performansiModel->getSummaryStats($tahun, $bulan);
        
        // Get karyawan list for dropdown
        $karyawanList = $this->karyawanModel->select('id, nik, nama_lengkap, nama_panggilan, jabatan')
                                      ->where('deleted_at', null)
                                      ->orderBy('nama_lengkap', 'ASC')
                                      ->findAll();
        
        // Get available years from data
        $availableYears = $this->performansiModel->getAvailableYears();
        if (empty($availableYears)) {
            $availableYears = [date('Y')];
        }
        
        // Get available months for selected year
        $availableMonths = $this->performansiModel->getAvailableMonths($tahun);
        
        // Calculate pagination
        $totalPages = ceil($totalData / $perPage);
        
        // Build base URL for pagination links
        $queryParams = [
            'tahun' => $tahun,
            'bulan' => $bulan,
            'grade' => $gradeFilter,
            'karyawan_id' => $karyawanIdFilter,
            'search' => $searchQuery
        ];
        
        $baseUrl = base_url('direktur/monitoring/performansi') . '?' . http_build_query(array_filter($queryParams));
        
        // Get user data for navbar
        $userId = $session->get('user_id');
        $userData = $this->userModel->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
            ->select('users.*, karyawan.nama_lengkap as karyawan_nama, karyawan.nama_panggilan')
            ->where('users.id', $userId)
            ->first();
        
        // Grade options
        $gradeOptions = [
            'A' => 'A (Sangat Baik - 90+)',
            'B' => 'B (Baik - 75-89)',
            'C' => 'C (Cukup - 60-74)',
            'D' => 'D (Kurang - 50-59)',
            'E' => 'E (Buruk - <50)'
        ];
        
        // Month names
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        // Prepare data for view
        $data = [
            'title' => 'Monitoring Performansi Karyawan',
            'subtitle' => 'Pantau Performansi dan KPI Karyawan',
            'active' => 'monitoring',
            'performansiData' => $performansiData,
            'karyawanList' => $karyawanList,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'gradeFilter' => $gradeFilter,
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
            'availableYears' => $availableYears,
            'availableMonths' => $availableMonths,
            'gradeOptions' => $gradeOptions,
            'monthNames' => $monthNames
        ];
        
        // Return view dengan include template
        return view('direktur/templates/header', $data)
             . view('direktur/templates/sidebar', $data)
             . view('direktur/templates/navbar', $data)
             . view('direktur/monitoring/performansi', $data)
             . view('direktur/templates/footer', $data);
    }

    /**
     * Display performance detail
     */
    public function detail($id)
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        
        // Get performance data with employee info
        $db = \Config\Database::connect();
        $builder = $db->table('karyawan_performansi')
            ->select('karyawan_performansi.*, 
                karyawan.nik, karyawan.nama_lengkap, karyawan.nama_panggilan, karyawan.jabatan, karyawan.departemen,
                creator.username as created_by_username,
                creator_karyawan.nama_lengkap as created_by_name,
                updater.username as updated_by_username,
                updater_karyawan.nama_lengkap as updated_by_name,
                approver.username as approver_username,
                approver_karyawan.nama_lengkap as approver_name,
                evaluator.username as evaluator_username,
                evaluator_karyawan.nama_lengkap as evaluator_name')
            ->join('karyawan', 'karyawan.id = karyawan_performansi.karyawan_id')
            ->join('users as creator', 'creator.id = karyawan_performansi.created_by', 'left')
            ->join('karyawan as creator_karyawan', 'creator_karyawan.id = creator.karyawan_id', 'left')
            ->join('users as updater', 'updater.id = karyawan_performansi.updated_by', 'left')
            ->join('karyawan as updater_karyawan', 'updater_karyawan.id = updater.karyawan_id', 'left')
            ->join('users as approver', 'approver.id = karyawan_performansi.approved_by', 'left')
            ->join('karyawan as approver_karyawan', 'approver_karyawan.id = approver.karyawan_id', 'left')
            ->join('users as evaluator', 'evaluator.id = karyawan_performansi.evaluated_by', 'left')
            ->join('karyawan as evaluator_karyawan', 'evaluator_karyawan.id = evaluator.karyawan_id', 'left')
            ->where('karyawan_performansi.id', $id)
            ->where('karyawan_performansi.deleted_at', null);
        
        $performansi = $builder->get()->getRowArray();
        
        if (!$performansi) {
            return redirect()->to(base_url('direktur/monitoring/performansi'))
                ->with('error', 'Data performansi tidak ditemukan');
        }
        
        // Get historical trend data for this karyawan
        $trendData = $this->performansiModel->getTrend($performansi['karyawan_id'], 6);
        
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
        
        $data = [
            'title' => 'Detail Performansi Karyawan',
            'active' => 'monitoring',
            'performansi' => $performansi,
            'trendData' => $trendData,
            'monthNames' => $monthNames,
            'user' => $userData
        ];
        
        return view('direktur/templates/header', $data)
             . view('direktur/templates/sidebar', $data)
             . view('direktur/templates/navbar', $data)
             . view('direktur/monitoring/performansi_detail', $data)
             . view('direktur/templates/footer', $data);
    }

    /**
     * Get performance summary for chart (AJAX)
     */
    public function getSummary()
    {
        if (!$this->request->isAJAX()) {
            return $this->fail('Only AJAX requests are allowed', 400);
        }
        
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan');
        
        $stats = $this->performansiModel->getSummaryStats($tahun, $bulan);
        
        // Get grade distribution
        $db = \Config\Database::connect();
        $builder = $db->table('karyawan_performansi')
            ->select("
                grade,
                COUNT(*) as count
            ")
            ->where('periode_tahun', $tahun)
            ->where('deleted_at', null);
        
        if ($bulan) {
            $builder->where('periode_bulan', $bulan);
        }
        
        $builder->groupBy('grade');
        $gradeDistribution = $builder->get()->getResultArray();
        
        $grades = ['A', 'B', 'C', 'D', 'E'];
        $gradeCounts = [];
        foreach ($grades as $grade) {
            $found = false;
            foreach ($gradeDistribution as $gd) {
                if ($gd['grade'] == $grade) {
                    $gradeCounts[$grade] = $gd['count'];
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $gradeCounts[$grade] = 0;
            }
        }
        
        return $this->respond([
            'status' => 'success',
            'stats' => $stats,
            'gradeDistribution' => $gradeCounts
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
        $gradeFilter = $this->request->getGet('grade');
        $karyawanIdFilter = $this->request->getGet('karyawan_id');
        
        // Build query
        $db = \Config\Database::connect();
        $builder = $db->table('karyawan_performansi');
        
        $builder->select('
            karyawan_performansi.*,
            karyawan.nik,
            karyawan.nama_lengkap,
            karyawan.nama_panggilan,
            karyawan.jabatan,
            karyawan.departemen
        ')
        ->join('karyawan', 'karyawan.id = karyawan_performansi.karyawan_id')
        ->where('karyawan_performansi.deleted_at', null);
        
        // Apply filters
        $builder->where('karyawan_performansi.periode_tahun', $tahun);
        
        if ($bulan) {
            $builder->where('karyawan_performansi.periode_bulan', $bulan);
        }
        
        if ($gradeFilter) {
            $builder->where('karyawan_performansi.grade', $gradeFilter);
        }
        
        if ($karyawanIdFilter) {
            $builder->where('karyawan_performansi.karyawan_id', $karyawanIdFilter);
        }
        
        $builder->orderBy('karyawan_performansi.skor_total', 'DESC');
        
        $performansiData = $builder->get()->getResultArray();
        
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
            ->setTitle("Laporan Performansi Karyawan")
            ->setSubject("Export Data Performansi")
            ->setDescription("Laporan performansi karyawan periode tahun $tahun" . ($bulan ? " bulan $bulan" : ""));
        
        // Set headers
        $headers = [
            'No', 'NIK', 'Nama Karyawan', 'Jabatan', 'Departemen',
            'Periode', 'Skor Kehadiran', 'Skor Kualitas', 'Skor Inisiatif',
            'Skor Kedisiplinan', 'Skor Khusus', 'Skor Total', 'Grade', 'Predikat',
            'Status', 'Catatan Atasan'
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
        
        // Month names
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        // Write data
        $row = 2;
        $no = 1;
        
        foreach ($performansiData as $data) {
            $periode = $monthNames[$data['periode_bulan']] . ' ' . $data['periode_tahun'];
            
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $data['nik']);
            $sheet->setCellValue('C' . $row, $data['nama_panggilan'] ?? $data['nama_lengkap']);
            $sheet->setCellValue('D' . $row, $data['jabatan'] ?? '-');
            $sheet->setCellValue('E' . $row, $data['departemen'] ?? '-');
            $sheet->setCellValue('F' . $row, $periode);
            $sheet->setCellValue('G' . $row, number_format($data['skor_kehadiran'] ?? 0, 1));
            $sheet->setCellValue('H' . $row, number_format($data['skor_kualitas_kerja'] ?? 0, 1));
            $sheet->setCellValue('I' . $row, number_format($data['skor_inisiatif'] ?? 0, 1));
            $sheet->setCellValue('J' . $row, number_format($data['skor_kedisiplinan'] ?? 0, 1));
            $sheet->setCellValue('K' . $row, number_format($data['skor_khusus'] ?? 0, 1));
            $sheet->setCellValue('L' . $row, number_format($data['skor_total'] ?? 0, 1));
            $sheet->setCellValue('M' . $row, $data['grade'] ?? '-');
            $sheet->setCellValue('N' . $row, $data['predikat'] ?? '-');
            $sheet->setCellValue('O' . $row, $data['status'] ?? 'draft');
            $sheet->setCellValue('P' . $row, $data['catatan_atasan'] ?? '');
            
            $row++;
        }
        
        // Auto size columns
        foreach (range('A', 'P') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Set filename
        $filename = 'Laporan_Performansi_' . date('Ymd_His') . '.xlsx';
        
        // Create writer and output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit();
    }

    /**
     * Print performance report (view only)
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
        $gradeFilter = $this->request->getGet('grade');
        $karyawanIdFilter = $this->request->getGet('karyawan_id');
        
        // Build query
        $db = \Config\Database::connect();
        $builder = $db->table('karyawan_performansi');
        
        $builder->select('karyawan_performansi.*, karyawan.nik, karyawan.nama_lengkap, karyawan.nama_panggilan, karyawan.jabatan, karyawan.departemen')
                ->join('karyawan', 'karyawan.id = karyawan_performansi.karyawan_id')
                ->where('karyawan_performansi.deleted_at', null);
        
        // Apply filters
        $builder->where('karyawan_performansi.periode_tahun', $tahun);
        
        if ($bulan) {
            $builder->where('karyawan_performansi.periode_bulan', $bulan);
        }
        
        if ($gradeFilter) {
            $builder->where('karyawan_performansi.grade', $gradeFilter);
        }
        
        if ($karyawanIdFilter) {
            $builder->where('karyawan_performansi.karyawan_id', $karyawanIdFilter);
        }
        
        $builder->orderBy('karyawan_performansi.skor_total', 'DESC');
        
        $performansiData = $builder->get()->getResultArray();
        
        // Get statistics
        $stats = $this->performansiModel->getSummaryStats($tahun, $bulan);
        
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
        
        // Prepare data for view
        $data = [
            'title' => 'Cetak Laporan Performansi',
            'active' => 'monitoring',
            'performansiData' => $performansiData,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'gradeFilter' => $gradeFilter,
            'karyawanIdFilter' => $karyawanIdFilter,
            'selectedKaryawan' => $selectedKaryawan,
            'stats' => $stats,
            'monthNames' => $monthNames,
            'user' => $session->get()
        ];
        
        // Return the print view (simple HTML for printing)
        return view('direktur/monitoring/performansi_print', $data);
    }

    /**
     * Get top performers for dashboard (AJAX)
     */
    public function getTopPerformers()
    {
        if (!$this->request->isAJAX()) {
            return $this->fail('Only AJAX requests are allowed', 400);
        }
        
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan');
        $limit = $this->request->getGet('limit') ?? 5;
        
        $topPerformers = $this->performansiModel->getTopPerformers($tahun, $bulan, $limit);
        
        return $this->respond([
            'status' => 'success',
            'data' => $topPerformers
        ]);
    }
}