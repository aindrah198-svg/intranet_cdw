<?php
// app/Views/direktur/approval/pembelian_print.php

$pembelian = $pembelian ?? [];
$items = $items ?? [];

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

// Helper function untuk format currency
if (!function_exists('formatCurrency')) {
    function formatCurrency($amount) {
        if (empty($amount) || $amount == 0) {
            return '-';
        }
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

// Helper function untuk terbilang
if (!function_exists('terbilang')) {
    function terbilang($angka) {
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

// Helper function untuk prioritas text
if (!function_exists('getPrioritasText')) {
    function getPrioritasText($prioritas) {
        $map = [
            'Rendah' => 'RENDAH',
            'Normal' => 'NORMAL',
            'Tinggi' => 'TINGGI',
            'Urgent' => 'URGENT'
        ];
        return $map[$prioritas] ?? strtoupper($prioritas);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Purchase Request - <?= htmlspecialchars($pembelian['nomor_pr'] ?? 'PR') ?></title>
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
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .items-table th {
            background: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }
        
        .items-table td.number {
            text-align: right;
        }
        
        .items-table td.center {
            text-align: center;
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
        
        .priority-urgent {
            color: #dc3545;
            font-weight: bold;
        }
        
        .priority-high {
            color: #fd7e14;
            font-weight: bold;
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
        
        .grand-total {
            font-weight: bold;
            background: #f8f9fa;
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
            <h3>PURCHASE REQUEST (PR)</h3>
            <p>No. <?= htmlspecialchars($pembelian['nomor_pr'] ?? '-') ?></p>
        </div>

        <!-- Surat Info -->
        <div class="surat-info">
            Jakarta, <?= formatDateIndo($pembelian['tanggal_pengajuan'] ?? date('Y-m-d')) ?>
        </div>

        <!-- Data Pengaju -->
        <div class="section">
            <div class="section-title">A. DATA PENGAJU</div>
            <table class="info-table">
                <tr>
                    <td>NIK / NIP</td>
                    <td>: <?= htmlspecialchars($pembelian['nik'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td>Nama Lengkap</td>
                    <td>: <?= htmlspecialchars($pembelian['nama_lengkap'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td>: <?= htmlspecialchars($pembelian['jabatan'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td>Departemen</td>
                    <td>: <?= htmlspecialchars($pembelian['departemen'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td>Tanggal Masuk</td>
                    <td>: <?= formatDateIndo($pembelian['karyawan_tanggal_masuk'] ?? '') ?></td>
                </tr>
            </table>
        </div>

        <!-- Detail Purchase Request -->
        <div class="section">
            <div class="section-title">B. DETAIL PURCHASE REQUEST</div>
            <table class="info-table">
                <tr>
                    <td>Prioritas</td>
                    <td>: 
                        <span class="<?= ($pembelian['prioritas'] ?? '') === 'Urgent' ? 'priority-urgent' : (($pembelian['prioritas'] ?? '') === 'Tinggi' ? 'priority-high' : '') ?>">
                            <?= getPrioritasText($pembelian['prioritas'] ?? 'Normal') ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>Tanggal Dibutuhkan</td>
                    <td>: <?= !empty($pembelian['tanggal_dibutuhkan']) ? formatDateIndo($pembelian['tanggal_dibutuhkan']) : '-' ?></td>
                </tr>
                <tr>
                    <td>Alasan Pembelian</td>
                    <td>: <?= nl2br(htmlspecialchars($pembelian['alasan_pembelian'] ?? '-')) ?></td>
                </tr>
            </table>
        </div>

        <!-- Daftar Item -->
        <div class="section">
            <div class="section-title">C. DAFTAR ITEM</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Barang</th>
                        <th>Spesifikasi</th>
                        <th width="80">Qty</th>
                        <th width="80">Satuan</th>
                        <th width="130">Harga Estimasi</th>
                        <th width="130">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="7" class="center">Tidak ada item</td>
                    </tr>
                    <?php else: ?>
                        <?php $no = 1; $grandTotal = 0; ?>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td class="center"><?= $no++ ?></td>
                            <td><?= htmlspecialchars($item['nama_barang']) ?></td>
                            <td><?= nl2br(htmlspecialchars($item['spesifikasi'] ?? '-')) ?></td>
                            <td class="center"><?= number_format($item['qty'], 2) ?></td>
                            <td class="center"><?= htmlspecialchars($item['satuan'] ?? 'pcs') ?></td>
                            <td class="number"><?= formatCurrency($item['estimasi_harga'] ?? 0) ?></td>
                            <td class="number"><?= formatCurrency($item['total_estimasi'] ?? ($item['qty'] * $item['estimasi_harga'])) ?></td>
                        </tr>
                        <?php 
                            $grandTotal += ($item['total_estimasi'] ?? ($item['qty'] * $item['estimasi_harga']));
                        endforeach; ?>
                        <tr class="grand-total">
                            <td colspan="6" class="number"><strong>GRAND TOTAL</strong></td>
                            <td class="number"><strong><?= formatCurrency($grandTotal) ?></strong></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Informasi Supplier & PO -->
        <?php if (!empty($pembelian['supplier']) || !empty($pembelian['no_po_dibuat'])): ?>
        <div class="section">
            <div class="section-title">D. INFORMASI SUPPLIER & PEMESANAN</div>
            <table class="info-table">
                <?php if (!empty($pembelian['supplier'])): ?>
                <tr>
                    <td>Supplier</td>
                    <td>: <?= htmlspecialchars($pembelian['supplier']) ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($pembelian['no_po_dibuat'])): ?>
                <tr>
                    <td>No. PO</td>
                    <td>: <?= htmlspecialchars($pembelian['no_po_dibuat']) ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($pembelian['tanggal_pemesanan'])): ?>
                <tr>
                    <td>Tanggal Pemesanan</td>
                    <td>: <?= formatDateIndo($pembelian['tanggal_pemesanan']) ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($pembelian['tanggal_terima'])): ?>
                <tr>
                    <td>Tanggal Penerimaan</td>
                    <td>: <?= formatDateIndo($pembelian['tanggal_terima']) ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($pembelian['status_penerimaan'])): ?>
                <tr>
                    <td>Status Penerimaan</td>
                    <td>: <?= $pembelian['status_penerimaan'] === 'Lengkap' ? 'LENGKAP' : ($pembelian['status_penerimaan'] === 'Sebagian' ? 'SEBAGIAN' : 'BELUM DITERIMA') ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
        <?php endif; ?>

        <!-- Status Pengajuan -->
        <div class="section">
            <div class="section-title">E. STATUS PENGAJUAN</div>
            <table class="info-table">
                <tr>
                    <td>Status HRD</td>
                    <td>: 
                        <?php if (($pembelian['status_hrd'] ?? '') === 'Disetujui HRD'): ?>
                            <span class="status-badge status-approved">DISETUJUI</span>
                        <?php elseif (($pembelian['status_hrd'] ?? '') === 'Ditolak HRD'): ?>
                            <span class="status-badge status-rejected">DITOLAK</span>
                        <?php else: ?>
                            <span class="status-badge status-pending">MENUNGGU</span>
                        <?php endif; ?>
                        <?php if (!empty($pembelian['hrd_nama']) && ($pembelian['status_hrd'] ?? '') === 'Disetujui HRD'): ?>
                            <br><small>Oleh: <?= htmlspecialchars($pembelian['hrd_nama']) ?></small>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>Status Direktur</td>
                    <td>: 
                        <?php if (($pembelian['status_direktur'] ?? '') === 'Disetujui'): ?>
                            <span class="status-badge status-approved">DISETUJUI</span>
                        <?php elseif (($pembelian['status_direktur'] ?? '') === 'Ditolak'): ?>
                            <span class="status-badge status-rejected">DITOLAK</span>
                        <?php else: ?>
                            <span class="status-badge status-pending">MENUNGGU</span>
                        <?php endif; ?>
                        <?php if (!empty($pembelian['direktur_nama']) && ($pembelian['status_direktur'] ?? '') !== 'Menunggu'): ?>
                            <br><small>Oleh: <?= htmlspecialchars($pembelian['direktur_nama']) ?></small>
                            <br><small>Tanggal: <?= formatDateTime($pembelian['disetujui_direktur_at'] ?? '') ?></small>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>Status Keseluruhan</td>
                    <td>: <strong><?= strtoupper($pembelian['status_keseluruhan'] ?? 'DRAFT') ?></strong></td>
                </tr>
                <?php if (!empty($pembelian['alasan_penolakan_direktur'])): ?>
                <tr>
                    <td>Alasan Penolakan</td>
                    <td>: <?= nl2br(htmlspecialchars($pembelian['alasan_penolakan_direktur'])) ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($pembelian['catatan'])): ?>
                <tr>
                    <td>Catatan</td>
                    <td>: <?= nl2br(htmlspecialchars($pembelian['catatan'])) ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>

        <!-- Tanda Tangan -->
        <div class="signature">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-name">Pengaju,</div>
                <div class="signature-title">(<?= htmlspecialchars($pembelian['nama_panggilan'] ?? $pembelian['nama_lengkap'] ?? '_________________') ?>)</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-name">HRD,</div>
                <div class="signature-title">(<?= htmlspecialchars($pembelian['hrd_nama'] ?? '_________________') ?>)</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-name">Direktur,</div>
                <div class="signature-title">(<?= htmlspecialchars($pembelian['direktur_nama'] ?? 'Cecep Trihardiyanto') ?>)</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            Dokumen ini dicetak pada: <?= date('d/m/Y H:i:s') ?><br>
            Purchase Request ini adalah dokumen resmi PT. Cipta Duta Wacana (CDW Engineering)
        </div>
    </div>

    <script>
        // Auto print jika diperlukan (opsional, bisa diaktifkan)
        // setTimeout(function() { window.print(); }, 500);
    </script>
</body>
</html>