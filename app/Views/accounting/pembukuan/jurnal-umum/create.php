<?php

$title = $title ?? 'Buat Jurnal Baru';
$active = $active ?? 'bookkeeping';
$subtitle = $subtitle ?? 'Tambah Pencatatan Transaksi Baru';

// Get today's date for default
$today = date('Y-m-d');

// Get COA data - ensure we have data
$coaOptions = $coaOptions ?? [];
?>

<?= $this->include('accounting/templates/header') ?>
<?= $this->include('accounting/templates/sidebar') ?>
<?= $this->include('accounting/templates/navbar') ?>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="page-title mb-1">Buat Jurnal Baru</h2>
                    <p class="page-subtitle text-muted mb-0"><?= $subtitle ?></p>
                </div>
                <div class="btn-group">
                    <a href="<?= site_url('accounting/pembukuan/jurnal-umum') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                    <button type="button" class="btn btn-success" id="autoFillBtn">
                        <i class="fas fa-robot me-1"></i> Auto Fill Dummy Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts Section -->
    <?php if (session()->getFlashdata('error')): ?>
    <div class="row">
        <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <?php endif ?>
    
    <?php if (session()->getFlashdata('errors')): ?>
    <div class="row">
        <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <?php endif ?>

    <!-- Success Message -->
    <?php if (session()->getFlashdata('success')): ?>
    <div class="row">
        <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <?php endif ?>

    <!-- Warning if no COA data -->
    <?php if (empty($coaOptions)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Perhatian:</strong> Belum ada data Chart of Accounts (COA). 
                <a href="<?= site_url('accounting/pembukuan/daftar-akun/create') ?>" class="alert-link">
                    Tambah akun terlebih dahulu
                </a>
                sebelum membuat jurnal.
            </div>
        </div>
    </div>
    <?php endif ?>

    <!-- Jurnal Form -->
    <form id="jurnalForm" method="post" action="<?= site_url('accounting/pembukuan/jurnal-umum/store') ?>">
        <?= csrf_field() ?>
        
        <div class="row">
            <!-- Left Column: Header Info -->
            <div class="col-md-4">
                <div class="modern-card mb-4">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-info-circle me-2"></i> Informasi Jurnal
                    </h5>
                    
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">
                            <i class="fas fa-calendar me-1"></i> Tanggal Jurnal <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" 
                               value="<?= old('tanggal', $today) ?>" required>
                        <div class="form-text">Tanggal pencatatan transaksi</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">
                            <i class="fas fa-file-alt me-1"></i> Keterangan <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3" 
                                  placeholder="Deskripsi transaksi..." required><?= old('keterangan') ?></textarea>
                        <div class="form-text">Jelaskan transaksi dengan singkat dan jelas</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tipe_referensi" class="form-label">
                                <i class="fas fa-tag me-1"></i> Tipe Referensi
                            </label>
                            <select class="form-select" id="tipe_referensi" name="tipe_referensi">
                                <?php foreach ($refTypeOptions as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= old('tipe_referensi') == $value ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="referensi" class="form-label">
                                <i class="fas fa-hashtag me-1"></i> Nomor Referensi
                            </label>
                            <input type="text" class="form-control" id="referensi" name="referensi" 
                                   value="<?= old('referensi') ?>" placeholder="Contoh: INV-001">
                        </div>
                    </div>
                    
                    <!-- Balance Summary -->
                    <div class="alert alert-info mt-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="alert-heading mb-1">Summary Balance</h6>
                                <div class="row">
                                    <div class="col-6">
                                        <small>Total Debit:</small>
                                        <h5 id="totalDebitDisplay" class="text-success mb-0">0.00</h5>
                                    </div>
                                    <div class="col-6">
                                        <small>Total Kredit:</small>
                                        <h5 id="totalKreditDisplay" class="text-warning mb-0">0.00</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center">
                                <div id="balanceStatus" class="badge bg-danger">
                                    <i class="fas fa-times-circle me-1"></i> Unbalanced
                                </div>
                                <div class="small mt-1" id="balanceDifference">Selisih: 0.00</div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100" id="submitBtn" disabled>
                        <i class="fas fa-save me-1"></i> Simpan Jurnal
                    </button>
                </div>
            </div>
            
            <!-- Right Column: Jurnal Details -->
            <div class="col-md-8">
                <div class="modern-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-list me-2"></i> Detail Jurnal
                        </h5>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-success" id="addRowBtn">
                                <i class="fas fa-plus me-1"></i> Tambah Baris
                            </button>
                            <button type="button" class="btn btn-sm btn-warning" id="clearAllBtn">
                                <i class="fas fa-eraser me-1"></i> Clear All
                            </button>
                        </div>
                    </div>
                    
                    <div class="alert alert-light border mb-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            <strong>Panduan:</strong> 
                            <ul class="mb-0 ps-3">
                                <li>Minimal 2 baris (1 debit dan 1 kredit)</li>
                                <li>Total debit dan kredit harus sama (balance)</li>
                                <li>Satu baris tidak boleh memiliki debit dan kredit sekaligus</li>
                                <li>Nilai tidak boleh negatif</li>
                            </ul>
                        </small>
                    </div>
                    
                    <!-- Hidden input for details data -->
                    <input type="hidden" id="detailsData" name="details">
                    
                    <!-- Jurnal Details Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered" id="jurnalDetailsTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="40%">Akun <span class="text-danger">*</span></th>
                                    <th width="15%">Debit</th>
                                    <th width="15%">Kredit</th>
                                    <th width="25%">Keterangan</th>
                                    <th width="5%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="jurnalDetailsBody">
                                <!-- Row 1 -->
                                <tr class="jurnal-row">
                                    <td>
                                        <select class="form-select select-coa" required>
                                            <option value="">Pilih Akun...</option>
                                            <?php if (!empty($coaOptions)): ?>
                                                <?php foreach ($coaOptions as $account): ?>
                                                    <option value="<?= $account['id'] ?>" 
                                                            data-kode="<?= $account['kode_akun'] ?>"
                                                            data-nama="<?= htmlspecialchars($account['nama_akun'], ENT_QUOTES) ?>"
                                                            data-tipe="<?= $account['tipe_akun'] ?>"
                                                            data-saldo="<?= $account['saldo_normal'] ?>">
                                                        <?= $account['kode_akun'] ?> - <?= $account['nama_akun'] ?> (<?= $account['tipe_akun'] ?>)
                                                    </option>
                                                <?php endforeach ?>
                                            <?php endif ?>
                                        </select>
                                        <input type="hidden" class="coa-id" name="coa_id[]">
                                        <input type="hidden" class="coa-kode" name="coa_kode[]">
                                        <input type="hidden" class="coa-nama" name="coa_nama[]">
                                        <div class="mt-1">
                                            <small class="coa-info text-muted"></small>
                                        </div>
                                        <div class="invalid-feedback coa-error" style="display: none;">
                                            Akun harus dipilih
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control debit-input" min="0" step="0.01" 
                                                   placeholder="0.00" data-type="debit" name="debit[]">
                                        </div>
                                        <div class="invalid-feedback debit-error" style="display: none;">
                                            Nilai tidak valid
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control kredit-input" min="0" step="0.01" 
                                                   placeholder="0.00" data-type="kredit" name="kredit[]">
                                        </div>
                                        <div class="invalid-feedback kredit-error" style="display: none;">
                                            Nilai tidak valid
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control row-keterangan" 
                                               placeholder="Keterangan baris..." name="row_keterangan[]">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-danger remove-row-btn" title="Hapus baris">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <!-- Row 2 -->
                                <tr class="jurnal-row">
                                    <td>
                                        <select class="form-select select-coa" required>
                                            <option value="">Pilih Akun...</option>
                                            <?php if (!empty($coaOptions)): ?>
                                                <?php foreach ($coaOptions as $account): ?>
                                                    <option value="<?= $account['id'] ?>" 
                                                            data-kode="<?= $account['kode_akun'] ?>"
                                                            data-nama="<?= htmlspecialchars($account['nama_akun'], ENT_QUOTES) ?>"
                                                            data-tipe="<?= $account['tipe_akun'] ?>"
                                                            data-saldo="<?= $account['saldo_normal'] ?>">
                                                        <?= $account['kode_akun'] ?> - <?= $account['nama_akun'] ?> (<?= $account['tipe_akun'] ?>)
                                                    </option>
                                                <?php endforeach ?>
                                            <?php endif ?>
                                        </select>
                                        <input type="hidden" class="coa-id" name="coa_id[]">
                                        <input type="hidden" class="coa-kode" name="coa_kode[]">
                                        <input type="hidden" class="coa-nama" name="coa_nama[]">
                                        <div class="mt-1">
                                            <small class="coa-info text-muted"></small>
                                        </div>
                                        <div class="invalid-feedback coa-error" style="display: none;">
                                            Akun harus dipilih
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control debit-input" min="0" step="0.01" 
                                                   placeholder="0.00" data-type="debit" name="debit[]">
                                        </div>
                                        <div class="invalid-feedback debit-error" style="display: none;">
                                            Nilai tidak valid
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control kredit-input" min="0" step="0.01" 
                                                   placeholder="0.00" data-type="kredit" name="kredit[]">
                                        </div>
                                        <div class="invalid-feedback kredit-error" style="display: none;">
                                            Nilai tidak valid
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control row-keterangan" 
                                               placeholder="Keterangan baris..." name="row_keterangan[]">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-danger remove-row-btn" title="Hapus baris">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="table-secondary">
                                    <th class="text-end">TOTAL:</th>
                                    <th class="text-end" id="footerTotalDebit">0.00</th>
                                    <th class="text-end" id="footerTotalKredit">0.00</th>
                                    <th colspan="2">
                                        <span id="footerBalanceStatus" class="badge bg-danger">
                                            <i class="fas fa-times-circle me-1"></i> Unbalanced
                                        </span>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <!-- Validation Errors -->
                    <div id="validationErrors" class="alert alert-danger d-none">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Kesalahan Validasi:</strong>
                        <ul id="errorList" class="mb-0"></ul>
                    </div>
                    
                    <!-- Debug Section (hidden by default) -->
                    <div class="mt-3 border-top pt-3 d-none">
                        <h6><i class="fas fa-bug me-2"></i> Debug Data</h6>
                        <textarea id="debugDetails" class="form-control" rows="5" readonly></textarea>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const jurnalForm = document.getElementById('jurnalForm');
    const detailsDataInput = document.getElementById('detailsData');
    const jurnalDetailsBody = document.getElementById('jurnalDetailsBody');
    const addRowBtn = document.getElementById('addRowBtn');
    const clearAllBtn = document.getElementById('clearAllBtn');
    const autoFillBtn = document.getElementById('autoFillBtn');
    const submitBtn = document.getElementById('submitBtn');
    
    // Summary elements
    const totalDebitDisplay = document.getElementById('totalDebitDisplay');
    const totalKreditDisplay = document.getElementById('totalKreditDisplay');
    const balanceStatus = document.getElementById('balanceStatus');
    const balanceDifference = document.getElementById('balanceDifference');
    const footerTotalDebit = document.getElementById('footerTotalDebit');
    const footerTotalKredit = document.getElementById('footerTotalKredit');
    const footerBalanceStatus = document.getElementById('footerBalanceStatus');
    
    // Validation elements
    const validationErrors = document.getElementById('validationErrors');
    const errorList = document.getElementById('errorList');
    
    // Debug element
    const debugDetails = document.getElementById('debugDetails');
    
    // Get COA options from HTML
    const coaOptions = [];
    const coaSelects = document.querySelectorAll('.select-coa');
    if (coaSelects.length > 0) {
        const firstSelect = coaSelects[0];
        Array.from(firstSelect.options).forEach(option => {
            if (option.value) {
                coaOptions.push({
                    id: option.value,
                    kode_akun: option.dataset.kode,
                    nama_akun: option.dataset.nama,
                    tipe_akun: option.dataset.tipe,
                    saldo_normal: option.dataset.saldo
                });
            }
        });
    }
    
    console.log('COA Options available:', coaOptions.length);
    
    // Initialize event listeners for existing rows
    document.querySelectorAll('.jurnal-row').forEach(row => {
        setupRowEventListeners(row);
        validateRow(row);
    });
    
    // Update totals initially
    updateTotals();
    
    // Event Listeners
    addRowBtn.addEventListener('click', addJurnalRow);
    clearAllBtn.addEventListener('click', clearAllRows);
    autoFillBtn.addEventListener('click', autoFillDummyData);
    
    // Auto-dismiss alerts
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            if (alert.classList.contains('alert-dismissible')) {
                const bsAlert = new bootstrap.Alert(alert);
                setTimeout(() => bsAlert.close(), 5000);
            }
        });
    }, 5000);
    
    // Form submission
    jurnalForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        console.log('Form submission started...');
        
        // Hide previous errors
        validationErrors.classList.add('d-none');
        
        if (validateForm()) {
            // Serialize form data
            const formData = serializeForm();
            
            console.log('Serialized data:', formData);
            
            // Debug: Show data being sent
            if (debugDetails) {
                debugDetails.value = JSON.stringify(formData.details, null, 2);
            }
            
            // Set hidden input value
            detailsDataInput.value = JSON.stringify(formData.details);
            
            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';
            submitBtn.disabled = true;
            
            // Submit form
            setTimeout(() => {
                this.submit();
            }, 100);
        }
    });
    
    // Functions
    function addJurnalRow() {
        const rowClone = createRowTemplate();
        const newRow = rowClone.querySelector('tr');
        
        setupRowEventListeners(newRow);
        jurnalDetailsBody.appendChild(newRow);
        updateTotals();
        validateRow(newRow);
        
        // Scroll to new row
        newRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
    
    function createRowTemplate() {
        const template = document.createElement('template');
        template.innerHTML = `
            <tr class="jurnal-row">
                <td>
                    <select class="form-select select-coa" required>
                        <option value="">Pilih Akun...</option>
                        ${coaOptions.map(account => `
                            <option value="${account.id}" 
                                    data-kode="${account.kode_akun}"
                                    data-nama="${account.nama_akun}"
                                    data-tipe="${account.tipe_akun}"
                                    data-saldo="${account.saldo_normal}">
                                ${account.kode_akun} - ${account.nama_akun} (${account.tipe_akun})
                            </option>
                        `).join('')}
                    </select>
                    <input type="hidden" class="coa-id" name="coa_id[]">
                    <input type="hidden" class="coa-kode" name="coa_kode[]">
                    <input type="hidden" class="coa-nama" name="coa_nama[]">
                    <div class="mt-1">
                        <small class="coa-info text-muted"></small>
                    </div>
                    <div class="invalid-feedback coa-error" style="display: none;">
                        Akun harus dipilih
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control debit-input" min="0" step="0.01" 
                               placeholder="0.00" data-type="debit" name="debit[]">
                    </div>
                    <div class="invalid-feedback debit-error" style="display: none;">
                        Nilai tidak valid
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control kredit-input" min="0" step="0.01" 
                               placeholder="0.00" data-type="kredit" name="kredit[]">
                    </div>
                    <div class="invalid-feedback kredit-error" style="display: none;">
                        Nilai tidak valid
                    </div>
                </td>
                <td>
                    <input type="text" class="form-control row-keterangan" 
                           placeholder="Keterangan baris..." name="row_keterangan[]">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger remove-row-btn" title="Hapus baris">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        return template.content;
    }
    
    function setupRowEventListeners(row) {
        const coaSelect = row.querySelector('.select-coa');
        const debitInput = row.querySelector('.debit-input');
        const kreditInput = row.querySelector('.kredit-input');
        const removeBtn = row.querySelector('.remove-row-btn');
        
        coaSelect.addEventListener('change', function() {
            updateCoaInfo(this);
            validateRow(row);
            updateTotals();
        });
        
        debitInput.addEventListener('input', function() {
            if (this.value > 0) {
                row.querySelector('.kredit-input').value = '';
                row.querySelector('.kredit-input').classList.remove('is-invalid');
            }
            validateRow(row);
            updateTotals();
        });
        
        kreditInput.addEventListener('input', function() {
            if (this.value > 0) {
                row.querySelector('.debit-input').value = '';
                row.querySelector('.debit-input').classList.remove('is-invalid');
            }
            validateRow(row);
            updateTotals();
        });
        
        // Allow paste event
        debitInput.addEventListener('paste', function(e) {
            setTimeout(() => {
                if (this.value > 0) {
                    row.querySelector('.kredit-input').value = '';
                    row.querySelector('.kredit-input').classList.remove('is-invalid');
                }
                validateRow(row);
                updateTotals();
            }, 10);
        });
        
        kreditInput.addEventListener('paste', function(e) {
            setTimeout(() => {
                if (this.value > 0) {
                    row.querySelector('.debit-input').value = '';
                    row.querySelector('.debit-input').classList.remove('is-invalid');
                }
                validateRow(row);
                updateTotals();
            }, 10);
        });
        
        removeBtn.addEventListener('click', function() {
            const rows = document.querySelectorAll('.jurnal-row');
            if (rows.length > 2) {
                row.remove();
                updateTotals();
                validateForm();
            } else {
                showAlert('Jurnal minimal harus memiliki 2 baris', 'warning');
            }
        });
    }
    
    function validateRow(row) {
        let isValid = true;
        const coaSelect = row.querySelector('.select-coa');
        const coaId = row.querySelector('.coa-id');
        const debitInput = row.querySelector('.debit-input');
        const kreditInput = row.querySelector('.kredit-input');
        const coaError = row.querySelector('.coa-error');
        const debitError = row.querySelector('.debit-error');
        const kreditError = row.querySelector('.kredit-error');
        
        // Reset errors
        coaSelect.classList.remove('is-invalid');
        debitInput.classList.remove('is-invalid');
        kreditInput.classList.remove('is-invalid');
        coaError.style.display = 'none';
        debitError.style.display = 'none';
        kreditError.style.display = 'none';
        
        // Validate COA
        if (!coaSelect.value || coaId.value === '') {
            coaSelect.classList.add('is-invalid');
            coaError.style.display = 'block';
            isValid = false;
        }
        
        // Validate debit/kredit
        const debit = parseFloat(debitInput.value) || 0;
        const kredit = parseFloat(kreditInput.value) || 0;
        
        if (debit > 0 && kredit > 0) {
            debitInput.classList.add('is-invalid');
            kreditInput.classList.add('is-invalid');
            debitError.textContent = 'Tidak boleh memiliki debit dan kredit sekaligus';
            kreditError.textContent = 'Tidak boleh memiliki debit dan kredit sekaligus';
            debitError.style.display = 'block';
            kreditError.style.display = 'block';
            isValid = false;
        } else if (debit === 0 && kredit === 0) {
            debitInput.classList.add('is-invalid');
            kreditInput.classList.add('is-invalid');
            debitError.textContent = 'Harus memiliki nilai debit atau kredit';
            kreditError.textContent = 'Harus memiliki nilai debit atau kredit';
            debitError.style.display = 'block';
            kreditError.style.display = 'block';
            isValid = false;
        } else if (debit < 0 || kredit < 0) {
            if (debit < 0) {
                debitInput.classList.add('is-invalid');
                debitError.textContent = 'Nilai tidak boleh negatif';
                debitError.style.display = 'block';
            }
            if (kredit < 0) {
                kreditInput.classList.add('is-invalid');
                kreditError.textContent = 'Nilai tidak boleh negatif';
                kreditError.style.display = 'block';
            }
            isValid = false;
        }
        
        return isValid;
    }
    
    function updateCoaInfo(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const row = selectElement.closest('tr');
        
        if (selectedOption && selectedOption.value) {
            const coaId = row.querySelector('.coa-id');
            const coaKode = row.querySelector('.coa-kode');
            const coaNama = row.querySelector('.coa-nama');
            const coaInfo = row.querySelector('.coa-info');
            
            coaId.value = selectedOption.value;
            coaKode.value = selectedOption.dataset.kode;
            coaNama.value = selectedOption.dataset.nama;
            
            coaInfo.textContent = `${selectedOption.dataset.tipe} - Saldo Normal: ${selectedOption.dataset.saldo}`;
            coaInfo.className = `coa-info ${selectedOption.dataset.saldo === 'Debit' ? 'text-success' : 'text-warning'}`;
        }
    }
    
    function clearAllRows() {
        if (confirm('Apakah Anda yakin ingin menghapus semua baris?')) {
            const rows = document.querySelectorAll('.jurnal-row');
            
            // Keep only 2 rows
            rows.forEach((row, index) => {
                if (index >= 2) {
                    row.remove();
                } else {
                    // Clear row data but keep structure
                    row.querySelector('.select-coa').value = '';
                    row.querySelector('.debit-input').value = '';
                    row.querySelector('.kredit-input').value = '';
                    row.querySelector('.row-keterangan').value = '';
                    row.querySelector('.coa-info').textContent = '';
                    
                    // Clear hidden fields
                    row.querySelector('.coa-id').value = '';
                    row.querySelector('.coa-kode').value = '';
                    row.querySelector('.coa-nama').value = '';
                    
                    // Clear validation
                    row.querySelectorAll('.is-invalid').forEach(el => {
                        el.classList.remove('is-invalid');
                    });
                    row.querySelectorAll('.invalid-feedback').forEach(el => {
                        el.style.display = 'none';
                    });
                }
            });
            
            updateTotals();
            validateForm();
            
            showAlert('Semua baris telah direset', 'info');
        }
    }
    
    function autoFillDummyData() {
        if (coaOptions.length < 2) {
            showAlert('Minimal diperlukan 2 akun COA untuk membuat data dummy.', 'warning');
            return;
        }
        
        // Clear any existing errors
        validationErrors.classList.add('d-none');
        
        // Set form header data
        document.getElementById('tanggal').value = '<?= date('Y-m-d') ?>';
        document.getElementById('keterangan').value = 'Pembelian perlengkapan kantor untuk operasional';
        document.getElementById('tipe_referensi').value = 'pembelian';
        document.getElementById('referensi').value = 'PO-' + Date.now().toString().slice(-6);
        
        // Get rows
        const rows = document.querySelectorAll('.jurnal-row');
        
        // Ensure we have at least 2 rows
        while (rows.length < 2) {
            addJurnalRow();
        }
        
        const updatedRows = document.querySelectorAll('.jurnal-row');
        
        if (updatedRows.length >= 2) {
            // First row - Debit (Use first COA)
            const row1 = updatedRows[0];
            const coaSelect1 = row1.querySelector('.select-coa');
            const debitInput1 = row1.querySelector('.debit-input');
            const keterangan1 = row1.querySelector('.row-keterangan');
            
            // Select first COA
            if (coaOptions[0]) {
                coaSelect1.value = coaOptions[0].id;
                updateCoaInfo(coaSelect1);
            }
            debitInput1.value = 5000000;
            keterangan1.value = 'Pembelian perlengkapan kantor';
            
            // Second row - Credit (Use second COA)
            const row2 = updatedRows[1];
            const coaSelect2 = row2.querySelector('.select-coa');
            const kreditInput2 = row2.querySelector('.kredit-input');
            const keterangan2 = row2.querySelector('.row-keterangan');
            
            // Select second COA
            if (coaOptions[1]) {
                coaSelect2.value = coaOptions[1].id;
                updateCoaInfo(coaSelect2);
            }
            kreditInput2.value = 5000000;
            keterangan2.value = 'Hutang kepada supplier';
            
            // Validate rows
            validateRow(row1);
            validateRow(row2);
            
            // Update totals
            updateTotals();
            
            // Validate form
            validateForm();
            
            // Show success message
            showAlert('Data dummy berhasil dimuat! Jurnal sudah balance dan siap disimpan.', 'success');
        }
    }
    
    function updateTotals() {
        let totalDebit = 0;
        let totalKredit = 0;
        
        document.querySelectorAll('.jurnal-row').forEach(row => {
            const debit = parseFloat(row.querySelector('.debit-input').value) || 0;
            const kredit = parseFloat(row.querySelector('.kredit-input').value) || 0;
            
            totalDebit += debit;
            totalKredit += kredit;
        });
        
        // Update displays
        totalDebitDisplay.textContent = formatCurrency(totalDebit);
        totalKreditDisplay.textContent = formatCurrency(totalKredit);
        footerTotalDebit.textContent = formatCurrency(totalDebit);
        footerTotalKredit.textContent = formatCurrency(totalKredit);
        
        const difference = totalDebit - totalKredit;
        balanceDifference.textContent = `Selisih: ${formatCurrency(Math.abs(difference))}`;
        
        // Update balance status
        const isBalanced = Math.abs(difference) <= 0.01; // Tolerance 0.01
        
        if (isBalanced) {
            balanceStatus.innerHTML = '<i class="fas fa-check-circle me-1"></i> Balanced';
            balanceStatus.className = 'badge bg-success';
            footerBalanceStatus.innerHTML = '<i class="fas fa-check-circle me-1"></i> Balanced';
            footerBalanceStatus.className = 'badge bg-success';
            
            // Enable submit if form is valid
            if (validateForm(true)) { // true = silent validation
                submitBtn.disabled = false;
            }
        } else {
            balanceStatus.innerHTML = '<i class="fas fa-times-circle me-1"></i> Unbalanced';
            balanceStatus.className = 'badge bg-danger';
            footerBalanceStatus.innerHTML = '<i class="fas fa-times-circle me-1"></i> Unbalanced';
            footerBalanceStatus.className = 'badge bg-danger';
            submitBtn.disabled = true;
        }
    }
    
    function validateForm(silent = false) {
        const errors = [];
        const rows = document.querySelectorAll('.jurnal-row');
        let hasDebit = false;
        let hasKredit = false;
        let validRows = 0;
        
        // Check minimum rows
        if (rows.length < 2) {
            errors.push('Jurnal minimal harus memiliki 2 baris');
        }
        
        // Validate each row
        rows.forEach((row, index) => {
            const rowNum = index + 1;
            const coaId = row.querySelector('.coa-id').value;
            const coaKode = row.querySelector('.coa-kode').value;
            const debitInput = row.querySelector('.debit-input');
            const kreditInput = row.querySelector('.kredit-input');
            const debit = parseFloat(debitInput.value) || 0;
            const kredit = parseFloat(kreditInput.value) || 0;
            
            // Check COA selected - use hidden input value
            if (!coaId || coaId === '') {
                errors.push(`Baris ${rowNum}: Akun harus dipilih`);
            } else if (!coaKode || coaKode === '') {
                errors.push(`Baris ${rowNum}: Data akun tidak lengkap`);
            } else {
                validRows++;
            }
            
            // Check debit/kredit values
            if (debit > 0 && kredit > 0) {
                errors.push(`Baris ${rowNum}: Tidak boleh memiliki debit dan kredit sekaligus`);
            }
            
            if (debit === 0 && kredit === 0) {
                errors.push(`Baris ${rowNum}: Harus memiliki nilai debit atau kredit`);
            }
            
            if (debit < 0 || kredit < 0) {
                errors.push(`Baris ${rowNum}: Nilai tidak boleh negatif`);
            }
            
            // Track debit/kredit presence
            if (debit > 0) hasDebit = true;
            if (kredit > 0) hasKredit = true;
        });
        
        // Check valid rows count
        if (validRows < 2) {
            errors.push('Jurnal harus memiliki minimal 2 baris dengan akun yang valid');
        }
        
        // Check has both debit and credit
        if (!hasDebit) {
            errors.push('Jurnal harus memiliki minimal 1 baris debit');
        }
        
        if (!hasKredit) {
            errors.push('Jurnal harus memiliki minimal 1 baris kredit');
        }
        
        // Check balance
        const totalDebit = parseFloat(totalDebitDisplay.textContent.replace(/[^0-9.-]+/g, '')) || 0;
        const totalKredit = parseFloat(totalKreditDisplay.textContent.replace(/[^0-9.-]+/g, '')) || 0;
        
        if (Math.abs(totalDebit - totalKredit) > 0.01) {
            errors.push(`Jurnal tidak balance! Debit: ${formatCurrency(totalDebit)}, Kredit: ${formatCurrency(totalKredit)}`);
        }
        
        // Check keterangan
        const keterangan = document.getElementById('keterangan').value.trim();
        if (!keterangan) {
            errors.push('Keterangan jurnal harus diisi');
        }
        
        // If silent mode, just return validation result
        if (silent) {
            return errors.length === 0;
        }
        
        // Display errors if any
        if (errors.length > 0) {
            errorList.innerHTML = '';
            errors.forEach(error => {
                const li = document.createElement('li');
                li.textContent = error;
                errorList.appendChild(li);
            });
            validationErrors.classList.remove('d-none');
            validationErrors.scrollIntoView({ behavior: 'smooth' });
            return false;
        }
        
        validationErrors.classList.add('d-none');
        return true;
    }
    
    function serializeForm() {
        const details = [];
        
        document.querySelectorAll('.jurnal-row').forEach(row => {
            const coaId = row.querySelector('.coa-id').value;
            const coaKode = row.querySelector('.coa-kode').value;
            const coaNama = row.querySelector('.coa-nama').value;
            const debitInput = row.querySelector('.debit-input');
            const kreditInput = row.querySelector('.kredit-input');
            const debit = parseFloat(debitInput.value) || 0;
            const kredit = parseFloat(kreditInput.value) || 0;
            const keterangan = row.querySelector('.row-keterangan').value || '';
            
            // Only add if coaId exists
            if (coaId && coaId !== '' && coaKode && coaNama) {
                details.push({
                    coa_id: coaId,
                    kode_akun: coaKode,
                    nama_akun: coaNama,
                    debit: debit,
                    kredit: kredit,
                    keterangan: keterangan
                });
            }
        });
        
        return {
            tanggal: document.getElementById('tanggal').value,
            keterangan: document.getElementById('keterangan').value,
            referensi: document.getElementById('referensi').value,
            tipe_referensi: document.getElementById('tipe_referensi').value,
            details: details
        };
    }
    
    function formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(amount);
    }
    
    function showAlert(message, type = 'info') {
        // Remove existing custom alerts
        const existingAlerts = document.querySelectorAll('.custom-alert');
        existingAlerts.forEach(alert => alert.remove());
        
        // Define icons for each alert type
        const icons = {
            'success': 'check-circle',
            'error': 'exclamation-circle',
            'warning': 'exclamation-triangle',
            'info': 'info-circle'
        };
        
        // Create alert element
        const alertEl = document.createElement('div');
        alertEl.className = `alert alert-${type} alert-dismissible fade show custom-alert`;
        alertEl.innerHTML = `
            <i class="fas fa-${icons[type] || 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Insert after header
        const container = document.querySelector('.container-fluid');
        const firstRow = container.querySelector('.row:first-child');
        container.insertBefore(alertEl, firstRow.nextSibling);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            if (alertEl.parentNode) {
                const bsAlert = new bootstrap.Alert(alertEl);
                bsAlert.close();
            }
        }, 5000);
    }
    
    // Force initial COA info update for existing rows if they have values
    document.querySelectorAll('.jurnal-row .select-coa').forEach(select => {
        if (select.value) {
            updateCoaInfo(select);
        }
    });
    
    // Real-time validation
    setInterval(() => {
        const rows = document.querySelectorAll('.jurnal-row');
        if (rows.length >= 2) {
            // Update totals every second
            updateTotals();
        }
    }, 1000);
    
    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl + Enter to submit
        if (e.ctrlKey && e.key === 'Enter') {
            e.preventDefault();
            if (!submitBtn.disabled) {
                jurnalForm.submit();
            }
        }
        
        // Ctrl + N to add new row
        if (e.ctrlKey && e.key === 'n') {
            e.preventDefault();
            addJurnalRow();
        }
    });
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<style>
.select-coa {
    width: 100%;
}

.jurnal-row {
    transition: background-color 0.2s;
    vertical-align: middle;
}

.jurnal-row:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

.coa-info {
    font-size: 0.85em;
    display: block;
    margin-top: 2px;
}

#validationErrors {
    animation: fadeIn 0.3s;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Improve input styling */
.debit-input:focus {
    border-color: #198754;
    box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
}

.kredit-input:focus {
    border-color: #ffc107;
    box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
}

/* Success button */
#autoFillBtn {
    background-color: #20c997;
    border-color: #20c997;
}

#autoFillBtn:hover {
    background-color: #198754;
    border-color: #198754;
}

/* Warning button */
#clearAllBtn {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
}

#clearAllBtn:hover {
    background-color: #e0a800;
    border-color: #e0a800;
    color: #000;
}

.custom-alert {
    margin-top: 1rem;
    margin-bottom: 1rem;
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        transform: translateY(-20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Table styling */
#jurnalDetailsTable {
    font-size: 0.9rem;
}

#jurnalDetailsTable th {
    font-weight: 600;
    background-color: #f8f9fa;
}

#jurnalDetailsTable tfoot th {
    background-color: #e9ecef;
}

/* Input group styling */
.input-group-text {
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    #jurnalDetailsTable {
        font-size: 0.8rem;
    }
    
    .input-group-text {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
    }
    
    .form-control {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
    }
    
    .btn-sm {
        padding: 0.15rem 0.3rem;
        font-size: 0.7rem;
    }
}

/* Highlight balanced state */
.badge.bg-success {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(25, 135, 84, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(25, 135, 84, 0);
    }
}

/* Row highlight when invalid */
.is-invalid {
    border-color: #dc3545 !important;
}

.invalid-feedback {
    font-size: 0.75rem;
    margin-top: 0.25rem;
}
</style>

<?= $this->include('accounting/templates/footer') ?>