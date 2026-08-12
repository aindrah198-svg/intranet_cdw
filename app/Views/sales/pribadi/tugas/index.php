<div class="content-wrapper p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-tasks mr-2"></i>Tugas Saya</h4>
            <p class="text-muted mb-0">Daftar penugasan dari Direktur & Manajemen</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-clipboard-list mr-2"></i>Daftar Tugas Harian</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th>Tanggal</th>
                            <th>Judul Tugas / Instruksi</th>
                            <th>Prioritas</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tugasList)): ?>
                            <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada tugas yang diberikan.</td></tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($tugasList as $t): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= date('d/m/Y', strtotime($t['tanggal_tugas'] ?? $t['tanggal'] ?? date('Y-m-d'))) ?></td>
                                    <td><strong><?= esc($t['judul_tugas'] ?? $t['judul'] ?? $t['deskripsi_tugas'] ?? $t['deskripsi'] ?? '-') ?></strong></td>
                                    <td><span class="badge badge-warning"><?= esc(ucfirst($t['prioritas'] ?? 'Normal')) ?></span></td>
                                    <td class="text-center"><span class="badge badge-info"><?= esc(ucfirst($t['status'] ?? 'Pending')) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
