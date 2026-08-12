<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="fas fa-money-check-alt text-primary me-2"></i> Slip Gaji Saya</h4>
                <p class="text-muted mb-0">Informasi penggajian resmi read-only dari HRD & Finance</p>
            </div>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show"><?= session()->getFlashdata('error') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <div class="card card-custom p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Periode Penggajian</th>
                            <th>Gaji Pokok</th>
                            <th>Tunjangan</th>
                            <th>Potongan</th>
                            <th>Total Gaji Bersih (Take Home Pay)</th>
                            <th>Status Transfer</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($payrollList)): foreach ($payrollList as $idx => $p): ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td class="fw-bold text-dark"><?= esc($p['periode_bulan'] ?? date('m')) ?> / <?= esc($p['periode_tahun'] ?? date('Y')) ?></td>
                                <td>Rp <?= number_format($p['gaji_pokok'] ?? 0, 0, ',', '.') ?></td>
                                <td><span class="text-success">+ Rp <?= number_format(($p['total_penghasilan'] ?? 0) - ($p['gaji_pokok'] ?? 0), 0, ',', '.') ?></span></td>
                                <td><span class="text-danger">- Rp <?= number_format($p['total_potongan'] ?? 0, 0, ',', '.') ?></span></td>
                                <td class="fw-bold text-primary fs-6">Rp <?= number_format($p['gaji_bersih'] ?? 0, 0, ',', '.') ?></td>
                                <td><span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Paid / Transferred</span></td>
                                <td>
                                    <a href="<?= base_url('staff/payroll/cetak/' . $p['id']) ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-print me-1"></i> Cetak Slip</a>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="8" class="text-center text-muted py-4">Belum ada slip gaji yang dipublikasikan oleh HRD untuk akun Anda.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
