<?php
$active = $active ?? 'dashboard';
$uri = service('uri');
$segments = $uri->getSegments();
$segment2 = $segments[1] ?? '';
$segment3 = $segments[2] ?? '';

// Active state checks
$isDashboardActive = $active === 'dashboard' || $segment2 === 'dashboard' || $segment2 === '';
$isAbsensiActive = $segment2 === 'absensi';

$isTugasProyekActive = $segment2 === 'tugas-proyek';
$isSpkActive = $segment2 === 'tugas-proyek' && ($segment3 === 'spk' || $segment3 === '');
$isTimelineActive = $segment2 === 'tugas-proyek' && $segment3 === 'timeline';
$isTambahanBarangActive = $segment2 === 'tugas-proyek' && $segment3 === 'tambahan-barang';
$isInfoClientActive = $segment2 === 'tugas-proyek' && ($segment3 === 'info-client' || $segment3 === 'tambah-client');

$isGudangActive = $segment2 === 'gudang';
$isGudangDashActive = $segment2 === 'gudang' && ($segment3 === '' || $segment3 === 'index');
$isPenyimpananActive = $segment2 === 'gudang' && $segment3 === 'penyimpanan';
$isPinjamActive = $segment2 === 'gudang' && $segment3 === 'peralatan-dipinjam';
$isPerawatanActive = $segment2 === 'gudang' && $segment3 === 'perawatan-alat';

$isPengajuanActive = $segment2 === 'pengajuan' || $segment2 === 'cuti';
$isPengajuanSemuaActive = $segment2 === 'pengajuan' && ($segment3 === '' || $segment3 === 'index');
$isPembelianActive = $segment2 === 'pengajuan' && $segment3 === 'permintaan-pembelian';
$isBiayaLapanganActive = $segment2 === 'pengajuan' && $segment3 === 'biaya-lapangan';
$isCutiActive = $segment2 === 'cuti' || ($segment2 === 'pengajuan' && $segment3 === 'cuti');

$isLaporanActive = $segment2 === 'laporan';
$isLaporanDashActive = $segment2 === 'laporan' && ($segment3 === '' || $segment3 === 'index');
$isLaporanLapanganActive = $segment2 === 'laporan' && ($segment3 === 'lapangan' || $segment3 === 'harian');
$isKeluhanActive = $segment2 === 'laporan' && $segment3 === 'keluhan';
$isLaporanInvActive = $segment2 === 'laporan' && $segment3 === 'inventory';

$isPribadiActive = $segment2 === 'pribadi' || $segment2 === 'profile';
$isSlipGajiActive = $segment2 === 'pribadi' && $segment3 === 'slip-gaji';
$isProfilActive = $segment2 === 'profile' || ($segment2 === 'pribadi' && $segment3 === 'profil');
?>

<!-- Navigasi Sidebar Teknisi -->
<nav class="sidebar text-white" id="sidebar" style="width: 260px; background: #004d40; min-height: 100vh; position: fixed; top: 0; left: 0; z-index: 1050; overflow-y: auto;">
    <div class="sidebar-header p-3 border-bottom border-secondary text-center">
        <h5 class="mb-0 fw-bold text-white tracking-wide"><i class="fas fa-tools text-warning me-2"></i>TEKNISI PANEL</h5>
        <small class="text-white-50">Operasional Teknik & Lapangan</small>
    </div>

    <div class="sidebar-user p-3 border-bottom border-secondary d-flex align-items-center">
        <div class="rounded-circle bg-warning text-dark font-weight-bold d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px; font-weight: 700; font-size: 1.1rem;">
            <?= strtoupper(substr(session()->get('username') ?? 'T', 0, 1)) ?>
        </div>
        <div>
            <div class="fw-bold text-white mb-0"><?= esc(session()->get('name') ?? session()->get('username') ?? 'Teknisi Staf') ?></div>
            <small class="badge bg-warning text-dark">Staf Teknisi</small>
        </div>
    </div>

    <div class="sidebar-menu p-2">
        <ul class="nav flex-column gap-1">
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link text-white py-2 px-3 rounded <?= $isDashboardActive ? 'bg-success fw-bold active' : '' ?>" href="<?= site_url('teknisi/dashboard') ?>">
                    <i class="fas fa-tachometer-alt me-2 text-warning"></i> Dashboard
                </a>
            </li>

            <!-- Absensi -->
            <li class="nav-item">
                <a class="nav-link text-white py-2 px-3 rounded <?= $isAbsensiActive ? 'bg-success fw-bold active' : '' ?>" href="<?= site_url('teknisi/absensi') ?>">
                    <i class="fas fa-user-check me-2 text-warning"></i> Absensi
                </a>
            </li>

            <!-- Tugas & Proyek -->
            <li class="nav-item">
                <a class="nav-link text-white py-2 px-3 rounded d-flex justify-content-between align-items-center <?= $isTugasProyekActive ? 'bg-success fw-bold' : '' ?>" 
                   data-bs-toggle="collapse" data-toggle="collapse" href="#menuTugasProyek" role="button" aria-expanded="<?= $isTugasProyekActive ? 'true' : 'false' ?>">
                    <span><i class="fas fa-tasks me-2 text-warning"></i> Tugas & Proyek</span>
                    <i class="fas fa-chevron-down small"></i>
                </a>
                <div class="collapse <?= $isTugasProyekActive ? 'show' : '' ?>" id="menuTugasProyek">
                    <ul class="nav flex-column ps-3 py-1 gap-1">
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $isSpkActive ? 'text-white fw-bold bg-white bg-opacity-25' : '' ?>" href="<?= site_url('teknisi/tugas-proyek/spk') ?>">
                                <i class="fas fa-file-contract me-2"></i> SPK / Tugas Instalasi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $isTimelineActive ? 'text-white fw-bold bg-white bg-opacity-25' : '' ?>" href="<?= site_url('teknisi/tugas-proyek/timeline') ?>">
                                <i class="fas fa-chart-line me-2"></i> Timeline / Grafik
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $isTambahanBarangActive ? 'text-white fw-bold bg-white bg-opacity-25' : '' ?>" href="<?= site_url('teknisi/tugas-proyek/tambahan-barang') ?>">
                                <i class="fas fa-boxes me-2"></i> Tambahan Barang
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $isInfoClientActive ? 'text-white fw-bold bg-white bg-opacity-25' : '' ?>" href="<?= site_url('teknisi/tugas-proyek/info-client') ?>">
                                <i class="fas fa-address-book me-2"></i> Info Client Project
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Gudang & Penyimpanan -->
            <li class="nav-item">
                <a class="nav-link text-white py-2 px-3 rounded d-flex justify-content-between align-items-center <?= $isGudangActive ? 'bg-success fw-bold' : '' ?>" 
                   data-bs-toggle="collapse" data-toggle="collapse" href="#menuGudang" role="button" aria-expanded="<?= $isGudangActive ? 'true' : 'false' ?>">
                    <span><i class="fas fa-warehouse me-2 text-warning"></i> Gudang & Penyimpanan</span>
                    <i class="fas fa-chevron-down small"></i>
                </a>
                <div class="collapse <?= $isGudangActive ? 'show' : '' ?>" id="menuGudang">
                    <ul class="nav flex-column ps-3 py-1 gap-1">
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $isGudangDashActive ? 'text-white fw-bold bg-white bg-opacity-25' : '' ?>" href="<?= site_url('teknisi/gudang') ?>">
                                <i class="fas fa-home me-2"></i> Dashboard Gudang
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $isPenyimpananActive ? 'text-white fw-bold bg-white bg-opacity-25' : '' ?>" href="<?= site_url('teknisi/gudang/penyimpanan') ?>">
                                <i class="fas fa-box me-2"></i> Penyimpanan Gudang
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $isPinjamActive ? 'text-white fw-bold bg-white bg-opacity-25' : '' ?>" href="<?= site_url('teknisi/gudang/peralatan-dipinjam') ?>">
                                <i class="fas fa-tools me-2"></i> Peralatan Dipinjam
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $isPerawatanActive ? 'text-white fw-bold bg-white bg-opacity-25' : '' ?>" href="<?= site_url('teknisi/gudang/perawatan-alat') ?>">
                                <i class="fas fa-wrench me-2"></i> Perawatan Alat
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Pengajuan -->
            <li class="nav-item">
                <a class="nav-link text-white py-2 px-3 rounded d-flex justify-content-between align-items-center <?= $isPengajuanActive ? 'bg-success fw-bold' : '' ?>" 
                   data-bs-toggle="collapse" data-toggle="collapse" href="#menuPengajuan" role="button" aria-expanded="<?= $isPengajuanActive ? 'true' : 'false' ?>">
                    <span><i class="fas fa-file-alt me-2 text-warning"></i> Pengajuan</span>
                    <i class="fas fa-chevron-down small"></i>
                </a>
                <div class="collapse <?= $isPengajuanActive ? 'show' : '' ?>" id="menuPengajuan">
                    <ul class="nav flex-column ps-3 py-1 gap-1">
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $isPengajuanSemuaActive ? 'text-white fw-bold bg-white bg-opacity-25' : '' ?>" href="<?= site_url('teknisi/pengajuan') ?>">
                                <i class="fas fa-list me-2"></i> Semua Pengajuan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $isPembelianActive ? 'text-white fw-bold bg-white bg-opacity-25' : '' ?>" href="<?= site_url('teknisi/pengajuan/permintaan-pembelian') ?>">
                                <i class="fas fa-shopping-cart me-2"></i> Permintaan Pembelian
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $isBiayaLapanganActive ? 'text-white fw-bold bg-white bg-opacity-25' : '' ?>" href="<?= site_url('teknisi/pengajuan/biaya-lapangan') ?>">
                                <i class="fas fa-money-bill-wave me-2"></i> Biaya Lapangan (Reimburse)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $isCutiActive ? 'text-white fw-bold bg-white bg-opacity-25' : '' ?>" href="<?= site_url('teknisi/pengajuan/cuti') ?>">
                                <i class="fas fa-calendar-minus me-2"></i> Cuti
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Laporan (FITUR WAJIB) -->
            <li class="nav-item">
                <a class="nav-link text-white py-2 px-3 rounded d-flex justify-content-between align-items-center <?= $isLaporanActive ? 'bg-success fw-bold' : '' ?>" 
                   data-bs-toggle="collapse" data-toggle="collapse" href="#menuLaporan" role="button" aria-expanded="<?= $isLaporanActive ? 'true' : 'false' ?>">
                    <span><i class="fas fa-chart-bar me-2 text-warning"></i> Laporan & Keluhan</span>
                    <i class="fas fa-chevron-down small"></i>
                </a>
                <div class="collapse <?= $isLaporanActive ? 'show' : '' ?>" id="menuLaporan">
                    <ul class="nav flex-column ps-3 py-1 gap-1">
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $isLaporanDashActive ? 'text-white fw-bold bg-white bg-opacity-25' : '' ?>" href="<?= site_url('teknisi/laporan') ?>">
                                <i class="fas fa-chart-pie me-2"></i> Dashboard Laporan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $isLaporanLapanganActive ? 'text-white fw-bold bg-white bg-opacity-25' : '' ?>" href="<?= site_url('teknisi/laporan/lapangan') ?>">
                                <i class="fas fa-hard-hat me-2"></i> Laporan Pekerjaan Harian
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $isKeluhanActive ? 'text-white fw-bold bg-white bg-opacity-25' : '' ?>" href="<?= site_url('teknisi/laporan/keluhan') ?>">
                                <i class="fas fa-exclamation-triangle me-2"></i> Keluhan Lapangan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $isLaporanInvActive ? 'text-white fw-bold bg-white bg-opacity-25' : '' ?>" href="<?= site_url('teknisi/laporan/inventory') ?>">
                                <i class="fas fa-boxes me-2"></i> Laporan Inventory
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Menu Pribadi -->
            <li class="nav-item">
                <a class="nav-link text-white py-2 px-3 rounded d-flex justify-content-between align-items-center <?= $isPribadiActive ? 'bg-success fw-bold' : '' ?>" 
                   data-bs-toggle="collapse" data-toggle="collapse" href="#menuPribadi" role="button" aria-expanded="<?= $isPribadiActive ? 'true' : 'false' ?>">
                    <span><i class="fas fa-user-clock me-2 text-warning"></i> Menu Pribadi</span>
                    <i class="fas fa-chevron-down small"></i>
                </a>
                <div class="collapse <?= $isPribadiActive ? 'show' : '' ?>" id="menuPribadi">
                    <ul class="nav flex-column ps-3 py-1 gap-1">
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded" href="<?= site_url('teknisi/pribadi/absensi') ?>">
                                <i class="fas fa-user-check me-2"></i> Absensi Saya
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded" href="<?= site_url('teknisi/pribadi/tugas') ?>">
                                <i class="fas fa-tasks me-2"></i> Tugas Saya
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded" href="<?= site_url('teknisi/pribadi/laporan-harian') ?>">
                                <i class="fas fa-clipboard-list me-2"></i> Laporan Kerja Harian
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded" href="<?= site_url('teknisi/pribadi/keluhan') ?>">
                                <i class="fas fa-comment-dots me-2"></i> Keluhan Saya
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded" href="<?= site_url('teknisi/pribadi/pengajuan') ?>">
                                <i class="fas fa-paper-plane me-2"></i> Form Pengajuan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $isSlipGajiActive ? 'text-white fw-bold bg-white bg-opacity-25' : '' ?>" href="<?= site_url('teknisi/pribadi/slip-gaji') ?>">
                                <i class="fas fa-file-invoice-dollar me-2"></i> Slip Gaji
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $isProfilActive ? 'text-white fw-bold bg-white bg-opacity-25' : '' ?>" href="<?= site_url('teknisi/profile') ?>">
                                <i class="fas fa-id-card me-2"></i> Profil
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Logout -->
            <li class="nav-item mt-3">
                <a class="nav-link text-danger py-2 px-3 rounded fw-bold" href="<?= site_url('logout') ?>">
                    <i class="fas fa-sign-out-alt me-2"></i> Keluar
                </a>
            </li>
        </ul>
    </div>
</nav>

<style>
.sidebar .nav-link {
    cursor: pointer !important;
    text-decoration: none !important;
    transition: all 0.2s ease-in-out;
}
.sidebar .nav-link:hover {
    color: #ffffff !important;
    background-color: rgba(255, 255, 255, 0.15) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Pure JS Click Fallback for Sidebar Collapses
    document.querySelectorAll('.sidebar [data-bs-toggle="collapse"], .sidebar [data-toggle="collapse"]').forEach(function(toggler) {
        toggler.addEventListener('click', function(e) {
            var targetId = this.getAttribute('href') || this.getAttribute('data-bs-target') || this.getAttribute('data-target');
            if (targetId && targetId.startsWith('#')) {
                var targetEl = document.querySelector(targetId);
                if (targetEl) {
                    e.preventDefault();
                    if (targetEl.classList.contains('show')) {
                        targetEl.classList.remove('show');
                        this.setAttribute('aria-expanded', 'false');
                    } else {
                        targetEl.classList.add('show');
                        this.setAttribute('aria-expanded', 'true');
                    }
                }
            }
        });
    });
});
</script>