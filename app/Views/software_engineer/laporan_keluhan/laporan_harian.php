<?= view('software_engineer/templates/header') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1"><i class="fas fa-calendar-check text-primary me-2"></i> Laporan Progress Harian Software Engineer</h5>
        <small class="text-muted">Form pengisian laporan kerja harian dev/ops (shared table <code>laporan_harian</code>)</small>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahLaporan">
        <i class="fas fa-plus me-1"></i> Input Laporan Harian
    </button>
</div>

<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Ringkasan Pekerjaan</th>
                        <th>Detail Pekerjaan</th>
                        <th>Kendala / Issue</th>
                        <th>Rencana Besok</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($laporan_list)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada laporan harian.</td></tr>
                    <?php else: ?>
                        <?php foreach ($laporan_list as $l): ?>
                            <tr>
                                <td class="code-font fw-bold text-dark"><?= date('d M Y', strtotime($l['tanggal'])) ?></td>
                                <td class="fw-bold text-primary"><?= esc($l['ringkasan_kerja']) ?></td>
                                <td><small class="text-dark"><?= esc($l['detail_pekerjaan']) ?></small></td>
                                <td><small class="text-danger"><?= esc($l['kendala'] ?: '-') ?></small></td>
                                <td><small class="text-muted"><?= esc($l['rencana_besok'] ?: '-') ?></small></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Input Laporan Harian -->
<div class="modal fade" id="modalTambahLaporan" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= site_url('software-engineer/laporan-keluhan/laporan-harian/store') ?>" method="POST" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-calendar-check me-1"></i> Input Laporan Progress Harian</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tanggal Laporan</label>
                    <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Ringkasan Pekerjaan Utama</label>
                    <input type="text" name="ringkasan_kerja" class="form-control" required placeholder="Contoh: Bug fixing modul accounting & setup cron job">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Detail Pekerjaan (Tasks Complete)</label>
                    <textarea name="detail_pekerjaan" class="form-control" rows="3" required placeholder="- Selesai membuat API endpoint X&#10;- Refactoring query Y"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kendala Teknis (Jika Ada)</label>
                    <textarea name="kendala" class="form-control" rows="2" placeholder="Masalah server, API limit, atau bug blocker..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Rencana Kerja Besok</label>
                    <input type="text" name="rencana_besok" class="form-control" placeholder="Contoh: Lanjut integrasi payment gateway">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Laporan Harian</button>
            </div>
        </form>
    </div>
</div>

<?= view('software_engineer/templates/footer') ?>
