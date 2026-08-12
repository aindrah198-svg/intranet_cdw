<?= $this->include('direktur/templates/header') ?>
<?= $this->include('direktur/templates/sidebar') ?>
<?= $this->include('direktur/templates/navbar') ?>

<style>
    /* Styling Premium Modern Material & Glassmorphism (Sama Persis dengan Halaman Tambah Karyawan) */
    .employee-card-modern {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        border-radius: 18px !important;
        box-shadow: 0 8px 25px rgba(30, 60, 114, 0.06), 0 2px 6px rgba(0, 0, 0, 0.02) !important;
    }

    .form-section-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e3c72;
        display: flex;
        align-items: center;
        gap: 8px;
        padding-bottom: 8px;
        border-bottom: 2px solid #f1f5f9;
        margin-bottom: 20px;
    }

    .form-control-custom, .form-select-custom {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 10px 14px;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .form-control-custom:focus, .form-select-custom:focus {
        border-color: #1e3c72;
        box-shadow: 0 0 0 3px rgba(30, 60, 114, 0.12);
    }
</style>

<div class="container-fluid py-3 py-md-4">

    <!-- Header Section Terpadu -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-4 border border-light">
        <div class="d-flex align-items-center">
            <div class="bg-gradient-primary text-white rounded-3 p-2.5 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 46px; height: 46px;">
                <i class="fas fa-comment-medical fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold text-dark">Catat Keluhan Karyawan</h4>
                <small class="text-muted d-none d-sm-inline">Input keluhan yang disampaikan secara langsung kepada Direktur.</small>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('direktur/karyawan/keluhan') ?>" class="btn btn-outline-secondary rounded-pill px-3.5 py-2 shadow-sm d-inline-flex align-items-center text-sm fw-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> <span class="d-none d-md-inline">Kembali ke Daftar</span><span class="d-inline d-md-none">Kembali</span>
            </a>
        </div>
    </div>

    <!-- Form Input Card -->
    <div class="row">
        <div class="col-12">
            <div class="card employee-card-modern p-3 p-sm-4 mb-5">
                <div class="card-body p-2 p-md-3">

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger rounded-3 shadow-sm border-0 mb-4 text-white" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                            <i class="fas fa-exclamation-triangle me-2"></i><?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('direktur/karyawan/keluhan/simpan') ?>" method="post">
                        <?= csrf_field() ?>

                        <!-- Section 1: Information -->
                        <div class="form-section-title">
                            <i class="fas fa-user-edit text-primary"></i> Data Keluhan Karyawan
                        </div>

                        <div class="row g-3 mb-4">
                            <!-- Karyawan -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">Nama Karyawan <span class="text-danger">*</span></label>
                                <select name="karyawan_id" class="form-select form-select-custom" required>
                                    <option value="">-- Pilih Karyawan --</option>
                                    <?php foreach($karyawanList as $k): ?>
                                        <option value="<?= $k['id'] ?>" <?= old('karyawan_id') == $k['id'] ? 'selected' : '' ?>>
                                            <?= esc($k['nama_lengkap']) ?> (<?= esc($k['divisi'] ?: 'Staf') ?> - NIK: <?= esc($k['nik'] ?: '-') ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Kategori -->
                            <div class="col-12 col-md-3">
                                <label class="form-label fw-semibold text-sm">Kategori Keluhan <span class="text-danger">*</span></label>
                                <select name="kategori" class="form-select form-select-custom" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php foreach($kategoriList as $kat): ?>
                                        <option value="<?= esc($kat) ?>" <?= old('kategori') == $kat ? 'selected' : '' ?>><?= esc($kat) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Tanggal -->
                            <div class="col-12 col-md-3">
                                <label class="form-label fw-semibold text-sm">Tanggal Lapor <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control form-control-custom" value="<?= old('tanggal', date('Y-m-d')) ?>" required>
                            </div>

                            <!-- Judul Keluhan -->
                            <div class="col-12">
                                <label class="form-label fw-semibold text-sm">Judul / Pokok Keluhan <span class="text-danger">*</span></label>
                                <input type="text" name="judul" class="form-control form-control-custom" value="<?= old('judul') ?>" required placeholder="Contoh: Permasalahan AC Ruangan Kerja Staf">
                            </div>

                            <!-- Deskripsi -->
                            <div class="col-12">
                                <label class="form-label fw-semibold text-sm">Deskripsi Lengkap Keluhan <span class="text-danger">*</span></label>
                                <textarea name="deskripsi" class="form-control form-control-custom" rows="5" required placeholder="Jelaskan kronologi, pokok kendala, atau poin yang disampaikan karyawan secara detail..."><?= old('deskripsi') ?></textarea>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="pt-3 border-top border-light d-flex justify-content-end align-items-center gap-2">
                            <a href="<?= base_url('direktur/karyawan/keluhan') ?>" class="btn btn-light rounded-pill px-4 py-2 text-sm fw-semibold border">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 text-sm fw-semibold shadow-sm">
                                <i class="fas fa-save me-1.5"></i> Simpan Keluhan
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('direktur/templates/footer') ?>
