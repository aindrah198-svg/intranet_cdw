<?php

namespace App\Controllers\Teknisi;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\KaryawanModel;
use App\Models\AbsensiModel;

class Dashboard extends BaseController
{
    protected $userModel;
    protected $karyawanModel;
    protected $absensiModel;
    
    /**
     * Initialize controller - JANGAN PAKAI CONSTRUCTOR!
     */
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, 
                                 \CodeIgniter\HTTP\ResponseInterface $response, 
                                 \Psr\Log\LoggerInterface $logger)
    {
        // Panggil parent initController dulu
        parent::initController($request, $response, $logger);
        
        // Inisialisasi model di sini
        $this->userModel = new UserModel();
        $this->karyawanModel = new KaryawanModel();
        $this->absensiModel = new AbsensiModel();
    }
    
    public function index()
    {
        // Cek session
        $session = \Config\Services::session();
        
        // Cek login
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu.');
        }
        
        // Cek role (case-insensitive)
        $userRole = $session->get('role');
        $roleLower = strtolower($userRole ?? '');
        
        if ($roleLower !== 'teknisi') {
            // Redirect sesuai role
            switch ($roleLower) {
                case 'admin':
                case 'hrd':
                    return redirect()->to(base_url('admin'))->with('info', 'Anda dialihkan ke dashboard Admin.');
                case 'direktur':
                    return redirect()->to(base_url('direktur'))->with('info', 'Anda dialihkan ke dashboard Direktur.');
                case 'accounting':
                    return redirect()->to(base_url('accounting'))->with('info', 'Anda dialihkan ke dashboard Accounting.');
                case 'sales':
                case 'marketing':
                    return redirect()->to(base_url('sales'))->with('info', 'Anda dialihkan ke dashboard Sales.');
                case 'staff':
                    return redirect()->to(base_url('staff'))->with('info', 'Anda dialihkan ke dashboard Staff.');
                default:
                    return redirect()->to(base_url('login'))->with('error', 'Role tidak dikenali.');
            }
        }
        
        // Ambil user_id dari session
        $userId = $session->get('user_id');
        
        // Get user data dengan relasi ke karyawan
        $userData = $this->userModel->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
            ->select('users.*, karyawan.*')
            ->where('users.id', $userId)
            ->first();
        
        // Jika userData tidak ditemukan, gunakan data session
        if (!$userData) {
            $userData = [
                'user_id' => $session->get('user_id'),
                'name' => $session->get('name'),
                'username' => $session->get('username'),
                'email' => $session->get('email'),
                'role' => $userRole,
                'nik' => 'N/A',
                'nama_lengkap' => $session->get('name'),
                'nama_panggilan' => $session->get('username'),
                'jabatan' => 'Teknisi',
                'departemen' => 'Engineering',
                'divisi' => 'Technical',
                'tanggal_masuk' => null,
                'status_karyawan' => 'Tetap'
            ];
        }
        
        // Get today's attendance untuk dashboard
        $absensiToday = null;
        if ($userData['karyawan_id'] ?? null) {
            $absensiToday = $this->absensiModel->getTodayAttendanceByKaryawan($userData['karyawan_id']);
        }
        
        // Format tanggal masuk jika ada
        $tanggalMasukFormatted = '';
        if (!empty($userData['tanggal_masuk'])) {
            $tanggalMasukFormatted = date('d F Y', strtotime($userData['tanggal_masuk']));
        }
        
        // Hitung masa kerja
        $masaKerja = $this->calculateMonthsOfWork($userData['tanggal_masuk'] ?? null);
        
        // Data untuk view
        $data = [
            'title' => 'Dashboard Teknisi',
            'subtitle' => 'Selamat datang di sistem',
            'active' => 'dashboard',
            'user' => $userData,
            'absensiToday' => $absensiToday,
            'tanggal_masuk_formatted' => $tanggalMasukFormatted,
            'masa_kerja' => $masaKerja,
            'stats' => $this->getDashboardStats($userData['karyawan_id'] ?? null)
        ];
        
        // Render view
        return view('teknisi/templates/header', $data) .
               view('teknisi/templates/sidebar', $data) .
               view('teknisi/dashboard/index', $data) .
               view('teknisi/templates/footer', $data);
    }
    
    /**
     * Get dashboard statistics
     */
    private function getDashboardStats($karyawanId)
    {
        $stats = [
            'total_hari_kerja' => 0,
            'total_hadir' => 0,
            'total_terlambat' => 0,
            'persentase_kehadiran' => 0
        ];
        
        if (!$karyawanId) {
            return $stats;
        }
        
        // Get current month
        $currentMonth = date('Y-m');
        $startDate = $currentMonth . '-01';
        $endDate = date('Y-m-t');
        
        // Get attendance for this month
        $absensiBulanIni = $this->absensiModel->getByKaryawan($karyawanId, $startDate, $endDate);
        
        // Calculate stats
        $total_hadir = 0;
        $total_terlambat = 0;
        
        if (is_array($absensiBulanIni)) {
            foreach ($absensiBulanIni as $absensi) {
                if (($absensi['status'] ?? '') === 'Hadir') {
                    $total_hadir++;
                }
                if (($absensi['terlambat'] ?? 0) > 0) {
                    $total_terlambat++;
                }
            }
        }
        
        // Calculate total working days this month
        $totalHariKerja = $this->calculateWorkingDays($startDate, $endDate);
        
        // Calculate attendance percentage
        $persentase = $totalHariKerja > 0 ? ($total_hadir / $totalHariKerja) * 100 : 0;
        
        return [
            'total_hari_kerja' => $totalHariKerja,
            'total_hadir' => $total_hadir,
            'total_terlambat' => $total_terlambat,
            'persentase_kehadiran' => round($persentase, 1)
        ];
    }
    
    /**
     * Calculate working days (exclude weekends)
     */
    private function calculateWorkingDays($startDate, $endDate)
    {
        $start = strtotime($startDate);
        $end = strtotime($endDate);
        $workingDays = 0;
        
        while ($start <= $end) {
            $dayOfWeek = date('N', $start);
            // Exclude Saturday (6) and Sunday (7)
            if ($dayOfWeek < 6) {
                $workingDays++;
            }
            $start = strtotime('+1 day', $start);
        }
        
        return $workingDays;
    }
    
    /**
     * Calculate months and years of work
     */
    private function calculateMonthsOfWork($startDate)
    {
        if (empty($startDate)) {
            return ['years' => 0, 'months' => 0];
        }
        
        $start = new \DateTime($startDate);
        $now = new \DateTime();
        
        $interval = $start->diff($now);
        
        return [
            'years' => $interval->y,
            'months' => $interval->m
        ];
    }
}