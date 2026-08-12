<?php
$data = [
    'title'    => 'Semua Pengajuan',
    'subtitle' => 'Pusat Manajemen Pengajuan & Permohonan Administrasi CDW',
    'active'   => 'pengajuan-semua',
    'user'     => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
];

$totalGeneral = count($pengajuan ?? []);
$totalCuti = count($cuti ?? []);
$totalKeluhan = count($keluhan ?? []);
$totalAll = $totalGeneral + $totalCuti + $totalKeluhan;

$pendingCount = 0;
foreach($pengajuan as $p) { if(strtolower($p['status'] ?? '') === 'menunggu') $pendingCount++; }
foreach($cuti as $c) { if(strtolower($c['status'] ?? '') === 'menunggu') $pendingCount++; }
foreach($keluhan as $k) { if(strtolower($k['status'] ?? '') === 'menunggu') $pendingCount++; }
?>

<?= view('admin/templates/header', $data) ?>
<?= view('admin/templates/sidebar', $data) ?>
<?= view('admin/templates/navbar', $data) ?>

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
        overflow-x: hidden !important;
    }
    .row {
        margin-left: -6px !important;
        margin-right: -6px !important;
    }
    .row > [class*="col-"] {
        padding-left: 6px !important;
        padding-right: 6px !important;
    }
    .card {
        max-width: 100% !important;
        box-sizing: border-box !important;
    }
    .pengajuan-card-modern {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        border-radius: 18px !important;
        box-shadow: 0 8px 25px rgba(30, 60, 114, 0.06), 0 2px 6px rgba(0, 0, 0, 0.02) !important;
        transition: all 0.3s ease;
    }
    .pengajuan-card-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 32px rgba(30, 60, 114, 0.12) !important;
    }
    .stat-card-pengajuan {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.8);
        border-radius: 16px;
        box-shadow: 0 6px 20px rgba(30, 60, 114, 0.05);
        transition: transform 0.2s ease;
    }
    .stat-card-pengajuan:hover {
        transform: translateY(-2px);
    }
    .stat-number-responsive {
        font-size: clamp(1.1rem, 2.5vw, 1.4rem);
        font-weight: 700;
        line-height: 1.2;
    }
    .table-scroll-wrapper {
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
        display: block !important;
        margin-bottom: 0;
        border-radius: 12px;
        border: 1px solid #cbd5e1;
    }
    .table-scroll-wrapper table {
        min-width: 860px !important;
        width: 100% !important;
        margin-bottom: 0 !important;
    }
</style>

<div class="container-fluid py-3 py-md-4">

    <!-- 1. Header Section -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-3 border border-light flex-wrap gap-3">
        <div class="d-flex align-items-center overflow-hidden">
            <div class="bg-primary text-white rounded-3 p-2 me-3 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 44px; height: 44px; background: linear-gradient(135deg, #4a148c, #7b1fa2) !important;">
                <i class="fas fa-clipboard-list fs-5"></i>
            </div>
            <div class="overflow-hidden">
                <h4 class="mb-0 fw-bold text-dark text-truncate" style="font-size: 1.25rem;">Semua Pengajuan Permohonan & Izin</h4>
                <small class="text-muted d-none d-sm-inline">Monitoring permohonan & izin kantor (Sakit, WFH, WFC, Perjalanan Dinas, Izin Pribadi).</small>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('admin/pengajuan/tambah') ?>" class="btn btn-primary rounded-pill shadow-sm d-inline-flex align-items-center text-sm fw-semibold px-3" style="background: #7b1fa2; border-color: #7b1fa2;">
                <i class="fas fa-plus me-1.5"></i> <span>Form Pengajuan Baru</span>
            </a>
        </div>
    </div>

    <!-- 2. KPI Statistik Cards -->
    <div class="row g-2 g-md-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="card stat-card-pengajuan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-layer-group text-primary"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Total Seluruh</small>
                        <div class="stat-number-responsive text-primary text-truncate"><?= number_format($totalAll) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card-pengajuan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-clock text-warning"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Menunggu Approval</small>
                        <div class="stat-number-responsive text-warning text-truncate"><?= number_format($pendingCount) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card-pengajuan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-umbrella-beach text-info"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Pengajuan Cuti</small>
                        <div class="stat-number-responsive text-info text-truncate"><?= number_format($totalCuti) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card-pengajuan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-danger bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-comment-dots text-danger"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Keluhan Terdata</small>
                        <div class="stat-number-responsive text-danger text-truncate"><?= number_format($totalKeluhan) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Tabel Semua Pengajuan -->
    <div class="card pengajuan-card-modern p-3 p-md-4 mb-3">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold text-dark mb-0 fs-6 fs-md-5 d-flex align-items-center">
                    <i class="fas fa-list-alt me-2" style="color: #7b1fa2;"></i> Daftar Pengajuan Administrasi & Permohonan
                </h5>
                <small class="text-muted text-xs d-block mt-0.5">Geser tabel kesamping jika membuka di tampilan seluler/mobile.</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <input type="text" id="searchTablePengajuan" class="form-control form-control-sm rounded-pill px-3" style="max-width: 220px;" placeholder="Cari nomor / judul / status...">
                <a href="<?= base_url('admin/pengajuan/tambah') ?>" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold text-nowrap shadow-sm" style="background: #7b1fa2; border-color: #7b1fa2;">
                    <i class="fas fa-plus me-1"></i> Tambah Pengajuan
                </a>
            </div>
        </div>

        <div class="table-scroll-wrapper">
            <table class="table table-hover align-middle text-sm mb-0" id="pengajuanTable">
                <thead class="table-light text-nowrap">
                    <tr>
                        <th width="16%">No. Pengajuan / Tanggal</th>
                        <th width="20%">Judul & Kategori</th>
                        <th width="16%">Waktu Pelaksanaan</th>
                        <th width="18%">Keterangan / Alasan</th>
                        <th width="10%" class="text-center">Bukti Foto</th>
                        <th width="10%">Status</th>
                        <th width="14%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($pengajuan)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data pengajuan permohonan / izin terdata.</td></tr>
                    <?php else: ?>
                        <?php foreach($pengajuan as $p): ?>
                        <tr>
                            <td class="text-nowrap">
                                <div class="fw-bold text-dark"><i class="fas fa-file-alt text-primary me-1"></i><?= esc($p['nomor_pengajuan']) ?></div>
                                <small class="text-muted text-xs"><i class="fas fa-calendar me-1"></i><?= date('d M Y', strtotime($p['tanggal_pengajuan'])) ?></small>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= esc($p['judul_pengajuan']) ?></div>
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-0.5 rounded-pill text-xs fw-semibold">
                                    <?= esc($p['kategori_pengajuan']) ?>
                                </span>
                            </td>
                            <td class="text-nowrap">
                                <small class="text-dark fw-semibold d-block"><?= date('d M Y', strtotime($p['tanggal_mulai'])) ?> - <?= date('d M Y', strtotime($p['tanggal_selesai'])) ?></small>
                            </td>
                            <td>
                                <small class="text-dark d-block text-truncate" style="max-width: 200px;" title="<?= esc($p['keterangan']) ?>">
                                    <?= esc($p['keterangan'] ?: '-') ?>
                                </small>
                            </td>
                            <td class="text-center">
                                <?php if (!empty($p['bukti_foto'])): ?>
                                    <a href="<?= base_url($p['bukti_foto']) ?>" target="_blank" title="Lihat foto bukti">
                                        <img src="<?= base_url($p['bukti_foto']) ?>" alt="Bukti" class="rounded-3 shadow-sm border" style="width: 38px; height: 38px; object-fit: cover;">
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted text-xs italic">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-nowrap">
                                <?php
                                    $st = strtolower($p['status'] ?? 'menunggu');
                                    $badge = 'bg-warning text-dark';
                                    if ($st === 'disetujui') $badge = 'bg-success text-white';
                                    if ($st === 'ditolak') $badge = 'bg-danger text-white';
                                ?>
                                <span class="badge <?= $badge ?> px-2.5 py-1 rounded-pill text-xs fw-semibold">
                                    <?= strtoupper(esc($p['status'] ?? 'Menunggu')) ?>
                                </span>
                            </td>
                            <td class="text-center text-nowrap">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <a href="<?= base_url('admin/pengajuan/detail/'.$p['id']) ?>" class="btn btn-xs btn-outline-info rounded-pill px-2.5 py-1 text-xs fw-semibold">
                                        <i class="fas fa-eye me-1"></i> Detail
                                    </a>
                                    <?php if ($st === 'menunggu'): ?>
                                        <a href="<?= base_url('admin/pengajuan/edit/'.$p['id']) ?>" class="btn btn-xs btn-outline-warning rounded-pill px-2.5 py-1 text-xs fw-semibold">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </a>
                                        <button type="button" onclick="confirmDeletePengajuan(<?= $p['id'] ?>, '<?= esc($p['judul_pengajuan'], 'js') ?>')" class="btn btn-xs btn-outline-danger rounded-pill px-2 py-1 text-xs fw-semibold">
                                            <i class="fas fa-trash me-1"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
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
        confirmButtonColor: '#7b1fa2',
        timer: 3000,
        timerProgressBar: true,
        customClass: { popup: 'rounded-4 shadow-lg' }
    });
});
</script>
<?php endif; ?>

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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchTablePengajuan');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#pengajuanTable tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }
});

function confirmDeletePengajuan(id, judul) {
    Swal.fire({
        title: 'Hapus Pengajuan?',
        text: 'Pengajuan "' + judul + '" akan dihapus permanen.',
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
            form.action = '<?= base_url('admin/pengajuan/delete') ?>/' + id;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function confirmDeleteCuti(id, nomor) {
    Swal.fire({
        title: 'Hapus Pengajuan Cuti?',
        text: 'Pengajuan cuti "' + nomor + '" akan dihapus.',
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
            form.action = '<?= base_url('admin/pengajuan/cuti/delete') ?>/' + id;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>

<?= view('admin/templates/footer', $data) ?>
