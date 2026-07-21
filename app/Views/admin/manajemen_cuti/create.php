<?php
// C:\xampp\htdocs\cdwnet\app\Views\admin\manajemen_cuti\create.php
$title = 'Ajukan Cuti';
$active = 'cuti';
$css = [
    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
    'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css'
];
$scripts = [
    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
    'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.js',
    'https://npmcdn.com/flatpickr/dist/l10n/id.js'
];
?>

<?= $this->include('admin/templates/header') ?>
<?= $this->include('admin/templates/sidebar') ?>
<?= $this->include('admin/templates/navbar') ?>

<!-- Main Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Ajukan Cuti</h1>
        <a href="<?= base_url('admin/cuti'); ?>" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-lg-8">
            <!-- Form Pengajuan Cuti -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Pengajuan Cuti</h6>
                </div>
                <div class="card-body">
                    <?php if (session()->has('errors')): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach (session('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->has('error')): ?>
                        <div class="alert alert-danger">
                            <?= session('error') ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('admin/cuti/store'); ?>" method="post" id="cutiForm">
                        <?= csrf_field(); ?>

                        <!-- Karyawan -->
                        <div class="form-group">
                            <label for="karyawan_id" class="font-weight-bold">Karyawan <span class="text-danger">*</span></label>
                            <select class="form-control select2 <?= session('errors.karyawan_id') ? 'is-invalid' : '' ?>" 
                                    id="karyawan_id" name="karyawan_id" required>
                                <option value="">Pilih Karyawan</option>
                                <?php if (isset($karyawan)): ?>
                                    <?php foreach ($karyawan as $k): ?>
                                        <option value="<?= $k['id']; ?>" 
                                                <?= old('karyawan_id') == $k['id'] ? 'selected' : '' ?>>
                                            <?= esc($k['nik']); ?> - <?= esc($k['nama_lengkap']); ?> 
                                            (<?= esc($k['jabatan']); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php if (session('errors.karyawan_id')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.karyawan_id') ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Info Kuota Cuti -->
                        <div class="alert alert-info" id="quotaInfo" style="display: none;">
                            <i class="fas fa-info-circle me-2"></i>
                            <span id="quotaText">Pilih karyawan untuk melihat kuota cuti</span>
                        </div>

                        <!-- Jenis Cuti -->
                        <div class="form-group">
                            <label for="jenis_cuti" class="font-weight-bold">Jenis Cuti <span class="text-danger">*</span></label>
                            <select class="form-control <?= session('errors.jenis_cuti') ? 'is-invalid' : '' ?>" 
                                    id="jenis_cuti" name="jenis_cuti" required>
                                <option value="">Pilih Jenis Cuti</option>
                                <option value="Tahunan" <?= old('jenis_cuti') == 'Tahunan' ? 'selected' : '' ?>>Cuti Tahunan</option>
                                <option value="Hamil" <?= old('jenis_cuti') == 'Hamil' ? 'selected' : '' ?>>Cuti Hamil</option>
                                <option value="Sakit" <?= old('jenis_cuti') == 'Sakit' ? 'selected' : '' ?>>Cuti Sakit</option>
                                <option value="Khusus" <?= old('jenis_cuti') == 'Khusus' ? 'selected' : '' ?>>Cuti Khusus</option>
                                <option value="Lainnya" <?= old('jenis_cuti') == 'Lainnya' ? 'selected' : '' ?>>Cuti Lainnya</option>
                            </select>
                            <?php if (session('errors.jenis_cuti')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.jenis_cuti') ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Tanggal Cuti -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_mulai" class="font-weight-bold">Tanggal Mulai <span class="text-danger">*</span></label>
                                    <input type="date" 
                                           class="form-control <?= session('errors.tanggal_mulai') ? 'is-invalid' : '' ?>" 
                                           id="tanggal_mulai" name="tanggal_mulai" 
                                           value="<?= old('tanggal_mulai', date('Y-m-d')) ?>" 
                                           required>
                                    <?php if (session('errors.tanggal_mulai')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.tanggal_mulai') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_selesai" class="font-weight-bold">Tanggal Selesai <span class="text-danger">*</span></label>
                                    <input type="date" 
                                           class="form-control <?= session('errors.tanggal_selesai') ? 'is-invalid' : '' ?>" 
                                           id="tanggal_selesai" name="tanggal_selesai" 
                                           value="<?= old('tanggal_selesai', date('Y-m-d')) ?>" 
                                           required>
                                    <?php if (session('errors.tanggal_selesai')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.tanggal_selesai') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Info Hari -->
                        <div class="alert alert-warning" id="daysInfo">
                            <i class="fas fa-calendar-alt me-2"></i>
                            <span id="daysText">Hitung hari kerja akan muncul setelah memilih tanggal</span>
                        </div>

                        <!-- Alasan Cuti -->
                        <div class="form-group">
                            <label for="alasan" class="font-weight-bold">Alasan Cuti <span class="text-danger">*</span></label>
                            <textarea class="form-control <?= session('errors.alasan') ? 'is-invalid' : '' ?>" 
                                      id="alasan" name="alasan" 
                                      rows="5" 
                                      placeholder="Jelaskan alasan cuti secara detail..." 
                                      required><?= old('alasan') ?></textarea>
                            <?php if (session('errors.alasan')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.alasan') ?>
                                </div>
                            <?php endif; ?>
                            <small class="form-text text-muted">Minimal 10 karakter</small>
                        </div>

                        <!-- Lampiran (opsional) -->
                        <div class="form-group">
                            <label for="lampiran" class="font-weight-bold">Lampiran (Opsional)</label>
                            <input type="file" class="form-control-file" id="lampiran" name="lampiran">
                            <small class="form-text text-muted">
                                Unggah surat dokter, surat keterangan, atau dokumen pendukung lainnya (Max: 2MB)
                            </small>
                        </div>

                        <hr>

                        <!-- Button Group -->
                        <div class="d-flex justify-content-between">
                            <button type="reset" class="btn btn-secondary">
                                <i class="fas fa-redo me-1"></i> Reset
                            </button>
                            <div>
                                <a href="<?= base_url('admin/cuti'); ?>" class="btn btn-light">
                                    <i class="fas fa-times me-1"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-1"></i> Ajukan Cuti
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Informasi Penting -->
        <div class="col-lg-4">
            <!-- Kuota Cuti -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Informasi Kuota Cuti</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-calendar-check fa-3x text-primary mb-3"></i>
                        <h5>Kuota Cuti Tahunan</h5>
                        <p class="text-muted">Tahun <?= date('Y'); ?></p>
                    </div>
                    
                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Kuota:</span>
                            <span class="font-weight-bold">12 hari</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Terpakai:</span>
                            <span class="font-weight-bold" id="terpakaiText">-</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Sisa:</span>
                            <span class="font-weight-bold text-success" id="sisaText">-</span>
                        </div>
                        
                        <div class="progress mb-4">
                            <div id="quotaProgress" class="progress-bar bg-success" 
                                 role="progressbar" style="width: 0%" 
                                 aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <hr>

                    <h6 class="font-weight-bold mb-3">Ketentuan Cuti:</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <small>Cuti tahunan maksimal 12 hari/tahun</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <small>Cuti sakit dengan surat dokter</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <small>Cuti hamil 3 bulan sebelum dan sesudah melahirkan</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <small>Pengajuan minimal 3 hari sebelumnya</small>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Catatan -->
            <div class="card shadow">
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold">Catatan Penting</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Perhatian:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Pastikan tanggal tidak bentrok dengan cuti lain</li>
                            <li>Ajukan cuti minimal 3 hari kerja sebelumnya</li>
                            <li>Cuti sakit wajib melampirkan surat dokter</li>
                            <li>Status akan dicek oleh HRD dan atasan</li>
                            <li>Cuti dapat dibatalkan sebelum disetujui</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        placeholder: 'Pilih karyawan',
        allowClear: true
    });

    // Initialize flatpickr for date inputs
    flatpickr("#tanggal_mulai", {
        dateFormat: "Y-m-d",
        locale: "id",
        minDate: "today",
        onChange: function(selectedDates, dateStr) {
            calculateDays();
        }
    });

    flatpickr("#tanggal_selesai", {
        dateFormat: "Y-m-d",
        locale: "id",
        minDate: "today",
        onChange: function(selectedDates, dateStr) {
            calculateDays();
        }
    });

    // Load quota when karyawan is selected
    $('#karyawan_id').change(function() {
        const karyawanId = $(this).val();
        if (karyawanId) {
            loadQuota(karyawanId);
        } else {
            hideQuotaInfo();
        }
    });

    // Calculate days when dates change
    $('#tanggal_mulai, #tanggal_selesai').change(function() {
        calculateDays();
    });

    // Jenis cuti change handler
    $('#jenis_cuti').change(function() {
        const jenisCuti = $(this).val();
        const karyawanId = $('#karyawan_id').val();
        
        if (karyawanId) {
            loadQuota(karyawanId);
        }
        
        // Show/hide lampiran requirement
        if (jenisCuti === 'Sakit') {
            $('#lampiran').prop('required', true).prev('label').append(' <span class="text-danger">*</span>');
        } else {
            $('#lampiran').prop('required', false).prev('label').find('.text-danger').remove();
        }
    });

    // Form validation
    $('#cutiForm').submit(function(e) {
        const tanggalMulai = $('#tanggal_mulai').val();
        const tanggalSelesai = $('#tanggal_selesai').val();
        const karyawanId = $('#karyawan_id').val();
        const jenisCuti = $('#jenis_cuti').val();
        
        if (!tanggalMulai || !tanggalSelesai || !karyawanId || !jenisCuti) {
            e.preventDefault();
            alert('Harap lengkapi semua data yang diperlukan!');
            return false;
        }

        // Check if end date is before start date
        const start = new Date(tanggalMulai);
        const end = new Date(tanggalSelesai);
        
        if (end < start) {
            e.preventDefault();
            alert('Tanggal selesai tidak boleh sebelum tanggal mulai!');
            return false;
        }

        // Check if cuti is for future date (at least tomorrow)
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (start <= today) {
            if (!confirm('Anda mengajukan cuti untuk hari ini atau tanggal yang sudah lewat. Apakah Anda yakin?')) {
                e.preventDefault();
                return false;
            }
        }

        return true;
    });

    // Load initial data if there's old input
    <?php if (old('karyawan_id')): ?>
        loadQuota(<?= old('karyawan_id'); ?>);
    <?php endif; ?>

    <?php if (old('tanggal_mulai') && old('tanggal_selesai')): ?>
        calculateDays();
    <?php endif; ?>
});

function loadQuota(karyawanId) {
    $.ajax({
        url: '<?= base_url("admin/cuti/check-quota"); ?>',
        type: 'GET',
        data: { karyawan_id: karyawanId },
        success: function(response) {
            if (response.error) {
                $('#quotaInfo').hide();
                return;
            }

            const quota = response.quota;
            const used = response.terpakai;
            const remaining = response.sisa;
            const percentage = (used / quota) * 100;

            // Update quota display
            $('#terpakaiText').text(used + ' hari');
            $('#sisaText').text(remaining + ' hari');
            $('#quotaProgress').css('width', percentage + '%').attr('aria-valuenow', percentage);

            // Update quota info text
            const jenisCuti = $('#jenis_cuti').val();
            if (jenisCuti === 'Tahunan') {
                $('#quotaText').html(`
                    Kuota cuti tahunan: <strong>${quota} hari</strong> | 
                    Terpakai: <strong>${used} hari</strong> | 
                    Sisa: <strong>${remaining} hari</strong>
                `);
                $('#quotaInfo').show().removeClass('alert-danger').addClass('alert-info');
                
                // Show warning if quota insufficient
                if (remaining <= 0) {
                    $('#quotaInfo').removeClass('alert-info').addClass('alert-danger')
                        .html('<i class="fas fa-exclamation-triangle me-2"></i>Kuota cuti tahunan sudah habis! Pilih jenis cuti lain.');
                }
            } else {
                $('#quotaText').html('Untuk jenis cuti <strong>' + jenisCuti + '</strong>, kuota tidak dikurangi dari cuti tahunan.');
                $('#quotaInfo').show().removeClass('alert-danger').addClass('alert-info');
            }
        },
        error: function() {
            $('#quotaInfo').hide();
        }
    });
}

function hideQuotaInfo() {
    $('#quotaInfo').hide();
    $('#terpakaiText').text('-');
    $('#sisaText').text('-');
    $('#quotaProgress').css('width', '0%').attr('aria-valuenow', 0);
}

function calculateDays() {
    const startDate = $('#tanggal_mulai').val();
    const endDate = $('#tanggal_selesai').val();
    
    if (!startDate || !endDate) {
        $('#daysText').text('Hitung hari kerja akan muncul setelah memilih tanggal');
        return;
    }

    // Check if end date is before start date
    if (new Date(endDate) < new Date(startDate)) {
        $('#daysText').html('<span class="text-danger">Tanggal selesai tidak boleh sebelum tanggal mulai!</span>');
        $('#daysInfo').removeClass('alert-warning').addClass('alert-danger');
        return;
    }

    $.ajax({
        url: '<?= base_url("admin/cuti/calculate-days"); ?>',
        type: 'GET',
        data: { 
            start_date: startDate,
            end_date: endDate
        },
        success: function(response) {
            if (response.error) {
                $('#daysText').text('Terjadi kesalahan saat menghitung hari.');
                return;
            }

            const totalDays = response.total_days;
            const workDays = response.work_days;
            const weekendDays = response.weekend_days;
            
            let daysText = `
                <strong>${totalDays} hari kalender</strong> (${workDays} hari kerja, ${weekendDays} hari weekend)
            `;
            
            // Check if it's too long
            if (workDays > 30) {
                daysText += '<br><span class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i>Cuti terlalu lama, perlu persetujuan khusus!</span>';
                $('#daysInfo').removeClass('alert-warning').addClass('alert-danger');
            } else {
                $('#daysInfo').removeClass('alert-danger').addClass('alert-warning');
            }

            $('#daysText').html(daysText);
            
            // Check quota if it's tahunan cuti
            const jenisCuti = $('#jenis_cuti').val();
            const karyawanId = $('#karyawan_id').val();
            
            if (jenisCuti === 'Tahunan' && karyawanId) {
                setTimeout(() => {
                    loadQuota(karyawanId);
                    checkQuotaSufficiency(workDays);
                }, 100);
            }
        },
        error: function() {
            $('#daysText').text('Terjadi kesalahan saat menghitung hari.');
        }
    });
}

function checkQuotaSufficiency(requiredDays) {
    const remaining = parseInt($('#sisaText').text().replace(' hari', ''));
    
    if (!isNaN(remaining) && remaining < requiredDays) {
        $('#daysInfo').removeClass('alert-warning').addClass('alert-danger');
        $('#daysText').append(`<br><span class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i>Sisa kuota (${remaining} hari) tidak mencukupi!</span>`);
    }
}
</script>

<?= $this->include('admin/templates/footer') ?>