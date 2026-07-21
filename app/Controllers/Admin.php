<?php
namespace App\Controllers;

use App\Models\UserModel;

class Admin extends BaseController
{
    protected $userModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        // Debug: log session data
        error_log("=== ADMIN INDEX CALLED ===");
        error_log("Session data: " . print_r(session()->get(), true));
        
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            error_log("User not logged in, redirecting to login");
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu!');
        }

        $data = [
            'title' => 'Admin Dashboard - CDW Engineering',
            'subtitle' => 'Dashboard Overview',
            'user' => [
                'id' => session()->get('user_id') ?: 0,
                'name' => session()->get('name') ?: 'Administrator',
                'username' => session()->get('username') ?: 'admin',
                'email' => session()->get('email') ?: 'admin@cdw-engineering.com',
                'role' => session()->get('role') ?: 'admin'
            ],
            'active' => 'dashboard',
            'totalUsers' => $this->userModel->countAllResults()
        ];
        
        error_log("Data to view: " . print_r($data, true));
        
        // Perbaikan: pastikan data dikirim dengan benar
        return view('admin/index', $data);
    }

    public function profile()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu!');
        }

        $data = [
            'title' => 'Profile - CDW Engineering',
            'subtitle' => 'Profile Settings',
            'user' => [
                'name' => session()->get('name') ?: 'Administrator',
                'username' => session()->get('username') ?: 'admin',
                'email' => session()->get('email') ?: 'admin@cdw-engineering.com',
                'role' => session()->get('role') ?: 'admin',
                'login_time' => date('d-m-Y H:i:s', session()->get('login_time') ?: time())
            ],
            'active' => 'profile'
        ];
        
        return view('admin/profile', $data);
    }

    public function settings()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu!');
        }

        $data = [
            'title' => 'Settings - CDW Engineering',
            'subtitle' => 'System Settings',
            'user' => [
                'name' => session()->get('name') ?: 'Administrator',
                'username' => session()->get('username') ?: 'admin',
                'email' => session()->get('email') ?: 'admin@cdw-engineering.com',
                'role' => session()->get('role') ?: 'admin'
            ],
            'active' => 'settings'
        ];
        
        return view('admin/settings', $data);
    }

    public function updateProfile()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu!');
        }

        $rules = [
            'name' => 'required|min_length[3]',
            'email' => 'required|valid_email'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Update session data
        session()->set([
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email')
        ]);

        return redirect()->to(base_url('admin/profile'))->with('success', 'Profile berhasil diperbarui!');
    }

    public function changePassword()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu!');
        }

        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        return redirect()->to(base_url('admin/settings'))->with('success', 'Password berhasil diubah!');
    }
    
    public function users()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to(base_url('admin'))->with('error', 'Akses ditolak!');
        }

        $data = [
            'title' => 'User Management - CDW Engineering',
            'subtitle' => 'Manage System Users',
            'user' => [
                'name' => session()->get('name') ?: 'Administrator',
                'role' => session()->get('role') ?: 'admin'
            ],
            'users' => $this->userModel->getAllActiveUsers(),
            'active' => 'users'
        ];
        
        return view('admin/users', $data);
    }
}