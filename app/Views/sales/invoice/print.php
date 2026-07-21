<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice <?= htmlspecialchars($invoice['nomor_invoice']) ?></title>
    <style>
        /* Reset & Base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            background-color: #fff;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        /* Header */
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #000;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        
        .company-address {
            font-size: 10px;
            margin-bottom: 3px;
        }
        
        .document-title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
            text-transform: uppercase;
        }
        
        /* Info Tables */
        .info-section {
            margin-bottom: 20px;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .info-table td {
            padding: 4px 5px;
            vertical-align: top;
            border: 1px solid #ddd;
        }
        
        .info-table .label {
            font-weight: bold;
            width: 25%;
            background-color: #f5f5f5;
        }
        
        /* Items Table */
        .items-section {
            margin: 20px 0;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .items-table th {
            background-color: #333;
            color: white;
            padding: 6px 4px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #555;
            font-size: 10px;
        }
        
        .items-table td {
            padding: 5px 4px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        
        .col-no { width: 5%; text-align: center; }
        .col-desc { width: 45%; }
        .col-qty { width: 8%; text-align: center; }
        .col-unit { width: 8%; text-align: center; }
        .col-price { width: 12%; text-align: right; }
        .col-subtotal { width: 12%; text-align: right; }
        
        /* Totals */
        .total-section {
            margin-top: 10px;
        }
        
        .total-table {
            width: 300px;
            border-collapse: collapse;
            float: right;
        }
        
        .total-table td {
            padding: 6px 8px;
            border: 1px solid #ddd;
        }
        
        .total-label {
            text-align: left;
            font-weight: bold;
            background-color: #f9f9f9;
        }
        
        .total-value {
            text-align: right;
            font-weight: bold;
            width: 120px;
        }
        
        .grand-total {
            background-color: #f0f0f0;
            font-size: 13px;
            border-top: 2px solid #000 !important;
        }
        
        /* Footer & Signature */
        .footer-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #000;
            clear: both;
        }
        
        .signature-box {
            float: right;
            text-align: center;
            width: 200px;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            width: 150px;
            margin: 20px auto 5px;
        }
        
        /* Utilities */
        .clear { clear: both; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .mb-10 { margin-bottom: 10px; }
        .mt-20 { margin-top: 20px; }
        
        /* Print Styles */
        @media print {
            body {
                padding: 10px;
            }
            
            .no-print {
                display: none !important;
            }
            
            .items-table th {
                background-color: #333 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .total-label {
                background-color: #f9f9f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .grand-total {
                background-color: #f0f0f0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Print Controls -->
        <div class="no-print text-center mb-10" style="margin-bottom: 20px;">
            <button onclick="window.print()" class="btn-print" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
                🖨️ Print Invoice
            </button>
            <button onclick="window.close()" class="btn-close" style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
                ✕ Tutup
            </button>
        </div>
        
        <!-- Header -->
        <div class="header">
            <div class="company-name">PT. CIPTA DUTA WACANA</div>
            <div class="company-address">
                Beltway Office Park Tower B Lantai 5, Jl. TB Simatupang No. 41<br>
                Ragunan-Pasar Minggu, Jakarta Selatan
            </div>
            <div class="company-address">
                Phone: (+62-21) 29857462; 29215392; 29084991 | Fax: (+62-21) 29857201
            </div>
            <div class="document-title">INVOICE</div>
        </div>
        
        <!-- Client & Invoice Info -->
        <div class="info-section">
            <table class="info-table">
                <tr>
                    <td class="label">Kepada:</td>
                    <td><?= htmlspecialchars($invoice['nama_perusahaan']) ?></td>
                    <td class="label">No. Invoice:</td>
                    <td><?= htmlspecialchars($invoice['nomor_invoice']) ?></td>
                </tr>
                <tr>
                    <td class="label">Alamat:</td>
                    <td><?= nl2br(htmlspecialchars($invoice['alamat_client'] ?? '-')) ?></td>
                    <td class="label">Tanggal:</td>
                    <td><?= date('d/m/Y', strtotime($invoice['tanggal_invoice'])) ?></td>
                </tr>
                <tr>
                    <td class="label">Attn:</td>
                    <td><?= htmlspecialchars($invoice['nama_kontak'] ?? '-') ?></td>
                    <td class="label">Jatuh Tempo:</td>
                    <td><?= date('d/m/Y', strtotime($invoice['tanggal_jatuh_tempo'])) ?></td>
                </tr>
                <tr>
                    <td class="label">Telp/Email:</td>
                    <td><?= htmlspecialchars($invoice['telepon'] ?? '-') ?> / <?= htmlspecialchars($invoice['email'] ?? '-') ?></td>
                    <td class="label">Project:</td>
                    <td><?= htmlspecialchars($invoice['nama_project']) ?></td>
                </tr>
            </table>
        </div>
        
        <!-- Items Table -->
        <div class="items-section">
            <table class="items-table">
                <thead>
                    <tr>
                        <th class="col-no">No.</th>
                        <th class="col-desc">DESKRIPSI ITEM</th>
                        <th class="col-qty">QTY</th>
                        <th class="col-unit">SATUAN</th>
                        <th class="col-price">HARGA SATUAN (Rp)</th>
                        <th class="col-subtotal">SUBTOTAL (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $index => $item): ?>
                    <tr>
                        <td class="col-no text-center"><?= $index + 1 ?></td>
                        <td class="col-desc">
                            <strong><?= htmlspecialchars($item['nama_item']) ?></strong>
                            <?php if ($item['deskripsi']): ?>
                            <br>
                            <small><?= nl2br(htmlspecialchars($item['deskripsi'])) ?></small>
                            <?php endif; ?>
                        </td>
                        <td class="col-qty text-center"><?= number_format($item['qty'], 2) ?></td>
                        <td class="col-unit text-center"><?= htmlspecialchars($item['satuan']) ?></td>
                        <td class="col-price"><?= number_format($item['harga_satuan'], 0, ',', '.') ?></td>
                        <td class="col-subtotal text-bold"><?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Total Calculation -->
        <div class="total-section">
            <table class="total-table">
                <tr>
                    <td class="total-label">SUB TOTAL</td>
                    <td class="total-value"><?= number_format($total, 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td class="total-label">PPN 11%</td>
                    <td class="total-value"><?= number_format($ppn, 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td class="total-label grand-total">GRAND TOTAL</td>
                    <td class="total-value grand-total"><?= number_format($grandTotal, 0, ',', '.') ?></td>
                </tr>
            </table>
            <div class="clear"></div>
        </div>
        
        <!-- Terbilang -->
        <div class="mb-10 mt-20">
            <p><strong>Terbilang:</strong> <em>
                <?php
                function terbilang($angka) {
                    $angka = abs($angka);
                    $baca = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
                    $terbilang = "";
                    
                    if ($angka < 12) $terbilang = " " . $baca[$angka];
                    elseif ($angka < 20) $terbilang = terbilang($angka - 10) . " Belas";
                    elseif ($angka < 100) $terbilang = terbilang($angka / 10) . " Puluh" . terbilang($angka % 10);
                    elseif ($angka < 200) $terbilang = " Seratus" . terbilang($angka - 100);
                    elseif ($angka < 1000) $terbilang = terbilang($angka / 100) . " Ratus" . terbilang($angka % 100);
                    elseif ($angka < 2000) $terbilang = " Seribu" . terbilang($angka - 1000);
                    elseif ($angka < 1000000) $terbilang = terbilang($angka / 1000) . " Ribu" . terbilang($angka % 1000);
                    elseif ($angka < 1000000000) $terbilang = terbilang($angka / 1000000) . " Juta" . terbilang($angka % 1000000);
                    
                    return trim($terbilang);
                }
                ?>
                <?= terbilang($grandTotal) ?> Rupiah
            </em></p>
            
            <?php if ($invoice['keterangan']): ?>
            <p><strong>Catatan:</strong> <?= htmlspecialchars($invoice['keterangan']) ?></p>
            <?php endif; ?>
        </div>
        
        <!-- Payment Info -->
        <div style="padding: 10px; background-color: #f9f9f9; border: 1px solid #ddd; margin: 15px 0;">
            <p class="text-bold">Informasi Pembayaran:</p>
            <p>Transfer ke: Bank Mandiri No. Rek: 101.000.676.6073</p>
            <p>Atas Nama: PT. CIPTA DUTA WACANA</p>
        </div>
        
        <!-- Footer & Signature -->
        <div class="footer-section">
            <div class="signature-box">
                <div class="signature-line"></div>
                <p style="margin: 5px 0;">Hormat kami,</p>
                <p class="text-bold">Cecep Tri Hardiyanto</p>
                <p>Direktur</p>
            </div>
            <div class="clear"></div>
        </div>
        
        <!-- Footer Note -->
        <div style="font-size: 9px; color: #666; text-align: center; margin-top: 20px;">
            <p>Invoice ini sah dan dapat digunakan sebagai dokumen tagihan resmi</p>
            <p>Dicetak pada: <?= date('d/m/Y H:i:s') ?></p>
        </div>
    </div>
    
    <script>
        // Auto print option
        window.onload = function() {
            // Uncomment to auto print on load
            // setTimeout(function() { window.print(); }, 1000);
        };
    </script>
</body>
</html>