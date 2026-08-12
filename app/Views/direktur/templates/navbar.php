<?php
// app/Views/direktur/templates/navbar.php
$title = $title ?? 'Dashboard Direktur';
$subtitle = $subtitle ?? date('l, d F Y');
$user = $user ?? ['name' => 'Direktur', 'role' => 'direktur'];

if (!function_exists('getSafeNotifCount')) {
    function getSafeNotifCount($db, $table, $columns, $values) {
        if (!$db->tableExists($table)) return 0;
        foreach ((array)$columns as $col) {
            if ($db->fieldExists($col, $table)) {
                return $db->table($table)->whereIn($col, (array)$values)->countAllResults();
            }
        }
        return 0;
    }
}

// Real-time Notifications Query
$db = \Config\Database::connect();
$notifList = [];

$kasbonCount = getSafeNotifCount($db, 'form_kasbon', ['status_direktur', 'status'], ['Menunggu', 'menunggu', 'pending']);
if ($kasbonCount > 0) {
    $notifList[] = [
        'icon' => 'fas fa-wallet',
        'bg' => 'bg-warning text-dark',
        'title' => "$kasbonCount Kasbon Menunggu Approval",
        'desc' => 'Pengajuan kasbon karyawan butuh persetujuan',
        'url' => base_url('direktur/keuangan/kasbon?status=pending')
    ];
}

$pembelianCount = getSafeNotifCount($db, 'form_pembelian', ['status_direktur', 'status'], ['Menunggu', 'menunggu', 'pending']);
if ($pembelianCount > 0) {
    $notifList[] = [
        'icon' => 'fas fa-shopping-cart',
        'bg' => 'bg-info text-white',
        'title' => "$pembelianCount Form Pembelian (PR)",
        'desc' => 'Pengajuan pengadaan barang butuh review',
        'url' => base_url('direktur/keuangan/pembelian?status=pending')
    ];
}

$laporanCount = getSafeNotifCount($db, 'laporan_harian', ['status'], ['menunggu_review', 'pending', 'menunggu']);
if ($laporanCount > 0) {
    $notifList[] = [
        'icon' => 'fas fa-file-alt',
        'bg' => 'bg-primary text-white',
        'title' => "$laporanCount Laporan Harian Masuk",
        'desc' => 'Laporan kerja staf belum direview',
        'url' => base_url('direktur/proyek/monitoring-laporan?status=menunggu_review')
    ];
}

$atkCount = getSafeNotifCount($db, 'pengajuan_atk', ['status'], ['menunggu', 'pending']);
if ($atkCount > 0) {
    $notifList[] = [
        'icon' => 'fas fa-pen-nib',
        'bg' => 'bg-success text-white',
        'title' => "$atkCount Pengajuan ATK Baru",
        'desc' => 'Pengajuan alat tulis kantor butuh persetujuan',
        'url' => base_url('direktur/pengadaan/pengajuan-atk')
    ];
}

$asetCount = getSafeNotifCount($db, 'pengadaan_aset', ['status'], ['menunggu', 'pending']);
if ($asetCount > 0) {
    $notifList[] = [
        'icon' => 'fas fa-desktop',
        'bg' => 'bg-secondary text-white',
        'title' => "$asetCount Usulan Aset Baru",
        'desc' => 'Pengadaan aset perusahaan butuh approval',
        'url' => base_url('direktur/pengadaan/aset')
    ];
}

$kerusakanCount = getSafeNotifCount($db, 'laporan_kerusakan', ['status_tindakan', 'status'], ['dilaporkan', 'menunggu', 'pending']);
if ($kerusakanCount > 0) {
    $notifList[] = [
        'icon' => 'fas fa-tools',
        'bg' => 'bg-danger text-white',
        'title' => "$kerusakanCount Kerusakan Alat",
        'desc' => 'Laporan kerusakan barang perlu tindakan',
        'url' => base_url('direktur/pengadaan/kerusakan')
    ];
}

$suratCount = getSafeNotifCount($db, 'surat_karyawan', ['status', 'status_surat'], ['draft', 'Draft']);
if ($suratCount > 0) {
    $notifList[] = [
        'icon' => 'fas fa-envelope-open-text',
        'bg' => 'bg-info text-white',
        'title' => "$suratCount Draft Surat (Kontrak/SP)",
        'desc' => 'Draft surat karyawan siap diterbitkan',
        'url' => base_url('direktur/karyawan/surat')
    ];
}

$izinCount = getSafeNotifCount($db, 'form_izin', ['status_keseluruhan', 'status_atasan', 'status'], ['menunggu', 'pending', 'Menunggu', 'Pending']);
if ($izinCount > 0) {
    $notifList[] = [
        'icon' => 'fas fa-clipboard-check',
        'bg' => 'bg-primary text-white',
        'title' => "$izinCount Permohonan Izin",
        'desc' => 'Pengajuan izin karyawan butuh approval',
        'url' => base_url('direktur/karyawan/pengajuan')
    ];
}

$cutiCount = getSafeNotifCount($db, 'cuti', ['status_direktur', 'status_pengajuan', 'status'], ['menunggu', 'pending', 'Menunggu', 'Pending']);
if ($cutiCount > 0) {
    $notifList[] = [
        'icon' => 'fas fa-umbrella-beach',
        'bg' => 'bg-warning text-dark',
        'title' => "$cutiCount Pengajuan Cuti",
        'desc' => 'Permohonan cuti karyawan butuh persetujuan',
        'url' => base_url('direktur/karyawan/cuti')
    ];
}

$keluhanCount = getSafeNotifCount($db, 'keluhan_karyawan', ['status'], ['dikirim', 'menunggu', 'pending', 'Menunggu', 'Pending']);
if ($keluhanCount > 0) {
    $notifList[] = [
        'icon' => 'fas fa-comments',
        'bg' => 'bg-danger text-white',
        'title' => "$keluhanCount Keluhan Karyawan",
        'desc' => 'Laporan keluhan karyawan baru masuk',
        'url' => base_url('direktur/karyawan/keluhan')
    ];
}

$totalNotif = count($notifList);
?>
<!-- Main Content Area -->
<div class="main-content">
    <!-- Top Navigation -->
    <nav class="top-navbar navbar navbar-expand-lg glass-effect" style="
        height: var(--header-height);
        padding: 0 25px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.8);
        position: sticky;
        top: 0;
        z-index: 1000;
        backdrop-filter: blur(10px);
    ">
        <div class="container-fluid p-0 d-flex justify-content-between align-items-center flex-nowrap">
            <!-- Left Side: Page Title and Breadcrumb -->
            <div class="d-flex align-items-center" style="min-width: 0;">
                <!-- Sidebar Toggle -->
                <button class="btn btn-modern-outline me-3" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                
                <!-- Page Title -->
                <div class="d-flex flex-column">
                    <h1 class="page-title mb-0 text-truncate" style="max-width: 100%; font-size: clamp(1rem, 2vw + 0.5rem, 1.5rem);">
                        <span class="text-gradient"><?= htmlspecialchars($title) ?></span>
                    </h1>
                    <div class="d-none d-sm-flex align-items-center gap-2">
                        <small class="text-muted">
                            <i class="far fa-calendar-alt me-1"></i>
                            <?= htmlspecialchars($subtitle) ?>
                        </small>
                    </div>
                </div>
            </div>

            <!-- Right Side: User Info, Notifications, etc. -->
            <div class="d-flex align-items-center gap-2 gap-sm-3 flex-shrink-0">

                <!-- Notifications -->
                <div class="dropdown position-relative">
                    <button class="btn btn-link text-dark p-0 position-relative" type="button" data-bs-toggle="dropdown" 
                            style="text-decoration: none;">
                        <i class="fas fa-bell fa-lg"></i>
                        <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle navbar-badge <?= $totalNotif > 0 ? '' : 'd-none' ?>" style="font-size: 0.7rem; padding: 3px 6px;">
                            <?= $totalNotif ?>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-0" style="
                        min-width: 330px;
                        border: none;
                        box-shadow: var(--shadow-lg);
                        border-radius: var(--border-radius-sm);
                        overflow: hidden;
                        margin-top: 10px;
                    ">
                        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold">Notifikasi Direktur</h6>
                            <span class="badge bg-primary rounded-pill navbar-notif-action-badge"><?= $totalNotif ?> Perlu Action</span>
                        </div>
                        <div style="max-height: 320px; overflow-y: auto;">
                            <?php if(empty($notifList)): ?>
                                <div class="p-4 text-center text-muted">
                                    <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                                    <p class="mb-0 small">Semua pengajuan telah diproses!</p>
                                </div>
                            <?php else: ?>
                                <?php foreach($notifList as $n): ?>
                                <a href="<?= $n['url'] ?>" class="dropdown-item p-3 border-bottom text-wrap">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div class="<?= $n['bg'] ?> rounded-circle d-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="<?= $n['icon'] ?>"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fs-6 fw-bold"><?= esc($n['title']) ?></h6>
                                            <small class="text-muted d-block" style="font-size: 0.8rem;"><?= esc($n['desc']) ?></small>
                                        </div>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- User Profile -->
                <div class="dropdown">
                    <button class="btn p-0 d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" 
                            style="border: none; background: none;">
                        <div class="position-relative">
                            <div class="user-avatar bg-primary text-white d-flex align-items-center justify-content-center" 
                                 style="width: 45px; height: 45px; border-radius: 50%; font-weight: 600; font-size: 1.1rem;">
                                <?= strtoupper(substr($user['name'] ?? 'D', 0, 1)) ?>
                            </div>
                            <div class="position-absolute bottom-0 end-0 bg-success rounded-circle border border-2 border-white" 
                                 style="width: 12px; height: 12px;"></div>
                        </div>
                        <div class="d-none d-md-block text-start" style="line-height: 1.2;">
                            <strong style="font-size: 0.9rem;"><?= htmlspecialchars($user['name'] ?? 'Direktur') ?></strong><br>
                            <small class="text-muted" style="font-size: 0.75rem;">
                                <?= ucfirst($user['role'] ?? 'direktur') ?>
                            </small>
                        </div>
                        <i class="fas fa-chevron-down text-muted ms-1"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" style="
                        min-width: 200px;
                        border: none;
                        box-shadow: var(--shadow-lg);
                        border-radius: var(--border-radius-sm);
                        margin-top: 10px;
                    ">
                        <div class="p-3 border-bottom">
                            <h6 class="mb-0"><?= htmlspecialchars($user['name'] ?? 'Direktur') ?></h6>
                            <small class="text-muted"><?= ucfirst($user['role'] ?? 'direktur') ?></small>
                        </div>
                        <a class="dropdown-item p-2 px-3" href="<?= base_url('logout') ?>">
                            <i class="fas fa-sign-out-alt text-danger me-2"></i> Keluar
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </nav>