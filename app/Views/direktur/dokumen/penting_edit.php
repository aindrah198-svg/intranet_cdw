<?php
// app/Views/direktur/dokumen/penting_edit.php

$title = $title ?? 'Edit Dokumen Penting';
$templateData = [
    'title' => $title,
    'active' => 'dokumen'
];

echo view('direktur/templates/header', $templateData);
echo view('direktur/templates/sidebar', $templateData);
echo view('direktur/templates/navbar', $templateData);
?>

<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-xs">
                    <li class="breadcrumb-item"><a href="<?= base_url('direktur/dokumen/penting') ?>" class="text-decoration-none text-muted">Dokumen Penting</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Edit Dokumen</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-edit text-warning me-2"></i> Edit Dokumen Penting</h4>
            <small class="text-muted">Perbarui informasi judul, nomor, masa berlaku, atau ganti file dokumen.</small>
        </div>
        <div>
            <a href="<?= base_url('direktur/dokumen/penting') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- Main Card Form -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden mb-4">
                <div class="card-header bg-gradient-primary text-white py-3 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="card-title fs-6 fw-bold mb-0"><i class="fas fa-edit me-2"></i> Edit #<?= esc($d['nomor_dokumen'] ?: 'DOC-00'.$d['id']) ?></h5>
                    <span class="badge bg-white text-primary rounded-pill px-3 py-1 text-xs fw-bold"><?= esc($d['kategori']) ?></span>
                </div>
                <form action="<?= base_url('direktur/dokumen/penting/update') ?>" method="POST" enctype="multipart/form-data" onsubmit="const btn = this.querySelector('button[type=submit]'); if(btn){ btn.disabled = true; btn.innerHTML = '<i class=\'fas fa-spinner fa-spin me-1\'></i> Menyimpan...'; }">
                    <input type="hidden" name="id" value="<?= $d['id'] ?>">
                    <div class="card-body p-4">
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Judul Dokumen *</label>
                            <input type="text" class="form-control rounded-3" name="judul_dokumen" value="<?= esc($d['judul_dokumen']) ?>" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Nomor Dokumen</label>
                                <input type="text" class="form-control rounded-3" name="nomor_dokumen" value="<?= esc($d['nomor_dokumen']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Kategori Dokumen *</label>
                                <select name="kategori" class="form-select rounded-3" required>
                                    <option value="Legalitas" <?= strtolower($d['kategori']) == 'legalitas' ? 'selected' : '' ?>>Legalitas & Izin Usaha</option>
                                    <option value="Kontrak Utama" <?= strtolower($d['kategori']) == 'kontrak utama' ? 'selected' : '' ?>>Kontrak Utama / MoU</option>
                                    <option value="Perpajakan" <?= strtolower($d['kategori']) == 'perpajakan' ? 'selected' : '' ?>>Perpajakan (NPWP/SKT)</option>
                                    <option value="Sertifikasi" <?= strtolower($d['kategori']) == 'sertifikasi' ? 'selected' : '' ?>>Sertifikasi & Kualifikasi</option>
                                    <option value="Lainnya" <?= strtolower($d['kategori']) == 'lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Tanggal Terbit</label>
                                <input type="date" class="form-control rounded-3" name="tanggal_terbit" value="<?= esc($d['tanggal_terbit']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Tanggal Kadaluarsa (Kosongkan jika Permanen)</label>
                                <input type="date" class="form-control rounded-3" name="tanggal_kadaluarsa" value="<?= esc($d['tanggal_kadaluarsa']) ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">File Dokumen</label>
                            <?php if(!empty($d['file_path'])): ?>
                                <div class="mb-2 p-2.5 bg-light rounded-3 border d-flex align-items-center justify-content-between">
                                    <span class="text-xs text-dark fw-semibold"><i class="fas fa-paperclip text-primary me-1.5"></i> <?= esc($d['file_path']) ?></span>
                                    <a href="<?= base_url('uploads/dokumen/'.$d['file_path']) ?>" target="_blank" class="btn btn-xs btn-outline-primary rounded-pill px-3">
                                        <i class="fas fa-download me-1"></i> Unduh File Terkini
                                    </a>
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control rounded-3" name="file_dokumen" accept=".pdf,.png,.jpg,.jpeg">
                            <small class="text-muted text-xs">Pilih file baru jika ingin mengganti berkas yang lama.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Keterangan Singkat</label>
                            <textarea class="form-control rounded-3" name="keterangan" rows="3"><?= esc($d['keterangan']) ?></textarea>
                        </div>

                    </div>
                    <div class="card-footer bg-light px-4 py-3 text-end rounded-bottom-4">
                        <a href="<?= base_url('direktur/dokumen/penting') ?>" class="btn btn-secondary rounded-pill px-4 me-2 font-semibold">Batal</a>
                        <button type="submit" class="btn btn-warning text-white rounded-pill px-4 font-semibold shadow-sm">
                            <i class="fas fa-save me-1.5"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= view('direktur/templates/footer', $templateData) ?>
