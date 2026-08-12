<?php
$dataHeader = [
    'title'    => $title ?? 'Detail Keluhan Karyawan',
    'subtitle' => $subtitle ?? 'Informasi Rinci Laporan Keluhan',
    'active'   => 'laporan-keluhan',
    'user'     => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
];

$st = strtolower($keluhan['status'] ?? 'baru');
$nama = $keluhan['nama_lengkap'] ?? 'Karyawan CDW';
?>

<?= view('admin/templates/header', $dataHeader) ?>
<?= view('admin/templates/sidebar', $dataHeader) ?>
<?= view('admin/templates/navbar', $dataHeader) ?>

<div class="container-fluid py-3 py-md-4">
    <!-- Header Card -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-4 shadow-sm p-3 p-md-4 mb-4 border border-light flex-wrap gap-3">
        <div class="d-flex align-items-center overflow-hidden">
            <div class="bg-primary text-white rounded-3 p-3 me-3 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 48px; height: 48px; background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;">
                <i class="fas fa-file-alt fs-5"></i>
            </div>
            <div class="overflow-hidden">
                <h4 class="mb-0 fw-bold text-dark text-truncate" style="font-size: 1.25rem;"><?= esc($keluhan['judul'] ?? 'Detail Keluhan') ?></h4>
                <p class="text-muted mb-0 text-sm">Informasi lengkap rincian keluhan dan status tindak lanjut.</p>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('admin/laporan/keluhan') ?>" class="btn btn-outline-secondary rounded-pill px-3.5 py-2 text-sm fw-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-dark text-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold text-sm mb-0 d-flex align-items-center">
                        <i class="fas fa-info-circle text-warning me-2 fs-5"></i> Rincian Keluhan #<?= $keluhan['id'] ?>
                    </h5>
                    <div>
                        <?php if ($st === 'selesai'): ?>
                            <span class="badge bg-success text-white px-3 py-1.5 rounded-pill text-xs fw-bold"><i class="fas fa-check-circle me-1"></i> Selesai</span>
                        <?php elseif ($st === 'diproses'): ?>
                            <span class="badge bg-info text-white px-3 py-1.5 rounded-pill text-xs fw-bold"><i class="fas fa-spinner me-1"></i> Sedang Diproses</span>
                        <?php elseif ($st === 'ditolak'): ?>
                            <span class="badge bg-danger text-white px-3 py-1.5 rounded-pill text-xs fw-bold"><i class="fas fa-times-circle me-1"></i> Ditolak</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill text-xs fw-bold"><i class="fas fa-clock me-1"></i> Menunggu Tanggapan</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-6">
                            <div class="card border-0 bg-light p-3.5 rounded-3 h-100 border border-secondary border-opacity-10">
                                <h6 class="fw-bold text-primary border-bottom pb-2 mb-3 text-sm">
                                    <i class="fas fa-user-tag me-1.5"></i> INFORMASI PELAPOR & KATEGORI
                                </h6>
                                <div class="d-flex justify-content-between py-2 border-bottom border-secondary border-opacity-10 text-sm">
                                    <span class="text-muted">Nama Karyawan:</span>
                                    <span class="fw-bold text-dark"><?= esc($nama) ?></span>
                                </div>
                                <div class="d-flex justify-content-between py-2 border-bottom border-secondary border-opacity-10 text-sm">
                                    <span class="text-muted">NIK / Jabatan:</span>
                                    <span class="fw-semibold text-secondary"><?= esc($keluhan['nik'] ?? '-') ?> (<?= esc($keluhan['jabatan'] ?? 'Staf') ?>)</span>
                                </div>
                                <div class="d-flex justify-content-between py-2 border-bottom border-secondary border-opacity-10 text-sm">
                                    <span class="text-muted">Kategori Keluhan:</span>
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2.5 py-1 rounded-pill text-xs"><?= esc($keluhan['kategori'] ?? 'Lainnya') ?></span>
                                </div>
                                <div class="d-flex justify-content-between py-2 text-sm">
                                    <span class="text-muted">Tanggal Dilaporkan:</span>
                                    <span class="fw-semibold text-dark"><?= date('d F Y', strtotime($keluhan['tanggal'] ?? date('Y-m-d'))) ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="card border-0 bg-light p-3.5 rounded-3 h-100 border border-secondary border-opacity-10">
                                <h6 class="fw-bold text-primary border-bottom pb-2 mb-3 text-sm">
                                    <i class="fas fa-clock me-1.5"></i> STATUS PENANGANAN & TANGGAPAN
                                </h6>
                                <div class="d-flex justify-content-between py-2 border-bottom border-secondary border-opacity-10 text-sm">
                                    <span class="text-muted">Status Penanganan:</span>
                                    <span class="fw-bold text-dark text-capitalize"><?= esc($keluhan['status'] ?? 'baru') ?></span>
                                </div>
                                <div class="d-flex justify-content-between py-2 border-bottom border-secondary border-opacity-10 text-sm">
                                    <span class="text-muted">Penanggap:</span>
                                    <span class="fw-semibold text-dark"><?= esc($keluhan['nama_penanggap'] ?? 'Pimpinan / Manajemen') ?></span>
                                </div>
                                <div class="d-flex justify-content-between py-2 text-sm">
                                    <span class="text-muted">Waktu Tanggapan:</span>
                                    <span class="fw-semibold text-dark"><?= !empty($keluhan['tanggal_tanggapan']) ? date('d F Y H:i', strtotime($keluhan['tanggal_tanggapan'])) : '-' ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Judul & Deskripsi Keluhan -->
                    <div class="card border-0 rounded-3 p-3.5 bg-white border border-light shadow-xs mb-4">
                        <label class="fw-bold text-dark text-xs text-uppercase text-secondary mb-2">
                            <i class="fas fa-heading me-1 text-primary"></i> Subjek / Judul Keluhan:
                        </label>
                        <h5 class="fw-bold text-dark mb-3"><?= esc($keluhan['judul'] ?? 'Keluhan') ?></h5>

                        <label class="fw-bold text-dark text-xs text-uppercase text-secondary mb-2">
                            <i class="fas fa-align-left me-1 text-primary"></i> Deskripsi Rinci Keluhan:
                        </label>
                        <p class="mb-0 text-dark text-sm leading-relaxed" style="white-space: pre-line;"><?= esc($keluhan['deskripsi'] ?? '-') ?></p>
                    </div>

                    <!-- Box Tanggapan Pimpinan -->
                    <?php if (!empty($keluhan['tanggapan'])): ?>
                        <div class="card border-0 rounded-3 p-4 bg-success bg-opacity-10 border border-success border-opacity-20">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="fas fa-reply text-success fs-5"></i>
                                <h6 class="fw-bold text-success mb-0 text-sm">Tanggapan / Tindak Lanjut Pimpinan</h6>
                            </div>
                            <p class="mb-0 text-dark text-sm fw-semibold" style="white-space: pre-line;"><?= esc($keluhan['tanggapan']) ?></p>
                            <?php if (!empty($keluhan['tanggal_tanggapan'])): ?>
                                <small class="text-muted text-xs mt-2 d-block"><i class="fas fa-clock me-1"></i> Ditanggapi pada: <?= date('d F Y H:i', strtotime($keluhan['tanggal_tanggapan'])) ?></small>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning border-0 rounded-3 p-3.5 text-sm mb-0">
                            <i class="fas fa-clock me-2"></i> Belum ada tanggapan resmi dari pimpinan/manajemen. Laporan ini sedang dalam peninjauan.
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card-footer bg-light border-top py-3 px-4 d-flex justify-content-between align-items-center">
                    <a href="<?= base_url('admin/laporan/keluhan') ?>" class="btn btn-light rounded-pill px-4 text-sm fw-semibold border">
                        <i class="fas fa-arrow-left me-1.5"></i> Kembali
                    </a>
                    <button type="button" class="btn btn-outline-danger rounded-pill px-4 text-sm fw-bold" onclick="confirmDeleteKeluhan(<?= $keluhan['id'] ?>, '<?= esc($keluhan['judul']) ?>')">
                        <i class="fas fa-trash-alt me-1.5"></i> Hapus Laporan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDeleteKeluhan(id, judul) {
    Swal.fire({
        title: 'Hapus Laporan Keluhan?',
        text: 'Laporan keluhan "' + judul + '" akan dihapus dari sistem.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-4 shadow-lg' }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= base_url('admin/laporan/keluhan/delete') ?>/' + id;
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '<?= csrf_token() ?>';
            csrfInput.value = '<?= csrf_hash() ?>';
            form.appendChild(csrfInput);

            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>

<?= view('admin/templates/footer', $dataHeader) ?>
