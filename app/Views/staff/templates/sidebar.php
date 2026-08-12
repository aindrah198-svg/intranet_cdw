<?php
$active = $active ?? 'dashboard';
?>

<!-- Sidebar -->
<nav class="sidebar">
    <div class="sidebar-brand">
        <h5 class="mb-0 text-white fw-bold">
            <i class="fas fa-city me-2 text-primary"></i>
            CDW ENGINEERING
        </h5>
        <small class="text-white-50">Staff Panel</small>
    </div>
    
    <div class="sidebar-user">
        <div class="sidebar-user-avatar">
            <?= strtoupper(substr($user['name'] ?? 'S', 0, 1)) ?>
        </div>
        <div class="sidebar-user-name"><?= htmlspecialchars($user['name'] ?? 'Staff') ?></div>
        <div class="sidebar-user-role"><?= htmlspecialchars($user['jabatan'] ?? 'Staff') ?> (<?= htmlspecialchars($user['divisi'] ?? 'General') ?>)</div>
    </div>
    
    <div class="sidebar-menu" style="padding: 15px 0;">
        <ul class="nav flex-column">
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link <?= $active == 'dashboard' ? 'active' : '' ?>" href="<?= base_url('staff/dashboard') ?>">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
            </li>

            <!-- Absensi -->
            <li class="nav-item">
                <a class="nav-link <?= in_array($active, ['absensi_checkin', 'absensi_riwayat']) ? '' : 'collapsed' ?>" href="#menuAbsensi" data-bs-toggle="collapse" role="button" aria-expanded="<?= in_array($active, ['absensi_checkin', 'absensi_riwayat']) ? 'true' : 'false' ?>">
                    <i class="fas fa-fingerprint me-2"></i> Absensi <i class="fas fa-chevron-down ms-auto small"></i>
                </a>
                <div class="collapse <?= in_array($active, ['absensi_checkin', 'absensi_riwayat']) ? 'show' : '' ?>" id="menuAbsensi">
                    <ul class="nav flex-column ps-3 small">
                        <li class="nav-item">
                            <a class="nav-link py-1 <?= $active == 'absensi_checkin' ? 'active text-primary fw-bold' : '' ?>" href="<?= base_url('staff/absensi') ?>">
                                <i class="fas fa-clock me-2"></i> Absen Masuk/Pulang
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link py-1 <?= $active == 'absensi_riwayat' ? 'active text-primary fw-bold' : '' ?>" href="<?= base_url('staff/absensi/riwayat') ?>">
                                <i class="fas fa-history me-2"></i> Riwayat Absensi
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Tugas & Laporan -->
            <li class="nav-item">
                <a class="nav-link <?= in_array($active, ['tugas', 'laporan_create', 'laporan_riwayat']) ? '' : 'collapsed' ?>" href="#menuTugas" data-bs-toggle="collapse" role="button" aria-expanded="<?= in_array($active, ['tugas', 'laporan_create', 'laporan_riwayat']) ? 'true' : 'false' ?>">
                    <i class="fas fa-tasks me-2"></i> Tugas & Laporan <i class="fas fa-chevron-down ms-auto small"></i>
                </a>
                <div class="collapse <?= in_array($active, ['tugas', 'laporan_create', 'laporan_riwayat']) ? 'show' : '' ?>" id="menuTugas">
                    <ul class="nav flex-column ps-3 small">
                        <li class="nav-item">
                            <a class="nav-link py-1 <?= $active == 'tugas' ? 'active text-primary fw-bold' : '' ?>" href="<?= base_url('staff/tugas') ?>">
                                <i class="fas fa-clipboard-list me-2"></i> Tugas Saya
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link py-1 <?= $active == 'laporan_create' ? 'active text-primary fw-bold' : '' ?>" href="<?= base_url('staff/laporan/create') ?>">
                                <i class="fas fa-edit me-2"></i> Laporan Kerja Harian
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link py-1 <?= $active == 'laporan_riwayat' ? 'active text-primary fw-bold' : '' ?>" href="<?= base_url('staff/laporan') ?>">
                                <i class="fas fa-file-alt me-2"></i> Riwayat Laporan Saya
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Form Pengajuan -->
            <li class="nav-item">
                <a class="nav-link <?= in_array($active, ['pengajuan_cuti', 'pengajuan_izin', 'pengajuan_kasbon', 'pengajuan_riwayat']) ? '' : 'collapsed' ?>" href="#menuPengajuan" data-bs-toggle="collapse" role="button" aria-expanded="<?= in_array($active, ['pengajuan_cuti', 'pengajuan_izin', 'pengajuan_kasbon', 'pengajuan_riwayat']) ? 'true' : 'false' ?>">
                    <i class="fas fa-file-signature me-2"></i> Form Pengajuan <i class="fas fa-chevron-down ms-auto small"></i>
                </a>
                <div class="collapse <?= in_array($active, ['pengajuan_cuti', 'pengajuan_izin', 'pengajuan_kasbon', 'pengajuan_riwayat']) ? 'show' : '' ?>" id="menuPengajuan">
                    <ul class="nav flex-column ps-3 small">
                        <li class="nav-item">
                            <a class="nav-link py-1 <?= $active == 'pengajuan_cuti' ? 'active text-primary fw-bold' : '' ?>" href="<?= base_url('staff/pengajuan/cuti') ?>">
                                <i class="fas fa-calendar-minus me-2"></i> Form Cuti
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link py-1 <?= $active == 'pengajuan_izin' ? 'active text-primary fw-bold' : '' ?>" href="<?= base_url('staff/pengajuan/izin') ?>">
                                <i class="fas fa-envelope-open-text me-2"></i> Form Izin
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link py-1 <?= $active == 'pengajuan_kasbon' ? 'active text-primary fw-bold' : '' ?>" href="<?= base_url('staff/pengajuan/kasbon') ?>">
                                <i class="fas fa-hand-holding-usd me-2"></i> Form Kasbon
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link py-1 <?= $active == 'pengajuan_riwayat' ? 'active text-primary fw-bold' : '' ?>" href="<?= base_url('staff/pengajuan/riwayat') ?>">
                                <i class="fas fa-folder-open me-2"></i> Riwayat Pengajuan Saya
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Slip Gaji -->
            <li class="nav-item">
                <a class="nav-link <?= $active == 'payroll' ? 'active' : '' ?>" href="<?= base_url('staff/payroll') ?>">
                    <i class="fas fa-money-check-alt me-2"></i> Slip Gaji
                </a>
            </li>

            <!-- Profil -->
            <li class="nav-item">
                <a class="nav-link <?= in_array($active, ['profil', 'dokumen']) ? '' : 'collapsed' ?>" href="#menuProfil" data-bs-toggle="collapse" role="button" aria-expanded="<?= in_array($active, ['profil', 'dokumen']) ? 'true' : 'false' ?>">
                    <i class="fas fa-user-cog me-2"></i> Profil <i class="fas fa-chevron-down ms-auto small"></i>
                </a>
                <div class="collapse <?= in_array($active, ['profil', 'dokumen']) ? 'show' : '' ?>" id="menuProfil">
                    <ul class="nav flex-column ps-3 small">
                        <li class="nav-item">
                            <a class="nav-link py-1 <?= $active == 'profil' ? 'active text-primary fw-bold' : '' ?>" href="<?= base_url('staff/profil') ?>">
                                <i class="fas fa-user-edit me-2"></i> Profil Saya
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link py-1 <?= $active == 'dokumen' ? 'active text-primary fw-bold' : '' ?>" href="<?= base_url('staff/dokumen') ?>">
                                <i class="fas fa-id-card me-2"></i> Dokumen Saya
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Keluar -->
            <li class="nav-item mt-4 border-top pt-2">
                <a class="nav-link text-danger fw-bold" href="<?= base_url('logout') ?>">
                    <i class="fas fa-sign-out-alt me-2"></i> Keluar
                </a>
            </li>
        </ul>
    </div>
</nav>

<!-- Mobile Toggle Button -->
<button class="btn btn-primary position-fixed bottom-0 end-0 m-3 d-md-none rounded-circle p-3 shadow" style="z-index: 1001;" onclick="document.querySelector('.sidebar').classList.toggle('show')">
    <i class="fas fa-bars"></i>
</button>
