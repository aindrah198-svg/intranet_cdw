<?php
$title = $title ?? 'Detail Invoice';
$active = $active ?? 'invoice';

// Format currency
function formatRupiah($value) {
    return number_format($value, 0, ',', '.');
}

// Status badge color
$statusColors = [
    'belum_bayar' => 'warning',
    'sebagian' => 'info',
    'lunas' => 'success',
    'overdue' => 'danger'
];
$statusColor = $statusColors[$invoice['status_pembayaran']] ?? 'secondary';

// Calculate totals
$ppn = $total * 0.11;
$grandTotal = $total + $ppn;

// Check if overdue
$isOverdue = (strtotime($invoice['tanggal_jatuh_tempo']) < time() && $invoice['status_pembayaran'] != 'lunas');

// Calculate days overdue
$daysOverdue = $isOverdue ? floor((time() - strtotime($invoice['tanggal_jatuh_tempo'])) / (60 * 60 * 24)) : 0;

// Payment progress percentage
$paymentPercentage = $total > 0 ? round(($paymentSummary / $total) * 100, 1) : 0;

// Get payment method colors
$methodColors = [
    'transfer' => 'primary',
    'tunai' => 'success',
    'cek' => 'warning',
    'giro' => 'info'
];
?>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h1 class="display-5 fw-bold text-primary mb-3">
                            <i class="fas fa-file-invoice me-3"></i>
                            <?= $title ?>
                        </h1>
                        <p class="lead text-muted">
                            <?= $subtitle ?? 'Informasi lengkap invoice' ?>
                        </p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-<?= $statusColor ?> fs-6 px-3 py-2 mb-2">
                            <i class="fas fa-circle me-1"></i>
                            <?= ucfirst(str_replace('_', ' ', $invoice['status_pembayaran'])) ?>
                        </span>
                        <?php if ($isOverdue): ?>
                            <span class="badge bg-danger fs-6 px-3 py-2">
                                <i class="fas fa-exclamation-triangle me-1"></i> 
                                Overdue: <?= $daysOverdue ?> hari
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Payment Summary Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-5 border-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                Total Invoice
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                Rp <?= formatRupiah($grandTotal) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-5 border-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                Sudah Dibayar
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                Rp <?= formatRupiah($paymentSummary) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-5 border-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-danger text-uppercase mb-1">
                                Sisa Tagihan
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                Rp <?= formatRupiah($remaining) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-5 border-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">
                                Progress Pembayaran
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 fw-bold text-gray-800">
                                        <?= $paymentPercentage ?>%
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" 
                                             role="progressbar" 
                                             style="width: <?= $paymentPercentage ?>%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Left Column - Invoice Details -->
        <div class="col-lg-8 mb-4">
            <!-- Invoice & Client Information -->
            <div class="row mb-4">
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow h-100">
                        <div class="card-header bg-light py-3">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Informasi Invoice
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th width="40%"><small>Nomor Invoice</small></th>
                                    <td><small><?= htmlspecialchars($invoice['nomor_invoice']) ?></small></td>
                                </tr>
                                <tr>
                                    <th><small>Tanggal Invoice</small></th>
                                    <td><small><?= date('d/m/Y', strtotime($invoice['tanggal_invoice'])) ?></small></td>
                                </tr>
                                <tr>
                                    <th><small>Jatuh Tempo</small></th>
                                    <td>
                                        <small>
                                            <?= date('d/m/Y', strtotime($invoice['tanggal_jatuh_tempo'])) ?>
                                            <?php if ($isOverdue): ?>
                                                <span class="badge bg-danger ms-2">
                                                    <?= $daysOverdue ?> hari
                                                </span>
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                </tr>
                                <tr>
                                    <th><small>Metode Pembayaran</small></th>
                                    <td>
                                        <small>
                                            <?php if ($invoice['metode_pembayaran']): ?>
                                                <span class="badge bg-<?= $methodColors[$invoice['metode_pembayaran']] ?? 'secondary' ?>">
                                                    <?= ucfirst($invoice['metode_pembayaran']) ?>
                                                </span>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                </tr>
                                <?php if ($invoice['nomor_penawaran']): ?>
                                <tr>
                                    <th><small>No. Penawaran</small></th>
                                    <td><small><?= htmlspecialchars($invoice['nomor_penawaran']) ?></small></td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <th><small>Dibuat Oleh</small></th>
                                    <td><small><?= htmlspecialchars($invoice['created_by_name'] ?? 'System') ?></small></td>
                                </tr>
                            </table>
                            
                            <?php if ($invoice['keterangan']): ?>
                            <div class="mt-3 p-2 bg-light rounded">
                                <small class="text-muted d-block mb-1">
                                    <i class="fas fa-sticky-note me-1"></i> Keterangan:
                                </small>
                                <small><?= nl2br(htmlspecialchars($invoice['keterangan'])) ?></small>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow h-100">
                        <div class="card-header bg-light py-3">
                            <h5 class="mb-0">
                                <i class="fas fa-user-tie me-2"></i>
                                Informasi Client
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th width="40%"><small>Perusahaan</small></th>
                                    <td><small><?= htmlspecialchars($invoice['nama_perusahaan']) ?></small></td>
                                </tr>
                                <tr>
                                    <th><small>Contact Person</small></th>
                                    <td><small><?= htmlspecialchars($invoice['nama_kontak'] ?? '-') ?></small></td>
                                </tr>
                                <tr>
                                    <th><small>Telepon</small></th>
                                    <td><small><?= htmlspecialchars($invoice['telepon'] ?? '-') ?></small></td>
                                </tr>
                                <tr>
                                    <th><small>Email</small></th>
                                    <td><small><?= htmlspecialchars($invoice['email'] ?? '-') ?></small></td>
                                </tr>
                                <tr>
                                    <th><small>Alamat</small></th>
                                    <td><small><?= nl2br(htmlspecialchars($invoice['alamat_client'] ?? '-')) ?></small></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Project Information -->
            <div class="card border-0 shadow mb-4">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-project-diagram me-2"></i>
                        Informasi Project
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th width="40%"><small>Nama Project</small></th>
                                    <td><small><?= htmlspecialchars($invoice['nama_project']) ?></small></td>
                                </tr>
                                <tr>
                                    <th><small>Kode Project</small></th>
                                    <td><small><?= htmlspecialchars($invoice['kode_project'] ?? '-') ?></small></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th width="40%"><small>Nilai Project</small></th>
                                    <td><small>Rp <?= formatRupiah($invoice['nilai_project'] ?? 0) ?></small></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="card border-0 shadow mb-4">
                <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        Detail Item Invoice
                    </h5>
                    <small class="text-muted"><?= count($items) ?> item</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4" width="5%">No</th>
                                    <th width="35%">Item</th>
                                    <th class="text-center" width="10%">Qty</th>
                                    <th class="text-center" width="10%">Satuan</th>
                                    <th class="text-end" width="20%">Harga Satuan</th>
                                    <th class="text-end pe-4" width="20%">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $index => $item): ?>
                                <tr>
                                    <td class="ps-4 align-middle"><?= $index + 1 ?></td>
                                    <td class="align-middle">
                                        <div>
                                            <strong class="d-block"><?= htmlspecialchars($item['nama_item']) ?></strong>
                                            <?php if ($item['deskripsi']): ?>
                                            <small class="text-muted"><?= nl2br(htmlspecialchars($item['deskripsi'])) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle"><?= number_format($item['qty'], 2) ?></td>
                                    <td class="text-center align-middle"><?= htmlspecialchars($item['satuan']) ?></td>
                                    <td class="text-end align-middle"><?= formatRupiah($item['harga_satuan']) ?></td>
                                    <td class="text-end pe-4 align-middle fw-bold"><?= formatRupiah($item['subtotal']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="5" class="text-end fw-bold ps-4">SUB TOTAL</td>
                                    <td class="text-end pe-4 fw-bold"><?= formatRupiah($total) ?></td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end fw-bold ps-4">PPN 11%</td>
                                    <td class="text-end pe-4 fw-bold"><?= formatRupiah($ppn) ?></td>
                                </tr>
                                <tr class="table-primary">
                                    <td colspan="5" class="text-end fw-bold fs-5 ps-4">GRAND TOTAL</td>
                                    <td class="text-end pe-4 fw-bold fs-5"><?= formatRupiah($grandTotal) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Actions & Payments -->
        <div class="col-lg-4 mb-4">
            <!-- Actions Card -->
            <div class="card border-0 shadow mb-4">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-cogs me-2"></i>
                        Aksi Invoice
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <!-- Quick Action: Add Payment -->
                        <?php if ($remaining > 0): ?>
                        <button type="button" class="btn btn-success mb-2" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                            <i class="fas fa-plus-circle me-2"></i> Tambah Pembayaran
                        </button>
                        <?php else: ?>
                        <button class="btn btn-success mb-2" disabled>
                            <i class="fas fa-check-circle me-2"></i> Invoice Lunas
                        </button>
                        <?php endif; ?>
                        
                        <!-- Print & Export -->
                        <div class="btn-group w-100 mb-2" role="group">
                            <a href="<?= base_url('sales/invoice/print/' . $invoice['id']) ?>" 
                               target="_blank"
                               class="btn btn-outline-primary">
                                <i class="fas fa-print me-2"></i> Print
                            </a>
                            <a href="<?= base_url('sales/invoice/export-excel/' . $invoice['id']) ?>" 
                               class="btn btn-outline-success">
                                <i class="fas fa-file-excel me-2"></i> Excel
                            </a>
                            <a href="<?= base_url('sales/invoice/export-pdf/' . $invoice['id']) ?>" 
                               class="btn btn-outline-danger">
                                <i class="fas fa-file-pdf me-2"></i> PDF
                            </a>
                        </div>
                        
                        <!-- Edit Button -->
                        <?php if (in_array($invoice['status_pembayaran'], ['belum_bayar', 'sebagian'])): ?>
                        <a href="<?= base_url('sales/invoice/edit/' . $invoice['id']) ?>" 
                           class="btn btn-warning mb-2">
                            <i class="fas fa-edit me-2"></i> Edit Invoice
                        </a>
                        <?php endif; ?>
                        
                        <!-- Status Update -->
                        <div class="dropdown mb-2">
                            <button class="btn btn-outline-info dropdown-toggle w-100" type="button" 
                                    data-bs-toggle="dropdown">
                                <i class="fas fa-sync-alt me-2"></i> Update Status
                            </button>
                            <ul class="dropdown-menu w-100">
                                <?php 
                                $statusOptions = [
                                    'belum_bayar' => ['label' => 'Belum Bayar', 'color' => 'warning'],
                                    'sebagian' => ['label' => 'Sebagian', 'color' => 'info'],
                                    'lunas' => ['label' => 'Lunas', 'color' => 'success'],
                                    'overdue' => ['label' => 'Overdue', 'color' => 'danger']
                                ];
                                
                                foreach ($statusOptions as $value => $option):
                                    if ($value == $invoice['status_pembayaran']) continue;
                                ?>
                                <li>
                                    <form action="<?= base_url('sales/invoice/update-status/' . $invoice['id']) ?>" 
                                          method="POST" class="d-inline w-100">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="status" value="<?= $value ?>">
                                        <button type="submit" class="dropdown-item">
                                            <span class="badge bg-<?= $option['color'] ?> me-2">●</span> 
                                            <?= $option['label'] ?>
                                        </button>
                                    </form>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        
                        <!-- Navigation -->
                        <div class="btn-group w-100" role="group">
                            <a href="<?= base_url('sales/invoice') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-list me-2"></i> Daftar
                            </a>
                            <a href="javascript:history.back()" class="btn btn-outline-dark">
                                <i class="fas fa-arrow-left me-2"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="card border-0 shadow mb-4">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-credit-card me-2"></i>
                        Informasi Pembayaran
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-success">
                        <h6 class="alert-heading mb-2">
                            <i class="fas fa-bank me-2"></i>Bank Transfer
                        </h6>
                        <p class="mb-1"><strong>Bank:</strong> Mandiri</p>
                        <p class="mb-1"><strong>No. Rekening:</strong> 101.000.676.6073</p>
                        <p class="mb-0"><strong>Atas Nama:</strong> PT. CIPTA DUTA WACANA</p>
                    </div>
                    
                    <!-- Terbilang -->
                    <div class="border p-3 rounded bg-light">
                        <h6 class="mb-2 text-primary">
                            <i class="fas fa-file-alt me-2"></i>Terbilang
                        </h6>
                        <p class="mb-0 fst-italic text-dark">
                            <?php
                            function terbilang($angka) {
                                $angka = abs($angka);
                                $baca = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
                                $terbilang = "";
                                
                                if ($angka < 12) $terbilang = " " . $baca[$angka];
                                elseif ($angka < 20) $terbilang = terbilang($angka - 10) . " Belas";
                                elseif ($angka < 100) $terbilang = terbilang($angka / 10) . " Puluh" . terbilang($angka % 10);
                                elseif ($angka < 200) $terbilang = " Seratus" . terbilang($angka - 100);
                                elseif ($angka < 1000) $terbilang = terbilang($angka / 100) . " Ratus" . terbilang($angka % 100);
                                elseif ($angka < 2000) $terbilang = " Seribu" . terbilang($angka - 1000);
                                elseif ($angka < 1000000) $terbilang = terbilang($angka / 1000) . " Ribu" . terbilang($angka % 1000);
                                elseif ($angka < 1000000000) $terbilang = terbilang($angka / 1000000) . " Juta" . terbilang($angka % 1000000);
                                
                                return trim($terbilang);
                            }
                            ?>
                            <strong><?= terbilang($grandTotal) ?> Rupiah</strong>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Payment History -->
            <div class="card border-0 shadow">
                <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>
                        Riwayat Pembayaran
                    </h5>
                    <small class="text-muted"><?= count($payments) ?> transaksi</small>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($payments)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Belum ada riwayat pembayaran</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                            <?php foreach ($payments as $payment): ?>
                            <div class="list-group-item border-0">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-0 text-primary"><?= htmlspecialchars($payment['nomor_pembayaran']) ?></h6>
                                        <small class="text-muted"><?= date('d/m/Y', strtotime($payment['tanggal_bayar'])) ?></small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-<?= $methodColors[$payment['metode_bayar']] ?? 'secondary' ?>">
                                            <?= ucfirst($payment['metode_bayar']) ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-success fs-5">
                                        Rp <?= formatRupiah($payment['jumlah_bayar']) ?>
                                    </span>
                                    <?php if ($payment['bank']): ?>
                                    <small class="text-muted"><?= $payment['bank'] ?></small>
                                    <?php endif; ?>
                                </div>
                                <?php if ($payment['no_referensi']): ?>
                                <small class="text-muted d-block">
                                    <i class="fas fa-hashtag me-1"></i> Ref: <?= htmlspecialchars($payment['no_referensi']) ?>
                                </small>
                                <?php endif; ?>
                                <?php if ($payment['keterangan']): ?>
                                <small class="text-muted d-block mt-1">
                                    <i class="fas fa-comment me-1"></i> <?= htmlspecialchars($payment['keterangan']) ?>
                                </small>
                                <?php endif; ?>
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-user me-1"></i> <?= htmlspecialchars($payment['created_by_name'] ?? '-') ?>
                                </small>
                                
                                <!-- Delete Payment Button -->
                                <?php if ($remaining > 0): ?>
                                <form action="<?= base_url('sales/invoice/delete-payment/' . $payment['id']) ?>" 
                                      method="POST" 
                                      class="mt-2"
                                      onsubmit="return confirm('Hapus pembayaran ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash me-1"></i> Hapus
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Pembayaran -->
<div class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPaymentModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Tambah Pembayaran
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('sales/invoice/add-payment/' . $invoice['id']) ?>" method="POST" id="paymentForm">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <div class="d-flex justify-content-between">
                            <div>
                                <i class="fas fa-info-circle me-2"></i>
                                Sisa tagihan:
                            </div>
                            <strong>Rp <?= formatRupiah($remaining) ?></strong>
                        </div>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="tanggal_bayar" class="form-label">Tanggal Bayar <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal_bayar" name="tanggal_bayar" 
                                   value="<?= date('Y-m-d') ?>" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="jumlah_bayar" class="form-label">Jumlah Bayar (Rp) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="jumlah_bayar" name="jumlah_bayar" 
                                   value="<?= formatRupiah($remaining) ?>" required>
                            <div class="form-text">Maks: Rp <?= formatRupiah($remaining) ?></div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="metode_bayar" class="form-label">Metode Bayar <span class="text-danger">*</span></label>
                            <select class="form-select" id="metode_bayar" name="metode_bayar" required>
                                <option value="">Pilih Metode</option>
                                <option value="transfer">Transfer Bank</option>
                                <option value="tunai">Tunai</option>
                                <option value="cek">Cek</option>
                                <option value="giro">Giro</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="bank" class="form-label">Bank (Opsional)</label>
                            <input type="text" class="form-control" id="bank" name="bank" placeholder="Nama bank">
                        </div>
                        
                        <div class="col-md-12">
                            <label for="no_referensi" class="form-label">No. Referensi (Opsional)</label>
                            <input type="text" class="form-control" id="no_referensi" name="no_referensi" 
                                   placeholder="No. transfer/cek/giro">
                        </div>
                        
                        <div class="col-md-12">
                            <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="2" 
                                      placeholder="Catatan pembayaran"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="submitPaymentBtn">
                        <i class="fas fa-save me-1"></i> Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 10px;
    overflow: hidden;
}

.table th {
    font-weight: 600;
    font-size: 0.85rem;
}

.border-start {
    border-left-width: 4px !important;
}

.progress {
    height: 8px;
    border-radius: 4px;
}

.list-group-item {
    border-left: none;
    border-right: none;
    padding: 1rem;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

.btn-group .btn {
    border-radius: 6px !important;
}

.modal-content {
    border-radius: 10px;
    border: none;
}

.form-control, .form-select {
    border-radius: 6px;
    border: 1px solid #dee2e6;
}

.form-control:focus, .form-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.badge {
    font-size: 0.75em;
    padding: 0.35em 0.65em;
}

.text-xs {
    font-size: 0.7rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Format currency
    function formatCurrency(value) {
        if (!value && value !== 0) return '0';
        return new Intl.NumberFormat('id-ID').format(value);
    }
    
    function parseCurrency(value) {
        if (!value) return 0;
        return parseFloat(value.toString().replace(/[^\d]/g, '')) || 0;
    }
    
    // Currency formatting for payment modal
    const jumlahBayarInput = document.getElementById('jumlah_bayar');
    if (jumlahBayarInput) {
        // Format on load
        jumlahBayarInput.value = formatCurrency(parseCurrency(jumlahBayarInput.value));
        
        jumlahBayarInput.addEventListener('focus', function() {
            this.value = parseCurrency(this.value);
        });
        
        jumlahBayarInput.addEventListener('input', function() {
            this.value = formatCurrency(parseCurrency(this.value));
        });
        
        jumlahBayarInput.addEventListener('blur', function() {
            const maxAmount = <?= $remaining ?>;
            const enteredAmount = parseCurrency(this.value);
            
            if (enteredAmount > maxAmount) {
                alert('Jumlah pembayaran melebihi sisa tagihan (Rp ' + formatCurrency(maxAmount) + ')');
                this.value = formatCurrency(maxAmount);
            }
        });
    }
    
    // Form validation
    const paymentForm = document.getElementById('paymentForm');
    if (paymentForm) {
        paymentForm.addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitPaymentBtn');
            const originalText = submitBtn.innerHTML;
            
            // Show loading
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';
            submitBtn.disabled = true;
            
            // Format jumlah bayar
            if (jumlahBayarInput) {
                jumlahBayarInput.value = parseCurrency(jumlahBayarInput.value);
            }
            
            // Validate amount
            const amount = parseCurrency(jumlahBayarInput.value);
            const maxAmount = <?= $remaining ?>;
            
            if (amount > maxAmount) {
                e.preventDefault();
                alert('Jumlah pembayaran melebihi sisa tagihan');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                jumlahBayarInput.value = formatCurrency(maxAmount);
                return false;
            }
            
            if (amount <= 0) {
                e.preventDefault();
                alert('Jumlah pembayaran harus lebih dari 0');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                return false;
            }
            
            // Restore button after 3 seconds (in case of error)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 3000);
            
            return true;
        });
    }
    
    // Auto-close alerts after 5 seconds
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
    
    // Highlight current status in dropdown
    document.querySelectorAll('.dropdown-item').forEach(item => {
        const form = item.querySelector('form');
        if (form) {
            const statusInput = form.querySelector('input[name="status"]');
            if (statusInput && statusInput.value === '<?= $invoice["status_pembayaran"] ?>') {
                item.style.display = 'none';
            }
        }
    });
});
</script>