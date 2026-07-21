<?php
// File: app/Views/admin/absensi/export_pdf.php

// Convert DateTime to string jika diperlukan
if (isset($startDate) && $startDate instanceof DateTime) {
    $startDateStr = $startDate->format('Y-m-d');
} else {
    $startDateStr = (string)($startDate ?? date('Y-m-01'));
}

if (isset($endDate) && $endDate instanceof DateTime) {
    $endDateStr = $endDate->format('Y-m-d');
} else {
    $endDateStr = (string)($endDate ?? date('Y-m-d'));
}

$pdfTitle = "Laporan Absensi Karyawan";
$period = "Periode: " . date('d/m/Y', strtotime($startDateStr)) . " s/d " . date('d/m/Y', strtotime($endDateStr));
$exportDate = "Tanggal Export: " . date('d/m/Y H:i:s');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $pdfTitle ?></title>
    <style>
        /* PDF Styles - LANDSCAPE */
        @page {
            margin: 10mm 5mm;
            size: A4 landscape;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 7px;
            line-height: 1.2;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #4e73df;
        }
        
        .company-name {
            font-size: 14px;
            font-weight: bold;
            color: #2e59d9;
            margin-bottom: 2px;
        }
        
        .report-title {
            font-size: 12px;
            font-weight: bold;
            margin: 3px 0;
        }
        
        .period-info {
            font-size: 9px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .summary-section {
            margin-bottom: 10px;
            padding: 5px;
            background: #f8f9fa;
            border-radius: 3px;
            border-left: 2px solid #4e73df;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 5px;
            margin-top: 5px;
        }
        
        .summary-item {
            text-align: center;
            padding: 4px;
            background: white;
            border-radius: 2px;
            box-shadow: 0 1px 1px rgba(0,0,0,0.1);
        }
        
        .summary-value {
            font-size: 10px;
            font-weight: bold;
            color: #2e59d9;
            margin-bottom: 1px;
        }
        
        .summary-label {
            font-size: 6px;
            color: #6c757d;
            text-transform: uppercase;
        }
        
        /* Table Styles for Landscape - OPTIMIZED */
        .table-container {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 6px;
            table-layout: fixed; /* Tambahkan ini untuk kontrol lebar kolom */
        }
        
        .table-header {
            background: #4e73df;
            color: white;
            font-weight: bold;
        }
        
        .table-header th {
            padding: 4px 2px;
            border: 1px solid #3c60d8;
            text-align: center;
            font-size: 6px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .table-body td {
            padding: 3px 2px;
            border: 1px solid #ddd;
            text-align: center;
            vertical-align: middle;
            font-size: 6px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .table-body tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .text-left {
            text-align: left !important;
        }
        
        .text-center {
            text-align: center !important;
        }
        
        .text-right {
            text-align: right !important;
        }
        
        /* Status and Shift Badges - OPTIMIZED */
        .status-badge {
            padding: 1px 3px;
            border-radius: 6px;
            font-size: 6px;
            font-weight: bold;
            display: inline-block;
            min-width: 30px;
        }
        
        .status-hadir { background-color: #d1f2eb; color: #155724; }
        .status-izin { background-color: #cce5ff; color: #004085; }
        .status-sakit { background-color: #fff3cd; color: #856404; }
        .status-cuti { background-color: #e7f3ff; color: #0d6efd; }
        .status-alpha { background-color: #f8d7da; color: #721c24; }
        
        .shift-badge {
            padding: 1px 3px;
            border-radius: 6px;
            font-size: 6px;
            font-weight: bold;
            display: inline-block;
            min-width: 25px;
        }
        
        .shift-pagi { background-color: #ffc107; color: #000; }
        .shift-siang { background-color: #28a745; color: white; }
        .shift-sore { background-color: #17a2b8; color: white; }
        .shift-malam { background-color: #6f42c1; color: white; }
        
        .late-badge {
            background-color: #dc3545;
            color: white;
            padding: 1px 2px;
            border-radius: 2px;
            font-size: 6px;
            display: inline-block;
        }
        
        /* Filter Info */
        .filter-info {
            background: #e7f3ff;
            padding: 4px;
            border-radius: 2px;
            margin-bottom: 5px;
            font-size: 6px;
            border: 1px solid #cce5ff;
        }
        
        /* Footer */
        .footer {
            margin-top: 15px;
            padding-top: 5px;
            border-top: 1px solid #ddd;
            font-size: 6px;
            color: #666;
        }
        
        .footer-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        
        /* Page Break */
        .page-break {
            page-break-before: always;
        }
        
        .no-data {
            text-align: center;
            padding: 20px;
            color: #6c757d;
            font-style: italic;
        }
        
        /* Statistics Table */
        .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 6px;
        }
        
        .stats-table th {
            background: #f8f9fa;
            padding: 4px;
            border: 1px solid #ddd;
            text-align: left;
            font-size: 6px;
        }
        
        .stats-table td {
            padding: 3px;
            border: 1px solid #ddd;
            font-size: 6px;
        }
        
        /* Progress Bar for Statistics */
        .progress-bar {
            height: 6px;
            background: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
            margin-top: 2px;
        }
        
        .progress-fill {
            height: 100%;
            background: #4e73df;
        }
        
        /* Compact styles for long text */
        .compact-text {
            font-size: 5.5px;
            line-height: 1.1;
            max-height: 15px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .truncate {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: inline-block;
            max-width: 100%;
        }
        
        /* Column widths - VERY OPTIMIZED */
        .col-no { width: 20px; }
        .col-date { width: 55px; }
        .col-name { width: 90px; }
        .col-nik { width: 60px; }
        .col-dept { width: 40px; }
        .col-shift { width: 35px; }
        .col-masuk { width: 40px; }
        .col-pulang { width: 40px; }
        .col-jam { width: 45px; }
        .col-status { width: 40px; }
        .col-terlambat { width: 45px; }
        .col-lokasi { width: 70px; }
        .col-keterangan { width: 60px; }
        
        /* Print-specific styles */
        @media print {
            body {
                font-size: 6px;
            }
            
            .table-header th {
                font-size: 5.5px;
                padding: 3px 2px;
            }
            
            .table-body td {
                font-size: 5.5px;
                padding: 2px 1px;
            }
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header">
        <div class="company-name">CDW ENGINEERING</div>
        <div class="report-title">LAPORAN ABSENSI KARYAWAN</div>
        <div class="period-info">
            <?= $period ?><br>
            <?= $exportDate ?>
        </div>
    </div>
    
    <!-- Filter Information -->
    <?php if (!empty($statusFilter) || !empty($karyawanIdFilter)): ?>
    <div class="filter-info">
        <strong>Filter yang diterapkan:</strong>
        <?php if (!empty($statusFilter)): ?>
            <span style="background: #d1f2eb; padding: 1px 3px; border-radius: 2px; margin: 0 3px; font-size: 6px;">
                Status: <?= $statusFilter ?>
            </span>
        <?php endif; ?>
        <?php if (!empty($karyawanIdFilter) && !empty($selectedKaryawan)): ?>
            <span style="background: #e7f3ff; padding: 1px 3px; border-radius: 2px; margin: 0 3px; font-size: 6px;">
                Karyawan: <?= esc($selectedKaryawan['nama_lengkap'] ?? '') ?> (<?= esc($selectedKaryawan['nik'] ?? '') ?>)
            </span>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <!-- Summary Section -->
    <div class="summary-section">
        <strong>Ringkasan Data:</strong>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value"><?= $totalAbsensi ?? 0 ?></div>
                <div class="summary-label">Total Absensi</div>
            </div>
            <div class="summary-item">
                <div class="summary-value"><?= $totalKaryawan ?? 0 ?></div>
                <div class="summary-label">Karyawan</div>
            </div>
            <div class="summary-item">
                <div class="summary-value"><?= $totalHadir ?? 0 ?></div>
                <div class="summary-label">Hadir</div>
            </div>
            <div class="summary-item">
                <div class="summary-value"><?= $totalTerlambat ?? 0 ?></div>
                <div class="summary-label">Terlambat</div>
            </div>
            <div class="summary-item">
                <div class="summary-value"><?= number_format($totalLembur ?? 0, 1) ?>h</div>
                <div class="summary-label">Total Lembur</div>
            </div>
        </div>
    </div>
    
    <!-- Attendance Data Table -->
    <?php if (!empty($absensiData)): ?>
        <table class="table-container">
            <thead class="table-header">
                <tr>
                    <th class="col-no">No</th>
                    <th class="col-date">Tanggal</th>
                    <th class="col-name">Nama</th>
                    <th class="col-nik">NIK</th>
                    <th class="col-dept">Dept</th>
                    <th class="col-shift">Shift</th>
                    <th class="col-masuk">Masuk</th>
                    <th class="col-pulang">Pulang</th>
                    <th class="col-jam">Jam Kerja</th>
                    <th class="col-status">Status</th>
                    <th class="col-terlambat">Terlambat</th>
                    <th class="col-lokasi">Lokasi</th>
                    <th class="col-keterangan">Keterangan</th>
                </tr>
            </thead>
            <tbody class="table-body">
                <?php $no = 1; ?>
                <?php foreach ($absensiData as $absensi): ?>
                    <?php if ($no > 1 && $no % 40 == 1): ?>
                        <!-- Page break every 40 rows -->
                        </tbody>
                        </table>
                        <div class="page-break"></div>
                        
                        <!-- Repeat Header on new page -->
                        <div class="header">
                            <div class="company-name">CDW ENGINEERING</div>
                            <div class="report-title">LAPORAN ABSENSI KARYAWAN (Lanjutan)</div>
                            <div class="period-info"><?= $period ?></div>
                        </div>
                        
                        <table class="table-container">
                            <thead class="table-header">
                                <tr>
                                    <th class="col-no">No</th>
                                    <th class="col-date">Tanggal</th>
                                    <th class="col-name">Nama</th>
                                    <th class="col-nik">NIK</th>
                                    <th class="col-dept">Dept</th>
                                    <th class="col-shift">Shift</th>
                                    <th class="col-masuk">Masuk</th>
                                    <th class="col-pulang">Pulang</th>
                                    <th class="col-jam">Jam Kerja</th>
                                    <th class="col-status">Status</th>
                                    <th class="col-terlambat">Terlambat</th>
                                    <th class="col-lokasi">Lokasi</th>
                                    <th class="col-keterangan">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="table-body">
                    <?php endif; ?>
                    
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td class="text-center">
                            <?= date('d/m/Y', strtotime($absensi['tanggal'])) ?>
                        </td>
                        <td class="text-left compact-text">
                            <div class="truncate" title="<?= esc($absensi['nama_lengkap'] ?? '-') ?>">
                                <?= esc($absensi['nama_lengkap'] ?? '-') ?>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="truncate"><?= esc($absensi['nik'] ?? '-') ?></div>
                        </td>
                        <td class="text-center compact-text">
                            <div class="truncate"><?= esc($absensi['departemen'] ?? '-') ?></div>
                        </td>
                        <td class="text-center">
                            <?php if ($absensi['shift']): ?>
                                <span class="shift-badge shift-<?= $absensi['shift'] ?>">
                                    <?= strtoupper(substr($absensi['shift'], 0, 1)) ?>
                                </span>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ($absensi['waktu_masuk']): ?>
                                <?= date('H:i', strtotime($absensi['waktu_masuk'])) ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ($absensi['waktu_pulang']): ?>
                                <?= date('H:i', strtotime($absensi['waktu_pulang'])) ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ($absensi['jam_kerja']): ?>
                                <strong style="font-size: 6px;"><?= number_format($absensi['jam_kerja'], 1) ?>h</strong>
                                <?php if ($absensi['jam_lembur'] > 0): ?>
                                    <br>
                                    <small style="color: #dc3545; font-size: 5px;">+<?= number_format($absensi['jam_lembur'], 1) ?>h</small>
                                <?php endif; ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ($absensi['status']): ?>
                                <span class="status-badge status-<?= strtolower($absensi['status']) ?>">
                                    <?= substr($absensi['status'], 0, 1) ?>
                                </span>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ($absensi['terlambat'] && $absensi['terlambat'] > 0): ?>
                                <span class="late-badge">
                                    <?php
                                    $jam = floor($absensi['terlambat'] / 60);
                                    $menit = $absensi['terlambat'] % 60;
                                    if ($jam > 0) {
                                        echo $jam . 'j';
                                    } else {
                                        echo $menit . 'm';
                                    }
                                    ?>
                                </span>
                            <?php else: ?>
                                <span style="color: #28a745; font-size: 5.5px;">OK</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-left compact-text">
                            <?php if ($absensi['lokasi_masuk'] && $absensi['lokasi_masuk'] != 'Lokasi tidak terdeteksi'): ?>
                                <div class="truncate" title="<?= esc($absensi['lokasi_masuk']) ?>">
                                    <?php 
                                    // Singkatkan lokasi jika terlalu panjang
                                    $lokasi = $absensi['lokasi_masuk'];
                                    if (strlen($lokasi) > 25) {
                                        echo esc(substr($lokasi, 0, 22)) . '...';
                                    } else {
                                        echo esc($lokasi);
                                    }
                                    ?>
                                </div>
                            <?php else: ?>
                                <span style="color: #6c757d;">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-left compact-text">
                            <?php if ($absensi['keterangan']): ?>
                                <div class="truncate" title="<?= esc($absensi['keterangan']) ?>">
                                    <?php 
                                    // Singkatkan keterangan jika terlalu panjang
                                    $keterangan = $absensi['keterangan'];
                                    if (strlen($keterangan) > 20) {
                                        echo esc(substr($keterangan, 0, 17)) . '...';
                                    } else {
                                        echo esc($keterangan);
                                    }
                                    ?>
                                </div>
                            <?php else: ?>
                                <span style="color: #6c757d;">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="no-data">
            <p>Tidak ada data absensi untuk periode ini.</p>
        </div>
    <?php endif; ?>
    
    <!-- Statistics Section (Page 2) -->
    <?php if (!empty($absensiData) && count($absensiData) > 0): ?>
        <div class="page-break"></div>
        
        <div class="header">
            <div class="company-name">CDW ENGINEERING</div>
            <div class="report-title">ANALISIS & STATISTIK ABSENSI</div>
            <div class="period-info"><?= $period ?></div>
        </div>
        
        <?php
        // Calculate statistics
        $statusCounts = [];
        $shiftCounts = [];
        $deptCounts = [];
        $totalJamKerja = 0;
        $totalJamLembur = 0;
        $totalTerlambatCount = 0;
        $totalTerlambatMinutes = 0;
        
        foreach ($absensiData as $absensi) {
            // Count by status
            $status = $absensi['status'] ?? 'Unknown';
            if (!isset($statusCounts[$status])) {
                $statusCounts[$status] = 0;
            }
            $statusCounts[$status]++;
            
            // Count by shift
            $shift = $absensi['shift'] ?? 'Unknown';
            if (!isset($shiftCounts[$shift])) {
                $shiftCounts[$shift] = 0;
            }
            $shiftCounts[$shift]++;
            
            // Count by department
            $dept = $absensi['departemen'] ?? 'Unknown';
            if (!isset($deptCounts[$dept])) {
                $deptCounts[$dept] = 0;
            }
            $deptCounts[$dept]++;
            
            // Sum totals
            $totalJamKerja += $absensi['jam_kerja'] ?? 0;
            $totalJamLembur += $absensi['jam_lembur'] ?? 0;
            
            if (($absensi['terlambat'] ?? 0) > 0) {
                $totalTerlambatCount++;
                $totalTerlambatMinutes += $absensi['terlambat'];
            }
        }
        
        $avgJamKerja = count($absensiData) > 0 ? $totalJamKerja / count($absensiData) : 0;
        $avgTerlambat = $totalTerlambatCount > 0 ? $totalTerlambatMinutes / $totalTerlambatCount : 0;
        ?>
        
        <!-- Overall Statistics -->
        <div class="summary-section">
            <strong>Statistik Keseluruhan:</strong>
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-value"><?= number_format($avgJamKerja, 1) ?>h</div>
                    <div class="summary-label">Avg Jam Kerja</div>
                </div>
                <div class="summary-item">
                    <div class="summary-value"><?= number_format($avgTerlambat, 0) ?>m</div>
                    <div class="summary-label">Avg Keterlambatan</div>
                </div>
                <div class="summary-item">
                    <div class="summary-value">
                        <?= count($absensiData) > 0 ? number_format(($totalTerlambatCount / count($absensiData)) * 100, 1) : 0 ?>%
                    </div>
                    <div class="summary-label">% Terlambat</div>
                </div>
                <div class="summary-item">
                    <div class="summary-value"><?= count($absensiData) ?></div>
                    <div class="summary-label">Total Record</div>
                </div>
                <div class="summary-item">
                    <div class="summary-value">
                        <?= $totalHadir > 0 ? number_format(($totalLembur / $totalHadir), 1) : 0 ?>h
                    </div>
                    <div class="summary-label">Avg Lembur/Hadir</div>
                </div>
            </div>
        </div>
        
        <!-- Status Distribution -->
        <div style="margin: 10px 0;">
            <h4 style="font-size: 8px; color: #2e59d9; border-bottom: 1px solid #ddd; padding-bottom: 3px; margin-bottom: 5px;">
                Distribusi Status Absensi
            </h4>
            
            <table class="stats-table">
                <tr>
                    <th width="80">Status</th>
                    <th width="40">Jumlah</th>
                    <th width="60">%</th>
                    <th width="80">Grafik</th>
                </tr>
                <?php foreach ($statusCounts as $status => $count): 
                    $percentage = count($absensiData) > 0 ? ($count / count($absensiData)) * 100 : 0;
                ?>
                <tr>
                    <td>
                        <span class="status-badge status-<?= strtolower($status) ?>">
                            <?= $status ?>
                        </span>
                    </td>
                    <td class="text-center"><?= $count ?></td>
                    <td class="text-center"><?= number_format($percentage, 1) ?>%</td>
                    <td>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?= $percentage ?>%"></div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        
        <!-- Shift Distribution -->
        <?php if (!empty($shiftCounts)): ?>
        <div style="margin: 10px 0;">
            <h4 style="font-size: 8px; color: #2e59d9; border-bottom: 1px solid #ddd; padding-bottom: 3px; margin-bottom: 5px;">
                Distribusi Shift
            </h4>
            
            <table class="stats-table">
                <tr>
                    <th width="80">Shift</th>
                    <th width="40">Jumlah</th>
                    <th width="60">%</th>
                    <th width="80">Grafik</th>
                </tr>
                <?php foreach ($shiftCounts as $shift => $count): 
                    $percentage = count($absensiData) > 0 ? ($count / count($absensiData)) * 100 : 0;
                ?>
                <tr>
                    <td>
                        <?php if ($shift !== 'Unknown'): ?>
                        <span class="shift-badge shift-<?= $shift ?>">
                            <?= ucfirst(substr($shift, 0, 1)) ?>
                        </span>
                        <?php else: ?>
                            <?= $shift ?>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?= $count ?></td>
                    <td class="text-center"><?= number_format($percentage, 1) ?>%</td>
                    <td>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?= $percentage ?>%"></div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php endif; ?>
        
        <!-- Department Distribution -->
        <?php if (!empty($deptCounts)): ?>
        <div style="margin: 10px 0;">
            <h4 style="font-size: 8px; color: #2e59d9; border-bottom: 1px solid #ddd; padding-bottom: 3px; margin-bottom: 5px;">
                Distribusi per Dept
            </h4>
            
            <table class="stats-table">
                <tr>
                    <th>Dept</th>
                    <th width="40">Jumlah</th>
                    <th width="60">%</th>
                    <th width="80">Grafik</th>
                </tr>
                <?php foreach ($deptCounts as $dept => $count): 
                    $percentage = count($absensiData) > 0 ? ($count / count($absensiData)) * 100 : 0;
                ?>
                <tr>
                    <td><div class="truncate"><?= $dept ?></div></td>
                    <td class="text-center"><?= $count ?></td>
                    <td class="text-center"><?= number_format($percentage, 1) ?>%</td>
                    <td>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?= $percentage ?>%"></div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php endif; ?>
        
    <?php endif; ?>
    
    <!-- Footer -->
    <div class="footer">
        <div class="footer-grid">
            <div>
                <strong>Informasi Sistem:</strong><br>
                Laporan dihasilkan oleh: Sistem Absensi CDW Engineering<br>
                &copy; <?= date('Y') ?> CDW Engineering
            </div>
            <div style="text-align: right;">
                <strong>Dokumen:</strong><br>
                Laporan Absensi Karyawan<br>
                Periode: <?= $period ?><br>
                Halaman: <span id="pageNum"></span>/<span id="pageCount"></span>
            </div>
        </div>
    </div>
</body>
</html>