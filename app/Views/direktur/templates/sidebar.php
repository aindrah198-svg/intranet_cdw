<?php
// app/Views/direktur/templates/sidebar.php

$active = $active ?? 'dashboard';
$uri = service('uri');
$segments = $uri->getSegments();

// Ambil segment dengan cara yang aman
$segment1 = isset($segments[1]) ? $segments[1] : '';
$segment2 = isset($segments[2]) ? $segments[2] : '';
$segment3 = isset($segments[3]) ? $segments[3] : '';

// Definisikan menu untuk direktur
$isDashboardActive = ($active == 'dashboard' || $segment1 == '');
$isKaryawanActive = (in_array($active, ['karyawan', 'sdm']) || in_array($segment1, ['karyawan', 'sdm']));
$isProyekActive = (in_array($active, ['penugasan', 'proyek']) || in_array($segment1, ['penugasan', 'proyek']));
$isKeuanganActive = (in_array($active, ['keuangan']) || $segment1 == 'keuangan') && !($segment1 == 'keuangan' && $segment2 == 'pembelian');
$isPengadaanActive = (in_array($active, ['pengadaan', 'aset']) || in_array($segment1, ['pengadaan', 'aset']) || ($segment1 == 'keuangan' && $segment2 == 'pembelian'));
$isDokumenActive = (in_array($active, ['dokumen']) || $segment1 == 'dokumen');
?>
<style>
@media print {
    .sidebar, .sidebar * {
        display: none !important;
        visibility: hidden !important;
        width: 0 !important;
        height: 0 !important;
        opacity: 0 !important;
    }
}
</style>

<!-- Sidebar -->
<div class="sidebar" style="
    width: var(--sidebar-width, 270px);
    max-width: 85vw;
    background: linear-gradient(180deg, #1e3c72 0%, #2a5298 100%);
    color: white;
    height: 100vh !important;
    height: 100dvh !important;
    min-height: 100% !important;
    position: fixed !important;
    left: 0 !important;
    top: 0 !important;
    bottom: 0 !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 1050 !important;
    box-shadow: 6px 0 25px rgba(0,0,0,0.25);
    display: flex;
    flex-direction: column;
">
    <div class="sidebar-header" style="
        padding: 20px; 
        text-align: center; 
        border-bottom: 1px solid rgba(255,255,255,0.1);
        position: relative;
        flex-shrink: 0;
    ">
        <button class="btn btn-link text-white d-lg-none" onclick="toggleSidebar()" style="position: absolute; right: 10px; top: 15px; font-size: 1.2rem; text-decoration: none; padding: 0; border: none; z-index: 1050;">
            <i class="fas fa-times"></i>
        </button>
        <h4 style="margin: 0; font-weight: 600; font-size: 1.3rem;">
            <i class="fas fa-crown me-2"></i>DIREKTUR CDW
        </h4>
        <p style="opacity: 0.8; font-size: 0.8rem; margin: 5px 0 0;">
            Executive Dashboard
        </p>
    </div>
    
    <div class="sidebar-menu" style="padding: 20px 0; flex: 1; overflow-y: auto;">
        <ul class="nav flex-column" style="list-style: none; padding: 0; margin: 0;">
            
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link <?= $isDashboardActive ? 'active' : '' ?>" 
                   href="<?= base_url('direktur') ?>"
                   style="
                       color: rgba(255,255,255,0.8); 
                       padding: 12px 20px; 
                       transition: all 0.3s; 
                       border-left: 3px solid transparent;
                       display: flex;
                       align-items: center;
                       text-decoration: none;
                       <?= $isDashboardActive ? 'background: rgba(255,255,255,0.1); border-left-color: #4dabf7;' : '' ?>
                   ">
                    <i class="fas fa-tachometer-alt" style="width: 25px; text-align: center;"></i> 
                    <span>Dashboard</span>
                </a>
            </li>
            
            <!-- Karyawan & SDM Menu -->
            <li class="nav-item">
                <a class="nav-link <?= $isKaryawanActive ? 'active' : '' ?>" 
                   data-bs-toggle="collapse" href="#karyawanMenu" role="button" 
                   aria-expanded="<?= $isKaryawanActive ? 'true' : 'false' ?>"
                   style="
                       color: rgba(255,255,255,0.8); 
                       padding: 12px 20px; 
                       transition: all 0.3s; 
                       border-left: 3px solid transparent;
                       display: flex;
                       align-items: center;
                       justify-content: space-between;
                       text-decoration: none;
                       cursor: pointer;
                       <?= $isKaryawanActive ? 'background: rgba(255,255,255,0.1); border-left-color: #4dabf7;' : '' ?>
                   ">
                    <div style="display: flex; align-items: center;">
                        <i class="fas fa-users" style="width: 25px; text-align: center;"></i>
                        <span>Karyawan & SDM</span>
                    </div>
                    <i class="fas fa-chevron-down" style="
                        transition: transform 0.3s; 
                        font-size: 0.8rem;
                        <?= $isKaryawanActive ? 'transform: rotate(180deg);' : '' ?>
                    ">
                    </i>
                </a>
                <div class="collapse <?= $isKaryawanActive ? 'show' : '' ?>" 
                     id="karyawanMenu" style="background: rgba(0,0,0,0.1);">
                    <ul class="nav flex-column" style="padding: 5px 0; list-style: none;">
                        <!-- Kelola Karyawan -->
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'karyawan' && ($segment2 == '' || $segment2 == 'tambah' || $segment2 == 'edit')) ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/karyawan') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'karyawan' && ($segment2 == '' || $segment2 == 'tambah' || $segment2 == 'edit')) ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-users-cog me-2" style="width: 20px;"></i>
                                <span>Kelola Karyawan</span>
                            </a>
                        </li>
                        <!-- Kelola Akun Karyawan -->
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'karyawan' && strpos($segment2, 'akun') !== false) ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/karyawan/akun') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'karyawan' && strpos($segment2, 'akun') !== false) ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-user-shield me-2" style="width: 20px;"></i>
                                <span>Kelola Akun Karyawan</span>
                            </a>
                        </li>
                        <!-- Surat Kontrak/SP -->
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'karyawan' && $segment2 == 'surat') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/karyawan/surat') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'karyawan' && $segment2 == 'surat') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-envelope-open-text me-2" style="width: 20px;"></i>
                                <span>Surat (Kontrak/SP)</span>
                            </a>
                        <!-- Permohonan & Izin Karyawan -->
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'karyawan' && $segment2 == 'pengajuan') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/karyawan/pengajuan') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'karyawan' && $segment2 == 'pengajuan') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-clipboard-check me-2" style="width: 20px;"></i>
                                <span>Permohonan & Izin</span>
                            </a>
                        </li>
                        <!-- Cuti Karyawan & Approval -->
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'karyawan' && $segment2 == 'cuti') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/karyawan/cuti') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'karyawan' && $segment2 == 'cuti') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-umbrella-beach me-2" style="width: 20px;"></i>
                                <span>Cuti Karyawan</span>
                            </a>
                        </li>
                        <!-- Keluhan Karyawan -->
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'karyawan' && $segment2 == 'keluhan') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/karyawan/keluhan') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'karyawan' && $segment2 == 'keluhan') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-comments me-2" style="width: 20px;"></i>
                                <span>Keluhan Karyawan</span>
                            </a>
                        </li>
                        <!-- Monitoring Absensi -->
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'karyawan' && $segment2 == 'absensi') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/karyawan/absensi') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'karyawan' && $segment2 == 'absensi') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-user-clock me-2" style="width: 20px;"></i>
                                <span>Monitoring Absensi</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Penugasan & Proyek Menu -->
            <li class="nav-item">
                <a class="nav-link <?= $isProyekActive ? 'active' : '' ?>" 
                   data-bs-toggle="collapse" href="#proyekMenu" role="button" 
                   aria-expanded="<?= $isProyekActive ? 'true' : 'false' ?>"
                   style="
                       color: rgba(255,255,255,0.8); 
                       padding: 12px 20px; 
                       transition: all 0.3s; 
                       border-left: 3px solid transparent;
                       display: flex;
                       align-items: center;
                       justify-content: space-between;
                       text-decoration: none;
                       cursor: pointer;
                       <?= $isProyekActive ? 'background: rgba(255,255,255,0.1); border-left-color: #4dabf7;' : '' ?>
                   ">
                    <div style="display: flex; align-items: center;">
                        <i class="fas fa-project-diagram" style="width: 25px; text-align: center;"></i>
                        <span>Penugasan & Proyek</span>
                    </div>
                    <i class="fas fa-chevron-down" style="
                        transition: transform 0.3s; 
                        font-size: 0.8rem;
                        <?= $isProyekActive ? 'transform: rotate(180deg);' : '' ?>
                    ">
                    </i>
                </a>
                <div class="collapse <?= $isProyekActive ? 'show' : '' ?>" 
                     id="proyekMenu" style="background: rgba(0,0,0,0.1);">
                    <ul class="nav flex-column" style="padding: 5px 0; list-style: none;">
                        <!-- Penugasan Harian -->
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'penugasan' && $segment2 == '') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/penugasan') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'penugasan' && $segment2 == '') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-tasks me-2" style="width: 20px;"></i>
                                <span>Penugasan Harian</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'proyek' && $segment2 == 'laporan-harian') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/proyek/laporan-harian') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'proyek' && $segment2 == 'laporan-harian') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-clipboard-check me-2" style="width: 20px;"></i>
                                <span>Laporan Kerja Harian</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'proyek' && $segment2 == 'monitoring-laporan') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/proyek/monitoring-laporan') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'proyek' && $segment2 == 'monitoring-laporan') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-chart-line me-2" style="width: 20px;"></i>
                                <span>Monitoring Laporan</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'proyek' && $segment2 == 'baru') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/proyek/baru') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'proyek' && $segment2 == 'baru') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-folder-plus me-2" style="width: 20px;"></i>
                                <span>Project Baru</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'proyek' && $segment2 == 'timeline') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/proyek/timeline') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'proyek' && $segment2 == 'timeline') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-stream me-2" style="width: 20px;"></i>
                                <span>Timeline Kerja</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'proyek' && $segment2 == 'selesai') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/proyek/selesai') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'proyek' && $segment2 == 'selesai') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-archive me-2" style="width: 20px;"></i>
                                <span>Project Selesai</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'proyek' && $segment2 == 'pencarian-barang') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/proyek/pencarian-barang') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'proyek' && $segment2 == 'pencarian-barang') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-search-location me-2" style="width: 20px;"></i>
                                <span>Penugasan Pencarian</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Keuangan Menu -->
            <li class="nav-item">
                <a class="nav-link <?= $isKeuanganActive ? 'active' : '' ?>" 
                   data-bs-toggle="collapse" href="#keuanganMenu" role="button" 
                   aria-expanded="<?= $isKeuanganActive ? 'true' : 'false' ?>"
                   style="
                       color: rgba(255,255,255,0.8); 
                       padding: 12px 20px; 
                       transition: all 0.3s; 
                       border-left: 3px solid transparent;
                       display: flex;
                       align-items: center;
                       justify-content: space-between;
                       text-decoration: none;
                       cursor: pointer;
                       <?= $isKeuanganActive ? 'background: rgba(255,255,255,0.1); border-left-color: #4dabf7;' : '' ?>
                   ">
                    <div style="display: flex; align-items: center;">
                        <i class="fas fa-money-bill-wave" style="width: 25px; text-align: center;"></i>
                        <span>Keuangan</span>
                    </div>
                    <i class="fas fa-chevron-down" style="
                        transition: transform 0.3s; 
                        font-size: 0.8rem;
                        <?= $isKeuanganActive ? 'transform: rotate(180deg);' : '' ?>
                    ">
                    </i>
                </a>
                <div class="collapse <?= $isKeuanganActive ? 'show' : '' ?>" 
                     id="keuanganMenu" style="background: rgba(0,0,0,0.1);">
                    <ul class="nav flex-column" style="padding: 5px 0; list-style: none;">
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'keuangan' && $segment2 == 'penggajian') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/keuangan/penggajian') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'keuangan' && $segment2 == 'penggajian') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-hand-holding-usd me-2" style="width: 20px;"></i>
                                <span>Penggajian Karyawan</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'keuangan' && $segment2 == 'kasbon') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/keuangan/kasbon') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'keuangan' && $segment2 == 'kasbon') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-wallet me-2" style="width: 20px;"></i>
                                <span>Kasbon</span>
                            </a>
                         </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'keuangan' && $segment2 == 'laporan') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/keuangan/laporan') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'keuangan' && $segment2 == 'laporan') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-chart-pie me-2" style="width: 20px;"></i>
                                <span>Laporan Keuangan</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Pengadaan & Aset Menu -->
            <li class="nav-item">
                <a class="nav-link <?= $isPengadaanActive ? 'active' : '' ?>" 
                   data-bs-toggle="collapse" href="#pengadaanMenu" role="button" 
                   aria-expanded="<?= $isPengadaanActive ? 'true' : 'false' ?>"
                   style="
                       color: rgba(255,255,255,0.8); 
                       padding: 12px 20px; 
                       transition: all 0.3s; 
                       border-left: 3px solid transparent;
                       display: flex;
                       align-items: center;
                       justify-content: space-between;
                       text-decoration: none;
                       cursor: pointer;
                       <?= $isPengadaanActive ? 'background: rgba(255,255,255,0.1); border-left-color: #4dabf7;' : '' ?>
                   ">
                    <div style="display: flex; align-items: center;">
                        <i class="fas fa-boxes" style="width: 25px; text-align: center;"></i>
                        <span>Pengadaan & Aset</span>
                    </div>
                    <i class="fas fa-chevron-down" style="
                        transition: transform 0.3s; 
                        font-size: 0.8rem;
                        <?= $isPengadaanActive ? 'transform: rotate(180deg);' : '' ?>
                    ">
                    </i>
                </a>
                <div class="collapse <?= $isPengadaanActive ? 'show' : '' ?>" 
                     id="pengadaanMenu" style="background: rgba(0,0,0,0.1);">
                    <ul class="nav flex-column" style="padding: 5px 0; list-style: none;">
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'pengadaan' && $segment2 == 'pengajuan-atk') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/pengadaan/pengajuan-atk') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'pengadaan' && $segment2 == 'pengajuan-atk') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-pen-nib me-2" style="width: 20px;"></i>
                                <span>Pengajuan ATK</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'pengadaan' && $segment2 == 'stok-atk') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/pengadaan/stok-atk') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'pengadaan' && $segment2 == 'stok-atk') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-clipboard-list me-2" style="width: 20px;"></i>
                                <span>Monitoring Stok ATK</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'pengadaan' && $segment2 == 'aset') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/pengadaan/aset') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'pengadaan' && $segment2 == 'aset') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-desktop me-2" style="width: 20px;"></i>
                                <span>Pengadaan Aset</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'keuangan' && $segment2 == 'pembelian') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/keuangan/pembelian') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'keuangan' && $segment2 == 'pembelian') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-shopping-cart me-2" style="width: 20px;"></i>
                                <span>Pencatatan & Tracking Pembelian (PR)</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'pengadaan' && $segment2 == 'kerusakan') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/pengadaan/kerusakan') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'pengadaan' && $segment2 == 'kerusakan') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-tools me-2" style="width: 20px;"></i>
                                <span>Kerusakan Alat</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'pengadaan' && $segment2 == 'gudang') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/pengadaan/gudang') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'pengadaan' && $segment2 == 'gudang') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-warehouse me-2" style="width: 20px;"></i>
                                <span>Monitoring Gudang</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Dokumen Menu -->
            <li class="nav-item">
                <a class="nav-link <?= $isDokumenActive ? 'active' : '' ?>" 
                   data-bs-toggle="collapse" href="#dokumenMenu" role="button" 
                   aria-expanded="<?= $isDokumenActive ? 'true' : 'false' ?>"
                   style="
                       color: rgba(255,255,255,0.8); 
                       padding: 12px 20px; 
                       transition: all 0.3s; 
                       border-left: 3px solid transparent;
                       display: flex;
                       align-items: center;
                       justify-content: space-between;
                       text-decoration: none;
                       cursor: pointer;
                       <?= $isDokumenActive ? 'background: rgba(255,255,255,0.1); border-left-color: #4dabf7;' : '' ?>
                   ">
                    <div style="display: flex; align-items: center;">
                        <i class="fas fa-folder-open" style="width: 25px; text-align: center;"></i>
                        <span>Dokumen</span>
                    </div>
                    <i class="fas fa-chevron-down" style="
                        transition: transform 0.3s; 
                        font-size: 0.8rem;
                        <?= $isDokumenActive ? 'transform: rotate(180deg);' : '' ?>
                    ">
                    </i>
                </a>
                <div class="collapse <?= $isDokumenActive ? 'show' : '' ?>" 
                     id="dokumenMenu" style="background: rgba(0,0,0,0.1);">
                    <ul class="nav flex-column" style="padding: 5px 0; list-style: none;">
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'dokumen' && $segment2 == 'penting') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/dokumen/penting') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'dokumen' && $segment2 == 'penting') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-file-invoice me-2" style="width: 20px;"></i>
                                <span>Dokumen Penting</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'dokumen' && $segment2 == 'sertifikat') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/dokumen/sertifikat') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'dokumen' && $segment2 == 'sertifikat') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-certificate me-2" style="width: 20px;"></i>
                                <span>Dokumen Sertifikat</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'dokumen' && $segment2 == 'kontak') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/dokumen/kontak') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'dokumen' && $segment2 == 'kontak') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-address-book me-2" style="width: 20px;"></i>
                                <span>Kontak Project</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- Logout -->
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('logout') ?>"
                   style="
                       color: rgba(255,255,255,0.9); 
                       padding: 12px 20px; 
                       transition: all 0.3s; 
                       border-left: 3px solid transparent;
                       display: flex;
                       align-items: center;
                       text-decoration: none;
                       background: rgba(255,255,255,0.08);
                       margin-top: 20px;
                   ">
                    <i class="fas fa-sign-out-alt" style="width: 25px; text-align: center;"></i> 
                    <span>Keluar</span>
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- CSS untuk hover effect -->
<style>
    .sidebar {
        height: 100vh !important;
        height: 100dvh !important;
        top: 0 !important;
        bottom: 0 !important;
        min-height: 100% !important;
    }

    .sidebar .nav-link:not(.active):hover {
        background: rgba(255,255,255,0.05) !important;
        color: white !important;
        border-left-color: rgba(255,255,255,0.3) !important;
    }
    
    .sidebar .nav-link.active {
        color: white !important;
    }
    
    #karyawanMenu .nav-link:not(.active):hover,
    #proyekMenu .nav-link:not(.active):hover,
    #keuanganMenu .nav-link:not(.active):hover,
    #pengadaanMenu .nav-link:not(.active):hover,
    #dokumenMenu .nav-link:not(.active):hover {
        background: rgba(255,255,255,0.05) !important;
        color: white !important;
        border-left-color: rgba(255,255,255,0.2) !important;
    }
    
    .sidebar-menu::-webkit-scrollbar {
        width: 5px;
    }
    
    .sidebar-menu::-webkit-scrollbar-track {
        background: rgba(255,255,255,0.05);
    }
    
    .sidebar-menu::-webkit-scrollbar-thumb {
        background: rgba(255,255,255,0.2);
        border-radius: 3px;
    }
    
    .sidebar-menu::-webkit-scrollbar-thumb:hover {
        background: rgba(255,255,255,0.3);
    }
</style>