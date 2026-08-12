<?php
$data = [
    'title'  => 'Keluhan Karyawan',
    'active' => 'karyawan',
    'user'   => ['name' => session()->get('name') ?? 'Direktur', 'role' => 'direktur']
];

$totalKeluhan = count($keluhan ?? []);
$waitingCount = 0;
$diprosesCount = 0;
$selesaiCount = 0;

foreach($keluhan as $k) {
    $st = strtolower($k['status'] ?? '');
    if ($st === 'menunggu') $waitingCount++;
    if ($st === 'diproses') $diprosesCount++;
    if ($st === 'selesai') $selesaiCount++;
}
?>

<?= view('direktur/templates/header', $data) ?>
<?= view('direktur/templates/sidebar', $data) ?>
<?= view('direktur/templates/navbar', $data) ?>

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
        padding-left: 14px !important;
        padding-right: 14px !important;
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
    .dir-keluhan-card {
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        border-radius: 18px !important;
        box-shadow: 0 8px 25px rgba(30, 60, 114, 0.08) !important;
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
        min-width: 880px !important;
        width: 100% !important;
        margin-bottom: 0 !important;
    }
</style>

<div class="container-fluid py-3 py-md-4">

    <!-- 1. Header Page -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-3 border border-light flex-wrap gap-3">
        <div class="d-flex align-items-center overflow-hidden">
            <div class="bg-danger text-white rounded-3 p-2 me-3 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 44px; height: 44px; background: linear-gradient(135deg, #d32f2f, #b71c1c) !important;">
                <i class="fas fa-comments fs-5"></i>
            </div>
            <div class="overflow-hidden">
                <h4 class="mb-0 fw-bold text-dark text-truncate" style="font-size: 1.25rem;">Monitoring & Tanggapan Keluhan Karyawan</h4>
                <small class="text-muted d-none d-sm-inline">Tinjau dan beri tanggapan solusi atas keluhan fasilitas, lingkungan, & kendala kerja karyawan.</small>
            </div>
        </div>
    </div>

    <!-- 2. Ringkasan Stats -->
    <div class="row g-2 g-md-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="card p-3 rounded-4 border-0 shadow-sm bg-white">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-danger bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fas fa-comments text-danger"></i>
                    </div>
                    <div>
                        <small class="text-muted text-xs uppercase fw-bold d-block">Total Keluhan</small>
                        <div class="fs-4 fw-bold text-danger"><?= number_format($totalKeluhan) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card p-3 rounded-4 border-0 shadow-sm bg-white">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fas fa-clock text-warning"></i>
                    </div>
                    <div>
                        <small class="text-muted text-xs uppercase fw-bold d-block">Belum Ditanggapi</small>
                        <div class="fs-4 fw-bold text-warning"><?= number_format($waitingCount) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card p-3 rounded-4 border-0 shadow-sm bg-white">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fas fa-spinner text-info"></i>
                    </div>
                    <div>
                        <small class="text-muted text-xs uppercase fw-bold d-block">Sedang Diproses</small>
                        <div class="fs-4 fw-bold text-info"><?= number_format($diprosesCount) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card p-3 rounded-4 border-0 shadow-sm bg-white">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                    <div>
                        <small class="text-muted text-xs uppercase fw-bold d-block">Selesai Ditindak</small>
                        <div class="fs-4 fw-bold text-success"><?= number_format($selesaiCount) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Tabel Keluhan Direktur -->
    <div class="card dir-keluhan-card p-3 p-md-4 mb-3">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold text-dark mb-0 fs-6 fs-md-5 d-flex align-items-center">
                    <i class="fas fa-list me-2 text-danger"></i> Daftar Keluhan Karyawan
                </h5>
                <small class="text-muted text-xs d-block mt-0.5">Keluhan yang diajukan dari akun Admin & Karyawan.</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <input type="text" id="searchTableDirKeluhan" class="form-control form-control-sm rounded-pill px-3" style="max-width: 220px;" placeholder="Cari nama / judul keluhan...">
            </div>
        </div>

        <div class="table-scroll-wrapper">
            <table class="table table-hover align-middle text-sm mb-0" id="dirKeluhanTable">
                <thead class="table-light text-nowrap">
                    <tr>
                        <th width="15%">Tanggal</th>
                        <th width="20%">Pelapor / Karyawan</th>
                        <th width="18%">Kategori</th>
                        <th width="22%">Judul & Deskripsi</th>
                        <th width="12%">Status</th>
                        <th width="13%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($keluhan)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada keluhan karyawan yang tercatat.</td></tr>
                    <?php else: ?>
                        <?php foreach($keluhan as $k): ?>
                        <tr>
                            <td class="text-nowrap">
                                <small class="text-dark fw-bold d-block"><i class="fas fa-calendar me-1 text-muted"></i><?= date('d M Y', strtotime($k['tanggal'])) ?></small>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= esc($k['nama_lengkap'] ?: 'Admin User') ?></div>
                                <small class="text-muted text-xs"><?= esc($k['divisi'] ?: 'Administrasi') ?> - <?= esc($k['jabatan'] ?: 'Staff') ?></small>
                            </td>
                            <td class="text-nowrap">
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2.5 py-0.5 rounded-pill text-xs fw-semibold">
                                    <?= esc($k['kategori']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark text-truncate" style="max-width: 220px;"><?= esc($k['judul']) ?></div>
                                <small class="text-muted d-block text-truncate" style="max-width: 220px;"><?= esc($k['deskripsi']) ?></small>
                            </td>
                            <td class="text-nowrap">
                                <?php
                                    $st = strtolower($k['status'] ?? 'menunggu');
                                    $badge = 'bg-warning text-dark';
                                    if ($st === 'diproses') $badge = 'bg-info text-white';
                                    if ($st === 'selesai') $badge = 'bg-success text-white';
                                    if ($st === 'ditolak') $badge = 'bg-danger text-white';
                                ?>
                                <span class="badge <?= $badge ?> px-2.5 py-1 rounded-pill text-xs fw-semibold">
                                    <?= strtoupper(esc($k['status'] ?? 'Menunggu')) ?>
                                </span>
                            </td>
                            <td class="text-center text-nowrap">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <a href="<?= base_url('direktur/karyawan/keluhan/detail/'.$k['id']) ?>" class="btn btn-xs btn-outline-info rounded-pill px-2.5 py-1 text-xs fw-semibold">
                                        <i class="fas fa-reply me-1"></i> Tanggapi
                                    </a>
                                    <button type="button" onclick="confirmDeleteDirKeluhan(<?= $k['id'] ?>, '<?= esc($k['judul'], 'js') ?>')" class="btn btn-xs btn-outline-danger rounded-pill px-2 py-1 text-xs fw-semibold">
                                        <i class="fas fa-trash"></i>
                                    </button>
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
        confirmButtonColor: '#d32f2f',
        timer: 3000,
        timerProgressBar: true,
        customClass: { popup: 'rounded-4 shadow-lg' }
    });
});
</script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchTableDirKeluhan');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#dirKeluhanTable tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }
});

function confirmDeleteDirKeluhan(id, judul) {
    Swal.fire({
        title: 'Hapus Keluhan?',
        text: 'Keluhan "' + judul + '" akan dihapus.',
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
            form.action = '<?= base_url('direktur/karyawan/keluhan/delete') ?>/' + id;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>

<?= view('direktur/templates/footer', $data) ?>
