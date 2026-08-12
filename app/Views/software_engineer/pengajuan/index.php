<?= view('software_engineer/templates/header') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1"><i class="fas fa-paper-plane text-primary me-2"></i> Semua Pengajuan Saya</h5>
        <small class="text-muted">Rekap seluruh permintaan lisensi, alat, upgrade hosting, & pengajuan cuti</small>
    </div>
</div>

<div class="row g-4">
    <!-- Permintaan Lisensi / Alat / Upgrade Hosting -->
    <div class="col-md-6">
        <div class="card card-custom h-100">
            <div class="card-header bg-transparent border-bottom fw-bold d-flex justify-content-between align-items-center">
                <span><i class="fas fa-laptop text-primary me-2"></i> Permintaan Alat / Software / Lisensi</span>
                <a href="<?= site_url('software-engineer/pengajuan/permintaan-alat') ?>" class="btn btn-sm btn-outline-primary">+ Pengajuan Baru</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Item / Lisensi</th>
                                <th>Estimasi Biaya</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pengajuan_alat)): ?>
                                <tr><td colspan="3" class="text-center text-muted py-4">Belum ada pengajuan alat/lisensi.</td></tr>
                            <?php else: ?>
                                <?php foreach ($pengajuan_alat as $pa): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold"><?= esc($pa['nama_barang_tools'] ?? 'Lisensi Tools') ?></div>
                                            <small class="text-muted"><?= esc($pa['alasan'] ?? '-') ?></small>
                                        </td>
                                        <td><small class="code-font">Rp <?= number_format($pa['estimasi_biaya'] ?? 0, 0, ',', '.') ?></small></td>
                                        <td><span class="badge bg-warning text-dark"><?= esc($pa['status'] ?? 'Pending') ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Pengajuan Cuti -->
    <div class="col-md-6">
        <div class="card card-custom h-100">
            <div class="card-header bg-transparent border-bottom fw-bold d-flex justify-content-between align-items-center">
                <span><i class="fas fa-umbrella-beach text-success me-2"></i> Form Pengajuan Cuti</span>
                <a href="<?= site_url('software-engineer/pengajuan/cuti') ?>" class="btn btn-sm btn-outline-success">+ Ajukan Cuti</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Jenis Cuti</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($cuti_list)): ?>
                                <tr><td colspan="3" class="text-center text-muted py-4">Belum ada riwayat pengajuan cuti.</td></tr>
                            <?php else: ?>
                                <?php foreach ($cuti_list as $c): ?>
                                    <tr>
                                        <td class="fw-bold text-dark"><?= esc($c['jenis_cuti']) ?></td>
                                        <td><small class="code-font"><?= date('d M Y', strtotime($c['tanggal_mulai'])) ?> s.d. <?= date('d M Y', strtotime($c['tanggal_selesai'])) ?></small></td>
                                        <td>
                                            <?php if ($c['status'] === 'Approved'): ?>
                                                <span class="badge bg-success">Disetujui</span>
                                            <?php elseif ($c['status'] === 'Rejected'): ?>
                                                <span class="badge bg-danger">Ditolak</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Pending</span>
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
    </div>
</div>

<?= view('software_engineer/templates/footer') ?>
