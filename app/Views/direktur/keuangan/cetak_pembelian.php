<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Pencatatan Pembelian (PR) - PT CDW Engineering</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11.5px; margin: 0; padding: 15px; color: #1e293b; background: #fff; }
        .print-box { border: 1.5px solid #334155; padding: 20px; max-width: 1000px; margin: 0 auto; border-radius: 6px; page-break-inside: avoid; }
        .header { text-align: center; border-bottom: 2px double #334155; padding-bottom: 10px; margin-bottom: 15px; }
        .header h2 { margin: 0; color: #1e3c72; font-size: 20px; font-weight: 700; text-transform: uppercase; }
        .header p { margin: 3px 0 0; color: #64748b; font-size: 11px; }
        .header .doc-title { margin-top: 10px; font-size: 14px; font-weight: 700; color: #0f172a; text-decoration: underline; }
        
        .info-meta { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 11px; color: #475569; }

        .table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 11px; }
        .table th, .table td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        .table th { background-color: #f1f5f9; color: #1e293b; font-weight: 700; font-size: 11px; text-transform: uppercase; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: 700; }
        .text-success { color: #16a34a; }
        .text-danger { color: #dc2626; }
        .text-primary { color: #2563eb; }

        .footer-sig { margin-top: 30px; display: flex; justify-content: space-between; page-break-inside: avoid; }
        .sig-box { text-align: center; width: 200px; }
        .sig-box p { margin: 0 0 40px 0; }

        @media print {
            .no-print { display: none !important; }
            body { padding: 0 !important; }
            .print-box { border: 1px solid #000 !important; margin: 0 auto !important; box-shadow: none !important; }
        }
    </style>
</head>
<body>

<div class="no-print" style="max-width: 1000px; margin: 0 auto 15px; text-align: right;">
    <button onclick="window.print()" style="padding: 8px 20px; background: #1e3c72; color: white; border: none; border-radius: 20px; cursor: pointer; font-weight: bold; font-size: 12px;">
        <i class="fas fa-print"></i> Cetak / Simpan PDF
    </button>
</div>

<div class="print-box">
    <div class="header">
        <h2>PT CDW ENGINEERING</h2>
        <p>Jl. Utama No. 100, Jakarta | Telp: (021) 555-0199 | Email: procurement@cdwengineering.co.id</p>
        <div class="doc-title">REKAPITULASI PENCATATAN & APPROVAL PEMBELIAN (PR)</div>
        <small style="color: #64748b;">Filter: <?= esc($filterStatus ? ucfirst($filterStatus) : 'Semua Status') ?> | Tipe: <?= esc($filterTipe ? ucfirst($filterTipe) : 'Semua Tipe') ?></small>
    </div>

    <div class="info-meta">
        <div><strong>Tanggal Cetak:</strong> <?= esc($tanggalCetak) ?></div>
        <div><strong>Otorisasi:</strong> Direksi & Divisi Keuangan</div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th class="text-center" width="4%">No</th>
                <th width="12%">No PR & Tgl</th>
                <th width="18%">Pemohon & Jabatan</th>
                <th width="12%">Tipe & Platform</th>
                <th width="14%">Metode Bayar</th>
                <th class="text-right" width="14%">Total Estimasi (Rp)</th>
                <th>Alasan Pembelian</th>
                <th class="text-center" width="10%">Status Direktur</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; $totalEstimasiAll = 0; ?>
            <?php if(empty($pembelian)): ?>
                <tr><td colspan="8" class="text-center" style="padding: 20px;">Belum ada pengajuan pencatatan pembelian.</td></tr>
            <?php else: ?>
                <?php foreach($pembelian as $p): ?>
                    <?php $totalEstimasiAll += floatval($p['total_estimasi'] ?? 0); ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td>
                            <strong><?= esc($p['nomor_pr']) ?></strong><br>
                            <small style="color:#64748b;"><?= date('d/m/Y', strtotime($p['tanggal_pengajuan'])) ?></small>
                        </td>
                        <td>
                            <strong><?= esc($p['nama_lengkap']) ?></strong><br>
                            <small style="color:#64748b;"><?= esc($p['jabatan'] ?? '-') ?></small>
                        </td>
                        <td>
                            <strong><?= esc($p['tipe_pembelian'] ?? 'Online') ?></strong><br>
                            <small style="color:#64748b;"><?= esc($p['platform_pembelian'] ?? 'Tokopedia') ?></small>
                        </td>
                        <td><?= esc($p['metode_pembayaran'] ?? '-') ?></td>
                        <td class="text-right fw-bold text-primary">Rp <?= number_format($p['total_estimasi'] ?? 0, 0, ',', '.') ?></td>
                        <td><?= esc($p['alasan_pembelian']) ?></td>
                        <td class="text-center">
                            <?php
                            $st = $p['status_direktur'] ?? 'Menunggu';
                            $color = '#d97706';
                            if ($st == 'Disetujui') $color = '#16a34a';
                            if ($st == 'Ditolak') $color = '#dc2626';
                            ?>
                            <span style="font-weight:600; color:<?= $color ?>;"><?= esc($st) ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr style="background: #f8fafc; font-weight: 700;">
                    <td colspan="5" class="text-right">TOTAL NOMINAL ESTIMASI PEMBELIAN:</td>
                    <td class="text-right text-primary" style="font-size: 13px;">Rp <?= number_format($totalEstimasiAll, 0, ',', '.') ?></td>
                    <td colspan="2"></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer-sig">
        <div class="sig-box">
            <p>Disiapkan Oleh,<br><strong>Procurement Manager</strong></p>
            <strong>( ................................ )</strong>
        </div>
        <div class="sig-box">
            <p>Disetujui Oleh,<br><strong>Direktur Utama</strong></p>
            <strong>( Cecep Trihardiyanto )</strong>
        </div>
    </div>
</div>

</body>
</html>
