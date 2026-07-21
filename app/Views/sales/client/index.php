<?php
$title = $title ?? 'Data Client';
$active = $active ?? 'client';
?>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header">
                <h1 class="display-5 fw-bold text-primary mb-3">
                    <i class="fas fa-user-tie me-3"></i>
                    <?= $title ?>
                </h1>
                <p class="lead text-muted">
                    <?= $subtitle ?? 'Kelola data client/pelanggan Anda' ?>
                    <span class="badge bg-primary ms-2"><?= count($clients ?? []) ?> Client</span>
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
                            <h6 class="text-uppercase text-muted mb-2">Total Clients</h6>
                            <h2 class="mb-0"><?= count($clients ?? []) ?></h2>
                        </div>
                        <div class="icon-circle bg-primary text-white">
                            <i class="fas fa-users fa-2x"></i>
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
                            <h6 class="text-uppercase text-muted mb-2">Active</h6>
                            <h2 class="mb-0"><?= count(array_filter($clients ?? [], function($c) { return $c['status'] == 'active'; })) ?></h2>
                        </div>
                        <div class="icon-circle bg-success text-white">
                            <i class="fas fa-check-circle fa-2x"></i>
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
                            <h6 class="text-uppercase text-muted mb-2">Potensial</h6>
                            <h2 class="mb-0"><?= count(array_filter($clients ?? [], function($c) { return $c['status'] == 'potensial'; })) ?></h2>
                        </div>
                        <div class="icon-circle bg-warning text-white">
                            <i class="fas fa-star fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-danger border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted mb-2">Inactive</h6>
                            <h2 class="mb-0"><?= count(array_filter($clients ?? [], function($c) { return $c['status'] == 'inactive'; })) ?></h2>
                        </div>
                        <div class="icon-circle bg-danger text-white">
                            <i class="fas fa-times-circle fa-2x"></i>
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
                        Daftar Client
                    </h5>
                </div>
                <div class="d-flex gap-2">
                    <!-- Filter Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-filter me-1"></i> Filter Status
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= base_url('sales/client') ?>">Semua Status</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= base_url('sales/client?status=active') ?>">Active</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('sales/client?status=potensial') ?>">Potensial</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('sales/client?status=inactive') ?>">Inactive</a></li>
                        </ul>
                    </div>
                    
                    <!-- Add New Button -->
                    <a href="<?= base_url('sales/client/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-1"></i> Tambah Client Baru
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

            <?php if (empty($clients)): ?>
                <!-- Empty State -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-user-tie fa-5x text-muted mb-3"></i>
                    </div>
                    <h4 class="text-muted mb-3">Belum Ada Client</h4>
                    <p class="text-muted mb-4">Anda belum memiliki data client. Mulai dengan menambahkan client baru.</p>
                    <a href="<?= base_url('sales/client/create') ?>" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus-circle me-2"></i> Tambah Client Pertama
                    </a>
                </div>
            <?php else: ?>
                <!-- Clients Table -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">#</th>
                                <th>Perusahaan</th>
                                <th>Kontak</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Sales</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clients as $index => $client): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-muted"><?= $index + 1 ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-primary text-white me-3">
                                            <?= strtoupper(substr($client['nama_perusahaan'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <h6 class="mb-0"><?= htmlspecialchars($client['nama_perusahaan']) ?></h6>
                                            <small class="text-muted">
                                                <i class="fas fa-hashtag me-1"></i>
                                                <?= htmlspecialchars($client['kode_client']) ?>
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($client['nama_kontak']): ?>
                                        <div class="d-flex flex-column">
                                            <span><?= htmlspecialchars($client['nama_kontak']) ?></span>
                                            <?php if ($client['telepon']): ?>
                                            <small class="text-muted">
                                                <i class="fas fa-phone me-1"></i>
                                                <?= htmlspecialchars($client['telepon']) ?>
                                            </small>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <?= ucfirst($client['kategori']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $client['status'] == 'active' ? 'success' : ($client['status'] == 'potensial' ? 'warning' : 'danger') ?>">
                                        <?= ucfirst($client['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle-sm bg-secondary text-white me-2">
                                            <?= strtoupper(substr($client['nama_sales'] ?? 'S', 0, 1)) ?>
                                        </div>
                                        <span><?= htmlspecialchars($client['nama_sales'] ?? 'Belum ditentukan') ?></span>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="<?= base_url('sales/client/detail/' . $client['id']) ?>" 
                                           class="btn btn-outline-info" 
                                           data-bs-toggle="tooltip" 
                                           title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('sales/client/edit/' . $client['id']) ?>" 
                                           class="btn btn-outline-warning" 
                                           data-bs-toggle="tooltip" 
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal<?= $client['id'] ?>"
                                                data-bs-tooltip="tooltip" 
                                                title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>

                                    <!-- Delete Modal for each client -->
                                    <div class="modal fade" id="deleteModal<?= $client['id'] ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        Konfirmasi Hapus
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <div class="mb-4">
                                                        <div class="avatar-circle bg-danger text-white mx-auto mb-3">
                                                            <i class="fas fa-trash-alt fa-2x"></i>
                                                        </div>
                                                        <h4>Hapus Client?</h4>
                                                        <p class="text-muted">
                                                            Anda yakin ingin menghapus <strong><?= htmlspecialchars($client['nama_perusahaan']) ?></strong>?
                                                        </p>
                                                        <p class="text-danger small">
                                                            <i class="fas fa-exclamation-circle me-1"></i>
                                                            Tindakan ini tidak dapat dibatalkan
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        <i class="fas fa-times me-1"></i> Batal
                                                    </button>
                                                    <form action="<?= base_url('sales/client/delete/' . $client['id']) ?>" method="POST" class="d-inline">
                                                        <?= csrf_field() ?>

                                                        <button type="submit" class="btn btn-danger">
                                                            <i class="fas fa-trash-alt me-1"></i> Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
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
                                Menampilkan <strong><?= count($clients) ?></strong> client
                            </p>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-secondary" disabled>
                                    <i class="fas fa-print me-1"></i> Print
                                </button>
                                <button type="button" class="btn btn-outline-secondary" disabled>
                                    <i class="fas fa-file-export me-1"></i> Export
                                </button>
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
                    <div class="row">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-primary text-white me-3">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Total Client</h6>
                                    <p class="mb-0 text-muted">Semua client yang terdaftar</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-success text-white me-3">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Client Active</h6>
                                    <p class="mb-0 text-muted">Client dengan status aktif</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-warning text-white me-3">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Client Potensial</h6>
                                    <p class="mb-0 text-muted">Client prospek yang perlu follow up</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom styles untuk tabel client */
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.avatar-circle-sm {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: bold;
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
    })
    
    // Add confirmation for delete buttons
    document.querySelectorAll('form[action*="delete"]').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            if (!confirm('Apakah Anda yakin ingin menghapus client ini?')) {
                e.preventDefault();
            }
        });
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

// Filter functionality
function filterClients(status) {
    window.location.href = '<?= base_url('sales/client') ?>?status=' + status;
}

// Search functionality (placeholder)
function searchClients() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>