<?php

namespace App\Models\Direktur;

use CodeIgniter\Model;

class RingkasanPenggajianModel extends Model
{
    protected $table = 'ringkasan_penggajian';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'karyawan_id',
        'periode_tahun',
        'periode_bulan',
        // Komponen Penghasilan
        'gaji_pokok',
        'tunjangan_jabatan',
        'tunjangan_makan',
        'tunjangan_transport',
        'tunjangan_kesehatan',
        'tunjangan_hari_raya',
        'tunjangan_lainnya',
        'lembur',
        'bonus_kinerja',
        'insentif_proyek',
        'komisi_penjualan',
        'total_penghasilan',
        // Komponen Potongan
        'potongan_bpjs_kesehatan',
        'potongan_bpjs_tenaga_kerja',
        'potongan_pph21',
        'potongan_absensi',
        'potongan_pinjaman',
        'potongan_lainnya',
        'total_potongan',
        'gaji_bersih',
        // Rekap Absensi
        'jumlah_hadir',
        'jumlah_terlambat',
        'jumlah_alpha',
        'jumlah_izin',
        'jumlah_sakit',
        'total_jam_lembur',
        // Status
        'status',
        'approved_by',
        'approved_at',
        'paid_at',
        'payment_method',
        'payment_reference',
        'catatan',
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
     * Calculate total penghasilan
     */
    public function calculateTotalPenghasilan($data)
    {
        $total = ($data['gaji_pokok'] ?? 0) +
                 ($data['tunjangan_jabatan'] ?? 0) +
                 ($data['tunjangan_makan'] ?? 0) +
                 ($data['tunjangan_transport'] ?? 0) +
                 ($data['tunjangan_kesehatan'] ?? 0) +
                 ($data['tunjangan_hari_raya'] ?? 0) +
                 ($data['tunjangan_lainnya'] ?? 0) +
                 ($data['lembur'] ?? 0) +
                 ($data['bonus_kinerja'] ?? 0) +
                 ($data['insentif_proyek'] ?? 0) +
                 ($data['komisi_penjualan'] ?? 0);
        
        return round($total, 2);
    }
    
    /**
     * Calculate total potongan
     */
    public function calculateTotalPotongan($data)
    {
        $total = ($data['potongan_bpjs_kesehatan'] ?? 0) +
                 ($data['potongan_bpjs_tenaga_kerja'] ?? 0) +
                 ($data['potongan_pph21'] ?? 0) +
                 ($data['potongan_absensi'] ?? 0) +
                 ($data['potongan_pinjaman'] ?? 0) +
                 ($data['potongan_lainnya'] ?? 0);
        
        return round($total, 2);
    }
    
    /**
     * Calculate gaji bersih
     */
    public function calculateGajiBersih($total_penghasilan, $total_potongan)
    {
        return round($total_penghasilan - $total_potongan, 2);
    }
    
    /**
     * Calculate all totals automatically
     */
    public function calculateTotals($data)
    {
        $total_penghasilan = $this->calculateTotalPenghasilan($data);
        $total_potongan = $this->calculateTotalPotongan($data);
        $gaji_bersih = $this->calculateGajiBersih($total_penghasilan, $total_potongan);
        
        return [
            'total_penghasilan' => $total_penghasilan,
            'total_potongan' => $total_potongan,
            'gaji_bersih' => $gaji_bersih
        ];
    }
    
    /**
     * Save with auto calculation
     */
    public function saveWithTotals($data, $userId = null)
    {
        // Calculate totals
        $totals = $this->calculateTotals($data);
        $data = array_merge($data, $totals);
        
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
    
    /**
     * Get all payroll data with karyawan info
     */
    public function getAllWithKaryawan($periode_tahun = null, $periode_bulan = null)
    {
        $builder = $this->db->table($this->table)
            ->select('ringkasan_penggajian.*, karyawan.nik, karyawan.nama_lengkap, karyawan.nama_panggilan, karyawan.jabatan, karyawan.departemen')
            ->join('karyawan', 'karyawan.id = ringkasan_penggajian.karyawan_id')
            ->where('ringkasan_penggajian.deleted_at', null);
        
        if ($periode_tahun) {
            $builder->where('ringkasan_penggajian.periode_tahun', $periode_tahun);
        }
        
        if ($periode_bulan) {
            $builder->where('ringkasan_penggajian.periode_bulan', $periode_bulan);
        }
        
        $builder->orderBy('ringkasan_penggajian.periode_tahun', 'DESC')
                ->orderBy('ringkasan_penggajian.periode_bulan', 'DESC')
                ->orderBy('ringkasan_penggajian.gaji_bersih', 'DESC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get payroll by karyawan
     */
    public function getByKaryawan($karyawan_id, $limit = null)
    {
        $builder = $this->db->table($this->table)
            ->select('ringkasan_penggajian.*, karyawan.nik, karyawan.nama_lengkap, karyawan.jabatan')
            ->join('karyawan', 'karyawan.id = ringkasan_penggajian.karyawan_id')
            ->where('ringkasan_penggajian.karyawan_id', $karyawan_id)
            ->where('ringkasan_penggajian.deleted_at', null)
            ->orderBy('periode_tahun', 'DESC')
            ->orderBy('periode_bulan', 'DESC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get payroll by period
     */
    public function getByPeriod($tahun, $bulan = null)
    {
        $builder = $this->db->table($this->table)
            ->select('ringkasan_penggajian.*, karyawan.nik, karyawan.nama_lengkap, karyawan.nama_panggilan, karyawan.jabatan, karyawan.departemen')
            ->join('karyawan', 'karyawan.id = ringkasan_penggajian.karyawan_id')
            ->where('ringkasan_penggajian.periode_tahun', $tahun)
            ->where('ringkasan_penggajian.deleted_at', null);
        
        if ($bulan) {
            $builder->where('ringkasan_penggajian.periode_bulan', $bulan);
        }
        
        $builder->orderBy('ringkasan_penggajian.gaji_bersih', 'DESC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get summary statistics for dashboard
     */
    public function getSummaryStats($tahun, $bulan = null)
    {
        $builder = $this->db->table($this->table)
            ->select("
                COUNT(*) as total_karyawan,
                SUM(total_penghasilan) as total_penghasilan_semua,
                SUM(total_potongan) as total_potongan_semua,
                SUM(gaji_bersih) as total_gaji_bersih,
                AVG(gaji_bersih) as rata_rata_gaji,
                SUM(jumlah_hadir) as total_hadir,
                SUM(total_jam_lembur) as total_lembur,
                SUM(jumlah_terlambat) as total_terlambat,
                SUM(jumlah_alpha) as total_alpha
            ")
            ->where('periode_tahun', $tahun)
            ->where('deleted_at', null);
        
        if ($bulan) {
            $builder->where('periode_bulan', $bulan);
        }
        
        $result = $builder->get()->getRowArray();
        
        return $result ?: [
            'total_karyawan' => 0,
            'total_penghasilan_semua' => 0,
            'total_potongan_semua' => 0,
            'total_gaji_bersih' => 0,
            'rata_rata_gaji' => 0,
            'total_hadir' => 0,
            'total_lembur' => 0,
            'total_terlambat' => 0,
            'total_alpha' => 0
        ];
    }
    
    /**
     * Get payroll by status
     */
    public function getByStatus($status, $tahun, $bulan = null)
    {
        $builder = $this->db->table($this->table)
            ->select('ringkasan_penggajian.*, karyawan.nik, karyawan.nama_lengkap, karyawan.nama_panggilan, karyawan.jabatan')
            ->join('karyawan', 'karyawan.id = ringkasan_penggajian.karyawan_id')
            ->where('ringkasan_penggajian.status', $status)
            ->where('ringkasan_penggajian.periode_tahun', $tahun)
            ->where('ringkasan_penggajian.deleted_at', null);
        
        if ($bulan) {
            $builder->where('ringkasan_penggajian.periode_bulan', $bulan);
        }
        
        $builder->orderBy('ringkasan_penggajian.gaji_bersih', 'DESC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Check if payroll exists for given karyawan and period
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
     * Update status to approved
     */
    public function approve($id, $userId, $catatan = null)
    {
        $data = [
            'status' => 'approved',
            'approved_by' => $userId,
            'approved_at' => date('Y-m-d H:i:s'),
            'updated_by' => $userId
        ];
        
        if ($catatan) {
            $data['catatan'] = $catatan;
        }
        
        return $this->update($id, $data);
    }
    
    /**
     * Update status to paid
     */
    public function markAsPaid($id, $userId, $payment_method = 'transfer', $payment_reference = null)
    {
        $data = [
            'status' => 'paid',
            'paid_at' => date('Y-m-d H:i:s'),
            'payment_method' => $payment_method,
            'payment_reference' => $payment_reference,
            'updated_by' => $userId
        ];
        
        return $this->update($id, $data);
    }
    
    /**
     * Reject payroll data
     */
    public function reject($id, $userId, $alasan)
    {
        return $this->update($id, [
            'status' => 'rejected',
            'catatan' => $alasan,
            'updated_by' => $userId
        ]);
    }
    
    /**
     * Get payroll summary for a specific period (group by department)
     */
    public function getSummaryByDepartment($tahun, $bulan = null)
    {
        $builder = $this->db->table($this->table)
            ->select("
                karyawan.departemen,
                COUNT(ringkasan_penggajian.id) as jumlah_karyawan,
                SUM(ringkasan_penggajian.total_penghasilan) as total_penghasilan,
                SUM(ringkasan_penggajian.total_potongan) as total_potongan,
                SUM(ringkasan_penggajian.gaji_bersih) as total_gaji_bersih
            ")
            ->join('karyawan', 'karyawan.id = ringkasan_penggajian.karyawan_id')
            ->where('ringkasan_penggajian.periode_tahun', $tahun)
            ->where('ringkasan_penggajian.deleted_at', null)
            ->groupBy('karyawan.departemen')
            ->orderBy('total_gaji_bersih', 'DESC');
        
        if ($bulan) {
            $builder->where('ringkasan_penggajian.periode_bulan', $bulan);
        }
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get top earners for a period
     */
    public function getTopEarners($tahun, $bulan = null, $limit = 5)
    {
        $builder = $this->db->table($this->table)
            ->select('ringkasan_penggajian.*, karyawan.nik, karyawan.nama_lengkap, karyawan.nama_panggilan, karyawan.jabatan')
            ->join('karyawan', 'karyawan.id = ringkasan_penggajian.karyawan_id')
            ->where('ringkasan_penggajian.periode_tahun', $tahun)
            ->where('ringkasan_penggajian.deleted_at', null)
            ->orderBy('ringkasan_penggajian.gaji_bersih', 'DESC')
            ->limit($limit);
        
        if ($bulan) {
            $builder->where('ringkasan_penggajian.periode_bulan', $bulan);
        }
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Format currency to Rupiah
     */
    public function formatRupiah($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}