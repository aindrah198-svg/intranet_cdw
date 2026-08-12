<?php

namespace App\Models;

use CodeIgniter\Model;

class PencarianBarangModel extends Model
{
    protected $table = 'pencarian_barang';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $allowedFields = [
        'judul',
        'deskripsi',
        'tanggal_mulai',
        'jam_mulai',
        'batas_waktu',
        'jam_deadline',
        'tipe_pembelian',
        'nama_toko_marketplace',
        'nominal_estimasi',
        'karyawan_id',
        'status',
        'hasil_pencarian',
        'lampiran_hasil',
        'is_approved_keuangan',
        'pembelian_id',
        'dibuat_oleh'
    ];
    
    public function getPenugasanWithDetails($filters = [])
    {
        $builder = $this->select('pencarian_barang.*, COALESCE(NULLIF(karyawan.nama_lengkap, ""), NULLIF(u_karyawan.name, ""), u_karyawan.username) as ditugaskan_kepada, COALESCE(NULLIF(pembuat.nama_lengkap, ""), NULLIF(pembuat_u.name, ""), pembuat_u.username) as pembuat_tugas')
                        ->join('karyawan', 'karyawan.id = pencarian_barang.karyawan_id', 'left')
                        ->join('users as u_karyawan', 'u_karyawan.id = pencarian_barang.karyawan_id', 'left')
                        ->join('users as pembuat_u', 'pembuat_u.id = pencarian_barang.dibuat_oleh', 'left')
                        ->join('karyawan as pembuat', 'pembuat.id = pembuat_u.karyawan_id', 'left');
                        
        if (!empty($filters['status'])) {
            $builder->where('pencarian_barang.status', $filters['status']);
        }
        
        if (!empty($filters['karyawan_id'])) {
            $builder->where('pencarian_barang.karyawan_id', $filters['karyawan_id']);
        }
        
        return $builder->orderBy('pencarian_barang.created_at', 'DESC')
                       ->findAll();
    }
}
