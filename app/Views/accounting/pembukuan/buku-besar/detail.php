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
                    <h2 class="mb-1">Detail Buku Besar</h2>
                    <p class="text-muted mb-0">
                        <?= $coa['kode_akun'] ?> - <?= $coa['nama_akun'] ?>
                    </p>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-download me-1"></i> Export
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" onclick="exportData('excel')"><i class="fas fa-file-excel text-success me-2"></i> Excel</a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportData('pdf')"><i class="fas fa-file-pdf text-danger me-2"></i> PDF</a></li>
                    </ul>
                    <a href="<?= site_url('accounting/pembukuan/buku-besar/print?coa_id=' . $coa['id'] . '&tanggal_mulai=' . $start_date . '&tanggal_selesai=' . $end_date) ?>" 
                       class="btn btn-info" target="_blank">
                        <i class="fas fa-print me-1"></i> Print
                    </a>
                    <a href="<?= site_url('accounting/pembukuan/buku-besar') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Akun Card -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Kode Akun</h6>
                    <h3><?= $coa['kode_akun'] ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Tipe Akun</h6>
                    <h3><?= $coa['tipe_akun'] ?></h3>
                    <small>Saldo Normal: <?= $coa['saldo_normal'] ?></small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Saldo Awal</h6>
                    <h3>Rp <?= number_format($buku_besar['saldo_awal'], 0, ',', '.') ?></h3>
                    <small>Per <?= date('d/m/Y', strtotime($start_date)) ?></small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title">Saldo Akhir</h6>
                    <h3>Rp <?= number_format($buku_besar['saldo_akhir'], 0, ',', '.') ?></h3>
                    <small>Per <?= date('d/m/Y', strtotime($end_date)) ?></small>
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
            <form method="get" action="<?= site_url('accounting/pembukuan/buku-besar/detail/' . $coa['id']) ?>" id="filterForm">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Periode</label>
                        <input type="month" class="form-control" name="periode" value="<?= $periode ?? date('Y-m') ?>" onchange="this.form.submit()">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" name="tanggal_mulai" value="<?= $start_date ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" class="form-control" name="tanggal_selesai" value="<?= $end_date ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i> Terapkan</button>
                            <a href="<?= site_url('accounting/pembukuan/buku-besar/detail/' . $coa['id']) ?>" class="btn btn-secondary">Reset</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Buku Besar Table -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-book me-2"></i> 
                Mutasi Transaksi
                <small class="text-muted ms-2">Periode: <?= date('d/m/Y', strtotime($start_date)) ?> - <?= date('d/m/Y', strtotime($end_date)) ?></small>
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="bukuBesarTable">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="10%">Tanggal</th>
                            <th width="15%">No. Jurnal</th>
                            <th width="30%">Keterangan</th>
                            <th width="10%">Referensi</th>
                            <th width="8%">Tipe</th>
                            <th width="10%" class="text-end">Debit</th>
                            <th width="10%" class="text-end">Kredit</th>
                            <th width="10%" class="text-end">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Baris Saldo Awal -->
                        <tr class="table-secondary">
                            <td colspan="6"><strong>Saldo Awal</strong></td>
                            <td class="text-end">
                                <?php if ($buku_besar['saldo_awal'] > 0): ?>
                                    <strong>Rp <?= number_format($buku_besar['saldo_awal'], 0, ',', '.') ?></strong>
                                <?php else: ?>
                                    -
                                <?php endif ?>
                            </td>
                            <td class="text-end">
                                <?php if ($buku_besar['saldo_awal'] < 0): ?>
                                    <strong>Rp <?= number_format(abs($buku_besar['saldo_awal']), 0, ',', '.') ?></strong>
                                <?php else: ?>
                                    -
                                <?php endif ?>
                            </td>
                            <td class="text-end fw-bold">
                                Rp <?= number_format($buku_besar['saldo_awal'], 0, ',', '.') ?>
                            </td>
                        </tr>
                        
                        <?php if (empty($buku_besar['entries'])): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5>Tidak ada transaksi</h5>
                                <p class="text-muted">Tidak ada transaksi pada periode ini</p>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php 
                            $no = 1;
                            foreach ($buku_besar['entries'] as $entry): 
                                // Tentukan badge color berdasarkan tipe jurnal
                                $badgeClass = match($entry['tipe_jurnal']) {
                                    'mutasi_bank' => 'bg-info',
                                    'penyesuaian' => 'bg-warning',
                                    default => 'bg-secondary'
                                };
                                
                                $badgeText = match($entry['tipe_jurnal']) {
                                    'mutasi_bank' => 'Mutasi Bank',
                                    'penyesuaian' => 'Penyesuaian',
                                    default => 'Umum'
                                };
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d/m/Y', strtotime($entry['tanggal'])) ?></td>
                                <td>
                                    <a href="<?= site_url('accounting/pembukuan/jurnal-umum/detail/' . $entry['jurnal_id']) ?>" 
                                       target="_blank" class="text-decoration-none">
                                        <?= $entry['nomor_jurnal'] ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($entry['keterangan']) ?></td>
                                <td>
                                    <?php if (!empty($entry['referensi'])): ?>
                                        <span class="badge bg-light text-dark"><?= $entry['referensi'] ?></span>
                                    <?php else: ?>
                                        -
                                    <?php endif ?>
                                </td>
                                <td>
                                    <span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span>
                                </td>
                                <td class="text-end text-success">
                                    <?php if ($entry['debit'] > 0): ?>
                                        <strong>Rp <?= number_format($entry['debit'], 0, ',', '.') ?></strong>
                                    <?php else: ?>
                                        -
                                    <?php endif ?>
                                </td>
                                <td class="text-end text-danger">
                                    <?php if ($entry['kredit'] > 0): ?>
                                        <strong>Rp <?= number_format($entry['kredit'], 0, ',', '.') ?></strong>
                                    <?php else: ?>
                                        -
                                    <?php endif ?>
                                </td>
                                <td class="text-end fw-bold">
                                    Rp <?= number_format($entry['saldo_akhir'], 0, ',', '.') ?>
                                </td>
                            </tr>
                            <?php endforeach ?>
                        <?php endif ?>
                    </tbody>
                    <?php if (!empty($buku_besar['entries'])): ?>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="6" class="text-end">TOTAL PERIODE:</th>
                            <th class="text-end text-success">
                                <strong>Rp <?= number_format($buku_besar['total_debit'], 0, ',', '.') ?></strong>
                            </th>
                            <th class="text-end text-danger">
                                <strong>Rp <?= number_format($buku_besar['total_kredit'], 0, ',', '.') ?></strong>
                            </th>
                            <th class="text-end fw-bold">
                                Rp <?= number_format($buku_besar['saldo_akhir'], 0, ',', '.') ?>
                            </th>
                        </tr>
                        <tr class="table-info">
                            <th colspan="8" class="text-end">SALDO AKHIR:</th>
                            <th class="text-end fw-bold">
                                Rp <?= number_format($buku_besar['saldo_akhir'], 0, ',', '.') ?>
                            </th>
                        </tr>
                    </tfoot>
                    <?php endif ?>
                </table>
            </div>
        </div>
    </div>

    <!-- Info Tambahan -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i> Informasi Akun</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="35%">Kode Akun</th>
                            <td>: <?= $coa['kode_akun'] ?></td>
                        </tr>
                        <tr>
                            <th>Nama Akun</th>
                            <td>: <?= $coa['nama_akun'] ?></td>
                        </tr>
                        <tr>
                            <th>Tipe Akun</th>
                            <td>: <?= $coa['tipe_akun'] ?></td>
                        </tr>
                        <tr>
                            <th>Saldo Normal</th>
                            <td>: <?= $coa['saldo_normal'] ?></td>
                        </tr>
                        <?php if (!empty($coa['deskripsi'])): ?>
                        <tr>
                            <th>Deskripsi</th>
                            <td>: <?= htmlspecialchars($coa['deskripsi']) ?></td>
                        </tr>
                        <?php endif ?>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i> Ringkasan Periode</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="35%">Periode</th>
                            <td>: <?= date('d/m/Y', strtotime($start_date)) ?> - <?= date('d/m/Y', strtotime($end_date)) ?></td>
                        </tr>
                        <tr>
                            <th>Total Debit</th>
                            <td>: <span class="text-success">Rp <?= number_format($buku_besar['total_debit'], 0, ',', '.') ?></span></td>
                        </tr>
                        <tr>
                            <th>Total Kredit</th>
                            <td>: <span class="text-danger">Rp <?= number_format($buku_besar['total_kredit'], 0, ',', '.') ?></span></td>
                        </tr>
                        <tr>
                            <th>Jumlah Transaksi</th>
                            <td>: <?= count($buku_besar['entries']) ?> transaksi</td>
                        </tr>
                        <tr>
                            <th>Saldo Awal</th>
                            <td>: Rp <?= number_format($buku_besar['saldo_awal'], 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <th>Saldo Akhir</th>
                            <td>: <strong>Rp <?= number_format($buku_besar['saldo_akhir'], 0, ',', '.') ?></strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportData(type) {
    const coaId = <?= $coa['id'] ?>;
    const startDate = '<?= $start_date ?>';
    const endDate = '<?= $end_date ?>';
    
    window.location.href = '<?= site_url("accounting/pembukuan/buku-besar/export") ?>?coa_id=' + coaId + '&type=' + type + '&tanggal_mulai=' + startDate + '&tanggal_selesai=' + endDate;
}

// Optional: Print functionality
function printTable() {
    const printWindow = window.open('', '_blank');
    const tableContent = document.getElementById('bukuBesarTable').outerHTML;
    const title = '<?= $coa['kode_akun'] ?> - <?= $coa['nama_akun'] ?>';
    const periode = 'Periode: <?= date('d/m/Y', strtotime($start_date)) ?> - <?= date('d/m/Y', strtotime($end_date)) ?>';
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Buku Besar - <?= $coa['kode_akun'] ?></title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
                h1 { text-align: center; font-size: 18px; }
                h2 { text-align: center; font-size: 14px; color: #666; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #000; padding: 6px; }
                th { background-color: #f0f0f0; }
                .text-end { text-align: right; }
                .text-center { text-align: center; }
                .footer { margin-top: 30px; text-align: right; font-size: 10px; }
            </style>
        </head>
        <body>
            <h1>BUKU BESAR</h1>
            <h2><?= $coa['kode_akun'] ?> - <?= $coa['nama_akun'] ?></h2>
            <div class="text-center"><?= $periode ?></div>
            ${tableContent}
            <div class="footer">
                Dicetak pada: <?= date('d/m/Y H:i:s') ?><br>
                Oleh: <?= session()->get('name') ?? 'System' ?>
            </div>
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}
</script>

<?= $this->include('accounting/templates/footer') ?>