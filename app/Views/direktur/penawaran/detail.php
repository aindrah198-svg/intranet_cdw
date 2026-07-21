<?php
// app/Views/direktur/penawaran/detail.php
$title = 'Detail Penawaran';
$subtitle = 'Nomor: ' . ($penawaran['nomor_penawaran'] ?? '-');
$user = $user ?? session()->get();
$active = 'penawaran';
?>

<?= $this->include('direktur/templates/header') ?>
<?= $this->include('direktur/templates/sidebar') ?>
<?= $this->include('direktur/templates/navbar') ?>

<style>
    .detail-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        margin-bottom: 20px;
    }
    
    .detail-header {
        background: linear-gradient(135deg, var(--cdw-primary), var(--cdw-secondary));
        color: white;
        border-radius: 12px 12px 0 0;
        padding: 20px;
        margin: -25px -25px 25px -25px;
    }
    
    .info-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 5px;
    }
    
    .info-value {
        color: #212529;
        font-size: 1rem;
        margin-bottom: 15px;
    }
    
    .item-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .item-table th {
        background: #f8f9fa;
        padding: 12px;
        text-align: left;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        color: #495057;
    }
    
    .item-table td {
        padding: 12px;
        border-bottom: 1px solid #e9ecef;
        vertical-align: top;
    }
    
    .action-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #dee2e6;
    }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title mb-1">
                <i class="fas fa-file-contract me-2"></i>Detail Penawaran
            </h1>
            <p class="page-subtitle">Nomor: <?= $penawaran['nomor_penawaran'] ?? '-' ?></p>
        </div>
        <div>
            <a href="<?= base_url('direktur/penawaran') ?>" class="btn btn-modern-outline">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="detail-card">
                <div class="detail-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0"><?= $penawaran['nomor_penawaran'] ?? 'Penawaran' ?></h3>
                            <p class="mb-0 opacity-75">Status: 
                                <?php 
                                $statusClass = '';
                                $statusText = '';
                                switch(strtolower($penawaran['status'] ?? 'draft')) {
                                    case 'draft': $statusClass = 'badge bg-secondary'; $statusText = 'Draft'; break;
                                    case 'sent': $statusClass = 'badge bg-info'; $statusText = 'Terkirim'; break;
                                    case 'revisi': $statusClass = 'badge bg-warning'; $statusText = 'Revisi'; break;
                                    case 'diterima': $statusClass = 'badge bg-success'; $statusText = 'Diterima'; break;
                                    case 'ditolak': $statusClass = 'badge bg-danger'; $statusText = 'Ditolak'; break;
                                    default: $statusClass = 'badge bg-secondary'; $statusText = 'Draft';
                                }
                                ?>
                                <span class="<?= $statusClass ?>"><?= $statusText ?></span>
                            </p>
                        </div>
                        <div>
                            <i class="fas fa-file-invoice fa-3x"></i>
                        </div>
                    </div>
                </div>

                <!-- Informasi Utama -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="info-label">Tanggal Penawaran</div>
                        <div class="info-value"><?= date('d F Y', strtotime($penawaran['tanggal_penawaran'])) ?></div>
                        
                        <div class="info-label">Tanggal Kadaluarsa</div>
                        <div class="info-value"><?= !empty($penawaran['tanggal_kadaluarsa']) ? date('d F Y', strtotime($penawaran['tanggal_kadaluarsa'])) : '-' ?></div>
                        
                        <div class="info-label">Client</div>
                        <div class="info-value"><?= $penawaran['client_nama'] ?? '-' ?></div>
                        
                        <div class="info-label">Project</div>
                        <div class="info-value"><?= $penawaran['nama_project'] ?? '-' ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Kode Project</div>
                        <div class="info-value"><?= $penawaran['kode_project'] ?? '-' ?></div>
                        
                        <div class="info-label">Kode Client</div>
                        <div class="info-value"><?= $penawaran['kode_client'] ?? '-' ?></div>
                        
                        <div class="info-label">Dibuat Oleh</div>
                        <div class="info-value"><?= $penawaran['created_by_name'] ?? '-' ?></div>
                        
                        <div class="info-label">Tanggal Dibuat</div>
                        <div class="info-value"><?= date('d F Y H:i', strtotime($penawaran['created_at'])) ?></div>
                    </div>
                </div>

                <!-- Item Penawaran -->
                <div class="mb-4">
                    <h5 class="mb-3">Item Penawaran</h5>
                    <div class="table-responsive">
                        <table class="item-table">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="30%">Nama Item</th>
                                    <th width="25%">Deskripsi</th>
                                    <th width="10%">Qty</th>
                                    <th width="10%">Satuan</th>
                                    <th width="10%">Harga</th>
                                    <th width="10%">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($items)): ?>
                                    <?php $no = 1; $total = 0; ?>
                                    <?php foreach($items as $item): ?>
                                        <?php $itemTotal = $item['qty'] * $item['harga_satuan']; $total += $itemTotal; ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= $item['nama_item'] ?></td>
                                            <td><?= $item['deskripsi'] ?></td>
                                            <td><?= number_format($item['qty'], 2) ?></td>
                                            <td><?= $item['satuan'] ?></td>
                                            <td>Rp <?= number_format($item['harga_satuan'], 0, ',', '.') ?></td>
                                            <td>Rp <?= number_format($itemTotal, 0, ',', '.') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-3 text-muted">
                                            Tidak ada item penawaran
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Ringkasan Harga -->
                <div class="row">
                    <div class="col-md-8"></div>
                    <div class="col-md-4">
                        <div class="bg-light p-3 rounded">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span>Rp <?= number_format($penawaran['subtotal'] ?? $total, 0, ',', '.') ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>PPN (11%):</span>
                                <span>Rp <?= number_format($penawaran['ppn_amount'] ?? ($total * 0.11), 0, ',', '.') ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Diskon:</span>
                                <span>Rp <?= number_format($penawaran['discount_amount'] ?? 0, 0, ',', '.') ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 fw-bold fs-5">
                                <span>Total:</span>
                                <span>Rp <?= number_format($penawaran['total'] ?? ($total + ($total * 0.11)), 0, ',', '.') ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Keterangan dan Catatan -->
                <?php if(!empty($penawaran['keterangan']) || !empty($penawaran['catatan_khusus'])): ?>
                <div class="mt-4 pt-3 border-top">
                    <div class="row">
                        <?php if(!empty($penawaran['keterangan'])): ?>
                        <div class="col-md-6">
                            <div class="info-label">Keterangan (Client)</div>
                            <div class="info-value"><?= nl2br($penawaran['keterangan']) ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($penawaran['catatan_khusus'])): ?>
                        <div class="col-md-6">
                            <div class="info-label">Catatan Khusus (Internal)</div>
                            <div class="info-value"><?= nl2br($penawaran['catatan_khusus']) ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <?php if(in_array(strtolower($penawaran['status']), ['draft', 'sent', 'revisi'])): ?>
                        <a href="<?= base_url('direktur/penawaran/edit/' . $penawaran['id']) ?>" 
                           class="btn btn-modern-outline">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <a href="<?= base_url('direktur/penawaran/approve/' . $penawaran['id']) ?>" 
                           class="btn btn-success"
                           onclick="return confirm('Approve penawaran ini?')">
                            <i class="fas fa-check me-1"></i> Approve
                        </a>
                        <a href="<?= base_url('direktur/penawaran/reject/' . $penawaran['id']) ?>" 
                           class="btn btn-danger"
                           onclick="return confirm('Tolak penawaran ini?')">
                            <i class="fas fa-times me-1"></i> Tolak
                        </a>
                    <?php endif; ?>
                    
                    <?php if(strtolower($penawaran['status']) === 'diterima'): ?>
                        <a href="<?= base_url('direktur/penawaran/print/' . $penawaran['id']) ?>" 
                           target="_blank" class="btn btn-modern-primary">
                            <i class="fas fa-print me-1"></i> Print
                        </a>
                    <?php endif; ?>
                    
                    <a href="<?= base_url('direktur/penawaran/export/pdf/' . $penawaran['id']) ?>" 
                       target="_blank" class="btn btn-modern-outline">
                        <i class="fas fa-file-pdf me-1"></i> Export PDF
                    </a>
                    
                    <form action="<?= base_url('direktur/penawaran/delete/' . $penawaran['id']) ?>" 
                          method="POST" style="display: inline;" 
                          onsubmit="return confirm('Hapus penawaran ini?')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('direktur/templates/footer') ?>