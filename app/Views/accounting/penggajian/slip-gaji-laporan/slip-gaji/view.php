<?php
// app/Views/accounting/penggajian/slip-gaji-laporan/slip-gaji/view.php
$data['active'] = 'slip-gaji-laporan';
$this->extend('accounting/templates/header');
?>

<?php $this->section('content'); ?>
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 text-gradient-accounting">Slip Gaji</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-2">
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/penggajian') ?>">Penggajian</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/penggajian/slip-gaji-laporan') ?>">Slip Gaji & Laporan</a></li>
                    <li class="breadcrumb-item active">Slip Gaji</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= site_url('accounting/penggajian/slip-gaji/print/' . $perhitungan['id']) ?>" class="btn btn-secondary" target="_blank">
                <i class="fas fa-print me-1"></i> Print
            </a>
            <a href="<?= site_url('accounting/penggajian/slip-gaji/pdf/' . $perhitungan['id']) ?>" class="btn btn-danger" target="_blank">
                <i class="fas fa-file-pdf me-1"></i> PDF
            </a>
            <a href="<?= site_url('accounting/penggajian/slip-gaji-laporan') ?>" class="btn btn-accounting-outline">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Slip Gaji Content -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow-lg" id="slipGajiCard">
                <div class="card-body p-5">
                    <!-- Header Perusahaan -->
                    <div class="text-center mb-4">
                        <h3 class="mb-0"><?= $perusahaan['nama_perusahaan'] ?? 'PT. CIPTA DUTA WACANA' ?></h3>
                        <p class="text-muted small mb-0">
                            <?= $perusahaan['alamat'] ?? 'Beltway Office Park Tower B Lantai 5, Jl. TB Simatupang No. 41, Jakarta Selatan' ?>
                        </p>
                        <p class="text-muted small">
                            Telp: <?= $perusahaan['telepon'] ?? '(+62-21) 29857462' ?> | Email: <?= $perusahaan['email'] ?? 'info@cdw-engineering.com' ?>
                        </p>
                        <hr class="my-3">
                        <h4 class="mb-0">SLIP GAJI</h4>
                        <p class="text-muted">
                            Periode: <?= $this->getNamaBulan($perhitungan['periode_bulan']) ?> <?= $perhitungan['periode_tahun'] ?>
                        </p>
                    </div>

                    <!-- Informasi Karyawan -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="35%"><strong>NIK</strong></td>
                                    <td>: <?= $perhitungan['nomor_karyawan'] ?? $perhitungan['nik'] ?? '-' ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Nama Karyawan</strong></td>
                                    <td>: <?= $perhitungan['nama_karyawan'] ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Jabatan</strong></td>
                                    <td>: <?= $perhitungan['jabatan'] ?? '-' ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Departemen</strong></td>
                                    <td>: <?= $perhitungan['departemen'] ?? '-' ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="35%"><strong>Tanggal Masuk</strong></td>
                                    <td>: <?= $perhitungan['tanggal_masuk'] ?? '-' ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Status Karyawan</strong></td>
                                    <td>: <?= $perhitungan['status_karyawan'] ?? '-' ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Bank & No Rekening</strong></td>
                                    <td>: <?= ($perhitungan['bank'] ?? '-') . ' - ' . ($perhitungan['no_rekening'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Nomor Perhitungan</strong></td>
                                    <td>: <?= $perhitungan['nomor_perhitungan'] ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Detail Pendapatan -->
                        <div class="col-md-6">
                            <div class="card bg-light mb-3">
                                <div class="card-header bg-success text-white">
                                    <strong><i class="fas fa-plus-circle me-2"></i> DETAIL PENDAPATAN</strong>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-sm table-bordered mb-0">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th>Komponen</th>
                                                <th class="text-end">Jumlah (Rp)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Gaji Pokok</td>
                                                <td class="text-end"><?= number_format($pendapatanDetail['gaji_pokok'], 0, ',', '.') ?></td>
                                            </tr>
                                            <tr>
                                                <td>Tunjangan Jabatan</td>
                                                <td class="text-end"><?= number_format($pendapatanDetail['tunjangan_jabatan'], 0, ',', '.') ?></td>
                                            </tr>
                                            <tr>
                                                <td>Tunjangan Makan</td>
                                                <td class="text-end"><?= number_format($pendapatanDetail['tunjangan_makan'], 0, ',', '.') ?></td>
                                            </tr>
                                            <tr>
                                                <td>Tunjangan Transport</td>
                                                <td class="text-end"><?= number_format($pendapatanDetail['tunjangan_transport'], 0, ',', '.') ?></td>
                                            </tr>
                                            <tr>
                                                <td>Tunjangan Lainnya</td>
                                                <td class="text-end"><?= number_format($pendapatanDetail['tunjangan_lainnya'], 0, ',', '.') ?></td>
                                            </tr>
                                            <tr>
                                                <td>Upah Lembur</td>
                                                <td class="text-end"><?= number_format($pendapatanDetail['upah_lembur'], 0, ',', '.') ?></td>
                                            </tr>
                                            <tr class="table-active">
                                                <td><strong>TOTAL PENDAPATAN</strong></td>
                                                <td class="text-end"><strong><?= number_format($perhitungan['total_pendapatan'], 0, ',', '.') ?></strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Potongan -->
                        <div class="col-md-6">
                            <div class="card bg-light mb-3">
                                <div class="card-header bg-danger text-white">
                                    <strong><i class="fas fa-minus-circle me-2"></i> DETAIL POTONGAN</strong>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-sm table-bordered mb-0">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th>Komponen</th>
                                                <th class="text-end">Jumlah (Rp)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>BPJS Kesehatan</td>
                                                <td class="text-end"><?= number_format($potonganDetail['bpjs_kes'], 0, ',', '.') ?></td>
                                            </tr>
                                            <tr>
                                                <td>BPJS Ketenagakerjaan</td>
                                                <td class="text-end"><?= number_format($potonganDetail['bpjs_tk'], 0, ',', '.') ?></td>
                                            </tr>
                                            <tr>
                                                <td>PPh 21</td>
                                                <td class="text-end"><?= number_format($potonganDetail['pph21'], 0, ',', '.') ?></td>
                                            </tr>
                                            <tr>
                                                <td>Potongan Absensi</td>
                                                <td class="text-end"><?= number_format($potonganDetail['absensi'], 0, ',', '.') ?></td>
                                            </tr>
                                            <tr>
                                                <td>Potongan Kasbon</td>
                                                <td class="text-end"><?= number_format($potonganDetail['kasbon'], 0, ',', '.') ?></td>
                                            </tr>
                                            <tr>
                                                <td>Potongan Lainnya</td>
                                                <td class="text-end"><?= number_format($potonganDetail['lainnya'], 0, ',', '.') ?></td>
                                            </tr>
                                            <tr class="table-active">
                                                <td><strong>TOTAL POTONGAN</strong></td>
                                                <td class="text-end"><strong><?= number_format($perhitungan['total_potongan'], 0, ',', '.') ?></strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ringkasan Gaji -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center py-3">
                                    <h5 class="mb-0">GAJI BERSIH</h5>
                                    <h2 class="mb-0">Rp <?= number_format($perhitungan['gaji_bersih'], 0, ',', '.') ?></h2>
                                    <small>Terbilang: <?= $this->terbilang($perhitungan['gaji_bersih']) ?> Rupiah</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Kehadiran (jika ada) -->
                    <?php if ($kehadiran['hari_kerja'] > 0 || $kehadiran['hadir'] > 0): ?>
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-header bg-info text-white">
                                    <strong><i class="fas fa-calendar-alt me-2"></i> RINGKASAN KEHADIRAN</strong>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-3 mb-2">
                                            <div class="border rounded p-2">
                                                <h5 class="mb-0"><?= $kehadiran['hari_kerja'] ?></h5>
                                                <small class="text-muted">Hari Kerja</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <div class="border rounded p-2 bg-success text-white">
                                                <h5 class="mb-0"><?= $kehadiran['hadir'] ?></h5>
                                                <small>Hadir</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <div class="border rounded p-2 bg-warning">
                                                <h5 class="mb-0"><?= $kehadiran['izin'] ?></h5>
                                                <small>Izin</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <div class="border rounded p-2 bg-info text-white">
                                                <h5 class="mb-0"><?= $kehadiran['sakit'] ?></h5>
                                                <small>Sakit</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <div class="border rounded p-2">
                                                <h5 class="mb-0"><?= $kehadiran['cuti'] ?></h5>
                                                <small>Cuti</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <div class="border rounded p-2 bg-danger text-white">
                                                <h5 class="mb-0"><?= $kehadiran['alpha'] ?></h5>
                                                <small>Alpha</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <div class="border rounded p-2">
                                                <h5 class="mb-0"><?= $kehadiran['terlambat'] ?></h5>
                                                <small>Terlambat (hari)</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <div class="border rounded p-2">
                                                <h5 class="mb-0"><?= number_format($kehadiran['lembur'], 1) ?></h5>
                                                <small>Jam Lembur</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Catatan -->
                    <?php if ($perhitungan['catatan']): ?>
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-secondary">
                                <strong><i class="fas fa-sticky-note me-2"></i> Catatan:</strong><br>
                                <?= nl2br($perhitungan['catatan']) ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Tanda Tangan -->
                    <div class="row mt-5">
                        <div class="col-6 text-center">
                            <div class="mt-4 pt-3">
                                <div class="border-top d-inline-block" style="width: 80%;"></div>
                                <p class="mb-0 mt-2">Hormat Kami,</p>
                                <p class="mb-0">HRD / Accounting</p>
                            </div>
                        </div>
                        <div class="col-6 text-center">
                            <div class="mt-4 pt-3">
                                <div class="border-top d-inline-block" style="width: 80%;"></div>
                                <p class="mb-0 mt-2">Diterima oleh,</p>
                                <p class="mb-0"><?= $perhitungan['nama_karyawan'] ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="row mt-4">
                        <div class="col-12 text-center">
                            <hr>
                            <small class="text-muted">
                                Slip gaji ini adalah bukti resmi pembayaran gaji periode <?= $this->getNamaBulan($perhitungan['periode_bulan']) ?> <?= $perhitungan['periode_tahun'] ?><br>
                                Dicetak pada: <?= date('d/m/Y H:i:s') ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Add print styles
function printSlip() {
    window.print();
}

// Add PDF download function (if needed)
function downloadPDF() {
    window.location.href = '<?= site_url('accounting/penggajian/slip-gaji/pdf/' . $perhitungan['id']) ?>';
}
</script>

<style media="print">
    @media print {
        .sidebar, .top-navbar, .btn, nav, .page-header, .card-header, .btn-group, .btn, .footer, .no-print {
            display: none !important;
        }
        .container-fluid {
            padding: 0 !important;
            margin: 0 !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        .card-body {
            padding: 0 !important;
        }
        body {
            background: white !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        @page {
            size: A4;
            margin: 1.5cm;
        }
    }
</style>

<?php $this->endSection(); ?>

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

<?php
// Helper function untuk get nama bulan (ditempatkan di sini karena di view)
function getNamaBulan($bulan) {
    $bulanNames = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    return $bulanNames[$bulan] ?? '';
}
?>