<?php
// app/Models/Direktur/CutiModel.php

namespace App\Models\Direktur;

use CodeIgniter\Model;

class CutiModel extends Model
{
    protected $table = 'cuti';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'nomor_cuti',
        'karyawan_id',
        'user_id',
        'jenis_cuti',
        'alasan',
        'tanggal_mulai',
        'tanggal_selesai',
        'lama_hari',
        'sisa_cuti_tahunan',
        'alamat_selama_cuti',
        'no_telepon_cuti',
        'pejabat_penerima_tugas',
        'status_pengajuan',
        'status_atasan',
        'status_hrd',
        'status_direktur',
        'atasan_id',
        'hrd_id',
        'direktur_id',
        'tanggal_disetujui_atasan',
        'tanggal_disetujui_hrd',
        'tanggal_disetujui_direktur',
        'alasan_penolakan_atasan',
        'alasan_penolakan_hrd',
        'alasan_penolakan_direktur',
        'catatan_hrd',
        'tanggal_pengajuan',
        'dokumen_pendukung',
        'created_by',
        'updated_by'
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    
    // Validation rules
    protected $validationRules = [
        'karyawan_id' => 'required|integer',
        'jenis_cuti' => 'required|in_list[Tahunan,Sakit,Hamil,Penting,Izin,Lainnya]',
        'alasan' => 'required|min_length[5]',
        'tanggal_mulai' => 'required|valid_date',
        'tanggal_selesai' => 'required|valid_date',
    ];
    
    protected $validationMessages = [
        'karyawan_id' => [
            'required' => 'Karyawan harus dipilih',
            'integer' => 'ID Karyawan tidak valid'
        ],
        'jenis_cuti' => [
            'required' => 'Jenis cuti harus dipilih',
            'in_list' => 'Jenis cuti tidak valid'
        ],
        'alasan' => [
            'required' => 'Alasan cuti harus diisi',
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
    
    protected $skipValidation = false;
    
    /**
     * Get all cuti with karyawan info for direktur approval
     * Menampilkan data cuti yang membutuhkan approval direktur
     */
    public function getForDirekturApproval($status = null, $limit = null, $offset = 0)
    {
        $builder = $this->db->table('cuti');
        
        $builder->select('
            cuti.*,
            karyawan.nik,
            karyawan.nama_lengkap,
            karyawan.nama_panggilan,
            karyawan.jabatan,
            karyawan.departemen,
            karyawan.foto,
            atasan.nama_lengkap as atasan_nama,
            hrd.nama_lengkap as hrd_nama,
            creator.nama_lengkap as created_by_name,
            creator_karyawan.jabatan as created_by_jabatan
        ');
        
        $builder->join('karyawan', 'karyawan.id = cuti.karyawan_id');
        $builder->join('karyawan as atasan', 'atasan.id = cuti.atasan_id', 'left');
        $builder->join('karyawan as hrd', 'hrd.id = cuti.hrd_id', 'left');
        $builder->join('users as creator_user', 'creator_user.id = cuti.created_by', 'left');
        $builder->join('karyawan as creator', 'creator.id = creator_user.karyawan_id', 'left');
        $builder->join('karyawan as creator_karyawan', 'creator_karyawan.id = creator_user.karyawan_id', 'left');
        
        $builder->where('cuti.deleted_at', null);
        
        // Filter by status yang membutuhkan approval direktur
        if ($status === 'pending') {
            $builder->where('cuti.status_direktur', 'Menunggu');
            $builder->where('cuti.status_hrd', 'Disetujui'); // Sudah approved HRD
        } elseif ($status === 'all') {
            // Semua data
        } elseif ($status) {
            $builder->where('cuti.status_direktur', $status);
        } else {
            // Default: menampilkan yang menunggu dan sudah diproses (tidak termasuk draft)
            $builder->whereIn('cuti.status_direktur', ['Menunggu', 'Disetujui', 'Ditolak']);
            $builder->where('cuti.status_pengajuan !=', 'Draft');
        }
        
        $builder->orderBy('cuti.tanggal_pengajuan', 'DESC');
        
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
        $builder = $this->db->table('cuti');
        $builder->where('cuti.deleted_at', null);
        
        if ($status === 'pending') {
            $builder->where('cuti.status_direktur', 'Menunggu');
            $builder->where('cuti.status_hrd', 'Disetujui');
        } elseif ($status === 'all') {
            // Semua
        } elseif ($status) {
            $builder->where('cuti.status_direktur', $status);
        } else {
            $builder->whereIn('cuti.status_direktur', ['Menunggu', 'Disetujui', 'Ditolak']);
            $builder->where('cuti.status_pengajuan !=', 'Draft');
        }
        
        return $builder->countAllResults();
    }
    
    /**
     * Get cuti detail by ID with complete info
     */
    public function getDetailById($id)
    {
        $builder = $this->db->table('cuti');
        
        $builder->select('
            cuti.*,
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
            direktur.nama_lengkap as direktur_nama,
            direktur.jabatan as direktur_jabatan,
            creator.nama_lengkap as created_by_name,
            updater.nama_lengkap as updated_by_name
        ');
        
        $builder->join('karyawan', 'karyawan.id = cuti.karyawan_id');
        $builder->join('karyawan as atasan', 'atasan.id = cuti.atasan_id', 'left');
        $builder->join('karyawan as hrd', 'hrd.id = cuti.hrd_id', 'left');
        $builder->join('karyawan as direktur', 'direktur.id = cuti.direktur_id', 'left');
        $builder->join('users as creator_user', 'creator_user.id = cuti.created_by', 'left');
        $builder->join('karyawan as creator', 'creator.id = creator_user.karyawan_id', 'left');
        $builder->join('users as updater_user', 'updater_user.id = cuti.updated_by', 'left');
        $builder->join('karyawan as updater', 'updater.id = updater_user.karyawan_id', 'left');
        
        $builder->where('cuti.id', $id);
        $builder->where('cuti.deleted_at', null);
        
        return $builder->get()->getRowArray();
    }
    
    /**
     * Approve cuti by direktur
     */
    public function approveByDirektur($id, $userId, $karyawanId)
    {
        $data = [
            'status_direktur' => 'Disetujui',
            'direktur_id' => $karyawanId,
            'tanggal_disetujui_direktur' => date('Y-m-d H:i:s'),
            'updated_by' => $userId,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        // Update status pengajuan jika semua approval sudah selesai
        $cuti = $this->find($id);
        if ($cuti && $cuti['status_atasan'] == 'Disetujui' && $cuti['status_hrd'] == 'Disetujui') {
            $data['status_pengajuan'] = 'Disetujui';
        }
        
        return $this->update($id, $data);
    }
    
    /**
     * Reject cuti by direktur
     */
    public function rejectByDirektur($id, $userId, $karyawanId, $alasan)
    {
        $data = [
            'status_direktur' => 'Ditolak',
            'status_pengajuan' => 'Ditolak',
            'direktur_id' => $karyawanId,
            'tanggal_disetujui_direktur' => date('Y-m-d H:i:s'),
            'alasan_penolakan_direktur' => $alasan,
            'updated_by' => $userId,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->update($id, $data);
    }
    
    /**
     * Get pending approval count for badge notification
     */
    public function getPendingCount()
    {
        return $this->db->table('cuti')
            ->where('status_direktur', 'Menunggu')
            ->where('status_hrd', 'Disetujui')
            ->where('deleted_at', null)
            ->countAllResults();
    }
    
    /**
     * Get cuti by date range
     */
    public function getByDateRange($startDate, $endDate, $status = null)
    {
        $builder = $this->db->table('cuti');
        
        $builder->select('
            cuti.*,
            karyawan.nama_lengkap,
            karyawan.nama_panggilan,
            karyawan.jabatan,
            karyawan.departemen
        ');
        
        $builder->join('karyawan', 'karyawan.id = cuti.karyawan_id');
        $builder->where('cuti.deleted_at', null);
        $builder->where('cuti.tanggal_mulai >=', $startDate);
        $builder->where('cuti.tanggal_mulai <=', $endDate);
        
        if ($status) {
            $builder->where('cuti.status_pengajuan', $status);
        }
        
        $builder->orderBy('cuti.tanggal_mulai', 'ASC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get cuti statistics for dashboard
     */
    public function getStatistics($year = null, $month = null)
    {
        if (!$year) {
            $year = date('Y');
        }
        
        $builder = $this->db->table('cuti');
        $builder->select("
            COUNT(*) as total_pengajuan,
            SUM(CASE WHEN status_pengajuan = 'Disetujui' THEN 1 ELSE 0 END) as total_disetujui,
            SUM(CASE WHEN status_pengajuan = 'Ditolak' THEN 1 ELSE 0 END) as total_ditolak,
            SUM(CASE WHEN status_pengajuan = 'Menunggu Atasan' THEN 1 ELSE 0 END) as total_menunggu_atasan,
            SUM(CASE WHEN status_pengajuan = 'Menunggu HRD' THEN 1 ELSE 0 END) as total_menunggu_hrd,
            SUM(CASE WHEN status_direktur = 'Menunggu' AND status_hrd = 'Disetujui' THEN 1 ELSE 0 END) as total_menunggu_direktur,
            SUM(lama_hari) as total_hari_cuti
        ");
        
        $builder->where('YEAR(tanggal_pengajuan)', $year);
        $builder->where('deleted_at', null);
        
        if ($month) {
            $builder->where('MONTH(tanggal_pengajuan)', $month);
        }
        
        return $builder->get()->getRowArray();
    }
    
    /**
     * Get cuti by jenis for chart
     */
    public function getByJenis($year = null, $month = null)
    {
        if (!$year) {
            $year = date('Y');
        }
        
        $builder = $this->db->table('cuti');
        $builder->select("
            jenis_cuti,
            COUNT(*) as total,
            SUM(lama_hari) as total_hari
        ");
        
        $builder->where('YEAR(tanggal_pengajuan)', $year);
        $builder->where('status_pengajuan', 'Disetujui');
        $builder->where('deleted_at', null);
        $builder->groupBy('jenis_cuti');
        
        if ($month) {
            $builder->where('MONTH(tanggal_pengajuan)', $month);
        }
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get cuti by month for chart
     */
    public function getByMonth($year = null)
    {
        if (!$year) {
            $year = date('Y');
        }
        
        $builder = $this->db->table('cuti');
        $builder->select("
            MONTH(tanggal_mulai) as bulan,
            COUNT(*) as total_pengajuan,
            SUM(lama_hari) as total_hari
        ");
        
        $builder->where('YEAR(tanggal_mulai)', $year);
        $builder->where('status_pengajuan', 'Disetujui');
        $builder->where('deleted_at', null);
        $builder->groupBy('MONTH(tanggal_mulai)');
        $builder->orderBy('bulan', 'ASC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Export cuti data to array
     */
    public function getForExport($startDate = null, $endDate = null, $status = null)
    {
        $builder = $this->db->table('cuti');
        
        $builder->select('
            cuti.nomor_cuti,
            cuti.jenis_cuti,
            cuti.alasan,
            cuti.tanggal_mulai,
            cuti.tanggal_selesai,
            cuti.lama_hari,
            cuti.status_pengajuan,
            cuti.status_atasan,
            cuti.status_hrd,
            cuti.status_direktur,
            cuti.tanggal_pengajuan,
            cuti.tanggal_disetujui_atasan,
            cuti.tanggal_disetujui_hrd,
            cuti.tanggal_disetujui_direktur,
            karyawan.nik,
            karyawan.nama_lengkap,
            karyawan.jabatan,
            karyawan.departemen,
            atasan.nama_lengkap as atasan_nama,
            hrd.nama_lengkap as hrd_nama
        ');
        
        $builder->join('karyawan', 'karyawan.id = cuti.karyawan_id');
        $builder->join('karyawan as atasan', 'atasan.id = cuti.atasan_id', 'left');
        $builder->join('karyawan as hrd', 'hrd.id = cuti.hrd_id', 'left');
        $builder->where('cuti.deleted_at', null);
        
        if ($startDate) {
            $builder->where('cuti.tanggal_pengajuan >=', $startDate);
        }
        if ($endDate) {
            $builder->where('cuti.tanggal_pengajuan <=', $endDate);
        }
        if ($status) {
            $builder->where('cuti.status_pengajuan', $status);
        }
        
        $builder->orderBy('cuti.tanggal_pengajuan', 'DESC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get human readable status label
     */
    public function getStatusLabel($status)
    {
        $labels = [
            'Draft' => '<span class="badge bg-secondary">Draft</span>',
            'Menunggu Atasan' => '<span class="badge bg-info">Menunggu Atasan</span>',
            'Menunggu HRD' => '<span class="badge bg-primary">Menunggu HRD</span>',
            'Disetujui' => '<span class="badge bg-success">Disetujui</span>',
            'Ditolak' => '<span class="badge bg-danger">Ditolak</span>',
            'Dibatalkan' => '<span class="badge bg-dark">Dibatalkan</span>'
        ];
        
        return $labels[$status] ?? '<span class="badge bg-secondary">' . $status . '</span>';
    }
    
    /**
     * Get human readable jenis cuti label
     */
    public function getJenisCutiLabel($jenis)
    {
        $labels = [
            'Tahunan' => '<span class="badge bg-success">Cuti Tahunan</span>',
            'Sakit' => '<span class="badge bg-danger">Cuti Sakit</span>',
            'Hamil' => '<span class="badge bg-warning">Cuti Hamil</span>',
            'Penting' => '<span class="badge bg-info">Cuti Penting</span>',
            'Izin' => '<span class="badge bg-secondary">Izin</span>',
            'Lainnya' => '<span class="badge bg-dark">Lainnya</span>'
        ];
        
        return $labels[$jenis] ?? '<span class="badge bg-secondary">' . $jenis . '</span>';
    }
    
    /**
     * Get direktur approval status label
     */
    public function getDirekturStatusLabel($status)
    {
        $labels = [
            'Menunggu' => '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Menunggu</span>',
            'Disetujui' => '<span class="badge bg-success"><i class="fas fa-check me-1"></i>Disetujui</span>',
            'Ditolak' => '<span class="badge bg-danger"><i class="fas fa-times me-1"></i>Ditolak</span>',
            'Tidak Diperlukan' => '<span class="badge bg-secondary"><i class="fas fa-minus me-1"></i>Tidak Diperlukan</span>'
        ];
        
        return $labels[$status] ?? '<span class="badge bg-secondary">' . $status . '</span>';
    }
}