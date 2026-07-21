<?php
// Hapus semua output buffer
ob_clean();

// Set header untuk Excel - TAMBAHKAN CHARSET
header("Content-type: application/vnd-ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=Laporan_Pengeluaran_SPK_" . date('Ymd_His') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// Data dari controller
$spk = isset($spk) ? $spk : null;
$pengeluaran = isset($pengeluaran) ? $pengeluaran : [];
$total_pengeluaran = isset($total_pengeluaran) ? $total_pengeluaran : 0;
$uang_akomodasi = isset($uang_akomodasi) ? $uang_akomodasi : 40000000;
$sisa_akomodasi = isset($sisa_akomodasi) ? $sisa_akomodasi : ($uang_akomodasi - $total_pengeluaran);
$terpakai_persen = isset($terpakai_persen) ? $terpakai_persen : ($uang_akomodasi > 0 ? round(($total_pengeluaran / $uang_akomodasi) * 100, 2) : 0);

// Hitung total per jenis
$total_bensin = 0;
$total_tol = 0;
$total_makan = 0;
$total_akomodasi = 0;
$total_material = 0;
$total_lainnya = 0;

foreach($pengeluaran as $item) {
    switch($item->jenis) {
        case 'Bensin':
            $total_bensin += $item->jumlah;
            break;
        case 'Tol':
            $total_tol += $item->jumlah;
            break;
        case 'Makan':
            $total_makan += $item->jumlah;
            break;
        case 'Akomodasi':
            $total_akomodasi += $item->jumlah;
            break;
        case 'Material Tambahan':
            $total_material += $item->jumlah;
            break;
        default:
            $total_lainnya += $item->jumlah;
    }
}

// Fungsi untuk format nomor referensi agar tidak berubah jadi scientific notation
function formatNoRef($no_ref) {
    if (empty($no_ref)) return '-';
    // Tambahkan tanda kutip di depan untuk memaksa Excel membaca sebagai teks
    return "=\"" . $no_ref . "\"";
}
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Laporan Pengeluaran SPK</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #0d6efd;
            margin: 0;
            font-size: 24px;
        }
        .header h3 {
            color: #666;
            margin: 5px 0 0;
            font-weight: normal;
            font-size: 16px;
        }
        .info-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }
        .info-section table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-section td {
            padding: 8px;
            border: 1px solid #dee2e6;
        }
        .info-section .label {
            font-weight: bold;
            width: 200px;
            background-color: #e9ecef;
        }
        .akomodasi-box {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .akomodasi-box table {
            width: 100%;
            color: white;
        }
        .akomodasi-box td {
            padding: 5px;
        }
        .akomodasi-box .label {
            font-weight: bold;
            width: 200px;
        }
        .akomodasi-box .value {
            font-size: 18px;
            font-weight: bold;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .data-table th {
            background-color: #0d6efd;
            color: white;
            padding: 10px;
            text-align: center;
            font-weight: bold;
        }
        .data-table td {
            padding: 8px;
            border: 1px solid #dee2e6;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .data-table tr:hover {
            background-color: #e9ecef;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .subtotal-row {
            background-color: #fff3cd;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .signature {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature div {
            width: 45%;
            text-align: center;
        }
        .signature-line {
            margin-top: 50px;
            border-top: 1px solid #333;
            width: 200px;
            margin-left: auto;
            margin-right: auto;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #333;
            padding: 3px 8px;
            border-radius: 3px;
        }
        .badge-danger {
            background-color: #dc3545;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
        }
        /* Style untuk teks biasa (ganti icon) */
        .status-success {
            color: #28a745;
            font-weight: bold;
        }
        .status-warning {
            color: #ffc107;
            font-weight: bold;
        }
        .status-danger {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN REKAP PENGELUARAN PROYEK</h1>
        <h3>CDW ENGINEERING</h3>
    </div>

    <!-- Informasi SPK -->
    <div class="info-section">
        <table>
            <tr>
                <td class="label">Nomor SPK</td>
                <td>: <?= $spk && isset($spk->nomor_spk) ? $spk->nomor_spk : '-' ?></td>
                <td class="label">Tanggal Mulai</td>
                <td>: <?= $spk && isset($spk->tanggal_mulai) ? date('d/m/Y', strtotime($spk->tanggal_mulai)) : '-' ?></td>
            </tr>
            <tr>
                <td class="label">Judul Pekerjaan</td>
                <td>: <?= $spk && isset($spk->judul_pekerjaan) ? $spk->judul_pekerjaan : '-' ?></td>
                <td class="label">Tanggal Selesai</td>
                <td>: <?= $spk && isset($spk->tanggal_selesai) ? date('d/m/Y', strtotime($spk->tanggal_selesai)) : '-' ?></td>
            </tr>
            <tr>
                <td class="label">Lokasi</td>
                <td>: <?= $spk && isset($spk->lokasi) ? $spk->lokasi : '-' ?></td>
                <td class="label">Status</td>
                <td>: <?= $spk && isset($spk->status) ? $spk->status : '-' ?></td>
            </tr>
            <tr>
                <td class="label">Tanggal Export</td>
                <td colspan="3">: <?= date('d/m/Y H:i:s') ?></td>
            </tr>
        </table>
    </div>

 <!-- Informasi Akomodasi (Modal Awal, Terpakai, Sisa) -->
<div class="akomodasi-box">
    <table>
        <tr>
            <td class="label">UANG AKOMODASI (MODAL AWAL)</td>
            <td class="value">: Rp <?= number_format($uang_akomodasi, 0, ',', '.') ?></td>
            <!-- HAPUS BAGIAN INI (rowspan dan span status) -->
            <!--
            <td rowspan="3" style="text-align: center; vertical-align: middle;">
                <?php if($sisa_akomodasi > 0): ?>
                    <span style="color: #28a745; font-weight: bold;">&#10004; SISA (Rp <?= number_format($sisa_akomodasi, 0, ',', '.') ?>)</span>
                <?php elseif($sisa_akomodasi == 0): ?>
                    <span style="color: #ffc107; font-weight: bold;">&#9888; PAS (Tepat)</span>
                <?php else: ?>
                    <span style="color: #dc3545; font-weight: bold;">&#10008; KEKURANGAN (Rp <?= number_format(abs($sisa_akomodasi), 0, ',', '.') ?>)</span>
                <?php endif; ?>
            </td>
            -->
        </tr>
        <tr>
            <td class="label">TOTAL PENGELUARAN TERPAKAI</td>
            <td class="value">: Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td class="label">SISA AKOMODASI</td>
            <td class="value">: Rp <?= number_format($sisa_akomodasi, 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td class="label">PERSENTASE TERPAKAI</td>
            <td colspan="2">: <?= $terpakai_persen ?>% (<?= number_format($total_pengeluaran, 0, ',', '.') ?> / <?= number_format($uang_akomodasi, 0, ',', '.') ?>)</td>
        </tr>
    </table>
</div>

    <!-- Detail Pengeluaran -->
    <h3 style="color: #0d6efd; margin-bottom: 10px;">DETAIL PENGELUARAN</h3>
    
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="10%">Tanggal</th>
                <th width="15%">No. Referensi</th>
                <th width="12%">Jenis</th>
                <th width="20%">Nama Pengeluaran</th>
                <th width="18%">Deskripsi</th>
                <th width="15%">Jumlah (Rp)</th>
                <th width="5%">Bukti</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($pengeluaran)): ?>
                <?php $no = 1; ?>
                <?php foreach($pengeluaran as $item): ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td class="text-center"><?= isset($item->tanggal) ? date('d/m/Y', strtotime($item->tanggal)) : '-' ?></td>
                    <td style="mso-number-format:\@;"><?= $item->no_ref ?? '-' ?></td>
                    <td class="text-center"><?= $item->jenis ?? '-' ?></td>
                    <td><?= $item->nama_pengeluaran ?? '-' ?></td>
                    <td><?= $item->deskripsi ?? '-' ?></td>
                    <td class="text-right"><?= number_format($item->jumlah ?? 0, 0, ',', '.') ?></td>
                    <td class="text-center">
                        <?= (!empty($item->foto_nota)) ? 'Ada' : '-' ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data pengeluaran</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Rekapitulasi per Jenis -->
    <h3 style="color: #0d6efd; margin: 20px 0 10px;">REKAPITULASI PER JENIS</h3>
    
    <table class="data-table" style="width: 60%;">
        <thead>
            <tr>
                <th>No</th>
                <th>Jenis Pengeluaran</th>
                <th>Total (Rp)</th>
                <th>Persentase</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            $jenis_data = [
                'Bensin' => $total_bensin,
                'Tol' => $total_tol,
                'Makan' => $total_makan,
                'Akomodasi' => $total_akomodasi,
                'Material Tambahan' => $total_material,
                'Lainnya' => $total_lainnya
            ];
            
            foreach($jenis_data as $jenis => $total):
                if($total > 0):
            ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td><?= $jenis ?></td>
                <td class="text-right">Rp <?= number_format($total, 0, ',', '.') ?></td>
                <td class="text-center"><?= $total_pengeluaran > 0 ? round(($total / $total_pengeluaran) * 100, 2) : 0 ?>%</td>
            </tr>
            <?php 
                endif;
            endforeach; 
            ?>
            <tr class="total-row">
                <td colspan="2" class="text-right"><strong>TOTAL</strong></td>
                <td class="text-right"><strong>Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></strong></td>
                <td class="text-center"><strong>100%</strong></td>
            </tr>
        </tbody>
    </table>

 <!-- Ringkasan Akomodasi -->
<div style="margin-top: 30px; padding: 15px; border: 2px solid #0d6efd; border-radius: 5px;">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td colspan="2" style="font-size: 16px; font-weight: bold; color: #0d6efd; padding-bottom: 10px;">
                RINGKASAN ALOKASI AKOMODASI
            </td>
        </tr>
        <tr>
            <td style="width: 250px; padding: 5px;">Uang Akomodasi (Modal Awal)</td>
            <td style="padding: 5px;">: Rp <?= number_format($uang_akomodasi, 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td style="padding: 5px;">Total Pengeluaran (Terpakai)</td>
            <td style="padding: 5px;">: Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td style="padding: 5px;">Sisa Akomodasi</td>
            <td style="padding: 5px;">: 
                <strong style="color: <?= $sisa_akomodasi > 0 ? '#28a745' : ($sisa_akomodasi == 0 ? '#ffc107' : '#dc3545') ?>;">
                    Rp <?= number_format($sisa_akomodasi, 0, ',', '.') ?>
                </strong>
            </td>
        </tr>
        <!-- UNCOMMENT BAGIAN INI (Baris Status) -->
       <tr>
    <td style="padding: 5px;">Status</td>
    <td style="padding: 5px;">: 
        <?php if($sisa_akomodasi > 0): ?>
            <span style="color: #28a745; font-weight: bold;">&#10004; SISA (Rp <?= number_format($sisa_akomodasi, 0, ',', '.') ?>)</span>
        <?php elseif($sisa_akomodasi == 0): ?>
            <span style="color: #ffc107; font-weight: bold;">&#9888; PAS (Tepat)</span>
        <?php else: ?>
            <span style="color: #dc3545; font-weight: bold;">&#10008; KEKURANGAN (Rp <?= number_format(abs($sisa_akomodasi), 0, ',', '.') ?>)</span>
        <?php endif; ?>
    </td>
</tr>
    </table>
</div>

    <!-- Signature -->
    <div class="signature">
        <div>
            <div>Mengetahui,</div>
            <div class="signature-line"></div>
            <div><strong>Direktur</strong></div>
            <div style="margin-top: 5px; font-size: 11px;">( _________________ )</div>
        </div>
        <div>
            <div>Hormat Kami,</div>
            <div class="signature-line"></div>
            <div><strong>Teknisi</strong></div>
            <div style="margin-top: 5px; font-size: 11px;">( _________________ )</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div>Dokumen ini digenerate secara otomatis dari sistem pada <?= date('d/m/Y H:i:s') ?></div>
        <div>CDW Engineering - Sistem Manajemen Proyek</div>
    </div>
</body>
</html>