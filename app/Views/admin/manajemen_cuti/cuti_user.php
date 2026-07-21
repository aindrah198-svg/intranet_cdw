<?php
// C:\xampp\htdocs\cdwnet\app\Views\admin\manajemen_cuti\cuti_user.php
// File untuk My Cuti (Cuti Saya)

$title = 'Cuti Saya';
$active = 'cuti';

// Default values from controller
$cuti = $cuti ?? [];
$karyawan = $karyawan ?? null;
$user = $user ?? null;
$kuota = $kuota ?? null;
$stats = $stats ?? [];
$autoConnected = $autoConnected ?? false;
$totalCuti = count($cuti);

// Extract stats
$approvedCount = $stats['approvedCount'] ?? 0;
$pendingCount = $stats['pendingCount'] ?? 0;
$rejectedCount = $stats['rejectedCount'] ?? 0;
$totalDays = $stats['totalDays'] ?? 0;
$quota = $stats['quota'] ?? 12;
$remaining = $stats['remaining'] ?? 12;
$progress = $stats['progress'] ?? 0;
$circumference = $stats['circumference'] ?? 339.2928; // 2 * 3.1416 * 54

// User info
$userName = $user['name'] ?? session()->get('name') ?? 'User';
?>

<?= $this->include('admin/templates/header') ?>
<?= $this->include('admin/templates/sidebar') ?>
<?= $this->include('admin/templates/navbar') ?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Cuti Saya</h1>
        <div>
            <a href="<?= base_url('admin/cuti/create'); ?>" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Ajukan Cuti
            </a>
            <a href="<?= base_url('admin/cuti'); ?>" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm ms-2">
                <i class="fas fa-users fa-sm text-white-50"></i> Semua Cuti
            </a>
        </div>
    </div>

    <!-- Auto-connect Success Message -->
    <?php if ($autoConnected && $karyawan): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <strong>Berhasil!</strong> Akun Anda telah otomatis terhubung dengan data karyawan: 
        <strong><?= esc($karyawan['nama_lengkap']); ?></strong> (NIK: <?= esc($karyawan['nik']); ?>)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <!-- User Info -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 <?= $karyawan ? 'bg-success text-white' : 'bg-warning text-dark'; ?>">
            <h6 class="m-0 font-weight-bold">
                <i class="fas <?= $karyawan ? 'fa-user-check' : 'fa-user-clock'; ?> me-2"></i>
                Informasi <?= $karyawan ? 'Karyawan' : 'Akun'; ?>
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="font-weight-bold text-primary">Data Akun</h6>
                    <p><strong>Nama User:</strong> <?= esc($userName); ?></p>
                    <p><strong>Email:</strong> <?= esc($user['email'] ?? '-'); ?></p>
                    <p><strong>Role:</strong> <?= esc($user['role'] ?? '-'); ?></p>
                    <p><strong>Status Akun:</strong> 
                        <span class="badge bg-<?= ($user['status'] ?? 'active') === 'active' ? 'success' : 'warning'; ?>">
                            <?= ucfirst($user['status'] ?? 'active'); ?>
                        </span>
                    </p>
                </div>
                <div class="col-md-6">
                    <?php if ($karyawan): ?>
                        <h6 class="font-weight-bold text-success">Data Karyawan</h6>
                        <p><strong>NIK:</strong> <?= esc($karyawan['nik']); ?></p>
                        <p><strong>Nama Lengkap:</strong> <?= esc($karyawan['nama_lengkap']); ?></p>
                        <p><strong>Jabatan:</strong> <?= esc($karyawan['jabatan'] ?? '-'); ?></p>
                        <p><strong>Departemen:</strong> <?= esc($karyawan['departemen'] ?? '-'); ?></p>
                        <p><strong>Status Karyawan:</strong> 
                            <span class="badge bg-info"><?= esc($karyawan['status_karyawan'] ?? '-'); ?></span>
                        </p>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Perhatian</h6>
                            <p class="mb-2">Akun Anda belum terhubung dengan data karyawan.</p>
                            <p class="mb-0">Untuk mengajukan cuti, akun Anda harus terhubung dengan data karyawan.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Kuota Cuti -->
    <?php if ($karyawan): ?>
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-chart-pie me-2"></i>
                        Kuota Cuti Tahunan <?= date('Y'); ?>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="quota-display text-center mb-4">
                                <div class="d-flex justify-content-center align-items-center mb-3">
                                    <div class="circular-progress mx-auto" style="width: 150px; height: 150px; position: relative;">
                                        <!-- SVG Progress Circle -->
                                        <svg width="150" height="150" viewBox="0 0 150 150">
                                            <!-- Background circle -->
                                            <circle cx="75" cy="75" r="68" fill="none" stroke="#e9ecef" stroke-width="14"/>
                                            <!-- Progress circle -->
                                            <circle cx="75" cy="75" r="68" fill="none" stroke="#28a745" stroke-width="14"
                                                    stroke-dasharray="<?= $progress ?> <?= $circumference ?>"
                                                    stroke-linecap="round"
                                                    transform="rotate(-90 75 75)"/>
                                        </svg>
                                        <!-- Inner text -->
                                        <div style="position: absolute; width: 120px; height: 120px; 
                                                    border-radius: 50%; top: 15px; left: 15px; 
                                                    display: flex; flex-direction: column; 
                                                    justify-content: center; align-items: center;">
                                            <span style="font-size: 2rem; font-weight: bold; color: #28a745;">
                                                <?= $remaining; ?>
                                            </span>
                                            <span style="font-size: 0.9rem; color: #6c757d;">Hari Tersisa</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row text-center">
                                    <div class="col-md-4">
                                        <div class="quota-item">
                                            <h5 class="font-weight-bold text-primary"><?= $quota; ?></h5>
                                            <small class="text-muted">Total Kuota</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="quota-item">
                                            <h5 class="font-weight-bold text-warning"><?= $totalDays; ?></h5>
                                            <small class="text-muted">Telah Digunakan</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="quota-item">
                                            <h5 class="font-weight-bold text-success"><?= $remaining; ?></h5>
                                            <small class="text-muted">Sisa Kuota</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <?php if ($kuota): ?>
                            <div class="alert alert-success">
                                <h6><i class="fas fa-check-circle me-2"></i> Data Kuota Tersedia</h6>
                                <p class="mb-1">Kuota tahunan Anda untuk tahun <?= date('Y'); ?> adalah <strong><?= $kuota['kuota_tahunan']; ?> hari</strong>.</p>
                                <?php if ($kuota['terpakai'] > 0): ?>
                                    <p class="mb-0 mt-2">Anda telah menggunakan <?= $kuota['terpakai']; ?> hari dari kuota yang tersedia.</p>
                                <?php endif; ?>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle me-2"></i> Informasi Kuota</h6>
                                <p class="mb-0">Data kuota cuti tahunan Anda untuk tahun <?= date('Y'); ?> sedang diproses.</p>
                            </div>
                            <?php endif; ?>
                            
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle me-2"></i> Aturan Penggunaan Cuti</h6>
                                <ul class="mb-0" style="padding-left: 1.2rem;">
                                    <li>Kuota cuti tahunan: <strong>12 hari</strong> per tahun</li>
                                    <li>Cuti sakit <strong>tidak mengurangi</strong> kuota tahunan</li>
                                    <li>Ajukan cuti minimal <strong>3 hari kerja</strong> sebelumnya</li>
                                    <li>Sisa cuti <strong>tidak dapat dibawa</strong> ke tahun berikutnya</li>
                                    <li>Cuti hamil: 3 bulan sebelum & 3 bulan setelah melahirkan</li>
                                    <li>Kuota diperbarui setiap <strong>1 Januari</strong></li>
                                </ul>
                            </div>
                            
                            <?php if ($remaining > 0): ?>
                            <div class="text-center mt-3">
                                <a href="<?= base_url('admin/cuti/create'); ?>" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> Ajukan Cuti Baru
                                </a>
                                <a href="<?= base_url('admin/cuti/calendar'); ?>" class="btn btn-info">
                                    <i class="fas fa-calendar-alt me-1"></i> Lihat Kalendar
                                </a>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-warning text-center">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Kuota cuti tahunan Anda sudah habis.
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Pengajuan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalCuti; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Disetujui
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $approvedCount; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Menunggu
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $pendingCount; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Ditolak
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $rejectedCount; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Cuti -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-history me-2"></i>
                Riwayat Pengajuan Cuti Saya
                <?php if ($totalCuti > 0): ?>
                <span class="badge bg-primary ms-2"><?= $totalCuti; ?> Data</span>
                <?php endif; ?>
            </h6>
            <div>
                <span class="badge bg-success">Disetujui</span>
                <span class="badge bg-warning ms-2">Menunggu</span>
                <span class="badge bg-danger ms-2">Ditolak</span>
                <span class="badge bg-secondary ms-2">Dibatalkan</span>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($cuti)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-calendar-alt fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted mb-3">Belum ada pengajuan cuti</h4>
                    <p class="text-muted mb-4">Mulai ajukan cuti pertama Anda untuk mengelola waktu istirahat dengan baik.</p>
                    <?php if ($karyawan && $remaining > 0): ?>
                    <a href="<?= base_url('admin/cuti/create'); ?>" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i> Ajukan Cuti Pertama
                    </a>
                    <?php elseif (!$karyawan): ?>
                    <div class="alert alert-warning w-75 mx-auto">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Untuk mengajukan cuti, akun Anda harus terhubung dengan data karyawan.
                    </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0" id="myCutiTable">
                        <thead class="thead-light">
                            <tr>
                                <th width="50">No</th>
                                <th>Nomor Cuti</th>
                                <th>Jenis Cuti</th>
                                <th>Periode</th>
                                <th>Lama (Hari)</th>
                                <th>Status</th>
                                <th>Tanggal Pengajuan</th>
                                <th width="120" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($cuti as $c): ?>
                            <?php 
                            // Determine badge color
                            $badgeClass = 'secondary';
                            $iconClass = 'fa-question-circle';
                            
                            if (in_array($c['status'], ['Disetujui HRD', 'Disetujui Atasan'])) {
                                $badgeClass = 'success';
                                $iconClass = 'fa-check-circle';
                            } elseif ($c['status'] === 'Ditolak') {
                                $badgeClass = 'danger';
                                $iconClass = 'fa-times-circle';
                            } elseif ($c['status'] === 'Menunggu') {
                                $badgeClass = 'warning';
                                $iconClass = 'fa-clock';
                            } elseif ($c['status'] === 'Dibatalkan') {
                                $badgeClass = 'secondary';
                                $iconClass = 'fa-ban';
                            } elseif ($c['status'] === 'Draft') {
                                $badgeClass = 'info';
                                $iconClass = 'fa-edit';
                            }
                            ?>
                            <tr>
                                <td class="text-center"><?= $no++; ?></td>
                                <td>
                                    <strong><?= esc($c['nomor_cuti']); ?></strong>
                                    <?php if (($c['jenis_cuti'] ?? '') === 'Tahunan' && !empty($c['sisa_cuti_tahunan'])): ?>
                                        <br><small class="text-muted">Sisa kuota: <?= $c['sisa_cuti_tahunan']; ?> hari</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <i class="fas fa-calendar-day me-1 text-primary"></i>
                                    <?= esc($c['jenis_cuti']); ?>
                                </td>
                                <td>
                                    <i class="fas fa-calendar-alt me-1 text-info"></i>
                                    <?= date('d/m/Y', strtotime($c['tanggal_mulai'])); ?><br>
                                    <small class="text-muted ms-3">s/d <?= date('d/m/Y', strtotime($c['tanggal_selesai'])); ?></small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">
                                        <i class="fas fa-calendar-check me-1"></i>
                                        <?= $c['lama_hari']; ?> hari
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $badgeClass; ?>">
                                        <i class="fas <?= $iconClass; ?> me-1"></i>
                                        <?= $c['status']; ?>
                                    </span>
                                    <?php if (($c['status'] ?? '') === 'Disetujui HRD' && session()->get('role') === 'staff'): ?>
                                        <br><small class="text-muted"><i class="fas fa-user-clock me-1"></i>Menunggu atasan</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <i class="fas fa-calendar-plus me-1 text-secondary"></i>
                                    <?= date('d/m/Y', strtotime($c['tanggal_pengajuan'])); ?><br>
                                    <small class="text-muted ms-3"><?= date('H:i', strtotime($c['tanggal_pengajuan'])); ?></small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="<?= base_url('admin/cuti/show/' . $c['id']); ?>" 
                                           class="btn btn-info" title="Detail" data-bs-toggle="tooltip">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if (in_array($c['status'], ['Draft', 'Menunggu'])): ?>
                                        <a href="<?= base_url('admin/cuti/edit/' . $c['id']); ?>" 
                                           class="btn btn-warning" title="Edit" data-bs-toggle="tooltip">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php endif; ?>
                                        <?php if ($c['status'] === 'Menunggu' && in_array(session()->get('role'), ['admin', 'hrd', 'atasan'])): ?>
                                        <a href="<?= base_url('admin/cuti/approve/' . $c['id']); ?>" 
                                           class="btn btn-success" title="Setujui" data-bs-toggle="tooltip">
                                            <i class="fas fa-check"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Export Button -->
                <div class="text-center mt-4">
                    <a href="<?= base_url('admin/cuti/export/excel'); ?>?my_cuti=1" class="btn btn-success" target="_blank">
                        <i class="fas fa-file-excel me-1"></i> Export Riwayat Cuti
                    </a>
                    <button class="btn btn-info ms-2" onclick="window.print()">
                        <i class="fas fa-print me-1"></i> Cetak Laporan
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Upcoming Leave -->
    <?php if (!empty($stats['upcoming'])): ?>
    <div class="card shadow mt-4">
        <div class="card-header py-3 bg-success text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-calendar-check me-2"></i>
                Cuti Mendatang
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach ($stats['upcoming'] as $up): ?>
                <?php 
                $startDate = new DateTime($up['tanggal_mulai']);
                $endDate = new DateTime($up['tanggal_selesai']);
                $currentDate = new DateTime();
                $interval = $currentDate->diff($startDate);
                $daysUntil = $interval->days;
                ?>
                <div class="col-md-4 mb-3">
                    <div class="card border-success h-100">
                        <div class="card-body">
                            <h6 class="font-weight-bold text-success">
                                <i class="fas fa-plane-departure me-2"></i>
                                <?= esc($up['jenis_cuti'] ?? '-'); ?>
                            </h6>
                            <p class="mb-2">
                                <i class="fas fa-calendar-alt me-1 text-muted"></i>
                                <?= date('d M Y', strtotime($up['tanggal_mulai'])); ?> 
                                <i class="fas fa-arrow-right mx-1 text-muted"></i>
                                <?= date('d M Y', strtotime($up['tanggal_selesai'])); ?>
                            </p>
                            <p class="mb-2">
                                <i class="fas fa-clock me-1 text-muted"></i> 
                                <span class="badge bg-info"><?= $up['lama_hari'] ?? 0; ?> hari</span>
                            </p>
                            <p class="mb-0">
                                <i class="fas fa-hourglass-half me-1 text-muted"></i>
                                <span class="text-warning"><?= $daysUntil; ?> hari lagi</span>
                            </p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.circular-progress {
    position: relative;
}

.quota-item {
    padding: 10px;
}

.quota-item h5 {
    margin: 0;
}

#myCutiTable tbody tr:hover {
    background-color: #f8f9fa;
    transition: background-color 0.3s;
}

.badge {
    font-size: 0.85em;
    padding: 0.4em 0.8em;
}

.card {
    border-radius: 10px;
    overflow: hidden;
}

.card-header {
    border-top-left-radius: 10px !important;
    border-top-right-radius: 10px !important;
}
</style>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#myCutiTable').DataTable({
        "pageLength": 10,
        "lengthMenu": [10, 25, 50, 100],
        "order": [[6, 'desc']], // Sort by tanggal pengajuan
        "language": {
            "search": "Cari:",
            "lengthMenu": "Tampilkan _MENU_ data",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
            "infoFiltered": "(disaring dari _MAX_ total data)",
            "zeroRecords": "Data tidak ditemukan",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Berikutnya",
                "previous": "Sebelumnya"
            }
        },
        "columnDefs": [
            { "orderable": false, "targets": [0, 7] }
        ],
        "drawCallback": function(settings) {
            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });

    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);

    // Log untuk debugging
    console.log('My Cuti Page Loaded');
    console.log('Total Cuti:', <?= $totalCuti; ?>);
    console.log('Has Karyawan:', <?= $karyawan ? 'true' : 'false'; ?>);
    console.log('Karyawan Name:', '<?= $karyawan ? esc($karyawan['nama_lengkap']) : "None"; ?>');
    console.log('Kuota Stats:', {
        total: <?= $quota; ?>,
        used: <?= $totalDays; ?>,
        remaining: <?= $remaining; ?>
    });
});

// Function to update quota display (if needed dynamically)
function updateQuotaDisplay() {
    const quota = <?= $quota; ?>;
    const used = <?= $totalDays; ?>;
    const remaining = quota - used;
    const percentage = (used / quota) * 100;
    const circumference = 2 * Math.PI * 68;
    const progress = circumference * percentage / 100;
    
    // Update progress circle
    const progressCircle = document.querySelector('.circular-progress circle:nth-child(2)');
    if (progressCircle) {
        progressCircle.setAttribute('stroke-dasharray', `${progress} ${circumference}`);
    }
    
    // Update remaining days text
    const remainingText = document.querySelector('.circular-progress span:first-child');
    if (remainingText) {
        remainingText.textContent = remaining;
    }
    
    // Update stats in table
    $('.quota-item:nth-child(2) h5').text(used);
    $('.quota-item:nth-child(3) h5').text(remaining);
}

// Call on page load
updateQuotaDisplay();
</script>

<?= $this->include('admin/templates/footer') ?>