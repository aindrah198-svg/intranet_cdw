<?php
$title = 'Absensi Teknisi';
$active = 'absensi';
?>

<?= $this->include('teknisi/templates/header') ?>
<?= $this->include('teknisi/templates/sidebar') ?>
<?= $this->include('teknisi/templates/navbar') ?>

<!-- Custom CSS khusus absensi teknisi -->
<style>
/* Loading Screen */
#loadingScreen {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.95);
    z-index: 9999;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    transition: opacity 0.3s;
}

/* Card Styling */
.absensi-card {
    border-radius: 15px;
    transition: all 0.3s;
    border: 2px solid transparent;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

.absensi-card:hover {
    border-color: #4e73df;
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(78, 115, 223, 0.15);
}

/* Button Styling */
.btn-absensi {
    padding: 15px 30px;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 10px;
    transition: all 0.3s;
    min-width: 220px;
    border: none;
}

.btn-absensi:hover {
    transform: translateY(-3px);
    box-shadow: 0 7px 20px rgba(0,0,0,0.15);
}

.btn-absensi:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
}

/* Status Badges */
.status-badge {
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
}

/* Time Display */
.time-display {
    font-family: 'Courier New', monospace;
    font-weight: bold;
    font-size: 1.3rem;
    color: #2e59d9;
}

/* Shift Options */
.shift-option {
    cursor: pointer;
    border: 2px solid #dee2e6;
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    transition: all 0.3s;
    background: white;
}

.shift-option:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

.shift-option.active {
    border-color: #4e73df !important;
    background: linear-gradient(135deg, rgba(78, 115, 223, 0.05), rgba(78, 115, 223, 0.1));
}

/* Table Styling */
.table-absensi {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 0 20px rgba(0,0,0,0.05);
}

.table-absensi th {
    background: linear-gradient(135deg, #4e73df 0%, #2e59d9 100%);
    color: white;
    border: none;
    padding: 15px;
    font-weight: 600;
}

.table-absensi td {
    padding: 12px 15px;
    vertical-align: middle;
    border-bottom: 1px solid #f0f0f0;
}

.table-absensi tr:hover {
    background-color: rgba(78, 115, 223, 0.03);
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .btn-absensi {
        width: 100%;
        min-width: auto;
        margin-bottom: 10px;
    }
    
    .time-display {
        font-size: 1.1rem;
    }
    
    .absensi-card {
        margin-bottom: 20px;
    }
    
    .shift-option {
        margin-bottom: 15px;
    }
}

/* Animations */
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.pulse-animation {
    animation: pulse 0.5s ease-in-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.slide-in {
    animation: slideIn 0.5s ease-out;
}

/* Alert Styling */
.custom-alert {
    border-radius: 10px;
    border: none;
    padding: 20px;
    margin-bottom: 20px;
}

.custom-alert.info {
    background: linear-gradient(135deg, #e7f3ff 0%, #d1e7ff 100%);
    border-left: 5px solid #0d6efd;
}

.custom-alert.success {
    background: linear-gradient(135deg, #d1f2eb 0%, #a3e4d7 100%);
    border-left: 5px solid #28a745;
}

.custom-alert.warning {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    border-left: 5px solid #ffc107;
}

.custom-alert.danger {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    border-left: 5px solid #dc3545;
}

/* Progress Bar */
.progress-container {
    background: #f0f0f0;
    border-radius: 10px;
    overflow: hidden;
    height: 8px;
    margin: 20px 0;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #4e73df, #2e59d9);
    border-radius: 10px;
    transition: width 0.5s ease;
}

/* Icon Styling */
.icon-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 24px;
}

.icon-circle.primary {
    background: linear-gradient(135deg, #4e73df, #2e59d9);
    color: white;
}

.icon-circle.success {
    background: linear-gradient(135deg, #28a745, #1e7e34);
    color: white;
}

.icon-circle.warning {
    background: linear-gradient(135deg, #ffc107, #e0a800);
    color: white;
}

.icon-circle.info {
    background: linear-gradient(135deg, #17a2b8, #117a8b);
    color: white;
}

.icon-circle.danger {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
}

/* Stat Cards */
.stat-card {
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    background: white;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    transition: all 0.3s;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.stat-value {
    font-size: 2rem;
    font-weight: bold;
    color: #4e73df;
    margin: 10px 0;
}

.stat-label {
    color: #6c757d;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Accuracy Badges */
.accuracy-badge {
    font-size: 0.75rem;
    padding: 3px 8px;
    border-radius: 10px;
    margin-left: 5px;
}

.accuracy-very-high {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.accuracy-high {
    background-color: #cce5ff;
    color: #004085;
    border: 1px solid #b8daff;
}

.accuracy-good {
    background-color: #d1ecf1;
    color: #0c5460;
    border: 1px solid #bee5eb;
}

.accuracy-medium {
    background-color: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.accuracy-low {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.accuracy-unknown {
    background-color: #e2e3e5;
    color: #383d41;
    border: 1px solid #d6d8db;
}

/* Location Styling */
.location-info {
    font-size: 0.85rem;
    color: #6c757d;
}

.location-info .accuracy-info {
    margin-top: 5px;
    display: inline-block;
}

/* Toast Styling */
.custom-toast {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1060;
    min-width: 300px;
    max-width: 350px;
}

/* GPS Warning */
.gps-warning {
    background: linear-gradient(135deg, #fff3cd, #ffeaa7);
    border: 1px solid #ffc107;
    border-radius: 8px;
    padding: 10px 15px;
    margin: 10px 0;
}

/* Fade Out Animation */
@keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; }
}
</style>

<!-- Loading Screen -->
<div id="loadingScreen" style="display: none;">
    <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    <h4 class="text-primary mb-2">Memuat Absensi...</h4>
    <p class="text-muted mb-3" id="loadingMessage">Menyiapkan sistem</p>
    <div class="progress-container" style="width: 300px;">
        <div id="loadingProgress" class="progress-bar" style="width: 0%"></div>
    </div>
</div>

<!-- Main Content -->
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-clock me-2"></i>Absensi Teknisi
            </h1>
            <p class="text-muted mb-0">Sistem absensi dengan lokasi GPS yang akurat</p>
        </div>
        <div>
            <button class="btn btn-outline-primary btn-sm" onclick="refreshPage()">
                <i class="fas fa-sync-alt me-1"></i>Refresh
            </button>
            <button class="btn btn-outline-info btn-sm ms-2" onclick="checkLocationInfo()">
                <i class="fas fa-map-marked-alt me-1"></i>Cek Lokasi
            </button>
        </div>
    </div>

    <!-- GPS Warning (akan ditampilkan jika GPS bermasalah) -->
    <div id="gpsWarning" class="gps-warning slide-in mb-4" style="display: none;">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle text-warning fa-lg me-3"></i>
            <div>
                <h6 class="mb-1 text-warning">GPS Tidak Optimal</h6>
                <p class="mb-0 small" id="gpsWarningMessage"></p>
            </div>
        </div>
    </div>

    <!-- Current Time & Date -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="absensi-card p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="text-primary mb-2">
                            <i class="far fa-clock me-2"></i>Waktu Saat Ini
                        </h5>
                        <div class="time-display mb-2" id="currentTime">Loading...</div>
                        <div class="text-muted" id="currentDate">Loading...</div>
                        <small class="text-muted">
                            <i class="fas fa-globe-asia me-1"></i>Zona Waktu: WIB (UTC+7)
                        </small>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="icon-circle primary">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="absensi-card p-4">
                <h5 class="text-primary mb-3">
                    <i class="fas fa-user-check me-2"></i>Status Hari Ini
                </h5>
                <div class="text-center">
                    <div id="statusIndicator" class="mb-3">
                        <?php if (!empty($absensiToday)): ?>
                            <?php if ($absensiToday['waktu_masuk']): ?>
                                <?php if ($absensiToday['waktu_pulang']): ?>
                                    <span class="badge bg-success status-badge">Selesai Bekerja</span>
                                <?php else: ?>
                                    <span class="badge bg-primary status-badge">Sedang Bekerja</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="badge bg-secondary status-badge">Belum Absen</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="badge bg-secondary status-badge">Belum Absen</span>
                        <?php endif; ?>
                    </div>
                    <small class="text-muted" id="statusDetails">
                        <?php if (!empty($absensiToday)): ?>
                            <?php if ($absensiToday['waktu_masuk']): ?>
                                Absen masuk: <?= date('H:i', strtotime($absensiToday['waktu_masuk'])) ?>
                                <?php if ($absensiToday['waktu_pulang']): ?>
                                    <br>Pulang: <?= date('H:i', strtotime($absensiToday['waktu_pulang'])) ?>
                                <?php endif; ?>
                            <?php else: ?>
                                Belum absen hari ini
                            <?php endif; ?>
                        <?php else: ?>
                            Belum absen hari ini
                        <?php endif; ?>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Actions -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="absensi-card p-4 text-center h-100">
                <div class="icon-circle primary">
                    <i class="fas fa-sign-in-alt"></i>
                </div>
                <h4 class="mb-3">Absensi Masuk</h4>
                <p class="text-muted mb-4">Lakukan absensi masuk saat mulai bekerja</p>
                
                <button class="btn btn-primary btn-absensi mb-3" id="btnCheckin" onclick="showShiftSelection()" 
                    <?= (!empty($absensiToday) && $absensiToday['waktu_masuk']) ? 'disabled' : '' ?>>
                    <i class="fas fa-fingerprint me-2"></i> CHECK IN
                </button>
                
                <div class="mt-3">
                    <small id="checkinInfo" class="text-muted">
                        <?php if (!empty($absensiToday) && $absensiToday['waktu_masuk']): ?>
                            <span class="text-success">
                                <i class="fas fa-check-circle me-1"></i>
                                Sudah absen: <?= date('H:i', strtotime($absensiToday['waktu_masuk'])) ?>
                            </span>
                        <?php else: ?>
                            <i class="fas fa-hourglass-start me-1"></i> Belum absen masuk
                        <?php endif; ?>
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="absensi-card p-4 text-center h-100">
                <div class="icon-circle success">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <h4 class="mb-3">Absensi Pulang</h4>
                <p class="text-muted mb-4">Lakukan absensi pulang setelah selesai bekerja</p>
                
                <button class="btn btn-success btn-absensi mb-3" id="btnCheckout" onclick="checkOut()"
                    <?= (empty($absensiToday) || !$absensiToday['waktu_masuk'] || $absensiToday['waktu_pulang']) ? 'disabled' : '' ?>>
                    <i class="fas fa-walking me-2"></i> CHECK OUT
                </button>
                
                <div class="mt-3">
                    <small id="checkoutInfo" class="text-muted">
                        <?php if (!empty($absensiToday) && $absensiToday['waktu_pulang']): ?>
                            <span class="text-success">
                                <i class="fas fa-check-circle me-1"></i>
                                Sudah absen: <?= date('H:i', strtotime($absensiToday['waktu_pulang'])) ?>
                            </span>
                        <?php elseif (!empty($absensiToday) && $absensiToday['waktu_masuk']): ?>
                            <i class="fas fa-hourglass-end me-1"></i> Belum absen pulang
                        <?php else: ?>
                            <i class="fas fa-clock me-1"></i> Belum bisa absen pulang
                        <?php endif; ?>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Location Info - Diperbarui dengan informasi akurasi -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="absensi-card p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="text-primary mb-2">
                            <i class="fas fa-map-marker-alt me-2"></i>Lokasi GPS Saat Ini
                        </h5>
                        <div class="mb-2">
                            <span id="locationStatus">
                                <i class="fas fa-spinner fa-spin me-1"></i> Mendeteksi lokasi GPS...
                            </span>
                        </div>
                        <div class="location-info">
                            <div id="coordinates" class="mb-1"></div>
                            <div id="locationName" class="mb-1"></div>
                            <div id="accuracyInfo">
                                <span id="accuracyValue" class="accuracy-info"></span>
                                <span id="accuracyBadge" class="accuracy-badge"></span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <button class="btn btn-outline-primary btn-sm me-2" onclick="refreshLocation()">
                            <i class="fas fa-redo me-1"></i> Perbarui Lokasi
                        </button>
                        <button class="btn btn-outline-info btn-sm" onclick="getDetailedLocationInfo()">
                            <i class="fas fa-info-circle me-1"></i> Detail
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tips untuk lokasi akurat -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="absensi-card p-3 bg-light">
                <div class="d-flex align-items-center">
                    <i class="fas fa-lightbulb text-warning fa-2x me-3"></i>
                    <div>
                        <h6 class="mb-1">Tips untuk Lokasi Akurat:</h6>
                        <ul class="mb-0" style="font-size: 0.9rem;">
                            <li>Pastikan GPS aktif di perangkat Anda</li>
                            <li>Berikan izin akses lokasi ke browser</li>
                            <li>Buka area terbuka untuk sinyal GPS yang lebih baik</li>
                            <li>Pastikan koneksi internet stabil</li>
                            <li>Tunggu beberapa detik hingga akurasi tinggi (< 20m)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Attendance Details -->
    <div class="row">
        <div class="col-md-8">
            <div class="absensi-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="text-primary mb-0">
                        <i class="fas fa-calendar-day me-2"></i>Detail Absensi Hari Ini
                    </h5>
                    <button class="btn btn-sm btn-outline-primary" onclick="loadTodayAttendance()">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-absensi">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Shift</th>
                                <th>Masuk</th>
                                <th>Pulang</th>
                                <th>Jam Kerja</th>
                                <th>Status</th>
                                <th>Terlambat</th>
                            </tr>
                        </thead>
                        <tbody id="todayAttendanceBody">
                            <?php if (!empty($absensiToday)): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($absensiToday['tanggal'])) ?></td>
                                    <td>
                                        <?php
                                        $shift_names = [
                                            'pagi' => ['name' => 'Pagi', 'color' => 'warning'],
                                            'siang' => ['name' => 'Siang', 'color' => 'success'],
                                            'sore' => ['name' => 'Sore', 'color' => 'info'],
                                            'malam' => ['name' => 'Malam', 'color' => 'primary']
                                        ];
                                        $shift = $absensiToday['shift'] ?? 'siang';
                                        ?>
                                        <span class="badge bg-<?= $shift_names[$shift]['color'] ?>">
                                            <?= $shift_names[$shift]['name'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($absensiToday['waktu_masuk']): ?>
                                            <?= date('H:i', strtotime($absensiToday['waktu_masuk'])) ?>
                                            <?php if ($absensiToday['lokasi_masuk'] && $absensiToday['lokasi_masuk'] != 'Lokasi tidak terdeteksi'): ?>
                                                <br><small class="text-muted"><?= htmlspecialchars(substr($absensiToday['lokasi_masuk'], 0, 30)) ?>...</small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($absensiToday['waktu_pulang']): ?>
                                            <?= date('H:i', strtotime($absensiToday['waktu_pulang'])) ?>
                                            <?php if ($absensiToday['lokasi_pulang'] && $absensiToday['lokasi_pulang'] != 'Lokasi tidak terdeteksi'): ?>
                                                <br><small class="text-muted"><?= htmlspecialchars(substr($absensiToday['lokasi_pulang'], 0, 30)) ?>...</small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($absensiToday['jam_kerja']): ?>
                                            <?= number_format($absensiToday['jam_kerja'], 1) ?> jam
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $status_badge = [
                                            'Hadir' => 'success',
                                            'Izin' => 'info',
                                            'Sakit' => 'warning',
                                            'Cuti' => 'primary'
                                        ];
                                        $status = $absensiToday['status'] ?? 'Belum Absen';
                                        ?>
                                        <span class="badge bg-<?= $status_badge[$status] ?? 'secondary' ?>">
                                            <?= $status ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($absensiToday['terlambat'] && $absensiToday['terlambat'] > 0): ?>
                                            <?php
                                            $terlambat = $absensiToday['terlambat'];
                                            $jam = floor($terlambat / 60);
                                            $menit = $terlambat % 60;
                                            if ($jam > 0 && $menit > 0) {
                                                echo "<span class='badge bg-danger'>{$jam}j {$menit}m</span>";
                                            } elseif ($jam > 0) {
                                                echo "<span class='badge bg-danger'>{$jam} jam</span>";
                                            } else {
                                                echo "<span class='badge bg-danger'>{$menit}m</span>";
                                            }
                                            ?>
                                        <?php else: ?>
                                            <span class="badge bg-success">Tepat waktu</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-calendar-times fa-2x text-muted mb-3 d-block"></i>
                                        Belum ada absensi hari ini
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="absensi-card p-4 h-100">
                <h5 class="text-primary mb-4">
                    <i class="fas fa-chart-bar me-2"></i>Statistik Bulan Ini
                </h5>
                
                <div class="row">
                    <div class="col-6 mb-4">
                        <div class="stat-card">
                            <i class="fas fa-calendar-check fa-2x text-primary mb-3"></i>
                            <div class="stat-value" id="statHadir"><?= $stats['hadir_bulan_ini'] ?? 0 ?></div>
                            <div class="stat-label">Hadir</div>
                        </div>
                    </div>
                    <div class="col-6 mb-4">
                        <div class="stat-card">
                            <i class="fas fa-clock fa-2x text-warning mb-3"></i>
                            <div class="stat-value" id="statTerlambat"><?= $stats['terlambat_bulan_ini'] ?? 0 ?></div>
                            <div class="stat-label">Terlambat</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card">
                            <i class="fas fa-clock fa-2x text-orange mb-3"></i>
                            <div class="stat-value" id="statLembur"><?= number_format($stats['jam_lembur_bulan_ini'] ?? 0, 1) ?></div>
                            <div class="stat-label">Jam Lembur</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card">
                            <i class="fas fa-calendar-alt fa-2x text-info mb-3"></i>
                            <div class="stat-value" id="statCuti"><?= $stats['cuti_terpakai'] ?? 0 ?></div>
                            <div class="stat-label">Cuti Terpakai</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Attendance History -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="absensi-card p-4">
                <h5 class="text-primary mb-4">
                    <i class="fas fa-history me-2"></i>Riwayat Absensi Terbaru (7 hari)
                </h5>
                
                <div class="table-responsive">
                    <table class="table table-absensi">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Shift</th>
                                <th>Masuk</th>
                                <th>Pulang</th>
                                <th>Jam Kerja</th>
                                <th>Status</th>
                                <th>Terlambat</th>
                            </tr>
                        </thead>
                        <tbody id="attendanceHistoryBody">
                            <?php if (!empty($absensiHistory)): ?>
                                <?php foreach ($absensiHistory as $absensi): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($absensi['tanggal'])) ?></td>
                                        <td>
                                            <?php
                                            $shift_names = [
                                                'pagi' => 'Pagi',
                                                'siang' => 'Siang',
                                                'sore' => 'Sore',
                                                'malam' => 'Malam'
                                            ];
                                            $shift = $absensi['shift'] ?? '';
                                            $shift_colors = [
                                                'pagi' => 'warning',
                                                'siang' => 'success',
                                                'sore' => 'info',
                                                'malam' => 'primary'
                                            ];
                                            ?>
                                            <span class="badge bg-<?= $shift_colors[$shift] ?? 'secondary' ?>">
                                                <?= $shift_names[$shift] ?? '-' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($absensi['waktu_masuk']): ?>
                                                <?= date('H:i', strtotime($absensi['waktu_masuk'])) ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($absensi['waktu_pulang']): ?>
                                                <?= date('H:i', strtotime($absensi['waktu_pulang'])) ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($absensi['jam_kerja']): ?>
                                                <?= number_format($absensi['jam_kerja'], 1) ?> jam
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $status_badge = [
                                                'Hadir' => 'success',
                                                'Izin' => 'info',
                                                'Sakit' => 'warning',
                                                'Cuti' => 'primary'
                                            ];
                                            $status = $absensi['status'] ?? '-';
                                            ?>
                                            <span class="badge bg-<?= $status_badge[$status] ?? 'secondary' ?>">
                                                <?= $status ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($absensi['terlambat'] && $absensi['terlambat'] > 0): ?>
                                                <?= $absensi['terlambat'] ?>m
                                            <?php else: ?>
                                                <span class="badge bg-success">Tepat</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-clipboard-list fa-2x text-muted mb-3 d-block"></i>
                                        Tidak ada riwayat absensi
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Shift Selection Modal -->
<div class="modal fade" id="shiftModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-clock me-2"></i>Pilih Shift Kerja
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="icon-circle primary mb-3">
                        <i class="fas fa-business-time"></i>
                    </div>
                    <h4>Shift Kerja Hari Ini</h4>
                    <p class="text-muted">Silakan pilih shift kerja yang akan Anda jalani</p>
                </div>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="shift-option" onclick="selectShift('pagi')">
                            <i class="fas fa-sun fa-2x text-warning mb-3"></i>
                            <h5>Shift Pagi</h5>
                            <p class="small text-muted mb-1">07:00 - 16:00</p>
                            <span class="badge bg-warning">9 jam kerja</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="shift-option" onclick="selectShift('siang')">
                            <i class="fas fa-cloud-sun fa-2x text-success mb-3"></i>
                            <h5>Shift Siang</h5>
                            <p class="small text-muted mb-1">08:00 - 17:00</p>
                            <span class="badge bg-success">9 jam kerja</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="shift-option" onclick="selectShift('sore')">
                            <i class="fas fa-moon fa-2x text-info mb-3"></i>
                            <h5>Shift Sore</h5>
                            <p class="small text-muted mb-1">09:00 - 18:00</p>
                            <span class="badge bg-info">9 jam kerja</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="shift-option" onclick="selectShift('malam')">
                            <i class="fas fa-star fa-2x text-primary mb-3"></i>
                            <h5>Shift Malam</h5>
                            <p class="small text-muted mb-1">20:00 - 05:00</p>
                            <span class="badge bg-primary">9 jam kerja</span>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    <small>
                        <strong>Toleransi terlambat:</strong> 30 menit untuk semua shift<br>
                        <strong>Shift malam:</strong> Berlangsung dari jam 20:00 sampai besok jam 05:00<br>
                        <strong>Catatan:</strong> Pastikan GPS aktif untuk absensi dengan lokasi akurat
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmShiftBtn" onclick="proceedCheckInWithValidation()" disabled>
                    <i class="fas fa-check me-1"></i> Konfirmasi & Check In
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Checkout Confirmation Modal -->
<div class="modal fade" id="checkoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-sign-out-alt me-2"></i>Konfirmasi Check Out
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="icon-circle success mb-3">
                        <i class="fas fa-walking"></i>
                    </div>
                    <h4>Check Out Sekarang?</h4>
                    <p class="text-muted">Anda akan melakukan absensi pulang</p>
                </div>
                
                <div id="checkoutLocationWarning" class="alert alert-warning" style="display: none;">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <div id="checkoutLocationMessage"></div>
                </div>
                
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Pastikan Anda sudah menyelesaikan pekerjaan sebelum check out
                </div>
                
                <div class="mb-3">
                    <label for="checkoutNote" class="form-label">Catatan (opsional):</label>
                    <textarea class="form-control" id="checkoutNote" rows="2" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" onclick="confirmCheckOut()">
                    <i class="fas fa-check me-1"></i> Ya, Check Out
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="icon-circle success mb-4" style="width: 80px; height: 80px;">
                    <i class="fas fa-check fa-2x"></i>
                </div>
                <h3 class="text-success mb-3" id="successTitle">Sukses!</h3>
                <p id="successMessage" class="mb-4"></p>
                <div id="successDetails" class="text-start mb-4" style="display: none;">
                    <small class="text-muted">
                        <strong>Detail:</strong><br>
                        <span id="successDetailContent"></span>
                    </small>
                </div>
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="icon-circle danger mb-4" style="width: 80px; height: 80px; background: linear-gradient(135deg, #dc3545, #c82333);">
                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                </div>
                <h3 class="text-danger mb-3" id="errorTitle">Error!</h3>
                <p id="errorMessage" class="mb-4"></p>
                <div id="errorSolution" class="text-start mb-4" style="display: none;">
                    <small class="text-muted">
                        <strong>Solusi:</strong><br>
                        <span id="errorSolutionContent"></span>
                    </small>
                </div>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Location Details Modal -->
<div class="modal fade" id="locationDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-map-marked-alt me-2"></i>Detail Lokasi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="fas fa-satellite-dish fa-3x text-info mb-3"></i>
                    <h4>Informasi Lokasi GPS</h4>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <th>Koordinat</th>
                                <td id="detailCoordinates"></td>
                            </tr>
                            <tr>
                                <th>Nama Lokasi</th>
                                <td id="detailLocationName"></td>
                            </tr>
                            <tr>
                                <th>Akurasi</th>
                                <td>
                                    <span id="detailAccuracy"></span>
                                    <span id="detailAccuracyBadge" class="accuracy-badge"></span>
                                </td>
                            </tr>
                            <tr>
                                <th>Status GPS</th>
                                <td id="detailGPSStatus"></td>
                            </tr>
                            <tr>
                                <th>Waktu Update</th>
                                <td id="detailTimestamp"></td>
                            </tr>
                            <tr>
                                <th>Sumber</th>
                                <td id="detailSource"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <small>
                        <strong>Keterangan Akurasi:</strong><br>
                        • < 20m: Sangat tinggi (GPS aktif)<br>
                        • 20-50m: Tinggi (GPS baik)<br>
                        • 50-100m: Baik (GPS sedang)<br>
                        • 100-500m: Sedang (GPS lemah)<br>
                        • > 500m: Rendah (perkiraan jaringan)
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-info" onclick="refreshLocation()">
                    <i class="fas fa-redo me-1"></i> Perbarui
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
// Global variables
let currentLocation = null;
let selectedShift = null;
let isProcessing = false;
let locationWatchId = null;

// Show loading screen
function showLoading(message = 'Memuat...') {
    document.getElementById('loadingScreen').style.display = 'flex';
    document.getElementById('loadingMessage').textContent = message;
}

function hideLoading() {
    document.getElementById('loadingScreen').style.display = 'none';
}

function updateLoadingProgress(percent, message = null) {
    document.getElementById('loadingProgress').style.width = percent + '%';
    if (message) {
        document.getElementById('loadingMessage').textContent = message;
    }
}

// Update current time
function updateCurrentTime() {
    const now = new Date();
    const timeStr = now.toLocaleTimeString('id-ID', { 
        hour: '2-digit', 
        minute: '2-digit',
        second: '2-digit'
    });
    const dateStr = now.toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    document.getElementById('currentTime').textContent = timeStr;
    document.getElementById('currentDate').textContent = dateStr;
}

// Get accuracy status and CSS class
function getAccuracyStatus(accuracy) {
    if (accuracy === null || accuracy === undefined) {
        return { status: 'unknown', label: 'Tidak diketahui', class: 'accuracy-unknown' };
    }
    
    const acc = parseFloat(accuracy);
    if (acc <= 20) {
        return { status: 'very_high', label: 'Sangat tinggi', class: 'accuracy-very-high' };
    } else if (acc <= 50) {
        return { status: 'high', label: 'Tinggi', class: 'accuracy-high' };
    } else if (acc <= 100) {
        return { status: 'good', label: 'Baik', class: 'accuracy-good' };
    } else if (acc <= 500) {
        return { status: 'medium', label: 'Sedang', class: 'accuracy-medium' };
    } else {
        return { status: 'low', label: 'Rendah', class: 'accuracy-low' };
    }
}

// Update GPS warning display
function updateGPSWarning(location) {
    const warningDiv = document.getElementById('gpsWarning');
    const warningMessage = document.getElementById('gpsWarningMessage');
    
    if (!location || !location.accuracy || location.accuracy > 100) {
        if (location && location.accuracy) {
            warningMessage.textContent = `Akurasi GPS rendah (${Math.round(location.accuracy)}m). Untuk hasil terbaik, aktifkan GPS dan pastikan di area terbuka.`;
        } else {
            warningMessage.textContent = 'GPS tidak terdeteksi. Untuk absensi akurat, aktifkan GPS di perangkat Anda.';
        }
        warningDiv.style.display = 'block';
    } else {
        warningDiv.style.display = 'none';
    }
}

// Get location with high accuracy
async function getLocation() {
    return new Promise((resolve, reject) => {
        if (!navigator.geolocation) {
            const error = new Error('Browser tidak mendukung geolocation');
            updateLocationDisplay(null, error);
            reject(error);
            return;
        }
        
        document.getElementById('locationStatus').innerHTML = 
            '<i class="fas fa-spinner fa-spin me-1"></i> Mendeteksi lokasi GPS dengan akurasi tinggi...';
        
        const options = {
            enableHighAccuracy: true,
            timeout: 15000,
            maximumAge: 0
        };
        
        navigator.geolocation.getCurrentPosition(
            (position) => {
                currentLocation = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    accuracy: position.coords.accuracy,
                    altitude: position.coords.altitude,
                    altitudeAccuracy: position.coords.altitudeAccuracy,
                    heading: position.coords.heading,
                    speed: position.coords.speed,
                    timestamp: position.timestamp,
                    source: 'gps'
                };
                
                updateLocationDisplay(currentLocation);
                updateGPSWarning(currentLocation);
                resolve(currentLocation);
            },
            (error) => {
                let errorMessage = 'Tidak dapat mendapatkan lokasi';
                let icon = 'fa-exclamation-triangle';
                
                if (error.code === error.PERMISSION_DENIED) {
                    errorMessage = 'Izin lokasi ditolak. Silakan berikan izin akses lokasi di pengaturan browser.';
                    icon = 'fa-ban';
                } else if (error.code === error.POSITION_UNAVAILABLE) {
                    errorMessage = 'GPS tidak tersedia. Pastikan GPS aktif di perangkat Anda.';
                    icon = 'fa-satellite-dish';
                } else if (error.code === error.TIMEOUT) {
                    errorMessage = 'Timeout mendapatkan lokasi. Coba lagi dalam beberapa detik.';
                    icon = 'fa-hourglass-end';
                }
                
                currentLocation = {
                    latitude: null,
                    longitude: null,
                    error: errorMessage,
                    code: error.code,
                    source: 'error'
                };
                
                updateLocationDisplay(currentLocation, error);
                updateGPSWarning(currentLocation);
                resolve(currentLocation);
            },
            options
        );
    });
}

// Update location display
function updateLocationDisplay(location, error = null) {
    if (error || !location || !location.latitude) {
        let errorMessage = error ? error.message : (location?.error || 'Lokasi tidak terdeteksi');
        let icon = 'fa-exclamation-triangle';
        
        if (error?.code === 1) icon = 'fa-ban';
        if (error?.code === 2) icon = 'fa-satellite-dish';
        if (error?.code === 3) icon = 'fa-hourglass-end';
        
        document.getElementById('locationStatus').innerHTML = 
            `<span class="text-warning"><i class="fas ${icon} me-1"></i> ${errorMessage}</span>`;
        document.getElementById('coordinates').textContent = '';
        document.getElementById('locationName').textContent = '';
        document.getElementById('accuracyInfo').innerHTML = '';
        
    } else {
        const lat = location.latitude.toFixed(8);
        const lng = location.longitude.toFixed(8);
        const accuracy = Math.round(location.accuracy || 0);
        const accStatus = getAccuracyStatus(location.accuracy);
        
        document.getElementById('locationStatus').innerHTML = 
            `<span class="text-success"><i class="fas fa-check-circle me-1"></i> Lokasi GPS terdeteksi</span>`;
        document.getElementById('coordinates').textContent = `Koordinat: ${lat}, ${lng}`;
        document.getElementById('accuracyValue').textContent = `Akurasi: ${accuracy}m`;
        document.getElementById('accuracyBadge').textContent = accStatus.label;
        document.getElementById('accuracyBadge').className = `accuracy-badge ${accStatus.class}`;
        
        // Get location name from server
        getLocationName(location.latitude, location.longitude);
    }
}

// Get location name from server
async function getLocationName(latitude, longitude) {
    try {
        const response = await fetch('<?= base_url("teknisi/absensi/getLocationInfo"); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ latitude, longitude })
        });
        
        if (response.ok) {
            const result = await response.json();
            if (result.status === 'success') {
                document.getElementById('locationName').textContent = result.data.location_name;
            }
        }
    } catch (error) {
        console.error('Error getting location name:', error);
    }
}

// Get detailed location info
async function getDetailedLocationInfo() {
    if (!currentLocation || !currentLocation.latitude) {
        showError('Lokasi Tidak Tersedia', 'Tidak ada data lokasi yang tersedia. Silakan perbarui lokasi terlebih dahulu.');
        return;
    }
    
    try {
        const response = await fetch('<?= base_url("teknisi/absensi/getLocationInfo"); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ 
                latitude: currentLocation.latitude, 
                longitude: currentLocation.longitude,
                accuracy: currentLocation.accuracy 
            })
        });
        
        if (!response.ok) throw new Error('Network response was not ok');
        
        const result = await response.json();
        
        if (result.status === 'success') {
            const data = result.data;
            const accStatus = getAccuracyStatus(data.coordinates?.accuracy || currentLocation.accuracy);
            const now = new Date();
            
            document.getElementById('detailCoordinates').textContent = 
                `${data.coordinates.latitude}, ${data.coordinates.longitude}`;
            document.getElementById('detailLocationName').textContent = 
                data.location_name || 'Tidak diketahui';
            document.getElementById('detailAccuracy').textContent = 
                `${Math.round(data.coordinates?.accuracy || currentLocation.accuracy || 0)} meter`;
            document.getElementById('detailAccuracyBadge').textContent = accStatus.label;
            document.getElementById('detailAccuracyBadge').className = `accuracy-badge ${accStatus.class}`;
            document.getElementById('detailGPSStatus').textContent = 
                data.is_in_indonesia ? 'Di dalam Indonesia' : 'Di luar Indonesia';
            document.getElementById('detailTimestamp').textContent = 
                now.toLocaleString('id-ID');
            document.getElementById('detailSource').textContent = 
                currentLocation.source === 'gps' ? 'GPS Satellite' : 'Network/IP';
            
            $('#locationDetailsModal').modal('show');
        }
        
    } catch (error) {
        console.error('Error getting location details:', error);
        showError('Detail Lokasi', 'Gagal mendapatkan detail lokasi: ' + error.message);
    }
}

// Refresh location
async function refreshLocation() {
    try {
        showLoading('Memperbarui lokasi GPS...');
        await getLocation();
        hideLoading();
        showToast('Lokasi berhasil diperbarui', 'success');
    } catch (error) {
        hideLoading();
        console.error('Error refreshing location:', error);
        showToast('Gagal memperbarui lokasi', 'error');
    }
}

// Check location info
function checkLocationInfo() {
    if (!currentLocation || !currentLocation.latitude) {
        showError('Lokasi Tidak Tersedia', 'Tidak ada data lokasi yang tersedia. Silakan perbarui lokasi terlebih dahulu.');
        return;
    }
    getDetailedLocationInfo();
}

// Show shift selection modal
function showShiftSelection() {
    // First check if location is available
    if (!currentLocation || !currentLocation.latitude) {
        showError('Lokasi Diperlukan', 'Sistem membutuhkan lokasi GPS untuk absensi. Silakan perbarui lokasi terlebih dahulu.');
        refreshLocation();
        return;
    }
    
    // Reset selection
    document.querySelectorAll('.shift-option').forEach(option => {
        option.classList.remove('active');
    });
    document.getElementById('confirmShiftBtn').disabled = true;
    selectedShift = null;
    
    $('#shiftModal').modal('show');
}

// Select shift
function selectShift(shift) {
    // Remove active class from all options
    document.querySelectorAll('.shift-option').forEach(option => {
        option.classList.remove('active');
    });
    
    // Add active class to selected option
    const shiftOptions = document.querySelectorAll('.shift-option');
    const shiftMap = { 'pagi': 0, 'siang': 1, 'sore': 2, 'malam': 3 };
    const index = shiftMap[shift];
    
    if (index >= 0 && index < shiftOptions.length) {
        shiftOptions[index].classList.add('active');
        selectedShift = shift;
        document.getElementById('confirmShiftBtn').disabled = false;
    }
}

// Validate location before check in
function validateLocationForCheckIn() {
    if (!currentLocation || !currentLocation.latitude) {
        return {
            valid: false,
            message: 'Lokasi tidak terdeteksi',
            solution: 'Perbarui lokasi GPS terlebih dahulu'
        };
    }
    
    if (currentLocation.accuracy && currentLocation.accuracy > 500) {
        return {
            valid: false,
            message: 'Akurasi GPS terlalu rendah',
            solution: 'Pindah ke area terbuka atau tunggu GPS stabil'
        };
    }
    
    if (currentLocation.error) {
        return {
            valid: false,
            message: currentLocation.error,
            solution: 'Periksa pengaturan GPS dan izin browser'
        };
    }
    
    return {
        valid: true,
        message: 'Lokasi valid untuk absensi',
        accuracy: currentLocation.accuracy
    };
}

// Proceed with check in with validation
async function proceedCheckInWithValidation() {
    if (!selectedShift || isProcessing) return;
    
    // Validate location first
    const locationValidation = validateLocationForCheckIn();
    if (!locationValidation.valid) {
        showError('Lokasi Tidak Valid', 
            locationValidation.message,
            locationValidation.solution || 'Silakan perbarui lokasi GPS Anda');
        return;
    }
    
    // Check if accuracy is low but acceptable
    if (locationValidation.accuracy && locationValidation.accuracy > 100) {
        const confirm = await showConfirmDialog(
            'Akurasi GPS Sedang',
            `Akurasi GPS saat ini ${Math.round(locationValidation.accuracy)}m (disarankan < 20m). Lanjutkan?`,
            'Lanjutkan',
            'Perbarui'
        );
        
        if (!confirm) {
            refreshLocation();
            return;
        }
    }
    
    proceedCheckIn();
}

// Proceed with check in
async function proceedCheckIn() {
    if (!selectedShift || isProcessing) return;
    
    isProcessing = true;
    $('#shiftModal').modal('hide');
    
    // Show loading
    const btn = document.getElementById('btnCheckin');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memproses...';
    btn.disabled = true;
    
    try {
        // Prepare data with accuracy information
        const data = {
            shift: selectedShift,
            latitude_masuk: currentLocation?.latitude || null,
            longitude_masuk: currentLocation?.longitude || null,
            accuracy: currentLocation?.accuracy || null,
            altitude: currentLocation?.altitude || null,
            heading: currentLocation?.heading || null,
            speed: currentLocation?.speed || null
        };
        
        // Send check in request
        const response = await fetch('<?= base_url("teknisi/absensi/checkin"); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        });
        
        if (!response.ok) throw new Error('Network response was not ok');
        
        const result = await response.json();
        
        if (result.status === 'success') {
            // Update UI
            updateCheckInUI(result.data);
            
            // Show success with location details
            const locationMsg = result.data.lokasi_masuk || 'lokasi terdeteksi';
            const accuracyMsg = result.location_info?.accuracy ? 
                ` (Akurasi: ${Math.round(result.location_info.accuracy)}m)` : '';
            
            showSuccessWithDetails(
                'Check In Berhasil', 
                `Anda berhasil melakukan absensi masuk di ${locationMsg}${accuracyMsg}`,
                result.location_info
            );
            
            // Reload data after 1 second
            setTimeout(() => {
                location.reload();
            }, 1000);
            
        } else {
            throw new Error(result.message || 'Gagal check in');
        }
        
    } catch (error) {
        console.error('Check in error:', error);
        
        // Tampilkan pesan error yang lebih spesifik
        let errorMessage = error.message;
        let solution = '';
        
        if (errorMessage.includes('GPS') || errorMessage.includes('lokasi')) {
            solution = 'Aktifkan GPS, berikan izin lokasi, dan pastikan di area terbuka.';
        } else if (errorMessage.includes('Indonesia')) {
            solution = 'Pastikan Anda berada dalam wilayah Indonesia saat absensi.';
        }
        
        showError('Check In Gagal', errorMessage, solution);
        
        // Reset button
        btn.innerHTML = originalText;
        btn.disabled = false;
    } finally {
        isProcessing = false;
    }
}

// Show check out confirmation
function checkOut() {
    // Validate location for checkout
    const locationValidation = validateLocationForCheckIn();
    const warningDiv = document.getElementById('checkoutLocationWarning');
    const warningMsg = document.getElementById('checkoutLocationMessage');
    
    if (!locationValidation.valid) {
        warningMsg.textContent = locationValidation.message + '. ' + (locationValidation.solution || '');
        warningDiv.style.display = 'block';
    } else {
        warningDiv.style.display = 'none';
    }
    
    $('#checkoutModal').modal('show');
}

// Confirm check out
async function confirmCheckOut() {
    if (isProcessing) return;
    
    isProcessing = true;
    $('#checkoutModal').modal('hide');
    
    const btn = document.getElementById('btnCheckout');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memproses...';
    btn.disabled = true;
    
    const note = document.getElementById('checkoutNote').value;
    
    try {
        // Validate location
        if (!currentLocation || !currentLocation.latitude) {
            await getLocation();
        }
        
        // Prepare data
        const data = {
            latitude_pulang: currentLocation?.latitude || null,
            longitude_pulang: currentLocation?.longitude || null,
            accuracy: currentLocation?.accuracy || null,
            keterangan: note || ''
        };
        
        // Send check out request
        const response = await fetch('<?= base_url("teknisi/absensi/checkout"); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        });
        
        if (!response.ok) throw new Error('Network response was not ok');
        
        const result = await response.json();
        
        if (result.status === 'success') {
            // Clear note
            document.getElementById('checkoutNote').value = '';
            
            // Show success with details
            const locationMsg = result.data.lokasi_pulang || 'lokasi terdeteksi';
            const accuracyMsg = result.location_info?.accuracy ? 
                ` (Akurasi: ${Math.round(result.location_info.accuracy)}m)` : '';
            
            showSuccessWithDetails(
                'Check Out Berhasil', 
                `Anda berhasil melakukan absensi pulang di ${locationMsg}${accuracyMsg}`,
                result.location_info
            );
            
            // Reload page after 1 second
            setTimeout(() => {
                location.reload();
            }, 1000);
            
        } else {
            throw new Error(result.message || 'Gagal check out');
        }
        
    } catch (error) {
        console.error('Check out error:', error);
        showError('Check Out Gagal', error.message || 'Terjadi kesalahan saat check out');
        
        // Reset button
        btn.innerHTML = originalText;
        btn.disabled = false;
    } finally {
        isProcessing = false;
    }
}

// Update UI after check in
function updateCheckInUI(data) {
    // Update status
    document.getElementById('statusIndicator').innerHTML = 
        '<span class="badge bg-primary status-badge">Sedang Bekerja</span>';
    
    // Update check in info
    if (data.waktu_masuk) {
        const waktuMasuk = new Date(data.waktu_masuk).toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit'
        });
        document.getElementById('checkinInfo').innerHTML = 
            `<span class="text-success"><i class="fas fa-check-circle me-1"></i> Sudah absen: ${waktuMasuk}</span>`;
    }
    
    // Enable check out button
    document.getElementById('btnCheckout').disabled = false;
    
    // Update status details
    if (data.waktu_masuk) {
        const waktuMasuk = new Date(data.waktu_masuk).toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit'
        });
        document.getElementById('statusDetails').textContent = `Absen masuk: ${waktuMasuk}`;
    }
}

// Load today's attendance
async function loadTodayAttendance() {
    try {
        location.reload();
    } catch (error) {
        console.error('Error loading attendance:', error);
        showToast('Gagal memuat data absensi', 'error');
    }
}

// Show success modal with details
function showSuccessWithDetails(title, message, details = null) {
    document.getElementById('successTitle').textContent = title;
    document.getElementById('successMessage').textContent = message;
    
    if (details) {
        const detailContent = document.getElementById('successDetailContent');
        detailContent.innerHTML = '';
        
        if (details.lokasi) {
            detailContent.innerHTML += `<strong>Lokasi:</strong> ${details.lokasi}<br>`;
        }
        if (details.accuracy) {
            detailContent.innerHTML += `<strong>Akurasi:</strong> ${Math.round(details.accuracy)} meter<br>`;
        }
        if (details.distance_from_checkin) {
            detailContent.innerHTML += `<strong>Jarak dari check-in:</strong> ${details.distance_from_checkin} meter<br>`;
        }
        if (details.distance_from_office) {
            detailContent.innerHTML += `<strong>Jarak dari kantor:</strong> ${details.distance_from_office} meter<br>`;
        }
        
        document.getElementById('successDetails').style.display = 'block';
    } else {
        document.getElementById('successDetails').style.display = 'none';
    }
    
    $('#successModal').modal('show');
}

// Show error modal with solution
function showError(title, message, solution = '') {
    document.getElementById('errorTitle').textContent = title;
    document.getElementById('errorMessage').textContent = message;
    
    if (solution) {
        document.getElementById('errorSolutionContent').textContent = solution;
        document.getElementById('errorSolution').style.display = 'block';
    } else {
        document.getElementById('errorSolution').style.display = 'none';
    }
    
    $('#errorModal').modal('show');
}

// Show confirm dialog
function showConfirmDialog(title, text, confirmText = 'Ya', cancelText = 'Tidak') {
    return new Promise((resolve) => {
        Swal.fire({
            title: title,
            text: text,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#858796',
            confirmButtonText: confirmText,
            cancelButtonText: cancelText
        }).then((result) => {
            resolve(result.isConfirmed);
        });
    });
}

// Show toast notification
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'error' ? 'danger' : type} border-0`;
    toast.style.position = 'fixed';
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '1060';
    toast.style.minWidth = '300px';
    toast.style.animation = 'slideIn 0.3s ease-out';
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${type === 'success' ? '<i class="fas fa-check-circle me-2"></i>' : 
                  type === 'error' ? '<i class="fas fa-exclamation-circle me-2"></i>' : 
                  '<i class="fas fa-info-circle me-2"></i>'}
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        toast.style.animation = 'fadeOut 0.3s ease-out';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

// Refresh page
function refreshPage() {
    showLoading('Memperbarui data...');
    setTimeout(() => {
        location.reload();
    }, 500);
}

// Initialize page
async function initializePage() {
    showLoading('Menyiapkan sistem absensi...');
    updateLoadingProgress(30, 'Mendeteksi lokasi GPS...');
    
    try {
        // Start clock
        updateCurrentTime();
        setInterval(updateCurrentTime, 1000);
        
        // Get location
        await getLocation();
        
        updateLoadingProgress(70, 'Memuat data absensi...');
        
        // Simulate loading data
        await new Promise(resolve => setTimeout(resolve, 500));
        
        updateLoadingProgress(100, 'Sistem siap!');
        
        // Hide loading after delay
        setTimeout(() => {
            hideLoading();
            
            // Show welcome message based on location accuracy
            if (currentLocation && currentLocation.accuracy) {
                if (currentLocation.accuracy <= 20) {
                    showToast('GPS aktif dengan akurasi tinggi', 'success');
                } else if (currentLocation.accuracy <= 100) {
                    showToast('GPS aktif dengan akurasi baik', 'info');
                } else {
                    showToast('Akurasi GPS rendah. Untuk hasil terbaik, aktifkan GPS', 'warning');
                }
            } else {
                showToast('Sistem absensi siap digunakan', 'info');
            }
        }, 500);
        
    } catch (error) {
        console.error('Initialization error:', error);
        hideLoading();
        showToast('Sistem siap dengan keterbatasan GPS', 'warning');
    }
}

// Start initialization
document.addEventListener('DOMContentLoaded', initializePage);

// Handle page visibility change
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        updateCurrentTime();
    }
});
</script>

<?= $this->include('teknisi/templates/footer') ?>