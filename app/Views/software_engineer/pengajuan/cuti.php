<?= view('software_engineer/templates/header') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1"><i class="fas fa-umbrella-beach text-success me-2"></i> Form Pengajuan Cuti</h5>
        <small class="text-muted">Ajukan permohonan cuti tahunan, cuti sakit, atau keperluan pribadi</small>
    </div>
    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalPengajuanCuti">
        <i class="fas fa-plus me-1"></i> Form Cuti Baru
    </button>
</div>

<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Jenis Cuti</th>
                        <th>Periode Cuti</th>
                        <th>Alasan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($cuti_list)): ?>
                        <tr><td colspan="4" class="text-center text-muted py-4">Belum ada riwayat pengajuan cuti.</td></tr>
                    <?php else: ?>
                        <?php foreach ($cuti_list as $c): ?>
                            <tr>
                                <td class="fw-bold text-dark"><?= esc($c['jenis_cuti']) ?></td>
                                <td class="code-font"><?= date('d M Y', strtotime($c['tanggal_mulai'])) ?> s.d. <?= date('d M Y', strtotime($c['tanggal_selesai'])) ?></td>
                                <td><small class="text-muted"><?= esc($c['alasan']) ?></small></td>
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

<!-- Modal Form Cuti -->
<div class="modal fade" id="modalPengajuanCuti" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= site_url('software-engineer/pengajuan/cuti/store') ?>" method="POST" class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-umbrella-beach me-1"></i> Form Pengajuan Cuti</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Jenis Cuti</label>
                    <select name="jenis_cuti" class="form-select">
                        <option value="Cuti Tahunan">Cuti Tahunan</option>
                        <option value="Cuti Sakit">Cuti Sakit</option>
                        <option value="Cuti Alasan Penting">Cuti Alasan Penting</option>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-control" required value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="form-control" required value="<?= date('Y-m-d') ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Alasan Cuti</label>
                    <textarea name="alasan" class="form-control" rows="3" required placeholder="Uraikan keperluan cuti..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success">Kirim Cuti</button>
            </div>
        </form>
    </div>
</div>

<?= view('software_engineer/templates/footer') ?>
