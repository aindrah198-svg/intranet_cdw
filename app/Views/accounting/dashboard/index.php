<?= $this->include('accounting/templates/header') ?>
<?= $this->include('accounting/templates/sidebar') ?>
<?= $this->include('accounting/templates/navbar') ?>

<style>
    /* Styling Dashboard Sederhana & Modern */
    .welcome-card-modern {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        border-radius: 16px;
        padding: 24px 28px;
        color: white;
        box-shadow: 0 8px 24px rgba(30, 60, 114, 0.15);
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }
    .welcome-card-modern::after {
        content: '';
        position: absolute;
        top: -30px;
        right: -30px;
        width: 160px;
        height: 160px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 50%;
        pointer-events: none;
    }
    
    .stat-card-modern {
        background: #ffffff;
        border-radius: 14px;
        padding: 20px;
        border: 1px solid rgba(0, 0, 0, 0.06);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        transition: all 0.25s ease;
        height: 100%;
    }
    .stat-card-modern:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 22px rgba(0, 0, 0, 0.08);
    }
    
    .icon-badge-modern {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-bottom: 14px;
    }
    .badge-blue { background: rgba(37, 99, 235, 0.1); color: #2563eb; }
    .badge-green { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .badge-purple { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }
    .badge-amber { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }

    .shortcut-card-modern {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        text-align: center;
        text-decoration: none !important;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 95px;
        transition: all 0.25s ease;
    }
    .shortcut-card-modern:hover {
        border-color: #1e3c72;
        background: rgba(30, 60, 114, 0.03);
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(30, 60, 114, 0.08);
    }
</style>

<div class="container-fluid py-4">
    <!-- Header Page Title -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1"><i class="fas fa-calculator text-primary me-2"></i>Dashboard Accounting</h3>
            <p class="text-muted small mb-0"><?= esc($subtitle ?? date('l, d F Y')) ?> — Ringkasan Sistem Keuangan Perusahaan</p>
        </div>
    </div>

    <!-- Welcome Card -->
    <div class="welcome-card-modern">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h4 class="fw-bold mb-1.5"><i class="fas fa-user-circle me-2"></i>Selamat Datang, <?= esc($karyawan['nama_panggilan'] ?? $user['name'] ?? 'Accounting Staff') ?>!</h4>
                <p class="mb-0 text-white-50" style="font-size: 0.93rem;">Kelola Modul Pembukuan, Laporan Keuangan, dan Pengajuan Transaksi Perusahaan secara terintegrasi.</p>
            </div>
            <div class="d-flex align-items-center gap-2 bg-white bg-opacity-10 px-3 py-2 rounded-3 border border-white border-opacity-25">
                <i class="fas fa-id-badge fs-5"></i>
                <div class="text-end">
                    <div class="fw-bold text-xs text-uppercase opacity-75">NIK Staff</div>
                    <div class="fw-bold small"><?= esc($karyawan['nik'] ?? 'AC-001') ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Statistics Row -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card-modern">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="icon-badge-modern badge-amber">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <span class="badge bg-warning bg-opacity-10 text-warning px-2.5 py-1 rounded-pill small fw-semibold">
                        Pending
                    </span>
                </div>
                <h3 class="fw-bold text-dark mb-1"><?= number_format($totalKasbon) ?></h3>
                <div class="text-muted small fw-semibold">Kasbon Menunggu Review</div>
                <small class="text-muted text-xs mt-1 d-block"><i class="fas fa-clock me-1 text-warning"></i> Perlu verifikasi direktur</small>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card-modern">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="icon-badge-modern badge-blue">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-2.5 py-1 rounded-pill small fw-semibold">
                        PR Pembelian
                    </span>
                </div>
                <h3 class="fw-bold text-dark mb-1"><?= number_format($totalPembelian) ?></h3>
                <div class="text-muted small fw-semibold">Pengajuan Pembelian (PR)</div>
                <small class="text-muted text-xs mt-1 d-block"><i class="fas fa-info-circle me-1 text-primary"></i> Memerlukan pencatatan</small>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card-modern">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="icon-badge-modern badge-green">
                        <i class="fas fa-users"></i>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success px-2.5 py-1 rounded-pill small fw-semibold">
                        Aktif
                    </span>
                </div>
                <h3 class="fw-bold text-dark mb-1"><?= number_format($totalKaryawan) ?></h3>
                <div class="text-muted small fw-semibold">Karyawan Terdaftar</div>
                <small class="text-muted text-xs mt-1 d-block"><i class="fas fa-check-circle me-1 text-success"></i> Data karyawan aktif</small>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card-modern">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="icon-badge-modern badge-purple">
                        <i class="fas fa-list-alt"></i>
                    </div>
                    <span class="badge bg-secondary bg-opacity-10 text-dark px-2.5 py-1 rounded-pill small fw-semibold">
                        Master COA
                    </span>
                </div>
                <h3 class="fw-bold text-dark mb-1"><?= number_format($totalCOA) ?></h3>
                <div class="text-muted small fw-semibold">Daftar Akun (COA)</div>
                <small class="text-muted text-xs mt-1 d-block"><i class="fas fa-database me-1 text-purple"></i> Bagan akun pembukuan</small>
            </div>
        </div>
    </div>

    <!-- Quick Access Section -->
    <div class="bg-white rounded-3 p-4 border shadow-sm mb-4">
        <h5 class="fw-bold text-dark mb-3"><i class="fas fa-bolt text-warning me-2"></i>Akses Cepat Modul Accounting</h5>
        
        <div class="row g-3">
            <?php
            $quickMenus = [
                ['href' => base_url('accounting/pembelian'), 'icon' => 'fas fa-shopping-cart', 'label' => 'Tracking Pembelian (PR)', 'color' => '#1e3c72'],
                ['href' => base_url('accounting/pembukuan/daftar-akun'), 'icon' => 'fas fa-list-alt', 'label' => 'Daftar Akun (COA)', 'color' => '#2563eb'],
                ['href' => base_url('accounting/pembukuan/jurnal-umum'), 'icon' => 'fas fa-file-invoice', 'label' => 'Jurnal Umum', 'color' => '#059669'],
                ['href' => base_url('accounting/pembukuan/buku-besar'), 'icon' => 'fas fa-book-open', 'label' => 'Buku Besar', 'color' => '#d97706'],
                ['href' => base_url('accounting/laporan-keuangan/laporan/laba-rugi'), 'icon' => 'fas fa-chart-line', 'label' => 'Laporan Laba Rugi', 'color' => '#dc2626'],
                ['href' => base_url('accounting/laporan-keuangan/laporan/neraca'), 'icon' => 'fas fa-balance-scale', 'label' => 'Laporan Neraca', 'color' => '#7c3aed'],
                ['href' => base_url('accounting/laporan-keuangan/laporan/arus-kas'), 'icon' => 'fas fa-money-bill-wave', 'label' => 'Laporan Arus Kas', 'color' => '#0284c7'],
                ['href' => base_url('accounting/pribadi/absensi'), 'icon' => 'fas fa-calendar-check', 'label' => 'Absensi Saya', 'color' => '#0d9488'],
                ['href' => base_url('accounting/pribadi/profil'), 'icon' => 'fas fa-user-cog', 'label' => 'Profil Saya', 'color' => '#475569'],
            ];
            foreach ($quickMenus as $qm): ?>
            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                <a href="<?= $qm['href'] ?>" class="shortcut-card-modern">
                    <i class="<?= $qm['icon'] ?>" style="font-size: 1.35rem; color: <?= $qm['color'] ?>; margin-bottom: 6px;"></i>
                    <span style="font-size: 0.8rem; font-weight: 600; color: #334155; line-height: 1.2;"><?= $qm['label'] ?></span>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?= $this->include('accounting/templates/footer') ?>