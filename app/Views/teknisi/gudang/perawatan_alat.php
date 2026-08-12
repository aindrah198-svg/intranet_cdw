<div class="content-wrapper p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-wrench mr-2"></i>Perawatan & Maintenance Alat Gudang</h4>
            <p class="text-muted mb-0">Catatan pemeliharaan rutin, perbaikan, dan kalibrasi peralatan milik gudang</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history mr-2"></i>Riwayat Perawatan Peralatan Operasional</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th>Kode Maintenance</th>
                            <th>Nama Peralatan</th>
                            <th>Jenis Perawatan</th>
                            <th>Tgl Maintenance</th>
                            <th class="text-right">Biaya (Rp)</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($list)): ?>
                            <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada data riwayat perawatan alat.</td></tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($list as $row): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><code><?= esc($row['kode_perawatan']) ?></code></td>
                                    <td><strong><?= esc($row['nama_alat']) ?></strong></td>
                                    <td><span class="badge badge-info"><?= esc($row['jenis_perawatan']) ?></span></td>
                                    <td><?= date('d/m/Y', strtotime($row['tgl_perawatan'])) ?></td>
                                    <td class="text-right">Rp <?= number_format($row['biaya'], 0, ',', '.') ?></td>
                                    <td class="text-center"><span class="badge badge-success"><?= esc($row['status']) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
