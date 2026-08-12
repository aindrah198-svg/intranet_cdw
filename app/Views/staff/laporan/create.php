<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="fas fa-edit text-primary me-2"></i> Form Laporan Kerja Harian</h4>
                <p class="text-muted mb-0">Submit laporan hasil pelaksanaan tugas harian Anda</p>
            </div>
            <a href="<?= base_url('staff/laporan') ?>" class="btn btn-outline-secondary btn-sm"><i class="fas fa-list me-1"></i> Riwayat Laporan</a>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show"><?= session()->getFlashdata('error') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-custom p-4">
                    <form action="<?= base_url('staff/laporan/store') ?>" method="POST" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label font-semibold">Tanggal Laporan <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-semibold">Judul Aktivitas / Pekerjaan <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control" placeholder="Contoh: Pemeliharaan Server & Troubleshooting Network Branch" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-semibold">Deskripsi Hasil Kerja & Kendala Lapangan <span class="text-danger">*</span></label>
                            <textarea name="deskripsi" class="form-control" rows="5" placeholder="Tuliskan secara ringkas hasil pencapaian kerja hari ini dan kendala jika ada..." required></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label font-semibold">Foto Bukti / Dokumen Lampiran (Opsional)</label>
                            <input type="file" name="lampiran" class="form-control" accept="image/*,.pdf,.doc,.docx">
                            <small class="text-muted">Format yang didukung: JPG, PNG, PDF, DOC (Maks 5MB)</small>
                        </div>

                        <div class="d-flex justify-content-end gap-2 border-top pt-3">
                            <a href="<?= base_url('staff/laporan') ?>" class="btn btn-light px-4">Batal</a>
                            <button type="submit" class="btn btn-primary px-4 fw-bold"><i class="fas fa-paper-plane me-1"></i> Submit Laporan Harian</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
