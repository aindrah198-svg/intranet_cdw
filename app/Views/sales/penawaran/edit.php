<?php
$title = $title ?? 'Edit Penawaran';
$active = $active ?? 'penawaran';
?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <!-- Header Card -->
            <div class="card border-0 shadow-lg mb-4">
                <div class="card-header bg-warning text-dark py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">
                                <i class="fas fa-edit me-2"></i>
                                <?= $title ?>
                            </h4>
                            <p class="mb-0 mt-1 small opacity-75"><?= $subtitle ?? 'Form Edit Penawaran Harga' ?></p>
                        </div>
                        <a href="<?= base_url('sales/penawaran') ?>" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
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

                    <?php if (isset($errors) && !empty($errors)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Terjadi Kesalahan</h5>
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (!isset($penawaran) || !$penawaran): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-exclamation-triangle fa-4x text-warning mb-3"></i>
                            <h4>Data Penawaran Tidak Ditemukan</h4>
                            <p class="text-muted">Penawaran yang ingin diedit tidak ditemukan dalam sistem.</p>
                            <a href="<?= base_url('sales/penawaran') ?>" class="btn btn-primary">
                                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Penawaran
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- Status Badge -->
                        <div class="alert alert-info mb-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle fa-2x me-3"></i>
                                <div>
                                    <h6 class="mb-1">Mengedit Penawaran</h6>
                                    <p class="mb-0">
                                        Anda sedang mengedit penawaran: <strong><?= htmlspecialchars($penawaran['nomor_penawaran']) ?></strong>
                                        - Status: <span class="badge bg-<?= $penawaran['status'] == 'draft' ? 'secondary' : 'warning' ?>"><?= ucfirst($penawaran['status']) ?></span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Form Edit Penawaran -->
                        <form id="penawaranForm" action="<?= base_url('sales/penawaran/update/' . $penawaran['id']) ?>" method="POST" class="needs-validation" novalidate>
                            <?= csrf_field() ?>
                            
                            <div class="row">
                                <!-- Informasi Penawaran -->
                                <div class="col-12 mb-4">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body">
                                            <h5 class="card-title mb-4">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Informasi Penawaran
                                            </h5>
                                            
                                            <div class="row">
                                                <!-- Nomor Penawaran -->
                                                <div class="col-md-4 mb-3">
                                                    <label for="nomor_penawaran" class="form-label">
                                                        <i class="fas fa-hashtag me-1"></i> Nomor Penawaran <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" 
                                                           class="form-control <?= (isset($validation) && $validation->hasError('nomor_penawaran')) ? 'is-invalid' : '' ?>" 
                                                           id="nomor_penawaran" 
                                                           name="nomor_penawaran" 
                                                           value="<?= old('nomor_penawaran', $penawaran['nomor_penawaran']) ?>" 
                                                           required>
                                                    <div class="invalid-feedback">
                                                        <?= isset($validation) ? $validation->getError('nomor_penawaran') : 'Nomor penawaran wajib diisi' ?>
                                                    </div>
                                                </div>

                                                <!-- Tanggal Penawaran -->
                                                <div class="col-md-4 mb-3">
                                                    <label for="tanggal_penawaran" class="form-label">
                                                        <i class="fas fa-calendar me-1"></i> Tanggal Penawaran <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="date" 
                                                           class="form-control <?= (isset($validation) && $validation->hasError('tanggal_penawaran')) ? 'is-invalid' : '' ?>" 
                                                           id="tanggal_penawaran" 
                                                           name="tanggal_penawaran" 
                                                           value="<?= old('tanggal_penawaran', $penawaran['tanggal_penawaran']) ?>" 
                                                           required>
                                                    <div class="invalid-feedback">
                                                        <?= isset($validation) ? $validation->getError('tanggal_penawaran') : 'Tanggal penawaran wajib diisi' ?>
                                                    </div>
                                                </div>

                                                <!-- Tanggal Kadaluarsa -->
                                                <div class="col-md-4 mb-3">
                                                    <label for="tanggal_kadaluarsa" class="form-label">
                                                        <i class="fas fa-calendar-times me-1"></i> Tanggal Kadaluarsa
                                                    </label>
                                                    <input type="date" 
                                                           class="form-control <?= (isset($validation) && $validation->hasError('tanggal_kadaluarsa')) ? 'is-invalid' : '' ?>" 
                                                           id="tanggal_kadaluarsa" 
                                                           name="tanggal_kadaluarsa" 
                                                           value="<?= old('tanggal_kadaluarsa', $penawaran['tanggal_kadaluarsa']) ?>">
                                                    <div class="invalid-feedback">
                                                        <?= isset($validation) ? $validation->getError('tanggal_kadaluarsa') : '' ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Informasi Project -->
                                <div class="col-12 mb-4">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body">
                                            <h5 class="card-title mb-4">
                                                <i class="fas fa-project-diagram me-2"></i>
                                                Informasi Project
                                            </h5>
                                            
                                            <div class="row">
                                                <!-- Project Info (Read-only) -->
                                                <div class="col-md-6 mb-3">
                                                    <div class="alert alert-secondary mb-0">
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-tasks fa-2x me-3"></i>
                                                            <div>
                                                                <h6 class="mb-1">Informasi Project</h6>
                                                                <p class="mb-0">
                                                                    <strong><?= htmlspecialchars($penawaran['kode_project']) ?></strong> - 
                                                                    <?= htmlspecialchars($penawaran['nama_project']) ?>
                                                                </p>
                                                                <p class="mb-0 small">
                                                                    Client: <?= htmlspecialchars($penawaran['nama_perusahaan']) ?>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Status Info -->
                                                <div class="col-md-6 mb-3">
                                                    <div class="alert alert-<?= $penawaran['status'] == 'draft' ? 'warning' : 'info' ?> mb-0">
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-circle fa-2x me-3"></i>
                                                            <div>
                                                                <h6 class="mb-1">Status Penawaran</h6>
                                                                <p class="mb-0">
                                                                    <strong><?= ucfirst($penawaran['status']) ?></strong>
                                                                </p>
                                                                <?php if ($penawaran['status'] == 'revisi'): ?>
                                                                <p class="mb-0 small">
                                                                    Client meminta revisi penawaran
                                                                </p>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Daftar Item Penawaran -->
                                <div class="col-12 mb-4">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-light">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h5 class="mb-0">
                                                    <i class="fas fa-list-alt me-2"></i>
                                                    Daftar Item Penawaran
                                                </h5>
                                                <button type="button" class="btn btn-sm btn-primary" onclick="addItem()">
                                                    <i class="fas fa-plus me-1"></i> Tambah Item
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table" id="itemsTable">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th width="5%">#</th>
                                                            <th width="30%">Nama Item</th>
                                                            <th width="25%">Deskripsi</th>
                                                            <th width="10%">Qty</th>
                                                            <th width="10%">Satuan</th>
                                                            <th width="15%">Harga Satuan (Rp)</th>
                                                            <th width="15%">Subtotal (Rp)</th>
                                                            <th width="5%">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="itemsBody">
                                                        <!-- Items will be added dynamically from PHP data -->
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="6" class="text-end fw-bold">TOTAL</td>
                                                            <td class="fw-bold">
                                                                <span id="totalAmount">0</span>
                                                                <input type="hidden" id="totalInput" name="total" value="0">
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Keterangan dan Catatan -->
                                <div class="col-12 mb-4">
                                    <div class="row">
                                        <!-- Keterangan -->
                                        <div class="col-md-6 mb-3">
                                            <div class="card h-100 border-0 bg-light">
                                                <div class="card-body">
                                                    <h6 class="card-title mb-3">
                                                        <i class="fas fa-sticky-note me-2"></i>
                                                        Keterangan
                                                    </h6>
                                                    <textarea class="form-control <?= (isset($validation) && $validation->hasError('keterangan')) ? 'is-invalid' : '' ?>" 
                                                              id="keterangan" 
                                                              name="keterangan" 
                                                              rows="4"><?= old('keterangan', $penawaran['keterangan']) ?></textarea>
                                                    <div class="invalid-feedback">
                                                        <?= isset($validation) ? $validation->getError('keterangan') : '' ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Catatan Khusus -->
                                        <div class="col-md-6 mb-3">
                                            <div class="card h-100 border-0 bg-light">
                                                <div class="card-body">
                                                    <h6 class="card-title mb-3">
                                                        <i class="fas fa-exclamation-circle me-2"></i>
                                                        Catatan Khusus
                                                    </h6>
                                                    <textarea class="form-control <?= (isset($validation) && $validation->hasError('catatan_khusus')) ? 'is-invalid' : '' ?>" 
                                                              id="catatan_khusus" 
                                                              name="catatan_khusus" 
                                                              rows="4"><?= old('catatan_khusus', $penawaran['catatan_khusus']) ?></textarea>
                                                    <div class="invalid-feedback">
                                                        <?= isset($validation) ? $validation->getError('catatan_khusus') : '' ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between pt-3 border-top">
                                <div>
                                    <a href="<?= base_url('sales/penawaran/detail/' . $penawaran['id']) ?>" class="btn btn-info">
                                        <i class="fas fa-eye me-1"></i> Lihat Detail
                                    </a>
                                    <a href="<?= base_url('sales/penawaran') ?>" class="btn btn-secondary ms-2">
                                        <i class="fas fa-times me-1"></i> Batal
                                    </a>
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save me-1"></i> Update Penawaran
                                    </button>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize item counter
let itemCounter = 0;

// Load existing items from PHP
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($items)): ?>
        <?php foreach ($items as $index => $item): ?>
            addItem(
                `<?= addslashes($item['nama_item']) ?>`,
                `<?= addslashes($item['deskripsi']) ?>`,
                `<?= $item['qty'] ?>`,
                `<?= $item['satuan'] ?>`,
                `<?= $item['harga_satuan'] ?>`
            );
        <?php endforeach; ?>
    <?php else: ?>
        addItem(); // Add one empty item if no items exist
    <?php endif; ?>
    
    calculateTotal();
});

// Add new item row with optional data
function addItem(name = '', description = '', qty = 1, unit = 'unit', price = 0) {
    itemCounter++;
    const row = `
        <tr id="itemRow${itemCounter}">
            <td>${itemCounter}</td>
            <td>
                <input type="text" 
                       class="form-control form-control-sm item-name" 
                       name="items[${itemCounter}][nama_item]" 
                       value="${name}"
                       required
                       placeholder="Nama item...">
            </td>
            <td>
                <textarea class="form-control form-control-sm" 
                          name="items[${itemCounter}][deskripsi]" 
                          rows="1"
                          placeholder="Deskripsi...">${description}</textarea>
            </td>
            <td>
                <input type="number" 
                       class="form-control form-control-sm item-qty" 
                       name="items[${itemCounter}][qty]" 
                       min="0.01" 
                       step="0.01" 
                       value="${qty}"
                       required>
            </td>
            <td>
                <select class="form-select form-select-sm" name="items[${itemCounter}][satuan]">
                    <option value="unit" ${unit === 'unit' ? 'selected' : ''}>Unit</option>
                    <option value="pcs" ${unit === 'pcs' ? 'selected' : ''}>Pcs</option>
                    <option value="set" ${unit === 'set' ? 'selected' : ''}>Set</option>
                    <option value="lot" ${unit === 'lot' ? 'selected' : ''}>Lot</option>
                    <option value="jam" ${unit === 'jam' ? 'selected' : ''}>Jam</option>
                    <option value="hari" ${unit === 'hari' ? 'selected' : ''}>Hari</option>
                    <option value="bulan" ${unit === 'bulan' ? 'selected' : ''}>Bulan</option>
                    <option value="tahun" ${unit === 'tahun' ? 'selected' : ''}>Tahun</option>
                    <option value="meter" ${unit === 'meter' ? 'selected' : ''}>Meter</option>
                    <option value="kg" ${unit === 'kg' ? 'selected' : ''}>Kg</option>
                    <option value="liter" ${unit === 'liter' ? 'selected' : ''}>Liter</option>
                </select>
            </td>
            <td>
                <input type="number" 
                       class="form-control form-control-sm item-price" 
                       name="items[${itemCounter}][harga_satuan]" 
                       min="0" 
                       step="1000" 
                       value="${price}"
                       required
                       onchange="calculateTotal()">
            </td>
            <td>
                <span class="item-subtotal" id="subtotal${itemCounter}">${(qty * price).toLocaleString('id-ID')}</span>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeItem(${itemCounter})">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        </tr>
    `;
    
    $('#itemsBody').append(row);
    calculateTotal();
}

// Remove item row
function removeItem(id) {
    $(`#itemRow${id}`).remove();
    renumberItems();
    calculateTotal();
}

// Renumber items
function renumberItems() {
    $('#itemsBody tr').each(function(index) {
        $(this).find('td:first').text(index + 1);
        const newIndex = index + 1;
        $(this).find('input, select, textarea').each(function() {
            const name = $(this).attr('name');
            if (name) {
                $(this).attr('name', name.replace(/items\[\d+\]/, `items[${newIndex}]`));
            }
        });
    });
    itemCounter = $('#itemsBody tr').length;
}

// Calculate total
function calculateTotal() {
    let total = 0;
    
    $('#itemsBody tr').each(function() {
        const qty = parseFloat($(this).find('.item-qty').val()) || 0;
        const price = parseFloat($(this).find('.item-price').val()) || 0;
        const subtotal = qty * price;
        
        const rowId = $(this).attr('id').replace('itemRow', '');
        $(`#subtotal${rowId}`).text(subtotal.toLocaleString('id-ID'));
        $(this).find('.item-subtotal').text(subtotal.toLocaleString('id-ID'));
        
        total += subtotal;
    });
    
    $('#totalAmount').text(total.toLocaleString('id-ID'));
    $('#totalInput').val(total);
}

// Format currency on blur
$(document).on('blur', '.item-price', function() {
    const value = parseFloat($(this).val()) || 0;
    $(this).val(value.toLocaleString('id-ID', {maximumFractionDigits: 0}));
});

// Remove formatting on focus
$(document).on('focus', '.item-price', function() {
    const value = $(this).val().replace(/\./g, '');
    $(this).val(value);
});

// Bootstrap validation
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                // Check if there's at least one item
                if (itemCounter === 0) {
                    event.preventDefault();
                    event.stopPropagation();
                    alert('Mohon tambahkan minimal 1 item penawaran');
                    return;
                }
                
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false)
        })
})();
</script>