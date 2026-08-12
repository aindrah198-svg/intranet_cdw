<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 13px; color: #333; margin: 20px; }
        .header { text-align: center; border-bottom: 2px solid #2563eb; padding-bottom: 15px; margin-bottom: 20px; }
        .company-title { font-size: 18px; font-weight: bold; color: #1e293b; }
        .doc-title { font-size: 14px; font-weight: bold; color: #2563eb; margin-top: 5px; text-transform: uppercase; }
        .grid { display: table; width: 100%; margin-bottom: 15px; }
        .row { display: table-row; }
        .cell { display: table-cell; padding: 4px 8px; }
        .fw-bold { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #cbd5e1; padding: 8px 12px; text-align: left; }
        th { background-color: #f1f5f9; }
        .text-right { text-align: right; }
        .total-row { background-color: #e2e8f0; font-weight: bold; font-size: 14px; }
        .footer-sign { margin-top: 40px; width: 100%; display: table; }
        .sign-box { display: table-cell; text-align: center; width: 50%; }
        @media print { button { display: none; } }
    </style>
</head>
<body>
    <button onclick="window.print()" style="padding: 8px 16px; background: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer; margin-bottom: 15px;">🖨️ Cetak / Save PDF</button>

    <div class="header">
        <div class="company-title">PT CDW ENGINEERING</div>
        <div>Jl. Raya Industri CDW No. 88, Indonesia</div>
        <div class="doc-title">SLIP GAJI KARYAWAN PERIODE <?= esc($slip['periode_bulan'] ?? '') ?> / <?= esc($slip['periode_tahun'] ?? '') ?></div>
    </div>

    <div class="grid">
        <div class="row">
            <div class="cell fw-bold">NIK Karyawan</div>
            <div class="cell">: <?= esc($slip['nik'] ?? '-') ?></div>
            <div class="cell fw-bold">Jabatan</div>
            <div class="cell">: <?= esc($slip['jabatan'] ?? 'Staff') ?></div>
        </div>
        <div class="row">
            <div class="cell fw-bold">Nama Karyawan</div>
            <div class="cell">: <?= esc($slip['nama_lengkap'] ?? '-') ?></div>
            <div class="cell fw-bold">Divisi</div>
            <div class="cell">: <?= esc($slip['divisi'] ?? 'General') ?></div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Komponen Penggajian</th>
                <th class="text-right">Penerimaan (Rp)</th>
                <th class="text-right">Potongan (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Gaji Pokok</td>
                <td class="text-right"><?= number_format($slip['gaji_pokok'] ?? 0, 0, ',', '.') ?></td>
                <td class="text-right">-</td>
            </tr>
            <tr>
                <td>Tunjangan Jabatan & Operasional / Bonus</td>
                <td class="text-right"><?= number_format(($slip['total_penghasilan'] ?? 0) - ($slip['gaji_pokok'] ?? 0), 0, ',', '.') ?></td>
                <td class="text-right">-</td>
            </tr>
            <tr>
                <td>Potongan BPJS & Absensi / Kasbon / Pajak</td>
                <td class="text-right">-</td>
                <td class="text-right"><?= number_format($slip['total_potongan'] ?? 0, 0, ',', '.') ?></td>
            </tr>
            <tr class="total-row">
                <td>TOTAL DITERIMA (TAKE HOME PAY)</td>
                <td colspan="2" class="text-right" style="color: #2563eb;">Rp <?= number_format($slip['gaji_bersih'] ?? 0, 0, ',', '.') ?></td>
            </tr>
        </tbody>
    </table>

    <div class="footer-sign">
        <div class="sign-box">
            <p>Penerima,</p>
            <br><br><br>
            <p class="fw-bold">( <?= esc($slip['nama_lengkap'] ?? 'Karyawan') ?> )</p>
        </div>
        <div class="sign-box">
            <p>HRD / Finance Manager,</p>
            <br><br><br>
            <p class="fw-bold">( HRD PT CDW Engineering )</p>
        </div>
    </div>
</body>
</html>
