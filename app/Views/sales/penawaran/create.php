<?php
$title = $title ?? 'Buat Penawaran Baru';
$active = $active ?? 'penawaran';
?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <!-- Header Card -->
            <div class="card border-0 shadow-lg mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">
                                <i class="fas fa-file-invoice-dollar me-2"></i>
                                <?= $title ?>
                            </h4>
                            <p class="mb-0 mt-1 small opacity-75"><?= $subtitle ?? 'Form Penawaran Harga Baru' ?></p>
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

                    <!-- Form Buat Penawaran -->
                    <form id="penawaranForm" action="<?= base_url('sales/penawaran/store') ?>" method="POST" class="needs-validation" novalidate>
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
                                                       value="<?= old('nomor_penawaran', $nomorPenawaran ?? '') ?>" 
                                                       required 
                                                       readonly>
                                                <div class="invalid-feedback">
                                                    <?= isset($validation) ? $validation->getError('nomor_penawaran') : 'Nomor penawaran wajib diisi' ?>
                                                </div>
                                                <small class="text-muted">Nomor penawaran di-generate otomatis</small>
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
                                                       value="<?= old('tanggal_penawaran', date('Y-m-d')) ?>" 
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
                                                       value="<?= old('tanggal_kadaluarsa', date('Y-m-d', strtotime('+7 days'))) ?>">
                                                <div class="invalid-feedback">
                                                    <?= isset($validation) ? $validation->getError('tanggal_kadaluarsa') : '' ?>
                                                </div>
                                                <small class="text-muted">Default: 7 hari dari tanggal penawaran</small>
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
                                            <!-- Pilih Project -->
                                            <div class="col-md-6 mb-3">
                                                <label for="project_id" class="form-label">
                                                    <i class="fas fa-tasks me-1"></i> Pilih Project <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select <?= (isset($validation) && $validation->hasError('project_id')) ? 'is-invalid' : '' ?>" 
                                                        id="project_id" 
                                                        name="project_id" 
                                                        required>
                                                    <option value="">-- Pilih Project --</option>
                                                    <?php if (!empty($projectOptions)): ?>
                                                        <?php foreach ($projectOptions as $project): ?>
                                                            <option value="<?= $project['id'] ?>" 
                                                                <?= old('project_id') == $project['id'] ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($project['kode_project']) ?> - 
                                                                <?= htmlspecialchars($project['nama_project']) ?> 
                                                                (<?= htmlspecialchars($project['nama_perusahaan']) ?>)
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                                <div class="invalid-feedback">
                                                    <?= isset($validation) ? $validation->getError('project_id') : 'Project wajib dipilih' ?>
                                                </div>
                                            </div>

                                            <!-- Info Client -->
                                            <div class="col-md-6 mb-3">
                                                <div class="alert alert-info mb-0">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-user-tie fa-2x me-3"></i>
                                                        <div>
                                                            <h6 class="mb-1">Informasi Client</h6>
                                                            <p class="mb-0" id="clientInfo">
                                                                Pilih project untuk melihat informasi client
                                                            </p>
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
                                                    <!-- Items will be added dynamically -->
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
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Klik "Tambah Item" untuk menambahkan barang/jasa yang ditawarkan
                                        </small>
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
                                                          rows="4" 
                                                          placeholder="Tulis keterangan penawaran..."><?= old('keterangan') ?></textarea>
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
                                                          rows="4" 
                                                          placeholder="Tulis catatan khusus..."><?= old('catatan_khusus') ?></textarea>
                                                <div class="invalid-feedback">
                                                    <?= isset($validation) ? $validation->getError('catatan_khusus') : '' ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Informasi Pembuat -->
                            <div class="col-12 mb-4">
                                <div class="alert alert-secondary">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-check fa-2x me-3"></i>
                                        <div>
                                            <h6 class="mb-1">Informasi Pembuat</h6>
                                            <p class="mb-0">
                                                Penawaran ini dibuat oleh: <strong><?= htmlspecialchars($user['name'] ?? 'Anda') ?></strong>
                                            </p>
                                            <p class="mb-0 small text-muted">
                                                Status awal penawaran akan otomatis di-set sebagai <span class="badge bg-secondary">DRAFT</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between pt-3 border-top">
                            <a href="<?= base_url('sales/penawaran') ?>" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>
                            <div>
                                <button type="submit" name="action" value="draft" class="btn btn-warning me-2">
                                    <i class="fas fa-save me-1"></i> Simpan sebagai Draft
                                </button>
                                <button type="submit" name="action" value="send" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-1"></i> Simpan & Kirim
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-info-circle me-2"></i>Petunjuk Pengisian</h5>
                    <ul class="mb-0">
                        <li>Kolom dengan tanda <span class="text-danger">*</span> wajib diisi</li>
                        <li>Nomor penawaran di-generate otomatis oleh sistem</li>
                        <li>Tanggal kadaluarsa default adalah 7 hari dari tanggal penawaran</li>
                        <li>Untuk item penawaran, pastikan mengisi minimal 1 item</li>
                        <li>Simpan sebagai <strong>DRAFT</strong> untuk menyimpan sementara</li>
                        <li>Simpan & <strong>KIRIM</strong> untuk langsung mengirim ke client</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize item counter
let itemCounter = 0;

// Add new item row
function addItem() {
    itemCounter++;
    const row = `
        <tr id="itemRow${itemCounter}">
            <td>${itemCounter}</td>
            <td>
                <input type="text" 
                       class="form-control form-control-sm item-name" 
                       name="items[${itemCounter}][nama_item]" 
                       required
                       placeholder="Nama item...">
            </td>
            <td>
                <textarea class="form-control form-control-sm" 
                          name="items[${itemCounter}][deskripsi]" 
                          rows="1"
                          placeholder="Deskripsi..."></textarea>
            </td>
            <td>
                <input type="number" 
                       class="form-control form-control-sm item-qty" 
                       name="items[${itemCounter}][qty]" 
                       min="0.01" 
                       step="0.01" 
                       value="1"
                       required>
            </td>
            <td>
                <select class="form-select form-select-sm" name="items[${itemCounter}][satuan]">
                    <option value="unit">Unit</option>
                    <option value="pcs">Pcs</option>
                    <option value="set">Set</option>
                    <option value="lot">Lot</option>
                    <option value="jam">Jam</option>
                    <option value="hari">Hari</option>
                    <option value="bulan">Bulan</option>
                    <option value="tahun">Tahun</option>
                    <option value="meter">Meter</option>
                    <option value="kg">Kg</option>
                    <option value="liter">Liter</option>
                </select>
            </td>
            <td>
                <input type="number" 
                       class="form-control form-control-sm item-price" 
                       name="items[${itemCounter}][harga_satuan]" 
                       min="0" 
                       step="1000" 
                       value="0"
                       required
                       onchange="calculateTotal()">
            </td>
            <td>
                <span class="item-subtotal" id="subtotal${itemCounter}">0</span>
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
        // Update input names
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

// Get client info when project is selected
$('#project_id').change(function() {
    const projectId = $(this).val();
    if (projectId) {
        // In real implementation, you would fetch this via AJAX
        // For now, we'll just show a message
        $('#clientInfo').html(`
            <i class="fas fa-spinner fa-spin me-1"></i>
            Memuat informasi client...
        `);
        
        // Simulate AJAX call
        setTimeout(() => {
            $('#clientInfo').html(`
                Informasi client akan ditampilkan setelah project dipilih.<br>
                <small class="text-muted">Fitur ini akan ditampilkan setelah integrasi API</small>
            `);
        }, 500);
    } else {
        $('#clientInfo').text('Pilih project untuk melihat informasi client');
    }
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

// Initialize with one item
document.addEventListener('DOMContentLoaded', function() {
    addItem();
    
    // Set default dates
    const today = new Date().toISOString().split('T')[0];
    const nextWeek = new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
    
    document.getElementById('tanggal_penawaran').value = today;
    document.getElementById('tanggal_kadaluarsa').value = nextWeek;
});
</script>