<?php

namespace App\Models\SoftwareEngineer;

use CodeIgniter\Model;

class SeDocModel extends Model
{
    protected $table            = 'se_technical_docs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'system_id',
        'kategori',
        'judul',
        'versi_doc',
        'content',
        'link_file',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getDocsWithSystem($kategori = null)
    {
        $builder = $this->select('se_technical_docs.*, s.nama_sistem, s.kode_sistem')
                        ->join('se_systems s', 's.id = se_technical_docs.system_id', 'left');
        
        if ($kategori) {
            $builder->where('se_technical_docs.kategori', $kategori);
        }

        return $builder->orderBy('se_technical_docs.updated_at', 'DESC')->findAll();
    }
}
