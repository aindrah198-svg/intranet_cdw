<?= view('admin/templates/header') ?>
<?= view('admin/templates/sidebar') ?>
<?= view('admin/templates/navbar') ?>

<?php
$isDisposisi = (strtolower($surat['status']) === 'disposisi');
$statusClass = $isDisposisi ? 'bg-success bg-opacity-10 text-success border-success' : 'bg-warning bg-opacity-10 text-warning border-warning';
$statusLabel = $isDisposisi ? 'Sudah Didisposisi' : 'Pending Disposisi';
$statusIcon  = $isDisposisi ? 'fas fa-check-circle' : 'fas fa-clock';
?>

<style>
    .detail-card-modern {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        border-radius: 18px !important;
        box-shadow: 0 8px 25px rgba(30, 60, 114, 0.06) !important;
    }

    .data-pill-bar {
        background: #f8fafc;
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 12px;
        padding: 12px 16px;
    }

    .data-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 3px;
    }

    .data-value {
        font-size: 0.95rem;
        font-weight: 600;
        color: #0f172a;
    }
</style>

<div class="container-fluid py-3 py-md-4">

    <!-- Flash Message Notification -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Breadcrumb & Header Bar -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center bg-white rounded-4 shadow-sm p-3.5 p-md-4 mb-4 border border-light gap-3">
        <div class="d-flex align-items-center">
            <a href="<?= base_url('admin/surat/masuk') ?>" class="btn btn-light rounded-circle me-3 d-flex align-items-center justify-content-center p-0" style="width: 42px; height: 42px;">
                <i class="fas fa-arrow-left text-dark"></i>
            </a>
            <div>
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb mb-0 small" style="font-size: 0.78rem;">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>" class="text-decoration-none text-muted">Admin Panel</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/surat/masuk') ?>" class="text-decoration-none text-muted">Surat Masuk</a></li>
                        <li class="breadcrumb-item active fw-bold text-primary" aria-current="page">Detail Surat</li>
                    </ol>
                </nav>
                <h4 class="mb-0 fw-bold text-dark fs-5 fs-md-4">Detail Surat Masuk: <?= esc($surat['no_surat']) ?></h4>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?= base_url('admin/surat/masuk/edit/' . $surat['id']) ?>" class="btn btn-outline-warning rounded-pill px-3.5 py-2 text-sm fw-semibold">
                <i class="fas fa-edit me-1.5"></i> Edit
            </a>
            <button onclick="window.print()" class="btn btn-outline-secondary rounded-pill px-3.5 py-2 text-sm fw-semibold">
                <i class="fas fa-print me-1.5"></i> Cetak
            </button>
        </div>
    </div>

    <!-- Main Content Layout -->
    <div class="row g-4">
        
        <!-- Panel Kiri: Identitas Dokumen & Perihal (8 Kolom) -->
        <div class="col-12 col-lg-8">
            <div class="card detail-card-modern p-4 p-md-5 mb-4">
                
                <!-- Status Badge Banner -->
                <div class="d-flex align-items-center justify-content-between pb-4 mb-4 border-bottom">
                    <div>
                        <span class="badge bg-light text-dark border px-3 py-1.5 rounded-pill font-monospace fw-bold me-2">
                            <i class="fas fa-hashtag me-1"></i><?= esc($surat['no_surat']) ?>
                        </span>
                        <span class="badge <?= $statusClass ?> border rounded-pill px-3 py-1.5 font-semibold">
                            <i class="<?= $statusIcon ?> me-1.5"></i> <?= $statusLabel ?>
                        </span>
                    </div>
                    <small class="text-muted fw-semibold">
                        <i class="fas fa-calendar-alt me-1"></i> Diterima: <?= date('d F Y', strtotime($surat['tanggal_diterima'])) ?>
                    </small>
                </div>

                <!-- Data Identitas -->
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <div class="data-pill-bar">
                            <div class="data-label"><i class="fas fa-building text-primary me-1"></i> Pengirim Surat</div>
                            <div class="data-value"><?= esc($surat['pengirim']) ?></div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="data-pill-bar">
                            <div class="data-label"><i class="fas fa-calendar-check text-info me-1"></i> Tanggal Registrasi</div>
                            <div class="data-value"><?= date('d F Y', strtotime($surat['tanggal_diterima'])) ?></div>
                        </div>
                    </div>
                </div>

                <!-- Perihal / Hal Surat -->
                <div class="mb-4">
                    <h6 class="fw-bold text-dark text-xs text-uppercase mb-2">
                        <i class="fas fa-align-left me-1.5 text-primary"></i> Perihal / Ringkasan Isi Surat
                    </h6>
                    <div class="p-4 bg-light rounded-3 border text-dark fs-6" style="white-space: pre-line; line-height: 1.7;">
                        <?= esc($surat['perihal']) ?>
                    </div>
                </div>

                <!-- File Attachment Preview -->
                <div class="mt-4 pt-4 border-top">
                    <h6 class="fw-bold text-dark text-xs text-uppercase mb-3">
                        <i class="fas fa-paperclip me-1.5 text-danger"></i> Berkas Lampiran Dokumen Surat
                    </h6>
                    <?php if (!empty($surat['file_surat'])): ?>
                        <div class="p-3 bg-light rounded-3 border d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-3">
                                    <i class="fas fa-file-pdf fs-3"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark"><?= esc($surat['file_surat']) ?></div>
                                    <small class="text-muted">Dokumen Resmi Terlampir</small>
                                </div>
                            </div>
                            <a href="<?= base_url('uploads/surat_masuk/' . $surat['file_surat']) ?>" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                <i class="fas fa-download me-1"></i> Buka / Unduh
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-secondary py-3 px-3 rounded-3 text-xs mb-0">
                            <i class="fas fa-info-circle me-1.5"></i> Tidak ada file fisik lampiran PDF yang diunggah saat registrasi.
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>

        <!-- Panel Kanan: Status & Form Disposisi Surat (4 Kolom) -->
        <div class="col-12 col-lg-4">
            <div class="card detail-card-modern p-4 mb-4">
                <h6 class="fw-bold text-dark text-xs text-uppercase mb-3 pb-2 border-bottom">
                    <i class="fas fa-paper-plane me-1.5 text-success"></i> Status & Instruksi Disposisi
                </h6>

                <!-- Current Disposisi Status -->
                <div class="p-3.5 bg-light rounded-3 border mb-4">
                    <small class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size: 0.7rem;">Penerima Disposisi</small>
                    <div class="fw-bold text-dark fs-6 mb-2">
                        <?= !empty($surat['penerima_disposisi']) ? esc($surat['penerima_disposisi']) : '<i class="text-muted small">- Belum Ditentukan -</i>' ?>
                    </div>

                    <small class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size: 0.7rem;">Catatan Disposisi</small>
                    <div class="text-secondary small" style="white-space: pre-line;">
                        <?= !empty($surat['catatan_disposisi']) ? esc($surat['catatan_disposisi']) : '<i class="text-muted small">- Belum ada catatan -</i>' ?>
                    </div>
                </div>

                <!-- Form Update Disposisi langsung dari Halaman Detail -->
                <form action="<?= base_url('admin/surat/masuk/update') ?>" method="POST">
                    <input type="hidden" name="id" value="<?= $surat['id'] ?>">
                    <input type="hidden" name="no_surat" value="<?= esc($surat['no_surat']) ?>">
                    <input type="hidden" name="pengirim" value="<?= esc($surat['pengirim']) ?>">
                    <input type="hidden" name="perihal" value="<?= esc($surat['perihal']) ?>">
                    <input type="hidden" name="tanggal_diterima" value="<?= $surat['tanggal_diterima'] ?>">

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark text-xs text-uppercase">Update Status Disposisi</label>
                        <select name="status" class="form-select rounded-3 text-sm fw-semibold">
                            <option value="pending" <?= strtolower($surat['status']) === 'pending' ? 'selected' : '' ?>>Pending Disposisi</option>
                            <option value="disposisi" <?= strtolower($surat['status']) === 'disposisi' ? 'selected' : '' ?>>Sudah Didisposisi</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark text-xs text-uppercase">Teruskan Ke (Divisi/Karyawan)</label>
                        <input type="text" name="penerima_disposisi" class="form-control rounded-3 text-sm" value="<?= esc($surat['penerima_disposisi'] ?? '') ?>" placeholder="Misal: Divisi Engineering / Direktur">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark text-xs text-uppercase">Catatan Instruksi</label>
                        <textarea name="catatan_disposisi" class="form-control rounded-3 text-sm" rows="3" placeholder="Instruksi tindak lanjut surat..."><?= esc($surat['catatan_disposisi'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-success w-100 rounded-pill py-2.5 fw-bold shadow-sm">
                        <i class="fas fa-check-circle me-1.5"></i> Update Disposisi Surat
                    </button>
                </form>

            </div>
        </div>

    </div>

</div>

<?= view('admin/templates/footer', $data) ?>
