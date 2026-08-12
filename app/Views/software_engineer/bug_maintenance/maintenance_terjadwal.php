<?= view('software_engineer/templates/header') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1"><i class="fas fa-tools text-warning me-2"></i> Maintenance Terjadwal & Patch Server</h5>
        <small class="text-muted">Rencana update PHP version, migrasi server, security patch, & downtime window</small>
    </div>
    <button class="btn btn-warning btn-sm text-dark fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahMaintenance">
        <i class="fas fa-plus me-1"></i> Buat Jadwal Maintenance
    </button>
</div>

<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Rencana Waktu</th>
                        <th>Sistem</th>
                        <th>Judul Maintenance</th>
                        <th>Jenis Update</th>
                        <th>Estimasi Downtime</th>
                        <th>Penanggung Jawab</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($schedules)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada jadwal maintenance mendatang.</td></tr>
                    <?php else: ?>
                        <?php foreach ($schedules as $s): ?>
                            <tr>
                                <td class="code-font fw-bold text-dark"><?= date('d M Y H:i', strtotime($s['tgl_rencana'])) ?></td>
                                <td class="fw-bold text-primary"><?= esc($s['nama_sistem']) ?></td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= esc($s['judul_maintenance']) ?></div>
                                    <small class="text-muted"><?= esc($s['catatan']) ?></small>
                                </td>
                                <td><span class="badge bg-secondary"><?= esc($s['jenis_maintenance']) ?></span></td>
                                <td><small class="code-font text-danger fw-semibold"><i class="fas fa-clock me-1"></i> <?= esc($s['estimasi_downtime']) ?></small></td>
                                <td><small class="text-muted"><i class="fas fa-user me-1"></i> <?= esc($s['penanggung_jawab']) ?></small></td>
                                <td>
                                    <?php if ($s['status'] === 'terjadwal'): ?>
                                        <span class="badge bg-warning text-dark">Terjadwal</span>
                                    <?php elseif ($s['status'] === 'proses'): ?>
                                        <span class="badge bg-primary">Sedang Proses</span>
                                    <?php elseif ($s['status'] === 'selesai'): ?>
                                        <span class="badge bg-success">Selesai</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Dibatalkan</span>
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

<!-- Modal Tambah Maintenance -->
<div class="modal fade" id="modalTambahMaintenance" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= site_url('software-engineer/bug-maintenance/maintenance-terjadwal/store') ?>" method="POST" class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold"><i class="fas fa-tools me-1"></i> Jadwal Maintenance Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Sistem Target</label>
                    <select name="system_id" class="form-select" required>
                        <?php foreach ($systems as $sys): ?>
                            <option value="<?= $sys['id'] ?>"><?= esc($sys['nama_sistem']) ?> (<?= esc($sys['kode_sistem']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Judul Maintenance</label>
                    <input type="text" name="judul_maintenance" class="form-control" required placeholder="Contoh: Upgrade PHP 8.2 ke 8.3 & OS Patching">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Jenis Maintenance</label>
                        <input type="text" name="jenis_maintenance" class="form-control" placeholder="Server Migration / DB Optimization">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Estimasi Downtime</label>
                        <input type="text" name="estimasi_downtime" class="form-control" placeholder="30 Menit / 1 Jam">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Rencana Waktu Eksekusi</label>
                        <input type="datetime-local" name="tgl_rencana" class="form-control" required value="<?= date('Y-m-d\TH:i') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Penanggung Jawab</label>
                        <input type="text" name="penanggung_jawab" class="form-control" value="<?= esc(session()->get('name')) ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Catatan / Pemberitahuan Klien</label>
                    <textarea name="catatan" class="form-control" rows="2" placeholder="Informasi dampak maintenance terhadap pengguna..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-warning text-dark fw-bold">Simpan Jadwal</button>
            </div>
        </form>
    </div>
</div>

<?= view('software_engineer/templates/footer') ?>
