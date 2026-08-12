<div class="content-wrapper p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-id-card mr-2"></i>Profil Saya</h4>
            <p class="text-muted mb-0">Informasi akun pengguna dan data karyawan sales</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm text-center p-4">
                <div class="mb-3">
                    <div class="avatar-circle mx-auto bg-warning text-dark d-flex align-items-center justify-content-center rounded-circle font-weight-bold" style="width: 90px; height: 90px; font-size: 36px;">
                        <?= strtoupper(substr($user['username'] ?? 'S', 0, 1)) ?>
                    </div>
                </div>
                <h5 class="font-weight-bold mb-1"><?= esc($user['username'] ?? 'Sales Staff') ?></h5>
                <p class="text-muted small mb-2"><?= esc($user['email'] ?? '-') ?></p>
                <span class="badge badge-warning text-dark px-3 py-2 align-self-center">Sales & Marketing</span>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user mr-2"></i>Detail Profil Staff</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless align-middle mb-0">
                        <tr>
                            <td width="200" class="font-weight-bold text-muted">Username</td>
                            <td>: <?= esc($user['username'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-muted">Email</td>
                            <td>: <?= esc($user['email'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-muted">Role System</td>
                            <td>: <span class="badge badge-info"><?= esc($user['role'] ?? 'sales') ?></span></td>
                        </tr>
                        <?php if (!empty($karyawan)): ?>
                            <tr>
                                <td class="font-weight-bold text-muted">NIK Karyawan</td>
                                <td>: <?= esc($karyawan['nik'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-muted">Nama Lengkap</td>
                                <td>: <?= esc($karyawan['nama_lengkap'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-muted">Jabatan</td>
                                <td>: <?= esc($karyawan['jabatan'] ?? 'Sales Executive') ?></td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td class="font-weight-bold text-muted">Status Akun</td>
                            <td>: <span class="badge badge-success">Aktif</span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
