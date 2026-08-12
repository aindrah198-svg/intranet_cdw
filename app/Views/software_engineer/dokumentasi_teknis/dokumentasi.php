<?= view('software_engineer/templates/header') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1"><i class="fas fa-file-alt text-success me-2"></i> Dokumentasi Teknis per Sistem</h5>
        <small class="text-muted">Repository dokumentasi teknis, API specs, setup guide, & versi rilis internal</small>
    </div>
    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahDoc">
        <i class="fas fa-plus me-1"></i> Tambah Dokumen Teknis
    </button>
</div>

<div class="row g-4">
    <?php if (empty($docs)): ?>
        <div class="col-12 text-center text-muted py-5">Belum ada dokumentasi teknis yang dibuat.</div>
    <?php else: ?>
        <?php foreach ($docs as $d): ?>
            <div class="col-md-6">
                <div class="card card-custom h-100">
                    <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-primary"><i class="fas fa-book me-1"></i> <?= esc($d['nama_sistem']) ?></span>
                        <span class="badge bg-light text-dark border code-font">Versi <?= esc($d['versi_doc']) ?></span>
                    </div>
                    <div class="card-body">
                        <h6 class="fw-bold text-dark mb-2"><?= esc($d['judul']) ?></h6>
                        <div class="p-3 bg-light rounded code-font small text-dark mb-3" style="white-space: pre-line; max-height: 200px; overflow-y: auto;">
                            <?= esc($d['content']) ?>
                        </div>
                        <?php if (!empty($d['link_file'])): ?>
                            <a href="<?= esc($d['link_file']) ?>" target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                <i class="fas fa-external-link-alt me-1"></i> Buka Link / File Dokumen
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer bg-transparent border-top text-muted small">
                        <i class="fas fa-user me-1"></i> Updated by <?= esc($d['updated_by']) ?> • <?= date('d M Y', strtotime($d['updated_at'])) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal Tambah Doc -->
<div class="modal fade" id="modalTambahDoc" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= site_url('software-engineer/dokumentasi-teknis/dokumentasi-sistem/store') ?>" method="POST" class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-file-alt me-1"></i> Tambah Dokumentasi Teknis</h5>
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
                        <label class="form-label fw-semibold">Judul Dokumen Teknis</label>
                        <input type="text" name="judul" class="form-control" required placeholder="Contoh: Technical Setup & Deployment Guide">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Versi Doc</label>
                        <input type="text" name="versi_doc" class="form-control code-font" placeholder="1.0">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Link File External (Google Docs / Notion / Confluence)</label>
                    <input type="url" name="link_file" class="form-control" placeholder="https://docs.google.com/document/d/...">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Isi / Ringkasan Dokumentasi Teknis</label>
                    <textarea name="content" class="form-control code-font" rows="5" required placeholder="Tuliskan petunjuk teknis, dependensi library, atau struktur modul..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success">Simpan Dokumen</button>
            </div>
        </form>
    </div>
</div>

<?= view('software_engineer/templates/footer') ?>
