<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    protected $table = 'client';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $allowedFields = [
        'kode_client',
        'nama_perusahaan',
        'nama_kontak',
        'telepon',
        'email',
        'alamat',
        'npwp',
        'kategori',
        'status',
        'sales_id',
        'created_at',
        'updated_at'
    ];
    
    // PERBAIKAN: Rules yang benar
protected $validationRules = [
    'kode_client' => 'required|is_unique[client.kode_client,id,{id}]|max_length[50]',
    'nama_perusahaan' => 'required|max_length[200]',
    'nama_kontak' => 'permit_empty|max_length[100]',
    'telepon' => 'permit_empty|max_length[20]',
    'email' => 'permit_empty|valid_email|is_unique[client.email,id,{id}]|max_length[100]',
    'alamat' => 'permit_empty',
    'npwp' => 'permit_empty|max_length[25]',
    'kategori' => 'permit_empty|in_list[perusahaan,pemerintah,perorangan]',
    'status' => 'permit_empty|in_list[active,inactive,potensial]',
    'sales_id' => 'permit_empty|is_not_unique[karyawan.id]'
];
    
    protected $validationMessages = [
        'kode_client' => [
            'required' => 'Kode Client wajib diisi',
            'is_unique' => 'Kode Client sudah digunakan',
            'max_length' => 'Kode Client maksimal 50 karakter'
        ],
        'nama_perusahaan' => [
            'required' => 'Nama perusahaan wajib diisi',
            'max_length' => 'Nama perusahaan maksimal 200 karakter'
        ],
        'email' => [
            'valid_email' => 'Format email tidak valid'
        ],
        'kategori' => [
            'in_list' => 'Kategori harus salah satu dari: perusahaan, pemerintah, perorangan'
        ],
        'status' => [
            'in_list' => 'Status harus salah satu dari: active, inactive, potensial'
        ]
    ];
    
    // Set skipValidation ke false
    protected $skipValidation = false;
    
    // Generate kode client otomatis - VERSI SIMPLE
    public function generateKodeClient()
    {
        $prefix = 'CL';
        $year = date('Y');
        $month = date('m');
        
        // Cari kode terakhir tahun ini
        $lastCode = $this->db->table($this->table)
            ->select('kode_client')
            ->like('kode_client', $prefix . $year . $month, 'after')
            ->orderBy('kode_client', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();
        
        if ($lastCode && !empty($lastCode['kode_client'])) {
            $code = $lastCode['kode_client'];
            // Ambil 4 digit terakhir
            $lastNumber = intval(substr($code, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $year . $month . sprintf('%04d', $newNumber);
    }
    
    // Get clients by sales_id
    public function getClientsBySales($salesId)
    {
        return $this->where('sales_id', $salesId)
            ->orderBy('nama_perusahaan', 'ASC')
            ->findAll();
    }
    
    // Get client dengan data sales (karyawan)
    public function getClientWithSales($id)
    {
        $builder = $this->db->table('client c');
        $builder->select('c.*, k.nama_lengkap as nama_sales')
                ->join('karyawan k', 'k.id = c.sales_id', 'left')
                ->where('c.id', $id);
        
        return $builder->get()->getRowArray();
    }
    
    // Get all clients with sales info
    public function getAllClientsWithSales()
    {
        $builder = $this->db->table('client c');
        $builder->select('c.*, k.nama_lengkap as nama_sales')
                ->join('karyawan k', 'k.id = c.sales_id', 'left')
                ->orderBy('c.nama_perusahaan', 'ASC');
        
        return $builder->get()->getResultArray();
    }
}