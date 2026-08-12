<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanHarianModel extends Model
{
    protected $table = 'laporan_harian';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $allowedFields = [
        'karyawan_id',
        'tanggal',
        'judul',
        'deskripsi',
        'lampiran',
        'status',
        'komentar_direktur',
        'direview_oleh'
    ];
    
    public function getLaporanWithKaryawan($filters = [])
    {
        $builder = $this->select('laporan_harian.*, karyawan.nama_lengkap, karyawan.jabatan, karyawan.divisi')
                        ->join('karyawan', 'karyawan.id = laporan_harian.karyawan_id', 'left');
                        
        if (!empty($filters['karyawan_id'])) {
            $builder->where('laporan_harian.karyawan_id', $filters['karyawan_id']);
        }
        
        if (!empty($filters['tanggal'])) {
            $builder->where('laporan_harian.tanggal', $filters['tanggal']);
        }
        
        if (!empty($filters['status'])) {
            $builder->where('laporan_harian.status', $filters['status']);
        }
        
        return $builder->orderBy('laporan_harian.tanggal', 'DESC')
                       ->orderBy('laporan_harian.created_at', 'DESC')
                       ->findAll();
    }
}
