<?php
// C:\xampp\htdocs\intranet_cdw\app\Views\admin\jam_kerja\index.php

$title = 'Jam Kerja Karyawan';
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
        <h1 class="h3 mb-0 text-gray-800">Jam Kerja Karyawan</h1>
        <div class="d-flex gap-2">
            <a href="<?= base_url('admin/jam-kerja/create') ?>" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Manual
            </a>
            <button type="button" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm" onclick="exportExcel()">
                <i class="fas fa-file-excel fa-sm text-white-50"></i> Export Excel
            </button>
            <a href="<?= base_url('admin/jam-kerja/rekap') ?>" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm">
                <i class="fas fa-chart-bar fa-sm text-white-50"></i> Rekap Bulanan
            </a>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Jam Kerja</h6>
        </div>
        <div class="card-body">
            <form method="get" action="<?= base_url('admin/jam-kerja'); ?>">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="start_date">Dari Tanggal:</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" 
                                   value="<?= esc($filter['start_date'] ?? date('Y-m-01')); ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="end_date">Sampai Tanggal:</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" 
                                   value="<?= esc($filter['end_date'] ?? date('Y-m-d')); ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="karyawan_id">Karyawan:</label>
                            <select class="form-control select2" id="karyawan_id" name="karyawan_id">
                                <option value="">Semua Karyawan</option>
                                <?php if(isset($karyawan)): ?>
                                <?php foreach ($karyawan as $k): ?>
                                    <option value="<?= $k['id']; ?>" <?= (isset($filter['karyawan_id']) && $filter['karyawan_id'] == $k['id']) ? 'selected' : ''; ?>>
                                        <?= esc($k['nik']); ?> - <?= esc($k['nama_lengkap']); ?>
                                    </option>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
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
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="status">Status:</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">Semua Status</option>
                                <option value="Hadir" <?= (isset($filter['status']) && $filter['status'] == 'Hadir') ? 'selected' : ''; ?>>Hadir</option>
                                <option value="Izin" <?= (isset($filter['status']) && $filter['status'] == 'Izin') ? 'selected' : ''; ?>>Izin</option>
                                <option value="Sakit" <?= (isset($filter['status']) && $filter['status'] == 'Sakit') ? 'selected' : ''; ?>>Sakit</option>
                                <option value="Cuti" <?= (isset($filter['status']) && $filter['status'] == 'Cuti') ? 'selected' : ''; ?>>Cuti</option>
                                <option value="Alpha" <?= (isset($filter['status']) && $filter['status'] == 'Alpha') ? 'selected' : ''; ?>>Alpha</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="<?= base_url('admin/jam-kerja'); ?>" class="btn btn-secondary">
                        <i class="fas fa-redo me-1"></i> Reset
                    </a>
                    <button type="button" class="btn btn-info" onclick="printReport()">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                    <a href="<?= base_url('admin/jam-kerja/import') ?>" class="btn btn-warning">
                        <i class="fas fa-upload me-1"></i> Import
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics -->
    <?php if (isset($summary) && !empty($summary)): ?>
    <?php 
        $totalKaryawan = count($summary);
        $totalJamKerja = array_sum(array_column($summary, 'total_jam_kerja'));
        $totalLembur = array_sum(array_column($summary, 'total_lembur'));
        $totalTerlambat = array_sum(array_column($summary, 'total_terlambat'));
        $totalHariHadir = array_sum(array_column($summary, 'hari_hadir'));
        $rataJamPerKaryawan = $totalKaryawan > 0 ? $totalJamKerja / $totalKaryawan : 0;
        
        // Hitung persentase kehadiran
        $totalHariPerKaryawan = $totalKaryawan > 0 ? array_sum(array_column($summary, 'total_hari')) / $totalKaryawan : 0;
        $persentaseHadir = $totalHariPerKaryawan > 0 ? ($totalHariHadir / $totalKaryawan) / $totalHariPerKaryawan * 100 : 0;
    ?>
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Karyawan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalKaryawan; ?></div>
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
                                <?php 
                                $jam = floor($totalJamKerja);
                                $menit = round(($totalJamKerja - $jam) * 60);
                                echo $jam > 0 ? "{$jam} jam" : "{$menit} menit";
                                ?>
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
                                <?php 
                                $jam = floor($totalLembur);
                                $menit = round(($totalLembur - $jam) * 60);
                                echo $jam > 0 ? "{$jam} jam" : "{$menit} menit";
                                ?>
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
                                Persentase Hadir
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($persentaseHadir, 1); ?>%
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

    <!-- Data Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Rekapitulasi Jam Kerja Karyawan</h6>
            <div>
                <small class="text-muted">
                    Periode: <?= date('d/m/Y', strtotime($filter['start_date'])); ?> - <?= date('d/m/Y', strtotime($filter['end_date'])); ?>
                </small>
                <small class="ms-3 text-muted">
                    Total Data: <?= isset($summary) ? count($summary) : 0; ?>
                </small>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0" id="jamKerjaTable">
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
                            <th class="text-center">Terlambat</th>
                            <th width="120" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($summary) && !empty($summary)): ?>
                            <?php $no = 1; ?>
                            <?php foreach ($summary as $s): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td>
                                    <strong><?= esc($s['nik']); ?></strong>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="ms-2">
                                            <div class="fw-bold"><?= esc($s['nama_lengkap']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?= esc($s['jabatan']); ?></td>
                                <td>
                                    <span class="badge bg-info text-white"><?= esc($s['departemen']); ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary"><?= $s['total_hari']; ?></span>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $persentase = $s['total_hari'] > 0 ? ($s['hari_hadir'] / $s['total_hari']) * 100 : 0;
                                    $badgeClass = $persentase >= 80 ? 'bg-success' : ($persentase >= 60 ? 'bg-warning' : 'bg-danger');
                                    ?>
                                    <span class="badge <?= $badgeClass; ?>">
                                        <?= $s['hari_hadir']; ?> 
                                        <small>(<?= number_format($persentase, 0); ?>%)</small>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold"><?= $s['total_jam_kerja_display']; ?></span>
                                </td>
                                <td class="text-center">
                                    <small><?= $s['rata_per_hari_display']; ?></small>
                                </td>
                                <td class="text-center">
                                    <?php if ($s['total_lembur'] > 0): ?>
                                        <span class="badge bg-warning text-dark"><?= $s['total_lembur_display']; ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($s['total_terlambat'] > 0): ?>
                                        <span class="badge bg-danger">
                                            <?= number_format($s['total_terlambat']); ?> menit
                                        </span>
                                    <?php else: ?>
                                        <span class="text-success">
                                            <i class="fas fa-check"></i> Tepat waktu
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('admin/jam-kerja/detail/' . $s['karyawan_id']); ?>" 
                                           class="btn btn-sm btn-info" title="Detail" data-bs-toggle="tooltip">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('admin/jam-kerja/by-karyawan/' . $s['karyawan_id']); ?>" 
                                           class="btn btn-sm btn-primary" title="Lihat Detail" data-bs-toggle="tooltip">
                                            <i class="fas fa-chart-bar"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="12" class="text-center py-5">
                                <div class="mb-3">
                                    <i class="fas fa-business-time fa-4x text-muted"></i>
                                </div>
                                <h5 class="text-muted">Tidak ada data jam kerja ditemukan</h5>
                                <p class="text-muted">
                                    Coba sesuaikan filter tanggal atau 
                                    <a href="<?= base_url('admin/jam-kerja/create'); ?>">tambah data manual</a>
                                </p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (isset($summary) && !empty($summary)): ?>
            <div class="mt-3 text-muted">
                <small>
                    <i class="fas fa-info-circle me-1"></i>
                    Keterangan: 
                    <span class="badge bg-success">Kehadiran ≥80%</span>
                    <span class="badge bg-warning text-dark">Kehadiran 60-79%</span>
                    <span class="badge bg-danger">Kehadiran <60%</span>
                </small>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Initialize DataTable
$(document).ready(function() {
    $('#jamKerjaTable').DataTable({
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
            },
            "processing": "Memproses..."
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
        "order": [[1, 'asc']], // Sort by NIK
        "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
               "<'row'<'col-sm-12'tr>>" +
               "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        "initComplete": function() {
            // Inisialisasi tooltip
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });

    // Initialize Select2
    $('.select2').select2({
        placeholder: 'Pilih karyawan',
        allowClear: true,
        width: '100%'
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);
});

// Export to Excel
function exportExcel() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const karyawanId = document.getElementById('karyawan_id').value;
    const departemen = document.getElementById('departemen').value;
    const status = document.getElementById('status').value;
    
    let url = '<?= base_url("admin/jam-kerja/export/excel"); ?>?' +
        'start_date=' + encodeURIComponent(startDate) +
        '&end_date=' + encodeURIComponent(endDate);
    
    if (karyawanId) {
        url += '&karyawan_id=' + encodeURIComponent(karyawanId);
    }
    
    if (departemen) {
        url += '&departemen=' + encodeURIComponent(departemen);
    }
    
    if (status) {
        url += '&status=' + encodeURIComponent(status);
    }
    
    // Show loading
    const exportBtn = event.target;
    const originalHtml = exportBtn.innerHTML;
    exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
    exportBtn.disabled = true;
    
    // Open in new tab for download
    setTimeout(function() {
        window.open(url, '_blank');
        
        // Restore button
        exportBtn.innerHTML = originalHtml;
        exportBtn.disabled = false;
    }, 1000);
}

// Print Report
function printReport() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const karyawanId = document.getElementById('karyawan_id').value;
    const departemen = document.getElementById('departemen').value;
    const status = document.getElementById('status').value;
    
    let url = '<?= base_url("admin/jam-kerja/export/print"); ?>?' +
        'start_date=' + encodeURIComponent(startDate) +
        '&end_date=' + encodeURIComponent(endDate);
    
    if (karyawanId) {
        url += '&karyawan_id=' + encodeURIComponent(karyawanId);
    }
    
    if (departemen) {
        url += '&departemen=' + encodeURIComponent(departemen);
    }
    
    if (status) {
        url += '&status=' + encodeURIComponent(status);
    }
    
    // Show loading
    const printBtn = event.target;
    const originalHtml = printBtn.innerHTML;
    printBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Membuat laporan...';
    printBtn.disabled = true;
    
    // Open in new tab for printing
    setTimeout(function() {
        const printWindow = window.open(url, '_blank');
        
        // Restore button
        printBtn.innerHTML = originalHtml;
        printBtn.disabled = false;
        
        if (printWindow) {
            printWindow.onload = function() {
                setTimeout(function() {
                    printWindow.print();
                }, 500);
            };
        }
    }, 1000);
}

// Auto refresh page every 5 minutes (300000 ms)
// setTimeout(function() {
//     location.reload();
// }, 300000);
</script>

<style>
/* Custom styles for Jam Kerja page */
.badge {
    font-size: 0.85em;
    padding: 0.4em 0.6em;
}

.table td {
    vertical-align: middle;
}

.select2-container--default .select2-selection--single {
    height: calc(2.25rem + 2px);
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: calc(2.25rem + 2px);
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: calc(2.25rem + 2px);
}

/* Highlight rows based on attendance percentage */
tr:hover {
    background-color: #f8f9fa !important;
}
</style>

<?= $this->include('admin/templates/footer') ?>