<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Jam Kerja - CDW Engineering</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }
        
        @media print {
            body {
                width: 100%;
                margin: 0;
                padding: 0;
            }
            
            .no-print {
                display: none !important;
            }
            
            .print-only {
                display: block !important;
            }
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 10pt;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 10px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
        }
        
        .company-name {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 2px;
            letter-spacing: 1px;
        }
        
        .report-title {
            font-size: 14pt;
            font-weight: bold;
            margin: 8px 0;
        }
        
        .report-info {
            font-size: 9pt;
            margin: 5px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            page-break-inside: auto;
        }
        
        th {
            background-color: #f0f0f0 !important;
            color: #000 !important;
            font-weight: bold;
            padding: 6px 4px;
            border: 1px solid #000;
            text-align: center;
            vertical-align: middle;
            font-size: 9pt;
        }
        
        td {
            padding: 5px 4px;
            border: 1px solid #000;
            vertical-align: middle;
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
        
        .total-row td {
            font-weight: bold;
            background-color: #f5f5f5 !important;
            border-top: 2px solid #000;
        }
        
        .summary {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #000;
            font-size: 9pt;
            page-break-inside: avoid;
        }
        
        .summary-title {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 10pt;
        }
        
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #000;
            font-size: 8pt;
            text-align: center;
        }
        
        .filter-box {
            margin: 10px 0;
            padding: 8px;
            border: 1px dashed #666;
            font-size: 9pt;
            background-color: #f9f9f9;
        }
        
        .filter-title {
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .print-controls {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            margin: 0 5px;
        }
        
        .btn-print {
            background-color: #1e3c72;
            color: white;
        }
        
        .btn-back {
            background-color: #6c757d;
            color: white;
        }
        
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        
        .alternate-row {
            background-color: #f8f8f8;
        }
    </style>
</head>
<body>
    <!-- Print Controls (Visible only on screen) -->
    <div class="print-controls no-print">
        <h3>Preview Laporan Jam Kerja</h3>
        <p>Klik tombol cetak untuk mencetak laporan ini</p>
        <button class="btn btn-print" onclick="window.print()">🖨️ Cetak Laporan</button>
        <a href="<?= base_url('admin/jam-kerja'); ?>" class="btn btn-back">← Kembali</a>
    </div>
    
    <!-- Header Section -->
    <div class="header">
        <div class="company-name">CDW ENGINEERING</div>
        <div class="report-title">LAPORAN REKAP JAM KERJA KARYAWAN</div>
        <div class="report-info">
            Periode: <?= date('d F Y', strtotime($filter['start_date'])); ?> s/d <?= date('d F Y', strtotime($filter['end_date'])); ?><br>
            Dicetak: <?= date('d/m/Y H:i:s'); ?> | Halaman: <span class="page-number"></span>
        </div>
    </div>
    
    <!-- Filter Information (if any) -->
    <?php if (!empty($filter['karyawan_id']) || !empty($filter['status']) || !empty($filter['departemen'])): ?>
    <div class="filter-box">
        <div class="filter-title">FILTER YANG DITERAPKAN:</div>
        <?php if (!empty($filter['karyawan_id'])): ?>
        <div>✓ Karyawan: Spesifik</div>
        <?php endif; ?>
        <?php if (!empty($filter['status'])): ?>
        <div>✓ Status: <?= $filter['status']; ?></div>
        <?php endif; ?>
        <?php if (!empty($filter['departemen'])): ?>
        <div>✓ Departemen: <?= $filter['departemen']; ?></div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <!-- Main Table -->
    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="100">NIK</th>
                <th width="150">NAMA KARYAWAN</th>
                <th width="100">JABATAN</th>
                <th width="100">DEPARTEMEN</th>
                <th width="50">TOTAL HARI</th>
                <th width="50">HADIR</th>
                <th width="80">TOTAL JAM KERJA</th>
                <th width="70">RATA PER HARI</th>
                <th width="70">LEMBUR</th>
                <th width="70">TERLAMBAT</th>
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
                ?>
                <?php foreach ($summary as $index => $s): ?>
                <tr class="<?= $index % 2 == 0 ? '' : 'alternate-row'; ?>">
                    <td class="text-center"><?= $no++; ?></td>
                    <td class="text-center"><?= esc($s['nik'] ?? '-'); ?></td>
                    <td><?= esc($s['nama_lengkap'] ?? '-'); ?></td>
                    <td><?= esc($s['jabatan'] ?? '-'); ?></td>
                    <td><?= esc($s['departemen'] ?? '-'); ?></td>
                    <td class="text-center"><?= $s['total_hari']; ?></td>
                    <td class="text-center"><?= $s['hari_hadir']; ?></td>
                    <td class="text-center">
                        <?php 
                        $jam = floor($s['total_jam_kerja']);
                        $menit = round(($s['total_jam_kerja'] - $jam) * 60);
                        if ($jam > 0 && $menit > 0) {
                            echo $jam . ":" . str_pad($menit, 2, '0', STR_PAD_LEFT);
                        } elseif ($jam > 0) {
                            echo $jam . ":00";
                        } elseif ($menit > 0) {
                            echo "0:" . str_pad($menit, 2, '0', STR_PAD_LEFT);
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>
                    <td class="text-center">
                        <?php 
                        if ($s['hari_hadir'] > 0) {
                            $rata = $s['total_jam_kerja'] / $s['hari_hadir'];
                            $jam = floor($rata);
                            $menit = round(($rata - $jam) * 60);
                            if ($jam > 0 && $menit > 0) {
                                echo $jam . ":" . str_pad($menit, 2, '0', STR_PAD_LEFT);
                            } elseif ($jam > 0) {
                                echo $jam . ":00";
                            } elseif ($menit > 0) {
                                echo "0:" . str_pad($menit, 2, '0', STR_PAD_LEFT);
                            } else {
                                echo '0:00';
                            }
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>
                    <td class="text-center">
                        <?php 
                        if ($s['total_lembur'] > 0) {
                            $jam = floor($s['total_lembur']);
                            $menit = round(($s['total_lembur'] - $jam) * 60);
                            echo $jam . ":" . str_pad($menit, 2, '0', STR_PAD_LEFT);
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>
                    <td class="text-center"><?= $s['total_terlambat']; ?> mnt</td>
                </tr>
                <?php 
                $totalAllHari += $s['total_hari'];
                $totalAllHadir += $s['hari_hadir'];
                $totalAllJamKerja += $s['total_jam_kerja'];
                $totalAllLembur += $s['total_lembur'];
                $totalAllTerlambat += $s['total_terlambat'];
                ?>
                <?php endforeach; ?>
                
                <!-- Total Row -->
                <tr class="total-row">
                    <td colspan="5" class="text-right"><strong>TOTAL:</strong></td>
                    <td class="text-center"><strong><?= $totalAllHari; ?></strong></td>
                    <td class="text-center"><strong><?= $totalAllHadir; ?></strong></td>
                    <td class="text-center"><strong>
                        <?php 
                        $jam = floor($totalAllJamKerja);
                        $menit = round(($totalAllJamKerja - $jam) * 60);
                        echo $jam . ":" . str_pad($menit, 2, '0', STR_PAD_LEFT);
                        ?>
                    </strong></td>
                    <td class="text-center"><strong>
                        <?php 
                        if ($totalAllHadir > 0) {
                            $rata = $totalAllJamKerja / $totalAllHadir;
                            $jam = floor($rata);
                            $menit = round(($rata - $jam) * 60);
                            echo $jam . ":" . str_pad($menit, 2, '0', STR_PAD_LEFT);
                        } else {
                            echo '0:00';
                        }
                        ?>
                    </strong></td>
                    <td class="text-center"><strong>
                        <?php 
                        $jam = floor($totalAllLembur);
                        $menit = round(($totalAllLembur - $jam) * 60);
                        echo $jam . ":" . str_pad($menit, 2, '0', STR_PAD_LEFT);
                        ?>
                    </strong></td>
                    <td class="text-center"><strong><?= $totalAllTerlambat; ?> mnt</strong></td>
                </tr>
            <?php else: ?>
                <tr>
                    <td colspan="11" class="text-center" style="padding: 20px;">
                        <em>Tidak ada data jam kerja pada periode yang dipilih.</em>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <!-- Summary Section -->
    <div class="summary">
        <div class="summary-title">RINGKASAN LAPORAN</div>
        <?php if (isset($summary) && !empty($summary)): ?>
        <div>• Total Karyawan: <?= count($summary); ?> orang</div>
        <div>• Rata-rata Hari Kerja per Karyawan: <?= count($summary) > 0 ? number_format($totalAllHari / count($summary), 1) : 0; ?> hari</div>
        <div>• Rata-rata Kehadiran: <?= count($summary) > 0 ? number_format($totalAllHadir / count($summary), 1) : 0; ?> hari</div>
        <div>• Total Jam Kerja Seluruh Karyawan: <?= floor($totalAllJamKerja); ?> jam <?= round(($totalAllJamKerja - floor($totalAllJamKerja)) * 60); ?> menit</div>
        <div>• Total Lembur: <?= floor($totalAllLembur); ?> jam <?= round(($totalAllLembur - floor($totalAllLembur)) * 60); ?> menit</div>
        <div>• Persentase Kehadiran: <?= $totalAllHari > 0 ? round(($totalAllHadir / $totalAllHari) * 100, 1) : 0; ?>%</div>
        <?php else: ?>
        <div>• Tidak ada data untuk ditampilkan</div>
        <?php endif; ?>
    </div>
    
    <!-- Footer -->
    <div class="footer print-only">
        Laporan Jam Kerja - CDW Engineering<br>
        <?= date('d F Y H:i:s'); ?> | Generated by HRM System
    </div>
    
    <script>
        // Auto print on load
        window.onload = function() {
            // Add page numbers
            const pages = document.querySelectorAll('.page-number');
            pages.forEach((page, index) => {
                page.textContent = (index + 1);
            });
            
            // Auto print if no-print controls are not shown
            if (document.querySelector('.no-print')) {
                // User is viewing on screen, don't auto print
                return;
            }
            
            // If accessed directly, auto print after 1 second
            setTimeout(function() {
                window.print();
            }, 1000);
        }
        
        // Handle after print
        window.onafterprint = function() {
            // Optionally redirect back
            // window.location.href = "<?= base_url('admin/jam-kerja'); ?>";
        };
    </script>
</body>
</html>