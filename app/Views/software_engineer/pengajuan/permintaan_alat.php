<?= view('software_engineer/templates/header') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1"><i class="fas fa-laptop text-primary me-2"></i> Permintaan Alat / Software / Lisensi Tools</h5>
        <small class="text-muted">Form pengajuan upgrade server, lisensi IDE/SaaS, domain, atau perangkat keras SE</small>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalPermintaanAlat">
        <i class="fas fa-plus me-1"></i> Buat Permintaan Baru
    </button>
</div>

<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Item / Lisensi</th>
                        <th>Alasan & Kebutuhan</th>
                        <th>Estimasi Biaya</th>
                        <th>Status Approval</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pengajuan_alat)): ?>
                        <tr><td colspan="4" class="text-center text-muted py-4">Belum ada permintaan lisensi/alat diajukan.</td></tr>
                    <?php else: ?>
                        <?php foreach ($pengajuan_alat as $pa): ?>
                            <tr>
                                <td class="fw-bold text-primary"><?= esc($pa['nama_barang_tools'] ?? 'Tools') ?></td>
                                <td><small class="text-dark"><?= esc($pa['alasan'] ?? '-') ?></small></td>
                                <td><small class="code-font fw-bold text-dark">Rp <?= number_format($pa['estimasi_biaya'] ?? 0, 0, ',', '.') ?></small></td>
                                <td><span class="badge bg-warning text-dark"><?= esc($pa['status'] ?? 'Pending') ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Permintaan Alat -->
<div class="modal fade" id="modalPermintaanAlat" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= site_url('software-engineer/pengajuan/permintaan-alat/store') ?>" method="POST" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-paper-plane me-1"></i> Permintaan Alat / Software / Lisensi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Item / Software / Lisensi</label>
                    <input type="text" name="nama_item" class="form-control" required placeholder="Contoh: Lisensi JetBrains PhpStorm / Upgrade Cloud VPS RAM">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Estimasi Biaya (Rp)</label>
                    <input type="number" name="estimasi_biaya" class="form-control code-font" placeholder="1500000">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Alasan & Urgensi Kebutuhan</label>
                    <textarea name="alasan" class="form-control" rows="3" required placeholder="Uraikan manfaat dan urgensi untuk mendukung dev/ops..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
            </div>
        </form>
    </div>
</div>

<?= view('software_engineer/templates/footer') ?>
