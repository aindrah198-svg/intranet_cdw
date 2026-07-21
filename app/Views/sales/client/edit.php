<?php
$title = $title ?? 'Edit Client';
$active = $active ?? 'client';
?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header Card -->
            <div class="card border-0 shadow-lg mb-4">
                <div class="card-header bg-warning text-dark py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">
                                <i class="fas fa-edit me-2"></i>
                                <?= $title ?>
                            </h4>
                            <p class="mb-0 mt-1 small opacity-75"><?= $subtitle ?? 'Form Edit Data Client' ?></p>
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

                    <?php if (!isset($client) || !$client): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-exclamation-triangle fa-4x text-warning mb-3"></i>
                            <h4>Data Client Tidak Ditemukan</h4>
                            <p class="text-muted">Client yang ingin diedit tidak ditemukan dalam sistem.</p>
                            <a href="<?= base_url('sales/client') ?>" class="btn btn-primary">
                                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Client
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- Form Edit Client -->
<form action="<?= base_url('sales/client/update/' . $client['id']) ?>" method="POST" class="needs-validation" novalidate>
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="PUT">
                            
                            <div class="row">
                                <!-- Client Info Header -->
                                <div class="col-12 mb-4">
                                    <div class="alert alert-info">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-info-circle fa-2x me-3"></i>
                                            <div>
                                                <h6 class="mb-1">Mengedit Data Client</h6>
                                                <p class="mb-0">
                                                    Anda sedang mengedit data client: <strong><?= htmlspecialchars($client['nama_perusahaan']) ?></strong>
                                                    (<?= htmlspecialchars($client['kode_client']) ?>)
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kode Client -->
                                <div class="col-md-6 mb-3">
                                    <label for="kode_client" class="form-label">
                                        <i class="fas fa-hashtag me-1"></i> Kode Client <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control <?= (isset($validation) && $validation->hasError('kode_client')) ? 'is-invalid' : '' ?>" 
                                           id="kode_client" 
                                           name="kode_client" 
                                           value="<?= old('kode_client', $client['kode_client']) ?>" 
                                           required>
                                    <div class="invalid-feedback">
                                        <?= isset($validation) ? $validation->getError('kode_client') : 'Kode client wajib diisi' ?>
                                    </div>
                                    <small class="text-muted">Kode client harus unik</small>
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
                                           value="<?= old('nama_perusahaan', $client['nama_perusahaan']) ?>" 
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
                                           value="<?= old('nama_kontak', $client['nama_kontak']) ?>">
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
                                           value="<?= old('telepon', $client['telepon']) ?>">
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
                                           value="<?= old('email', $client['email']) ?>">
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
                                           value="<?= old('npwp', $client['npwp']) ?>">
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
                                        <option value="perusahaan" <?= (old('kategori', $client['kategori']) == 'perusahaan') ? 'selected' : '' ?>>Perusahaan</option>
                                        <option value="pemerintah" <?= (old('kategori', $client['kategori']) == 'pemerintah') ? 'selected' : '' ?>>Pemerintah</option>
                                        <option value="perorangan" <?= (old('kategori', $client['kategori']) == 'perorangan') ? 'selected' : '' ?>>Perorangan</option>
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
                                        <option value="active" <?= (old('status', $client['status']) == 'active') ? 'selected' : '' ?>>Active</option>
                                        <option value="inactive" <?= (old('status', $client['status']) == 'inactive') ? 'selected' : '' ?>>Inactive</option>
                                        <option value="potensial" <?= (old('status', $client['status']) == 'potensial') ? 'selected' : '' ?>>Potensial</option>
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
                                              rows="3"><?= old('alamat', $client['alamat']) ?></textarea>
                                    <div class="invalid-feedback">
                                        <?= isset($validation) ? $validation->getError('alamat') : '' ?>
                                    </div>
                                </div>

                                <!-- Sales Information (Read-only) -->
                                <div class="col-12 mb-4">
                                    <div class="alert alert-secondary">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user-tie fa-2x me-3"></i>
                                            <div>
                                                <h6 class="mb-1">Informasi Sales</h6>
                                                <p class="mb-0">
                                                    Client ini ditangani oleh: 
                                                    <strong><?= htmlspecialchars($client['nama_sales'] ?? 'Belum ditentukan') ?></strong>
                                                    (Tidak dapat diubah melalui form ini)
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between pt-3 border-top">
                                <div>
                                    <a href="<?= base_url('sales/client/detail/' . $client['id']) ?>" class="btn btn-info">
                                        <i class="fas fa-eye me-1"></i> Lihat Detail
                                    </a>
                                    <a href="<?= base_url('sales/client') ?>" class="btn btn-secondary ms-2">
                                        <i class="fas fa-times me-1"></i> Batal
                                    </a>
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save me-1"></i> Update Client
                                    </button>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Delete Section -->
            <?php if (isset($client) && $client): ?>
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
                            <h6 class="text-danger mb-1">Hapus Client</h6>
                            <p class="text-muted small mb-0">
                                Tindakan ini akan menghapus client secara permanen dari sistem.
                                Data yang dihapus tidak dapat dikembalikan.
                            </p>
                        </div>
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash-alt me-1"></i> Hapus Client
                        </button>
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
                                    Anda akan menghapus client:
                                    <strong><?= htmlspecialchars($client['nama_perusahaan']) ?></strong>
                                    (<?= htmlspecialchars($client['kode_client']) ?>)
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
                            <form action="<?= base_url('sales/client/delete/' . $client['id']) ?>" method="POST" class="d-inline">
                                <?= csrf_field() ?>
                               
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash-alt me-1"></i> Ya, Hapus Client
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
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
document.getElementById('email')?.addEventListener('blur', function() {
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
document.getElementById('telepon')?.addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9+\-\s]/g, '');
});

// Kode client validation
document.getElementById('kode_client')?.addEventListener('input', function() {
    const kode = this.value;
    if (kode.length > 50) {
        this.value = kode.substring(0, 50);
    }
});
</script>