<?php
// app/Models/Direktur/DokumenModel.php

namespace App\Models\Direktur;

use CodeIgniter\Model;

class DokumenModel extends Model
{
    protected $table = 'form_dokumen';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'nomor_form',
        'karyawan_id',
        'tanggal_pengajuan',
        'jenis_dokumen',
        'keperluan',
        'keterangan_tambahan',
        'status_hrd',
        'status_direktur',
        'status_keseluruhan',
        'dokumen_hasil_path',
        'approved_by_hrd',
        'approved_by_direktur',
        'approved_at_hrd',
        'approved_at_direktur',
        'alasan_penolakan',
        'catatan',
        'created_by',
        'updated_by'
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    
    protected $validationRules = [
        'karyawan_id' => 'required|integer',
        'jenis_dokumen' => 'required',
        'keperluan' => 'required|min_length[5]',
    ];
    
    protected $validationMessages = [
        'karyawan_id' => [
            'required' => 'Karyawan harus dipilih',
            'integer' => 'ID Karyawan tidak valid'
        ],
        'jenis_dokumen' => [
            'required' => 'Jenis dokumen harus dipilih'
        ],
        'keperluan' => [
            'required' => 'Keperluan dokumen harus diisi',
            'min_length' => 'Keperluan minimal 5 karakter'
        ]
    ];
    
    /**
     * Get all form dokumen for direktur approval
     * Menampilkan form dokumen yang membutuhkan approval direktur (status_direktur = 'Menunggu')
     */
    public function getForDirekturApproval($status = null, $limit = null, $offset = 0)
    {
        $builder = $this->db->table('form_dokumen');
        
        $builder->select('
            form_dokumen.*,
            karyawan.nik,
            karyawan.nama_lengkap,
            karyawan.nama_panggilan,
            karyawan.jabatan,
            karyawan.departemen,
            karyawan.foto,
            hrd.nama_lengkap as hrd_nama,
            creator.nama_lengkap as created_by_name
        ');
        
        $builder->join('karyawan', 'karyawan.id = form_dokumen.karyawan_id');
        $builder->join('users as creator_user', 'creator_user.id = form_dokumen.created_by', 'left');
        $builder->join('karyawan as creator', 'creator.id = creator_user.karyawan_id', 'left');
        $builder->join('karyawan as hrd', 'hrd.id = form_dokumen.approved_by_hrd', 'left');
        
        $builder->where('form_dokumen.deleted_at', null);
        
        // Filter status untuk approval direktur
        if ($status === 'pending') {
            $builder->where('form_dokumen.status_direktur', 'Menunggu');
            $builder->where('form_dokumen.status_hrd', 'Diproses');
        } elseif ($status === 'approved') {
            $builder->where('form_dokumen.status_direktur', 'Disetujui');
        } elseif ($status === 'rejected') {
            $builder->where('form_dokumen.status_direktur', 'Ditolak');
        } elseif ($status === 'processed') {
            $builder->where('form_dokumen.status_hrd', 'Diproses');
        } elseif ($status === 'completed') {
            $builder->where('form_dokumen.status_keseluruhan', 'Selesai');
        } else {
            // Default: tampilkan yang menunggu, disetujui, ditolak
            $builder->whereIn('form_dokumen.status_direktur', ['Menunggu', 'Disetujui', 'Ditolak']);
            $builder->where('form_dokumen.status_keseluruhan !=', 'Draft');
        }
        
        $builder->orderBy('form_dokumen.tanggal_pengajuan', 'DESC');
        
        if ($limit !== null) {
            $builder->limit($limit, $offset);
        }
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get total count for direktur approval
     */
    public function getCountForDirekturApproval($status = null)
    {
        $builder = $this->db->table('form_dokumen');
        $builder->where('deleted_at', null);
        
        if ($status === 'pending') {
            $builder->where('status_direktur', 'Menunggu');
            $builder->where('status_hrd', 'Diproses');
        } elseif ($status === 'all') {
            // Semua
        } elseif ($status) {
            $builder->where('status_direktur', $status);
        } else {
            $builder->whereIn('status_direktur', ['Menunggu', 'Disetujui', 'Ditolak']);
            $builder->where('status_keseluruhan !=', 'Draft');
        }
        
        return $builder->countAllResults();
    }
    
    /**
     * Get pending count for badge notification
     */
    public function getPendingCount()
    {
        return $this->db->table('form_dokumen')
            ->where('status_direktur', 'Menunggu')
            ->where('status_hrd', 'Diproses')
            ->where('deleted_at', null)
            ->countAllResults();
    }
    
    /**
     * Get form dokumen detail by ID with complete info
     */
    public function getDetailById($id)
    {
        $builder = $this->db->table('form_dokumen');
        
        $builder->select('
            form_dokumen.*,
            karyawan.nik,
            karyawan.nama_lengkap,
            karyawan.nama_panggilan,
            karyawan.jabatan,
            karyawan.departemen,
            karyawan.tanggal_masuk as karyawan_tanggal_masuk,
            karyawan.foto,
            hrd.nama_lengkap as hrd_nama,
            hrd.jabatan as hrd_jabatan,
            direktur.nama_lengkap as direktur_nama,
            direktur.jabatan as direktur_jabatan,
            creator.nama_lengkap as created_by_name,
            creator.jabatan as created_by_jabatan
        ');
        
        $builder->join('karyawan', 'karyawan.id = form_dokumen.karyawan_id');
        $builder->join('users as creator_user', 'creator_user.id = form_dokumen.created_by', 'left');
        $builder->join('karyawan as creator', 'creator.id = creator_user.karyawan_id', 'left');
        $builder->join('karyawan as hrd', 'hrd.id = form_dokumen.approved_by_hrd', 'left');
        $builder->join('karyawan as direktur', 'direktur.id = form_dokumen.approved_by_direktur', 'left');
        
        $builder->where('form_dokumen.id', $id);
        $builder->where('form_dokumen.deleted_at', null);
        
        return $builder->get()->getRowArray();
    }
    
    /**
     * Approve form dokumen by direktur
     */
    public function approveByDirektur($id, $userId, $karyawanId)
    {
        $data = [
            'status_direktur' => 'Disetujui',
            'status_keseluruhan' => 'Selesai',
            'approved_by_direktur' => $karyawanId,
            'approved_at_direktur' => date('Y-m-d H:i:s'),
            'updated_by' => $userId,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->update($id, $data);
    }
    
    /**
     * Reject form dokumen by direktur
     */
    public function rejectByDirektur($id, $userId, $karyawanId, $alasan)
    {
        $data = [
            'status_direktur' => 'Ditolak',
            'status_keseluruhan' => 'Ditolak',
            'approved_by_direktur' => $karyawanId,
            'approved_at_direktur' => date('Y-m-d H:i:s'),
            'alasan_penolakan' => $alasan,
            'updated_by' => $userId,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->update($id, $data);
    }
    
    /**
     * Get form dokumen statistics for dashboard
     */
    public function getStatistics($startDate = null, $endDate = null)
    {
        $builder = $this->db->table('form_dokumen');
        
        $builder->select("
            COUNT(*) as total_dokumen,
            SUM(CASE WHEN status_direktur = 'Menunggu' AND status_hrd = 'Diproses' THEN 1 ELSE 0 END) as total_menunggu,
            SUM(CASE WHEN status_direktur = 'Disetujui' THEN 1 ELSE 0 END) as total_disetujui,
            SUM(CASE WHEN status_direktur = 'Ditolak' THEN 1 ELSE 0 END) as total_ditolak,
            SUM(CASE WHEN status_hrd = 'Diproses' THEN 1 ELSE 0 END) as total_diproses,
            SUM(CASE WHEN status_keseluruhan = 'Selesai' THEN 1 ELSE 0 END) as total_selesai
        ");
        
        $builder->where('deleted_at', null);
        
        if ($startDate) {
            $builder->where('tanggal_pengajuan >=', $startDate);
        }
        if ($endDate) {
            $builder->where('tanggal_pengajuan <=', $endDate . ' 23:59:59');
        }
        
        return $builder->get()->getRowArray();
    }
    
    /**
     * Get form dokumen for export
     */
    public function getForExport($startDate = null, $endDate = null, $status = null)
    {
        $builder = $this->db->table('form_dokumen');
        
        $builder->select('
            form_dokumen.nomor_form,
            form_dokumen.tanggal_pengajuan,
            form_dokumen.jenis_dokumen,
            form_dokumen.keperluan,
            form_dokumen.keterangan_tambahan,
            form_dokumen.status_hrd,
            form_dokumen.status_direktur,
            form_dokumen.status_keseluruhan,
            karyawan.nik,
            karyawan.nama_lengkap,
            karyawan.jabatan,
            karyawan.departemen
        ');
        
        $builder->join('karyawan', 'karyawan.id = form_dokumen.karyawan_id');
        $builder->where('form_dokumen.deleted_at', null);
        
        if ($startDate) {
            $builder->where('form_dokumen.tanggal_pengajuan >=', $startDate);
        }
        if ($endDate) {
            $builder->where('form_dokumen.tanggal_pengajuan <=', $endDate . ' 23:59:59');
        }
        if ($status) {
            $builder->where('form_dokumen.status_direktur', $status);
        }
        
        $builder->orderBy('form_dokumen.tanggal_pengajuan', 'DESC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get list of document types
     */
    public function getJenisDokumenList()
    {
        return [
            'SK Pengangkatan' => 'SK Pengangkatan',
            'SK Mutasi' => 'SK Mutasi',
            'SK Pemberhentian' => 'SK Pemberhentian',
            'Surat Keterangan Kerja' => 'Surat Keterangan Kerja',
            'Surat Keterangan Gaji' => 'Surat Keterangan Gaji',
            'Surat Referensi' => 'Surat Referensi',
            'Copy Kontrak Kerja' => 'Copy Kontrak Kerja',
            'Copy Dokumen Lain' => 'Copy Dokumen Lain',
            'Legalitas Perusahaan' => 'Legalitas Perusahaan',
            'Lainnya' => 'Lainnya'
        ];
    }
    
    /**
     * Get human readable status label for direktur
     */
    public function getDirekturStatusLabel($status)
    {
        $labels = [
            'Menunggu' => '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Menunggu</span>',
            'Disetujui' => '<span class="badge bg-success"><i class="fas fa-check me-1"></i>Disetujui</span>',
            'Ditolak' => '<span class="badge bg-danger"><i class="fas fa-times me-1"></i>Ditolak</span>'
        ];
        
        return $labels[$status] ?? '<span class="badge bg-secondary">' . $status . '</span>';
    }
    
    /**
     * Get human readable status label for keseluruhan
     */
    public function getKeseluruhanStatusLabel($status)
    {
        $labels = [
            'Draft' => '<span class="badge bg-secondary">Draft</span>',
            'Menunggu HRD' => '<span class="badge bg-info">Menunggu HRD</span>',
            'Menunggu Direktur' => '<span class="badge bg-warning text-dark">Menunggu Direktur</span>',
            'Diproses' => '<span class="badge bg-primary">Diproses</span>',
            'Selesai' => '<span class="badge bg-success">Selesai</span>',
            'Ditolak' => '<span class="badge bg-danger">Ditolak</span>'
        ];
        
        return $labels[$status] ?? '<span class="badge bg-secondary">' . $status . '</span>';
    }
    
    /**
     * Get human readable label for jenis dokumen
     */
    public function getJenisDokumenLabel($jenis)
    {
        $labels = [
            'SK Pengangkatan' => '<span class="badge bg-primary">SK Pengangkatan</span>',
            'SK Mutasi' => '<span class="badge bg-info">SK Mutasi</span>',
            'SK Pemberhentian' => '<span class="badge bg-danger">SK Pemberhentian</span>',
            'Surat Keterangan Kerja' => '<span class="badge bg-success">Surat Keterangan Kerja</span>',
            'Surat Keterangan Gaji' => '<span class="badge bg-success">Surat Keterangan Gaji</span>',
            'Surat Referensi' => '<span class="badge bg-secondary">Surat Referensi</span>',
            'Copy Kontrak Kerja' => '<span class="badge bg-dark">Copy Kontrak Kerja</span>',
            'Copy Dokumen Lain' => '<span class="badge bg-secondary">Copy Dokumen Lain</span>',
            'Legalitas Perusahaan' => '<span class="badge bg-warning text-dark">Legalitas Perusahaan</span>',
            'Lainnya' => '<span class="badge bg-secondary">Lainnya</span>'
        ];
        
        return $labels[$jenis] ?? '<span class="badge bg-secondary">' . $jenis . '</span>';
    }
}