<?php
// app/Views/direktur/dokumen/penting_tambah.php

$title = $title ?? 'Upload Dokumen Penting Baru';
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
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Upload Dokumen</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-file-upload text-primary me-2"></i> Upload Dokumen Penting Baru</h4>
            <small class="text-muted">Isi rincian berkas legalitas, izin usaha, perpajakan, atau dokumen penting perseroan.</small>
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
                <div class="card-header bg-gradient-primary text-white py-3 px-4">
                    <h5 class="card-title fs-6 fw-bold mb-0"><i class="fas fa-file-contract me-2"></i> Form Upload Dokumen Resmi</h5>
                </div>
                <form action="<?= base_url('direktur/dokumen/penting/simpan') ?>" method="POST" enctype="multipart/form-data" onsubmit="const btn = this.querySelector('button[type=submit]'); if(btn){ btn.disabled = true; btn.innerHTML = '<i class=\'fas fa-spinner fa-spin me-1\'></i> Mengunggah...'; }">
                    <div class="card-body p-4">
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Judul Dokumen *</label>
                            <input type="text" class="form-control rounded-3" name="judul_dokumen" required placeholder="Cth: Akta Pendirian Perusahaan, NIB OSS, SIUP">
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Nomor Dokumen</label>
                                <input type="text" class="form-control rounded-3" name="nomor_dokumen" placeholder="Cth: AHU-0012345/2026">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Kategori Dokumen *</label>
                                <select name="kategori" class="form-select rounded-3" required>
                                    <option value="Legalitas" selected>Legalitas & Izin Usaha</option>
                                    <option value="Kontrak Utama">Kontrak Utama / MoU</option>
                                    <option value="Perpajakan">Perpajakan (NPWP/SKT)</option>
                                    <option value="Sertifikasi">Sertifikasi & Kualifikasi</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Tanggal Terbit</label>
                                <input type="date" class="form-control rounded-3" name="tanggal_terbit">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Tanggal Kadaluarsa (Kosongkan jika Permanen)</label>
                                <input type="date" class="form-control rounded-3" name="tanggal_kadaluarsa">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">File Dokumen (PDF / Gambar) *</label>
                            <input type="file" class="form-control rounded-3" name="file_dokumen" required accept=".pdf,.png,.jpg,.jpeg">
                            <small class="text-muted text-xs">Format berkas didukung: PDF, PNG, JPG (Maksimal 5MB)</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Keterangan Singkat</label>
                            <textarea class="form-control rounded-3" name="keterangan" rows="3" placeholder="Catatan mengenai isi berkas, notaris, atau instansi penerbit..."></textarea>
                        </div>

                    </div>
                    <div class="card-footer bg-light px-4 py-3 text-end rounded-bottom-4">
                        <a href="<?= base_url('direktur/dokumen/penting') ?>" class="btn btn-secondary rounded-pill px-4 me-2 font-semibold">Batal</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 font-semibold shadow-sm">
                            <i class="fas fa-save me-1.5"></i> Simpan Dokumen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= view('direktur/templates/footer', $templateData) ?>
