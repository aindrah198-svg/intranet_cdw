<?php
// C:\xampp\htdocs\cdwnet\app\Views\admin\jam_kerja\rekap.php
$title = 'Rekap Jam Kerja';
$active = 'jamkerja';
$css = [
    'https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css',
    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
];
$scripts = [
    'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js',
    'https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js',
    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
];
?>

<?= $this->include('admin/templates/header') ?>
<?= $this->include('admin/templates/sidebar') ?>
<?= $this->include('admin/templates/navbar') ?>

<!-- Main Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Rekap Jam Kerja</h1>
        <div class="d-flex gap-2">
            <div class="btn-group" role="group">
                <button type="button" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm" onclick="exportExcel()">
                    <i class="fas fa-file-excel fa-sm text-white-50"></i> Export Excel
                </button>
                <button type="button" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm" onclick="toggleZeroAttendance()">
                    <i class="fas fa-eye-slash fa-sm text-white-50"></i> <span id="toggleText">Tampilkan Semua</span>
                </button>
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
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tahun">Tahun:</label>
                            <select class="form-control" id="tahun" name="tahun">
                                <?php foreach ($years as $year): ?>
                                    <option value="<?= $year; ?>" <?= ($year == $filter['tahun']) ? 'selected' : ''; ?>>
                                        <?= $year; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="bulan">Bulan:</label>
                            <select class="form-control" id="bulan" name="bulan">
                                <?php foreach ($months as $key => $month): ?>
                                    <option value="<?= $key; ?>" <?= ($key == $filter['bulan']) ? 'selected' : ''; ?>>
                                        <?= $month; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="departemen">Departemen:</label>
                            <select class="form-control" id="departemen" name="departemen">
                                <option value="">Semua Departemen</option>
                                <?php if(isset($departemen_list)): ?>
                                <?php foreach ($departemen_list as $dept): ?>
                                    <option value="<?= esc($dept['departemen']); ?>" <?= (isset($filter['departemen']) && $filter['departemen'] == $dept['departemen']) ? 'selected' : ''; ?>>
                                        <?= esc($dept['departemen']); ?>
                                    </option>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="jabatan">Jabatan:</label>
                            <select class="form-control" id="jabatan" name="jabatan">
                                <option value="">Semua Jabatan</option>
                                <?php if(isset($jabatan_list)): ?>
                                <?php foreach ($jabatan_list as $jab): ?>
                                    <option value="<?= esc($jab['jabatan']); ?>" <?= (isset($filter['jabatan']) && $filter['jabatan'] == $jab['jabatan']) ? 'selected' : ''; ?>>
                                        <?= esc($jab['jabatan']); ?>
                                    </option>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="<?= base_url('admin/jam-kerja/rekap'); ?>" class="btn btn-secondary">
                        <i class="fas fa-redo me-1"></i> Reset
                    </a>
                    <button type="button" class="btn btn-info" onclick="printReport()">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics -->
    <?php if (isset($statistics)): ?>
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Karyawan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $statistics['total_karyawan']; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                                Total Jam Kerja
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $statistics['total_jam_kerja_display']; ?>
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
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Lembur
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $statistics['total_lembur_display']; ?>
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
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Rata-rata per Karyawan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $statistics['rata_rata_jam_kerja_display']; ?>
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
    <?php endif; ?>

    <!-- Summary -->
    <?php 
    // Filter data: hanya tampilkan yang memiliki kehadiran > 0
    $rekap_with_attendance = [];
    if (isset($rekap) && !empty($rekap)) {
        foreach ($rekap as $r) {
            if ($r['hari_hadir'] > 0) {
                $rekap_with_attendance[] = $r;
            }
        }
    }
    
    $show_all = isset($_GET['show_all']) && $_GET['show_all'] == '1';
    $display_rekap = $show_all ? $rekap : $rekap_with_attendance;
    ?>
    
    <?php if (isset($display_rekap) && !empty($display_rekap)): ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                Rekap Bulan <?= $months[$filter['bulan']] . ' ' . $filter['tahun']; ?>
                <small class="text-muted ms-2">
                    (<?= count($display_rekap); ?> karyawan<?= $show_all ? '' : ' dengan kehadiran'; ?>)
                    <?php if (!$show_all && isset($rekap) && count($rekap_with_attendance) < count($rekap)): ?>
                        <span class="badge bg-secondary ms-2">
                            <?= count($rekap) - count($rekap_with_attendance); ?> tersembunyi
                        </span>
                    <?php endif; ?>
                </small>
            </h6>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="showZeroAttendance" <?= $show_all ? 'checked' : ''; ?> onchange="toggleZeroAttendance()">
                <label class="form-check-label" for="showZeroAttendance">
                    Tampilkan semua
                </label>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="rekapTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>NIK</th>
                            <th>Nama Karyawan</th>
                            <th>Jabatan</th>
                            <th>Departemen</th>
                            <th class="text-center">Total Hari</th>
                            <th class="text-center">Hadir</th>
                            <th class="text-center">Total Jam Kerja</th>
                            <th class="text-center">Rata per Hari</th>
                            <th class="text-center">Total Lembur</th>
                            <th class="text-center">Persentase</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($display_rekap as $r): ?>
                        <?php 
                        $persentase = $r['total_hari'] > 0 ? ($r['hari_hadir'] / $r['total_hari']) * 100 : 0;
                        $badgeClass = $persentase >= 80 ? 'bg-success' : ($persentase >= 60 ? 'bg-warning' : 'bg-danger');
                        ?>
                        <tr class="<?= $r['hari_hadir'] == 0 ? 'table-secondary text-muted' : ''; ?>">
                            <td><?= $no++; ?></td>
                            <td>
                                <strong><?= esc($r['karyawan']['nik']); ?></strong>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="ms-2">
                                        <div class="fw-bold"><?= esc($r['karyawan']['nama_lengkap']); ?></div>
                                        <small class="text-muted"><?= esc($r['karyawan']['email'] ?? ''); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><?= esc($r['karyawan']['jabatan']); ?></td>
                            <td>
                                <span class="badge bg-info text-white"><?= esc($r['karyawan']['departemen']); ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary"><?= $r['total_hari']; ?></span>
                            </td>
                            <td class="text-center">
                                <?php if ($r['hari_hadir'] > 0): ?>
                                    <span class="badge <?= $badgeClass; ?>">
                                        <?= $r['hari_hadir']; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">0</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center fw-bold">
                                <?= $r['total_jam_kerja_display']; ?>
                            </td>
                            <td class="text-center">
                                <small><?= $r['rata_per_hari_display']; ?></small>
                            </td>
                            <td class="text-center">
                                <?php if ($r['total_lembur'] > 0): ?>
                                    <span class="badge bg-warning text-dark"><?= $r['total_lembur_display']; ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar <?= 
                                        $persentase >= 80 ? 'bg-success' : 
                                        ($persentase >= 60 ? 'bg-warning' : 'bg-danger') 
                                    ?>" 
                                    role="progressbar" 
                                    style="width: <?= min($persentase, 100); ?>%" 
                                    aria-valuenow="<?= $persentase; ?>" 
                                    aria-valuemin="0" 
                                    aria-valuemax="100">
                                        <?= number_format($persentase, 1); ?>%
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <a href="<?= base_url('admin/jam-kerja/detail/' . $r['karyawan']['id']); ?>" 
                                   class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-dark">
                            <th colspan="5" class="text-end">Total:</th>
                            <th class="text-center"><?= array_sum(array_column($display_rekap, 'total_hari')); ?></th>
                            <th class="text-center"><?= array_sum(array_column($display_rekap, 'hari_hadir')); ?></th>
                            <th class="text-center fw-bold">
                                <?php 
                                $totalJam = array_sum(array_column($display_rekap, 'total_jam_kerja'));
                                $jam = floor($totalJam);
                                $menit = round(($totalJam - $jam) * 60);
                                if ($jam > 0 && $menit > 0) {
                                    echo "{$jam} jam {$menit} menit";
                                } elseif ($jam > 0) {
                                    echo "{$jam} jam";
                                } elseif ($menit > 0) {
                                    echo "{$menit} menit";
                                } else {
                                    echo '-';
                                }
                                ?>
                            </th>
                            <th class="text-center">
                                <?php 
                                $rataTotal = count($display_rekap) > 0 ? 
                                    array_sum(array_column($display_rekap, 'rata_per_hari')) / count($display_rekap) : 0;
                                $jam = floor($rataTotal);
                                $menit = round(($rataTotal - $jam) * 60);
                                if ($jam > 0 && $menit > 0) {
                                    echo "{$jam} jam {$menit} mnt";
                                } elseif ($jam > 0) {
                                    echo "{$jam} jam";
                                } elseif ($menit > 0) {
                                    echo "{$menit} mnt";
                                } else {
                                    echo '-';
                                }
                                ?>
                            </th>
                            <th class="text-center">
                                <?php 
                                $totalLembur = array_sum(array_column($display_rekap, 'total_lembur'));
                                if ($totalLembur > 0) {
                                    $jam = floor($totalLembur);
                                    $menit = round(($totalLembur - $jam) * 60);
                                    if ($jam > 0 && $menit > 0) {
                                        echo "{$jam} jam {$menit} mnt";
                                    } elseif ($jam > 0) {
                                        echo "{$jam} jam";
                                    } else {
                                        echo "{$menit} mnt";
                                    }
                                } else {
                                    echo '-';
                                }
                                ?>
                            </th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <?php if (!$show_all && isset($rekap) && count($rekap_with_attendance) < count($rekap)): ?>
            <div class="alert alert-info mt-3">
                <i class="fas fa-info-circle me-2"></i>
                Menampilkan <strong><?= count($rekap_with_attendance); ?></strong> karyawan dengan kehadiran. 
                <strong><?= count($rekap) - count($rekap_with_attendance); ?></strong> karyawan tanpa kehadiran tidak ditampilkan.
                <a href="?<?= http_build_query(array_merge($filter, ['show_all' => '1'])); ?>" class="alert-link ms-2">
                    Tampilkan semua
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php else: ?>
    <div class="card shadow">
        <div class="card-body text-center py-5">
            <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Tidak ada data rekap ditemukan</h5>
            <p class="text-muted">Silakan pilih periode lain</p>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#rekapTable').DataTable({
        "pageLength": 25,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
        "language": {
            "search": "Cari:",
            "lengthMenu": "Tampilkan _MENU_ data per halaman",
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
            { 
                "orderable": false, 
                "targets": [0, 11] 
            },
            {
                "className": "dt-center",
                "targets": [5, 6, 7, 8, 9, 10, 11]
            }
        ],
        "order": [[2, 'asc']], // Sort by nama
        "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
               "<'row'<'col-sm-12'tr>>" +
               "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
    });

    // Auto-hide alerts
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);
    
    // Update toggle text
    updateToggleText();
});

// Update toggle button text
function updateToggleText() {
    const checkbox = document.getElementById('showZeroAttendance');
    const toggleText = document.getElementById('toggleText');
    if (toggleText) {
        toggleText.textContent = checkbox.checked ? 'Sembunyikan 0' : 'Tampilkan Semua';
    }
}

// Toggle zero attendance display
function toggleZeroAttendance() {
    const checkbox = document.getElementById('showZeroAttendance');
    const tahun = document.getElementById('tahun').value;
    const bulan = document.getElementById('bulan').value;
    const departemen = document.getElementById('departemen').value;
    const jabatan = document.getElementById('jabatan').value;
    
    let url = '<?= base_url("admin/jam-kerja/rekap"); ?>?' +
        'tahun=' + encodeURIComponent(tahun) +
        '&bulan=' + encodeURIComponent(bulan) +
        '&show_all=' + (checkbox.checked ? '1' : '0');
    
    if (departemen) {
        url += '&departemen=' + encodeURIComponent(departemen);
    }
    
    if (jabatan) {
        url += '&jabatan=' + encodeURIComponent(jabatan);
    }
    
    window.location.href = url;
}

// Export to Excel
function exportExcel() {
    const tahun = document.getElementById('tahun').value;
    const bulan = document.getElementById('bulan').value;
    const departemen = document.getElementById('departemen').value;
    const jabatan = document.getElementById('jabatan').value;
    const showAll = document.getElementById('showZeroAttendance').checked ? '1' : '0';
    
    let url = '<?= base_url("admin/jam-kerja/export/excel"); ?>?' +
        'tahun=' + encodeURIComponent(tahun) +
        '&bulan=' + encodeURIComponent(bulan) +
        '&type=rekap' +
        '&show_all=' + showAll;
    
    if (departemen) {
        url += '&departemen=' + encodeURIComponent(departemen);
    }
    
    if (jabatan) {
        url += '&jabatan=' + encodeURIComponent(jabatan);
    }
    
    window.open(url, '_blank');
}

// Print Report
function printReport() {
    const tahun = document.getElementById('tahun').value;
    const bulan = document.getElementById('bulan').value;
    const departemen = document.getElementById('departemen').value;
    const jabatan = document.getElementById('jabatan').value;
    const showAll = document.getElementById('showZeroAttendance').checked ? '1' : '0';
    
    let url = '<?= base_url("admin/jam-kerja/rekap"); ?>?' +
        'tahun=' + encodeURIComponent(tahun) +
        '&bulan=' + encodeURIComponent(bulan) +
        '&print=true' +
        '&show_all=' + showAll;
    
    if (departemen) {
        url += '&departemen=' + encodeURIComponent(departemen);
    }
    
    if (jabatan) {
        url += '&jabatan=' + encodeURIComponent(jabatan);
    }
    
    const printWindow = window.open(url, '_blank');
    if (printWindow) {
        printWindow.onload = function() {
            printWindow.print();
        };
    }
}
</script>

<style>
/* Custom styles for rekap page */
.table-secondary {
    opacity: 0.7;
}

.progress {
    min-width: 80px;
}

.badge {
    font-size: 0.85em;
    padding: 0.4em 0.6em;
}

.form-switch .form-check-input {
    width: 3em;
    height: 1.5em;
    cursor: pointer;
}

.table th {
    white-space: nowrap;
}

.table td {
    vertical-align: middle;
}
</style>

<?= $this->include('admin/templates/footer') ?>