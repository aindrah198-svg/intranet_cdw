<?php
// app/Views/admin/templates/sidebar.php

$active = $active ?? 'dashboard';
$uri = service('uri');
$segments = $uri->getSegments();

// Ambil segment dengan cara yang aman
$segment1 = isset($segments[1]) ? $segments[1] : '';
$segment2 = isset($segments[2]) ? $segments[2] : '';
$segment3 = isset($segments[3]) ? $segments[3] : '';

// Definisikan semua menu utama dan submenu untuk pengecekan yang lebih mudah
$karyawanMenuItems = ['karyawan', 'dokumen', 'kontrak', 'akun'];
$isKaryawanMenuActive = (in_array($active, $karyawanMenuItems) || 
                         in_array($segment1, ['karyawan']));

$operasionalMenuItems = ['absensi', 'jamkerja', 'cuti'];
$isOperasionalMenuActive = (in_array($active, $operasionalMenuItems) || 
                           in_array($segment1, ['absensi', 'jam-kerja', 'cuti']));

// Menu baru: Manajemen Form (HRD/Admin membuat & mengelola form, direktur yang approve)
$formMenuItems = [
    'form-cuti', 'form-spk', 'form-kasbon', 'form-dokumen', 
    'form-pembelian', 'form-surat-jalan', 'form-bast', 'form-izin'
];
$isFormMenuActive = (in_array($active, $formMenuItems) || 
                     in_array($segment1, $formMenuItems));

$finansialMenuItems = ['payroll', 'bpjs', 'pajak', 'pph21'];
$isFinansialMenuActive = (in_array($active, $finansialMenuItems) || 
                         in_array($segment1, ['payroll', 'bpjs', 'pajak']));

$peformaMenuItems = ['kpi', 'tinjauan', 'audit', 'laporan'];
$isPeformaMenuActive = (in_array($active, $peformaMenuItems) || 
                       in_array($segment1, ['kpi', 'tinjauan', 'audit', 'laporan']));
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
            <i class="fas fa-hard-hat me-2"></i>CDW ENGINEERING
        </h4>
        <p style="opacity: 0.8; font-size: 0.8rem; margin: 5px 0 0;">
            Human Resource Management
        </p>
    </div>
    
    <div class="sidebar-menu" style="padding: 20px 0; height: calc(100vh - 120px); overflow-y: auto;">
        <ul class="nav flex-column" style="list-style: none; padding: 0; margin: 0;">
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link <?= $active == 'dashboard' ? 'active' : '' ?>" 
                   href="<?= base_url('admin') ?>"
                   style="
                       color: rgba(255,255,255,0.8); 
                       padding: 12px 20px; 
                       transition: all 0.3s; 
                       border-left: 3px solid transparent;
                       display: flex;
                       align-items: center;
                       text-decoration: none;
                       <?= $active == 'dashboard' ? 'background: rgba(255,255,255,0.1); border-left-color: #4dabf7;' : '' ?>
                   ">
                    <i class="fas fa-tachometer-alt" style="width: 25px; text-align: center;"></i> 
                    <span>Dashboard</span>
                </a>
            </li>
            
            <!-- Data Karyawan Menu -->
            <li class="nav-item">
                <a class="nav-link <?= $isKaryawanMenuActive ? 'active' : '' ?>" 
                   data-bs-toggle="collapse" href="#karyawanMenu" role="button" 
                   aria-expanded="<?= $isKaryawanMenuActive ? 'true' : 'false' ?>"
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
                       <?= $isKaryawanMenuActive ? 'background: rgba(255,255,255,0.1); border-left-color: #4dabf7;' : '' ?>
                   ">
                    <div style="display: flex; align-items: center;">
                        <i class="fas fa-users" style="width: 25px; text-align: center;"></i>
                        <span style="margin-left: 5px;">Data Karyawan</span>
                    </div>
                    <i class="fas fa-chevron-down" style="
                        transition: transform 0.3s; 
                        font-size: 0.8rem;
                        <?= $isKaryawanMenuActive ? 'transform: rotate(180deg);' : '' ?>
                    ">
                    </i>
                </a>
                <div class="collapse <?= $isKaryawanMenuActive ? 'show' : '' ?>" 
                     id="karyawanMenu" style="background: rgba(0,0,0,0.1);">
                    <ul class="nav flex-column" style="padding: 5px 0; list-style: none;">
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment2 == '' || $segment2 == 'aktif' || $segment2 == 'keluar' || $segment2 == 'search' || $segment2 == 'create' || $segment2 == 'show' || $segment2 == 'edit') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('admin/karyawan') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($segment2 == '' || $segment2 == 'aktif' || $segment2 == 'keluar' || $segment2 == 'search' || $segment2 == 'create' || $segment2 == 'show' || $segment2 == 'edit') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-list me-2" style="width: 20px;"></i>
                                <span>Daftar Karyawan</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $segment2 == 'dokumen' ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('admin/karyawan/dokumen') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= $segment2 == 'dokumen' ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-folder me-2" style="width: 20px;"></i>
                                <span>Dokumen Karyawan</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $segment2 == 'kontrak' ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('admin/karyawan/kontrak') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= $segment2 == 'kontrak' ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-file-contract me-2" style="width: 20px;"></i>
                                <span>Kontrak Kerja</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $segment2 == 'akun' ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('admin/karyawan/akun') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= $segment2 == 'akun' ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-user-cog me-2" style="width: 20px;"></i>
                                <span>Manajemen Akun</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- Operasional Menu -->
            <li class="nav-item">
                <a class="nav-link <?= $isOperasionalMenuActive ? 'active' : '' ?>" 
                   data-bs-toggle="collapse" href="#operasionalMenu" role="button" 
                   aria-expanded="<?= $isOperasionalMenuActive ? 'true' : 'false' ?>"
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
                       <?= $isOperasionalMenuActive ? 'background: rgba(255,255,255,0.1); border-left-color: #4dabf7;' : '' ?>
                   ">
                    <div style="display: flex; align-items: center;">
                        <i class="fas fa-tools" style="width: 25px; text-align: center;"></i>
                        <span style="margin-left: 5px;">Operasional</span>
                    </div>
                    <i class="fas fa-chevron-down" style="
                        transition: transform 0.3s; 
                        font-size: 0.8rem;
                        <?= $isOperasionalMenuActive ? 'transform: rotate(180deg);' : '' ?>
                    ">
                    </i>
                </a>
                <div class="collapse <?= $isOperasionalMenuActive ? 'show' : '' ?>" 
                     id="operasionalMenu" style="background: rgba(0,0,0,0.1);">
                    <ul class="nav flex-column" style="padding: 5px 0; list-style: none;">
                        <li class="nav-item">
                            <a class="nav-link <?= ($active == 'absensi' && $segment2 == 'user') ? 'active fw-bold' : '' ?>" 
                                href="<?= base_url('admin/absensi/my-attendance') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($active == 'absensi' && $segment2 == 'user') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-clock me-2" style="width: 20px;"></i>
                                <span>Absensi Saya</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($active == 'absensi' && $segment2 == '') ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('admin/absensi') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($active == 'absensi' && $segment2 == '') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-clipboard-list me-2" style="width: 20px;"></i>
                                <span>Absensi</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $active == 'jamkerja' || $segment1 == 'jam-kerja' ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('admin/jam-kerja') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= $active == 'jamkerja' || $segment1 == 'jam-kerja' ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-business-time me-2" style="width: 20px;"></i>
                                <span>Jam Kerja</span>
                            </a>
                        </li>
                       
                        <li class="nav-item">
                            <a class="nav-link <?= $active == 'cuti' || $segment1 == 'cuti' ? 'active fw-bold' : '' ?>" 
                            href="<?= base_url('admin/cuti') ?>"
                            style="
                                color: rgba(255,255,255,0.8); 
                                padding: 10px 15px 10px 45px;
                                font-size: 0.9rem;
                                border-left: 2px solid transparent;
                                display: flex;
                                align-items: center;
                                text-decoration: none;
                                transition: all 0.2s;
                                <?= $active == 'cuti' || $segment1 == 'cuti' ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                            ">
                                <i class="fas fa-calendar-alt me-2" style="width: 20px;"></i>
                                <span>Manajemen Cuti</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- ============ MENU BARU: MANAJEMEN FORM ============ -->
            <!-- HRD/Admin bertugas membuat, mengedit, menghapus, mencetak form -->
            <!-- Direktur akan melakukan approval terpisah -->
            <li class="nav-item">
                <a class="nav-link <?= $isFormMenuActive ? 'active' : '' ?>" 
                   data-bs-toggle="collapse" href="#formMenu" role="button" 
                   aria-expanded="<?= $isFormMenuActive ? 'true' : 'false' ?>"
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
                       <?= $isFormMenuActive ? 'background: rgba(255,255,255,0.1); border-left-color: #4dabf7;' : '' ?>
                   ">
                    <div style="display: flex; align-items: center;">
                        <i class="fas fa-edit" style="width: 25px; text-align: center;"></i>
                        <span style="margin-left: 5px;">Manajemen Form</span>
                    </div>
                    <i class="fas fa-chevron-down" style="
                        transition: transform 0.3s; 
                        font-size: 0.8rem;
                        <?= $isFormMenuActive ? 'transform: rotate(180deg);' : '' ?>
                    ">
                    </i>
                </a>
                <div class="collapse <?= $isFormMenuActive ? 'show' : '' ?>" 
                     id="formMenu" style="background: rgba(0,0,0,0.1);">
                    <ul class="nav flex-column" style="padding: 5px 0; list-style: none;">
                        <li class="nav-item">
                            <a class="nav-link <?= $active == 'form-cuti' || $segment1 == 'form-cuti' ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('admin/form-cuti') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= $active == 'form-cuti' || $segment1 == 'form-cuti' ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-calendar-check me-2" style="width: 20px;"></i>
                                <span>Form Cuti</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $active == 'form-spk' || $segment1 == 'form-spk' ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('admin/form-spk') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= $active == 'form-spk' || $segment1 == 'form-spk' ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-file-signature me-2" style="width: 20px;"></i>
                                <span>Form SPK</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $active == 'form-kasbon' || $segment1 == 'form-kasbon' ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('admin/form-kasbon') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= $active == 'form-kasbon' || $segment1 == 'form-kasbon' ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-money-bill me-2" style="width: 20px;"></i>
                                <span>Form Kasbon</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $active == 'form-dokumen' || $segment1 == 'form-dokumen' ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('admin/form-dokumen') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= $active == 'form-dokumen' || $segment1 == 'form-dokumen' ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-folder-open me-2" style="width: 20px;"></i>
                                <span>Form Dokumen</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $active == 'form-pembelian' || $segment1 == 'form-pembelian' ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('admin/form-pembelian') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= $active == 'form-pembelian' || $segment1 == 'form-pembelian' ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-shopping-cart me-2" style="width: 20px;"></i>
                                <span>Form Pembelian</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $active == 'form-surat-jalan' || $segment1 == 'form-surat-jalan' ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('admin/form-surat-jalan') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= $active == 'form-surat-jalan' || $segment1 == 'form-surat-jalan' ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-truck me-2" style="width: 20px;"></i>
                                <span>Form Surat Jalan</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $active == 'form-bast' || $segment1 == 'form-bast' ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('admin/form-bast') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= $active == 'form-bast' || $segment1 == 'form-bast' ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-check-double me-2" style="width: 20px;"></i>
                                <span>Form BAST</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $active == 'form-izin' || $segment1 == 'form-izin' ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('admin/form-izin') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= $active == 'form-izin' || $segment1 == 'form-izin' ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-id-card me-2" style="width: 20px;"></i>
                                <span>Form Izin</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <!-- ============ END MANAJEMEN FORM ============ -->
            
            <!-- Finansial Menu -->
            <li class="nav-item">
                <a class="nav-link <?= $isFinansialMenuActive ? 'active' : '' ?>" 
                   data-bs-toggle="collapse" href="#finansialMenu" role="button" 
                   aria-expanded="<?= $isFinansialMenuActive ? 'true' : 'false' ?>"
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
                       <?= $isFinansialMenuActive ? 'background: rgba(255,255,255,0.1); border-left-color: #4dabf7;' : '' ?>
                   ">
                    <div style="display: flex; align-items: center;">
                        <i class="fas fa-money-bill-wave" style="width: 25px; text-align: center;"></i>
                        <span style="margin-left: 5px;">Finansial</span>
                    </div>
                    <i class="fas fa-chevron-down" style="
                        transition: transform 0.3s; 
                        font-size: 0.8rem;
                        <?= $isFinansialMenuActive ? 'transform: rotate(180deg);' : '' ?>
                    ">
                    </i>
                </a>
                <div class="collapse <?= $isFinansialMenuActive ? 'show' : '' ?>" 
                     id="finansialMenu" style="background: rgba(0,0,0,0.1);">
                    <ul class="nav flex-column" style="padding: 5px 0; list-style: none;">
                        <li class="nav-item">
                            <a class="nav-link <?= $segment1 == 'payroll' ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('admin/payroll') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= $segment1 == 'payroll' ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-money-check-alt me-2" style="width: 20px;"></i>
                                <span>Payroll</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $segment1 == 'bpjs' ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('admin/bpjs') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= $segment1 == 'bpjs' ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-heartbeat me-2" style="width: 20px;"></i>
                                <span>BPJS</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $segment1 == 'pajak' || $active == 'pajak' || $active == 'pph21' ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('admin/pajak') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= $segment1 == 'pajak' || $active == 'pajak' || $active == 'pph21' ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-file-invoice-dollar me-2" style="width: 20px;"></i>
                                <span>Pajak PPh21</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- Peforma & Keamanan Menu -->
            <li class="nav-item">
                <a class="nav-link <?= $isPeformaMenuActive ? 'active' : '' ?>" 
                   data-bs-toggle="collapse" href="#peformaMenu" role="button" 
                   aria-expanded="<?= $isPeformaMenuActive ? 'true' : 'false' ?>"
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
                       <?= $isPeformaMenuActive ? 'background: rgba(255,255,255,0.1); border-left-color: #4dabf7;' : '' ?>
                   ">
                    <div style="display: flex; align-items: center;">
                        <i class="fas fa-chart-line" style="width: 25px; text-align: center;"></i>
                        <span style="margin-left: 5px;">Peforma & Keamanan</span>
                    </div>
                    <i class="fas fa-chevron-down" style="
                        transition: transform 0.3s; 
                        font-size: 0.8rem;
                        <?= $isPeformaMenuActive ? 'transform: rotate(180deg);' : '' ?>
                    ">
                    </i>
                </a>
                <div class="collapse <?= $isPeformaMenuActive ? 'show' : '' ?>" 
                     id="peformaMenu" style="background: rgba(0,0,0,0.1);">
                    <ul class="nav flex-column" style="padding: 5px 0; list-style: none;">
                        <li class="nav-item">
                            <a class="nav-link <?= $segment1 == 'kpi' ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('admin/kpi') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= $segment1 == 'kpi' ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-bullseye me-2" style="width: 20px;"></i>
                                <span>KPI Karyawan</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $segment1 == 'tinjauan' ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('admin/tinjauan') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= $segment1 == 'tinjauan' ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-search me-2" style="width: 20px;"></i>
                                <span>Tinjauan Karyawan</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $segment1 == 'audit' ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('admin/audit') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= $segment1 == 'audit' ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-history me-2" style="width: 20px;"></i>
                                <span>Audit Trail</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $segment1 == 'laporan' ? 'active fw-bold' : '' ?>" 
                               href="<?= base_url('admin/laporan') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= $segment1 == 'laporan' ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-file-alt me-2" style="width: 20px;"></i>
                                <span>Laporan Harian</span>
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
    
    #karyawanMenu .nav-link:not(.active):hover,
    #operasionalMenu .nav-link:not(.active):hover,
    #formMenu .nav-link:not(.active):hover,
    #finansialMenu .nav-link:not(.active):hover,
    #peformaMenu .nav-link:not(.active):hover {
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