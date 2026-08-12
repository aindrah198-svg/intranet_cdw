<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji - <?= esc($karyawan['nama_lengkap']) ?></title>
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm;
        }
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 0; padding: 15px; background: #fff; color: #1e293b; }
        .slip-box { border: 1.5px solid #1e293b; padding: 20px; max-width: 680px; margin: 0 auto; border-radius: 6px; page-break-inside: avoid; }
        .header { text-align: center; border-bottom: 2px double #1e293b; padding-bottom: 10px; margin-bottom: 15px; }
        .header h2 { margin: 0; color: #1e3c72; font-size: 18px; letter-spacing: 0.5px; }
        .header p { margin: 3px 0 0; color: #64748b; font-size: 11px; }
        .header h3 { margin: 10px 0 2px; text-decoration: underline; font-size: 14px; color: #0f172a; }
        .header small { font-size: 11px; color: #475569; }
        
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 11.5px; }
        .info-table td { padding: 3px 0; vertical-align: top; }
        
        .detail-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 11.5px; }
        .detail-table th, .detail-table td { border: 1px solid #cbd5e1; padding: 6px 10px; }
        .detail-table th { background: #f1f5f9; text-align: left; font-size: 11px; text-transform: uppercase; color: #334155; }
        
        .sub-table { width: 100%; border-collapse: collapse; }
        .sub-table td { border: none !important; padding: 3px 4px !important; font-size: 11px; }
        
        .text-right { text-align: right; }
        .text-danger { color: #dc2626; }
        .text-success { color: #16a34a; }
        .text-warning { color: #b58100; }
        
        .takehome-box { background: #eef2f7; border: 1px solid #cbd5e1; padding: 8px 12px; border-radius: 4px; margin-bottom: 12px; }
        
        .footer { margin-top: 25px; display: flex; justify-content: space-between; page-break-inside: avoid; }
        .signature { text-align: center; width: 180px; }
        .signature p { margin: 0 0 45px 0; font-size: 11px; }

        @media print {
            .no-print { display: none !important; }
            body { padding: 0 !important; }
            .slip-box { border: 1px solid #000 !important; margin: 0 auto !important; box-shadow: none !important; }
        }
    </style>
    <?php if(!empty($autoPrint)): ?>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
    <?php endif; ?>
</head>
<body>

<div class="no-print" style="max-width: 680px; margin: 0 auto 15px; text-align: right;">
    <button onclick="window.print()" style="padding: 8px 18px; background: #1e3c72; color: white; border: none; border-radius: 20px; cursor: pointer; font-weight: bold; font-size: 12px;">
        <i class="fas fa-print"></i> Cetak / Simpan PDF 1 Halaman
    </button>
</div>

<?php
$gajiPokok = floatval($gaji['gaji_pokok'] ?? ($karyawan['gaji_pokok'] ?? 0));
$tunjangan = floatval($gaji['tunjangan'] ?? 0);
$bonus = floatval($gaji['bonus'] ?? 0);
$potKasbon = floatval($gaji['potongan_kasbon'] ?? 0);
$bpjsKes = floatval($gaji['potongan_bpjs_kes'] ?? 0);
$bpjsJht = floatval($gaji['potongan_bpjs_jht'] ?? 0);
$bpjsJp  = floatval($gaji['potongan_bpjs_jp'] ?? 0);
$potLain = floatval($gaji['potongan_lainnya'] ?? 0);

$totalPendapatan = $gajiPokok + $tunjangan + $bonus;
$totalPotongan = $potKasbon + $bpjsKes + $bpjsJht + $bpjsJp + $potLain;
$takeHomePay = floatval($gaji['gaji_bersih'] ?? ($totalPendapatan - $totalPotongan));
?>

<div class="slip-box">
    <div class="header">
        <h2>PT CDW ENGINEERING</h2>
        <p>Jl. Utama No. 100, Jakarta | Telp: (021) 555-0199 | Email: hr@cdwengineering.co.id</p>
        <h3>SLIP GAJI KARYAWAN</h3>
        <small>Periode: <?= !empty($gaji['bulan']) ? esc($gaji['bulan']).'/'.esc($gaji['tahun']) : date('F Y') ?></small>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%"><strong>NIK</strong></td>
            <td width="35%">: <?= esc($karyawan['nik'] ?? '-') ?></td>
            <td width="15%"><strong>Tanggal</strong></td>
            <td width="35%">: <?= esc($tanggalCetak) ?></td>
        </tr>
        <tr>
            <td><strong>Nama</strong></td>
            <td>: <?= esc($karyawan['nama_lengkap']) ?></td>
            <td><strong>Jabatan</strong></td>
            <td>: <?= esc($karyawan['jabatan'] ?? '-') ?></td>
        </tr>
        <tr>
            <td><strong>Divisi</strong></td>
            <td>: <?= esc($karyawan['divisi'] ?? 'Teknis/Operasional') ?></td>
            <td><strong>Status Pay</strong></td>
            <td>: <?= esc(ucfirst($gaji['status_pembayaran'] ?? 'Paid')) ?></td>
        </tr>
    </table>

    <table class="detail-table">
        <thead>
            <tr>
                <th width="50%">PENERIMAAN (INCOME)</th>
                <th width="50%">POTONGAN (DEDUCTION)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="vertical-align: top; padding: 4px;">
                    <table class="sub-table">
                        <tr>
                            <td>Gaji Pokok</td>
                            <td class="text-right">Rp <?= number_format($gajiPokok, 0, ',', '.') ?></td>
                        </tr>
                        <?php if($tunjangan > 0): ?>
                        <tr>
                            <td>Tunjangan Operasional</td>
                            <td class="text-right">Rp <?= number_format($tunjangan, 0, ',', '.') ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if($bonus > 0): ?>
                        <tr>
                            <td>Bonus / Insentif</td>
                            <td class="text-right">Rp <?= number_format($bonus, 0, ',', '.') ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </td>
                <td style="vertical-align: top; padding: 4px;">
                    <table class="sub-table">
                        <?php if($potKasbon > 0): ?>
                        <tr>
                            <td>Potongan Kasbon</td>
                            <td class="text-right text-danger">- Rp <?= number_format($potKasbon, 0, ',', '.') ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if($bpjsKes > 0): ?>
                        <tr>
                            <td>BPJS Kesehatan (1%)</td>
                            <td class="text-right text-danger">- Rp <?= number_format($bpjsKes, 0, ',', '.') ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if($bpjsJht > 0): ?>
                        <tr>
                            <td>BPJS TK JHT (2%)</td>
                            <td class="text-right text-danger">- Rp <?= number_format($bpjsJht, 0, ',', '.') ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if($bpjsJp > 0): ?>
                        <tr>
                            <td>BPJS TK JP (1%)</td>
                            <td class="text-right text-danger">- Rp <?= number_format($bpjsJp, 0, ',', '.') ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if($potLain > 0): ?>
                        <tr>
                            <td>Potongan Lainnya</td>
                            <td class="text-right text-danger">- Rp <?= number_format($potLain, 0, ',', '.') ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if($totalPotongan == 0): ?>
                        <tr>
                            <td colspan="2" style="text-align: center; color: #94a3b8; font-style: italic;">Tidak ada potongan</td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </td>
            </tr>
            <tr style="background: #f8fafc; font-weight: bold;">
                <td class="text-success">
                    TOTAL PENERIMAAN: Rp <?= number_format($totalPendapatan, 0, ',', '.') ?>
                </td>
                <td class="text-danger text-right">
                    TOTAL POTONGAN: - Rp <?= number_format($totalPotongan, 0, ',', '.') ?>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="takehome-box">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 13px; font-weight: bold; color: #0f172a;">TAKE HOME PAY (GAJI BERSIH DITERIMA):</span>
            <span style="font-size: 15px; font-weight: bold; color: #1e3c72;">Rp <?= number_format($takeHomePay, 0, ',', '.') ?></span>
        </div>
        <?php if(isset($sisaKasbon) && $sisaKasbon > 0): ?>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px; padding-top: 4px; border-top: 1px dashed #cbd5e1;">
                <span style="font-size: 11px; font-weight: bold; color: #b58100;">* SISA KASBON / PINJAMAN AKTIF:</span>
                <span style="font-size: 11px; font-weight: bold; color: #dc2626;">Rp <?= number_format($sisaKasbon, 0, ',', '.') ?></span>
            </div>
        <?php endif; ?>
    </div>

    <?php if(!empty($gaji['bukti_transfer'])): ?>
        <div style="margin-top: 10px; border: 1px dashed #cbd5e1; padding: 6px; border-radius: 4px; text-align: center;">
            <small style="color: #64748b; font-weight: bold; display: block; margin-bottom: 4px; font-size: 10px;">LAMPIRAN BUKTI TRANSFER PEMBAYARAN:</small>
            <?php 
            $ext = strtolower(pathinfo($gaji['bukti_transfer'], PATHINFO_EXTENSION));
            if($ext === 'pdf'): 
            ?>
                <a href="<?= base_url($gaji['bukti_transfer']) ?>" target="_blank" style="color: #0d6efd; font-weight: bold; font-size: 11px;">[ Unduh Dokumen Bukti Transfer PDF ]</a>
            <?php else: ?>
                <img src="<?= base_url($gaji['bukti_transfer']) ?>" alt="Bukti Transfer" style="max-width: 100%; max-height: 120px; border-radius: 4px; border: 1px solid #ddd;">
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="footer">
        <div class="signature">
            <p>Penerima,</p>
            <strong><?= esc($karyawan['nama_lengkap']) ?></strong>
        </div>
        <div class="signature">
            <p>Mengetahui,<br><strong>Direktur Utama</strong></p>
            <strong>( Cecep Trihardiyanto )</strong>
        </div>
    </div>
</div>

</body>
</html>
