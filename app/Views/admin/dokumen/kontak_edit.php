<?php
$title = $title ?? 'Edit Kontak Project';
$data = [
    'title'  => $title,
    'user'   => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin'],
    'active' => 'dokumen'
];

echo view('admin/templates/header', $data);
echo view('admin/templates/sidebar', $data);
echo view('admin/templates/navbar', $data);
?>

<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-xs">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dokumen/kontak') ?>" class="text-decoration-none text-muted">Kontak Project</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Edit Kontak</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-edit text-warning me-2"></i> Edit Kontak Project</h4>
            <small class="text-muted">Perbarui data nama PIC, perusahaan, telepon, email, atau penugasan project.</small>
        </div>
        <div>
            <a href="<?= base_url('admin/dokumen/kontak') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- Main Card Form -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden mb-4">
                <div class="card-header bg-primary text-white py-3 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="card-title fs-6 fw-bold mb-0"><i class="fas fa-edit me-2"></i> Edit Kontak: <?= esc($k['nama_kontak']) ?></h5>
                    <span class="badge bg-white text-primary rounded-pill px-3 py-1 text-xs fw-bold"><?= esc($k['jabatan'] ?: 'PIC') ?></span>
                </div>
                <form action="<?= base_url('admin/dokumen/kontak/update') ?>" method="POST" onsubmit="const btn = this.querySelector('button[type=submit]'); if(btn){ btn.disabled = true; btn.innerHTML = '<i class=\'fas fa-spinner fa-spin me-1\'></i> Menyimpan...'; }">
                    <input type="hidden" name="id" value="<?= $k['id'] ?>">
                    <div class="card-body p-4">
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Project Terkait</label>
                            <select name="project_id" class="form-select rounded-3">
                                <option value="">-- Non-Project / Kontak Umum --</option>
                                <?php foreach(($projects ?? []) as $p): ?>
                                    <option value="<?= $p['id'] ?>" <?= $k['project_id'] == $p['id'] ? 'selected' : '' ?>><?= esc($p['kode_project']) ?> - <?= esc($p['nama_project']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Nama Kontak / PIC *</label>
                            <input type="text" class="form-control rounded-3" name="nama_kontak" value="<?= esc($k['nama_kontak']) ?>" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Perusahaan / Klien</label>
                                <input type="text" class="form-control rounded-3" name="perusahaan_klien" value="<?= esc($k['perusahaan_klien']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Jabatan PIC</label>
                                <input type="text" class="form-control rounded-3" name="jabatan" value="<?= esc($k['jabatan']) ?>">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">No. Telepon / WhatsApp *</label>
                                <input type="text" class="form-control rounded-3" name="telepon" value="<?= esc($k['telepon']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Email Kontak</label>
                                <input type="email" class="form-control rounded-3" name="email" value="<?= esc($k['email']) ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Catatan Khusus</label>
                            <textarea class="form-control rounded-3" name="catatan" rows="3"><?= esc($k['catatan']) ?></textarea>
                        </div>

                    </div>
                    <div class="card-footer bg-light px-4 py-3 text-end rounded-bottom-4">
                        <a href="<?= base_url('admin/dokumen/kontak') ?>" class="btn btn-secondary rounded-pill px-4 me-2 font-semibold">Batal</a>
                        <button type="submit" class="btn btn-warning text-white rounded-pill px-4 font-semibold shadow-sm">
                            <i class="fas fa-save me-1.5"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= view('admin/templates/footer', $data) ?>
