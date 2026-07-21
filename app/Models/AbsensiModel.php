<?php
// C:\xampp\htdocs\cdwnet\app\Models\AbsensiModel.php

namespace App\Models;

use CodeIgniter\Model;

class AbsensiModel extends Model
{
    protected $table = 'absensi';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    
protected $allowedFields = [
    'karyawan_id', 'tanggal', 'waktu_masuk', 'waktu_pulang',
    'lokasi_masuk', 'lokasi_pulang', 'latitude_masuk', 'longitude_masuk',
    'latitude_pulang', 'longitude_pulang', 'status', 'jam_kerja',
    'jam_lembur', 'terlambat', 'keterangan', 'device_masuk', 'device_pulang', 
    'shift', 'jam_shift_mulai', 'jam_shift_selesai',
    'location_metadata_masuk', 'location_metadata_pulang', // tambahkan ini
    'created_by', 'updated_by' // tambahkan untuk audit trail
];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    
    // Validation rules
    protected $validationRules = [
        'karyawan_id' => 'required|integer',
        'tanggal' => 'required|valid_date',
    ];
    
    protected $validationMessages = [
        'karyawan_id' => [
            'required' => 'ID Karyawan harus diisi',
            'integer' => 'ID Karyawan harus berupa angka'
        ],
        'tanggal' => [
            'required' => 'Tanggal absensi harus diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ]
    ];
    
    protected $skipValidation = false;
    
    // ========== SHIFT METHODS ==========
    
    /**
     * Get jam mulai shift berdasarkan jenis shift
     */
    public function getJamMulaiByShift($shift)
    {
        $shift_times = [
            'pagi' => '07:00:00',
            'siang' => '08:00:00',
            'sore' => '09:00:00',
            'malam' => '20:00:00'
        ];
        
        return $shift_times[$shift] ?? '08:00:00';
    }
    
    /**
     * Get jam selesai shift berdasarkan jenis shift
     */
    public function getJamSelesaiByShift($shift)
    {
        $shift_times = [
            'pagi' => '16:00:00',
            'siang' => '17:00:00',
            'sore' => '18:00:00',
            'malam' => '05:00:00' // Besok pagi
        ];
        
        return $shift_times[$shift] ?? '17:00:00';
    }
    
    /**
     * Get nama shift untuk display
     */
    public function getNamaShift($shift)
    {
        $shift_names = [
            'pagi' => 'Shift Pagi',
            'siang' => 'Shift Siang',
            'sore' => 'Shift Sore',
            'malam' => 'Shift Malam'
        ];
        
        return $shift_names[$shift] ?? 'Shift';
    }
    
    /**
     * Get durasi shift dalam jam (tanpa istirahat)
     */
    public function getDurasiShift($shift)
    {
        $shift_durations = [
            'pagi' => 8,    // 7-16 = 9 jam, minus 1 jam istirahat = 8 jam
            'siang' => 8,   // 8-17 = 9 jam, minus 1 jam istirahat = 8 jam
            'sore' => 8,    // 9-18 = 9 jam, minus 1 jam istirahat = 8 jam
            'malam' => 9    // 20-5 = 9 jam, minus 0.5 jam istirahat = 8.5 jam (tapi biasanya dihitung 9 jam kerja)
        ];
        
        return $shift_durations[$shift] ?? 8;
    }
    
    /**
     * Get break duration in hours for a specific shift
     */
    public function getBreakDuration($shift)
    {
        $break_durations = [
            'pagi' => 1.0,    // 1 hour break for morning shift (07:00-16:00)
            'siang' => 1.0,   // 1 hour break for day shift (08:00-17:00)
            'sore' => 1.0,    // 1 hour break for evening shift (09:00-18:00)
            'malam' => 0.5    // 30 minutes break for night shift (20:00-05:00)
        ];
        
        return $break_durations[$shift] ?? 1.0;
    }
    
    // ========== CALCULATION METHODS ==========
    
    /**
     * Calculate working hours WITH break deduction
     * REVISED: Now includes 1 hour break deduction for day shifts
     */
    public function calculateJamKerja($waktu_masuk, $waktu_pulang, $shift = 'siang')
    {
        if (!$waktu_masuk || !$waktu_pulang) {
            return 0;
        }
        
        // Konversi ke format 24 jam jika perlu
        $waktu_masuk = $this->convertTimeStringTo24Hour($waktu_masuk);
        $waktu_pulang = $this->convertTimeStringTo24Hour($waktu_pulang);
        
        if (!$waktu_masuk || !$waktu_pulang) {
            return 0;
        }
        
        $masuk = strtotime($waktu_masuk);
        $pulang = strtotime($waktu_pulang);
        
        // Jika pulang lebih kecil dari masuk (misal lewat tengah malam, khusus shift malam)
        if ($pulang < $masuk) {
            $pulang += 86400; // Tambah 24 jam
        }
        
        $selisih_detik = $pulang - $masuk;
        $jam_kerja_total = $selisih_detik / 3600; // konversi ke jam
        
        // Apply break time deduction based on shift
        $jam_kerja_setelah_istirahat = $this->applyBreakDeduction($jam_kerja_total, $shift);
        
        // Pastikan jam kerja minimal 0
        $jam_kerja_setelah_istirahat = max(0, $jam_kerja_setelah_istirahat);
        
        // Format 2 decimal
        return round($jam_kerja_setelah_istirahat, 2);
    }
    
    /**
     * Calculate total working hours WITHOUT break deduction (for comparison)
     */
    public function calculateJamKerjaTotal($waktu_masuk, $waktu_pulang)
    {
        if (!$waktu_masuk || !$waktu_pulang) {
            return 0;
        }
        
        $waktu_masuk = $this->convertTimeStringTo24Hour($waktu_masuk);
        $waktu_pulang = $this->convertTimeStringTo24Hour($waktu_pulang);
        
        if (!$waktu_masuk || !$waktu_pulang) {
            return 0;
        }
        
        $masuk = strtotime($waktu_masuk);
        $pulang = strtotime($waktu_pulang);
        
        if ($pulang < $masuk) {
            $pulang += 86400;
        }
        
        $selisih_detik = $pulang - $masuk;
        $jam_total = $selisih_detik / 3600;
        
        return round($jam_total, 2);
    }
    
    /**
     * Apply break time deduction based on shift type
     * Rules:
     * - Shift Pagi/Siang/Sore: Deduct 1 hour if work > 4 hours
     * - Shift Malam: Deduct 30 minutes if work > 4 hours
     */
    private function applyBreakDeduction($jam_kerja_total, $shift)
    {
        // Minimum work hours to qualify for break
        $min_hours_for_break = 4;
        
        if ($jam_kerja_total <= $min_hours_for_break) {
            // Tidak ada potongan untuk kerja singkat (kurang dari 4 jam)
            return $jam_kerja_total;
        }
        
        // Determine break duration based on shift
        $break_duration = $this->getBreakDuration($shift);
        
        // Apply break deduction
        $jam_setelah_istirahat = $jam_kerja_total - $break_duration;
        
        // Ensure minimum of 0 hours
        return max(0, $jam_setelah_istirahat);
    }
    
    /**
     * Check terlambat berdasarkan shift
     */
    public function checkTerlambat($waktu_masuk, $shift = 'siang')
    {
        if (!$waktu_masuk) {
            return 0;
        }
        
        // Konversi ke format 24 jam jika perlu
        $waktu_masuk = $this->convertTimeStringTo24Hour($waktu_masuk);
        if (!$waktu_masuk) {
            return 0;
        }
        
        // Tentukan jam mulai berdasarkan shift
        $jam_mulai_shift = $this->getJamMulaiByShift($shift);
        $toleransi = 30; // 30 menit untuk semua shift
        
        $jam_masuk = strtotime($waktu_masuk);
        $jam_mulai_normal = strtotime($jam_mulai_shift);
        $batas_toleransi = strtotime($jam_mulai_shift) + ($toleransi * 60);
        
        // Jika masuk dalam toleransi, tepat waktu
        if ($jam_masuk <= $batas_toleransi) {
            return 0;
        }
        
        // Hitung terlambat
        if ($jam_masuk > $jam_mulai_normal) {
            $selisih = $jam_masuk - $jam_mulai_normal;
            $terlambat_menit = (int) ceil($selisih / 60);
            
            // Batasi maksimal 8 jam (480 menit)
            return min($terlambat_menit, 480);
        }
        
        return 0;
    }
    
    /**
     * Calculate lembur
     */
    public function calculateLembur($waktu_pulang, $jam_shift_selesai = '17:00:00')
    {
        if (empty($waktu_pulang)) {
            return 0;
        }
        
        // Konversi ke format 24 jam jika perlu
        $waktu_pulang = $this->convertTimeStringTo24Hour($waktu_pulang);
        
        if (!$waktu_pulang) {
            return 0;
        }
        
        $jam_pulang = strtotime($waktu_pulang);
        $jam_selesai_shift = strtotime($jam_shift_selesai);
        
        // Untuk shift malam (selesai 05:00), perlu penanganan khusus
        if ($jam_shift_selesai == '05:00:00') {
            // Jika pulang sebelum jam 12:00, berarti masih dalam shift malam
            if ($jam_pulang < strtotime('12:00:00')) {
                return 0;
            }
            // Jika pulang setelah jam 12:00, jam_selesai_shift ditambah 24 jam
            $jam_selesai_shift += 86400; // Tambah 24 jam
        }
        
        // Jika pulang sebelum atau sama dengan jam selesai shift → TIDAK LEMBUR
        if ($jam_pulang <= $jam_selesai_shift) {
            return 0;
        }
        
        // Hitung lembur (dalam jam)
        $selisih_detik = $jam_pulang - $jam_selesai_shift;
        $jam_lembur = $selisih_detik / 3600;
        
        // Bulatkan 2 digit di belakang koma
        return round(max(0, $jam_lembur), 2);
    }
    
    /**
     * Calculate jam kerja efektif (jam kerja setelah dikurangi terlambat)
     */
    public function calculateJamKerjaEfektif($jam_kerja, $terlambat_menit)
    {
        $terlambat_jam = $terlambat_menit / 60;
        $jam_efektif = $jam_kerja - $terlambat_jam;
        
        return round(max(0, $jam_efektif), 2);
    }
    
    // ========== HELPER METHODS ==========
    
    /**
     * Convert time string to 24 hour format
     */
    private function convertTimeStringTo24Hour($timeString)
    {
        if (empty($timeString)) {
            return null;
        }
        
        // Jika sudah format 24 jam (HH:MM:SS)
        if (preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $timeString)) {
            return $timeString;
        }
        
        // Jika format 24 jam tanpa detik (HH:MM)
        if (preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $timeString)) {
            return $timeString . ':00';
        }
        
        // Jika format 12 jam dengan AM/PM
        $pattern = '/^(\d{1,2})[:\.](\d{2})\s*(AM|PM|am|pm)$/i';
        if (preg_match($pattern, $timeString, $matches)) {
            $hour = intval($matches[1]);
            $minute = $matches[2];
            $meridiem = strtoupper($matches[3]);
            
            if ($meridiem == 'AM') {
                if ($hour == 12) {
                    $hour = 0;
                }
            } else { // PM
                if ($hour != 12) {
                    $hour += 12;
                }
            }
            
            return sprintf('%02d:%s:00', $hour, $minute);
        }
        
        return null;
    }
    
    /**
     * Validate if a time string is valid
     */
    public function validateTimeString($timeString)
    {
        if (empty($timeString)) {
            return true;
        }
        
        // Cek format 12 jam dengan AM/PM
        $pattern = '/^(\d{1,2})[:\.](\d{2})\s*(AM|PM|am|pm)$/i';
        if (preg_match($pattern, $timeString, $matches)) {
            $hour = intval($matches[1]);
            $minute = intval($matches[2]);
            
            // Validasi jam (1-12)
            if ($hour < 1 || $hour > 12) {
                return false;
            }
            
            // Validasi menit (0-59)
            if ($minute < 0 || $minute > 59) {
                return false;
            }
            
            return true;
        }
        
        // Cek format 24 jam
        $pattern24 = '/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/';
        if (preg_match($pattern24, $timeString)) {
            return true;
        }
        
        return false;
    }
    
    // ========== QUERY METHODS ==========
    
    /**
     * Get attendance with employee data
     */
    public function getAbsensiWithKaryawan($conditions = [], $limit = null, $offset = 0)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('absensi');
        
        $builder->select('absensi.*, karyawan.nik, karyawan.nama_lengkap, karyawan.jabatan, karyawan.departemen');
        $builder->join('karyawan', 'karyawan.id = absensi.karyawan_id');
        $builder->where('absensi.deleted_at', null);
        
        if (!empty($conditions)) {
            foreach ($conditions as $key => $value) {
                $builder->where($key, $value);
            }
        }
        
        $builder->orderBy('absensi.tanggal', 'DESC');
        $builder->orderBy('absensi.waktu_masuk', 'DESC');
        
        if ($limit !== null) {
            $builder->limit($limit, $offset);
        }
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get attendance by employee
     */
    public function getByKaryawan($karyawan_id, $startDate = null, $endDate = null)
    {
        $this->where('karyawan_id', $karyawan_id);
        $this->where('deleted_at', null);
        
        if ($startDate) {
            $this->where('tanggal >=', $startDate);
        }
        
        if ($endDate) {
            $this->where('tanggal <=', $endDate);
        }
        
        $this->orderBy('tanggal', 'DESC');
        
        return $this->findAll();
    }
    
    /**
     * Get attendance by date range
     */
    public function getByDateRange($startDate, $endDate, $karyawan_id = null)
    {
        $this->select('absensi.*, karyawan.nik, karyawan.nama_lengkap, karyawan.jabatan');
        $this->join('karyawan', 'karyawan.id = absensi.karyawan_id');
        $this->where('absensi.tanggal >=', $startDate);
        $this->where('absensi.tanggal <=', $endDate);
        $this->where('absensi.deleted_at', null);
        
        if ($karyawan_id) {
            $this->where('absensi.karyawan_id', $karyawan_id);
        }
        
        $this->orderBy('absensi.tanggal', 'DESC');
        $this->orderBy('karyawan.nama_lengkap', 'ASC');
        
        return $this->findAll();
    }
    
    /**
     * Get today's attendance
     */
    public function getToday($karyawan_id = null)
    {
        $today = date('Y-m-d');
        
        $db = \Config\Database::connect();
        $builder = $db->table('absensi');
        $builder->select('absensi.*, karyawan.nik, karyawan.nama_lengkap');
        $builder->join('karyawan', 'karyawan.id = absensi.karyawan_id');
        $builder->where('absensi.tanggal', $today);
        $builder->where('absensi.deleted_at', null);
        
        if ($karyawan_id) {
            $builder->where('absensi.karyawan_id', $karyawan_id);
        }
        
        $builder->orderBy('absensi.waktu_masuk', 'DESC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Check if employee already has attendance for today
     */
    public function hasAttendanceToday($karyawan_id)
    {
        $today = date('Y-m-d');
        return $this->where('karyawan_id', $karyawan_id)
                    ->where('tanggal', $today)
                    ->where('deleted_at', null)
                    ->first();
    }
    
    /**
     * Get today's attendance for specific karyawan
     */
    public function getTodayAttendanceByKaryawan($karyawan_id)
    {
        $today = date('Y-m-d');
        
        return $this->where('karyawan_id', $karyawan_id)
                    ->where('tanggal', $today)
                    ->where('deleted_at', null)
                    ->first();
    }
    
    /**
     * Get attendance history for specific karyawan
     */
    public function getAbsensiHistory($karyawan_id, $startDate, $endDate, $limit = 7)
    {
        $this->where('karyawan_id', $karyawan_id);
        $this->where('tanggal >=', $startDate);
        $this->where('tanggal <=', $endDate);
        $this->where('deleted_at', null);
        $this->orderBy('tanggal', 'DESC');
        $this->limit($limit);
        
        return $this->findAll();
    }
    
    /**
     * Get attendance statistics
     */
    public function getStats($startDate = null, $endDate = null)
    {
        if (!$startDate) {
            $startDate = date('Y-m-01'); // First day of current month
        }
        
        if (!$endDate) {
            $endDate = date('Y-m-t'); // Last day of current month
        }
        
        $this->select("
            COUNT(*) as total_absensi,
            SUM(CASE WHEN status = 'Hadir' THEN 1 ELSE 0 END) as total_hadir,
            SUM(CASE WHEN status = 'Izin' THEN 1 ELSE 0 END) as total_izin,
            SUM(CASE WHEN status = 'Sakit' THEN 1 ELSE 0 END) as total_sakit,
            SUM(CASE WHEN status = 'Cuti' THEN 1 ELSE 0 END) as total_cuti,
            SUM(CASE WHEN terlambat > 0 THEN 1 ELSE 0 END) as total_terlambat,
            AVG(jam_kerja) as rata_jam_kerja,
            SUM(jam_lembur) as total_lembur,
            COUNT(DISTINCT karyawan_id) as total_karyawan
        ");
        
        $this->where('tanggal >=', $startDate);
        $this->where('tanggal <=', $endDate);
        $this->where('deleted_at', null);
        
        $result = $this->first();
        
        return $result ?: [
            'total_absensi' => 0,
            'total_hadir' => 0,
            'total_izin' => 0,
            'total_sakit' => 0,
            'total_cuti' => 0,
            'total_terlambat' => 0,
            'rata_jam_kerja' => 0,
            'total_lembur' => 0,
            'total_karyawan' => 0
        ];
    }
    
    /**
     * Check if attendance exists for employee on specific date
     */
    public function getExistingAttendance($karyawan_id, $tanggal, $exclude_id = null)
    {
        $builder = $this->db->table($this->table)
            ->where('karyawan_id', $karyawan_id)
            ->where('tanggal', $tanggal)
            ->where('deleted_at IS NULL');
        
        if ($exclude_id) {
            $builder->where('id !=', $exclude_id);
        }
        
        return $builder->get()->getRowArray();
    }
    
    /**
     * Get monthly attendance summary for a specific employee
     */
    public function getMonthlySummary($karyawan_id, $month, $year)
    {
        $startDate = date('Y-m-01', strtotime("$year-$month-01"));
        $endDate = date('Y-m-t', strtotime("$year-$month-01"));
        
        $this->select("
            COUNT(*) as total_hari_kerja,
            SUM(CASE WHEN status = 'Hadir' THEN 1 ELSE 0 END) as total_hadir,
            SUM(CASE WHEN status = 'Izin' THEN 1 ELSE 0 END) as total_izin,
            SUM(CASE WHEN status = 'Sakit' THEN 1 ELSE 0 END) as total_sakit,
            SUM(CASE WHEN status = 'Cuti' THEN 1 ELSE 0 END) as total_cuti,
            SUM(CASE WHEN terlambat > 0 THEN 1 ELSE 0 END) as total_terlambat,
            SUM(jam_kerja) as total_jam_kerja,
            SUM(jam_lembur) as total_lembur,
            AVG(jam_kerja) as rata_jam_kerja
        ");
        
        $this->where('karyawan_id', $karyawan_id);
        $this->where('tanggal >=', $startDate);
        $this->where('tanggal <=', $endDate);
        $this->where('deleted_at', null);
        
        return $this->first();
    }
    
    /**
     * Hard delete (permanent delete)
     */
    public function hardDelete($id)
    {
        $db = db_connect();
        return $db->table($this->table)
                  ->where('id', $id)
                  ->delete();
    }
    
    // ========== FORMATTING METHODS ==========
    
    /**
     * Format minutes to hours:minutes
     */
    public function formatMenitKeJam($menit)
    {
        if ($menit <= 0) {
            return '0 menit';
        }
        
        $jam = floor($menit / 60);
        $sisa_menit = $menit % 60;
        
        if ($jam > 0 && $sisa_menit > 0) {
            return $jam . ' jam ' . $sisa_menit . ' menit';
        } elseif ($jam > 0) {
            return $jam . ' jam';
        } else {
            return $sisa_menit . ' menit';
        }
    }
    
    /**
     * Format time to 12 hour format
     */
    public function formatTo12Hour($time)
    {
        if (empty($time)) {
            return null;
        }
        
        // Jika format 24 jam
        if (preg_match('/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/', $time, $matches)) {
            $hour = intval($matches[1]);
            $minute = $matches[2];
            
            // Tentukan AM/PM
            if ($hour == 0) {
                $hour = 12;
                $meridiem = 'AM';
            } elseif ($hour == 12) {
                $meridiem = 'PM';
            } elseif ($hour > 12) {
                $hour -= 12;
                $meridiem = 'PM';
            } else {
                $meridiem = 'AM';
            }
            
            return sprintf('%d:%s %s', $hour, $minute, $meridiem);
        }
        
        return $time;
    }
    
    /**
     * Format decimal hours to hours:minutes
     */
    public function formatDecimalToHoursMinutes($decimal_hours)
    {
        if ($decimal_hours <= 0) {
            return '0 jam';
        }
        
        $hours = floor($decimal_hours);
        $minutes = round(($decimal_hours - $hours) * 60);
        
        if ($hours > 0 && $minutes > 0) {
            return $hours . ' jam ' . $minutes . ' menit';
        } elseif ($hours > 0) {
            return $hours . ' jam';
        } else {
            return $minutes . ' menit';
        }
    }
    
    /**
     * Get shift information for display
     */
    public function getShiftInfo($shift)
    {
        $info = [
            'pagi' => [
                'nama' => 'Shift Pagi',
                'jam_mulai' => '07:00',
                'jam_selesai' => '16:00',
                'durasi' => '8 jam kerja (9 jam - 1 jam istirahat)',
                'warna' => 'success'
            ],
            'siang' => [
                'nama' => 'Shift Siang',
                'jam_mulai' => '08:00',
                'jam_selesai' => '17:00',
                'durasi' => '8 jam kerja (9 jam - 1 jam istirahat)',
                'warna' => 'info'
            ],
            'sore' => [
                'nama' => 'Shift Sore',
                'jam_mulai' => '09:00',
                'jam_selesai' => '18:00',
                'durasi' => '8 jam kerja (9 jam - 1 jam istirahat)',
                'warna' => 'warning'
            ],
            'malam' => [
                'nama' => 'Shift Malam',
                'jam_mulai' => '20:00',
                'jam_selesai' => '05:00',
                'durasi' => '9 jam kerja (9.5 jam - 0.5 jam istirahat)',
                'warna' => 'dark'
            ]
        ];
        
        return $info[$shift] ?? [
            'nama' => 'Shift',
            'jam_mulai' => '08:00',
            'jam_selesai' => '17:00',
            'durasi' => '8 jam kerja',
            'warna' => 'secondary'
        ];
    }
    
    // ========== BATCH OPERATIONS ==========
    
    /**
     * Import attendance data from CSV/Excel
     */
    public function importFromArray($data)
    {
        $success_count = 0;
        $error_count = 0;
        $errors = [];
        
        foreach ($data as $index => $row) {
            try {
                // Validate required fields
                if (empty($row['karyawan_id']) || empty($row['tanggal'])) {
                    $errors[] = "Baris $index: Karyawan dan Tanggal harus diisi";
                    $error_count++;
                    continue;
                }
                
                // Check if attendance already exists
                $existing = $this->getExistingAttendance($row['karyawan_id'], $row['tanggal']);
                if ($existing) {
                    $errors[] = "Baris $index: Absensi sudah ada untuk karyawan pada tanggal " . $row['tanggal'];
                    $error_count++;
                    continue;
                }
                
                // Calculate working hours if times are provided
                if (!empty($row['waktu_masuk']) && !empty($row['waktu_pulang'])) {
                    $shift = $row['shift'] ?? 'siang';
                    $row['jam_kerja'] = $this->calculateJamKerja($row['waktu_masuk'], $row['waktu_pulang'], $shift);
                    
                    // Calculate overtime
                    if (!empty($row['jam_shift_selesai'])) {
                        $row['jam_lembur'] = $this->calculateLembur($row['waktu_pulang'], $row['jam_shift_selesai']);
                    }
                    
                    // Calculate lateness
                    if (!empty($row['waktu_masuk'])) {
                        $row['terlambat'] = $this->checkTerlambat($row['waktu_masuk'], $shift);
                    }
                }
                
                // Set default values
                if (empty($row['status'])) {
                    $row['status'] = 'Hadir';
                }
                
                if (empty($row['shift'])) {
                    $row['shift'] = 'siang';
                }
                
                // Get shift times
                $row['jam_shift_mulai'] = $this->getJamMulaiByShift($row['shift']);
                $row['jam_shift_selesai'] = $this->getJamSelesaiByShift($row['shift']);
                
                // Save to database
                if ($this->save($row)) {
                    $success_count++;
                } else {
                    $errors[] = "Baris $index: Gagal menyimpan data";
                    $error_count++;
                }
            } catch (\Exception $e) {
                $errors[] = "Baris $index: " . $e->getMessage();
                $error_count++;
            }
        }
        
        return [
            'success_count' => $success_count,
            'error_count' => $error_count,
            'errors' => $errors
        ];
    }
    /**
 * Get all attendance with filter for admin/hr
 */
public function getAllAttendanceWithFilter($startDate, $endDate, $departmentId = null, $status = null, $karyawanId = null)
{
    $builder = $this->db->table('absensi')
        ->select('absensi.*, karyawan.nama_lengkap as nama_karyawan, karyawan.nik, departments.nama as department_name')
        ->join('karyawan', 'karyawan.id = absensi.karyawan_id', 'left')
        ->join('departments', 'departments.id = karyawan.department_id', 'left')
        ->where('absensi.tanggal >=', $startDate)
        ->where('absensi.tanggal <=', $endDate)
        ->where('absensi.deleted_at IS NULL')
        ->orderBy('absensi.tanggal', 'DESC')
        ->orderBy('karyawan.nama_lengkap', 'ASC');
    
    if ($departmentId) {
        $builder->where('karyawan.department_id', $departmentId);
    }
    
    if ($status) {
        $builder->where('absensi.status', $status);
    }
    
    if ($karyawanId) {
        $builder->where('absensi.karyawan_id', $karyawanId);
    }
    
    return $builder->get()->getResultArray();
}

/**
 * Get attendance by employee within date range (with department info)
 */
public function getByKaryawanWithDepartment($karyawanId, $startDate, $endDate)
{
    return $this->db->table('absensi')
        ->select('absensi.*, karyawan.nama_lengkap, karyawan.nik, departments.nama as department_name')
        ->join('karyawan', 'karyawan.id = absensi.karyawan_id')
        ->join('departments', 'departments.id = karyawan.department_id', 'left')
        ->where('absensi.karyawan_id', $karyawanId)
        ->where('absensi.tanggal >=', $startDate)
        ->where('absensi.tanggal <=', $endDate)
        ->where('absensi.deleted_at IS NULL')
        ->orderBy('absensi.tanggal', 'DESC')
        ->get()
        ->getResultArray();
}

/**
 * Get attendance statistics for admin dashboard
 */
public function getAdminStats($startDate, $endDate, $departmentId = null)
{
    $builder = $this->db->table('absensi')
        ->select("
            COUNT(*) as total_absensi,
            SUM(CASE WHEN status = 'Hadir' THEN 1 ELSE 0 END) as hadir,
            SUM(CASE WHEN status = 'Izin' THEN 1 ELSE 0 END) as izin,
            SUM(CASE WHEN status = 'Sakit' THEN 1 ELSE 0 END) as sakit,
            SUM(CASE WHEN status = 'Cuti' THEN 1 ELSE 0 END) as cuti,
            SUM(CASE WHEN status = 'Libur' THEN 1 ELSE 0 END) as libur,
            SUM(CASE WHEN status = 'Alpha' THEN 1 ELSE 0 END) as alpha,
            SUM(CASE WHEN terlambat > 0 THEN 1 ELSE 0 END) as total_terlambat,
            AVG(jam_kerja) as average_jam_kerja,
            SUM(jam_lembur) as total_jam_lembur,
            COUNT(DISTINCT karyawan_id) as total_karyawan
        ")
        ->join('karyawan', 'karyawan.id = absensi.karyawan_id')
        ->where('absensi.tanggal >=', $startDate)
        ->where('absensi.tanggal <=', $endDate)
        ->where('absensi.deleted_at IS NULL');
    
    if ($departmentId) {
        $builder->where('karyawan.department_id', $departmentId);
    }
    
    $result = $builder->get()->getRowArray();
    
    return $result ?: [
        'total_absensi' => 0,
        'hadir' => 0,
        'izin' => 0,
        'sakit' => 0,
        'cuti' => 0,
        'libur' => 0,
        'alpha' => 0,
        'total_terlambat' => 0,
        'average_jam_kerja' => 0,
        'total_jam_lembur' => 0,
        'total_karyawan' => 0
    ];
}

/**
 * Get monthly report for multiple employees
 */
public function getMonthlyReport($startDate, $endDate, $departmentId = null, $karyawanId = null)
{
    // Get all employees first
    $karyawanBuilder = $this->db->table('karyawan')
        ->select('karyawan.*, departments.nama as department_name')
        ->join('departments', 'departments.id = karyawan.department_id', 'left')
        ->where('karyawan.status', 'active');
    
    if ($departmentId) {
        $karyawanBuilder->where('karyawan.department_id', $departmentId);
    }
    
    if ($karyawanId) {
        $karyawanBuilder->where('karyawan.id', $karyawanId);
    }
    
    $karyawanBuilder->orderBy('karyawan.nama_lengkap', 'ASC');
    $employees = $karyawanBuilder->get()->getResultArray();
    
    // Get all attendance for the period
    $attendanceBuilder = $this->db->table('absensi')
        ->select('absensi.*, karyawan.nama_lengkap')
        ->join('karyawan', 'karyawan.id = absensi.karyawan_id')
        ->where('absensi.tanggal >=', $startDate)
        ->where('absensi.tanggal <=', $endDate)
        ->where('absensi.deleted_at IS NULL');
    
    if ($departmentId) {
        $attendanceBuilder->join('departments', 'departments.id = karyawan.department_id')
            ->where('karyawan.department_id', $departmentId);
    }
    
    if ($karyawanId) {
        $attendanceBuilder->where('absensi.karyawan_id', $karyawanId);
    }
    
    $allAttendance = $attendanceBuilder->get()->getResultArray();
    
    // Organize attendance by employee
    $attendanceByEmployee = [];
    foreach ($allAttendance as $attendance) {
        $karyawanId = $attendance['karyawan_id'];
        if (!isset($attendanceByEmployee[$karyawanId])) {
            $attendanceByEmployee[$karyawanId] = [];
        }
        $attendanceByEmployee[$karyawanId][] = $attendance;
    }
    
    // Calculate report for each employee
    $report = [];
    foreach ($employees as $employee) {
        $employeeId = $employee['id'];
        $employeeAttendance = $attendanceByEmployee[$employeeId] ?? [];
        
        $employeeReport = [
            'karyawan' => $employee,
            'total_hari' => 0,
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'cuti' => 0,
            'libur' => 0,
            'alpha' => 0,
            'total_terlambat' => 0,
            'total_jam_kerja' => 0,
            'total_jam_lembur' => 0,
            'detail' => $employeeAttendance
        ];
        
        foreach ($employeeAttendance as $attendance) {
            $employeeReport['total_hari']++;
            
            switch ($attendance['status']) {
                case 'Hadir':
                    $employeeReport['hadir']++;
                    if ($attendance['terlambat'] && $attendance['terlambat'] > 0) {
                        $employeeReport['total_terlambat']++;
                    }
                    $employeeReport['total_jam_kerja'] += $attendance['jam_kerja'] ?? 0;
                    $employeeReport['total_jam_lembur'] += $attendance['jam_lembur'] ?? 0;
                    break;
                case 'Izin':
                    $employeeReport['izin']++;
                    break;
                case 'Sakit':
                    $employeeReport['sakit']++;
                    break;
                case 'Cuti':
                    $employeeReport['cuti']++;
                    break;
                case 'Libur':
                    $employeeReport['libur']++;
                    break;
                case 'Alpha':
                    $employeeReport['alpha']++;
                    break;
            }
        }
        
        $report[] = $employeeReport;
    }
    
    return $report;
}

/**
 * Get attendance by employee for specific month
 */
public function getAttendanceByEmployeeMonth($karyawanId, $year, $month)
{
    $startDate = date('Y-m-01', strtotime("$year-$month-01"));
    $endDate = date('Y-m-t', strtotime("$year-$month-01"));
    
    return $this->db->table('absensi')
        ->where('karyawan_id', $karyawanId)
        ->where('tanggal >=', $startDate)
        ->where('tanggal <=', $endDate)
        ->where('deleted_at IS NULL')
        ->orderBy('tanggal', 'ASC')
        ->get()
        ->getResultArray();
}

/**
 * Calculate summary statistics for specific employee
 */
public function getEmployeeStats($karyawanId, $startDate, $endDate)
{
    $result = $this->db->table('absensi')
        ->select("
            COUNT(*) as total_hari,
            SUM(CASE WHEN status = 'Hadir' THEN 1 ELSE 0 END) as hadir,
            SUM(CASE WHEN status = 'Izin' THEN 1 ELSE 0 END) as izin,
            SUM(CASE WHEN status = 'Sakit' THEN 1 ELSE 0 END) as sakit,
            SUM(CASE WHEN status = 'Cuti' THEN 1 ELSE 0 END) as cuti,
            SUM(CASE WHEN terlambat > 0 THEN 1 ELSE 0 END) as terlambat,
            SUM(jam_kerja) as total_jam_kerja,
            SUM(jam_lembur) as total_lembur,
            AVG(jam_kerja) as rata_jam_kerja
        ")
        ->where('karyawan_id', $karyawanId)
        ->where('tanggal >=', $startDate)
        ->where('tanggal <=', $endDate)
        ->where('deleted_at IS NULL')
        ->get()
        ->getRowArray();
    
    return $result ?: [
        'total_hari' => 0,
        'hadir' => 0,
        'izin' => 0,
        'sakit' => 0,
        'cuti' => 0,
        'terlambat' => 0,
        'total_jam_kerja' => 0,
        'total_lembur' => 0,
        'rata_jam_kerja' => 0
    ];
}

/**
 * Get attendance with location metadata
 */
public function getAttendanceWithLocation($id)
{
    return $this->db->table('absensi')
        ->select('absensi.*, karyawan.nama_lengkap, karyawan.nik')
        ->join('karyawan', 'karyawan.id = absensi.karyawan_id')
        ->where('absensi.id', $id)
        ->where('absensi.deleted_at IS NULL')
        ->get()
        ->getRowArray();
}

/**
 * Search attendance by criteria
 */
public function searchAttendance($keyword, $startDate = null, $endDate = null, $departmentId = null)
{
    $builder = $this->db->table('absensi')
        ->select('absensi.*, karyawan.nama_lengkap, karyawan.nik, departments.nama as department_name')
        ->join('karyawan', 'karyawan.id = absensi.karyawan_id')
        ->join('departments', 'departments.id = karyawan.department_id', 'left')
        ->where('absensi.deleted_at IS NULL');
    
    if ($keyword) {
        $builder->groupStart()
            ->like('karyawan.nama_lengkap', $keyword)
            ->orLike('karyawan.nik', $keyword)
            ->orLike('absensi.status', $keyword)
            ->orLike('absensi.keterangan', $keyword)
            ->orLike('absensi.lokasi_masuk', $keyword)
            ->orLike('absensi.lokasi_pulang', $keyword)
            ->groupEnd();
    }
    
    if ($startDate) {
        $builder->where('absensi.tanggal >=', $startDate);
    }
    
    if ($endDate) {
        $builder->where('absensi.tanggal <=', $endDate);
    }
    
    if ($departmentId) {
        $builder->where('karyawan.department_id', $departmentId);
    }
    
    $builder->orderBy('absensi.tanggal', 'DESC')
            ->orderBy('karyawan.nama_lengkap', 'ASC');
    
    return $builder->get()->getResultArray();
}

/**
 * Get attendance summary by status
 */
public function getSummaryByStatus($startDate, $endDate, $departmentId = null)
{
    $builder = $this->db->table('absensi')
        ->select('status, COUNT(*) as count')
        ->join('karyawan', 'karyawan.id = absensi.karyawan_id')
        ->where('absensi.tanggal >=', $startDate)
        ->where('absensi.tanggal <=', $endDate)
        ->where('absensi.deleted_at IS NULL')
        ->groupBy('absensi.status');
    
    if ($departmentId) {
        $builder->where('karyawan.department_id', $departmentId);
    }
    
    $results = $builder->get()->getResultArray();
    
    // Format results
    $summary = [];
    foreach ($results as $result) {
        $summary[$result['status']] = $result['count'];
    }
    
    return $summary;
}

/**
 * Get late attendance count
 */
public function getLateCount($startDate, $endDate, $departmentId = null)
{
    $builder = $this->db->table('absensi')
        ->where('terlambat >', 0)
        ->where('status', 'Hadir')
        ->where('tanggal >=', $startDate)
        ->where('tanggal <=', $endDate)
        ->where('deleted_at IS NULL');
    
    if ($departmentId) {
        $builder->join('karyawan', 'karyawan.id = absensi.karyawan_id')
                ->where('karyawan.department_id', $departmentId);
    }
    
    return $builder->countAllResults();
}

/**
 * Get overtime summary
 */
public function getOvertimeSummary($startDate, $endDate, $departmentId = null)
{
    $builder = $this->db->table('absensi')
        ->select('karyawan.nama_lengkap, SUM(jam_lembur) as total_lembur')
        ->join('karyawan', 'karyawan.id = absensi.karyawan_id')
        ->where('absensi.jam_lembur >', 0)
        ->where('absensi.tanggal >=', $startDate)
        ->where('absensi.tanggal <=', $endDate)
        ->where('absensi.deleted_at IS NULL')
        ->groupBy('absensi.karyawan_id')
        ->orderBy('total_lembur', 'DESC');
    
    if ($departmentId) {
        $builder->where('karyawan.department_id', $departmentId);
    }
    
    return $builder->get()->getResultArray();
}

/**
 * Check if location coordinates are valid
 */
public function validateCoordinates($latitude, $longitude)
{
    if ($latitude === null || $longitude === null) {
        return false;
    }
    
    $lat = floatval($latitude);
    $lng = floatval($longitude);
    
    // Check if values are valid numbers
    if (!is_numeric($lat) || !is_numeric($lng)) {
        return false;
    }
    
    // Check latitude range
    if ($lat < -90 || $lat > 90) {
        return false;
    }
    
    // Check longitude range
    if ($lng < -180 || $lng > 180) {
        return false;
    }
    
    // Check for null island (0,0)
    if (abs($lat) < 0.0001 && abs($lng) < 0.0001) {
        return false;
    }
    
    return true;
}

/**
 * Get distance between two coordinates in meters
 */
public function calculateDistance($lat1, $lon1, $lat2, $lon2)
{
    if (!$this->validateCoordinates($lat1, $lon1) || !$this->validateCoordinates($lat2, $lon2)) {
        return null;
    }
    
    $earthRadius = 6371000; // meters
    
    $lat1 = deg2rad(floatval($lat1));
    $lon1 = deg2rad(floatval($lon1));
    $lat2 = deg2rad(floatval($lat2));
    $lon2 = deg2rad(floatval($lon2));
    
    $latDelta = $lat2 - $lat1;
    $lonDelta = $lon2 - $lon1;
    
    $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
        cos($lat1) * cos($lat2) * pow(sin($lonDelta / 2), 2)));
    
    return round($angle * $earthRadius, 2);
}

// ========== AUDIT TRAIL METHODS ==========

/**
 * Update attendance with audit trail
 */
public function updateWithAudit($id, $data, $userId)
{
    // Add audit trail information
    $data['updated_by'] = $userId;
    $data['updated_at'] = date('Y-m-d H:i:s');
    
    return $this->update($id, $data);
}

/**
 * Create attendance with audit trail
 */
public function createWithAudit($data, $userId)
{
    // Add audit trail information
    $data['created_by'] = $userId;
    $data['updated_by'] = $userId;
    $data['created_at'] = date('Y-m-d H:i:s');
    $data['updated_at'] = date('Y-m-d H:i:s');
    
    return $this->insert($data);
}

/**
 * Get attendance with audit trail info
 */
public function getWithAuditInfo($id)
{
    return $this->db->table('absensi')
        ->select('absensi.*, 
            creator.username as created_by_username,
            creator_karyawan.nama_lengkap as created_by_name,
            updater.username as updated_by_username,
            updater_karyawan.nama_lengkap as updated_by_name')
        ->join('users as creator', 'creator.id = absensi.created_by', 'left')
        ->join('karyawan as creator_karyawan', 'creator_karyawan.id = creator.karyawan_id', 'left')
        ->join('users as updater', 'updater.id = absensi.updated_by', 'left')
        ->join('karyawan as updater_karyawan', 'updater_karyawan.id = updater.karyawan_id', 'left')
        ->where('absensi.id', $id)
        ->where('absensi.deleted_at IS NULL')
        ->get()
        ->getRowArray();
}

// ========== EXPORT METHODS ==========

// App\Controllers\Admin\Absensi.php

public function print()
{
    // Cek session
    $session = \Config\Services::session();
    if (!$session->get('isLoggedIn') || strtolower($session->get('role')) !== 'admin') {
        return redirect()->to(base_url('login'));
    }
    
    // Get filter parameters from request
    $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
    $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');
    $statusFilter = $this->request->getGet('status');
    $karyawanIdFilter = $this->request->getGet('karyawan_id');
    
    // Load models
    $absensiModel = new AbsensiModel();
    $karyawanModel = new KaryawanModel();
    
    // Build query
    $db = \Config\Database::connect();
    $builder = $db->table('absensi');
    
    $builder->select('absensi.*, karyawan.nik, karyawan.nama_lengkap, karyawan.departemen')
            ->join('karyawan', 'karyawan.id = absensi.karyawan_id')
            ->where('absensi.deleted_at', null);
    
    // Apply filters
    $builder->where('absensi.tanggal >=', $startDate);
    $builder->where('absensi.tanggal <=', $endDate);
    
    if ($statusFilter) {
        $builder->where('absensi.status', $statusFilter);
    }
    
    if ($karyawanIdFilter) {
        $builder->where('absensi.karyawan_id', $karyawanIdFilter);
    }
    
    // Get attendance data
    $builder->orderBy('absensi.tanggal', 'DESC');
    $builder->orderBy('karyawan.nama_lengkap', 'ASC');
    $absensiData = $builder->get()->getResultArray();
    
    // Get statistics
    $statsBuilder = $db->table('absensi')
        ->select("
            COUNT(*) as total_absensi,
            COUNT(DISTINCT absensi.karyawan_id) as total_karyawan,
            SUM(CASE WHEN absensi.status = 'Hadir' THEN 1 ELSE 0 END) as total_hadir,
            SUM(CASE WHEN absensi.terlambat > 0 THEN 1 ELSE 0 END) as total_terlambat,
            SUM(absensi.jam_lembur) as total_lembur
        ")
        ->join('karyawan', 'karyawan.id = absensi.karyawan_id')
        ->where('absensi.deleted_at', null)
        ->where('absensi.tanggal >=', $startDate)
        ->where('absensi.tanggal <=', $endDate);
    
    if ($statusFilter) {
        $statsBuilder->where('absensi.status', $statusFilter);
    }
    
    if ($karyawanIdFilter) {
        $statsBuilder->where('absensi.karyawan_id', $karyawanIdFilter);
    }
    
    $stats = $statsBuilder->get()->getRowArray();
    
    // Get selected karyawan info if filtered
    $selectedKaryawan = null;
    if ($karyawanIdFilter) {
        $selectedKaryawan = $karyawanModel->find($karyawanIdFilter);
    }
    
    // Get summary by status
    $statusBuilder = $db->table('absensi')
        ->select('status, COUNT(*) as count')
        ->join('karyawan', 'karyawan.id = absensi.karyawan_id')
        ->where('absensi.deleted_at', null)
        ->where('absensi.tanggal >=', $startDate)
        ->where('absensi.tanggal <=', $endDate)
        ->groupBy('absensi.status');
    
    if ($karyawanIdFilter) {
        $statusBuilder->where('absensi.karyawan_id', $karyawanIdFilter);
    }
    
    $statusResults = $statusBuilder->get()->getResultArray();
    $summaryByStatus = [];
    foreach ($statusResults as $result) {
        $summaryByStatus[$result['status']] = $result['count'];
    }
    
    // Get summary by shift
    $shiftBuilder = $db->table('absensi')
        ->select('shift, COUNT(*) as count')
        ->join('karyawan', 'karyawan.id = absensi.karyawan_id')
        ->where('absensi.deleted_at', null)
        ->where('absensi.tanggal >=', $startDate)
        ->where('absensi.tanggal <=', $endDate)
        ->groupBy('absensi.shift');
    
    if ($karyawanIdFilter) {
        $shiftBuilder->where('absensi.karyawan_id', $karyawanIdFilter);
    }
    
    $shiftResults = $shiftBuilder->get()->getResultArray();
    $summaryByShift = [];
    foreach ($shiftResults as $result) {
        $summaryByShift[$result['shift']] = $result['count'];
    }
    
    // Prepare query params for export links
    $queryParams = [
        'start_date' => $startDate,
        'end_date' => $endDate,
        'status' => $statusFilter,
        'karyawan_id' => $karyawanIdFilter
    ];
    
    // Prepare data for view
    $data = [
        'title' => 'Cetak Laporan Absensi',
        'active' => 'absensi',
        'absensiData' => $absensiData,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'statusFilter' => $statusFilter,
        'karyawanIdFilter' => $karyawanIdFilter,
        'selectedKaryawan' => $selectedKaryawan,
        'queryParams' => $queryParams,
        'totalAbsensi' => $stats['total_absensi'] ?? 0,
        'totalKaryawan' => $stats['total_karyawan'] ?? 0,
        'totalHadir' => $stats['total_hadir'] ?? 0,
        'totalTerlambat' => $stats['total_terlambat'] ?? 0,
        'totalLembur' => $stats['total_lembur'] ?? 0,
        'summaryByStatus' => $summaryByStatus,
        'summaryByShift' => $summaryByShift
    ];
    
    // Return the print view
    return view('admin/absensi/print', $data);
}

/**
 * Get data for Excel export
 */
public function getExportData($startDate, $endDate, $departmentId = null, $status = null, $karyawanId = null)
{
    $builder = $this->db->table('absensi')
        ->select('
            absensi.tanggal,
            karyawan.nik,
            karyawan.nama_lengkap,
            departments.nama as department_name,
            absensi.shift,
            absensi.waktu_masuk,
            absensi.waktu_pulang,
            absensi.jam_kerja,
            absensi.jam_lembur,
            absensi.terlambat,
            absensi.status,
            absensi.lokasi_masuk,
            absensi.lokasi_pulang,
            absensi.keterangan,
            absensi.created_at,
            absensi.updated_at
        ')
        ->join('karyawan', 'karyawan.id = absensi.karyawan_id')
        ->join('departments', 'departments.id = karyawan.department_id', 'left')
        ->where('absensi.tanggal >=', $startDate)
        ->where('absensi.tanggal <=', $endDate)
        ->where('absensi.deleted_at IS NULL')
        ->orderBy('absensi.tanggal', 'DESC')
        ->orderBy('karyawan.nama_lengkap', 'ASC');
    
    if ($departmentId) {
        $builder->where('karyawan.department_id', $departmentId);
    }
    
    if ($status) {
        $builder->where('absensi.status', $status);
    }
    
    if ($karyawanId) {
        $builder->where('absensi.karyawan_id', $karyawanId);
    }
    
    return $builder->get()->getResultArray();
}

/**
 * Get summary for export
 */
public function getExportSummary($startDate, $endDate, $departmentId = null)
{
    $builder = $this->db->table('absensi')
        ->select('
            karyawan.nik,
            karyawan.nama_lengkap,
            departments.nama as department_name,
            COUNT(*) as total_hari,
            SUM(CASE WHEN absensi.status = "Hadir" THEN 1 ELSE 0 END) as hadir,
            SUM(CASE WHEN absensi.status = "Izin" THEN 1 ELSE 0 END) as izin,
            SUM(CASE WHEN absensi.status = "Sakit" THEN 1 ELSE 0 END) as sakit,
            SUM(CASE WHEN absensi.status = "Cuti" THEN 1 ELSE 0 END) as cuti,
            SUM(CASE WHEN absensi.terlambat > 0 THEN 1 ELSE 0 END) as terlambat,
            SUM(absensi.jam_kerja) as total_jam_kerja,
            SUM(absensi.jam_lembur) as total_lembur
        ')
        ->join('karyawan', 'karyawan.id = absensi.karyawan_id')
        ->join('departments', 'departments.id = karyawan.department_id', 'left')
        ->where('absensi.tanggal >=', $startDate)
        ->where('absensi.tanggal <=', $endDate)
        ->where('absensi.deleted_at IS NULL')
        ->groupBy('absensi.karyawan_id')
        ->orderBy('karyawan.nama_lengkap', 'ASC');
    
    if ($departmentId) {
        $builder->where('karyawan.department_id', $departmentId);
    }
    
    return $builder->get()->getResultArray();
}

// ========== VALIDATION METHODS ==========

/**
 * Validate attendance data for manual input
 */
public function validateManualInput($data)
{
    $errors = [];
    
    // Check required fields
    if (empty($data['karyawan_id'])) {
        $errors[] = 'Karyawan harus dipilih';
    }
    
    if (empty($data['tanggal'])) {
        $errors[] = 'Tanggal harus diisi';
    } elseif (!strtotime($data['tanggal'])) {
        $errors[] = 'Format tanggal tidak valid';
    }
    
    if (empty($data['status'])) {
        $errors[] = 'Status harus dipilih';
    }
    
    if (empty($data['shift'])) {
        $errors[] = 'Shift harus dipilih';
    }
    
    // Check if status is "Hadir" but no check-in time
    if ($data['status'] === 'Hadir' && empty($data['waktu_masuk'])) {
        $errors[] = 'Waktu masuk harus diisi untuk status Hadir';
    }
    
    // Check if attendance already exists for this employee on this date
    if (!empty($data['karyawan_id']) && !empty($data['tanggal'])) {
        $existing = $this->getExistingAttendance($data['karyawan_id'], $data['tanggal']);
        if ($existing && empty($data['id'])) {
            $errors[] = 'Absensi untuk karyawan ini pada tanggal tersebut sudah ada';
        }
    }
    
    // Validate times
    if (!empty($data['waktu_masuk']) && !$this->validateTimeString($data['waktu_masuk'])) {
        $errors[] = 'Format waktu masuk tidak valid';
    }
    
    if (!empty($data['waktu_pulang']) && !$this->validateTimeString($data['waktu_pulang'])) {
        $errors[] = 'Format waktu pulang tidak valid';
    }
    
    // Validate check-out is after check-in if both provided
    if (!empty($data['waktu_masuk']) && !empty($data['waktu_pulang'])) {
        $masuk = strtotime($data['waktu_masuk']);
        $pulang = strtotime($data['waktu_pulang']);
        
        if ($pulang < $masuk && $data['shift'] !== 'malam') {
            $errors[] = 'Waktu pulang tidak boleh lebih awal dari waktu masuk';
        }
    }
    
    return $errors;
}

}