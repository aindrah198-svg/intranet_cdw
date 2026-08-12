<?php

namespace App\Models\SoftwareEngineer;

use CodeIgniter\Model;

class SeHostingDomainModel extends Model
{
    protected $table            = 'se_hosting_domain';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'system_id',
        'nama_provider_hosting',
        'nama_domain',
        'tgl_expired_hosting',
        'tgl_expired_domain',
        'tgl_expired_ssl',
        'paket_hosting',
        'biaya_per_tahun',
        'catatan',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getExpiringAlerts($days = 30)
    {
        $targetDate = date('Y-m-d', strtotime("+{$days} days"));
        $today = date('Y-m-d');

        return $this->select('se_hosting_domain.*, s.nama_sistem, s.kode_sistem, s.jenis')
                    ->join('se_systems s', 's.id = se_hosting_domain.system_id', 'left')
                    ->groupStart()
                        ->where("tgl_expired_hosting <= '{$targetDate}' AND tgl_expired_hosting >= '{$today}'")
                        ->orWhere("tgl_expired_domain <= '{$targetDate}' AND tgl_expired_domain >= '{$today}'")
                        ->orWhere("tgl_expired_ssl <= '{$targetDate}' AND tgl_expired_ssl >= '{$today}'")
                    ->groupEnd()
                    ->findAll();
    }
}
