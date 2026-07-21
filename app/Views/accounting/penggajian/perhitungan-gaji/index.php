<?php
// app/Views/accounting/penggajian/perhitungan-gaji/index.php
$data['active'] = 'perhitungan-gaji';
$this->extend('accounting/templates/header');
?>

<?php $this->section('content'); ?>
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 text-gradient-accounting">Perhitungan Gaji Karyawan</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-2">
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/penggajian') ?>">Penggajian</a></li>
                    <li class="breadcrumb-item active">Perhitungan Gaji</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <a href="<?= site_url('accounting/penggajian/perhitungan-gaji/create') ?>" class="btn btn-accounting">
                <i class="fas fa-plus me-1"></i> Perhitungan Manual
            </a>
            <a href="<?= site_url('accounting/penggajian/perhitungan-gaji/hitung-massal?bulan=' . $bulan . '&tahun=' . $tahun) ?>" class="btn btn-accounting-outline">
                <i class="fas fa-chart-line me-1"></i> Hitung Massal
            </a>
            <button type="button" class="btn btn-accounting-outline dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-download me-1"></i> Export
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="<?= site_url('accounting/penggajian/perhitungan-gaji/export-excel?bulan=' . $bulan . '&tahun=' . $tahun . '&status=' . $status) ?>">
                        <i class="fas fa-file-excel me-2 text-success"></i> Export Excel
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="<?= site_url('accounting/penggajian/perhitungan-gaji/export-pdf?bulan=' . $bulan . '&tahun=' . $tahun . '&status=' . $status) ?>">
                        <i class="fas fa-file-pdf me-2 text-danger"></i> Export PDF
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Bulan</label>
                    <select name="bulan" class="form-select">
                        <?php foreach ($bulanOptions as $key => $val): ?>
                            <option value="<?= $key ?>" <?= $bulan == $key ? 'selected' : '' ?>>
                                <?= $val ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tahun</label>
                    <select name="tahun" class="form-select">
                        <?php foreach ($tahunOptions as $t): ?>
                            <option value="<?= $t ?>" <?= $tahun == $t ? 'selected' : '' ?>>
                                <?= $t ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <?php foreach ($statusOptions as $opt): ?>
                            <option value="<?= $opt ?>" <?= $status == $opt ? 'selected' : '' ?>>
                                <?= $opt ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Karyawan</label>
                    <select name="karyawan_id" class="form-select">
                        <option value="">Semua Karyawan</option>
                        <?php foreach ($karyawanOptions as $k): ?>
                            <option value="<?= $k['id'] ?>" <?= $karyawan_id == $k['id'] ? 'selected' : '' ?>>
                                <?= $k['nik'] ?> - <?= $k['nama_lengkap'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-accounting w-100">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow-sm h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Karyawan
                            </div>
                            <div class="h5 mb-0 font-weight-bold"><?= number_format($ringkasan['jumlah_karyawan'] ?? 0) ?></div>
                            <small class="text-muted">orang</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow-sm h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Gaji Bersih
                            </div>
                            <div class="h5 mb-0 font-weight-bold">Rp <?= number_format($ringkasan['total_gaji_bersih'] ?? 0, 0, ',', '.') ?></div>
                            <small class="text-muted">Total Pendapatan - Potongan</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow-sm h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Status Perhitungan
                            </div>
                            <div class="h5 mb-0 font-weight-bold">
                                <?= ($statusCount['dihitung'] ?? 0) + ($statusCount['disetujui'] ?? 0) ?> / <?= $statusCount['total'] ?? 0 ?>
                            </div>
                            <small class="text-muted">Selesai / Total</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow-sm h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Disetujui (Siap Bayar)
                            </div>
                            <div class="h5 mb-0 font-weight-bold"><?= $statusCount['disetujui'] ?? 0 ?></div>
                            <small class="text-muted">karyawan</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card">
        <div class="card-header bg-gradient-accounting text-white">
            <i class="fas fa-table me-2"></i> Daftar Perhitungan Gaji
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover accounting-table" id="dataTable">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="10%">Nomor</th>
                            <th width="10%">NIK</th>
                            <th width="20%">Nama Karyawan</th>
                            <th width="15%">Jabatan</th>
                            <th width="10%">Gaji Bersih</th>
                            <th width="10%">Status</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($perhitungan)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    Tidak ada data perhitungan gaji untuk periode ini
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($perhitungan as $item): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td class="text-center"><code><?= $item['nomor_perhitungan'] ?></code></td>
                                <td><?= $item['nik'] ?></td>
                                <td>
                                    <strong><?= $item['nama_lengkap'] ?></strong><br>
                                    <small class="text-muted"><?= $item['departemen'] ?></small>
                                </td>
                                <td><?= $item['jabatan'] ?></td>
                                <td class="text-end">
                                    <strong class="text-primary">Rp <?= number_format($item['gaji_bersih'], 0, ',', '.') ?></strong>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $badgeClass = match($item['status']) {
                                        'Disetujui' => 'success',
                                        'Dihitung' => 'info',
                                        'Ditolak' => 'danger',
                                        'Draft' => 'secondary',
                                        default => 'secondary'
                                    };
                                    ?>
                                    <span class="badge bg-<?= $badgeClass ?> px-3 py-2"><?= $item['status'] ?></span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="<?= site_url('accounting/penggajian/perhitungan-gaji/detail/' . $item['id']) ?>" 
                                           class="btn btn-sm btn-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($item['status'] == 'Draft'): ?>
                                            <a href="<?= site_url('accounting/penggajian/perhitungan-gaji/edit/' . $item['id']) ?>" 
                                               class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-success" 
                                                    onclick="hitungGaji(<?= $item['id'] ?>)" title="Hitung">
                                                <i class="fas fa-calculator"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="hapusPerhitungan(<?= $item['id'] ?>)" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php elseif ($item['status'] == 'Dihitung'): ?>
                                            <button type="button" class="btn btn-sm btn-success" 
                                                    onclick="approveGaji(<?= $item['id'] ?>)" title="Setujui">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="rejectGaji(<?= $item['id'] ?>)" title="Tolak">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if (isset($pager) && !empty($pager)): ?>
            <div class="mt-3">
                <?= view('accounting/templates/pagination', ['pager' => $pager]) ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Reject -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-ban me-2"></i> Tolak Perhitungan Gaji
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="catatan" class="form-control" rows="4" required placeholder="Masukkan alasan penolakan..."></textarea>
                        <small class="text-muted">Alasan ini akan dicatat untuk referensi</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let rejectId = null;

function hitungGaji(id) {
    if (confirm('Hitung gaji ini? Setelah dihitung, data akan diproses untuk pembayaran.')) {
        $.ajax({
            url: '<?= site_url('accounting/penggajian/perhitungan-gaji/hitung') ?>/' + id,
            method: 'POST',
            data: { _method: 'POST' },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                toastr.error('Terjadi kesalahan saat menghitung gaji');
                console.error(xhr.responseText);
            }
        });
    }
}

function approveGaji(id) {
    if (confirm('Setujui perhitungan gaji ini? Gaji akan masuk ke proses pembayaran.')) {
        $.ajax({
            url: '<?= site_url('accounting/penggajian/perhitungan-gaji/approve') ?>/' + id,
            method: 'POST',
            data: { _method: 'POST' },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                toastr.error('Terjadi kesalahan saat menyetujui gaji');
                console.error(xhr.responseText);
            }
        });
    }
}

function rejectGaji(id) {
    rejectId = id;
    $('#rejectModal').modal('show');
}

$('#rejectForm').on('submit', function(e) {
    e.preventDefault();
    let catatan = $(this).find('textarea[name="catatan"]').val();
    
    if (!catatan.trim()) {
        toastr.warning('Alasan penolakan harus diisi');
        return;
    }
    
    $.ajax({
        url: '<?= site_url('accounting/penggajian/perhitungan-gaji/reject') ?>/' + rejectId,
        method: 'POST',
        data: { catatan: catatan },
        success: function(response) {
            if (response.success) {
                $('#rejectModal').modal('hide');
                toastr.success(response.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                toastr.error(response.message);
            }
        },
        error: function(xhr) {
            toastr.error('Terjadi kesalahan saat menolak gaji');
            console.error(xhr.responseText);
        }
    });
});

function hapusPerhitungan(id) {
    if (confirm('Hapus perhitungan gaji ini? Tindakan ini tidak dapat dibatalkan.')) {
        $.ajax({
            url: '<?= site_url('accounting/penggajian/perhitungan-gaji/delete') ?>/' + id,
            method: 'POST',
            data: { _method: 'DELETE' },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                toastr.error('Terjadi kesalahan saat menghapus perhitungan');
                console.error(xhr.responseText);
            }
        });
    }
}

// Initialize DataTable
$(document).ready(function() {
    $('#dataTable').DataTable({
        "pageLength": 25,
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "responsive": true,
        "language": {
            "search": "Cari:",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Berikutnya",
                "previous": "Sebelumnya"
            }
        }
    });
});
</script>

<?php $this->endSection(); ?>