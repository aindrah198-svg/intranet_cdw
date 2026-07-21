<?php
// app/Views/accounting/penggajian/slip-gaji-laporan/slip-gaji/batch-print.php
// Batch print multiple salary slips
$data['title'] = 'Batch Print Slip Gaji - ' . $bulanOptions[$bulan] . ' ' . $tahun;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Batch Print Slip Gaji - <?= $bulanOptions[$bulan] ?> <?= $tahun ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 10px;
            background: white;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .page-break:first-child {
            page-break-before: avoid;
        }
        
        .slip-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border: 1px solid #ddd;
            margin-bottom: 20px;
            page-break-after: always;
        }
        
        .slip-container:last-child {
            page-break-after: auto;
            margin-bottom: 0;
        }
        
        .slip-header {
            text-align: center;
            padding: 15px;
            border-bottom: 2px solid #333;
        }
        
        .company-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .company-address {
            font-size: 8px;
            color: #666;
        }
        
        .slip-title {
            font-size: 12px;
            font-weight: bold;
            margin-top: 8px;
            margin-bottom: 3px;
        }
        
        .slip-periode {
            font-size: 10px;
            color: #666;
        }
        
        .info-section {
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .info-table td {
            padding: 4px;
            vertical-align: top;
        }
        
        .info-table td:first-child {
            width: 30%;
            font-weight: bold;
        }
        
        .info-table td:last-child {
            width: 70%;
        }
        
        .detail-section {
            padding: 10px 15px;
        }
        
        .section-title {
            font-size: 11px;
            font-weight: bold;
            background-color: #f5f5f5;
            padding: 5px 8px;
            margin-bottom: 8px;
            border-left: 4px solid #28a745;
        }
        
        .section-title.potongan {
            border-left-color: #dc3545;
        }
        
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        .detail-table th,
        .detail-table td {
            border: 1px solid #ddd;
            padding: 4px 6px;
            text-align: left;
        }
        
        .detail-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 9px;
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
            padding: 8px;
            text-align: center;
            margin: 10px 15px;
            border-radius: 4px;
        }
        
        .gaji-bersih h3 {
            margin-bottom: 3px;
            font-size: 11px;
        }
        
        .gaji-bersih h2 {
            font-size: 16px;
            margin: 0;
        }
        
        .gaji-bersih small {
            font-size: 8px;
        }
        
        .kehadiran-section {
            padding: 10px 15px;
            border-top: 1px solid #eee;
        }
        
        .kehadiran-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
        }
        
        .kehadiran-item {
            flex: 1;
            min-width: 70px;
            text-align: center;
            padding: 5px;
            background-color: #f5f5f5;
            border-radius: 4px;
        }
        
        .kehadiran-item h4 {
            font-size: 12px;
            margin-bottom: 3px;
        }
        
        .kehadiran-item small {
            font-size: 8px;
            color: #666;
        }
        
        .signature-section {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            border-top: 1px solid #eee;
            margin-top: 5px;
        }
        
        .signature-box {
            text-align: center;
            width: 45%;
        }
        
        .signature-line {
            margin-top: 30px;
            border-top: 1px solid #000;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }
        
        .footer {
            text-align: center;
            padding: 8px;
            font-size: 7px;
            color: #999;
            border-top: 1px solid #eee;
        }
        
        .print-header {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            border-bottom: 2px solid #333;
        }
        
        .print-header h3 {
            font-size: 14px;
        }
        
        .print-header p {
            font-size: 10px;
            color: #666;
        }
        
        .print-button {
            text-align: center;
            margin: 20px;
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .print-button button {
            padding: 10px 20px;
            font-size: 12px;
            cursor: pointer;
            background-color: #4F81BD;
            color: white;
            border: none;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .print-button button:hover {
            background-color: #3a6ea5;
        }
        
        @media print {
            .print-button {
                display: none;
            }
            .slip-container {
                border: none;
                page-break-after: always;
                margin: 0;
                padding: 0;
            }
            .slip-container:last-child {
                page-break-after: auto;
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
            🖨️ Cetak Semua Slip Gaji
        </button>
    </div>
    
    <div class="print-header">
        <h3>LAPORAN SLIP GAJI BULANAN</h3>
        <p>Periode: <?= $bulanOptions[$bulan] ?> <?= $tahun ?></p>
        <p>Total Slip: <?= count($slipGaji) ?> slip | Total Karyawan: <?= count($slipGaji) ?> orang</p>
        <p>Total Gaji Bersih: Rp <?= number_format(array_sum(array_column($slipGaji, 'gaji_bersih')), 0, ',', '.') ?></p>
        <hr>
    </div>
    
    <?php $no = 1; foreach ($slipGaji as $index => $item): 
        // Hitung komponen pendapatan detail
        $pendapatanDetail = [
            'gaji_pokok' => $item['gaji_pokok'],
            'tunjangan_jabatan' => $item['tunjangan_jabatan'],
            'tunjangan_makan' => $item['tunjangan_makan'],
            'tunjangan_transport' => $item['tunjangan_transport'],
            'tunjangan_lainnya' => $item['tunjangan_lainnya'],
            'upah_lembur' => $item['upah_lembur']
        ];
        
        // Hitung komponen potongan detail
        $potonganDetail = [
            'bpjs_kes' => $item['potongan_bpjs_kes'],
            'bpjs_tk' => $item['potongan_bpjs_tk'],
            'pph21' => $item['potongan_pph21'],
            'absensi' => $item['potongan_absensi'],
            'kasbon' => $item['potongan_kasbon'],
            'lainnya' => $item['potongan_lainnya']
        ];
        
        // Data kehadiran
        $kehadiran = [
            'hari_kerja' => $item['total_hari_kerja'],
            'hadir' => $item['total_hadir'],
            'izin' => $item['total_izin'],
            'sakit' => $item['total_sakit'],
            'cuti' => $item['total_cuti'],
            'alpha' => $item['total_alpha'],
            'terlambat' => $item['total_terlambat'],
            'lembur' => $item['jam_lembur']
        ];
    ?>
    <div class="slip-container <?= $index > 0 ? 'page-break' : '' ?>">
        <!-- Header -->
        <div class="slip-header">
            <div class="company-name"><?= $perusahaan['nama_perusahaan'] ?? 'PT. CIPTA DUTA WACANA' ?></div>
            <div class="company-address">
                <?= $perusahaan['alamat'] ?? 'Beltway Office Park Tower B Lantai 5, Jl. TB Simatupang No. 41, Jakarta Selatan' ?>
            </div>
            <div class="slip-title">SLIP GAJI</div>
            <div class="slip-periode">
                Periode: <?= $bulanOptions[$bulan] ?> <?= $tahun ?>
            </div>
        </div>

        <!-- Informasi Karyawan -->
        <div class="info-section">
            <table class="info-table">
                <tr>
                    <td>NIK</td>
                    <td>: <?= $item['nik'] ?? '-' ?></td>
                    <td>Jabatan</td>
                    <td>: <?= $item['jabatan'] ?? '-' ?></td>
                </tr>
                <tr>
                    <td>Nama Karyawan</td>
                    <td>: <?= $item['nama_lengkap'] ?></td>
                    <td>Departemen</td>
                    <td>: <?= $item['departemen'] ?? '-' ?></td>
                </tr>
                <tr>
                    <td>Tanggal Masuk</td>
                    <td>: <?= $item['tanggal_masuk'] ?? '-' ?></td>
                    <td>Status Karyawan</td>
                    <td>: <?= $item['status_karyawan'] ?? '-' ?></td>
                </tr>
                <tr>
                    <td>Bank & No Rekening</td>
                    <td colspan="3">: <?= ($item['bank'] ?? '-') . ' - ' . ($item['no_rekening'] ?? '-') ?></td>
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
                        <td class="text-right"><strong><?= number_format($item['total_pendapatan'], 0, ',', '.') ?></strong></td>
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
                        <td class="text-right"><strong><?= number_format($item['total_potongan'], 0, ',', '.') ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Gaji Bersih -->
        <div class="gaji-bersih">
            <h3>GAJI BERSIH</h3>
            <h2>Rp <?= number_format($item['gaji_bersih'], 0, ',', '.') ?></h2>
            <small>Terbilang: <?= $this->terbilang($item['gaji_bersih']) ?> Rupiah</small>
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
        <?php if ($item['catatan']): ?>
        <div class="detail-section">
            <div class="section-title">CATATAN</div>
            <p style="background-color: #f8f9fa; padding: 6px; border-radius: 4px; font-size: 9px;"><?= nl2br($item['catatan']) ?></p>
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
                <p><?= $item['nama_lengkap'] ?></p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            Slip gaji ini adalah bukti resmi pembayaran gaji periode <?= $bulanOptions[$bulan] ?> <?= $tahun ?>
        </div>
    </div>
    <?php endforeach; ?>
    
    <script>
        // Auto print when page loads (optional - uncomment to auto print)
        // window.onload = function() {
        //     window.print();
        // }
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
?>