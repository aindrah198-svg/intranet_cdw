<?php

namespace App\Models;

use CodeIgniter\Model;

class ProyekTimelineModel extends Model
{
    protected $table = 'proyek_timeline';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $allowedFields = [
        'proyek_id',
        'nama_tugas',
        'deskripsi',
        'karyawan_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'status'
    ];
    
    public function getTimelineByProyek($proyek_id)
    {
        return $this->select('proyek_timeline.*, COALESCE(NULLIF(users.name, ""), users.username) as ditugaskan_kepada')
                    ->join('users', 'users.id = proyek_timeline.karyawan_id', 'left')
                    ->where('proyek_id', $proyek_id)
                    ->orderBy('tanggal_mulai', 'ASC')
                    ->findAll();
    }
}
