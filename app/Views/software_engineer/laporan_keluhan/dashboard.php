<?= view('software_engineer/templates/header') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1"><i class="fas fa-chart-line text-warning me-2"></i> Dashboard Laporan & Keluhan</h5>
        <small class="text-muted">Ringkasan progress harian SE dan status keluhan kerja</small>
    </div>
</div>

<div class="row g-4">
    <!-- Laporan Progress Harian -->
    <div class="col-md-7">
        <div class="card card-custom h-100">
            <div class="card-header bg-transparent border-bottom fw-bold d-flex justify-content-between align-items-center">
                <span><i class="fas fa-calendar-check text-primary me-2"></i> Laporan Progress Harian Terakhir</span>
                <a href="<?= site_url('software-engineer/laporan-keluhan/laporan-harian') ?>" class="btn btn-sm btn-outline-primary">+ Buat Laporan</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Ringkasan Kerja</th>
                                <th>Kendala</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($laporan_list)): ?>
                                <tr><td colspan="3" class="text-center text-muted py-4">Belum ada laporan harian dibuat.</td></tr>
                            <?php else: ?>
                                <?php foreach ($laporan_list as $l): ?>
                                    <tr>
                                        <td class="code-font fw-bold text-dark"><?= date('d M Y', strtotime($l['tanggal'])) ?></td>
                                        <td><?= esc($l['ringkasan_kerja']) ?></td>
                                        <td><small class="text-danger"><?= esc($l['kendala'] ?: 'Tidak ada') ?></small></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Keluhan -->
    <div class="col-md-5">
        <div class="card card-custom h-100">
            <div class="card-header bg-transparent border-bottom fw-bold d-flex justify-content-between align-items-center">
                <span><i class="fas fa-exclamation-circle text-danger me-2"></i> Keluhan Kerja (Infrastruktur / Tim)</span>
                <a href="<?= site_url('software-engineer/laporan-keluhan/keluhan') ?>" class="btn btn-sm btn-outline-danger">+ Buat Keluhan</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($keluhan_list)): ?>
                                <tr><td colspan="3" class="text-center text-muted py-4">Belum ada keluhan dikirim.</td></tr>
                            <?php else: ?>
                                <?php foreach ($keluhan_list as $k): ?>
                                    <tr>
                                        <td class="fw-bold text-dark"><?= esc($k['judul_keluhan']) ?></td>
                                        <td><small class="text-muted"><?= esc($k['kategori']) ?></small></td>
                                        <td><span class="badge bg-warning text-dark"><?= esc($k['status']) ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('software_engineer/templates/footer') ?>
