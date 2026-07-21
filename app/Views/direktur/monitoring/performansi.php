<?php
// Data dari controller
$performansiData = $performansiData ?? [];
$karyawanList = $karyawanList ?? [];
$tahun = $tahun ?? date('Y');
$bulan = $bulan ?? '';
$gradeFilter = $gradeFilter ?? '';
$karyawanIdFilter = $karyawanIdFilter ?? '';
$searchQuery = $searchQuery ?? '';
$currentPage = $currentPage ?? 1;
$perPage = $perPage ?? 20;
$totalData = $totalData ?? 0;
$totalPages = $totalPages ?? 1;
$baseUrl = $baseUrl ?? base_url('direktur/monitoring/performansi');
$queryParams = $queryParams ?? [];
$stats = $stats ?? [];
$availableYears = $availableYears ?? [date('Y')];
$availableMonths = $availableMonths ?? [];
$gradeOptions = $gradeOptions ?? [];
$monthNames = $monthNames ?? [];

// Helper functions
if (!function_exists('formatScore')) {
    function formatScore($score) {
        if (empty($score) && $score !== 0) return '-';
        return number_format((float)$score, 1);
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
?>

<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-gradient">Monitoring Performansi Karyawan</h4>
            <p class="text-muted mb-0">Pantau performansi dan KPI karyawan berdasarkan periode</p>
        </div>
        <div>
            <button class="btn btn-modern-outline me-2" onclick="window.location.href='<?= base_url('direktur/monitoring/performansi/print?' . http_build_query(array_filter($queryParams))) ?>'">
                <i class="fas fa-print me-2"></i>Cetak
            </button>
            <button class="btn btn-modern-primary" onclick="window.location.href='<?= base_url('direktur/monitoring/performansi/exportExcel?' . http_build_query(array_filter($queryParams))) ?>'">
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
        <form method="GET" action="<?= base_url('direktur/monitoring/performansi') ?>" class="row g-3">
            <div class="col-md-2">
                <label class="form-label">Tahun</label>
                <select name="tahun" class="form-select" id="filterTahun">
                    <?php foreach ($availableYears as $year): ?>
                    <option value="<?= $year ?>" <?= $tahun == $year ? 'selected' : '' ?>><?= $year ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Bulan</label>
                <select name="bulan" class="form-select" id="filterBulan">
                    <option value="">Semua Bulan</option>
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m ?>" <?= $bulan == $m ? 'selected' : '' ?> <?= in_array($m, $availableMonths) ? '' : 'disabled' ?>>
                        <?= $monthNames[$m] ?>
                    </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Grade</label>
                <select name="grade" class="form-select">
                    <option value="">Semua Grade</option>
                    <?php foreach ($gradeOptions as $key => $label): ?>
                    <option value="<?= $key ?>" <?= $gradeFilter == $key ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Karyawan</label>
                <select name="karyawan_id" class="form-select">
                    <option value="">Semua Karyawan</option>
                    <?php foreach ($karyawanList as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= $karyawanIdFilter == $k['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($k['nama_panggilan'] ?? $k['nama_lengkap']) ?> - <?= htmlspecialchars($k['jabatan'] ?? '-') ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Cari</label>
                <input type="text" name="search" class="form-control" placeholder="Nama / NIK..." value="<?= htmlspecialchars($searchQuery) ?>">
            </div>
            <div class="col-md-12 d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>Filter
                </button>
                <a href="<?= base_url('direktur/monitoring/performansi') ?>" class="btn btn-secondary">
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
                    <i class="fas fa-users fa-2x text-primary"></i>
                </div>
                <h2 class="mb-0"><?= number_format($stats['total_karyawan_terdata'] ?? 0) ?></h2>
                <p class="text-muted mb-0">Karyawan Terdata</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="modern-card modern-card-accent text-center">
                <div class="mb-2">
                    <i class="fas fa-chart-line fa-2x text-success"></i>
                </div>
                <h2 class="mb-0"><?= formatScore($stats['rata_rata_skor'] ?? 0) ?></h2>
                <p class="text-muted mb-0">Rata-rata Skor</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="modern-card modern-card-warning text-center">
                <div class="mb-2">
                    <i class="fas fa-trophy fa-2x text-warning"></i>
                </div>
                <h2 class="mb-0"><?= formatScore($stats['skor_tertinggi'] ?? 0) ?></h2>
                <p class="text-muted mb-0">Skor Tertinggi</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="modern-card modern-card-info text-center">
                <div class="mb-2">
                    <i class="fas fa-star fa-2x text-info"></i>
                </div>
                <h2 class="mb-0"><?= number_format($stats['total_grade_a'] ?? 0) ?></h2>
                <p class="text-muted mb-0">Grade A (Sangat Baik)</p>
            </div>
        </div>
    </div>

    <!-- Grade Distribution Chart -->
    <div class="modern-card mb-4">
        <h5 class="mb-3">
            <i class="fas fa-chart-pie me-2 text-primary"></i>
            Distribusi Grade Performansi
        </h5>
        <div class="row">
            <div class="col-md-8">
                <canvas id="gradeChart" style="max-height: 300px;"></canvas>
            </div>
            <div class="col-md-4">
                <div class="grade-stats">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><span class="badge bg-success">A</span> Sangat Baik (90+)</span>
                            <span class="fw-bold"><?= number_format($stats['total_grade_a'] ?? 0) ?> org</span>
                        </div>
                        <div class="progress mt-1" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: <?= ($stats['total_karyawan_terdata'] ?? 1) > 0 ? (($stats['total_grade_a'] ?? 0) / ($stats['total_karyawan_terdata'] ?? 1)) * 100 : 0 ?>%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><span class="badge bg-primary">B</span> Baik (75-89)</span>
                            <span class="fw-bold"><?= number_format($stats['total_grade_b'] ?? 0) ?> org</span>
                        </div>
                        <div class="progress mt-1" style="height: 8px;">
                            <div class="progress-bar bg-primary" style="width: <?= ($stats['total_karyawan_terdata'] ?? 1) > 0 ? (($stats['total_grade_b'] ?? 0) / ($stats['total_karyawan_terdata'] ?? 1)) * 100 : 0 ?>%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><span class="badge bg-warning">C</span> Cukup (60-74)</span>
                            <span class="fw-bold"><?= number_format($stats['total_grade_c'] ?? 0) ?> org</span>
                        </div>
                        <div class="progress mt-1" style="height: 8px;">
                            <div class="progress-bar bg-warning" style="width: <?= ($stats['total_karyawan_terdata'] ?? 1) > 0 ? (($stats['total_grade_c'] ?? 0) / ($stats['total_karyawan_terdata'] ?? 1)) * 100 : 0 ?>%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><span class="badge bg-danger">D</span> Kurang (50-59)</span>
                            <span class="fw-bold"><?= number_format($stats['total_grade_d'] ?? 0) ?> org</span>
                        </div>
                        <div class="progress mt-1" style="height: 8px;">
                            <div class="progress-bar bg-danger" style="width: <?= ($stats['total_karyawan_terdata'] ?? 1) > 0 ? (($stats['total_grade_d'] ?? 0) / ($stats['total_karyawan_terdata'] ?? 1)) * 100 : 0 ?>%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><span class="badge bg-dark">E</span> Buruk ({grade}Cu;kup)</span>
                            <span class="fw-bold"><?= number_format($stats['total_grade_e'] ?? 0) ?> org</span>
                        </div>
                        <div class="progress mt-1" style="height: 8px;">
                            <div class="progress-bar bg-dark" style="width: <?= ($stats['total_karyawan_terdata'] ?? 1) > 0 ? (($stats['total_grade_e'] ?? 0) / ($stats['total_karyawan_terdata'] ?? 1)) * 100 : 0 ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data Performansi -->
    <div class="modern-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">
                <i class="fas fa-table me-2 text-primary"></i>
                Data Performansi Karyawan
            </h5>
            <div>
                <span class="text-muted">Total: <?= number_format($totalData) ?> data</span>
                <span class="text-muted ms-3">Halaman <?= $currentPage ?> dari <?= $totalPages ?></span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Periode</th>
                        <th>Nama Karyawan</th>
                        <th>Jabatan</th>
                        <th>Skor Total</th>
                        <th>Grade</th>
                        <th>Predikat</th>
                        <th>Kehadiran</th>
                        <th>Kualitas</th>
                        <th>Kedisiplinan</th>
                        <th>Status</th>
                        <th width="80">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($performansiData)): ?>
                    <tr>
                        <td colspan="12" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                            Tidak ada data performansi untuk periode yang dipilih
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php 
                        $no = (($currentPage - 1) * $perPage) + 1; 
                        foreach ($performansiData as $item): 
                            $progressColor = getProgressColor($item['skor_total'] ?? 0);
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <span class="fw-bold"><?= $monthNames[$item['periode_bulan']] ?? $item['periode_bulan'] ?></span>
                                <br>
                                <small class="text-muted"><?= $item['periode_tahun'] ?></small>
                            </td>
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
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold fs-5 me-2"><?= formatScore($item['skor_total'] ?? 0) ?></span>
                                    <div class="progress flex-grow-1" style="height: 6px; width: 80px;">
                                        <div class="progress-bar bg-<?= $progressColor ?>" style="width: <?= ($item['skor_total'] ?? 0) ?>%"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-<?= getGradeBadgeClass($item['grade'] ?? 'E') ?> fs-6 px-3 py-2">
                                    <?= $item['grade'] ?? '-' ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($item['predikat'] ?? '-') ?></td>
                            <td>
                                <div class="text-center">
                                    <div class="fw-bold"><?= formatScore($item['skor_kehadiran'] ?? 0) ?></div>
                                    <div class="progress mt-1" style="height: 4px; width: 60px; margin: 0 auto;">
                                        <div class="progress-bar bg-success" style="width: <?= ($item['skor_kehadiran'] ?? 0) ?>%"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-center">
                                    <div class="fw-bold"><?= formatScore($item['skor_kualitas_kerja'] ?? 0) ?></div>
                                    <div class="progress mt-1" style="height: 4px; width: 60px; margin: 0 auto;">
                                        <div class="progress-bar bg-info" style="width: <?= ($item['skor_kualitas_kerja'] ?? 0) ?>%"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-center">
                                    <div class="fw-bold"><?= formatScore($item['skor_kedisiplinan'] ?? 0) ?></div>
                                    <div class="progress mt-1" style="height: 4px; width: 60px; margin: 0 auto;">
                                        <div class="progress-bar bg-warning" style="width: <?= ($item['skor_kedisiplinan'] ?? 0) ?>%"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
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
                                <span class="badge bg-<?= $statusClass[$item['status']] ?? 'secondary' ?>">
                                    <?= $statusLabel[$item['status']] ?? $item['status'] ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= base_url('direktur/monitoring/performansi/detail/' . $item['id']) ?>" 
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

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Grade distribution chart
    const ctx = document.getElementById('gradeChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['A (Sangat Baik)', 'B (Baik)', 'C (Cukup)', 'D (Kurang)', 'E (Buruk)'],
                datasets: [{
                    data: [
                        <?= $stats['total_grade_a'] ?? 0 ?>,
                        <?= $stats['total_grade_b'] ?? 0 ?>,
                        <?= $stats['total_grade_c'] ?? 0 ?>,
                        <?= $stats['total_grade_d'] ?? 0 ?>,
                        <?= $stats['total_grade_e'] ?? 0 ?>
                    ],
                    backgroundColor: ['#1cc88a', '#4e73df', '#f6c23e', '#e74a3b', '#5a5c69'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Auto submit when tahun or bulan changes
    const tahunSelect = document.getElementById('filterTahun');
    const bulanSelect = document.getElementById('filterBulan');
    
    if (tahunSelect) {
        tahunSelect.addEventListener('change', function() {
            this.form.submit();
        });
    }
    
    if (bulanSelect) {
        bulanSelect.addEventListener('change', function() {
            this.form.submit();
        });
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
    background: white;
    border-radius: 12px;
    padding: 1.25rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
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
.modern-card-info {
    border-left: 4px solid #36b9cc;
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
.grade-stats .progress {
    background-color: #e3e6f0;
}
.badge.fs-6 {
    font-size: 0.9rem;
}
.table td {
    vertical-align: middle;
}
</style>

<?= view('direktur/templates/footer', ['scripts' => $scripts ?? []]) ?>