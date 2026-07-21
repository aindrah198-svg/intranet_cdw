<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
    <style>
        @page {
            margin: 15mm;
        }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.2;
            color: #000;
        }
        .header {
            margin-bottom: 5mm;
        }
        .company-header {
            margin-bottom: 3mm;
            position: relative;
        }
        .logo-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100px; /* Sesuaikan dengan ukuran logo */
            height: 100px;
        }
        .logo-img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .company-info {
            margin-left: 110px; /* Beri ruang untuk logo */
        }
        .company-info h1 {
            font-size: 14px;
            font-weight: bold;
            margin: 0 0 3px 0;
        }
        .company-info p {
            margin: 1px 0;
            font-size: 10px;
        }
        .title {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            margin: 5mm 0;
            text-decoration: underline;
            clear: both;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3mm;
            font-size: 10px;
        }
        .info-table td {
            padding: 2px;
            vertical-align: top;
        }
        .info-table .label {
            font-weight: bold;
            white-space: nowrap;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 3mm 0;
            font-size: 10px;
            page-break-inside: avoid;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 3px;
            text-align: left;
            vertical-align: top;
        }
        .items-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .signature-table {
            width: 100%;
            margin-top: 15mm;
            border-collapse: collapse;
            font-size: 10px;
        }
        .signature-table td {
            padding: 15mm 5px 0;
            text-align: center;
            vertical-align: top;
            width: 33.33%;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 80%;
            margin: 20px auto 5px;
        }
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
            margin-bottom: 1mm;
        }
        .mb-2 {
            margin-bottom: 2mm;
        }
        .mb-3 {
            margin-bottom: 3mm;
        }
        .no-border {
            border: none !important;
        }
        .border-top {
            border-top: 1px solid #000;
        }
        .page-number {
            position: fixed;
            bottom: 10mm;
            right: 15mm;
            font-size: 9px;
            color: #666;
        }
        .item-list {
            margin: 0;
            padding-left: 15px;
        }
        .item-list li {
            margin-bottom: 2px;
        }
        .narative-content {
            white-space: pre-line;
            font-size: 10px;
            line-height: 1.3;
        }
    </style>
</head>
<body>
    <!-- Header Perusahaan dengan Logo -->
    <div class="header">
        <div class="company-header">
            <!-- Logo -->
            <div class="logo-container">
                <?php
                $logoPath = FCPATH . 'assets/img/logo/logo_cdw.jpg';
                if (file_exists($logoPath)) {
                    $logoData = base64_encode(file_get_contents($logoPath));
                    $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
                    $logoBase64 = 'data:image/' . $logoType . ';base64,' . $logoData;
                } else {
                    // Fallback jika logo tidak ditemukan
                    $logoBase64 = 'data:image/svg+xml;base64,' . base64_encode('
                        <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100">
                            <rect width="100" height="100" fill="#f0f0f0"/>
                            <text x="50" y="50" text-anchor="middle" dy=".3em" font-size="12">LOGO</text>
                        </svg>
                    ');
                }
                ?>
                <img src="<?= $logoBase64 ?>" alt="Logo PT. CIPTA DUTA WACANA" class="logo-img">
            </div>
            
            <!-- Informasi Perusahaan -->
            <div class="company-info">
                <h1>PT. CIPTA DUTA WACANA</h1>
                <p>Villa Bintaro Regency</p>
                <p>Jl. Riau Blok K1 No. 2</p>
                <p>Pondok Kacang Timur</p>
                <p>Tangerang Selatan 15226</p>
                <p>www.cdw-engineering.com</p>
            </div>
        </div>
        
        <!-- Judul Surat Jalan -->
        <div class="title">
            SURAT JALAN / DELIVERY NOTE
        </div>
        
        <!-- Informasi Nomor dan Tanggal -->
        <table class="info-table">
            <tr>
                <td width="50%">
                    <table>
                        <tr>
                            <td class="label" width="80">Nomor</td>
                            <td width="5">:</td>
                            <td><?= $suratJalan['nomor_surat_jalan'] ?></td>
                        </tr>
                        <tr>
                            <td class="label">Tanggal</td>
                            <td>:</td>
                            <td><?= date('d-M-y', strtotime($suratJalan['tanggal_kirim'])) ?></td>
                        </tr>
                    </table>
                </td>
                <td width="50%" class="text-right">
                    <!-- Kosong untuk alignment -->
                </td>
            </tr>
        </table>
    </div>
    
    <!-- Penerima -->
    <div class="mb-3">
        <div class="text-bold mb-1">Dikirimkan kepada</div>
        <table class="info-table">
            <tr>
                <td width="20%" class="label"><?= $suratJalan['penerima_perusahaan'] ?></td>
                <td width="2%"></td>
                <td width="78%">
                    <?= $suratJalan['alamat_pengiriman'] ?>
                    <?php if (!empty($suratJalan['lokasi_proyek'])): ?>
                    <br><span class="text-bold">Lokasi Proyek:</span> <?= $suratJalan['lokasi_proyek'] ?>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td class="label">Up</td>
                <td>:</td>
                <td><?= $suratJalan['penerima_up'] ?></td>
            </tr>
            <?php if (!empty($suratJalan['penerima_telepon'])): ?>
            <tr>
                <td class="label">Telp</td>
                <td>:</td>
                <td><?= $suratJalan['penerima_telepon'] ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>
    
    <!-- Catatan Barang Naratif (Sesuai contoh) -->
    <?php if (!empty($suratJalan['catatan_barang'])): ?>
    <div class="mb-3">
        <div class="narative-content"><?= $suratJalan['catatan_barang'] ?></div>
    </div>
    <?php endif; ?>
    
    <!-- Tabel Barang (jika ada items) -->
    <?php if (!empty($items)): ?>
    <div class="mb-3">
        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="40%">Nama Barang</th>
                    <th width="10%">Qty</th>
                    <th width="10%">Satuan</th>
                    <th width="15%">Berat</th>
                    <th width="20%">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $totalQty = 0;
                $totalBerat = 0;
                ?>
                <?php foreach ($items as $index => $item): ?>
                <?php 
                $totalQty += floatval($item['qty']);
                $totalBerat += floatval($item['berat']);
                ?>
                <tr>
                    <td class="text-center"><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($item['nama_barang']) ?></td>
                    <td class="text-center"><?= $item['qty_formatted'] ?></td>
                    <td class="text-center"><?= htmlspecialchars($item['satuan']) ?></td>
                    <td class="text-center"><?= $item['berat_formatted'] ?></td>
                    <td><?= htmlspecialchars($item['keterangan'] ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
                <!-- Baris Total -->
                <tr>
                    <td colspan="2" class="text-bold text-center">TOTAL</td>
                    <td class="text-bold text-center"><?= number_format($totalQty, 2) ?></td>
                    <td></td>
                    <td class="text-bold text-center"><?= $totalBerat > 0 ? number_format($totalBerat, 2) . ' kg' : '-' ?></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
    
    <!-- Informasi Pengiriman -->
    <?php if (!empty($suratJalan['sopir']) || !empty($suratJalan['no_kendaraan'])): ?>
    <div class="mb-3">
        <table class="info-table">
            <?php if (!empty($suratJalan['sopir'])): ?>
            <tr>
                <td width="15%" class="label">Sopir</td>
                <td width="2%">:</td>
                <td><?= $suratJalan['sopir'] ?></td>
            </tr>
            <?php endif; ?>
            <?php if (!empty($suratJalan['no_kendaraan'])): ?>
            <tr>
                <td class="label">No. Kendaraan</td>
                <td>:</td>
                <td><?= $suratJalan['no_kendaraan'] ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>
    <?php endif; ?>
    
    <!-- Tanda Tangan -->
    <table class="signature-table">
        <tr>
            <td>
                <div>Disiapkan oleh,</div>
                <div class="signature-line"></div>
                <div><?= $suratJalan['disiapkan_oleh'] ?? '' ?></div>
                <div><?= $suratJalan['disiapkan_telepon'] ?? '' ?></div>
                <div><?= $suratJalan['disiapkan_jabatan'] ?? '' ?></div>
            </td>
            <td>
                <div>Dikirim oleh,</div>
                <div class="signature-line"></div>
                <div><?= $suratJalan['dikirim_oleh'] ?? '' ?></div>
                <div><?= $suratJalan['dikirim_telepon'] ?? '' ?></div>
            </td>
            <td>
                <div>Diterima oleh,</div>
                <div class="signature-line"></div>
                <div><?= $suratJalan['diterima_oleh'] ?? '' ?></div>
                <div><?= $suratJalan['diterima_telepon'] ?? '' ?></div>
                <div><?= $suratJalan['diterima_perusahaan'] ?? '' ?></div>
                <?php if (!empty($suratJalan['tanggal_terima'])): ?>
                <div>Tanggal: <?= date('d-M-y H:i', strtotime($suratJalan['tanggal_terima'])) ?></div>
                <?php endif; ?>
            </td>
        </tr>
    </table>
    
    <!-- Page Number -->
    <div class="page-number">
        Halaman 1
    </div>
</body>
</html>