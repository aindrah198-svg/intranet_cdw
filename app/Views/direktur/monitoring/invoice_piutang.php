<?php
// Data dari controller
$invoiceData = $invoiceData ?? [];
$clientList = $clientList ?? [];
$statusFilter = $statusFilter ?? '';
$clientIdFilter = $clientIdFilter ?? '';
$startDate = $startDate ?? '';
$endDate = $endDate ?? '';
$searchQuery = $searchQuery ?? '';
$currentPage = $currentPage ?? 1;
$perPage = $perPage ?? 20;
$totalData = $totalData ?? 0;
$totalPages = $totalPages ?? 1;
$baseUrl = $baseUrl ?? base_url('direktur/monitoring/invoice-piutang');
$queryParams = $queryParams ?? [];
$stats = $stats ?? [];
$statusOptions = $statusOptions ?? [];
$agingReport = $agingReport ?? [];
$agingSummary = $agingSummary ?? [];
$summaryByClient = $summaryByClient ?? [];

// Helper functions
if (!function_exists('formatRupiah')) {
    function formatRupiah($amount) {
        if (empty($amount) && $amount !== 0) return '-';
        return 'Rp ' . number_format((float)$amount, 0, ',', '.');
    }
}

if (!function_exists('formatDate')) {
    function formatDate($date) {
        if (empty($date)) return '-';
        if ($date instanceof DateTime) {
            return $date->format('d/m/Y');
        }
        $timestamp = strtotime($date);
        return $timestamp ? date('d/m/Y', $timestamp) : '-';
    }
}

if (!function_exists('getStatusBadgeClass')) {
    function getStatusBadgeClass($status) {
        $classes = [
            'draft' => 'secondary',
            'sent' => 'info',
            'partial' => 'warning',
            'paid' => 'success',
            'overdue' => 'danger',
            'cancelled' => 'dark'
        ];
        return $classes[$status] ?? 'secondary';
    }
}

if (!function_exists('getStatusLabel')) {
    function getStatusLabel($status) {
        $labels = [
            'draft' => 'Draft',
            'sent' => 'Dikirim',
            'partial' => 'Sebagian Dibayar',
            'paid' => 'Lunas',
            'overdue' => 'Overdue',
            'cancelled' => 'Dibatalkan'
        ];
        return $labels[$status] ?? $status;
    }
}

if (!function_exists('isOverdue')) {
    function isOverdue($tanggal_jatuh_tempo, $status) {
        if ($status == 'paid' || $status == 'cancelled') return false;
        if (empty($tanggal_jatuh_tempo)) return false;
        return strtotime($tanggal_jatuh_tempo) < strtotime(date('Y-m-d'));
    }
}
?>

<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-gradient">Monitoring Invoice & Piutang</h4>
            <p class="text-muted mb-0">Pantau tagihan dan piutang dari client</p>
        </div>
        <div>
            <button class="btn btn-modern-outline me-2" onclick="window.location.href='<?= base_url('direktur/monitoring/invoice-piutang/print?' . http_build_query(array_filter($queryParams))) ?>'">
                <i class="fas fa-print me-2"></i>Cetak
            </button>
            <button class="btn btn-modern-primary" onclick="window.location.href='<?= base_url('direktur/monitoring/invoice-piutang/exportExcel?' . http_build_query(array_filter($queryParams))) ?>'">
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
        <form method="GET" action="<?= base_url('direktur/monitoring/invoice-piutang') ?>" class="row g-3">
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <?php foreach ($statusOptions as $key => $option): ?>
                    <option value="<?= $key ?>" <?= $statusFilter == $key ? 'selected' : '' ?>>
                        <?= $option['label'] ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Client</label>
                <select name="client_id" class="form-select">
                    <option value="">Semua Client</option>
                    <?php foreach ($clientList as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $clientIdFilter == $c['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['nama_perusahaan']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Tgl Mulai</label>
                <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($startDate) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Tgl Selesai</label>
                <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($endDate) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Cari</label>
                <input type="text" name="search" class="form-control" placeholder="No Invoice / Client..." value="<?= htmlspecialchars($searchQuery) ?>">
            </div>
            <div class="col-md-12 d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>Filter
                </button>
                <a href="<?= base_url('direktur/monitoring/invoice-piutang') ?>" class="btn btn-secondary">
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
                    <i class="fas fa-file-invoice fa-2x text-primary"></i>
                </div>
                <h2 class="mb-0"><?= number_format($stats['total_invoice'] ?? 0) ?></h2>
                <p class="text-muted mb-0">Total Invoice</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="modern-card modern-card-accent text-center">
                <div class="mb-2">
                    <i class="fas fa-money-bill-wave fa-2x text-success"></i>
                </div>
                <h5 class="mb-0"><?= formatRupiah($stats['total_nilai_invoice'] ?? 0) ?></h5>
                <p class="text-muted mb-0">Nilai Invoice</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="modern-card modern-card-warning text-center">
                <div class="mb-2">
                    <i class="fas fa-hand-holding-usd fa-2x text-warning"></i>
                </div>
                <h5 class="mb-0"><?= formatRupiah($stats['total_piutang_belum_dibayar'] ?? 0) ?></h5>
                <p class="text-muted mb-0">Piutang Belum Dibayar</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="modern-card modern-card-danger text-center">
                <div class="mb-2">
                    <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                </div>
                <h2 class="mb-0"><?= number_format($stats['total_overdue'] ?? 0) ?></h2>
                <p class="text-muted mb-0">Overdue</p>
            </div>
        </div>
    </div>

<!-- Aging Report & Summary by Client -->
<div class="row g-2 mb-3">
    <!-- Aging Report Chart - DIPERKECIL -->
    <div class="col-md-5">
        <div class="modern-card" style="padding: 0.75rem;">
            <h6 class="mb-2" style="font-size: 0.85rem;">
                <i class="fas fa-chart-pie me-1 text-primary"></i>
                Aging Piutang
            </h6>
            <canvas id="agingChart" style="height: 120px; width: 100%;"></canvas>
            <div class="mt-2">
                <div class="row text-center g-1">
                    <div class="col-3">
                        <small style="font-size: 0.7rem;" class="text-muted">Current</small>
                        <p class="mb-0 fw-bold text-success" style="font-size: 0.75rem;"><?= formatRupiah($agingSummary['current'] ?? 0) ?></p>
                    </div>
                    <div class="col-3">
                        <small style="font-size: 0.7rem;" class="text-muted">31-60 hr</small>
                        <p class="mb-0 fw-bold text-warning" style="font-size: 0.75rem;"><?= formatRupiah($agingSummary['31_60'] ?? 0) ?></p>
                    </div>
                    <div class="col-3">
                        <small style="font-size: 0.7rem;" class="text-muted">61-90 hr</small>
                        <p class="mb-0 fw-bold text-orange" style="font-size: 0.75rem;"><?= formatRupiah($agingSummary['61_90'] ?? 0) ?></p>
                    </div>
                    <div class="col-3">
                        <small style="font-size: 0.7rem;" class="text-muted">&gt;90 hr</small>
                        <p class="mb-0 fw-bold text-danger" style="font-size: 0.75rem;"><?= formatRupiah($agingSummary['90_plus'] ?? 0) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary by Client - DIPERKECIL -->
    <div class="col-md-7">
        <div class="modern-card" style="padding: 0.75rem;">
            <h6 class="mb-2" style="font-size: 0.85rem;">
                <i class="fas fa-building me-1 text-primary"></i>
                Piutang per Client
            </h6>
            <div class="table-responsive" style="max-height: 180px;">
                <table class="table table-sm" style="font-size: 0.75rem;">
                    <thead style="position: sticky; top: 0; background: white;">
                        <tr>
                            <th>Client</th>
                            <th class="text-end" width="90">Jml Inv</th>
                            <th class="text-end" width="130">Total Piutang</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($summaryByClient)): ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted py-2">Belum ada data piutang</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($summaryByClient as $client): ?>
                        <tr>
                            <td><?= htmlspecialchars(substr($client['nama_perusahaan'] ?? '-', 0, 20)) ?><?= strlen($client['nama_perusahaan'] ?? '') > 20 ? '...' : '' ?></td>
                            <td class="text-end"><?= number_format($client['jumlah_invoice'] ?? 0) ?></td>
                            <td class="text-end text-warning fw-bold"><?= formatRupiah($client['total_piutang'] ?? 0) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

    <!-- Tabel Data Invoice -->
    <div class="modern-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">
                <i class="fas fa-table me-2 text-primary"></i>
                Data Invoice
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
                        <th>Nomor Invoice</th>
                        <th>Tanggal</th>
                        <th>Jatuh Tempo</th>
                        <th>Client</th>
                        <th>Deskripsi</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Sisa Piutang</th>
                        <th>Status</th>
                        <th width="80">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($invoiceData)): ?>
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                            Tidak ada data invoice
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php 
                        $no = (($currentPage - 1) * $perPage) + 1; 
                        foreach ($invoiceData as $item): 
                            $overdue = isOverdue($item['tanggal_jatuh_tempo'] ?? '', $item['status'] ?? '');
                            $dueClass = $overdue ? 'text-danger fw-bold' : '';
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <span class="fw-bold"><?= htmlspecialchars($item['nomor_invoice']) ?></span>
                            </td>
                            <td><?= formatDate($item['tanggal_invoice'] ?? '') ?></td>
                            <td class="<?= $dueClass ?>">
                                <?= formatDate($item['tanggal_jatuh_tempo'] ?? '') ?>
                                <?php if ($overdue): ?>
                                <i class="fas fa-exclamation-circle text-danger ms-1" title="Melewati jatuh tempo"></i>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($item['nama_perusahaan'] ?? '-') ?></strong><br>
                                <small class="text-muted"><?= htmlspecialchars($item['nama_kontak'] ?? '-') ?></small>
                            </td>
                            <td><?= htmlspecialchars(substr($item['deskripsi'] ?? '-', 0, 50)) ?></td>
                            <td class="text-end fw-bold"><?= formatRupiah($item['total'] ?? 0) ?></td>
                            <td class="text-end <?= ($item['sisa_piutang'] ?? 0) > 0 ? 'text-warning fw-bold' : 'text-success' ?>">
                                <?= formatRupiah($item['sisa_piutang'] ?? 0) ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= getStatusBadgeClass($item['status'] ?? 'draft') ?>">
                                    <?= getStatusLabel($item['status'] ?? 'draft') ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= base_url('direktur/monitoring/invoice-piutang/detail/' . $item['id']) ?>" 
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
    // Aging Chart
    const agingCtx = document.getElementById('agingChart');
    if (agingCtx) {
        new Chart(agingCtx, {
            type: 'doughnut',
            data: {
                labels: ['Current (0-30 hari)', '31-60 hari', '61-90 hari', '> 90 hari'],
                datasets: [{
                    data: [
                        <?= $agingSummary['current'] ?? 0 ?>,
                        <?= $agingSummary['31_60'] ?? 0 ?>,
                        <?= $agingSummary['61_90'] ?? 0 ?>,
                        <?= $agingSummary['90_plus'] ?? 0 ?>
                    ],
                    backgroundColor: ['#1cc88a', '#f6c23e', '#fd7e14', '#e74a3b'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                return context.label + ': Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
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
.modern-card-danger {
    border-left: 4px solid #e74a3b;
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
.text-orange {
    color: #fd7e14;
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
.table-sm {
    font-size: 0.875rem;
}
</style>

<?= view('direktur/templates/footer', ['scripts' => $scripts ?? []]) ?>