<?= view('software_engineer/templates/header') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1"><i class="fas fa-clock text-info me-2"></i> Absensi Saya</h5>
        <small class="text-muted">Riwayat presensi kehadiran harian Software Engineer</small>
    </div>
</div>

<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Jam Masuk</th>
                        <th>Jam Keluar</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($absensi)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada catatan absensi.</td></tr>
                    <?php else: ?>
                        <?php foreach ($absensi as $a): ?>
                            <tr>
                                <td class="code-font fw-bold text-dark"><?= date('d M Y', strtotime($a['tanggal'])) ?></td>
                                <td class="code-font text-success"><?= esc($a['jam_masuk'] ?: '-') ?></td>
                                <td class="code-font text-danger"><?= esc($a['jam_keluar'] ?: '-') ?></td>
                                <td><span class="badge bg-success"><?= esc($a['status'] ?: 'Hadir') ?></span></td>
                                <td><small class="text-muted"><?= esc($a['keterangan'] ?: '-') ?></small></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= view('software_engineer/templates/footer') ?>
