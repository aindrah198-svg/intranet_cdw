<?php
$data = [
    'title'    => $title ?? 'Edit Booking Ruang Meeting',
    'subtitle' => 'Perbarui Reservasi Ruang Rapat CDW Engineering',
    'active'   => 'booking-ruang',
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
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/fasilitas/booking-ruang') ?>" class="text-decoration-none text-muted">Booking Ruang</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Edit Reservasi</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-edit text-warning me-2"></i> Edit Reservasi Ruang Rapat</h4>
            <small class="text-muted">Perbarui ruangan, peminjam, jam penggunaan, atau status reservasi.</small>
        </div>
        <div>
            <a href="<?= base_url('admin/fasilitas/booking-ruang') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Main Card Form -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden mb-4">
                <div class="card-header text-white py-3 px-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #2e7d32, #4caf50);">
                    <h5 class="card-title fs-6 fw-bold mb-0"><i class="fas fa-edit me-2"></i> Edit Booking: <?= esc($b['nama_ruangan']) ?></h5>
                    <span class="badge bg-white text-dark rounded-pill px-3 py-1 text-xs fw-bold"><?= esc($b['peminjam']) ?></span>
                </div>
                <form action="<?= base_url('admin/fasilitas/booking-ruang/update') ?>" method="POST" onsubmit="const btn = this.querySelector('button[type=submit]'); if(btn){ btn.disabled = true; btn.innerHTML = '<i class=\'fas fa-spinner fa-spin me-1\'></i> Menyimpan...'; }">
                    <input type="hidden" name="id" value="<?= $b['id'] ?>">
                    <div class="card-body p-4">
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Pilih Ruangan Meeting *</label>
                            <select name="nama_ruangan" class="form-select rounded-3" required>
                                <option value="Ruang Rapat Utama (Lt 2)" <?= $b['nama_ruangan'] === 'Ruang Rapat Utama (Lt 2)' ? 'selected' : '' ?>>Ruang Rapat Utama (Lt 2 - Kapasitas 20 Orang)</option>
                                <option value="Ruang Diskusi Teknik (Lt 1)" <?= $b['nama_ruangan'] === 'Ruang Diskusi Teknik (Lt 1)' ? 'selected' : '' ?>>Ruang Diskusi Teknik (Lt 1 - Kapasitas 8 Orang)</option>
                                <option value="Executive Boardroom (Lt 3)" <?= $b['nama_ruangan'] === 'Executive Boardroom (Lt 3)' ? 'selected' : '' ?>>Executive Boardroom (Lt 3 - Kapasitas 12 Orang)</option>
                                <option value="Ruang Meeting Hybrid / Zoom" <?= $b['nama_ruangan'] === 'Ruang Meeting Hybrid / Zoom' ? 'selected' : '' ?>>Ruang Meeting Hybrid / Zoom Studio</option>
                            </select>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-xs text-dark">Tanggal Meeting *</label>
                                <input type="date" class="form-control rounded-3" name="tanggal" value="<?= esc($b['tanggal']) ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-xs text-dark">Jam Mulai *</label>
                                <input type="time" class="form-control rounded-3" name="jam_mulai" value="<?= esc($b['jam_mulai']) ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-xs text-dark">Jam Selesai *</label>
                                <input type="time" class="form-control rounded-3" name="jam_selesai" value="<?= esc($b['jam_selesai']) ?>" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-5">
                                <label class="form-label fw-semibold text-xs text-dark">Nama Peminjam / PIC *</label>
                                <input type="text" class="form-control rounded-3" name="peminjam" value="<?= esc($b['peminjam']) ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-xs text-dark">Divisi / Unit Kerja *</label>
                                <input type="text" class="form-control rounded-3" name="divisi" value="<?= esc($b['divisi']) ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-xs text-dark">Jumlah Peserta</label>
                                <input type="number" class="form-control rounded-3" name="jumlah_peserta" value="<?= (int)$b['jumlah_peserta'] ?>" min="1">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold text-xs text-dark">Agenda Meeting *</label>
                                <textarea class="form-control rounded-3" name="agenda" rows="3" required><?= esc($b['agenda']) ?></textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-xs text-dark">Status Reservasi</label>
                                <select name="status" class="form-select rounded-3">
                                    <option value="Disetujui" <?= $b['status'] === 'Disetujui' ? 'selected' : '' ?>>Disetujui</option>
                                    <option value="Pending" <?= $b['status'] === 'Pending' ? 'selected' : '' ?>>Pending / Menunggu</option>
                                    <option value="Selesai" <?= $b['status'] === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                                    <option value="Ditolak" <?= $b['status'] === 'Ditolak' ? 'selected' : '' ?>>Ditolak</option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer bg-light px-4 py-3 text-end rounded-bottom-4">
                        <a href="<?= base_url('admin/fasilitas/booking-ruang') ?>" class="btn btn-secondary rounded-pill px-4 me-2 font-semibold">Batal</a>
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
