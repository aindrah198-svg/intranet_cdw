<div class="main-content" style="margin-left: 250px; padding: 25px;">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1 fw-bold text-dark"><i class="fas fa-trophy text-warning me-2"></i> Key Performance Indicator (KPI) Karyawan</h4>
                <p class="text-muted small mb-0">Perhitungan skor performa bulanan berdasarkan agregasi Laporan Harian & Tingkat Kehadiran</p>
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
                                <th>Divisi / Jabatan</th>
                                <th>Laporan Harian Submit</th>
                                <th>Kehadiran (%)</th>
                                <th>Skor KPI Bulanan</th>
                                <th>Predikat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($karyawanList)): foreach($karyawanList as $idx => $k): ?>
                                <tr>
                                    <td><?= $idx + 1 ?></td>
                                    <td class="fw-bold"><?= esc($k['nama_lengkap']) ?></td>
                                    <td><?= esc($k['divisi']) ?> - <?= esc($k['jabatan']) ?></td>
                                    <td><span class="badge bg-info">22 / 22 Hari</span></td>
                                    <td><span class="badge bg-success">100%</span></td>
                                    <td class="fw-bold fs-6">88.5</td>
                                    <td><span class="badge bg-success">Sangat Baik (A)</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-chart-bar"></i> Detail KPI</button>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="8" class="text-center text-muted py-4">Belum ada data skor KPI.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
