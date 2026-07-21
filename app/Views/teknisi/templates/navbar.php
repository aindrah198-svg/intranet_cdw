<?php
// app/Views/teknisi/templates/navbar.php
$title = $title ?? 'Dashboard';
$subtitle = $subtitle ?? date('l, d F Y');
$user = $user ?? ['name' => 'Teknisi', 'role' => 'teknisi'];
?>

<!-- Main Content Area -->
<div class="main-content">
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <!-- Mobile Toggle -->
            <button class="navbar-toggler d-md-none" type="button" onclick="toggleSidebar()">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Page Title -->
            <div class="d-flex align-items-center">
                <div>
                    <h5 class="mb-1" style="color: var(--dark-color); font-weight: 600;">
                        <?= htmlspecialchars($title) ?>
                    </h5>
                    <small class="text-muted" style="font-size: 0.9rem;">
                        <i class="far fa-calendar-alt me-1"></i><?= htmlspecialchars($subtitle) ?>
                    </small>
                </div>
            </div>
            
            <!-- Right Side -->
            <div class="d-flex align-items-center">
                <!-- Notifications -->
                <div class="dropdown me-3">
                    <a href="#" class="nav-link p-0" data-bs-toggle="dropdown">
                        <i class="fas fa-bell fa-lg text-muted"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                              style="font-size: 0.6rem;">
                            3
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" style="min-width: 300px;">
                        <h6 class="dropdown-header">Notifikasi</h6>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex">
                                <i class="fas fa-info-circle text-info me-2 mt-1"></i>
                                <div>
                                    <small>Pesan baru dari admin</small>
                                    <div class="text-muted" style="font-size: 0.8rem;">5 menit lalu</div>
                                </div>
                            </div>
                        </a>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex">
                                <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                <div>
                                    <small>Cuti Anda disetujui</small>
                                    <div class="text-muted" style="font-size: 0.8rem;">2 jam lalu</div>
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-center" href="#">
                            Lihat semua notifikasi
                        </a>
                    </div>
                </div>
                
                <!-- User Menu -->
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" 
                       data-bs-toggle="dropdown">
                        <div class="user-avatar me-2" style="
                            width: 40px;
                            height: 40px;
                            background: linear-gradient(45deg, var(--primary-color), #224abe);
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            color: white;
                            font-weight: bold;
                            font-size: 1rem;
                        ">
                            <?= strtoupper(substr($user['name'] ?? 'T', 0, 1)) ?>
                        </div>
                        <div class="d-none d-md-block">
                            <span class="fw-bold"><?= htmlspecialchars($user['name'] ?? 'Teknisi') ?></span><br>
                            <small class="text-muted"><?= ucfirst($user['role'] ?? 'teknisi') ?></small>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <div class="dropdown-header">
                            <strong><?= htmlspecialchars($user['name'] ?? 'Teknisi') ?></strong><br>
                            <small class="text-muted"><?= htmlspecialchars($user['email'] ?? 'teknisi@cdw.com') ?></small>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?= base_url('teknisi/profile') ?>">
                            <i class="fas fa-user me-2"></i> Profile
                        </a>
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-cog me-2"></i> Settings
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" href="<?= base_url('logout') ?>">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    
    <?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    
    <?php if(session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <ul class="mb-0" style="margin-left: 20px;">
        <?php foreach(session()->getFlashdata('errors') as $error): ?>
            <li><?= $error ?></li>
        <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>