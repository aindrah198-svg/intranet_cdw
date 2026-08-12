<?php

namespace App\Controllers\Direktur\Monitoring;

use App\Controllers\BaseController;
use App\Models\Direktur\AbsensiModel;
use App\Models\KaryawanModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

class Absensi extends BaseController
{
    use ResponseTrait;
    
    protected $absensiModel;
    protected $karyawanModel;
    protected $userModel;
    protected $karyawanId;
    
    /**
     * Initialize controller
     */
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, 
                                 \CodeIgniter\HTTP\ResponseInterface $response, 
                                 \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        $this->absensiModel = new AbsensiModel();
        $this->karyawanModel = new KaryawanModel();
        $this->userModel = new UserModel();
        
        $session = \Config\Services::session();
        $userId = $session->get('user_id');
        
        if ($userId) {
            $user = $this->userModel->find($userId);
            $this->karyawanId = $user['karyawan_id'] ?? null;
        }
    }

    /**
     * Display attendance list for direktur (monitoring only)
     */
    public function index()
    {
        // Cek session
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        
        // Get filter parameters
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');
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
        $builder = $db->table('absensi');
        
        $builder->select('absensi.*, karyawan.nik, karyawan.nama_lengkap, karyawan.nama_panggilan, karyawan.jabatan, karyawan.departemen')
                ->join('karyawan', 'karyawan.id = absensi.karyawan_id')
                ->where('absensi.deleted_at', null);
        
        // Apply filters
        $builder->where('absensi.tanggal >=', $startDate);
        $builder->where('absensi.tanggal <=', $endDate);
        
        if ($statusFilter) {
            $builder->where('absensi.status', $statusFilter);
        }
        
        if ($karyawanIdFilter) {
            $builder->where('absensi.karyawan_id', $karyawanIdFilter);
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
        $totalAbsensi = $totalBuilder->countAllResults();
        
        // Apply pagination and get results
        $builder->limit($perPage, $offset);
        $builder->orderBy('absensi.tanggal', 'DESC');
        $builder->orderBy('absensi.waktu_masuk', 'DESC');
        
        $absensiData = $builder->get()->getResultArray();
        
        // Get statistics
        $statsBuilder = $db->table('absensi')
            ->select("
                COUNT(*) as total_absensi,
                COUNT(DISTINCT absensi.karyawan_id) as total_karyawan,
                SUM(CASE WHEN absensi.status = 'Hadir' THEN 1 ELSE 0 END) as total_hadir,
                SUM(CASE WHEN absensi.status = 'Terlambat' THEN 1 ELSE 0 END) as total_terlambat,
                SUM(CASE WHEN absensi.status = 'Izin' THEN 1 ELSE 0 END) as total_izin,
                SUM(CASE WHEN absensi.status = 'Sakit' THEN 1 ELSE 0 END) as total_sakit,
                SUM(CASE WHEN absensi.status = 'Alpha' THEN 1 ELSE 0 END) as total_alpha,
                SUM(CASE WHEN absensi.terlambat > 0 THEN 1 ELSE 0 END) as total_terlambat_count,
                SUM(absensi.jam_lembur) as total_lembur
            ")
            ->join('karyawan', 'karyawan.id = absensi.karyawan_id')
            ->where('absensi.deleted_at', null)
            ->where('absensi.tanggal >=', $startDate)
            ->where('absensi.tanggal <=', $endDate);
        
        if ($statusFilter) {
            $statsBuilder->where('absensi.status', $statusFilter);
        }
        
        if ($karyawanIdFilter) {
            $statsBuilder->where('absensi.karyawan_id', $karyawanIdFilter);
        }
        
        $stats = $statsBuilder->get()->getRowArray();
        
        // Get karyawan list for dropdown
        $karyawanList = $this->karyawanModel->select('id, nik, nama_lengkap, nama_panggilan, jabatan')
                                      ->where('deleted_at', null)
                                      ->orderBy('nama_lengkap', 'ASC')
                                      ->findAll();
        
        // Calculate pagination
        $totalPages = ceil($totalAbsensi / $perPage);
        
        // Build base URL for pagination links
        $queryParams = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $statusFilter,
            'karyawan_id' => $karyawanIdFilter,
            'search' => $searchQuery
        ];
        
        $baseUrl = base_url('direktur/monitoring/absensi') . '?' . http_build_query(array_filter($queryParams));
        
        // Get user data for navbar
        $userId = $session->get('user_id');
        $userData = $this->userModel->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
            ->select('users.*, karyawan.nama_lengkap as karyawan_nama, karyawan.nama_panggilan')
            ->where('users.id', $userId)
            ->first();
        
        // Prepare data for view
        $data = [
            'title' => 'Monitoring Absensi Karyawan',
            'subtitle' => 'Pantau Kehadiran Karyawan',
            'active' => 'monitoring',
            'absensiData' => $absensiData,
            'karyawanList' => $karyawanList,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'statusFilter' => $statusFilter,
            'karyawanIdFilter' => $karyawanIdFilter,
            'searchQuery' => $searchQuery,
            'currentPage' => $currentPage,
            'perPage' => $perPage,
            'totalAbsensi' => $stats['total_absensi'] ?? 0,
            'totalKaryawan' => $stats['total_karyawan'] ?? 0,
            'totalHadir' => $stats['total_hadir'] ?? 0,
            'totalTerlambat' => $stats['total_terlambat'] ?? 0,
            'totalIzin' => $stats['total_izin'] ?? 0,
            'totalSakit' => $stats['total_sakit'] ?? 0,
            'totalAlpha' => $stats['total_alpha'] ?? 0,
            'totalTerlambatCount' => $stats['total_terlambat_count'] ?? 0,
            'totalLembur' => $stats['total_lembur'] ?? 0,
            'totalPages' => $totalPages,
            'baseUrl' => $baseUrl,
            'queryParams' => $queryParams,
            'user' => $userData
        ];
        
        // Return view utama (template header, sidebar, navbar, footer sudah di-include di view)
        return view('direktur/monitoring/absensi', $data);
    }

    /**
     * Display attendance detail
     */
    public function detail($id)
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        
        // Get attendance data with employee info
        $db = \Config\Database::connect();
        $builder = $db->table('absensi')
            ->select('absensi.*, 
                karyawan.nik, karyawan.nama_lengkap, karyawan.nama_panggilan, karyawan.jabatan, karyawan.departemen,
                creator.username as created_by_username,
                creator_karyawan.nama_lengkap as created_by_name,
                updater.username as updated_by_username,
                updater_karyawan.nama_lengkap as updated_by_name')
            ->join('karyawan', 'karyawan.id = absensi.karyawan_id')
            ->join('users as creator', 'creator.id = absensi.created_by', 'left')
            ->join('karyawan as creator_karyawan', 'creator_karyawan.id = creator.karyawan_id', 'left')
            ->join('users as updater', 'updater.id = absensi.updated_by', 'left')
            ->join('karyawan as updater_karyawan', 'updater_karyawan.id = updater.karyawan_id', 'left')
            ->where('absensi.id', $id)
            ->where('absensi.deleted_at', null);
        
        $absensi = $builder->get()->getRowArray();
        
        if (!$absensi) {
            return redirect()->to(base_url('direktur/monitoring/absensi'))
                ->with('error', 'Data absensi tidak ditemukan');
        }
        
        // Get user data for navbar
        $userId = $session->get('user_id');
        $userData = $this->userModel->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
            ->select('users.*, karyawan.nama_lengkap as karyawan_nama')
            ->where('users.id', $userId)
            ->first();
        
        $data = [
            'title' => 'Detail Absensi',
            'active' => 'monitoring',
            'absensi' => $absensi,
            'user' => $userData
        ];
        
        return view('direktur/monitoring/absensi_detail', $data);
    }

    /**
     * Get attendance summary for chart (AJAX)
     */
    public function getSummary()
    {
        if (!$this->request->isAJAX()) {
            return $this->fail('Only AJAX requests are allowed', 400);
        }
        
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');
        
        $db = \Config\Database::connect();
        $builder = $db->table('absensi')
            ->select("
                status,
                COUNT(*) as count
            ")
            ->where('tanggal >=', $startDate)
            ->where('tanggal <=', $endDate)
            ->where('deleted_at', null)
            ->groupBy('status');
        
        $results = $builder->get()->getResultArray();
        
        $summary = [
            'Hadir' => 0,
            'Terlambat' => 0,
            'Izin' => 0,
            'Sakit' => 0,
            'Alpha' => 0
        ];
        
        foreach ($results as $result) {
            $summary[$result['status']] = $result['count'];
        }
        
        return $this->respond([
            'status' => 'success',
            'data' => $summary
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
        $startDate = (string) ($this->request->getGet('start_date') ?? date('Y-m-01'));
        $endDate = (string) ($this->request->getGet('end_date') ?? date('Y-m-d'));
        $statusFilter = $this->request->getGet('status');
        $karyawanIdFilter = $this->request->getGet('karyawan_id');
        
        // Build query
        $db = \Config\Database::connect();
        $builder = $db->table('absensi');
        
        $builder->select('
            absensi.*,
            karyawan.nik,
            karyawan.nama_lengkap,
            karyawan.nama_panggilan,
            karyawan.jabatan,
            karyawan.departemen
        ')
        ->join('karyawan', 'karyawan.id = absensi.karyawan_id')
        ->where('absensi.deleted_at', null);
        
        // Apply filters
        $builder->where('absensi.tanggal >=', $startDate);
        $builder->where('absensi.tanggal <=', $endDate);
        
        if ($statusFilter) {
            $builder->where('absensi.status', $statusFilter);
        }
        
        if ($karyawanIdFilter) {
            $builder->where('absensi.karyawan_id', $karyawanIdFilter);
        }
        
        $builder->orderBy('absensi.tanggal', 'DESC');
        $builder->orderBy('karyawan.nama_lengkap', 'ASC');
        
        $absensiData = $builder->get()->getResultArray();
        
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
            ->setTitle("Laporan Absensi Karyawan")
            ->setSubject("Export Data Absensi")
            ->setDescription("Laporan absensi karyawan periode $startDate s/d $endDate");
        
        // Set headers
        $headers = [
            'No', 'Tanggal', 'NIK', 'Nama Karyawan', 'Jabatan', 'Departemen', 'Shift',
            'Jam Masuk', 'Jam Pulang', 'Jam Kerja', 'Jam Lembur', 'Terlambat (menit)', 'Status',
            'Lokasi Masuk', 'Lokasi Pulang', 'Keterangan'
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
        
        foreach ($absensiData as $data) {
            $waktu_masuk = !empty($data['waktu_masuk']) ? date('H:i', strtotime($data['waktu_masuk'])) : '-';
            $waktu_pulang = !empty($data['waktu_pulang']) ? date('H:i', strtotime($data['waktu_pulang'])) : '-';
            
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($data['tanggal'])));
            $sheet->setCellValue('C' . $row, $data['nik']);
            $sheet->setCellValue('D' . $row, $data['nama_panggilan'] ?? $data['nama_lengkap']);
            $sheet->setCellValue('E' . $row, $data['jabatan'] ?? '-');
            $sheet->setCellValue('F' . $row, $data['departemen'] ?? '-');
            $sheet->setCellValue('G' . $row, ucfirst($data['shift'] ?? 'siang'));
            $sheet->setCellValue('H' . $row, $waktu_masuk);
            $sheet->setCellValue('I' . $row, $waktu_pulang);
            $sheet->setCellValue('J' . $row, number_format($data['jam_kerja'] ?? 0, 1));
            $sheet->setCellValue('K' . $row, number_format($data['jam_lembur'] ?? 0, 1));
            $sheet->setCellValue('L' . $row, $data['terlambat'] ?? 0);
            $sheet->setCellValue('M' . $row, $data['status'] ?? '-');
            $sheet->setCellValue('N' . $row, substr($data['lokasi_masuk'] ?? '-', 0, 100));
            $sheet->setCellValue('O' . $row, substr($data['lokasi_pulang'] ?? '-', 0, 100));
            $sheet->setCellValue('P' . $row, $data['keterangan'] ?? '');
            
            $row++;
        }
        
        // Auto size columns
        foreach (range('A', 'P') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Set filename
        $filename = 'Laporan_Absensi_' . date('Ymd_His') . '.xlsx';
        
        // Create writer and output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit();
    }

    /**
     * Print attendance report (view only)
     */
    public function print()
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        
        // Get filter parameters
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');
        $statusFilter = $this->request->getGet('status');
        $karyawanIdFilter = $this->request->getGet('karyawan_id');
        
        // Build query
        $db = \Config\Database::connect();
        $builder = $db->table('absensi');
        
        $builder->select('absensi.*, karyawan.nik, karyawan.nama_lengkap, karyawan.nama_panggilan, karyawan.jabatan, karyawan.departemen')
                ->join('karyawan', 'karyawan.id = absensi.karyawan_id')
                ->where('absensi.deleted_at', null);
        
        // Apply filters
        $builder->where('absensi.tanggal >=', $startDate);
        $builder->where('absensi.tanggal <=', $endDate);
        
        if ($statusFilter) {
            $builder->where('absensi.status', $statusFilter);
        }
        
        if ($karyawanIdFilter) {
            $builder->where('absensi.karyawan_id', $karyawanIdFilter);
        }
        
        $builder->orderBy('absensi.tanggal', 'DESC');
        $builder->orderBy('karyawan.nama_lengkap', 'ASC');
        $absensiData = $builder->get()->getResultArray();
        
        // Get statistics
        $statsBuilder = $db->table('absensi')
            ->select("
                COUNT(*) as total_absensi,
                COUNT(DISTINCT absensi.karyawan_id) as total_karyawan,
                SUM(CASE WHEN absensi.status = 'Hadir' THEN 1 ELSE 0 END) as total_hadir,
                SUM(CASE WHEN absensi.status = 'Terlambat' THEN 1 ELSE 0 END) as total_terlambat,
                SUM(CASE WHEN absensi.status = 'Izin' THEN 1 ELSE 0 END) as total_izin,
                SUM(CASE WHEN absensi.status = 'Sakit' THEN 1 ELSE 0 END) as total_sakit,
                SUM(CASE WHEN absensi.status = 'Alpha' THEN 1 ELSE 0 END) as total_alpha
            ")
            ->join('karyawan', 'karyawan.id = absensi.karyawan_id')
            ->where('absensi.deleted_at', null)
            ->where('absensi.tanggal >=', $startDate)
            ->where('absensi.tanggal <=', $endDate);
        
        if ($statusFilter) {
            $statsBuilder->where('absensi.status', $statusFilter);
        }
        
        if ($karyawanIdFilter) {
            $statsBuilder->where('absensi.karyawan_id', $karyawanIdFilter);
        }
        
        $stats = $statsBuilder->get()->getRowArray();
        
        // Get selected karyawan info if filtered
        $selectedKaryawan = null;
        if ($karyawanIdFilter) {
            $selectedKaryawan = $this->karyawanModel->find($karyawanIdFilter);
        }
        
        // Prepare data for view
        $data = [
            'title' => 'Cetak Laporan Absensi',
            'active' => 'monitoring',
            'absensiData' => $absensiData,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'statusFilter' => $statusFilter,
            'karyawanIdFilter' => $karyawanIdFilter,
            'selectedKaryawan' => $selectedKaryawan,
            'totalAbsensi' => $stats['total_absensi'] ?? 0,
            'totalKaryawan' => $stats['total_karyawan'] ?? 0,
            'totalHadir' => $stats['total_hadir'] ?? 0,
            'totalTerlambat' => $stats['total_terlambat'] ?? 0,
            'totalIzin' => $stats['total_izin'] ?? 0,
            'totalSakit' => $stats['total_sakit'] ?? 0,
            'totalAlpha' => $stats['total_alpha'] ?? 0,
            'user' => $session->get()
        ];
        
        // Return the print view (simple HTML for printing)
        return view('direktur/monitoring/absensi_print', $data);
    }

    /**
     * Get attendance statistics for dashboard (AJAX)
     */
    public function getStats()
    {
        if (!$this->request->isAJAX()) {
            return $this->fail('Only AJAX requests are allowed', 400);
        }
        
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        
        return $this->respond([
            'status' => 'success',
            'data' => $this->absensiModel->getSummary($bulan, $tahun)
        ]);
    }

    /**
     * Simpan data absensi baru (Manual Create by Direktur)
     */
    public function simpan()
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $karyawanId = $this->request->getPost('karyawan_id');
        $tanggal    = $this->request->getPost('tanggal');
        $status     = $this->request->getPost('status');
        $shift      = $this->request->getPost('shift') ?? 'siang';
        $waktuMasuk = $this->request->getPost('waktu_masuk');
        $waktuPulang= $this->request->getPost('waktu_pulang');
        $terlambat  = (int)($this->request->getPost('terlambat') ?? 0);
        $jamLembur  = (float)($this->request->getPost('jam_lembur') ?? 0);
        $keterangan = $this->request->getPost('keterangan');

        if (empty($karyawanId) || empty($tanggal) || empty($status)) {
            return redirect()->back()->with('error', 'Karyawan, Tanggal, dan Status wajib diisi.');
        }

        $wMasukFull = !empty($waktuMasuk) ? (strlen($waktuMasuk) == 5 ? $waktuMasuk . ':00' : $waktuMasuk) : null;
        $wPulangFull= !empty($waktuPulang) ? (strlen($waktuPulang) == 5 ? $waktuPulang . ':00' : $waktuPulang) : null;

        $jamKerja = 0;
        if (!empty($wMasukFull) && !empty($wPulangFull)) {
            $tMasuk  = strtotime($tanggal . ' ' . $wMasukFull);
            $tPulang = strtotime($tanggal . ' ' . $wPulangFull);
            if ($tPulang > $tMasuk) {
                $durasi = ($tPulang - $tMasuk) / 3600;
                $jamKerja = round(max(0, $durasi > 4 ? $durasi - 1 : $durasi), 1);
            }
        }

        $insertData = [
            'karyawan_id'  => $karyawanId,
            'tanggal'      => $tanggal,
            'waktu_masuk'  => $wMasukFull,
            'waktu_pulang' => $wPulangFull,
            'status'       => $status,
            'shift'        => $shift,
            'terlambat'    => $terlambat,
            'jam_kerja'    => $jamKerja,
            'jam_lembur'   => $jamLembur,
            'keterangan'   => $keterangan,
            'created_by'   => $session->get('user_id'),
        ];

        $this->absensiModel->insert($insertData);

        return redirect()->to(base_url('direktur/monitoring/absensi'))
            ->with('success', 'Data absensi berhasil ditambahkan.');
    }

    /**
     * Update data absensi
     */
    public function update($id)
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $absensi = $this->absensiModel->find($id);
        if (!$absensi) {
            return redirect()->to(base_url('direktur/monitoring/absensi'))
                ->with('error', 'Data absensi tidak ditemukan.');
        }

        $status     = $this->request->getPost('status') ?? $absensi['status'];
        $shift      = $this->request->getPost('shift') ?? $absensi['shift'];
        $waktuMasuk = $this->request->getPost('waktu_masuk');
        $waktuPulang= $this->request->getPost('waktu_pulang');
        $terlambat  = $this->request->getPost('terlambat') !== null ? (int)$this->request->getPost('terlambat') : $absensi['terlambat'];
        $jamLembur  = $this->request->getPost('jam_lembur') !== null ? (float)$this->request->getPost('jam_lembur') : $absensi['jam_lembur'];
        $keterangan = $this->request->getPost('keterangan') ?? $absensi['keterangan'];

        $wMasukFull = !empty($waktuMasuk) ? (strlen($waktuMasuk) == 5 ? $waktuMasuk . ':00' : $waktuMasuk) : null;
        $wPulangFull= !empty($waktuPulang) ? (strlen($waktuPulang) == 5 ? $waktuPulang . ':00' : $waktuPulang) : null;

        $jamKerja = $absensi['jam_kerja'];
        if (!empty($wMasukFull) && !empty($wPulangFull)) {
            $tMasuk  = strtotime($absensi['tanggal'] . ' ' . $wMasukFull);
            $tPulang = strtotime($absensi['tanggal'] . ' ' . $wPulangFull);
            if ($tPulang > $tMasuk) {
                $durasi = ($tPulang - $tMasuk) / 3600;
                $jamKerja = round(max(0, $durasi > 4 ? $durasi - 1 : $durasi), 1);
            }
        }

        $updateData = [
            'status'       => $status,
            'shift'        => $shift,
            'waktu_masuk'  => $wMasukFull,
            'waktu_pulang' => $wPulangFull,
            'terlambat'    => $terlambat,
            'jam_kerja'    => $jamKerja,
            'jam_lembur'   => $jamLembur,
            'keterangan'   => $keterangan,
            'updated_by'   => $session->get('user_id'),
        ];

        $this->absensiModel->update($id, $updateData);

        return redirect()->to(base_url('direktur/monitoring/absensi'))
            ->with('success', 'Data absensi berhasil diperbarui.');
    }

    /**
     * Hapus data absensi (Soft delete)
     */
    public function delete($id)
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $absensi = $this->absensiModel->find($id);
        if (!$absensi) {
            return redirect()->to(base_url('direktur/monitoring/absensi'))
                ->with('error', 'Data absensi tidak ditemukan.');
        }

        $this->absensiModel->delete($id);

        return redirect()->to(base_url('direktur/monitoring/absensi'))
            ->with('success', 'Data absensi berhasil dihapus.');
    }

    /**
     * Export data absensi ke PDF menggunakan Dompdf
     */
    public function exportPdf()
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $startDate = (string) ($this->request->getGet('start_date') ?? date('Y-m-01'));
        $endDate = (string) ($this->request->getGet('end_date') ?? date('Y-m-d'));
        $statusFilter = $this->request->getGet('status');
        $karyawanIdFilter = $this->request->getGet('karyawan_id');
        $searchQuery = $this->request->getGet('search');

        $db = \Config\Database::connect();
        $builder = $db->table('absensi')
            ->select('absensi.*, karyawan.nik, karyawan.nama_lengkap, karyawan.nama_panggilan, karyawan.jabatan, karyawan.departemen')
            ->join('karyawan', 'karyawan.id = absensi.karyawan_id')
            ->where('absensi.deleted_at', null)
            ->where('absensi.tanggal >=', $startDate)
            ->where('absensi.tanggal <=', $endDate);

        if ($statusFilter) {
            $builder->where('absensi.status', $statusFilter);
        }

        if ($karyawanIdFilter) {
            $builder->where('absensi.karyawan_id', $karyawanIdFilter);
        }

        if ($searchQuery) {
            $builder->groupStart()
                ->like('karyawan.nama_lengkap', $searchQuery)
                ->orLike('karyawan.nik', $searchQuery)
                ->groupEnd();
        }

        $builder->orderBy('absensi.tanggal', 'DESC');
        $builder->orderBy('karyawan.nama_lengkap', 'ASC');
        $absensiData = $builder->get()->getResultArray();

        // Statistics
        $statsBuilder = $db->table('absensi')
            ->select("
                COUNT(*) as total_absensi,
                COUNT(DISTINCT absensi.karyawan_id) as total_karyawan,
                SUM(CASE WHEN absensi.status = 'Hadir' THEN 1 ELSE 0 END) as total_hadir,
                SUM(CASE WHEN absensi.status = 'Terlambat' THEN 1 ELSE 0 END) as total_terlambat,
                SUM(CASE WHEN absensi.status = 'Izin' THEN 1 ELSE 0 END) as total_izin,
                SUM(CASE WHEN absensi.status = 'Sakit' THEN 1 ELSE 0 END) as total_sakit,
                SUM(CASE WHEN absensi.status = 'Alpha' THEN 1 ELSE 0 END) as total_alpha
            ")
            ->join('karyawan', 'karyawan.id = absensi.karyawan_id')
            ->where('absensi.deleted_at', null)
            ->where('absensi.tanggal >=', $startDate)
            ->where('absensi.tanggal <=', $endDate);

        if ($statusFilter) {
            $statsBuilder->where('absensi.status', $statusFilter);
        }

        if ($karyawanIdFilter) {
            $statsBuilder->where('absensi.karyawan_id', $karyawanIdFilter);
        }

        $stats = $statsBuilder->get()->getRowArray();

        $selectedKaryawan = null;
        if ($karyawanIdFilter) {
            $selectedKaryawan = $this->karyawanModel->find($karyawanIdFilter);
        }

        $data = [
            'title' => 'Laporan Monitoring Absensi Karyawan',
            'absensiData' => $absensiData,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'statusFilter' => $statusFilter,
            'selectedKaryawan' => $selectedKaryawan,
            'stats' => $stats
        ];

        $html = view('direktur/monitoring/absensi_pdf', $data);

        if (class_exists('Dompdf\Dompdf')) {
            $options = new \Dompdf\Options();
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'Helvetica');
            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            $filename = 'Laporan_Absensi_' . date('Ymd_His') . '.pdf';
            $dompdf->stream($filename, ['Attachment' => 1]);
            exit();
        } else {
            return $this->response->setHeader('Content-Type', 'text/html')->setBody($html);
        }
    }
}