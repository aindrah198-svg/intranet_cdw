<?php
/**
 * Template Cetak Surat Jalan
 * Format Manual dengan Logo Perusahaan
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
$suratJalan = $surat_jalan ?? [];
$items = $items ?? [];

// Generate Base64 untuk logo
$logoPath = FCPATH . 'public/assets/img/logo/logo_cdw.jpg';
if (file_exists($logoPath)) {
    $logoData = base64_encode(file_get_contents($logoPath));
    $logoBase64 = 'data:image/jpeg;base64,' . $logoData;
} else {
    $logoBase64 = null;
}

// Data perusahaan
$perusahaan = [
    'nama_perusahaan' => 'PT. Cipta Duta Wacana',
    'alamat' => 'Villa Bintaro Regency, Jl. Riau Blok K1 No. 2<br>Pondok Kacang Timur<br>Tangerang Selatan 15226',
    'website' => 'www.cdw-engineering.com'
];

// Calculate totals
$totalQty = 0;
$totalBerat = 0;
foreach ($items as $item) {
    $totalQty += $item['qty'];
    $totalBerat += $item['berat'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SURAT JALAN - <?= htmlspecialchars($suratJalan['nomor_surat_jalan']) ?></title>
    <style>
        /* Reset dan Base Styles */
        @page {
            size: A4;
            margin: 15mm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        
        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 0;
        }
        
        /* Header Styles */
        .header {
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        
        .company-info {
            display: flex;
            align-items: flex-start;
            margin-bottom: 10px;
        }
        
        .logo {
            flex: 0 0 100px;
            margin-right: 20px;
        }
        
        .logo img {
            width: 100px;
            height: 100px;
            object-fit: contain;
        }
        
        .company-details {
            flex: 1;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }
        
        .company-address {
            font-size: 11px;
            line-height: 1.3;
            margin-bottom: 3px;
            color: #555;
        }
        
        .company-contact {
            font-size: 11px;
            color: #555;
        }
        
        /* Title Styles */
        .document-title {
            text-align: center;
            margin: 20px 0;
            padding: 10px 0;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
        }
        
        .document-title h1 {
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
        }
        
        /* Info Section */
        .info-section {
            margin-bottom: 15px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 15px;
        }
        
        .info-item {
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        
        .info-value {
            display: inline-block;
        }
        
        /* Receiver Section */
        .receiver-section {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f5f5f5;
            border-left: 4px solid #333;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
        
        .receiver-info {
            line-height: 1.5;
        }
        
        .receiver-company {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .receiver-up {
            margin-bottom: 5px;
        }
        
        .receiver-address {
            margin-bottom: 5px;
            white-space: pre-line;
        }
        
        /* Items Section */
        .items-section {
            margin-bottom: 20px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .items-table th {
            background-color: #f2f2f2;
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
        }
        
        .items-table td {
            border: 1px solid #000;
            padding: 8px;
            font-size: 11px;
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
            padding-right: 20px;
        }
        
        /* Notes Section */
        .notes-section {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            min-height: 80px;
        }
        
        .notes-title {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 13px;
        }
        
        .notes-content {
            white-space: pre-line;
            line-height: 1.4;
        }
        
        /* Delivery Info */
        .delivery-info {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }
        
        .delivery-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .delivery-item {
            margin-bottom: 8px;
        }
        
        /* Signature Section */
        .signature-section {
            margin-top: 40px;
        }
        
        .signature-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            text-align: center;
        }
        
        .signature-box {
            padding-top: 60px;
            position: relative;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            width: 80%;
            margin: 0 auto;
            position: absolute;
            top: 40px;
            left: 10%;
        }
        
        .signature-title {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 13px;
        }
        
        .signature-name {
            margin-bottom: 5px;
            font-weight: bold;
            min-height: 20px;
        }
        
        .signature-contact {
            font-size: 11px;
            color: #555;
            margin-bottom: 5px;
            min-height: 15px;
        }
        
        .signature-company {
            font-size: 11px;
            color: #333;
            font-weight: bold;
            min-height: 15px;
        }
        
        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
        
        /* Print-specific styles */
        @media print {
            body {
                font-size: 11px;
            }
            
            .container {
                max-width: 100%;
                margin: 0;
                padding: 0;
            }
            
            .no-print {
                display: none !important;
            }
            
            .items-table {
                page-break-inside: avoid;
            }
            
            .signature-section {
                page-break-inside: avoid;
            }
        }
        
        /* Additional spacing */
        .spacer {
            height: 10px;
        }
        
        .double-spacer {
            height: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header dengan Logo Perusahaan -->
        <div class="header">
            <div class="company-info">
                <?php if ($logoBase64): ?>
                <div class="logo">
                    <img src="<?= $logoBase64 ?>" alt="Logo PT. Cipta Duta Wacana">
                </div>
                <?php endif; ?>
                
                <div class="company-details">
                    <div class="company-name">
                        PT. CIPTA DUTA WACANA
                    </div>
                    <div class="company-address">
                        Villa Bintaro Regency, Jl. Riau Blok K1 No. 2<br>
                        Pondok Kacang Timur<br>
                        Tangerang Selatan 15226
                    </div>
                    <div class="company-contact">
                        Website: www.cdw-engineering.com
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
                <div>
                    <div class="info-item">
                        <span class="info-label">Nomor</span>
                        <span class="info-value">: <?= htmlspecialchars($suratJalan['nomor_surat_jalan']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Tanggal</span>
                        <span class="info-value">: <?= formatDate($suratJalan['tanggal_kirim']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Project</span>
                        <span class="info-value">: <?= htmlspecialchars($suratJalan['kode_project'] ?? '') ?> - <?= htmlspecialchars($suratJalan['nama_project'] ?? '') ?></span>
                    </div>
                </div>
                <div>
                    <?php if (!empty($suratJalan['nomor_invoice'])): ?>
                    <div class="info-item">
                        <span class="info-label">Invoice No</span>
                        <span class="info-value">: <?= htmlspecialchars($suratJalan['nomor_invoice']) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($suratJalan['lokasi_proyek'])): ?>
                    <div class="info-item">
                        <span class="info-label">Lokasi Proyek</span>
                        <span class="info-value">: <?= htmlspecialchars($suratJalan['lokasi_proyek']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
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
        <div class="notes-section">
            <div class="notes-title">Deskripsi Pengiriman:</div>
            <div class="notes-content"><?= nl2br(htmlspecialchars($suratJalan['catatan_barang'])) ?></div>
        </div>
        <div class="spacer"></div>
        <?php endif; ?>
        
        <!-- Barang yang Dikirim -->
        <div class="items-section">
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
            <div style="text-align: center; padding: 20px; border: 1px dashed #ccc; margin-top: 10px;">
                Tidak ada barang dalam surat jalan ini
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Informasi Pengiriman -->
        <div class="delivery-info">
            <div class="delivery-grid">
                <div>
                    <div class="section-title" style="margin-bottom: 10px;">Informasi Pengiriman:</div>
                    <?php if (!empty($suratJalan['sopir'])): ?>
                    <div class="delivery-item">
                        <strong>Sopir:</strong> <?= htmlspecialchars($suratJalan['sopir']) ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($suratJalan['sopir_telepon'])): ?>
                    <div class="delivery-item">
                        <strong>Telp Sopir:</strong> <?= htmlspecialchars($suratJalan['sopir_telepon']) ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($suratJalan['no_kendaraan'])): ?>
                    <div class="delivery-item">
                        <strong>No. Kendaraan:</strong> <?= htmlspecialchars($suratJalan['no_kendaraan']) ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($suratJalan['keterangan'])): ?>
                <div>
                    <div class="section-title" style="margin-bottom: 10px;">Keterangan:</div>
                    <div class="notes-content"><?= nl2br(htmlspecialchars($suratJalan['keterangan'])) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Tanda Tangan -->
        <div class="signature-section">
            <div class="signature-grid">
                <!-- Disiapkan Oleh -->
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
                        PT. Cipta Duta Wacana
                    </div>
                </div>
                
                <!-- Dikirim Oleh -->
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
                
                <!-- Diterima Oleh -->
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
        
        <!-- Footer -->
        <div class="footer">
            <p>
                Surat Jalan ini dicetak secara otomatis dari sistem PT. Cipta Duta Wacana<br>
                <?= date('d F Y H:i:s') ?>
            </p>
        </div>
    </div>
    
    <!-- Print Control (only visible in browser) -->
    <div class="no-print" style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;">
        <div style="background: white; padding: 10px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <button onclick="window.print()" style="background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-size: 14px; margin-right: 10px;">
                <i class="fas fa-print"></i> Cetak
            </button>
            <button onclick="window.close()" style="background: #6c757d; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-size: 14px;">
                <i class="fas fa-times"></i> Tutup
            </button>
        </div>
    </div>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <script>
    // Auto print jika ada parameter ?print=true
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('print') === 'true') {
            window.print();
        }
        
        // Auto close setelah print (jika diatur)
        if (urlParams.get('autoclose') === 'true') {
            window.onafterprint = function() {
                setTimeout(function() {
                    window.close();
                }, 1000);
            };
        }
    });
    
    // Function untuk print
    function printDocument() {
        window.print();
    }
    
    // Function untuk close
    function closeWindow() {
        window.close();
    }
    </script>
</body>
</html>