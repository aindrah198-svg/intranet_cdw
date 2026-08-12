<?php
$data = [
    'title'    => $title ?? 'Edit Kunjungan Tamu',
    'subtitle' => 'Perbarui Informasi Tamu Kantor CDW Engineering',
    'active'   => 'buku-tamu',
    'user'     => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
];

echo view('admin/templates/header', $data);
echo view('admin/templates/sidebar', $data);
echo view('admin/templates/navbar', $data);
?>

<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-xs">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/fasilitas/buku-tamu') ?>" class="text-decoration-none text-muted">Buku Tamu</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Edit Tamu</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-user-edit text-warning me-2"></i> Edit Data Kunjungan Tamu</h4>
            <small class="text-muted">Perbarui data nama tamu, instansi, jam kedatangan, atau status kunjungan.</small>
        </div>
        <div>
            <a href="<?= base_url('admin/fasilitas/buku-tamu') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Main Card Form -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden mb-4">
                <div class="card-header text-white py-3 px-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #00695c, #00897b);">
                    <h5 class="card-title fs-6 fw-bold mb-0"><i class="fas fa-edit me-2"></i> Edit Tamu: <?= esc($t['nama_tamu']) ?></h5>
                    <span class="badge bg-white text-dark rounded-pill px-3 py-1 text-xs fw-bold"><?= esc($t['instansi'] ?: 'Umum') ?></span>
                </div>
                <form action="<?= base_url('admin/fasilitas/buku-tamu/update') ?>" method="POST" onsubmit="const btn = this.querySelector('button[type=submit]'); if(btn){ btn.disabled = true; btn.innerHTML = '<i class=\'fas fa-spinner fa-spin me-1\'></i> Menyimpan...'; }">
                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
                    <div class="card-body p-4">
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Nama Tamu *</label>
                                <input type="text" class="form-control rounded-3" name="nama_tamu" value="<?= esc($t['nama_tamu']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Instansi / Perusahaan *</label>
                                <input type="text" class="form-control rounded-3" name="instansi" value="<?= esc($t['instansi']) ?>" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">No. Telepon / WhatsApp</label>
                                <input type="text" class="form-control rounded-3" name="telepon" value="<?= esc($t['telepon']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Bertemu Dengan *</label>
                                <input type="text" class="form-control rounded-3" name="bertemu_dengan" value="<?= esc($t['bertemu_dengan']) ?>" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Waktu Kedatangan</label>
                                <input type="datetime-local" class="form-control rounded-3" name="tanggal_jam" value="<?= date('Y-m-d\TH:i', strtotime($t['tanggal_jam'])) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Status Kunjungan</label>
                                <select name="status" class="form-select rounded-3">
                                    <option value="Bertemu" <?= $t['status'] === 'Bertemu' ? 'selected' : '' ?>>Sedang Bertemu</option>
                                    <option value="Menunggu" <?= $t['status'] === 'Menunggu' ? 'selected' : '' ?>>Menunggu di Lobby</option>
                                    <option value="Selesai" <?= $t['status'] === 'Selesai' ? 'selected' : '' ?>>Kunjungan Selesai</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Keperluan / Maksud Kunjungan *</label>
                            <textarea class="form-control rounded-3" name="keperluan" rows="4" required><?= esc($t['keperluan']) ?></textarea>
                        </div>

                    </div>
                    <div class="card-footer bg-light px-4 py-3 text-end rounded-bottom-4">
                        <a href="<?= base_url('admin/fasilitas/buku-tamu') ?>" class="btn btn-secondary rounded-pill px-4 me-2 font-semibold">Batal</a>
                        <button type="submit" class="btn btn-warning text-white rounded-pill px-4 font-semibold shadow-sm">
                            <i class="fas fa-save me-1.5"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= view('admin/templates/footer', $data) ?>
