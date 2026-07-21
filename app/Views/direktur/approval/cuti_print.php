<?php
// app/Views/direktur/approval/cuti_print.php

$cuti = $cuti ?? [];

// Helper function untuk format tanggal
if (!function_exists('formatDate')) {
    function formatDate($date) {
        if (empty($date)) return '-';
        $timestamp = strtotime($date);
        return $timestamp ? date('d/m/Y', $timestamp) : '-';
    }
}

// Helper function untuk format tanggal panjang (Indonesia)
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

// Helper function untuk terbilang hari
if (!function_exists('terbilangHari')) {
    function terbilangHari($days) {
        $days = (int)$days;
        if ($days <= 0) return '0 hari';
        
        $satuan = $days > 1 ? 'hari' : 'hari';
        return $days . ' ' . $satuan;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Pengajuan Cuti - <?= htmlspecialchars($cuti['nomor_cuti'] ?? 'Cuti Karyawan') ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', 'Arial', sans-serif;
            font-size: 12pt;
            line-height: 1.5;
            background: white;
            padding: 20px;
        }
        
        .print-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
        }
        
        /* Header */
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .header h1 {
            font-size: 18pt;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        
        .header h2 {
            font-size: 14pt;
            margin-bottom: 5px;
            font-weight: normal;
        }
        
        .header .subtitle {
            font-size: 10pt;
            color: #666;
        }
        
        /* Title */
        .title {
            text-align: center;
            margin-bottom: 25px;
        }
        
        .title h3 {
            font-size: 16pt;
            text-decoration: underline;
            margin-bottom: 5px;
        }
        
        /* Table */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .info-table tr td {
            padding: 8px 5px;
            vertical-align: top;
        }
        
        .info-table td:first-child {
            width: 180px;
            font-weight: bold;
        }
        
        .info-table td:last-child {
            border-bottom: 1px dotted #ccc;
        }
        
        /* Section */
        .section {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #333;
        }
        
        /* Timeline */
        .timeline-item {
            margin-bottom: 20px;
            padding-left: 20px;
            border-left: 3px solid #ddd;
        }
        
        .timeline-status {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .timeline-status.approved {
            color: #28a745;
        }
        
        .timeline-status.rejected {
            color: #dc3545;
        }
        
        .timeline-status.pending {
            color: #ffc107;
        }
        
        .timeline-date {
            font-size: 10pt;
            color: #666;
            margin-bottom: 8px;
        }
        
        .timeline-note {
            font-size: 10pt;
            font-style: italic;
            margin-top: 5px;
            color: #555;
        }
        
        /* Signature */
        .signature {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            text-align: center;
            width: 250px;
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
            font-size: 10pt;
            color: #666;
        }
        
        /* Footer */
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 9pt;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        
        /* Badge */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10pt;
            font-weight: normal;
        }
        
        .badge-success {
            background: #28a745;
            color: white;
        }
        
        .badge-danger {
            background: #dc3545;
            color: white;
        }
        
        .badge-warning {
            background: #ffc107;
            color: #333;
        }
        
        .badge-secondary {
            background: #6c757d;
            color: white;
        }
        
        /* Print styles */
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            
            .no-print {
                display: none;
            }
            
            .page-break {
                page-break-before: always;
            }
            
            .badge {
                border: 1px solid #ccc;
                background: none !important;
                color: #333 !important;
            }
            
            .timeline-status.approved {
                color: #000 !important;
            }
            
            .timeline-status.rejected {
                color: #000 !important;
            }
            
            .signature-line {
                border-top: 1px solid #000 !important;
            }
        }
        
        /* Button */
        .print-btn {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin-bottom: 20px;
            cursor: pointer;
            border: none;
            font-size: 12pt;
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
            <h3>FORMULIR PENGAJUAN CUTI</h3>
            <p>No. Pengajuan: <?= htmlspecialchars($cuti['nomor_cuti'] ?? '-') ?></p>
        </div>

        <!-- Informasi Karyawan -->
        <div class="section">
            <div class="section-title">A. DATA KARYAWAN</div>
            <table class="info-table">
                <tr>
                    <td>NIK / NIP</td>
                    <td>: <?= htmlspecialchars($cuti['nik'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td>Nama Lengkap</td>
                    <td>: <?= htmlspecialchars($cuti['nama_lengkap'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td>: <?= htmlspecialchars($cuti['jabatan'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td>Departemen</td>
                    <td>: <?= htmlspecialchars($cuti['departemen'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td>Tanggal Masuk</td>
                    <td>: <?= formatDateIndo($cuti['karyawan_tanggal_masuk'] ?? '') ?></td>
                </tr>
            </table>
        </div>

        <!-- Detail Cuti -->
        <div class="section">
            <div class="section-title">B. DETAIL CUTI</div>
            <table class="info-table">
                <tr>
                    <td>Jenis Cuti</td>
                    <td>: <?= htmlspecialchars($cuti['jenis_cuti'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td>Tanggal Mulai</td>
                    <td>: <?= formatDateIndo($cuti['tanggal_mulai'] ?? '') ?></td>
                </tr>
                <tr>
                    <td>Tanggal Selesai</td>
                    <td>: <?= formatDateIndo($cuti['tanggal_selesai'] ?? '') ?></td>
                </tr>
                <tr>
                    <td>Lama Cuti</td>
                    <td>: <?= terbilangHari($cuti['lama_hari'] ?? 0) ?></td>
                </tr>
                <tr>
                    <td>Sisa Cuti Tahunan</td>
                    <td>: <?= ($cuti['sisa_cuti_tahunan'] ?? 12) . ' Hari' ?></td>
                </tr>
                <tr>
                    <td>Alasan Cuti</td>
                    <td>: <?= nl2br(htmlspecialchars($cuti['alasan'] ?? '-')) ?></td>
                </tr>
                <?php if (!empty($cuti['alamat_selama_cuti'])): ?>
                <tr>
                    <td>Alamat Selama Cuti</td>
                    <td>: <?= nl2br(htmlspecialchars($cuti['alamat_selama_cuti'])) ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($cuti['no_telepon_cuti'])): ?>
                <tr>
                    <td>No. Telepon Cuti</td>
                    <td>: <?= htmlspecialchars($cuti['no_telepon_cuti']) ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td>Pejabat Penerima Tugas</td>
                    <td>: <?= htmlspecialchars($cuti['pejabat_penerima_tugas'] ?? '-') ?></td>
                </tr>
            </table>
        </div>

        <!-- Riwayat Persetujuan -->
        <div class="section">
            <div class="section-title">C. RIWAYAT PERSETUJUAN</div>
            
            <!-- Atasan -->
            <div class="timeline-item">
                <div class="timeline-status <?= ($cuti['status_atasan'] ?? '') === 'Disetujui' ? 'approved' : (($cuti['status_atasan'] ?? '') === 'Ditolak' ? 'rejected' : 'pending') ?>">
                    <strong>Persetujuan Atasan</strong>
                    <?php if (($cuti['status_atasan'] ?? '') === 'Disetujui'): ?>
                        <span class="badge badge-success">✓ DISETUJUI</span>
                    <?php elseif (($cuti['status_atasan'] ?? '') === 'Ditolak'): ?>
                        <span class="badge badge-danger">✗ DITOLAK</span>
                    <?php else: ?>
                        <span class="badge badge-warning">⌛ MENUNGGU</span>
                    <?php endif; ?>
                </div>
                <?php if (!empty($cuti['atasan_nama'])): ?>
                <div class="timeline-date">Oleh: <?= htmlspecialchars($cuti['atasan_nama']) ?></div>
                <?php endif; ?>
                <?php if (!empty($cuti['tanggal_disetujui_atasan'])): ?>
                <div class="timeline-date">Tanggal: <?= formatDateTime($cuti['tanggal_disetujui_atasan']) ?></div>
                <?php endif; ?>
                <?php if (!empty($cuti['alasan_penolakan_atasan'])): ?>
                <div class="timeline-note">Catatan: <?= htmlspecialchars($cuti['alasan_penolakan_atasan']) ?></div>
                <?php endif; ?>
            </div>

            <!-- HRD -->
            <div class="timeline-item">
                <div class="timeline-status <?= ($cuti['status_hrd'] ?? '') === 'Disetujui' ? 'approved' : (($cuti['status_hrd'] ?? '') === 'Ditolak' ? 'rejected' : 'pending') ?>">
                    <strong>Persetujuan HRD</strong>
                    <?php if (($cuti['status_hrd'] ?? '') === 'Disetujui'): ?>
                        <span class="badge badge-success">✓ DISETUJUI</span>
                    <?php elseif (($cuti['status_hrd'] ?? '') === 'Ditolak'): ?>
                        <span class="badge badge-danger">✗ DITOLAK</span>
                    <?php else: ?>
                        <span class="badge badge-warning">⌛ MENUNGGU</span>
                    <?php endif; ?>
                </div>
                <?php if (!empty($cuti['hrd_nama'])): ?>
                <div class="timeline-date">Oleh: <?= htmlspecialchars($cuti['hrd_nama']) ?></div>
                <?php endif; ?>
                <?php if (!empty($cuti['tanggal_disetujui_hrd'])): ?>
                <div class="timeline-date">Tanggal: <?= formatDateTime($cuti['tanggal_disetujui_hrd']) ?></div>
                <?php endif; ?>
                <?php if (!empty($cuti['catatan_hrd'])): ?>
                <div class="timeline-note">Catatan: <?= htmlspecialchars($cuti['catatan_hrd']) ?></div>
                <?php endif; ?>
                <?php if (!empty($cuti['alasan_penolakan_hrd'])): ?>
                <div class="timeline-note">Catatan: <?= htmlspecialchars($cuti['alasan_penolakan_hrd']) ?></div>
                <?php endif; ?>
            </div>

            <!-- Direktur -->
            <div class="timeline-item">
                <div class="timeline-status <?= ($cuti['status_direktur'] ?? '') === 'Disetujui' ? 'approved' : (($cuti['status_direktur'] ?? '') === 'Ditolak' ? 'rejected' : 'pending') ?>">
                    <strong>Persetujuan Direktur</strong>
                    <?php if (($cuti['status_direktur'] ?? '') === 'Disetujui'): ?>
                        <span class="badge badge-success">✓ DISETUJUI</span>
                    <?php elseif (($cuti['status_direktur'] ?? '') === 'Ditolak'): ?>
                        <span class="badge badge-danger">✗ DITOLAK</span>
                    <?php else: ?>
                        <span class="badge badge-warning">⌛ MENUNGGU</span>
                    <?php endif; ?>
                </div>
                <?php if (!empty($cuti['direktur_nama'])): ?>
                <div class="timeline-date">Oleh: <?= htmlspecialchars($cuti['direktur_nama']) ?></div>
                <?php endif; ?>
                <?php if (!empty($cuti['tanggal_disetujui_direktur'])): ?>
                <div class="timeline-date">Tanggal: <?= formatDateTime($cuti['tanggal_disetujui_direktur']) ?></div>
                <?php endif; ?>
                <?php if (!empty($cuti['alasan_penolakan_direktur'])): ?>
                <div class="timeline-note">Catatan: <?= htmlspecialchars($cuti['alasan_penolakan_direktur']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Status Akhir -->
        <?php if (($cuti['status_pengajuan'] ?? '') === 'Disetujui'): ?>
        <div class="section">
            <div class="section-title">D. STATUS PENGAJUAN</div>
            <table class="info-table">
                <tr>
                    <td>Status Akhir</td>
                    <td>: <strong style="color: #28a745;">DISETUJUI</strong></td>
                </tr>
                <tr>
                    <td>Tanggal Disetujui</td>
                    <td>: <?= formatDateTime($cuti['tanggal_disetujui_direktur'] ?? '-') ?></td>
                </tr>
            </table>
        </div>
        <?php elseif (($cuti['status_pengajuan'] ?? '') === 'Ditolak'): ?>
        <div class="section">
            <div class="section-title">D. STATUS PENGAJUAN</div>
            <table class="info-table">
                <tr>
                    <td>Status Akhir</td>
                    <td>: <strong style="color: #dc3545;">DITOLAK</strong></td>
                </tr>
                <tr>
                    <td>Alasan Penolakan</td>
                    <td>: <?= nl2br(htmlspecialchars($cuti['alasan_penolakan_direktur'] ?? $cuti['alasan_penolakan_hrd'] ?? $cuti['alasan_penolakan_atasan'] ?? '-')) ?></td>
                </tr>
            </table>
        </div>
        <?php endif; ?>

        <!-- Tanda Tangan -->
        <div class="signature">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-name">Pemohon,</div>
                <div class="signature-title">(<?= htmlspecialchars($cuti['nama_panggilan'] ?? $cuti['nama_lengkap'] ?? '_________________') ?>)</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-name">Atasan Langsung,</div>
                <div class="signature-title">(<?= htmlspecialchars($cuti['atasan_nama'] ?? '_________________') ?>)</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-name">HRD,</div>
                <div class="signature-title">(<?= htmlspecialchars($cuti['hrd_nama'] ?? '_________________') ?>)</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-name">Direktur,</div>
                <div class="signature-title">(<?= htmlspecialchars($cuti['direktur_nama'] ?? 'Cecep Trihardiyanto') ?>)</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            Dokumen ini dicetak pada: <?= date('d/m/Y H:i:s') ?><br>
            Dokumen ini adalah bukti resmi pengajuan cuti PT. Cipta Duta Wacana (CDW Engineering)
        </div>
    </div>

    <script>
        // Auto print jika diperlukan (opsional, bisa diaktifkan)
        // setTimeout(function() { window.print(); }, 500);
    </script>
</body>
</html>