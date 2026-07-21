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
$isMonitoringActive = (in_array($active, ['monitoring', 'absensi', 'performansi', 'penggajian', 'invoice']) || 
                      in_array($segment1, ['monitoring']));
$isApprovalActive = (in_array($active, ['approval', 'cuti', 'spk', 'kasbon', 'dokumen', 'pembelian', 'surat-jalan', 'izin', 'bast']) || 
                     in_array($segment1, ['approval']));
$isLaporanActive = (in_array($active, ['laporan', 'keuangan', 'stok']) || 
                    in_array($segment1, ['laporan']));
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
        <h4 style="margin: 0; font-weight: 600; font-size: 1.3rem;">
            <i class="fas fa-crown me-2"></i>DIREKTUR CDW
        </h4>
        <p style="opacity: 0.8; font-size: 0.8rem; margin: 5px 0 0;">
            Executive Dashboard
        </p>
    </div>
    
    <div class="sidebar-menu" style="padding: 20px 0; height: calc(100vh - 120px); overflow-y: auto;">
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
            
            <!-- Monitoring Menu -->
            <li class="nav-item">
                <a class="nav-link <?= $isMonitoringActive ? 'active' : '' ?>" 
                   data-bs-toggle="collapse" href="#monitoringMenu" role="button" 
                   aria-expanded="<?= $isMonitoringActive ? 'true' : 'false' ?>"
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
                       <?= $isMonitoringActive ? 'background: rgba(255,255,255,0.1); border-left-color: #4dabf7;' : '' ?>
                   ">
                    <div style="display: flex; align-items: center;">
                        <i class="fas fa-chart-line" style="width: 25px; text-align: center;"></i>
                        <span>Monitoring</span>
                    </div>
                    <i class="fas fa-chevron-down" style="
                        transition: transform 0.3s; 
                        font-size: 0.8rem;
                        <?= $isMonitoringActive ? 'transform: rotate(180deg);' : '' ?>
                    ">
                    </i>
                </a>
                <div class="collapse <?= $isMonitoringActive ? 'show' : '' ?>" 
                     id="monitoringMenu" style="background: rgba(0,0,0,0.1);">
                    <ul class="nav flex-column" style="padding: 5px 0; list-style: none;">
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'monitoring' && $segment2 == 'absensi') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/monitoring/absensi') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'monitoring' && $segment2 == 'absensi') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-user-clock me-2" style="width: 20px;"></i>
                                <span>Monitoring Absensi</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'monitoring' && $segment2 == 'performansi') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/monitoring/performansi') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'monitoring' && $segment2 == 'performansi') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-chart-bar me-2" style="width: 20px;"></i>
                                <span>Monitoring Performansi</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'monitoring' && $segment2 == 'ringkasan-penggajian') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/monitoring/ringkasan-penggajian') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'monitoring' && $segment2 == 'ringkasan-penggajian') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-money-check-alt me-2" style="width: 20px;"></i>
                                <span>Ringkasan Penggajian</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'monitoring' && $segment2 == 'invoice-piutang') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/monitoring/invoice-piutang') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'monitoring' && $segment2 == 'invoice-piutang') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-file-invoice-dollar me-2" style="width: 20px;"></i>
                                <span>Invoice & Piutang</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- Approval Menu -->
            <li class="nav-item">
                <a class="nav-link <?= $isApprovalActive ? 'active' : '' ?>" 
                   data-bs-toggle="collapse" href="#approvalMenu" role="button" 
                   aria-expanded="<?= $isApprovalActive ? 'true' : 'false' ?>"
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
                       <?= $isApprovalActive ? 'background: rgba(255,255,255,0.1); border-left-color: #4dabf7;' : '' ?>
                   ">
                    <div style="display: flex; align-items: center;">
                        <i class="fas fa-check-circle" style="width: 25px; text-align: center;"></i>
                        <span>Approval</span>
                    </div>
                    <i class="fas fa-chevron-down" style="
                        transition: transform 0.3s; 
                        font-size: 0.8rem;
                        <?= $isApprovalActive ? 'transform: rotate(180deg);' : '' ?>
                    ">
                    </i>
                </a>
                <div class="collapse <?= $isApprovalActive ? 'show' : '' ?>" 
                     id="approvalMenu" style="background: rgba(0,0,0,0.1);">
                    <ul class="nav flex-column" style="padding: 5px 0; list-style: none;">
                        <!-- Approval Cuti -->
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'approval' && $segment2 == 'cuti') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/approval/cuti') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'approval' && $segment2 == 'cuti') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-calendar-alt me-2" style="width: 20px;"></i>
                                <span>Approval Cuti</span>
                            </a>
                        </li>
                        <!-- Approval SPK -->
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'approval' && $segment2 == 'spk') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/approval/spk') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'approval' && $segment2 == 'spk') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-file-contract me-2" style="width: 20px;"></i>
                                <span>Approval SPK</span>
                            </a>
                        </li>
                        <!-- Approval Kasbon -->
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'approval' && $segment2 == 'kasbon') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/approval/kasbon') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'approval' && $segment2 == 'kasbon') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-money-bill-wave me-2" style="width: 20px;"></i>
                                <span>Approval Kasbon</span>
                            </a>
                        </li>
                        <!-- Approval Dokumen -->
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'approval' && $segment2 == 'dokumen') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/approval/dokumen') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'approval' && $segment2 == 'dokumen') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-file-alt me-2" style="width: 20px;"></i>
                                <span>Approval Dokumen</span>
                            </a>
                        </li>
                        <!-- Approval Pembelian -->
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'approval' && $segment2 == 'pembelian') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/approval/pembelian') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'approval' && $segment2 == 'pembelian') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-shopping-cart me-2" style="width: 20px;"></i>
                                <span>Approval Pembelian</span>
                            </a>
                        </li>
                        <!-- Approval Surat Jalan -->
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'approval' && $segment2 == 'surat-jalan') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/approval/surat-jalan') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'approval' && $segment2 == 'surat-jalan') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-truck me-2" style="width: 20px;"></i>
                                <span>Approval Surat Jalan</span>
                            </a>
                        </li>
                        <!-- Approval Izin -->
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'approval' && $segment2 == 'izin') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/approval/izin') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'approval' && $segment2 == 'izin') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-user-edit me-2" style="width: 20px;"></i>
                                <span>Approval Izin</span>
                            </a>
                        </li>
                        <!-- Approval BAST -->
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'approval' && $segment2 == 'bast') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/approval/bast') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'approval' && $segment2 == 'bast') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-file-signature me-2" style="width: 20px;"></i>
                                <span>Approval BAST</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- Laporan Menu -->
            <li class="nav-item">
                <a class="nav-link <?= $isLaporanActive ? 'active' : '' ?>" 
                   data-bs-toggle="collapse" href="#laporanMenu" role="button" 
                   aria-expanded="<?= $isLaporanActive ? 'true' : 'false' ?>"
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
                       <?= $isLaporanActive ? 'background: rgba(255,255,255,0.1); border-left-color: #4dabf7;' : '' ?>
                   ">
                    <div style="display: flex; align-items: center;">
                        <i class="fas fa-chart-bar" style="width: 25px; text-align: center;"></i>
                        <span>Laporan</span>
                    </div>
                    <i class="fas fa-chevron-down" style="
                        transition: transform 0.3s; 
                        font-size: 0.8rem;
                        <?= $isLaporanActive ? 'transform: rotate(180deg);' : '' ?>
                    ">
                    </i>
                </a>
                <div class="collapse <?= $isLaporanActive ? 'show' : '' ?>" 
                     id="laporanMenu" style="background: rgba(0,0,0,0.1);">
                    <ul class="nav flex-column" style="padding: 5px 0; list-style: none;">
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'laporan' && $segment2 == 'keuangan') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/laporan/keuangan') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'laporan' && $segment2 == 'keuangan') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-chart-line me-2" style="width: 20px;"></i>
                                <span>Laporan Keuangan</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment1 == 'laporan' && $segment2 == 'stok-gudang') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('direktur/laporan/stok-gudang') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment1 == 'laporan' && $segment2 == 'stok-gudang') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-warehouse me-2" style="width: 20px;"></i>
                                <span>Laporan Stok Gudang</span>
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
    .sidebar .nav-link:not(.active):hover {
        background: rgba(255,255,255,0.05) !important;
        color: white !important;
        border-left-color: rgba(255,255,255,0.3) !important;
    }
    
    .sidebar .nav-link.active {
        color: white !important;
    }
    
    #monitoringMenu .nav-link:not(.active):hover,
    #approvalMenu .nav-link:not(.active):hover,
    #laporanMenu .nav-link:not(.active):hover {
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