<?php
// app/Views/hrd/index.php
$data = $data ?? [
    'title' => 'HRD Dashboard - CDW Engineering',
    'subtitle' => date('l, d F Y'),
    'user' => ['name' => session()->get('name') ?: 'HRD Manager', 'role' => 'hrd'],
    'active' => 'dashboard'
];
?>
<?= view('hrd/templates/header', $data) ?>
<?= view('hrd/templates/sidebar', $data) ?>
<?= view('hrd/templates/navbar', $data) ?>

<!-- Welcome Card -->
<div class="welcome-card">
    <h3><i class="fas fa-users-cog me-2"></i>Selamat Datang di HRD Center, <?= htmlspecialchars($data['user']['name']) ?>!</h3>
    <p>Kelola seluruh data karyawan, rekrutmen, absensi, cuti, dan penggajian CDW Engineering secara terpusat.</p>
</div>

<!-- Stats Cards -->
<div class="stats-row">
    <div class="dashboard-card">
        <div class="card-icon blue"><i class="fas fa-users"></i></div>
        <div class="card-value">156</div>
        <div class="card-label">Total Karyawan</div>
        <small class="text-success"><i class="fas fa-check me-1"></i> Terdata aktif</small>
    </div>
    
    <div class="dashboard-card">
        <div class="card-icon green"><i class="fas fa-user-check"></i></div>
        <div class="card-value">142</div>
        <div class="card-label">Hadir Hari Ini</div>
        <small class="text-success"><i class="fas fa-arrow-up me-1"></i> 91% Tingkat Kehadiran</small>
    </div>
    
    <div class="dashboard-card">
        <div class="card-icon orange"><i class="fas fa-plane-departure"></i></div>
        <div class="card-value">5</div>
        <div class="card-label">Sedang Cuti / Izin</div>
        <small class="text-warning"><i class="fas fa-clock me-1"></i> Dalam pengawasan HR</small>
    </div>
    
    <div class="dashboard-card">
        <div class="card-icon purple"><i class="fas fa-user-plus"></i></div>
        <div class="card-value">8</div>
        <div class="card-label">Pelamar Baru</div>
        <small class="text-info"><i class="fas fa-file-alt me-1"></i> Perlu direview</small>
    </div>
</div>

<!-- Quick Access HRD -->
<div class="dashboard-card mb-4">
    <h5 class="mb-3" style="color: #1e3c72; font-weight: 700;"><i class="fas fa-bolt me-2"></i>Akses Cepat HRD</h5>
    <div class="row g-3">
        <div class="col-6 col-md-3">
            <a href="<?= base_url('hrd/karyawan/create') ?>" class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center">
                <i class="fas fa-user-plus fa-2x mb-2"></i>
                <span style="font-size:0.85rem; font-weight:600;">Tambah Karyawan</span>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="<?= base_url('hrd/absensi') ?>" class="btn btn-outline-success w-100 py-3 d-flex flex-column align-items-center">
                <i class="fas fa-clock fa-2x mb-2"></i>
                <span style="font-size:0.85rem; font-weight:600;">Kelola Absensi</span>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="<?= base_url('hrd/cuti/pending') ?>" class="btn btn-outline-warning w-100 py-3 d-flex flex-column align-items-center">
                <i class="fas fa-envelope-open-text fa-2x mb-2"></i>
                <span style="font-size:0.85rem; font-weight:600;">Approval Cuti</span>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="<?= base_url('hrd/finansial/payroll') ?>" class="btn btn-outline-info w-100 py-3 d-flex flex-column align-items-center">
                <i class="fas fa-calculator fa-2x mb-2"></i>
                <span style="font-size:0.85rem; font-weight:600;">Rekap Payroll</span>
            </a>
        </div>
    </div>
</div>

<?= view('hrd/templates/footer') ?>
