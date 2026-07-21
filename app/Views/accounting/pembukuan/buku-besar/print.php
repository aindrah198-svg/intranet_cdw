<?= $this->include('accounting/templates/header') ?>
<?= $this->include('accounting/templates/sidebar') ?>
<?= $this->include('accounting/templates/navbar') ?>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Print Buku Besar</h2>
                    <p class="text-muted mb-0">
                        <?= $coa['kode_akun'] ?> - <?= $coa['nama_akun'] ?>
                    </p>
                </div>
                <div class="btn-group no-print">
                    <button type="button" class="btn btn-primary" onclick="window.print()">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                    <a href="<?= site_url('accounting/pembukuan/buku-besar/detail/' . $coa['id']) ?>?tanggal_mulai=<?= $start_date ?>&tanggal_selesai=<?= $end_date ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Content -->
    <div class="print-container">
        <!-- Kop Surat -->
        <div class="text-center mb-4">
            <h2 class="mb-0">PT. CIPTA DUTA WACANA</h2>
            <p class="mb-0">Beltway Office Park Tower B Lantai 5, Jl. TB Simatupang No. 41</p>
            <p class="mb-0">Ragunan - Pasar Minggu, Jakarta Selatan 12550</p>
            <p>Telp: (+62-21) 29857462 | Email: info@cdw-engineering.com</p>
            <hr class="my-3">
            <h3 class="mb-1">BUKU BESAR</h3>
            <h4 class="mb-0"><?= $coa['kode_akun'] ?> - <?= $coa['nama_akun'] ?></h4>
            <p class="mb-0">Periode: <?= date('d/m/Y', strtotime($start_date)) ?> s/d <?= date('d/m/Y', strtotime($end_date)) ?></p>
        </div>

        <!-- Info Akun -->
        <div class="row mb-4">
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <th width="30%">Kode Akun</th>
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
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <th width="30%">Saldo Normal</th>
                        <td>: <?= $coa['saldo_normal'] ?></td>
                    </tr>
                    <tr>
                        <th>Saldo Awal</th>
                        <td>: Rp <?= number_format($buku_besar['saldo_awal'], 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <th>Saldo Akhir</th>
                        <td>: Rp <?= number_format($buku_besar['saldo_akhir'], 0, ',', '.') ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Tabel Transaksi -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th width="5%">No</th>
                        <th width="12%">Tanggal</th>
                        <th width="15%">No. Jurnal</th>
                        <th width="33%">Keterangan</th>
                        <th width="10%">Referensi</th>
                        <th width="12%" class="text-end">Debit</th>
                        <th width="12%" class="text-end">Kredit</th>
                        <th width="10%" class="text-end">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Saldo Awal -->
                    <tr class="table-secondary">
                        <td colspan="5"><strong>Saldo Awal</strong></td>
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
                        <td colspan="8" class="text-center py-4">
                            <em>Tidak ada transaksi pada periode ini</em>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php 
                        $no = 1;
                        foreach ($buku_besar['entries'] as $entry): 
                        ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td class="text-center"><?= date('d/m/Y', strtotime($entry['tanggal'])) ?></td>
                            <td><?= $entry['nomor_jurnal'] ?></td>
                            <td><?= htmlspecialchars($entry['keterangan']) ?></td>
                            <td class="text-center"><?= $entry['referensi'] ?? '-' ?></td>
                            <td class="text-end">
                                <?= $entry['debit'] > 0 ? 'Rp ' . number_format($entry['debit'], 0, ',', '.') : '-' ?>
                            </td>
                            <td class="text-end">
                                <?= $entry['kredit'] > 0 ? 'Rp ' . number_format($entry['kredit'], 0, ',', '.') : '-' ?>
                            </td>
                            <td class="text-end fw-bold">
                                Rp <?= number_format($entry['saldo_akhir'], 0, ',', '.') ?>
                            </td>
                        </tr>
                        <?php endforeach ?>
                    <?php endif ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="5" class="text-end">TOTAL PERIODE:</th>
                        <th class="text-end">Rp <?= number_format($buku_besar['total_debit'], 0, ',', '.') ?></th>
                        <th class="text-end">Rp <?= number_format($buku_besar['total_kredit'], 0, ',', '.') ?></th>
                        <th class="text-end"></th>
                    </tr>
                    <tr class="table-info">
                        <th colspan="7" class="text-end">SALDO AKHIR:</th>
                        <th class="text-end fw-bold">Rp <?= number_format($buku_besar['saldo_akhir'], 0, ',', '.') ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Tanda Tangan -->
        <div class="row mt-5 pt-4">
            <div class="col-6">
                <div class="text-center">
                    <p>Mengetahui,</p>
                    <p class="mb-5 pt-4">_________________________</p>
                    <p>Kepala Bagian Akuntansi</p>
                </div>
            </div>
            <div class="col-6">
                <div class="text-center">
                    <p>Jakarta, <?= date('d F Y') ?></p>
                    <p class="mb-5 pt-4">_________________________</p>
                    <p>Petugas</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer text-center mt-5 pt-3">
            <hr>
            <small class="text-muted">
                Dicetak pada: <?= date('d/m/Y H:i:s') ?> | 
                Oleh: <?= $printed_by ?? session()->get('name') ?? 'System' ?> | 
                Hal: <?= $coa['kode_akun'] ?> - <?= $coa['nama_akun'] ?>
            </small>
        </div>
    </div>
</div>

<style media="print">
    /* Print Styles */
    @page {
        size: A4 landscape;
        margin: 1.5cm;
    }
    
    body {
        margin: 0;
        padding: 0;
        font-size: 11px;
    }
    
    .no-print {
        display: none !important;
    }
    
    .container-fluid {
        padding: 0 !important;
        margin: 0 !important;
    }
    
    .print-container {
        padding: 0;
        margin: 0;
    }
    
    .table {
        font-size: 10px;
        width: 100%;
        border-collapse: collapse;
    }
    
    .table th, .table td {
        border: 1px solid #000;
        padding: 4px;
    }
    
    .table-dark th {
        background-color: #000 !important;
        color: #fff !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    .table-secondary {
        background-color: #f0f0f0 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    .table-light {
        background-color: #f8f9fa !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    .table-info {
        background-color: #d1ecf1 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    h2, h3, h4 {
        margin: 5px 0;
    }
    
    hr {
        border: 1px solid #000;
        margin: 10px 0;
    }
    
    .footer {
        position: fixed;
        bottom: 0;
        width: 100%;
        font-size: 8px;
    }
    
    .text-end {
        text-align: right;
    }
    
    .text-center {
        text-align: center;
    }
    
    .fw-bold {
        font-weight: bold;
    }
</style>

<style>
    /* Screen Styles for preview */
    .print-container {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    
    .table {
        font-size: 12px;
    }
    
    .table th, .table td {
        vertical-align: middle;
    }
    
    .footer {
        font-size: 10px;
        color: #6c757d;
    }
</style>

<script>
// Auto print when page loads (optional)
// Uncomment if you want auto print
/*
window.addEventListener('load', function() {
    setTimeout(function() {
        window.print();
    }, 1000);
});
*/
</script>

<?= $this->include('accounting/templates/footer') ?>