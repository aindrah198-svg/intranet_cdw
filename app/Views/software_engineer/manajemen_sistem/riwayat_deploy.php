<?= view('software_engineer/templates/header') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1"><i class="fas fa-history text-info me-2"></i> Riwayat Deployment Production</h5>
        <small class="text-muted">Log setiap rilis/deploy ke production: versi, tanggal, changelog, & status</small>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahDeploy">
        <i class="fas fa-plus me-1"></i> Catat Deployment Baru
    </button>
</div>

<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Waktu Deploy</th>
                        <th>Sistem</th>
                        <th>Versi / Tag</th>
                        <th>Changelog / Perubahan</th>
                        <th>Deployed By</th>
                        <th>Environment</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($deployments)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada riwayat deployment dicatat.</td></tr>
                    <?php else: ?>
                        <?php foreach ($deployments as $d): ?>
                            <tr>
                                <td class="code-font small"><?= date('d M Y H:i', strtotime($d['tanggal_deploy'])) ?></td>
                                <td class="fw-bold text-primary"><?= esc($d['nama_sistem']) ?></td>
                                <td><span class="badge bg-cyan text-dark code-font"><?= esc($d['versi']) ?></span></td>
                                <td><small class="text-dark"><?= esc($d['perubahan']) ?></small></td>
                                <td><small class="text-muted"><i class="fas fa-user me-1"></i> <?= esc($d['deployed_by']) ?></small></td>
                                <td><span class="badge bg-secondary"><?= esc(ucfirst($d['environment'])) ?></span></td>
                                <td>
                                    <?php if ($d['status_deploy'] === 'sukses'): ?>
                                        <span class="badge bg-success">Sukses</span>
                                    <?php elseif ($d['status_deploy'] === 'rollback'): ?>
                                        <span class="badge bg-warning text-dark">Rollback</span>
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

<!-- Modal Tambah Deploy -->
<div class="modal fade" id="modalTambahDeploy" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= site_url('software-engineer/manajemen-sistem/riwayat-deploy/store') ?>" method="POST" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-rocket me-1"></i> Catat Deployment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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
                        <label class="form-label fw-semibold">Versi / Tag Release</label>
                        <input type="text" name="versi" class="form-control code-font" required placeholder="v1.2.0 atau commit hash">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Environment</label>
                        <select name="environment" class="form-select">
                            <option value="production">Production</option>
                            <option value="staging">Staging</option>
                            <option value="testing">Testing</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Rincian Perubahan (Changelog)</label>
                    <textarea name="perubahan" class="form-control" rows="3" required placeholder="Penambahan fitur X, perbaikan bug Y..."></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Waktu Deploy</label>
                        <input type="datetime-local" name="tanggal_deploy" class="form-control" value="<?= date('Y-m-d\TH:i') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Status Deploy</label>
                        <select name="status_deploy" class="form-select">
                            <option value="sukses">Sukses</option>
                            <option value="gagal">Gagal</option>
                            <option value="rollback">Rollback</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Deploy Log</button>
            </div>
        </form>
    </div>
</div>

<?= view('software_engineer/templates/footer') ?>
