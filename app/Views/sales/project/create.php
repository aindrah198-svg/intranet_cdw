<?php
$title = $title ?? 'Buat Project Baru';
$active = $active ?? 'project';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header">
                <h1 class="display-5 fw-bold text-primary mb-3">
                    <i class="fas fa-project-diagram me-3"></i>
                    <?= $title ?>
                </h1>
                <p class="lead text-muted">
                    Buat project baru untuk melanjutkan pembuatan invoice
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

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Form Project Baru
                    </h5>
                </div>
                
                <form action="<?= base_url('sales/project/store') ?>" method="POST" id="projectForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="redirect_url" value="<?= $redirect_url ?>">
                    
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="kode_project" class="form-label">Kode Project <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="kode_project" 
                                       name="kode_project" value="<?= old('kode_project', $project_code ?? '') ?>" required>
                                <div class="form-text">Kode project unik, contoh: PROJ-<?= date('Ym') ?>-001</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                                <select class="form-select" id="client_id" name="client_id" required>
                                    <option value="">Pilih Client</option>
                                    <?php foreach ($clients as $client): ?>
                                        <option value="<?= $client['id'] ?>" 
                                            <?= old('client_id') == $client['id'] ? 'selected' : '' ?>>
                                            <?= $client['nama_perusahaan'] ?>
                                            <?= $client['nama_kontak'] ? ' - ' . $client['nama_kontak'] : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="nama_project" class="form-label">Nama Project <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_project" 
                                       name="nama_project" value="<?= old('nama_project') ?>" required>
                                <div class="form-text">Nama project yang jelas dan deskriptif</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="nilai_project" class="form-label">Nilai Project (Rp)</label>
                                <input type="text" class="form-control" id="nilai_project" 
                                       name="nilai_project" value="<?= old('nilai_project', '0') ?>">
                                <div class="form-text">Nilai kontrak/project</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="tanggal_mulai" 
                                       name="tanggal_mulai" value="<?= old('tanggal_mulai', date('Y-m-d')) ?>">
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi Project</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" 
                                          rows="4"><?= old('deskripsi') ?></textarea>
                                <div class="form-text">Deskripsi singkat tentang project</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-light py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <a href="<?= $redirect_url ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Invoice
                                </a>
                            </div>
                            <div class="text-muted small">
                                Project akan otomatis dipilih di form invoice
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary" id="submitProjectBtn">
                                    <i class="fas fa-save me-1"></i> Simpan Project
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="alert alert-info mt-4">
                <div class="d-flex">
                    <div class="me-3">
                        <i class="fas fa-info-circle fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="alert-heading mb-2">Informasi Penting</h6>
                        <p class="mb-1">• Setelah project berhasil dibuat, Anda akan otomatis kembali ke halaman pembuatan invoice.</p>
                        <p class="mb-1">• Project yang baru dibuat akan otomatis terpilih di dropdown project.</p>
                        <p class="mb-0">• Status project otomatis di-set ke "deal" sehingga siap untuk dibuatkan invoice.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Format currency input
    const nilaiInput = document.getElementById('nilai_project');
    if (nilaiInput) {
        // Format on blur
        nilaiInput.addEventListener('blur', function() {
            let value = this.value.replace(/[^\d]/g, '');
            if (value) {
                value = parseInt(value).toLocaleString('id-ID');
            } else {
                value = '0';
            }
            this.value = value;
        });
        
        // Parse on focus
        nilaiInput.addEventListener('focus', function() {
            this.value = this.value.replace(/[^\d]/g, '');
        });
    }
    
    // Auto-focus on project name
    const namaProjectInput = document.getElementById('nama_project');
    if (namaProjectInput) {
        namaProjectInput.focus();
    }
    
    // Generate suggested project code
    const kodeProjectInput = document.getElementById('kode_project');
    if (kodeProjectInput && !kodeProjectInput.value) {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        kodeProjectInput.value = `PROJ-${year}${month}-001`;
    }
    
    // Form submission
    const projectForm = document.getElementById('projectForm');
    if (projectForm) {
        projectForm.addEventListener('submit', function(e) {
            // Format nilai project before submit
            if (nilaiInput) {
                nilaiInput.value = nilaiInput.value.replace(/[^\d]/g, '');
            }
            
            // Validate required fields
            const requiredFields = ['kode_project', 'nama_project', 'client_id'];
            let isValid = true;
            
            requiredFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field && !field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else if (field) {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Harap isi semua field yang wajib diisi!');
                return;
            }
            
            // Show loading on submit button
            const submitBtn = document.getElementById('submitProjectBtn');
            if (submitBtn) {
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';
                submitBtn.disabled = true;
                
                // Allow form to submit
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 3000);
            }
        });
    }
});
</script>