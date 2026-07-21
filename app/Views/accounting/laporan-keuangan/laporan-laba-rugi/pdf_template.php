<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
    <style>
        @page {
            margin: 15mm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.3;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 14px;
            font-weight: bold;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            font-size: 10px;
        }
        .status-box {
            text-align: center;
            margin-bottom: 15px;
            padding: 6px;
            border: 1px solid #000;
            font-weight: bold;
            background-color: #f9f9f9;
            font-size: 9px;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            border-bottom: 1px solid #000;
            margin: 12px 0 6px 0;
            padding-bottom: 2px;
            text-transform: uppercase;
        }
        .account-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .account-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .account-table .kode {
            width: 15%;
            color: #555;
            font-size: 9px;
        }
        .account-table .nama {
            width: 65%;
        }
        .account-table .saldo {
            width: 20%;
            text-align: right;
        }
        .total-row td {
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 5px;
            padding-bottom: 5px;
        }
        .subtotal-row td {
            font-weight: bold;
            border-top: 1px dashed #000;
            padding-top: 4px;
            padding-bottom: 4px;
        }
        .info-row {
            margin: 8px 0;
            padding: 6px;
            border: 1px solid #000;
            background-color: #f9f9f9;
        }
        .info-row .label {
            font-weight: bold;
        }
        .info-row .value {
            font-weight: bold;
            text-align: right;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .summary-table td {
            padding: 4px 0;
            border-bottom: 1px dotted #ccc;
        }
        .summary-table .label {
            width: 70%;
        }
        .summary-table .value {
            width: 30%;
            text-align: right;
        }
        .summary-table .total-label {
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 6px;
        }
        .summary-table .total-value {
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 6px;
            text-align: right;
        }
        .footer {
            margin-top: 25px;
            font-size: 8px;
            color: #555;
            text-align: right;
        }
        .signature {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            text-align: center;
            width: 45%;
        }
        .signature-line {
            margin-top: 30px;
            border-top: 1px solid #000;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }
        .signature-label {
            margin-top: 4px;
            font-size: 8px;
        }
        .two-columns {
            width: 100%;
        }
        .two-columns td {
            vertical-align: top;
            width: 50%;
        }
        .left-col {
            padding-right: 15px;
        }
        .right-col {
            padding-left: 15px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .font-bold {
            font-weight: bold;
        }
        .negative {
            color: #d9534f;
        }
        .positive {
            color: #28a745;
        }
        
        <?php
        if (!function_exists('formatRupiahPdf')) {
            function formatRupiahPdf($nilai) {
                $nilai = (float) $nilai;
                if ($nilai < 0) {
                    return '(Rp ' . number_format(abs($nilai), 0, ',', '.') . ')';
                }
                return 'Rp ' . number_format($nilai, 0, ',', '.');
            }
        }
        ?>
    </style>
</head>
<body>

    <div class="header">
        <h1>LAPORAN LABA RUGI</h1>
        <p>PT. CIPTA DUTA WACANA</p>
        <p>Periode: <?= $periode['start_label'] ?? date('d F Y', strtotime($periode_start)) ?> s/d <?= $periode['end_label'] ?? date('d F Y', strtotime($periode_end)) ?></p>
    </div>

    <div class="status-box">
        STATUS: 
        <?php if ($is_profit): ?>
            <span style="color: green;">LABA</span> (Keuntungan: <?= number_format($laba_bersih, 0, ',', '.') ?>)
        <?php elseif ($is_loss): ?>
            <span style="color: red;">RUGI</span> (Kerugian: <?= number_format(abs($laba_bersih), 0, ',', '.') ?>)
        <?php else: ?>
            <span style="color: orange;">BREAK EVEN</span>
        <?php endif; ?>
    </div>

    <!-- Ringkasan Singkat 2 Kolom seperti Neraca -->
    <table class="two-columns">
        <tr>
            <td class="left-col">
                <div class="info-row">
                    <div style="display: flex; justify-content: space-between;">
                        <span class="label">Total Pendapatan</span>
                        <span class="value positive"><?= number_format($total_pendapatan, 0, ',', '.') ?></span>
                    </div>
                </div>
            </td>
            <td class="right-col">
                <div class="info-row">
                    <div style="display: flex; justify-content: space-between;">
                        <span class="label">Total Beban</span>
                        <span class="value negative"><?= number_format(($total_hpp + $total_beban_operasional + $total_beban_lain), 0, ',', '.') ?></span>
                    </div>
                </div>
             </td>
        </tr>
    </table>

    <!-- ============================================ -->
    <!-- PENDAPATAN USAHA -->
    <!-- ============================================ -->
    <div class="section-title">A. PENDAPATAN USAHA</div>
    <table class="account-table">
        <?php if (!empty($pendapatan)): ?>
            <?php foreach ($pendapatan as $item): ?>
            <tr>
                <td class="kode"><?= $item['kode_akun'] ?></td>
                <td class="nama"><?= $item['nama_akun'] ?></td>
                <td class="saldo"><?= number_format($item['saldo'], 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="3" style="text-align:center; font-style:italic;">Tidak ada data</td></tr>
        <?php endif; ?>
        <tr class="total-row">
            <td colspan="2">TOTAL PENDAPATAN USAHA</td>
            <td class="saldo"><?= number_format($total_pendapatan, 0, ',', '.') ?></td>
        </tr>
    </table>

    <!-- ============================================ -->
    <!-- HARGA POKOK PENJUALAN (HPP) -->
    <!-- ============================================ -->
    <?php if (!empty($hpp)): ?>
    <div class="section-title">B. HARGA POKOK PENJUALAN (HPP)</div>
    <table class="account-table">
        <?php foreach ($hpp as $item): ?>
        <tr>
            <td class="kode"><?= $item['kode_akun'] ?></td>
            <td class="nama"><?= $item['nama_akun'] ?></td>
            <td class="saldo"><?= number_format($item['saldo'], 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
        <tr class="total-row">
            <td colspan="2">TOTAL HPP</td>
            <td class="saldo"><?= number_format($total_hpp, 0, ',', '.') ?></td>
        </tr>
    </table>
    <?php endif; ?>

    <!-- LABA KOTOR -->
    <div class="info-row" style="margin: 8px 0; background-color: #f0f0f0;">
        <div style="display: flex; justify-content: space-between;">
            <span class="label">LABA KOTOR</span>
            <span class="value"><?= number_format($laba_kotor, 0, ',', '.') ?></span>
        </div>
        <div style="font-size: 8px; color: #666; margin-top: 2px;">Pendapatan Usaha - Harga Pokok Penjualan</div>
    </div>

    <!-- ============================================ -->
    <!-- BEBAN OPERASIONAL -->
    <!-- ============================================ -->
    <?php if (!empty($beban_operasional)): ?>
    <div class="section-title">C. BEBAN OPERASIONAL</div>
    <table class="account-table">
        <?php foreach ($beban_operasional as $item): ?>
        <tr>
            <td class="kode"><?= $item['kode_akun'] ?></td>
            <td class="nama"><?= $item['nama_akun'] ?></td>
            <td class="saldo"><?= number_format($item['saldo'], 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
        <tr class="total-row">
            <td colspan="2">TOTAL BEBAN OPERASIONAL</td>
            <td class="saldo"><?= number_format($total_beban_operasional, 0, ',', '.') ?></td>
        </tr>
    </table>
    <?php endif; ?>

    <!-- LABA OPERASIONAL -->
    <div class="info-row" style="margin: 8px 0; background-color: #f0f0f0;">
        <div style="display: flex; justify-content: space-between;">
            <span class="label">LABA OPERASIONAL</span>
            <span class="value"><?= number_format($laba_operasional, 0, ',', '.') ?></span>
        </div>
        <div style="font-size: 8px; color: #666; margin-top: 2px;">Laba Kotor - Beban Operasional</div>
    </div>

    <!-- ============================================ -->
    <!-- BEBAN LAIN-LAIN -->
    <!-- ============================================ -->
    <?php if (!empty($beban_lain)): ?>
    <div class="section-title">D. BEBAN LAIN-LAIN</div>
    <table class="account-table">
        <?php foreach ($beban_lain as $item): ?>
        <tr>
            <td class="kode"><?= $item['kode_akun'] ?></td>
            <td class="nama"><?= $item['nama_akun'] ?></td>
            <td class="saldo"><?= number_format($item['saldo'], 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
        <tr class="total-row">
            <td colspan="2">TOTAL BEBAN LAIN-LAIN</td>
            <td class="saldo"><?= number_format($total_beban_lain, 0, ',', '.') ?></td>
        </tr>
    </table>
    <?php endif; ?>

    <!-- LABA/RUGI BERSIH -->
    <div class="info-row" style="margin: 12px 0; background-color: #e8f5e9; border: 1px solid #4caf50;">
        <div style="display: flex; justify-content: space-between;">
            <span class="label" style="font-size: 11px;"><?= $is_profit ? 'LABA BERSIH' : ($is_loss ? 'RUGI BERSIH' : 'BREAK EVEN') ?></span>
            <span class="value" style="font-size: 11px; font-weight: bold;"><?= number_format(abs($laba_bersih), 0, ',', '.') ?></span>
        </div>
        <div style="font-size: 8px; color: #666; margin-top: 2px;">
            <?php if ($is_profit): ?>
            Perusahaan mendapatkan keuntungan
            <?php elseif ($is_loss): ?>
            Perusahaan mengalami kerugian
            <?php else: ?>
            Perusahaan break even (tidak laba tidak rugi)
            <?php endif; ?>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- RINGKASAN & RASIO (2 KOLOM seperti Neraca) -->
    <!-- ============================================ -->
    <table class="two-columns" style="margin-top: 15px;">
        <tr>
            <td class="left-col">
                <div class="section-title" style="margin-top: 0;">E. RINGKASAN</div>
                <table class="summary-table">
                    <tr><td class="label">Total Pendapatan</td><td class="value"><?= number_format($total_pendapatan, 0, ',', '.') ?></td></tr>
                    <?php if ($total_hpp > 0): ?>
                    <tr><td class="label">Total HPP</td><td class="value"><?= number_format($total_hpp, 0, ',', '.') ?></td></tr>
                    <?php endif; ?>
                    <tr><td class="label">Laba Kotor</td><td class="value"><?= number_format($laba_kotor, 0, ',', '.') ?></td></tr>
                    <tr><td class="label">Total Beban Operasional</td><td class="value"><?= number_format($total_beban_operasional, 0, ',', '.') ?></td></tr>
                    <tr><td class="label">Laba Operasional</td><td class="value"><?= number_format($laba_operasional, 0, ',', '.') ?></td></tr>
                    <?php if ($total_beban_lain > 0): ?>
                    <tr><td class="label">Total Beban Lain-lain</td><td class="value"><?= number_format($total_beban_lain, 0, ',', '.') ?></td></tr>
                    <?php endif; ?>
                    <tr class="total-row"><td class="total-label"><?= $is_profit ? 'LABA BERSIH' : ($is_loss ? 'RUGI BERSIH' : 'BREAK EVEN') ?></td><td class="total-value"><?= number_format(abs($laba_bersih), 0, ',', '.') ?></td></tr>
                </table>
            </td>
            <td class="right-col">
                <div class="section-title" style="margin-top: 0;">F. RASIO KEUANGAN</div>
                <table class="summary-table">
                    <tr><td class="label">Gross Profit Margin</td><td class="value"><?= $total_pendapatan > 0 ? number_format(($laba_kotor / $total_pendapatan) * 100, 2) : '0' ?>%</td></tr>
                    <tr><td class="label">Operating Profit Margin</td><td class="value"><?= $total_pendapatan > 0 ? number_format(($laba_operasional / $total_pendapatan) * 100, 2) : '0' ?>%</td></tr>
                    <tr><td class="label">Net Profit Margin</td><td class="value"><?= number_format($margin_laba ?? 0, 2) ?>%</td></tr>
                    <tr><td class="label">Beban terhadap Pendapatan</td><td class="value"><?= $total_pendapatan > 0 ? number_format((($total_hpp + $total_beban_operasional + $total_beban_lain) / $total_pendapatan) * 100, 2) : '0' ?>%</td></tr>
                </table>
            </td>
        </table>
    </table>

    <!-- Footer -->
    <div class="footer">
        Dicetak pada: <?= $date_generated ?? date('d/m/Y H:i:s') ?> oleh <?= $user['name'] ?? 'System' ?>
    </div>

</body>
</html>