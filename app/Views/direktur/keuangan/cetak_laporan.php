<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan Eksekutif - PT CDW Engineering</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 0; padding: 15px; color: #1e293b; background: #fff; }
        .box { border: 1.5px solid #334155; padding: 20px; max-width: 1000px; margin: 0 auto; border-radius: 6px; page-break-inside: avoid; }
        .header { text-align: center; border-bottom: 2px double #334155; padding-bottom: 10px; margin-bottom: 15px; }
        .header h2 { margin: 0; color: #1e3c72; font-size: 20px; font-weight: 700; text-transform: uppercase; }
        .header p { margin: 3px 0 0; color: #64748b; font-size: 11px; }
        .header .doc-title { margin-top: 10px; font-size: 14px; font-weight: 700; color: #0f172a; text-decoration: underline; }
        
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 11px; }
        .table th, .table td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        .table th { background-color: #f1f5f9; color: #1e293b; font-weight: 700; font-size: 11px; text-transform: uppercase; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: 700; }
        .text-success { color: #16a34a; }
        .text-danger { color: #dc2626; }
        .text-primary { color: #2563eb; }

        .footer { margin-top: 30px; display: flex; justify-content: space-between; page-break-inside: avoid; }
        .signature { text-align: center; width: 200px; }
        .signature p { margin: 0 0 40px 0; }

        @media print {
            .no-print { display: none !important; }
            body { padding: 0 !important; }
            .box { border: 1px solid #000 !important; margin: 0 auto !important; box-shadow: none !important; }
        }
    </style>
</head>
<body>

<div class="no-print" style="max-width: 1000px; margin: 0 auto 15px; text-align: right;">
    <button onclick="window.print()" style="padding: 8px 20px; background: #1e3c72; color: white; border: none; border-radius: 20px; cursor: pointer; font-weight: bold; font-size: 12px;">
        <i class="fas fa-print"></i> Cetak / Simpan PDF
    </button>
</div>

<div class="box">
    <div class="header">
        <h2>PT CDW ENGINEERING</h2>
        <p>Jl. Utama No. 100, Jakarta | Telp: (021) 555-0199 | Email: finance@cdwengineering.co.id</p>
        <div class="doc-title">LAPORAN KEUANGAN EKSEKUTIF & ARUS KAS (REAL DATABASE)</div>
        <small style="color: #64748b;">Tahun Buku: <?= esc($tahun) ?> | Tanggal Cetak: <?= esc($tanggalCetak) ?></small>
    </div>

    <!-- Ringkasan Finansial Utama -->
    <table class="table mb-3">
        <thead>
            <tr>
                <th>RINGKASAN EKSEKUTIF KEUANGAN</th>
                <th class="text-right">NOMINAL (RP)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>1. Total Pendapatan Client (Gross Revenue / Inflow)</strong></td>
                <td class="text-right text-success fw-bold">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;• Beban Pembelian & Pengadaan Barang (PR)</td>
                <td class="text-right text-danger">Rp <?= number_format($totalPembelian, 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;• Beban Penggajian & Payroll Karyawan</td>
                <td class="text-right text-danger">Rp <?= number_format($totalGaji, 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;• Pencairan Kasbon Karyawan</td>
                <td class="text-right text-danger">Rp <?= number_format($totalKasbon, 0, ',', '.') ?></td>
            </tr>
            <tr style="background: #fef2f2;">
                <td><strong>2. TOTAL OUTFLOW OPERASIONAL</strong></td>
                <td class="text-right text-danger fw-bold">Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?></td>
            </tr>
            <tr style="background: #f0fdf4;">
                <td><strong style="font-size: 12px;">3. ESTIMASI LABA / RUGI BERSIH (NET PROFIT)</strong></td>
                <td class="text-right text-primary fw-bold" style="font-size: 13px;">Rp <?= number_format($labaBersih, 0, ',', '.') ?></td>
            </tr>
        </tbody>
    </table>

    <!-- Rincian Arus Kas 12 Bulan -->
    <h4 style="margin: 15px 0 5px 0; color: #0f172a; font-size: 12px; text-transform: uppercase;">Rincian Arus Kas Per Bulan (Januari - Desember <?= esc($tahun) ?>)</h4>
    <table class="table">
        <thead>
            <tr>
                <th class="text-center">Bulan</th>
                <th class="text-right">Pendapatan Client (Rp)</th>
                <th class="text-right">Pembelian (PR)</th>
                <th class="text-right">Gaji Karyawan</th>
                <th class="text-right">Kasbon</th>
                <th class="text-right">Total Outflow (Rp)</th>
                <th class="text-right">Surplus / Defisit</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($monthlyData as $m): ?>
                <tr>
                    <td class="text-center fw-bold"><?= esc($m['bulan_name']) ?></td>
                    <td class="text-right text-success">Rp <?= number_format($m['pendapatan'], 0, ',', '.') ?></td>
                    <td class="text-right">Rp <?= number_format($m['pembelian'], 0, ',', '.') ?></td>
                    <td class="text-right">Rp <?= number_format($m['gaji'], 0, ',', '.') ?></td>
                    <td class="text-right">Rp <?= number_format($m['kasbon'], 0, ',', '.') ?></td>
                    <td class="text-right text-danger fw-bold">Rp <?= number_format($m['total_pengeluaran'], 0, ',', '.') ?></td>
                    <td class="text-right fw-bold <?= $m['surplus'] >= 0 ? 'text-primary' : 'text-danger' ?>">
                        <?= $m['surplus'] >= 0 ? '+' : '' ?>Rp <?= number_format($m['surplus'], 0, ',', '.') ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr style="background: #f1f5f9; font-weight: bold;">
                <td class="text-center">TOTAL TAHUNAN</td>
                <td class="text-right text-success">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></td>
                <td class="text-right">Rp <?= number_format($totalPembelian, 0, ',', '.') ?></td>
                <td class="text-right">Rp <?= number_format($totalGaji, 0, ',', '.') ?></td>
                <td class="text-right">Rp <?= number_format($totalKasbon, 0, ',', '.') ?></td>
                <td class="text-right text-danger">Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?></td>
                <td class="text-right text-primary">Rp <?= number_format($labaBersih, 0, ',', '.') ?></td>
            </tr>
        </tbody>
    </table>

    <!-- Log Transaksi Real-Time -->
    <?php if (!empty($realTransactions)): ?>
        <h4 style="margin: 20px 0 5px 0; color: #0f172a; font-size: 12px; text-transform: uppercase;">Log Transaksi Real-Time Terbaru</h4>
        <table class="table">
            <thead>
                <tr>
                    <th class="text-center">Tanggal</th>
                    <th>Modul</th>
                    <th>No. Ref</th>
                    <th>Pemohon</th>
                    <th>Keterangan</th>
                    <th class="text-right">Nominal (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_slice($realTransactions, 0, 10) as $tx): ?>
                    <tr>
                        <td class="text-center"><?= date('d/m/Y', strtotime($tx['tanggal'])) ?></td>
                        <td><?= esc($tx['jenis']) ?></td>
                        <td><strong><?= esc($tx['nomor']) ?></strong></td>
                        <td><?= esc($tx['pemohon']) ?></td>
                        <td><?= esc($tx['keterangan']) ?></td>
                        <td class="text-right fw-bold <?= $tx['tipe'] === 'Pemasukan' ? 'text-success' : 'text-danger' ?>">
                            Rp <?= number_format($tx['nominal'], 0, ',', '.') ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div class="footer">
        <div class="signature">
            <p>Disiapkan Oleh,<br><strong>Finance & Accounting Manager</strong></p>
            <strong>( ................................ )</strong>
        </div>
        <div class="signature">
            <p>Disetujui Oleh,<br><strong>Direktur Utama</strong></p>
            <strong>( Cecep Trihardiyanto )</strong>
        </div>
    </div>
</div>

</body>
</html>
