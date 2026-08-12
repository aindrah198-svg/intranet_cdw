<div class="content-wrapper p-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-history mr-2"></i>Riwayat Audit Saya</h4>
            <p class="text-muted mb-0">Catatan Log Aktivitas dan Audit Trail Pengguna</p>
        </div>
    </div>

    <!-- Tabel Riwayat Audit -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list-alt mr-2"></i>50 Log Aktivitas Terakhir</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th>Waktu Activity</th>
                            <th>Aktivitas / Modul</th>
                            <th>Keterangan Deskripsi</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($auditList)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fas fa-info-circle fa-2x mb-2 d-block"></i>
                                    Belum ada log catatan riwayat aktivitas tersimpan.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($auditList as $row): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><strong><?= !empty($row['created_at']) ? date('d/m/Y H:i:s', strtotime($row['created_at'])) : '-' ?></strong></td>
                                    <td><span class="badge badge-info"><?= esc($row['action'] ?? $row['modul'] ?? 'Aktivitas') ?></span></td>
                                    <td><?= esc($row['description'] ?? $row['keterangan'] ?? '-') ?></td>
                                    <td><code><?= esc($row['ip_address'] ?? '127.0.0.1') ?></code></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
