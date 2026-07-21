<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\KaryawanModel;

class Akun extends BaseController
{
    protected $userModel;
    protected $karyawanModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->karyawanModel = new KaryawanModel();
    }
    
    public function index()
    {
        $users = $this->userModel->getAllUsersWithKaryawan();
        
        $statistik = [
            'total' => count($users),
            'active' => count(array_filter($users, function($u) { return $u['status'] == 'active'; })),
            'inactive' => count(array_filter($users, function($u) { return $u['status'] == 'inactive'; })),
            'suspended' => count(array_filter($users, function($u) { return $u['status'] == 'suspended'; })),
            'admin' => count(array_filter($users, function($u) { return strtolower($u['role']) == 'admin'; })),
            'manager' => count(array_filter($users, function($u) { return strtolower($u['role']) == 'manager'; })),
            'staff' => count(array_filter($users, function($u) { return strtolower($u['role']) == 'staff'; }))
        ];
        
        $data = [
            'title' => 'Manajemen Akun',
            'active' => 'akun',
            'users' => $users,
            'statistik' => $statistik
        ];
        
        return view('admin/akun/index', $data);
    }
    
    public function create()
    {
        $karyawanBelumAkun = $this->getKaryawanBelumAkunData();
        $existingRoles = $this->getExistingRoles();
        
        $data = [
            'title' => 'Buat Akun Baru',
            'active' => 'akun',
            'karyawanBelumAkun' => $karyawanBelumAkun,
            'existingRoles' => $existingRoles
        ];
        
        return view('admin/akun/create', $data);
    }
    
   public function store()
{
    $validation = \Config\Services::validation();
    
    $rules = [
        'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
        'email' => 'required|valid_email|is_unique[users.email]',
        'name' => 'required|min_length[3]|max_length[100]',
        'password' => 'required|min_length[6]|matches[password_confirmation]',
        'password_confirmation' => 'required|matches[password]',
        'role' => 'required'
    ];
    
    if (!$this->validate($rules)) {
        return redirect()->back()->withInput()->with('errors', $validation->getErrors());
    }
    
    // Handle custom role
    $role = $this->request->getVar('role');
    if ($role === 'custom') {
        $customRole = $this->request->getVar('custom_role');
        $role = !empty($customRole) ? trim($customRole) : 'staff';
    }
    
    $data = [
        'karyawan_id' => $this->request->getVar('karyawan_id'),
        'username' => $this->request->getVar('username'),
        'email' => $this->request->getVar('email'),
        'name' => $this->request->getVar('name'),
        'role' => $role,
        'status' => $this->request->getVar('status') ?? 'active',
        'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT)
    ];
    
    try {
        if ($this->userModel->save($data)) {
            return redirect()->to('/admin/karyawan/akun')->with('success', 'Akun berhasil dibuat');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal membuat akun');
        }
    } catch (\Exception $e) {
        log_message('error', 'Create user error: ' . $e->getMessage());
        return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}
    
    public function show($id)
    {
        $user = $this->userModel->getUserWithKaryawan($id);
        
        if (!$user) {
            return redirect()->to('/admin/karyawan/akun')->with('error', 'Akun tidak ditemukan');
        }
        
        $data = [
            'title' => 'Detail Akun',
            'active' => 'akun',
            'user' => $user
        ];
        
        return view('admin/akun/show', $data);
    }
    
    public function edit($id)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to('/admin/karyawan/akun')->with('error', 'Akun tidak ditemukan');
        }
        
        $karyawan = null;
        if ($user['karyawan_id']) {
            $karyawan = $this->karyawanModel->find($user['karyawan_id']);
        }
        
        $existingRoles = $this->getExistingRoles();
        
        $data = [
            'title' => 'Edit Akun',
            'active' => 'akun',
            'user' => $user,
            'karyawan' => $karyawan,
            'existingRoles' => $existingRoles
        ];
        
        return view('admin/akun/edit', $data);
    }
    
 public function update($id)
{
    $user = $this->userModel->find($id);
    
    if (!$user) {
        return redirect()->to('/admin/karyawan/akun')->with('error', 'Akun tidak ditemukan');
    }
    
    // Nonaktifkan validasi model
    $this->userModel->skipValidation(true);
    
    // Handle custom role
    $role = $this->request->getVar('role');
    if ($role === 'custom') {
        $customRole = $this->request->getVar('custom_role');
        $role = !empty($customRole) ? trim($customRole) : $user['role'];
    }
    
    // Siapkan data untuk update
    $data = [
        'username' => $this->request->getVar('username'),
        'email' => $this->request->getVar('email'),
        'name' => $this->request->getVar('name'),
        'role' => $role,
        'status' => $this->request->getVar('status')
    ];
    
    // Jika password diisi, hash password
    $password = $this->request->getVar('password');
    if (!empty($password)) {
        $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        $data['password_changed_at'] = date('Y-m-d H:i:s');
    }
    
    try {
        if ($this->userModel->update($id, $data)) {
            return redirect()->to('/admin/karyawan/akun')->with('success', 'Akun berhasil diperbarui');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui akun');
        }
    } catch (\Exception $e) {
        log_message('error', 'Update user error: ' . $e->getMessage());
        return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}
    
   public function delete($id)
{
    // Hapus pengecekan method di awal untuk testing
    // if ($this->request->getMethod() !== 'post') {
    //     return redirect()->to('/admin/karyawan/akun')->with('error', 'Metode tidak diizinkan');
    // }
    
    // Untuk debugging, cek method yang digunakan
    log_message('info', 'Delete method: ' . $this->request->getMethod());
    log_message('info', 'Delete ID: ' . $id);
    
    $user = $this->userModel->find($id);
    
    if (!$user) {
        return redirect()->to('/admin/karyawan/akun')->with('error', 'Akun tidak ditemukan');
    }
    
    // Cek apakah user sedang login sendiri
    $session = session();
    $loggedInUser = $session->get('user') ?? $session->get('logged_in');
    if ($loggedInUser && isset($loggedInUser['id']) && $loggedInUser['id'] == $id) {
        return redirect()->to('/admin/karyawan/akun')->with('error', 'Tidak dapat menghapus akun yang sedang login');
    }
    
    try {
        // Gunakan soft delete
        if ($this->userModel->delete($id)) {
            return redirect()->to('/admin/karyawan/akun')->with('success', 'Akun berhasil dihapus');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus akun');
        }
    } catch (\Exception $e) {
        log_message('error', 'Delete user error: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}
    
    // Helper methods
    private function getKaryawanBelumAkunData()
    {
        $db = \Config\Database::connect();
        
        $subquery = $db->table('users')
            ->select('karyawan_id')
            ->where('karyawan_id IS NOT NULL')
            ->where('deleted_at', null)
            ->getCompiledSelect();
        
        return $this->karyawanModel->select('karyawan.id, karyawan.nik, karyawan.nama_lengkap, karyawan.jabatan, karyawan.departemen, karyawan.email')
                    ->where("karyawan.id NOT IN ($subquery)", null, false)
                    ->where('karyawan.deleted_at', null)
                    ->where('karyawan.tanggal_keluar', null)
                    ->orderBy('karyawan.nama_lengkap', 'ASC')
                    ->findAll();
    }
    
    private function getExistingRoles()
    {
        $roles = $this->userModel->distinct()
                                ->select('role')
                                ->where('deleted_at', null)
                                ->orderBy('role', 'ASC')
                                ->findAll();
        
        $roleArray = [];
        foreach ($roles as $role) {
            $roleArray[] = $role['role'];
        }
        
        // Tambahkan default roles jika belum ada
        $defaultRoles = ['admin', 'manager', 'staff'];
        foreach ($defaultRoles as $role) {
            if (!in_array($role, $roleArray)) {
                $roleArray[] = $role;
            }
        }
        
        return array_unique($roleArray);
    }

    public function checkUsername($username)
{
    $id = $this->request->getVar('current_id');
    
    $query = $this->userModel->where('username', $username)
                             ->where('deleted_at', null);
    
    if ($id) {
        $query->where('id !=', $id);
    }
    
    $exists = $query->first();
    
    return $this->response->setJSON(['exists' => $exists ? true : false]);
}

public function checkEmail($email)
{
    $id = $this->request->getVar('current_id');
    
    $query = $this->userModel->where('email', $email)
                             ->where('deleted_at', null);
    
    if ($id) {
        $query->where('id !=', $id);
    }
    
    $exists = $query->first();
    
    return $this->response->setJSON(['exists' => $exists ? true : false]);
}

/**
 * Reset password user
 */
public function resetPassword($id)
{
    // Cek method harus POST
    if (!$this->request->isAJAX() && $this->request->getMethod() !== 'post') {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Metode tidak diizinkan'
        ]);
    }
    
    $user = $this->userModel->find($id);
    
    if (!$user) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Akun tidak ditemukan'
        ]);
    }
    
    // Generate password acak
    $newPassword = $this->generateRandomPassword();
    
    try {
        $data = [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
            'password_changed_at' => date('Y-m-d H:i:s')
        ];
        
        // Nonaktifkan validasi sementara
        $this->userModel->skipValidation(true);
        
        if ($this->userModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'newPassword' => $newPassword,
                'message' => 'Password berhasil direset'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mereset password'
            ]);
        }
    } catch (\Exception $e) {
        log_message('error', 'Reset password error: ' . $e->getMessage());
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ]);
    }
}

/**
 * Toggle status user (active/inactive/suspended)
 */
public function toggleStatus($id)
{
    // Cek method harus POST
    if (!$this->request->isAJAX() && $this->request->getMethod() !== 'post') {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Metode tidak diizinkan'
        ]);
    }
    
    $user = $this->userModel->find($id);
    
    if (!$user) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Akun tidak ditemukan'
        ]);
    }
    
    // Tentukan status baru
    $currentStatus = $user['status'];
    $newStatus = '';
    
    switch ($currentStatus) {
        case 'active':
            $newStatus = 'inactive';
            $message = 'Akun berhasil dinonaktifkan';
            break;
        case 'inactive':
            $newStatus = 'active';
            $message = 'Akun berhasil diaktifkan';
            break;
        case 'suspended':
            $newStatus = 'active';
            $message = 'Akun berhasil diaktifkan (dari suspended)';
            break;
        default:
            $newStatus = 'active';
            $message = 'Status akun diperbarui';
    }
    
    try {
        // Cek apakah user sedang login sendiri
        $session = session();
        $loggedInUser = $session->get('user') ?? $session->get('logged_in');
        if ($loggedInUser && isset($loggedInUser['id']) && $loggedInUser['id'] == $id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak dapat mengubah status akun yang sedang login'
            ]);
        }
        
        $data = ['status' => $newStatus];
        
        // Nonaktifkan validasi sementara
        $this->userModel->skipValidation(true);
        
        if ($this->userModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'newStatus' => $newStatus,
                'message' => $message
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengubah status akun'
            ]);
        }
    } catch (\Exception $e) {
        log_message('error', 'Toggle status error: ' . $e->getMessage());
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ]);
    }
}

/**
 * Generate random password
 */
private function generateRandomPassword($length = 8)
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
    $password = '';
    $charsLength = strlen($chars) - 1;
    
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[rand(0, $charsLength)];
    }
    
    return $password;
}

}