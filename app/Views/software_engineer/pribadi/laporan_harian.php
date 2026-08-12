<?= view('software_engineer/templates/header') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1"><i class="fas fa-file-invoice text-success me-2"></i> Laporan Kerja Harian Saya</h5>
        <small class="text-muted">Histori laporan kerja harian pribadi Software Engineer</small>
    </div>
</div>

<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Ringkasan Kerja</th>
                        <th>Detail Pekerjaan</th>
                        <th>Kendala</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($laporan)): ?>
                        <tr><td colspan="4" class="text-center text-muted py-4">Belum ada riwayat laporan kerja harian.</td></tr>
                    <?php else: ?>
                        <?php foreach ($laporan as $l): ?>
                            <tr>
                                <td class="code-font fw-bold text-dark"><?= date('d M Y', strtotime($l['tanggal'])) ?></td>
                                <td class="fw-bold text-primary"><?= esc($l['ringkasan_kerja']) ?></td>
                                <td><small class="text-dark"><?= esc($l['detail_pekerjaan']) ?></small></td>
                                <td><small class="text-danger"><?= esc($l['kendala'] ?: '-') ?></small></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= view('software_engineer/templates/footer') ?>
