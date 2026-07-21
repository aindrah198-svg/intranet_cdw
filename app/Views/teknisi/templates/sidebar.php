<?php
// app/Views/teknisi/templates/sidebar.php

$active = $active ?? 'dashboard';
$uri = service('uri');
$segments = $uri->getSegments();

// Ambil segment dengan cara yang aman
$segment1 = isset($segments[1]) ? $segments[1] : '';
$segment2 = isset($segments[2]) ? $segments[2] : '';
$segment3 = isset($segments[3]) ? $segments[3] : '';

// Check active menu
$isAbsensiActive = $segment2 === 'absensi';
$isTugasProyekActive = $segment2 === 'tugas-proyek';
$isGudangActive = $segment2 === 'gudang';
$isPengajuanActive = $segment2 === 'pengajuan' || $segment2 === 'cuti';
$isLaporanActive = $segment2 === 'laporan';
$isProfileActive = $segment2 === 'profile';
?>

<!-- Sidebar -->
<nav class="sidebar">
    <div class="sidebar-brand">
        <h5 class="mb-0">
            <i class="fas fa-tools me-2"></i>
            TEKNISI PANEL
        </h5>
    </div>
    
    <div class="sidebar-user">
        <div class="sidebar-user-avatar">
            <?= strtoupper(substr($user['name'] ?? 'T', 0, 1)) ?>
        </div>
        <div class="sidebar-user-name"><?= htmlspecialchars($user['name'] ?? 'Teknisi') ?></div>
        <div class="sidebar-user-role"><?= ucfirst($user['role'] ?? 'teknisi') ?></div>
    </div>
    
    <div class="sidebar-menu" style="padding: 20px 0; height: calc(100vh - 220px); overflow-y: auto;">
        <ul class="nav flex-column">
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link <?= $active == 'dashboard' ? 'active' : '' ?>" 
                   href="<?= base_url('teknisi/dashboard') ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            
            <!-- Absensi -->
            <li class="nav-item">
                <a class="nav-link <?= $isAbsensiActive ? 'active' : '' ?>" 
                   href="<?= base_url('teknisi/absensi') ?>">
                    <i class="fas fa-clock"></i> Absensi
                </a>
            </li>
            
            <!-- Tugas & Proyek -->
            <li class="nav-item">
                <a class="nav-link <?= $isTugasProyekActive ? 'active has-submenu' : 'has-submenu' ?>" 
                   data-bs-toggle="collapse" href="#tugasProyekMenu" 
                   aria-expanded="<?= $isTugasProyekActive ? 'true' : 'false' ?>">
                    <i class="fas fa-tasks"></i> Tugas & Proyek
                </a>
                <div class="collapse <?= $isTugasProyekActive ? 'show' : '' ?>" id="tugasProyekMenu">
                    <ul class="nav flex-column submenu">
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment3 === 'spk') ? 'active' : '' ?>" 
                               href="<?= base_url('teknisi/tugas-proyek/spk') ?>">
                                <i class="fas fa-file-contract me-2"></i> SPK/Tugas Instalasi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment3 === 'timeline') ? 'active' : '' ?>" 
                               href="<?= base_url('teknisi/tugas-proyek/timeline') ?>">
                                <i class="fas fa-chart-line me-2"></i> Timeline/Grafik
                            </a>
                        </li>
                        <!-- MENU TAMBAHAN WAKTU TELAH DIHAPUS -->
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment3 === 'tambahan-barang') ? 'active' : '' ?>" 
                               href="<?= base_url('teknisi/tugas-proyek/tambahan-barang') ?>">
                                <i class="fas fa-boxes me-2"></i> Tambahan Barang
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment3 === 'tambah-client') ? 'active' : '' ?>" 
                               href="<?= base_url('teknisi/tugas-proyek/tambah-client') ?>">
                                <i class="fas fa-user-plus me-2"></i> Tambah Client
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- Gudang & Penyimpanan -->
            <li class="nav-item">
                <a class="nav-link <?= $isGudangActive ? 'active has-submenu' : 'has-submenu' ?>" 
                   data-bs-toggle="collapse" href="#gudangMenu" 
                   aria-expanded="<?= $isGudangActive ? 'true' : 'false' ?>">
                    <i class="fas fa-warehouse"></i> Gudang & Penyimpanan
                </a>
                <div class="collapse <?= $isGudangActive ? 'show' : '' ?>" id="gudangMenu">
                    <ul class="nav flex-column submenu">
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment2 === 'gudang' && empty($segment3)) ? 'active' : '' ?>" 
                               href="<?= base_url('teknisi/gudang') ?>">
                                <i class="fas fa-home me-2"></i> Dashboard Gudang
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment3 === 'penyimpanan') ? 'active' : '' ?>" 
                               href="<?= base_url('teknisi/gudang/penyimpanan') ?>">
                                <i class="fas fa-box me-2"></i> Penyimpanan Gudang
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment3 === 'peralatan-dipinjam') ? 'active' : '' ?>" 
                               href="<?= base_url('teknisi/gudang/peralatan-dipinjam') ?>">
                                <i class="fas fa-tools me-2"></i> Peralatan Dipinjam
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment3 === 'perawatan-alat') ? 'active' : '' ?>" 
                               href="<?= base_url('teknisi/gudang/perawatan-alat') ?>">
                                <i class="fas fa-wrench me-2"></i> Perawatan Alat
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- Pengajuan -->
            <li class="nav-item">
                <a class="nav-link <?= $isPengajuanActive ? 'active has-submenu' : 'has-submenu' ?>" 
                   data-bs-toggle="collapse" href="#pengajuanMenu" 
                   aria-expanded="<?= $isPengajuanActive ? 'true' : 'false' ?>">
                    <i class="fas fa-file-alt"></i> Pengajuan
                </a>
                <div class="collapse <?= $isPengajuanActive ? 'show' : '' ?>" id="pengajuanMenu">
                    <ul class="nav flex-column submenu">
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment2 === 'pengajuan' && empty($segment3)) ? 'active' : '' ?>" 
                               href="<?= base_url('teknisi/pengajuan') ?>">
                                <i class="fas fa-list me-2"></i> Semua Pengajuan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment3 === 'permintaan-pembelian') ? 'active' : '' ?>" 
                               href="<?= base_url('teknisi/pengajuan/permintaan-pembelian') ?>">
                                <i class="fas fa-shopping-cart me-2"></i> Permintaan Pembelian
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment3 === 'biaya-lapangan') ? 'active' : '' ?>" 
                               href="<?= base_url('teknisi/pengajuan/biaya-lapangan') ?>">
                                <i class="fas fa-money-bill-wave me-2"></i> Biaya Lapangan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment2 === 'cuti') ? 'active' : '' ?>" 
                               href="<?= base_url('teknisi/cuti') ?>">
                                <i class="fas fa-calendar-alt me-2"></i> Cuti
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- Laporan -->
            <li class="nav-item">
                <a class="nav-link <?= $isLaporanActive ? 'active has-submenu' : 'has-submenu' ?>" 
                   data-bs-toggle="collapse" href="#laporanMenu" 
                   aria-expanded="<?= $isLaporanActive ? 'true' : 'false' ?>">
                    <i class="fas fa-chart-bar"></i> Laporan
                </a>
                <div class="collapse <?= $isLaporanActive ? 'show' : '' ?>" id="laporanMenu">
                    <ul class="nav flex-column submenu">
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment2 === 'laporan' && empty($segment3)) ? 'active' : '' ?>" 
                               href="<?= base_url('teknisi/laporan') ?>">
                                <i class="fas fa-home me-2"></i> Dashboard Laporan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment3 === 'lapangan') ? 'active' : '' ?>" 
                               href="<?= base_url('teknisi/laporan/lapangan') ?>">
                                <i class="fas fa-hard-hat me-2"></i> Laporan Lapangan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($segment3 === 'inventory') ? 'active' : '' ?>" 
                               href="<?= base_url('teknisi/laporan/inventory') ?>">
                                <i class="fas fa-boxes me-2"></i> Laporan Inventory
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- Profile -->
            <li class="nav-item">
                <a class="nav-link <?= $isProfileActive ? 'active' : '' ?>" 
                   href="<?= base_url('teknisi/profile') ?>">
                    <i class="fas fa-user"></i> Profile
                </a>
            </li>
            
            <!-- Logout -->
            <li class="nav-item mt-4">
                <a class="nav-link text-danger" href="<?= base_url('logout') ?>">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</nav>

<!-- Mobile Toggle Button -->
<button class="btn btn-primary sidebar-toggle d-md-none" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
</button>

<script>
// Sidebar toggle for mobile
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    
    sidebar.classList.toggle('show');
    mainContent.classList.toggle('expanded');
}
</script>