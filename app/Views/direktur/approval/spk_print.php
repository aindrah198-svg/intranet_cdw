<?php
// app/Views/direktur/approval/spk_print.php

$spk = $spk ?? [];

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

// Helper function untuk format currency
if (!function_exists('formatCurrency')) {
    function formatCurrency($amount) {
        if (empty($amount) || $amount == 0) {
            return '-';
        }
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

// Helper function untuk terbilang - DIPERBAIKI
if (!function_exists('terbilang')) {
    function terbilang($angka) {
        // Pastikan angka adalah integer positif
        $angka = abs((int) $angka);
        
        if ($angka == 0) {
            return 'Nol';
        }
        
        $satuan = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan'];
        $belasan = ['Sepuluh', 'Sebelas', 'Dua Belas', 'Tiga Belas', 'Empat Belas', 'Lima Belas', 'Enam Belas', 'Tujuh Belas', 'Delapan Belas', 'Sembilan Belas'];
        $puluhan = ['', '', 'Dua Puluh', 'Tiga Puluh', 'Empat Puluh', 'Lima Puluh', 'Enam Puluh', 'Tujuh Puluh', 'Delapan Puluh', 'Sembilan Puluh'];
        
        if ($angka < 10) {
            return $satuan[$angka];
        } elseif ($angka < 20) {
            return $belasan[$angka - 10];
        } elseif ($angka < 100) {
            $puluh = (int)($angka / 10);
            $sisa = $angka % 10;
            return $puluhan[$puluh] . ($sisa > 0 ? ' ' . $satuan[$sisa] : '');
        } elseif ($angka < 200) {
            return 'Seratus' . ($angka > 100 ? ' ' . terbilang($angka - 100) : '');
        } elseif ($angka < 1000) {
            $ratus = (int)($angka / 100);
            $sisa = $angka % 100;
            return $satuan[$ratus] . ' Ratus' . ($sisa > 0 ? ' ' . terbilang($sisa) : '');
        } elseif ($angka < 2000) {
            return 'Seribu' . ($angka > 1000 ? ' ' . terbilang($angka - 1000) : '');
        } elseif ($angka < 1000000) {
            $ribu = (int)($angka / 1000);
            $sisa = $angka % 1000;
            return terbilang($ribu) . ' Ribu' . ($sisa > 0 ? ' ' . terbilang($sisa) : '');
        } elseif ($angka < 1000000000) {
            $juta = (int)($angka / 1000000);
            $sisa = $angka % 1000000;
            return terbilang($juta) . ' Juta' . ($sisa > 0 ? ' ' . terbilang($sisa) : '');
        } elseif ($angka < 1000000000000) {
            $milyar = (int)($angka / 1000000000);
            $sisa = $angka % 1000000000;
            return terbilang($milyar) . ' Milyar' . ($sisa > 0 ? ' ' . terbilang($sisa) : '');
        }
        
        return number_format($angka) . ' (terlalu besar)';
    }
}

// Helper function untuk format status
if (!function_exists('getStatusText')) {
    function getStatusText($status) {
        $statusMap = [
            'draft' => 'DRAFT / MENUNGGU PERSETUJUAN',
            'disetujui' => 'DISETUJUI',
            'ditolak' => 'DITOLAK',
            'on_progress' => 'ON PROGRESS',
            'selesai' => 'SELESAI',
            'batal' => 'BATAL'
        ];
        return $statusMap[$status] ?? strtoupper($status);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SPK - <?= htmlspecialchars($spk['nomor_spk'] ?? 'Surat Perintah Kerja') ?></title>
    <style>
        /* CSS sama seperti sebelumnya */
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
        
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 9pt;
        }
        
        .status-approved {
            background: #28a745;
            color: white;
        }
        
        .status-pending {
            background: #ffc107;
            color: #333;
        }
        
        .status-rejected {
            background: #dc3545;
            color: white;
        }
        
        .status-progress {
            background: #17a2b8;
            color: white;
        }
        
        .status-completed {
            background: #007bff;
            color: white;
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
            <h3>SURAT PERINTAH KERJA (SPK)</h3>
            <p>No. <?= htmlspecialchars($spk['nomor_spk'] ?? '-') ?></p>
        </div>

        <!-- Surat Info -->
        <div class="surat-info">
            Jakarta, <?= formatDateIndo($spk['created_at'] ?? date('Y-m-d')) ?>
        </div>

        <!-- Kepada Yth -->
        <div class="section">
            <div class="section-title">KEPADA YTH.</div>
            <table class="info-table">
                <tr>
                    <td width="150">Perusahaan</td>
                    <td>: <?= htmlspecialchars($spk['nama_perusahaan'] ?? '-') ?></td>
                </tr>
                <?php if (!empty($spk['client_alamat'])): ?>
                <tr>
                    <td>Alamat</td>
                    <td>: <?= nl2br(htmlspecialchars($spk['client_alamat'])) ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>

        <!-- Penanggung Jawab -->
        <div class="section">
            <div class="section-title">PENANGGUNG JAWAB</div>
            <table class="info-table">
                <tr>
                    <td width="150">Nama</td>
                    <td>: <?= htmlspecialchars($spk['penanggung_jawab_nama'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td>: <?= htmlspecialchars($spk['penanggung_jawab_jabatan'] ?? '-') ?></td>
                </tr>
            </table>
        </div>

        <!-- Pekerjaan -->
        <div class="section">
            <div class="section-title">PEKERJAAN</div>
            <table class="info-table">
                <tr>
                    <td width="150">Judul Pekerjaan</td>
                    <td>: <?= htmlspecialchars($spk['judul_pekerjaan'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td>Lokasi</td>
                    <td>: <?= htmlspecialchars($spk['lokasi_pekerjaan'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td>Uraian</td>
                    <td>: <?= nl2br(htmlspecialchars($spk['deskripsi_pekerjaan'] ?? '-')) ?></td>
                </tr>
            </table>
        </div>

        <!-- Waktu Pelaksanaan -->
        <div class="section">
            <div class="section-title">WAKTU PELAKSANAAN</div>
            <table class="info-table">
                <tr>
                    <td width="150">Tanggal Mulai</td>
                    <td>: <?= formatDateIndo($spk['tanggal_mulai'] ?? '') ?></td>
                </tr>
                <tr>
                    <td>Tanggal Selesai</td>
                    <td>: <?= formatDateIndo($spk['tanggal_selesai'] ?? '') ?></td>
                </tr>
            </table>
        </div>

        <!-- Nilai Kontrak -->
        <div class="section">
            <div class="section-title">NILAI KONTRAK</div>
            <table class="info-table">
                <tr>
                    <td width="150">Nilai Kontrak</td>
                    <td>: <?= formatCurrency($spk['nilai_kontrak'] ?? 0) ?></td>
                </tr>
                <tr>
                    <td>Terbilang</td>
                    <td>: <strong><?= terbilang(floor($spk['nilai_kontrak'] ?? 0)) ?> Rupiah</strong></td>
                </tr>
            </table>
        </div>

        <!-- Status -->
        <div class="section">
            <div class="section-title">STATUS SPK</div>
            <table class="info-table">
                <tr>
                    <td width="150">Status</td>
                    <td>: 
                        <span class="status-badge 
                            <?php 
                                $status = $spk['status'] ?? 'draft';
                                if ($status == 'disetujui') echo 'status-approved';
                                elseif ($status == 'ditolak') echo 'status-rejected';
                                elseif ($status == 'on_progress') echo 'status-progress';
                                elseif ($status == 'selesai') echo 'status-completed';
                                else echo 'status-pending';
                            ?>
                        ">
                            <?= getStatusText($status) ?>
                        </span>
                    </td>
                </tr>
                <?php if (!empty($spk['approved_by_name']) && ($status == 'disetujui' || $status == 'ditolak')): ?>
                <tr>
                    <td>Disetujui Oleh</td>
                    <td>: <?= htmlspecialchars($spk['approved_by_name']) ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>

        <!-- Tanda Tangan -->
        <div class="signature">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-name">Penerima Perintah,</div>
                <div class="signature-title">(<?= htmlspecialchars($spk['penanggung_jawab_nama'] ?? '_________________') ?>)</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-name">Pemberi Perintah,</div>
                <div class="signature-title">(<?= htmlspecialchars($spk['created_by_name'] ?? '_________________') ?>)</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-name">Mengetahui,</div>
                <div class="signature-title">(<?= htmlspecialchars($spk['approved_by_name'] ?? 'Cecep Trihardiyanto') ?>)</div>
                <div class="signature-title">Direktur</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            Dicetak: <?= date('d/m/Y H:i:s') ?>
        </div>
    </div>
</body>
</html>