<?php

namespace App\Models\SoftwareEngineer;

use CodeIgniter\Model;

class SeCredentialLogModel extends Model
{
    protected $table            = 'se_credential_access_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'credential_id',
        'user_id',
        'username',
        'action',
        'ip_address',
        'user_agent',
        'created_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    public function getLogsWithDetail()
    {
        return $this->select('se_credential_access_logs.*, c.tipe_akses, c.username_akses, s.nama_sistem')
                    ->join('se_credentials c', 'c.id = se_credential_access_logs.credential_id', 'left')
                    ->join('se_systems s', 's.id = c.system_id', 'left')
                    ->orderBy('se_credential_access_logs.created_at', 'DESC')
                    ->findAll();
    }
}
