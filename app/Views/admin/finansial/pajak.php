<div class="main-content" style="margin-left: 250px; padding: 25px;">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1 fw-bold text-dark"><i class="fas fa-receipt text-warning me-2"></i> Perhitungan Pajak Penghasilan PPh21</h4>
                <p class="text-muted small mb-0">Status PTKP, kalkulasi Pajak Penghasilan PPh21 bulanan & tahunan karyawan</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nama Karyawan</th>
                                <th>NPWP</th>
                                <th>Status PTKP</th>
                                <th>Penghasilan Bruto</th>
                                <th>Estimasi PPh21 Bulanan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($karyawanList)): foreach($karyawanList as $idx => $k): ?>
                                <tr>
                                    <td><?= $idx + 1 ?></td>
                                    <td class="fw-bold"><?= esc($k['nama_lengkap']) ?></td>
                                    <td><code><?= esc($k['no_npwp'] ?: 'Tanpa NPWP') ?></code></td>
                                    <td><span class="badge bg-secondary">TK/0</span></td>
                                    <td>Rp 5.750.000</td>
                                    <td class="fw-bold text-danger">Rp 45.000</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-calculator"></i> Hitung Ulang</button>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data pajak karyawan.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
