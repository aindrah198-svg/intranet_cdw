<?php
$title = $title ?? 'Tambah Client Baru';
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
                                <i class="fas fa-user-plus me-2"></i>
                                <?= $title ?>
                            </h4>
                            <p class="mb-0 mt-1 small opacity-75"><?= $subtitle ?? 'Form Tambah Client Baru' ?></p>
                        </div>
                        <a href="<?= base_url('sales/client') ?>" class="btn btn-light btn-sm">
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

                    <!-- Form Tambah Client -->
                    <form action="<?= base_url('sales/client/store') ?>" method="POST" class="needs-validation" novalidate>
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <!-- Kode Client -->
                            <div class="col-md-6 mb-3">
                                <label for="kode_client" class="form-label">
                                    <i class="fas fa-hashtag me-1"></i> Kode Client <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control <?= (isset($validation) && $validation->hasError('kode_client')) ? 'is-invalid' : '' ?>" 
                                       id="kode_client" 
                                       name="kode_client" 
                                       value="<?= old('kode_client', $kode_client ?? '') ?>" 
                                       required 
                                       readonly>
                                <div class="invalid-feedback">
                                    <?= isset($validation) ? $validation->getError('kode_client') : 'Kode client wajib diisi' ?>
                                </div>
                                <small class="text-muted">Kode client di-generate otomatis</small>
                            </div>

                            <!-- Nama Perusahaan -->
                            <div class="col-md-6 mb-3">
                                <label for="nama_perusahaan" class="form-label">
                                    <i class="fas fa-building me-1"></i> Nama Perusahaan <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control <?= (isset($validation) && $validation->hasError('nama_perusahaan')) ? 'is-invalid' : '' ?>" 
                                       id="nama_perusahaan" 
                                       name="nama_perusahaan" 
                                       value="<?= old('nama_perusahaan') ?>" 
                                       required>
                                <div class="invalid-feedback">
                                    <?= isset($validation) ? $validation->getError('nama_perusahaan') : 'Nama perusahaan wajib diisi' ?>
                                </div>
                            </div>

                            <!-- Nama Kontak -->
                            <div class="col-md-6 mb-3">
                                <label for="nama_kontak" class="form-label">
                                    <i class="fas fa-user me-1"></i> Nama Kontak
                                </label>
                                <input type="text" 
                                       class="form-control <?= (isset($validation) && $validation->hasError('nama_kontak')) ? 'is-invalid' : '' ?>" 
                                       id="nama_kontak" 
                                       name="nama_kontak" 
                                       value="<?= old('nama_kontak') ?>">
                                <div class="invalid-feedback">
                                    <?= isset($validation) ? $validation->getError('nama_kontak') : '' ?>
                                </div>
                            </div>

                            <!-- Telepon -->
                            <div class="col-md-6 mb-3">
                                <label for="telepon" class="form-label">
                                    <i class="fas fa-phone me-1"></i> Telepon
                                </label>
                                <input type="text" 
                                       class="form-control <?= (isset($validation) && $validation->hasError('telepon')) ? 'is-invalid' : '' ?>" 
                                       id="telepon" 
                                       name="telepon" 
                                       value="<?= old('telepon') ?>">
                                <div class="invalid-feedback">
                                    <?= isset($validation) ? $validation->getError('telepon') : '' ?>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i> Email
                                </label>
                                <input type="email" 
                                       class="form-control <?= (isset($validation) && $validation->hasError('email')) ? 'is-invalid' : '' ?>" 
                                       id="email" 
                                       name="email" 
                                       value="<?= old('email') ?>">
                                <div class="invalid-feedback">
                                    <?= isset($validation) ? $validation->getError('email') : '' ?>
                                </div>
                            </div>

                            <!-- NPWP -->
                            <div class="col-md-6 mb-3">
                                <label for="npwp" class="form-label">
                                    <i class="fas fa-file-invoice me-1"></i> NPWP
                                </label>
                                <input type="text" 
                                       class="form-control <?= (isset($validation) && $validation->hasError('npwp')) ? 'is-invalid' : '' ?>" 
                                       id="npwp" 
                                       name="npwp" 
                                       value="<?= old('npwp') ?>">
                                <div class="invalid-feedback">
                                    <?= isset($validation) ? $validation->getError('npwp') : '' ?>
                                </div>
                            </div>

                            <!-- Kategori -->
                            <div class="col-md-6 mb-3">
                                <label for="kategori" class="form-label">
                                    <i class="fas fa-tag me-1"></i> Kategori
                                </label>
                                <select class="form-select <?= (isset($validation) && $validation->hasError('kategori')) ? 'is-invalid' : '' ?>" 
                                        id="kategori" 
                                        name="kategori">
                                    <option value="perusahaan" <?= old('kategori') == 'perusahaan' ? 'selected' : '' ?>>Perusahaan</option>
                                    <option value="pemerintah" <?= old('kategori') == 'pemerintah' ? 'selected' : '' ?>>Pemerintah</option>
                                    <option value="perorangan" <?= old('kategori') == 'perorangan' ? 'selected' : '' ?>>Perorangan</option>
                                </select>
                                <div class="invalid-feedback">
                                    <?= isset($validation) ? $validation->getError('kategori') : '' ?>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">
                                    <i class="fas fa-circle me-1"></i> Status
                                </label>
                                <select class="form-select <?= (isset($validation) && $validation->hasError('status')) ? 'is-invalid' : '' ?>" 
                                        id="status" 
                                        name="status">
                                    <option value="active" <?= old('status') == 'active' ? 'selected' : '' ?>>Active</option>
                                    <option value="inactive" <?= old('status') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                    <option value="potensial" <?= old('status') == 'potensial' ? 'selected' : '' ?>>Potensial</option>
                                </select>
                                <div class="invalid-feedback">
                                    <?= isset($validation) ? $validation->getError('status') : '' ?>
                                </div>
                            </div>

                            <!-- Alamat -->
                            <div class="col-12 mb-3">
                                <label for="alamat" class="form-label">
                                    <i class="fas fa-map-marker-alt me-1"></i> Alamat
                                </label>
                                <textarea class="form-control <?= (isset($validation) && $validation->hasError('alamat')) ? 'is-invalid' : '' ?>" 
                                          id="alamat" 
                                          name="alamat" 
                                          rows="3"><?= old('alamat') ?></textarea>
                                <div class="invalid-feedback">
                                    <?= isset($validation) ? $validation->getError('alamat') : '' ?>
                                </div>
                            </div>

                            <!-- Sales ID (Hidden) -->
                            <input type="hidden" name="sales_id" value="<?= $user['karyawan_id'] ?? 1 ?>">

                            <!-- Informasi Sales -->
                            <div class="col-12 mb-4">
                                <div class="alert alert-info">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-tie fa-2x me-3"></i>
                                        <div>
                                            <h6 class="mb-1">Informasi Sales</h6>
                                            <p class="mb-0">
                                                Client ini akan ditangani oleh: 
                                                <strong><?= htmlspecialchars($user['name'] ?? 'Sales Anda') ?></strong>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between pt-3 border-top">
                            <a href="<?= base_url('sales/client') ?>" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan Client
                            </button>
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
                        <li>Kode client akan di-generate otomatis oleh sistem</li>
                        <li>Email harus valid jika diisi</li>
                        <li>Status "Potensial" untuk client yang masih dalam tahap penawaran</li>
                        <li>Client akan secara otomatis ditangani oleh Anda sebagai sales</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Bootstrap validation
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
})()

// Real-time validation untuk email
document.getElementById('email').addEventListener('blur', function() {
    const email = this.value;
    if (email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            this.classList.add('is-invalid');
            const feedback = this.nextElementSibling;
            feedback.textContent = 'Format email tidak valid';
        } else {
            this.classList.remove('is-invalid');
        }
    }
});

// Telepon number formatting
document.getElementById('telepon').addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9+\-\s]/g, '');
});
</script>