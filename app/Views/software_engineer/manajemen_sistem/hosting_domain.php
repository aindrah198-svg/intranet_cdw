<?= view('software_engineer/templates/header') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1"><i class="fas fa-hdd text-info me-2"></i> Hosting & Domain Management</h5>
        <small class="text-muted">Tracking tanggal kedaluwarsa hosting, domain, SSL & reminder otomatis H-30</small>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahHosting">
        <i class="fas fa-plus me-1"></i> Catat Hosting/Domain
    </button>
</div>

<?php if (!empty($alerts_h30)): ?>
    <div class="alert alert-warning border-warning shadow-sm mb-4">
        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-bell me-2 text-danger"></i> Perhatian: <?= count($alerts_h30) ?> Layanan Hosting/Domain Memasuki Masa Kedaluwarsa (H-30)</h6>
        <ul class="mb-0 small">
            <?php foreach ($alerts_h30 as $a): ?>
                <li>
                    <strong><?= esc($a['nama_sistem']) ?></strong> (Domain: <code><?= esc($a['nama_domain']) ?></code>) 
                    • Expired Hosting: <span class="badge bg-danger"><?= date('d M Y', strtotime($a['tgl_expired_hosting'])) ?></span>
                    • Expired Domain: <span class="badge bg-warning text-dark"><?= date('d M Y', strtotime($a['tgl_expired_domain'])) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Sistem</th>
                        <th>Provider & Paket</th>
                        <th>Nama Domain</th>
                        <th>Expired Hosting</th>
                        <th>Expired Domain</th>
                        <th>Expired SSL</th>
                        <th>Biaya / Thn</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($hostings)): ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">Belum ada data hosting & domain dicatat.</td></tr>
                    <?php else: ?>
                        <?php foreach ($hostings as $h): ?>
                            <tr>
                                <td class="fw-bold text-primary"><?= esc($h['nama_sistem']) ?></td>
                                <td>
                                    <div class="fw-semibold"><?= esc($h['nama_provider_hosting']) ?></div>
                                    <small class="text-muted"><?= esc($h['paket_hosting']) ?></small>
                                </td>
                                <td class="code-font text-dark"><?= esc($h['nama_domain']) ?></td>
                                <td>
                                    <span class="badge bg-info text-dark"><?= $h['tgl_expired_hosting'] ? date('d M Y', strtotime($h['tgl_expired_hosting'])) : '-' ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?= $h['tgl_expired_domain'] ? date('d M Y', strtotime($h['tgl_expired_domain'])) : '-' ?></span>
                                </td>
                                <td>
                                    <small class="text-muted"><?= $h['tgl_expired_ssl'] ? date('d M Y', strtotime($h['tgl_expired_ssl'])) : '-' ?></small>
                                </td>
                                <td><small class="code-font">Rp <?= number_format($h['biaya_per_tahun'], 0, ',', '.') ?></small></td>
                                <td>
                                    <form action="<?= site_url('software-engineer/manajemen-sistem/hosting-domain/delete/' . $h['id']) ?>" method="POST" onsubmit="return confirm('Hapus catatan hosting/domain ini?');">
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Hosting -->
<div class="modal fade" id="modalTambahHosting" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= site_url('software-engineer/manajemen-sistem/hosting-domain/store') ?>" method="POST" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-1"></i> Catat Hosting & Domain</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Pilih Sistem Target</label>
                    <select name="system_id" class="form-select" required>
                        <?php foreach ($systems as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= esc($s['nama_sistem']) ?> (<?= esc($s['kode_sistem']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Provider Hosting</label>
                        <input type="text" name="nama_provider_hosting" class="form-control" required placeholder="Niagahoster / AWS / VPS">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Nama Domain</label>
                        <input type="text" name="nama_domain" class="form-control code-font" required placeholder="intranet.cdw.co.id">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Expired Hosting</label>
                        <input type="date" name="tgl_expired_hosting" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Expired Domain</label>
                        <input type="date" name="tgl_expired_domain" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Expired SSL</label>
                        <input type="date" name="tgl_expired_ssl" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Paket Hosting</label>
                        <input type="text" name="paket_hosting" class="form-control" placeholder="Cloud VPS Linux 8GB">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Biaya per Tahun (Rp)</label>
                        <input type="number" name="biaya_per_tahun" class="form-control" placeholder="4500000">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Catatan Tambahan</label>
                    <textarea name="catatan" class="form-control" rows="2" placeholder="Catatan perpanjangan atau nama akun..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Hosting & Domain</button>
            </div>
        </form>
    </div>
</div>

<?= view('software_engineer/templates/footer') ?>
