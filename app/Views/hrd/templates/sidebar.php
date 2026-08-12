<?php
// app/Views/hrd/templates/sidebar.php

$active = $active ?? 'dashboard';
$uri = service('uri');
$segments = $uri->getSegments();

$segment1 = isset($segments[1]) ? $segments[1] : '';
$segment2 = isset($segments[2]) ? $segments[2] : '';

$isKaryawanActive  = ($segment1 === 'karyawan');
$isRekrutmenActive = ($segment1 === 'rekrutmen');
$isOperasionalActive = (in_array($segment1, ['absensi', 'jam-kerja', 'cuti']) && $segment2 !== 'my-attendance');
$isFormActive        = ($segment1 === 'form-pengajuan');
$isFinansialActive   = ($segment1 === 'finansial');
$isPerformaActive    = ($segment1 === 'performa');
$isLaporanHarianActive = ($segment1 === 'laporan-harian');
$isKeluhanActive       = ($segment1 === 'keluhan');
?>

<!-- Sidebar HRD -->
<div class="sidebar" style="
    width: var(--sidebar-width, 260px);
    background: linear-gradient(180deg, #1e3c72 0%, #2a5298 100%);
    color: white;
    height: 100vh;
    position: fixed;
    left: 0; top: 0;
    z-index: 1000;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
    overflow-y: auto;
    transition: all 0.3s;
">
    <div class="sidebar-header" style="padding: 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1);">
        <h4 style="margin: 0; font-weight: 600; font-size: 1.25rem;">
            <i class="fas fa-users-cog me-2"></i>CDW HRD
        </h4>
        <p style="opacity: 0.8; font-size: 0.78rem; margin: 5px 0 0; font-weight: 500;">
            Human Resource Management
        </p>
    </div>
    
    <div class="sidebar-menu" style="padding: 15px 0;">
        <ul class="nav flex-column" style="list-style: none; padding: 0; margin: 0;">
            
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link <?= ($active == 'dashboard' || $segment1 == '' || $segment1 == 'dashboard') ? 'active' : '' ?>" 
                   href="<?= base_url('hrd') ?>"
                   style="color: rgba(255,255,255,0.85); padding: 11px 20px; transition: all 0.3s; border-left: 3px solid transparent; display: flex; align-items: center; text-decoration: none; <?= ($active == 'dashboard' || $segment1 == '' || $segment1 == 'dashboard') ? 'background: rgba(255,255,255,0.15); border-left-color: #4dabf7; color: white;' : '' ?>">
                    <i class="fas fa-tachometer-alt" style="width: 25px; text-align: center;"></i> 
                    <span>Dashboard</span>
                </a>
            </li>
            
            <!-- Data Karyawan -->
            <li class="nav-item">
                <a class="nav-link <?= $isKaryawanActive ? 'active' : '' ?>" 
                   data-bs-toggle="collapse" href="#karyawanMenu" role="button"
                   style="color: rgba(255,255,255,0.85); padding: 11px 20px; transition: all 0.3s; border-left: 3px solid transparent; display: flex; align-items: center; justify-content: space-between; text-decoration: none; cursor: pointer; <?= $isKaryawanActive ? 'background: rgba(255,255,255,0.15); border-left-color: #4dabf7; color: white;' : '' ?>">
                    <div style="display: flex; align-items: center;">
                        <i class="fas fa-users" style="width: 25px; text-align: center;"></i>
                        <span>Data Karyawan</span>
                    </div>
                    <i class="fas fa-chevron-down" style="font-size: 0.8rem;"></i>
                </a>
                <div class="collapse <?= $isKaryawanActive ? 'show' : '' ?>" id="karyawanMenu" style="background: rgba(0,0,0,0.15);">
                    <ul class="nav flex-column" style="padding: 4px 0; list-style: none;">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('hrd/karyawan') ?>" style="color: rgba(255,255,255,0.8); padding: 8px 15px 8px 45px; font-size: 0.875rem; text-decoration: none; display: flex; align-items: center;">
                                <i class="fas fa-list me-2" style="width: 18px;"></i><span>Daftar Karyawan</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('hrd/karyawan/dokumen') ?>" style="color: rgba(255,255,255,0.8); padding: 8px 15px 8px 45px; font-size: 0.875rem; text-decoration: none; display: flex; align-items: center;">
                                <i class="fas fa-folder me-2" style="width: 18px;"></i><span>Dokumen Karyawan</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('hrd/karyawan/kontrak') ?>" style="color: rgba(255,255,255,0.8); padding: 8px 15px 8px 45px; font-size: 0.875rem; text-decoration: none; display: flex; align-items: center;">
                                <i class="fas fa-file-contract me-2" style="width: 18px;"></i><span>Kontrak Kerja</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('hrd/karyawan/akun') ?>" style="color: rgba(255,255,255,0.8); padding: 8px 15px 8px 45px; font-size: 0.875rem; text-decoration: none; display: flex; align-items: center;">
                                <i class="fas fa-user-lock me-2" style="width: 18px;"></i><span>Manajemen Akun</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- Rekrutmen -->
            <li class="nav-item">
                <a class="nav-link <?= $isRekrutmenActive ? 'active' : '' ?>" 
                   data-bs-toggle="collapse" href="#rekrutmenMenu" role="button"
                   style="color: rgba(255,255,255,0.85); padding: 11px 20px; transition: all 0.3s; border-left: 3px solid transparent; display: flex; align-items: center; justify-content: space-between; text-decoration: none; cursor: pointer; <?= $isRekrutmenActive ? 'background: rgba(255,255,255,0.15); border-left-color: #4dabf7; color: white;' : '' ?>">
                    <div style="display: flex; align-items: center;">
                        <i class="fas fa-user-plus" style="width: 25px; text-align: center;"></i>
                        <span>Rekrutmen</span>
                    </div>
                    <i class="fas fa-chevron-down" style="font-size: 0.8rem;"></i>
                </a>
                <div class="collapse <?= $isRekrutmenActive ? 'show' : '' ?>" id="rekrutmenMenu" style="background: rgba(0,0,0,0.15);">
                    <ul class="nav flex-column" style="padding: 4px 0; list-style: none;">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('hrd/rekrutmen/pelamar') ?>" style="color: rgba(255,255,255,0.8); padding: 8px 15px 8px 45px; font-size: 0.875rem; text-decoration: none; display: flex; align-items: center;">
                                <i class="fas fa-briefcase me-2" style="width: 18px;"></i><span>Lowongan & Pelamar</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('hrd/rekrutmen/onboarding') ?>" style="color: rgba(255,255,255,0.8); padding: 8px 15px 8px 45px; font-size: 0.875rem; text-decoration: none; display: flex; align-items: center;">
                                <i class="fas fa-user-check me-2" style="width: 18px;"></i><span>Onboarding</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Operasional -->
            <li class="nav-item">
                <a class="nav-link <?= $isOperasionalActive ? 'active' : '' ?>" 
                   data-bs-toggle="collapse" href="#operasionalMenu" role="button"
                   style="color: rgba(255,255,255,0.85); padding: 11px 20px; transition: all 0.3s; border-left: 3px solid transparent; display: flex; align-items: center; justify-content: space-between; text-decoration: none; cursor: pointer; <?= $isOperasionalActive ? 'background: rgba(255,255,255,0.15); border-left-color: #4dabf7; color: white;' : '' ?>">
                    <div style="display: flex; align-items: center;">
                        <i class="fas fa-calendar-check" style="width: 25px; text-align: center;"></i>
                        <span>Operasional</span>
                    </div>
                    <i class="fas fa-chevron-down" style="font-size: 0.8rem;"></i>
                </a>
                <div class="collapse <?= $isOperasionalActive ? 'show' : '' ?>" id="operasionalMenu" style="background: rgba(0,0,0,0.15);">
                    <ul class="nav flex-column" style="padding: 4px 0; list-style: none;">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('hrd/absensi/my-attendance') ?>" style="color: rgba(255,255,255,0.8); padding: 8px 15px 8px 45px; font-size: 0.875rem; text-decoration: none; display: flex; align-items: center;">
                                <i class="fas fa-user-clock me-2" style="width: 18px;"></i><span>Absensi Saya</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('hrd/absensi') ?>" style="color: rgba(255,255,255,0.8); padding: 8px 15px 8px 45px; font-size: 0.875rem; text-decoration: none; display: flex; align-items: center;">
                                <i class="fas fa-clock me-2" style="width: 18px;"></i><span>Kelola Absensi</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('hrd/jam-kerja') ?>" style="color: rgba(255,255,255,0.8); padding: 8px 15px 8px 45px; font-size: 0.875rem; text-decoration: none; display: flex; align-items: center;">
                                <i class="fas fa-business-time me-2" style="width: 18px;"></i><span>Jam Kerja</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('hrd/cuti') ?>" style="color: rgba(255,255,255,0.8); padding: 8px 15px 8px 45px; font-size: 0.875rem; text-decoration: none; display: flex; align-items: center;">
                                <i class="fas fa-plane-departure me-2" style="width: 18px;"></i><span>Manajemen Cuti</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Finansial -->
            <li class="nav-item">
                <a class="nav-link <?= $isFinansialActive ? 'active' : '' ?>" 
                   data-bs-toggle="collapse" href="#finansialMenu" role="button"
                   style="color: rgba(255,255,255,0.85); padding: 11px 20px; transition: all 0.3s; border-left: 3px solid transparent; display: flex; align-items: center; justify-content: space-between; text-decoration: none; cursor: pointer; <?= $isFinansialActive ? 'background: rgba(255,255,255,0.15); border-left-color: #4dabf7; color: white;' : '' ?>">
                    <div style="display: flex; align-items: center;">
                        <i class="fas fa-coins" style="width: 25px; text-align: center;"></i>
                        <span>Finansial & Payroll</span>
                    </div>
                    <i class="fas fa-chevron-down" style="font-size: 0.8rem;"></i>
                </a>
                <div class="collapse <?= $isFinansialActive ? 'show' : '' ?>" id="finansialMenu" style="background: rgba(0,0,0,0.15);">
                    <ul class="nav flex-column" style="padding: 4px 0; list-style: none;">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('hrd/finansial/payroll') ?>" style="color: rgba(255,255,255,0.8); padding: 8px 15px 8px 45px; font-size: 0.875rem; text-decoration: none; display: flex; align-items: center;">
                                <i class="fas fa-calculator me-2" style="width: 18px;"></i><span>Payroll</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('hrd/finansial/bpjs') ?>" style="color: rgba(255,255,255,0.8); padding: 8px 15px 8px 45px; font-size: 0.875rem; text-decoration: none; display: flex; align-items: center;">
                                <i class="fas fa-notes-medical me-2" style="width: 18px;"></i><span>BPJS</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('hrd/finansial/pajak') ?>" style="color: rgba(255,255,255,0.8); padding: 8px 15px 8px 45px; font-size: 0.875rem; text-decoration: none; display: flex; align-items: center;">
                                <i class="fas fa-receipt me-2" style="width: 18px;"></i><span>Pajak PPh21</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Performa & Keamanan -->
            <li class="nav-item">
                <a class="nav-link <?= $isPerformaActive ? 'active' : '' ?>" 
                   data-bs-toggle="collapse" href="#performaMenu" role="button"
                   style="color: rgba(255,255,255,0.85); padding: 11px 20px; transition: all 0.3s; border-left: 3px solid transparent; display: flex; align-items: center; justify-content: space-between; text-decoration: none; cursor: pointer; <?= $isPerformaActive ? 'background: rgba(255,255,255,0.15); border-left-color: #4dabf7; color: white;' : '' ?>">
                    <div style="display: flex; align-items: center;">
                        <i class="fas fa-chart-line" style="width: 25px; text-align: center;"></i>
                        <span>Performa & Keamanan</span>
                    </div>
                    <i class="fas fa-chevron-down" style="font-size: 0.8rem;"></i>
                </a>
                <div class="collapse <?= $isPerformaActive ? 'show' : '' ?>" id="performaMenu" style="background: rgba(0,0,0,0.15);">
                    <ul class="nav flex-column" style="padding: 4px 0; list-style: none;">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('hrd/performa/kpi') ?>" style="color: rgba(255,255,255,0.8); padding: 8px 15px 8px 45px; font-size: 0.875rem; text-decoration: none; display: flex; align-items: center;">
                                <i class="fas fa-trophy me-2" style="width: 18px;"></i><span>KPI Karyawan</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('hrd/performa/tinjauan') ?>" style="color: rgba(255,255,255,0.8); padding: 8px 15px 8px 45px; font-size: 0.875rem; text-decoration: none; display: flex; align-items: center;">
                                <i class="fas fa-clipboard-check me-2" style="width: 18px;"></i><span>Tinjauan Karyawan</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('hrd/performa/audit-trail') ?>" style="color: rgba(255,255,255,0.8); padding: 8px 15px 8px 45px; font-size: 0.875rem; text-decoration: none; display: flex; align-items: center;">
                                <i class="fas fa-history me-2" style="width: 18px;"></i><span>Audit Trail</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Laporan Harian & Keluhan -->
            <li class="nav-item">
                <a class="nav-link <?= $isLaporanHarianActive ? 'active' : '' ?>" href="<?= base_url('hrd/laporan-harian') ?>" style="color: rgba(255,255,255,0.85); padding: 11px 20px; transition: all 0.3s; border-left: 3px solid transparent; display: flex; align-items: center; text-decoration: none; <?= $isLaporanHarianActive ? 'background: rgba(255,255,255,0.15); border-left-color: #4dabf7; color: white;' : '' ?>">
                    <i class="fas fa-tasks" style="width: 25px; text-align: center;"></i><span>Laporan Harian</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $isKeluhanActive ? 'active' : '' ?>" href="<?= base_url('hrd/keluhan') ?>" style="color: rgba(255,255,255,0.85); padding: 11px 20px; transition: all 0.3s; border-left: 3px solid transparent; display: flex; align-items: center; text-decoration: none; <?= $isKeluhanActive ? 'background: rgba(255,255,255,0.15); border-left-color: #4dabf7; color: white;' : '' ?>">
                    <i class="fas fa-comment-dots" style="width: 25px; text-align: center;"></i><span>Keluhan Karyawan</span>
                </a>
            </li>

            <!-- Keluar -->
            <li class="nav-item mt-4">
                <a class="nav-link text-danger fw-bold" href="<?= base_url('logout') ?>" style="color: #ff6b6b !important; padding: 12px 20px; display: flex; align-items: center; text-decoration: none;">
                    <i class="fas fa-sign-out-alt" style="width: 25px; text-align: center;"></i><span>Keluar</span>
                </a>
            </li>

        </ul>
    </div>
</div>
