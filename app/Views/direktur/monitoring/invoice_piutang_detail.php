<?php
// Data dari controller
$invoice = $invoice ?? [];
$payments = $payments ?? [];
$statusOptions = $statusOptions ?? [];
$metodeOptions = $metodeOptions ?? [];

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

$statusInfo = $statusOptions[$invoice['status']] ?? ['label' => $invoice['status'], 'class' => 'secondary'];
$isOverdue = ($invoice['status'] != 'paid' && $invoice['status'] != 'cancelled' && 
              strtotime($invoice['tanggal_jatuh_tempo']) < strtotime(date('Y-m-d')));
?>


<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-gradient">Detail Invoice & Piutang</h4>
            <p class="text-muted mb-0">
                <a href="<?= base_url('direktur/monitoring/invoice-piutang') ?>" class="text-decoration-none">
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
        <!-- Left Column - Invoice Summary -->
        <div class="col-lg-5">
            <!-- Invoice Info Card -->
            <div class="modern-card mb-4">
                <h6 class="mb-3">
                    <i class="fas fa-file-invoice me-2 text-primary"></i>
                    Informasi Invoice
                </h6>
                <div class="text-center mb-3">
                    <div class="invoice-number mb-2">
                        <span class="badge bg-primary fs-6 px-4 py-2"><?= htmlspecialchars($invoice['nomor_invoice']) ?></span>
                    </div>
                    <span class="badge bg-<?= getStatusBadgeClass($invoice['status']) ?> fs-6 px-3 py-2">
                        <?= getStatusLabel($invoice['status']) ?>
                    </span>
                    <?php if ($isOverdue): ?>
                    <span class="badge bg-danger fs-6 px-3 py-2 ms-2">
                        <i class="fas fa-exclamation-triangle me-1"></i>Overdue
                    </span>
                    <?php endif; ?>
                </div>
                <hr>
                <div class="row mb-2">
                    <div class="col-5 text-muted">Tanggal Invoice</div>
                    <div class="col-7 fw-bold"><?= formatDate($invoice['tanggal_invoice'] ?? '') ?></div>
                </div>
                <div class="row mb-2">
                    <div class="col-5 text-muted">Jatuh Tempo</div>
                    <div class="col-7 fw-bold <?= $isOverdue ? 'text-danger' : '' ?>">
                        <?= formatDate($invoice['tanggal_jatuh_tempo'] ?? '') ?>
                        <?php if ($isOverdue): ?>
                        <i class="fas fa-clock ms-1"></i>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-5 text-muted">Client</div>
                    <div class="col-7">
                        <strong><?= htmlspecialchars($invoice['nama_perusahaan'] ?? '-') ?></strong><br>
                        <small class="text-muted"><?= htmlspecialchars($invoice['nama_kontak'] ?? '-') ?></small>
                    </div>
                </div>
                <?php if (!empty($invoice['telepon'])): ?>
                <div class="row mb-2">
                    <div class="col-5 text-muted">Telepon</div>
                    <div class="col-7"><?= htmlspecialchars($invoice['telepon']) ?></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($invoice['email_client'])): ?>
                <div class="row mb-2">
                    <div class="col-5 text-muted">Email</div>
                    <div class="col-7"><?= htmlspecialchars($invoice['email_client']) ?></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($invoice['alamat'])): ?>
                <div class="row mb-2">
                    <div class="col-5 text-muted">Alamat</div>
                    <div class="col-7"><?= nl2br(htmlspecialchars(substr($invoice['alamat'], 0, 100))) ?></div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Financial Summary Card -->
            <div class="modern-card mb-4">
                <h6 class="mb-3">
                    <i class="fas fa-chart-line me-2 text-primary"></i>
                    Ringkasan Keuangan
                </h6>
                <div class="text-center mb-4">
                    <div class="total-circle mx-auto mb-2" style="position: relative; width: 140px; height: 140px;">
                        <?php 
                        $total = floatval($invoice['total'] ?? 0);
                        $sisa = floatval($invoice['sisa_piutang'] ?? 0);
                        $persenDibayar = $total > 0 ? (($total - $sisa) / $total) * 100 : 0;
                        ?>
                        <div class="border rounded-circle bg-light d-flex align-items-center justify-content-center flex-column"
                             style="width: 140px; height: 140px; border-width: 3px !important; border-color: <?= $sisa > 0 ? '#f6c23e' : '#1cc88a' ?> !important;">
                            <span class="fs-6 fw-bold text-muted">Sisa Piutang</span>
                            <span class="fs-5 fw-bold <?= $sisa > 0 ? 'text-warning' : 'text-success' ?>">
                                <?= formatRupiah($sisa) ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="financial-details">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Total Invoice</span>
                            <span class="fw-bold text-primary"><?= formatRupiah($total) ?></span>
                        </div>
                        <div class="progress mt-1" style="height: 6px;">
                            <div class="progress-bar bg-primary" style="width: 100%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Sudah Dibayar</span>
                            <span class="fw-bold text-success"><?= formatRupiah($total - $sisa) ?></span>
                        </div>
                        <div class="progress mt-1" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: <?= $persenDibayar ?>%"></div>
                        </div>
                        <small class="text-muted"><?= formatNumber($persenDibayar, 1) ?>% dari total</small>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Sisa Piutang</span>
                            <span class="fw-bold <?= $sisa > 0 ? 'text-warning' : 'text-success' ?>">
                                <?= formatRupiah($sisa) ?>
                            </span>
                        </div>
                        <div class="progress mt-1" style="height: 6px;">
                            <div class="progress-bar bg-warning" style="width: <?= $persenDibayar ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Summary Card -->
            <div class="modern-card">
                <h6 class="mb-3">
                    <i class="fas fa-credit-card me-2 text-primary"></i>
                    Ringkasan Pembayaran
                </h6>
                <div class="text-center">
                    <div class="row">
                        <div class="col-6">
                            <h3 class="mb-0 text-success"><?= number_format(count($payments)) ?></h3>
                            <small class="text-muted">Transaksi</small>
                        </div>
                        <div class="col-6">
                            <h3 class="mb-0 text-primary"><?= formatRupiah($total - $sisa) ?></h3>
                            <small class="text-muted">Total Dibayar</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Detail Data -->
        <div class="col-lg-7">
            <!-- Deskripsi Card -->
            <?php if (!empty($invoice['deskripsi'])): ?>
            <div class="modern-card mb-4">
                <h6 class="mb-3">
                    <i class="fas fa-align-left me-2 text-primary"></i>
                    Deskripsi
                </h6>
                <div class="p-3 bg-light rounded">
                    <?= nl2br(htmlspecialchars($invoice['deskripsi'])) ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Detail Tagihan Card -->
            <div class="modern-card mb-4">
                <h6 class="mb-3">
                    <i class="fas fa-receipt me-2 text-primary"></i>
                    Detail Tagihan
                </h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr class="table-light">
                                <td width="60%">Subtotal</td>
                                <td width="40%" class="text-end"><?= formatRupiah($invoice['subtotal'] ?? 0) ?></td>
                            </tr>
                            <tr>
                                <td>PPN (11%)</td>
                                <td class="text-end"><?= formatRupiah($invoice['ppn'] ?? 0) ?></td>
                            </tr>
                            <tr class="table-primary">
                                <td><strong>TOTAL INVOICE</strong></td>
                                <td class="text-end fw-bold fs-5"><?= formatRupiah($invoice['total'] ?? 0) ?></td>
                            </tr>
                            <tr class="table-success">
                                <td><strong>Total Dibayar</strong></td>
                                <td class="text-end fw-bold text-success"><?= formatRupiah(($invoice['total'] ?? 0) - ($invoice['sisa_piutang'] ?? 0)) ?></td>
                            </tr>
                            <tr class="table-warning">
                                <td><strong>SISA PIUTANG</strong></td>
                                <td class="text-end fw-bold text-warning fs-5"><?= formatRupiah($invoice['sisa_piutang'] ?? 0) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- History Pembayaran Card -->
            <div class="modern-card mb-4">
                <h6 class="mb-3">
                    <i class="fas fa-history me-2 text-primary"></i>
                    History Pembayaran
                </h6>
                <?php if (empty($payments)): ?>
                <div class="text-center py-4">
                    <i class="fas fa-credit-card fa-3x text-muted mb-3 d-block"></i>
                    <p class="text-muted">Belum ada pembayaran</p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Nomor Pembayaran</th>
                                <th>Jumlah</th>
                                <th>Metode</th>
                                <th>Referensi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td><?= formatDate($payment['tanggal_bayar'] ?? '') ?></td>
                                <td><span class="fw-bold"><?= htmlspecialchars($payment['nomor_pembayaran'] ?? '-') ?></span></td>
                                <td class="text-success fw-bold"><?= formatRupiah($payment['jumlah_bayar'] ?? 0) ?></td>
                                <td>
                                    <span class="badge bg-<?= $metodeOptions[$payment['metode_bayar']]['class'] ?? 'secondary' ?>">
                                        <?= $metodeOptions[$payment['metode_bayar']]['label'] ?? $payment['metode_bayar'] ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($payment['nomor_referensi'] ?? '-') ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="table-light">
                                <td colspan="2" class="text-end fw-bold">TOTAL</td>
                                <td class="text-end fw-bold text-success"><?= formatRupiah(($invoice['total'] ?? 0) - ($invoice['sisa_piutang'] ?? 0)) ?></td>
                                <td colspan="2"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>

            <!-- Catatan Card -->
            <?php if (!empty($invoice['keterangan'])): ?>
            <div class="modern-card mb-4">
                <h6 class="mb-3">
                    <i class="fas fa-comment me-2 text-primary"></i>
                    Catatan
                </h6>
                <div class="p-3 bg-light rounded">
                    <?= nl2br(htmlspecialchars($invoice['keterangan'])) ?>
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
                                <td><strong><?= htmlspecialchars($invoice['created_by_name'] ?? '-') ?></strong></td>
                            </tr>
                            <tr>
                                <td>Dibuat pada</td>
                                <td><?= formatDateIndonesia($invoice['created_at'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <td>Terakhir update</td>
                                <td><?= formatDateIndonesia($invoice['updated_at'] ?? '') ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <td width="40%">Status</td>
                                <td><span class="badge bg-<?= getStatusBadgeClass($invoice['status']) ?>">
                                    <?= getStatusLabel($invoice['status']) ?>
                                </span></td>
                            </tr>
                            <?php if ($invoice['status'] == 'paid' && !empty($invoice['paid_at'])): ?>
                            <tr>
                                <td>Tanggal Lunas</td>
                                <td><?= formatDateIndonesia($invoice['paid_at'] ?? '') ?></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
.table-bordered td, .table-bordered th {
    border-color: #e3e6f0;
}
.table-sm td {
    padding: 0.5rem;
}
.invoice-number .badge {
    font-size: 1rem;
    font-weight: 600;
    letter-spacing: 1px;
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
}
</style>

<?= view('direktur/templates/footer', ['scripts' => $scripts ?? []]) ?>