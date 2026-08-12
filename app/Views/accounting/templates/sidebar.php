<?php
// app/Views/accounting/templates/sidebar.php

$active = $active ?? 'dashboard';
$uri = service('uri');
$segments = $uri->getSegments();

$segment1 = isset($segments[1]) ? $segments[1] : '';
$segment2 = isset($segments[2]) ? $segments[2] : '';
$segment3 = isset($segments[3]) ? $segments[3] : '';

$kasBankMenuItems = ['mutasi-bank', 'transfer-internal', 'rekonsiliasi', 'kas-kecil', 'pengeluaran-pribadi'];
$isKasBankActive = (in_array($active, $kasBankMenuItems) || in_array($segment2, $kasBankMenuItems) || $segment1 == 'kas-bank');

$pembukuanMenuItems = ['daftar-akun', 'jurnal-umum', 'buku-besar', 'jurnal-posted'];
$isPembukuanActive = (in_array($active, $pembukuanMenuItems) || in_array($segment2, $pembukuanMenuItems) || $segment1 == 'pembukuan');

$penggajianMenuItems = ['review-payroll', 'perhitungan-gaji', 'proses-pembayaran', 'slip-gaji'];
$isPenggajianActive = (in_array($active, $penggajianMenuItems) || in_array($segment2, $penggajianMenuItems) || $segment1 == 'penggajian');

$kasbonMenuItems = ['kasbon', 'kasbon-potong', 'potong-gaji'];
$isKasbonActive = (in_array($active, $kasbonMenuItems) || in_array($segment2, $kasbonMenuItems) || $segment1 == 'kasbon');

$asetTetapMenuItems = ['register-aset', 'penyusutan', 'pelepasan-aset'];
$isAsetTetapActive = (in_array($active, $asetTetapMenuItems) || in_array($segment2, $asetTetapMenuItems) || $segment1 == 'aset-tetap');

$pajakMenuItems = ['ppn', 'pph-badan', 'arsip-pajak'];
$isPajakActive = (in_array($active, $pajakMenuItems) || in_array($segment2, $pajakMenuItems) || $segment1 == 'manajemen-pajak');

$piutangMenuItems = ['invoice', 'jatuh-tempo'];
$isPiutangActive = (in_array($active, $piutangMenuItems) || in_array($segment2, $piutangMenuItems) || $segment1 == 'piutang');

$laporanMenuItems = ['laba-rugi', 'neraca', 'arus-kas', 'modal-pemilik'];
$isLaporanActive = (in_array($active, $laporanMenuItems) || in_array($segment2, $laporanMenuItems) || in_array($segment3, $laporanMenuItems) || $segment1 == 'laporan-keuangan');

$pribadiMenuItems = ['absensi-saya', 'tugas-saya', 'timeline-kerja', 'project-saat-ini', 'profil'];
$isPribadiActive = (in_array($active, $pribadiMenuItems) || in_array($segment2, $pribadiMenuItems) || in_array($segment2, ['absensi','profil','riwayat-audit']) || $segment1 == 'pribadi');

// Notification: Tugas Saya
$db = \Config\Database::connect();
$notifTugasSaya = 0;
if ($db->tableExists('penugasan_harian')) {
    $sessUserId = session()->get('user_id') ?? session()->get('karyawan_id');
    $q = $db->table('penugasan_harian')
        ->where('deleted_at', null)
        ->whereIn('status', ['pending', 'baru', 'proses'])
        ->groupStart()
            ->where('penerima_role', 'accounting')
            ->orWhere('penerima_role', 'all');
    if ($sessUserId) { $q->orWhere('penerima_id', $sessUserId); }
    $q->groupEnd();
    $notifTugasSaya = $q->countAllResults();
}
$notifPribadiTotal = $notifTugasSaya;

function accSubLink($href, $icon, $label, $isActive, $badge = 0) {
    $fw = $isActive ? 'font-weight:600;color:white;' : '';
    $bdg = $badge > 0 ? '<span class="badge bg-danger rounded-pill ms-auto px-2" style="font-size:0.7rem;font-weight:700;">'.$badge.'</span>' : '';
    return '
        <a href="'.$href.'" style="color:rgba(255,255,255,0.78);padding:8px 10px 8px 48px;font-size:0.82rem;display:flex;align-items:center;text-decoration:none;transition:all 0.25s;'.$fw.'">
            <i class="'.$icon.'" style="width:18px;margin-right:7px;"></i><span style="flex-grow:1;">'.$label.'</span>'.$bdg.'
        </a>';
}
?>
<!-- Sidebar -->
<div class="sidebar" style="
    width: var(--sidebar-width, 250px);
    background: linear-gradient(180deg, #1e3c72 0%, #2a5298 100%);
    color: white;
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    transition: all 0.3s;
    z-index: 1000;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
">
    <div class="sidebar-header" style="
        padding: 20px; 
        text-align: center; 
        border-bottom: 1px solid rgba(255,255,255,0.1);
    ">
        <h4 style="margin: 0; font-weight: 600; font-size: 1.2rem;">
            <i class="fas fa-calculator me-2"></i>KEUANGAN & AKUNTANSI
        </h4>
        <p style="opacity: 0.8; font-size: 0.85rem; margin: 5px 0 0;">
            <i class="fas fa-user-shield me-1"></i>Accounting Panel
        </p>
    </div>
    
    <div class="sidebar-menu" style="padding: 15px 0; height: calc(100vh - 110px); overflow-y: auto;">
        <ul class="nav flex-column" style="list-style: none; padding: 0; margin: 0;">
            
            <li class="nav-item mb-1">
                <div style="padding: 8px 20px; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.6;">
                    <i class="fas fa-bars me-1"></i> MENU UTAMA
                </div>
            </li>
            
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link <?= $active == 'dashboard' ? 'active' : '' ?>" 
                   href="<?= site_url('accounting/dashboard') ?>"
                   style="color: rgba(255,255,255,0.8); padding: 10px 20px; text-decoration: none; display: flex; align-items: center; <?= $active == 'dashboard' ? 'background: rgba(255,255,255,0.15); border-left: 3px solid #4dabf7; color: white;' : '' ?>">
                    <i class="fas fa-tachometer-alt me-2" style="width: 20px;"></i> 
                    <span>Dashboard</span>
                </a>
            </li>
            
            <!-- Kas & Bank -->
            <li class="nav-item">
                <a class="nav-link <?= $isKasBankActive ? 'active' : '' ?>" 
                   data-bs-toggle="collapse" href="#kasBankMenu" role="button" aria-expanded="<?= $isKasBankActive ? 'true' : 'false' ?>"
                   style="color: rgba(255,255,255,0.8); padding: 10px 20px; text-decoration: none; display: flex; align-items: center; justify-content: space-between; cursor: pointer; <?= $isKasBankActive ? 'background: rgba(255,255,255,0.1); border-left: 3px solid #4dabf7;' : '' ?>">
                    <div><i class="fas fa-money-bill-wave me-2" style="width: 20px;"></i><span>Kas & Bank</span></div>
                    <i class="fas fa-chevron-down" style="font-size: 0.75rem; <?= $isKasBankActive ? 'transform: rotate(180deg);' : '' ?>"></i>
                </a>
                <div class="collapse <?= $isKasBankActive ? 'show' : '' ?>" id="kasBankMenu" style="background: rgba(0,0,0,0.15);">
                    <ul class="nav flex-column" style="padding: 2px 0; list-style: none;">
                        <li><a class="nav-link py-2 ps-4 text-white-50" href="<?= site_url('accounting/kas-bank/mutasi-bank') ?>"><i class="fas fa-exchange-alt me-2"></i>Mutasi Bank</a></li>
                        <li><a class="nav-link py-2 ps-4 text-white-50" href="<?= site_url('accounting/kas-bank/transfer-internal') ?>"><i class="fas fa-random me-2"></i>Transfer Internal</a></li>
                        <li><a class="nav-link py-2 ps-4 text-white-50" href="<?= site_url('accounting/kas-bank/rekonsiliasi') ?>"><i class="fas fa-balance-scale me-2"></i>Rekonsiliasi</a></li>
                        <li><a class="nav-link py-2 ps-4 text-white-50" href="<?= site_url('accounting/kas-bank/kas-kecil') ?>"><i class="fas fa-wallet me-2"></i>Kas Kecil</a></li>
                        <li><a class="nav-link py-2 ps-4 text-white-50" href="<?= site_url('accounting/kas-bank/pengeluaran-pribadi') ?>"><i class="fas fa-hand-holding-usd me-2"></i>Pengeluaran Pribadi</a></li>
                    </ul>
                </div>
            </li>
            
            <!-- Pembukuan -->
            <li class="nav-item">
                <a class="nav-link <?= $isPembukuanActive ? 'active' : '' ?>" 
                   data-bs-toggle="collapse" href="#pembukuanMenu" role="button" aria-expanded="<?= $isPembukuanActive ? 'true' : 'false' ?>"
                   style="color: rgba(255,255,255,0.8); padding: 10px 20px; text-decoration: none; display: flex; align-items: center; justify-content: space-between; cursor: pointer; <?= $isPembukuanActive ? 'background: rgba(255,255,255,0.1); border-left: 3px solid #4dabf7;' : '' ?>">
                    <div><i class="fas fa-book me-2" style="width: 20px;"></i><span>Pembukuan</span></div>
                    <i class="fas fa-chevron-down" style="font-size: 0.75rem; <?= $isPembukuanActive ? 'transform: rotate(180deg);' : '' ?>"></i>
                </a>
                <div class="collapse <?= $isPembukuanActive ? 'show' : '' ?>" id="pembukuanMenu" style="background: rgba(0,0,0,0.15);">
                    <ul class="nav flex-column" style="padding: 2px 0; list-style: none;">
                        <li><a class="nav-link py-2 ps-4 text-white-50" href="<?= site_url('accounting/pembukuan/daftar-akun') ?>"><i class="fas fa-list-alt me-2"></i>Daftar Akun (COA)</a></li>
                        <li><a class="nav-link py-2 ps-4 text-white-50" href="<?= site_url('accounting/pembukuan/jurnal-umum') ?>"><i class="fas fa-file-invoice me-2"></i>Jurnal Umum</a></li>
                        <li><a class="nav-link py-2 ps-4 text-white-50" href="<?= site_url('accounting/pembukuan/buku-besar') ?>"><i class="fas fa-book-open me-2"></i>Buku Besar</a></li>
                        <li><a class="nav-link py-2 ps-4 text-white-50" href="<?= site_url('accounting/pembukuan/buku-besar/jurnal-posted') ?>"><i class="fas fa-check-circle me-2"></i>Jurnal Posted</a></li>
                    </ul>
                </div>
            </li>
            
            <!-- Laporan Keuangan -->
            <li class="nav-item">
                <a class="nav-link <?= $isLaporanActive ? 'active' : '' ?>" 
                   data-bs-toggle="collapse" href="#laporanMenu" role="button" aria-expanded="<?= $isLaporanActive ? 'true' : 'false' ?>"
                   style="color: rgba(255,255,255,0.8); padding: 10px 20px; text-decoration: none; display: flex; align-items: center; justify-content: space-between; cursor: pointer; <?= $isLaporanActive ? 'background: rgba(255,255,255,0.1); border-left: 3px solid #4dabf7;' : '' ?>">
                    <div><i class="fas fa-chart-bar me-2" style="width: 20px;"></i><span>Laporan Keuangan</span></div>
                    <i class="fas fa-chevron-down" style="font-size: 0.75rem; <?= $isLaporanActive ? 'transform: rotate(180deg);' : '' ?>"></i>
                </a>
                <div class="collapse <?= $isLaporanActive ? 'show' : '' ?>" id="laporanMenu" style="background: rgba(0,0,0,0.15);">
                    <ul class="nav flex-column" style="padding: 2px 0; list-style: none;">
                        <li><a class="nav-link py-2 ps-4 text-white-50" href="<?= site_url('accounting/laporan-keuangan/laporan/laba-rugi') ?>"><i class="fas fa-chart-line me-2"></i>Laporan Laba Rugi</a></li>
                        <li><a class="nav-link py-2 ps-4 text-white-50" href="<?= site_url('accounting/laporan-keuangan/laporan/neraca') ?>"><i class="fas fa-balance-scale me-2"></i>Laporan Neraca</a></li>
                        <li><a class="nav-link py-2 ps-4 text-white-50" href="<?= site_url('accounting/laporan-keuangan/laporan/arus-kas') ?>"><i class="fas fa-money-bill-wave me-2"></i>Laporan Arus Kas</a></li>
                        <li><a class="nav-link py-2 ps-4 text-white-50" href="<?= site_url('accounting/laporan-keuangan/laporan/modal-pemilik') ?>"><i class="fas fa-user-tie me-2"></i>Laporan Modal Pemilik</a></li>
                    </ul>
                </div>
            </li>
            
            <li class="nav-item mb-1 mt-3">
                <div style="padding: 4px 16px 2px; font-size:0.65rem; color:rgba(255,255,255,0.45); font-weight:600; letter-spacing:1.5px; text-transform:uppercase; margin-top:10px;">
                    MENU PRIBADI
                </div>
            </li>

            <!-- Menu Pribadi Collapsible -->
            <li class="nav-item">
                <a data-bs-toggle="collapse" href="#pribadiMenu"
                   style="color:rgba(255,255,255,0.85);padding:10px 20px;display:flex;align-items:center;justify-content:space-between;text-decoration:none;border-left:3px solid <?= $isPribadiActive ? '#ce93d8' : 'transparent' ?>;<?= $isPribadiActive ? 'background:rgba(255,255,255,0.15);' : '' ?>">
                    <div style="display:flex;align-items:center;flex-grow:1;margin-right:8px;">
                        <i class="fas fa-user-circle" style="width:24px;text-align:center;margin-right:8px;"></i>
                        <span style="font-size:0.875rem;">Menu Pribadi</span>
                        <?php if ($notifPribadiTotal > 0): ?>
                            <span class="badge bg-danger rounded-pill ms-auto me-2 px-2" style="font-size:0.72rem;font-weight:700;"><?= $notifPribadiTotal ?></span>
                        <?php endif; ?>
                    </div>
                    <i class="fas fa-chevron-down" style="font-size:0.75rem;"></i>
                </a>
                <div class="collapse <?= $isPribadiActive ? 'show' : '' ?>" id="pribadiMenu" style="background:rgba(0,0,0,0.15);">
                    <?= accSubLink(site_url('accounting/pribadi/absensi'),       'fas fa-fingerprint',     'Absensi',         $active==='absensi-saya'  || $segment2==='absensi') ?>
                    <?= accSubLink(site_url('accounting/pribadi/tugas-saya'),    'fas fa-tasks',           'Tugas Hari Ini',  $active==='tugas-saya'    || $segment2==='tugas-saya', $notifTugasSaya) ?>
                    <?= accSubLink(site_url('accounting/pribadi/timeline-kerja'),'fas fa-stream',          'Timeline Kerja',  $active==='timeline-kerja'|| $segment2==='timeline-kerja') ?>
                    <?= accSubLink(site_url('accounting/pribadi/project-saat-ini'),'fas fa-project-diagram','Project Saat Ini',$active==='project-saat-ini'||$segment2==='project-saat-ini') ?>
                    <?= accSubLink(site_url('accounting/pribadi/profil'),        'fas fa-id-badge',        'Profil',          $active==='profil'        || $segment2==='profil') ?>
                </div>
            </li>
            
            <li class="mt-4 px-3 mb-4">
                <a href="<?= site_url('logout') ?>" class="btn btn-outline-light w-100 btn-sm">
                    <i class="fas fa-sign-out-alt me-1"></i> Keluar
                </a>
            </li>
        </ul>
    </div>
</div>