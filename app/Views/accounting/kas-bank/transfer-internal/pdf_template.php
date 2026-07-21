<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Laporan Transfer Internal' ?></title>
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
        <h1>LAPORAN TRANSFER INTERNAL</h1>
        <h2>PT. CIPTA DUTA WACANA</h2>
        <p>Periode: <?= $periode_text ?? 'Semua Periode' ?></p>
        <?php if (!empty($filter_text)): ?>
        <p style="margin-top: 3px;"><?= $filter_text ?></p>
        <?php endif; ?>
    </div>

    <!-- Statistik Ringkasan -->
    <table class="stats-table">
        <tr>
            <td class="stat-label">Total Transfer</td>
            <td class="stat-label">Total Nilai Transfer</td>
            <td class="stat-label">Transaksi Hari Ini</td>
            <td class="stat-label">Jumlah Hari Ini</td>
        </tr>
        <tr>
            <td class="stat-value"><?= number_format($stats['total_transaksi'] ?? 0, 0) ?></td>
            <td class="stat-value"><?= formatRupiahPdf($stats['total_transfer'] ?? 0) ?></td>
            <td class="stat-value"><?= number_format($stats['transaksi_hari_ini'] ?? 0, 0) ?></td>
            <td class="stat-value"><?= formatRupiahPdf($stats['jumlah_hari_ini'] ?? 0) ?></td>
        </tr>
    </table>

    <!-- Detail Transfer Internal -->
    <table class="main-table">
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="8%">Tanggal</th>
                <th width="10%">Kode</th>
                <th width="14%">Akun Sumber</th>
                <th width="14%">Akun Tujuan</th>
                <th width="8%">Bank Asal</th>
                <th width="8%">Bank Tujuan</th>
                <th width="10%">Jumlah</th>
                <th width="15%">Keterangan</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data)): ?>
            <tr>
                <td colspan="10" class="text-center">Tidak ada data transfer internal untuk periode ini</td>
            </tr>
            <?php else: ?>
                <?php 
                $no = 1;
                $totalNilai = 0;
                foreach ($data as $item): 
                    $totalNilai += $item['Jumlah'];
                    
                    $statusClass = '';
                    if ($item['Status'] == 'Posted') {
                        $statusClass = 'badge-success';
                    } elseif ($item['Status'] == 'Draft') {
                        $statusClass = 'badge-warning';
                    } elseif ($item['Status'] == 'Dibatalkan') {
                        $statusClass = 'badge-danger';
                    }
                    
                    // Batasi panjang teks
                    $akunSumber = $item['Akun Sumber'] ?? '-';
                    $akunTujuan = $item['Akun Tujuan'] ?? '-';
                    $keterangan = $item['Keterangan'] ?? '-';
                    if (strlen($akunSumber) > 40) $akunSumber = substr($akunSumber, 0, 37) . '...';
                    if (strlen($akunTujuan) > 40) $akunTujuan = substr($akunTujuan, 0, 37) . '...';
                    if (strlen($keterangan) > 45) $keterangan = substr($keterangan, 0, 42) . '...';
                ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td class="text-center"><?= date('d/m/Y', strtotime($item['Tanggal'])) ?></td>
                    <td class="text-center"><?= $item['Kode Transfer'] ?></td>
                    <td><small><?= htmlspecialchars($akunSumber) ?></small></td>
                    <td><small><?= htmlspecialchars($akunTujuan) ?></small></td>
                    <td><small><?= htmlspecialchars($item['Bank Asal'] ?? '-') ?></small></td>
                    <td><small><?= htmlspecialchars($item['Bank Tujuan'] ?? '-') ?></small></td>
                    <td class="text-right"><?= number_format($item['Jumlah'], 0, ',', '.') ?></td>
                    <td><small><?= htmlspecialchars($keterangan) ?></small></td>
                    <td class="text-center"><span class="badge <?= $statusClass ?>"><?= $item['Status'] ?></span></td>
                </tr>
                <?php endforeach; ?>
                
                <!-- Total Baris -->
                <tr class="summary-row" style="background-color: #e8f5e9;">
                    <td colspan="7" class="text-right font-bold">TOTAL NILAI TRANSFER</td>
                    <td class="text-right font-bold"><?= number_format($totalNilai, 0, ',', '.') ?></td>
                    <td colspan="2"></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Informasi Tambahan -->
    <div style="margin-top: 15px; border: 1px solid #ddd; padding: 10px; background-color: #f9f9f9;">
        <div style="font-size: 9px; font-weight: bold; margin-bottom: 5px;">
            Informasi:
        </div>
        <div style="font-size: 8px; color: #666;">
            Transfer internal mencatat perpindahan dana antar rekening perusahaan. 
            Transaksi ini tidak menimbulkan pendapatan atau biaya, hanya memindahkan posisi harta.
        </div>
    </div>

    <!-- Tanda Tangan -->
    <div class="signature">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-label">Dibuat oleh,</div>
            <div class="signature-label">(Accounting Staff)</div>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-label">Disetujui oleh,</div>
            <div class="signature-label">(Finance Manager)</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        Dicetak pada: <?= $date_generated ?? date('d/m/Y H:i:s') ?> oleh <?= $user_name ?? 'System' ?>
    </div>
</div>

</body>
</html>