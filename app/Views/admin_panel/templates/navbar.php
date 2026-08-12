<?php
// app/Views/admin_panel/templates/navbar.php
$title    = $title    ?? 'Dashboard';
$subtitle = $subtitle ?? date('l, d F Y');
$user     = $user     ?? ['name' => 'Administrator', 'role' => 'admin'];
?>
<!-- Main Content Area -->
<div class="main-content">
    <!-- Top Navbar -->
    <nav style="
        height: var(--header-height);
        padding: 0 25px;
        background: rgba(255,255,255,0.93);
        backdrop-filter: blur(12px);
        border-bottom: 1px solid rgba(123,31,162,0.15);
        position: sticky; top: 0; z-index: 900;
        display: flex; align-items: center; justify-content: space-between;
        box-shadow: 0 2px 10px rgba(123,31,162,0.08);
    ">
        <!-- Left: Title -->
        <div>
            <h1 style="font-size:1.2rem;font-weight:700;margin:0;color:#4a148c;"><?= htmlspecialchars($title) ?></h1>
            <small style="color:#999;font-size:0.78rem;">
                <i class="far fa-calendar-alt me-1"></i><?= htmlspecialchars($subtitle) ?>
                &nbsp;•&nbsp;
                <i class="far fa-clock me-1"></i><span id="liveClock"><?= date('H:i:s') ?></span>
            </small>
        </div>

        <!-- Right: User & Actions -->
        <div style="display:flex;align-items:center;gap:14px;">
            <!-- Badge Admin -->
            <span style="background:linear-gradient(135deg,#4a148c,#7b1fa2);color:white;padding:4px 12px;border-radius:20px;font-size:0.75rem;font-weight:600;">
                <i class="fas fa-user-shield me-1"></i> Admin Panel
            </span>

            <!-- Notifications -->
            <div class="dropdown position-relative">
                <button class="btn btn-link p-0" type="button" data-bs-toggle="dropdown" style="color:#7b1fa2;text-decoration:none;position:relative;">
                    <i class="fas fa-bell fa-lg"></i>
                    <span style="position:absolute;top:-5px;right:-5px;background:#ff5722;color:white;border-radius:50%;width:16px;height:16px;font-size:0.6rem;display:flex;align-items:center;justify-content:center;font-weight:700;">3</span>
                </button>
                <div class="dropdown-menu dropdown-menu-end" style="min-width:280px;border:none;box-shadow:0 10px 30px rgba(0,0,0,0.15);border-radius:12px;padding:0;overflow:hidden;">
                    <div style="padding:14px 16px;background:linear-gradient(135deg,#4a148c,#7b1fa2);color:white;">
                        <strong style="font-size:0.9rem;">Notifikasi</strong>
                        <span style="font-size:0.75rem;opacity:0.8;float:right;">3 baru</span>
                    </div>
                    <div style="padding:12px 16px;border-bottom:1px solid #f3e5f5;">
                        <small class="text-muted">Belum ada notifikasi baru.</small>
                    </div>
                </div>
            </div>

            <!-- User Dropdown -->
            <div class="dropdown">
                <button class="btn btn-link p-0 d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" style="text-decoration:none;color:#4a148c;">
                    <div style="width:36px;height:36px;background:linear-gradient(135deg,#4a148c,#9c27b0);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:0.9rem;font-weight:700;">
                        <?= strtoupper(substr($user['name'] ?? 'A', 0, 1)) ?>
                    </div>
                    <div style="text-align:left;line-height:1.2;">
                        <div style="font-size:0.82rem;font-weight:600;color:#4a148c;"><?= htmlspecialchars($user['name'] ?? 'Admin') ?></div>
                        <div style="font-size:0.7rem;color:#9c27b0;text-transform:uppercase;">Administrator</div>
                    </div>
                    <i class="fas fa-chevron-down" style="font-size:0.7rem;color:#9c27b0;"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" style="border:none;box-shadow:0 8px 25px rgba(0,0,0,0.12);border-radius:10px;padding:8px;">
                    <a class="dropdown-item" href="<?= base_url('admin/profil') ?>" style="border-radius:6px;padding:8px 14px;font-size:0.85rem;">
                        <i class="fas fa-id-badge me-2 text-purple"></i> Profil Saya
                    </a>
                    <div class="dropdown-divider" style="margin:4px 0;"></div>
                    <a class="dropdown-item text-danger" href="<?= base_url('logout') ?>" style="border-radius:6px;padding:8px 14px;font-size:0.85rem;">
                        <i class="fas fa-sign-out-alt me-2"></i> Keluar
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show mx-4 mt-3" role="alert" style="border-radius:10px;">
        <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show mx-4 mt-3" role="alert" style="border-radius:10px;">
        <i class="fas fa-exclamation-circle me-2"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Content Wrapper starts after navbar -->
    <div class="content-wrapper">

    <script>
    // Live Clock
    setInterval(() => {
        const now = new Date();
        const t = now.toLocaleTimeString('id-ID');
        const el = document.getElementById('liveClock');
        if (el) el.textContent = t;
    }, 1000);
    </script>
