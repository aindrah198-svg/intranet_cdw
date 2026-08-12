<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="fas fa-envelope-open-text text-primary me-2"></i> Form Pengajuan Izin</h4>
                <p class="text-muted mb-0">Ajukan izin sakit atau keperluan mendadak (tanpa potong kuota cuti)</p>
            </div>
            <a href="<?= base_url('staff/pengajuan/riwayat') ?>" class="btn btn-outline-secondary btn-sm"><i class="fas fa-history me-1"></i> Riwayat Pengajuan</a>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show"><?= session()->getFlashdata('error') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-custom p-4">
                    <form action="<?= base_url('staff/pengajuan/izin/store') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label font-semibold">Jenis Izin <span class="text-danger">*</span></label>
                            <select name="jenis_izin" class="form-select" required>
                                <option value="Sakit">Izin Sakit (dengan/tanpa surat dokter)</option>
                                <option value="Keperluan Keluarga">Izin Keperluan Keluarga Mendadak</option>
                                <option value="Terlambat Hadir">Izin Datang Terlambat / Pulang Awal</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-semibold">Tanggal Izin <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label font-semibold">Alasan / Penjelasan Izin <span class="text-danger">*</span></label>
                            <textarea name="alasan" class="form-control" rows="4" placeholder="Tuliskan keterangan detail alasan izin..." required></textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2 border-top pt-3">
                            <a href="<?= base_url('staff/pengajuan/riwayat') ?>" class="btn btn-light px-4">Batal</a>
                            <button type="submit" class="btn btn-primary px-4 fw-bold"><i class="fas fa-paper-plane me-1"></i> Kirim Pengajuan Izin</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
