<?php
// C:\xampp\htdocs\intranet_cdw\app\Views\admin\profile\index.php

$title = 'Profil Saya';
$active = 'profile';
$css = [
    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
];
$scripts = [
    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
];
?>

<?= $this->include('admin/templates/header') ?>
<?= $this->include('admin/templates/sidebar') ?>
<?= $this->include('admin/templates/navbar') ?>

<!-- Main Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Profil Saya</h1>
        <div class="d-flex gap-2">
            <a href="<?= base_url('admin/profile/activity') ?>" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm">
                <i class="fas fa-history fa-sm text-white-50"></i> Log Aktivitas
            </a>
            <?php if(isset($karyawan) && !empty($karyawan['cv_path'])): ?>
            <a href="<?= base_url('admin/profile/download-cv') ?>" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
                <i class="fas fa-download fa-sm text-white-50"></i> Download CV
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Profil</li>
        </ol>
    </nav>

    <!-- Flash Messages -->
    <?php if (session()->has('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i><?= session('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <?php if (session()->has('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><?= session('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <?php if (session()->has('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>Terjadi kesalahan validasi
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <ul class="mb-0 mt-2">
            <?php foreach (session('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- Profile Information -->
    <div class="row">
        <!-- Left Column - Profile Card -->
        <div class="col-lg-4 mb-4">
            <!-- Profile Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary">
                    <h6 class="m-0 font-weight-bold text-white">Foto Profil</h6>
                </div>
                <div class="card-body text-center">
                    <!-- Profile Photo -->
                    <div class="mb-4">
                        <?php if(isset($karyawan) && !empty($karyawan['foto'])): ?>
                            <img src="<?= base_url('uploads/foto/' . $karyawan['foto']) ?>" 
                                 class="rounded-circle img-fluid shadow-lg" 
                                 style="width: 180px; height: 180px; object-fit: cover; border: 5px solid #e3e6f0;"
                                 alt="Foto Profil">
                        <?php else: ?>
                            <img src="<?= base_url('assets/img/undraw_profile.svg') ?>" 
                                 class="rounded-circle img-fluid shadow-lg" 
                                 style="width: 180px; height: 180px; border: 5px solid #e3e6f0;"
                                 alt="Default Profile">
                        <?php endif; ?>
                    </div>
                    
                    <h4 class="font-weight-bold text-primary mb-2"><?= esc($profile_user['name']) ?></h4>
                    <p class="mb-1">
                        <span class="badge bg-<?= ($profile_user['status'] == 'active') ? 'success' : 'danger' ?>">
                            <?= ucfirst($profile_user['status']) ?>
                        </span>
                        <span class="badge bg-info ms-1"><?= ucfirst($profile_user['role']) ?></span>
                    </p>
                    <p class="text-muted mb-3">
                        <i class="fas fa-user-tag me-1"></i><?= esc($profile_user['username']) ?>
                    </p>
                    
                    <!-- Upload Photo Form -->
                    <form action="<?= base_url('admin/profile/update-photo') ?>" method="post" enctype="multipart/form-data" id="photoForm">
                        <?= csrf_field() ?>
                        <div class="input-group mb-3">
                            <input type="file" class="form-control" name="photo" id="photoInput" accept="image/*" required>
                            <button type="submit" class="btn btn-primary" id="uploadBtn">
                                <i class="fas fa-upload me-1"></i> Upload
                            </button>
                        </div>
                        <small class="text-muted d-block">Format: JPG, PNG, GIF (Max 2MB)</small>
                    </form>
                </div>
            </div>

            <!-- Quick Info Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-success">
                    <h6 class="m-0 font-weight-bold text-white">Informasi Kontak</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="font-weight-bold text-primary mb-1">
                            <i class="fas fa-envelope me-2"></i>Email
                        </h6>
                        <p class="mb-2"><?= esc($profile_user['email']) ?></p>
                    </div>
                    
                    <?php if(isset($karyawan) && !empty($karyawan['telepon'])): ?>
                    <div class="mb-3">
                        <h6 class="font-weight-bold text-primary mb-1">
                            <i class="fas fa-phone me-2"></i>Telepon
                        </h6>
                        <p class="mb-2"><?= esc($karyawan['telepon']) ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if(isset($karyawan) && !empty($karyawan['alamat'])): ?>
                    <div class="mb-3">
                        <h6 class="font-weight-bold text-primary mb-1">
                            <i class="fas fa-map-marker-alt me-2"></i>Alamat
                        </h6>
                        <p class="mb-0"><?= esc($karyawan['alamat']) ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mt-4">
                        <h6 class="font-weight-bold text-primary mb-2">
                            <i class="fas fa-history me-2"></i>Login Terakhir
                        </h6>
                        <p class="text-muted">
                            <?= $profile_user['last_login'] ? date('d/m/Y H:i', strtotime($profile_user['last_login'])) : 'Belum pernah login' ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Edit Forms -->
        <div class="col-lg-8">
            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs mb-4" id="profileTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">
                        <i class="fas fa-user-edit me-2"></i>Edit Profil
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab">
                        <i class="fas fa-key me-2"></i>Ubah Password
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="karyawan-tab" data-bs-toggle="tab" data-bs-target="#karyawan" type="button" role="tab">
                        <i class="fas fa-briefcase me-2"></i>Data Karyawan
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="profileTabContent">
                <!-- Tab 1: Edit Profile -->
                <div class="tab-pane fade show active" id="info" role="tabpanel">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Edit Informasi Profil</h6>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('admin/profile/update') ?>" method="post" id="profileForm">
                                <?= csrf_field() ?>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Nama Lengkap *</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="<?= old('name', esc($profile_user['name'])) ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email *</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?= old('email', esc($profile_user['email'])) ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" 
                                               value="<?= esc($profile_user['username']) ?>" readonly>
                                        <small class="text-muted">Username tidak dapat diubah</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="role" class="form-label">Role</label>
                                        <input type="text" class="form-control" id="role" 
                                               value="<?= ucfirst($profile_user['role']) ?>" readonly>
                                    </div>
                                </div>
                                
                                <?php if(isset($karyawan)): ?>
                                <div class="row mt-3">
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Telepon</label>
                                        <input type="text" class="form-control" id="phone" name="phone" 
                                               value="<?= old('phone', esc($karyawan['telepon'] ?? '')) ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="address" class="form-label">Alamat</label>
                                        <textarea class="form-control" id="address" name="address" rows="3"><?= old('address', esc($karyawan['alamat'] ?? '')) ?></textarea>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                                    </button>
                                    <button type="reset" class="btn btn-secondary">
                                        <i class="fas fa-redo me-1"></i> Reset
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Tab 2: Change Password -->
                <div class="tab-pane fade" id="password" role="tabpanel">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Ubah Password</h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Password baru minimal 6 karakter dan berbeda dengan password saat ini.
                            </div>
                            
                            <form action="<?= base_url('admin/profile/change-password') ?>" method="post" id="passwordForm">
                                <?= csrf_field() ?>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="current_password" class="form-label">Password Saat Ini *</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                                            <button class="btn btn-outline-secondary" type="button" id="toggleCurrentPassword">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="new_password" class="form-label">Password Baru *</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6">
                                            <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        <small class="form-text text-muted">Minimal 6 karakter</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="confirm_password" class="form-label">Konfirmasi Password Baru *</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6">
                                            <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        <small id="passwordMatch" class="form-text"></small>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-key me-1"></i> Ubah Password
                                    </button>
                                    <button type="reset" class="btn btn-secondary">
                                        <i class="fas fa-redo me-1"></i> Reset
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Tab 3: Karyawan Data -->
                <div class="tab-pane fade" id="karyawan" role="tabpanel">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Data Karyawan</h6>
                        </div>
                        <div class="card-body">
                            <?php if(isset($karyawan)): ?>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">NIK</label>
                                        <input type="text" class="form-control" value="<?= esc($karyawan['nik']) ?>" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nama Lengkap</label>
                                        <input type="text" class="form-control" value="<?= esc($karyawan['nama_lengkap']) ?>" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Jabatan</label>
                                        <input type="text" class="form-control" value="<?= esc($karyawan['jabatan']) ?>" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Departemen</label>
                                        <input type="text" class="form-control" value="<?= esc($karyawan['departemen']) ?>" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Status Karyawan</label>
                                        <input type="text" class="form-control" value="<?= esc($karyawan['status_karyawan']) ?>" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tanggal Masuk</label>
                                        <input type="text" class="form-control" 
                                               value="<?= $karyawan['tanggal_masuk'] ? date('d/m/Y', strtotime($karyawan['tanggal_masuk'])) : '-' ?>" 
                                               readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tempat Lahir</label>
                                        <input type="text" class="form-control" value="<?= esc($karyawan['tempat_lahir'] ?? '-') ?>" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tanggal Lahir</label>
                                        <input type="text" class="form-control" 
                                               value="<?= $karyawan['tanggal_lahir'] ? date('d/m/Y', strtotime($karyawan['tanggal_lahir'])) : '-' ?>" 
                                               readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Agama</label>
                                        <input type="text" class="form-control" value="<?= esc($karyawan['agama'] ?? '-') ?>" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Status Pernikahan</label>
                                        <input type="text" class="form-control" value="<?= esc($karyawan['status_pernikahan'] ?? '-') ?>" readonly>
                                    </div>
                                </div>
                                
                                <hr class="my-4">
                                
                                <h6 class="font-weight-bold text-primary mb-3">Informasi Keuangan</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">No. NPWP</label>
                                        <input type="text" class="form-control" value="<?= esc($karyawan['no_npwp'] ?? '-') ?>" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">No. BPJS Kesehatan</label>
                                        <input type="text" class="form-control" value="<?= esc($karyawan['no_bpjs_kes'] ?? '-') ?>" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">No. BPJS Ketenagakerjaan</label>
                                        <input type="text" class="form-control" value="<?= esc($karyawan['no_bpjs_tk'] ?? '-') ?>" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Bank</label>
                                        <input type="text" class="form-control" value="<?= esc($karyawan['bank'] ?? '-') ?>" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">No. Rekening</label>
                                        <input type="text" class="form-control" value="<?= esc($karyawan['no_rekening'] ?? '-') ?>" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nama Rekening</label>
                                        <input type="text" class="form-control" value="<?= esc($karyawan['nama_rekening'] ?? '-') ?>" readonly>
                                    </div>
                                </div>
                                
                                <hr class="my-4">
                                
                                <h6 class="font-weight-bold text-primary mb-3">Pendidikan</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Pendidikan Terakhir</label>
                                        <input type="text" class="form-control" value="<?= esc($karyawan['pendidikan_terakhir'] ?? '-') ?>" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Jurusan</label>
                                        <input type="text" class="form-control" value="<?= esc($karyawan['jurusan'] ?? '-') ?>" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Institusi</label>
                                        <input type="text" class="form-control" value="<?= esc($karyawan['institusi'] ?? '-') ?>" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tahun Lulus</label>
                                        <input type="text" class="form-control" value="<?= esc($karyawan['tahun_lulus'] ?? '-') ?>" readonly>
                                    </div>
                                </div>
                                
                                <hr class="my-4">
                                
                                <h6 class="font-weight-bold text-primary mb-3">Kontak Darurat</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nama</label>
                                        <input type="text" class="form-control" value="<?= esc($karyawan['kontak_darurat_nama'] ?? '-') ?>" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Hubungan</label>
                                        <input type="text" class="form-control" value="<?= esc($karyawan['kontak_darurat_hubungan'] ?? '-') ?>" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Telepon</label>
                                        <input type="text" class="form-control" value="<?= esc($karyawan['kontak_darurat_telepon'] ?? '-') ?>" readonly>
                                    </div>
                                </div>
                                
                            <?php else: ?>
                                <div class="alert alert-warning text-center">
                                    <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                                    <h5>Data Karyawan Tidak Ditemukan</h5>
                                    <p class="mb-0">Akun Anda belum terhubung dengan data karyawan. Hubungi administrator untuk informasi lebih lanjut.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Photo Preview Modal -->
<div class="modal fade" id="photoPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Pratinjau Foto Profil</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="previewImage" src="" class="img-fluid rounded" alt="Preview">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('photoInput').click()">
                    <i class="fas fa-camera me-1"></i> Pilih Foto Lain
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize Select2 if needed
    if ($('.select2').length > 0) {
        $('.select2').select2({
            placeholder: 'Pilih...',
            allowClear: true,
            width: '100%'
        });
    }

    // Password toggle functionality
    $('#toggleCurrentPassword').click(function() {
        const passwordField = $('#current_password');
        const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
        passwordField.attr('type', type);
        $(this).find('i').toggleClass('fa-eye fa-eye-slash');
    });

    $('#toggleNewPassword').click(function() {
        const passwordField = $('#new_password');
        const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
        passwordField.attr('type', type);
        $(this).find('i').toggleClass('fa-eye fa-eye-slash');
    });

    $('#toggleConfirmPassword').click(function() {
        const passwordField = $('#confirm_password');
        const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
        passwordField.attr('type', type);
        $(this).find('i').toggleClass('fa-eye fa-eye-slash');
    });

    // Check password match
    function checkPasswordMatch() {
        const newPassword = $('#new_password').val();
        const confirmPassword = $('#confirm_password').val();
        const matchText = $('#passwordMatch');

        if (!newPassword || !confirmPassword) {
            matchText.text('');
            return;
        }

        if (newPassword === confirmPassword) {
            matchText.html('<i class="fas fa-check-circle text-success me-1"></i>Password cocok');
            matchText.removeClass('text-danger').addClass('text-success');
        } else {
            matchText.html('<i class="fas fa-times-circle text-danger me-1"></i>Password tidak cocok');
            matchText.removeClass('text-success').addClass('text-danger');
        }
    }

    $('#new_password, #confirm_password').on('input', checkPasswordMatch);

    // Photo preview
    $('#photoInput').change(function() {
        const file = this.files[0];
        if (!file) return;

        // Validate file type
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!validTypes.includes(file.type)) {
            alert('Format file tidak didukung. Gunakan JPG, PNG, atau GIF.');
            $(this).val('');
            return;
        }

        // Validate file size (2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file maksimal 2MB.');
            $(this).val('');
            return;
        }

        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#previewImage').attr('src', e.target.result);
            $('#photoPreviewModal').modal('show');
        };
        reader.readAsDataURL(file);
    });

    // Form validation
    $('#profileForm').submit(function(e) {
        const name = $('#name').val().trim();
        const email = $('#email').val().trim();

        if (!name || !email) {
            e.preventDefault();
            alert('Nama dan email harus diisi.');
            return false;
        }

        if (!validateEmail(email)) {
            e.preventDefault();
            alert('Format email tidak valid.');
            return false;
        }

        return true;
    });

    $('#passwordForm').submit(function(e) {
        const currentPassword = $('#current_password').val();
        const newPassword = $('#new_password').val();
        const confirmPassword = $('#confirm_password').val();

        if (!currentPassword || !newPassword || !confirmPassword) {
            e.preventDefault();
            alert('Semua field password harus diisi.');
            return false;
        }

        if (newPassword.length < 6) {
            e.preventDefault();
            alert('Password baru minimal 6 karakter.');
            return false;
        }

        if (newPassword !== confirmPassword) {
            e.preventDefault();
            alert('Password baru dan konfirmasi tidak cocok.');
            return false;
        }

        return true;
    });

    $('#photoForm').submit(function(e) {
        const fileInput = $('#photoInput')[0];
        
        if (!fileInput.files.length) {
            e.preventDefault();
            alert('Pilih foto terlebih dahulu.');
            return false;
        }

        // Show loading
        $('#uploadBtn').html('<i class="fas fa-spinner fa-spin me-1"></i> Mengupload...');
        $('#uploadBtn').prop('disabled', true);
    });

    // Helper function
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);

    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>

<style>
/* Custom styles for Profile page */
.nav-tabs .nav-link {
    color: #495057;
    font-weight: 500;
    padding: 12px 20px;
}

.nav-tabs .nav-link:hover {
    color: #4e73df;
    background-color: rgba(78, 115, 223, 0.05);
}

.nav-tabs .nav-link.active {
    color: #4e73df;
    border-color: #dee2e6 #dee2e6 #fff;
    border-top: 2px solid #4e73df;
    background-color: white;
}

.tab-pane {
    padding-top: 20px;
}

.card {
    border-radius: 10px;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.card-header {
    border-top-left-radius: 10px !important;
    border-top-right-radius: 10px !important;
}

.profile-photo-container {
    position: relative;
    display: inline-block;
}

.profile-photo-container .btn-photo-overlay {
    position: absolute;
    bottom: 10px;
    right: 10px;
    opacity: 0;
    transition: opacity 0.3s;
}

.profile-photo-container:hover .btn-photo-overlay {
    opacity: 1;
}

.input-group-text {
    background-color: #f8f9fc;
}

.form-label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 5px;
}

.form-control:focus, .form-select:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.badge {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.85em;
}

.bg-success {
    background: linear-gradient(135deg, #1cc88a, #17a673) !important;
}

.bg-danger {
    background: linear-gradient(135deg, #e74a3b, #d52a1a) !important;
}

.bg-info {
    background: linear-gradient(135deg, #36b9cc, #258391) !important;
}

.bg-primary {
    background: linear-gradient(135deg, #4e73df, #2e59d9) !important;
}

.bg-warning {
    background: linear-gradient(135deg, #f6c23e, #dda20a) !important;
}

/* Tab content animation */
.tab-pane {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .nav-tabs .nav-link {
        padding: 10px 15px;
        font-size: 0.9rem;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .profile-photo-container img {
        width: 150px;
        height: 150px;
    }
}
</style>

<?= $this->include('admin/templates/footer') ?>