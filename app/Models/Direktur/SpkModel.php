<?php
// app/Models/Direktur/SpkModel.php

namespace App\Models\Direktur;

use CodeIgniter\Model;

class SpkModel extends Model
{
    protected $table = 'spk_instalasi';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'nomor_spk',
        'client_id',
        'project_id',
        'judul_pekerjaan',
        'deskripsi_pekerjaan',
        'lokasi_pekerjaan',
        'tanggal_mulai',
        'tanggal_selesai',
        'penanggung_jawab_id',
        'nilai_kontrak',
        'status',
        'catatan',
        'approved_by',
        'approved_at',
        'created_by'
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    
    protected $validationRules = [
        'nomor_spk' => 'required',
        'client_id' => 'required|integer',
        'judul_pekerjaan' => 'required|min_length[5]',
        'tanggal_mulai' => 'required|valid_date',
        'tanggal_selesai' => 'required|valid_date',
        'nilai_kontrak' => 'permit_empty|numeric'
    ];
    
    protected $validationMessages = [
        'nomor_spk' => [
            'required' => 'Nomor SPK harus diisi'
        ],
        'client_id' => [
            'required' => 'Client harus dipilih',
            'integer' => 'ID Client tidak valid'
        ],
        'judul_pekerjaan' => [
            'required' => 'Judul pekerjaan harus diisi',
            'min_length' => 'Judul pekerjaan minimal 5 karakter'
        ],
        'tanggal_mulai' => [
            'required' => 'Tanggal mulai harus diisi',
            'valid_date' => 'Format tanggal mulai tidak valid'
        ],
        'tanggal_selesai' => [
            'required' => 'Tanggal selesai harus diisi',
            'valid_date' => 'Format tanggal selesai tidak valid'
        ],
        'nilai_kontrak' => [
            'numeric' => 'Nilai kontrak harus berupa angka'
        ]
    ];
    
    /**
     * Get all SPK for direktur approval
     * Menampilkan SPK yang membutuhkan approval direktur (status = 'draft' atau status = 'menunggu')
     */
    public function getForDirekturApproval($status = null, $limit = null, $offset = 0)
    {
        $builder = $this->db->table('spk_instalasi');
        
        $builder->select('
            spk_instalasi.*,
            client.nama_perusahaan,
            client.kode_client,
            karyawan.nama_lengkap as penanggung_jawab_nama,
            karyawan.jabatan as penanggung_jawab_jabatan,
            creator.nama_lengkap as created_by_name,
            project.nama_project
        ');
        
        $builder->join('client', 'client.id = spk_instalasi.client_id');
        $builder->join('karyawan', 'karyawan.id = spk_instalasi.penanggung_jawab_id', 'left');
        $builder->join('users as creator_user', 'creator_user.id = spk_instalasi.created_by', 'left');
        $builder->join('karyawan as creator', 'creator.id = creator_user.karyawan_id', 'left');
        $builder->join('project', 'project.id = spk_instalasi.project_id', 'left');
        
        $builder->where('spk_instalasi.deleted_at', null);
        
        // Filter status untuk approval direktur
        if ($status === 'pending') {
            $builder->where('spk_instalasi.status', 'draft');
        } elseif ($status === 'approved') {
            $builder->where('spk_instalasi.status', 'disetujui');
        } elseif ($status === 'rejected') {
            $builder->where('spk_instalasi.status', 'ditolak');
        } elseif ($status === 'all') {
            // Semua status
        } else {
            // Default: menampilkan yang draft/on_progress/selesai
            $builder->whereIn('spk_instalasi.status', ['draft', 'disetujui', 'ditolak', 'on_progress', 'selesai']);
        }
        
        $builder->orderBy('spk_instalasi.created_at', 'DESC');
        
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
        $builder = $this->db->table('spk_instalasi');
        $builder->where('deleted_at', null);
        
        if ($status === 'pending') {
            $builder->where('status', 'draft');
        } elseif ($status === 'all') {
            // Semua
        } elseif ($status) {
            $builder->where('status', $status);
        } else {
            $builder->whereIn('status', ['draft', 'disetujui', 'ditolak', 'on_progress', 'selesai']);
        }
        
        return $builder->countAllResults();
    }
    
    /**
     * Get pending count for badge notification
     */
    public function getPendingCount()
    {
        return $this->db->table('spk_instalasi')
            ->where('status', 'draft')
            ->where('deleted_at', null)
            ->countAllResults();
    }
    
    /**
     * Get SPK detail by ID with complete info
     */
    public function getDetailById($id)
    {
        $builder = $this->db->table('spk_instalasi');
        
        $builder->select('
            spk_instalasi.*,
            client.nama_perusahaan,
            client.kode_client,
            client.alamat as client_alamat,
            client.telepon as client_telepon,
            client.email_client as client_email,
            karyawan.nama_lengkap as penanggung_jawab_nama,
            karyawan.nik as penanggung_jawab_nik,
            karyawan.jabatan as penanggung_jawab_jabatan,
            karyawan.telepon as penanggung_jawab_telepon,
            creator.nama_lengkap as created_by_name,
            creator.jabatan as created_by_jabatan,
            approver.nama_lengkap as approved_by_name,
            approver.jabatan as approved_by_jabatan,
            project.nama_project,
            project.kode_project
        ');
        
        $builder->join('client', 'client.id = spk_instalasi.client_id');
        $builder->join('karyawan', 'karyawan.id = spk_instalasi.penanggung_jawab_id', 'left');
        $builder->join('users as creator_user', 'creator_user.id = spk_instalasi.created_by', 'left');
        $builder->join('karyawan as creator', 'creator.id = creator_user.karyawan_id', 'left');
        $builder->join('users as approver_user', 'approver_user.id = spk_instalasi.approved_by', 'left');
        $builder->join('karyawan as approver', 'approver.id = approver_user.karyawan_id', 'left');
        $builder->join('project', 'project.id = spk_instalasi.project_id', 'left');
        
        $builder->where('spk_instalasi.id', $id);
        $builder->where('spk_instalasi.deleted_at', null);
        
        return $builder->get()->getRowArray();
    }
    
    /**
     * Approve SPK by direktur
     */
    public function approveByDirektur($id, $userId)
    {
        $data = [
            'status' => 'disetujui',
            'approved_by' => $userId,
            'approved_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->update($id, $data);
    }
    
    /**
     * Reject SPK by direktur
     */
    public function rejectByDirektur($id, $userId, $alasan)
    {
        $data = [
            'status' => 'ditolak',
            'approved_by' => $userId,
            'approved_at' => date('Y-m-d H:i:s'),
            'catatan' => $alasan,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->update($id, $data);
    }
    
    /**
     * Get SPK statistics for dashboard
     */
    public function getStatistics($startDate = null, $endDate = null)
    {
        $builder = $this->db->table('spk_instalasi');
        
        $builder->select("
            COUNT(*) as total_spk,
            SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as total_draft,
            SUM(CASE WHEN status = 'disetujui' THEN 1 ELSE 0 END) as total_disetujui,
            SUM(CASE WHEN status = 'ditolak' THEN 1 ELSE 0 END) as total_ditolak,
            SUM(CASE WHEN status = 'on_progress' THEN 1 ELSE 0 END) as total_on_progress,
            SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as total_selesai,
            SUM(nilai_kontrak) as total_nilai_kontrak
        ");
        
        $builder->where('deleted_at', null);
        
        if ($startDate) {
            $builder->where('created_at >=', $startDate);
        }
        if ($endDate) {
            $builder->where('created_at <=', $endDate . ' 23:59:59');
        }
        
        return $builder->get()->getRowArray();
    }
    
    /**
     * Get SPK for export
     */
    public function getForExport($startDate = null, $endDate = null, $status = null)
    {
        $builder = $this->db->table('spk_instalasi');
        
        $builder->select('
            spk_instalasi.nomor_spk,
            spk_instalasi.judul_pekerjaan,
            spk_instalasi.deskripsi_pekerjaan,
            spk_instalasi.lokasi_pekerjaan,
            spk_instalasi.tanggal_mulai,
            spk_instalasi.tanggal_selesai,
            spk_instalasi.nilai_kontrak,
            spk_instalasi.status,
            spk_instalasi.created_at,
            spk_instalasi.approved_at,
            client.nama_perusahaan,
            client.kode_client,
            karyawan.nama_lengkap as penanggung_jawab_nama
        ');
        
        $builder->join('client', 'client.id = spk_instalasi.client_id');
        $builder->join('karyawan', 'karyawan.id = spk_instalasi.penanggung_jawab_id', 'left');
        $builder->where('spk_instalasi.deleted_at', null);
        
        if ($startDate) {
            $builder->where('spk_instalasi.created_at >=', $startDate);
        }
        if ($endDate) {
            $builder->where('spk_instalasi.created_at <=', $endDate . ' 23:59:59');
        }
        if ($status) {
            $builder->where('spk_instalasi.status', $status);
        }
        
        $builder->orderBy('spk_instalasi.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get human readable status label
     */
    public function getStatusLabel($status)
    {
        $labels = [
            'draft' => '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Draft</span>',
            'disetujui' => '<span class="badge bg-success"><i class="fas fa-check me-1"></i>Disetujui</span>',
            'ditolak' => '<span class="badge bg-danger"><i class="fas fa-times me-1"></i>Ditolak</span>',
            'on_progress' => '<span class="badge bg-info"><i class="fas fa-spinner me-1"></i>On Progress</span>',
            'selesai' => '<span class="badge bg-primary"><i class="fas fa-check-circle me-1"></i>Selesai</span>',
            'batal' => '<span class="badge bg-secondary"><i class="fas fa-ban me-1"></i>Batal</span>'
        ];
        
        return $labels[$status] ?? '<span class="badge bg-secondary">' . $status . '</span>';
    }
    
    /**
     * Format currency
     */
    public function formatCurrency($amount)
    {
        if (empty($amount) || $amount == 0) {
            return '-';
        }
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}