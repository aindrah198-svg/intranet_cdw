<?php
// app/Views/accounting/penggajian/slip-gaji-laporan/slip-gaji/print.php
// This is a print-optimized version without sidebar, navbar, etc.
$data['title'] = 'Slip Gaji - ' . $perhitungan['nama_karyawan'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - <?= $perhitungan['nama_karyawan'] ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 11px;
            background: white;
            padding: 20px;
        }
        
        .slip-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .slip-header {
            text-align: center;
            padding: 20px;
            border-bottom: 2px solid #333;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .company-address {
            font-size: 9px;
            color: #666;
        }
        
        .slip-title {
            font-size: 14px;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 5px;
        }
        
        .slip-periode {
            font-size: 11px;
            color: #666;
        }
        
        .info-section {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        
        .info-table td:first-child {
            width: 35%;
            font-weight: bold;
        }
        
        .info-table td:last-child {
            width: 65%;
        }
        
        .detail-section {
            padding: 15px;
        }
        
        .section-title {
            font-size: 12px;
            font-weight: bold;
            background-color: #f5f5f5;
            padding: 8px;
            margin-bottom: 10px;
            border-left: 4px solid #28a745;
        }
        
        .section-title.potongan {
            border-left-color: #dc3545;
        }
        
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .detail-table th,
        .detail-table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        
        .detail-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        
        .detail-table td.text-right,
        .detail-table th.text-right {
            text-align: right;
        }
        
        .total-row {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        
        .gaji-bersih {
            background-color: #28a745;
            color: white;
            padding: 10px;
            text-align: center;
            margin: 15px 0;
            border-radius: 5px;
        }
        
        .gaji-bersih h3 {
            margin-bottom: 5px;
            font-size: 14px;
        }
        
        .gaji-bersih h2 {
            font-size: 20px;
            margin: 0;
        }
        
        .gaji-bersih small {
            font-size: 9px;
        }
        
        .kehadiran-section {
            padding: 15px;
            border-top: 1px solid #eee;
        }
        
        .kehadiran-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        
        .kehadiran-item {
            flex: 1;
            min-width: 80px;
            text-align: center;
            padding: 8px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }
        
        .kehadiran-item h4 {
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .kehadiran-item small {
            font-size: 9px;
            color: #666;
        }
        
        .signature-section {
            display: flex;
            justify-content: space-between;
            padding: 20px 15px;
            border-top: 1px solid #eee;
            margin-top: 10px;
        }
        
        .signature-box {
            text-align: center;
            width: 45%;
        }
        
        .signature-line {
            margin-top: 40px;
            border-top: 1px solid #000;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }
        
        .footer {
            text-align: center;
            padding: 10px;
            font-size: 8px;
            color: #999;
            border-top: 1px solid #eee;
        }
        
        .print-button {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .print-button button {
            padding: 8px 20px;
            font-size: 12px;
            cursor: pointer;
            background-color: #4F81BD;
            color: white;
            border: none;
            border-radius: 4px;
        }
        
        .print-button button:hover {
            background-color: #3a6ea5;
        }
        
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            .print-button {
                display: none;
            }
            .slip-container {
                border: none;
                box-shadow: none;
            }
            @page {
                size: A4;
                margin: 1.2cm;
            }
        }
    </style>
</head>
<body>
    <div class="print-button">
        <button onclick="window.print()">
            <i class="fas fa-print"></i> Cetak Slip Gaji
        </button>
    </div>
    
    <div class="slip-container">
        <!-- Header -->
        <div class="slip-header">
            <div class="company-name"><?= $perusahaan['nama_perusahaan'] ?? 'PT. CIPTA DUTA WACANA' ?></div>
            <div class="company-address">
                <?= $perusahaan['alamat'] ?? 'Beltway Office Park Tower B Lantai 5, Jl. TB Simatupang No. 41, Jakarta Selatan' ?>
                <br>
                Telp: <?= $perusahaan['telepon'] ?? '(+62-21) 29857462' ?> | Email: <?= $perusahaan['email'] ?? 'info@cdw-engineering.com' ?>
            </div>
            <div class="slip-title">SLIP GAJI</div>
            <div class="slip-periode">
                Periode: <?= $this->getNamaBulan($perhitungan['periode_bulan']) ?> <?= $perhitungan['periode_tahun'] ?>
            </div>
        </div>

        <!-- Informasi Karyawan -->
        <div class="info-section">
            <table class="info-table">
                <tr>
                    <td>NIK</td>
                    <td>: <?= $perhitungan['nomor_karyawan'] ?? $perhitungan['nik'] ?? '-' ?></td>
                    <td>Jabatan</td>
                    <td>: <?= $perhitungan['jabatan'] ?? '-' ?></td>
                </tr>
                <tr>
                    <td>Nama Karyawan</td>
                    <td>: <?= $perhitungan['nama_karyawan'] ?></td>
                    <td>Departemen</td>
                    <td>: <?= $perhitungan['departemen'] ?? '-' ?></td>
                </tr>
                <tr>
                    <td>Tanggal Masuk</td>
                    <td>: <?= $perhitungan['tanggal_masuk'] ?? '-' ?></td>
                    <td>Status Karyawan</td>
                    <td>: <?= $perhitungan['status_karyawan'] ?? '-' ?></td>
                </tr>
                <tr>
                    <td>Bank & No Rekening</td>
                    <td>: <?= ($perhitungan['bank'] ?? '-') . ' - ' . ($perhitungan['no_rekening'] ?? '-') ?></td>
                    <td>Nomor Perhitungan</td>
                    <td>: <?= $perhitungan['nomor_perhitungan'] ?></td>
                </tr>
            </table>
        </div>

        <!-- Detail Pendapatan -->
        <div class="detail-section">
            <div class="section-title">DETAIL PENDAPATAN</div>
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>Komponen</th>
                        <th class="text-right">Jumlah (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Gaji Pokok</td>
                        <td class="text-right"><?= number_format($pendapatanDetail['gaji_pokok'], 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>Tunjangan Jabatan</td>
                        <td class="text-right"><?= number_format($pendapatanDetail['tunjangan_jabatan'], 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>Tunjangan Makan</td>
                        <td class="text-right"><?= number_format($pendapatanDetail['tunjangan_makan'], 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>Tunjangan Transport</td>
                        <td class="text-right"><?= number_format($pendapatanDetail['tunjangan_transport'], 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>Tunjangan Lainnya</td>
                        <td class="text-right"><?= number_format($pendapatanDetail['tunjangan_lainnya'], 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>Upah Lembur</td>
                        <td class="text-right"><?= number_format($pendapatanDetail['upah_lembur'], 0, ',', '.') ?></td>
                    </tr>
                    <tr class="total-row">
                        <td><strong>TOTAL PENDAPATAN</strong></td>
                        <td class="text-right"><strong><?= number_format($perhitungan['total_pendapatan'], 0, ',', '.') ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Detail Potongan -->
        <div class="detail-section">
            <div class="section-title potongan">DETAIL POTONGAN</div>
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>Komponen</th>
                        <th class="text-right">Jumlah (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>BPJS Kesehatan</td>
                        <td class="text-right"><?= number_format($potonganDetail['bpjs_kes'], 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>BPJS Ketenagakerjaan</td>
                        <td class="text-right"><?= number_format($potonganDetail['bpjs_tk'], 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>PPh 21</td>
                        <td class="text-right"><?= number_format($potonganDetail['pph21'], 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>Potongan Absensi</td>
                        <td class="text-right"><?= number_format($potonganDetail['absensi'], 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>Potongan Kasbon</td>
                        <td class="text-right"><?= number_format($potonganDetail['kasbon'], 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>Potongan Lainnya</td>
                        <td class="text-right"><?= number_format($potonganDetail['lainnya'], 0, ',', '.') ?></td>
                    </tr>
                    <tr class="total-row">
                        <td><strong>TOTAL POTONGAN</strong></td>
                        <td class="text-right"><strong><?= number_format($perhitungan['total_potongan'], 0, ',', '.') ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Gaji Bersih -->
        <div class="gaji-bersih">
            <h3>GAJI BERSIH</h3>
            <h2>Rp <?= number_format($perhitungan['gaji_bersih'], 0, ',', '.') ?></h2>
            <small>Terbilang: <?= $this->terbilang($perhitungan['gaji_bersih']) ?> Rupiah</small>
        </div>

        <!-- Data Kehadiran -->
        <?php if ($kehadiran['hari_kerja'] > 0 || $kehadiran['hadir'] > 0): ?>
        <div class="kehadiran-section">
            <div class="section-title">RINGKASAN KEHADIRAN</div>
            <div class="kehadiran-grid">
                <div class="kehadiran-item">
                    <h4><?= $kehadiran['hari_kerja'] ?></h4>
                    <small>Hari Kerja</small>
                </div>
                <div class="kehadiran-item" style="background-color: #28a745; color: white;">
                    <h4><?= $kehadiran['hadir'] ?></h4>
                    <small>Hadir</small>
                </div>
                <div class="kehadiran-item" style="background-color: #ffc107;">
                    <h4><?= $kehadiran['izin'] ?></h4>
                    <small>Izin</small>
                </div>
                <div class="kehadiran-item" style="background-color: #17a2b8; color: white;">
                    <h4><?= $kehadiran['sakit'] ?></h4>
                    <small>Sakit</small>
                </div>
                <div class="kehadiran-item">
                    <h4><?= $kehadiran['cuti'] ?></h4>
                    <small>Cuti</small>
                </div>
                <div class="kehadiran-item" style="background-color: #dc3545; color: white;">
                    <h4><?= $kehadiran['alpha'] ?></h4>
                    <small>Alpha</small>
                </div>
                <div class="kehadiran-item">
                    <h4><?= $kehadiran['terlambat'] ?></h4>
                    <small>Terlambat (hari)</small>
                </div>
                <div class="kehadiran-item">
                    <h4><?= number_format($kehadiran['lembur'], 1) ?></h4>
                    <small>Jam Lembur</small>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Catatan -->
        <?php if ($perhitungan['catatan']): ?>
        <div class="detail-section">
            <div class="section-title">CATATAN</div>
            <p style="background-color: #f8f9fa; padding: 8px; border-radius: 4px;"><?= nl2br($perhitungan['catatan']) ?></p>
        </div>
        <?php endif; ?>

        <!-- Tanda Tangan -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line"></div>
                <p>Hormat Kami,</p>
                <p>HRD / Accounting</p>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <p>Diterima oleh,</p>
                <p><?= $perhitungan['nama_karyawan'] ?></p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            Slip gaji ini adalah bukti resmi pembayaran gaji periode <?= $this->getNamaBulan($perhitungan['periode_bulan']) ?> <?= $perhitungan['periode_tahun'] ?><br>
            Dicetak pada: <?= date('d/m/Y H:i:s') ?>
        </div>
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.print();
    </script>
</body>
</html>

<?php
// Helper function untuk terbilang
function terbilang($angka) {
    $angka = (float)$angka;
    $bilangan = array(
        '', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'
    );
    
    if ($angka < 12) {
        return $bilangan[$angka];
    } elseif ($angka < 20) {
        return $bilangan[$angka - 10] . ' Belas';
    } elseif ($angka < 100) {
        $puluh = floor($angka / 10);
        $satuan = $angka % 10;
        return $bilangan[$puluh] . ' Puluh ' . $bilangan[$satuan];
    } elseif ($angka < 200) {
        return 'Seratus ' . terbilang($angka - 100);
    } elseif ($angka < 1000) {
        $ratus = floor($angka / 100);
        $sisa = $angka % 100;
        return $bilangan[$ratus] . ' Ratus ' . terbilang($sisa);
    } elseif ($angka < 2000) {
        return 'Seribu ' . terbilang($angka - 1000);
    } elseif ($angka < 1000000) {
        $ribu = floor($angka / 1000);
        $sisa = $angka % 1000;
        return terbilang($ribu) . ' Ribu ' . terbilang($sisa);
    } elseif ($angka < 1000000000) {
        $juta = floor($angka / 1000000);
        $sisa = $angka % 1000000;
        return terbilang($juta) . ' Juta ' . terbilang($sisa);
    } elseif ($angka < 1000000000000) {
        $miliar = floor($angka / 1000000000);
        $sisa = $angka % 1000000000;
        return terbilang($miliar) . ' Miliar ' . terbilang($sisa);
    }
    return '';
}

// Helper function untuk get nama bulan
function getNamaBulan($bulan) {
    $bulanNames = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    return $bulanNames[$bulan] ?? '';
}
?>