<div class="content-wrapper p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-file-invoice-dollar mr-2"></i>Slip Gaji Saya</h4>
            <p class="text-muted mb-0">Daftar slip gaji bulanan staf teknisi yang telah disetujui</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-money-check-alt mr-2"></i>Riwayat Slip Gaji</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th>Periode Gaji</th>
                            <th class="text-right">Gaji Pokok</th>
                            <th class="text-right">Pendapatan / Tunjangan</th>
                            <th class="text-right">Potongan</th>
                            <th class="text-right">Gaji Bersih (THP)</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($slipGajiList)): ?>
                            <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada slip gaji yang dirilis.</td></tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($slipGajiList as $s): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><strong><?= str_pad($s['periode_bulan'] ?? $s['bulan'] ?? '1', 2, '0', STR_PAD_LEFT) ?> / <?= $s['periode_tahun'] ?? $s['tahun'] ?? date('Y') ?></strong></td>
                                    <td class="text-right">Rp <?= number_format($s['gaji_pokok'] ?? 0, 0, ',', '.') ?></td>
                                    <td class="text-right text-success">+Rp <?= number_format($s['total_pendapatan'] ?? $s['total_tunjangan'] ?? 0, 0, ',', '.') ?></td>
                                    <td class="text-right text-danger">-Rp <?= number_format($s['total_potongan'] ?? 0, 0, ',', '.') ?></td>
                                    <td class="text-right font-weight-bold text-primary">Rp <?= number_format($s['gaji_bersih'] ?? 0, 0, ',', '.') ?></td>
                                    <td class="text-center"><span class="badge badge-success"><?= esc($s['status'] ?? 'Disetujui') ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
