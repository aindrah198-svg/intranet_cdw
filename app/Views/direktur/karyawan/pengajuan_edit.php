<?php
$data = [
    'title'  => 'Edit Permohonan / Izin Karyawan',
    'active' => 'karyawan',
    'user'   => ['name' => session()->get('name') ?? 'Direktur', 'role' => 'direktur']
];

echo view('direktur/templates/header', $data);
echo view('direktur/templates/sidebar', $data);
echo view('direktur/templates/navbar', $data);
?>

<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-xs">
                    <li class="breadcrumb-item"><a href="<?= base_url('direktur/karyawan/pengajuan') ?>" class="text-decoration-none text-muted">Permohonan & Izin</a></li>
                    <li class="breadcrumb-item active text-warning fw-bold" aria-current="page">Edit Permohonan</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-edit text-warning me-2"></i> Edit Permohonan & Status (Direktur)</h4>
            <small class="text-muted">Perbarui status persetujuan, rincian permohonan, atau foto bukti pendukung.</small>
        </div>
        <div>
            <a href="<?= base_url('direktur/karyawan/pengajuan') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Main Card Form -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden mb-4">
                <div class="card-header text-white py-3 px-4" style="background: linear-gradient(135deg, #1e3c72, #2a5298);">
                    <h5 class="card-title fs-6 fw-bold mb-0"><i class="fas fa-user-edit me-2"></i> Edit Permohonan: <?= esc($p['nama_lengkap'] ?? 'Karyawan') ?> (Nomor: <?= esc($p['nomor_pengajuan'] ?? 'PGJ-'.$p['id']) ?>)</h5>
                </div>
                <form action="<?= base_url('direktur/karyawan/pengajuan/update/'.$p['id']) ?>" method="POST" enctype="multipart/form-data" onsubmit="const btn = this.querySelector('button[type=submit]'); if(btn){ btn.disabled = true; btn.innerHTML = '<i class=\'fas fa-spinner fa-spin me-1\'></i> Menyimpan...'; }">
                    <div class="card-body p-4">
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Pemohon / Karyawan</label>
                                <input type="text" class="form-control rounded-3 bg-light text-dark fw-bold" value="<?= esc($p['nama_lengkap'] ?? 'Admin/Karyawan') ?> (<?= esc($p['divisi'] ?? '-') ?>)" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Status Persetujuan Direktur *</label>
                                <select name="status" class="form-select rounded-3 fw-bold" required>
                                    <option value="Menunggu" <?= (strtolower($p['status'] ?? '') === 'menunggu') ? 'selected' : '' ?>>Menunggu Persetujuan</option>
                                    <option value="Disetujui" <?= (strtolower($p['status'] ?? '') === 'disetujui') ? 'selected' : '' ?>>Disetujui</option>
                                    <option value="Ditolak" <?= (strtolower($p['status'] ?? '') === 'ditolak') ? 'selected' : '' ?>>Ditolak</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Kategori Permohonan / Izin *</label>
                                <select name="kategori_pengajuan" class="form-select rounded-3" required>
                                    <option value="Sakit / Izin Medis" <?= ($p['kategori_pengajuan'] === 'Sakit / Izin Medis') ? 'selected' : '' ?>>Sakit / Izin Medis</option>
                                    <option value="Kecelakaan Kerja" <?= ($p['kategori_pengajuan'] === 'Kecelakaan Kerja') ? 'selected' : '' ?>>Kecelakaan Kerja</option>
                                    <option value="WFH (Work From Home)" <?= ($p['kategori_pengajuan'] === 'WFH (Work From Home)') ? 'selected' : '' ?>>WFH (Work From Home)</option>
                                    <option value="WFC (Work From Cafe)" <?= ($p['kategori_pengajuan'] === 'WFC (Work From Cafe)') ? 'selected' : '' ?>>WFC (Work From Cafe)</option>
                                    <option value="Perjalanan Dinas Luar" <?= ($p['kategori_pengajuan'] === 'Perjalanan Dinas Luar') ? 'selected' : '' ?>>Perjalanan Dinas / Tugas Luar</option>
                                    <option value="Izin Terlambat / Pulang Awal" <?= ($p['kategori_pengajuan'] === 'Izin Terlambat / Pulang Awal') ? 'selected' : '' ?>>Izin Terlambat / Pulang Awal</option>
                                    <option value="Permohonan Administrasi" <?= ($p['kategori_pengajuan'] === 'Permohonan Administrasi') ? 'selected' : '' ?>>Permohonan Administrasi / General</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Judul / Perihal Permohonan *</label>
                                <input type="text" class="form-control rounded-3" name="judul_pengajuan" value="<?= esc($p['judul_pengajuan']) ?>" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Tanggal Mulai *</label>
                                <input type="date" class="form-control rounded-3" id="tglMulaiPengajuan" name="tanggal_mulai" value="<?= esc($p['tanggal_mulai']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Tanggal Selesai *</label>
                                <input type="date" class="form-control rounded-3" id="tglSelesaiPengajuan" name="tanggal_selesai" value="<?= esc($p['tanggal_selesai']) ?>" min="<?= esc($p['tanggal_mulai']) ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Keterangan / Alasan Permohonan *</label>
                            <textarea class="form-control rounded-3" name="keterangan" rows="4" required><?= esc($p['keterangan']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Perbarui Foto Bukti Pendukung (Opsional)</label>
                            <input type="file" class="form-control rounded-3" name="bukti_foto" accept="image/*">
                            <small class="text-muted text-xs d-block mt-1">Biarkan kosong jika tidak ingin mengganti foto bukti yang sudah diunggah.</small>
                            <?php if(!empty($p['bukti_foto'])): ?>
                                <div class="mt-2">
                                    <small class="text-muted text-xs d-block mb-1">Foto Bukti Saat Ini:</small>
                                    <img src="<?= base_url($p['bukti_foto']) ?>" alt="Bukti" class="img-thumbnail rounded-3 shadow-sm" style="max-height: 140px;">
                                </div>
                            <?php endif; ?>
                        </div>

                    </div>
                    <div class="card-footer bg-light px-4 py-3 text-end rounded-bottom-4">
                        <a href="<?= base_url('direktur/karyawan/pengajuan') ?>" class="btn btn-secondary rounded-pill px-4 me-2 font-semibold">Batal</a>
                        <button type="submit" class="btn btn-warning text-white rounded-pill px-4 font-semibold shadow-sm" style="background: #f57c00; border-color: #f57c00;">
                            <i class="fas fa-save me-1.5"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (session()->getFlashdata('error')): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'error',
        title: 'Perhatian!',
        text: '<?= esc(session()->getFlashdata('error')) ?>',
        confirmButtonColor: '#dc3545',
        customClass: { popup: 'rounded-4 shadow-lg' }
    });
});
</script>
<?php endif; ?>

<?= view('direktur/templates/footer', $data) ?>
