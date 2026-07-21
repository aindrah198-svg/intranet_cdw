<?php
// app/Models/Direktur/IzinModel.php

namespace App\Models\Direktur;

use CodeIgniter\Model;

class IzinModel extends Model
{
    protected $table = 'form_izin';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'nomor_izin',
        'karyawan_id',
        'tanggal_pengajuan',
        'jenis_izin',
        'alasan',
        'tanggal_mulai',
        'tanggal_selesai',
        'lama_hari',
        'jam_keluar',
        'jam_kembali',
        'dokumen_pendukung',
        'status_atasan',
        'status_hrd',
        'status_keseluruhan',
        'atasan_id',
        'hrd_id',
        'tanggal_disetujui_atasan',
        'tanggal_disetujui_hrd',
        'alasan_penolakan_atasan',
        'alasan_penolakan_hrd',
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
        'jenis_izin' => 'required|in_list[Izin,Sakit Ringan,Keperluan Keluarga,Keperluan Mendadak,Lainnya]',
        'alasan' => 'required|min_length[5]',
        'tanggal_mulai' => 'required|valid_date',
        'tanggal_selesai' => 'required|valid_date',
    ];
    
    protected $validationMessages = [
        'karyawan_id' => [
            'required' => 'Karyawan harus dipilih',
            'integer' => 'ID Karyawan tidak valid'
        ],
        'jenis_izin' => [
            'required' => 'Jenis izin harus dipilih',
            'in_list' => 'Jenis izin tidak valid'
        ],
        'alasan' => [
            'required' => 'Alasan izin harus diisi',
            'min_length' => 'Alasan minimal 5 karakter'
        ],
        'tanggal_mulai' => [
            'required' => 'Tanggal mulai harus diisi',
            'valid_date' => 'Format tanggal mulai tidak valid'
        ],
        'tanggal_selesai' => [
            'required' => 'Tanggal selesai harus diisi',
            'valid_date' => 'Format tanggal selesai tidak valid'
        ]
    ];
    
    /**
     * Get all form izin for direktur approval
     * Menampilkan form izin yang membutuhkan approval direktur (status_hrd = 'Disetujui')
     * Approval direktur diperlukan untuk izin > 2 hari atau izin tertentu
     */
    public function getForDirekturApproval($status = null, $limit = null, $offset = 0)
    {
        $builder = $this->db->table('form_izin');
        
        $builder->select('
            form_izin.*,
            karyawan.nik,
            karyawan.nama_lengkap,
            karyawan.nama_panggilan,
            karyawan.jabatan,
            karyawan.departemen,
            karyawan.foto,
            atasan.nama_lengkap as atasan_nama,
            hrd.nama_lengkap as hrd_nama,
            creator.nama_lengkap as created_by_name
        ');
        
        $builder->join('karyawan', 'karyawan.id = form_izin.karyawan_id');
        $builder->join('users as creator_user', 'creator_user.id = form_izin.created_by', 'left');
        $builder->join('karyawan as creator', 'creator.id = creator_user.karyawan_id', 'left');
        $builder->join('karyawan as atasan', 'atasan.id = form_izin.atasan_id', 'left');
        $builder->join('karyawan as hrd', 'hrd.id = form_izin.hrd_id', 'left');
        
        $builder->where('form_izin.deleted_at', null);
        
        // Filter status untuk approval direktur
        if ($status === 'pending') {
            $builder->where('form_izin.status_hrd', 'Disetujui');
            $builder->where('form_izin.status_keseluruhan', 'Menunggu');
        } elseif ($status === 'approved') {
            $builder->where('form_izin.status_keseluruhan', 'Disetujui');
        } elseif ($status === 'rejected') {
            $builder->where('form_izin.status_keseluruhan', 'Ditolak');
        } else {
            // Default: tampilkan yang menunggu, disetujui, ditolak
            $builder->whereIn('form_izin.status_keseluruhan', ['Menunggu', 'Disetujui', 'Ditolak']);
            $builder->where('form_izin.status_hrd', 'Disetujui');
        }
        
        $builder->orderBy('form_izin.tanggal_pengajuan', 'DESC');
        
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
        $builder = $this->db->table('form_izin');
        $builder->where('deleted_at', null);
        
        if ($status === 'pending') {
            $builder->where('status_hrd', 'Disetujui');
            $builder->where('status_keseluruhan', 'Menunggu');
        } elseif ($status === 'all') {
            // Semua
        } elseif ($status) {
            $builder->where('status_keseluruhan', $status);
        } else {
            $builder->whereIn('status_keseluruhan', ['Menunggu', 'Disetujui', 'Ditolak']);
            $builder->where('status_hrd', 'Disetujui');
        }
        
        return $builder->countAllResults();
    }
    
    /**
     * Get pending count for badge notification
     */
    public function getPendingCount()
    {
        return $this->db->table('form_izin')
            ->where('status_hrd', 'Disetujui')
            ->where('status_keseluruhan', 'Menunggu')
            ->where('deleted_at', null)
            ->countAllResults();
    }
    
    /**
     * Get form izin detail by ID with complete info
     */
    public function getDetailById($id)
    {
        $builder = $this->db->table('form_izin');
        
        $builder->select('
            form_izin.*,
            karyawan.nik,
            karyawan.nama_lengkap,
            karyawan.nama_panggilan,
            karyawan.jabatan,
            karyawan.departemen,
            karyawan.tanggal_masuk as karyawan_tanggal_masuk,
            karyawan.foto,
            atasan.nama_lengkap as atasan_nama,
            atasan.jabatan as atasan_jabatan,
            hrd.nama_lengkap as hrd_nama,
            hrd.jabatan as hrd_jabatan,
            creator.nama_lengkap as created_by_name,
            creator.jabatan as created_by_jabatan
        ');
        
        $builder->join('karyawan', 'karyawan.id = form_izin.karyawan_id');
        $builder->join('users as creator_user', 'creator_user.id = form_izin.created_by', 'left');
        $builder->join('karyawan as creator', 'creator.id = creator_user.karyawan_id', 'left');
        $builder->join('karyawan as atasan', 'atasan.id = form_izin.atasan_id', 'left');
        $builder->join('karyawan as hrd', 'hrd.id = form_izin.hrd_id', 'left');
        
        $builder->where('form_izin.id', $id);
        $builder->where('form_izin.deleted_at', null);
        
        return $builder->get()->getRowArray();
    }
    
    /**
     * Approve form izin by direktur
     */
    public function approveByDirektur($id, $userId, $karyawanId)
    {
        $data = [
            'status_keseluruhan' => 'Disetujui',
            'updated_by' => $userId,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->update($id, $data);
    }
    
    /**
     * Reject form izin by direktur
     */
    public function rejectByDirektur($id, $userId, $karyawanId, $alasan)
    {
        $data = [
            'status_keseluruhan' => 'Ditolak',
            'alasan_penolakan_atasan' => $alasan,
            'updated_by' => $userId,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->update($id, $data);
    }
    
    /**
     * Get form izin statistics for dashboard
     */
    public function getStatistics($startDate = null, $endDate = null)
    {
        $builder = $this->db->table('form_izin');
        
        $builder->select("
            COUNT(*) as total_izin,
            SUM(CASE WHEN status_hrd = 'Disetujui' AND status_keseluruhan = 'Menunggu' THEN 1 ELSE 0 END) as total_menunggu,
            SUM(CASE WHEN status_keseluruhan = 'Disetujui' THEN 1 ELSE 0 END) as total_disetujui,
            SUM(CASE WHEN status_keseluruhan = 'Ditolak' THEN 1 ELSE 0 END) as total_ditolak,
            SUM(CASE WHEN jenis_izin = 'Sakit Ringan' THEN 1 ELSE 0 END) as total_sakit,
            SUM(lama_hari) as total_hari_izin
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
     * Get jenis izin list
     */
    public function getJenisIzinList()
    {
        return [
            'Izin' => 'Izin',
            'Sakit Ringan' => 'Sakit Ringan',
            'Keperluan Keluarga' => 'Keperluan Keluarga',
            'Keperluan Mendadak' => 'Keperluan Mendadak',
            'Lainnya' => 'Lainnya'
        ];
    }
    
    /**
     * Get human readable status label
     */
    public function getStatusLabel($status)
    {
        $labels = [
            'Menunggu' => '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Menunggu</span>',
            'Disetujui' => '<span class="badge bg-success"><i class="fas fa-check me-1"></i>Disetujui</span>',
            'Ditolak' => '<span class="badge bg-danger"><i class="fas fa-times me-1"></i>Ditolak</span>',
            'Dibatalkan' => '<span class="badge bg-secondary"><i class="fas fa-ban me-1"></i>Dibatalkan</span>'
        ];
        
        return $labels[$status] ?? '<span class="badge bg-secondary">' . $status . '</span>';
    }
    
    /**
     * Get jenis izin badge
     */
    public function getJenisIzinBadge($jenis)
    {
        $badges = [
            'Izin' => '<span class="badge bg-info">Izin</span>',
            'Sakit Ringan' => '<span class="badge bg-danger">Sakit Ringan</span>',
            'Keperluan Keluarga' => '<span class="badge bg-warning text-dark">Keperluan Keluarga</span>',
            'Keperluan Mendadak' => '<span class="badge bg-primary">Keperluan Mendadak</span>',
            'Lainnya' => '<span class="badge bg-secondary">Lainnya</span>'
        ];
        
        return $badges[$jenis] ?? '<span class="badge bg-secondary">' . $jenis . '</span>';
    }
    
    /**
     * Get form izin for export
     */
    public function getForExport($startDate = null, $endDate = null, $status = null)
    {
        $builder = $this->db->table('form_izin');
        
        $builder->select('
            form_izin.nomor_izin,
            form_izin.tanggal_pengajuan,
            form_izin.jenis_izin,
            form_izin.alasan,
            form_izin.tanggal_mulai,
            form_izin.tanggal_selesai,
            form_izin.lama_hari,
            form_izin.jam_keluar,
            form_izin.jam_kembali,
            form_izin.status_atasan,
            form_izin.status_hrd,
            form_izin.status_keseluruhan,
            karyawan.nik,
            karyawan.nama_lengkap,
            karyawan.jabatan,
            karyawan.departemen
        ');
        
        $builder->join('karyawan', 'karyawan.id = form_izin.karyawan_id');
        $builder->where('form_izin.deleted_at', null);
        
        if ($startDate) {
            $builder->where('form_izin.tanggal_pengajuan >=', $startDate);
        }
        if ($endDate) {
            $builder->where('form_izin.tanggal_pengajuan <=', $endDate . ' 23:59:59');
        }
        if ($status) {
            $builder->where('form_izin.status_keseluruhan', $status);
        }
        
        $builder->orderBy('form_izin.tanggal_pengajuan', 'DESC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Calculate working hours difference between jam_keluar and jam_kembali
     */
    public function calculateJamIzin($jamKeluar, $jamKembali)
    {
        if (empty($jamKeluar) || empty($jamKembali)) {
            return 0;
        }
        
        $keluar = strtotime($jamKeluar);
        $kembali = strtotime($jamKembali);
        
        if ($kembali <= $keluar) {
            return 0;
        }
        
        $selisih = $kembali - $keluar;
        $jam = floor($selisih / 3600);
        $menit = floor(($selisih % 3600) / 60);
        
        if ($jam > 0 && $menit > 0) {
            return $jam . ' jam ' . $menit . ' menit';
        } elseif ($jam > 0) {
            return $jam . ' jam';
        } else {
            return $menit . ' menit';
        }
    }
}