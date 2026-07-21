<?php

// Status colors mapping
$statusColors = [
    'draft' => 'secondary',
    'posted' => 'success',
    'void' => 'danger'
];

// Get company info
$company = model('PerusahaanModel')->first() ?? [
    'nama_perusahaan' => 'PT. Cipta Duta Wacana',
    'alamat' => 'Jl. Contoh No. 123',
    'kota' => 'Jakarta',
    'telepon' => '(021) 12345678',
    'email' => 'info@perusahaan.com'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Jurnal - <?= $jurnal['nomor_jurnal'] ?></title>
    <style>
        /* Print Styles */
        @media print {
            @page {
                margin: 0.5in;
                size: A4 portrait;
            }
            
            body {
                font-family: 'Arial', sans-serif;
                font-size: 11pt;
                line-height: 1.4;
                color: #000;
                background: #fff;
            }
            
            .no-print {
                display: none !important;
            }
            
            .page-break {
                page-break-before: always;
            }
            
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 15px;
            }
            
            th, td {
                padding: 6px 8px;
                text-align: left;
                border: 1px solid #ddd;
            }
            
            th {
                background-color: #f8f9fa;
                font-weight: bold;
            }
            
            .text-right {
                text-align: right;
            }
            
            .text-center {
                text-align: center;
            }
            
            .text-bold {
                font-weight: bold;
            }
            
            .border-bottom {
                border-bottom: 2px solid #000;
            }
            
            .mt-3 {
                margin-top: 1rem;
            }
            
            .mb-3 {
                margin-bottom: 1rem;
            }
            
            .py-2 {
                padding-top: 0.5rem;
                padding-bottom: 0.5rem;
            }
        }
        
        /* Screen Styles */
        @media screen {
            body {
                font-family: Arial, sans-serif;
                background: #f5f5f5;
                padding: 20px;
            }
            
            .print-container {
                max-width: 210mm;
                min-height: 297mm;
                margin: 0 auto;
                background: white;
                padding: 20mm;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }
            
            .no-print {
                text-align: center;
                margin-bottom: 20px;
                padding: 10px;
                background: #fff;
                border-radius: 5px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            }
        }
    </style>
</head>
<body>
    <!-- Print Controls (Screen Only) -->
    <div class="no-print">
        <h3>Preview Cetak Jurnal</h3>
        <p>Nomor Jurnal: <?= $jurnal['nomor_jurnal'] ?></p>
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Cetak Sekarang
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            <i class="fas fa-times"></i> Tutup
        </button>
        <hr>
    </div>
    
    <!-- Print Content -->
    <div class="print-container">
        <!-- Header -->
        <div class="text-center mb-3">
            <h2 style="margin-bottom: 5px;"><?= $company['nama_perusahaan'] ?></h2>
            <p style="margin: 5px 0; font-size: 12pt;">
                <?= $company['alamat'] ?? '' ?><br>
                <?= $company['kota'] ?? '' ?> 
                <?= $company['telepon'] ? 'Telp: ' . $company['telepon'] : '' ?>
                <?= $company['email'] ? 'Email: ' . $company['email'] : '' ?>
            </p>
            <div class="border-bottom py-2" style="margin: 15px 0;"></div>
            
            <h3 style="margin: 10px 0;">JURNAL UMUM</h3>
            <p style="margin: 5px 0; font-size: 11pt;">
                Tanggal Cetak: <?= date('d/m/Y H:i') ?>
            </p>
        </div>
        
        <!-- Jurnal Info -->
        <table style="margin-bottom: 20px;">
            <tr>
                <td width="30%"><strong>Nomor Jurnal</strong></td>
                <td width="2%">:</td>
                <td><?= $jurnal['nomor_jurnal'] ?></td>
                <td width="30%"><strong>Status</strong></td>
                <td width="2%">:</td>
                <td>
                    <span style="
                        padding: 2px 8px;
                        border-radius: 3px;
                        font-size: 10pt;
                        background: <?= 
                            $jurnal['status'] == 'draft' ? '#6c757d' : 
                            ($jurnal['status'] == 'posted' ? '#28a745' : '#dc3545') 
                        ?>;
                        color: white;
                    ">
                        <?= strtoupper($jurnal['status']) ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td><strong>Tanggal Jurnal</strong></td>
                <td>:</td>
                <td><?= date('d F Y', strtotime($jurnal['tanggal'])) ?></td>
                <td><strong>Balance Status</strong></td>
                <td>:</td>
                <td>
                    <?= $totalDebit == $totalKredit ? 
                        '<span style="color: #28a745;">✓ BALANCED</span>' : 
                        '<span style="color: #dc3545;">✗ UNBALANCED</span>' 
                    ?>
                </td>
            </tr>
            <?php if ($jurnal['tipe_referensi'] || $jurnal['referensi']): ?>
            <tr>
                <td><strong>Tipe Referensi</strong></td>
                <td>:</td>
                <td><?= $jurnal['tipe_referensi'] ? ucfirst($jurnal['tipe_referensi']) : '-' ?></td>
                <td><strong>Nomor Referensi</strong></td>
                <td>:</td>
                <td><?= $jurnal['referensi'] ?: '-' ?></td>
            </tr>
            <?php endif ?>
            <tr>
                <td><strong>Keterangan</strong></td>
                <td>:</td>
                <td colspan="4"><?= htmlspecialchars($jurnal['keterangan']) ?></td>
            </tr>
        </table>
        
        <!-- Jurnal Details -->
        <h4 style="margin: 20px 0 10px 0; text-align: center;">DETAIL TRANSAKSI</h4>
        
        <!-- Debit Section -->
        <?php if (!empty($debitDetails)): ?>
        <h5 style="margin: 15px 0 5px 0; color: #28a745;">
            DEBIT (<?= count($debitDetails) ?> Akun)
        </h5>
        <table>
            <thead>
                <tr style="background-color: #f8f9fa;">
                    <th width="15%">Kode Akun</th>
                    <th width="30%">Nama Akun</th>
                    <th width="20%">Keterangan</th>
                    <th width="15%" class="text-right">Jumlah (Rp)</th>
                    <th width="20%">Saldo Normal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($debitDetails as $detail): ?>
                <tr>
                    <td><?= $detail['kode_akun'] ?></td>
                    <td><?= $detail['nama_akun'] ?></td>
                    <td><?= $detail['keterangan'] ?: '-' ?></td>
                    <td class="text-right"><?= number_format($detail['debit'], 2) ?></td>
                    <td>DEBIT</td>
                </tr>
                <?php endforeach ?>
                <tr style="background-color: #f8f9fa; font-weight: bold;">
                    <td colspan="3" class="text-right">Subtotal Debit:</td>
                    <td class="text-right"><?= number_format($totalDebit, 2) ?></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <?php endif ?>
        
        <!-- Kredit Section -->
        <?php if (!empty($kreditDetails)): ?>
        <h5 style="margin: 20px 0 5px 0; color: #ffc107;">
            KREDIT (<?= count($kreditDetails) ?> Akun)
        </h5>
        <table>
            <thead>
                <tr style="background-color: #f8f9fa;">
                    <th width="15%">Kode Akun</th>
                    <th width="30%">Nama Akun</th>
                    <th width="20%">Keterangan</th>
                    <th width="15%" class="text-right">Jumlah (Rp)</th>
                    <th width="20%">Saldo Normal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($kreditDetails as $detail): ?>
                <tr>
                    <td><?= $detail['kode_akun'] ?></td>
                    <td><?= $detail['nama_akun'] ?></td>
                    <td><?= $detail['keterangan'] ?: '-' ?></td>
                    <td class="text-right"><?= number_format($detail['kredit'], 2) ?></td>
                    <td>KREDIT</td>
                </tr>
                <?php endforeach ?>
                <tr style="background-color: #f8f9fa; font-weight: bold;">
                    <td colspan="3" class="text-right">Subtotal Kredit:</td>
                    <td class="text-right"><?= number_format($totalKredit, 2) ?></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <?php endif ?>
        
        <!-- Balance Summary -->
        <div style="
            margin-top: 30px;
            padding: 15px;
            border: 2px solid <?= $totalDebit == $totalKredit ? '#28a745' : '#dc3545' ?>;
            border-radius: 5px;
            background-color: <?= $totalDebit == $totalKredit ? '#f8fff8' : '#fff8f8' ?>;
        ">
            <h4 style="text-align: center; margin-bottom: 15px; color: <?= $totalDebit == $totalKredit ? '#28a745' : '#dc3545' ?>;">
                <i class="fas fa-balance-scale"></i> RINGKASAN BALANCE
            </h4>
            
            <table style="background: transparent; border: none;">
                <tr>
                    <td width="50%" style="border: none; padding: 8px;">
                        <div style="font-size: 11pt; color: #666;">Total Debit</div>
                        <div style="font-size: 16pt; font-weight: bold; color: #28a745;">
                            Rp <?= number_format($totalDebit, 2) ?>
                        </div>
                    </td>
                    <td width="50%" style="border: none; padding: 8px;">
                        <div style="font-size: 11pt; color: #666;">Total Kredit</div>
                        <div style="font-size: 16pt; font-weight: bold; color: #ffc107;">
                            Rp <?= number_format($totalKredit, 2) ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="border: none; padding: 8px; text-align: center;">
                        <div style="
                            display: inline-block;
                            padding: 8px 20px;
                            border-radius: 4px;
                            font-size: 12pt;
                            font-weight: bold;
                            background: <?= $totalDebit == $totalKredit ? '#28a745' : '#dc3545' ?>;
                            color: white;
                        ">
                            <?= $totalDebit == $totalKredit ? '✓ BALANCED' : '✗ UNBALANCED' ?>
                            <?php if ($totalDebit != $totalKredit): ?>
                                <br>
                                <small>Selisih: Rp <?= number_format(abs($totalDebit - $totalKredit), 2) ?></small>
                            <?php endif ?>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Audit Trail -->
        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px dashed #ccc;">
            <table style="border: none; font-size: 10pt;">
                <tr>
                    <td width="33%" style="border: none; padding: 5px;">
                        <strong>Dibuat Oleh:</strong><br>
                        <?= $jurnal['creator_name'] ?? 'System' ?>
                    </td>
                    <td width="33%" style="border: none; padding: 5px;">
                        <strong>Tanggal Dibuat:</strong><br>
                        <?= date('d/m/Y H:i', strtotime($jurnal['created_at'])) ?>
                    </td>
                    <td width="34%" style="border: none; padding: 5px;">
                        <strong>Status:</strong><br>
                        <?= strtoupper($jurnal['status']) ?>
                    </td>
                </tr>
                <?php if ($jurnal['status'] == 'posted'): ?>
                <tr>
                    <td style="border: none; padding: 5px;">
                        <strong>Diposting Oleh:</strong><br>
                        <?= $jurnal['poster_name'] ?? 'System' ?>
                    </td>
                    <td style="border: none; padding: 5px;">
                        <strong>Tanggal Posting:</strong><br>
                        <?= date('d/m/Y H:i', strtotime($jurnal['posted_at'])) ?>
                    </td>
                    <td style="border: none; padding: 5px;">
                        <strong>Dicetak Oleh:</strong><br>
                        <?= $printed_by ?>
                    </td>
                </tr>
                <?php endif ?>
            </table>
        </div>
        
        <!-- Footer -->
        <div style="margin-top: 50px; text-align: center; font-size: 10pt; color: #666;">
            <p>
                <strong>CATATAN:</strong><br>
                1. Dokumen ini dicetak secara otomatis dari sistem akuntansi.<br>
                2. Berlaku sebagai bukti pencatatan transaksi keuangan.<br>
                3. Simpan dokumen ini untuk keperluan audit dan arsip.
            </p>
            <div style="border-top: 1px solid #ccc; padding-top: 10px;">
                <?= date('d F Y, H:i:s') ?> | Halaman 1 dari 1
            </div>
        </div>
    </div>
    
    <script>
        // Auto print when page loads (optional)
        window.onload = function() {
            // Uncomment below line to auto-print when page loads
            // setTimeout(function() { window.print(); }, 1000);
        };
        
        // After print, close window or stay
        window.onafterprint = function() {
            // Optional: close window after printing
            // window.close();
        };
    </script>
</body>
</html>