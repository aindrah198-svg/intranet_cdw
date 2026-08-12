<?php
$data = [
    'title'    => $title ?? 'Edit Pengajuan',
    'subtitle' => 'Perbarui Data & Rincian Pengajuan Admin CDW Engineering',
    'active'   => 'pengajuan-semua',
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
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/pengajuan/semua') ?>" class="text-decoration-none text-muted">Pengajuan</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Edit Pengajuan</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-edit text-warning me-2"></i> Edit Data Pengajuan</h4>
            <small class="text-muted">Perbarui kategori, judul, tanggal pelaksanaan, atau catatan permohonan pengajuan.</small>
        </div>
        <div>
            <a href="<?= base_url('admin/pengajuan/semua') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Batal / Kembali
            </a>
        </div>
    </div>

    <!-- Main Card Form -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden mb-4">
                <div class="card-header text-white py-3 px-4" style="background: linear-gradient(135deg, #4a148c, #7b1fa2);">
                    <h5 class="card-title fs-6 fw-bold mb-0"><i class="fas fa-pencil-alt me-2"></i> Form Perubahan Data Pengajuan (<?= esc($p['nomor_pengajuan']) ?>)</h5>
                </div>
                <form action="<?= base_url('admin/pengajuan/update') ?>" method="POST">
                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                    
                    <div class="card-body p-4">
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Kategori Pengajuan *</label>
                                <select name="kategori_pengajuan" class="form-select rounded-3" required>
                                    <option value="Cuti Tahunan" <?= ($p['kategori_pengajuan']=='Cuti Tahunan')?'selected':'' ?>>Cuti Tahunan</option>
                                    <option value="Cuti Sakit" <?= ($p['kategori_pengajuan']=='Cuti Sakit')?'selected':'' ?>>Cuti Sakit / Izin Medis</option>
                                    <option value="Cuti Khusus" <?= ($p['kategori_pengajuan']=='Cuti Khusus')?'selected':'' ?>>Cuti Khusus / Melahirkan / Pernikahan</option>
                                    <option value="Izin Dinas Luar" <?= ($p['kategori_pengajuan']=='Izin Dinas Luar')?'selected':'' ?>>Izin Perjalanan Dinas Luar</option>
                                    <option value="Pengajuan General" <?= ($p['kategori_pengajuan']=='Pengajuan General')?'selected':'' ?>>Pengajuan General / Permohonan Administrasi</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Status Pengajuan</label>
                                <select name="status" class="form-select rounded-3">
                                    <option value="Menunggu" <?= (($p['status'] ?? '')=='Menunggu')?'selected':'' ?>>Menunggu Approval</option>
                                    <option value="Disetujui" <?= (($p['status'] ?? '')=='Disetujui')?'selected':'' ?>>Disetujui</option>
                                    <option value="Ditolak" <?= (($p['status'] ?? '')=='Ditolak')?'selected':'' ?>>Ditolak</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Judul / Perihal Pengajuan *</label>
                            <input type="text" class="form-control rounded-3" name="judul_pengajuan" value="<?= esc($p['judul_pengajuan']) ?>" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Tanggal Mulai *</label>
                                <input type="date" class="form-control rounded-3" name="tanggal_mulai" value="<?= esc($p['tanggal_mulai']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Tanggal Selesai *</label>
                                <input type="date" class="form-control rounded-3" name="tanggal_selesai" value="<?= esc($p['tanggal_selesai']) ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Keterangan / Alasan Pengajuan *</label>
                            <textarea class="form-control rounded-3" name="keterangan" rows="4" required><?= esc($p['keterangan']) ?></textarea>
                        </div>

                    </div>
                    <div class="card-footer bg-light px-4 py-3 text-end rounded-bottom-4">
                        <a href="<?= base_url('admin/pengajuan/semua') ?>" class="btn btn-secondary rounded-pill px-4 me-2 font-semibold">Batal</a>
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
