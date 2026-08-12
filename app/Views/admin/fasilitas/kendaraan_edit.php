<?php
$data = [
    'title'     => $title ?? 'Edit Penugasan Kendaraan',
    'subtitle'  => 'Perbarui Rincian Operasional Mobil Dinas CDW Engineering',
    'active'    => 'kendaraan',
    'user'      => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
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
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/fasilitas/kendaraan') ?>" class="text-decoration-none text-muted">Koordinasi Kendaraan</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Edit Penugasan</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-edit text-warning me-2"></i> Edit Operasional Kendaraan Dinas</h4>
            <small class="text-muted">Perbarui unit armada, plat nomor, driver, pengguna, rute tujuan, atau status penugasan.</small>
        </div>
        <div>
            <a href="<?= base_url('admin/fasilitas/kendaraan') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Main Card Form -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden mb-4">
                <div class="card-header text-white py-3 px-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1565c0, #1e88e5);">
                    <h5 class="card-title fs-6 fw-bold mb-0"><i class="fas fa-edit me-2"></i> Edit Penugasan: <?= esc($k['nama_kendaraan']) ?></h5>
                    <span class="badge bg-white text-dark rounded-pill px-3 py-1 text-xs fw-bold"><?= esc($k['plat_nomor'] ?: 'Armada Dinas') ?></span>
                </div>
                <form action="<?= base_url('admin/fasilitas/kendaraan/update') ?>" method="POST" onsubmit="const btn = this.querySelector('button[type=submit]'); if(btn){ btn.disabled = true; btn.innerHTML = '<i class=\'fas fa-spinner fa-spin me-1\'></i> Menyimpan...'; }">
                    <input type="hidden" name="id" value="<?= $k['id'] ?>">
                    <div class="card-body p-4">
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-7">
                                <label class="form-label fw-semibold text-xs text-dark">Nama / Jenis Kendaraan *</label>
                                <input type="text" class="form-control rounded-3" name="nama_kendaraan" value="<?= esc($k['nama_kendaraan']) ?>" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-semibold text-xs text-dark">Plat Nomor Kendaraan</label>
                                <input type="text" class="form-control rounded-3" name="plat_nomor" value="<?= esc($k['plat_nomor']) ?>">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Pengemudi / Driver</label>
                                <input type="text" class="form-control rounded-3" name="driver" value="<?= esc($k['driver']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Pengguna / Tim Pemakai *</label>
                                <input type="text" class="form-control rounded-3" name="pengguna" value="<?= esc($k['pengguna']) ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Tujuan & Lokasi Kunjungan *</label>
                            <input type="text" class="form-control rounded-3" name="tujuan" value="<?= esc($k['tujuan']) ?>" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Waktu Keberangkatan *</label>
                                <input type="datetime-local" class="form-control rounded-3" name="tanggal_mulai" value="<?= !empty($k['tanggal_mulai']) ? date('Y-m-d\TH:i', strtotime($k['tanggal_mulai'])) : '' ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Estimasi Kepulangan *</label>
                                <input type="datetime-local" class="form-control rounded-3" name="tanggal_selesai" value="<?= !empty($k['tanggal_selesai']) ? date('Y-m-d\TH:i', strtotime($k['tanggal_selesai'])) : '' ?>" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Status Operasional</label>
                                <select name="status" class="form-select rounded-3">
                                    <option value="Sedang Berjalan" <?= $k['status'] === 'Sedang Berjalan' ? 'selected' : '' ?>>Sedang Berjalan</option>
                                    <option value="Disetujui" <?= $k['status'] === 'Disetujui' ? 'selected' : '' ?>>Disetujui / Ready</option>
                                    <option value="Pending" <?= $k['status'] === 'Pending' ? 'selected' : '' ?>>Pending / Menunggu</option>
                                    <option value="Selesai" <?= $k['status'] === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                                    <option value="Ditolak" <?= $k['status'] === 'Ditolak' ? 'selected' : '' ?>>Ditolak</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Catatan / Barang Bawaan</label>
                                <input type="text" class="form-control rounded-3" name="catatan" value="<?= esc($k['catatan']) ?>">
                            </div>
                        </div>

                    </div>
                    <div class="card-footer bg-light px-4 py-3 text-end rounded-bottom-4">
                        <a href="<?= base_url('admin/fasilitas/kendaraan') ?>" class="btn btn-secondary rounded-pill px-4 me-2 font-semibold">Batal</a>
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
