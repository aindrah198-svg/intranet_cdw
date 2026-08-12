<div class="content-wrapper p-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-bullseye mr-2"></i>Target vs Realisasi Penjualan</h4>
            <p class="text-muted mb-0">Evaluasi pencapaian target omzet dan target perolehan leads bulanan</p>
        </div>
        <div>
            <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#modalSetTarget">
                <i class="fas fa-edit mr-1"></i> Set Target Bulanan
            </button>
        </div>
    </div>

    <!-- Filter Year -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="<?= site_url('sales/laporan/target') ?>" class="form-inline">
                <label class="mr-2 font-weight-bold">Tahun Evaluasi:</label>
                <select name="tahun" class="form-control mr-3">
                    <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                        <option value="<?= $y ?>" <?= ($tahun == $y) ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
                <button type="submit" class="btn btn-primary"><i class="fas fa-search mr-1"></i> Tampilkan</button>
            </form>
        </div>
    </div>

    <!-- Target Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-line mr-2"></i>Matrix Pencapaian Target Penjualan Tahun <?= $tahun ?></h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="bg-light text-center">
                        <tr>
                            <th width="120">Bulan</th>
                            <th>Target Penjualan</th>
                            <th>Realisasi Penjualan</th>
                            <th>Target Leads</th>
                            <th>Realisasi Leads</th>
                            <th width="180">Pencapaian Omzet</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $bulanNames = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                        foreach ($targetList as $m => $row): 
                        ?>
                            <tr>
                                <td class="font-weight-bold"><?= $bulanNames[$m] ?></td>
                                <td class="text-right">Rp <?= number_format($row['target_penjualan'], 0, ',', '.') ?></td>
                                <td class="text-right font-weight-bold text-success">Rp <?= number_format($row['realisasi_penjualan'], 0, ',', '.') ?></td>
                                <td class="text-center"><?= $row['target_leads'] ?></td>
                                <td class="text-center font-weight-bold"><?= $row['realisasi_leads'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 mr-2" style="height: 12px;">
                                            <div class="progress-bar <?= $row['persen'] >= 100 ? 'bg-success' : 'bg-primary' ?>" style="width: <?= min($row['persen'], 100) ?>%"></div>
                                        </div>
                                        <span class="small font-weight-bold"><?= $row['persen'] ?>%</span>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Set Target -->
<div class="modal fade" id="modalSetTarget" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= site_url('sales/laporan/save-target') ?>" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold text-primary">Set Target Penjualan Bulanan</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Tahun</label>
                        <input type="number" name="tahun" class="form-control" value="<?= date('Y') ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Bulan</label>
                        <select name="bulan" class="form-control" required>
                            <?php foreach ($bulanNames as $k => $v): ?>
                                <option value="<?= $k ?>" <?= (date('n') == $k) ? 'selected' : '' ?>><?= $v ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Target Omzet Penjualan (Rp)</label>
                        <input type="number" name="target_penjualan" class="form-control" placeholder="100000000" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Target Jumlah Leads Baru</label>
                        <input type="number" name="target_leads" class="form-control" placeholder="20" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Target</button>
                </div>
            </form>
        </div>
    </div>
</div>
