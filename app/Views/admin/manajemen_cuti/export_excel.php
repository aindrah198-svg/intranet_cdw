<?php
// C:\xampp\htdocs\cdwnet\app\Views\admin\manajemen_cuti\export_excel.php

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="laporan_cuti_' . date('Ymd_His') . '.xls"');
header('Cache-Control: max-age=0');
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Cuti Karyawan</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            font-family: Arial, sans-serif;
            font-size: 11px;
        }
        th {
            background-color: #1e3c72;
            color: white;
            font-weight: bold;
            padding: 8px;
            text-align: center;
            border: 1px solid #ddd;
        }
        td {
            padding: 6px 8px;
            border: 1px solid #ddd;
            vertical-align: middle;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #1e3c72;
        }
        .report-title {
            font-size: 16px;
            margin: 10px 0;
            font-weight: bold;
        }
        .report-info {
            margin-bottom: 20px;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .text-left {
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .status-approved {
            background-color: #d4edda;
            color: #155724;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 10px;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 10px;
        }
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 10px;
        }
        .status-canceled {
            background-color: #e2e3e5;
            color: #383d41;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 10px;
        }
        .summary {
            margin-top: 30px;
            border-top: 2px solid #1e3c72;
            padding-top: 15px;
        }
        .cuti-cell {
            mso-number-format: "\@"; /* Excel format as text */
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">CDW ENGINEERING</div>
        <div class="report-title">LAPORAN PENGADAAN CUTI KARYAWAN</div>
        <div class="report-info">
            Periode: <?= date('d/m/Y', strtotime($filter['start_date'] ?? date('Y-m-01'))); ?> s/d <?= date('d/m/Y', strtotime($filter['end_date'] ?? date('Y-m-d'))); ?>
            <br>
            Dicetak: <?= date('d/m/Y H:i:s'); ?>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="100">Nomor Cuti</th>
                <th width="80">NIK</th>
                <th width="150">Nama Karyawan</th>
                <th width="80">Jabatan</th>
                <th width="80">Departemen</th>
                <th width="70">Jenis Cuti</th>
                <th width="100">Periode Cuti</th>
                <th width="50">Lama (Hari)</th>
                <th width="80">Status</th>
                <th width="120">Tanggal Pengajuan</th>
                <th width="100">Disetujui Pada</th>
                <th width="100">Disetujui Oleh</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($cuti) && !empty($cuti)): ?>
                <?php 
                $no = 1;
                $totalPengajuan = 0;
                $totalDisetujui = 0;
                $totalDitolak = 0;
                $totalMenunggu = 0;
                $totalHariCuti = 0;
                ?>
                <?php foreach ($cuti as $item): ?>
                <?php 
                $totalPengajuan++;
                $statusClass = 'status-pending';
                $statusText = $item['status'];
                
                if (in_array($item['status'], ['Disetujui HRD', 'Disetujui Atasan'])) {
                    $statusClass = 'status-approved';
                    $statusText = 'Disetujui';
                    $totalDisetujui++;
                    $totalHariCuti += $item['lama_hari'];
                } elseif ($item['status'] === 'Ditolak') {
                    $statusClass = 'status-rejected';
                    $totalDitolak++;
                } elseif ($item['status'] === 'Menunggu') {
                    $totalMenunggu++;
                } elseif ($item['status'] === 'Dibatalkan') {
                    $statusClass = 'status-canceled';
                }
                ?>
                <tr>
                    <td class="text-center"><?= $no++; ?></td>
                    <td class="cuti-cell"><?= esc($item['nomor_cuti'] ?? '-'); ?></td>
                    <td class="cuti-cell"><?= "'" . esc($item['nik'] ?? '-'); ?></td>
                    <td><?= esc($item['nama_lengkap'] ?? '-'); ?></td>
                    <td><?= esc($item['jabatan'] ?? '-'); ?></td>
                    <td><?= esc($item['departemen'] ?? '-'); ?></td>
                    <td class="text-center"><?= esc($item['jenis_cuti'] ?? '-'); ?></td>
                    <td class="text-center">
                        <?php 
                        $periode = '-';
                        if (!empty($item['tanggal_mulai']) && !empty($item['tanggal_selesai'])) {
                            $periode = date('d/m/Y', strtotime($item['tanggal_mulai'])) . '<br>s/d<br>' . 
                                     date('d/m/Y', strtotime($item['tanggal_selesai']));
                        }
                        echo $periode;
                        ?>
                    </td>
                    <td class="text-center"><?= $item['lama_hari'] ?? '0'; ?></td>
                    <td class="text-center">
                        <span class="<?= $statusClass; ?>">
                            <?= $statusText; ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <?= !empty($item['tanggal_pengajuan']) ? 
                            date('d/m/Y H:i', strtotime($item['tanggal_pengajuan'])) : '-'; ?>
                    </td>
                    <td class="text-center">
                        <?= !empty($item['disetujui_at']) ? 
                            date('d/m/Y H:i', strtotime($item['disetujui_at'])) : '-'; ?>
                    </td>
                    <td><?= esc($item['disetujui_nama'] ?? '-'); ?></td>
                </tr>
                <?php endforeach; ?>
                
                <!-- Summary Row -->
                <tr style="background-color: #e8f4ff; font-weight: bold;">
                    <td colspan="8" class="text-right">TOTAL STATISTIK:</td>
                    <td class="text-center"><?= $totalHariCuti; ?> hari</td>
                    <td class="text-center">
                        <?= $totalPengajuan; ?> pengajuan<br>
                        (<?= $totalDisetujui; ?> disetujui, 
                         <?= $totalMenunggu; ?> menunggu,
                         <?= $totalDitolak; ?> ditolak)
                    </td>
                    <td colspan="3"></td>
                </tr>
                
                <!-- Statistics Row -->
                <?php if (isset($stats) && !empty($stats)): ?>
                <tr style="background-color: #f0f8ff;">
                    <td colspan="13">
                        <strong>STATISTIK LAPORAN:</strong><br>
                        Total Pengajuan: <?= $stats['total_pengajuan'] ?? $totalPengajuan; ?> | 
                        Disetujui: <?= $stats['total_disetujui'] ?? $totalDisetujui; ?> | 
                        Ditolak: <?= $stats['total_ditolak'] ?? $totalDitolak; ?> | 
                        Menunggu: <?= $stats['total_menunggu'] ?? $totalMenunggu; ?> |
                        Total Hari Cuti: <?= $stats['total_hari_cuti'] ?? $totalHariCuti; ?> hari
                    </td>
                </tr>
                <?php endif; ?>
            <?php else: ?>
                <tr>
                    <td colspan="13" class="text-center" style="padding: 20px;">
                        Tidak ada data cuti pada periode yang dipilih.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <!-- Footer -->
    <div class="summary">
        <div><strong>KETERANGAN:</strong></div>
        <div>1. Format NIK: Angka panjang diawali dengan tanda petik (') untuk mencegah konversi ke scientific notation</div>
        <div>2. Format Nomor Cuti: CTI-YYYYMMDD-XXXX (dipertahankan sebagai text)</div>
        <div>3. Status: <span class="status-approved">Disetujui</span> | 
                        <span class="status-pending">Menunggu</span> | 
                        <span class="status-rejected">Ditolak</span> | 
                        <span class="status-canceled">Dibatalkan</span></div>
        <div>4. Jenis Cuti: Tahunan, Hamil, Sakit, Khusus, Lainnya</div>
        <div>5. Periode: <?= date('d/m/Y', strtotime($filter['start_date'] ?? date('Y-m-01'))); ?> - 
                        <?= date('d/m/Y', strtotime($filter['end_date'] ?? date('Y-m-d'))); ?></div>
        <div>6. Disetujui Oleh: Nama HRD atau Atasan yang menyetujui cuti</div>
        <div>7. Dicetak pada: <?= date('d/m/Y H:i:s'); ?></div>
    </div>
</body>
</html>