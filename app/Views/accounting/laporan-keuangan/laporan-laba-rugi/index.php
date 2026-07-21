<?php
/**
 * LAPORAN LABA RUGI - INCOME STATEMENT
 * Format: Multi-Step (Bertingkat)
 * 
 * Data dari Controller: LabaRugi.php
 * Data dari Model: LabaRugiModel.php
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
                    <h2 class="mb-1">Laporan Laba Rugi</h2>
                    <p class="text-muted mb-0">
                        <i class="fas fa-calendar-alt me-1"></i> 
                        Periode: <?= $filters['periode_label'] ?? date('d F Y') ?>
                    </p>
                </div>
                <div>
                    <button type="button" class="btn btn-danger" onclick="exportLaporanPdf()">
                        <i class="fas fa-file-pdf me-2"></i> Export PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Periode -->
    <div class="card mb-4">
        <div class="card-body">
            <form id="filterForm" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Tanggal Mulai</label>
                    <input type="date" class="form-control" name="tanggal_mulai" 
                           value="<?= $filters['tanggal_mulai'] ?? date('Y-m-01') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Tanggal Selesai</label>
                    <input type="date" class="form-control" name="tanggal_selesai" 
                           value="<?= $filters['tanggal_selesai'] ?? date('Y-m-t') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Periode Cepat</label>
                    <select class="form-select" id="quick_period" onchange="applyQuickPeriod(this.value)">
                        <option value="">-- Pilih Periode --</option>
                        <option value="this_month">Bulan Ini</option>
                        <option value="last_month">Bulan Lalu</option>
                        <option value="this_quarter">Kuartal Ini</option>
                        <option value="this_year">Tahun Ini</option>
                        <option value="last_year">Tahun Lalu</option>
                        <?php foreach ($recentPeriods ?? [] as $period): ?>
                        <option value="period_<?= $period['start'] ?>_<?= $period['end'] ?>">
                            <?= $period['label'] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Status Laba Rugi -->
    <div class="alert alert-<?= $is_profit ? 'success' : ($is_loss ? 'danger' : 'info') ?> alert-dismissible fade show">
        <div class="d-flex align-items-center">
            <i class="fas fa-<?= $is_profit ? 'trophy' : ($is_loss ? 'exclamation-triangle' : 'balance-scale') ?> fa-2x me-3"></i>
            <div>
                <strong>STATUS: <?= $is_profit ? 'LABA ✓' : ($is_loss ? 'RUGI ✗' : 'BREAK EVEN') ?></strong>
                <p class="mb-0">
                    Laba/Rugi Bersih: <?= formatRupiahView($laba_bersih ?? 0) ?>
                    <?php if ($margin_laba > 0): ?>
                    (Margin: <?= number_format($margin_laba, 2) ?>%)
                    <?php endif; ?>
                </p>
                <small class="text-muted">
                    Total Pendapatan: <?= formatRupiahView($total_pendapatan ?? 0) ?> | 
                    Total Beban: <?= formatRupiahView(($total_hpp ?? 0) + ($total_beban_operasional ?? 0) + ($total_beban_lain ?? 0)) ?>
                </small>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <?php foreach ($stats as $key => $stat): ?>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start-<?= $stat['color'] ?> border-start-3 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-<?= $stat['color'] ?> text-uppercase mb-1">
                                <?= $stat['label'] ?>
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                <?= $stat['value'] ?>
                            </div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-<?= $stat['color'] ?> me-2">
                                    <i class="fas <?= $stat['icon'] ?>"></i>
                                </span>
                                <span>Periode Berjalan</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas <?= $stat['icon'] ?> fa-2x text-<?= $stat['color'] ?>"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- MAIN LAPORAN LABA RUGI -->
    <div class="row">
        <div class="col-12">
            <div class="modern-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i> 
                        Laporan Laba Rugi (Multi-Step)
                    </h4>
                    <span class="badge bg-info p-2">
                        <i class="fas fa-calendar-alt me-1"></i>
                        <?= $filters['periode_label'] ?? date('d F Y') ?>
                    </span>
                </div>

                <!-- Company Header -->
                <div class="text-center mb-4">
                    <h2 class="text-primary">PT. CIPTA DUTA WACANA</h2>
                    <h4 class="text-dark">LAPORAN LABA RUGI</h4>
                    <p class="text-muted">
                        Untuk periode <?= date('d F Y', strtotime($filters['tanggal_mulai'] ?? date('Y-m-01'))) ?> 
                        s/d <?= date('d F Y', strtotime($filters['tanggal_selesai'] ?? date('Y-m-t'))) ?>
                    </p>
                </div>

                <!-- ============================================ -->
                <!-- PENDAPATAN USAHA -->
                <!-- ============================================ -->
                <div class="laporan-section mb-4">
                    <h5 class="section-title bg-success text-white p-2 rounded">
                        <i class="fas fa-money-bill-wave me-2"></i> PENDAPATAN USAHA
                    </h5>
                    
                    <?php if (empty($pendapatan)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Tidak ada data pendapatan untuk periode ini.
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="15%">Kode Akun</th>
                                    <th width="65%">Nama Akun</th>
                                    <th width="20%" class="text-end">Jumlah (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendapatan as $item): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-success"><?= $item['kode_akun'] ?></span>
                                    </td>
                                    <td><?= $item['nama_akun'] ?></td>
                                    <td class="text-end fw-bold text-success">
                                        <?= formatRupiahView($item['saldo']) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <tr class="table-active fw-bold">
                                    <td colspan="2" class="text-end">TOTAL PENDAPATAN USAHA</td>
                                    <td class="text-end text-success">
                                        <?= formatRupiahView($total_pendapatan) ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- ============================================ -->
                <!-- HARGA POKOK PENJUALAN (HPP) -->
                <!-- ============================================ -->
                <?php if (!empty($hpp)): ?>
                <div class="laporan-section mb-4">
                    <h5 class="section-title bg-warning text-dark p-2 rounded">
                        <i class="fas fa-boxes me-2"></i> HARGA POKOK PENJUALAN (HPP)
                    </h5>
                    
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="15%">Kode Akun</th>
                                    <th width="65%">Nama Akun</th>
                                    <th width="20%" class="text-end">Jumlah (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($hpp as $item): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-warning"><?= $item['kode_akun'] ?></span>
                                    </td>
                                    <td><?= $item['nama_akun'] ?></td>
                                    <td class="text-end text-danger">
                                        <?= formatRupiahView($item['saldo']) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <tr class="table-active fw-bold">
                                    <td colspan="2" class="text-end">TOTAL HPP</td>
                                    <td class="text-end text-danger">
                                        <?= formatRupiahView($total_hpp) ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

                <!-- ============================================ -->
                <!-- LABA KOTOR -->
                <!-- ============================================ -->
                <div class="alert alert-primary">
                    <div class="d-flex justify-content-between align-items-center">
                        <strong class="fs-5">LABA KOTOR</strong>
                        <strong class="fs-5 <?= $laba_kotor >= 0 ? 'text-success' : 'text-danger' ?>">
                            <?= formatRupiahView($laba_kotor) ?>
                        </strong>
                    </div>
                    <small class="text-muted">Pendapatan Usaha - Harga Pokok Penjualan</small>
                </div>

                <!-- ============================================ -->
                <!-- BEBAN OPERASIONAL -->
                <!-- ============================================ -->
                <?php if (!empty($beban_operasional)): ?>
                <div class="laporan-section mb-4">
                    <h5 class="section-title bg-danger text-white p-2 rounded">
                        <i class="fas fa-file-invoice-dollar me-2"></i> BEBAN OPERASIONAL
                    </h5>
                    
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="15%">Kode Akun</th>
                                    <th width="65%">Nama Akun</th>
                                    <th width="20%" class="text-end">Jumlah (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($beban_operasional as $item): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-danger"><?= $item['kode_akun'] ?></span>
                                    </td>
                                    <td><?= $item['nama_akun'] ?></td>
                                    <td class="text-end text-danger">
                                        <?= formatRupiahView($item['saldo']) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <tr class="table-active fw-bold">
                                    <td colspan="2" class="text-end">TOTAL BEBAN OPERASIONAL</td>
                                    <td class="text-end text-danger">
                                        <?= formatRupiahView($total_beban_operasional) ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

                <!-- ============================================ -->
                <!-- LABA OPERASIONAL -->
                <!-- ============================================ -->
                <div class="alert alert-info">
                    <div class="d-flex justify-content-between align-items-center">
                        <strong class="fs-5">LABA OPERASIONAL</strong>
                        <strong class="fs-5 <?= $laba_operasional >= 0 ? 'text-success' : 'text-danger' ?>">
                            <?= formatRupiahView($laba_operasional) ?>
                        </strong>
                    </div>
                    <small class="text-muted">Laba Kotor - Beban Operasional</small>
                </div>

                <!-- ============================================ -->
                <!-- BEBAN LAIN-LAIN -->
                <!-- ============================================ -->
                <?php if (!empty($beban_lain)): ?>
                <div class="laporan-section mb-4">
                    <h5 class="section-title bg-secondary text-white p-2 rounded">
                        <i class="fas fa-tools me-2"></i> BEBAN LAIN-LAIN
                    </h5>
                    
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="15%">Kode Akun</th>
                                    <th width="65%">Nama Akun</th>
                                    <th width="20%" class="text-end">Jumlah (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($beban_lain as $item): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary"><?= $item['kode_akun'] ?></span>
                                    </td>
                                    <td><?= $item['nama_akun'] ?></td>
                                    <td class="text-end text-danger">
                                        <?= formatRupiahView($item['saldo']) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <tr class="table-active fw-bold">
                                    <td colspan="2" class="text-end">TOTAL BEBAN LAIN-LAIN</td>
                                    <td class="text-end text-danger">
                                        <?= formatRupiahView($total_beban_lain) ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

                <!-- ============================================ -->
                <!-- LABA/RUGI BERSIH -->
                <!-- ============================================ -->
                <div class="alert alert-<?= $is_profit ? 'success' : ($is_loss ? 'danger' : 'info') ?> mt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <strong class="fs-4"><?= $is_profit ? 'LABA BERSIH' : ($is_loss ? 'RUGI BERSIH' : 'BREAK EVEN') ?></strong>
                        <strong class="fs-4 <?= $is_profit ? 'text-white' : ($is_loss ? 'text-white' : 'text-dark') ?>">
                            <?= formatRupiahView($laba_bersih) ?>
                        </strong>
                    </div>
                    <small>
                        <?php if ($is_profit): ?>
                        Perusahaan mendapatkan keuntungan sebesar <?= formatRupiahView($laba_bersih) ?>
                        <?php elseif ($is_loss): ?>
                        Perusahaan mengalami kerugian sebesar <?= formatRupiahView(abs($laba_bersih)) ?>
                        <?php else: ?>
                        Perusahaan break even (tidak laba tidak rugi)
                        <?php endif; ?>
                    </small>
                </div>

                <!-- ============================================ -->
                <!-- RINGKASAN -->
                <!-- ============================================ -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="text-muted">Ringkasan</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="60%">Total Pendapatan</td>
                                        <td width="40%" class="text-end text-success fw-bold">
                                            <?= formatRupiahView($total_pendapatan) ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Total HPP</td>
                                        <td class="text-end text-danger">
                                            <?= formatRupiahView($total_hpp) ?>
                                        </td>
                                    </tr>
                                    <tr class="border-top">
                                        <td><strong>Laba Kotor</strong></td>
                                        <td class="text-end text-primary fw-bold">
                                            <?= formatRupiahView($laba_kotor) ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Total Beban Operasional</td>
                                        <td class="text-end text-danger">
                                            <?= formatRupiahView($total_beban_operasional) ?>
                                        </td>
                                    </tr>
                                    <tr class="border-top">
                                        <td><strong>Laba Operasional</strong></td>
                                        <td class="text-end text-primary fw-bold">
                                            <?= formatRupiahView($laba_operasional) ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Total Beban Lain-lain</td>
                                        <td class="text-end text-danger">
                                            <?= formatRupiahView($total_beban_lain) ?>
                                        </td>
                                    </tr>
                                    <tr class="border-top fw-bold">
                                        <td><strong><?= $is_profit ? 'LABA BERSIH' : ($is_loss ? 'RUGI BERSIH' : 'BREAK EVEN') ?></strong></td>
                                        <td class="text-end <?= $is_profit ? 'text-success' : ($is_loss ? 'text-danger' : 'text-info') ?>">
                                            <?= formatRupiahView($laba_bersih) ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="text-muted">Rasio Keuangan</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="60%">Gross Profit Margin</td>
                                        <td width="40%" class="text-end fw-bold">
                                            <?= $total_pendapatan > 0 ? number_format(($laba_kotor / $total_pendapatan) * 100, 2) : '0' ?>%
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Operating Profit Margin</td>
                                        <td class="text-end fw-bold">
                                            <?= $total_pendapatan > 0 ? number_format(($laba_operasional / $total_pendapatan) * 100, 2) : '0' ?>%
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Net Profit Margin</td>
                                        <td class="text-end fw-bold">
                                            <?= number_format($margin_laba, 2) ?>%
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Beban terhadap Pendapatan</td>
                                        <td class="text-end fw-bold">
                                            <?= $total_pendapatan > 0 ? number_format((($total_hpp + $total_beban_operasional + $total_beban_lain) / $total_pendapatan) * 100, 2) : '0' ?>%
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DETAIL AKUN (Collapsible) -->
                <div class="card mt-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">
                            <button class="btn btn-link text-white p-0" type="button" data-bs-toggle="collapse" data-bs-target="#detailAkunCollapse">
                                <i class="fas fa-list me-2"></i> Detail Semua Akun
                            </button>
                        </h5>
                    </div>
                    <div class="collapse" id="detailAkunCollapse">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Kode Akun</th>
                                            <th>Nama Akun</th>
                                            <th>Tipe</th>
                                            <th class="text-end">Saldo Periode (Rp)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $allAkun = array_merge($pendapatan, $hpp, $beban_operasional, $beban_lain);
                                        if (empty($allAkun)): 
                                        ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Tidak ada data akun untuk periode ini
                                            </td>
                                        </tr>
                                        <?php else: ?>
                                        <?php foreach ($allAkun as $akun): ?>
                                        <tr>
                                            <td><span class="badge bg-secondary"><?= htmlspecialchars($akun['kode_akun'] ?? '') ?></span></td>
                                            <td><?= htmlspecialchars($akun['nama_akun'] ?? '') ?></td>
                                            <td>
                                                <?php 
                                                $tipe = '';
                                                if (strpos($akun['kode_akun'], '4-') === 0) $tipe = 'Pendapatan';
                                                elseif (strpos($akun['kode_akun'], '5-11') === 0) $tipe = 'HPP';
                                                elseif (strpos($akun['kode_akun'], '5-') === 0) $tipe = 'Beban';
                                                echo $tipe;
                                                ?>
                                            </td>
                                            <td class="text-end <?= ($akun['saldo'] ?? 0) < 0 ? 'text-danger' : 'text-success' ?>">
                                                <?= formatRupiahView($akun['saldo'] ?? 0) ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center text-muted small mt-4">
                    Dicetak: <?= $date_generated ?? date('d/m/Y H:i:s') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Export PDF function
function exportLaporanPdf() {
    const startDate = document.querySelector('input[name="tanggal_mulai"]').value;
    const endDate = document.querySelector('input[name="tanggal_selesai"]').value;
    window.location.href = '<?= site_url("accounting/laporan-keuangan/laporan/laba-rugi/export-pdf") ?>?tanggal_mulai=' + startDate + '&tanggal_selesai=' + endDate;
}

// Quick period function
function applyQuickPeriod(period) {
    const today = new Date();
    let startDate = '';
    let endDate = '';
    
    switch(period) {
        case 'this_month':
            startDate = formatDate(new Date(today.getFullYear(), today.getMonth(), 1));
            endDate = formatDate(new Date(today.getFullYear(), today.getMonth() + 1, 0));
            break;
        case 'last_month':
            startDate = formatDate(new Date(today.getFullYear(), today.getMonth() - 1, 1));
            endDate = formatDate(new Date(today.getFullYear(), today.getMonth(), 0));
            break;
        case 'this_quarter':
            const quarter = Math.floor(today.getMonth() / 3);
            startDate = formatDate(new Date(today.getFullYear(), quarter * 3, 1));
            endDate = formatDate(new Date(today.getFullYear(), quarter * 3 + 3, 0));
            break;
        case 'this_year':
            startDate = formatDate(new Date(today.getFullYear(), 0, 1));
            endDate = formatDate(new Date(today.getFullYear(), 11, 31));
            break;
        case 'last_year':
            startDate = formatDate(new Date(today.getFullYear() - 1, 0, 1));
            endDate = formatDate(new Date(today.getFullYear() - 1, 11, 31));
            break;
        default:
            if (period && period.startsWith('period_')) {
                const parts = period.replace('period_', '').split('_');
                if (parts.length === 2) {
                    startDate = parts[0];
                    endDate = parts[1];
                }
            }
            break;
    }
    
    if (startDate && endDate) {
        document.querySelector('input[name="tanggal_mulai"]').value = startDate;
        document.querySelector('input[name="tanggal_selesai"]').value = endDate;
        document.getElementById('filterForm').submit();
    }
}

function formatDate(date) {
    const d = new Date(date);
    const month = '' + (d.getMonth() + 1);
    const day = '' + d.getDate();
    const year = d.getFullYear();
    return [year, month.padStart(2, '0'), day.padStart(2, '0')].join('-');
}

// Date validation
const tanggalMulai = document.querySelector('input[name="tanggal_mulai"]');
const tanggalSelesai = document.querySelector('input[name="tanggal_selesai"]');

if (tanggalMulai && tanggalSelesai) {
    tanggalMulai.addEventListener('change', function() {
        if (this.value && tanggalSelesai.value && this.value > tanggalSelesai.value) {
            alert('Tanggal mulai tidak boleh lebih besar dari tanggal selesai');
            this.value = '';
        }
    });
    
    tanggalSelesai.addEventListener('change', function() {
        if (this.value && tanggalMulai.value && this.value < tanggalMulai.value) {
            alert('Tanggal selesai tidak boleh lebih kecil dari tanggal mulai');
            this.value = '';
        }
    });
}

// Auto-dismiss alerts
setTimeout(() => {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        if (alert.classList.contains('alert-dismissible')) {
            const bsAlert = new bootstrap.Alert(alert);
            setTimeout(() => bsAlert.close(), 5000);
        }
    });
}, 5000);
</script>

<style>
.modern-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    border: none;
}

.section-title {
    font-weight: 600;
    margin-bottom: 15px;
    border-left: 4px solid currentColor;
    padding-left: 15px;
}

.border-start-3 {
    border-left-width: 3px !important;
}

.table-sm td {
    padding: 6px 8px;
    vertical-align: middle;
}

@media print {
    .btn, .dropdown, .modal, .alert-dismissible, 
    .filter-section, .btn-group, [onclick] {
        display: none !important;
    }
    
    .modern-card {
        box-shadow: none !important;
        padding: 0 !important;
    }
    
    .container-fluid {
        padding: 0 !important;
    }
}
</style>

<?= $this->include('accounting/templates/footer') ?>