<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Kasbon Karyawan - PT CDW Engineering</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 13px; margin: 0; padding: 20px; color: #1e293b; background: #fff; }
        .print-box { border: 2px solid #334155; padding: 25px; max-width: 900px; margin: 0 auto; border-radius: 8px; }
        .header { text-align: center; border-bottom: 2px double #334155; padding-bottom: 15px; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #1e3c72; font-size: 22px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .header p { margin: 4px 0 0; color: #64748b; font-size: 12px; }
        .header .doc-title { margin-top: 15px; font-size: 16px; font-weight: 700; color: #0f172a; text-decoration: underline; }
        
        .info-meta { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 12px; color: #475569; }

        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th, .table td { border: 1px solid #cbd5e1; padding: 10px 12px; text-align: left; }
        .table th { background-color: #f1f5f9; color: #1e293b; font-weight: 700; font-size: 12px; text-transform: uppercase; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: 700; }
        .text-success { color: #16a34a; }
        .text-danger { color: #dc2626; }
        .text-primary { color: #2563eb; }

        .footer-sig { margin-top: 40px; display: flex; justify-content: space-between; }
        .sig-box { text-align: center; width: 220px; }

        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
            .print-box { border: none; padding: 0; }
        }
    </style>
</head>
<body>

<div class="no-print" style="max-width: 900px; margin: 0 auto 20px; text-align: right;">
    <button onclick="window.print()" style="padding: 10px 22px; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; border: none; border-radius: 20px; cursor: pointer; font-weight: 600; box-shadow: 0 4px 12px rgba(30,60,114,0.3);">
        <i class="fas fa-print"></i> Cetak / Simpan PDF
    </button>
</div>

<div class="print-box">
    <div class="header">
        <h2>PT CDW ENGINEERING</h2>
        <p>Jl. Utama No. 100, Jakarta | Telp: (021) 555-0199 | Email: finance@cdwengineering.co.id</p>
        <div class="doc-title">REKAPITULASI PENGAJUAN KASBON KARYAWAN</div>
        <small style="color: #64748b;">Filter Status: <?= esc($filterStatus ? ucfirst($filterStatus) : 'Semua Status') ?></small>
    </div>

    <div class="info-meta">
        <div><strong>Tanggal Cetak:</strong> <?= esc($tanggalCetak) ?></div>
        <div><strong>Status Dokumen:</strong> Monitoring Direksi</div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th class="text-center" width="5%">No</th>
                <th>Nomor & Tgl Kasbon</th>
                <th>Pemohon (Nama & Jabatan)</th>
                <th class="text-right">Jumlah (Rp)</th>
                <th>Alasan Pinjaman</th>
                <th class="text-center">Status HRD</th>
                <th class="text-center">Status Direktur</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; $totalNominal = 0; ?>
            <?php if(empty($kasbon)): ?>
                <tr><td colspan="7" class="text-center" style="padding: 20px;">Belum ada data pengajuan kasbon.</td></tr>
            <?php else: ?>
                <?php foreach($kasbon as $k): ?>
                    <?php $totalNominal += floatval($k['jumlah_kasbon'] ?? 0); ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td>
                            <strong><?= esc($k['nomor_kasbon']) ?></strong><br>
                            <small style="color:#64748b;"><?= date('d M Y', strtotime($k['tanggal_pengajuan'])) ?></small>
                        </td>
                        <td>
                            <strong><?= esc($k['nama_lengkap']) ?></strong><br>
                            <small style="color:#64748b;"><?= esc($k['jabatan']) ?></small>
                        </td>
                        <td class="text-right fw-bold text-primary">Rp <?= number_format($k['jumlah_kasbon'], 0, ',', '.') ?></td>
                        <td><?= esc($k['alasan']) ?></td>
                        <td class="text-center"><span style="font-weight:600; color:#0891b2;"><?= esc($k['status_hrd'] ?: 'Draft') ?></span></td>
                        <td class="text-center">
                            <?php
                            $st = $k['status_direktur'] ?? 'Menunggu';
                            $color = '#d97706';
                            if ($st == 'Disetujui') $color = '#16a34a';
                            if ($st == 'Ditolak') $color = '#dc2626';
                            ?>
                            <span style="font-weight:600; color:<?= $color ?>;"><?= esc($st) ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr style="background: #f8fafc; font-weight: 700;">
                    <td colspan="3" class="text-right">TOTAL NOMINAL KASBON REKAPITULASI</td>
                    <td class="text-right text-primary" style="font-size: 14px;">Rp <?= number_format($totalNominal, 0, ',', '.') ?></td>
                    <td colspan="3"></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer-sig">
        <div class="sig-box">
            <p>Disiapkan Oleh,<br><strong>Finance Manager</strong></p>
            <br><br><br>
            <strong>( ................................ )</strong>
        </div>
        <div class="sig-box">
            <p>Disetujui Oleh,<br><strong>Direktur Utama</strong></p>
            <br><br><br>
            <strong>( Cecep Trihardiyanto )</strong>
        </div>
    </div>
</div>

</body>
</html>
