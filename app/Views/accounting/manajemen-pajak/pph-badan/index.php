<div class="content-wrapper p-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-building mr-2"></i>Manajemen PPh Badan</h4>
            <p class="text-muted mb-0">Perhitungan, Setoran Masa / Tahunan, dan Kredit Pajak PPh Badan</p>
        </div>
        <div>
            <a href="<?= site_url('accounting/manajemen-pajak/pph-badan/perhitungan') ?>" class="btn btn-primary shadow-sm mr-2">
                <i class="fas fa-calculator mr-1"></i> Perhitungan PPh
            </a>
            <a href="<?= site_url('accounting/manajemen-pajak/pph-badan/setoran') ?>" class="btn btn-outline-primary shadow-sm">
                <i class="fas fa-receipt mr-1"></i> Setoran PPh
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="<?= site_url('accounting/manajemen-pajak/pph-badan') ?>" class="form-inline">
                <label class="mr-2 font-weight-bold">Tahun Pajak:</label>
                <select name="tahun" class="form-control mr-3">
                    <?php foreach ($tahunOptions as $t): ?>
                        <option value="<?= $t ?>" <?= ($tahun == $t) ? 'selected' : '' ?>><?= $t ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="btn btn-primary"><i class="fas fa-filter mr-1"></i> Filter Data</button>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <small class="text-uppercase font-weight-bold">Penghasilan Bruto (Omzet)</small>
                    <h4 class="mb-0 mt-2 font-weight-bold">Rp <?= number_format($pphTahunIni['penghasilan_bruto'] ?? 0, 0, ',', '.') ?></h4>
                    <small class="text-white-50">Tahun Pajak <?= $tahun ?></small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body">
                    <small class="text-uppercase font-weight-bold">Penghasilan Kena Pajak (PKP)</small>
                    <h4 class="mb-0 mt-2 font-weight-bold">Rp <?= number_format($pphTahunIni['pkp'] ?? 0, 0, ',', '.') ?></h4>
                    <small class="text-white-50">Setelah Koreksi Fiskal</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning text-dark">
                <div class="card-body">
                    <small class="text-uppercase font-weight-bold">PPh Terutang (<?= $tarifPphBadan['tarif_persen'] ?? 22 ?>%)</small>
                    <h4 class="mb-0 mt-2 font-weight-bold">Rp <?= number_format($pphTahunIni['pph_terutang'] ?? 0, 0, ',', '.') ?></h4>
                    <small class="text-dark-50">Estimasi PPh Terutang Tahun Ini</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-danger text-white">
                <div class="card-body">
                    <small class="text-uppercase font-weight-bold">Kurang / (Lebih) Bayar</small>
                    <h4 class="mb-0 mt-2 font-weight-bold">Rp <?= number_format(abs($pphTahunIni['pph_kurang_bayar'] ?? 0), 0, ',', '.') ?></h4>
                    <small class="text-white-50">PPh 29 Setelah Kredit Pajak</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan Perhitungan PPh Badan -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history mr-2"></i>Riwayat Perhitungan PPh Badan per Tahun</h6>
            <a href="<?= site_url('accounting/manajemen-pajak/pph-badan/laporan') ?>" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-file-excel mr-1"></i> Laporan SPT Tahunan
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th>Tahun Pajak</th>
                            <th class="text-right">Penghasilan Bruto</th>
                            <th class="text-right">PKP</th>
                            <th class="text-right">PPh Terutang</th>
                            <th class="text-right">Kredit Pajak</th>
                            <th class="text-right">Kurang / (Lebih) Bayar</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ringkasanPph)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="fas fa-folder-open fa-2x mb-2 d-block"></i>
                                    Belum ada data perhitungan PPh Badan yang tersimpan.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($ringkasanPph as $row): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><strong><?= $row['tahun_pajak'] ?? $row['tahun'] ?? '-' ?></strong></td>
                                    <td class="text-right">Rp <?= number_format($row['penghasilan_bruto'] ?? 0, 0, ',', '.') ?></td>
                                    <td class="text-right">Rp <?= number_format($row['pkp'] ?? 0, 0, ',', '.') ?></td>
                                    <td class="text-right text-danger font-weight-bold">Rp <?= number_format($row['total_pph_terutang'] ?? $row['pph_terutang'] ?? 0, 0, ',', '.') ?></td>
                                    <td class="text-right text-success">Rp <?= number_format($row['total_kredit_pajak'] ?? $row['kredit_pajak'] ?? 0, 0, ',', '.') ?></td>
                                    <td class="text-right font-weight-bold">Rp <?= number_format($row['total_pph_kurang_bayar'] ?? $row['pph_kurang_bayar'] ?? 0, 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <span class="badge badge-info"><?= $row['status'] ?? 'Draft' ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
