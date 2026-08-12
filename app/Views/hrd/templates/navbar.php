<?php
// app/Views/hrd/templates/navbar.php
$title = $title ?? 'HRD Dashboard';
$subtitle = $subtitle ?? date('l, d F Y');
$user = $user ?? ['name' => 'HRD Manager', 'role' => 'hrd'];
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
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(10px);
    ">
        <div class="container-fluid p-0">
            <!-- Left Side -->
            <div class="d-flex align-items-center">
                <div class="d-flex flex-column">
                    <h1 class="page-title mb-0" style="font-size: 1.2rem; font-weight: 700; color: #1e3c72;">
                        <?= htmlspecialchars($title) ?>
                    </h1>
                    <div class="d-flex align-items-center gap-2">
                        <small class="text-muted"><i class="far fa-calendar-alt me-1"></i><?= htmlspecialchars($subtitle) ?></small>
                        <span class="text-muted">•</span>
                        <small class="text-muted"><i class="far fa-clock me-1"></i><span id="liveClock"><?= date('H:i:s') ?></span></small>
                    </div>
                </div>
            </div>

            <!-- Right Side -->
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-primary px-3 py-2 rounded-pill font-weight-bold">
                    <i class="fas fa-users-cog me-1"></i> HRD Management
                </span>

                <div class="dropdown">
                    <button class="btn p-0 d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" style="border: none; background: none;">
                        <div class="user-avatar text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #1e3c72, #2a5298); font-weight: 600;">
                            <?= strtoupper(substr($user['name'] ?? 'H', 0, 1)) ?>
                        </div>
                        <div class="d-none d-md-block text-start" style="line-height: 1.2;">
                            <strong style="font-size: 0.88rem; color: #1e3c72;"><?= htmlspecialchars($user['name'] ?? 'HRD Manager') ?></strong><br>
                            <small class="text-muted" style="font-size: 0.72rem; text-transform: uppercase;">HRD Department</small>
                        </div>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius: 10px;">
                        <a class="dropdown-item" href="<?= base_url('hrd/profile') ?>"><i class="fas fa-user me-2"></i> Profil Saya</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt me-2"></i> Keluar</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <div class="container-fluid mt-4 px-4">
        <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 10px;">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 10px;">
            <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
