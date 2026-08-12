<?php
// app/Views/admin_panel/templates/sidebar.php
$active   = $active   ?? 'dashboard';
$uri      = service('uri');
$segments = $uri->getSegments();
$seg1     = $segments[1] ?? '';
$seg2     = $segments[2] ?? '';

$isSuratActive      = ($seg1 === 'surat');
$isInventarisActive = ($seg1 === 'inventaris');
$isDokumenActive    = in_array($seg1, ['dokumen', 'dokumen-legal']);
$isFasilitasActive  = ($seg1 === 'fasilitas');
$isPengajuanActive  = in_array($seg1, ['pengajuan', 'form-pengajuan', 'keluhan-saya']);
$isLaporanActive    = in_array($seg1, ['laporan', 'laporan-harian-saya', 'slip-gaji']);
$isPribadiActive    = in_array($seg1, ['absensi-saya', 'tugas-saya', 'profil']);

function adminSidebarLink($href, $icon, $label, $isActive) {
    $style = $isActive
        ? 'background:rgba(255,255,255,0.18);border-left-color:#ce93d8;color:white;'
        : '';
    return '
        <a href="'.$href.'" style="color:rgba(255,255,255,0.85);padding:10px 20px;display:flex;align-items:center;text-decoration:none;transition:all 0.3s;border-left:3px solid transparent;'.$style.'"
           onmouseover="this.style.background=\'rgba(255,255,255,0.1)\'" onmouseout="this.style.background=\''.($isActive?'rgba(255,255,255,0.18)':'').'\'">
            <i class="'.$icon.'" style="width:24px;text-align:center;margin-right:8px;"></i>
            <span style="font-size:0.875rem;">'.$label.'</span>
        </a>';
}

function adminSubLink($href, $icon, $label, $isActive) {
    $fw = $isActive ? 'font-weight:600;color:white;' : '';
    return '
        <a href="'.$href.'" style="color:rgba(255,255,255,0.78);padding:8px 10px 8px 48px;font-size:0.82rem;display:flex;align-items:center;text-decoration:none;transition:all 0.25s;'.$fw.'">
            <i class="'.$icon.'" style="width:18px;margin-right:7px;"></i>'.$label.'
        </a>';
}
?>

<!-- Admin Panel Sidebar -->
<div class="sidebar" style="
    width: var(--sidebar-width, 265px);
    background: linear-gradient(180deg, #4a148c 0%, #7b1fa2 60%, #9c27b0 100%);
    color: white;
    height: 100vh;
    position: fixed;
    left: 0; top: 0;
    z-index: 1000;
    box-shadow: 3px 0 15px rgba(74,20,140,0.4);
    overflow-y: auto;
    transition: all 0.3s;
">
    <!-- Header -->
    <div style="padding: 18px 20px; text-align:center; border-bottom: 1px solid rgba(255,255,255,0.15); background: rgba(0,0,0,0.15);">
        <div style="background: rgba(255,255,255,0.15); border-radius: 50%; width:52px; height:52px; margin: 0 auto 10px; display:flex; align-items:center; justify-content:center;">
            <i class="fas fa-user-shield" style="font-size:1.4rem;"></i>
        </div>
        <h5 style="margin:0; font-weight:700; font-size:1rem; letter-spacing:0.5px;">ADMIN PANEL</h5>
        <p style="opacity:0.75; font-size:0.72rem; margin:4px 0 0; font-weight:500; text-transform:uppercase; letter-spacing:1px;">CDW Engineering</p>
        <div style="margin-top:8px; background:rgba(255,255,255,0.12); border-radius:20px; padding:3px 10px; display:inline-block;">
            <small style="font-size:0.7rem; opacity:0.9;">
                <i class="fas fa-user me-1"></i><?= htmlspecialchars(session()->get('name') ?? 'Admin') ?>
            </small>
        </div>
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

        <!-- Surat Menyurat -->
        <div>
            <a data-bs-toggle="collapse" href="#suratMenu"
               style="color:rgba(255,255,255,0.85);padding:10px 20px;display:flex;align-items:center;justify-content:space-between;text-decoration:none;transition:all 0.3s;border-left:3px solid <?= $isSuratActive ? '#ce93d8' : 'transparent' ?>;<?= $isSuratActive ? 'background:rgba(255,255,255,0.15);' : '' ?>">
                <div style="display:flex;align-items:center;">
                    <i class="fas fa-envelope" style="width:24px;text-align:center;margin-right:8px;"></i>
                    <span style="font-size:0.875rem;">Surat Menyurat</span>
                </div>
                <i class="fas fa-chevron-down" style="font-size:0.75rem;transition:transform 0.3s;<?= $isSuratActive ? 'transform:rotate(180deg);' : '' ?>"></i>
            </a>
            <div class="collapse <?= $isSuratActive ? 'show' : '' ?>" id="suratMenu" style="background:rgba(0,0,0,0.15);">
                <?= adminSubLink(base_url('admin/surat/masuk'),  'fas fa-inbox',    'Surat Masuk',     $seg2==='masuk') ?>
                <?= adminSubLink(base_url('admin/surat/keluar'), 'fas fa-paper-plane', 'Surat Keluar', $seg2==='keluar') ?>
                <?= adminSubLink(base_url('admin/surat/template'), 'fas fa-file-alt', 'Template Surat', $seg2==='template') ?>
            </div>
        </div>

        <!-- ATK & Inventaris -->
        <div>
            <a data-bs-toggle="collapse" href="#inventarisMenu"
               style="color:rgba(255,255,255,0.85);padding:10px 20px;display:flex;align-items:center;justify-content:space-between;text-decoration:none;transition:all 0.3s;border-left:3px solid <?= $isInventarisActive ? '#ce93d8' : 'transparent' ?>;<?= $isInventarisActive ? 'background:rgba(255,255,255,0.15);' : '' ?>">
                <div style="display:flex;align-items:center;">
                    <i class="fas fa-boxes" style="width:24px;text-align:center;margin-right:8px;"></i>
                    <span style="font-size:0.875rem;">ATK & Inventaris</span>
                </div>
                <i class="fas fa-chevron-down" style="font-size:0.75rem;transition:transform 0.3s;<?= $isInventarisActive ? 'transform:rotate(180deg);' : '' ?>"></i>
            </a>
            <div class="collapse <?= $isInventarisActive ? 'show' : '' ?>" id="inventarisMenu" style="background:rgba(0,0,0,0.15);">
                <?= adminSubLink(base_url('admin/inventaris/stok-atk'),       'fas fa-pen',         'Stok ATK',           $seg2==='stok-atk') ?>
                <?= adminSubLink(base_url('admin/inventaris/pengajuan-atk'),   'fas fa-shopping-cart','Pengajuan Pembelian ATK', $seg2==='pengajuan-atk') ?>
                <?= adminSubLink(base_url('admin/inventaris/inventaris-kantor'),'fas fa-building',   'Inventaris Kantor',  $seg2==='inventaris-kantor') ?>
            </div>
        </div>

        <!-- Dokumen -->
        <div>
            <a data-bs-toggle="collapse" href="#dokumenMenu"
               style="color:rgba(255,255,255,0.85);padding:10px 20px;display:flex;align-items:center;justify-content:space-between;text-decoration:none;transition:all 0.3s;border-left:3px solid <?= $isDokumenActive ? '#ce93d8' : 'transparent' ?>;<?= $isDokumenActive ? 'background:rgba(255,255,255,0.15);' : '' ?>">
                <div style="display:flex;align-items:center;">
                    <i class="fas fa-folder-open" style="width:24px;text-align:center;margin-right:8px;"></i>
                    <span style="font-size:0.875rem;">Dokumen</span>
                </div>
                <i class="fas fa-chevron-down" style="font-size:0.75rem;transition:transform 0.3s;<?= $isDokumenActive ? 'transform:rotate(180deg);' : '' ?>"></i>
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
               style="color:rgba(255,255,255,0.85);padding:10px 20px;display:flex;align-items:center;justify-content:space-between;text-decoration:none;transition:all 0.3s;border-left:3px solid <?= $isFasilitasActive ? '#ce93d8' : 'transparent' ?>;<?= $isFasilitasActive ? 'background:rgba(255,255,255,0.15);' : '' ?>">
                <div style="display:flex;align-items:center;">
                    <i class="fas fa-concierge-bell" style="width:24px;text-align:center;margin-right:8px;"></i>
                    <span style="font-size:0.875rem;">Fasilitas & Tamu</span>
                </div>
                <i class="fas fa-chevron-down" style="font-size:0.75rem;transition:transform 0.3s;<?= $isFasilitasActive ? 'transform:rotate(180deg);' : '' ?>"></i>
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
               style="color:rgba(255,255,255,0.85);padding:10px 20px;display:flex;align-items:center;justify-content:space-between;text-decoration:none;transition:all 0.3s;border-left:3px solid <?= $isPengajuanActive ? '#ce93d8' : 'transparent' ?>;<?= $isPengajuanActive ? 'background:rgba(255,255,255,0.15);' : '' ?>">
                <div style="display:flex;align-items:center;">
                    <i class="fas fa-clipboard-list" style="width:24px;text-align:center;margin-right:8px;"></i>
                    <span style="font-size:0.875rem;">Pengajuan</span>
                </div>
                <i class="fas fa-chevron-down" style="font-size:0.75rem;transition:transform 0.3s;<?= $isPengajuanActive ? 'transform:rotate(180deg);' : '' ?>"></i>
            </a>
            <div class="collapse <?= $isPengajuanActive ? 'show' : '' ?>" id="pengajuanMenu" style="background:rgba(0,0,0,0.15);">
                <?= adminSubLink(base_url('admin/pengajuan/semua'),    'fas fa-list-alt',      'Semua Pengajuan', $seg1==='pengajuan' && ($seg2==='' || $seg2==='semua')) ?>
                <?= adminSubLink(base_url('admin/form-pengajuan/cuti'), 'fas fa-paper-plane',  'Form Pengajuan',  $seg1==='form-pengajuan') ?>
                <?= adminSubLink(base_url('admin/pengajuan/cuti'),     'fas fa-umbrella-beach','Cuti',            $seg1==='pengajuan' && $seg2==='cuti') ?>
                <?= adminSubLink(base_url('admin/keluhan-saya'),        'fas fa-comment-alt',   'Keluhan Saya',    $seg1==='keluhan-saya') ?>
            </div>
        </div>

        <!-- Laporan & Keluhan -->
        <div>
            <a data-bs-toggle="collapse" href="#laporanMenu"
               style="color:rgba(255,255,255,0.85);padding:10px 20px;display:flex;align-items:center;justify-content:space-between;text-decoration:none;transition:all 0.3s;border-left:3px solid <?= $isLaporanActive ? '#ce93d8' : 'transparent' ?>;<?= $isLaporanActive ? 'background:rgba(255,255,255,0.15);' : '' ?>">
                <div style="display:flex;align-items:center;">
                    <i class="fas fa-chart-bar" style="width:24px;text-align:center;margin-right:8px;"></i>
                    <span style="font-size:0.875rem;">Laporan & Keluhan</span>
                </div>
                <i class="fas fa-chevron-down" style="font-size:0.75rem;transition:transform 0.3s;<?= $isLaporanActive ? 'transform:rotate(180deg);' : '' ?>"></i>
            </a>
            <div class="collapse <?= $isLaporanActive ? 'show' : '' ?>" id="laporanMenu" style="background:rgba(0,0,0,0.15);">
                <?= adminSubLink(base_url('admin/laporan/dashboard'),    'fas fa-tachometer-alt','Dashboard Laporan',    $seg1==='laporan' && $seg2==='dashboard') ?>
                <?= adminSubLink(base_url('admin/laporan/kerja-harian'),  'fas fa-tasks',          'Laporan Kerja Harian',$seg1==='laporan' && ($seg2==='kerja-harian' || $seg1==='laporan-harian-saya')) ?>
                <?= adminSubLink(base_url('admin/laporan/keluhan'),       'fas fa-comment-dots',   'Keluhan',              $seg1==='laporan' && $seg2==='keluhan') ?>
                <?= adminSubLink(base_url('admin/slip-gaji'),             'fas fa-money-bill-wave','Slip Gaji',           $seg1==='slip-gaji') ?>
            </div>
        </div>

        <div style="padding: 4px 16px 2px; font-size:0.65rem; color:rgba(255,255,255,0.45); font-weight:600; letter-spacing:1.5px; text-transform:uppercase; margin-top:10px;">
            MENU PRIBADI
        </div>

        <!-- Menu Pribadi -->
        <div>
            <a data-bs-toggle="collapse" href="#pribadiMenu"
               style="color:rgba(255,255,255,0.85);padding:10px 20px;display:flex;align-items:center;justify-content:space-between;text-decoration:none;transition:all 0.3s;border-left:3px solid <?= $isPribadiActive ? '#ce93d8' : 'transparent' ?>;<?= $isPribadiActive ? 'background:rgba(255,255,255,0.15);' : '' ?>">
                <div style="display:flex;align-items:center;">
                    <i class="fas fa-user-circle" style="width:24px;text-align:center;margin-right:8px;"></i>
                    <span style="font-size:0.875rem;">Menu Pribadi</span>
                </div>
                <i class="fas fa-chevron-down" style="font-size:0.75rem;transition:transform 0.3s;<?= $isPribadiActive ? 'transform:rotate(180deg);' : '' ?>"></i>
            </a>
            <div class="collapse <?= $isPribadiActive ? 'show' : '' ?>" id="pribadiMenu" style="background:rgba(0,0,0,0.15);">
                <?= adminSubLink(base_url('admin/absensi-saya'), 'fas fa-fingerprint', 'Absensi Saya', $seg1==='absensi-saya') ?>
                <?= adminSubLink(base_url('admin/tugas-saya'),   'fas fa-tasks',       'Tugas Saya',   $seg1==='tugas-saya') ?>
                <?= adminSubLink(base_url('admin/profil'),       'fas fa-id-badge',    'Profil',       $seg1==='profil') ?>
            </div>
        </div>

        <!-- Keluar -->
        <div style="margin-top: 16px; border-top: 1px solid rgba(255,255,255,0.12); padding-top: 12px;">
            <a href="<?= base_url('logout') ?>"
               style="color:#f48fb1;padding:11px 20px;display:flex;align-items:center;text-decoration:none;transition:all 0.3s;font-weight:600;">
                <i class="fas fa-sign-out-alt" style="width:24px;text-align:center;margin-right:8px;"></i>
                <span style="font-size:0.875rem;">Keluar</span>
            </a>
        </div>

    </nav>
</div>
