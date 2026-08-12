<?php

namespace App\Models\SoftwareEngineer;

use CodeIgniter\Model;

class SeDeploymentModel extends Model
{
    protected $table            = 'se_deployments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'system_id',
        'versi',
        'tanggal_deploy',
        'perubahan',
        'deployed_by',
        'environment',
        'status_deploy',
        'catatan',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getDeploymentsWithSystem()
    {
        return $this->select('se_deployments.*, s.nama_sistem, s.kode_sistem')
                    ->join('se_systems s', 's.id = se_deployments.system_id', 'left')
                    ->orderBy('se_deployments.tanggal_deploy', 'DESC')
                    ->findAll();
    }
}
