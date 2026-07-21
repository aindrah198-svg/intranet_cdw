<?php
$title = 'Cetak Laporan Absensi';
$active = 'absensi';

// Helper functions
function format_date($date) {
    if (empty($date)) return '';
    
    try {
        // Coba parse berbagai format tanggal
        if ($date instanceof DateTime) {
            return $date->format('d/m/Y');
        }
        
        if (is_string($date)) {
            // Coba format yang umum
            $timestamp = strtotime($date);
            if ($timestamp !== false) {
                return date('d/m/Y', $timestamp);
            }
        }
        
        // Fallback
        return (string) $date;
    } catch (Exception $e) {
        return '';
    }
}

function format_time($time) {
    if (empty($time) || $time == '00:00:00') return '-';
    
    try {
        if ($time instanceof DateTime) {
            return $time->format('H:i');
        }
        
        if (is_string($time)) {
            // Hapus detik jika ada
            $time = substr($time, 0, 5);
            return $time;
        }
        
        return (string) $time;
    } catch (Exception $e) {
        return '-';
    }
}

// Format tanggal untuk display
$startDateDisplay = format_date($startDate);
$endDateDisplay = format_date($endDate);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi - CDW Engineering</title>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4e73df;
            padding-bottom: 20px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2e59d9;
            margin-bottom: 5px;
        }
        
        .company-address {
            font-size: 11px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .report-title {
            font-size: 18px;
            font-weight: bold;
            margin: 10px 0;
            color: #333;
        }
        
        .report-period {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }
        
        .print-info {
            text-align: right;
            margin-bottom: 15px;
            color: #666;
            font-size: 11px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        
        .info-box {
            background: #f8f9fa;
            border: 1px solid #e3e6f0;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        
        .info-label {
            font-weight: bold;
            width: 150px;
            color: #495057;
        }
        
        .info-value {
            flex: 1;
            color: #666;
        }
        
        .table-container {
            margin-top: 20px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .data-table th {
            background: #4e73df;
            color: white;
            font-weight: bold;
            padding: 10px;
            text-align: left;
            border: 1px solid #dddddd;
        }
        
        .data-table td {
            padding: 8px 10px;
            border: 1px solid #dddddd;
        }
        
        .data-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .status-hadir { color: #28a745; font-weight: bold; }
        .status-izin { color: #17a2b8; font-weight: bold; }
        .status-sakit { color: #ffc107; font-weight: bold; }
        .status-cuti { color: #6f42c1; font-weight: bold; }
        .status-alpha { color: #dc3545; font-weight: bold; }
        
        .summary-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        
        .summary-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
            border-bottom: 1px solid #e3e6f0;
            padding-bottom: 5px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .summary-card {
            background: #f8f9fa;
            border: 1px solid #e3e6f0;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        
        .summary-value {
            font-size: 24px;
            font-weight: bold;
            color: #4e73df;
            margin: 10px 0;
        }
        
        .summary-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e3e6f0;
            text-align: center;
            color: #666;
            font-size: 11px;
        }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .nowrap { white-space: nowrap; }
        
        .shift-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .shift-pagi { background: #e3f2fd; color: #1976d2; }
        .shift-siang { background: #e8f5e9; color: #388e3c; }
        .shift-sore { background: #fff3e0; color: #f57c00; }
        .shift-malam { background: #f3e5f5; color: #7b1fa2; }
        
        .page-break {
            page-break-after: always;
        }
        
        .print-controls {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }
        
        @media print {
            .print-controls {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Print Controls -->
    <div class="print-controls no-print">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <a href="<?= base_url('admin/absensi') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
            <div>
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="fas fa-print"></i> Cetak
                </button>
                <a href="<?= base_url('admin/absensi/export/pdf?' . http_build_query($queryParams)) ?>" 
                   class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
            </div>
        </div>
    </div>

    <div class="print-info">
        Dicetak: <?= date('d/m/Y H:i:s') ?>
    </div>

    <div class="header">
        <div class="company-name">PT CDW Engineering</div>
        <div class="company-address">
            Jl. Contoh No. 123, Jakarta Selatan, DKI Jakarta 12560<br>
            Telp: (021) 12345678 | Email: info@cdw-engineering.com
        </div>
        <div class="report-title">LAPORAN ABSENSI KARYAWAN</div>
        <div class="report-period">
            Periode: <?= $startDateDisplay ?> - <?= $endDateDisplay ?>
        </div>
    </div>

    <div class="info-box">
        <div class="info-row">
            <div class="info-label">Tanggal Cetak</div>
            <div class="info-value"><?= date('d/m/Y H:i:s') ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Total Data</div>
            <div class="info-value"><?= number_format(count($absensiData), 0, ',', '.') ?> records</div>
        </div>
        <div class="info-row">
            <div class="info-label">Total Karyawan</div>
            <div class="info-value"><?= number_format($totalKaryawan, 0, ',', '.') ?> orang</div>
        </div>
        <div class="info-row">
            <div class="info-label">Filter Status</div>
            <div class="info-value"><?= $statusFilter ?: 'Semua Status' ?></div>
        </div>
        <?php if ($karyawanIdFilter && $selectedKaryawan): ?>
        <div class="info-row">
            <div class="info-label">Karyawan Terpilih</div>
            <div class="info-value"><?= $selectedKaryawan['nama_lengkap'] ?> (NIK: <?= $selectedKaryawan['nik'] ?>)</div>
        </div>
        <?php endif; ?>
    </div>

    <div class="summary-section">
        <div class="summary-title">Ringkasan Statistik</div>
        <div class="summary-grid">
            <div class="summary-card">
                <div class="summary-label">Total Absensi</div>
                <div class="summary-value"><?= number_format($totalAbsensi, 0, ',', '.') ?></div>
                <small>Data Record</small>
            </div>
            <div class="summary-card">
                <div class="summary-label">Hadir</div>
                <div class="summary-value"><?= number_format($totalHadir, 0, ',', '.') ?></div>
                <small><?= $totalAbsensi > 0 ? round(($totalHadir / $totalAbsensi) * 100, 1) : 0 ?>% dari total</small>
            </div>
            <div class="summary-card">
                <div class="summary-label">Terlambat</div>
                <div class="summary-value"><?= number_format($totalTerlambat, 0, ',', '.') ?></div>
                <small><?= $totalHadir > 0 ? round(($totalTerlambat / $totalHadir) * 100, 1) : 0 ?>% dari hadir</small>
            </div>
            <div class="summary-card">
                <div class="summary-label">Jam Lembur</div>
                <div class="summary-value"><?= number_format($totalLembur, 1, ',', '.') ?></div>
                <small>Total jam lembur</small>
            </div>
        </div>
    </div>

    <div class="table-container">
        <div style="margin-bottom: 15px; font-weight: bold; color: #495057;">
            Data Detail Absensi (<?= count($absensiData) ?> records)
        </div>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th width="30">No</th>
                    <th width="100">Tanggal</th>
                    <th width="120">NIK</th>
                    <th>Nama Karyawan</th>
                    <th width="90">Shift</th>
                    <th width="90">Waktu Masuk</th>
                    <th width="90">Waktu Pulang</th>
                    <th width="80">Jam Kerja</th>
                    <th width="70">Lembur</th>
                    <th width="80">Terlambat</th>
                    <th width="80">Status</th>
                    <th width="120">Lokasi</th>
                </tr>
            </thead>
            <tbody>
                <?php $counter = 1; ?>
                <?php foreach ($absensiData as $absensi): ?>
                    <?php
                    // Format shift badge
                    $shiftClass = 'shift-' . ($absensi['shift'] ?? 'siang');
                    $shiftName = ucfirst($absensi['shift'] ?? 'siang');
                    
                    // Format status dengan color
                    $statusClass = 'status-' . strtolower($absensi['status'] ?? 'hadir');
                    
                    // Format time dengan helper function
                    $waktuMasuk = format_time($absensi['waktu_masuk'] ?? '');
                    $waktuPulang = format_time($absensi['waktu_pulang'] ?? '');
                    
                    // Format hours
                    $jamKerja = !empty($absensi['jam_kerja']) ? 
                        number_format((float)$absensi['jam_kerja'], 1, ',', '') . ' jam' : '-';
                    $jamLembur = !empty($absensi['jam_lembur']) ? 
                        number_format((float)$absensi['jam_lembur'], 1, ',', '') . ' jam' : '-';
                    
                    // Format lateness
                    $terlambat = !empty($absensi['terlambat']) && (int)$absensi['terlambat'] > 0 ? 
                        '<span style="color: #dc3545; font-weight: bold;">' . (int)$absensi['terlambat'] . ' mnt</span>' : 
                        '0 mnt';
                    
                    // Format tanggal
                    $tanggalDisplay = format_date($absensi['tanggal'] ?? '');
                    
                    // Location (simplified)
                    $lokasi = $absensi['lokasi_masuk'] ?? '-';
                    if (strlen($lokasi) > 30) {
                        $lokasi = substr($lokasi, 0, 30) . '...';
                    }
                    ?>
                    <tr>
                        <td class="text-center"><?= $counter ?></td>
                        <td class="nowrap"><?= $tanggalDisplay ?></td>
                        <td><?= $absensi['nik'] ?? '-' ?></td>
                        <td><?= $absensi['nama_lengkap'] ?? '-' ?></td>
                        <td class="text-center">
                            <span class="shift-badge <?= $shiftClass ?>"><?= $shiftName ?></span>
                        </td>
                        <td class="text-center"><?= $waktuMasuk ?></td>
                        <td class="text-center"><?= $waktuPulang ?></td>
                        <td class="text-center"><?= $jamKerja ?></td>
                        <td class="text-center"><?= $jamLembur ?></td>
                        <td class="text-center"><?= $terlambat ?></td>
                        <td class="text-center">
                            <span class="<?= $statusClass ?>"><?= $absensi['status'] ?? '-' ?></span>
                        </td>
                        <td><?= $lokasi ?></td>
                    </tr>
                    <?php 
                    $counter++;
                    // Add page break after every 25 rows
                    if ($counter % 25 == 0 && $counter < count($absensiData)): ?>
                        </tbody>
                        </table>
                        <div class="page-break"></div>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th width="30">No</th>
                                    <th width="100">Tanggal</th>
                                    <th width="120">NIK</th>
                                    <th>Nama Karyawan</th>
                                    <th width="90">Shift</th>
                                    <th width="90">Waktu Masuk</th>
                                    <th width="90">Waktu Pulang</th>
                                    <th width="80">Jam Kerja</th>
                                    <th width="70">Lembur</th>
                                    <th width="80">Terlambat</th>
                                    <th width="80">Status</th>
                                    <th width="120">Lokasi</th>
                                </tr>
                            </thead>
                            <tbody>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (!empty($summaryByStatus)): ?>
    <div class="summary-section">
        <div class="summary-title">Distribusi Berdasarkan Status</div>
        <div class="summary-grid">
            <?php foreach ($summaryByStatus as $status => $count): ?>
                <?php 
                $percentage = $totalAbsensi > 0 ? round(($count / $totalAbsensi) * 100, 1) : 0;
                $statusClass = 'status-' . strtolower($status);
                ?>
                <div class="summary-card">
                    <div class="summary-label"><?= $status ?></div>
                    <div class="summary-value <?= $statusClass ?>"><?= number_format($count, 0, ',', '.') ?></div>
                    <small><?= $percentage ?>% dari total</small>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($summaryByShift)): ?>
    <div class="summary-section">
        <div class="summary-title">Distribusi Berdasarkan Shift</div>
        <div class="summary-grid">
            <?php foreach ($summaryByShift as $shift => $count): ?>
                <?php 
                $percentage = $totalAbsensi > 0 ? round(($count / $totalAbsensi) * 100, 1) : 0;
                $shiftClass = 'shift-' . strtolower($shift);
                ?>
                <div class="summary-card">
                    <div class="summary-label">Shift <?= ucfirst($shift) ?></div>
                    <div class="summary-value"><?= number_format($count, 0, ',', '.') ?></div>
                    <small><?= $percentage ?>% dari total</small>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="footer">
        <div style="margin-bottom: 10px;">
            <strong>Catatan:</strong><br>
            • Jam kerja sudah termasuk potongan istirahat sesuai shift<br>
            • Lembur dihitung setelah jam kerja shift berakhir<br>
            • Terlambat dihitung setelah toleransi 30 menit dari jam mulai shift
        </div>
        <div style="border-top: 1px solid #eee; padding-top: 15px;">
            <table width="100%">
                <tr>
                    <td width="50%" style="text-align: left; padding-left: 20px;">
                        <div style="margin-top: 40px;">
                            <strong>Disetujui Oleh,</strong><br><br><br><br>
                            ___________________________<br>
                            HR Manager
                        </div>
                    </td>
                    <td width="50%" style="text-align: right; padding-right: 20px;">
                        <div style="margin-top: 40px;">
                            <strong>Dibuat Oleh,</strong><br><br><br><br>
                            ___________________________<br>
                            Admin HR
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div style="margin-top: 30px; font-size: 10px; color: #999;">
            Dokumen ini dicetak secara otomatis dari Sistem Absensi CDW Engineering<br>
            Dokumen ini sah tanpa tanda tangan basah
        </div>
    </div>

    <script>
        // Auto print jika diperlukan
        <?php if ($this->request->getGet('auto') === 'true'): ?>
            window.onload = function() {
                setTimeout(function() {
                    window.print();
                }, 1000);
            };
        <?php endif; ?>
        
        // Keyboard shortcut for print
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
    </script>
</body>
</html>