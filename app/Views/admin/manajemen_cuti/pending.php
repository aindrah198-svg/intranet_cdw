<?php
// C:\xampp\htdocs\cdwnet\app\Views\admin\manajemen_cuti\pending.php
$title = 'Cuti Menunggu Persetujuan';
$active = 'cuti';
$css = [
    'https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css'
];
$scripts = [
    'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js',
    'https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js'
];
?>

<?= $this->include('admin/templates/header') ?>
<?= $this->include('admin/templates/sidebar') ?>
<?= $this->include('admin/templates/navbar') ?>

<!-- Main Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Cuti Menunggu Persetujuan</h1>
        <div>
            <a href="<?= base_url('admin/cuti'); ?>" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Daftar
            </a>
            <a href="<?= base_url('admin/cuti/create'); ?>" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Ajukan Cuti Baru
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Menunggu
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($cuti); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Hari Ini
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php 
                                $todayCount = 0;
                                foreach ($cuti as $c) {
                                    if (date('Y-m-d', strtotime($c['tanggal_pengajuan'])) == date('Y-m-d')) {
                                        $todayCount++;
                                    }
                                }
                                echo $todayCount;
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Cuti Tahunan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php 
                                $tahunanCount = 0;
                                foreach ($cuti as $c) {
                                    if ($c['jenis_cuti'] === 'Tahunan') {
                                        $tahunanCount++;
                                    }
                                }
                                echo $tahunanCount;
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Urgent (≤ 2 hari)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php 
                                $urgentCount = 0;
                                $today = new DateTime();
                                foreach ($cuti as $c) {
                                    $startDate = new DateTime($c['tanggal_mulai']);
                                    $interval = $today->diff($startDate);
                                    if ($interval->days <= 2 && !$interval->invert) {
                                        $urgentCount++;
                                    }
                                }
                                echo $urgentCount;
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Cuti Menunggu Persetujuan</h6>
            <div>
                <span class="badge bg-warning">Menunggu HRD</span>
                <?php if (session()->get('role') === 'hrd'): ?>
                    <span class="badge bg-secondary ms-2">Menunggu Atasan</span>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($cuti)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5 class="text-success">Tidak ada cuti yang menunggu persetujuan</h5>
                    <p class="text-muted">Semua pengajuan cuti sudah diproses.</p>
                    <a href="<?= base_url('admin/cuti'); ?>" class="btn btn-primary">
                        <i class="fas fa-list me-1"></i> Lihat Semua Cuti
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0" id="pendingTable">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>Nomor Cuti</th>
                                <th>Karyawan</th>
                                <th>Jenis</th>
                                <th>Periode</th>
                                <th>Lama (Hari)</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Prioritas</th>
                                <th width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($cuti as $c): ?>
                            <?php 
                            // Tentukan prioritas
                            $today = new DateTime();
                            $startDate = new DateTime($c['tanggal_mulai']);
                            $interval = $today->diff($startDate);
                            $daysUntilStart = $interval->days;
                            
                            $priority = 'normal';
                            $priorityBadge = 'secondary';
                            $priorityText = 'Normal';
                            
                            if ($c['jenis_cuti'] === 'Sakit') {
                                $priority = 'high';
                                $priorityBadge = 'danger';
                                $priorityText = 'Sakit';
                            } elseif ($c['jenis_cuti'] === 'Hamil') {
                                $priority = 'high';
                                $priorityBadge = 'danger';
                                $priorityText = 'Hamil';
                            } elseif ($daysUntilStart <= 2 && !$interval->invert) {
                                $priority = 'urgent';
                                $priorityBadge = 'warning';
                                $priorityText = 'Urgent';
                            } elseif ($c['lama_hari'] > 10) {
                                $priority = 'high';
                                $priorityBadge = 'info';
                                $priorityText = 'Panjang';
                            }
                            ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= esc($c['nomor_cuti']); ?></td>
                                <td>
                                    <strong><?= esc($c['nama_lengkap']); ?></strong><br>
                                    <small class="text-muted"><?= esc($c['jabatan']); ?> | <?= esc($c['departemen']); ?></small>
                                </td>
                                <td><?= esc($c['jenis_cuti']); ?></td>
                                <td>
                                    <?= date('d/m/Y', strtotime($c['tanggal_mulai'])); ?><br>
                                    <small>s/d</small><br>
                                    <?= date('d/m/Y', strtotime($c['tanggal_selesai'])); ?>
                                </td>
                                <td class="text-center"><?= $c['lama_hari']; ?> hari</td>
                                <td><?= date('d/m/Y H:i', strtotime($c['tanggal_pengajuan'])); ?></td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $priorityBadge; ?>">
                                        <?= $priorityText; ?>
                                    </span>
                                    <?php if ($priority === 'urgent'): ?>
                                        <br><small><?= $daysUntilStart; ?> hari lagi</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('admin/cuti/show/' . $c['id']); ?>" 
                                           class="btn btn-sm btn-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if (in_array(session()->get('role'), ['admin', 'hrd'])): ?>
                                        <a href="<?= base_url('admin/cuti/approve/' . $c['id']); ?>" 
                                           class="btn btn-sm btn-success" title="Setujui"
                                           onclick="return confirm('Setujui pengajuan cuti ini?')">
                                            <i class="fas fa-check"></i>
                                        </a>
                                        <button onclick="rejectCuti(<?= $c['id']; ?>)" 
                                                class="btn btn-sm btn-danger" title="Tolak">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Quick Actions -->
                <?php if (in_array(session()->get('role'), ['admin', 'hrd'])): ?>
                <div class="card mt-4 border-left-primary">
                    <div class="card-body">
                        <h6 class="font-weight-bold text-primary mb-3">Aksi Cepat</h6>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-success" onclick="approveAll()">
                                <i class="fas fa-check-double me-1"></i> Setujui Semua Urgent
                            </button>
                            <button type="button" class="btn btn-info" onclick="viewCalendar()">
                                <i class="fas fa-calendar-alt me-1"></i> Lihat Kalendar
                            </button>
                            <button type="button" class="btn btn-warning" onclick="exportPending()">
                                <i class="fas fa-file-excel me-1"></i> Export Excel
                            </button>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Penolakan Cuti</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="rejectForm" method="post">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <p id="rejectMessage">Masukkan alasan penolakan:</p>
                    <div class="form-group">
                        <label for="reject_reason" class="text-danger">Alasan Penolakan *</label>
                        <textarea class="form-control" id="reject_reason" name="alasan_penolakan" 
                                  rows="4" placeholder="Jelaskan alasan penolakan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
#pendingTable tbody tr:hover {
    background-color: #f8f9fa;
    cursor: pointer;
}
</style>

<script>
$(document).ready(function() {
    $('#pendingTable').DataTable({
        "pageLength": 10,
        "lengthMenu": [10, 25, 50, 100],
        "order": [[7, 'asc'], [5, 'desc']], // Urutkan berdasarkan prioritas, lalu lama hari
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
        }
    });
    
    // Click row to view detail
    $('#pendingTable tbody tr').click(function(e) {
        if (!$(e.target).closest('button, a').length) {
            const detailUrl = $(this).find('a[title="Detail"]').attr('href');
            if (detailUrl) {
                window.location.href = detailUrl;
            }
        }
    });
    
    // Auto-hide alerts
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);
});

let currentCutiId = null;

function rejectCuti(id) {
    currentCutiId = id;
    $('#rejectModal').modal('show');
}

$('#rejectForm').submit(function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const formAction = '<?= base_url("admin/cuti/reject/"); ?>' + currentCutiId;
    
    $.ajax({
        url: formAction,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            $('#rejectModal').modal('hide');
            location.reload();
        },
        error: function(xhr) {
            alert('Terjadi kesalahan. Silakan coba lagi.');
        }
    });
});

function approveAll() {
    if (confirm('Setujui semua pengajuan cuti dengan status Urgent (≤ 2 hari lagi)?')) {
        // Get all urgent cuti IDs
        const urgentIds = [];
        $('#pendingTable tbody tr').each(function() {
            const priorityText = $(this).find('.badge').text().trim();
            if (priorityText === 'Urgent') {
                const detailUrl = $(this).find('a[title="Detail"]').attr('href');
                if (detailUrl) {
                    const cutiId = detailUrl.split('/').pop();
                    urgentIds.push(cutiId);
                }
            }
        });
        
        if (urgentIds.length === 0) {
            alert('Tidak ada cuti dengan status Urgent.');
            return;
        }
        
        // Process approvals
        let approvedCount = 0;
        urgentIds.forEach(function(id) {
            $.ajax({
                url: '<?= base_url("admin/cuti/approve/"); ?>' + id,
                type: 'POST',
                data: {
                    '<?= csrf_token(); ?>': '<?= csrf_hash(); ?>'
                },
                success: function() {
                    approvedCount++;
                    if (approvedCount === urgentIds.length) {
                        alert('Berhasil menyetujui ' + approvedCount + ' cuti urgent.');
                        location.reload();
                    }
                }
            });
        });
    }
}

function viewCalendar() {
    window.location.href = '<?= base_url("admin/cuti/calendar"); ?>';
}

function exportPending() {
    window.location.href = '<?= base_url("admin/cuti/export/excel"); ?>?status=Menunggu';
}
</script>

<?= $this->include('admin/templates/footer') ?>