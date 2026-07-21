<?php
$title = $title ?? 'Tambah Akun Baru';
$active = $active ?? 'bookkeeping';
$subtitle = $subtitle ?? 'Tambah Akun ke Chart of Accounts';
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
                    <h2 class="page-title mb-1">Tambah Akun Baru</h2>
                    <p class="page-subtitle text-muted mb-0"><?= $subtitle ?></p>
                </div>
                <div class="btn-group">
                    <a href="<?= site_url('accounting/pembukuan/daftar-akun') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts Section -->
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
                <ul class="mb-0 mt-1">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <?php endif ?>

    <!-- Form Section -->
    <div class="row">
        <div class="col-lg-8">
            <div class="modern-card">
                <form action="<?= site_url('accounting/pembukuan/daftar-akun/store') ?>" method="post" id="coaForm">
                    <?= csrf_field() ?>
                    
                    <div class="row g-3">
                        <!-- Parent Selection -->
                        <div class="col-md-6">
                            <label for="parent_id" class="form-label">Parent Akun</label>
                            <select name="parent_id" id="parent_id" class="form-select">
                                <option value="">-- Tanpa Parent (Root Level) --</option>
                                <?php foreach ($parentOptions as $id => $option): ?>
                                    <option value="<?= $id ?>" <?= old('parent_id') == $id ? 'selected' : '' ?>>
                                        <?= $option ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Pilih parent untuk membuat sub-akun</small>
                        </div>

                        <div class="col-md-6">
                            <label for="parent_info" class="form-label">Informasi Parent</label>
                            <div id="parent_info" class="form-control bg-light" style="min-height: 38px;">
                                <span class="text-muted">-- Pilih parent untuk melihat detail --</span>
                            </div>
                        </div>

                        <!-- Kode Akun -->
                        <div class="col-md-6">
                            <label for="kode_akun" class="form-label">Kode Akun</label>
                            <div class="input-group">
                                <input type="text" name="kode_akun" id="kode_akun" class="form-control" 
                                       value="<?= old('kode_akun') ?>" 
                                       placeholder="Biarkan kosong untuk auto-generate">
                                <button type="button" class="btn btn-outline-secondary" id="suggestCodeBtn">
                                    <i class="fas fa-lightbulb"></i> Suggest
                                </button>
                            </div>
                            <div id="kodeValidation" class="mt-1 small"></div>
                            <small class="text-muted">Biarkan kosong untuk auto-generate</small>
                        </div>

                        <div class="col-md-6">
                            <label for="nama_akun" class="form-label">Nama Akun <span class="text-danger">*</span></label>
                            <input type="text" name="nama_akun" id="nama_akun" class="form-control" 
                                   value="<?= old('nama_akun') ?>" required>
                        </div>

                        <!-- Tipe Akun -->
                        <div class="col-md-6">
                            <label for="tipe_akun" class="form-label">Tipe Akun <span class="text-danger">*</span></label>
                            <select name="tipe_akun" id="tipe_akun" class="form-select" required>
                                <option value="">-- Pilih Tipe Akun --</option>
                                <?php foreach ($tipeAkunOptions as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= old('tipe_akun') == $value ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="kategori" class="form-label">Kategori</label>
                            <select name="kategori" id="kategori" class="form-select">
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach ($kategoriOptions as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= old('kategori') == $value ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Saldo Normal -->
                        <div class="col-md-6">
                            <label for="saldo_normal" class="form-label">Saldo Normal <span class="text-danger">*</span></label>
                            <select name="saldo_normal" id="saldo_normal" class="form-select" required>
                                <option value="">-- Pilih Saldo Normal --</option>
                                <?php foreach ($saldoNormalOptions as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= old('saldo_normal') == $value ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Jenis Akun -->
                        <div class="col-md-6">
                            <label class="form-label">Jenis Akun <span class="text-danger">*</span></label>
                            <div class="mt-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="is_header" id="is_header_1" value="1" 
                                           <?= old('is_header', '1') == '1' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="is_header_1">
                                        <i class="fas fa-folder text-primary me-1"></i> Header (Grup)
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="is_header" id="is_header_0" value="0"
                                           <?= old('is_header') == '0' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="is_header_0">
                                        <i class="fas fa-file text-secondary me-1"></i> Detail (Transaksi)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Level -->
                        <div class="col-md-6">
                            <label for="level" class="form-label">Level <span class="text-danger">*</span></label>
                            <select name="level" id="level" class="form-select" required>
                                <?php foreach ($levelOptions as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= old('level', '1') == $value ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div id="levelHelp" class="small text-muted mt-1"></div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <div class="mt-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="is_active" value="1" checked>
                                    <label class="form-check-label" for="is_active">Aktif</label>
                                </div>
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="col-12">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3"><?= old('deskripsi') ?></textarea>
                        </div>

                        <!-- Form Actions -->
                        <div class="col-12 mt-4">
                            <div class="d-flex justify-content-between">
                                <a href="<?= site_url('accounting/pembukuan/daftar-akun') ?>" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Simpan Akun
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Help Section - SEDERHANA -->
        <div class="col-lg-4">
            <div class="modern-card">
                <h5 class="card-title mb-3"><i class="fas fa-info-circle me-2"></i> Informasi Singkat</h5>
                
                <!-- Struktur Saat Ini -->
                <div class="mb-3">
                    <h6 class="text-primary"><i class="fas fa-chart-simple me-1"></i> Struktur Saat Ini</h6>
                    <div class="small" id="structureInfo">
                        <?php
                        try {
                            $stats = model('CoaModel')->getStats();
                            echo '<div class="mb-2">';
                            echo '<strong>Total Akun:</strong> ' . $stats['total'] . '<br>';
                            echo '<strong>Aktif:</strong> ' . $stats['active'] . ' | <strong>Header:</strong> ' . $stats['header'] . '<br>';
                            echo '</div>';
                            
                            echo '<div class="mb-2"><strong>Per Tipe:</strong><br>';
                            foreach ($stats['by_type'] as $type => $typeStats) {
                                if ($typeStats['total'] > 0) {
                                    echo '<span class="badge bg-secondary me-1 mb-1">' . $type . ': ' . $typeStats['total'] . '</span>';
                                }
                            }
                            echo '</div>';
                        } catch (\Exception $e) {
                            echo '<span class="text-muted">Belum ada data COA.</span>';
                        }
                        ?>
                    </div>
                </div>

                <hr>

                <!-- Tips Singkat -->
                <div class="mt-2">
                    <h6 class="text-success"><i class="fas fa-lightbulb me-1"></i> Tips</h6>
                    <ul class="small mb-0">
                        <li>Biarkan kode kosong → auto-generate</li>
                        <li>Pilih parent dulu → level otomatis</li>
                        <li>Tipe akun harus sama dengan parent</li>
                        <li>Akun tanpa parent = Level 1</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Base URL
    const baseUrl = '<?= base_url() ?>';
    
    // DOM Elements
    const parentSelect = document.getElementById('parent_id');
    const parentInfoDiv = document.getElementById('parent_info');
    const tipeSelect = document.getElementById('tipe_akun');
    const kodeInput = document.getElementById('kode_akun');
    const levelSelect = document.getElementById('level');
    const saldoSelect = document.getElementById('saldo_normal');
    const suggestBtn = document.getElementById('suggestCodeBtn');
    const isHeader1 = document.getElementById('is_header_1');
    const isHeader0 = document.getElementById('is_header_0');
    const form = document.getElementById('coaForm');
    const kodeValidation = document.getElementById('kodeValidation');
    const levelHelp = document.getElementById('levelHelp');

    // Store parent data from PHP
    const parentData = <?= json_encode($parentData ?? []) ?>;

    // Update level options based on parent
    function updateLevelOptions(parentLevel) {
        for (let i = 0; i < levelSelect.options.length; i++) {
            levelSelect.options[i].disabled = false;
            levelSelect.options[i].style.color = '';
        }

        if (parentLevel) {
            for (let i = 0; i < levelSelect.options.length; i++) {
                const levelValue = parseInt(levelSelect.options[i].value);
                if (levelValue <= parentLevel) {
                    levelSelect.options[i].disabled = true;
                    levelSelect.options[i].style.color = '#ccc';
                }
            }
            
            const minLevel = parentLevel + 1;
            const validOption = Array.from(levelSelect.options).find(opt => 
                parseInt(opt.value) >= minLevel && !opt.disabled
            );
            
            if (validOption) {
                levelSelect.value = validOption.value;
            }
            
            levelHelp.innerHTML = `<i class="fas fa-info-circle me-1"></i>Minimal level: ${minLevel} (Parent level: ${parentLevel})`;
        } else {
            for (let i = 0; i < levelSelect.options.length; i++) {
                if (levelSelect.options[i].value !== '1') {
                    levelSelect.options[i].disabled = true;
                    levelSelect.options[i].style.color = '#ccc';
                }
            }
            levelSelect.value = '1';
            levelHelp.innerHTML = '<i class="fas fa-info-circle me-1"></i>Akun tanpa parent harus Level 1';
        }
    }

    // Load parent info via AJAX
    async function loadParentInfo(parentId) {
        if (!parentId) {
            parentInfoDiv.innerHTML = '<span class="text-muted">-- Pilih parent untuk melihat detail --</span>';
            updateLevelOptions(null);
            isHeader1.disabled = false;
            return;
        }

        if (parentData[parentId]) {
            const parent = parentData[parentId];
            parentInfoDiv.innerHTML = `
                <div class="small">
                    <strong>${escapeHtml(parent.kode)} - ${escapeHtml(parent.nama)}</strong><br>
                    <span class="text-muted">${escapeHtml(parent.tipe)} | ${parent.is_header ? 'Header' : 'Detail'} | Level ${parent.level}</span>
                </div>
            `;
            
            tipeSelect.value = parent.tipe;
            triggerTipeChange();
            updateLevelOptions(parent.level);
            
            if (parent.is_header == 0) {
                isHeader0.checked = true;
                isHeader1.disabled = true;
            } else {
                isHeader1.disabled = false;
            }
            
            if (!kodeInput.value) {
                await suggestKode();
            }
        } else {
            try {
                // 🔥 PERBAIKAN: Gunakan route yang benar
                const response = await fetch(`${baseUrl}/pembukuan/daftar-akun/ajax-get-parent-info?parent_id=${parentId}`);
                const data = await response.json();
                
                if (data.success) {
                    parentInfoDiv.innerHTML = `
                        <div class="small">
                            <strong>${escapeHtml(data.parent.kode)} - ${escapeHtml(data.parent.nama)}</strong><br>
                            <span class="text-muted">${escapeHtml(data.parent.tipe)} | ${data.parent.is_header ? 'Header' : 'Detail'} | Level ${data.parent.level}</span>
                        </div>
                    `;
                    
                    tipeSelect.value = data.parent.tipe;
                    triggerTipeChange();
                    updateLevelOptions(data.parent.level);
                    
                    if (data.parent.is_header == 0) {
                        isHeader0.checked = true;
                        isHeader1.disabled = true;
                    } else {
                        isHeader1.disabled = false;
                    }
                    
                    if (!kodeInput.value && data.next_code) {
                        kodeInput.value = data.next_code;
                        await validateKode();
                    }
                }
            } catch (error) {
                console.error('Error loading parent info:', error);
                parentInfoDiv.innerHTML = '<span class="text-danger">Gagal memuat informasi parent</span>';
            }
        }
    }

    // Validate kode format via AJAX
    async function validateKode() {
        const kode = kodeInput.value.trim();
        const tipe = tipeSelect.value;
        
        if (!kode) {
            kodeValidation.innerHTML = '<span class="text-info"><i class="fas fa-info-circle me-1"></i>Kosongkan untuk auto-generate</span>';
            return true;
        }
        
        if (!tipe) {
            kodeValidation.innerHTML = '<span class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i>Pilih tipe akun terlebih dahulu</span>';
            return false;
        }
        
        const regex = /^[1-5](-\d+)*$/;
        if (!regex.test(kode)) {
            kodeValidation.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-circle me-1"></i>Format tidak valid. Contoh: 1-1000</span>';
            return false;
        }
        
        const prefixMap = { 'Aset': '1', 'Kewajiban': '2', 'Ekuitas': '3', 'Pendapatan': '4', 'Beban': '5' };
        const expectedPrefix = prefixMap[tipe];
        
        if (!kode.startsWith(expectedPrefix + '-')) {
            kodeValidation.innerHTML = `<span class="text-danger"><i class="fas fa-exclamation-circle me-1"></i>Harus diawali dengan "${expectedPrefix}-"</span>`;
            return false;
        }
        
        kodeValidation.innerHTML = '<span class="text-muted"><i class="fas fa-spinner fa-spin me-1"></i>Memeriksa kode...</span>';
        
        try {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 5000);
            
            // 🔥 PERBAIKAN: Gunakan route yang benar
            const response = await fetch(`${baseUrl}/pembukuan/daftar-akun/ajax-validate-kode?kode=${encodeURIComponent(kode)}&tipe=${encodeURIComponent(tipe)}`, {
                signal: controller.signal,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            clearTimeout(timeoutId);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.valid) {
                kodeValidation.innerHTML = `<span class="text-success"><i class="fas fa-check-circle me-1"></i>${data.message || 'Kode valid dan tersedia'}</span>`;
                return true;
            } else {
                kodeValidation.innerHTML = `<span class="text-danger"><i class="fas fa-exclamation-circle me-1"></i>${data.message || 'Kode tidak valid'}</span>`;
                return false;
            }
            
        } catch (error) {
            console.error('Validation error:', error);
            
            if (error.name === 'AbortError') {
                kodeValidation.innerHTML = '<span class="text-warning"><i class="fas fa-clock me-1"></i>Timeout, coba lagi</span>';
            } else {
                kodeValidation.innerHTML = '<span class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i>Gagal memvalidasi, periksa koneksi</span>';
            }
            
            return true;
        }
    }

    // Suggest kode based on parent or type
    async function suggestKode() {
        const parentId = parentSelect.value;
        const tipe = tipeSelect.value;
        
        if (!tipe) {
            alert('Pilih tipe akun terlebih dahulu');
            tipeSelect.focus();
            return;
        }
        
        if (parentId) {
            try {
                // 🔥 PERBAIKAN: Gunakan route yang benar
                const response = await fetch(`${baseUrl}/pembukuan/daftar-akun/ajax-get-parent-info?parent_id=${parentId}`);
                const data = await response.json();
                
                if (data.success && data.next_code) {
                    kodeInput.value = data.next_code;
                } else if (parentData[parentId]) {
                    kodeInput.value = parentData[parentId].kode + '-001';
                }
            } catch (error) {
                if (parentData[parentId]) {
                    kodeInput.value = parentData[parentId].kode + '-001';
                }
            }
        } else {
            const prefixMap = { 'Aset': '1', 'Kewajiban': '2', 'Ekuitas': '3', 'Pendapatan': '4', 'Beban': '5' };
            const prefix = prefixMap[tipe];
            kodeInput.value = `${prefix}-1000`;
        }
        
        await validateKode();
    }

    // Trigger tipe change effects
    function triggerTipeChange() {
        const tipe = tipeSelect.value;
        const saldoMap = { 'Aset': 'Debit', 'Kewajiban': 'Kredit', 'Ekuitas': 'Kredit', 'Pendapatan': 'Kredit', 'Beban': 'Debit' };
        
        if (tipe && saldoMap[tipe] && !saldoSelect.value) {
            saldoSelect.value = saldoMap[tipe];
        }
        
        validateKode();
    }

    // Escape HTML to prevent XSS
    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    // Event Listeners
    parentSelect.addEventListener('change', function() {
        loadParentInfo(this.value);
    });
    
    tipeSelect.addEventListener('change', function() {
        triggerTipeChange();
    });
    
    kodeInput.addEventListener('blur', validateKode);
    kodeInput.addEventListener('input', function() {
        if (this.value) {
            kodeValidation.innerHTML = '<span class="text-muted"><i class="fas fa-spinner fa-spin me-1"></i>Memeriksa...</span>';
        } else {
            kodeValidation.innerHTML = '<span class="text-info"><i class="fas fa-info-circle me-1"></i>Kosongkan untuk auto-generate</span>';
        }
    });
    
    suggestBtn.addEventListener('click', suggestKode);
    
    // Form submission validation
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (kodeInput.value.trim()) {
            const isValid = await validateKode();
            if (!isValid) {
                alert('Kode akun tidak valid. Silakan perbaiki.');
                kodeInput.focus();
                return false;
            }
        }
        
        const level = parseInt(levelSelect.value);
        const parentId = parentSelect.value;
        
        if (parentId && parentData[parentId]) {
            const parentLevel = parentData[parentId].level;
            if (level <= parentLevel) {
                alert(`Level harus lebih tinggi dari level parent (${parentLevel})`);
                levelSelect.focus();
                return false;
            }
        } else if (level !== 1) {
            alert('Akun tanpa parent harus Level 1');
            levelSelect.focus();
            return false;
        }
        
        if (parentId && parentData[parentId]) {
            const parentTipe = parentData[parentId].tipe;
            if (tipeSelect.value !== parentTipe) {
                alert(`Tipe akun harus sama dengan parent (${parentTipe})`);
                tipeSelect.focus();
                return false;
            }
        }
        
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';
        submitBtn.disabled = true;
        
        this.submit();
    });
    
    // Initial setup
    updateLevelOptions(null);
    
    const defaultParent = '<?= $defaultParentId ?? '' ?>';
    if (defaultParent) {
        parentSelect.value = defaultParent;
        loadParentInfo(defaultParent);
    }
});
</script>

<style>
.modern-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.modern-card .card-title {
    font-weight: 600;
    border-left: 4px solid #4e73df;
    padding-left: 1rem;
}

select option:disabled {
    color: #ccc;
    background-color: #f8f9fa;
}

#parent_info {
    display: flex;
    align-items: center;
}

#kodeValidation {
    min-height: 24px;
}
</style>

<?= $this->include('accounting/templates/footer') ?>