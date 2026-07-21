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
            font-size: 11px;
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
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            font-size: 12px;
        }
        .status-box {
            text-align: center;
            margin-bottom: 15px;
            padding: 8px;
            border: 1px solid #000;
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
        }
        .main-table > tbody > tr > td {
            vertical-align: top;
            width: 50%;
        }
        .left-col {
            padding-right: 15px;
        }
        .right-col {
            padding-left: 15px;
            border-left: 1px solid #ddd;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            border-bottom: 1px solid #000;
            margin-bottom: 5px;
            padding-bottom: 3px;
            text-transform: uppercase;
        }
        .account-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .account-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .account-table .kode {
            width: 20%;
            color: #555;
            font-size: 10px;
        }
        .account-table .nama {
            width: 50%;
        }
        .account-table .saldo {
            width: 30%;
            text-align: right;
        }
        .subtotal-row td {
            font-weight: bold;
            border-top: 1px dashed #000;
            padding-top: 5px;
            padding-bottom: 5px;
        }
        .total-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            background-color: #f0f0f0;
            margin-top: 5px;
            margin-bottom: 20px;
        }
        .total-table td {
            padding: 8px 5px;
            font-weight: bold;
            font-size: 12px;
        }
        .total-table .label {
            text-align: left;
        }
        .total-table .value {
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            font-size: 9px;
            color: #555;
            text-align: right;
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
        <h1>LAPORAN NERACA (BALANCE SHEET)</h1>
        <p>PT. CIPTA DUTA WACANA</p>
        <p>Per Tanggal: <?= date('d F Y', strtotime($periodeDate)) ?></p>
    </div>

    <div class="status-box">
        STATUS: 
        <?php if ($verifikasi['is_seimbang'] ?? false): ?>
            <span style="color: green;">SEIMBANG</span>
        <?php else: ?>
            <span style="color: red;">TIDAK SEIMBANG</span><br>
            <span style="font-size: 10px; color: red;">Selisih: <?= formatRupiahPdf($verifikasi['selisih'] ?? 0) ?></span>
        <?php endif; ?>
    </div>

    <table class="main-table">
        <tbody>
            <tr>
                <!-- KOLOM KIRI: ASET -->
                <td class="left-col">
                    <div class="section-title">A. ASET LANCAR</div>
                    <table class="account-table">
                        <?php if (!empty($aset_lancar)): ?>
                            <?php foreach ($aset_lancar as $akun): ?>
                            <tr>
                                <td class="kode"><?= $akun['kode_akun'] ?? '' ?></td>
                                <td class="nama"><?= $akun['nama_akun'] ?? '' ?></td>
                                <td class="saldo"><?= formatRupiahPdf($akun['saldo'] ?? 0) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" style="text-align:center; font-style:italic;">Tidak ada data</td></tr>
                        <?php endif; ?>
                        <tr class="subtotal-row">
                            <td colspan="2">Total Aset Lancar</td>
                            <td class="saldo"><?= formatRupiahPdf($subtotal_aset_lancar ?? 0) ?></td>
                        </tr>
                    </table>

                    <div class="section-title">B. ASET TETAP</div>
                    <table class="account-table">
                        <?php if (!empty($aset_tetap)): ?>
                            <?php foreach ($aset_tetap as $akun): ?>
                            <tr>
                                <td class="kode"><?= $akun['kode_akun'] ?? '' ?></td>
                                <td class="nama"><?= $akun['nama_akun'] ?? '' ?></td>
                                <td class="saldo"><?= formatRupiahPdf($akun['saldo'] ?? 0) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" style="text-align:center; font-style:italic;">Tidak ada data</td></tr>
                        <?php endif; ?>
                        <tr class="subtotal-row">
                            <td colspan="2">Total Aset Tetap</td>
                            <td class="saldo"><?= formatRupiahPdf($subtotal_aset_tetap ?? 0) ?></td>
                        </tr>
                    </table>

                    <?php if (!empty($aset_lainnya)): ?>
                    <div class="section-title">C. ASET LAINNYA</div>
                    <table class="account-table">
                        <?php foreach ($aset_lainnya as $akun): ?>
                        <tr>
                            <td class="kode"><?= $akun['kode_akun'] ?? '' ?></td>
                            <td class="nama"><?= $akun['nama_akun'] ?? '' ?></td>
                            <td class="saldo"><?= formatRupiahPdf($akun['saldo'] ?? 0) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="subtotal-row">
                            <td colspan="2">Total Aset Lainnya</td>
                            <td class="saldo"><?= formatRupiahPdf($subtotal_aset_lainnya ?? 0) ?></td>
                        </tr>
                    </table>
                    <?php endif; ?>

                    <table class="total-table">
                        <tr>
                            <td class="label">TOTAL ASET</td>
                            <td class="value"><?= $total_aset_formatted ?? 'Rp 0' ?></td>
                        </tr>
                    </table>
                </td>

                <!-- KOLOM KANAN: KEWAJIBAN & EKUITAS -->
                <td class="right-col">
                    <div class="section-title">A. KEWAJIBAN LANCAR</div>
                    <table class="account-table">
                        <?php if (!empty($kewajiban_lancar)): ?>
                            <?php foreach ($kewajiban_lancar as $akun): ?>
                            <tr>
                                <td class="kode"><?= $akun['kode_akun'] ?? '' ?></td>
                                <td class="nama"><?= $akun['nama_akun'] ?? '' ?></td>
                                <td class="saldo"><?= formatRupiahPdf($akun['saldo'] ?? 0) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" style="text-align:center; font-style:italic;">Tidak ada data</td></tr>
                        <?php endif; ?>
                        <tr class="subtotal-row">
                            <td colspan="2">Total Kewajiban Lancar</td>
                            <td class="saldo"><?= formatRupiahPdf($subtotal_kewajiban_lancar ?? 0) ?></td>
                        </tr>
                    </table>

                    <?php if (!empty($kewajiban_jangka_panjang)): ?>
                    <div class="section-title">B. KEWAJIBAN JANGKA PANJANG</div>
                    <table class="account-table">
                        <?php foreach ($kewajiban_jangka_panjang as $akun): ?>
                        <tr>
                            <td class="kode"><?= $akun['kode_akun'] ?? '' ?></td>
                            <td class="nama"><?= $akun['nama_akun'] ?? '' ?></td>
                            <td class="saldo"><?= formatRupiahPdf($akun['saldo'] ?? 0) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="subtotal-row">
                            <td colspan="2">Total Kewajiban Jangka Panjang</td>
                            <td class="saldo"><?= formatRupiahPdf($subtotal_kewajiban_jangka_panjang ?? 0) ?></td>
                        </tr>
                    </table>
                    <?php endif; ?>

                    <table class="total-table" style="background-color: transparent; border: 1px dashed #000;">
                        <tr>
                            <td class="label">TOTAL KEWAJIBAN</td>
                            <td class="value"><?= $total_kewajiban_formatted ?? 'Rp 0' ?></td>
                        </tr>
                    </table>

                    <div class="section-title">C. EKUITAS</div>
                    <table class="account-table">
                        <?php foreach ($ekuitas as $akun): 
                            if (!isset($akun['is_laba_berjalan']) || !$akun['is_laba_berjalan']):
                        ?>
                        <tr>
                            <td class="kode"><?= $akun['kode_akun'] ?? '' ?></td>
                            <td class="nama"><?= $akun['nama_akun'] ?? '' ?></td>
                            <td class="saldo"><?= formatRupiahPdf($akun['saldo'] ?? 0) ?></td>
                        </tr>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                        
                        <?php if (($laba_bersih ?? 0) != 0): ?>
                        <tr>
                            <td class="kode">LABA</td>
                            <td class="nama">Laba Tahun Berjalan</td>
                            <td class="saldo"><?= formatRupiahPdf($laba_bersih ?? 0) ?></td>
                        </tr>
                        <?php endif; ?>

                        <tr class="subtotal-row">
                            <td colspan="2">Total Ekuitas</td>
                            <td class="saldo"><?= formatRupiahPdf(($total_ekuitas ?? 0)) ?></td>
                        </tr>
                    </table>

                    <table class="total-table">
                        <tr>
                            <td class="label">TOTAL KEWAJIBAN & EKUITAS</td>
                            <td class="value"><?= formatRupiahPdf(($total_kewajiban ?? 0) + ($total_ekuitas ?? 0)) ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- VERIFIKASI AKHIR -->
    <div style="margin-top: 15px; border: 1px solid #000; padding: 10px; background-color: #f9f9f9;">
        <div style="font-weight: bold; font-size: 12px; margin-bottom: 5px; border-bottom: 1px solid #ccc; padding-bottom: 5px;">VERIFIKASI PERSAMAAN AKUNTANSI (ASET = KEWAJIBAN + EKUITAS)</div>
        <div style="text-align: center; margin-top: 5px;">
            <div style="font-size: 14px; font-weight: bold; margin-bottom: 5px;"><?= $verifikasi['formula'] ?? '' ?></div>
            <div style="font-weight: bold; color: <?= ($verifikasi['is_seimbang'] ?? false) ? 'green' : 'red' ?>;">
                <?= ($verifikasi['is_seimbang'] ?? false) ? 'NERACA SEIMBANG' : 'NERACA TIDAK SEIMBANG' ?>
            </div>
            <?php if (!($verifikasi['is_seimbang'] ?? false)): ?>
            <div style="color: red; font-size: 10px; margin-top: 3px;">Selisih: <?= formatRupiahPdf($verifikasi['selisih'] ?? 0) ?></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- RASIO KEUANGAN -->
    <div style="margin-top: 15px; border: 1px solid #000; padding: 10px;">
        <div style="font-weight: bold; font-size: 12px; margin-bottom: 10px; border-bottom: 1px solid #ccc; padding-bottom: 5px;">RASIO KEUANGAN</div>
        <table style="width: 100%; text-align: center; border-collapse: collapse;">
            <tr>
                <td style="width: 25%; padding: 5px;">
                    <div style="font-size: 10px; color: #555; font-weight: bold;">Rasio Lancar</div>
                    <div style="font-weight: bold; font-size: 14px; margin: 5px 0;"><?= $rasio['current_ratio'] ?? '0' ?> : 1</div>
                    <div style="font-size: 8px; color: #777;">Aset Lancar / Kewajiban Lancar</div>
                </td>
                <td style="width: 25%; padding: 5px; border-left: 1px dashed #ccc;">
                    <div style="font-size: 10px; color: #555; font-weight: bold;">Debt to Equity</div>
                    <div style="font-weight: bold; font-size: 14px; margin: 5px 0;"><?= $rasio['debt_to_equity'] ?? '0' ?></div>
                    <div style="font-size: 8px; color: #777;">Total Kewajiban / Ekuitas</div>
                </td>
                <td style="width: 25%; padding: 5px; border-left: 1px dashed #ccc;">
                    <div style="font-size: 10px; color: #555; font-weight: bold;">Debt to Assets</div>
                    <div style="font-weight: bold; font-size: 14px; margin: 5px 0;"><?= $rasio['debt_to_assets'] ?? '0' ?></div>
                    <div style="font-size: 8px; color: #777;">Total Kewajiban / Total Aset</div>
                </td>
                <td style="width: 25%; padding: 5px; border-left: 1px dashed #ccc;">
                    <div style="font-size: 10px; color: #555; font-weight: bold;">Modal Kerja</div>
                    <div style="font-weight: bold; font-size: 14px; margin: 5px 0; color: #000;"><?= $rasio['working_capital_formatted'] ?? 'Rp 0' ?></div>
                    <div style="font-size: 8px; color: #777;">Aset Lancar - Kewajiban Lancar</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Dicetak pada: <?= $date_generated ?? date('d/m/Y H:i:s') ?> oleh <?= $user['name'] ?? 'System' ?>
    </div>

</body>
</html>
