<?php
// app/Models/Direktur/KasbonModel.php

namespace App\Models\Direktur;

use CodeIgniter\Model;

class KasbonModel extends Model
{
    protected $table = 'form_kasbon';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'nomor_kasbon',
        'karyawan_id',
        'tanggal_pengajuan',
        'jumlah_kasbon',
        'alasan',
        'tanggal_dibutuhkan',
        'rencana_pelunasan',
        'status_hrd',
        'status_direktur',
        'status_keseluruhan',
        'disetujui_hrd_oleh',
        'disetujui_hrd_at',
        'disetujui_direktur_oleh',
        'disetujui_direktur_at',
        'alasan_penolakan_hrd',
        'alasan_penolakan_direktur',
        'tanggal_pencairan',
        'metode_pencairan',
        'bank_tujuan',
        'no_rekening_tujuan',
        'bukti_pencairan',
        'sisa_pinjaman',
        'lunas_pada',
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
        'jumlah_kasbon' => 'required|numeric|greater_than[0]',
        'alasan' => 'required|min_length[5]',
        'tanggal_dibutuhkan' => 'permit_empty|valid_date'
    ];
    
    protected $validationMessages = [
        'karyawan_id' => [
            'required' => 'Karyawan harus dipilih',
            'integer' => 'ID Karyawan tidak valid'
        ],
        'jumlah_kasbon' => [
            'required' => 'Jumlah kasbon harus diisi',
            'numeric' => 'Jumlah kasbon harus berupa angka',
            'greater_than' => 'Jumlah kasbon harus lebih dari 0'
        ],
        'alasan' => [
            'required' => 'Alasan kasbon harus diisi',
            'min_length' => 'Alasan minimal 5 karakter'
        ]
    ];
    
    /**
     * Get all kasbon for direktur approval
     * Menampilkan kasbon yang membutuhkan approval direktur (status_direktur = 'Menunggu' dan status_hrd = 'Disetujui HRD')
     */
    public function getForDirekturApproval($status = null, $limit = null, $offset = 0)
    {
        $builder = $this->db->table('form_kasbon');
        
        $builder->select('
            form_kasbon.*,
            karyawan.nik,
            karyawan.nama_lengkap,
            karyawan.nama_panggilan,
            karyawan.jabatan,
            karyawan.departemen,
            karyawan.foto,
            hrd.nama_lengkap as hrd_nama,
            creator.nama_lengkap as created_by_name
        ');
        
        $builder->join('karyawan', 'karyawan.id = form_kasbon.karyawan_id');
        $builder->join('users as creator_user', 'creator_user.id = form_kasbon.created_by', 'left');
        $builder->join('karyawan as creator', 'creator.id = creator_user.karyawan_id', 'left');
        $builder->join('karyawan as hrd', 'hrd.id = form_kasbon.disetujui_hrd_oleh', 'left');
        
        $builder->where('form_kasbon.deleted_at', null);
        
        // Filter status untuk approval direktur
        if ($status === 'pending') {
            $builder->where('form_kasbon.status_direktur', 'Menunggu');
            $builder->where('form_kasbon.status_hrd', 'Disetujui HRD');
        } elseif ($status === 'approved') {
            $builder->where('form_kasbon.status_direktur', 'Disetujui');
        } elseif ($status === 'rejected') {
            $builder->where('form_kasbon.status_direktur', 'Ditolak');
        } else {
            // Default: tampilkan yang menunggu, disetujui, ditolak
            $builder->where('form_kasbon.status_direktur !=', '');
            $builder->where('form_kasbon.status_keseluruhan !=', 'Draft');
        }
        
        $builder->orderBy('form_kasbon.tanggal_pengajuan', 'DESC');
        
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
        $builder = $this->db->table('form_kasbon');
        $builder->where('deleted_at', null);
        
        if ($status === 'pending') {
            $builder->where('status_direktur', 'Menunggu');
            $builder->where('status_hrd', 'Disetujui HRD');
        } elseif ($status === 'all') {
            // Semua
        } elseif ($status) {
            $builder->where('status_direktur', $status);
        } else {
            $builder->where('status_direktur !=', '');
            $builder->where('status_keseluruhan !=', 'Draft');
        }
        
        return $builder->countAllResults();
    }
    
    /**
     * Get pending count for badge notification
     */
    public function getPendingCount()
    {
        return $this->db->table('form_kasbon')
            ->where('status_direktur', 'Menunggu')
            ->where('status_hrd', 'Disetujui HRD')
            ->where('deleted_at', null)
            ->countAllResults();
    }
    
    /**
     * Get kasbon detail by ID with complete info
     */
    public function getDetailById($id)
    {
        $builder = $this->db->table('form_kasbon');
        
        $builder->select('
            form_kasbon.*,
            karyawan.nik,
            karyawan.nama_lengkap,
            karyawan.nama_panggilan,
            karyawan.jabatan,
            karyawan.departemen,
            karyawan.tanggal_masuk as karyawan_tanggal_masuk,
            karyawan.gaji_pokok,
            karyawan.foto,
            hrd.nama_lengkap as hrd_nama,
            hrd.jabatan as hrd_jabatan,
            direktur.nama_lengkap as direktur_nama,
            direktur.jabatan as direktur_jabatan,
            creator.nama_lengkap as created_by_name
        ');
        
        $builder->join('karyawan', 'karyawan.id = form_kasbon.karyawan_id');
        $builder->join('users as creator_user', 'creator_user.id = form_kasbon.created_by', 'left');
        $builder->join('karyawan as creator', 'creator.id = creator_user.karyawan_id', 'left');
        $builder->join('karyawan as hrd', 'hrd.id = form_kasbon.disetujui_hrd_oleh', 'left');
        $builder->join('karyawan as direktur', 'direktur.id = form_kasbon.disetujui_direktur_oleh', 'left');
        
        $builder->where('form_kasbon.id', $id);
        $builder->where('form_kasbon.deleted_at', null);
        
        return $builder->get()->getRowArray();
    }
    
    /**
     * Approve kasbon by direktur
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
     * Reject kasbon by direktur
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
     * Get kasbon statistics for dashboard
     */
    public function getStatistics($startDate = null, $endDate = null)
    {
        $builder = $this->db->table('form_kasbon');
        
        $builder->select("
            COUNT(*) as total_kasbon,
            SUM(CASE WHEN status_direktur = 'Menunggu' AND status_hrd = 'Disetujui HRD' THEN 1 ELSE 0 END) as total_menunggu,
            SUM(CASE WHEN status_direktur = 'Disetujui' THEN 1 ELSE 0 END) as total_disetujui,
            SUM(CASE WHEN status_direktur = 'Ditolak' THEN 1 ELSE 0 END) as total_ditolak,
            SUM(CASE WHEN status_keseluruhan = 'Dicairkan' THEN 1 ELSE 0 END) as total_dicairkan,
            SUM(CASE WHEN status_keseluruhan = 'Lunas' THEN 1 ELSE 0 END) as total_lunas,
            SUM(jumlah_kasbon) as total_nominal,
            SUM(CASE WHEN status_direktur = 'Disetujui' THEN jumlah_kasbon ELSE 0 END) as total_disetujui_nominal,
            SUM(sisa_pinjaman) as total_sisa_pinjaman
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
     * Get kasbon for export
     */
    public function getForExport($startDate = null, $endDate = null, $status = null)
    {
        $builder = $this->db->table('form_kasbon');
        
        $builder->select('
            form_kasbon.nomor_kasbon,
            form_kasbon.tanggal_pengajuan,
            form_kasbon.jumlah_kasbon,
            form_kasbon.alasan,
            form_kasbon.tanggal_dibutuhkan,
            form_kasbon.rencana_pelunasan,
            form_kasbon.status_direktur,
            form_kasbon.status_keseluruhan,
            form_kasbon.sisa_pinjaman,
            karyawan.nik,
            karyawan.nama_lengkap,
            karyawan.jabatan,
            karyawan.departemen
        ');
        
        $builder->join('karyawan', 'karyawan.id = form_kasbon.karyawan_id');
        $builder->where('form_kasbon.deleted_at', null);
        
        if ($startDate) {
            $builder->where('form_kasbon.tanggal_pengajuan >=', $startDate);
        }
        if ($endDate) {
            $builder->where('form_kasbon.tanggal_pengajuan <=', $endDate . ' 23:59:59');
        }
        if ($status) {
            $builder->where('form_kasbon.status_direktur', $status);
        }
        
        $builder->orderBy('form_kasbon.tanggal_pengajuan', 'DESC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get human readable status label
     */
    public function getStatusLabel($status)
    {
        $labels = [
            'Menunggu' => '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Menunggu</span>',
            'Disetujui' => '<span class="badge bg-success"><i class="fas fa-check me-1"></i>Disetujui</span>',
            'Ditolak' => '<span class="badge bg-danger"><i class="fas fa-times me-1"></i>Ditolak</span>'
        ];
        
        return $labels[$status] ?? '<span class="badge bg-secondary">' . $status . '</span>';
    }
    
    /**
     * Get keseluruhan status label
     */
    public function getKeseluruhanStatusLabel($status)
    {
        $labels = [
            'Draft' => '<span class="badge bg-secondary">Draft</span>',
            'Menunggu HRD' => '<span class="badge bg-info">Menunggu HRD</span>',
            'Menunggu Direktur' => '<span class="badge bg-warning text-dark">Menunggu Direktur</span>',
            'Disetujui' => '<span class="badge bg-success">Disetujui</span>',
            'Ditolak' => '<span class="badge bg-danger">Ditolak</span>',
            'Dicairkan' => '<span class="badge bg-primary">Dicairkan</span>',
            'Lunas' => '<span class="badge bg-dark">Lunas</span>'
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