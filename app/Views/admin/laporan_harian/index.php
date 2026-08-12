<div class="main-content" style="margin-left: 250px; padding: 25px;">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1 fw-bold text-dark"><i class="fas fa-tasks text-primary me-2"></i> Laporan Kerja Harian (Shared with Direktur)</h4>
                <p class="text-muted small mb-0">Monitor seluruh laporan aktivitas harian karyawan (1 Data = 1 Tabel `penugasan_harian` / `laporan_harian`)</p>
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
                                <th>Divisi</th>
                                <th>Tanggal Laporan</th>
                                <th>Judul Tugas / Aktivitas</th>
                                <th>Status Report</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($laporan)): foreach($laporan as $idx => $l): ?>
                                <tr>
                                    <td><?= $idx + 1 ?></td>
                                    <td class="fw-bold"><?= esc($l['nama_lengkap'] ?? 'Karyawan') ?></td>
                                    <td><span class="badge bg-secondary"><?= esc($l['divisi'] ?? 'General') ?></span></td>
                                    <td><?= date('d M Y', strtotime($l['tanggal'] ?? $l['created_at'] ?? date('Y-m-d'))) ?></td>
                                    <td><?= esc($l['judul'] ?? $l['tugas'] ?? 'Laporan Pekerjaan Lapangan') ?></td>
                                    <td>
                                        <span class="badge bg-success">Submit On Time</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i> Detail Laporan</button>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data laporan harian yang disubmit.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
