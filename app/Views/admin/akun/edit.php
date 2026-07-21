<?php
$title = 'Edit Akun';
$active = 'akun';
// Pastikan $existingRoles sudah ada dari controller
$existingRoles = $existingRoles ?? ['admin', 'manager', 'staff'];
?>

<?= $this->include('admin/templates/header') ?>
<?= $this->include('admin/templates/sidebar') ?>
<?= $this->include('admin/templates/navbar') ?>

<div class="dashboard-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-2" style="color: var(--cdw-dark); font-weight: 600;">
                <i class="fas fa-user-edit me-2"></i><?= $title ?>
            </h5>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('admin/karyawan/akun') ?>">Manajemen Akun</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('admin/karyawan/akun/show/' . $user['id']) ?>">Detail Akun</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </div>
        <div>
            <a href="<?= base_url('admin/karyawan/akun/show/' . $user['id']) ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                   <?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <ul class="mb-0" style="margin-left: 20px;">
            <?php 
            $errors = session()->getFlashdata('errors');
            if (is_array($errors)): 
                foreach ($errors as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; 
            else: ?>
                <li><?= $errors ?></li>
            <?php endif; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
                    
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?= base_url('admin/karyawan/akun/update/' . $user['id']) ?>" method="post" id="editAkunForm">
                        <?= csrf_field() ?>
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-body p-3">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <h6 class="mb-2">Informasi Akun</h6>
                                                <p class="mb-1 text-muted">
                                                    <small>ID: <?= $user['id'] ?> • Dibuat: <?= date('d/m/Y', strtotime($user['created_at'])) ?></small>
                                                </p>
                                                <?php if ($user['karyawan_id'] && isset($karyawan)): ?>
                                                <p class="mb-0">
                                                    <i class="fas fa-link me-1"></i> 
                                                    Terkait dengan karyawan: 
                                                    <strong><?= $karyawan['nama_lengkap'] ?? 'Tidak diketahui' ?></strong>
                                                    (NIK: <?= $karyawan['nik'] ?? '-' ?>)
                                                </p>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-4 text-end">
                                                <?php
                                                $statusClass = '';
                                                switch ($user['status']) {
                                                    case 'active':
                                                        $statusClass = 'bg-success';
                                                        break;
                                                    case 'inactive':
                                                        $statusClass = 'bg-secondary';
                                                        break;
                                                    case 'suspended':
                                                        $statusClass = 'bg-danger';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge <?= $statusClass ?>">
                                                    <?= ucfirst($user['status']) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <h6 class="mb-3" style="color: var(--cdw-blue);">
                            <i class="fas fa-user-circle me-2"></i>Data Akun
                        </h6>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Username *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" name="username" class="form-control" 
                                           value="<?= old('username', $user['username']) ?>" 
                                           placeholder="Masukkan username" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Lengkap *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                    <input type="text" name="name" class="form-control" 
                                           value="<?= old('name', $user['name']) ?>" 
                                           placeholder="Masukkan nama lengkap" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Email *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" class="form-control" 
                                           value="<?= old('email', $user['email']) ?>" 
                                           placeholder="Masukkan email" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Role *</label>
                                <div class="input-group">
                                    <select name="role" id="roleSelect" class="form-select" required onchange="handleRoleChange(this.value)">
                                        <option value="">-- Pilih Role --</option>
                                        <?php foreach ($existingRoles as $role): ?>
                                            <option value="<?= $role ?>" <?= (old('role', $user['role']) == $role) ? 'selected' : '' ?>>
                                                <?= ucfirst($role) ?>
                                            </option>
                                        <?php endforeach; ?>
                                        <option value="custom" <?= (old('role') == 'custom') ? 'selected' : '' ?>>-- Buat Role Baru --</option>
                                    </select>
                                </div>
                                <div class="mt-2" id="customRoleContainer" style="display: <?= (old('role') == 'custom') ? 'block' : 'none'; ?>;">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-plus-circle"></i></span>
                                        <input type="text" name="custom_role" id="customRole" 
                                               class="form-control" placeholder="Masukkan nama role baru"
                                               value="<?= old('custom_role') ?>">
                                        <button type="button" class="btn btn-outline-primary" onclick="useCustomRole()">
                                            Gunakan
                                        </button>
                                    </div>
                                    <small class="text-muted">Role akan ditambahkan ke daftar pilihan</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Status Akun *</label>
                                <select name="status" class="form-select" required>
                                    <option value="">-- Pilih Status --</option>
                                    <option value="active" <?= old('status', $user['status']) == 'active' ? 'selected' : '' ?>>Aktif</option>
                                    <option value="inactive" <?= old('status', $user['status']) == 'inactive' ? 'selected' : '' ?>>Nonaktif</option>
                                    <option value="suspended" <?= old('status', $user['status']) == 'suspended' ? 'selected' : '' ?>>Suspended</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password" id="password" class="form-control" 
                                           placeholder="Biarkan kosong jika tidak ingin mengubah">
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                            </div>
                        </div>
                        
                        <div class="row mb-4" id="passwordConfirmationRow" style="display: none;">
                            <div class="col-md-6">
                                <label class="form-label">Konfirmasi Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password_confirmation" id="password_confirmation" 
                                           class="form-control" placeholder="Konfirmasi password">
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password_confirmation')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('admin/karyawan/akun/show/' . $user['id']) ?>" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Panduan Edit</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-lightbulb me-2"></i>Tips Edit Akun:</h6>
                        <ul class="mb-0" style="margin-left: 20px;">
                            <li>Username harus unik dan tidak boleh sama dengan akun lain</li>
                            <li>Email harus valid dan belum digunakan akun lain</li>
                            <li>Role menentukan hak akses pengguna</li>
                            <li>Status aktif/nonaktif mengontrol kemampuan login</li>
                            <li>Password hanya diisi jika ingin mengubah password</li>
                        </ul>
                    </div>
                    
                    <div class="mt-4">
                        <h6 class="mb-3">Aksi Lainnya</h6>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-warning" onclick="resetPassword(<?= $user['id'] ?>, '<?= addslashes($user['name']) ?>')">
                                <i class="fas fa-key me-2"></i> Reset Password
                            </button>
                            <?php if ($user['status'] == 'active'): ?>
                            <button type="button" class="btn btn-secondary" onclick="toggleStatus(<?= $user['id'] ?>, '<?= addslashes($user['name']) ?>')">
                                <i class="fas fa-power-off me-2"></i> Nonaktifkan Akun
                            </button>
                            <?php else: ?>
                            <button type="button" class="btn btn-success" onclick="toggleStatus(<?= $user['id'] ?>, '<?= addslashes($user['name']) ?>')">
                                <i class="fas fa-power-off me-2"></i> Aktifkan Akun
                            </button>
                            <?php endif; ?>
                            <a href="<?= base_url('admin/karyawan/akun/delete/' . $user['id']) ?>" 
                               class="btn btn-danger" 
                               onclick="return confirm('Apakah Anda yakin ingin menghapus akun <?= addslashes($user['name']) ?>?')">
                                <i class="fas fa-trash me-2"></i> Hapus Akun
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0">
                            <small class="text-muted">Dibuat</small>
                            <div><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></div>
                        </li>
                        <li class="list-group-item px-0">
                            <small class="text-muted">Diperbarui Terakhir</small>
                            <div><?= date('d/m/Y H:i', strtotime($user['updated_at'])) ?></div>
                        </li>
                        <?php if (!empty($user['last_login'])): ?>
                        <li class="list-group-item px-0">
                            <small class="text-muted">Login Terakhir</small>
                            <div><?= date('d/m/Y H:i', strtotime($user['last_login'])) ?></div>
                        </li>
                        <?php endif; ?>
                        <?php if (!empty($user['password_changed_at'])): ?>
                        <li class="list-group-item px-0">
                            <small class="text-muted">Password Diubah</small>
                            <div><?= date('d/m/Y H:i', strtotime($user['password_changed_at'])) ?></div>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reset Password -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reset Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="resetPasswordContent">
                    <!-- Konten akan diisi oleh JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="copyPasswordBtn" style="display: none;">
                    <i class="fas fa-copy me-1"></i> Salin Password
                </button>
            </div>
        </div>
    </div>
</div>

<style>
#passwordConfirmationRow {
    transition: all 0.3s ease;
}
</style>

<script>

    // Check username availability
document.querySelector('input[name="username"]').addEventListener('blur', function() {
    var username = this.value;
    var userId = <?= $user['id'] ?>;
    
    if (username.length >= 3) {
        $.ajax({
            url: '<?= base_url('admin/karyawan/akun/check-username/') ?>' + encodeURIComponent(username),
            type: 'GET',
            data: {
                current_id: userId
            },
            dataType: 'json',
            success: function(response) {
                var feedback = $('#usernameFeedback');
                if (!feedback.length) {
                    // Buat feedback element jika belum ada
                    $('input[name="username"]').after('<div class="mt-1"><small id="usernameFeedback" class="form-text"></small></div>');
                    feedback = $('#usernameFeedback');
                }
                
                if (response.exists) {
                    feedback.html('<span class="text-danger"><i class="fas fa-times-circle"></i> Username sudah digunakan</span>');
                    $('input[name="username"]').addClass('is-invalid');
                } else {
                    feedback.html('<span class="text-success"><i class="fas fa-check-circle"></i> Username tersedia</span>');
                    $('input[name="username"]').removeClass('is-invalid');
                }
            }
        });
    }
});

// Check email availability
document.querySelector('input[name="email"]').addEventListener('blur', function() {
    var email = this.value;
    var userId = <?= $user['id'] ?>;
    
    if (email.includes('@')) {
        $.ajax({
            url: '<?= base_url('admin/karyawan/akun/check-email/') ?>' + encodeURIComponent(email),
            type: 'GET',
            data: {
                current_id: userId
            },
            dataType: 'json',
            success: function(response) {
                var feedback = $('#emailFeedback');
                if (!feedback.length) {
                    // Buat feedback element jika belum ada
                    $('input[name="email"]').after('<div class="mt-1"><small id="emailFeedback" class="form-text"></small></div>');
                    feedback = $('#emailFeedback');
                }
                
                if (response.exists) {
                    feedback.html('<span class="text-danger"><i class="fas fa-times-circle"></i> Email sudah digunakan</span>');
                    $('input[name="email"]').addClass('is-invalid');
                } else {
                    feedback.html('<span class="text-success"><i class="fas fa-check-circle"></i> Email tersedia</span>');
                    $('input[name="email"]').removeClass('is-invalid');
                }
            }
        });
    }
});

// Tampilkan/menyembunyikan konfirmasi password
document.getElementById('password').addEventListener('input', function() {
    var confirmationRow = document.getElementById('passwordConfirmationRow');
    if (this.value.length > 0) {
        confirmationRow.style.display = 'block';
        document.getElementById('password_confirmation').setAttribute('required', 'required');
    } else {
        confirmationRow.style.display = 'none';
        document.getElementById('password_confirmation').removeAttribute('required');
    }
});

function togglePassword(fieldId) {
    var field = document.getElementById(fieldId);
    var type = field.getAttribute('type') === 'password' ? 'text' : 'password';
    field.setAttribute('type', type);
}

function resetPassword(userId, userName) {
    if (confirm('Apakah Anda yakin ingin mereset password untuk akun "' + userName + '"?')) {
        $.ajax({
            url: '<?= base_url('admin/karyawan/akun/reset-password/') ?>' + userId,
            type: 'POST',
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            beforeSend: function() {
                // Tampilkan loading
                $('#resetPasswordContent').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-3x"></i><p>Mereset password...</p></div>');
                $('#resetPasswordModal').modal('show');
            },
            success: function(response) {
                if (response.success) {
                    var content = `
                        <div class="text-center">
                            <i class="fas fa-key fa-3x text-success mb-3"></i>
                            <h5>Password Berhasil Direset!</h5>
                            <p>Password baru untuk akun <strong>${userName}</strong> adalah:</p>
                            <div class="alert alert-info">
                                <h4 class="mb-0" id="newPasswordText">${response.newPassword}</h4>
                            </div>
                            <p class="text-muted">Silakan salin password ini dan berikan kepada pengguna.</p>
                        </div>
                    `;
                    
                    $('#resetPasswordContent').html(content);
                    $('#copyPasswordBtn').show();
                } else {
                    $('#resetPasswordContent').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function() {
                $('#resetPasswordContent').html('<div class="alert alert-danger">Terjadi kesalahan saat mereset password</div>');
            }
        });
    }
}

function toggleStatus(userId, userName) {
    var action = confirm('Apakah Anda yakin ingin mengubah status akun "' + userName + '"?');
    if (action) {
        $.ajax({
            url: '<?= base_url('admin/karyawan/akun/update-status/') ?>' + userId,
            type: 'POST',
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    window.location.reload();
                } else {
                    alert('Gagal mengubah status: ' + response.message);
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat mengubah status');
            }
        });
    }
}

$('#copyPasswordBtn').on('click', function() {
    var password = $('#newPasswordText').text().trim();
    
    navigator.clipboard.writeText(password).then(function() {
        alert('Password berhasil disalin ke clipboard!');
        $('#resetPasswordModal').modal('hide');
    }, function(err) {
        alert('Gagal menyalin password: ' + err);
    });
});

// Validasi form edit
$('#editAkunForm').on('submit', function(e) {
    var password = $('#password').val();
    var confirmPassword = $('#password_confirmation').val();
    
    if (password && password.length < 6) {
        e.preventDefault();
        alert('Password minimal 6 karakter!');
        return false;
    }
    
    if (password && password !== confirmPassword) {
        e.preventDefault();
        alert('Password dan konfirmasi password tidak cocok!');
        return false;
    }
    
    return true;
});

// Fungsi untuk custom role (sama seperti di create)
function handleRoleChange(value) {
    var customRoleContainer = document.getElementById('customRoleContainer');
    var roleSelect = document.getElementById('roleSelect');
    
    if (value === 'custom') {
        customRoleContainer.style.display = 'block';
        roleSelect.required = false;
    } else {
        customRoleContainer.style.display = 'none';
        roleSelect.required = true;
        document.getElementById('customRole').value = '';
    }
}

function useCustomRole() {
    var customRoleInput = document.getElementById('customRole');
    var customRole = customRoleInput.value.trim();
    
    if (customRole.length < 2) {
        alert('Nama role minimal 2 karakter');
        return;
    }
    
    // Tambahkan role baru ke dropdown
    var roleSelect = document.getElementById('roleSelect');
    var option = document.createElement('option');
    option.value = customRole;
    option.textContent = customRole.charAt(0).toUpperCase() + customRole.slice(1);
    
    // Insert sebelum option "Buat Role Baru"
    var customOption = roleSelect.querySelector('option[value="custom"]');
    roleSelect.insertBefore(option, customOption);
    
    // Pilih role yang baru ditambahkan
    roleSelect.value = customRole;
    
    // Sembunyikan input custom
    document.getElementById('customRoleContainer').style.display = 'none';
    customRoleInput.value = '';
}
</script>

<?= $this->include('admin/templates/footer') ?>