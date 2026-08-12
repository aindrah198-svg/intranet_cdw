<div class="main-content" style="margin-left: 250px; padding: 25px;">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1 fw-bold text-dark"><i class="fas fa-umbrella-beach text-warning me-2"></i> Form Pengajuan Cuti Karyawan</h4>
                <p class="text-muted small mb-0">Input formulir permohonan cuti tahunan / cuti khusus karyawan</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4">
                        <form action="<?= base_url('admin/form-pengajuan/cuti/store') ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Pilih Karyawan <span class="text-danger">*</span></label>
                                <select name="karyawan_id" class="form-select" required>
                                    <option value="">-- Pilih Karyawan --</option>
                                    <?php if(!empty($karyawanList)): foreach($karyawanList as $k): ?>
                                        <option value="<?= $k['id'] ?>"><?= esc($k['nik']) ?> - <?= esc($k['nama_lengkap']) ?> (<?= esc($k['divisi']) ?>)</option>
                                    <?php endforeach; endif; ?>
                                </select>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Tanggal Mulai Cuti <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_mulai" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Tanggal Selesai Cuti <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_selesai" class="form-control" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Jenis Cuti <span class="text-danger">*</span></label>
                                <select name="jenis_cuti" class="form-select" required>
                                    <option value="Tahunan">Cuti Tahunan</option>
                                    <option value="Sakit">Cuti Sakit dengan Surat Dokter</option>
                                    <option value="Melahirkan">Cuti Melahirkan / Parental</option>
                                    <option value="Penting">Cuti Alasan Penting</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Alasan / Catatan Cuti <span class="text-danger">*</span></label>
                                <textarea name="alasan" class="form-control" rows="3" placeholder="Tuliskan keterangan/alasan pengajuan cuti..." required></textarea>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-paper-plane me-1"></i> Submit Form Cuti</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
