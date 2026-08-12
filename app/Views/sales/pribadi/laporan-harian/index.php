<div class="content-wrapper p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-clipboard-list mr-2"></i>Laporan Kerja Harian</h4>
            <p class="text-muted mb-0">Catatan aktivitas pekerjaan sales harian</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history mr-2"></i>Riwayat Laporan Kerja Harian</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th>Tanggal</th>
                            <th>Aktivitas Ringkas</th>
                            <th>Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($laporanList)): ?>
                            <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada catatan laporan kerja harian.</td></tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($laporanList as $l): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= date('d/m/Y', strtotime($l['tanggal'])) ?></td>
                                    <td><?= esc($l['deskripsi'] ?? '-') ?></td>
                                    <td><span class="badge badge-success"><?= esc($l['progress'] ?? '100%') ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
