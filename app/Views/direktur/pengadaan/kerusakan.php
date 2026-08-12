<?php
$title = $title ?? 'Kerusakan Alat';
$templateData = [
    'title' => $title,
    'user'  => session()->get('user') ?? ['name' => 'Direktur', 'role' => 'direktur'],
    'active' => 'pengadaan'
];

$totalLaporan = count($kerusakan ?? []);
$ringanCount = 0;
$sedangCount = 0;
$beratCount = 0;

foreach($kerusakan as $item) {
    $tk = strtolower($item['tingkat_kerusakan'] ?? '');
    if($tk === 'sedang') $sedangCount++;
    elseif($tk === 'berat') $beratCount++;
    else $ringanCount++;
}
?>

<?= view('direktur/templates/header', $templateData) ?>
<?= view('direktur/templates/sidebar', $templateData) ?>
<?= view('direktur/templates/navbar', $templateData) ?>

<style>
    /* Prevent Any Horizontal Page Overflow */
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

    /* Glassmorphism & Modern Card Styling */
    .pengadaan-card-modern {
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

    .pengadaan-card-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 32px rgba(30, 60, 114, 0.12) !important;
    }

    .stat-card-pengadaan {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.8);
        border-radius: 16px;
        box-shadow: 0 6px 20px rgba(30, 60, 114, 0.05);
        transition: transform 0.2s ease;
    }

    .stat-card-pengadaan:hover {
        transform: translateY(-2px);
    }

    .stat-number-responsive {
        font-size: clamp(1.1rem, 2.5vw, 1.4rem);
        font-weight: 700;
        line-height: 1.2;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 5px 12px;
        border-radius: 30px;
        font-size: 0.76rem;
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    
    .status-pill-ringan {
        background: rgba(13, 202, 240, 0.12);
        color: #0dcaf0;
        border: 1px solid rgba(13, 202, 240, 0.25);
    }

    .status-pill-sedang {
        background: rgba(255, 193, 7, 0.15);
        color: #b58100;
        border: 1px solid rgba(255, 193, 7, 0.3);
    }

    .status-pill-berat {
        background: rgba(220, 53, 69, 0.12);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.25);
    }

    /* Inner Table Scroll Container - Keeps Page Fit 100% */
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
        min-width: 760px !important;
        width: 100% !important;
        margin-bottom: 0 !important;
    }

    .table-scroll-wrapper::-webkit-scrollbar {
        height: 6px;
    }
    .table-scroll-wrapper::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    .table-scroll-wrapper::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    @media (max-width: 767.98px) {
        .header-mobile-flex {
            flex-direction: column;
            align-items: stretch !important;
        }
        .header-btn-group {
            width: 100%;
        }
        .header-btn-group .btn {
            width: 100%;
            justify-content: center;
            font-size: 0.85rem;
            padding: 9px 12px;
        }
        .stat-card-pengadaan {
            padding: 12px !important;
        }
        .stat-icon-wrapper {
            width: 40px !important;
            height: 40px !important;
            min-width: 40px;
        }
    }
</style>

<div class="container-fluid py-3 py-md-4">

    <!-- 1. Header Section -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-3 border border-light header-mobile-flex gap-3">
        <div class="d-flex align-items-center overflow-hidden">
            <div class="bg-gradient-primary text-white rounded-3 p-2 me-2.5 me-md-3 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 44px; height: 44px;">
                <i class="fas fa-tools fs-5"></i>
            </div>
            <div class="overflow-hidden">
                <h4 class="mb-0 fw-bold text-dark text-truncate" style="font-size: 1.25rem;">Laporan Kerusakan Alat & Inventaris</h4>
                <small class="text-muted d-none d-sm-inline">Monitoring kerusakan peralatan kerja dan status tindak perbaikannya.</small>
            </div>
        </div>
        <div class="header-btn-group">
            <button class="btn btn-danger rounded-pill shadow-sm d-inline-flex align-items-center fw-semibold" data-bs-toggle="modal" data-bs-target="#tambahKerusakanModal">
                <i class="fas fa-exclamation-triangle me-1.5"></i> <span>Laporkan Kerusakan</span>
            </button>
        </div>
    </div>

    <!-- 2. KPI Statistik Cards -->
    <div class="row g-2 g-md-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="card stat-card-pengadaan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center stat-icon-wrapper" style="width: 44px; height: 44px;">
                        <i class="fas fa-tools text-primary"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Total Laporan</small>
                        <div class="stat-number-responsive text-primary text-truncate"><?= number_format($totalLaporan) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card-pengadaan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center stat-icon-wrapper" style="width: 44px; height: 44px;">
                        <i class="fas fa-info-circle text-info"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Kerusakan Ringan</small>
                        <div class="stat-number-responsive text-info text-truncate"><?= number_format($ringanCount) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card-pengadaan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center stat-icon-wrapper" style="width: 44px; height: 44px;">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Kerusakan Sedang</small>
                        <div class="stat-number-responsive text-warning text-truncate"><?= number_format($sedangCount) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card-pengadaan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-danger bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center stat-icon-wrapper" style="width: 44px; height: 44px;">
                        <i class="fas fa-ban text-danger"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Kerusakan Berat</small>
                        <div class="stat-number-responsive text-danger text-truncate"><?= number_format($beratCount) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Tabel Data Kerusakan Alat -->
    <div class="card pengadaan-card-modern p-3 p-md-4 mb-3">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold text-dark mb-0 fs-6 fs-md-5 d-flex align-items-center">
                    <i class="fas fa-wrench text-danger me-2"></i> Daftar Laporan Kerusakan Alat
                </h5>
                <small class="text-muted text-xs d-block mt-0.5">Geser tabel kesamping untuk melihat detail kerusakan di mobile.</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <input type="text" id="searchTableKerusakan" class="form-control form-control-sm rounded-pill px-3" style="max-width: 200px;" placeholder="Cari nama alat / pelapor...">
                <a href="<?= base_url('direktur/pengadaan/kerusakan/tambah') ?>" class="btn btn-sm btn-danger rounded-pill px-3 fw-semibold text-nowrap shadow-sm">
                    <i class="fas fa-plus me-1"></i> Laporkan Kerusakan
                </a>
            </div>
        </div>

        <div class="table-scroll-wrapper">
            <table class="table table-hover align-middle text-sm mb-0" id="kerusakanTable">
                <thead class="table-light text-nowrap">
                    <tr>
                        <th width="20%">Kode & Nama Alat</th>
                        <th width="14%">Lokasi & Pelapor</th>
                        <th width="12%">Kerusakan</th>
                        <th width="22%">Teknisi & Tujuan Service</th>
                        <th width="14%">Status Perbaikan</th>
                        <th width="18%" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($kerusakan)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada laporan kerusakan alat terdaftar.</td></tr>
                    <?php else: ?>
                        <?php foreach($kerusakan as $k): ?>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark"><?= esc($k['nama_alat']) ?></div>
                                <small class="text-muted text-xs"><i class="fas fa-barcode me-1"></i><?= esc($k['kode_laporan'] ?: 'KRK-00'.$k['id']) ?></small>
                            </td>
                            <td>
                                <small class="text-dark fw-semibold d-block"><i class="fas fa-user-edit me-1 text-primary"></i><?= esc($k['pelapor'] ?? 'Direktur') ?></small>
                                <small class="text-muted text-xs"><i class="fas fa-map-marker-alt me-1 text-danger"></i><?= esc($k['lokasi_alat'] ?: '-') ?></small>
                            </td>
                            <td>
                                <?php
                                    $tk = strtolower($k['tingkat_kerusakan'] ?? '');
                                    $pillClass = 'status-pill-ringan';
                                    if ($tk === 'sedang') $pillClass = 'status-pill-sedang';
                                    if ($tk === 'berat') $pillClass = 'status-pill-berat';
                                ?>
                                <span class="status-pill <?= $pillClass ?>">
                                    <i class="fas fa-circle fs-6 me-1.5" style="font-size: 0.4rem !important;"></i>
                                    <?= strtoupper(esc($k['tingkat_kerusakan'])) ?>
                                </span>
                            </td>
                            <td>
                                <small class="text-dark fw-semibold d-block text-truncate" style="max-width: 200px;">
                                    <i class="fas fa-user-cog text-warning me-1"></i> <?= esc($k['teknisi_pengurus'] ?: 'Belum ditentukan') ?>
                                </small>
                                <small class="text-muted text-xs d-block text-truncate" style="max-width: 200px;">
                                    <i class="fas fa-location-dot text-info me-1"></i> Ke: <?= esc($k['lokasi_perbaikan'] ?: 'Workshop IT') ?>
                                </small>
                            </td>
                            <td>
                                <?php
                                    $st = strtolower($k['status_tindakan'] ?? 'dilaporkan');
                                    $stBg = 'bg-secondary';
                                    $stLabel = 'Dilaporkan';
                                    if ($st === 'dalam_perbaikan' || $st === 'proses_perbaikan') {
                                        $stBg = 'bg-primary';
                                        $stLabel = 'Dalam Perbaikan';
                                    } elseif ($st === 'selesai') {
                                        $stBg = 'bg-success';
                                        $stLabel = 'Selesai Servis';
                                    } elseif ($st === 'rusak_total') {
                                        $stBg = 'bg-danger';
                                        $stLabel = 'Rusak Total';
                                    }
                                ?>
                                <span class="badge <?= $stBg ?> px-2.5 py-1 rounded-pill text-xs fw-semibold">
                                    <?= $stLabel ?>
                                </span>
                            </td>
                            <td class="text-end text-nowrap">
                                <div class="btn-group">
                                    <a href="<?= base_url('direktur/pengadaan/kerusakan/detail/'.$k['id']) ?>" class="btn btn-xs btn-outline-info rounded-pill px-2 py-1 text-xs fw-semibold me-1" title="Detail Laporan">
                                        <i class="fas fa-eye me-1"></i> Detail
                                    </a>
                                    <a href="<?= base_url('direktur/pengadaan/kerusakan/edit/'.$k['id']) ?>" class="btn btn-xs btn-outline-warning rounded-pill px-2 py-1 text-xs fw-semibold me-1" title="Edit Laporan">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-2 py-1 text-xs fw-semibold" title="Hapus Laporan" onclick="confirmDeleteKerusakan('<?= base_url('direktur/pengadaan/kerusakan/delete/'.$k['id']) ?>', '<?= esc(addslashes($k['nama_alat'])) ?>')">
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

<!-- SweetAlert2 CDN & Custom Interactivity Scripts -->
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

<?php if (session()->getFlashdata('error')): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: '<?= esc(session()->getFlashdata('error')) ?>',
        confirmButtonColor: '#dc3545',
        customClass: { popup: 'rounded-4 shadow-lg' }
    });
});
</script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchTableKerusakan');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#kerusakanTable tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }
});

function confirmDeleteKerusakan(url, name) {
    Swal.fire({
        title: 'Apakah Anda Yakin?',
        text: "Anda akan menghapus laporan kerusakan '" + name + "'. Tindakan ini tidak dapat dibatalkan!",
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
            form.action = url;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>

<?= view('direktur/templates/footer', $templateData) ?>
