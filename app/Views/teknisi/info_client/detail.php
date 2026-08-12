<div class="content-wrapper p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-id-card mr-2"></i>Detail Client Project</h4>
            <p class="text-muted mb-0">Informasi lengkap kontak & perusahaan klien</p>
        </div>
        <a href="<?= site_url('teknisi/tugas-proyek/info-client') ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <?php if (empty($client)): ?>
                <div class="alert alert-warning text-center py-4">Data klien tidak ditemukan.</div>
            <?php else: ?>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted font-weight-bold d-block">Nama Contact Person</label>
                        <h5><?= esc($client['nama_klien'] ?? $client['nama_client'] ?? '-') ?></h5>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted font-weight-bold d-block">Nama Perusahaan</label>
                        <h5><?= esc($client['perusahaan'] ?? '-') ?></h5>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted font-weight-bold d-block">Email Kontak</label>
                        <p class="mb-0"><?= esc($client['email'] ?? '-') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted font-weight-bold d-block">Nomor Telepon / HP</label>
                        <p class="mb-0"><?= esc($client['telepon'] ?? '-') ?></p>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="text-muted font-weight-bold d-block">Alamat Lokasi Proyek / Kantor</label>
                        <p class="mb-0"><?= nl2br(esc($client['alamat'] ?? '-')) ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
