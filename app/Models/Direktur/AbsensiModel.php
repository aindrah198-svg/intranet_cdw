<?php
namespace App\Models\Direktur;  // <-- Ganti namespace dari App\Models menjadi App\Models\Direktur

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
        'location_metadata_masuk', 'location_metadata_pulang',
        'created_by', 'updated_by'
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
    
    // TAMBAHKAN METHOD INI UNTUK DIPAKAI DI CONTROLLER
    public function getAllWithKaryawan()
    {
        return $this->select('absensi.*, karyawan.nama_lengkap, karyawan.nama_panggilan, karyawan.jabatan')
                    ->join('karyawan', 'karyawan.id = absensi.karyawan_id')
                    ->orderBy('absensi.tanggal', 'DESC')
                    ->orderBy('absensi.waktu_masuk', 'DESC')
                    ->findAll();
    }
    
    public function getToday($karyawan_id = null)
    {
        $today = date('Y-m-d');
        
        $builder = $this->select('absensi.*, karyawan.nama_lengkap, karyawan.nama_panggilan, karyawan.jabatan')
                    ->join('karyawan', 'karyawan.id = absensi.karyawan_id')
                    ->where('absensi.tanggal', $today)
                    ->where('absensi.deleted_at', null);
        
        if ($karyawan_id) {
            $builder->where('absensi.karyawan_id', $karyawan_id);
        }
        
        return $builder->orderBy('absensi.waktu_masuk', 'ASC')->findAll();
    }
    
    // ========== SHIFT METHODS ==========
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
    
    public function getJamSelesaiByShift($shift)
    {
        $shift_times = [
            'pagi' => '16:00:00',
            'siang' => '17:00:00',
            'sore' => '18:00:00',
            'malam' => '05:00:00'
        ];
        
        return $shift_times[$shift] ?? '17:00:00';
    }
    
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
    
    public function getDurasiShift($shift)
    {
        $shift_durations = [
            'pagi' => 8,
            'siang' => 8,
            'sore' => 8,
            'malam' => 9
        ];
        
        return $shift_durations[$shift] ?? 8;
    }
    
    public function getBreakDuration($shift)
    {
        $break_durations = [
            'pagi' => 1.0,
            'siang' => 1.0,
            'sore' => 1.0,
            'malam' => 0.5
        ];
        
        return $break_durations[$shift] ?? 1.0;
    }
    
    // ========== CALCULATION METHODS ==========
    public function calculateJamKerja($waktu_masuk, $waktu_pulang, $shift = 'siang')
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
        $jam_kerja_total = $selisih_detik / 3600;
        $jam_kerja_setelah_istirahat = $this->applyBreakDeduction($jam_kerja_total, $shift);
        $jam_kerja_setelah_istirahat = max(0, $jam_kerja_setelah_istirahat);
        
        return round($jam_kerja_setelah_istirahat, 2);
    }
    
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
    
    private function applyBreakDeduction($jam_kerja_total, $shift)
    {
        $min_hours_for_break = 4;
        
        if ($jam_kerja_total <= $min_hours_for_break) {
            return $jam_kerja_total;
        }
        
        $break_duration = $this->getBreakDuration($shift);
        $jam_setelah_istirahat = $jam_kerja_total - $break_duration;
        
        return max(0, $jam_setelah_istirahat);
    }
    
    public function checkTerlambat($waktu_masuk, $shift = 'siang')
    {
        if (!$waktu_masuk) {
            return 0;
        }
        
        $waktu_masuk = $this->convertTimeStringTo24Hour($waktu_masuk);
        if (!$waktu_masuk) {
            return 0;
        }
        
        $jam_mulai_shift = $this->getJamMulaiByShift($shift);
        $toleransi = 30;
        
        $jam_masuk = strtotime($waktu_masuk);
        $jam_mulai_normal = strtotime($jam_mulai_shift);
        $batas_toleransi = strtotime($jam_mulai_shift) + ($toleransi * 60);
        
        if ($jam_masuk <= $batas_toleransi) {
            return 0;
        }
        
        if ($jam_masuk > $jam_mulai_normal) {
            $selisih = $jam_masuk - $jam_mulai_normal;
            $terlambat_menit = (int) ceil($selisih / 60);
            return min($terlambat_menit, 480);
        }
        
        return 0;
    }
    
    public function calculateLembur($waktu_pulang, $jam_shift_selesai = '17:00:00')
    {
        if (empty($waktu_pulang)) {
            return 0;
        }
        
        $waktu_pulang = $this->convertTimeStringTo24Hour($waktu_pulang);
        
        if (!$waktu_pulang) {
            return 0;
        }
        
        $jam_pulang = strtotime($waktu_pulang);
        $jam_selesai_shift = strtotime($jam_shift_selesai);
        
        if ($jam_shift_selesai == '05:00:00') {
            if ($jam_pulang < strtotime('12:00:00')) {
                return 0;
            }
            $jam_selesai_shift += 86400;
        }
        
        if ($jam_pulang <= $jam_selesai_shift) {
            return 0;
        }
        
        $selisih_detik = $jam_pulang - $jam_selesai_shift;
        $jam_lembur = $selisih_detik / 3600;
        
        return round(max(0, $jam_lembur), 2);
    }
    
    public function calculateJamKerjaEfektif($jam_kerja, $terlambat_menit)
    {
        $terlambat_jam = $terlambat_menit / 60;
        $jam_efektif = $jam_kerja - $terlambat_jam;
        
        return round(max(0, $jam_efektif), 2);
    }
    
    // ========== HELPER METHODS ==========
    private function convertTimeStringTo24Hour($timeString)
    {
        if (empty($timeString)) {
            return null;
        }
        
        if (preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $timeString)) {
            return $timeString;
        }
        
        if (preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $timeString)) {
            return $timeString . ':00';
        }
        
        $pattern = '/^(\d{1,2})[:\.](\d{2})\s*(AM|PM|am|pm)$/i';
        if (preg_match($pattern, $timeString, $matches)) {
            $hour = intval($matches[1]);
            $minute = $matches[2];
            $meridiem = strtoupper($matches[3]);
            
            if ($meridiem == 'AM') {
                if ($hour == 12) {
                    $hour = 0;
                }
            } else {
                if ($hour != 12) {
                    $hour += 12;
                }
            }
            
            return sprintf('%02d:%s:00', $hour, $minute);
        }
        
        return null;
    }
    
    public function validateTimeString($timeString)
    {
        if (empty($timeString)) {
            return true;
        }
        
        $pattern = '/^(\d{1,2})[:\.](\d{2})\s*(AM|PM|am|pm)$/i';
        if (preg_match($pattern, $timeString, $matches)) {
            $hour = intval($matches[1]);
            $minute = intval($matches[2]);
            
            if ($hour < 1 || $hour > 12) {
                return false;
            }
            if ($minute < 0 || $minute > 59) {
                return false;
            }
            return true;
        }
        
        $pattern24 = '/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/';
        if (preg_match($pattern24, $timeString)) {
            return true;
        }
        
        return false;
    }
    
    // ========== QUERY METHODS ==========
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
    
    public function hasAttendanceToday($karyawan_id)
    {
        $today = date('Y-m-d');
        return $this->where('karyawan_id', $karyawan_id)
                    ->where('tanggal', $today)
                    ->where('deleted_at', null)
                    ->first();
    }
    
    public function getTodayAttendanceByKaryawan($karyawan_id)
    {
        $today = date('Y-m-d');
        
        return $this->where('karyawan_id', $karyawan_id)
                    ->where('tanggal', $today)
                    ->where('deleted_at', null)
                    ->first();
    }
    
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
    
    public function getStats($startDate = null, $endDate = null)
    {
        if (!$startDate) {
            $startDate = date('Y-m-01');
        }
        if (!$endDate) {
            $endDate = date('Y-m-t');
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
    
    public function hardDelete($id)
    {
        $db = db_connect();
        return $db->table($this->table)
                  ->where('id', $id)
                  ->delete();
    }
    
    // ========== FORMATTING METHODS ==========
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
    
    public function formatTo12Hour($time)
    {
        if (empty($time)) {
            return null;
        }
        
        if (preg_match('/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/', $time, $matches)) {
            $hour = intval($matches[1]);
            $minute = $matches[2];
            
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
    public function importFromArray($data)
    {
        $success_count = 0;
        $error_count = 0;
        $errors = [];
        
        foreach ($data as $index => $row) {
            try {
                if (empty($row['karyawan_id']) || empty($row['tanggal'])) {
                    $errors[] = "Baris $index: Karyawan dan Tanggal harus diisi";
                    $error_count++;
                    continue;
                }
                
                $existing = $this->getExistingAttendance($row['karyawan_id'], $row['tanggal']);
                if ($existing) {
                    $errors[] = "Baris $index: Absensi sudah ada untuk karyawan pada tanggal " . $row['tanggal'];
                    $error_count++;
                    continue;
                }
                
                if (!empty($row['waktu_masuk']) && !empty($row['waktu_pulang'])) {
                    $shift = $row['shift'] ?? 'siang';
                    $row['jam_kerja'] = $this->calculateJamKerja($row['waktu_masuk'], $row['waktu_pulang'], $shift);
                    
                    if (!empty($row['jam_shift_selesai'])) {
                        $row['jam_lembur'] = $this->calculateLembur($row['waktu_pulang'], $row['jam_shift_selesai']);
                    }
                    
                    if (!empty($row['waktu_masuk'])) {
                        $row['terlambat'] = $this->checkTerlambat($row['waktu_masuk'], $shift);
                    }
                }
                
                if (empty($row['status'])) {
                    $row['status'] = 'Hadir';
                }
                
                if (empty($row['shift'])) {
                    $row['shift'] = 'siang';
                }
                
                $row['jam_shift_mulai'] = $this->getJamMulaiByShift($row['shift']);
                $row['jam_shift_selesai'] = $this->getJamSelesaiByShift($row['shift']);
                
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
    
    // ========== AUDIT TRAIL METHODS ==========
    public function updateWithAudit($id, $data, $userId)
    {
        $data['updated_by'] = $userId;
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return $this->update($id, $data);
    }
    
    public function createWithAudit($data, $userId)
    {
        $data['created_by'] = $userId;
        $data['updated_by'] = $userId;
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return $this->insert($data);
    }
    
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
    
    // ========== VALIDATION METHODS ==========
    public function validateManualInput($data)
    {
        $errors = [];
        
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
        
        if ($data['status'] === 'Hadir' && empty($data['waktu_masuk'])) {
            $errors[] = 'Waktu masuk harus diisi untuk status Hadir';
        }
        
        if (!empty($data['karyawan_id']) && !empty($data['tanggal'])) {
            $existing = $this->getExistingAttendance($data['karyawan_id'], $data['tanggal']);
            if ($existing && empty($data['id'])) {
                $errors[] = 'Absensi untuk karyawan ini pada tanggal tersebut sudah ada';
            }
        }
        
        if (!empty($data['waktu_masuk']) && !$this->validateTimeString($data['waktu_masuk'])) {
            $errors[] = 'Format waktu masuk tidak valid';
        }
        
        if (!empty($data['waktu_pulang']) && !$this->validateTimeString($data['waktu_pulang'])) {
            $errors[] = 'Format waktu pulang tidak valid';
        }
        
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