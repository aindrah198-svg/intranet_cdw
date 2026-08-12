<div class="main-content" style="margin-left: 250px; padding: 25px;">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1 fw-bold text-dark"><i class="fas fa-comment-dots text-danger me-2"></i> Keluhan & Aspirasi Karyawan (Shared with Direktur)</h4>
                <p class="text-muted small mb-0">Tinjau & berikan tanggapan atas keluhan karyawan (1 Data = 1 Tabel `keluhan_karyawan`)</p>
            </div>
        </div>

        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Stat Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3 bg-primary text-white p-3">
                    <h6 class="text-white-50 small mb-1">Total Keluhan Baru</h6>
                    <h3 class="fw-bold mb-0"><?= $statistik['baru'] ?? 0 ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3 bg-warning text-dark p-3">
                    <h6 class="text-dark-50 small mb-1">Sedang Diproses</h6>
                    <h3 class="fw-bold mb-0"><?= $statistik['diproses'] ?? 0 ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3 bg-success text-white p-3">
                    <h6 class="text-white-50 small mb-1">Selesai Ditanggapi</h6>
                    <h3 class="fw-bold mb-0"><?= $statistik['selesai'] ?? 0 ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3 bg-danger text-white p-3">
                    <h6 class="text-white-50 small mb-1">Ditolak</h6>
                    <h3 class="fw-bold mb-0"><?= $statistik['ditolak'] ?? 0 ?></h3>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Pengirim (Karyawan)</th>
                                <th>Divisi</th>
                                <th>Kategori</th>
                                <th>Judul Keluhan</th>
                                <th>Status</th>
                                <th>Tanggapan HRD</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($keluhanList)): foreach($keluhanList as $idx => $k): ?>
                                <tr>
                                    <td><?= $idx + 1 ?></td>
                                    <td class="fw-bold"><?= esc($k['nama_lengkap'] ?: 'Anonim') ?></td>
                                    <td><?= esc($k['divisi'] ?: '-') ?></td>
                                    <td><span class="badge bg-secondary"><?= esc($k['kategori']) ?></span></td>
                                    <td><?= esc($k['judul']) ?></td>
                                    <td>
                                        <?php if($k['status'] == 'baru'): ?>
                                            <span class="badge bg-primary">Baru</span>
                                        <?php elseif($k['status'] == 'diproses'): ?>
                                            <span class="badge bg-warning text-dark">Diproses</span>
                                        <?php elseif($k['status'] == 'selesai'): ?>
                                            <span class="badge bg-success">Selesai</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Ditolak</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><small class="text-muted"><?= esc($k['tanggapan'] ?: 'Belum ditanggapi') ?></small></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalTanggapi<?= $k['id'] ?>">
                                            <i class="fas fa-reply me-1"></i> Tanggapi
                                        </button>

                                        <!-- Modal Tanggapi -->
                                        <div class="modal fade" id="modalTanggapi<?= $k['id'] ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="<?= base_url('admin/keluhan/tanggapi/'.$k['id']) ?>" method="post">
                                                        <?= csrf_field() ?>
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Tanggapi Keluhan Karyawan</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-content p-3 border-0">
                                                            <p class="mb-2"><strong>Judul:</strong> <?= esc($k['judul']) ?></p>
                                                            <p class="mb-3 text-muted"><strong>Deskripsi:</strong> <?= esc($k['deskripsi']) ?></p>
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Update Status</label>
                                                                <select name="status" class="form-select" required>
                                                                    <option value="diproses" <?= $k['status'] == 'diproses' ? 'selected' : '' ?>>Diproses</option>
                                                                    <option value="selesai" <?= $k['status'] == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                                                    <option value="ditolak" <?= $k['status'] == 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Tanggapan HRD</label>
                                                                <textarea name="tanggapan" class="form-control" rows="3" required><?= esc($k['tanggapan']) ?></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-primary">Simpan Tanggapan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="8" class="text-center text-muted py-4">Belum ada data keluhan karyawan.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
