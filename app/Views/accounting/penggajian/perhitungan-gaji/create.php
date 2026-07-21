<?php
// app/Views/accounting/penggajian/perhitungan-gaji/create.php
$data['active'] = 'perhitungan-gaji';
$this->extend('accounting/templates/header');
?>

<?php $this->section('content'); ?>
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 text-gradient-accounting">Tambah Perhitungan Gaji</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-2">
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/penggajian') ?>">Penggajian</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/penggajian/perhitungan-gaji') ?>">Perhitungan Gaji</a></li>
                    <li class="breadcrumb-item active">Tambah Perhitungan</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= site_url('accounting/penggajian/perhitungan-gaji') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Form Utama -->
            <div class="card mb-4">
                <div class="card-header bg-gradient-accounting text-white">
                    <i class="fas fa-user me-2"></i> Data Karyawan & Periode
                </div>
                <div class="card-body">
                    <form id="formPerhitungan" action="<?= site_url('accounting/penggajian/perhitungan-gaji/store') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="mode" value="manual">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Karyawan <span class="text-danger">*</span></label>
                                <select name="karyawan_id" id="karyawan_id" class="form-select" required>
                                    <option value="">Pilih Karyawan</option>
                                    <?php foreach ($karyawanOptions as $k): ?>
                                        <option value="<?= $k['id'] ?>" <?= ($karyawan && $karyawan['id'] == $k['id']) ? 'selected' : '' ?>>
                                            <?= $k['nik'] ?> - <?= $k['nama_lengkap'] ?> (<?= $k['jabatan'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback" id="error_karyawan_id"></div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Bulan <span class="text-danger">*</span></label>
                                <select name="periode_bulan" id="periode_bulan" class="form-select" required>
                                    <?php foreach ($bulanOptions as $key => $val): ?>
                                        <option value="<?= $key ?>" <?= $bulan == $key ? 'selected' : '' ?>>
                                            <?= $val ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback" id="error_periode_bulan"></div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Tahun <span class="text-danger">*</span></label>
                                <select name="periode_tahun" id="periode_tahun" class="form-select" required>
                                    <?php foreach ($tahunOptions as $t): ?>
                                        <option value="<?= $t ?>" <?= $tahun == $t ? 'selected' : '' ?>>
                                            <?= $t ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback" id="error_periode_tahun"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Perhitungan <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_perhitungan" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                <div class="invalid-feedback" id="error_tanggal_perhitungan"></div>
                            </div>
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
                                <input type="text" name="gaji_pokok" id="gaji_pokok" class="form-control currency-input" 
                                       value="<?= number_format($perhitungan['gaji_pokok'], 0, ',', '.') ?>">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tunjangan Jabatan</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="tunjangan_jabatan" id="tunjangan_jabatan" class="form-control currency-input" 
                                       value="<?= number_format($perhitungan['tunjangan_jabatan'], 0, ',', '.') ?>">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tunjangan Makan</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="tunjangan_makan" id="tunjangan_makan" class="form-control currency-input" 
                                       value="<?= number_format($perhitungan['tunjangan_makan'], 0, ',', '.') ?>">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tunjangan Transport</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="tunjangan_transport" id="tunjangan_transport" class="form-control currency-input" 
                                       value="<?= number_format($perhitungan['tunjangan_transport'], 0, ',', '.') ?>">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tunjangan Lainnya</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="tunjangan_lainnya" id="tunjangan_lainnya" class="form-control currency-input" 
                                       value="<?= number_format($perhitungan['tunjangan_lainnya'], 0, ',', '.') ?>">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Upah Lembur</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="upah_lembur" id="upah_lembur" class="form-control currency-input" 
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
                                <input type="text" name="potongan_bpjs_kes" id="potongan_bpjs_kes" class="form-control currency-input" 
                                       value="<?= number_format($perhitungan['potongan_bpjs_kes'], 0, ',', '.') ?>">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Potongan BPJS Ketenagakerjaan</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="potongan_bpjs_tk" id="potongan_bpjs_tk" class="form-control currency-input" 
                                       value="<?= number_format($perhitungan['potongan_bpjs_tk'], 0, ',', '.') ?>">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Potongan PPh 21</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="potongan_pph21" id="potongan_pph21" class="form-control currency-input" 
                                       value="<?= number_format($perhitungan['potongan_pph21'], 0, ',', '.') ?>">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Potongan Absensi</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="potongan_absensi" id="potongan_absensi" class="form-control currency-input" 
                                       value="<?= number_format($perhitungan['potongan_absensi'], 0, ',', '.') ?>">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Potongan Kasbon</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="potongan_kasbon" id="potongan_kasbon" class="form-control currency-input" 
                                       value="<?= number_format($perhitungan['potongan_kasbon'], 0, ',', '.') ?>">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Potongan Lainnya</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="potongan_lainnya" id="potongan_lainnya" class="form-control currency-input" 
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
                            <td class="text-end"><strong id="total_pendapatan_display">Rp 0</strong></td>
                        </tr>
                        <tr>
                            <td><strong>Total Potongan</strong></td>
                            <td class="text-end"><strong id="total_potongan_display">Rp 0</strong></td>
                        </tr>
                        <tr class="border-top">
                            <td><strong class="text-success">GAJI BERSIH</strong></td>
                            <td class="text-end"><strong class="text-success" id="gaji_bersih_display">Rp 0</strong></td>
                        </tr>
                    </table>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <label class="form-label">Catatan Ringkasan</label>
                        <div id="ringkasan_catatan" class="small text-muted">
                            <i class="fas fa-info-circle me-1"></i> Isi semua komponen untuk melihat ringkasan
                        </div>
                    </div>
                    
                    <button type="submit" form="formPerhitungan" class="btn btn-accounting w-100 mb-2">
                        <i class="fas fa-save me-1"></i> Simpan Perhitungan
                    </button>
                    <button type="button" class="btn btn-accounting-outline w-100" onclick="hitungOtomatis()">
                        <i class="fas fa-sync-alt me-1"></i> Hitung Otomatis
                    </button>
                </div>
            </div>

            <!-- Informasi Karyawan -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-user-circle me-2"></i> Informasi Karyawan
                </div>
                <div class="card-body" id="info_karyawan">
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-user fa-3x mb-2 d-block"></i>
                        <p>Pilih karyawan terlebih dahulu</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

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
    let gajiPokok = parseCurrency(document.getElementById('gaji_pokok').value);
    let tunjanganJabatan = parseCurrency(document.getElementById('tunjangan_jabatan').value);
    let tunjanganMakan = parseCurrency(document.getElementById('tunjangan_makan').value);
    let tunjanganTransport = parseCurrency(document.getElementById('tunjangan_transport').value);
    let tunjanganLainnya = parseCurrency(document.getElementById('tunjangan_lainnya').value);
    let upahLembur = parseCurrency(document.getElementById('upah_lembur').value);
    
    let potonganBpjsKes = parseCurrency(document.getElementById('potongan_bpjs_kes').value);
    let potonganBpjsTk = parseCurrency(document.getElementById('potongan_bpjs_tk').value);
    let potonganPph21 = parseCurrency(document.getElementById('potongan_pph21').value);
    let potonganAbsensi = parseCurrency(document.getElementById('potongan_absensi').value);
    let potonganKasbon = parseCurrency(document.getElementById('potongan_kasbon').value);
    let potonganLainnya = parseCurrency(document.getElementById('potongan_lainnya').value);
    
    let totalPendapatan = gajiPokok + tunjanganJabatan + tunjanganMakan + tunjanganTransport + tunjanganLainnya + upahLembur;
    let totalPotongan = potonganBpjsKes + potonganBpjsTk + potonganPph21 + potonganAbsensi + potonganKasbon + potonganLainnya;
    let gajiBersih = totalPendapatan - totalPotongan;
    
    document.getElementById('total_pendapatan_display').innerHTML = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalPendapatan);
    document.getElementById('total_potongan_display').innerHTML = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalPotongan);
    document.getElementById('gaji_bersih_display').innerHTML = 'Rp ' + new Intl.NumberFormat('id-ID').format(gajiBersih);
}

// Auto calculate all
function hitungOtomatis() {
    let gajiPokok = parseCurrency(document.getElementById('gaji_pokok').value);
    let tunjanganJabatan = parseCurrency(document.getElementById('tunjangan_jabatan').value);
    let tunjanganMakan = parseCurrency(document.getElementById('tunjangan_makan').value);
    let tunjanganTransport = parseCurrency(document.getElementById('tunjangan_transport').value);
    let tunjanganLainnya = parseCurrency(document.getElementById('tunjangan_lainnya').value);
    let upahLembur = parseCurrency(document.getElementById('upah_lembur').value);
    
    let totalPendapatan = gajiPokok + tunjanganJabatan + tunjanganMakan + tunjanganTransport + tunjanganLainnya + upahLembur;
    let gajiBersih = totalPendapatan;
    
    document.getElementById('gaji_bersih_display').innerHTML = 'Rp ' + new Intl.NumberFormat('id-ID').format(gajiBersih);
    updateSummary();
    
    toastr.success('Perhitungan otomatis selesai');
}

// Get karyawan info
function getKaryawanInfo(karyawanId) {
    if (!karyawanId) return;
    
    $.ajax({
        url: '<?= site_url('accounting/penggajian/perhitungan-gaji/ajax-get-karyawan-info') ?>/' + karyawanId,
        method: 'GET',
        success: function(response) {
            if (response.success && response.data) {
                let html = `
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="40%">NIK</td>
                            <td><strong>${response.data.nik || '-'}</strong></td>
                        </tr>
                        <tr>
                            <td>Nama Lengkap</td>
                            <td><strong>${response.data.nama_lengkap || '-'}</strong></td>
                        </tr>
                        <tr>
                            <td>Jabatan</td>
                            <td>${response.data.jabatan || '-'}</td>
                        </tr>
                        <tr>
                            <td>Departemen</td>
                            <td>${response.data.departemen || '-'}</td>
                        </tr>
                        <tr>
                            <td>Tanggal Masuk</td>
                            <td>${response.data.tanggal_masuk || '-'}</td>
                        </tr>
                        <tr>
                            <td>Status Karyawan</td>
                            <td><span class="badge bg-${response.data.status_karyawan == 'Tetap' ? 'success' : 'info'}">${response.data.status_karyawan || '-'}</span></td>
                        </tr>
                        <tr>
                            <td>Bank</td>
                            <td>${response.data.bank || '-'}</td>
                        </tr>
                        <tr>
                            <td>No Rekening</td>
                            <td>${response.data.no_rekening || '-'}</td>
                        </tr>
                    </table>
                `;
                $('#info_karyawan').html(html);
                
                // Auto fill if gaji pokok is empty
                if (response.data.gaji_pokok && !document.getElementById('gaji_pokok').value) {
                    document.getElementById('gaji_pokok').value = new Intl.NumberFormat('id-ID').format(response.data.gaji_pokok);
                    updateSummary();
                }
            } else {
                $('#info_karyawan').html('<div class="text-center text-muted py-3"><i class="fas fa-user fa-3x mb-2 d-block"></i><p>Data karyawan tidak ditemukan</p></div>');
            }
        },
        error: function() {
            $('#info_karyawan').html('<div class="text-center text-muted py-3"><i class="fas fa-exclamation-triangle fa-3x mb-2 d-block"></i><p>Gagal mengambil data karyawan</p></div>');
        }
    });
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
    
    // Karyawan select change
    $('#karyawan_id').on('change', function() {
        getKaryawanInfo($(this).val());
    });
    
    // Initial load if karyawan is selected
    let initialKaryawanId = $('#karyawan_id').val();
    if (initialKaryawanId) {
        getKaryawanInfo(initialKaryawanId);
    }
    
    // Initial summary
    updateSummary();
});

// Form validation
$('#formPerhitungan').on('submit', function(e) {
    let karyawanId = $('#karyawan_id').val();
    if (!karyawanId) {
        e.preventDefault();
        $('#error_karyawan_id').text('Pilih karyawan terlebih dahulu');
        $('#karyawan_id').addClass('is-invalid');
        return false;
    }
    return true;
});
</script>

<?php $this->endSection(); ?>