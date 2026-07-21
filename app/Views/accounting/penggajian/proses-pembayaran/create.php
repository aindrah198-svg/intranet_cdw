<?php
// app/Views/accounting/penggajian/proses-pembayaran/create.php
$data['active'] = 'proses-pembayaran';
$this->extend('accounting/templates/header');
?>

<?php $this->section('content'); ?>
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 text-gradient-accounting">Buat Proses Pembayaran Gaji</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-2">
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/penggajian') ?>">Penggajian</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/penggajian/proses-pembayaran') ?>">Proses Pembayaran</a></li>
                    <li class="breadcrumb-item active">Buat Baru</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= site_url('accounting/penggajian/proses-pembayaran') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <?php if (empty($perhitungan)): ?>
        <!-- No Data Alert -->
        <div class="alert alert-warning text-center">
            <i class="fas fa-exclamation-triangle fa-3x mb-3 d-block"></i>
            <h5>Tidak Ada Perhitungan Gaji yang Siap Dibayar</h5>
            <p class="mb-0">
                Tidak ditemukan perhitungan gaji dengan status "Disetujui" untuk periode 
                <strong><?= $bulanOptions[$bulan] ?> <?= $tahun ?></strong>.
            </p>
            <hr>
            <a href="<?= site_url('accounting/penggajian/perhitungan-gaji?bulan=' . $bulan . '&tahun=' . $tahun . '&status=Disetujui') ?>" 
               class="btn btn-accounting mt-2">
                <i class="fas fa-calculator me-1"></i> Lihat Perhitungan Gaji
            </a>
        </div>
    <?php else: ?>
        <form id="formPembayaran" action="<?= site_url('accounting/penggajian/proses-pembayaran/store') ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="row">
                <div class="col-lg-8">
                    <!-- Informasi Proses -->
                    <div class="card mb-4">
                        <div class="card-header bg-gradient-accounting text-white">
                            <i class="fas fa-info-circle me-2"></i> Informasi Proses Pembayaran
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Nama Proses <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_proses" class="form-control" 
                                           value="Pembayaran Gaji <?= $bulanOptions[$bulan] ?> <?= $tahun ?>" required>
                                    <small class="text-muted">Contoh: Pembayaran Gaji Maret 2026</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Periode</label>
                                    <input type="text" class="form-control" value="<?= $bulanOptions[$bulan] ?> <?= $tahun ?>" readonly disabled>
                                    <input type="hidden" name="periode_bulan" value="<?= $bulan ?>">
                                    <input type="hidden" name="periode_tahun" value="<?= $tahun ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Proses <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_proses" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Pembayaran <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_pembayaran" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                                    <select name="metode_pembayaran" id="metodePembayaran" class="form-select" required>
                                        <option value="">Pilih Metode</option>
                                        <?php foreach ($metodeOptions as $opt): ?>
                                            <option value="<?= $opt ?>"><?= $opt ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3" id="bankPengirimGroup" style="display: none;">
                                    <label class="form-label">Bank Pengirim</label>
                                    <input type="text" name="bank_pengirim" class="form-control" placeholder="Nama Bank Rekening Perusahaan">
                                </div>
                                <div class="col-md-6 mb-3" id="coaBankGroup" style="display: none;">
                                    <label class="form-label">Akun Bank (COA) <span class="text-danger">*</span></label>
                                    <select name="coa_bank_id" id="coa_bank_id" class="form-select">
                                        <option value="">Pilih Akun Bank</option>
                                        <?php foreach ($coaBankOptions as $coa): ?>
                                            <option value="<?= $coa['id'] ?>">
                                                <?= $coa['kode_akun'] ?> - <?= $coa['nama_akun'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted">Akun kas/bank yang akan digunakan untuk pembayaran</small>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Keterangan</label>
                                    <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan tambahan tentang proses pembayaran..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Ringkasan Pembayaran -->
                    <div class="card mb-4 sticky-top" style="top: 20px;">
                        <div class="card-header bg-gradient-accounting text-white">
                            <i class="fas fa-chart-pie me-2"></i> Ringkasan Pembayaran
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <div class="bg-success bg-opacity-10 rounded p-3">
                                    <h2 class="text-success mb-0" id="totalNominalDisplay">
                                        Rp <?= number_format($totalNominal, 0, ',', '.') ?>
                                    </h2>
                                    <small class="text-muted">Total Gaji yang Akan Dibayar</small>
                                </div>
                            </div>
                            <hr>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td>Jumlah Karyawan</td>
                                    <td class="text-end"><strong id="totalKaryawanCount"><?= $totalKaryawan ?></strong> orang</td>
                                </tr>
                                <tr>
                                    <td>Periode</td>
                                    <td class="text-end"><?= $bulanOptions[$bulan] ?> <?= $tahun ?></td>
                                </tr>
                                <tr id="saldoInfo" style="display: none;">
                                    <td>Saldo Bank Tersedia</td>
                                    <td class="text-end"><span id="saldoBankDisplay">Rp 0</span></td>
                                </tr>
                            </table>
                            
                            <div class="alert alert-info small" id="budgetAlert" style="display: none;">
                                <i class="fas fa-info-circle me-1"></i>
                                <span id="budgetMessage"></span>
                            </div>
                            
                            <button type="submit" class="btn btn-accounting w-100 mb-2" id="btnSubmit">
                                <i class="fas fa-save me-1"></i> Simpan Proses Pembayaran
                            </button>
                            <button type="button" class="btn btn-accounting-outline w-100" onclick="window.history.back()">
                                <i class="fas fa-times me-1"></i> Batal
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daftar Karyawan -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-users me-2"></i> Daftar Karyawan yang Akan Dibayar
                    <span class="badge bg-light text-dark ms-2"><?= $totalKaryawan ?> karyawan</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="karyawanTable">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="10%">NIK</th>
                                    <th width="20%">Nama Karyawan</th>
                                    <th width="15%">Jabatan</th>
                                    <th width="10%">Bank</th>
                                    <th width="15%">No Rekening</th>
                                    <th width="15%">Gaji Bersih</th>
                                    <th width="10%">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($perhitungan as $item): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= $item['nomor_karyawan'] ?? $item['nik'] ?? '-' ?></td>
                                    <td>
                                        <strong><?= $item['nama_karyawan'] ?></strong><br>
                                        <small class="text-muted"><?= $item['jabatan'] ?? '-' ?></small>
                                    </td>
                                    <td><?= $item['jabatan'] ?? '-' ?></td>
                                    <td><?= $item['bank'] ?? '-' ?></td>
                                    <td><?= $item['no_rekening'] ?? '-' ?></td>
                                    <td class="text-end text-primary">
                                        <strong>Rp <?= number_format($item['gaji_bersih'], 0, ',', '.') ?></strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success">Disetujui</span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-active">
                                <tr>
                                    <td colspan="6" class="text-end"><strong>Total</strong></td>
                                    <td class="text-end"><strong>Rp <?= number_format($totalNominal, 0, ',', '.') ?></strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <div class="mt-3 text-muted small">
                        <i class="fas fa-info-circle me-1"></i>
                        Data di atas adalah perhitungan gaji dengan status "Disetujui" untuk periode <?= $bulanOptions[$bulan] ?> <?= $tahun ?>.
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
$(document).ready(function() {
    // Metode pembayaran change handler
    $('#metodePembayaran').on('change', function() {
        let metode = $(this).val();
        
        if (metode === 'Transfer Bank') {
            $('#bankPengirimGroup').show();
            $('#coaBankGroup').show();
            $('#saldoInfo').show();
            checkSaldo();
        } else {
            $('#bankPengirimGroup').hide();
            $('#coaBankGroup').hide();
            $('#saldoInfo').hide();
            $('#budgetAlert').hide();
            $('#coa_bank_id').removeAttr('required');
        }
        
        if (metode === 'Transfer Bank') {
            $('#coa_bank_id').attr('required', true);
        } else {
            $('#coa_bank_id').removeAttr('required');
        }
    });
    
    // COA Bank change handler untuk validasi saldo
    $('#coa_bank_id').on('change', function() {
        checkSaldo();
    });
    
    // Initial check if Transfer Bank is selected by default
    if ($('#metodePembayaran').val() === 'Transfer Bank') {
        $('#bankPengirimGroup').show();
        $('#coaBankGroup').show();
        $('#saldoInfo').show();
        checkSaldo();
    }
});

function checkSaldo() {
    let coaBankId = $('#coa_bank_id').val();
    let totalNominal = <?= $totalNominal ?>;
    
    if (!coaBankId) {
        $('#saldoBankDisplay').text('Rp 0');
        $('#budgetAlert').hide();
        return;
    }
    
    $.ajax({
        url: '<?= site_url('accounting/penggajian/proses-pembayaran/ajax-validate-budget') ?>',
        method: 'POST',
        data: {
            coa_bank_id: coaBankId,
            total_nominal: totalNominal
        },
        success: function(response) {
            if (response.success) {
                let saldo = response.saldo_available;
                let totalPayment = response.total_payment;
                let available = response.available;
                
                $('#saldoBankDisplay').text('Rp ' + new Intl.NumberFormat('id-ID').format(saldo));
                
                if (available) {
                    $('#budgetAlert').removeClass('alert-danger').addClass('alert-success');
                    $('#budgetMessage').html('<i class="fas fa-check-circle me-1"></i> ' + response.message);
                    $('#budgetAlert').show();
                    $('#btnSubmit').prop('disabled', false);
                } else {
                    $('#budgetAlert').removeClass('alert-success').addClass('alert-danger');
                    $('#budgetMessage').html('<i class="fas fa-exclamation-triangle me-1"></i> ' + response.message);
                    $('#budgetAlert').show();
                    $('#btnSubmit').prop('disabled', true);
                }
            } else {
                $('#budgetAlert').removeClass('alert-success').addClass('alert-warning');
                $('#budgetMessage').html('<i class="fas fa-exclamation-circle me-1"></i> Gagal memeriksa saldo');
                $('#budgetAlert').show();
            }
        },
        error: function() {
            $('#budgetAlert').removeClass('alert-success').addClass('alert-warning');
            $('#budgetMessage').html('<i class="fas fa-exclamation-circle me-1"></i> Gagal memeriksa saldo bank');
            $('#budgetAlert').show();
        }
    });
}

// Form validation
$('#formPembayaran').on('submit', function(e) {
    let metode = $('#metodePembayaran').val();
    
    if (!metode) {
        e.preventDefault();
        toastr.warning('Pilih metode pembayaran terlebih dahulu');
        return false;
    }
    
    if (metode === 'Transfer Bank') {
        let coaBankId = $('#coa_bank_id').val();
        if (!coaBankId) {
            e.preventDefault();
            toastr.warning('Pilih akun bank untuk pembayaran');
            return false;
        }
    }
    
    return true;
});

// Initialize DataTable
$(document).ready(function() {
    $('#karyawanTable').DataTable({
        "pageLength": 25,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "responsive": true,
        "language": {
            "search": "Cari:",
            "lengthMenu": "Tampilkan _MENU_ data",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Berikutnya",
                "previous": "Sebelumnya"
            }
        }
    });
});
</script>

<?php $this->endSection(); ?>