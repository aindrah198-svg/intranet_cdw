<div class="content-wrapper p-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-percentage mr-2"></i>Manajemen PPN (Pajak Pertambahan Nilai)</h4>
            <p class="text-muted mb-0">Kelola Faktur Pajak Masukan, Keluaran, dan Rekapitulasi PPN Terutang</p>
        </div>
        <div>
            <a href="<?= site_url('accounting/manajemen-pajak/ppn/faktur') ?>" class="btn btn-primary shadow-sm mr-2">
                <i class="fas fa-file-invoice-dollar mr-1"></i> Faktur Pajak
            </a>
            <a href="<?= site_url('accounting/manajemen-pajak/ppn/laporan') ?>" class="btn btn-outline-primary shadow-sm">
                <i class="fas fa-file-pdf mr-1"></i> Laporan SPT PPN
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="<?= site_url('accounting/manajemen-pajak/ppn') ?>" class="form-inline">
                <label class="mr-2 font-weight-bold">Masa Pajak:</label>
                <select name="bulan" class="form-control mr-3">
                    <?php foreach ($bulanOptions as $key => $val): ?>
                        <option value="<?= str_pad($key, 2, '0', STR_PAD_LEFT) ?>" <?= ($bulan == str_pad($key, 2, '0', STR_PAD_LEFT) || $bulan == $key) ? 'selected' : '' ?>>
                            <?= $val ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label class="mr-2 font-weight-bold">Tahun:</label>
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
                    <small class="text-uppercase font-weight-bold">PPN Masukan (Dapat Dikreditkan)</small>
                    <h4 class="mb-0 mt-2 font-weight-bold">Rp <?= number_format($ppn_masukan ?? 0, 0, ',', '.') ?></h4>
                    <small class="text-white-50">Pembelian & Pengadaan Barang/Jasa</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-info text-white">
                <div class="card-body">
                    <small class="text-uppercase font-weight-bold">PPN Keluaran (Penjualan)</small>
                    <h4 class="mb-0 mt-2 font-weight-bold">Rp <?= number_format($ppn_keluaran ?? 0, 0, ',', '.') ?></h4>
                    <small class="text-white-50">Faktur Penjualan ke Pelanggan</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm <?= ($ppn_kurang_bayar ?? 0) >= 0 ? 'bg-danger' : 'bg-success' ?> text-white">
                <div class="card-body">
                    <small class="text-uppercase font-weight-bold">PPN Kurang / (Lebih) Bayar</small>
                    <h4 class="mb-0 mt-2 font-weight-bold">Rp <?= number_format(abs($ppn_kurang_bayar ?? 0), 0, ',', '.') ?></h4>
                    <small class="text-white-50"><?= ($ppn_kurang_bayar ?? 0) >= 0 ? 'Kurang Bayar (Setor ke Kas Negara)' : 'Lebih Bayar (Kompensasi)' ?></small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-secondary text-white">
                <div class="card-body">
                    <small class="text-uppercase font-weight-bold">Tarif PPN Aktif</small>
                    <h4 class="mb-0 mt-2 font-weight-bold"><?= number_format($tarifPpn['tarif_persen'] ?? 11, 1) ?> %</h4>
                    <small class="text-white-50">Dasar UU PPN Berlaku</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list mr-2"></i>Rekapitulasi Masa PPN Tahun <?= $tahun ?></h6>
            <div>
                <a href="<?= site_url('accounting/manajemen-pajak/ppn/masukan') ?>" class="btn btn-sm btn-outline-success mr-2">
                    <i class="fas fa-arrow-down mr-1"></i> Data PPN Masukan
                </a>
                <a href="<?= site_url('accounting/manajemen-pajak/ppn/keluaran') ?>" class="btn btn-sm btn-outline-info">
                    <i class="fas fa-arrow-up mr-1"></i> Data PPN Keluaran
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th>Masa Pajak</th>
                            <th class="text-right">Total PPN Masukan</th>
                            <th class="text-right">Total PPN Keluaran</th>
                            <th class="text-right">PPN Terutang / Less</th>
                            <th class="text-center">Status Setor</th>
                            <th class="text-center" width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ringkasanPpn)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-folder-open fa-2x mb-2 d-block"></i>
                                    Belum ada rekapitulasi data PPN untuk tahun <?= $tahun ?>.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($ringkasanPpn as $item): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><strong>Masa <?= $item['masa_pajak'] ?? '-' ?> - <?= $tahun ?></strong></td>
                                    <td class="text-right text-success">Rp <?= number_format($item['total_ppn_masukan'] ?? 0, 0, ',', '.') ?></td>
                                    <td class="text-right text-info">Rp <?= number_format($item['total_ppn_keluaran'] ?? 0, 0, ',', '.') ?></td>
                                    <td class="text-right font-weight-bold">Rp <?= number_format(($item['total_ppn_keluaran'] ?? 0) - ($item['total_ppn_masukan'] ?? 0), 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <span class="badge badge-warning">Belum Setor</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= site_url('accounting/manajemen-pajak/ppn/laporan?bulan=' . $item['masa_pajak'] . '&tahun=' . $tahun) ?>" class="btn btn-sm btn-light shadow-sm" title="Lihat Detail">
                                            <i class="fas fa-eye text-primary"></i>
                                        </a>
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
