<?php
$title = $title ?? 'Detail Client';
$active = $active ?? 'client';
?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header Card -->
            <div class="card border-0 shadow-lg mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">
                                <i class="fas fa-user-tie me-2"></i>
                                <?= $title ?>
                            </h4>
                            <p class="mb-0 mt-1 small opacity-75"><?= $subtitle ?? 'Detail Informasi Client' ?></p>
                        </div>
                        <div class="btn-group">
                            <a href="<?= base_url('sales/client') ?>" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                            <?php if (isset($client) && $client): ?>
                            <a href="<?= base_url('sales/client/edit/' . $client['id']) ?>" class="btn btn-warning btn-sm ms-2">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <?php if (!isset($client) || !$client): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-user-slash fa-4x text-muted mb-3"></i>
                            <h4>Data Client Tidak Ditemukan</h4>
                            <p class="text-muted">Client yang Anda cari tidak ada dalam sistem.</p>
                            <a href="<?= base_url('sales/client') ?>" class="btn btn-primary">
                                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Client
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- Client Info Section -->
                        <div class="row mb-5">
                            <!-- Company Info -->
                            <div class="col-md-8">
                                <div class="d-flex align-items-start mb-4">
                                    <div class="icon-circle bg-primary text-white me-4">
                                        <i class="fas fa-building fa-2x"></i>
                                    </div>
                                    <div>
                                        <h2 class="mb-1"><?= htmlspecialchars($client['nama_perusahaan']) ?></h2>
                                        <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                                            <span class="badge bg-secondary fs-6">
                                                <?= htmlspecialchars($client['kode_client']) ?>
                                            </span>
                                            <span class="badge bg-<?= $client['status'] == 'active' ? 'success' : ($client['status'] == 'potensial' ? 'warning' : 'danger') ?> fs-6">
                                                <?= ucfirst($client['status']) ?>
                                            </span>
                                            <span class="badge bg-info fs-6">
                                                <?= ucfirst($client['kategori']) ?>
                                            </span>
                                        </div>
                                        <?php if ($client['nama_kontak']): ?>
                                            <p class="mb-0">
                                                <i class="fas fa-user me-1 text-muted"></i>
                                                Contact: <strong><?= htmlspecialchars($client['nama_kontak']) ?></strong>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Quick Actions -->
                            <div class="col-md-4">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3">Quick Actions</h6>
                                        <div class="d-grid gap-2">
                                            <a href="tel:<?= htmlspecialchars($client['telepon']) ?>" class="btn btn-outline-primary">
                                                <i class="fas fa-phone me-1"></i> Telepon
                                            </a>
                                            <a href="mailto:<?= htmlspecialchars($client['email']) ?>" class="btn btn-outline-success">
                                                <i class="fas fa-envelope me-1"></i> Email
                                            </a>
                                            <button class="btn btn-outline-info" onclick="copyToClipboard('<?= htmlspecialchars($client['kode_client']) ?>')">
                                                <i class="fas fa-copy me-1"></i> Copy Kode
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Information -->
                        <div class="row">
                            <!-- Contact Information -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">
                                            <i class="fas fa-address-card me-2"></i>
                                            Informasi Kontak
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tbody>
                                                <?php if ($client['telepon']): ?>
                                                <tr>
                                                    <th width="30%"><i class="fas fa-phone text-muted me-2"></i> Telepon</th>
                                                    <td><?= htmlspecialchars($client['telepon']) ?></td>
                                                </tr>
                                                <?php endif; ?>
                                                
                                                <?php if ($client['email']): ?>
                                                <tr>
                                                    <th><i class="fas fa-envelope text-muted me-2"></i> Email</th>
                                                    <td>
                                                        <a href="mailto:<?= htmlspecialchars($client['email']) ?>">
                                                            <?= htmlspecialchars($client['email']) ?>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endif; ?>
                                                
                                                <?php if ($client['alamat']): ?>
                                                <tr>
                                                    <th><i class="fas fa-map-marker-alt text-muted me-2"></i> Alamat</th>
                                                    <td><?= nl2br(htmlspecialchars($client['alamat'])) ?></td>
                                                </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Business Information -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">
                                            <i class="fas fa-briefcase me-2"></i>
                                            Informasi Bisnis
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <th width="40%"><i class="fas fa-hashtag text-muted me-2"></i> Kode Client</th>
                                                    <td><?= htmlspecialchars($client['kode_client']) ?></td>
                                                </tr>
                                                <tr>
                                                    <th><i class="fas fa-tag text-muted me-2"></i> Kategori</th>
                                                    <td>
                                                        <span class="badge bg-info">
                                                            <?= ucfirst($client['kategori']) ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th><i class="fas fa-circle text-muted me-2"></i> Status</th>
                                                    <td>
                                                        <span class="badge bg-<?= $client['status'] == 'active' ? 'success' : ($client['status'] == 'potensial' ? 'warning' : 'danger') ?>">
                                                            <?= ucfirst($client['status']) ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <?php if ($client['npwp']): ?>
                                                <tr>
                                                    <th><i class="fas fa-file-invoice text-muted me-2"></i> NPWP</th>
                                                    <td><?= htmlspecialchars($client['npwp']) ?></td>
                                                </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Sales Information -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">
                                            <i class="fas fa-user-tie me-2"></i>
                                            Informasi Sales
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-primary text-white me-3">
                                                <?= strtoupper(substr($client['nama_sales'] ?? 'S', 0, 1)) ?>
                                            </div>
                                            <div>
                                                <h6 class="mb-1"><?= htmlspecialchars($client['nama_sales'] ?? 'Belum ditentukan') ?></h6>
                                                <p class="text-muted mb-0">Sales Penanggung Jawab</p>
                                            </div>
                                        </div>
                                        <hr>
                                        <p class="small text-muted mb-0">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Client ini ditangani oleh sales yang bertanggung jawab.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- System Information -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">
                                            <i class="fas fa-database me-2"></i>
                                            Sistem Informasi
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <th width="50%"><i class="fas fa-calendar-plus text-muted me-2"></i> Dibuat Pada</th>
                                                    <td><?= date('d/m/Y H:i', strtotime($client['created_at'])) ?></td>
                                                </tr>
                                                <tr>
                                                    <th><i class="fas fa-calendar-check text-muted me-2"></i> Diupdate Pada</th>
                                                    <td><?= date('d/m/Y H:i', strtotime($client['updated_at'])) ?></td>
                                                </tr>
                                                <tr>
                                                    <th><i class="fas fa-id-card text-muted me-2"></i> ID Client</th>
                                                    <td>#<?= $client['id'] ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Related Data (Placeholder for future features) -->
                        <div class="row">
                            <div class="col-12 mb-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">
                                            <i class="fas fa-project-diagram me-2"></i>
                                            Aktivitas Terkait
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center">
                                            <div class="col-md-3 mb-3">
                                                <div class="p-3 border rounded">
                                                    <h3 class="text-primary mb-2">0</h3>
                                                    <p class="mb-0">Projects</p>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <div class="p-3 border rounded">
                                                    <h3 class="text-success mb-2">0</h3>
                                                    <p class="mb-0">Penawaran</p>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <div class="p-3 border rounded">
                                                    <h3 class="text-info mb-2">0</h3>
                                                    <p class="mb-0">Invoice</p>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <div class="p-3 border rounded">
                                                    <h3 class="text-warning mb-2">0</h3>
                                                    <p class="mb-0">Pembayaran</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-center mt-3">
                                            <button class="btn btn-outline-primary" disabled>
                                                <i class="fas fa-plus me-1"></i> Buat Project Baru
                                            </button>
                                            <p class="text-muted small mt-2 mb-0">Fitur ini akan tersedia segera</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.icon-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.table-borderless th {
    font-weight: 600;
    color: #495057;
}

.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.btn-group .btn {
    border-radius: 0.375rem !important;
}

@media (max-width: 768px) {
    .icon-circle {
        width: 50px;
        height: 50px;
    }
    
    .display-5 {
        font-size: 1.8rem;
    }
}
</style>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success notification
        alert('Kode client "' + text + '" berhasil disalin!');
    }, function(err) {
        console.error('Gagal menyalin: ', err);
        alert('Gagal menyalin kode client');
    });
}

// Add confirmation for actions
document.addEventListener('DOMContentLoaded', function() {
    // Add confirmation for delete button if exists
    const deleteBtn = document.querySelector('a[href*="delete"]');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function(e) {
            if (!confirm('Apakah Anda yakin ingin menghapus client ini?')) {
                e.preventDefault();
            }
        });
    }
});
</script>