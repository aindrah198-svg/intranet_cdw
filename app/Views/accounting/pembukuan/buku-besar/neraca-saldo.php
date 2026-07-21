<?= $this->include('accounting/templates/header') ?>
<?= $this->include('accounting/templates/sidebar') ?>
<?= $this->include('accounting/templates/navbar') ?>

<div class="container-fluid py-4">
    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif ?>
    
    <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif ?>

    <!-- Header Section -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Neraca Saldo</h2>
                    <p class="text-muted mb-0">Trial Balance - Ringkasan Saldo Seluruh Akun</p>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-download me-1"></i> Export
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" onclick="exportData('excel')"><i class="fas fa-file-excel text-success me-2"></i> Excel</a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportData('pdf')"><i class="fas fa-file-pdf text-danger me-2"></i> PDF</a></li>
                    </ul>
                    <button type="button" class="btn btn-info" onclick="window.print()">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                    <a href="<?= site_url('accounting/pembukuan/buku-besar') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Periode -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i> Filter Periode</h5>
        </div>
        <div class="card-body">
            <form method="get" action="<?= site_url('accounting/pembukuan/buku-besar/neraca-saldo') ?>" id="filterForm">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Periode</label>
                        <input type="month" class="form-control" name="periode" value="<?= $periode ?? date('Y-m') ?>" onchange="this.form.submit()">
                        <small class="text-muted">Saldo per akhir bulan</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Tertentu</label>
                        <input type="date" class="form-control" name="tanggal" value="<?= $tanggal ?? '' ?>" placeholder="dd/mm/yyyy">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tipe Akun</label>
                        <select class="form-select" name="tipe_akun" id="tipeAkun">
                            <option value="">-- Semua Tipe --</option>
                            <option value="Aset" <?= ($tipe_akun ?? '') == 'Aset' ? 'selected' : '' ?>>Aset</option>
                            <option value="Kewajiban" <?= ($tipe_akun ?? '') == 'Kewajiban' ? 'selected' : '' ?>>Kewajiban</option>
                            <option value="Ekuitas" <?= ($tipe_akun ?? '') == 'Ekuitas' ? 'selected' : '' ?>>Ekuitas</option>
                            <option value="Pendapatan" <?= ($tipe_akun ?? '') == 'Pendapatan' ? 'selected' : '' ?>>Pendapatan</option>
                            <option value="Beban" <?= ($tipe_akun ?? '') == 'Beban' ? 'selected' : '' ?>>Beban</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i> Tampilkan</button>
                            <a href="<?= site_url('accounting/pembukuan/buku-besar/neraca-saldo') ?>" class="btn btn-secondary">Reset</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Ringkasan Card -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Akun</h6>
                    <h3><?= number_format($neraca_saldo['total_akun'] ?? 0) ?></h3>
                    <small>Akun aktif</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Debit</h6>
                    <h3>Rp <?= number_format($neraca_saldo['total_debit'] ?? 0, 0, ',', '.') ?></h3>
                    <small>Semua akun</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Kredit</h6>
                    <h3>Rp <?= number_format($neraca_saldo['total_kredit'] ?? 0, 0, ',', '.') ?></h3>
                    <small>Semua akun</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card <?= ($neraca_saldo['is_balance'] ?? false) ? 'bg-info' : 'bg-warning' ?> text-white">
                <div class="card-body">
                    <h6 class="card-title">Status</h6>
                    <h3><?= ($neraca_saldo['is_balance'] ?? false) ? 'SEIMBANG' : 'TIDAK SEIMBANG' ?></h3>
                    <small>Selisih: Rp <?= number_format(abs(($neraca_saldo['total_debit'] ?? 0) - ($neraca_saldo['total_kredit'] ?? 0)), 0, ',', '.') ?></small>
                </div>
            </div>
        </div>
    </div>

    <!-- Neraca Saldo Table -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-balance-scale me-2"></i> 
                Neraca Saldo
                <small class="text-muted ms-2">
                    Per <?= !empty($tanggal) ? date('d/m/Y', strtotime($tanggal)) : date('F Y', strtotime($periode . '-01')) ?>
                </small>
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="neracaSaldoTable">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="12%">Kode Akun</th>
                            <th width="30%">Nama Akun</th>
                            <th width="12%">Tipe Akun</th>
                            <th width="8%">Normal</th>
                            <th width="15%" class="text-end">Debit</th>
                            <th width="15%" class="text-end">Kredit</th>
                            <th width="8%" class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($neraca_saldo['data'])): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                <h5>Tidak ada data</h5>
                                <p class="text-muted">Belum ada transaksi pada periode ini</p>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php 
                            $no = 1;
                            $totalDebit = 0;
                            $totalKredit = 0;
                            
                            // Group by tipe akun
                            $groupedData = [];
                            foreach ($neraca_saldo['data'] as $item) {
                                $tipe = $item['tipe_akun'];
                                if (!isset($groupedData[$tipe])) {
                                    $groupedData[$tipe] = [];
                                }
                                $groupedData[$tipe][] = $item;
                            }
                            
                            // Urutan tipe akun
                            $tipeOrder = ['Aset', 'Kewajiban', 'Ekuitas', 'Pendapatan', 'Beban'];
                            
                            foreach ($tipeOrder as $tipe):
                                if (isset($groupedData[$tipe]) && !empty($groupedData[$tipe])):
                        ?>
                        <!-- Header Group -->
                        <tr class="table-secondary">
                            <td colspan="8">
                                <strong>
                                    <i class="fas fa-folder-open me-2"></i>
                                    <?= $tipe === 'Aset' ? 'ASET' : ($tipe === 'Kewajiban' ? 'KEWAJIBAN' : ($tipe === 'Ekuitas' ? 'EKUITAS' : ($tipe === 'Pendapatan' ? 'PENDAPATAN' : 'BEBAN'))) ?>
                                </strong>
                            </td>
                        </tr>
                        
                        <?php foreach ($groupedData[$tipe] as $item): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $item['kode_akun'] ?></td>
                            <td><?= $item['nama_akun'] ?></td>
                            <td><?= $item['tipe_akun'] ?></td>
                            <td>
                                <span class="badge <?= $item['saldo_normal'] == 'Debit' ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $item['saldo_normal'] ?>
                                </span>
                            </td>
                            <td class="text-end text-success">
                                <?php if ($item['debit'] > 0): ?>
                                    <strong>Rp <?= number_format($item['debit'], 0, ',', '.') ?></strong>
                                <?php else: ?>
                                    -
                                <?php endif ?>
                            </td>
                            <td class="text-end text-danger">
                                <?php if ($item['kredit'] > 0): ?>
                                    <strong>Rp <?= number_format($item['kredit'], 0, ',', '.') ?></strong>
                                <?php else: ?>
                                    -
                                <?php endif ?>
                            </td>
                            <td class="text-center">
                                <?php if ($item['debit'] > 0 || $item['kredit'] > 0): ?>
                                    <i class="fas fa-check-circle text-success" title="Memiliki saldo"></i>
                                <?php else: ?>
                                    <i class="fas fa-minus-circle text-muted" title="Saldo nol"></i>
                                <?php endif ?>
                            </td>
                        </tr>
                        <?php 
                            $totalDebit += $item['debit'];
                            $totalKredit += $item['kredit'];
                        ?>
                        <?php endforeach; ?>
                        
                        <!-- Subtotal Group -->
                        <tr class="table-light">
                            <td colspan="5" class="text-end"><strong>Subtotal <?= $tipe ?></strong></td>
                            <td class="text-end text-success"><strong>Rp <?= number_format(array_sum(array_column($groupedData[$tipe], 'debit')), 0, ',', '.') ?></strong></td>
                            <td class="text-end text-danger"><strong>Rp <?= number_format(array_sum(array_column($groupedData[$tipe], 'kredit')), 0, ',', '.') ?></strong></td>
                            <td></td>
                        </tr>
                        
                        <?php 
                            endif;
                            endforeach; 
                        ?>
                        <?php endif ?>
                    </tbody>
                    
                    <!-- Footer Total -->
                    <tfoot class="table-dark">
                        <tr>
                            <th colspan="5" class="text-end">TOTAL NERACA SALDO</th>
                            <th class="text-end">Rp <?= number_format($neraca_saldo['total_debit'] ?? 0, 0, ',', '.') ?></th>
                            <th class="text-end">Rp <?= number_format($neraca_saldo['total_kredit'] ?? 0, 0, ',', '.') ?></th>
                            <th class="text-center">
                                <?php if (($neraca_saldo['is_balance'] ?? false)): ?>
                                    <i class="fas fa-check-circle"></i> Balance
                                <?php else: ?>
                                    <i class="fas fa-exclamation-triangle"></i> Not Balance
                                <?php endif ?>
                            </th>
                        </tr>
                        
                        <!-- Selisih (jika tidak balance) -->
                        <?php if (!($neraca_saldo['is_balance'] ?? false)): ?>
                        <tr class="table-warning">
                            <th colspan="5" class="text-end">SELISIH</th>
                            <th class="text-end">
                                <?php 
                                $selisih = ($neraca_saldo['total_debit'] ?? 0) - ($neraca_saldo['total_kredit'] ?? 0);
                                if ($selisih > 0): 
                                ?>
                                    Rp <?= number_format($selisih, 0, ',', '.') ?>
                                <?php else: ?>
                                    -
                                <?php endif ?>
                            </th>
                            <th class="text-end">
                                <?php if ($selisih < 0): ?>
                                    Rp <?= number_format(abs($selisih), 0, ',', '.') ?>
                                <?php else: ?>
                                    -
                                <?php endif ?>
                            </th>
                            <th></th>
                        </tr>
                        <?php endif ?>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Catatan -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Catatan:</strong>
                <ul class="mb-0 mt-2">
                    <li>Neraca saldo ini menunjukkan saldo setiap akun pada periode yang dipilih.</li>
                    <li>Total Debit harus sama dengan Total Kredit untuk neraca yang balance.</li>
                    <li>Akun dengan saldo nol tidak ditampilkan dalam daftar.</li>
                    <li>Jika tidak balance, periksa kembali jurnal yang sudah diposting.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
// Export data
function exportData(type) {
    const periode = document.querySelector('input[name="periode"]')?.value || '<?= $periode ?? date("Y-m") ?>';
    const tanggal = document.querySelector('input[name="tanggal"]')?.value || '';
    const tipeAkun = document.querySelector('select[name="tipe_akun"]')?.value || '';
    
    let url = '<?= site_url("accounting/pembukuan/buku-besar/export-neraca-saldo") ?>?type=' + type + '&periode=' + periode;
    if (tanggal) url += '&tanggal=' + tanggal;
    if (tipeAkun) url += '&tipe_akun=' + tipeAkun;
    
    window.location.href = url;
}

// Filter by tipe akun - TAMBAHKAN PENGECEKAN ELEMENT EXIST
document.addEventListener('DOMContentLoaded', function() {
    const tipeAkunSelect = document.getElementById('tipeAkun');
    if (tipeAkunSelect) {
        tipeAkunSelect.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    }
});
</script>

<style media="print">
    .btn-group, .card-header .btn, .alert, .navbar, .sidebar, .btn, form, .footer {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    .table {
        font-size: 10px;
    }
    body {
        margin: 0;
        padding: 0;
    }
    .container-fluid {
        padding: 0;
    }
    .no-print {
        display: none !important;
    }
    .card {
        break-inside: avoid;
        page-break-inside: avoid;
    }
    thead {
        display: table-header-group;
    }
    tfoot {
        display: table-footer-group;
    }
</style>

<?= $this->include('accounting/templates/footer') ?>