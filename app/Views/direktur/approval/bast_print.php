<?php
// app/Views/direktur/approval/bast_print.php

$bast = $bast ?? [];

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

// Helper function untuk kondisi text
if (!function_exists('getKondisiText')) {
    function getKondisiText($kondisi) {
        $map = [
            'Baik' => 'BAIK',
            'Cukup' => 'CUKUP',
            'Perlu Perbaikan' => 'PERLU PERBAIKAN'
        ];
        return $map[$kondisi] ?? strtoupper($kondisi);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>BAST - <?= htmlspecialchars($bast['nomor_bast'] ?? 'Berita Acara Serah Terima') ?></title>
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
            width: 180px;
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
        
        .condition-good {
            color: #28a745;
            font-weight: bold;
        }
        
        .condition-fair {
            color: #fd7e14;
            font-weight: bold;
        }
        
        .condition-poor {
            color: #dc3545;
            font-weight: bold;
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
            <h3>BERITA ACARA SERAH TERIMA (BAST)</h3>
            <p>No. <?= htmlspecialchars($bast['nomor_bast'] ?? '-') ?></p>
        </div>

        <!-- Surat Info -->
        <div class="surat-info">
            Jakarta, <?= formatDateIndo($bast['tanggal_bast'] ?? date('Y-m-d')) ?>
        </div>

        <!-- Prelim -->
        <div class="section">
            <p style="text-align: justify; margin-bottom: 15px;">
                Pada hari ini, <?= formatDateIndo($bast['tanggal_bast'] ?? date('Y-m-d')) ?>, telah dilakukan serah terima pekerjaan antara:
            </p>
        </div>

        <!-- Pihak Pertama -->
        <div class="section">
            <div class="section-title">PIHAK PERTAMA (PENYERAH)</div>
            <table class="info-table">
                <tr>
                    <td>Nama</td>
                    <td>: <?= htmlspecialchars($bast['pihak_pertama_nama'] ?? 'PT. CIPTA DUTA WACANA') ?></td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td>: <?= htmlspecialchars($bast['pihak_pertama_jabatan'] ?? 'Direktur') ?></td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>: Beltway Office Park Tower B Lantai 5, Jl. TB Simatupang No. 41, Jakarta Selatan</td>
                </tr>
                <tr>
                    <td>Selanjutnya disebut</td>
                    <td>: <strong>PIHAK PERTAMA</strong></td>
                </tr>
            </table>
        </div>

        <!-- Pihak Kedua -->
        <div class="section">
            <div class="section-title">PIHAK KEDUA (PENERIMA)</div>
            <table class="info-table">
                <tr>
                    <td>Nama Perusahaan</td>
                    <td>: <?= htmlspecialchars($bast['nama_perusahaan'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td>Nama</td>
                    <td>: <?= htmlspecialchars($bast['pihak_kedua_nama'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td>: <?= htmlspecialchars($bast['pihak_kedua_jabatan'] ?? '-') ?></td>
                </tr>
                <?php if (!empty($bast['client_alamat'])): ?>
                <tr>
                    <td>Alamat</td>
                    <td>: <?= nl2br(htmlspecialchars($bast['client_alamat'])) ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td>Selanjutnya disebut</td>
                    <td>: <strong>PIHAK KEDUA</strong></td>
                </tr>
            </table>
        </div>

        <!-- Dasar / Perintah -->
        <div class="section">
            <div class="section-title">DASAR / PERINTAH</div>
            <table class="info-table">
                <tr>
                    <td>No. SPK</td>
                    <td>: <?= htmlspecialchars($bast['nomor_spk'] ?? '-') ?></td>
                </tr>
                <?php if (!empty($bast['nomor_surat_jalan'])): ?>
                <tr>
                    <td>No. Surat Jalan</td>
                    <td>: <?= htmlspecialchars($bast['nomor_surat_jalan']) ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>

        <!-- Uraian Pekerjaan -->
        <div class="section">
            <div class="section-title">URAIAN PEKERJAAN</div>
            <table class="info-table">
                <tr>
                    <td>Judul Pekerjaan</td>
                    <td>: <strong><?= htmlspecialchars($bast['judul_pekerjaan'] ?? '-') ?></strong></td>
                </tr>
                <?php if (!empty($bast['lokasi_pekerjaan'])): ?>
                <tr>
                    <td>Lokasi Pekerjaan</td>
                    <td>: <?= htmlspecialchars($bast['lokasi_pekerjaan']) ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td>Deskripsi Pekerjaan</td>
                    <td>: <?= nl2br(htmlspecialchars($bast['deskripsi_pekerjaan'] ?? '-')) ?></td>
                </tr>
                <tr>
                    <td>Kondisi</td>
                    <td>: 
                        <span class="<?= ($bast['kondisi'] ?? '') === 'Baik' ? 'condition-good' : (($bast['kondisi'] ?? '') === 'Cukup' ? 'condition-fair' : 'condition-poor') ?>">
                            <?= getKondisiText($bast['kondisi'] ?? 'Baik') ?>
                        </span>
                    </td>
                </tr>
                <?php if (!empty($bast['catatan_tambahan'])): ?>
                <tr>
                    <td>Catatan Tambahan</td>
                    <td>: <?= nl2br(htmlspecialchars($bast['catatan_tambahan'])) ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>

        <!-- Pernyataan -->
        <div class="section">
            <p style="text-align: justify; margin-top: 15px;">
                Demikian Berita Acara Serah Terima ini dibuat dengan sebenar-benarnya dan ditandatangani oleh kedua belah pihak 
                pada hari dan tanggal tersebut di atas, untuk dapat dipergunakan sebagaimana mestinya.
            </p>
        </div>

        <!-- Tanda Tangan -->
        <div class="signature">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-name">PIHAK PERTAMA</div>
                <div class="signature-title">(<?= htmlspecialchars($bast['pihak_pertama_nama'] ?? 'Cecep Trihardiyanto') ?>)</div>
                <div class="signature-title">Direktur</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-name">PIHAK KEDUA</div>
                <div class="signature-title">(<?= htmlspecialchars($bast['pihak_kedua_nama'] ?? '_________________') ?>)</div>
                <div class="signature-title"><?= htmlspecialchars($bast['pihak_kedua_jabatan'] ?? '') ?></div>
            </div>
        </div>

        <!-- Mengetahui -->
        <div class="signature" style="margin-top: 20px;">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-name">Mengetahui,</div>
                <div class="signature-title">HRD</div>
                <div class="signature-title">(<?= htmlspecialchars($bast['hrd_nama'] ?? '_________________') ?>)</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-name"></div>
                <div class="signature-title"></div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            Dokumen ini dicetak pada: <?= date('d/m/Y H:i:s') ?><br>
            Berita Acara Serah Terima ini adalah dokumen resmi PT. Cipta Duta Wacana (CDW Engineering)
        </div>
    </div>

    <script>
        // Auto print jika diperlukan (opsional, bisa diaktifkan)
        // setTimeout(function() { window.print(); }, 500);
    </script>
</body>
</html>