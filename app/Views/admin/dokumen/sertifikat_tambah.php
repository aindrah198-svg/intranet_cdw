<?php
$title = $title ?? 'Tambah Sertifikat Baru';
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
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Tambah Sertifikat</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-plus-circle text-primary me-2"></i> Tambah Dokumen Sertifikat Baru</h4>
            <small class="text-muted">Isi rincian sertifikasi ISO, Lisensi K3, atau sertifikat keahlian karyawan.</small>
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
                <div class="card-header bg-primary text-white py-3 px-4">
                    <h5 class="card-title fs-6 fw-bold mb-0"><i class="fas fa-award me-2"></i> Form Sertifikasi & Kualifikasi</h5>
                </div>
                <form action="<?= base_url('admin/dokumen/sertifikat/simpan') ?>" method="POST" enctype="multipart/form-data" onsubmit="const btn = this.querySelector('button[type=submit]'); if(btn){ btn.disabled = true; btn.innerHTML = '<i class=\'fas fa-spinner fa-spin me-1\'></i> Menyimpan...'; }">
                    <div class="card-body p-4">
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Nama Sertifikat / Pelatihan *</label>
                            <input type="text" class="form-control rounded-3" name="nama_sertifikat" required placeholder="Cth: ISO 9001:2015, Sertifikasi Ahli K3 Umum, Cisco CCNA">
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Lembaga Penerbit *</label>
                                <input type="text" class="form-control rounded-3" name="penerbit" required placeholder="Cth: SUCOFINDO, BNSP, Kemnaker">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Nomor Sertifikat</label>
                                <input type="text" class="form-control rounded-3" name="nomor_sertifikat" placeholder="Cth: ISO-9001-CDW-2024">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Pemegang Sertifikat (Kosongkan jika Sertifikat Perusahaan)</label>
                            <select name="karyawan_id" class="form-select rounded-3">
                                <option value="">-- Sertifikat Perusahaan (Corporate) --</option>
                                <?php foreach(($karyawan ?? []) as $k): ?>
                                    <option value="<?= $k['id'] ?>"><?= esc($k['nama_lengkap']) ?> (<?= esc($k['jabatan']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Tanggal Perolehan</label>
                                <input type="date" class="form-control rounded-3" name="tanggal_perolehan">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Masa Berlaku (Kosongkan jika Permanen)</label>
                                <input type="date" class="form-control rounded-3" name="masa_berlaku">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Upload File Sertifikat (PDF / Gambar)</label>
                            <input type="file" class="form-control rounded-3" name="file_sertifikat" accept=".pdf,.png,.jpg,.jpeg">
                            <small class="text-muted text-xs">Format berkas didukung: PDF, PNG, JPG (Maksimal 5MB)</small>
                        </div>

                    </div>
                    <div class="card-footer bg-light px-4 py-3 text-end rounded-bottom-4">
                        <a href="<?= base_url('admin/dokumen/sertifikat') ?>" class="btn btn-secondary rounded-pill px-4 me-2 font-semibold">Batal</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 font-semibold shadow-sm">
                            <i class="fas fa-save me-1.5"></i> Simpan Sertifikat
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= view('admin/templates/footer', $data) ?>
