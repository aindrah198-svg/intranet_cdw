<?php
// C:\xampp\htdocs\cdwnet\app\Controllers\Teknisi\TeknisiController.php

namespace App\Controllers\Teknisi;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class TeknisiController extends BaseController
{
    // Properti dasar
    protected $session;
    protected $userRole;
    protected $userId;
    protected $userData;
    protected $karyawanData;
    
    /**
     * Initialize the controller - INI YANG BENAR UNTUK CI4
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // HARUS memanggil parent initController
        parent::initController($request, $response, $logger);
        
        $this->session = \Config\Services::session();
        $this->userRole = $this->session->get('role');
        $this->userId = $this->session->get('user_id');
        
        // Debug info
        log_message('debug', 'TEKNISI CONTROLLER: Initializing...');
        log_message('debug', 'TEKNISI CONTROLLER: User ID = ' . $this->userId);
        log_message('debug', 'TEKNISI CONTROLLER: User Role = ' . $this->userRole);
        log_message('debug', 'TEKNISI CONTROLLER: Is Logged In = ' . ($this->session->get('isLoggedIn') ? 'Yes' : 'No'));
        
        // Cek apakah user sudah login
        if (!$this->session->get('isLoggedIn')) {
            log_message('debug', 'TEKNISI CONTROLLER: User not logged in. Redirecting to login.');
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Silakan login terlebih dahulu.');
        }
        
        // Cek apakah user adalah teknisi (case-insensitive)
        $roleLower = strtolower($this->userRole ?? '');
        
        if ($roleLower !== 'teknisi') {
            log_message('debug', 'TEKNISI CONTROLLER: User is not TEKNISI. Redirecting...');
            // Redirect ke dashboard sesuai role
            return $this->redirectToDashboard($this->userRole);
        }
        
        // Load user data
        $this->userData = [
            'user_id' => $this->userId,
            'name' => $this->session->get('name'),
            'username' => $this->session->get('username'),
            'email' => $this->session->get('email'),
            'role' => $this->userRole,
            'karyawan_id' => $this->session->get('karyawan_id')
        ];
        
        log_message('debug', 'TEKNISI CONTROLLER: User Data = ' . print_r($this->userData, true));
        
        // Load karyawan data
        $userModel = new \App\Models\UserModel();
        $this->karyawanData = $userModel->getKaryawanByUserId($this->userId);
        
        if ($this->karyawanData) {
            log_message('debug', 'TEKNISI CONTROLLER: Karyawan data found. Karyawan ID = ' . $this->karyawanData['id']);
        } else {
            log_message('debug', 'TEKNISI CONTROLLER: No karyawan data found for user ID = ' . $this->userId);
        }
        
        log_message('debug', 'TEKNISI CONTROLLER: Initialization complete.');
    }
    
    /**
     * Redirect ke dashboard sesuai role
     */
    private function redirectToDashboard($role)
    {
        $roleLower = strtolower($role ?? '');
        
        log_message('debug', 'TEKNISI CONTROLLER redirectToDashboard: Role = ' . $role . ' (Lowercase: ' . $roleLower . ')');
        
        switch ($roleLower) {
            case 'admin':
            case 'hrd':
                log_message('debug', 'TEKNISI CONTROLLER: Redirecting to ADMIN dashboard');
                return redirect()->to(base_url('admin'))->with('info', 'Anda dialihkan ke dashboard Admin.');
            case 'direktur':
                log_message('debug', 'TEKNISI CONTROLLER: Redirecting to DIREKTUR dashboard');
                return redirect()->to(base_url('direktur'))->with('info', 'Anda dialihkan ke dashboard Direktur.');
            case 'accounting':
                log_message('debug', 'TEKNISI CONTROLLER: Redirecting to ACCOUNTING dashboard');
                return redirect()->to(base_url('accounting'))->with('info', 'Anda dialihkan ke dashboard Accounting.');
            case 'sales':
            case 'marketing':
                log_message('debug', 'TEKNISI CONTROLLER: Redirecting to SALES dashboard');
                return redirect()->to(base_url('sales'))->with('info', 'Anda dialihkan ke dashboard Sales.');
            case 'staff':
                log_message('debug', 'TEKNISI CONTROLLER: Redirecting to STAFF dashboard');
                return redirect()->to(base_url('staff'))->with('info', 'Anda dialihkan ke dashboard Staff.');
            default:
                log_message('debug', 'TEKNISI CONTROLLER: Role not recognized: ' . $role);
                return redirect()->to(base_url('login'))->with('error', 'Role tidak dikenali. Silakan login kembali.');
        }
    }
    
    /**
     * Render view dengan template teknisi
     */
    protected function renderView($view, $data = [])
    {
        // Set default data
        $defaultData = [
            'user' => $this->userData,
            'karyawan' => $this->karyawanData,
            'active' => 'dashboard',
            'title' => 'Dashboard Teknisi',
            'subtitle' => date('l, d F Y')
        ];
        
        // Merge dengan data yang dikirim
        $data = array_merge($defaultData, $data);
        
        // Debug info
        log_message('debug', 'TEKNISI CONTROLLER renderView: Rendering view = ' . $view);
        log_message('debug', 'TEKNISI CONTROLLER renderView: Active menu = ' . $data['active']);
        log_message('debug', 'TEKNISI CONTROLLER renderView: User name = ' . ($data['user']['name'] ?? 'Unknown'));
        
        // Cek apakah view file ada
        $viewPath = APPPATH . 'Views/' . $view . '.php';
        if (!file_exists($viewPath)) {
            log_message('error', 'TEKNISI CONTROLLER: View file not found: ' . $viewPath);
            
            // Fallback: tampilkan halaman sederhana
            return view('errors/html/error_404', [
                'message' => 'View file not found: ' . $view,
                'code' => 404
            ]);
        }
        
        // Render view dengan template teknisi
        echo view('teknisi/templates/header', $data);
        echo view('teknisi/templates/sidebar', $data);
        echo view('teknisi/templates/navbar', $data);
        echo view($view, $data);
        echo view('teknisi/templates/footer', $data);
    }
    
    /**
     * Helper method untuk mendapatkan data karyawan
     */
    protected function getKaryawanData()
    {
        return $this->karyawanData;
    }
    
    /**
     * Helper method untuk mendapatkan data user
     */
    protected function getUserData()
    {
        return $this->userData;
    }
    
    /**
     * Helper method untuk cek apakah user sudah absen hari ini
     */
    protected function hasCheckedInToday()
    {
        if (!$this->karyawanData) {
            return false;
        }
        
        $absensiModel = new \App\Models\AbsensiModel();
        $today = date('Y-m-d');
        
        $absensiToday = $absensiModel->where('karyawan_id', $this->karyawanData['id'])
                                    ->where('DATE(tanggal)', $today)
                                    ->first();
        
        return !empty($absensiToday);
    }
    
    /**
     * Helper method untuk mendapatkan absensi hari ini
     */
    protected function getTodayAttendance()
    {
        if (!$this->karyawanData) {
            return null;
        }
        
        $absensiModel = new \App\Models\AbsensiModel();
        $today = date('Y-m-d');
        
        return $absensiModel->where('karyawan_id', $this->karyawanData['id'])
                           ->where('DATE(tanggal)', $today)
                           ->first();
    }
    
    /**
     * Helper method untuk validasi akses
     */
    protected function validateAccess($requiredRole = 'teknisi')
    {
        $userRole = strtolower($this->userRole ?? '');
        $requiredRole = strtolower($requiredRole);
        
        if ($userRole !== $requiredRole) {
            log_message('debug', 'TEKNISI CONTROLLER validateAccess: Access denied. User role = ' . $userRole . ', Required role = ' . $requiredRole);
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Akses ditolak!');
        }
        
        return true;
    }
}