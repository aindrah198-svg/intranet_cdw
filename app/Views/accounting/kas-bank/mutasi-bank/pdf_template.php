<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Laporan Mutasi Bank' ?></title>
    <style>
        @page {
            margin: 15mm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9px;
            line-height: 1.3;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .report-container {
            width: 100%;
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
        .header h2 {
            font-size: 11px;
            font-weight: normal;
            margin: 0 0 3px 0;
        }
        .header p {
            margin: 0;
            font-size: 9px;
        }
        .period-box {
            text-align: center;
            margin-bottom: 15px;
            padding: 6px;
            border: 1px solid #000;
            background-color: #f9f9f9;
            font-size: 9px;
        }
        .stats-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .stats-table td {
            padding: 6px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .stats-table .stat-label {
            font-weight: bold;
            background-color: #f5f5f5;
        }
        .stats-table .stat-value {
            font-weight: bold;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 8px;
        }
        .main-table th {
            background-color: #f0f0f0;
            border: 1px solid #000;
            padding: 5px;
            font-weight: bold;
            text-align: center;
        }
        .main-table td {
            border: 1px solid #000;
            padding: 4px 5px;
            vertical-align: top;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .text-left {
            text-align: left;
        }
        .font-bold {
            font-weight: bold;
        }
        .text-success {
            color: #28a745;
        }
        .text-danger {
            color: #dc3545;
        }
        .badge {
            display: inline-block;
            padding: 1px 4px;
            font-size: 7px;
            font-weight: bold;
            border-radius: 2px;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-warning {
            background-color: #ffc107;
            color: black;
        }
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        .footer {
            margin-top: 20px;
            font-size: 8px;
            color: #555;
            text-align: right;
            border-top: 1px solid #ddd;
            padding-top: 8px;
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
        .summary-row {
            background-color: #f9f9f9;
            font-weight: bold;
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

<div class="report-container">
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN MUTASI BANK</h1>
        <h2>PT. CIPTA DUTA WACANA</h2>
        <p>Periode: <?= $periode_text ?? 'Semua Periode' ?></p>
        <?php if (!empty($filter_text)): ?>
        <p style="margin-top: 3px;"><?= $filter_text ?></p>
        <?php endif; ?>
    </div>

    <!-- Statistik Ringkasan -->
    <table class="stats-table">
        <tr>
            <td class="stat-label">Total Transaksi</td>
            <td class="stat-label">Total Masuk</td>
            <td class="stat-label">Total Keluar</td>
            <td class="stat-label">Saldo Akhir</td>
        </tr>
        <tr>
            <td class="stat-value"><?= number_format($stats['total_transaksi'] ?? 0, 0) ?></td>
            <td class="stat-value text-success"><?= formatRupiahPdf($stats['total_masuk'] ?? 0) ?></td>
            <td class="stat-value text-danger"><?= formatRupiahPdf($stats['total_keluar'] ?? 0) ?></td>
            <td class="stat-value"><?= formatRupiahPdf(($stats['total_masuk'] ?? 0) - ($stats['total_keluar'] ?? 0)) ?></td>
        </tr>
    </table>

    <!-- Detail Mutasi Bank -->
    <table class="main-table">
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="8%">Tanggal</th>
                <th width="10%">Kode</th>
                <th width="5%">Tipe</th>
                <th width="12%">Akun Debit</th>
                <th width="12%">Akun Kredit</th>
                <th width="8%">Bank</th>
                <th width="10%">Jumlah</th>
                <th width="8%">Referensi</th>
                <th width="16%">Keterangan</th>
                <th width="8%">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data)): ?>
            <tr>
                <td colspan="11" class="text-center">Tidak ada data mutasi bank untuk periode ini</td>
            </tr>
            <?php else: ?>
                <?php 
                $no = 1;
                $totalMasuk = 0;
                $totalKeluar = 0;
                foreach ($data as $item): 
                    // Gunakan snake_case keys dari model
                    $tipeDatabase = $item['tipe'] ?? ''; // 'Kredit' atau 'Debit'
                    $tipeUser = ($tipeDatabase == 'Kredit') ? 'Masuk' : 'Keluar';
                    $tipeClass = ($tipeUser == 'Masuk') ? 'text-success' : 'text-danger';
                    $jumlah = (float)($item['jumlah'] ?? 0);
                    
                    if ($tipeUser == 'Masuk') {
                        $totalMasuk += $jumlah;
                    } else {
                        $totalKeluar += $jumlah;
                    }
                    
                    $status = $item['status'] ?? 'Draft';
                    $statusClass = '';
                    if ($status == 'Posted') {
                        $statusClass = 'badge-success';
                    } elseif ($status == 'Draft') {
                        $statusClass = 'badge-warning';
                    } elseif ($status == 'Dibatalkan') {
                        $statusClass = 'badge-danger';
                    }
                    
                    $bank = '-';
                    if ($tipeDatabase == 'Debit' && !empty(trim($item['bank_asal'] ?? ''))) {
                        $bank = trim($item['bank_asal']);
                    } elseif ($tipeDatabase == 'Kredit' && !empty(trim($item['bank_tujuan'] ?? ''))) {
                        $bank = trim($item['bank_tujuan']);
                    }
                    
                    // Ambil nama akun dari model
                    $akunDebit = $item['nama_akun_debit'] ?? ($item['kode_akun_debit'] ?? '-');
                    $akunKredit = $item['nama_akun_kredit'] ?? ($item['kode_akun_kredit'] ?? '-');
                    $keterangan = $item['keterangan'] ?? '-';
                    $kodeTransaksi = $item['kode_transaksi'] ?? '-';
                    $tanggal = $item['tanggal'] ?? date('Y-m-d');
                    $noReferensi = $item['no_referensi'] ?? '-';
                    
                    // Batasi panjang teks
                    if (strlen($akunDebit) > 35) $akunDebit = substr($akunDebit, 0, 32) . '...';
                    if (strlen($akunKredit) > 35) $akunKredit = substr($akunKredit, 0, 32) . '...';
                    if (strlen($keterangan) > 40) $keterangan = substr($keterangan, 0, 37) . '...';
                ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td class="text-center"><?= date('d/m/Y', strtotime($tanggal)) ?></td>
                    <td><?= htmlspecialchars($kodeTransaksi) ?></td>
                    <td class="text-center <?= $tipeClass ?>"><?= $tipeUser ?></td>
                    <td><small><?= htmlspecialchars($akunDebit) ?></small></td>
                    <td><small><?= htmlspecialchars($akunKredit) ?></small></td>
                    <td><small><?= htmlspecialchars($bank) ?></small></td>
                    <td class="text-right <?= $tipeClass ?>"><?= number_format($jumlah, 0, ',', '.') ?></td>
                    <td><small><?= htmlspecialchars($noReferensi) ?></small></td>
                    <td><small><?= htmlspecialchars($keterangan) ?></small></td>
                    <td class="text-center"><span class="badge <?= $statusClass ?>"><?= $status ?></span></td>
                </tr>
                <?php endforeach; ?>
                
                <!-- Total Baris -->
                <tr class="summary-row">
                    <td colspan="7" class="text-right font-bold">TOTAL MASUK</td>
                    <td class="text-right font-bold text-success"><?= number_format($totalMasuk, 0, ',', '.') ?></td>
                    <td colspan="3"></td>
                </tr>
                <tr class="summary-row">
                    <td colspan="7" class="text-right font-bold">TOTAL KELUAR</td>
                    <td class="text-right font-bold text-danger"><?= number_format($totalKeluar, 0, ',', '.') ?></td>
                    <td colspan="3"></td>
                </tr>
                <tr class="summary-row" style="background-color: #e8f5e9;">
                    <td colspan="7" class="text-right font-bold">SALDO AKHIR</td>
                    <td class="text-right font-bold"><?= number_format($totalMasuk - $totalKeluar, 0, ',', '.') ?></td>
                    <td colspan="3"></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Ringkasan per Bank -->
    <?php if (!empty($ringkasanBank) && isset($ringkasanBank)): ?>
    <div style="margin-top: 15px;">
        <div style="font-size: 10px; font-weight: bold; border-bottom: 1px solid #000; margin-bottom: 8px; padding-bottom: 3px;">
            RINGKASAN PER BANK
        </div>
        <table class="main-table" style="width: 60%;">
            <thead>
                <tr>
                    <th width="40%">Bank / Akun</th>
                    <th width="20%" class="text-right">Total Masuk</th>
                    <th width="20%" class="text-right">Total Keluar</th>
                    <th width="20%" class="text-right">Saldo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ringkasanBank as $bank): 
                    $saldo = ($bank['total_masuk'] ?? 0) - ($bank['total_keluar'] ?? 0);
                    $saldoClass = $saldo >= 0 ? 'text-success' : 'text-danger';
                ?>
                <tr>
                    <td><small><?= htmlspecialchars(($bank['kode_bank'] ?? '') . ' - ' . ($bank['nama_bank'] ?? '')) ?></small></td>
                    <td class="text-right text-success"><?= number_format($bank['total_masuk'] ?? 0, 0, ',', '.') ?></td>
                    <td class="text-right text-danger"><?= number_format($bank['total_keluar'] ?? 0, 0, ',', '.') ?></td>
                    <td class="text-right <?= $saldoClass ?>"><?= number_format($saldo, 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>



    <!-- Footer -->

</div>

</body>
</html>