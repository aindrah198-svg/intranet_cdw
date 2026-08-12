<?php
$title = $title ?? 'Kontak Project';
$data = [
    'title'  => $title,
    'user'   => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin'],
    'active' => 'dokumen'
];

$totalKontak = count($kontak ?? []);
$klienCount = 0;
$projectCount = 0;
$waCount = 0;

foreach($kontak as $item) {
    if (!empty($item['perusahaan_klien'])) $klienCount++;
    if (!empty($item['project_id']) || !empty($item['nama_project'])) $projectCount++;
    if (!empty($item['telepon'])) $waCount++;
}
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
    .dokumen-card-modern {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        border-radius: 18px !important;
        box-shadow: 0 8px 25px rgba(30, 60, 114, 0.06), 0 2px 6px rgba(0, 0, 0, 0.02) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .dokumen-card-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 32px rgba(30, 60, 114, 0.12) !important;
    }
    .stat-card-dokumen {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.8);
        border-radius: 16px;
        box-shadow: 0 6px 20px rgba(30, 60, 114, 0.05);
        transition: transform 0.2s ease;
    }
    .stat-card-dokumen:hover {
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
        min-width: 840px !important;
        width: 100% !important;
        margin-bottom: 0 !important;
    }
</style>

<div class="container-fluid py-3 py-md-4">

    <!-- 1. Header Section -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-3 border border-light flex-wrap gap-3">
        <div class="d-flex align-items-center overflow-hidden">
            <div class="bg-primary text-white rounded-3 p-2 me-3 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 44px; height: 44px;">
                <i class="fas fa-address-book fs-5"></i>
            </div>
            <div class="overflow-hidden">
                <h4 class="mb-0 fw-bold text-dark text-truncate" style="font-size: 1.25rem;">Kontak Project & Stakeholder</h4>
                <small class="text-muted d-none d-sm-inline">Direktori kontak PIC Klien, Subkontraktor, Vendor & Stakeholder Proyek CDW.</small>
            </div>
        </div>
        <div>
            <a href="<?= base_url('admin/dokumen/kontak/tambah') ?>" class="btn btn-primary rounded-pill shadow-sm d-inline-flex align-items-center text-sm fw-semibold px-3">
                <i class="fas fa-plus me-1.5"></i> <span>Tambah Kontak PIC</span>
            </a>
        </div>
    </div>

    <!-- 2. KPI Statistik Cards -->
    <div class="row g-2 g-md-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="card stat-card-dokumen p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-users text-primary"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Total Kontak PIC</small>
                        <div class="stat-number-responsive text-primary text-truncate"><?= number_format($totalKontak) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card-dokumen p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-building text-info"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Perusahaan Klien</small>
                        <div class="stat-number-responsive text-info text-truncate"><?= number_format($klienCount) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card-dokumen p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-project-diagram text-warning"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Project Terkait</small>
                        <div class="stat-number-responsive text-warning text-truncate"><?= number_format($projectCount) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card-dokumen p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fab fa-whatsapp text-success"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">WA Terhubung</small>
                        <div class="stat-number-responsive text-success text-truncate"><?= number_format($waCount) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Tabel Kontak Project -->
    <div class="card dokumen-card-modern p-3 p-md-4 mb-3">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold text-dark mb-0 fs-6 fs-md-5 d-flex align-items-center">
                    <i class="fas fa-id-card text-primary me-2"></i> Direktori Kontak Stakeholder Proyek
                </h5>
                <small class="text-muted text-xs d-block mt-0.5">Geser tabel kesamping untuk melihat rincian kontak di mobile.</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <input type="text" id="searchTableKontak" class="form-control form-control-sm rounded-pill px-3" style="max-width: 220px;" placeholder="Cari nama / klien / project...">
                <a href="<?= base_url('admin/dokumen/kontak/tambah') ?>" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold text-nowrap shadow-sm">
                    <i class="fas fa-plus me-1"></i> Tambah Kontak
                </a>
            </div>
        </div>

        <div class="table-scroll-wrapper">
            <table class="table table-hover align-middle text-sm mb-0" id="kontakTable">
                <thead class="table-light text-nowrap">
                    <tr>
                        <th width="22%">Nama PIC / Kontak</th>
                        <th width="18%">Perusahaan / Instansi</th>
                        <th width="12%">Jabatan</th>
                        <th width="16%">Project Terkait</th>
                        <th width="14%">No. Telepon / WA</th>
                        <th width="8%">Email</th>
                        <th width="10%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($kontak)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada kontak project terdata.</td></tr>
                    <?php else: ?>
                        <?php foreach($kontak as $k): ?>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark"><i class="fas fa-user-circle text-primary me-1"></i><?= esc($k['nama_kontak']) ?></div>
                                <small class="text-muted text-xs d-block text-truncate" style="max-width: 200px;"><?= esc($k['catatan'] ?: '-') ?></small>
                            </td>
                            <td class="fw-semibold text-dark text-nowrap">
                                <i class="fas fa-building text-info me-1"></i><?= esc($k['perusahaan_klien'] ?: '-') ?>
                            </td>
                            <td class="text-nowrap">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2.5 py-1 rounded-pill text-xs fw-semibold">
                                    <?= esc($k['jabatan'] ?: 'PIC') ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($k['nama_project'])): ?>
                                    <small class="text-dark fw-bold d-block"><i class="fas fa-project-diagram text-warning me-1"></i><?= esc($k['nama_project']) ?></small>
                                    <small class="text-muted text-xs"><?= esc($k['kode_project'] ?: '') ?></small>
                                <?php else: ?>
                                    <small class="text-muted italic">Non-Project / Umum</small>
                                <?php endif; ?>
                            </td>
                            <td class="text-nowrap">
                                <?php
                                    $rawHp = preg_replace('/[^0-9]/', '', $k['telepon'] ?? '');
                                    if (substr($rawHp, 0, 1) === '0') {
                                        $rawHp = '62' . substr($rawHp, 1);
                                    }
                                ?>
                                <?php if(!empty($k['telepon'])): ?>
                                    <a href="https://wa.me/<?= $rawHp ?>" target="_blank" class="btn btn-xs btn-outline-success rounded-pill px-2.5 py-1 text-xs fw-bold">
                                        <i class="fab fa-whatsapp me-1"></i><?= esc($k['telepon']) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted text-xs">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-nowrap">
                                <?php if(!empty($k['email'])): ?>
                                    <a href="mailto:<?= esc($k['email']) ?>" class="text-primary text-decoration-none text-xs fw-semibold">
                                        <i class="fas fa-envelope me-1"></i><?= esc($k['email']) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted text-xs">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center text-nowrap">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <a href="<?= base_url('admin/dokumen/kontak/detail/'.$k['id']) ?>" class="btn btn-xs btn-outline-info rounded-pill px-2.5 py-1 text-xs fw-semibold" title="Detail Kontak">
                                        <i class="fas fa-eye me-1"></i> Detail
                                    </a>
                                    <a href="<?= base_url('admin/dokumen/kontak/edit/'.$k['id']) ?>" class="btn btn-xs btn-outline-warning rounded-pill px-2.5 py-1 text-xs fw-semibold" title="Edit Kontak">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>
                                    <button type="button" onclick="confirmDeleteKontak(<?= $k['id'] ?>, '<?= esc($k['nama_kontak'], 'js') ?>')" class="btn btn-xs btn-outline-danger rounded-pill px-2.5 py-1 text-xs fw-semibold" title="Hapus Kontak">
                                        <i class="fas fa-trash me-1"></i> Hapus
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
        confirmButtonColor: '#0d6efd',
        timer: 3000,
        timerProgressBar: true,
        customClass: { popup: 'rounded-4 shadow-lg' }
    });
});
</script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchTableKontak');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#kontakTable tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }
});

function confirmDeleteKontak(id, nama) {
    Swal.fire({
        title: 'Hapus Kontak PIC?',
        text: 'Kontak "' + nama + '" akan dihapus dari sistem.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-trash me-1"></i> Ya, Hapus!',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-4 shadow-lg' }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= base_url('admin/dokumen/kontak/delete') ?>/' + id;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>

<?= view('admin/templates/footer', $data) ?>
