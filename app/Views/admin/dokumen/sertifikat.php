<?php
$title = $title ?? 'Dokumen Sertifikat';
$data = [
    'title'  => $title,
    'user'   => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin'],
    'active' => 'dokumen'
];

$totalSertifikat = count($sertifikat ?? []);
$corporateCount = 0;
$karyawanCount = 0;
$aktifCount = 0;

foreach($sertifikat as $item) {
    if (empty($item['karyawan_id'])) $corporateCount++;
    else $karyawanCount++;
    if (strtolower($item['status'] ?? 'aktif') === 'aktif') $aktifCount++;
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
        min-width: 820px !important;
        width: 100% !important;
        margin-bottom: 0 !important;
    }
</style>

<div class="container-fluid py-3 py-md-4">

    <!-- 1. Header Section -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-3 border border-light flex-wrap gap-3">
        <div class="d-flex align-items-center overflow-hidden">
            <div class="bg-primary text-white rounded-3 p-2 me-3 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 44px; height: 44px;">
                <i class="fas fa-award fs-5"></i>
            </div>
            <div class="overflow-hidden">
                <h4 class="mb-0 fw-bold text-dark text-truncate" style="font-size: 1.25rem;">Dokumen Sertifikat & Keahlian</h4>
                <small class="text-muted d-none d-sm-inline">Arsip sertifikasi ISO perusahaan dan sertifikasi kompetensi SDM / Karyawan.</small>
            </div>
        </div>
        <div>
            <a href="<?= base_url('admin/dokumen/sertifikat/tambah') ?>" class="btn btn-primary rounded-pill shadow-sm d-inline-flex align-items-center text-sm fw-semibold px-3">
                <i class="fas fa-plus me-1.5"></i> <span>Tambah Sertifikat Baru</span>
            </a>
        </div>
    </div>

    <!-- 2. KPI Statistik Cards -->
    <div class="row g-2 g-md-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="card stat-card-dokumen p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-certificate text-primary"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Total Sertifikat</small>
                        <div class="stat-number-responsive text-primary text-truncate"><?= number_format($totalSertifikat) ?></div>
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
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Corporate (PT)</small>
                        <div class="stat-number-responsive text-info text-truncate"><?= number_format($corporateCount) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card-dokumen p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-user-graduate text-warning"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">SDM / Karyawan</small>
                        <div class="stat-number-responsive text-warning text-truncate"><?= number_format($karyawanCount) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card-dokumen p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Status Aktif</small>
                        <div class="stat-number-responsive text-success text-truncate"><?= number_format($aktifCount) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Tabel Dokumen Sertifikat -->
    <div class="card dokumen-card-modern p-3 p-md-4 mb-3">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold text-dark mb-0 fs-6 fs-md-5 d-flex align-items-center">
                    <i class="fas fa-file-signature text-primary me-2"></i> Daftar Sertifikasi & Kualifikasi Kompetensi
                </h5>
                <small class="text-muted text-xs d-block mt-0.5">Geser tabel kesamping untuk melihat detail sertifikat di mobile.</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <input type="text" id="searchTableSertifikat" class="form-control form-control-sm rounded-pill px-3" style="max-width: 220px;" placeholder="Cari nama / penerbit...">
                <a href="<?= base_url('admin/dokumen/sertifikat/tambah') ?>" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold text-nowrap shadow-sm">
                    <i class="fas fa-plus me-1"></i> Tambah Sertifikat
                </a>
            </div>
        </div>

        <div class="table-scroll-wrapper">
            <table class="table table-hover align-middle text-sm mb-0" id="sertifikatTable">
                <thead class="table-light text-nowrap">
                    <tr>
                        <th width="24%">Nama Sertifikat</th>
                        <th width="18%">Lembaga Penerbit</th>
                        <th width="18%">Pemegang / Karyawan</th>
                        <th width="14%">Masa Berlaku</th>
                        <th width="10%">Status</th>
                        <th width="6%" class="text-center">File</th>
                        <th width="10%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($sertifikat)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data sertifikat terdata.</td></tr>
                    <?php else: ?>
                        <?php foreach($sertifikat as $s): ?>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark"><?= esc($s['nama_sertifikat']) ?></div>
                                <small class="text-muted text-xs"><i class="fas fa-barcode me-1"></i><?= esc($s['nomor_sertifikat'] ?: '-') ?></small>
                            </td>
                            <td class="fw-semibold text-dark text-nowrap">
                                <i class="fas fa-building text-primary me-1"></i><?= esc($s['penerbit']) ?>
                            </td>
                            <td class="text-nowrap">
                                <?php if (!empty($s['karyawan'])): ?>
                                    <small class="text-dark fw-bold d-block"><i class="fas fa-user-tie me-1 text-info"></i><?= esc($s['karyawan']) ?></small>
                                <?php else: ?>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-0.5 rounded-pill text-xs">
                                        Perusahaan (Corporate)
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-nowrap">
                                <?php if($s['masa_berlaku']): ?>
                                    <span class="badge bg-warning bg-opacity-10 text-dark px-2.5 py-1 rounded-pill text-xs fw-semibold">
                                        <i class="fas fa-calendar-alt me-1 text-warning"></i><?= date('d M Y', strtotime($s['masa_berlaku'])) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-success bg-opacity-10 text-success px-2.5 py-1 rounded-pill text-xs fw-semibold">
                                        <i class="fas fa-infinity me-1 text-success"></i>Permanen
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                    $st = strtolower($s['status'] ?? 'aktif');
                                    $badge = 'bg-success';
                                    if ($st == 'kadaluarsa') $badge = 'bg-danger';
                                    if ($st == 'proses_perpanjangan') $badge = 'bg-warning text-dark';
                                ?>
                                <span class="badge <?= $badge ?> px-2.5 py-1 rounded-pill text-xs fw-semibold">
                                    <?= strtoupper(esc($s['status'])) ?>
                                </span>
                            </td>
                            <td class="text-center text-nowrap">
                                <?php if($s['file_path']): ?>
                                    <a href="<?= base_url('uploads/sertifikat/'.$s['file_path']) ?>" target="_blank" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1 text-xs fw-semibold">
                                        <i class="fas fa-eye me-1"></i> Lihat
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted text-xs">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center text-nowrap">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <a href="<?= base_url('admin/dokumen/sertifikat/detail/'.$s['id']) ?>" class="btn btn-xs btn-outline-info rounded-pill px-2.5 py-1 text-xs fw-semibold" title="Detail Sertifikat">
                                        <i class="fas fa-eye me-1"></i> Detail
                                    </a>
                                    <a href="<?= base_url('admin/dokumen/sertifikat/edit/'.$s['id']) ?>" class="btn btn-xs btn-outline-warning rounded-pill px-2.5 py-1 text-xs fw-semibold" title="Edit Sertifikat">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>
                                    <button type="button" onclick="confirmDeleteSertifikat(<?= $s['id'] ?>, '<?= esc($s['nama_sertifikat'], 'js') ?>')" class="btn btn-xs btn-outline-danger rounded-pill px-2.5 py-1 text-xs fw-semibold" title="Hapus Sertifikat">
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
    const searchInput = document.getElementById('searchTableSertifikat');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#sertifikatTable tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }
});

function confirmDeleteSertifikat(id, nama) {
    Swal.fire({
        title: 'Hapus Sertifikat?',
        text: 'Sertifikat "' + nama + '" akan dihapus permanen dari sistem.',
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
            form.action = '<?= base_url('admin/dokumen/sertifikat/delete') ?>/' + id;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>

<?= view('admin/templates/footer', $data) ?>
