<?php
/**
 * LAPORAN NERACA - BALANCE SHEET
 * Format: Staffel (Aset - Kewajiban - Ekuitas)
 * 
 * Data dari Controller: Neraca.php
 * Data dari Model: NeracaModel.php
 */

// Helper function untuk format rupiah
function formatRupiahView($nilai) {
    $nilai = (float) $nilai;
    if ($nilai < 0) {
        return '(Rp ' . number_format(abs($nilai), 0, ',', '.') . ')';
    }
    return 'Rp ' . number_format($nilai, 0, ',', '.');
}
?>

<?= $this->include('accounting/templates/header') ?>
<?= $this->include('accounting/templates/sidebar') ?>
<?= $this->include('accounting/templates/navbar') ?>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Laporan Neraca</h2>
                    <p class="text-muted mb-0">
                        <i class="fas fa-calendar-alt me-1"></i> 
                        Per <?= $filters['periode_label'] ?? date('d F Y') ?>
                    </p>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-primary" onclick="refreshNeraca()">
                        <i class="fas fa-sync-alt me-1"></i> Refresh
                    </button>
                    <button type="button" class="btn btn-outline-success" onclick="validateNeraca()">
                        <i class="fas fa-check-circle me-1"></i> Validasi
                    </button>
                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-download me-1"></i> Export
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" onclick="exportNeraca('csv')"><i class="fas fa-file-csv me-2"></i> CSV</a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportNeraca('excel')"><i class="fas fa-file-excel me-2"></i> Excel</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" onclick="printNeraca()"><i class="fas fa-print me-2"></i> Print</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Periode -->
    <div class="card mb-4">
        <div class="card-body">
            <form id="filterForm" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Tanggal Periode</label>
                    <input type="date" class="form-control" name="tanggal_periode" 
                           value="<?= $filters['tanggal_periode'] ?? date('Y-m-d') ?>">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i> Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Status Neraca -->
    <div class="alert alert-<?= ($verifikasi['is_seimbang'] ?? false) ? 'success' : 'danger' ?> alert-dismissible fade show">
        <div class="d-flex align-items-center">
            <i class="fas fa-<?= ($verifikasi['is_seimbang'] ?? false) ? 'check-circle' : 'exclamation-triangle' ?> fa-2x me-3"></i>
            <div>
                <strong>STATUS: <?= ($verifikasi['is_seimbang'] ?? false) ? 'NERACA SEIMBANG ✓' : 'NERACA TIDAK SEIMBANG ✗' ?></strong>
                <p class="mb-0"><?= $verifikasi['formula'] ?? '' ?></p>
                <?php if (!($verifikasi['is_seimbang'] ?? true)): ?>
                <small class="text-danger">Selisih: <?= formatRupiahView($verifikasi['selisih'] ?? 0) ?></small>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Info Laba Berjalan -->
    <?php if (($laba_bersih ?? 0) != 0): ?>
    <div class="alert alert-info alert-dismissible fade show">
        <div class="d-flex align-items-center">
            <i class="fas fa-chart-line fa-2x me-3"></i>
            <div>
                <strong>Laba Tahun Berjalan: <?= $laba_bersih_formatted ?? 'Rp 0' ?></strong>
                <p class="mb-0">
                    <?= ($laba_bersih > 0) ? 'Menambah Ekuitas' : 'Mengurangi Ekuitas' ?>
                </p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Aset</h6>
                    <h3><?= $total_aset_formatted ?? 'Rp 0' ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h6 class="card-title">Total Kewajiban</h6>
                    <h3><?= $total_kewajiban_formatted ?? 'Rp 0' ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-<?= ($laba_bersih ?? 0) >= 0 ? 'success' : 'danger' ?> text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Ekuitas</h6>
                    <h3><?= $total_ekuitas_formatted ?? 'Rp 0' ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Rasio Lancar</h6>
                    <h3><?= $rasio['current_ratio'] ?? '0' ?> : 1</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN NERACA - 2 KOLOM -->
    <div class="row">
        <!-- KOLOM KIRI: ASET -->
        <div class="col-md-6">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-building me-2"></i> ASET</h4>
                </div>
                <div class="card-body">
                    
                    <!-- ASET LANCAR -->
                    <h5 class="border-bottom pb-2 text-primary">A. ASET LANCAR</h5>
                    <table class="table table-sm">
                        <tbody>
                            <?php foreach ($aset_lancar as $akun): ?>
                            <tr>
                                <td width="60%">
                                    <small class="text-muted"><?= $akun['kode_akun'] ?? '' ?></small><br>
                                    <?= $akun['nama_akun'] ?? '' ?>
                                </td>
                                <td width="40%" class="text-end">
                                    <?= formatRupiahView($akun['saldo'] ?? 0) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="fw-bold bg-light">
                                <td class="text-end">Total Aset Lancar</td>
                                <td class="text-end text-primary">
                                    <?= formatRupiahView($subtotal_aset_lancar ?? 0) ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- ASET TETAP -->
                    <h5 class="border-bottom pb-2 mt-3 text-primary">B. ASET TETAP</h5>
                    <table class="table table-sm">
                        <tbody>
                            <?php foreach ($aset_tetap as $akun): ?>
                            <tr>
                                <td width="60%">
                                    <small class="text-muted"><?= $akun['kode_akun'] ?? '' ?></small><br>
                                    <?= $akun['nama_akun'] ?? '' ?>
                                </td>
                                <td width="40%" class="text-end">
                                    <?= formatRupiahView($akun['saldo'] ?? 0) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="fw-bold bg-light">
                                <td class="text-end">Total Aset Tetap</td>
                                <td class="text-end text-primary">
                                    <?= formatRupiahView($subtotal_aset_tetap ?? 0) ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- ASET LAINNYA (jika ada) -->
                    <?php if (!empty($aset_lainnya)): ?>
                    <h5 class="border-bottom pb-2 mt-3 text-primary">C. ASET LAINNYA</h5>
                    <table class="table table-sm">
                        <tbody>
                            <?php foreach ($aset_lainnya as $akun): ?>
                            <tr>
                                <td width="60%">
                                    <small class="text-muted"><?= $akun['kode_akun'] ?? '' ?></small><br>
                                    <?= $akun['nama_akun'] ?? '' ?>
                                </td>
                                <td width="40%" class="text-end">
                                    <?= formatRupiahView($akun['saldo'] ?? 0) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="fw-bold bg-light">
                                <td class="text-end">Total Aset Lainnya</td>
                                <td class="text-end text-primary">
                                    <?= formatRupiahView($subtotal_aset_lainnya ?? 0) ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <?php endif; ?>

                    <!-- TOTAL ASET -->
                    <div class="alert alert-primary mt-3">
                        <div class="d-flex justify-content-between">
                            <strong>TOTAL ASET</strong>
                            <strong><?= $total_aset_formatted ?? 'Rp 0' ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: KEWAJIBAN & EKUITAS -->
        <div class="col-md-6">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-balance-scale me-2"></i> KEWAJIBAN & EKUITAS</h4>
                </div>
                <div class="card-body">

                    <!-- KEWAJIBAN LANCAR -->
                    <h5 class="border-bottom pb-2 text-danger">A. KEWAJIBAN LANCAR</h5>
                    <table class="table table-sm">
                        <tbody>
                            <?php foreach ($kewajiban_lancar as $akun): ?>
                            <tr>
                                <td width="60%">
                                    <small class="text-muted"><?= $akun['kode_akun'] ?? '' ?></small><br>
                                    <?= $akun['nama_akun'] ?? '' ?>
                                </td>
                                <td width="40%" class="text-end">
                                    <?= formatRupiahView($akun['saldo'] ?? 0) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="fw-bold bg-light">
                                <td class="text-end">Total Kewajiban Lancar</td>
                                <td class="text-end text-danger">
                                    <?= formatRupiahView($subtotal_kewajiban_lancar ?? 0) ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- KEWAJIBAN JANGKA PANJANG (jika ada) -->
                    <?php if (!empty($kewajiban_jangka_panjang)): ?>
                    <h5 class="border-bottom pb-2 mt-3" style="color: #fd7e14;">B. KEWAJIBAN JANGKA PANJANG</h5>
                    <table class="table table-sm">
                        <tbody>
                            <?php foreach ($kewajiban_jangka_panjang as $akun): ?>
                            <tr>
                                <td width="60%">
                                    <small class="text-muted"><?= $akun['kode_akun'] ?? '' ?></small><br>
                                    <?= $akun['nama_akun'] ?? '' ?>
                                </td>
                                <td width="40%" class="text-end">
                                    <?= formatRupiahView($akun['saldo'] ?? 0) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="fw-bold bg-light">
                                <td class="text-end">Total Kewajiban Jangka Panjang</td>
                                <td class="text-end" style="color: #fd7e14;">
                                    <?= formatRupiahView($subtotal_kewajiban_jangka_panjang ?? 0) ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <?php endif; ?>

                    <!-- TOTAL KEWAJIBAN -->
                    <div class="alert alert-warning mt-2">
                        <div class="d-flex justify-content-between">
                            <strong>TOTAL KEWAJIBAN</strong>
                            <strong><?= $total_kewajiban_formatted ?? 'Rp 0' ?></strong>
                        </div>
                    </div>

                    <!-- EKUITAS -->
                    <h5 class="border-bottom pb-2 mt-3 text-success">C. EKUITAS</h5>
                    <table class="table table-sm">
                        <tbody>
                            <?php 
                            $totalEkuitasFromAkun = 0;
                            foreach ($ekuitas as $akun): 
                                $totalEkuitasFromAkun += $akun['saldo'] ?? 0;
                            ?>
                            <tr>
                                <td width="60%">
                                    <small class="text-muted"><?= $akun['kode_akun'] ?? '' ?></small><br>
                                    <?= $akun['nama_akun'] ?? '' ?>
                                </td>
                                <td width="40%" class="text-end <?= ($akun['saldo'] ?? 0) < 0 ? 'text-danger' : 'text-success' ?>">
                                    <?= formatRupiahView($akun['saldo'] ?? 0) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <!-- Laba Berjalan -->
                            <?php if (($laba_bersih ?? 0) != 0): ?>
                            <tr class="bg-light">
                                <td>
                                    <small class="text-muted">LABA</small><br>
                                    Laba Tahun Berjalan
                                </td>
                                <td class="text-end <?= ($laba_bersih ?? 0) > 0 ? 'text-success' : 'text-danger' ?> fw-bold">
                                    <?= formatRupiahView($laba_bersih ?? 0) ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            
                            <tr class="fw-bold bg-light">
                                <td class="text-end">Total Ekuitas</td>
                                <td class="text-end text-success">
                                    <?= formatRupiahView(($total_ekuitas ?? 0)) ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- TOTAL KEWAJIBAN & EKUITAS -->
                    <div class="alert alert-success mt-3">
                        <div class="d-flex justify-content-between">
                            <strong>TOTAL KEWAJIBAN & EKUITAS</strong>
                            <strong><?= formatRupiahView(($total_kewajiban ?? 0) + ($total_ekuitas ?? 0)) ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- VERIFIKASI AKHIR -->
    <div class="card border-<?= ($verifikasi['is_seimbang'] ?? false) ? 'success' : 'danger' ?> mt-4">
        <div class="card-header bg-<?= ($verifikasi['is_seimbang'] ?? false) ? 'success' : 'danger' ?> text-white">
            <h5 class="mb-0">Verifikasi Persamaan Akuntansi</h5>
        </div>
        <div class="card-body text-center">
            <h3 class="mb-3"><?= $verifikasi['formula'] ?? '' ?></h3>
            <h4 class="<?= ($verifikasi['is_seimbang'] ?? false) ? 'text-success' : 'text-danger' ?>">
                <?= ($verifikasi['is_seimbang'] ?? false) ? '✓ NERACA SEIMBANG' : '✗ NERACA TIDAK SEIMBANG' ?>
            </h4>
            <?php if (!($verifikasi['is_seimbang'] ?? false)): ?>
            <p class="text-danger">Selisih: <?= formatRupiahView($verifikasi['selisih'] ?? 0) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- RASIO KEUANGAN -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Rasio Keuangan</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <h6>Rasio Lancar</h6>
                            <h4><?= $rasio['current_ratio'] ?? '0' ?> : 1</h4>
                            <small class="text-muted">Aset Lancar / Kewajiban Lancar</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <h6>Debt to Equity</h6>
                            <h4><?= $rasio['debt_to_equity'] ?? '0' ?></h4>
                            <small class="text-muted">Total Kewajiban / Ekuitas</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <h6>Debt to Assets</h6>
                            <h4><?= $rasio['debt_to_assets'] ?? '0' ?></h4>
                            <small class="text-muted">Total Kewajiban / Total Aset</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <h6>Modal Kerja</h6>
                            <h4><?= $rasio['working_capital_formatted'] ?? 'Rp 0' ?></h4>
                            <small class="text-muted">Aset Lancar - Kewajiban Lancar</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="text-center text-muted small mt-4">
        Dicetak: <?= $date_generated ?? date('d/m/Y H:i:s') ?>
    </div>
</div>

<script>
// Refresh function
function refreshNeraca() {
    const url = new URL(window.location.href);
    url.searchParams.set('refresh', '1');
    window.location.href = url.toString();
}

// Validate function
function validateNeraca() {
    const periodeDate = document.querySelector('input[name="tanggal_periode"]').value;
    
    fetch('<?= site_url("accounting/laporan-keuangan/neraca/ajax-validate") ?>?tanggal_periode=' + periodeDate, {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.is_seimbang) {
            alert('✓ Neraca SEIMBANG\n' + data.formula);
        } else {
            alert('✗ Neraca TIDAK SEIMBANG\n' + data.formula + '\nSelisih: ' + data.selisih_formatted);
        }
    })
    .catch(error => {
        alert('Error: ' + error);
    });
}

// Export function
function exportNeraca(type) {
    const periodeDate = document.querySelector('input[name="tanggal_periode"]').value;
    window.location.href = '<?= site_url("accounting/laporan-keuangan/neraca/export") ?>?tanggal_periode=' + periodeDate + '&type=' + type;
}

// Print function
function printNeraca() {
    const periodeDate = document.querySelector('input[name="tanggal_periode"]').value;
    window.open('<?= site_url("accounting/laporan-keuangan/neraca/print") ?>?tanggal_periode=' + periodeDate, '_blank');
}
</script>

<style>
.table-sm td {
    padding: 6px 8px;
    vertical-align: middle;
}
.card-header h4, .card-header h5 {
    margin: 0;
}
.bg-warning.text-dark .card-title,
.bg-warning.text-dark h3 {
    color: #212529 !important;
}
</style>

<?= $this->include('accounting/templates/footer') ?>