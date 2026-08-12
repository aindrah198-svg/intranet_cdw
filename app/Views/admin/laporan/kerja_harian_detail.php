<?php
$data = [
    'title'  => 'Detail Laporan Kerja Harian',
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
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Detail Laporan</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-file-alt text-primary me-2"></i> Detail Laporan Kerja Harian</h4>
            <small class="text-muted">Rincian laporan aktivitas harian yang terkirim ke Direktur/Manajemen.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('admin/laporan/kerja-harian') ?>" class="btn btn-outline-secondary rounded-pill px-3 text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali
            </a>
            <a href="<?= base_url('admin/laporan/kerja-harian/edit/'.$l['id']) ?>" class="btn btn-warning text-white rounded-pill px-3 text-sm font-semibold">
                <i class="fas fa-edit me-1.5"></i> Edit Laporan
            </a>
        </div>
    </div>

    <!-- Main Detail Card -->
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 rounded-4 shadow-sm overflow-hidden mb-4">
                <div class="card-header bg-white py-3 px-4 d-flex align-items-center justify-content-between border-bottom border-light">
                    <h5 class="card-title fs-6 fw-bold mb-0 text-dark"><?= esc($l['judul']) ?></h5>
                    <span class="badge bg-success px-3 py-1.5 rounded-pill text-xs fw-semibold">
                        <?= strtoupper(esc($l['status'] ?? 'Terkirim')) ?>
                    </span>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label class="text-muted text-xs font-semibold uppercase mb-1">Rincian Aktivitas Pekerjaan</label>
                        <div class="p-3 bg-light rounded-3 text-dark text-sm border font-monospace" style="white-space: pre-wrap;">
                            <?= esc($l['deskripsi']) ?>
                        </div>
                    </div>

                    <?php if(!empty($l['komentar_direktur'])): ?>
                    <div class="alert alert-info border-0 rounded-3 p-3 mb-0">
                        <h6 class="fw-bold text-info mb-1"><i class="fas fa-comment-dots me-1.5"></i> Tanggapan / Komentar Direktur:</h6>
                        <p class="mb-0 text-sm"><?= esc($l['komentar_direktur']) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 rounded-4 shadow-sm overflow-hidden mb-4">
                <div class="card-header bg-white py-3 px-4 border-bottom border-light">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-info-circle text-primary me-2"></i> Informasi Pelapor</h6>
                </div>
                <div class="card-body p-4 text-sm">
                    <div class="mb-3">
                        <small class="text-muted text-xs font-semibold uppercase d-block">Nama Karyawan</small>
                        <div class="fw-bold text-dark fs-6 mt-1"><?= esc($l['nama_lengkap'] ?: session()->get('name') ?: 'Admin') ?></div>
                        <div class="text-muted text-xs">NIK: <?= esc($l['nik'] ?: '-') ?> &bull; <?= esc($l['jabatan'] ?: 'Staff Admin') ?></div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted text-xs font-semibold uppercase d-block">Tanggal Pelaporan</small>
                        <div class="fw-semibold text-dark mt-1"><i class="far fa-calendar-alt text-primary me-1"></i><?= date('d F Y', strtotime($l['tanggal'])) ?></div>
                    </div>

                    <hr>

                    <div class="d-grid gap-2">
                        <a href="<?= base_url('admin/laporan/kerja-harian/edit/'.$l['id']) ?>" class="btn btn-warning text-white rounded-pill font-semibold">
                            <i class="fas fa-edit me-1.5"></i> Edit Laporan
                        </a>
                        <a href="<?= base_url('admin/laporan/kerja-harian/delete/'.$l['id']) ?>" class="btn btn-outline-danger rounded-pill font-semibold" onclick="return confirm('Hapus laporan harian ini?')">
                            <i class="fas fa-trash me-1.5"></i> Hapus Laporan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('admin/templates/footer', $data) ?>
