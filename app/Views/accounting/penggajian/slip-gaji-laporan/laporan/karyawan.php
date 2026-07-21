<?php
// app/Views/accounting/penggajian/slip-gaji-laporan/laporan/karyawan.php
$data['active'] = 'slip-gaji-laporan';
$this->extend('accounting/templates/header');
?>

<?php $this->section('content'); ?>
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 text-gradient-accounting">Laporan Penggajian Karyawan</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-2">
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/penggajian') ?>">Penggajian</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/penggajian/slip-gaji-laporan') ?>">Slip Gaji & Laporan</a></li>
                    <li class="breadcrumb-item active">Laporan Karyawan</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= site_url('accounting/penggajian/slip-gaji/export-excel?karyawan_id=' . $karyawan['id'] . '&tahun=' . $tahun) ?>" class="btn btn-success">
                <i class="fas fa-file-excel me-1"></i> Export Excel
            </a>
            <a href="<?= site_url('accounting/penggajian/slip-gaji/export-pdf?karyawan_id=' . $karyawan['id'] . '&tahun=' . $tahun) ?>" class="btn btn-danger">
                <i class="fas fa-file-pdf me-1"></i> Export PDF
            </a>
            <a href="<?= site_url('accounting/penggajian/slip-gaji-laporan') ?>" class="btn btn-accounting-outline">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Informasi Karyawan -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-gradient-accounting text-white">
                    <i class="fas fa-user-circle me-2"></i> Profil Karyawan
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="35%"><strong>NIK</strong></td>
                                    <td>: <?= $karyawan['nik'] ?? '-' ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Nama Lengkap</strong></td>
                                    <td>: <?= $karyawan['nama_lengkap'] ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Nama Panggilan</strong></td>
                                    <td>: <?= $karyawan['nama_panggilan'] ?? '-' ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Jenis Kelamin</strong></td>
                                    <td>: <?= $karyawan['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Tempat, Tanggal Lahir</strong></td>
                                    <td>: <?= ($karyawan['tempat_lahir'] ?? '-') . ', ' . ($karyawan['tanggal_lahir'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Agama</strong></td>
                                    <td>: <?= $karyawan['agama'] ?? '-' ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="35%"><strong>Jabatan</strong></td>
                                    <td>: <?= $karyawan['jabatan'] ?? '-' ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Departemen</strong></td>
                                    <td>: <?= $karyawan['departemen'] ?? '-' ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Divisi</strong></td>
                                    <td>: <?= $karyawan['divisi'] ?? '-' ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Masuk</strong></td>
                                    <td>: <?= $karyawan['tanggal_masuk'] ?? '-' ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Status Karyawan</strong></td>
                                    <td>: 
                                        <span class="badge bg-<?= $karyawan['status_karyawan'] == 'Tetap' ? 'success' : ($karyawan['status_karyawan'] == 'Kontrak' ? 'info' : 'warning') ?>">
                                            <?= $karyawan['status_karyawan'] ?? '-' ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Bank & No Rekening</strong></td>
                                    <td>: <?= ($karyawan['bank'] ?? '-') . ' - ' . ($karyawan['no_rekening'] ?? '-') ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-chart-pie me-2"></i> Ringkasan Tahunan
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="50%"><strong>Total Gaji Bersih</strong></td>
                            <td class="text-end"><strong class="text-success">Rp <?= number_format($ringkasanTahunan['total_gaji_bersih'], 0, ',', '.') ?></strong></td>
                        </tr>
                        <tr>
                            <td><strong>Total Pendapatan</strong></td>
                            <td class="text-end">Rp <?= number_format($ringkasanTahunan['total_pendapatan'], 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td><strong>Total Potongan</strong></td>
                            <td class="text-end text-danger">Rp <?= number_format($ringkasanTahunan['total_potongan'], 0, ',', '.') ?></td>
                        </tr>
                        <tr class="border-top">
                            <td><strong>Rata-rata Gaji per Bulan</strong></td>
                            <td class="text-end">Rp <?= number_format($ringkasanTahunan['rata_rata_gaji'], 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td><strong>Jumlah Bulan Gaji</strong></td>
                            <td class="text-end"><?= count($riwayat) ?> bulan</td>
                        </tr>
                    </table>
                </div>
            </div>
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
                    <a href="<?= site_url('accounting/penggajian/slip-gaji/laporan-karyawan/' . $karyawan['id']) ?>" class="btn btn-accounting-outline w-100">
                        <i class="fas fa-undo-alt me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Grafik Gaji Bulanan -->
    <div class="card mb-4">
        <div class="card-header bg-gradient-accounting text-white">
            <i class="fas fa-chart-line me-2"></i> Grafik Gaji Bulanan - Tahun <?= $tahun ?>
        </div>
        <div class="card-body">
            <canvas id="salaryChart" style="height: 300px;"></canvas>
        </div>
    </div>

    <!-- Riwayat Gaji -->
    <div class="card">
        <div class="card-header bg-gradient-accounting text-white">
            <i class="fas fa-history me-2"></i> Riwayat Gaji
            <span class="badge bg-light text-dark ms-2"><?= count($riwayat) ?> bulan</span>
        </div>
        <div class="card-body">
            <?php if (empty($riwayat)): ?>
                <div class="alert alert-warning text-center py-5">
                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                    <h5>Tidak Ada Data Gaji</h5>
                    <p class="mb-0">Belum ada data perhitungan gaji untuk tahun <?= $tahun ?>.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover accounting-table" id="riwayatTable">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">Periode</th>
                                <th width="10%">Nomor</th>
                                <th width="12%">Gaji Pokok</th>
                                <th width="12%">Tunjangan</th>
                                <th width="10%">Lembur</th>
                                <th width="12%">Pendapatan</th>
                                <th width="12%">Potongan</th>
                                <th width="12%">Gaji Bersih</th>
                                <th width="5%">Status</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($riwayat as $item): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td class="text-center">
                                    <?= $bulanOptions[$item['periode_bulan']] ?> <?= $item['periode_tahun'] ?>
                                </td>
                                <td class="text-center"><code><?= $item['nomor_perhitungan'] ?></code></td>
                                <td class="text-end">Rp <?= number_format($item['gaji_pokok'], 0, ',', '.') ?></td>
                                <td class="text-end">
                                    Rp <?= number_format(($item['tunjangan_jabatan'] ?? 0) + ($item['tunjangan_makan'] ?? 0) + ($item['tunjangan_transport'] ?? 0) + ($item['tunjangan_lainnya'] ?? 0), 0, ',', '.') ?>
                                </td>
                                <td class="text-end">Rp <?= number_format($item['upah_lembur'] ?? 0, 0, ',', '.') ?></td>
                                <td class="text-end">Rp <?= number_format($item['total_pendapatan'], 0, ',', '.') ?></td>
                                <td class="text-end text-danger">Rp <?= number_format($item['total_potongan'], 0, ',', '.') ?></td>
                                <td class="text-end text-primary">
                                    <strong>Rp <?= number_format($item['gaji_bersih'], 0, ',', '.') ?></strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success">Disetujui</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="<?= site_url('accounting/penggajian/slip-gaji/view/' . $item['id']) ?>" 
                                           class="btn btn-sm btn-info" target="_blank" title="Lihat Slip">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= site_url('accounting/penggajian/slip-gaji/print/' . $item['id']) ?>" 
                                           class="btn btn-sm btn-secondary" target="_blank" title="Print">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <a href="<?= site_url('accounting/penggajian/slip-gaji/pdf/' . $item['id']) ?>" 
                                           class="btn btn-sm btn-danger" target="_blank" title="PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-active">
                            <tr>
                                <td colspan="3" class="text-end"><strong>TOTAL</strong></td>
                                <td class="text-end"><strong>Rp <?= number_format(array_sum(array_column($riwayat, 'gaji_pokok')), 0, ',', '.') ?></strong></td>
                                <td class="text-end"><strong>Rp <?= number_format(array_sum(array_column($riwayat, 'tunjangan_jabatan')) + array_sum(array_column($riwayat, 'tunjangan_makan')) + array_sum(array_column($riwayat, 'tunjangan_transport')) + array_sum(array_column($riwayat, 'tunjangan_lainnya')), 0, ',', '.') ?></strong></td>
                                <td class="text-end"><strong>Rp <?= number_format(array_sum(array_column($riwayat, 'upah_lembur')), 0, ',', '.') ?></strong></td>
                                <td class="text-end"><strong>Rp <?= number_format(array_sum(array_column($riwayat, 'total_pendapatan')), 0, ',', '.') ?></strong></td>
                                <td class="text-end"><strong>Rp <?= number_format(array_sum(array_column($riwayat, 'total_potongan')), 0, ',', '.') ?></strong></td>
                                <td class="text-end"><strong>Rp <?= number_format(array_sum(array_column($riwayat, 'gaji_bersih')), 0, ',', '.') ?></strong></td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Statistik Tambahan -->
    <?php if (!empty($riwayat)): ?>
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card border-left-success">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Gaji Tertinggi</h6>
                    <h4 class="mb-0 text-success">Rp <?= number_format(max(array_column($riwayat, 'gaji_bersih')), 0, ',', '.') ?></h4>
                    <small>
                        <?php 
                        $maxIndex = array_search(max(array_column($riwayat, 'gaji_bersih')), array_column($riwayat, 'gaji_bersih'));
                        if ($maxIndex !== false): ?>
                            Periode: <?= $bulanOptions[$riwayat[$maxIndex]['periode_bulan']] ?> <?= $riwayat[$maxIndex]['periode_tahun'] ?>
                        <?php endif; ?>
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-warning">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Gaji Terendah</h6>
                    <h4 class="mb-0 text-warning">Rp <?= number_format(min(array_column($riwayat, 'gaji_bersih')), 0, ',', '.') ?></h4>
                    <small>
                        <?php 
                        $minIndex = array_search(min(array_column($riwayat, 'gaji_bersih')), array_column($riwayat, 'gaji_bersih'));
                        if ($minIndex !== false): ?>
                            Periode: <?= $bulanOptions[$riwayat[$minIndex]['periode_bulan']] ?> <?= $riwayat[$minIndex]['periode_tahun'] ?>
                        <?php endif; ?>
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-info">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Rata-rata Gaji</h6>
                    <h4 class="mb-0 text-info">Rp <?= number_format(array_sum(array_column($riwayat, 'gaji_bersih')) / count($riwayat), 0, ',', '.') ?></h4>
                    <small>per bulan</small>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Grafik Gaji Bulanan
let ctx = document.getElementById('salaryChart').getContext('2d');
let salaryChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [<?php 
            $labels = [];
            foreach ($riwayat as $item) {
                $labels[] = "'" . $bulanOptions[$item['periode_bulan']] . "'";
            }
            echo implode(',', $labels);
        ?>],
        datasets: [{
            label: 'Gaji Bersih',
            data: [<?php 
                $data = [];
                foreach ($riwayat as $item) {
                    $data[] = $item['gaji_bersih'];
                }
                echo implode(',', $data);
            ?>],
            borderColor: 'rgb(40, 167, 69)',
            backgroundColor: 'rgba(40, 167, 69, 0.1)',
            tension: 0.3,
            fill: true,
            pointBackgroundColor: 'rgb(40, 167, 69)',
            pointBorderColor: '#fff',
            pointRadius: 5,
            pointHoverRadius: 7
        }, {
            label: 'Total Pendapatan',
            data: [<?php 
                $pendapatanData = [];
                foreach ($riwayat as $item) {
                    $pendapatanData[] = $item['total_pendapatan'];
                }
                echo implode(',', $pendapatanData);
            ?>],
            borderColor: 'rgb(23, 162, 184)',
            backgroundColor: 'rgba(23, 162, 184, 0.1)',
            tension: 0.3,
            fill: true,
            pointBackgroundColor: 'rgb(23, 162, 184)',
            pointBorderColor: '#fff',
            pointRadius: 5,
            pointHoverRadius: 7
        }, {
            label: 'Total Potongan',
            data: [<?php 
                $potonganData = [];
                foreach ($riwayat as $item) {
                    $potonganData[] = $item['total_potongan'];
                }
                echo implode(',', $potonganData);
            ?>],
            borderColor: 'rgb(220, 53, 69)',
            backgroundColor: 'rgba(220, 53, 69, 0.1)',
            tension: 0.3,
            fill: true,
            pointBackgroundColor: 'rgb(220, 53, 69)',
            pointBorderColor: '#fff',
            pointRadius: 5,
            pointHoverRadius: 7
        }]
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
                text: 'Grafik Gaji Bulanan - <?= $karyawan['nama_lengkap'] ?> (<?= $tahun ?>)'
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

// Initialize DataTable
$(document).ready(function() {
    $('#riwayatTable').DataTable({
        "pageLength": 12,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "responsive": true,
        "language": {
            "search": "Cari:",
            "lengthMenu": "Tampilkan _MENU_ data",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Berikutnya",
                "previous": "Sebelumnya"
            }
        },
        "order": [[1, 'desc']]
    });
});
</script>

<?php $this->endSection(); ?>