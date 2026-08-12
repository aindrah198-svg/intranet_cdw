<div class="content-wrapper p-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-tools mr-2"></i>Peralatan Dipinjam (Tracking Alat Project)</h4>
            <p class="text-muted mb-0">Pencatatan alat tidak habis pakai (bor, tangga, alat ukur) & konfirmasi pengembalian</p>
        </div>
        <div>
            <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#modalPinjamAlat">
                <i class="fas fa-plus mr-1"></i> Pinjam Alat Baru
            </button>
        </div>
    </div>

    <!-- Alert Status -->
    <div class="alert alert-info border-0 shadow-sm mb-4">
        <i class="fas fa-info-circle mr-2"></i> <strong>Perhatian:</strong> Alat pinjam wajib dikembalikan tepat waktu setelah proyek selesai untuk menjaga ketersediaan aset operasional.
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list mr-2"></i>Daftar Peminjaman Alat Operasional</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th>Kode Pinjam</th>
                            <th>Nama Peralatan</th>
                            <th>Proyek / Lokasi</th>
                            <th>Tgl Pinjam</th>
                            <th>Rencana Kembali</th>
                            <th>Tgl Realisasi Kembali</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" width="140">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($list)): ?>
                            <tr><td colspan="9" class="text-center py-4 text-muted">Belum ada catatan peminjaman alat.</td></tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($list as $row): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><code><?= esc($row['kode_peminjaman']) ?></code></td>
                                    <td><strong><?= esc($row['nama_alat']) ?></strong> (<?= $row['qty'] ?> Unit)</td>
                                    <td><?= esc($row['nama_proyek'] ?? '-') ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['tgl_pinjam'])) ?></td>
                                    <td class="text-warning font-weight-bold"><?= date('d/m/Y', strtotime($row['tgl_kembali_rencana'])) ?></td>
                                    <td><?= !empty($row['tgl_kembali_realisasi']) ? date('d/m/Y', strtotime($row['tgl_kembali_realisasi'])) : '-' ?></td>
                                    <td class="text-center">
                                        <?php if ($row['status'] == 'Dipinjam'): ?>
                                            <span class="badge badge-warning">Dipinjam</span>
                                        <?php else: ?>
                                            <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Dikembalikan</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($row['status'] == 'Dipinjam'): ?>
                                            <form action="<?= site_url('teknisi/gudang/kembalikan-alat/' . $row['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Konfirmasi pengembalian alat ini?')">
                                                <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-undo mr-1"></i> Kembalikan</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted small"><i class="fas fa-check-circle text-success"></i> Selesai</span>
                                        <?php endif; ?>
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

<!-- Modal Pinjam Alat -->
<div class="modal fade" id="modalPinjamAlat" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= site_url('teknisi/gudang/pinjam-alat') ?>" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold text-primary">Form Peminjaman Alat Kerja</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Nama Peralatan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_alat" class="form-control" placeholder="Contoh: Mesin Bor Cordless / Tangga Teleskopik" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Jumlah Unit (Qty)</label>
                        <input type="number" name="qty" class="form-control" value="1" min="1" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Proyek / Pekerjaan</label>
                        <input type="text" name="nama_proyek" class="form-control" placeholder="Contoh: Instalasi FO Gedung A">
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Tanggal Peminjaman</label>
                        <input type="date" name="tgl_pinjam" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Rencana Tanggal Pengembalian <span class="text-danger">*</span></label>
                        <input type="date" name="tgl_kembali_rencana" class="form-control" value="<?= date('Y-m-d', strtotime('+7 days')) ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Catatan Peminjaman</label>
                        <textarea name="catatan" class="form-control" rows="2" placeholder="Keperluan penggunaan alat..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Peminjaman</button>
                </div>
            </form>
        </div>
    </div>
</div>
