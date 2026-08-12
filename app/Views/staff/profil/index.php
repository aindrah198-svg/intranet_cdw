<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="fas fa-user-edit text-primary me-2"></i> Profil Saya</h4>
                <p class="text-muted mb-0">Informasi data diri dan kontak pribadi karyawan</p>
            </div>
            <a href="<?= base_url('staff/dokumen') ?>" class="btn btn-outline-info btn-sm"><i class="fas fa-id-card me-1"></i> Dokumen Saya</a>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show"><?= session()->getFlashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <div class="row g-4 justify-content-center">
            <div class="col-md-4">
                <div class="card card-custom p-4 text-center">
                    <div class="sidebar-user-avatar mx-auto mb-3" style="width: 100px; height: 100px; font-size: 2.5rem; background: #2563eb; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <?= strtoupper(substr($detail['nama_lengkap'] ?? $user['name'] ?? 'S', 0, 1)) ?>
                    </div>
                    <h5 class="fw-bold mb-1"><?= esc($detail['nama_lengkap'] ?? $user['name']) ?></h5>
                    <p class="badge bg-primary px-3 py-2 rounded-pill mb-2"><?= esc($detail['jabatan'] ?? 'Staff') ?></p>
                    <p class="text-muted small mb-0">Divisi: <strong><?= esc($detail['divisi'] ?? 'General') ?></strong></p>
                    <p class="text-muted small">NIK: <strong><?= esc($detail['nik'] ?? '-') ?></strong></p>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card card-custom p-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Informasi Pribadi & Kontak</h5>
                    <form action="<?= base_url('staff/profil/update') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label font-semibold">Nomor Telepon / WhatsApp <span class="text-danger">*</span></label>
                                <input type="text" name="no_telepon" class="form-control" value="<?= esc($detail['no_telepon'] ?? '') ?>" placeholder="08xxxxxxxxxx" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-semibold">Email Karyawan (Read-only)</label>
                                <input type="email" class="form-control bg-light" value="<?= esc($detail['email'] ?? '') ?>" readonly>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-semibold">Alamat Tempat Tinggal <span class="text-danger">*</span></label>
                            <textarea name="alamat" class="form-control" rows="3" required><?= esc($detail['alamat'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label font-semibold">Kontak Darurat (Nama & No HP)</label>
                            <input type="text" name="kontak_darurat" class="form-control" value="<?= esc($detail['kontak_darurat'] ?? '') ?>" placeholder="Contoh: Budi (Ayah) - 08123456789">
                        </div>

                        <div class="d-flex justify-content-end border-top pt-3">
                            <button type="submit" class="btn btn-primary px-4 fw-bold"><i class="fas fa-save me-1"></i> Simpan Perubahan Profil</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
