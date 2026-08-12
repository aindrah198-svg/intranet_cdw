<?php
$dataHeader = [
    'title'    => $title ?? 'Laporan Keluhan Karyawan',
    'subtitle' => $subtitle ?? 'Pengelolaan & Pemantauan Keluhan Karyawan',
    'active'   => 'laporan-keluhan',
    'user'     => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
];
?>

<?= view('admin/templates/header', $dataHeader) ?>
<?= view('admin/templates/sidebar', $dataHeader) ?>
<?= view('admin/templates/navbar', $dataHeader) ?>

<style>
    html, body {
        max-width: 100% !important;
        overflow-x: hidden !important;
    }
    .main-content {
        max-width: 100% !important;
        overflow-x: hidden !important;
    }
    .container-fluid {
        max-width: 100% !important;
        padding-left: 12px !important;
        padding-right: 12px !important;
        box-sizing: border-box !important;
    }
    .keluhan-card-modern {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        border-radius: 18px !important;
        box-shadow: 0 8px 25px rgba(30, 60, 114, 0.05) !important;
    }
    .stat-card-keluhan {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card-keluhan:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    }
    .table-scroll-wrapper {
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
        display: block !important;
        margin-bottom: 0;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }
    .table-scroll-wrapper table {
        min-width: 920px !important;
        width: 100% !important;
        margin-bottom: 0 !important;
    }
    .avatar-circle-sm {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
        flex-shrink: 0;
    }
</style>

<div class="container-fluid py-3 py-md-4">

    <!-- 1. Header Section -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-4 shadow-sm p-3 p-md-4 mb-4 border border-light flex-wrap gap-3">
        <div class="d-flex align-items-center overflow-hidden">
            <div class="bg-danger text-white rounded-3 p-3 me-3 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 52px; height: 52px; background: linear-gradient(135deg, #ef4444, #dc2626) !important;">
                <i class="fas fa-exclamation-triangle fs-4"></i>
            </div>
            <div class="overflow-hidden">
                <h4 class="mb-1 fw-bold text-dark" style="font-size: 1.3rem;">Laporan Keluhan Karyawan</h4>
                <p class="text-muted mb-0 text-sm">Pengelolaan & pemantauan keluhan operasional dan SDM karyawan CDW Engineering.</p>
            </div>
        </div>
        <div>
            <a href="<?= base_url('admin/laporan/keluhan/tambah') ?>" class="btn btn-danger text-white rounded-pill shadow-sm d-inline-flex align-items-center text-sm fw-bold px-3.5 py-2">
                <i class="fas fa-plus me-1.5"></i> <span>+ Laporkan Keluhan Baru</span>
            </a>
        </div>
    </div>

    <!-- Alert Flash Notifications -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- 2. Metrics Statistics Row -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card-keluhan p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <small class="text-muted font-semibold text-uppercase text-xs">Total Keluhan</small>
                    <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-circle">
                        <i class="fas fa-comments fs-6"></i>
                    </div>
                </div>
                <div class="fs-5 fw-bold text-dark mb-0"><?= $statistik['total'] ?? 0 ?> Laporan</div>
                <small class="text-muted text-xs">Seluruh Keluhan Terdaftar</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card-keluhan p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <small class="text-muted font-semibold text-uppercase text-xs">Menunggu Respon</small>
                    <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-circle">
                        <i class="fas fa-clock fs-6"></i>
                    </div>
                </div>
                <div class="fs-5 fw-bold text-warning mb-0"><?= $statistik['baru'] ?? 0 ?> Baru</div>
                <small class="text-muted text-xs">Belum Ditanggapi</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card-keluhan p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <small class="text-muted font-semibold text-uppercase text-xs">Sedang Diproses</small>
                    <div class="bg-info bg-opacity-10 text-info p-2 rounded-circle">
                        <i class="fas fa-spinner fs-6"></i>
                    </div>
                </div>
                <div class="fs-5 fw-bold text-info mb-0"><?= $statistik['diproses'] ?? 0 ?> Diproses</div>
                <small class="text-muted text-xs">Dalam Penanganan</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card-keluhan p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <small class="text-muted font-semibold text-uppercase text-xs">Selesai</small>
                    <div class="bg-success bg-opacity-10 text-success p-2 rounded-circle">
                        <i class="fas fa-check-circle fs-6"></i>
                    </div>
                </div>
                <div class="fs-5 fw-bold text-success mb-0"><?= $statistik['selesai'] ?? 0 ?> Selesai</div>
                <small class="text-muted text-xs">Telah Ditindaklanjuti</small>
            </div>
        </div>
    </div>

    <!-- 3. Filter Tabs & Data Table Card -->
    <div class="keluhan-card-modern">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2 rounded-top-4">
            <div class="btn-group rounded-pill p-1 bg-light border" role="group">
                <a href="<?= base_url('admin/laporan/keluhan') ?>" class="btn btn-sm rounded-pill fw-semibold <?= empty($filterStatus) ? 'btn-primary shadow-sm' : 'btn-light text-secondary' ?>">Semua (<?= $statistik['total'] ?? 0 ?>)</a>
                <a href="<?= base_url('admin/laporan/keluhan?status=baru') ?>" class="btn btn-sm rounded-pill fw-semibold <?= $filterStatus === 'baru' ? 'btn-warning text-dark shadow-sm' : 'btn-light text-secondary' ?>">Baru (<?= $statistik['baru'] ?? 0 ?>)</a>
                <a href="<?= base_url('admin/laporan/keluhan?status=diproses') ?>" class="btn btn-sm rounded-pill fw-semibold <?= $filterStatus === 'diproses' ? 'btn-info text-white shadow-sm' : 'btn-light text-secondary' ?>">Diproses (<?= $statistik['diproses'] ?? 0 ?>)</a>
                <a href="<?= base_url('admin/laporan/keluhan?status=selesai') ?>" class="btn btn-sm rounded-pill fw-semibold <?= $filterStatus === 'selesai' ? 'btn-success shadow-sm' : 'btn-light text-secondary' ?>">Selesai (<?= $statistik['selesai'] ?? 0 ?>)</a>
                <a href="<?= base_url('admin/laporan/keluhan?status=ditolak') ?>" class="btn btn-sm rounded-pill fw-semibold <?= $filterStatus === 'ditolak' ? 'btn-danger shadow-sm' : 'btn-light text-secondary' ?>">Ditolak (<?= $statistik['ditolak'] ?? 0 ?>)</a>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-scroll-wrapper">
                <table class="table table-hover align-middle mb-0 text-sm">
                    <thead class="table-light border-bottom">
                        <tr>
                            <th class="ps-4" style="width: 50px;">#</th>
                            <th>Karyawan Pemohon</th>
                            <th>Tanggal & Kategori</th>
                            <th>Judul & Ringkasan Keluhan</th>
                            <th class="text-center">Status & Tanggapan Pimpinan</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($keluhanList)): ?>
                            <?php foreach ($keluhanList as $idx => $k): ?>
                                <?php 
                                    $nama = $k['nama_lengkap'] ?? 'Karyawan CDW';
                                    $initials = strtoupper(substr($nama, 0, 2));
                                    $st = strtolower($k['status'] ?? 'baru');
                                ?>
                                <tr>
                                    <td class="ps-4 fw-semibold text-secondary"><?= $idx + 1 ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-circle-sm"><?= $initials ?></div>
                                            <div>
                                                <div class="fw-bold text-dark"><?= esc($nama) ?></div>
                                                <small class="text-muted">NIK: <?= esc($k['nik'] ?? '-') ?> | <?= esc($k['jabatan'] ?? 'Staf') ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted d-block mb-1"><i class="fas fa-calendar-alt me-1"></i> <?= date('d/m/Y', strtotime($k['tanggal'] ?? date('Y-m-d'))) ?></small>
                                        <span class="badge bg-light text-dark border font-normal px-2.5 py-1 rounded-pill text-xs"><?= esc($k['kategori'] ?? 'Lainnya') ?></span>
                                    </td>
                                    <td style="max-width: 320px;">
                                        <div class="fw-bold text-dark mb-0.5 text-truncate" title="<?= esc($k['judul']) ?>"><?= esc($k['judul'] ?? 'Keluhan') ?></div>
                                        <small class="text-muted d-block text-truncate" style="max-width: 300px;" title="<?= esc($k['deskripsi']) ?>"><?= esc($k['deskripsi'] ?? '-') ?></small>
                                    </td>
                                    <td class="text-center" style="max-width: 280px;">
                                        <?php if ($st === 'selesai'): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-1.5 rounded-pill"><i class="fas fa-check-circle me-1"></i> Selesai / Ditindaklanjuti</span>
                                        <?php elseif ($st === 'diproses'): ?>
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info px-3 py-1.5 rounded-pill"><i class="fas fa-spinner me-1"></i> Sedang Diproses</span>
                                        <?php elseif ($st === 'ditolak'): ?>
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-1.5 rounded-pill"><i class="fas fa-times-circle me-1"></i> Ditolak</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-3 py-1.5 rounded-pill text-dark"><i class="fas fa-clock me-1"></i> Menunggu Tanggapan</span>
                                        <?php endif; ?>

                                        <?php if (!empty($k['tanggapan'])): ?>
                                            <div class="p-2 bg-success bg-opacity-10 text-success border border-success border-opacity-20 rounded-3 text-xs mt-1.5 text-start text-truncate" title="<?= esc($k['tanggapan']) ?>">
                                                <i class="fas fa-reply me-1"></i> <strong>Tanggapan:</strong> <?= esc($k['tanggapan']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <a href="<?= base_url('admin/laporan/keluhan/detail/' . $k['id']) ?>" class="btn btn-sm btn-outline-primary rounded-circle p-2 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Lihat Detail Halaman">
                                                <i class="fas fa-eye text-xs"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-circle p-2 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Hapus Keluhan" onclick="confirmDeleteKeluhan(<?= $k['id'] ?>, '<?= esc($k['judul']) ?>')">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-comments fs-1 mb-3 text-secondary opacity-50 d-block"></i>
                                    Belum ada data keluhan karyawan terdaftar.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
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
