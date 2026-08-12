<?= $this->include('direktur/templates/header') ?>
<?= $this->include('direktur/templates/sidebar') ?>
<?= $this->include('direktur/templates/navbar') ?>

<style>
    /* Styling Premium Modern Material & Glassmorphism */
    .employee-card-modern {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        border-radius: 18px !important;
        box-shadow: 0 8px 25px rgba(30, 60, 114, 0.06), 0 2px 6px rgba(0, 0, 0, 0.02) !important;
    }

    .form-section-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e3c72;
        display: flex;
        align-items: center;
        gap: 8px;
        padding-bottom: 8px;
        border-bottom: 2px solid #f1f5f9;
        margin-bottom: 20px;
    }

    .form-control-custom, .form-select-custom {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 10px 14px;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .form-control-custom:focus, .form-select-custom:focus {
        border-color: #1e3c72;
        box-shadow: 0 0 0 3px rgba(30, 60, 114, 0.12);
    }
</style>

<div class="container-fluid py-3 py-md-4">

    <!-- Header Section Terpadu -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-4 border border-light">
        <div class="d-flex align-items-center">
            <div class="bg-gradient-primary text-white rounded-3 p-2.5 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 46px; height: 46px;">
                <i class="fas fa-user-edit fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold text-dark">Edit Akun Karyawan</h4>
                <small class="text-muted d-none d-sm-inline">Perbarui kredensial, role hak akses, atau reset password untuk <strong><?= esc($akun['nama_lengkap'] ?? $akun['name']) ?></strong></small>
            </div>
        </div>
        <a href="<?= base_url('direktur/karyawan/akun') ?>" class="btn btn-outline-secondary rounded-pill px-3.5 py-2 shadow-sm d-inline-flex align-items-center text-sm fw-semibold">
            <i class="fas fa-arrow-left me-1.5"></i> <span class="d-none d-md-inline">Kembali ke Kelola Akun</span><span class="d-inline d-md-none">Kembali</span>
        </a>
    </div>

    <!-- Form Container Card -->
    <div class="row">
        <div class="col-12 col-xl-9 mx-auto">
            <div class="card employee-card-modern p-4 mb-5">
                
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show text-white rounded-3" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger alert-dismissible fade show text-white rounded-3" role="alert">
                        <ul class="mb-0 ps-3">
                            <?php foreach (session()->getFlashdata('errors') as $err): ?>
                                <li><?= esc($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('direktur/karyawan/update-akun/' . $akun['id']) ?>" method="post">
                    <?= csrf_field() ?>

                    <!-- Section 1: Informasi Karyawan (Read Only) -->
                    <div class="form-section-title">
                        <i class="fas fa-id-badge text-primary"></i> Identitas Karyawan (Read Only)
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-xs fw-bold text-uppercase text-muted mb-1">Nama Lengkap Karyawan</label>
                            <input type="text" class="form-control form-control-custom bg-light fw-bold text-dark" value="<?= esc($akun['nama_lengkap'] ?? $akun['name']) ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-xs fw-bold text-uppercase text-muted mb-1">NIK Karyawan</label>
                            <input type="text" class="form-control form-control-custom bg-light fw-bold text-dark" value="<?= esc($akun['nik'] ?? '-') ?>" readonly>
                        </div>
                    </div>

                    <!-- Section 2: Kredensial Login & Akses System -->
                    <div class="form-section-title">
                        <i class="fas fa-user-lock text-primary"></i> Kredensial & Hak Akses System
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-sm fw-semibold text-dark">Username Login <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control form-control-custom" value="<?= old('username', esc($akun['username'])) ?>" required placeholder="Masukkan username">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-sm fw-semibold text-dark">Email Akun <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control form-control-custom" value="<?= old('email', esc($akun['email'])) ?>" required placeholder="contoh@cdw.co.id">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-sm fw-semibold text-dark">Role Hak Akses <span class="text-danger">*</span></label>
                            <select name="role" class="form-select form-select-custom" required>
                                <option value="employee" <?= old('role', $akun['role']) == 'employee' || old('role', $akun['role']) == 'staff' ? 'selected' : '' ?>>Employee / Staff</option>
                                <option value="hrd" <?= old('role', $akun['role']) == 'hrd' ? 'selected' : '' ?>>HRD</option>
                                <option value="admin" <?= old('role', $akun['role']) == 'admin' ? 'selected' : '' ?>>Admin</option>
                                <option value="accounting" <?= old('role', $akun['role']) == 'accounting' ? 'selected' : '' ?>>Accounting / Keuangan</option>
                                <option value="teknisi" <?= old('role', $akun['role']) == 'teknisi' ? 'selected' : '' ?>>Teknisi</option>
                                <option value="sales" <?= old('role', $akun['role']) == 'sales' ? 'selected' : '' ?>>Sales / Marketing</option>
                                <option value="direktur" <?= old('role', $akun['role']) == 'direktur' ? 'selected' : '' ?>>Direktur</option>
                                <option value="software_engineer" <?= old('role', $akun['role']) == 'software_engineer' ? 'selected' : '' ?>>Software Engineer</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-sm fw-semibold text-dark">Status Akun <span class="text-danger">*</span></label>
                            <select name="status" class="form-select form-select-custom" required>
                                <option value="active" <?= old('status', $akun['status']) == 'active' ? 'selected' : '' ?>>Active (Aktif Bisa Login)</option>
                                <option value="inactive" <?= old('status', $akun['status']) == 'inactive' ? 'selected' : '' ?>>Inactive (Suspend / Non-Aktif)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Section 3: Reset Password (Opsional) -->
                    <div class="form-section-title mt-4">
                        <i class="fas fa-key text-primary"></i> Reset Password (Opsional)
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="form-label text-sm fw-semibold text-dark">Password Baru</label>
                            <input type="text" name="password" class="form-control form-control-custom" placeholder="Kosongkan jika tidak ingin mengubah password lama">
                            <div class="form-text text-xs text-muted mt-1">
                                <i class="fas fa-info-circle me-1 text-primary"></i> Isi kolom ini HANYA jika Anda ingin mereset password lama karyawan menjadi password baru.
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="<?= base_url('direktur/karyawan/akun') ?>" class="btn btn-light rounded-pill px-4 fw-semibold border">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm">
                            <i class="fas fa-save me-1.5"></i> Simpan Perubahan Akun
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

</div>

<?= $this->include('direktur/templates/footer') ?>
