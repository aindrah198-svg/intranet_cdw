<?= view('software_engineer/templates/header') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1"><i class="fas fa-spider text-danger me-2"></i> Bug Tracking & Issue Management</h5>
        <small class="text-muted">Pelacakan bug per sistem: Open → In Progress → Fixed → Verified</small>
    </div>
    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahBug">
        <i class="fas fa-plus me-1"></i> Laporkan Bug Baru
    </button>
</div>

<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Sistem</th>
                        <th>Judul Bug</th>
                        <th>Severity</th>
                        <th>Status Flow</th>
                        <th>Reporter</th>
                        <th>Tanggal Ditemukan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bugs)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada bug open atau terdaftar.</td></tr>
                    <?php else: ?>
                        <?php foreach ($bugs as $b): ?>
                            <tr>
                                <td class="fw-bold text-primary"><?= esc($b['nama_sistem']) ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= esc($b['judul_bug']) ?></div>
                                    <small class="text-muted d-block text-truncate" style="max-width: 300px;"><?= esc($b['deskripsi']) ?></small>
                                </td>
                                <td>
                                    <?php
                                    $sevBadge = 'bg-secondary';
                                    if ($b['severity'] === 'critical') $sevBadge = 'bg-danger text-white fw-bold';
                                    if ($b['severity'] === 'high') $sevBadge = 'bg-warning text-dark';
                                    if ($b['severity'] === 'medium') $sevBadge = 'bg-info text-dark';
                                    ?>
                                    <span class="badge <?= $sevBadge ?>"><?= esc(strtoupper($b['severity'])) ?></span>
                                </td>
                                <td>
                                    <?php
                                    $stBadge = 'bg-secondary';
                                    if ($b['status'] === 'in_progress') $stBadge = 'bg-primary';
                                    if ($b['status'] === 'fixed') $stBadge = 'bg-info text-dark';
                                    if ($b['status'] === 'verified') $stBadge = 'bg-success';
                                    ?>
                                    <span class="badge <?= $stBadge ?>"><?= esc(strtoupper(str_replace('_', ' ', $b['status']))) ?></span>
                                </td>
                                <td><small class="text-muted"><i class="fas fa-user me-1"></i> <?= esc($b['reporter']) ?></small></td>
                                <td><small class="code-font"><?= date('d M Y', strtotime($b['tgl_ditemukan'])) ?></small></td>
                                <td>
                                    <!-- Status Update Dropdown -->
                                    <button class="btn btn-xs btn-outline-secondary dropdown-toggle py-0 px-2" style="font-size: 0.75rem;" data-bs-toggle="dropdown">
                                        Update Status
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end small">
                                        <li>
                                            <form action="<?= site_url('software-engineer/bug-maintenance/bug-tracking/update-status/' . $b['id']) ?>" method="POST">
                                                <input type="hidden" name="status" value="in_progress">
                                                <button type="submit" class="dropdown-item">Set In Progress</button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="<?= site_url('software-engineer/bug-maintenance/bug-tracking/update-status/' . $b['id']) ?>" method="POST">
                                                <input type="hidden" name="status" value="fixed">
                                                <button type="submit" class="dropdown-item">Set Fixed</button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="<?= site_url('software-engineer/bug-maintenance/bug-tracking/update-status/' . $b['id']) ?>" method="POST">
                                                <input type="hidden" name="status" value="verified">
                                                <button type="submit" class="dropdown-item">Set Verified</button>
                                            </form>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Bug -->
<div class="modal fade" id="modalTambahBug" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= site_url('software-engineer/bug-maintenance/bug-tracking/store') ?>" method="POST" class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-bug me-1"></i> Laporkan Bug Report</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Sistem Yang Mengalami Bug</label>
                    <select name="system_id" class="form-select" required>
                        <?php foreach ($systems as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= esc($s['nama_sistem']) ?> (<?= esc($s['kode_sistem']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Judul Bug / Isu</label>
                    <input type="text" name="judul_bug" class="form-control" required placeholder="Contoh: Error 500 saat upload lampiran PDF">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Tingkat Keparahan (Severity)</label>
                        <select name="severity" class="form-select">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="critical">Critical (Down)</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Tanggal Ditemukan</label>
                        <input type="date" name="tgl_ditemukan" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Deskripsi Error & Steps to Reproduce</label>
                    <textarea name="deskripsi" class="form-control" rows="3" required placeholder="Langkah mereproduksi error dan pesan error log..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger">Simpan Bug Report</button>
            </div>
        </form>
    </div>
</div>

<?= view('software_engineer/templates/footer') ?>
