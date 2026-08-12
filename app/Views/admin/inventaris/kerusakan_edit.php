<?php
$title = $title ?? 'Edit Laporan Kerusakan Alat';
$templateData = [
    'title'  => $title,
    'user'   => session()->get('user') ?? ['name' => session()->get('name') ?? 'Administrator', 'role' => 'admin'],
    'active' => 'inventaris'
];
?>

<?= view('admin/templates/header', $templateData) ?>
<?= view('admin/templates/sidebar', $templateData) ?>
<?= view('admin/templates/navbar', $templateData) ?>

<div class="container-fluid py-3 py-md-4">
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-4 border border-light flex-wrap gap-3">
        <div class="d-flex align-items-center">
            <a href="<?= base_url('admin/inventaris/kerusakan') ?>" class="btn btn-outline-secondary rounded-pill me-3 px-3 py-1.5 text-xs fw-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali
            </a>
            <div>
                <h4 class="mb-0 fw-bold text-dark fs-5">Edit Laporan Kerusakan Alat #<?= esc($k['kode_laporan']) ?></h4>
                <small class="text-muted">Perbarui status tindakan perbaikan, teknisi, atau progres penanganan.</small>
            </div>
        </div>
    </div>

    <form action="<?= base_url('admin/inventaris/kerusakan/update') ?>" method="POST">
        <input type="hidden" name="id" value="<?= esc($k['id']) ?>">

        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4" style="max-width: 800px; margin: 0 auto;">
            <h6 class="fw-bold text-dark mb-3"><i class="fas fa-edit text-primary me-2"></i> Perbarui Data Laporan Kerusakan</h6>
            
            <div class="mb-3">
                <label class="form-label text-xs fw-semibold text-dark">Nama Alat / Peralatan Kantor *</label>
                <input type="text" name="nama_alat" class="form-control rounded-3" value="<?= esc($k['nama_alat']) ?>" required>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12 col-md-6">
                    <label class="form-label text-xs fw-semibold text-dark">Lokasi Keberadaan Alat *</label>
                    <input type="text" name="lokasi_alat" class="form-control rounded-3" value="<?= esc($k['lokasi_alat']) ?>" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label text-xs fw-semibold text-dark">Tingkat Kerusakan *</label>
                    <select name="tingkat_kerusakan" class="form-select rounded-3" required>
                        <option value="ringan" <?= $k['tingkat_kerusakan']==='ringan'?'selected':'' ?>>Ringan</option>
                        <option value="sedang" <?= $k['tingkat_kerusakan']==='sedang'?'selected':'' ?>>Sedang</option>
                        <option value="berat" <?= $k['tingkat_kerusakan']==='berat'?'selected':'' ?>>Berat</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label text-xs fw-semibold text-dark">Deskripsi Kerusakan / Gejala</label>
                <textarea name="deskripsi_kerusakan" class="form-control rounded-3" rows="3"><?= esc($k['deskripsi_kerusakan']) ?></textarea>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12 col-md-4">
                    <label class="form-label text-xs fw-semibold text-dark">Teknisi Pengurus</label>
                    <input type="text" name="teknisi_pengurus" class="form-control rounded-3" value="<?= esc($k['teknisi_pengurus']) ?>">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label text-xs fw-semibold text-dark">Lokasi Perbaikan</label>
                    <input type="text" name="lokasi_perbaikan" class="form-control rounded-3" value="<?= esc($k['lokasi_perbaikan']) ?>">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label text-xs fw-semibold text-dark">Petugas Pembawa</label>
                    <input type="text" name="petugas_pembawa" class="form-control rounded-3" value="<?= esc($k['petugas_pembawa']) ?>">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label text-xs fw-semibold text-dark">Status Tindakan Perbaikan</label>
                <select name="status_tindakan" class="form-select rounded-3">
                    <option value="dilaporkan" <?= $k['status_tindakan']==='dilaporkan'?'selected':'' ?>>Dilaporkan (Menunggu Tindakan)</option>
                    <option value="dalam_perbaikan" <?= $k['status_tindakan']==='dalam_perbaikan'?'selected':'' ?>>Dalam Perbaikan / Servis</option>
                    <option value="selesai" <?= $k['status_tindakan']==='selesai'?'selected':'' ?>>Selesai (Sudah Diperbaiki)</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label text-xs fw-semibold text-dark">Catatan Perbaikan / Hasil Servis</label>
                <textarea name="catatan_perbaikan" class="form-control rounded-3" rows="3"><?= esc($k['catatan_perbaikan']) ?></textarea>
            </div>

            <div class="d-flex justify-content-end gap-2 border-top pt-3">
                <a href="<?= base_url('admin/inventaris/kerusakan') ?>" class="btn btn-outline-secondary rounded-pill px-4">Batal</a>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold"><i class="fas fa-save me-1.5"></i> Simpan Perubahan</button>
            </div>
        </div>
    </form>
</div>

<?= view('admin/templates/footer', $templateData) ?>
