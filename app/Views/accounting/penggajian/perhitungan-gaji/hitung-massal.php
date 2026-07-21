<?php
// app/Views/accounting/penggajian/perhitungan-gaji/hitung-massal.php
$data['active'] = 'perhitungan-gaji';
$this->extend('accounting/templates/header');
?>

<?php $this->section('content'); ?>
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 text-gradient-accounting">Hitung Gaji Massal</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-2">
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/penggajian') ?>">Penggajian</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/penggajian/perhitungan-gaji') ?>">Perhitungan Gaji</a></li>
                    <li class="breadcrumb-item active">Hitung Massal</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= site_url('accounting/penggajian/perhitungan-gaji') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Alert Info -->
    <div class="alert alert-info mb-4">
        <div class="d-flex">
            <div class="me-3">
                <i class="fas fa-info-circle fa-2x"></i>
            </div>
            <div>
                <h5 class="mb-1">Informasi Perhitungan Massal</h5>
                <p class="mb-0">
                    Halaman ini digunakan untuk menghitung gaji beberapa karyawan sekaligus dalam satu periode.
                    Pilih karyawan yang akan dihitung dan mode perhitungan yang diinginkan.
                </p>
            </div>
        </div>
    </div>

    <!-- Filter Periode -->
    <div class="card mb-4">
        <div class="card-header bg-gradient-accounting text-white">
            <i class="fas fa-calendar-alt me-2"></i> Filter Periode
        </div>
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Bulan</label>
                    <select name="bulan" class="form-select" id="filterBulan">
                        <?php foreach ($bulanOptions as $key => $val): ?>
                            <option value="<?= $key ?>" <?= $bulan == $key ? 'selected' : '' ?>>
                                <?= $val ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tahun</label>
                    <select name="tahun" class="form-select" id="filterTahun">
                        <?php foreach ($tahunOptions as $t): ?>
                            <option value="<?= $t ?>" <?= $tahun == $t ? 'selected' : '' ?>>
                                <?= $t ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-accounting w-100">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-accounting-outline w-100" onclick="selectAll()">
                        <i class="fas fa-check-double me-1"></i> Pilih Semua
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Form Perhitungan Massal -->
    <form id="formMassal" action="<?= site_url('accounting/penggajian/perhitungan-gaji/proses-hitung-massal') ?>" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="periode_bulan" value="<?= $bulan ?>">
        <input type="hidden" name="periode_tahun" value="<?= $tahun ?>">
        <input type="hidden" name="tanggal_perhitungan" value="<?= date('Y-m-d') ?>">
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Daftar Karyawan -->
                <div class="card mb-4">
                    <div class="card-header bg-gradient-accounting text-white">
                        <i class="fas fa-users me-2"></i> Daftar Karyawan
                        <span class="badge bg-light text-dark ms-2" id="selectedCount">0</span>
                    </div>
                    <div class="card-body">
                        <?php if (empty($karyawan)): ?>
                            <div class="alert alert-warning text-center">
                                <i class="fas fa-exclamation-triangle fa-2x mb-2 d-block"></i>
                                <h5>Tidak Ada Karyawan</h5>
                                <p class="mb-0">Semua karyawan sudah memiliki perhitungan gaji untuk periode <?= $bulanOptions[$bulan] ?> <?= $tahun ?>.</p>
                                <a href="<?= site_url('accounting/penggajian/perhitungan-gaji') ?>" class="btn btn-accounting mt-3">
                                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="karyawanTable">
                                    <thead>
                                        <tr>
                                            <th width="5%">
                                                <input type="checkbox" id="checkAll" class="form-check-input">
                                            </th>
                                            <th width="10%">NIK</th>
                                            <th width="25%">Nama Karyawan</th>
                                            <th width="20%">Jabatan</th>
                                            <th width="15%">Gaji Pokok</th>
                                            <th width="15%">Tunjangan</th>
                                            <th width="10%">Gaji Referensi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($karyawan as $karyawanItem): ?>
                                            <tr>
                                                <td class="text-center">
                                                    <input type="checkbox" name="karyawan_id[]" value="<?= $karyawanItem['id'] ?>" class="form-check-input karyawan-checkbox">
                                                </td>
                                                <td><?= $karyawanItem['nik'] ?></td>
                                                <td>
                                                    <strong><?= $karyawanItem['nama_lengkap'] ?></strong><br>
                                                    <small class="text-muted"><?= $karyawanItem['departemen'] ?></small>
                                                </td>
                                                <td><?= $karyawanItem['jabatan'] ?></td>
                                                <td class="text-end">Rp <?= number_format($karyawanItem['gaji_pokok'] ?? 0, 0, ',', '.') ?></td>
                                                <td class="text-end">Rp <?= number_format(($karyawanItem['tunjangan_jabatan'] ?? 0) + ($karyawanItem['tunjangan_makan'] ?? 0) + ($karyawanItem['tunjangan_transport'] ?? 0), 0, ',', '.') ?></td>
                                                <td class="text-end">
                                                    <strong class="text-primary">Rp <?= number_format(($karyawanItem['gaji_pokok'] ?? 0) + ($karyawanItem['tunjangan_jabatan'] ?? 0) + ($karyawanItem['tunjangan_makan'] ?? 0) + ($karyawanItem['tunjangan_transport'] ?? 0), 0, ',', '.') ?></strong>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Menampilkan <?= count($karyawan) ?> karyawan yang belum memiliki perhitungan gaji untuk periode ini
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Pengaturan Perhitungan -->
                <div class="card mb-4 sticky-top" style="top: 20px;">
                    <div class="card-header bg-gradient-accounting text-white">
                        <i class="fas fa-cog me-2"></i> Pengaturan Perhitungan
                    </div>
                    <div class="card-body">
                        <!-- Mode Perhitungan -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Mode Perhitungan</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="mode" id="modeSystem" value="system" checked>
                                <label class="form-check-label" for="modeSystem">
                                    <i class="fas fa-chart-line text-success me-1"></i>
                                    <strong>Mode Sistem</strong>
                                    <br>
                                    <small class="text-muted">Menghitung gaji berdasarkan data karyawan (gaji pokok + tunjangan)</small>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mode" id="modeFixed" value="fixed">
                                <label class="form-check-label" for="modeFixed">
                                    <i class="fas fa-lock text-warning me-1"></i>
                                    <strong>Mode Tetap</strong>
                                    <br>
                                    <small class="text-muted">Menggunakan nominal gaji tetap yang dapat diatur per karyawan</small>
                                </label>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <!-- Informasi Ringkasan -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ringkasan</label>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td>Total Karyawan Tersedia</td>
                                    <td class="text-end"><strong id="totalTersedia"><?= count($karyawan) ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Karyawan Dipilih</td>
                                    <td class="text-end"><strong id="totalTerpilih">0</strong></td>
                                </tr>
                                <tr>
                                    <td>Estimasi Total Gaji</td>
                                    <td class="text-end"><strong id="estimasiTotal">Rp 0</strong></td>
                                </tr>
                            </table>
                        </div>
                        
                        <hr>
                        
                        <!-- Tombol Aksi -->
                        <button type="submit" class="btn btn-accounting w-100 mb-2" id="btnProses" disabled>
                            <i class="fas fa-calculator me-1"></i> Proses Perhitungan
                        </button>
                        <button type="button" class="btn btn-accounting-outline w-100" onclick="resetSelection()">
                            <i class="fas fa-undo-alt me-1"></i> Reset Pilihan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal untuk Input Gaji Tetap (Mode Fixed) -->
<div class="modal fade" id="fixedSalaryModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-lock me-2"></i> Atur Gaji Tetap per Karyawan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle me-1"></i>
                    Isi nominal gaji tetap untuk setiap karyawan yang dipilih.
                </div>
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-sm table-bordered" id="fixedSalaryTable">
                        <thead>
                            <tr>
                                <th width="30%">Karyawan</th>
                                <th width="40%">Gaji Referensi</th>
                                <th width="30%">Gaji Tetap <span class="text-danger">*</span></th>
                            </tr>
                        </thead>
                        <tbody id="fixedSalaryBody">
                            <!-- Dynamic content from JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" id="confirmFixedSalary">
                    <i class="fas fa-check me-1"></i> Konfirmasi & Proses
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let selectedKaryawan = [];
let karyawanData = <?= json_encode($karyawan) ?>;

$(document).ready(function() {
    // Check all functionality
    $('#checkAll').on('change', function() {
        $('.karyawan-checkbox').prop('checked', $(this).prop('checked'));
        updateSelection();
    });
    
    // Individual checkbox change
    $('.karyawan-checkbox').on('change', function() {
        updateSelection();
        $('#checkAll').prop('checked', $('.karyawan-checkbox:checked').length === $('.karyawan-checkbox').length);
    });
    
    // Mode change handler
    $('input[name="mode"]').on('change', function() {
        updateSelection();
    });
    
    // Initial update
    updateSelection();
});

function updateSelection() {
    selectedKaryawan = [];
    let totalGaji = 0;
    
    $('.karyawan-checkbox:checked').each(function() {
        let row = $(this).closest('tr');
        let karyawanId = $(this).val();
        let karyawan = karyawanData.find(k => k.id == karyawanId);
        
        if (karyawan) {
            let gajiReferensi = (karyawan.gaji_pokok || 0) + (karyawan.tunjangan_jabatan || 0) + 
                                (karyawan.tunjangan_makan || 0) + (karyawan.tunjangan_transport || 0);
            
            selectedKaryawan.push({
                id: karyawanId,
                nama: karyawan.nama_lengkap,
                nik: karyawan.nik,
                gaji_referensi: gajiReferensi,
                gaji_pokok: karyawan.gaji_pokok || 0,
                tunjangan_jabatan: karyawan.tunjangan_jabatan || 0,
                tunjangan_makan: karyawan.tunjangan_makan || 0,
                tunjangan_transport: karyawan.tunjangan_transport || 0
            });
            
            totalGaji += gajiReferensi;
        }
    });
    
    $('#totalTerpilih').text(selectedKaryawan.length);
    $('#estimasiTotal').text('Rp ' + new Intl.NumberFormat('id-ID').format(totalGaji));
    
    // Enable/disable proses button
    $('#btnProses').prop('disabled', selectedKaryawan.length === 0);
}

function selectAll() {
    $('#checkAll').prop('checked', true);
    $('.karyawan-checkbox').prop('checked', true);
    updateSelection();
}

function resetSelection() {
    $('#checkAll').prop('checked', false);
    $('.karyawan-checkbox').prop('checked', false);
    updateSelection();
}

// Form submission handler
$('#formMassal').on('submit', function(e) {
    e.preventDefault();
    
    if (selectedKaryawan.length === 0) {
        toastr.warning('Pilih minimal satu karyawan untuk dihitung');
        return;
    }
    
    let mode = $('input[name="mode"]:checked').val();
    
    if (mode === 'fixed') {
        // Show modal to input fixed salaries
        showFixedSalaryModal();
    } else {
        // Direct submission for system mode
        submitForm();
    }
});

function showFixedSalaryModal() {
    let html = '';
    
    selectedKaryawan.forEach(function(karyawan, index) {
        let gajiReferensi = karyawan.gaji_referensi;
        html += `
            <tr>
                <td>
                    <strong>${karyawan.nama}</strong><br>
                    <small class="text-muted">${karyawan.nik}</small>
                    <input type="hidden" name="karyawan_id_${index}" value="${karyawan.id}">
                </td>
                <td class="text-end">
                    Rp ${new Intl.NumberFormat('id-ID').format(gajiReferensi)}
                    <br>
                    <small class="text-muted">(Gaji Pokok + Tunjangan)</small>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" name="gaji_bersih_${karyawan.id}" class="form-control currency-input-fixed" 
                               value="${new Intl.NumberFormat('id-ID').format(gajiReferensi)}" required>
                    </div>
                </td>
            </tr>
        `;
    });
    
    $('#fixedSalaryBody').html(html);
    $('#fixedSalaryModal').modal('show');
    
    // Format currency inputs
    $('.currency-input-fixed').on('input', function() {
        let value = $(this).val().replace(/[^0-9]/g, '');
        if (value) {
            $(this).val(new Intl.NumberFormat('id-ID').format(parseInt(value)));
        }
    });
}

function submitForm() {
    let mode = $('input[name="mode"]:checked').val();
    let formData = new FormData($('#formMassal')[0]);
    
    if (mode === 'fixed') {
        // Add fixed salary values from modal
        selectedKaryawan.forEach(function(karyawan) {
            let gajiInput = $(`input[name="gaji_bersih_${karyawan.id}"]`);
            if (gajiInput.length) {
                let gajiValue = gajiInput.val().replace(/[^0-9]/g, '');
                formData.set(`gaji_bersih_${karyawan.id}`, gajiValue || '0');
            } else {
                formData.set(`gaji_bersih_${karyawan.id}`, karyawan.gaji_referensi);
            }
        });
    }
    
    // Show loading
    let btnProses = $('#btnProses');
    let originalText = btnProses.html();
    btnProses.html('<i class="fas fa-spinner fa-spin me-1"></i> Memproses...').prop('disabled', true);
    
    $.ajax({
        url: $('#formMassal').attr('action'),
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                if (response.errors_detail) {
                    console.log('Errors:', response.errors_detail);
                }
                setTimeout(() => {
                    window.location.href = '<?= site_url('accounting/penggajian/perhitungan-gaji') ?>';
                }, 1500);
            } else {
                toastr.error(response.message);
                btnProses.html(originalText).prop('disabled', false);
            }
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            toastr.error('Terjadi kesalahan saat memproses perhitungan');
            btnProses.html(originalText).prop('disabled', false);
        }
    });
}

$('#confirmFixedSalary').on('click', function() {
    // Validate all fixed salary inputs
    let isValid = true;
    $('.currency-input-fixed').each(function() {
        let value = $(this).val().replace(/[^0-9]/g, '');
        if (!value || parseInt(value) <= 0) {
            $(this).addClass('is-invalid');
            isValid = false;
        } else {
            $(this).removeClass('is-invalid');
        }
    });
    
    if (isValid) {
        $('#fixedSalaryModal').modal('hide');
        submitForm();
    } else {
        toastr.warning('Pastikan semua nominal gaji tetap diisi dengan benar');
    }
});

// Format currency function for display
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
}
</script>

<?php $this->endSection(); ?>