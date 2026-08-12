<?= view('software_engineer/templates/header') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1"><i class="fas fa-comment-dots text-danger me-2"></i> Keluhan Saya</h5>
        <small class="text-muted">Riwayat keluhan kerja pribadi yang telah Anda sampaikan</small>
    </div>
</div>

<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Judul Keluhan</th>
                        <th>Kategori</th>
                        <th>Detail Keluhan</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($keluhan)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada keluhan pribadi dikirim.</td></tr>
                    <?php else: ?>
                        <?php foreach ($keluhan as $k): ?>
                            <tr>
                                <td class="fw-bold text-dark"><?= esc($k['judul_keluhan']) ?></td>
                                <td><span class="badge bg-secondary"><?= esc($k['kategori']) ?></span></td>
                                <td><small class="text-dark"><?= esc($k['detail_keluhan']) ?></small></td>
                                <td><small class="code-font"><?= date('d M Y', strtotime($k['created_at'])) ?></small></td>
                                <td><span class="badge bg-warning text-dark"><?= esc($k['status']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= view('software_engineer/templates/footer') ?>
