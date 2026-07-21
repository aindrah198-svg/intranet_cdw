<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AbsensiModel;
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
     * Display attendance list for admin
     */
    public function index()
    {
        // Cek session
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn') || strtolower($session->get('role')) !== 'admin') {
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
        
        $builder->select('absensi.*, karyawan.nik, karyawan.nama_lengkap, karyawan.departemen')
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
                    ->orLike('absensi.lokasi_masuk', $searchQuery)
                    ->orLike('absensi.lokasi_pulang', $searchQuery)
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
                SUM(CASE WHEN absensi.terlambat > 0 THEN 1 ELSE 0 END) as total_terlambat
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
        $karyawanList = $this->karyawanModel->select('id, nik, nama_lengkap')
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
        
        $baseUrl = base_url('admin/absensi') . '?' . http_build_query(array_filter($queryParams));
        
        // Get user data for navbar
        $userId = $session->get('user_id');
        $userData = $this->userModel->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
            ->select('users.*, karyawan.nama_lengkap')
            ->where('users.id', $userId)
            ->first();
        
        // Prepare data for view
        $data = [
            'title' => 'Daftar Absensi Karyawan',
            'active' => 'absensi',
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
            'totalPages' => $totalPages,
            'baseUrl' => $baseUrl,
            'queryParams' => $queryParams,
            'user' => $userData
        ];
        
        // Return view dengan include template
        return view('admin/templates/header', $data)
             . view('admin/templates/sidebar', $data)
             . view('admin/templates/navbar', $data)
             . view('admin/absensi/index', $data)
             . view('admin/templates/footer', $data);
    }

    /**
     * Display create attendance form
     */
    public function create()
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn') || strtolower($session->get('role')) !== 'admin') {
            return redirect()->to(base_url('login'));
        }
        
        // Get active karyawan list for dropdown
        $karyawanList = $this->karyawanModel->select('id, nik, nama_lengkap')
                                      ->where('deleted_at', null)
                                      ->orderBy('nama_lengkap', 'ASC')
                                      ->findAll();
        
        // Get user data for navbar
        $userId = $session->get('user_id');
        $userData = $this->userModel->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
            ->select('users.*, karyawan.nama_lengkap')
            ->where('users.id', $userId)
            ->first();
        
        $data = [
            'title' => 'Tambah Absensi Manual',
            'active' => 'absensi',
            'karyawanList' => $karyawanList,
            'user' => $userData
        ];
        
        return view('admin/templates/header', $data)
             . view('admin/templates/sidebar', $data)
             . view('admin/templates/navbar', $data)
             . view('admin/absensi/create', $data)
             . view('admin/templates/footer', $data);
    }

    /**
     * Store new attendance (Manual input by admin)
     */
    public function store()
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn') || strtolower($session->get('role')) !== 'admin') {
            return redirect()->to(base_url('login'));
        }
        
        // Get current user ID for audit trail
        $userId = $session->get('user_id');
        
        // Get form data
        $data = $this->request->getPost();
        
        // Basic validation rules
        $validation = \Config\Services::validation();
        $validation->setRules([
            'karyawan_id' => 'required|integer',
            'tanggal' => 'required|valid_date',
            'status' => 'required|in_list[Hadir,Izin,Sakit,Cuti,Alpha]',
            'shift' => 'required|in_list[pagi,siang,sore,malam]'
        ]);
        
        // Additional validation for time fields
        if (!empty($data['waktu_masuk'])) {
            if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $data['waktu_masuk'])) {
                $validation->setError('waktu_masuk', 'Format waktu masuk harus HH:MM (contoh: 08:00)');
            }
        }
        
        if (!empty($data['waktu_pulang'])) {
            if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $data['waktu_pulang'])) {
                $validation->setError('waktu_pulang', 'Format waktu pulang harus HH:MM (contoh: 17:00)');
            }
        }
        
        if (!$validation->run($data)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $validation->getErrors());
        }
        
        // Check if attendance already exists for this employee on this date
        $existing = $this->absensiModel->getExistingAttendance($data['karyawan_id'], $data['tanggal']);
        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Absensi untuk karyawan ini pada tanggal tersebut sudah ada');
        }
        
        // Jika status bukan "Hadir", kosongkan waktu masuk/pulang
        if ($data['status'] !== 'Hadir') {
            $data['waktu_masuk'] = null;
            $data['waktu_pulang'] = null;
            $data['jam_kerja'] = 0;
            $data['terlambat'] = 0;
            $data['jam_lembur'] = 0;
            $data['lokasi_masuk'] = null;
            $data['lokasi_pulang'] = null;
        } else {
            // Validate that waktu_masuk is required for "Hadir" status
            if (empty($data['waktu_masuk'])) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Waktu masuk harus diisi untuk status Hadir');
            }
            
            // Format waktu dengan detik jika belum ada
            if (!empty($data['waktu_masuk']) && strlen($data['waktu_masuk']) === 5) {
                $data['waktu_masuk'] .= ':00';
            }
            if (!empty($data['waktu_pulang']) && strlen($data['waktu_pulang']) === 5) {
                $data['waktu_pulang'] .= ':00';
            }
            
            // Calculate working hours
            $shift = $data['shift'];
            
            // Calculate jam kerja jika ada waktu masuk dan pulang
            if (!empty($data['waktu_masuk']) && !empty($data['waktu_pulang'])) {
                $data['jam_kerja'] = $this->absensiModel->calculateJamKerja($data['waktu_masuk'], $data['waktu_pulang'], $shift);
                $data['jam_lembur'] = $this->absensiModel->calculateLembur($data['waktu_pulang'], $this->absensiModel->getJamSelesaiByShift($shift));
            } else if (!empty($data['waktu_masuk'])) {
                // Only check-in time provided
                $data['jam_kerja'] = 0;
                $data['jam_lembur'] = 0;
            }
            
            // Calculate late time
            $data['terlambat'] = $this->absensiModel->checkTerlambat($data['waktu_masuk'], $shift);
        }
        
        // Set shift times
        $data['jam_shift_mulai'] = $this->absensiModel->getJamMulaiByShift($data['shift']);
        $data['jam_shift_selesai'] = $this->absensiModel->getJamSelesaiByShift($data['shift']);
        
        // Set default values
        $data['jam_kerja'] = $data['jam_kerja'] ?? 0;
        $data['terlambat'] = $data['terlambat'] ?? 0;
        $data['jam_lembur'] = $data['jam_lembur'] ?? 0;
        
        // Clean empty strings
        $data['lokasi_masuk'] = empty($data['lokasi_masuk']) ? null : $data['lokasi_masuk'];
        $data['lokasi_pulang'] = empty($data['lokasi_pulang']) ? null : $data['lokasi_pulang'];
        $data['keterangan'] = empty($data['keterangan']) ? null : $data['keterangan'];
        
        // AUDIT TRAIL
        $data['created_by'] = $userId;
        $data['updated_by'] = $userId;
        
        try {
            if ($this->absensiModel->save($data)) {
                return redirect()->to(base_url('admin/absensi'))
                    ->with('success', 'Data absensi berhasil ditambahkan');
            } else {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Gagal menyimpan data absensi');
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Display attendance detail
     */
    public function detail($id)
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn') || strtolower($session->get('role')) !== 'admin') {
            return redirect()->to(base_url('login'));
        }
        
        // Get attendance data with employee AND audit info
        $db = \Config\Database::connect();
        $builder = $db->table('absensi')
            ->select('absensi.*, 
                karyawan.nik, karyawan.nama_lengkap, karyawan.jabatan, karyawan.departemen,
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
            return redirect()->to(base_url('admin/absensi'))
                ->with('error', 'Data absensi tidak ditemukan');
        }
        
        // Get user data for navbar
        $userId = $session->get('user_id');
        $userData = $this->userModel->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
            ->select('users.*, karyawan.nama_lengkap')
            ->where('users.id', $userId)
            ->first();
        
        $data = [
            'title' => 'Detail Absensi',
            'active' => 'absensi',
            'absensi' => $absensi,
            'user' => $userData
        ];
        
        return view('admin/templates/header', $data)
             . view('admin/templates/sidebar', $data)
             . view('admin/templates/navbar', $data)
             . view('admin/absensi/detail', $data)
             . view('admin/templates/footer', $data);
    }

    /**
     * Display edit attendance form
     */
    public function edit($id)
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn') || strtolower($session->get('role')) !== 'admin') {
            return redirect()->to(base_url('login'));
        }
        
        // Get attendance data with audit info
        $db = \Config\Database::connect();
        $builder = $db->table('absensi')
            ->select('absensi.*, 
                karyawan.nik, karyawan.nama_lengkap, karyawan.jabatan, karyawan.departemen,
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
            return redirect()->to(base_url('admin/absensi'))
                ->with('error', 'Data absensi tidak ditemukan');
        }
        
        // Get active karyawan list for dropdown
        $karyawanList = $this->karyawanModel->select('id, nik, nama_lengkap')
                                      ->where('deleted_at', null)
                                      ->orderBy('nama_lengkap', 'ASC')
                                      ->findAll();
        
        // Get user data for navbar
        $userId = $session->get('user_id');
        $userData = $this->userModel->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
            ->select('users.*, karyawan.nama_lengkap')
            ->where('users.id', $userId)
            ->first();
        
        $data = [
            'title' => 'Edit Absensi',
            'active' => 'absensi',
            'absensi' => $absensi,
            'karyawanList' => $karyawanList,
            'user' => $userData
        ];
        
        return view('admin/templates/header', $data)
             . view('admin/templates/sidebar', $data)
             . view('admin/templates/navbar', $data)
             . view('admin/absensi/edit', $data)
             . view('admin/templates/footer', $data);
    }

    /**
     * Update attendance data
     */
    public function update($id)
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn') || strtolower($session->get('role')) !== 'admin') {
            return redirect()->to(base_url('login'));
        }
        
        // Get current user ID for audit trail
        $userId = $session->get('user_id');
        
        // Check if attendance exists
        $absensi = $this->absensiModel->find($id);
        if (!$absensi) {
            return redirect()->to(base_url('admin/absensi'))
                ->with('error', 'Data absensi tidak ditemukan');
        }
        
        // Get form data
        $data = $this->request->getPost();
        
        // Basic validation rules
        $validation = \Config\Services::validation();
        $validation->setRules([
            'karyawan_id' => 'required|integer',
            'tanggal' => 'required|valid_date',
            'status' => 'required|in_list[Hadir,Izin,Sakit,Cuti,Alpha]',
            'shift' => 'required|in_list[pagi,siang,sore,malam]'
        ]);
        
        // Additional validation for time fields
        if (!empty($data['waktu_masuk'])) {
            if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $data['waktu_masuk'])) {
                $validation->setError('waktu_masuk', 'Format waktu masuk harus HH:MM (contoh: 08:00)');
            }
        }
        
        if (!empty($data['waktu_pulang'])) {
            if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $data['waktu_pulang'])) {
                $validation->setError('waktu_pulang', 'Format waktu pulang harus HH:MM (contoh: 17:00)');
            }
        }
        
        if (!$validation->run($data)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $validation->getErrors());
        }
        
        // Check if attendance already exists for this employee on this date (exclude current)
        if ($absensi['karyawan_id'] != $data['karyawan_id'] || $absensi['tanggal'] != $data['tanggal']) {
            $existing = $this->absensiModel->getExistingAttendance($data['karyawan_id'], $data['tanggal'], $id);
            if ($existing) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Absensi untuk karyawan ini pada tanggal tersebut sudah ada');
            }
        }
        
        // Jika status bukan "Hadir", kosongkan waktu masuk/pulang
        if ($data['status'] !== 'Hadir') {
            $data['waktu_masuk'] = null;
            $data['waktu_pulang'] = null;
            $data['jam_kerja'] = 0;
            $data['terlambat'] = 0;
            $data['jam_lembur'] = 0;
            $data['lokasi_masuk'] = null;
            $data['lokasi_pulang'] = null;
        } else {
            // Validate that waktu_masuk is required for "Hadir" status
            if (empty($data['waktu_masuk'])) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Waktu masuk harus diisi untuk status Hadir');
            }
            
            // Format waktu dengan detik jika belum ada
            if (!empty($data['waktu_masuk']) && strlen($data['waktu_masuk']) === 5) {
                $data['waktu_masuk'] .= ':00';
            }
            if (!empty($data['waktu_pulang']) && strlen($data['waktu_pulang']) === 5) {
                $data['waktu_pulang'] .= ':00';
            }
            
            // Calculate working hours
            $shift = $data['shift'];
            
            // Calculate jam kerja jika ada waktu masuk dan pulang
            if (!empty($data['waktu_masuk']) && !empty($data['waktu_pulang'])) {
                $data['jam_kerja'] = $this->absensiModel->calculateJamKerja($data['waktu_masuk'], $data['waktu_pulang'], $shift);
                $data['jam_lembur'] = $this->absensiModel->calculateLembur($data['waktu_pulang'], $this->absensiModel->getJamSelesaiByShift($shift));
            } else if (!empty($data['waktu_masuk'])) {
                // Only check-in time provided
                $data['jam_kerja'] = 0;
                $data['jam_lembur'] = 0;
            }
            
            // Calculate late time
            $data['terlambat'] = $this->absensiModel->checkTerlambat($data['waktu_masuk'], $shift);
        }
        
        // Set shift times
        $data['jam_shift_mulai'] = $this->absensiModel->getJamMulaiByShift($data['shift']);
        $data['jam_shift_selesai'] = $this->absensiModel->getJamSelesaiByShift($data['shift']);
        
        // Set default values
        $data['jam_kerja'] = $data['jam_kerja'] ?? 0;
        $data['terlambat'] = $data['terlambat'] ?? 0;
        $data['jam_lembur'] = $data['jam_lembur'] ?? 0;
        
        // Clean empty strings
        $data['lokasi_masuk'] = empty($data['lokasi_masuk']) ? null : $data['lokasi_masuk'];
        $data['lokasi_pulang'] = empty($data['lokasi_pulang']) ? null : $data['lokasi_pulang'];
        $data['keterangan'] = empty($data['keterangan']) ? null : $data['keterangan'];
        
        // AUDIT TRAIL
        // Keep original created_by, only update updated_by
        $data['created_by'] = $absensi['created_by']; // Preserve original creator
        $data['updated_by'] = $userId; // Set current user as updater
        
        try {
            if ($this->absensiModel->update($id, $data)) {
                return redirect()->to(base_url('admin/absensi/detail/' . $id))
                    ->with('success', 'Data absensi berhasil diperbarui');
            } else {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Gagal memperbarui data absensi');
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Delete attendance data (soft delete)
     */
    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->fail('Only AJAX requests are allowed', 400);
        }
        
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn') || strtolower($session->get('role')) !== 'admin') {
            return $this->respond([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }
        
        try {
            // Check if attendance exists
            $absensi = $this->absensiModel->find($id);
            if (!$absensi) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Data absensi tidak ditemukan'
                ], 404);
            }
            
            // Soft delete (update deleted_at)
            $data = [
                'deleted_at' => date('Y-m-d H:i:s'),
                'updated_by' => $session->get('user_id')
            ];
            
            if ($this->absensiModel->update($id, $data)) {
                return $this->respond([
                    'status' => 'success',
                    'message' => 'Data absensi berhasil dihapus'
                ]);
            } else {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Gagal menghapus data absensi'
                ], 500);
            }
            
        } catch (\Exception $e) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Manual checkout for specific attendance (by admin)
     */
    public function manualCheckout($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->fail('Only AJAX requests are allowed', 400);
        }
        
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn') || strtolower($session->get('role')) !== 'admin') {
            return $this->respond([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }
        
        $userId = $session->get('user_id');
        $requestData = $this->request->getJSON();
        
        try {
            // Get attendance data
            $attendance = $this->absensiModel->find($id);
            if (!$attendance) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Data absensi tidak ditemukan'
                ], 404);
            }
            
            if (!empty($attendance['waktu_pulang'])) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Karyawan sudah melakukan checkout'
                ], 400);
            }
            
            $waktu_pulang = $requestData->waktu_pulang ?? date('H:i:s');
            
            // Validate time format
            if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', substr($waktu_pulang, 0, 5))) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Format waktu tidak valid. Gunakan format HH:MM'
                ], 400);
            }
            
            // Format waktu dengan detik jika belum ada
            if (strlen($waktu_pulang) === 5) {
                $waktu_pulang .= ':00';
            }
            
            // Calculate working hours and overtime
            $updateData = [
                'waktu_pulang' => $waktu_pulang,
                'keterangan' => $requestData->keterangan ?? ($attendance['keterangan'] ?? 'Manual checkout by admin'),
                'updated_by' => $userId
            ];
            
            if (!empty($attendance['waktu_masuk'])) {
                $shift = $attendance['shift'] ?? 'siang';
                
                $jam_kerja = $this->absensiModel->calculateJamKerja($attendance['waktu_masuk'], $waktu_pulang);
                $updateData['jam_kerja'] = $jam_kerja;
                
                $jam_lembur = $this->absensiModel->calculateLembur($waktu_pulang, $shift);
                $updateData['jam_lembur'] = $jam_lembur;
            }
            
            if ($this->absensiModel->update($id, $updateData)) {
                return $this->respond([
                    'status' => 'success',
                    'message' => 'Checkout manual berhasil',
                    'data' => array_merge($attendance, $updateData)
                ]);
            } else {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Gagal melakukan checkout manual'
                ], 500);
            }
            
        } catch (\Exception $e) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export data to Excel
     */
    public function exportExcel()
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn') || strtolower($session->get('role')) !== 'admin') {
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
        
        $builder->select('
            absensi.*,
            karyawan.nik,
            karyawan.nama_lengkap,
            karyawan.jabatan,
            karyawan.departemen,
            karyawan.email,
            creator.username as dibuat_oleh,
            creator_karyawan.nama_lengkap as nama_dibuat_oleh,
            updater.username as diupdate_oleh,
            updater_karyawan.nama_lengkap as nama_diupdate_oleh
        ')
        ->join('karyawan', 'karyawan.id = absensi.karyawan_id')
        ->join('users as creator', 'creator.id = absensi.created_by', 'left')
        ->join('karyawan as creator_karyawan', 'creator_karyawan.id = creator.karyawan_id', 'left')
        ->join('users as updater', 'updater.id = absensi.updated_by', 'left')
        ->join('karyawan as updater_karyawan', 'updater_karyawan.id = updater.karyawan_id', 'left')
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
            ->setDescription("Laporan absensi karyawan periode $startDate s/d $endDate")
            ->setKeywords("absensi karyawan laporan")
            ->setCategory("Laporan");
        
        // Set headers
        $headers = [
            'No', 'Tanggal', 'NIK', 'Nama Karyawan', 'Departemen', 'Jabatan', 'Shift',
            'Waktu Masuk', 'Waktu Pulang', 'Jam Kerja', 'Jam Lembur', 'Terlambat (menit)', 'Status',
            'Lokasi Masuk', 'Lokasi Pulang', 'Keterangan', 'Device Masuk', 'Device Pulang',
            'Dibuat Oleh', 'Diupdate Oleh', 'Tanggal Dibuat', 'Tanggal Diupdate'
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
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ];
        
        $sheet->getStyle('A1:V1')->applyFromArray($headerStyle);
        
        // Write data
        $row = 2;
        $no = 1;
        
        foreach ($absensiData as $data) {
            // Format waktu
            $waktu_masuk = !empty($data['waktu_masuk']) ? date('H:i', strtotime($data['waktu_masuk'])) : '';
            $waktu_pulang = !empty($data['waktu_pulang']) ? date('H:i', strtotime($data['waktu_pulang'])) : '';
            
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($data['tanggal'])));
            $sheet->setCellValue('C' . $row, $data['nik']);
            $sheet->setCellValue('D' . $row, $data['nama_lengkap']);
            $sheet->setCellValue('E' . $row, $data['departemen']);
            $sheet->setCellValue('F' . $row, $data['jabatan']);
            $sheet->setCellValue('G' . $row, ucfirst($data['shift']));
            $sheet->setCellValue('H' . $row, $waktu_masuk);
            $sheet->setCellValue('I' . $row, $waktu_pulang);
            $sheet->setCellValue('J' . $row, number_format($data['jam_kerja'], 1));
            $sheet->setCellValue('K' . $row, number_format($data['jam_lembur'], 1));
            $sheet->setCellValue('L' . $row, $data['terlambat']);
            $sheet->setCellValue('M' . $row, $data['status']);
            $sheet->setCellValue('N' . $row, $data['lokasi_masuk']);
            $sheet->setCellValue('O' . $row, $data['lokasi_pulang']);
            $sheet->setCellValue('P' . $row, $data['keterangan']);
            $sheet->setCellValue('Q' . $row, $data['device_masuk']);
            $sheet->setCellValue('R' . $row, $data['device_pulang']);
            $sheet->setCellValue('S' . $row, $data['nama_dibuat_oleh'] ?? $data['dibuat_oleh'] ?? '-');
            $sheet->setCellValue('T' . $row, $data['nama_diupdate_oleh'] ?? $data['diupdate_oleh'] ?? '-');
            $sheet->setCellValue('U' . $row, !empty($data['created_at']) ? date('d/m/Y H:i', strtotime($data['created_at'])) : '-');
            $sheet->setCellValue('V' . $row, !empty($data['updated_at']) ? date('d/m/Y H:i', strtotime($data['updated_at'])) : '-');
            
            $row++;
        }
        
        // Auto size columns
        foreach (range('A', 'V') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Add borders to data
        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ];
        
        $lastRow = $row - 1;
        if ($lastRow >= 2) {
            $sheet->getStyle('A2:V' . $lastRow)->applyFromArray($dataStyle);
        }
        
        // Set alignment for numeric columns
        $numericStyle = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT
            ]
        ];
        
        $sheet->getStyle('J2:K' . $lastRow)->applyFromArray($numericStyle);
        $sheet->getStyle('L2:L' . $lastRow)->applyFromArray($numericStyle);
        
        // Center alignment for certain columns
        $centerStyle = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]
        ];
        
        $sheet->getStyle('A2:A' . $lastRow)->applyFromArray($centerStyle);
        $sheet->getStyle('G2:I' . $lastRow)->applyFromArray($centerStyle);
        $sheet->getStyle('M2:M' . $lastRow)->applyFromArray($centerStyle);
        
        // Add summary info
        $summaryRow = $row + 2;
        $sheet->setCellValue('A' . $summaryRow, 'SUMMARY');
        $sheet->getStyle('A' . $summaryRow . ':B' . $summaryRow)->getFont()->setBold(true);
        
        $summaryRow++;
        $sheet->setCellValue('A' . $summaryRow, 'Periode:');
        $sheet->setCellValue('B' . $summaryRow, date('d/m/Y', strtotime($startDate)) . ' s/d ' . date('d/m/Y', strtotime($endDate)));
        
        $summaryRow++;
        $sheet->setCellValue('A' . $summaryRow, 'Total Data:');
        $sheet->setCellValue('B' . $summaryRow, count($absensiData));
        
        $summaryRow++;
        $sheet->setCellValue('A' . $summaryRow, 'Tanggal Export:');
        $sheet->setCellValue('B' . $summaryRow, date('d/m/Y H:i:s'));
        
        // Set filename
        $filename = 'Laporan_Absensi_' . date('Ymd_His') . '.xlsx';
        
        // Create writer and output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Expires: 0');
        header('Pragma: public');
        
        $writer->save('php://output');
        exit();
    }

    /**
     * Export data to PDF
     */
    public function exportPdf()
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn') || strtolower($session->get('role')) !== 'admin') {
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
        
        $builder->select('absensi.*, karyawan.nik, karyawan.nama_lengkap, karyawan.departemen')
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
        
        // Get attendance data
        $builder->orderBy('absensi.tanggal', 'DESC');
        $builder->orderBy('karyawan.nama_lengkap', 'ASC');
        $absensiData = $builder->get()->getResultArray();
        
        // Get statistics
        $statsBuilder = $db->table('absensi')
            ->select("
                COUNT(*) as total_absensi,
                COUNT(DISTINCT absensi.karyawan_id) as total_karyawan,
                SUM(CASE WHEN absensi.status = 'Hadir' THEN 1 ELSE 0 END) as total_hadir,
                SUM(CASE WHEN absensi.terlambat > 0 THEN 1 ELSE 0 END) as total_terlambat,
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
        
        // Prepare data untuk view
        $data = [
            'absensiData' => $absensiData,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'statusFilter' => $statusFilter,
            'karyawanIdFilter' => $karyawanIdFilter,
            'totalAbsensi' => $stats['total_absensi'] ?? 0,
            'totalKaryawan' => $stats['total_karyawan'] ?? 0,
            'totalHadir' => $stats['total_hadir'] ?? 0,
            'totalTerlambat' => $stats['total_terlambat'] ?? 0,
            'totalLembur' => $stats['total_lembur'] ?? 0
        ];
        
        // Cek jika DOMPDF terinstall
        if (class_exists('Dompdf\Dompdf')) {
            try {
                $options = new \Dompdf\Options();
                $options->set('isHtml5ParserEnabled', true);
                $options->set('isRemoteEnabled', true);
                $options->set('defaultFont', 'Arial');
                
                $dompdf = new \Dompdf\Dompdf($options);
                
                // Load HTML dari view
                $html = view('admin/absensi/export_pdf', $data);
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                
                // Output PDF
                $dompdf->stream("laporan_absensi_" . date('Ymd_His') . ".pdf", [
                    "Attachment" => true
                ]);
                
                exit();
            } catch (\Exception $e) {
                // Jika error, fallback ke print view
                return redirect()->to(base_url('admin/absensi/export/print?' . http_build_query([
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status' => $statusFilter,
                    'karyawan_id' => $karyawanIdFilter
                ])))->with('error', 'Error PDF: ' . $e->getMessage());
            }
        } else {
            // DOMPDF tidak terinstall, redirect ke print view
            return redirect()->to(base_url('admin/absensi/export/print?' . http_build_query([
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $statusFilter,
                'karyawan_id' => $karyawanIdFilter
            ])))->with('error', 'Fitur PDF export membutuhkan instalasi DOMPDF. Silakan install via Composer: <code>composer require dompdf/dompdf</code>');
        }
    }

    /**
     * Print attendance report (view only)
     */
    public function print()
    {
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn') || strtolower($session->get('role')) !== 'admin') {
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
        
        $builder->select('absensi.*, karyawan.nik, karyawan.nama_lengkap, karyawan.departemen')
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
        
        // Get attendance data
        $builder->orderBy('absensi.tanggal', 'DESC');
        $builder->orderBy('karyawan.nama_lengkap', 'ASC');
        $absensiData = $builder->get()->getResultArray();
        
        // Get statistics
        $statsBuilder = $db->table('absensi')
            ->select("
                COUNT(*) as total_absensi,
                COUNT(DISTINCT absensi.karyawan_id) as total_karyawan,
                SUM(CASE WHEN absensi.status = 'Hadir' THEN 1 ELSE 0 END) as total_hadir,
                SUM(CASE WHEN absensi.terlambat > 0 THEN 1 ELSE 0 END) as total_terlambat,
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
        
        // Get selected karyawan info if filtered
        $selectedKaryawan = null;
        if ($karyawanIdFilter) {
            $selectedKaryawan = $this->karyawanModel->find($karyawanIdFilter);
        }
        
        // Get summary by status
        $statusBuilder = $db->table('absensi')
            ->select('status, COUNT(*) as count')
            ->join('karyawan', 'karyawan.id = absensi.karyawan_id')
            ->where('absensi.deleted_at', null)
            ->where('absensi.tanggal >=', $startDate)
            ->where('absensi.tanggal <=', $endDate)
            ->groupBy('absensi.status');
        
        if ($karyawanIdFilter) {
            $statusBuilder->where('absensi.karyawan_id', $karyawanIdFilter);
        }
        
        $statusResults = $statusBuilder->get()->getResultArray();
        $summaryByStatus = [];
        foreach ($statusResults as $result) {
            $summaryByStatus[$result['status']] = $result['count'];
        }
        
        // Prepare query params for export links
        $queryParams = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $statusFilter,
            'karyawan_id' => $karyawanIdFilter
        ];
        
        // Prepare data for view
        $data = [
            'title' => 'Cetak Laporan Absensi',
            'active' => 'absensi',
            'absensiData' => $absensiData,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'statusFilter' => $statusFilter,
            'karyawanIdFilter' => $karyawanIdFilter,
            'selectedKaryawan' => $selectedKaryawan,
            'queryParams' => $queryParams,
            'totalAbsensi' => $stats['total_absensi'] ?? 0,
            'totalKaryawan' => $stats['total_karyawan'] ?? 0,
            'totalHadir' => $stats['total_hadir'] ?? 0,
            'totalTerlambat' => $stats['total_terlambat'] ?? 0,
            'totalLembur' => $stats['total_lembur'] ?? 0,
            'summaryByStatus' => $summaryByStatus
        ];
        
        // Return the print view
        return view('admin/absensi/print', $data);
    }

    /**
     * Display absensi page for admin
     */
    public function myAttendance()
    {
        $session = \Config\Services::session();
        
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        
        $userRole = strtolower($session->get('role') ?? '');
        if ($userRole !== 'admin') {
            return redirect()->to(base_url($userRole))->with('info', 'Anda dialihkan ke dashboard sesuai role.');
        }
        
        $today = date('Y-m-d');
        $absensiToday = null;
        
        if ($this->karyawanId) {
            $absensiToday = $this->absensiModel->getTodayAttendanceByKaryawan($this->karyawanId);
        }
        
        $startDate = date('Y-m-d', strtotime('-7 days'));
        $endDate = date('Y-m-d');
        $absensiHistory = [];
        
        if ($this->karyawanId) {
            $absensiHistory = $this->absensiModel->getAbsensiHistory($this->karyawanId, $startDate, $endDate, 7);
        }
        
        $userId = $session->get('user_id');
        $userData = $this->userModel->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
            ->select('users.*, karyawan.*')
            ->where('users.id', $userId)
            ->first();
        
        $data = [
            'title' => 'Absensi Admin',
            'subtitle' => 'Sistem Absensi Masuk & Pulang',
            'active' => 'absensi',
            'user' => $userData,
            'absensiToday' => $absensiToday,
            'absensiHistory' => $absensiHistory,
            'karyawan_id' => $this->karyawanId,
            'stats' => $this->getMonthlyStats()
        ];
        
        return view('admin/templates/header', $data)
             . view('admin/templates/sidebar', $data)
             . view('admin/absensi/my_attendance', $data)
             . view('admin/templates/footer', $data);
    }

    /**
     * Process check-in for admin with accurate location
     */
    public function checkin()
    {
        log_message('debug', 'ADMIN ABSENSI CHECKIN: Processing check-in with location');
        
        if (!$this->request->isAJAX()) {
            return $this->fail('Only AJAX requests are allowed', 400);
        }
        
        // Get session for audit trail
        $session = \Config\Services::session();
        $userId = $session->get('user_id');
        
        if (!$this->karyawanId) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Data karyawan tidak ditemukan'
            ], 400);
        }
        
        date_default_timezone_set('Asia/Jakarta');
        $today = date('Y-m-d');
        
        $existing = $this->absensiModel->hasAttendanceToday($this->karyawanId);
        
        if ($existing && !empty($existing['waktu_masuk'])) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Anda sudah melakukan absensi masuk hari ini'
            ], 400);
        }
        
        $requestData = $this->request->getJSON();
        $shift = $requestData->shift ?? 'siang';
        
        $valid_shifts = ['pagi', 'siang', 'sore', 'malam'];
        if (!in_array($shift, $valid_shifts)) {
            $shift = 'siang';
        }
        
        $waktu_masuk = date('H:i:s');
        
        // Process location data
        $latitude_masuk = $requestData->latitude_masuk ?? null;
        $longitude_masuk = $requestData->longitude_masuk ?? null;
        $accuracy = $requestData->accuracy ?? null;
        
        // Validate and normalize coordinates
        $validCoords = $this->validateAndNormalizeCoordinates($latitude_masuk, $longitude_masuk);
        
        if ($validCoords) {
            $latitude_masuk = $validCoords['latitude'];
            $longitude_masuk = $validCoords['longitude'];
            $lokasi_masuk = $this->getLocationNameFromCoordinates($latitude_masuk, $longitude_masuk);
            
            // Validate location is within Indonesia
            if (!$this->isLocationInIndonesia($latitude_masuk, $longitude_masuk)) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Lokasi berada di luar Indonesia'
                ], 400);
            }
            
            // Check if location is valid (not 0,0)
            if ($this->isNullIsland($latitude_masuk, $longitude_masuk)) {
                $lokasi_masuk = 'Lokasi tidak valid (null island)';
                $latitude_masuk = null;
                $longitude_masuk = null;
            }
            
            // Store accuracy information
            $location_metadata = [
                'accuracy' => $accuracy,
                'timestamp' => time(),
                'source' => 'gps'
            ];
            
        } else {
            $latitude_masuk = null;
            $longitude_masuk = null;
            $lokasi_masuk = 'Lokasi tidak terdeteksi';
            $location_metadata = [
                'source' => 'unknown',
                'timestamp' => time()
            ];
        }
        
        // Calculate lateness
        $terlambat_info = $this->hitungTerlambatBerdasarkanShift($waktu_masuk, $shift);
        
        // Get shift hours
        $jam_shift_mulai = $this->absensiModel->getJamMulaiByShift($shift);
        $jam_shift_selesai = $this->absensiModel->getJamSelesaiByShift($shift);
        
        // Prepare data with accurate location
        $data = [
            'karyawan_id' => $this->karyawanId,
            'shift' => $shift,
            'jam_shift_mulai' => $jam_shift_mulai,
            'jam_shift_selesai' => $jam_shift_selesai,
            'tanggal' => $today,
            'waktu_masuk' => $waktu_masuk,
            'lokasi_masuk' => $lokasi_masuk,
            'latitude_masuk' => $latitude_masuk,
            'longitude_masuk' => $longitude_masuk,
            'location_metadata_masuk' => json_encode($location_metadata),
            'status' => 'Hadir',
            'device_masuk' => $this->request->getUserAgent()->getAgentString(),
            'ip_address_masuk' => $this->request->getIPAddress(),
            'terlambat' => $terlambat_info['terlambat'],
            'keterangan' => $terlambat_info['keterangan'],
            // AUDIT TRAIL
            'created_by' => $userId,
            'updated_by' => $userId
        ];
        
        if ($existing && !empty($existing['id'])) {
            $data['id'] = $existing['id'];
        }
        
        log_message('debug', 'ADMIN ABSENSI CHECKIN: Saving attendance with location: ' . print_r($data, true));
        
        try {
            if ($this->absensiModel->save($data)) {
                $attendanceId = !empty($existing['id']) ? $existing['id'] : $this->absensiModel->getInsertID();
                
                // Calculate distance from office (optional)
                $distanceFromOffice = null;
                if ($latitude_masuk && $longitude_masuk) {
                    $distanceFromOffice = $this->calculateDistanceFromOffice($latitude_masuk, $longitude_masuk);
                }
                
                $waktu_display_wib = date('H:i', strtotime($waktu_masuk)) . ' WIB';
                $nama_shift = $this->formatNamaShift($shift);
                
                return $this->respond([
                    'status' => 'success',
                    'message' => 'Absensi masuk berhasil',
                    'data' => array_merge($data, ['id' => $attendanceId]),
                    'waktu_display' => $waktu_display_wib,
                    'terlambat_display' => $this->formatMenitKeJam($terlambat_info['terlambat']),
                    'keterangan_terlambat' => $terlambat_info['keterangan'],
                    'shift_info' => [
                        'shift' => $shift,
                        'nama_shift' => $nama_shift,
                        'jam_mulai' => substr($jam_shift_mulai, 0, 5),
                        'jam_selesai' => substr($jam_shift_selesai, 0, 5)
                    ],
                    'location_info' => [
                        'lokasi' => $lokasi_masuk,
                        'latitude' => $latitude_masuk,
                        'longitude' => $longitude_masuk,
                        'accuracy' => $accuracy,
                        'distance_from_office' => $distanceFromOffice,
                        'accuracy_status' => $this->getAccuracyStatus($accuracy)
                    ]
                ]);
            } else {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Gagal melakukan absensi masuk'
                ], 500);
            }
        } catch (\Exception $e) {
            log_message('error', 'ADMIN ABSENSI CHECKIN Exception: ' . $e->getMessage());
            return $this->respond([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process check-out for admin with accurate location
     */
    public function checkout()
    {
        log_message('debug', 'ADMIN ABSENSI CHECKOUT GPS: Processing check-out with location');
        
        if (!$this->request->isAJAX()) {
            return $this->fail('Only AJAX requests are allowed', 400);
        }
        
        // Get session for audit trail
        $session = \Config\Services::session();
        $userId = $session->get('user_id');
        
        if (!$this->karyawanId) {
            log_message('debug', 'ADMIN ABSENSI CHECKOUT: karyawanId not found');
            return $this->respond([
                'status' => 'error',
                'message' => 'Data karyawan tidak ditemukan'
            ], 400);
        }
        
        date_default_timezone_set('Asia/Jakarta');
        $today = date('Y-m-d');
        
        $attendance = $this->absensiModel->getTodayAttendanceByKaryawan($this->karyawanId);
        
        if (!$attendance) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Data absensi tidak ditemukan. Silakan check in terlebih dahulu.'
            ], 404);
        }
        
        if (!empty($attendance['waktu_pulang'])) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Anda sudah melakukan absensi pulang hari ini'
            ], 400);
        }
        
        $waktu_pulang = date('H:i:s');
        $requestData = $this->request->getJSON();
        
        // Process location for checkout
        $latitude_pulang = $requestData->latitude_pulang ?? null;
        $longitude_pulang = $requestData->longitude_pulang ?? null;
        $accuracy = $requestData->accuracy ?? null;
        
        // Validate and normalize coordinates
        $validCoords = $this->validateAndNormalizeCoordinates($latitude_pulang, $longitude_pulang);
        
        if ($validCoords) {
            $latitude_pulang = $validCoords['latitude'];
            $longitude_pulang = $validCoords['longitude'];
            $lokasi_pulang = $this->getLocationNameFromCoordinates($latitude_pulang, $longitude_pulang);
            
            // Validate location is within Indonesia
            if (!$this->isLocationInIndonesia($latitude_pulang, $longitude_pulang)) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Lokasi berada di luar Indonesia'
                ], 400);
            }
            
            // Check if location is valid (not 0,0)
            if ($this->isNullIsland($latitude_pulang, $longitude_pulang)) {
                $lokasi_pulang = 'Lokasi tidak valid (null island)';
                $latitude_pulang = null;
                $longitude_pulang = null;
            }
            
            // Calculate distance from check-in location
            $distanceFromCheckin = null;
            if ($attendance['latitude_masuk'] && $attendance['longitude_masuk'] && 
                $latitude_pulang && $longitude_pulang) {
                $distanceFromCheckin = $this->calculateDistance(
                    $attendance['latitude_masuk'], $attendance['longitude_masuk'],
                    $latitude_pulang, $longitude_pulang
                );
            }
            
            $location_metadata = [
                'accuracy' => $accuracy,
                'distance_from_checkin' => $distanceFromCheckin,
                'timestamp' => time(),
                'source' => 'gps'
            ];
            
        } else {
            $latitude_pulang = null;
            $longitude_pulang = null;
            $lokasi_pulang = 'Lokasi tidak terdeteksi';
            $location_metadata = [
                'source' => 'unknown',
                'timestamp' => time()
            ];
        }
        
        // Prepare data
        $updateData = [
            'id' => $attendance['id'],
            'waktu_pulang' => $waktu_pulang,
            'lokasi_pulang' => $lokasi_pulang,
            'latitude_pulang' => $latitude_pulang,
            'longitude_pulang' => $longitude_pulang,
            'location_metadata_pulang' => json_encode($location_metadata),
            'device_pulang' => $this->request->getUserAgent()->getAgentString(),
            'ip_address_pulang' => $this->request->getIPAddress(),
            'keterangan' => $requestData->keterangan ?? ($attendance['keterangan'] ?? ''),
            // AUDIT TRAIL
            'updated_by' => $userId
        ];
        
        // Calculate working hours and overtime
        if (!empty($attendance['waktu_masuk'])) {
            $shift = $attendance['shift'] ?? 'siang';
            
            $jam_kerja = $this->absensiModel->calculateJamKerja($attendance['waktu_masuk'], $waktu_pulang);
            $updateData['jam_kerja'] = $jam_kerja;
            
            $jam_lembur = $this->absensiModel->calculateLembur($waktu_pulang, $shift);
            $updateData['jam_lembur'] = $jam_lembur;
        }
        
        log_message('debug', 'ADMIN ABSENSI CHECKOUT: Updating attendance with location: ' . print_r($updateData, true));
        
        try {
            if ($this->absensiModel->save($updateData)) {
                if (isset($updateData['jam_lembur']) && $updateData['jam_lembur'] > 100) {
                    log_message('error', 'Invalid overtime detected: ' . $updateData['jam_lembur'] . ' hours, resetting to 0');
                    $this->absensiModel->update($attendance['id'], ['jam_lembur' => 0]);
                    $updateData['jam_lembur'] = 0;
                }
                
                $waktu_display_wib = date('H:i', strtotime($waktu_pulang)) . ' WIB';
                $jam_kerja_display = $this->formatJamKerja($updateData['jam_kerja'] ?? 0);
                
                return $this->respond([
                    'status' => 'success',
                    'message' => 'Absensi pulang berhasil',
                    'data' => $updateData,
                    'waktu_display' => $waktu_display_wib,
                    'jam_kerja_display' => $jam_kerja_display,
                    'jam_lembur_display' => !empty($updateData['jam_lembur']) && $updateData['jam_lembur'] < 100 ? 
                        number_format($updateData['jam_lembur'], 1) . ' jam' : '0 jam',
                    'location_info' => [
                        'lokasi' => $lokasi_pulang,
                        'latitude' => $latitude_pulang,
                        'longitude' => $longitude_pulang,
                        'accuracy' => $accuracy,
                        'distance_from_checkin' => $distanceFromCheckin,
                        'accuracy_status' => $this->getAccuracyStatus($accuracy)
                    ]
                ]);
            } else {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Gagal melakukan absensi pulang'
                ], 500);
            }
        } catch (\Exception $e) {
            log_message('error', 'ADMIN ABSENSI CHECKOUT Exception: ' . $e->getMessage());
            return $this->respond([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get attendance history for admin
     */
    public function history()
    {
        log_message('debug', 'ADMIN ABSENSI HISTORY: Getting attendance history');
        
        if (!$this->karyawanId) {
            return $this->respond(['history' => []]);
        }
        
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');
        
        $history = $this->absensiModel->getAbsensiHistory($this->karyawanId, $startDate, $endDate, 30);
        
        $formattedHistory = [];
        foreach ($history as $absensi) {
            $formattedHistory[] = [
                'tanggal' => $absensi['tanggal'],
                'waktu_masuk' => $absensi['waktu_masuk'],
                'waktu_pulang' => $absensi['waktu_pulang'],
                'jam_kerja' => $absensi['jam_kerja'] ?? 0,
                'jam_lembur' => $absensi['jam_lembur'] ?? 0,
                'terlambat' => $absensi['terlambat'] ?? 0,
                'status' => $absensi['status'] ?? 'Hadir',
                'keterangan' => $absensi['keterangan'] ?? '',
                'shift' => $absensi['shift'] ?? 'siang',
                'lokasi_masuk' => $absensi['lokasi_masuk'] ?? '',
                'lokasi_pulang' => $absensi['lokasi_pulang'] ?? ''
            ];
        }
        
        log_message('debug', 'ADMIN ABSENSI HISTORY: Returning ' . count($formattedHistory) . ' records');
        
        return $this->respond(['history' => $formattedHistory]);
    }

    /**
     * Helper: Calculate lateness based on shift
     */
    private function hitungTerlambatBerdasarkanShift($waktu_masuk, $shift)
    {
        $jam_mulai_shift = $this->absensiModel->getJamMulaiByShift($shift);
        $jam_selesai_shift = $this->absensiModel->getJamSelesaiByShift($shift);
        $toleransi = 30;
        
        $jam_masuk = strtotime($waktu_masuk);
        $jam_mulai = strtotime($jam_mulai_shift);
        $jam_selesai = strtotime($jam_selesai_shift);
        $batas_toleransi = $jam_mulai + ($toleransi * 60);
        
        if ($shift !== 'malam') {
            if ($jam_masuk > $jam_selesai) {
                $selisih = $jam_masuk - $jam_mulai;
                $terlambat_menit = (int) ceil($selisih / 60);
                
                return [
                    'terlambat' => $terlambat_menit,
                    'keterangan' => 'Absensi di luar jam shift (terlambat ' . $this->formatMenitKeJam($terlambat_menit) . ')'
                ];
            }
        }
        
        if ($shift === 'malam') {
            if ($jam_masuk < strtotime('12:00:00')) {
                $jam_mulai_malam = strtotime('20:00:00') - 86400;
                $jam_selesai_malam = strtotime('05:00:00');
                $batas_toleransi_malam = $jam_mulai_malam + ($toleransi * 60);
                
                if ($jam_masuk < $jam_mulai_malam) {
                    return [
                        'terlambat' => 0,
                        'keterangan' => 'Masuk lebih awal (shift malam)'
                    ];
                }
                
                if ($jam_masuk <= $batas_toleransi_malam) {
                    return [
                        'terlambat' => 0,
                        'keterangan' => 'Tepat waktu (dalam toleransi ' . $toleransi . ' menit)'
                    ];
                }
                
                $selisih = $jam_masuk - $jam_mulai_malam;
                $terlambat_menit = (int) ceil($selisih / 60);
                
                return [
                    'terlambat' => $terlambat_menit,
                    'keterangan' => 'Terlambat ' . $this->formatMenitKeJam($terlambat_menit)
                ];
            }
        }
        
        if ($jam_masuk < $jam_mulai) {
            return [
                'terlambat' => 0,
                'keterangan' => 'Masuk lebih awal'
            ];
        }
        
        if ($jam_masuk <= $batas_toleransi) {
            return [
                'terlambat' => 0,
                'keterangan' => 'Tepat waktu (dalam toleransi ' . $toleransi . ' menit)'
            ];
        }
        
        $selisih = $jam_masuk - $jam_mulai;
        $terlambat_menit = (int) ceil($selisih / 60);
        $terlambat_final = min($terlambat_menit, 480);
        
        return [
            'terlambat' => $terlambat_final,
            'keterangan' => $terlambat_final >= 480 ? 
                'Terlambat extreme (maksimal)' : 
                'Terlambat ' . $this->formatMenitKeJam($terlambat_final)
        ];
    }

    /**
     * Get current location information
     */
    public function getLocationInfo()
    {
        if (!$this->request->isAJAX()) {
            return $this->fail('Only AJAX requests are allowed', 400);
        }
        
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn') || strtolower($session->get('role')) !== 'admin') {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }
        
        $data = $this->request->getJSON();
        
        try {
            if (empty($data->latitude) || empty($data->longitude)) {
                return $this->respond(['status' => 'error', 'message' => 'Koordinat diperlukan'], 400);
            }
            
            $validCoords = $this->validateAndNormalizeCoordinates($data->latitude, $data->longitude);
            
            if (!$validCoords) {
                return $this->respond(['status' => 'error', 'message' => 'Koordinat tidak valid'], 400);
            }
            
            $locationName = $this->getLocationNameFromCoordinates(
                $validCoords['latitude'], 
                $validCoords['longitude']
            );
            
            $isInIndonesia = $this->isLocationInIndonesia(
                $validCoords['latitude'], 
                $validCoords['longitude']
            );
            
            $distanceFromOffice = $this->calculateDistanceFromOffice(
                $validCoords['latitude'], 
                $validCoords['longitude']
            );
            
            return $this->respond([
                'status' => 'success',
                'message' => 'Location information retrieved',
                'data' => [
                    'coordinates' => $validCoords,
                    'location_name' => $locationName,
                    'is_in_indonesia' => $isInIndonesia,
                    'distance_from_office' => $distanceFromOffice,
                    'accuracy_status' => $this->getAccuracyStatus($data->accuracy ?? null)
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============ HELPER METHODS ============

    /**
     * Validate and normalize coordinates
     */
    private function validateAndNormalizeCoordinates($latitude, $longitude)
    {
        if (is_null($latitude) || is_null($longitude)) {
            return null;
        }
        
        $lat = floatval($latitude);
        $lng = floatval($longitude);
        
        if (!is_numeric($lat) || !is_numeric($lng)) {
            return null;
        }
        
        if ($lat < -90 || $lat > 90) {
            return null;
        }
        
        if ($lng < -180 || $lng > 180) {
            return null;
        }
        
        if (abs($lat) < 0.0001 && abs($lng) < 0.0001) {
            return null;
        }
        
        return [
            'latitude' => number_format($lat, 8, '.', ''),
            'longitude' => number_format($lng, 8, '.', '')
        ];
    }

    /**
     * Check if location is within Indonesia boundaries
     */
    private function isLocationInIndonesia($latitude, $longitude)
    {
        $minLat = -11.0;
        $maxLat = 6.0;
        $minLng = 95.0;
        $maxLng = 141.0;
        
        $lat = floatval($latitude);
        $lng = floatval($longitude);
        
        return ($lat >= $minLat && $lat <= $maxLat && 
                $lng >= $minLng && $lng <= $maxLng);
    }

    /**
     * Check if coordinates are null island (0,0)
     */
    private function isNullIsland($latitude, $longitude)
    {
        $lat = floatval($latitude);
        $lng = floatval($longitude);
        
        return (abs($lat) < 0.0001 && abs($lng) < 0.0001);
    }

    /**
     * Get location name from coordinates using reverse geocoding
     */
    private function getLocationNameFromCoordinates($latitude, $longitude)
    {
        if (!$latitude || !$longitude) {
            return 'Lokasi tidak diketahui';
        }
        
        try {
            $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$latitude}&lon={$longitude}&zoom=18&addressdetails=1";
            
            $client = \Config\Services::curlrequest();
            $response = $client->get($url, [
                'headers' => [
                    'User-Agent' => 'CDW Engineering HR System',
                    'Accept-Language' => 'id-ID,id;q=0.9,en;q=0.8'
                ],
                'timeout' => 5
            ]);
            
            $data = json_decode($response->getBody(), true);
            
            if (isset($data['display_name'])) {
                return $data['display_name'];
            }
            
            if (isset($data['address'])) {
                $address = $data['address'];
                
                if (isset($address['road']) && isset($address['suburb'])) {
                    return $address['road'] . ', ' . $address['suburb'];
                } elseif (isset($address['village'])) {
                    return $address['village'];
                } elseif (isset($address['city'])) {
                    return $address['city'];
                }
            }
            
        } catch (\Exception $e) {
            log_message('debug', 'Reverse geocoding failed: ' . $e->getMessage());
        }
        
        return "Koordinat: {$latitude}, {$longitude}";
    }

    /**
     * Calculate distance between two coordinates in meters
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        if (!$lat1 || !$lon1 || !$lat2 || !$lon2) {
            return null;
        }
        
        $earthRadius = 6371000;
        
        $lat1 = deg2rad(floatval($lat1));
        $lon1 = deg2rad(floatval($lon1));
        $lat2 = deg2rad(floatval($lat2));
        $lon2 = deg2rad(floatval($lon2));
        
        $latDelta = $lat2 - $lat1;
        $lonDelta = $lon2 - $lon1;
        
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($lat1) * cos($lat2) * pow(sin($lonDelta / 2), 2)));
        
        return round($angle * $earthRadius, 2);
    }

    /**
     * Calculate distance from office coordinates
     */
    private function calculateDistanceFromOffice($latitude, $longitude)
    {
        $officeLat = -6.2088;
        $officeLng = 106.8456;
        
        return $this->calculateDistance($latitude, $longitude, $officeLat, $officeLng);
    }

    /**
     * Get accuracy status based on meter value
     */
    private function getAccuracyStatus($accuracy)
    {
        if (is_null($accuracy)) {
            return 'unknown';
        }
        
        $accuracy = floatval($accuracy);
        
        if ($accuracy <= 20) {
            return 'very_high';
        } elseif ($accuracy <= 50) {
            return 'high';
        } elseif ($accuracy <= 100) {
            return 'good';
        } elseif ($accuracy <= 500) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    /**
     * Helper: Format shift name
     */
    private function formatNamaShift($shift)
    {
        $shift_names = [
            'pagi' => 'Shift Pagi',
            'siang' => 'Shift Siang',
            'sore' => 'Shift Sore',
            'malam' => 'Shift Malam'
        ];
        
        return $shift_names[$shift] ?? 'Shift Siang';
    }

    /**
     * Helper: Format minutes to hours
     */
    private function formatMenitKeJam($menit)
    {
        if ($menit <= 0) return '0 menit';
        
        $jam = floor($menit / 60);
        $sisa_menit = $menit % 60;
        
        if ($jam > 0 && $sisa_menit > 0) {
            return $jam . ' jam ' . $sisa_menit . ' menit';
        } elseif ($jam > 0) {
            return $jam . ' jam';
        } else {
            return $sisa_menit . ' menit';
        }
    }

    /**
     * Helper: Format working hours
     */
    private function formatJamKerja($jam_decimal)
    {
        if (empty($jam_decimal) || $jam_decimal <= 0) return '-';
        
        $jam = floor($jam_decimal);
        $menit = round(($jam_decimal - $jam) * 60);
        
        if ($jam > 0 && $menit > 0) {
            return "{$jam} jam {$menit} menit";
        } elseif ($jam > 0) {
            return "{$jam} jam";
        } else {
            return "{$menit} menit";
        }
    }

    /**
     * Get monthly statistics
     */
    private function getMonthlyStats()
    {
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-t');
        
        $stats = [
            'hadir_bulan_ini' => 0,
            'terlambat_bulan_ini' => 0,
            'jam_lembur_bulan_ini' => 0,
            'cuti_terpakai' => 0
        ];
        
        if ($this->karyawanId) {
            $absensiBulanIni = $this->absensiModel->getByKaryawan($this->karyawanId, $startDate, $endDate);
            
            if (is_array($absensiBulanIni)) {
                foreach ($absensiBulanIni as $absensi) {
                    if (($absensi['status'] ?? '') === 'Hadir') {
                        $stats['hadir_bulan_ini']++;
                    }
                    
                    if (($absensi['terlambat'] ?? 0) > 0) {
                        $stats['terlambat_bulan_ini']++;
                    }
                    
                    $stats['jam_lembur_bulan_ini'] += $absensi['jam_lembur'] ?? 0;
                    
                    if (($absensi['status'] ?? '') === 'Cuti') {
                        $stats['cuti_terpakai']++;
                    }
                }
            }
        }
        
        return $stats;
    }
}