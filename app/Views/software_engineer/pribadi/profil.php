<?= view('software_engineer/templates/header') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1"><i class="fas fa-user-cog text-primary me-2"></i> Profil Akun Software Engineer</h5>
        <small class="text-muted">Kelola data profil, email, dan ubah kata sandi akun</small>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card card-custom text-center p-4">
            <div class="rounded-circle bg-info text-dark font-weight-bold d-inline-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" style="width: 80px; height: 80px; font-size: 2.2rem; background-color: #38bdf8 !important;">
                <?= strtoupper(substr($user['username'] ?? 'S', 0, 1)) ?>
            </div>
            <h5 class="fw-bold mb-1 text-dark"><?= esc($user['name'] ?? $user['username']) ?></h5>
            <span class="badge bg-cyan text-dark mb-3" style="background-color: #38bdf8;">Software Engineer</span>
            
            <div class="border-top pt-3 text-start small">
                <div class="mb-2"><strong class="text-secondary">Username:</strong> <span class="code-font text-dark float-end"><?= esc($user['username']) ?></span></div>
                <div class="mb-2"><strong class="text-secondary">Role System:</strong> <span class="code-font text-dark float-end"><?= esc($user['role']) ?></span></div>
                <div class="mb-2"><strong class="text-secondary">Email:</strong> <span class="code-font text-dark float-end"><?= esc($user['email']) ?></span></div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card card-custom">
            <div class="card-header bg-transparent border-bottom fw-bold">
                <i class="fas fa-user-edit me-2 text-primary"></i> Edit Detail Profil & Kata Sandi
            </div>
            <div class="card-body">
                <form action="<?= site_url('software-engineer/pribadi/profil/update') ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="<?= esc($user['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat Email</label>
                        <input type="email" name="email" class="form-control code-font" value="<?= esc($user['email']) ?>" required>
                    </div>
                    <hr class="my-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-danger"><i class="fas fa-lock me-1"></i> Ubah Kata Sandi (Kosongkan jika tidak diubah)</label>
                        <input type="password" name="password" class="form-control code-font" placeholder="••••••••">
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan Perubahan Profil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= view('software_engineer/templates/footer') ?>
