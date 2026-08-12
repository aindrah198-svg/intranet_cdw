<?= view('software_engineer/templates/header') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1"><i class="fas fa-history text-danger me-2"></i> Audit Trail Security Log (Akses Kredensial)</h5>
        <small class="text-muted">Catatan jejak digital lengkap dari siapapun yang mengakses atau merilis password sensitif</small>
    </div>
    <a href="<?= site_url('software-engineer/manajemen-sistem/kredensial-akses') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Kredensial
    </a>
</div>

<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Waktu Akses</th>
                        <th>User (Username)</th>
                        <th>Sistem</th>
                        <th>Tipe Akses</th>
                        <th>Action</th>
                        <th>IP Address</th>
                        <th>User Agent</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($audit_logs)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada riwayat audit log.</td></tr>
                    <?php else: ?>
                        <?php foreach ($audit_logs as $log): ?>
                            <tr>
                                <td class="code-font small text-dark fw-bold"><?= date('Y-m-d H:i:s', strtotime($log['created_at'])) ?></td>
                                <td>
                                    <span class="badge bg-primary"><i class="fas fa-user me-1"></i> <?= esc($log['username']) ?></span>
                                </td>
                                <td class="fw-bold text-dark"><?= esc($log['nama_sistem'] ?: '-') ?></td>
                                <td><small class="text-muted"><?= esc($log['tipe_akses'] ?: '-') ?> (<?= esc($log['username_akses'] ?: '-') ?>)</small></td>
                                <td>
                                    <span class="badge bg-danger text-white code-font"><i class="fas fa-eye me-1"></i> <?= esc($log['action']) ?></span>
                                </td>
                                <td><small class="code-font text-secondary"><?= esc($log['ip_address']) ?></small></td>
                                <td><small class="text-muted text-truncate d-inline-block" style="max-width: 200px;"><?= esc($log['user_agent']) ?></small></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= view('software_engineer/templates/footer') ?>
