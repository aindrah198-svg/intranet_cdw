<?php
// app/Views/accounting/penggajian/slip-gaji-laporan/laporan/rekap.php
$data['active'] = 'slip-gaji-laporan';
$this->extend('accounting/templates/header');
?>

<?php $this->section('content'); ?>
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 text-gradient-accounting">Rekap Gaji Tahunan</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-2">
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/penggajian') ?>">Penggajian</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/penggajian/slip-gaji-laporan') ?>">Slip Gaji & Laporan</a></li>
                    <li class="breadcrumb-item active">Rekap Gaji Tahunan</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= site_url('accounting/penggajian/slip-gaji/export-excel?tahun=' . $tahun) ?>" class="btn btn-success">
                <i class="fas fa-file-excel me-1"></i> Export Excel
            </a>
            <a href="<?= site_url('accounting/penggajian/slip-gaji/export-pdf?tahun=' . $tahun) ?>" class="btn btn-danger">
                <i class="fas fa-file-pdf me-1"></i> Export PDF
            </a>
            <a href="<?= site_url('accounting/penggajian/slip-gaji-laporan') ?>" class="btn btn-accounting-outline">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Filter Tahun -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tahun</label>
                    <select name="tahun" class="form-select">
                        <?php foreach ($tahunOptions as $t): ?>
                            <option value="<?= $t ?>" <?= $tahun == $t ? 'selected' : '' ?>>
                                <?= $t ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-accounting w-100">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <a href="<?= site_url('accounting/penggajian/slip-gaji/rekap-gaji?tahun=' . date('Y')) ?>" class="btn btn-accounting-outline w-100">
                        <i class="fas fa-undo-alt me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow-sm h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Karyawan (Rata-rata)
                            </div>
                            <div class="h5 mb-0 font-weight-bold"><?= number_format($totalTahunan['total_karyawan'] / 12, 1) ?></div>
                            <small class="text-muted">per bulan</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow-sm h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Gaji Bersih
                            </div>
                            <div class="h5 mb-0 font-weight-bold">Rp <?= number_format($totalTahunan['total_gaji_bersih'], 0, ',', '.') ?></div>
                            <small class="text-muted">seluruh tahun</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow-sm h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Rata-rata Gaji per Bulan
                            </div>
                            <div class="h5 mb-0 font-weight-bold">Rp <?= number_format($totalTahunan['total_gaji_bersih'] / 12, 0, ',', '.') ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow-sm h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Potongan
                            </div>
                            <div class="h5 mb-0 font-weight-bold">Rp <?= number_format($totalTahunan['total_potongan'], 0, ',', '.') ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-minus-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Rekap Bulanan -->
    <div class="card mb-4">
        <div class="card-header bg-gradient-accounting text-white">
            <i class="fas fa-chart-bar me-2"></i> Grafik Rekap Gaji Bulanan - Tahun <?= $tahun ?>
        </div>
        <div class="card-body">
            <canvas id="rekapChart" style="height: 350px;"></canvas>
        </div>
    </div>

    <!-- Tabel Rekap Bulanan -->
    <div class="card mb-4">
        <div class="card-header bg-gradient-accounting text-white">
            <i class="fas fa-table me-2"></i> Rekap Gaji per Bulan
            <span class="badge bg-light text-dark ms-2">Tahun <?= $tahun ?></span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="rekapTable">
                    <thead class="table-secondary">
                        <tr>
                            <th width="5%">No</th>
                            <th width="10%">Bulan</th>
                            <th width="8%">Jumlah Karyawan</th>
                            <th width="12%">Total Gaji Pokok</th>
                            <th width="12%">Total Tunjangan</th>
                            <th width="10%">Total Upah Lembur</th>
                            <th width="12%">Total Pendapatan</th>
                            <th width="12%">Total Potongan</th>
                            <th width="12%">Total Gaji Bersih</th>
                            <th width="7%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($rekap as $item): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td class="text-center">
                                <strong><?= $item['nama_bulan'] ?></strong>
                            </td>
                            <td class="text-center"><?= number_format($item['jumlah_karyawan']) ?> org</td>
                            <td class="text-end">Rp <?= number_format($item['total_gaji_pokok'], 0, ',', '.') ?></td>
                            <td class="text-end">Rp <?= number_format($item['total_tunjangan'], 0, ',', '.') ?></td>
                            <td class="text-end">Rp <?= number_format($item['total_upah_lembur'], 0, ',', '.') ?></td>
                            <td class="text-end">Rp <?= number_format($item['total_pendapatan'], 0, ',', '.') ?></td>
                            <td class="text-end text-danger">Rp <?= number_format($item['total_potongan'], 0, ',', '.') ?></td>
                            <td class="text-end text-primary">
                                <strong>Rp <?= number_format($item['total_gaji_bersih'], 0, ',', '.') ?></strong>
                            </td>
                            <td class="text-center">
                                <a href="<?= site_url('accounting/penggajian/slip-gaji/laporan-periode?bulan=' . $item['bulan'] . '&tahun=' . $tahun) ?>" 
                                   class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-active">
                        <tr>
                            <td colspan="2" class="text-end"><strong>TOTAL</strong></td>
                            <td class="text-center"><strong><?= number_format($totalTahunan['total_karyawan']) ?></strong></td>
                            <td class="text-end"><strong>Rp <?= number_format($totalTahunan['total_gaji_pokok'], 0, ',', '.') ?></strong></td>
                            <td class="text-end"><strong>Rp <?= number_format($totalTahunan['total_tunjangan'], 0, ',', '.') ?></strong></td>
                            <td class="text-end"><strong>Rp <?= number_format($totalTahunan['total_upah_lembur'], 0, ',', '.') ?></strong></td>
                            <td class="text-end"><strong>Rp <?= number_format($totalTahunan['total_pendapatan'], 0, ',', '.') ?></strong></td>
                            <td class="text-end"><strong>Rp <?= number_format($totalTahunan['total_potongan'], 0, ',', '.') ?></strong></td>
                            <td class="text-end"><strong>Rp <?= number_format($totalTahunan['total_gaji_bersih'], 0, ',', '.') ?></strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Statistik Perbandingan -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-chart-pie me-2"></i> Komposisi Gaji Tahunan
                </div>
                <div class="card-body">
                    <canvas id="compositionChart" style="height: 300px;"></canvas>
                    <div class="mt-3">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="border rounded p-2">
                                    <h6 class="text-success mb-0">Gaji Pokok</h6>
                                    <h5 class="mb-0"><?= number_format(($totalTahunan['total_gaji_pokok'] / $totalTahunan['total_pendapatan']) * 100, 1) ?>%</h5>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded p-2">
                                    <h6 class="text-info mb-0">Tunjangan</h6>
                                    <h5 class="mb-0"><?= number_format(($totalTahunan['total_tunjangan'] / $totalTahunan['total_pendapatan']) * 100, 1) ?>%</h5>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded p-2">
                                    <h6 class="text-warning mb-0">Lembur</h6>
                                    <h5 class="mb-0"><?= number_format(($totalTahunan['total_upah_lembur'] / $totalTahunan['total_pendapatan']) * 100, 1) ?>%</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <i class="fas fa-chart-pie me-2"></i> Komposisi Potongan Tahunan
                </div>
                <div class="card-body">
                    <canvas id="potonganChart" style="height: 300px;"></canvas>
                    <div class="mt-3">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="border rounded p-2">
                                    <h6 class="text-danger mb-0">BPJS</h6>
                                    <h5 class="mb-0"><?= number_format(($totalTahunan['total_potongan_bpjs'] ?? 0) / max($totalTahunan['total_potongan'], 1) * 100, 1) ?>%</h5>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded p-2">
                                    <h6 class="text-danger mb-0">PPh 21</h6>
                                    <h5 class="mb-0"><?= number_format(($totalTahunan['total_potongan_pph21'] ?? 0) / max($totalTahunan['total_potongan'], 1) * 100, 1) ?>%</h5>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded p-2">
                                    <h6 class="text-danger mb-0">Lainnya</h6>
                                    <h5 class="mb-0"><?= number_format(($totalTahunan['total_potongan_lain'] ?? 0) / max($totalTahunan['total_potongan'], 1) * 100, 1) ?>%</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Trend Analysis -->
    <div class="card mt-4">
        <div class="card-header bg-gradient-accounting text-white">
            <i class="fas fa-chart-line me-2"></i> Analisis Trend Gaji
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="alert alert-success">
                        <h6 class="mb-1">📈 Bulan dengan Gaji Tertinggi</h6>
                        <p class="mb-0">
                            <?php 
                            $maxGaji = max(array_column($rekap, 'total_gaji_bersih'));
                            $maxIndex = array_search($maxGaji, array_column($rekap, 'total_gaji_bersih'));
                            if ($maxIndex !== false):
                            ?>
                            <strong><?= $rekap[$maxIndex]['nama_bulan'] ?></strong><br>
                            Rp <?= number_format($rekap[$maxIndex]['total_gaji_bersih'], 0, ',', '.') ?>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-warning">
                        <h6 class="mb-1">📉 Bulan dengan Gaji Terendah</h6>
                        <p class="mb-0">
                            <?php 
                            $minGaji = min(array_column($rekap, 'total_gaji_bersih'));
                            $minIndex = array_search($minGaji, array_column($rekap, 'total_gaji_bersih'));
                            if ($minIndex !== false):
                            ?>
                            <strong><?= $rekap[$minIndex]['nama_bulan'] ?></strong><br>
                            Rp <?= number_format($rekap[$minIndex]['total_gaji_bersih'], 0, ',', '.') ?>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-info">
                        <h6 class="mb-1">📊 Rata-rata Pertumbuhan</h6>
                        <p class="mb-0">
                            <?php 
                            $gajiList = array_column($rekap, 'total_gaji_bersih');
                            $growth = 0;
                            if (count($gajiList) > 1) {
                                $growth = (($gajiList[count($gajiList)-1] - $gajiList[0]) / max($gajiList[0], 1)) * 100;
                            }
                            ?>
                            <strong><?= number_format($growth, 1) ?>%</strong><br>
                            dari Jan ke Des
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Rekap Bulanan Chart (Bar)
let ctxRekap = document.getElementById('rekapChart').getContext('2d');
let rekapChart = new Chart(ctxRekap, {
    type: 'bar',
    data: {
        labels: [<?php 
            $labels = [];
            foreach ($rekap as $item) {
                $labels[] = "'" . $item['nama_bulan'] . "'";
            }
            echo implode(',', $labels);
        ?>],
        datasets: [
            {
                label: 'Total Gaji Bersih',
                data: [<?php 
                    $gajiData = [];
                    foreach ($rekap as $item) {
                        $gajiData[] = $item['total_gaji_bersih'];
                    }
                    echo implode(',', $gajiData);
                ?>],
                backgroundColor: 'rgba(40, 167, 69, 0.8)',
                borderColor: 'rgb(40, 167, 69)',
                borderWidth: 1,
                borderRadius: 5
            },
            {
                label: 'Total Pendapatan',
                data: [<?php 
                    $pendapatanData = [];
                    foreach ($rekap as $item) {
                        $pendapatanData[] = $item['total_pendapatan'];
                    }
                    echo implode(',', $pendapatanData);
                ?>],
                backgroundColor: 'rgba(23, 162, 184, 0.8)',
                borderColor: 'rgb(23, 162, 184)',
                borderWidth: 1,
                borderRadius: 5
            },
            {
                label: 'Total Potongan',
                data: [<?php 
                    $potonganData = [];
                    foreach ($rekap as $item) {
                        $potonganData[] = $item['total_potongan'];
                    }
                    echo implode(',', $potonganData);
                ?>],
                backgroundColor: 'rgba(220, 53, 69, 0.8)',
                borderColor: 'rgb(220, 53, 69)',
                borderWidth: 1,
                borderRadius: 5
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Rekap Gaji Bulanan - Tahun <?= $tahun ?>'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        let value = context.raw;
                        return label + ': Rp ' + new Intl.NumberFormat('id-ID').format(value);
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                    }
                }
            }
        }
    }
});

// Komposisi Gaji (Pie)
let ctxComposition = document.getElementById('compositionChart').getContext('2d');
let compositionChart = new Chart(ctxComposition, {
    type: 'pie',
    data: {
        labels: ['Gaji Pokok', 'Tunjangan', 'Upah Lembur'],
        datasets: [{
            data: [
                <?= $totalTahunan['total_gaji_pokok'] ?>,
                <?= $totalTahunan['total_tunjangan'] ?>,
                <?= $totalTahunan['total_upah_lembur'] ?>
            ],
            backgroundColor: [
                'rgba(40, 167, 69, 0.8)',
                'rgba(23, 162, 184, 0.8)',
                'rgba(255, 193, 7, 0.8)'
            ],
            borderColor: '#fff',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.label || '';
                        let value = context.raw;
                        let total = context.dataset.data.reduce((a, b) => a + b, 0);
                        let percentage = ((value / total) * 100).toFixed(1);
                        return label + ': Rp ' + new Intl.NumberFormat('id-ID').format(value) + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});

// Komposisi Potongan (Pie)
let ctxPotongan = document.getElementById('potonganChart').getContext('2d');
let potonganChart = new Chart(ctxPotongan, {
    type: 'pie',
    data: {
        labels: ['BPJS Kesehatan & TK', 'PPh 21', 'Potongan Lainnya'],
        datasets: [{
            data: [
                <?= $totalTahunan['total_bpjs_kes'] + $totalTahunan['total_bpjs_tk'] ?? 0 ?>,
                <?= $totalTahunan['total_pph21'] ?? 0 ?>,
                <?= $totalTahunan['total_potongan'] - (($totalTahunan['total_bpjs_kes'] ?? 0) + ($totalTahunan['total_bpjs_tk'] ?? 0) + ($totalTahunan['total_pph21'] ?? 0)) ?>
            ],
            backgroundColor: [
                'rgba(220, 53, 69, 0.8)',
                'rgba(255, 193, 7, 0.8)',
                'rgba(108, 117, 125, 0.8)'
            ],
            borderColor: '#fff',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.label || '';
                        let value = context.raw;
                        let total = context.dataset.data.reduce((a, b) => a + b, 0);
                        let percentage = ((value / total) * 100).toFixed(1);
                        return label + ': Rp ' + new Intl.NumberFormat('id-ID').format(value) + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});

// Initialize DataTable
$(document).ready(function() {
    $('#rekapTable').DataTable({
        "pageLength": 12,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "responsive": true,
        "language": {
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Berikutnya",
                "previous": "Sebelumnya"
            }
        },
        "order": [[1, 'asc']]
    });
});
</script>

<?php $this->endSection(); ?>