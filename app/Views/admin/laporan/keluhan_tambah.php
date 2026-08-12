<?php
$dataHeader = [
    'title'    => $title ?? 'Laporkan Keluhan Baru',
    'subtitle' => $subtitle ?? 'Form Input Laporan Keluhan Karyawan',
    'active'   => 'laporan-keluhan',
    'user'     => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
];
?>

<?= view('admin/templates/header', $dataHeader) ?>
<?= view('admin/templates/sidebar', $dataHeader) ?>
<?= view('admin/templates/navbar', $dataHeader) ?>

<div class="container-fluid py-3 py-md-4">
    <!-- Header Card -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-4 shadow-sm p-3 p-md-4 mb-4 border border-light flex-wrap gap-3">
        <div class="d-flex align-items-center overflow-hidden">
            <div class="bg-danger text-white rounded-3 p-3 me-3 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 48px; height: 48px; background: linear-gradient(135deg, #ef4444, #dc2626) !important;">
                <i class="fas fa-bullhorn fs-5"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold text-dark" style="font-size: 1.25rem;">Form Pelaporan Keluhan Baru</h4>
                <p class="text-muted mb-0 text-sm">Sampaikan kendala, keluhan operasional, atau masukan kerja karyawan.</p>
            </div>
        </div>
        <div>
            <a href="<?= base_url('admin/laporan/keluhan') ?>" class="btn btn-outline-secondary rounded-pill px-3.5 py-2 text-sm fw-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali ke Daftar Keluhan
            </a>
        </div>
    </div>

    <!-- Alert Flash Notifications -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Form Body Card -->
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-dark text-white border-0 py-3 px-4">
                    <h5 class="card-title fw-bold text-sm mb-0">
                        <i class="fas fa-edit text-warning me-2"></i> Isi Rincian Laporan Keluhan
                    </h5>
                </div>
                <form action="<?= base_url('admin/laporan/keluhan/simpan') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-secondary">Pelapor (Karyawan) <span class="text-danger">*</span></label>
                            <?php if (!empty($userKaryawan)): ?>
                                <input type="text" class="form-control bg-light rounded-3 text-sm fw-bold" value="<?= esc($userKaryawan['nama_lengkap']) ?> (NIK: <?= esc($userKaryawan['nik'] ?? '-') ?> - <?= esc($userKaryawan['jabatan'] ?? 'Staf') ?>)" readonly>
                                <input type="hidden" name="karyawan_id" value="<?= esc($userKaryawan['id']) ?>">
                            <?php else: ?>
                                <select name="karyawan_id" class="form-select rounded-3 text-sm" required>
                                    <option value="">-- Pilih Karyawan Pelapor --</option>
                                    <?php foreach ($karyawanList as $k): ?>
                                        <option value="<?= $k['id'] ?>"><?= esc($k['nama_lengkap']) ?> (NIK: <?= esc($k['nik'] ?? '-') ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-xs text-secondary">Tanggal Keluhan <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control rounded-3 text-sm" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-xs text-secondary">Kategori Keluhan <span class="text-danger">*</span></label>
                                <select name="kategori" class="form-select rounded-3 text-sm" required>
                                    <option value="">-- Pilih Kategori Keluhan --</option>
                                    <?php foreach ($kategoriList as $kat): ?>
                                        <option value="<?= esc($kat) ?>"><?= esc($kat) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-secondary">Judul Keluhan / Subjek Masalah <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control rounded-3 text-sm" placeholder="Contoh: Kendala AC Ruangan atau Keterlambatan Pengadaan ATK" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-secondary">Deskripsi Rinci Keluhan <span class="text-danger">*</span></label>
                            <textarea name="deskripsi" class="form-control rounded-3 text-sm" rows="6" placeholder="Jelaskan kendala, kronologi, atau keluhan secara lengkap dan detail..." required></textarea>
                        </div>
                    </div>

                    <div class="card-footer bg-light border-top py-3 px-4 d-flex justify-content-between align-items-center">
                        <a href="<?= base_url('admin/laporan/keluhan') ?>" class="btn btn-light rounded-pill px-4 text-sm fw-semibold border">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-danger rounded-pill px-4 text-sm fw-bold shadow-sm">
                            <i class="fas fa-paper-plane me-1.5"></i> Kirim Laporan Keluhan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= view('admin/templates/footer', $dataHeader) ?>
