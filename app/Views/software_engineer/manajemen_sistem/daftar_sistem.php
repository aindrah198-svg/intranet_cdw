<?= view('software_engineer/templates/header') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1"><i class="fas fa-globe text-primary me-2"></i> Daftar Sistem & Website (Inventaris Full)</h5>
        <small class="text-muted">Kelola inventaris seluruh aplikasi/sistem internal, eksternal, maupun klien</small>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahSistem">
        <i class="fas fa-plus me-1"></i> Tambah Sistem Baru
    </button>
</div>

<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kode & Nama Sistem</th>
                        <th>Kategori</th>
                        <th>Tech Stack</th>
                        <th>Link Production / Repo</th>
                        <th>PIC SE</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($systems)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada sistem terdaftar.</td></tr>
                    <?php else: ?>
                        <?php foreach ($systems as $sys): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark"><?= esc($sys['nama_sistem']) ?></div>
                                    <span class="badge bg-light text-dark border code-font"><?= esc($sys['kode_sistem']) ?></span>
                                </td>
                                <td>
                                    <?php if ($sys['jenis'] === 'internal'): ?>
                                        <span class="badge bg-primary">Internal CDW</span>
                                    <?php elseif ($sys['jenis'] === 'eksternal'): ?>
                                        <span class="badge bg-info text-dark">Eksternal (Milik)</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Sistem Klien</span>
                                    <?php endif; ?>
                                </td>
                                <td><small class="code-font text-dark"><?= esc($sys['tech_stack']) ?></small></td>
                                <td>
                                    <?php if (!empty($sys['link_production'])): ?>
                                        <a href="<?= esc($sys['link_production']) ?>" target="_blank" class="btn btn-xs btn-outline-primary py-0 px-2 text-decoration-none me-1" style="font-size: 0.75rem;">
                                            <i class="fas fa-external-link-alt me-1"></i> Prod
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($sys['link_repository'])): ?>
                                        <a href="<?= esc($sys['link_repository']) ?>" target="_blank" class="btn btn-xs btn-outline-dark py-0 px-2 text-decoration-none" style="font-size: 0.75rem;">
                                            <i class="fab fa-github me-1"></i> Repo
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td><small class="text-muted"><i class="fas fa-user-cog me-1"></i> <?= esc($sys['pic_internal'] ?: 'SE Team') ?></small></td>
                                <td>
                                    <?php if ($sys['status'] == 'aktif'): ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php elseif ($sys['status'] == 'maintenance'): ?>
                                        <span class="badge bg-warning text-dark">Maintenance</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form action="<?= site_url('software-engineer/manajemen-sistem/daftar-sistem/delete/' . $sys['id']) ?>" method="POST" onsubmit="return confirm('Hapus sistem ini dari inventaris?');">
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

<!-- Modal Tambah Sistem -->
<div class="modal fade" id="modalTambahSistem" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= site_url('software-engineer/manajemen-sistem/daftar-sistem/store') ?>" method="POST" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-1"></i> Tambah Sistem / Website</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label fw-semibold">Nama Sistem</label>
                        <input type="text" name="nama_sistem" class="form-control" required placeholder="Contoh: ERP CDW / Catpedia.id">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Kode Unik</label>
                        <input type="text" name="kode_sistem" class="form-control code-font" required placeholder="ERP-CDW">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Jenis / Kategori</label>
                        <select name="jenis" class="form-select">
                            <option value="internal">Internal CDW</option>
                            <option value="eksternal">Eksternal (Milik Sendiri)</option>
                            <option value="klien">Sistem Klien</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Status Operational</label>
                        <select name="status" class="form-select">
                            <option value="aktif">Aktif</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tech Stack</label>
                    <input type="text" name="tech_stack" class="form-control code-font" placeholder="Contoh: PHP 8.2, CI4, MySQL, Bootstrap 5">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Link Production URL</label>
                    <input type="url" name="link_production" class="form-control" placeholder="https://intranet.cdw.co.id">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Link Repository (GitHub/GitLab)</label>
                    <input type="url" name="link_repository" class="form-control" placeholder="https://github.com/org/repo">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Deskripsi Sistem</label>
                    <textarea name="deskripsi" class="form-control" rows="3" placeholder="Fungsi utama dan arsitektur sistem..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Ke Inventaris</button>
            </div>
        </form>
    </div>
</div>

<?= view('software_engineer/templates/footer') ?>
