<?php
// app/Views/admin/templates/sidebar.php
$active   = $active   ?? 'dashboard';
$uri      = service('uri');
$segments = $uri->getSegments();
$seg1     = $segments[1] ?? '';
$seg2     = $segments[2] ?? '';

$isSuratActive      = ($seg1 === 'surat');
$isInventarisActive = in_array($seg1, ['inventaris', 'pengadaan']);
$isDokumenActive    = in_array($seg1, ['dokumen', 'dokumen-legal']);
$isFasilitasActive  = ($seg1 === 'fasilitas');
$isPengajuanActive  = in_array($seg1, ['pengajuan', 'form-pengajuan', 'keluhan-saya', 'kasbon']);
$isLaporanActive    = in_array($seg1, ['laporan', 'laporan-harian-saya', 'slip-gaji']);
$isPribadiActive    = in_array($seg1, ['absensi-saya', 'tugas-saya', 'timeline-kerja', 'project-saat-ini', 'profil']);

// Real-time Notification Queries for Admin
$db = \Config\Database::connect();

$notifSurat = 0;
if ($db->tableExists('surat_karyawan')) {
    $notifSurat = $db->table('surat_karyawan')->where('status', 'draft')->countAllResults();
}

$notifATK = 0;
if ($db->tableExists('pengajuan_atk')) {
    $q = $db->table('pengajuan_atk')->where('status', 'menunggu');
    if ($db->fieldExists('deleted_at', 'pengajuan_atk')) $q->where('deleted_at', null);
    $notifATK = $q->countAllResults();
}

$notifAset = 0;
if ($db->tableExists('pengadaan_aset')) {
    $q = $db->table('pengadaan_aset')->where('status', 'menunggu');
    if ($db->fieldExists('deleted_at', 'pengadaan_aset')) $q->where('deleted_at', null);
    $notifAset = $q->countAllResults();
}

$notifPR = 0;
if ($db->tableExists('form_pembelian')) {
    $q = $db->table('form_pembelian')->whereIn('status_direktur', ['Menunggu', 'pending', 'menunggu']);
    if ($db->fieldExists('deleted_at', 'form_pembelian')) $q->where('deleted_at', null);
    $notifPR = $q->countAllResults();
}

$notifKerusakan = 0;
if ($db->tableExists('laporan_kerusakan')) {
    $q = $db->table('laporan_kerusakan')->where('status_tindakan', 'dilaporkan');
    if ($db->fieldExists('deleted_at', 'laporan_kerusakan')) $q->where('deleted_at', null);
    $notifKerusakan = $q->countAllResults();
}

$notifInventarisTotal = $notifATK + $notifAset + $notifPR + $notifKerusakan;

$notifCuti = 0;
if ($db->tableExists('cuti')) {
    $q = $db->table('cuti')->whereIn('status_hrd', ['menunggu', 'pending', 'Menunggu', 'Pending']);
    if ($db->fieldExists('deleted_at', 'cuti')) $q->where('deleted_at', null);
    $notifCuti = $q->countAllResults();
}

$notifKasbon = 0;
if ($db->tableExists('form_kasbon')) {
    $q = $db->table('form_kasbon')->whereIn('status_direktur', ['Menunggu', 'pending', 'menunggu']);
    if ($db->fieldExists('deleted_at', 'form_kasbon')) $q->where('deleted_at', null);
    $notifKasbon = $q->countAllResults();
}

$notifKeluhan = 0;
if ($db->tableExists('keluhan_karyawan')) {
    $q = $db->table('keluhan_karyawan')->whereIn('status', ['dikirim', 'menunggu', 'pending', 'Menunggu']);
    if ($db->fieldExists('deleted_at', 'keluhan_karyawan')) $q->where('deleted_at', null);
    $notifKeluhan = $q->countAllResults();
}

$notifPengajuanTotal = $notifCuti + $notifKasbon + $notifKeluhan;

$notifLaporanHarian = 0;
if ($db->tableExists('laporan_harian')) {
    $q = $db->table('laporan_harian')->where('status', 'menunggu_review');
    if ($db->fieldExists('deleted_at', 'laporan_harian')) $q->where('deleted_at', null);
    $notifLaporanHarian = $q->countAllResults();
}

$notifLaporanTotal = $notifLaporanHarian + $notifKeluhan;
$notifTugasSaya = 0;
if ($db->tableExists('penugasan_harian')) {
    $sessUserId = session()->get('user_id') ?? session()->get('karyawan_id');
    $q = $db->table('penugasan_harian')
        ->where('deleted_at', null)
        ->whereIn('status', ['pending', 'baru', 'proses'])
        ->groupStart()
            ->where('penerima_role', 'admin')
            ->orWhere('penerima_role', 'all');
    if ($sessUserId) {
        $q->orWhere('penerima_id', $sessUserId);
    }
    $q->groupEnd();
    $notifTugasSaya = $q->countAllResults();
}

$notifPribadiTotal = $notifTugasSaya;

function adminSidebarLink($href, $icon, $label, $isActive, $badgeCount = 0) {
    $style = $isActive ? 'background:rgba(255,255,255,0.18);border-left-color:#60a5fa;color:white;' : '';
    $badgeHtml = $badgeCount > 0 ? '<span class="badge bg-danger rounded-pill ms-auto px-2 py-0.5" style="font-size:0.72rem;font-weight:700;box-shadow:0 2px 6px rgba(220,53,69,0.4);">'.$badgeCount.'</span>' : '';
    return '
        <a href="'.$href.'" style="color:rgba(255,255,255,0.85);padding:10px 20px;display:flex;align-items:center;text-decoration:none;transition:all 0.3s;border-left:3px solid transparent;'.$style.'">
            <i class="'.$icon.'" style="width:24px;text-align:center;margin-right:8px;"></i>
            <span style="font-size:0.875rem;">'.$label.'</span>
            '.$badgeHtml.'
        </a>';
}

function adminSubLink($href, $icon, $label, $isActive, $badgeCount = 0) {
    $fw = $isActive ? 'font-weight:600;color:white;' : '';
    $badgeHtml = $badgeCount > 0 ? '<span class="badge bg-danger rounded-pill ms-auto px-2 py-0.5" style="font-size:0.7rem;font-weight:700;box-shadow:0 2px 5px rgba(220,53,69,0.35);">'.$badgeCount.'</span>' : '';
    return '
        <a href="'.$href.'" style="color:rgba(255,255,255,0.78);padding:8px 10px 8px 48px;font-size:0.82rem;display:flex;align-items:center;text-decoration:none;transition:all 0.25s;'.$fw.'">
            <i class="'.$icon.'" style="width:18px;margin-right:7px;"></i><span style="flex-grow:1;">'.$label.'</span>'.$badgeHtml.'
        </a>';
}
?>

<!-- Admin Sidebar -->
<div class="sidebar">
    <!-- Header -->
    <div style="padding: 18px 20px; text-align:center; border-bottom: 1px solid rgba(255,255,255,0.15); background: rgba(0,0,0,0.15);">
        <div style="background: rgba(255,255,255,0.15); border-radius: 50%; width:52px; height:52px; margin: 0 auto 10px; display:flex; align-items:center; justify-content:center;">
            <i class="fas fa-user-shield" style="font-size:1.4rem;"></i>
        </div>
        <h5 style="margin:0; font-weight:700; font-size:1rem; letter-spacing:0.5px;">ADMIN PANEL</h5>
        <p style="opacity:0.75; font-size:0.72rem; margin:4px 0 0; font-weight:500; text-transform:uppercase; letter-spacing:1px;">CDW Engineering</p>
    </div>

    <!-- Menu -->
    <nav style="padding: 10px 0;">

        <!-- Dashboard -->
        <div style="padding: 6px 0;">
            <?= adminSidebarLink(base_url('admin'), 'fas fa-tachometer-alt', 'Dashboard', $seg1 === '' || $seg1 === 'dashboard') ?>
        </div>

        <div style="padding: 4px 16px 2px; font-size:0.65rem; color:rgba(255,255,255,0.45); font-weight:600; letter-spacing:1.5px; text-transform:uppercase; margin-top:8px;">
            ADMINISTRASI
        </div>

        <!-- Surat Menyurat (Unified Module) -->
        <div style="padding: 2px 0;">
            <?= adminSidebarLink(base_url('admin/surat'), 'fas fa-envelope', 'Surat Menyurat', $seg1 === 'surat', $notifSurat) ?>
        </div>

        <!-- Pengadaan & Aset / ATK & Inventaris -->
        <div>
            <a data-bs-toggle="collapse" href="#inventarisMenu"
               style="color:rgba(255,255,255,0.85);padding:10px 20px;display:flex;align-items:center;justify-content:space-between;text-decoration:none;border-left:3px solid <?= $isInventarisActive ? '#ce93d8' : 'transparent' ?>;<?= $isInventarisActive ? 'background:rgba(255,255,255,0.15);' : '' ?>">
                <div style="display:flex;align-items:center;">
                    <i class="fas fa-boxes" style="width:24px;text-align:center;margin-right:8px;"></i>
                    <span style="font-size:0.875rem;">Pengadaan & Aset</span>
                </div>
                <div style="display:flex;align-items:center;gap:6px;">
                    <?php if ($notifInventarisTotal > 0): ?>
                        <span class="badge bg-danger rounded-pill px-2 py-0.5" style="font-size:0.72rem;font-weight:700;box-shadow:0 2px 6px rgba(220,53,69,0.4);"><?= $notifInventarisTotal ?></span>
                    <?php endif; ?>
                    <i class="fas fa-chevron-down" style="font-size:0.75rem;"></i>
                </div>
            </a>
            <div class="collapse <?= $isInventarisActive ? 'show' : '' ?>" id="inventarisMenu" style="background:rgba(0,0,0,0.15);">
                <?= adminSubLink(base_url('admin/inventaris/pengajuan-atk'),  'fas fa-pen-nib',        'Pengajuan ATK',                       $seg2==='pengajuan-atk', $notifATK) ?>
                <?= adminSubLink(base_url('admin/inventaris/stok-atk'),       'fas fa-clipboard-list', 'Monitoring Stok ATK',                $seg2==='stok-atk') ?>
                <?= adminSubLink(base_url('admin/inventaris/aset'),           'fas fa-desktop',        'Pengadaan Aset',                      $seg2==='aset' || $seg2==='inventaris-kantor', $notifAset) ?>
                <?= adminSubLink(base_url('admin/inventaris/pembelian'),      'fas fa-shopping-cart',  'Pencatatan & Tracking Pembelian (PR)',$seg2==='pembelian', $notifPR) ?>
                <?= adminSubLink(base_url('admin/inventaris/kerusakan'),      'fas fa-tools',          'Kerusakan Alat',                      $seg2==='kerusakan', $notifKerusakan) ?>
                <?= adminSubLink(base_url('admin/inventaris/gudang'),         'fas fa-warehouse',      'Monitoring Gudang',                   $seg2==='gudang') ?>
            </div>
        </div>

        <!-- Dokumen -->
        <div>
            <a data-bs-toggle="collapse" href="#dokumenMenu"
               style="color:rgba(255,255,255,0.85);padding:10px 20px;display:flex;align-items:center;justify-content:space-between;text-decoration:none;border-left:3px solid <?= $isDokumenActive ? '#ce93d8' : 'transparent' ?>;<?= $isDokumenActive ? 'background:rgba(255,255,255,0.15);' : '' ?>">
                <div style="display:flex;align-items:center;">
                    <i class="fas fa-folder-open" style="width:24px;text-align:center;margin-right:8px;"></i>
                    <span style="font-size:0.875rem;">Dokumen</span>
                </div>
                <i class="fas fa-chevron-down" style="font-size:0.75rem;"></i>
            </a>
            <div class="collapse <?= $isDokumenActive ? 'show' : '' ?>" id="dokumenMenu" style="background:rgba(0,0,0,0.15);">
                <?= adminSubLink(base_url('admin/dokumen/penting'),    'fas fa-file-invoice', 'Dokumen Penting',    $seg1==='dokumen' && ($seg2==='' || $seg2==='penting')) ?>
                <?= adminSubLink(base_url('admin/dokumen/sertifikat'), 'fas fa-certificate',  'Dokumen Sertifikat', $seg1==='dokumen' && $seg2==='sertifikat') ?>
                <?= adminSubLink(base_url('admin/dokumen/kontak'),     'fas fa-address-book', 'Kontak Project',     $seg1==='dokumen' && $seg2==='kontak') ?>
            </div>
        </div>

        <!-- Fasilitas & Tamu -->
        <div>
            <a data-bs-toggle="collapse" href="#fasilitasMenu"
               style="color:rgba(255,255,255,0.85);padding:10px 20px;display:flex;align-items:center;justify-content:space-between;text-decoration:none;border-left:3px solid <?= $isFasilitasActive ? '#ce93d8' : 'transparent' ?>;<?= $isFasilitasActive ? 'background:rgba(255,255,255,0.15);' : '' ?>">
                <div style="display:flex;align-items:center;">
                    <i class="fas fa-concierge-bell" style="width:24px;text-align:center;margin-right:8px;"></i>
                    <span style="font-size:0.875rem;">Fasilitas & Tamu</span>
                </div>
                <i class="fas fa-chevron-down" style="font-size:0.75rem;"></i>
            </a>
            <div class="collapse <?= $isFasilitasActive ? 'show' : '' ?>" id="fasilitasMenu" style="background:rgba(0,0,0,0.15);">
                <?= adminSubLink(base_url('admin/fasilitas/buku-tamu'),    'fas fa-book-open',  'Buku Tamu',          $seg2===''||$seg2==='buku-tamu') ?>
                <?= adminSubLink(base_url('admin/fasilitas/booking-ruang'),'fas fa-calendar-check','Booking Ruang Meeting', $seg2==='booking-ruang') ?>
                <?= adminSubLink(base_url('admin/fasilitas/kendaraan'),    'fas fa-car',        'Koordinasi Kendaraan',$seg2==='kendaraan') ?>
            </div>
        </div>

        <!-- Pengajuan -->
        <div>
            <a data-bs-toggle="collapse" href="#pengajuanMenu"
               style="color:rgba(255,255,255,0.85);padding:10px 20px;display:flex;align-items:center;justify-content:space-between;text-decoration:none;border-left:3px solid <?= $isPengajuanActive ? '#ce93d8' : 'transparent' ?>;<?= $isPengajuanActive ? 'background:rgba(255,255,255,0.15);' : '' ?>">
                <div style="display:flex;align-items:center;flex-grow:1;margin-right:8px;">
                    <i class="fas fa-clipboard-list" style="width:24px;text-align:center;margin-right:8px;"></i>
                    <span style="font-size:0.875rem;">Pengajuan</span>
                    <?php if ($notifPengajuanTotal > 0): ?>
                        <span class="badge bg-danger rounded-pill ms-auto me-2 px-2 py-0.5" style="font-size:0.72rem;font-weight:700;box-shadow:0 2px 6px rgba(220,53,69,0.4);"><?= $notifPengajuanTotal ?></span>
                    <?php endif; ?>
                </div>
                <i class="fas fa-chevron-down" style="font-size:0.75rem;"></i>
            </a>
            <div class="collapse <?= $isPengajuanActive ? 'show' : '' ?>" id="pengajuanMenu" style="background:rgba(0,0,0,0.15);">
                <?= adminSubLink(base_url('admin/pengajuan/semua'),    'fas fa-list-alt',        'Pengajuan', $seg1==='pengajuan' && ($seg2==='' || $seg2==='semua'), $notifPengajuanTotal) ?>
                <?= adminSubLink(base_url('admin/pengajuan/cuti'),     'fas fa-umbrella-beach',  'Cuti',      $seg1==='pengajuan' && $seg2==='cuti', $notifCuti) ?>
                <?= adminSubLink(base_url('admin/pengajuan/kasbon'),   'fas fa-hand-holding-usd','Kasbon',    $seg1==='pengajuan' && $seg2==='kasbon', $notifKasbon) ?>
                <?= adminSubLink(base_url('admin/laporan/keluhan'),      'fas fa-comment-alt',     'Keluhan',   ($seg1==='laporan' && $seg2==='keluhan') || $seg1==='keluhan-saya', $notifKeluhan) ?>
            </div>
        </div>

        <!-- Laporan & Keluhan -->
        <div>
            <a data-bs-toggle="collapse" href="#laporanMenu"
               style="color:rgba(255,255,255,0.85);padding:10px 20px;display:flex;align-items:center;justify-content:space-between;text-decoration:none;border-left:3px solid <?= $isLaporanActive ? '#ce93d8' : 'transparent' ?>;<?= $isLaporanActive ? 'background:rgba(255,255,255,0.15);' : '' ?>">
                <div style="display:flex;align-items:center;flex-grow:1;margin-right:8px;">
                    <i class="fas fa-chart-bar" style="width:24px;text-align:center;margin-right:8px;"></i>
                    <span style="font-size:0.875rem;">Laporan & Keluhan</span>
                    <?php if ($notifLaporanTotal > 0): ?>
                        <span class="badge bg-danger rounded-pill ms-auto me-2 px-2 py-0.5" style="font-size:0.72rem;font-weight:700;box-shadow:0 2px 6px rgba(220,53,69,0.4);"><?= $notifLaporanTotal ?></span>
                    <?php endif; ?>
                </div>
                <i class="fas fa-chevron-down" style="font-size:0.75rem;"></i>
            </a>
            <div class="collapse <?= $isLaporanActive ? 'show' : '' ?>" id="laporanMenu" style="background:rgba(0,0,0,0.15);">
                <?= adminSubLink(base_url('admin/laporan/kerja-harian'),  'fas fa-tasks',          'Laporan Kerja Harian',$seg1==='laporan' && ($seg2==='kerja-harian' || $seg1==='laporan-harian-saya'), $notifLaporanHarian) ?>
                <?= adminSubLink(base_url('admin/laporan/keluhan'),       'fas fa-comment-dots',   'Keluhan',              $seg1==='laporan' && $seg2==='keluhan', $notifKeluhan) ?>
                <?= adminSubLink(base_url('admin/slip-gaji'),             'fas fa-money-bill-wave','Slip Gaji',           $seg1==='slip-gaji') ?>
            </div>
        </div>

        <div style="padding: 4px 16px 2px; font-size:0.65rem; color:rgba(255,255,255,0.45); font-weight:600; letter-spacing:1.5px; text-transform:uppercase; margin-top:10px;">
            MENU PRIBADI
        </div>

        <!-- Menu Pribadi -->
        <div>
            <a data-bs-toggle="collapse" href="#pribadiMenu"
               style="color:rgba(255,255,255,0.85);padding:10px 20px;display:flex;align-items:center;justify-content:space-between;text-decoration:none;border-left:3px solid <?= $isPribadiActive ? '#ce93d8' : 'transparent' ?>;<?= $isPribadiActive ? 'background:rgba(255,255,255,0.15);' : '' ?>">
                <div style="display:flex;align-items:center;flex-grow:1;margin-right:8px;">
                    <i class="fas fa-user-circle" style="width:24px;text-align:center;margin-right:8px;"></i>
                    <span style="font-size:0.875rem;">Menu Pribadi</span>
                    <?php if ($notifPribadiTotal > 0): ?>
                        <span class="badge bg-danger rounded-pill ms-auto me-2 px-2 py-0.5" style="font-size:0.72rem;font-weight:700;box-shadow:0 2px 6px rgba(220,53,69,0.4);"><?= $notifPribadiTotal ?></span>
                    <?php endif; ?>
                </div>
                <i class="fas fa-chevron-down" style="font-size:0.75rem;"></i>
            </a>
            <div class="collapse <?= $isPribadiActive ? 'show' : '' ?>" id="pribadiMenu" style="background:rgba(0,0,0,0.15);">
                <?= adminSubLink(base_url('admin/absensi-saya'),      'fas fa-fingerprint',   'Absensi',          $seg1==='absensi-saya') ?>
                <?= adminSubLink(base_url('admin/tugas-saya'),        'fas fa-tasks',         'Tugas Hari Ini',   $seg1==='tugas-saya') ?>
                <?= adminSubLink(base_url('admin/timeline-kerja'),    'fas fa-stream',        'Timeline Kerja',   $seg1==='timeline-kerja') ?>
                <?= adminSubLink(base_url('admin/project-saat-ini'),  'fas fa-project-diagram','Project Saat Ini', $seg1==='project-saat-ini') ?>
                <?= adminSubLink(base_url('admin/profil'),            'fas fa-id-badge',      'Profil',           $seg1==='profil') ?>
            </div>
        </div>

        <!-- Keluar -->
        <div style="margin-top: 16px; border-top: 1px solid rgba(255,255,255,0.12); padding-top: 12px;">
            <a href="<?= base_url('logout') ?>" style="color:#f48fb1;padding:11px 20px;display:flex;align-items:center;text-decoration:none;font-weight:600;">
                <i class="fas fa-sign-out-alt" style="width:24px;text-align:center;margin-right:8px;"></i>
                <span style="font-size:0.875rem;">Keluar</span>
            </a>
        </div>

    </nav>
</div>

<style>
    @keyframes badgePulse {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
        70% { transform: scale(1.05); box-shadow: 0 0 0 6px rgba(220, 53, 69, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
    }
    .sidebar .badge.bg-danger {
        animation: badgePulse 2s infinite;
    }
</style>