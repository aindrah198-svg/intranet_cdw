<?php
$title = $title ?? 'Daftar Invoice';
$active = $active ?? 'invoice';

// Helper function untuk format currency
function formatRupiah($value) {
    return number_format($value, 0, ',', '.');
}
?>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header">
                <h1 class="display-5 fw-bold text-primary mb-3">
                    <i class="fas fa-file-invoice me-3"></i>
                    <?= $title ?>
                </h1>
                <p class="lead text-muted">
                    <?= $subtitle ?? 'Kelola invoice dan tagihan Anda' ?>
                    <span class="badge bg-primary ms-2"><?= count($invoiceList ?? []) ?> Invoice</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-primary border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted mb-2">Total Invoice</h6>
                            <h2 class="mb-0"><?= count($invoiceList ?? []) ?></h2>
                        </div>
                        <div class="icon-circle bg-primary text-white">
                            <i class="fas fa-file-invoice fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-warning border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted mb-2">Belum Bayar</h6>
                            <h2 class="mb-0"><?= $statusCount['belum_bayar'] ?? 0 ?></h2>
                        </div>
                        <div class="icon-circle bg-warning text-white">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-info border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted mb-2">Sebagian</h6>
                            <h2 class="mb-0"><?= $statusCount['sebagian'] ?? 0 ?></h2>
                        </div>
                        <div class="icon-circle bg-info text-white">
                            <i class="fas fa-hourglass-half fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-success border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted mb-2">Lunas</h6>
                            <h2 class="mb-0"><?= $statusCount['lunas'] ?? 0 ?></h2>
                        </div>
                        <div class="icon-circle bg-success text-white">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted mb-2">Total Nilai Invoice</h6>
                            <h3 class="mb-0 text-primary">Rp <?= formatRupiah($totalInvoiceValue ?? 0) ?></h3>
                        </div>
                        <div class="icon-circle bg-primary text-white">
                            <i class="fas fa-money-bill-wave fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted mb-2">Total Sudah Dibayar</h6>
                            <h3 class="mb-0 text-success">Rp <?= formatRupiah($totalPaid ?? 0) ?></h3>
                        </div>
                        <div class="icon-circle bg-success text-white">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted mb-2">Total Sisa Tagihan</h6>
                            <h3 class="mb-0 text-warning">Rp <?= formatRupiah($totalRemaining ?? 0) ?></h3>
                        </div>
                        <div class="icon-circle bg-warning text-white">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="card border-0 shadow-lg">
        <div class="card-header bg-light py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        Daftar Invoice
                    </h5>
                </div>
                <div class="d-flex gap-2">
                    <!-- Filter Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-filter me-1"></i> Filter Status
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= base_url('sales/invoice') ?>">Semua Status</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= base_url('sales/invoice?status=belum_bayar') ?>">Belum Bayar</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('sales/invoice?status=sebagian') ?>">Sebagian</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('sales/invoice?status=lunas') ?>">Lunas</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('sales/invoice?status=overdue') ?>">Overdue</a></li>
                        </ul>
                    </div>
                    
                    <!-- Add New Button -->
                    <a href="<?= base_url('sales/invoice/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-1"></i> Buat Invoice Baru
                    </a>
                </div>
            </div>
        </div>
        
        <div class="card-body p-0">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show m-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show m-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (empty($invoiceList)): ?>
                <!-- Empty State -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-file-invoice-dollar fa-5x text-muted mb-3"></i>
                    </div>
                    <h4 class="text-muted mb-3">Belum Ada Invoice</h4>
                    <p class="text-muted mb-4">Anda belum membuat invoice. Mulai dengan membuat invoice baru.</p>
                    <a href="<?= base_url('sales/invoice/create') ?>" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus-circle me-2"></i> Buat Invoice Pertama
                    </a>
                </div>
            <?php else: ?>
                <!-- Invoice Table -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">#</th>
                                <th>Nomor Invoice</th>
                                <th>Client</th>
                                <th>Project</th>
                                <th>Tanggal Invoice</th>
                                <th>Jatuh Tempo</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Total (Rp)</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($invoiceList as $index => $invoice): ?>
                            <?php 
                                // Status badge color
                                $statusColors = [
                                    'belum_bayar' => 'warning',
                                    'sebagian' => 'info',
                                    'lunas' => 'success',
                                    'overdue' => 'danger'
                                ];
                                $statusColor = $statusColors[$invoice['status_pembayaran']] ?? 'secondary';
                            ?>
                            <tr class="<?= $invoice['is_overdue'] ? 'table-danger' : '' ?>">
                                <td class="ps-4 fw-bold text-muted"><?= $index + 1 ?></td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <strong class="text-primary"><?= htmlspecialchars($invoice['nomor_invoice']) ?></strong>
                                        <small class="text-muted">
                                            <?php if (isset($invoice['nomor_penawaran']) && $invoice['nomor_penawaran']): ?>
                                            <i class="fas fa-file-contract me-1"></i>
                                            <?= htmlspecialchars($invoice['nomor_penawaran']) ?>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-primary text-white me-3">
                                            <?= strtoupper(substr($invoice['nama_perusahaan'] ?? 'C', 0, 1)) ?>
                                        </div>
                                        <div>
                                            <h6 class="mb-0"><?= htmlspecialchars($invoice['nama_perusahaan'] ?? 'N/A') ?></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span><?= htmlspecialchars($invoice['nama_project'] ?? 'N/A') ?></span>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($invoice['kode_project'] ?? '') ?>
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <?= date('d/m/Y', strtotime($invoice['tanggal_invoice'])) ?>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span><?= date('d/m/Y', strtotime($invoice['tanggal_jatuh_tempo'])) ?></span>
                                        <?php if ($invoice['is_overdue']): ?>
                                        <small class="text-danger fw-bold">
                                            <i class="fas fa-exclamation-triangle me-1"></i> Overdue
                                        </small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $statusColor ?>">
                                        <?php if ($invoice['status_pembayaran'] == 'sebagian'): ?>
                                            Sebagian (<?= $invoice['payment_percentage'] ?>%)
                                        <?php else: ?>
                                            <?= ucfirst(str_replace('_', ' ', $invoice['status_pembayaran'])) ?>
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex flex-column">
                                        <strong class="text-dark"><?= $invoice['total_formatted'] ?></strong>
                                        <?php if ($invoice['status_pembayaran'] == 'sebagian'): ?>
                                        <small class="text-muted">
                                            Tersisa: <?= $invoice['remaining_formatted'] ?>
                                        </small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="<?= base_url('sales/invoice/detail/' . $invoice['id']) ?>" 
                                           class="btn btn-outline-info" 
                                           data-bs-toggle="tooltip" 
                                           title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($invoice['status_pembayaran'] == 'belum_bayar'): ?>
                                        <a href="<?= base_url('sales/invoice/edit/' . $invoice['id']) ?>" 
                                           class="btn btn-outline-warning" 
                                           data-bs-toggle="tooltip" 
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php endif; ?>
                                        <a href="<?= base_url('sales/invoice/print/' . $invoice['id']) ?>" 
                                           target="_blank"
                                           class="btn btn-outline-secondary" 
                                           data-bs-toggle="tooltip" 
                                           title="Print">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <?php if ($invoice['status_pembayaran'] == 'belum_bayar'): ?>
                                        <form action="<?= base_url('sales/invoice/delete/' . $invoice['id']) ?>" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus invoice ini?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-danger">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Table Footer -->
                <div class="card-footer bg-light py-3">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <p class="mb-0 text-muted">
                                Menampilkan <strong><?= count($invoiceList) ?></strong> invoice
                                <br>
                                <small>Total nilai: <strong>Rp <?= formatRupiah($totalInvoiceValue ?? 0) ?></strong> | 
                                Sudah dibayar: <strong>Rp <?= formatRupiah($totalPaid ?? 0) ?></strong> | 
                                Sisa tagihan: <strong>Rp <?= formatRupiah($totalRemaining ?? 0) ?></strong></small>
                            </p>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-secondary" onclick="window.location.reload()">
                                    <i class="fas fa-sync-alt me-1"></i> Refresh
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Info -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title mb-3"><i class="fas fa-info-circle me-2"></i>Informasi Status Invoice</h6>
                    <div class="row">
                        <div class="col-md-3 mb-3 mb-md-0">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-warning me-2">Belum Bayar</span>
                                <span class="text-muted small">Invoice baru dibuat</span>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-info me-2">Sebagian</span>
                                <span class="text-muted small">Sudah ada pembayaran</span>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-success me-2">Lunas</span>
                                <span class="text-muted small">Pembayaran lengkap</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-danger me-2">Overdue</span>
                                <span class="text-muted small">Lewat jatuh tempo</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1rem;
}

.icon-circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card {
    border-radius: 15px;
    overflow: hidden;
}

.table th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.8rem;
    color: #6c757d;
    border-bottom: 2px solid #dee2e6;
}

.table td {
    vertical-align: middle;
    border-bottom: 1px solid #f0f0f0;
}

.table tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.05);
}

.btn-group .btn {
    border-radius: 0.375rem !important;
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}
</style>

<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
        alerts.forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
</script>