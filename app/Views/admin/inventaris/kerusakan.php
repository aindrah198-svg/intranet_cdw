<?php
$title = $title ?? 'Kerusakan Alat';
$templateData = [
    'title'  => $title,
    'user'   => session()->get('user') ?? ['name' => session()->get('name') ?? 'Administrator', 'role' => 'admin'],
    'active' => 'inventaris'
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

<?= view('admin/templates/header', $templateData) ?>
<?= view('admin/templates/sidebar', $templateData) ?>
<?= view('admin/templates/navbar', $templateData) ?>

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

    .pengadaan-card-modern {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        border-radius: 18px !important;
        box-shadow: 0 8px 25px rgba(30, 60, 114, 0.06) !important;
        transition: all 0.3s ease;
    }

    .stat-card-pengadaan {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.8);
        border-radius: 16px;
        box-shadow: 0 6px 20px rgba(30, 60, 114, 0.05);
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 5px 12px;
        border-radius: 30px;
        font-size: 0.76rem;
        font-weight: 600;
    }

    .table-scroll-wrapper {
        width: 100% !important;
        overflow-x: auto !important;
        border-radius: 12px;
        border: 1px solid #cbd5e1;
    }

    .table-scroll-wrapper table {
        min-width: 800px !important;
        width: 100% !important;
    }
</style>

<div class="container-fluid py-3 py-md-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-3 border border-light flex-wrap gap-3">
        <div class="d-flex align-items-center">
            <div class="bg-gradient-primary text-white rounded-3 p-2 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px; background: linear-gradient(135deg, #1e3c72, #2a5298);">
                <i class="fas fa-tools fs-5"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold text-dark fs-5">Laporan Kerusakan Alat & Perbaikan</h4>
                <small class="text-muted d-none d-sm-inline">Pantau kendala alat kantor, teknisi pengurus, lokasi perbaikan & status tindakan.</small>
            </div>
        </div>
        <div>
            <a href="<?= base_url('admin/inventaris/kerusakan/tambah') ?>" class="btn btn-primary rounded-pill shadow-sm d-inline-flex align-items-center fw-semibold">
                <i class="fas fa-plus me-1.5"></i> Laporkan Kerusakan Alat
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3" role="alert">
            <i class="fas fa-check-circle me-1.5"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-3" role="alert">
            <i class="fas fa-exclamation-circle me-1.5"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- KPI Ringkasan -->
    <div class="row g-2 g-md-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="card stat-card-pengadaan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 me-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-screwdriver-wrench text-primary fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5 text-dark"><?= number_format($totalLaporan) ?></div>
                        <div class="text-muted text-xs">Total Laporan Kerusakan</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card-pengadaan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 me-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-info-circle text-info fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5 text-info"><?= number_format($ringanCount) ?></div>
                        <div class="text-muted text-xs">Kerusakan Ringan</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card-pengadaan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 me-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-triangle-exclamation text-warning fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5 text-warning"><?= number_format($sedangCount) ?></div>
                        <div class="text-muted text-xs">Kerusakan Sedang</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card-pengadaan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-danger bg-opacity-10 me-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-circle-exclamation text-danger fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5 text-danger"><?= number_format($beratCount) ?></div>
                        <div class="text-muted text-xs">Kerusakan Berat</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table -->
    <div class="card pengadaan-card-modern p-3 p-md-4">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h5 class="fw-bold text-dark mb-0 fs-6">
                <i class="fas fa-list text-primary me-2"></i> Daftar Laporan Kerusakan & Perbaikan Alat
            </h5>
        </div>

        <div class="table-scroll-wrapper">
            <table class="table table-hover align-middle mb-0" id="tableKerusakan">
                <thead class="bg-light text-secondary">
                    <tr style="font-size: 0.78rem; text-transform: uppercase;">
                        <th class="ps-3 py-3" style="width: 40px;">No</th>
                        <th class="py-3">Kode & Nama Alat</th>
                        <th class="py-3">Lokasi Alat</th>
                        <th class="py-3">Pelapor</th>
                        <th class="py-3 text-center">Tingkat Kerusakan</th>
                        <th class="py-3 text-center">Status Perbaikan</th>
                        <th class="py-3 text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.85rem;">
                    <?php if (!empty($kerusakan)): ?>
                        <?php $no = 1; foreach ($kerusakan as $k): ?>
                            <tr>
                                <td class="ps-3 fw-bold text-secondary"><?= $no++ ?></td>
                                <td>
                                    <strong class="text-primary d-block"><?= esc($k['nama_alat']) ?></strong>
                                    <small class="text-muted d-block" style="font-size: 0.72rem;"><?= esc($k['kode_laporan'] ?: '-') ?></small>
                                </td>
                                <td>
                                    <span class="text-dark"><i class="fas fa-map-marker-alt text-danger me-1"></i><?= esc($k['lokasi_alat'] ?: '-') ?></span>
                                </td>
                                <td>
                                    <strong class="text-dark d-block"><?= esc($k['pelapor'] ?? 'Staf / Karyawan') ?></strong>
                                    <small class="text-muted d-block" style="font-size: 0.72rem;"><?= date('d M Y', strtotime($k['created_at'] ?? date('Y-m-d'))) ?></small>
                                </td>
                                <td class="text-center">
                                    <?php
                                        $tk = strtolower($k['tingkat_kerusakan'] ?? 'sedang');
                                        $badgeTk = 'bg-warning text-dark';
                                        if ($tk === 'berat') $badgeTk = 'bg-danger text-white';
                                        elseif ($tk === 'ringan') $badgeTk = 'bg-info text-white';
                                    ?>
                                    <span class="badge <?= $badgeTk ?> px-2.5 py-1 rounded-pill">
                                        <?= ucfirst(esc($k['tingkat_kerusakan'])) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php
                                        $st = strtolower($k['status_tindakan'] ?? 'dilaporkan');
                                        $badgeSt = 'bg-secondary text-white';
                                        if ($st === 'selesai' || $st === 'diperbaiki') $badgeSt = 'bg-success text-white';
                                        elseif ($st === 'dalam_perbaikan' || $st === 'proses') $badgeSt = 'bg-warning text-dark';
                                    ?>
                                    <span class="badge <?= $badgeSt ?> px-2.5 py-1 rounded-pill">
                                        <?= ucfirst(str_replace('_', ' ', esc($k['status_tindakan']))) ?>
                                    </span>
                                </td>
                                <td class="text-center pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('admin/inventaris/kerusakan/detail/' . $k['id']) ?>" class="btn btn-outline-primary rounded-start px-2" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('admin/inventaris/kerusakan/edit/' . $k['id']) ?>" class="btn btn-outline-secondary px-2" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?= base_url('admin/inventaris/kerusakan/delete/' . $k['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus laporan kerusakan ini?')">
                                            <button type="submit" class="btn btn-outline-danger rounded-end px-2" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof $.fn.DataTable !== 'undefined') {
            $('#tableKerusakan').DataTable({
                responsive: false,
                language: {
                    search: "Cari Laporan:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    emptyTable: "Belum ada laporan kerusakan alat.",
                    zeroRecords: "Tidak ada laporan kerusakan yang cocok.",
                    paginate: { first: "Awal", last: "Akhir", next: "→", previous: "←" }
                }
            });
        }
    });
</script>

<?= view('admin/templates/footer', $templateData) ?>
