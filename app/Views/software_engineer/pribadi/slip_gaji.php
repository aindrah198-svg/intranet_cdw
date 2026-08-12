<?= view('software_engineer/templates/header') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1"><i class="fas fa-money-check-alt text-success me-2"></i> Slip Gaji Saya</h5>
        <small class="text-muted">Riwayat slip gaji bulanan karyawan Software Engineer</small>
    </div>
</div>

<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Periode Gaji</th>
                        <th>Gaji Pokok</th>
                        <th>Tunjangan</th>
                        <th>Potongan</th>
                        <th>Total Take Home Pay</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($slips)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data slip gaji diterbitkan.</td></tr>
                    <?php else: ?>
                        <?php foreach ($slips as $s): ?>
                            <tr>
                                <td class="fw-bold text-dark"><?= esc($s['periode'] ?? $s['bulan'] ?? '-') ?></td>
                                <td><small class="code-font">Rp <?= number_format($s['gaji_pokok'] ?? 0, 0, ',', '.') ?></small></td>
                                <td><small class="code-font text-success">+ Rp <?= number_format($s['total_tunjangan'] ?? 0, 0, ',', '.') ?></small></td>
                                <td><small class="code-font text-danger">- Rp <?= number_format($s['total_potongan'] ?? 0, 0, ',', '.') ?></small></td>
                                <td><span class="badge bg-success font-weight-bold code-font">Rp <?= number_format($s['thp'] ?? $s['take_home_pay'] ?? 0, 0, ',', '.') ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= view('software_engineer/templates/footer') ?>
