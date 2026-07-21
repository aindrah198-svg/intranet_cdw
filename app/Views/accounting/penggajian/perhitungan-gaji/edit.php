<?php
// app/Views/accounting/penggajian/perhitungan-gaji/edit.php
$data['active'] = 'perhitungan-gaji';
$this->extend('accounting/templates/header');
?>

<?php $this->section('content'); ?>
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 text-gradient-accounting">Edit Perhitungan Gaji</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-2">
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/penggajian') ?>">Penggajian</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/penggajian/perhitungan-gaji') ?>">Perhitungan Gaji</a></li>
                    <li class="breadcrumb-item active">Edit Perhitungan</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= site_url('accounting/penggajian/perhitungan-gaji') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Status: <?= $perhitungan['status'] ?></strong> - 
        <?php if ($perhitungan['status'] == 'Draft'): ?>
            Perhitungan dalam status draft, Anda dapat mengedit data.
        <?php elseif ($perhitungan['status'] == 'Dihitung'): ?>
            Perhitungan sudah dihitung. Anda dapat menyetujui atau menolak.
        <?php else: ?>
            Perhitungan sudah final, tidak dapat diedit.
        <?php endif; ?>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form id="formPerhitungan" action="<?= site_url('accounting/penggajian/perhitungan-gaji/update/' . $perhitungan['id']) ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="PUT">
                
                <!-- Data Karyawan & Periode -->
                <div class="card mb-4">
                    <div class="card-header bg-gradient-accounting text-white">
                        <i class="fas fa-user me-2"></i> Data Karyawan & Periode
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Karyawan</label>
                                <input type="text" class="form-control" value="<?= $karyawan['nik'] ?> - <?= $karyawan['nama_lengkap'] ?>" readonly disabled>
                                <input type="hidden" name="karyawan_id" value="<?= $perhitungan['karyawan_id'] ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Periode</label>
                                <input type="text" class="form-control" value="<?= $bulanOptions[$perhitungan['periode_bulan']] ?> <?= $perhitungan['periode_tahun'] ?>" readonly disabled>
                                <input type="hidden" name="periode_bulan" value="<?= $perhitungan['periode_bulan'] ?>">
                                <input type="hidden" name="periode_tahun" value="<?= $perhitungan['periode_tahun'] ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Tanggal Perhitungan <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_perhitungan" class="form-control" value="<?= $perhitungan['tanggal_perhitungan'] ?>" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Komponen Pendapatan -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-plus-circle me-2"></i> Komponen Pendapatan
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gaji Pokok</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="gaji_pokok" class="form-control currency-input" 
                                           value="<?= number_format($perhitungan['gaji_pokok'], 0, ',', '.') ?>">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tunjangan Jabatan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="tunjangan_jabatan" class="form-control currency-input" 
                                           value="<?= number_format($perhitungan['tunjangan_jabatan'], 0, ',', '.') ?>">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tunjangan Makan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="tunjangan_makan" class="form-control currency-input" 
                                           value="<?= number_format($perhitungan['tunjangan_makan'], 0, ',', '.') ?>">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tunjangan Transport</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="tunjangan_transport" class="form-control currency-input" 
                                           value="<?= number_format($perhitungan['tunjangan_transport'], 0, ',', '.') ?>">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tunjangan Lainnya</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="tunjangan_lainnya" class="form-control currency-input" 
                                           value="<?= number_format($perhitungan['tunjangan_lainnya'], 0, ',', '.') ?>">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Upah Lembur</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="upah_lembur" class="form-control currency-input" 
                                           value="<?= number_format($perhitungan['upah_lembur'], 0, ',', '.') ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Komponen Potongan -->
                <div class="card mb-4">
                    <div class="card-header bg-danger text-white">
                        <i class="fas fa-minus-circle me-2"></i> Komponen Potongan
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Potongan BPJS Kesehatan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="potongan_bpjs_kes" class="form-control currency-input" 
                                           value="<?= number_format($perhitungan['potongan_bpjs_kes'], 0, ',', '.') ?>">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Potongan BPJS Ketenagakerjaan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="potongan_bpjs_tk" class="form-control currency-input" 
                                           value="<?= number_format($perhitungan['potongan_bpjs_tk'], 0, ',', '.') ?>">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Potongan PPh 21</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="potongan_pph21" class="form-control currency-input" 
                                           value="<?= number_format($perhitungan['potongan_pph21'], 0, ',', '.') ?>">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Potongan Absensi</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="potongan_absensi" class="form-control currency-input" 
                                           value="<?= number_format($perhitungan['potongan_absensi'], 0, ',', '.') ?>">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Potongan Kasbon</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="potongan_kasbon" class="form-control currency-input" 
                                           value="<?= number_format($perhitungan['potongan_kasbon'], 0, ',', '.') ?>">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Potongan Lainnya</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="potongan_lainnya" class="form-control currency-input" 
                                           value="<?= number_format($perhitungan['potongan_lainnya'], 0, ',', '.') ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Kehadiran -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <i class="fas fa-calendar-alt me-2"></i> Data Kehadiran
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Total Hari Kerja</label>
                                <input type="number" name="total_hari_kerja" class="form-control" value="<?= $perhitungan['total_hari_kerja'] ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Total Hadir</label>
                                <input type="number" name="total_hadir" class="form-control" value="<?= $perhitungan['total_hadir'] ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Total Izin</label>
                                <input type="number" name="total_izin" class="form-control" value="<?= $perhitungan['total_izin'] ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Total Sakit</label>
                                <input type="number" name="total_sakit" class="form-control" value="<?= $perhitungan['total_sakit'] ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Total Cuti</label>
                                <input type="number" name="total_cuti" class="form-control" value="<?= $perhitungan['total_cuti'] ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Total Alpha</label>
                                <input type="number" name="total_alpha" class="form-control" value="<?= $perhitungan['total_alpha'] ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Total Terlambat (hari)</label>
                                <input type="number" name="total_terlambat" class="form-control" value="<?= $perhitungan['total_terlambat'] ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Jam Lembur</label>
                                <input type="number" step="0.5" name="jam_lembur" class="form-control" value="<?= $perhitungan['jam_lembur'] ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Catatan -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-sticky-note me-2"></i> Catatan
                    </div>
                    <div class="card-body">
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Catatan perhitungan gaji..."><?= $perhitungan['catatan'] ?></textarea>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <!-- Ringkasan Perhitungan -->
            <div class="card mb-4 sticky-top" style="top: 20px;">
                <div class="card-header bg-gradient-accounting text-white">
                    <i class="fas fa-calculator me-2"></i> Ringkasan Perhitungan
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td><strong>Total Pendapatan</strong></td>
                            <td class="text-end"><strong id="total_pendapatan_display">Rp <?= number_format($perhitungan['total_pendapatan'], 0, ',', '.') ?></strong></td>
                        </tr>
                        <tr>
                            <td><strong>Total Potongan</strong></td>
                            <td class="text-end"><strong id="total_potongan_display">Rp <?= number_format($perhitungan['total_potongan'], 0, ',', '.') ?></strong></td>
                        </tr>
                        <tr class="border-top">
                            <td><strong class="text-success">GAJI BERSIH</strong></td>
                            <td class="text-end"><strong class="text-success" id="gaji_bersih_display">Rp <?= number_format($perhitungan['gaji_bersih'], 0, ',', '.') ?></strong></td>
                        </tr>
                    </table>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <label class="form-label">Status Saat Ini</label>
                        <div>
                            <span class="badge bg-<?= $perhitungan['status'] == 'Disetujui' ? 'success' : ($perhitungan['status'] == 'Dihitung' ? 'info' : 'secondary') ?> px-3 py-2">
                                <?= $perhitungan['status'] ?>
                            </span>
                        </div>
                    </div>
                    
                    <button type="submit" form="formPerhitungan" class="btn btn-accounting w-100 mb-2" <?= $perhitungan['status'] != 'Draft' ? 'disabled' : '' ?>>
                        <i class="fas fa-save me-1"></i> Update Perhitungan
                    </button>
                    <button type="button" class="btn btn-accounting-outline w-100" onclick="hitungOtomatis()" <?= $perhitungan['status'] != 'Draft' ? 'disabled' : '' ?>>
                        <i class="fas fa-sync-alt me-1"></i> Hitung Ulang
                    </button>
                </div>
            </div>

            <!-- Informasi Karyawan -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-user-circle me-2"></i> Informasi Karyawan
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="40%">NIK</td>
                            <td><strong><?= $karyawan['nik'] ?? '-' ?></strong></td>
                        </tr>
                        <tr>
                            <td>Nama Lengkap</td>
                            <td><strong><?= $karyawan['nama_lengkap'] ?? '-' ?></strong></td>
                        </tr>
                        <tr>
                            <td>Jabatan</td>
                            <td><?= $karyawan['jabatan'] ?? '-' ?></td>
                        </tr>
                        <tr>
                            <td>Departemen</td>
                            <td><?= $karyawan['departemen'] ?? '-' ?></td>
                        </tr>
                        <tr>
                            <td>Tanggal Masuk</td>
                            <td><?= $karyawan['tanggal_masuk'] ?? '-' ?></td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td><span class="badge bg-<?= ($karyawan['status_karyawan'] ?? '') == 'Tetap' ? 'success' : 'info' ?>">
                                <?= $karyawan['status_karyawan'] ?? '-' ?>
                            </span></td>
                        </tr>
                        <tr>
                            <td>Bank</td>
                            <td><?= $karyawan['bank'] ?? '-' ?></td>
                        </tr>
                        <tr>
                            <td>No Rekening</td>
                            <td><?= $karyawan['no_rekening'] ?? '-' ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Format currency input
function formatCurrencyInput(input) {
    let value = input.value.replace(/[^0-9]/g, '');
    if (value) {
        input.value = new Intl.NumberFormat('id-ID').format(parseInt(value));
    }
}

// Parse currency string to number
function parseCurrency(value) {
    if (!value) return 0;
    return parseInt(value.toString().replace(/[^0-9]/g, '')) || 0;
}

// Update summary
function updateSummary() {
    let gajiPokok = parseCurrency(document.querySelector('input[name="gaji_pokok"]').value);
    let tunjanganJabatan = parseCurrency(document.querySelector('input[name="tunjangan_jabatan"]').value);
    let tunjanganMakan = parseCurrency(document.querySelector('input[name="tunjangan_makan"]').value);
    let tunjanganTransport = parseCurrency(document.querySelector('input[name="tunjangan_transport"]').value);
    let tunjanganLainnya = parseCurrency(document.querySelector('input[name="tunjangan_lainnya"]').value);
    let upahLembur = parseCurrency(document.querySelector('input[name="upah_lembur"]').value);
    
    let potonganBpjsKes = parseCurrency(document.querySelector('input[name="potongan_bpjs_kes"]').value);
    let potonganBpjsTk = parseCurrency(document.querySelector('input[name="potongan_bpjs_tk"]').value);
    let potonganPph21 = parseCurrency(document.querySelector('input[name="potongan_pph21"]').value);
    let potonganAbsensi = parseCurrency(document.querySelector('input[name="potongan_absensi"]').value);
    let potonganKasbon = parseCurrency(document.querySelector('input[name="potongan_kasbon"]').value);
    let potonganLainnya = parseCurrency(document.querySelector('input[name="potongan_lainnya"]').value);
    
    let totalPendapatan = gajiPokok + tunjanganJabatan + tunjanganMakan + tunjanganTransport + tunjanganLainnya + upahLembur;
    let totalPotongan = potonganBpjsKes + potonganBpjsTk + potonganPph21 + potonganAbsensi + potonganKasbon + potonganLainnya;
    let gajiBersih = totalPendapatan - totalPotongan;
    
    document.getElementById('total_pendapatan_display').innerHTML = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalPendapatan);
    document.getElementById('total_potongan_display').innerHTML = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalPotongan);
    document.getElementById('gaji_bersih_display').innerHTML = 'Rp ' + new Intl.NumberFormat('id-ID').format(gajiBersih);
}

// Hitung otomatis
function hitungOtomatis() {
    updateSummary();
    toastr.success('Perhitungan ulang selesai');
}

// Event listeners
$(document).ready(function() {
    // Currency input formatting
    $('.currency-input').on('input', function() {
        formatCurrencyInput(this);
        updateSummary();
    });
    
    // Number input formatting
    $('input[type="number"]').on('input', function() {
        updateSummary();
    });
    
    // Initial update
    updateSummary();
});
</script>

<?php $this->endSection(); ?>