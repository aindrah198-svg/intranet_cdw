<?= view('software_engineer/templates/header') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1"><i class="fas fa-key text-warning me-2"></i> Kredensial Akses Sensitif (Audit Trail Protected)</h5>
        <small class="text-muted">Penyimpanan kredensial terenkripsi aman. Setiap aksi buka/urai password wajib melalui Audit Trail Log</small>
    </div>
    <div>
        <a href="<?= site_url('software-engineer/manajemen-sistem/kredensial-akses/audit-log') ?>" class="btn btn-outline-dark btn-sm me-2">
            <i class="fas fa-history me-1"></i> Audit Log Security
        </a>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahKredensial">
            <i class="fas fa-plus me-1"></i> Tambah Kredensial
        </button>
    </div>
</div>

<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Sistem</th>
                        <th>Tipe Akses</th>
                        <th>Username / Email</th>
                        <th>Password (Encrypted)</th>
                        <th>Admin PIC</th>
                        <th>Ganti Password Terakhir</th>
                        <th>Aksi Security</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($credentials)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada kredensial tersimpan.</td></tr>
                    <?php else: ?>
                        <?php foreach ($credentials as $c): ?>
                            <tr>
                                <td class="fw-bold text-primary"><?= esc($c['nama_sistem']) ?></td>
                                <td><span class="badge bg-secondary"><?= esc($c['tipe_akses']) ?></span></td>
                                <td class="code-font text-dark fw-semibold"><?= esc($c['username_akses']) ?></td>
                                <td>
                                    <span id="password_text_<?= $c['id'] ?>" class="text-muted code-font" style="letter-spacing: 2px;">••••••••••••</span>
                                </td>
                                <td><small class="text-muted"><i class="fas fa-user-shield me-1"></i> <?= esc($c['admin_pic']) ?></small></td>
                                <td><small class="text-muted"><?= $c['tgl_terakhir_ganti_password'] ? date('d M Y', strtotime($c['tgl_terakhir_ganti_password'])) : '-' ?></small></td>
                                <td>
                                    <button type="button" id="btn_reveal_<?= $c['id'] ?>" onclick="revealCredential(<?= $c['id'] ?>)" class="btn btn-sm btn-outline-warning py-0 px-2 me-1">
                                        <i class="fas fa-eye me-1"></i> Reveal (Log)
                                    </button>
                                    <form action="<?= site_url('software-engineer/manajemen-sistem/kredensial-akses/delete/' . $c['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus kredensial ini?');">
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Kredensial -->
<div class="modal fade" id="modalTambahKredensial" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= site_url('software-engineer/manajemen-sistem/kredensial-akses/store') ?>" method="POST" class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold"><i class="fas fa-user-lock me-1"></i> Tambah Kredensial Sensitif</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Sistem Target</label>
                    <select name="system_id" class="form-select" required>
                        <?php foreach ($systems as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= esc($s['nama_sistem']) ?> (<?= esc($s['kode_sistem']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Tipe Akses</label>
                        <select name="tipe_akses" class="form-select" required>
                            <option value="cPanel / Hosting Provider">cPanel / Hosting Provider</option>
                            <option value="Database Server (MySQL/PostgreSQL)">Database Server</option>
                            <option value="VPS / SSH Root">VPS / SSH Root</option>
                            <option value="Super Admin Web">Super Admin Web</option>
                            <option value="API Secret Key / Token">API Secret Key / Token</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Admin PIC</label>
                        <input type="text" name="admin_pic" class="form-control" placeholder="Nama Penanggung Jawab">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Username Akses</label>
                        <input type="text" name="username_akses" class="form-control code-font" required placeholder="root / admin">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Password Asli (Akan Di-Enkripsi)</label>
                        <input type="password" name="password_akses" class="form-control code-font" required placeholder="••••••••">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">URL Login Server/CPanel</label>
                    <input type="url" name="url_login" class="form-control" placeholder="https://cpanel.domain.com:2083">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tanggal Terakhir Ganti Password</label>
                    <input type="date" name="tgl_terakhir_ganti_password" class="form-control" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Catatan Keamanan</label>
                    <textarea name="catatan_keamanan" class="form-control" rows="2" placeholder="Catatan 2FA atau IP restriction..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-warning text-dark fw-bold">Enkripsi & Simpan</button>
            </div>
        </form>
    </div>
</div>

<?= view('software_engineer/templates/footer') ?>
