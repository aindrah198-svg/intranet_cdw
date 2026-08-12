<?php
$data = [
    'title'  => 'Edit Laporan Kerja Harian',
    'active' => 'laporan-kerja',
    'user'   => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
];

echo view('admin/templates/header', $data);
echo view('admin/templates/sidebar', $data);
echo view('admin/templates/navbar', $data);

$l = $laporan;
?>

<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-xs">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>" class="text-decoration-none text-muted">Laporan & Keluhan</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/laporan/kerja-harian') ?>" class="text-decoration-none text-muted">Laporan Kerja Harian</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Edit Laporan</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-edit text-primary me-2"></i> Edit Laporan Kerja Harian</h4>
            <small class="text-muted">Perbarui isi aktivitas pekerjaan harian Anda.</small>
        </div>
        <div>
            <a href="<?= base_url('admin/laporan/kerja-harian') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card border-0 rounded-4 shadow-sm overflow-hidden mb-4">
        <div class="card-header bg-white py-3 px-4 border-bottom border-light">
            <h5 class="card-title fs-6 fw-bold mb-0 text-dark"><i class="fas fa-edit text-primary me-2"></i> Edit Informasi Laporan</h5>
        </div>
        <div class="card-body p-4">
            <form action="<?= base_url('admin/laporan/kerja-harian/update') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= $l['id'] ?>">

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark text-sm">Tanggal Laporan <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control rounded-3" value="<?= esc($l['tanggal']) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark text-sm">Judul Laporan / Aktivitas <span class="text-danger">*</span></label>
                        <input type="text" name="judul" class="form-control rounded-3" value="<?= esc($l['judul']) ?>" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-dark text-sm">Deskripsi & Rincian Pekerjaan <span class="text-danger">*</span></label>
                        <textarea name="deskripsi" class="form-control rounded-3" rows="6" required><?= esc($l['deskripsi']) ?></textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="<?= base_url('admin/laporan/kerja-harian') ?>" class="btn btn-secondary rounded-pill px-4 font-semibold">Batal</a>
                    <button type="submit" class="btn btn-warning text-white rounded-pill px-4 font-semibold shadow-sm">
                        <i class="fas fa-save me-1.5"></i> Perbarui Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= view('admin/templates/footer', $data) ?>
