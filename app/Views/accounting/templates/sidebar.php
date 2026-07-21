<?php
// app/Views/accounting/templates/sidebar.php

$active = $active ?? 'dashboard';
$uri = service('uri');
$segments = $uri->getSegments();

// Ambil segment dengan cara yang aman
$segment1 = isset($segments[1]) ? $segments[1] : '';
$segment2 = isset($segments[2]) ? $segments[2] : '';
$segment3 = isset($segments[3]) ? $segments[3] : '';

// Definisikan menu accounting
$kasBankMenuItems = ['mutasi-bank', 'transfer-internal', 'rekonsiliasi', 'kas-kecil', 'pengeluaran-pribadi'];
$isKasBankActive = (in_array($active, $kasBankMenuItems) || 
                   in_array($segment2, $kasBankMenuItems) || 
                   $segment1 == 'kas-bank');

$pembukuanMenuItems = ['daftar-akun', 'jurnal-umum', 'buku-besar'];
$isPembukuanActive = (in_array($active, $pembukuanMenuItems) || 
                     in_array($segment2, $pembukuanMenuItems) || 
                     $segment1 == 'pembukuan');

$penggajianMenuItems = ['data-karyawan', 'perhitungan-gaji', 'proses-pembayaran', 'slip-gaji-laporan'];
$isPenggajianActive = (in_array($active, $penggajianMenuItems) || 
                      in_array($segment2, $penggajianMenuItems) || 
                      $segment1 == 'penggajian');

$asetTetapMenuItems = ['register-aset', 'penyusutan', 'pelepasan-aset'];
$isAsetTetapActive = (in_array($active, $asetTetapMenuItems) || 
                     in_array($segment2, $asetTetapMenuItems) || 
                     $segment1 == 'aset-tetap');

$pajakMenuItems = ['ppn', 'pph-badan', 'arsip-pajak'];
$isPajakActive = (in_array($active, $pajakMenuItems) || 
                 in_array($segment2, $pajakMenuItems) || 
                 $segment1 == 'manajemen-pajak');

$laporanMenuItems = ['laba-rugi', 'neraca', 'arus-kas', 'modal-pemilik'];
$isLaporanActive = (in_array($active, $laporanMenuItems) || 
                   in_array($segment2, $laporanMenuItems) || 
                   in_array($segment3, $laporanMenuItems) ||  // Tambahkan ini
                   $segment1 == 'laporan-keuangan');

$pribadiMenuItems = ['absensi', 'profil', 'riwayat-audit'];
$isPribadiActive = (in_array($active, $pribadiMenuItems) || 
                   in_array($segment2, $pribadiMenuItems) || 
                   $segment1 == 'pribadi');
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
            <i class="fas fa-calculator me-2"></i>PANEL KEUANGAN & AKUNTANSI
        </h4>
        <p style="opacity: 0.8; font-size: 0.9rem; margin: 5px 0 0;">
            <i class="fas fa-user me-1"></i>Akuntansi
        </p>
    </div>
    
    <div class="sidebar-menu" style="padding: 20px 0; height: calc(100vh - 120px); overflow-y: auto;">
        <ul class="nav flex-column" style="list-style: none; padding: 0; margin: 0;">
            
            <!-- MENU UTAMA -->
            <li class="nav-item mb-2">
                <div style="padding: 10px 20px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.6;">
                    <i class="fas fa-bars me-1"></i> MENU UTAMA
                </div>
            </li>
            
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link <?= $active == 'dashboard' ? 'active' : '' ?>" 
                   href="<?= site_url('accounting/dashboard') ?>"
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
            
            <!-- Kas & Bank -->
            <li class="nav-item">
                <a class="nav-link <?= $isKasBankActive ? 'active' : '' ?>" 
                   data-bs-toggle="collapse" href="#kasBankMenu" role="button" 
                   aria-expanded="<?= $isKasBankActive ? 'true' : 'false' ?>"
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
                       <?= $isKasBankActive ? 'background: rgba(255,255,255,0.1); border-left-color: #4dabf7;' : '' ?>
                   ">
                    <div style="display: flex; align-items: center;">
                        <i class="fas fa-money-bill-wave" style="width: 25px; text-align: center;"></i>
                        <span style="margin-left: 5px;">Kas & Bank</span>
                    </div>
                    <i class="fas fa-chevron-down" style="
                        transition: transform 0.3s; 
                        font-size: 0.8rem;
                        <?= $isKasBankActive ? 'transform: rotate(180deg);' : '' ?>
                    ">
                    </i>
                </a>
                <div class="collapse <?= $isKasBankActive ? 'show' : '' ?>" 
                     id="kasBankMenu" style="background: rgba(0,0,0,0.1);">
                    <ul class="nav flex-column" style="padding: 5px 0; list-style: none;">
                        <li class="nav-item">
                            <a class="nav-link <?= ($active == 'mutasi-bank' || $segment2 == 'mutasi-bank') ? 'active fw-bold' : '' ?>" 
                               href="<?= site_url('accounting/kas-bank/mutasi-bank') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($active == 'mutasi-bank' || $segment2 == 'mutasi-bank') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-exchange-alt me-2" style="width: 20px;"></i>
                                <span>Mutasi Bank</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($active == 'transfer-internal' || $segment2 == 'transfer-internal') ? 'active fw-bold' : '' ?>" 
                               href="<?= site_url('accounting/kas-bank/transfer-internal') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($active == 'transfer-internal' || $segment2 == 'transfer-internal') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-random me-2" style="width: 20px;"></i>
                                <span>Transfer Internal</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($active == 'rekonsiliasi' || $segment2 == 'rekonsiliasi') ? 'active fw-bold' : '' ?>" 
                               href="<?= site_url('accounting/kas-bank/rekonsiliasi') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($active == 'rekonsiliasi' || $segment2 == 'rekonsiliasi') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-balance-scale me-2" style="width: 20px;"></i>
                                <span>Rekonsiliasi</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($active == 'kas-kecil' || $segment2 == 'kas-kecil') ? 'active fw-bold' : '' ?>" 
                               href="<?= site_url('accounting/kas-bank/kas-kecil') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($active == 'kas-kecil' || $segment2 == 'kas-kecil') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-wallet me-2" style="width: 20px;"></i>
                                <span>Kas Kecil</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($active == 'pengeluaran-pribadi' || $segment2 == 'pengeluaran-pribadi') ? 'active fw-bold' : '' ?>" 
                               href="<?= site_url('accounting/kas-bank/pengeluaran-pribadi') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($active == 'pengeluaran-pribadi' || $segment2 == 'pengeluaran-pribadi') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-hand-holding-usd me-2" style="width: 20px;"></i>
                                <span>Pengeluaran Pribadi</span>
                               
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- Pembukuan -->
            <li class="nav-item">
                <a class="nav-link <?= $isPembukuanActive ? 'active' : '' ?>" 
                   data-bs-toggle="collapse" href="#pembukuanMenu" role="button" 
                   aria-expanded="<?= $isPembukuanActive ? 'true' : 'false' ?>"
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
                       <?= $isPembukuanActive ? 'background: rgba(255,255,255,0.1); border-left-color: #4dabf7;' : '' ?>
                   ">
                    <div style="display: flex; align-items: center;">
                        <i class="fas fa-book" style="width: 25px; text-align: center;"></i>
                        <span style="margin-left: 5px;">Pembukuan</span>
                    </div>
                    <i class="fas fa-chevron-down" style="
                        transition: transform 0.3s; 
                        font-size: 0.8rem;
                        <?= $isPembukuanActive ? 'transform: rotate(180deg);' : '' ?>
                    ">
                    </i>
                </a>
                <div class="collapse <?= $isPembukuanActive ? 'show' : '' ?>" 
                     id="pembukuanMenu" style="background: rgba(0,0,0,0.1);">
                    <ul class="nav flex-column" style="padding: 5px 0; list-style: none;">
                        <li class="nav-item">
                            <a class="nav-link <?= ($active == 'daftar-akun' || $segment2 == 'daftar-akun') ? 'active fw-bold' : '' ?>" 
                               href="<?= site_url('accounting/pembukuan/daftar-akun') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($active == 'daftar-akun' || $segment2 == 'daftar-akun') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-list-alt me-2" style="width: 20px;"></i>
                                <span>Daftar Akun (COA)</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($active == 'jurnal-umum' || $segment2 == 'jurnal-umum') ? 'active fw-bold' : '' ?>" 
                               href="<?= site_url('accounting/pembukuan/jurnal-umum') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($active == 'jurnal-umum' || $segment2 == 'jurnal-umum') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-file-invoice me-2" style="width: 20px;"></i>
                                <span>Jurnal Umum</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link <?= ($active == 'buku-besar' || $segment2 == 'buku-besar') ? 'active fw-bold' : '' ?>" 
                               href="<?= site_url('accounting/pembukuan/buku-besar') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($active == 'buku-besar' || $segment2 == 'buku-besar') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-book-open me-2" style="width: 20px;"></i>
                                <span>Buku Besar</span>
                            </a>
                        </li>

                        <!-- TAMBAHKAN INI SETELAH MENU BUKU BESAR -->
<li class="nav-item">
    <a class="nav-link <?= ($active == 'jurnal-posted' || $segment2 == 'jurnal-posted') ? 'active fw-bold' : '' ?>" 
       href="<?= site_url('accounting/pembukuan/buku-besar/jurnal-posted') ?>"
       style="
           color: rgba(255,255,255,0.8); 
           padding: 10px 15px 10px 45px;
           font-size: 0.9rem;
           border-left: 2px solid transparent;
           display: flex;
           align-items: center;
           text-decoration: none;
           transition: all 0.2s;
           <?= ($active == 'jurnal-posted' || $segment2 == 'jurnal-posted') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
       ">
        <i class="fas fa-check-circle me-2" style="width: 20px;"></i>
        <span>Jurnal Posted</span>
    </a>
</li>

                    </ul>
                </div>
            </li>
            
            <!-- Penggajian -->
            <li class="nav-item">
                <a class="nav-link <?= $isPenggajianActive ? 'active' : '' ?>" 
                   data-bs-toggle="collapse" href="#penggajianMenu" role="button" 
                   aria-expanded="<?= $isPenggajianActive ? 'true' : 'false' ?>"
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
                       <?= $isPenggajianActive ? 'background: rgba(255,255,255,0.1); border-left-color: #4dabf7;' : '' ?>
                   ">
                    <div style="display: flex; align-items: center;">
                        <i class="fas fa-money-check-alt" style="width: 25px; text-align: center;"></i>
                        <span style="margin-left: 5px;">Penggajian</span>
                    </div>
                    <i class="fas fa-chevron-down" style="
                        transition: transform 0.3s; 
                        font-size: 0.8rem;
                        <?= $isPenggajianActive ? 'transform: rotate(180deg);' : '' ?>
                    ">
                    </i>
                </a>
                <div class="collapse <?= $isPenggajianActive ? 'show' : '' ?>" 
                     id="penggajianMenu" style="background: rgba(0,0,0,0.1);">
                    <ul class="nav flex-column" style="padding: 5px 0; list-style: none;">
                        <li class="nav-item">
                            <a class="nav-link <?= ($active == 'data-karyawan' || $segment2 == 'data-karyawan') ? 'active fw-bold' : '' ?>" 
                               href="<?= site_url('accounting/penggajian/data-karyawan') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($active == 'data-karyawan' || $segment2 == 'data-karyawan') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-users me-2" style="width: 20px;"></i>
                                <span>Data Karyawan</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($active == 'perhitungan-gaji' || $segment2 == 'perhitungan-gaji') ? 'active fw-bold' : '' ?>" 
                               href="<?= site_url('accounting/penggajian/perhitungan-gaji') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($active == 'perhitungan-gaji' || $segment2 == 'perhitungan-gaji') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-calculator me-2" style="width: 20px;"></i>
                                <span>Perhitungan Gaji</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($active == 'proses-pembayaran' || $segment2 == 'proses-pembayaran') ? 'active fw-bold' : '' ?>" 
                               href="<?= site_url('accounting/penggajian/proses-pembayaran') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($active == 'proses-pembayaran' || $segment2 == 'proses-pembayaran') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-credit-card me-2" style="width: 20px;"></i>
                                <span>Proses Pembayaran</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($active == 'slip-gaji-laporan' || $segment2 == 'slip-gaji-laporan') ? 'active fw-bold' : '' ?>" 
                               href="<?= site_url('accounting/penggajian/slip-gaji-laporan') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($active == 'slip-gaji-laporan' || $segment2 == 'slip-gaji-laporan') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-file-contract me-2" style="width: 20px;"></i>
                                <span>Slip Gaji & Laporan</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- Aset Tetap -->
            <li class="nav-item">
                <a class="nav-link <?= $isAsetTetapActive ? 'active' : '' ?>" 
                   data-bs-toggle="collapse" href="#asetTetapMenu" role="button" 
                   aria-expanded="<?= $isAsetTetapActive ? 'true' : 'false' ?>"
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
                       <?= $isAsetTetapActive ? 'background: rgba(255,255,255,0.1); border-left-color: #4dabf7;' : '' ?>
                   ">
                    <div style="display: flex; align-items: center;">
                        <i class="fas fa-building" style="width: 25px; text-align: center;"></i>
                        <span style="margin-left: 5px;">Aset Tetap</span>
                    </div>
                    <i class="fas fa-chevron-down" style="
                        transition: transform 0.3s; 
                        font-size: 0.8rem;
                        <?= $isAsetTetapActive ? 'transform: rotate(180deg);' : '' ?>
                    ">
                    </i>
                </a>
                <div class="collapse <?= $isAsetTetapActive ? 'show' : '' ?>" 
                     id="asetTetapMenu" style="background: rgba(0,0,0,0.1);">
                    <ul class="nav flex-column" style="padding: 5px 0; list-style: none;">
                        <li class="nav-item">
                            <a class="nav-link <?= ($active == 'register-aset' || $segment2 == 'register-aset') ? 'active fw-bold' : '' ?>" 
                               href="<?= site_url('accounting/aset-tetap/register-aset') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($active == 'register-aset' || $segment2 == 'register-aset') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-clipboard-list me-2" style="width: 20px;"></i>
                                <span>Register Aset</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($active == 'penyusutan' || $segment2 == 'penyusutan') ? 'active fw-bold' : '' ?>" 
                               href="<?= site_url('accounting/aset-tetap/penyusutan') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($active == 'penyusutan' || $segment2 == 'penyusutan') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-chart-line me-2" style="width: 20px;"></i>
                                <span>Penyusutan</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($active == 'pelepasan-aset' || $segment2 == 'pelepasan-aset') ? 'active fw-bold' : '' ?>" 
                               href="<?= site_url('accounting/aset-tetap/pelepasan-aset') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($active == 'pelepasan-aset' || $segment2 == 'pelepasan-aset') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-trash-alt me-2" style="width: 20px;"></i>
                                <span>Pelepasan Aset</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- Manajemen Pajak -->
            <li class="nav-item">
                <a class="nav-link <?= $isPajakActive ? 'active' : '' ?>" 
                   data-bs-toggle="collapse" href="#pajakMenu" role="button" 
                   aria-expanded="<?= $isPajakActive ? 'true' : 'false' ?>"
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
                       <?= $isPajakActive ? 'background: rgba(255,255,255,0.1); border-left-color: #4dabf7;' : '' ?>
                   ">
                    <div style="display: flex; align-items: center;">
                        <i class="fas fa-file-invoice-dollar" style="width: 25px; text-align: center;"></i>
                        <span style="margin-left: 5px;">Manajemen Pajak</span>
                    </div>
                    <i class="fas fa-chevron-down" style="
                        transition: transform 0.3s; 
                        font-size: 0.8rem;
                        <?= $isPajakActive ? 'transform: rotate(180deg);' : '' ?>
                    ">
                    </i>
                </a>
                <div class="collapse <?= $isPajakActive ? 'show' : '' ?>" 
                     id="pajakMenu" style="background: rgba(0,0,0,0.1);">
                    <ul class="nav flex-column" style="padding: 5px 0; list-style: none;">
                        <li class="nav-item">
                            <a class="nav-link <?= ($active == 'ppn' || $segment2 == 'ppn') ? 'active fw-bold' : '' ?>" 
                               href="<?= site_url('accounting/manajemen-pajak/ppn') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($active == 'ppn' || $segment2 == 'ppn') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-percentage me-2" style="width: 20px;"></i>
                                <span>PPN</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($active == 'pph-badan' || $segment2 == 'pph-badan') ? 'active fw-bold' : '' ?>" 
                               href="<?= site_url('accounting/manajemen-pajak/pph-badan') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($active == 'pph-badan' || $segment2 == 'pph-badan') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-landmark me-2" style="width: 20px;"></i>
                                <span>PPh Badan</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($active == 'arsip-pajak' || $segment2 == 'arsip-pajak') ? 'active fw-bold' : '' ?>" 
                               href="<?= site_url('accounting/manajemen-pajak/arsip-pajak') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($active == 'arsip-pajak' || $segment2 == 'arsip-pajak') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-archive me-2" style="width: 20px;"></i>
                                <span>Arsip Pajak</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- Laporan Keuangan -->
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
                        <span style="margin-left: 5px;">Laporan Keuangan</span>
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
                            <a class="nav-link <?= ($active == 'laporan-laba-rugi' || $segment2 == 'laporan-laba-rugi') ? 'active fw-bold' : '' ?>" 
                                 href="<?= site_url('accounting/laporan-keuangan/laporan/laba-rugi') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($active == 'laporan-laba-rugi' || $segment2 == 'laporan-laba-rugi') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-chart-line me-2" style="width: 20px;"></i>
                                <span>Laporan Laba Rugi</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($active == 'neraca' || $segment2 == 'neraca') ? 'active fw-bold' : '' ?>" 
                             href="<?= site_url('accounting/laporan-keuangan/laporan/neraca') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($active == 'neraca' || $segment2 == 'neraca') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-balance-scale me-2" style="width: 20px;"></i>
                                <span>Laporan Neraca</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($active == 'laporan-arus-kas' || $segment2 == 'laporan-arus-kas') ? 'active fw-bold' : '' ?>" 
 href="<?= site_url('accounting/laporan-keuangan/laporan/arus-kas') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($active == 'laporan-arus-kas' || $segment2 == 'laporan-arus-kas') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-money-bill-wave me-2" style="width: 20px;"></i>
                                <span>Laporan Arus Kas</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($active == 'laporan-modal-pemilik' || $segment2 == 'laporan-modal-pemilik') ? 'active fw-bold' : '' ?>" 
                                 href="<?= site_url('accounting/laporan-keuangan/laporan/modal-pemilik') ?>"
                               style="
                                   color: rgba(255,255,255,0.8); 
                                   padding: 10px 15px 10px 45px;
                                   font-size: 0.9rem;
                                   border-left: 2px solid transparent;
                                   display: flex;
                                   align-items: center;
                                   text-decoration: none;
                                   transition: all 0.2s;
                                   <?= ($active == 'laporan-modal-pemilik' || $segment2 == 'laporan-modal-pemilik') ? 'background: rgba(255,255,255,0.15); border-left-color: #69db7c; color: white;' : '' ?>
                               ">
                                <i class="fas fa-user-tie me-2" style="width: 20px;"></i>
                                <span>Laporan Modal Pemilik</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- Separator -->
            <li class="nav-item mb-2 mt-4">
                <div style="padding: 10px 20px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.6;">
                    <i class="fas fa-user me-1"></i> MENU PRIBADI
                </div>
            </li>
            
            <!-- Absensi -->
            <li class="nav-item">
                <a class="nav-link <?= $active == 'absensi' ? 'active' : '' ?>" 
                   href="<?= site_url('accounting/pribadi/absensi') ?>"
                   style="
                       color: rgba(255,255,255,0.8); 
                       padding: 12px 20px; 
                       transition: all 0.3s; 
                       border-left: 3px solid transparent;
                       display: flex;
                       align-items: center;
                       text-decoration: none;
                       <?= $active == 'absensi' ? 'background: rgba(255,255,255,0.1); border-left-color: #4dabf7;' : '' ?>
                   ">
                    <i class="fas fa-clock" style="width: 25px; text-align: center;"></i>
                    <span>Absensi</span>
                </a>
            </li>
            
            <!-- Profile -->
            <li class="nav-item">
                <a class="nav-link <?= $active == 'profil' ? 'active' : '' ?>" 
                   href="<?= site_url('accounting/pribadi/profil') ?>"
                   style="
                       color: rgba(255,255,255,0.8); 
                       padding: 12px 20px; 
                       transition: all 0.3s; 
                       border-left: 3px solid transparent;
                       display: flex;
                       align-items: center;
                       text-decoration: none;
                       <?= $active == 'profil' ? 'background: rgba(255,255,255,0.1); border-left-color: #4dabf7;' : '' ?>
                   ">
                    <i class="fas fa-user" style="width: 25px; text-align: center;"></i>
                    <span>Profil</span>
                </a>
            </li>
            
            <!-- Riwayat Audit -->
            <li class="nav-item">
                <a class="nav-link <?= $active == 'riwayat-audit' ? 'active' : '' ?>" 
                   href="<?= site_url('accounting/pribadi/riwayat-audit') ?>"
                   style="
                       color: rgba(255,255,255,0.8); 
                       padding: 12px 20px; 
                       transition: all 0.3s; 
                       border-left: 3px solid transparent;
                       display: flex;
                       align-items: center;
                       text-decoration: none;
                       <?= $active == 'riwayat-audit' ? 'background: rgba(255,255,255,0.1); border-left-color: #4dabf7;' : '' ?>
                   ">
                    <i class="fas fa-history" style="width: 25px; text-align: center;"></i>
                    <span>Riwayat Audit</span>
                </a>
            </li>
            
            <!-- Keluar -->
            <li class="nav-item mt-4">
                <a class="nav-link" href="<?= site_url('logout') ?>"
                   style="
                       color: rgba(255,255,255,0.9); 
                       padding: 12px 20px; 
                       transition: all 0.3s; 
                       border-left: 3px solid transparent;
                       display: flex;
                       align-items: center;
                       text-decoration: none;
                       background: rgba(255,255,255,0.08);
                       margin: 10px 15px;
                       border-radius: var(--border-radius-sm);
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
    
    .collapse .nav-link:not(.active):hover {
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