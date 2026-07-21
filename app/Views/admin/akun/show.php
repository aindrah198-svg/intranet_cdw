<?php
$title = 'Detail Akun';
$active = 'akun';
?>

<?= $this->include('admin/templates/header') ?>
<?= $this->include('admin/templates/sidebar') ?>
<?= $this->include('admin/templates/navbar') ?>

<div class="dashboard-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-2" style="color: var(--cdw-dark); font-weight: 600;">
                <i class="fas fa-user-circle me-2"></i><?= $title ?>
            </h5>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('admin/karyawan/akun') ?>">Manajemen Akun</a></li>
                <li class="breadcrumb-item active">Detail Akun</li>
            </ol>
        </div>
        <div>
            <a href="<?= base_url('admin/karyawan/akun') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            <a href="<?= base_url('admin/karyawan/akun/edit/' . $user['id']) ?>" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Akun</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3 text-center">
                            <div class="avatar-circle-lg mb-3" style="background: linear-gradient(135deg, #6c757d, #495057);">
                                <?= strtoupper(substr($user['name'] ?? '?', 0, 1)) ?>
                            </div>
                            <h5 class="mb-1"><?= $user['name'] ?></h5>
                            <div class="mb-3">
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
                        <div class="col-md-9">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Username</label>
                                    <p class="fw-bold"><?= $user['username'] ?></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Email</label>
                                    <p class="fw-bold"><?= $user['email'] ?></p>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Role</label>
                                    <p>
                                        <?php
                                        $roleClass = '';
                                        switch ($user['role']) {
                                            case 'admin':
                                                $roleClass = 'bg-danger';
                                                break;
                                            case 'manager':
                                                $roleClass = 'bg-warning';
                                                break;
                                            case 'staff':
                                                $roleClass = 'bg-info';
                                                break;
                                        }
                                        ?>
                                        <span class="badge <?= $roleClass ?>">
                                            <?php if ($user['role'] == 'admin'): ?>
                                                <i class="fas fa-shield-alt me-1"></i>
                                            <?php elseif ($user['role'] == 'manager'): ?>
                                                <i class="fas fa-user-tie me-1"></i>
                                            <?php else: ?>
                                                <i class="fas fa-user me-1"></i>
                                            <?php endif; ?>
                                            <?= ucfirst($user['role']) ?>
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Status</label>
                                    <p>
                                        <span class="badge <?= $statusClass ?>">
                                            <?= ucfirst($user['status']) ?>
                                        </span>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Dibuat Pada</label>
                                    <p><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Diperbarui Pada</label>
                                    <p><?= date('d/m/Y H:i', strtotime($user['updated_at'])) ?></p>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Terakhir Login</label>
                                    <p>
                                        <?php if (!empty($user['last_login'])): ?>
                                            <?= date('d/m/Y H:i', strtotime($user['last_login'])) ?>
                                        <?php else: ?>
                                            <span class="text-muted">Belum pernah login</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Password Diubah</label>
                                    <p>
                                        <?php if (!empty($user['password_changed_at'])): ?>
                                            <?= date('d/m/Y H:i', strtotime($user['password_changed_at'])) ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($user['karyawan_id']) && !empty($user['nama_lengkap'])): ?>
                    <hr class="my-4">
                    
                    <div class="row">
                        <div class="col-12">
                            <h6 class="mb-3" style="color: var(--cdw-primary);">
                                <i class="fas fa-id-card me-2"></i>Data Karyawan Terkait
                            </h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label text-muted">NIK</label>
                                    <p class="fw-bold"><?= $user['nik'] ?? '-' ?></p>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted">Nama Lengkap</label>
                                    <p class="fw-bold"><?= $user['nama_lengkap'] ?></p>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted">Jabatan</label>
                                    <p><?= $user['jabatan'] ?? '-' ?></p>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label text-muted">Departemen</label>
                                    <p><?= $user['departemen'] ?? '-' ?></p>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted">Email Karyawan</label>
                                    <p><?= $user['karyawan_email'] ?? '-' ?></p>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted">Telepon</label>
                                    <p><?= $user['telepon'] ?? '-' ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Akun ini belum dikaitkan dengan data karyawan.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-cogs me-2"></i>Aksi Cepat</h6>
                </div>
                <div class="card-body">
                   <div class="d-grid gap-2">
    <a href="<?= base_url('admin/karyawan/akun/edit/' . $user['id']) ?>" class="btn btn-warning">
        <i class="fas fa-edit me-2"></i> Edit Akun
    </a>
    
    <button type="button" class="btn btn-primary" onclick="resetPassword(<?= $user['id'] ?>)">
        <i class="fas fa-key me-2"></i> Reset Password
    </button>
    
    <?php if ($user['status'] == 'active'): ?>
    <button type="button" class="btn btn-secondary" onclick="toggleStatus(<?= $user['id'] ?>)">
        <i class="fas fa-power-off me-2"></i> Nonaktifkan Akun
    </button>
    <?php else: ?>
    <button type="button" class="btn btn-success" onclick="toggleStatus(<?= $user['id'] ?>)">
        <i class="fas fa-power-off me-2"></i> Aktifkan Akun
    </button>
    <?php endif; ?>
    
    <button type="button" class="btn btn-danger" onclick="confirmDelete(<?= $user['id'] ?>)">
        <i class="fas fa-trash me-2"></i> Hapus Akun
    </button>
</div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-history me-2"></i>Aktivitas Terakhir</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0">
                            <small class="text-muted">Dibuat</small>
                            <div><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></div>
                        </li>
                        <li class="list-group-item px-0">
                            <small class="text-muted">Diperbarui</small>
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

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus Akun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus akun ini?</p>
                <p class="text-danger"><small>Akun yang dihapus tidak dapat digunakan untuk login. Data dapat dipulihkan dari menu sampah.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="confirmDelete" class="btn btn-danger">Hapus</a>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle-lg {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
    font-weight: bold;
    color: white;
    margin: 0 auto;
}
</style>

<script>
// Fungsi reset password
function resetPassword(userId) {
    if (confirm('Apakah Anda yakin ingin mereset password akun ini?')) {
        // Tampilkan loading
        $('#resetPasswordContent').html(`
            <div class="text-center">
                <i class="fas fa-spinner fa-spin fa-3x"></i>
                <p>Sedang mereset password...</p>
            </div>
        `);
        $('#resetPasswordModal').modal('show');
        
        $.ajax({
            url: '<?= base_url('admin/karyawan/akun/reset-password/') ?>' + userId,
            type: 'POST',
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var content = `
                        <div class="text-center">
                            <i class="fas fa-key fa-3x text-success mb-3"></i>
                            <h5>Password Berhasil Direset!</h5>
                            <p>Password baru untuk akun ini adalah:</p>
                            <div class="alert alert-info">
                                <h4 class="mb-0" id="newPasswordText">${response.newPassword}</h4>
                            </div>
                            <p class="text-muted">Silakan salin password ini dan berikan kepada pengguna.</p>
                        </div>
                    `;
                    
                    $('#resetPasswordContent').html(content);
                    $('#copyPasswordBtn').show();
                } else {
                    $('#resetPasswordContent').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            ${response.message}
                        </div>
                    `);
                    $('#copyPasswordBtn').hide();
                }
            },
            error: function(xhr, status, error) {
                $('#resetPasswordContent').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Terjadi kesalahan saat mereset password
                    </div>
                `);
                $('#copyPasswordBtn').hide();
            }
        });
    }
}

// Fungsi toggle status
function toggleStatus(userId) {
    var actionText = confirm('Apakah Anda yakin ingin mengubah status akun ini?');
    if (actionText) {
        // Tampilkan loading
        var originalText = event.target.innerHTML;
        event.target.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memproses...';
        event.target.disabled = true;
        
        $.ajax({
            url: '<?= base_url('admin/karyawan/akun/toggle-status/') ?>' + userId,
            type: 'POST',
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Refresh halaman setelah 1.5 detik
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                    
                    // Tampilkan pesan sukses
                    showToast('success', response.message);
                } else {
                    // Kembalikan tombol ke keadaan semula
                    event.target.innerHTML = originalText;
                    event.target.disabled = false;
                    
                    // Tampilkan pesan error
                    showToast('error', response.message);
                }
            },
            error: function(xhr, status, error) {
                // Kembalikan tombol ke keadaan semula
                event.target.innerHTML = originalText;
                event.target.disabled = false;
                
                showToast('error', 'Terjadi kesalahan saat mengubah status');
            }
        });
    }
}

// Fungsi konfirmasi hapus
function confirmDelete(id) {
    $('#confirmDelete').attr('href', '<?= base_url('admin/karyawan/akun/delete/') ?>' + id);
    $('#deleteModal').modal('show');
}

// Fungsi untuk menyalin password
$('#copyPasswordBtn').on('click', function() {
    var password = $('#newPasswordText').text().trim();
    
    navigator.clipboard.writeText(password).then(function() {
        showToast('success', 'Password berhasil disalin ke clipboard!');
        $('#resetPasswordModal').modal('hide');
    }, function(err) {
        showToast('error', 'Gagal menyalin password: ' + err);
    });
});

// Fungsi untuk menampilkan toast notification
function showToast(type, message) {
    var toastClass = type === 'success' ? 'text-bg-success' : 'text-bg-danger';
    var icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    
    var toastHTML = `
        <div class="toast align-items-center ${toastClass} border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas ${icon} me-2"></i>${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    // Buat container toast jika belum ada
    if (!$('#toastContainer').length) {
        $('body').append('<div id="toastContainer" class="toast-container position-fixed bottom-0 end-0 p-3"></div>');
    }
    
    $('#toastContainer').append(toastHTML);
    var toastElement = $('#toastContainer .toast').last();
    var toast = new bootstrap.Toast(toastElement[0]);
    toast.show();
}
</script>

<?= $this->include('admin/templates/footer') ?>