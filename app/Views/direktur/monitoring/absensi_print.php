<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Cetak Laporan Absensi' ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0 0 5px; font-size: 18px; }
        .header p { margin: 0; font-size: 12px; color: #555; }
        .info-section { margin-bottom: 20px; }
        .info-section table { width: 100%; }
        .info-section td { padding: 3px 0; }
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th, .data-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .data-table th { background-color: #f2f2f2; font-weight: bold; }
        .summary-box { display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 20px; }
        .summary-item { border: 1px solid #ddd; padding: 10px; border-radius: 4px; flex: 1; min-width: 100px; text-align: center; }
        .summary-item strong { display: block; font-size: 16px; margin-bottom: 5px; }
        .footer { text-align: right; margin-top: 50px; }
        .footer .signature { margin-top: 70px; }
        
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
            @page { margin: 1cm; size: landscape; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 8px 15px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Cetak Sekarang</button>
        <button onclick="window.close()" style="padding: 8px 15px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">Tutup</button>
    </div>

    <div class="header">
        <h2>PT CDW Engineering</h2>
        <p>LAPORAN ABSENSI KARYAWAN</p>
    </div>

    <div class="info-section">
        <table>
            <tr>
                <td width="120"><strong>Periode</strong></td>
                <td width="10">:</td>
                <td><?= date('d/m/Y', strtotime($startDate)) ?> s/d <?= date('d/m/Y', strtotime($endDate)) ?></td>
                
                <td width="120"><strong>Dicetak Pada</strong></td>
                <td width="10">:</td>
                <td><?= date('d/m/Y H:i') ?></td>
            </tr>
            <tr>
                <td><strong>Status Filter</strong></td>
                <td>:</td>
                <td><?= empty($statusFilter) ? 'Semua Status' : htmlspecialchars($statusFilter) ?></td>
                
                <td><strong>Karyawan</strong></td>
                <td>:</td>
                <td><?= empty($selectedKaryawan) ? 'Semua Karyawan' : htmlspecialchars($selectedKaryawan['nama_lengkap']) . ' (' . htmlspecialchars($selectedKaryawan['nik']) . ')' ?></td>
            </tr>
        </table>
    </div>

    <div class="summary-box">
        <div class="summary-item">
            <strong><?= number_format($totalAbsensi ?? 0) ?></strong>
            Total Hari
        </div>
        <div class="summary-item">
            <strong><?= number_format($totalHadir ?? 0) ?></strong>
            Hadir
        </div>
        <div class="summary-item">
            <strong><?= number_format($totalTerlambat ?? 0) ?></strong>
            Terlambat
        </div>
        <div class="summary-item">
            <strong><?= number_format($totalIzin ?? 0) ?></strong>
            Izin
        </div>
        <div class="summary-item">
            <strong><?= number_format($totalSakit ?? 0) ?></strong>
            Sakit
        </div>
        <div class="summary-item">
            <strong><?= number_format($totalAlpha ?? 0) ?></strong>
            Alpha
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="30">No</th>
                <th>Tanggal</th>
                <th>Karyawan</th>
                <th>Shift</th>
                <th>Jam Masuk</th>
                <th>Jam Keluar</th>
                <th>Jam Kerja</th>
                <th>Terlambat</th>
                <th>Status</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($absensiData)): ?>
                <tr>
                    <td colspan="10" style="text-align: center;">Tidak ada data absensi untuk periode ini.</td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($absensiData as $item): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= date('d/m/Y', strtotime($item['tanggal'])) ?></td>
                        <td>
                            <?= htmlspecialchars($item['nama_panggilan'] ?? $item['nama_lengkap']) ?><br>
                            <span style="font-size: 10px; color: #666;"><?= htmlspecialchars($item['jabatan']) ?></span>
                        </td>
                        <td><?= ucfirst(htmlspecialchars($item['shift'] ?? 'siang')) ?></td>
                        <td><?= !empty($item['waktu_masuk']) ? date('H:i', strtotime($item['waktu_masuk'])) : '-' ?></td>
                        <td><?= !empty($item['waktu_pulang']) ? date('H:i', strtotime($item['waktu_pulang'])) : '-' ?></td>
                        <td><?= number_format($item['jam_kerja'] ?? 0, 1) ?> jam</td>
                        <td><?= ($item['terlambat'] ?? 0) > 0 ? $item['terlambat'] . ' mnt' : '-' ?></td>
                        <td><?= htmlspecialchars($item['status']) ?></td>
                        <td><?= htmlspecialchars($item['keterangan'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Bandung, <?= date('d F Y') ?></p>
        <p>Mengetahui,</p>
        <div class="signature">
            <p><strong><?= htmlspecialchars($user['nama'] ?? 'Direktur') ?></strong><br>Direktur</p>
        </div>
    </div>
</body>
</html>
