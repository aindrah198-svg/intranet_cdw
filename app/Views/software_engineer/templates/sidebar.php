<?php
$active = $active ?? 'dashboard';
$sub    = $sub ?? '';
$uri    = service('uri');
$segments = $uri->getSegments();
$segment2 = $segments[1] ?? '';
$segment3 = $segments[2] ?? '';

// Active states
$isDashActive        = $active === 'dashboard' || $segment2 === 'dashboard' || $segment2 === '';
$isDevActive         = $active === 'development' || $segment2 === 'development';
$isMgmtActive        = $active === 'manajemen-sistem' || $segment2 === 'manajemen-sistem';
$isBugMaintActive    = $active === 'bug-maintenance' || $segment2 === 'bug-maintenance';
$isDocActive         = $active === 'dokumentasi-teknis' || $segment2 === 'dokumentasi-teknis';
$isPengajuanActive   = $active === 'pengajuan' || $segment2 === 'pengajuan';
$isLaporanActive     = $active === 'laporan-keluhan' || $segment2 === 'laporan-keluhan';
$isPribadiActive     = $active === 'pribadi' || $segment2 === 'pribadi';
?>

<!-- Sidebar Software Engineer -->
<nav class="sidebar text-white" id="sidebar" style="width: 260px; background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%); min-height: 100vh; position: fixed; top: 0; left: 0; z-index: 1050; overflow-y: auto; box-shadow: 4px 0 15px rgba(0,0,0,0.3);">
    <div class="sidebar-header p-3 border-bottom border-secondary border-opacity-25 text-center">
        <h5 class="mb-0 fw-bold text-cyan tracking-wide" style="color: #38bdf8;"><i class="fas fa-laptop-code text-info me-2"></i>SOFTWARE ENGINEER</h5>
        <small class="text-white-50" style="font-size: 0.8rem;">SE Panel & System Ops</small>
    </div>

    <div class="sidebar-user p-3 border-bottom border-secondary border-opacity-25 d-flex align-items-center bg-black bg-opacity-25">
        <div class="rounded-circle bg-info text-dark font-weight-bold d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 42px; height: 42px; font-weight: 700; font-size: 1.1rem; background-color: #38bdf8 !important;">
            <?= strtoupper(substr(session()->get('username') ?? 'S', 0, 1)) ?>
        </div>
        <div class="overflow-hidden">
            <div class="fw-bold text-white mb-0 text-truncate" style="font-size: 0.95rem;"><?= esc(session()->get('name') ?? session()->get('username') ?? 'Software Engineer') ?></div>
            <span class="badge bg-cyan text-dark fw-semibold" style="background-color: #38bdf8; font-size: 0.7rem;">Dev & Maintainer</span>
        </div>
    </div>

    <div class="sidebar-menu p-2">
        <ul class="nav flex-column gap-1">
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link text-white py-2 px-3 rounded <?= $isDashActive ? 'bg-primary fw-bold active shadow-sm' : 'hover-glow' ?>" href="<?= site_url('software-engineer/dashboard') ?>" style="<?= $isDashActive ? 'background-color: #0284c7 !important;' : '' ?>">
                    <i class="fas fa-tachometer-alt me-2 text-cyan" style="color: #38bdf8;"></i> Dashboard
                </a>
            </li>

            <!-- Development -->
            <li class="nav-item">
                <a class="nav-link text-white py-2 px-3 rounded d-flex justify-content-between align-items-center <?= $isDevActive ? 'bg-primary fw-bold' : '' ?>" 
                   data-bs-toggle="collapse" href="#menuDev" role="button" aria-expanded="<?= $isDevActive ? 'true' : 'false' ?>" style="<?= $isDevActive ? 'background-color: #0284c7 !important;' : '' ?>">
                    <span><i class="fas fa-code-branch me-2 text-warning"></i> Development</span>
                    <i class="fas fa-chevron-down small opacity-75"></i>
                </a>
                <div class="collapse <?= $isDevActive ? 'show' : '' ?>" id="menuDev">
                    <ul class="nav flex-column ps-3 py-1 gap-1">
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $sub === 'task-board' ? 'text-white fw-bold bg-white bg-opacity-10' : '' ?>" href="<?= site_url('software-engineer/development/task-board') ?>">
                                <i class="fas fa-tasks me-2"></i> Task Board
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $sub === 'sprint' ? 'text-white fw-bold bg-white bg-opacity-10' : '' ?>" href="<?= site_url('software-engineer/development/sprint') ?>">
                                <i class="fas fa-running me-2"></i> Timeline / Sprint
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $sub === 'info-client' ? 'text-white fw-bold bg-white bg-opacity-10' : '' ?>" href="<?= site_url('software-engineer/development/info-client') ?>">
                                <i class="fas fa-building me-2"></i> Info Client/Project
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Manajemen Sistem -->
            <li class="nav-item">
                <a class="nav-link text-white py-2 px-3 rounded d-flex justify-content-between align-items-center <?= $isMgmtActive ? 'bg-primary fw-bold' : '' ?>" 
                   data-bs-toggle="collapse" href="#menuMgmt" role="button" aria-expanded="<?= $isMgmtActive ? 'true' : 'false' ?>" style="<?= $isMgmtActive ? 'background-color: #0284c7 !important;' : '' ?>">
                    <span><i class="fas fa-server me-2 text-info" style="color: #38bdf8;"></i> Manajemen Sistem</span>
                    <i class="fas fa-chevron-down small opacity-75"></i>
                </a>
                <div class="collapse <?= $isMgmtActive ? 'show' : '' ?>" id="menuMgmt">
                    <ul class="nav flex-column ps-3 py-1 gap-1">
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $sub === 'daftar-sistem' ? 'text-white fw-bold bg-white bg-opacity-10' : '' ?>" href="<?= site_url('software-engineer/manajemen-sistem/daftar-sistem') ?>">
                                <i class="fas fa-globe me-2"></i> Daftar Sistem/Website
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $sub === 'hosting-domain' ? 'text-white fw-bold bg-white bg-opacity-10' : '' ?>" href="<?= site_url('software-engineer/manajemen-sistem/hosting-domain') ?>">
                                <i class="fas fa-hdd me-2"></i> Hosting & Domain
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $sub === 'kredensial-akses' ? 'text-white fw-bold bg-white bg-opacity-10' : '' ?>" href="<?= site_url('software-engineer/manajemen-sistem/kredensial-akses') ?>">
                                <i class="fas fa-key me-2 text-warning"></i> Kredensial Akses ⚠️
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $sub === 'riwayat-deploy' ? 'text-white fw-bold bg-white bg-opacity-10' : '' ?>" href="<?= site_url('software-engineer/manajemen-sistem/riwayat-deploy') ?>">
                                <i class="fas fa-history me-2"></i> Riwayat Deploy
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Bug & Maintenance -->
            <li class="nav-item">
                <a class="nav-link text-white py-2 px-3 rounded d-flex justify-content-between align-items-center <?= $isBugMaintActive ? 'bg-primary fw-bold' : '' ?>" 
                   data-bs-toggle="collapse" href="#menuBugMaint" role="button" aria-expanded="<?= $isBugMaintActive ? 'true' : 'false' ?>" style="<?= $isBugMaintActive ? 'background-color: #0284c7 !important;' : '' ?>">
                    <span><i class="fas fa-bug me-2 text-danger"></i> Bug & Maintenance</span>
                    <i class="fas fa-chevron-down small opacity-75"></i>
                </a>
                <div class="collapse <?= $isBugMaintActive ? 'show' : '' ?>" id="menuBugMaint">
                    <ul class="nav flex-column ps-3 py-1 gap-1">
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $sub === 'bug-tracking' ? 'text-white fw-bold bg-white bg-opacity-10' : '' ?>" href="<?= site_url('software-engineer/bug-maintenance/bug-tracking') ?>">
                                <i class="fas fa-spider me-2"></i> Bug Tracking
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $sub === 'maintenance-terjadwal' ? 'text-white fw-bold bg-white bg-opacity-10' : '' ?>" href="<?= site_url('software-engineer/bug-maintenance/maintenance-terjadwal') ?>">
                                <i class="fas fa-tools me-2"></i> Maintenance Terjadwal
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $sub === 'backup-log' ? 'text-white fw-bold bg-white bg-opacity-10' : '' ?>" href="<?= site_url('software-engineer/bug-maintenance/backup-log') ?>">
                                <i class="fas fa-database me-2"></i> Backup Log
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Dokumentasi Teknis -->
            <li class="nav-item">
                <a class="nav-link text-white py-2 px-3 rounded d-flex justify-content-between align-items-center <?= $isDocActive ? 'bg-primary fw-bold' : '' ?>" 
                   data-bs-toggle="collapse" href="#menuDoc" role="button" aria-expanded="<?= $isDocActive ? 'true' : 'false' ?>" style="<?= $isDocActive ? 'background-color: #0284c7 !important;' : '' ?>">
                    <span><i class="fas fa-book me-2 text-success"></i> Dokumentasi Teknis</span>
                    <i class="fas fa-chevron-down small opacity-75"></i>
                </a>
                <div class="collapse <?= $isDocActive ? 'show' : '' ?>" id="menuDoc">
                    <ul class="nav flex-column ps-3 py-1 gap-1">
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $sub === 'dokumentasi-sistem' ? 'text-white fw-bold bg-white bg-opacity-10' : '' ?>" href="<?= site_url('software-engineer/dokumentasi-teknis/dokumentasi-sistem') ?>">
                                <i class="fas fa-file-alt me-2"></i> Dokumentasi per Sistem
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $sub === 'arsitektur-sistem' ? 'text-white fw-bold bg-white bg-opacity-10' : '' ?>" href="<?= site_url('software-engineer/dokumentasi-teknis/arsitektur-sistem') ?>">
                                <i class="fas fa-project-diagram me-2"></i> Arsitektur Sistem
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Pengajuan -->
            <li class="nav-item">
                <a class="nav-link text-white py-2 px-3 rounded d-flex justify-content-between align-items-center <?= $isPengajuanActive ? 'bg-primary fw-bold' : '' ?>" 
                   data-bs-toggle="collapse" href="#menuPengajuan" role="button" aria-expanded="<?= $isPengajuanActive ? 'true' : 'false' ?>" style="<?= $isPengajuanActive ? 'background-color: #0284c7 !important;' : '' ?>">
                    <span><i class="fas fa-paper-plane me-2 text-cyan" style="color: #38bdf8;"></i> Pengajuan</span>
                    <i class="fas fa-chevron-down small opacity-75"></i>
                </a>
                <div class="collapse <?= $isPengajuanActive ? 'show' : '' ?>" id="menuPengajuan">
                    <ul class="nav flex-column ps-3 py-1 gap-1">
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $sub === 'semua' ? 'text-white fw-bold bg-white bg-opacity-10' : '' ?>" href="<?= site_url('software-engineer/pengajuan') ?>">
                                <i class="fas fa-list me-2"></i> Semua Pengajuan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $sub === 'permintaan-alat' ? 'text-white fw-bold bg-white bg-opacity-10' : '' ?>" href="<?= site_url('software-engineer/pengajuan/permintaan-alat') ?>">
                                <i class="fas fa-laptop me-2"></i> Permintaan Alat/Software
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $sub === 'cuti' ? 'text-white fw-bold bg-white bg-opacity-10' : '' ?>" href="<?= site_url('software-engineer/pengajuan/cuti') ?>">
                                <i class="fas fa-umbrella-beach me-2"></i> Cuti
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Laporan & Keluhan -->
            <li class="nav-item">
                <a class="nav-link text-white py-2 px-3 rounded d-flex justify-content-between align-items-center <?= $isLaporanActive ? 'bg-primary fw-bold' : '' ?>" 
                   data-bs-toggle="collapse" href="#menuLaporan" role="button" aria-expanded="<?= $isLaporanActive ? 'true' : 'false' ?>" style="<?= $isLaporanActive ? 'background-color: #0284c7 !important;' : '' ?>">
                    <span><i class="fas fa-chart-bar me-2 text-warning"></i> Laporan & Keluhan</span>
                    <i class="fas fa-chevron-down small opacity-75"></i>
                </a>
                <div class="collapse <?= $isLaporanActive ? 'show' : '' ?>" id="menuLaporan">
                    <ul class="nav flex-column ps-3 py-1 gap-1">
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $sub === 'dashboard' ? 'text-white fw-bold bg-white bg-opacity-10' : '' ?>" href="<?= site_url('software-engineer/laporan-keluhan/dashboard') ?>">
                                <i class="fas fa-chart-line me-2"></i> Dashboard Laporan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $sub === 'laporan-harian' ? 'text-white fw-bold bg-white bg-opacity-10' : '' ?>" href="<?= site_url('software-engineer/laporan-keluhan/laporan-harian') ?>">
                                <i class="fas fa-calendar-check me-2"></i> Laporan Progress Harian
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $sub === 'keluhan' ? 'text-white fw-bold bg-white bg-opacity-10' : '' ?>" href="<?= site_url('software-engineer/laporan-keluhan/keluhan') ?>">
                                <i class="fas fa-exclamation-circle me-2"></i> Keluhan
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Menu Pribadi -->
            <li class="nav-item">
                <a class="nav-link text-white py-2 px-3 rounded d-flex justify-content-between align-items-center <?= $isPribadiActive ? 'bg-primary fw-bold' : '' ?>" 
                   data-bs-toggle="collapse" href="#menuPribadi" role="button" aria-expanded="<?= $isPribadiActive ? 'true' : 'false' ?>" style="<?= $isPribadiActive ? 'background-color: #0284c7 !important;' : '' ?>">
                    <span><i class="fas fa-user-shield me-2 text-info" style="color: #38bdf8;"></i> Menu Pribadi</span>
                    <i class="fas fa-chevron-down small opacity-75"></i>
                </a>
                <div class="collapse <?= $isPribadiActive ? 'show' : '' ?>" id="menuPribadi">
                    <ul class="nav flex-column ps-3 py-1 gap-1">
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $sub === 'absensi' ? 'text-white fw-bold bg-white bg-opacity-10' : '' ?>" href="<?= site_url('software-engineer/pribadi/absensi') ?>">
                                <i class="fas fa-clock me-2"></i> Absensi Saya
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $sub === 'tugas' ? 'text-white fw-bold bg-white bg-opacity-10' : '' ?>" href="<?= site_url('software-engineer/pribadi/tugas') ?>">
                                <i class="fas fa-tasks me-2"></i> Tugas Saya
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $sub === 'laporan-harian' ? 'text-white fw-bold bg-white bg-opacity-10' : '' ?>" href="<?= site_url('software-engineer/pribadi/laporan-harian') ?>">
                                <i class="fas fa-file-invoice me-2"></i> Laporan Kerja Harian
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $sub === 'keluhan' ? 'text-white fw-bold bg-white bg-opacity-10' : '' ?>" href="<?= site_url('software-engineer/pribadi/keluhan') ?>">
                                <i class="fas fa-comment-dots me-2"></i> Keluhan Saya
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $sub === 'pengajuan' ? 'text-white fw-bold bg-white bg-opacity-10' : '' ?>" href="<?= site_url('software-engineer/pribadi/pengajuan') ?>">
                                <i class="fas fa-paper-plane me-2"></i> Form Pengajuan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $sub === 'slip-gaji' ? 'text-white fw-bold bg-white bg-opacity-10' : '' ?>" href="<?= site_url('software-engineer/pribadi/slip-gaji') ?>">
                                <i class="fas fa-money-check-alt me-2"></i> Slip Gaji
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $sub === 'profil' ? 'text-white fw-bold bg-white bg-opacity-10' : '' ?>" href="<?= site_url('software-engineer/pribadi/profil') ?>">
                                <i class="fas fa-user-cog me-2"></i> Profil
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Keluar -->
            <li class="nav-item mt-3 pt-2 border-top border-secondary border-opacity-25">
                <a class="nav-link text-danger py-2 px-3 rounded fw-semibold" href="<?= site_url('logout') ?>">
                    <i class="fas fa-sign-out-alt me-2"></i> Keluar
                </a>
            </li>
        </ul>
    </div>
</nav>
