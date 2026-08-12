<?php
$title = $title ?? 'Laporkan Kerusakan Alat Baru';
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
                <h4 class="mb-0 fw-bold text-dark fs-5">Buat Laporan Kerusakan Alat Baru</h4>
                <small class="text-muted">Isi rincian kendala alat, lokasi, tingkat kerusakan & teknisi yang ditugaskan.</small>
            </div>
        </div>
    </div>

    <form action="<?= base_url('admin/inventaris/kerusakan/simpan') ?>" method="POST">
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4" style="max-width: 800px; margin: 0 auto;">
            <h6 class="fw-bold text-dark mb-3"><i class="fas fa-edit text-primary me-2"></i> Formulir Kerusakan Alat</h6>
            
            <div class="mb-3">
                <label class="form-label text-xs fw-semibold text-dark">Nama Alat / Peralatan Kantor *</label>
                <input type="text" name="nama_alat" class="form-control rounded-3" placeholder="Contoh: Printer Epson L3210 / AC Ruang Meeting / Proyektor BenQ" required>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12 col-md-6">
                    <label class="form-label text-xs fw-semibold text-dark">Lokasi Keberadaan Alat *</label>
                    <input type="text" name="lokasi_alat" class="form-control rounded-3" placeholder="Contoh: Ruang HRD Lt. 2 / Workshop IT" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label text-xs fw-semibold text-dark">Tingkat Kerusakan *</label>
                    <select name="tingkat_kerusakan" class="form-select rounded-3" required>
                        <option value="ringan">Ringan (Masih bisa digunakan sebagian)</option>
                        <option value="sedang" selected>Sedang (Membutuhkan servis / pergantian sparepart)</option>
                        <option value="berat">Berat (Mati total / tidak bisa dipakai)</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label text-xs fw-semibold text-dark">Deskripsi Kerusakan / Gejala *</label>
                <textarea name="deskripsi_kerusakan" class="form-control rounded-3" rows="3" placeholder="Jelaskan kendala fisik, error, atau gejala yang dialami..." required></textarea>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12 col-md-4">
                    <label class="form-label text-xs fw-semibold text-dark">Teknisi Pengurus / Penanggung Jawab</label>
                    <input type="text" name="teknisi_pengurus" class="form-control rounded-3" placeholder="Contoh: Rian (Teknisi IT) / Service Center Resmi">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label text-xs fw-semibold text-dark">Lokasi Perbaikan</label>
                    <input type="text" name="lokasi_perbaikan" class="form-control rounded-3" placeholder="Contoh: Workshop IT / Service Center Epson">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label text-xs fw-semibold text-dark">Petugas Pembawa / Driver</label>
                    <input type="text" name="petugas_pembawa" class="form-control rounded-3" placeholder="Contoh: Doni (Driver Operational)">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label text-xs fw-semibold text-dark">Catatan / Estimasi Selesai</label>
                <textarea name="catatan_perbaikan" class="form-control rounded-3" rows="2" placeholder="Informasi tambahan perbaikan..."></textarea>
            </div>

            <div class="d-flex justify-content-end gap-2 border-top pt-3">
                <a href="<?= base_url('admin/inventaris/kerusakan') ?>" class="btn btn-outline-secondary rounded-pill px-4">Batal</a>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold"><i class="fas fa-save me-1.5"></i> Simpan Laporan Kerusakan</button>
            </div>
        </div>
    </form>
</div>

<?= view('admin/templates/footer', $templateData) ?>
