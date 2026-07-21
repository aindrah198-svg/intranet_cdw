<?php
// app/Models/Direktur/SuratJalanModel.php

namespace App\Models\Direktur;

use CodeIgniter\Model;

class SuratJalanModel extends Model
{
    protected $table = 'surat_jalan';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'nomor_surat_jalan',
        'project_id',
        'invoice_id',
        'tanggal_kirim',
        'penerima_nama',
        'alamat_pengiriman',
        'sopir',
        'no_kendaraan',
        'status',
        'status_hrd',
        'status_direktur',
        'disetujui_hrd_oleh',
        'disetujui_direktur_oleh',
        'disetujui_hrd_at',
        'disetujui_direktur_at',
        'alasan_penolakan_hrd',
        'alasan_penolakan_direktur',
        'keterangan',
        'created_by',
        'perusahaan_pengirim_id',
        'perusahaan_pengirim_nama',
        'perusahaan_pengirim_alamat',
        'perusahaan_pengirim_website',
        'penerima_perusahaan',
        'penerima_up',
        'penerima_telepon',
        'lokasi_proyek',
        'disiapkan_oleh',
        'disiapkan_telepon',
        'disiapkan_jabatan',
        'dikirim_oleh',
        'dikirim_telepon',
        'diterima_oleh',
        'diterima_telepon',
        'diterima_perusahaan',
        'status_terima',
        'tanggal_terima',
        'kode_format',
        'bulan_format',
        'tahun_format',
        'catatan_barang'
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    
    protected $validationRules = [
        'nomor_surat_jalan' => 'required',
        'tanggal_kirim' => 'required|valid_date',
        'status' => 'required|in_list[diproses,dikirim,diterima,dibatalkan]'
    ];
    
    protected $validationMessages = [
        'nomor_surat_jalan' => [
            'required' => 'Nomor surat jalan harus diisi'
        ],
        'tanggal_kirim' => [
            'required' => 'Tanggal kirim harus diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ],
        'status' => [
            'required' => 'Status harus dipilih',
            'in_list' => 'Status tidak valid'
        ]
    ];
    
    /**
     * Get all surat jalan for direktur approval
     * Menampilkan surat jalan yang membutuhkan approval direktur (status_direktur = 'Menunggu' dan status_hrd = 'Disetujui HRD')
     */
    public function getForDirekturApproval($status = null, $limit = null, $offset = 0)
    {
        $builder = $this->db->table('surat_jalan');
        
        $builder->select('
            surat_jalan.*,
            project.nama_project,
            project.kode_project,
            client.nama_perusahaan as client_nama,
            invoice.nomor_invoice,
            hrd.nama_lengkap as hrd_nama,
            creator.nama_lengkap as created_by_name
        ');
        
        $builder->join('project', 'project.id = surat_jalan.project_id', 'left');
        $builder->join('client', 'client.id = project.client_id', 'left');
        $builder->join('invoice', 'invoice.id = surat_jalan.invoice_id', 'left');
        $builder->join('users as creator_user', 'creator_user.id = surat_jalan.created_by', 'left');
        $builder->join('karyawan as creator', 'creator.id = creator_user.karyawan_id', 'left');
        $builder->join('karyawan as hrd', 'hrd.id = surat_jalan.disetujui_hrd_oleh', 'left');
        
        // Filter status untuk approval direktur
        if ($status === 'pending') {
            $builder->where('surat_jalan.status_direktur', 'Menunggu');
            $builder->where('surat_jalan.status_hrd', 'Disetujui HRD');
        } elseif ($status === 'approved') {
            $builder->where('surat_jalan.status_direktur', 'Disetujui');
        } elseif ($status === 'rejected') {
            $builder->where('surat_jalan.status_direktur', 'Ditolak');
        } else {
            // Default: tampilkan yang menunggu, disetujui, ditolak
            $builder->whereIn('surat_jalan.status_direktur', ['Menunggu', 'Disetujui', 'Ditolak']);
            $builder->where('surat_jalan.status_hrd !=', 'Draft');
        }
        
        $builder->orderBy('surat_jalan.created_at', 'DESC');
        
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
        $builder = $this->db->table('surat_jalan');
        
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
        return $this->db->table('surat_jalan')
            ->where('status_direktur', 'Menunggu')
            ->where('status_hrd', 'Disetujui HRD')
            ->countAllResults();
    }
    
    /**
     * Get surat jalan detail by ID with complete info
     */
    public function getDetailById($id)
    {
        $builder = $this->db->table('surat_jalan');
        
        $builder->select('
            surat_jalan.*,
            project.nama_project,
            project.kode_project,
            project.deskripsi as project_deskripsi,
            client.nama_perusahaan as client_nama,
            client.alamat as client_alamat,
            client.telepon as client_telepon,
            invoice.nomor_invoice,
            invoice.tanggal_invoice,
            hrd.nama_lengkap as hrd_nama,
            hrd.jabatan as hrd_jabatan,
            direktur.nama_lengkap as direktur_nama,
            direktur.jabatan as direktur_jabatan,
            creator.nama_lengkap as created_by_name,
            creator.jabatan as created_by_jabatan
        ');
        
        $builder->join('project', 'project.id = surat_jalan.project_id', 'left');
        $builder->join('client', 'client.id = project.client_id', 'left');
        $builder->join('invoice', 'invoice.id = surat_jalan.invoice_id', 'left');
        $builder->join('users as creator_user', 'creator_user.id = surat_jalan.created_by', 'left');
        $builder->join('karyawan as creator', 'creator.id = creator_user.karyawan_id', 'left');
        $builder->join('karyawan as hrd', 'hrd.id = surat_jalan.disetujui_hrd_oleh', 'left');
        $builder->join('karyawan as direktur', 'direktur.id = surat_jalan.disetujui_direktur_oleh', 'left');
        
        $builder->where('surat_jalan.id', $id);
        
        return $builder->get()->getRowArray();
    }
    
    /**
     * Get items for a surat jalan
     */
    public function getItems($suratJalanId)
    {
        $builder = $this->db->table('surat_jalan_item');
        $builder->where('surat_jalan_id', $suratJalanId);
        $builder->orderBy('no_urut', 'ASC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Approve surat jalan by direktur
     */
    public function approveByDirektur($id, $userId, $karyawanId)
    {
        $data = [
            'status_direktur' => 'Disetujui',
            'disetujui_direktur_oleh' => $karyawanId,
            'disetujui_direktur_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->update($id, $data);
    }
    
    /**
     * Reject surat jalan by direktur
     */
    public function rejectByDirektur($id, $userId, $karyawanId, $alasan)
    {
        $data = [
            'status_direktur' => 'Ditolak',
            'disetujui_direktur_oleh' => $karyawanId,
            'disetujui_direktur_at' => date('Y-m-d H:i:s'),
            'alasan_penolakan_direktur' => $alasan,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->update($id, $data);
    }
    
    /**
     * Get surat jalan statistics for dashboard
     */
    public function getStatistics($startDate = null, $endDate = null)
    {
        $builder = $this->db->table('surat_jalan');
        
        $builder->select("
            COUNT(*) as total_surat_jalan,
            SUM(CASE WHEN status_direktur = 'Menunggu' AND status_hrd = 'Disetujui HRD' THEN 1 ELSE 0 END) as total_menunggu,
            SUM(CASE WHEN status_direktur = 'Disetujui' THEN 1 ELSE 0 END) as total_disetujui,
            SUM(CASE WHEN status_direktur = 'Ditolak' THEN 1 ELSE 0 END) as total_ditolak,
            SUM(CASE WHEN status = 'dikirim' THEN 1 ELSE 0 END) as total_dikirim,
            SUM(CASE WHEN status = 'diterima' THEN 1 ELSE 0 END) as total_diterima
        ");
        
        if ($startDate) {
            $builder->where('tanggal_kirim >=', $startDate);
        }
        if ($endDate) {
            $builder->where('tanggal_kirim <=', $endDate);
        }
        
        return $builder->get()->getRowArray();
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
     * Get human readable status label for pengiriman
     */
    public function getStatusPengirimanLabel($status)
    {
        $labels = [
            'diproses' => '<span class="badge bg-secondary">Diproses</span>',
            'dikirim' => '<span class="badge bg-primary">Dikirim</span>',
            'diterima' => '<span class="badge bg-success">Diterima</span>',
            'dibatalkan' => '<span class="badge bg-danger">Dibatalkan</span>'
        ];
        
        return $labels[$status] ?? '<span class="badge bg-secondary">' . $status . '</span>';
    }
    
    /**
     * Get human readable status terima label
     */
    public function getStatusTerimaLabel($status)
    {
        $labels = [
            'pending' => '<span class="badge bg-warning text-dark">Pending</span>',
            'diterima' => '<span class="badge bg-success">Diterima</span>',
            'ditolak' => '<span class="badge bg-danger">Ditolak</span>'
        ];
        
        return $labels[$status] ?? '<span class="badge bg-secondary">' . $status . '</span>';
    }
    
    /**
     * Get surat jalan for export
     */
    public function getForExport($startDate = null, $endDate = null, $status = null)
    {
        $builder = $this->db->table('surat_jalan');
        
        $builder->select('
            surat_jalan.nomor_surat_jalan,
            surat_jalan.tanggal_kirim,
            surat_jalan.status,
            surat_jalan.status_hrd,
            surat_jalan.status_direktur,
            surat_jalan.sopir,
            surat_jalan.no_kendaraan,
            surat_jalan.penerima_nama,
            surat_jalan.alamat_pengiriman,
            project.nama_project,
            client.nama_perusahaan as client_nama,
            invoice.nomor_invoice
        ');
        
        $builder->join('project', 'project.id = surat_jalan.project_id', 'left');
        $builder->join('client', 'client.id = project.client_id', 'left');
        $builder->join('invoice', 'invoice.id = surat_jalan.invoice_id', 'left');
        
        if ($startDate) {
            $builder->where('surat_jalan.tanggal_kirim >=', $startDate);
        }
        if ($endDate) {
            $builder->where('surat_jalan.tanggal_kirim <=', $endDate);
        }
        if ($status) {
            $builder->where('surat_jalan.status_direktur', $status);
        }
        
        $builder->orderBy('surat_jalan.tanggal_kirim', 'DESC');
        
        return $builder->get()->getResultArray();
    }
}