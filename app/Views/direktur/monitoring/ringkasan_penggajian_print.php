<?php
// Data dari controller
$penggajianData = $penggajianData ?? [];
$tahun = $tahun ?? date('Y');
$bulan = $bulan ?? '';
$statusFilter = $statusFilter ?? '';
$karyawanIdFilter = $karyawanIdFilter ?? '';
$selectedKaryawan = $selectedKaryawan ?? [];
$stats = $stats ?? [];
$monthNames = $monthNames ?? [];
$statusLabel = $statusLabel ?? [];

// Helper functions
if (!function_exists('formatRupiah')) {
    function formatRupiah($amount) {
        if (empty($amount) && $amount !== 0) return '-';
        return 'Rp ' . number_format((float)$amount, 0, ',', '.');
    }
}

if (!function_exists('formatNumber')) {
    function formatNumber($num) {
        if (($num === null || $num === '') && $num !== 0) return '-';
        return number_format((float)$num, 0, ',', '.');
    }
}

if (!function_exists('formatDecimal')) {
    function formatDecimal($num, $decimals = 1) {
        if (($num === null || $num === '') && $num !== 0) return '-';
        return number_format((float)$num, $decimals, ',', '.');
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

// Periode text
$periodeText = '';
if ($bulan) {
    $periodeText = $monthNames[$bulan] . ' ' . $tahun;
} else {
    $periodeText = 'Tahun ' . $tahun;
}

// Status filter text
$statusText = '';
if ($statusFilter) {
    $statusText = $statusLabel[$statusFilter] ?? $statusFilter;
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
    <title>Laporan Penggajian Karyawan - <?= $periodeText ?></title>
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
        .filter-info table {
            width: 100%;
        }
        .filter-info td {
            padding: 2px 5px;
        }
        .filter-info td:first-child {
            width: 100px;
            font-weight: bold;
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
        .text-primary {
            color: #4e73df;
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
        .badge-proses { background: #36b9cc; color: white; }
        .badge-approved { background: #1cc88a; color: white; }
        .badge-paid { background: #4e73df; color: white; }
        .badge-rejected { background: #e74a3b; color: white; }
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
            .badge-draft, .badge-proses, .badge-approved, .badge-paid, .badge-rejected {
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
        .total-row {
            background-color: #e3e6f0;
            font-weight: bold;
        }
        .subtotal-row {
            background-color: #f8f9fc;
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
            <h1>LAPORAN PENGGASIAN KARYAWAN</h1>
            <h2>Periode: <?= $periodeText ?></h2>
            <p>Dicetak: <?= formatDateIndonesia() ?> <?= date('H:i') ?> WIB</p>
        </div>

        <!-- Filter Information -->
        <div class="filter-info">
            <table>
                <tr>
                    <td>Periode</td>
                    <td>: <?= $periodeText ?></td>
                    <?php if ($statusFilter): ?>
                    <td width="30"></td>
                    <td>Status</td>
                    <td>: <?= $statusText ?></td>
                    <?php endif; ?>
                </tr>
                <?php if ($karyawanText): ?>
                <tr>
                    <td>Karyawan</td>
                    <td colspan="4">: <?= $karyawanText ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td>Total Data</td>
                    <td colspan="4">: <?= count($penggajianData) ?> karyawan</td>
                </tr>
            </table>
        </div>

        <!-- Statistics Summary -->
        <div class="stats-container">
            <div class="stat-card">
                <h3><?= number_format($stats['total_karyawan'] ?? 0) ?></h3>
                <p>Karyawan</p>
            </div>
            <div class="stat-card">
                <h3><?= formatRupiah($stats['total_gaji_bersih'] ?? 0) ?></h3>
                <p>Total Gaji Bersih</p>
            </div>
            <div class="stat-card">
                <h3><?= formatRupiah($stats['rata_rata_gaji'] ?? 0) ?></h3>
                <p>Rata-rata Gaji</p>
            </div>
            <div class="stat-card">
                <h3><?= formatDecimal($stats['total_lembur'] ?? 0, 1) ?></h3>
                <p>Total Jam Lembur</p>
            </div>
        </div>

        <!-- Main Data Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th width="30">No</th>
                        <th>NIK</th>
                        <th>Nama Karyawan</th>
                        <th>Jabatan</th>
                        <th>Departemen</th>
                        <th class="text-right">Gaji Pokok</th>
                        <th class="text-right">Tunjangan</th>
                        <th class="text-right">Lembur</th>
                        <th class="text-right">Total Penghasilan</th>
                        <th class="text-right">Total Potongan</th>
                        <th class="text-right">Gaji Bersih</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($penggajianData)): ?>
                    <tr>
                        <td colspan="12" class="text-center">Tidak ada data penggajian untuk periode yang dipilih</td>
                    </tr>
                    <?php else: ?>
                        <?php 
                        $no = 1;
                        $grandTotalPenghasilan = 0;
                        $grandTotalPotongan = 0;
                        $grandTotalGajiBersih = 0;
                        ?>
                        <?php foreach ($penggajianData as $item): 
                            $totalTunjangan = ($item['tunjangan_jabatan'] ?? 0) + ($item['tunjangan_makan'] ?? 0) + 
                                              ($item['tunjangan_transport'] ?? 0) + ($item['tunjangan_kesehatan'] ?? 0) +
                                              ($item['tunjangan_hari_raya'] ?? 0) + ($item['tunjangan_lainnya'] ?? 0);
                            $grandTotalPenghasilan += $item['total_penghasilan'] ?? 0;
                            $grandTotalPotongan += $item['total_potongan'] ?? 0;
                            $grandTotalGajiBersih += $item['gaji_bersih'] ?? 0;
                        ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= htmlspecialchars($item['nik'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($item['nama_panggilan'] ?? $item['nama_lengkap']) ?></td>
                            <td><?= htmlspecialchars($item['jabatan'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($item['departemen'] ?? '-') ?></td>
                            <td class="text-right"><?= formatRupiah($item['gaji_pokok'] ?? 0) ?></td>
                            <td class="text-right"><?= formatRupiah($totalTunjangan) ?></td>
                            <td class="text-right"><?= formatRupiah($item['lembur'] ?? 0) ?></td>
                            <td class="text-right text-primary fw-bold"><?= formatRupiah($item['total_penghasilan'] ?? 0) ?></td>
                            <td class="text-right text-danger"><?= formatRupiah($item['total_potongan'] ?? 0) ?></td>
                            <td class="text-right text-success fw-bold"><?= formatRupiah($item['gaji_bersih'] ?? 0) ?></td>
                            <td class="text-center">
                                <span class="badge badge-<?= $item['status'] ?? 'draft' ?>">
                                    <?= $statusLabel[$item['status']] ?? $item['status'] ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <!-- Grand Total Row -->
                        <tr class="total-row">
                            <td colspan="8" class="text-right fw-bold">GRAND TOTAL</td>
                            <td class="text-right fw-bold"><?= formatRupiah($grandTotalPenghasilan) ?></td>
                            <td class="text-right fw-bold"><?= formatRupiah($grandTotalPotongan) ?></td>
                            <td class="text-right fw-bold"><?= formatRupiah($grandTotalGajiBersih) ?></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Detailed Per Karyawan (if filtered by single employee) -->
        <?php if ($karyawanIdFilter && !empty($selectedKaryawan) && !empty($penggajianData)): ?>
        <div class="page-break"></div>
        
        <?php foreach ($penggajianData as $item): ?>
        <div style="margin-top: 20px;">
            <h3 style="color: #4e73df; font-size: 14px; margin-bottom: 10px;">
                Slip Gaji: <?= htmlspecialchars($item['nama_lengkap']) ?>
                <span style="font-size: 11px; font-weight: normal;">(<?= ($monthNames[$item['periode_bulan']] ?? $item['periode_bulan']) . ' ' . $item['periode_tahun'] ?>)</span>
            </h3>
            
            <table style="width: 100%; margin-bottom: 15px;">
                <tr>
                    <td width="30%">NIK</td>
                    <td width="70%">: <?= htmlspecialchars($item['nik'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td>: <?= htmlspecialchars($item['jabatan'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td>Departemen</td>
                    <td>: <?= htmlspecialchars($item['departemen'] ?? '-') ?></td>
                </tr>
            </table>
            
            <!-- Penghasilan -->
            <table style="width: 48%; float: left; margin-right: 4%;">
                <thead>
                    <tr>
                        <th colspan="2">KOMPONEN PENGHASILAN</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Gaji Pokok</td>
                        <td class="text-right"><?= formatRupiah($item['gaji_pokok'] ?? 0) ?></td>
                    </tr>
                    <tr>
                        <td>Tunjangan Jabatan</td>
                        <td class="text-right"><?= formatRupiah($item['tunjangan_jabatan'] ?? 0) ?></td>
                    </tr>
                    <tr>
                        <td>Tunjangan Makan</td>
                        <td class="text-right"><?= formatRupiah($item['tunjangan_makan'] ?? 0) ?></td>
                    </tr>
                    <tr>
                        <td>Tunjangan Transportasi</td>
                        <td class="text-right"><?= formatRupiah($item['tunjangan_transport'] ?? 0) ?></td>
                    </tr>
                    <tr>
                        <td>Tunjangan Kesehatan</td>
                        <td class="text-right"><?= formatRupiah($item['tunjangan_kesehatan'] ?? 0) ?></td>
                    </tr>
                    <?php if (!empty($item['tunjangan_hari_raya']) && $item['tunjangan_hari_raya'] > 0): ?>
                    <tr>
                        <td>Tunjangan Hari Raya (THR)</td>
                        <td class="text-right"><?= formatRupiah($item['tunjangan_hari_raya'] ?? 0) ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td>Lembur (<?= formatDecimal($item['total_jam_lembur'] ?? 0, 1) ?> jam)</td>
                        <td class="text-right"><?= formatRupiah($item['lembur'] ?? 0) ?></td>
                    </tr>
                    <?php if (!empty($item['bonus_kinerja']) && $item['bonus_kinerja'] > 0): ?>
                    <tr>
                        <td>Bonus Kinerja</td>
                        <td class="text-right"><?= formatRupiah($item['bonus_kinerja'] ?? 0) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($item['insentif_proyek']) && $item['insentif_proyek'] > 0): ?>
                    <tr>
                        <td>Insentif Proyek</td>
                        <td class="text-right"><?= formatRupiah($item['insentif_proyek'] ?? 0) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($item['komisi_penjualan']) && $item['komisi_penjualan'] > 0): ?>
                    <tr>
                        <td>Komisi Penjualan</td>
                        <td class="text-right"><?= formatRupiah($item['komisi_penjualan'] ?? 0) ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr class="total-row">
                        <td class="fw-bold">TOTAL PENGHASILAN</td>
                        <td class="text-right fw-bold"><?= formatRupiah($item['total_penghasilan'] ?? 0) ?></td>
                    </tr>
                </tbody>
            </table>
            
            <!-- Potongan -->
            <table style="width: 48%; float: left;">
                <thead>
                    <tr>
                        <th colspan="2">KOMPONEN POTONGAN</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>BPJS Kesehatan</td>
                        <td class="text-right"><?= formatRupiah($item['potongan_bpjs_kesehatan'] ?? 0) ?></td>
                    </tr>
                    <tr>
                        <td>BPJS Ketenagakerjaan</td>
                        <td class="text-right"><?= formatRupiah($item['potongan_bpjs_tenaga_kerja'] ?? 0) ?></td>
                    </tr>
                    <tr>
                        <td>PPh 21</td>
                        <td class="text-right"><?= formatRupiah($item['potongan_pph21'] ?? 0) ?></td>
                    </tr>
                    <tr>
                        <td>Potongan Absensi</td>
                        <td class="text-right"><?= formatRupiah($item['potongan_absensi'] ?? 0) ?></td>
                    </tr>
                    <?php if (!empty($item['potongan_pinjaman']) && $item['potongan_pinjaman'] > 0): ?>
                    <tr>
                        <td>Potongan Pinjaman</td>
                        <td class="text-right"><?= formatRupiah($item['potongan_pinjaman'] ?? 0) ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr class="total-row">
                        <td class="fw-bold">TOTAL POTONGAN</td>
                        <td class="text-right fw-bold"><?= formatRupiah($item['total_potongan'] ?? 0) ?></td>
                    </tr>
                </tbody>
            </table>
            
            <div style="clear: both;"></div>
            
            <!-- Gaji Bersih -->
            <div style="margin-top: 15px; padding: 10px; background: #1cc88a; color: white; text-align: center; border-radius: 6px;">
                <strong style="font-size: 12px;">GAJI BERSIH DITERIMA</strong>
                <h2 style="margin: 5px 0 0 0; font-size: 20px;"><?= formatRupiah($item['gaji_bersih'] ?? 0) ?></h2>
            </div>
            
            <!-- Catatan -->
            <?php if (!empty($item['catatan'])): ?>
            <div style="margin-top: 15px; padding: 8px; background: #f8f9fc; border-radius: 4px;">
                <strong>Catatan:</strong> <?= nl2br(htmlspecialchars($item['catatan'])) ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>

        <!-- Signature -->
        <div class="signature">
            <div class="signature-item">
                <div>Mengetahui,</div>
                <div style="margin-top: 30px;">___________________</div>
                <div>HRD Manager</div>
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