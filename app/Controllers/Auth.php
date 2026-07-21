<?php
namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected $userModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        // Jika sudah login, redirect ke dashboard sesuai role
        if (session()->get('isLoggedIn')) {
            return $this->redirectToDashboard();
        }

        // Cek cookie "ingat saya" - DISABLED UNTUK SEMENTARA
        // $rememberedUser = $this->checkRememberMe();
        // if ($rememberedUser) {
        //     $this->setUserSession($rememberedUser);
        //     return $this->redirectToDashboard($rememberedUser['role']);
        // }

        $data = [
            'title' => 'Login - CDW Engineering',
            'active' => 'login',
            'validation' => \Config\Services::validation()
        ];
        
        return view('auth/login', $data);
    }

    public function process()
    {
        // Validation rules
        $rules = [
            'username' => 'required',
            'password' => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $remember = $this->request->getPost('remember');

        // Debug logging
        log_message('debug', "Login attempt for: {$username}");

        // Verify user from database
        $user = $this->userModel->verifyLogin($username, $password);
        
        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Username atau password salah!');
        }

        log_message('debug', "Login SUCCESS for user ID: {$user['id']}, Role: {$user['role']}");
        
        // Set session data
        $userData = [
            'isLoggedIn' => true,
            'user_id' => $user['id'],
            'username' => $user['username'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'karyawan_id' => $user['karyawan_id'] ?? null,
            'login_time' => time(),
            'login_ip' => $this->request->getIPAddress()
        ];

        session()->set($userData);
        
        // Jika checkbox "ingat saya" dicentang - DISABLED UNTUK SEMENTARA
        // if ($remember == '1') {
        //     $this->setRememberMeCookie($user);
        // } else {
        //     $this->deleteRememberMeCookie();
        // }

        // Update last login
        $this->userModel->update($user['id'], [
            'last_login' => date('Y-m-d H:i:s'),
            'login_ip' => $this->request->getIPAddress()
        ]);

        // Redirect based on role
        return $this->redirectToDashboard($user['role']);
    }

    /**
     * Set cookie untuk fitur "Ingat Saya" - DISABLED
     */
    /*
    private function setRememberMeCookie($user)
    {
        // Generate token unik
        $token = bin2hex(random_bytes(32));
        $selector = bin2hex(random_bytes(16));
        
        // Hash token
        $tokenHash = hash('sha256', $token);
        
        // Simpan token di database
        $db = \Config\Database::connect();
        
        // Hapus token lama
        $db->table('user_remember_tokens')
           ->where('user_id', $user['id'])
           ->delete();
        
        // Buat data token
        $tokenData = [
            'selector' => $selector,
            'token_hash' => $tokenHash,
            'user_id' => $user['id'],
            'expires_at' => date('Y-m-d H:i:s', strtotime('+30 days')),
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // Simpan ke database
        $db->table('user_remember_tokens')->insert($tokenData);
        
        // Set cookie dengan cara yang benar
        $cookieValue = $selector . ':' . $token;
        $expire = time() + (30 * 24 * 60 * 60);
        
        // Gunakan setcookie() native dengan semua parameter
        $cookieSet = setcookie(
            'remember_me',
            $cookieValue,
            [
                'expires' => $expire,
                'path' => '/',
                'domain' => '',
                'secure' => false,
                'httponly' => true,
                'samesite' => 'Lax'
            ]
        );
        
        if (!$cookieSet) {
            log_message('error', 'Failed to set remember me cookie');
        }
        
        return $cookieSet;
    }
    */

    /**
     * Cek cookie "Ingat Saya" - DISABLED
     */
    /*
    private function checkRememberMe()
    {
        if (!isset($_COOKIE['remember_me'])) {
            return false;
        }
        
        $cookieValue = $_COOKIE['remember_me'];
        
        // Parse cookie value
        $parts = explode(':', $cookieValue);
        
        if (count($parts) !== 2) {
            return false;
        }
        
        list($selector, $token) = $parts;
        
        $db = \Config\Database::connect();
        
        // Cari token di database
        $tokenData = $db->table('user_remember_tokens')
                       ->where('selector', $selector)
                       ->where('expires_at >', date('Y-m-d H:i:s'))
                       ->get()
                       ->getRowArray();
        
        if (!$tokenData) {
            return false;
        }
        
        // Verify token hash
        $tokenHash = hash('sha256', $token);
        if (!hash_equals($tokenData['token_hash'], $tokenHash)) {
            return false;
        }
        
        // Get user data
        $user = $this->userModel->find($tokenData['user_id']);
        
        if (!$user || $user['status'] !== 'active') {
            return false;
        }
        
        return $user;
    }
    */

    /**
     * Hapus cookie "Ingat Saya" - DISABLED
     */
    /*
    private function deleteRememberMeCookie()
    {
        if (isset($_COOKIE['remember_me'])) {
            setcookie('remember_me', '', time() - 3600, '/');
            unset($_COOKIE['remember_me']);
        }
    }
    */

    /**
     * Set user session dari data user
     */
    private function setUserSession($user)
    {
        $userData = [
            'isLoggedIn' => true,
            'user_id' => $user['id'],
            'username' => $user['username'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'karyawan_id' => $user['karyawan_id'] ?? null,
            'login_time' => time(),
            'login_ip' => $this->request->getIPAddress(),
            'remembered' => true
        ];

        session()->set($userData);
        
        // Update last login
        $this->userModel->update($user['id'], [
            'last_login' => date('Y-m-d H:i:s'),
            'login_ip' => $this->request->getIPAddress()
        ]);
    }

    /**
     * Redirect ke dashboard berdasarkan role
     */
    private function redirectToDashboard($role = null)
    {
        $role = $role ?? session()->get('role');
        
        if (!$role) {
            return redirect()->to(base_url('login'))->with('error', 'Role tidak ditemukan!');
        }
        
        // Normalize role
        $roleLower = strtolower(trim($role));
        
        switch ($roleLower) {
            case 'admin':
            case 'hrd':
                return redirect()->to(base_url('admin'))->with('success', 'Login berhasil! Selamat datang ' . session()->get('name'));
            
            case 'teknisi':
                return redirect()->to(base_url('teknisi'))->with('success', 'Login berhasil! Selamat datang ' . session()->get('name'));
            
            case 'direktur':
                return redirect()->to(base_url('direktur'))->with('success', 'Login berhasil! Selamat datang ' . session()->get('name'));
            
            case 'accounting':
                return redirect()->to(base_url('accounting'))->with('success', 'Login berhasil! Selamat datang ' . session()->get('name'));
            
            case 'sales':
            case 'marketing':
                return redirect()->to(base_url('sales'))->with('success', 'Login berhasil! Selamat datang ' . session()->get('name'));
            
            case 'staff':
            default:
                return redirect()->to(base_url('staff'))->with('success', 'Login berhasil! Selamat datang ' . session()->get('name'));
        }
    }

    /**
     * Logout user
     */
    public function logout()
    {
        $userId = session()->get('user_id');
        $username = session()->get('username');
        
        log_message('info', "User logout: {$username} (ID: {$userId})");
        
        // Hapus semua token remember me untuk user ini dari database
        if ($userId) {
            $db = \Config\Database::connect();
            $db->table('user_remember_tokens')
               ->where('user_id', $userId)
               ->delete();
        }
        
        // Destroy session
        session()->destroy();
        
        return redirect()->to(base_url('login'))->with('success', 'Anda telah logout!');
    }

    /**
     * Forgot password page
     */
    public function forgotPassword()
    {
        $data = [
            'title' => 'Lupa Password - CDW Engineering',
            'active' => 'login'
        ];
        
        return view('auth/forgot_password', $data);
    }

    /**
     * Send password reset link
     */
    public function sendResetLink()
    {
        $rules = [
            'email' => 'required|valid_email'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        
        // Check if email exists in database
        $user = $this->userModel->findByEmail($email);

        if ($user) {
            // Generate reset token
            $resetToken = bin2hex(random_bytes(32));
            
            log_message('info', "Password reset requested for email: {$email}");
            
            return redirect()->to(base_url('login'))->with('success', 'Instruksi reset password telah dikirim ke email Anda.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Email tidak ditemukan!');
        }
    }
    
    /**
     * Reset password page
     */
    public function resetPassword($token = null)
    {
        if (!$token) {
            return redirect()->to(base_url('login'))->with('error', 'Token reset tidak valid!');
        }
        
        $data = [
            'title' => 'Reset Password - CDW Engineering',
            'token' => $token
        ];
        
        return view('auth/reset_password', $data);
    }
    
    /**
     * Update password
     */
    public function updatePassword()
    {
        $rules = [
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        return redirect()->to(base_url('login'))->with('success', 'Password berhasil direset!');
    }
    
    /**
     * Check if user is logged in
     */
    public function isLoggedIn()
    {
        return session()->get('isLoggedIn') === true;
    }
    
    /**
     * Get current user data
     */
    public function getCurrentUser()
    {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return [
            'id' => session()->get('user_id'),
            'username' => session()->get('username'),
            'name' => session()->get('name'),
            'email' => session()->get('email'),
            'role' => session()->get('role'),
            'karyawan_id' => session()->get('karyawan_id')
        ];
    }
}