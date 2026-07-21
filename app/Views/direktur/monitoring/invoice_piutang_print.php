<?php
// Data dari controller
$invoiceData = $invoiceData ?? [];
$statusFilter = $statusFilter ?? '';
$clientIdFilter = $clientIdFilter ?? '';
$selectedClient = $selectedClient ?? [];
$startDate = $startDate ?? '';
$endDate = $endDate ?? '';
$stats = $stats ?? [];
$statusOptions = $statusOptions ?? [];

// Helper functions
if (!function_exists('formatRupiah')) {
    function formatRupiah($amount) {
        if (empty($amount) && $amount !== 0) return '-';
        return 'Rp ' . number_format((float)$amount, 0, ',', '.');
    }
}

if (!function_exists('formatDate')) {
    function formatDate($date) {
        if (empty($date)) return '-';
        $timestamp = strtotime($date);
        return $timestamp ? date('d/m/Y', $timestamp) : '-';
    }
}

if (!function_exists('formatDateIndonesia')) {
    function formatDateIndonesia($datetime = null) {
        if (empty($datetime)) {
            return date('d F Y');
        }
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $timestamp = strtotime($datetime);
        if (!$timestamp) return '-';
        $tgl = date('d', $timestamp);
        $bln = (int)date('m', $timestamp);
        $thn = date('Y', $timestamp);
        return "$tgl {$bulan[$bln]} $thn";
    }
}

if (!function_exists('getStatusLabel')) {
    function getStatusLabel($status) {
        $labels = [
            'draft' => 'Draft',
            'sent' => 'Dikirim',
            'partial' => 'Sebagian Dibayar',
            'paid' => 'Lunas',
            'overdue' => 'Overdue',
            'cancelled' => 'Dibatalkan'
        ];
        return $labels[$status] ?? $status;
    }
}

// Filter text
$filterText = [];
if ($statusFilter) $filterText[] = 'Status: ' . getStatusLabel($statusFilter);
if ($clientIdFilter && !empty($selectedClient)) $filterText[] = 'Client: ' . $selectedClient['nama_perusahaan'];
if ($startDate) $filterText[] = 'Tanggal Mulai: ' . formatDate($startDate);
if ($endDate) $filterText[] = 'Tanggal Selesai: ' . formatDate($endDate);

$filterString = !empty($filterText) ? implode(' | ', $filterText) : 'Semua Data';

// Calculate totals
$grandTotal = 0;
$grandSisa = 0;
foreach ($invoiceData as $item) {
    $grandTotal += $item['total'] ?? 0;
    $grandSisa += $item['sisa_piutang'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Invoice & Piutang</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            background: white;
            padding: 20px;
        }
        .print-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
        }
        /* Header */
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #4e73df;
        }
        .header h1 {
            font-size: 22px;
            color: #4e73df;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 16px;
            color: #333;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 10px;
        }
        .company-info {
            margin-bottom: 10px;
        }
        .company-name {
            font-size: 13px;
            font-weight: bold;
            color: #2c3e50;
        }
        /* Filter Info */
        .filter-info {
            background: #f8f9fc;
            padding: 8px 12px;
            margin-bottom: 20px;
            border-radius: 4px;
            border-left: 3px solid #4e73df;
            font-size: 10px;
        }
        /* Statistics Cards */
        .stats-container {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .stat-card {
            flex: 1;
            background: #f8f9fc;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
            border: 1px solid #e3e6f0;
        }
        .stat-card h3 {
            font-size: 18px;
            margin-bottom: 4px;
            color: #4e73df;
        }
        .stat-card p {
            font-size: 10px;
            color: #666;
            margin-bottom: 0;
        }
        /* Tables */
        .table-container {
            margin-bottom: 20px;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #4e73df;
            color: white;
            font-weight: 600;
            text-align: center;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .text-success {
            color: #1cc88a;
        }
        .text-danger {
            color: #e74a3b;
        }
        .text-warning {
            color: #f6c23e;
        }
        .fw-bold {
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-draft { background: #858796; color: white; }
        .badge-sent { background: #36b9cc; color: white; }
        .badge-partial { background: #f6c23e; color: #333; }
        .badge-paid { background: #1cc88a; color: white; }
        .badge-overdue { background: #e74a3b; color: white; }
        .badge-cancelled { background: #5a5c69; color: white; }
        .footer {
            margin-top: 25px;
            padding-top: 12px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9px;
            color: #999;
        }
        .page-break {
            page-break-before: always;
        }
        .signature {
            margin-top: 35px;
            display: flex;
            justify-content: space-between;
        }
        .signature-item {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            margin-top: 35px;
            border-top: 1px solid #333;
            width: 100%;
        }
        .total-row {
            background-color: #e3e6f0;
            font-weight: bold;
        }
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            .print-container {
                padding: 10px;
            }
            .no-print {
                display: none;
            }
            th {
                background-color: #4e73df !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .badge-draft, .badge-sent, .badge-partial, .badge-paid, .badge-overdue, .badge-cancelled {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
        .print-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #4e73df;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            z-index: 1000;
        }
        .print-btn:hover {
            background: #224abe;
        }
        .overdue-row {
            background-color: #fff3f3;
        }
    </style>
</head>
<body>
    <div class="print-container">
        <!-- Print Button -->
        <button class="print-btn no-print" onclick="window.print();">
            <i class="fas fa-print"></i> Cetak / Simpan PDF
        </button>

        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <div class="company-name">PT. CIPTA DUTA WACANA</div>
                <div style="font-size: 9px; color: #666;">
                    Beltway Office Park Tower B Lantai 5, Jl. TB Simatupang No. 41 Ragunan-Pasar Minggu, Jakarta Selatan<br>
                    Telp: (+62-21) 29857462 | Fax: (+62-21) 29857201 | Email: info@cdw-engineering.com
                </div>
            </div>
            <h1>LAPORAN INVOICE & PIUTANG</h1>
            <h2>Periode: <?= !empty($startDate) || !empty($endDate) ? formatDate($startDate) . ' s/d ' . formatDate($endDate) : 'Semua Periode' ?></h2>
            <p>Dicetak: <?= formatDateIndonesia() ?> <?= date('H:i') ?> WIB</p>
        </div>

        <!-- Filter Information -->
        <div class="filter-info">
            <strong>Filter:</strong> <?= $filterString ?>
        </div>

        <!-- Statistics Summary -->
        <div class="stats-container">
            <div class="stat-card">
                <h3><?= number_format($stats['total_invoice'] ?? 0) ?></h3>
                <p>Total Invoice</p>
            </div>
            <div class="stat-card">
                <h3><?= formatRupiah($stats['total_nilai_invoice'] ?? 0) ?></h3>
                <p>Nilai Invoice</p>
            </div>
            <div class="stat-card">
                <h3><?= formatRupiah($stats['total_piutang_belum_dibayar'] ?? 0) ?></h3>
                <p>Piutang Belum Dibayar</p>
            </div>
            <div class="stat-card">
                <h3><?= number_format($stats['total_overdue'] ?? 0) ?></h3>
                <p>Overdue</p>
            </div>
        </div>

        <!-- Main Data Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th width="30">No</th>
                        <th>Nomor Invoice</th>
                        <th>Tanggal</th>
                        <th>Jatuh Tempo</th>
                        <th>Client</th>
                        <th>Deskripsi</th>
                        <th class="text-right">Total</th>
                        <th class="text-right">Sisa Piutang</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($invoiceData)): ?>
                    <tr>
                        <td colspan="9" class="text-center">Tidak ada data invoice untuk periode yang dipilih</td>
                    </tr>
                    <?php else: ?>
                        <?php 
                        $no = 1;
                        $totalAllInvoice = 0;
                        $totalAllSisa = 0;
                        foreach ($invoiceData as $item): 
                            $isOverdue = ($item['status'] != 'paid' && $item['status'] != 'cancelled' && 
                                          strtotime($item['tanggal_jatuh_tempo']) < strtotime(date('Y-m-d')));
                            $totalAllInvoice += $item['total'] ?? 0;
                            $totalAllSisa += $item['sisa_piutang'] ?? 0;
                        ?>
                        <tr> <?= $isOverdue ? 'class="overdue-row"' : '' ?>>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= htmlspecialchars($item['nomor_invoice']) ?></td>
                            <td class="text-center"><?= formatDate($item['tanggal_invoice'] ?? '') ?></td>
                            <td class="text-center <?= $isOverdue ? 'text-danger fw-bold' : '' ?>">
                                <?= formatDate($item['tanggal_jatuh_tempo'] ?? '') ?>
                                <?php if ($isOverdue): ?>*<?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($item['nama_perusahaan'] ?? '-') ?></td>
                            <td><?= htmlspecialchars(substr($item['deskripsi'] ?? '-', 0, 40)) ?>...</td>
                            <td class="text-right"><?= formatRupiah($item['total'] ?? 0) ?></td>
                            <td class="text-right <?= ($item['sisa_piutang'] ?? 0) > 0 ? 'text-warning fw-bold' : 'text-success' ?>">
                                <?= formatRupiah($item['sisa_piutang'] ?? 0) ?>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-<?= $item['status'] ?? 'draft' ?>">
                                    <?= getStatusLabel($item['status'] ?? 'draft') ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <!-- Grand Total Row -->
                        <tr class="total-row">
                            <td colspan="6" class="text-right fw-bold">GRAND TOTAL</td>
                            <td class="text-right fw-bold"><?= formatRupiah($totalAllInvoice) ?></td>
                            <td class="text-right fw-bold"><?= formatRupiah($totalAllSisa) ?></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Aging Report Summary -->
        <?php 
        $agingCurrent = 0;
        $aging31_60 = 0;
        $aging61_90 = 0;
        $aging90Plus = 0;
        
        foreach ($invoiceData as $item) {
            if ($item['status'] == 'paid' || $item['status'] == 'cancelled') continue;
            if (($item['sisa_piutang'] ?? 0) <= 0) continue;
            
            $jatuhTempo = strtotime($item['tanggal_jatuh_tempo']);
            $today = strtotime(date('Y-m-d'));
            $daysOverdue = floor(($today - $jatuhTempo) / 86400);
            
            if ($daysOverdue <= 0) {
                $agingCurrent += $item['sisa_piutang'];
            } elseif ($daysOverdue <= 30) {
                $aging31_60 += $item['sisa_piutang'];
            } elseif ($daysOverdue <= 60) {
                $aging61_90 += $item['sisa_piutang'];
            } else {
                $aging90Plus += $item['sisa_piutang'];
            }
        }
        ?>

        <div style="margin-top: 20px;">
            <h3 style="font-size: 12px; color: #4e73df; margin-bottom: 10px;">Aging Piutang</h3>
            <table style="width: auto; min-width: 300px;">
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th class="text-right">Jumlah Piutang</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Current (0-30 hari)</td>
                        <td class="text-right text-success fw-bold"><?= formatRupiah($agingCurrent) ?></td>
                    </tr>
                    <tr>
                        <td>31-60 hari</td>
                        <td class="text-right text-warning fw-bold"><?= formatRupiah($aging31_60) ?></td>
                    </tr>
                    <tr>
                        <td>61-90 hari</td>
                        <td class="text-right text-orange fw-bold"><?= formatRupiah($aging61_90) ?></td>
                    </tr>
                    <tr>
                        <td>> 90 hari</td>
                        <td class="text-right text-danger fw-bold"><?= formatRupiah($aging90Plus) ?></td>
                    </tr>
                    <tr class="total-row">
                        <td class="fw-bold">TOTAL</td>
                        <td class="text-right fw-bold"><?= formatRupiah($agingCurrent + $aging31_60 + $aging61_90 + $aging90Plus) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Notes -->
        <div style="margin-top: 20px; font-size: 9px; color: #666;">
            <?php if (!empty($invoiceData) && $totalAllSisa > 0): ?>
            <p><strong>Catatan:</strong> Invoice yang ditandai dengan * pada kolom Jatuh Tempo berarti sudah melewati tanggal jatuh tempo (overdue).</p>
            <?php endif; ?>
        </div>

        <!-- Signature -->
        <div class="signature">
            <div class="signature-item">
                <div>Mengetahui,</div>
                <div style="margin-top: 30px;">___________________</div>
                <div>Finance Manager</div>
            </div>
            <div class="signature-item">
                <div>Menyetujui,</div>
                <div style="margin-top: 30px;">___________________</div>
                <div>Direktur</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Laporan ini dihasilkan secara otomatis oleh sistem CDW Engineering Intranet.</p>
            <p>© <?= date('Y') ?> PT. CIPTA DUTA WACANA - All Rights Reserved</p>
        </div>
    </div>
</body>
</html>