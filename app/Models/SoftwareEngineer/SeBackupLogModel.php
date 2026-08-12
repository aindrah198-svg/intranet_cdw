<?php

namespace App\Models\SoftwareEngineer;

use CodeIgniter\Model;

class SeBackupLogModel extends Model
{
    protected $table            = 'se_backup_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'system_id',
        'jenis_backup',
        'tanggal_backup',
        'ukuran_mb',
        'lokasi_simpan',
        'status_backup',
        'petugas',
        'catatan',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getLogsWithSystem()
    {
        return $this->select('se_backup_logs.*, s.nama_sistem, s.kode_sistem')
                    ->join('se_systems s', 's.id = se_backup_logs.system_id', 'left')
                    ->orderBy('se_backup_logs.tanggal_backup', 'DESC')
                    ->findAll();
    }
}
