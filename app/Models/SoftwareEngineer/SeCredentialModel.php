<?php

namespace App\Models\SoftwareEngineer;

use CodeIgniter\Model;

class SeCredentialModel extends Model
{
    protected $table            = 'se_credentials';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'system_id',
        'tipe_akses',
        'username_akses',
        'encrypted_password',
        'admin_pic',
        'url_login',
        'tgl_terakhir_ganti_password',
        'catatan_keamanan',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getCredentialsWithSystem()
    {
        return $this->select('se_credentials.*, s.nama_sistem, s.kode_sistem, s.jenis')
                    ->join('se_systems s', 's.id = se_credentials.system_id', 'left')
                    ->findAll();
    }
}
