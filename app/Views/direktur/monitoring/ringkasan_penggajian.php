<?php
// Data dari controller
$penggajianData = $penggajianData ?? [];
$karyawanList = $karyawanList ?? [];
$tahun = $tahun ?? date('Y');
$bulan = $bulan ?? '';
$statusFilter = $statusFilter ?? '';
$karyawanIdFilter = $karyawanIdFilter ?? '';
$searchQuery = $searchQuery ?? '';
$currentPage = $currentPage ?? 1;
$perPage = $perPage ?? 20;
$totalData = $totalData ?? 0;
$totalPages = $totalPages ?? 1;
$baseUrl = $baseUrl ?? base_url('direktur/monitoring/ringkasan-penggajian');
$queryParams = $queryParams ?? [];
$stats = $stats ?? [];
$summaryByDepartment = $summaryByDepartment ?? [];
$topEarners = $topEarners ?? [];
$availableYears = $availableYears ?? [date('Y')];
$availableMonths = $availableMonths ?? [];
$statusOptions = $statusOptions ?? [];
$paymentMethods = $paymentMethods ?? [];
$monthNames = $monthNames ?? [];

// Helper functions
if (!function_exists('formatRupiah')) {
    function formatRupiah($amount) {
        if (empty($amount) && $amount !== 0) return '-';
        return 'Rp ' . number_format((float)$amount, 0, ',', '.');
    }
}

if (!function_exists('formatNumber')) {
    function formatNumber($num) {
        if (($num === null || $num === '') && $num !== 0) return '-';
        return number_format((float)$num, 0, ',', '.');
    }
}

if (!function_exists('getStatusBadgeClass')) {
    function getStatusBadgeClass($status) {
        $classes = [
            'draft' => 'secondary',
            'proses' => 'info',
            'approved' => 'success',
            'paid' => 'primary',
            'rejected' => 'danger'
        ];
        return $classes[$status] ?? 'secondary';
    }
}

if (!function_exists('getStatusLabel')) {
    function getStatusLabel($status) {
        $labels = [
            'draft' => 'Draft',
            'proses' => 'Diproses',
            'approved' => 'Disetujui',
            'paid' => 'Dibayar',
            'rejected' => 'Ditolak'
        ];
        return $labels[$status] ?? $status;
    }
}
?>


<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-gradient">Ringkasan Penggajian Karyawan</h4>
            <p class="text-muted mb-0">Monitoring gaji dan kompensasi karyawan per periode</p>
        </div>
        <div>
            <button class="btn btn-modern-outline me-2" onclick="window.location.href='<?= base_url('direktur/monitoring/ringkasan-penggajian/print?' . http_build_query(array_filter($queryParams))) ?>'">
                <i class="fas fa-print me-2"></i>Cetak
            </button>
            <button class="btn btn-modern-primary" onclick="window.location.href='<?= base_url('direktur/monitoring/ringkasan-penggajian/exportExcel?' . http_build_query(array_filter($queryParams))) ?>'">
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
        <form method="GET" action="<?= base_url('direktur/monitoring/ringkasan-penggajian') ?>" class="row g-3">
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
                    <option value="<?= $m ?>" <?= $bulan == $m ? 'selected' : '' ?>>
                        <?= $monthNames[$m] ?>
                    </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <?php foreach ($statusOptions as $key => $label): ?>
                    <option value="<?= $key ?>" <?= $statusFilter == $key ? 'selected' : '' ?>><?= $label ?></option>
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
                <a href="<?= base_url('direktur/monitoring/ringkasan-penggajian') ?>" class="btn btn-secondary">
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
                <h2 class="mb-0"><?= number_format($stats['total_karyawan'] ?? 0) ?></h2>
                <p class="text-muted mb-0">Karyawan</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="modern-card modern-card-accent text-center">
                <div class="mb-2">
                    <i class="fas fa-money-bill-wave fa-2x text-success"></i>
                </div>
                <h5 class="mb-0"><?= formatRupiah($stats['total_gaji_bersih'] ?? 0) ?></h5>
                <p class="text-muted mb-0">Total Gaji Bersih</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="modern-card modern-card-warning text-center">
                <div class="mb-2">
                    <i class="fas fa-chart-line fa-2x text-warning"></i>
                </div>
                <h5 class="mb-0"><?= formatRupiah($stats['rata_rata_gaji'] ?? 0) ?></h5>
                <p class="text-muted mb-0">Rata-rata Gaji</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="modern-card modern-card-info text-center">
                <div class="mb-2">
                    <i class="fas fa-clock fa-2x text-info"></i>
                </div>
                <h2 class="mb-0"><?= formatNumber($stats['total_lembur'] ?? 0) ?></h2>
                <p class="text-muted mb-0">Total Jam Lembur</p>
            </div>
        </div>
    </div>

    <!-- Summary by Department & Top Earners -->
    <div class="row g-3 mb-4">
        <!-- Summary by Department Chart -->
        <div class="col-md-6">
            <div class="modern-card">
                <h5 class="mb-3">
                    <i class="fas fa-building me-2 text-primary"></i>
                    Ringkasan per Departemen
                </h5>
                <canvas id="departmentChart" style="height: 250px;"></canvas>
                <div class="mt-3">
                    <?php foreach ($summaryByDepartment as $dept): ?>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><strong><?= htmlspecialchars($dept['departemen'] ?? 'Lainnya') ?></strong></span>
                            <span><?= formatRupiah($dept['total_gaji_bersih'] ?? 0) ?></span>
                        </div>
                        <div class="progress mt-1" style="height: 6px;">
                            <?php 
                            $maxGaji = max(array_column($summaryByDepartment, 'total_gaji_bersih')) ?: 1;
                            $percentage = (($dept['total_gaji_bersih'] ?? 0) / $maxGaji) * 100;
                            ?>
                            <div class="progress-bar bg-primary" style="width: <?= $percentage ?>%"></div>
                        </div>
                        <small class="text-muted"><?= $dept['jumlah_karyawan'] ?? 0 ?> karyawan</small>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($summaryByDepartment)): ?>
                    <p class="text-muted text-center">Belum ada data untuk periode ini</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Top Earners -->
        <div class="col-md-6">
            <div class="modern-card">
                <h5 class="mb-3">
                    <i class="fas fa-trophy me-2 text-warning"></i>
                    Top 5 Gaji Tertinggi
                </h5>
                <?php if (!empty($topEarners)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Karyawan</th>
                                <th>Jabatan</th>
                                <th>Gaji Bersih</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $rank = 1; ?>
                            <?php foreach ($topEarners as $earner): ?>
                            <tr>
                                <td>
                                    <?php if ($rank == 1): ?>
                                    <i class="fas fa-medal text-warning"></i>
                                    <?php elseif ($rank == 2): ?>
                                    <i class="fas fa-medal text-secondary"></i>
                                    <?php elseif ($rank == 3): ?>
                                    <i class="fas fa-medal text-bronze" style="color: #cd7f32;"></i>
                                    <?php else: ?>
                                    <?= $rank ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($earner['nama_panggilan'] ?? $earner['nama_lengkap']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($earner['nik'] ?? '-') ?></small>
                                </td>
                                <td><?= htmlspecialchars($earner['jabatan'] ?? '-') ?></td>
                                <td class="text-success fw-bold"><?= formatRupiah($earner['gaji_bersih'] ?? 0) ?></td>
                            </tr>
                            <?php $rank++; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted text-center">Belum ada data untuk periode ini</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Tabel Data Penggajian -->
    <div class="modern-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">
                <i class="fas fa-table me-2 text-primary"></i>
                Data Penggajian
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
                        <th>Gaji Pokok</th>
                        <th>Tunjangan</th>
                        <th>Lembur</th>
                        <th>Total Penghasilan</th>
                        <th>Total Potongan</th>
                        <th>Gaji Bersih</th>
                        <th>Status</th>
                        <th width="80">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($penggajianData)): ?>
                    <tr>
                        <td colspan="12" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                            Tidak ada data penggajian untuk periode yang dipilih
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php 
                        $no = (($currentPage - 1) * $perPage) + 1; 
                        foreach ($penggajianData as $item): 
                            $totalTunjangan = ($item['tunjangan_jabatan'] ?? 0) + ($item['tunjangan_makan'] ?? 0) + 
                                              ($item['tunjangan_transport'] ?? 0) + ($item['tunjangan_kesehatan'] ?? 0) +
                                              ($item['tunjangan_hari_raya'] ?? 0) + ($item['tunjangan_lainnya'] ?? 0);
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
                            <td class="text-end"><?= formatRupiah($item['gaji_pokok'] ?? 0) ?></td>
                            <td class="text-end"><?= formatRupiah($totalTunjangan) ?></td>
                            <td class="text-end"><?= formatRupiah($item['lembur'] ?? 0) ?></td>
                            <td class="text-end text-primary fw-bold"><?= formatRupiah($item['total_penghasilan'] ?? 0) ?></td>
                            <td class="text-end text-danger"><?= formatRupiah($item['total_potongan'] ?? 0) ?></td>
                            <td class="text-end text-success fw-bold"><?= formatRupiah($item['gaji_bersih'] ?? 0) ?></td>
                            <td>
                                <span class="badge bg-<?= getStatusBadgeClass($item['status'] ?? 'draft') ?>">
                                    <?= getStatusLabel($item['status'] ?? 'draft') ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= base_url('direktur/monitoring/ringkasan-penggajian/detail/' . $item['id']) ?>" 
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

<!-- Chart.js Script for Department Chart -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Department Chart
    const deptCtx = document.getElementById('departmentChart');
    if (deptCtx) {
        const departments = [
            <?php foreach ($summaryByDepartment as $dept): ?>
            '<?= htmlspecialchars($dept['departemen'] ?? 'Lainnya') ?>',
            <?php endforeach; ?>
        ];
        const gajiValues = [
            <?php foreach ($summaryByDepartment as $dept): ?>
            <?= $dept['total_gaji_bersih'] ?? 0 ?>,
            <?php endforeach; ?>
        ];
        
        if (departments.length > 0) {
            new Chart(deptCtx, {
                type: 'bar',
                data: {
                    labels: departments,
                    datasets: [{
                        label: 'Total Gaji Bersih',
                        data: gajiValues,
                        backgroundColor: 'rgba(78, 115, 223, 0.8)',
                        borderColor: '#4e73df',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Total: Rp ' + context.raw.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        }
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
.table td {
    vertical-align: middle;
}
.text-end {
    text-align: right;
}
.text-bronze {
    color: #cd7f32;
}
</style>

<?= view('direktur/templates/footer', ['scripts' => $scripts ?? []]) ?>