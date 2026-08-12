<?php

namespace App\Models\SoftwareEngineer;

use CodeIgniter\Model;

class SeBugModel extends Model
{
    protected $table            = 'se_bugs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'system_id',
        'judul_bug',
        'deskripsi',
        'severity',
        'status',
        'reporter',
        'assigned_to',
        'tgl_ditemukan',
        'tgl_diselesaikan',
        'solusi',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getBugsWithSystem()
    {
        return $this->select('se_bugs.*, s.nama_sistem, s.kode_sistem')
                    ->join('se_systems s', 's.id = se_bugs.system_id', 'left')
                    ->orderBy('se_bugs.created_at', 'DESC')
                    ->findAll();
    }
}
