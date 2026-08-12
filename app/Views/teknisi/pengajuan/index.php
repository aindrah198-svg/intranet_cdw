<div class="content-wrapper p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-list mr-2"></i>Semua Pengajuan Staf Teknisi</h4>
            <p class="text-muted mb-0">Dashboard rekap seluruh pengajuan reimbursement biaya lapangan, pembelian, & cuti</p>
        </div>
        <div>
            <a href="<?= site_url('teknisi/pengajuan/biaya-lapangan') ?>" class="btn btn-primary shadow-sm mr-2">
                <i class="fas fa-money-bill-wave mr-1"></i> Biaya Lapangan (Reimburse)
            </a>
            <a href="<?= site_url('teknisi/pengajuan/permintaan-pembelian') ?>" class="btn btn-info text-white shadow-sm">
                <i class="fas fa-shopping-cart mr-1"></i> Permintaan Pembelian
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Reimbursement Biaya Lapangan -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-file-invoice-dollar mr-2"></i>Pengajuan Reimbursement Biaya Lapangan</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Tgl Pengajuan</th>
                                <th>Kategori</th>
                                <th class="text-right">Nominal (Rp)</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($biayaList)): ?>
                                <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada pengajuan reimbursement.</td></tr>
                            <?php else: ?>
                                <?php foreach ($biayaList as $b): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($b['tgl_pengajuan'])) ?></td>
                                        <td><span class="badge badge-light border"><?= esc($b['kategori_biaya']) ?></span></td>
                                        <td class="text-right font-weight-bold">Rp <?= number_format($b['nominal'], 0, ',', '.') ?></td>
                                        <td class="text-center">
                                            <span class="badge badge-<?= $b['status'] == 'Disetujui' ? 'success' : ($b['status'] == 'Pending' ? 'warning' : 'secondary') ?>"><?= esc($b['status']) ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Cuti & Izin -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-calendar-minus mr-2"></i>Pengajuan Cuti / Izin</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Tgl Mulai</th>
                                <th>Alasan</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($cutiList)): ?>
                                <tr><td colspan="3" class="text-center py-4 text-muted">Belum ada pengajuan cuti.</td></tr>
                            <?php else: ?>
                                <?php foreach ($cutiList as $c): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($c['tanggal_mulai'])) ?></td>
                                        <td><?= esc($c['alasan'] ?? '-') ?></td>
                                        <td class="text-center">
                                            <span class="badge badge-info"><?= esc($c['status'] ?? 'Pending') ?></span>
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
</div>
