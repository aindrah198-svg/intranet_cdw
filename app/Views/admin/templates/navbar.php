<?php
// app/Views/admin/templates/navbar.php
$title    = $title    ?? 'Dashboard';
$subtitle = $subtitle ?? date('l, d F Y');
$user     = $user     ?? ['name' => 'Administrator', 'role' => 'admin'];

// Real-time Notification Queries for Admin Navbar Dropdown
$db = \Config\Database::connect();
$adminNotifList = [];

if ($db->tableExists('surat_karyawan')) {
    $suratCount = $db->table('surat_karyawan')->where('status', 'draft')->countAllResults();
    if ($suratCount > 0) {
        $adminNotifList[] = [
            'icon'  => 'fas fa-envelope',
            'bg'    => 'bg-info text-white',
            'title' => "$suratCount Draft Surat Menyurat",
            'desc'  => 'Draft surat karyawan siap diterbitkan',
            'url'   => base_url('admin/surat')
        ];
    }
}

if ($db->tableExists('pengajuan_atk')) {
    $q = $db->table('pengajuan_atk')->where('status', 'menunggu');
    if ($db->fieldExists('deleted_at', 'pengajuan_atk')) $q->where('deleted_at', null);
    $atkCount = $q->countAllResults();
    if ($atkCount > 0) {
        $adminNotifList[] = [
            'icon'  => 'fas fa-pen-nib',
            'bg'    => 'bg-success text-white',
            'title' => "$atkCount Pengajuan ATK Baru",
            'desc'  => 'Permohonan ATK perlu diproses admin',
            'url'   => base_url('admin/inventaris/pengajuan-atk')
        ];
    }
}

if ($db->tableExists('pengadaan_aset')) {
    $q = $db->table('pengadaan_aset')->where('status', 'menunggu');
    if ($db->fieldExists('deleted_at', 'pengadaan_aset')) $q->where('deleted_at', null);
    $asetCount = $q->countAllResults();
    if ($asetCount > 0) {
        $adminNotifList[] = [
            'icon'  => 'fas fa-desktop',
            'bg'    => 'bg-secondary text-white',
            'title' => "$asetCount Pengadaan Aset Baru",
            'desc'  => 'Usulan aset baru perlu tindakan admin',
            'url'   => base_url('admin/inventaris/aset')
        ];
    }
}

if ($db->tableExists('form_pembelian')) {
    $q = $db->table('form_pembelian')->whereIn('status_direktur', ['Menunggu', 'pending', 'menunggu']);
    if ($db->fieldExists('deleted_at', 'form_pembelian')) $q->where('deleted_at', null);
    $prCount = $q->countAllResults();
    if ($prCount > 0) {
        $adminNotifList[] = [
            'icon'  => 'fas fa-shopping-cart',
            'bg'    => 'bg-warning text-dark',
            'title' => "$prCount Tracking Pembelian (PR)",
            'desc'  => 'Form pembelian butuh review',
            'url'   => base_url('admin/inventaris/pembelian')
        ];
    }
}

if ($db->tableExists('laporan_kerusakan')) {
    $q = $db->table('laporan_kerusakan')->where('status_tindakan', 'dilaporkan');
    if ($db->fieldExists('deleted_at', 'laporan_kerusakan')) $q->where('deleted_at', null);
    $kerusakanCount = $q->countAllResults();
    if ($kerusakanCount > 0) {
        $adminNotifList[] = [
            'icon'  => 'fas fa-tools',
            'bg'    => 'bg-danger text-white',
            'title' => "$kerusakanCount Kerusakan Alat",
            'desc'  => 'Laporan kerusakan barang perlu perbaikan',
            'url'   => base_url('admin/inventaris/kerusakan')
        ];
    }
}

if ($db->tableExists('cuti')) {
    $q = $db->table('cuti')->whereIn('status_hrd', ['menunggu', 'pending', 'Menunggu', 'Pending']);
    if ($db->fieldExists('deleted_at', 'cuti')) $q->where('deleted_at', null);
    $cutiCount = $q->countAllResults();
    if ($cutiCount > 0) {
        $adminNotifList[] = [
            'icon'  => 'fas fa-umbrella-beach',
            'bg'    => 'bg-primary text-white',
            'title' => "$cutiCount Pengajuan Cuti",
            'desc'  => 'Permohonan cuti karyawan perlu verifikasi',
            'url'   => base_url('admin/pengajuan/cuti')
        ];
    }
}

if ($db->tableExists('form_kasbon')) {
    $q = $db->table('form_kasbon')->whereIn('status_direktur', ['Menunggu', 'pending', 'menunggu']);
    if ($db->fieldExists('deleted_at', 'form_kasbon')) $q->where('deleted_at', null);
    $kasbonCount = $q->countAllResults();
    if ($kasbonCount > 0) {
        $adminNotifList[] = [
            'icon'  => 'fas fa-hand-holding-usd',
            'bg'    => 'bg-warning text-dark',
            'title' => "$kasbonCount Pengajuan Kasbon",
            'desc'  => 'Pengajuan kasbon karyawan butuh review',
            'url'   => base_url('admin/pengajuan/kasbon')
        ];
    }
}

if ($db->tableExists('keluhan_karyawan')) {
    $q = $db->table('keluhan_karyawan')->whereIn('status', ['dikirim', 'menunggu', 'pending', 'Menunggu']);
    if ($db->fieldExists('deleted_at', 'keluhan_karyawan')) $q->where('deleted_at', null);
    $keluhanCount = $q->countAllResults();
    if ($keluhanCount > 0) {
        $adminNotifList[] = [
            'icon'  => 'fas fa-comment-alt',
            'bg'    => 'bg-danger text-white',
            'title' => "$keluhanCount Keluhan Karyawan",
            'desc'  => 'Laporan keluhan karyawan baru masuk',
            'url'   => base_url('admin/laporan/keluhan')
        ];
    }
}

if ($db->tableExists('laporan_harian')) {
    $q = $db->table('laporan_harian')->where('status', 'menunggu_review');
    if ($db->fieldExists('deleted_at', 'laporan_harian')) $q->where('deleted_at', null);
    $laporanCount = $q->countAllResults();
    if ($laporanCount > 0) {
        $adminNotifList[] = [
            'icon'  => 'fas fa-tasks',
            'bg'    => 'bg-primary text-white',
            'title' => "$laporanCount Laporan Kerja Harian",
            'desc'  => 'Laporan kerja staf belum direview',
            'url'   => base_url('admin/laporan/kerja-harian')
        ];
    }
}

$totalAdminNotif = count($adminNotifList);
?>
<div class="main-content">
    <nav style="
        height: var(--header-height);
        padding: 0 25px;
        background: rgba(255,255,255,0.93);
        backdrop-filter: blur(12px);
        border-bottom: 1px solid rgba(30,60,114,0.12);
        position: sticky; top: 0; z-index: 900;
        display: flex; align-items: center; justify-content: space-between;
        box-shadow: 0 2px 10px rgba(30,60,114,0.06);
    ">
        <div style="display:flex;align-items:center;gap:12px;">
            <button type="button" class="btn btn-light border-0 shadow-sm rounded-circle d-flex align-items-center justify-content-center" id="sidebarToggleBtn" onclick="toggleSidebar()" style="width:38px;height:38px;color:#1e3c72;cursor:pointer;flex-shrink:0;">
                <i class="fas fa-bars fs-6"></i>
            </button>
            <div>
                <h1 style="font-size:1.2rem;font-weight:700;margin:0;color:#1e3c72;"><?= htmlspecialchars($title) ?></h1>
                <small style="color:#999;font-size:0.78rem;">
                    <i class="far fa-calendar-alt me-1"></i><?= htmlspecialchars($subtitle) ?>
                    &nbsp;•&nbsp;
                    <i class="far fa-clock me-1"></i><span id="liveClock"><?= date('H:i:s') ?></span>
                </small>
            </div>
        </div>

        <div style="display:flex;align-items:center;gap:14px;">
            <!-- Notifications Bell Dropdown -->
            <div class="dropdown position-relative">
                <button class="btn btn-link text-dark p-0 position-relative" type="button" data-bs-toggle="dropdown" style="text-decoration:none; color:#1e3c72;">
                    <i class="fas fa-bell fa-lg" style="color:#1e3c72;"></i>
                    <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle navbar-badge <?= $totalAdminNotif > 0 ? '' : 'd-none' ?>" style="font-size: 0.68rem; padding: 3px 6px;">
                        <?= $totalAdminNotif ?>
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu-end p-0" style="
                    min-width: 330px;
                    border: none;
                    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
                    border-radius: 10px;
                    overflow: hidden;
                    margin-top: 10px;
                ">
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center" style="background:#f8fafc;">
                        <h6 class="mb-0 fw-bold" style="font-size:0.9rem; color:#1e3c72;">Notifikasi Admin</h6>
                        <span class="badge bg-primary rounded-pill"><?= $totalAdminNotif ?> Perlu Action</span>
                    </div>
                    <div style="max-height: 320px; overflow-y: auto;">
                        <?php if(empty($adminNotifList)): ?>
                            <div class="p-4 text-center text-muted">
                                <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                                <p class="mb-0 small">Semua pengajuan telah diproses!</p>
                            </div>
                        <?php else: ?>
                            <?php foreach($adminNotifList as $n): ?>
                            <a href="<?= $n['url'] ?>" class="dropdown-item p-3 border-bottom text-wrap d-flex align-items-center" style="transition:background 0.2s;">
                                <div class="me-3">
                                    <div class="<?= $n['bg'] ?> rounded-circle d-flex align-items-center justify-content-center" style="width:38px; height:38px; font-size:0.9rem;">
                                        <i class="<?= $n['icon'] ?>"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold" style="font-size:0.85rem; color:#1e293b;"><?= esc($n['title']) ?></h6>
                                    <small class="text-muted d-block" style="font-size:0.78rem;"><?= esc($n['desc']) ?></small>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <span style="background:linear-gradient(135deg,#1e3c72,#2a5298);color:white;padding:4px 12px;border-radius:20px;font-size:0.75rem;font-weight:600;">
                <i class="fas fa-user-shield me-1"></i> Admin Panel
            </span>

            <div class="dropdown">
                <button class="btn btn-link p-0 d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" style="text-decoration:none;color:#1e3c72;">
                    <div style="width:36px;height:36px;background:linear-gradient(135deg,#1e3c72,#2563eb);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:0.9rem;font-weight:700;">
                        <?= strtoupper(substr($user['name'] ?? 'A', 0, 1)) ?>
                    </div>
                    <div style="text-align:left;line-height:1.2;">
                        <div style="font-size:0.82rem;font-weight:600;color:#1e3c72;"><?= htmlspecialchars($user['name'] ?? 'Admin') ?></div>
                        <div style="font-size:0.7rem;color:#2563eb;text-transform:uppercase;">Administrator</div>
                    </div>
                    <i class="fas fa-chevron-down" style="font-size:0.7rem;color:#2563eb;"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" style="border:none;box-shadow:0 8px 25px rgba(0,0,0,0.12);border-radius:10px;padding:8px;">
                    <a class="dropdown-item" href="<?= base_url('admin/profil') ?>" style="border-radius:6px;padding:8px 14px;font-size:0.85rem;">
                        <i class="fas fa-id-badge me-2 text-purple"></i> Profil Saya
                    </a>
                    <div class="dropdown-divider" style="margin:4px 0;"></div>
                    <a class="dropdown-item text-danger" href="<?= base_url('logout') ?>" style="border-radius:6px;padding:8px 14px;font-size:0.85rem;">
                        <i class="fas fa-sign-out-alt me-2"></i> Keluar
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="content-wrapper">