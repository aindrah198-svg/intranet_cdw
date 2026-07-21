<?php
// C:\xampp\htdocs\cdwnet\app\Views\admin\jam_kerja\detail.php
$title = 'Detail Jam Kerja';
$active = 'jamkerja';
$css = ['https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'];
$scripts = ['https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'];
?>

<?= $this->include('admin/templates/header') ?>
<?= $this->include('admin/templates/sidebar') ?>
<?= $this->include('admin/templates/navbar') ?>

<!-- Main Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Jam Kerja</h1>
        <div class="d-flex gap-2">
            <a href="<?= base_url('admin/jam-kerja'); ?>" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
            </a>
            <button type="button" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" onclick="printReport()">
                <i class="fas fa-print fa-sm text-white-50"></i> Print
            </button>
        </div>
    </div>

    <!-- Karyawan Info -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-left-primary shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 text-center">
                            <?php if (!empty($karyawan['foto'])): ?>
                                <img src="<?= base_url('uploads/karyawan/' . $karyawan['foto']); ?>" 
                                     alt="Foto" class="img-fluid rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                            <?php else: ?>
                                <i class="fas fa-user-circle fa-4x text-primary mb-3"></i>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-10">
                            <h4 class="mb-1"><?= esc($karyawan['nama_lengkap']); ?></h4>
                            <p class="text-muted mb-2">
                                NIK: <?= esc($karyawan['nik']); ?> | 
                                Jabatan: <?= esc($karyawan['jabatan']); ?> | 
                                Departemen: <?= esc($karyawan['departemen']); ?>
                            </p>
                            <div class="d-flex gap-3">
                                <div>
                                    <small class="text-muted">Mulai Kerja:</small><br>
                                    <strong><?= !empty($karyawan['tanggal_masuk']) ? date('d/m/Y', strtotime($karyawan['tanggal_masuk'])) : '-'; ?></strong>
                                </div>
                                <div>
                                    <small class="text-muted">Status:</small><br>
                                    <?php 
                                    $statusColor = 'secondary';
                                    $statusText = $karyawan['status_karyawan'] ?? '-';
                                    if ($statusText == 'Tetap') $statusColor = 'success';
                                    elseif ($statusText == 'Kontrak') $statusColor = 'warning';
                                    elseif ($statusText == 'Probation') $statusColor = 'info';
                                    elseif ($statusText == 'Magang') $statusColor = 'primary';
                                    ?>
                                    <span class="badge bg-<?= $statusColor; ?>">
                                        <?= $statusText; ?>
                                    </span>
                                </div>
                                <div>
                                    <small class="text-muted">Email:</small><br>
                                    <strong><?= !empty($karyawan['email']) ? $karyawan['email'] : '-'; ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Hari
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $summary['total_hari']; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Hari Hadir
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $summary['hari_hadir']; ?></div>
                            <div class="text-xs text-muted mt-1">
                                <?= $summary['total_hari'] > 0 ? round(($summary['hari_hadir'] / $summary['total_hari']) * 100, 1) : 0; ?>%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
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
                                Total Jam Kerja
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $summary['total_jam_kerja_display']; ?>
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
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Rata per Hari
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $summary['rata_per_hari_display']; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Total Lembur
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $summary['total_lembur_display']; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-business-time fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Total Terlambat
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $summary['total_terlambat']; ?> menit
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                Efisiensi Kerja
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php
                                $totalJamSeharusnya = $summary['hari_hadir'] * 8; // 8 jam per hari
                                $efisiensi = ($totalJamSeharusnya > 0) ? ($summary['total_jam_kerja'] / $totalJamSeharusnya) * 100 : 0;
                                echo round($efisiensi, 1) . '%';
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-pie fa-2x text-gray-300"></i>
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
                                Kehadiran
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php 
                                $totalAbsensi = $summary['hari_hadir'] + $summary['hari_izin'] + $summary['hari_sakit'] + $summary['hari_cuti'];
                                $presentase = ($totalAbsensi > 0) ? ($summary['hari_hadir'] / $totalAbsensi) * 100 : 0;
                                echo round($presentase, 1) . '%';
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Distribution -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Distribusi Status Absensi</h6>
                    <small class="text-muted">Total: <?= $summary['total_hari']; ?> hari kerja</small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 text-center">
                            <div class="mb-3">
                                <div class="badge bg-success p-3 rounded-circle">
                                    <i class="fas fa-check fa-2x"></i>
                                </div>
                                <div class="mt-2">
                                    <h5><?= $summary['hari_hadir']; ?></h5>
                                    <small>Hadir</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 text-center">
                            <div class="mb-3">
                                <div class="badge bg-info p-3 rounded-circle">
                                    <i class="fas fa-file-alt fa-2x"></i>
                                </div>
                                <div class="mt-2">
                                    <h5><?= $summary['hari_izin']; ?></h5>
                                    <small>Izin</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 text-center">
                            <div class="mb-3">
                                <div class="badge bg-warning p-3 rounded-circle">
                                    <i class="fas fa-procedures fa-2x"></i>
                                </div>
                                <div class="mt-2">
                                    <h5><?= $summary['hari_sakit']; ?></h5>
                                    <small>Sakit</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 text-center">
                            <div class="mb-3">
                                <div class="badge bg-primary p-3 rounded-circle">
                                    <i class="fas fa-umbrella-beach fa-2x"></i>
                                </div>
                                <div class="mt-2">
                                    <h5><?= $summary['hari_cuti']; ?></h5>
                                    <small>Cuti</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 text-center">
                            <div class="mb-3">
                                <div class="badge bg-secondary p-3 rounded-circle">
                                    <i class="fas fa-bed fa-2x"></i>
                                </div>
                                <div class="mt-2">
                                    <h5><?= $summary['hari_libur']; ?></h5>
                                    <small>Libur</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 text-center">
                            <div class="mb-3">
                                <div class="badge bg-danger p-3 rounded-circle">
                                    <i class="fas fa-times fa-2x"></i>
                                </div>
                                <div class="mt-2">
                                    <h5><?= $summary['hari_alpha']; ?></h5>
                                    <small>Alpha</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Periode</h6>
        </div>
        <div class="card-body">
            <form method="get" action="">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="start_date">Dari Tanggal:</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" 
                                   value="<?= esc($filter['start_date']); ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="end_date">Sampai Tanggal:</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" 
                                   value="<?= esc($filter['end_date']); ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status">Status:</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">Semua Status</option>
                                <option value="Hadir" <?= (isset($filter['status']) && $filter['status'] == 'Hadir') ? 'selected' : ''; ?>>Hadir</option>
                                <option value="Izin" <?= (isset($filter['status']) && $filter['status'] == 'Izin') ? 'selected' : ''; ?>>Izin</option>
                                <option value="Sakit" <?= (isset($filter['status']) && $filter['status'] == 'Sakit') ? 'selected' : ''; ?>>Sakit</option>
                                <option value="Cuti" <?= (isset($filter['status']) && $filter['status'] == 'Cuti') ? 'selected' : ''; ?>>Cuti</option>
                                <option value="Libur" <?= (isset($filter['status']) && $filter['status'] == 'Libur') ? 'selected' : ''; ?>>Libur</option>
                                <option value="Alpha" <?= (isset($filter['status']) && $filter['status'] == 'Alpha') ? 'selected' : ''; ?>>Alpha</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="<?= base_url('admin/jam-kerja/detail/' . $karyawan['id']); ?>" class="btn btn-secondary">
                        <i class="fas fa-redo me-1"></i> Reset
                    </a>
                    <button type="button" class="btn btn-success" onclick="exportDetail()">
                        <i class="fas fa-file-excel me-1"></i> Export Excel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Detail Absensi -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Detail Absensi Per Hari</h6>
            <div>
                <small class="text-muted me-3">
                    Periode: <?= date('d/m/Y', strtotime($filter['start_date'])); ?> - <?= date('d/m/Y', strtotime($filter['end_date'])); ?>
                </small>
                <small class="text-muted">
                    Total: <?= count($absensi); ?> hari
                </small>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="detailTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Hari</th>
                            <th>Shift</th>
                            <th>Masuk</th>
                            <th>Pulang</th>
                            <th>Jam Kerja</th>
                            <th>Status</th>
                            <th>Terlambat</th>
                            <th>Lembur</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
<tbody>
    <?php if (isset($absensi) && !empty($absensi)): ?>
        <?php $no = 1; ?>
        <?php foreach ($absensi as $a): ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= date('d/m/Y', strtotime($a['tanggal'])); ?></td>
            <td><?= date('D', strtotime($a['tanggal'])); ?></td>
            <td class="text-center">
                <?php 
                $shift_names = [
                    'pagi' => 'Pagi',
                    'siang' => 'Siang',
                    'sore' => 'Sore',
                    'malam' => 'Malam'
                ];
                $shift = $a['shift'] ?? '';
                if ($shift && isset($shift_names[$shift])) {
                    $badge_color = [
                        'pagi' => 'warning',
                        'siang' => 'success',
                        'sore' => 'info',
                        'malam' => 'primary'
                    ];
                    ?>
                    <span class="badge bg-<?= $badge_color[$shift] ?? 'secondary'; ?>">
                        <?= $shift_names[$shift]; ?>
                    </span>
                    <?php
                } else {
                    echo '-';
                }
                ?>
            </td>
            <td class="text-center">
                <?php if (!empty($a['waktu_masuk'])): ?>
                    <?= date('H:i', strtotime($a['waktu_masuk'])); ?>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
            <td class="text-center">
                <?php if (!empty($a['waktu_pulang'])): ?>
                    <?= date('H:i', strtotime($a['waktu_pulang'])); ?>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
            <td class="text-center">
                <?php 
                if (!empty($a['jam_kerja']) && $a['jam_kerja'] > 0) {
                    $jam = floor($a['jam_kerja']);
                    $menit = round(($a['jam_kerja'] - $jam) * 60);
                    
                    if ($jam > 0 && $menit > 0) {
                        echo "{$jam} jam {$menit} menit";
                    } elseif ($jam > 0) {
                        echo "{$jam} jam";
                    } elseif ($menit > 0) {
                        echo "{$menit} menit";
                    } else {
                        echo '-';
                    }
                } else {
                    echo '-';
                }
                ?>
            </td>
            <td class="text-center">
                <?php 
                $statusColors = [
                    'Hadir' => 'success',
                    'Izin' => 'info',
                    'Sakit' => 'warning',
                    'Cuti' => 'primary',
                    'Libur' => 'secondary',
                    'Alpha' => 'danger'
                ];
                $status = $a['status'] ?? '';
                ?>
                <span class="badge bg-<?= $statusColors[$status] ?? 'secondary'; ?>">
                    <?= $status; ?>
                </span>
            </td>
            <td class="text-center">
                <?php if (!empty($a['terlambat']) && $a['terlambat'] > 0): ?>
                    <span class="text-danger"><?= $a['terlambat']; ?> menit</span>
                <?php else: ?>
                    <span class="text-success">Tepat waktu</span>
                <?php endif; ?>
            </td>
            <td class="text-center">
                <?php 
                if (!empty($a['jam_lembur']) && $a['jam_lembur'] > 0) {
                    $jam = floor($a['jam_lembur']);
                    $menit = round(($a['jam_lembur'] - $jam) * 60);
                    
                    if ($jam > 0 && $menit > 0) {
                        echo "{$jam} jam {$menit} menit";
                    } elseif ($jam > 0) {
                        echo "{$jam} jam";
                    } elseif ($menit > 0) {
                        echo "{$menit} menit";
                    } else {
                        echo '-';
                    }
                } else {
                    echo '-';
                }
                ?>
            </td>
            <td><?= !empty($a['keterangan']) ? esc($a['keterangan']) : '-'; ?></td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="11" class="text-center py-4">
                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                <p class="text-muted">Tidak ada data absensi ditemukan untuk periode ini.</p>
            </td>
        </tr>
    <?php endif; ?>
</tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-md-6">
                    <small class="text-muted">Catatan:</small>
                    <ul class="small text-muted mb-0">
                        <li>Jam kerja dihitung dari selisih waktu masuk dan pulang</li>
                        <li>Lembur dihitung setelah jam shift selesai</li>
                        <li>Terlambat dihitung dari jam mulai shift + toleransi 30 menit</li>
                    </ul>
                </div>
                <div class="col-md-6 text-end">
                    <small class="text-muted">Dicetak pada: <?= date('d/m/Y H:i:s'); ?></small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#detailTable').DataTable({
        "pageLength": 10,
        "lengthMenu": [10, 25, 50, 100],
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
        "order": [[1, 'desc']] // Sort by tanggal descending
    });

    // Auto-hide alerts
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);
});

// Print Report
function printReport() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const status = document.getElementById('status').value;
    
    let url = '<?= base_url("admin/jam-kerja/detail/" . $karyawan['id']); ?>?' +
        'start_date=' + encodeURIComponent(startDate) +
        '&end_date=' + encodeURIComponent(endDate) +
        '&print=true';
    
    if (status) {
        url += '&status=' + encodeURIComponent(status);
    }
    
    const printWindow = window.open(url, '_blank', 'width=1200,height=800');
    if (printWindow) {
        printWindow.onload = function() {
            printWindow.print();
        };
    }
}

// Export to Excel
function exportDetail() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const status = document.getElementById('status').value;
    
    let url = '<?= base_url("admin/jam-kerja/export/excel"); ?>?' +
        'karyawan_id=<?= $karyawan['id']; ?>' +
        '&start_date=' + encodeURIComponent(startDate) +
        '&end_date=' + encodeURIComponent(endDate);
    
    if (status) {
        url += '&status=' + encodeURIComponent(status);
    }
    
    window.open(url, '_blank');
}
</script>

<?= $this->include('admin/templates/footer') ?>