<?php
$title = $title ?? 'Edit Dokumen Sertifikat';
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
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dokumen/sertifikat') ?>" class="text-decoration-none text-muted">Dokumen Sertifikat</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Edit Sertifikat</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-edit text-warning me-2"></i> Edit Dokumen Sertifikat</h4>
            <small class="text-muted">Perbarui data penerbit, pemegang, masa berlaku, atau ganti berkas sertifikat.</small>
        </div>
        <div>
            <a href="<?= base_url('admin/dokumen/sertifikat') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- Main Card Form -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden mb-4">
                <div class="card-header bg-primary text-white py-3 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="card-title fs-6 fw-bold mb-0"><i class="fas fa-edit me-2"></i> Edit Sertifikat #<?= esc($s['nomor_sertifikat'] ?: 'CERT-00'.$s['id']) ?></h5>
                    <span class="badge bg-white text-primary rounded-pill px-3 py-1 text-xs fw-bold"><?= strtoupper(esc($s['status'])) ?></span>
                </div>
                <form action="<?= base_url('admin/dokumen/sertifikat/update') ?>" method="POST" enctype="multipart/form-data" onsubmit="const btn = this.querySelector('button[type=submit]'); if(btn){ btn.disabled = true; btn.innerHTML = '<i class=\'fas fa-spinner fa-spin me-1\'></i> Menyimpan...'; }">
                    <input type="hidden" name="id" value="<?= $s['id'] ?>">
                    <div class="card-body p-4">
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Nama Sertifikat / Pelatihan *</label>
                            <input type="text" class="form-control rounded-3" name="nama_sertifikat" value="<?= esc($s['nama_sertifikat']) ?>" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Lembaga Penerbit *</label>
                                <input type="text" class="form-control rounded-3" name="penerbit" value="<?= esc($s['penerbit']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Nomor Sertifikat</label>
                                <input type="text" class="form-control rounded-3" name="nomor_sertifikat" value="<?= esc($s['nomor_sertifikat']) ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Pemegang Sertifikat</label>
                            <select name="karyawan_id" class="form-select rounded-3">
                                <option value="">-- Sertifikat Perusahaan (Corporate) --</option>
                                <?php foreach(($karyawan ?? []) as $k): ?>
                                    <option value="<?= $k['id'] ?>" <?= $s['karyawan_id'] == $k['id'] ? 'selected' : '' ?>><?= esc($k['nama_lengkap']) ?> (<?= esc($k['jabatan']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-xs text-dark">Tanggal Perolehan</label>
                                <input type="date" class="form-control rounded-3" name="tanggal_perolehan" value="<?= esc($s['tanggal_perolehan']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-xs text-dark">Masa Berlaku (Kosongkan jika Permanen)</label>
                                <input type="date" class="form-control rounded-3" name="masa_berlaku" value="<?= esc($s['masa_berlaku']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-xs text-dark">Status Sertifikat</label>
                                <select name="status" class="form-select rounded-3">
                                    <option value="aktif" <?= strtolower($s['status']) == 'aktif' ? 'selected' : '' ?>>AKTIF</option>
                                    <option value="proses_perpanjangan" <?= strtolower($s['status']) == 'proses_perpanjangan' ? 'selected' : '' ?>>PROSES PERPANJANGAN</option>
                                    <option value="kadaluarsa" <?= strtolower($s['status']) == 'kadaluarsa' ? 'selected' : '' ?>>KADALUARSA</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">File Sertifikat</label>
                            <?php if(!empty($s['file_path'])): ?>
                                <div class="mb-2 p-2.5 bg-light rounded-3 border d-flex align-items-center justify-content-between">
                                    <span class="text-xs text-dark fw-semibold"><i class="fas fa-paperclip text-primary me-1.5"></i> <?= esc($s['file_path']) ?></span>
                                    <a href="<?= base_url('uploads/sertifikat/'.$s['file_path']) ?>" target="_blank" class="btn btn-xs btn-outline-primary rounded-pill px-3">
                                        <i class="fas fa-download me-1"></i> Unduh File Terkini
                                    </a>
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control rounded-3" name="file_sertifikat" accept=".pdf,.png,.jpg,.jpeg">
                            <small class="text-muted text-xs">Pilih file baru jika ingin mengganti berkas yang lama.</small>
                        </div>

                    </div>
                    <div class="card-footer bg-light px-4 py-3 text-end rounded-bottom-4">
                        <a href="<?= base_url('admin/dokumen/sertifikat') ?>" class="btn btn-secondary rounded-pill px-4 me-2 font-semibold">Batal</a>
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
