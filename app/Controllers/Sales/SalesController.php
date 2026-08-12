<?php

namespace App\Controllers\Sales;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class SalesController extends BaseController
{
    protected $session;
    protected $userRole;
    protected $userId;
    protected $userData;
    protected $karyawanData;
    protected $karyawanModel;
    protected $clientModel;
    protected $db; // Tambahkan ini!
    
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        $this->session = \Config\Services::session();
        $this->userRole = $this->session->get('role');
        $this->userId = $this->session->get('user_id');
        $this->db = \Config\Database::connect(); // Inisialisasi database
        
        // Load models
        $this->karyawanModel = new \App\Models\KaryawanModel();
        $this->clientModel = new \App\Models\ClientModel();
        
        // Cek login - PERBAIKI: jangan langsung return
        if (!$this->session->get('isLoggedIn')) {
            $this->session->setFlashdata('error', 'Silakan login terlebih dahulu.');
            // Tidak boleh return redirect di initController!
            // Biarkan ditangani oleh filter
        }
        
        // Setup user data
        $this->setupUserData();
        
        // Cek role - setelah setup user data
        if (!empty($this->userId)) {
            $this->checkUserRole();
        }
    }
    
    private function checkUserRole()
    {
        $roleLower = strtolower($this->userRole ?? '');
        $allowedRoles = ['sales', 'marketing', 'admin', 'direktur']; // Tambahkan admin/direktur untuk testing
        
        if (!in_array($roleLower, $allowedRoles)) {
            $this->redirectToDashboard($this->userRole);
        }
    }
    
    private function redirectToDashboard($role)
    {
        $roleLower = strtolower($role ?? '');
        
        switch ($roleLower) {
            case 'hrd':
                header('Location: ' . base_url('hrd'));
                exit();
            case 'admin':
                header('Location: ' . base_url('admin'));
                exit();
            case 'direktur':
                header('Location: ' . base_url('direktur'));
                exit();
            case 'accounting':
                header('Location: ' . base_url('accounting'));
                exit();
            case 'teknisi':
                header('Location: ' . base_url('teknisi'));
                exit();
            case 'staff':
                header('Location: ' . base_url('staff'));
                exit();
            default:
                header('Location: ' . base_url('login') . '?error=Role+tidak+dikenali');
                exit();
        }
    }
    
    private function setupUserData()
    {
        $this->userData = [
            'id' => $this->userId ?? 0, // PASTIKAN ADA
            'user_id' => $this->userId ?? 0,
            'name' => $this->session->get('name') ?? 'Guest',
            'username' => $this->session->get('username') ?? 'guest',
            'email' => $this->session->get('email') ?? '',
            'role' => $this->userRole ?? 'sales',
            'karyawan_id' => $this->session->get('karyawan_id') ?? null
        ];
        
        // DEBUG: Log user data
        log_message('debug', 'SalesController - User Data Setup: ' . json_encode($this->userData));
        
        // Jika data karyawan_id kosong, cari dari database
        if (empty($this->userData['karyawan_id']) && !empty($this->userData['id'])) {
            $this->karyawanData = $this->karyawanModel->getKaryawanByUserId($this->userData['id']);
            if ($this->karyawanData) {
                $this->userData['karyawan_id'] = $this->karyawanData['id'] ?? null;
                $this->session->set('karyawan_id', $this->userData['karyawan_id']);
                
                // Update karyawanData dengan data lengkap
                $this->karyawanData = $this->karyawanModel->find($this->userData['karyawan_id']);
            }
        } elseif (!empty($this->userData['karyawan_id'])) {
            $this->karyawanData = $this->karyawanModel->find($this->userData['karyawan_id']);
        }
        
        // DEBUG: Log karyawan data
        log_message('debug', 'SalesController - Karyawan Data: ' . json_encode($this->karyawanData));
    }
    
    /**
     * Render view dengan layout sales
     * PERBAIKI: Gunakan return, bukan echo!
     */
    protected function renderView($view, $data = [])
    {
        $userData = $this->getUserData();
        $karyawanData = $this->getKaryawanData();
        
        // Default data
        $defaultData = [
            'user' => $userData,
            'karyawan' => $karyawanData,
            'title' => $data['title'] ?? 'Dashboard Sales',
            'subtitle' => $data['subtitle'] ?? date('l, d F Y'),
            'active' => $data['active'] ?? 'dashboard'
        ];
        
        // Merge dengan data tambahan
        $viewData = array_merge($defaultData, $data);
        
        // Return string (bisa juga langsung output)
        $output = view('sales/templates/header', $viewData);
        $output .= view('sales/templates/sidebar', $viewData);
        $output .= view('sales/templates/navbar', $viewData);
        $output .= view($view, $viewData);
        $output .= view('sales/templates/footer', $viewData);
        
        return $output;
    }
    
    /**
     * Alternative: Render view dengan return response object
     */
    protected function renderViewAsResponse($view, $data = [])
    {
        $userData = $this->getUserData();
        $karyawanData = $this->getKaryawanData();
        
        // Default data
        $defaultData = [
            'user' => $userData,
            'karyawan' => $karyawanData,
            'title' => $data['title'] ?? 'Dashboard Sales',
            'subtitle' => $data['subtitle'] ?? date('l, d F Y'),
            'active' => $data['active'] ?? 'dashboard'
        ];
        
        // Merge dengan data tambahan
        $viewData = array_merge($defaultData, $data);
        
        // Build the view content
        $content = view('sales/templates/header', $viewData);
        $content .= view('sales/templates/sidebar', $viewData);
        $content .= view('sales/templates/navbar', $viewData);
        $content .= view($view, $viewData);
        $content .= view('sales/templates/footer', $viewData);
        
        // Return as response
        return $this->response->setBody($content);
    }
    
    /**
     * Simple render - tanpa layout (untuk AJAX/API)
     */
    protected function renderSimple($view, $data = [])
    {
        return view($view, $data);
    }
    
    protected function getUserData()
    {
        if (empty($this->userData)) {
            // Setup minimal jika belum ada
            $this->userData = [
                'id' => $this->session->get('user_id') ?? 0,
                'user_id' => $this->session->get('user_id') ?? 0,
                'name' => $this->session->get('name') ?? 'Guest',
                'username' => $this->session->get('username') ?? 'guest',
                'email' => $this->session->get('email') ?? '',
                'role' => $this->session->get('role') ?? 'sales',
                'karyawan_id' => $this->session->get('karyawan_id') ?? null
            ];
        }
        return $this->userData;
    }
    
    protected function getKaryawanData()
    {
        if (empty($this->karyawanData) && !empty($this->userData['karyawan_id'])) {
            $this->karyawanData = $this->karyawanModel->find($this->userData['karyawan_id']);
        }
        return $this->karyawanData;
    }
    
    /**
     * Helper: Get database connection
     */
    protected function getDb()
    {
        return $this->db ?? \Config\Database::connect();
    }
    
    /**
     * Helper: Check if user is logged in
     */
    protected function isLoggedIn()
    {
        return $this->session->get('isLoggedIn') === true;
    }
    
    /**
     * Helper: Check if user has specific role
     */
    protected function hasRole($role)
    {
        $userRole = strtolower($this->userRole ?? '');
        $checkRole = strtolower($role);
        return $userRole === $checkRole;
    }
    
    /**
     * Helper: Check if user has any of the roles
     */
    protected function hasAnyRole(array $roles)
    {
        $userRole = strtolower($this->userRole ?? '');
        foreach ($roles as $role) {
            if ($userRole === strtolower($role)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Helper: Redirect if not logged in
     */
    protected function requireLogin()
    {
        if (!$this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }
        return null;
    }
    
    /**
     * Helper: Validate access to controller
     */
    protected function validateAccess($allowedRoles = ['sales', 'marketing'])
    {
        if (!$this->isLoggedIn()) {
            return redirect()->to('/login');
        }
        
        if (!$this->hasAnyRole($allowedRoles)) {
            return redirect()->to('/sales/dashboard')->with('error', 'Akses ditolak.');
        }
        
        return true;
    }
}