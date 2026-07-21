<?php
// app/Views/direktur/approval/izin_print.php

$izin = $izin ?? [];
$jamIzin = $jamIzin ?? '';

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

// Helper function untuk format waktu
if (!function_exists('formatTime')) {
    function formatTime($time) {
        if (empty($time)) return '-';
        $timestamp = strtotime($time);
        return $timestamp ? date('H:i', $timestamp) : '-';
    }
}

// Helper function untuk status text
if (!function_exists('getStatusText')) {
    function getStatusText($status) {
        $map = [
            'Menunggu' => 'MENUNGGU',
            'Disetujui' => 'DISETUJUI',
            'Ditolak' => 'DITOLAK',
            'Dibatalkan' => 'DIBATALKAN'
        ];
        return $map[$status] ?? strtoupper($status);
    }
}

// Helper function untuk jenis izin text
if (!function_exists('getJenisIzinText')) {
    function getJenisIzinText($jenis) {
        $map = [
            'Izin' => 'IZIN',
            'Sakit Ringan' => 'SAKIT RINGAN',
            'Keperluan Keluarga' => 'KEPERLUAN KELUARGA',
            'Keperluan Mendadak' => 'KEPERLUAN MENDAK',
            'Lainnya' => 'LAINNYA'
        ];
        return $map[$jenis] ?? strtoupper($jenis);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Izin - <?= htmlspecialchars($izin['nomor_izin'] ?? 'Pengajuan Izin') ?></title>
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
            <h3>FORMULIR PENGAJUAN IZIN</h3>
            <p>No. <?= htmlspecialchars($izin['nomor_izin'] ?? '-') ?></p>
        </div>

        <!-- Surat Info -->
        <div class="surat-info">
            Jakarta, <?= formatDateIndo($izin['tanggal_pengajuan'] ?? date('Y-m-d')) ?>
        </div>

        <!-- Data Karyawan -->
        <div class="section">
            <div class="section-title">A. DATA KARYAWAN</div>
            <table class="info-table">
                <tr>
                    <td>NIK / NIP</td>
                    <td>: <?= htmlspecialchars($izin['nik'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td>Nama Lengkap</td>
                    <td>: <?= htmlspecialchars($izin['nama_lengkap'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td>: <?= htmlspecialchars($izin['jabatan'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td>Departemen</td>
                    <td>: <?= htmlspecialchars($izin['departemen'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td>Tanggal Masuk</td>
                    <td>: <?= formatDateIndo($izin['karyawan_tanggal_masuk'] ?? '') ?></td>
                </tr>
            </table>
        </div>

        <!-- Detail Izin -->
        <div class="section">
            <div class="section-title">B. DETAIL IZIN</div>
            <table class="info-table">
                <tr>
                    <td>Jenis Izin</td>
                    <td>: <strong><?= getJenisIzinText($izin['jenis_izin'] ?? '-') ?></strong></td>
                </tr>
                <tr>
                    <td>Alasan Izin</td>
                    <td>: <?= nl2br(htmlspecialchars($izin['alasan'] ?? '-')) ?></td>
                </tr>
                <tr>
                    <td>Tanggal Mulai</td>
                    <td>: <?= formatDateIndo($izin['tanggal_mulai'] ?? '') ?></td>
                </tr>
                <tr>
                    <td>Tanggal Selesai</td>
                    <td>: <?= formatDateIndo($izin['tanggal_selesai'] ?? '') ?></td>
                </tr>
                <tr>
                    <td>Lama Izin</td>
                    <td>: <strong><?= ($izin['lama_hari'] ?? 0) . ' Hari' ?></strong></td>
                </tr>
                <?php if (!empty($izin['jam_keluar']) || !empty($izin['jam_kembali'])): ?>
                <tr>
                    <td>Jam Keluar</td>
                    <td>: <?= formatTime($izin['jam_keluar'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td>Jam Kembali</td>
                    <td>: <?= formatTime($izin['jam_kembali'] ?? '-') ?></td>
                </tr>
                <?php if (!empty($jamIzin)): ?>
                <tr>
                    <td>Durasi Izin</td>
                    <td>: <?= $jamIzin ?></td>
                </tr>
                <?php endif; ?>
                <?php endif; ?>
                <?php if (!empty($izin['dokumen_pendukung'])): ?>
                <tr>
                    <td>Dokumen Pendukung</td>
                    <td>: <a href="<?= base_url($izin['dokumen_pendukung']) ?>" target="_blank">Lihat Dokumen</a></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>

        <!-- Status Persetujuan -->
        <div class="section">
            <div class="section-title">C. STATUS PERSETUJUAN</div>
            <table class="info-table">
                <tr>
                    <td>Persetujuan Atasan</td>
                    <td>: 
                        <?php if (($izin['status_atasan'] ?? '') === 'Disetujui'): ?>
                            <span class="status-badge status-approved">DISETUJUI</span>
                            <?php if (!empty($izin['atasan_nama'])): ?>
                                <br><small>Oleh: <?= htmlspecialchars($izin['atasan_nama']) ?></small>
                                <br><small>Tanggal: <?= formatDateTime($izin['tanggal_disetujui_atasan'] ?? '') ?></small>
                            <?php endif; ?>
                        <?php elseif (($izin['status_atasan'] ?? '') === 'Ditolak'): ?>
                            <span class="status-badge status-rejected">DITOLAK</span>
                        <?php else: ?>
                            <span class="status-badge status-pending">MENUNGGU</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>Persetujuan HRD</td>
                    <td>: 
                        <?php if (($izin['status_hrd'] ?? '') === 'Disetujui'): ?>
                            <span class="status-badge status-approved">DISETUJUI</span>
                        <?php elseif (($izin['status_hrd'] ?? '') === 'Ditolak'): ?>
                            <span class="status-badge status-rejected">DITOLAK</span>
                        <?php else: ?>
                            <span class="status-badge status-pending">TIDAK DIPERLUKAN</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>Status Akhir</td>
                    <td>: <strong><?= getStatusText($izin['status_keseluruhan'] ?? 'Menunggu') ?></strong></td>
                </tr>
                <?php if (!empty($izin['alasan_penolakan_atasan']) && ($izin['status_keseluruhan'] ?? '') === 'Ditolak'): ?>
                <tr>
                    <td>Alasan Penolakan</td>
                    <td>: <?= nl2br(htmlspecialchars($izin['alasan_penolakan_atasan'])) ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($izin['catatan'])): ?>
                <tr>
                    <td>Catatan</td>
                    <td>: <?= nl2br(htmlspecialchars($izin['catatan'])) ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>

        <!-- Tanda Tangan -->
        <div class="signature">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-name">Pemohon,</div>
                <div class="signature-title">(<?= htmlspecialchars($izin['nama_panggilan'] ?? $izin['nama_lengkap'] ?? '_________________') ?>)</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-name">Atasan Langsung,</div>
                <div class="signature-title">(<?= htmlspecialchars($izin['atasan_nama'] ?? '_________________') ?>)</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-name">HRD,</div>
                <div class="signature-title">(<?= htmlspecialchars($izin['hrd_nama'] ?? '_________________') ?>)</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-name">Direktur,</div>
                <div class="signature-title">(<?= htmlspecialchars($izin['direktur_nama'] ?? 'Cecep Trihardiyanto') ?>)</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            Dokumen ini dicetak pada: <?= date('d/m/Y H:i:s') ?><br>
            Formulir Izin ini adalah dokumen resmi PT. Cipta Duta Wacana (CDW Engineering)
        </div>
    </div>

    <script>
        // Auto print jika diperlukan (opsional, bisa diaktifkan)
        // setTimeout(function() { window.print(); }, 500);
    </script>
</body>
</html>