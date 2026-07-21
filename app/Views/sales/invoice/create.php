<?php
$title = $title ?? 'Buat Invoice Baru';
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
                    <i class="fas fa-file-invoice me-3"></i>
                    <?= $title ?>
                </h1>
                <p class="lead text-muted">
                    <?= $subtitle ?? 'Form pembuatan invoice baru' ?>
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

    <!-- Main Form Card -->
    <div class="card border-0 shadow-lg">
        <div class="card-header bg-light py-3">
            <h5 class="mb-0">
                <i class="fas fa-edit me-2"></i>
                Form Invoice
            </h5>
        </div>
        
        <form action="<?= base_url('sales/invoice/store') ?>" method="POST" id="invoiceForm">
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
                                               name="nomor_invoice" value="<?= old('nomor_invoice', $nomorInvoice ?? '') ?>" 
                                               required readonly>
                                        <div class="form-text">Nomor invoice di-generate otomatis</div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="tanggal_invoice" class="form-label">Tanggal Invoice <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="tanggal_invoice" 
                                               name="tanggal_invoice" value="<?= old('tanggal_invoice', date('Y-m-d')) ?>" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="tanggal_jatuh_tempo" class="form-label">Jatuh Tempo <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="tanggal_jatuh_tempo" 
                                               name="tanggal_jatuh_tempo" value="<?= old('tanggal_jatuh_tempo', $defaultJatuhTempo ?? date('Y-m-d', strtotime('+14 days'))) ?>" required>
                                    </div>
                                    
                                    <div class="col-md-12 mb-3">
                                        <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                                        <select class="form-select" id="metode_pembayaran" name="metode_pembayaran">
                                            <option value="">Pilih Metode</option>
                                            <option value="transfer" <?= old('metode_pembayaran') == 'transfer' ? 'selected' : '' ?>>Transfer Bank</option>
                                            <option value="tunai" <?= old('metode_pembayaran') == 'tunai' ? 'selected' : '' ?>>Tunai</option>
                                            <option value="cek" <?= old('metode_pembayaran') == 'cek' ? 'selected' : '' ?>>Cek</option>
                                            <option value="giro" <?= old('metode_pembayaran') == 'giro' ? 'selected' : '' ?>>Giro</option>
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
                                                        <?= old('project_id') == $project['id'] ? 'selected' : '' ?>
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
                                            • <a href="<?= base_url('sales/project/create?redirect=' . urlencode(current_url())) ?>" 
                                               target="_blank" class="text-primary">
                                                <i class="fas fa-external-link-alt me-1"></i>Buat project baru
                                            </a>
                                            • <button type="button" class="btn btn-link text-primary p-0" id="refreshProjectsLink">
                                                <i class="fas fa-redo me-1"></i>Refresh daftar project
                                            </button>
                                            <br>• Hanya project dengan status "deal" yang ditampilkan
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-12 mb-3">
                                        <label for="penawaran_id" class="form-label">Berdasarkan Penawaran (Opsional)</label>
                                        <select class="form-select" id="penawaran_id" name="penawaran_id">
                                            <option value="">Pilih Penawaran</option>
                                            <?php foreach ($penawaranOptions as $penawaran): ?>
                                                <option value="<?= $penawaran['id'] ?>" 
                                                    <?= old('penawaran_id') == $penawaran['id'] ? 'selected' : '' ?>
                                                    data-project="<?= $penawaran['project_id'] ?>">
                                                    <?= $penawaran['nomor_penawaran'] ?> - <?= $penawaran['nama_project'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="form-text">Pilih penawaran untuk mengisi item secara otomatis</div>
                                    </div>
                                    
                                    <div class="col-md-12 mb-3">
                                        <label for="keterangan" class="form-label">Keterangan</label>
                                        <textarea class="form-control" id="keterangan" name="keterangan" 
                                                  rows="3"><?= old('keterangan') ?></textarea>
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
                                    <!-- Items will be added here -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6" class="text-end fw-bold">SUB TOTAL</td>
                                        <td>
                                            <input type="text" class="form-control text-end fw-bold" 
                                                   id="subTotal" value="0" readonly>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="text-end fw-bold">PPN 11%</td>
                                        <td>
                                            <input type="text" class="form-control text-end fw-bold" 
                                                   id="ppn" value="0" readonly>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr class="table-primary">
                                        <td colspan="6" class="text-end fw-bold fs-6">GRAND TOTAL</td>
                                        <td>
                                            <input type="text" class="form-control text-end fw-bold fs-6 text-primary" 
                                                   id="grandTotal" value="0" readonly>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
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
                    <a href="<?= base_url('sales/invoice') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                    <div>
                        <button type="button" class="btn btn-outline-primary me-2" id="previewBtn">
                            <i class="fas fa-eye me-1"></i> Preview
                        </button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-save me-1"></i> Simpan Invoice
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Template untuk item row -->
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

#itemsTable .form-control {
    border: 1px solid #dee2e6;
}

#itemsTable .form-control:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.remove-item {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

#refreshProjectsBtn:hover, #refreshProjectsLink:hover {
    color: #0a58ca !important;
}

/* Notification style */
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

@keyframes fadeOut {
    from {
        opacity: 1;
    }
    to {
        opacity: 0;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemCounter = 0;
    
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
                notification.style.animation = 'fadeOut 0.3s ease-out';
                setTimeout(() => notification.remove(), 300);
            }
        }, duration);
    }
    
    // Check if project was just created from URL parameter
    function checkNewProject() {
        const urlParams = new URLSearchParams(window.location.search);
        const newProjectId = urlParams.get('new_project_id');
        const newProjectName = urlParams.get('new_project_name');
        
        if (newProjectId && newProjectName) {
            // Show success message
            showNotification(`Project "${decodeURIComponent(newProjectName)}" berhasil dibuat!`, 'success');
            
            // Select the new project
            const projectSelect = document.getElementById('project_id');
            
            // Check if project already exists in dropdown
            let exists = false;
            for (let option of projectSelect.options) {
                if (option.value === newProjectId) {
                    option.selected = true;
                    exists = true;
                    break;
                }
            }
            
            // If not exists, add it
            if (!exists && projectSelect) {
                const option = document.createElement('option');
                option.value = newProjectId;
                option.textContent = decodeURIComponent(newProjectName);
                option.selected = true;
                
                // Add after the first option (empty option)
                projectSelect.add(option, 1);
            }
            
            // Clean URL parameters
            const cleanUrl = window.location.pathname + window.location.search
                .replace(/[?&]new_project_(id|name)=[^&]+/g, '')
                .replace(/^&/, '?')
                .replace(/\?$/, '');
            window.history.replaceState({}, document.title, cleanUrl);
        }
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
        row.querySelector('.item-number').textContent = itemCounter + 1;
        
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
        
        // Update terbilang
        updateTerbilang(grandTotal);
    }
    
    // Update terbilang text
    function updateTerbilang(amount) {
        const terbilangElement = document.getElementById('terbilangText');
        if (!terbilangElement) return;
        
        const terbilang = convertToTerbilang(amount);
        terbilangElement.textContent = terbilang + ' Rupiah';
    }
    
    // Convert number to Indonesian words
    function convertToTerbilang(angka) {
        angka = Math.floor(angka);
        const bilangan = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
        
        if (angka < 12) {
            return bilangan[angka];
        } else if (angka < 20) {
            return convertToTerbilang(angka - 10) + " Belas";
        } else if (angka < 100) {
            return convertToTerbilang(Math.floor(angka / 10)) + " Puluh " + convertToTerbilang(angka % 10);
        } else if (angka < 200) {
            return "Seratus " + convertToTerbilang(angka - 100);
        } else if (angka < 1000) {
            return convertToTerbilang(Math.floor(angka / 100)) + " Ratus " + convertToTerbilang(angka % 100);
        } else if (angka < 2000) {
            return "Seribu " + convertToTerbilang(angka - 1000);
        } else if (angka < 1000000) {
            return convertToTerbilang(Math.floor(angka / 1000)) + " Ribu " + convertToTerbilang(angka % 1000);
        } else if (angka < 1000000000) {
            return convertToTerbilang(Math.floor(angka / 1000000)) + " Juta " + convertToTerbilang(angka % 1000000);
        } else if (angka < 1000000000000) {
            return convertToTerbilang(Math.floor(angka / 1000000000)) + " Miliar " + convertToTerbilang(angka % 1000000000);
        }
        return "";
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
    
    // Refresh projects button
    const refreshBtn = document.getElementById('refreshProjectsBtn');
    const refreshLink = document.getElementById('refreshProjectsLink');
    
    if (refreshBtn) {
        refreshBtn.addEventListener('click', refreshProjects);
    }
    
    if (refreshLink) {
        refreshLink.addEventListener('click', refreshProjects);
    }
    
    // Preview button
    const previewBtn = document.getElementById('previewBtn');
    if (previewBtn) {
        previewBtn.addEventListener('click', function() {
            // Validate form first
            const projectSelect = document.getElementById('project_id');
            const itemRows = document.querySelectorAll('.item-row');
            
            if (!projectSelect || !projectSelect.value) {
                showNotification('Pilih project terlebih dahulu', 'warning');
                projectSelect.focus();
                return;
            }
            
            if (itemRows.length === 0) {
                showNotification('Tambah minimal 1 item', 'warning');
                return;
            }
            
            // Calculate totals
            calculateTotals();
            
            // Show preview info
            const projectName = projectSelect.options[projectSelect.selectedIndex].text;
            const total = document.getElementById('grandTotal').value;
            
            const previewMessage = `
                <strong>Preview Invoice</strong><br>
                Project: ${projectName}<br>
                Total: Rp ${total}<br><br>
                <small>Fitur print preview akan tersedia setelah invoice disimpan.</small>
            `;
            
            // Create preview modal
            const previewModal = document.createElement('div');
            previewModal.className = 'modal fade';
            previewModal.innerHTML = `
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Preview Invoice</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                ${previewMessage}
                            </div>
                            <div id="previewContent"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(previewModal);
            const modal = new bootstrap.Modal(previewModal);
            modal.show();
            
            // Remove modal after close
            previewModal.addEventListener('hidden.bs.modal', function() {
                previewModal.remove();
            });
        });
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
            
            // Add hidden totals
            const subtotal = parseCurrency(document.getElementById('subTotal').value);
            const grandTotal = parseCurrency(document.getElementById('grandTotal').value);
            
            const subtotalInput = document.createElement('input');
            subtotalInput.type = 'hidden';
            subtotalInput.name = 'subtotal';
            subtotalInput.value = subtotal;
            this.appendChild(subtotalInput);
            
            const totalInput = document.createElement('input');
            totalInput.type = 'hidden';
            totalInput.name = 'total';
            totalInput.value = grandTotal;
            this.appendChild(totalInput);
            
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
    
    // Check for new project on page load
    checkNewProject();
    
    // Initialize with one item
    addItemRow();
    
    // Add event listener for tab/window focus to refresh projects
    window.addEventListener('focus', function() {
        // Check URL for new project parameters
        checkNewProject();
    });
    
    // Show welcome message
    setTimeout(() => {
        if (!window.location.search.includes('new_project_id')) {
            showNotification('Isi form invoice dengan lengkap', 'info', 3000);
        }
    }, 1000);
});
</script>