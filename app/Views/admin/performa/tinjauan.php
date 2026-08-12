<div class="main-content" style="margin-left: 250px; padding: 25px;">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1 fw-bold text-dark"><i class="fas fa-clipboard-check text-info me-2"></i> Tinjauan & Evaluasi Periodik Karyawan</h4>
                <p class="text-muted small mb-0">Evaluasi masa probation, evaluasi tahunan, dan catatan rekomendasi perpanjangan kontrak</p>
            </div>
            <button class="btn btn-primary"><i class="fas fa-plus me-1"></i> Buat Form Evaluasi</button>
        </div>

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nama Karyawan</th>
                                <th>Jenis Evaluasi</th>
                                <th>Periode Penilaian</th>
                                <th>Hasil Evaluasi</th>
                                <th>Rekomendasi HRD</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($karyawanList)): foreach($karyawanList as $idx => $k): ?>
                                <tr>
                                    <td><?= $idx + 1 ?></td>
                                    <td class="fw-bold"><?= esc($k['nama_lengkap']) ?></td>
                                    <td><span class="badge bg-secondary">Probation Review (3 Bulan)</span></td>
                                    <td>Mei 2026 - Juli 2026</td>
                                    <td><span class="badge bg-success">Memenuhi Syarat</span></td>
                                    <td class="fw-semibold text-primary">Diangkat Karyawan Tetap</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-file-alt"></i> Lihat Form</button>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data tinjauan evaluasi.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
