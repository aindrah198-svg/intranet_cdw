<?php
$title = 'Dashboard Accounting';
$active = 'dashboard';
$subtitle = 'Selamat datang di sistem accounting';
?>

<?= $this->include('accounting/templates/header') ?>
<?= $this->include('accounting/templates/sidebar') ?>
<?= $this->include('accounting/templates/navbar') ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h2 class="page-title">Dashboard Accounting</h2>
            <p class="page-subtitle">Selamat datang, <?= $user['name'] ?? 'User' ?></p>
        </div>
    </div>

    <!-- Welcome Card -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="financial-card fade-in">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="mb-3">
                            <i class="fas fa-user-circle me-2 text-accounting-primary"></i>
                            Selamat Datang, <?= htmlspecialchars($karyawan['nama_panggilan'] ?? $user['name'] ?? 'Accounting Staff') ?>!
                        </h4>
                        <p class="text-muted mb-2">
                            Anda login sebagai <span class="badge bg-primary"><?= strtoupper($user['role'] ?? 'ACCOUNTING') ?></span>
                        </p>
                        <div class="d-flex flex-wrap gap-3 mt-3">
                            <?php if(!empty($karyawan['nik'])): ?>
                            <span class="text-muted">
                                <i class="fas fa-id-badge me-1"></i>
                                NIK: <?= htmlspecialchars($karyawan['nik']) ?>
                            </span>
                            <?php endif; ?>
                            <span class="text-muted">
                                <i class="fas fa-briefcase me-1"></i>
                                <?= htmlspecialchars($karyawan['jabatan'] ?? 'Accounting Staff') ?>
                            </span>
                            <span class="text-muted">
                                <i class="fas fa-building me-1"></i>
                                <?= htmlspecialchars($karyawan['departemen'] ?? 'Finance & Accounting') ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="bg-gradient-accounting text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" 
                             style="width: 80px; height: 80px; font-size: 2rem; font-weight: bold;">
                            <?= strtoupper(substr($karyawan['nama_panggilan'] ?? $user['name'] ?? 'A', 0, 1)) ?>
                        </div>
                        <h6 class="mb-0"><?= htmlspecialchars($karyawan['nama_lengkap'] ?? $user['name'] ?? 'Accounting Staff') ?></h6>
                        <small class="text-muted"><?= htmlspecialchars($karyawan['email'] ?? $user['email'] ?? '') ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="financial-card fade-in">
                <h5 class="mb-4">
                    <i class="fas fa-chart-line me-2 text-accounting-primary"></i>
                    Ringkasan Keuangan
                </h5>
                
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="financial-card financial-card-income">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Total Pendapatan</h6>
                                    <h4 class="mb-0">Rp 850 Jt</h4>
                                    <small class="text-success">
                                        <i class="fas fa-arrow-up me-1"></i> 15% dari bulan lalu
                                    </small>
                                </div>
                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px;">
                                    <i class="fas fa-arrow-up"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="financial-card financial-card-expense">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Total Pengeluaran</h6>
                                    <h4 class="mb-0">Rp 620 Jt</h4>
                                    <small class="text-danger">
                                        <i class="fas fa-arrow-down me-1"></i> 8% dari bulan lalu
                                    </small>
                                </div>
                                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px;">
                                    <i class="fas fa-arrow-down"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="financial-card financial-card-asset">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Kas & Bank</h6>
                                    <h4 class="mb-0">Rp 1.2 M</h4>
                                    <small class="text-info">
                                        <i class="fas fa-wallet me-1"></i> Saldo terkini
                                    </small>
                                </div>
                                <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px;">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="financial-card financial-card-liability">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Hutang Dagang</h6>
                                    <h4 class="mb-0">Rp 380 Jt</h4>
                                    <small class="text-warning">
                                        <i class="fas fa-calendar-alt me-1"></i> Jatuh tempo: 5 hari
                                    </small>
                                </div>
                                <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px;">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <div class="financial-card">
                <h5 class="mb-3">
                    <i class="fas fa-chart-bar me-2 text-accounting-primary"></i>
                    Performa Keuangan 6 Bulan Terakhir
                </h5>
                <div class="chart-container" style="height: 300px;">
                    <canvas id="financialChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mb-4">
            <div class="financial-card">
                <h5 class="mb-3">
                    <i class="fas fa-chart-pie me-2 text-accounting-primary"></i>
                    Distribusi Pengeluaran
                </h5>
                <div class="chart-container" style="height: 250px;">
                    <canvas id="expenseChart"></canvas>
                </div>
                <div class="mt-3 text-center">
                    <span class="badge bg-primary me-2 mb-1">Gaji 45%</span>
                    <span class="badge bg-success me-2 mb-1">Operasional 30%</span>
                    <span class="badge bg-info mb-1">Lainnya 25%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="financial-card">
                <h5 class="mb-3">
                    <i class="fas fa-exchange-alt me-2 text-accounting-primary"></i>
                    Transaksi Terbaru
                </h5>
                <div class="table-responsive">
                    <table class="table accounting-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th>Jenis</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>15 Jan 2024</td>
                                <td>Pembayaran Invoice PT. Pertamina</td>
                                <td><span class="badge bg-success">Pendapatan</span></td>
                                <td class="credit-amount">Rp 2.5 M</td>
                                <td><span class="badge bg-success">Lunas</span></td>
                            </tr>
                            <tr>
                                <td>14 Jan 2024</td>
                                <td>Biaya operasional bulanan</td>
                                <td><span class="badge bg-danger">Pengeluaran</span></td>
                                <td class="debit-amount">Rp 250 Jt</td>
                                <td><span class="badge bg-info">Diproses</span></td>
                            </tr>
                            <tr>
                                <td>13 Jan 2024</td>
                                <td>Gaji karyawan Januari 2024</td>
                                <td><span class="badge bg-danger">Pengeluaran</span></td>
                                <td class="debit-amount">Rp 1.85 M</td>
                                <td><span class="badge bg-success">Selesai</span></td>
                            </tr>
                            <tr>
                                <td>12 Jan 2024</td>
                                <td>Pembelian peralatan kantor</td>
                                <td><span class="badge bg-danger">Pengeluaran</span></td>
                                <td class="debit-amount">Rp 85 Jt</td>
                                <td><span class="badge bg-warning">Pending</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="text-end mt-3">
                    <a href="<?= base_url('accounting/transactions') ?>" class="btn btn-accounting-outline">
                        <i class="fas fa-list me-1"></i> Lihat Semua Transaksi
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="financial-card">
                <h5 class="mb-3">
                    <i class="fas fa-bolt me-2 text-accounting-primary"></i>
                    Akses Cepat
                </h5>
                <div class="row g-3">
                    <div class="col-md-3 col-sm-6">
                        <a href="<?= base_url('accounting/kas-bank') ?>" class="btn btn-accounting w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3 text-center">
                            <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                            <h6>Kas & Bank</h6>
                            <small class="text-muted">Manajemen kas</small>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="<?= base_url('accounting/pembukuan') ?>" class="btn btn-accounting-outline w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3 text-center">
                            <i class="fas fa-book fa-2x mb-2"></i>
                            <h6>Pembukuan</h6>
                            <small class="text-muted">Jurnal & buku besar</small>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="<?= base_url('accounting/penggajian') ?>" class="btn btn-accounting w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3 text-center">
                            <i class="fas fa-money-check-alt fa-2x mb-2"></i>
                            <h6>Penggajian</h6>
                            <small class="text-muted">Sistem gaji</small>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="<?= base_url('accounting/laporan-keuangan') ?>" class="btn btn-accounting-outline w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3 text-center">
                            <i class="fas fa-chart-bar fa-2x mb-2"></i>
                            <h6>Laporan</h6>
                            <small class="text-muted">Laporan keuangan</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Financial Chart
    const financialCtx = document.getElementById('financialChart').getContext('2d');
    const financialChart = new Chart(financialCtx, {
        type: 'line',
        data: {
            labels: ['Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Pendapatan',
                data: [750, 820, 780, 850, 920, 950],
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }, {
                label: 'Pengeluaran',
                data: [620, 650, 680, 700, 750, 780],
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value + ' Jt';
                        }
                    }
                }
            }
        }
    });

    // Expense Chart
    const expenseCtx = document.getElementById('expenseChart').getContext('2d');
    const expenseChart = new Chart(expenseCtx, {
        type: 'doughnut',
        data: {
            labels: ['Gaji', 'Operasional', 'Lainnya'],
            datasets: [{
                data: [45, 30, 25],
                backgroundColor: ['#28a745', '#17a2b8', '#6c757d'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            cutout: '65%'
        }
    });
});
</script>

<?= $this->include('accounting/templates/footer') ?>