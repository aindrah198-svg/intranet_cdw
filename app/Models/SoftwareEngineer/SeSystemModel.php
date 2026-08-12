<?php

namespace App\Models\SoftwareEngineer;

use CodeIgniter\Model;

class SeSystemModel extends Model
{
    protected $table            = 'se_systems';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_sistem',
        'kode_sistem',
        'jenis',
        'tech_stack',
        'status',
        'link_production',
        'link_repository',
        'deskripsi',
        'client_id',
        'pic_internal',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getSystemWithHosting()
    {
        return $this->select('se_systems.*, h.nama_provider_hosting, h.nama_domain, h.tgl_expired_hosting, h.tgl_expired_domain, h.tgl_expired_ssl')
                    ->join('se_hosting_domain h', 'h.system_id = se_systems.id', 'left')
                    ->findAll();
    }
}
