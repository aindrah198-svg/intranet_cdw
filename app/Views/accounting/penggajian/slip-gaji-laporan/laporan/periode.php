<?php
// app/Views/accounting/penggajian/slip-gaji-laporan/laporan/periode.php
$data['active'] = 'slip-gaji-laporan';
$this->extend('accounting/templates/header');
?>

<?php $this->section('content'); ?>
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 text-gradient-accounting">Laporan Penggajian Periode</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-2">
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/penggajian') ?>">Penggajian</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/penggajian/slip-gaji-laporan') ?>">Slip Gaji & Laporan</a></li>
                    <li class="breadcrumb-item active">Laporan Periode</li>
                </ol>
            </nav>
        </div>
        <div>
            <?php if ($bulan): ?>
                <a href="<?= site_url('accounting/penggajian/slip-gaji/export-excel?bulan=' . $bulan . '&tahun=' . $tahun) ?>" class="btn btn-success">
                    <i class="fas fa-file-excel me-1"></i> Export Excel
                </a>
                <a href="<?= site_url('accounting/penggajian/slip-gaji/export-pdf?bulan=' . $bulan . '&tahun=' . $tahun) ?>" class="btn btn-danger">
                    <i class="fas fa-file-pdf me-1"></i> Export PDF
                </a>
                <a href="<?= site_url('accounting/penggajian/slip-gaji/batch-print?bulan=' . $bulan . '&tahun=' . $tahun) ?>" class="btn btn-secondary" target="_blank">
                    <i class="fas fa-print me-1"></i> Cetak Semua Slip
                </a>
            <?php endif; ?>
            <a href="<?= site_url('accounting/penggajian/slip-gaji-laporan') ?>" class="btn btn-accounting-outline">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-header bg-gradient-accounting text-white">
            <i class="fas fa-filter me-2"></i> Filter Laporan
        </div>
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Bulan</label>
                    <select name="bulan" class="form-select" required>
                        <option value="">Pilih Bulan</option>
                        <?php foreach ($bulanOptions as $key => $val): ?>
                            <option value="<?= $key ?>" <?= $bulan == $key ? 'selected' : '' ?>>
                                <?= $val ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tahun</label>
                    <select name="tahun" class="form-select" required>
                        <?php foreach ($tahunOptions as $t): ?>
                            <option value="<?= $t ?>" <?= $tahun == $t ? 'selected' : '' ?>>
                                <?= $t ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-accounting w-100">
                        <i class="fas fa-chart-bar me-1"></i> Tampilkan
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

    <?php if ($bulan): ?>
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
                                    Rata-rata Gaji
                                </div>
                                <div class="h5 mb-0 font-weight-bold">Rp <?= number_format(($ringkasan['total_gaji_bersih'] ?? 0) / max(($ringkasan['jumlah_karyawan'] ?? 1), 1), 0, ',', '.') ?></div>
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

        <!-- Summary Table -->
        <div class="card mb-4">
            <div class="card-header bg-gradient-accounting text-white">
                <i class="fas fa-chart-pie me-2"></i> Ringkasan Penggajian
                <span class="badge bg-light text-dark ms-2">Periode: <?= $bulanOptions[$bulan] ?> <?= $tahun ?></span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr class="table-secondary">
                                <th colspan="2" class="text-center">PENDAPATAN</th>
                            </tr>
                             <tr>
                                <td>Total Gaji Pokok</td>
                                <td class="text-end">Rp <?= number_format($ringkasan['total_gaji_pokok'] ?? 0, 0, ',', '.') ?></td>
                            </tr>
                             <tr>
                                <td>Total Tunjangan Jabatan</td>
                                <td class="text-end">Rp <?= number_format($ringkasan['total_tunjangan_jabatan'] ?? 0, 0, ',', '.') ?></td>
                            </tr>
                             <tr>
                                <td>Total Tunjangan Makan</td>
                                <td class="text-end">Rp <?= number_format($ringkasan['total_tunjangan_makan'] ?? 0, 0, ',', '.') ?></td>
                            </tr>
                             <tr>
                                <td>Total Tunjangan Transport</td>
                                <td class="text-end">Rp <?= number_format($ringkasan['total_tunjangan_transport'] ?? 0, 0, ',', '.') ?></td>
                            </tr>
                             <tr>
                                <td>Total Upah Lembur</td>
                                <td class="text-end">Rp <?= number_format($ringkasan['total_upah_lembur'] ?? 0, 0, ',', '.') ?></td>
                            </tr>
                            <tr class="table-active">
                                <td><strong>TOTAL PENDAPATAN</strong></td>
                                <td class="text-end"><strong>Rp <?= number_format($ringkasan['total_pendapatan'] ?? 0, 0, ',', '.') ?></strong></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr class="table-secondary">
                                <th colspan="2" class="text-center">POTONGAN</th>
                            </tr>
                             <tr>
                                <td>Total Potongan BPJS Kesehatan</td>
                                <td class="text-end">Rp <?= number_format($ringkasan['total_bpjs_kes'] ?? 0, 0, ',', '.') ?></td>
                            </tr>
                             <tr>
                                <td>Total Potongan BPJS TK</td>
                                <td class="text-end">Rp <?= number_format($ringkasan['total_bpjs_tk'] ?? 0, 0, ',', '.') ?></td>
                            </tr>
                             <tr>
                                <td>Total Potongan PPh 21</td>
                                <td class="text-end">Rp <?= number_format($ringkasan['total_pph21'] ?? 0, 0, ',', '.') ?></td>
                            </tr>
                             <tr>
                                <td>Total Potongan Lainnya</td>
                                <td class="text-end">Rp <?= number_format(($ringkasan['total_potongan'] ?? 0) - (($ringkasan['total_bpjs_kes'] ?? 0) + ($ringkasan['total_bpjs_tk'] ?? 0) + ($ringkasan['total_pph21'] ?? 0)), 0, ',', '.') ?></td>
                            </tr>
                            <tr class="table-active">
                                <td><strong>TOTAL POTONGAN</strong></td>
                                <td class="text-end"><strong>Rp <?= number_format($ringkasan['total_potongan'] ?? 0, 0, ',', '.') ?></strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-success text-center">
                            <h5 class="mb-0">GAJI BERSIH YANG DIBAYARKAN</h5>
                            <h2 class="mb-0">Rp <?= number_format($ringkasan['total_gaji_bersih'] ?? 0, 0, ',', '.') ?></h2>
                            <small>Terbilang: <?= $this->terbilang($ringkasan['total_gaji_bersih'] ?? 0) ?> Rupiah</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rekap Per Departemen -->
        <?php if (!empty($rekapDepartemen)): ?>
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-building me-2"></i> Rekap Gaji per Departemen
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-secondary">
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
                                <td><strong><?= $dept['departemen'] ?? 'Tidak Ada Departemen' ?></strong></td>
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
                        <tfoot class="table-active">
                             <tr>
                                <td class="text-end"><strong>TOTAL</strong></td>
                                <td class="text-end"><strong><?= number_format(array_sum(array_column($rekapDepartemen, 'jumlah_karyawan'))) ?> orang</strong></td>
                                <td class="text-end"><strong>Rp <?= number_format(array_sum(array_column($rekapDepartemen, 'total_gaji_pokok')), 0, ',', '.') ?></strong></td>
                                <td class="text-end"><strong>Rp <?= number_format(array_sum(array_column($rekapDepartemen, 'total_tunjangan_jabatan')) + array_sum(array_column($rekapDepartemen, 'total_tunjangan_makan')) + array_sum(array_column($rekapDepartemen, 'total_tunjangan_transport')), 0, ',', '.') ?></strong></td>
                                <td class="text-end"><strong>Rp <?= number_format(array_sum(array_column($rekapDepartemen, 'total_upah_lembur')), 0, ',', '.') ?></strong></td>
                                <td class="text-end"><strong>Rp <?= number_format(array_sum(array_column($rekapDepartemen, 'total_pendapatan')), 0, ',', '.') ?></strong></td>
                                <td class="text-end"><strong>Rp <?= number_format(array_sum(array_column($rekapDepartemen, 'total_potongan')), 0, ',', '.') ?></strong></td>
                                <td class="text-end"><strong>Rp <?= number_format(array_sum(array_column($rekapDepartemen, 'total_gaji_bersih')), 0, ',', '.') ?></strong></td>
                             </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Detail Perhitungan Gaji -->
        <div class="card">
            <div class="card-header bg-gradient-accounting text-white">
                <i class="fas fa-table me-2"></i> Detail Perhitungan Gaji
                <span class="badge bg-light text-dark ms-2"><?= count($perhitungan) ?> karyawan</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover accounting-table" id="detailTable">
                        <thead>
                             <tr>
                                <th width="5%">No</th>
                                <th width="8%">NIK</th>
                                <th width="15%">Nama Karyawan</th>
                                <th width="12%">Jabatan</th>
                                <th width="8%">Gaji Pokok</th>
                                <th width="8%">Tunjangan</th>
                                <th width="8%">Lembur</th>
                                <th width="8%">Pendapatan</th>
                                <th width="8%">Potongan</th>
                                <th width="8%">Gaji Bersih</th>
                                <th width="5%">Status</th>
                                <th width="7%">Aksi</th>
                             </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($perhitungan)): ?>
                                 <tr>
                                    <td colspan="12" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        Tidak ada data perhitungan gaji untuk periode <?= $bulanOptions[$bulan] ?> <?= $tahun ?>
                                    </td>
                                 </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($perhitungan as $item): ?>
                                 <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= $item['nik'] ?? '-' ?></td>
                                    <td>
                                        <strong><?= $item['nama_lengkap'] ?></strong><br>
                                        <small class="text-muted"><?= $item['departemen'] ?? '-' ?></small>
                                    </td>
                                    <td><?= $item['jabatan'] ?? '-' ?></td>
                                    <td class="text-end">Rp <?= number_format($item['gaji_pokok'], 0, ',', '.') ?></td>
                                    <td class="text-end">Rp <?= number_format(($item['tunjangan_jabatan'] ?? 0) + ($item['tunjangan_makan'] ?? 0) + ($item['tunjangan_transport'] ?? 0) + ($item['tunjangan_lainnya'] ?? 0), 0, ',', '.') ?></td>
                                    <td class="text-end">Rp <?= number_format($item['upah_lembur'] ?? 0, 0, ',', '.') ?></td>
                                    <td class="text-end">Rp <?= number_format($item['total_pendapatan'], 0, ',', '.') ?></td>
                                    <td class="text-end text-danger">Rp <?= number_format($item['total_potongan'], 0, ',', '.') ?></td>
                                    <td class="text-end text-primary">
                                        <strong>Rp <?= number_format($item['gaji_bersih'], 0, ',', '.') ?></strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success">Disetujui</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= site_url('accounting/penggajian/slip-gaji/view/' . $item['id']) ?>" class="btn btn-sm btn-info" target="_blank" title="Lihat Slip">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= site_url('accounting/penggajian/slip-gaji/print/' . $item['id']) ?>" class="btn btn-sm btn-secondary" target="_blank" title="Print">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </td>
                                 </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="table-active">
                             <tr>
                                <td colspan="4" class="text-end"><strong>TOTAL</strong></td>
                                <td class="text-end"><strong>Rp <?= number_format(array_sum(array_column($perhitungan, 'gaji_pokok')), 0, ',', '.') ?></strong></td>
                                <td class="text-end"><strong>Rp <?= number_format(array_sum(array_column($perhitungan, 'tunjangan_jabatan')) + array_sum(array_column($perhitungan, 'tunjangan_makan')) + array_sum(array_column($perhitungan, 'tunjangan_transport')) + array_sum(array_column($perhitungan, 'tunjangan_lainnya')), 0, ',', '.') ?></strong></td>
                                <td class="text-end"><strong>Rp <?= number_format(array_sum(array_column($perhitungan, 'upah_lembur')), 0, ',', '.') ?></strong></td>
                                <td class="text-end"><strong>Rp <?= number_format(array_sum(array_column($perhitungan, 'total_pendapatan')), 0, ',', '.') ?></strong></td>
                                <td class="text-end"><strong>Rp <?= number_format(array_sum(array_column($perhitungan, 'total_potongan')), 0, ',', '.') ?></strong></td>
                                <td class="text-end"><strong>Rp <?= number_format(array_sum(array_column($perhitungan, 'gaji_bersih')), 0, ',', '.') ?></strong></td>
                                <td colspan="2"></td>
                             </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center py-5">
            <i class="fas fa-calendar-alt fa-3x mb-3 d-block"></i>
            <h5>Pilih Periode Laporan</h5>
            <p class="mb-0">Silakan pilih bulan dan tahun untuk menampilkan laporan penggajian.</p>
        </div>
    <?php endif; ?>
</div>

<script>
function resetFilter() {
    window.location.href = '<?= site_url('accounting/penggajian/slip-gaji/laporan-periode') ?>';
}

// Initialize DataTable
$(document).ready(function() {
    $('#detailTable').DataTable({
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
});
</script>

<?php $this->endSection(); ?>

<?php
// Helper function untuk terbilang
function terbilang($angka) {
    $angka = (float)$angka;
    $bilangan = array(
        '', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'
    );
    
    if ($angka < 12) {
        return $bilangan[$angka];
    } elseif ($angka < 20) {
        return $bilangan[$angka - 10] . ' Belas';
    } elseif ($angka < 100) {
        $puluh = floor($angka / 10);
        $satuan = $angka % 10;
        return $bilangan[$puluh] . ' Puluh ' . $bilangan[$satuan];
    } elseif ($angka < 200) {
        return 'Seratus ' . terbilang($angka - 100);
    } elseif ($angka < 1000) {
        $ratus = floor($angka / 100);
        $sisa = $angka % 100;
        return $bilangan[$ratus] . ' Ratus ' . terbilang($sisa);
    } elseif ($angka < 2000) {
        return 'Seribu ' . terbilang($angka - 1000);
    } elseif ($angka < 1000000) {
        $ribu = floor($angka / 1000);
        $sisa = $angka % 1000;
        return terbilang($ribu) . ' Ribu ' . terbilang($sisa);
    } elseif ($angka < 1000000000) {
        $juta = floor($angka / 1000000);
        $sisa = $angka % 1000000;
        return terbilang($juta) . ' Juta ' . terbilang($sisa);
    } elseif ($angka < 1000000000000) {
        $miliar = floor($angka / 1000000000);
        $sisa = $angka % 1000000000;
        return terbilang($miliar) . ' Miliar ' . terbilang($sisa);
    }
    return '';
}
?>