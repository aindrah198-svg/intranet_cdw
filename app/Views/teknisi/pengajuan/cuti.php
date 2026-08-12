<div class="content-wrapper p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-calendar-minus mr-2"></i>Form Pengajuan Cuti / Izin Teknisi</h4>
            <p class="text-muted mb-0">Permohonan cuti tahunan, izin sakit, atau keperluan pribadi</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history mr-2"></i>Riwayat Pengajuan Cuti</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Alasan Cuti / Izin</th>
                            <th class="text-center">Status Approval</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($cutiList)): ?>
                            <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada catatan pengajuan cuti.</td></tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($cutiList as $c): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= date('d/m/Y', strtotime($c['tanggal_mulai'])) ?></td>
                                    <td><?= date('d/m/Y', strtotime($c['tanggal_selesai'] ?? $c['tanggal_mulai'])) ?></td>
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
</div>
