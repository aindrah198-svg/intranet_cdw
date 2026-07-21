<?php
header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename="laporan_jam_kerja_' . date('Ymd_His') . '.xls"');
header('Cache-Control: max-age=0');
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Jam Kerja - CDW Engineering</title>
    <style>
        body {
            font-family: 'Calibri', Arial, sans-serif;
            color: #333;
            background-color: #fff;
            margin: 0;
            padding: 0;
        }
        
        /* Container utama tanpa border */
        .excel-container {
            padding: 20px;
        }
        
        .header-section {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
        }
        
        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #1e3c72;
            margin-bottom: 5px;
            text-align: center;
        }
        
        .company-tagline {
            font-size: 12px;
            color: #666;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .report-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin: 10px 0;
            text-align: center;
        }
        
        .report-info {
            font-size: 12px;
            color: #555;
            margin: 10px 0;
            text-align: center;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-top: 15px;
            table-layout: fixed;
        }
        
        /* Header Tabel - Warna Abu Muda */
        thead th {
            background-color: #f5f5f5 !important;
            color: #333 !important;
            font-weight: bold;
            padding: 8px 4px;
            border: 1px solid #ddd;
            text-align: center;
            vertical-align: middle;
            white-space: normal;
        }
        
        /* Header Utama (baris pertama) */
        .header-main {
            background-color: #e9ecef !important;
            font-size: 11px;
            padding: 10px 4px;
            border-bottom: 2px solid #ccc;
        }
        
        /* Sub-header (baris kedua) */
        .header-sub {
            background-color: #f8f9fa !important;
            font-size: 10px;
            color: #555 !important;
            padding: 8px 4px;
        }
        
        /* Data Cells */
        td {
            padding: 6px 4px;
            border: 1px solid #ddd;
            vertical-align: middle;
        }
        
        /* Warna baris bergantian */
        .row-odd {
            background-color: #fafafa;
        }
        
        .row-even {
            background-color: #ffffff;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-left {
            text-align: left;
        }
        
        /* Format NIK untuk Excel */
        .nik-cell {
            mso-number-format: "\@";
            text-align: center;
        }
        
        /* Baris Total */
        .total-row {
            background-color: #f8f9fa !important;
            font-weight: bold;
            border-top: 2px solid #666;
            border-bottom: 2px solid #666;
        }
        
        .total-row td {
            font-weight: bold;
            color: #333;
            text-align: center;
        }
        
        /* Baris Statistik */
        .stat-row {
            background-color: #f0f8ff !important;
            font-size: 10px;
            color: #555;
        }
        
        .stat-row td {
            text-align: center;
        }
        
        /* Summary Section */
        .summary-section {
            margin-top: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        
        .summary-title {
            font-weight: bold;
            color: #1e3c72;
            margin-bottom: 10px;
            font-size: 12px;
        }
        
        .summary-item {
            margin-bottom: 5px;
            line-height: 1.5;
        }
        
        /* Lebar kolom yang disesuaikan */
        .col-no { width: 40px; }
        .col-nik { width: 85px; }
        .col-nama { width: 160px; }
        .col-jabatan { width: 120px; }
        .col-departemen { width: 120px; }
        .col-total-hari { width: 65px; }
        .col-hadir { width: 65px; }
        .col-total-jam { width: 85px; }
        .col-rata { width: 85px; }
        .col-lembur { width: 75px; }
        .col-terlambat { width: 75px; }
        
        /* Spacing khusus untuk header */
        .header-spacing {
            padding: 8px 4px !important;
        }
        
        /* Section Header untuk grouping */
        .section-header {
            background-color: #e9ecef !important;
            color: #333 !important;
            font-weight: bold;
        }
        
        /* Filter Info */
        .filter-info {
            background-color: #f0f8ff;
            padding: 8px;
            margin: 10px 0 15px 0;
            border: 1px solid #cce5ff;
            border-radius: 4px;
            font-size: 11px;
            text-align: center;
        }
        
        .filter-title {
            font-weight: bold;
            color: #1e3c72;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="excel-container">
        <!-- Header Section -->
        <div class="header-section">
            <div class="company-name">CDW ENGINEERING</div>
            <div class="company-tagline">Human Resource Management System</div>
            <div class="report-title">LAPORAN REKAP JAM KERJA KARYAWAN</div>
            <div class="report-info">
                Periode: <?= date('d F Y', strtotime($filter['start_date'])); ?> s/d <?= date('d F Y', strtotime($filter['end_date'])); ?><br>
                Dicetak: <?= date('d F Y H:i:s'); ?>
            </div>
        </div>
        
        <?php if (!empty($filter['karyawan_id']) || !empty($filter['status']) || !empty($filter['departemen'])): ?>
        <div class="filter-info">
            <div class="filter-title">FILTER YANG DITERAPKAN:</div>
            <?php if (!empty($filter['karyawan_id'])): ?>
            <div>• Karyawan: Spesifik</div>
            <?php endif; ?>
            <?php if (!empty($filter['status'])): ?>
            <div>• Status: <?= $filter['status']; ?></div>
            <?php endif; ?>
            <?php if (!empty($filter['departemen'])): ?>
            <div>• Departemen: <?= $filter['departemen']; ?></div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <!-- Main Table -->
        <table cellspacing="0" cellpadding="0">
            <thead>
                <!-- Baris 1: Header Utama -->
                <tr>
                    <th class="col-no header-main" rowspan="2" style="text-align: center;">No</th>
                    <th class="col-nik header-main" rowspan="2" style="text-align: center;">NIK</th>
                    <th class="col-nama header-main" rowspan="2" style="text-align: center;">NAMA KARYAWAN</th>
                    <th class="col-jabatan header-main" rowspan="2" style="text-align: center;">JABATAN</th>
                    <th class="col-departemen header-main" rowspan="2" style="text-align: center;">DEPARTEMEN</th>
                    <th class="section-header header-main" colspan="2" style="text-align: center;">KEHADIRAN</th>
                    <th class="section-header header-main" colspan="3" style="text-align: center;">JAM KERJA</th>
                    <th class="col-terlambat header-main" rowspan="2" style="text-align: center;">TERLAMBAT<br>(Menit)</th>
                </tr>
                
                <!-- Baris 2: Sub-header -->
                <tr>
                    <!-- Sub-header untuk KEHADIRAN -->
                    <th class="col-total-hari header-sub" style="text-align: center;">TOTAL<br>HARI</th>
                    <th class="col-hadir header-sub" style="text-align: center;">HADIR</th>
                    
                    <!-- Sub-header untuk JAM KERJA -->
                    <th class="col-total-jam header-sub" style="text-align: center;">TOTAL JAM<br>KERJA</th>
                    <th class="col-rata header-sub" style="text-align: center;">RATA-RATA<br>PER HARI</th>
                    <th class="col-lembur header-sub" style="text-align: center;">LEMBUR<br>(Jam)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($summary) && !empty($summary)): ?>
                    <?php 
                    $no = 1;
                    $totalAllHari = 0;
                    $totalAllHadir = 0;
                    $totalAllJamKerja = 0;
                    $totalAllLembur = 0;
                    $totalAllTerlambat = 0;
                    $totalKaryawan = count($summary);
                    ?>
                    <?php foreach ($summary as $index => $s): ?>
                    <tr class="<?= $index % 2 == 0 ? 'row-even' : 'row-odd'; ?>">
                        <td class="text-center col-no"><?= $no++; ?></td>
                        <td class="nik-cell col-nik"><?= "'" . esc($s['nik'] ?? '-'); ?></td>
                        <td class="text-left col-nama"><?= esc($s['nama_lengkap'] ?? '-'); ?></td>
                        <td class="text-left col-jabatan"><?= esc($s['jabatan'] ?? '-'); ?></td>
                        <td class="text-left col-departemen"><?= esc($s['departemen'] ?? '-'); ?></td>
                        <td class="text-center col-total-hari"><?= $s['total_hari']; ?></td>
                        <td class="text-center col-hadir"><?= $s['hari_hadir']; ?></td>
                        <td class="text-center col-total-jam"><?= number_format($s['total_jam_kerja'], 2); ?></td>
                        <td class="text-center col-rata">
                            <?php if ($s['hari_hadir'] > 0): ?>
                                <?= number_format($s['total_jam_kerja'] / $s['hari_hadir'], 2); ?>
                            <?php else: ?>
                                0.00
                            <?php endif; ?>
                        </td>
                        <td class="text-center col-lembur"><?= number_format($s['total_lembur'], 2); ?></td>
                        <td class="text-center col-terlambat"><?= $s['total_terlambat']; ?></td>
                    </tr>
                    <?php 
                    $totalAllHari += $s['total_hari'];
                    $totalAllHadir += $s['hari_hadir'];
                    $totalAllJamKerja += $s['total_jam_kerja'];
                    $totalAllLembur += $s['total_lembur'];
                    $totalAllTerlambat += $s['total_terlambat'];
                    ?>
                    <?php endforeach; ?>
                    
                    <!-- Baris Total -->
                    <tr class="total-row">
                        <td colspan="5" class="text-center"><strong>TOTAL:</strong></td>
                        <td class="text-center"><strong><?= $totalAllHari; ?></strong></td>
                        <td class="text-center"><strong><?= $totalAllHadir; ?></strong></td>
                        <td class="text-center"><strong><?= number_format($totalAllJamKerja, 2); ?></strong></td>
                        <td class="text-center">
                            <strong>
                            <?php if ($totalAllHadir > 0): ?>
                                <?= number_format($totalAllJamKerja / $totalAllHadir, 2); ?>
                            <?php else: ?>
                                0.00
                            <?php endif; ?>
                            </strong>
                        </td>
                        <td class="text-center"><strong><?= number_format($totalAllLembur, 2); ?></strong></td>
                        <td class="text-center"><strong><?= $totalAllTerlambat; ?></strong></td>
                    </tr>
                    
                    <!-- Baris Statistik -->
                    <tr class="stat-row">
                        <td colspan="3" class="text-center"><strong>STATISTIK:</strong></td>
                        <td colspan="2" class="text-center">Total Karyawan: <?= $totalKaryawan; ?></td>
                        <td class="text-center">Rata Hari: <?= $totalKaryawan > 0 ? number_format($totalAllHari / $totalKaryawan, 1) : 0; ?></td>
                        <td class="text-center">Rata Hadir: <?= $totalKaryawan > 0 ? number_format($totalAllHadir / $totalKaryawan, 1) : 0; ?></td>
                        <td class="text-center">Rata Jam Kerja: <?= $totalKaryawan > 0 ? number_format($totalAllJamKerja / $totalKaryawan, 2) : 0; ?></td>
                        <td class="text-center">-</td>
                        <td class="text-center">Rata Lembur: <?= $totalKaryawan > 0 ? number_format($totalAllLembur / $totalKaryawan, 2) : 0; ?></td>
                        <td class="text-center">-</td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td colspan="11" class="text-center" style="padding: 20px; color: #666;">
                            <em>Tidak ada data jam kerja pada periode yang dipilih.</em>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <!-- Summary Section -->
        <div class="summary-section">
            <div class="summary-title">KETERANGAN LAPORAN</div>
            
            <div class="summary-item">
                <strong>1. Sumber Data:</strong> Sistem Absensi CDW Engineering
            </div>
            <div class="summary-item">
                <strong>2. Periode Laporan:</strong> <?= date('d F Y', strtotime($filter['start_date'])); ?> hingga <?= date('d F Y', strtotime($filter['end_date'])); ?>
            </div>
            <div class="summary-item">
                <strong>3. Total Karyawan:</strong> <?= count($summary ?? []); ?> orang
            </div>
            <div class="summary-item">
                <strong>4. Definisi Kolom:</strong>
            </div>
            <div style="margin-left: 15px; font-size: 10px; line-height: 1.4;">
                • <strong>Total Hari:</strong> Jumlah hari kerja dalam periode (termasuk semua status)<br>
                • <strong>Hadir:</strong> Hanya hari dengan status 'Hadir'<br>
                • <strong>Total Jam Kerja:</strong> Akumulasi jam kerja semua hari hadir (dalam jam)<br>
                • <strong>Rata per Hari:</strong> Total Jam Kerja ÷ Jumlah Hari Hadir<br>
                • <strong>Lembur:</strong> Akumulasi jam lembur dalam periode (dalam jam)<br>
                • <strong>Terlambat:</strong> Total menit keterlambatan dalam periode
            </div>
            
            <div class="summary-item" style="margin-top: 10px;">
                <strong>5. Informasi Sistem:</strong> Dokumen ini dihasilkan otomatis oleh HRM System CDW Engineering
            </div>
        </div>
    </div>
</body>
</html>