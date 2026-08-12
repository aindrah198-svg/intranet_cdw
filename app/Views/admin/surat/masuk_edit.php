<?= view('admin/templates/header') ?>
<?= view('admin/templates/sidebar') ?>
<?= view('admin/templates/navbar') ?>

<style>
    .form-card-modern {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        border-radius: 18px !important;
        box-shadow: 0 8px 25px rgba(30, 60, 114, 0.06) !important;
    }

    .form-section-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e3c72;
        display: flex;
        align-items: center;
        gap: 8px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f1f5f9;
        margin-bottom: 20px;
    }

    .form-control-custom, .form-select-custom {
        border-radius: 10px;
        border: 1px solid #cbd5e1;
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

    <!-- Breadcrumb & Header Bar -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center bg-white rounded-4 shadow-sm p-3.5 p-md-4 mb-4 border border-light gap-3">
        <div class="d-flex align-items-center">
            <a href="<?= base_url('admin/surat/masuk/detail/' . $surat['id']) ?>" class="btn btn-light rounded-circle me-3 d-flex align-items-center justify-content-center p-0" style="width: 42px; height: 42px;">
                <i class="fas fa-arrow-left text-dark"></i>
            </a>
            <div>
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb mb-0 small" style="font-size: 0.78rem;">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>" class="text-decoration-none text-muted">Admin Panel</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/surat/masuk') ?>" class="text-decoration-none text-muted">Surat Masuk</a></li>
                        <li class="breadcrumb-item active fw-bold text-primary" aria-current="page">Edit Surat</li>
                    </ol>
                </nav>
                <h4 class="mb-0 fw-bold text-dark fs-5 fs-md-4">Edit Surat Masuk: <?= esc($surat['no_surat']) ?></h4>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="card form-card-modern p-4 p-md-5 mb-4">
        <form action="<?= base_url('admin/surat/masuk/update') ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $surat['id'] ?>">

            <!-- Section 1: Informasi Dokumen Surat -->
            <div class="form-section-title">
                <i class="fas fa-edit text-warning"></i> 1. Informasi Dokumen & Registrasi Surat
            </div>

            <div class="row g-3 mb-4">
                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold text-dark text-xs text-uppercase">Nomor Surat</label>
                    <input type="text" name="no_surat" class="form-control form-control-custom fw-bold text-primary" value="<?= esc($surat['no_surat']) ?>" required>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold text-dark text-xs text-uppercase">Tanggal Diterima</label>
                    <input type="date" name="tanggal_diterima" class="form-control form-control-custom" value="<?= esc($surat['tanggal_diterima']) ?>" required>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold text-dark text-xs text-uppercase">Pengirim Surat</label>
                    <input type="text" name="pengirim" class="form-control form-control-custom" value="<?= esc($surat['pengirim']) ?>" required>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold text-dark text-xs text-uppercase">Perihal / Hal Surat</label>
                    <textarea name="perihal" class="form-control form-control-custom" rows="3" required><?= esc($surat['perihal']) ?></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold text-dark text-xs text-uppercase">Upload Ulang Berkas Surat (Opsional)</label>
                    <input type="file" name="file_surat" class="form-control form-control-custom" accept=".pdf,.png,.jpg,.jpeg">
                    <?php if (!empty($surat['file_surat'])): ?>
                        <small class="text-muted d-block mt-1" style="font-size:0.75rem;">
                            <i class="fas fa-file-pdf text-danger me-1"></i> Berkas saat ini: <strong><?= esc($surat['file_surat']) ?></strong>
                        </small>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Section 2: Disposisi Surat -->
            <div class="form-section-title mt-4">
                <i class="fas fa-paper-plane text-success"></i> 2. Status & Disposisi Surat
            </div>

            <div class="row g-3 mb-4">
                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold text-dark text-xs text-uppercase">Status Disposisi</label>
                    <select name="status" class="form-select form-select-custom fw-semibold">
                        <option value="pending" <?= strtolower($surat['status']) === 'pending' ? 'selected' : '' ?>>Pending Disposisi (Belum Diteruskan)</option>
                        <option value="disposisi" <?= strtolower($surat['status']) === 'disposisi' ? 'selected' : '' ?>>Sudah Didisposisikan</option>
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold text-dark text-xs text-uppercase">Penerima Disposisi</label>
                    <input type="text" name="penerima_disposisi" class="form-control form-control-custom" value="<?= esc($surat['penerima_disposisi'] ?? '') ?>" placeholder="Misal: Divisi Operasional / Direktur Utama">
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold text-dark text-xs text-uppercase">Catatan / Instruksi Disposisi</label>
                    <textarea name="catatan_disposisi" class="form-control form-control-custom" rows="2" placeholder="Catatan instruksi disposisi..."><?= esc($surat['catatan_disposisi'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Action Footer Buttons -->
            <div class="d-flex align-items-center justify-content-end gap-2 pt-4 border-top">
                <a href="<?= base_url('admin/surat/masuk/detail/' . $surat['id']) ?>" class="btn btn-light rounded-pill px-4 py-2.5 fw-semibold">Batal</a>
                <button type="submit" class="btn btn-warning rounded-pill px-4 py-2.5 fw-bold shadow-sm">
                    <i class="fas fa-save me-2"></i> Perbarui Surat Masuk
                </button>
            </div>

        </form>
    </div>

</div>

<?= view('admin/templates/footer', $data) ?>
