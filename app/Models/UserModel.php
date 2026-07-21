<?php
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    
  
    protected $allowedFields = [
        'karyawan_id',
        'username', 
        'password', 
        'name', 
        'email', 
        'role',
        'status',
        'last_login',
        'password_changed_at',
        'deleted_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    
protected $validationRules = [
    'username' => [
        'rules' => 'required|min_length[3]|max_length[50]|is_unique[users.username,id,{id}]',
        'errors' => [
            'is_unique' => 'Username sudah digunakan oleh akun lain.'
        ]
    ],
    'password' => [
        'rules' => 'permit_empty|min_length[6]',
        'errors' => [
            'min_length' => 'Password minimal 6 karakter.'
        ]
    ],
    'name' => [
        'rules' => 'required|min_length[3]|max_length[100]',
        'errors' => [
            'required' => 'Nama wajib diisi.',
            'min_length' => 'Nama minimal 3 karakter.'
        ]
    ],
    'email' => [
        'rules' => 'required|valid_email|is_unique[users.email,id,{id}]',
        'errors' => [
            'is_unique' => 'Email sudah digunakan oleh akun lain.',
            'valid_email' => 'Email tidak valid.'
        ]
    ],
    'role' => [
        'rules' => 'required|max_length[50]',
        'errors' => [
            'required' => 'Role wajib diisi.'
        ]
    ],
    'status' => [
        'rules' => 'permit_empty|in_list[active,inactive,suspended]',
        'errors' => [
            'in_list' => 'Status tidak valid.'
        ]
    ]
];

  protected $beforeDelete = ['checkSoftDeleteRelations'];
    
    // Tambah method untuk cek relations sebelum soft delete
protected function checkSoftDeleteRelations(array $data)
{
    if (isset($data['id'])) {
        $userId = $data['id'][0] ?? $data['id'];
        
        // Cek jika user terkait dengan data penting
        $hasPenawaran = $this->db->table('penawaran')
            ->where('created_by', $userId)
            ->orWhere('approved_by', $userId)
            ->orWhere('signed_by', $userId)
            ->countAllResults();
            
        if ($hasPenawaran > 0) {
            // Cancel delete jika ada relasi
            return false;
        }
    }
    return $data;
}
    
    protected $validationMessages = [];
    protected $skipValidation = false;
    
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];
    
    /**
     * Hash password sebelum insert/update
     */
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password']) && !empty($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
            $data['data']['password_changed_at'] = date('Y-m-d H:i:s');
        } else {
            unset($data['data']['password']);
        }
        
        return $data;
    }
    
    /**
     * Get all users with karyawan data
     */
    public function getAllUsersWithKaryawan()
    {
        $this->select('users.*, karyawan.nik, karyawan.nama_lengkap, karyawan.jabatan, karyawan.departemen, karyawan.email as karyawan_email');
        $this->join('karyawan', 'karyawan.id = users.karyawan_id', 'left');
        $this->where('users.deleted_at', null);
        $this->orderBy('users.created_at', 'DESC');
        
        return $this->findAll();
    }
    
    /**
     * Get user with karyawan data
     */
    public function getUserWithKaryawan($id)
    {
        $this->select('users.*, karyawan.nik, karyawan.nama_lengkap, karyawan.jabatan, karyawan.departemen, karyawan.email as karyawan_email, karyawan.telepon');
        $this->join('karyawan', 'karyawan.id = users.karyawan_id', 'left');
        $this->where('users.id', $id);
        
        return $this->first();
    }
    
    /**
     * Get users statistics
     */
    public function getStatistik()
    {
        $statistik = [
            'total' => $this->where('deleted_at', null)->countAllResults(),
            'active' => $this->where('status', 'active')->where('deleted_at', null)->countAllResults(),
            'inactive' => $this->where('status', 'inactive')->where('deleted_at', null)->countAllResults(),
            'suspended' => $this->where('status', 'suspended')->where('deleted_at', null)->countAllResults(),
        ];
        
        // Hitung per role
        $roles = $this->distinct()->select('role')->where('deleted_at', null)->findAll();
        foreach ($roles as $role) {
            $roleName = strtolower($role['role']);
            $statistik[$roleName] = $this->where('role', $role['role'])
                                         ->where('deleted_at', null)
                                         ->countAllResults();
        }
        
        return $statistik;
    }
    
    /**
     * Get all distinct roles
     */
    public function getAllRoles()
    {
        return $this->distinct()
                    ->select('role')
                    ->where('deleted_at', null)
                    ->orderBy('role', 'ASC')
                    ->findAll();
    }
    
  /**
 * Verify login with status check - PERBAIKAN
 */
public function verifyLogin($identifier, $password)
{
    log_message('debug', "UserModel: Searching for user with identifier: {$identifier}");
    
    // Cari user berdasarkan username ATAU email (case-insensitive)
    $user = $this->where('deleted_at', null)
                 ->groupStart()
                    ->where('username', $identifier)
                    ->orWhere('email', $identifier)
                 ->groupEnd()
                 ->first();
    
    if (!$user) {
        log_message('debug', "UserModel: User NOT FOUND in database");
        return false;
    }
    
    log_message('debug', "UserModel: User found:");
    log_message('debug', "- ID: {$user['id']}");
    log_message('debug', "- Username: {$user['username']}");
    log_message('debug', "- Status: {$user['status']}");
    
    // Cek status user
    if ($user['status'] !== 'active') {
        log_message('debug', "UserModel: User is not active. Status: {$user['status']}");
        return false;
    }
    
    // Debug password
    $passwordHash = $user['password'] ?? '';
    $passwordLength = strlen($password);
    $hashLength = strlen($passwordHash);
    
    log_message('debug', "UserModel: Password verification:");
    log_message('debug', "- Input password length: {$passwordLength}");
    log_message('debug', "- Stored hash length: {$hashLength}");
    log_message('debug', "- Hash preview: " . substr($passwordHash, 0, 20) . "...");
    
    // Verify password
    if (!password_verify($password, $passwordHash)) {
        log_message('debug', "UserModel: Password verification FAILED");
        return false;
    }
    
    log_message('debug', "UserModel: Password verification SUCCESS");
    
    // Update last login
    $this->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);
    
    return $user;
}

// Tambahkan method untuk mendapatkan role yang sering digunakan
public function getCommonRoles()
{
    $commonRoles = ['admin', 'manager', 'staff', 'teknisi', 'supervisor', 'koordinator'];
    return $commonRoles;
}

    /**
     * Cari user by username (exclude deleted)
     */
    public function findByUsername($username)
    {
        return $this->where('username', $username)
                    ->where('deleted_at', null)
                    ->first();
    }
    
    /**
     * Cari user by email (exclude deleted)
     */
    public function findByEmail($email)
    {
        return $this->where('email', $email)
                    ->where('deleted_at', null)
                    ->first();
    }
    
 
    
    /**
     * Get all active users (not deleted)
     */
    public function getAllActiveUsers()
    {
        return $this->where('deleted_at', null)
                    ->orderBy('role', 'asc')
                    ->orderBy('name', 'asc')
                    ->findAll();
    }
    
    /**
     * Get users by role
     */
    public function getUsersByRole($role)
    {
        return $this->where('role', $role)
                    ->where('deleted_at', null)
                    ->orderBy('name', 'asc')
                    ->findAll();
    }
    
    /**
     * Soft delete user
     */
    public function softDelete($id)
    {
        $data = [
            'deleted_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->update($id, $data);
    }
    
    /**
     * Restore soft deleted user
     */
    public function restore($id)
    {
        $data = [
            'deleted_at' => null
        ];
        
        return $this->update($id, $data);
    }
    
   
    
    
    
    /**
     * Get user by karyawan ID
     */
    public function getByKaryawanId($karyawanId)
    {
        return $this->where('karyawan_id', $karyawanId)
                    ->where('deleted_at', null)
                    ->first();
    }
    
    /**
     * Get active users count
     */
    public function getActiveUsersCount()
    {
        return $this->where('status', 'active')
                    ->where('deleted_at', null)
                    ->countAllResults();
    }
    
    /**
     * Reset password user
     */
    public function resetPassword($id, $newPassword)
    {
        $data = [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
            'password_changed_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->update($id, $data);
    }
    
    /**
     * Update user status
     */
    public function updateStatus($id, $status)
    {
        return $this->update($id, ['status' => $status]);
    }

    /**
 * Get karyawan data by user ID
 */
public function getKaryawanByUserId($user_id)
{
    $db = \Config\Database::connect();
    
    return $db->table('users')
              ->select('karyawan.*, users.id as user_id')
              ->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
              ->where('users.id', $user_id)
              ->where('users.deleted_at', null)
              ->get()
              ->getRowArray();
}
    
    
}