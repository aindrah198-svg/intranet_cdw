<?php
// app/Views/accounting/templates/navbar.php
$title = $title ?? 'Dashboard';
$subtitle = $subtitle ?? date('l, d F Y');
$user = $user ?? ['name' => 'Accounting Staff', 'role' => 'accounting'];

// Deteksi apakah di halaman COA
$uri = service('uri');
$segments = $uri->getSegments();
$isCoaPage = (isset($segments[1]) && $segments[1] === 'coa') || ($active ?? '') === 'coa';

// TAMBAHKAN INI - Inisialisasi variabel untuk financial stats
$showFinancialStats = $showFinancialStats ?? true;
?>
<!-- Main Content Area -->
<div class="main-content">
    <!-- Top Navigation -->
    <nav class="top-navbar navbar navbar-expand-lg glass-effect" style="
        height: var(--header-height);
        padding: 0 25px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.8);
        position: sticky;
        top: 0;
        z-index: 1000;
        backdrop-filter: blur(10px);
    ">
        <div class="container-fluid p-0">
            <!-- Left Side: Page Title and Breadcrumb -->
            <div class="d-flex align-items-center">
                <!-- Sidebar Toggle for Mobile -->
                <button class="btn btn-accounting-outline d-lg-none me-3" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                
                <!-- Page Title -->
                <div class="d-flex flex-column">
                    <h1 class="page-title mb-0">
                        <span class="text-gradient-accounting"><?= htmlspecialchars($title) ?></span>
                    </h1>
                    <div class="d-flex align-items-center gap-2">
                        <small class="text-muted">
                            <i class="far fa-calendar-alt me-1"></i>
                            <?= htmlspecialchars($subtitle) ?>
                        </small>
                        <span class="text-muted">•</span>
                        <small class="text-muted">
                            <i class="far fa-clock me-1"></i>
                            <span id="liveClock"><?= date('H:i:s') ?></span>
                        </small>
                    </div>
                </div>
            </div>

            <!-- Right Side: User Info, Notifications, etc. -->
            <div class="d-flex align-items-center gap-3">
                <!-- Quick Actions -->
                <?php if (!$isCoaPage): ?>
                <div class="d-none d-md-flex gap-2">
                    <button class="btn btn-accounting-outline btn-sm" data-bs-toggle="modal" data-bs-target="#quickJournalModal">
                        <i class="fas fa-plus"></i> Jurnal Cepat
                    </button>
                    <button class="btn btn-accounting btn-sm" onclick="window.location.href='<?= base_url('accounting/dashboard') ?>'">
                        <i class="fas fa-home"></i> Dashboard
                    </button>
                </div>
                <?php endif; ?>

                <!-- Search Bar -->
                <div class="d-none d-lg-block">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" placeholder="<?= $isCoaPage ? 'Cari akun...' : 'Cari transaksi...' ?>" 
                               style="background: rgba(255,255,255,0.7); border-radius: var(--border-radius-sm);">
                    </div>
                </div>

                <!-- Notifications -->
                <div class="dropdown position-relative">
                    <button class="btn btn-link text-dark p-0" type="button" data-bs-toggle="dropdown" 
                            style="text-decoration: none;">
                        <i class="fas fa-bell fa-lg"></i>
                        <span class="notification-badge">5</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-0" style="
                        min-width: 320px;
                        border: none;
                        box-shadow: var(--shadow-lg);
                        border-radius: var(--border-radius-sm);
                        overflow: hidden;
                        margin-top: 10px;
                    ">
                        <div class="p-3 border-bottom">
                            <h6 class="mb-0">Notifikasi</h6>
                            <small class="text-muted">5 notifikasi belum dibaca</small>
                        </div>
                        <div style="max-height: 300px; overflow-y: auto;">
                            <!-- Notification Items -->
                            <?php if ($isCoaPage): ?>
                            <!-- Notifikasi khusus COA -->
                            <a href="#" class="dropdown-item p-3 border-bottom">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px;">
                                            <i class="fas fa-list-alt"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Chart of Accounts</h6>
                                        <p class="mb-0 text-muted" style="font-size: 0.85rem;">
                                            Ada 5 akun aktif di sistem
                                        </p>
                                        <small class="text-muted">Baru saja</small>
                                    </div>
                                </div>
                            </a>
                            <?php else: ?>
                            <!-- Notifikasi regular -->
                            <a href="#" class="dropdown-item p-3 border-bottom">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <div class="bg-gradient-accounting text-white rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px;">
                                            <i class="fas fa-file-invoice-dollar"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Jurnal Belum Diposting</h6>
                                        <p class="mb-0 text-muted" style="font-size: 0.85rem;">
                                            3 jurnal menunggu posting
                                        </p>
                                        <small class="text-muted">1 jam yang lalu</small>
                                    </div>
                                </div>
                            </a>
                            <?php endif; ?>
                            <a href="#" class="dropdown-item p-3">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px;">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Rekonsiliasi Tertunda</h6>
                                        <p class="mb-0 text-muted" style="font-size: 0.85rem;">
                                            Rekonsiliasi bank bulan lalu belum selesai
                                        </p>
                                        <small class="text-muted">1 hari yang lalu</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="p-2 border-top">
                            <a href="#" class="btn btn-accounting-outline w-100 btn-sm">
                                <i class="fas fa-eye me-1"></i> Lihat Semua
                            </a>
                        </div>
                    </div>
                </div>

                <!-- User Profile -->
                <div class="dropdown">
                    <button class="btn p-0 d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" 
                            style="border: none; background: none;">
                        <div class="position-relative">
                            <div class="user-avatar bg-gradient-accounting text-white d-flex align-items-center justify-content-center" 
                                 style="width: 45px; height: 45px; border-radius: 50%; font-weight: 600; font-size: 1.1rem;">
                                <?= strtoupper(substr($user['name'] ?? 'A', 0, 1)) ?>
                            </div>
                            <div class="position-absolute bottom-0 end-0 bg-success rounded-circle border border-2 border-white" 
                                 style="width: 12px; height: 12px;"></div>
                        </div>
                        <div class="d-none d-md-block text-start" style="line-height: 1.2;">
                            <strong style="font-size: 0.9rem;"><?= htmlspecialchars($user['name'] ?? 'Accounting Staff') ?></strong><br>
                            <small class="text-muted" style="font-size: 0.75rem;">
                                <?= ucfirst($user['role'] ?? 'accounting') ?>
                            </small>
                        </div>
                        <i class="fas fa-chevron-down text-muted"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" style="
                        min-width: 200px;
                        border: none;
                        box-shadow: var(--shadow-lg);
                        border-radius: var(--border-radius-sm);
                        margin-top: 10px;
                    ">
                        <div class="p-3 border-bottom">
                            <h6 class="mb-0"><?= htmlspecialchars($user['name'] ?? 'Accounting Staff') ?></h6>
                            <small class="text-muted"><?= ucfirst($user['role'] ?? 'accounting') ?></small>
                        </div>
                        <a class="dropdown-item" href="<?= base_url('accounting/pribadi/profil') ?>">
                            <i class="fas fa-user me-2"></i> Profil Saya
                        </a>
                        <a class="dropdown-item" href="<?= base_url('accounting/settings') ?>">
                            <i class="fas fa-cog me-2"></i> Pengaturan
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?= base_url('accounting/help') ?>">
                            <i class="fas fa-question-circle me-2"></i> Bantuan
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" href="<?= base_url('logout') ?>">
                            <i class="fas fa-sign-out-alt me-2"></i> Keluar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>



    <!-- Main Content Container -->
    <div class="container-fluid mt-4 px-4">
        <!-- Flash Messages -->
        <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success fade-in" role="alert" style="border-left: 4px solid #28a745;">
            <div class="d-flex align-items-center">
                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                     style="width: 32px; height: 32px;">
                    <i class="fas fa-check"></i>
                </div>
                <div>
                    <h6 class="mb-1">Sukses!</h6>
                    <p class="mb-0"><?= session()->getFlashdata('success') ?></p>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger fade-in" role="alert" style="border-left: 4px solid #dc3545;">
            <div class="d-flex align-items-center">
                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                     style="width: 32px; height: 32px;">
                    <i class="fas fa-exclamation"></i>
                </div>
                <div>
                    <h6 class="mb-1">Error!</h6>
                    <p class="mb-0"><?= session()->getFlashdata('error') ?></p>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if(session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger fade-in" role="alert" style="border-left: 4px solid #dc3545;">
            <div class="d-flex">
                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" 
                     style="width: 32px; height: 32px;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <h6 class="mb-2">Harap perbaiki kesalahan berikut:</h6>
                    <ul class="mb-0 ps-3" style="list-style: disc;">
                    <?php foreach(session()->getFlashdata('errors') as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        <?php endif; ?>