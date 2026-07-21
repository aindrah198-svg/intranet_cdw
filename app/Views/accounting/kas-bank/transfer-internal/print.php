<?= $this->include('accounting/templates/header_print') ?>

<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
        line-height: 1.4;
        color: #333;
        margin: 20px;
        background: #fff;
    }
    .header {
        text-align: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #4e73df;
        padding-bottom: 15px;
    }
    .header h1 {
        font-size: 24px;
        margin: 0 0 5px 0;
        color: #4e73df;
        font-weight: bold;
    }
    .header h2 {
        font-size: 16px;
        margin: 0 0 5px 0;
        color: #666;
        font-weight: normal;
    }
    .header .subtitle {
        font-size: 14px;
        color: #888;
        margin-top: 5px;
    }
    .title-section {
        background-color: #f8f9fc;
        padding: 10px 15px;
        border-left: 4px solid #4e73df;
        margin-bottom: 20px;
        font-size: 16px;
        font-weight: bold;
        color: #4e73df;
    }
    .info-box {
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 20px;
        background-color: #fff;
    }
    .info-box h3 {
        margin-top: 0;
        margin-bottom: 15px;
        font-size: 14px;
        font-weight: bold;
        color: #495057;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 8px;
    }
    .info-table {
        width: 100%;
        border-collapse: collapse;
    }
    .info-table td {
        padding: 8px 5px;
        border: none;
    }
    .info-table .label {
        width: 150px;
        color: #6c757d;
        font-weight: normal;
    }
    .info-table .value {
        font-weight: bold;
        color: #333;
    }
    .amount-box {
        background-color: #e8f4f8;
        padding: 15px;
        border-radius: 5px;
        text-align: center;
        margin-bottom: 20px;
        border: 1px solid #b8daff;
    }
    .amount-box .label {
        font-size: 14px;
        color: #004085;
        margin-bottom: 5px;
    }
    .amount-box .amount {
        font-size: 28px;
        font-weight: bold;
        color: #004085;
        line-height: 1.2;
    }
    .amount-box .terbilang {
        font-size: 14px;
        color: #004085;
        font-style: italic;
        margin-top: 5px;
    }
    .account-section {
        margin-bottom: 20px;
    }
    .account-row {
        display: flex;
        margin-bottom: 10px;
    }
    .account-box {
        flex: 1;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 15px;
        margin: 0 5px;
        background-color: #f8f9fc;
    }
    .account-box.sumber {
        border-left: 4px solid #dc3545;
    }
    .account-box.tujuan {
        border-left: 4px solid #28a745;
    }
    .account-box .title {
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 5px;
        text-transform: uppercase;
    }
    .account-box .kode-akun {
        font-size: 14px;
        font-weight: bold;
        color: #333;
    }
    .account-box .nama-akun {
        font-size: 16px;
        font-weight: bold;
        margin: 5px 0;
    }
    .account-box .bank-detail {
        font-size: 12px;
        color: #6c757d;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px dashed #dee2e6;
    }
    .journal-section {
        background-color: #f8f9fc;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 20px;
    }
    .journal-section h3 {
        margin-top: 0;
        margin-bottom: 15px;
        font-size: 14px;
        font-weight: bold;
        color: #495057;
    }
    .journal-table {
        width: 100%;
        border-collapse: collapse;
    }
    .journal-table th {
        background-color: #e9ecef;
        padding: 8px;
        text-align: left;
        font-size: 12px;
        font-weight: bold;
        border: 1px solid #dee2e6;
    }
    .journal-table td {
        padding: 8px;
        border: 1px solid #dee2e6;
    }
    .journal-table .text-end {
        text-align: right;
    }
    .journal-table .text-success {
        color: #28a745;
        font-weight: bold;
    }
    .journal-table .text-danger {
        color: #dc3545;
        font-weight: bold;
    }
    .footer {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #dee2e6;
        font-size: 11px;
        color: #6c757d;
    }
    .signature-section {
        margin-top: 40px;
        display: flex;
        justify-content: space-between;
    }
    .signature-box {
        width: 30%;
        text-align: center;
    }
    .signature-line {
        margin-top: 40px;
        margin-bottom: 5px;
        border-top: 1px solid #333;
        width: 100%;
    }
    .badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: bold;
    }
    .badge-success {
        background-color: #28a745;
        color: white;
    }
    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }
    .badge-danger {
        background-color: #dc3545;
        color: white;
    }
    .badge-info {
        background-color: #17a2b8;
        color: white;
    }
    .text-muted {
        color: #6c757d;
    }
    .text-primary {
        color: #4e73df;
    }
    .text-success {
        color: #28a745;
    }
    .text-danger {
        color: #dc3545;
    }
    .mt-4 {
        margin-top: 20px;
    }
    .mb-2 {
        margin-bottom: 10px;
    }
    .mb-3 {
        margin-bottom: 15px;
    }
    .mb-4 {
        margin-bottom: 20px;
    }
    hr {
        border: 0;
        border-top: 1px solid #dee2e6;
        margin: 20px 0;
    }
    table.compact-table td {
        padding: 4px 5px;
    }
    .watermark {
        position: fixed;
        bottom: 20px;
        right: 20px;
        opacity: 0.1;
        font-size: 60px;
        color: #4e73df;
        transform: rotate(-15deg);
        pointer-events: none;
        z-index: 1000;
    }
</style>

<div class="watermark">
    <i class="fas fa-exchange-alt"></i>
</div>

<div class="header">
    <h1>PT. CIPTA DUTA WACANA</h1>
    <h2>BUKTI TRANSFER INTERNAL</h2>
    <div class="subtitle">Nomor: <?= $transfer['kode_transfer'] ?></div>
</div>

<div class="title-section">
    <i class="fas fa-exchange-alt me-2"></i> DETAIL TRANSFER INTERNAL
</div>

<!-- Informasi Umum -->
<div class="info-box">
    <h3>Informasi Transfer</h3>
    <table class="info-table">
        <tr>
            <td class="label">Kode Transfer</td>
            <td class="value">: <?= $transfer['kode_transfer'] ?></td>
            <td class="label" style="width: 150px;">Tanggal Transfer</td>
            <td class="value">: <?= date('d F Y', strtotime($transfer['tanggal'])) ?></td>
        </tr>
        <tr>
            <td class="label">Status</td>
            <td class="value">
                : 
                <?php if ($transfer['status'] == 'Posted'): ?>
                    <span class="badge badge-success">POSTED</span>
                <?php elseif ($transfer['status'] == 'Draft'): ?>
                    <span class="badge badge-warning">DRAFT</span>
                <?php elseif ($transfer['status'] == 'Dibatalkan'): ?>
                    <span class="badge badge-danger">DIBATALKAN</span>
                <?php endif; ?>
            </td>
            <td class="label">Nomor Referensi</td>
            <td class="value">: <?= !empty($transfer['no_referensi']) ? $transfer['no_referensi'] : '-' ?></td>
        </tr>
        <tr>
            <td class="label">Dibuat Oleh</td>
            <td class="value">: <?= $transfer['creator_fullname'] ?? $transfer['creator_name'] ?? '-' ?></td>
            <td class="label">Dibuat Tanggal</td>
            <td class="value">: <?= date('d F Y H:i', strtotime($transfer['created_at'])) ?></td>
        </tr>
        <?php if (!empty($transfer['posted_at'])): ?>
        <tr>
            <td class="label">Diposting Oleh</td>
            <td class="value">: <?= $transfer['posted_by_name'] ?? '-' ?></td>
            <td class="label">Diposting Tanggal</td>
            <td class="value">: <?= date('d F Y H:i', strtotime($transfer['posted_at'])) ?></td>
        </tr>
        <?php endif; ?>
    </table>
</div>

<!-- Jumlah Transfer -->
<div class="amount-box">
    <div class="label">JUMLAH TRANSFER</div>
    <div class="amount">Rp <?= number_format($transfer['jumlah'], 0, ',', '.') ?></div>
    <div class="terbilang"># <?= $transfer['terbilang'] ?> #</div>
</div>

<!-- Akun Sumber dan Tujuan -->
<div class="account-section">
    <div class="account-row">
        <div class="account-box sumber">
            <div class="title">AKUN SUMBER (MENGURANGI SALDO)</div>
            <div class="kode-akun"><?= $transfer['kode_akun_sumber'] ?? '-' ?></div>
            <div class="nama-akun"><?= $transfer['nama_akun_sumber'] ?? '-' ?></div>
            <div class="bank-detail">
                <i class="fas fa-building"></i> 
                <?php if (!empty($transfer['bank_asal'])): ?>
                    Bank Asal: <?= $transfer['bank_asal'] ?>
                <?php else: ?>
                    Tanpa detail bank
                <?php endif; ?>
            </div>
        </div>
        <div class="account-box tujuan">
            <div class="title">AKUN TUJUAN (MENAMBAH SALDO)</div>
            <div class="kode-akun"><?= $transfer['kode_akun_tujuan'] ?? '-' ?></div>
            <div class="nama-akun"><?= $transfer['nama_akun_tujuan'] ?? '-' ?></div>
            <div class="bank-detail">
                <i class="fas fa-building"></i> 
                <?php if (!empty($transfer['bank_tujuan'])): ?>
                    Bank Tujuan: <?= $transfer['bank_tujuan'] ?>
                <?php else: ?>
                    Tanpa detail bank
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Keterangan -->
<div class="info-box">
    <h3>Keterangan</h3>
    <p style="margin: 10px 0; padding: 10px; background-color: #f8f9fa; border-radius: 5px;">
        <?= nl2br(htmlspecialchars($transfer['keterangan'])) ?>
    </p>
</div>

<!-- Jurnal yang dibuat -->
<?php if (!empty($transfer['jurnal_id'])): ?>
<div class="journal-section">
    <h3>JURNAL YANG DIBUAT</h3>
    <table class="journal-table">
        <thead>
            <tr>
                <th>Kode Akun</th>
                <th>Nama Akun</th>
                <th class="text-end">Debit (Rp)</th>
                <th class="text-end">Kredit (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= $transfer['kode_akun_tujuan'] ?? '-' ?></td>
                <td><?= $transfer['nama_akun_tujuan'] ?? '-' ?> (Tujuan)</td>
                <td class="text-end text-success"><?= number_format($transfer['jumlah'], 0, ',', '.') ?></td>
                <td class="text-end">-</td>
            </tr>
            <tr>
                <td><?= $transfer['kode_akun_sumber'] ?? '-' ?></td>
                <td><?= $transfer['nama_akun_sumber'] ?? '-' ?> (Sumber)</td>
                <td class="text-end">-</td>
                <td class="text-end text-danger"><?= number_format($transfer['jumlah'], 0, ',', '.') ?></td>
            </tr>
        </tbody>
        <tfoot>
            <tr style="background-color: #e9ecef; font-weight: bold;">
                <td colspan="2" class="text-end">TOTAL</td>
                <td class="text-end text-success"><?= number_format($transfer['jumlah'], 0, ',', '.') ?></td>
                <td class="text-end text-danger"><?= number_format($transfer['jumlah'], 0, ',', '.') ?></td>
            </tr>
        </tfoot>
    </table>
    <div style="margin-top: 10px; font-size: 11px; color: #6c757d;">
        <i class="fas fa-info-circle"></i> Nomor Jurnal: <?= $transfer['nomor_jurnal'] ?? '#' . $transfer['jurnal_id'] ?>
    </div>
</div>
<?php endif; ?>

<!-- Informasi Tambahan -->
<div class="info-box">
    <h3>INFORMASI TAMBAHAN</h3>
    <table class="info-table compact-table">
        <tr>
            <td class="label">Pengaruh pada Neraca</td>
            <td class="value">: Hanya mengubah komposisi Kas/Bank, total aset tetap sama</td>
        </tr>
        <tr>
            <td class="label">Pengaruh pada Laba Rugi</td>
            <td class="value">: Tidak berpengaruh (bukan pendapatan/biaya)</td>
        </tr>
        <tr>
            <td class="label">Pengaruh pada Arus Kas</td>
            <td class="value">: Masuk dalam aktivitas operasi (internal)</td>
        </tr>
    </table>
</div>

<!-- Tanda Tangan -->
<div class="signature-section">
    <div class="signature-box">
        <div class="signature-line"></div>
        <div><strong>Dibuat Oleh,</strong></div>
        <div style="margin-top: 5px; font-size: 11px;"><?= $transfer['creator_fullname'] ?? $transfer['creator_name'] ?? '-' ?></div>
    </div>
    
    <?php if (!empty($transfer['posted_by_name'])): ?>
    <div class="signature-box">
        <div class="signature-line"></div>
        <div><strong>Diposting Oleh,</strong></div>
        <div style="margin-top: 5px; font-size: 11px;"><?= $transfer['posted_by_name'] ?></div>
    </div>
    <?php endif; ?>
    
    <div class="signature-box">
        <div class="signature-line"></div>
        <div><strong>Mengetahui,</strong></div>
        <div style="margin-top: 5px; font-size: 11px;">(Manajer Keuangan)</div>
    </div>
</div>

<!-- Footer -->
<div class="footer">
    <table style="width: 100%;">
        <tr>
            <td style="text-align: left;">
                <i class="fas fa-print"></i> Dicetak pada: <?= date('d F Y H:i:s') ?>
            </td>
            <td style="text-align: right;">
                <i class="fas fa-user"></i> Dicetak oleh: <?= session()->get('name') ?>
            </td>
        </tr>
    </table>
    <p style="text-align: center; margin-top: 10px; font-size: 10px;">
        Dokumen ini sah dan dicetak dari sistem.<br>
        PT. CIPTA DUTA WACANA - Engineering Division
    </p>
</div>

<script>
    window.print();
</script>

<?= $this->include('accounting/templates/footer_print') ?>