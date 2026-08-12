<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Purchase Requisition - <?= esc($pr['nomor_pr']) ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; margin: 0; padding: 20px; background: #fff; }
        .box { border: 2px solid #333; padding: 25px; max-width: 900px; margin: 0 auto; border-radius: 8px; }
        .header { text-align: center; border-bottom: 2px double #333; padding-bottom: 15px; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #1e3c72; }
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
    <button onclick="window.print()" style="padding: 10px 20px; background: #1e3c72; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">
        Cetak / Simpan PDF
    </button>
</div>

<div class="box">
    <div class="header">
        <h2>PT CDW ENGINEERING</h2>
        <p>Jl. Utama No. 100, Jakarta | Telp: (021) 555-0199 | Email: procurement@cdwengineering.co.id</p>
        <h3 style="margin-top: 15px; margin-bottom: 0; text-decoration: underline;">PURCHASE REQUISITION (FORM PEMBELIAN)</h3>
        <small>Nomor PR: <?= esc($pr['nomor_pr']) ?> | Tanggal: <?= date('d F Y', strtotime($pr['tanggal_pengajuan'])) ?></small>
    </div>

    <table style="width: 100%; margin-bottom: 20px; font-size: 13px;">
        <tr>
            <td style="width: 15%;"><strong>Pemohon</strong></td>
            <td style="width: 35%;">: <?= esc($pr['nama_lengkap'] ?? 'Admin Panel') ?> (<?= esc($pr['departemen'] ?? '-') ?>)</td>
            <td style="width: 15%;"><strong>Prioritas</strong></td>
            <td style="width: 35%;">: <?= esc($pr['prioritas'] ?: 'Normal') ?></td>
        </tr>
        <tr>
            <td><strong>Supplier</strong></td>
            <td>: <?= esc($pr['supplier'] ?: '-') ?></td>
            <td><strong>Tipe Pembelian</strong></td>
            <td>: <?= esc($pr['tipe_pembelian'] ?: 'Online') ?></td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang / Spesifikasi</th>
                <th style="text-align: center;">Jumlah</th>
                <th class="text-right">Harga Satuan Est. (Rp)</th>
                <th class="text-right">Subtotal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pr['items'])): ?>
                <tr><td colspan="5" style="text-align: center; color: #888;">Belum ada item barang yang dicatat.</td></tr>
            <?php else: ?>
                <?php $no=1; foreach($pr['items'] as $item): ?>
                    <?php
                        $qty = $item['jumlah'] ?? $item['qty'] ?? 1;
                        $harga = $item['harga_satuan'] ?? $item['harga_estimasi'] ?? 0;
                        $subtotal = $item['subtotal'] ?? ($qty * $harga);
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><strong><?= esc($item['nama_barang']) ?></strong></td>
                        <td style="text-align: center;"><?= esc($qty) ?> <?= esc($item['satuan'] ?? 'Pcs') ?></td>
                        <td class="text-right">Rp <?= number_format($harga, 0, ',', '.') ?></td>
                        <td class="text-right">Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background: #f8f9fa;">
                <td colspan="4" style="text-align: right;">Total Estimasi Keseluruhan:</td>
                <td class="text-right" style="color: #1e3c72;">Rp <?= number_format($pr['total_estimasi'], 0, ',', '.') ?></td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 20px;">
        <strong>Alasan Pembelian / Peruntukan:</strong>
        <p style="background: #f9f9f9; padding: 10px; border-radius: 5px; margin-top: 5px; border: 1px solid #eee;">
            <?= nl2br(esc($pr['alasan_pembelian'] ?: '-')) ?>
        </p>
    </div>

    <div class="footer">
        <div class="signature">
            <p>Admin / Pemohon,</p>
            <br><br><br>
            <strong>( Admin Department )</strong>
        </div>
        <div class="signature">
            <p>Disetujui Oleh,<br><strong>Direktur Utama</strong></p>
            <br><br><br>
            <strong>( Signed & Sealed )</strong>
        </div>
    </div>
</div>

</body>
</html>
