<div class="content-wrapper p-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-hard-hat mr-2"></i>Laporan Pekerjaan Harian / Lapangan (Fitur Wajib)</h4>
            <p class="text-muted mb-0">Catatan progres pengerjaan proyek fisik, lokasi instalasi, & foto kegiatan harian</p>
        </div>
        <div>
            <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#modalLaporanHarian">
                <i class="fas fa-plus mr-1"></i> Buat Laporan Harian
            </button>
        </div>
    </div>

    <!-- Info Alert -->
    <div class="alert alert-success border-0 shadow-sm mb-4">
        <i class="fas fa-check-circle mr-2"></i> <strong>Terintegrasi Terpusat:</strong> Data tersimpan di tabel <code>laporan_harian</code> terpadu yang dapat dipantau langsung oleh Direktur & Manajemen.
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-clipboard-list mr-2"></i>Riwayat Laporan Pekerjaan Harian Teknisi</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th>Tanggal</th>
                            <th>Judul Pekerjaan / Proyek</th>
                            <th>Deskripsi Aktivitas & Progres Lapangan</th>
                            <th class="text-center">Status Review</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($laporanList)): ?>
                            <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada data laporan pekerjaan harian.</td></tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($laporanList as $row): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><strong><?= date('d/m/Y', strtotime($row['tanggal'])) ?></strong></td>
                                    <td><strong><?= esc($row['judul'] ?? 'Pekerjaan Lapangan') ?></strong></td>
                                    <td><?= nl2br(esc($row['deskripsi'] ?? '-')) ?></td>
                                    <td class="text-center">
                                        <span class="badge badge-info"><?= esc($row['status'] ?? 'Pending Review') ?></span>
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

<!-- Modal Laporan Harian -->
<div class="modal fade" id="modalLaporanHarian" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="<?= site_url('teknisi/laporan/store-lapangan') ?>" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold text-primary">Input Laporan Pekerjaan Harian Lapangan</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Tanggal Pekerjaan</label>
                        <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Judul Pekerjaan / Proyek <span class="text-danger">*</span></label>
                        <input type="text" name="judul" class="form-control" placeholder="Contoh: Instalasi Fiber Optic & Termination Rack Server" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Rincian Aktivitas, Hasil Pekerjaan, & Kendala <span class="text-danger">*</span></label>
                        <textarea name="deskripsi" class="form-control" rows="4" placeholder="Jelaskan progres persentase, pekerjaan yang diselesaikan, material yang dipakai, dan kondisi lapangan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan & Kirim Laporan</button>
                </div>
            </form>
        </div>
    </div>
</div>
