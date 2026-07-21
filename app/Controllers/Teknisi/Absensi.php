<?php

namespace App\Controllers\Teknisi;

use App\Controllers\BaseController;
use App\Models\AbsensiModel;
use App\Models\KaryawanModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

class Absensi extends BaseController
{
    use ResponseTrait;
    
    protected $absensiModel;
    protected $karyawanModel;
    protected $userModel;
    protected $karyawanId;
    
    /**
     * Initialize controller
     */
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, 
                                 \CodeIgniter\HTTP\ResponseInterface $response, 
                                 \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        $this->absensiModel = new AbsensiModel();
        $this->karyawanModel = new KaryawanModel();
        $this->userModel = new UserModel();
        
        $session = \Config\Services::session();
        $userId = $session->get('user_id');
        
        if ($userId) {
            $user = $this->userModel->find($userId);
            $this->karyawanId = $user['karyawan_id'] ?? null;
        }
    }

    /**
     * Display absensi page for teknisi
     */
    public function index()
    {
        $session = \Config\Services::session();
        
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        
        $userRole = strtolower($session->get('role') ?? '');
        if ($userRole !== 'teknisi') {
            return redirect()->to(base_url($userRole))->with('info', 'Anda dialihkan ke dashboard sesuai role.');
        }
        
        $today = date('Y-m-d');
        $absensiToday = null;
        
        if ($this->karyawanId) {
            $absensiToday = $this->absensiModel->getTodayAttendanceByKaryawan($this->karyawanId);
        }
        
        $startDate = date('Y-m-d', strtotime('-7 days'));
        $endDate = date('Y-m-d');
        $absensiHistory = [];
        
        if ($this->karyawanId) {
            $absensiHistory = $this->absensiModel->getAbsensiHistory($this->karyawanId, $startDate, $endDate, 7);
        }
        
        $userId = $session->get('user_id');
        $userData = $this->userModel->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
            ->select('users.*, karyawan.*')
            ->where('users.id', $userId)
            ->first();
        
        $data = [
            'title' => 'Absensi Teknisi',
            'subtitle' => 'Sistem Absensi Masuk & Pulang',
            'active' => 'absensi',
            'user' => $userData,
            'absensiToday' => $absensiToday,
            'absensiHistory' => $absensiHistory,
            'karyawan_id' => $this->karyawanId,
            'stats' => $this->getMonthlyStats()
        ];
        
        return view('teknisi/templates/header', $data) .
               view('teknisi/templates/sidebar', $data) .
               view('teknisi/absensi/index', $data) .
               view('teknisi/templates/footer', $data);
    }

    /**
     * Process check-in for teknisi with accurate location
     */
    public function checkin()
    {
        log_message('debug', 'TEKNISI ABSENSI CHECKIN: Processing check-in with location');
        
        if (!$this->request->isAJAX()) {
            return $this->fail('Only AJAX requests are allowed', 400);
        }
        
        if (!$this->karyawanId) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Data karyawan tidak ditemukan'
            ], 400);
        }
        
        date_default_timezone_set('Asia/Jakarta');
        $today = date('Y-m-d');
        
        $existing = $this->absensiModel->hasAttendanceToday($this->karyawanId);
        
        if ($existing && !empty($existing['waktu_masuk'])) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Anda sudah melakukan absensi masuk hari ini'
            ], 400);
        }
        
        $requestData = $this->request->getJSON();
        $shift = $requestData->shift ?? 'siang';
        
        $valid_shifts = ['pagi', 'siang', 'sore', 'malam'];
        if (!in_array($shift, $valid_shifts)) {
            $shift = 'siang';
        }
        
        $waktu_masuk = date('H:i:s');
        
        // Process location data
        $latitude_masuk = $requestData->latitude_masuk ?? null;
        $longitude_masuk = $requestData->longitude_masuk ?? null;
        $accuracy = $requestData->accuracy ?? null;
        $altitude = $requestData->altitude ?? null;
        $heading = $requestData->heading ?? null;
        $speed = $requestData->speed ?? null;
        
        // Validate and normalize coordinates
        $validCoords = $this->validateAndNormalizeCoordinates($latitude_masuk, $longitude_masuk);
        
        if ($validCoords) {
            $latitude_masuk = $validCoords['latitude'];
            $longitude_masuk = $validCoords['longitude'];
            $lokasi_masuk = $this->getLocationNameFromCoordinates($latitude_masuk, $longitude_masuk);
            
            // Validate location is within Indonesia
            if (!$this->isLocationInIndonesia($latitude_masuk, $longitude_masuk)) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Lokasi berada di luar Indonesia'
                ], 400);
            }
            
            // Check if location is valid (not 0,0)
            if ($this->isNullIsland($latitude_masuk, $longitude_masuk)) {
                $lokasi_masuk = 'Lokasi tidak valid (null island)';
                $latitude_masuk = null;
                $longitude_masuk = null;
            }
            
            // Store accuracy information
            $location_metadata = [
                'accuracy' => $accuracy,
                'altitude' => $altitude,
                'heading' => $heading,
                'speed' => $speed,
                'timestamp' => time(),
                'source' => 'gps'
            ];
            
        } else {
            $latitude_masuk = null;
            $longitude_masuk = null;
            $lokasi_masuk = 'Lokasi tidak terdeteksi';
            $location_metadata = [
                'source' => 'unknown',
                'timestamp' => time()
            ];
        }
        
        // Calculate lateness
        $terlambat_info = $this->hitungTerlambatBerdasarkanShift($waktu_masuk, $shift);
        
        // Get shift hours
        $jam_shift_mulai = $this->absensiModel->getJamMulaiByShift($shift);
        $jam_shift_selesai = $this->absensiModel->getJamSelesaiByShift($shift);
        
        // Prepare data with accurate location
        $data = [
            'karyawan_id' => $this->karyawanId,
            'shift' => $shift,
            'jam_shift_mulai' => $jam_shift_mulai,
            'jam_shift_selesai' => $jam_shift_selesai,
            'tanggal' => $today,
            'waktu_masuk' => $waktu_masuk,
            'lokasi_masuk' => $lokasi_masuk,
            'latitude_masuk' => $latitude_masuk,
            'longitude_masuk' => $longitude_masuk,
            'location_metadata_masuk' => json_encode($location_metadata),
            'status' => 'Hadir',
            'device_masuk' => $this->request->getUserAgent()->getAgentString(),
            'ip_address_masuk' => $this->request->getIPAddress(),
            'terlambat' => $terlambat_info['terlambat'],
            'keterangan' => $terlambat_info['keterangan']
        ];
        
        if ($existing && !empty($existing['id'])) {
            $data['id'] = $existing['id'];
        }
        
        log_message('debug', 'TEKNISI ABSENSI CHECKIN: Saving attendance with location: ' . print_r($data, true));
        
        try {
            if ($this->absensiModel->save($data)) {
                $attendanceId = !empty($existing['id']) ? $existing['id'] : $this->absensiModel->getInsertID();
                
                // Calculate distance from office (optional)
                $distanceFromOffice = null;
                if ($latitude_masuk && $longitude_masuk) {
                    $distanceFromOffice = $this->calculateDistanceFromOffice($latitude_masuk, $longitude_masuk);
                }
                
                $waktu_display_wib = date('H:i', strtotime($waktu_masuk)) . ' WIB';
                $nama_shift = $this->formatNamaShift($shift);
                
                return $this->respond([
                    'status' => 'success',
                    'message' => 'Absensi masuk berhasil',
                    'data' => array_merge($data, ['id' => $attendanceId]),
                    'waktu_display' => $waktu_display_wib,
                    'terlambat_display' => $this->formatMenitKeJam($terlambat_info['terlambat']),
                    'keterangan_terlambat' => $terlambat_info['keterangan'],
                    'shift_info' => [
                        'shift' => $shift,
                        'nama_shift' => $nama_shift,
                        'jam_mulai' => substr($jam_shift_mulai, 0, 5),
                        'jam_selesai' => substr($jam_shift_selesai, 0, 5)
                    ],
                    'location_info' => [
                        'lokasi' => $lokasi_masuk,
                        'latitude' => $latitude_masuk,
                        'longitude' => $longitude_masuk,
                        'accuracy' => $accuracy,
                        'distance_from_office' => $distanceFromOffice,
                        'accuracy_status' => $this->getAccuracyStatus($accuracy)
                    ]
                ]);
            } else {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Gagal melakukan absensi masuk'
                ], 500);
            }
        } catch (\Exception $e) {
            log_message('error', 'TEKNISI ABSENSI CHECKIN Exception: ' . $e->getMessage());
            return $this->respond([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process check-out for teknisi with accurate location
     */
    public function checkout()
    {
        log_message('debug', 'TEKNISI ABSENSI CHECKOUT: Processing check-out with location');
        
        if (!$this->request->isAJAX()) {
            return $this->fail('Only AJAX requests are allowed', 400);
        }
        
        if (!$this->karyawanId) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Data karyawan tidak ditemukan'
            ], 400);
        }
        
        date_default_timezone_set('Asia/Jakarta');
        $today = date('Y-m-d');
        
        $attendance = $this->absensiModel->getTodayAttendanceByKaryawan($this->karyawanId);
        
        if (!$attendance) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Data absensi tidak ditemukan. Silakan check in terlebih dahulu.'
            ], 404);
        }
        
        if (!empty($attendance['waktu_pulang'])) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Anda sudah melakukan absensi pulang hari ini'
            ], 400);
        }
        
        $waktu_pulang = date('H:i:s');
        $requestData = $this->request->getJSON();
        
        // Process location for checkout
        $latitude_pulang = $requestData->latitude_pulang ?? null;
        $longitude_pulang = $requestData->longitude_pulang ?? null;
        $accuracy = $requestData->accuracy ?? null;
        
        // Validate and normalize coordinates
        $validCoords = $this->validateAndNormalizeCoordinates($latitude_pulang, $longitude_pulang);
        
        if ($validCoords) {
            $latitude_pulang = $validCoords['latitude'];
            $longitude_pulang = $validCoords['longitude'];
            $lokasi_pulang = $this->getLocationNameFromCoordinates($latitude_pulang, $longitude_pulang);
            
            // Validate location is within Indonesia
            if (!$this->isLocationInIndonesia($latitude_pulang, $longitude_pulang)) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Lokasi berada di luar Indonesia'
                ], 400);
            }
            
            // Check if location is valid (not 0,0)
            if ($this->isNullIsland($latitude_pulang, $longitude_pulang)) {
                $lokasi_pulang = 'Lokasi tidak valid (null island)';
                $latitude_pulang = null;
                $longitude_pulang = null;
            }
            
            // Calculate distance from check-in location
            $distanceFromCheckin = null;
            if ($attendance['latitude_masuk'] && $attendance['longitude_masuk'] && 
                $latitude_pulang && $longitude_pulang) {
                $distanceFromCheckin = $this->calculateDistance(
                    $attendance['latitude_masuk'], $attendance['longitude_masuk'],
                    $latitude_pulang, $longitude_pulang
                );
            }
            
            $location_metadata = [
                'accuracy' => $accuracy,
                'distance_from_checkin' => $distanceFromCheckin,
                'timestamp' => time(),
                'source' => 'gps'
            ];
            
        } else {
            $latitude_pulang = null;
            $longitude_pulang = null;
            $lokasi_pulang = 'Lokasi tidak terdeteksi';
            $location_metadata = [
                'source' => 'unknown',
                'timestamp' => time()
            ];
        }
        
        // Prepare data
        $data = [
            'id' => $attendance['id'],
            'waktu_pulang' => $waktu_pulang,
            'lokasi_pulang' => $lokasi_pulang,
            'latitude_pulang' => $latitude_pulang,
            'longitude_pulang' => $longitude_pulang,
            'location_metadata_pulang' => json_encode($location_metadata),
            'device_pulang' => $this->request->getUserAgent()->getAgentString(),
            'ip_address_pulang' => $this->request->getIPAddress(),
            'keterangan' => $requestData->keterangan ?? ($attendance['keterangan'] ?? '')
        ];
        
        // Calculate working hours and overtime
        if (!empty($attendance['waktu_masuk'])) {
            $shift = $attendance['shift'] ?? 'siang';
            
            $jam_kerja = $this->absensiModel->calculateJamKerja($attendance['waktu_masuk'], $waktu_pulang);
            $data['jam_kerja'] = $jam_kerja;
            
            $jam_lembur = $this->absensiModel->calculateLembur($waktu_pulang, $shift);
            $data['jam_lembur'] = $jam_lembur;
        }
        
        log_message('debug', 'TEKNISI ABSENSI CHECKOUT: Updating attendance with location: ' . print_r($data, true));
        
        try {
         if ($this->absensiModel->save($data)) {
    // TAMBAHKAN validasi ini:
    if (isset($data['jam_lembur']) && $data['jam_lembur'] > 100) {
        log_message('error', 'Invalid overtime detected: ' . $data['jam_lembur'] . ' hours, resetting to 0');
        
        // Update data dengan jam lembur yang benar
        $this->absensiModel->update($attendance['id'], ['jam_lembur' => 0]);
        
        // Update response data juga
        $data['jam_lembur'] = 0;
    }
    
    $waktu_display_wib = date('H:i', strtotime($waktu_pulang)) . ' WIB';
    $jam_kerja_display = $this->formatJamKerja($data['jam_kerja'] ?? 0);
    
    return $this->respond([
        'status' => 'success',
        'message' => 'Absensi pulang berhasil',
        'data' => $data,
        'waktu_display' => $waktu_display_wib,
        'jam_kerja_display' => $jam_kerja_display,
        'jam_lembur_display' => !empty($data['jam_lembur']) && $data['jam_lembur'] < 100 ? 
            number_format($data['jam_lembur'], 1) . ' jam' : '0 jam',  // PERBAIKAN DISINI
        'location_info' => [
            'lokasi' => $lokasi_pulang,
            'latitude' => $latitude_pulang,
            'longitude' => $longitude_pulang,
            'accuracy' => $accuracy,
            'distance_from_checkin' => $distanceFromCheckin,
            'accuracy_status' => $this->getAccuracyStatus($accuracy)
                    ]
                ]);
            } else {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Gagal melakukan absensi pulang'
                ], 500);
            }
        } catch (\Exception $e) {
            log_message('error', 'TEKNISI ABSENSI CHECKOUT Exception: ' . $e->getMessage());
            return $this->respond([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate and normalize coordinates
     */
    private function validateAndNormalizeCoordinates($latitude, $longitude)
    {
        // Check if coordinates are provided
        if (is_null($latitude) || is_null($longitude)) {
            return null;
        }
        
        // Convert to float
        $lat = floatval($latitude);
        $lng = floatval($longitude);
        
        // Check if values are valid numbers
        if (!is_numeric($lat) || !is_numeric($lng)) {
            return null;
        }
        
        // Validate latitude range (-90 to 90)
        if ($lat < -90 || $lat > 90) {
            return null;
        }
        
        // Validate longitude range (-180 to 180)
        if ($lng < -180 || $lng > 180) {
            return null;
        }
        
        // Check for null island (0,0) coordinates
        if (abs($lat) < 0.0001 && abs($lng) < 0.0001) {
            return null;
        }
        
        // Normalize to 8 decimal places
        return [
            'latitude' => number_format($lat, 8, '.', ''),
            'longitude' => number_format($lng, 8, '.', '')
        ];
    }

    /**
     * Check if location is within Indonesia boundaries
     */
    private function isLocationInIndonesia($latitude, $longitude)
    {
        // Indonesia approximate boundaries
        $minLat = -11.0; // Southernmost point
        $maxLat = 6.0;   // Northernmost point
        $minLng = 95.0;  // Westernmost point
        $maxLng = 141.0; // Easternmost point
        
        $lat = floatval($latitude);
        $lng = floatval($longitude);
        
        return ($lat >= $minLat && $lat <= $maxLat && 
                $lng >= $minLng && $lng <= $maxLng);
    }

    /**
     * Check if coordinates are null island (0,0)
     */
    private function isNullIsland($latitude, $longitude)
    {
        $lat = floatval($latitude);
        $lng = floatval($longitude);
        
        return (abs($lat) < 0.0001 && abs($lng) < 0.0001);
    }

    /**
     * Get location name from coordinates using reverse geocoding
     */
    private function getLocationNameFromCoordinates($latitude, $longitude)
    {
        if (!$latitude || !$longitude) {
            return 'Lokasi tidak diketahui';
        }
        
        try {
            // Use Nominatim (OpenStreetMap) for reverse geocoding
            $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$latitude}&lon={$longitude}&zoom=18&addressdetails=1";
            
            $client = \Config\Services::curlrequest();
            $response = $client->get($url, [
                'headers' => [
                    'User-Agent' => 'CDW Engineering HR System',
                    'Accept-Language' => 'id-ID,id;q=0.9,en;q=0.8'
                ],
                'timeout' => 5
            ]);
            
            $data = json_decode($response->getBody(), true);
            
            if (isset($data['display_name'])) {
                return $data['display_name'];
            }
            
            // Fallback: try with simpler format
            if (isset($data['address'])) {
                $address = $data['address'];
                
                if (isset($address['road']) && isset($address['suburb'])) {
                    return $address['road'] . ', ' . $address['suburb'];
                } elseif (isset($address['village'])) {
                    return $address['village'];
                } elseif (isset($address['city'])) {
                    return $address['city'];
                }
            }
            
        } catch (\Exception $e) {
            log_message('debug', 'Reverse geocoding failed: ' . $e->getMessage());
        }
        
        // Fallback to coordinates
        return "Koordinat: {$latitude}, {$longitude}";
    }

    /**
     * Calculate distance between two coordinates in meters
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        if (!$lat1 || !$lon1 || !$lat2 || !$lon2) {
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

    /**
     * Calculate distance from office coordinates
     */
    private function calculateDistanceFromOffice($latitude, $longitude)
    {
        // Office coordinates (example: Jakarta)
        $officeLat = -6.2088;
        $officeLng = 106.8456;
        
        return $this->calculateDistance($latitude, $longitude, $officeLat, $officeLng);
    }

    /**
     * Get accuracy status based on meter value
     */
    private function getAccuracyStatus($accuracy)
    {
        if (is_null($accuracy)) {
            return 'unknown';
        }
        
        $accuracy = floatval($accuracy);
        
        if ($accuracy <= 20) {
            return 'very_high';
        } elseif ($accuracy <= 50) {
            return 'high';
        } elseif ($accuracy <= 100) {
            return 'good';
        } elseif ($accuracy <= 500) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    /**
     * Get attendance history for teknisi
     */
    public function history()
    {
        log_message('debug', 'TEKNISI ABSENSI HISTORY: Getting attendance history');
        
        if (!$this->karyawanId) {
            return $this->respond(['history' => []]);
        }
        
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');
        
        $history = $this->absensiModel->getAbsensiHistory($this->karyawanId, $startDate, $endDate, 30);
        
        $formattedHistory = [];
        foreach ($history as $absensi) {
            $formattedHistory[] = [
                'tanggal' => $absensi['tanggal'],
                'waktu_masuk' => $absensi['waktu_masuk'],
                'waktu_pulang' => $absensi['waktu_pulang'],
                'jam_kerja' => $absensi['jam_kerja'] ?? 0,
                'jam_lembur' => $absensi['jam_lembur'] ?? 0,
                'terlambat' => $absensi['terlambat'] ?? 0,
                'status' => $absensi['status'] ?? 'Hadir',
                'keterangan' => $absensi['keterangan'] ?? '',
                'shift' => $absensi['shift'] ?? 'siang',
                'lokasi_masuk' => $absensi['lokasi_masuk'] ?? '',
                'lokasi_pulang' => $absensi['lokasi_pulang'] ?? ''
            ];
        }
        
        log_message('debug', 'TEKNISI ABSENSI HISTORY: Returning ' . count($formattedHistory) . ' records');
        
        return $this->respond(['history' => $formattedHistory]);
    }

    /**
     * Helper: Calculate lateness based on shift
     */
    private function hitungTerlambatBerdasarkanShift($waktu_masuk, $shift)
    {
        $jam_mulai_shift = $this->absensiModel->getJamMulaiByShift($shift);
        $jam_selesai_shift = $this->absensiModel->getJamSelesaiByShift($shift);
        $toleransi = 30;
        
        $jam_masuk = strtotime($waktu_masuk);
        $jam_mulai = strtotime($jam_mulai_shift);
        $jam_selesai = strtotime($jam_selesai_shift);
        $batas_toleransi = $jam_mulai + ($toleransi * 60);
        
        // Handle non-night shifts
        if ($shift !== 'malam') {
            if ($jam_masuk > $jam_selesai) {
                $selisih = $jam_masuk - $jam_mulai;
                $terlambat_menit = (int) ceil($selisih / 60);
                
                return [
                    'terlambat' => $terlambat_menit,
                    'keterangan' => 'Absensi di luar jam shift (terlambat ' . $this->formatMenitKeJam($terlambat_menit) . ')'
                ];
            }
        }
        
        // Handle night shift
        if ($shift === 'malam') {
            if ($jam_masuk < strtotime('12:00:00')) {
                $jam_mulai_malam = strtotime('20:00:00') - 86400;
                $jam_selesai_malam = strtotime('05:00:00');
                $batas_toleransi_malam = $jam_mulai_malam + ($toleransi * 60);
                
                if ($jam_masuk < $jam_mulai_malam) {
                    return [
                        'terlambat' => 0,
                        'keterangan' => 'Masuk lebih awal (shift malam)'
                    ];
                }
                
                if ($jam_masuk <= $batas_toleransi_malam) {
                    return [
                        'terlambat' => 0,
                        'keterangan' => 'Tepat waktu (dalam toleransi ' . $toleransi . ' menit)'
                    ];
                }
                
                $selisih = $jam_masuk - $jam_mulai_malam;
                $terlambat_menit = (int) ceil($selisih / 60);
                
                return [
                    'terlambat' => $terlambat_menit,
                    'keterangan' => 'Terlambat ' . $this->formatMenitKeJam($terlambat_menit)
                ];
            }
        }
        
        // Normal calculation
        if ($jam_masuk < $jam_mulai) {
            return [
                'terlambat' => 0,
                'keterangan' => 'Masuk lebih awal'
            ];
        }
        
        if ($jam_masuk <= $batas_toleransi) {
            return [
                'terlambat' => 0,
                'keterangan' => 'Tepat waktu (dalam toleransi ' . $toleransi . ' menit)'
            ];
        }
        
        $selisih = $jam_masuk - $jam_mulai;
        $terlambat_menit = (int) ceil($selisih / 60);
        $terlambat_final = min($terlambat_menit, 480);
        
        return [
            'terlambat' => $terlambat_final,
            'keterangan' => $terlambat_final >= 480 ? 
                'Terlambat extreme (maksimal)' : 
                'Terlambat ' . $this->formatMenitKeJam($terlambat_final)
        ];
    }

    /**
     * Helper: Format shift name
     */
    private function formatNamaShift($shift)
    {
        $shift_names = [
            'pagi' => 'Shift Pagi',
            'siang' => 'Shift Siang',
            'sore' => 'Shift Sore',
            'malam' => 'Shift Malam'
        ];
        
        return $shift_names[$shift] ?? 'Shift Siang';
    }

    /**
     * Helper: Format minutes to hours
     */
    private function formatMenitKeJam($menit)
    {
        if ($menit <= 0) return '0 menit';
        
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
     * Helper: Format working hours
     */
    private function formatJamKerja($jam_decimal)
    {
        if (empty($jam_decimal) || $jam_decimal <= 0) return '-';
        
        $jam = floor($jam_decimal);
        $menit = round(($jam_decimal - $jam) * 60);
        
        if ($jam > 0 && $menit > 0) {
            return "{$jam} jam {$menit} menit";
        } elseif ($jam > 0) {
            return "{$jam} jam";
        } else {
            return "{$menit} menit";
        }
    }

    /**
     * Get monthly statistics
     */
    private function getMonthlyStats()
    {
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-t');
        
        $stats = [
            'hadir_bulan_ini' => 0,
            'terlambat_bulan_ini' => 0,
            'jam_lembur_bulan_ini' => 0,
            'cuti_terpakai' => 0
        ];
        
        if ($this->karyawanId) {
            $absensiBulanIni = $this->absensiModel->getByKaryawan($this->karyawanId, $startDate, $endDate);
            
            if (is_array($absensiBulanIni)) {
                foreach ($absensiBulanIni as $absensi) {
                    if (($absensi['status'] ?? '') === 'Hadir') {
                        $stats['hadir_bulan_ini']++;
                    }
                    
                    if (($absensi['terlambat'] ?? 0) > 0) {
                        $stats['terlambat_bulan_ini']++;
                    }
                    
                    $stats['jam_lembur_bulan_ini'] += $absensi['jam_lembur'] ?? 0;
                    
                    if (($absensi['status'] ?? '') === 'Cuti') {
                        $stats['cuti_terpakai']++;
                    }
                }
            }
        }
        
        return $stats;
    }

    /**
     * Manual attendance for testing (optional)
     */
    public function manualCheckin()
    {
        if (!$this->request->isAJAX()) {
            return $this->fail('Only AJAX requests are allowed', 400);
        }
        
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn') || strtolower($session->get('role')) !== 'teknisi') {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }
        
        $data = $this->request->getJSON();
        
        // Validate required fields
        if (empty($data->latitude) || empty($data->longitude)) {
            return $this->respond(['status' => 'error', 'message' => 'Koordinat diperlukan'], 400);
        }
        
        try {
            // Similar to checkin() but with manual validation
            $validCoords = $this->validateAndNormalizeCoordinates($data->latitude, $data->longitude);
            
            if (!$validCoords) {
                return $this->respond(['status' => 'error', 'message' => 'Koordinat tidak valid'], 400);
            }
            
            // You can add additional manual checks here
            
            return $this->respond([
                'status' => 'success',
                'message' => 'Manual check-in validated',
                'coordinates' => $validCoords
            ]);
            
        } catch (\Exception $e) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current location information
     */
    public function getLocationInfo()
    {
        if (!$this->request->isAJAX()) {
            return $this->fail('Only AJAX requests are allowed', 400);
        }
        
        $session = \Config\Services::session();
        if (!$session->get('isLoggedIn') || strtolower($session->get('role')) !== 'teknisi') {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }
        
        $data = $this->request->getJSON();
        
        try {
            if (empty($data->latitude) || empty($data->longitude)) {
                return $this->respond(['status' => 'error', 'message' => 'Koordinat diperlukan'], 400);
            }
            
            $validCoords = $this->validateAndNormalizeCoordinates($data->latitude, $data->longitude);
            
            if (!$validCoords) {
                return $this->respond(['status' => 'error', 'message' => 'Koordinat tidak valid'], 400);
            }
            
            $locationName = $this->getLocationNameFromCoordinates(
                $validCoords['latitude'], 
                $validCoords['longitude']
            );
            
            $isInIndonesia = $this->isLocationInIndonesia(
                $validCoords['latitude'], 
                $validCoords['longitude']
            );
            
            $distanceFromOffice = $this->calculateDistanceFromOffice(
                $validCoords['latitude'], 
                $validCoords['longitude']
            );
            
            return $this->respond([
                'status' => 'success',
                'message' => 'Location information retrieved',
                'data' => [
                    'coordinates' => $validCoords,
                    'location_name' => $locationName,
                    'is_in_indonesia' => $isInIndonesia,
                    'distance_from_office' => $distanceFromOffice,
                    'accuracy_status' => $this->getAccuracyStatus($data->accuracy ?? null)
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}