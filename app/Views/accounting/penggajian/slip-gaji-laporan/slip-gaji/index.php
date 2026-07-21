<?php
// app/Views/accounting/penggajian/slip-gaji-laporan/index.php
$data['active'] = 'slip-gaji-laporan';
$this->extend('accounting/templates/header');
?>

<?php $this->section('content'); ?>
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 text-gradient-accounting">Slip Gaji & Laporan</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-2">
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/penggajian') ?>">Penggajian</a></li>
                    <li class="breadcrumb-item active">Slip Gaji & Laporan</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <button type="button" class="btn btn-accounting-outline dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-download me-1"></i> Export
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="<?= site_url('accounting/penggajian/slip-gaji/export-excel?bulan=' . $bulan . '&tahun=' . $tahun . '&karyawan_id=' . $karyawan_id) ?>">
                        <i class="fas fa-file-excel me-2 text-success"></i> Export Excel
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="<?= site_url('accounting/penggajian/slip-gaji/export-pdf?bulan=' . $bulan . '&tahun=' . $tahun . '&karyawan_id=' . $karyawan_id) ?>">
                        <i class="fas fa-file-pdf me-2 text-danger"></i> Export PDF
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3" id="filterForm">
                <div class="col-md-2">
                    <label class="form-label">Bulan</label>
                    <select name="bulan" class="form-select" id="filterBulan">
                        <?php foreach ($bulanOptions as $key => $val): ?>
                            <option value="<?= $key ?>" <?= $bulan == $key ? 'selected' : '' ?>>
                                <?= $val ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tahun</label>
                    <select name="tahun" class="form-select" id="filterTahun">
                        <?php foreach ($tahunOptions as $t): ?>
                            <option value="<?= $t ?>" <?= $tahun == $t ? 'selected' : '' ?>>
                                <?= $t ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Karyawan</label>
                    <select name="karyawan_id" class="form-select" id="filterKaryawan">
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
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-accounting-outline w-100" onclick="resetFilter()">
                        <i class="fas fa-undo-alt me-1"></i> Reset
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
                            <small class="text-muted">karyawan</small>
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
                                Total Gaji Pokok
                            </div>
                            <div class="h5 mb-0 font-weight-bold">Rp <?= number_format($ringkasan['total_gaji_pokok'] ?? 0, 0, ',', '.') ?></div>
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
                                Total Potongan
                            </div>
                            <div class="h5 mb-0 font-weight-bold">Rp <?= number_format($ringkasan['total_potongan'] ?? 0, 0, ',', '.') ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-minus-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Aksi Cepat</h6>
                            <small class="text-muted">Cetak slip gaji untuk periode yang dipilih</small>
                        </div>
                        <div>
                            <button type="button" class="btn btn-accounting" onclick="batchPrint()">
                                <i class="fas fa-print me-1"></i> Cetak Slip Massal
                            </button>
                            <button type="button" class="btn btn-accounting-outline" onclick="window.location.href='<?= site_url('accounting/penggajian/slip-gaji/laporan-periode?bulan=' . $bulan . '&tahun=' . $tahun) ?>'">
                                <i class="fas fa-chart-bar me-1"></i> Laporan Periode
                            </button>
                            <button type="button" class="btn btn-accounting-outline" onclick="window.location.href='<?= site_url('accounting/penggajian/slip-gaji/rekap-gaji?tahun=' . $tahun) ?>'">
                                <i class="fas fa-table me-1"></i> Rekap Gaji
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rekap Per Departemen -->
    <?php if (!empty($rekapDepartemen)): ?>
    <div class="card mb-4">
        <div class="card-header bg-gradient-accounting text-white">
            <i class="fas fa-building me-2"></i> Rekap Gaji per Departemen
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Departemen</th>
                            <th class="text-end">Jumlah Karyawan</th>
                            <th class="text-end">Total Gaji Pokok</th>
                            <th class="text-end">Total Tunjangan</th>
                            <th class="text-end">Total Upah Lembur</th>
                            <th class="text-end">Total Pendapatan</th>
                            <th class="text-end">Total Potongan</th>
                            <th class="text-end">Total Gaji Bersih</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rekapDepartemen as $dept): ?>
                         <tr>
                            <td><?= $dept['departemen'] ?? 'Tidak Ada Departemen' ?></td>
                            <td class="text-end"><?= number_format($dept['jumlah_karyawan']) ?> orang</td>
                            <td class="text-end">Rp <?= number_format($dept['total_gaji_pokok'] ?? 0, 0, ',', '.') ?></td>
                            <td class="text-end">Rp <?= number_format(($dept['total_tunjangan_jabatan'] ?? 0) + ($dept['total_tunjangan_makan'] ?? 0) + ($dept['total_tunjangan_transport'] ?? 0), 0, ',', '.') ?></td>
                            <td class="text-end">Rp <?= number_format($dept['total_upah_lembur'] ?? 0, 0, ',', '.') ?></td>
                            <td class="text-end">Rp <?= number_format($dept['total_pendapatan'] ?? 0, 0, ',', '.') ?></td>
                            <td class="text-end">Rp <?= number_format($dept['total_potongan'] ?? 0, 0, ',', '.') ?></td>
                            <td class="text-end"><strong>Rp <?= number_format($dept['total_gaji_bersih'] ?? 0, 0, ',', '.') ?></strong></td>
                         </tr>
                        <?php endforeach; ?>
                    </tbody>
                 </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Daftar Slip Gaji -->
    <div class="card">
        <div class="card-header bg-gradient-accounting text-white d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-file-invoice me-2"></i> Daftar Slip Gaji
                <span class="badge bg-light text-dark ms-2"><?= count($slipGaji) ?> slip</span>
            </div>
            <div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()">
                    <label class="form-check-label text-white" for="selectAllCheckbox">Pilih Semua</label>
                </div>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($slipGaji)): ?>
                <div class="alert alert-warning text-center py-5">
                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                    <h5>Tidak Ada Slip Gaji</h5>
                    <p class="mb-0">Tidak ditemukan slip gaji dengan status "Disetujui" untuk periode 
                        <strong><?= $bulanOptions[$bulan] ?> <?= $tahun ?></strong>.</p>
                    <hr>
                    <a href="<?= site_url('accounting/penggajian/perhitungan-gaji?bulan=' . $bulan . '&tahun=' . $tahun . '&status=Disetujui') ?>" 
                       class="btn btn-accounting mt-2">
                        <i class="fas fa-calculator me-1"></i> Lihat Perhitungan Gaji
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover accounting-table" id="slipTable">
                        <thead>
                             <tr>
                                <th width="5%">
                                    <input type="checkbox" id="checkAll" class="form-check-input" onchange="toggleSelectAll()">
                                </th>
                                <th width="5%">No</th>
                                <th width="10%">Nomor</th>
                                <th width="10%">NIK</th>
                                <th width="20%">Nama Karyawan</th>
                                <th width="15%">Jabatan</th>
                                <th width="10%">Periode</th>
                                <th width="10%">Gaji Bersih</th>
                                <th width="15%">Aksi</th>
                             </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($slipGaji as $item): ?>
                             <tr>
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input slip-checkbox" value="<?= $item['id'] ?>">
                                </td>
                                <td class="text-center"><?= $no++ ?></td>
                                <td class="text-center"><code><?= $item['nomor_perhitungan'] ?></code></td>
                                <td><?= $item['nik'] ?></td>
                                <td>
                                    <strong><?= $item['nama_lengkap'] ?></strong><br>
                                    <small class="text-muted"><?= $item['departemen'] ?></small>
                                </td>
                                <td><?= $item['jabatan'] ?></td>
                                <td class="text-center">
                                    <?= $bulanOptions[$item['periode_bulan']] ?> <?= $item['periode_tahun'] ?>
                                </td>
                                <td class="text-end text-primary">
                                    <strong>Rp <?= number_format($item['gaji_bersih'], 0, ',', '.') ?></strong>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="<?= site_url('accounting/penggajian/slip-gaji/view/' . $item['id']) ?>" 
                                           class="btn btn-sm btn-info" title="Lihat Slip" target="_blank">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= site_url('accounting/penggajian/slip-gaji/print/' . $item['id']) ?>" 
                                           class="btn btn-sm btn-secondary" title="Print" target="_blank">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <a href="<?= site_url('accounting/penggajian/slip-gaji/pdf/' . $item['id']) ?>" 
                                           class="btn btn-sm btn-danger" title="PDF" target="_blank">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    </div>
                                </td>
                             </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-active">
                             <tr>
                                <td colspan="7" class="text-end"><strong>Total Gaji Bersih</strong></td>
                                <td class="text-end"><strong>Rp <?= number_format(array_sum(array_column($slipGaji, 'gaji_bersih')), 0, ',', '.') ?></strong></td>
                                <td></td>
                             </tr>
                        </tfoot>
                     </table>
                </div>
                
                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <div>
                        <button type="button" class="btn btn-accounting" onclick="batchPrintSelected()" id="btnBatchPrint" disabled>
                            <i class="fas fa-print me-1"></i> Cetak Slip Terpilih
                        </button>
                        <button type="button" class="btn btn-accounting-outline" onclick="window.location.href='<?= site_url('accounting/penggajian/slip-gaji/batch-print?bulan=' . $bulan . '&tahun=' . $tahun . '&karyawan_ids=' . implode(',', array_column($slipGaji, 'id'))) ?>'">
                            <i class="fas fa-print me-1"></i> Cetak Semua Slip
                        </button>
                    </div>
                    <div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Menampilkan <?= count($slipGaji) ?> slip gaji
                        </small>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
let selectedSlips = [];

function toggleSelectAll() {
    let checkAll = document.getElementById('checkAll');
    let checkboxes = document.querySelectorAll('.slip-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = checkAll.checked;
    });
    
    updateSelectedSlips();
}

function updateSelectedSlips() {
    selectedSlips = [];
    let checkboxes = document.querySelectorAll('.slip-checkbox:checked');
    
    checkboxes.forEach(checkbox => {
        selectedSlips.push(checkbox.value);
    });
    
    let btnBatchPrint = document.getElementById('btnBatchPrint');
    if (selectedSlips.length > 0) {
        btnBatchPrint.disabled = false;
        btnBatchPrint.innerHTML = `<i class="fas fa-print me-1"></i> Cetak Slip Terpilih (${selectedSlips.length})`;
    } else {
        btnBatchPrint.disabled = true;
        btnBatchPrint.innerHTML = `<i class="fas fa-print me-1"></i> Cetak Slip Terpilih`;
    }
}

function batchPrint() {
    if (selectedSlips.length === 0) {
        toastr.warning('Pilih minimal satu slip gaji untuk dicetak');
        return;
    }
    
    let ids = selectedSlips.join(',');
    let url = '<?= site_url('accounting/penggajian/slip-gaji/batch-print') ?>?ids=' + ids;
    window.open(url, '_blank');
}

function batchPrintSelected() {
    batchPrint();
}

function resetFilter() {
    document.getElementById('filterBulan').value = '<?= date('m') ?>';
    document.getElementById('filterTahun').value = '<?= date('Y') ?>';
    document.getElementById('filterKaryawan').value = '';
    document.getElementById('filterForm').submit();
}

// Initialize DataTable
$(document).ready(function() {
    $('#slipTable').DataTable({
        "pageLength": 25,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "responsive": true,
        "language": {
            "search": "Cari:",
            "lengthMenu": "Tampilkan _MENU_ data",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Berikutnya",
                "previous": "Sebelumnya"
            }
        },
        "order": [[1, 'asc']]
    });
    
    // Add event listener to checkboxes after DataTable initialization
    $('#slipTable').on('change', '.slip-checkbox', function() {
        let checkAll = document.getElementById('checkAll');
        let allChecked = document.querySelectorAll('.slip-checkbox:checked').length === document.querySelectorAll('.slip-checkbox').length;
        checkAll.checked = allChecked;
        updateSelectedSlips();
    });
});
</script>

<?php $this->endSection(); ?>