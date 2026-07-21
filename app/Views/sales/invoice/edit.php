<?php
$title = $title ?? 'Edit Invoice';
$active = $active ?? 'invoice';

// Format currency
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
                    <i class="fas fa-edit me-3"></i>
                    <?= $title ?>
                </h1>
                <p class="lead text-muted">
                    <?= $subtitle ?? 'Form edit invoice' ?>
                </p>
            </div>
        </div>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Periksa kesalahan berikut:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Payment Summary Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="text-muted small mb-1">Total Invoice</div>
                                <div class="h4 fw-bold text-primary">Rp <?= formatRupiah($total) ?></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="text-muted small mb-1">Sudah Dibayar</div>
                                <div class="h4 fw-bold text-success">Rp <?= formatRupiah($paymentSummary) ?></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="text-muted small mb-1">Sisa Tagihan</div>
                                <div class="h4 fw-bold <?= $remaining > 0 ? 'text-danger' : 'text-success' ?>">
                                    Rp <?= formatRupiah($remaining) ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="text-muted small mb-1">Status</div>
                                <div class="h4 fw-bold">
                                    <?php 
                                    $badgeClass = [
                                        'belum_bayar' => 'badge bg-danger',
                                        'sebagian' => 'badge bg-warning',
                                        'lunas' => 'badge bg-success',
                                        'overdue' => 'badge bg-danger'
                                    ];
                                    $statusText = [
                                        'belum_bayar' => 'Belum Bayar',
                                        'sebagian' => 'Sebagian',
                                        'lunas' => 'Lunas',
                                        'overdue' => 'Overdue'
                                    ];
                                    ?>
                                    <span class="<?= $badgeClass[$invoice['status_pembayaran']] ?>">
                                        <?= $statusText[$invoice['status_pembayaran']] ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    <?php if ($total > 0): ?>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Progress Pembayaran</small>
                            <small><?= round(($paymentSummary / $total) * 100, 1) ?>%</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" 
                                 role="progressbar" 
                                 style="width: <?= ($paymentSummary / $total) * 100 ?>%" 
                                 aria-valuenow="<?= ($paymentSummary / $total) * 100 ?>" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100"></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Form Card -->
    <div class="card border-0 shadow-lg">
        <div class="card-header bg-light py-3">
            <h5 class="mb-0">
                <i class="fas fa-edit me-2"></i>
                Form Edit Invoice
            </h5>
        </div>
        
        <form action="<?= base_url('sales/invoice/update/' . $invoice['id']) ?>" method="POST" id="invoiceForm">
            <?= csrf_field() ?>
            
            <div class="card-body">
                <!-- Basic Information -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card border">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Invoice</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="nomor_invoice" class="form-label">Nomor Invoice <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nomor_invoice" 
                                               name="nomor_invoice" value="<?= old('nomor_invoice', $invoice['nomor_invoice'] ?? '') ?>" 
                                               required>
                                        <div class="form-text">Nomor invoice unik</div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="tanggal_invoice" class="form-label">Tanggal Invoice <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="tanggal_invoice" 
                                               name="tanggal_invoice" value="<?= old('tanggal_invoice', $invoice['tanggal_invoice'] ?? date('Y-m-d')) ?>" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="tanggal_jatuh_tempo" class="form-label">Jatuh Tempo <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="tanggal_jatuh_tempo" 
                                               name="tanggal_jatuh_tempo" value="<?= old('tanggal_jatuh_tempo', $invoice['tanggal_jatuh_tempo'] ?? date('Y-m-d', strtotime('+14 days'))) ?>" required>
                                    </div>
                                    
                                    <div class="col-md-12 mb-3">
                                        <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                                        <select class="form-select" id="metode_pembayaran" name="metode_pembayaran">
                                            <option value="">Pilih Metode</option>
                                            <option value="transfer" <?= old('metode_pembayaran', $invoice['metode_pembayaran'] ?? '') == 'transfer' ? 'selected' : '' ?>>Transfer Bank</option>
                                            <option value="tunai" <?= old('metode_pembayaran', $invoice['metode_pembayaran'] ?? '') == 'tunai' ? 'selected' : '' ?>>Tunai</option>
                                            <option value="cek" <?= old('metode_pembayaran', $invoice['metode_pembayaran'] ?? '') == 'cek' ? 'selected' : '' ?>>Cek</option>
                                            <option value="giro" <?= old('metode_pembayaran', $invoice['metode_pembayaran'] ?? '') == 'giro' ? 'selected' : '' ?>>Giro</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card border">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-project-diagram me-2"></i>Informasi Project</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="project_id" class="form-label">Project <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select class="form-select" id="project_id" name="project_id" required>
                            <option value="">Pilih Project</option>
                            <?php foreach ($projectOptions as $project): ?>
                                <option value="<?= $project['id'] ?>" 
                                    <?= $invoice['project_id'] == $project['id'] ? 'selected' : '' ?>
                                    data-client="<?= htmlspecialchars($project['nama_perusahaan']) ?>">
                                    [<?= $project['kode_project'] ?>] <?= $project['nama_project'] ?> - <?= $project['nama_perusahaan'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <a href="<?= base_url('sales/project/create?redirect=' . urlencode(current_url())) ?>" 
                           class="btn btn-outline-primary" target="_blank" id="newProjectBtn">
                            <i class="fas fa-plus"></i> Baru
                        </a>
                        <button type="button" class="btn btn-outline-secondary" id="refreshProjectsBtn" title="Refresh daftar project">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                    <div class="form-text mt-2">
                        Project yang sedang dipilih: 
                        <strong><?= $invoice['nama_project'] ?? 'Tidak ada' ?></strong>
                    </div>
                </div>
                
                <div class="col-md-12 mb-3">
                    <label for="penawaran_id" class="form-label">Berdasarkan Penawaran (Opsional)</label>
                    <select class="form-select" id="penawaran_id" name="penawaran_id">
                        <option value="">Pilih Penawaran</option>
                        <?php foreach ($penawaranOptions as $penawaran): ?>
                            <option value="<?= $penawaran['id'] ?>" 
                                <?= $invoice['penawaran_id'] == $penawaran['id'] ? 'selected' : '' ?>
                                data-project="<?= $penawaran['project_id'] ?>">
                                <?= $penawaran['nomor_penawaran'] ?> - <?= $penawaran['nama_project'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">
                        <?php if ($invoice['penawaran_id']): ?>
                            Penawaran yang dipilih: <strong><?= $invoice['nomor_penawaran'] ?? '-' ?></strong>
                        <?php else: ?>
                            Pilih penawaran untuk mengisi item secara otomatis
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-12 mb-3">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <textarea class="form-control" id="keterangan" name="keterangan" 
                              rows="3"><?= $invoice['keterangan'] ?? '' ?></textarea>
                    <div class="form-text">Catatan tambahan untuk invoice</div>
                </div>
            </div>
        </div>
    </div>
</div>
                </div>

                <!-- Items Section -->
                <div class="card border mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fas fa-list me-2"></i>Item Invoice</h6>
                        <button type="button" class="btn btn-sm btn-primary" id="addItemBtn">
                            <i class="fas fa-plus me-1"></i> Tambah Item
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="itemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="30%">Nama Item <span class="text-danger">*</span></th>
                                        <th width="20%">Deskripsi</th>
                                        <th width="10%">Qty <span class="text-danger">*</span></th>
                                        <th width="10%">Satuan</th>
                                        <th width="15%">Harga Satuan (Rp) <span class="text-danger">*</span></th>
                                        <th width="15%">Subtotal (Rp)</th>
                                        <th width="5%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsBody">
                                    <?php foreach ($items as $index => $item): ?>
                                    <tr class="item-row">
                                        <td class="item-number text-center"><?= $index + 1 ?></td>
                                        <td>
                                            <input type="text" class="form-control item-name" 
                                                   name="items[<?= $index ?>][nama_item]" 
                                                   value="<?= htmlspecialchars($item['nama_item']) ?>" required>
                                        </td>
                                        <td>
                                            <textarea class="form-control item-desc" 
                                                      name="items[<?= $index ?>][deskripsi]" 
                                                      rows="1"><?= htmlspecialchars($item['deskripsi'] ?? '') ?></textarea>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control item-qty" 
                                                   name="items[<?= $index ?>][qty]" 
                                                   step="0.01" min="0" value="<?= $item['qty'] ?>" required>
                                        </td>
                                        <td>
                                            <select class="form-select item-unit" name="items[<?= $index ?>][satuan]">
                                                <option value="unit" <?= ($item['satuan'] ?? 'unit') == 'unit' ? 'selected' : '' ?>>Unit</option>
                                                <option value="pcs" <?= ($item['satuan'] ?? '') == 'pcs' ? 'selected' : '' ?>>Pcs</option>
                                                <option value="set" <?= ($item['satuan'] ?? '') == 'set' ? 'selected' : '' ?>>Set</option>
                                                <option value="lot" <?= ($item['satuan'] ?? '') == 'lot' ? 'selected' : '' ?>>Lot</option>
                                                <option value="jam" <?= ($item['satuan'] ?? '') == 'jam' ? 'selected' : '' ?>>Jam</option>
                                                <option value="hari" <?= ($item['satuan'] ?? '') == 'hari' ? 'selected' : '' ?>>Hari</option>
                                                <option value="bulan" <?= ($item['satuan'] ?? '') == 'bulan' ? 'selected' : '' ?>>Bulan</option>
                                                <option value="meter" <?= ($item['satuan'] ?? '') == 'meter' ? 'selected' : '' ?>>Meter</option>
                                                <option value="liter" <?= ($item['satuan'] ?? '') == 'liter' ? 'selected' : '' ?>>Liter</option>
                                                <option value="kg" <?= ($item['satuan'] ?? '') == 'kg' ? 'selected' : '' ?>>Kg</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control text-end item-price" 
                                                   name="items[<?= $index ?>][harga_satuan]" 
                                                   value="<?= formatRupiah($item['harga_satuan']) ?>" required>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control text-end item-subtotal" 
                                                   value="<?= formatRupiah($item['subtotal']) ?>" readonly>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-danger remove-item" title="Hapus item">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6" class="text-end fw-bold">SUB TOTAL</td>
                                        <td>
                                            <input type="text" class="form-control text-end fw-bold" 
                                                   id="subTotal" value="<?= formatRupiah($total) ?>" readonly>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="text-end fw-bold">PPN 11%</td>
                                        <td>
                                            <input type="text" class="form-control text-end fw-bold" 
                                                   id="ppn" value="<?= formatRupiah($total * 0.11) ?>" readonly>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr class="table-primary">
                                        <td colspan="6" class="text-end fw-bold fs-6">GRAND TOTAL</td>
                                        <td>
                                            <input type="text" class="form-control text-end fw-bold fs-6 text-primary" 
                                                   id="grandTotal" value="<?= formatRupiah($total * 1.11) ?>" readonly>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Payment History -->
                <div class="card border mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Pembayaran</h6>
                        <?php if ($remaining > 0): ?>
                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                            <i class="fas fa-plus me-1"></i> Tambah Pembayaran
                        </button>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if (empty($payments)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada riwayat pembayaran</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No. Pembayaran</th>
                                            <th>Tanggal</th>
                                            <th>Jumlah</th>
                                            <th>Metode</th>
                                            <th>Bank/Referensi</th>
                                            <th>Dibuat Oleh</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($payments as $payment): ?>
                                        <tr>
                                            <td><?= $payment['nomor_pembayaran'] ?></td>
                                            <td><?= date('d/m/Y', strtotime($payment['tanggal_bayar'])) ?></td>
                                            <td class="fw-bold">Rp <?= formatRupiah($payment['jumlah_bayar']) ?></td>
                                            <td>
                                                <?php 
                                                $methodColors = [
                                                    'transfer' => 'primary',
                                                    'tunai' => 'success',
                                                    'cek' => 'warning',
                                                    'giro' => 'info'
                                                ];
                                                ?>
                                                <span class="badge bg-<?= $methodColors[$payment['metode_bayar']] ?? 'secondary' ?>">
                                                    <?= ucfirst($payment['metode_bayar']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?= $payment['bank'] ? $payment['bank'] . ' ' : '' ?>
                                                <?= $payment['no_referensi'] ? '(' . $payment['no_referensi'] . ')' : '' ?>
                                            </td>
                                            <td><?= $payment['created_by_name'] ?? '-' ?></td>
                                            <td>
                                              <form action="<?= base_url('sales/invoice/delete-payment/' . $payment['id']) ?>" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Hapus pembayaran ini?')">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="2" class="text-end fw-bold">Total Dibayar:</td>
                                            <td class="fw-bold text-success">Rp <?= formatRupiah($paymentSummary) ?></td>
                                            <td colspan="4"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="card border mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-credit-card me-2"></i>Informasi Pembayaran</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Bank:</strong> Mandiri</p>
                                <p class="mb-2"><strong>No. Rekening:</strong> 101.000.676.6073</p>
                                <p class="mb-0"><strong>Atas Nama:</strong> PT. CIPTA DUTA WACANA</p>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <small>Informasi pembayaran ini akan dicantumkan pada invoice yang dicetak.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-footer bg-light py-3">
                <div class="d-flex justify-content-between">
                    <a href="<?= base_url('sales/invoice/detail/' . $invoice['id']) ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Detail
                    </a>
                    <div>
                        <button type="button" class="btn btn-outline-primary me-2" id="previewBtn">
                            <i class="fas fa-eye me-1"></i> Preview
                        </button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </form>
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
          <form action="<?= base_url('sales/invoice/add-payment/' . $invoice['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Sisa tagihan: <strong>Rp <?= formatRupiah($remaining) ?></strong>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_bayar" class="form-label">Tanggal Bayar <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal_bayar" name="tanggal_bayar" 
                                   value="<?= date('Y-m-d') ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="jumlah_bayar" class="form-label">Jumlah Bayar (Rp) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="jumlah_bayar" name="jumlah_bayar" 
                                   value="<?= formatRupiah($remaining) ?>" required>
                            <div class="form-text">Maksimal: Rp <?= formatRupiah($remaining) ?></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="metode_bayar" class="form-label">Metode Bayar <span class="text-danger">*</span></label>
                            <select class="form-select" id="metode_bayar" name="metode_bayar" required>
                                <option value="">Pilih Metode</option>
                                <option value="transfer">Transfer Bank</option>
                                <option value="tunai">Tunai</option>
                                <option value="cek">Cek</option>
                                <option value="giro">Giro</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="bank" class="form-label">Bank (Opsional)</label>
                            <input type="text" class="form-control" id="bank" name="bank">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="no_referensi" class="form-label">No. Referensi (Opsional)</label>
                            <input type="text" class="form-control" id="no_referensi" name="no_referensi">
                            <div class="form-text">No. transfer/cek/giro</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                            <input type="text" class="form-control" id="keterangan" name="keterangan">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Template untuk item row baru -->
<template id="itemTemplate">
    <tr class="item-row">
        <td class="item-number text-center">1</td>
        <td>
            <input type="text" class="form-control item-name" name="items[0][nama_item]" required>
        </td>
        <td>
            <textarea class="form-control item-desc" name="items[0][deskripsi]" rows="1"></textarea>
        </td>
        <td>
            <input type="number" class="form-control item-qty" name="items[0][qty]" 
                   step="0.01" min="0" value="1" required>
        </td>
        <td>
            <select class="form-select item-unit" name="items[0][satuan]">
                <option value="unit" selected>Unit</option>
                <option value="pcs">Pcs</option>
                <option value="set">Set</option>
                <option value="lot">Lot</option>
                <option value="jam">Jam</option>
                <option value="hari">Hari</option>
                <option value="bulan">Bulan</option>
                <option value="meter">Meter</option>
                <option value="liter">Liter</option>
                <option value="kg">Kg</option>
            </select>
        </td>
        <td>
            <input type="text" class="form-control text-end item-price" 
                   name="items[0][harga_satuan]" value="0" required>
        </td>
        <td>
            <input type="text" class="form-control text-end item-subtotal" readonly>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger remove-item" title="Hapus item">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
</template>

<style>
.card {
    border-radius: 10px;
    overflow: hidden;
}

.table th {
    font-weight: 600;
    font-size: 0.85rem;
    background-color: #f8f9fa;
}

.form-control:readonly {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

#itemsTable input, #itemsTable select, #itemsTable textarea {
    font-size: 0.9rem;
    border-radius: 4px;
}

.item-row {
    vertical-align: middle;
}

#itemsTable tfoot tr:last-child {
    background-color: #e7f3ff;
}

.progress {
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
}

.badge {
    font-size: 0.85em;
    padding: 0.4em 0.8em;
}

.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    min-width: 300px;
    max-width: 400px;
    animation: slideIn 0.3s ease-out;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemCounter = <?= count($items) ?>;
    
    // Initialize currency formatting
    function formatCurrency(value) {
        if (!value && value !== 0) return '0';
        return new Intl.NumberFormat('id-ID').format(value);
    }
    
    function parseCurrency(value) {
        if (!value) return 0;
        return parseFloat(value.toString().replace(/[^\d]/g, '')) || 0;
    }
    
    // Function to show notification
    function showNotification(message, type = 'info', duration = 5000) {
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        }[type] || 'alert-info';
        
        const icon = {
            'success': 'fa-check-circle',
            'error': 'fa-exclamation-circle',
            'warning': 'fa-exclamation-triangle',
            'info': 'fa-info-circle'
        }[type] || 'fa-info-circle';
        
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert ${alertClass} alert-dismissible fade show notification`;
        notification.setAttribute('role', 'alert');
        
        notification.innerHTML = `
            <i class="fas ${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        // Add to body
        document.body.appendChild(notification);
        
        // Auto remove after duration
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, duration);
    }
    
    // Function to refresh project list
    async function refreshProjects() {
        const projectSelect = document.getElementById('project_id');
        const refreshBtn = document.getElementById('refreshProjectsBtn');
        const currentValue = projectSelect.value;
        
        if (!refreshBtn) return;
        
        // Show loading
        const originalHtml = refreshBtn.innerHTML;
        refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        refreshBtn.disabled = true;
        
        try {
            // Fetch updated project list
            const response = await fetch('<?= base_url('sales/invoice/get-project-options') ?>');
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const data = await response.json();
            
            if (data.success && data.projects) {
                // Save the text of currently selected option
                const selectedText = projectSelect.options[projectSelect.selectedIndex]?.text || '';
                
                // Clear existing options except the first one
                projectSelect.innerHTML = '<option value="">Pilih Project</option>';
                
                // Add new options
                data.projects.forEach(project => {
                    const option = document.createElement('option');
                    option.value = project.id;
                    option.textContent = `[${project.kode_project}] ${project.nama_project} - ${project.nama_perusahaan}`;
                    option.setAttribute('data-client', project.nama_perusahaan);
                    
                    // Try to restore selected value
                    if (project.id == currentValue || 
                        option.textContent === selectedText) {
                        option.selected = true;
                    }
                    
                    projectSelect.appendChild(option);
                });
                
                // If nothing selected, try to select first project
                if (!projectSelect.value && projectSelect.options.length > 1) {
                    projectSelect.options[1].selected = true;
                }
                
                showNotification('Daftar project berhasil diperbarui', 'success');
            } else {
                showNotification('Gagal memuat daftar project', 'error');
            }
        } catch (error) {
            console.error('Error refreshing projects:', error);
            showNotification('Gagal memuat daftar project: ' + error.message, 'error');
        } finally {
            // Restore button
            refreshBtn.innerHTML = originalHtml;
            refreshBtn.disabled = false;
        }
    }
    
    // Add item row
    function addItemRow(data = {}) {
        const template = document.getElementById('itemTemplate');
        if (!template) {
            console.error('Item template not found!');
            showNotification('Template item tidak ditemukan', 'error');
            return;
        }
        
        const clone = template.content.cloneNode(true);
        const row = clone.querySelector('.item-row');
        
        // Update indices
        const index = itemCounter;
        row.querySelectorAll('[name]').forEach(input => {
            const name = input.getAttribute('name');
            input.setAttribute('name', name.replace('[0]', `[${index}]`));
        });
        
        // Set item number
        row.querySelector('.item-number').textContent = index + 1;
        
        // Fill data if provided
        if (data.nama_item) {
            row.querySelector('.item-name').value = data.nama_item;
        }
        if (data.deskripsi) {
            row.querySelector('.item-desc').value = data.deskripsi;
        }
        if (data.qty) {
            row.querySelector('.item-qty').value = data.qty;
        }
        if (data.satuan) {
            row.querySelector('.item-unit').value = data.satuan;
        }
        if (data.harga_satuan) {
            row.querySelector('.item-price').value = formatCurrency(data.harga_satuan);
        }
        
        // Add to table
        const itemsBody = document.getElementById('itemsBody');
        if (itemsBody) {
            itemsBody.appendChild(row);
            
            // Update counters and calculate
            itemCounter++;
            updateItemNumbers();
            calculateSubtotal(row);
            
            // Add event listeners
            const qtyInput = row.querySelector('.item-qty');
            const priceInput = row.querySelector('.item-price');
            
            if (qtyInput) {
                qtyInput.addEventListener('input', () => calculateSubtotal(row));
                qtyInput.addEventListener('blur', function() {
                    if (!this.value || parseFloat(this.value) <= 0) {
                        this.value = 1;
                        calculateSubtotal(row);
                    }
                });
            }
            
            if (priceInput) {
                priceInput.addEventListener('focus', function() {
                    this.value = parseCurrency(this.value);
                });
                
                priceInput.addEventListener('input', function() {
                    this.value = formatCurrency(parseCurrency(this.value));
                    calculateSubtotal(row);
                });
                
                priceInput.addEventListener('blur', function() {
                    if (!this.value || parseCurrency(this.value) <= 0) {
                        this.value = formatCurrency(0);
                        calculateSubtotal(row);
                    } else {
                        this.value = formatCurrency(parseCurrency(this.value));
                    }
                });
            }
            
            // Remove button
            const removeBtn = row.querySelector('.remove-item');
            if (removeBtn) {
                removeBtn.addEventListener('click', function() {
                    const itemRows = document.querySelectorAll('.item-row');
                    if (itemRows.length > 1) {
                        if (confirm('Hapus item ini?')) {
                            row.remove();
                            updateItemNumbers();
                            calculateTotals();
                            showNotification('Item berhasil dihapus', 'success', 2000);
                        }
                    } else {
                        showNotification('Invoice harus memiliki minimal 1 item', 'warning');
                    }
                });
            }
        }
    }
    
    // Update item numbers
    function updateItemNumbers() {
        document.querySelectorAll('.item-row').forEach((row, index) => {
            const numberCell = row.querySelector('.item-number');
            if (numberCell) {
                numberCell.textContent = index + 1;
            }
        });
    }
    
    // Calculate subtotal for a row
    function calculateSubtotal(row) {
        const qtyInput = row.querySelector('.item-qty');
        const priceInput = row.querySelector('.item-price');
        const subtotalInput = row.querySelector('.item-subtotal');
        
        if (!qtyInput || !priceInput || !subtotalInput) return;
        
        const qty = parseFloat(qtyInput.value) || 0;
        const price = parseCurrency(priceInput.value);
        const subtotal = qty * price;
        
        subtotalInput.value = formatCurrency(subtotal);
        calculateTotals();
    }
    
    // Calculate totals
    function calculateTotals() {
        let subtotal = 0;
        
        document.querySelectorAll('.item-row').forEach(row => {
            const subtotalInput = row.querySelector('.item-subtotal');
            if (subtotalInput) {
                subtotal += parseCurrency(subtotalInput.value);
            }
        });
        
        const ppn = subtotal * 0.11;
        const grandTotal = subtotal + ppn;
        
        const subTotalInput = document.getElementById('subTotal');
        const ppnInput = document.getElementById('ppn');
        const grandTotalInput = document.getElementById('grandTotal');
        
        if (subTotalInput) subTotalInput.value = formatCurrency(subtotal);
        if (ppnInput) ppnInput.value = formatCurrency(ppn);
        if (grandTotalInput) grandTotalInput.value = formatCurrency(grandTotal);
    }
    
    // Load penawaran items
    const penawaranSelect = document.getElementById('penawaran_id');
    if (penawaranSelect) {
        penawaranSelect.addEventListener('change', function() {
            const penawaranId = this.value;
            const selectedOption = this.options[this.selectedIndex];
            const projectId = selectedOption ? selectedOption.dataset.project : null;
            
            // Set project if not already set
            const projectSelect = document.getElementById('project_id');
            if (projectId && projectSelect && !projectSelect.value) {
                projectSelect.value = projectId;
            }
            
            if (penawaranId) {
                showNotification('Memuat item dari penawaran...', 'info');
                
                fetch(`<?= base_url('sales/invoice/get-penawaran-items/') ?>${penawaranId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success && data.items && data.items.length > 0) {
                            // Clear existing items
                            document.querySelectorAll('.item-row').forEach(row => row.remove());
                            itemCounter = 0;
                            
                            // Add new items from penawaran
                            data.items.forEach(item => {
                                addItemRow({
                                    nama_item: item.nama_item,
                                    deskripsi: item.deskripsi,
                                    qty: item.qty,
                                    satuan: item.satuan,
                                    harga_satuan: item.harga_satuan
                                });
                            });
                            
                            showNotification(`Berhasil memuat ${data.items.length} item dari penawaran`, 'success');
                        } else {
                            showNotification('Tidak ada item ditemukan dalam penawaran ini', 'warning');
                        }
                    })
                    .catch(error => {
                        console.error('Error loading penawaran items:', error);
                        showNotification('Gagal memuat item penawaran: ' + error.message, 'error');
                    });
            }
        });
    }
    
    // Add item button
    const addItemBtn = document.getElementById('addItemBtn');
    if (addItemBtn) {
        addItemBtn.addEventListener('click', function() {
            addItemRow();
            showNotification('Item baru ditambahkan', 'success', 2000);
        });
    }
    
    // Initialize existing items with event listeners
    document.querySelectorAll('.item-row').forEach(row => {
        const qtyInput = row.querySelector('.item-qty');
        const priceInput = row.querySelector('.item-price');
        const removeBtn = row.querySelector('.remove-item');
        
        if (qtyInput) {
            qtyInput.addEventListener('input', () => calculateSubtotal(row));
        }
        
        if (priceInput) {
            priceInput.addEventListener('focus', function() {
                this.value = parseCurrency(this.value);
            });
            
            priceInput.addEventListener('input', function() {
                this.value = formatCurrency(parseCurrency(this.value));
                calculateSubtotal(row);
            });
            
            priceInput.addEventListener('blur', function() {
                this.value = formatCurrency(parseCurrency(this.value));
            });
        }
        
        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                const itemRows = document.querySelectorAll('.item-row');
                if (itemRows.length > 1) {
                    if (confirm('Hapus item ini?')) {
                        row.remove();
                        updateItemNumbers();
                        calculateTotals();
                        showNotification('Item berhasil dihapus', 'success', 2000);
                    }
                } else {
                    showNotification('Invoice harus memiliki minimal 1 item', 'warning');
                }
            });
        }
    });
    
    // Refresh projects button
    const refreshBtn = document.getElementById('refreshProjectsBtn');
    const refreshLink = document.getElementById('refreshProjectsLink');
    
    if (refreshBtn) {
        refreshBtn.addEventListener('click', refreshProjects);
    }
    
    if (refreshLink) {
        refreshLink.addEventListener('click', refreshProjects);
    }
    
    // Form submission validation
    const invoiceForm = document.getElementById('invoiceForm');
    if (invoiceForm) {
        invoiceForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate project
            const projectSelect = document.getElementById('project_id');
            if (!projectSelect || !projectSelect.value) {
                showNotification('Pilih project terlebih dahulu', 'error');
                projectSelect.focus();
                return;
            }
            
            // Validate items
            const itemRows = document.querySelectorAll('.item-row');
            if (itemRows.length === 0) {
                showNotification('Invoice harus memiliki minimal 1 item', 'error');
                return;
            }
            
            // Validate each item
            let valid = true;
            document.querySelectorAll('.item-row').forEach((row, index) => {
                const name = row.querySelector('.item-name').value.trim();
                const qty = row.querySelector('.item-qty').value;
                const price = row.querySelector('.item-price').value;
                
                if (!name) {
                    showNotification(`Item #${index + 1}: Nama item harus diisi`, 'error');
                    row.querySelector('.item-name').focus();
                    valid = false;
                }
                
                if (!qty || parseFloat(qty) <= 0) {
                    showNotification(`Item #${index + 1}: Quantity harus lebih dari 0`, 'error');
                    row.querySelector('.item-qty').focus();
                    valid = false;
                }
                
                if (!price || parseCurrency(price) <= 0) {
                    showNotification(`Item #${index + 1}: Harga harus lebih dari 0`, 'error');
                    row.querySelector('.item-price').focus();
                    valid = false;
                }
            });
            
            if (!valid) return;
            
            // Format currency inputs
            document.querySelectorAll('.item-price').forEach(input => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = input.name;
                hiddenInput.value = parseCurrency(input.value);
                input.parentNode.appendChild(hiddenInput);
                
                // Remove original input name to avoid duplicate
                input.removeAttribute('name');
            });
            
            // Show loading on submit button
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn) {
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';
                submitBtn.disabled = true;
                
                // Restore button after 5 seconds (just in case)
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 5000);
            }
            
            // Submit form
            this.submit();
        });
    }
    
    // Currency formatting for payment modal
    const jumlahBayarInput = document.getElementById('jumlah_bayar');
    if (jumlahBayarInput) {
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
                showNotification('Jumlah pembayaran melebihi sisa tagihan', 'error');
                this.value = formatCurrency(maxAmount);
            }
        });
    }
    
    // Show welcome message
    setTimeout(() => {
        showNotification('Edit invoice dengan hati-hati', 'info', 3000);
    }, 1000);
});

// Dynamic load penawaran based on selected project
const projectSelect = document.getElementById('project_id');
if (projectSelect) {
    projectSelect.addEventListener('change', function() {
        const projectId = this.value;
        const penawaranSelect = document.getElementById('penawaran_id');
        
        if (!projectId) {
            // Clear penawaran options if no project selected
            penawaranSelect.innerHTML = '<option value="">Pilih Penawaran</option>';
            return;
        }
        
        // Show loading
        const originalHtml = penawaranSelect.innerHTML;
        penawaranSelect.innerHTML = '<option value="">Memuat penawaran...</option>';
        penawaranSelect.disabled = true;
        
        // Load penawaran options for selected project
        fetch(`<?= base_url('sales/invoice/get-penawaran-options-by-project/') ?>${projectId}`)
            .then(response => response.json())
            .then(data => {
                penawaranSelect.innerHTML = '<option value="">Pilih Penawaran</option>';
                
                if (data.success && data.penawarans) {
                    data.penawarans.forEach(penawaran => {
                        const option = document.createElement('option');
                        option.value = penawaran.id;
                        option.textContent = `${penawaran.nomor_penawaran} - ${penawaran.nama_project}`;
                        option.setAttribute('data-project', penawaran.project_id);
                        penawaranSelect.appendChild(option);
                    });
                    
                    // Try to keep the previously selected penawaran if it belongs to this project
                    const currentPenawaranId = '<?= $invoice['penawaran_id'] ?? 0 ?>';
                    if (currentPenawaranId) {
                        for (let option of penawaranSelect.options) {
                            if (option.value == currentPenawaranId) {
                                option.selected = true;
                                break;
                            }
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Error loading penawaran options:', error);
                penawaranSelect.innerHTML = '<option value="">Gagal memuat penawaran</option>';
            })
            .finally(() => {
                penawaranSelect.disabled = false;
            });
    });
    
    // Trigger change event on page load to load penawaran for current project
    setTimeout(() => {
        if (projectSelect.value) {
            projectSelect.dispatchEvent(new Event('change'));
        }
    }, 500);
}
</script>