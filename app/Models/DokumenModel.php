<?php

namespace App\Models;

use CodeIgniter\Model;

class DokumenModel extends Model
{
    protected $table = 'dokumen';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'karyawan_id', 'jenis', 'nama_file', 'path', 'ukuran',
        'nomor_dokumen', 'tanggal_berlaku', 'tanggal_kadaluarsa',
        'keterangan', 'status', 'diupload_oleh'
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    // Validation
    protected $validationRules = [
        'karyawan_id' => 'required|integer',
        'jenis' => 'required|in_list[KTP,KK,IJAZAH,CV,NPWP,BPJS_KES,BPJS_TK,SIM,SERTIFIKAT,SKCK,PAS_FOTO,SURAT_LAMARAN,REFERENSI,KONTRAK_KERJA,BUKU_REKENING,VAKSIN,FOTO_PRIBADI,LAINNYA]',
        'nama_file' => 'required|max_length[255]',
        'path' => 'required|max_length[500]',
    ];
    
    protected $validationMessages = [];
    protected $skipValidation = false;
    
    /**
     * Get dokumen with karyawan data
     */
    public function getDokumenWithKaryawan($limit = 10, $offset = 0, $search = null)
    {
        $builder = $this->db->table('dokumen d');
        $builder->select('d.*, k.nik, k.nama_lengkap, k.jabatan, k.departemen');
        $builder->join('karyawan k', 'k.id = d.karyawan_id', 'left');
        
        // Hapus join ke users jika kolom 'nama' tidak ada
        // $builder->join('users u', 'u.id = d.diupload_oleh', 'left');
        
        if ($search) {
            $builder->groupStart();
            $builder->like('k.nama_lengkap', $search);
            $builder->orLike('k.nik', $search);
            $builder->orLike('d.nomor_dokumen', $search);
            $builder->orLike('d.jenis', $search);
            $builder->groupEnd();
        }
        
        $builder->orderBy('d.created_at', 'DESC');
        $builder->limit($limit, $offset);
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get total dokumen with filter
     */
    public function getTotalDokumen($search = null)
    {
        $builder = $this->db->table('dokumen d');
        $builder->join('karyawan k', 'k.id = d.karyawan_id', 'left');
        
        if ($search) {
            $builder->groupStart();
            $builder->like('k.nama_lengkap', $search);
            $builder->orLike('k.nik', $search);
            $builder->orLike('d.nomor_dokumen', $search);
            $builder->orLike('d.jenis', $search);
            $builder->groupEnd();
        }
        
        return $builder->countAllResults();
    }
    
    /**
     * Get dokumen by karyawan
     */
    public function getDokumenByKaryawan($karyawan_id)
    {
        return $this->where('karyawan_id', $karyawan_id)
                   ->orderBy('created_at', 'DESC')
                   ->findAll();
    }
    
    /**
     * Count dokumen by karyawan
     */
    public function countDokumenByKaryawan($karyawan_id)
    {
        return $this->where('karyawan_id', $karyawan_id)->countAllResults();
    }
    
   // Di DokumenModel.php
public function getDokumenDetail($id)
{
    $builder = $this->db->table('dokumen d');
    $builder->select('d.*, k.nik, k.nama_lengkap, k.jabatan, k.departemen');
    $builder->join('karyawan k', 'k.id = d.karyawan_id', 'left');
    $builder->where('d.id', $id);
    
    $result = $builder->get()->getRowArray();
    
    if ($result) {
        // Pastikan path file benar
        if (!empty($result['path']) && strpos($result['path'], 'http') === false) {
            // Jika path tidak dimulai dengan http, pastikan diawali dengan slash
            if (strpos($result['path'], '/') !== 0 && strpos($result['path'], 'writable/') !== 0) {
                $result['path'] = 'writable/uploads/dokumen/' . $result['path'];
            }
        }
        
        // Pastikan keterangan adalah string
        if (isset($result['keterangan']) && is_array($result['keterangan'])) {
            $result['keterangan'] = implode(', ', array_filter($result['keterangan']));
        }
    }
    
    return $result;
}
    
    /**
     * Get jenis dokumen options
     */
    public function getJenisOptions()
    {
        return [
            'KTP' => 'KTP',
            'KK' => 'Kartu Keluarga',
            'IJAZAH' => 'Ijazah',
            'CV' => 'Curriculum Vitae',
            'NPWP' => 'NPWP',
            'BPJS_KES' => 'BPJS Kesehatan',
            'BPJS_TK' => 'BPJS Ketenagakerjaan',
            'SIM' => 'SIM',
            'SERTIFIKAT' => 'Sertifikat',
            'SKCK' => 'SKCK',
            'PAS_FOTO' => 'Pas Foto',
            'SURAT_LAMARAN' => 'Surat Lamaran',
            'REFERENSI' => 'Referensi',
            'KONTRAK_KERJA' => 'Kontrak Kerja',
            'BUKU_REKENING' => 'Buku Rekening',
            'VAKSIN' => 'Sertifikat Vaksin',
            'FOTO_PRIBADI' => 'Foto Pribadi',
            'LAINNYA' => 'Lainnya'
        ];
    }
    
    /**
     * Get status options
     */
    public function getStatusOptions()
    {
        return [
            'pending' => 'Pending',
            'diterima' => 'Diterima',
            'ditolak' => 'Ditolak'
        ];
    }
}