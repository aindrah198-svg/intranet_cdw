<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Print Penawaran') ?></title>
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: #000;
        }
        
        .print-container {
            max-width: 210mm;
            margin: 0 auto;
        }
        
        .header {
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .company-info {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .company-address {
            font-size: 11px;
            margin-bottom: 5px;
        }
        
        .document-title {
            text-align: center;
            margin: 20px 0;
        }
        
        .document-title h1 {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        
        .document-title h2 {
            font-size: 14px;
            font-weight: normal;
            margin: 0;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .info-table td {
            padding: 8px;
            vertical-align: top;
            border: 1px solid #ddd;
        }
        
        .info-table .label {
            font-weight: bold;
            width: 30%;
            background-color: #f8f9fa;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .items-table th {
            background-color: #333;
            color: white;
            padding: 8px;
            text-align: center;
            font-weight: bold;
        }
        
        .items-table td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
        }
        
        .items-table .text-left {
            text-align: left;
        }
        
        .items-table .text-right {
            text-align: right;
        }
        
        .total-row {
            font-weight: bold;
            background-color: #f8f9fa;
        }
        
        .footer {
            margin-top: 30px;
            border-top: 1px solid #333;
            padding-top: 20px;
        }
        
        .signature {
            float: right;
            text-align: center;
            margin-top: 50px;
        }
        
        .signature-line {
            width: 200px;
            border-top: 1px solid #000;
            margin: 40px auto 5px;
        }
        
        .terms {
            font-size: 10px;
            margin-top: 20px;
            clear: both;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .no-print {
            display: none;
        }
        
        /* Print styles */
        @media print {
            .no-print {
                display: none;
            }
            
            body {
                margin: 0;
                padding: 0;
            }
            
            .print-container {
                margin: 0;
            }
            
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Print Button -->
    <div class="no-print" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
        <button onclick="window.print()" class="print-button" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
            <i class="fas fa-print"></i> Print
        </button>
        <button onclick="window.close()" class="print-button" style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
            <i class="fas fa-times"></i> Close
        </button>
    </div>

    <div class="print-container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <div class="company-name">PT. CIPTA DUTA WACANA</div>
                <div class="company-address">
                    Beltway Office Park Tower B Lantai 5<br>
                    Jl. TB Simatupang No. 41 Ragunan-Pasar Minggu, Jakarta Selatan<br>
                    Phone: (+62-21) 29857462; 29215392; 29084991 | Fax: (+62-21) 29857201
                </div>
            </div>
            
            <div class="document-title">
                <h1>QUOTATION</h1>
                <h2>SURAT PENAWARAN HARGA</h2>
            </div>
            
            <table class="info-table">
                <tr>
                    <td class="label">To.</td>
                    <td><?= htmlspecialchars($penawaran['nama_perusahaan']) ?></td>
                    <td class="label">Quotation Number</td>
                    <td><?= htmlspecialchars($penawaran['nomor_penawaran']) ?></td>
                </tr>
                <tr>
                    <td class="label">Address</td>
                    <td><?= nl2br(htmlspecialchars($penawaran['alamat_client'] ?? '-')) ?></td>
                    <td class="label">Date</td>
                    <td><?= date('d/m/Y', strtotime($penawaran['tanggal_penawaran'])) ?></td>
                </tr>
                <tr>
                    <td class="label">Attn</td>
                    <td><?= htmlspecialchars($penawaran['nama_kontak'] ?? '-') ?></td>
                    <td class="label">Customer Code</td>
                    <td><?= htmlspecialchars($penawaran['kode_client'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="label">Contact</td>
                    <td>
                        <?php if ($penawaran['telepon']): ?>
                            <?= htmlspecialchars($penawaran['telepon']) ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td class="label">Due Date</td>
                    <td>
                        <?php if ($penawaran['tanggal_kadaluarsa']): ?>
                            <?= date('d/m/Y', strtotime($penawaran['tanggal_kadaluarsa'])) ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Opening Text -->
        <div style="margin-bottom: 20px;">
            <p>Bersama ini kami ingin mengajukan penawaran harga untuk <?= htmlspecialchars($penawaran['nama_project'] ?? 'project') ?> dengan detail terlampir.</p>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Description</th>
                    <th>Qty</th>
                    <th>SATUAN</th>
                    <th>Unit Price (Rp.)</th>
                    <th>Total (Rp.)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $index => $item): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td class="text-left">
                                <strong><?= htmlspecialchars($item['nama_item']) ?></strong><br>
                                <small><?= nl2br(htmlspecialchars($item['deskripsi'])) ?></small>
                            </td>
                            <td><?= number_format($item['qty'], 2) ?></td>
                            <td><?= htmlspecialchars($item['satuan']) ?></td>
                            <td class="text-right"><?= number_format($item['harga_satuan'], 0, ',', '.') ?></td>
                            <td class="text-right"><?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No items found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="5" class="text-right"><strong>TOTAL PENAWARAN</strong></td>
                    <td class="text-right"><strong>Rp <?= number_format($total, 0, ',', '.') ?></strong></td>
                </tr>
            </tfoot>
        </table>

        <!-- Remarks -->
        <div style="margin-bottom: 20px;">
            <p><strong>Remarks:</strong> Payment can be transfer to Bank : (Full Amount)</p>
            <p>Atas Nama : PT. CIPTA DUTA WACANA</p>
            <p>BANK MANDIRI ACC No. 101-000-676-607-3 (IDR)</p>
        </div>

        <!-- In Words -->
        <div style="margin-bottom: 20px;">
            <p><strong>In Word:</strong> <?= terbilang($total) ?> Rupiah</p>
            <p>Masa Berlaku penawaran 1 (satu) Minggu</p>
        </div>

        <!-- Terms and Conditions -->
        <div class="terms">
            <p><strong>Term and Condition :</strong></p>
            <ol style="margin-top: 5px; padding-left: 20px;">
                <li>Harga belum termasuk Pajak</li>
                <li>Dispenser Ready Stock</li>
                <li>Pembayaran 50% DP, dan 50% Cash Before Delivery (CBD)</li>
                <li>Masa Garansi Peralatan supply 6 (enam Bulan)</li>
            </ol>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="signature">
                <div class="signature-line"></div>
                <p>Cecep Tri Hardiyanto<br>Direktur</p>
            </div>
            
            <div style="clear: both;"></div>
        </div>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            // Uncomment the line below to auto-print
            // window.print();
        };
    </script>
</body>
</html>

<?php
// Helper function to convert number to words
function terbilang($angka) {
    $angka = abs($angka);
    $baca = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
    $terbilang = "";
    
    if ($angka < 12) {
        $terbilang = " " . $baca[$angka];
    } else if ($angka < 20) {
        $terbilang = terbilang($angka - 10) . " Belas";
    } else if ($angka < 100) {
        $terbilang = terbilang($angka / 10) . " Puluh" . terbilang($angka % 10);
    } else if ($angka < 200) {
        $terbilang = " Seratus" . terbilang($angka - 100);
    } else if ($angka < 1000) {
        $terbilang = terbilang($angka / 100) . " Ratus" . terbilang($angka % 100);
    } else if ($angka < 2000) {
        $terbilang = " Seribu" . terbilang($angka - 1000);
    } else if ($angka < 1000000) {
        $terbilang = terbilang($angka / 1000) . " Ribu" . terbilang($angka % 1000);
    } else if ($angka < 1000000000) {
        $terbilang = terbilang($angka / 1000000) . " Juta" . terbilang($angka % 1000000);
    }
    
    return trim($terbilang);
}
?>