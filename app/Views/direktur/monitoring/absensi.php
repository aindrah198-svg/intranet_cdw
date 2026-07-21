<?php
// Data dari controller - langsung akses variabelnya, bukan melalui $data
$absensiData = $absensiData ?? [];
$karyawanList = $karyawanList ?? [];

// Konversi DateTime ke string jika diperlukan
$startDateRaw = $startDate ?? date('Y-m-01');
$endDateRaw = $endDate ?? date('Y-m-d');

// Konversi jika DateTime object
if ($startDateRaw instanceof DateTime) {
    $startDate = $startDateRaw->format('Y-m-d');
} else {
    $startDate = (string) $startDateRaw;
}

if ($endDateRaw instanceof DateTime) {
    $endDate = $endDateRaw->format('Y-m-d');
} else {
    $endDate = (string) $endDateRaw;
}

$statusFilter = $statusFilter ?? '';
$karyawanIdFilter = $karyawanIdFilter ?? '';
$searchQuery = $searchQuery ?? '';
$currentPage = $currentPage ?? 1;
$perPage = $perPage ?? 20;
$totalAbsensi = $totalAbsensi ?? 0;
$totalKaryawan = $totalKaryawan ?? 0;
$totalHadir = $totalHadir ?? 0;
$totalTerlambat = $totalTerlambat ?? 0;
$totalIzin = $totalIzin ?? 0;
$totalSakit = $totalSakit ?? 0;
$totalAlpha = $totalAlpha ?? 0;
$totalPages = $totalPages ?? 1;
$baseUrl = $baseUrl ?? base_url('direktur/monitoring/absensi');
$queryParams = $queryParams ?? [];

// Helper function untuk format tanggal
if (!function_exists('formatDate')) {
    function formatDate($date) {
        if (empty($date)) return '-';
        if ($date instanceof DateTime) {
            return $date->format('d/m/Y');
        }
        if (is_string($date)) {
            $timestamp = strtotime($date);
            return $timestamp ? date('d/m/Y', $timestamp) : '-';
        }
        return '-';
    }
}

// Helper function untuk format waktu
if (!function_exists('formatTime')) {
    function formatTime($time) {
        if (empty($time)) return '-';
        if ($time instanceof DateTime) {
            return $time->format('H:i');
        }
        if (is_string($time)) {
            $timestamp = strtotime($time);
            return $timestamp ? date('H:i', $timestamp) : '-';
        }
        return '-';
    }
}

// Helper function untuk format string aman
if (!function_exists('safeStr')) {
    function safeStr($value) {
        if ($value instanceof DateTime) {
            return $value->format('Y-m-d');
        }
        return (string) $value;
    }
}
?>


<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-gradient">Monitoring Absensi Karyawan</h4>
            <p class="text-muted mb-0">Pantau kehadiran dan keterlambatan karyawan</p>
        </div>
        <div>
            <button class="btn btn-modern-outline me-2" onclick="window.location.href='<?= base_url('direktur/monitoring/absensi/print?' . http_build_query(array_filter($queryParams))) ?>'">
                <i class="fas fa-print me-2"></i>Cetak
            </button>
            <button class="btn btn-modern-primary" onclick="window.location.href='<?= base_url('direktur/monitoring/absensi/exportExcel?' . http_build_query(array_filter($queryParams))) ?>'">
                <i class="fas fa-file-excel me-2"></i>Export Excel
            </button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="modern-card mb-4">
        <h5 class="mb-3">
            <i class="fas fa-filter me-2 text-primary"></i>
            Filter Data
        </h5>
        <form method="GET" action="<?= base_url('direktur/monitoring/absensi') ?>" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars(safeStr($startDate)) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Selesai</label>
                <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars(safeStr($endDate)) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="Hadir" <?= $statusFilter == 'Hadir' ? 'selected' : '' ?>>Hadir</option>
                    <option value="Terlambat" <?= $statusFilter == 'Terlambat' ? 'selected' : '' ?>>Terlambat</option>
                    <option value="Izin" <?= $statusFilter == 'Izin' ? 'selected' : '' ?>>Izin</option>
                    <option value="Sakit" <?= $statusFilter == 'Sakit' ? 'selected' : '' ?>>Sakit</option>
                    <option value="Alpha" <?= $statusFilter == 'Alpha' ? 'selected' : '' ?>>Alpha</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Karyawan</label>
                <select name="karyawan_id" class="form-select">
                    <option value="">Semua Karyawan</option>
                    <?php foreach ($karyawanList as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= $karyawanIdFilter == $k['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($k['nama_panggilan'] ?? $k['nama_lengkap']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Cari</label>
                <input type="text" name="search" class="form-control" placeholder="Nama / NIK..." value="<?= htmlspecialchars($searchQuery) ?>">
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-2"></i>Filter
                </button>
                <a href="<?= base_url('direktur/monitoring/absensi') ?>" class="btn btn-secondary w-100">
                    <i class="fas fa-undo me-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="modern-card modern-card-primary text-center">
                <div class="mb-2">
                    <i class="fas fa-calendar-check fa-2x text-primary"></i>
                </div>
                <h2 class="mb-0"><?= number_format($totalAbsensi) ?></h2>
                <p class="text-muted mb-0">Total Absensi</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="modern-card modern-card-accent text-center">
                <div class="mb-2">
                    <i class="fas fa-check-circle fa-2x text-success"></i>
                </div>
                <h2 class="mb-0"><?= number_format($totalHadir) ?></h2>
                <p class="text-muted mb-0">Hadir</p>
                <small class="text-muted">(<?= $totalAbsensi > 0 ? round(($totalHadir / $totalAbsensi) * 100, 1) : 0 ?>%)</small>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="modern-card modern-card-warning text-center">
                <div class="mb-2">
                    <i class="fas fa-clock fa-2x text-warning"></i>
                </div>
                <h2 class="mb-0"><?= number_format($totalTerlambat) ?></h2>
                <p class="text-muted mb-0">Terlambat</p>
                <small class="text-muted">(<?= $totalAbsensi > 0 ? round(($totalTerlambat / $totalAbsensi) * 100, 1) : 0 ?>%)</small>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="modern-card modern-card-danger text-center">
                <div class="mb-2">
                    <i class="fas fa-user-slash fa-2x text-danger"></i>
                </div>
                <h2 class="mb-0"><?= number_format($totalAlpha) ?></h2>
                <p class="text-muted mb-0">Tidak Hadir</p>
            </div>
        </div>
    </div>

    <!-- Tabel Data Absensi -->
    <div class="modern-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">
                <i class="fas fa-table me-2 text-primary"></i>
                Data Absensi
            </h5>
            <div>
                <span class="text-muted">Total: <?= number_format($totalAbsensi) ?> data</span>
                <span class="text-muted ms-3">Halaman <?= $currentPage ?> dari <?= $totalPages ?></span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Tanggal</th>
                        <th>Nama Karyawan</th>
                        <th>Jabatan</th>
                        <th>Shift</th>
                        <th>Jam Masuk</th>
                        <th>Jam Keluar</th>
                        <th>Jam Kerja</th>
                        <th>Terlambat</th>
                        <th>Status</th>
                        <th width="80">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($absensiData)): ?>
                    <tr>
                        <td colspan="11" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                            Tidak ada data absensi
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php 
                        $no = (($currentPage - 1) * $perPage) + 1; 
                        foreach ($absensiData as $item): 
                            $persenJamKerja = 0;
                            $targetJamKerja = 8;
                            if (!empty($item['jam_kerja']) && $item['jam_kerja'] > 0) {
                                $persenJamKerja = round(($item['jam_kerja'] / $targetJamKerja) * 100);
                            }
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= formatDate($item['tanggal'] ?? '') ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-2">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 32px; height: 32px;">
                                            <?= strtoupper(substr($item['nama_panggilan'] ?? $item['nama_lengkap'] ?? '?', 0, 1)) ?>
                                        </div>
                                    </div>
                                    <div>
                                        <strong><?= htmlspecialchars($item['nama_panggilan'] ?? $item['nama_lengkap']) ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($item['nik'] ?? '-') ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($item['jabatan'] ?? '-') ?></td>
                            <td>
                                <?php 
                                $shiftClass = [
                                    'pagi' => 'success',
                                    'siang' => 'info',
                                    'sore' => 'warning',
                                    'malam' => 'dark'
                                ];
                                $shiftName = ucfirst($item['shift'] ?? 'siang');
                                ?>
                                <span class="badge bg-<?= $shiftClass[$item['shift'] ?? 'siang'] ?? 'secondary' ?>">
                                    <?= $shiftName ?>
                                </span>
                            </td>
                            <td><?= formatTime($item['waktu_masuk'] ?? '') ?></td>
                            <td><?= formatTime($item['waktu_pulang'] ?? '') ?></td>
                            <td>
                                <?php if (!empty($item['jam_kerja']) && $item['jam_kerja'] > 0): ?>
                                    <?= number_format($item['jam_kerja'], 1) ?> jam
                                    <div class="progress mt-1" style="height: 4px;">
                                        <div class="progress-bar bg-success" style="width: <?= min($persenJamKerja, 100) ?>%"></div>
                                    </div>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($item['terlambat']) && $item['terlambat'] > 0): ?>
                                    <span class="text-warning">
                                        <i class="fas fa-clock"></i> <?= $item['terlambat'] ?> menit
                                    </span>
                                <?php else: ?>
                                    <span class="text-success">Tepat waktu</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                $badgeClass = [
                                    'Hadir' => 'success',
                                    'Terlambat' => 'warning',
                                    'Izin' => 'info',
                                    'Sakit' => 'primary',
                                    'Alpha' => 'danger'
                                ];
                                ?>
                                <span class="badge bg-<?= $badgeClass[$item['status']] ?? 'secondary' ?>">
                                    <?= $item['status'] ?? '-' ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= base_url('direktur/monitoring/absensi/detail/' . $item['id']) ?>" 
                                   class="btn btn-sm btn-outline-primary" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <nav class="mt-3">
            <ul class="pagination justify-content-center">
                <?php if ($currentPage > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $baseUrl ?>&page=<?= $currentPage - 1 ?>">
                        <i class="fas fa-chevron-left"></i> Previous
                    </a>
                </li>
                <?php endif; ?>
                
                <?php 
                $startPage = max(1, $currentPage - 2);
                $endPage = min($totalPages, $currentPage + 2);
                for ($i = $startPage; $i <= $endPage; $i++): 
                ?>
                <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                    <a class="page-link" href="<?= $baseUrl ?>&page=<?= $i ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
                
                <?php if ($currentPage < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $baseUrl ?>&page=<?= $currentPage + 1 ?>">
                        Next <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.querySelector('select[name="status"]');
    if (statusSelect && statusSelect.value) {
        const statusText = statusSelect.options[statusSelect.selectedIndex]?.text || '';
        if (statusText === 'Hadir') {
            statusSelect.classList.add('border-success');
        } else if (statusText === 'Terlambat') {
            statusSelect.classList.add('border-warning');
        }
    }
});
</script>

<style>
.avatar-sm {
    width: 32px;
    min-width: 32px;
}
.modern-card {
    transition: transform 0.2s, box-shadow 0.2s;
}
.modern-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}
.modern-card-primary {
    border-left: 4px solid #4e73df;
}
.modern-card-accent {
    border-left: 4px solid #1cc88a;
}
.modern-card-warning {
    border-left: 4px solid #f6c23e;
}
.modern-card-danger {
    border-left: 4px solid #e74a3b;
}
.text-gradient {
    background: linear-gradient(135deg, #4e73df, #1cc88a);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.table th {
    background: #f8f9fc;
    font-weight: 600;
    border-bottom: 2px solid #e3e6f0;
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
.btn-modern-primary {
    background: linear-gradient(135deg, #4e73df, #1cc88a);
    border: none;
    color: white;
    padding: 8px 16px;
    border-radius: 8px;
    transition: all 0.2s;
}
.btn-modern-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(78, 115, 223, 0.3);
}
.progress {
    background-color: #e3e6f0;
    border-radius: 4px;
}
.progress-bar {
    border-radius: 4px;
}
</style>

<?= view('direktur/templates/footer', ['scripts' => $scripts ?? []]) ?>