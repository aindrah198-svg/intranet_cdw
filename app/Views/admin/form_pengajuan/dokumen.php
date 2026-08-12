<div class="main-content" style="margin-left: 250px; padding: 25px;">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1 fw-bold text-dark"><i class="fas fa-id-card text-success me-2"></i> Form Permintaan Dokumen Kerja</h4>
                <p class="text-muted small mb-0">Input permohonan penerbitan surat keterangan kerja, rekomendasi, atau sertifikat untuk karyawan</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4">
                        <form action="<?= base_url('admin/form-pengajuan/dokumen/store') ?>" method="post">
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

                            <div class="mb-3">
                                <label class="form-label fw-bold">Jenis Dokumen Yang Diminta <span class="text-danger">*</span></label>
                                <select name="jenis_dokumen" class="form-select" required>
                                    <option value="Surat Keterangan Kerja">Surat Keterangan Kerja (Paket Bank/Visa)</option>
                                    <option value="Surat Rekomendasi">Surat Rekomendasi</option>
                                    <option value="Slip Gaji Perorangan">Slip Gaji Resmi Stamp HRD</option>
                                    <option value="Sertifikat Pengalaman Kerja">Sertifikat Pengalaman Kerja (Parkir Resign)</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Tujuan / Keperluan Dokumen <span class="text-danger">*</span></label>
                                <textarea name="keperluan" class="form-control" rows="3" placeholder="Contoh: Pengajuan KPR Bank / Keperluan pembuatan paspor..." required></textarea>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-paper-plane me-1"></i> Submit Permintaan Dokumen</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
