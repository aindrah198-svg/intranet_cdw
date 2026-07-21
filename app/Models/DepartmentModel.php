<?php

namespace App\Models;

use CodeIgniter\Model;

class DepartmentModel extends Model
{
    protected $table = 'departments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'kode', 'nama', 'deskripsi', 'kepala_department', 'status'
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'kode' => 'required|is_unique[departments.kode,id,{id}]',
        'nama' => 'required'
    ];
    
    protected $validationMessages = [
        'kode' => [
            'required' => 'Kode department harus diisi',
            'is_unique' => 'Kode department sudah digunakan'
        ],
        'nama' => [
            'required' => 'Nama department harus diisi'
        ]
    ];
    
    protected $skipValidation = false;
    
    /**
     * Get active departments
     */
    public function getActiveDepartments()
    {
        return $this->where('status', 'active')
                    ->orderBy('nama', 'ASC')
                    ->findAll();
    }
    
    /**
     * Get department with employee count
     */
    public function getDepartmentsWithCount()
    {
        $db = \Config\Database::connect();
        
        return $db->table('departments')
            ->select('departments.*, COUNT(karyawan.id) as total_karyawan')
            ->join('karyawan', 'karyawan.department_id = departments.id AND karyawan.status = "active"', 'left')
            ->groupBy('departments.id')
            ->orderBy('departments.nama', 'ASC')
            ->get()
            ->getResultArray();
    }
}