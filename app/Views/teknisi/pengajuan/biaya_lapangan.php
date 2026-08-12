<div class="content-wrapper p-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-money-bill-wave mr-2"></i>Biaya Lapangan (Reimbursement)</h4>
            <p class="text-muted mb-0">Pengajuan klaim penggantian operasional lokasi (Transport, BBM, Makan, Parkir, Tol)</p>
        </div>
        <div>
            <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#modalBiayaLapangan">
                <i class="fas fa-plus mr-1"></i> Buat Klaim Reimburse
            </button>
        </div>
    </div>

    <!-- Info Alert -->
    <div class="alert alert-info border-0 shadow-sm mb-4">
        <i class="fas fa-info-circle mr-2"></i> <strong>Penting:</strong> Biaya Lapangan adalah **Reimbursement** penggantian biaya kerja operasional di lokasi proyek yang disalurkan ke Accounting. Ini **bukan Kasbon** dan **tidak memotong gaji**.
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list mr-2"></i>Riwayat Pengajuan Biaya Lapangan</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th>Kode Klaim</th>
                            <th>Nama Proyek</th>
                            <th>Kategori</th>
                            <th>Tgl Pengajuan</th>
                            <th>Keterangan Deskripsi</th>
                            <th class="text-right">Nominal (Rp)</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($list)): ?>
                            <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada pengajuan klaim biaya lapangan.</td></tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($list as $row): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><code><?= esc($row['kode_pengajuan']) ?></code></td>
                                    <td><strong><?= esc($row['nama_proyek'] ?? 'Proyek Operasional') ?></strong></td>
                                    <td><span class="badge badge-light border"><?= esc($row['kategori_biaya']) ?></span></td>
                                    <td><?= date('d/m/Y', strtotime($row['tgl_pengajuan'])) ?></td>
                                    <td><?= esc($row['keterangan']) ?></td>
                                    <td class="text-right font-weight-bold text-success">Rp <?= number_format($row['nominal'], 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <span class="badge badge-<?= $row['status'] == 'Disetujui' || $row['status'] == 'Dicairkan' ? 'success' : ($row['status'] == 'Pending' ? 'warning' : 'danger') ?>"><?= esc($row['status']) ?></span>
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

<!-- Modal Biaya Lapangan -->
<div class="modal fade" id="modalBiayaLapangan" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= site_url('teknisi/pengajuan/store-biaya-lapangan') ?>" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold text-primary">Klaim Reimbursement Biaya Lapangan</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Nama Proyek / Lokasi Kerja <span class="text-danger">*</span></label>
                        <input type="text" name="nama_proyek" class="form-control" placeholder="Contoh: Instalasi Fiber Optic Kantor Cabang B" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Kategori Biaya</label>
                        <select name="kategori_biaya" class="form-control">
                            <option value="Transport">Transportasi / Bensin / Tol / Parkir</option>
                            <option value="BBM">BBM</option>
                            <option value="Makan">Uang Makan Lapangan</option>
                            <option value="Parkir/Tol">Parkir / Tol</option>
                            <option value="Akomodasi">Akomodasi / Penginapan</option>
                            <option value="Material Darurat">Material Darurat Proyek</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Nominal Reimbursement (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="nominal" class="form-control" placeholder="150000" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Tanggal Pengeluaran</label>
                        <input type="date" name="tgl_pengajuan" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Keterangan / Rincian Pengeluaran <span class="text-danger">*</span></label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Deskripsikan pengeluaran uang secara detail..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirim Klaim Reimbursement</button>
                </div>
            </form>
        </div>
    </div>
</div>
