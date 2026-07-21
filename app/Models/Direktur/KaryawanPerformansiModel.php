<?php

namespace App\Models\Direktur;

use CodeIgniter\Model;

class KaryawanPerformansiModel extends Model
{
    protected $table = 'karyawan_performansi';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'karyawan_id',
        'periode_tahun',
        'periode_bulan',
        // Target
        'target_kehadiran',
        'realisasi_kehadiran',
        'target_penyelesaian_tugas',
        'realisasi_penyelesaian_tugas',
        'target_kesalahan_kerja',
        'realisasi_kesalahan_kerja',
        'target_kepuasan_client',
        'realisasi_kepuasan_client',
        'target_proaktif',
        'realisasi_proaktif',
        'target_kerjasama_tim',
        'realisasi_kerjasama_tim',
        'target_terlambat',
        'realisasi_terlambat',
        'target_ketidakhadiran',
        'realisasi_ketidakhadiran',
        'target_lembur',
        'realisasi_lembur',
        'target_proyek_selesai',
        'realisasi_proyek_selesai',
        // Skor
        'skor_kehadiran',
        'skor_kualitas_kerja',
        'skor_inisiatif',
        'skor_kedisiplinan',
        'skor_khusus',
        'skor_total',
        'grade',
        'predikat',
        // Catatan
        'catatan_atasan',
        'catatan_karyawan',
        'rekomendasi',
        // Status
        'status',
        'approved_by',
        'approved_at',
        'evaluated_by',
        'evaluated_at',
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
        'periode_tahun' => 'required|integer|min_length[4]|max_length[4]',
        'periode_bulan' => 'required|integer|greater_than[0]|less_than[13]',
    ];
    
    protected $validationMessages = [
        'karyawan_id' => [
            'required' => 'Karyawan harus dipilih',
            'integer' => 'ID Karyawan harus berupa angka'
        ],
        'periode_tahun' => [
            'required' => 'Tahun periode harus diisi',
            'integer' => 'Tahun harus berupa angka',
            'min_length' => 'Tahun harus 4 digit',
            'max_length' => 'Tahun harus 4 digit'
        ],
        'periode_bulan' => [
            'required' => 'Bulan periode harus diisi',
            'integer' => 'Bulan harus berupa angka',
            'greater_than' => 'Bulan harus antara 1-12',
            'less_than' => 'Bulan harus antara 1-12'
        ]
    ];
    
    protected $skipValidation = false;
    
    /**
     * Get grade based on score
     */
    public function getGrade($score)
    {
        if ($score >= 90) {
            return ['grade' => 'A', 'predikat' => 'Sangat Baik'];
        } elseif ($score >= 75) {
            return ['grade' => 'B', 'predikat' => 'Baik'];
        } elseif ($score >= 60) {
            return ['grade' => 'C', 'predikat' => 'Cukup'];
        } elseif ($score >= 50) {
            return ['grade' => 'D', 'predikat' => 'Kurang'];
        } else {
            return ['grade' => 'E', 'predikat' => 'Buruk'];
        }
    }
    
    /**
     * Calculate total score based on weighted components
     */
    public function calculateTotalScore($skor_kehadiran, $skor_kualitas_kerja, $skor_inisiatif, $skor_kedisiplinan, $skor_khusus)
    {
        $total = ($skor_kehadiran * 0.25) + 
                 ($skor_kualitas_kerja * 0.20) + 
                 ($skor_inisiatif * 0.15) + 
                 ($skor_kedisiplinan * 0.20) + 
                 ($skor_khusus * 0.20);
        
        return round($total, 2);
    }
    
    /**
     * Get all performansi data with karyawan info
     */
    public function getAllWithKaryawan($periode_tahun = null, $periode_bulan = null)
    {
        $this->select('karyawan_performansi.*, karyawan.nik, karyawan.nama_lengkap, karyawan.nama_panggilan, karyawan.jabatan, karyawan.departemen')
             ->join('karyawan', 'karyawan.id = karyawan_performansi.karyawan_id')
             ->where('karyawan_performansi.deleted_at', null);
        
        if ($periode_tahun) {
            $this->where('karyawan_performansi.periode_tahun', $periode_tahun);
        }
        
        if ($periode_bulan) {
            $this->where('karyawan_performansi.periode_bulan', $periode_bulan);
        }
        
        $this->orderBy('karyawan_performansi.periode_tahun', 'DESC')
             ->orderBy('karyawan_performansi.periode_bulan', 'DESC')
             ->orderBy('karyawan_performansi.skor_total', 'DESC');
        
        return $this->findAll();
    }
    
    /**
     * Get performansi by karyawan
     */
    public function getByKaryawan($karyawan_id, $limit = null)
    {
        $this->select('karyawan_performansi.*, karyawan.nik, karyawan.nama_lengkap, karyawan.jabatan')
             ->join('karyawan', 'karyawan.id = karyawan_performansi.karyawan_id')
             ->where('karyawan_performansi.karyawan_id', $karyawan_id)
             ->where('karyawan_performansi.deleted_at', null)
             ->orderBy('periode_tahun', 'DESC')
             ->orderBy('periode_bulan', 'DESC');
        
        if ($limit) {
            $this->limit($limit);
        }
        
        return $this->findAll();
    }
    
    /**
     * Get performansi by period
     */
    public function getByPeriod($tahun, $bulan = null)
    {
        $this->select('karyawan_performansi.*, karyawan.nik, karyawan.nama_lengkap, karyawan.nama_panggilan, karyawan.jabatan, karyawan.departemen')
             ->join('karyawan', 'karyawan.id = karyawan_performansi.karyawan_id')
             ->where('karyawan_performansi.periode_tahun', $tahun)
             ->where('karyawan_performansi.deleted_at', null);
        
        if ($bulan) {
            $this->where('karyawan_performansi.periode_bulan', $bulan);
        }
        
        $this->orderBy('karyawan_performansi.skor_total', 'DESC');
        
        return $this->findAll();
    }
    
    /**
     * Get summary statistics for dashboard
     */
    public function getSummaryStats($tahun, $bulan = null)
    {
        $builder = $this->db->table($this->table)
            ->select("
                COUNT(*) as total_karyawan_terdata,
                AVG(skor_total) as rata_rata_skor,
                MAX(skor_total) as skor_tertinggi,
                MIN(skor_total) as skor_terendah,
                SUM(CASE WHEN grade = 'A' THEN 1 ELSE 0 END) as total_grade_a,
                SUM(CASE WHEN grade = 'B' THEN 1 ELSE 0 END) as total_grade_b,
                SUM(CASE WHEN grade = 'C' THEN 1 ELSE 0 END) as total_grade_c,
                SUM(CASE WHEN grade = 'D' THEN 1 ELSE 0 END) as total_grade_d,
                SUM(CASE WHEN grade = 'E' THEN 1 ELSE 0 END) as total_grade_e
            ")
            ->where('periode_tahun', $tahun)
            ->where('deleted_at', null);
        
        if ($bulan) {
            $builder->where('periode_bulan', $bulan);
        }
        
        $result = $builder->get()->getRowArray();
        
        return $result ?: [
            'total_karyawan_terdata' => 0,
            'rata_rata_skor' => 0,
            'skor_tertinggi' => 0,
            'skor_terendah' => 0,
            'total_grade_a' => 0,
            'total_grade_b' => 0,
            'total_grade_c' => 0,
            'total_grade_d' => 0,
            'total_grade_e' => 0
        ];
    }
    
    /**
     * Get performansi trend for a karyawan over time
     */
    public function getTrend($karyawan_id, $limit = 6)
    {
        return $this->select('periode_tahun, periode_bulan, skor_total, grade, predikat')
                    ->where('karyawan_id', $karyawan_id)
                    ->where('deleted_at', null)
                    ->orderBy('periode_tahun', 'ASC')
                    ->orderBy('periode_bulan', 'ASC')
                    ->limit($limit)
                    ->findAll();
    }
    
    /**
     * Get top performers
     */
    public function getTopPerformers($tahun, $bulan = null, $limit = 5)
    {
        $this->select('karyawan_performansi.*, karyawan.nik, karyawan.nama_lengkap, karyawan.nama_panggilan, karyawan.jabatan')
             ->join('karyawan', 'karyawan.id = karyawan_performansi.karyawan_id')
             ->where('karyawan_performansi.periode_tahun', $tahun)
             ->where('karyawan_performansi.deleted_at', null)
             ->orderBy('karyawan_performansi.skor_total', 'DESC')
             ->limit($limit);
        
        if ($bulan) {
            $this->where('karyawan_performansi.periode_bulan', $bulan);
        }
        
        return $this->findAll();
    }
    
    /**
     * Get performansi by grade
     */
    public function getByGrade($grade, $tahun, $bulan = null)
    {
        $this->select('karyawan_performansi.*, karyawan.nik, karyawan.nama_lengkap, karyawan.nama_panggilan, karyawan.jabatan')
             ->join('karyawan', 'karyawan.id = karyawan_performansi.karyawan_id')
             ->where('karyawan_performansi.grade', $grade)
             ->where('karyawan_performansi.periode_tahun', $tahun)
             ->where('karyawan_performansi.deleted_at', null)
             ->orderBy('karyawan_performansi.skor_total', 'DESC');
        
        if ($bulan) {
            $this->where('karyawan_performansi.periode_bulan', $bulan);
        }
        
        return $this->findAll();
    }
    
    /**
     * Check if performansi exists for given karyawan and period
     */
    public function exists($karyawan_id, $tahun, $bulan, $exclude_id = null)
    {
        $builder = $this->db->table($this->table)
            ->where('karyawan_id', $karyawan_id)
            ->where('periode_tahun', $tahun)
            ->where('periode_bulan', $bulan)
            ->where('deleted_at IS NULL');
        
        if ($exclude_id) {
            $builder->where('id !=', $exclude_id);
        }
        
        return $builder->get()->getRowArray() !== null;
    }
    
       /**
     * Get available years from data
     */
    public function getAvailableYears()
    {
        // Gunakan query builder dengan distinct
        $builder = $this->db->table($this->table);
        $builder->select('periode_tahun');
        $builder->distinct();
        $builder->where('deleted_at', null);
        $builder->orderBy('periode_tahun', 'DESC');
        
        $result = $builder->get()->getResultArray();
        
        if (empty($result)) {
            return [date('Y')];
        }
        
        $years = [];
        foreach ($result as $row) {
            $years[] = $row['periode_tahun'];
        }
        
        return $years;
    }
    
    /**
     * Get available months for a year
     */
    public function getAvailableMonths($tahun)
    {
        // Gunakan query builder dengan distinct
        $builder = $this->db->table($this->table);
        $builder->select('periode_bulan');
        $builder->distinct();
        $builder->where('periode_tahun', $tahun);
        $builder->where('deleted_at', null);
        $builder->orderBy('periode_bulan', 'ASC');
        
        $result = $builder->get()->getResultArray();
        
        if (empty($result)) {
            return [];
        }
        
        $months = [];
        foreach ($result as $row) {
            $months[] = $row['periode_bulan'];
        }
        
        return $months;
    }
    /**
     * Update status and approve
     */
    public function approve($id, $userId, $catatan_atasan = null)
    {
        $data = [
            'status' => 'approved',
            'approved_by' => $userId,
            'approved_at' => date('Y-m-d H:i:s'),
            'updated_by' => $userId
        ];
        
        if ($catatan_atasan) {
            $data['catatan_atasan'] = $catatan_atasan;
        }
        
        return $this->update($id, $data);
    }
    
    /**
     * Update status to review
     */
    public function submitReview($id, $userId)
    {
        return $this->update($id, [
            'status' => 'review',
            'evaluated_by' => $userId,
            'evaluated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $userId
        ]);
    }
    
    /**
     * Reject performansi data
     */
    public function reject($id, $userId, $alasan)
    {
        return $this->update($id, [
            'status' => 'rejected',
            'catatan_atasan' => $alasan,
            'updated_by' => $userId
        ]);
    }
    
    /**
     * Close performansi period
     */
    public function close($id, $userId)
    {
        return $this->update($id, [
            'status' => 'closed',
            'updated_by' => $userId
        ]);
    }
    
    /**
     * Calculate all scores based on target and realization
     */
    public function calculateScores($data)
    {
        // Skor Kehadiran (target 100%)
        $skor_kehadiran = $data['realisasi_kehadiran'] ?? 0;
        if ($skor_kehadiran > $data['target_kehadiran']) {
            $skor_kehadiran = $data['target_kehadiran'];
        }
        $skor_kehadiran = ($skor_kehadiran / ($data['target_kehadiran'] ?? 100)) * 100;
        
        // Skor Kualitas Kerja (dari penyelesaian tugas dan kesalahan)
        $skor_penyelesaian = ($data['realisasi_penyelesaian_tugas'] / ($data['target_penyelesaian_tugas'] ?? 100)) * 100;
        $skor_kesalahan = max(0, 100 - ($data['realisasi_kesalahan_kerja'] * 10));
        $skor_kepuasan = ($data['realisasi_kepuasan_client'] / ($data['target_kepuasan_client'] ?? 90)) * 100;
        $skor_kualitas_kerja = ($skor_penyelesaian + $skor_kesalahan + $skor_kepuasan) / 3;
        
        // Skor Inisiatif
        $skor_proaktif = ($data['realisasi_proaktif'] / ($data['target_proaktif'] ?? 85)) * 100;
        $skor_kerjasama = ($data['realisasi_kerjasama_tim'] / ($data['target_kerjasama_tim'] ?? 90)) * 100;
        $skor_inisiatif = ($skor_proaktif + $skor_kerjasama) / 2;
        
        // Skor Kedisiplinan
        $skor_terlambat = max(0, 100 - ($data['realisasi_terlambat'] * 10));
        $skor_hadir = ($data['realisasi_kehadiran'] / 100) * 100;
        $skor_kedisiplinan = ($skor_terlambat + $skor_hadir) / 2;
        
        // Skor Khusus
        $skor_lembur = min(100, ($data['realisasi_lembur'] / max(1, ($data['target_lembur'] ?? 10))) * 100);
        $skor_proyek = ($data['realisasi_proyek_selesai'] / max(1, ($data['target_proyek_selesai'] ?? 1))) * 100;
        $skor_khusus = ($skor_lembur + $skor_proyek) / 2;
        
        // Limit to 100
        $skor_kehadiran = min(100, $skor_kehadiran);
        $skor_kualitas_kerja = min(100, $skor_kualitas_kerja);
        $skor_inisiatif = min(100, $skor_inisiatif);
        $skor_kedisiplinan = min(100, $skor_kedisiplinan);
        $skor_khusus = min(100, $skor_khusus);
        
        // Calculate total
        $skor_total = $this->calculateTotalScore(
            $skor_kehadiran, $skor_kualitas_kerja, $skor_inisiatif, $skor_kedisiplinan, $skor_khusus
        );
        
        // Get grade and predicate
        $gradeInfo = $this->getGrade($skor_total);
        
        return [
            'skor_kehadiran' => round($skor_kehadiran, 2),
            'skor_kualitas_kerja' => round($skor_kualitas_kerja, 2),
            'skor_inisiatif' => round($skor_inisiatif, 2),
            'skor_kedisiplinan' => round($skor_kedisiplinan, 2),
            'skor_khusus' => round($skor_khusus, 2),
            'skor_total' => $skor_total,
            'grade' => $gradeInfo['grade'],
            'predikat' => $gradeInfo['predikat']
        ];
    }
    
    /**
     * Create or update with auto score calculation
     */
    public function saveWithScores($data, $userId = null)
    {
        // Calculate scores
        $scores = $this->calculateScores($data);
        
        // Merge scores into data
        $data = array_merge($data, $scores);
        
        // Add audit fields
        if ($userId) {
            if (empty($data['id'])) {
                $data['created_by'] = $userId;
            }
            $data['updated_by'] = $userId;
        }
        
        // Set default status if not set
        if (empty($data['status'])) {
            $data['status'] = 'draft';
        }
        
        return $this->save($data);
    }
}