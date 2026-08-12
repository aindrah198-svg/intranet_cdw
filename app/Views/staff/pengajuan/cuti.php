<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="fas fa-calendar-minus text-primary me-2"></i> Form Pengajuan Cuti</h4>
                <p class="text-muted mb-0">Ajukan cuti tahunan (kuota kepotong otomatis)</p>
            </div>
            <a href="<?= base_url('staff/pengajuan/riwayat') ?>" class="btn btn-outline-secondary btn-sm"><i class="fas fa-history me-1"></i> Riwayat Pengajuan</a>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show"><?= session()->getFlashdata('error') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Info Kuota -->
                <div class="card card-custom p-3 mb-4 bg-info bg-opacity-10 border-info">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle fa-2x text-info me-3"></i>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">Sisa Kuota Cuti Anda Tahun Ini: <span class="text-info"><?= esc($sisaCuti) ?> Hari</span></h6>
                            <small class="text-muted">Pengajuan cuti tahunan akan memotong sisa kuota ini secara otomatis setelah disetujui HRD.</small>
                        </div>
                    </div>
                </div>

                <div class="card card-custom p-4">
                    <form action="<?= base_url('staff/pengajuan/cuti/store') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label font-semibold">Jenis Cuti <span class="text-danger">*</span></label>
                            <select name="jenis_cuti" class="form-select" required>
                                <option value="Tahunan">Cuti Tahunan</option>
                                <option value="Hamil">Cuti Melahirkan / Hamil</option>
                                <option value="Khusus">Cuti Khusus (Pernikahan, Duka, dll)</option>
                            </select>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label font-semibold">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_mulai" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-semibold">Tanggal Selesai <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_selesai" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label font-semibold">Alasan Pengajuan Cuti <span class="text-danger">*</span></label>
                            <textarea name="alasan" class="form-control" rows="4" placeholder="Jelaskan alasan atau keperluan pengajuan cuti..." required></textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2 border-top pt-3">
                            <a href="<?= base_url('staff/pengajuan/riwayat') ?>" class="btn btn-light px-4">Batal</a>
                            <button type="submit" class="btn btn-primary px-4 fw-bold"><i class="fas fa-paper-plane me-1"></i> Kirim Pengajuan Cuti</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
