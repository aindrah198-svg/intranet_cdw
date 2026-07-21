<?php
// app/Views/direktur/approval/surat_jalan_print.php

$suratJalan = $suratJalan ?? [];
$items = $items ?? [];

// Helper function untuk format tanggal
if (!function_exists('formatDate')) {
    function formatDate($date) {
        if (empty($date)) return '-';
        $timestamp = strtotime($date);
        return $timestamp ? date('d/m/Y', $timestamp) : '-';
    }
}

// Helper function untuk format tanggal Indonesia
if (!function_exists('formatDateIndo')) {
    function formatDateIndo($date) {
        if (empty($date)) return '-';
        $timestamp = strtotime($date);
        if (!$timestamp) return '-';
        
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        $tgl = date('d', $timestamp);
        $bln = $bulan[(int)date('m', $timestamp)];
        $thn = date('Y', $timestamp);
        
        return $tgl . ' ' . $bln . ' ' . $thn;
    }
}

// Helper function untuk format datetime
if (!function_exists('formatDateTime')) {
    function formatDateTime($datetime) {
        if (empty($datetime)) return '-';
        $timestamp = strtotime($datetime);
        return $timestamp ? date('d/m/Y H:i', $timestamp) : '-';
    }
}

// Helper function untuk status text
if (!function_exists('getStatusText')) {
    function getStatusText($status) {
        $map = [
            'diproses' => 'DIPROSES',
            'dikirim' => 'DIKIRIM',
            'diterima' => 'DITERIMA',
            'dibatalkan' => 'DIBATALKAN'
        ];
        return $map[$status] ?? strtoupper($status);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Jalan - <?= htmlspecialchars($suratJalan['nomor_surat_jalan'] ?? 'Surat Jalan') ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            background: white;
            padding: 20px;
        }
        
        .print-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
        }
        
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .header h1 {
            font-size: 16pt;
            margin-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .header h2 {
            font-size: 12pt;
            margin-bottom: 3px;
            font-weight: normal;
        }
        
        .header .subtitle {
            font-size: 9pt;
            color: #666;
        }
        
        .title {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .title h3 {
            font-size: 14pt;
            text-decoration: underline;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        
        .surat-info {
            margin-bottom: 20px;
            text-align: right;
            font-size: 10pt;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .info-table tr td {
            padding: 6px 5px;
            vertical-align: top;
        }
        
        .info-table td:first-child {
            width: 150px;
            font-weight: bold;
        }
        
        .section {
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 10px;
            padding-bottom: 3px;
            border-bottom: 1px solid #333;
            text-transform: uppercase;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .items-table th {
            background: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }
        
        .items-table td.center {
            text-align: center;
        }
        
        .signature {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            text-align: center;
            width: 220px;
        }
        
        .signature-line {
            margin-top: 50px;
            margin-bottom: 10px;
            padding-top: 10px;
            border-top: 1px solid #333;
        }
        
        .signature-name {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .signature-title {
            font-size: 9pt;
            color: #666;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 8pt;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            .no-print {
                display: none;
            }
            .signature-line {
                border-top: 1px solid #000 !important;
            }
        }
        
        .print-btn {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
            margin-bottom: 20px;
            cursor: pointer;
            border: none;
            font-size: 11pt;
        }
        
        .print-btn:hover {
            background: #0056b3;
        }
        
        .back-btn {
            background: #6c757d;
        }
        
        .back-btn:hover {
            background: #5a6268;
        }
    </style>
</head>
<body>
    <div class="print-container">
        <!-- Tombol Aksi (hanya tampil di layar, tidak saat print) -->
        <div class="no-print" style="margin-bottom: 20px; text-align: center;">
            <button onclick="window.print();" class="print-btn">
                <i class="fas fa-print"></i> Cetak / Simpan PDF
            </button>
            <button onclick="window.close();" class="print-btn back-btn" style="margin-left: 10px;">
                <i class="fas fa-times"></i> Tutup
            </button>
        </div>

        <!-- Header Perusahaan -->
        <div class="header">
            <h1>PT. CIPTA DUTA WACANA</h1>
            <h2>CDW ENGINEERING</h2>
            <div class="subtitle">
                Beltway Office Park Tower B Lantai 5, Jl. TB Simatupang No. 41<br>
                Ragunan - Pasar Minggu, Jakarta Selatan 12550<br>
                Telp: (021) 29857462 | Email: info@cdw-engineering.com
            </div>
        </div>

        <!-- Title -->
        <div class="title">
            <h3>SURAT JALAN</h3>
            <p>No. <?= htmlspecialchars($suratJalan['nomor_surat_jalan'] ?? '-') ?></p>
        </div>

        <!-- Surat Info -->
        <div class="surat-info">
            <?= htmlspecialchars($suratJalan['kode_format'] ?? 'DN-CDW') ?>/<?= htmlspecialchars($suratJalan['bulan_format'] ?? date('m')) ?>/<?= htmlspecialchars($suratJalan['tahun_format'] ?? date('Y')) ?>
        </div>

        <!-- Informasi Penerima -->
        <div class="section">
            <div class="section-title">KEPADA YTH.</div>
            <table class="info-table">
                <tr>
                    <td width="150">Perusahaan</td>
                    <td>: <?= htmlspecialchars($suratJalan['penerima_perusahaan'] ?? $suratJalan['client_nama'] ?? '-') ?></td>
                </tr>
                <?php if (!empty($suratJalan['penerima_up'])): ?>
                <tr>
                    <td>UP / Contact Person</td>
                    <td>: <?= htmlspecialchars($suratJalan['penerima_up']) ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td>Alamat</td>
                    <td>: <?= nl2br(htmlspecialchars($suratJalan['alamat_pengiriman'] ?? '-')) ?></td>
                </tr>
                <?php if (!empty($suratJalan['penerima_telepon'])): ?>
                <tr>
                    <td>Telepon</td>
                    <td>: <?= htmlspecialchars($suratJalan['penerima_telepon']) ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>

        <!-- Informasi Pengiriman -->
        <div class="section">
            <div class="section-title">INFORMASI PENGIRIMAN</div>
            <table class="info-table">
                <tr>
                    <td width="150">Tanggal Kirim</td>
                    <td>: <?= formatDateIndo($suratJalan['tanggal_kirim'] ?? '') ?></td>
                </tr>
                <?php if (!empty($suratJalan['lokasi_proyek'])): ?>
                <tr>
                    <td>Lokasi Proyek</td>
                    <td>: <?= htmlspecialchars($suratJalan['lokasi_proyek']) ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($suratJalan['project_id'])): ?>
                <tr>
                    <td>Project</td>
                    <td>: <?= htmlspecialchars($suratJalan['nama_project'] ?? '-') ?> (<?= htmlspecialchars($suratJalan['kode_project'] ?? '-') ?>)</td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($suratJalan['nomor_invoice'])): ?>
                <tr>
                    <td>No. Invoice</td>
                    <td>: <?= htmlspecialchars($suratJalan['nomor_invoice']) ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>

        <!-- Daftar Barang -->
        <div class="section">
            <div class="section-title">DAFTAR BARANG</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Barang</th>
                        <th width="100">Jumlah</th>
                        <th width="80">Satuan</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="5" class="center">Tidak ada item</td>
                    </tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td class="center"><?= $no++ ?></td>
                            <td><?= htmlspecialchars($item['nama_barang']) ?></td>
                            <td class="center"><?= number_format($item['qty'], 2) ?></td>
                            <td class="center"><?= htmlspecialchars($item['satuan'] ?? 'pcs') ?></td>
                            <td><?= htmlspecialchars($item['keterangan'] ?? '-') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php if (!empty($suratJalan['catatan_barang'])): ?>
            <div class="mt-2" style="font-size: 10pt;">
                <em>Catatan: <?= nl2br(htmlspecialchars($suratJalan['catatan_barang'])) ?></em>
            </div>
            <?php endif; ?>
        </div>

        <!-- Informasi Kurir -->
        <div class="section">
            <div class="section-title">INFORMASI KURIR</div>
            <table class="info-table">
                <tr>
                    <td width="150">Nama Sopir</td>
                    <td>: <?= htmlspecialchars($suratJalan['sopir'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td>No. Kendaraan</td>
                    <td>: <?= htmlspecialchars($suratJalan['no_kendaraan'] ?? '-') ?></td>
                </tr>
            </table>
        </div>

        <!-- Status -->
        <div class="section">
            <div class="section-title">STATUS</div>
            <table class="info-table">
                <tr>
                    <td width="150">Status Pengiriman</td>
                    <td>: <strong><?= getStatusText($suratJalan['status'] ?? 'diproses') ?></strong></td>
                </tr>
                <?php if (($suratJalan['status_terima'] ?? '') === 'diterima'): ?>
                <tr>
                    <td>Status Penerimaan</td>
                    <td>: <strong>DITERIMA</strong><br>
                        <small>Oleh: <?= htmlspecialchars($suratJalan['diterima_oleh'] ?? '-') ?></small><br>
                        <small>Tanggal: <?= formatDateTime($suratJalan['tanggal_terima'] ?? '') ?></small>
                    </td>
                </tr>
                <?php endif; ?>
            </table>
        </div>

        <!-- Tanda Tangan -->
        <div class="signature">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-name">Disiapkan Oleh,</div>
                <div class="signature-title">(<?= htmlspecialchars($suratJalan['disiapkan_oleh'] ?? '_________________') ?>)</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-name">Dikirim Oleh,</div>
                <div class="signature-title">(<?= htmlspecialchars($suratJalan['dikirim_oleh'] ?? '_________________') ?>)</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-name">Penerima,</div>
                <div class="signature-title">(<?= htmlspecialchars($suratJalan['penerima_nama'] ?? '_________________') ?>)</div>
                <div class="signature-title" style="margin-top: 5px;"><?= htmlspecialchars($suratJalan['penerima_perusahaan'] ?? '') ?></div>
            </div>
        </div>

        <!-- Catatan -->
        <?php if (!empty($suratJalan['keterangan'])): ?>
        <div class="section">
            <div class="section-title">CATATAN</div>
            <div class="bg-light p-2 rounded" style="font-size: 10pt;">
                <?= nl2br(htmlspecialchars($suratJalan['keterangan'])) ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Footer -->
        <div class="footer">
            Dokumen ini dicetak pada: <?= date('d/m/Y H:i:s') ?><br>
            Surat Jalan ini adalah dokumen resmi PT. Cipta Duta Wacana (CDW Engineering)
        </div>
    </div>

    <script>
        // Auto print jika diperlukan (opsional, bisa diaktifkan)
        // setTimeout(function() { window.print(); }, 500);
    </script>
</body>
</html>