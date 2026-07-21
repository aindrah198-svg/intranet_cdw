<?php
// Data dari controller
$penggajian = $penggajian ?? [];
$historyData = $historyData ?? [];
$monthNames = $monthNames ?? [];
$statusClass = $statusClass ?? [];
$statusLabel = $statusLabel ?? [];

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

if (!function_exists('formatDecimal')) {
    function formatDecimal($num, $decimals = 1) {
        if (($num === null || $num === '') && $num !== 0) return '-';
        return number_format((float)$num, $decimals, ',', '.');
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
            <h4 class="mb-1 text-gradient">Detail Penggajian Karyawan</h4>
            <p class="text-muted mb-0">
                <a href="<?= base_url('direktur/monitoring/ringkasan-penggajian') ?>" class="text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Ringkasan Penggajian
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
                            <?= strtoupper(substr($penggajian['nama_panggilan'] ?? $penggajian['nama_lengkap'] ?? '?', 0, 1)) ?>
                        </div>
                    </div>
                    <h5 class="mb-1"><?= htmlspecialchars($penggajian['nama_lengkap'] ?? '-') ?></h5>
                    <p class="text-muted mb-1"><?= htmlspecialchars($penggajian['nik'] ?? '-') ?></p>
                    <p class="mb-0">
                        <span class="badge bg-secondary"><?= htmlspecialchars($penggajian['jabatan'] ?? '-') ?></span>
                        <span class="badge bg-light text-dark"><?= htmlspecialchars($penggajian['departemen'] ?? '-') ?></span>
                    </p>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-6">
                        <h6 class="text-muted mb-1">Periode</h6>
                        <p class="mb-0 fw-bold">
                            <?= ($monthNames[$penggajian['periode_bulan']] ?? $penggajian['periode_bulan']) . ' ' . $penggajian['periode_tahun'] ?>
                        </p>
                    </div>
                    <div class="col-6">
                        <h6 class="text-muted mb-1">Status</h6>
                        <span class="badge bg-<?= $statusClass[$penggajian['status']] ?? 'secondary' ?> fs-6 px-3 py-2">
                            <?= $statusLabel[$penggajian['status']] ?? $penggajian['status'] ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Gaji Summary Card -->
            <div class="modern-card mb-4">
                <h6 class="mb-3">
                    <i class="fas fa-chart-simple me-2 text-primary"></i>
                    Ringkasan Gaji
                </h6>
                <div class="text-center mb-4">
                    <div class="gaji-circle mx-auto mb-2" style="position: relative; width: 140px; height: 140px;">
                        <div class="border rounded-circle bg-light d-flex align-items-center justify-content-center flex-column"
                             style="width: 140px; height: 140px; border-width: 3px !important; border-color: #1cc88a !important;">
                            <span class="fs-4 fw-bold text-success"><?= formatRupiah($penggajian['gaji_bersih'] ?? 0) ?></span>
                            <small class="text-muted">Gaji Bersih</small>
                        </div>
                    </div>
                </div>
                <div class="gaji-details">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Total Penghasilan</span>
                            <span class="fw-bold text-primary"><?= formatRupiah($penggajian['total_penghasilan'] ?? 0) ?></span>
                        </div>
                        <div class="progress mt-1" style="height: 6px;">
                            <div class="progress-bar bg-primary" style="width: 100%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Total Potongan</span>
                            <span class="fw-bold text-danger"><?= formatRupiah($penggajian['total_potongan'] ?? 0) ?></span>
                        </div>
                        <div class="progress mt-1" style="height: 6px;">
                            <?php 
                            $persenPotongan = ($penggajian['total_penghasilan'] ?? 1) > 0 
                                ? (($penggajian['total_potongan'] ?? 0) / ($penggajian['total_penghasilan'] ?? 1)) * 100 
                                : 0;
                            ?>
                            <div class="progress-bar bg-danger" style="width: <?= min($persenPotongan, 100) ?>%"></div>
                        </div>
                        <small class="text-muted"><?= formatDecimal($persenPotongan, 1) ?>% dari total penghasilan</small>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span><i class="fas fa-calendar-check me-2 text-success"></i>Hadir</span>
                            <span class="fw-bold"><?= formatNumber($penggajian['jumlah_hadir'] ?? 0) ?> hari</span>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <span><i class="fas fa-clock me-2 text-warning"></i>Lembur</span>
                            <span class="fw-bold"><?= formatDecimal($penggajian['total_jam_lembur'] ?? 0, 1) ?> jam</span>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <span><i class="fas fa-exclamation-triangle me-2 text-danger"></i>Terlambat</span>
                            <span class="fw-bold"><?= formatNumber($penggajian['jumlah_terlambat'] ?? 0) ?> hari</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- History Chart Card -->
            <?php if (!empty($historyData)): ?>
            <div class="modern-card">
                <h6 class="mb-3">
                    <i class="fas fa-chart-line me-2 text-primary"></i>
                    History Gaji (6 Bulan Terakhir)
                </h6>
                <canvas id="historyChart" style="height: 200px;"></canvas>
            </div>
            <?php endif; ?>
        </div>

        <!-- Right Column - Detail Data -->
        <div class="col-lg-8">
            <!-- Komponen Penghasilan Card -->
            <div class="modern-card mb-4">
                <h6 class="mb-3">
                    <i class="fas fa-plus-circle me-2 text-success"></i>
                    Komponen Penghasilan
                </h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td width="50%">Gaji Pokok</td>
                                <td width="50%" class="text-end fw-bold"><?= formatRupiah($penggajian['gaji_pokok'] ?? 0) ?></td>
                            </tr>
                            <tr>
                                <td>Tunjangan Jabatan</td>
                                <td class="text-end"><?= formatRupiah($penggajian['tunjangan_jabatan'] ?? 0) ?></td>
                            </tr>
                            <tr>
                                <td>Tunjangan Makan</td>
                                <td class="text-end"><?= formatRupiah($penggajian['tunjangan_makan'] ?? 0) ?></td>
                            </tr>
                            <tr>
                                <td>Tunjangan Transportasi</td>
                                <td class="text-end"><?= formatRupiah($penggajian['tunjangan_transport'] ?? 0) ?></td>
                            </tr>
                            <tr>
                                <td>Tunjangan Kesehatan (BPJS)</td>
                                <td class="text-end"><?= formatRupiah($penggajian['tunjangan_kesehatan'] ?? 0) ?></td>
                            </tr>
                            <?php if (!empty($penggajian['tunjangan_hari_raya']) && $penggajian['tunjangan_hari_raya'] > 0): ?>
                            <tr>
                                <td>Tunjangan Hari Raya (THR)</td>
                                <td class="text-end"><?= formatRupiah($penggajian['tunjangan_hari_raya'] ?? 0) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($penggajian['tunjangan_lainnya']) && $penggajian['tunjangan_lainnya'] > 0): ?>
                            <tr>
                                <td>Tunjangan Lainnya</td>
                                <td class="text-end"><?= formatRupiah($penggajian['tunjangan_lainnya'] ?? 0) ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td>Lembur</td>
                                <td class="text-end"><?= formatRupiah($penggajian['lembur'] ?? 0) ?> (<?= formatDecimal($penggajian['total_jam_lembur'] ?? 0, 1) ?> jam)</td>
                            </tr>
                            <?php if (!empty($penggajian['bonus_kinerja']) && $penggajian['bonus_kinerja'] > 0): ?>
                            <tr>
                                <td>Bonus Kinerja</td>
                                <td class="text-end"><?= formatRupiah($penggajian['bonus_kinerja'] ?? 0) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($penggajian['insentif_proyek']) && $penggajian['insentif_proyek'] > 0): ?>
                            <tr>
                                <td>Insentif Proyek</td>
                                <td class="text-end"><?= formatRupiah($penggajian['insentif_proyek'] ?? 0) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($penggajian['komisi_penjualan']) && $penggajian['komisi_penjualan'] > 0): ?>
                            <tr>
                                <td>Komisi Penjualan</td>
                                <td class="text-end"><?= formatRupiah($penggajian['komisi_penjualan'] ?? 0) ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr class="table-primary">
                                <td><strong>TOTAL PENGHASILAN</strong></td>
                                <td class="text-end fw-bold fs-5"><?= formatRupiah($penggajian['total_penghasilan'] ?? 0) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Komponen Potongan Card -->
            <div class="modern-card mb-4">
                <h6 class="mb-3">
                    <i class="fas fa-minus-circle me-2 text-danger"></i>
                    Komponen Potongan
                </h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td width="50%">Potongan BPJS Kesehatan</td>
                                <td width="50%" class="text-end"><?= formatRupiah($penggajian['potongan_bpjs_kesehatan'] ?? 0) ?></td>
                            </tr>
                            <tr>
                                <td>Potongan BPJS Ketenagakerjaan</td>
                                <td class="text-end"><?= formatRupiah($penggajian['potongan_bpjs_tenaga_kerja'] ?? 0) ?></td>
                            </tr>
                            <tr>
                                <td>Potongan PPh 21</td>
                                <td class="text-end"><?= formatRupiah($penggajian['potongan_pph21'] ?? 0) ?></td>
                            </tr>
                            <tr>
                                <td>Potongan Absensi (Terlambat/Alpha)</td>
                                <td class="text-end"><?= formatRupiah($penggajian['potongan_absensi'] ?? 0) ?></td>
                            </tr>
                            <?php if (!empty($penggajian['potongan_pinjaman']) && $penggajian['potongan_pinjaman'] > 0): ?>
                            <tr>
                                <td>Potongan Pinjaman/Kasbon</td>
                                <td class="text-end"><?= formatRupiah($penggajian['potongan_pinjaman'] ?? 0) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($penggajian['potongan_lainnya']) && $penggajian['potongan_lainnya'] > 0): ?>
                            <tr>
                                <td>Potongan Lainnya</td>
                                <td class="text-end"><?= formatRupiah($penggajian['potongan_lainnya'] ?? 0) ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr class="table-danger">
                                <td><strong>TOTAL POTONGAN</strong></td>
                                <td class="text-end fw-bold fs-5"><?= formatRupiah($penggajian['total_potongan'] ?? 0) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Ringkasan Akhir Card -->
            <div class="modern-card mb-4 bg-gradient-success text-white">
                <h6 class="mb-3 text-white">
                    <i class="fas fa-receipt me-2"></i>
                    Ringkasan Akhir
                </h6>
                <div class="row text-center">
                    <div class="col-6">
                        <p class="mb-1 text-white-50">Total Penghasilan</p>
                        <h4 class="mb-0"><?= formatRupiah($penggajian['total_penghasilan'] ?? 0) ?></h4>
                    </div>
                    <div class="col-6">
                        <p class="mb-1 text-white-50">Total Potongan</p>
                        <h4 class="mb-0"><?= formatRupiah($penggajian['total_potongan'] ?? 0) ?></h4>
                    </div>
                </div>
                <hr class="bg-white my-3">
                <div class="text-center">
                    <p class="mb-1 text-white-50">Gaji Bersih Diterima</p>
                    <h2 class="mb-0 fw-bold"><?= formatRupiah($penggajian['gaji_bersih'] ?? 0) ?></h2>
                </div>
            </div>

            <!-- Catatan Card -->
            <?php if (!empty($penggajian['catatan'])): ?>
            <div class="modern-card mb-4">
                <h6 class="mb-3">
                    <i class="fas fa-comment me-2 text-primary"></i>
                    Catatan
                </h6>
                <div class="p-3 bg-light rounded">
                    <?= nl2br(htmlspecialchars($penggajian['catatan'])) ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Informasi Pembayaran Card -->
            <?php if ($penggajian['status'] == 'paid'): ?>
            <div class="modern-card mb-4">
                <h6 class="mb-3">
                    <i class="fas fa-credit-card me-2 text-primary"></i>
                    Informasi Pembayaran
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <td width="40%">Metode Pembayaran</td>
                                <td><strong><?= $paymentMethods[$penggajian['payment_method']] ?? $penggajian['payment_method'] ?></strong></td>
                            </tr>
                            <tr>
                                <td>Referensi Pembayaran</td>
                                <td><strong><?= htmlspecialchars($penggajian['payment_reference'] ?? '-') ?></strong></td>
                            </tr>
                            <tr>
                                <td>Tanggal Pembayaran</td>
                                <td><strong><?= formatDateIndonesia($penggajian['paid_at'] ?? '') ?></strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>

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
                                <td><strong><?= htmlspecialchars($penggajian['created_by_name'] ?? '-') ?></strong></td>
                            </tr>
                            <tr>
                                <td>Dibuat pada</td>
                                <td><?= formatDateIndonesia($penggajian['created_at'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <td>Terakhir update</td>
                                <td><?= formatDateIndonesia($penggajian['updated_at'] ?? '') ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <td>
                                <td>Disetujui oleh</td>
                                <td><strong><?= htmlspecialchars($penggajian['approver_name'] ?? '-') ?></strong></td>
                            </tr>
                            <tr>
                                <td>Disetujui pada</td>
                                <td><?= formatDateIndonesia($penggajian['approved_at'] ?? '') ?></td>
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
    // History Chart
    const historyCanvas = document.getElementById('historyChart');
    <?php if (!empty($historyData)): ?>
    if (historyCanvas) {
        // Reverse data for chronological order (oldest to newest)
        const historyLabels = [
            <?php 
            $reversedHistory = array_reverse($historyData);
            foreach ($reversedHistory as $history): 
            ?>
            '<?= ($monthNames[$history['periode_bulan']] ?? $history['periode_bulan']) . ' \'' . substr($history['periode_tahun'], -2) ?>',
            <?php endforeach; ?>
        ];
        const historyGaji = [
            <?php foreach ($reversedHistory as $history): ?>
            <?= $history['gaji_bersih'] ?? 0 ?>,
            <?php endforeach; ?>
        ];
        
        new Chart(historyCanvas, {
            type: 'line',
            data: {
                labels: historyLabels,
                datasets: [{
                    label: 'Gaji Bersih',
                    data: historyGaji,
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
                                return 'Gaji: Rp ' + context.raw.toLocaleString('id-ID');
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
.bg-gradient-success {
    background: linear-gradient(135deg, #1cc88a, #13855c);
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
.table-bordered td, .table-bordered th {
    border-color: #e3e6f0;
}
.table-sm td {
    padding: 0.5rem;
}
.text-white-50 {
    color: rgba(255, 255, 255, 0.7);
}
@media print {
    .btn-modern-outline, .btn-modern-primary, .sidebar, .navbar, .btn {
        display: none !important;
    }
    .modern-card {
        box-shadow: none;
        border: 1px solid #ddd;
        page-break-inside: avoid;
    }
    .bg-gradient-success {
        background: #1cc88a !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}
</style>

<?= view('direktur/templates/footer', ['scripts' => $scripts ?? []]) ?>