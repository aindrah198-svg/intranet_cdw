<?php
// app/Views/admin_panel/dashboard/index.php
$data = $data ?? [
    'title'    => 'Admin Panel - CDW Engineering',
    'subtitle' => date('l, d F Y'),
    'active'   => 'dashboard',
    'user'     => ['name' => session()->get('name') ?? 'Administrator', 'role' => 'admin'],
];
?>
<?= view('admin_panel/templates/header', $data) ?>
<?= view('admin_panel/templates/sidebar', $data) ?>
<?= view('admin_panel/templates/navbar', $data) ?>

<!-- Welcome Card -->
<div class="welcome-card">
    <div style="position:relative;z-index:1;">
        <h3>
            <i class="fas fa-user-shield me-2"></i>
            Selamat Datang, <?= htmlspecialchars($data['user']['name']) ?>!
        </h3>
        <p>Anda login sebagai <strong>Administrator</strong> di CDW Engineering — <?= date('l, d F Y') ?></p>
    </div>
</div>

<!-- Stats Cards -->
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

<!-- Quick Access -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="stat-card">
            <h5 style="font-weight:700;color:#4a148c;margin-bottom:18px;">
                <i class="fas fa-bolt me-2"></i>Akses Cepat
            </h5>
            <div class="row g-3">
                <?php
                $menus = [
                    ['href' => base_url('admin/surat/masuk'),          'icon' => 'fas fa-inbox',          'label' => 'Surat Masuk',       'color' => '#7b1fa2'],
                    ['href' => base_url('admin/surat/keluar'),         'icon' => 'fas fa-paper-plane',    'label' => 'Surat Keluar',      'color' => '#6a1b9a'],
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
                    <a href="<?= $m['href'] ?>" style="
                        display:flex;flex-direction:column;align-items:center;justify-content:center;
                        padding:16px 10px;border-radius:12px;text-decoration:none;
                        background:rgba(123,31,162,0.05);border:1px solid rgba(123,31,162,0.1);
                        transition:all 0.3s;text-align:center;min-height:80px;
                    " onmouseover="this.style.background='<?= $m['color'] ?>15';this.style.borderColor='<?= $m['color'] ?>40';this.style.transform='translateY(-3px)'"
                       onmouseout="this.style.background='rgba(123,31,162,0.05)';this.style.borderColor='rgba(123,31,162,0.1)';this.style.transform='none'">
                        <i class="<?= $m['icon'] ?>" style="font-size:1.4rem;color:<?= $m['color'] ?>;margin-bottom:6px;"></i>
                        <span style="font-size:0.75rem;font-weight:600;color:#333;"><?= $m['label'] ?></span>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Two Column Info -->
<div class="row g-4">
    <div class="col-md-6">
        <div class="stat-card h-100">
            <h6 style="font-weight:700;color:#4a148c;margin-bottom:14px;">
                <i class="fas fa-envelope me-2"></i> Surat Terbaru
            </h6>
            <div style="display:flex;flex-direction:column;gap:10px;">
                <?php for ($i=1; $i<=4; $i++): ?>
                <div style="display:flex;align-items:center;gap:12px;padding:10px;background:#f3e5f5;border-radius:8px;">
                    <div style="width:36px;height:36px;background:#7b1fa2;border-radius:8px;display:flex;align-items:center;justify-content:center;color:white;font-size:0.8rem;flex-shrink:0;">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:0.82rem;font-weight:600;color:#333;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Surat Masuk #SM-2025-00<?= $i ?></div>
                        <div style="font-size:0.72rem;color:#888;">Diterima <?= $i ?> hari lalu</div>
                    </div>
                    <span style="background:#e8f5e9;color:#388e3c;padding:2px 8px;border-radius:20px;font-size:0.7rem;font-weight:600;">Baru</span>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-card h-100">
            <h6 style="font-weight:700;color:#4a148c;margin-bottom:14px;">
                <i class="fas fa-calendar-check me-2"></i> Booking Ruang Hari Ini
            </h6>
            <div style="display:flex;flex-direction:column;gap:10px;">
                <?php
                $bookings = [
                    ['jam' => '08:00-09:00', 'ruang' => 'Ruang Rapat A',     'peminjam' => 'Div. Teknik'],
                    ['jam' => '10:00-11:30', 'ruang' => 'Ruang Presentasi',  'peminjam' => 'Div. Sales'],
                    ['jam' => '13:00-14:00', 'ruang' => 'Ruang Direktur',    'peminjam' => 'HR & Direktur'],
                    ['jam' => '15:00-16:00', 'ruang' => 'Ruang Rapat B',     'peminjam' => 'Div. Accounting'],
                ];
                foreach ($bookings as $b): ?>
                <div style="display:flex;align-items:center;gap:12px;padding:10px;background:#f3e5f5;border-radius:8px;">
                    <div style="font-size:0.72rem;font-weight:700;color:#7b1fa2;white-space:nowrap;min-width:80px;"><?= $b['jam'] ?></div>
                    <div style="flex:1;">
                        <div style="font-size:0.82rem;font-weight:600;color:#333;"><?= $b['ruang'] ?></div>
                        <div style="font-size:0.72rem;color:#888;"><?= $b['peminjam'] ?></div>
                    </div>
                    <span style="background:#fff3e0;color:#e65100;padding:2px 8px;border-radius:20px;font-size:0.7rem;font-weight:600;">Dijadwalkan</span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?= view('admin_panel/templates/footer', $data) ?>
