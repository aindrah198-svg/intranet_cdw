<?php
// app/Models/Direktur/BastModel.php

namespace App\Models\Direktur;

use CodeIgniter\Model;

class BastModel extends Model
{
    protected $table = 'form_bast';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'nomor_bast',
        'spk_id',
        'surat_jalan_id',
        'client_id',
        'tanggal_bast',
        'judul_pekerjaan',
        'lokasi_pekerjaan',
        'deskripsi_pekerjaan',
        'kondisi',
        'catatan_tambahan',
        'dokumen_pendukung',
        'status_hrd',
        'status_direktur',
        'status_keseluruhan',
        'disetujui_hrd_oleh',
        'disetujui_direktur_oleh',
        'disetujui_hrd_at',
        'disetujui_direktur_at',
        'alasan_penolakan_hrd',
        'alasan_penolakan_direktur',
        'pihak_pertama_nama',
        'pihak_pertama_jabatan',
        'pihak_pertama_tanggal',
        'pihak_pertama_ttd',
        'pihak_kedua_nama',
        'pihak_kedua_jabatan',
        'pihak_kedua_tanggal',
        'pihak_kedua_ttd',
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
        'nomor_bast' => 'required',
        'client_id' => 'required|integer',
        'tanggal_bast' => 'required|valid_date',
        'judul_pekerjaan' => 'required|min_length[5]',
        'deskripsi_pekerjaan' => 'required|min_length[5]',
        'kondisi' => 'required|in_list[Baik,Cukup,Perlu Perbaikan]'
    ];
    
    protected $validationMessages = [
        'nomor_bast' => [
            'required' => 'Nomor BAST harus diisi'
        ],
        'client_id' => [
            'required' => 'Client harus dipilih',
            'integer' => 'ID Client tidak valid'
        ],
        'tanggal_bast' => [
            'required' => 'Tanggal BAST harus diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ],
        'judul_pekerjaan' => [
            'required' => 'Judul pekerjaan harus diisi',
            'min_length' => 'Judul pekerjaan minimal 5 karakter'
        ],
        'deskripsi_pekerjaan' => [
            'required' => 'Deskripsi pekerjaan harus diisi',
            'min_length' => 'Deskripsi pekerjaan minimal 5 karakter'
        ],
        'kondisi' => [
            'required' => 'Kondisi harus dipilih',
            'in_list' => 'Kondisi tidak valid'
        ]
    ];
    
    /**
     * Get all BAST for direktur approval
     * Menampilkan BAST yang membutuhkan approval direktur (status_direktur = 'Menunggu' dan status_hrd = 'Disetujui HRD')
     */
    public function getForDirekturApproval($status = null, $limit = null, $offset = 0)
    {
        $builder = $this->db->table('form_bast');
        
        $builder->select('
            form_bast.*,
            client.nama_perusahaan,
            client.kode_client,
            spk_instalasi.nomor_spk,
            spk_instalasi.nilai_kontrak,
            surat_jalan.nomor_surat_jalan,
            hrd.nama_lengkap as hrd_nama,
            creator.nama_lengkap as created_by_name
        ');
        
        $builder->join('client', 'client.id = form_bast.client_id');
        $builder->join('spk_instalasi', 'spk_instalasi.id = form_bast.spk_id', 'left');
        $builder->join('surat_jalan', 'surat_jalan.id = form_bast.surat_jalan_id', 'left');
        $builder->join('users as creator_user', 'creator_user.id = form_bast.created_by', 'left');
        $builder->join('karyawan as creator', 'creator.id = creator_user.karyawan_id', 'left');
        $builder->join('karyawan as hrd', 'hrd.id = form_bast.disetujui_hrd_oleh', 'left');
        
        $builder->where('form_bast.deleted_at', null);
        
        // Filter status untuk approval direktur
        if ($status === 'pending') {
            $builder->where('form_bast.status_direktur', 'Menunggu');
            $builder->where('form_bast.status_hrd', 'Disetujui HRD');
        } elseif ($status === 'approved') {
            $builder->where('form_bast.status_direktur', 'Disetujui');
        } elseif ($status === 'rejected') {
            $builder->where('form_bast.status_direktur', 'Ditolak');
        } else {
            // Default: tampilkan yang menunggu, disetujui, ditolak
            $builder->whereIn('form_bast.status_direktur', ['Menunggu', 'Disetujui', 'Ditolak']);
            $builder->where('form_bast.status_hrd !=', 'Draft');
        }
        
        $builder->orderBy('form_bast.created_at', 'DESC');
        
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
        $builder = $this->db->table('form_bast');
        $builder->where('deleted_at', null);
        
        if ($status === 'pending') {
            $builder->where('status_direktur', 'Menunggu');
            $builder->where('status_hrd', 'Disetujui HRD');
        } elseif ($status === 'all') {
            // Semua
        } elseif ($status) {
            $builder->where('status_direktur', $status);
        } else {
            $builder->whereIn('status_direktur', ['Menunggu', 'Disetujui', 'Ditolak']);
            $builder->where('status_hrd !=', 'Draft');
        }
        
        return $builder->countAllResults();
    }
    
    /**
     * Get pending count for badge notification
     */
    public function getPendingCount()
    {
        return $this->db->table('form_bast')
            ->where('status_direktur', 'Menunggu')
            ->where('status_hrd', 'Disetujui HRD')
            ->where('deleted_at', null)
            ->countAllResults();
    }
    
    /**
     * Get BAST detail by ID with complete info
     */
    public function getDetailById($id)
    {
        $builder = $this->db->table('form_bast');
        
        $builder->select('
            form_bast.*,
            client.nama_perusahaan,
            client.kode_client,
            client.alamat as client_alamat,
            client.telepon as client_telepon,
            client.email_client as client_email,
            spk_instalasi.nomor_spk,
            spk_instalasi.nilai_kontrak,
            spk_instalasi.tanggal_mulai as spk_tanggal_mulai,
            spk_instalasi.tanggal_selesai as spk_tanggal_selesai,
            surat_jalan.nomor_surat_jalan,
            hrd.nama_lengkap as hrd_nama,
            hrd.jabatan as hrd_jabatan,
            direktur.nama_lengkap as direktur_nama,
            direktur.jabatan as direktur_jabatan,
            creator.nama_lengkap as created_by_name,
            creator.jabatan as created_by_jabatan
        ');
        
        $builder->join('client', 'client.id = form_bast.client_id');
        $builder->join('spk_instalasi', 'spk_instalasi.id = form_bast.spk_id', 'left');
        $builder->join('surat_jalan', 'surat_jalan.id = form_bast.surat_jalan_id', 'left');
        $builder->join('users as creator_user', 'creator_user.id = form_bast.created_by', 'left');
        $builder->join('karyawan as creator', 'creator.id = creator_user.karyawan_id', 'left');
        $builder->join('karyawan as hrd', 'hrd.id = form_bast.disetujui_hrd_oleh', 'left');
        $builder->join('karyawan as direktur', 'direktur.id = form_bast.disetujui_direktur_oleh', 'left');
        
        $builder->where('form_bast.id', $id);
        $builder->where('form_bast.deleted_at', null);
        
        return $builder->get()->getRowArray();
    }
    
    /**
     * Approve BAST by direktur
     */
    public function approveByDirektur($id, $userId, $karyawanId)
    {
        $data = [
            'status_direktur' => 'Disetujui',
            'status_keseluruhan' => 'Disetujui',
            'disetujui_direktur_oleh' => $karyawanId,
            'disetujui_direktur_at' => date('Y-m-d H:i:s'),
            'updated_by' => $userId,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->update($id, $data);
    }
    
    /**
     * Reject BAST by direktur
     */
    public function rejectByDirektur($id, $userId, $karyawanId, $alasan)
    {
        $data = [
            'status_direktur' => 'Ditolak',
            'status_keseluruhan' => 'Ditolak',
            'disetujui_direktur_oleh' => $karyawanId,
            'disetujui_direktur_at' => date('Y-m-d H:i:s'),
            'alasan_penolakan_direktur' => $alasan,
            'updated_by' => $userId,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->update($id, $data);
    }
    
    /**
     * Get BAST statistics for dashboard
     */
    public function getStatistics($startDate = null, $endDate = null)
    {
        $builder = $this->db->table('form_bast');
        
        $builder->select("
            COUNT(*) as total_bast,
            SUM(CASE WHEN status_direktur = 'Menunggu' AND status_hrd = 'Disetujui HRD' THEN 1 ELSE 0 END) as total_menunggu,
            SUM(CASE WHEN status_direktur = 'Disetujui' THEN 1 ELSE 0 END) as total_disetujui,
            SUM(CASE WHEN status_direktur = 'Ditolak' THEN 1 ELSE 0 END) as total_ditolak,
            SUM(CASE WHEN kondisi = 'Baik' THEN 1 ELSE 0 END) as total_kondisi_baik,
            SUM(CASE WHEN kondisi = 'Cukup' THEN 1 ELSE 0 END) as total_kondisi_cukup,
            SUM(CASE WHEN kondisi = 'Perlu Perbaikan' THEN 1 ELSE 0 END) as total_kondisi_perlu_perbaikan
        ");
        
        $builder->where('deleted_at', null);
        
        if ($startDate) {
            $builder->where('tanggal_bast >=', $startDate);
        }
        if ($endDate) {
            $builder->where('tanggal_bast <=', $endDate);
        }
        
        return $builder->get()->getRowArray();
    }
    
    /**
     * Get kondisi list
     */
    public function getKondisiList()
    {
        return [
            'Baik' => 'Baik',
            'Cukup' => 'Cukup',
            'Perlu Perbaikan' => 'Perlu Perbaikan'
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
     * Get kondisi badge
     */
    public function getKondisiBadge($kondisi)
    {
        $badges = [
            'Baik' => '<span class="badge bg-success">Baik</span>',
            'Cukup' => '<span class="badge bg-warning text-dark">Cukup</span>',
            'Perlu Perbaikan' => '<span class="badge bg-danger">Perlu Perbaikan</span>'
        ];
        
        return $badges[$kondisi] ?? '<span class="badge bg-secondary">' . $kondisi . '</span>';
    }
    
    /**
     * Get BAST for export
     */
    public function getForExport($startDate = null, $endDate = null, $status = null)
    {
        $builder = $this->db->table('form_bast');
        
        $builder->select('
            form_bast.nomor_bast,
            form_bast.tanggal_bast,
            form_bast.judul_pekerjaan,
            form_bast.deskripsi_pekerjaan,
            form_bast.lokasi_pekerjaan,
            form_bast.kondisi,
            form_bast.status_hrd,
            form_bast.status_direktur,
            form_bast.status_keseluruhan,
            client.nama_perusahaan,
            client.kode_client,
            spk_instalasi.nomor_spk
        ');
        
        $builder->join('client', 'client.id = form_bast.client_id');
        $builder->join('spk_instalasi', 'spk_instalasi.id = form_bast.spk_id', 'left');
        $builder->where('form_bast.deleted_at', null);
        
        if ($startDate) {
            $builder->where('form_bast.tanggal_bast >=', $startDate);
        }
        if ($endDate) {
            $builder->where('form_bast.tanggal_bast <=', $endDate);
        }
        if ($status) {
            $builder->where('form_bast.status_direktur', $status);
        }
        
        $builder->orderBy('form_bast.tanggal_bast', 'DESC');
        
        return $builder->get()->getResultArray();
    }
}