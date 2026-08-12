<?php

namespace App\Controllers\Hrd;

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
            'hrd' => count(array_filter($users, function($u) { return strtolower($u['role']) == 'hrd'; })),
            'staff' => count(array_filter($users, function($u) { return strtolower($u['role']) == 'staff'; }))
        ];
        
        $data = [
            'title' => 'Manajemen Akun',
            'active' => 'akun',
            'users' => $users,
            'statistik' => $statistik
        ];
        
        return view('hrd/akun/index', $data);
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
        
        return view('hrd/akun/create', $data);
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
                return redirect()->to('/hrd/karyawan/akun')->with('success', 'Akun berhasil dibuat');
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
            return redirect()->to('/hrd/karyawan/akun')->with('error', 'Akun tidak ditemukan');
        }
        
        $data = [
            'title' => 'Detail Akun',
            'active' => 'akun',
            'user' => $user
        ];
        
        return view('hrd/akun/show', $data);
    }
    
    public function edit($id)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to('/hrd/karyawan/akun')->with('error', 'Akun tidak ditemukan');
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
        
        return view('hrd/akun/edit', $data);
    }
    
    public function update($id)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to('/hrd/karyawan/akun')->with('error', 'Akun tidak ditemukan');
        }
        
        $this->userModel->skipValidation(true);
        
        $role = $this->request->getVar('role');
        if ($role === 'custom') {
            $customRole = $this->request->getVar('custom_role');
            $role = !empty($customRole) ? trim($customRole) : $user['role'];
        }
        
        $data = [
            'username' => $this->request->getVar('username'),
            'email' => $this->request->getVar('email'),
            'name' => $this->request->getVar('name'),
            'role' => $role,
            'status' => $this->request->getVar('status')
        ];
        
        $password = $this->request->getVar('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
            $data['password_changed_at'] = date('Y-m-d H:i:s');
        }
        
        try {
            if ($this->userModel->update($id, $data)) {
                if (session()->get('user_id') == $id) {
                    session()->set([
                        'role' => $role,
                        'name' => $data['name'],
                        'username' => $data['username'],
                        'email' => $data['email']
                    ]);
                }
                return redirect()->to('/hrd/karyawan/akun')->with('success', 'Akun berhasil diperbarui');
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
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/hrd/karyawan/akun')->with('error', 'Akun tidak ditemukan');
        }
        
        $session = session();
        $loggedInUser = $session->get('user') ?? $session->get('logged_in');
        if ($loggedInUser && isset($loggedInUser['id']) && $loggedInUser['id'] == $id) {
            return redirect()->to('/hrd/karyawan/akun')->with('error', 'Tidak dapat menghapus akun yang sedang login');
        }
        
        try {
            if ($this->userModel->delete($id)) {
                return redirect()->to('/hrd/karyawan/akun')->with('success', 'Akun berhasil dihapus');
            } else {
                return redirect()->back()->with('error', 'Gagal menghapus akun');
            }
        } catch (\Exception $e) {
            log_message('error', 'Delete user error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
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
        
        $defaultRoles = ['admin', 'hrd', 'manager', 'staff', 'teknisi', 'sales', 'accounting', 'direktur', 'software_engineer'];
        foreach ($defaultRoles as $role) {
            if (!in_array($role, $roleArray)) {
                $roleArray[] = $role;
            }
        }
        
        return array_unique($roleArray);
    }
}
