<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="fas fa-file-alt text-primary me-2"></i> Riwayat Laporan Kerja Saya</h4>
                <p class="text-muted mb-0">Daftar laporan harian yang telah disubmit ke Direktur & HRD</p>
            </div>
            <a href="<?= base_url('staff/laporan/create') ?>" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i> Buat Laporan Baru</a>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show"><?= session()->getFlashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <div class="card card-custom p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal Laporan</th>
                            <th>Judul Aktivitas</th>
                            <th>Deskripsi Hasil Kerja</th>
                            <th>Lampiran</th>
                            <th>Status Review Direktur</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($laporanList)): foreach ($laporanList as $idx => $l): ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td class="fw-bold"><?= date('d M Y', strtotime($l['tanggal'])) ?></td>
                                <td class="fw-semibold text-primary"><?= esc($l['judul']) ?></td>
                                <td><small class="text-secondary"><?= nl2br(esc(substr($l['deskripsi'], 0, 100))) ?>...</small></td>
                                <td>
                                    <?php if (!empty($l['lampiran'])): ?>
                                        <a href="<?= base_url('uploads/laporan/' . $l['lampiran']) ?>" target="_blank" class="btn btn-sm btn-outline-info"><i class="fas fa-download"></i> Lampiran</a>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= ($l['status'] ?? '') == 'Disetujui' ? 'success' : (($l['status'] ?? '') == 'Ditolak' ? 'danger' : 'warning') ?>">
                                        <?= esc($l['status'] ?? 'Pending') ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada laporan harian yang disubmit.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
