<?php
// C:\xampp\htdocs\cdwnet\app\Views\admin\manajemen_cuti\index.php
$title = 'Manajemen Cuti';
$active = 'cuti';
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
        <h1 class="h3 mb-0 text-gray-800">Manajemen Cuti</h1>
        <div class="d-flex gap-2">
            <a href="<?= base_url('admin/cuti/create') ?>" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Ajukan Cuti
            </a>
            <a href="<?= base_url('admin/cuti/calendar') ?>" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm">
                <i class="fas fa-calendar-alt fa-sm text-white-50"></i> Kalendar
            </a>
            <button type="button" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm" onclick="exportExcel()">
                <i class="fas fa-file-excel fa-sm text-white-50"></i> Export
            </button>
        </div>
    </div>

    <!-- Statistics -->
    <?php if (isset($stats) && !empty($stats)): ?>
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Pengajuan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_pengajuan'] ?? 0; ?></div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_disetujui'] ?? 0; ?></div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_menunggu'] ?? 0; ?></div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_ditolak'] ?? 0; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Search and Filter -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Data Cuti</h6>
        </div>
        <div class="card-body">
            <form method="get" action="<?= base_url('admin/cuti'); ?>">
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
                    <div class="col-md-3">
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
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Status:</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">Semua Status</option>
                                <option value="Menunggu" <?= (isset($filter['status']) && $filter['status'] == 'Menunggu') ? 'selected' : ''; ?>>Menunggu</option>
                                <option value="Disetujui HRD" <?= (isset($filter['status']) && $filter['status'] == 'Disetujui HRD') ? 'selected' : ''; ?>>Disetujui HRD</option>
                                <option value="Disetujui Atasan" <?= (isset($filter['status']) && $filter['status'] == 'Disetujui Atasan') ? 'selected' : ''; ?>>Disetujui Atasan</option>
                                <option value="Ditolak" <?= (isset($filter['status']) && $filter['status'] == 'Ditolak') ? 'selected' : ''; ?>>Ditolak</option>
                                <option value="Dibatalkan" <?= (isset($filter['status']) && $filter['status'] == 'Dibatalkan') ? 'selected' : ''; ?>>Dibatalkan</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="jenis_cuti">Jenis Cuti:</label>
                            <select class="form-control" id="jenis_cuti" name="jenis_cuti">
                                <option value="">Semua Jenis</option>
                                <option value="Tahunan" <?= (isset($filter['jenis_cuti']) && $filter['jenis_cuti'] == 'Tahunan') ? 'selected' : ''; ?>>Tahunan</option>
                                <option value="Hamil" <?= (isset($filter['jenis_cuti']) && $filter['jenis_cuti'] == 'Hamil') ? 'selected' : ''; ?>>Hamil</option>
                                <option value="Sakit" <?= (isset($filter['jenis_cuti']) && $filter['jenis_cuti'] == 'Sakit') ? 'selected' : ''; ?>>Sakit</option>
                                <option value="Khusus" <?= (isset($filter['jenis_cuti']) && $filter['jenis_cuti'] == 'Khusus') ? 'selected' : ''; ?>>Khusus</option>
                                <option value="Lainnya" <?= (isset($filter['jenis_cuti']) && $filter['jenis_cuti'] == 'Lainnya') ? 'selected' : ''; ?>>Lainnya</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="d-flex align-items-end h-100">
                            <div class="form-group w-100">
                                <label>&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter me-1"></i> Filter
                                    </button>
                                    <a href="<?= base_url('admin/cuti'); ?>" class="btn btn-secondary">
                                        <i class="fas fa-redo me-1"></i> Reset
                                    </a>
<a href="<?= base_url('admin/cuti/my-cuti'); ?>" class="btn btn-info">
    <i class="fas fa-user me-1"></i> Cuti Saya
</a>
                                    <a href="<?= base_url('admin/cuti/pending'); ?>" class="btn btn-warning">
                                        <i class="fas fa-clock me-1"></i> Menunggu
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pengajuan Cuti</h6>
            <div>
                <small class="text-muted">
                    Total <?= count($cuti ?? []); ?> data ditemukan
                </small>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0" id="cutiTable">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Nomor Cuti</th>
                            <th>Karyawan</th>
                            <th>Jenis</th>
                            <th>Periode</th>
                            <th>Lama (Hari)</th>
                            <th>Status</th>
                            <th>Tanggal Pengajuan</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($cuti) && !empty($cuti)): ?>
                            <?php $no = 1; ?>
                            <?php foreach ($cuti as $c): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= esc($c['nomor_cuti']); ?></td>
                                <td>
                                    <?= esc($c['nama_lengkap']); ?>
                                    <br>
                                    <small class="text-muted">NIK: <?= esc($c['nik']); ?></small>
                                </td>
                                <td><?= esc($c['jenis_cuti']); ?></td>
                                <td>
                                    <?= date('d/m/Y', strtotime($c['tanggal_mulai'])); ?> 
                                    <br>
                                    <small>s/d <?= date('d/m/Y', strtotime($c['tanggal_selesai'])); ?></small>
                                </td>
                                <td class="text-center"><?= $c['lama_hari']; ?> hari</td>
                                <td>
                                    <?php 
                                    $badgeClass = 'secondary';
                                    if ($c['status'] === 'Disetujui HRD' || $c['status'] === 'Disetujui Atasan') {
                                        $badgeClass = 'success';
                                    } elseif ($c['status'] === 'Ditolak') {
                                        $badgeClass = 'danger';
                                    } elseif ($c['status'] === 'Menunggu') {
                                        $badgeClass = 'warning';
                                    } elseif ($c['status'] === 'Dibatalkan') {
                                        $badgeClass = 'secondary';
                                    }
                                    ?>
                                    <span class="badge bg-<?= $badgeClass; ?>"><?= $c['status']; ?></span>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($c['tanggal_pengajuan'])); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('admin/cuti/show/' . $c['id']); ?>" 
                                           class="btn btn-sm btn-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if (in_array($c['status'], ['Draft', 'Menunggu'])): ?>
                                        <a href="<?= base_url('admin/cuti/edit/' . $c['id']); ?>" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php endif; ?>
                                        <?php if (in_array(session()->get('role'), ['admin', 'hrd', 'atasan']) && $c['status'] === 'Menunggu'): ?>
                                        <button onclick="approveCuti(<?= $c['id']; ?>)" 
                                                class="btn btn-sm btn-success" title="Setujui">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Tidak ada data cuti ditemukan.</p>
                                <a href="<?= base_url('admin/cuti/create'); ?>" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> Ajukan Cuti
                                </a>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk approve/reject -->
<div class="modal fade" id="approvalModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Persetujuan Cuti</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="approvalForm" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="action">Aksi:</label>
                        <select class="form-control" id="action" name="action" required>
                            <option value="">Pilih Aksi</option>
                            <option value="approve">Setujui</option>
                            <option value="reject">Tolak</option>
                        </select>
                    </div>
                    <div class="form-group" id="reasonGroup" style="display: none;">
                        <label for="alasan_penolakan">Alasan Penolakan:</label>
                        <textarea class="form-control" id="alasan_penolakan" name="alasan_penolakan" 
                                  rows="3" placeholder="Masukkan alasan penolakan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Initialize DataTable
$(document).ready(function() {
    $('#cutiTable').DataTable({
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
        "columnDefs": [
            { "orderable": false, "targets": [0, 8] }
        ],
        "order": [[1, 'desc']] // Sort by nomor cuti
    });

    // Initialize Select2
    $('.select2').select2({
        placeholder: 'Pilih karyawan',
        allowClear: true
    });

    // Auto-hide alerts
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);
});

// Action for approval modal
let currentCutiId = null;

function approveCuti(id) {
    currentCutiId = id;
    $('#approvalModal').modal('show');
}

// Toggle reason field
$('#action').change(function() {
    if ($(this).val() === 'reject') {
        $('#reasonGroup').show();
        $('#alasan_penolakan').prop('required', true);
    } else {
        $('#reasonGroup').hide();
        $('#alasan_penolakan').prop('required', false);
    }
});

// Handle approval form submission
$('#approvalForm').submit(function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const action = formData.get('action');
    
    let url = '';
    if (action === 'approve') {
        url = '<?= base_url("admin/cuti/approve/"); ?>' + currentCutiId;
    } else if (action === 'reject') {
        url = '<?= base_url("admin/cuti/reject/"); ?>' + currentCutiId;
    }
    
    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            $('#approvalModal').modal('hide');
            location.reload();
        },
        error: function(xhr) {
            alert('Terjadi kesalahan. Silakan coba lagi.');
        }
    });
});

// Export to Excel
function exportExcel() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const karyawanId = document.getElementById('karyawan_id').value;
    const status = document.getElementById('status').value;
    const jenisCuti = document.getElementById('jenis_cuti').value;
    
    let url = '<?= base_url("admin/cuti/export/excel"); ?>?' +
        'start_date=' + encodeURIComponent(startDate) +
        '&end_date=' + encodeURIComponent(endDate);
    
    if (karyawanId) {
        url += '&karyawan_id=' + encodeURIComponent(karyawanId);
    }
    
    if (status) {
        url += '&status=' + encodeURIComponent(status);
    }
    
    if (jenisCuti) {
        url += '&jenis_cuti=' + encodeURIComponent(jenisCuti);
    }
    
    window.open(url, '_blank');
}
</script>

<?= $this->include('admin/templates/footer') ?>