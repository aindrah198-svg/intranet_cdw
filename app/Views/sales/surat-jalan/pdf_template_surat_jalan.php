<?php
/**
 * Template PDF Surat Jalan
 * Format Manual dengan Logo Perusahaan
 * Khusus untuk export PDF menggunakan DomPDF
 */

// Helper functions
function formatDate($date, $format = 'd-M-y') {
    if (empty($date) || $date == '0000-00-00') return '-';
    return date($format, strtotime($date));
}

function formatNumber($number, $decimals = 0) {
    if (empty($number)) return '-';
    return number_format($number, $decimals, ',', '.');
}

// Data dari controller
$suratJalan = $suratJalan ?? [];
$items = $items ?? [];

// Calculate totals
$totalQty = 0;
$totalBerat = 0;
foreach ($items as $item) {
    $totalQty += $item['qty'];
    $totalBerat += $item['berat'];
}

// Default company data jika tidak ada dari controller
$perusahaan = [
    'nama_perusahaan' => 'PT. Cipta Duta Wacana',
    'alamat' => 'Villa Bintaro Regency, Jl. Riau Blok K1 No. 2, Pondok Kacang Timur, Tangerang Selatan 15226',
    'website' => 'www.cdw-engineering.com'
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SURAT JALAN - <?= htmlspecialchars($suratJalan['nomor_surat_jalan']) ?></title>
    <style>
        /* Reset dan Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        
        .container {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            padding: 15mm;
        }
        
        /* Header Styles */
        .header {
            margin-bottom: 10px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
        }
        
        .company-info {
            display: flex;
            align-items: flex-start;
            margin-bottom: 8px;
        }
        
        .logo {
            flex: 0 0 80px;
            margin-right: 15px;
        }
        
        .logo img {
            max-width: 80px;
            height: auto;
        }
        
        .company-details {
            flex: 1;
        }
        
        .company-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 4px;
            color: #333;
        }
        
        .company-address {
            font-size: 10px;
            line-height: 1.3;
            margin-bottom: 2px;
            color: #555;
        }
        
        .company-contact {
            font-size: 10px;
            color: #555;
        }
        
        /* Title Styles */
        .document-title {
            text-align: center;
            margin: 15px 0;
            padding: 8px 0;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
        }
        
        .document-title h1 {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
        }
        
        /* Info Section */
        .info-section {
            margin-bottom: 12px;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 12px;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-cell {
            display: table-cell;
            padding: 3px 0;
            vertical-align: top;
        }
        
        .info-label {
            font-weight: bold;
            width: 120px;
        }
        
        .info-value {
            padding-left: 10px;
        }
        
        /* Receiver Section */
        .receiver-section {
            margin-bottom: 12px;
            padding: 8px;
            background-color: #f5f5f5;
            border-left: 4px solid #333;
        }
        
        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
        }
        
        .receiver-info {
            line-height: 1.5;
        }
        
        .receiver-company {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 4px;
        }
        
        .receiver-up {
            margin-bottom: 4px;
        }
        
        .receiver-address {
            margin-bottom: 4px;
            white-space: pre-line;
        }
        
        /* Items Section */
        .items-section {
            margin-bottom: 15px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 10px;
        }
        
        .items-table th {
            background-color: #f2f2f2;
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            font-weight: bold;
        }
        
        .items-table td {
            border: 1px solid #000;
            padding: 6px;
        }
        
        .items-table .number {
            text-align: center;
            width: 5%;
        }
        
        .items-table .item-name {
            width: 40%;
        }
        
        .items-table .quantity {
            text-align: center;
            width: 10%;
        }
        
        .items-table .unit {
            text-align: center;
            width: 10%;
        }
        
        .items-table .weight {
            text-align: center;
            width: 10%;
        }
        
        .items-table .note {
            width: 25%;
        }
        
        .total-row {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        
        .total-row td {
            text-align: center;
        }
        
        .total-row .label {
            text-align: right;
            padding-right: 15px;
        }
        
        /* Notes Section */
        .notes-section {
            margin-bottom: 15px;
            padding: 8px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            min-height: 60px;
        }
        
        .notes-title {
            font-weight: bold;
            margin-bottom: 4px;
            font-size: 11px;
        }
        
        .notes-content {
            white-space: pre-line;
            line-height: 1.4;
            font-size: 10px;
        }
        
        /* Delivery Info */
        .delivery-info {
            margin-bottom: 15px;
            padding: 8px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }
        
        .delivery-grid {
            display: table;
            width: 100%;
        }
        
        .delivery-row {
            display: table-row;
        }
        
        .delivery-cell {
            display: table-cell;
            padding: 3px 0;
            vertical-align: top;
        }
        
        /* Signature Section */
        .signature-section {
            margin-top: 30px;
        }
        
        .signature-grid {
            display: table;
            width: 100%;
            text-align: center;
        }
        
        .signature-row {
            display: table-row;
        }
        
        .signature-cell {
            display: table-cell;
            padding: 0 10px;
            vertical-align: top;
        }
        
        .signature-box {
            padding-top: 50px;
            position: relative;
            min-height: 120px;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            width: 80%;
            margin: 0 auto;
            position: absolute;
            top: 30px;
            left: 10%;
        }
        
        .signature-title {
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 11px;
        }
        
        .signature-name {
            margin-bottom: 4px;
            font-weight: bold;
            min-height: 18px;
            font-size: 11px;
        }
        
        .signature-contact {
            font-size: 10px;
            color: #555;
            margin-bottom: 4px;
            min-height: 14px;
        }
        
        .signature-company {
            font-size: 10px;
            color: #333;
            font-weight: bold;
            min-height: 14px;
        }
        
        /* Footer */
        .footer {
            margin-top: 25px;
            padding-top: 8px;
            border-top: 1px solid #ddd;
            font-size: 9px;
            color: #666;
            text-align: center;
        }
        
        /* Page break */
        .page-break {
            page-break-before: always;
        }
        
        /* Utility classes */
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-bold {
            font-weight: bold;
        }
        
        .mb-1 {
            margin-bottom: 5px;
        }
        
        .mb-2 {
            margin-bottom: 10px;
        }
        
        .mt-1 {
            margin-top: 5px;
        }
        
        .mt-2 {
            margin-top: 10px;
        }
        
        /* For DomPDF compatibility */
        .avoid-break {
            page-break-inside: avoid;
        }
        
        .keep-together {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header dengan Logo Perusahaan -->
        <div class="header">
            <div class="company-info">
                <div class="company-details">
                    <div class="company-name">
                        <?= htmlspecialchars($perusahaan['nama_perusahaan']) ?>
                    </div>
                    <div class="company-address">
                        <?= nl2br(htmlspecialchars($perusahaan['alamat'])) ?>
                    </div>
                    <div class="company-contact">
                        Website: <?= htmlspecialchars($perusahaan['website']) ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Judul Dokumen -->
        <div class="document-title">
            <h1>SURAT JALAN / DELIVERY NOTE</h1>
        </div>
        
        <!-- Informasi Surat Jalan -->
        <div class="info-section">
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-cell info-label">Nomor</div>
                    <div class="info-cell info-value">: <?= htmlspecialchars($suratJalan['nomor_surat_jalan']) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-cell info-label">Tanggal</div>
                    <div class="info-cell info-value">: <?= formatDate($suratJalan['tanggal_kirim']) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-cell info-label">Project</div>
                    <div class="info-cell info-value">: <?= htmlspecialchars($suratJalan['kode_project'] ?? '') ?> - <?= htmlspecialchars($suratJalan['nama_project'] ?? '') ?></div>
                </div>
                <?php if (!empty($suratJalan['nomor_invoice'])): ?>
                <div class="info-row">
                    <div class="info-cell info-label">Invoice No</div>
                    <div class="info-cell info-value">: <?= htmlspecialchars($suratJalan['nomor_invoice']) ?></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($suratJalan['lokasi_proyek'])): ?>
                <div class="info-row">
                    <div class="info-cell info-label">Lokasi Proyek</div>
                    <div class="info-cell info-value">: <?= htmlspecialchars($suratJalan['lokasi_proyek']) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Penerima -->
        <div class="receiver-section">
            <div class="section-title">Dikirimkan kepada:</div>
            <div class="receiver-info">
                <div class="receiver-company">
                    <?= htmlspecialchars($suratJalan['penerima_perusahaan'] ?? $suratJalan['nama_perusahaan'] ?? '') ?>
                </div>
                <div class="receiver-up">
                    UP: <?= htmlspecialchars($suratJalan['penerima_up'] ?? $suratJalan['penerima'] ?? '') ?>
                    <?php if (!empty($suratJalan['penerima_telepon'])): ?>
                        | Telp: <?= htmlspecialchars($suratJalan['penerima_telepon']) ?>
                    <?php endif; ?>
                </div>
                <div class="receiver-address">
                    <?= nl2br(htmlspecialchars($suratJalan['alamat_pengiriman'] ?? '')) ?>
                </div>
            </div>
        </div>
        
        <!-- Catatan Barang (Naratif) -->
        <?php if (!empty($suratJalan['catatan_barang'])): ?>
        <div class="notes-section avoid-break">
            <div class="notes-title">Deskripsi Pengiriman:</div>
            <div class="notes-content"><?= nl2br(htmlspecialchars($suratJalan['catatan_barang'])) ?></div>
        </div>
        <?php endif; ?>
        
        <!-- Barang yang Dikirim -->
        <div class="items-section keep-together">
            <div class="section-title">Detail Barang:</div>
            
            <?php if (!empty($items)): ?>
            <table class="items-table">
                <thead>
                    <tr>
                        <th class="number">No.</th>
                        <th class="item-name">Nama Barang</th>
                        <th class="quantity">Qty</th>
                        <th class="unit">Satuan</th>
                        <th class="weight">Berat</th>
                        <th class="note">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td class="number"><?= $no++ ?></td>
                        <td class="item-name"><?= htmlspecialchars($item['nama_barang']) ?></td>
                        <td class="quantity"><?= formatNumber($item['qty']) ?></td>
                        <td class="unit"><?= htmlspecialchars($item['satuan']) ?></td>
                        <td class="weight">
                            <?php if ($item['berat'] > 0): ?>
                                <?= formatNumber($item['berat'], 2) ?> <?= htmlspecialchars($item['satuan_berat'] ?? 'kg') ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="note"><?= htmlspecialchars($item['keterangan'] ?? '') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <!-- Total Row -->
                    <tr class="total-row">
                        <td colspan="2" class="label">TOTAL</td>
                        <td class="quantity"><?= formatNumber($totalQty) ?></td>
                        <td class="unit">-</td>
                        <td class="weight">
                            <?php if ($totalBerat > 0): ?>
                                <?= formatNumber($totalBerat, 2) ?> kg
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="note"></td>
                    </tr>
                </tbody>
            </table>
            <?php else: ?>
            <div style="text-align: center; padding: 15px; border: 1px dashed #ccc; margin-top: 8px;">
                Tidak ada barang dalam surat jalan ini
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Informasi Pengiriman -->
        <div class="delivery-info">
            <div class="section-title mb-1">Informasi Pengiriman:</div>
            <div class="delivery-grid">
                <?php if (!empty($suratJalan['sopir'])): ?>
                <div class="delivery-row">
                    <div class="delivery-cell" style="width: 100px;"><strong>Sopir:</strong></div>
                    <div class="delivery-cell"><?= htmlspecialchars($suratJalan['sopir']) ?></div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($suratJalan['sopir_telepon'])): ?>
                <div class="delivery-row">
                    <div class="delivery-cell" style="width: 100px;"><strong>Telp Sopir:</strong></div>
                    <div class="delivery-cell"><?= htmlspecialchars($suratJalan['sopir_telepon']) ?></div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($suratJalan['no_kendaraan'])): ?>
                <div class="delivery-row">
                    <div class="delivery-cell" style="width: 100px;"><strong>No. Kendaraan:</strong></div>
                    <div class="delivery-cell"><?= htmlspecialchars($suratJalan['no_kendaraan']) ?></div>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($suratJalan['keterangan'])): ?>
            <div class="mt-1">
                <div class="section-title mb-1">Keterangan:</div>
                <div class="notes-content"><?= nl2br(htmlspecialchars($suratJalan['keterangan'])) ?></div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Tanda Tangan -->
        <div class="signature-section keep-together">
            <div class="signature-grid">
                <div class="signature-row">
                    <!-- Disiapkan Oleh -->
                    <div class="signature-cell">
                        <div class="signature-box">
                            <div class="signature-line"></div>
                            <div class="signature-title">Disiapkan oleh,</div>
                            <div class="signature-name">
                                <?= htmlspecialchars($suratJalan['disiapkan_oleh'] ?? '') ?>
                            </div>
                            <div class="signature-contact">
                                <?php if (!empty($suratJalan['disiapkan_telepon'])): ?>
                                    <?= htmlspecialchars($suratJalan['disiapkan_telepon']) ?>
                                <?php endif; ?>
                            </div>
                            <div class="signature-company">
                                <?= htmlspecialchars($perusahaan['nama_perusahaan']) ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Dikirim Oleh -->
                    <div class="signature-cell">
                        <div class="signature-box">
                            <div class="signature-line"></div>
                            <div class="signature-title">Dikirim oleh,</div>
                            <div class="signature-name">
                                <?= htmlspecialchars($suratJalan['dikirim_oleh'] ?? $suratJalan['sopir'] ?? '') ?>
                            </div>
                            <div class="signature-contact">
                                <?php if (!empty($suratJalan['dikirim_telepon'])): ?>
                                    <?= htmlspecialchars($suratJalan['dikirim_telepon']) ?>
                                <?php endif; ?>
                            </div>
                            <div class="signature-company">
                                Supir
                            </div>
                        </div>
                    </div>
                    
                    <!-- Diterima Oleh -->
                    <div class="signature-cell">
                        <div class="signature-box">
                            <div class="signature-line"></div>
                            <div class="signature-title">Diterima oleh,</div>
                            <div class="signature-name">
                                <?= htmlspecialchars($suratJalan['diterima_oleh'] ?? $suratJalan['penerima_up'] ?? $suratJalan['penerima'] ?? '') ?>
                            </div>
                            <div class="signature-contact">
                                <?php if (!empty($suratJalan['diterima_telepon'])): ?>
                                    <?= htmlspecialchars($suratJalan['diterima_telepon']) ?>
                                <?php endif; ?>
                            </div>
                            <div class="signature-company">
                                <?= htmlspecialchars($suratJalan['diterima_perusahaan'] ?? $suratJalan['penerima_perusahaan'] ?? '') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>
                Surat Jalan ini dicetak secara otomatis dari sistem<br>
                <?= htmlspecialchars($perusahaan['nama_perusahaan']) ?> - 
                <?= date('d F Y H:i:s') ?>
            </p>
        </div>
    </div>
</body>
</html>