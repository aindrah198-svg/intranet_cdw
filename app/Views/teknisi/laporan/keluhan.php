<div class="content-wrapper p-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-exclamation-triangle mr-2"></i>Keluhan Lapangan & Karyawan (Fitur Wajib)</h4>
            <p class="text-muted mb-0">Pusat keluhan teknis, kendala fasilitas kerja, keselamatan K3, dan respon manajemen</p>
        </div>
        <div>
            <button class="btn btn-danger shadow-sm" data-toggle="modal" data-target="#modalKeluhan">
                <i class="fas fa-plus mr-1"></i> Buat Keluhan Baru
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history mr-2"></i>Daftar Keluhan Lapangan & Respon Manajemen</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th>Kode</th>
                            <th>Kategori</th>
                            <th>Judul Keluhan</th>
                            <th>Rincian Kendala Lapangan</th>
                            <th>Tgl Disampaikan</th>
                            <th class="text-center">Status</th>
                            <th>Tanggapan Manajemen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($keluhanList)): ?>
                            <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada keluhan yang disampaikan.</td></tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($keluhanList as $row): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><code><?= esc($row['kode_keluhan']) ?></code></td>
                                    <td><span class="badge badge-warning"><?= esc($row['kategori']) ?></span></td>
                                    <td><strong><?= esc($row['judul']) ?></strong></td>
                                    <td><?= nl2br(esc($row['deskripsi'])) ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['tgl_keluhan'])) ?></td>
                                    <td class="text-center">
                                        <span class="badge badge-<?= $row['status'] == 'Selesai' ? 'success' : ($row['status'] == 'Diproses' ? 'info' : 'danger') ?>"><?= esc($row['status']) ?></span>
                                    </td>
                                    <td><?= esc($row['tanggapan_manajemen'] ?? 'Menunggu respon manajemen') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Keluhan -->
<div class="modal fade" id="modalKeluhan" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= site_url('teknisi/laporan/store-keluhan') ?>" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold text-danger">Form Keluhan & Kendala Lapangan</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Kategori Keluhan <span class="text-danger">*</span></label>
                        <select name="kategori" class="form-control" required>
                            <option value="Kendala Lapangan">Kendala Lapangan / Akses Proyek</option>
                            <option value="Fasilitas/Alat Kerja">Fasilitas / Alat Kerja Rusak</option>
                            <option value="K3/Keselamatan">K3 / Keselamatan Kerja</option>
                            <option value="Administrasi">Administrasi / Operasional</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Judul Keluhan <span class="text-danger">*</span></label>
                        <input type="text" name="judul" class="form-control" placeholder="Contoh: Alat ukur Splicer error di lokasi" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Tanggal Kejadian</label>
                        <input type="date" name="tgl_keluhan" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Deskripsi Rinci Keluhan <span class="text-danger">*</span></label>
                        <textarea name="deskripsi" class="form-control" rows="4" placeholder="Jelaskan kronologi kendala atau keluhan secara detail..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Kirim Keluhan Ke Manajemen</button>
                </div>
            </form>
        </div>
    </div>
</div>
