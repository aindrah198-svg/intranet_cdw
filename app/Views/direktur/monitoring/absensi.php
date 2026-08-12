<?= $this->include('direktur/templates/header') ?>
<?= $this->include('direktur/templates/sidebar') ?>
<?= $this->include('direktur/templates/navbar') ?>

<?php
$absensiData = $absensiData ?? [];
$karyawanList = $karyawanList ?? [];

$startDate = (string) ($startDate ?? date('Y-m-01'));
$endDate = (string) ($endDate ?? date('Y-m-d'));
$statusFilter = $statusFilter ?? '';
$karyawanIdFilter = $karyawanIdFilter ?? '';
$searchQuery = $searchQuery ?? '';

// Query string helper
$queryString = http_build_query(array_filter([
    'start_date' => $startDate,
    'end_date'   => $endDate,
    'status'     => $statusFilter,
    'karyawan_id'=> $karyawanIdFilter,
    'search'     => $searchQuery
]));
?>

<style>
    /* Styling Premium Modern Material & Glassmorphism (Sama Persis Halaman Karyawan & Akun) */
    .employee-card-modern {
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
    
    .employee-card-modern:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 32px rgba(30, 60, 114, 0.12), 0 4px 10px rgba(0, 0, 0, 0.04) !important;
    }

    /* Avatar Soft Glowing Ring */
    .avatar-glow {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        box-shadow: 0 4px 14px rgba(30, 60, 114, 0.35);
        border: 2px solid rgba(255, 255, 255, 0.9);
        width: 48px;
        height: 48px;
        font-size: 1.2rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-weight: 700;
        flex-shrink: 0;
    }

    /* Status Pill Frosted Glass */
    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 5px 12px;
        border-radius: 30px;
        font-size: 0.76rem;
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    
    .status-pill-hadir {
        background: rgba(25, 135, 84, 0.12);
        color: #198754;
        border: 1px solid rgba(25, 135, 84, 0.25);
    }
    
    .status-pill-terlambat {
        background: rgba(255, 193, 7, 0.15);
        color: #b58100;
        border: 1px solid rgba(255, 193, 7, 0.3);
    }

    .status-pill-izin {
        background: rgba(13, 202, 240, 0.12);
        color: #0dcaf0;
        border: 1px solid rgba(13, 202, 240, 0.25);
    }

    .status-pill-sakit {
        background: rgba(111, 66, 193, 0.12);
        color: #6f42c1;
        border: 1px solid rgba(111, 66, 193, 0.25);
    }

    .status-pill-alpha {
        background: rgba(220, 53, 69, 0.12);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.25);
    }

    /* Bilah Data Horizontal (Data Bars) */
    .data-pill-bar {
        background: #f8fafc;
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 12px;
        padding: 10px 14px;
        transition: all 0.2s ease;
        height: 100%;
    }
    
    .data-pill-bar:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }

    .data-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 2px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .data-value {
        font-size: 0.92rem;
        font-weight: 600;
        color: #0f172a;
    }

    /* Modern Action Pills */
    .btn-action-pill {
        border-radius: 20px;
        padding: 7px 18px;
        font-size: 0.82rem;
        font-weight: 600;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid transparent;
        text-decoration: none;
        cursor: pointer;
    }

    .btn-action-view {
        background: rgba(13, 110, 253, 0.08);
        color: #0d6efd;
        border-color: rgba(13, 110, 253, 0.2);
    }

    .btn-action-view:hover {
        background: #0d6efd;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
    }

    .btn-action-edit {
        background: rgba(13, 202, 240, 0.1);
        color: #0891b2;
        border-color: rgba(13, 202, 240, 0.25);
    }

    .btn-action-edit:hover {
        background: #0891b2;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(8, 145, 178, 0.3);
    }

    .btn-action-delete {
        background: rgba(220, 53, 69, 0.08);
        color: #dc3545;
        border-color: rgba(220, 53, 69, 0.2);
    }

    .btn-action-delete:hover {
        background: #dc3545;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
    }
    
    .id-tag {
        background: #e2e8f0;
        color: #475569;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 6px;
        letter-spacing: 0.5px;
    }

    .form-control-custom, .form-select-custom {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 10px 14px;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .form-control-custom:focus, .form-select-custom:focus {
        border-color: #1e3c72;
        box-shadow: 0 0 0 3px rgba(30, 60, 114, 0.12);
    }

    /* Eye-Catching Responsive Pagination Styling */
    .pagination-modern .page-link {
        border: none !important;
        color: #475569;
        font-weight: 600;
        font-size: 0.88rem;
        min-width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 0 2px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        background: transparent;
        border-radius: 8px;
    }

    .pagination-modern .page-link:hover {
        background: rgba(30, 60, 114, 0.08);
        color: #1e3c72;
    }

    .pagination-modern .page-item.active .page-link {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(30, 60, 114, 0.35);
        font-weight: 700;
    }

    .active-filter-badge {
        font-size: 0.75rem;
    .active-filter-badge {
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 20px;
        background: rgba(30, 60, 114, 0.1);
        color: #1e3c72;
        font-weight: 600;
    }
</style>

<div class="container-fluid py-3 py-md-4">

    <!-- 1. Header Section Terpadu (Sama Persis Halaman Kelola Karyawan & Akun & Keluhan) -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-4 border border-light">
        <div class="d-flex align-items-center">
            <div class="bg-gradient-primary text-white rounded-3 p-2.5 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 46px; height: 46px;">
                <i class="fas fa-user-clock fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold text-dark">Monitoring Absensi</h4>
                <small class="text-muted d-none d-sm-inline">Pantau kehadiran, keterlambatan, dan jam kerja karyawan.</small>
            </div>
        </div>
        <button type="button" class="btn btn-primary rounded-pill px-3.5 py-2 shadow-sm d-inline-flex align-items-center text-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTambahAbsensi">
            <i class="fas fa-plus me-1.5"></i> <span class="d-none d-md-inline">Tambah Absensi</span><span class="d-inline d-md-none">Tambah</span>
        </button>
    </div>

    <!-- Alert Flash Data -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show text-white shadow-sm border-0 mb-4" role="alert" style="border-radius: 12px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show text-white shadow-sm border-0 mb-4" role="alert" style="border-radius: 12px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
            <i class="fas fa-exclamation-triangle me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- 2. Search & Filter Bar (Pencarian Instant Dengan Centered Modal Filter) -->
    <div class="input-group mb-3 shadow-sm rounded-pill overflow-hidden border border-light">
        <span class="input-group-text bg-white border-end-0 text-muted ps-3.5">
            <i class="fas fa-search"></i>
        </span>
        <input type="text" id="searchAbsensi" class="form-control border-start-0 border-end-0 ps-1 py-2.5" placeholder="Cari nama karyawan, NIK, divisi, atau status absensi...">
        <button class="btn btn-dark px-4 d-flex align-items-center gap-2 fw-semibold" type="button" data-bs-toggle="modal" data-bs-target="#filterModal">
            <i class="fas fa-sliders-h"></i> <span class="d-none d-sm-inline">Filter</span>
        </button>
    </div>

    <!-- Indicator Active Filter (Jika sedang memfilter) -->
    <div id="activeFilterTags" class="d-flex align-items-center gap-2 mb-3 d-none">
        <span class="text-xs text-muted fw-bold">Filter Aktif:</span>
        <div id="filterTagContainer" class="d-flex gap-1 flex-wrap"></div>
        <button type="button" class="btn btn-link btn-sm text-danger p-0 text-decoration-none ms-2" id="btnClearActiveFilter" style="font-size: 0.8rem;">
            <i class="fas fa-times-circle me-1"></i> Hapus Filter
        </button>
    </div>

    <!-- Modal Filter Lanjutan (Centered & Responsive) -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center" id="filterModalLabel">
                        <i class="fas fa-filter text-primary me-2"></i> Filter Monitoring Absensi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Filter Status Absensi -->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-sm">Status Absensi</label>
                            <select id="filterStatus" class="form-select rounded-3">
                                <option value="">Semua Status</option>
                                <option value="Hadir">Hadir</option>
                                <option value="Terlambat">Terlambat</option>
                                <option value="Izin">Izin</option>
                                <option value="Sakit">Sakit</option>
                                <option value="Alpha">Alpha</option>
                            </select>
                        </div>
                        <!-- Filter Shift -->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-sm">Shift</label>
                            <select id="filterShift" class="form-select rounded-3">
                                <option value="">Semua Shift</option>
                                <option value="pagi">Pagi</option>
                                <option value="siang">Siang</option>
                                <option value="sore">Sore</option>
                                <option value="malam">Malam</option>
                            </select>
                        </div>
                        <!-- Filter Tanggal Mulai -->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-sm">Tanggal Mulai</label>
                            <input type="date" id="filterStartDate" class="form-control rounded-3" value="<?= esc($startDate) ?>">
                        </div>
                        <!-- Filter Tanggal Selesai -->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-sm">Tanggal Selesai</label>
                            <input type="date" id="filterEndDate" class="form-control rounded-3" value="<?= esc($endDate) ?>">
                        </div>
                        <!-- Opsi Export -->
                        <div class="col-12 pt-2 border-top">
                            <label class="form-label fw-semibold text-sm d-block">Opsi Export Data</label>
                            <div class="d-flex gap-2">
                                <a href="<?= base_url('direktur/monitoring/absensi/exportPdf?' . $queryString) ?>" class="btn btn-outline-danger btn-sm rounded-pill w-100 fw-semibold">
                                    <i class="fas fa-file-pdf me-1"></i> Export PDF
                                </a>
                                <a href="<?= base_url('direktur/monitoring/absensi/exportExcel?' . $queryString) ?>" class="btn btn-outline-success btn-sm rounded-pill w-100 fw-semibold">
                                    <i class="fas fa-file-excel me-1"></i> Export Excel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-3 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold border" id="btnResetFilter">
                        <i class="fas fa-undo me-1"></i> Reset
                    </button>
                    <button type="button" class="btn btn-primary rounded-pill px-4 fw-semibold" data-bs-dismiss="modal" id="btnApplyFilter">
                        <i class="fas fa-check me-1"></i> Terapkan Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Daftar Kartu Absensi Modern (Card List Grid persis dengan Halaman Karyawan & Keluhan) -->
    <div class="row g-3" id="absensiCardContainer">
        <?php if (empty($absensiData)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center rounded-4 shadow-sm p-4 my-2">
                    <i class="fas fa-folder-open fa-2x mb-2 text-primary"></i>
                    <h5 class="fw-bold mb-1">Data Absensi Tidak Ditemukan</h5>
                    <p class="mb-0 text-muted small">Belum ada record absensi karyawan tercatat untuk filter ini.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($absensiData as $item): ?>
                <?php
                    $st = $item['status'] ?? 'Hadir';
                    $statusPillClass = match($st) {
                        'Hadir'     => 'status-pill-hadir',
                        'Terlambat' => 'status-pill-terlambat',
                        'Izin'      => 'status-pill-izin',
                        'Sakit'     => 'status-pill-sakit',
                        'Alpha'     => 'status-pill-alpha',
                        default     => 'status-pill-hadir'
                    };
                    $statusIcon = match($st) {
                        'Hadir'     => 'fas fa-check-circle',
                        'Terlambat' => 'fas fa-clock',
                        'Izin'      => 'fas fa-info-circle',
                        'Sakit'     => 'fas fa-user-md',
                        'Alpha'     => 'fas fa-times-circle',
                        default     => 'fas fa-check-circle'
                    };
                    $nama = $item['nama_lengkap'] ?? $item['nama_panggilan'] ?? 'Karyawan';
                    $initial = strtoupper(substr($nama, 0, 1));
                    $wMasuk  = !empty($item['waktu_masuk']) ? date('H:i', strtotime($item['waktu_masuk'])) : '-';
                    $wPulang = !empty($item['waktu_pulang']) ? date('H:i', strtotime($item['waktu_pulang'])) : '-';
                    $shiftName = ucfirst($item['shift'] ?? 'siang');
                ?>
                <div class="col-12 absensi-card-wrapper" data-status="<?= esc($st) ?>" data-shift="<?= esc($item['shift'] ?? 'siang') ?>" data-search="<?= esc(strtolower($nama.' '.$item['nik'].' '.$item['jabatan'].' '.$item['departemen'].' '.$st)) ?>">
                    <div class="card employee-card-modern absensi-card p-3 p-sm-4">
                        
                        <!-- Visual Header Kartu -->
                        <div class="d-flex align-items-center justify-content-between pb-3 border-bottom border-light flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-3">
                                <!-- Avatar Lingkaran Inisial -->
                                <div class="avatar-glow text-white rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0">
                                    <?= $initial ?>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <!-- Nama Karyawan -->
                                        <h3 class="mb-0 fw-bold text-dark" style="font-size: 1.18rem; letter-spacing: -0.2px;">
                                            <?= esc($nama) ?>
                                        </h3>
                                        <!-- Tag NIK -->
                                        <span class="id-tag">NIK: <?= esc($item['nik'] ?: '-') ?></span>
                                    </div>
                                    <!-- Lencana Status Chip Frosted Glass -->
                                    <div class="mt-1.5 d-flex align-items-center gap-1.5 flex-wrap">
                                        <span class="status-pill <?= $statusPillClass ?>">
                                            <i class="<?= $statusIcon ?> me-1"></i> <?= esc($st) ?>
                                        </span>
                                        <span class="badge bg-light text-dark border font-weight-normal" style="border-radius: 6px; font-size: 0.72rem;">
                                            Shift <?= $shiftName ?>
                                        </span>
                                        <span class="text-xs text-muted ms-1">
                                            <i class="far fa-calendar-alt me-1"></i> <?= date('d M Y', strtotime($item['tanggal'])) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Penataan Bidang Data (Bilah Data Horizontal Grid) -->
                        <div class="py-3">
                            <div class="row g-2.5">
                                <!-- Jabatan & Departemen -->
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="data-pill-bar">
                                        <div class="data-label">
                                            <i class="fas fa-user-tag text-primary"></i> Jabatan / Departemen
                                        </div>
                                        <div class="data-value text-break">
                                            <?= esc($item['jabatan'] ?: '-') ?> <span class="text-muted">|</span> <?= esc($item['departemen'] ?: 'Staf') ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Waktu Masuk & Pulang -->
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="data-pill-bar">
                                        <div class="data-label">
                                            <i class="far fa-clock text-primary"></i> Masuk & Pulang
                                        </div>
                                        <div class="data-value">
                                            <span class="text-success fw-bold"><?= $wMasuk ?></span> - <span class="text-danger fw-bold"><?= $wPulang ?></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Jam Kerja & Terlambat -->
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="data-pill-bar">
                                        <div class="data-label">
                                            <i class="fas fa-business-time text-primary"></i> Jam Kerja & Terlambat
                                        </div>
                                        <div class="data-value">
                                            <?= !empty($item['jam_kerja']) ? number_format($item['jam_kerja'], 1).' jam' : '-' ?>
                                            <?php if(!empty($item['terlambat']) && $item['terlambat'] > 0): ?>
                                                <span class="badge bg-warning text-dark ms-1" style="font-size: 0.7rem;">+<?= esc($item['terlambat']) ?> mnt</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Keterangan -->
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="data-pill-bar">
                                        <div class="data-label">
                                            <i class="fas fa-comment-dots text-primary"></i> Keterangan
                                        </div>
                                        <div class="data-value text-truncate">
                                            <?= esc($item['keterangan'] ?: '-') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Aksi Terintegrasi (Modern Action Pills) -->
                        <div class="pt-2 border-top border-light d-flex justify-content-end align-items-center gap-2 flex-wrap">
                            <a href="<?= base_url('direktur/monitoring/absensi/detail/' . $item['id']) ?>" class="btn-action-pill btn-action-view" title="Lihat Detail Absensi">
                                <i class="far fa-eye"></i> Detail
                            </a>
                            <button type="button" class="btn-action-pill btn-action-edit" onclick="openEditModal(<?= htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8') ?>)" title="Edit Absensi">
                                <i class="far fa-edit"></i> Edit
                            </button>
                            <form action="<?= base_url('direktur/monitoring/absensi/delete/' . $item['id']) ?>" method="post" class="d-inline form-delete-absensi">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn-action-pill btn-action-delete btn-delete-absensi" data-nama="<?= esc($nama) ?>" title="Hapus Data Absensi">
                                    <i class="far fa-trash-alt"></i> Hapus
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Pesan Jika Data Tidak Ditemukan -->
    <div id="noResultsMessage" class="alert alert-info text-center rounded-4 shadow-sm p-4 d-none my-3">
        <i class="fas fa-search fa-2x mb-2 text-primary"></i>
        <h5 class="fw-bold mb-1">Data Absensi Tidak Ditemukan</h5>
        <p class="mb-0 text-muted small">Coba ubah kata kunci pencarian atau reset filter Anda.</p>
    </div>

    <!-- 4. Control Bar Bawah: Pengaturan Tampilkan Per Halaman + Info + Eye-Catching Responsive Pagination -->
    <div class="card shadow-sm rounded-4 border-0 p-3 mt-4 mb-5 bg-white">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            
            <!-- Pengaturan Jumlah Tampilan Data -->
            <div class="d-flex align-items-center gap-2">
                <span class="text-xs text-muted fw-bold text-uppercase">Tampilkan:</span>
                <select id="perPageSelect" class="form-select form-select-sm rounded-pill shadow-xs fw-semibold text-dark border-light px-3 py-1.5" style="width: auto; cursor: pointer;">
                    <option value="5" selected>5 Record / hal</option>
                    <option value="10">10 Record / hal</option>
                    <option value="25">25 Record / hal</option>
                    <option value="50">50 Record / hal</option>
                    <option value="all">Tampilkan Semua</option>
                </select>
            </div>

            <!-- Teks Statistik Info Data -->
            <div class="text-muted text-sm fw-semibold text-center" id="paginationInfo">
                Menampilkan 1 - 5 dari <?= count($absensiData) ?> absensi
            </div>

            <!-- Eye-Catching Pagination Component -->
            <div id="paginationContainer" class="d-flex justify-content-center">
                <!-- Diisi otomatis secara responsif oleh JavaScript -->
            </div>

        </div>
    </div>

</div>

<!-- Modal Tambah Absensi Manual -->
<div class="modal fade" id="modalTambahAbsensi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <form action="<?= base_url('direktur/monitoring/absensi/simpan') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                        <i class="fas fa-user-plus text-primary me-2"></i> Tambah Record Absensi Manual
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-sm">Pilih Karyawan <span class="text-danger">*</span></label>
                            <select name="karyawan_id" class="form-select form-select-custom" required>
                                <option value="">-- Pilih Karyawan --</option>
                                <?php foreach ($karyawanList as $k): ?>
                                    <option value="<?= $k['id'] ?>"><?= esc($k['nama_lengkap']) ?> (NIK: <?= esc($k['nik'] ?: '-') ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label fw-semibold text-sm">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control form-control-custom" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label fw-semibold text-sm">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select form-select-custom" required>
                                <option value="Hadir">🟢 Hadir</option>
                                <option value="Terlambat">🟡 Terlambat</option>
                                <option value="Izin">🔵 Izin</option>
                                <option value="Sakit">🟣 Sakit</option>
                                <option value="Alpha">🔴 Alpha</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label fw-semibold text-sm">Shift</label>
                            <select name="shift" class="form-select form-select-custom">
                                <option value="pagi">Pagi</option>
                                <option value="siang" selected>Siang</option>
                                <option value="sore">Sore</option>
                                <option value="malam">Malam</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label fw-semibold text-sm">Jam Masuk</label>
                            <input type="time" name="waktu_masuk" class="form-control form-control-custom" value="08:00">
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label fw-semibold text-sm">Jam Pulang</label>
                            <input type="time" name="waktu_pulang" class="form-control form-control-custom" value="17:00">
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label fw-semibold text-sm">Terlambat (menit)</label>
                            <input type="number" name="terlambat" class="form-control form-control-custom" value="0" min="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-sm">Keterangan / Catatan</label>
                            <textarea name="keterangan" class="form-control form-control-custom" rows="2" placeholder="Catatan opsional..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-3 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
                        <i class="fas fa-save me-1.5"></i> Simpan Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Absensi -->
<div class="modal fade" id="modalEditAbsensi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <form id="formEditAbsensi" action="" method="post">
                <?= csrf_field() ?>
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                        <i class="fas fa-edit text-warning me-2"></i> Edit Record Absensi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-sm">Karyawan</label>
                            <input type="text" id="editNamaKaryawan" class="form-control form-control-custom bg-light" readonly>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label fw-semibold text-sm">Tanggal</label>
                            <input type="text" id="editTanggal" class="form-control form-control-custom bg-light" readonly>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label fw-semibold text-sm">Status <span class="text-danger">*</span></label>
                            <select name="status" id="editStatus" class="form-select form-select-custom" required>
                                <option value="Hadir">🟢 Hadir</option>
                                <option value="Terlambat">🟡 Terlambat</option>
                                <option value="Izin">🔵 Izin</option>
                                <option value="Sakit">🟣 Sakit</option>
                                <option value="Alpha">🔴 Alpha</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label fw-semibold text-sm">Shift</label>
                            <select name="shift" id="editShift" class="form-select form-select-custom">
                                <option value="pagi">Pagi</option>
                                <option value="siang">Siang</option>
                                <option value="sore">Sore</option>
                                <option value="malam">Malam</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label fw-semibold text-sm">Jam Masuk</label>
                            <input type="time" name="waktu_masuk" id="editWaktuMasuk" class="form-control form-control-custom">
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label fw-semibold text-sm">Jam Pulang</label>
                            <input type="time" name="waktu_pulang" id="editWaktuPulang" class="form-control form-control-custom">
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label fw-semibold text-sm">Terlambat (menit)</label>
                            <input type="number" name="terlambat" id="editTerlambat" class="form-control form-control-custom" min="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-sm">Keterangan / Catatan</label>
                            <textarea name="keterangan" id="editKeterangan" class="form-control form-control-custom" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-3 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-white rounded-pill px-4 fw-semibold shadow-sm" style="background: #b58100; border-color: #b58100;">
                        <i class="fas fa-save me-1.5"></i> Perbarui Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput   = document.getElementById('searchAbsensi');
    const filterStatus  = document.getElementById('filterStatus');
    const filterShift   = document.getElementById('filterShift');
    const perPageSelect = document.getElementById('perPageSelect');
    const btnReset      = document.getElementById('btnResetFilter');
    const btnApply      = document.getElementById('btnApplyFilter');
    const btnClearActive = document.getElementById('btnClearActiveFilter');
    const cards         = Array.from(document.querySelectorAll('.absensi-card-wrapper'));
    const paginationEl  = document.getElementById('paginationContainer');
    const infoEl        = document.getElementById('paginationInfo');
    const noResultsEl   = document.getElementById('noResultsMessage');
    const activeTagsBox = document.getElementById('activeFilterTags');
    const tagContainer  = document.getElementById('filterTagContainer');

    let currentPage = 1;
    let itemsPerPage = 5;

    function filterAndPaginate() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const statusVal  = filterStatus ? filterStatus.value.toLowerCase().trim() : '';
        const shiftVal   = filterShift  ? filterShift.value.toLowerCase().trim() : '';

        const perPageVal = perPageSelect ? perPageSelect.value : '5';
        itemsPerPage = perPageVal === 'all' ? 999999 : parseInt(perPageVal) || 5;

        // Update indikator filter aktif
        let tagsHtml = '';
        if (statusVal) {
            tagsHtml += `<span class="active-filter-badge">Status: ${statusVal}</span>`;
        }
        if (shiftVal) {
            tagsHtml += `<span class="active-filter-badge">Shift: ${shiftVal}</span>`;
        }

        if (tagsHtml && activeTagsBox && tagContainer) {
            tagContainer.innerHTML = tagsHtml;
            activeTagsBox.classList.remove('d-none');
        } else if (activeTagsBox) {
            activeTagsBox.classList.add('d-none');
        }

        // Filter Kartu
        let visibleCards = cards.filter(card => {
            const cardSearch = (card.getAttribute('data-search') || '').toLowerCase();
            const cardStatus = (card.getAttribute('data-status') || '').toLowerCase();
            const cardShift  = (card.getAttribute('data-shift') || '').toLowerCase();

            const matchSearch = !searchTerm || cardSearch.includes(searchTerm);
            const matchStatus = !statusVal  || cardStatus.includes(statusVal);
            const matchShift  = !shiftVal   || cardShift.includes(shiftVal);

            return matchSearch && matchStatus && matchShift;
        });

        const totalVisible = visibleCards.length;
        const totalPages   = Math.ceil(totalVisible / itemsPerPage) || 1;

        if (currentPage > totalPages) {
            currentPage = totalPages;
        }

        // Sembunyikan semua kartu
        cards.forEach(card => card.style.display = 'none');

        // Tampilkan pesan jika kosong
        if (totalVisible === 0) {
            if (noResultsEl) noResultsEl.classList.remove('d-none');
            if (infoEl) infoEl.textContent = 'Tidak ada data absensi yang cocok.';
            if (paginationEl) paginationEl.innerHTML = '';
            return;
        } else {
            if (noResultsEl) noResultsEl.classList.add('d-none');
        }

        // Paginate Kartu
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex   = Math.min(startIndex + itemsPerPage, totalVisible);

        for (let i = startIndex; i < endIndex; i++) {
            visibleCards[i].style.display = 'block';
        }

        if (infoEl) {
            if (itemsPerPage >= 999999) {
                infoEl.textContent = `Menampilkan seluruh ${totalVisible} record absensi`;
            } else {
                infoEl.textContent = `Menampilkan ${startIndex + 1} - ${endIndex} dari ${totalVisible} absensi`;
            }
        }

        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        if (!paginationEl) return;
        if (totalPages <= 1) {
            paginationEl.innerHTML = '';
            return;
        }

        let html = '<ul class="pagination pagination-modern mb-0 shadow-sm rounded-pill overflow-hidden bg-white p-1 border border-light">';

        // Tombol Sebelumnya
        html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <button class="page-link rounded-pill" data-page="${currentPage - 1}">
                        <i class="fas fa-chevron-left me-1"></i> <span class="d-none d-sm-inline">Sebelumnya</span>
                    </button>
                 </li>`;

        // Algoritma Smart Page Windowing untuk Tampilan Responsif (Mobile & Desktop)
        const maxVisibleButtons = window.innerWidth < 576 ? 3 : 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxVisibleButtons / 2));
        let endPage = Math.min(totalPages, startPage + maxVisibleButtons - 1);

        if (endPage - startPage + 1 < maxVisibleButtons) {
            startPage = Math.max(1, endPage - maxVisibleButtons + 1);
        }

        if (startPage > 1) {
            html += `<li class="page-item"><button class="page-link rounded-circle" data-page="1">1</button></li>`;
            if (startPage > 2) {
                html += `<li class="page-item disabled"><span class="page-link border-0">...</span></li>`;
            }
        }

        for (let p = startPage; p <= endPage; p++) {
            html += `<li class="page-item ${p === currentPage ? 'active' : ''}">
                        <button class="page-link rounded-circle" data-page="${p}">${p}</button>
                     </li>`;
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                html += `<li class="page-item disabled"><span class="page-link border-0">...</span></li>`;
            }
            html += `<li class="page-item"><button class="page-link rounded-circle" data-page="${totalPages}">${totalPages}</button></li>`;
        }

        // Tombol Selanjutnya
        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <button class="page-link rounded-pill" data-page="${currentPage + 1}">
                        <span class="d-none d-sm-inline">Selanjutnya</span> <i class="fas fa-chevron-right ms-1"></i>
                    </button>
                 </li>`;

        html += '</ul>';
        paginationEl.innerHTML = html;

        // Bind Event Klik Pagination
        paginationEl.querySelectorAll('.page-link').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const targetPage = parseInt(this.getAttribute('data-page'));
                if (targetPage && targetPage >= 1 && targetPage <= totalPages && targetPage !== currentPage) {
                    currentPage = targetPage;
                    filterAndPaginate();
                    window.scrollTo({ top: 120, behavior: 'smooth' });
                }
            });
        });
    }

    // Event listeners
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            currentPage = 1;
            filterAndPaginate();
        });
    }

    if (perPageSelect) {
        perPageSelect.addEventListener('change', function () {
            currentPage = 1;
            filterAndPaginate();
        });
    }

    if (btnApply) {
        btnApply.addEventListener('click', function () {
            currentPage = 1;
            filterAndPaginate();
        });
    }

    if (btnReset) {
        btnReset.addEventListener('click', function () {
            if (filterStatus) filterStatus.value = '';
            if (filterShift) filterShift.value = '';
            currentPage = 1;
            filterAndPaginate();
        });
    }

    if (btnClearActive) {
        btnClearActive.addEventListener('click', function () {
            if (filterStatus) filterStatus.value = '';
            if (filterShift) filterShift.value = '';
            currentPage = 1;
            filterAndPaginate();
        });
    }

    // Initial Filter & Paginate
    filterAndPaginate();
});

// Real-time Notifikasi & SweetAlert Delete Handler
// Gunakan window.addEventListener('load') agar jQuery sudah dimuat oleh footer
window.addEventListener('load', function () {
    if (typeof $ === 'undefined' || typeof Swal === 'undefined') return;
    function updateNotificationBadge() {
        const notifUrl = '<?= base_url("direktur/dashboard/get-notifications") ?>';
        $.ajax({
            url: notifUrl,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response && response.status === 'success') {
                    const count = parseInt(response.count) || 0;
                    const $badge = $('.navbar-badge');
                    if ($badge.length) {
                        $badge.text(count);
                        if (count > 0) {
                            $badge.removeClass('d-none').show();
                        } else {
                            $badge.addClass('d-none').hide();
                        }
                    }
                    const $actionBadge = $('.navbar-notif-action-badge');
                    if ($actionBadge.length) {
                        $actionBadge.text(count + ' Perlu Action');
                    }
                }
            }
        });
    }

    const flashSuccess = '<?= session()->getFlashdata('success') ? esc(session()->getFlashdata('success'), 'js') : '' ?>';
    const flashError   = '<?= session()->getFlashdata('error') ? esc(session()->getFlashdata('error'), 'js') : '' ?>';

    if (flashSuccess) {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            html: flashSuccess,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true
        });
        updateNotificationBadge();
    }

    if (flashError) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            html: flashError,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true
        });
    }

    $(document).on('click', '.btn-delete-absensi', function (e) {
        e.preventDefault();
        const form = $(this).closest('form');
        const nama = $(this).data('nama') || 'record absensi ini';

        Swal.fire({
            title: 'Konfirmasi Hapus Data',
            text: `Apakah Anda yakin ingin menghapus data absensi "${nama}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="far fa-trash-alt me-1"></i> Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});

function openEditModal(data) {
    document.getElementById('formEditAbsensi').action = '<?= base_url('direktur/monitoring/absensi/update/') ?>' + data.id;
    document.getElementById('editNamaKaryawan').value = (data.nama_lengkap || data.nama_panggilan || '-') + ' (' + (data.nik || '-') + ')';
    document.getElementById('editTanggal').value = data.tanggal || '';
    document.getElementById('editStatus').value = data.status || 'Hadir';
    document.getElementById('editShift').value = data.shift || 'siang';
    
    if (data.waktu_masuk) {
        document.getElementById('editWaktuMasuk').value = data.waktu_masuk.substring(0, 5);
    } else {
        document.getElementById('editWaktuMasuk').value = '';
    }
    
    if (data.waktu_pulang) {
        document.getElementById('editWaktuPulang').value = data.waktu_pulang.substring(0, 5);
    } else {
        document.getElementById('editWaktuPulang').value = '';
    }
    
    document.getElementById('editTerlambat').value = data.terlambat || 0;
    document.getElementById('editKeterangan').value = data.keterangan || '';
    
    const editModal = new bootstrap.Modal(document.getElementById('modalEditAbsensi'));
    editModal.show();
}
</script>

<?= $this->include('direktur/templates/footer') ?>