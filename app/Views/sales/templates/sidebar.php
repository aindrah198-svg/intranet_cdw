<?php
$active = $active ?? 'dashboard';
$uri = service('uri');
$segments = $uri->getSegments();
$segment2 = $segments[1] ?? '';
$segment3 = $segments[2] ?? '';

// Active state checks
$isDashboardActive = $active === 'dashboard' || $segment2 === 'dashboard' || $segment2 === '';
$isLeadsActive = $segment2 === 'leads' && $segment3 !== 'pipeline';
$isPipelineActive = $segment2 === 'leads' && $segment3 === 'pipeline';
$isLeadsGroupActive = $isLeadsActive || $isPipelineActive;

$isQuotationCreateActive = $segment2 === 'quotation' && $segment3 === 'create';
$isQuotationHistoryActive = $segment2 === 'quotation' && $segment3 !== 'create';
$isQuotationGroupActive = $isQuotationCreateActive || $isQuotationHistoryActive;

$isDealActive = $segment2 === 'deal' && ($segment3 === '' || $segment3 === 'index');
$isDealInvoiceActive = $segment2 === 'deal' && $segment3 === 'invoice';
$isDealProjectActive = $segment2 === 'deal' && $segment3 === 'project';
$isDealGroupActive = $isDealActive || $isDealInvoiceActive || $isDealProjectActive;

$isLaporanHarianActive = $segment2 === 'laporan' && ($segment3 === '' || $segment3 === 'index');
$isLaporanTargetActive = $segment2 === 'laporan' && $segment3 === 'target';
$isLaporanGroupActive = $isLaporanHarianActive || $isLaporanTargetActive;

$isKontakActive = $segment2 === 'kontak';

$isAbsensiActive = $segment2 === 'pribadi' && $segment3 === 'absensi';
$isTugasActive = $segment2 === 'pribadi' && $segment3 === 'tugas';
$isLaporanKerjaActive = $segment2 === 'pribadi' && $segment3 === 'laporan-harian';
$isPengajuanActive = $segment2 === 'pribadi' && $segment3 === 'pengajuan';
$isSlipGajiActive = $segment2 === 'pribadi' && $segment3 === 'slip-gaji';
$isProfilActive = $segment2 === 'pribadi' && $segment3 === 'profil';
$isPribadiGroupActive = $isAbsensiActive || $isTugasActive || $isLaporanKerjaActive || $isPengajuanActive || $isSlipGajiActive || $isProfilActive;
?>

<!-- Navigasi Sidebar Sales & Marketing -->
<nav class="sidebar text-white" id="sidebar" style="width: 260px; background: #1a237e; min-height: 100vh; position: fixed; top: 0; left: 0; z-index: 1050; overflow-y: auto;">
    <div class="sidebar-header p-3 border-bottom border-secondary text-center">
        <h5 class="mb-0 fw-bold text-white tracking-wide"><i class="fas fa-chart-line text-warning me-2"></i>SALES & MARKETING</h5>
        <small class="text-white-50">Sales Panel</small>
    </div>

    <div class="sidebar-user p-3 border-bottom border-secondary d-flex align-items-center">
        <div class="rounded-circle bg-warning text-dark font-weight-bold d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px; font-weight: 700; font-size: 1.1rem;">
            <?= strtoupper(substr(session()->get('username') ?? 'S', 0, 1)) ?>
        </div>
        <div>
            <div class="fw-bold text-white mb-0"><?= esc(session()->get('username') ?? 'Sales Staff') ?></div>
            <small class="badge bg-warning text-dark">Sales & Marketing</small>
        </div>
    </div>

    <div class="sidebar-menu p-2">
        <ul class="nav flex-column gap-1">
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link text-white py-2 px-3 rounded <?= $isDashboardActive ? 'bg-primary fw-bold active' : '' ?>" href="<?= site_url('sales/dashboard') ?>">
                    <i class="fas fa-tachometer-alt me-2 text-warning"></i> Dashboard
                </a>
            </li>

            <!-- Leads & Pipeline -->
            <li class="nav-item">
                <a class="nav-link text-white py-2 px-3 rounded d-flex justify-content-between align-items-center <?= $isLeadsGroupActive ? 'bg-primary fw-bold' : '' ?>" 
                   data-bs-toggle="collapse" data-toggle="collapse" href="#menuLeads" role="button" aria-expanded="<?= $isLeadsGroupActive ? 'true' : 'false' ?>">
                    <span><i class="fas fa-filter me-2 text-warning"></i> Leads & Pipeline</span>
                    <i class="fas fa-chevron-down small"></i>
                </a>
                <div class="collapse <?= $isLeadsGroupActive ? 'show' : '' ?>" id="menuLeads">
                    <ul class="nav flex-column ps-3 py-1 gap-1">
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $isLeadsActive ? 'text-white fw-bold bg-secondary bg-opacity-25' : '' ?>" href="<?= site_url('sales/leads') ?>">
                                <i class="fas fa-list me-2"></i> Daftar Leads
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $isPipelineActive ? 'text-white fw-bold bg-secondary bg-opacity-25' : '' ?>" href="<?= site_url('sales/leads/pipeline') ?>">
                                <i class="fas fa-columns me-2"></i> Pipeline (Kanban)
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Quotation -->
            <li class="nav-item">
                <a class="nav-link text-white py-2 px-3 rounded d-flex justify-content-between align-items-center <?= $isQuotationGroupActive ? 'bg-primary fw-bold' : '' ?>" 
                   data-bs-toggle="collapse" data-toggle="collapse" href="#menuQuotation" role="button" aria-expanded="<?= $isQuotationGroupActive ? 'true' : 'false' ?>">
                    <span><i class="fas fa-file-invoice-dollar me-2 text-warning"></i> Quotation</span>
                    <i class="fas fa-chevron-down small"></i>
                </a>
                <div class="collapse <?= $isQuotationGroupActive ? 'show' : '' ?>" id="menuQuotation">
                    <ul class="nav flex-column ps-3 py-1 gap-1">
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $isQuotationCreateActive ? 'text-white fw-bold bg-secondary bg-opacity-25' : '' ?>" href="<?= site_url('sales/quotation/create') ?>">
                                <i class="fas fa-plus-circle me-2"></i> Buat Quotation
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $isQuotationHistoryActive ? 'text-white fw-bold bg-secondary bg-opacity-25' : '' ?>" href="<?= site_url('sales/quotation') ?>">
                                <i class="fas fa-history me-2"></i> Riwayat Quotation
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Closing & Deal -->
            <li class="nav-item">
                <a class="nav-link text-white py-2 px-3 rounded d-flex justify-content-between align-items-center <?= $isDealGroupActive ? 'bg-primary fw-bold' : '' ?>" 
                   data-bs-toggle="collapse" data-toggle="collapse" href="#menuDeal" role="button" aria-expanded="<?= $isDealGroupActive ? 'true' : 'false' ?>">
                    <span><i class="fas fa-handshake me-2 text-warning"></i> Closing & Deal</span>
                    <i class="fas fa-chevron-down small"></i>
                </a>
                <div class="collapse <?= $isDealGroupActive ? 'show' : '' ?>" id="menuDeal">
                    <ul class="nav flex-column ps-3 py-1 gap-1">
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $isDealActive ? 'text-white fw-bold bg-secondary bg-opacity-25' : '' ?>" href="<?= site_url('sales/deal') ?>">
                                <i class="fas fa-check-circle me-2"></i> Closing Deal
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Laporan Penjualan -->
            <li class="nav-item">
                <a class="nav-link text-white py-2 px-3 rounded d-flex justify-content-between align-items-center <?= $isLaporanGroupActive ? 'bg-primary fw-bold' : '' ?>" 
                   data-bs-toggle="collapse" data-toggle="collapse" href="#menuLaporan" role="button" aria-expanded="<?= $isLaporanGroupActive ? 'true' : 'false' ?>">
                    <span><i class="fas fa-chart-bar me-2 text-warning"></i> Laporan Penjualan</span>
                    <i class="fas fa-chevron-down small"></i>
                </a>
                <div class="collapse <?= $isLaporanGroupActive ? 'show' : '' ?>" id="menuLaporan">
                    <ul class="nav flex-column ps-3 py-1 gap-1">
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $isLaporanHarianActive ? 'text-white fw-bold bg-secondary bg-opacity-25' : '' ?>" href="<?= site_url('sales/laporan') ?>">
                                <i class="fas fa-calendar-alt me-2"></i> Laporan Harian/Mingguan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $isLaporanTargetActive ? 'text-white fw-bold bg-secondary bg-opacity-25' : '' ?>" href="<?= site_url('sales/laporan/target') ?>">
                                <i class="fas fa-bullseye me-2"></i> Target vs Realisasi
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Kontak Klien -->
            <li class="nav-item">
                <a class="nav-link text-white py-2 px-3 rounded <?= $isKontakActive ? 'bg-primary fw-bold active' : '' ?>" href="<?= site_url('sales/kontak') ?>">
                    <i class="fas fa-address-book me-2 text-warning"></i> Kontak Klien
                </a>
            </li>

            <hr class="border-secondary my-2">

            <!-- Menu Pribadi -->
            <li class="nav-item">
                <a class="nav-link text-white py-2 px-3 rounded d-flex justify-content-between align-items-center <?= $isPribadiGroupActive ? 'bg-primary fw-bold' : '' ?>" 
                   data-bs-toggle="collapse" data-toggle="collapse" href="#menuPribadi" role="button" aria-expanded="<?= $isPribadiGroupActive ? 'true' : 'false' ?>">
                    <span><i class="fas fa-user-clock me-2 text-warning"></i> Menu Pribadi</span>
                    <i class="fas fa-chevron-down small"></i>
                </a>
                <div class="collapse <?= $isPribadiGroupActive ? 'show' : '' ?>" id="menuPribadi">
                    <ul class="nav flex-column ps-3 py-1 gap-1">
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $isAbsensiActive ? 'text-white fw-bold bg-secondary bg-opacity-25' : '' ?>" href="<?= site_url('sales/pribadi/absensi') ?>">
                                <i class="fas fa-user-check me-2"></i> Absensi Saya
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $isTugasActive ? 'text-white fw-bold bg-secondary bg-opacity-25' : '' ?>" href="<?= site_url('sales/pribadi/tugas') ?>">
                                <i class="fas fa-tasks me-2"></i> Tugas Saya
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $isLaporanKerjaActive ? 'text-white fw-bold bg-secondary bg-opacity-25' : '' ?>" href="<?= site_url('sales/pribadi/laporan-harian') ?>">
                                <i class="fas fa-clipboard-list me-2"></i> Laporan Kerja Harian
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $isPengajuanActive ? 'text-white fw-bold bg-secondary bg-opacity-25' : '' ?>" href="<?= site_url('sales/pribadi/pengajuan') ?>">
                                <i class="fas fa-paper-plane me-2"></i> Form Pengajuan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $isSlipGajiActive ? 'text-white fw-bold bg-secondary bg-opacity-25' : '' ?>" href="<?= site_url('sales/pribadi/slip-gaji') ?>">
                                <i class="fas fa-file-invoice-dollar me-2"></i> Slip Gaji
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 py-1.5 px-3 rounded <?= $isProfilActive ? 'text-white fw-bold bg-secondary bg-opacity-25' : '' ?>" href="<?= site_url('sales/pribadi/profil') ?>">
                                <i class="fas fa-id-card me-2"></i> Profil
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Keluar -->
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
.sidebar .collapse .nav-link:hover {
    color: #ffffff !important;
    background-color: rgba(255, 255, 255, 0.25) !important;
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