<?php
// C:\xampp\htdocs\cdwnet\app\Views\admin\kontrak\print.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <style>
        /* Reset CSS untuk print */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            background: #fff;
            padding: 20px;
        }
        
        /* Page Setup */
        @page {
            margin: 2cm 1.5cm;
            size: A4 portrait;
        }
        
        /* Header Kontrak */
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #000;
        }
        
        .header h1 {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        
        .header h2 {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .nomor-kontrak {
            font-size: 12pt;
            font-weight: bold;
            margin-top: 10px;
        }
        
        /* Content */
        .content {
            margin-bottom: 30px;
        }
        
        /* Pihak */
        .pihak {
            margin-bottom: 30px;
        }
        
        .pihak h3 {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 10px;
            text-decoration: underline;
        }
        
        .pihak-info {
            margin-left: 20px;
            margin-bottom: 15px;
        }
        
        .pihak-info p {
            margin-bottom: 5px;
        }
        
        /* Pasal */
        .pasal {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        
        .pasal h4 {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .pasal h5 {
            font-size: 12pt;
            font-weight: bold;
            margin: 10px 0 5px 20px;
        }
        
        .pasal p {
            margin-bottom: 8px;
            text-align: justify;
        }
        
        .pasal ol, .pasal ul {
            margin-left: 40px;
            margin-bottom: 10px;
        }
        
        .pasal li {
            margin-bottom: 5px;
        }
        
        /* Table */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        
        .table th, .table td {
            border: 1px solid #000;
            padding: 5px 8px;
            text-align: left;
            vertical-align: top;
        }
        
        .table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        
        /* Tanda Tangan */
        .signature {
            margin-top: 50px;
            page-break-inside: avoid;
        }
        
        .signature-table {
            width: 100%;
            border: none;
        }
        
        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
            padding: 0 20px;
        }
        
        .signature-space {
            height: 80px;
            margin-bottom: 5px;
            border-bottom: 1px solid #000;
        }
        
        /* Footer */
        .footer {
            margin-top: 30px;
            font-size: 10pt;
            text-align: center;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
        
        /* Utility Classes */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .text-underline { text-decoration: underline; }
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 10px; }
        .mb-3 { margin-bottom: 15px; }
        .mt-1 { margin-top: 5px; }
        .mt-2 { margin-top: 10px; }
        .mt-3 { margin-top: 15px; }
        
        /* Print Specific */
        @media print {
            body {
                padding: 0;
            }
            
            .no-print {
                display: none !important;
            }
            
            .page-break {
                page-break-before: always;
            }
            
            .keep-together {
                page-break-inside: avoid;
            }
            
            .footer {
                position: fixed;
                bottom: 0;
                width: 100%;
            }
        }
        
        /* Non-print elements */
        .print-actions {
            background: #f5f5f5;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            text-align: center;
        }
        
        .print-actions button {
            margin: 0 5px;
            padding: 8px 15px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- Print Actions (Hanya tampil di browser) -->
    <div class="print-actions no-print">
        <button onclick="window.print()" class="btn-print">
            <i class="fas fa-print"></i> Cetak
        </button>
        <button onclick="window.close()" class="btn-close">
            <i class="fas fa-times"></i> Tutup
        </button>
        <button onclick="saveAsPDF()" class="btn-pdf">
            <i class="fas fa-file-pdf"></i> Simpan sebagai PDF
        </button>
    </div>

    <!-- Header Kontrak -->
    <div class="header">
        <h1>SURAT PERJANJIAN KERJA</h1>
        <h2><?= esc($kontrak['jenis_kontrak']) ?></h2>
        <div class="nomor-kontrak">Nomor: <?= esc($kontrak['nomor_kontrak']) ?></div>
    </div>

    <!-- Pihak-pihak yang terikat perjanjian -->
    <div class="pihak">
        <div class="text-center mb-3">
            <p>Yang bertanda tangan di bawah ini:</p>
        </div>
        
        <div class="pihak-info">
            <h3 class="text-underline">PIHAK PERTAMA</h3>
            <p>Nama &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: 
               <strong><?= esc($kontrak['pihak_pertama_nama'] ?? 'PT. Cipta Duta Wacana') ?></strong>
            </p>
            <p>Jabatan &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: 
               <?= esc($kontrak['pihak_pertama_jabatan'] ?? 'Direktur') ?>
            </p>
            <p>Alamat &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: 
               <?= nl2br(esc($kontrak['pihak_pertama_alamat'] ?? 'Villa Bintaro Regency Blok K1 No. 2 Pondok Kacang Timur, Tangerang Selatan 15226')) ?>
            </p>
            <p class="mt-2">Dalam hal ini bertindak untuk dan atas nama pribadi dan pimpinan perusahaan dan selanjutnya disebut <strong>PIHAK PERTAMA</strong>.</p>
        </div>
        
        <div class="pihak-info">
            <h3 class="text-underline">PIHAK KEDUA</h3>
            <p>Nama &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: 
               <strong><?= esc($kontrak['pihak_kedua_nama'] ?? $kontrak['nama_lengkap'] ?? '-') ?></strong>
            </p>
            <p>Jabatan &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: 
               <?= esc($kontrak['pihak_kedua_jabatan'] ?? $kontrak['jabatan'] ?? '-') ?>
            </p>
            <p>Alamat &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: 
               <?= nl2br(esc($kontrak['pihak_kedua_alamat'] ?? $kontrak['alamat'] ?? '-')) ?>
            </p>
            <p class="mt-2">Dalam hal ini bertindak untuk dan atas nama diri pribadi dan selanjutnya disebut <strong>PIHAK KEDUA</strong>.</p>
        </div>
        
        <div class="text-center mt-3">
            <p>Kedua belah pihak menyepakati perjanjian kerja dengan uraian sebagai berikut:</p>
        </div>
    </div>

    <!-- PASAL 1: MASA KERJA -->
    <div class="pasal">
        <h4 class="text-center">PASAL 1<br>MASA KERJA</h4>
        
        <h5>Ayat 1</h5>
        <p><strong>PIHAK PERTAMA</strong> menyatakan menerima <strong>PIHAK KEDUA</strong> sebagai karyawan 
           <?= $kontrak['jenis_kontrak'] == 'Probation' ? 'percobaan (Probation Period)' : '' ?> 
           selama <?= $kontrak['masa_kerja_bulan'] ? $kontrak['masa_kerja_bulan'] . ' (' . terbilang($kontrak['masa_kerja_bulan']) . ')' : 'tidak ditentukan' ?> 
           Bulan di perusahaan PT. Cipta Duta Wacana yang berkedudukan di <?= esc($kontrak['lokasi_kerja'] ?? 'Beltway Office Park Tower B Lantai 5. JL. TB Simatupang No. 41 Kecamatan Pasar Minggu Jakarta Selatan') ?> 
           dan <strong>PIHAK KEDUA</strong> dengan ini menyatakan kesediaannya.</p>
        
        <h5>Ayat 2</h5>
        <p>Perjanjian kerja ini berlaku untuk jangka waktu <?= $kontrak['masa_kerja_bulan'] ? $kontrak['masa_kerja_bulan'] . ' (' . terbilang($kontrak['masa_kerja_bulan']) . ')' : 'tidak ditentukan' ?> 
           bulan dimulai sejak tanggal <?= date('d F Y', strtotime($kontrak['tanggal_mulai'])) ?> 
           <?php if($kontrak['tanggal_selesai']): ?>
           sampai dengan Tanggal <?= date('d F Y', strtotime($kontrak['tanggal_selesai'])) ?>
           <?php endif; ?>.</p>
        
        <?php if($kontrak['masa_percobaan_bulan']): ?>
        <h5>Ayat 3</h5>
        <p>Masa percobaan (probation) berlaku selama <?= $kontrak['masa_percobaan_bulan'] ?> 
           (<?= terbilang($kontrak['masa_percobaan_bulan']) ?>) bulan terhitung sejak tanggal mulai kerja.</p>
        <?php endif; ?>
        
        <h5>Ayat <?= $kontrak['masa_percobaan_bulan'] ? '4' : '3' ?></h5>
        <p>Selama jangka waktu perjanjian kerja ini masing-masing pihak dapat memutuskan hubungan kerja dengan pemberitahuan secara tertulis minimal 
           <?= $kontrak['pemberitahuan_pemutusan_hari'] ?? '30' ?> (<?= terbilang($kontrak['pemberitahuan_pemutusan_hari'] ?? 30) ?>) hari kerja.</p>
    </div>

    <!-- PASAL 2: TATA TERTIB PERUSAHAAN -->
    <div class="pasal">
        <h4 class="text-center">PASAL 2<br>TATA TERTIB PERUSAHAAN</h4>
        <p><strong>PIHAK KEDUA</strong> menyatakan kesediaannya untuk mematuhi serta mentaati seluruh peraturan tata tertib perusahaan yang telah ditetapkan <strong>PIHAK PERTAMA</strong>.</p>
    </div>

    <!-- PASAL 3: JAM KERJA -->
    <div class="pasal">
        <h4 class="text-center">PASAL 3<br>JAM KERJA</h4>
        
        <h5>Ayat 1</h5>
        <p>Berdasarkan peraturan ketenagakerjaan yang berlaku, jam kerja efektif perusahaan untuk <strong>PIHAK KEDUA</strong> adalah selama 8 (Delapan) Jam berlaku dari hari Senin sampai dengan Hari Jumat tidak termasuk waktu istirahat. Sabtu dan Minggu adalah waktu libur, terkecuali diharuskan lembur oleh perusahaan.</p>
        
        <h5>Ayat 2</h5>
        <p>Waktu Kerja yang ditetapkan oleh perusahaan adalah flexible dengan 3 pilihan:</p>
        <ol>
            <li>Jam 07:00 – 16:00</li>
            <li>Jam 08:00 – 17:00</li>
            <li>Jam 09:00 – 18:00</li>
        </ol>
        
        <h5>Ayat 3</h5>
        <p>Waktu Istirahat ditetapkan selama 1 (satu) jam dari Senin – Jumat</p>
    </div>

    <!-- PASAL 4: PENEMPATAN, TUGAS, DAN TANGGUNG JAWAB -->
    <div class="pasal">
        <h4 class="text-center">PASAL 4<br>PENEMPATAN, TUGAS, DAN TANGGUNG JAWAB</h4>
        
        <h5>Ayat 1</h5>
        <p><strong>PIHAK KEDUA</strong> akan bekerja sebagai <?= esc($kontrak['jabatan']) ?> di PT. Cipta Duta Wacana.</p>
        
        <h5>Ayat 2</h5>
        <p>Tugas dan tanggung jawab <strong>PIHAK KEDUA</strong> adalah sebagai berikut:</p>
        <ol>
            <li>Memahami dan melaksanakan pekerjaan sesuai dengan bidangnya</li>
            <li>Mentaati peraturan perusahaan yang berlaku</li>
            <li>Menjaga kerahasiaan informasi perusahaan</li>
            <li>Berkontribusi positif terhadap kemajuan perusahaan</li>
        </ol>
        
        <h5>Ayat 3</h5>
        <p><strong>PIHAK PERTAMA</strong> berhak menempatkan <strong>PIHAK KEDUA</strong> dalam melaksanakan tugas dan pekerjaan lain yang oleh <strong>PIHAK PERTAMA</strong> dianggap lebih cocok serta sesuai dengan keahlian yang dimiliki <strong>PIHAK KEDUA</strong>, dengan syarat masih tetap berada di dalam koridor tanggung jawab sebagai karyawan di perusahaan PT. Cipta Duta Wacana.</p>
    </div>

    <!-- PASAL 5: PERPANJANGAN MASA KONTRAK KERJA -->
    <div class="pasal">
        <h4 class="text-center">PASAL 5<br>PERPANJANGAN MASA KONTRAK KERJA</h4>
        
        <h5>Ayat 1</h5>
        <p>Setelah berakhirnya jangka waktu perjanjian kerja, perjanjian kerja ini dapat langsung diperpanjang jika <strong>PIHAK PERTAMA</strong> masih membutuhkan <strong>PIHAK KEDUA</strong> dan <strong>PIHAK KEDUA</strong> juga menyatakan kesediaannya.</p>
        
        <h5>Ayat 2</h5>
        <p>Jika setelah berakhirnya perjanjian kerja ternyata <strong>PIHAK PERTAMA</strong> masih membutuhkan <strong>PIHAK KEDUA</strong>, maka <strong>PIHAK PERTAMA</strong> akan memperbaharui kontrak kerja kepada <strong>PIHAK KEDUA</strong> sebagai karyawan kontrak lanjutan PT. Cipta Duta Wacana maupun membuat kontrak kerja tetap terhadap <strong>PIHAK KEDUA</strong>.</p>
        
        <h5>Ayat 3</h5>
        <p>Jika setelah berakhirnya perjanjian kerja ternyata <strong>PIHAK KEDUA</strong> tidak diajukan untuk pengangkatan sebagai karyawan tetap oleh <strong>PIHAK PERTAMA</strong>, maka perjanjian kerja kontrak akan berakhir bersamaan dengan berakhirnya waktu perjanjian tersebut.</p>
    </div>

    <!-- PASAL 6: GAJI POKOK DAN TUNJANGAN-TUNJANGAN -->
    <div class="pasal">
        <h4 class="text-center">PASAL 6<br>GAJI POKOK DAN TUNJANGAN-TUNJANGAN</h4>
        
        <h5>Ayat 1</h5>
        <p><strong>PIHAK PERTAMA</strong> memberikan gaji pokok kepada <strong>PIHAK KEDUA</strong> sebesar Rp. <?= number_format($kontrak['gaji_pokok'] ?? 0, 0, ',', '.') ?> (<?= terbilang($kontrak['gaji_pokok'] ?? 0) ?> Rupiah) setiap bulan yang harus dibayarkan <strong>PIHAK PERTAMA</strong> pada tanggal terakhir setiap bulan setelah dipotong pajak pendapatan sesuai peraturan perpajakan di Indonesia dengan system transfer.</p>
        
        <h5>Ayat 2</h5>
        <p>Selain gaji pokok, <strong>PIHAK KEDUA</strong> juga berhak mendapatkan tunjangan-tunjangan sebagai berikut:</p>
        
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jenis Tunjangan</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php $tunjangan_no = 1; ?>
                <?php if($kontrak['tunjangan_bpjs']): ?>
                <tr>
                    <td><?= $tunjangan_no++; ?></td>
                    <td>Tunjangan BPJS</td>
                    <td>Dibayarkan oleh perusahaan untuk Karyawan</td>
                </tr>
                <?php endif; ?>
                
                <?php if($kontrak['tunjangan_makan_lokal'] > 0): ?>
                <tr>
                    <td><?= $tunjangan_no++; ?></td>
                    <td>Uang Makan (Lokal)</td>
                    <td>Rp <?= number_format($kontrak['tunjangan_makan_lokal'], 0, ',', '.') ?> per hari bila mendapatkan tugas keluar kota di pulau Jawa</td>
                </tr>
                <?php endif; ?>
                
                <?php if($kontrak['tunjangan_makan_luar_jawa'] > 0): ?>
                <tr>
                    <td><?= $tunjangan_no++; ?></td>
                    <td>Uang Makan (Luar Jawa)</td>
                    <td>Rp <?= number_format($kontrak['tunjangan_makan_luar_jawa'], 0, ',', '.') ?> per hari bila ditugaskan keluar kota di luar pulau Jawa</td>
                </tr>
                <?php endif; ?>
                
                <?php if($kontrak['reimburse_transport']): ?>
                <tr>
                    <td><?= $tunjangan_no++; ?></td>
                    <td>Reimburse Transport</td>
                    <td>Bensin, Parkir, Tol (jika menggunakan kendaraan roda 4) jika ditugaskan tugas keluar perusahaan</td>
                </tr>
                <?php endif; ?>
                
                <?php if($kontrak['reimburse_entertaint']): ?>
                <tr>
                    <td><?= $tunjangan_no++; ?></td>
                    <td>Reimburse Entertaint</td>
                    <td>Biaya entertain atau biaya menjamu customer dengan dilampirkan bukti bon/struk pembayaran</td>
                </tr>
                <?php endif; ?>
                
                <?php if($kontrak['tunjangan_penginapan_max'] > 0): ?>
                <tr>
                    <td><?= $tunjangan_no++; ?></td>
                    <td>Tunjangan Penginapan</td>
                    <td>Maksimal Rp <?= number_format($kontrak['tunjangan_penginapan_max'], 0, ',', '.') ?> per hari bila diperlukan menginap pada saat perjalanan dinas</td>
                </tr>
                <?php endif; ?>
                
                <?php if($tunjangan_no == 1): ?>
                <tr>
                    <td colspan="3" class="text-center">Tidak ada tunjangan khusus</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <h5>Ayat 3</h5>
        <p>Pembayaran tunjangan-tunjangan tersebut akan disatukan dengan pembayaran gaji pokok yang akan diterima <strong>PIHAK KEDUA</strong> pada tanggal terakhir setiap bulan.</p>
    </div>

    <!-- PASAL 7: CUTI -->
    <div class="pasal">
        <h4 class="text-center">PASAL 7<br>CUTI</h4>
        
        <h5>Ayat 1</h5>
        <p>Hak cuti timbul setelah <strong>PIHAK KEDUA</strong> mempunyai masa kerja selama <?= $kontrak['hak_cuti_setelah_tahun'] ?? 1 ?> (<?= terbilang($kontrak['hak_cuti_setelah_tahun'] ?? 1) ?>) tahun.</p>
        
        <h5>Ayat 2</h5>
        <p>Jika telah mempunyai masa kerja seperti ayat 1 tersebut di atas, maka <strong>PIHAK KEDUA</strong> akan mendapatkan cuti selama <?= $kontrak['jumlah_cuti_tahunan_hari'] ?? 12 ?> (<?= terbilang($kontrak['jumlah_cuti_tahunan_hari'] ?? 12) ?>) hari setiap tahun, yang terdiri dari:</p>
        <ol>
            <li>Cuti pribadi selama <?= $kontrak['jumlah_cuti_tahunan_hari'] ?? 12 ?> hari kerja.</li>
            <li>Cuti bersama sesuai dengan peraturan pemerintah</li>
        </ol>
        
        <h5>Ayat 3</h5>
        <p>Sebelum melaksanakan cuti, <strong>PIHAK KEDUA</strong> telah mengajukan permohonan terlebih dahulu secara tertulis, selambat-lambatnya 7 (tujuh) hari dengan mendapat pengesahan berupa tanda tangan dan ijin dari atasan langsung yang bersangkutan.</p>
    </div>

    <!-- PASAL 8: PEMUTUSAN HUBUNGAN KERJA (PHK) -->
    <div class="pasal">
        <h4 class="text-center">PASAL 8<br>PEMUTUSAN HUBUNGAN KERJA (PHK)</h4>
        
        <h5>Ayat 1</h5>
        <p>Dengan memperhatikan Undang-Undang dan Peraturan Ketenagakerjaan yang berlaku, <strong>PIHAK PERTAMA</strong> dapat mengakhiri hubungan kerja dengan <strong>PIHAK KEDUA</strong> karena pengingkaran perjanjian ini.</p>
        
        <h5>Ayat 2</h5>
        <p>Jika terjadi Pemutusan Hubungan Kerja (PHK), maka <strong>PIHAK KEDUA</strong> diharuskan mengembalikan barang-barang inventaris (jika ada) kepada perusahaan.</p>
        
        <h5>Ayat 3</h5>
        <p><strong>PIHAK KEDUA</strong> juga diharuskan menyelesaikan hal-hal yang berhubungan dengan administrasi keuangan, seperti hutang atau pinjaman yang dilakukan <strong>PIHAK KEDUA</strong> dan juga laporan pekerjaan.</p>
    </div>

    <!-- PASAL 9: PENGUNDURAN DIRI -->
    <div class="pasal">
        <h4 class="text-center">PASAL 9<br>PENGUNDURAN DIRI</h4>
        
        <h5>Ayat 1</h5>
        <p>Jika <strong>PIHAK KEDUA</strong> mengundurkan diri dari jabatannya maka <strong>PIHAK KEDUA</strong> harus memberikan informasi dan surat tertulis minimal 2 (dua) Minggu sebelum tanggal pengunduran diri berlaku.</p>
        
        <h5>Ayat 2</h5>
        <p><strong>PIHAK KEDUA</strong> tidak mendapatkan hak sisa gaji jika pengunduran diri dilakukan secara mendadak ataupun tidak mengikuti prosedur yang berlaku di perusahaan.</p>
    </div>

    <!-- PASAL 10: KEADAAN DARURAT (FORCE MAJEUR) -->
    <div class="pasal">
        <h4 class="text-center">PASAL 10<br>KEADAAN DARURAT (FORCE MAJEUR)</h4>
        <p>Perjanjian kerja ini batal dengan sendirinya jika karena keadaan atau situasi yang memaksa, seperti: pelanggaran karena menyalah gunakan obat-obatan atau narkoba, pelanggaran secara hukum, bencana alam, pemberontakan, perang, huru-hara, kerusuhan, Peraturan Pemerintah atau apapun yang mengakibatkan perjanjian kerja ini tidak mungkin lagi untuk diwujudkan termasuk bila terjadi pailit perusahaan (Perusahaan bangkrut).</p>
    </div>

    <!-- PASAL 11: PENUTUP -->
    <div class="pasal">
        <h4 class="text-center">PASAL 11<br>PENUTUP</h4>
        <p>Demikianlah perjanjian ini dibuat, disetujui, dan ditandatangani dalam rangkap dua, asli dan tembusan bermaterei cukup dan berkekuatan hukum yang sama. Satu dipegang oleh <strong>PIHAK PERTAMA</strong> dan lainnya untuk <strong>PIHAK KEDUA</strong>.</p>
    </div>

    <!-- Tempat dan Tanggal -->
    <div class="text-right mt-3">
        <p>Dibuat di : Villa Bintaro Regency</p>
        <p>Tanggal : <?= date('d F Y', strtotime($kontrak['created_at'])) ?></p>
    </div>

    <!-- Tanda Tangan -->
    <div class="signature keep-together">
        <table class="signature-table">
            <tr>
                <td>
                    <div class="text-bold">PIHAK PERTAMA</div>
                    <div class="signature-space"></div>
                    <div><?= esc($kontrak['pihak_pertama_nama'] ?? 'PT. Cipta Duta Wacana') ?></div>
                    <div><?= esc($kontrak['pihak_pertama_jabatan'] ?? 'Direktur') ?></div>
                </td>
                <td>
                    <div class="text-bold">PIHAK KEDUA</div>
                    <div class="signature-space"></div>
                    <div><?= esc($kontrak['pihak_kedua_nama'] ?? $kontrak['nama_lengkap'] ?? '-') ?></div>
                    <div><?= esc($kontrak['pihak_kedua_jabatan'] ?? $kontrak['jabatan'] ?? '-') ?></div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dicetak pada: <?= date('d/m/Y H:i:s') ?> | Status: <?= $kontrak['status'] ?></p>
        <p>PT. Cipta Duta Wacana - Human Resource Management System</p>
    </div>

    <script>
        // Fungsi untuk save as PDF
        function saveAsPDF() {
            window.open('<?= base_url('admin/karyawan/kontrak/pdf/' . $kontrak['id']) ?>', '_blank');
        }
        
        // Print otomatis saat halaman load (opsional)
        // window.onload = function() {
        //     window.print();
        // };
        
        // Before print event
        window.onbeforeprint = function() {
            // Sembunyikan tombol print sebelum cetak
            document.querySelector('.print-actions').style.display = 'none';
        };
        
        // After print event
        window.onafterprint = function() {
            // Tampilkan kembali tombol print setelah cetak
            document.querySelector('.print-actions').style.display = 'block';
        };
    </script>
</body>
</html>

<?php
// Helper function untuk konversi angka ke terbilang
if (!function_exists('terbilang')) {
    function terbilang($x) {
        $angka = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];
        
        if ($x < 12)
            return " " . $angka[$x];
        elseif ($x < 20)
            return terbilang($x - 10) . " belas";
        elseif ($x < 100)
            return terbilang($x / 10) . " puluh" . terbilang($x % 10);
        elseif ($x < 200)
            return " seratus" . terbilang($x - 100);
        elseif ($x < 1000)
            return terbilang($x / 100) . " ratus" . terbilang($x % 100);
        elseif ($x < 2000)
            return " seribu" . terbilang($x - 1000);
        elseif ($x < 1000000)
            return terbilang($x / 1000) . " ribu" . terbilang($x % 1000);
        elseif ($x < 1000000000)
            return terbilang($x / 1000000) . " juta" . terbilang($x % 1000000);
    }
}
?>