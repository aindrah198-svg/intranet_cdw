<?php
// app/Views/admin/dashboard/index.php
$data = $data ?? [
    'title'    => 'Admin Panel - CDW Engineering',
    'subtitle' => date('l, d F Y'),
    'active'   => 'dashboard',
    'user'     => ['name' => session()->get('name') ?? 'Administrator', 'role' => 'admin'],
];
?>
<?= view('admin/templates/header', $data) ?>
<?= view('admin/templates/sidebar', $data) ?>
<?= view('admin/templates/navbar', $data) ?>

<div class="welcome-card">
    <div style="position:relative;z-index:1;">
        <h3><i class="fas fa-user-shield me-2"></i>Selamat Datang di Admin Panel, <?= htmlspecialchars($data['user']['name']) ?>!</h3>
        <p>Kelola modul Administrasi Perusahaan: Surat Menyurat, ATK, Legal, Fasilitas, dan Laporan secara mandiri.</p>
    </div>
</div>

<div class="stats-row">
    <div class="dashboard-card">
        <div class="card-icon purple"><i class="fas fa-envelope"></i></div>
        <div class="card-value">12</div>
        <div class="card-label">Surat Masuk Baru</div>
        <small class="text-warning"><i class="fas fa-clock me-1"></i> Perlu ditindaklanjuti</small>
    </div>
    <div class="dashboard-card">
        <div class="card-icon orange"><i class="fas fa-boxes"></i></div>
        <div class="card-value">48</div>
        <div class="card-label">Item Inventaris</div>
        <small class="text-success"><i class="fas fa-check me-1"></i> Semua tercatat</small>
    </div>
    <div class="dashboard-card">
        <div class="card-icon teal"><i class="fas fa-clipboard-list"></i></div>
        <div class="card-value">7</div>
        <div class="card-label">Pengajuan Pending</div>
        <small class="text-danger"><i class="fas fa-exclamation me-1"></i> Perlu persetujuan</small>
    </div>
    <div class="dashboard-card">
        <div class="card-icon red"><i class="fas fa-comment-dots"></i></div>
        <div class="card-value">3</div>
        <div class="card-label">Keluhan Baru</div>
        <small class="text-warning"><i class="fas fa-clock me-1"></i> Belum ditanggapi</small>
    </div>
</div>

<div class="stat-card p-4 bg-white rounded-3 shadow-sm mb-4">
    <h5 style="font-weight:700;color:#1e3c72;margin-bottom:18px;"><i class="fas fa-bolt me-2 text-primary"></i>Akses Cepat Modul Admin</h5>
    <div class="row g-3">
        <?php
        $menus = [
            ['href' => base_url('admin/surat/masuk'),          'icon' => 'fas fa-inbox',          'label' => 'Surat Masuk',       'color' => '#1e3c72'],
            ['href' => base_url('admin/surat/keluar'),         'icon' => 'fas fa-paper-plane',    'label' => 'Surat Keluar',      'color' => '#2563eb'],
            ['href' => base_url('admin/inventaris/stok-atk'),  'icon' => 'fas fa-pen',            'label' => 'Stok ATK',          'color' => '#ef6c00'],
            ['href' => base_url('admin/inventaris/inventaris-kantor'), 'icon' => 'fas fa-building','label' => 'Inventaris Kantor','color' => '#e65100'],
            ['href' => base_url('admin/dokumen/penting'),       'icon' => 'fas fa-folder-open',    'label' => 'Dokumen Penting',   'color' => '#1565c0'],
            ['href' => base_url('admin/fasilitas/buku-tamu'),  'icon' => 'fas fa-book-open',      'label' => 'Buku Tamu',         'color' => '#00695c'],
            ['href' => base_url('admin/fasilitas/booking-ruang'), 'icon' => 'fas fa-calendar-check','label' => 'Booking Ruang', 'color' => '#2e7d32'],
            ['href' => base_url('admin/pengajuan/semua'),      'icon' => 'fas fa-list-alt',       'label' => 'Semua Pengajuan',   'color' => '#ad1457'],
            ['href' => base_url('admin/laporan/dashboard'),    'icon' => 'fas fa-chart-bar',      'label' => 'Dashboard Laporan', 'color' => '#c62828'],
            ['href' => base_url('admin/absensi-saya'),         'icon' => 'fas fa-fingerprint',    'label' => 'Absensi Saya',      'color' => '#4527a0'],
            ['href' => base_url('admin/slip-gaji'),            'icon' => 'fas fa-money-bill-wave','label' => 'Slip Gaji',         'color' => '#0277bd'],
            ['href' => base_url('admin/profil'),               'icon' => 'fas fa-id-badge',       'label' => 'Profil',            'color' => '#37474f'],
        ];
        foreach ($menus as $m): ?>
        <div class="col-6 col-md-4 col-lg-3 col-xl-2">
            <a href="<?= $m['href'] ?>" class="text-decoration-none d-flex flex-column align-items-center justify-content-center p-3 rounded-3"
               style="background:rgba(30,60,114,0.04);border:1px solid rgba(30,60,114,0.1);transition:all 0.3s;min-height:85px;text-align:center;">
                <i class="<?= $m['icon'] ?>" style="font-size:1.4rem;color:<?= $m['color'] ?>;margin-bottom:6px;"></i>
                <span style="font-size:0.78rem;font-weight:600;color:#333;"><?= $m['label'] ?></span>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?= view('admin/templates/footer', $data) ?>
