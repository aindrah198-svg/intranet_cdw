<?= view('software_engineer/templates/header') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1"><i class="fas fa-paper-plane text-info me-2"></i> Form Pengajuan Saya</h5>
        <small class="text-muted">Histori seluruh permohonan pengajuan pribadi</small>
    </div>
</div>

<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Jenis Pengajuan</th>
                        <th>Keterangan / Periode</th>
                        <th>Status Approval</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($cuti_list)): ?>
                        <tr><td colspan="3" class="text-center text-muted py-4">Belum ada pengajuan pribadi.</td></tr>
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

<?= view('software_engineer/templates/footer') ?>
