<div class="content-wrapper p-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-coins mr-2"></i>Laporan Perubahan Modal Pemilik</h4>
            <p class="text-muted mb-0">Owner's Equity Statement - Perubahan ekuitas pemilik periode terpilih</p>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-outline-secondary shadow-sm mr-2">
                <i class="fas fa-print mr-1"></i> Cetak Laporan
            </button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="<?= site_url('accounting/laporan-keuangan/laporan/modal-pemilik') ?>" class="form-inline">
                <label class="mr-2 font-weight-bold">Periode Tanggal:</label>
                <input type="date" name="tanggal_mulai" class="form-control mr-2" value="<?= $startDate ?>">
                <span class="mr-2">s/d</span>
                <input type="date" name="tanggal_selesai" class="form-control mr-3" value="<?= $endDate ?>">

                <button type="submit" class="btn btn-primary"><i class="fas fa-filter mr-1"></i> Tampilkan Laporan</button>
            </form>
        </div>
    </div>

    <!-- Summary Statement Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 text-center font-weight-bold text-dark">PT. CIPTA DUTA WACANA</h5>
            <h6 class="mb-0 text-center text-primary font-weight-bold">LAPORAN PERUBAHAN MODAL</h6>
            <p class="text-center text-muted small mb-0">Periode: <?= date('d F Y', strtotime($startDate)) ?> - <?= date('d F Y', strtotime($endDate)) ?></p>
        </div>
        <div class="card-body p-4">
            <div class="row justify-content-center">
                <div class="col-md-9">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="thead-dark">
                            <tr>
                                <th>Uraian Perubahan Ekuitas / Modal</th>
                                <th class="text-right" width="240">Jumlah (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Modal Awal Pemilik</strong> (per <?= date('d/m/Y', strtotime($startDate)) ?>)</td>
                                <td class="text-right font-weight-bold">Rp <?= number_format($modalAwal, 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td class="pl-4"><i class="fas fa-plus text-success mr-2"></i> Setoran Modal Tambahan</td>
                                <td class="text-right text-success">Rp <?= number_format($setoranModal, 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td class="pl-4"><i class="fas fa-plus text-info mr-2"></i> Laba / (Rugi) Bersih Periode Berjalan</td>
                                <td class="text-right <?= $labaBersih >= 0 ? 'text-info' : 'text-danger' ?> font-weight-bold">
                                    Rp <?= number_format($labaBersih, 0, ',', '.') ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="pl-4"><i class="fas fa-minus text-danger mr-2"></i> Prive / Penarikan / Deviden Pemilik</td>
                                <td class="text-right text-danger">(Rp <?= number_format(abs($prive), 0, ',', '.') ?>)</td>
                            </tr>
                            <tr class="bg-light font-weight-bold text-primary style-total">
                                <td><strong>MODAL AKHIR PEMILIK</strong> (per <?= date('d/m/Y', strtotime($endDate)) ?>)</td>
                                <td class="text-right font-weight-bold h5 mb-0 text-primary">Rp <?= number_format($modalAkhir, 0, ',', '.') ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
