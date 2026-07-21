<?php
$title = 'Buat Akun Baru';
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
                <i class="fas fa-user-plus me-2"></i><?= $title ?>
            </h5>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('admin/karyawan/akun') ?>">Manajemen Akun</a></li>
                <li class="breadcrumb-item active">Buat Baru</li>
            </ol>
        </div>
        <div>
            <a href="<?= base_url('admin/karyawan/akun') ?>" class="btn btn-secondary">
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
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
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
                    
                    <form action="<?= base_url('admin/karyawan/akun/store') ?>" method="post" id="createAkunForm">
                        <?= csrf_field() ?>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Pilih Karyawan (Opsional)</label>
                                <select name="karyawan_id" id="karyawan_id" class="form-select" onchange="updateKaryawanData()">
                                    <option value="">-- Pilih Karyawan --</option>
                                    <?php if (isset($karyawanBelumAkun) && !empty($karyawanBelumAkun)): ?>
                                        <?php foreach ($karyawanBelumAkun as $karyawan): ?>
                                            <option value="<?= $karyawan['id'] ?>" 
                                                    data-nama="<?= $karyawan['nama_lengkap'] ?>"
                                                    data-email="<?= $karyawan['email'] ?? '' ?>">
                                                <?= $karyawan['nik'] ?> - <?= $karyawan['nama_lengkap'] ?> (<?= $karyawan['jabatan'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="">Tidak ada karyawan tersedia</option>
                                    <?php endif; ?>
                                </select>
                                <small class="text-muted">Pilih karyawan untuk mengaitkan akun dengan data karyawan</small>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body p-3">
                                        <small class="text-muted">Informasi Karyawan Terpilih:</small>
                                        <div id="karyawanInfo" class="mt-2">
                                            <p class="mb-1 text-muted">Belum ada karyawan dipilih</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <h6 class="mb-3" style="color: var(--cdw-blue);">
                            <i class="fas fa-user-circle me-2"></i>Informasi Akun
                        </h6>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Username *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" name="username" id="username" class="form-control" 
                                           value="<?= old('username') ?>" 
                                           placeholder="Masukkan username" required
                                           onkeyup="checkUsernameAvailability(this.value)">
                                </div>
                                <div class="mt-1">
                                    <small id="usernameFeedback" class="form-text"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Lengkap *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                    <input type="text" name="name" id="name" class="form-control" 
                                           value="<?= old('name') ?>" 
                                           placeholder="Masukkan nama lengkap" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Email *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" id="email" class="form-control" 
                                           value="<?= old('email') ?>" 
                                           placeholder="Masukkan email" required
                                           onkeyup="checkEmailAvailability(this.value)">
                                </div>
                                <div class="mt-1">
                                    <small id="emailFeedback" class="form-text"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Role *</label>
                                <div class="input-group">
                                    <select name="role" id="roleSelect" class="form-select" required onchange="handleRoleChange(this.value)">
                                        <option value="">-- Pilih Role --</option>
                                        <?php foreach ($existingRoles as $role): ?>
                                            <option value="<?= $role ?>" <?= old('role') == $role ? 'selected' : '' ?>>
                                                <?= ucfirst($role) ?>
                                            </option>
                                        <?php endforeach; ?>
                                        <option value="custom" <?= old('role') == 'custom' ? 'selected' : '' ?>>-- Buat Role Baru --</option>
                                    </select>
                                </div>
                                <div class="mt-2" id="customRoleContainer" style="display: <?= old('role') == 'custom' ? 'block' : 'none'; ?>;">
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
                                <label class="form-label">Password *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password" id="password" class="form-control" 
                                           placeholder="Masukkan password" required>
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Minimal 6 karakter</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Konfirmasi Password *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password_confirmation" id="password_confirmation" 
                                           class="form-control" placeholder="Konfirmasi password" required>
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password_confirmation')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Status Akun</label>
                                <select name="status" class="form-select">
                                    <option value="active" <?= old('status', 'active') == 'active' ? 'selected' : '' ?>>Aktif</option>
                                    <option value="inactive" <?= old('status') == 'inactive' ? 'selected' : '' ?>>Nonaktif</option>
                                    <option value="suspended" <?= old('status') == 'suspended' ? 'selected' : '' ?>>Suspended</option>
                                </select>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('admin/karyawan/akun') ?>" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan Akun
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Panduan</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-lightbulb me-2"></i>Tips Membuat Akun:</h6>
                        <ul class="mb-0" style="margin-left: 20px;">
                            <li>Username harus unik dan mudah diingat</li>
                            <li>Email harus valid dan belum terdaftar</li>
                            <li>Password minimal 6 karakter</li>
                            <li>Pilih role dari daftar atau buat role baru</li>
                            <li>Kaitkan dengan karyawan untuk integrasi data</li>
                        </ul>
                    </div>
                    
                    <h6 class="mt-4 mb-3">Role yang Tersedia:</h6>
                    <?php foreach ($existingRoles as $role): ?>
                        <div class="mb-2">
                            <?php
                            $badgeClass = '';
                            switch (strtolower($role)) {
                                case 'admin':
                                    $badgeClass = 'bg-danger';
                                    break;
                                case 'manager':
                                    $badgeClass = 'bg-warning';
                                    break;
                                case 'teknisi':
                                    $badgeClass = 'bg-primary';
                                    break;
                                default:
                                    $badgeClass = 'bg-info';
                            }
                            ?>
                            <span class="badge <?= $badgeClass ?> mb-1"><?= ucfirst($role) ?></span>
                            <?php if (strtolower($role) == 'admin'): ?>
                                <p class="text-muted small mb-0">Akses penuh ke semua fitur sistem</p>
                            <?php elseif (strtolower($role) == 'manager'): ?>
                                <p class="text-muted small mb-0">Akses terbatas, dapat mengelola data karyawan</p>
                            <?php elseif (strtolower($role) == 'teknisi'): ?>
                                <p class="text-muted small mb-0">Akses untuk pekerjaan teknis dan perawatan</p>
                            <?php else: ?>
                                <p class="text-muted small mb-0">Akses dasar sesuai kebutuhan</p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Role Baru:</strong> Anda dapat membuat role baru dengan memilih "Buat Role Baru" dan mengetikkan nama role yang diinginkan.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateKaryawanData() {
        var select = document.getElementById('karyawan_id');
        var selectedOption = select.options[select.selectedIndex];
        var karyawanInfo = document.getElementById('karyawanInfo');
        
        if (select.value) {
            var nama = selectedOption.getAttribute('data-nama');
            var email = selectedOption.getAttribute('data-email');
            
            var html = `
                <p class="mb-1"><strong>Nama:</strong> ${nama}</p>
                <p class="mb-1"><strong>Email:</strong> ${email || '-'}</p>
            `;
            
            karyawanInfo.innerHTML = html;
            
            // Auto-fill form fields
            document.getElementById('name').value = nama;
            if (email) {
                document.getElementById('email').value = email;
                checkEmailAvailability(email);
            }
        } else {
            karyawanInfo.innerHTML = '<p class="mb-1 text-muted">Belum ada karyawan dipilih</p>';
            document.getElementById('name').value = '';
            document.getElementById('email').value = '';
        }
    }
    
    function checkUsernameAvailability(username) {
        if (username.length >= 3) {
            $.ajax({
                url: '<?= base_url('admin/karyawan/akun/check-username/') ?>' + encodeURIComponent(username),
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    var feedback = $('#usernameFeedback');
                    if (response.exists) {
                        feedback.html('<span class="text-danger"><i class="fas fa-times-circle"></i> Username sudah digunakan</span>');
                    } else {
                        feedback.html('<span class="text-success"><i class="fas fa-check-circle"></i> Username tersedia</span>');
                    }
                }
            });
        } else {
            $('#usernameFeedback').html('<span class="text-muted">Minimal 3 karakter</span>');
        }
    }
    
    function checkEmailAvailability(email) {
        if (email.includes('@')) {
            $.ajax({
                url: '<?= base_url('admin/karyawan/akun/check-email/') ?>' + encodeURIComponent(email),
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    var feedback = $('#emailFeedback');
                    if (response.exists) {
                        feedback.html('<span class="text-danger"><i class="fas fa-times-circle"></i> Email sudah digunakan</span>');
                    } else {
                        feedback.html('<span class="text-success"><i class="fas fa-check-circle"></i> Email tersedia</span>');
                    }
                }
            });
        } else {
            $('#emailFeedback').html('<span class="text-muted">Masukkan email yang valid</span>');
        }
    }
    
    function togglePassword(fieldId) {
        var field = document.getElementById(fieldId);
        var type = field.getAttribute('type') === 'password' ? 'text' : 'password';
        field.setAttribute('type', type);
    }
    
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
        
        // Simpan role ke localStorage untuk digunakan nanti
        saveRoleToLocalStorage(customRole);
    }
    
    function saveRoleToLocalStorage(role) {
        var roles = JSON.parse(localStorage.getItem('customRoles') || '[]');
        if (!roles.includes(role)) {
            roles.push(role);
            localStorage.setItem('customRoles', JSON.stringify(roles));
        }
    }
    
    function loadCustomRoles() {
        var roles = JSON.parse(localStorage.getItem('customRoles') || '[]');
        var roleSelect = document.getElementById('roleSelect');
        
        roles.forEach(function(role) {
            // Cek apakah role sudah ada di dropdown
            var exists = false;
            for (var i = 0; i < roleSelect.options.length; i++) {
                if (roleSelect.options[i].value === role) {
                    exists = true;
                    break;
                }
            }
            
            if (!exists) {
                var option = document.createElement('option');
                option.value = role;
                option.textContent = role.charAt(0).toUpperCase() + role.slice(1);
                var customOption = roleSelect.querySelector('option[value="custom"]');
                roleSelect.insertBefore(option, customOption);
            }
        });
    }
    
    // Load custom roles saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        loadCustomRoles();
    });
    
    // Form validation
    $('#createAkunForm').on('submit', function(e) {
        var password = $('#password').val();
        var confirmPassword = $('#password_confirmation').val();
        var roleSelect = document.getElementById('roleSelect');
        var customRole = document.getElementById('customRole').value.trim();
        var selectedRole = roleSelect.value;
        
        // Validasi role
        if (selectedRole === '' || (selectedRole === 'custom' && customRole.length < 2)) {
            e.preventDefault();
            alert('Silakan pilih atau buat role untuk akun ini');
            return false;
        }
        
        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Password dan konfirmasi password tidak cocok!');
            return false;
        }
        
        if (password.length < 6) {
            e.preventDefault();
            alert('Password minimal 6 karakter!');
            return false;
        }
        
        return true;
    });
</script>

<?= $this->include('admin/templates/footer') ?>