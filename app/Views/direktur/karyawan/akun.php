<?= $this->include('direktur/templates/header') ?>
<?= $this->include('direktur/templates/sidebar') ?>
<?= $this->include('direktur/templates/navbar') ?>

<?php
// Ekstrak Role secara unik dari data akun aktif
$rolesInDb = array_values(array_unique(array_filter(array_column($akun_aktif, 'role'))));
sort($rolesInDb);

$countAkunAktif = count($akun_aktif);
$countBelumAkun = count($karyawan_belum_akun);
?>

<style>
    /* Styling Premium Modern Material & Glassmorphism */
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
        width: 50px;
        height: 50px;
        font-size: 1.3rem;
    }

    .avatar-glow-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        box-shadow: 0 4px 14px rgba(245, 158, 11, 0.35);
        border: 2px solid rgba(255, 255, 255, 0.9);
        width: 50px;
        height: 50px;
        font-size: 1.3rem;
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
    
    .status-pill-active {
        background: rgba(25, 135, 84, 0.12);
        color: #198754;
        border: 1px solid rgba(25, 135, 84, 0.25);
    }
    
    .status-pill-inactive {
        background: rgba(220, 53, 69, 0.12);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.25);
    }

    .status-pill-pending {
        background: rgba(255, 193, 7, 0.15);
        color: #b58100;
        border: 1px solid rgba(255, 193, 7, 0.3);
    }

    /* Role Pills */
    .role-pill {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        letter-spacing: 0.4px;
        text-transform: uppercase;
    }
    .role-direktur { background: rgba(112, 51, 255, 0.12); color: #6f42c1; border: 1px solid rgba(112, 51, 255, 0.25); }
    .role-admin { background: rgba(13, 110, 253, 0.12); color: #0d6efd; border: 1px solid rgba(13, 110, 253, 0.25); }
    .role-hrd { background: rgba(13, 202, 240, 0.12); color: #0891b2; border: 1px solid rgba(13, 202, 240, 0.25); }
    .role-accounting { background: rgba(25, 135, 84, 0.12); color: #198754; border: 1px solid rgba(25, 135, 84, 0.25); }
    .role-teknisi { background: rgba(245, 158, 11, 0.15); color: #d97706; border: 1px solid rgba(245, 158, 11, 0.3); }
    .role-sales { background: rgba(236, 72, 153, 0.12); color: #db2777; border: 1px solid rgba(236, 72, 153, 0.25); }
    .role-software_engineer { background: rgba(99, 102, 241, 0.12); color: #4f46e5; border: 1px solid rgba(99, 102, 241, 0.25); }
    .role-default { background: rgba(108, 117, 125, 0.12); color: #495057; border: 1px solid rgba(108, 117, 125, 0.25); }

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
    }

    .btn-action-generate {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
    }

    .btn-action-generate:hover {
        background: linear-gradient(135deg, #0a58ca 0%, #084298 100%);
        color: #ffffff;
        box-shadow: 0 6px 16px rgba(13, 110, 253, 0.35);
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

    /* Navigation Tabs Modern Pill Styling */
    .nav-pills-modern {
        background: rgba(241, 245, 249, 0.8);
        padding: 5px;
        border-radius: 30px;
        border: 1px solid #e2e8f0;
    }

    .nav-pills-modern .nav-link {
        border-radius: 25px;
        color: #64748b;
        font-weight: 600;
        font-size: 0.88rem;
        padding: 8px 20px;
        transition: all 0.25s ease;
        border: none;
    }

    .nav-pills-modern .nav-link:hover {
        color: #1e3c72;
    }

    .nav-pills-modern .nav-link.active {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(30, 60, 114, 0.3);
    }

    /* Responsive Pagination Styling */
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

    .pagination-modern .page-item.disabled .page-link {
        color: #cbd5e1;
        background: transparent;
    }

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

    <!-- 1. Header Section (Terpadu & Estetik) -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-4 border border-light flex-wrap gap-3">
        <div class="d-flex align-items-center">
            <div class="bg-gradient-primary text-white rounded-3 p-2.5 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 46px; height: 46px;">
                <i class="fas fa-user-shield fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold text-dark">Kelola Akun Karyawan</h4>
                <small class="text-muted d-none d-sm-inline">Kelola akun pengguna, hak akses role, serta pembuatan akun login karyawan CDW Engineering.</small>
            </div>
        </div>

        <!-- Quick Stats Pill Badges -->
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill font-weight-bold" style="font-size: 0.82rem;">
                <i class="fas fa-user-check text-success me-1.5"></i> Akun Aktif: <strong><?= $countAkunAktif ?></strong>
            </span>
            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill font-weight-bold" style="font-size: 0.82rem;">
                <i class="fas fa-user-clock text-warning me-1.5"></i> Belum Punya Akun: <strong><?= $countBelumAkun ?></strong>
            </span>
        </div>
    </div>

    <!-- 2. Navigasi Tab (Akun Aktif VS Belum Memiliki Akun) -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <ul class="nav nav-pills nav-pills-modern" id="accountTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-aktif-tab" data-bs-toggle="pill" data-bs-target="#tab-aktif" type="button" role="tab" aria-controls="tab-aktif" aria-selected="true">
                    <i class="fas fa-users-cog me-1.5"></i> Akun Terdaftar <span class="badge bg-white text-dark ms-1 rounded-pill"><?= $countAkunAktif ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-belum-tab" data-bs-toggle="pill" data-bs-target="#tab-belum" type="button" role="tab" aria-controls="tab-belum" aria-selected="false">
                    <i class="fas fa-user-plus me-1.5"></i> Belum Memiliki Akun <span class="badge bg-warning text-dark ms-1 rounded-pill"><?= $countBelumAkun ?></span>
                </button>
            </li>
        </ul>
    </div>

    <!-- 3. Search & Filter Bar (Input Group Dengan Centered Modal Filter) -->
    <div class="input-group mb-3 shadow-sm rounded-pill overflow-hidden border border-light">
        <span class="input-group-text bg-white border-end-0 text-muted ps-3.5">
            <i class="fas fa-search"></i>
        </span>
        <input type="text" id="searchAkun" class="form-control border-start-0 border-end-0 ps-1 py-2.5" placeholder="Cari nama karyawan, username, NIK, email, role, atau divisi...">
        <button class="btn btn-dark px-4 d-flex align-items-center gap-2 fw-semibold" type="button" data-bs-toggle="modal" data-bs-target="#filterModal">
            <i class="fas fa-sliders-h"></i> <span class="d-none d-sm-inline">Filter</span>
        </button>
    </div>

    <!-- Indicator Active Filter -->
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
                        <i class="fas fa-filter text-primary me-2"></i> Filter Data Akun
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Filter Role Akses -->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-sm">Role Hak Akses</label>
                            <select id="filterRole" class="form-select rounded-3">
                                <option value="">Semua Role</option>
                                <option value="direktur">Direktur</option>
                                <option value="admin">Admin</option>
                                <option value="hrd">HRD</option>
                                <option value="accounting">Accounting</option>
                                <option value="teknisi">Teknisi</option>
                                <option value="sales">Sales</option>
                                <option value="software_engineer">Software Engineer</option>
                                <option value="employee">Employee / Staff</option>
                            </select>
                        </div>
                        <!-- Filter Status Akun -->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-sm">Status Akun</label>
                            <select id="filterStatus" class="form-select rounded-3">
                                <option value="">Semua Status</option>
                                <option value="active">Active (Aktif)</option>
                                <option value="inactive">Inactive (Suspend)</option>
                            </select>
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

    <!-- 4. Tab Content (Daftar Kartu Akun Aktif & Belum Punya Akun) -->
    <div class="tab-content" id="accountTabsContent">

        <!-- TAB 1: DAFTAR AKUN AKTIF -->
        <div class="tab-pane fade show active" id="tab-aktif" role="tabpanel" aria-labelledby="tab-aktif-tab">
            <div class="row g-3" id="akunAktifContainer">
                <?php foreach ($akun_aktif as $akun): ?>
                    <?php
                        $namaUser = $akun['nama_lengkap'] ?? $akun['name'];
                        $initial  = !empty($namaUser) ? strtoupper(substr($namaUser, 0, 1)) : 'U';
                        $roleStr  = strtolower($akun['role'] ?? 'employee');
                        $roleClass = 'role-' . $roleStr;
                        if (!in_array($roleClass, ['role-direktur','role-admin','role-hrd','role-accounting','role-teknisi','role-sales','role-software_engineer'])) {
                            $roleClass = 'role-default';
                        }
                        
                        $statusStr = strtolower($akun['status'] ?? 'active');
                        $statusPillClass = ($statusStr === 'active' || $statusStr === 'aktif') ? 'status-pill-active' : 'status-pill-inactive';
                        $statusText      = ($statusStr === 'active' || $statusStr === 'aktif') ? 'Active' : 'Suspend';
                        $statusIcon      = ($statusStr === 'active' || $statusStr === 'aktif') ? 'fas fa-check-circle' : 'fas fa-ban';

                        $isSelf = (session('id') == $akun['id']);
                    ?>
                    <div class="col-12 akun-card-wrapper" data-role="<?= esc($roleStr) ?>" data-status="<?= esc($statusStr) ?>" data-type="aktif">
                        <div class="card employee-card-modern p-3 p-sm-4">
                            
                            <!-- Header Kartu -->
                            <div class="d-flex align-items-center justify-content-between pb-3 border-bottom border-light flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-3">
                                    <!-- Avatar Lingkaran Inisial -->
                                    <div class="avatar-glow text-white rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0">
                                        <?= $initial ?>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <!-- Nama Pengguna -->
                                            <h3 class="mb-0 fw-bold text-dark" style="font-size: 1.18rem; letter-spacing: -0.2px;">
                                                <?= esc($namaUser) ?>
                                            </h3>
                                            <!-- Tag Username -->
                                            <span class="id-tag"><i class="fas fa-at text-primary me-1"></i><?= esc($akun['username']) ?></span>
                                        </div>
                                        <!-- Lencana Role & Status Chip -->
                                        <div class="mt-1.5 d-flex align-items-center gap-2 flex-wrap">
                                            <span class="role-pill <?= $roleClass ?>">
                                                <?= esc(str_replace('_', ' ', strtoupper($akun['role']))) ?>
                                            </span>
                                            <span class="status-pill <?= $statusPillClass ?>">
                                                <i class="<?= $statusIcon ?> me-1"></i> <?= $statusText ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bilah Data Horizontal -->
                            <div class="py-3">
                                <div class="row g-2.5">
                                    <!-- NIK -->
                                    <div class="col-12 col-sm-6 col-md-3">
                                        <div class="data-pill-bar">
                                            <div class="data-label">
                                                <i class="far fa-id-card text-primary"></i> NIK Karyawan
                                            </div>
                                            <div class="data-value text-break">
                                                <?= !empty($akun['nik']) ? esc($akun['nik']) : '-' ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div class="col-12 col-sm-6 col-md-3">
                                        <div class="data-pill-bar">
                                            <div class="data-label">
                                                <i class="far fa-envelope text-primary"></i> Email Akun
                                            </div>
                                            <div class="data-value text-break">
                                                <?= !empty($akun['email']) ? esc($akun['email']) : '-' ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Divisi -->
                                    <div class="col-12 col-sm-6 col-md-3">
                                        <div class="data-pill-bar">
                                            <div class="data-label">
                                                <i class="fas fa-sitemap text-primary"></i> Divisi
                                            </div>
                                            <div class="data-value">
                                                <?= !empty($akun['divisi']) ? esc($akun['divisi']) : '-' ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ID User -->
                                    <div class="col-12 col-sm-6 col-md-3">
                                        <div class="data-pill-bar">
                                            <div class="data-label">
                                                <i class="fas fa-fingerprint text-primary"></i> User ID System
                                            </div>
                                            <div class="data-value">
                                                #<?= esc($akun['id']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tombol Aksi -->
                            <div class="pt-2 border-top border-light d-flex justify-content-end align-items-center gap-2 flex-wrap">
                                <?php if (!$isSelf): ?>
                                    <a href="<?= base_url('direktur/karyawan/edit-akun/' . $akun['id']) ?>" class="btn-action-pill btn-action-edit" title="Edit Akun">
                                        <i class="far fa-edit"></i> Edit Akun
                                    </a>
                                    <form action="<?= base_url('direktur/karyawan/hapus-akun/' . $akun['id']) ?>" method="post" class="d-inline form-hapus-akun">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn-action-pill btn-action-delete btn-hapus-akun" data-nama="<?= esc($namaUser) ?>" title="Hapus Akun">
                                            <i class="far fa-trash-alt"></i> Hapus Permanen
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="badge bg-light text-success border border-success px-3 py-2 rounded-pill font-weight-bold" style="font-size: 0.8rem;">
                                        <i class="fas fa-user-check me-1"></i> Akun Anda (Aktif Saat Ini)
                                    </span>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($akun_aktif)): ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center rounded-4 shadow-sm p-4 my-2">
                            <i class="fas fa-info-circle fa-2x mb-2 text-primary"></i>
                            <h5 class="fw-bold mb-1">Belum Ada Akun Terdaftar</h5>
                            <p class="mb-0 text-muted small">Silakan buat akun baru di tab "Belum Memiliki Akun".</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- TAB 2: KARYAWAN BELUM MEMILIKI AKUN -->
        <div class="tab-pane fade" id="tab-belum" role="tabpanel" aria-labelledby="tab-belum-tab">
            <div class="row g-3" id="karyawanBelumAkunContainer">
                <?php foreach ($karyawan_belum_akun as $kar): ?>
                    <?php
                        $initial = !empty($kar['nama_lengkap']) ? strtoupper(substr($kar['nama_lengkap'], 0, 1)) : 'K';
                    ?>
                    <div class="col-12 akun-card-wrapper" data-role="" data-status="pending" data-type="belum">
                        <div class="card employee-card-modern p-3 p-sm-4 border-warning">
                            
                            <!-- Header Kartu -->
                            <div class="d-flex align-items-center justify-content-between pb-3 border-bottom border-light flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-3">
                                    <!-- Avatar Lingkaran Inisial Warning -->
                                    <div class="avatar-glow-warning text-white rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0">
                                        <?= $initial ?>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <!-- Nama Karyawan -->
                                            <h3 class="mb-0 fw-bold text-dark" style="font-size: 1.18rem; letter-spacing: -0.2px;">
                                                <?= esc($kar['nama_lengkap']) ?>
                                            </h3>
                                            <span class="id-tag">NIK: <?= esc($kar['nik']) ?></span>
                                        </div>
                                        <div class="mt-1.5">
                                            <span class="status-pill status-pill-pending">
                                                <i class="fas fa-user-clock me-1"></i> Belum Memiliki Akun Login
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bilah Data Horizontal -->
                            <div class="py-3">
                                <div class="row g-2.5">
                                    <!-- Email Karyawan -->
                                    <div class="col-12 col-sm-6 col-md-4">
                                        <div class="data-pill-bar">
                                            <div class="data-label">
                                                <i class="far fa-envelope text-primary"></i> Email Karyawan
                                            </div>
                                            <div class="data-value text-break">
                                                <?= !empty($kar['email']) ? esc($kar['email']) : '<span class="text-muted fst-italic">Belum diset</span>' ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Divisi & Jabatan -->
                                    <div class="col-12 col-sm-6 col-md-4">
                                        <div class="data-pill-bar">
                                            <div class="data-label">
                                                <i class="fas fa-briefcase text-primary"></i> Divisi & Jabatan
                                            </div>
                                            <div class="data-value">
                                                <?= esc($kar['divisi']) ?> <span class="text-muted">|</span> <?= esc($kar['jabatan']) ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Status Karyawan -->
                                    <div class="col-12 col-sm-6 col-md-4">
                                        <div class="data-pill-bar">
                                            <div class="data-label">
                                                <i class="fas fa-user-tag text-primary"></i> Status Kerja
                                            </div>
                                            <div class="data-value">
                                                <?= esc($kar['status_karyawan'] ?? 'Aktif') ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tombol Aksi Generate Akun -->
                            <div class="pt-2 border-top border-light d-flex justify-content-end align-items-center gap-2 flex-wrap">
                                <form action="<?= base_url('direktur/karyawan/generate-akun') ?>" method="post" class="d-inline form-generate-akun">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="karyawan_id" value="<?= $kar['id'] ?>">
                                    <button type="submit" class="btn-action-pill btn-action-generate btn-generate-akun" data-nama="<?= esc($kar['nama_lengkap']) ?>" title="Generate Akun Otomatis">
                                        <i class="fas fa-magic me-1"></i> Generate Akun Otomatis
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($karyawan_belum_akun)): ?>
                    <div class="col-12">
                        <div class="text-center py-5 bg-white rounded-4 shadow-sm border border-light">
                            <div class="mb-3">
                                <i class="fas fa-check-double fa-4x text-success opacity-75"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-1">Semua Karyawan Sudah Memiliki Akun!</h5>
                            <p class="text-muted small mb-3">Seluruh karyawan aktif telah terdaftar dan siap menggunakan sistem.</p>
                            <a href="<?= base_url('direktur/karyawan/tambah') ?>" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm fw-semibold text-sm">
                                <i class="fas fa-user-plus me-1.5"></i> Tambah Karyawan Baru
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- Pesan Jika Data Tidak Ditemukan (Filter / Search Kosong) -->
    <div id="noResultsMessage" class="alert alert-info text-center rounded-4 shadow-sm p-4 d-none my-3">
        <i class="fas fa-search fa-2x mb-2 text-primary"></i>
        <h5 class="fw-bold mb-1">Data Akun Tidak Ditemukan</h5>
        <p class="mb-0 text-muted small">Coba ubah kata kunci pencarian atau reset filter Anda.</p>
    </div>

    <!-- 5. Control Bar Bawah: Per Page + Info + Eye-Catching Responsive Pagination -->
    <div class="card shadow-sm rounded-4 border-0 p-3 mt-4 mb-5 bg-white">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            
            <!-- Pengaturan Jumlah Tampilan Data (5, 10, 25, 50, 75, 100, Semua) -->
            <div class="d-flex align-items-center gap-2">
                <span class="text-xs text-muted fw-bold text-uppercase">Tampilkan:</span>
                <select id="perPageSelect" class="form-select form-select-sm rounded-pill shadow-xs fw-semibold text-dark border-light px-3 py-1.5" style="width: auto; cursor: pointer;">
                    <option value="5" selected>5 Akun / hal</option>
                    <option value="10">10 Akun / hal</option>
                    <option value="25">25 Akun / hal</option>
                    <option value="50">50 Akun / hal</option>
                    <option value="75">75 Akun / hal</option>
                    <option value="100">100 Akun / hal</option>
                    <option value="all">Tampilkan Semua</option>
                </select>
            </div>

            <!-- Teks Info Pagination -->
            <div class="text-muted text-sm fw-semibold text-center" id="paginationInfo">
                Menampilkan 1 - 5 dari data akun
            </div>

            <!-- Responsive Pagination Component -->
            <div id="paginationContainer" class="d-flex justify-content-center">
                <!-- Diisi otomatis secara responsif oleh JavaScript -->
            </div>

        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput   = document.getElementById('searchAkun');
    const filterRole    = document.getElementById('filterRole');
    const filterStatus  = document.getElementById('filterStatus');
    const perPageSelect = document.getElementById('perPageSelect');
    const btnReset      = document.getElementById('btnResetFilter');
    const btnApply      = document.getElementById('btnApplyFilter');
    const btnClearActive = document.getElementById('btnClearActiveFilter');
    const paginationEl  = document.getElementById('paginationContainer');
    const infoEl        = document.getElementById('paginationInfo');
    const noResultsEl   = document.getElementById('noResultsMessage');
    const activeTagsBox = document.getElementById('activeFilterTags');
    const tagContainer  = document.getElementById('filterTagContainer');

    let currentTab = 'tab-aktif'; // 'tab-aktif' or 'tab-belum'
    let currentPage = 1;
    let itemsPerPage = 5;

    // Detect Tab Changes
    const tabButtons = document.querySelectorAll('#accountTabs button[data-bs-toggle="pill"]');
    tabButtons.forEach(btn => {
        btn.addEventListener('shown.bs.tab', function (e) {
            currentTab = e.target.getAttribute('data-bs-target').replace('#', '');
            currentPage = 1;
            filterAndPaginate();
        });
    });

    function getActiveCards() {
        const selector = currentTab === 'tab-aktif' 
            ? '#akunAktifContainer .akun-card-wrapper' 
            : '#karyawanBelumAkunContainer .akun-card-wrapper';
        return Array.from(document.querySelectorAll(selector));
    }

    function filterAndPaginate() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const roleVal    = filterRole ? filterRole.value.toLowerCase().trim() : '';
        const statusVal  = filterStatus ? filterStatus.value.toLowerCase().trim() : '';

        const perPageVal = perPageSelect ? perPageSelect.value : '5';
        itemsPerPage = perPageVal === 'all' ? 999999 : parseInt(perPageVal) || 5;

        updateActiveFilterTags(roleVal, statusVal);

        const cards = getActiveCards();

        let visibleCards = cards.filter(card => {
            const text   = card.innerText.toLowerCase();
            const role   = (card.getAttribute('data-role') || '').toLowerCase();
            const status = (card.getAttribute('data-status') || '').toLowerCase();

            const matchSearch = !searchTerm || text.includes(searchTerm);
            const matchRole   = !roleVal   || role.includes(roleVal);
            const matchStatus = !statusVal || status.includes(statusVal);

            return matchSearch && matchRole && matchStatus;
        });

        const totalVisible = visibleCards.length;
        const totalPages   = Math.ceil(totalVisible / itemsPerPage) || 1;

        if (currentPage > totalPages) {
            currentPage = totalPages;
        }

        // Hide all cards in current tab
        cards.forEach(card => card.style.display = 'none');

        if (totalVisible === 0) {
            if (noResultsEl) noResultsEl.classList.remove('d-none');
            if (infoEl) infoEl.textContent = 'Tidak ada data akun yang cocok.';
            if (paginationEl) paginationEl.innerHTML = '';
            return;
        } else {
            if (noResultsEl) noResultsEl.classList.add('d-none');
        }

        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex   = Math.min(startIndex + itemsPerPage, totalVisible);

        for (let i = startIndex; i < endIndex; i++) {
            visibleCards[i].style.display = 'block';
        }

        if (infoEl) {
            if (itemsPerPage >= 999999) {
                infoEl.textContent = `Menampilkan seluruh ${totalVisible} akun`;
            } else {
                infoEl.textContent = `Menampilkan ${startIndex + 1} - ${endIndex} dari ${totalVisible} akun`;
            }
        }

        renderPagination(totalPages);
    }

    function updateActiveFilterTags(roleVal, statusVal) {
        if (!activeTagsBox || !tagContainer) return;
        tagContainer.innerHTML = '';

        if (roleVal || statusVal) {
            activeTagsBox.classList.remove('d-none');
            if (roleVal) {
                tagContainer.innerHTML += `<span class="active-filter-badge">Role: ${filterRole.options[filterRole.selectedIndex].text}</span>`;
            }
            if (statusVal) {
                tagContainer.innerHTML += `<span class="active-filter-badge">Status: ${filterStatus.options[filterStatus.selectedIndex].text}</span>`;
            }
        } else {
            activeTagsBox.classList.add('d-none');
        }
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

        paginationEl.querySelectorAll('.page-link').forEach(btn => {
            btn.addEventListener('click', function(e) {
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

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            currentPage = 1;
            filterAndPaginate();
        });
    }

    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            currentPage = 1;
            filterAndPaginate();
        });
    }

    if (btnApply) {
        btnApply.addEventListener('click', function() {
            currentPage = 1;
            filterAndPaginate();
        });
    }

    function resetAllFilters() {
        if (filterRole)   filterRole.value   = '';
        if (filterStatus) filterStatus.value = '';
        if (searchInput)  searchInput.value  = '';
        if (perPageSelect) perPageSelect.value = '5';
        currentPage = 1;
        filterAndPaginate();
    }

    if (btnReset) btnReset.addEventListener('click', resetAllFilters);
    if (btnClearActive) btnClearActive.addEventListener('click', resetAllFilters);

    filterAndPaginate();
});

// Real-time Notifikasi & SweetAlert Actions
window.addEventListener('load', function () {
    if (typeof $ === 'undefined' || typeof Swal === 'undefined') return;

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
            timer: 6000,
            timerProgressBar: true
        });
    }

    if (flashError) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            html: flashError,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true
        });
    }

    // SweetAlert Konfirmasi Generate Akun
    $(document).on('click', '.btn-generate-akun', function (e) {
        e.preventDefault();
        const form = $(this).closest('form');
        const nama = $(this).data('nama') || 'karyawan ini';

        Swal.fire({
            title: 'Generate Akun Otomatis',
            text: `Apakah Anda yakin ingin membuatkan akun login otomatis untuk "${nama}"?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-magic me-1"></i> Ya, Generate Akun!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // SweetAlert Konfirmasi Hapus Akun
    $(document).on('click', '.btn-hapus-akun', function (e) {
        e.preventDefault();
        const form = $(this).closest('form');
        const nama = $(this).data('nama') || 'pengguna ini';

        Swal.fire({
            title: 'Konfirmasi Hapus Akun',
            text: `Apakah Anda yakin ingin menghapus akun milik "${nama}" secara permanen? Pengguna tidak akan dapat login lagi.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="far fa-trash-alt me-1"></i> Ya, Hapus Permanen!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>

<?= $this->include('direktur/templates/footer') ?>
