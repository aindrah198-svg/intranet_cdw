<?php
$data = [
    'title'  => 'Detail & Tanggapan Keluhan Karyawan',
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
                    <li class="breadcrumb-item"><a href="<?= base_url('direktur/karyawan/keluhan') ?>" class="text-decoration-none text-muted">Keluhan Karyawan</a></li>
                    <li class="breadcrumb-item active text-danger fw-bold" aria-current="page">Detail & Tanggapan</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-reply text-danger me-2"></i> Detail & Penanganan Keluhan</h4>
            <small class="text-muted">Pratinjau detail aspirasi/keluhan karyawan dan berikan instruksi/tanggapan direksi.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('direktur/karyawan/keluhan') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="row g-4">
        <!-- Kolom Kiri: Detail Keluhan -->
        <div class="col-lg-7">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden h-100">
                <div class="card-header text-white py-3.5 px-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #d32f2f, #b71c1c);">
                    <div>
                        <h5 class="card-title fs-5 fw-bold mb-1"><i class="fas fa-exclamation-circle me-2"></i> <?= esc($keluhan['judul']) ?></h5>
                        <small class="text-white-50"><i class="fas fa-tags me-1"></i> Kategori: <?= esc($keluhan['kategori']) ?></small>
                    </div>
                    <?php
                        $st = strtolower($keluhan['status'] ?? 'menunggu');
                        $badge = 'bg-warning text-dark';
                        if ($st === 'diproses') $badge = 'bg-info text-white';
                        if ($st === 'selesai') $badge = 'bg-success text-white';
                        if ($st === 'ditolak') $badge = 'bg-danger text-white';
                    ?>
                    <span class="badge <?= $badge ?> rounded-pill px-3 py-1.5 fs-6 fw-bold shadow-sm">
                        <?= strtoupper(esc($keluhan['status'] ?? 'Menunggu')) ?>
                    </span>
                </div>
                <div class="card-body p-4">
                    
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-user me-1 text-danger"></i> Karyawan Pelapor</small>
                                <h6 class="fw-bold text-dark mb-0 mt-1"><?= esc($keluhan['nama_lengkap'] ?: 'Admin User') ?></h6>
                                <small class="text-muted text-xs d-block"><?= esc($keluhan['divisi'] ?: 'Administrasi') ?> - <?= esc($keluhan['jabatan'] ?: 'Staff') ?></small>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-calendar-alt me-1 text-info"></i> Tanggal Keluhan</small>
                                <h6 class="fw-bold text-dark mb-0 mt-1"><?= date('d M Y', strtotime($keluhan['tanggal'])) ?></h6>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-align-left text-danger me-2"></i> Isi Rincian Keluhan</h6>
                        <div class="p-3 bg-light rounded-3 border text-dark text-sm" style="line-height: 1.6;">
                            <?= nl2br(esc($keluhan['deskripsi'] ?: 'Tidak ada deskripsi rincian.')) ?>
                        </div>
                    </div>

                    <?php if(!empty($keluhan['tanggapan'])): ?>
                    <div class="p-3 rounded-4 border bg-success bg-opacity-10">
                        <h6 class="fw-bold text-dark mb-1"><i class="fas fa-check-circle text-success me-2"></i> Tanggapan Terpasang Saat Ini</h6>
                        <small class="text-muted d-block mb-2">Oleh: <strong><?= esc($keluhan['ditanggapi_oleh'] ?: 'Direktur Utama') ?></strong> pada <?= !empty($keluhan['tanggal_tanggapan']) ? date('d M Y H:i', strtotime($keluhan['tanggal_tanggapan'])) : '-' ?></small>
                        <div class="p-3 bg-white rounded-3 border text-dark text-sm fw-semibold">
                            <?= nl2br(esc($keluhan['tanggapan'])) ?>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Form Tanggapan Direktur -->
        <div class="col-lg-5">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden h-100">
                <div class="card-header text-white py-3.5 px-4" style="background: linear-gradient(135deg, #1e3c72, #2a5298);">
                    <h5 class="card-title fs-6 fw-bold mb-0"><i class="fas fa-pen-nib me-2"></i> Form Tanggapan & Tindak Lanjut Direktur</h5>
                </div>
                <form action="<?= base_url('direktur/karyawan/keluhan/tanggapi/'.$keluhan['id']) ?>" method="POST">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Update Status Keluhan *</label>
                            <select name="status" class="form-select rounded-3" required>
                                <option value="Diproses" <?= (($keluhan['status'] ?? '')=='Diproses')?'selected':'' ?>>Diproses (Sedang ditindaklanjuti)</option>
                                <option value="Selesai" <?= (($keluhan['status'] ?? '')=='Selesai')?'selected':'' ?>>Selesai (Sudah terselesaikan)</option>
                                <option value="Ditolak" <?= (($keluhan['status'] ?? '')=='Ditolak')?'selected':'' ?>>Ditolak (Tidak dapat diproses)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Isi Tanggapan / Instruksi Penanganan *</label>
                            <textarea name="tanggapan" class="form-control rounded-3" rows="6" required placeholder="Tuliskan solusi, instruksi ke bagian HRD/Pengadaan, atau respon tanggapan resmi direksi..."><?= esc($keluhan['tanggapan']) ?></textarea>
                        </div>
                    </div>
                    <div class="card-footer bg-light px-4 py-3 text-end rounded-bottom-4">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm" style="background: #1e3c72; border-color: #1e3c72;">
                            <i class="fas fa-save me-1.5"></i> Simpan Tanggapan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (session()->getFlashdata('success')): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '<?= esc(session()->getFlashdata('success')) ?>',
        confirmButtonColor: '#1e3c72',
        timer: 3000,
        timerProgressBar: true,
        customClass: { popup: 'rounded-4 shadow-lg' }
    });
});
</script>
<?php endif; ?>

<?= view('direktur/templates/footer', $data) ?>
