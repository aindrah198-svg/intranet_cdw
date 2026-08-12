<?php
$data = [
    'title'    => 'Edit Keluhan Saya',
    'subtitle' => 'Perbarui Data Keluhan & Aspirasi Admin',
    'active'   => 'keluhan-saya',
    'user'     => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
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
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/keluhan-saya') ?>" class="text-decoration-none text-muted">Keluhan Saya</a></li>
                    <li class="breadcrumb-item active text-danger fw-bold" aria-current="page">Edit Keluhan</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-edit text-warning me-2"></i> Edit Keluhan Saya</h4>
            <small class="text-muted">Perbarui isi kategori, judul, atau rincian keluhan sebelum ditanggapi manajemen.</small>
        </div>
        <div>
            <a href="<?= base_url('admin/keluhan-saya') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Batal / Kembali
            </a>
        </div>
    </div>

    <!-- Main Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden mb-4">
                <div class="card-header text-white py-3 px-4" style="background: linear-gradient(135deg, #d32f2f, #b71c1c);">
                    <h5 class="card-title fs-6 fw-bold mb-0"><i class="fas fa-pencil-alt me-2"></i> Form Perubahan Keluhan</h5>
                </div>
                <form action="<?= base_url('admin/keluhan-saya/update') ?>" method="POST">
                    <input type="hidden" name="id" value="<?= $k['id'] ?>">

                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Kategori Keluhan *</label>
                            <select name="kategori" class="form-select rounded-3" required>
                                <option value="Fasilitas" <?= ($k['kategori']=='Fasilitas')?'selected':'' ?>>Fasilitas / Perangkat Kerja</option>
                                <option value="Lingkungan Kerja" <?= ($k['kategori']=='Lingkungan Kerja')?'selected':'' ?>>Lingkungan Kerja / Kenyamanan</option>
                                <option value="Beban Kerja" <?= ($k['kategori']=='Beban Kerja')?'selected':'' ?>>Beban Kerja / Penugasan</option>
                                <option value="Hubungan Rekan Kerja" <?= ($k['kategori']=='Hubungan Rekan Kerja')?'selected':'' ?>>Hubungan Rekan Kerja</option>
                                <option value="Lainnya" <?= ($k['kategori']=='Lainnya')?'selected':'' ?>>Lainnya</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Judul Keluhan / Kendala *</label>
                            <input type="text" name="judul" class="form-control rounded-3" value="<?= esc($k['judul']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Rincian Deskripsi Keluhan *</label>
                            <textarea name="deskripsi" class="form-control rounded-3" rows="4" required><?= esc($k['deskripsi']) ?></textarea>
                        </div>
                    </div>
                    <div class="card-footer bg-light px-4 py-3 text-end rounded-bottom-4">
                        <a href="<?= base_url('admin/keluhan-saya') ?>" class="btn btn-secondary rounded-pill px-4 me-2 font-semibold">Batal</a>
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
