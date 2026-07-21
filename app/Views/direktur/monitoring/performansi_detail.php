<?php
// Data dari controller
$performansi = $performansi ?? [];
$trendData = $trendData ?? [];
$monthNames = $monthNames ?? [];

// Helper functions
if (!function_exists('formatScore')) {
    function formatScore($score) {
        if (empty($score) && $score !== 0) return '-';
        return number_format((float)$score, 1);
    }
}

if (!function_exists('formatNumber')) {
    function formatNumber($num) {
        if ($num === null || $num === '') return '0';
        return number_format((float)$num, 0);
    }
}

if (!function_exists('hitungPersen')) {
    function hitungPersen($realisasi, $target) {
        $target = floatval($target);
        $realisasi = floatval($realisasi);
        
        if ($target <= 0) {
            return $realisasi > 0 ? 100 : 0;
        }
        
        return ($realisasi / $target) * 100;
    }
}

if (!function_exists('formatPersen')) {
    function formatPersen($realisasi, $target) {
        $persen = hitungPersen($realisasi, $target);
        return formatNumber($persen) . '%';
    }
}

if (!function_exists('getGradeBadgeClass')) {
    function getGradeBadgeClass($grade) {
        $classes = [
            'A' => 'success',
            'B' => 'primary',
            'C' => 'warning',
            'D' => 'danger',
            'E' => 'dark'
        ];
        return $classes[$grade] ?? 'secondary';
    }
}

if (!function_exists('getProgressColor')) {
    function getProgressColor($score) {
        if ($score >= 90) return 'success';
        if ($score >= 75) return 'primary';
        if ($score >= 60) return 'warning';
        return 'danger';
    }
}

if (!function_exists('formatDateIndonesia')) {
    function formatDateIndonesia($datetime) {
        if (empty($datetime)) return '-';
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $timestamp = strtotime($datetime);
        if (!$timestamp) return '-';
        $tgl = date('d', $timestamp);
        $bln = (int)date('m', $timestamp);
        $thn = date('Y', $timestamp);
        $jam = date('H:i', $timestamp);
        return "$tgl {$bulan[$bln]} $thn $jam";
    }
}
?>


<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-gradient">Detail Performansi Karyawan</h4>
            <p class="text-muted mb-0">
                <a href="<?= base_url('direktur/monitoring/performansi') ?>" class="text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Monitoring
                </a>
            </p>
        </div>
        <div>
            <button class="btn btn-modern-outline me-2" onclick="window.print()">
                <i class="fas fa-print me-2"></i>Cetak
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Left Column - Karyawan Info & Summary -->
        <div class="col-lg-4">
            <!-- Karyawan Info Card -->
            <div class="modern-card mb-4">
                <div class="text-center mb-3">
                    <div class="avatar-lg mx-auto mb-3">
                        <div class="bg-gradient-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 80px; height: 80px; margin: 0 auto; font-size: 2rem;">
                            <?= strtoupper(substr($performansi['nama_panggilan'] ?? $performansi['nama_lengkap'] ?? '?', 0, 1)) ?>
                        </div>
                    </div>
                    <h5 class="mb-1"><?= htmlspecialchars($performansi['nama_lengkap'] ?? '-') ?></h5>
                    <p class="text-muted mb-1"><?= htmlspecialchars($performansi['nik'] ?? '-') ?></p>
                    <p class="mb-0">
                        <span class="badge bg-secondary"><?= htmlspecialchars($performansi['jabatan'] ?? '-') ?></span>
                        <span class="badge bg-light text-dark"><?= htmlspecialchars($performansi['departemen'] ?? '-') ?></span>
                    </p>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-6">
                        <h6 class="text-muted mb-1">Periode</h6>
                        <p class="mb-0 fw-bold">
                            <?= ($monthNames[$performansi['periode_bulan']] ?? $performansi['periode_bulan']) . ' ' . $performansi['periode_tahun'] ?>
                        </p>
                    </div>
                    <div class="col-6">
                        <h6 class="text-muted mb-1">Status</h6>
                        <?php 
                        $statusClass = [
                            'draft' => 'secondary',
                            'review' => 'info',
                            'approved' => 'success',
                            'rejected' => 'danger',
                            'closed' => 'dark'
                        ];
                        $statusLabel = [
                            'draft' => 'Draft',
                            'review' => 'Review',
                            'approved' => 'Disetujui',
                            'rejected' => 'Ditolak',
                            'closed' => 'Tertutup'
                        ];
                        ?>
                        <span class="badge bg-<?= $statusClass[$performansi['status']] ?? 'secondary' ?>">
                            <?= $statusLabel[$performansi['status']] ?? $performansi['status'] ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Score Summary Card -->
            <div class="modern-card mb-4">
                <h6 class="mb-3">
                    <i class="fas fa-chart-simple me-2 text-primary"></i>
                    Ringkasan Skor
                </h6>
                <div class="text-center mb-4">
                    <div class="score-circle mx-auto mb-2" style="position: relative; width: 120px; height: 120px;">
                        <?php $skorTotal = floatval($performansi['skor_total'] ?? 0); ?>
                        <svg viewBox="0 0 36 36" style="width: 100%; height: 100%; transform: rotate(-90deg);">
                            <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" 
                                  fill="none" stroke="#e3e6f0" stroke-width="3"/>
                            <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" 
                                  fill="none" stroke="<?= getProgressColor($skorTotal) == 'success' ? '#1cc88a' : (getProgressColor($skorTotal) == 'primary' ? '#4e73df' : (getProgressColor($skorTotal) == 'warning' ? '#f6c23e' : '#e74a3b')) ?>" 
                                  stroke-width="3" 
                                  stroke-dasharray="<?= min($skorTotal, 100) ?> 100"/>
                        </svg>
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                            <span class="fs-2 fw-bold"><?= formatScore($skorTotal) ?></span>
                            <small class="text-muted d-block">Skor Total</small>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="badge bg-<?= getGradeBadgeClass($performansi['grade'] ?? 'E') ?> fs-5 px-3 py-2">
                            Grade <?= $performansi['grade'] ?? '-' ?>
                        </span>
                        <p class="mt-2 mb-0 fw-bold"><?= htmlspecialchars($performansi['predikat'] ?? '-') ?></p>
                    </div>
                </div>
                <hr>
                <div class="score-details">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Skor Kehadiran</span>
                            <span class="fw-bold"><?= formatScore($performansi['skor_kehadiran'] ?? 0) ?></span>
                        </div>
                        <div class="progress mt-1" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: <?= min($performansi['skor_kehadiran'] ?? 0, 100) ?>%"></div>
                        </div>
                        <small class="text-muted">Bobot 25%</small>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Skor Kualitas Kerja</span>
                            <span class="fw-bold"><?= formatScore($performansi['skor_kualitas_kerja'] ?? 0) ?></span>
                        </div>
                        <div class="progress mt-1" style="height: 6px;">
                            <div class="progress-bar bg-info" style="width: <?= min($performansi['skor_kualitas_kerja'] ?? 0, 100) ?>%"></div>
                        </div>
                        <small class="text-muted">Bobot 20%</small>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Skor Inisiatif</span>
                            <span class="fw-bold"><?= formatScore($performansi['skor_inisiatif'] ?? 0) ?></span>
                        </div>
                        <div class="progress mt-1" style="height: 6px;">
                            <div class="progress-bar bg-warning" style="width: <?= min($performansi['skor_inisiatif'] ?? 0, 100) ?>%"></div>
                        </div>
                        <small class="text-muted">Bobot 15%</small>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Skor Kedisiplinan</span>
                            <span class="fw-bold"><?= formatScore($performansi['skor_kedisiplinan'] ?? 0) ?></span>
                        </div>
                        <div class="progress mt-1" style="height: 6px;">
                            <div class="progress-bar bg-danger" style="width: <?= min($performansi['skor_kedisiplinan'] ?? 0, 100) ?>%"></div>
                        </div>
                        <small class="text-muted">Bobot 20%</small>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Skor Khusus</span>
                            <span class="fw-bold"><?= formatScore($performansi['skor_khusus'] ?? 0) ?></span>
                        </div>
                        <div class="progress mt-1" style="height: 6px;">
                            <div class="progress-bar bg-secondary" style="width: <?= min($performansi['skor_khusus'] ?? 0, 100) ?>%"></div>
                        </div>
                        <small class="text-muted">Bobot 20%</small>
                    </div>
                </div>
            </div>

            <!-- Trend Chart Card -->
            <?php if (!empty($trendData)): ?>
            <div class="modern-card">
                <h6 class="mb-3">
                    <i class="fas fa-chart-line me-2 text-primary"></i>
                    Tren Performansi
                </h6>
                <canvas id="trendChart" style="height: 200px;"></canvas>
            </div>
            <?php endif; ?>
        </div>

        <!-- Right Column - Detail Data -->
        <div class="col-lg-8">
            <!-- Target vs Realisasi Card -->
            <div class="modern-card mb-4">
                <h6 class="mb-3">
                    <i class="fas fa-bullseye me-2 text-primary"></i>
                    Target vs Realisasi
                </h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="35%">Komponen</th>
                                <th width="25%">Target</th>
                                <th width="25%">Realisasi</th>
                                <th width="15%">Pencapaian</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $targetKehadiran = floatval($performansi['target_kehadiran'] ?? 100);
                            $realisasiKehadiran = floatval($performansi['realisasi_kehadiran'] ?? 0);
                            ?>
                            <tr>
                                <td>Kehadiran</td>
                                <td><?= formatNumber($targetKehadiran) ?>%</td>
                                <td class="<?= $realisasiKehadiran >= $targetKehadiran ? 'text-success' : 'text-warning' ?>">
                                    <?= formatNumber($realisasiKehadiran) ?>%
                                </td>
                                <td><span class="fw-bold <?= $realisasiKehadiran >= $targetKehadiran ? 'text-success' : 'text-warning' ?>">
                                    <?= formatPersen($realisasiKehadiran, $targetKehadiran) ?>
                                </span></td>
                            </tr>

                            <?php
                            $targetPenyelesaian = floatval($performansi['target_penyelesaian_tugas'] ?? 100);
                            $realisasiPenyelesaian = floatval($performansi['realisasi_penyelesaian_tugas'] ?? 0);
                            ?>
                            <tr>
                                <td>Penyelesaian Tugas</td>
                                <td><?= formatNumber($targetPenyelesaian) ?>%</td>
                                <td class="<?= $realisasiPenyelesaian >= $targetPenyelesaian ? 'text-success' : 'text-warning' ?>">
                                    <?= formatNumber($realisasiPenyelesaian) ?>%
                                </td>
                                <td><span class="fw-bold <?= $realisasiPenyelesaian >= $targetPenyelesaian ? 'text-success' : 'text-warning' ?>">
                                    <?= formatPersen($realisasiPenyelesaian, $targetPenyelesaian) ?>
                                </span></td>
                            </tr>

                            <?php
                            $targetKesalahan = floatval($performansi['target_kesalahan_kerja'] ?? 0);
                            $realisasiKesalahan = floatval($performansi['realisasi_kesalahan_kerja'] ?? 0);
                            $targetKesalahanTampil = $targetKesalahan > 0 ? '≤ ' . formatNumber($targetKesalahan) . '%' : '0%';
                            ?>
                            <tr>
                                <td>Kesalahan Kerja</td>
                                <td><?= $targetKesalahanTampil ?></td>
                                <td class="<?= $realisasiKesalahan <= $targetKesalahan ? 'text-success' : 'text-danger' ?>">
                                    <?= formatNumber($realisasiKesalahan) ?>%
                                </td>
                                <td><span class="fw-bold <?= $realisasiKesalahan <= $targetKesalahan ? 'text-success' : 'text-danger' ?>">
                                    <?= $realisasiKesalahan <= $targetKesalahan ? '✓ Tercapai' : '✗ Tidak tercapai' ?>
                                </span></td>
                            </tr>

                            <?php
                            $targetKepuasan = floatval($performansi['target_kepuasan_client'] ?? 90);
                            $realisasiKepuasan = floatval($performansi['realisasi_kepuasan_client'] ?? 0);
                            ?>
                            <tr>
                                <td>Kepuasan Client</td>
                                <td><?= formatNumber($targetKepuasan) ?>%</td>
                                <td class="<?= $realisasiKepuasan >= $targetKepuasan ? 'text-success' : 'text-warning' ?>">
                                    <?= formatNumber($realisasiKepuasan) ?>%
                                </td>
                                <td><span class="fw-bold <?= $realisasiKepuasan >= $targetKepuasan ? 'text-success' : 'text-warning' ?>">
                                    <?= formatPersen($realisasiKepuasan, $targetKepuasan) ?>
                                </span></td>
                            </tr>

                            <?php
                            $targetProaktif = floatval($performansi['target_proaktif'] ?? 85);
                            $realisasiProaktif = floatval($performansi['realisasi_proaktif'] ?? 0);
                            ?>
                            <tr>
                                <td>Proaktif</td>
                                <td><?= formatNumber($targetProaktif) ?>%</td>
                                <td class="<?= $realisasiProaktif >= $targetProaktif ? 'text-success' : 'text-warning' ?>">
                                    <?= formatNumber($realisasiProaktif) ?>%
                                </td>
                                <td><span class="fw-bold <?= $realisasiProaktif >= $targetProaktif ? 'text-success' : 'text-warning' ?>">
                                    <?= formatPersen($realisasiProaktif, $targetProaktif) ?>
                                </span></td>
                            </tr>

                            <?php
                            $targetKerjasama = floatval($performansi['target_kerjasama_tim'] ?? 90);
                            $realisasiKerjasama = floatval($performansi['realisasi_kerjasama_tim'] ?? 0);
                            ?>
                            <tr>
                                <td>Kerjasama Tim</td>
                                <td><?= formatNumber($targetKerjasama) ?>%</td>
                                <td class="<?= $realisasiKerjasama >= $targetKerjasama ? 'text-success' : 'text-warning' ?>">
                                    <?= formatNumber($realisasiKerjasama) ?>%
                                </td>
                                <td><span class="fw-bold <?= $realisasiKerjasama >= $targetKerjasama ? 'text-success' : 'text-warning' ?>">
                                    <?= formatPersen($realisasiKerjasama, $targetKerjasama) ?>
                                </span></td>
                            </tr>

                            <?php
                            $targetTerlambat = floatval($performansi['target_terlambat'] ?? 0);
                            $realisasiTerlambat = floatval($performansi['realisasi_terlambat'] ?? 0);
                            $targetTerlambatTampil = $targetTerlambat > 0 ? '≤ ' . formatNumber($targetTerlambat) . 'x' : '0x';
                            ?>
                            <tr>
                                <td>Keterlambatan</td>
                                <td><?= $targetTerlambatTampil ?></td>
                                <td class="<?= $realisasiTerlambat <= $targetTerlambat ? 'text-success' : 'text-danger' ?>">
                                    <?= formatNumber($realisasiTerlambat) ?>x
                                </td>
                                <td><span class="fw-bold <?= $realisasiTerlambat <= $targetTerlambat ? 'text-success' : 'text-danger' ?>">
                                    <?= $realisasiTerlambat <= $targetTerlambat ? '✓ Tercapai' : '✗ Tidak tercapai' ?>
                                </span></td>
                            </tr>

                            <?php
                            $targetKetidakhadiran = floatval($performansi['target_ketidakhadiran'] ?? 0);
                            $realisasiKetidakhadiran = floatval($performansi['realisasi_ketidakhadiran'] ?? 0);
                            $targetKetidakhadiranTampil = $targetKetidakhadiran > 0 ? '≤ ' . formatNumber($targetKetidakhadiran) . ' hari' : '0 hari';
                            ?>
                            <tr>
                                <td>Ketidakhadiran</td>
                                <td><?= $targetKetidakhadiranTampil ?></td>
                                <td class="<?= $realisasiKetidakhadiran <= $targetKetidakhadiran ? 'text-success' : 'text-danger' ?>">
                                    <?= formatNumber($realisasiKetidakhadiran) ?> hari
                                </td>
                                <td><span class="fw-bold <?= $realisasiKetidakhadiran <= $targetKetidakhadiran ? 'text-success' : 'text-danger' ?>">
                                    <?= $realisasiKetidakhadiran <= $targetKetidakhadiran ? '✓ Tercapai' : '✗ Tidak tercapai' ?>
                                </span></td>
                            </tr>

                            <?php
                            $targetLembur = floatval($performansi['target_lembur'] ?? 0);
                            $realisasiLembur = floatval($performansi['realisasi_lembur'] ?? 0);
                            ?>
                            <tr>
                                <td>Jam Lembur</td>
                                <td><?= formatNumber($targetLembur) ?> jam</td>
                                <td class="<?= $realisasiLembur >= $targetLembur ? 'text-success' : 'text-warning' ?>">
                                    <?= formatNumber($realisasiLembur) ?> jam
                                </td>
                                <td><span class="fw-bold <?= $realisasiLembur >= $targetLembur ? 'text-success' : 'text-warning' ?>">
                                    <?= formatPersen($realisasiLembur, $targetLembur) ?>
                                </span></td>
                            </tr>

                            <?php
                            $targetProyek = floatval($performansi['target_proyek_selesai'] ?? 0);
                            $realisasiProyek = floatval($performansi['realisasi_proyek_selesai'] ?? 0);
                            ?>
                            <tr>
                                <td>Proyek Selesai</td>
                                <td><?= formatNumber($targetProyek) ?> proyek</td>
                                <td class="<?= $realisasiProyek >= $targetProyek ? 'text-success' : 'text-warning' ?>">
                                    <?= formatNumber($realisasiProyek) ?> proyek
                                </td>
                                <td><span class="fw-bold <?= $realisasiProyek >= $targetProyek ? 'text-success' : 'text-warning' ?>">
                                    <?= formatPersen($realisasiProyek, $targetProyek) ?>
                                </span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Catatan & Rekomendasi Card -->
            <div class="modern-card mb-4">
                <h6 class="mb-3">
                    <i class="fas fa-comment me-2 text-primary"></i>
                    Catatan & Evaluasi
                </h6>
                <div class="mb-3">
                    <label class="form-label fw-bold">Catatan Atasan / Direktur</label>
                    <div class="p-3 bg-light rounded">
                        <?= !empty($performansi['catatan_atasan']) ? nl2br(htmlspecialchars($performansi['catatan_atasan'])) : '<span class="text-muted">Belum ada catatan</span>' ?>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Catatan Karyawan</label>
                    <div class="p-3 bg-light rounded">
                        <?= !empty($performansi['catatan_karyawan']) ? nl2br(htmlspecialchars($performansi['catatan_karyawan'])) : '<span class="text-muted">Belum ada catatan dari karyawan</span>' ?>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Rekomendasi</label>
                    <div class="p-3 bg-light rounded">
                        <?= !empty($performansi['rekomendasi']) ? nl2br(htmlspecialchars($performansi['rekomendasi'])) : '<span class="text-muted">Belum ada rekomendasi</span>' ?>
                    </div>
                </div>
            </div>

            <!-- Audit Info Card -->
            <div class="modern-card">
                <h6 class="mb-3">
                    <i class="fas fa-history me-2 text-primary"></i>
                    Informasi Audit
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <td width="40%">Dibuat oleh</td>
                                <td><strong><?= htmlspecialchars($performansi['created_by_name'] ?? '-') ?></strong></td>
                            </tr>
                            <tr>
                                <td>Dibuat pada</td>
                                <td><?= formatDateIndonesia($performansi['created_at'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <td>Terakhir update</td>
                                <td><?= formatDateIndonesia($performansi['updated_at'] ?? '') ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <td width="40%">Dievaluasi oleh</td>
                                <td><strong><?= htmlspecialchars($performansi['evaluator_name'] ?? '-') ?></strong></td>
                            </tr>
                            <tr>
                                <td>Disetujui oleh</td>
                                <td><strong><?= htmlspecialchars($performansi['approver_name'] ?? '-') ?></strong></td>
                            </tr>
                            <tr>
                                <td>Disetujui pada</td>
                                <td><?= formatDateIndonesia($performansi['approved_at'] ?? '') ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Trend Chart
    const trendCanvas = document.getElementById('trendChart');
    <?php if (!empty($trendData)): ?>
    if (trendCanvas) {
        const trendLabels = [
            <?php foreach ($trendData as $trend): ?>
            '<?= ($monthNames[$trend['periode_bulan']] ?? $trend['periode_bulan']) . ' ' . $trend['periode_tahun'] ?>',
            <?php endforeach; ?>
        ];
        const trendScores = [
            <?php foreach ($trendData as $trend): ?>
            <?= floatval($trend['skor_total'] ?? 0) ?>,
            <?php endforeach; ?>
        ];
        
        new Chart(trendCanvas, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [{
                    label: 'Skor Performansi',
                    data: trendScores,
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#4e73df',
                    pointBorderColor: '#fff',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Skor Total'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Skor: ' + context.raw.toFixed(1);
                            }
                        }
                    }
                }
            }
        });
    }
    <?php endif; ?>
});
</script>

<style>
.avatar-lg {
    width: 80px;
    height: 80px;
}
.bg-gradient-primary {
    background: linear-gradient(135deg, #4e73df, #224abe);
}
.modern-card {
    transition: transform 0.2s, box-shadow 0.2s;
    background: white;
    border-radius: 12px;
    padding: 1.25rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.modern-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}
.text-gradient {
    background: linear-gradient(135deg, #4e73df, #1cc88a);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.btn-modern-outline {
    border: 1px solid #4e73df;
    background: transparent;
    color: #4e73df;
    padding: 8px 16px;
    border-radius: 8px;
    transition: all 0.2s;
}
.btn-modern-outline:hover {
    background: #4e73df;
    color: white;
}
.progress {
    background-color: #e3e6f0;
    border-radius: 4px;
}
.progress-bar {
    border-radius: 4px;
}
.table-bordered td, .table-bordered th {
    border-color: #e3e6f0;
}
.table-sm td {
    padding: 0.5rem;
}
@media print {
    .btn-modern-outline, .btn-modern-primary, .sidebar, .navbar, .btn {
        display: none !important;
    }
    .modern-card {
        box-shadow: none;
        border: 1px solid #ddd;
    }
}
</style>

