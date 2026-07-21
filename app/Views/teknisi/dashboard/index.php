<?php
$title = 'Dashboard Teknisi';
$active = 'dashboard';
$user = $user ?? ['name' => 'Teknisi', 'role' => 'teknisi'];
$absensiToday = $absensiToday ?? null;
$stats = $stats ?? [];
$tanggal_masuk_formatted = $tanggal_masuk_formatted ?? '';
$masa_kerja = $masa_kerja ?? ['years' => 0, 'months' => 0];
?>

<?= $this->include('teknisi/templates/header') ?>
<?= $this->include('teknisi/templates/sidebar') ?>
<?= $this->include('teknisi/templates/navbar') ?>

<div class="container-fluid py-4">
    <!-- Welcome Card -->
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-lg">
                <div class="card-body p-5">
                    <!-- Header with User Info -->
                    <div class="row align-items-center mb-5">
                        <div class="col-md-8">
                            <h1 class="display-5 fw-bold text-primary mb-3">
                                Selamat Datang, <?= htmlspecialchars($user['nama_panggilan'] ?? $user['name'] ?? 'Teknisi') ?>!
                            </h1>
                            <p class="lead text-muted mb-2">
                                Anda login sebagai <span class="badge bg-primary fs-6">TEKNISI</span>
                            </p>
                            <div class="d-flex flex-wrap gap-3 mt-3">
                                <span class="text-muted">
                                    <i class="fas fa-id-badge me-1"></i>
                                    NIK: <?= htmlspecialchars($user['nik'] ?? 'N/A') ?>
                                </span>
                                <span class="text-muted">
                                    <i class="fas fa-briefcase me-1"></i>
                                    <?= htmlspecialchars($user['jabatan'] ?? 'Teknisi') ?>
                                </span>
                                <span class="text-muted">
                                    <i class="fas fa-building me-1"></i>
                                    <?= htmlspecialchars($user['departemen'] ?? 'Engineering') ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="avatar-circle bg-primary text-white display-4 mb-3 mx-auto">
                                <?= strtoupper(substr($user['nama_panggilan'] ?? $user['name'] ?? 'T', 0, 1)) ?>
                            </div>
                            <h5 class="mb-1"><?= htmlspecialchars($user['nama_lengkap'] ?? $user['name'] ?? 'Teknisi') ?></h5>
                            <p class="text-muted small"><?= htmlspecialchars($user['email'] ?? '') ?></p>
                        </div>
                    </div>
                    
                    <!-- Today's Attendance Status -->
                    <div class="row mb-5">
                        <div class="col-12">
                            <div class="card border-start <?= $absensiToday ? 'border-success' : 'border-warning' ?> border-4">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h5 class="card-title">
                                                <i class="fas fa-calendar-day me-2"></i>
                                                Status Absensi Hari Ini
                                            </h5>
                                            <div class="d-flex flex-wrap gap-3 mt-2">
                                                <?php if ($absensiToday): ?>
                                                    <?php if ($absensiToday['waktu_masuk']): ?>
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check-circle me-1"></i>
                                                            Masuk: <?= date('H:i', strtotime($absensiToday['waktu_masuk'])) ?> WIB
                                                        </span>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($absensiToday['waktu_pulang']): ?>
                                                        <span class="badge bg-info">
                                                            <i class="fas fa-check-circle me-1"></i>
                                                            Pulang: <?= date('H:i', strtotime($absensiToday['waktu_pulang'])) ?> WIB
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning">
                                                            <i class="fas fa-clock me-1"></i>
                                                            Belum Pulang
                                                        </span>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($absensiToday['terlambat'] > 0): ?>
                                                        <span class="badge bg-danger">
                                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                                            Terlambat: <?= $absensiToday['terlambat'] ?> menit
                                                        </span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">
                                                        <i class="fas fa-clock me-1"></i>
                                                        Belum Absen Hari Ini
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <h4 class="mb-0 text-primary"><?= date('l, d F Y') ?></h4>
                                            <p class="text-muted mb-0" id="liveTime"><?= date('H:i:s') ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Statistics Cards -->
                    <div class="row mb-5">
                        <div class="col-md-3 mb-4">
                            <div class="card border-start border-primary border-4 h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-uppercase text-muted mb-2">Kehadiran Bulan Ini</h6>
                                            <h2 class="mb-0"><?= $stats['total_hadir'] ?? 0 ?>/<?= $stats['total_hari_kerja'] ?? 0 ?></h2>
                                            <p class="text-muted mb-0">Hari</p>
                                        </div>
                                        <div class="icon-circle bg-primary text-white">
                                            <i class="fas fa-calendar-check fa-2x"></i>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-primary" 
                                                 style="width: <?= min(100, $stats['persentase_kehadiran'] ?? 0) ?>%;"></div>
                                        </div>
                                        <small class="text-muted"><?= $stats['persentase_kehadiran'] ?? 0 ?>%</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-4">
                            <div class="card border-start border-danger border-4 h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-uppercase text-muted mb-2">Keterlambatan</h6>
                                            <h2 class="mb-0"><?= $stats['total_terlambat'] ?? 0 ?></h2>
                                            <p class="text-muted mb-0">Kali</p>
                                        </div>
                                        <div class="icon-circle bg-danger text-white">
                                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-4">
                            <div class="card border-start border-success border-4 h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-uppercase text-muted mb-2">Masa Kerja</h6>
                                            <h2 class="mb-0">
                                                <?php 
                                                if ($masa_kerja['years'] > 0 || $masa_kerja['months'] > 0) {
                                                    echo $masa_kerja['years'] . ' Thn ' . $masa_kerja['months'] . ' Bln';
                                                } else {
                                                    echo '-';
                                                }
                                                ?>
                                            </h2>
                                            <p class="text-muted mb-0">
                                                <?= $tanggal_masuk_formatted ?: 'Tanggal masuk belum diatur' ?>
                                            </p>
                                        </div>
                                        <div class="icon-circle bg-success text-white">
                                            <i class="fas fa-history fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-4">
                            <div class="card border-start border-info border-4 h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-uppercase text-muted mb-2">Status Karyawan</h6>
                                            <h2 class="mb-0">
                                                <?= htmlspecialchars($user['status_karyawan'] ?? 'Kontrak') ?>
                                            </h2>
                                            <p class="text-muted mb-0">
                                                <span class="badge <?= ($user['status'] ?? 'active') === 'active' ? 'bg-success' : 'bg-warning' ?>">
                                                    <?= ucfirst($user['status'] ?? 'Active') ?>
                                                </span>
                                            </p>
                                        </div>
                                        <div class="icon-circle bg-info text-white">
                                            <i class="fas fa-user-tie fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="row mb-5">
                        <div class="col-12">
                            <h4 class="mb-4">
                                <i class="fas fa-bolt text-warning me-2"></i>
                                Akses Cepat
                            </h4>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <a href="<?= base_url('teknisi/absensi') ?>" 
                                       class="btn btn-primary btn-lg w-100 py-3 d-flex flex-column align-items-center">
                                        <i class="fas fa-clock fa-2x mb-2"></i>
                                        <span>Absensi</span>
                                        <small class="text-white-50 mt-1">Masuk/Pulang</small>
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="<?= base_url('teknisi/cuti') ?>" 
                                       class="btn btn-success btn-lg w-100 py-3 d-flex flex-column align-items-center">
                                        <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                                        <span>Cuti/Izin</span>
                                        <small class="text-white-50 mt-1">Pengajuan</small>
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="<?= base_url('teknisi/profile') ?>" 
                                       class="btn btn-info btn-lg w-100 py-3 d-flex flex-column align-items-center">
                                        <i class="fas fa-user-circle fa-2x mb-2"></i>
                                        <span>Profil</span>
                                        <small class="text-white-50 mt-1">Data Pribadi</small>
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="<?= base_url('logout') ?>" 
                                       class="btn btn-outline-danger btn-lg w-100 py-3 d-flex flex-column align-items-center">
                                        <i class="fas fa-sign-out-alt fa-2x mb-2"></i>
                                        <span>Logout</span>
                                        <small class="text-danger-50 mt-1">Keluar Sistem</small>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Activity -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">
                                        <i class="fas fa-history me-2"></i>
                                        Aktivitas Terakhir (7 Hari)
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div id="recentActivity">
                                        <!-- Will be loaded via AJAX -->
                                        <div class="text-center py-4">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="mt-2 text-muted">Memuat aktivitas...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer Note -->
            <div class="text-center mt-4">
                <p class="text-muted">
                    <i class="fas fa-hard-hat me-1"></i>
                    CDW Engineering - Sistem Manajemen Teknisi
                    <span class="mx-2">•</span>
                    <?= htmlspecialchars($user['divisi'] ?? 'Technical') ?>
                    <span class="mx-2">•</span>
                    Versi 1.0
                </p>
            </div>
        </div>
    </div>
</div>

<script>
// Live Clock Update
function updateLiveTime() {
    const now = new Date();
    const timeStr = now.toLocaleTimeString('id-ID', { 
        hour: '2-digit', 
        minute: '2-digit',
        second: '2-digit'
    });
    const dateStr = now.toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    document.getElementById('liveTime').textContent = timeStr + ' WIB';
}

// Update every second
setInterval(updateLiveTime, 1000);

// Load recent activity via AJAX
function loadRecentActivity() {
    fetch('<?= base_url("teknisi/absensi/history") ?>?limit=7')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('recentActivity');
            if (data.history && data.history.length > 0) {
                let html = '<div class="table-responsive"><table class="table table-hover">';
                html += '<thead><tr>' +
                    '<th>Tanggal</th>' +
                    '<th>Masuk</th>' +
                    '<th>Pulang</th>' +
                    '<th>Jam Kerja</th>' +
                    '<th>Status</th>' +
                    '</tr></thead><tbody>';
                
                data.history.forEach(item => {
                    const terlambatClass = item.terlambat > 0 ? 'text-danger' : 'text-success';
                    const masukTime = item.waktu_masuk ? 
                        `<span class="${terlambatClass}">${item.waktu_masuk.substring(0, 5)}</span>` : '-';
                    const pulangTime = item.waktu_pulang ? item.waktu_pulang.substring(0, 5) : '-';
                    
                    html += `<tr>
                        <td>${item.tanggal}</td>
                        <td>${masukTime}</td>
                        <td>${pulangTime}</td>
                        <td>${item.jam_kerja ? item.jam_kerja + ' jam' : '-'}</td>
                        <td>
                            <span class="badge ${item.status === 'Hadir' ? 'bg-success' : 'bg-warning'}">
                                ${item.status}
                            </span>
                        </td>
                    </tr>`;
                });
                
                html += '</tbody></table></div>';
                container.innerHTML = html;
            } else {
                container.innerHTML = '<p class="text-center text-muted py-3">Belum ada aktivitas absensi</p>';
            }
        })
        .catch(error => {
            console.error('Error loading activity:', error);
            document.getElementById('recentActivity').innerHTML = 
                '<p class="text-center text-danger py-3">Gagal memuat aktivitas</p>';
        });
}

// Load activity on page load
document.addEventListener('DOMContentLoaded', function() {
    updateLiveTime();
    loadRecentActivity();
    
    // Auto refresh activity every 5 minutes
    setInterval(loadRecentActivity, 300000);
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Alt+A untuk Absensi
        if (e.altKey && e.key === 'a') {
            e.preventDefault();
            window.location.href = '<?= base_url("teknisi/absensi") ?>';
        }
        
        // Alt+P untuk Profile
        if (e.altKey && e.key === 'p') {
            e.preventDefault();
            window.location.href = '<?= base_url("teknisi/profile") ?>';
        }
        
        // Alt+L untuk Logout
        if (e.altKey && e.key === 'l') {
            e.preventDefault();
            window.location.href = '<?= base_url("logout") ?>';
        }
    });
});
</script>

<style>
/* Custom styles */
.card {
    border-radius: 15px;
    overflow: hidden;
}

.avatar-circle {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.icon-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-lg {
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-lg:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.badge {
    font-size: 0.85em;
    padding: 0.5em 1em;
}

.progress {
    border-radius: 3px;
}

/* Animation */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.card {
    animation: fadeIn 0.5s ease-out;
}

/* Responsive */
@media (max-width: 768px) {
    .display-5 {
        font-size: 1.8rem;
    }
    
    .avatar-circle {
        width: 70px;
        height: 70px;
        font-size: 1.5rem;
    }
    
    .btn-lg {
        padding: 0.75rem !important;
        font-size: 0.9rem;
    }
    
    .icon-circle {
        width: 50px;
        height: 50px;
    }
    
    .icon-circle i {
        font-size: 1.5rem;
    }
}
</style>

<?= $this->include('teknisi/templates/footer') ?>