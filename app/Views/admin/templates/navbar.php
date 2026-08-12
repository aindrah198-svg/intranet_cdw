<?php
// app/Views/admin/templates/navbar.php
$title    = $title    ?? 'Dashboard';
$subtitle = $subtitle ?? date('l, d F Y');
$user     = $user     ?? ['name' => 'Administrator', 'role' => 'admin'];
?>
<div class="main-content">
    <nav style="
        height: var(--header-height);
        padding: 0 25px;
        background: rgba(255,255,255,0.93);
        backdrop-filter: blur(12px);
        border-bottom: 1px solid rgba(30,60,114,0.12);
        position: sticky; top: 0; z-index: 900;
        display: flex; align-items: center; justify-content: space-between;
        box-shadow: 0 2px 10px rgba(30,60,114,0.06);
    ">
        <div style="display:flex;align-items:center;gap:12px;">
            <button type="button" class="btn btn-light border-0 shadow-sm rounded-circle d-flex align-items-center justify-content-center" id="sidebarToggleBtn" onclick="toggleSidebar()" style="width:38px;height:38px;color:#1e3c72;cursor:pointer;flex-shrink:0;">
                <i class="fas fa-bars fs-6"></i>
            </button>
            <div>
                <h1 style="font-size:1.2rem;font-weight:700;margin:0;color:#1e3c72;"><?= htmlspecialchars($title) ?></h1>
                <small style="color:#999;font-size:0.78rem;">
                    <i class="far fa-calendar-alt me-1"></i><?= htmlspecialchars($subtitle) ?>
                    &nbsp;•&nbsp;
                    <i class="far fa-clock me-1"></i><span id="liveClock"><?= date('H:i:s') ?></span>
                </small>
            </div>
        </div>

        <div style="display:flex;align-items:center;gap:14px;">
            <span style="background:linear-gradient(135deg,#1e3c72,#2a5298);color:white;padding:4px 12px;border-radius:20px;font-size:0.75rem;font-weight:600;">
                <i class="fas fa-user-shield me-1"></i> Admin Panel
            </span>

            <div class="dropdown">
                <button class="btn btn-link p-0 d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" style="text-decoration:none;color:#1e3c72;">
                    <div style="width:36px;height:36px;background:linear-gradient(135deg,#1e3c72,#2563eb);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:0.9rem;font-weight:700;">
                        <?= strtoupper(substr($user['name'] ?? 'A', 0, 1)) ?>
                    </div>
                    <div style="text-align:left;line-height:1.2;">
                        <div style="font-size:0.82rem;font-weight:600;color:#1e3c72;"><?= htmlspecialchars($user['name'] ?? 'Admin') ?></div>
                        <div style="font-size:0.7rem;color:#2563eb;text-transform:uppercase;">Administrator</div>
                    </div>
                    <i class="fas fa-chevron-down" style="font-size:0.7rem;color:#2563eb;"></i>
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

    <div class="content-wrapper">