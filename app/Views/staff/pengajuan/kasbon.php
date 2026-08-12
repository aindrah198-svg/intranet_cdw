<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="fas fa-hand-holding-usd text-primary me-2"></i> Form Pengajuan Kasbon</h4>
                <p class="text-muted mb-0">Ajukan pinjaman kasbon perusahaan dengan skema pengembalian terukur</p>
            </div>
            <a href="<?= base_url('staff/pengajuan/riwayat') ?>" class="btn btn-outline-secondary btn-sm"><i class="fas fa-history me-1"></i> Riwayat Pengajuan</a>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show"><?= session()->getFlashdata('error') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-custom p-4">
                    <form action="<?= base_url('staff/pengajuan/kasbon/store') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label font-semibold">Nominal Kasbon (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold">Rp</span>
                                <input type="number" name="nominal" class="form-control" placeholder="Contoh: 1000000" min="50000" step="50000" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-semibold">Keperluan Kasbon <span class="text-danger">*</span></label>
                            <textarea name="keperluan" class="form-control" rows="3" placeholder="Jelaskan alasan mendesak pengajuan kasbon..." required></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label font-semibold">Skema Pengembalian / Potong Gaji <span class="text-danger">*</span></label>
                            <select name="skema_pengembalian" class="form-select" required>
                                <option value="Potong Gaji Bulan Ini (1x)">Potong Gaji Bulan Ini (1x lunas)</option>
                                <option value="Cicilan 2 Bulan">Cicilan Potong Gaji 2x (2 Bulan)</option>
                                <option value="Cicilan 3 Bulan">Cicilan Potong Gaji 3x (3 Bulan)</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-end gap-2 border-top pt-3">
                            <a href="<?= base_url('staff/pengajuan/riwayat') ?>" class="btn btn-light px-4">Batal</a>
                            <button type="submit" class="btn btn-primary px-4 fw-bold"><i class="fas fa-paper-plane me-1"></i> Kirim Pengajuan Kasbon</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
