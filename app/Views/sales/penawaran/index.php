<?php
$title = $title ?? 'Daftar Penawaran Harga';
$active = $active ?? 'penawaran';
?>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header">
                <h1 class="display-5 fw-bold text-primary mb-3">
                    <i class="fas fa-file-invoice-dollar me-3"></i>
                    <?= $title ?>
                </h1>
                <p class="lead text-muted">
                    <?= $subtitle ?? 'Kelola penawaran harga Anda' ?>
                    <span class="badge bg-primary ms-2"><?= count($penawaranList ?? []) ?> Penawaran</span>
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
                            <h6 class="text-uppercase text-muted mb-2">Total</h6>
                            <h2 class="mb-0"><?= count($penawaranList ?? []) ?></h2>
                        </div>
                        <div class="icon-circle bg-primary text-white">
                            <i class="fas fa-file-alt fa-2x"></i>
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
                            <h6 class="text-uppercase text-muted mb-2">Draft</h6>
                            <h2 class="mb-0"><?= count(array_filter($penawaranList ?? [], function($p) { return $p['status'] == 'draft'; })) ?></h2>
                        </div>
                        <div class="icon-circle bg-warning text-white">
                            <i class="fas fa-edit fa-2x"></i>
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
                            <h6 class="text-uppercase text-muted mb-2">Sent</h6>
                            <h2 class="mb-0"><?= count(array_filter($penawaranList ?? [], function($p) { return $p['status'] == 'sent'; })) ?></h2>
                        </div>
                        <div class="icon-circle bg-info text-white">
                            <i class="fas fa-paper-plane fa-2x"></i>
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
                            <h6 class="text-uppercase text-muted mb-2">Diterima</h6>
                            <h2 class="mb-0"><?= count(array_filter($penawaranList ?? [], function($p) { return $p['status'] == 'diterima'; })) ?></h2>
                        </div>
                        <div class="icon-circle bg-success text-white">
                            <i class="fas fa-check-circle fa-2x"></i>
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
                        Daftar Penawaran Harga
                    </h5>
                </div>
                <div class="d-flex gap-2">
                    <!-- Filter Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-filter me-1"></i> Filter Status
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= base_url('sales/penawaran') ?>">Semua Status</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= base_url('sales/penawaran?status=draft') ?>">Draft</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('sales/penawaran?status=sent') ?>">Sent</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('sales/penawaran?status=revisi') ?>">Revisi</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('sales/penawaran?status=diterima') ?>">Diterima</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('sales/penawaran?status=ditolak') ?>">Ditolak</a></li>
                        </ul>
                    </div>
                    
                    <!-- Add New Button -->
                    <a href="<?= base_url('sales/penawaran/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-1"></i> Buat Penawaran Baru
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

            <?php if (empty($penawaranList)): ?>
                <!-- Empty State -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-file-invoice-dollar fa-5x text-muted mb-3"></i>
                    </div>
                    <h4 class="text-muted mb-3">Belum Ada Penawaran</h4>
                    <p class="text-muted mb-4">Anda belum membuat penawaran harga. Mulai dengan membuat penawaran baru.</p>
                    <a href="<?= base_url('sales/penawaran/create') ?>" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus-circle me-2"></i> Buat Penawaran Pertama
                    </a>
                </div>
            <?php else: ?>
                <!-- Penawaran Table -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">#</th>
                                <th>Nomor Penawaran</th>
                                <th>Client</th>
                                <th>Project</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($penawaranList as $index => $penawaran): ?>
                            <?php 
                                // Status badge color
                                $statusColors = [
                                    'draft' => 'secondary',
                                    'sent' => 'info',
                                    'revisi' => 'warning',
                                    'diterima' => 'success',
                                    'ditolak' => 'danger',
                                    'kadaluarsa' => 'dark'
                                ];
                                $statusColor = $statusColors[$penawaran['status']] ?? 'secondary';
                            ?>
                            <tr>
                                <td class="ps-4 fw-bold text-muted"><?= $index + 1 ?></td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <strong class="text-primary"><?= htmlspecialchars($penawaran['nomor_penawaran']) ?></strong>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            <?= date('d/m/Y', strtotime($penawaran['tanggal_penawaran'])) ?>
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-primary text-white me-3">
                                            <?= strtoupper(substr($penawaran['nama_perusahaan'] ?? 'C', 0, 1)) ?>
                                        </div>
                                        <div>
                                            <h6 class="mb-0"><?= htmlspecialchars($penawaran['nama_perusahaan'] ?? 'N/A') ?></h6>
                                            <small class="text-muted">
                                                <?= htmlspecialchars($penawaran['kode_client'] ?? '') ?>
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span><?= htmlspecialchars($penawaran['nama_project'] ?? 'N/A') ?></span>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($penawaran['kode_project'] ?? '') ?>
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span>Penawaran: <?= date('d/m/Y', strtotime($penawaran['tanggal_penawaran'])) ?></span>
                                        <?php if ($penawaran['tanggal_kadaluarsa']): ?>
                                        <small class="text-muted">
                                            Kadaluarsa: <?= date('d/m/Y', strtotime($penawaran['tanggal_kadaluarsa'])) ?>
                                        </small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $statusColor ?>">
                                        <?= ucfirst($penawaran['status']) ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="<?= base_url('sales/penawaran/detail/' . $penawaran['id']) ?>" 
                                           class="btn btn-outline-info" 
                                           data-bs-toggle="tooltip" 
                                           title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($penawaran['status'] == 'draft' || $penawaran['status'] == 'revisi'): ?>
                                        <a href="<?= base_url('sales/penawaran/edit/' . $penawaran['id']) ?>" 
                                           class="btn btn-outline-warning" 
                                           data-bs-toggle="tooltip" 
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php endif; ?>
                                        <?php if ($penawaran['status'] == 'draft'): ?>
                                        <a href="<?= base_url('sales/penawaran/send/' . $penawaran['id']) ?>" 
                                           class="btn btn-outline-success" 
                                           data-bs-toggle="tooltip" 
                                           title="Kirim ke Client"
                                           onclick="return confirm('Kirim penawaran ini ke client?')">
                                            <i class="fas fa-paper-plane"></i>
                                        </a>
                                        <?php endif; ?>
                                        <?php if ($penawaran['status'] == 'draft' || $penawaran['status'] == 'revisi'): ?>
                                        <form action="<?= base_url('sales/penawaran/delete/' . $penawaran['id']) ?>" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus penawaran ini?')">
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
                                Menampilkan <strong><?= count($penawaranList) ?></strong> penawaran
                            </p>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-secondary" disabled>
                                    <i class="fas fa-print me-1"></i> Print All
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="window.location.reload()">
                                    <i class="fas fa-sync-alt me-1"></i> Refresh
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 text-end">
    <div class="btn-group" role="group">
        <div class="dropdown">
            <button class="btn btn-outline-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-download me-1"></i> Export
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item" href="<?= base_url('sales/penawaran/export-excel-all') ?>">
                        <i class="fas fa-file-excel text-success me-2"></i> Export All ke Excel
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="<?= base_url('sales/penawaran/export-pdf-all') ?>">
                        <i class="fas fa-file-pdf text-danger me-2"></i> Export All ke PDF
                    </a>
                </li>
            </ul>
        </div>
        <button type="button" class="btn btn-outline-secondary" onclick="window.location.reload()">
            <i class="fas fa-sync-alt me-1"></i> Refresh
        </button>
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
                    <h6 class="card-title mb-3"><i class="fas fa-info-circle me-2"></i>Informasi Status Penawaran</h6>
                    <div class="row">
                        <div class="col-md-3 mb-3 mb-md-0">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-secondary me-2">Draft</span>
                                <span class="text-muted small">Dapat diedit, belum dikirim</span>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-info me-2">Sent</span>
                                <span class="text-muted small">Terkirim ke client</span>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-warning me-2">Revisi</span>
                                <span class="text-muted small">Client minta revisi</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-success me-2">Diterima</span>
                                <span class="text-muted small">Client menyetujui</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom styles untuk tabel penawaran */
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

/* Responsive table */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.85rem;
    }
    
    .avatar-circle {
        width: 30px;
        height: 30px;
        font-size: 0.8rem;
    }
    
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
    }
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