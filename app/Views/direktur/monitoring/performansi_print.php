<?php
// Data dari controller
$performansiData = $performansiData ?? [];
$tahun = $tahun ?? date('Y');
$bulan = $bulan ?? '';
$gradeFilter = $gradeFilter ?? '';
$karyawanIdFilter = $karyawanIdFilter ?? '';
$selectedKaryawan = $selectedKaryawan ?? [];
$stats = $stats ?? [];
$monthNames = $monthNames ?? [];

// Helper functions
if (!function_exists('formatScore')) {
    function formatScore($score) {
        if (empty($score) && $score !== 0) return '-';
        return number_format((float)$score, 1);
    }
}

if (!function_exists('formatNumber')) {
    function formatNumber($num) {
        if (empty($num) && $num !== 0) return '-';
        return number_format((float)$num, 0);
    }
}

if (!function_exists('getGradeBadgeClass')) {
    function getGradeBadgeClass($grade) {
        $classes = [
            'A' => 'success',
            'B' => 'primary',
            'C' => 'warning',
            'D' => 'danger',
            'E' => 'dark'
        ];
        return $classes[$grade] ?? 'secondary';
    }
}

if (!function_exists('getProgressColor')) {
    function getProgressColor($score) {
        if ($score >= 90) return '#1cc88a';
        if ($score >= 75) return '#4e73df';
        if ($score >= 60) return '#f6c23e';
        return '#e74a3b';
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
        $tgl = date('d', $timestamp);
        $bln = (int)date('m', $timestamp);
        $thn = date('Y', $timestamp);
        return "$tgl {$bulan[$bln]} $thn";
    }
}

// Periode text
$periodeText = '';
if ($bulan) {
    $periodeText = $monthNames[$bulan] . ' ' . $tahun;
} else {
    $periodeText = 'Tahun ' . $tahun;
}

// Grade filter text
$gradeText = '';
if ($gradeFilter) {
    $gradeOptions = [
        'A' => 'Grade A (Sangat Baik - 90+)',
        'B' => 'Grade B (Baik - 75-89)',
        'C' => 'Grade C (Cukup - 60-74)',
        'D' => 'Grade D (Kurang - 50-59)',
        'E' => 'Grade E (Buruk - <50)'
    ];
    $gradeText = $gradeOptions[$gradeFilter] ?? $gradeFilter;
}

// Karyawan text
$karyawanText = '';
if ($karyawanIdFilter && !empty($selectedKaryawan)) {
    $karyawanText = $selectedKaryawan['nama_lengkap'] . ' (' . ($selectedKaryawan['jabatan'] ?? '-') . ')';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Performansi Karyawan - <?= $periodeText ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.5;
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
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #4e73df;
        }
        .header h1 {
            font-size: 24px;
            color: #4e73df;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 18px;
            color: #333;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 11px;
        }
        .company-info {
            margin-bottom: 10px;
        }
        .company-name {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
        }
        /* Filter Info */
        .filter-info {
            background: #f8f9fc;
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border-left: 4px solid #4e73df;
            font-size: 11px;
        }
        .filter-info table {
            width: 100%;
        }
        .filter-info td {
            padding: 3px 5px;
        }
        .filter-info td:first-child {
            width: 120px;
            font-weight: bold;
        }
        /* Statistics Cards */
        .stats-container {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }
        .stat-card {
            flex: 1;
            background: #f8f9fc;
            border-radius: 8px;
            padding: 12px;
            text-align: center;
            border: 1px solid #e3e6f0;
        }
        .stat-card h3 {
            font-size: 22px;
            margin-bottom: 5px;
            color: #4e73df;
        }
        .stat-card p {
            font-size: 11px;
            color: #666;
            margin-bottom: 0;
        }
        /* Tables */
        .table-container {
            margin-bottom: 25px;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px 10px;
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
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-A { background: #1cc88a; color: white; }
        .badge-B { background: #4e73df; color: white; }
        .badge-C { background: #f6c23e; color: #333; }
        .badge-D { background: #e74a3b; color: white; }
        .badge-E { background: #5a5c69; color: white; }
        .badge-success { background: #1cc88a; color: white; }
        .badge-primary { background: #4e73df; color: white; }
        .badge-warning { background: #f6c23e; color: #333; }
        .badge-danger { background: #e74a3b; color: white; }
        .progress {
            background-color: #e3e6f0;
            border-radius: 10px;
            height: 6px;
            overflow: hidden;
            width: 80px;
        }
        .progress-bar {
            height: 100%;
            border-radius: 10px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
        .page-break {
            page-break-before: always;
        }
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            .print-container {
                padding: 15px;
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
            .badge-A, .badge-B, .badge-C, .badge-D, .badge-E,
            .badge-success, .badge-primary, .badge-warning, .badge-danger,
            .progress-bar {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
        /* Print button */
        .print-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #4e73df;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            z-index: 1000;
        }
        .print-btn:hover {
            background: #224abe;
        }
        /* Score highlight */
        .score-high {
            font-weight: bold;
            color: #1cc88a;
        }
        .score-medium {
            font-weight: bold;
            color: #f6c23e;
        }
        .score-low {
            font-weight: bold;
            color: #e74a3b;
        }
        .signature {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-item {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            margin-top: 40px;
            border-top: 1px solid #333;
            width: 100%;
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
                <div style="font-size: 10px; color: #666;">
                    Beltway Office Park Tower B Lantai 5, Jl. TB Simatupang No. 41 Ragunan-Pasar Minggu, Jakarta Selatan<br>
                    Telp: (+62-21) 29857462 | Fax: (+62-21) 29857201 | Email: info@cdw-engineering.com
                </div>
            </div>
            <h1>LAPORAN PERFORMANSI KARYAWAN</h1>
            <h2>Periode: <?= $periodeText ?></h2>
            <p>Dicetak: <?= formatDateIndonesia() ?> <?= date('H:i') ?> WIB</p>
        </div>

        <!-- Filter Information -->
        <div class="filter-info">
            <table>
                <tr>
                    <td>Periode</td>
                    <td>: <?= $periodeText ?></td>
                    <?php if ($gradeFilter): ?>
                    <td>Grade</td>
                    <td>: <?= $gradeText ?></td>
                    <?php endif; ?>
                </tr>
                <?php if ($karyawanText): ?>
                <tr>
                    <td>Karyawan</td>
                    <td colspan="3">: <?= $karyawanText ?></td>
                </tr>
                <?php endif; ?>
                <?php if ($gradeFilter && $karyawanText): ?>
                <?php else: ?>
                <tr>
                    <td>Total Data</td>
                    <td>: <?= count($performansiData) ?> karyawan</td>
                </tr>
                <?php endif; ?>
            </table>
        </div>

        <!-- Statistics Summary -->
        <div class="stats-container">
            <div class="stat-card">
                <h3><?= number_format($stats['total_karyawan_terdata'] ?? 0) ?></h3>
                <p>Karyawan Terdata</p>
            </div>
            <div class="stat-card">
                <h3><?= formatScore($stats['rata_rata_skor'] ?? 0) ?></h3>
                <p>Rata-rata Skor</p>
            </div>
            <div class="stat-card">
                <h3><?= formatScore($stats['skor_tertinggi'] ?? 0) ?></h3>
                <p>Skor Tertinggi</p>
            </div>
            <div class="stat-card">
                <h3><?= number_format($stats['total_grade_a'] ?? 0) ?></h3>
                <p>Grade A</p>
            </div>
        </div>

        <!-- Grade Distribution Summary -->
        <div class="table-container">
            <table style="width: auto; margin-bottom: 20px;">
                <thead>
                    <tr>
                        <th>Grade</th>
                        <th>Predikat</th>
                        <th>Jumlah</th>
                        <th>Persentase</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totalKaryawan = $stats['total_karyawan_terdata'] ?? 1;
                    $grades = [
                        'A' => ['label' => 'Sangat Baik (90+)', 'count' => $stats['total_grade_a'] ?? 0],
                        'B' => ['label' => 'Baik (75-89)', 'count' => $stats['total_grade_b'] ?? 0],
                        'C' => ['label' => 'Cukup (60-74)', 'count' => $stats['total_grade_c'] ?? 0],
                        'D' => ['label' => 'Kurang (50-59)', 'count' => $stats['total_grade_d'] ?? 0],
                        'E' => ['label' => 'Buruk (<50)', 'count' => $stats['total_grade_e'] ?? 0]
                    ];
                    foreach ($grades as $grade => $info):
                    ?>
                    <tr>
                        <td class="text-center"><span class="badge badge-<?= $grade ?>"><?= $grade ?></span></td>
                        <td><?= $info['label'] ?></td>
                        <td class="text-center"><?= number_format($info['count']) ?></td>
                        <td class="text-center"><?= round(($info['count'] / $totalKaryawan) * 100, 1) ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Main Data Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th width="30">No</th>
                        <th>Periode</th>
                        <th>NIK</th>
                        <th>Nama Karyawan</th>
                        <th>Jabatan</th>
                        <th>Skor Total</th>
                        <th>Grade</th>
                        <th>Predikat</th>
                        <th>Kehadiran</th>
                        <th>Kualitas</th>
                        <th>Kedisiplinan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($performansiData)): ?>
                    <tr>
                        <td colspan="12" class="text-center">Tidak ada data performansi untuk periode yang dipilih</td>
                    </tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php foreach ($performansiData as $item): 
                            $scoreClass = '';
                            if (($item['skor_total'] ?? 0) >= 90) $scoreClass = 'score-high';
                            elseif (($item['skor_total'] ?? 0) >= 75) $scoreClass = '';
                            elseif (($item['skor_total'] ?? 0) >= 60) $scoreClass = 'score-medium';
                            else $scoreClass = 'score-low';
                        ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td class="text-center"><?= ($monthNames[$item['periode_bulan']] ?? $item['periode_bulan']) . '/' . $item['periode_tahun'] ?></td>
                            <td><?= htmlspecialchars($item['nik'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($item['nama_panggilan'] ?? $item['nama_lengkap']) ?></td>
                            <td><?= htmlspecialchars($item['jabatan'] ?? '-') ?></td>
                            <td class="text-center <?= $scoreClass ?>"><?= formatScore($item['skor_total'] ?? 0) ?></td>
                            <td class="text-center"><span class="badge badge-<?= $item['grade'] ?? 'E' ?>"><?= $item['grade'] ?? '-' ?></span></td>
                            <td><?= htmlspecialchars($item['predikat'] ?? '-') ?></td>
                            <td class="text-center"><?= formatScore($item['skor_kehadiran'] ?? 0) ?></td>
                            <td class="text-center"><?= formatScore($item['skor_kualitas_kerja'] ?? 0) ?></td>
                            <td class="text-center"><?= formatScore($item['skor_kedisiplinan'] ?? 0) ?></td>
                            <td class="text-center">
                                <?php 
                                $statusLabel = [
                                    'draft' => 'Draft',
                                    'review' => 'Review',
                                    'approved' => 'Disetujui',
                                    'rejected' => 'Ditolak',
                                    'closed' => 'Tertutup'
                                ];
                                ?>
                                <?= $statusLabel[$item['status']] ?? $item['status'] ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Detailed Performance per Karyawan (if filtered by single employee) -->
        <?php if ($karyawanIdFilter && !empty($selectedKaryawan) && !empty($performansiData)): ?>
        <div class="page-break"></div>
        
        <?php foreach ($performansiData as $item): ?>
        <div style="margin-top: 30px;">
            <h3 style="color: #4e73df; margin-bottom: 15px;">
                Detail Performansi: <?= htmlspecialchars($item['nama_lengkap']) ?>
                <span style="font-size: 12px; font-weight: normal;">(<?= ($monthNames[$item['periode_bulan']] ?? $item['periode_bulan']) . ' ' . $item['periode_tahun'] ?>)</span>
            </h3>
            
            <!-- Target vs Realisasi -->
            <table style="margin-bottom: 20px; width: 100%;">
                <thead>
                    <tr>
                        <th width="35%">Komponen</th>
                        <th width="25%">Target</th>
                        <th width="25%">Realisasi</th>
                        <th width="15%">Pencapaian</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Kehadiran</td>
                        <td class="text-center"><?= formatNumber($item['target_kehadiran'] ?? 100) ?>%</td>
                        <td class="text-center"><?= formatNumber($item['realisasi_kehadiran'] ?? 0) ?>%</td>
                        <td class="text-center"><?= formatNumber(($item['realisasi_kehadiran'] ?? 0) / (($item['target_kehadiran'] ?? 100) ?: 1) * 100) ?>%</td>
                    </tr>
                    <tr>
                        <td>Penyelesaian Tugas</td>
                        <td class="text-center"><?= formatNumber($item['target_penyelesaian_tugas'] ?? 100) ?>%</td>
                        <td class="text-center"><?= formatNumber($item['realisasi_penyelesaian_tugas'] ?? 0) ?>%</td>
                        <td class="text-center"><?= formatNumber(($item['realisasi_penyelesaian_tugas'] ?? 0) / (($item['target_penyelesaian_tugas'] ?? 100) ?: 1) * 100) ?>%</td>
                    </tr>
                    <tr>
                        <td>Kesalahan Kerja</td>
                        <td class="text-center">≤ <?= formatNumber($item['target_kesalahan_kerja'] ?? 0) ?>%</td>
                        <td class="text-center"><?= formatNumber($item['realisasi_kesalahan_kerja'] ?? 0) ?>%</td>
                        <td class="text-center"><?= ($item['realisasi_kesalahan_kerja'] ?? 0) <= ($item['target_kesalahan_kerja'] ?? 0) ? '✓ Tercapai' : '✗ Tidak tercapai' ?></td>
                    </tr>
                    <tr>
                        <td>Kepuasan Client</td>
                        <td class="text-center"><?= formatNumber($item['target_kepuasan_client'] ?? 90) ?>%</td>
                        <td class="text-center"><?= formatNumber($item['realisasi_kepuasan_client'] ?? 0) ?>%</td>
                        <td class="text-center"><?= formatNumber(($item['realisasi_kepuasan_client'] ?? 0) / (($item['target_kepuasan_client'] ?? 90) ?: 1) * 100) ?>%</td>
                    </tr>
                    <tr>
                        <td>Proaktif</td>
                        <td class="text-center"><?= formatNumber($item['target_proaktif'] ?? 85) ?>%</td>
                        <td class="text-center"><?= formatNumber($item['realisasi_proaktif'] ?? 0) ?>%</td>
                        <td class="text-center"><?= formatNumber(($item['realisasi_proaktif'] ?? 0) / (($item['target_proaktif'] ?? 85) ?: 1) * 100) ?>%</td>
                    </tr>
                    <tr>
                        <td>Kerjasama Tim</td>
                        <td class="text-center"><?= formatNumber($item['target_kerjasama_tim'] ?? 90) ?>%</td>
                        <td class="text-center"><?= formatNumber($item['realisasi_kerjasama_tim'] ?? 0) ?>%</td>
                        <td class="text-center"><?= formatNumber(($item['realisasi_kerjasama_tim'] ?? 0) / (($item['target_kerjasama_tim'] ?? 90) ?: 1) * 100) ?>%</td>
                    </tr>
                    <tr>
                        <td>Keterlambatan</td>
                        <td class="text-center">≤ <?= formatNumber($item['target_terlambat'] ?? 0) ?>x</td>
                        <td class="text-center"><?= formatNumber($item['realisasi_terlambat'] ?? 0) ?>x</td>
                        <td class="text-center"><?= ($item['realisasi_terlambat'] ?? 0) <= ($item['target_terlambat'] ?? 0) ? '✓ Tercapai' : '✗ Tidak tercapai' ?></td>
                    </tr>
                    <tr>
                        <td>Ketidakhadiran</td>
                        <td class="text-center">≤ <?= formatNumber($item['target_ketidakhadiran'] ?? 0) ?> hari</td>
                        <td class="text-center"><?= formatNumber($item['realisasi_ketidakhadiran'] ?? 0) ?> hari</td>
                        <td class="text-center"><?= ($item['realisasi_ketidakhadiran'] ?? 0) <= ($item['target_ketidakhadiran'] ?? 0) ? '✓ Tercapai' : '✗ Tidak tercapai' ?></td>
                    </tr>
                    <tr>
                        <td>Jam Lembur</td>
                        <td class="text-center"><?= formatNumber($item['target_lembur'] ?? 0) ?> jam</td>
                        <td class="text-center"><?= formatNumber($item['realisasi_lembur'] ?? 0) ?> jam</td>
                        <td class="text-center"><?= formatNumber(($item['realisasi_lembur'] ?? 0) / (($item['target_lembur'] ?? 1) ?: 1) * 100) ?>%</td>
                    </tr>
                    <tr>
                        <td>Proyek Selesai</td>
                        <td class="text-center"><?= formatNumber($item['target_proyek_selesai'] ?? 0) ?> proyek</td>
                        <td class="text-center"><?= formatNumber($item['realisasi_proyek_selesai'] ?? 0) ?> proyek</td>
                        <td class="text-center"><?= formatNumber(($item['realisasi_proyek_selesai'] ?? 0) / (($item['target_proyek_selesai'] ?? 1) ?: 1) * 100) ?>%</td>
                    </tr>
                </tbody>
            </table>

            <!-- Catatan -->
            <?php if (!empty($item['catatan_atasan'])): ?>
            <div style="margin-bottom: 15px;">
                <strong>Catatan Atasan:</strong>
                <div style="background: #f8f9fc; padding: 10px; border-radius: 5px; margin-top: 5px;">
                    <?= nl2br(htmlspecialchars($item['catatan_atasan'])) ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($item['rekomendasi'])): ?>
            <div style="margin-bottom: 15px;">
                <strong>Rekomendasi:</strong>
                <div style="background: #f8f9fc; padding: 10px; border-radius: 5px; margin-top: 5px;">
                    <?= nl2br(htmlspecialchars($item['rekomendasi'])) ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>

        <!-- Signature -->
        <div class="signature">
            <div class="signature-item">
                <div>Mengetahui,</div>
                <div style="margin-top: 40px;">___________________</div>
                <div>HRD Manager</div>
            </div>
            <div class="signature-item">
                <div>Menyetujui,</div>
                <div style="margin-top: 40px;">___________________</div>
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