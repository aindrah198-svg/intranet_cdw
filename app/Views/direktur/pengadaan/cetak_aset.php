<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Daftar Aset Perusahaan - PT CDW Engineering</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; margin: 0; padding: 20px; background: #fff; }
        .box { border: 2px solid #333; padding: 25px; max-width: 900px; margin: 0 auto; border-radius: 8px; }
        .header { text-align: center; border-bottom: 2px double #333; padding-bottom: 15px; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #2b8a3e; }
        .header p { margin: 5px 0 0; color: #666; font-size: 12px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table th, .table td { border: 1px solid #ccc; padding: 10px; }
        .table th { background: #f1f3f5; text-align: left; }
        .text-right { text-align: right; }
        .footer { margin-top: 40px; display: flex; justify-content: space-between; }
        .signature { text-align: center; width: 220px; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
            .box { border: none; }
        }
    </style>
</head>
<body>

<div class="no-print" style="max-width: 900px; margin: 0 auto 20px; text-align: right;">
    <button onclick="window.print()" style="padding: 10px 20px; background: #2b8a3e; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">
        <i class="fas fa-print"></i> Cetak / Simpan PDF
    </button>
</div>

<div class="box">
    <div class="header">
        <h2>PT CDW ENGINEERING</h2>
        <p>Jl. Utama No. 100, Jakarta | Telp: (021) 555-0199 | Email: asset@cdwengineering.co.id</p>
        <h3 style="margin-top: 15px; margin-bottom: 0; text-decoration: underline;">INVENTARIS & DAFTAR ASET PERUSAHAAN</h3>
        <small>Tanggal Cetak: <?= esc($tanggalCetak) ?></small>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Pengadaan</th>
                <th>Nama Aset</th>
                <th>Kategori</th>
                <th>Jumlah</th>
                <th class="text-right">Estimasi Biaya (Rp)</th>
                <th>Status Direktur</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($aset)): ?>
                <tr><td colspan="7" style="text-align: center; color: #888;">Belum ada data pengadaan aset terdata.</td></tr>
            <?php else: ?>
                <?php $no=1; foreach($aset as $a): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= esc($a['kode_pengadaan']) ?></td>
                    <td><strong><?= esc($a['nama_aset']) ?></strong></td>
                    <td><?= esc($a['kategori']) ?></td>
                    <td><?= esc($a['jumlah']) ?> Unit</td>
                    <td class="text-right">Rp <?= number_format($a['estimasi_harga'], 0, ',', '.') ?></td>
                    <td><?= ucfirst(esc($a['status'])) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <div class="signature">
            <p>Manajer Logistik & Aset,</p>
            <br><br><br>
            <strong>( Logistics Dept )</strong>
        </div>
        <div class="signature">
            <p>Disetujui Oleh,<br><strong>Direktur CDW</strong></p>
            <br><br><br>
            <strong>( Signed & Sealed )</strong>
        </div>
    </div>
</div>

</body>
</html>
