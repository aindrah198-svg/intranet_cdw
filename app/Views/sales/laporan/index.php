<div class="content-wrapper p-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-calendar-alt mr-2"></i>Laporan Penjualan Harian & Mingguan</h4>
            <p class="text-muted mb-0">Laporan transaksi closing dan performa penjualan per periode</p>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-outline-secondary shadow-sm"><i class="fas fa-print mr-1"></i> Cetak Laporan</button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="<?= site_url('sales/laporan') ?>" class="form-inline">
                <label class="mr-2 font-weight-bold">Bulan:</label>
                <select name="bulan" class="form-control mr-3">
                    <?php
                    $bulanNames = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
                    foreach ($bulanNames as $k => $v):
                    ?>
                        <option value="<?= $k ?>" <?= ($bulan == $k || $bulan == (int)$k) ? 'selected' : '' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                </select>

                <label class="mr-2 font-weight-bold">Tahun:</label>
                <select name="tahun" class="form-control mr-3">
                    <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                        <option value="<?= $y ?>" <?= ($tahun == $y) ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>

                <button type="submit" class="btn btn-primary"><i class="fas fa-filter mr-1"></i> Tampilkan</button>
            </form>
        </div>
    </div>

    <!-- Summary Box -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm bg-primary text-white p-3">
                <small class="text-uppercase">Total Volume Penjualan Closing</small>
                <h3 class="font-weight-bold mb-0 mt-1">Rp <?= number_format($totalClosing, 0, ',', '.') ?></h3>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm bg-info text-white p-3">
                <small class="text-uppercase">Jumlah Deal Closing Terkonfirmasi</small>
                <h3 class="font-weight-bold mb-0 mt-1"><?= $jumlahDeal ?> Deal</h3>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list mr-2"></i>Rincian Transaksi Closing Sales</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th>Tanggal</th>
                            <th>Kode Deal</th>
                            <th>Nama Deal</th>
                            <th>Klien / Perusahaan</th>
                            <th class="text-right">Nilai Deal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($deals)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Tidak ada transaksi closing pada bulan ini.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($deals as $d): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= date('d/m/Y', strtotime($d['tanggal_closing'])) ?></td>
                                    <td><code><?= esc($d['kode_deal']) ?></code></td>
                                    <td><strong><?= esc($d['nama_deal']) ?></strong></td>
                                    <td><?= esc($d['perusahaan'] ?? '-') ?></td>
                                    <td class="text-right font-weight-bold text-success">Rp <?= number_format($d['nilai_deal'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
