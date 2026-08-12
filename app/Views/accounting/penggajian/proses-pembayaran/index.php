<div class="main-content">
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 text-gradient-accounting">Proses Pembayaran Gaji</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-2">
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/penggajian') ?>">Penggajian</a></li>
                    <li class="breadcrumb-item active">Proses Pembayaran</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= site_url('accounting/penggajian/proses-pembayaran/create') ?>" class="btn btn-accounting">
                <i class="fas fa-plus me-1"></i> Buat Pembayaran Baru
            </a>
        </div>
    </div>

    <!-- Info Card - Gaji Siap Bayar -->
    <?php if (!empty($perhitunganTersedia) && $totalSiapBayar > 0): ?>
    <div class="alert alert-success mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <i class="fas fa-money-bill-wave fa-2x me-3 float-start"></i>
                <div>
                    <h5 class="mb-1">Gaji Siap Dibayar</h5>
                    <p class="mb-0">
                        Terdapat <strong><?= count($perhitunganTersedia) ?></strong> karyawan dengan total gaji 
                        <strong>Rp <?= number_format($totalSiapBayar, 0, ',', '.') ?></strong> 
                        untuk periode <?= $bulanOptions[$bulan] ?> <?= $tahun ?>
                    </p>
                </div>
            </div>
            <a href="<?= site_url('accounting/penggajian/proses-pembayaran/create?bulan=' . $bulan . '&tahun=' . $tahun) ?>" class="btn btn-success">
                <i class="fas fa-credit-card me-1"></i> Proses Pembayaran
            </a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Filter -->
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
            <div class="card border-left-primary shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Total Proses</div>
                            <h3 class="mb-0"><?= count($proses) ?></h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-clipboard-list fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Total Karyawan</div>
                            <h3 class="mb-0"><?= array_sum(array_column($proses, 'total_karyawan')) ?></h3>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-users fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Total Nominal</div>
                            <h5 class="mb-0">Rp <?= number_format(array_sum(array_column($proses, 'total_nominal')), 0, ',', '.') ?></h5>
                        </div>
                        <div class="bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-money-bill-wave fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Selesai</div>
                            <h3 class="mb-0"><?= count(array_filter($proses, fn($p) => $p['status'] == 'Selesai')) ?></h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-check-circle fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card">
        <div class="card-header bg-gradient-accounting text-white">
            <i class="fas fa-table me-2"></i> Daftar Proses Pembayaran
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover accounting-table" id="dataTable">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="10%">Nomor Proses</th>
                            <th width="20%">Nama Proses</th>
                            <th width="10%">Periode</th>
                            <th width="10%">Tgl Pembayaran</th>
                            <th width="10%">Metode</th>
                            <th width="8%">Karyawan</th>
                            <th width="12%">Total Nominal</th>
                            <th width="8%">Status</th>
                            <th width="7%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($proses)): ?>
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    Tidak ada data proses pembayaran untuk periode ini
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($proses as $item): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td class="text-center"><code><?= $item['nomor_proses'] ?></code></td>
                                <td>
                                    <strong><?= $item['nama_proses'] ?></strong><br>
                                    <small class="text-muted">Dibuat: <?= date('d/m/Y', strtotime($item['created_at'])) ?></small>
                                </td>
                                <td class="text-center">
                                    <?= $bulanOptions[$item['periode_bulan']] ?> <?= $item['periode_tahun'] ?>
                                </td>
                                <td class="text-center"><?= date('d/m/Y', strtotime($item['tanggal_pembayaran'])) ?></td>
                                <td class="text-center">
                                    <?php
                                    $badgeClass = match($item['metode_pembayaran']) {
                                        'Transfer Bank' => 'primary',
                                        'Tunai' => 'success',
                                        'Cek' => 'warning',
                                        'Giro' => 'info',
                                        default => 'secondary'
                                    };
                                    ?>
                                    <span class="badge bg-<?= $badgeClass ?>"><?= $item['metode_pembayaran'] ?></span>
                                </td>
                                <td class="text-center"><?= $item['total_karyawan'] ?> org</td>
                                <td class="text-end">
                                    <strong class="text-primary">Rp <?= number_format($item['total_nominal'], 0, ',', '.') ?></strong>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $statusBadge = match($item['status']) {
                                        'Selesai' => 'success',
                                        'Diproses' => 'info',
                                        'Draft' => 'secondary',
                                        'Dibatalkan' => 'danger',
                                        default => 'secondary'
                                    };
                                    ?>
                                    <span class="badge bg-<?= $statusBadge ?> px-3 py-2"><?= $item['status'] ?></span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="<?= site_url('accounting/penggajian/proses-pembayaran/detail/' . $item['id']) ?>" 
                                           class="btn btn-sm btn-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($item['status'] == 'Draft'): ?>
                                            <a href="<?= site_url('accounting/penggajian/proses-pembayaran/edit/' . $item['id']) ?>" 
                                               class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-success" 
                                                    onclick="processPayment(<?= $item['id'] ?>)" title="Proses">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        <?php endif; ?>
                                        <?php if ($item['status'] == 'Diproses'): ?>
                                            <button type="button" class="btn btn-sm btn-success" 
                                                    onclick="completePayment(<?= $item['id'] ?>)" title="Selesaikan">
                                                <i class="fas fa-check-double"></i>
                                            </button>
                                        <?php endif; ?>
                                        <?php if ($item['status'] == 'Selesai'): ?>
                                            <a href="<?= site_url('accounting/penggajian/proses-pembayaran/export-excel/' . $item['id']) ?>" 
                                               class="btn btn-sm btn-success" title="Export Excel">
                                                <i class="fas fa-file-excel"></i>
                                            </a>
                                            <?php if ($item['metode_pembayaran'] == 'Transfer Bank'): ?>
                                                <a href="<?= site_url('accounting/penggajian/proses-pembayaran/export-bank-transfer/' . $item['id']) ?>" 
                                                   class="btn btn-sm btn-primary" title="Export Bank Transfer">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if (in_array($item['status'], ['Draft', 'Diproses'])): ?>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="cancelPayment(<?= $item['id'] ?>)" title="Batalkan">
                                                <i class="fas fa-ban"></i>
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

<script>
function processPayment(id) {
    if (confirm('Proses pembayaran ini? Data akan diproses untuk pembayaran.')) {
        $.ajax({
            url: '<?= site_url('accounting/penggajian/proses-pembayaran/process') ?>/' + id,
            method: 'POST',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('Terjadi kesalahan saat memproses pembayaran');
            }
        });
    }
}

function completePayment(id) {
    if (confirm('Selesaikan pembayaran ini? Aksi ini akan membuat jurnal dan mutasi bank.')) {
        $.ajax({
            url: '<?= site_url('accounting/penggajian/proses-pembayaran/complete') ?>/' + id,
            method: 'POST',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('Terjadi kesalahan saat menyelesaikan pembayaran');
            }
        });
    }
}

function cancelPayment(id) {
    if (confirm('Batalkan proses pembayaran ini? Tindakan ini tidak dapat dibatalkan.')) {
        $.ajax({
            url: '<?= site_url('accounting/penggajian/proses-pembayaran/cancel') ?>/' + id,
            method: 'POST',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('Terjadi kesalahan saat membatalkan pembayaran');
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

</div>