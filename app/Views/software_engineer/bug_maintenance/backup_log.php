<?= view('software_engineer/templates/header') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1"><i class="fas fa-database text-success me-2"></i> Log Backup Database & File Sistem</h5>
        <small class="text-muted">Pencatatan krusial riwayat backup berkala (tanggal, lokasi simpan, ukuran, & verifikasi)</small>
    </div>
    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahBackup">
        <i class="fas fa-plus me-1"></i> Catat Log Backup
    </button>
</div>

<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal Backup</th>
                        <th>Sistem</th>
                        <th>Jenis Backup</th>
                        <th>Ukuran (MB)</th>
                        <th>Lokasi Penyimpanan</th>
                        <th>Petugas</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($backups)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada catatan log backup sistem.</td></tr>
                    <?php else: ?>
                        <?php foreach ($backups as $b): ?>
                            <tr>
                                <td class="code-font fw-bold text-dark"><?= date('d M Y H:i', strtotime($b['tanggal_backup'])) ?></td>
                                <td class="fw-bold text-primary"><?= esc($b['nama_sistem']) ?></td>
                                <td><span class="badge bg-secondary"><?= esc(strtoupper($b['jenis_backup'])) ?></span></td>
                                <td><small class="code-font"><?= number_format($b['ukuran_mb'], 2) ?> MB</small></td>
                                <td><small class="code-font text-dark"><i class="fas fa-cloud me-1 text-info"></i> <?= esc($b['lokasi_simpan']) ?></small></td>
                                <td><small class="text-muted"><i class="fas fa-user me-1"></i> <?= esc($b['petugas']) ?></small></td>
                                <td>
                                    <?php if ($b['status_backup'] === 'sukses'): ?>
                                        <span class="badge bg-success">Sukses</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Gagal</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Backup -->
<div class="modal fade" id="modalTambahBackup" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= site_url('software-engineer/bug-maintenance/backup-log/store') ?>" method="POST" class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-database me-1"></i> Catat Backup Log</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Sistem Target</label>
                    <select name="system_id" class="form-select" required>
                        <?php foreach ($systems as $sys): ?>
                            <option value="<?= $sys['id'] ?>"><?= esc($sys['nama_sistem']) ?> (<?= esc($sys['kode_sistem']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Jenis Backup</label>
                        <select name="jenis_backup" class="form-select">
                            <option value="database">Database MySQL/PgSQL</option>
                            <option value="files">Source Code & Files</option>
                            <option value="full_system">Full System Snapshot</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Ukuran File (MB)</label>
                        <input type="number" step="0.01" name="ukuran_mb" class="form-control code-font" placeholder="25.50">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Lokasi Simpan Backup</label>
                    <input type="text" name="lokasi_simpan" class="form-control code-font" required placeholder="Google Drive CDW / AWS S3 / Server Backup NAS">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Waktu Backup</label>
                        <input type="datetime-local" name="tanggal_backup" class="form-control" value="<?= date('Y-m-d\TH:i') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Status Backup</label>
                        <select name="status_backup" class="form-select">
                            <option value="sukses">Sukses / Verifikasi OK</option>
                            <option value="gagal">Gagal</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Catatan</label>
                    <textarea name="catatan" class="form-control" rows="2" placeholder="Detail checksum atau nama file backup..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success">Simpan Backup Log</button>
            </div>
        </form>
    </div>
</div>

<?= view('software_engineer/templates/footer') ?>
