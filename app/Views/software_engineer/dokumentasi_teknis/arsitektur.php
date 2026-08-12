<?= view('software_engineer/templates/header') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1"><i class="fas fa-project-diagram text-primary me-2"></i> Diagram & Catatan Arsitektur Sistem</h5>
        <small class="text-muted">Desain skema database, data flow diagram (DFD), arsitektur mikro/monolit, & struktur server</small>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahArsitektur">
        <i class="fas fa-plus me-1"></i> Catat Arsitektur Baru
    </button>
</div>

<div class="row g-4">
    <?php if (empty($docs)): ?>
        <div class="col-12 text-center text-muted py-5">Belum ada catatan arsitektur sistem.</div>
    <?php else: ?>
        <?php foreach ($docs as $d): ?>
            <div class="col-md-6">
                <div class="card card-custom h-100 border-start border-primary border-4">
                    <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark"><i class="fas fa-sitemap me-1 text-primary"></i> <?= esc($d['nama_sistem']) ?></span>
                        <span class="badge bg-primary text-white code-font">Rev <?= esc($d['versi_doc']) ?></span>
                    </div>
                    <div class="card-body">
                        <h6 class="fw-bold text-primary mb-2"><?= esc($d['judul']) ?></h6>
                        <div class="p-3 bg-dark text-light rounded code-font small mb-3" style="white-space: pre-line; max-height: 250px; overflow-y: auto;">
                            <?= esc($d['content']) ?>
                        </div>
                        <?php if (!empty($d['link_file'])): ?>
                            <a href="<?= esc($d['link_file']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-diagram-project me-1"></i> Buka Diagram ERD / Figma / Whimsical
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer bg-transparent border-top text-muted small">
                        <i class="fas fa-user-edit me-1"></i> Updated by <?= esc($d['updated_by']) ?> • <?= date('d M Y', strtotime($d['updated_at'])) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal Tambah Arsitektur -->
<div class="modal fade" id="modalTambahArsitektur" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= site_url('software-engineer/dokumentasi-teknis/arsitektur-sistem/store') ?>" method="POST" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-sitemap me-1"></i> Catat Arsitektur / ERD</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label fw-semibold">Judul Arsitektur / Skema</label>
                        <input type="text" name="judul" class="form-control" required placeholder="Contoh: Database ERD & Microservice Topology">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Versi Rev</label>
                        <input type="text" name="versi_doc" class="form-control code-font" placeholder="1.0">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Link Diagram (Figma / dbdiagram.io / Eraser)</label>
                    <input type="url" name="link_file" class="form-control" placeholder="https://dbdiagram.io/d/...">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Catatan Arsitektur & Relasi Database</label>
                    <textarea name="content" class="form-control code-font" rows="5" required placeholder="Jelaskan alur data, tabel utama, relasi FK, atau caching layer..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Arsitektur</button>
            </div>
        </form>
    </div>
</div>

<?= view('software_engineer/templates/footer') ?>
