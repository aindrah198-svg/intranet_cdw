<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="fas fa-folder-open text-primary me-2"></i> Riwayat Pengajuan Saya</h4>
                <p class="text-muted mb-0">Satu tempat terpadu untuk mengecek status Cuti, Izin, dan Kasbon Anda</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= base_url('staff/pengajuan/cuti') ?>" class="btn btn-outline-primary btn-sm"><i class="fas fa-plus me-1"></i> Form Cuti</a>
                <a href="<?= base_url('staff/pengajuan/izin') ?>" class="btn btn-outline-info btn-sm"><i class="fas fa-plus me-1"></i> Form Izin</a>
                <a href="<?= base_url('staff/pengajuan/kasbon') ?>" class="btn btn-outline-success btn-sm"><i class="fas fa-plus me-1"></i> Form Kasbon</a>
            </div>
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
                            <th>Kode / ID</th>
                            <th>Kategori</th>
                            <th>Tgl Pengajuan</th>
                            <th>Keterangan / Alasan</th>
                            <th>Status Pengajuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($submissions)): foreach ($submissions as $idx => $s): ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td class="fw-bold text-dark"><?= esc($s['kode'] ?? 'REQ-'.$s['id']) ?></td>
                                <td><span class="badge bg-secondary"><?= esc($s['kategori']) ?></span></td>
                                <td><?= date('d M Y', strtotime($s['tanggal'] ?? $s['created_at'])) ?></td>
                                <td><small class="text-secondary"><?= esc($s['alasan'] ?? '-') ?></small></td>
                                <td>
                                    <?php 
                                    $st = $s['status'] ?? 'Menunggu';
                                    $badge = ($st == 'Disetujui' || $st == 'Disetujui HRD' || $st == 'Disetujui Direktur') ? 'success' : ($st == 'Ditolak' ? 'danger' : 'warning');
                                    ?>
                                    <span class="badge bg-<?= $badge ?>"><?= esc($st) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada riwayat pengajuan Cuti, Izin, atau Kasbon.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
