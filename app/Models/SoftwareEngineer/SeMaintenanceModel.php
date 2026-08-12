<?php

namespace App\Models\SoftwareEngineer;

use CodeIgniter\Model;

class SeMaintenanceModel extends Model
{
    protected $table            = 'se_maintenance_schedule';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'system_id',
        'judul_maintenance',
        'jenis_maintenance',
        'tgl_rencana',
        'estimasi_downtime',
        'status',
        'penanggung_jawab',
        'catatan',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getSchedulesWithSystem()
    {
        return $this->select('se_maintenance_schedule.*, s.nama_sistem, s.kode_sistem')
                    ->join('se_systems s', 's.id = se_maintenance_schedule.system_id', 'left')
                    ->orderBy('se_maintenance_schedule.tgl_rencana', 'ASC')
                    ->findAll();
    }
}
