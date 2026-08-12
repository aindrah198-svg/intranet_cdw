<div class="content-wrapper p-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-warehouse mr-2"></i>Dashboard Gudang & Inventory</h4>
            <p class="text-muted mb-0">Ringkasan stok barang, status peminjaman alat, dan perawatan peralatan</p>
        </div>
        <div>
            <a href="<?= site_url('teknisi/gudang/peralatan-dipinjam') ?>" class="btn btn-primary shadow-sm">
                <i class="fas fa-tools mr-1"></i> Peralatan Dipinjam
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-warning text-dark p-3">
                <small class="text-uppercase font-weight-bold">Peralatan Sedang Dipinjam</small>
                <h3 class="mb-0 mt-2 font-weight-bold"><?= $totalPinjam ?> Unit</h3>
                <small class="text-dark-50">Bor, Tangga, Alat Ukur, dll.</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-info text-white p-3">
                <small class="text-uppercase font-weight-bold">Perawatan Alat Dijadwalkan</small>
                <h3 class="mb-0 mt-2 font-weight-bold"><?= $totalPerawatan ?> Alat</h3>
                <small class="text-white-50">Maintenance & Kalibrasi</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-success text-white p-3">
                <small class="text-uppercase font-weight-bold">Stok Gudang Material</small>
                <h3 class="mb-0 mt-2 font-weight-bold">Tersedia</h3>
                <small class="text-white-50">Kabel, Konektor, & Sparepart</small>
            </div>
        </div>
    </div>

    <!-- Recent Borrowed Equipment Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history mr-2"></i>Peminjaman Peralatan Terbaru</h6>
            <a href="<?= site_url('teknisi/gudang/peralatan-dipinjam') ?>" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Kode Pinjam</th>
                            <th>Nama Alat</th>
                            <th>Qty</th>
                            <th>Tgl Pinjam</th>
                            <th>Rencana Kembali</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($peralatanPinjamList)): ?>
                            <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada peminjaman alat aktif.</td></tr>
                        <?php else: ?>
                            <?php foreach ($peralatanPinjamList as $row): ?>
                                <tr>
                                    <td><code><?= esc($row['kode_peminjaman']) ?></code></td>
                                    <td><strong><?= esc($row['nama_alat']) ?></strong></td>
                                    <td><?= $row['qty'] ?> Unit</td>
                                    <td><?= date('d/m/Y', strtotime($row['tgl_pinjam'])) ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['tgl_kembali_rencana'])) ?></td>
                                    <td class="text-center">
                                        <span class="badge badge-<?= $row['status'] == 'Dipinjam' ? 'warning' : 'success' ?>"><?= esc($row['status']) ?></span>
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
