<div class="content-wrapper p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-paper-plane mr-2"></i>Form Pengajuan (Cuti / Kasbon)</h4>
            <p class="text-muted mb-0">Riwayat pengajuan cuti, izin, dan pinjaman kasbon</p>
        </div>
    </div>

    <div class="row">
        <!-- Cuti -->
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
                                <tr><td colspan="3" class="text-center py-3 text-muted">Belum ada pengajuan cuti.</td></tr>
                            <?php else: ?>
                                <?php foreach ($cutiList as $c): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($c['tanggal_mulai'])) ?></td>
                                        <td><?= esc($c['alasan'] ?? '-') ?></td>
                                        <td class="text-center"><span class="badge badge-info"><?= esc($c['status'] ?? 'Pending') ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Kasbon -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-hand-holding-usd mr-2"></i>Pengajuan Kasbon</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Tanggal</th>
                                <th class="text-right">Nominal</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($kasbonList)): ?>
                                <tr><td colspan="3" class="text-center py-3 text-muted">Belum ada pengajuan kasbon.</td></tr>
                            <?php else: ?>
                                <?php foreach ($kasbonList as $kb): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($kb['tanggal_pengajuan'] ?? $kb['created_at'])) ?></td>
                                        <td class="text-right font-weight-bold">Rp <?= number_format($kb['jumlah'] ?? $kb['nominal'] ?? 0, 0, ',', '.') ?></td>
                                        <td class="text-center"><span class="badge badge-warning"><?= esc($kb['status'] ?? 'Pending') ?></span></td>
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
