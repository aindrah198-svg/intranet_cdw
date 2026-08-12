<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="fas fa-hand-holding-usd text-primary me-2"></i> Daftar Kasbon Karyawan</h4>
                <p class="text-muted mb-0">Daftar pengajuan kasbon karyawan yang disetujui Direktur/HRD untuk dieksekusi Accounting</p>
            </div>
            <a href="<?= base_url('accounting/kasbon/potong-gaji') ?>" class="btn btn-primary">
                <i class="fas fa-calculator me-1"></i> Proses Potong Gaji
            </a>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show"><?= session()->getFlashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <!-- Ringkasan Stat Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-6 col-xl-3">
                <div class="card card-custom p-3 border-start border-4 border-primary">
                    <div class="text-muted small">Total Kasbon Disetujui</div>
                    <div class="fs-4 fw-bold text-primary mt-1">Rp <?= number_format($totalApproved, 0, ',', '.') ?></div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card card-custom p-3 border-start border-4 border-warning">
                    <div class="text-muted small">Total Kasbon Menunggu</div>
                    <div class="fs-4 fw-bold text-warning mt-1">Rp <?= number_format($totalPending, 0, ',', '.') ?></div>
                </div>
            </div>
        </div>

        <div class="card card-custom p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>No. Kasbon</th>
                            <th>Karyawan</th>
                            <th>Tgl Pengajuan</th>
                            <th>Jumlah Kasbon</th>
                            <th>Alasan</th>
                            <th>Status Direktur</th>
                            <th>Status HRD</th>
                            <th>Status Potong</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($kasbonList)): foreach ($kasbonList as $idx => $k): ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td class="fw-bold text-dark"><?= esc($k['nomor_kasbon'] ?? ('KSB-' . $k['id'])) ?></td>
                                <td>
                                    <div class="fw-bold"><?= esc($k['nama_lengkap'] ?? 'Staff') ?></div>
                                    <div class="small text-muted"><?= esc($k['nik'] ?? '-') ?> | <?= esc($k['departemen'] ?? 'General') ?></div>
                                </td>
                                <td><?= esc($k['tanggal_pengajuan'] ?? date('Y-m-d', strtotime($k['created_at']))) ?></td>
                                <td class="fw-bold text-primary">Rp <?= number_format($k['jumlah_kasbon'] ?? 0, 0, ',', '.') ?></td>
                                <td><?= esc($k['alasan'] ?? '-') ?></td>
                                <td>
                                    <?php $stDir = strtolower($k['status_direktur'] ?? 'menunggu'); ?>
                                    <span class="badge bg-<?= $stDir == 'disetujui' ? 'success' : ($stDir == 'ditolak' ? 'danger' : 'warning') ?>">
                                        <?= ucfirst($stDir) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php $stHrd = strtolower($k['status_hrd'] ?? 'menunggu'); ?>
                                    <span class="badge bg-<?= $stHrd == 'disetujui' ? 'success' : ($stHrd == 'ditolak' ? 'danger' : 'warning') ?>">
                                        <?= ucfirst($stHrd) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php $stAll = strtolower($k['status_keseluruhan'] ?? 'belum'); ?>
                                    <span class="badge bg-<?= $stAll == 'lunas' ? 'info' : 'secondary' ?>">
                                        <?= ucfirst($stAll) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="9" class="text-center text-muted py-4">Belum ada data kasbon karyawan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
