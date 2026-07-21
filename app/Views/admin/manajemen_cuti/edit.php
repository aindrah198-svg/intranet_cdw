<?php
// C:\xampp\htdocs\cdwnet\app\Views\admin\manajemen_cuti\edit.php
$title = 'Edit Pengajuan Cuti';
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
        <h1 class="h3 mb-0 text-gray-800">Edit Pengajuan Cuti</h1>
        <div>
            <a href="<?= base_url('admin/cuti/show/' . $cuti['id']); ?>" class="btn btn-sm btn-info shadow-sm">
                <i class="fas fa-eye fa-sm text-white-50"></i> Detail
            </a>
            <a href="<?= base_url('admin/cuti'); ?>" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-lg-8">
            <!-- Form Edit Cuti -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Form Edit Pengajuan Cuti</h6>
                    <span class="badge bg-<?= getStatusBadgeClass($cuti['status']) ?>">
                        <?= $cuti['status']; ?>
                    </span>
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

                    <!-- Status Alert -->
                    <?php if (!in_array($cuti['status'], ['Draft', 'Menunggu'])): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Perhatian!</strong> Cuti dengan status <strong><?= $cuti['status']; ?></strong> tidak dapat diedit. 
                            <?php if ($cuti['status'] === 'Disetujui HRD' || $cuti['status'] === 'Disetujui Atasan'): ?>
                                <br>Hubungi HRD untuk perubahan.
                            <?php elseif ($cuti['status'] === 'Ditolak'): ?>
                                <br>Ajukan cuti baru dengan data yang diperbaiki.
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('admin/cuti/update/' . $cuti['id']); ?>" method="post" id="cutiForm">
                        <?= csrf_field(); ?>

                        <!-- Informasi Cuti -->
                        <div class="alert alert-info">
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">Nomor Cuti</small><br>
                                    <strong><?= $cuti['nomor_cuti']; ?></strong>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Tanggal Pengajuan</small><br>
                                    <strong><?= date('d/m/Y H:i', strtotime($cuti['tanggal_pengajuan'])); ?></strong>
                                </div>
                            </div>
                        </div>

                        <!-- Karyawan (readonly jika sudah disetujui) -->
                        <div class="form-group">
                            <label for="karyawan_id" class="font-weight-bold">Karyawan <span class="text-danger">*</span></label>
                            <?php if (in_array($cuti['status'], ['Draft', 'Menungju'])): ?>
                                <select class="form-control select2 <?= session('errors.karyawan_id') ? 'is-invalid' : '' ?>" 
                                        id="karyawan_id" name="karyawan_id" required>
                                    <option value="">Pilih Karyawan</option>
                                    <?php if (isset($karyawan)): ?>
                                        <?php foreach ($karyawan as $k): ?>
                                            <option value="<?= $k['id']; ?>" 
                                                    <?= old('karyawan_id', $cuti['karyawan_id']) == $k['id'] ? 'selected' : '' ?>>
                                                <?= esc($k['nik']); ?> - <?= esc($k['nama_lengkap']); ?> 
                                                (<?= esc($k['jabatan']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            <?php else: ?>
                                <input type="text" class="form-control" value="<?= $cuti['nama_lengkap'] ?? 'Karyawan tidak ditemukan'; ?>" readonly>
                                <input type="hidden" name="karyawan_id" value="<?= $cuti['karyawan_id']; ?>">
                            <?php endif; ?>
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
                            <?php if (in_array($cuti['status'], ['Draft', 'Menungju'])): ?>
                                <select class="form-control <?= session('errors.jenis_cuti') ? 'is-invalid' : '' ?>" 
                                        id="jenis_cuti" name="jenis_cuti" required>
                                    <option value="">Pilih Jenis Cuti</option>
                                    <option value="Tahunan" <?= old('jenis_cuti', $cuti['jenis_cuti']) == 'Tahunan' ? 'selected' : '' ?>>Cuti Tahunan</option>
                                    <option value="Hamil" <?= old('jenis_cuti', $cuti['jenis_cuti']) == 'Hamil' ? 'selected' : '' ?>>Cuti Hamil</option>
                                    <option value="Sakit" <?= old('jenis_cuti', $cuti['jenis_cuti']) == 'Sakit' ? 'selected' : '' ?>>Cuti Sakit</option>
                                    <option value="Khusus" <?= old('jenis_cuti', $cuti['jenis_cuti']) == 'Khusus' ? 'selected' : '' ?>>Cuti Khusus</option>
                                    <option value="Lainnya" <?= old('jenis_cuti', $cuti['jenis_cuti']) == 'Lainnya' ? 'selected' : '' ?>>Cuti Lainnya</option>
                                </select>
                            <?php else: ?>
                                <input type="text" class="form-control" value="<?= $cuti['jenis_cuti']; ?>" readonly>
                                <input type="hidden" name="jenis_cuti" value="<?= $cuti['jenis_cuti']; ?>">
                            <?php endif; ?>
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
                                    <?php if (in_array($cuti['status'], ['Draft', 'Menungju'])): ?>
                                        <input type="date" 
                                               class="form-control <?= session('errors.tanggal_mulai') ? 'is-invalid' : '' ?>" 
                                               id="tanggal_mulai" name="tanggal_mulai" 
                                               value="<?= old('tanggal_mulai', $cuti['tanggal_mulai']) ?>" 
                                               required>
                                    <?php else: ?>
                                        <input type="text" 
                                               class="form-control" 
                                               value="<?= date('d/m/Y', strtotime($cuti['tanggal_mulai'])); ?>" 
                                               readonly>
                                        <input type="hidden" name="tanggal_mulai" value="<?= $cuti['tanggal_mulai']; ?>">
                                    <?php endif; ?>
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
                                    <?php if (in_array($cuti['status'], ['Draft', 'Menungju'])): ?>
                                        <input type="date" 
                                               class="form-control <?= session('errors.tanggal_selesai') ? 'is-invalid' : '' ?>" 
                                               id="tanggal_selesai" name="tanggal_selesai" 
                                               value="<?= old('tanggal_selesai', $cuti['tanggal_selesai']) ?>" 
                                               required>
                                    <?php else: ?>
                                        <input type="text" 
                                               class="form-control" 
                                               value="<?= date('d/m/Y', strtotime($cuti['tanggal_selesai'])); ?>" 
                                               readonly>
                                        <input type="hidden" name="tanggal_selesai" value="<?= $cuti['tanggal_selesai']; ?>">
                                    <?php endif; ?>
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
                            <?php if (isset($cuti['lama_hari'])): ?>
                                <span id="daysText">
                                    Lama cuti: <strong><?= $cuti['lama_hari']; ?> hari kerja</strong>
                                </span>
                            <?php else: ?>
                                <span id="daysText">Hitung hari kerja akan muncul setelah memilih tanggal</span>
                            <?php endif; ?>
                        </div>

                        <!-- Alasan Cuti -->
                        <div class="form-group">
                            <label for="alasan" class="font-weight-bold">Alasan Cuti <span class="text-danger">*</span></label>
                            <?php if (in_array($cuti['status'], ['Draft', 'Menungju'])): ?>
                                <textarea class="form-control <?= session('errors.alasan') ? 'is-invalid' : '' ?>" 
                                          id="alasan" name="alasan" 
                                          rows="5" 
                                          placeholder="Jelaskan alasan cuti secara detail..." 
                                          required><?= old('alasan', $cuti['alasan']) ?></textarea>
                            <?php else: ?>
                                <textarea class="form-control" rows="5" readonly><?= $cuti['alasan']; ?></textarea>
                                <input type="hidden" name="alasan" value="<?= $cuti['alasan']; ?>">
                            <?php endif; ?>
                            <?php if (session('errors.alasan')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.alasan') ?>
                                </div>
                            <?php endif; ?>
                            <small class="form-text text-muted">Minimal 10 karakter</small>
                        </div>

                        <!-- Alasan Penolakan (jika ada) -->
                        <?php if ($cuti['alasan_penolakan']): ?>
                            <div class="form-group">
                                <label class="font-weight-bold text-danger">Alasan Penolakan</label>
                                <textarea class="form-control" rows="3" readonly><?= $cuti['alasan_penolakan']; ?></textarea>
                            </div>
                        <?php endif; ?>

                        <!-- Disetujui Oleh (jika sudah disetujui) -->
                        <?php if ($cuti['disetujui_oleh'] && $cuti['disetujui_at']): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Disetujui oleh:</strong> <?= $cuti['disetujui_nama'] ?? 'Unknown'; ?><br>
                                <strong>Pada:</strong> <?= date('d/m/Y H:i', strtotime($cuti['disetujui_at'])); ?>
                            </div>
                        <?php endif; ?>

                        <hr>

                        <!-- Button Group -->
                        <div class="d-flex justify-content-between">
                            <?php if (in_array($cuti['status'], ['Draft', 'Menungju'])): ?>
                                <button type="reset" class="btn btn-secondary">
                                    <i class="fas fa-redo me-1"></i> Reset
                                </button>
                                <div>
                                    <a href="<?= base_url('admin/cuti'); ?>" class="btn btn-light">
                                        <i class="fas fa-times me-1"></i> Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                                    </button>
                                </div>
                            <?php else: ?>
                                <div class="w-100 text-center">
                                    <a href="<?= base_url('admin/cuti'); ?>" class="btn btn-primary">
                                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Informasi Penting -->
        <div class="col-lg-4">
            <!-- Status Timeline -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Status Pengajuan</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <!-- Step 1: Pengajuan -->
                        <div class="timeline-step <?= $cuti['status'] !== 'Draft' ? 'timeline-step-success' : '' ?>">
                            <div class="timeline-content">
                                <div class="inner-circle <?= $cuti['status'] !== 'Draft' ? 'bg-success' : 'bg-secondary' ?>"></div>
                                <p class="h6 mt-2 mb-0">Pengajuan</p>
                                <p class="text-muted mb-0">
                                    <?= date('d/m/Y', strtotime($cuti['tanggal_pengajuan'])); ?>
                                </p>
                                <?php if ($cuti['status'] === 'Draft'): ?>
                                    <span class="badge bg-secondary mt-1">Draft</span>
                                <?php else: ?>
                                    <span class="badge bg-success mt-1">Selesai</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Step 2: Review HRD -->
                        <div class="timeline-step <?= in_array($cuti['status'], ['Disetujui HRD', 'Disetujui Atasan', 'Ditolak']) ? 'timeline-step-success' : '' ?>">
                            <div class="timeline-content">
                                <div class="inner-circle <?= in_array($cuti['status'], ['Disetujui HRD', 'Disetujui Atasan', 'Ditolak']) ? 'bg-success' : 'bg-secondary' ?>"></div>
                                <p class="h6 mt-2 mb-0">Review HRD</p>
                                <?php if (in_array($cuti['status'], ['Disetujui HRD', 'Disetujui Atasan', 'Ditolak'])): ?>
                                    <p class="text-muted mb-0">
                                        <?= $cuti['disetujui_at'] ? date('d/m/Y', strtotime($cuti['disetujui_at'])) : '-' ?>
                                    </p>
                                    <?php if ($cuti['status'] === 'Ditolak'): ?>
                                        <span class="badge bg-danger mt-1">Ditolak</span>
                                    <?php else: ?>
                                        <span class="badge bg-success mt-1">Selesai</span>
                                    <?php endif; ?>
                                <?php elseif ($cuti['status'] === 'Menunggu'): ?>
                                    <span class="badge bg-warning mt-1">Menunggu</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary mt-1">Belum</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Step 3: Persetujuan Atasan -->
                        <div class="timeline-step <?= in_array($cuti['status'], ['Disetujui Atasan']) ? 'timeline-step-success' : '' ?>">
                            <div class="timeline-content">
                                <div class="inner-circle <?= $cuti['status'] === 'Disetujui Atasan' ? 'bg-success' : 'bg-secondary' ?>"></div>
                                <p class="h6 mt-2 mb-0">Atasan</p>
                                <?php if ($cuti['status'] === 'Disetujui Atasan'): ?>
                                    <p class="text-muted mb-0">
                                        <?= $cuti['disetujui_at'] ? date('d/m/Y', strtotime($cuti['disetujui_at'])) : '-' ?>
                                    </p>
                                    <span class="badge bg-success mt-1">Disetujui</span>
                                <?php elseif ($cuti['status'] === 'Disetujui HRD'): ?>
                                    <span class="badge bg-warning mt-1">Menunggu</span>
                                <?php elseif ($cuti['status'] === 'Ditolak'): ?>
                                    <span class="badge bg-danger mt-1">Ditolak</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary mt-1">Belum</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Kuota -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold">Informasi Kuota</h6>
                </div>
                <div class="card-body">
                    <?php if ($cuti['jenis_cuti'] === 'Tahunan' && $cuti['sisa_cuti_tahunan']): ?>
                        <div class="text-center mb-3">
                            <i class="fas fa-calendar-check fa-3x text-info"></i>
                            <h5 class="mt-2">Sisa Cuti Tahunan</h5>
                            <h2 class="text-success"><?= $cuti['sisa_cuti_tahunan']; ?> hari</h2>
                            <p class="text-muted">Pada saat pengajuan</p>
                        </div>
                        
                        <?php 
                        $terpakai = 12 - $cuti['sisa_cuti_tahunan'];
                        $percentage = ($terpakai / 12) * 100;
                        ?>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Kuota: 12 hari</span>
                                <span>Terpakai: <?= $terpakai; ?> hari</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-info" 
                                     role="progressbar" 
                                     style="width: <?= $percentage; ?>%" 
                                     aria-valuenow="<?= $percentage; ?>" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center">
                            <i class="fas fa-calendar-alt fa-3x text-muted"></i>
                            <h5 class="mt-2">Jenis Cuti: <?= $cuti['jenis_cuti']; ?></h5>
                            <p class="text-muted">
                                <?php if ($cuti['jenis_cuti'] === 'Hamil'): ?>
                                    Cuti hamil tidak mengurangi kuota tahunan
                                <?php elseif ($cuti['jenis_cuti'] === 'Sakit'): ?>
                                    Cuti sakit dengan surat dokter
                                <?php else: ?>
                                    Cuti khusus/perizinan lainnya
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Aksi Cepat -->
            <?php if (in_array($cuti['status'], ['Draft', 'Menungju'])): ?>
                <div class="card shadow">
                    <div class="card-header py-3 bg-warning text-white">
                        <h6 class="m-0 font-weight-bold">Aksi Cepat</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                                <i class="fas fa-trash me-2"></i> Hapus Pengajuan
                            </button>
                            
                            <?php if ($cuti['status'] === 'Menunggu'): ?>
                                <button type="button" class="btn btn-secondary" onclick="confirmCancel()">
                                    <i class="fas fa-times me-2"></i> Batalkan Pengajuan
                                </button>
                            <?php endif; ?>
                            
                            <?php if (session()->get('role') === 'hrd' && $cuti['status'] === 'Menunggu'): ?>
                                <button type="button" class="btn btn-success" onclick="quickApprove()">
                                    <i class="fas fa-check me-2"></i> Setujui Sekarang
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus pengajuan cuti ini?</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Aksi ini tidak dapat dibatalkan!
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form action="<?= base_url('admin/cuti/delete/' . $cuti['id']); ?>" method="post" style="display: inline;">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Confirmation Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Pembatalan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('admin/cuti/cancel/' . $cuti['id']); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin membatalkan pengajuan cuti ini?</p>
                    <div class="form-group">
                        <label for="cancel_reason">Alasan Pembatalan:</label>
                        <textarea class="form-control" id="cancel_reason" name="cancel_reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Batalkan Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    list-style: none;
    padding: 0;
}
.timeline-step {
    position: relative;
    padding-bottom: 2rem;
    padding-left: 2.5rem;
}
.timeline-step:before {
    content: '';
    position: absolute;
    left: 0.75rem;
    top: 0;
    height: 100%;
    width: 2px;
    background: #e0e0e0;
}
.timeline-step:last-child:before {
    display: none;
}
.timeline-step.timeline-step-success:before {
    background: #28a745;
}
.timeline-content {
    position: relative;
}
.inner-circle {
    position: absolute;
    left: 0;
    top: 0;
    width: 1.5rem;
    height: 1.5rem;
    border-radius: 50%;
    z-index: 1;
}
</style>

<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        placeholder: 'Pilih karyawan',
        allowClear: true
    });

    // Initialize flatpickr for date inputs (only if editable)
    const isEditable = <?= in_array($cuti['status'], ['Draft', 'Menungju']) ? 'true' : 'false'; ?>;
    
    if (isEditable) {
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

        // Load initial quota if karyawan is selected
        const initialKaryawanId = $('#karyawan_id').val();
        if (initialKaryawanId) {
            loadQuota(initialKaryawanId);
        }

        // Calculate initial days
        calculateDays();
    }

    // Form validation
    $('#cutiForm').submit(function(e) {
        if (!isEditable) {
            e.preventDefault();
            alert('Cuti tidak dapat diedit dalam status saat ini.');
            return false;
        }

        const tanggalMulai = $('#tanggal_mulai').val();
        const tanggalSelesai = $('#tanggal_selesai').val();
        const karyawanId = $('#karyawan_id').val();
        const jenisCuti = $('#jenis_cuti').val();
        
        if (!tanggalMulai || !tanggalSelesai || !karyawanId || !jenisCuti) {
            e.preventDefault();
            alert('Harap lengkapi semua data yang diperlukan!');
            return false;
        }

        const start = new Date(tanggalMulai);
        const end = new Date(tanggalSelesai);
        
        if (end < start) {
            e.preventDefault();
            alert('Tanggal selesai tidak boleh sebelum tanggal mulai!');
            return false;
        }

        return true;
    });
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

            // Update quota info text
            const jenisCuti = $('#jenis_cuti').val();
            if (jenisCuti === 'Tahunan') {
                $('#quotaText').html(`
                    Kuota cuti tahunan: <strong>${quota} hari</strong> | 
                    Terpakai: <strong>${used} hari</strong> | 
                    Sisa: <strong>${remaining} hari</strong>
                `);
                $('#quotaInfo').show().removeClass('alert-danger').addClass('alert-info');
                
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
}

function calculateDays() {
    const startDate = $('#tanggal_mulai').val();
    const endDate = $('#tanggal_selesai').val();
    
    if (!startDate || !endDate) {
        $('#daysText').text('Hitung hari kerja akan muncul setelah memilih tanggal');
        return;
    }

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
            
            if (workDays > 30) {
                daysText += '<br><span class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i>Cuti terlalu lama, perlu persetujuan khusus!</span>';
                $('#daysInfo').removeClass('alert-warning').addClass('alert-danger');
            } else {
                $('#daysInfo').removeClass('alert-danger').addClass('alert-warning');
            }

            $('#daysText').html(daysText);
        },
        error: function() {
            $('#daysText').text('Terjadi kesalahan saat menghitung hari.');
        }
    });
}

function confirmDelete() {
    $('#deleteModal').modal('show');
}

function confirmCancel() {
    $('#cancelModal').modal('show');
}

function quickApprove() {
    if (confirm('Apakah Anda yakin ingin menyetujui cuti ini?')) {
        $.ajax({
            url: '<?= base_url("admin/cuti/approve/") ?>' + <?= $cuti['id'] ?>,
            type: 'POST',
            data: {
                '<?= csrf_token(); ?>': '<?= csrf_hash(); ?>'
            },
            success: function() {
                alert('Cuti berhasil disetujui!');
                location.reload();
            },
            error: function() {
                alert('Terjadi kesalahan. Silakan coba lagi.');
            }
        });
    }
}

// Helper function for badge classes
function getStatusBadgeClass(status) {
    const classes = {
        'Draft': 'secondary',
        'Menunggu': 'warning',
        'Disetujui HRD': 'success',
        'Disetujui Atasan': 'success',
        'Ditolak': 'danger',
        'Dibatalkan': 'secondary'
    };
    return classes[status] || 'secondary';
}
</script>

<?= $this->include('admin/templates/footer') ?>