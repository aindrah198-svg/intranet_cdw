<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="fas fa-clipboard-list text-primary me-2"></i> Tugas Saya</h4>
                <p class="text-muted mb-0">Daftar penugasan kerja yang diberikan oleh Direktur & HRD</p>
            </div>
            <a href="<?= base_url('staff/laporan/create') ?>" class="btn btn-primary btn-sm"><i class="fas fa-edit me-1"></i> Submit Laporan Harian</a>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show"><?= session()->getFlashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <div class="card card-custom p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Judul Tugas</th>
                            <th>Deskripsi / Instruksi</th>
                            <th>Tanggal & Tenggat</th>
                            <th>Prioritas</th>
                            <th>Status Sekarang</th>
                            <th>Update Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($tugas)): foreach ($tugas as $idx => $t): ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td class="fw-bold text-dark"><?= esc($t['judul_tugas']) ?></td>
                                <td><small class="text-secondary"><?= nl2br(esc($t['deskripsi_tugas'] ?? '-')) ?></small></td>
                                <td>
                                    <div class="small fw-semibold"><i class="far fa-calendar-alt me-1 text-primary"></i> <?= esc($t['tanggal_tugas'] ?? date('Y-m-d')) ?></div>
                                    <small class="text-danger"><i class="far fa-clock me-1"></i> s/d <?= esc($t['tenggat_waktu'] ?? '-') ?></small>
                                </td>
                                <td>
                                    <?php 
                                    $prioClass = ($t['prioritas'] ?? '') == 'tinggi' || ($t['prioritas'] ?? '') == 'mendesak' ? 'danger' : (($t['prioritas'] ?? '') == 'sedang' ? 'warning' : 'secondary');
                                    ?>
                                    <span class="badge bg-<?= $prioClass ?>"><?= ucfirst(esc($t['prioritas'] ?? 'rendah')) ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-<?= ($t['status'] ?? '') == 'selesai' ? 'success' : (($t['status'] ?? '') == 'proses' ? 'info' : 'warning') ?>">
                                        <?= ucfirst(esc($t['status'] ?? 'pending')) ?>
                                    </span>
                                </td>
                                <td>
                                    <form action="<?= base_url('staff/tugas/update-status') ?>" method="POST" class="d-flex gap-1">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                        <select name="status" class="form-select form-select-sm" style="width: 120px;" onchange="this.form.submit()">
                                            <option value="pending" <?= ($t['status'] ?? '') == 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="proses" <?= ($t['status'] ?? '') == 'proses' ? 'selected' : '' ?>>Proses</option>
                                            <option value="selesai" <?= ($t['status'] ?? '') == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="7" class="text-center text-muted py-4">Belum ada tugas yang di-assign untuk Anda.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
