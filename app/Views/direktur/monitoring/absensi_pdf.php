<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'Laporan Absensi Karyawan') ?></title>
    <style>
        @page {
            margin: 15mm 15mm 15mm 15mm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            color: #1e293b;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2px solid #1e3c72;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .company-name {
            font-size: 16pt;
            font-weight: bold;
            color: #1e3c72;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .doc-title {
            font-size: 12pt;
            font-weight: bold;
            color: #334155;
            margin-top: 3px;
        }
        .period-info {
            font-size: 9pt;
            color: #64748b;
            margin-top: 3px;
        }
        .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .stats-table td {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 8px;
            text-align: center;
            border-radius: 4px;
        }
        .stats-label {
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
            display: block;
        }
        .stats-value {
            font-size: 12pt;
            font-weight: bold;
            color: #0f172a;
            margin-top: 2px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .data-table th {
            background: #1e3c72;
            color: #ffffff;
            font-size: 8.5pt;
            font-weight: bold;
            padding: 7px 6px;
            text-align: left;
            border: 1px solid #1e3c72;
            text-transform: uppercase;
        }
        .data-table td {
            font-size: 8.5pt;
            padding: 6px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 3px 7px;
            border-radius: 10px;
            font-size: 7.5pt;
            font-weight: bold;
        }
        .badge-hadir { background-color: #dcfce7; color: #15803d; }
        .badge-terlambat { background-color: #fef3c7; color: #b45309; }
        .badge-izin { background-color: #e0f2fe; color: #0369a1; }
        .badge-sakit { background-color: #f3e8ff; color: #7e22ce; }
        .badge-alpha { background-color: #ffe4e6; color: #be123c; }

        .footer-sig {
            margin-top: 30px;
            width: 100%;
        }
        .sig-box {
            float: right;
            width: 220px;
            text-align: center;
        }
        .sig-space {
            height: 50px;
        }
    </style>
</head>
<body>

    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td>
                    <div class="company-name">CDW ENGINEERING</div>
                    <div class="doc-title">Laporan Monitoring Absensi Karyawan</div>
                    <div class="period-info">
                        Periode: <strong><?= date('d/m/Y', strtotime($startDate)) ?></strong> s/d <strong><?= date('d/m/Y', strtotime($endDate)) ?></strong>
                        <?php if(!empty($selectedKaryawan)): ?>
                            | Karyawan: <strong><?= esc($selectedKaryawan['nama_lengkap']) ?></strong>
                        <?php endif; ?>
                        <?php if(!empty($statusFilter)): ?>
                            | Status: <strong><?= esc($statusFilter) ?></strong>
                        <?php endif; ?>
                    </div>
                </td>
                <td style="text-align: right; vertical-align: top; font-size: 8.5pt; color: #64748b;">
                    Dicetak pada:<br>
                    <strong><?= date('d M Y, H:i') ?> WIB</strong>
                </td>
            </tr>
        </table>
    </div>

    <!-- Ringkasan Statistik -->
    <table class="stats-table">
        <tr>
            <td style="width: 20%;">
                <span class="stats-label">Total Record</span>
                <div class="stats-value"><?= number_format($stats['total_absensi'] ?? 0) ?></div>
            </td>
            <td style="width: 20%;">
                <span class="stats-label">Hadir</span>
                <div class="stats-value" style="color: #16a34a;"><?= number_format($stats['total_hadir'] ?? 0) ?></div>
            </td>
            <td style="width: 20%;">
                <span class="stats-label">Terlambat</span>
                <div class="stats-value" style="color: #d97706;"><?= number_format($stats['total_terlambat'] ?? 0) ?></div>
            </td>
            <td style="width: 20%;">
                <span class="stats-label">Izin / Sakit</span>
                <div class="stats-value" style="color: #0284c7;"><?= number_format(($stats['total_izin'] ?? 0) + ($stats['total_sakit'] ?? 0)) ?></div>
            </td>
            <td style="width: 20%;">
                <span class="stats-label">Alpha / Absen</span>
                <div class="stats-value" style="color: #dc2626;"><?= number_format($stats['total_alpha'] ?? 0) ?></div>
            </td>
        </tr>
    </table>

    <!-- Tabel Data -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px; text-align: center;">No</th>
                <th style="width: 65px;">Tanggal</th>
                <th style="width: 75px;">NIK</th>
                <th>Nama Karyawan</th>
                <th style="width: 80px;">Jabatan</th>
                <th style="width: 40px; text-align: center;">Shift</th>
                <th style="width: 50px; text-align: center;">Masuk</th>
                <th style="width: 50px; text-align: center;">Pulang</th>
                <th style="width: 50px; text-align: center;">Jam Kerja</th>
                <th style="width: 55px; text-align: center;">Terlambat</th>
                <th style="width: 65px; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($absensiData)): ?>
                <tr>
                    <td colspan="11" style="text-align: center; padding: 15px; color: #64748b;">
                        Tidak ada data absensi untuk periode ini.
                    </td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($absensiData as $item): ?>
                    <?php
                        $st = $item['status'] ?? 'Hadir';
                        $badgeClass = match($st) {
                            'Hadir'     => 'badge-hadir',
                            'Terlambat' => 'badge-terlambat',
                            'Izin'      => 'badge-izin',
                            'Sakit'     => 'badge-sakit',
                            'Alpha'     => 'badge-alpha',
                            default     => 'badge-hadir'
                        };
                        $wMasuk  = !empty($item['waktu_masuk']) ? date('H:i', strtotime($item['waktu_masuk'])) : '-';
                        $wPulang = !empty($item['waktu_pulang']) ? date('H:i', strtotime($item['waktu_pulang'])) : '-';
                    ?>
                    <tr>
                        <td style="text-align: center;"><?= $no++ ?></td>
                        <td><?= date('d/m/Y', strtotime($item['tanggal'])) ?></td>
                        <td><?= esc($item['nik'] ?: '-') ?></td>
                        <td><strong><?= esc($item['nama_lengkap']) ?></strong></td>
                        <td><?= esc($item['jabatan'] ?: '-') ?></td>
                        <td style="text-align: center; text-transform: capitalize;"><?= esc($item['shift'] ?? 'siang') ?></td>
                        <td style="text-align: center;"><?= $wMasuk ?></td>
                        <td style="text-align: center;"><?= $wPulang ?></td>
                        <td style="text-align: center;"><?= !empty($item['jam_kerja']) ? number_format($item['jam_kerja'], 1).' jam' : '-' ?></td>
                        <td style="text-align: center;">
                            <?= !empty($item['terlambat']) && $item['terlambat'] > 0 ? esc($item['terlambat']).' mnt' : '-' ?>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge <?= $badgeClass ?>"><?= esc($st) ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer-sig">
        <div class="sig-box">
            <p>Mengetahui,<br><strong>Direktur Utama</strong></p>
            <div class="sig-space"></div>
            <p style="margin-bottom: 0;"><u>_______________________</u></p>
        </div>
    </div>

</body>
</html>
