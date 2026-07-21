<?php
// app/Views/accounting/penggajian/proses-pembayaran/edit.php
$data['active'] = 'proses-pembayaran';
$this->extend('accounting/templates/header');
?>

<?php $this->section('content'); ?>
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 text-gradient-accounting">Edit Proses Pembayaran Gaji</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-2">
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/penggajian') ?>">Penggajian</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/penggajian/proses-pembayaran') ?>">Proses Pembayaran</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= site_url('accounting/penggajian/proses-pembayaran') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Status: <?= $proses['status'] ?></strong> - 
        <?php if ($proses['status'] == 'Draft'): ?>
            Proses dalam status draft, Anda dapat mengedit data.
        <?php elseif ($proses['status'] == 'Diproses'): ?>
            Proses sedang diproses, hanya dapat dibatalkan.
        <?php else: ?>
            Proses sudah selesai, tidak dapat diedit.
        <?php endif; ?>
    </div>

    <?php if ($proses['status'] != 'Draft'): ?>
        <!-- Read-only view for non-draft status -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <i class="fas fa-lock me-2"></i> Detail Proses Pembayaran (Read Only)
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Nomor Proses</label>
                        <p class="mb-0 fw-bold"><code><?= $proses['nomor_proses'] ?></code></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Nama Proses</label>
                        <p class="mb-0"><?= $proses['nama_proses'] ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Periode</label>
                        <p class="mb-0"><?= $bulanOptions[$proses['periode_bulan']] ?> <?= $proses['periode_tahun'] ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Status</label>
                        <p class="mb-0">
                            <span class="badge bg-<?= $proses['status'] == 'Selesai' ? 'success' : ($proses['status'] == 'Diproses' ? 'info' : 'secondary') ?>">
                                <?= $proses['status'] ?>
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Tanggal Proses</label>
                        <p class="mb-0"><?= date('d/m/Y', strtotime($proses['tanggal_proses'])) ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Tanggal Pembayaran</label>
                        <p class="mb-0"><?= date('d/m/Y', strtotime($proses['tanggal_pembayaran'])) ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Metode Pembayaran</label>
                        <p class="mb-0"><?= $proses['metode_pembayaran'] ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Bank Pengirim</label>
                        <p class="mb-0"><?= $proses['bank_pengirim'] ?? '-' ?></p>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="text-muted small">Keterangan</label>
                        <p class="mb-0"><?= $proses['keterangan'] ?? '-' ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Daftar Karyawan (Read-only) -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-users me-2"></i> Daftar Karyawan yang Dibayar
                <span class="badge bg-light text-dark ms-2"><?= count($details) ?> karyawan</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">NIK</th>
                                <th width="20%">Nama Karyawan</th>
                                <th width="15%">Bank</th>
                                <th width="15%">No Rekening</th>
                                <th width="15%">Gaji Bersih</th>
                                <th width="10%">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($details as $item): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td><?= $item['nomor_karyawan'] ?? '-' ?></td>
                                <td>
                                    <strong><?= $item['nama_karyawan'] ?></strong>
                                </td>
                                <td><?= $item['bank'] ?? '-' ?></td>
                                <td><?= $item['no_rekening'] ?? '-' ?></td>
                                <td class="text-end text-primary">
                                    <strong>Rp <?= number_format($item['gaji_bersih'], 0, ',', '.') ?></strong>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $statusBadge = match($item['status_pembayaran']) {
                                        'Berhasil' => 'success',
                                        'Gagal' => 'danger',
                                        'Pending' => 'warning',
                                        default => 'secondary'
                                    };
                                    ?>
                                    <span class="badge bg-<?= $statusBadge ?>"><?= $item['status_pembayaran'] ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-active">
                            <tr>
                                <td colspan="5" class="text-end"><strong>Total</strong></td>
                                <td class="text-end"><strong>Rp <?= number_format($proses['total_nominal'], 0, ',', '.') ?></strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        
        <?php if ($proses['status'] == 'Diproses'): ?>
        <div class="mt-4 text-center">
            <button type="button" class="btn btn-success" onclick="completePayment(<?= $proses['id'] ?>)">
                <i class="fas fa-check-double me-1"></i> Selesaikan Pembayaran
            </button>
            <button type="button" class="btn btn-danger ms-2" onclick="cancelPayment(<?= $proses['id'] ?>)">
                <i class="fas fa-ban me-1"></i> Batalkan Pembayaran
            </button>
        </div>
        <?php endif; ?>
        
    <?php else: ?>
        <!-- Edit Form for Draft Status -->
        <form id="formPembayaran" action="<?= site_url('accounting/penggajian/proses-pembayaran/update/' . $proses['id']) ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="PUT">
            
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
                                    <label class="form-label">Nomor Proses</label>
                                    <input type="text" class="form-control" value="<?= $proses['nomor_proses'] ?>" readonly disabled>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Nama Proses <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_proses" class="form-control" value="<?= $proses['nama_proses'] ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Periode</label>
                                    <input type="text" class="form-control" value="<?= $bulanOptions[$proses['periode_bulan']] ?> <?= $proses['periode_tahun'] ?>" readonly disabled>
                                    <input type="hidden" name="periode_bulan" value="<?= $proses['periode_bulan'] ?>">
                                    <input type="hidden" name="periode_tahun" value="<?= $proses['periode_tahun'] ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Proses <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_proses" class="form-control" value="<?= $proses['tanggal_proses'] ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Pembayaran <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_pembayaran" class="form-control" value="<?= $proses['tanggal_pembayaran'] ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                                    <select name="metode_pembayaran" id="metodePembayaran" class="form-select" required>
                                        <option value="">Pilih Metode</option>
                                        <?php foreach ($metodeOptions as $opt): ?>
                                            <option value="<?= $opt ?>" <?= $proses['metode_pembayaran'] == $opt ? 'selected' : '' ?>>
                                                <?= $opt ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3" id="bankPengirimGroup" style="<?= $proses['metode_pembayaran'] == 'Transfer Bank' ? '' : 'display: none;' ?>">
                                    <label class="form-label">Bank Pengirim</label>
                                    <input type="text" name="bank_pengirim" class="form-control" value="<?= $proses['bank_pengirim'] ?>" placeholder="Nama Bank Rekening Perusahaan">
                                </div>
                                <div class="col-md-6 mb-3" id="coaBankGroup" style="<?= $proses['metode_pembayaran'] == 'Transfer Bank' ? '' : 'display: none;' ?>">
                                    <label class="form-label">Akun Bank (COA) <span class="text-danger">*</span></label>
                                    <select name="coa_bank_id" id="coa_bank_id" class="form-select">
                                        <option value="">Pilih Akun Bank</option>
                                        <?php foreach ($coaBankOptions as $coa): ?>
                                            <option value="<?= $coa['id'] ?>" <?= $proses['coa_bank_id'] == $coa['id'] ? 'selected' : '' ?>>
                                                <?= $coa['kode_akun'] ?> - <?= $coa['nama_akun'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted">Akun kas/bank yang akan digunakan untuk pembayaran</small>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Keterangan</label>
                                    <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan tambahan tentang proses pembayaran..."><?= $proses['keterangan'] ?></textarea>
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
                                    <h2 class="text-success mb-0">
                                        Rp <?= number_format($proses['total_nominal'], 0, ',', '.') ?>
                                    </h2>
                                    <small class="text-muted">Total Gaji yang Dibayar</small>
                                </div>
                            </div>
                            <hr>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td>Jumlah Karyawan</td>
                                    <td class="text-end"><strong><?= $proses['total_karyawan'] ?></strong> orang</td>
                                </tr>
                                <tr>
                                    <td>Periode</td>
                                    <td class="text-end"><?= $bulanOptions[$proses['periode_bulan']] ?> <?= $proses['periode_tahun'] ?></td>
                                </tr>
                                <tr id="saldoInfo" style="<?= $proses['metode_pembayaran'] == 'Transfer Bank' ? '' : 'display: none;' ?>">
                                    <td>Saldo Bank Tersedia</td>
                                    <td class="text-end"><span id="saldoBankDisplay">Rp 0</span></td>
                                </tr>
                            </table>
                            
                            <div class="alert alert-info small" id="budgetAlert" style="display: none;">
                                <i class="fas fa-info-circle me-1"></i>
                                <span id="budgetMessage"></span>
                            </div>
                            
                            <button type="submit" class="btn btn-accounting w-100 mb-2" id="btnSubmit">
                                <i class="fas fa-save me-1"></i> Update Proses
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
                    <span class="badge bg-light text-dark ms-2"><?= count($details) ?> karyawan</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="karyawanTable">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="10%">NIK</th>
                                    <th width="20%">Nama Karyawan</th>
                                    <th width="15%">Bank</th>
                                    <th width="15%">No Rekening</th>
                                    <th width="15%">Gaji Bersih</th>
                                    <th width="10%">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($details as $item): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= $item['nomor_karyawan'] ?? '-' ?></td>
                                    <td>
                                        <strong><?= $item['nama_karyawan'] ?></strong>
                                    </td>
                                    <td><?= $item['bank'] ?? '-' ?></td>
                                    <td><?= $item['no_rekening'] ?? '-' ?></td>
                                    <td class="text-end text-primary">
                                        <strong>Rp <?= number_format($item['gaji_bersih'], 0, ',', '.') ?></strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning">Pending</span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-active">
                                <tr>
                                    <td colspan="5" class="text-end"><strong>Total</strong></td>
                                    <td class="text-end"><strong>Rp <?= number_format($proses['total_nominal'], 0, ',', '.') ?></strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <div class="mt-3 text-muted small">
                        <i class="fas fa-info-circle me-1"></i>
                        Data karyawan diambil dari perhitungan gaji yang sudah disetujui. Tidak dapat diedit.
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
function completePayment(id) {
    if (confirm('Selesaikan pembayaran ini? Aksi ini akan membuat jurnal dan mutasi bank.')) {
        $.ajax({
            url: '<?= site_url('accounting/penggajian/proses-pembayaran/complete') ?>/' + id,
            method: 'POST',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(() => {
                        window.location.href = '<?= site_url('accounting/penggajian/proses-pembayaran') ?>';
                    }, 1500);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('Terjadi kesalahan saat menyelesaikan pembayaran');
            }
        });
    }
}

function cancelPayment(id) {
    if (confirm('Batalkan proses pembayaran ini? Tindakan ini tidak dapat dibatalkan.')) {
        $.ajax({
            url: '<?= site_url('accounting/penggajian/proses-pembayaran/cancel') ?>/' + id,
            method: 'POST',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('Terjadi kesalahan saat membatalkan pembayaran');
            }
        });
    }
}

<?php if ($proses['status'] == 'Draft'): ?>
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
    
    // COA Bank change handler
    $('#coa_bank_id').on('change', function() {
        checkSaldo();
    });
    
    // Initial check
    if ($('#metodePembayaran').val() === 'Transfer Bank') {
        checkSaldo();
    }
});

function checkSaldo() {
    let coaBankId = $('#coa_bank_id').val();
    let totalNominal = <?= $proses['total_nominal'] ?>;
    
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
<?php endif; ?>
</script>

<?php $this->endSection(); ?>