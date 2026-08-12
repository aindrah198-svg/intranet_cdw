<?php
$data = [
    'title'  => 'Profil Saya & Pengaturan Akun',
    'active' => 'profil',
    'user'   => ['name' => $userData['name'] ?? session()->get('name') ?? 'Admin', 'role' => 'admin']
];

echo view('admin/templates/header', $data);
echo view('admin/templates/sidebar', $data);
echo view('admin/templates/navbar', $data);

$name     = esc($userData['name'] ?? session()->get('name') ?? 'Admin Systems');
$username = esc($userData['username'] ?? session()->get('username') ?? 'admin');
$email    = esc($userData['email'] ?? session()->get('email') ?? 'admin@cdw.co.id');
$noHp     = esc($userData['no_hp'] ?? '081234567890');
$role     = strtoupper(esc($userData['role'] ?? session()->get('role') ?? 'admin'));
$initial  = strtoupper(substr($name, 0, 1));
$joinDate = !empty($userData['created_at']) ? date('d F Y', strtotime($userData['created_at'])) : date('d F Y');
?>

<style>
    /* Styling Premium Modern Profile Card & Glassmorphism */
    .profile-banner-hero {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #4a148c 100%);
        height: 180px;
        border-radius: 24px 24px 0 0;
        position: relative;
        overflow: hidden;
    }
    
    .profile-banner-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        filter: blur(40px);
    }

    .profile-avatar-wrapper {
        position: relative;
        margin-top: -65px;
        margin-left: 32px;
        display: inline-block;
    }

    .profile-avatar-circle {
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        border: 4px solid #ffffff;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        font-weight: 800;
        color: #ffffff;
    }

    .online-indicator {
        position: absolute;
        bottom: 8px;
        right: 8px;
        width: 22px;
        height: 22px;
        background-color: #198754;
        border: 3px solid #ffffff;
        border-radius: 50%;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }

    .custom-input-group {
        position: relative;
    }

    .custom-input-group .input-group-text {
        background: #f8fafc;
        border-color: #e2e8f0;
        color: #64748b;
        border-radius: 12px 0 0 12px;
    }

    .custom-input-group .form-control {
        border-color: #e2e8f0;
        border-radius: 0 12px 12px 0;
        padding: 0.75rem 1rem;
        font-size: 0.92rem;
    }

    .custom-input-group .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
    }

    .nav-tabs-custom .nav-link {
        color: #64748b;
        font-weight: 600;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 12px 20px;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .nav-tabs-custom .nav-link.active {
        color: #0d6efd;
        border-bottom-color: #0d6efd;
        background: transparent;
    }

    .password-strength-bar {
        height: 6px;
        border-radius: 10px;
        transition: all 0.3s ease;
    }
</style>

<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-1 text-xs">
            <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>" class="text-decoration-none text-muted">Menu Utama</a></li>
            <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Profil Saya</li>
        </ol>
    </nav>

    <!-- Main Profile Card Header -->
    <div class="card border-0 rounded-4 shadow-lg overflow-hidden mb-4">
        <div class="profile-banner-hero p-4 d-flex justify-content-end align-items-start">
            <span class="badge bg-white bg-opacity-20 text-white backdrop-blur px-3 py-1.5 rounded-pill font-semibold text-xs border border-white border-opacity-25">
                <i class="fas fa-shield-alt me-1.5 text-warning"></i> Sesi Login Aman
            </span>
        </div>

        <div class="card-body p-4 pt-0">
            <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
                <div class="d-flex align-items-end flex-wrap gap-3">
                    <div class="profile-avatar-wrapper">
                        <div class="profile-avatar-circle">
                            <?= $initial ?>
                        </div>
                        <div class="online-indicator" title="Akun Aktif Online"></div>
                    </div>
                    <div class="mb-2">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h3 class="fw-bold text-dark mb-0 fs-4"><?= $name ?></h3>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-1 rounded-pill text-xs fw-bold">
                                SYSTEM ADMINISTRATOR
                            </span>
                        </div>
                        <p class="text-muted text-xs mb-0 mt-1">
                            <i class="fas fa-user-circle text-primary me-1"></i> Username: <strong><?= $username ?></strong> &bull; 
                            <i class="fas fa-envelope text-secondary me-1"></i> Email: <strong><?= $email ?></strong>
                        </p>
                    </div>
                </div>

                <div class="d-flex gap-2 mb-2">
                    <div class="px-3 py-2 bg-light rounded-3 text-center border">
                        <small class="text-muted text-xs d-block uppercase font-semibold">Status Akun</small>
                        <span class="badge bg-success bg-opacity-10 text-success font-semibold text-xs">Aktif</span>
                    </div>
                    <div class="px-3 py-2 bg-light rounded-3 text-center border">
                        <small class="text-muted text-xs d-block uppercase font-semibold">Terdaftar Sejak</small>
                        <span class="fw-bold text-dark text-xs"><?= $joinDate ?></span>
                    </div>
                </div>
            </div>

            <!-- Custom Tabs Navigation -->
            <ul class="nav nav-tabs nav-tabs-custom border-bottom" id="profileTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-info-tab" data-bs-toggle="tab" data-bs-target="#tab-info" type="button" role="tab">
                        <i class="fas fa-user-edit me-2"></i> Informasi Akun & Kontak
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-security-tab" data-bs-toggle="tab" data-bs-target="#tab-security" type="button" role="tab">
                        <i class="fas fa-key me-2"></i> Keamanan & Password
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-privilege-tab" data-bs-toggle="tab" data-bs-target="#tab-privilege" type="button" role="tab">
                        <i class="fas fa-user-shield me-2"></i> Hak Akses System
                    </button>
                </li>
            </ul>

            <!-- Tab Contents -->
            <div class="tab-content pt-4" id="profileTabContent">
                
                <!-- TAB 1: EDIT INFORMASI AKUN -->
                <div class="tab-pane fade show active" id="tab-info" role="tabpanel">
                    <form action="<?= base_url('admin/profil/update') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark text-sm">Nama Lengkap <span class="text-danger">*</span></label>
                                <div class="input-group custom-input-group">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                    <input type="text" name="name" class="form-control" value="<?= $name ?>" required placeholder="Masukkan Nama Lengkap Anda">
                                </div>
                                <small class="text-muted text-xs">Nama ini akan ditampilkan pada laporan kerja harian dan riwayat aktivitas.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark text-sm">Username Sistem (Readonly)</label>
                                <div class="input-group custom-input-group">
                                    <span class="input-group-text"><i class="fas fa-at"></i></span>
                                    <input type="text" class="form-control bg-light" value="<?= $username ?>" readonly>
                                </div>
                                <small class="text-muted text-xs">Username digunakan untuk login ke sistem intranet.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark text-sm">Alamat Email <span class="text-danger">*</span></label>
                                <div class="input-group custom-input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" class="form-control" value="<?= $email ?>" required placeholder="contoh@cdw.co.id">
                                </div>
                                <small class="text-muted text-xs">Email resmi perusahaan untuk notifikasi dan pemulihan akun.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark text-sm">No. WhatsApp / HP</label>
                                <div class="input-group custom-input-group">
                                    <span class="input-group-text"><i class="fab fa-whatsapp text-success"></i></span>
                                    <input type="text" name="no_hp" class="form-control" value="<?= $noHp ?>" placeholder="Contoh: 081234567890">
                                </div>
                                <small class="text-muted text-xs">Nomor kontak aktif untuk komunikasi internal tim.</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm font-semibold text-sm">
                                <i class="fas fa-save me-1.5"></i> Simpan Perubahan Profil
                            </button>
                        </div>
                    </form>
                </div>

                <!-- TAB 2: KEAMANAN & PASSWORD -->
                <div class="tab-pane fade" id="tab-security" role="tabpanel">
                    <form action="<?= base_url('admin/profil/update') ?>" method="POST" id="formChangePassword">
                        <?= csrf_field() ?>
                        <!-- Pass current name & email so they aren't overwritten -->
                        <input type="hidden" name="name" value="<?= $name ?>">
                        <input type="hidden" name="email" value="<?= $email ?>">
                        <input type="hidden" name="no_hp" value="<?= $noHp ?>">

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark text-sm">Password Baru</label>
                                <div class="input-group custom-input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password" id="inputNewPassword" class="form-control" placeholder="Biarkan kosong jika tidak ingin mengubah password" onkeyup="checkPasswordStrength(this.value)">
                                    <button type="button" class="btn btn-outline-secondary border-start-0" onclick="togglePasswordVisibility('inputNewPassword', this)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                
                                <!-- Password Strength Indicator -->
                                <div class="mt-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1 text-xs">
                                        <span class="text-muted">Kekuatan Password:</span>
                                        <span id="strengthText" class="fw-bold text-secondary">Belum diisi</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div id="strengthBar" class="progress-bar bg-secondary" style="width: 0%;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark text-sm">Konfirmasi Password Baru</label>
                                <div class="input-group custom-input-group">
                                    <span class="input-group-text"><i class="fas fa-check-double"></i></span>
                                    <input type="password" id="inputConfirmPassword" class="form-control" placeholder="Ketik ulang password baru">
                                    <button type="button" class="btn btn-outline-secondary border-start-0" onclick="togglePasswordVisibility('inputConfirmPassword', this)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <small id="matchMessage" class="text-xs d-block mt-1 text-muted">Pastikan password baru dan konfirmasi bernilai sama.</small>
                            </div>
                        </div>

                        <div class="alert alert-warning border-0 rounded-3 p-3 text-xs mb-4 d-flex align-items-center gap-2">
                            <i class="fas fa-exclamation-triangle text-warning fs-5"></i>
                            <div>
                                <strong>Tips Keamanan:</strong> Gunakan kombinasi minimal 8 karakter dengan campuran huruf besar, huruf kecil, dan angka untuk menjaga keamanan akun Administrator Anda.
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-warning text-dark rounded-pill px-4 shadow-sm font-semibold text-sm">
                                <i class="fas fa-key me-1.5"></i> Perbarui Password Akun
                            </button>
                        </div>
                    </form>
                </div>

                <!-- TAB 3: HAK AKSES SISTEM -->
                <div class="tab-pane fade" id="tab-privilege" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <div class="bg-primary bg-opacity-10 text-primary p-2.5 rounded-3">
                                        <i class="fas fa-users-cog fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0 text-sm">Kelola Karyawan & SDM</h6>
                                        <small class="text-success font-semibold text-xs"><i class="fas fa-check-circle me-1"></i> Full Access</small>
                                    </div>
                                </div>
                                <small class="text-muted text-xs d-block">Dapat mengelola data karyawan, pembuatan akun, dokumen kontrak SP, dan slip gaji.</small>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <div class="bg-info bg-opacity-10 text-info p-2.5 rounded-3">
                                        <i class="fas fa-tasks fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0 text-sm">Penugasan & Proyek</h6>
                                        <small class="text-success font-semibold text-xs"><i class="fas fa-check-circle me-1"></i> Full Access</small>
                                    </div>
                                </div>
                                <small class="text-muted text-xs d-block">Dapat menerima dan memproses penugasan harian dari Direktur serta memonitor timeline kerja proyek.</small>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <div class="bg-success bg-opacity-10 text-success p-2.5 rounded-3">
                                        <i class="fas fa-user-clock fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0 text-sm">Monitoring Absensi</h6>
                                        <small class="text-success font-semibold text-xs"><i class="fas fa-check-circle me-1"></i> Full Access</small>
                                    </div>
                                </div>
                                <small class="text-muted text-xs d-block">Terhubung langsung dengan sistem absensi mandiri dan pemantauan jam kerja karyawan.</small>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function checkPasswordStrength(val) {
    const bar = document.getElementById('strengthBar');
    const text = document.getElementById('strengthText');
    if (!val) {
        bar.style.width = '0%';
        bar.className = 'progress-bar bg-secondary';
        text.innerText = 'Belum diisi';
        return;
    }

    let score = 0;
    if (val.length >= 6) score += 25;
    if (val.length >= 8) score += 25;
    if (/[A-Z]/.test(val)) score += 25;
    if (/[0-9]/.test(val) || /[^A-Za-z0-9]/.test(val)) score += 25;

    bar.style.width = score + '%';
    if (score <= 25) {
        bar.className = 'progress-bar bg-danger';
        text.innerText = 'Sangat Lemah';
        text.className = 'fw-bold text-danger';
    } else if (score <= 50) {
        bar.className = 'progress-bar bg-warning';
        text.innerText = 'Cukup';
        text.className = 'fw-bold text-warning';
    } else if (score <= 75) {
        bar.className = 'progress-bar bg-info';
        text.innerText = 'Kuat';
        text.className = 'fw-bold text-info';
    } else {
        bar.className = 'progress-bar bg-success';
        text.innerText = 'Sangat Kuat';
        text.className = 'fw-bold text-success';
    }
}

document.getElementById('formChangePassword').addEventListener('submit', function(e) {
    const pass = document.getElementById('inputNewPassword').value;
    const confirm = document.getElementById('inputConfirmPassword').value;

    if (pass !== '' && pass !== confirm) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Password Tidak Cocok!',
            text: 'Konfirmasi password baru yang Anda masukkan tidak sesuai dengan password baru.',
            confirmButtonColor: '#dc3545',
            customClass: { popup: 'rounded-4 shadow-lg' }
        });
    }
});
</script>

<?php if (session()->getFlashdata('success')): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '<?= esc(session()->getFlashdata('success')) ?>',
        timer: 3500,
        showConfirmButton: false,
        customClass: { popup: 'rounded-4 shadow-lg' }
    });
});
</script>
<?php endif; ?>

<?= view('admin/templates/footer', $data) ?>
