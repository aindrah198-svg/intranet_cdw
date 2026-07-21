<?php
$title = $title ?? 'Detail Penawaran';
$active = $active ?? 'penawaran';
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
                                <i class="fas fa-file-invoice-dollar me-2"></i>
                                <?= $title ?>
                            </h4>
                            <p class="mb-0 mt-1 small opacity-75"><?= $subtitle ?? 'Detail Informasi Penawaran' ?></p>
                        </div>
                        <div class="btn-group">
                            <a href="<?= base_url('sales/penawaran') ?>" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                            <a href="<?= base_url('sales/penawaran/print/' . $penawaran['id']) ?>" 
                               target="_blank" 
                               class="btn btn-warning btn-sm ms-2">
                                <i class="fas fa-print me-1"></i> Print
                            </a>
                            <?php if (isset($penawaran) && $penawaran): ?>
                                <?php if ($penawaran['status'] == 'draft' || $penawaran['status'] == 'revisi'): ?>
                                <a href="<?= base_url('sales/penawaran/edit/' . $penawaran['id']) ?>" 
                                   class="btn btn-info btn-sm ms-2">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <?php if (!isset($penawaran) || !$penawaran): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-file-invoice-dollar fa-4x text-muted mb-3"></i>
                            <h4>Data Penawaran Tidak Ditemukan</h4>
                            <p class="text-muted">Penawaran yang Anda cari tidak ada dalam sistem.</p>
                            <a href="<?= base_url('sales/penawaran') ?>" class="btn btn-primary">
                                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Penawaran
                            </a>
                        </div>
                    <?php else: ?>
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
                        
                        <!-- Penawaran Info Section -->
                        <div class="row mb-5">
                            <!-- Company Info -->
                            <div class="col-md-8">
                                <div class="d-flex align-items-start mb-4">
                                    <div class="icon-circle bg-primary text-white me-4">
                                        <i class="fas fa-file-invoice fa-2x"></i>
                                    </div>
                                    <div>
                                        <h2 class="mb-1"><?= htmlspecialchars($penawaran['nomor_penawaran']) ?></h2>
                                        <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                                            <span class="badge bg-<?= $statusColor ?> fs-6">
                                                <?= ucfirst($penawaran['status']) ?>
                                            </span>
                                            <span class="badge bg-primary fs-6">
                                                <?= htmlspecialchars($penawaran['kode_project']) ?>
                                            </span>
                                        </div>
                                        <p class="mb-0">
                                            <i class="fas fa-building me-1 text-muted"></i>
                                            Client: <strong><?= htmlspecialchars($penawaran['nama_perusahaan']) ?></strong>
                                        </p>
                                        <p class="mb-0">
                                            <i class="fas fa-tasks me-1 text-muted"></i>
                                            Project: <strong><?= htmlspecialchars($penawaran['nama_project']) ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Quick Actions -->
                            <div class="col-md-4">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3">Quick Actions</h6>
                                        <div class="d-grid gap-2">
                                            <?php if ($penawaran['status'] == 'draft'): ?>
                                                <a href="<?= base_url('sales/penawaran/send/' . $penawaran['id']) ?>" 
                                                   class="btn btn-success"
                                                   onclick="return confirm('Kirim penawaran ini ke client?')">
                                                    <i class="fas fa-paper-plane me-1"></i> Kirim ke Client
                                                </a>
                                            <?php endif; ?>
                                            
                                            <a href="<?= base_url('sales/penawaran/print/' . $penawaran['id']) ?>" 
                                               target="_blank" 
                                               class="btn btn-outline-primary">
                                                <i class="fas fa-print me-1"></i> Print Penawaran
                                            </a>

                                            
                                            
                                            <?php if ($penawaran['status'] == 'draft' || $penawaran['status'] == 'revisi'): ?>
                                                <a href="<?= base_url('sales/penawaran/edit/' . $penawaran['id']) ?>" 
                                                   class="btn btn-outline-warning">
                                                    <i class="fas fa-edit me-1"></i> Edit Penawaran
                                                </a>
                                            <?php endif; ?>
                                        </div>

                                            <!-- Export Buttons -->
                <div class="dropdown">
                    <button class="btn btn-outline-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-download me-1"></i> Export
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="<?= base_url('sales/penawaran/export-excel/' . $penawaran['id']) ?>">
                                <i class="fas fa-file-excel text-success me-2"></i> Export ke Excel
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= base_url('sales/penawaran/export-pdf/' . $penawaran['id']) ?>">
                                <i class="fas fa-file-pdf text-danger me-2"></i> Export ke PDF
                            </a>
                        </li>
                    </ul>
                </div>
                
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Information -->
                        <div class="row">
                            <!-- Penawaran Information -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Informasi Penawaran
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <th width="40%"><i class="fas fa-hashtag text-muted me-2"></i> Nomor Penawaran</th>
                                                    <td><?= htmlspecialchars($penawaran['nomor_penawaran']) ?></td>
                                                </tr>
                                                <tr>
                                                    <th><i class="fas fa-calendar text-muted me-2"></i> Tanggal Penawaran</th>
                                                    <td><?= date('d/m/Y', strtotime($penawaran['tanggal_penawaran'])) ?></td>
                                                </tr>
                                                <tr>
                                                    <th><i class="fas fa-calendar-times text-muted me-2"></i> Tanggal Kadaluarsa</th>
                                                    <td>
                                                        <?php if ($penawaran['tanggal_kadaluarsa']): ?>
                                                            <?= date('d/m/Y', strtotime($penawaran['tanggal_kadaluarsa'])) ?>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th><i class="fas fa-circle text-muted me-2"></i> Status</th>
                                                    <td>
                                                        <span class="badge bg-<?= $statusColor ?>">
                                                            <?= ucfirst($penawaran['status']) ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <?php if ($penawaran['keterangan']): ?>
                                                <tr>
                                                    <th><i class="fas fa-sticky-note text-muted me-2"></i> Keterangan</th>
                                                    <td><?= nl2br(htmlspecialchars($penawaran['keterangan'])) ?></td>
                                                </tr>
                                                <?php endif; ?>
                                                <?php if ($penawaran['catatan_khusus']): ?>
                                                <tr>
                                                    <th><i class="fas fa-exclamation-circle text-muted me-2"></i> Catatan Khusus</th>
                                                    <td><?= nl2br(htmlspecialchars($penawaran['catatan_khusus'])) ?></td>
                                                </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Client Information -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">
                                            <i class="fas fa-user-tie me-2"></i>
                                            Informasi Client
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <th width="40%"><i class="fas fa-building text-muted me-2"></i> Perusahaan</th>
                                                    <td><?= htmlspecialchars($penawaran['nama_perusahaan']) ?></td>
                                                </tr>
                                                <tr>
                                                    <th><i class="fas fa-user text-muted me-2"></i> Kontak</th>
                                                    <td><?= htmlspecialchars($penawaran['nama_kontak'] ?? '-') ?></td>
                                                </tr>
                                                <tr>
                                                    <th><i class="fas fa-phone text-muted me-2"></i> Telepon</th>
                                                    <td><?= htmlspecialchars($penawaran['telepon'] ?? '-') ?></td>
                                                </tr>
                                                <tr>
                                                    <th><i class="fas fa-envelope text-muted me-2"></i> Email</th>
                                                    <td>
                                                        <?php if ($penawaran['email']): ?>
                                                            <a href="mailto:<?= htmlspecialchars($penawaran['email']) ?>">
                                                                <?= htmlspecialchars($penawaran['email']) ?>
                                                            </a>
                                                        <?php else: ?>
                                                            -
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th><i class="fas fa-map-marker-alt text-muted me-2"></i> Alamat</th>
                                                    <td><?= nl2br(htmlspecialchars($penawaran['alamat_client'] ?? '-')) ?></td>
                                                </tr>
                                                <?php if ($penawaran['npwp']): ?>
                                                <tr>
                                                    <th><i class="fas fa-file-invoice text-muted me-2"></i> NPWP</th>
                                                    <td><?= htmlspecialchars($penawaran['npwp']) ?></td>
                                                </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Project Information -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">
                                            <i class="fas fa-project-diagram me-2"></i>
                                            Informasi Project
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <th width="40%"><i class="fas fa-hashtag text-muted me-2"></i> Kode Project</th>
                                                    <td><?= htmlspecialchars($penawaran['kode_project']) ?></td>
                                                </tr>
                                                <tr>
                                                    <th><i class="fas fa-tasks text-muted me-2"></i> Nama Project</th>
                                                    <td><?= htmlspecialchars($penawaran['nama_project']) ?></td>
                                                </tr>
                                                <tr>
                                                    <th><i class="fas fa-money-bill text-muted me-2"></i> Nilai Project</th>
                                                    <td>
                                                        <?php if ($penawaran['nilai_project']): ?>
                                                            Rp <?= number_format($penawaran['nilai_project'], 0, ',', '.') ?>
                                                        <?php else: ?>
                                                            -
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php if ($penawaran['deskripsi_project']): ?>
                                                <tr>
                                                    <th><i class="fas fa-align-left text-muted me-2"></i> Deskripsi</th>
                                                    <td><?= nl2br(htmlspecialchars($penawaran['deskripsi_project'])) ?></td>
                                                </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
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
                                                    <th width="50%"><i class="fas fa-user-plus text-muted me-2"></i> Dibuat Oleh</th>
                                                    <td><?= htmlspecialchars($penawaran['created_by_name'] ?? 'System') ?></td>
                                                </tr>
                                                <tr>
                                                    <th><i class="fas fa-user-check text-muted me-2"></i> Disetujui Oleh</th>
                                                    <td><?= htmlspecialchars($penawaran['approved_by_name'] ?? '-') ?></td>
                                                </tr>
                                                <tr>
                                                    <th><i class="fas fa-calendar-plus text-muted me-2"></i> Dibuat Pada</th>
                                                    <td><?= date('d/m/Y H:i', strtotime($penawaran['created_at'])) ?></td>
                                                </tr>
                                                <tr>
                                                    <th><i class="fas fa-calendar-check text-muted me-2"></i> Diupdate Pada</th>
                                                    <td><?= date('d/m/Y H:i', strtotime($penawaran['updated_at'])) ?></td>
                                                </tr>
                                                <tr>
                                                    <th><i class="fas fa-id-card text-muted me-2"></i> ID Penawaran</th>
                                                    <td>#<?= $penawaran['id'] ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="row">
                            <div class="col-12 mb-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">
                                            <i class="fas fa-list-alt me-2"></i>
                                            Daftar Item Penawaran
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Nama Item</th>
                                                        <th>Deskripsi</th>
                                                        <th>Qty</th>
                                                        <th>Satuan</th>
                                                        <th>Harga Satuan (Rp)</th>
                                                        <th>Subtotal (Rp)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($items)): ?>
                                                        <?php foreach ($items as $index => $item): ?>
                                                            <tr>
                                                                <td><?= $index + 1 ?></td>
                                                                <td><?= htmlspecialchars($item['nama_item']) ?></td>
                                                                <td><?= nl2br(htmlspecialchars($item['deskripsi'])) ?></td>
                                                                <td><?= number_format($item['qty'], 2) ?></td>
                                                                <td><?= htmlspecialchars($item['satuan']) ?></td>
                                                                <td class="text-end"><?= number_format($item['harga_satuan'], 0, ',', '.') ?></td>
                                                                <td class="text-end fw-bold"><?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="7" class="text-center text-muted py-4">
                                                                <i class="fas fa-exclamation-circle me-2"></i>
                                                                Tidak ada item penawaran
                                                            </td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                                <tfoot class="table-dark">
                                                    <tr>
                                                        <td colspan="6" class="text-end fw-bold">TOTAL PENAWARAN</td>
                                                        <td class="text-end fw-bold">
                                                            Rp <?= number_format($total, 0, ',', '.') ?>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status Update Form (for sales) -->
                        <?php if ($penawaran['status'] == 'sent' || $penawaran['status'] == 'revisi'): ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-sync-alt me-2"></i>
                                            Update Status Penawaran
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="<?= base_url('sales/penawaran/update-status/' . $penawaran['id']) ?>" method="POST">
                                            <?= csrf_field() ?>
                                            <div class="row align-items-center">
                                                <div class="col-md-8 mb-3 mb-md-0">
                                                    <label for="status" class="form-label">Update Status Menjadi:</label>
                                                    <select class="form-select" id="status" name="status" required>
                                                        <option value="">-- Pilih Status --</option>
                                                        <option value="diterima" <?= $penawaran['status'] == 'diterima' ? 'selected' : '' ?>>Diterima</option>
                                                        <option value="ditolak" <?= $penawaran['status'] == 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                                                        <option value="revisi" <?= $penawaran['status'] == 'revisi' ? 'selected' : '' ?>>Revisi</option>
                                                        <option value="kadaluarsa" <?= $penawaran['status'] == 'kadaluarsa' ? 'selected' : '' ?>>Kadaluarsa</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <button type="submit" class="btn btn-primary w-100">
                                                        <i class="fas fa-save me-1"></i> Update Status
                                                    </button>
                                                </div>
                                            </div>
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Status "Diterima" akan mengubah status project menjadi "Deal"
                                            </small>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Delete Section (only for draft/revisi) -->
                        <?php if ($penawaran['status'] == 'draft' || $penawaran['status'] == 'revisi'): ?>
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm border-danger">
                                    <div class="card-header bg-danger text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            Area Berbahaya
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="text-danger mb-1">Hapus Penawaran</h6>
                                                <p class="text-muted small mb-0">
                                                    Tindakan ini akan menghapus penawaran secara permanen dari sistem.
                                                    Data yang dihapus tidak dapat dikembalikan.
                                                </p>
                                            </div>
                                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                                <i class="fas fa-trash-alt me-1"></i> Hapus Penawaran
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Delete Modal -->
                        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title" id="deleteModalLabel">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            Konfirmasi Penghapusan
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="text-center mb-4">
                                            <i class="fas fa-trash-alt fa-4x text-danger mb-3"></i>
                                            <h4>Apakah Anda yakin?</h4>
                                            <p class="text-muted">
                                                Anda akan menghapus penawaran:
                                                <strong><?= htmlspecialchars($penawaran['nomor_penawaran']) ?></strong>
                                            </p>
                                            <p class="text-danger small">
                                                <i class="fas fa-exclamation-circle me-1"></i>
                                                Tindakan ini tidak dapat dibatalkan!
                                            </p>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                            <i class="fas fa-times me-1"></i> Batal
                                        </button>
                                        <form action="<?= base_url('sales/penawaran/delete/' . $penawaran['id']) ?>" method="POST" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-trash-alt me-1"></i> Ya, Hapus Penawaran
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
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
// Add confirmation for actions
document.addEventListener('DOMContentLoaded', function() {
    // Add confirmation for status update
    const statusForm = document.querySelector('form[action*="update-status"]');
    if (statusForm) {
        statusForm.addEventListener('submit', function(e) {
            const status = document.getElementById('status').value;
            if (!confirm(`Apakah Anda yakin ingin mengupdate status penawaran menjadi "${status}"?`)) {
                e.preventDefault();
            }
        });
    }
});
</script>